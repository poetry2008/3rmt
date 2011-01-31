<?php
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_INFO);
  
//begin dynamic meta tags query -->
$the_product_info = tep_get_product_by_id((int)$_GET['products_id'], SITE_ID, $languages_id);
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
// end dynamic meta tags query -->
