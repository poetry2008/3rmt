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
if ($cart->count_contents() < 1) {
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
tep_session_unregister('hc_point'); 
tep_session_unregister('hc_camp_point'); 
// check if bank info

// load the selected payment module

//判断支付方法是否存在， 支付方法是否被允许 
if (!$payment_modules->moduleIsEnabled($payment)){
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
}

$campaign_error = false;
$campaign_error_str = '';

if (is_array($payment_modules->modules) ){
  $validateModule = $payment_modules->pre_confirmation_check($payment);
  unset($_SESSION['campaign_fee']);
  if (!empty($_POST['camp_point'])) {
    $_POST['camp_point'] = get_strip_campaign_info($_POST['camp_point']); 
    if ($cart->show_total() > 0) {
      $campaign_query = tep_db_query("select * from ".TABLE_CAMPAIGN." where keyword = '".trim($_POST['camp_point'])."' and (site_id = '".SITE_ID."' or site_id = '0') and status = '1' and end_date >= '".date('Y-m-d', time())."' and start_date <= '".date('Y-m-d', time())."' and type='1' order by site_id desc limit 1"); 
    } else if ($cart->show_total() < 0) {
      $campaign_query = tep_db_query("select * from ".TABLE_CAMPAIGN." where keyword = '".trim($_POST['camp_point'])."' and (site_id = '".SITE_ID."' or site_id = '0') and status = '1' and end_date >= '".date('Y-m-d', time())."' and start_date <= '".date('Y-m-d', time())."' and type='2' order by site_id desc limit 1"); 
    }
    if ($campaign_query) { 
    $campaign_res = tep_db_fetch_array($campaign_query); 
    if ($campaign_res) {
      if ($cart->show_total() > 0) {
        if ($cart->show_total() <= $campaign_res['limit_value']) {
          $campaign_error = true;
        } 
      } else {
        if ($cart->show_total() >= $campaign_res['limit_value']) {
          $campaign_error = true;
        } 
      }
      $max_campaign_query = tep_db_query("select count(*) as total from ".TABLE_CUSTOMER_TO_CAMPAIGN." where customer_id = '".$customer_id."' and campaign_id = '".$campaign_res['id']."'"); 
      $max_campaign_res = tep_db_fetch_array($max_campaign_query); 
      if ((int)$max_campaign_res['total'] >= $campaign_res['max_use']) {
          $campaign_error = true;
      }
      if (!$campaign_error) {
        if (isset($_POST['point'])) {
          $_POST['point'] = 0; 
        }
        $hc_camp_point = $_POST['camp_point'];
        tep_session_register('hc_camp_point');
        $percent_pos = strpos($campaign_res['point_value'], '%'); 
        if ($percent_pos !== false) {
          $campaign_fee = $order->info['subtotal']*substr($campaign_res['point_value'], 0, -1)/100; 
          if ($campaign_fee > 0) {
            $campaign_fee = 0 - $campaign_fee; 
          }
        } else {
          $campaign_fee = $campaign_res['point_value']; 
        }
        @eval("\$campaign_fee = (int)$campaign_fee;");
        tep_session_register('campaign_fee'); 
        $camp_id = $campaign_res['id'];
        tep_session_register('camp_id'); 
      }
    } else {
      $campaign_error = true;
    }
    }
  } else {
  if (!empty($_POST['point'])) {
    $_POST['point'] = get_strip_campaign_info($_POST['point']); 
    if (preg_match('/^[0-9a-zA-Z]+$/', $_POST['point'])) {
      if (!preg_match('/^[0-9]+$/', $_POST['point'])) {
        $campaign_query = tep_db_query("select * from ".TABLE_CAMPAIGN." where keyword = '".trim($_POST['point'])."' and (site_id = '".SITE_ID."' or site_id = '0') and status = '1' and end_date >= '".date('Y-m-d', time())."' and start_date <= '".date('Y-m-d', time())."' order by site_id desc limit 1"); 
        
        $campaign_res = tep_db_fetch_array($campaign_query); 
        if ($campaign_res) {
          if ($campaign_res['type'] != '1') {
            $campaign_error = true;
          } else {
            if ($cart->show_total() <= $campaign_res['limit_value']) {
              $campaign_error = true;
            }
          }
           
          $max_campaign_query = tep_db_query("select count(*) as total from ".TABLE_CUSTOMER_TO_CAMPAIGN." where customer_id = '".$customer_id."' and campaign_id = '".$campaign_res['id']."'"); 
          $max_campaign_res = tep_db_fetch_array($max_campaign_query); 
          if ((int)$max_campaign_res['total'] >= $campaign_res['max_use']) {
              $campaign_error = true;
          } 
          
          if (!$campaign_error) {
            $hc_point = $_POST['point']; 
            tep_session_register('hc_point'); 
            $_POST['point'] = 0; 
            $percent_pos = strpos($campaign_res['point_value'], '%'); 
            if ($percent_pos !== false) {
              $campaign_fee = $order->info['subtotal']*substr($campaign_res['point_value'], 0, -1)/100; 
              if ($campaign_fee > 0) {
                $campaign_fee = 0 - $campaign_fee; 
              }
            } else {
              $campaign_fee = $campaign_res['point_value']; 
            }
            @eval("\$campaign_fee = (int)$campaign_fee;");
            tep_session_register('campaign_fee'); 
            $camp_id = $campaign_res['id'];
            tep_session_register('camp_id'); 
          }
        } else {
          $campaign_error = true;
        }
      } else {
        if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
          $cus_point_query = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
          $cus_point = tep_db_fetch_array($cus_point_query);
          if ($cus_point['point'] < $_POST['point']) {
            $campaign_error = true;
          }
        }
      }
    }
  }
  }
 
  if ($campaign_error) {
    $campaign_error_str = isset($_POST['point'])?$_POST['point']:(isset($_POST['camp_point'])?$_POST['camp_point']:0);
  }
  if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
      $point_query = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
      $current_point = tep_db_fetch_array($point_query);
  }
  if ($validateModule['validated']===false or $validateModule == false or $campaign_error == true){
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
  unset($_SESSION['h_point']); 
  unset($_SESSION['h_code_fee']); 
  if (isset($_POST['point'])) {
    $h_point = $_POST['point']; 
    tep_session_register('h_point');
  }
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
