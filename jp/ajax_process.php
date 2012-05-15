<?php
require('includes/application_top.php');

if ($_GET['action'] == 'get_option') {
  require('option/HM_Option.php');
  require('option/HM_Option_Group.php');

  $hm_option = new HM_Option();
  
  $product_info_raw = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$_GET['p_id']."'");
  $product_info = tep_db_fetch_array($product_info_raw);
   
  $option_array = $hm_option->get_product_option($product_info['belong_to_option'], $_GET['p_id']);
 
  echo json_encode($option_array);
} else if ($_GET['action'] == 'calc_price') {
  require('option/HM_Option.php');
  require('option/HM_Option_Group.php');

  $hm_option = new HM_Option();
  $option_array = array(); 
  foreach ($_GET as $key => $value) {
    $op_single_str = substr($key, 0, 3);
    if ($op_single_str == 'op_') {
      $option_array[$key] = trim($value); 
    }
  }
  
  $attributes_price = $hm_option->calc_option_price($option_array);
  
  $products_info_raw = tep_db_query("select * from ".TABLE_PRODUCTS." where products_id = '".$_GET['p_id']."'");
  $products_info = tep_db_fetch_array($products_info_raw);
  
  $products_price = tep_get_final_price($products_info['products_price'], $products_info['products_price_offset'], $products_info['products_small_sum'], $_GET['qty']);
 
  $final_price = $products_price + $attributes_price;
  $price_array = array();
  if ($final_price < 0) {
    //$price_array['price'] = '<font color="#ff0000">'.$currencies->display_price($final_price, tep_get_tax_rate($products_info['products_tax_class_id  ']), $_GET['qty']).'</font>'; 
    $price_array['price'] = $currencies->display_price($final_price, tep_get_tax_rate($products_info['products_tax_class_id  ']), $_GET['qty']); 
  } else {
    $price_array['price'] = $currencies->display_price($final_price, tep_get_tax_rate($products_info['products_tax_class_id  ']), $_GET['qty']); 
  }
  echo json_encode($price_array['price']);
}
