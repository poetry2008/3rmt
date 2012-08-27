<?php
set_time_limit(0);
include("includes/configure.php");

$con = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die('can not connect server!');
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");

echo 'start!<br>';
$before_time = "2012-08-13 04:00:00";

$configure_raw = mysql_query("select configuration_value from configuration where configuration_key = 'MODULE_ORDER_TOTAL_POINT_ADD_STATUS' and site_id = '0'");
$configure_res = mysql_fetch_array($configure_raw);
define('MODULE_ORDER_TOTAL_POINT_ADD_STATUS', $configure_res['configuration_value']);

$configure_one_raw = mysql_query("select configuration_value from configuration where configuration_key = 'MODULE_ORDER_TOTAL_POINT_FEE' and site_id = '0'");
$configure_one_res = mysql_fetch_array($configure_one_raw);
define('MODULE_ORDER_TOTAL_POINT_FEE', $configure_one_res['configuration_value']);

$orders_list_raw = mysql_query("select orders_id from orders_status_history where date_added >= '".$before_time."' and orders_status_id = '".MODULE_ORDER_TOTAL_POINT_ADD_STATUS."' group by orders_id");

while ($orders_list_res = mysql_fetch_array($orders_list_raw)) {
  $pcount_query = mysql_query("select customers_id, payment_method from orders where orders_id = '".$orders_list_res['orders_id']."'");
  $pcount = mysql_fetch_array($pcount_query);
  if ($pcount) {
    if ($pcount['payment_method'] != 'ポイント(買い取り)') {
      $query2 = mysql_query("select value from orders_total where class = 'ot_point' and orders_id = '".$orders_list_res['orders_id']."'");
      $result2 = mysql_fetch_array($query2);
      
      $query3 = mysql_query("select value from orders_total where class = 'ot_subtotal' and orders_id = '".$orders_list_res['orders_id']."'");
      $result3 = mysql_fetch_array($query3);
      
      $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
        
      if ($result3['value'] >= 0) {
        $get_point = ($result3['value'] - (int)$result2['value']) * $point_rate;
      } else {
        if ($result3['value'] > -200) {
          if ($check_status['payment_method'] == '来店支払い') {
            $get_point = 0;
          } else {
            $get_point = abs($result3['value']);
          }
        } else {
          $get_point = 0;
        }
      }
      mysql_query( "update customers set point = point + " . $get_point . " where customers_id = '" . $pcount['customers_id'] . "'");
    }
  }
}

echo 'finish';
