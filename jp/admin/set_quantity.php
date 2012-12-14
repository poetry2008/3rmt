<?php 
require('includes/application_top.php');

$pID   = (int)$_GET['pid'];

if ($pID && (trim($_GET['quantity']) !== '' || trim($_GET['virtual_quantity']) !== '')) {
  $html_str = ''; 
  if (isset($_GET['quantity'])) {
    tep_db_query("update products set products_real_quantity='".mysql_real_escape_string((int)$_GET['quantity'])."',products_last_modified=now() where products_id='".$pID."'");
    $product = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$pID."'"));
    $html_str .= $product['products_real_quantity'];
  } else {
    tep_db_query("update products set products_virtual_quantity='".mysql_real_escape_string((int)$_GET['virtual_quantity'])."',products_last_modified=now() where products_id='".$pID."'");
    $product = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$pID."'"));
    $html_str .= $product['products_virtual_quantity'];
  }

  $html_str .= '|||';
  $products_info_raw = tep_db_query("select products_last_modified from ".TABLE_PRODUCTS." where products_id = '".$pID."'"); 
  $products_info = tep_db_fetch_array($products_info_raw); 
  
  $html_str .= tep_get_signal_pic_info($products_info['products_last_modified']); 
  echo $html_str;
}
