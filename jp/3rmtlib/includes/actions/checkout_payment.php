<?php
require_once DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PAYMENT;
// if the customer is not logged on, redirect them to the login page
if (!tep_session_is_registered('customer_id')) {
  $navigation->set_snapshot();
  tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
}

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($cart->count_contents() < 1) {
  tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
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
// if no billing destination address was selected, use the customers own address as default
/*
 * 计算配送费用
 */
$country_fee_array = array();
  $country_fee_id_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
  while($country_fee_id_array = tep_db_fetch_array($country_fee_id_query)){

    $country_fee_array[$country_fee_id_array['fixed_option']] = $country_fee_id_array['name_flag'];
  }
  tep_db_free_result($country_fee_id_query);
$weight = $cart->weight;

foreach($_SESSION['options'] as $op_key=>$op_value){
  if($op_key == $country_fee_array[3]){
    $city_query = tep_db_query("select * from ". TABLE_COUNTRY_CITY ." where name='". $op_value[1] ."' and status='0'");
    $city_num = tep_db_num_rows($city_query);
  }
  
  if($op_key == $country_fee_array[2]){
    $address_query = tep_db_query("select * from ". TABLE_COUNTRY_AREA ." where name='". $op_value[1] ."' and status='0'");
    $address_num = tep_db_num_rows($address_query);
  }
  
  if($op_key == $country_fee_array[1]){ 
    $country_query = tep_db_query("select * from ". TABLE_COUNTRY_FEE ." where name='". $op_value[1] ."' and status='0'");
    $address_country_num = tep_db_num_rows($country_query);
  }

if($city_num > 0 && $op_key == $country_fee_array[3]){
  $city_array = tep_db_fetch_array($city_query);
  tep_db_free_result($city_query);
  $city_free_value = $city_array['free_value'];
  $city_weight_fee_array = unserialize($city_array['weight_fee']);

  //根据重量来获取相应的配送费用
  foreach($city_weight_fee_array as $key=>$value){
    
    if(strpos($key,'-') > 0){

      $temp_array = explode('-',$key);
      $city_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
    }else{
  
      $city_weight_fee = $weight <= $key ? $value : 0;
    }

    if($city_weight_fee > 0){

      break;
    }
  }
}elseif($address_num > 0 && $op_key == $country_fee_array[2]){
  $address_array = tep_db_fetch_array($address_query);
  tep_db_free_result($address_query);
  $address_free_value = $address_array['free_value'];
  $address_weight_fee_array = unserialize($address_array['weight_fee']);

  //根据重量来获取相应的配送费用
  foreach($address_weight_fee_array as $key=>$value){
    
    if(strpos($key,'-') > 0){

      $temp_array = explode('-',$key);
      $address_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
    }else{
  
      $address_weight_fee = $weight <= $key ? $value : 0;
    }

    if($address_weight_fee > 0){

      break;
    }
  }
}elseif($address_country_num && $op_key == $country_fee_array[1]){
  if($address_country_num > 0){
  $country_array = tep_db_fetch_array($country_query);
  tep_db_free_result($country_query);
  $country_free_value = $country_array['free_value'];
  $country_weight_fee_array = unserialize($country_array['weight_fee']);

  //根据重量来获取相应的配送费用
  foreach($country_weight_fee_array as $key=>$value){
    
    if(strpos($key,'-') > 0){

      $temp_array = explode('-',$key);
      $country_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
    }else{
  
      $country_weight_fee = $weight <= $key ? $value : 0;
    }

    if($country_weight_fee > 0){

      break;
    }
  }
  }
}

}
if($city_weight_fee != ''){

  $weight_fee = $city_weight_fee;
}else{
  $weight_fee = $address_weight_fee != '' ? $address_weight_fee : $country_weight_fee;
}
if($city_free_value != ''){

  $free_value = $city_free_value;
}else{
  $free_value = $address_free_value != '' ? $address_free_value : $country_free_value;
}

$shipping_fee = $cart->total > $free_value ? 0 : $weight_fee;

if (!tep_session_is_registered('billto')) {
  tep_session_register('billto');
  $billto = $customer_default_address_id;
} else {
  // verify the selected billing address
  //ccdd
  $check_address_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customer_id . "' and address_book_id = '" . (int)$billto . "'");
  $check_address = tep_db_fetch_array($check_address_query);

  if ($check_address['total'] != '1') {
    $billto = $customer_default_address_id;
    if (tep_session_is_registered('payment')) tep_session_unregister('payment');
  }
}

require_once DIR_WS_CLASSES . 'order.php';
$order = new order;

if (!tep_session_is_registered('comments')) tep_session_register('comments');

$total_weight = $cart->show_weight();
$total_count = $cart->count_contents();

$breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
// load all enabled payment modules



if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  //ccdd
  $point_query = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
  $point = tep_db_fetch_array($point_query);
}
require_once DIR_WS_CLASSES . 'payment.php';



//准备变量

$payment_modules =  payment::getInstance(SITE_ID);
$selection = $payment_modules->selection();



$order->info['total'] = $order->info['total'] + $shipping_fee;
//统一的头输出 

page_head();?>
<script type="text/javascript" src="./js/jquery-1.3.2.min.js">
  </script>
  <script type="text/javascript" src="./js/payment.js">
  </script>
  <?php
  //输出payment 的javascript验证
  if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') 
    {
      echo $payment_modules->javascript_validation($point['point']); 
    }
?>
</head>
