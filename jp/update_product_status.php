<?php
  require('includes/application_top.php');

  $product_query = tep_db_query("select * from ".TABLE_PRODUCTS);
  
  while ($product_res = tep_db_fetch_array($product_query)) {
    $update_sql = "update `".TABLE_PRODUCTS_DESCRIPTION."` set `products_status` = '".$product_res['products_status']."' where `products_id`= '".$product_res['products_id']."'"; 
    tep_db_query($update_sql); 
  }
?>
