<?php
/*
  $Id$
*/

if (!isset($_SESSION['preorder_info_id'])) {
  forward404();
}

require(DIR_WS_FUNCTIONS . 'visites.php');
require(DIR_WS_CLASSES . 'payment.php');

if (isset($preorder_real_point)) {
  $preorder_point = $preorder_real_point;
}

include(DIR_WS_LANGUAGES . $language . '/change_preorder_process.php');

$preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$_SESSION['preorder_info_id']."' and site_id = '".SITE_ID."'");
$preorder = tep_db_fetch_array($preorder_raw);

if ($preorder) {
  //$order_query = tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
  //$orders_id = $_SESSION['preorder_info_id'];
  
  //if (tep_db_num_rows($order_query)) {
    $orders_id = date('Ymd').'-'.date('His').tep_get_order_end_num(); 
  //}
  $payment_modules = payment::getInstance($preorder['site_id']);   
  $cpayment_code = payment::changeRomaji($preorder['payment_method'], PAYMENT_RETURN_TYPE_CODE);   
  
  $option_info_array = get_preorder_total_info($cpayment_code, $preorder['orders_id'], $preorder_option_info);
  
  $torihikihouhou_date_str = $_SESSION['preorder_info_date'].' '. $_SESSION['preorder_info_start_hour'] .':'. $_SESSION['preorder_info_start_min'] .':00';
  $torihikihouhou_date_end_str = $_SESSION['preorder_info_date'].' '. $_SESSION['preorder_info_end_hour'] .':'. $_SESSION['preorder_info_end_min'] .':00';
  $default_status_raw = tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".DEFAULT_ORDERS_STATUS_ID."'");
  $default_status_res = tep_db_fetch_array($default_status_raw); 
  $preorder_cus_id = $preorder['customers_id']; 
  $sql_data_array = array('orders_id' => $orders_id,
                           'site_id' => $preorder['site_id'], 
                           'customers_id' => $preorder_cus_id, 
                           'customers_name' => $preorder['customers_name'], 
                           'customers_name_f' => $preorder['customers_name_f'], 
                           'customers_company' => $preorder['customers_company'], 
                           'customers_street_address' => $preorder['customers_street_address'], 
                           'customers_suburb' => $preorder['customers_suburb'], 
                           'customers_city' => $preorder['customers_city'], 
                           'customers_postcode' => $preorder['customers_postcode'], 
                           'customers_state' => $preorder['customers_state'], 
                           'customers_country' => $preorder['customers_country'], 
                           'customers_telephone' => $preorder['customers_telephone'], 
                           'customers_email_address' => $preorder['customers_email_address'], 
                           'customers_address_format_id' => $preorder['customers_address_format_id'], 
                           'delivery_name' => $preorder['delivery_name'], 
                           'delivery_name_f' => $preorder['delivery_name_f'], 
                           'delivery_company' => $preorder['delivery_company'], 
                           'delivery_street_address' => $preorder['delivery_street_address'], 
                           'delivery_suburb' => $preorder['delivery_suburb'], 
                           'delivery_city' => $preorder['delivery_city'], 
                           'delivery_postcode' => $preorder['delivery_postcode'], 
                           'delivery_state' => $preorder['delivery_state'], 
                           'delivery_country' => $preorder['delivery_country'], 
                           'delivery_telephone' => $preorder['delivery_telephone'], 
                           'delivery_address_format_id' => $preorder['delivery_address_format_id'], 
                           'billing_name' => $preorder['billing_name'], 
                           'billing_name_f' => $preorder['billing_name_f'], 
                           'billing_company' => $preorder['billing_company'], 
                           'billing_street_address' => $preorder['billing_street_address'], 
                           'billing_suburb' => $preorder['billing_suburb'], 
                           'billing_city' => $preorder['billing_city'], 
                           'billing_postcode' => $preorder['billing_postcode'], 
                           'billing_state' => $preorder['billing_state'], 
                           'billing_country' => $preorder['billing_country'], 
                           'billing_telephone' => $preorder['billing_telephone'], 
                           'billing_address_format_id' => $preorder['billing_address_format_id'], 
                           'payment_method' => $preorder['payment_method'], 
                           'cc_type' => $preorder['cc_type'], 
                           'cc_owner' => $preorder['cc_owner'], 
                           'cc_number' => $preorder['cc_number'], 
                           'cc_expires' => $preorder['cc_expires'], 
                           'last_modified' => $preorder['last_modified'], 
                           'date_purchased' => 'now()', 
                           'orders_status' => DEFAULT_ORDERS_STATUS_ID, 
                           'orders_date_finished' => $preorder['orders_date_finished'], 
                           'currency' => $preorder['currency'], 
                           'currency_value' => $preorder['currency_value'], 
                           'torihiki_Bahamut' => $preorder['torihiki_Bahamut'], 
                           'torihiki_houhou' => $_SESSION['preorder_info_tori'], 
                           'torihiki_date' => $torihikihouhou_date_str, 
                           'torihiki_date_end' => $torihikihouhou_date_end_str,
                           'code_fee' => (isset($option_info_array['fee']))?$option_info_array['fee']:$preorder['code_fee'], 
                           'shipping_fee' => $_POST['shipping_fee'],
                           'language_id' => $preorder['language_id'], 
                           'orders_status_name' => $default_status_res['orders_status_name'], 
                           'orders_status_image' => $preorder['orders_status_image'],
                           'finished' => $preorder['finished'], 
                           'orders_ref' => $_SESSION['referer'], 
                           'orders_ref_site' => tep_get_domain($_SESSION['referer']), 
                           'orders_ip' => $_SERVER['REMOTE_ADDR'], 
                           'orders_host_name' => trim(strtolower(@gethostbyaddr($_SERVER['REMOTE_ADDR']))), 
                           'orders_user_agent' => $_SERVER['HTTP_USER_AGENT'], 
                           'orders_comment' => $preorder['orders_comment'], 
                           'orders_important_flag' => $preorder['orders_important_flag'], 
                           'orders_care_flag' => $preorder['orders_care_flag'], 
                           'orders_wait_flag' => '1', 
                           'orders_inputed_flag' => '0', 
                           'orders_screen_resolution' => $_SESSION['screenResolution'], 
                           'orders_color_depth' => $_SESSION['colorDepth'], 
                           'orders_flash_enable' => $_SESSION['flashEnable'], 
                           'orders_flash_version' => $_SESSION['flashVersion'], 
                           'orders_director_enable' => $_SESSION['directorEnable'], 
                           'orders_quicktime_enable' => $_SESSION['quicktimeEnable'], 
                           'orders_realplayer_enable' => $_SESSION['realPlayerEnable'], 
                           'orders_windows_media_enable' => $_SESSION['windowsMediaEnable'], 
                           'orders_pdf_enable' => $_SESSION['pdfEnable'], 
                           'orders_java_enable' => $_SESSION['javaEnable'], 
                           'orders_http_accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'], 
                           'orders_system_language' => $_SESSION['systemLanguage'], 
                           'orders_user_language' => $_SESSION['userLanguage'], 
                           'orders_work' => '', 
                           'q_8_1' => $preorder['q_8_1'], 
                           'telecom_option' => $_SESSION['preorder_option'], 
                           'orders_ref_keywords' => strtolower(SBC2DBC(parseKeyword($_SESSION['referer']))), 
                           'flag_qaf' => $preorder['flag_qaf'], 
                           'end_user' => $preorder['end_user'], 
                           'confirm_payment_time' => $preorder['confirm_payment_time'],
                           'orders_type' => 1, 
                          );
  
  if (isset($_SESSION['referer_adurl']) && $_SESSION['referer_adurl']) {
    $sql_data_array['orders_adurl'] = $_SESSION['referer_adurl'];
  }
  
  $telecom_option_ok = $payment_modules->preorderDealUnknow($sql_data_array, $cpayment_code); 
  
  tep_db_perform(TABLE_ORDERS, $sql_data_array);

  //住所信息录入
  $add_list = array();
  foreach($_POST as $address_key=>$address_value){
    if(substr($address_key,0,3) == 'op_'){
      $address_query = tep_db_query("select id,name,name_flag from ". TABLE_ADDRESS ." where name_flag='". substr($address_key,3) ."'");
      $address_array = tep_db_fetch_array($address_query);
      tep_db_free_result($address_query);
      $address_id = $address_array['id'];
      $add_list[] = array($address_array['name'],$address_value);
      $address_add_query = tep_db_query("insert into ". TABLE_ADDRESS_ORDERS ." value(NULL,'$orders_id',{$preorder_cus_id},$address_id,'{$address_array['name_flag']}','$address_value')");
      tep_db_free_result($address_add_query);
    }
  }

  $address_show_array = array(); 
  $address_show_list_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_show_list_array = tep_db_fetch_array($address_show_list_query)){

    $address_show_array[$address_show_list_array['id']] = $address_show_list_array['name_flag'];
  }
  tep_db_free_result($address_show_list_query);
  $address_temp_str = '';
  foreach($_POST as $address_his_key=>$address_his_value){
    if(substr($address_his_key,0,3) == 'op_'){
    
      if(in_array(substr($address_his_key,3),$address_show_array)){

         $address_temp_str .= $address_his_value;
      }

    } 
  }
  
  $address_error = false;
  $address_sh_his_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id='$preorder_cus_id' group by orders_id");
  while($address_sh_his_array = tep_db_fetch_array($address_sh_his_query)){

    $address_sh_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where customers_id='$preorder_cus_id' and orders_id='". $address_sh_his_array['orders_id'] ."'");
    $add_temp_str = '';
    while($address_sh_array = tep_db_fetch_array($address_sh_query)){
     
      if(in_array($address_sh_array['name'],$address_show_array)){

        $add_temp_str .= $address_sh_array['value'];
      }  
    }
    if($address_temp_str == $add_temp_str){

      $address_error = true;
      break;
    }
    tep_db_free_result($address_sh_query);
  }
  tep_db_free_result($address_sh_his_query);
if($address_error == false){
  foreach($_POST as $address_history_key=>$address_history_value){
    if(substr($address_history_key,0,3) == 'op_'){
      $address_history_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where name_flag='". substr($address_history_key,3) ."'");
      $address_history_array = tep_db_fetch_array($address_history_query);
      tep_db_free_result($address_history_query);
      $address_history_id = $address_history_array['id'];
      $address_history_add_query = tep_db_query("insert into ". TABLE_ADDRESS_HISTORY ." value(NULL,'$orders_id',{$preorder_cus_id},$address_history_id,'{$address_history_array['name_flag']}','$address_history_value')");
      tep_db_free_result($address_history_add_query);
    }
  }
}

  //住所信息录入结束

  $preorder_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_SESSION['preorder_info_id']."'");
  
  while ($preorder_total_res = tep_db_fetch_array($preorder_total_raw)) {
    if ($preorder_total_res['class'] == 'ot_total') {
      if (isset($_SESSION['preorder_campaign_fee'])) {
        if (isset($option_info_array['total'])) {
          $preorder_total_num = $option_info_array['total'] + (int)$_SESSION['preorder_campaign_fee']+(int)$_POST['shipping_fee']; 
          $preorder_total_print_num = $option_info_array['total'] + (int)$_SESSION['preorder_campaign_fee']+(int)$_POST['shipping_fee']; 
        } else {
          $preorder_total_num = $preorder_total_res['value'] + (int)$_SESSION['preorder_campaign_fee']+(int)$_POST['shipping_fee']; 
          $preorder_total_print_num = $preorder_total_res['value'] + (int)$_SESSION['preorder_campaign_fee']+(int)$_POST['shipping_fee']; 
        }
      } else {
        if (isset($option_info_array['total'])) {
          $preorder_total_num = $option_info_array['total'] - (int)$preorder_point+(int)$_POST['shipping_fee']; 
          $preorder_total_print_num = $option_info_array['total'] - (int)$preorder_point+(int)$_POST['shipping_fee']; 
        } else {
          $preorder_total_num = $preorder_total_res['value'] - (int)$preorder_point+(int)$_POST['shipping_fee']; 
          $preorder_total_print_num = $preorder_total_res['value'] - (int)$preorder_point+(int)$_POST['shipping_fee']; 
        }
      }
    } else if ($preorder_total_res['class'] == 'ot_point') {
      $preorder_total_num = (int)$preorder_point; 
    } else if ($preorder_total_res['class'] == 'ot_subtotal') {
      if (isset($option_info_array['subtotal'])) {
        $preorder_total_num = $option_info_array['subtotal']; 
      } else {
        $preorder_total_num = $preorder_total_res['value']; 
      }
    } else {
      $preorder_total_num = $preorder_total_res['value']; 
    }
    
    //if ($preorder_total_res['class'] == 'ot_subtotal') {
      //$preorder_subtotal_num = $preorder_total_res['value']; 
    //}
    $sql_data_array = array('orders_id' => $orders_id,
                            'title' => $preorder_total_res['title'], 
                            'text' => $preorder_total_res['text'], 
                            'value' => $preorder_total_num, 
                            'class' => $preorder_total_res['class'], 
                            'sort_order' => $preorder_total_res['sort_order'], 
        ); 
    if ($preorder_total_res['class'] == 'ot_total') {
      if ($telecom_option_ok != true) {
        $telecom_option_ok = $payment_modules->getPreexpress((int)$preorder_total_res['value'], $orders_id, $cpayment_code); 
      }
    }
    tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
  }
  $order_comment_str = '';
  
  $comment_raw = tep_db_query("select comments from ".TABLE_PREORDERS_STATUS_HISTORY." where orders_id = '".$_SESSION['preorder_info_id']."' and comments != '' order by orders_status_history_id asc limit 1");
 
  $comment_res = tep_db_fetch_array($comment_raw);
  if ($comment_res) {
    $order_comment_str = $comment_res['comments'];
  }
  
  
  $customer_notification = (SEND_EMAILS == 'true') ? '1' : '0'; 
  $sql_data_array = array('orders_id' => $orders_id,
                          'orders_status_id' => DEFAULT_ORDERS_STATUS_ID, 
                          'date_added' => date('Y-m-d H:i:s', time()), 
                          'customer_notified' => $customer_notification, 
                          'comments' => $order_comment_str, 
      ); 
  tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
 
  if ($telecom_option_ok) {
    tep_db_perform(TABLE_ORDERS, array('orders_status' => '30'), 'update', "orders_id='".$orders_id."'");
    $sql_data_array = array('orders_id' => $orders_id, 
                            'orders_status_id' => '30', 
                            'date_added' => 'now()', 
                            'customer_notified' => '0',
                            'comments' => 'checkout');
    // ccdd
    //tep_order_status_change($orders_id,30);
    tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
    orders_updated($orders_id);
  }
  $products_ordered_text = ''; 
  
  $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
  $preorder_product_res = tep_db_fetch_array($preorder_product_raw); 
  $sql_data_array = array('orders_id' => $orders_id,
                          'products_id' => $preorder_product_res['products_id'],
                          'products_model' => $preorder_product_res['products_model'], 
                          'products_name' => $preorder_product_res['products_name'], 
                          'products_price' => $preorder_product_res['products_price'], 
                          'final_price' => (isset($option_info_array['final_price']))?$option_info_array['final_price']:$preorder_product_res['final_price'], 
                          'products_tax' => $preorder_product_res['products_tax'], 
                          'products_quantity' => $preorder_product_res['products_quantity'], 
                          'products_rate' => $preorder_product_res['products_rate'], 
                          'torihiki_date' => $torihikihouhou_date_str, 
                          'site_id' => SITE_ID
      );
  tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
  $order_products_id = tep_db_insert_id();

  
  $cl_max_len = 0; 
if (isset($_SESSION['preorder_option_info'])) {
  $cl_len_array = array();  
  foreach ($_SESSION['preorder_option_info'] as $cl_key => $cl_value) {
    $cl_key_info = explode('_', $cl_key);
    $cl_attr_query = tep_db_query("select front_title from ".TABLE_OPTION_ITEM." where name = '".$cl_key_info['1']."' and id = '".$cl_key_info[3]."'");
    $cl_attr_values = tep_db_fetch_array($cl_attr_query);
    if ($cl_attr_values) {
      $cl_len_array[] = mb_strlen($cl_attr_values['front_title'], 'utf-8'); 
    }
  }
  
  
}
$old_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$_SESSION['preorder_info_id']."'");

while ($old_attr_res = tep_db_fetch_array($old_attr_raw)) {
  $old_attr_info = @unserialize(stripslashes($old_attr_res['option_info'])); 
  $cl_len_array[] = mb_strlen($old_attr_info['title'], 'utf-8');
}

if (!empty($cl_len_array)) {
  $cl_max_len = max($cl_len_array); 
}

if($cl_max_len < 4) {
  $cl_max_len = 4;
}
  
  
  $products_ordered_text .= '注文商品'.str_repeat('　', intval(($cl_max_len-mb_strlen('注文商品','utf-8')))).'：'.$preorder_product_res['products_name'];
  if (tep_not_null($preorder_product_res['products_model'])) {
    $products_ordered_text .= ' ('.$preorder_product_res['products_model'].')'; 
  }
  
  $products_ordered_atttibutes_text = '';


$mold_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$_SESSION['preorder_info_id']."'");
while ($mold_attr_res = tep_db_fetch_array($mold_attr_raw)) {
  $mold_attr_info = @unserialize(stripslashes($mold_attr_res['option_info'])); 

  $sql_data_array = array('orders_id' => $orders_id,
                          'orders_products_id' => $order_products_id,
                          'options_values_price' => $mold_attr_res['options_values_price'],
                          'option_info' => tep_db_input(serialize($mold_attr_info)),
                          'option_group_id' => $mold_attr_res['option_group_id'],
                          'option_item_id' => $mold_attr_res['option_item_id'],
                          ); 
  tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);
  
  if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
    $sql_data_array = array('orders_id' => $orders_id, 
                            'orders_products_id' => $order_products_id, 
                            'orders_products_filename' => $attributes_values['products_attributes_filename'], 
                            'download_maxdays' => $attributes_values['products_attributes_maxdays'], 
                            'download_count' => $attributes_values['products_attributes_maxcount']);
    // ccdd
    tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
  }

  $products_ordered_attributes .= "\n"
        .$mold_attr_info['title']
        .str_repeat('　', intval(($cl_max_len-mb_strlen($mold_attr_info['title'],'utf-8'))))
        .'：'.$mold_attr_info['value'];
      if ($mold_attr_res['options_values_price'] != '0') {
        $products_ordered_attributes .= '　('.$currencies->format($mold_attr_res['options_values_price']*$preorder_product_res['products_quantity']).')';
      }

}

if (isset($_SESSION['preorder_option_info'])) {
   foreach ($_SESSION['preorder_option_info'] as $op_key => $op_value) {
      $op_key_info = explode('_', $op_key);
      $option_attr_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_key_info['1']."' and id = '".$op_key_info[3]."'");
      $option_attr_values = tep_db_fetch_array($option_attr_query);
      
      if ($option_attr_values) {

      $input_option_array = array('title' => $option_attr_values['front_title'], 'value' => $op_value);
      $ao_price = 0; 
      if ($option_attr_values['type'] == 'radio') {
         $ao_option_array = @unserialize($option_attr_values['option']);
         if (!empty($ao_option_array['radio_image'])) {
           foreach ($ao_option_array['radio_image'] as $or_key => $or_value) {
             if (trim($or_value['title']) == trim($op_value)) {
               $ao_price = $or_value['money']; 
               break; 
             }
           }
         } 
      } else {
        $ao_price = $option_attr_values['price']; 
      }
      $sql_data_array = array('orders_id' => $orders_id,
                              'orders_products_id' => $order_products_id,
                              'options_values_price' => $ao_price,
                              'option_info' => tep_db_input(serialize($input_option_array)),
                              'option_group_id' => $option_attr_values['group_id'],
                              'option_item_id' => $option_attr_values['id'],
                              ); 
      tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);
      
      if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
        $sql_data_array = array('orders_id' => $orders_id, 
                                'orders_products_id' => $order_products_id, 
                                'orders_products_filename' => $attributes_values['products_attributes_filename'], 
                                'download_maxdays' => $attributes_values['products_attributes_maxdays'], 
                                'download_count' => $attributes_values['products_attributes_maxcount']);
        // ccdd
        tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
      }
      
      $products_ordered_attributes .= "\n"
        .$option_attr_values['front_title']
        .str_repeat('　', intval(($cl_max_len-mb_strlen($option_attr_values['front_title'],'utf-8'))))
        .'：'.$op_value;
      if ($ao_price != '0') {
        $products_ordered_attributes .= '　('.$currencies->format($ao_price*$preorder_product_res['products_quantity']).')';
      }
   }
   }
}

$preorder_oa_raw = tep_db_query("select * from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id = '".$_SESSION['preorder_info_id']."'");

while ($preorder_oa_res = tep_db_fetch_array($preorder_oa_raw)) {
   $sql_data_array = array('orders_id' => $orders_id,
                           'form_id' => $preorder_oa_res['form_id'], 
                           'item_id' => $preorder_oa_res['item_id'], 
                           'group_id' => $preorder_oa_res['group_id'], 
                           'name' => $preorder_oa_res['name'], 
                           'value' => $preorder_oa_res['value'], 
       );
    tep_db_perform(TABLE_OA_FORMVALUE, $sql_data_array);
 
}

$products_ordered_text .= $products_ordered_attributes;

$products_ordered_text .= "\n".'個数'.str_repeat('　', intval(($cl_max_len-mb_strlen('個数','utf-8')))).'：' .  $preorder_product_res['products_quantity'] . '個' .  tep_get_full_count2($preorder_product_res['products_quantity'], $preorder_product_res['products_id'])."\n";
$products_ordered_text .= '単価'.str_repeat('　', intval(($cl_max_len-mb_strlen('単価','utf-8')))).'：' .  $currencies->display_price(isset($option_info_array['final_price'])?$option_info_array['final_price']:$preorder_product_res['final_price'], $preorder_product_res['products_tax']) . "\n";

$products_ordered_text .= '小計'.str_repeat('　', intval(($cl_max_len-mb_strlen('小計','utf-8')))).'：' .  $currencies->display_price(isset($option_info_array['final_price'])?$option_info_array['final_price']:$preorder_product_res['final_price'], $preorder_product_res['products_tax'], $preorder_product_res['products_quantity']) . "\n";

//if (tep_not_null($_SESSION['preorder_info_character'])) {
  //$products_ordered_text .= 'キャラクター名　　：' .$_SESSION['preorder_info_character']."\n";
//}

$products_ordered_text .= "------------------------------------------\n";
if (tep_get_cflag_by_product_id($preorder_prodct_res['products_id'])) {
  if (tep_get_bflag_by_product_id($preorder_prodct_res['products_id'])) {
    $products_ordered_text .= "※ 当社キャラクター名は、お取引10分前までに電子メールにてお知らせいたします。\n\n";
  } else {
    $products_ordered_text .= "※ 当社キャラクター名は、お支払い確認後に電子メールにてお知らせいたします。\n\n";
  }
}

$mailoption['ORDER_ID']         = $orders_id;
$mailoption['ORDER_DATE']       = tep_date_long(time())  ;
$mailoption['USER_NAME']        = $preorder['customers_name'];
$mailoption['USER_MAILACCOUNT'] = $preorder['customers_email_address'];
$mailoption['ORDER_TOTAL']      = $currencies->format(abs($preorder_total_print_num));

$mailoption['TORIHIKIHOUHOU']   = $_SESSION['preorder_info_tori'];
$mailoption['ORDER_PAYMENT']    = $preorder['payment_method'];
$mailoption['ORDER_TTIME']      =  str_string($_SESSION['preorder_info_date']) .  $_SESSION['preorder_info_start_hour'] . '時' . $_SESSION['preorder_info_start_min'] .  '分~'. $_SESSION['preorder_info_end_hour'].'時'. $_SESSION['preorder_info_end_min'].'分　（24時間表記）';

$mailoption['EXTRA_COMMENT']   = '';
$mailoption['ORDER_PRODUCTS']   = $products_ordered_text;
$mailoption['ORDER_TMETHOD']    = $torihikihouhou_date_str;
$mailoption['SITE_NAME']        = STORE_NAME;
$mailoption['SITE_MAIL']        = SUPPORT_EMAIL_ADDRESS;
$mailoption['SITE_URL']         = HTTP_SERVER;

$payment_modules->preorder_deal_mailoption($mailoption, $cpayment_code, $preorder); 


$mailoption['ORDER_COUNT'] = $preorder_product_res['products_quantity'];
$mailoption['ORDER_LTOTAL'] = number_format((isset($option_info_array['final_price'])?$option_info_array['final_price']:$preorder_product_res['final_price'])*$preorder_product_res['products_quantity'], 0, '.', '');
//$mailoption['ORDER_ACTORNAME'] = $_SESSION['preorder_info_character'];
$mailoption['ORDER_ACTORNAME'] = '';
if ($preorder_point){
  $mailoption['POINT']            = str_replace('円', '', $currencies->format(abs($preorder_point)));
}else {
    $mailoption['POINT']            = 0;
}

if (isset($_SESSION['preorder_campaign_fee'])) {
  $mailoption['POINT']          = str_replace('円', '', $currencies->format(abs($_SESSION['preorder_campaign_fee'])));
}

if (!empty($preorder['code_fee'])) {
  $mailoption['MAILFEE']          = str_replace('円', '', $currencies->format(isset($option_info_array['fee'])?abs($option_info_array['fee']):abs($preorder['code_fee'])));
} else {
  $mailoption['MAILFEE']          = '0';
}

$email_order_text = '';

if (isset($payment_modules->modules[strtoupper($cpayment_code)]->show_add_comment)) {
  $mailoption['ORDER_COMMENT']    = trim($preorder['comment_msg']);
} else {
  $mailoption['ORDER_COMMENT']    = trim($order_comment_str);
}
$mailoption['ADD_INFO'] = '';

$email_order_text = $payment_modules->getOrderMailString($cpayment_code, $mailoption); 
$shipping_fee_value = isset($_POST['shipping_fee']) ? $_POST['shipping_fee'] : 0; 
$email_temp = '▼ポイント割引';
$email_temp_str = '▼ ポイント割引';
$email_shipping_fee = '▼お届け料金　　　：'.$shipping_fee_value.'円
'.$email_temp;
$email_order_text = str_replace($email_temp,$email_shipping_fee,$email_order_text);
$email_order_text = str_replace($email_temp_str,$email_shipping_fee,$email_order_text);
$email_address = '▼注文商品';
//zhusuo
$address_len_array = array();
foreach($add_list as $address_value){

  $address_len_array[] = strlen($address_value[0]);
}
$maxlen = max($address_len_array);
$email_address_str = '▼住所情報'."\n";
$email_address_str .= '------------------------------------------'."\n";
$maxlen = 9;
foreach($add_list as $ad_value){
  $ad_len = mb_strlen($ad_value[0],'utf8');
  $temp_str = str_repeat('　',$maxlen-$ad_len);
  $email_address_str .= $ad_value[0].$temp_str.'：'.$ad_value[1]."\n";
}
$email_address_str .= '------------------------------------------'."\n";
$email_address_str .= $email_address;
$email_order_text = str_replace($email_address,$email_address_str,$email_order_text);

tep_mail($preorder['customers_name'], $preorder['customers_email_address'], EMAIL_TEXT_SUBJECT, $email_order_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');
  
if (SENTMAIL_ADDRESS != '') {
    tep_mail('', SENTMAIL_ADDRESS, EMAIL_TEXT_SUBJECT2, $email_order_text, $preorder['customers_name'], $preorder['customers_email_address'], '');
}

$email_printing_order = '';
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= 'サイト名　　　　：' . STORE_NAME . "\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= '取引日時　　　　：' .  str_string($_SESSION['preorder_info_date']) . $_SESSION['preorder_info_start_hour'] . '時' .  $_SESSION['preorder_info_start_min'] . '分~'. $_SESSION['preorder_info_end_hour'] .'時'. $_SESSION['preorder_info_end_min'] .'分　（24時間表記）' . "\n";
$email_printing_order .= 'オプション　　　：' . $_SESSION['preorder_info_tori'] . "\n";
$email_printing_order .=
'------------------------------------------------------------------------' . "\n";
$email_printing_order .= '日時変更　　　　：' . date('Y') . ' 年  月  日  時  分' .
"\n";
$email_printing_order .= '日時変更　　　　：' . date('Y') . ' 年  月  日  時  分' .
"\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= '注文者名　　　　：' .
$preorder['customers_name'] . '様'
. "\n";
$email_printing_order .= '注文番号　　　　：' . $orders_id . "\n";
$email_printing_order .= '注文日　　　　　：' . tep_date_long(time()) . "\n";
$email_printing_order .= 'メールアドレス　：' . $preorder['customers_email_address'] .
"\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";

if (isset($_SESSION['preorder_campaign_fee'])) {
  if (abs($_SESSION['preorder_campaign_fee']) > 0) {
      $email_printing_order .= '割引　　　　　　：' .  abs($_SESSION['preorder_campaign_fee']). '円' . "\n";
  }
} else {
  if ($preorder_point > 0) {
      $email_printing_order .= '割引　　　　　　：' . (int)$preorder_point . '円' . "\n";
  }
}

if (!empty($option_info_array['fee'])) {
  $email_printing_order .= '手数料　　　　　：'.$option_info_array['fee'].'円'."\n";
} else {
  if (!empty($preoder['code_fee'])) {
    $email_printing_order .= '手数料　　　　　：'.$preorder['code_fee'].'円'."\n";
  }
}

$email_printing_order .= 'お支払金額　　　：' .  $currencies->format(abs($preorder_total_print_num)) . "\n";

$email_printing_order .= 'お支払方法　　　：' . $preorder['payment_method'] . "\n";
  

$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= $products_ordered_text;

$email_printing_order .= '備考　　　　　　：' . "\n";

if (!empty($order_comment_str)) {
  $email_printing_order .= $order_comment_str . "\n";
}
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= 'IPアドレス　　　　　　：' . $_SERVER["REMOTE_ADDR"] .
"\n";
$email_printing_order .= 'ホスト名　　　　　　　：' .
@gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
$email_printing_order .= 'ユーザーエージェント　：' . $_SERVER["HTTP_USER_AGENT"] .
"\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= '信用調査' . "\n";

$credit_inquiry_query = tep_db_query("select customers_fax, customers_guest_chk from " . TABLE_CUSTOMERS . " where customers_id = '" . $preorder_cus_id . "'");
$credit_inquiry       = tep_db_fetch_array($credit_inquiry_query);
$email_printing_order .= $credit_inquiry['customers_fax'] . "\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= '注文履歴　　　　　　　：';

if ($credit_inquiry['customers_guest_chk'] == '1') { $email_printing_order .= 'ゲスト'; } else { $email_printing_order .= '会員'; }
    
  $email_printing_order .= "\n";
    
  $order_history_query_raw = "select o.orders_id, o.customers_name, o.customers_id, o.date_purchased, s.orders_status_name, ot.value as order_total_value from " .  TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" .  tep_db_input($preorder_cus_id) . "' and o.orders_status = s.orders_status_id and s.language_id = '" . $languages_id . "' and ot.class = 'ot_total' order by o.date_purchased DESC limit 0,5";  
    //ccdd
    $order_history_query = tep_db_query($order_history_query_raw);
    while ($order_history = tep_db_fetch_array($order_history_query)) {
        $email_printing_order .= $order_history['date_purchased'] . '　　' .  tep_output_string_protected($order_history['customers_name']) . '　　' .  abs(intval($order_history['order_total_value'])) . '円　　' .  $order_history['orders_status_name'] . "\n";
    }

$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n\n\n";

$cpayment_class = $payment_modules->getModule($cpayment_code);
if (method_exists($cpayment_class,'getMailString')){
  $email_printing_order .= $cpayment_class->getMailString($preorder_total_print_num);
}

if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
  tep_mail('', PRINT_EMAIL_ADDRESS, STORE_NAME, $email_printing_order, $preorder['customers_name'], $preorder['customers_email_address'], '');
}

if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  if(MODULE_ORDER_TOTAL_POINT_ADD_STATUS == '0') {
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " .  intval($preorder_get_point - $preorder_point) . " where customers_id = " . $preorder_cus_id );
  } else {
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point - " .  intval($preorder_point) . " where customers_id = " . $preorder_cus_id );
  }
}

$link_customer_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$preorder_cus_id."' and site_id = '".SITE_ID."'");
$link_customer_res = tep_db_fetch_array($link_customer_raw);

if ($link_customer_res) {
  if ($link_customer_res['customers_guest_chk'] == '1') {
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = '0' where customers_id = " . $preorder_cus_id );
  }
}

if (isset($_SESSION['preorder_campaign_fee'])) {
  $campaign_raw = tep_db_query("select * from ".TABLE_CAMPAIGN." where id = '".$_SESSION['preorder_camp_id']."' and (site_id = '".SITE_ID."' or site_id = '0')"); 
  $campaign = tep_db_fetch_array($campaign_raw); 
  $sql_data_array = array(
      'customer_id' => $preorder_cus_id,
      'campaign_id' => $_SESSION['preorder_camp_id'],
      'orders_id' => $orders_id,
      'campaign_fee' => $_SESSION['preorder_campaign_fee'],
      'campaign_title' => $campaign['title'],
      'campaign_name' => $campaign['name'],
      'campaign_keyword' => $campaign['keyword'],
      'campaign_start_date' => $campaign['start_date'],
      'campaign_end_date' => $campaign['end_date'],
      'campaign_max_use' => $campaign['max_use'],
      'campaign_point_value' => $campaign['point_value'],
      'campaign_limit_value' => $campaign['limit_value'],
      'campaign_type' => $campaign['type'],
      'site_id' => SITE_ID
      );
  tep_db_perform(TABLE_CUSTOMER_TO_CAMPAIGN, $sql_data_array);
}

tep_db_query("delete from ".TABLE_PREORDERS." where orders_id = '".$_SESSION['preorder_info_id']."' and site_id = '".SITE_ID."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_DOWNLOAD." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_TO_ACTOR." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_QUESTIONS." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_QUESTIONS_PRODUCTS." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_STATUS_HISTORY." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_TO_COMPUTERS." where orders_id = '".$_SESSION['preorder_info_id']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id = '".$_SESSION['preorder_info_id']."'"); 

last_customer_action();

}



tep_session_unregister('preorder_info_tori');
tep_session_unregister('preorder_info_date');
tep_session_unregister('preorder_info_hour');
tep_session_unregister('preorder_info_min');
tep_session_unregister('preorder_info_character');
tep_session_unregister('preorder_info_id');
tep_session_unregister('preorder_info_pay');
tep_session_unregister('preorder_option_info');
if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  tep_session_unregister('preorder_point');
  tep_session_unregister('preorder_real_point');
  tep_session_unregister('preorder_get_point');
}

unset($_SESSION['preorder_option']);
unset($_SESSION['referer_adurl']);

unset($_SESSION['preorder_campaign_fee']);
unset($_SESSION['preorder_camp_id']);

tep_redirect(tep_href_link('change_preorder_success.php'));







