<?php
set_time_limit(0);
include("includes/configure.php");

$con = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die('can not connect server!');
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");

echo '<html>';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
echo '</head>';
echo '<body>';
echo 'start......<br><br>';
$i = 0;
$product_list_query = mysql_query("select products_id, site_id, p_manual from products_description where p_manual != ''");
while ($product_list_res = mysql_fetch_array($product_list_query)) {
  $tmp_content = stripslashes($product_list_res['p_manual']);
  $tmp_pos = strpos($tmp_content, 'https://192.168.0.200/admin/upload/manuals/'); 
  if ($tmp_pos !== false) {
    $tmp_content_tmp = str_replace('https://192.168.0.200/admin/upload/manuals/', 'upload/manuals/', $tmp_content); 
    $sql = "update `products_description` set `p_manual` = '".addslashes($tmp_content_tmp)."' where `site_id` = '".$product_list_res['site_id']."' and `products_id` = '".$product_list_res['products_id']."'"; 
    $product_name_query = mysql_query("select products_name from products_description where site_id = '0' and products_id = '".$product_list_res['products_id']."'"); 
    $product_name_info = mysql_fetch_array($product_name_query);
    if ($product_name_info) {
      echo $product_name_info['products_name'].'<br>'; 
      $i++; 
    }
    mysql_query($sql); 
  }
}

echo '<br><br>TOTAL:'.$i;
echo '<br><br>finish';
echo '</body>';
echo '</html>';
