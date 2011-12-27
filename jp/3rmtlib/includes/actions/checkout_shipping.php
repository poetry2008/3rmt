<?php
// if the customer is not logged on, redirect them to the login page
if (!tep_session_is_registered('customer_id')) {
  $navigation->set_snapshot();
  tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
}

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($cart->count_contents() < 1) {
  tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
}

// Stock Check
$cart_products = $cart->get_products();
if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
  if(empty($cart_products)){
    $products = $cart->get_products();
  }else{
    $products = $cart_products;
  }
  for ($i=0, $n=sizeof($products); $i<$n; $i++) {
    if (tep_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
      break;
    }
  }
}

// if no shipping destination address was selected, use the customers own address as default
if (!tep_session_is_registered('sendto')) {
  tep_session_register('sendto');
  $sendto = $customer_default_address_id;
} else {
  // verify the selected shipping address
  //ccdd
  $check_address_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer_id . "' and address_book_id = '" . $sendto . "'");
  $check_address = tep_db_fetch_array($check_address_query);

  if ($check_address['total'] != '1') {
    $sendto = $customer_default_address_id;
    if (tep_session_is_registered('shipping')) tep_session_unregister('shipping');
  }
}

require(DIR_WS_CLASSES . 'order.php');
$order = new order;

// register a random ID in the session to check throughout the checkout procedure
// against alterations in the shopping cart contents
if (!tep_session_is_registered('cartID')) tep_session_register('cartID');
$cartID = $cart->cartID;

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
if ($order->content_type == 'virtual') {
  if (!tep_session_is_registered('shipping')) tep_session_register('shipping');
  $shipping = false;
  $sendto = false;
  tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

$total_weight = $cart->show_weight();
$total_count = $cart->count_contents();

// load all enabled shipping modules
require(DIR_WS_CLASSES . 'shipping.php');
// 生成 shipping 移动到 产品 列表
$shipping_modules = shipping::getInstance(SITE_ID);
/*
   foreach($shipping_modules->modules as $s_modules){
//这里书每一个配送方法的信息
echo $s_modules->title;
}
 */

if ( defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true') ) {
  switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {
    case 'national':
      if ($order->delivery['country_id'] == STORE_COUNTRY) $pass = true; break;
    case 'international':
      if ($order->delivery['country_id'] != STORE_COUNTRY) $pass = true; break;
    case 'both':
      $pass = true; break;
    default:
      $pass = false; break;
  }

  $free_shipping = false;
  if ( ($pass == true) && ($order->info['subtotal'] >= MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER) ) {
    $free_shipping = true;

    include(DIR_WS_LANGUAGES . $language . '/modules/order_total/ot_shipping.php');
  }
} else {
  $free_shipping = false;
}

require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_SHIPPING);

// process the selected shipping method
if ( isset($_POST['action']) && ($_POST['action'] == 'process') ) {
  $shipping_date_arr = array();
  //这里是表单提交过来的信息的处理位置
  $error = false;

  if (!tep_session_is_registered('comments')) tep_session_register('comments');

  if (!tep_session_is_registered('shipping')) tep_session_register('shipping');
  unset($_SESSION['shipping_method_info_arr']);
  $shipping_method_info_arr = array();
  //这里 处理数据 判断是否有错误
  if(isset($_POST['each_product_shipping'])&&$_POST['each_product_shipping']==true){
    //多个配送
    //每一个商品的配送
    $shipping_method_info = array();
    //这里是判断 输入值是否错误
    foreach($cart as $l_key => $l_val){
      if($l_key == 'contents'){
        foreach($l_val as $l_key2 => $l_val2){
          $list_cp_result = tep_get_product_by_id($l_key2, SITE_ID, $languages_id);
          $list_save_pid = $list_cp_result['products_id'];
    if(!isset($_POST['address_radio_'.$list_save_pid])||
        $_POST['address_radio_'.$list_save_pid]!='on'){
      $error = true;
      $list_address_radio_error = 'address_radio_error_'.$list_save_pid;
      $$list_address_radio_error = true;
    }
    if(!isset($_POST['shipping_address_'.$list_save_pid])||
        $_POST['shipping_address_'.$list_save_pid]==''){
      $error = true;
      $list_shipping_address_error = 'shipping_address_error_'.$list_save_pid;
      $$list_shipping_address_error = true;
    }
    if(!isset($_POST['shipping_method_'.$list_save_pid])||
        $_POST['shipping_method_'.$list_save_pid]==''){
      $error = true;
      $list_shipping_method_error = 'shipping_method_error_'.$list_save_pid;
      $$list_shipping_method_error = true;
    }
    if(!isset($_POST['date_'.$list_save_pid])||
        $_POST['date_'.$list_save_pid]==''){
      $error = true;
      $list_date_error = 'date_error_'.$list_save_pid;
      $$list_date_error = true;
    }
    if(!isset($_POST['torihiki_time_radio_'.$list_save_pid])||
        $_POST['torihiki_time_radio_'.$list_save_pid]==''){
      $error = true;
      $list_torihiki_time_radio_error = 'torihiki_time_radio_error_'.$list_save_pid;
      $$list_torihiki_time_radio_error = true;
    }
    $shipping_method_info['shipping_cost'] =
      tep_db_prepare_input($_POST['shipping_cost_'.$list_save_pid]);
    $shipping_method_info['shipping_address'] =
      tep_db_prepare_input($_POST['shipping_address_'.$list_save_pid]);
    $shipping_method_info['shipping_method'] =
      tep_db_prepare_input($_POST['shipping_method_'.$list_save_pid]);
    $shipping_method_info['torihiki_time'] =
      tep_db_prepare_input($_POST['torihiki_time_radio_'.$list_save_pid]);
    $torihiki_time_arr = explode('-',$shipping_method_info['torihiki_time']);
    $shipping_method_info['insert_torihiki_date'] = 
             tep_db_prepare_input($_POST['date_'.$list_save_pid])." ".$torihiki_time_arr[0];
    $shipping_method_info['insert_torihiki_date_end'] =
             tep_db_prepare_input($_POST['date_'.$list_save_pid])." ".$torihiki_time_arr[1];
    $shipping_date_arr[] = 
      tep_db_prepare_input($_POST['date_'.$list_save_pid])." ".$torihiki_time_arr[0];
    $shipping_method_info['torihikihouhou'] =
      tep_db_prepare_input($_POST['torihikihouhou_'.$list_save_pid]);
    $shipping_method_info_arr[$list_save_pid] = $shipping_method_info;
    //没一个商品的结束
        }
      }
    }
  }else{
    //一种配送
    $shipping_method_info = array();
    //这里是判断 输入值是否错误
    if(!isset($_POST['address_radio'])||$_POST['address_radio']!='on'){
      $error = true;
      $address_radio_error = true;
    }
    if(!isset($_POST['shipping_address'])||$_POST['shipping_address']==''){
      $error = true;
      $shipping_address_error = true;
    }
    if(!isset($_POST['shipping_method'])||$_POST['shipping_method']==''){
      $error = true;
      $shipping_method_error = true;
    }
    if(!isset($_POST['date'])||$_POST['date']==''){
      $error = true;
      $date_error = true;
    }
    if(!isset($_POST['torihiki_time_radio'])||$_POST['torihiki_time_radio']==''){
      $error = true;
      $torihiki_time_radio_error = true;
    }
    $shipping_method_info['shipping_cost'] = tep_db_prepare_input($_POST['shipping_cost']);
    $shipping_method_info['shipping_address'] = tep_db_prepare_input($_POST['shipping_address']);
    $shipping_method_info['shipping_method'] = tep_db_prepare_input($_POST['shipping_method']);
    $shipping_method_info['torihiki_time'] = tep_db_prepare_input($_POST['torihiki_time_radio']);
    $torihiki_time_arr = explode('-',$shipping_method_info['torihiki_time']);
    $shipping_method_info['insert_torihiki_date'] = 
             tep_db_prepare_input($_POST['date'])." ".$torihiki_time_arr[0]."/".
             tep_db_prepare_input($_POST['date'])." ".$torihiki_time_arr[1];
    $shipping_date_arr[] = tep_db_prepare_input($_POST['date'])." ".$torihiki_time_arr[0];
    $shipping_method_info['torihikihouhou'] = tep_db_prepare_input($_POST['torihikihouhou']);
    $shipping_method_info_arr['all_products'] = $shipping_method_info;
  }


  $products_error = false;
  //处理商品 别名 并保存值到session
  foreach($_POST as $input_k => $value){
    if($value == ""&&preg_match('/^cname_\d+\{\d\}\d+/',$input_k)){
      $error = true;
      $products_error = true;
    }
  }
  if(!$products_error&&!$error){
    unset($_SESSION['character']);
    foreach($cart as $c_key => $c_val){
      if($c_key == 'contents'){
        foreach($c_val as $c_key_son => $c_val_son){
          $_SESSION['character'][$c_key_son] = $_POST['cname_' . $c_key_son];
        }
      }
    }
  }


  if($error == false) {
    tep_session_register('each_product_shipping');
    tep_session_register('shipping_date_arr');
    tep_session_register('shipping_method_info_arr');

    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
  }
}



$breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));

$torihiki_array = explode("\n", DS_TORIHIKI_HOUHOU);
$torihiki_list[] = array('id' => '', 'text' => TEXT_PRESE_SELECT);
for($i=0; $i<sizeof($torihiki_array); $i++) {
  $torihiki_list[] = array('id' => $torihiki_array[$i],
      'text' => $torihiki_array[$i]
      );
}

//print_r($_SESSION);
//print_r($_SESSION['cart']->contents);
$keys = array_keys($_SESSION['cart']->contents);
$product_ids = array();
foreach($keys as $akey){
  $arr = explode('{', $akey);
  if (!empty($arr[0])) {
    $product_ids[] = $arr[0];
  }
}
//print_r($_COOKIES);
//配送 是否 每个商品用一个 配送
$each_product_shipping = false;
foreach($cart_products as $t_p){
  if($t_p['shipping_flag'] == 1){
    $each_product_shipping = true;
    break;
  }
}
$each_product_shipping = true;

//获取 用户地址本
$c_address_book = tep_get_address_by_customers_id($customer_id);
