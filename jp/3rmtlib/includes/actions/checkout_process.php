<?php
/*
  $Id$
*/

ini_set("display_errors","Off");
require(DIR_WS_FUNCTIONS . 'visites.php');

// user new point value it from checkout_confirmation.php 
if(isset($real_point)){
  $point = $real_point;
}
// if the customer is not logged on, redirect them to the login page
if (!tep_session_is_registered('customer_id')) {
  $navigation->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_CHECKOUT_PAYMENT));
  tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
}
if ((tep_not_null(MODULE_PAYMENT_INSTALLED)) && (!tep_session_is_registered('payment')) ) {
  tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL')); 
}
// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset($cart->cartID) && tep_session_is_registered('cartID')) {
  if ($cart->cartID != $cartID) {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  }
}
// Stock Check
if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
  $products = $cart->get_products();
  for ($i=0, $n=sizeof($products); $i<$n; $i++) {
    if (tep_check_stock((int)$products[$i]['id'], $products[$i]['quantity'])) {
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
      break;
    }
  }
}

include(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PROCESS);
// load selected payment module
require(DIR_WS_CLASSES . 'payment.php');
$payment_modules = payment::getInstance(SITE_ID);
$insert_id = date("Ymd") . '-' . date("His") . tep_get_order_end_num();
# Check
//ccdd
$NewOidQuery = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS." where orders_id = '".$insert_id."' and site_id = '".SITE_ID."'");
$NewOid = tep_db_fetch_array($NewOidQuery);
if($NewOid['cnt'] > 0) {
  # OrderNo
    //$insert_id = date("Ymd") . '-' . date("His") . ds_makeRandStr(2);
    $insert_id = date("Ymd") . '-' . date("His") . tep_get_order_end_num();
}

$comments_info = $payment_modules->dealComment($payment,$comments);
if (is_array($comments_info)) {
  $comments = $comments_info['comment'];
} else {
  $comments = $comments_info;
}
require(DIR_WS_CLASSES . 'order.php');
$order = new order;

// load the before_process function from the payment modules
$payment_modules->before_process($payment);

require(DIR_WS_CLASSES . 'order_total.php');
$order_total_modules = new order_total;

$order_totals = $order_total_modules->process();
  
  
# Select
//$cnt = strlen($NewOid);
// 2003-06-06 add_telephone
$sql_data_array = array('orders_id'         => $insert_id,
                        'customers_id'      => $customer_id,
                        'customers_name'    => tep_get_fullname($order->customer['firstname'],$order->customer['lastname']),
                        'customers_name_f'  => tep_get_fullname($order->customer['firstname_f'],$order->customer['lastname_f']),
                        'customers_company' => $order->customer['company'],
                        'customers_street_address' => $order->customer['street_address'],
                        'customers_suburb' => $order->customer['suburb'],
                        'customers_city' => $order->customer['city'],
                        'customers_postcode' => $order->customer['postcode'], 
                        'customers_state' => $order->customer['state'], 
                        'customers_country' => $order->customer['country']['title'], 
                        'customers_telephone' => $order->customer['telephone'],
                        'customers_email_address' => $order->customer['email_address'],
                        'customers_address_format_id' => $order->customer['format_id'], 
                        'delivery_name'    => tep_get_fullname($order->delivery['firstname'],$order->delivery['lastname']),
                        'delivery_name_f'  => tep_get_fullname($order->delivery['firstname_f'],$order->delivery['lastname_f']),
                        'delivery_company' => $order->delivery['company'],
                        'delivery_street_address' => $order->delivery['street_address'], 
                        'delivery_suburb'    => $order->delivery['suburb'], 
                        'delivery_city'      => $order->delivery['city'], 
                        'delivery_postcode'  => $order->delivery['postcode'], 
                        'delivery_state'     => $order->delivery['state'], 
                        'delivery_country'   => $order->delivery['country']['title'], 
                        'delivery_telephone' => $order->delivery['telephone'], 
                        'delivery_address_format_id' => $order->delivery['format_id'], 
                        'billing_name' => tep_get_fullname($order->billing['firstname'],$order->billing['lastname']),
                        'billing_name_f' => tep_get_fullname($order->billing['firstname_f'],$order->billing['lastname_f']),
                        'billing_company' => $order->billing['company'],
                        'billing_street_address' => $order->billing['street_address'], 
                        'billing_suburb'   => $order->billing['suburb'], 
                        'billing_city'     => $order->billing['city'], 
                        'billing_postcode' => $order->billing['postcode'], 
                        'billing_state' => $order->billing['state'], 
                        'billing_country' => $order->billing['country']['title'], 
                        'billing_telephone' => $order->billing['telephone'], 
                        'billing_address_format_id' => $order->billing['format_id'], 
                        'payment_method' => payment::changeRomaji($order->info['payment_method'], PAYMENT_RETURN_TYPE_TITLE), 
                        'cc_type'    => $order->info['cc_type'], 
                        'cc_owner'   => $order->info['cc_owner'], 
                        'cc_number'  => $order->info['cc_number'], 
                        'cc_expires' => $order->info['cc_expires'], 
                        'date_purchased'    => 'now()', 
                        'orders_status'     => $order->info['order_status'], 
                        'currency'          => $order->info['currency'], 
                        'currency_value'    => $order->info['currency_value'],
                        'torihiki_houhou'   => $torihikihouhou,
                        'site_id'           => SITE_ID,
                        'torihiki_date'     => $insert_torihiki_date,
                        'torihiki_date_end' => $insert_torihiki_date_end,
                        'orders_ref'        => $_SESSION['referer'],
                        'orders_ref_site'   => tep_get_domain($_SESSION['referer']),
                        'orders_ref_keywords' => strtolower(SBC2DBC(parseKeyword($_SESSION['referer']))),
                        'orders_ip'         => $_SERVER['REMOTE_ADDR'],
                        'orders_host_name'  => trim(strtolower(@gethostbyaddr($_SERVER['REMOTE_ADDR']))),
                        'orders_user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'orders_wait_flag'  => 1,
                        'orders_screen_resolution'    => $_SESSION['screenResolution'],
                        'orders_color_depth'          => $_SESSION['colorDepth'],
                        'orders_flash_enable'         => $_SESSION['flashEnable'],
                        'orders_flash_version'        => $_SESSION['flashVersion'],
                        'orders_director_enable'      => $_SESSION['directorEnable'],
                        'orders_quicktime_enable'     => $_SESSION['quicktimeEnable'],
                        'orders_realplayer_enable'    => $_SESSION['realPlayerEnable'],
                        'orders_windows_media_enable' => $_SESSION['windowsMediaEnable'],
                        'orders_pdf_enable'           => $_SESSION['pdfEnable'],
                        'orders_java_enable'          => $_SESSION['javaEnable'],
                        'orders_system_language'      => $_SESSION['systemLanguage'],
                        'orders_user_language'        => $_SESSION['userLanguage'],
                        'orders_http_accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
                        'telecom_option'              => $_SESSION['option'],
                      );
//作所信息入库开始

foreach($_SESSION['options'] as $op_key=>$op_value){
  
  $address_options_query = tep_db_query("select id from ". TABLE_ADDRESS ." where name_flag='". $op_key ."'");
  $address_options_array = tep_db_fetch_array($address_options_query);
  tep_db_free_result($address_options_query);
  $address_query = tep_db_query("insert into ". TABLE_ADDRESS_ORDERS ." values(NULL,'$insert_id',$customer_id,{$address_options_array['id']},'$op_key','$op_value[1]')");
  tep_db_free_result($address_query);
}


//作所信息入库结束
  
if (isset($_SESSION['referer_adurl']) && $_SESSION['referer_adurl']) {
  $sql_data_array['orders_adurl'] = $_SESSION['referer_adurl'];
}
$telecom_option_ok = $payment_modules->dealUnknow($payment,$sql_data_array);
//所有的费用 应该都叫 code_fee
if (isset($_POST['code_fee'])) {
  $sql_data_array['code_fee'] = intval($_POST['code_fee']);
} else{
  $sql_data_array['code_fee'] = 0;
}
//配送费用
if(isset($_POST['shipping_fee'])){

  $sql_data_array['shipping_fee'] = intval($_POST['shipping_fee']);
}else{
  $sql_data_array['shipping_fee'] = 0;
}

$bflag_single = ds_count_bflag();

if ($bflag_single == 'View') {
  $orign_hand_fee = $sql_data_array['code_fee'];
  $buy_handle_fee = $payment_modules->handle_calc_fee($payment,$order->info['total']); 
  $sql_data_array['code_fee'] = $orign_hand_fee + $buy_handle_fee; 
  $new_handle_fee = $sql_data_array['code_fee'];
}
// ccdd
//$sql_data_array['orders_status'] = 30;
tep_db_perform(TABLE_ORDERS, $sql_data_array);
tep_order_status_change($insert_id,$sql_data_array['orders_status']);
$total_data_arr = array();
for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
  $sql_data_array = array('orders_id' => $insert_id,
                          'title' => $order_totals[$i]['title'],
                          'text' => $order_totals[$i]['text'],
                          'value' => $order_totals[$i]['value'], 
                          'class' => $order_totals[$i]['code'], 
                          'sort_order' => $order_totals[$i]['sort_order'],
                          );
  // ccdd
  if($telecom_option_ok!=true){
  $telecom_option_ok = $payment_modules->getExpress($payment,$order_totals,$i);
  }
  $total_data_arr[] = $sql_data_array;
}
foreach ($total_data_arr as $sql_data_array){
  tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
}

tep_order_status_change($orders['orders_id'],30);
$customer_notification = (SEND_EMAILS == 'true') ? '1' : '0';
$sql_data_array = array('orders_id' => $insert_id, 
                        'orders_status_id' => $order->info['order_status'], 
                        'date_added' => 'now()', 
                        'customer_notified' => $customer_notification,
                        'comments' => $order->info['comments']);
// ccdd
tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
  
//# 追加分（買取情報）

if ($telecom_option_ok == true) {
  tep_db_perform(TABLE_ORDERS, array('orders_status' => '30'), 'update', "orders_id='".$insert_id."'");
  $sql_data_array = array('orders_id' => $insert_id, 
                          'orders_status_id' => '30', 
                          'date_added' => 'now()', 
                          'customer_notified' => '0',
                          'comments' => 'checkout');
  // ccdd
  tep_order_status_change($orders['orders_id'],30);
  tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
  orders_updated($insert_id);
}

  

// initialized for the email confirmation
$products_ordered = '';
$subtotal = 0;
$total_tax = 0;

for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
  // Stock Update - Joao Correia
  if (STOCK_LIMITED == 'true') {
    $stock_query = tep_db_query("select products_real_quantity,products_virtual_quantity from " . TABLE_PRODUCTS .  " where products_id = '" . (int)$order->products[$i]['id'] . "'");
    if (tep_db_num_rows($stock_query) > 0) {
      $stock_values = tep_db_fetch_array($stock_query);
      if ($order->products[$i]['qty'] > $stock_values['products_real_quantity']) {
        // 荵ｰ取商品大于螳梵髏
        tep_db_perform(
                       'products',
                       array(
                             'products_virtual_quantity' => $stock_values['products_virtual_quantity'] - ($order->products[$i]['qty'] - $stock_values['products_real_quantity']),
                             'products_real_quantity'    => 0
                             ),
                       'update',
                       "products_id = '" . (int)$order->products[$i]['id'] . "'"
                       );
      } else {
        tep_db_perform(
                       'products',
                       array(
                             'products_real_quantity' => $stock_values['products_real_quantity'] - $order->products[$i]['qty'],
                             ),
                       'update',
                       "products_id = '" . (int)$order->products[$i]['id'] . "'"
                       );
      }
    }
  }

  // Update products_ordered (for bestsellers list)
  //ccdd
  tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%d', $order->products[$i]['qty']) . " where products_id = '" . (int)$order->products[$i]['id'] . "'");

  $chara = '';
  //$character_id = $order->products[$i]['id'];
  /* 
  foreach($_SESSION['character'] as $st => $en) {
    if($_SESSION['character'][$character_id] == $_SESSION['character'][$st]) {
      $chara = $_SESSION['character'][$character_id];
    }
  }
  */ 
  $sql_data_array = array('orders_id' => $insert_id, 
                          'products_id' => (int)$order->products[$i]['id'], 
                          'products_model' => $order->products[$i]['model'], 
                          'products_name' => $order->products[$i]['search_name'], // for search, insert products_name where site_id = 0
                          'products_price' => $order->products[$i]['price'], 
                          'final_price' => $order->products[$i]['final_price'], 
                          'products_tax' => $order->products[$i]['tax'], 
                          'products_quantity' => $order->products[$i]['qty'],
                          'products_rate' => tep_get_products_rate((int)$order->products[$i]['id']),
                          'site_id' => SITE_ID,
                          );
  // ccdd
  tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
  $order_products_id = tep_db_insert_id();

  //------insert customer choosen option to order--------
  $attributes_exist = '0';
  $products_ordered_attributes = '';
  if (!empty($order->products[$i]['op_attributes'])) {
    $attributes_exist = '1';
    foreach ($order->products[$i]['op_attributes'] as $op_key => $op_value) {
       
      
      $input_option_array = array('title' => $op_value['front_title'], 'value' => $op_value['value']);
      $sql_data_array = array('orders_id' => $insert_id, 
                              'orders_products_id' => $order_products_id, 
                              'options_values_price' => $op_value['price'], 
                              'option_info' => tep_db_input(serialize($input_option_array)),  
                              'option_group_id' => $op_value['group_id'], 
                              'option_item_id' => $op_value['item_id'] 
                              );
      // ccdd
      tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

      if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
        $sql_data_array = array('orders_id' => $insert_id, 
                                'orders_products_id' => $order_products_id, 
                                'orders_products_filename' => $attributes_values['products_attributes_filename'], 
                                'download_maxdays' => $attributes_values['products_attributes_maxdays'], 
                                'download_count' => $attributes_values['products_attributes_maxcount']);
        // ccdd
        tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
      }
      $products_ordered_attributes .= "\n" 
        . $op_value['front_title'] 
        . str_repeat('　',intval((27-strlen($op_value['front_title']))/3))
        . '：' . $op_value['value'];
    }
  }
  //------insert customer choosen option eof ----
  $total_weight += ($order->products[$i]['qty'] * $order->products[$i]['weight']);
  $total_tax += tep_calculate_tax($total_products_price, $products_tax) * $order->products[$i]['qty'];
  $total_cost += $total_products_price;

  $products_ordered .= '注文商品　　　　　：' . $order->products[$i]['name'];
  if(tep_not_null($order->products[$i]['model'])) {
    $products_ordered .= ' (' . $order->products[$i]['model'] . ')';
  }
  $products_ordered .= $products_ordered_attributes . "\n";
  $products_ordered .= '個数　　　　　　　：' . $order->products[$i]['qty'] . '個' .  tep_get_full_count2($order->products[$i]['qty'], (int)$order->products[$i]['id']) . "\n";
  $products_ordered .= '単価　　　　　　　：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax']) . "\n";
  $products_ordered .= '小計　　　　　　　：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . "\n";
  //if(tep_not_null($chara)) {
    //$products_ordered .= 'キャラクター名　　：' .  (EMAIL_USE_HTML === 'true' ? htmlspecialchars(stripslashes($chara)) : stripslashes($chara)) . "\n";
  //}
  $products_ordered .= "------------------------------------------\n";
  if (tep_get_cflag_by_product_id((int)$order->products[$i]['id'])) {
    if (tep_get_bflag_by_product_id((int)$order->products[$i]['id'])) {
      $products_ordered .= "※ 当社キャラクター名は、お取引10分前までに電子メールにてお知らせいたします。\n\n";
    } else {
      $products_ordered .= "※ 当社キャラクター名は、お支払い確認後に電子メールにてお知らせいたします。\n\n";
    }
  }
}

$order_type_str = tep_check_order_type($insert_id);
tep_db_query("update `".TABLE_ORDERS."` set `orders_type` = '".$order_type_str."' where orders_id = '".$insert_id."'");

orders_updated($insert_id);

$otq = tep_db_query("select * from ".TABLE_ORDERS_TOTAL." where class = 'ot_total' and orders_id = '".$insert_id."'");
$ot = tep_db_fetch_array($otq);

// mail oprion like mailprint
// CUSTOMER_INFO
$email_customer_info = '';
$email_customer_info .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_customer_info .= 'IPアドレス　　　　　　：' . $_SERVER["REMOTE_ADDR"] . "\n";
$email_customer_info .= 'ホスト名　　　　　　　：' . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
$email_customer_info .= 'ユーザーエージェント　：' . $_SERVER["HTTP_USER_AGENT"] . "\n";
$email_customer_info .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_credit_research = ''; 
$credit_inquiry_query = tep_db_query("select customers_fax, customers_guest_chk from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
$credit_inquiry       = tep_db_fetch_array($credit_inquiry_query);
$email_credit_research .= $credit_inquiry['customers_fax'] . "\n";
$email_credit_research .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_orders_history = '';
  
if ($credit_inquiry['customers_guest_chk'] == '1') { 
  $email_orders_history .= 'ゲスト'; 
} else { 
  $email_orders_history .= '会員'; 
}
  
$email_orders_history .= "\n";
  
$order_history_query_raw = "select o.orders_id, o.customers_name, o.customers_id,
  o.date_purchased, s.orders_status_name, ot.value as order_total_value from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . tep_db_input($customer_id) . "' and o.orders_status = s.orders_status_id and s.language_id = '" . $languages_id . "' and ot.class = 'ot_total' order by o.date_purchased DESC limit 0,5";  
//ccdd
$order_history_query = tep_db_query($order_history_query_raw);
while ($order_history = tep_db_fetch_array($order_history_query)) {
  $email_orders_history .= $order_history['date_purchased'] . '　　' .
    tep_output_string_protected($order_history['customers_name']) . '　　' .
    abs(intval($order_history['order_total_value'])) . '円　　' . $order_history['orders_status_name'] . "\n";
}
  
$email_orders_history .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n\n\n";

# メール本文整形 --------------------------------------{

//mailoption {
$mailoption['ORDER_ID']         = $insert_id;
$mailoption['ORDER_DATE']       = tep_date_long(time())  ;
$mailoption['USER_NAME']        = tep_get_fullname($order->customer['firstname'],$order->customer['lastname'])  ;
$mailoption['USER_MAILACCOUNT'] = $order->customer['email_address'];
$mailoption['ORDER_TOTAL']      = $currencies->format(abs($ot['value']));
@$payment_class = $payment_modules->getModule($payment);

$mailoption['TORIHIKIHOUHOU']   = $torihikihouhou;
$mailoption['ORDER_PAYMENT']    = $payment_class->title ;
$mailoption['ORDER_TTIME']      =  str_string($date) . $start_hour . '時' . $start_min . '分~'. $end_hour .'時'. $end_min .'分　（24時間表記）' ;
$mailoption['ORDER_COMMENT']    = $_SESSION['mailcomments'];//
unset($_SESSION['comments']);
$mailoption['ADD_INFO']    = str_replace("\n".$mailoption['ORDER_COMMENT'],'',trim($order->info['comments']));
$mailoption['ORDER_PRODUCTS']   = $products_ordered ;
$mailoption['ORDER_TMETHOD']    = $insert_torihiki_date;
$mailoption['SITE_NAME']        = STORE_NAME ;
$mailoption['SITE_MAIL']        = SUPPORT_EMAIL_ADDRESS ;
$mailoption['SITE_URL']         = HTTP_SERVER ;

$payment_modules->deal_mailoption($mailoption, $payment);

if ($point){
  $mailoption['POINT']            = str_replace('円', '', $currencies->format(abs($point)));
}else {
  $mailoption['POINT']            = 0;
}
if (isset($_SESSION['campaign_fee'])) {
  $mailoption['POINT']            = str_replace('円', '', $currencies->format(abs($_SESSION['campaign_fee'])));
}
if(!isset($_SESSION['mailfee'])){
  $total_mail_fee =0;
}else{
  $total_mail_fee = str_replace('円','',$_SESSION['mailfee']);
}

$mailoption['MAILFEE']          = str_replace('円','',$total_mail_fee);
$email_order = '';
$email_order = $payment_modules->getOrderMailString($payment,$mailoption);

$shipping_fee_value = isset($_POST['shipping_fee']) ? $_POST['shipping_fee'] : 0; 
$email_temp = '▼ポイント割引';
$email_temp_str = '▼ ポイント割引';
$email_shipping_fee = '▼お届け料金　　   ：'.$shipping_fee_value.'円
'.$email_temp;
$email_order = str_replace($email_temp,$email_shipping_fee,$email_order);
$email_order = str_replace($email_temp_str,$email_shipping_fee,$email_order);

// 2003.03.08 Edit Japanese osCommerce
tep_mail(tep_get_fullname($order->customer['firstname'],$order->customer['lastname']), $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');
  
if (SENTMAIL_ADDRESS != '') {
  tep_mail('', SENTMAIL_ADDRESS, EMAIL_TEXT_SUBJECT2, $email_order, tep_get_fullname($order->customer['firstname'],$order->customer['lastname']), $order->customer['email_address'], '');
}
  
last_customer_action();

# 印刷用メール本文 ----------------------------
$email_printing_order = '';
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= 'サイト名　　　　：' . STORE_NAME . "\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= '取引日時　　　　：' . str_string($date) . $start_hour . '時' . $start_min . '分~'. $end_hour .'時'. $end_min .'分　（24時間表記）' . "\n";
$email_printing_order .= 'オプション　　　：' . $torihikihouhou . "\n";
$email_printing_order .= '------------------------------------------------------------------------' . "\n";
$email_printing_order .= '日時変更　　　　：' . date('Y') . ' 年  月  日  時  分' . "\n";
$email_printing_order .= '日時変更　　　　：' . date('Y') . ' 年  月  日  時  分' . "\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= '注文者名　　　　：' . tep_get_fullname($order->customer['firstname'],$order->customer['lastname']) . '様' . "\n";
$email_printing_order .= '注文番号　　　　：' . $insert_id . "\n";
$email_printing_order .= '注文日　　　　　：' . tep_date_long(time()) . "\n";
$email_printing_order .= 'メールアドレス　：' . $order->customer['email_address'] . "\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
if (!empty($_POST['shipping_fee'])) {
  $email_printing_order .= 'お届け料金　　　　　：'.$_POST['shipping_fee'].'円'."\n"; 
}
if (isset($_SESSION['campaign_fee'])) {
  if (abs($_SESSION['campaign_fee']) > 0) {
    $email_printing_order .= '割引　　　　　　：' . abs((int)$_SESSION['campaign_fee']) . '円' . "\n";
  }
} else if ($point > 0) {
  $email_printing_order .= '割引　　：' . (int)$point . '円' . "\n";
}
if (!empty($total_mail_fee)) {
  $email_printing_order .= '手数料　　　　　：'.$total_mail_fee.'円'."\n"; 
}
$email_printing_order .= 'お支払金額　　　：' .  $currencies->format(abs($ot['value'])) . "\n";
$email_printing_order .= 'お支払方法　　　：' . $payment_class->title . "\n";
  
if(tep_not_null($bbbank)) {
  $email_printing_order .= 'お支払先金融機関' . "\n";
  $email_printing_order .= $bbbank . "\n";
}
  
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= $products_ordered;

$email_printing_order .= '備考　　　　　　：' . "\n";
if ($order->info['comments']) {
  $email_printing_order .= $order->info['comments'] . "\n";
}
$email_printing_order .= $email_customer_info;
$email_printing_order .= '信用調査' . "\n";
$email_printing_order .= $email_credit_research;
$email_printing_order .= '注文履歴　　　　　　　：';
$email_printing_order .= $email_orders_history;

if (method_exists($payment_class,'getMailString')){
  $email_printing_order .=$payment_class->getMailString($ot['value']);
}
# ------------------------------------------
// send emails to other people
if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
  tep_mail('', PRINT_EMAIL_ADDRESS, STORE_NAME, $email_printing_order, tep_get_fullname($order->customer['firstname'],$order->customer['lastname']), $order->customer['email_address'], '');
}

// Include OSC-AFFILIATE 
// require(DIR_WS_INCLUDES . 'affiliate_checkout_process.php');

//$ac_total = tep_add_tax($affiliate_total,0);
  
  
//tep_session_register('ac_total');

// load the after_process function from the payment modules
$payment_modules->after_process($payment);

$cart->reset(true);

//Add point
if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  if(MODULE_ORDER_TOTAL_POINT_ADD_STATUS == '0') {
    //ccdd

    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . intval($get_point - $point) . " where customers_id = " . $customer_id );
  } else {
    //ccdd

    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point - " . intval($point) . " where customers_id = " . $customer_id );
  }
  
  if (isset($_SESSION['campaign_fee'])) {
    $campaign_raw = tep_db_query("select * from ".TABLE_CAMPAIGN." where id = '".$_SESSION['camp_id']."' and (site_id = '".SITE_ID."' or site_id = '0')"); 
    $campaign = tep_db_fetch_array($campaign_raw); 
    $sql_data_array = array(
        'customer_id' => $customer_id,
        'campaign_id' => $_SESSION['camp_id'],
        'orders_id' => $insert_id,
        'campaign_fee' => $_SESSION['campaign_fee'],
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
}
  
  
// ゲスト購入の場合はポイントリセット
if($guestchk == '1') {
  //ccdd
  tep_db_query("update ".TABLE_CUSTOMERS." set point = '0' where customers_id = '".$customer_id."'");
}  
  
  

// unregister session variables used during checkout
tep_session_unregister('sendto');
tep_session_unregister('billto');
tep_session_unregister('shipping');
tep_session_unregister('payment');
tep_session_unregister('comments');
if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  tep_session_unregister('point');
  tep_session_unregister('get_point');
  tep_session_unregister('real_point');
}
  
tep_session_unregister('torihikihouhou');
tep_session_unregister('date');
tep_session_unregister('hour');
tep_session_unregister('min');
tep_session_unregister('insert_torihiki_date');
/*
tep_session_unregister('bank_name');
tep_session_unregister('bank_shiten');
tep_session_unregister('bank_kamoku');
tep_session_unregister('bank_kouza_num');
tep_session_unregister('bank_kouza_name');
*/
#convenience_store
unset($_SESSION['character']);
unset($_SESSION['option']);
unset($_SESSION['referer_adurl']);

  
unset($_SESSION['campaign_fee']); 
unset($_SESSION['camp_id']); 
unset($_SESSION['options']);
//$pr = '?SID=' . $convenience_sid;
  
/*
  echo '<pre>';
  foreach ($log_queries as $qk => $qv) {
  echo '[' . $log_times[$qk] . ']' . $qk . "\t=>\t" . $qv."\n";
  }
  exit;
*/

tep_redirect(tep_href_link(FILENAME_CHECKOUT_SUCCESS,'','SSL'),'T');
    
require(DIR_WS_INCLUDES . 'application_bottom.php');

