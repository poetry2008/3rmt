<?php
      if ( ($_POST['products_id']) && (is_array($_POST['product_categories'])) ) {
        $product_id = tep_db_prepare_input($_POST['products_id']);
        $product_categories = $_POST['product_categories'];

        //option delete
        tep_db_query("delete from products_attributes where products_id = '".$products_id."'");

        for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
          tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . tep_db_input($product_id) . "' and categories_id = '" . tep_db_input($product_categories[$i]) . "'");
        }

        $product_categories_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . tep_db_input($product_id) . "'");
        $product_categories = tep_db_fetch_array($product_categories_query);

        if ($product_categories['total'] == '0') {
          tep_remove_product($product_id);
        }
      }

      if (USE_CACHE == 'true') {
        tep_reset_cache_block('categories');
        tep_reset_cache_block('also_purchased');
      }

