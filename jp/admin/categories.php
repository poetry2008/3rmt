<?php
/*
   $Id$

   分类&商品管理
 */
require('includes/application_top.php');
//把指定的页面设置为不缓存
if(isset($_GET['action']) && $_GET['action'] == 'setting_products_tags'){

  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  # 永远是改动过的
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");
}
require(DIR_WS_CLASSES . 'currencies.php');  
require(DIR_FS_ADMIN . '/classes/notice_box.php');
include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_TAGS);
//获取指定页面排序的URL参数
$sort_str = '';
if (isset($_GET['sort'])&&$_GET['sort']){
  $sort_str = 'action='.$_GET['action'].'&sort='.$_GET['sort'];
}
$cPath_yobi = cpathPart($_GET['cPath'], 1);  
$currencies = new currencies();
$action = (isset($_GET['action']) ? $_GET['action'] : '');
if ( eregi("(insert|update|setflag)", $action) ) include_once('includes/reset_seo_cache.php');

if (isset($_GET['action']) && $_GET['action']) {
  switch ($_GET['action']) {
/* -----------------------------------------------------
   cale 'edit_products_tags' 单个商品关联标签
   case 'products_tags_save' 保存商品关联标签 
   case 'products_tags_delete' 保存商品关联标签
   case 'get_last_order_date' 获得该商品最近一次被购买的时间 
   case 'edit_category|new_product' 判断是否有权限操作该动作 
   case 'all_update' 更新商品的价格及关联的同业者 
   case 'simple_update_product' 更新商品及其关联的价格,实际库存,虚拟库存,最大/最小库存,更新者,更新时间 
   case 'get_products' 关联商品的下拉列表 
   case 'get_cart_products' 显示该商品的提醒商品名 
   case 'toggle' 更新分类的状态 
   case 'setflag' 更新商品的状态 
   case 'simple_update' 商品价格和数量的简单更新 
   case 'upload_inventory' 更新最大/最小在库数 
   case 'insert_category' 创建分类  
   case 'update_category' 更新分类 
   case 'delete_product_description_confirm' 删除商品描述 
   case 'delete_category_description_confirm' 删除分类描述  
   case 'delete_category_confirm' 删除分类  
   case 'delete_product_confirm' 删除商品 
   case 'move_category_confirm' 移动分类  
   case 'move_product_confirm' 移动商品  
   case 'insert_product' 创建商品  
   case 'update_product' 更新商品  
   case 'copy_to_confirm' 复制商品  
   case 'new_product_preview' 在商品预览页把提交的数据放到session里  
   case 'delete_select_categories_products' 删除分类及子分类及相对应的商品，或者删除商品
 ------------------------------------------------------*/
    case 'edit_products_tags':
        tep_isset_eof();
        $tags_id = $_POST['tags_id'];
        $tags_type = $_POST['tags_type'];
        $tags_url = $_POST['tags_url'];
        $tags_url_array = explode('|||',$tags_url);
        $tags_url = str_replace('|||','&',$tags_url);
        foreach($tags_url_array as $tags_url_value){

          $tags_url_value_array = explode('=',$tags_url_value);
          if($tags_url_value_array[0] == 'pID'){
            
            $tags_pid_key = $tags_url_value_array[1];
          }

          if($tags_url_value_array[0] == 'cPath'){

            $tags_path_key = $tags_url_value_array[1];
          }
        }

        if($tags_pid_key){

          $tags_key = $tags_pid_key;
        }else{

          if($tags_path_key){

            $tags_key = $tags_path_key;
          }else{

            $tags_key = 0;
          }
        }

        if($tags_type == 1){

          $_SESSION['pid_tags_id_list_array'][$tags_key] = $tags_id;
        }else{
          $tags_key_array = array();
          foreach($tags_id as $tags_id_value){

            $tags_key_array[$tags_id_value] = $tags_id_value;
          }
          $_SESSION['carttags_id_list_array'][$tags_key] = $tags_key_array;  
        }
    break;
    case 'products_tags_save': 
        $tags_id_list = $_POST['tags_id_list'];
        $tags_id_list_array = explode(',',$tags_id_list);
        $tags_url = $_POST['tags_url'];
        if (is_array($tags_id_list_array) && !empty($tags_id_list_array)) {
          foreach($tags_id_list_array as $tid) {
          tep_db_query("delete from products_to_tags where tags_id='".$tid."'");
            if ($_POST['products_id']) {
               foreach($_POST['products_id'] as $pid) {
                tep_db_query("update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now(),products_user_update='".$_SESSION['user_name']."' where products_id='".$pid."'");
                tep_db_perform("products_to_tags", array('products_id' => (int)$pid, 'tags_id' => (int)$tid));
               }
            }
          } 
        }
        tep_redirect(tep_href_link(FILENAME_CATEGORIES.'?'.$tags_url));
    break;
    case 'products_tags_delete': 
        $categories_id_list = $_POST['categories_id_list'];
        $products_id_list = $_POST['products_id_list']; 
        $categories_id_list = explode(',',$categories_id_list);
        $products_id_list = explode(',',$products_id_list); 
        $tags_url = $_POST['tags_url'];
        $products_tags_array = array();
        foreach($categories_id_list as $categories_id_value){

                  $parent_categories_query = tep_db_query("select parent_id from categories  where categories_id='".$categories_id_value."'");
                  $parent_categories_array = tep_db_fetch_array($parent_categories_query);
                  tep_db_free_result($parent_categories_query);
                  if($parent_categories_array['parent_id'] == '0'){

                    $child_categories_query = tep_db_query("select categories_id from categories  where parent_id='".$categories_id_value."'");
                    while($child_categories_array = tep_db_fetch_array($child_categories_query)){

                      $parent_categories_id_query = tep_db_query("select categories_id from categories  where parent_id='".$child_categories_array['categories_id']."'");
                      
                      if(tep_db_num_rows($parent_categories_id_query)){
                        
                        while($parent_categories_id_array = tep_db_fetch_array($parent_categories_id_query)){
                          $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$parent_categories_id_array['categories_id']."'");
                          while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                            $products_tags_array[] = $products_id_list_array['products_id'];
                          }
                          tep_db_free_result($products_id_list_query);
                        }
                        tep_db_free_result($parent_categories_id_query);
                      }else{

                        $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$child_categories_array['categories_id']."'");
                        while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                          $products_tags_array[] = $products_id_list_array['products_id'];
                        }
                        tep_db_free_result($products_id_list_query);
                      }
                    }
                    tep_db_free_result($child_categories_query);
                  }else{

                    $parent_categories_id_query = tep_db_query("select categories_id from categories  where parent_id='".$categories_id_value."'");
                      
                      if(tep_db_num_rows($parent_categories_id_query)){
                        
                        while($parent_categories_id_array = tep_db_fetch_array($parent_categories_id_query)){
                          $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$parent_categories_id_array['categories_id']."'");
                          while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                            $products_tags_array[] = $products_id_list_array['products_id'];
                          }
                          tep_db_free_result($products_id_list_query);
                        }
                        tep_db_free_result($parent_categories_id_query);
                      }else{

                        $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$categories_id_value."'");
                        while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                          $products_tags_array[] = $products_id_list_array['products_id'];
                        }
                        tep_db_free_result($products_id_list_query);
                      }
                  }
        }
        foreach($products_id_list as $products_id_value){

                  $products_tags_array[] = $products_id_value;
        }
        foreach($products_tags_array as $products_tags_value) {
          tep_db_query("delete from products_to_tags where products_id='".$products_tags_value."'");
            
        } 
        tep_redirect(tep_href_link(FILENAME_CATEGORIES.'?'.$tags_url));
    break;
    case 'get_last_order_date';
      echo intval(tep_calc_limit_time_by_order_id($_POST['pid'],$_POST['single'],$_POST['limit_time']));
    exit;
    break;
    case 'edit_category'; 
    case 'new_product'; 
    if (!($ocertify->npermission >= 10)){
      forward401();
    }
    break;
    //一并更新
    case 'all_update':
    tep_isset_eof();
    require('includes/set/all_update.php');
    tep_redirect(tep_href_link(FILENAME_CATEGORIES, tep_get_all_get_params(array('action', 'x', 'y'))));
    break;
    // 产品 浮动DIV 保存 动作
    case 'simple_update_product':
    // 浮动DIV 修改信息
    tep_isset_eof();
    $products_id = tep_db_prepare_input($_GET['pID']);
    $site_id     = tep_db_prepare_input($_GET['site_id']);
    $relate_products_id = tep_db_prepare_input($_POST['relate_products_id']);
    //指定%的情况下，计算价格
    $HTTP_POST_VARS['products_price_offset'] = SBC2DBC($HTTP_POST_VARS['products_price_offset']);
    $update_sql_data = array(
        'max_inventory'             => tep_db_prepare_input($_POST['inventory_max']),
        'min_inventory'             => tep_db_prepare_input($_POST['inventory_min']),
        'products_real_quantity'    => tep_db_prepare_input($_POST['products_real_quantity']),
        'products_virtual_quantity' => tep_db_prepare_input($_POST['products_virtual_quantity']),
        'products_price'            => tep_get_bflag_by_product_id($products_id) ? 0 - abs(tep_db_prepare_input($_POST['products_price'])): abs(tep_db_prepare_input($_POST['products_price'])));
    tep_db_perform(TABLE_PRODUCTS, $update_sql_data, 'update', 'products_id = \'' . tep_db_input($products_id) . '\'');
    if(isset($relate_products_id)&&$relate_products_id){
      //指定%的情况下，计算价格
      $HTTP_POST_VARS['relate_products_price_offset'] = SBC2DBC($HTTP_POST_VARS['relate_products_price_offset']);
      // jiakong
      $relate_update_sql_data = array(
          'max_inventory'             => tep_db_prepare_input($_POST['relate_inventory_max']),
          'min_inventory'             => tep_db_prepare_input($_POST['relate_inventory_min']),
          'products_real_quantity'    => tep_db_prepare_input($_POST['relate_products_real_quantity']),
          'products_virtual_quantity' => tep_db_prepare_input($_POST['relate_products_virtual_quantity']),
          'products_price'            => tep_get_bflag_by_product_id($relate_products_id) ? 0 - abs(tep_db_prepare_input($_POST['relate_products_price'])): abs(tep_db_prepare_input($_POST['relate_products_price'])));
      tep_db_perform(TABLE_PRODUCTS, $relate_update_sql_data, 'update', 'products_id = \'' . tep_db_input($relate_products_id) . '\'');

    }
    if($site_id=="" || $site_id==0){
      $update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now(), products_user_update='".$_SESSION['user_name']."'  where products_id='".$products_id."'";
      tep_db_query($update_sql);
      $relate_update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now(), products_user_update='".$_SESSION['user_name']."' where products_id='".$relate_products_id."'";
      tep_db_query($relate_update_sql);
    }else{
      $update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now(), products_user_update='".$_SESSION['user_name']."' where products_id='".$products_id."' and site_id='".$site_id."'";
      tep_db_query($update_sql);
      $relate_update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now(),products_user_update='".$_SESSION['user_name']."' where products_id='".$relate_products_id."' and site_id='".$site_id."'";
      tep_db_query($relate_update_sql);
    }
    tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] .  '&pID=' .  $products_id.(!empty($_GET['site_id'])?'&site_id='.$_GET['site_id']:'')));
    break;
    // 保存动作结束
    case 'get_products':
    echo tep_draw_pull_down_menu('xxx',array_merge(array(array('id' => '0','text' => TEXT_NO_ASSOCIATION)),tep_get_products_tree($_GET['cid'])),$_GET['rid'],'onchange=\'$("#relate_products_id").val(this.options[this.selectedIndex].value)\'');
    exit;
    break;
    case 'get_cart_products':
    echo '<html><head>'; 
    echo '<meta http-equiv="Content-Type" content="text/html; charset='.CHARSET.'">';
    echo '</head>';
    echo '<body>'; 
    foreach(tep_get_cart_products($_GET['products_id'],$_GET['tags_id'],$_GET['buyflag']) as $p){
      $p = tep_get_product_by_id($p,0,4);
      echo $p['products_name'] . "<br>";
    }
    echo '</body></html>'; 
    exit;
    break;
    case 'toggle':
    $up_rs = (isset($_GET['up_rs']))?true:false; 
    if ($_GET['cID']) {
      $cID = intval($_GET['cID']);
      $site_id = (isset($_GET['site_id']))?$_GET['site_id']:0;
      if  (isset($_SESSION['site_permission']))    {
        $site_arr=$_SESSION['site_permission'];
      }
      else{
        $site_arr="";
      }
      forward401Unless(editPermission($site_arr, $site_id));
      tep_insert_pwd_log($_GET['once_pwd'],$ocertify->auth_user);
      $c_page = (isset($_GET['page']))?'&page='.$_GET['page']:''; 

      if (isset($_GET['status']) && ($_GET['status'] == 0 || $_GET['status'] == 1 || $_GET['status'] == 2 || $_GET['status'] == 3)){
        //0-绿色 1-红色 2-蓝色 3-黑色 
        tep_set_category_link_product_status($cID, $_GET['status'], $site_id, $up_rs); 
        if($site_id == "" || $site_id == 0){
          $update_sql = "update ".TABLE_CATEGORIES_DESCRIPTION." set last_modified=now(), user_last_modified='".$_SESSION['user_name']."' where categories_id='".$_GET['cID']."'";
          tep_db_query($update_sql);
        }else{
          $update_sql = "update ".TABLE_CATEGORIES_DESCRIPTION." set last_modified=now(), user_last_modified='".$_SESSION['user_name']."' where categories_id='".$_GET['cID']."' and site_id='".$site_id."'";
          tep_db_query($update_sql);
        }
      } 
    }
    if (USE_CACHE == 'true') {
      tep_reset_cache_block('categories');
      tep_reset_cache_block('also_purchased');
    }
    tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' .  $HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page));
    break;
    case 'setflag':
    if(isset($_SESSION['site_permission'])) {
      $site_arr=$_SESSION['site_permission'];
    } else {
      $site_arr="";
    }
    $site_id = (isset($_GET['site_id']))?$_GET['site_id']:0;  
    tep_insert_pwd_log($_GET['once_pwd'],$ocertify->auth_user);

    $p_page = (isset($_GET['page']))?'&page='.$_GET['page']:''; 
    $up_rs = (isset($_GET['up_rs']))?true:false; 
    
    if($site_id == "" || $site_id == 0){
      $update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now(), products_user_update='".$_SESSION['user_name']."'  where products_id='".$_GET['pID']."'";
      tep_db_query($update_sql);
    }else{
      $update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now(), products_user_update='".$_SESSION['user_name']."' where products_id='".$_GET['pID']."' and site_id='".$site_id."'";
      tep_db_query($update_sql);
    }
    
    if ($site_id == 0) {
      tep_set_all_product_status($_GET['pID'], $_GET['flag'], $up_rs); 
      if (USE_CACHE == 'true') {
        tep_reset_cache_block('categories');
        tep_reset_cache_block('also_purchased');
      }
      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' .  $_GET['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page));
    }

    if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') || ($_GET['flag'] == '2') || ($_GET['flag'] == '3')) {
      //0-红色 1-绿色 2-蓝色 3-黑色 
      if ($_GET['pID']) {
        tep_set_product_status_by_site_id($_GET['pID'], $_GET['flag'], $_GET['site_id'], $up_rs);
      }
      if (USE_CACHE == 'true') {
        tep_reset_cache_block('categories');
        tep_reset_cache_block('also_purchased');
      }
    }
    tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' .  $_GET['cPath'].'&pID='.$_GET['pID'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page));
    break;
    case 'simple_update': // 价格和数量的简单更新
    tep_isset_eof();
    $products_id = tep_db_prepare_input($_GET['pID']);
    $site_id     = tep_db_prepare_input($_POST['pID']);
    //指定%的情况下，计算价格
    $HTTP_POST_VARS['products_price_offset'] = SBC2DBC($HTTP_POST_VARS['products_price_offset']);
    $update_sql_data = array(
        'products_real_quantity'    => tep_db_prepare_input($_POST['products_real_quantity']),
        'products_virtual_quantity' => tep_db_prepare_input($_POST['products_virtual_quantity']),
        'products_attention_5'      => tep_db_prepare_input($_POST['products_attention_5']),
        'products_price'            =>
        tep_get_bflag_by_product_id($products_id) ? 0 - abs(tep_db_prepare_input($_POST['products_price'])): abs(tep_db_prepare_input($_POST['products_price'])));
    tep_db_perform(TABLE_PRODUCTS, $update_sql_data, 'update', 'products_id = \'' . tep_db_input($products_id) . '\'');
    if($site_id=="" || $site_id==0){
      $update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now() where products_id='".$products_id."'";
      tep_db_query($update_sql);
    }else{
      $update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now() where products_id='".$products_id."' and site_id='".$site_id."'";
      tep_db_query($update_sql);
    }
    tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&pID=' . $products_id));
    break;
    case 'upload_inventory':
    tep_isset_eof();
    $error = false;
    $products_id = $_POST['products_id'];
    $max_inventory = $_POST['max_inventory'];
    $min_inventory = $_POST['min_inventory'];
    if($max_inventory&&
        $max_inventory<$min_inventory){
      $error = true;
    }
    if($error){
      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID='
            . $products_id.'&action=edit_inventory&msg=error'));
    }else{
      $upload_inventory_sql = 'update '.TABLE_PRODUCTS.'
        set 
        max_inventory="'.$max_inventory.'",
        min_inventory="'.$min_inventory.'"
          where products_id="'.$products_id.'"'; 
      $update_latest_date = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now() where products_id='".$products_id."' and site_id='".$_GET['site_id']."'";
      tep_db_query($update_latest_date);
      tep_db_query($upload_inventory_sql);
      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $products_id.($_GET['search']?'&search='.$_GET['search']:'')));
    }
    break;
    case 'insert_category':
    case 'update_category':
    tep_isset_eof();
    $categories_id = tep_db_prepare_input($_POST['categories_id']);
    $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;

    if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
    else $site_arr="";
    forward401Unless(editPermission($site_arr, $site_id));
    $sort_order = tep_db_prepare_input($_POST['sort_order']);
    if(!isset($site_id)||$site_id==''||$site_id==0){
      $sql_data_array = array('sort_order' => $sort_order);
    }else{
      $sql_data_array = array();
    }

    if ($_GET['action'] == 'insert_category') {
      $insert_sql_data = array('parent_id' => $current_category_id,
          'user_added'=> $_POST['user_added'],
          'date_added' => 'now()');
      $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
      tep_db_perform(TABLE_CATEGORIES, $sql_data_array);
      $categories_id = tep_db_insert_id();
    } elseif ($_GET['action'] == 'update_category') {
      $update_sql_data = array('last_modified' => 'now()');
      $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
      tep_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\'');
      if($site_id=="" || $site_id==0){
        $update_sql = "update ".TABLE_CATEGORIES_DESCRIPTION." set last_modified=now(),user_last_modified='".$_POST['user_last_modified']."' where categories_id='".$categories_id."'";

        tep_db_query($update_sql);
      }else{
        $update_sql = "update ".TABLE_CATEGORIES_DESCRIPTION." set last_modified=now(),user_last_modified='".$_POST['user_last_modified']."' where categories_id='".$categories_id."' and site_id='".$site_id."'";
        tep_db_query($update_sql);
      }
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
      $character_romaji       = tep_replace_full_character($_POST['character_romaji']);
      $alpha_romaji           = tep_replace_full_character($_POST['alpha_romaji']);

      $language_id = $languages[$i]['id'];
      $sql_data_array = array(
          'categories_name' => tep_db_prepare_input($categories_name_array[$language_id]),
          'romaji' => str_replace(array('/','_', ' ', '　'), '-', tep_db_prepare_input($romaji[$language_id])),
          'categories_meta_text' => tep_db_prepare_input($categories_meta_text[$language_id]),
          'seo_name' => tep_db_prepare_input($seo_name[$language_id]),
          'seo_description' => tep_db_prepare_input($seo_description[$language_id]),
          'categories_header_text' => tep_db_prepare_input($categories_header_text[$language_id]),
          'categories_footer_text' => tep_db_prepare_input($categories_footer_text[$language_id]),
          'text_information' => tep_db_prepare_input($text_information[$language_id]),
          'meta_keywords' => tep_db_prepare_input($meta_keywords[$language_id]),
          'meta_description' => tep_db_prepare_input($meta_description[$language_id]),
          'character_romaji' => tep_db_prepare_input($character_romaji[$language_id]),
          'alpha_romaji' => tep_db_prepare_input($alpha_romaji[$language_id]),
          'last_modified' => date('Y-m-d H:i:s', time()),
          'user_last_modified' => $_SESSION['user_name'],
          );

      if ($_GET['action'] == 'insert_category' || ($_GET['action'] == 'update_category' && !tep_categories_description_exist($categories_id, $language_id, $site_id))) {

        $insert_sql_data = array('categories_id' => $categories_id,
            'language_id'   => $languages[$i]['id'],
            'site_id'       => $site_id
            );

        if ($site_id != 0) {
          $has_status_raw = tep_db_query("select categories_status from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$categories_id."' and site_id = '".$site_id."'"); 
          if (!tep_db_num_rows($has_status_raw)) {
            $has_default_status_raw = tep_db_query("select categories_status from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$categories_id."' and site_id = '0'"); 
            $has_default_status = tep_db_fetch_array($has_default_status_raw);  
            if ($has_default_status) {
              $insert_sql_data['categories_status'] = $has_default_status['categories_status']; 
            }
          }
        } 

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
        $image_directory = tep_get_local_path(tep_get_upload_dir($site_id) . 'categories/');

        if (is_uploaded_file($categories_image2['tmp_name'])) {
          tep_db_query("update " . TABLE_CATEGORIES_DESCRIPTION . " set categories_image2 = '" . $categories_image2['name'] . "' where categories_id = '" . tep_db_input($categories_id) . "' and site_id='".$site_id."'");
          tep_copy_uploaded_file($categories_image2, $image_directory);
        }
        //categories_image3 upload => INSERT
        $categories_image3 = tep_get_uploaded_file('categories_image3');
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

    if ((($site_id == '') || ($site_id == 0))) {
      //删除没有关系的mission 
      $sql_del_no_categories_mission = 'DELETE FROM '.TABLE_MISSION.' WHERE id NOT IN (SELECT mission_id FROM '.TABLE_CATEGORIES_TO_MISSION.')';
          $sql_del_no_mission_session = 'delete from '.TABLE_SESSION_LOG.'  WHERE mission_id NOT IN (SELECT mission_id FROM '.TABLE_CATEGORIES_TO_MISSION.')';
            $sql_del_no_mission_record = 'delete from '.TABLE_RECORD.'  WHERE mission_id NOT IN (SELECT mission_id FROM '.TABLE_CATEGORIES_TO_MISSION.')';

              $kWord = trim($_POST['keyword']);
              if(isset($_POST['categories_id'])&&$_POST['categories_id']){
              $categories_id = $_POST['categories_id'];
              }
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
              $mission_to_categories_whith_keyword_exist = "SELECT c2m.categories_id ,c2m.mission_id from " .TABLE_CATEGORIES_TO_MISSION.' c2m, ' .TABLE_MISSION .' m ' ."WHERE m.keyword='".$kWord."' " ."AND c2m.mission_id = m.id " ."AND c2m.categories_id = ".$categories_id;

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
              }
    }

    if (USE_CACHE == 'true') {
      tep_reset_cache_block('categories');
      tep_reset_cache_block('also_purchased');
    }

    if (isset($_GET['rdirect'])) {
      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&cID=' . $categories_id.'&site_id=0'.($_GET['search']?'&search='.$_GET['search']:'')));
    } else {
      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&cID=' . $categories_id.'&site_id='.$site_id.($_GET['search']?'&search='.$_GET['search']:'')));
    }
    break;
    case 'delete_product_description_confirm':
    tep_isset_eof();
    $d_page = (isset($_GET['page']))?'&page='.$_GET['page']:''; 
    if ($_GET['pID'] && $_GET['site_id']) {
      tep_db_query("delete from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$_GET['pID']."' && site_id = '".(int)$_GET['site_id']."'");
    }
    if (isset($_GET['rdirect'])) {
      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&pID='.  (int)$_GET['pID'].'&site_id=0'.$d_page.($_GET['search']?'&search='.$_GET['search']:'')));
    } else {
      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&pID='.  (int)$_GET['pID'].'&site_id='.(int)$_GET['site_id'].$d_page.($_GET['search']?'&search='.$_GET['search']:'')));
    }
    break;
    case 'delete_category_description_confirm':
    tep_isset_eof();
    $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:''; 
    if ($_GET['cID'] && $_GET['site_id']) {
      tep_db_query("delete from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$_GET['cID']."' && site_id = '".(int)$_GET['site_id']."'");
    }
    if (isset($_GET['rdirect'])) {
      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&cID='.  (int)$_GET['cID'].'&site_id=0'.$dc_page.($_GET['search']?'&search='.$_GET['search']:'')));
    } else {
      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&cID='.  (int)$_GET['cID'].'&site_id='.(int)$_GET['site_id'].$dc_page.($_GET['search']?'&search='.$_GET['search']:'')));
    }
    break;
    case 'delete_select_categories_products':
    tep_isset_eof();
    $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:''; 
    if (isset($_POST['categories_id_list']) && is_array($_POST['categories_id_list']) && !empty($_POST['categories_id_list'])) {
    foreach($_POST['categories_id_list'] as $categories_value){
      $categories_id = tep_db_prepare_input($categories_value);

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
    }

    if (isset($_POST['products_id_list']) && is_array($_POST['products_id_list']) && !empty($_POST['products_id_list'])) {
    foreach($_POST['products_id_list'] as $products_value){
      $product_id = tep_db_prepare_input($products_value);
      $product_categories = tep_generate_category_path($product_id, 'product');

      for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
        tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . tep_db_input($product_id) . "' and categories_id = '" . tep_db_input($product_categories[$i][sizeof($product_categories[$i])-1]['id']) . "'");
      }

      $product_categories_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . tep_db_input($product_id) . "'");
      $product_categories = tep_db_fetch_array($product_categories_query);

      if ($product_categories['total'] == '0') {
        tep_remove_product($product_id);
      }  
    }
    }

    if (USE_CACHE == 'true') {
      tep_reset_cache_block('categories');
      tep_reset_cache_block('also_purchased');
    }

    $cPath = isset($cPath) && trim($cPath) != '' ? 'cPath='.$cPath : '';
    tep_redirect(tep_href_link(FILENAME_CATEGORIES, $cPath.$dc_page.($_GET['search']?'&search='.$_GET['search']:'')));
    break;
    case 'delete_category_confirm':
    tep_isset_eof();
    $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:''; 
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

    tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath.$dc_page.($_GET['search']?'&search='.$_GET['search']:'')));
    break;
    case 'delete_product_confirm':
    tep_isset_eof();
    $d_page = (isset($_GET['page']))?'&page='.$_GET['page']:''; 
    if ( ($_POST['products_id']) && (is_array($_POST['product_categories'])) ) {
      $product_id = tep_db_prepare_input($_POST['products_id']);
      $product_categories = $_POST['product_categories'];


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

    tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath.$d_page.($_GET['search']?'&search='.$_GET['search']:'')));
    break;
    case 'move_category_confirm':
    tep_isset_eof();
    if ( ($_POST['categories_id']) && ($_POST['categories_id'] != $_POST['move_to_category_id']) ) {
      $categories_id = tep_db_prepare_input($_POST['categories_id']);
      $new_parent_id = tep_db_prepare_input($_POST['move_to_category_id']);
      $categories_id_query = tep_db_query("select romaji from ". TABLE_CATEGORIES_DESCRIPTION ." where categories_id='". $categories_id ."'");
      $categories_id_array = tep_db_fetch_array($categories_id_query);
      tep_db_free_result($categories_id_query);
      $categories_id_code = $categories_id_array['romaji']; 
      $categories_new_id_query = tep_db_query("select distinct c_d.romaji as cd_romaji from ". TABLE_CATEGORIES ." as c left join ". TABLE_CATEGORIES_DESCRIPTION." as c_d on c.categories_id=c_d.categories_id where parent_id='". $new_parent_id ."'");
      $categories_new_id_code = array();
      while($categories_new_id_array = tep_db_fetch_array($categories_new_id_query)){

        $categories_new_id_code[] = $categories_new_id_array['cd_romaji'];
      }
      tep_db_free_result($categories_new_id_query);
      $move_flag == false;
      if(!empty($categories_new_id_code)){

        if(in_array($categories_id_code,$categories_new_id_code)){
          //判断是否到其子分类下
          $messageStack->add(ERROR_MOVE_CATEGORY, 'error');
        }else{
          $move_flag = true; 
        } 
      }else{
        $move_flag = true; 
      }

      if($move_flag == true){
        tep_db_query("update " . TABLE_CATEGORIES . " set parent_id = '" . tep_db_input($new_parent_id) . "', last_modified = now() where categories_id = '" . tep_db_input($categories_id) . "'");

        if (USE_CACHE == 'true') {
          tep_reset_cache_block('categories');
          tep_reset_cache_block('also_purchased');
        }
      }
    }
    if($move_flag == true){
      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $new_parent_id . '&cID=' . $categories_id));
    }
    break;
    case 'move_product_confirm':
    tep_isset_eof();
    $products_id = tep_db_prepare_input($_POST['products_id']);
    $new_parent_id = tep_db_prepare_input($_POST['move_to_category_id']);

    $products_id_query = tep_db_query("select romaji from ". TABLE_PRODUCTS_DESCRIPTION ." where products_id='". $products_id ."'");
    $products_id_array = tep_db_fetch_array($products_id_query);
    tep_db_free_result($products_id_query);
    $products_id_code = $products_id_array['romaji']; 
    $products_code_id_query = tep_db_query("select distinct p_d.romaji as pd_romaji from ". TABLE_PRODUCTS_DESCRIPTION ." as p_d left join ". TABLE_PRODUCTS_TO_CATEGORIES ." as p_t_c on p_d.products_id=p_t_c.products_id where p_t_c.categories_id='". $new_parent_id ."'");
    $products_code_array = array();
    while($products_code_id_array = tep_db_fetch_array($products_code_id_query)){

      $products_code_array[] = $products_code_id_array['pd_romaji'];
    }
    tep_db_free_result($products_code_id_query);
    $move_flag = false;
    if(!empty($products_code_array)){

      if(in_array($products_id_code,$products_code_array)){
        //判断该商品的罗马字在要移动要的分类下的商品的罗马字是否存在
        $messageStack->add(ERROR_MOVE_CATEGORY, 'error');
      }else{
        $move_flag = true; 
      }
    }else{
      $move_flag = true; 
    }
    if($move_flag == true){
      $duplicate_check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . tep_db_input($products_id) . "' and categories_id = '" . tep_db_input($new_parent_id) . "'");
      $duplicate_check = tep_db_fetch_array($duplicate_check_query);
      if ($duplicate_check['total'] < 1) tep_db_query("update " . TABLE_PRODUCTS_TO_CATEGORIES . " set categories_id = '" . tep_db_input($new_parent_id) . "' where products_id = '" . tep_db_input($products_id) . "' and categories_id = '" . $current_category_id . "'");

      if (USE_CACHE == 'true') {
        tep_reset_cache_block('categories');
        tep_reset_cache_block('also_purchased');
      }

      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $new_parent_id . '&pID=' . $products_id));
    }
    break;
    case 'insert_product':
    case 'update_product':
    tep_isset_eof();
    $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
    if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
    else $site_arr="";
    forward401Unless(editPermission($site_arr, $site_id));

    if ( (isset($_POST['edit_x']) && $_POST['edit_x']) || (isset($_POST['edit_y']) && $_POST['edit_y']) ) {
      $_GET['action'] = 'new_product';
    } else {
      $products_id = tep_db_prepare_input($_GET['pID']);

      if(isset($_POST['products_image2_del']) && $_POST['products_image2_del'] == 'none') {
        $_POST['products_image2'] = 'none';
      }

      if(isset($_POST['products_image3_del']) && $_POST['products_image3_del'] == 'none') {
        $_POST['products_image3'] = 'none';
      }
      //指定%的情况下，计算价格
      $HTTP_POST_VARS['products_price_offset'] = SBC2DBC($HTTP_POST_VARS['products_price_offset']);

      $products_attention_1_1 = tep_db_prepare_input($_POST['products_attention_1_1']);
      $products_attention_1_2 = tep_db_prepare_input($_POST['products_attention_1_2']);
      $products_attention_1_3 = tep_db_prepare_input($_POST['products_attention_1_3']);
      $products_attention_1_4 = tep_db_prepare_input($_POST['products_attention_1_4']);
      $products_attention_1 = tep_db_prepare_input($_POST['products_jan']);
      $products_attention_2 = tep_db_prepare_input($_POST['products_size']);
      $products_attention_3 = tep_db_prepare_input($_POST['products_naiyou']);
      $products_attention_4 = tep_db_prepare_input($_POST['products_zaishitu']);
      $products_attention_5 = tep_db_prepare_input($_POST['products_attention_5']);

      $sql_data_array = array(
          'products_real_quantity' => tep_db_prepare_input($_POST['products_real_quantity']),
          'products_model' => tep_db_prepare_input($_POST['products_model']),
          'products_attention_1_1' => $products_attention_1_1,
          'products_attention_1_2' => $products_attention_1_2,
          'products_attention_1_3' => $products_attention_1_3,
          'products_attention_1_4' => $products_attention_1_4,
          'products_attention_1' => $products_attention_1,
          'products_attention_2' => $products_attention_2,
          'products_attention_3' => $products_attention_3,
          'products_attention_4' => $products_attention_4,
          'products_attention_5' => $products_attention_5,
          'products_price' =>
          tep_db_prepare_input($_POST['products_bflag'])? 0 - abs(tep_db_prepare_input($_POST['products_price'])):abs(tep_db_prepare_input($_POST['products_price'])),
          'products_price_offset' => tep_db_prepare_input($HTTP_POST_VARS['products_price_offset']),
          'products_shipping_time' => tep_db_prepare_input($_POST['products_shipping_time']),
          'products_weight' => tep_db_prepare_input($_POST['products_shipping_weight']),
          'products_status' => tep_db_prepare_input($_POST['products_status']),
          'products_tax_class_id' => tep_db_prepare_input($_POST['products_tax_class_id']),
          'manufacturers_id' => tep_db_prepare_input($_POST['manufacturers_id']),
          'products_bflag' => tep_db_prepare_input($_POST['products_bflag']),
          'option_type' => tep_db_prepare_input($_POST['option_type']),
          'relate_products_id' => tep_db_prepare_input($_POST['relate_products_id']),
          'products_small_sum' => tep_db_prepare_input($_POST['products_small_sum']),
          'products_cartorder' => tep_db_prepare_input($_POST['products_cartorder']),
          );
      //处理 发售日 时间
      if(!isset($site_id)||$site_id==''||$site_id==0){
        $products_date_available = tep_db_prepare_input($_POST['products_date_available']);
        $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';
        $sql_data_array = array_merge($sql_data_array,
            array(
              'products_date_available' => $products_date_available,
              'products_cart_min' => tep_db_prepare_input($_POST['products_cart_min']),
              'products_cartflag' => tep_db_prepare_input($_POST['products_cartflag']),
              'products_cart_buyflag' => tep_db_prepare_input($_POST['products_cart_buyflag']),
              'sort_order' => tep_db_prepare_input($_POST['sort_order'])));
      }


      if ($_POST['products_image']) {
        $sql_data_array['products_image'] = (($_POST['products_image'] == 'none') ? '' : tep_db_prepare_input($_POST['products_image']));
      }
      if ($_POST['products_image2']) {
        $sql_data_array['products_image2'] = (($_POST['products_image2'] == 'none') ? '' : tep_db_prepare_input($_POST['products_image2']));
      }
      if ($_POST['products_image3']) {
        $sql_data_array['products_image3'] = (($_POST['products_image3'] == 'none') ? '' : tep_db_prepare_input($_POST['products_image3']));
      }
      if ($_POST['products_cart_image']) {
        $sql_data_array['products_cart_image'] = (($_POST['products_cart_image'] == 'none') ? '' : tep_db_prepare_input($_POST['products_cart_image']));
      }


      if ($_GET['action'] == 'insert_product') {
        if ($site_id == 0) {
          $option_group_raw = tep_db_query('select id from '.TABLE_OPTION_GROUP.' where name = \''.$_POST['option_keyword'].'\''); 
          $option_group_res = tep_db_fetch_array($option_group_raw);
          if ($option_group_res) {
            $sql_data_array['belong_to_option'] = $option_group_res['id']; 
          } 
        } 
        $insert_sql_data = array('products_date_added' => 'now()','products_user_added'=>$_POST['products_user_added']);
        $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
        tep_db_perform(TABLE_PRODUCTS, $sql_data_array);
        $products_id = tep_db_insert_id();
        tep_db_query("insert into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) values ('" . $products_id . "', '" . $current_category_id . "')");
      } elseif ($_GET['action'] == 'update_product') {
        if ($site_id == 0) {
          $option_group_raw = tep_db_query('select id from '.TABLE_OPTION_GROUP.' where name = \''.$_POST['option_keyword'].'\''); 
          $option_group_res = tep_db_fetch_array($option_group_raw);
          if ($option_group_res) {
            $sql_data_array['belong_to_option'] = $option_group_res['id']; 
          } else {
            $sql_data_array['belong_to_option'] = ''; 
          }
        } 
        tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . tep_db_input($products_id) . '\'');
        if($site_id=="" || $site_id==0){
          $update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now(),products_user_update='".$_POST['products_user_update']."' where products_id='".$products_id."'";
          tep_db_query($update_sql);
        }else{
          $update_sql = "update ".TABLE_PRODUCTS_DESCRIPTION." set products_last_modified=now(),products_user_update='".$_POST['products_user_update']."' where products_id='".$products_id."' and site_id='".$site_id."'";
          tep_db_query($update_sql);
        }
      }
      
      //如果SESSION存在的话，就读取SESSION的数据，不存在，则读取POST的
      if(isset($_SESSION['carttags_id_list_array']) && !empty($_SESSION['carttags_id_list_array'])){

        if($_GET['pID']){

          $products_id_flag = $_GET['pID'];
        }else{

          if($_GET['cPath']){

            $products_id_flag = $_GET['cPath'];
          }else{
            $products_id_flag = 0;
          } 
        }

        $_POST['carttags'] = $_SESSION['carttags_id_list_array'][$products_id_flag];
        unset($_SESSION['carttags_id_list_array'][$products_id_flag]);
      } 
      if (isset($_POST['carttags']) && $site_id == '0') {
        tep_db_query("delete from products_to_carttag where products_id='".$products_id."'");
        foreach($_POST['carttags'] as $ck => $ct){
          tep_db_perform('products_to_carttag', array(
                'products_id' => $products_id,
                'tags_id' => $ck,
                'buyflag' => tep_db_prepare_input($_POST['products_cart_buyflag']),
                'created_at' => 'now()'
                ));
        }
      }

      if ($_POST['relate_products_id'] && $products_id) {
        tep_db_query("update ".TABLE_PRODUCTS." set relate_products_id='".$products_id."' where products_id='".$_POST['relate_products_id']."'");
      }

      //add product tags
      if ($site_id == 0 && ((isset($_SESSION['pid_tags_id_list_array']) && !empty($_SESSION['pid_tags_id_list_array'])) || (isset($_POST['tags']) && !empty($_POST['tags'])))) {
        tep_db_query("delete from ".TABLE_PRODUCTS_TO_TAGS." where products_id='".$products_id."'"); 
      } 
      //如果SESSION存在的话，就读取SESSION的数据，不存在，则读取POST的
      if(isset($_SESSION['pid_tags_id_list_array']) && !empty($_SESSION['pid_tags_id_list_array'])){

        if($_GET['pID']){

          $products_id_flag = $_GET['pID'];
        }else{

          if($_GET['cPath']){

            $products_id_flag = $_GET['cPath'];
          }else{
            $products_id_flag = 0;
          } 
        }

        $_POST['tags'] = $_SESSION['pid_tags_id_list_array'][$products_id_flag];
        unset($_SESSION['pid_tags_id_list_array'][$products_id_flag]);
      }
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

      // 按照颜色划分图像开始插入
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
      // 按照颜色划分图像插入完成

      $languages = tep_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
        //结合商品说明
        $des = tep_db_prepare_input($_POST['products_description'][$language_id]);
        $sql_data_array = array(
            'products_name'        => tep_db_prepare_input($_POST['products_name'][$language_id]),
            'romaji' => tep_db_prepare_input(str_replace('_', '-', $_POST['romaji'])),
            'products_description' => $des,
            'products_status' => tep_db_prepare_input($_POST['products_status']),
            'products_url'         => tep_db_prepare_input($_POST['products_url'][$language_id]),
            'preorder_status' => tep_db_prepare_input($_POST['preorder_status']) 
            );
        if ($site_id) {
          $default_preorder_raw = tep_db_query("select preorder_status from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$products_id."' and site_id = '0'"); 
          $default_preorder_res = tep_db_fetch_array($default_preorder_raw);
          if ($default_preorder_res) {
            $sql_data_array['preorder_status'] = $default_preorder_res['preorder_status']; 
          }
        }
        if (isset($_GET['action']) && ($_GET['action'] == 'insert_product' || ($_GET['action'] == 'update_product' && !tep_products_description_exist($products_id,$site_id,$language_id)))) {
          $insert_sql_data = array('products_id' => $products_id,
              'language_id' => $language_id,
              'products_user_update' => $_SESSION['user_name'], 
              'products_last_modified' => date('Y-m-d H:i:s', time()), 
              'site_id' => $site_id);
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
        } elseif ($_GET['action'] == 'update_product') {
          tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \'' . tep_db_input($products_id) . '\' and language_id = \'' . $language_id . '\' and site_id =\''.$site_id.'\'');
        }

        if ($site_id == 0) {
          tep_db_query("update `".TABLE_PRODUCTS_DESCRIPTION."` set `preorder_status` = '".$_POST['preorder_status']."' where products_id = '".$products_id."' and `site_id` != '0'"); 
        }
      }
      // option值插入完成

      if (USE_CACHE == 'true') {
        tep_reset_cache_block('categories');
        tep_reset_cache_block('also_purchased');
      }

      unset($_SESSION['product_history']);
      if (isset($_POST['rdirect'])) {
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .
              '&page='.$_GET['page'].'&pID=' . $products_id.'&site_id=0'.($_GET['search']?'&search='.$_GET['search']:'')));
      } else {
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&page='.$_GET['page'].'&pID=' .  $products_id.'&site_id='.$site_id.($_GET['search']?'&search='.$_GET['search']:'')));
      }
    }
    break;
    case 'copy_to_confirm':
    tep_isset_eof();
    if ( (tep_not_null($_POST['products_id'])) && (tep_not_null($_POST['categories_id'])) ) {
      $products_id   = tep_db_prepare_input($_POST['products_id']);
      $categories_id = tep_db_prepare_input($_POST['categories_id']);

      $products_categories_id_query = tep_db_query("select categories_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where products_id='". $products_id ."'");
      $products_categories_id_array = tep_db_fetch_array($products_categories_id_query);
      tep_db_free_result($products_categories_id_query);

      $move_flag = false;
      if($products_categories_id_array['categories_id'] != $categories_id){

        $products_id_query = tep_db_query("select romaji from ". TABLE_PRODUCTS_DESCRIPTION ." where products_id='". $products_id ."'");
        $products_id_array = tep_db_fetch_array($products_id_query);
        tep_db_free_result($products_id_query);
        $products_id_code = $products_id_array['romaji']; 
        $products_code_id_query = tep_db_query("select distinct p_d.romaji as pd_romaji from ". TABLE_PRODUCTS_DESCRIPTION ." as p_d left join ". TABLE_PRODUCTS_TO_CATEGORIES ." as p_t_c on p_d.products_id=p_t_c.products_id where p_t_c.categories_id='". $categories_id ."'");
        $products_code_array = array();
        while($products_code_id_array = tep_db_fetch_array($products_code_id_query)){

          $products_code_array[] = $products_code_id_array['pd_romaji'];
        }
        tep_db_free_result($products_code_id_query);

        if(!empty($products_code_array)){
          //判断该商品罗马字在指定分类下的商品的罗马字是否重复
          if(in_array($products_id_code,$products_code_array)){

            $messageStack->add(ERROR_MOVE_CATEGORY, 'error');
          }else{
            $move_flag = true; 
          }
        }else{
          $move_flag = true; 
        }
      }else{
        $products_id_query = tep_db_query("select romaji from ". TABLE_PRODUCTS_DESCRIPTION ." where products_id='". $products_id ."'");
        $products_id_array = tep_db_fetch_array($products_id_query);
        tep_db_free_result($products_id_query);
        $products_id_code = $products_id_array['romaji']; 
        $products_code_id_query = tep_db_query("select distinct p_d.romaji as pd_romaji from ". TABLE_PRODUCTS_DESCRIPTION ." as p_d left join ". TABLE_PRODUCTS_TO_CATEGORIES ." as p_t_c on p_d.products_id=p_t_c.products_id where p_t_c.categories_id='". $categories_id ."'");
        $products_code_array = array();
        while($products_code_id_array = tep_db_fetch_array($products_code_id_query)){

          $products_code_array[] = $products_code_id_array['pd_romaji'];
        }
        tep_db_free_result($products_code_id_query);

        if(!empty($products_code_array)){

          $code_num_array = array();
          $products_id_code = str_replace(array('(',')'),array('\(','\)'),$products_id_code);
          foreach($products_code_array as $code_key=>$code_value){ 
            preg_match_all('/^'.$products_id_code.'\(([0-9])\)$/',$code_value,$code_array);
            if($code_array[1][0] != ''){
              $code_num_array[] = $code_array[1][0]; 
            }
          } 
          if(max($code_num_array) < 2){
            $code_str = 2;
          }else{
            $code_str = max($code_num_array)+1;
          }
        }
        $move_flag = true; 
      }
      if($move_flag == true){ 
        if ($_POST['copy_as'] == 'link') {
          //复制链接 
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
          //重复复制 
          $product_query = tep_db_query("
              select *
              from " . TABLE_PRODUCTS . " 
              where products_id = '" . tep_db_input($products_id) . "'
              ");
          $product = tep_db_fetch_array($product_query);
          //products_virtual_quantity, 
          tep_db_query("
              insert into " . TABLE_PRODUCTS . " (
                products_real_quantity, 
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
                products_attention_1_1, 
                products_attention_1_2, 
                products_attention_1_3, 
                products_attention_1_4, 
                products_attention_1,         
                products_attention_2, 
                products_attention_3, 
                products_attention_4,
                products_attention_5,
                belong_to_option
                  ) values (
                    '" . $product['real_quantity'] . "', 
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
                    '" . addslashes($description['products_attention_1_1']) . "', 
                    '" . addslashes($description['products_attention_1_2']) . "', 
                    '" . addslashes($description['products_attention_1_3']) . "', 
                    '" . addslashes($description['products_attention_1_4']) . "', 
                    '" . addslashes($description['products_attention_1']) . "', 
                    '" . addslashes($description['products_attention_2']) . "', 
                    '" . addslashes($description['products_attention_3']) . "', 
                    '" . addslashes($description['products_attention_4']) . "', 
                    '" . addslashes($description['products_attention_5']) . "',
                    '" . $product['belong_to_option'] . "'
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
                  products_status, 
                  romaji
                  ) values (
                    '" . $dup_products_id . "', 
                    '" . $description['language_id'] . "', 
                    '" . addslashes($description['products_name']) . "', 
                    '" . addslashes($description['products_description']) . "', 
                    '" . $description['products_url'] . "', 
                    '0',
                    '" . $description['site_id'] . "', 
                    '" . $description['products_status'] . "', 
                    '" . $description['romaji'].'('.$code_str.')'."'
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
    }
    if($move_flag == true){
      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $categories_id . '&pID=' . $products_id));
    }
    break;
    case 'new_product_preview':
    $_SESSION['product_history'] = $_POST;
    if (!isset($_GET['read'])) { 
      $romaji_error = 0; 
      $romaji_error_str = '';

      $productsId = $_GET['pID'];  
      if (empty($productsId)) {
        if (trim($_POST['romaji']) == '') {
          $romaji_error = 1; 
          $romaji_error_str = TEXT_ROMAJI_NOT_NULL;
        }

        if(!tep_check_symbol($_POST['romaji'])){
          $romaji_error = 1; 
          $romaji_error_str = CATEGORY_ROMAJI_ERROR_NOTICE;
        }

        if(!tep_check_romaji($_POST['romaji'])){
          $romaji_error = 1; 
          $romaji_error_str = TEXT_ROMAJI_ERROR;
        }
        if (isset($_GET['cPath'])) {
          $ca_arr = explode('_', $_GET['cPath']); 
          $belong_ca = $ca_arr[count($ca_arr)-1];
          $exist_ro_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c where pd.products_id = p2c.products_id and pd.site_id = '".$site_id."' and pd.romaji = '".$_POST['romaji']."' and p2c.categories_id = '".$belong_ca."'"); 
          if (tep_db_num_rows($exist_ro_query)) {
            $romaji_error = 1; 
            $romaji_error_str = TEXT_ROMAJI_EXISTS;
          }
        } else {
          if (tep_db_num_rows(tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where romaji = '".$_POST['romaji']."' and site_id = '".$site_id."'"))) {
            $romaji_error = 1; 
            $romaji_error_str = TEXT_ROMAJI_EXISTS;
          }
        }
      } else {
        tep_isset_eof();
        if (trim($_POST['romaji']) == '') {
          $romaji_error = 1; 
          $romaji_error_str = TEXT_ROMAJI_NOT_NULL;
        }
        if(!tep_check_symbol($_POST['romaji'])){
          $romaji_error = 1; 
          $romaji_error_str = CATEGORY_ROMAJI_ERROR_NOTICE;
        }
        if(!tep_check_romaji($_POST['romaji'])){
          $romaji_error = 1; 
          $romaji_error_str = TEXT_ROMAJI_ERROR;
        }
        if (isset($_GET['cPath'])) {
          $ca_arr = explode('_', $_GET['cPath']); 
          $belong_ca = $ca_arr[count($ca_arr)-1];
          $exist_ro_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c where pd.products_id = p2c.products_id and pd.site_id = '".$site_id."' and pd.romaji = '".$_POST['romaji']."' and p2c.categories_id = '".$belong_ca."' and pd.products_id != '".$_GET['pID']."'"); 
          if (tep_db_num_rows($exist_ro_query)) {
            $romaji_error = 1; 
            $romaji_error_str = TEXT_ROMAJI_EXISTS;
          }
        } else {
          if (tep_db_num_rows(tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where romaji = '".$_POST['romaji']."' and site_id = '".$site_id."' and products_id != '".$_GET['pID']."'"))) {
            $romaji_error = 1; 
            $romaji_error_str = TEXT_ROMAJI_EXISTS;
          }
        }
      }

      if ($romaji_error == 1) {
        $_GET['action'] = 'new_product'; 
        break;
      }
    }
  }
}

// check if the catalog image directory exists
if (file_exists(tep_get_upload_root())) {
  if (!is_writeable(tep_get_upload_root())) $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
} else {
  $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
}

//删除商品图像
if (isset($_GET['mode']) && $_GET['mode'] == 'p_delete') {
  $image_location  = tep_get_upload_dir($site_id). 'products/' . $_GET['file'];//原始图像
  $image_location2 = tep_get_upload_dir($site_id) .'imagecache3/'. $_GET['file'];//缩略图
  $delete_image = $_GET['cl'];
  if (file_exists($image_location)) @unlink($image_location);
  if (file_exists($image_location2)) @unlink($image_location2);
  tep_db_query("update  " . TABLE_PRODUCTS . " set ".$delete_image." = '' where products_id  = '" . $_GET['pID'] . "'");
  tep_redirect(tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&action='.$_GET['action']));
  $messageStack->add(CATEGORY_PIC_DEL_SUCCESS_NOTICE, 'success');
}
if (isset($_GET['mode']) && $_GET['mode'] == 'c_delete') {
  $image_location  = tep_get_upload_dir($site_id). 'carttags/' . $_GET['file'];//原始图像
  $delete_image = $_GET['cl'];
  if (file_exists($image_location)) @unlink($image_location);
  tep_db_query("update  " . TABLE_PRODUCTS . " set ".$delete_image." = '' where products_id  = '" . $_GET['pID'] . "'");
  tep_redirect(tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&action='.$_GET['action']));
  $messageStack->add(CATEGORY_PIC_DEL_SUCCESS_NOTICE, 'success');
}

/*-----------------------------------
 功能：产品复选框
 参数：$cid(string) 类别ID值
 参数：$products_id_array(array)商品ID数组 
 返回值：无
 ----------------------------------*/ 
  function products_box($cid,$products_id_array){
      global $checked_flag,$i;
      $products_query = tep_db_query("select * from products p,products_to_categories p2c,products_description pd where p.products_id=pd.products_id and p2c.products_id=pd.products_id and pd.site_id=0 and p2c.categories_id='".$cid."' order by p.sort_order, pd.products_name, pd.products_id");
      if (tep_db_num_rows($products_query)) {
        echo '<ul id="p_'.$categories['categories_id'].'" class="products_box"'.($cid == 0 ? ' style="padding-left:0;"' : '').'>'."\n";
        while($products = tep_db_fetch_array($products_query)) {
          if(!empty($products_id_array)){
            if(in_array($products['products_id'],$products_id_array)){

              $checked = ' checked';
            }else{
              $checked = ''; 
              $checked_flag = false;
            } 
          }
          echo '<li>'."\n";
          echo '<input type="checkbox" class="products_checkbox" name="products_id[]" id="products_'.$products['products_id'].'" value="'.$products['products_id'].'"'.$checked.'>'.$products['products_name']."\n";
          echo '</li>'."\n";
          $i++;
        }
        echo '</ul>'."\n";
      }else{
        $checked_flag = false; 
      }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php 
$site_id = isset($_GET['site_id']) ? $_GET['site_id']:0;
if((isset($_GET['action']) && $_GET['action']=='new_product_preview') && (isset($_GET['pID']) && $_GET['pID'])){
  $products_query = tep_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$_GET['pID']."' and site_id='".$site_id."'");
  $products_array = tep_db_fetch_array($products_query);
  echo $products_array['products_name'];
}else if(isset($_GET['cPath']) && $_GET['cPath']!=""){
  if(strpos($_GET['cPath'],"_")){
    $cpath_arr = explode("_",$_GET['cPath']);
    $cpath = end($cpath_arr);
  }else{
    $cpath = $_GET['cPath']	;
  }
  $categories_query = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cpath."' and site_id='".$site_id."'");
  $categories_array = tep_db_fetch_array($categories_query);
  echo HEADING_TITLE.$categories_array['categories_name'];
}

else{
  echo HEADING_TITLE;
}

$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=[^&]+/',$belong,$belong_action_array);
preg_match_all('/cPath=[^&]+/',$belong,$belong_array);
preg_match_all('/pID=[^&]+/',$belong,$belong_pid_array);
if($belong_array[0][0] != ''){
  if(preg_match('/action=new_product_preview/',$belong)){
    if(preg_match('/read=only/',$belong)){
      $belong = $href_url.'?'.$belong_array[0][0].'|||'.$belong_pid_array[0][0];
    }else{
      if($belong_pid_array[0][0] != ''){
        $belong = $href_url.'?'.$belong_array[0][0].'|||'.$belong_action_array[0][0].'|||'.$belong_pid_array[0][0]; 
      }else{
        $belong = $href_url.'?action=new_product'; 
      }
    }
  }else{
    if($belong_array[0][0] != 'cPath=0'){
      if(preg_match('/action=new_product/',$belong)){
        if($belong_pid_array[0][0] != ''){
          $belong = $href_url.'?'.$belong_array[0][0].'|||action=new_product|||'.$belong_pid_array[0][0];
        }else{
          $belong = $href_url.'?'.$belong_array[0][0].'|||action=new_product'; 
        }
      }else{
        $belong = $href_url.'?'.$belong_array[0][0]; 
      }
    }else{
      if(preg_match('/action=new_product/',$belong)){
        $belong = $href_url.'?action=new_product';
      }else{
        $belong = $href_url; 
      }
    }
  }
}else{
  if(preg_match('/action=new_product_preview/',$belong)){
    if($belong_pid_array[0][0] != ''){
      $belong = $href_url.'?'.$belong_pid_array[0][0];
    }else{
      $belong = $href_url.'?action=new_product'; 
    }
  }else if(preg_match('/action=new_product/',$belong)){
    $belong = $href_url.'?action=new_product'; 
  }else{
    $belong = $href_url; 
  }
}
$belong = str_replace('0_','',$belong);
?>
</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/jquery.autocomplete.css">
<script language="javascript" >
<?php tep_get_javascript('general','includes');?>
</script>
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/udlr.js"></script>
<script type="text/javascript" >
<?php tep_get_javascript('c_admin','includes|set');?>
</script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/3.4.1/build/yui/yui.js"></script>
<script language="javascript" src="includes/jquery.form.js"></script>
<script language="javascript" >
<?php tep_get_javascript('one_time_pwd','includes|javascript');?>
</script>
<script language="javascript">
<?php //隐藏或显示推荐商品的关联选项?>
function cattags_show(num){

  if(num == 0){

    $("#cattags_list").hide();
    $("#cattags_title").hide();
    $("#cattags_contents").hide();
  }else{
    $("#cattags_list").show();
    $("#cattags_title").show();
    $("#cattags_contents").show();
  }
}
<?php //切换排序的动作?>
function change_sort_tags(sort_type)
{
  url = '<?php echo FILENAME_CATEGORIES;?>?action=<?php echo $_GET['action'];?>&sort=' +sort_type;
  window.location.href = url;
}

<?php //多选框清空动作?>
function all_reset_tags(tags_list_id)
{ 
  if (document.edit_tags.elements[tags_list_id]) {
    if (document.edit_tags.elements[tags_list_id].length == null) {
        document.edit_tags.elements[tags_list_id].checked = false;
    } else {
      for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
        document.edit_tags.elements[tags_list_id][i].checked = false;
      }
    }
  }
}

<?php //多选框全选动作?>
function all_select_tags(tags_list_id)
{
  if(document.edit_tags.all_check){
    var check_flag = document.edit_tags.all_check.checked;
  }else{

    var check_flag = true;
  }
  if (document.edit_tags.elements[tags_list_id]) {
    if (document.edit_tags.elements[tags_list_id].length == null) {
      if (check_flag == true) {
        document.edit_tags.elements[tags_list_id].checked = true;
      } else {
        document.edit_tags.elements[tags_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
        if (check_flag == true) {
          document.edit_tags.elements[tags_list_id][i].checked = true;
        } else {
          document.edit_tags.elements[tags_list_id][i].checked = false;
        }
      }
    }
  }
}

<?php //删除商品的关联时的确认提示?>
function delete_select_products_tags(tags_list_id)
{
   
    if (confirm('<?php echo TEXT_PRODUCTS_DELETE_TAGS_CONFIRM;?>')) {
      document.edit_tags.action = '<?php echo FILENAME_CATEGORIES;?>?action=products_tags_delete';
      document.edit_tags.submit(); 
    }else{

      document.getElementsByName("select_edit_tags")[0].value = 0;
      document.getElementsByName("select_edit_tags")[1].value = 0;
    } 
}

<?php //商品关联标签时，判断多选框是否被选中?>
function edit_products_tags_check(tags_list_id)
{ 
  var options = {
    url: 'categories.php?action=edit_products_tags',
    type:  'POST',
    success: function() {
        alert('<?php echo TEXT_EDIT_TAGS_SAVE_SUCCESS;?>');
    }
  };
  $('#edit_tags_id').ajaxSubmit(options);
  return false; 
}

<?php //标签关联商品时，判断多选框是否被选中?>
function setting_products_tags(tags_list_id)
{
  sel_num = 0;
  if (document.edit_tags.elements[tags_list_id].length == null) {
    if (document.edit_tags.elements[tags_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
      if (document.edit_tags.elements[tags_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  }
  
  if (sel_num == 1) {
    document.edit_tags.action = '<?php echo FILENAME_CATEGORIES;?>?action=setting_products_tags';
    document.edit_tags.submit(); 
  } else {
    document.getElementsByName("select_edit_tags")[0].value = 0;
    document.getElementsByName("select_edit_tags")[1].value = 0;
    alert('<?php echo TEXT_TAGS_MUST_SELECT;?>'); 
  }
}

<?php //针对不同的方法，执行不同的动作?>
function select_type_changed(value)
{
  switch(value){
 
  case '1':
    setting_products_tags('tags_list_id[]');
    break;
  case '2': 
    delete_select_products_tags('tags_list_id[]');
    break;
  }
}

<?php //删除分类及商品时，判断多选框是否被选中及删除时的确认提示?>
function delete_select_products(categories_list_id,products_list_id)
{ 
  var sel_num = 0;
  if(document.myForm1.elements[categories_list_id]){
    if (document.myForm1.elements[categories_list_id].length == null) {
      if (document.myForm1.elements[categories_list_id].checked == true) {
        sel_num = 1;
      }
    } else {
      for (i = 0; i < document.myForm1.elements[categories_list_id].length; i++) {
        if (document.myForm1.elements[categories_list_id][i].checked == true) {
          sel_num = 1;
          break;
        }
      }
    }
  }

  var sel_num_products = 0;
  if(document.myForm1.elements[products_list_id]){ 
    if (document.myForm1.elements[products_list_id].length == null) {
      if (document.myForm1.elements[products_list_id].checked == true) {
        sel_num_products = 1;
      }
    } else {
      for (i = 0; i < document.myForm1.elements[products_list_id].length; i++) {
        if (document.myForm1.elements[products_list_id][i].checked == true) {
          sel_num_products = 1;
          break;
        }
      }
    }
  }
  
  if (sel_num+sel_num_products >= 1) {
    if (confirm('<?php echo TEXT_TAGS_DELETE_CONFIRM;?>')) {
      document.myForm1.action = '<?php echo FILENAME_CATEGORIES;?>?action=delete_select_categories_products&<?php echo $_SERVER['QUERY_STRING'];?>';
      document.myForm1.submit(); 
    }else{
      document.getElementsByName("products_to_tags")[0].value = 0;
    }
  } else {
    document.getElementsByName("products_to_tags")[0].value = 0;
    alert('<?php echo TEXT_TAGS_MUST_SELECT;?>'); 
  }
}

<?php //商品关联标签时，判断多选框是否被选中?>
function select_products_to_tags(categories_list_id,products_list_id)
{ 
  var sel_num = 0;
  if(document.myForm1.elements[categories_list_id]){
    if (document.myForm1.elements[categories_list_id].length == null) {
      if (document.myForm1.elements[categories_list_id].checked == true) {
        sel_num = 1;
      }
    } else {
      for (i = 0; i < document.myForm1.elements[categories_list_id].length; i++) {
        if (document.myForm1.elements[categories_list_id][i].checked == true) {
          sel_num = 1;
          break;
        }
      }
    }
  }

  var sel_num_products = 0;
  if(document.myForm1.elements[products_list_id]){ 
    if (document.myForm1.elements[products_list_id].length == null) {
      if (document.myForm1.elements[products_list_id].checked == true) {
        sel_num_products = 1;
      }
    } else {
      for (i = 0; i < document.myForm1.elements[products_list_id].length; i++) {
        if (document.myForm1.elements[products_list_id][i].checked == true) {
          sel_num_products = 1;
          break;
        }
      }
    }
  }
  
  if (sel_num+sel_num_products >= 1) {
    document.myForm1.action = '<?php echo FILENAME_CATEGORIES;?>?action=products_to_tags<?php echo $_SERVER['QUERY_STRING'] != '' ? '&'.$_SERVER['QUERY_STRING'] : '';?>';
    document.myForm1.submit(); 
  } else {
    document.getElementsByName("products_to_tags")[0].value = 0;
    alert('<?php echo TEXT_TAGS_MUST_SELECT;?>'); 
  }
}
<?php //对选择动作的处理?>
function products_tags_change(value){

  if(value == '1'){

    select_products_to_tags('categories_id_list[]','products_id_list[]'); 
  }
  if(value == '2'){

    delete_select_products('categories_id_list[]','products_id_list[]');
  }
}
<?php //多选框全选动作?>
function all_products_check(products_list_id)
{
  var check_flag = document.myForm1.all_check.checked;
  if (document.myForm1.elements[products_list_id]) {
    if (document.myForm1.elements[products_list_id].length == null) {
      if (check_flag == true) {
        document.myForm1.elements[products_list_id].checked = true;
      } else {
        document.myForm1.elements[products_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.myForm1.elements[products_list_id].length; i++) {
        if (check_flag == true) {
          document.myForm1.elements[products_list_id][i].checked = true;
        } else {
          document.myForm1.elements[products_list_id][i].checked = false;
        }
      }
    }
  }
}

<?php //判断是否选中了具体商品?>
function products_tags_submit(){

  var submit_flag = false;
  $(".products_checkbox").each(function(){

    if($(this).attr('checked') == 'checked'){
    
      submit_flag = true; 
    }
  }); 
  if(submit_flag == true){

    document.products_to_tags.submit();
  }else{

    alert('<?php echo TEXT_PRODUCTS_TAGS_CHECK;?>');
  }
}
<?php //多选框全选动作?>
function all_select_products(tags_list_id)
{
  var check_flag = document.products_to_tags.all_check.checked;
  if (document.products_to_tags.elements[tags_list_id]) {
    if (document.products_to_tags.elements[tags_list_id].length == null) {
      if (check_flag == true) {
        document.products_to_tags.elements[tags_list_id].checked = true;
      } else {
        document.products_to_tags.elements[tags_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.products_to_tags.elements[tags_list_id].length; i++) {
        if (check_flag == true) {
          document.products_to_tags.elements[tags_list_id][i].checked = true;
        } else {
          document.products_to_tags.elements[tags_list_id][i].checked = false;
        }
      }
    }
  }
}
<?php //类别ID开关 ?>
function switch_categories(cid){
  if ($('#d_'+cid).css('display') == 'block') {
    $('#d_'+cid).css('display', 'none');
  } else {
    $('#d_'+cid).css('display', 'block');
  }
}
<?php //检查全部 ?>
function check_all(cid){
  if ($('#categories_'+cid).attr('checked')) {
    $('#d_'+cid+' input[type=checkbox]').attr('checked','checked');
  } else {
    $('#d_'+cid+' input[type=checkbox]').removeAttr('checked');
  }
}

window.onresize = resizepage;
<?php //浏览器窗口缩放时执行的函数?>
function resizepage(){
  if($(".box_warp").height() < $(".compatible").height()){
    $(".box_warp").height($(".compatible").height());
  }
}
$(document).ready(function(){
    if(document.getElementsByName("select_edit_tags")[0]){
      document.getElementsByName("select_edit_tags")[0].value = 0;
    }
    if(document.getElementsByName("select_edit_tags")[1]){
      document.getElementsByName("select_edit_tags")[1].value = 0;
    }
    if(document.getElementsByName("products_to_tags")[0]){
      document.getElementsByName("products_to_tags")[0].value = 0;
    }
    if($(".box_warp").height() < $(".compatible").height()){
      $(".box_warp").height($(".compatible").height()+100);
    }
    $(".udlr").udlr(); 
    ajaxLoad('<?php echo $cPath;?>', '<?php echo empty($_GET['site_id'])?'1':'0';?>'); 
    }); 
<?php //设置图片的说明?>
function set_image_alt_and_title(_this,pid,limit_time_info,limit_flag){
  $.ajax({
type:'POST',
dataType: 'text',
url: 'categories.php?action=get_last_order_date',
data: 'pid='+pid+'&limit_time='+limit_time_info+'&single='+limit_flag,
async:false,
success: function(text) {
  if((text.indexOf('</body>')>0)&&(text.indexOf('</html>')>0)){
    alert("<?php echo TEXT_TIMEOUT_RELOGIN;?>");
    window.location.reload();
  }
  $(_this).attr('alt',text+'<?php echo PIC_MAE_ALT_TEXT;?>');
  $(_this).attr('title',text+'<?php echo PIC_MAE_ALT_TEXT;?>');
}
});
}
<?php //关联商品的下拉列表?>
function relate_products1(cid,rid){
  $.ajax({
dataType: 'text',
url: 'categories.php?action=get_products&cid='+cid+'&rid='+rid,
success: function(text) {
$('#relate_products').html(text);
}
});
}
<?php //点击确认后跳转到指定页面?>
function confirmg(question,url) {
  var x = confirm(question);
  if (x) {
    window.location = url;
  }
}
<?php //检查分类名字和罗马字是否正确?>
function cmess(pid, cid, site_id) {
  if (document.getElementById('cname').value == "") {
    alert('<?php echo ERROR_CATEGORY_NAME_IS_NOT_NULL;?>'); 
    return false; 
  }

  if (document.getElementById('cromaji').value == "") {
    alert('<?php echo TEXT_ROMAJI_NOT_NULL;?>'); 
    return false; 
  }

  flag1 = c_is_set_romaji(pid,cid,site_id);
  flag2 = c_is_set_error_char(true); 

  if(flag1&&flag2){
    return true;
  }else{
    return false;
  }

}
<?php //检查商品名字和罗马字是否为空?>
function mess(){
  if (document.getElementById('pname').value == "") {
    alert('<?php echo ERROR_PRODUCT_NAME_IS_NOT_NULL;?>'); 
    return false; 
  }

  if (document.getElementById('promaji').value == "") {
    alert('<?php echo TEXT_ROMAJI_NOT_NULL;?>'); 
    return false; 
  }
}
<?php //检查价格是否正确?>
function check_price(new_id,old_price,percent){
  $('#'+new_id).css('border-color','');
  new_price = Math.abs($('#'+new_id).val());
  old_price = Math.abs(old_price);
  if (percent != '' && percent != 0 && percent != null) {
    if (new_price > old_price) {
      if( ((new_price - old_price) / old_price) * 100 >= percent ) {
        error_msg = percent+"<?php echo CATEGORY_JS_CHAE_ERROR_TEXT;?>\n";
      }
    } else {
      if( ((old_price - new_price) / new_price) * 100 >= percent ) {
        error_msg = percent+"<?php echo CATEGORY_JS_CHAE_ERROR_TEXT;?>\n";
      }
    }
  }

  if (error_msg != '') {
    alert(error_msg);
    error_msg = '';
  }

  if(confirm("<?php echo CATEGORY_JS_UPDATE_NOTICE;?>")){
    return true;
  }else{
    alert("<?php echo CATEGORY_JS_UPDATE_ERROR_TEXT;?>");
    $('#'+new_id).css('border-color','red');
    $('#'+new_id).focus();
    return false;
  }
}
<?php //计算价格?>
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
<?php //给id等于qt的元素赋值?>
function change_qt(ele){
  qt = ele.innerHTML;
  if (qt) {
    $('#qt').val(qt);
  }
}
<?php //开启提醒商品?>
function get_cart_products(){
  tagstr = '';

  $(".carttags").each(function(){
      start  = $(this).attr('name').indexOf('[') + 1;
      end    = $(this).attr('name').indexOf(']');
      if(this.checked)
      tagstr += '&tags_id[]='+$(this).attr('name').substr(start, end-start);
      });
  if (tagstr != '')
    window.open("categories.php?action=get_cart_products&products_id=<?php echo $_GET['pID'];?>&buyflag="+$("input[@type=radio][name=products_cart_buyflag][checked]").val()+tagstr, '','toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=yes,resizable=yes,width=300');
}
<?php //分类树的开启和关闭?>
function display(){
  offset = $(".pageHeading").offset();
  var categories_tree = document.getElementById('categories_tree'); 
  if(categories_tree.style.display == 'none' || categories_tree.style.display == ''){
    categories_tree.style.top = offset.top + 'px';
    categories_tree.style.display = 'block';
  }else{
    categories_tree.style.display = 'none';
  }
}
<?php //清除属性输入框的值?>
function clear_option()
{
  document.getElementById('option_keyword').value = '';
}
<?php //自动搜索组?>
$(function() {
    function format(group) {
    return group.name;
    }
    $("#option_keyword").autocomplete('ajax_orders.php?action=search_group', {
multipleSeparator: '',
dataType: "json",
parse: function(data) {
return $.map(data, function(row) {
  return {
data: row,
value: row.name,
result: row.name
}
});
},
formatItem: function(item) {
return format(item);
}
}).result(function(e, item) {
  });
});
<?php //跳转到指定的组的页面?>
function handle_option()
{
  var option_value = document.getElementById('option_keyword').value;
  if (option_value != '') {
    $.ajax({
type:'POST',
dataType: 'text',
url: 'ajax_orders.php?action=handle_option',
data:'keyword='+option_value,
async:false,
success: function(msg) {
open_url = "<?php echo HTTP_SERVER;?>"+'/admin/option.php?keyword='+option_value+"&search=2";     
window.open(open_url, 'newwindow', ''); 
}
});  
} 
}
<?php //设置弹出页面的位置 ?>
function info_box_set(ele, current_belong){
  $.ajax({
type:'POST',
dataType: 'text',
url: 'ajax_orders.php?action=get_top_layer',
data: 'current_belong='+current_belong,
async:false,
success: function(msg) {
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_popup_info').height()){
offset = ele.offsetTop+$("#products_list_table").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#products_list_table").position().top-1)) {
offset = ele.offsetTop+$("#products_list_table").position().top-1-$('#show_popup_info').height()+head_top;
} else {
offset = ele.offsetTop+$("#products_list_table").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#products_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#products_list_table").position().top-1)) {
    offset = ele.offsetTop+$("#products_list_table").position().top-1-$('#show_popup_info').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#products_list_table").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#products_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_popup_info').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#products_list_table").position().top-1)) {
      offset = ele.offsetTop+$("#products_list_table").position().top-1-$('#show_popup_info').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#products_list_table").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#products_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#products_list_table").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_popup_info').css('top',offset);
}
box_warp_height = box_warp_height + $('#show_popup_info').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
}
$('#show_popup_info').css('z-index', msg);
$('#show_popup_info').css('left',leftset);
}
});
}
<?php //隐藏弹出页面?>
function hidden_info_box(){
  $('#show_popup_info').css('display','none');
}

var ele_tags_obj = '';
<?php //弹出标签信息?>
function edit_products_tags(ele,tags_id_list,type,url,pid){
  $.ajax({
    url: 'ajax.php?action=edit_products_tags',
    data: 'tags_id_list='+tags_id_list+'&type='+type+'&url='+url+'&pid='+pid,
    type: 'POST', 
    dataType: 'text',  
    async:false,
    success: function(text) {
      ele_obj = $(ele).offset();
      ele_tags_obj = ele;
      $('#show_popup_info').html(text);
      $('#show_popup_info').css('top',ele_obj.top+$(ele).height());
      $('#show_popup_info').css('left',ele_obj.left);
      $('#show_popup_info').css('z-index', 1);
      $('#show_popup_info').css('display','block');
    }
  });
}

<?php //弹出商品信息?>
function show_product_info(pid,ele){
  $.ajax({
dataType: 'text',
url: 'ajax.php?action=product_info_box&pID='+pid+'&site_id=<?php echo (isset($_GET['site_id'])?$_GET['site_id']:'0')."&page=".$_GET['page']."&cPath=".$cPath."&search=".$_GET['search'];?>',
success: function(text) {
//show_p_info 
$('#show_popup_info').html(text);
if(ele!=''){
info_box_set(ele, '<?php echo $belong;?>');
}
$('#show_popup_info').css('display','block');
}
});
}
<?php //弹出商品移动页?>
function show_product_move(pid){
  $.ajax({
dataType: 'text',
url: 'ajax.php?action=product_move_box&pID='+pid+'&site_id=<?php echo $site_id."&page=".$_GET['page']."&cPath=".$cPath."&search=".$_GET['search'];?>',
success: function(text) {
//show_p_info 
$('#show_popup_info').html(text);
$('#show_popup_info').css('display','block');
}
});
}
<?php //弹出商品复制页?>
function show_product_copy(pid){
  $.ajax({
dataType: 'text',
url: 'ajax.php?action=product_copy_to_box&pID='+pid+'&site_id=<?php echo $site_id."&page=".$_GET['page']."&cPath=".$cPath."&search=".$_GET['search'];?>',
success: function(text) {
//show_p_info 
$('#show_popup_info').html(text);
$('#show_popup_info').css('display','block');
}
});
}
<?php //弹出商品删除页?>
function show_product_delete(pid){
  $.ajax({
dataType: 'text',
url: 'ajax.php?action=product_delete_box&pID='+pid+'&site_id=<?php echo $site_id."&page=".$_GET['page']."&cPath=".$cPath."&search=".$_GET['search'];?>',
success: function(text) {
//show_p_info 
$('#show_popup_info').html(text);
$('#show_popup_info').css('display','block');
}
});
}
<?php //弹出商品删除描述页?>
function show_product_description_delete(pid){
  $.ajax({
dataType: 'text',
url: 'ajax.php?action=product_description_delete_box&pID='+pid+'&site_id=<?php echo $site_id."&page=".$_GET['page']."&cPath=".$cPath."&search=".$_GET['search'].(isset($_GET['rdirect'])?'&rdirect='.$_GET['rdirect']:'');?>',
success: function(text) {
//show_p_info 
$('#show_popup_info').html(text);
$('#show_popup_info').css('display','block');
}
});
}
<?php //弹出分类信息?>
function show_category_info(cid,ele){
  $.ajax({
dataType: 'text',
url: 'ajax.php?<?php echo tep_get_all_get_params(array('action'));?>action=show_category_info&current_cid='+cid,
success: function(text) {
$('#show_popup_info').html(text);
if(ele!=''){
info_box_set(ele, '<?php echo $belong;?>');
}
$('#show_popup_info').css('display','block');
}
});
}
<?php //弹出移动分类页?>
function move_category_id(cid){
  $.ajax({
dataType: 'text',
url: 'ajax.php?<?php echo tep_get_all_get_params(array('action', 'current_cid'));?>action=move_category&current_cid='+cid,
success: function(text) {
$('#show_popup_info').html(text);
$('#show_popup_info').css('display','block');
}
});
}
<?php //弹出删除分类页?>
function delete_category_info(cid, del_type){
  $.ajax({
dataType: 'text',
url: 'ajax.php?<?php echo tep_get_all_get_params(array('action', 'current_cid', 'del_type'));?>action=delete_category&current_cid='+cid+'&del_type'+del_type,
success: function(text) {
$('#show_popup_info').html(text);
$('#show_popup_info').css('display','block');
}
});
}
<?php //弹出更新商品价格/进货/库存页?>
function show_update_info(ele, pid, update_type, cnt_num) {
  if (update_type == '1') {
    origin_quantity = $('#virtual_quantity_'+pid).html(); 
    url_str = 'ajax.php?action=update_virtual_quantity';
    data_str = 'pid='+pid+"&origin_num="+origin_quantity;
  } else if (update_type == '2') {
    origin_quantity = $('#quantity_'+pid).html(); 
    url_str = 'ajax.php?action=update_real_quantity'; 
    data_str = 'pid='+pid+"&origin_num="+origin_quantity;
  } else {
    origin_price = $('#h_edit_p_'+pid).html(); 
    url_str = 'ajax.php?action=set_new_price'; 
    data_str = 'pid='+pid+"&origin_price="+origin_price+'&cnt_num='+cnt_num;
  }
  $.ajax({
type:'POST',
dataType:'text',
data:data_str, 
async:false, 
url: url_str,
success: function(text) {
$('#show_popup_info').html(text);
if(ele!=''){
info_box_set(ele, '<?php echo $belong;?>');
}
$('#show_popup_info').css('display','block');
if (update_type == '1') {
$('#virtual_pro_num').select().focus();
} else if (update_type == '2') {
$('#real_pro_num').select().focus();
} else {
$('#new_confirm_price').select().focus();
}
}
});
}
<?php //弹出日历?>
function open_new_calendar()
{
  var is_open = $('#toggle_open').val(); 
  if (is_open == 0) {
    //mm-dd-yyyy || mm/dd/yyyy
    $('#toggle_open').val('1'); 

    var rules = {
      "all": {
        "all": {
          "all": {
            "all": "current_s_day",
          }
        }
      }};
    if ($("#date_orders").val() != '') {
      if ($("#date_orders").val() == '0000-00-00') {
        date_info_str = '<?php echo date('Y-m-d', time())?>';  
        date_info = date_info_str.split('-');  
      } else {
        date_info = $("#date_orders").val().split('-'); 
      }
    } else {
      //mm-dd-yyyy || mm/dd/yyyy
      date_info_str = '<?php echo date('Y-m-d', time())?>';  
      date_info = date_info_str.split('-');  
    }
    new_date = new Date(date_info[0], date_info[1]-1, date_info[2]); 
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
contentBox: "#mycalendar",
width:'170px',
date: new_date

}).render();
        if (rules != '') {
        month_tmp = date_info[1].substr(0, 1);
        if (month_tmp == '0') {
        month_tmp = date_info[1].substr(1);
        month_tmp = month_tmp-1;
        } else {
        month_tmp = date_info[1]-1; 
        }
        day_tmp = date_info[2].substr(0, 1);

        if (day_tmp == '0') {
        day_tmp = date_info[2].substr(1);
        } else {
        day_tmp = date_info[2];   
        }
        data_tmp_str = date_info[0]+'-'+month_tmp+'-'+day_tmp;
        calendar.set("customRenderer", {
rules: rules,
filterFunction: function (date, node, rules) {
cmp_tmp_str = date.getFullYear()+'-'+date.getMonth()+'-'+date.getDate();
if (cmp_tmp_str == data_tmp_str) {
node.addClass("redtext"); 
}
}
});
}
var dtdate = Y.DataType.Date;
calendar.on("selectionChange", function (ev) {
    var newDate = ev.newSelection[0];
    tmp_show_date = dtdate.format(newDate); 
    tmp_show_date_array = tmp_show_date.split('-');
    $(".cal-TextBox").val(tmp_show_date); 
    $("#date_orders").val(tmp_show_date); 
    $('#toggle_open').val('0');
    $('#toggle_open').next().html('<div id="mycalendar"></div>');
    });
});
}
}
$(document).ready(function() {
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_popup_info').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
    if (event.which == 13) {
      <?php //回车?> 
      if ($('#show_popup_info').css('display') != 'none') {
        tmp_click_str = $("#show_popup_info").find('input:button').first().attr('onclick'); 
        tmp_click_symbol = '0'; 
        if (tmp_click_str.indexOf('update_virtual_quantity') >= 0) {
          tmp_click_symbol = '1'; 
        } else if (tmp_click_str.indexOf('update_quantity') >= 0) {
          tmp_click_symbol = '1'; 
        } else if (tmp_click_str.indexOf('set_new_price') >= 0) {
          tmp_click_symbol = '1'; 
        }
        if (tmp_click_symbol == '1') {
          $("#show_popup_info").find('input:button').first().trigger("click"); 
        }else{
          if(ele_tags_obj != ''){
             $("#show_popup_info").find('input:button').first().trigger("click"); 
          } 
        }
      } 
    }
    if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_popup_info').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_popup_info').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});
</script>
<?php 
require("includes/note_js.php");
?>
<script language="javascript" src="includes/javascript/jquery.autocomplete.js"></script>
<style type="text/css">
.categories_box{
  display:block;
}
a.dpicker {
width: 16px;
height: 18px;
border: none;
color: #fff;
padding: 0;
margin:0;
overflow: hidden;
display:block;
cursor: pointer;
background: url(./includes/calendar.png) no-repeat;
}
#new_yui3 {
  margin-left:-168px;
  *margin-left:-28px;
  margin-left:-170px\9;
position: absolute;
          z-index:200px;
          margin-top:15px;
}
@media screen and (-webkit-min-device-pixel-ratio:0) {
#new_yui3{
position: absolute;
          z-index:200px;
          margin-top:17px;
}
}
.yui3-skin-sam img,.yui3-skin-sam input,.date_box{ float:left;}
.yui3-skin-sam .redtext {
color:#0066CC;
}
</style>
</head>
<?php 
if(isset($_GET['eof'])&&$_GET['eof']=='error'){ 
// 数据传输错误 提示信息 
  ?>
    <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="show_error_message()" >
    <div id="popup_info">
    <div class="popup_img"><img onclick="close_error_message()" src="images/close_error_message.gif" alt="close" /></div>
    <span><?php echo TEXT_EOF_ERROR_MSG;?></span>
    </div>
    <div id="popup_box"></div>
    <?php }else{?>
      <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
        <?php } ?>
        <?php
        if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
          <script language='javascript'>
            one_time_pwd('<?php echo $page_name;?>');
          </script>
            <?php }?>
            <div id="spiffycalendar" class="text"></div>
            <!-- header -->
            <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
            <!-- header_eof -->
            <!-- body -->
            <input type="hidden" name="show_info_id" value="show_popup_info" id="show_info_id">
            <div id="show_popup_info" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;">
            </div>
            <div id="categories_tree">
            <?php
            require(DIR_WS_CLASSES . 'category_tree.php');
            $osC_CategoryTree = new osC_CategoryTree; 
            echo $osC_CategoryTree->buildTree();
            ?>
            </div>
            <table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
            <tr>
            <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
            <!-- left_navigation -->
            <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
            <!-- left_navigation_eof -->
            </table></td>
            <!-- body_text -->
            <td width="100%" valign="top" id='categories_right_td'><div class="box_warp">
            <?php echo $notes;?>
            <div class="compatible">
            <?php
            //商品关联标签时，标签显示页面 
            if(isset($_GET['action']) && $_GET['action'] == 'products_to_tags'){

              if(isset($_POST['products_id_list']) && is_array($_POST['products_id_list']) && !empty($_POST['products_id_list']) && empty($_POST['categories_id_list'])){

                if(count($_POST['products_id_list']) == 1){

                  $products_id_num = $_POST['products_id_list'][0];
                  $products_tags_list_array = array();
                  $products_tags_query = tep_db_query("select tags_id from products_to_tags where products_id='".$products_id_num."'");
                  while($products_tags_array = tep_db_fetch_array($products_tags_query)){

                    $products_tags_list_array[] = $products_tags_array['tags_id']; 
                  }
                  tep_db_free_result($products_tags_query);
                }
              }
            ?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo TEXT_PRODUCTS_TO_TAGS_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
          <tr>
            <td><?php echo TEXT_PRODUCTS_TO_TAGS_TITLE_SELECT; ?></td>
            <td class="pageHeading" align="right">
                    <select name="select_edit_tags" onchange="select_type_changed(this.value);">
                      <option value="0"><?php echo TEXT_PRODUCTS_TO_TAGS_SELECT;?></option>
                      <option value="1"><?php echo TEXT_PRODUCTS_TO_TAGS_SETTING;?></option>
                      <option value="2"><?php echo TEXT_PRODUCTS_TO_TAGS_DELETE;?></option>
                    </select>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
          <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2" id="tags_list_box">
              <tr>
              <td colspan='4' align="right">
                 <select onchange="if(options[selectedIndex].value) change_sort_tags(options[selectedIndex].value)">
                 <?php if(!isset($_GET['sort'])){ ?>
                    <option selected="" value="4a"><?php echo LISTING_TITLE_A_TO_Z;?></option> <option value="4d"><?php echo LISTING_TITLE_Z_TO_A;?></option>
                    <option value="5a"><?php echo LISTING_TITLE_A_TO_N;?></option>
                    <option value="5d"><?php echo LISTING_TITLE_N_TO_A;?></option>
                    <?php }else{ 
                    if($_GET['sort']=='4a'){
                      echo '<option selected="" value="4a">'.LISTING_TITLE_A_TO_Z.'</option>';
                    }else{
                      echo '<option value="4a">'.LISTING_TITLE_A_TO_Z.'</option>';
                    }
                    if($_GET['sort']=='4d'){
                      echo '<option selected="" value="4d">'.LISTING_TITLE_Z_TO_A.'</option>';
                    }else{
                      echo '<option value="4d">'.LISTING_TITLE_Z_TO_A.'</option>';
                    } if($_GET['sort']=='5a'){
                      echo '<option selected="" value="5a">'.LISTING_TITLE_A_TO_N.'</option>';
                    }else{
                      echo '<option value="5a">'.LISTING_TITLE_A_TO_N.'</option>';
                    }
                    if($_GET['sort']=='5d'){
                      echo '<option selected="" value="5d">'.LISTING_TITLE_N_TO_A.'</option>';
                    }else{
                      echo '<option value="5d">'.LISTING_TITLE_N_TO_A.'</option>';
                    }
                    }
                    ?>
                 </select>
              </td>
              </tr>
              <form name="edit_tags" method="post" action="<?php echo FILENAME_TAGS;?>">
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td><input type="checkbox" name="all_check" onclick="all_select_tags('tags_list_id[]');"><?php echo TEXT_PRODUCTS_TAGS_ALL_CHECK; ?></td></tr></table></td>
                <?php
            if(isset($_POST['categories_id_list']) && is_array($_POST['categories_id_list']) && !empty($_POST['categories_id_list'])){

              $categories_id_list_str = implode(',',$_POST['categories_id_list']);
            }
            if(isset($_POST['products_id_list']) && is_array($_POST['products_id_list']) && !empty($_POST['products_id_list'])){

              $products_id_list_str = implode(',',$_POST['products_id_list']);
            }
            $tags_url = $_SERVER['QUERY_STRING'];
            $tags_url_array = explode('&',$tags_url); 
            unset($tags_url_array[0]);
            $tags_url = implode('&',$tags_url_array);
                ?>
                  <td cols="3">&nbsp;<input type="hidden" name="categories_id_list" value="<?php echo $categories_id_list_str;?>"><input type="hidden" name="products_id_list" value="<?php echo $products_id_list_str;?>"><input type="hidden" name="tags_url" value="<?php echo $tags_url;?>"></td>
              </tr>
<?php
  //echo MAX_DISPLAY_SEARCH_RESULTS;
  $tags_query_raw = "
  select t.tags_id, t.tags_name, t.tags_images, t.tags_checked, t.user_added,t.date_added,t.user_update,t.date_update
  from " . TABLE_TAGS . " t order by t.tags_order,t.tags_name";
  if(isset($_GET['sort'])&&$_GET['sort']){
    $tags_query_raw = "
      select t.tags_id, t.tags_name, t.tags_images, t.tags_checked
      from " . TABLE_TAGS ." t ";
    switch($_GET['sort']){
/*----------------------------
 case '4a'  排列顺序(a-z) 递增
 case '4d'  排列顺序(z-a) 递减
 case '5a'  排列顺序(あ-ん) 递增
 case '5d'  排列顺序(ん-あ) 递减
 ---------------------------*/
      case '4a':
        $tags_query_raw .=' order by t.tags_name asc'; 
        break;
      case '4d':
        $tags_query_raw .=' order by t.tags_name desc'; 
        break;
      case '5a':
        $tags_query_raw .=' order by t.tags_name asc'; 
        break;
      case '5d':
        $tags_query_raw .=' order by t.tags_name desc'; 
        break;
    }
  }
  $tags_query = tep_db_query($tags_query_raw);
  $tags_sum = tep_db_num_rows($tags_query);
  $tags_cols = ceil($tags_sum/4);
  $tags_number = 0;
  echo '<tr><td width="25%" class="dataTableContent" valign="top"><table width="100%" cellspacing="0" cellpadding="2" border="0">';
  while ($tags = tep_db_fetch_array($tags_query)) {
      if (( (!@$_GET['cID']) || (@$_GET['cID'] == $tags['tags_id'])) && (!@$cInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($tags);
    }
 
    echo '              <tr>' . "\n";
?>
  <td class="dataTableContent" width="10%" valign="top"><input type="checkbox" name="tags_list_id[]" value="<?php echo $tags['tags_id'];?>"<?php echo in_array($tags['tags_id'],$products_tags_list_array) ? ' checked="checked"' : '';?>></td>      
                <td class="dataTableContent"><?php echo $tags['tags_name']; ?></td> 
                <td class="dataTableContent" align="right">&nbsp;</td>
              </tr>
<?php
    $tags_number++;
    if($tags_number % $tags_cols == 0){

      echo '</table></td><td width="25%" class="dataTableContent" valign="top"><table width="100%" cellspacing="0" cellpadding="2" border="0">';
    } 
  }
?>
              </table>
              </td>
              </tr>
              </form>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr>
                    <td colspan="2" align="right">
                    <select name="select_edit_tags" onchange="select_type_changed(this.value);">
                      <option value="0"><?php echo TEXT_PRODUCTS_TO_TAGS_SELECT;?></option>
                      <option value="1"><?php echo TEXT_PRODUCTS_TO_TAGS_SETTING;?></option>
                      <option value="2"><?php echo TEXT_PRODUCTS_TO_TAGS_DELETE;?></option>
                    </select>
                    </td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table>
           <?php
            //商品关联标签时,所有商品显示页面
            }else if(isset($_GET['action']) && $_GET['action'] == 'setting_products_tags'){
              if(isset($_GET['action']) && $_GET['action'] == 'setting_products_tags' && isset( $_POST['tags_list_id']) &&  is_array($_POST['tags_list_id'])){

                $tags_id_array = $_POST['tags_list_id'];
                $tags_id_str = implode(',',$tags_id_array);
                $categories_id_list = $_POST['categories_id_list'];
                $products_id_list = $_POST['products_id_list']; 
                if(trim($categories_id_list) != ''){
                  $categories_id_list = explode(',',$categories_id_list);
                }
                if(trim($products_id_list) != ''){
                  $products_id_list = explode(',',$products_id_list); 
                }
                $products_tags_array = array();
               if(!empty($categories_id_list)){
                foreach($categories_id_list as $categories_id_value){

                  $parent_categories_query = tep_db_query("select parent_id from categories  where categories_id='".$categories_id_value."'");
                  $parent_categories_array = tep_db_fetch_array($parent_categories_query);
                  tep_db_free_result($parent_categories_query);

                  $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$categories_id_value."'");
                  while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                     $products_tags_array[] = $products_id_list_array['products_id'];
                  }
                  tep_db_free_result($products_id_list_query);
                  if($parent_categories_array['parent_id'] == '0'){

                    $child_categories_query = tep_db_query("select categories_id from categories  where parent_id='".$categories_id_value."'");
                    while($child_categories_array = tep_db_fetch_array($child_categories_query)){

                      $parent_categories_id_query = tep_db_query("select categories_id from categories  where parent_id='".$child_categories_array['categories_id']."'");
                      
                      if(tep_db_num_rows($parent_categories_id_query)){
                        
                        while($parent_categories_id_array = tep_db_fetch_array($parent_categories_id_query)){
                          $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$parent_categories_id_array['categories_id']."'");
                          while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                            $products_tags_array[] = $products_id_list_array['products_id'];
                          }
                          tep_db_free_result($products_id_list_query);
                        }
                        tep_db_free_result($parent_categories_id_query);
                        $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$child_categories_array['categories_id']."'");
                        while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                          $products_tags_array[] = $products_id_list_array['products_id'];
                        }
                        tep_db_free_result($products_id_list_query);
                      }else{

                        $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$child_categories_array['categories_id']."'");
                        while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                          $products_tags_array[] = $products_id_list_array['products_id'];
                        }
                        tep_db_free_result($products_id_list_query);
                      }
                    }
                    tep_db_free_result($child_categories_query);
                  }else{

                    $parent_categories_id_query = tep_db_query("select categories_id from categories  where parent_id='".$categories_id_value."'");
                      
                      if(tep_db_num_rows($parent_categories_id_query)){
                        
                        while($parent_categories_id_array = tep_db_fetch_array($parent_categories_id_query)){
                          $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$parent_categories_id_array['categories_id']."'");
                          while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                            $products_tags_array[] = $products_id_list_array['products_id'];
                          }
                          tep_db_free_result($products_id_list_query);
                        }
                        tep_db_free_result($parent_categories_id_query);
                        $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$categories_id_value."'");
                        while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                          $products_tags_array[] = $products_id_list_array['products_id'];
                        }
                        tep_db_free_result($products_id_list_query);
                      }else{

                        $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$categories_id_value."'");
                        while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                          $products_tags_array[] = $products_id_list_array['products_id'];
                        }
                        tep_db_free_result($products_id_list_query);
                      }
                  }
                }
               }
               if(!empty($products_id_list)){
                foreach($products_id_list as $products_id_value){

                  $products_tags_array[] = $products_id_value;
                }
               }
              }
              $tags_url = $_POST['tags_url'];
            ?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo PRODUCTS_TO_TAGS_TITLE;?></td> 
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr> 
        </table></td>
      </tr>
      <tr>
        <td>
  <?php echo tep_draw_form('products_to_tags',FILENAME_CATEGORIES, 'action=products_tags_save', 'post');?>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr> 
          <td valign="top" align="left">&nbsp;<input type="checkbox" name="all_check" onclick="all_select_products('categories_id[]');all_select_products('products_id[]')"><?php echo TEXT_PRODUCTS_TAGS_ALL_CHECK;?><input type="hidden" name="tags_id_list" value="<?php echo $tags_id_str;?>"><input type="hidden" name="tags_url" value="<?php echo $tags_url;?>"><br><td align="right"><input type="button" value="<?php echo IMAGE_SAVE;?>" onclick="products_tags_submit();"></td><table width="100%" class="box_ul"><tr>
<?php
  $i = 0;
  $j = 0;
  $temp_array = array();
  $products_num_query = tep_db_query("select products_id from ". TABLE_PRODUCTS);
  $products_num = tep_db_num_rows($products_num_query);
  tep_db_free_result($products_num_query);
  $products_num = ($products_num/5);
  $categories_query = tep_db_query("select * from categories c,categories_description cd where c.categories_id=cd.categories_id and c.parent_id='0' and cd.site_id='0' order by c.sort_order, cd.categories_name");
  if (tep_db_num_rows($categories_query)) {
    echo "<td width='25%' valign='top'><ul style='padding-left:0;'>"."\n";
    while($categories = tep_db_fetch_array($categories_query)){
      echo '<li>'."\n";
      echo '<input onclick="check_all('.$categories['categories_id'].')" type="checkbox" name="categories_id[]" id="categories_'.$categories['categories_id'].'" value="'.$categories['categories_id'].'"><a href="javascript:void(0)" onclick="switch_categories('.$categories['categories_id'].')">'.$categories['categories_name'].'</a>'."\n";
      echo '<div id="d_'.$categories['categories_id'].'" class="categories_box">';
      $subcategories_query = tep_db_query("select * from categories c,categories_description cd where c.categories_id=cd.categories_id and c.parent_id='".$categories['categories_id']."' and cd.site_id='0' order by c.sort_order, cd.categories_name");
      if (tep_db_num_rows($subcategories_query)) {
        echo '<ul id="c_'.$categories['categories_id'].'">'."\n";
        $categories_checked_flag = true;
        while($subcategories = tep_db_fetch_array($subcategories_query)) { echo '<li>'."\n";
          echo '<input onclick="check_all('.$subcategories['categories_id'].')" type="checkbox" name="categories_id[]" id="categories_'.$subcategories['categories_id'].'" value="'.$subcategories['categories_id'].'"><a href="javascript:void(0)" onclick="switch_categories('.$subcategories['categories_id'].')">'.$subcategories['categories_name'].'</a>'."\n";
          echo '<div id="d_'.$subcategories['categories_id'].'" class="categories_box">';
          $subsubcategories_query = tep_db_query("select * from categories c,categories_description cd where c.categories_id=cd.categories_id and c.parent_id='".$subcategories['categories_id']."' and cd.site_id='0' order by c.sort_order, cd.categories_name");
          if (tep_db_num_rows($subsubcategories_query)) {
            echo '<ul id="c_'.$subcategories['categories_id'].'">';
            while($subsubcategories = tep_db_fetch_array($subsubcategories_query)) {
              echo '<li>'."\n";
              echo '<input onclick="check_all('.$subsubcategories['categories_id'].')" type="checkbox" name="categories_id[]" id="categories_'.$subsubcategories['categories_id'].'" value="'.$subsubcategories['categories_id'].'"><a href="javascript:void(0)" onclick="switch_categories('.$subsubcategories['categories_id'].')">'.$subsubcategories['categories_name'].'</a>'."\n";
              echo '<div id="d_'.$subsubcategories['categories_id'].'" class="categories_box">';
              $checked_flag = true;
              products_box($subsubcategories['categories_id'],$products_tags_array);
              if($checked_flag == true && !empty($products_tags_array)){

                echo '<script language="javascript">';
                echo '$("#categories_'.$subsubcategories['categories_id'].'").attr("checked","checked");';
                echo '</script>';
              }
              echo '</div>'."\n";
              echo '</li>'."\n";
            }
            echo '</ul>'."\n";
          }
          $checked_flag = true;
          products_box($subcategories['categories_id'],$products_tags_array);
          if($checked_flag == true && !empty($products_tags_array)){

            echo '<script language="javascript">';
            echo '$("#categories_'.$subcategories['categories_id'].'").attr("checked","checked");';
            echo '</script>';
          }else{

            $categories_checked_flag = false;
          }
          echo '</div>'."\n";
          echo '</li>'."\n";
        }
        if($categories_checked_flag == true && !empty($products_tags_array)){

          echo '<script language="javascript">';
          echo '$("#categories_'.$categories['categories_id'].'").attr("checked","checked");';
          echo '</script>';
        }
        echo '</ul>'."\n";
      }
      products_box($categories['categories_id'],$products_tags_array);
      echo '</div>'."\n";
      echo '</li>'."\n";  
      if (!in_array(intval($i/$products_num),$temp_array) && intval($i/$products_num) != 0 && $i-$j >= $products_num) {
        $temp_array[] = intval($i/$products_num);
        echo '</ul></td><td width="25%" valign="top"><ul style="padding-left:0;">';  
        $j = $i;
      }
    }
    echo "</ul>";
    products_box(0,$products_tags_array);
    echo "</td>"."\n"; 
  } else {
    echo '<td width="100%">'.TEXT_P_TAGS_NO_TAG.'<td>';
  }
?>
            </tr></table>
            </td>
          </tr>
          <tr>
           <td colspan="2" align="right">
           <input type="button" value="<?php echo IMAGE_SAVE;?>" onclick="products_tags_submit();"> 
           </td>
          </tr> 
         </table>
         </form>
        </td>
      </tr>
  </table> 
            <?php
            //商品分类及商品管理页面
            }else{
            ?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <?php
            if (isset($_GET['action']) && $_GET['action'] == 'new_product') {
              //新建/更新商品页 
              if ( isset($_GET['pID']) && ($_GET['pID']) && (!$_POST) ) {
                $site_id = isset($_GET['site_id']) ?$_GET['site_id']:0;
                $product_query = tep_db_query("
                    select pd.products_name, 
                    pd.products_description, 
                    pd.products_url, 
                    pd.romaji, 
                    p.products_id,
                    p.option_type, 
                    p.products_real_quantity + p.products_virtual_quantity as products_quantity,
                    p.products_real_quantity, 
                    p.products_virtual_quantity, 
                    p.products_model, 
                    p.products_image,
                    p.products_image2,
                    p.products_image3, 
                    p.products_price, 
                    p.products_price_offset,
                    p.products_weight, 
                    p.products_user_added,
                    p.products_date_added, 
                    pd.products_last_modified, 
                    pd.products_user_update,
                    date_format(p.products_date_available, '%Y-%m-%d') as products_date_available, 
                    p.products_shipping_time,
                    p.products_weight,
                    pd.products_status, 
                    p.products_tax_class_id, 
                    p.manufacturers_id, 
                    p.products_bflag, 
                    p.products_cflag, 
                    p.relate_products_id,
                    p.sort_order,
                    p.max_inventory,
                    p.min_inventory,
                    p.products_small_sum,
                    p.products_cartflag ,
                    p.products_cart_buyflag,
                    p.products_cart_image,
                    p.products_cart_min,
                    p.products_cartorder,
                    p.belong_to_option,
                    pd.preorder_status
                      from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
                      where p.products_id = '" . $_GET['pID'] . "' 
                      and p.products_id = pd.products_id 
                      and pd.language_id = '" . $languages_id . "' 
                      and pd.site_id = '".(tep_products_description_exist($_GET['pID'], $site_id, $languages_id)?$site_id:0)."'");
                $product = tep_db_fetch_array($product_query);
                if (isset($_SESSION['product_history'])) {
                  $product = array_merge($product, $_SESSION['product_history']);     
                  $products_description = $_SESSION['product_history']['products_description'];
                  $products_name = $_SESSION['product_history']['products_name'];
                  $products_url = $_SESSION['product_history']['products_url'];
                  unset($_SESSION['product_history']);
                }
                $pInfo = new objectInfo($product);
              } elseif ($_POST) {
                $pInfo = new objectInfo($_POST);
                $products_name = $_POST['products_name'];
                $products_description = $_POST['products_description'];

                $products_url = $_POST['products_url'];
                $site_id = isset($_POST['site_id']) ?$_POST['site_id']:0;
              } elseif ($_SESSION['product_history']){
                $pInfo = new objectInfo($_SESSION['product_history']);
                $products_name = $_SESSION['product_history']['products_name'];
                $products_description = $_SESSION['product_history']['products_description'];

                $products_url = $_SESSION['product_history']['products_url'];
                $site_id = isset($_SESSION['product_history']['site_id'])?$_SESSION['product_history']['site_id']:0;
                unset($_SESSION['product_history']);
              }else{
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

              //拆分商品说明
              if(isset($pInfo->products_id)){
                $des_query = tep_db_query("
                    select 
                    p.products_attention_1_1,
                    p.products_attention_1_2,
                    p.products_attention_1_3,
                    p.products_attention_1_4,
                    p.products_attention_1,
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
                <tr><?php echo tep_draw_form('new_product', FILENAME_CATEGORIES, 'cPath=' .
                    $cPath . '&page='.$_GET['page'].'&pID=' .
                    (isset($_GET['pID'])?$_GET['pID']:'') . '&action=new_product_preview'.($_GET['search']?'&search='.$_GET['search']:''),
                    'post', 'enctype="multipart/form-data" onSubmit="return products_form_validator(\''.
                $current_category_id.'\',\''.$pInfo->products_id.'\',\''.$site_id.'\');"'); ?>
                <input type="hidden" name="site_id" value="<?php echo $site_id;?>">
                <input type="hidden" name="products_user_added" value="<?php echo $user_info['name']?>"> 
                <input type="hidden" name="products_user_update" value="<?php echo $user_info['name']?>">
                <td><table border="0" cellspacing="0" cellpadding="2" width="100%">
                <tr>
                <td class="main" valign="top"><?php echo $site_id?('<br><b>'.tep_get_site_name_by_id($site_id).'</b>'):'';?></td>
                <td class="main" align="right"><?php 
                echo tep_html_element_submit(IMAGE_PREVIEW) .  '&nbsp;&nbsp;';
              if (isset($_GET['rdirect'])) {
                echo '<a class = "new_product_reset" href="' .  tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&page='.$_GET['page'].'&site_id=0&pID=' .  (isset($_GET['pID'])?$_GET['pID']:'')) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>'; 
              } else {
                echo '<a class = "new_product_reset" href="' .  tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&page='.$_GET['page'].'&site_id='.$site_id.'&pID=' .  (isset($_GET['pID'])?$_GET['pID']:'')) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>'; 
              }
              ?> 
                </td>
                </tr>
                <tr>
                <td colspan="2"><fieldset>
                <legend style="color:#FF0000 "><?php echo CATEGORY_PRODUCT_INFO_TITLE;?></legend>
                <table>
                <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_STATUS; ?></td>
                <td class="main"><?php echo
                tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                tep_draw_radio_field('products_status', '1',
                    $pInfo->products_status == '1' or
                    !isset($pInfo->products_status)) . '&nbsp;' .
                TEXT_PRODUCT_AVAILABLE . '&nbsp;' .
                tep_draw_radio_field('products_status', '2',
                    $pInfo->products_status == '2') . '&nbsp;' .  TEXT_PRODUCT_BEFORE_TEXT.
                '&nbsp;' . tep_draw_radio_field('products_status', '0',
                    $pInfo->products_status == '0') . '&nbsp;' .
                TEXT_PRODUCT_NOT_AVAILABLE . '&nbsp;' .
                tep_draw_radio_field('products_status', '3',
                    $pInfo->products_status == '3') . '&nbsp;' .
                TEXT_PRODUCT_NOT_SHOW; ?></td>
                <td class="main">&nbsp;</td>
                </tr>

                <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_BUY_AND_SELL; ?></td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_radio_field('products_bflag', '0', $in_bflag, '', ($site_id?'disabled':'')) . '&nbsp;' .  TEXT_PRODUCT_USUALLY . '&nbsp;' .  tep_draw_radio_field('products_bflag', '1', $out_bflag, '', ($site_id?'disabled':'')) . '&nbsp;' . TEXT_PRODUCT_PURCHASE; ?>
                <?php
                if ($site_id) {
                  echo tep_draw_hidden_field('products_bflag', $pInfo->products_bflag); 
                }
              ?>
                </td>
                <td class="main">&nbsp;</td>
                </tr>
                <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_PREORDER_TEXT;?></td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15'). '&nbsp;'.tep_draw_radio_field('preorder_status', '1', $pInfo->preorder_status == '1', '', ($site_id?'disabled':'')).'&nbsp;On'.tep_draw_radio_field('preorder_status', '0', (isset($pInfo->preorder_status)?($pInfo->preorder_status == '0'):true), '', ($site_id?'disabled':'')).'&nbsp;Off'?>
                <?php
                if ($site_id) {
                  echo tep_draw_hidden_field('preorder_status', $pInfo->preorder_status); 
                }
              ?>
                </td>
                </tr>
                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?><br>
                <small>(YYYY-MM-DD)</small></td>
                <td class="main">
                <div class="yui3-skin-sam yui3-g">
                <?php echo tep_draw_separator('pixel_trans.gif', '24', '15').
                tep_draw_input_field('products_date_available','',(($site_id)?'class="readonly" disabled value="'.$pInfo->products_date_available.'"':'class="cal-TextBox" value="'.$pInfo->products_date_available.'"'));?>
                <input id="date_orders" type="hidden" name='date_orders' size='15' value='<?php echo $pInfo->products_date_available;?>'>
                <div class="date_box">
                <a href="javascript:void(0);" onclick="open_new_calendar();" class="dpicker"></a> 
                </div>
                <input type="hidden" id="date_order" name="update_tori_torihiki_date" value="<?php echo $pInfo->products_date_available; ?>">
                <input type="hidden" name="toggle_open" value="0" id="toggle_open"> 
                <div class="yui3-u" id="new_yui3">
                <div id="mycalendar"></div> 
                </div>
                </div>
                </td>
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
                <td class="main"><?php echo TEXT_PRODUCT_SORT_ORDER_TEXT; ?></td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .  tep_draw_input_field('sort_order', isset($pInfo->sort_order)?$pInfo->sort_order:'1000','id="op"' .  ($site_id ? 'class="readonly" readonly' : 'onkeyup="clearLibNum(this);"')); ?></td>
                <td class="main">&nbsp;</td>
                </tr>
                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <?php
                $products_shipping_time = '<select name="products_shipping_time"'. ($site_id ?  'class="readonly"  onfocus="this.lastIndex=this.selectedIndex" onchange="this.selectedIndex=this.lastIndex"' : '').'>';
              $products_shipping_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where status='0' order by sort");
              while($products_shipping_array = tep_db_fetch_array($products_shipping_query)){

                if($products_shipping_array['id'] == $pInfo->products_shipping_time){

                  $selected = 'selected';
                }
                $products_shipping_time .= '<option value="'. $products_shipping_array['id'] .'" '. $selected .'>'. $products_shipping_array['name'] .'</option>';  
                $selected = '';
              }
              tep_db_free_result($products_shipping_query);
              $products_shipping_time .= '</select>';
              ?>
                <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_SHIPPING_TIME; ?></td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $products_shipping_time; ?></td>
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
                    <td class="main"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES .  $languages[$i]['directory'] . '/images/' .  $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;<span class="categories_input01">' .  tep_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (isset($products_name[$languages[$i]['id']]) ?  stripslashes($products_name[$languages[$i]['id']]) : (isset($pInfo->products_id)?tep_get_products_name($pInfo->products_id, $languages[$i]['id'], $site_id, true):'')), 'id="pname"').'</span>'; ?></td>
                    <td class="fieldRequired"><?php echo TEXT_PRODUCT_SEARCH_READ; ?></td>
                    </tr>
                    <?php
                }
              ?>

                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_SHIPPING_WEIGHT; ?></td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;<input type="text" id="products_weight" name="products_shipping_weight" value="'. ($pInfo->products_weight == '' ? 0 : $pInfo->products_weight) .'">'; ?></td>
                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_ROMAJI;?></td> 
                <td class="main">
                <?php
                echo  '<span
                class="categories_input01">'.tep_draw_separator('pixel_trans.gif',
                    '24', '15') . '&nbsp;'.tep_draw_input_field('romaji',
                      $pInfo->romaji, 'id="promaji"').'</span><br>'; 
              echo '<input type="button" onclick = "p_is_set_romaji(\''.$current_category_id.'\',\''.$pInfo->products_id.'\',\''.$site_id.'\')"
                value="'.TEXT_ROMAJI_IS_SET.'">'.
                '<input type="button" onclick = "p_is_set_error_char()"
                value="'.IS_SET_ERROR_CHAR.'">';
              ?>
                </td> 
                </tr>
                <tr>
                <td class="main"><?php echo TEXT_PRODUCT_LINK_PRODUCT_TEXT; ?></td>
                <td class="main" colspan="2">
                <?php echo tep_draw_separator('pixel_trans.gif', '24', '15');?>
                <?php echo tep_draw_pull_down_menu('relate_categories', tep_get_category_tree('&npsp;'), ($pInfo->relate_products_id?tep_get_products_parent_id($pInfo->relate_products_id):$current_category_id), ($site_id ? 'class="readonly"  onfocus="this.lastIndex=this.selectedIndex" onchange="this.selectedIndex=this.lastIndex"' : '').' onchange="relate_products1(this.options[this.selectedIndex].value, \''.$pInfo->relate_products_id.'\')"');?>
                <span id="relate_products">
                <?php echo tep_draw_pull_down_menu('relate_products', array_merge(array(array('id' => '0','text' => TEXT_NO_ASSOCIATION)),tep_get_products_tree($pInfo->relate_products_id?tep_get_products_parent_id($pInfo->relate_products_id):$current_category_id)),$pInfo->relate_products_id,($site_id ? 'class="readonly"  onfocus="this.lastIndex=this.selectedIndex" onchange="this.selectedIndex=this.lastIndex"' : '').'onchange="$(\'#relate_products_id\').val(this.options[this.selectedIndex].value)"');?>
                </span>
                <input type="hidden" name="relate_products_id" id="relate_products_id" value="<?php echo $pInfo->relate_products_id;?>">
                <input type="hidden" name="products_price_def" value="">
                </td>
                </tr>
                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr bgcolor="#CCCCCC">
                <td class="main"><?php echo '<font color="blue"><b>' . TEXT_PRODUCTS_PRICE . '</b></font>'; ?></td>
                <?php //add abs fro products ?>
                <td class="main"><?php echo
                tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .
                tep_draw_input_field('products_price',
                    isset($pInfo->products_price)?(abs($pInfo->products_price)?abs($pInfo->products_price):'0'):'','
                    onkeyup="clearNoNum(this)" id="pp"' . ($site_id ? 'class="readonly" readonly' : '')); ?></td>

                </tr>
                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr bgcolor="#CCCCCC">
                <td class="main"><?php echo '<font color="blue"><b>'.TEXT_PRODUCT_ADDORSUB_VALUE.'</b></font>'; ?></td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_price_offset', $pInfo->products_price_offset, ($site_id ? 'class="readonly" readonly' : 'id="products_add_del"')); ?></td>
                </tr>
                <tr>
                <td class="main">&nbsp;</td>
                <td colspan="2" class="smallText"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . TEXT_PRODUCT_PRICE_READ_ITEM_ONE; ?><br>
                <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') .  TEXT_PRODUCT_PRICE_READ_ITEM_TWO; ?><br>
                <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') .  TEXT_PRODUCT_PRICE_READ_ITEM_THREE; ?></td>
                </tr>
                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr>
                <td class="main" valign="top"><?php echo TEXT_PRODUCTS_SMALL_SUM; ?></td>
                <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;<div class="textarea_box">' .  tep_draw_textarea_field('products_small_sum', 'soft', '70', '5', isset($pInfo->products_small_sum)?$pInfo->products_small_sum:'', ($site_id ? 'class="readonly" readonly' : '')).'</div>'; ?></td>
                </tr>
                <tr>
                <td class="main">&nbsp;</td>
                <td colspan="2" class="smallText">
                <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') .  TEXT_PRODUCT_SMALLNUM_READ_ITEM_ONE; ?><br>
                <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') .  TEXT_PRODUCT_SMALLNUM_READ_ITEM_TWO; ?><br>
                <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') .  TEXT_PRODUCT_SMALLNUM_READ_ITEM_THREE; ?><br>
                <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') .  TEXT_PRODUCT_SMALLNUM_READ_ITEM_FOUR; ?><br>
                <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') .  TEXT_PRODUCT_SMALLNUM_READ_ITEM_FIVE; ?><br>
                <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') .  TEXT_PRODUCT_SMALLNUM_READ_ITEM_SIX; ?></td>
                </tr>
                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr bgcolor="#CCCCCC">
                <td class="main"><?php echo '<font color="blue"><b>' . TEXT_PRODUCTS_REAL_QUANTITY . '</b></font>'; ?></td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_real_quantity', isset($pInfo->products_real_quantity)?$pInfo->products_real_quantity:'', ($site_id ? 'class="readonly" readonly' : 'id="products_real_quantity" onkeyup="clearLibNum(this);"')); ?></td>
                </tr>
                <tr>
                <td>&nbsp;</td>
                <td class="smallText" colspan="2"><?php echo TEXT_PRODUCT_KUSHUOMING_TEXT;?></td>
                </tr>
                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr>
                <td class="main"><?php echo TEXT_PRODUCTS_MODEL; ?></td>
                <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('products_model', isset($pInfo->products_model)?$pInfo->products_model:'', ($site_id ? 'class="readonly" readonly' : '')); ?></td>
                <td class="fieldRequired"><?php echo TEXT_PRODUCT_SEARCH_READ;?></td>
                </tr>
                <tr>
                <td class="main" colspan="3">
                <?php 
                echo '<span class="categories_input01">' 
                . TEXT_PRODUCT_XIANGMU_NAME.'&nbsp;' .
                tep_draw_input_field('products_attention_1_1', isset($pInfo->products_attention_1_1)?$pInfo->products_attention_1_1:(isset($des_result['products_attention_1_1'])?$des_result['products_attention_1_1']:''), 'style="width:100px;" '.($site_id ? 'class="readonly" readonly' : ''))
                . '&nbsp;&nbsp;&nbsp;'.TEXT_PRODUCT_ATTONE_TEXT.'&nbsp;' .
                tep_draw_input_field('products_attention_1_2', isset($pInfo->products_attention_1_2)?$pInfo->products_attention_1_2:(isset($des_result['products_attention_1_2'])?$des_result['products_attention_1_2']:''), 'style="width:100px;" '.($site_id ? 'class="readonly" readonly' : ''))
                . '&nbsp;&nbsp;&nbsp;'.TEXT_PRODUCT_SHUZHI_TEXT.'&nbsp;' .
                tep_draw_input_field('products_attention_1_3', isset($pInfo->products_attention_1_3)?$pInfo->products_attention_1_3:(isset($des_result['products_attention_1_3'])?$des_result['products_attention_1_3']:''), 'style="width:100px;" '.($site_id ? 'class="readonly" readonly' : 'id="products_attention_1_3" onkeyup="clearLibNum(this);"'))
                . '&nbsp;&nbsp;&nbsp;'.TEXT_PRODUCT_ATTTWO_TEXT.'&nbsp;' .
                tep_draw_input_field('products_attention_1_4', isset($pInfo->products_attention_1_4)?$pInfo->products_attention_1_4:(isset($des_result['products_attention_1_4'])?$des_result['products_attention_1_4']:''), 'style="width:100px;" '.($site_id ? 'class="readonly" readonly' : ''))
                .'</span>'; ?>
                </td>
                </tr>
                <tr>
                <td class="main"><?php echo TEXT_PRODUCT_PROJECT_TEXT;?>１</td>
                <td class="main" colspan="2">
                <?php 
                echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;<span class="categories_input01">' . tep_draw_input_field('products_jan', isset($pInfo->products_jan)?$pInfo->products_jan:(isset($des_result['products_attention_1'])?$des_result['products_attention_1']:''), ($site_id ? 'class="readonly" readonly' : '')).'</span>'; ?><br>
                <span class="smallText"><?php echo TEXT_PRODUCT_PROJECT_READ;?></span></td>
                </tr>
                <tr>
                <td class="main"><?php echo TEXT_PRODUCT_PROJECT_TEXT;?>２</td>
                <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;<span class="categories_input01">' . tep_draw_input_field('products_size', isset($pInfo->products_size)?$pInfo->products_size:(isset($des_result['products_attention_2'])?$des_result['products_attention_2']:''), ($site_id ? 'class="readonly" readonly' : '')).'</span>'; ?></td>
                </tr>
                <tr>
                <td class="main"><?php echo TEXT_PRODUCT_PROJECT_TEXT;?>３</td>
                <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;<span class="categories_input01">' . tep_draw_input_field('products_naiyou', isset($pInfo->products_naiyou)?$pInfo->products_naiyou:(isset($des_result['products_attention_3'])?$des_result['products_attention_3']:''), ($site_id ? 'class="readonly" readonly' : '')).'</span>'; ?></td>
                </tr>
                <tr>
                <td class="main"><?php echo TEXT_PRODUCT_PROJECT_TEXT;?>４</td>
                <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;<span class="categories_input01">' . tep_draw_input_field('products_zaishitu', isset($pInfo->products_zaishitu)?$pInfo->products_zaishitu:(isset($des_result['products_attention_4'])?$des_result['products_attention_4']:''), ($site_id ? 'class="readonly" readonly' : '')).'</span>'; ?></td>
                </tr>
                <tr>
                <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <tr>
                <td class="main" valign="top"><?php echo TEXT_PRODUCT_ATTFIVE_TITLE;?></td>
                <td class="main" colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;<div class="textarea_box">' .  tep_draw_textarea_field('products_attention_5', 'soft', '70', '15', isset($pInfo->products_attention_5)?$pInfo->products_attention_5:(isset($des_result['products_attention_5'])?$des_result['products_attention_5']:''), ($site_id ? 'class="readonly" readonly' : '')).'</div>'; ?></td>
                </tr>
                </table>
                </fieldset></td>
                </tr>
                <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>

                <tr>
                <td colspan="2"><fieldset>
                <legend style="color:#FF0000"><?php echo TEXT_PRODUCT_SHUOMING_TEXT;?></legend>
                <table>

                <?php
                for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                  ?>
                    <tr>
                    <td class="main" valign="top"  nowrap="nowrap"><?php if ($i == 0) echo TEXT_PRODUCTS_DESCRIPTION; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td class="main"><table border="0" cellspacing="0" cellpadding="0">
                    <tr>
                    <td class="main" valign="top"><?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;</td>
                    <td class="main"><?php echo '<div class="textarea_box">'.tep_draw_textarea_field('products_description[' .  $languages[$i]['id'] . ']', 'soft', '78', '15', (isset($products_description[$languages[$i]['id']]) ?  stripslashes($products_description[$languages[$i]['id']]) : (isset($pInfo->products_id)?tep_get_products_description($pInfo->products_id, $languages[$i]['id'], $site_id, true):''))).'</div>'; ?></td>
                    </tr>
                    </table>

#STORE_NAME# <br>
                    <?php echo TEXT_PRODUCT_DESC_HTML_READ;?><br>
                    <span class="fieldRequired"><?php echo TEXT_PRODUCT_SEARCH_READ;?></span></td>
                    </tr>
                    <?php
                }
              ?>
                <!-- options -->
                <tr>
                <td class="main" nowrap="nowrap">
                <?php echo TEXT_PRODUCTS_OPTION_TEXT;?> 
                </td>
                <td class="main">
                <?php
                if (!$_GET['site_id']) { 
                  $option_keyword_str = ''; 
                  if (isset($pInfo->belong_to_option)) {
                    $option_group_raw = tep_db_query("select name from ".TABLE_OPTION_GROUP." where id = '".$pInfo->belong_to_option."'"); 
                    $option_group = tep_db_fetch_array($option_group_raw);
                    if ($option_group) {
                      $option_keyword_str = $option_group['name']; 
                    }
                  }
                  ?>
                    <input type="text" name="option_keyword" id="option_keyword" value="<?php echo $option_keyword_str;?>">
                    <a href="javascript:void(0);"><?php echo tep_html_element_button(OPTION_EDIT, 'onclick="handle_option();"');?></a> 
                    <a href="javascript:void(0);"><?php echo tep_html_element_button(OPTION_CLEAR, 'onclick="clear_option();"');?></a> 
                    <?php 
                } else { 
                  $option_group_raw = tep_db_query("select name from ".TABLE_OPTION_GROUP." where id = '".$pInfo->belong_to_option."'"); 
                  $option_group = tep_db_fetch_array($option_group_raw);
                  echo '<input type="text" name="hide_option_keyword" value="'.$option_group['name'].'" class="readonly" readonly>'; 
                } 
              ?>
                </td>
                </tr>
                <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                </tr>
                <?php 
                if (false) { 
                  ?> 
                    <tr>
                    <td class="main" valign="top"><?php echo TEXT_PRODUCT_OPTIONS_TITLE;?></td>
                    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif',
                        '24', '15') . '&nbsp;<span class="categories_textarea01">' .  tep_draw_textarea_field('products_options', 'soft', '70', '15', (isset($pInfo->products_options)?$pInfo->products_options:(($options_array)?$options_array:'')), ($site_id ? 'class="readonly" readonly' : '')).'</span>'; ?></td>
                    </tr>
                    <tr>
                    <td class="main" valign="top"><?php echo TEXT_PRODUCT_OIMAGE_TITLE;?></td>
                    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15');?> 
                    <?php
                    if (!isset($pInfo->option_image_type)) {
                      ?>
                        <input type="radio" name="option_image_type" value="select" checked><?php echo TEXT_PRODUCT_OPTIONS_SELECT_TEXT;?>
                        <input type="radio" name="option_image_type" value="radio"><?php echo TEXT_PRODUCT_OPTIONS_RADIO_TEXT;?>
                        <?php
                    } else {
                      ?>
                        <input type="radio" name="option_image_type" value="select" <?php if($pInfo->option_image_type == 'select'){?> checked<?php }?>><?php echo TEXT_PRODUCT_OPTIONS_SELECT_TEXT;?>
                        <input type="radio" name="option_image_type" value="radio" <?php if($pInfo->option_image_type == 'radio'){?> checked<?php }?>><?php echo TEXT_PRODUCT_OPTIONS_RADIO_TEXT;?>
                        <?php
                    }
                  ?>
                    </td>
                    </tr>
                    <tr>
                    <td></td>
                    <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;'; ?><?php echo TEXT_PRODUCT_LAN_READ;?><br>
                    <?php echo tep_draw_separator('pixel_trans.gif', '24', '15') .  '&nbsp;'; ?><?php echo TEXT_PRODUCT_LAN_LI_TEXT;?><br>
                    <table border="0" cellspacing="0" cellpadding="3">
                    <tr>
                    <td class="main">                  
                    <?php echo TEXT_PRODUCT_LAN_COMMENT;?>
                    </td>
                    <td width="50" align="center" class="main">&rarr;</td>
                    <td class="main"><?php echo TEXT_PRODUCT_LAN_TEXT;?>
                    <select name="select">
                    <option selected><?php echo TEXT_LANGUAGE_JAPAN;?></option>
                    <option><?php echo TEXT_LANGUAGE_CHINA;?></option>
                    <option><?php echo TEXT_LANGUAGE_KOREA;?></option>
                    </select></td>
                    </tr>
                    </table>
                    </td>
                    </tr>
                    <?php }?> 
                    <!-- options -->
                    <tr>
                    </table>
                    </fieldset></td>
                    </tr>
                    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                    </tr>
                    <?php if (!$site_id) {?>
                      <tr>
                        <td colspan="2"><fieldset>
                        <legend style="color:#009900 "><?php echo TEXT_PRODUCT_IMAGE_TITLE;?></legend>
                        <table>
                        <tr>
                        <td class="main"><?php echo TEXT_PRODUCTS_IMAGE; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                        <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('products_image') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . (isset($pInfo->products_image)?$pInfo->products_image:'') . tep_draw_hidden_field('products_previous_image', isset($pInfo->products_image)?$pInfo->products_image:''); ?>
                        <?php
                        if(isset($pInfo->products_image) && tep_not_null($pInfo->products_image)){
                          echo '<br>'.tep_info_image('products/'.$pInfo->products_image,$pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, $site_id).'<br>'."\n";
                          ?>
                            <a href="javascript:confirmg('<?php echo TEXT_PRODUCT_IMAGE_DEL_CONFIRM;?>','<?php echo tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&cl=products_image&action='.$_GET['action'].'&file='.(isset($pInfo->products_image)?$pInfo->products_image:'').'&mode=p_delete&site_id='.$site_id) ; ?>');" style="color:#0000FF;"><?php echo TEXT_PRODUCT_IMAGE_DEL_TEXT;?></a>
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
                                <a href="javascript:confirmg('<?php echo TEXT_PRODUCT_IMAGE_DEL_CONFIRM;?>','<?php echo tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&cl=products_image2&action='.$_GET['action'].'&file='.$pInfo->products_image2.'&mode=p_delete') ; ?>');" style="color:#0000FF;"><?php echo TEXT_PRODUCT_IMAGE_DEL_TEXT;?></a>
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
                                    <a href="javascript:confirmg('<?php echo TEXT_PRODUCT_IMAGE_DEL_CONFIRM;?>','<?php echo tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&cl=products_image3&action='.$_GET['action'].'&file='.$pInfo->products_image3.'&mode=p_delete') ; ?>');" style="color:#0000FF;"><?php echo TEXT_PRODUCT_IMAGE_DEL_TEXT;?></a>
                                    <?php } ?>
                                    </td>
                                    </tr>
                                    </table>
                                    <?php
                                    if(COLOR_SEARCH_BOX_TF == "true" ){
                                      ?>
                                        <?php // 按照颜色划分图片 ?>
                                        <hr size="1">
                                        <legend style="color:#009900 "><?php echo TEXT_PRODUCT_COLOR_IMAGE_TITLE;?></legend>
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
                                        echo '<td class="main" width="33%">'.TEXT_TEXT.'&nbsp;'.tep_draw_input_field('colorname_'.$color['color_id'], $ctp['color_to_products_name']).'<br>'.$color['color_name'].': '.tep_draw_file_field('image_'.$color['color_id']).'<br>&nbsp;&nbsp;&nbsp;' . $ctp['color_image'].tep_draw_hidden_field('image_pre_'.$color['color_id'], $ctp['color_image']).'</td>';
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
                                        <?php // 按照颜色划分图片 ?>
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
                                    <fieldset><legend style="color:#009900 "><?php echo TEXT_PRODUCTS_TAGS;?></legend>                                    <table border="0" cellspacing="0" cellpadding="2" width="100%">
                                    <tr>
                                    <?php if($site_id){/*echo ' class="readonly"';*/}?> 
                                    <?php
                                    //show tags 
                                    $checked_tags = array();

                                  if (isset($pInfo->tags)) {
                                    foreach ($pInfo->tags as $t_key => $t_value) {
                                      $checked_tags[$t_value] = $t_value; 
                                    }
                                  } else if (isset($_GET['pID']) && $_GET['pID']) {
                                    $c_query = tep_db_query("select * from ".TABLE_PRODUCTS_TO_TAGS." where products_id = '".$_GET['pID']."'"); 
                                    while ($ptt = tep_db_fetch_array($c_query)) {
                                      $checked_tags[$ptt['tags_id']] = $ptt['tags_id']; 
                                    }
                                  }
                                  //获取分类下关联的标签
                                  $products_tags_id_list_array = array();
                                  $products_to_tags_query = tep_db_query("select tags_id from ". TABLE_PRODUCTS_TO_TAGS ." where products_id='".$_GET['pID']."'");
                                  while($products_to_tags_array = tep_db_fetch_array($products_to_tags_query)){

                                       $products_tags_id_list_array[] = $products_to_tags_array['tags_id'];
                                  }
                                  tep_db_free_result($products_to_tags_query);

                                  if(isset($_GET['cPath']) && trim($_GET['cPath']) != '' && $_GET['cPath'] != 0 && empty($products_tags_id_list_array)){
                                    $categories_cpath_array = explode('_',$_GET['cPath']);
                                    if(count($categories_cpath_array) > 1){ 
                                      $categories_tags_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".end($categories_cpath_array)."'");
                                      while($categories_tags_array = tep_db_fetch_array($categories_tags_query)){

                                        $products_to_tags_query = tep_db_query("select tags_id from ". TABLE_PRODUCTS_TO_TAGS ." where products_id='".$categories_tags_array['products_id']."'");
                                        while($products_to_tags_array = tep_db_fetch_array($products_to_tags_query)){

                                          $products_tags_id_list_array[] = $products_to_tags_array['tags_id'];
                                        }
                                        tep_db_free_result($products_to_tags_query);
                                      }
                                      tep_db_free_result($categories_tags_query);
                                    }else{
                                      $categories_parent_query = tep_db_query("select categories_id from ". TABLE_CATEGORIES ." where parent_id='".end($categories_cpath_array)."'");
                                      while($categories_parent_array = tep_db_fetch_array($categories_parent_query)){
                                      $categories_tags_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$categories_parent_array['categories_id']."'");
                                      while($categories_tags_array = tep_db_fetch_array($categories_tags_query)){

                                        $products_to_tags_query = tep_db_query("select tags_id from ". TABLE_PRODUCTS_TO_TAGS ." where products_id='".$categories_tags_array['products_id']."'");
                                        while($products_to_tags_array = tep_db_fetch_array($products_to_tags_query)){

                                          $products_tags_id_list_array[] = $products_to_tags_array['tags_id'];
                                        }
                                        tep_db_free_result($products_to_tags_query);
                                      }
                                      
                                      $parent_categories_id_query = tep_db_query("select categories_id from ". TABLE_CATEGORIES ." where parent_id='".$categories_parent_array['categories_id']."'"); 
                                      if(tep_db_num_rows($parent_categories_id_query)){

                                        while($parent_categories_id_array = tep_db_fetch_array($parent_categories_id_query)){
                                          $categories_tags_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$parent_categories_id_array['categories_id']."'");
                                          while($categories_tags_array = tep_db_fetch_array($categories_tags_query)){

                                            $products_to_tags_query = tep_db_query("select tags_id from ". TABLE_PRODUCTS_TO_TAGS ." where products_id='".$categories_tags_array['products_id']."'");
                                            while($products_to_tags_array = tep_db_fetch_array($products_to_tags_query)){

                                              $products_tags_id_list_array[] = $products_to_tags_array['tags_id'];
                                            }
                                            tep_db_free_result($products_to_tags_query);
                                          }
                                        }
                                        tep_db_free_result($parent_categories_id_query);
                                      }
                                      tep_db_free_result($categories_tags_query); 
                                      }
                                      $categories_products_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".end($categories_cpath_array)."'");
                                      while($categories_products_array = tep_db_fetch_array($categories_products_query)){
                                        $products_to_tags_query = tep_db_query("select tags_id from ". TABLE_PRODUCTS_TO_TAGS ." where products_id='".$categories_products_array['products_id']."'");
                                        while($products_to_tags_array = tep_db_fetch_array($products_to_tags_query)){

                                          $products_tags_id_list_array[] = $products_to_tags_array['tags_id'];
                                        }
                                        tep_db_free_result($products_to_tags_query); 
                                      }
                                      tep_db_free_result($categories_products_query);
                                    tep_db_free_result($categories_parent_query);
                                    }
                                  }
                                  $query_str = ' where 1>1';
                                  $url_str = $_SERVER['QUERY_STRING'];
                                  $url_str = str_replace('&','|||',$url_str);
                                  $products_tags_id_list_array = array_unique($products_tags_id_list_array);
                                  if(!empty($products_tags_id_list_array)){
                                    $query_tags_id = implode(',',$products_tags_id_list_array);
                                    $query_str = ' where tags_id in ('.$query_tags_id.')'; 
                                  }
                                  $t_query = tep_db_query("select * from ".TABLE_TAGS.$query_str); 
                                  $tags_temp_query = tep_db_query("select * from ".TABLE_TAGS.$query_str);
                                  $tags_num = tep_db_num_rows($t_query);
                                  $tag_array = array();
                                  $tags_array = array();
                                  while($tags_temp_array = tep_db_fetch_array($tags_temp_query)){

                                    $tags_array[] = $tags_temp_array['tags_id'];
                                  }
                                  tep_db_free_result($tags_temp_query);
                                  $tags_i = 1;
                                  while ($tag = tep_db_fetch_array($t_query)) {
                                    $tag_array[] = $tag; 
                                    ?>
                                      <td width="20%" valign="top">
                                      <?php if($tags_num <=15 ){?>
                                      <input type='checkbox' <?php echo ($site_id)?'disabled':'';?> name='tags[]' value='<?php echo $tag['tags_id'];?>' 
                                      <?php
                                      if ($_GET['pID'] || isset($pInfo->tags)) {
                                        if (isset($checked_tags[$tag['tags_id']])) {
                                          echo 'checked'; 
                                        }
                                      } else if ($tag['tags_checked']) {
                                        echo 'checked'; 
                                      } else if (isset($_POST['tags']) && in_array($tag['tags_id'], $_POST['tags'])) {
                                        echo 'checked'; 
                                      }
                                    ?><?php if ($site_id) {echo ' onclick="return false;"';}?>
                                      ><?php }echo $tag['tags_name'].($tags_i == 15 ? '&nbsp;&nbsp;...' : '').'</td>';?> 
                                    <?php 
                                    //对标签显示，进行排版
                                    if($tags_i % 5 == 0){

                                     echo '</tr><tr>'; 
                                    }
                                    $tags_i++;
                                    if($tags_i > 15){break;}
                                  }
                                  $tags_list_str = implode(',',$tags_array);
                                  if($tags_num == 0){

                                    echo '<td>'.TEXT_UNSET_DATA.'</td>';
                                  }
                                  ?></tr>
                                    <?php
                                  if($tags_num > 15){

                                    echo '<tr><td colspan="5"><input type="button" value="'.OPTION_EDIT.'" onclick="edit_products_tags(this,\''.$tags_list_str.'\',1,\''.$url_str.'\',\''.$_GET['pID'].'\');"></td></tr>';
                                  }
                                    ?>
                                    </table></fieldset> 
                                    </td>
                                    </tr>
                                    <tr>
                                    <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                                    </tr>
                                    <tr>
                                    <td colspan="2">
                                    <fieldset><legend style="color:#009900 "><?php echo TEXT_PRODUCT_CARTFLAG_TITLE;?></legend>
                                    <table width="100%">
                                    <tr><td width="150"><?php echo TEXT_PRODUCT_CARTFLAG_TITLE;?></td><td width="100"><input type="radio" <?php echo ($site_id)?'disabled':'';?> name="products_cartflag" value="0"<?php if(!$pInfo->products_cartflag){?> checked<?php }?> onclick="cattags_show(0);"><?php echo
                                    TEXT_PRODUCT_CARTFLAG_NO;?> </td><td><input type="radio" <?php echo ($site_id)?'disabled':'';?> name="products_cartflag" value="1"<?php if($pInfo->products_cartflag){?> checked<?php }?> onclick="cattags_show(1);"><?php echo TEXT_PRODUCT_CARTFLAG_YES;?>
                                    </td></tr>
                                    <tr id="cattags_list"<?php echo !$pInfo->products_cartflag ? ' style="display:none;"' : '';?>>
                                    <td width="150" align="left">&nbsp;</td>
                                    <td nowrap="nowrap" width="100"><input type="radio" <?php echo ($site_id)?'disabled':'';?> name="products_cart_buyflag" value='0'<?php if(!$pInfo->products_cart_buyflag){?> checked<?php }?>><?php echo TEXT_PRODUCT_BUYFLAG_SELL;?> </td><td><input type="radio"  <?php echo ($site_id)?'disabled':'';?> name="products_cart_buyflag" value='1'<?php if($pInfo->products_cart_buyflag){?> checked<?php }?>><?php echo TEXT_PRODUCT_BUYFLAG_BUY;?></td> 
                                      </tr>
                                    <tr><td colspan="3"> 
                                          </td></tr>
                                          </table>
                                          <?php 
                                    $carttag_array = array();
                                  $carttag_query = tep_db_query("select * from products_to_carttag where products_id='".$_GET['pID']."'");
                                  while ($carttag = tep_db_fetch_array($carttag_query)) {
                                    $carttag_array[$carttag['tags_id']] = $carttag;
                                  }
                                  ?>
                                      <table id="cattags_title" border="0" cellspacing="0" cellpadding="2" width="100%"<?php echo !$pInfo->products_cartflag ? ' style="display:none;"' : '';?>><tr>
                                       <?php 
                                       $tags_i = 1;
                                       foreach($tag_array as $tag){ 
                                       ?>
                                       <td width="20%" valign="top">
                                      <?php if($tags_num <=15 ){?>
                                        <input type='checkbox' <?php echo ($site_id)?'disabled':'';?> class="carttags" name='carttags[<?php echo $tag['tags_id'];?>]' value='1'<?php if(isset($carttag_array[$tag['tags_id']])){echo " checked";} else if (isset($pInfo->carttags[$tag['tags_id']])) {echo "checked";}?>><?php }echo $tag['tags_name'].($tags_i == 15 ? '&nbsp;&nbsp;...' : '').'</td>';?>
                                          <?php 
                                           if($tags_i % 5 == 0){echo '</tr><tr>';}  
                                           $tags_i++;
                                           }
                                           if($tags_num == 0){

                                             echo '<td>'.TEXT_UNSET_DATA.'</td>';
                                           } 
                                          ?>
                                          </tr>
                                          <?php
                                          if($tags_num > 15){

                                            echo '<tr><td colspan="5"><input type="button" value="'.OPTION_EDIT.'" onclick="edit_products_tags(this,\''.$tags_list_str.'\',2,\''.$url_str.'\',\''.$_GET['pID'].'\');"></td></tr>';
                                          }
                                          ?>
                                          </table>  
                                          <table id="cattags_contents" width="100%"<?php echo !$pInfo->products_cartflag ? ' style="display:none;"' : '';?>>
                                          <td></tr>
                                          <tr><td width="150"><?php echo TEXT_PRODUCT_CART_MIN_TEXT;?></td> <td><input id="products_cart_min" <?php echo ($site_id)?'class="readonly" disabled':'';?> name="products_cart_min" type="text" value="<?php echo $pInfo->products_cart_min?$pInfo->products_cart_min:0;?>" onkeyup="clearLibNum(this);">
                                          </td></tr>
                                          <tr>
                                          <td><?php echo TEXT_PRODUCT_CARTORDER_TEXT;?></td>
                                          <td><input id="products_cartorder" <?php echo ($site_id)?'class="readonly" disabled':'';?> name="products_cartorder" type="text" value="<?php echo $pInfo->products_cartorder?$pInfo->products_cartorder:1000;?>" onkeyup="clearLibNum(this);">
                                          </td></tr>
                                          <?php if ($pInfo->products_cart_image) {?>
                                            <tr>
                                              <td><?php echo TEXT_PRODUCT_PIC_PREVIEW_TEXT;?></td>
                                              <td><?php echo tep_image(tep_get_web_upload_dir(0) . 'carttags/' . $pInfo->products_cart_image, $pInfo->products_name, null, null, 'align="right" hspace="5" vspace="5"');?>
                                              <br>
                                              <a href="javascript:confirmg('<?php echo TEXT_PRODUCT_IMAGE_DEL_CONFIRM;?>','<?php echo tep_href_link('categories.php?cPath='.$_GET['cPath'].'&pID='.$_GET['pID'].'&cl=products_cart_image&action='.$_GET['action'].'&file='.$pInfo->products_cart_image.'&mode=c_delete') ; ?>');" style="color:#0000FF;"><?php echo TEXT_PRODUCT_IMAGE_DEL_TEXT;?></a>
                                              </td></tr>
                                              <?php }?>
                                              <tr><td><?php echo TEXT_PRODUCT_CARTIMAGE_TITLE;?></td>
                                              <td><input type="file" <?php echo ($site_id)?'class="readonly" disabled':'';?> name="products_cart_image">
                                              <br><?php echo TEXT_PRODUCT_CARTIMAGE_NOTICE;?>
                                              </td></tr>
                                              <tr><td colspan="2" style="text-align:center;"> <a href="javascript:void(0);" onclick="get_cart_products()"><?php echo TEXT_PRODUCT_RESULT_CONFIRM;?></a>
                                              </td></tr>
                                              </table>
                                              </fieldset>
                                              </td>
                                              </tr>
                                              <?php
                                              for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
                                                ?>
                                                  <tr>
                                                  <td width="135" class="main"><?php if ($i == 0) echo TEXT_PRODUCTS_URL . '<br><small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>'; ?></td>
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
                                    <td class="main" align="right">
                                    <?php 
                                    if (isset($_GET['rdirect'])) {
                                      echo tep_draw_hidden_field('rdirect', 'all'); 
                                    }
                                  echo tep_eof_hidden(); 
                                  echo tep_html_element_submit(IMAGE_PREVIEW) .  '&nbsp;&nbsp;'; 
                                  if (isset($_GET['rdirect'])) {
                                    echo '<a class = "new_product_reset" href="' .  tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&page='.$_GET['page'].'&site_id=0&pID=' .  (isset($_GET['pID'])?$_GET['pID']:'')) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>'; 
                                  } else {
                                    echo '<a class = "new_product_reset" href="' .  tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&page='.$_GET['page'].'&site_id='.$site_id.'&pID=' .  (isset($_GET['pID'])?$_GET['pID']:'')) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>'; 
                                  }
                                  ?>
                                    </td>
                                    </tr>
                                    <?php
                                    if ($romaji_error == 1) {
                                      echo '<script type="text/javascript">alert("'.$romaji_error_str.'")</script>';        
                                    }
                                  ?>
                                    <?php echo tep_draw_hidden_field('products_date_added', (isset($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d')));?>
                                    </form>

                                    <?php
            } elseif (isset($_GET['action']) && $_GET['action'] == 'new_product_preview') {
            //商品预览页 
              ?>                 
                <script  type='text/javascript'>
                $(document).ready(function (){ $("#pp").select().focus() }); 
              </script>
                <?

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
                  // copy image only if modified 
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

                  $products_cart_image = tep_get_uploaded_file('products_cart_image');
                  if (is_uploaded_file($products_cart_image['tmp_name'])) {
                    tep_copy_uploaded_file($products_cart_image, tep_get_local_path(tep_get_upload_dir($site_id).'carttags/'));
                    $products_cart_image_name = $products_cart_image['name'];
                  } else {
                    $products_cart_image_name = $_POST['products_cart_image'];
                  }
                  //color image upload    
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
                      $color_image_hidden .= tep_draw_hidden_field('image_'.$color['color_id'], $color_image['name']);
                    } 
                  }

                } else {
                  $site_id = isset($_GET['site_id']) ? $_GET['site_id'] : '0';
                  $product_query = tep_db_query("
                      select p.products_id, 
                      pd.language_id, 
                      pd.products_name, 
                      pd.products_description, 
                      pd.products_url, 
                      pd.romaji, 
                      p.products_real_quantity + p.products_virtual_quantity as products_quantity,
                      p.products_real_quantity, 
                      p.products_virtual_quantity, 
                      p.products_model, 
                      p.products_image,
                      p.products_image2,
                      p.products_image3, 
                      p.products_price, 
                      p.products_bflag,
                      p.products_weight, 
                      p.products_date_added, 
                      pd.products_last_modified, 
                      p.products_date_available, 
                      p.products_attention_5,
                      p.relate_products_id,
                      pd.products_status, 
                      p.manufacturers_id, 
                      pd.preorder_status
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

              if (isset($_GET['read']) && $_GET['read'] == 'only' && (!isset($_GET['origin']) || !$_GET['origin'])) {
                $form_action = 'simple_update';
              } elseif ($_GET['pID']) {
                $form_action = 'update_product';
              } else {
                $form_action = 'insert_product';
              }
              if (isset($_GET['read']) && $_GET['read'] == 'only' && (!isset($_GET['origin']) || !$_GET['origin'])) {
                $dougyousya_array = array();
                $cpath_array = explode('_', $_GET['cPath']);
                $categories_id = $cpath_array[0];
                $current_categories_id = $cpath_array[count($cpath_array)-1];
                $calc = tep_db_fetch_array(tep_db_query("select * from set_auto_calc where parent_id='".$current_categories_id."'"));
                //show menu start
                echo '<tr>';
                echo '<td>';
                echo "<div class='gotomenu_out_div'>";
                ?>
                  <?php echo tep_draw_form('goto', FILENAME_CATEGORIES, '', 'get') . "\n"; ?>
                  <div id="gotomenu">
                  <a href="javascript:void(0)" onclick="display()"><?php echo CATEGORY_TREE_SELECT_TEXT;?></a>
                  <div id="categories_tree">
                  <?php
                  require_once(DIR_WS_CLASSES . 'category_tree.php');
                $osC_CategoryTree = new osC_CategoryTree; 
                echo $osC_CategoryTree->buildTree();
                ?>
                  </div>
                  </div>
                  <?php
                  echo '</form>' . "\n";
                echo '</div>';
                echo tep_draw_form($form_action, FILENAME_CATEGORIES, 'from='.$_GET['from'].'&cPath=' . $cPath . '&pID=' . $_GET['pID'] . '&page='.$_GET['page'].'&action=' . $form_action, 'post', 'enctype="multipart/form-data" onSubmit="return check_price(\'pp\', '.$pInfo->products_price.', '.($calc?$calc['percent']:0).');"');
                echo tep_html_element_submit(IMAGE_EDIT);
                echo '</td>';
                echo '</tr>';
                //show menu end
              } else {
                echo tep_draw_form($form_action, FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&pID=' . $_GET['pID'] . '&page='.$_GET['page'].'&action=' . $form_action.($_GET['search']?'&search='.$_GET['search']:''), 'post', 'enctype="multipart/form-data" onSubmit="return mess();"');
              }
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
                //有特价的情况下的处理
                if (tep_get_special_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum)) {
                  $products_price_preview = '<s>' .
                    $currencies->format(tep_get_price(((int)$pInfo->products_bflag ? (0 - $pInfo->products_price) : $pInfo->products_price), $pInfo->products_price_offset, $pInfo->products_small_sum, $pInfo->products_bflag)) . '</s> <span class="specialPrice">' . $currencies->format(tep_get_special_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum)) . '</span>';
                } else {
                  $products_price_preview = $currencies->format(tep_get_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum, $pInfo->products_bflag));
                }
                ?>
                  <tr>
                  <td class="pageHeading">
                  <table width="100%"><tr><td>
                  <?php echo tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . $pInfo->products_name . "\n"; ?>

                  &nbsp; 
                <?php
                  if (isset($_GET['read'])) {
                    echo display_product_link($cPath, $_GET['pID'], $languages_id, $site_id ,true); 
                  }
                ?>
                  </td></tr></table>
                  </td>
                  </tr>
                  <tr>
                  <td>
                  <?php
                  //(进货价x倍率 和 同业者取最大值) + 增减值 结果是否在范围内 提示

                  $dougyousya_query = tep_db_query("select * from set_dougyousya_categories sdc,set_dougyousya_names sdn where sdc.dougyousya_id=sdn.dougyousya_id and sdc.categories_id='".$categories_id."'");
                while($d = tep_db_fetch_array($dougyousya_query)){
                  $d['price'] = get_dougyousya_history($_GET['pID'], $d['dougyousya_id']);
                  $dougyousya_array[] = $d;
                }

                if ($dougyousya_array) {
                  $dougyousya       = tep_db_fetch_array(tep_db_query("select * from set_products_dougyousya spd, set_dougyousya_names sdn where spd.dougyousya_id=sdn.dougyousya_id and spd.product_id='".$_GET['pID']."'"));
                  $oroshi           = tep_db_fetch_array(tep_db_query("select * from set_menu_list where products_id='".$_GET['pID']."'"));
                }
                ?>
                  <?php //价格数量变更功能
                  if (isset($_GET['read']) && $_GET['read'] == 'only' && (!isset($_GET['origin']) || !$_GET['origin'])) {
                    echo '<table width="100%"><tr><td align="left">';
                    echo '<table width="95%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">';
                    echo '  <tr><td><hr size="2" noshade></td></tr><tr>';
                    echo '  <tr>';
                    echo '  <td height="30">';
                    echo '<table  width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="left">';
                    // add abs for products_price 
                    echo CATEGORY_JIAGE_TEXT.'：&nbsp;' .  tep_draw_input_field('products_price',
                        number_format(abs($pInfo->products_price)?abs($pInfo->products_price):'0',0,'.',''),'onkeyup="clearNoNum(this)" id="pp" size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;"') . '&nbsp;' . CATEGORY_MONEY_UNIT_TEXT .  '&nbsp;&nbsp;&larr;&nbsp;' . (int)$pInfo->products_price . CATEGORY_MONEY_UNIT_TEXT.' ' . "\n";
                    echo '</td><td align="right">';
                    if (!$pInfo->products_bflag && $pInfo->relate_products_id)
                      echo CATEGORY_AVERAGE_PRICE.' '.@display_price(tep_get_avg_by_pid($pInfo->products_id)).CATEGORY_MONEY_UNIT_TEXT;
                    echo '</td></tr></table>';
                    echo '  </td>';
                    echo '  </tr><tr><td><hr size="2" noshade></td></tr><tr>';
                    echo '  <td height="30">';
                    echo CATEGORY_SHIKU_TEXT . tep_draw_input_field('products_real_quantity', $pInfo->products_real_quantity,'size="8" id="qt" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"') . '&nbsp;' .CATEGORY_GE_UNIT_TEXT. '&nbsp;&nbsp;&larr;&nbsp;' . $pInfo->products_real_quantity .CATEGORY_GE_UNIT_TEXT. "\n";
                    echo '  </td>';
                    echo '  </tr><tr><td><hr size="2" noshade style="border:0;"></td></tr><tr>';
                    echo '  <td height="42" style="background-color:#ccc; padding-top:5px;">';
                    echo CATEGORY_JIAKONGZAIKU_TEXT.'&nbsp;' .  tep_draw_input_field('products_virtual_quantity', $pInfo->products_virtual_quantity,' size="8" id="qt" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"') . '&nbsp;'.CATEGORY_GE_UNIT_TEXT. '&nbsp;&nbsp;&larr;&nbsp;' . $pInfo->products_virtual_quantity . CATEGORY_GE_UNIT_TEXT . "\n";
                    echo '  </td>';
                    echo '  </tr>';
                    echo '</table>';
                    echo '<table  width="95%" cellpadding="0" cellspacing="0" border="0">';
                    echo '<tr><td>';
                    echo CATEGORY_RULI_TEXT.'</tr></td><tr><td>' . tep_draw_textarea_field('products_attention_5', 'soft', '70', '10', $pInfo->products_attention_5) . '</tr></td>';
                    echo '</table>';
                    echo '</td>';
                    echo '<td width="50%" valign="top" align="right">';
                    if (tep_get_bflag_by_product_id($pInfo->products_id)) { 
                      // 如果买取
                      echo '<table width="95%" cellpadding="0" cellspacing="0" border="1">';
                      echo '  <tr>';
                      echo '  <td height="30"><button  type="button" onclick="calculate_price()">'.CATEGORY_CAL_TITLE_TEXT.'</button></td>';
                      echo '  <td>'.CATEGORY_CAL_ORIGIN_SELECT.'</td>';
                      echo '  <td>'.CATEGORY_NEXTLINE_TEXT.'5</td>';
                      echo '  <td>'.CATEGORY_NEXTLINE_TEXT.'0</td>';
                      echo '  </tr>';
                      echo '  <tr>';
                      echo '  <td align="right" height="30">5000</td>';
                      echo '  <td align="right"><a href="javascript:void(0)" id="a_1" onclick="change_qt(this)" style="text-decoration : underline;"></a>&nbsp;</td>';
                      echo '  <td align="right"><a href="javascript:void(0)" id="a_2" onclick="change_qt(this)" style="text-decoration : underline;"></a>&nbsp;</td>';
                      echo '  <td align="right"><a href="javascript:void(0)" id="a_3" onclick="change_qt(this)" style="text-decoration : underline;"></a>&nbsp;</td>';
                      echo '  </tr>';
                      echo '  <tr>';
                      echo '  <td align="right" height="30">10000</td>';
                      echo '  <td align="right"><a href="javascript:void(0)" id="b_1" onclick="change_qt(this)" style="text-decoration : underline;"></a>&nbsp;</td>';
                      echo '  <td align="right"><a href="javascript:void(0)" id="b_2" onclick="change_qt(this)" style="text-decoration : underline;"></a>&nbsp;</td>';
                      echo '  <td align="right"><a href="javascript:void(0)" id="b_3" onclick="change_qt(this)" style="text-decoration : underline;"></a>&nbsp;</td>';
                      echo '  </tr>';
                      echo '</table>';
                    }

                    $order_history_query = tep_db_query("
                        select * 
                        from ".TABLE_ORDERS_PRODUCTS." op left join ".TABLE_ORDERS." o on op.orders_id=o.orders_id left join ".TABLE_ORDERS_STATUS." os on o.orders_status=os.orders_status_id 
                        where 
                        op.products_id='".$pInfo->products_id."'
                        order by o.torihiki_date desc
                        limit 5
                        ");
                    ?>
                      <br>
                      <table width="95%" border="1" cellspacing="0" cellpadding="2">
                      <tr>
                      <th colspan="4" align="left"><?php echo TABLE_HEADING_PRODUCT_HISTORY;?></th>
                      </tr>
                      <tr>
                      <th><?php echo TABLE_HEADING_FETCHTIME_TEXT;?></th>
                      <th><?php echo TABLE_HEADING_GESHU;?></th>
                      <th><?php echo TABLE_HEADING_DANJIA;?></th>
                      <th><?php echo TABLE_HEADING_OSTATUS;?></th>
                      </tr>
                      <?php
                      if (tep_db_num_rows($order_history_query)) {
                        $sum_price = 0;
                        $sum_quantity = 0;
                        $sum_i = 0;
                        while($order_history = tep_db_fetch_array($order_history_query)){
                          ?>
                            <tr>
                            <td class="main" width="120"><?php echo $order_history['torihiki_date'];?></td>
                            <td class="main" width="100" align="right"><?php echo $order_history['products_quantity'];?><?php echo CATEGORY_GE_UNIT_TEXT;?></td>
                            <td class="main" align="right"><?php echo display_price($order_history['final_price']);?><?php echo CATEGORY_MONEY_UNIT_TEXT;?></td>
                            <td class="main" width="100"><?php echo $order_history['orders_status_name'];?></td>
                            </tr>
                            <?php
                            $sum_i ++;
                          if ($order_history['calc_price'] == '1') {
                            $sum_price += abs($order_history['final_price']) * $order_history['products_quantity'];
                            $sum_quantity += $order_history['products_quantity'];
                          }
                        }
                        ?>
                          <tr>
                          <th></th>
                          <td class="main" align="right"><table cellspacing="0" cellpadding="0" border='0' width="100%"><tr><td align="left"><?php echo CATEGORY_TOTALNUM_TEXT;?></td><td align="right"><?php echo $sum_quantity;?><?php echo CATEGORY_GE_UNIT_TEXT;?></td></tr></table></td>
                          <td class="main" align="right"><table cellspacing="0" cellpadding="0" border='0' width="100%"><tr><td align="left"><?php echo CATEGORY_AVERAGENUM_TEXT;?></td><td align="right"><?php echo display_price($sum_price/$sum_quantity);?><?php echo CATEGORY_MONEY_UNIT_TEXT;?></td></tr></table></td>
                          <td class="main"> </td>
                          </tr>
                          <?php
                      } else {
                        echo "<tr><td colspan='4'>no orders</td></tr>";
                      }
                    ?>
                      </table>

                      <?php
                      if ($pInfo->relate_products_id) {
                        $order_history_query = tep_db_query("
                            select * 
                            from ".TABLE_ORDERS_PRODUCTS." op left join ".TABLE_ORDERS." o on op.orders_id=o.orders_id left join ".TABLE_ORDERS_STATUS." os on o.orders_status=os.orders_status_id 
                            where 
                            op.products_id='".$pInfo->relate_products_id."'
                            order by o.torihiki_date desc
                            limit 5
                            ");
                        $relate_products_name = tep_get_relate_products_name($pInfo->products_id);
                        ?>
                          <br>
                          <table width="95%" border="1" cellspacing="0" cellpadding="2">
                          <tr>
                          <th colspan="4" align="left"><?php echo TEXT_PRODUCT_LINK_PRODUCT_TEXT;?><?php 
                          echo $relate_products_name;?></th>
                          </tr>
                          <tr>
                          <th><?php echo TABLE_HEADING_FETCHTIME_TEXT;?></th>
                          <th><?php echo TABLE_HEADING_GESHU;?></th>
                          <th><?php echo TABLE_HEADING_DANJIA;?></th>
                          <th><?php echo TABLE_HEADING_OSTATUS;?></th>
                          </tr>
                          <?php
                          if (tep_db_num_rows($order_history_query)) {
                            $sum_price = 0;
                            $sum_quantity = 0;
                            $sum_i = 0;
                            while($order_history = tep_db_fetch_array($order_history_query)){
                              ?>
                                <tr>
                                <td class="main" width="120"><?php echo $order_history['torihiki_date'];?></td>
                                <td class="main" width="100" align="right"><?php echo $order_history['products_quantity'];?><?php echo CATEGORY_GE_UNIT_TEXT;?></td>
                                <td class="main" align="right"><?php echo display_price( $order_history['final_price'] );?><?php echo CATEGORY_MONEY_UNIT_TEXT;?></td>
                                <td class="main" width="100"><?php echo $order_history['orders_status_name'];?></td>
                                </tr>
                                <?php
                                $sum_i ++;
                              if ($order_history['calc_price'] == '1') {
                                $sum_price += abs($order_history['final_price']) * $order_history['products_quantity'];
                                $sum_quantity += $order_history['products_quantity'];
                              }
                            }
                            ?>
                              <tr>
                              <th></th>
                              <td class="main" align="right"><table border='0' cellspacing="0" cellpadding="0" width="100%"><tr><td align="left"><?php echo CATEGORY_TOTALNUM_TEXT;?></td><td align="right"><?php echo $sum_quantity;?><?php echo CATEGORY_GE_UNIT_TEXT;?></td></tr></table></td>
                              <td class="main" align="right"><table border='0' cellspacing="0" cellpadding="0" width="100%"><tr><td align="left"><?php echo CATEGORY_AVERAGENUM_TEXT;?></td><td align="right"><?php echo @display_price($sum_price/$sum_quantity);?><?php echo CATEGORY_MONEY_UNIT_TEXT;?></td></tr></table></td>
                              <td class="main"> </td>
                              </tr>
                              <?php
                          } else {
                            echo "<tr><td colspan='4'>no orders</td></tr>";
                          }
                        ?>
                          </table>
                          <?php
                          echo "<div class='relate_history_info'>";
                        $relate_sub_date = get_configuration_by_site_id('DB_CALC_PRICE_HISTORY_DATE');
                        $relate_row_count = tep_get_relate_product_history_sum(
                            $pInfo->relate_products_id,$relate_sub_date,$site_id);
                        $out_relate_sum_str = sprintf(TEXT_RELATE_ROW_COUNT
                            ,$relate_products_name,$relate_sub_date,intval($relate_row_count));
                        echo $out_relate_sum_str;
                        echo "</div>";
                      }
                    echo '</td>';
                    echo '</tr></table>';
                    echo CATEGORY_BOTTOM_READ; 
                    echo '</td>';
                  } else {
                    echo TEXT_PRODUCTS_PRICE_INFO.'&nbsp;' . $products_price_preview .  '<br>'.TEXT_PRODUCTS_QUANTITY_INFO.'&nbsp;' . $pInfo->products_real_quantity .  CATEGORY_GE_UNIT_TEXT. "\n";
                  }
                ?>
                  </td>
                  </tr>
                  <?php
                  if (isset($_GET['read']) && $_GET['read'] == 'only' && (!isset($_GET['origin']) || !$_GET['origin'])) { 
                    //限制显示
                    echo '<tr><td><b>'.CATEGORY_BUTTON_UPDATE_TEXT.'</b></td></tr>' . "\n";
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
              } // 结束限制显示

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
                  if ($_GET['from'] == 'admin') {
                    $back_url = FILENAME_CATEGORIES;
                    $back_url_params = 'cPath=' . $cPath;
                  } else {
                    $back_url = FILENAME_CATEGORIES;
                    $back_url_params = 'cPath=' . $cPath . '&pID=' . $pInfo->products_id;
                  }
                }
                ?>
                  <tr>
                  <td align="right">
                  <?php echo '<a href="' . tep_href_link($back_url, $back_url_params, 'NONSSL') . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td>
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
                if ($products_cart_image)
                  echo tep_draw_hidden_field('products_cart_image', stripslashes($products_cart_image_name));
                echo "<a href='".
                  tep_href_link(FILENAME_CATEGORIES,tep_get_all_get_params(array('action')).'&action=new_product'.
                      ((isset($site_id)&&$site_id)?('&site_id='.$site_id):''))."'>";
                echo tep_html_element_button(IMAGE_BACK, ' name="edit"') . '&nbsp;&nbsp;';
                echo "</a>";

                if ($_GET['pID']) {
                  echo tep_html_element_submit(IMAGE_UPDATE);
                } else {
                  echo tep_html_element_submit(IMAGE_INSERT);
                }
                echo tep_draw_hidden_field('relate_products_id', $_POST['relate_products_id']); 
                foreach ($_POST['carttags'] as $ck => $ct) {
                  echo tep_draw_hidden_field('carttags['.$ck.']', $_POST['carttags'][$ck]); 
                }
                echo '&nbsp;&nbsp;';
                $np_page = isset($_GET['page'])?'&page='.$_GET['page']:''; 
                if (isset($_POST['rdirect'])) {
                  echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' .  $cPath .  '&pID=' . $_GET['pID'].'&site_id=0'.$np_page) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>';
                } else {
                  echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' .  $cPath .  '&pID=' . $_GET['pID'].'&site_id='.$_POST['site_id'].$np_page) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>';
                }
                ?></td>
                  </form>
                  </tr>
                  <?php
              }
            } elseif (isset($_GET['action']) && ($_GET['action'] == 'new_category' || $_GET['action'] == 'edit_category')) {
              //新建/更新分类页 
              if ($_GET['action'] == 'edit_category') { 
                $categories_query_raw = "
                  select * 
                  from (
                      select c.categories_id, 
                      cd.categories_status, 
                      cd.categories_name, 
                      cd.categories_image2, 
                      cd.categories_image3, 
                      cd.categories_meta_text, 
                      c.categories_image, 
                      c.parent_id, 
                      c.sort_order, 
                      c.date_added, 
                      c.user_added,
                      cd.last_modified, 
                      cd.user_last_modified,
                      cd.character_romaji,
                      cd.alpha_romaji,
                      cd.site_id
                      from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                      where
                      c.categories_id = '".$cID."' 
                      and c.categories_id = cd.categories_id 
                      and cd.language_id='" . $languages_id ."' 
                      order by site_id DESC
                      ) c 
                      where site_id = ".((isset($_GET['site_id']) && $_GET['site_id'])?$_GET['site_id']:0)."
                      or site_id = 0
                      group by categories_id
                      order by sort_order, categories_name
                      ";
                $categories_res = tep_db_fetch_array(tep_db_query($categories_query_raw)); 
                $category_childs = array('childs_count' => tep_childs_in_category_count($categories_res['categories_id'])); 
                $category_products = array('products_count' => tep_products_in_category_count($categories_res['categories_id'])); 
                $cInfo_array = tep_array_merge($categories_res, $category_childs, $category_products); 
                $cInfo = new objectInfo($cInfo_array);
              }
              ?>
                <tr>
                <td>
                <?php 
                if ($_GET['action'] == 'new_category') {
                  echo tep_draw_form('newcategory', FILENAME_CATEGORIES, 'action=insert_category&cPath='.$cPath, 'post', 'enctype="multipart/form-data" onSubmit="return cmess(\''.$current_category_id.'\', \'\', \''.$site_id.'\')"');
                } else {
                  echo tep_draw_form('categories', FILENAME_CATEGORIES, 'action=update_category&cPath='.$cPath.(isset($_GET['rdirect'])?'&rdirect=all':'').($_GET['search']?'&search='.$_GET['search']:''), 'post', 'enctype="multipart/form-data" onSubmit="return cmess(\''.$current_category_id.'\', \''.$cInfo->categories_id.'\', \''.$site_id.'\')"');
                  echo tep_draw_hidden_field('categories_id', $cInfo->categories_id); 
                  echo tep_draw_hidden_field('site_id', $site_id); 
                }
              ?> 
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <tr>
                <td class="pageHeading">
                <?php 
                echo sprintf(TEXT_NEW_CATEGORY, tep_output_generated_category_path($cInfo->categories_id)); 
              ?>
                </td> 
                </tr>
                <tr>
                <td>
                <table border="0" cellspacing="0" cellpadding="2" width="100%">
                <tr>
                <td class="main" align="right">
                <?php
                echo tep_html_element_submit(IMAGE_SAVE);
              if ($_GET['action'] == 'new_category') {
                echo '<a href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath='.$cPath.($_GET['search']?'&search='.$_GET['search']:'')).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>'; 
              } else {
                if (isset($_GET['rdirect'])) {
                  echo '<a href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath='.$cPath.'&cID='.$cInfo->categories_id.'&site_id=0'.($_GET['search']?'&search='.$_GET['search']:'')).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>'; 
                } else {
                  echo '<a href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath='.$cPath.'&cID='.$cInfo->categories_id.'&site_id='.$site_id.($_GET['search']?'&search='.$_GET['search']:'')).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>'; 
                }
              }
              ?>
                </td> 
                </tr>
                <tr>
                <td>
                <fieldset>
                <legend style="color:#ff0000;"><?php echo CATEGORY_INFORMATION_TITLE;?></legend> 
                <table border="0" cellspacing="0" cellpadding="2">
                <?php
                $c_languages = tep_get_languages(); 
              for ($ci = 0, $cn = sizeof($c_languages); $ci < $cn; $ci++) {
                ?> 
                  <tr>
                  <td class="main" width="180"><?php echo ($_GET['action'] == 'new_category')?TEXT_CATEGORIES_NAME:TEXT_EDIT_CATEGORIES_NAME;?></td>
                  <td class="main">
                  <?php    
                  echo
                  tep_draw_input_field('categories_name['.$c_languages[$ci]['id'].']', (($_GET['action'] == 'edit_category')?tep_get_category_name($cInfo->categories_id, $c_languages[$ci]['id'], $site_id, true):''), 'id="cname" class="tdul"'); 
                ?>
                  </td>
                  </tr>
                  <tr>
                  <td class="main"><?php echo TEXT_CATEGORY_ROMAJI;;?></td>
                  <td class="main">
                  <?php
                  echo tep_draw_input_field('romaji['.$c_languages[$ci]['id'].']', (($_GET['action'] == 'edit_category')?tep_get_category_romaji($cInfo->categories_id, $c_languages[$ci]['id'], $site_id, true):''), 'id="cromaji" class="tdul"'); 
                if ($_GET['action'] == 'edit_category') {
                  echo '<input type="button" onclick="c_is_set_romaji(\''.$current_category_id.'\', \''.$cInfo->categories_id.'\', \''.$site_id.'\')" value="'.TEXT_ROMAJI_IS_SET.'">'; 
                } else {
                  echo '<input type="button" onclick="c_is_set_romaji(\''.$current_category_id.'\', \'\', \''.$site_id.'\')" value="'.TEXT_ROMAJI_IS_SET.'">'; 
                }
                echo '&nbsp;<input type="button" onclick="c_is_set_error_char(false)" value="'.IS_SET_ERROR_CHAR.'">'; 
                ?>
                  </td>
                  </tr>
                  <?php
                  if (!empty($_GET['cPath'])) {
                    $c_tmp_arr = explode('_', $_GET['cPath']); 
                    if (count($c_tmp_arr) == 1 && empty($_GET['cID'])) {
                      ?>
                        <tr>
                        <td class="main" colspan="2"><?php echo HEAD_SEARCH_TITLE;?></td>
                        </tr>
                        <tr>
                        <td class="main"><?php echo CATEGORY_CHARACTER_ROMAJI;?></td> 
                        <td class="main">
                        <?php 
                        echo tep_draw_input_field('character_romaji['.$c_languages[$ci]['id'].']', (($_GET['action'] == 'edit_category')?$cInfo->character_romaji:''), 'class="tdul"').'<br>'.HEAD_SEARCH_CHARACTER_COMMENT; 
                      ?> 
                        </td> 
                        </tr>
                        <tr>
                        <td class="main"><?php echo CATEGORY_ALPHA_ROMAJI;?></td> 
                        <td class="main">
                        <?php 
                        echo tep_draw_input_field('alpha_romaji['.$c_languages[$ci]['id'].']', (($_GET['action'] == 'edit_category')?$cInfo->alpha_romaji:''), 'class="tdul"').'<br>'.HEAD_SEARCH_ALPHA_COMMENT; 
                      ?> 
                        </td> 
                        </tr>
                        <?php
                    }
                  } else {
                    ?>
                      <tr>
                      <td class="main" colspan="2"><?php echo HEAD_SEARCH_TITLE;?></td>
                      </tr>
                      <tr>
                      <td class="main"><?php echo CATEGORY_CHARACTER_ROMAJI;?></td> 
                      <td class="main">
                      <?php 
                      echo tep_draw_input_field('character_romaji['.$c_languages[$ci]['id'].']', (($_GET['action'] == 'edit_category')?$cInfo->character_romaji:''), 'class="tdul"').'<br>'.HEAD_SEARCH_CHARACTER_COMMENT; 
                    ?> 
                      </td> 
                      </tr>
                      <tr>
                      <td class="main"><?php echo CATEGORY_ALPHA_ROMAJI;?></td> 
                      <td class="main">
                      <?php 
                      echo tep_draw_input_field('alpha_romaji['.$c_languages[$ci]['id'].']', (($_GET['action'] == 'edit_category')?$cInfo->alpha_romaji:''), 'class="tdul"').'<br>'.HEAD_SEARCH_ALPHA_COMMENT; 
                    ?> 
                      </td> 
                      </tr>
                      <?php
                  }
              }
              ?>
                </table>
                </fieldset>
                <br> 
                <fieldset>
                <legend style="color:#ff0000"><?php echo CATEGORY_PIC_TITLE;?></legend> 
                <table border="0" cellspacing="0" cellpadding="2">
                <?php
                for ($ci_num = 0, $cn_num = sizeof($c_languages); $ci_num < $cn_num; $ci_num++) {
                  ?>
                    <tr>
                    <td class="main" valign="top" width="180"><?php echo CATEGORY_IMAGE_SHOW_TEXT;?></td> 
                    <td class="main">
                    <input type="file" name="categories_image2" class="tdul"> 
                    <?php
                    if ($_GET['action'] == 'edit_category') {
                      echo '<br>';
                      echo tep_image(tep_get_web_upload_dir($site_id).'categories/'.$cInfo->categories_image2, $cInfo->categories_name);
                      echo '<br>';
                      echo tep_get_upload_dir($site_id).'categories/'; 
                      echo '<br>';
                      echo '<b>'.$cInfo->categories_image2.'</b>'; 
                    }
                  ?>
                    </td> 
                    </tr>
                    <tr>
                    <td class="main" valign="top"><?php echo CATEGORY_SECOND_IMAGE_SHOW_TEXT;?></td> 
                    <td class="main">
                    <input type="file" name="categories_image3" class="tdul"> 
                    <?php
                    if ($_GET['action'] == 'new_category') {
                      echo '<br>'.CATEGORY_INSERT_IMAGE_READ; 
                    }
                  if ($_GET['action'] == 'edit_category') {
                    echo '<br>';
                    echo tep_image(tep_get_web_upload_dir($site_id).'categories/'.$cInfo->categories_image3, $cInfo->categories_name);
                    echo '<br>';
                    echo tep_get_upload_dir($site_id).'categories/'; 
                    echo '<br>';
                    echo '<b>'.$cInfo->categories_image3.'</b>'; 
                  }
                  ?>
                    </td> 
                    </tr>
                    <?php
                }
              ?>
                <tr>
                <td class="main" valign="top"><?php echo TEXT_CATEGORIES_IMAGE;?></td> 
                <td class="main">
                <input type="file" name="categories_image" class="tdul"> 
                <?php 
                if ($_GET['action'] == 'edit_category') {
                  echo '<br>';
                  echo tep_image(tep_get_web_upload_dir($site_id).'categories/'.$cInfo->categories_image, $cInfo->categories_name);
                  echo '<br>';
                  echo tep_get_upload_dir($site_id).'categories/'; 
                  echo '<br>';
                  echo '<b>'.$cInfo->categories_image.'</b>'; 
                }
              ?>
                </td>
                </tr>
                </table> 
                </fieldset>
                <br> 
                <fieldset>
                <legend style="color:#ff0000"><?php echo CATEGORY_SEO_TITLE;?></legend> 
                <table border="0" cellspacing="0" cellpadding="2">
                <?php
                for ($ci_tmp_num = 0, $cn_tmp_num = sizeof($c_languages); $ci_tmp_num < $cn_tmp_num; $ci_tmp_num++) {
                  ?>
                    <tr>
                    <td class="main" valign="top" width="180"><?php echo CATEGORY_META_TAG_TEXT;?></td> 
                    <td class="main">
                    <?php echo tep_draw_textarea_field('categories_meta_text['.$c_languages[$ci_tmp_num]['id'].']', 'soft', 30, 3, (($_GET['action'] == 'edit_category')?tep_get_category_meta_text($cInfo->categories_id, $c_languages[$ci_tmp_num]['id'], $site_id, true):''));?> 
                    <br> 
                    <?php echo CATEGORY_META_TAG_READ_TEXT;?>
                    </td> 
                    </tr>
                    <tr>
                    <td class="main"><?php echo CATEGORY_SEO_TITLE_TEXT;?></td> 
                    <td class="main">
                    <?php 
                    echo tep_draw_input_field('seo_name['.$c_languages[$ci_tmp_num]['id'].']', (($_GET['action'] == 'edit_category')?tep_get_seo_name($cInfo->categories_id, $c_languages[$ci_tmp_num]['id'], $site_id, true):''), 'class="tdul"'); 
                  ?>
                    </td>
                    </tr>
                    <tr>
                    <td class="main" valign="top"><?php echo CATEGORY_SEO_DES_TEXT;?></td> 
                    <td class="main">
                    <?php 
                    echo tep_draw_textarea_field('seo_description['.$c_languages[$ci_tmp_num]['id'].']', 'soft', 30, 3, (($_GET['action'] == 'edit_category')?tep_get_seo_description($cInfo->categories_id, $c_languages[$ci_tmp_num]['id'], $site_id, true):'')); 
                  ?>
                    </td>
                    </tr>
                    <tr>
                    <td class="main" valign="top"><?php echo CATEGORY_HEADER_TEXT;?></td> 
                    <td class="main">
                    <?php 
                    echo tep_draw_textarea_field('categories_header_text['.$c_languages[$ci_tmp_num]['id'].']', 'soft', 30, 3, (($_GET['action'] == 'edit_category')?tep_get_categories_header_text($cInfo->categories_id, $c_languages[$ci_tmp_num]['id'], $site_id, true):'')); 
                  ?>
                    </td>
                    </tr>
                    <tr>
                    <td class="main" valign="top"><?php echo CATEGORY_FOOTER_TEXT;?></td> 
                    <td class="main">
                    <?php 
                    echo tep_draw_textarea_field('categories_footer_text['.$c_languages[$ci_tmp_num]['id'].']', 'soft', 30, 3, (($_GET['action'] == 'edit_category')?tep_get_categories_footer_text($cInfo->categories_id, $c_languages[$ci_tmp_num]['id'], $site_id, true):'')); 
                  ?>
                    </td>
                    </tr>
                    <tr>
                    <td class="main" valign="top"><?php echo CATEGORY_TEXT_INFO_TEXT;?></td> 
                    <td class="main">
                    <?php 
                    echo tep_draw_textarea_field('text_information['.$c_languages[$ci_tmp_num]['id'].']', 'soft', 30, 3, (($_GET['action'] == 'edit_category')?tep_get_text_information($cInfo->categories_id, $c_languages[$ci_tmp_num]['id'], $site_id, true):'')); 
                  ?>
                    </td>
                    </tr>
                    <tr>
                    <td class="main" valign="top"><?php echo CATEGORY_META_TITLE_TEXT;?></td> 
                    <td class="main">
                    <?php 
                    echo tep_draw_textarea_field('meta_keywords['.$c_languages[$ci_tmp_num]['id'].']', 'soft', 30, 3, (($_GET['action'] == 'edit_category')?tep_get_meta_keywords($cInfo->categories_id, $c_languages[$ci_tmp_num]['id'], $site_id, true):'')); 
                  ?>
                    </td>
                    </tr>
                    <tr>
                    <td class="main" valign="top"><?php echo CATEGORY_META_DES_TEXT;?></td> 
                    <td class="main">
                    <?php 
                    echo tep_draw_textarea_field('meta_description['.$c_languages[$ci_tmp_num]['id'].']', 'soft', 30, 3, (($_GET['action'] == 'edit_category')?tep_get_meta_description($cInfo->categories_id, $c_languages[$ci_tmp_num]['id'], $site_id, true):'')); 
                  ?>
                    </td>
                    </tr>
                    <?php
                } 
              ?>
                <?php
                if ($_GET['action'] == 'edit_category'||
                    $_GET['action'] == 'new_category') { 
                  $categories_to_mission_sql = 'SELECT c2m.*,m.keyword from ' .TABLE_CATEGORIES_TO_MISSION.' c2m ,' .TABLE_MISSION.' m' .' where c2m.mission_id = m.id and c2m.categories_id  ="'.$_GET['cID'].'"';
                  $categories_to_mission_query = tep_db_query($categories_to_mission_sql);
                  $categories_to_mission_res = tep_db_fetch_array($categories_to_mission_query);
                  ?>
                    <tr>
                    <td class="main"><?php echo TEXT_CATEGORY_KEYWORD;?></td> 
                    <td class="main">
                    <?php 
                    if(empty($site_id)){
                      echo tep_draw_input_field('keyword', $categories_to_mission_res['keyword']?$categories_to_mission_res['keyword']:'', 'class="tdul"'); 
                      if ($categories_to_mission_res) {
                        echo tep_draw_hidden_field('method', 'upload'); 
                      } else {
                        echo tep_draw_hidden_field('method', 'insert'); 
                      }
                    }else{
                      echo tep_draw_input_field('keyword',
                          $categories_to_mission_res['keyword']?$categories_to_mission_res['keyword']:'',
                          'class="readonly" disabled'); 
                    }
                  ?>
                    </td>
                    </tr>
                    <?php
                } 
              ?>
                <tr>
                <td class="main"><?php echo TEXT_SORT_ORDER;?></td> 
                <td class="main">
                <?php echo tep_draw_input_field('sort_order',
                    (($_GET['action'] ==
                      'edit_category')?$cInfo->sort_order:''),
                    'onkeyup="clearLibNum(this);" '.( ($site_id)?'class="readonly" disabled':'class="tdul"')); 
              ?>
                </td>
                </tr>
                </table> 
                </fieldset>
                </td>
                </tr>
                <tr>
                <td class="main" align="right">
                <?php 
                echo tep_eof_hidden(); 
              if ($_GET['action'] == 'new_category') {
                echo '<input type="hidden" name="user_added" value='.$user_info['name'].'>'; 
              } else {
                echo '<input type="hidden" name="user_last_modified" value='.$user_info['name'].'>'; 
              }
              echo tep_html_element_submit(IMAGE_SAVE);
              if ($_GET['action'] == 'new_category') {
                echo '<a href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath='.$cPath.($_GET['search']?'&search='.$_GET['search']:'')).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>'; 
              } else {
                if (isset($_GET['rdirect'])) {
                  echo '<a href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath='.$cPath.'&cID='.$cInfo->categories_id.'&site_id=0'.($_GET['search']?'&search='.$_GET['search']:'')).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>'; 
                } else {
                  echo '<a href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath='.$cPath.'&cID='.$cInfo->categories_id.'&site_id='.$site_id.($_GET['search']?'&search='.$_GET['search']:'')).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>'; 
                }
              }
              ?> 
                </td>
                </tr>
                </table>
                </td>
                </tr>
                </table>
                </form> 
                </td>
                </tr>
                <?php
            } else {
              //分类/商品列表页 
              ?>
                <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                <?php
                if ($cPath) {
                  $display_ca_str = display_category_link($cPath, $current_category_id, $languages_id, $site_id,FILENAME_CATEGORIES,true); 
                  echo $display_ca_str; 
                  if (empty($display_ca_str)) {
                    echo get_same_level_category($cPath, $current_category_id, $languages_id, $site_id,FILENAME_CATEGORIES,true); 
                  }
                }else{
                  echo "<td class='smallText' align='right'>";
                }
              ?>
                </td>
                <td class="smallText" align="right">
                <?php echo tep_draw_form('search', FILENAME_CATEGORIES, '', 'get') . "\n"; ?>
                <table border="0">
                <tr>
                <td>
                <?php 
                echo tep_draw_hidden_field('site_id', isset($_GET['site_id'])?$_GET['site_id']:'0'); 
              echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search', isset($_GET['search'])?$_GET['search']:'') . "\n"; 
              ?>
                <input type="submit" value="<?php echo IMAGE_SEARCH;?>">
                </td>
                </tr>
                </table> 
                </form>
                </td>
                <td class="smallText" align="right" width="60">
                <?php echo tep_draw_form('goto', FILENAME_CATEGORIES, '', 'get') . "\n"; ?>

                <div id="gotomenu" style="width:100%">
                <a href="javascript:void(0)" onclick="display()"><?php echo CATEGORY_TREE_SELECT_TEXT;?></a>
                </div>
                </form>
                </td>
                </tr>
                <tr>
                <td class="pageHeading" colspan="3">
                <?php echo BOX_CATALOG_CATEGORIES_PRODUCTS; ?>
                <?php
                if ($cPath) {
                  $show_ca_query = tep_db_query("select * from (select c.categories_id,cd.site_id, cd.categories_name from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id =cd.categories_id and c.categories_id ='".$current_category_id."' and cd.language_id = '4' order by site_id DESC) c where site_id = '0' or site_id ='".$site_id."'group by categories_id limit 1");
                  $show_ca_res = tep_db_fetch_array($show_ca_query);
                  echo '&nbsp;'.$show_ca_res['categories_name'];
                }
              ?>
                </td>
                </tr>
                </table>
                </td>
                </tr>
                <tr>
                <td>
                <?php
                //取得价格/业者更新时间 
                $set_menu_list  = tep_db_fetch_array(tep_db_query("select * from set_menu_list where categories_id='".$current_category_id."'"));
              $kakaku_updated = $set_menu_list?date('n/j G:i',strtotime($set_menu_list['last_modified'])):'';
              ?>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                <td valign="top">
                <?php tep_site_filter(FILENAME_CATEGORIES, true);?> 
                <?php
              $categories_table_form = '<form name="myForm1" action="'.  tep_href_link(FILENAME_CATEGORIES, tep_get_all_get_params('action').'action=all_update').'" method="POST" onSubmit="return false"> ';
              // 商品列表
              $categories_table_params = array('width'=>'100%','cellpadding'=>'2','border'=>'0',
                  'cellspacing'=>'0','parameters'=>'id="products_list_table"');
              $notice_box = new notice_box('','',$categories_table_params);
              $categories_table_row = array();
              $categories_title_row[] = array('params'=>'class="dataTableHeadingContent"','text'=>'<input type="checkbox" name="all_check" onclick="all_products_check(\'categories_id_list[]\');all_products_check(\'products_id_list[]\');">');
              $categories_title_row[] = array('params'=>'class="dataTableHeadingContent"','text'=>TABLE_HEADING_CATEGORIES_PRODUCTS);
              $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"',
                  'text'=>TABLE_HEADING_CATEGORIES_PREORDER_NUM);
              $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"',
                  'text'=>TABLE_HEADING_CATEGORIES_ORDER_NUM);
              $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"',
                  'text'=>TABLE_HEADING_CATEGORIES_PRODUCT_VIRTUAL_STORE);
              $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"',
                  'text'=>TABLE_HEADING_CATEGORIES_PRODUCT_REAL_QUANTITY);
              $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"','text'=>'');
              $bairitu_str = '';
              $res=tep_db_query("select bairitu from set_auto_calc where parent_id='".$current_category_id."'"); 
              $col=tep_db_fetch_array($res);
              if (!$col) $col['bairitu'] = 1.1;
              ?>
                <?php 
                if (empty($site_id)) {
                  $bairitu .= '<a href="'.tep_href_link('cleate_list.php', 'cid='.$cPath_yobi.'&action=prelist&cPath='.$_GET['cPath']).'" style="font-weight:bold" class="title_text_link">'.TABLE_HEADING_CATEGORIES_PRODUCT_BUYING.'</a>';
                } else {
                  $bairitu .= TABLE_HEADING_CATEGORIES_PRODUCT_BUYING;
                }
              $bairitu .= '<br><small style="font-weight:bold;font-size:12px;">'.str_replace(' ', '<br>', $kakaku_updated).'</small>';
              $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"', 'text'=>$bairitu);
              if ($cPath_yobi){
                $res=tep_db_query("select count(*) as cnt from set_dougyousya_names sdn ,set_dougyousya_categories sdc  where sdn.dougyousya_id = sdc.dougyousya_id and sdc.categories_id = '".$cPath_yobi."'");
                $count_dougyousya=tep_db_fetch_array($res);
                if($count_dougyousya['cnt'] > 0) {
                  $res=tep_db_query("select * from set_dougyousya_names sdn ,set_dougyousya_categories sdc  where sdn.dougyousya_id = sdc.dougyousya_id and sdc.categories_id = '".$cPath_yobi. "' ORDER BY sdn.sort_order,sdc.dougyousya_id ASC");
                  while($col_dougyousya=tep_db_fetch_array($res)){
                    $temp_categories_title_str = '';
                    $i++;
                    $dougyousya_history = tep_db_fetch_array(tep_db_query("select * from set_dougyousya_history where categories_id='".$current_category_id."' and dougyousya_id='".$col_dougyousya['dougyousya_id']."' order by last_date desc"));
                    $dougyousya_updated = $dougyousya_history?date('n/j G:i',strtotime($dougyousya_history['last_date'])):'';
                    ?>
                      <?php
                      if (empty($site_id)) {
                        $temp_categories_title_str .= "<a style=\"font-weight:bold;\" class=\"title_text_link\" href='javascript:void(0);' onClick=dougyousya_history('history.php',".$cPath_yobi.",".$current_category_id.",'dougyousya_categories','".$col_dougyousya['dougyousya_id']."','".$_GET['cPath']."')>".$col_dougyousya['dougyousya_name']."</a>";
                      } else {
                        $temp_categories_title_str .= $col_dougyousya['dougyousya_name'];
                      }
                    $temp_categories_title_str .= "<input type='hidden' name='d_id[]' value='".$col_dougyousya['dougyousya_id']."'>";
                    $temp_categories_title_str .= "<br><small style=\"font-weight:bold;font-size:12px\">".
                      str_replace(' ','<br>',$dougyousya_updated)."</small>";
                    $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"',
                        'text'=>$temp_categories_title_str);
                  }
                } else {
                  $count_dougyousya['cnt'] = 1;
                  $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"',
                      'text'=>TABLE_HEADING_CATEGORIES_PEER_PERSON_NO_SETTING);
                }
              }
              $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"', 
                  'text'=>TABLE_HEADING_CATEGORIES_PRODUCT_NOW_PRICE);
              $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"', 
                  'text'=>TABLE_HEADING_CATEGORIES_PRODUCT_SETTING_PRICE);
              $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"', 
                  'text'=>'&nbsp;');
              $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"', 
                  'text'=>TABLE_HEADING_STATUS);
              $categories_title_row[] = array('align'=>'center','params'=>'class="dataTableHeadingContent"', 
                  'text'=>IMAGE_UPDATE);
              $categories_title_row[] = array('align'=>'right','params'=>'class="dataTableHeadingContent"', 
                  'text'=>TABLE_HEADING_ACTION.'&nbsp');
              // 商品列表标题
              $categories_table_row[] = array('params' => 'valign="top" class="dataTableHeadingRow"',
                  'text' => $categories_title_row);
              $categories_count = 0;
              $rows = 0;
              if (isset($_GET['search']) && $_GET['search']) {
                $categories_query_raw = "
                  select c.categories_id, 
                         cd.categories_status, 
                         cd.categories_name, 
                         cd.categories_image2, 
                         cd.categories_image3, 
                         cd.categories_meta_text, 
                         cd.character_romaji,
                         cd.alpha_romaji,
                         c.categories_image, 
                         c.parent_id, 
                         c.sort_order, 
                         c.date_added, 
                         cd.last_modified, 
                         cd.user_last_modified
                           from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                           where c.categories_id = cd.categories_id 
                           and cd.language_id = '" . $languages_id . "' 
                           and cd.categories_name like '%" . $_GET['search'] . "%' ";
                if(isset($_GET['site_id'])&&$_GET['site_id']){
                  $categories_query_raw .= " and cd.site_id = '".$_GET['site_id']."' ";
                }else{
                  $categories_query_raw .= " and cd.site_id = '0' ";
                }
                $categories_query_raw .= " order by c.sort_order, cd.categories_name";
              } else {
                $categories_query_raw = "
                  select * 
                  from (
                      select c.categories_id, 
                      cd.categories_status, 
                      cd.categories_name, 
                      cd.categories_image2, 
                      cd.categories_image3, 
                      cd.categories_meta_text, 
                      c.categories_image, 
                      c.parent_id, 
                      c.sort_order, 
                      c.date_added, 
                      c.user_added,
                      cd.last_modified, 
                      cd.user_last_modified,
                      cd.character_romaji,
                      cd.alpha_romaji,
                      cd.site_id
                      from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                      where
                      c.parent_id = '".$current_category_id."' 
                      and c.categories_id = cd.categories_id 
                      and cd.language_id='" . $languages_id ."' 
                      order by site_id DESC
                      ) c 
                      where site_id = ".((isset($_GET['site_id']) && $_GET['site_id'])?$_GET['site_id']:0)."
                      or site_id = 0
                      group by categories_id
                      order by sort_order, categories_name
                      ";
              }
              $categories_query = tep_db_query($categories_query_raw);
              while ($categories = tep_db_fetch_array($categories_query)) {
                $categories_count++;
                $rows++;
                //表格通用 用的临时变量
                $categories_table_row_params = '';
                $categories_table_content_row = array();
                $categories_name_params = '';
                $categories_name_text ='';
                $categories_colspan_params = '';
                $categories_colspan_text ='';
                $categories_status_params = '';
                $categories_status_text ='';
                $categories_change_params = '';
                $categories_change_text ='';
                $categories_operation_params = '';
                $categories_operation_text ='';
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

                // 每列弄成不同的颜色
                $even = 'dataTableSecondRow';
                $odd = 'dataTableRow';
                if (isset($nowColor) && $nowColor == $odd) {
                  $nowColor = $even;
                } else {
                  $nowColor = $odd;
                }

                if ( (isset($cInfo) && is_object($cInfo)) && ($categories['categories_id'] == $cInfo->categories_id) ) {
                  $select_single = 1; 
                  if($rows == 1 && !isset($_GET['cID'])){
                    $categories_table_row_params .= 'class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'"';
                  }else{
                    $categories_table_row_params .= 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"'; 
                  }
                } else {
                  $categories_table_row_params .= 'class="' . $nowColor . '" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'' . $nowColor . '\'"';
                }
                if ( (isset($cInfo) && is_object($cInfo)) && ($categories['categories_id'] == $cInfo->categories_id) ) {
                  $categories_name_params .= 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, tep_get_path($categories['categories_id']).'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0))) . '\'"';
                } else {
                  $categories_name_params .= 'class="dataTableContent" onclick="document.location.href=\'' .  tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .(isset($_GET['page'])&&$_GET['page'] ? ('&page=' .  $_GET['page']) : '') . '&cID=' .  $categories['categories_id'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).(isset($_GET['search'])?'&search='.$_GET['search']:''))
                    .'\'" ';
                }
                $categories_name_text .= '<a href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath='.$cPath.'&cID='.$categories['categories_id'].'&action=edit_category'.(!empty($_GET['site_id'])?'&site_id='.$_GET['site_id']:'').(isset($_GET['search'])?$_GET['search']:'')).'">'.tep_image(DIR_WS_ICONS.'preview.gif', ICON_PREVIEW).'</a>&nbsp;'; 
                $categories_name_text .= '<a href="'.tep_href_link(FILENAME_ORDERS, 'search_type=categories_id&scategories_id='.$categories['categories_id']).(!empty($site_id)?'&site_id='.$site_id:'').'&order_sort=torihiki_date&order_type=desc">'.tep_image(DIR_WS_ICONS.'search.gif', IMAGE_SEARCH).'</a>&nbsp;'; 
                $categories_name_text .=  '<a href="' .  tep_href_link(FILENAME_CATEGORIES, tep_get_path($categories['categories_id']).'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0))) . '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) .  '</a>&nbsp;'; 
                $categories_name_text .= '<a class="title_text_link" href="' .  tep_href_link(FILENAME_CATEGORIES, tep_get_path($categories['categories_id']).'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0))) . '">' . '<b>'.$categories['categories_name'].'</b>&nbsp;' .  '</a>';
                $tmp_count_cnt = 9 + $count_dougyousya['cnt']; 
                if ( (isset($cInfo) && is_object($cInfo)) && ($categories['categories_id'] == $cInfo->categories_id) ) {
                  $categories_colspan_params .= 'class="dataTableContent" align="right" colspan="'.$tmp_count_cnt.'" onclick="document.location.href=\'' .  tep_href_link(FILENAME_CATEGORIES, tep_get_path($categories['categories_id']).'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0))) . '\'"';
                  $categories_colspan_text .="&nbsp;";
                } else {
                  $categories_colspan_params .= 'class="dataTableContent" align="right" colspan="'.$tmp_count_cnt.'" onclick="document.location.href=\'' .  tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .(isset($_GET['page'])&&$_GET['page'] ? ('&page=' .  $_GET['page']) : '') . '&cID=' .  $categories['categories_id'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).(isset($_GET['search'])?'&search='.$_GET['search']:'')) .'\'"';
                  $categories_colspan_text .="&nbsp;";
                }
                $categories_status_params .= 'class="dataTableContent" align="center"';
                if ($ocertify->npermission == 15 or $ocertify->npermission == 10) {
                  $c_page = (isset($_GET['page']))?'&page='.$_GET['page']:''; 
                  $re_site_id = (isset($_GET['site_id']))?$_GET['site_id']:0; 
                  $unaccept_edit_single = false;
                  if (isset($_SESSION['site_permission'])) {
                    $accept_site_arr = explode(',', $_SESSION['site_permission']); 
                  } else {
                    $accept_site_arr = array(); 
                  }
                  if (!in_array($re_site_id, $accept_site_arr)) {
                    $unaccept_edit_single = true;
                  }
                  if ($_SESSION['user_permission'] == 15) {
                    $unaccept_edit_single = false;
                  }
                  if (!isset($_GET['site_id']) || $_GET['site_id'] == 0) {
                    $ca_status_arr = get_all_site_category_status($categories['categories_id']);  
                    foreach ($ca_status_arr as $cas_key => $cas_value) {
                      switch($cas_key) {
                        case 'blue':
                          if (!empty($cas_value)) {
                            ?>
                              <?php
                              if ($unaccept_edit_single) {
                                $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''. NOTICE_HAS_NO_PREVILEGE_EDIT.'\');" title="'.implode(',', $cas_value).'">'.tep_image(DIR_WS_IMAGES .  'icon_status_blue.gif', '').'</a> ';
                              } else {
                                $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=2&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')" title="'.implode(',', $cas_value).'">'.tep_image(DIR_WS_IMAGES . 'icon_status_blue.gif', '').'</a> ';
                              }
                          } else {
                            if ($unaccept_edit_single) {
                              $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''. NOTICE_HAS_NO_PREVILEGE_EDIT.'\');" >'.tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '').'</a> ';
                            } else {
                              $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=2&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')" >'.tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', '').'</a> ';
                            }
                          }
                          break;
                        case 'red':
                          if (!empty($cas_value)) {
                            ?>
                              <?php
                              if ($unaccept_edit_single) {
                                $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''. NOTICE_HAS_NO_PREVILEGE_EDIT.'\');" title="'.implode(',', $cas_value).'">'.tep_image(DIR_WS_IMAGES .  'icon_status_red.gif', '').'</a> ';
                              } else {
                                $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=1&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')" title="'.implode(',', $cas_value).'">'.tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', '').'</a> ';
                              }
                          } else {
                            if ($unaccept_edit_single) {
                              $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''. NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'.tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '').'</a> ';
                            } else {
                              $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=1&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')" >'.tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', '').'</a> ';
                            }
                          }
                          break;
                        case 'black':
                          if (!empty($cas_value)) {
                            if ($unaccept_edit_single) {
                              $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''. NOTICE_HAS_NO_PREVILEGE_EDIT.'\');" title="'.implode(',', $cas_value).'">'.tep_image(DIR_WS_IMAGES .  'icon_status_black.gif', '').'</a> ';
                            } else {
                              $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=3&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')" title="'.implode(',', $cas_value).'">'.tep_image(DIR_WS_IMAGES . 'icon_status_black.gif', '').'</a> ';
                            }
                          } else {
                            if ($unaccept_edit_single) {
                              $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''. NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'.tep_image(DIR_WS_IMAGES .  'icon_status_black_light.gif', '').'</a> ';
                            } else {
                              $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=3&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')" >'.tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', '').'</a> ';
                            }
                          }
                          break;
                        default:
                          if (!empty($cas_value)) {
                            if ($unaccept_edit_single) {
                              $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''. NOTICE_HAS_NO_PREVILEGE_EDIT.'\');" title="'.implode(',', $cas_value).'">'.tep_image(DIR_WS_IMAGES .  'icon_status_green.gif', '').'</a> ';
                            } else {
                              $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=0&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')" title="'.implode(',', $cas_value).'">'.tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', '').'</a> ';
                            }
                          } else {
                            if ($unaccept_edit_single) {
                              $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''. NOTICE_HAS_NO_PREVILEGE_EDIT.'\');" >'.tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '').'</a> ';
                            } else {
                              $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].'&status=0&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')" >'.tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '').'</a> ';
                            }
                          }
                          break;
                      }
                    }
                  } else {
                    $edit_notice_single = false;
                    if ($_GET['site_id']) {
                      $whether_ca_des_raw = tep_db_query("select * from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$categories['categories_id']."' and site_id = '".(int)$_GET['site_id']."'"); 
                      if (!tep_db_num_rows($whether_ca_des_raw)) {
                        $edit_notice_single = true;
                      }
                    }
                    if($categories['categories_status'] == '1'){
                      if ($unaccept_edit_single) {
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'.
                          tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '').'</a> ';
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'.
                          tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '').'</a> ';
                        $categories_status_text .= tep_image(DIR_WS_IMAGES .  'icon_status_red.gif', '');
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'.
                          tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', '').'</a> ';
                      } else { 
                        if ($edit_notice_single) {
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                            NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '').'</a> ';
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                            NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '').'</a> ';
                          $categories_status_text .= tep_image(DIR_WS_IMAGES .  'icon_status_red.gif', ''); 
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                            NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_black_light.gif', '').'</a> ';
                        } else {
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.
                            tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].
                                '&status=0&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')">'.
                                tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '').'</a> ';
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.
                            tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].
                                '&status=2&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')">'.
                                tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '').'</a> ';
                          $categories_status_text .= tep_image(DIR_WS_IMAGES .  'icon_status_red.gif', '');
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_black_status(\''.
                            tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].
                                '&status=3&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')">'.
                                tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', '').'</a> ';
                        }
                      }
                    } else if($categories['categories_status'] == '2'){
                      if ($unaccept_edit_single) {
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                          NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '').'</a> ';
                        $categories_status_text .= tep_image(DIR_WS_IMAGES .  'icon_status_blue.gif', '');
                        $categories_status_text .= '<a href="javascrpt:void(0);" onclick="window.alert(\''.
                          NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '').'</a> ';
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                          NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_black_light.gif', '').'</a> ';
                      } else { 
                        if ($edit_notice_single) {
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                            NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '').'</a> ';
                          $categories_status_text .=  tep_image(DIR_WS_IMAGES .  'icon_status_blue.gif', '');
                          $categories_status_text .= '<a href="javascrpt:void(0);" onclick="window.alert(\''.
                            NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '').'</a> ';
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                            NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_black_light.gif', '').'</a> ';
                        } else {
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.
                            tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].
                                '&status=0&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')">'.
                                tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '').'</a> ';
                          $categories_status_text .= tep_image(DIR_WS_IMAGES .  'icon_status_blue.gif', '');
                          $categories_status_text .= '<a href="javascrpt:void(0);" onclick="check_toggle_status(\''.
                            tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].
                                '&status=1&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')">'.
                                tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '').'</a> ';
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_black_status(\''.
                            tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].
                                '&status=3&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')">'.
                                tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', '').'</a> ';
                        }
                      }
                    } else if($categories['categories_status'] == '3'){
                      if ($unaccept_edit_single) {
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                          NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '').'</a> ';
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                          NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '').'</a> ';
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                          NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '').'</a> ';
                        $categories_status_text .= tep_image(DIR_WS_IMAGES.'icon_status_black.gif', ''); 
                      } else { 
                        if ($unaccept_edit_single) {
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                            NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '').'</a> ';
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                            NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '').'</a> ';
                          $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                            NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '').'</a> ';
                          $categories_status_text .=  tep_image(DIR_WS_IMAGES.'icon_status_black.gif', ''); 
                        } else { 
                          if ($edit_notice_single) {
                            $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                              NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '').'</a> ';
                            $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                              NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '').'</a> ';
                            $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                              NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '').'</a> ';
                            $categories_status_text .= tep_image(DIR_WS_IMAGES.'icon_status_black.gif', '');
                          } else {
                            $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.
                              tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].
                                  '&status=0&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')">'.
                                  tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '').'</a> ';
                            $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.
                              tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].
                                  '&status=2&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')">'.
                                  tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '').'</a> ';
                            $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.
                              tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].
                                  '&status=1&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')">'.
                                  tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '').'</a> ';
                            $categories_status_text .= tep_image(DIR_WS_IMAGES.'icon_status_black.gif', '');
                          }
                        }
                      }
                    } else {
                      if ($edit_notice_single) {
                        $categories_status_text .= tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', '');
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                          NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '').'</a> ';
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                          NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '').'</a> ';
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.
                          NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">'. tep_image(DIR_WS_IMAGES .  'icon_status_black_light.gif', '').'</a> ';
                      } else {
                        $categories_status_text .= tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', '');
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.
                          tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].
                              '&status=2&cPath='.$_GET['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')">'.
                              tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '').'</a> ';
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\''.
                          tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].
                              '&status=1&cPath='.$_GET['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')">'.
                              tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '').'</a> ';
                        $categories_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_black_status(\''.
                          tep_href_link(FILENAME_CATEGORIES, 'action=toggle&cID='.$categories['categories_id'].
                              '&status=3&cPath='.$HTTP_GET_VARS['cPath'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page).'\')">'.
                              tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', '').'</a> ';
                      }
                    }
                  }
                }
                $categories_table_content_row[] = array('params'=>'class="dataTableContent"','text'=>'<input type="checkbox" name="categories_id_list[]" value="'.$categories['categories_id'].'">');
                $categories_table_content_row[] = array('params'=>$categories_name_params,'text'=>$categories_name_text);
                $categories_table_content_row[] = array('params'=>$categories_colspan_params,'text'=>$categories_colspan_text);
                $categories_table_content_row[] = array('params'=>$categories_status_params, 'text'=>$categories_status_text);
                $categories_table_content_row[] = array('params'=>' class="dataTableContent"', 'text'=>'&nbsp');
                $categories_table_content_row[] = array('params'=>' class="dataTableContent" align="right"',
                    'text'=>'<a href="javascript:void(0);" onclick="show_category_info(\''.
                  $categories['categories_id'].'\', this)">'.tep_image(DIR_WS_IMAGES .  'icon_info.gif', IMAGE_ICON_INFO).'</a>&nbsp;');
                $categories_table_row[] = array('params'=>$categories_table_row_params, 'text'=>$categories_table_content_row);
              }
              // categories show list end 

              $products_count = 0;
              if (isset($_GET['search']) && $_GET['search']) {
                $products_query_raw = "
                  select p.products_id, 
                         pd.products_name, 
                         p.products_real_quantity + p.products_virtual_quantity as products_quantity,
                         p.products_real_quantity, 
                         p.products_virtual_quantity, 
                         p.products_image,
                         p.products_image2,
                         p.products_image3, 
                         p.products_price, 
                         p.products_price_offset,
                         p.products_small_sum,
                         p.products_user_added,
                         p.products_date_added, 
                         pd.products_last_modified, 
                         pd.products_user_update,
                         p.products_date_available, 
                         pd.products_status, 
                         p.products_bflag,
                         p2c.categories_id 
                           from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
                           where p.products_id = pd.products_id 
                           and pd.language_id = '" . $languages_id . "' 
                           and p.products_id = p2c.products_id 
                           and pd.products_name like '%" . $_GET['search'] . "%' ";
                if(isset($_GET['site_id'])&&$_GET['site_id']){
                  $products_query_raw .= " and pd.site_id = '".$_GET['site_id']."' ";
                }else{
                  $products_query_raw .= " and pd.site_id = 0 "; 
                }
                $products_query_raw .= " order by p.sort_order,pd.products_name, p.products_id";
              } else {
                $products_query_raw = "
                  select * from ( 
                      select p.products_id, 
                      pd.products_name, 
                      p.products_real_quantity + p.products_virtual_quantity as products_quantity, 
                      p.products_real_quantity, 
                      p.products_virtual_quantity, 
                      p.products_image,
                      p.products_image2,
                      p.products_image3, 
                      p.products_price, 
                      p.products_price_offset,
                      p.products_small_sum,
                      p.products_user_added,
                      p.products_date_added,
                      pd.products_last_modified, 
                      pd.products_user_update,
                      p.products_date_available, 
                      pd.site_id, 
                      p.sort_order, 
                      p.products_bflag,
                      pd.products_status 
                        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
                        where p.products_id = pd.products_id 
                        and pd.language_id = '" . $languages_id . "' 
                        and p.products_id = p2c.products_id 
                        and p2c.categories_id = '" . $current_category_id . "'
                        order by site_id DESC
                        ) c where  site_id = ".((isset($_GET['site_id']) && $_GET['site_id'])?$_GET['site_id']:0)." or site_id = 0 
                        group by products_id 
                        order by sort_order, products_name, products_id";
              }
              $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_PRODUCTS_ADMIN, $products_query_raw, $products_query_numrows);
              $products_query = tep_db_query($products_query_raw);
              $highlight_symbol = false; 
              if (isset($_GET['pID'])) { 
                while ($products_list = tep_db_fetch_array($products_query)) {
                  if ($products_list['products_id'] == $_GET['pID']) {
                    $highlight_symbol = true;
                    break;
                  }
                } 
              } 
              $res_kaku_list = array(); 
              $res_kaku=tep_db_query("select * from set_menu_list where categories_id='".$current_category_id."' ORDER BY set_list_id ASC");
              while($col_kaku=tep_db_fetch_array($res_kaku)){
                $res_kaku_list[] = $col_kaku; 
              } 
              $products_query = tep_db_query($products_query_raw);

              //获取各网站对应的时间限制
              $site_id_query = tep_db_query("select id from ".TABLE_SITES);
              $site_time_array = array();
              $orders_site_time_array = array();
              $site_time_str = '';
              $orders_query_str = '';
              while($site_id_array = tep_db_fetch_array($site_id_query)){

                    $site_temp_id = $site_id_array['id'];
                    $query_temp_num = '';
                    if(!empty($site_temp_id)){

                      if(get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',$site_temp_id) != ''){
                        $query_temp_num = get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',$site_temp_id);
                      }else{

                        if(get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',0) != ''){
                          $query_temp_num = get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',0); 
                        }
                      }
                    }else{
                      if(get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',0) != ''){
                        $query_temp_num = get_configuration_by_site_id('PREORDERS_PRODUCTS_EFFECTIVE_DATE',0); 
                      }
                    } 
                    $site_time_array[$site_temp_id] = $query_temp_num;  

                    //注文
                    $orders_query_temp_num = '';
                    if(!empty($site_temp_id)){

                      if(get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',$site_temp_id) != ''){
                        $orders_query_temp_num = get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',$site_temp_id);
                      }else{

                        if(get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',0) != ''){
                          $orders_query_temp_num = get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',0); 
                        }
                      }
                    }else{
                      if(get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',0) != ''){
                        $orders_query_temp_num = get_configuration_by_site_id('ORDERS_PRODUCTS_EFFECTIVE_DATE',0); 
                      }
                    } 
                    $orders_site_time_array[$site_temp_id] = $orders_query_temp_num; 
                    $orders_query_str .= "(orders.site_id = ".$site_temp_id;
                    if($orders_query_temp_num != ''){
                      $orders_query_str .= " and date_format(orders.date_purchased,'%Y-%m-%d %H:%i:%s') >= '".date('Y-m-d H:i:s',strtotime('-'.$orders_query_temp_num.' minutes'))."') or ";
                    }else{
                      $orders_query_str .= ') or ';
                    } 
              }
              tep_db_free_result($site_id_query); 
              if(in_array('',$site_time_array)){

                $site_time_str = '';
              }else{

                $site_time_max = max($site_time_array);
                $site_time_str = " and date_format(pre.date_purchased,'%Y-%m-%d %H:%i:%s') >= '".date('Y-m-d H:i:s',strtotime('-'.$site_time_max.' minutes'))."'";
              }
              if(!empty($site_id) && $site_id != 0){
                $query_num = $site_time_array[$site_id];
                $orders_query_num = $orders_site_time_array[$site_id];
              }
              while ($products = tep_db_fetch_array($products_query)) {
                $products_count++;
                $rows++;
                //表格通用 用的临时变量 产品
                $products_table_row_params = '';
                $products_table_content_row = array();
                $products_name_params = '';
                $products_name_text ='';
                $products_preorder_params = '';
                $products_preorder_text ='';
                $products_order_params = '';
                $products_order_text ='';
                $products_storage_params = '';
                $products_storage_text ='';
                $products_inventory_params = '';
                $products_inventory_text ='';
                $products_mae_image_params = '';
                $products_mae_image_text ='';
                $products_stock_params = '';
                $products_stock_text ='';
                $products_peer_params = '';
                $products_peer_text ='';
                $products_price_params = '';
                $products_price_text ='';
                $products_set_price_params = '';
                $products_set_price_text ='';
                $products_status_params = '';
                $products_status_text ='';
                $products_change_params = '';
                $products_change_text ='';
                $products_operation_params = '';
                $products_operation_text ='';


                // Get categories_id for product if search 
                if (isset($_GET['search']) && $_GET['search']) $cPath=$products['categories_id'];

                if ( 
                    ((!isset($_GET['pID']) || !$_GET['pID']) && (!isset($_GET['cID']) || !$_GET['cID']) || (isset($_GET['pID']) && $_GET['pID'] == $products['products_id'])) 
                    && (!isset($pInfo) || !$pInfo) 
                    && (!isset($cInfo) || !$cInfo) 
                    && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_') 
                   ) {
                  // find out the rating average from customer reviews
                  $pInfo_array = $products;
                  $pInfo = new objectInfo($pInfo_array);
                }
                if (!$highlight_symbol) {
                  $pInfo_array = $products;
                  $pInfo = new objectInfo($pInfo_array);
                }
                $highlight_symbol = true; 
                // 每列弄成不同的颜色
                // products list
                $even = 'dataTableSecondRow';
                $odd = 'dataTableRow';
                if (isset($nowColor) && $nowColor == $odd) {
                  $nowColor = $even;
                } else {
                  $nowColor = $odd;
                }
                if ( (isset($pInfo) && is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id)  && !isset($select_single)) {
                  if($rows == 1 && !isset($_GET['pID'])){           
                    $products_table_row_params .='class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'"';
                  }else{
                    $products_table_row_params .= 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"'; 
                  }
                } else {
                  $products_table_row_params .= 'class="' . $nowColor . '" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'' . $nowColor . '\'"';
                }
                $i_cnt=0;
                if (!empty($res_kaku_list)) {
                  foreach($res_kaku_list as $k_key => $k_value){
                    $menu_datas[$i_cnt][0]=$k_value['products_id'];
                    $menu_datas[$i_cnt][1]=tep_get_kakuukosuu_by_products_id($k_value['products_id']);
                    $menu_datas[$i_cnt][2]=$k_value['kakaku'];
                    $i_cnt++;
                  }
                }


                $products_name_params .= 'class="dataTableContent" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . ($_GET['page'] ? ('&page=' . $_GET['page']) : '' ) .  '&pID=' .  $products['products_id'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).(isset($_GET['search'])?'&search='.$_GET['search']:'')) . '\'"';
                //限制显示
                $products_name_text .= '<a href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&pID=' .  $products['products_id'] .  '&action=new_product'.(!empty($_GET['site_id'])?'&site_id='.$_GET['site_id']:'').'&page='.$_GET['page'].($_GET['search']?'&search='.$_GET['search']:'')).'">'.tep_image(DIR_WS_ICONS.'preview.gif', ICON_PREVIEW).'</a>&nbsp;'; 
                $products_name_text .= '<a href="orders.php?search_type=products_id&products_id=' .  $products['products_id'] .(!empty($site_id)?'&site_id='.$site_id:'') .'">' . tep_image(DIR_WS_ICONS . 'search.gif', IMAGE_SEARCH) . '</a>&nbsp;'; 
                if ($ocertify->npermission >= 10) { 
                  $products_name_text .= '<a class="title_text_link" href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&pID=' .  $products['products_id'] .  '&action=new_product'.(!empty($_GET['site_id'])?'&site_id='.$_GET['site_id']:'').'&page='.$_GET['page'].($_GET['search']?'&search='.$_GET['search']:'')).'"><span id="products_name_'.$products['products_id'].'">'.$products['products_name'].'</span></a>'; 
                } else {
                  $products_name_text.= '<span id="products_name_'.$products['products_id'].'">'.$products['products_name'].'</span>'; 
                }
                $products_table_content_row[] = array('params'=>'class="dataTableContent"', 'text'=>'<input type="checkbox" name="products_id_list[]" value="'.$products['products_id'].'">');
                $products_table_content_row[] = array('params'=>$products_name_params, 'text'=>$products_name_text);
                for($i=0;$i<$i_cnt;$i++){
                  if($products['products_id']==$menu_datas[$i][0]){
                    $imaginary=$menu_datas[$i][1];
                    $kakaku_treder=$menu_datas[$i][2];
                    break;
                  }else{
                    $imaginary=0;
                    $kakaku_treder=0;
                  }
                }
                //同行专用
                $query_str = ''; 
                
                if(!empty($site_id) && $site_id != 0){
                  if($query_num != ''){

                    $query_str = " and date_format(pre.date_purchased,'%Y-%m-%d %H:%i:%s') >= '".date('Y-m-d H:i:s',strtotime('-'.$query_num.' minutes'))."'";
                  }
                  $preorder_products_raw = tep_db_query("select sum(prep.products_quantity) as pre_total from ".TABLE_PREORDERS_PRODUCTS." prep ,".TABLE_PREORDERS." pre where  prep.products_id = '".$products['products_id']."' and prep.orders_id = pre.orders_id and pre.finished !='1' and pre.flag_qaf != '1'".$query_str.(!empty($site_id)?" and pre.site_id = '".$site_id."'":"")); 
                  $preorder_products_res = tep_db_fetch_array($preorder_products_raw);
                  if ($preorder_products_res) {
                    if ($preorder_products_res['pre_total']) {
                      $products_preorder_text .= '<a href="preorders.php?keywords='.urlencode($products['products_id']).'&search_type=sproducts_id'.(!empty($site_id)?'&site_id='.$site_id:'').'" style="text-decoration:underline;">';
                      $products_preorder_text .= $preorder_products_res['pre_total'];
                      $products_preorder_text .= '</a>';
                    } else {
                      $products_preorder_text .=  ''; 
                    }
                  }
                }else{
                  $preorder_products_raw = tep_db_query("select pre.site_id site_id,pre.date_purchased date_purchased,prep.products_quantity products_total from ".TABLE_PREORDERS_PRODUCTS." prep ,".TABLE_PREORDERS." pre where  prep.products_id = '".$products['products_id']."' and prep.orders_id = pre.orders_id and pre.finished !='1' and pre.flag_qaf != '1'".$site_time_str); 
                  $products_num = 0;
                  while($preorder_products_res = tep_db_fetch_array($preorder_products_raw)){

                    foreach($site_time_array as $site_key=>$site_value){

                      if($preorder_products_res['site_id'] == $site_key && $preorder_products_res['date_purchased'] >= date('Y-m-d H:i:s',strtotime('-'.$site_value.' minutes'))){
                        $products_num += $preorder_products_res['products_total']; 
                      }
                    }
                  }

                  if ($products_num) {
                      $products_preorder_text .= '<a href="preorders.php?keywords='.urlencode($products['products_id']).'&search_type=sproducts_id'.(!empty($site_id)?'&site_id='.$site_id:'').'" style="text-decoration:underline;">';
                      $products_preorder_text .= $products_num;
                      $products_preorder_text .= '</a>';
                  } else {
                      $products_preorder_text .=  ''; 
                  }
                  
                }
                $target_cnt=$products_count-1;
                $products_preorder_params .= 'class="dataTableContent" align="center"'; 
                $products_table_content_row[] = array('params'=>$products_preorder_params, 'text'=>$products_preorder_text);
                $products_order_params .= 'eclass="dataTableContent" align="center"';
                $tmp_order_product_num = tep_get_order_cnt_by_pid($products['products_id'], $site_id,$orders_query_str,$orders_query_num); 
                if($tmp_order_product_num){
                  $products_order_text .= '<a href="orders.php?keywords='.urlencode($products['products_id']).'&search_type=sproducts_id'.(!empty($site_id)?'&site_id='.$site_id:'').'" style="text-decoration:underline;">';
                  $products_order_text .= $tmp_order_product_num;
                  $products_order_text .= '</a>';  
                } 
                $products_table_content_row[] = array('params'=>$products_order_params, 'text'=>$products_order_text);
                if (empty($site_id)) {
                  $products_storage_params .= 'class="dataTableContent" align="right" onmouseover=\'this.style.cursor="pointer"\' id=\'virtual_quantity_'.$products['products_id'].'\' onclick="show_update_info(this, '. $products['products_id'].', \'1\', \'1\')"';
                  $products_storage_text .= $imaginary;
                  $products_inventory_params .= 'class="dataTableContent" align="right" onmouseover=\'this.style.cursor="pointer"\' style="font-weight:bold;" id=\'quantity_'.$products['products_id'].'\' onclick="show_update_info(this, '.$products['products_id'].', \'2\', \'1\')"';
                  $products_inventory_text .= $products['products_real_quantity'];
                } else {
                  $products_storage_params .= 'class="dataTableContent" align="right" onclick="document.location.href=\''.tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  ($_GET['page'] ? ('&page=' . $_GET['page']) : '' ) .  '&pID=' .  $products['products_id'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).(isset($_GET['search'])?'&search='.$_GET['search']:'')).'\';"';
                  $products_storage_text .= $imaginary;
                  $products_inventory_params .= 'class="dataTableContent" align="right" style="font-weight:bold;" onclick="document.location.href=\''.tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  ($_GET['page'] ? ('&page=' . $_GET['page']) : '' ) .  '&pID=' .  $products['products_id'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).(isset($_GET['search'])?'&search='.$_GET['search']:'')).'\';"';
                  $products_inventory_text .= $products['products_real_quantity'];
                }
                $products_table_content_row[] = array('params'=>$products_storage_params, 'text'=>$products_storage_text);
                $products_table_content_row[] = array('params'=>$products_inventory_params, 'text'=>$products_inventory_text);
                $products_mae_image_params .= 'class="dataTableContent"';
                $limit_time_query = tep_db_query("select * from ".TABLE_BESTSELLERS_TIME_TO_CATEGORY." where categories_id = '".$current_category_id."'"); 
                $limit_time_info = tep_db_fetch_array($limit_time_query); 
                if (tep_check_show_isbuy($products['products_id'], $limit_time_info)) { 
                  if (tep_check_best_sellers_isbuy($products['products_id'], $limit_time_info)) {
                    $diff_oday = tep_calc_limit_time_by_order_id($products['products_id'], false, $limit_time_info,true); 
                    if ($diff_oday !== '') {
                      $products_mae_image_text .= '<img onmouseover="set_image_alt_and_title(this,\''.$products['products_id'].'\',\''.$limit_time_info['limit_time'].'\',false)" src="images/icons/mae1.gif" alt="alt" title="title">'; 
                    } else { 
                      $products_mae_image_text .= '<img alt="'.PIC_MAE_ALT_TEXT_NODATA.'" title="'.PIC_MAE_ALT_TEXT_NODATA.'" src="images/icons/mae3.gif" alt="">'; 
                    }
                  } else {
                    $diff_oday = tep_calc_limit_time_by_order_id($products['products_id'], true, $limit_time_info,true); 
                    if ($diff_oday !== '') {
                      $products_mae_image_text .= '<img onmouseover="set_image_alt_and_title(this,\''.$products['products_id'].'\',\''.$limit_time_info['limit_time'].'\',true)" src="images/icons/mae2.gif" alt="alt" title="title">'; 
                    } else {
                      $products_mae_image_text .= '<img alt="'.PIC_MAE_ALT_TEXT_NODATA.'" title="'.PIC_MAE_ALT_TEXT_NODATA.'" src="images/icons/mae3.gif" alt="">'; 
                    }
                  }
                }
                $products_table_content_row[] = array('params'=>$products_mae_image_params, 'text'=>$products_mae_image_text);
                $products_stock_params .= 'class="dataTableContent" align="right" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . ($_GET['page'] ? ('&page=' . $_GET['page']) : '' ) .  '&pID=' .  $products['products_id'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).(isset($_GET['search'])?'&search='.$_GET['search']:'')) . '\'"';
                $products_stock_text .= '<span style="display:none" class = "TRADER_INPUT"  name="TRADER_INPUT[]" id="TRADER_'.$products['products_id'].'">'.($kakaku_treder?round($kakaku_treder,2):0).'</span>';
                $products_stock_text .= '<span name="INCREASE_INPUT" class = "INCREASE_INPUT">';
                if (strpos($col['bairitu'], '.') !== false) {
                  $float_number = strlen(substr($col['bairitu'], strpos($col['bairitu'], '.')));
                } else {
                  $float_number = 0;
                }
              $products_stock_text .= ceil(number_format($col['bairitu']*$kakaku_treder,$float_number,'.',''));
              $products_stock_text .= '</span>';
              $products_table_content_row[] = array('params'=>$products_stock_params, 'text'=>$products_stock_text);
                if ($cPath_yobi) {
                  if ($products_count==1) {
                    $res       = tep_db_query("select count(*) as cnt from set_dougyousya_names sdn ,set_dougyousya_categories sdc  where sdn.dougyousya_id = sdc.dougyousya_id and sdc.categories_id='".$cPath_yobi."'");
                    $count     = tep_db_fetch_array($res);
                    $radio_res = tep_db_query("select * from set_dougyousya_history where categories_id='".$current_category_id."' order by history_id asc");
                    $radio_col = tep_db_fetch_array($radio_res);
                  }
                    if ($count['cnt'] > 0) {
                      $dougyousya = get_products_dougyousya($products['products_id']);
                      $all_dougyousya = get_all_products_dougyousya($cPath_yobi, $products['products_id']);
                      $j_num = 0; 
                      for($i=0;$i<$count['cnt'];$i++) {
                        $products_peer_params = '';
                        $products_peer_text = '';
                        if (empty($site_id)) {
                          $products_peer_params .= "class='dataTableContent' align='right'";
                          $products_peer_text .= "<input type='radio' id='radio_".$target_cnt."_".$i."' value='".$all_dougyousya[$i]['dougyousya_id']."' name='chk[".$target_cnt."]' onClick='chek_radio(".$target_cnt.")'".(check_in_dougyousya($dougyousya, $all_dougyousya) ? ($all_dougyousya[$i]['dougyousya_id'] == $dougyousya?' checked':'') : ($i == 0 ? ' checked':''))."> <span name='TARGET_INPUT[]' id='target_".$target_cnt."_".$i."' >".get_dougyousya_history($products['products_id'], $all_dougyousya[$i]['dougyousya_id'])."</span> </td>";
                        } else {
                          $products_peer_params .= 'class="dataTableContent" align="right" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . ($_GET['page'] ? ('&page=' . $_GET['page']) : '' ) .  '&pID=' .  $products['products_id'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).(isset($_GET['search'])?'&search='.$_GET['search']:'')) . '\'"';
                          $products_peer_text .= "<input type='radio' disabled='disabled' name='ro_".$target_cnt."_".$i."'".(check_in_dougyousya($dougyousya, $all_dougyousya) ?  ($all_dougyousya[$i]['dougyousya_id'] == $dougyousya?' checked':'') : ($i == 0 ? ' checked':'')).">";
                          if ($j_num == 0) {
                            $products_peer_text .= "<input type='hidden' id='radio_".$target_cnt."_".$i."' value='".get_dougyousya_history($products['products_id'], $dougyousya)."' name='chk[".$target_cnt."]'>";
                          }
                          $products_peer_text .= "<span name='TARGET_INPUT[]' id='target_".$target_cnt."_".$i."' >".get_dougyousya_history($products['products_id'], $all_dougyousya[$i]['dougyousya_id'])."</span></td>";
                        }
                        $j_num++; 
                        $products_table_content_row[] = array('params'=>$products_peer_params, 'text'=>$products_peer_text);
                      }
                    } else {
                        $products_peer_params .= 'class="dataTableContent" align="center" colspan="'.$count_dougyousya['cnt'].'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . ($_GET['page'] ? ('&page=' . $_GET['page']) : '' ) .  '&pID=' .  $products['products_id'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).(isset($_GET['search'])?'&search='.$_GET['search']:'')) . '\'"';
                      if (empty($site_id)) {
                        $products_peer_text .= "<input type='radio' value='0' name='chk[".$target_cnt."]' checked>";
                      } else {
                        $products_peer_text .= "<input type='radio' value='0' name='hr_chk[".$target_cnt."]' checked disabled='disabled'>";
                        $products_peer_text .= "<input type='hidden' value='0' name='chk[".$target_cnt."]'>"; 
                      }
                      $products_peer_text .= "<span name='TARGET_INPUT[]' id='target_".$target_cnt."_0' >0</span></td>";
                      $products_table_content_row[] = array('params'=>$products_peer_params, 'text'=>$products_peer_text);
                    }
                  }
                $tmp_p_price = ($products['products_bflag'])?(0-(int)$products['products_price']):(int)$products['products_price']; 
                if (empty($site_id)) {
                  $products_price_params .= 'class="dataTableContent" align="right" onclick="show_update_info(this,'.$products['products_id'].', \'3\','. $target_cnt.')" onmouseover="this.style.cursor=\'pointer\'" ';
                } else {
                  $products_price_params .= 'class="dataTableContent" align="right" onclick="document.location.href=\''.tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  ($_GET['page'] ? ('&page=' . $_GET['page']) : '' ) .  '&pID=' .  $products['products_id'].'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).(isset($_GET['search'])?'&search='.$_GET['search']:'')).'\';"';
                }
                $product_price = tep_get_products_price($products['products_id'], $products);
                $products_price_text .= '<span id="edit_p_'.$products['products_id'].'">'; 
                if ($product_price['sprice']) {
                  $products_price_text .= '<span class="specialPrice"><b>' .  $currencies->format($product_price['sprice']) . '</b></span>';
                } else {
                  $products_price_text .= '<b>'.$currencies->format($product_price['price']).'</b>';
                }
                $products_price_text .= '</span>'; 
                $products_price_text .= '<span style="display:none;" id="h_edit_p_'.$products['products_id'].'">'.$tmp_p_price.'</span>'; 
                $products_table_content_row[] = array('params'=>$products_price_params, 'text'=>$products_price_text);
                $products_set_price_params .= 'class="dataTableContent" align="center"';
                if (empty($site_id)) {
                  $products_set_price_text .= '<input style="text-align:right;" pos="'.$products_count.'_1" class="udlr" type="text" size="6" value="'.(int)abs($products['products_price']).'" name="price[]" id="'. "price_input_".$products_count.'" onblur="event_onblur('.$products_count.')" onkeyup="clearNoNum(this);" onchange="event_onchange('.$products_count.')"><span id="price_error_'.  $products_count.'"></span>';
                } else {
                  $products_set_price_text .= '<input style="text-align:right;" pos="'.$products_count.'_1" class="udlr" type="hidden" size="6" value="'.(int)abs($products['products_price']).'" name="price[]" id="'."price_input_".$products_count.'" onblur="event_onblur('.$products_count.')" onkeyup="clearNoNum(this);" onchange="event_onchange('.$products_count.')"><span id="show_price_'.$products['products_id'].'">'.(int)abs($products['products_price']).'</span><input name="hide_price[]" type="hidden" value="'.$products['products_id'].'"><span id="price_error_'.$products_count.'" style="display:none"></span>';
                }
                $products_set_price_text .= '<input style="text-align:right;" pos="'.$products_count.'_2" class="_udlr" type="hidden" size="6" value="'.$products['products_price_offset'].'" name="offset[]" id="'."offset_input_".$products_count.'">';
                $products_table_content_row[] = array('params'=>$products_set_price_params, 'text'=>$products_set_price_text);
                $products_table_content_row[] = array('params'=>'class="dataTableContent" align="center"', 'text'=>'&nbsp;');
                $products_status_params .= 'class="dataTableContent" align="center"';
                if (empty($_GET['cPath'])) {
                  $products_status_text .= "<span name='TARGET_INPUT[]' id='target_".$target_cnt."_0' style='display:none'>0</span>";
                }
                $products_status_text .= '<input type="hidden" name="this_price[]" value="'.(int)$special_price_check.'">';
                $products_status_text .= '<input type="hidden" name="proid[]" value="'.$products['products_id'].'">';
                $products_status_text .= '<input type="hidden" name="pprice[]" value="'.abs($products['products_price']).'">';
                $products_status_text .= '<input type="hidden" name="bflag[]" value="'.$products['products_bflag'].'">';
                $p_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
                $repro_site_id = (isset($_GET['site_id']))?$_GET['site_id']:0; 
                $unaccept_pro_edit_single = false;
                if (isset($_SESSION['site_permission'])) {
                  $accept_pro_site_arr = explode(',', $_SESSION['site_permission']); 
                } else {
                  $accept_pro_site_arr = array(); 
                }
                if (!in_array($repro_site_id, $accept_pro_site_arr)) {
                  $unaccept_pro_edit_single = false;
                }
                if ($_SESSION['user_permission'] == 15) {
                  $unaccept_pro_edit_single = false;
                }
                if (!isset($_GET['site_id']) || $_GET['site_id'] == 0) {
                  $pro_status_arr = get_all_site_product_status($products['products_id']); 
                  foreach ($pro_status_arr as $pro_skey => $pro_svalue) {
                    switch ($pro_skey) {
                      case 'blue':
                        if (!empty($pro_svalue)) {
                          if ($unaccept_pro_edit_single) {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\'' .NOTICE_HAS_NO_PREVILEGE_EDIT.  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_blue.gif', '', 10, 10) . '</a>&nbsp;'; 
                          } else {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=2&pID=' .  $products['products_id'] .  '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_blue.gif', '', 10, 10) . '</a>&nbsp;'; 
                          }
                        } else {
                          if ($unaccept_pro_edit_single) {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\'' .NOTICE_HAS_NO_PREVILEGE_EDIT.  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '', 10, 10) . '</a>&nbsp;'; 
                          } else {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=2&pID=' .  $products['products_id'] .  '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '', 10, 10) . '</a>&nbsp;'; 
                          }
                        }
                        break;
                      case 'red':
                        if (!empty($pro_svalue)) {
                          if ($unaccept_pro_edit_single) {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\'' .NOTICE_HAS_NO_PREVILEGE_EDIT.  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_red.gif', '', 10, 10) . '</a>&nbsp;'; 
                          } else {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=0&pID=' .  $products['products_id'] .  '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_red.gif', '', 10, 10) . '</a>&nbsp;'; 
                          }
                        } else {
                          if ($unaccept_pro_edit_single) {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\'' .NOTICE_HAS_NO_PREVILEGE_EDIT.  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '', 10, 10) . '</a>&nbsp;'; 
                          } else {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=0&pID=' .  $products['products_id'] .  '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '', 10, 10) . '</a>&nbsp;'; 
                          }
                        }
                        break;
                      case 'black':
                        if (!empty($pro_svalue)) {
                          if ($unaccept_pro_edit_single) {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\'' .NOTICE_HAS_NO_PREVILEGE_EDIT.  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_black.gif', '', 10, 10) . '</a>'; 
                          } else {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_black_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=3&pID=' .  $products['products_id'] .  '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_black.gif', '', 10, 10) . '</a>'; 
                          }
                        } else {
                          if ($unaccept_pro_edit_single) {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\'' .NOTICE_HAS_NO_PREVILEGE_EDIT.  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_black_light.gif', '', 10, 10) . '</a>'; 
                          } else {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_black_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=3&pID=' .  $products['products_id'] .  '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_black_light.gif', '', 10, 10) . '</a>'; 
                          }
                        }
                        break;
                      default:
                        if (!empty($pro_svalue)) {
                          if ($unaccept_pro_edit_single) {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\'' .NOTICE_HAS_NO_PREVILEGE_EDIT.  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_green.gif', '', 10, 10) . '</a>&nbsp;'; 
                          } else {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=1&pID=' .  $products['products_id'] .  '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_green.gif', '', 10, 10) . '</a>&nbsp;'; 
                          }
                        } else {
                          if ($unaccept_pro_edit_single) {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\'' .NOTICE_HAS_NO_PREVILEGE_EDIT.  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '', 10, 10) . '</a>&nbsp;'; 
                          } else {
                            $products_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=1&pID=' .  $products['products_id'] .  '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')" title="'.implode(',', $pro_svalue).'">' .  tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', '', 10, 10) . '</a>&nbsp;'; 
                          }
                        }
                        break;
                    }
                  }
                } else {
                  $edit_pro_notice_single = false;
                  if ($_GET['site_id']) {
                    $whether_pro_des_raw = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$products['products_id']."' and site_id = '".(int)$_GET['site_id']."'"); 
                    if (!tep_db_num_rows($whether_pro_des_raw)) {
                      $edit_pro_notice_single = true;
                    }
                  }
                  if ($products['products_status'] == '1') {
                    if ($unaccept_pro_edit_single) {
                      $products_status_text .= tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="javascript:void(0)" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">' .  tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', 'black', 10, 10) . '</a>';
                    } else { 
                      if ($edit_pro_notice_single) {
                        $products_status_text .= tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="javascript:void(0)" onclick="window.alert(\''.NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', 'black', 10, 10) . '</a>';
                      } else {
                        $products_status_text .= tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="javascript:void(0)" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=2&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=0&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) . '\')">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="check_toggle_black_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=3&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) . '\')">' . tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', 'black', 10, 10) . '</a>';
                      }
                    }
                  } else if ($products['products_status'] == '2') {
                    if ($unaccept_pro_edit_single) {
                      $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">' .  tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' .  tep_image(DIR_WS_IMAGES . 'icon_status_blue.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">' .  tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', 'black', 10, 10) . '</a>';
                    } else { 
                      if ($edit_pro_notice_single) {
                        $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' .  tep_image(DIR_WS_IMAGES . 'icon_status_blue.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', 'black', 10, 10) . '</a>';
                      } else {
                        $products_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=1&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' .  tep_image(DIR_WS_IMAGES . 'icon_status_blue.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=0&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) . '\')">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="check_toggle_black_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=3&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) . '\')">' . tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', 'black', 10, 10) . '</a>';
                      }
                    }
                  } else if ($products['products_status'] == '3') {
                    if ($unaccept_pro_edit_single) {
                      $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) .  '</a>&nbsp'.tep_image(DIR_WS_IMAGES.'icon_status_black.gif', 'black',10, 10);
                    } else { 
                      if ($edit_pro_notice_single) {
                        $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) .  '</a>&nbsp'.tep_image(DIR_WS_IMAGES.'icon_status_black.gif', 'black',10, 10);
                      } else {
                        $products_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=1&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=2&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=0&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) . '\')">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) .  '</a>&nbsp'.tep_image(DIR_WS_IMAGES.'icon_status_black.gif', 'black',10, 10);
                      }
                    }
                  } else {
                    if ($unaccept_pro_edit_single) {
                      $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">' .  tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">' .  tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' .  tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10).'&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_HAS_NO_PREVILEGE_EDIT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', 'black', 10, 10) . '</a>';
                    } else { 
                      if ($edit_pro_notice_single) {
                        $products_status_text .= '<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' .  tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10).'&nbsp;<a href="javascript:void(0);" onclick="window.alert(\''.NOTICE_MUST_EDIT_CATEGORY_OR_PRODUCT.'\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', 'black', 10, 10) . '</a>';
                      } else {
                        $products_status_text .= '<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=1&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) .  '\')">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;<a href="javascript:void(0);" onclick="check_toggle_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=2&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) . '\')">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' .  tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10).'&nbsp;<a href="javascript:void(0);" onclick="check_toggle_black_status(\'' .  tep_href_link(FILENAME_CATEGORIES, 'action=setflag&flag=3&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page) . '\')">' . tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', 'black', 10, 10) . '</a>';
                      }
                    }
                  }
                }
                $products_table_content_row[] = array('params'=>$products_status_params, 'text'=>$products_status_text);
                $products_change_params .= 'class="dataTableContent" align="center"';
                if (empty($_GET['site_id'])) {
                  $products_change_text .= tep_get_signal_pic_info($products['products_last_modified']); 
                } else {
                  $pro_singal_raw = tep_db_query("select products_last_modified from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$products['products_id']."' and site_id = '0'"); 
                  $pro_signal_res = tep_db_fetch_array($pro_singal_raw); 
                  $products_change_text .= tep_get_signal_pic_info($pro_signal_res['products_last_modified']); 
                }
                $products_table_content_row[] = array('params'=>$products_change_params, 'text'=>$products_change_text);
                $products_operation_params .= 'class="dataTableContent" align="right"';
                $products_operation_text .= '<a href="javascript:void(0)" onclick="show_product_info(\''.$products['products_id'].'\',this)">'.tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO).'</a>&nbsp;'; 
                $products_table_content_row[] = array('params'=>$products_operation_params, 'text'=>$products_operation_text);
                $categories_table_row[] = array('params'=>$products_table_row_params, 'text'=>$products_table_content_row);
              }
              //product table list end
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

              if(empty($cPath_back)&&isset($cPath)){ 
                $res_list=tep_db_query("select parent_id from categories where categories_id ='".tep_db_prepare_input($cPath)."'");
                $col_list=tep_db_fetch_array($res_list);
                $cPath_yobi=$col_list['parent_id'];
              }
              $cPath_back = isset($cPath_back) && $cPath_back ? 'cPath=' . $cPath_back : '';
              $categories_list_end_params = 'colspan="'.(13 + $count_dougyousya['cnt']).'" align="right"';
              $categoties_list_end_text = '<input type="hidden" value="'.$cPath.'" name="cpath">';
              $categoties_list_end_text .= '<input type="hidden" value="'.$cPath_yobi.'" name="cpath_yobi">';
              $categoties_list_end_text .= '<input type="hidden" value="'.$current_category_id.'" name="cID_list">';
              if ($ocertify->npermission > 7) { 
                 $categoties_list_end_text .= '<input type="hidden" name="b[]" value="'.CATEGORY_BUTTON_CAL_SETTING.'"> ';
              }
              $categoties_list_end_text .= '<input type="hidden" value="'.CATEGORY_BUTTON_XIEYE_PRICE.'" name="d[]">';
              $categoties_list_end_text .= '<input type="hidden" name="flg_up" value="">';
              $categoties_list_end_text .= tep_eof_hidden();
              $categories_table_row[] = array('text'=>array('params'=>$categories_list_end_params,'text'=>$categoties_list_end_text));
              $notice_box->get_form($categories_table_form);
              $notice_box->get_contents($categories_table_row);
              echo $notice_box->show_notice();
                    // end table list
                    ?>
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                    <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_PRODUCTS_ADMIN, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CATEGORIES); ?></td>
                    <td class="smallText" align="right" colspan="3">
                    <?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_PRODUCTS_ADMIN, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'pID'))); ?>
                    </td>
                    </tr>
                    <tr>
                    <td class="smallText"><?php echo TEXT_CATEGORIES . '&nbsp;' . $categories_count . '<br>' . TEXT_PRODUCTS . '&nbsp;' . $products_query_numrows; ?></td>
                    <td class="smallText" align="right" colspan="3">
                    <?php
                    if ($cPath) {
                      if (!empty($cPath_back)) {
                        echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $cPath_back . '&cID=' .  $current_category_id.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0))) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>';
                      } else {
                        echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cID=' .  $current_category_id.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0))) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>';
                      }
                    }
              ?>
                <?php
                if ((!isset($_GET['search']) || !$_GET['search']) && $ocertify->npermission >= 10) { //限制显示
                  echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&action=new_category') . '">' . tep_html_element_button(IMAGE_NEW_CATEGORY) .  '</a>&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath .  '&action=new_product'.(isset($_GET['page'])?'&page='.$_GET['page']:'')) . '">' . tep_html_element_button(IMAGE_NEW_PRODUCT) . '</a>';
                }
              ?>
                <?php if (empty($site_id)) {?> 
                  <?php if ($ocertify->npermission > 7) { ?>
                    <input type='button' value='<?php echo CATEGORY_BUTTON_CAL_SETTING;?>' onClick="cleat_set('set_bairitu.php')">
                      <?php }?>
                      &nbsp;<input type='button' value='<?php echo CATEGORY_BUTTON_XIEYE_PRICE;?>' onClick="list_display('<?php echo $cPath_yobi?$cPath_yobi:0;?>','<?php echo $current_category_id;?>','<?php echo $_GET['cPath'];?>')">
                      &nbsp;<input type='button' name='x' value="<?php echo CATEGORY_BUTTON_ALL_UPDATE;?>" onClick="all_update()"> 
                      <?php }?> 
                      <select name="products_to_tags" onchange="products_tags_change(this.value);">
                      <option value="0"><?php echo TEXT_PRODUCTS_TAGS_SELECT;?></option>
                      <option value="1"><?php echo TEXT_PRODUCTS_TO_TAGS;?></option>
                      <option value="2"><?php echo TEXT_PRODUCTS_TAGS_DELETE;?></option>
                      </select>
                      </td>
                      </tr>
                      <?php
                      // google start
                      tep_display_google_results(FILENAME_CATEGORIES, true);
              // google end
              ?>
                </table>
                </td>
                </tr>
                </table></td>
                </tr>
                <?php
            }
?>
</table>
<?php
}
?>
</div>
</div></td>
<!-- body_text_eof -->
</tr>
</table>
<!-- body_eof -->
<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
<div id="wait" style="position:fixed; left:45%; top:45%; display:none; z-index:10000;"><img src="images/load.gif" alt="img"></div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
