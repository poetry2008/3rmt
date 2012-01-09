<?php
// if the customer is not logged on, redirect them to the login page
if (!tep_session_is_registered('customer_id')) {
  $navigation->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_CHECKOUT_PAYMENT));
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
$sendto = false;
// if no shipping method has been selected, redirect the customer to the shipping method selection page
//  if (!tep_session_is_registered('shipping')) {
//    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
//  }

$payment = $_POST['payment']; tep_session_register('payment');
//if (!tep_session_is_registered('payment'))
if (!tep_session_is_registered('comments')) tep_session_register('comments');
if (isset($_POST['comments_added']) && $_POST['comments_added'] != '') {
  $comments = tep_db_prepare_input($_POST['comments']);

}
$_SESSION['mailcomments'] = $_POST['comments'];
// check if bank info

// load the selected payment module
require(DIR_WS_CLASSES . 'payment.php');
$payment_modules = payment::getInstance(SITE_ID);

require(DIR_WS_CLASSES . 'order.php');
$order = new order;

//判断支付方法是否存在， 支付方法是否被允许 
if (!$payment_modules->moduleIsEnabled($payment)){
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
}

if (is_array($payment_modules->modules) ){
  $validateModule = $payment_modules->pre_confirmation_check($payment);
  if ($validateModule['validated']===false or $validateModule == false){
    $selection = $payment_modules->selection();
    if($validateModule !=false){
    $selection[strtoupper($payment)] = $validateModule;
    }else {

    }
    require_once DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PAYMENT;
    //{{
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
</head><?php
    //}}
    
    require_once "checkout_payment_template.php";    
    exit();
  }

}

require(DIR_WS_CLASSES . 'order_total.php');
$order_total_modules = new order_total;

// Stock Check
$any_out_of_stock = false;
if (STOCK_CHECK == 'true') {
  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    if (tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty'])) {
      $any_out_of_stock = true;
    }
  }
  // Out of Stock
  if ( (STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true) ) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  }
}

$breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
$breadcrumb->add(NAVBAR_TITLE_2);

if (isset($payment_modules->modules[strtoupper($payment)]->form_action_url) && $payment_modules->modules[strtoupper($payment)]->form_action_url) {
  $form_action_url = $payment_modules->modules[strtoupper($payment)]->form_action_url;
}else{
  $form_action_url = tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
}



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