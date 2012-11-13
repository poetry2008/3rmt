<?php
/*
  $Id$
*/

  //require(DIR_WS_BOXES . 'quick_link.php');

  if ( (USE_CACHE == 'true') && !SID ) {
    $tmp_id_array = array();
    if (basename($_SERVER['PHP_SELF']) == FILENAME_PREORDER) {
      $tmp_left_products_id = tep_preorder_get_products_id_by_param();
      $tmp_left_ca_path = tep_get_product_path($tmp_left_products_id);
      if (tep_not_null($tmp_left_ca_path)) {
        $tmp_id_array = tep_parse_category_path($tmp_left_ca_path); 
      }
    } else if (basename($_SERVER['PHP_SELF']) == FILENAME_PREORDER_PAYMENT) {
      $tmp_left_ca_path = tep_get_product_path($_POST['products_id']);
      if (tep_not_null($tmp_left_ca_path)) {
        $tmp_id_array = tep_parse_category_path($tmp_left_ca_path); 
      }
    } else if (basename($_SERVER['PHP_SELF']) == FILENAME_PREORDER_SUCCESS) {
      $tmp_left_preorder_product_raw = tep_db_query("select products_id from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_SESSION['send_preorder_id']."'"); 
      $tmp_left_preorder_product = tep_db_fetch_array($tmp_left_preorder_product_raw);
      $tmp_left_ca_path = tep_get_product_path($tmp_left_preorder_product['products_id']);
      if (tep_not_null($tmp_left_ca_path)) {
        $tmp_id_array = tep_parse_category_path($tmp_left_ca_path); 
      }
    }
    echo tep_cache_categories_box(false, false, $tmp_id_array);
  } else {
    include(DIR_WS_BOXES . 'categories.php');
  }
/*
  //Color
  if(COLOR_SEARCH_BOX_TF == 'true') {
    include(DIR_WS_BOXES . 'color.php');
  }
*/

  //require(DIR_WS_BOXES . 'information.php');
  //require(DIR_WS_BOXES . 'banners.php');
  //require(DIR_WS_BOXES . 'cl.php');

/*s
  require(DIR_WS_BOXES . 'whats_new.php');*/
    
  /*if (substr(basename($PHP_SELF), 0, 5) == 'index' && (int)$current_category_id == 0) {
   require(DIR_WS_BOXES . 'banners.php');
  }*/
?>
