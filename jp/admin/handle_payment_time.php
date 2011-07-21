<?php
require('includes/application_top.php');

$order_id = $_POST['oID'];
$del_confirm_payemnt_time_query = tep_db_query("UPDATE `".TABLE_ORDERS."` set `confirm_payment_time` = '0000-00-00 00:00:00' where orders_id = '".$order_id."'");
