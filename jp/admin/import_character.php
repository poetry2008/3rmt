<?php
set_time_limit(0);
include('includes/configure.php');
$connect = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD) or die ('can not connect database');
mysql_select_db(DB_DATABASE);
mysql_query('set names utf8');
echo "starting...<br>";

$order_products_raw = mysql_query('select orders_products_id, products_id, orders_id, products_character from orders_products');
$show_num = 0;
while ($order_products = mysql_fetch_array($order_products_raw)) {
  if ($order_products['products_character'] != '') {
    $products_raw = mysql_query('select belong_to_option from products where products_id = \''.$order_products['products_id'].'\''); 
    $products = mysql_fetch_array($products_raw); 
    $option_group_id = 0; 
    $option_item_id = 0; 
    if ($products) {
      if (!empty($products['belong_to_option'])) {
        $item_raw = mysql_query('select id from option_item where type=\'textarea\' and front_title = \'お客様のキャラクター名\' and group_id = \''.$products['belong_to_option'].'\''); 
        $item = mysql_fetch_array($item_raw);
        if ($item) {
          $option_group_id = $products['belong_to_option']; 
          $option_item_id = $item['id']; 
        }
      }
    }
    $option_info = array('title' => 'お客様のキャラクター名', 'value' => $order_products['products_character']); 
    $insert_sql = 'insert into `orders_products_attributes` values (NULL, \''.$order_products['orders_id'].'\', \''.$order_products['orders_products_id'].'\', \'\', \'\', 0, \'\', 0, \''.addslashes(serialize($option_info)).'\',\''.$option_group_id.'\', \''.$option_item_id.'\')';
    mysql_query($insert_sql); 
  }
 
  echo $show_num;
  $show_num++;
}

echo 'finish';
