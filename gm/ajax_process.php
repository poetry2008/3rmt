<?php
require_once('includes/application_top.php');

if ($_GET['action'] == 'calc_price') {
  
  $attributes_price = $_GET['oprice'];  
  $products_info_raw = tep_db_query("select * from ".TABLE_PRODUCTS." where products_id = '".$_GET['p_id']."'");
  $products_info = tep_db_fetch_array($products_info_raw);
  
  $products_price = tep_get_final_price($products_info['products_price'], $products_info['products_price_offset'], $products_info['products_small_sum'], $_GET['qty']);
 
  $final_price = $products_price + $attributes_price;
  $price_array = array();
  if ($final_price < 0) {
    $price_array['price'] = '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($final_price, tep_get_tax_rate($products_info['products_tax_class_id']), $_GET['qty'])).JPMONEY_UNIT_TEXT.'</font>'; 
  } else {
    $price_array['price'] = $currencies->display_price($final_price, tep_get_tax_rate($products_info['products_tax_class_id']), $_GET['qty']); 
  }
  echo json_encode($price_array);
  exit;
} else if($_GET['action'] == 'new_telecom_option') {
  if(!isset($_SESSION['option_list'])){
    $_SESSION['option_list'] = array($_SESSION['option']);
  }
  $_SESSION['option'] = date('Ymd-His'). ds_makeRandStr(2);
  array_push($_SESSION['option_list'],$_SESSION['option']);
  if($_SESSION['option']){
    echo $_SESSION['option'];
    exit;
  }
}
