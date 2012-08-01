<?php
set_time_limit(0);
include("includes/configure.php");
$connect = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD)or die ("can not connect server!");
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");

echo "start!<br>";
$ca_sql_group = "INSERT INTO `option_group` (`id`, `name`, `title`, `option`, `comment`, `is_preorder`, `sort_num`, `created_at`) VALUES (NULL, 'お客様のキャラクター名', 'お客様のキャラクター名', '', '', '1', '1000', '".date('Y-m-d H:i:s', time())."')";
mysql_query($ca_sql_group);
$ca_last_id = mysql_insert_id();
$ca_item_t_name = rand_name();

$ca_string_array = 'a:7:{s:5:"itext";s:0:"";s:7:"require";s:1:"1";s:8:"icomment";s:0:"";s:5:"iline";s:1:"1";s:6:"ictype";s:1:"0";s:8:"imax_num";s:3:"100";s:3:"eid";i:0;}';
$ca_string_array_show = @unserialize($ca_string_array);
$ca_string_array_show['eid'] = $ca_last_id;

$ca_string = serialize($ca_string_array_show);

$ca_sql_item_add = "INSERT INTO `option_item` (`id`, `group_id`, `title`, `front_title`, `name`, `comment`, `option`, `type`, `price`, `status`, `sort_num`, `place_type`, `created_at`) VALUES (NULL, '".$ca_last_id."', 'お客様のキャラクター名', 'お客様のキャラクター名', '".$ca_item_t_name."', '', '".addslashes($ca_string)."', 'textarea', '0.0000', '1', '1000', '1', '".date('Y-m-d H:i:s', time())."')";
mysql_query($ca_sql_item_add);

$no_option_pro_raw = mysql_query("select products_id from products where belong_to_option = '' and products_cflag = '1'");
while ($no_option_pro_res = mysql_fetch_array($no_option_pro_raw)) {
  $sql_products = "UPDATE `products` SET `belong_to_option` = '".$ca_last_id."' WHERE `products_id` = '".$no_option_pro_res['products_id']."'";
  mysql_query($sql_products);
}
echo "Complete!";


function rand_name(){
  $pattern = "abcdefghijklmnopqrstuvwxyz";
  $res = "";
  for($i = 0;$i < 16;$i++){
    $res .= $pattern[mt_rand(0,25)];
  }
  $check_res_sql = "select name from option_item where name='".$res."'";
  $check_res_query = mysql_query($check_res_sql);
  $check_res_rows = mysql_num_rows($check_res_query);
  if($check_res_rows > 0){
    rand_name();
  }
  return $res;
}

?>
