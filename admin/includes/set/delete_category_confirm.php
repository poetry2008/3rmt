<?php
      if ($_POST['categories_id']) {
        $categories_id = tep_db_prepare_input($_POST['categories_id']);

        $categories = tep_get_category_tree($categories_id, '', '0', '', true);
        $products = array();
        $products_delete = array();

        for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
          $product_ids_query = tep_db_query("select products_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . $categories[$i]['id'] . "'");
          while ($product_ids = tep_db_fetch_array($product_ids_query)) {
            $products[$product_ids['products_id']]['categories'][] = $categories[$i]['id'];
          }
        }

        reset($products);
        while (list($key, $value) = each($products)) {
          $category_ids = '';
          for ($i = 0, $n = sizeof($value['categories']); $i < $n; $i++) {
            $category_ids .= '\'' . $value['categories'][$i] . '\', ';
          }
          $category_ids = substr($category_ids, 0, -2);

          $check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $key . "' and categories_id not in (" . $category_ids . ")");
          $check = tep_db_fetch_array($check_query);
          if ($check['total'] < '1') {
            $products_delete[$key] = $key;
          }
        }

        // Removing categories can be a lengthy process
        tep_set_time_limit(0);
        for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
          tep_remove_category($categories[$i]['id']);
        }

        reset($products_delete);
        while (list($key) = each($products_delete)) {
          tep_remove_product($key);
        }
      }

      if (USE_CACHE == 'true') {
        tep_reset_cache_block('categories');
        tep_reset_cache_block('also_purchased');
      }

