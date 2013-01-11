<?php
// if the customer is not logged on, redirect them to the login page
if (!tep_session_is_registered('customer_id')) {
  $navigation->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_CHECKOUT_OPTION));
  tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
}else{

  $url_array = explode('/',$_SERVER['HTTP_REFERER']);
  $url_str = end($url_array);
  if(isset($_GET['is_finish'])){
    if(!isset($_SESSION['payment']) && $url_str != 'checkout_payment.php' && $url_str != 'login.php'){
      if(!isset($_SESSION['shipping_session_flag'])){
        $_SESSION['shipping_session_flag'] = true;
      }
      if (!empty($_SESSION['shipping_page_str'])) {
        tep_redirect(tep_href_link($_SESSION['shipping_page_str'], '', 'SSL'));
      } else {
        unset($_SESSION['shipping_session_flag']);
        tep_redirect(tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL'));
      }
    }
  }
}

// if there is nothing in the customers cart, redirect them to the shopping cart page
if ($cart->count_contents(true) < 1) {
  tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
}

// avoid hack attempts during the checkout procedure by checking the internal cartID
if (isset($cart->cartID) && tep_session_is_registered('cartID')) {
  if ($cart->cartID != $cartID) {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL'));
  }
}
$sendto = false;
// if no shipping method has been selected, redirect the customer to the shipping method selection page
//  if (!tep_session_is_registered('shipping')) {
//    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
//  }
require(DIR_WS_CLASSES . 'payment.php');
$payment_modules = payment::getInstance(SITE_ID);

require(DIR_WS_CLASSES . 'order.php');
$order = new order;

if (!isset($_GET['is_finish'])) {
$payment = $_POST['payment']; tep_session_register('payment');
//if (!tep_session_is_registered('payment'))
if (!tep_session_is_registered('comments')) tep_session_register('comments');
if (isset($_POST['comments_added']) && $_POST['comments_added'] != '') {
  $comments = tep_db_prepare_input($_POST['comments']);

}
$_SESSION['mailcomments'] = $_POST['comments'];
// check if bank info

// load the selected payment module

//判断支付方法是否存在， 支付方法是否被允许 
if (!$payment_modules->moduleIsEnabled($payment)){
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
}


if (is_array($payment_modules->modules) ){
  $validateModule = $payment_modules->pre_confirmation_check($payment);
  if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
      $point_query = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
      $current_point = tep_db_fetch_array($point_query);
  }
  if ($validateModule['validated']===false or $validateModule == false){
    $order->info['total'] = $order->info['total'] + $h_shipping_fee; 
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
      echo $payment_modules->javascript_validation($current_point['point']); 
    }
?>
</head><?php
    //}}
    
    require_once "checkout_payment_template.php";    
    exit();
  }

}
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  unset($_SESSION['h_code_fee']); 
  if (isset($_POST['code_fee'])) {
    $h_code_fee = $_POST['code_fee']; 
    tep_session_register('h_code_fee');
  }
  $payment_modules->deal_other_info($payment, $_POST); 
  header('Location:'.tep_href_link(FILENAME_CHECKOUT_CONFIRMATION, 'is_finish=1', 'SSL'));
}

$payment_selection = $payment_modules->selection();
$allow_payment_list = array();

$pay_total = $order->info['total'] + $h_shipping_fee;

if ((MODULE_ORDER_TOTAL_POINT_STATUS == 'true') && (intval($h_point) > 0)) {
   $pay_total -= intval($h_point);
}
if (isset($_SESSION['campaign_fee'])) {
   $pay_total += $_SESSION['campaign_fee'];
}

foreach ($payment_selection as $pay_key => $pay_single) {
  if ($payment_modules->moneyInRange($pay_single['id'], $pay_total)) {
    continue; 
  }
  if (!$payment_modules->showToUser($pay_single['id'], $_SESSION['guestchk'])) {
    continue; 
  }
  $allow_payment_list[] = $pay_single['id'];
}
if (!in_array($payment, $allow_payment_list)) {
  tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
}

require(DIR_WS_CLASSES . 'order_total.php');
$order_total_modules = new order_total;

// Stock Check
$any_out_of_stock = false;
if (STOCK_CHECK == 'true') {
  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    if (tep_check_stock((int)$order->products[$i]['id'], $order->products[$i]['qty'])) {
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
      $point_query = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
      $current_point = tep_db_fetch_array($point_query);
      echo $payment_modules->javascript_validation($current_point['point']); 
    }
?>
<script type="text/javascript">
<!--
var a_vars = Array();
var pagename='';
var visitesSite = 1;
var visitesURL = "<?php echo ($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER; ?>/visites.php";
<?php
  require(DIR_WS_ACTIONS.'visites.js');
?>
//-->
</script>
<script type="text/javascript" src="./js/confirm_session_error.js"></script>
</head>
