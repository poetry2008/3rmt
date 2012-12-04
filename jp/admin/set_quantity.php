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
  
  $last_modified_array = getdate(strtotime(tep_datetime_short($products_info['products_last_modified'])));
  $today_array = getdate();
  $last_modified = date('n/j H:i:s',strtotime(tep_datetime_short($products_info['products_last_modified'])));
  if (
     $last_modified_array["year"] == $today_array["year"] 
  && $last_modified_array["mon"] == $today_array["mon"] 
  && $last_modified_array["mday"] == $today_array["mday"]
  ) {
    if ($last_modified_array["hours"] >= ($today_array["hours"]-2)) {
      $html_str .= tep_image(DIR_WS_ICONS . 'signal_blue.gif', $last_modified);
    } elseif ($last_modified_array["hours"] >= ($today_array["hours"]-5)) {
      $html_str .= tep_image(DIR_WS_ICONS . 'signal_yellow.gif', $last_modified);
    } else {
      $html_str .= tep_image(DIR_WS_ICONS . 'signal_red.gif', $last_modified);
    }
  } else {
    $html_str .= tep_image(DIR_WS_ICONS . 'signal_blink.gif', $last_modified);
  }
 
  echo $html_str;
}
