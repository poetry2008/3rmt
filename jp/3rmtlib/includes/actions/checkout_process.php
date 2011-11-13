<?php
/*
  $Id$
*/
error_reporting(E_ALL^E_WARNING^E_DEPRECATED);
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

$comments = $payment_modules->dealComment($comments);
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
  
if (isset($_SESSION['referer_adurl']) && $_SESSION['referer_adurl']) {
  $sql_data_array['orders_adurl'] = $_SESSION['referer_adurl'];
}
$telecom_option_ok = $payment_modules->dealUnknow($sql_data_array);
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
  $telecom_option_ok = $payment_modules->getexpress($order_totals,$i);
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

$order_type_str = tep_check_order_type($insert_id);
tep_db_query("update `".TABLE_ORDERS."` set `orders_type` = '".$order_type_str."' where orders_id = '".$insert_id."'");

orders_updated($insert_id);

$otq = tep_db_query("select * from ".TABLE_ORDERS_TOTAL." where class = 'ot_total' and orders_id = '".$insert_id."'");
$ot = tep_db_fetch_array($otq);
# メール本文整形 --------------------------------------{

//mailoption {
$mailoption['ORDER_ID']         = $insert_id;
$mailoption['ORDER_DATE']       = tep_date_long(time())  ;
$mailoption['USER_NAME']        = tep_get_fullname($order->customer['firstname'],$order->customer['lastname'])  ;
$mailoption['USER_MAILACCOUNT'] = $order->customer['email_address'];
$mailoption['ORDER_TOTAL']      = $currencies->format(abs($ot['value']));
@$payment_class = $$payment;
$mailoption['TORIHIKIHOUHOU']   = $torihikihouhou;
$mailoption['ORDER_PAYMENT']    = $payment_class->title ;
$mailoption['ORDER_TTIME']      =  str_string($date) . $hour . '時' . $min . '分　（24時間表記）' ;
$mailoption['ORDER_COMMENT']    = trim($order->info['comments']);
$mailoption['ORDER_PRODUCTS']   = $products_ordered ;
$mailoption['ORDER_TMETHOD']    = $insert_torihiki_date;
$mailoption['SITE_NAME']        = STORE_NAME ;
$mailoption['SITE_MAIL']        = SUPPORT_EMAIL_ADDRESS ;
$mailoption['SITE_URL']         = HTTP_SERVER ;
$mailoption['BANK_NAME']        = $bank_name;
$mailoption['BANK_SHITEN']        = $bank_shiten;
$mailoption['BANK_KAMOKU']        = $bank_kamoku;
$mailoption['BANK_KOUZA_NUM']        = $bank_kouza_num;
$mailoption['BANK_KOUZA_NAME']        = $bank_kouza_name;

if ($point){
  $mailoption['POINT']            = str_replace('円', '', $currencies->format(abs($point)));
}else {
  $mailoption['POINT']            = 0;
}
if(!isset($total_mail_fee)){
  $total_mail_fee =0;
}
$mailoption['MAILFEE']          = str_replace('円','',$currencies->format(abs($total_mail_fee)));
$email_order = '';
$email_order = $payment_modules->getOrderMailString($mailoption);  
  
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
  $email_printing_order .= 'ポイント割引　　：' . (int)$point . '円' . "\n";
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

$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= 'IPアドレス　　　　　　：' . $_SERVER["REMOTE_ADDR"] . "\n";
$email_printing_order .= 'ホスト名　　　　　　　：' . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
$email_printing_order .= 'ユーザーエージェント　：' . $_SERVER["HTTP_USER_AGENT"] . "\n";
$email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
$email_printing_order .= '信用調査' . "\n";
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

if (method_exists($payment_class,'getMailString')){
  $email_printing_order .=$payment_class->getMailString();
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

