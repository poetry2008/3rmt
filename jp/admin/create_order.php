<?php
require_once('includes/application_top.php');
require_once('includes/step-by-step/new_application_top.php');
//处理本身表单 查找customer{{
if(isset($_GET['site_id']) and isset($_GET['Customer_mail'] )){
  $email = $_GET['Customer_mail'];
  $site_id = $_GET['site_id'];
  $customerId = tep_get_customer_id_by_email($email,$site_id);
  

  if(!$customerId){
    //如果不存在则跳转到新建用户的页面
    tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT, 'site_id='.$site_id.'email_address=' . $email, 'SSL'));
  }
}
//}}
//{{列出下一页面需要用的变量
if(!isset($customerId)){
  if(isset($_POST['customers_id'])){
    $customerId = $_POST['customers_id'];
  }
}
if(isset($customerId)){
$lastemail      = $email;
$account        = tep_get_customer_by_id($customerId);
$address        = tep_get_address_by_cid($customer);
  }
$customer_id    = isset($account['customers_id'])           ? $account['customers_id']:'';  //d
$firstname      = isset($account['customers_firstname'])    ? $account['customers_firstname']:'';//d
$lastname       = isset($account['customers_lastname'])     ? $account['customers_lastname']:'';//d
$email_address  = isset($account['customers_email_address'])? $account['customers_email_address']:'';//d
$telephone      = isset($account['customers_telephone'])    ? $account['customers_telephone']:'';//n
$fax            = isset($account['customers_fax'])          ? $account['customers_fax']:'';//n
$zone_id        = isset($account['entry_zone_id'])          ? $account['entry_zone_id']:'';//n
//$site_id        = isset($account['site_id'])                ? $account['site_id']:'';
$street_address = isset($address['entry_street_address'])   ? $address['entry_street_address']:'';//n
$company        = isset($address['entry_company'])          ? $address['entry_company']:'';//n
$suburb         = isset($address['entry_suburb'])           ? $address['entry_suburb']:'';//n
$postcode       = isset($address['entry_postcode'])         ? $address['entry_postcode']:'';//n
$city           = isset($address['entry_city'])             ? $address['entry_city']:'';//n
$state          = isset($address['entry_zone_id'])          ? tep_get_zone_name($address['entry_zone_id']):'';//n
$country        = isset($address['entry_country_id'])       ? tep_get_country_name($address['entry_country_id']):'';//n

$cpayment = payment::getInstance((int)$_GET['site_id']);
$payment_array = payment::getPaymentList();
if(!isset($selections)){
$selections = $cpayment->admin_selection();
}
$payment_list[] = array('id' => 'payment_null', 'text' => '支払方法を選択してください');
//}}

require_once(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_CREATE_ORDER);
require_once("includes/step-by-step/create_order_new_first.php");

