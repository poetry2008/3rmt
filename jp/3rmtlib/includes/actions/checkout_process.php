<?php
/*
  $Id$
*/
require(DIR_WS_FUNCTIONS . 'visites.php');
ini_set('display_errors' ,'On');

// user new point value it from checkout_confirmation.php 
if(isset($real_point)){
  $point = $real_point;
}

// if the customer is not logged on, redirect them to the login page

if (!tep_session_is_registered('customer_id')) {
  $navigation->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_CHECKOUT_PAYMENT));
  tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
}
  
//  if (!tep_session_is_registered('sendto')) {
//    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));

//  }

if ( (tep_not_null(MODULE_PAYMENT_INSTALLED)) && (!tep_session_is_registered('payment')) ) {
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
    if (tep_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
      break;
    }
  }
}

include(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PROCESS);

// load selected payment module
require(DIR_WS_CLASSES . 'payment.php');
$payment_modules = new payment($payment);

// load the selected shipping module
/*
  require(DIR_WS_CLASSES . 'shipping.php');
  $shipping_modules = new shipping($shipping);
  // add for Japanese update
  if (isset($shipping['timespec'])) {
  $comments = '['.TEXT_TIME_SPECIFY.$shipping['timespec'].']'
  ."\n".$comments;
  }
*/
  
# OrderNo
//if(!isset($_GET['option'])){
  $insert_id = date("Ymd") . '-' . date("His") . ds_makeRandStr(2);
//}else {
//  $insert_id = $_GET['option'];
//}
# Check
//ccdd
$NewOidQuery = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS." where orders_id = '".$insert_id."' and site_id = '".SITE_ID."'");
$NewOid = tep_db_fetch_array($NewOidQuery);
if($NewOid['cnt'] == 0) {
  # OrderNo
    $insert_id = date("Ymd") . '-' . date("His") . ds_makeRandStr(2);
}
  
# load the selected shipping module(convenience_store)
if ($payment == 'convenience_store') {
  $convenience_sid = str_replace('-', "", $insert_id);
  //$pay_comments = '取引コード' . $convenience_sid ."\n";
  //$pay_comments .= '郵便番号:' . $_POST['convenience_store_zip_code'] ."\n";
  //$pay_comments .= '住所1:' . $_POST['convenience_store_address1'] ."\n";
  //$pay_comments .= '住所2:' . $_POST['convenience_store_address2'] ."\n";
  //$pay_comments .= '氏:' . $_POST['convenience_store_l_name'] ."\n";
  //$pay_comments .= '名:' . $_POST['convenience_store_f_name'] ."\n";
  //$pay_comments .= '電話番号:' . $_POST['convenience_store_tel'] ."\n";
  //$pay_comments .= '接続URL:' . tep_href_link('convenience_store_chk.php', 'sid=' . $convenience_sid, 'SSL');
  $pay_comments = 'PCメールアドレス:'.$_POST['convenience_email']; 
  $comments = $pay_comments ."\n".$comments;
}

require(DIR_WS_CLASSES . 'order.php');
$order = new order;

// load the before_process function from the payment modules
$payment_modules->before_process();

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
                        'payment_method' => $order->info['payment_method'], 
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
  
// 扈溯ｮ｡Google Adsense来源
if (isset($_SESSION['referer_adurl']) && $_SESSION['referer_adurl']) {
  $sql_data_array['orders_adurl'] = $_SESSION['referer_adurl'];
}
// 鬪瑚ｯ＆s明信用蜊｡ 
if ($_SESSION['option']) {

$telecom_unknow = tep_db_fetch_array(tep_db_query("select * from telecom_unknow where `option`='".$_SESSION['option']."' and rel='yes'"));
if ($telecom_unknow) {
$sql_data_array['telecom_name']  = $telecom_unknow['username'];
$sql_data_array['telecom_tel']   = $telecom_unknow['telno'];
$sql_data_array['telecom_email'] = $telecom_unknow['email'];
$sql_data_array['telecom_money'] = $telecom_unknow['money'];
tep_db_query("update `telecom_unknow` set type='success' where `option`='".$_SESSION['option']."' and rel='yes' order by date_added limit 1");

$telecom_option_ok = true;
}
}
if (isset($_POST['codt_fee'])) {
  $sql_data_array['code_fee'] = intval($_POST['codt_fee']);
} else if (isset($_POST['money_order_fee'])) {
  $sql_data_array['code_fee'] = intval($_POST['money_order_fee']);
} else if (isset($_POST['postal_money_order_fee'])) {
  $sql_data_array['code_fee'] = intval($_POST['postal_money_order_fee']);
} else if (isset($_POST['telecom_order_fee'])) {
  $sql_data_array['code_fee'] = intval($_POST['telecom_order_fee']);
} else {
  $sql_data_array['code_fee'] = 0;
}
  
$bflag_single = ds_count_bflag();
if ($bflag_single == 'View') {
  $orign_hand_fee = $sql_data_array['code_fee'];
  $buy_handle_fee = calc_buy_handle($order->info['total']); 
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
                          'sort_order' => $order_totals[$i]['sort_order']);
  // ccdd

  if($order_totals[$i]['code'] =='ot_total' &&  array_key_exists('token', $_REQUEST)){
    $token = urlencode(htmlspecialchars($_REQUEST['token']));
    getexpress($order_totals[$i]['value'],$token);
    $telecom_option_ok = true;
  }
  $total_data_arr[] = $sql_data_array;
}
  foreach ($total_data_arr as $sql_data_array){
  tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
  }

//ペイパルの決済を完了させる
function getexpress($amt,$token){
  $paypalData = array();
  $testcode = 1;
  global $insert_id;
  // Add request-specific fields to the request string.
  $nvpStr = "&TOKEN=$token";
  // Execute the API operation; see the PPHttpPost function above.
  $httpParsedResponseAr = PPHttpPost('GetExpressCheckoutDetails', $nvpStr);

  if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
    foreach($httpParsedResponseAr as $key=>$value){
      $paypalData[$key] = urldecode($value);
    }
    // Extract the response details.
    $payerID = urlencode($httpParsedResponseAr['PAYERID']);
    $paymentType = urlencode("Sale");     // or 'Sale' or 'Order'
    $paymentAmount = urlencode($amt);
    $currencyID = urlencode("JPY");   
    //$token = urlencode($httpParsedResponseAr['TOKEN']);
    $nvpStr = "&TOKEN=$token&PAYERID=$payerID&AMT=$paymentAmount&PAYMENTACTION=$paymentType&CURRENCYCODE=$currencyID";

    // Execute the API operation; see the PPHttpPost function above.
    $httpParsedResponseAr = PPHttpPost('DoExpressCheckoutPayment', $nvpStr);
    /*
      ★PAYMENTTYPE      支払いが即時に行われるか遅れて行われるかを示します。 譏ｾ示及譌ｶ支付霑・･諡冶ｿ沁x付
      ★PAYERSTATUS      支払人のステータス 支付人身莉ｽ
      ★PAYMENTSTATUS      支払いのステータス。 支付状諤閼      Completed: 支払いが完了し、会員残高に正常に入金されました。 支付完豈普C蟶先姐余鬚攝ｳ常霑寢ｼ
      ★COUNTRYCODE      支払人の居住国 支付人居住国家
      ○EMAIL      支払人のメールアドレス。 支付人的驍ｮ箱  found
      ○AMT      最終請求金額。 最后申隸ｷ金鬚魘   found
      ○FIRSTNAME      支払人の名 支付人名字
      ○LASTNAME      支払人の姓。 支付人姓
      ○PHONENUM      支払人の電話番号 支付人逕ｵ隸搓・黴閼   found 
    */
    //var_dump($httpParsedResponseAr['ACK']);
    foreach($httpParsedResponseAr as $key=>$value){
      $paypalData[$key] = urldecode($value);
    }

    if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
      //成功コード発行予定
      //$sql_data_array['money'] =$httpParsedResponseAr["AMT"];
      //$sql_data_array['type']="success";
      //$sql_data_array['rel']="yes";
      //$sql_data_array['date_added']= 'now()';
      //$sql_data_array['last_modified']= 'now()';
      //      tep_db_perform("telecom_unknow", $sql_data_array);
      //エラーコード発行予定
      //                  exit('DoExpressCheckoutPayment failed: ' . urldecode(print_r($httpParsedResponseAr, true)));
      if($paypalData['PAYMENTSTATUS'] == "Completed"){
                  tep_db_perform('telecom_unknow', array(
        'payment_method' => 'paypal',
        '`option`'      => ' ',
        'username'      => $paypalData['FIRSTNAME'] . '' . $paypalData['LASTNAME'],
        'email'         => $paypalData['EMAIL'],
        'telno'         => $paypalData['PHONENUM'],
        'money'         => $paypalData['AMT'],
        'rel'           => 'yes',
        'type'          => 'success',
        'date_added'    => 'now()',
        'last_modified' => 'now()'
      ));
      }else{
      //不明扱い
                  tep_db_perform('telecom_unknow', array(
        'payment_method' => 'paypal',
        '`option`'      => ' ',
        'username'      => $paypalData['FIRSTNAME'] . '' . $paypalData['LASTNAME'],
        'email'         => $paypalData['EMAIL'],
        'telno'         => $paypalData['PHONENUM'],
        'money'         => $paypalData['AMT'],
        'rel'           => 'no',
        'date_added'    => 'now()',
        'last_modified' => 'now()'
      ));
              tep_db_query("delete from ".TABLE_ORDERS." where
            orders_id='".$insert_id."'");
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_UNSUCCESS,
                  'msg=paypal_error'));
            exit;
      }

    }else{
        tep_db_query("delete from ".TABLE_ORDERS." where
            orders_id='".$insert_id."'");
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_UNSUCCESS,
                  'msg=paypal_error'));
            exit;
    }
  }else{
        tep_db_query("delete from ".TABLE_ORDERS." where
            orders_id='".$insert_id."'");
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_UNSUCCESS,
                  'msg=paypal_error'));
            exit;
    // 不正
    //エラーコード発行予定
   // exit('GetExpressCheckoutDetails failed: ' . urldecode(print_r($httpParsedResponseAr, true)));
  }
  tep_db_perform(TABLE_ORDERS, array(
                                     'paypal_paymenttype'   => $paypalData['PAYMENTTYPE'],
                                     'paypal_payerstatus'   => $paypalData['PAYERSTATUS'],
                                     'paypal_paymentstatus' => $paypalData['PAYMENTSTATUS'],
                                     'paypal_countrycode'   => $paypalData['COUNTRYCODE'],
                                     'telecom_email'        => $paypalData['EMAIL'],
                                     'telecom_money'        => $paypalData['AMT'],
                                     'telecom_name'         => $paypalData['FIRSTNAME'] . ''. $paypalData['LASTNAME'],
                                     'telecom_tel'          => $paypalData['PHONENUM'],
                                     'orders_status'        => '30',
                                     'paypal_playerid'      => $payerID,
                                     'paypal_token'         => $token,
                                     ), 'update', "orders_id='".$insert_id."'");
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
if(tep_session_is_registered('bank_name')) {
  $bbbank = TEXT_BANK_NAME . '：' . $bank_name . "\n";
  $bbbank .= TEXT_BANK_SHITEN . '：' . $bank_shiten . "\n";
  $bbbank .= TEXT_BANK_KAMOKU . '：' . $bank_kamoku . "\n";
  $bbbank .= TEXT_BANK_KOUZA_NUM . '：' . $bank_kouza_num . "\n";
  $bbbank .= TEXT_BANK_KOUZA_NAME . '：' . $bank_kouza_name;

  $sql_data_array = array('orders_id' => $insert_id, 
                          'orders_status_id' => $order->info['order_status'], 
                          'date_added' => 'now()', 
                          'customer_notified' => $customer_notification,
                          'comments' => $bbbank);
  // ccdd
  tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
}
  

if ($telecom_option_ok) {
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
    if (DOWNLOAD_ENABLED == 'true') {
      $stock_query_raw = "SELECT products_real_quantity,products_virtual_quantity, pad.products_attributes_filename 
                            FROM " . TABLE_PRODUCTS . " p
                            LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                             ON p.products_id=pa.products_id
                            LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                             ON pa.products_attributes_id=pad.products_attributes_id
                            WHERE p.products_id = '" . tep_get_prid($order->products[$i]['id']) . "'";
      // Will work with only one option for downloadable products
      // otherwise, we have to build the query dynamically with a loop
      $products_attributes = $order->products[$i]['attributes'];
      if (is_array($products_attributes)) {
        $stock_query_raw .= " AND pa.options_id = '" . $products_attributes[0]['option_id'] . "' AND pa.options_values_id = '" . $products_attributes[0]['value_id'] . "'";
      }
      //ccdd
      $stock_query = tep_db_query($stock_query_raw);
    } else {
      //ccdd
      $stock_query = tep_db_query("select products_real_quantity,products_virtual_quantity from " . TABLE_PRODUCTS . " where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
    }
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
                       "products_id = '" . tep_get_prid($order->products[$i]['id']) . "'"
                       );
      } else {
        tep_db_perform(
                       'products',
                       array(
                             'products_real_quantity' => $stock_values['products_real_quantity'] - $order->products[$i]['qty'],
                             ),
                       'update',
                       "products_id = '" . tep_get_prid($order->products[$i]['id']) . "'"
                       );
      }
    }
  }

  // Update products_ordered (for bestsellers list)
  //ccdd
  tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%d', $order->products[$i]['qty']) . " where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");

  $chara = '';
  $character_id = $order->products[$i]['id'];
  foreach($_SESSION['character'] as $st => $en) {
    if($_SESSION['character'][$character_id] == $_SESSION['character'][$st]) {
      $chara = $_SESSION['character'][$character_id];
    }
  }
  
  $sql_data_array = array('orders_id' => $insert_id, 
                          'products_id' => tep_get_prid($order->products[$i]['id']), 
                          'products_model' => $order->products[$i]['model'], 
                          'products_name' => $order->products[$i]['search_name'], // for search, insert products_name where site_id = 0
                          'products_price' => $order->products[$i]['price'], 
                          'final_price' => $order->products[$i]['final_price'], 
                          'products_tax' => $order->products[$i]['tax'], 
                          'products_quantity' => $order->products[$i]['qty'],
                          'products_rate' => tep_get_products_rate(tep_get_prid($order->products[$i]['id'])),
                          'products_character' =>  stripslashes($chara),
                          'site_id' => SITE_ID
                          );
  // ccdd
  tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
  $order_products_id = tep_db_insert_id();

  //------insert customer choosen option to order--------
  $attributes_exist = '0';
  $products_ordered_attributes = '';
  if (isset($order->products[$i]['attributes'])) {
    $attributes_exist = '1';
    for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
      if (DOWNLOAD_ENABLED == 'true') {
        $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_attributes_id, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename 
                               from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa 
                               left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                on pa.products_attributes_id=pad.products_attributes_id
                               where pa.products_id = '" . $order->products[$i]['id'] . "' 
                                and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "' 
                                and pa.options_id = popt.products_options_id 
                                and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "' 
                                and pa.options_values_id = poval.products_options_values_id 
                                and popt.language_id = '" . $languages_id . "' 
                                and poval.language_id = '" . $languages_id . "'";
        //ccdd
        $attributes = tep_db_query($attributes_query);
      } else {
        //ccdd
        $attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_attributes_id from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . $order->products[$i]['id'] . "' and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $languages_id . "' and poval.language_id = '" . $languages_id . "'");
      }
      $attributes_values = tep_db_fetch_array($attributes);
    
      //---------------------------------------
      // オプションの在庫数減処理 - 2005.09.20
      //---------------------------------------
      if (STOCK_LIMITED == 'true') {
        $zaiko = $attributes_values['products_at_quantity']-$order->products[$i]['qty'];
        //ccdd
        tep_db_query("update ".TABLE_PRODUCTS_ATTRIBUTES." set products_at_quantity = '". $zaiko ."' where products_id = '" . tep_get_prid($order->products[$i]['id']) . "' and options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "' and options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'");

        //全てのオプション値が「0」担った時点で商品のステータスを（falseに）更新
        //ccdd
        $attributes_stock_check_query = tep_db_query("select * from ".TABLE_PRODUCTS_ATTRIBUTES." where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
        $stock_cnt = 0;
        while($attributes_stock_check = tep_db_fetch_array($attributes_stock_check_query)) {
          $stock_cnt += $attributes_stock_check['products_at_quantity'];
        }
    
        if($stock_cnt > 0) {
          //Not process
        } else {
          //Update products_status(TABLE: PRODUCTS)
          //ccdd
          tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_status = '0' where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
        }
      }
      //--------------------------------------END

      $sql_data_array = array('orders_id' => $insert_id, 
                              'orders_products_id' => $order_products_id, 
                              'products_options' => $attributes_values['products_options_name'],
                              'products_options_values' => $attributes_values['products_options_values_name'], 
                              'options_values_price' => $attributes_values['options_values_price'], 
                              'price_prefix' => $attributes_values['price_prefix'],
                              'attributes_id'  => $attributes_values['products_attributes_id']);
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
        . $attributes_values['products_options_name'] 
        . str_repeat('　',intval((27-strlen($attributes_values['products_options_name']))/3))
        . '：' . $attributes_values['products_options_values_name'];
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
  $products_ordered .= '個数　　　　　　　：' . $order->products[$i]['qty'] . '個' . tep_get_full_count2($order->products[$i]['qty'], $order->products[$i]['id']) . "\n";
  $products_ordered .= '単価　　　　　　　：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax']) . "\n";
  $products_ordered .= '小計　　　　　　　：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . "\n";
  if(tep_not_null($chara)) {
    $products_ordered .= 'キャラクター名　　：' .  (EMAIL_USE_HTML === 'true' ? htmlspecialchars(stripslashes($chara)) : stripslashes($chara)) . "\n";
  }
  $products_ordered .= "------------------------------------------\n";
  if (tep_get_cflag_by_product_id($order->products[$i]['id'])) {
    if (tep_get_bflag_by_product_id($order->products[$i]['id'])) {
      $products_ordered .= "※ 当社キャラクター名は、お取引10分前までに電子メールにてお知らせいたします。\n\n";
    } else {
      $products_ordered .= "※ 当社キャラクター名は、お支払い確認後に電子メールにてお知らせいたします。\n\n";
    }
  }
}

orders_updated($insert_id);

# メール本文整形 --------------------------------------
$email_order = '';
//ccdd
$otq = tep_db_query("select * from ".TABLE_ORDERS_TOTAL." where class = 'ot_total' and orders_id = '".$insert_id."'");
$ot = tep_db_fetch_array($otq);
  
$email_order .= tep_get_fullname($order->customer['firstname'],$order->customer['lastname']) . '様' . "\n\n";
$email_order .= 'この度は、' . STORE_NAME . 'をご利用いただき、誠にあり' . "\n";
$email_order .= 'がとうございます。' . "\n";
$email_order .= '下記の内容にてご注文を承りましたので、ご確認ください。' . "\n";
$email_order .= 'ご不明な点がございましたら、注文番号をご確認の上、' . "\n";
$email_order .= '「' . STORE_NAME . '」までお問い合わせください。' . "\n\n";
$email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
//$email_order .= '▼お支払金額　　　：' . strip_tags($ot['text']) . "\n\n";
$email_order .= '▼注文番号　　　　：' . $insert_id . "\n";
$email_order .= '▼注文日　　　　　：' . tep_date_long(time()) . "\n";
$email_order .= '▼お名前　　　　　：' . tep_get_fullname($order->customer['firstname'],$order->customer['lastname']) . "\n";
$email_order .= '▼メールアドレス　：' . $order->customer['email_address'] . "\n";
$email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
if ($point > 0) {
  $email_order .= '▼ポイント割引　　：' . $point . '円' . "\n";
}
$mail_fee = 0; 
if (isset($_POST['codt_fee']) && intval($_POST['codt_fee'])) {
  $mail_fee =  intval($_POST['codt_fee']);
} else if (isset($_POST['money_order_fee']) && intval($_POST['money_order_fee'])) {
  $mail_fee = intval($_POST['money_order_fee']);
} else if (isset($_POST['postal_money_order_fee']) && intval($_POST['postal_money_order_fee'])) {
  $mail_fee = intval($_POST['postal_money_order_fee']);
} else if (isset($_POST['telecom_order_fee']) && intval($_POST['telecom_order_fee'])) {
  $mail_fee = intval($_POST['telecom_order_fee']);
}
  
$buy_mail_fee = 0;
if ($bflag_single == 'View') {
  if (!empty($buy_handle_fee)) {
    $buy_mail_fee = $buy_handle_fee; 
  }
}
$total_mail_fee = $mail_fee + $buy_mail_fee;
  
if (!empty($total_mail_fee)) {
  $email_order .=  '▼手数料　　　　　：'.$total_mail_fee.'円'."\n";
}
$email_order .= '▼お支払金額　　　：' . $currencies->format(abs($ot['value'])) . "\n";
if (is_object($$payment)) {
  $payment_class = $$payment;
  $email_order .= '▼お支払方法　　　：' . $payment_class->title . "\n";
}
if ($payment == 'moneyorder') {
  $email_order .= C_BANK."\n"; 
} else if ($payment == 'postalmoneyorder') {
  $email_order .= C_POSTAL."\n"; 
} else if ($payment == 'telecom') {
  $email_order .= C_CC."\n"; 
}
  
if ($payment_class->email_footer) { 
  $email_order .= $payment_class->email_footer . "\n";
}
  
if(tep_not_null($bbbank)) {
  $email_order .= '▼お支払先金融機関' . "\n";
  $email_order .= $bbbank . "\n";
  $email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n\n";
  $email_order .= '・当社にて商品の受領確認がとれましたら代金お支払い手続きに入ります。' . "\n";
  $email_order .= '・本メール送信後7日以内に取引が完了できない場合、' . "\n";
  $email_order .= '　当社は、お客様がご注文を取り消されたものとして取り扱います。' . "\n\n";
}
  
$email_order .= "\n\n";
$email_order .= '▼注文商品' . "\n";
$email_order .= '------------------------------------------' . "\n";
$email_order .= $products_ordered . "\n";

$email_order .= '▼取引日時　　　　：' . str_string($date) . $hour . '時' . $min . '分　（24時間表記）' . "\n";
$email_order .= '　　　　　　　　　：' . $torihikihouhou . "\n";
  
$email_order .= '▼備考　　　　　　：' . "\n";
if (trim($order->info['comments'])) {
  $email_order .= $order->info['comments'] . "\n";
}

  
/*
  if ($payment == 'convenience_store') { 
  $email_order .= '■コンビニ決済情報' . "\n";
  $email_order .= '郵便番号:' . $_POST['convenience_store_zip_code'] ."\n";
  $email_order .= '住所    :' . $_POST['convenience_store_address1'] . " " . $_POST['convenience_store_address2'] ."\n";
  $email_order .= 'お名前  :' . $_POST['convenience_store_l_name'] . " " . $_POST['convenience_store_f_name'] ."\n";
  $email_order .= '電話番号:' . $_POST['convenience_store_tel'] . "\n\n";
  }
*/
  
$email_order .= "\n\n\n";
$email_order .= '[ご連絡・お問い合わせ先]━━━━━━━━━━━━' . "\n";
$email_order .= '株式会社 iimy' . "\n";
$email_order .= SUPPORT_EMAIL_ADDRESS . "\n";
$email_order .= HTTP_SERVER . "\n";
$email_order .= '━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  
# メール本文整形 --------------------------------------
  
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
$email_printing_order .= '取引日時　　　　：' . str_string($date) . $hour . '時' . $min . '分　（24時間表記）' . "\n";
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
if ($point > 0) {
  $email_printing_order .= '□ポイント割引　　：' . (int)$point . '円' . "\n";
}
if (!empty($total_mail_fee)) {
  $email_printing_order .= '手数料　　　　　：'.$total_mail_fee.'円'."\n"; 
}
$email_printing_order .= 'お支払金額　　　：' .  $currencies->format(abs($ot['value'])) . "\n";
if (is_object($$payment)) {
  $payment_class = $$payment;
  $email_printing_order .= 'お支払方法　　　：' . $payment_class->title . "\n";
}
  
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

/*
  if ($payment == 'convenience_store') {
  $email_printing_order .= '■コンビニ決済情報' . "\n";
  $email_printing_order .= '郵便番号:' . $_POST['convenience_store_zip_code'] ."\n";
  $email_printing_order .= '住所    :' . $_POST['convenience_store_address1'] . " " . $_POST['convenience_store_address2'] ."\n";
  $email_printing_order .= 'お名前  :' . $_POST['convenience_store_l_name'] . " " . $_POST['convenience_store_f_name'] ."\n";
  $email_printing_order .= '電話番号:' . $_POST['convenience_store_tel'] . "\n";
  }
*/

$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= 'IPアドレス　　　　　　：' . $_SERVER["REMOTE_ADDR"] . "\n";
$email_printing_order .= 'ホスト名　　　　　　　：' . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
$email_printing_order .= 'ユーザーエージェント　：' . $_SERVER["HTTP_USER_AGENT"] . "\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= '信用調査' . "\n";
//ccdd
$credit_inquiry_query = tep_db_query("select customers_fax, customers_guest_chk from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
$credit_inquiry       = tep_db_fetch_array($credit_inquiry_query);
  
$email_printing_order .= $credit_inquiry['customers_fax'] . "\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= '注文履歴　　　　　　　：';
  
if ($credit_inquiry['customers_guest_chk'] == '1') { $email_printing_order .= 'ゲスト'; } else { $email_printing_order .= '会員'; }
  
$email_printing_order .= "\n";
  
$order_history_query_raw = "select o.orders_id, o.customers_name, o.customers_id,
  o.date_purchased, s.orders_status_name, ot.value as order_total_value from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . tep_db_input($customer_id) . "' and o.orders_status = s.orders_status_id and s.language_id = '" . $languages_id . "' and ot.class = 'ot_total' order by o.date_purchased DESC limit 0,5";  
//ccdd
$order_history_query = tep_db_query($order_history_query_raw);
while ($order_history = tep_db_fetch_array($order_history_query)) {
  $email_printing_order .= $order_history['date_purchased'] . '　　' .
    tep_output_string_protected($order_history['customers_name']) . '　　' .
    abs(intval($order_history['order_total_value'])) . '円　　' . $order_history['orders_status_name'] . "\n";
}
  
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n\n\n";
  
  

if ($payment_class->title === '銀行振込(買い取り)') {
  $email_printing_order .= '★★★★★★★★★★★★この注文は【買取】です。★★★★★★★★★★★★' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '備考の有無　　　　　：□ 無　　｜　　□ 有　→　□ 返答済' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= 'キャラクターの有無　：□ 有　　｜　　□ 無　→　新規作成してお客様へ連絡' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '受領　※注意※　　●：＿＿月＿＿日' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '残量入力→誤差有無　：□ 無　　｜　　□ 有　→　□ 報告' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '受領メール送信　　　：□ 済' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '支払　　　　　　　　：＿＿月＿＿日　※総額5,000円未満は168円引く※' . "\n";
  $email_printing_order .= '　　　　　　　　　　　□ JNB　　□ eBank　　□ ゆうちょ' . "\n";
  $email_printing_order .= '　　　　　　　　　　　入金予定日＿＿月＿＿日　受付番号＿＿＿＿＿＿＿＿＿' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '支払完了メール送信　：□ 済　　　※追加文章がないか確認しましたか？※' . "\n";
} elseif ($payment_class->title === 'クレジットカード決済') {
  $email_printing_order .= 'この注文は【販売】です。' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '備考の有無　　　　　：□ 無　　｜　　□ 有　→　□ 返答済' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '決済確認　　　　　●：＿＿月＿＿日' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '在庫確認　　　　　　：□ 有　　｜　　□ 無　→　仕入困難ならお客様へ電話' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '信用調査　　　　　　：□ 2回目以降　→　□ 常連（以下のチェック必要無）' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　　　　□ 1. 過去に本人確認をしている' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　　　　□ 2. 決済内容に変更がない' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　　　　□ 3. 短期間に高額決済がない' . "\n";
  $email_printing_order .= '　　　　　　　　　　----------------------------------------------------' . "\n";
  $email_printing_order .= '　　　　　　　　　　　□ 初回　→　□ IP・ホストのチェック' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 電話確認をする' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 カード名義（カタカナ）＿＿＿＿＿＿' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 電話番号＿＿＿＿＿＿＿＿＿＿＿＿＿' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 ＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 ＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 ＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 □ カード名義・商品名・キャラ名一致' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 本人確認日：＿＿月＿＿日' . "\n";
  $email_printing_order .= '　　　　　　　　　　　　　　　　　 □ 信用調査入力' . "\n";
  $email_printing_order .= '　　　　　　　　　　----------------------------------------------------' . "\n";
  $email_printing_order .= '※ 疑わしい点があれば担当者へ報告をする　→　担当者＿＿＿＿の承諾を得た' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '発送　　　　　　　　：＿＿月＿＿日' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '残量入力→誤差有無　：□ 無　　｜　　□ 有　→　報告　□' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '発送完了メール送信　：□ 済' . "\n";
} else {
  $email_printing_order .= 'この注文は【販売】です。' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '備考の有無　　　　　：□ 無　　｜　　□ 有　→　□ 返答済' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '在庫確認　　　　　　：□ 有　　｜　　□ 無　→　入金確認後仕入' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '入金確認　　　　　●：＿＿月＿＿日　→　金額は' .
    abs($ot['value']) . '円ですか？　□ はい' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '入金確認メール送信　：□ 済' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '発送　　　　　　　　：＿＿月＿＿日' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '残量入力→誤差有無　：□ 無　　｜　　□ 有　→　報告　□' . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '発送完了メール送信　：□ 済' . "\n";    
}
  
$email_printing_order .= '------------------------------------------------------------------------' . "\n";
$email_printing_order .= '最終確認　　　　　　：確認者名＿＿＿＿' . "\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
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
$payment_modules->after_process();

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
  
tep_session_unregister('bank_name');
tep_session_unregister('bank_shiten');
tep_session_unregister('bank_kamoku');
tep_session_unregister('bank_kouza_num');
tep_session_unregister('bank_kouza_name');
  
#convenience_store
unset($_SESSION['character']);
unset($_SESSION['option']);
unset($_SESSION['referer_adurl']);

  
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

