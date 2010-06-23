<?php
      $products_id = tep_db_prepare_input($_POST['products_id']);
      $new_parent_id = tep_db_prepare_input($_POST['move_to_category_id']);

      $duplicate_check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . tep_db_input($products_id) . "' and categories_id = '" . tep_db_input($new_parent_id) . "'");
      $duplicate_check = tep_db_fetch_array($duplicate_check_query);
      if ($duplicate_check['total'] < 1) tep_db_query("update " . TABLE_PRODUCTS_TO_CATEGORIES . " set categories_id = '" . tep_db_input($new_parent_id) . "' where products_id = '" . tep_db_input($products_id) . "' and categories_id = '" . $current_category_id . "'");

      if (USE_CACHE == 'true') {
        tep_reset_cache_block('categories');
        tep_reset_cache_block('also_purchased');
      }

