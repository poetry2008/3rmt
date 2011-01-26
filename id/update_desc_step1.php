<?php

require('includes/application_top.php');

$category_arr = array();
$tmp_arr = array();

$category_arr = array(198, 394, 284);
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

foreach ($products_arr as $key => $value) {
  $pro_first_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$value."' and site_id = '1'");
  $pro_first_res = tep_db_fetch_array($pro_first_query); 
  if ($pro_first_res) {
    $update_zero_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_description = '".mysql_real_escape_string($pro_first_res['products_description'])."' where products_id = '".$value."' and site_id = '0';"; 
    tep_db_query($update_zero_sql); 
  }
}

echo 'finish';
