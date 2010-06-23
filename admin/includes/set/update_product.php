<?php

      
      $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;

      if ( (isset($_POST['edit_x']) && $_POST['edit_x']) || (isset($_POST['edit_y']) && $_POST['edit_y']) ) {
        $_GET['action'] = 'new_product';
      } else {
        $products_id = tep_db_prepare_input($_GET['pID']);
        $products_date_available = tep_db_prepare_input($_POST['products_date_available']);
        //$site_id = tep_db_prepare_input($_POST['site_id']);

        $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';

        if(isset($_POST['products_image2_del']) && $_POST['products_image2_del'] == 'none') {
          $_POST['products_image2'] = 'none';
        }
      
        if(isset($_POST['products_image3_del']) && $_POST['products_image3_del'] == 'none') {
          $_POST['products_image3'] = 'none';
        }
        //％指定の場合は価格を算出
        $HTTP_POST_VARS['products_price_offset'] = SBC2DBC($HTTP_POST_VARS['products_price_offset']);
        /*
          if (substr($HTTP_POST_VARS['products_price_offset'], -1) == '%') {
          $HTTP_POST_VARS['products_price_offset'] = (($HTTP_POST_VARS['products_price_offset'] / 100) * $HTTP_POST_VARS['products_price']);
          }*/
        $sql_data_array = array('products_quantity' => tep_db_prepare_input($_POST['products_quantity']),
                                'products_model' => tep_db_prepare_input($_POST['products_model']),
                                'products_image' => (($_POST['products_image'] == 'none') ? '' : tep_db_prepare_input($_POST['products_image'])),
                                'products_image2' => (($_POST['products_image2'] == 'none') ? '' : tep_db_prepare_input($_POST['products_image2'])),
                                'products_image3' => (($_POST['products_image3'] == 'none') ? '' : tep_db_prepare_input($_POST['products_image3'])),
                                'products_price' => tep_db_prepare_input($_POST['products_price']),
                                'products_price_offset' => tep_db_prepare_input($HTTP_POST_VARS['products_price_offset']),
                                'products_date_available' => $products_date_available,
                                'products_weight' => tep_db_prepare_input($_POST['products_weight']),
                                'products_status' => tep_db_prepare_input($_POST['products_status']),
                                'products_tax_class_id' => tep_db_prepare_input($_POST['products_tax_class_id']),
                                'manufacturers_id' => tep_db_prepare_input($_POST['manufacturers_id']),
                                'products_bflag' => tep_db_prepare_input($_POST['products_bflag']),
                                'products_cflag' => tep_db_prepare_input($_POST['products_cflag']),
                                'option_type' => tep_db_prepare_input($_POST['option_type']),
                                'products_small_sum' => tep_db_prepare_input($_POST['products_small_sum']));

        if ($_GET['action'] == 'insert_product') {
          $insert_sql_data = array('products_date_added' => 'now()');
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_PRODUCTS, $sql_data_array);
          $products_id = tep_db_insert_id();
          tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) values ('" . $products_id . "', '" . $current_category_id . "')");
        } elseif ($_GET['action'] == 'update_product') {
          $update_sql_data = array('products_last_modified' => 'now()');
          $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
          tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . tep_db_input($products_id) . '\'');
        }
        //add product tags
        tep_db_query("delete from ".TABLE_PRODUCTS_TO_TAGS." where products_id='".$products_id."'"); 
        if ($_POST['tags']) {
          $sql = "insert into ".TABLE_PRODUCTS_TO_TAGS."(products_id, tags_id) values "; 
          foreach ($_POST['tags'] as $key => $t) {
            $sql .= "('".$products_id."','".$t."')"; 
            if ($key != count($_POST['tags'])-1) {
              $sql .= ','; 
            }
          }
          tep_db_query($sql); 
        }

        //-----------------------------------------
        // カラー別画像インサートスタート
        //-----------------------------------------
        $color_query = tep_db_query("select * from ".TABLE_COLOR." order by color_name");
        $cnt=0;
        while($color = tep_db_fetch_array($color_query)) {
          if ($_GET['action'] == 'insert_product') {
            if($_POST['image_'.$color['color_id']]) {
              tep_db_query("insert into ".TABLE_COLOR_TO_PRODUCTS."(color_id, products_id, color_image, categories_id, manufacturers_id, color_to_products_name) values ('".$color['color_id']."', '".tep_db_input($products_id)."', '".$_POST['image_'.$color['color_id']]."', '".$current_category_id."', '".tep_db_prepare_input($_POST['manufacturers_id'])."', '".tep_db_prepare_input($_POST['colorname_'.$color['color_id']])."')");
            }
          } elseif ($_GET['action'] == 'update_product') {
            //update self check
            $upd_query = tep_db_query("select count(*) as cnt from ".TABLE_COLOR_TO_PRODUCTS." where products_id = '".tep_db_input($products_id)."' and color_id = '".$color['color_id']."'");
            $upd = tep_db_fetch_array($upd_query);
            if($upd['cnt'] == 0 && isset($_POST['image_'.$color['color_id']]) && $_POST['image_'.$color['color_id']]) {
              tep_db_query("insert into ".TABLE_COLOR_TO_PRODUCTS."(color_id, products_id, color_image, categories_id, manufacturers_id) values ('".$color['color_id']."', '".tep_db_input($products_id)."', '".$_POST['image_'.$color['color_id']]."', '".$current_category_id."', '".tep_db_prepare_input($_POST['manufacturers_id'])."')");
            }
        
            //Update color_to_products_name
            @tep_db_query("update ".TABLE_COLOR_TO_PRODUCTS." set color_to_products_name = '".tep_db_prepare_input($_POST['colorname_'.$color['color_id']])."' where products_id = '".tep_db_input($products_id)."' and color_id = '".$color['color_id']."'");
        
            if(isset($_POST['image_'.$color['color_id']]) && $_POST['image_'.$color['color_id']] && $_POST['image_'.$color['color_id']] == 'none') {
              //delete color_image date
              tep_db_query("delete from ".TABLE_COLOR_TO_PRODUCTS." where products_id = '".tep_db_input($products_id)."' and color_id = '".$color['color_id']."'");
            } elseif(isset($_POST['image_'.$color['color_id']]) && $_POST['image_'.$color['color_id']] && !empty($_POST['image_'.$color['color_id']]) && $_POST['image_'.$color['color_id']] != 'none') {
              //update color_image date
              tep_db_query("update ".TABLE_COLOR_TO_PRODUCTS." set color_image = '".tep_db_prepare_input($_POST['image_'.$color['color_id']])."' where products_id = '".tep_db_input($products_id)."' and color_id = '".$color['color_id']."'");
              tep_db_query("update ".TABLE_COLOR_TO_PRODUCTS." set categories_id = '".$current_category_id."' where products_id = '".tep_db_input($products_id)."' and color_id = '".$color['color_id']."'");
              tep_db_query("update ".TABLE_COLOR_TO_PRODUCTS." set manufacturers_id = '".tep_db_prepare_input($_POST['manufacturers_id'])."' where products_id = '".tep_db_input($products_id)."' and color_id = '".$color['color_id']."'");
            }
          }
        } // end color while
        //-----------------------------------------
        // カラー別画像インサート終了
        //-----------------------------------------

        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];

          //商品説明を結合
          $des = tep_db_prepare_input($_POST['products_description'][$language_id]);
          $products_attention_1 = tep_db_prepare_input($_POST['products_jan']);
          $products_attention_2 = tep_db_prepare_input($_POST['products_size']);
          $products_attention_3 = tep_db_prepare_input($_POST['products_naiyou']);
          $products_attention_4 = tep_db_prepare_input($_POST['products_zaishitu']);
          $products_attention_5 = tep_db_prepare_input($_POST['products_attention_5']);
          $sql_data_array = array(
                                  'products_name'        => tep_db_prepare_input($_POST['products_name'][$language_id]),
                                  'products_description' => $des,
                                  'products_attention_1' => $products_attention_1,
                                  'products_attention_2' => $products_attention_2,
                                  'products_attention_3' => $products_attention_3,
                                  'products_attention_4' => $products_attention_4,
                                  'products_attention_5' => $products_attention_5,
                                  'products_url'         => tep_db_prepare_input($_POST['products_url'][$language_id]));

          if (isset($_GET['action']) && ($_GET['action'] == 'insert_product' || ($_GET['action'] == 'update_product' && !tep_products_description_exist($products_id,$site_id,$language_id)))) {
            $insert_sql_data = array('products_id' => $products_id,
                                     'language_id' => $language_id,
                                     'site_id' => $site_id);
            $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
            tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
          } elseif ($_GET['action'] == 'update_product') {
            tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \'' . tep_db_input($products_id) . '\' and language_id = \'' . $language_id . '\' and site_id =\''.$site_id.'\'');
          }
        }
      
        //-----------------------------------------
        // オプション値インサートスタート
        //-----------------------------------------
        /*
          メモ
          ----------------------------
          $op1 -> オプション名ID
          $op2 -> オプション値ID
          $op3 -> 
        */
        $products_options_array = $_POST['products_options'];
        $options_array = explode("\n", $products_options_array);
      
        //商品に対応するオプションを全削除
        tep_db_query("delete from products_attributes where products_id = '".$products_id."'");
      
        for($i=0; $i<sizeof($options_array); $i++) {
          $products_options = explode(",", $options_array[$i]);
          $options_name = $products_options[0];
          $options_value = $products_options[1];
          $options_price = $products_options[2];
          $options_prefix = $products_options[3];
          $products_at_quantity = $products_options[4];

          if(!empty($options_name) && !empty($options_value) && $options_price != '' && !empty($options_prefix)) {
            //products_optionsをチェック
            $op_query1 = tep_db_query("select products_options_id from products_options where products_options_name = '".$options_name."' and language_id = '4'");
            if(tep_db_num_rows($op_query1)) {
              $op_result1 = tep_db_fetch_array($op_query1);
              $op1 = $op_result1['products_options_id'];
            } else {
              //products_options_idを作成
              $poid_query = tep_db_query("select products_options_id from products_options order by products_options_id desc limit 1");
              $poid_result = tep_db_fetch_array($poid_query);
              $poid = $poid_result['products_options_id'] + 1;
        
              tep_db_query("insert into products_options (products_options_id,language_id,products_options_name) values ('".$poid."', '4', '".tep_db_prepare_input($options_name)."')");
              $op1 = $poid;
            }
        
            //products_options_valuesをチェック
            $op_query2 = tep_db_query("select products_options_values_id from products_options_values where products_options_values_name = '".$options_value."' and language_id = '4'");
            if(tep_db_num_rows($op_query2)) {
              $op_result2 = tep_db_fetch_array($op_query2);
              $op2 = $op_result2['products_options_values_id'];
            } else {
              //products_options_values_idを作成
              $povid_query = tep_db_query("select products_options_values_id from products_options_values order by products_options_values_id desc limit 1");
              $povid_result = tep_db_fetch_array($povid_query);
              $povid = $povid_result['products_options_values_id'] + 1;
        
              tep_db_query("insert into products_options_values (products_options_values_id,language_id,products_options_values_name) values ('".$povid."', '4', '".tep_db_prepare_input($options_value)."')");
              $op2 = $povid;
            }
        
            //products_options_values_to_products_optionsをチェック
            $op_cnt_query3 = tep_db_query("select count(*) as cnt from products_options_values_to_products_options where products_options_id = '".$op1."' and products_options_values_id = '".$op2."'");
            $op_cnt_result3 = tep_db_fetch_array($op_cnt_query3);
            if($op_cnt_result3['cnt'] == 0) {
              tep_db_query("insert into products_options_values_to_products_options (products_options_values_to_products_options_id,products_options_id,products_options_values_id) values ('', '".$op1."', '".$op2."')");
            }
        
            //products_attributes
            $op_sql_date_array = array('products_id' => tep_db_prepare_input($products_id),
                                       'options_id' => tep_db_prepare_input($op1),
                                       'options_values_id' => tep_db_prepare_input($op2),
                                       'options_values_price' => tep_db_prepare_input($options_price),
                                       'price_prefix' => tep_db_prepare_input($options_prefix),
                                       'products_at_quantity' => tep_db_prepare_input($products_at_quantity)
                                       );
        
            tep_db_perform('products_attributes', $op_sql_date_array);
          }
        }
      
        //-----------------------------------------
        // オプション値インサート終了
        //-----------------------------------------
      
        //-----------------------------------------
        // 特価商品インサート
        //-----------------------------------------
        /*
          if(!empty($_POST['products_special_price'])) {
          //％指定の場合は価格を算出
          if (substr($_POST['products_special_price'], -1) == '%') {
          $new_special_insert_query = tep_db_query("select products_id, products_price from " . TABLE_PRODUCTS . " where products_id = '" . tep_db_prepare_input($products_id) . "'");
          $new_special_insert = tep_db_fetch_array($new_special_insert_query);
          $_POST['products_price'] = $new_special_insert['products_price'];
          $_POST['products_special_price'] = ($_POST['products_price'] - (($_POST['products_special_price'] / 100) * $_POST['products_price']));
          } 
      
          $spcnt_query = tep_db_query("select count(*) as cnt from " . TABLE_SPECIALS . " where products_id = '".tep_db_prepare_input($products_id)."'");
          $spcnt = tep_db_fetch_array($spcnt_query);
          if($spcnt['cnt'] > 0) {
          //登録済みなのでアップデート
          tep_db_query("update " . TABLE_SPECIALS . " set specials_new_products_price = '".tep_db_prepare_input($_POST['products_special_price'])."', specials_last_modified = now(), status = '1' where  products_id = '".tep_db_prepare_input($products_id)."'");
          } else {
          //未登録なのでインサート
          tep_db_query("insert into " . TABLE_SPECIALS . "(specials_id, products_id, specials_new_products_price, specials_date_added, status) values ('', '".tep_db_prepare_input($products_id)."', '".tep_db_prepare_input($_POST['products_special_price'])."', now(), '1')");
          }
          } else {
          $spcnt_query = tep_db_query("select count(*) as cnt from " . TABLE_SPECIALS . " where products_id = '".tep_db_prepare_input($products_id)."'");
          $spcnt = tep_db_fetch_array($spcnt_query);
          if($spcnt['cnt'] > 0) {
          //データを削除
          tep_db_query("delete from " . TABLE_SPECIALS . " where products_id = '" . tep_db_prepare_input($products_id) . "'");
          }
          }
        */
        //-----------------------------------------
        // 特価商品インサート終了
        //-----------------------------------------
      
        if (USE_CACHE == 'true') {
          tep_reset_cache_block('categories');
          tep_reset_cache_block('also_purchased');
        }

