<?php
set_time_limit(0);
include("includes/configure.php");

$connect = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD)or die ("can not connect server!");
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");
echo "start!<br>";

$option_group_raw = mysql_query("select * from option_group");

while ($option_group = mysql_fetch_array($option_group_raw)) {
  $product_info_raw = mysql_query("select products_id from products where belong_to_option = '".$option_group['id']."' limit 1");
  $product_info = mysql_fetch_array($product_info_raw);
  
  if ($product_info) {
    $own_category_raw = mysql_query("select categories_id from products_to_categories where products_id = '".$product_info['products_id']."'"); 
    $own_category = mysql_fetch_array($own_category_raw); 
    
    if ($own_category) {
      $category_name = tep_get_top_category_name($own_category['categories_id']);    
      if (!empty($category_name)) {
        $exists_group_raw = mysql_query("select name from option_group where name like '".$category_name."_%' order by name desc limit 1"); 
        $exists_group = mysql_fetch_array($exists_group_raw);
        if ($exists_group) {
          $last_pos = strrpos($exists_group['name'], '_');
          $tmp_num = substr($exists_group['name'], $last_pos + 1) + 1;
          $group_name = $category_name . '_' . $tmp_num; 
        } else {
          $exists_tmp_group_raw = mysql_query("select name from option_group where name = '".$category_name."' order by name desc limit 1"); 
          $exists_tmp_group = mysql_fetch_array($exists_tmp_group_raw);
          if ($exists_tmp_group) {
            $group_name = $category_name . '_1'; 
          } else {
            $group_name = $category_name; 
          }
        }
        mysql_query("update `option_group` set `name` = '".$group_name."' where id = '".$option_group['id']."'"); 
        $option_item_raw = mysql_query("select title, id from option_item where title != 'お客様のキャラクター名' and type = 'select' and group_id = '".$option_group['id']."'");  
        while ($option_item_res = mysql_fetch_array($option_item_raw)) {
            mysql_query("update `option_item` set title = '".$option_item_res['title'].'･'.$category_name."' where id = '".$option_item_res['id']."'");  
        }
      }
    }
  }
}
echo 'finish';
function tep_get_top_category_name($cid) 
{
   $return_name_array = array(); 
   $current_category_raw = mysql_query("select parent_id from categories where categories_id = '".$cid."'"); 
   $current_category = mysql_fetch_array($current_category_raw);
  
   if ($current_category) {
     $category_name_raw = mysql_query("select categories_name from categories_description where categories_id = '".$cid."' and (site_id = '0' or site_id = '1') order by site_id desc limit 1"); 
     $category_name = mysql_fetch_array($category_name_raw);
     $return_name_array[] = $category_name['categories_name'];
     if ($current_category['parent_id'] != '0') {
       $parent_category_raw = mysql_query("select parent_id from categories where categories_id = '".$current_category['parent_id']."'"); 
       $parent_category = mysql_fetch_array($parent_category_raw); 
       if ($parent_category) {
         $category_name_name_raw = mysql_query("select categories_name from categories_description where categories_id = '".$current_category['parent_id']."' and (site_id = '0' or site_id = '1') order by site_id desc limit 1"); 
         $category_name_name = mysql_fetch_array($category_name_name_raw);
         $return_name_array[] = $category_name_name['categories_name'];
         if ($parent_category['parent_id'] != '0') {
           $parent_parent_category_raw = mysql_query("select parent_id from categories where categories_id = '".$parent_category['parent_id']."'"); 
           $parent_parent_category = mysql_fetch_array($parent_parent_category_raw); 
           if ($parent_parent_category) {
             $category_name_name_name_raw = mysql_query("select categories_name from categories_description where categories_id = '".$parent_category['parent_id']."' and (site_id = '0' or site_id = '1') order by site_id desc limit 1"); 
             $category_name_name_name = mysql_fetch_array($category_name_name_name_raw);
             $return_name_array[] = $category_name_name_name['categories_name'];
           }
         }
       }
     }
   }

   if (!empty($return_name_array)) {
      $reverse_tmp_array = array_reverse($return_name_array);  
      return implode('･', $reverse_tmp_array); 
   }
   return '';
}
