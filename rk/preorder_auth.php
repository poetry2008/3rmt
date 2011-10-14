<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $pid = $_GET['pid']; 
  
  $preorder_query = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$pid."' and is_active = 0 and site_id = '".SITE_ID."'");
  $preorder_res = tep_db_fetch_array($preorder_query); 
   
  if ($preorder_res) {
    $now_time = time(); 
    
    if (($now_time - $preorder_res['send_mail_time']) > 60*60*24*3) {
       
      tep_db_query("delete from ".TABLE_PREORDERS." where orders_id = '".$pid."' and site_id = '".SITE_ID."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_DOWNLOAD." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_TO_ACTOR." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_QUESTIONS." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_QUESTIONS_PRODUCTS." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_STATUS_HISTORY." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_TO_COMPUTERS." where orders_id = '".$pid."'"); 
      tep_db_query("delete from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id = '".$pid."'"); 
      
      $customers_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$preorder_res['customers_id']."' and is_active = '1'"); 

      if (!tep_db_num_rows($customers_raw)) {
        tep_db_query("delete from ".TABLE_CUSTOMERS." where customers_id = '".$preorder_res['customers_id']."' and site_id = '".SITE_ID."'");
        tep_db_query("delete from ".TABLE_CUSTOMERS_INFO." where customers_info_id = '".$preorder_res['customers_id']."'");
        tep_db_query("delete from ".TABLE_ADDRESS_BOOK." where customers_id = '".$preorder_res['customers_id']."'");
        tep_db_query("delete from ".TABLE_CUSTOMERS_BASKET." where customers_id = '".$preorder_res['customers_id']."'");
        tep_db_query("delete from ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." where customers_id = '".$preorder_res['customers_id']."'");
      }
    
      tep_redirect(tep_href_link('account_timeout.php')); 
    } else {
      tep_db_query("update ".TABLE_PREORDERS." set `is_active` = 1, `date_purchased` = '".date('Y-m-d H:i:s', time())."' where orders_id = '".$pid."' and site_id = '".SITE_ID."'");  
      tep_db_query("update ".TABLE_CUSTOMERS." set `is_active` = 1 where customers_id = '".$preorder_res['customers_id']."' and site_id = '".SITE_ID."'"); 
      preorder_last_customer_action(); 
      
      $preorder_email_text = PREORDER_MAIL_CONTENT;
      $pre_name = '';
      $pre_num = 0;
      $pre_date = '';
      $replace_info_arr = array('${PRODUCTS_NAME}', '${PRODUCTS_QUANTITY}', '${EFFECTIVE_TIME}'); 
      
      $pre_date_str = strtotime($preorder_res['predate']); 
      $pre_date = date('Y', $pre_date_str).PREORDER_YEAR_TEXT.date('m', $pre_date_str).PREORDER_MONTH_TEXT.date('d', $pre_date_str).PREORDER_DAY_TEXT; 

      $preorder_products_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$pid."'");
      $preorder_products_res = tep_db_fetch_array($preorder_products_raw);
      
      if ($preorder_products_res) {
        $pre_name = $preorder_products_res['products_name']; 
        $pre_num = $preorder_products_res['products_quantity']; 
      }
     
      $pre_replace_info_arr = array($pre_name, $pre_num, $pre_date);
     
      $preorder_email_text = str_replace($replace_info_arr, $pre_replace_info_arr, $preorder_email_text);

      tep_mail($preorder_res['customers_name'], $preorder_res['customers_email_address'],PREORDER_MAIL_SUBJECT, $preorder_email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS); 
      tep_redirect(tep_href_link('preorder_active_success.php', 'pid='.$pid)); 
    }
  } else {
    tep_redirect(tep_href_link('account_timeout.php')); 
  }
?>
