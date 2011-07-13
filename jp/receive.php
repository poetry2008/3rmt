<?php 
require('includes/application_top.php');

check_uri('/^\/receive.php/');

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


//パラメータが設定されてない場合されていない箇所にエラーメッセージを設定する

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

  if ($w_rel == 'yes' &&  $w_option != "") {//optionが空白の場合optionの検索はしない
    $orders = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS." where telecom_option='".$w_option."' and date_purchased > '".(date('Y-m-d H:i:s',time()-86400))."'"));
  }

  if ($orders&&!$orders['telecom_name']&&!$orders['telecom_tel']&&!$orders['telecom_money']&&!$orders['telecom_email']) {
    // OK
    tep_db_perform(TABLE_ORDERS, array(
      'telecom_name'  => $w_username,
      'telecom_tel'   => $w_telno,
      'telecom_money' => $w_money,
      'telecom_email' => $w_email,
      'orders_status' => '30',
    ), 'update', "orders_id='".$orders['orders_id']."'");
    var_dump($orders);
    $sql_data_array = array('orders_id' => $orders['orders_id'], 
                          'orders_status_id' => '30', 
                          'date_added' => 'now()', 
                          'customer_notified' => '0',
                          'comments' => '');
    tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
    orders_updated($orders['orders_id']);
    tep_order_status_change($orders['orders_id'],30);
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
	$success = true;
  } else {
    // 不明
    tep_db_perform('telecom_unknow', array(
      '`option`' => $w_option,
      'username' => $w_username,
      'email' => $w_email,
      'telno' => $w_telno,
      'money' => $w_money,
      'rel' => $w_rel,
      'type' => ($w_rel == 'yes' && $w_option =="")?'success':'null',//optionが空白の場合手動作成である
      'date_added' => 'now()',
      'last_modified' => 'now()'

    ));
	$buming = true;
  }
} else {
       $error = true;
  // 不正
}

if($w_clientip == "76011"){
  echo "SuccessOK";
}else{
  echo "不正アクセス";
}


var_dump($success);
var_dump($buming);
var_dump($error);
echo '----------------';
var_dump($orders);
var_dump($orders['telecom_name']);
var_dump($orders['telecom_tel']);
var_dump($orders['telecom_money']);
var_dump($orders['telecom_email']);


?>
name _tel _money _email
