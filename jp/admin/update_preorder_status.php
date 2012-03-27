<?php
set_time_limit(0);
include("includes/configure.php");
$connect=mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD) or die('can not connect database');

mysql_select_db(DB_DATABASE);
mysql_query("set names 'utf8'");

echo "starting...<br>";

$product_des_raw = mysql_query("select products_id, preorder_status from products_description where site_id = '0'");
while ($product_des = mysql_fetch_array($product_des_raw)) {
  mysql_query("update `products_description` set `preorder_status` = '".$product_des['preorder_status']."' where `products_id` = '".$product_des['products_id']."' and site_id != '0'");
}

echo "finish";
