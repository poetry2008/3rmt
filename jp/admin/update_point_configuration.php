<?php
set_time_limit(0);
include("includes/configure.php");
$con = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die('can not connect server!');
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");

echo 'starting......';

$configuration_raw = mysql_query("select configuration_id, configuration_value from configuration where configuration_key in ('MODULE_PAYMENT_MONEYORDER_IS_GET_POINT', 'MODULE_PAYMENT_BUYINGPOINT_IS_GET_POINT', 'MODULE_PAYMENT_CONVENIENCE_STORE_IS_GET_POINT', 'MODULE_PAYMENT_PAYPAL_IS_GET_POINT', 'MODULE_PAYMENT_POSTALMONEYORDER_IS_GET_POINT', 'MODULE_PAYMENT_RAKUTEN_BANK_IS_GET_POINT', 'MODULE_PAYMENT_TELECOM_IS_GET_POINT', 'MODULE_PAYMENT_BUYING_IS_GET_POINT', 'MODULE_PAYMENT_FETCH_GOOD_IS_GET_POINT', 'MODULE_PAYMENT_FREE_PAYMENT_IS_GET_POINT')");

while ($configuration_info = mysql_fetch_array($configuration_raw)) {
  if ($configuration_info['configuration_value'] == '1') {
    mysql_query("update `configuration` set `configuration_value` = 'True', `set_function` = 'tep_cfg_select_option(array(''True'', ''False''),' where `configuration_id` = '".$configuration_info['configuration_id']."'"); 
  } else {
    mysql_query("update `configuration` set `configuration_value` = 'False', `set_function` = 'tep_cfg_select_option(array(''True'', ''False''),' where `configuration_id` = '".$configuration_info['configuration_id']."'"); 
  }
}
echo 'finish';
