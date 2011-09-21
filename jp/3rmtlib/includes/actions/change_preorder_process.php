<?php
/*
  $Id$
*/

if (!isset($_POST['pid'])) {
  forward404();
}

$preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$_POST['pid']."' and site_id = '".SITE_ID."'");
$preorder = tep_db_fetch_array($preorder_raw);

if ($preorder) {
  $order_query = tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$_POST['pid']."'"); 
  $orders_id = $_POST['pid'];
  
  if (tep_db_num_rows($order_query)) {
    $orders_id = date('Ymd').'-'.date('His').tep_get_order_end_num(); 
  }
  
  $torihikihouhou_date_str = $_POST['date'].' '.$_POST['hour'].':'.$_POST['min'].':00';
  $default_status_raw = tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".DEFAULT_ORDERS_STATUS_ID."'");
  $default_status_res = tep_db_fetch_array($default_status_raw); 
   
  $sql_data_array = array('orders_id' => $orders_id,
                           'site_id' => $preorder['site_id'], 
                           'customers_id' => $preorder['customers_id'], 
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
                           'torihiki_houhou' => $_POST['torihikihouhou'], 
                           'torihiki_date' => $torihikihouhou_date_str, 
                           'code_fee' => $preorder['code_fee'], 
                           'language_id' => $preorder['language_id'], 
                           'orders_status_name' => $default_status_res['orders_status_name'], 
                           'orders_status_image' => $preorder['orders_status_image'],
                           'finished' => $preorder['finished'], 
                           'orders_ref' => $preorder['orders_ref'], 
                           'orders_ref_site' => $preorder['orders_ref_site'], 
                           'orders_ip' => $preorder['orders_ip'], 
                           'orders_host_name' => $preorder['orders_host_name'], 
                           'orders_user_agent' => $preorder['orders_user_agent'], 
                           'orders_comment' => $preorder['orders_comment'], 
                           'orders_important_flag' => $preorder['orders_important_flag'], 
                           'orders_care_flag' => $preorder['orders_care_flag'], 
                           'orders_wait_flag' => $preorder['orders_wait_flag'], 
                           'orders_inputed_flag' => $preorder['orders_inputed_flag'], 
                           'orders_screen_resolution' => $preorder['orders_screen_resolution'], 
                           'orders_color_depth' => $preorder['orders_color_depth'], 
                           'orders_flash_enable' => $preorder['orders_flash_enable'], 
                           'orders_flash_version' => $preorder['orders_flash_version'], 
                           'orders_director_enable' => $preorder['orders_director_enable'], 
                           'orders_quicktime_enable' => $preorder['orders_quicktime_enable'], 
                           'orders_realplayer_enable' => $preorder['orders_realplayer_enable'], 
                           'orders_windows_media_enable' => $preorder['orders_windows_media_enable'], 
                           'orders_pdf_enable' => $preorder['orders_pdf_enable'], 
                           'orders_java_enable' => $preorder['orders_java_enable'], 
                           'orders_http_accept_language' => $preorder['orders_http_accept_language'], 
                           'orders_system_language' => $preorder['orders_system_language'], 
                           'orders_user_language' => $preorder['orders_user_language'], 
                           'orders_work' => $preorder['orders_work'], 
                           'q_8_1' => $preorder['q_8_1'], 
                           'telecom_name' => $preorder['telecom_name'], 
                           'telecom_tel' => $preorder['telecom_tel'], 
                           'telecom_money' => $preorder['telecom_money'], 
                           'telecom_email' => $preorder['telecom_email'], 
                           'telecom_clientip' => $preorder['telecom_clientip'], 
                           'telecom_option' => $preorder['telecom_option'], 
                           'telecom_cont' => $preorder['telecom_cont'], 
                           'telecom_sendid' => $preorder['telecom_sendid'], 
                           'telecom_unknow' => $preorder['telecom_unknow'], 
                           'orders_ref_keywords' => $preorder['orders_ref_keywords'], 
                           'orders_adurl' => $preorder['orders_adurl'], 
                           'paypal_paymenttype' => $preorder['paypal_paymenttype'], 
                           'paypal_payerstatus' => $preorder['paypal_payerstatus'], 
                           'paypal_paymentstatus' => $preorder['paypal_paymentstatus'], 
                           'paypal_countrycode' => $preorder['paypal_countrycode'], 
                           'paypal_token' => $preorder['paypal_token'], 
                           'paypal_playerid' => $preorder['paypal_playerid'], 
                           'flag_qaf' => $preorder['flag_qaf'], 
                           'end_user' => $preorder['end_user'], 
                           'confirm_payment_time' => $preorder['confirm_payment_time'],
                           'orders_type' => 1, 
                          );
  tep_db_perform(TABLE_ORDERS, $sql_data_array);

  $preorder_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_POST['pid']."'");
  
  while ($preorder_total_res = tep_db_fetch_array($preorder_total_raw)) {
    $sql_data_array = array('orders_id' => $orders_id,
                            'title' => $preorder_total_res['title'], 
                            'text' => $preorder_total_res['text'], 
                            'value' => $preorder_total_res['value'], 
                            'class' => $preorder_total_res['class'], 
                            'sort_order' => $preorder_total_res['sort_order'], 
        ); 
    tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
  }
  
  $preorder_status_history_raw = tep_db_query("select * from ".TABLE_PREORDERS_STATUS_HISTORY." where orders_id = '".$_POST['pid']."' and comments != '' order by orders_status_history_id asc");  
  $preorder_status_history_res = tep_db_fetch_array($preorder_status_history_raw);
  $sh_comments = ''; 
  if ($preorder_status_history_res) {
    $sh_comments = $preorder_status_history_res['comments']; 
  }
  $customer_notification = (SEND_EMAILS == 'true') ? '1' : '0'; 
  $sql_data_array = array('orders_id' => $orders_id,
                          'orders_status_id' => DEFAULT_ORDERS_STATUS_ID, 
                          'date_added' => date('Y-m-d H:i:s', time()), 
                          'customer_notified' => $customer_notification, 
                          'comments' => $sh_comments, 
      ); 
  tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
  
  
  $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_POST['pid']."'"); 
  $preorder_product_res = tep_db_fetch_array($preorder_product_raw); 
  $sql_data_array = array('orders_id' => $orders_id,
                          'products_id' => $preorder_product_res['products_id'],
                          'products_model' => $preorder_product_res['products_model'], 
                          'products_name' => $preorder_product_res['products_name'], 
                          'products_price' => $preorder_product_res['products_price'], 
                          'final_price' => $preorder_product_res['final_price'], 
                          'products_tax' => $preorder_product_res['products_tax'], 
                          'products_quantity' => $preorder_product_res['products_quantity'], 
                          'products_rate' => $preorder_product_res['products_rate'], 
                          'products_character' => isset($_POST['p_character'])?$_POST['p_character']:'',
                          'torihiki_date' => $torihikihouhou_date_str, 
                          'site_id' => SITE_ID
      );
  tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);

if (isset($_POST['op_id'])) {
   foreach ($_POST['op_id'] as $key => $value) {
      if (DOWNLOAD_ENABLED == 'true') {
        $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_attributes_id, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad on pa.products_attributes_id=pad.products_attributes_id where pa.products_id = '" .  $preorder_product_res['products_id'] . "' and pa.options_id = '" . $key . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $value . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $languages_id . "' and poval.language_id = '" . $languages_id . "'";
        $attributes = tep_db_query($attributes_query);
      } else {
        $attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_attributes_id from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES .  " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . $preorder_product_res['products_id'] . "' and pa.options_id = '" .  $key . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $value . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $languages_id . "' and poval.language_id = '" . $languages_id . "'");
      }
      $attributes_values = tep_db_fetch_array($attributes);
      
      $sql_data_array = array('orders_id' => $orders_id, 
                              'orders_products_id' => $preorder_product_res['products_id'], 
                              'products_options' => $attributes_values['products_options_name'],
                              'products_options_values' => $attributes_values['products_options_values_name'], 
                              'options_values_price' => $attributes_values['options_values_price'], 
                              'price_prefix' => $attributes_values['price_prefix'],
                              'attributes_id'  => $attributes_values['products_attributes_id']);
      // ccdd
      tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);
      
      if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
        $sql_data_array = array('orders_id' => $orders_id, 
                                'orders_products_id' => $preorder_product_res['products_id'], 
                                'orders_products_filename' => $attributes_values['products_attributes_filename'], 
                                'download_maxdays' => $attributes_values['products_attributes_maxdays'], 
                                'download_count' => $attributes_values['products_attributes_maxcount']);
        // ccdd
        tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
      }
      
   }
}


$preorder_oa_raw = tep_db_query("select * from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id = '".$_POST['pid']."'");

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


$preorders_computer_raw = tep_db_query("select * from ".TABLE_PREORDERS_TO_COMPUTERS." where orders_id = '".$_POST['pid']."'");
while ($preorders_computers_res = tep_db_fetch_array($preorders_computer_raw)) {
  $sql_data_array = array('orders_id' => $orders_id,
                          'computers_id' => $preorders_computers_res['computers_id'], 
      );
  tep_db_perform('orders_to_computers', $sql_data_array);
}

tep_db_query("delete from ".TABLE_PREORDERS." where orders_id = '".$_POST['pid']."' and site_id = '".SITE_ID."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_POST['pid']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$_POST['pid']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_DOWNLOAD." where orders_id = '".$_POST['pid']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_PRODUCTS_TO_ACTOR." where orders_id = '".$_POST['pid']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_QUESTIONS." where orders_id = '".$_POST['pid']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_QUESTIONS_PRODUCTS." where orders_id = '".$_POST['pid']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_STATUS_HISTORY." where orders_id = '".$_POST['pid']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_POST['pid']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_TO_COMPUTERS." where orders_id = '".$_POST['pid']."'"); 
tep_db_query("delete from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id = '".$_POST['pid']."'"); 

}


tep_redirect(tep_href_link('change_preorder_success.php'));







