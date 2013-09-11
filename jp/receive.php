<?php 
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'payment.php');
//check_uri('/^\/receive.php/');

header("Content-type: text/html"); 

$w_clientip = $_GET['clientip'];
$w_telno    = $_GET['telno'];
$w_email    = $_GET['email'];
$w_sendid   = $_GET['sendid'];
$w_username = $_GET['username'];
$w_money    = $_GET['money'];
$w_cont     = $_GET['cont'];
$w_option   = $_GET['option'];
$w_rel      = $_GET['rel'];


//没有设置参数的时候，设置错误信息

/*
$w_error="パラメータが不正です";
if(!isset($w_telno)){
  $w_telno=$w_error;
}
if(!isset($w_email)){
  $w_email=$w_error;
}
if(!isset($w_sendid)){
  $w_sendid=$w_error;
}
if(!isset($w_username)){
  $w_username=$w_error;
}
if(!isset($w_money)){
  $w_money=$w_error;
}
if(!isset($w_cont)){
  $w_cont=$w_error;
}
if(!isset($w_option)){
  $w_option=$w_error;
}
*/
if ($w_clientip == '76011' && $w_username && $w_email && $w_money && $w_telno) {

  if ($w_rel == 'yes' &&  $w_option != "") {//option是空白的是偶，不搜索option
    $orders = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS." where telecom_option='".$w_option."' and date_purchased > '".(date('Y-m-d H:i:s',time()-86400))."'"));
  }

  if ($orders&&!$orders['telecom_name']&&!$orders['telecom_tel']&&!$orders['telecom_money']&&!$orders['telecom_email']) {
    // OK
    $payment_modules = payment::getInstance($orders['site_id']); 
    $payment_code = payment::changeRomaji($orders['payment_method'], PAYMENT_RETURN_TYPE_CODE);    
    $orders_status_id = $payment_modules->get_default_status_id($payment_code, $orders['site_id']); 
    $orders_status_id = $orders_status_id != 0 ? $orders_status_id : DEFAULT_ORDERS_STATUS_ID; 
    
    tep_db_perform(TABLE_ORDERS, array(
      'telecom_name'  => $w_username,
      'telecom_tel'   => $w_telno,
      'telecom_money' => $w_money,
      'telecom_email' => $w_email,
      'orders_status' => $orders_status_id,
    ), 'update', "orders_id='".$orders['orders_id']."'");
    $sql_data_array = array('orders_id' => $orders['orders_id'], 
                          'orders_status_id' => $orders_status_id, 
                          'date_added' => 'now()', 
                          'customer_notified' => '0',
                          'comments' => $payment_modules->getModule($payment_code)->show_text_info);
    tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
    orders_updated($orders['orders_id']);
    tep_order_status_change($orders['orders_id'], $orders_status_id);
    // success
    tep_db_perform('telecom_unknow', array(
      '`option`'      => $w_option,
      'username'      => $w_username,
      'email'         => $w_email,
      'telno'         => $w_telno,
      'money'         => $w_money,
      'rel'           => $w_rel,
      'type'          => 'success',
      'date_added'    => 'now()',
      'last_modified' => 'now()'
    ));

  } else {
    // 不清楚
    tep_db_perform('telecom_unknow', array(
      '`option`' => $w_option,
      'username' => $w_username,
      'email' => $w_email,
      'telno' => $w_telno,
      'money' => $w_money,
      'rel' => $w_rel,
      'type' => ($w_rel == 'yes' && $w_option =="")?'success':'null',//option是空白的时候手动做成
      'date_added' => 'now()',
      'last_modified' => 'now()'

    ));
  }
} else {

  // 不正确
}

if($w_clientip == "76011"){
  echo "SuccessOK";
}else{
  echo "不正アクセス";
}

?>

