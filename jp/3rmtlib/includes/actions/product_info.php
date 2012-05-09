<?php
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_INFO);
  
//begin dynamic meta tags query -->
$the_product_info = tep_get_product_by_id((int)$_GET['products_id'], SITE_ID,
    $languages_id,true,'product_info');
//forward 404
forward404Unless($the_product_info);

$the_product_name        = strip_tags ($the_product_info['products_name'], "");
$the_product_description = mb_substr (strip_tags (replace_store_name($the_product_info['products_description']), ""),0,65);
$the_product_model       = strip_tags ($the_product_info['products_model'], "");

// ccdd
$the_manufacturer_query = tep_db_query("
    SELECT m.manufacturers_id, 
           m.manufacturers_name 
    FROM " . TABLE_MANUFACTURERS . " m 
      LEFT join " . TABLE_MANUFACTURERS_INFO . " mi 
        ON (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . (int)$languages_id . "'), " . TABLE_PRODUCTS . " p  
    WHERE p.products_id = '" . (int)$_GET['products_id'] . "' 
      AND p.manufacturers_id = m.manufacturers_id
    "); 
$the_manufacturers = tep_db_fetch_array($the_manufacturer_query);

require('option/HM_Option.php');
require('option/HM_Option_Group.php');

$hm_option = new HM_Option();

if ($_GET['action'] == 'process') {
  $option_info_array = array(); 
  if (!$hm_option->check()) {
    foreach ($_POST as $p_key => $p_value) {
      $op_single_str = substr($p_key, 0, 3);
      if ($op_single_str == 'op_') {
        $option_info_array[$p_key] = $p_value; 
      } 
    }
    if (isset($_POST['products_id']) && is_numeric($_POST['products_id'])) {
      $cart->add_cart($_POST['products_id'], $cart->get_quantity($cart->get_products_uprid($_POST['products_id'], $option_info_array))+$_POST['quantity'], '', true, $option_info_array);   
    }
    tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters))); 
  }
}

if (strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
  $_SESSION['history_url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
}
