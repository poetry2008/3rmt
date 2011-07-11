<?php
/*
  $Id$
*/
 $host_url = "localhost";

 $database_name = "maker_3rmt";
 
 $user_name = "maker";
 
 $user_pass = "123456";
  
 $con = mysql_connect($host_url, $user_name, $user_pass);

 if (!$con) {
   echo 'wrong'; 
   exit; 
 }
mysql_select_db($database_name);
mysql_query("set names utf8");

$product_list_query = mysql_query("select * from ( select distinct p.products_image, m.manufacturers_name, p.products_model,pd.products_description, p.products_real_quantity + p.products_virtual_quantity as products_quantity, p.products_weight, m.manufacturers_id, p.products_bflag, p.products_id, p.sort_order, pd.products_name, p.products_price, p.products_tax_class_id, pd.site_id, pd.products_status, p.products_price_offset, p.products_small_sum from ( products p ) left join manufacturers m using(manufacturers_id), products_description pd, categories c, products_to_categories p2c where p.products_id = pd.products_id and pd.language_id = '4' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id order by pd.site_id DESC ) p where site_id = 0 or site_id = 9 group by products_id having p.products_status != '0' and p.products_status != '3' union select * from ( select distinct p.products_image, m.manufacturers_name, p.products_model,pd.products_description, p.products_real_quantity + p.products_virtual_quantity as products_quantity, p.products_weight, m.manufacturers_id, p.products_bflag, p.products_id, p.sort_order, pd.products_name, p.products_price, p.products_tax_class_id, pd.site_id, pd.products_status, p.products_price_offset, p.products_small_sum from ( products p ) left join manufacturers m using(manufacturers_id), products_description pd, tags t, products_to_tags p2t where p.products_id = pd.products_id and pd.language_id = '4' and p.products_id = p2t.products_id and p2t.tags_id = t.tags_id order by pd.site_id DESC ) p where site_id = 0 or site_id = 9 group by products_id having p.products_status != '0' and p.products_status != '3';"); 
 
  while ($product_list_res = mysql_fetch_array($product_list_query)) {
    $products_id = $product_list_res['products_id'];
    var_dump($products_id);
    exit;
    for ($i=0; $i<155; $i++) {
      $reviews_sql = "insert reviews values(NULL, '".$products_id."', '0', 'test', '5', '".date('Y-m-d H:i:s', time())."', NULL, '5', '9', '1', '127.0.0.1')"; 
      mysql_query($reviews_sql);
      $reviews_id = mysql_insert_id(); 
      $reviews_desc_sql = "insert reviews_description values('".$reviews_id."', '4', 'hello')";
      mysql_query($reviews_desc_sql);
    }
  }
 
  echo 'finish';
