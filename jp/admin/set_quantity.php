<?php 
require('includes/application_top.php');

$pID   = (int)$_GET['pid'];

if ($pID && (trim($_GET['quantity']) !== '' || trim($_GET['virtual_quantity']) !== '')) {
  $html_str = ''; 
  if (isset($_GET['quantity'])) {
    //更新真实库存 
    if(isset($_GET['is_radices'])&&$_GET['is_radices']==1){
      $update_quantity = mysql_real_escape_string((int)$_GET['quantity']);
      $radices = tep_get_radices($pID); 
      $product = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$pID."'"));
      $real_quantity = (int)($product['products_real_quantity']);
      $update_quantity = (int)($update_quantity*$radices) + 
        (int)($real_quantity%$radices);
    }else{
      $update_quantity = mysql_real_escape_string((int)$_GET['quantity']);
    }
    tep_db_query("update products set products_real_quantity='".$update_quantity."' where products_id='".$pID."'");
    tep_db_query("update products_description set products_last_modified=now(), products_user_update='".$_SESSION['user_name']."' where products_id = '".$pID."'"); 
    $product = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$pID."'"));
    $html_str .= tep_get_quantity($pID);
    $html_str .= '<<<';
    $html_str .= (int)($product['products_real_quantity']);
  } else {
    //更新虚拟库存 
    tep_db_query("update products set products_virtual_quantity='".mysql_real_escape_string((int)$_GET['virtual_quantity'])."' where products_id='".$pID."'");
    tep_db_query("update products_description set products_last_modified=now(), products_user_update='".$_SESSION['user_name']."' where products_id = '".$pID."'"); 
    $product = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$pID."'"));
    $html_str .= $product['products_virtual_quantity'];
  }

  $html_str .= '|||';
  $products_info_raw = tep_db_query("select products_last_modified from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$pID."' and site_id = '0'"); 
  $products_info = tep_db_fetch_array($products_info_raw); 
  
  $html_str .= tep_get_signal_pic_info($products_info['products_last_modified']); 
  echo $html_str;
}
