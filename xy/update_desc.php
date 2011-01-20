<?php

require('includes/application_top.php');

$category_arr = array();
$tmp_arr = array();

$category_arr[] = 168;
$category_arr[] = 169;
$category_arr[] = 190;

foreach ($category_arr as $key => $value) {
  $tmp_arr = array_merge($tmp_arr, get_category_children_id($value));
}
//var_dump($tmp_arr);
// 取得了全部需要处理的分类
$category_arr = array_merge($category_arr, $tmp_arr);

if (empty($category_arr)) {
  echo 'has no category'; 
  exit;
}

$products_arr = array();

$belong_pro_query = tep_db_query("select distinct products_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where categories_id in (".implode(',', $category_arr).")");

while ($belong_pro_res = tep_db_fetch_array($belong_pro_query)) {
  // 取得了全部商品id
  $products_arr[] = $belong_pro_res['products_id']; 
}

if (empty($products_arr)) {
  echo 'has no products';
  exit;
}

foreach ($products_arr as $pkey => $pvalue) {
  $pro_exists_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$pvalue."' and site_id = '".SITE_ID."'");
  $pro_exists_num = tep_db_num_rows($pro_exists_query); 
  if ($pro_exists_num > 0) {
    $jp_pro_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$pvalue."' and site_id = '1'");
    $jp_pro_res = tep_db_fetch_array($jp_pro_query);
    if ($jp_pro_res) {
      $update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_description = '".$jp_pro_res['products_description']."' where products_id = '".$pvalue."' and site_id = '".SITE_ID."';"; 
      tep_db_query($update_sql); 
      //echo $update_sql."\n";
      print_r(mysql_error());
    }
  } else {
    $zero_pro_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$pvalue."' and site_id = '0'");
    $zero_pro_res = tep_db_fetch_array($zero_pro_query);
    if ($zero_pro_res) {
      $jp_pro_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$pvalue."' and site_id = '1'");
      $jp_pro_res = tep_db_fetch_array($jp_pro_query);
      $jp_des = '';
      if ($jp_pro_res['products_description']) {
        $jp_des = $jp_pro_res['products_description']; 
      }
      $insert_sql = "insert into ".TABLE_PRODUCTS_DESCRIPTION." values('".$pvalue."', '4', '".$zero_pro_res['products_name']."', '".$jp_des."', '".SITE_ID."', '".$zero_pro_res['products_url']."', '".$zero_pro_res['products_viewed']."', '".$zero_pro_res['romaji']."');"; 
      tep_db_query($insert_sql); 
      print_r(mysql_error());
      //echo $insert_sql."\n";
    }
  }
}
echo $pkey.'done';
function get_category_children_id($category_id)
{
  $return_arr = array();    
  $child_query = tep_db_query("select * from ".TABLE_CATEGORIES." where parent_id = '".$category_id."'");
  while ($child_res = tep_db_fetch_array($child_query)) {
    $return_arr[] = $child_res['categories_id']; 
    $child_child_query = tep_db_query("select * from ".TABLE_CATEGORIES." where parent_id = '".$child_res['categories_id']."'");
    while ($child_child_res = tep_db_fetch_array($child_child_query)) {
      $return_arr[] = $child_child_res['categories_id']; 
    }
  }
  return $return_arr;
}
