<?php
require('includes/application_top.php');

$order_id = $_POST['oID'];
$status_id = $_POST['stid'];
$orders_status_query = tep_db_query("select * from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_history_id = '".$status_id."'");
$orders_status_res = tep_db_fetch_array($orders_status_query);
if ($orders_status_res) {
  if ($orders_status_res['orders_status_id'] == '9') {
    $del_confirm_payemnt_time_query = tep_db_query("UPDATE `".TABLE_ORDERS."` set `confirm_payment_time` = '0000-00-00 00:00:00' where orders_id = '".$order_id."'");
    tep_insert_pwd_log($_POST['once_pwd'],$ocertify->auth_user);
  }
  tep_db_query("delete from `".TABLE_ORDERS_STATUS_HISTORY."` where `orders_status_history_id` = '".$status_id."'"); 
}
