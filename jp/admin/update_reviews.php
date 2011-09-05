<?php
set_time_limit(0);

ini_set('display_errors', 'On');

$host_url = "localhost";

$database_name = "maker_3rmt";

$user_name = "maker";

$user_pass = "123456";

$con = mysql_connect($host_url, $user_name, $user_pass);

if (!$con) {
  echo 'can not connect database';
  exit;
}

mysql_select_db($database_name);
mysql_query("set names utf8");

echo 'starting....';
$reviews_query = mysql_query("select * from reviews");

while ($reviews = mysql_fetch_array($reviews_query)) {
  $product_info_query = mysql_query("select products_status from products_description where (site_id = '0' or site_id = '".$reviews['site_id']."') and products_id = '".$reviews['products_id']."' order by site_id DESC limit 1");
  $product_info = mysql_fetch_array($product_info_query);
  mysql_query("UPDATE `reviews` set `products_status` = '".$product_info['products_status']."' where `reviews_id` = '".$reviews['reviews_id']."'");
}

echo 'finish';
