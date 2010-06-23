<?php
      $products_id = tep_db_prepare_input($_GET['pID']);
      $site_id     = tep_db_prepare_input($_POST['pID']);
      //％指定の場合は価格を算出
      $HTTP_POST_VARS['products_price_offset'] = SBC2DBC($HTTP_POST_VARS['products_price_offset']);
      /*
        if (substr($HTTP_POST_VARS['products_price_offset'], -1) == '%') {
        $HTTP_POST_VARS['products_price_offset'] = (($HTTP_POST_VARS['products_price_offset'] / 100) * $HTTP_POST_VARS['products_price']);
        } */
      $update_sql_data = array('products_last_modified' => 'now()',
                               'products_quantity' => tep_db_prepare_input($_POST['products_quantity']),
                               'products_price_offset' => tep_db_prepare_input($HTTP_POST_VARS['products_price_offset']),
                               'products_price' => tep_db_prepare_input($_POST['products_price']));
      tep_db_perform(TABLE_PRODUCTS, $update_sql_data, 'update', 'products_id = \'' . tep_db_input($products_id) . '\'');

      // 特価商品インサート
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
      
        // 特価商品インサート終了
        */

      /*
      // キャラクター名インサート
      $des_query = tep_db_query("
      select products_attention_1,
      products_attention_2,
      products_attention_3,
      products_attention_4,
      products_attention_5,
      products_description 
      from products_description 
      where language_id = '4' 
      and products_id = '" . tep_db_input($products_id) . "'");
      $des_result = tep_db_fetch_array($des_query);
      $sql_data_array = array( 
      //'products_description' => tep_db_prepare_input($des_result['products_description']),
      'products_attention_5' => tep_db_prepare_input($_POST['products_attention_5'])
      );
      if(!tep_products_description_exist($products_id, $site_id, 4)){
      }
      tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \'' . tep_db_input($products_id) . '\' and language_id = \'4\'');
      */
      // 終

