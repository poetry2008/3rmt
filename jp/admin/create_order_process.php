<?php
/*
  $Id$
*/
require('includes/application_top.php');
require('includes/step-by-step/new_application_top.php');
//此页能是POST过来 ，如果不是 则 跳转 到 CREATE_ORDER
if(isGet()){
tep_redirect(tep_redirect(tep_href_link(FILENAME_CREATE_ORDER, null, 'SSL')));
}else if(!$_POST['email_address']){
tep_redirect(tep_redirect(tep_href_link(FILENAME_CREATE_ORDER, null, 'SSL')));
}
require(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_CREATE_ORDER_PROCESS);
//debug info
//ini_set("display_errors","On");

$shipping_method     = tep_db_prepare_input($_POST['shipping_method']);
$shipping_address    = tep_db_prepare_input($_POST['shipping_address']);
$shipping_address_radio = tep_db_prepare_input($_POST['address_radio']);
$shipping_date       = tep_db_prepare_input($_POST['date']);
$shipping_work_time  = tep_db_prepare_input($_POST['work_time']);
$shipping_start_time = tep_db_prepare_input($_POST['start_time']);
$shipping_time       = tep_db_prepare_input($_POST['torihiki_time_radio']);


$payment_modules = payment::getInstance($_POST['site_id']);
$customer_id    = tep_db_prepare_input($_POST['customers_id']);
$firstname      = tep_db_prepare_input($_POST['firstname']);
$lastname       = tep_db_prepare_input($_POST['lastname']);
$email_address  = tep_db_prepare_input($_POST['email_address']);
/*
准备删除
$telephone      = isset($_POST['telephone']) ? tep_db_prepare_input($_POST['telephone']) : '';
$fax            = tep_db_prepare_input($_POST['fax']);
$street_address = isset($_POST['street_address']) ? tep_db_prepare_input($_POST['street_address']) : '';
$company        = isset($_POST['company']) ? tep_db_prepare_input($_POST['company']) : '';
$suburb         = isset($_POST['suburb']) ? tep_db_prepare_input($_POST['suburb']) : '';
$postcode       = isset($_POST['postcode']) ? tep_db_prepare_input($_POST['postcode']) : '';
$city           = isset($_POST['city']) ? tep_db_prepare_input($_POST['city']) : '';
$zone_id        = isset($_POST['zone_id']) ? tep_db_prepare_input($_POST['zone_id']) : '';
$state          = isset($_POST['state']) ? tep_db_prepare_input($_POST['state']) : '';
$country        = isset($_POST['country']) ? tep_db_prepare_input($_POST['country']) : '';
*/
$site_id        = tep_db_prepare_input($_POST['site_id']);
$format_id      = "1";
$size           = "1";
$new_value      = "1";
$error          = false; // reset error flag
$temp_amount    = "0";
$temp_amount    = number_format($temp_amount, 2, '.', '');
$payment_method = tep_db_prepare_input($_POST['payment_method']);
  
$currency_text  = DEFAULT_CURRENCY . ",1";
if(isset($_POST['Currency']) && !empty($_POST['Currency']))  {
  $currency_text = tep_db_prepare_input($_POST['Currency']);
}
//{{检查信息是否全
/*
  需要检查的信息有
  1.customer_id
  2.firstname
  3.lastname
  4.email_address
  5.site_id
  6.payment 支付方法,支付方法对应信息
  7.取引方法将不会被支持
*/


//v customer_id
if($customer_id == '') {
  $error = true;
} elseif(!is_numeric($customer_id)) {
  $error = true;
}

//v firstname
if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
  $error = true;
  $entry_firstname_error = true;
} else {
  $entry_firstname_error = false;
}
//v lastname
if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
  $error = true;
  $entry_lastname_error = true;
} else {
  $entry_lastname_error = false;
}
//v email_address
if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
  $error = true;
  $entry_email_address_error = true;
} else {
  $entry_email_address_error = false;
}
// v email_address
if (!tep_validate_email($email_address)) {
  $error = true;
  $entry_email_address_check_error = true;
} else {
  $entry_email_address_check_error = false;
}

if ($payment_method == 'payment_null') {
  $error = true;
  $entry_payment_method_error = true;
} 

//}}检查是否通过验证
//验证 shipping

if($shipping_method == 'shipping_null'){
  $error = true;
  $entry_shipping_method_error = true;
}
if($shipping_address_radio != 'show_address'){
  $error = true;
  $entry_shipping_address_radio_error = true;
}
if($shipping_address == 0){
  $error = true;
  $entry_shipping_address_error = true;
}
if($shipping_date ==''){
  $error = true;
  $entry_shipping_date_error = true;
}
if($shipping_time ==''){
  $error = true;
  $entry_shipping_time_error = true;
}

$payment_method_romaji = payment::changeRomaji($payment_method,PAYMENT_RETURN_TYPE_CODE);
$validateModule = $payment_modules->admin_confirmation_check($payment_method);

//如果不通过 或是 有其它错误
if ($validateModule['validated']===false or $error){
  $selections = $payment_modules->admin_selection();
  $selections[strtoupper($payment_method)] = $validateModule;
  require_once 'create_order.php';
  exit();
}

//开始生成订单
$currency_array = explode(",", $currency_text);
$currency = $currency_array[0];
$currency_value = $currency_array[1];
$insert_id = date("Ymd") . '-' . date("His") . tep_get_order_end_num();
$sql_data_array = array('orders_id'     => $insert_id,
			'customers_id'                => $customer_id,
			'customers_name'              => tep_get_fullname($firstname,$lastname),
			'customers_company'           => $company,
			'customers_street_address'    => $street_address,
			'customers_suburb'            => $suburb,
			'customers_city'              => $city,
			'customers_postcode'          => $postcode,
			'customers_state'             => $state,
			'customers_country'           => $country,
			'customers_telephone'         => $telephone,
			'customers_email_address'     => $email_address,
			'customers_address_format_id' => $format_id,
			'delivery_company'            => $company,
			'delivery_street_address'     => $street_address,
			'delivery_suburb'             => $suburb,
			'delivery_city'               => $city,
			'delivery_postcode'           => $postcode,
			'delivery_state'              => $state,
			'delivery_country'            => $country,
			'delivery_address_format_id'  => $format_id,
			'billing_name'                => tep_get_fullname($firstname,$lastname),
			'billing_company'             => $company,
			'billing_street_address'      => $street_address,
			'billing_suburb'              => $suburb,
			'billing_city'                => $city,
			'billing_postcode'            => $postcode,
			'billing_state'               => $state,
			'billing_country'             => $country,
			'billing_address_format_id'   => $format_id,
			'date_purchased'              => 'now()', 
			'orders_status'               => DEFAULT_ORDERS_STATUS_ID,
			'currency'                    => $currency,
			'currency_value'              => $currency_value,
			'payment_method'              => payment::changeRomaji($payment_method,
                            'title'),
                        //           	'torihiki_houhou'             => $torihikihouhou,
           	'torihiki_houhou'             => '',
			'torihiki_date'               => tep_db_input($date . ' ' . $hour . ':' . $min . ':00'),
			'site_id'                     => $site_id,
			'orders_wait_flag'            => '1'
			); 
$comment = $payment_modules->dealComment($payment_method,$comment);
//$sql_data_array['orders_comment'] = $comment;
//创建订单
tep_db_perform(TABLE_ORDERS, $sql_data_array);


last_customer_action();
orders_updated($insert_id);

$sql_data_array = array('orders_id' => $insert_id, 
              'orders_status_id' => $new_value, 
              'date_added' => 'now()', 
              'customer_notified' => '1',
              'comments' => $comment);
tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

require(DIR_FS_CATALOG . 'includes/classes/order.php');
$order = new order($insert_id);
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies;
require(DIR_WS_CLASSES . 'order_total.php');
$order_total = new order_total($site_id);
/*
$module_directory = DIR_FS_CATALOG_MODULES . 'order_total/';
$module_type = 'order_total';
$ot_tax_status = false;

if (defined('MODULE_ORDER_TOTAL_INSTALLED') && tep_not_null(MODULE_ORDER_TOTAL_INSTALLED)) {
  $thismodules = explode(';', MODULE_ORDER_TOTAL_INSTALLED);
  reset($thismodules);
  while (list(, $value) = each($thismodules)) {
    if($value != 'ot_tax.php') {
      //      include(DIR_WS_LANGUAGES . $language . '/modules/' . $module_type . '/' . $value);
      //      include($module_directory . $value);
      $class = substr($value, 0, strrpos($value, '.'));
      //      $GLOBALS[$class] = new $class;
    } elseif($value == 'ot_tax.php') {
      $ot_tax_status = true;
    }
  }
}
  
$order_total_array = array();
if (is_array($thismodules)) {
  reset($thismodules);
  while (list(, $value) = each($thismodules)) {
    $class = substr($value, 0, strrpos($value, '.'));
    if ($GLOBALS[$class]->enabled) {
      $GLOBALS[$class]->process();

      for ($i=0, $n=sizeof($GLOBALS[$class]->output); $i<$n; $i++) {
	if (tep_not_null($GLOBALS[$class]->output[$i]['title']) ) {
	  $order_total_array[] = array('code' => $GLOBALS[$class]->code,
				       'title' => $GLOBALS[$class]->output[$i]['title'],
				       'text' => "",
				       'value' => $GLOBALS[$class]->output[$i]['value'],
				       'sort_order' => $GLOBALS[$class]->sort_order);
	}
      }
    }

  }
}
*/
$order_total_array = $order_total->process();
  
$order_totals = $order_total_array;
for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
  $sql_data_array = array('orders_id' => $insert_id,
			  'title' => $order_totals[$i]['title'],
			  'value' => $order_totals[$i]['value'], 
			  'text'=> "",
			  'class' => $order_totals[$i]['code'], 
			  'sort_order' => $order_totals[$i]['sort_order']);
  tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
}
  
if($ot_tax_status == true) {
  include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/' . $module_type . '/ot_tax.php');
  include($module_directory . 'ot_tax.php');
  $ot_tax = new ot_tax;
  $sql_data_array = array('orders_id' => $insert_id,
			  'title' => $ot_tax->title,
			  'value' => 0, 
			  'text' => "",
			  'class' => $ot_tax->code, 
			  'sort_order' => $ot_tax->sort_order);
  tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
}


  $shipping_temp_arr = array();
  $shipping_temp_arr['shipping_method']     = $shipping_method;
  $shipping_temp_arr['shipping_address']    = $shipping_address;
  $shipping_temp_arr['shipping_date']       = $shipping_date;
  $shipping_temp_arr['shipping_work_time']  = $shipping_work_time;
  $shipping_temp_arr['shipping_start_time'] = $shipping_start_time;
  $shipping_temp_arr['shipping_time']       = $shipping_time;
  $_SESSION['shipping_info_arr_'.$insert_id] = $shipping_temp_arr;

tep_redirect(tep_href_link(FILENAME_EDIT_NEW_ORDERS, 'oID=' . $insert_id, 'SSL'));



require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
