<?php 
require('includes/application_top.php');

$pID   = (int)$_GET['pid'];

if ($pID && (trim($_GET['quantity']) !== '' || trim($_GET['virtual_quantity']) !== '')) {
  if (isset($_GET['quantity'])) {
    tep_db_query("update products set products_real_quantity='".mysql_real_escape_string((int)$_GET['quantity'])."',products_last_modified=now() where products_id='".$pID."'");
    $product = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$pID."'"));
    echo $product['products_real_quantity'];
  } else {
    tep_db_query("update products set products_virtual_quantity='".mysql_real_escape_string((int)$_GET['virtual_quantity'])."',products_last_modified=now() where products_id='".$pID."'");
    $product = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$pID."'"));
    echo $product['products_virtual_quantity'];
  }
}