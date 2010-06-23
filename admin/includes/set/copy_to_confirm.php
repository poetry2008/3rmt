<?php
      if ( (tep_not_null($_POST['products_id'])) && (tep_not_null($_POST['categories_id'])) ) {
        $products_id   = tep_db_prepare_input($_POST['products_id']);
        $categories_id = tep_db_prepare_input($_POST['categories_id']);

        if ($_POST['copy_as'] == 'link') {
          if ($_POST['categories_id'] != $current_category_id) {
            $check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . tep_db_input($products_id) . "' and categories_id = '" . tep_db_input($categories_id) . "'");
            $check = tep_db_fetch_array($check_query);
            if ($check['total'] < '1') {
              tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) values ('" . tep_db_input($products_id) . "', '" . tep_db_input($categories_id) . "')");
            }
          } else {
            $messageStack->add_session(ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
          }
        } elseif ($_POST['copy_as'] == 'duplicate') {
          $product_query = tep_db_query("
              select *
              from " . TABLE_PRODUCTS . " 
              where products_id = '" . tep_db_input($products_id) . "'
            ");
          $product = tep_db_fetch_array($product_query);

          tep_db_query("
              insert into " . TABLE_PRODUCTS . " (
                products_quantity, 
                products_model,
                products_image,
                products_image2,
                products_image3, 
                products_price, 
                products_price_offset,
                products_date_added, 
                products_date_available, 
                products_weight, 
                products_status, 
                products_tax_class_id, 
                manufacturers_id,
                products_bflag,
                products_cflag,
                products_small_sum,
                option_type
              ) values (
              '" . $product['products_quantity'] . "', 
              '" . $product['products_model'] . "', 
              '" . $product['products_image'] . "', 
              '" . $product['products_image2'] . "', 
              '" . $product['products_image3'] . "',
              '" . $product['products_price'] . "',  
              '" . $product['products_price_offset'] . "',  
              now(), 
              '" . $product['products_date_available'] . "', 
              '" . $product['products_weight'] . "', 
              '0', 
              '" . $product['products_tax_class_id'] . "', 
              '" . $product['manufacturers_id'] . "',
              '" . $product['products_bflag'] . "',
              '" . $product['products_cflag'] . "',
              '" . $product['products_small_sum'] . "',
              '" . $product['option_type'] . "',
            )");
          $dup_products_id = tep_db_insert_id();
      
          $description_query = tep_db_query("
              select *
              from " . TABLE_PRODUCTS_DESCRIPTION . " 
              where products_id = '" . tep_db_input($products_id) . "'");
          while ($description = tep_db_fetch_array($description_query)) {
            tep_db_query("
                insert into " . TABLE_PRODUCTS_DESCRIPTION . " (
                  products_id, 
                  language_id, 
                  products_name, 
                  products_description,
                  products_attention_1, 
                  products_attention_2, 
                  products_attention_3, 
                  products_attention_4,
                  products_attention_5,
                  products_url, 
                  products_viewed,
                  site_id
                ) values (
                  '" . $dup_products_id . "', 
                  '" . $description['language_id'] . "', 
                  '" . addslashes($description['products_name']) . "', 
                  '" . addslashes($description['products_description']) . "', 
                  '" . addslashes($description['products_attention_1']) . "', 
                  '" . addslashes($description['products_attention_2']) . "', 
                  '" . addslashes($description['products_attention_3']) . "', 
                  '" . addslashes($description['products_attention_4']) . "', 
                  '" . addslashes($description['products_attention_5']) . "', 
                  '" . $description['products_url'] . "', 
                  '0',
                  '" . $description['site_id'] . "'
                )");
          }

          tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) values ('" . $dup_products_id . "', '" . tep_db_input($categories_id) . "')");
          $products_id = $dup_products_id;
        }

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('categories');
          tep_reset_cache_block('also_purchased');
        }
      }

