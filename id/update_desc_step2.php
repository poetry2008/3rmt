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

foreach ($products_arr as $pkey => $pvalue) {
  $pro_exists_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$pvalue."' and site_id = '".SITE_ID."'");
  $pro_exists_num = tep_db_num_rows($pro_exists_query); 
  if ($pro_exists_num > 0) {
    $jp_pro_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$pvalue."' and site_id = '1'");
    $jp_pro_res = tep_db_fetch_array($jp_pro_query);
    if ($jp_pro_res) {
      $update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_description = '".mysql_real_escape_string($jp_pro_res['products_description'])."' where products_id = '".$pvalue."' and site_id = '".SITE_ID."';"; 
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
      $insert_sql = "insert into ".TABLE_PRODUCTS_DESCRIPTION." values('".$pvalue."', '4', '".mysql_real_escape_string($zero_pro_res['products_name'])."', '".mysql_real_escape_string($jp_des)."', '".SITE_ID."', '".$zero_pro_res['products_url']."', '".$zero_pro_res['products_viewed']."', '".mysql_real_escape_string($zero_pro_res['romaji'])."');"; 
      tep_db_query($insert_sql); 
      print_r(mysql_error());
      //echo $insert_sql."\n";
    }
  }
}
echo $pkey.'done';
