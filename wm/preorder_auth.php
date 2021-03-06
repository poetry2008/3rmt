<?php
/*
  $Id$
*/

  require('includes/application_top.php');
 
  $exists_customer_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where check_login_str = '".$_GET['pid']."' and site_id = '".SITE_ID."' and is_active = '0'");  
  if (!tep_db_num_rows($exists_customer_raw)) {
    tep_redirect(tep_href_link('account_timeout.php')); 
  }
  $exists_customer = tep_db_fetch_array($exists_customer_raw);  
 
  $preorder_query = tep_db_query("select * from ".TABLE_PREORDERS." where customers_id = '".$exists_customer['customers_id']."' and is_active = 0 and site_id = '".SITE_ID."' order by orders_id desc limit 1");
  $preorder_res = tep_db_fetch_array($preorder_query); 
   
  if ($preorder_res) {
    $pid = $preorder_res['orders_id']; 
    $now_time = time(); 
    $preorder_customer_res = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$preorder_res['customers_id']."'");     
    $preorder_customer = tep_db_fetch_array($preorder_customer_res); 
    if (($now_time - (int)$preorder_customer['send_mail_time']) > 60*60*24*3) {
       
      tep_db_query("delete from ".TABLE_PREORDERS." where orders_id = '".$pid."' and site_id = '".SITE_ID."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_DOWNLOAD." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_TO_ACTOR." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_QUESTIONS." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_QUESTIONS_PRODUCTS." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_STATUS_HISTORY." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_TO_BUTTONS." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id = '".$pid."'"); 
      
      $customers_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$preorder_res['customers_id']."' and is_active = '1'"); 

      if (!tep_db_num_rows($customers_raw)) {
        tep_db_query("delete from ".TABLE_CUSTOMERS." where customers_id = '".$preorder_res['customers_id']."' and site_id = '".SITE_ID."'");
        tep_db_query("delete from ".TABLE_CUSTOMERS_INFO." where customers_info_id = '".$preorder_res['customers_id']."'");
        tep_db_query("delete from ".TABLE_ADDRESS_BOOK." where customers_id = '".$preorder_res['customers_id']."'");
        tep_db_query("delete from ".TABLE_CUSTOMERS_BASKET." where customers_id = '".$preorder_res['customers_id']."'");
        tep_db_query("delete from ".TABLE_CUSTOMERS_BASKET_OPTIONS." where customers_id = '".$preorder_res['customers_id']."'");
      }
    
      tep_redirect(tep_href_link('account_timeout.php')); 
    } else {
      tep_db_query("update ".TABLE_PREORDERS." set `is_active` = 1, `date_purchased` = '".date('Y-m-d H:i:s', time())."' where orders_id = '".$pid."' and site_id = '".SITE_ID."'");  
      tep_db_query("update ".TABLE_CUSTOMERS." set `is_active` = 1 where customers_id = '".$preorder_res['customers_id']."' and site_id = '".SITE_ID."'"); 
      preorder_last_customer_action(); 

      //预约完成邮件认证
      $preorders_mail_array = tep_get_mail_templates('PREORDER_MAIL_CONTENT',SITE_ID); 
      $preorder_email_text = $preorders_mail_array['contents'];
      $preorder_mail_templates_array = explode("\r\n",$preorder_email_text);
      $replace_mail_array = array();
      foreach($preorder_mail_templates_array as $mail_key=>$mail_value){

        if(strpos($mail_value,'${PRODUCTS_PRICE}') !== false || strpos($mail_value,'${SUB_TOTAL}') !== false || strpos($mail_value,'${ORDER_TOTAL}') !== false || strpos($mail_value,'${CUSTOMIZED_FEE}') !== false){

          $replace_mail_array[] = "\r\n".$mail_value;
        }
      }
      $preorder_email_text = str_replace($replace_mail_array,'',$preorder_email_text);
      $pre_name = '';
      $pre_num = 0;
      $max_op_len = 0;
      $max_op_array = array();
      $attr_list_array = array();
      $mail_option_str = '';
      
      $preorder_attribute_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$pid."'");
      while ($preorder_attribute = tep_db_fetch_array($preorder_attribute_raw)) {
        $op_info_array = @unserialize(stripslashes($preorder_attribute['option_info'])); 
        $max_op_array[] = mb_strlen($op_info_array['title'], 'utf-8'); 
        $attr_list_array[] = array('title' => $op_info_array['title'], 'value' => $op_info_array['value']); 
      }
      
      if (!empty($max_op_array)) {
        $max_op_len = max($max_op_array); 
      }
      if (!empty($attr_list_array)) {
        foreach ($attr_list_array as $ar_key => $ar_value) {
          $mail_option_str .= $ar_value['title'].str_repeat('　', intval($max_op_len - mb_strlen($ar_value['title'], 'utf-8'))).'：'.str_replace(array("<br>", "<BR>", "\r", "\n", "\r\n"), "", stripslashes($ar_value['value']))."\n"; 
        }
      }
      $replace_info_arr = array('${PRODUCTS_NAME}', '${PRODUCTS_QUANTITY}', '${PAYMENT}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}', '${PREORDER_NUMBER}', '${ORDER_COMMENT}', '${PRODUCTS_ATTRIBUTES}'); 
      
      $preorder_products_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$pid."'");
      $preorder_products_res = tep_db_fetch_array($preorder_products_raw);
      
      if ($preorder_products_res) {
        $pre_name = $preorder_products_res['products_name']; 
        $pre_num = $preorder_products_res['products_quantity']; 
      }
      $per_num_end = '';
      if(isset($preorder_products_res['products_rate']) &&$preorder_products_res['products_rate']!=0 &&$preorder_products_res['products_rate']!=1 &&$preorder_products_res['products_rate']!=''){
        $per_num_end = NUM_UNIT_TEXT.' ('.number_format($preorder_products_res['products_rate']*$pre_num).') ';
        $pre_num = $pre_num.$per_num_end;
      }
     
      $pre_replace_info_arr = array($pre_name, $pre_num, $preorder_res['payment_method'], $preorder_res['customers_name'], STORE_NAME, HTTP_SERVER, $preorder_res['orders_id'], $preorder_res['comment_msg'], $mail_option_str);
     
      $preorder_email_text = str_replace($replace_info_arr, $pre_replace_info_arr, $preorder_email_text);
      $pre_email_text = str_replace('${SITE_NAME}', STORE_NAME, $preorders_mail_array['title']);

      $preorder_email_text = tep_replace_mail_templates($preorder_email_text,$preorder_res['customers_email_address'],$preorder_res['customers_name']); 
      if ($exists_customer['is_send_mail'] != '1') {
        tep_mail($preorder_res['customers_name'], $preorder_res['customers_email_address'], $pre_email_text, $preorder_email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS); 
        tep_mail('', SENTMAIL_ADDRESS, $pre_email_text, $preorder_email_text, $preorder_res['customers_name'], $preorder_res['customers_email_address']); 
      }
      $send_preorder_id = $pid;
      tep_session_register('send_preorder_id');
      tep_redirect(tep_href_link('preorder_success.php')); 
    }
  } else {
    tep_redirect(tep_href_link('account_timeout.php')); 
  }
?>
