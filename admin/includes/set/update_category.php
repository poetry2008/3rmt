<?php
      $categories_id = tep_db_prepare_input($_POST['categories_id']);
      $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
      $sort_order = tep_db_prepare_input($_POST['sort_order']);
      $sql_data_array = array('sort_order' => $sort_order);

      if ($_GET['action'] == 'insert_category') {
        $insert_sql_data = array('parent_id' => $current_category_id,
                                 'date_added' => 'now()');
        $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
        tep_db_perform(TABLE_CATEGORIES, $sql_data_array);
        $categories_id = tep_db_insert_id();
      } elseif ($_GET['action'] == 'update_category') {
        $update_sql_data = array('last_modified' => 'now()');
        $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
        tep_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\'');
      }

      $languages = tep_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $categories_name_array = $_POST['categories_name'];
        $categories_meta_text = $_POST['categories_meta_text'];
        $seo_name = $_POST['seo_name'];
        $categories_header_text = $_POST['categories_header_text'];
        $categories_footer_text = $_POST['categories_footer_text'];
        $text_information = $_POST['text_information'];
        $meta_keywords = $_POST['meta_keywords'];
        $meta_description = $_POST['meta_description'];

          
        $language_id = $languages[$i]['id'];
        $sql_data_array = array(
                                'categories_name' => tep_db_prepare_input($categories_name_array[$language_id]),
                                'categories_meta_text' => tep_db_prepare_input($categories_meta_text[$language_id]),
                                'seo_name' => tep_db_prepare_input($seo_name[$language_id]),
                                'categories_header_text' => tep_db_prepare_input($categories_header_text[$language_id]),
                                'categories_footer_text' => tep_db_prepare_input($categories_footer_text[$language_id]),
                                'text_information' => tep_db_prepare_input($text_information[$language_id]),
                                'meta_keywords' => tep_db_prepare_input($meta_keywords[$language_id]),
                                'meta_description' => tep_db_prepare_input($meta_description[$language_id]),
                                );
        if ($_GET['action'] == 'insert_category' || ($_GET['action'] == 'update_category' && !tep_categories_description_exist($categories_id, $site_id, $language_id))) {
          $insert_sql_data = array('categories_id' => $categories_id,
                                   'language_id'   => $languages[$i]['id'],
                                   'site_id'       => $site_id
                                   );
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
          //categories_image2 upload => INSERT
          $categories_image2 = tep_get_uploaded_file('categories_image2');
          //$image_directory = tep_get_local_path(DIR_FS_CATALOG_IMAGES);
          $image_directory = tep_get_local_path(tep_get_upload_dir($site_id) . 'categories/');
           
          if (is_uploaded_file($categories_image2['tmp_name'])) {
            tep_db_query("update " . TABLE_CATEGORIES_DESCRIPTION . " set categories_image2 = '" . $categories_image2['name'] . "' where categories_id = '" . tep_db_input($categories_id) . "' and site_id='".$site_id."'");
            tep_copy_uploaded_file($categories_image2, $image_directory);
          }
          //categories_image3 upload => INSERT
          $categories_image3 = tep_get_uploaded_file('categories_image3');
          //$image_directory = tep_get_local_path(DIR_FS_CATALOG_IMAGES);
          $image_directory = tep_get_local_path(tep_get_upload_dir($site_id) . 'categories/');
           
          if (is_uploaded_file($categories_image3['tmp_name'])) {
            tep_db_query("update " . TABLE_CATEGORIES_DESCRIPTION . " set categories_image3 = '" . $categories_image3['name'] . "' where categories_id = '" . tep_db_input($categories_id) . "' and site_id='".$site_id."'");
            tep_copy_uploaded_file($categories_image3, $image_directory);
          }
      
      
        } elseif ($_GET['action'] == 'update_category') {
          //print_r($sql_data_array);
          //echo('categories_id = \'' . $categories_id . '\' and language_id = \'' . $languages[$i]['id'] . '\' and site_id = \''.$site_id.'\'');
          //exit;
          tep_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\' and language_id = \'' . $languages[$i]['id'] . '\' and site_id = \''.$site_id.'\'');
            
          //categories_image2 upload => UPDATE
          $categories_image2 = tep_get_uploaded_file('categories_image2');
          //$image_directory = tep_get_local_path(DIR_FS_CATALOG_IMAGES);
          $image_directory = tep_get_local_path(tep_get_upload_dir($site_id) . 'categories/');
       
          if (is_uploaded_file($categories_image2['tmp_name'])) {
            tep_db_query("update " . TABLE_CATEGORIES_DESCRIPTION . " set categories_image2 = '" . $categories_image2['name'] . "' where categories_id = '" . tep_db_input($categories_id) . "' and site_id='".$site_id."'");
            tep_copy_uploaded_file($categories_image2, $image_directory);
          }
          //categories_image3 upload => UPDATE
          $categories_image3 = tep_get_uploaded_file('categories_image3');
       
          if (is_uploaded_file($categories_image2['tmp_name'])) {
            tep_db_query("update " . TABLE_CATEGORIES_DESCRIPTION . " set categories_image2 = '" . $categories_image2['name'] . "' where categories_id = '" . tep_db_input($categories_id) . "' and site_id='".$site_id."'");
            tep_copy_uploaded_file($categories_image2, $image_directory);
          }
          //categories_image3 upload => UPDATE
          $categories_image3 = tep_get_uploaded_file('categories_image3');
          //$image_directory = tep_get_local_path(DIR_FS_CATALOG_IMAGES);
          $image_directory = tep_get_local_path(tep_get_upload_dir($site_id) . 'categories/');
       
          if (is_uploaded_file($categories_image3['tmp_name'])) {
            tep_db_query("update " . TABLE_CATEGORIES_DESCRIPTION . " set categories_image3 = '" . $categories_image3['name'] . "' where categories_id = '" . tep_db_input($categories_id) . "' and site_id = '".$site_id."'");
            tep_copy_uploaded_file($categories_image3, $image_directory);
          }
        }
      }

      $categories_image = tep_get_uploaded_file('categories_image');
      //$image_directory = tep_get_local_path(DIR_FS_CATALOG_IMAGES);
      $image_directory = tep_get_local_path(tep_get_upload_dir($site_id) . 'categories/');

      if (is_uploaded_file($categories_image['tmp_name'])) {
        //print($categories_image.' '.$image_directory);
        //exit;
        tep_db_query("update " . TABLE_CATEGORIES . " set categories_image = '" . $categories_image['name'] . "' where categories_id = '" . tep_db_input($categories_id) . "'");
        tep_copy_uploaded_file($categories_image, $image_directory);
      }

      if (USE_CACHE == 'true') {
        tep_reset_cache_block('categories');
        tep_reset_cache_block('also_purchased');
      }

