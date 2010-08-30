<?php 
require('includes/application_top.php');

$pID   = (int)$_GET['pid'];

if ($pID && trim($_GET['quantity']) !== '') {
  tep_db_query("update products set products_quantity='".mysql_real_escape_string((int)$_GET['quantity'])."' where products_id='".$pID."'");
}

$product = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$pID."'"));
echo $product['products_quantity'];