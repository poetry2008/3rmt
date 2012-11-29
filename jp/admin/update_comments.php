<?php
set_time_limit(0);
include("includes/configure.php");

$con = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die('can not connect server!');
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");

echo 'start';
function tep_get_path_category($category_id)
{
   $category_array = array();
   
   $category_array[] = $category_id;
   $parent_category_raw = mysql_query("select parent_id from categories where categories_id = '".$category_id."'");
   $parent_category = mysql_fetch_array($parent_category_raw);
   
   if ($parent_category) {
     if ($parent_category['parent_id'] != '0') {
       $category_array [] = $parent_category['parent_id']; 
       $parent_parent_category_raw = mysql_query("select parent_id from categories where categories_id = '".$parent_category['parent_id']."'");
       $parent_parent_category = mysql_fetch_array($parent_parent_category_raw);
       
       if ($parent_parent_category) {
         if ($parent_parent_category['parent_id'] != '0') {
           $parent_parent_parent_category_raw = mysql_query("select parent_id from categories where categories_id = '".$parent_parent_category['parent_id']."'");
           $parent_parent_parent_category = mysql_fetch_array($parent_parent_parent_category_raw);
           if ($parent_parent_parent_category) {
             $category_array[] = $parent_parent_category['parent_id']; 
           }
         }
       }
     }
   }
   $category_tmp_array = array_reverse($category_array); 
   return implode('_', $category_tmp_array); 
}

$comments_raw = mysql_query("select * from set_comments");

while ($comments_info = mysql_fetch_array($comments_raw)) {
  $xlen = '460';
  $ylen = '150';
 
  $content = '';
  $content .= $comments_info['rule']."\r\n";
  $content .= "\r\n";
  $content .= "コメント:\r\n".$comments_info['comment']; 
  $belong = 'categories.php';  
  if (!empty($comments_info['categories_id'])) {
    $belong = 'categories.php?cPath='.tep_get_path_category($comments_info['categories_id']); 
  }
  
  $z_index = '1';
 
  $note_list_raw = mysql_query("select xyz from notes where belong = '".$belong."'");
  $note_list_array = array();
  
  while ($note_list_res = mysql_fetch_array($note_list_raw)) {
    $note_list_tmp_array = explode('|', $note_list_res['xyz']); 
    $note_list_array[] = $note_list_tmp_array[2]; 
  }
  
  if (!empty($note_list_array)) {
    $z_index = max($note_list_array) + 1; 
  }
 
  $xyz = '0|0|'.$z_index;
  
  $user_info_id = 'akiyama'; 
  $user_info_raw = mysql_query("select userid from users where name = '".trim($comments_info['author'])."' limit 1"); 
  $user_info_res = mysql_fetch_array($user_info_raw);
  
  if ($user_info_res) {
    $user_info_id = $user_info_res['userid']; 
  }
  $insert_note_sql = "insert into `notes` values(NULL, '単価ルール', '".addslashes($content)."', 'red', '".$xyz."|".$xlen."|".$ylen."', '".date('Y-m-d H:i:s', time())."', '1', '".addslashes($user_info_id)."', '".$belong."')";
  mysql_query($insert_note_sql);
}
echo '<br>finish';
