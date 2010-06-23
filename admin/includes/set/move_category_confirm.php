<?php
      if ( ($_POST['categories_id']) && ($_POST['categories_id'] != $_POST['move_to_category_id']) ) {
        $categories_id = tep_db_prepare_input($_POST['categories_id']);
        $new_parent_id = tep_db_prepare_input($_POST['move_to_category_id']);
        tep_db_query("update " . TABLE_CATEGORIES . " set parent_id = '" . tep_db_input($new_parent_id) . "', last_modified = now() where categories_id = '" . tep_db_input($categories_id) . "'");

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('categories');
          tep_reset_cache_block('also_purchased');
        }
      }

