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
  
  $torihikihouhou_date_str = $_SESSION['preorder_info_date'].' '.$_SESSION['preorder_info_hour'].':'.$_SESSION['preorder_info_min'].':00';
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
                           'code_fee' => $preorder['code_fee'], 
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

  $preorder_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$_SESSION['preorder_info_id']."'");
  
  while ($preorder_total_res = tep_db_fetch_array($preorder_total_raw)) {
    if ($preorder_total_res['class'] == 'ot_total') {
      $preorder_total_num = $preorder_total_res['value'] - (int)$preorder_point; 
      $preorder_total_print_num = $preorder_total_res['value'] - (int)$preorder_point; 
    } else if ($preorder_total_res['class'] == 'ot_point') {
      $preorder_total_num = (int)$preorder_point; 
    } else {
      $preorder_total_num = $preorder_total_res['value']; 
    }
    
    if ($preorder_total_res['class'] == 'ot_subtotal') {
      $preorder_subtotal_num = $preorder_total_res['value']; 
    }
    $sql_data_array = array('orders_id' => $orders_id,
                            'title' => $preorder_total_res['title'], 
                            'text' => $preorder_total_res['text'], 
                            'value' => $preorder_total_num, 
                            'class' => $preorder_total_res['class'], 
                            'sort_order' => $preorder_total_res['sort_order'], 
        ); 
    if ($preorder_total_res['class'] == 'ot_total') {
      $telecom_option_ok = $payment_modules->getPreexpress((int)$preorder_total_res['value'], $orders_id, $cpayment_code); 
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
                          'final_price' => $preorder_product_res['final_price'], 
                          'products_tax' => $preorder_product_res['products_tax'], 
                          'products_quantity' => $preorder_product_res['products_quantity'], 
                          'products_rate' => $preorder_product_res['products_rate'], 
                          'products_character' => isset($_SESSION['preorder_info_character'])?$_SESSION['preorder_info_character']:'',
                          'torihiki_date' => $torihikihouhou_date_str, 
                          'site_id' => SITE_ID
      );
  tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
  $order_products_id = tep_db_insert_id();

  $products_ordered_text .= '注文商品　　　　　：'.$preorder_product_res['products_name'];
  if (tep_not_null($preorder_product_res['products_model'])) {
    $products_ordered_text .= ' ('.$preorder_product_res['products_model'].')'; 
  }
  
  $products_ordered_atttibutes_text = '';
  
if (isset($_SESSION['preorder_info_attr'])) {
   foreach ($_SESSION['preorder_info_attr'] as $key => $value) {
      if (DOWNLOAD_ENABLED == 'true') {
        $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_attributes_id, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad on pa.products_attributes_id=pad.products_attributes_id where pa.products_id = '" .  $preorder_product_res['products_id'] . "' and pa.options_id = '" . $key . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $value . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $languages_id . "' and poval.language_id = '" . $languages_id . "'";
        $attributes = tep_db_query($attributes_query);
      } else {
        $attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_attributes_id from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES .  " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . $preorder_product_res['products_id'] . "' and pa.options_id = '" .  $key . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $value . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $languages_id . "' and poval.language_id = '" . $languages_id . "'");
      }
      $attributes_values = tep_db_fetch_array($attributes);
      
      $sql_data_array = array('orders_id' => $orders_id, 
                              'orders_products_id' => $order_products_id, 
                              'products_options' => $attributes_values['products_options_name'],
                              'products_options_values' => $attributes_values['products_options_values_name'], 
                              'options_values_price' => $attributes_values['options_values_price'], 
                              'price_prefix' => $attributes_values['price_prefix'],
                              'attributes_id'  => $attributes_values['products_attributes_id']);
      // ccdd
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
        .$attributes_values['products_options_name']
        .str_repeat('　', intval((27-strlen($attributes_values['products_options_name']))/3))
        .'：'.$attributes_values['products_options_values_name'];
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

$products_ordered_text .= "\n".'個数　　　　　　　：' .  $preorder_product_res['products_quantity'] . '個' .  "\n";
$products_ordered_text .= '単価　　　　　　　：' .  $currencies->display_price($preorder_product_res['final_price'], $preorder_product_res['products_tax']) . "\n";

$products_ordered_text .= '小計　　　　　　　：' .  $currencies->display_price($preorder_product_res['final_price'], $preorder_product_res['products_tax'], $preorder_product_res['products_quantity']) . "\n";

if (tep_not_null($_SESSION['preorder_info_character'])) {
  $products_ordered_text .= 'キャラクター名　　：' .$_SESSION['preorder_info_character']."\n";
}

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
$mailoption['ORDER_TTIME']      =  str_string($_SESSION['preorder_info_date']) .  $_SESSION['preorder_info_hour'] . '時' . $_SESSION['preorder_info_min'] .  '分　（24時間表記）';

$mailoption['EXTRA_COMMENT']   = '';
$mailoption['ORDER_PRODUCTS']   = $products_ordered_text;
$mailoption['ORDER_TMETHOD']    = $torihikihouhou_date_str;
$mailoption['SITE_NAME']        = STORE_NAME;
$mailoption['SITE_MAIL']        = SUPPORT_EMAIL_ADDRESS;
$mailoption['SITE_URL']         = HTTP_SERVER;
$bank_info_array = explode('<<<|||', $preorder['bank_info']);
$mailoption['BANK_NAME'] = $bank_info_array[0];
$mailoption['BANK_SHITEN'] = $bank_info_array[1];
$mailoption['BANK_KAMOKU'] = $bank_info_array[2];
$mailoption['BANK_KOUZA_NUM'] = $bank_info_array[3];
$mailoption['BANK_KOUZA_NAME'] = $bank_info_array[4];

$mailoption['ORDER_COUNT'] = $preorder_product_res['products_quantity'];
$mailoption['ORDER_LTOTAL'] = number_format($preorder_product_res['final_price']*$preorder_product_res['products_quantity'], 0, '.', '');
$mailoption['ORDER_ACTORNAME'] = $_SESSION['preorder_info_character'];
if ($preorder_point){
  $mailoption['POINT']            = str_replace('円', '', $currencies->format(abs($preorder_point)));
}else {
    $mailoption['POINT']            = 0;
}

if (!empty($preorder['code_fee'])) {
  $mailoption['MAILFEE']          = str_replace('円', '', $currencies->format(abs($preorder['code_fee'])));
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
tep_mail($preorder['customers_name'], $preorder['customers_email_address'], EMAIL_TEXT_SUBJECT, $email_order_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');
  
if (SENTMAIL_ADDRESS != '') {
    tep_mail('', SENTMAIL_ADDRESS, EMAIL_TEXT_SUBJECT2, $email_order_text, $preorder['customers_name'], $preorder['customers_email_address'], '');
}

$email_printing_order = '';
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= 'サイト名　　　　：' . STORE_NAME . "\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= '取引日時　　　　：' .  str_string($_SESSION['preorder_info_date']) . $_SESSION['preorder_info_hour'] . '時' .  $_SESSION['preorder_info_min'] . '分　（24時間表記）' . "\n";
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

if ($preorder_point > 0) {
    $email_printing_order .= '□ポイント割引　　：' . (int)$preorder_point . '円' . "\n";
}

if (!empty($preoder['code_fee'])) {
  $email_printing_order .= '手数料　　　　　：'.$preorder['code_fee'].'円'."\n";
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
if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  tep_session_unregister('preorder_point');
  tep_session_unregister('preorder_real_point');
  tep_session_unregister('preorder_get_point');
}

unset($_SESSION['preorder_option']);
unset($_SESSION['referer_adurl']);

tep_redirect(tep_href_link('change_preorder_success.php'));







