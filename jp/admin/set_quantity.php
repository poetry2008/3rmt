<?php 
require('includes/application_top.php');

$pID   = (int)$_GET['pid'];

if ($pID && trim($_GET['quantity']) !== '') {
  tep_db_query("update products set products_real_quantity='".mysql_real_escape_string((int)$_GET['quantity'])."' where products_id='".$pID."'");
  tep_db_query("update products set products_quantity=products_real_quantity+products_virtual_quantity where products_id='".$pID."'");
}

$product = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$pID."'"));
echo $product['products_real_quantity'];