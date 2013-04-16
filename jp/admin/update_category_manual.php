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

$category_list_query = mysql_query("select categories_id, site_id, c_manual from categories_description where c_manual != ''");
$i = 0;
while ($category_list_res = mysql_fetch_array($category_list_query)) {
  $tmp_content = stripslashes($category_list_res['c_manual']);
  $tmp_pos = strpos($tmp_content, 'https://192.168.0.200/admin/upload/manuals/');
  if ($tmp_pos !== false) {
    $tmp_content_tmp = str_replace('https://192.168.0.200/admin/upload/manuals/', 'upload/manuals/', $tmp_content); 
    $sql = "update `categories_description` set `c_manual` = '".addslashes($tmp_content_tmp)."' where `site_id` = '".$category_list_res['site_id']."' and `categories_id` = '".$category_list_res['categories_id']."'";
    $category_name_query = mysql_query("select categories_name from categories_description where categories_id = '".$category_list_res['categories_id']."' and site_id = '0'"); 
    $category_name_info = mysql_fetch_array($category_name_query);
    if($category_name_info) {
      echo $category_name_info['categories_name'].'<br>'; 
      $i++; 
    }
    mysql_query($sql);
  }
}

echo '<br><br>TOTAL:'.$i;
echo '<br><br>finish';
echo '</body>';
echo '</html>';
