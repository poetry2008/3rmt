<?php
/*
   $Id$
*/
  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');  
  

  $currencies = new currencies();

    /*
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    exit;
    */

  $action = (isset($_GET['action']) ? $_GET['action'] : '');
  if ( eregi("(insert|update|setflag)", $action) ) include_once('includes/reset_seo_cache.php');

  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
      case 'get_products':
        echo tep_draw_pull_down_menu('xxx',array_merge(array(array('id' => '0','text' => '関連付けなし')),tep_get_products_tree($_GET['cid'])),$_GET['rid'],'onchange=\'$("#relate_products_id").val(this.options[this.selectedIndex].value)\'');
        exit;
        break;
      case 'toggle':
          if ($_GET['cID']) {
            $cID = intval($_GET['cID']);
            if (isset($_GET['status']) && ($_GET['status'] == 0 || $_GET['status'] == 1 || $_GET['status'] == 2)){
              tep_set_categories_status($cID, intval($_GET['status']));
            } else {
              $c_query = tep_db_query("select * from `".TABLE_CATEGORIES."` where
                  `categories_id`='".$cID."'");
              $c = tep_db_fetch_array($c_query);
              if($c){
                $update_query = tep_db_query("UPDATE `".TABLE_CATEGORIES."` SET
                    `categories_status` = '".($c['categories_status']?'0':'1')."'
                    WHERE `categories_id` ='".$cID."' LIMIT 1 ;");
              }
            }
          }
          tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $HTTP_GET_VARS['cPath']));
          break;
      case 'setflag':
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') || ($_GET['flag'] == '2') ) {
          if ($_GET['pID']) {
            tep_set_product_status($_GET['pID'], $_GET['flag']);
          }
          if (USE_CACHE == 'true') {
            tep_reset_cache_block('categories');
            tep_reset_cache_block('also_purchased');
          }
        }
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath']));
        break;
      case 'simple_update': // 価格と数量の簡易アップデート
        $products_id = tep_db_prepare_input($_GET['pID']);
        $site_id     = tep_db_prepare_input($_POST['pID']);
        //％指定の場合は価格を算出
        $HTTP_POST_VARS['products_price_offset'] = SBC2DBC($HTTP_POST_VARS['products_price_offset']);
        $update_sql_data = array('products_last_modified' => 'now()',
                                 'products_quantity' => tep_db_prepare_input($_POST['products_quantity']),
                                 'products_attention_5' => tep_db_prepare_input($_POST['products_attention_5']),
                                 //'products_price_offset' => tep_db_prepare_input($HTTP_POST_VARS['products_price_offset']),
                                 'products_price' => tep_db_prepare_input($_POST['products_price']));
        tep_db_perform(TABLE_PRODUCTS, $update_sql_data, 'update', 'products_id = \'' . tep_db_input($products_id) . '\'');
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&pID=' . $products_id));
        break;
      case 'upload_keyword':
        //删除没有关系的mission 

        $sql_del_no_categories_mission = 'DELETE FROM '.TABLE_MISSION.' WHERE id NOT IN (SELECT mission_id FROM '.TABLE_CATEGORIES_TO_MISSION.')';
        $sql_del_no_mission_session = 'delete from '.TABLE_SESSION_LOG.'  WHERE mission_id NOT IN (SELECT mission_id FROM '.TABLE_CATEGORIES_TO_MISSION.')';
        $sql_del_no_mission_record = 'delete from '.TABLE_RECORD.'  WHERE mission_id NOT IN (SELECT mission_id FROM '.TABLE_CATEGORIES_TO_MISSION.')';

        $kWord = trim($_POST['keyword']);
        $categories_id = $_POST['categories_id'];
        $method = $_POST['method'];
        if($method){
          //如果关键字为空 删除当前关系 
          if($kWord==''){
          tep_db_query("DELETE FROM ".TABLE_CATEGORIES_TO_MISSION. " WHERE categories_id = ".$categories_id);
          tep_db_query($sql_del_no_categories_mission);
          tep_db_query($sql_del_no_mission_session);
          tep_db_query($sql_del_no_mission_record);
          break;

          }
          // 修改  只存在唯一一个mission即同名mission为同一mission
          // 1.判断 keyword是否在 mission 中存在,如果存在,则关联,如果不存在,则新建,并关联
        $mission_to_categories_whith_keyword_exist = "SELECT c2m.categories_id ,c2m.mission_id from "
                                                    .TABLE_CATEGORIES_TO_MISSION.' c2m, '
                                                    .TABLE_MISSION .' m '
                                                    ."WHERE m.keyword='".$kWord."' "
                                                    ."AND c2m.mission_id = m.id "
                                                    ."AND c2m.categories_id = ".$categories_id;
                                                        
       while(mysql_num_rows($exist_res = tep_db_query($mission_to_categories_whith_keyword_exist))==0){
         $sql_exist_mission_named = "SELECT id from ".TABLE_MISSION." where keyword='".$kWord."'";
         while(mysql_num_rows(tep_db_query($sql_exist_mission_named))==0){
          tep_db_perform(TABLE_MISSION, array(
                'name' => 'category '.$categories_id,
                'keyword' => $kWord,
                'page_limit' => '2',
                'result_limit' => '20',
                'enabled' => '1',
                'engine' => 'google',
                ));
         }
         $single_mission_sql = 'UPDATE '
              .TABLE_CATEGORIES_TO_MISSION .' c2m'
              .','.TABLE_MISSION.' m'
              ." SET mission_id = m.id"
              ." WHERE m.keyword = '".$kWord."'" 
              ." and c2m.categories_id = ".$categories_id;
         tep_db_query($single_mission_sql); 
         while(mysql_affected_rows()==0){
          tep_db_perform(TABLE_CATEGORIES_TO_MISSION, array(
                'mission_id' =>'0',
                'categories_id'=>$categories_id,
                ));
          
         tep_db_query($single_mission_sql); 
         }
        }
       //删除不存在关系的mission 
        tep_db_query($sql_del_no_categories_mission);

        $tmpVar =  tep_db_fetch_array($exist_res);
        $currentMissionId = $tmpVar['mission_id'];
        unset($tmpVar);
        //更新mission 姓名
        $sql_get_mission_with_categories= 'select c2m.categories_id from '.TABLE_CATEGORIES_TO_MISSION.' c2m where c2m.mission_id = '.$currentMissionId;
        $tmpArray = array();
        $tmpRes = tep_db_query($sql_get_mission_with_categories);
        while($tmpResultArray= tep_db_fetch_array($tmpRes)){
          $tmpArray[] = $tmpResultArray['categories_id'];
        }
        tep_db_query('update '.TABLE_MISSION.' set name = "'.'categoriy_'.join('_',$tmpArray).'" where id='.$currentMissionId);

        unset($tmpArray);
        unset($tmpResultArray);

        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $categories_id));

        }
        break;
      case 'upload_inventory':
        $error = false;
        $categories_id = $_POST['categories_id'];
        $max_inventory = $_POST['max_inventory'];
        $min_inventory = $_POST['min_inventory'];
        if(!(!$max_inventory == !$min_inventory)||
            $max_inventory<$min_inventory){
          $error = true;
        }
        if($error){
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID='
              . $categories_id.'&action=edit_inventory&msg=error'));
        }else{
        $upload_inventory_sql = 'update '.TABLE_CATEGORIES.'
          set 
          max_inventory="'.$max_inventory.'",
          min_inventory="'.$min_inventory.'"
          where categories_id="'.$categories_id.'" 
          and parent_id = "0"';
        tep_db_query($upload_inventory_sql);
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $categories_id));
        }
        break;
      case 'insert_category':
      case 'update_category':
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
          $categories_name_array  = $_POST['categories_name'];
          $categories_meta_text   = $_POST['categories_meta_text'];
          $seo_name               = $_POST['seo_name'];
          $seo_description        = $_POST['seo_description'];
          $categories_header_text = $_POST['categories_header_text'];
          $categories_footer_text = $_POST['categories_footer_text'];
          $text_information       = $_POST['text_information'];
          $meta_keywords          = $_POST['meta_keywords'];
          $meta_description       = $_POST['meta_description'];
          $romaji                 = $_POST['romaji'];

          $language_id = $languages[$i]['id'];
          $sql_data_array = array(
                  'categories_name' => tep_db_prepare_input($categories_name_array[$language_id]),
                  'romaji' => str_replace('_', '-', tep_db_prepare_input($romaji[$language_id])),
                  'categories_meta_text' => tep_db_prepare_input($categories_meta_text[$language_id]),
                  'seo_name' => tep_db_prepare_input($seo_name[$language_id]),
                  'seo_description' => tep_db_prepare_input($seo_description[$language_id]),
                  'categories_header_text' => tep_db_prepare_input($categories_header_text[$language_id]),
                  'categories_footer_text' => tep_db_prepare_input($categories_footer_text[$language_id]),
                  'text_information' => tep_db_prepare_input($text_information[$language_id]),
                  'meta_keywords' => tep_db_prepare_input($meta_keywords[$language_id]),
                  'meta_description' => tep_db_prepare_input($meta_description[$language_id]),
                );

          if ($_GET['action'] == 'insert_category' || ($_GET['action'] == 'update_category' && !tep_categories_description_exist($categories_id, $language_id, $site_id))) {
            $insert_sql_data = array('categories_id' => $categories_id,
                                     'language_id'   => $languages[$i]['id'],
                                     'site_id'       => $site_id
                                     );
            
            if(!tep_check_romaji($sql_data_array['romaji'])){
              $messageStack->add_session(TEXT_ROMAJI_ERROR, 'error');
              tep_redirect(tep_href_link(FILENAME_CATEGORIES));
            }
            if (tep_db_num_rows(tep_db_query("select * from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id=cd.categories_id and c.parent_id='".$current_category_id."' and cd.romaji='".$sql_data_array['romaji']."' and cd.site_id='".$site_id."'"))) {
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_CATEGORIES));
            }
            
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
        if(!tep_check_romaji($sql_data_array['romaji'])){
          $messageStack->add_session(TEXT_ROMAJI_ERROR, 'error');
          tep_redirect(tep_href_link(FILENAME_CATEGORIES));
        }
        if (tep_db_num_rows(tep_db_query("select * from ".TABLE_CATEGORIES_DESCRIPTION." cd,".TABLE_CATEGORIES." c where cd.categories_id=c.categories_id and c.parent_id='".$current_category_id."' and cd.romaji='".$sql_data_array['romaji']."' and cd.site_id='".$site_id."' and c.categories_id!='".$categories_id."'"))) {
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_CATEGORIES));
        }
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
        $image_directory = tep_get_local_path(tep_get_upload_dir($site_id) . 'categories/');

        if (is_uploaded_file($categories_image['tmp_name'])) {
          tep_db_query("update " . TABLE_CATEGORIES . " set categories_image = '" . $categories_image['name'] . "' where categories_id = '" . tep_db_input($categories_id) . "'");
          tep_copy_uploaded_file($categories_image, $image_directory);
        }

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('categories');
          tep_reset_cache_block('also_purchased');
        }

        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $categories_id));
        break;
      case 'delete_product_description_confirm':
        if ($_GET['pID'] && $_GET['site_id']) {
          tep_db_query("delete from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$_GET['pID']."' && site_id = '".(int)$_GET['site_id']."'");
        }
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID='. (int)$_GET['pID']));
        break;
      case 'delete_category_description_confirm':
        if ($_GET['cID'] && $_GET['site_id']) {
          tep_db_query("delete from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$_GET['cID']."' && site_id = '".(int)$_GET['site_id']."'");
        }
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID='. (int)$_GET['cID']));
        break;
      case 'delete_category_confirm':
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

        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath));
        break;
      case 'delete_product_confirm':
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

        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath));
        break;
      case 'move_category_confirm':
        if ( ($_POST['categories_id']) && ($_POST['categories_id'] != $_POST['move_to_category_id']) ) {
          $categories_id = tep_db_prepare_input($_POST['categories_id']);
          $new_parent_id = tep_db_prepare_input($_POST['move_to_category_id']);
          tep_db_query("update " . TABLE_CATEGORIES . " set parent_id = '" . tep_db_input($new_parent_id) . "', last_modified = now() where categories_id = '" . tep_db_input($categories_id) . "'");

          if (USE_CACHE == 'true') {
            tep_reset_cache_block('categories');
            tep_reset_cache_block('also_purchased');
          }
        }

        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $new_parent_id . '&cID=' . $categories_id));
        break;
      case 'move_product_confirm':
        $products_id = tep_db_prepare_input($_POST['products_id']);
        $new_parent_id = tep_db_prepare_input($_POST['move_to_category_id']);

        $duplicate_check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . tep_db_input($products_id) . "' and categories_id = '" . tep_db_input($new_parent_id) . "'");
        $duplicate_check = tep_db_fetch_array($duplicate_check_query);
        if ($duplicate_check['total'] < 1) tep_db_query("update " . TABLE_PRODUCTS_TO_CATEGORIES . " set categories_id = '" . tep_db_input($new_parent_id) . "' where products_id = '" . tep_db_input($products_id) . "' and categories_id = '" . $current_category_id . "'");

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('categories');
          tep_reset_cache_block('also_purchased');
        }

        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $new_parent_id . '&pID=' . $products_id));
        break;
      case 'insert_product':
      case 'update_product':
        
        $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
        
        if ($_GET['action'] == 'insert_product') {

          if (trim($_POST['romaji']) == '') {
            $messageStack->add_session(TEXT_ROMAJI_NOT_NULL, 'error');
            tep_redirect(tep_href_link(FILENAME_CATEGORIES));
          }
          if(!tep_check_romaji($_POST['romaji'])){
            $messageStack->add_session(TEXT_ROMAJI_ERROR, 'error');
            tep_redirect(tep_href_link(FILENAME_CATEGORIES));
          }
          if (isset($_GET['cPath'])) {
            $ca_arr = explode('_', $_GET['cPath']); 
            $belong_ca = $ca_arr[count($ca_arr)-1];
            $exist_ro_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c where pd.products_id = p2c.products_id and pd.site_id = '".$site_id."' and pd.romaji = '".$_POST['romaji']."' and p2c.categories_id = '".$belong_ca."'"); 
            if (tep_db_num_rows($exist_ro_query)) {
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_CATEGORIES));
            }
          } else {
            if (tep_db_num_rows(tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where romaji = '".$_POST['romaji']."' and site_id = '".$site_id."'"))) {
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_CATEGORIES));
            }
          }
        } else if ($_GET['action'] == 'update_product') {
          if (trim($_POST['romaji']) == '') {
            $messageStack->add_session(TEXT_ROMAJI_NOT_NULL, 'error');
            tep_redirect(tep_href_link(FILENAME_CATEGORIES));
          }
          if(!tep_check_romaji($_POST['romaji'])){
            $messageStack->add_session(TEXT_ROMAJI_ERROR, 'error');
            tep_redirect(tep_href_link(FILENAME_CATEGORIES));
          }
          if (isset($_GET['cPath'])) {
            $ca_arr = explode('_', $_GET['cPath']); 
            $belong_ca = $ca_arr[count($ca_arr)-1];
            $exist_ro_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c where pd.products_id = p2c.products_id and pd.site_id = '".$site_id."' and pd.romaji = '".$_POST['romaji']."' and p2c.categories_id = '".$belong_ca."' and pd.products_id != '".$_GET['pID']."'"); 
            if (tep_db_num_rows($exist_ro_query)) {
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_CATEGORIES));
            }
          } else {
            if (tep_db_num_rows(tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where romaji = '".$_POST['romaji']."' and site_id = '".$site_id."' and products_id != '".$_GET['pID']."'"))) {
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_CATEGORIES));
            }
          }
        }
        
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

      $products_attention_1 = tep_db_prepare_input($_POST['products_jan']);
      $products_attention_2 = tep_db_prepare_input($_POST['products_size']);
      $products_attention_3 = tep_db_prepare_input($_POST['products_naiyou']);
      $products_attention_4 = tep_db_prepare_input($_POST['products_zaishitu']);
      $products_attention_5 = tep_db_prepare_input($_POST['products_attention_5']);
      $sql_data_array = array('products_quantity' => tep_db_prepare_input($_POST['products_quantity']),
                                  'products_model' => tep_db_prepare_input($_POST['products_model']),
                                  //'products_image' => (($_POST['products_image'] == 'none') ? '' : tep_db_prepare_input($_POST['products_image'])),
                                  //'products_image2' => (($_POST['products_image2'] == 'none') ? '' : tep_db_prepare_input($_POST['products_image2'])),
                                  //'products_image3' => (($_POST['products_image3'] == 'none') ? '' : tep_db_prepare_input($_POST['products_image3'])),
                                  'products_attention_1' => $products_attention_1,
                                  'products_attention_2' => $products_attention_2,
                                  'products_attention_3' => $products_attention_3,
                                  'products_attention_4' => $products_attention_4,
                                  'products_attention_5' => $products_attention_5,
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
                                  'sort_order' => tep_db_prepare_input($_POST['sort_order']),
                                  'relate_products_id' => tep_db_prepare_input($_POST['relate_products_id']),
                                  'products_small_sum' => tep_db_prepare_input($_POST['products_small_sum']));
          


          if ($_POST['products_image']) {
            $sql_data_array['products_image'] = (($_POST['products_image'] == 'none') ? '' : tep_db_prepare_input($_POST['products_image']));
          }
          if ($_POST['products_image2']) {
            $sql_data_array['products_image2'] = (($_POST['products_image2'] == 'none') ? '' : tep_db_prepare_input($_POST['products_image2']));
          }
          if ($_POST['products_image3']) {
            $sql_data_array['products_image3'] = (($_POST['products_image3'] == 'none') ? '' : tep_db_prepare_input($_POST['products_image3']));
          }


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
          
        if ($_POST['relate_products_id'] && $products_id) {
          tep_db_query("update ".TABLE_PRODUCTS." set relate_products_id='".$products_id."' where products_id='".$_POST['relate_products_id']."'");
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
            $sql_data_array = array(
                'products_name'        => tep_db_prepare_input($_POST['products_name'][$language_id]),
                'romaji' => tep_db_prepare_input(str_replace('_', '-', $_POST['romaji'])),
                'products_description' => $des,
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
      
          if (USE_CACHE == 'true') {
            tep_reset_cache_block('categories');
            tep_reset_cache_block('also_purchased');
          }

          tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&page='.$_GET['page'].'&pID=' . $products_id));
        }
        break;
      case 'copy_to_confirm':
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
                option_type,
                products_attention_1, 
                products_attention_2, 
                products_attention_3, 
                products_attention_4,
                products_attention_5
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
              '" . addslashes($description['products_attention_1']) . "', 
              '" . addslashes($description['products_attention_2']) . "', 
              '" . addslashes($description['products_attention_3']) . "', 
              '" . addslashes($description['products_attention_4']) . "', 
              '" . addslashes($description['products_attention_5']) . "'
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
                  products_url, 
                  products_viewed,
                  site_id,
                  romaji
                ) values (
                  '" . $dup_products_id . "', 
                  '" . $description['language_id'] . "', 
                  '" . addslashes($description['products_name']) . "', 
                  '" . addslashes($description['products_description']) . "', 
                  '" . $description['products_url'] . "', 
                  '0',
                  '" . $description['site_id'] . "', 
                  '" . $description['romaji']."'
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

        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $categories_id . '&pID=' . $products_id));
        break;
    }
  }

// check if the catalog image directory exists
  if (file_exists(tep_get_upload_root())) {
    if (!is_writeable(tep_get_upload_root())) $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
  } else {
    $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
  }
  
  //商品画像削除
        if (isset($_GET['mode']) && $_GET['mode'] == 'p_delete') {
          //$image_location  = DIR_FS_DOCUMENT_ROOT . DIR_WS_CATALOG_IMAGES . $_GET['file'];//元画像
          //$image_location2 = DIR_FS_DOCUMENT_ROOT . DIR_WS_CATALOG_IMAGES .'imagecache3/'. $_GET['file'];//サムネイル画像
          $image_location  = tep_get_upload_dir($site_id). 'products/' . $_GET['file'];//元画像
          $image_location2 = tep_get_upload_dir($site_id) .'imagecache3/'. $_GET['file'];//サムネイル画像
          $delete_image = $_GET['cl'];
           if (file_exists($image_location)) @unlink($image_location);
           if (file_exists($image_location2)) @unlink($image_location2);
             tep_db_query("update  " . TABLE_PRODUCTS . " set ".$delete_image." = '' where products_id  = '" . $_GET['pID'] . "'");
             tep_redirect(tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&action='.$_GET['action']));
             $messageStack->add('画像削除に成功しました', 'success');
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript">
  function relate_products1(cid,rid){
    $.ajax({
      dataType: 'text',
      url: 'categories.php?action=get_products&cid='+cid+'&rid='+rid,
      success: function(text) {
        $('#relate_products').html(text);
      }
    });
  }
  function confirmg(question,url) {
    var x = confirm(question);
    if (x) {
      window.location = url;
    }
  }
  
function mess(){
  //if(document.getElementById('pp').value == "" || document.getElementById('pp').value < 1){
    //alert("価格情報を入力して下さい");
  //document.getElementById('pp').focus();
  //return false;
  //}
}
function calculate_price(){
  if (parseInt($('#pp').val()) != 0) {
    $('#a_1').html(Math.ceil(5000/$('#pp').val()));
    if ($('#a_1').html()%10 != 0) {
    if ($('#a_1').html()%10 < 5) {
      $('#a_2').html(Math.floor($('#a_1').html()/10)*10+5);
    } else {
      $('#a_2').html('');
    }
    $('#a_3').html(Math.floor($('#a_1').html()/10)*10+10);
    } else {
      $('#a_2').html('');
      $('#a_3').html('');
    }

    $('#b_1').html(Math.ceil(10000/$('#pp').val()));
    if ($('#b_1').html()%10 != 0) {
    if ($('#b_1').html()%10 < 5) {
      $('#b_2').html(Math.floor($('#b_1').html()/10)*10+5);
    } else {
      $('#b_2').html('');
    }
    $('#b_3').html(Math.floor($('#b_1').html()/10)*10+10);
    } else {
      $('#b_2').html('');
      $('#b_3').html('');
    }
  } else {
    $('#a_1').html('');
    $('#a_2').html('');
    $('#a_3').html('');
    $('#b_1').html('');
    $('#b_2').html('');
    $('#b_3').html('');
  }
}

function change_qt(ele){
  qt = ele.innerHTML;
  if (qt) {
    $('#qt').val(qt);
  }
}
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </table></td>
    <!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <?php
  if (isset($_GET['action']) && $_GET['action'] == 'new_product') {
    if ( isset($_GET['pID']) && ($_GET['pID']) && (!$_POST) ) {
      $site_id = isset($_GET['site_id']) ?$_GET['site_id']:0;
      $product_query = tep_db_query("
          select pd.products_name, 
                 pd.products_description, 
                 pd.products_url, 
                 pd.romaji, 
                 p.products_id,
                 p.option_type, 
                 p.products_quantity, 
                 p.products_model, 
                 p.products_image,
                 p.products_image2,
                 p.products_image3, 
                 p.products_price, 
                 p.products_price_offset,
                 p.products_weight, 
                 p.products_date_added, 
                 p.products_last_modified, 
                 date_format(p.products_date_available, '%Y-%m-%d') as products_date_available, 
                 p.products_status, 
                 p.products_tax_class_id, 
                 p.manufacturers_id, 
                 p.products_bflag, 
                 p.products_cflag, 
                 p.relate_products_id,
                 p.sort_order,
                 p.products_small_sum 
          from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
          where p.products_id = '" . $_GET['pID'] . "' 
            and p.products_id = pd.products_id 
            and pd.language_id = '" . $languages_id . "' 
            and pd.site_id = '".(tep_products_description_exist($_GET['pID'], $site_id, $languages_id)?$site_id:0)."'");
      $product = tep_db_fetch_array($product_query);
      $pInfo = new objectInfo($product);
    } elseif ($_POST) {
      //print_r($_POST);
      $pInfo = new objectInfo($_POST);
      $products_name = $_POST['products_name'];
      $products_description = $_POST['products_description'];

      $products_url = $_POST['products_url'];
      $site_id = isset($_POST['site_id']) ?$_POST['site_id']:0;
    } else {
      $pInfo = new objectInfo(array());
      $site_id = isset($_GET['site_id']) ?$_GET['site_id']:0;
    }

    $manufacturers_array = array(array('id' => '', 'text' => TEXT_NONE));
    $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
      $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
                                     'text' => $manufacturers['manufacturers_name']);
    }

    $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $tax_class_query = tep_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
    while ($tax_class = tep_db_fetch_array($tax_class_query)) {
      $tax_class_array[] = array('id' => $tax_class['tax_class_id'],
                                 'text' => $tax_class['tax_class_title']);
    }

    $languages = tep_get_languages();
    
  if(isset($pInfo->products_cflag)){
  switch ($pInfo->products_cflag) {
      case '1': $in_cflag = false; $out_cflag = true; break;
      case '0':
      default: $in_cflag = true; $out_cflag = false;
    }
  } else {
      $in_cflag = true; $out_cflag = false;
  }

  if(isset($pInfo->products_bflag)){
  switch ($pInfo->products_bflag) {
      case '1': $in_bflag = false; $out_bflag = true; break;
      case '0':
      default: $in_bflag = true; $out_bflag = false;
    }
  } else {
      $in_bflag = true; $out_bflag = false;
  }
  
  //商品説明を分割
  if(isset($pInfo->products_id)){
    $des_query = tep_db_query("
      select p.products_attention_1,
             p.products_attention_2,
             p.products_attention_3,
             p.products_attention_4,
             p.products_attention_5,
             pd.products_description 
      from products_description pd,products p
      where language_id = '4'
        and p.products_id = pd.products_id 
        and p.products_id = '".$pInfo->products_id."' 
        and site_id ='".(tep_products_description_exist($pInfo->products_id,$site_id,4)?$site_id:0)."'"); 
    $des_result = tep_db_fetch_array($des_query);
  }
?>
        <link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
        <script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
        <script language="javascript">
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo isset($pInfo->products_date_available)?$pInfo->products_date_available:''; ?>",scBTNMODE_CUSTOMBLUE);
</script>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading"><?php echo sprintf(TEXT_NEW_PRODUCT, tep_output_generated_category_path($current_category_id)); ?></td>
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
              </tr>
            </table></td>
        </tr>
        <tr><?php echo tep_draw_form('new_product', FILENAME_CATEGORIES, 'cPath=' . $cPath . '&page='.$_GET['page'].'&pID=' . (isset($_GET['pID'])?$_GET['pID']:'') . '&action=new_product_preview', 'post', 'enctype="multipart/form-data" onSubmit="return mess();"'); ?>
        <input type="hidden" name="site_id" value="<?php echo $site_id;?>">
          <td><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main" valign="top"><?php echo $site_id?('<br><b>'.tep_get_site_name_by_id($site_id).'</b>'):'';?></td>
                <td class="main" align="right"><?php echo  tep_image_submit('button_preview.gif', IMAGE_PREVIEW) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&page='.$_GET['page'].'&pID=' . (isset($_GET['pID'])?$_GET['pID']:'')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
              </tr>
              <tr>
                <td colspan="2"><fieldset>
                  <legend style="color:#FF0000 ">商品の基本情報</legend>
                  <table>
          <tr>
                      <td class="main"><?php echo TEXT_PRODUCTS_STATUS; ?></td>
                      <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('products_status', '1', $pInfo->products_status == '1' or !isset($pInfo->products_status)) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . tep_draw_radio_field('products_status', '2', $pInfo->products_status == '2') . '&nbsp;' . '過去ログ'. '&nbsp;' . tep_draw_radio_field('products_status', '0', $pInfo->products_status == '0') . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?></td>
                      <td class="main">&nbsp;</td>
          </tr>
  
          <tr>
                      <td class="main"><?php echo TEXT_PRODUCTS_BUY_AND_SELL; ?></td>
                      <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('products_bflag', '0', $in_bflag, '', ($site_id?'onclick="return false;"':'')) . '&nbsp;' . TEXT_PRODUCT_USUALLY . '&nbsp;' . tep_draw_radio_field('products_bflag', '1', $out_bflag, '', ($site_id?'onclick="return false;"':'')) . '&nbsp;' . TEXT_PRODUCT_PURCHASE; ?></td>
                      <td class="main">&nbsp;</td>
          </tr>
          <tr>
                      <td class="main"><?php echo TEXT_PRODUCTS_CHARACTER; ?></td>
                      <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('products_cflag', '0', $in_cflag, '', ($site_id?'onclick="return false;"':'')) . '&nbsp;' . TEXT_PRODUCT_NOT_INDISPENSABILITY . '&nbsp;' . tep_draw_radio_field('products_cflag', '1', $out_cflag, '', ($site_id?'onclick="return false;"':'')) . '&nbsp;' . TEXT_PRODUCT_INDISPENSABILITY; ?></td>
                      <td class="main">&nbsp;</td>
          </tr>   
            

                  <tr>
                      <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                    </tr>
                  <tr>
                      <td class="main"><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?><br>
                        <small>(YYYY-MM-DD)</small></td>
                      <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?>
                        <script language="javascript">dateAvailable.writeControl(); dateAvailable.dateFormat="yyyy-MM-dd";</script></td>
                      <td class="main">&nbsp;</td>
                  </tr>
                  <tr>
                      <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                    </tr>
                  <tr>
                      <td class="main"><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></td>
                      <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, isset($pInfo->manufacturers_id)?$pInfo->manufacturers_id:'', ($site_id ? 'class="readonly"  onfocus="this.lastIndex=this.selectedIndex" onchange="this.selectedIndex=this.lastIndex"' : '')); ?></td>
                      <td class="main">&nbsp;</td>
                  </tr>
          <tr>
                      <td class="main">オススメ商品並び順:</td>
                      <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('sort_order', isset($pInfo->sort_order)?$pInfo->sort_order:'','id="op"' . ($site_id ? 'class="readonly" readonly' : '')); ?></td>
                      <td class="main">&nbsp;</td>
          </tr>
                  <tr>
                      <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                    </tr>
                  <tr>
                      <td class="main"><?php echo TEXT_PRODUCTS_OPTION; ?></td>
                      <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('option_type', tep_get_option_array(), isset($pInfo->option_type)?$pInfo->option_type:'', ($site_id ? 'class="readonly"  onfocus="this.lastIndex=this.selectedIndex" onchange="this.selectedIndex=this.lastIndex"' : '')); ?></td>
                      <td class="main">&nbsp;</td>
                  </tr>
              <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <?php
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
?>
              <tr>
                <td class="main"><?php if ($i == 0) echo TEXT_PRODUCTS_NAME; ?></td>
                <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (isset($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : (isset($pInfo->products_id)?tep_get_products_name($pInfo->products_id, $languages[$i]['id'], $site_id, true):''))); ?></td>
                <td class="fieldRequired">検索キー</td>
              </tr>
              <?php
    }
?>
              <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_ROMAJI;?></td> 
                <td class="main">
                <?php
                echo tep_draw_input_field('romaji', $pInfo->romaji); 
                ?>
                </td> 
              </tr>
              <tr>
                <td class="main">関連付け商品:</td>
                <td class="main" colspan="2">
  <?php echo tep_draw_separator('pixel_trans.gif', '24', '15');?>
  <?php echo tep_draw_pull_down_menu('relate_categories', tep_get_category_tree('&npsp;'), ($pInfo->relate_products_id?tep_get_products_parent_id($pInfo->relate_products_id):$current_category_id), ($site_id ? 'class="readonly"  onfocus="this.lastIndex=this.selectedIndex" onchange="this.selectedIndex=this.lastIndex"' : '').' onchange="relate_products1(this.options[this.selectedIndex].value, \''.$pInfo->relate_products_id.'\')"');?>
  <span id="relate_products">
  <?php echo tep_draw_pull_down_menu('relate_products', array_merge(array(array('id' => '0','text' => '関連付けなし')),tep_get_products_tree($pInfo->relate_products_id?tep_get_products_parent_id($pInfo->relate_products_id):$current_category_id)),$pInfo->relate_products_id,($site_id ? 'class="readonly"  onfocus="this.lastIndex=this.selectedIndex" onchange="this.selectedIndex=this.lastIndex"' : '').'onchange="$(\'#relate_products_id\').val(this.options[this.selectedIndex].value)"');?>
  </span>
  <input type="hidden" name="relate_products_id" id="relate_products_id" value="<?php echo $pInfo->relate_products_id;?>">
  </td>
              </tr>
  
  
  
              <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
          <input type="hidden" name="products_price_def" value="">
                    <tr>
                      <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                    </tr>
          <tr bgcolor="#CCCCCC">
                      <td class="main"><?php echo '<font color="blue"><b>' . TEXT_PRODUCTS_PRICE . '</b></font>'; ?></td>
                      <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_price', isset($pInfo->products_price)?$pInfo->products_price:'','id="pp"' . ($site_id ? 'class="readonly" readonly' : '')); ?></td>
                    </tr>
                    <tr>
                      <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                    </tr>
                    <tr bgcolor="#CCCCCC">
                      <td class="main"><?php echo '<font color="blue"><b>増減の値:</b></font>'; ?></td>
                      <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_price_offset', $pInfo->products_price_offset, ($site_id ? 'class="readonly" readonly' : '')); ?></td>
                    </tr>
                    <tr>
                      <td class="main">&nbsp;</td>
            <td colspan="2" class="smallText"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;# 割り引くパーセンテージを "増減の値" 欄に入力することができます。例: 20%'; ?><br>
            <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;# 新しい価格を入力する場合には、新しい価格を入力してください。例: 1980'; ?><br>
            <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;# 登録されている情報を消去する場合は、値を空白にしてください。'; ?></td>
                    </tr>
                    <tr>
                      <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                    </tr>
          <tr>
            <td class="main" valign="top"><?php echo TEXT_PRODUCTS_SMALL_SUM; ?></td>
            <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_textarea_field('products_small_sum', 'soft', '70', '5', isset($pInfo->products_small_sum)?$pInfo->products_small_sum:'', ($site_id ? 'class="readonly" readonly' : '')); ?></td>
          </tr>
          <tr>
                      <td class="main">&nbsp;</td>
            <td colspan="2" class="smallText">
            <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;(例１：割増)商品単価を100円とした場合'; ?><br>
            <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp; 1:20,50:10,100:0'; ?><br>
            <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp; 1個から49個までの加算値は20→商品単価は120円'; ?><br>
            <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp; 50個～99個までの加算値は10→商品単価は110円'; ?><br>
            <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp; 割引の場合は、加算値を-20の様なマイナス値にして下さい。'; ?><br>
            <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp; <b>割引は未検証なので入力しないこと！</b>'; ?></td>
                    </tr>
          <tr>
                      <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                    </tr>
            <!--
                    <tr>
                      <td class="main"><?php echo TEXT_PRODUCTS_TAX_CLASS; ?></td>
                      <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_pull_down_menu('products_tax_class_id', $tax_class_array, isset($pInfo->products_tax_class_id)?$pInfo->products_tax_class_id:'', ($site_id ? 'class="readonly"  onfocus="this.lastIndex=this.selectedIndex" onchange="this.selectedIndex=this.lastIndex"' : '')); ?></td>
                    </tr>
          -->
        <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
        <tr bgcolor="#CCCCCC">
                <td class="main"><?php echo '<font color="blue"><b>' . TEXT_PRODUCTS_QUANTITY . '</b></font>'; ?></td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_quantity', isset($pInfo->products_quantity)?$pInfo->products_quantity:'', ($site_id ? 'class="readonly" readonly' : '')); ?></td>
              </tr>
        <tr>
          <td>&nbsp;</td>
                <td class="smallText" colspan="2">在庫計算する場合は入力してください。在庫を計算する場合は　基本設定→在庫管理　を設定してください。</td>
        </tr>
              <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_MODEL; ?></td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_model', isset($pInfo->products_model)?$pInfo->products_model:'', ($site_id ? 'class="readonly" readonly' : '')); ?></td>
                <td class="fieldRequired">検索キー</td>
              </tr>

          <tr>
                      <td class="main">項目１</td>
                      <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_jan', isset($des_result['products_attention_1'])?$des_result['products_attention_1']:'', ($site_id ? 'class="readonly" readonly' : '')); ?><br>
                      <span class="smallText">項目名とデータは「//」スラッシュ2本で区切ってください。例）サイズ//H1000　W560</span></td>
                    </tr>
          <tr>
                      <td class="main">項目２</td>
                      <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_size', isset($des_result['products_attention_2'])?$des_result['products_attention_2']:'', ($site_id ? 'class="readonly" readonly' : '')); ?></td>
                    </tr>
          <tr>
                      <td class="main">項目３</td>
                      <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_naiyou', isset($des_result['products_attention_3'])?$des_result['products_attention_3']:'', ($site_id ? 'class="readonly" readonly' : '')); ?></td>
                    </tr>
          <tr>
                      <td class="main">項目４</td>
                      <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_zaishitu', isset($des_result['products_attention_4'])?$des_result['products_attention_4']:'', ($site_id ? 'class="readonly" readonly' : '')); ?></td>
          </tr>
              <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
        <tr>
                <td class="main" valign="top">キャラクタ名</td>
                <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_textarea_field('products_attention_5', 'soft', '70', '15', isset($des_result['products_attention_5'])?$des_result['products_attention_5']:'', ($site_id ? 'class="readonly" readonly' : '')); ?></td>
              </tr>
          </table>
                  </fieldset></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>

              <tr>
                <td colspan="2"><fieldset>
                  <legend style="color:#FF0000">商品の説明文/オプション登録</legend>
                  <table>

<?php
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
?>
              <tr>
                <td class="main" valign="top"><?php if ($i == 0) echo TEXT_PRODUCTS_DESCRIPTION; ?></td>
                <td class="main"><table border="0" cellspacing="0" cellpadding="0">
                    <tr>
                      <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                      <td class="main"><?php echo tep_draw_textarea_field('products_description[' . $languages[$i]['id'] . ']', 'soft', '70', '15', (isset($products_description[$languages[$i]['id']]) ? stripslashes($products_description[$languages[$i]['id']]) : (isset($pInfo->products_id)?tep_get_products_description($pInfo->products_id, $languages[$i]['id'], $site_id, true):''))); ?></td>
                    </tr>
                  </table>
                  HTMLによる入力可<br>
                  <span class="fieldRequired">検索キー</span></td>
              </tr>
<?php
    }
?>
              <!-- options// -->
              <?php

      //オプションデータ取得
      if(isset($_GET['pID']) && $_GET['pID']) {
        $options_query = tep_db_query("select * from products_attributes where products_id = '".(int)$_GET['pID']."' order by products_attributes_id");
      if(tep_db_num_rows($options_query)) {
        $options_array = '';
        while($options = tep_db_fetch_array($options_query)) {
          $options_array .= tep_get_add_options_name($options['options_id']) . ',' . tep_get_add_options_value($options['options_values_id']) . ',' . (int)$options['options_values_price'] . ',' . $options['price_prefix'] . ',' . $options['products_at_quantity'] . "\n";
        }
        } else {
        $options_array = '';
      }
      } else {
        $options_array = '';
      }
      ?>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main" valign="top">オプション登録</td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_textarea_field('products_options', 'soft', '70', '15', $options_array, ($site_id ? 'class="readonly" readonly' : '')); ?></td>
              </tr>
              <tr>
                <td></td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?>「オプション名,オプション値,オプション価格,接頭辞,在庫数」の順で入力（区切りは「,」・改行で複数同時登録可）<br>
                  <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?>例）<br>
                  <table border="0" cellspacing="0" cellpadding="3">
                    <tr>
                      <td class="main">                  言語,日本語,0,+ <br>
                  言語,中国語,400,+ <br>
                  言語,韓国語,100,-</td>
                      <td width="50" align="center" class="main">→</td>
            <td class="main">言語:
                        <select name="select">
                          <option selected>日本語</option>
                          <option>中国語(+400円)</option>
                          <option>韓国語(-100円)</option>
                          </select></td>
                    </tr>
                  </table>
</td>
              </tr>
              <!-- //options -->
              <tr>
                  </table>
                  </fieldset></td>
              </tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
<?php if (!$site_id) {?>
              <tr>
                <td colspan="2"><fieldset>
                  <legend style="color:#009900 ">商品の画像</legend>
                  <table>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_IMAGE; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('products_image') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . (isset($pInfo->products_image)?$pInfo->products_image:'') . tep_draw_hidden_field('products_previous_image', isset($pInfo->products_image)?$pInfo->products_image:''); ?>
      <?php
      if(isset($pInfo->products_image) && tep_not_null($pInfo->products_image)){
       echo '<br>'.tep_info_image('products/'.$pInfo->products_image,$pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, $site_id).'<br>'."\n";
      ?>
      <a href="javascript:confirmg('この画像を削除しますか？','<?php echo tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&cl=products_image&action='.$_GET['action'].'&file='.(isset($pInfo->products_image)?$pInfo->products_image:'').'&mode=p_delete&site_id='.$site_id) ; ?>');" style="color:#0000FF;">この画像を削除する</a>
      <?php } ?>
      </td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_IMAGE; ?>2</td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('products_image2') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . (isset($pInfo->products_image2)?$pInfo->products_image2:'') . tep_draw_hidden_field('products_previous_image2', isset($pInfo->products_image2)?$pInfo->products_image2:''); ?>
      <?php
      if(isset($pInfo->products_image2) && tep_not_null($pInfo->products_image2)){
       echo '<br>'.tep_info_image('products/'.$pInfo->products_image2,$pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, $site_id).'<br>'."\n";
      ?>
      <a href="javascript:confirmg('この画像を削除しますか？','<?php echo tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&cl=products_image2&action='.$_GET['action'].'&file='.$pInfo->products_image2.'&mode=p_delete') ; ?>');" style="color:#0000FF;">この画像を削除する</a>
      <?php } ?>
      </td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_IMAGE; ?>3</td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('products_image3') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . (isset($pInfo->products_image3)?$pInfo->products_image3:'') . tep_draw_hidden_field('products_previous_image3', isset($pInfo->products_image3)?$pInfo->products_image3:''); ?>
      <?php
      if(isset($pInfo->products_image3) && tep_not_null($pInfo->products_image3)){
       echo '<br>'.tep_info_image('products/'.$pInfo->products_image3,$pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT , $site_id).'<br>'."\n";
      ?>
      <a href="javascript:confirmg('この画像を削除しますか？','<?php echo tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&cl=products_image3&action='.$_GET['action'].'&file='.$pInfo->products_image3.'&mode=p_delete') ; ?>');" style="color:#0000FF;">この画像を削除する</a>
      <?php } ?>
      </td>
          </tr>
                  </table>
          <?php
           if(COLOR_SEARCH_BOX_TF == "true" ){
           ?>
          <!-- カラー別画像// -->
          <hr size="1">
          <legend style="color:#009900 ">カラー別画像</legend>
          <table border="0" cellpadding="1" cellspacing="5">
          <tr>
            
          <?php
            $color_query = tep_db_query("select * from ".TABLE_COLOR." order by color_name");
            $cnt=0;
            while($color = tep_db_fetch_array($color_query)) {
              $ctp_query = tep_db_query("select color_image, color_to_products_name from ".TABLE_COLOR_TO_PRODUCTS." where color_id = '".$color['color_id']."' and products_id = '".$pInfo->products_id."'");
            $ctp = tep_db_fetch_array($ctp_query);
            echo '<td bgcolor="'.$color['color_tag'].'">';
            echo '<table border="0" cellpadding="0" cellspacing="5" width="100%" bgcolor="#FFFFFF">';
            echo '<tr>';
            echo '<td class="main" width="33%">テキスト：&nbsp;'.tep_draw_input_field('colorname_'.$color['color_id'], $ctp['color_to_products_name']).'<br>'.$color['color_name'].': '.tep_draw_file_field('image_'.$color['color_id']).'<br>&nbsp;&nbsp;&nbsp;' . $ctp['color_image'].tep_draw_hidden_field('image_pre_'.$color['color_id'], $ctp['color_image']).'</td>';
            echo '</tr>';
            echo '</table>';
            echo '</td>';
            $cnt++;
            if($cnt>2) {
              $cnt=0;
            echo '</tr><tr>';
            }
            }
          ?>
              
          </tr>
          </table>
          <!-- //カラー別画像 -->
         <?php
         }
         ?>
                  </fieldset></td>
              </tr>
<?php }?>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2">
                <table>
                  <tr>
                    <td><?php echo TEXT_PRODUCTS_TAGS;?></td> 
                  </tr>
                  <tr>
                    <td<?php if($site_id){echo ' class="readonly"';}?>> 
                    <?php
                      //show tags 
                      $checked_tags = array();
                      if (isset($_GET['pID']) && $_GET['pID']) {
                        $c_query = tep_db_query("select * from ".TABLE_PRODUCTS_TO_TAGS." where products_id = '".$_GET['pID']."'"); 
                        while ($ptt = tep_db_fetch_array($c_query)) {
                          $checked_tags[$ptt['tags_id']] = $ptt['tags_id']; 
                        }
                      }
                      $t_query = tep_db_query("select * from ".TABLE_TAGS); 
                      while ($tag = tep_db_fetch_array($t_query)) {
                      ?>
                        <input type='checkbox' name='tags[]' value='<?php echo $tag['tags_id'];?>' 
                      <?php
                        if ($_GET['pID']) {
                          if (isset($checked_tags[$tag['tags_id']])) {
                            echo 'checked'; 
                          }
                        } else if ($tag['tags_checked']) {
                          echo 'checked'; 
                        } else if (isset($_POST['tags']) && in_array($tag['tags_id'], $_POST['tags'])) {
                          echo 'checked'; 
                        }
                       ?><?php if ($site_id) {echo ' onclick="return false;"';}?>
                       ><?php echo $tag['tags_name'];?> 
                      <?php 
                      }
                    ?>
                    </td> 
                  </tr>
                </table>
                </td>
              </tr>
              <?php
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
?>
              <tr>
                <td class="main"><?php if ($i == 0) echo TEXT_PRODUCTS_URL . '<br><small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>'; ?></td>
                <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('products_url[' . $languages[$i]['id'] . ']', (isset($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) : (isset($pInfo->products_id) ?tep_get_products_url(isset($pInfo->products_id)?$pInfo->products_id:'', $languages[$i]['id'], $site_id):''))); ?></td>
              </tr>
              <?php
    }
?>
        <input type="hidden" name="products_weight" value="">
        <input type="hidden" name="site_id" value="<?php echo $site_id;?>">
            </table></td>
        </tr>

        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="main" align="right"><?php echo  tep_image_submit('button_preview.gif', IMAGE_PREVIEW) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&page='.$_GET['page'].'&pID=' . (isset($_GET['pID'])?$_GET['pID']:'')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
        </tr>


          <?php echo tep_draw_hidden_field('products_date_added', (isset($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d')));?>
          </form>

        <?php
  } elseif (isset($_GET['action']) && $_GET['action'] == 'new_product_preview') {

    if ($_POST) {
      $pInfo = new objectInfo($_POST);
      $products_name = $_POST['products_name'];
      $products_description = $_POST['products_description'];
      $products_url = $_POST['products_url'];
      $site_id = $_POST['site_id'];

// copy image only if modified
      $products_image = tep_get_uploaded_file('products_image');
      $image_directory = tep_get_local_path(tep_get_upload_dir($site_id).'products/');

      if (is_uploaded_file($products_image['tmp_name'])) {
        tep_copy_uploaded_file($products_image, $image_directory);
        $products_image_name = $products_image['name'];
        $products_image_name2 = $products_image2['name'];
        $products_image_name3 = $products_image3['name'];
    } else {
        $products_image_name = $_POST['products_previous_image'];
        $products_image_name2 = $_POST['products_previous_image2'];
        $products_image_name3 = $_POST['products_previous_image3'];
    }
// copy image only if modified -- add ds-style
      $products_image2 = tep_get_uploaded_file('products_image2');
      $products_image3 = tep_get_uploaded_file('products_image3');
      $image_directory = tep_get_local_path(tep_get_upload_dir($site_id).'products/');

      if (is_uploaded_file($products_image2['tmp_name'])) {
        tep_copy_uploaded_file($products_image2, $image_directory);
        $products_image_name2 = $products_image2['name'];
      } else {
        $products_image_name2 = $_POST['products_previous_image2'];
      }
      if (is_uploaded_file($products_image3['tmp_name'])) {
        tep_copy_uploaded_file($products_image3, $image_directory);
        $products_image_name3 = $products_image3['name'];
      } else {
        $products_image_name3 = $_POST['products_previous_image3'];
      }
    
    //========================================
    //color image upload    
    //========================================
    $color_query = tep_db_query("select * from ".TABLE_COLOR." order by color_name");
    $cnt=0;
    $color_image_hidden = '';
    while($color = tep_db_fetch_array($color_query)) {
      $ctp_query = tep_db_query("select color_image from ".TABLE_COLOR_TO_PRODUCTS." where color_id = '".$color['color_id']."' and products_id = '".(isset($pInfo->products_id)?$pInfo->products_id:'')."'");
      $ctp = tep_db_fetch_array($ctp_query);
      $color_image = tep_get_uploaded_file('image_'.$color['color_id']);
      $image_directory = tep_get_local_path(tep_get_upload_dir() . 'colors/');
      if (is_uploaded_file($color_image['tmp_name'])) {
        tep_copy_uploaded_file($color_image, $image_directory);
        //$products_image_name2 = $products_image2['name'];
        $color_image_hidden .= tep_draw_hidden_field('image_'.$color['color_id'], $color_image['name']);
      } else {
        //$products_image_name2 = $_POST['products_previous_image2'];
      }
    }
    //========================================
    
    } else {
      $site_id = isset($_GET['site_id']) ? $_GET['site_id'] : '0';
      $product_query = tep_db_query("
          select p.products_id, 
                 pd.language_id, 
                 pd.products_name, 
                 pd.products_description, 
                 pd.products_url, 
                 pd.romaji, 
                 p.products_quantity, 
                 p.products_model, 
                 p.products_image,
                 p.products_image2,
                 p.products_image3, 
                 p.products_price, 
                 p.products_weight, 
                 p.products_date_added, 
                 p.products_last_modified, 
                 p.products_date_available, 
                 p.products_attention_5,
                 p.products_status, 
                 p.manufacturers_id  
          from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
          where p.products_id = pd.products_id 
            and p.products_id = '" . $_GET['pID'] . "' 
            and pd.site_id='".(isset($_GET['site_id'])?$_GET['site_id']:'0')."'");
      $product = tep_db_fetch_array($product_query);

      $pInfo = new objectInfo($product);
      $products_image_name = $pInfo->products_image;
      $products_image_name2 = $pInfo->products_image2;
      $products_image_name3 = $pInfo->products_image3;
  }

  if (isset($_GET['read']) && $_GET['read'] == 'only' && (!isset($_GET['ordigin']) || !$_GET['origin'])) {
    $form_action = 'simple_update';
  } elseif ($_GET['pID']) {
    $form_action = 'update_product';
  } else {
    $form_action = 'insert_product';
  }

    echo tep_draw_form($form_action, FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $_GET['pID'] . '&page='.$_GET['page'].'&action=' . $form_action, 'post', 'enctype="multipart/form-data" onSubmit="return mess();"');
    echo '<input type="hidden" name="site_id" value="'.strval($site_id).'">';

    echo isset($color_image_hedden) ? $color_image_hidden : '';
  $languages = tep_get_languages();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      if (isset($_GET['read']) && $_GET['read'] == 'only') {
        $pInfo->products_name = tep_get_products_name($pInfo->products_id, $languages[$i]['id']);
        $pInfo->products_description = tep_get_products_description($pInfo->products_id, $languages[$i]['id']);
        $pInfo->products_url = tep_get_products_url($pInfo->products_id, $languages[$i]['id']);
      } else {
        $pInfo->products_name = tep_db_prepare_input($products_name[$languages[$i]['id']]);
        $pInfo->products_description = tep_db_prepare_input($products_description[$languages[$i]['id']]);
        $pInfo->products_url = tep_db_prepare_input($products_url[$languages[$i]['id']]);
      }

    //特価がある場合の処理
    /*
      $special_price_check = tep_get_products_special_price(isset($pInfo->products_id)?$pInfo->products_id:'');
      if (!empty($pInfo->products_special_price)) {
      //％指定の場合は価格を算出
        if (substr($_POST['products_special_price'], -1) == '%') {
          $sprice = ($pInfo->products_price - (($pInfo->products_special_price / 100) * $pInfo->products_price));
        } else {
      $sprice = $pInfo->products_special_price;
    }
    $products_price_preview = '<s>' . $currencies->format($pInfo->products_price) . '</s> <span class="specialPrice">' . $currencies->format($sprice) . '</span>';
    } elseif (!empty($special_price_check)) { //プレビューの表示用
        $products_price_preview = '<s>' . $currencies->format($pInfo->products_price) . '</s> <span class="specialPrice">' . $currencies->format($special_price_check) . '</span>';
    } else {
        $products_price_preview = $currencies->format($pInfo->products_price);
      }*/
      if (tep_get_special_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum)) {
        $products_price_preview = '<s>' . $currencies->format(tep_get_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum)) . '</s> <span class="specialPrice">' . $currencies->format(tep_get_special_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum)) . '</span>';
      } else {
        $products_price_preview = $currencies->format(tep_get_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum));
      }
?>
        <tr>
          <td class="pageHeading">
        <?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $pInfo->products_name . "\n"; ?>
      </td>
    </tr>
    <tr>
      <td>

<!--<hr size="2" noshade>--><b><?php //価格数量変更機能
if (isset($_GET['read']) && $_GET['read'] == 'only' && (!isset($_GET['origin']) || !$_GET['origin'])) {
  echo '<table width="100%"><tr><td align="left">';
  
  echo '<table width="95%" cellpadding="0" cellspacing="0">';
  echo '  <tr><td><hr size="2" noshade></td></tr><tr>';
  echo '  <tr>';
  echo '  <td height="30">';
  echo '価格：&nbsp;' . tep_draw_input_field('products_price', number_format($pInfo->products_price,0,'.',''),'id="pp" size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;"') . '&nbsp;円' . '&nbsp;&nbsp;←&nbsp;' . (int)$pInfo->products_price . '円' . "\n";
  echo '  </td>';
  echo '  </tr><tr><td><hr size="2" noshade></td></tr><tr>';
  echo '  <td height="30">';
  echo '数量：&nbsp;' . tep_draw_input_field('products_quantity', $pInfo->products_quantity,'size="8" id="qt" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;"') . '&nbsp;個' . '&nbsp;&nbsp;←&nbsp;' . $pInfo->products_quantity . '個' . "\n";
  echo '  </td>';
  echo '  </tr><tr><td><hr size="2" noshade></td></tr>';
  echo '</table>';

  echo '当社キャラクター名の入力欄：<br>' . tep_draw_textarea_field('products_attention_5', 'soft', '70', '10', $pInfo->products_attention_5) . '<br>' . "\n";
  echo '</td>';
  if (tep_get_bflag_by_product_id($pInfo->products_id)) { // 如果买取
    echo '<td width="50%" valign="top" align="right">';
    echo '<table width="95%" cellpadding="0" cellspacing="0" border="1">';
    echo '  <tr>';
    echo '  <td height="30"><button  type="button" onclick="calculate_price()">計算する</button></td>';
    echo '  <td>ズバリ</td>';
    echo '  <td>下一桁5</td>';
    echo '  <td>下一桁0</td>';
    echo '  </tr>';
    //echo '  <tr>';
    //echo '  <td colspan="4" valign="top"><hr size="2" noshade style=""></td>';
    //echo '  </tr>';
    echo '  <tr>';
    echo '  <td align="right" height="30">5000</td>';
    echo '  <td align="right"><a href="javascript:void(0)" id="a_1" onclick="change_qt(this)" style="text-decoration : underline;"></a>&nbsp;</td>';
    echo '  <td align="right"><a href="javascript:void(0)" id="a_2" onclick="change_qt(this)" style="text-decoration : underline;"></a>&nbsp;</td>';
    echo '  <td align="right"><a href="javascript:void(0)" id="a_3" onclick="change_qt(this)" style="text-decoration : underline;"></a>&nbsp;</td>';
    echo '  </tr>';
    //echo '  <tr>';
    //echo '  <td colspan="4"><hr size="2" noshade></td>';
    //echo '  </tr>';
    echo '  <tr>';
    echo '  <td align="right" height="30">10000</td>';
    echo '  <td align="right"><a href="javascript:void(0)" id="b_1" onclick="change_qt(this)" style="text-decoration : underline;"></a>&nbsp;</td>';
    echo '  <td align="right"><a href="javascript:void(0)" id="b_2" onclick="change_qt(this)" style="text-decoration : underline;"></a>&nbsp;</td>';
    echo '  <td align="right"><a href="javascript:void(0)" id="b_3" onclick="change_qt(this)" style="text-decoration : underline;"></a>&nbsp;</td>';
    echo '  </tr>';
    echo '</table>';
    echo '</td>';
  }
  echo '</tr></table>';
  echo '<table width="100%" cellspacing="0" cellpadding="5" border="0" class="smalltext"><tr><td><b>販売</b></td><td><b>買取</b></td></tr>' . "\n";
  echo '<tr><td>所持金上限や、弊社キャラクターの在庫の都合上、複数のキャラクターにて<br>分割してお届けする場合がございます。ご注文いただきました数量に達する<br>まで受領操作をお願いいたします。<br>【】または【】よりお届けいたします。</td><td>当社キャラクター【】または【】にトレードをお願いいたします。</td></tr></table><hr size="2" noshade>' . "\n";
  echo '</td>';
  echo tep_image_submit('button_update.gif', 'よく確認してから押しなさい') . '</form>' . "\n";
} else {
  echo '価格：&nbsp;' . $products_price_preview . '<br>数量：&nbsp;' . $pInfo->products_quantity . '個' . "\n";
}
?>
        </b>



      </td>
        </tr>
    <?php
if (isset($_GET['read']) && $_GET['read'] == 'only' && (!isset($_GET['origin']) || !$_GET['origin'])) { //表示制限
  echo '<tr><td><b>よく確認してから押しなさい</b></td></tr>' . "\n";
} else {
?>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="main"><?php echo $pInfo->products_description;?><hr size="1" noshade><table width=""><tr><td>
          <?php if ($products_image_name) echo tep_image(tep_get_web_upload_dir($site_id) . 'products/' . $products_image_name, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'align="" hspace="5" vspace="5"');?>
          </td><td>
          <?php if ($products_image_name2) echo tep_image(tep_get_web_upload_dir($site_id) . 'products/' . $products_image_name2, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'align="right" hspace="5" vspace="5"');?>
          </td><td align="right">
          <?php if ($products_image_name3) echo tep_image(tep_get_web_upload_dir($site_id) . 'products/' . $products_image_name3, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'align="right" hspace="5" vspace="5"');?>
          </td></tr></table></td>
        </tr>
        <?php
      if ($pInfo->products_url) {
?>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td class="main"><?php echo sprintf(TEXT_PRODUCT_MORE_INFORMATION, $pInfo->products_url); ?></td>
        </tr>
        <?php
      }
?>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <?php
      if (isset($pInfo->products_date_available) && $pInfo->products_date_available > date('Y-m-d')) {
?>
        <tr>
          <td align="center" class="smallText"><?php echo sprintf(TEXT_PRODUCT_DATE_AVAILABLE, tep_date_long($pInfo->products_date_available)); ?></td>
        </tr>
        <?php
      } else {
?>
        <tr>
          <td align="center" class="smallText"><?php echo sprintf(TEXT_PRODUCT_DATE_ADDED, tep_date_long($pInfo->products_date_added)); ?></td>
        </tr>
        <?php
      }
?>
        <tr>
          <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <?php
    }
} // 表示制限終わり

    if (isset($_GET['read']) && $_GET['read'] == 'only') {
      if (isset($_GET['origin']) && $_GET['origin']) {
        $pos_params = strpos($_GET['origin'], '?', 0);
        if ($pos_params != false) {
          $back_url = substr($_GET['origin'], 0, $pos_params);
          $back_url_params = substr($_GET['origin'], $pos_params + 1);
        } else {
          $back_url = $_GET['origin'];
          $back_url_params = '';
        }
      } else {
        $back_url = FILENAME_CATEGORIES;
        $back_url_params = 'cPath=' . $cPath . '&pID=' . $pInfo->products_id;
      }
?>
        <tr>
          <td align="right"><?php echo '<a href="' . tep_href_link($back_url, $back_url_params, 'NONSSL') . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
        </tr>
        <?php
    } else {
?>
        <tr>
          <td align="right" class="smallText"><?php
/* Re-Post all POST'ed variables */
      reset($_POST);
      while (list($key, $value) = each($_POST)) {
        if (!is_array($_POST[$key])) {
          echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
        }
      } 
      $languages = tep_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        echo tep_draw_hidden_field('products_name[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_name[$languages[$i]['id']])));
        echo tep_draw_hidden_field('products_description[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_description[$languages[$i]['id']])));
        echo tep_draw_hidden_field('products_url[' . $languages[$i]['id'] . ']', htmlspecialchars(stripslashes($products_url[$languages[$i]['id']])));
      }
      //add hidden tags
      if (isset($_POST['tags']) && $_POST['tags']) {
        foreach ($_POST['tags'] as $t) {
          echo tep_draw_hidden_field('tags[]', $t); 
        }
      }
      if ($products_image_name)
      echo tep_draw_hidden_field('products_image', stripslashes($products_image_name));
      if ($products_image_name2)
      echo tep_draw_hidden_field('products_image2', stripslashes($products_image_name2));
      if ($products_image_name3)
      echo tep_draw_hidden_field('products_image3', stripslashes($products_image_name3));
      echo tep_image_submit('button_back.gif', IMAGE_BACK, 'name="edit"') . '&nbsp;&nbsp;';

      if ($_GET['pID']) {
        echo tep_image_submit('button_update.gif', IMAGE_UPDATE);
      } else {
        echo tep_image_submit('button_insert.gif', IMAGE_INSERT);
      }
      
      echo tep_draw_hidden_field('relate_products_id', $_POST['relate_products_id']);
      echo '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $_GET['pID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>';
?></td>
          </form>
        </tr>
        <?php
    }
  } else {
?>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading"><?php echo BOX_CATALOG_CATEGORIES_PRODUCTS; ?></td>
        <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
        <?php echo tep_draw_form('search', FILENAME_CATEGORIES, '', 'get') . "\n"; ?>
          <td class="smallText" align="right">
            <?php echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search', isset($_GET['search'])?$_GET['search']:'') . "\n"; ?>
          </td>
        </form>
        <?php echo tep_draw_form('goto', FILENAME_CATEGORIES, '', 'get') . "\n"; ?>
          <td class="smallText" align="right">
            <?php echo HEADING_TITLE_GOTO . ' ' . tep_draw_pull_down_menu('cPath', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"') . "\n"; ?>
          </td>
        </form>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CATEGORIES_PRODUCTS; ?></td>
<!--
            <?php if ($ocertify->npermission == 15 or $ocertify->npermission == 10) {?>
            <?php if (!isset($_GET['cPath']) or !$_GET['cPath']){?>
            <td class="dataTableHeadingContent" align="right">表示</td>
            <?php }?>
            <?php }?>
-->
            <td class="dataTableHeadingContent" align="right">価格</td>
            <td class="dataTableHeadingContent" align="right">数量</td>
                      <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                    </tr>
                    <?php
    $categories_count = 0;
    $rows = 0;
    if (isset($_GET['search']) && $_GET['search']) {
      $categories_query_raw = "
        select c.categories_id, 
               c.categories_status, 
               cd.categories_name, 
               cd.categories_image2, 
               cd.categories_image3, 
               cd.categories_meta_text, 
               c.categories_image, 
               c.parent_id, 
               c.sort_order, 
               c.max_inventory,
               c.min_inventory,
               c.date_added, 
               c.last_modified 
        from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where c.categories_id = cd.categories_id 
          and cd.language_id = '" . $languages_id . "' 
          and cd.categories_name like '%" . $_GET['search'] . "%' 
          and cd.site_id = '0'
        order by c.sort_order, cd.categories_name";
    } else {
      $categories_query_raw = "
        select c.categories_id, 
               c.categories_status, 
               cd.categories_name, 
               cd.categories_image2, 
               cd.categories_image3, 
               cd.categories_meta_text, 
               c.categories_image, 
               c.parent_id, 
               c.sort_order, 
               c.max_inventory,
               c.min_inventory,
               c.date_added, 
               c.last_modified 
        from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where c.parent_id = '" . $current_category_id . "' 
          and c.categories_id = cd.categories_id 
          and cd.language_id = '" . $languages_id . "' 
          and cd.site_id = '0'
        order by c.sort_order, cd.categories_name";
    }
  $categories_query = tep_db_query($categories_query_raw);
    while ($categories = tep_db_fetch_array($categories_query)) {
      $categories_count++;
      $rows++;

// Get parent_id for subcategories if search 
      if (isset($_GET['search']) && $_GET['search']) $cPath= $categories['parent_id'];

      if ( 
          ((!isset($_GET['cID']) || !$_GET['cID']) && (!isset($_GET['pID']) || !$_GET['pID']) || (isset($_GET['cID']) && $_GET['cID'] == $categories['categories_id'])) 
          && (!isset($cInfo) || !$cInfo) 
          && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_')
        ) {
        $category_childs = array('childs_count' => tep_childs_in_category_count($categories['categories_id']));
        $category_products = array('products_count' => tep_products_in_category_count($categories['categories_id']));

        $cInfo_array = tep_array_merge($categories, $category_childs, $category_products);
        $cInfo = new objectInfo($cInfo_array);
      }

// 列を色違いにする
$even = 'dataTableSecondRow';
$odd = 'dataTableRow';
if (isset($nowColor) && $nowColor == $odd) {
  $nowColor = $even;
} else {
  $nowColor = $odd;
}

      if ( (isset($cInfo) && is_object($cInfo)) && ($categories['categories_id'] == $cInfo->categories_id) ) {
        echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, tep_get_path($categories['categories_id'])) . '\'">' . "\n";
      } else {
        echo '              <tr class="' . $nowColor . '" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'' . $nowColor . '\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . (isset($_GET['page'])&&$_GET['page'] ? ('&page=' . $_GET['page']) : '' ) . '&cID=' . $categories['categories_id']) . '\'">' . "\n";
      }
?>
                    <td class="dataTableContent">
                    <?php 
                    echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, tep_get_path($categories['categories_id'])) . '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;
                    <b>' . $categories['categories_name'] . '</b>'; ?></td>
                      

            <td class="dataTableContent" align="right">&nbsp;</td>
            <td class="dataTableContent" align="right">&nbsp;</td>
            <td class="dataTableContent" align="center">
<?php if ($ocertify->npermission == 15 or $ocertify->npermission == 10) {?>
<?php //if (!isset($_GET['cPath']) or !$_GET['cPath']){?>
                <?php if($categories['categories_status'] == '1'){?>
                  <a href="<?php echo tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=0&cPath='.$HTTP_GET_VARS['cPath']);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '');?></a> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=2&cPath='.$HTTP_GET_VARS['cPath']);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', '');?></a> <?php echo tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', '');?> 
                <?php } else if($categories['categories_status'] == '2'){?>
                  <a href="<?php echo tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=0&cPath='.$HTTP_GET_VARS['cPath']);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '');?></a> <?php echo tep_image(DIR_WS_IMAGES . 'icon_status_blue.gif', '');?> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=1&cPath='.$HTTP_GET_VARS['cPath']);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', '');?></a>
                <?php } else {?>
                  <?php echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', '');?> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=2&cPath='.$_GET['cPath']);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', '');?></a> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=1&cPath='.$_GET['cPath']);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', '');?></a> 
                <?php }?>
            <?php }?>
<?php //}?>
            </td>
            <td class="dataTableContent" align="right"><?php if ( (isset($cInfo) && is_object($cInfo)) && ($categories['categories_id'] == $cInfo->categories_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>
&nbsp;</td>
                    </tr>
                    <?php
    }

    $products_count = 0;
    if (isset($_GET['search']) && $_GET['search']) {
      $products_query_raw = "
        select p.products_id, 
               pd.products_name, 
               p.products_quantity, 
               p.products_image,
               p.products_image2,
               p.products_image3, 
               p.products_price, 
               p.products_price_offset,
               p.products_date_added, 
               p.products_last_modified, 
               p.products_date_available, 
               p.products_status, 
               p2c.categories_id 
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
        where p.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.products_id = p2c.products_id 
          and pd.products_name like '%" . $_GET['search'] . "%' 
          and pd.site_id='0'
        order by p.sort_order,pd.products_name";
    } else {
      $products_query_raw = "
        select p.products_id, 
               pd.products_name, 
               p.products_quantity, 
               p.products_image,
               p.products_image2,
               p.products_image3, 
               p.products_price, 
               p.products_price_offset,
               p.products_date_added, 
               p.products_last_modified, 
               p.products_date_available, 
               p.products_status 
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
        where p.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.products_id = p2c.products_id 
          and p2c.categories_id = '" . $current_category_id . "' 
          and pd.site_id='0'
        order by p.sort_order,pd.products_name";
    }
    $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_PRODUCTS_ADMIN, $products_query_raw, $products_query_numrows);
    $products_query = tep_db_query($products_query_raw);
    while ($products = tep_db_fetch_array($products_query)) {
      $products_count++;
      $rows++;

// Get categories_id for product if search 
      if (isset($_GET['search']) && $_GET['search']) $cPath=$products['categories_id'];

      if ( 
          ((!isset($_GET['pID']) || !$_GET['pID']) && (!isset($_GET['cID']) || !$_GET['cID']) || (isset($_GET['pID']) && $_GET['pID'] == $products['products_id'])) 
          && (!isset($pInfo) || !$pInfo) 
          && (!isset($cInfo) || !$cInfo) 
          && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_') 
        ) {
// find out the rating average from customer reviews
        $reviews_query = tep_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . $products['products_id'] . "'");
        $reviews = tep_db_fetch_array($reviews_query);
        $pInfo_array = tep_array_merge($products, $reviews);
        $pInfo = new objectInfo($pInfo_array);
      }

// 列を色違いにする
// products list
$even = 'dataTableSecondRow';
$odd = 'dataTableRow';
if (isset($nowColor) && $nowColor == $odd) {
  $nowColor = $even;
} else {
  $nowColor = $odd;
}

      if ( (isset($pInfo) && is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id) ) {
        echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . ($_GET['page'] ? ('&page=' . $_GET['page']) : '' ) . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '\'">' . "\n";
      } else {
        echo '              <tr class="' . $nowColor . '" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'' . $nowColor . '\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . ($_GET['page'] ? ('&page=' . $_GET['page']) : '' ) . '&pID=' . $products['products_id']) . '\'">' . "\n";
      }
?>
                    <td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;&nbsp;<a href="orders.php?search_type=products_name&keywords=' . urlencode($products['products_name']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_time.gif', '', 16, 16) . '</a>&nbsp;&nbsp;' . $products['products_name']; ?></td>

                      <td class="dataTableContent" align="right"><?php
      $product_price = tep_get_products_price($products['products_id']);
      if ($product_price['sprice']) {
        echo '<s>' . $currencies->format($product_price['price']) . '</s> <span class="specialPrice">' . $currencies->format($product_price['sprice']) . '</span>';
      } else {
        echo $currencies->format($product_price['price']);
      }
  ?></td>
            <td class="dataTableContent" align="right"><?php
//if (empty($products['products_quantity']) or $products['products_quantity'] < 1) {
if (empty($products['products_quantity']) or $products['products_quantity'] == 0) {
  echo '<b>在庫切れ</b>';
} else {
  echo intval($products['products_quantity']) . '個';
} ?></td>
            <td class="dataTableContent" align="center"><?php
if ($ocertify->npermission >= 10) { //表示制限
      if ($products['products_status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=2&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else if ($products['products_status'] == '2') {
        echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_blue.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=2&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
} else {
  // 価格更新警告
  $last_modified_array = getdate(strtotime(tep_datetime_short($products['products_last_modified'])));
  $today_array = getdate();
  if ($last_modified_array["year"] == $today_array["year"] && $last_modified_array["mon"] == $today_array["mon"] && $last_modified_array["mday"] == $today_array["mday"]) {
    if ($last_modified_array["hours"] >= ($today_array["hours"]-2)) {
      echo tep_image(DIR_WS_ICONS . 'signal_blue.gif', '更新正常');
    } elseif ($last_modified_array["hours"] >= ($today_array["hours"]-5)) {
      echo tep_image(DIR_WS_ICONS . 'signal_yellow.gif', '更新注意');
    } else {
      echo tep_image(DIR_WS_ICONS . 'signal_red.gif', '更新警告');
    }
  } else {
    echo tep_image(DIR_WS_ICONS . 'signal_blink.gif', '更新異常');
  }
  
  echo '&nbsp;&nbsp;' . tep_image(DIR_WS_ICONS . 'battery_0.gif', '数量異常');
  
}
?></td>
                      <td class="dataTableContent" align="right"><?php if ( isset($pInfo) && (is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>
&nbsp;</td>
                    </tr>
                    <?php
    }

    if ($cPath_array) {
      $cPath_back = '';
      for($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
        if ($cPath_back == '') {
          $cPath_back .= $cPath_array[$i];
        } else {
          $cPath_back .= '_' . $cPath_array[$i];
        }
      }
    }

    $cPath_back = isset($cPath_back) && $cPath_back ? 'cPath=' . $cPath_back : '';
?>
  <tr>
    <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_PRODUCTS_ADMIN, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
    <td class="smallText" align="right" colspan="4">
      <?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_PRODUCTS_ADMIN, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'pID'))); ?>
    </td>
  </tr>
                    <tr>
                      <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                          <tr>
                            <td class="smallText"><?php echo TEXT_CATEGORIES . '&nbsp;' . $categories_count . '<br>' . TEXT_PRODUCTS . '&nbsp;' . $products_query_numrows; ?></td>
                            <td align="right" class="smallText"><?php
  if ($cPath) {
    echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $cPath_back . '&cID=' . $current_category_id) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>&nbsp;';
  }
  if ((!isset($_GET['search']) || !$_GET['search']) && $ocertify->npermission >= 10) { //表示制限
    echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&action=new_category') . '">' . tep_image_button('button_new_category.gif', IMAGE_NEW_CATEGORY) . '</a>&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&action=new_product') . '">' . tep_image_button('button_new_product.gif', IMAGE_NEW_PRODUCT) . '</a>';
  }
?>&nbsp;</td>
                          </tr>
<?php
// google start
tep_display_google_results()
// google end
?>
                        </table></td>
                    </tr>
                  </table></td>
                <?php
    $heading = array();
    $contents = array();
    switch (isset($_GET['action'])?$_GET['action']:null) {
      case 'new_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CATEGORY . '</b>');

        $contents = array('form' => tep_draw_form('newcategory', FILENAME_CATEGORIES, 'action=insert_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"'));
        $contents[] = array('text' => TEXT_NEW_CATEGORY_INTRO);

        $category_inputs_string = '';
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $category_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']').'<br>'."\n".
                   '<br>Romaji:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('romaji[' . $languages[$i]['id'] . ']').'<br>'."\n".
                   '<br>トップページカテゴリバナー画像:<br>'.tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;'.tep_draw_file_field('categories_image2').'<br>'."\n".
                   '<br>カテゴリタイトル画像:<br>'.tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;'.tep_draw_file_field('categories_image3').'<br><font color="red">画像がない場合はテキスト表示されます</font><br>'."\n".
                   '<br>METAタグ<br>（この説明文はトップページのカテゴリバナーの下に表示される文章としても使用されます。2行にするにはカンマ「,」区切りで文章を記述してください。)<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('categories_meta_text[' . $languages[$i]['id'] . ']','',30,3).

                   '<br>SEOネーム:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('seo_name[' . $languages[$i]['id'] . ']', '').'<br>'."\n".
                   '<br>SEO Description:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('seo_description[' . $languages[$i]['id'] . ']','soft',30,3).
                   '<br>カテゴリHeaderのテキスト:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('categories_header_text[' . $languages[$i]['id'] . ']','soft',30,3).
                   '<br>カテゴリFooterのテキスト:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('categories_footer_text[' . $languages[$i]['id'] . ']','soft',30,3).
                   '<br>テキストのインフォメーション:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('text_information[' . $languages[$i]['id'] . ']','soft',30,3).
                   '<br>metaのキーワード:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('meta_keywords[' . $languages[$i]['id'] . ']','soft',30,3).
                   '<br>metaの説明:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('meta_description[' . $languages[$i]['id'] . ']','soft',30,3).
                   "\n";
        }

        $contents[] = array('text' => '<br>' . TEXT_CATEGORIES_NAME . $category_inputs_string);
        $contents[] = array('text' => '<br>' . TEXT_CATEGORIES_IMAGE . '<br>' . tep_draw_file_field('categories_image'));
        $contents[] = array('text' => '<br>' . TEXT_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', '', 'size="2"'));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'edit_category':
        $site_id = isset($_GET['site_id'])?$_GET['site_id']:0;
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CATEGORY . '</b>');

        $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES, 'action=update_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"') . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
        $contents[] = array('text' => TEXT_EDIT_INTRO.($site_id?('<br><b>'.tep_get_site_name_by_id($site_id).'</b>'):''));
        $contents[] = array('text' => tep_draw_hidden_field('site_id', $site_id));
 
        $category_inputs_string = '';
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $category_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', tep_get_category_name($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).'<br>'."\n".
         '<br>Romaji:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('romaji[' . $languages[$i]['id'] . ']', tep_get_category_romaji($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).'<br>'."\n".
         '<br>'.tep_image(tep_get_web_upload_dir($site_id) .'categories/'. $cInfo->categories_image2, $cInfo->categories_name).'<br>' . tep_get_upload_dir($site_id) . 'categories/<br><b>' . $cInfo->categories_image2 . '</b><br><br>トップページカテゴリバナー画像<br>'.tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;'.tep_draw_file_field('categories_image2').'<br>'."\n".
         '<br>'.tep_image(tep_get_web_upload_dir($site_id) . 'categories/'. $cInfo->categories_image3, $cInfo->categories_name).'<br>' . tep_get_upload_dir($site_id). 'categories/<br><b>' . $cInfo->categories_image3 . '</b><br><br>カテゴリタイトル画像<br>'.tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;'.tep_draw_file_field('categories_image3').'<br>'."\n".
         '<br>METAタグ（この説明文はトップページのカテゴリバナーの下に表示される文章としても使用されます。2行にするにはカンマ「,」区切りで文章を記述してください。)<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('categories_meta_text[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_category_meta_text($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).

         '<br>SEOネーム:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('seo_name[' . $languages[$i]['id'] . ']', tep_get_seo_name($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).'<br>'."\n".
         '<br>SEO Description:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('seo_description[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_seo_description($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).
         '<br>カテゴリHeaderのテキスト:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('categories_header_text[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_categories_header_text($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).
         '<br>カテゴリFooterのテキスト:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('categories_footer_text[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_categories_footer_text($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).
         '<br>テキストのインフォメーション:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('text_information[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_text_information($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).
         '<br>metaのキーワード:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('meta_keywords[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_meta_keywords($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).
         '<br>metaの説明:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('meta_description[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_meta_description($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).
         '';
        }

        $contents[] = array('text' => '<br>' . TEXT_EDIT_CATEGORIES_NAME . $category_inputs_string);
        $contents[] = array('text' => '<br>' . tep_image(tep_get_web_upload_dir($site_id).'categories/'. $cInfo->categories_image, $cInfo->categories_name) . '<br>' . tep_get_upload_dir($site_id). 'categories/<br><b>' . $cInfo->categories_image . '</b>');
        $contents[] = array('text' => '<br>' . TEXT_EDIT_CATEGORIES_IMAGE . '<br>' . tep_draw_file_field('categories_image'));
        $contents[] = array('text' => '<br>' . TEXT_EDIT_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"'));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'delete_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CATEGORY . '</b>');

        $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES, 'action=delete_category_confirm&cPath=' . $cPath) . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
        $contents[] = array('text' => TEXT_DELETE_CATEGORY_INTRO);
        $contents[] = array('text' => '<br><b>' . $cInfo->categories_name . '</b>');
        if ($cInfo->childs_count > 0) $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->childs_count));
        if ($cInfo->products_count > 0) $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $cInfo->products_count));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'delete_category_description':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CATEGORY . '</b>');

        $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES, 'action=delete_category_description_confirm&cID=' . $_GET['cID'] . '&cPath=' . $cPath . '&site_id=' . $_GET['site_id'], 'get'));
        $contents[] = array('text' => TEXT_DELETE_CATEGORY_INTRO);
        $contents[] = array('text' => '<br><b>' . $cInfo->categories_name . '</b>');
        //if ($cInfo->childs_count > 0) $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->childs_count));
        //if ($cInfo->products_count > 0) $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $cInfo->products_count));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'move_category':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_CATEGORY . '</b>');

        $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES, 'action=move_category_confirm') . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
        $contents[] = array('text' => sprintf(TEXT_MOVE_CATEGORIES_INTRO, $cInfo->categories_name));
        $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $cInfo->categories_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree('0', '', $cInfo->categories_id), $current_category_id));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'delete_product_description':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PRODUCT . '</b>');

        $contents = array('form' => tep_draw_form('products', FILENAME_CATEGORIES, 'action=delete_product_description_confirm&site_id=' . $_GET['site_id'] . '&pID=' . $_GET['pID'] . '&cPath=' . $cPath, 'get'));
        $contents[] = array('text' => TEXT_DELETE_PRODUCT_INTRO);
        $contents[] = array('text' => '<br><b>' . $pInfo->products_name . '</b>');

        //$contents[] = array('text' => '<br>' . $product_categories_string);
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'delete_product':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PRODUCT . '</b>');

        $contents = array('form' => tep_draw_form('products', FILENAME_CATEGORIES, 'action=delete_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));
        $contents[] = array('text' => TEXT_DELETE_PRODUCT_INTRO);
        $contents[] = array('text' => '<br><b>' . $pInfo->products_name . '</b>');

        $product_categories_string = '';
        $product_categories = tep_generate_category_path($pInfo->products_id, 'product');
        for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
          $category_path = '';
          for ($j = 0, $k = sizeof($product_categories[$i]); $j < $k; $j++) {
            $category_path .= $product_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
          }
          $category_path = substr($category_path, 0, -16);
          $product_categories_string .= tep_draw_checkbox_field('product_categories[]', $product_categories[$i][sizeof($product_categories[$i])-1]['id'], true) . '&nbsp;' . $category_path . '<br>';
        }
        $product_categories_string = substr($product_categories_string, 0, -4);

        $contents[] = array('text' => '<br>' . $product_categories_string);
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'move_product':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_PRODUCT . '</b>');

        $contents = array('form' => tep_draw_form('products', FILENAME_CATEGORIES, 'action=move_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));
        $contents[] = array('text' => sprintf(TEXT_MOVE_PRODUCTS_INTRO, $pInfo->products_name));
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' . tep_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
        $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $pInfo->products_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree(), $current_category_id));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'copy_to':
        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_COPY_TO . '</b>');

        $contents = array('form' => tep_draw_form('copy_to', FILENAME_CATEGORIES, 'action=copy_to_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));
        $contents[] = array('text' => TEXT_INFO_COPY_TO_INTRO);
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' . tep_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
        $contents[] = array('text' => '<br>' . TEXT_CATEGORIES . '<br>' . tep_draw_pull_down_menu('categories_id', tep_get_category_tree(), $current_category_id));
        $contents[] = array('text' => '<br>' . TEXT_HOW_TO_COPY . '<br>' . tep_draw_radio_field('copy_as', 'link', true) . ' ' . TEXT_COPY_AS_LINK . '<br>' . tep_draw_radio_field('copy_as', 'duplicate') . ' ' . TEXT_COPY_AS_DUPLICATE);
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_copy.gif', IMAGE_COPY) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      case 'edit_keyword':
        $categories_to_mission_sql = 'SELECT c2m.*,m.keyword from '
                                      .TABLE_CATEGORIES_TO_MISSION.' c2m ,'
                                      .TABLE_MISSION.' m'
                                      .' where c2m.mission_id = m.id and c2m.categories_id  ="'.$cID.'"';
        $categories_to_mission_query = tep_db_query($categories_to_mission_sql);
        $categories_to_mission_res =
          tep_db_fetch_array($categories_to_mission_query);
        $heading[] = array('text' => '<b>'. TEXT_INFO_KEYWORD.'</b>');
        $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES,
              'action=upload_keyword&cID=' . $_GET['cID'] . '&cPath=' . $cPath .
              '&site_id=' . $_GET['site_id'], 'post'));
        $contents[] = array('text' => '<br>' . TEXT_KEYWORD . '<br>' .
            tep_draw_input_field('keyword',
              $categories_to_mission_res['keyword']?$categories_to_mission_res['keyword']:'', ''));
        $contents[] = array('text' => tep_draw_hidden_field('categories_id',$cID));
        if($categories_to_mission_res){
        $contents[] = array('text' => tep_draw_hidden_field('method','upload'));
        }else{
        $contents[] = array('text' => tep_draw_hidden_field('method','insert'));
        }
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id). '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
        //min max edit
      case 'edit_inventory':
        $heading[] = array('text' => '<b>'. TEXT_INFO_INVENTORY.'</b>');
        $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES,
              'action=upload_inventory&cID=' . $_GET['cID'] . '&cPath=' . $cPath .
              '&site_id=' . $_GET['site_id'], 'post'));
        $contents[] = array('text' => '<br>' . TEXT_MAX . '<br>' .
            tep_draw_input_field('max_inventory',
              $cInfo->max_inventory?$cInfo->max_inventory:'', ''));
        $contents[] = array('text' => '<br>' . TEXT_MIN . '<br>' .
            tep_draw_input_field('min_inventory',
              $cInfo->min_inventory?$cInfo->min_inventory:'', ''));
        $contents[] = array('text' => tep_draw_hidden_field('categories_id',$cID));
        $inv_msg = $HTTP_GET_VARS['msg'];
        if(isset($inv_msg)&&$inv_msg=='error'){
        $contents[] = array('align' => 'center',
            'text' => TEXT_INVENTORY_ERROR);
        }
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id). '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;
      default:
        if ($rows > 0) {
          if (isset($cInfo) && is_object($cInfo)) { // category info box contents
            $heading[] = array('text' => '<b>' . $cInfo->categories_name . '</b>');



          if ($ocertify->npermission >= 10) { //表示制限

            $contents[] = array(
                'align' => 'left', 
                'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_category') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> '  
                . ($ocertify->npermission == 15 ? ( '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=delete_category') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a> '):'')
                . '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=move_category') . '">' . tep_image_button('button_move.gif', IMAGE_MOVE) . '</a>');

            foreach(tep_get_sites() as $site){
              $contents[] = array('text' => '<b>' . $site['romaji'] . '</b>');
              $contents[] = array(
                  'align' => 'left', 
                  'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_category&site_id='.$site['id']) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>'.
                  (tep_categories_description_exist($cInfo->categories_id, $languages_id, $site['id']) 
                   ? (' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=delete_category_description&site_id='.$site['id']) . '">'.tep_image_button('button_delete.gif', IMAGE_DELETE).'</a>')
                   :''
                   ));
            }
            $keyword_sql = "select m.keyword from ".TABLE_CATEGORIES_TO_MISSION." c2m,".TABLE_MISSION." m
                            where c2m.categories_id='".$cInfo->categories_id."' and c2m.mission_id = m.id  ";
            $keyword_query = tep_db_query($keyword_sql);
            $keyword_res = tep_db_fetch_array($keyword_query);
            $default_keyword = $keyword_res?$keyword_res['keyword']:'';
            $contents[] = array('text' => '<b>'.TEXT_KEYWORD.'&nbsp;:&nbsp;&nbsp;'.$default_keyword.'</b>');
            $contents[] = array(
                'align' => 'left',
                'text' => '<a href="'. tep_href_link(FILENAME_CATEGORIES, 'cPath=' .
              $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_keyword') .
                '">'.tep_image_button('button_edit.gif', TEXT_KEYWORD) . '</a> ');
            // max and nim
            if($cInfo->parent_id == 0){
            $contents[] = array('text' =>
                '<b>'.TEXT_MAX.'&nbsp;:&nbsp;&nbsp;'.$cInfo->max_inventory.'</b>');
            $contents[] = array('text' =>
                '<b>'.TEXT_MIN.'&nbsp;:&nbsp;&nbsp;'.$cInfo->min_inventory.'</b>');
            $contents[] = array(
                'align' => 'left',
                'text' => '<a href="'. tep_href_link(FILENAME_CATEGORIES, 'cPath=' .
              $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_inventory') .
                '">'.tep_image_button('button_edit.gif', TEXT_INVENTORY) . '</a> ');
            }
}
//print_r($cInfo);
            $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($cInfo->date_added));
            if (tep_not_null($cInfo->last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($cInfo->last_modified));
            $contents[] = array('text' => '<br>' . tep_info_image('categories/'.$cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 0) . '<br>' . $cInfo->categories_image);
            $contents[] = array('text' => '<br>' . TEXT_SUBCATEGORIES . ' ' . $cInfo->childs_count . '<br>' . TEXT_PRODUCTS . ' ' . $cInfo->products_count);
          } elseif (isset($pInfo) && is_object($pInfo)) { // product info box contents
            $heading[] = array('text' => '<b>' . tep_get_products_name($pInfo->products_id, $languages_id) . '</b>');
            
            // 关联商品
            $contents[] = array('align' => 'left', 'text' => '関連付け: '.tep_get_relate_products_name($pInfo->products_id));
          if ($ocertify->npermission >= 10) { //表示制限
            $contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product'.'&page='.$_GET['page']) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>' 
                . ($ocertify->npermission == 15 ? (' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_product'.'&page='.$_GET['page']) . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>'):'')
                . ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=move_product'.'&page='.$_GET['page']) . '">' . tep_image_button('button_move.gif', IMAGE_MOVE) . '</a> <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=copy_to') . '">' . tep_image_button('button_copy_to.gif', IMAGE_COPY_TO) . '</a>'
                . ' <a href="' . tep_href_link(FILENAME_REVIEWS, 'cPath=' . $cPath . '&products_id=' . $pInfo->products_id . '&action=new') . '">' . tep_image_button('button_reviews.gif', IMAGE_REVIEWS) . '</a>');
            foreach(tep_get_sites() as $site){
              $contents[] = array('text' => '<b>' . $site['romaji'] . '</b>');
              $contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product'. '&site_id='. $site['id'].'&page='.$_GET['page'])  .'">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>' . (
                tep_products_description_exist($pInfo->products_id, $site['id'], $languages_id) ? ' <a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_product_description&site_id='.$site['id'].'&page='.$_GET['page']) . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>'
                : ''
                    ) );
            }
            /*
            $keyword_sql = "select m.keyword from ".TABLE_CATEGORIES_TO_MISSION." c2m,".TABLE_MISSION." m
                            where m.id=c2m.mission_id and categories_id='".$cInfo->categories_id."'";
            $keyword_query = tep_db_query($keyword_sql);
            $keyword_res = tep_db_fetch_array($keyword_query);
            
            $default_keyword = $keyword_res?$keyword_res['keyword']:'';
            
            $contents[] = array('text' => '<b>'.TEXT_KEYWORD.$default_keyword.'</b>');
            
            $contents[] = array(
                'align' => 'left',
                'text' => '<a href="'. tep_href_link(FILENAME_CATEGORIES, 'cPath=' .
              $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_keyword') .
                '">'.tep_image_button('button_edit.gif', TEXT_KEYWORD) . '</a> ');
            */
}else{
            $contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(FILENAME_REVIEWS, 'cPath=' . $cPath . '&products_id=' . $pInfo->products_id . '&action=new') . '">' . tep_image_button('button_reviews.gif', IMAGE_REVIEWS) . '</a>');
}

            $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($pInfo->products_date_added));
            if (tep_not_null($pInfo->products_last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_datetime_short($pInfo->products_last_modified));
            if (date('Y-m-d') < $pInfo->products_date_available) $contents[] = array('text' => TEXT_DATE_AVAILABLE . ' ' . tep_date_short($pInfo->products_date_available));
            $contents[] = array('text' => '<br>' . tep_info_image('products/'.$pInfo->products_image, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 0) . '<br>' . $pInfo->products_image);
            if($pInfo->products_image2) {
              $contents[] = array('text' => '<br>' . tep_info_image('products/'.$pInfo->products_image2, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>' . $pInfo->products_image2, 0);
            }
            if($pInfo->products_image3) {
              $contents[] = array('text' => '<br>' . tep_info_image('products/'.$pInfo->products_image3, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>' . $pInfo->products_image3, 0);
            }
            
            
            
//特価がある場合の処理
$product_price = tep_get_products_price($pInfo->products_id);
if ($product_price['sprice']) {
  $contents[] = array('text' => '<br><b>' . TEXT_PRODUCTS_PRICE_INFO . ' <s>' . $currencies->format($product_price['price']) . '</s> <span class="specialPrice">' . $currencies->format($product_price['sprice']) . '</span></b>');
} else {
  $contents[] = array('text' => '<br><b>' . TEXT_PRODUCTS_PRICE_INFO . ' ' . $currencies->format($product_price['price']) . '</b>');
}
$contents[] = array('text' => '<br><b>' . TEXT_PRODUCTS_QUANTITY_INFO . ' ' . $pInfo->products_quantity . '個</b>');
$contents[] = array('text' => '<br>' . TEXT_PRODUCTS_AVERAGE_RATING . ' ' . number_format($pInfo->average_rating, 2) . '%');
          }
        } else { // create category/product info
          $heading[] = array('text' => '<b>' . EMPTY_CATEGORY . '</b>');

          $contents[] = array('text' => sprintf(TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS, isset($parent_categories_name)?$parent_categories_name:''));
        }
        break;
    }

    if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
      echo '            <td width="25%" valign="top">' . "\n";

      $box = new box;
      echo $box->infoBox($heading, $contents);

      echo '            </td>' . "\n";
    }
?>
              </tr>
            </table></td>
        </tr>
        <?php
  }
?>
      </table></td>
    <!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
