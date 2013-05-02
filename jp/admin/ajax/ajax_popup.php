<?php
if ($_GET['action'] == 'show_category_info') {
/* -----------------------------------------------------
    功能: 显示分类信息的弹出框
    参数: $_GET['current_cid'] 分类id 
    参数: $_GET['site_id'] 网站id 
    参数: $_GET['search'] 搜索字符串 
    参数: $_GET['cPath'] 分类路径 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $site_id = isset($_GET['site_id'])?$_GET['site_id']:0; 
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  if (isset($_GET['search']) && $_GET['search']) {
    $categories_query_raw = "
      select c.categories_id, 
            cd.site_id,
            cd.categories_name,
            c.sort_order
      from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
      where c.categories_id = cd.categories_id 
        and cd.language_id = '" . $languages_id . "' 
        and cd.categories_name like '%" . $_GET['search'] . "%' ";
    if(!empty($site_id)){
      $categories_query_raw .= " and cd.site_id = '".(int)$site_id."' ";
    }else{
      $categories_query_raw .= " and cd.site_id = '0' ";
    }
    $categories_query_raw .= " order by c.sort_order, cd.categories_name";
  } else {
    $categories_query_raw = "
      select * 
        from (
            select c.categories_id,
            cd.site_id,
            cd.categories_name,
            c.sort_order
          from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
          where
            c.parent_id = '".$current_category_id."' 
            and c.categories_id = cd.categories_id 
            and cd.language_id='" . $languages_id ."' 
          order by site_id DESC
        ) c 
      where site_id = ".(int)$site_id."
         or site_id = 0
      group by categories_id
      order by sort_order, categories_name
      ";
  }
  
  $cid_array = array();
  $categories_tmp_raw = tep_db_query($categories_query_raw);
  while ($category_info = tep_db_fetch_array($categories_tmp_raw)) {
    $cid_array[] = $category_info['categories_id']; 
  }
  foreach ($cid_array as $c_key => $c_value) {
    if ($_GET['current_cid'] == $c_value) {
      break; 
    }
  }
  
  $page_str = '';
  
  if ($c_key > 0) {
    $page_str .= '<a onclick="show_category_info(\''.$cid_array[$c_key-1].'\')" href="javascript:void(0);" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
 
  if ($c_key < (count($cid_array) - 1)) {
    $page_str .= '<a onclick="show_category_info(\''.$cid_array[$c_key+1].'\')" href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
 
  $category_info_raw = tep_db_query("select cd.categories_name, c.date_added, c.user_added, cd.last_modified, cd.user_last_modified from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = '".$_GET['current_cid']."' and c.categories_id = cd.categories_id and (cd.site_id = '0' or cd.site_id = '".$site_id."') order by cd.site_id desc limit 1");
  $category_info_res = tep_db_fetch_array($category_info_raw); 
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $category_info_res['categories_name']);
  $heading[] = array('align' => 'right', 'text' => $page_str);
  
  $buttons = array();
  
  if ($ocertify->npermission >= 10) {
    if (empty($site_id)) {
      $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_MOVE, 'onclick="move_category_id(\''.$_GET['current_cid'].'\')"').'</a>'; 
      $button[] = '<a href="'.tep_href_link(FILENAME_PRODUCTS_MANUAL, 'cPath='.$_GET['cPath'].'&cID='.$_GET['current_cid'].'&action=show_categories_manual&site_id='.$site_id).'">'.tep_html_element_button(IMAGE_MANUAL).'</a>'; 
    } 
    if (!empty($site_id)) {
      if (tep_db_num_rows(tep_db_query("select categories_id from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$_GET['current_cid']."' and site_id = '".$site_id."'"))) {
        if ($ocertify->npermission == 15) {
          $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="delete_category_info(\''.$_GET['current_cid'].'\', \'1\');"').'</a>'; 
        }
      }
    } else {
      if ($ocertify->npermission == 15) {
        $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="delete_category_info(\''.$_GET['current_cid'].'\', \'0\');"').'</a>'; 
      }
    }
  }
  
  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }
  
  $category_info_row = array();
  
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_USER_ADDED.'&nbsp;'.((tep_not_null($category_info_res['user_added'])?$category_info_res['user_added']:TEXT_UNSET_DATA))), 
        array('align' => 'left', 'text' => TEXT_DATE_ADDED.'&nbsp;'.((tep_not_null(tep_datetime_short($category_info_res['date_added'])))?tep_datetime_short($category_info_res['date_added']):TEXT_UNSET_DATA)), 
      );
  
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_USER_UPDATE.'&nbsp;'.((tep_not_null($category_info_res['user_last_modified'])?$category_info_res['user_last_modified']:TEXT_UNSET_DATA))), 
        array('align' => 'left', 'text' => TEXT_LAST_MODIFIED.'&nbsp;'.((tep_not_null(tep_datetime_short($category_info_res['last_modified'])))?tep_datetime_short($category_info_res['last_modified']):TEXT_UNSET_DATA)), 
      );
  
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'colspan="2"', 'text' => TEXT_SUBCATEGORIES.'&nbsp;'.tep_childs_in_category_count($_GET['current_cid'])), 
      );
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'colspan="2"', 'text' => TEXT_PRODUCTS.'&nbsp;'.tep_products_in_category_count($_GET['current_cid'])), 
      );
  
  $notice_box->get_heading($heading);
   
  $notice_box->get_contents($category_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'move_category') {
/* -----------------------------------------------------
    功能: 移动分类信息的弹出框
    参数: $_GET['current_cid'] 分类id 
    参数: $_GET['site_id'] 网站id 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $site_id = isset($_GET['site_id'])?$_GET['site_id']:0; 
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  $category_info_raw = tep_db_query("select c.parent_id, cd.categories_name from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = '".$_GET['current_cid']."' and c.categories_id = cd.categories_id and (cd.site_id = '0' or cd.site_id = '".$site_id."') order by cd.site_id desc limit 1");
  $category_info_res = tep_db_fetch_array($category_info_raw); 
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_INFO_HEADING_MOVE_CATEGORY);
  $heading[] = array('align' => 'right', 'text' => '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>');

  $buttons = array();

  $button[] = tep_html_element_submit(IMAGE_MOVE);
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="hidden_info_box();"').'</a>';

  $buttons = array('align' => 'center', 'button' => $button); 
  
  $move_category_info = array();
  
  $move_category_info[]['text'] = array(
        array('align' => 'left', 'text' => sprintf(TEXT_MOVE_CATEGORIES_INTRO, $category_info_res['categories_name'])), 
      );
  $move_category_info[]['text'] = array(
        array('align' => 'left', 'text' => sprintf(TEXT_MOVE, $category_info_res['categories_name']).tep_draw_hidden_field('categories_id', $_GET['current_cid'])), 
      );
  $move_category_info[]['text'] = array(
        array('align' => 'left', 'text' => tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree('0', '', $_GET['current_cid']), (int)$category_info_res['parent_id'])), 
      );
 
  $form_str = tep_draw_form('move_category', FILENAME_CATEGORIES, 'action=move_category_confirm');
  
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($move_category_info, $buttons);
  $notice_box->get_eof(tep_eof_hidden());

  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'delete_category') {
/* -----------------------------------------------------
    功能: 删除分类的弹出框
    参数: $_GET['current_cid'] 分类id 
    参数: $_GET['site_id'] 网站id 
    参数: $_GET['cPath'] 分类路径 
    参数: $_GET['search'] 搜索字符串 
    参数: $_GET['page'] 当前页 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $site_id = isset($_GET['site_id'])?$_GET['site_id']:0; 
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
  $category_info_raw = tep_db_query("select cd.categories_name from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = '".$_GET['current_cid']."' and c.categories_id = cd.categories_id and (cd.site_id = '0' or cd.site_id = '".$site_id."') order by cd.site_id desc limit 1");
  $category_info_res = tep_db_fetch_array($category_info_raw); 
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_INFO_HEADING_DELETE_CATEGORY);
  $heading[] = array('align' => 'right', 'text' => '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>');

  $buttons = array();

  $button[] = tep_html_element_submit(IMAGE_DELETE);
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="hidden_info_box();"').'</a>';

  $buttons = array('align' => 'center', 'button' => $button); 
  
  $delete_category_info = array();

  $delete_category_info[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_DELETE_CATEGORY_INTRO), 
      );
  
  $delete_category_info[]['text'] = array(
        array('align' => 'left', 'text' => $category_info_res['categories_name'].(empty($site_id)?tep_draw_hidden_field('categories_id', $_GET['current_cid']):'')), 
      );
  
  if (empty($site_id)) {
    $childs_count = tep_childs_in_category_count($_GET['current_cid']);
    $products_count = tep_products_in_category_count($_GET['current_cid']);
    
    if ($childs_count > 0) {
      $delete_category_info[]['text'] = array(
            array('align' => 'left', 'text' => sprintf(TEXT_DELETE_WARNING_CHILDS, $childs_count)), 
          );
    }
    
    if ($products_count > 0) {
      $delete_category_info[]['text'] = array(
            array('align' => 'left', 'text' => sprintf(TEXT_DELETE_WARNING_PRODUCTS, $products_count)), 
          );
    }
  }

  if (empty($site_id)) {
    $form_str = tep_draw_form('delete_category', FILENAME_CATEGORIES, 'action=delete_category_confirm&cPath='.$_GET['cPath'].(isset($_GET['page'])?'&page='.$_GET['page']:'').($_GET['search']?'&search='.$_GET['search']:''));
  } else {
    if (isset($_GET['rdirect'])) {
      $form_str = tep_draw_form('delete_category', FILENAME_CATEGORIES, 'action=delete_category_description_confirm&cID='.$_GET['current_cid'].'&cPath='.$_GET['cPath'].'&site_id='.$site_id.'&rdirect=all'.(isset($_GET['page'])?'&page='.$_GET['page']:'').($_GET['search']?'&search='.$_GET['search']:''));
    } else {
      $form_str = tep_draw_form('delete_category', FILENAME_CATEGORIES, 'action=delete_category_description_confirm&cID='.$_GET['current_cid'].'&cPath='.$_GET['cPath'].'&site_id='.$site_id.(isset($_GET['page'])?'&page='.$_GET['page']:'').($_GET['search']?'&search='.$_GET['search']:''));
    }
  }
  
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($delete_category_info, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'update_virtual_quantity') {
/* -----------------------------------------------------
    功能: 更新商品虚拟库存的弹出框
    参数: $_POST['pid'] 商品id 
    参数: $_POST['origin_num'] 数量 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
  $product_info_raw = tep_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$_POST['pid']."'");
  $product_info = tep_db_fetch_array($product_info_raw);
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TABLE_HEADING_CATEGORIES_PRODUCT_VIRTUAL_STORE);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  $buttons = array();
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CONFIRM, 'onclick="update_virtual_quantity(\''.$_POST['pid'].'\')"').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="hidden_info_box();"').'</a>'; 
    
  $buttons = array('align' => 'center', 'button' => $button); 
 
  $update_info_array = array();
  
  $update_info_array[]['text'] = array(
        array('align' => 'left', 'params' => 'colspan="2"', 'text' => $product_info['products_name']), 
      );
  
  $update_info_array[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_POPUP_WINDOW_SHOW.':'), 
        array('align' => 'left', 'text' => $_POST['origin_num']), 
      );
  
  $update_info_array[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_POPUP_WINDOW_EDIT.':'), 
        array('align' => 'left', 'text' => tep_draw_input_field('virtual_pro_num', $_POST['origin_num'], 'id="virtual_pro_num"')), 
      );
  
  $notice_box->get_heading($heading);
   
  $notice_box->get_contents($update_info_array, $buttons);
  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'update_real_quantity') {
/* -----------------------------------------------------
    功能: 更新商品数量的弹出框
    参数: $_POST['pid'] 商品id 
    参数: $_POST['origin_num'] 数量 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
  $product_info_raw = tep_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$_POST['pid']."'");
  $product_info = tep_db_fetch_array($product_info_raw);
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TABLE_HEADING_CATEGORIES_PRODUCT_REAL_QUANTITY);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  $buttons = array();
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CONFIRM, 'onclick="update_quantity(\''.$_POST['pid'].'\')"').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="hidden_info_box();"').'</a>'; 
    
  $buttons = array('align' => 'center', 'button' => $button); 
 
  $update_info_array = array();
  
  $update_info_array[]['text'] = array(
        array('align' => 'left', 'params' => 'colspan="2"', 'text' => $product_info['products_name']), 
      );
  
  $update_info_array[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_POPUP_WINDOW_SHOW.':'), 
        array('align' => 'left', 'text' => $_POST['origin_num']), 
      );
  
  $update_info_array[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_POPUP_WINDOW_EDIT.':'), 
        array('align' => 'left', 'text' => tep_draw_input_field('real_pro_num', $_POST['origin_num'], 'id="real_pro_num"')), 
      );
  
  $notice_box->get_heading($heading);
   
  $notice_box->get_contents($update_info_array, $buttons);
  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'set_new_price') {
/* -----------------------------------------------------
    功能: 设置商品价格的弹出框
    参数: $_POST['pid'] 商品id 
    参数: $_POST['origin_price'] 价格 
    参数: $_POST['cnt_num'] 目标代号 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
  $product_info_raw = tep_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$_POST['pid']."'");
  $product_info = tep_db_fetch_array($product_info_raw);
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TABLE_HEADING_DANJIA);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  $buttons = array();
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CONFIRM, 'onclick="set_new_price(\''.$_POST['pid'].'\', \''.$_POST['cnt_num'].'\')"').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="hidden_info_box();"').'</a>'; 
    
  $buttons = array('align' => 'center', 'button' => $button); 
 
  $update_info_array = array();
  
  $update_info_array[]['text'] = array(
        array('align' => 'left', 'params' => 'colspan="2"', 'text' => $product_info['products_name']), 
      );
  
  $update_info_array[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_POPUP_WINDOW_SHOW.':'), 
        array('align' => 'left', 'text' => $_POST['origin_price']), 
      );
  
  $update_info_array[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_POPUP_WINDOW_EDIT.':'), 
        array('align' => 'left', 'text' => tep_draw_input_field('new_confirm_price', $_POST['origin_price'], 'id="new_confirm_price"')), 
      );
  
  $notice_box->get_heading($heading);
   
  $notice_box->get_contents($update_info_array, $buttons);
  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'product_info_box') {
/* -----------------------------------------------------
    功能: 显示商品信息的弹出框
    参数: $_GET['pID'] 商品id 
    参数: $_GET['site_id'] 网站id 
    参数: $_GET['page'] 当前页 
    参数: $_GET['cPath'] 分类路径 
    参数: $_GET['search'] 搜索字符串 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $site_id = isset($_GET['site_id'])?$_GET['site_id']:0; 
  $isstaff = true;;
  if ($ocertify->npermission >= 10) {
    $isstaff = false;
  } 
  $pInfo = tep_get_pinfo_by_pid($_GET['pID'], $site_id);
  $cPath = $_GET['cPath'];
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
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
             p.products_user_added,
             p.products_date_added, 
             pd.products_last_modified, 
             pd.products_user_update,
             p.products_date_available, 
             pd.products_status, 
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
             p.products_user_added,
             p.products_date_added, 
             pd.products_last_modified, 
             pd.products_user_update,
             p.products_date_available, 
             pd.site_id, 
             p.sort_order, 
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
  $pid_arr = array();
  $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_PRODUCTS_ADMIN, $products_query_raw, $products_query_numrows);
  $products_query = tep_db_query($products_query_raw);
  while($products_row = tep_db_fetch_array($products_query)){
    $pid_arr[] = $products_row['products_id'];
  }
  foreach($pid_arr as $p_key => $p_value){
    if($_GET['pID'] == $p_value){
      break;
    }
  }
  $page_str = '';

  if($p_key > 0){ 
    $page_str .= '<a onclick="show_product_info(\''.$pid_arr[$p_key - 1].'\', \'\');" href="javascript:void(0);" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp';
  }
  
  if($p_key < count($pid_arr)-1){
    $page_str .= '<a onclick="show_product_info(\''.$pid_arr[$p_key + 1].'\', \'\');" href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp';
  } 
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';

  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $pInfo->products_name);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  $buttons = array();
 
  if (!$isstaff) {
    if (empty($site_id)) {
      $button[] = '<a href="' .  tep_href_link(FILENAME_PRODUCTS_MANUAL, 'cPath=' .  $cPath . '&pID=' .  $pInfo->products_id .  '&action=show_products_manual'.  '&site_id='.  $site_id.  '&page='.$_GET['page']) .'">'.tep_html_element_button(IMAGE_MANUAL).'</a>';
    }
      $button[] = '<a href="' . tep_href_link(FILENAME_REVIEWS, 'cPath=' . $cPath .  '&products_id=' . $pInfo->products_id .  '&action=new'.($_GET['search']?'&search='.$_GET['search']:'')) .  '">'.tep_html_element_button(IMAGE_REVIEWS).'</a>';
    if (empty($site_id)) {
      $button[] = '<input class="element_button" type="button" value="'.IMAGE_MOVE.'" onclick="show_product_move(\''.$pInfo->products_id.'\')">';
      $button[] = '<input class="element_button" type="button" value="'.IMAGE_COPY.'" onclick="show_product_copy(\''.$pInfo->products_id.'\')">';
    }
    if ($ocertify->npermission == 15) {
      if(isset($site_id) && $site_id != 0){
        if (tep_db_num_rows(tep_db_query("select products_id from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$pInfo->products_id."' and site_id = '".(int)$site_id."'"))) {
          $button[] = '<input class="element_button" type="button" value="'.IMAGE_DELETE.'" onclick="show_product_description_delete(\''.$pInfo->products_id.'\')">';
        }
      }else{
        $button[] = '<input class="element_button" type="button" value="'.IMAGE_DELETE.'" onclick="show_product_delete(\''.$pInfo->products_id.'\')">';
      }
    }
  } else {
    $button[] = '<a href="' . tep_href_link(FILENAME_REVIEWS, 'cPath=' . $cPath . '&products_id=' . $pInfo->products_id .  '&action=new') . '">'.tep_html_element_button(IMAGE_REVIEWS).'</a>';
  }
  if (empty($_GET['site_id'])) {
    $button[] = tep_html_element_submit(IMAGE_SAVE); 
  }
  
  $buttons = array('align' => 'center', 'type' => 'div', 'id' => 'order_del', 'params' => 'class="main"' , 'button' => $button);

  $product_info_params = array('width' => '95%', 'cellpadding' => '0', 'cellspacing' => '0', 'border' => '0', 'parameters' => 'style="margin-bottom:10px;"');
  
  $product_info_array = array();
  
  $product_tmp_price = tep_get_products_price($pInfo->products_id);
  $inventory = tep_get_product_inventory($pInfo->products_id);
 
  $product_info_array[]['text'] = array(
        array('params' => 'width="130" nowrap="nowrap"', 'text' => TABLE_HEADING_JIAGE_TEXT.':'),
        array('text' => (($product_tmp_price['sprice'])?'<s>'.$currencies->format($product_tmp_price['price']).'</s>&nbsp;':'').((!empty($_GET['site_id']))?number_format(abs($pInfo->products_price)?abs($pInfo->products_price):'0',0,'.',''):tep_draw_input_field('products_price', number_format(abs($pInfo->products_price)?abs($pInfo->products_price):'0',0,'.',''),'onkeyup="clearNoNum(this)" id="pp" size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;"')) . '&nbsp;' . CATEGORY_MONEY_UNIT_TEXT .  '&nbsp;&nbsp;&larr;&nbsp;' . (int)$pInfo->products_price .  CATEGORY_MONEY_UNIT_TEXT)
      );
  if (!$pInfo->products_bflag && $pInfo->relate_products_id) {
    $product_info_array[]['text'] = array(
          array('params' => 'nowrap="nowrap"', 'text' => mb_substr(CATEGORY_AVERAGE_PRICE,0, -1).':'),
          array('text' => @display_price(tep_get_avg_by_pid($pInfo->products_id)).CATEGORY_MONEY_UNIT_TEXT) 
        );
  }
  
  $product_info_array[]['text'] = array(
        array('params' => 'nowrap="nowrap"', 'text' => TABLE_HEADING_CATEGORIES_PRODUCT_REAL_QUANTITY.':'),
        array('text' => ((!empty($_GET['site_id']))?$pInfo->products_real_quantity:tep_draw_input_field('products_real_quantity', $pInfo->products_real_quantity,'size="8" id="qt" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"')) . '&nbsp;' .CATEGORY_GE_UNIT_TEXT.  '&nbsp;&nbsp;&larr;&nbsp;' . $pInfo->products_real_quantity .CATEGORY_GE_UNIT_TEXT)
      );
  
  $product_info_array[]['text'] = array(
        array('params' => 'nowrap="nowrap"', 'text' => TABLE_HEADING_CATEGORIES_PRODUCT_VIRTUAL_STORE.':'),
        array('text' => ((!empty($_GET['site_id']))?$pInfo->products_virtual_quantity:tep_draw_input_field('products_virtual_quantity', $pInfo->products_virtual_quantity,' size="8" id="qt" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"')) . '&nbsp;'.CATEGORY_GE_UNIT_TEXT.  '&nbsp;&nbsp;&larr;&nbsp;' . $pInfo->products_virtual_quantity .  CATEGORY_GE_UNIT_TEXT)
      );
 
  $product_info_array[]['text'] = array(
        array('params' => 'nowrap="nowrap"', 'text' => TEXT_PRODUCT_ADDORSUB_VALUE),
        array('text' => $pInfo->products_price_offset) 
      );
  
  if(empty($site_id)) {
    $product_info_array[]['text'] = array(
          array('params' => 'nowrap="nowrap"', 'text' => TEXT_MAX.':'),
          array('text' => (($isstaff)?$inventory['max']:tep_draw_input_field('inventory_max',$inventory['max']))) 
        );
    
    $product_info_array[]['text'] = array(
          array('params' => 'nowrap="nowrap"', 'text' => TEXT_MIN.':'),
          array('text' => (($isstaff)?$inventory['min']:tep_draw_input_field('inventory_min',$inventory['min']))) 
        );
  }

  $product_info_array[]['text'] = array(
        array('params' => 'nowrap="nowrap"', 'text' => TEXT_PRODUCTS_AVERAGE_RATING),
        array('text' => number_format($pInfo->average_rating,2).'%'.((!empty($site_id) || $isstaff)?tep_draw_hidden_field('inventory_max',$inventory['max']).tep_draw_hidden_field('inventory_min',$inventory['min']):'')) 
      );
  
  $product_info_str .= $notice_box->get_table($product_info_array, '', $product_info_params);
  $relate_exists_single = false;
  if (!empty($pInfo->relate_products_id)) {
    $relate_product_exists_raw = tep_db_query("select products_id from ".TABLE_PRODUCTS." where products_id = '".(int)$pInfo->relate_products_id."'"); 
    $relate_product_exists = tep_db_fetch_array($relate_product_exists_raw);
    if ($relate_product_exists) {
      $relate_exists_single = true;
    }
  }
  //关联商品信息
  if ($relate_exists_single) {
    $relate_product_info_array = array(); 
    $relate_pInfo = tep_get_pinfo_by_pid($pInfo->relate_products_id, $site_id); 
    $relate_product_tmp_price = tep_get_products_price($relate_pInfo->products_id);
    $inventory = tep_get_product_inventory($relate_pInfo->products_id);
    
    $relate_product_info_array[]['text'] = array(
          array('params' => 'colspan="2"', 'text' => TEXT_PRODUCT_LINK_PRODUCT_TEXT.$relate_pInfo->products_name) 
        );
    
    $relate_product_info_array[]['text'] = array(
          array('params' => 'width="130" nowrap="nowrap"', 'text' => TABLE_HEADING_JIAGE_TEXT.':'), 
          array('text' => tep_draw_hidden_field('relate_products_id', $relate_pInfo->products_id).(($relate_product_tmp_price['sprice'])?'<s>'.$currencies->format($relate_product_tmp_price['price']).'</s>&nbsp;':'').((!empty($_GET['site_id']))?number_format(abs($relate_pInfo->products_price)?abs($relate_pInfo->products_price):'0',0,'.',''):tep_draw_input_field('relate_products_price', number_format(abs($relate_pInfo->products_price)?abs($relate_pInfo->products_price):'0',0,'.',''),'onkeyup="clearNoNum(this)" size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;"')) . '&nbsp;' .  CATEGORY_MONEY_UNIT_TEXT .  '&nbsp;&nbsp;&larr;&nbsp;' .  (int)$relate_pInfo->products_price . CATEGORY_MONEY_UNIT_TEXT)
        );
  
    if (!$relate_pInfo->products_bflag && $relate_pInfo->relate_products_id) {
      $relate_product_info_array[]['text'] = array(
            array('params' => 'nowrap="nowrap"', 'text' => mb_substr(CATEGORY_AVERAGE_PRICE,0, -1).':'),
            array('text' => @display_price(tep_get_avg_by_pid($relate_pInfo->products_id)).CATEGORY_MONEY_UNIT_TEXT) 
          );
    }
    
    $relate_product_info_array[]['text'] = array(
          array('params' => 'nowrap="nowrap"', 'text' => TABLE_HEADING_CATEGORIES_PRODUCT_REAL_QUANTITY.':'),
          array('text' => ((!empty($_GET['site_id']))?$relate_pInfo->products_real_quantity:tep_draw_input_field('relate_products_real_quantity', $relate_pInfo->products_real_quantity,'size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"')) . '&nbsp;' .CATEGORY_GE_UNIT_TEXT.  '&nbsp;&nbsp;&larr;&nbsp;' . $relate_pInfo->products_real_quantity . CATEGORY_GE_UNIT_TEXT)
        );
  
    $relate_product_info_array[]['text'] = array(
          array('params' => 'nowrap="nowrap"', 'text' => TABLE_HEADING_CATEGORIES_PRODUCT_VIRTUAL_STORE.':'),
          array('text' => ((!empty($_GET['site_id']))?$relate_pInfo->products_virtual_quantity:tep_draw_input_field('relate_products_virtual_quantity', $relate_pInfo->products_virtual_quantity,' size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"')) . '&nbsp;'.CATEGORY_GE_UNIT_TEXT. '&nbsp;&nbsp;&larr;&nbsp;' . $relate_pInfo->products_virtual_quantity . CATEGORY_GE_UNIT_TEXT)
        );
    
    $relate_product_info_array[]['text'] = array(
          array('params' => 'nowrap="nowrap"', 'text' => TEXT_PRODUCT_ADDORSUB_VALUE),
          array('text' => $relate_pInfo->products_price_offset) 
        );
    if(empty($site_id)){
      $relate_product_info_array[]['text'] = array(
            array('params' => 'nowrap="nowrap"', 'text' => TEXT_MAX.':'),
            array('text' => (($isstaff)?$inventory['max']:tep_draw_input_field('relate_inventory_max',$inventory['max'])))
          );
      
      $relate_product_info_array[]['text'] = array(
            array('params' => 'nowrap="nowrap"', 'text' => TEXT_MIN.':'),
            array('text' => (($isstaff)?$inventory['min']:tep_draw_input_field('relate_inventory_min',$inventory['min'])))
          );
    }
    
    $relate_product_info_array[]['text'] = array(
          array('params' => 'nowrap="nowrap"', 'text' => TEXT_PRODUCTS_AVERAGE_RATING),
          array('text' => number_format($relate_pInfo->average_rating,2).'%'.((!empty($site_id) || $isstaff)?tep_draw_hidden_field('relate_inventory_max',$inventory['max']).tep_draw_hidden_field('relate_inventory_min',$inventory['min']):''))
        );
    $product_info_str .= $notice_box->get_table($relate_product_info_array, '', $product_info_params);
  }

  $history_table_params = array('width' => '95%', 'cellpadding' => '2', 'cellspacing' => '0', 'border' => '1');
  $history_info_str = '';
  if (empty($_GET['site_id'])) { 
    if (tep_get_bflag_by_product_id($pInfo->products_id)) {
      $sell_table_array = array();
      $sell_table_array[]['text'] = array(
            array('text' => '<button type="button" onclick="calculate_price()">'.CATEGORY_CAL_TITLE_TEXT.'</button>'), 
            array('text' => CATEGORY_CAL_ORIGIN_SELECT), 
            array('text' => CATEGORY_NEXTLINE_TEXT.'5'), 
            array('text' => CATEGORY_NEXTLINE_TEXT.'0') 
          );
      
      $sell_table_array[]['text'] = array(
            array('align' => 'right', 'params' => 'height="30"', 'text' => '5000'), 
            array('align' => 'right', 'text' => '<a href="javascript:void(0)" id="a_1" onclick="change_qt(this)" style="text-decoration:underline;"></a>&nbsp;'), 
            array('align' => 'right', 'text' => '<a href="javascript:void(0)" id="a_2" onclick="change_qt(this)" style="text-decoration:underline;"></a>&nbsp;'), 
            array('align' => 'right', 'text' => '<a href="javascript:void(0)" id="a_3" onclick="change_qt(this)" style="text-decoration:underline;"></a>&nbsp;') 
          );  
       
      $sell_table_array[]['text'] = array(
            array('align' => 'right', 'params' => 'height="30"', 'text' => '10000'), 
            array('align' => 'right', 'text' => '<a href="javascript:void(0)" id="b_1" onclick="change_qt(this)" style="text-decoration:underline;"></a>&nbsp;'), 
            array('align' => 'right', 'text' => '<a href="javascript:void(0)" id="b_2" onclick="change_qt(this)" style="text-decoration:underline;"></a>&nbsp;'), 
            array('align' => 'right', 'text' => '<a href="javascript:void(0)" id="b_3" onclick="change_qt(this)" style="text-decoration:underline;"></a>&nbsp;') 
          ); 
      $history_info_str .= $notice_box->get_table($sell_table_array, '', $history_table_params);
      $history_info_str .= '<br>';
    }
  }
  
  //商品历史记录 
  $order_history_query = tep_db_query("
    select * 
    from ".TABLE_ORDERS_PRODUCTS." op left join ".TABLE_ORDERS." o on op.orders_id=o.orders_id left join ".TABLE_ORDERS_STATUS." os on o.orders_status=os.orders_status_id 
    where 
    op.products_id='".$pInfo->products_id."'
    order by o.torihiki_date desc
    limit 5
  ");
  $product_history_array = array();
  $product_history_array[]['text'] = array(
        array('align' => 'left', 'params' => 'colspan="4"', 'text' => TABLE_HEADING_PRODUCT_HISTORY) 
      ); 
  $product_history_array[]['text'] = array(
        array('align' => 'center', 'params' => 'width="30%"', 'text' => TABLE_HEADING_FETCHTIME_TEXT), 
        array('align' => 'center', 'params' => 'width="15%"', 'text' => TABLE_HEADING_GESHU), 
        array('align' => 'center', 'params' => 'width="25%"', 'text' => TABLE_HEADING_DANJIA), 
        array('align' => 'center', 'text' => TABLE_HEADING_OSTATUS), 
      );      

  if (tep_db_num_rows($order_history_query)) {
    $sum_price = 0;
    $sum_quantity = 0;
    $sum_i = 0;
    while($order_history = tep_db_fetch_array($order_history_query)){
      $product_history_array[]['text'] = array(
            array('params' => 'class="main" width="120"', 'text' => $order_history['torihiki_date']), 
            array('align' => 'right', 'params' => 'class="main" width="100"', 'text' =>$order_history['products_quantity'].CATEGORY_GE_UNIT_TEXT), 
            array('align' => 'right', 'params' => 'class="main"', 'text' => display_price($order_history['final_price']).CATEGORY_MONEY_UNIT_TEXT), 
            array('params' => 'class="main" width="100"', 'text' => $order_history['orders_status_name']) 
          );   
      $sum_i++;
      if ($order_history['calc_price'] == '1') {
        $sum_price += abs($order_history['final_price']) * $order_history['products_quantity'];
        $sum_quantity += $order_history['products_quantity'];
      }
    }
    
    $product_history_table_params = array('width' => '100%', 'cellpadding' => '0', 'cellspacing' => '0', 'border' => '0');
    $product_history_row_quantity = array();
    $product_history_row_quantity[]['text'] = array(
          array('align' => 'left', 'text' => mb_substr(CATEGORY_TOTALNUM_TEXT, 1, mb_strlen(CATEGORY_TOTALNUM_TEXT, 'utf-8')-1, 'utf-8')) 
        );
    $product_history_row_quantity[]['text'] = array(
          array('align' => 'right', 'text' => $sum_quantity.CATEGORY_GE_UNIT_TEXT) 
        );
    
    $product_history_row_quantity_str = $notice_box->get_table($product_history_row_quantity, '', $product_history_table_params); 
    
    $product_history_row_average_num = array();
    $product_history_row_average_num[]['text'] = array(
          array('align' => 'left', 'text' => mb_substr(CATEGORY_AVERAGENUM_TEXT, 1, mb_strlen(CATEGORY_AVERAGENUM_TEXT, 'utf-8')-1, 'utf-8')) 
        );
    
    $product_history_row_average_num[]['text'] = array(
          array('align' => 'right', 'text' => display_price($sum_price/$sum_quantity).CATEGORY_MONEY_UNIT_TEXT) 
        );
    $product_history_row_average_num_str = $notice_box->get_table($product_history_row_average_num, '', $product_history_table_params); 
 
    $product_history_array[]['text'] = array(
          array('text' => ''), 
          array('align' => 'right', 'params' => 'class="main"', 'text' => $product_history_row_quantity_str), 
          array('align' => 'right', 'params' => 'class="main"', 'text' => $product_history_row_average_num_str), 
          array('text' => '')
        );
  } else {
    $product_history_array[]['text'] = array(
          array('params' => 'colspan="4"', 'text' => 'no orders') 
        ); 
  }
  $history_info_str .= $notice_box->get_table($product_history_array, '', $history_table_params);

  //关联商品历史记录
  if ($relate_exists_single) {
    $history_info_str .= '<br>'; 
    $relate_order_history_query = tep_db_query("
      select * 
      from ".TABLE_ORDERS_PRODUCTS." op left join ".TABLE_ORDERS." o on op.orders_id=o.orders_id left join ".TABLE_ORDERS_STATUS." os on o.orders_status=os.orders_status_id 
      where 
      op.products_id='".$pInfo->relate_products_id."'
      order by o.torihiki_date desc
      limit 5
    ");
    $relate_products_name = tep_get_relate_products_name($pInfo->products_id);
    
    $relate_product_history_array = array();
    $relate_product_history_array[]['text'] = array(
          array('align' => 'left', 'params' => 'colspan="4"', 'text' => TEXT_PRODUCT_LINK_PRODUCT_TEXT.$relate_products_name) 
        );
    $relate_product_history_array[]['text'] = array(
        array('align' => 'center', 'params' => 'width="30%"', 'text' => TABLE_HEADING_FETCHTIME_TEXT), 
        array('align' => 'center', 'params' => 'width="15%"', 'text' => TABLE_HEADING_GESHU), 
        array('align' => 'center', 'params' => 'width="25%"', 'text' => TABLE_HEADING_DANJIA), 
        array('align' => 'center', 'text' => TABLE_HEADING_OSTATUS), 
      );      
    if (tep_db_num_rows($relate_order_history_query)) {
      $sum_price = 0;
      $sum_quantity = 0;
      $sum_i = 0;
      while($relate_order_history = tep_db_fetch_array($relate_order_history_query)){
        $relate_product_history_array[]['text'] = array(
              array('params' => 'class="main" width="120"', 'text' => $relate_order_history['torihiki_date']), 
              array('align' => 'right', 'params' => 'class="main" width="100"', 'text' =>$relate_order_history['products_quantity'].CATEGORY_GE_UNIT_TEXT), 
              array('align' => 'right', 'params' => 'class="main"', 'text' => display_price($relate_order_history['final_price']).CATEGORY_MONEY_UNIT_TEXT), 
              array('params' => 'class="main" width="100"', 'text' => $relate_order_history['orders_status_name']) 
            );   
        $sum_i++;
        if ($relate_order_history['calc_price'] == '1') {
          $sum_price += abs($relate_order_history['final_price']) * $relate_order_history['products_quantity'];
          $sum_quantity += $relate_order_history['products_quantity'];
        }
      } 
      
      $relate_product_history_table_params = array('width' => '100%', 'cellpadding' => '0', 'cellspacing' => '0', 'border' => '0');
      $relate_product_history_row_quantity = array();
      $relate_product_history_row_quantity[]['text'] = array(
            array('align' => 'left', 'text' => mb_substr(CATEGORY_TOTALNUM_TEXT, 1, mb_strlen(CATEGORY_TOTALNUM_TEXT, 'utf-8')-1, 'utf-8')) 
          );
      $relate_product_history_row_quantity[]['text'] = array(
            array('align' => 'right', 'text' => $sum_quantity.CATEGORY_GE_UNIT_TEXT) 
          );
      
      $relate_product_history_row_quantity_str = $notice_box->get_table($relate_product_history_row_quantity, '', $relate_product_history_table_params); 
      
      $relate_product_history_row_average_num = array();
      $relate_product_history_row_average_num[]['text'] = array(
            array('align' => 'left', 'text' => mb_substr(CATEGORY_AVERAGENUM_TEXT, 1, mb_strlen(CATEGORY_AVERAGENUM_TEXT, 'utf-8')-1, 'utf-8')) 
          );
      
      $relate_product_history_row_average_num[]['text'] = array(
            array('align' => 'right', 'text' => display_price($sum_price/$sum_quantity).CATEGORY_MONEY_UNIT_TEXT) 
          );
      $relate_product_history_row_average_num_str = $notice_box->get_table($relate_product_history_row_average_num, '', $relate_product_history_table_params); 
   
      $relate_product_history_array[]['text'] = array(
            array('text' => ''), 
            array('align' => 'right', 'params' => 'class="main"', 'text' => $relate_product_history_row_quantity_str), 
            array('align' => 'right', 'params' => 'class="main"', 'text' => $relate_product_history_row_average_num_str), 
            array('text' => '')
          );
    } else {
      $relate_product_history_array[]['text'] = array(
            array('params' => 'colspan="4"', 'text' => 'no orders') 
          ); 
    }
    $relate_product_history_info_str = $notice_box->get_table($relate_product_history_array, '', $history_table_params);
    $history_info_str .= $relate_product_history_info_str;
    
    $relate_sub_date = get_configuration_by_site_id('DB_CALC_PRICE_HISTORY_DATE', 0);
    $relate_row_count = tep_get_relate_product_history_sum($pInfo->relate_products_id, $relate_sub_date, 0);
    $out_relate_sum_str = sprintf(TEXT_RELATE_ROW_COUNT, $relate_products_name, $relate_sub_date, intval($relate_row_count));
    $history_info_str .= '<div>'.$out_relate_sum_str.'</div>';
  }
  
  $data_info_array = array();
  $data_info_array[]['text'] = array(
        array('params' => 'width="80"', 'text' => TEXT_USER_ADDED), 
        array('params' => 'width="120"', 'text' => (!empty($pInfo->products_user_added)?$pInfo->products_user_added:TEXT_UNSET_DATA)), 
        array('params' => 'width="80"', 'text' => TEXT_DATE_ADDED), 
        array('params' => 'width="120"', 'text' => (!empty($pInfo->products_date_added)?tep_datetime_short($pInfo->products_date_added):TEXT_UNSET_DATA)) 
      ); 
  $data_info_array[]['text'] = array(
        array('params' => 'width="80"', 'text' => TEXT_USER_UPDATE), 
        array('params' => 'width="120"', 'text' => (!empty($pInfo->products_user_update)?$pInfo->products_user_update:TEXT_UNSET_DATA)), 
        array('params' => 'width="80"', 'text' => TEXT_LAST_MODIFIED), 
        array('params' => 'width="120"', 'text' => (!empty($pInfo->products_last_modified)?tep_datetime_short($pInfo->products_last_modified):TEXT_UNSET_DATA)) 
      ); 
  
  $data_table_params = array('cellpadding' => '0', 'cellspacing' => '0', 'border' => '0');
  $data_info_str = $notice_box->get_table($data_info_array, '', $data_table_params); 
  $contents  = array();
  $contents[]['text'] = array(
        array('text' => $product_info_str), 
        array('text' => $history_info_str) 
      );
  $contents[]['text'] = array(
        array('params' => 'colspan="2"', 'text' => $data_info_str), 
      );
  if (empty($_GET['site_id'])) {
    $form_action = 'simple_update_product';
    $form_str = tep_draw_form($form_action, FILENAME_CATEGORIES, 'cPath=' .  $_GET['cPath'] .  '&pID=' .  $_GET['pID'] . '&page='.$_GET['page'].  '&action=' .  $form_action.($_GET['search']?'&search='.  $_GET['search']:'').(!empty($_GET['site_id'])?'&site_id='.$_GET['site_id']:'&site_id=0'), 'post', '');
    $notice_box->get_form($form_str);
  }
  $notice_box->get_heading($heading);
  $notice_box->get_contents($contents, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'product_description_delete_box') {
/* -----------------------------------------------------
    功能: 删除商品描述的弹出框
    参数: $_GET['pID'] 商品id 
    参数: $_GET['site_id'] 网站id 
    参数: $_GET['page'] 当前页 
    参数: $_GET['cPath'] 分类路径 
    参数: $_GET['search'] 搜索字符串 
    参数: $_GET['rdirect'] 跳转标识 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN . DIR_WS_LANGUAGES .'/'. $language. '/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $site_id = isset($_GET['site_id']) ?$_GET['site_id']:0;
  $pInfo = tep_get_pinfo_by_pid($_GET['pID'],$site_id);
  $cPath = $_GET['cPath'];
  $d_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_INFO_HEADING_DELETE_PRODUCT);
  $heading[] = array('align' => 'right', 'text' => $page_str);
  
  $buttons = array();
  $button[] = tep_html_element_submit(IMAGE_DELETE); 
  $button[] = '<input type="button" value="'.IMAGE_CANCEL.'" onclick="hidden_info_box()" class="element_button">';
  
  $buttons = array('align' => 'center', 'button' => $button); 
  
  $delete_product_info = array();
  $delete_product_info[]['text'] = array(
        array('text' => TEXT_DELETE_PRODUCT_INTRO) 
      );
  $delete_product_info[]['text'] = array(
        array('text' => '<br>' . $pInfo->products_name) 
      );
  if (isset($_GET['rdirect'])) {
    $form_str = tep_draw_form('products', FILENAME_CATEGORIES, 'action=delete_product_description_confirm&site_id=' .  $_GET['site_id'] . '&pID=' . $_GET['pID'] . '&cPath=' .  $cPath.'&rdirect=all'.$d_page.($_GET['search']?'&search='.$_GET['search']:''), 'post');
  } else {
    $form_str = tep_draw_form('products', FILENAME_CATEGORIES, 'action=delete_product_description_confirm&site_id=' .  $_GET['site_id'] . '&pID=' . $_GET['pID'] . '&cPath=' .  $cPath.$d_page.($_GET['search']?'&search='.$_GET['search']:''), 'post');
  }
  
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
   
  $notice_box->get_contents($delete_product_info, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'product_delete_box') {
/* -----------------------------------------------------
    功能: 删除商品的弹出框
    参数: $_GET['pID'] 商品id 
    参数: $_GET['site_id'] 网站id 
    参数: $_GET['page'] 当前页 
    参数: $_GET['cPath'] 分类路径 
    参数: $_GET['search'] 搜索字符串 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $site_id = isset($_GET['site_id'])?$_GET['site_id']:0; 
  $pInfo = tep_get_pinfo_by_pid($_GET['pID'],$site_id);
  $cPath = $_GET['cPath'];
  $d_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_INFO_HEADING_DELETE_PRODUCT);
  $heading[] = array('align' => 'right', 'text' => $page_str);
  
  $buttons = array();
  $button[] = tep_html_element_submit(IMAGE_DELETE); 
  $button[] = '<input type="button" value="'.IMAGE_CANCEL.'" onclick="hidden_info_box()" class="element_button">';
  
  $buttons = array('align' => 'center', 'button' => $button); 
  
  $delete_product_info = array();
  $delete_product_info[]['text'] = array(
        array('text' => TEXT_DELETE_PRODUCT_INTRO) 
      );
  $delete_product_info[]['text'] = array(
        array('text' => '<br>' . $pInfo->products_name . tep_draw_hidden_field('products_id', $pInfo->products_id)) 
      );
  
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
  $delete_product_info[]['text'] = array(
        array('text' => '<br>' . $product_categories_string) 
      );

  $form_str = tep_draw_form('products', FILENAME_CATEGORIES, 'action=delete_product_confirm&cPath=' . $cPath.$d_page.($_GET['search']?'&search='.$_GET['search']:''));

  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
   
  $notice_box->get_contents($delete_product_info, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'product_move_box') {
/* -----------------------------------------------------
    功能: 移动商品的弹出框
    参数: $_GET['pID'] 商品id 
    参数: $_GET['site_id'] 网站id 
    参数: $_GET['page'] 当前页 
    参数: $_GET['cPath'] 分类路径 
    参数: $_GET['search'] 搜索字符串 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $site_id = isset($_GET['site_id'])?$_GET['site_id']:0; 
  $pInfo = tep_get_pinfo_by_pid($_GET['pID'],$site_id);
  $cPath = $_GET['cPath'];
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_INFO_HEADING_MOVE_PRODUCT);
  $heading[] = array('align' => 'right', 'text' => $page_str);
  
  $buttons = array();
  $button[] = tep_html_element_submit(IMAGE_MOVE); 
  $button[] = '<input type="button" value="'.IMAGE_CANCEL.'" onclick="hidden_info_box()" class="element_button">';
  
  $buttons = array('align' => 'center', 'button' => $button); 
  
  $move_product_info = array();
  $move_product_info[]['text'] = array(
        array('text' => sprintf(TEXT_MOVE_PRODUCTS_INTRO, $pInfo->products_name)) 
      );
  $move_product_info[]['text'] = array(
        array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br>' . tep_output_generated_category_path($pInfo->products_id, 'product') . tep_draw_hidden_field('products_id', $pInfo->products_id)) 
      );
  $move_product_info[]['text'] = array(
        array('text' => '<br>' . sprintf(TEXT_MOVE, $pInfo->products_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree('0','','','',false),$current_category_id)) 
      );
  
  $form_str = tep_draw_form('products', FILENAME_CATEGORIES, 'action=move_product_confirm&cPath=' . $cPath);

  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
   
  $notice_box->get_contents($move_product_info, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'product_copy_to_box') {
/* -----------------------------------------------------
    功能: 拷贝商品的弹出框
    参数: $_GET['pID'] 商品id 
    参数: $_GET['site_id'] 网站id 
    参数: $_GET['page'] 当前页 
    参数: $_GET['cPath'] 分类路径 
    参数: $_GET['search'] 搜索字符串 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $site_id = isset($_GET['site_id'])?$_GET['site_id']:0; 
  $pInfo = tep_get_pinfo_by_pid($_GET['pID'],$site_id);
  $cPath = $_GET['cPath'];
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_INFO_HEADING_COPY_TO);
  $heading[] = array('align' => 'right', 'text' => $page_str);
  
  $buttons = array();
  $button[] = tep_html_element_submit(IMAGE_COPY); 
  $button[] = '<input type="button" value="'.IMAGE_CANCEL.'" onclick="hidden_info_box()" class="element_button">';
  
  $buttons = array('align' => 'center', 'button' => $button); 
  
  $copy_product_info = array();
  $copy_product_info[]['text'] = array(
        array('text' => TEXT_INFO_COPY_TO_INTRO.tep_draw_hidden_field('products_id', $pInfo->products_id)) 
      );
  $copy_product_info[]['text'] = array(
        array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br>' . tep_output_generated_category_path($pInfo->products_id, 'product')) 
      );
  $copy_product_info[]['text'] = array(
        array('text' => '<br>' . TEXT_CATEGORIES . '<br>' . tep_draw_pull_down_menu('categories_id', tep_get_category_tree('0','','','',false), $current_category_id)) 
      );
  $copy_product_info[]['text'] = array(
        array('text' => '<br>' . TEXT_HOW_TO_COPY . '<br>' . tep_draw_radio_field('copy_as', 'link', true) . ' ' . TEXT_COPY_AS_LINK . '<br>' . tep_draw_radio_field('copy_as', 'duplicate') . ' ' . TEXT_COPY_AS_DUPLICATE) 
      );

  $form_str = tep_draw_form('copy_to', FILENAME_CATEGORIES, 'action=copy_to_confirm&cPath=' . $cPath);
  
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
   
  $notice_box->get_contents($copy_product_info, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'show_update_pic_info') {
/* -----------------------------------------------------
    功能: 图片更新的弹出框
    参数: $_POST['pic_id'] 图片id 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_MARKS);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
  $pic_info_raw = tep_db_query("select * from ".TABLE_CUSTOMERS_PIC_LIST." where id = '".$_POST['pic_id']."'");  
  $pic_info_res = tep_db_fetch_array($pic_info_raw); 
  
  $page_str = ''; 
  $pid_array = array();

  $pic_list_query = tep_db_query("select * from ".TABLE_CUSTOMERS_PIC_LIST." order by sort_order asc");
  while ($pic_list_tmp_info = tep_db_fetch_array($pic_list_query)) {
    $pid_array[] = $pic_list_tmp_info['id']; 
  }
  
  foreach ($pid_array as $p_key => $p_value) {
    if ($_POST['pic_id'] == $p_value) {
      break; 
    }
  }
  
  $page_str = '';
  
  if ($p_key > 0) {
    $page_str .= '<a onclick="show_popup_info(\'\', \''.$pid_array[$p_key-1].'\')" href="javascript:void(0);"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
 
  if ($p_key < (count($pid_array) - 1)) {
    $page_str .= '<a onclick="show_popup_info(\'\', \''.$pid_array[$p_key+1].'\')" href="javascript:void(0);">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $pic_info_res['pic_alt']);
  $heading[] = array('align' => 'right', 'text' => $page_str);
  
  $buttons = array();
  $button[] = tep_html_element_submit(IMAGE_SAVE); 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="hidden_info_box();"').'</a>';
  
  $buttons = array('align' => 'center', 'button' => $button); 
  
  $pic_info_row = array();

  $pic_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'colspan="2"', 'text' => MARKS_UPDATE_NOTICE_TEXT.tep_draw_hidden_field('pic_id', $_POST['pic_id'])), 
      );
  
  $pic_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="220"', 'text' => TABLE_HEADING_MARKS_PIC_LIST_TITLE.':'),
        array('text' => tep_draw_input_field('pic_alt', $pic_info_res['pic_alt']))
      );
  
  $pic_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="220"', 'text' => TABLE_HEADING_MARKS_PIC_LIST_SORT.':'),
        array('text' => tep_draw_input_field('sort_order', $pic_info_res['sort_order']))
      );
  
  $pic_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="220"', 'text' => TEXT_USER_ADDED),
        array('text' => ((tep_not_null($pic_info_res['user_added'])?$pic_info_res['user_added']:TEXT_UNSET_DATA)))
      );
  
  $pic_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="220"', 'text' => TEXT_DATE_ADDED),
        array('text' => ((tep_not_null(tep_datetime_short($pic_info_res['date_added'])))?tep_datetime_short($pic_info_res['date_added']):TEXT_UNSET_DATA))
      );
  
  $pic_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="220"', 'text' => TEXT_USER_UPDATE),
        array('text' => ((tep_not_null($pic_info_res['user_update'])?$pic_info_res['user_update']:TEXT_UNSET_DATA)))
      );
  
  $pic_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="220"', 'text' => TEXT_DATE_UPDATE),
        array('text' => ((tep_not_null(tep_datetime_short($pic_info_res['date_update'])))?tep_datetime_short($pic_info_res['date_update']):TEXT_UNSET_DATA))
      );
  
  $form_str = tep_draw_form('pic', FILENAME_MARKS, 'action=update_pic');
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($pic_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'new_group') {
/* -----------------------------------------------------
    功能: 新建option组
    参数: 无 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_OPTION);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 
  $page_str = '<a onclick="close_option_info();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => HEADING_TITLE); 
  $heading[] = array('align' => 'right', 'text' => $page_str); 
  
  $buttons = array();
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_group_info(0, 0);" id="button_save"').'</a>'; 
  $buttons = array('align' => 'center', 'button' => $button); 
 
  $new_group_row = array();
  
  $new_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_NAME), 
        array('align' => 'left', 'text' => tep_draw_input_field('name', '', 'id="name" class="campaign_input"').'<span id="name_error" style="color:#ff0000;"></span>') 
      );
  
  $new_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_TITLE), 
        array('align' => 'left', 'text' => tep_draw_input_field('title', '', 'id="title" class="campaign_input"').'<span id="title_error" style="color:#ff0000;"></span>') 
      );
  
  $new_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_GROUP_IS_PREORDER), 
        array('align' => 'left', 'text' => tep_draw_radio_field('is_preorder',1,false,'','id="is_preorder" style="padding-left:0;margin-left:0;"').OPTION_GROUP_IS_PREORDER.'&nbsp;'.tep_draw_radio_field('is_preorder',0,true,'','id="is_preorder"').OPTION_GROUP_IS_NOT_PREORDER) 
      );
  
  $new_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_GROUP_DESC), 
        array('align' => 'left', 'text' => tep_draw_textarea_field('comment', 'hard', '30', '10', '', 'class="campaign_input" onfocus="o_submit_single = false;" onblur="o_submit_single = true;"')) 
      );
  
  $new_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_SORT_NUM), 
        array('align' => 'left', 'text' => tep_draw_input_field('sort_num', '1000', 'size="31" id="sort_num" style="text-align:right; width:20%;"')) 
      );
  
  $form_str = tep_draw_form('option_group', FILENAME_OPTION, 'action=insert_group'); 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($new_group_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  
  echo $notice_box->show_notice().'||||||'.tep_get_note_top_layer(FILENAME_OPTION);
} else if ($_GET['action'] == 'edit_group') {
/* -----------------------------------------------------
    功能: 编辑option组
    参数: $_POST['group_id'] 组id 
    参数: $_POST['search'] 搜索的类型 
    参数: $_POST['sort_name'] 排序的名字 
    参数: $_POST['sort_type'] 排序的类型 
    参数: $_POST['keyword'] 关键字 
    参数: $_POST['page'] 当前页 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_OPTION);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 
  $group_raw = tep_db_query("select * from ".TABLE_OPTION_GROUP." where id = '".$_POST['group_id']."'"); 
  $group = tep_db_fetch_array($group_raw);
  
  foreach ($_POST as $p_key => $p_value) {
    if (($p_key != 'group_id') && ($p_key != 'action')) {
      $param_str .= $p_key.'='.$p_value.'&'; 
    }
  }
  $param_str = substr($param_str, 0, -1); 
  
  $page_str = '';
  
  if (isset($_POST['search'])) {
    if (isset($_POST['sort_name'])) {
      $sort_type = isset($_POST['sort_type'])?$_POST['sort_type']:'asc'; 
      if ($_POST['search'] == '2') {
        $group_query_raw = 'select og.*, if((select p.products_id from products p where p.belong_to_option = og.id limit 1), 1, 0) as is_belong_to from '.TABLE_OPTION_GROUP.' og where og.name = \''.tep_replace_full_character($_POST['keyword']).'\' order by is_belong_to '.$sort_type.', og.sort_num, og.name asc';
      } else {
        $group_query_raw = 'select og.*, if((select p.products_id from products p where p.belong_to_option = og.id limit 1), 1, 0) as is_belong_to from '.TABLE_OPTION_GROUP.' og where og.name like \'%'.tep_replace_full_character($_POST['keyword']).'%\' order by is_belong_to '.$sort_type.', og.sort_num, og.name asc';
      }
    } else {
      if ($_POST['search'] == '2') {
        $group_query_raw = 'select * from '.TABLE_OPTION_GROUP.' where name = \''.tep_replace_full_character($_POST['keyword']).'\' order by sort_num, name asc';   
      } else {
        $group_query_raw = 'select * from '.TABLE_OPTION_GROUP.' where name like \'%'.tep_replace_full_character($_POST['keyword']).'%\' order by sort_num, name asc';   
      }
    }
  } else {
    if (isset($_POST['sort_name'])) {
      $sort_type = isset($_POST['sort_type'])?$_POST['sort_type']:'asc'; 
      $group_query_raw = 'select og.*, if((select p.products_id from products p where p.belong_to_option = og.id limit 1), 1, 0) as is_belong_to from '.TABLE_OPTION_GROUP.' og order by is_belong_to '.$sort_type.', og.sort_num asc ,og.name asc';
    } else {
      $group_query_raw = 'select * from '.TABLE_OPTION_GROUP.' order by sort_num, name asc'; 
    }
  }
  $group_split = new splitPageResults($_POST['page'], MAX_DISPLAY_SEARCH_RESULTS, $group_query_raw, $group_query_numrows); 
  $group_query = tep_db_query($group_query_raw); 
  $gid_array = array(); 
  while ($group_row = tep_db_fetch_array($group_query)) {
    $gid_array[] = $group_row['id']; 
  }
  foreach ($gid_array as $g_key => $g_value) {
     if ($_POST['group_id'] == $g_value) {
       break; 
     }
  }
  if ($g_key > 0) {
    $page_str .= '<a id="option_prev" href="javascript:void(0);" onclick="show_link_group_info(\''.$gid_array[$g_key - 1].'\', \''.urlencode($param_str).'\')"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
  
  if ($g_key < count($gid_array)-1) {
    $page_str .= '<a id="option_next" href="javascript:void(0);" onclick="show_link_group_info(\''.$gid_array[$g_key + 1].'\', \''.urlencode($param_str).'\')">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }
  
  $page_str .= '<a onclick="close_option_info();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $group['name']); 
  $heading[] = array('align' => 'right', 'text' => $page_str); 
  
  $buttons = array();
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_NEW_PROJECT, 'onclick="create_option_group();"').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_group_info('.$group['id'].', 1);" id="button_save"').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="if(confirm(\''.TEXT_DEL_OPTION.'\')) window.location.href = \''.tep_href_link(FILENAME_OPTION, 'action=delete_group_confirm&group_id='.$group['id'].'&'.$param_str).'\';"').'</a>'; 
  
  $buttons = array('align' => 'center', 'button' => $button); 
 
  $edit_group_row = array();
  

  
  $edit_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_NAME), 
        array('align' => 'left', 'text' => tep_draw_input_field('name', $group['name'], 'id="name" class="campaign_input"').'<span id="name_error" style="color:#ff0000;"></span>') 
      );
  
  $edit_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_TITLE), 
        array('align' => 'left', 'text' => tep_draw_input_field('title', $group['title'], 'id="title" class="campaign_input"').'<span id="title_error" style="color:#ff0000;"></span>') 
      );
  
  if ($group['is_preorder'] == '1') {
    $is_preorder_str .= tep_draw_radio_field('is_preorder',1,true,'','id="is_preorder" style="padding-left:0;margin-left:0;"').OPTION_GROUP_IS_PREORDER.'&nbsp;'.tep_draw_radio_field('is_preorder',0,false,'','id="is_preorder"').OPTION_GROUP_IS_NOT_PREORDER; 
  } else {
    $is_preorder_str .= tep_draw_radio_field('is_preorder',1,false,'','id="is_preorder" style="padding-left:0;margin-left:0;"').OPTION_GROUP_IS_PREORDER.'&nbsp;'.tep_draw_radio_field('is_preorder',0,true,'','id="is_preorder"').OPTION_GROUP_IS_NOT_PREORDER; 
  }
  
  $edit_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_GROUP_IS_PREORDER), 
        array('align' => 'left', 'text' => $is_preorder_str) 
      );
  
  $edit_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_GROUP_DESC), 
        array('align' => 'left', 'text' => tep_draw_textarea_field('comment', 'hard', '30', '10', $group['comment'], 'class="campaign_input" onfocus="o_submit_single = false;" onblur="o_submit_single = true;"')) 
      );
  
  $edit_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_SORT_NUM), 
        array('align' => 'left', 'text' => tep_draw_input_field('sort_num', $group['sort_num'], 'size="31" id="sort_num" style="text-align:right;
width:20%;"')) 
      );
  
  $edit_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TEXT_USER_ADDED), 
        array('align' => 'left', 'text' => ((tep_not_null($group['user_added']))?$group['user_added']:TEXT_UNSET_DATA)) 
      );
  
  $edit_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TEXT_DATE_ADDED), 
        array('align' => 'left', 'text' => ((tep_not_null($group['created_at']))?$group['created_at']:TEXT_UNSET_DATA)) 
      );
  
  $edit_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TEXT_USER_UPDATE), 
        array('align' => 'left', 'text' => ((tep_not_null($group['user_update']))?$group['user_update']:TEXT_UNSET_DATA)) 
      );
  
  $edit_group_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TEXT_USER_ADDED), 
        array('align' => 'left', 'text' => ((tep_not_null($group['date_update']))?$group['date_update']:TEXT_UNSET_DATA).tep_draw_hidden_field('group_id', $group['id'])) 
      );

  $form_str = tep_draw_form('option_group', FILENAME_OPTION, 'action=update_group&'.$param_str); 
  
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($edit_group_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice().'||||||'.tep_get_note_top_layer(FILENAME_OPTION);
} else if ($_GET['action'] == 'new_item') {
/* -----------------------------------------------------
    功能: 新建option元素
    参数: $_POST['group_id'] 组id 
    参数: $_POST['gpage'] 组的当前页 
    参数: $_POST['keyword'] 关键字 
    参数: $_POST['search'] 搜索的类型 
    参数: $_POST['sort_name'] 排序的名字 
    参数: $_POST['sort_type'] 排序的类型 
    参数: $_POST['page'] 当前页 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_OPTION);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  require_once(DIR_FS_ADMIN.'enabledoptionitem.php'); 
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 
  $page_str = '<a onclick="close_option_info();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => 'Item'); 
  $heading[] = array('align' => 'right', 'text' => $page_str); 
  
  $buttons = array();
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_item_info()" id="button_save"').'</a>'; 
  $buttons = array('align' => 'center', 'button' => $button); 
 
  $new_item_row = array();
  
  $new_item_title_row = array();
  $new_item_title_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0');
  
  $new_item_title_row[]['text'] = array(
         array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_NAME),
         array('align' => 'left', 'text' => tep_draw_input_field('title', '', 'id="title" class="option_text" autocomplete="off"').'&nbsp;<a href="javascript:void(0);" onclick="search_item_title(this, 0, 0);">'.tep_html_element_button(IMAGE_SEARCH, 'onclick=""').'</a>'.'<span id="title_error" style="color:#ff0000;"></span>')
      );
  
  $new_item_title_row[]['text'] = array(
         array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_TITLE),
         array('align' => 'left', 'text' => tep_draw_input_field('front_title', '', 'id="front_title" class="option_text"').'<span id="front_error" style="color:#ff0000;"></span>')
      );
  
  $select_str = '<div id="se_item">'; 
  $select_str .= '<select id="type" name="type" onchange="change_option_item_type(0);" style="margin-left:0;padding-left:0;">'; 
  $i=0; 
  foreach ($enabled_item_array as $ekey => $evalue) {
    if ($i == 0) {
      $first_item = $ekey; 
    }
    $select_str .= '<option value="'.$ekey.'">'.strtolower($evalue).'</option>'; 
    $i++; 
  }
  $select_str .= '</select>'; 
  $select_str .= '</div>'; 

  $new_item_title_row[]['text'] = array(
         array('align' => 'left', 'text' => TABLE_HEADING_OPTION_ITEM_TYPE),
         array('align' => 'left', 'text' => $select_str)
      );
  $new_item_table_title_str = $notice_box->get_table($new_item_title_row, '', $new_item_title_params);
  
  $new_item_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="100%"', 'text' => $new_item_table_title_str) 
      );
  
  $new_item_show_select_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0', 'parameters' => 'id="show_select" class="option_spacing"');
  
  $classname = 'HM_Option_Item_'.ucfirst($first_item);
  require_once('option/'.$classname.'.php');
  $item_instance = new $classname();
  
  $new_item_show_select_row = $item_instance->prepareFormWithParent($item['id']);
  
  $new_item_table_show_select_str = $notice_box->get_table($new_item_show_select_row, '', $new_item_show_select_params, true);  

  $new_item_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="100%"', 'text' => $new_item_table_show_select_str) 
      );
  
  $new_item_other_row = array();
  $new_item_other_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0');
    
  $new_item_other_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"' , 'text' => TABLE_HEADING_OPTION_ITEM_PRICE),
        array('align' => 'left', 'text' => tep_draw_input_field('price', '', 'id="price" class="option_item_input"').'&nbsp;'.TEXT_MONEY_SYMBOL), 
      );
  
  $new_item_other_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"' , 'text' => TABLE_HEADING_OPTION_SORT_NUM),
        array('align' => 'left', 'text' => tep_draw_input_field('sort_num', '1000', 'id="sort_num" class="option_item_input"')), 
      );
  
  $new_item_other_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"' , 'text' => TABLE_HEADING_OPTION_ITEM_PLACE),
        array('align' => 'left', 'text' => '<div id="p_type">'.tep_draw_radio_field('place_type', '0', true, '', 'style="padding-left:0;margin-left:0;"').OPTION_ITEM_TYPE_PRODUCT.'&nbsp;&nbsp;'.tep_draw_radio_field('place_type', '1').OPTION_ITEM_TYPE_LAST.'</div><input type="hidden" name="is_copy" value="0" id="is_copy">'), 
      );
  
  $new_item_table_other_str = $notice_box->get_table($new_item_other_row, '', $new_item_other_params);  

  $new_item_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="100%"', 'text' => $new_item_table_other_str) 
      );
  
  
  $form_str = tep_draw_form('option_item', FILENAME_OPTION, 'action=insert_item&g_id='.$_POST['group_id'].(!empty($_POST['gpage'])?'&gpage='.$_POST['gpage']:'').(isset($_POST['keyword'])?'&keyword='.$_POST['keyword']:'').(isset($_POST['search'])?'&search='.$_POST['search']:'').(!empty($_POST['page'])?'&page='.$_POST['page']:'').(isset($_POST['sort_name'])?'&sort_name='.$_POST['sort_name']:'').(isset($_POST['sort_type'])?'&sort_type='.$_POST['sort_type']:''), 'post', 'enctype="multipart/form-data"'); 
  
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($new_item_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
 
  $item_script_str = '<script>$(function() {
      function format_item(item) {
          return item.name;
      }
      $("#title").autocomplete(\'ajax_orders.php?action=search_title\', {
        multipleSeparator: \'\',
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
          return format_item(item);
        }
      }).result(function(e, item) {
      });
});</script>'; 
  echo $item_script_str.$notice_box->show_notice().'||||||'.tep_get_note_top_layer(FILENAME_OPTION.'?g_id='.$_POST['group_id']);
} else if ($_GET['action'] == 'edit_item') {
/* -----------------------------------------------------
    功能: 编辑item
    参数: $_POST['item_id'] 元素id 
    参数: $_POST['g_id'] 组id 
    参数: $_POST['page'] 当前页 
    参数: $_POST['gpage'] 组的当前页 
    参数: $_POST['keyword'] 关键字 
    参数: $_POST['search'] 搜索的类型 
    参数: $_POST['sort_name'] 排序的名字 
    参数: $_POST['sort_type'] 排序的类型 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_OPTION);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  require_once(DIR_FS_ADMIN.'enabledoptionitem.php'); 
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
  $param_str = '';
  foreach ($_POST as $p_key => $p_value) {
    if (($p_key != 'item_id') && ($p_key != 'action')) {
      $param_str .= $p_key.'='.$p_value.'&'; 
    }
  }
  $param_str = substr($param_str, 0, -1);
  
  $page_str = '';
  $item_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$_POST['item_id']."'"); 
  $item = tep_db_fetch_array($item_raw); 
 
  $item_query_raw = 'select * from '.TABLE_OPTION_ITEM.' where group_id = \''.$_POST['g_id'].'\' order by sort_num, title asc';
  
  $item_split = new splitPageResults($_POST['page'], MAX_DISPLAY_SEARCH_RESULTS, $item_query_raw, $item_query_numrows);    
  $item_query = tep_db_query($item_query_raw);  
  
  $item_id_array = array(); 
  while ($item_row = tep_db_fetch_array($item_query)) {
    $item_id_array[] = $item_row['id']; 
  }
  
  foreach ($item_id_array as $i_key => $i_value) {
     if ($_POST['item_id'] == $i_value) {
       break; 
     }
  }
  if ($i_key > 0) {
    $page_str .= '<a id="option_prev" href="javascript:void(0);" onclick="show_link_item_info(\''.$item_id_array[$i_key - 1].'\', \''.urlencode($param_str).'\')"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
  
  if ($i_key < count($item_id_array)-1) {
    $page_str .= '<a id="option_next" href="javascript:void(0);" onclick="show_link_item_info(\''.$item_id_array[$i_key + 1].'\', \''.urlencode($param_str).'\')">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }
  

  $page_str .= '<a onclick="close_option_info();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $item['title']); 
  $heading[] = array('align' => 'right', 'text' => $page_str); 
  
  $buttons = array();
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_NEW_PROJECT, 'onclick="create_option_item(\''.$_POST['g_id'].'\', \''.urlencode($param_str).'\');"').'</a>&nbsp;'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_item_info();" id="button_save"').'</a>&nbsp;'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="if(confirm(\''.TEXT_DEL_OPTION.'\')) window.location.href = \''.tep_href_link(FILENAME_OPTION, 'action=delete_item_confirm&item_id='.$item['id'].'&g_id='.$_POST['g_id'].(!empty($_POST['gpage'])?'&gpage='.$_POST['gpage']:'').(isset($_POST['keyword'])?'&keyword='.$_POST['keyword']:'').(isset($_POST['search'])?'&search='.$_POST['search']:'').(!empty($_POST['page'])?'&page='.$_POST['page']:'').(isset($_POST['sort_name'])?'&sort_name='.$_POST['sort_name']:'').(isset($_POST['sort_type'])?'&sort_type='.$_POST['sort_type']:'')).'\';"').'</a>'; 
  
  $buttons = array('align' => 'center', 'button' => $button); 
 

  $edit_item_row = array();
  
  $edit_item_title_row = array();
  $edit_item_title_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0');
  
  $edit_item_title_row[]['text'] = array(
         array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_NAME.'<input type="hidden" name="item_id" value="'.$item['id'].'" id="item_uid">'),
         array('align' => 'left', 'text' => tep_draw_input_field('title', $item['title'], 'id="title" class="option_text" autocomplete="off"').'&nbsp;<a href="javascript:void(0);" onclick="search_item_title(this, 1, '.$item['id'].');">'.tep_html_element_button(IMAGE_SEARCH, 'onclick=""').'</a><span id="title_error" style="color:#ff0000;"></span><input type="hidden" name="is_more" id="is_more" value="0">')
      );
  
  $edit_item_title_row[]['text'] = array(
         array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_TITLE),
         array('align' => 'left', 'text' => tep_draw_input_field('front_title', $item['front_title'], 'id="front_title" class="option_text"').'<span id="front_error" style="color:#ff0000;"></span>')
      );
  
  $item_select_str = '<select id="type" name="type" onchange="change_option_item_type(0);" style="margin-left:0;padding-left:0;">'; 
  foreach ($enabled_item_array as $ekey => $evalue) {
    if (strtolower($evalue) == $item['type']) {
      $item_select_str .= '<option value="'.$ekey.'" selected>'.strtolower($evalue).'</option>'; 
    } else {
      $item_select_str .= '<option value="'.$ekey.'">'.strtolower($evalue).'</option>'; 
    }
  }
  $html_str .= '</select>';
  $edit_item_title_row[]['text'] = array(
         array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_ITEM_TYPE),
         array('align' => 'left', 'text' => '<div id="se_item">'.$item_select_str.'</div>')
      );
  $edit_item_table_title_str = $notice_box->get_table($edit_item_title_row, '', $edit_item_title_params);
  
  $edit_item_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="100%"', 'text' => $edit_item_table_title_str) 
      );
  
  $edit_item_show_select_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0', 'parameters' => 'id="show_select" class="option_spacing"');
  $classname = 'HM_Option_Item_'.ucfirst($item['type']);
  require_once('option/'.$classname.'.php');
  $item_instance = new $classname();
  $edit_item_show_select_row = $item_instance->prepareFormWithParent($item['id']);
  
  $edit_item_table_title_str = $notice_box->get_table($edit_item_show_select_row, '', $edit_item_show_select_params, true);

  $edit_item_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="100%"', 'text' => $edit_item_table_title_str) 
      );
  
  $edit_item_other_row = array();
  $edit_item_other_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0');
   
  if ($item['type'] != 'radio') {
    $edit_item_other_row[]['text'] = array(
           array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_ITEM_PRICE),
           array('align' => 'left', 'text' => tep_draw_input_field('price', number_format($item['price']), 'id="price" class="option_item_input"').'&nbsp;'.TEXT_MONEY_SYMBOL)
        );
  }
  
  $edit_item_other_row[]['text'] = array(
         array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_SORT_NUM),
         array('align' => 'left', 'text' => tep_draw_input_field('sort_num', $item['sort_num'], 'id="sort_num" class="option_item_input"'))
      );
  
  if ($item['place_type'] == 0) {
    $item_place_str = tep_draw_radio_field('place_type', '0', true, '', 'style="padding-left:0;margin-left:0;"').OPTION_ITEM_TYPE_PRODUCT.'&nbsp;&nbsp;'.tep_draw_radio_field('place_type', '1').OPTION_ITEM_TYPE_LAST; 
  } else {
    $item_place_str = tep_draw_radio_field('place_type', '0', false, '', 'style="padding-left:0;margin-left:0;"').OPTION_ITEM_TYPE_PRODUCT.'&nbsp;&nbsp;'.tep_draw_radio_field('place_type', '1', true).OPTION_ITEM_TYPE_LAST; 
  }
  $edit_item_other_row[]['text'] = array(
         array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_HEADING_OPTION_ITEM_PLACE),
         array('align' => 'left', 'text' => '<div id="p_type">'.$item_place_str.'</div>')
      );
  
  $edit_item_other_row[]['text'] = array(
         array('align' => 'left', 'text' => TEXT_USER_ADDED),
         array('align' => 'left', 'text' => ((tep_not_null($item['user_added']))?$item['user_added']:TEXT_UNSET_DATA))
      );
  
  $edit_item_other_row[]['text'] = array(
         array('align' => 'left', 'text' => TEXT_DATE_ADDED),
         array('align' => 'left', 'text' => ((tep_not_null($item['created_at']))?$item['created_at']:TEXT_UNSET_DATA))
      );
  
  $edit_item_other_row[]['text'] = array(
         array('align' => 'left', 'text' => TEXT_USER_UPDATE),
         array('align' => 'left', 'text' => ((tep_not_null($item['user_update']))?$item['user_update']:TEXT_UNSET_DATA))
      );
  
  $edit_item_other_row[]['text'] = array(
         array('align' => 'left', 'text' => TEXT_DATE_UPDATE),
         array('align' => 'left', 'text' => ((tep_not_null($item['date_update']))?$item['date_update']:TEXT_UNSET_DATA).'<input type="hidden" name="is_copy" value="0" id="is_copy">')
      );

  $edit_item_other_str = $notice_box->get_table($edit_item_other_row, '', $edit_item_other_params);
  
  $edit_item_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="100%"', 'text' => $edit_item_other_str) 
      );
  
  $item_script_str = '<script>$(function() {
      function format_item(item) {
          return item.name;
      }
      $("#title").autocomplete(\'ajax_orders.php?action=search_title\', {
        multipleSeparator: \'\',
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
          return format_item(item);
        }
      }).result(function(e, item) {
      });
});</script>';

  $form_str = tep_draw_form('option_item', FILENAME_OPTION, 'action=update_item&g_id='.$_POST['g_id'].(!empty($_POST['gpage'])?'&gpage='.$_POST['gpage']:'').(isset($_POST['keyword'])?'&keyword='.$_POST['keyword']:'').(isset($_POST['search'])?'&search='.$_POST['search']:'').(!empty($_POST['page'])?'&page='.$_POST['page']:'').(isset($_POST['sort_name'])?'&sort_name='.$_POST['sort_name']:'').(isset($_POST['sort_type'])?'&sort_type='.$_POST['sort_type']:''), 'post', 'enctype="multipart/form-data"'); 
  
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($edit_item_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  
  echo $item_script_str.$notice_box->show_notice().'||||||'.tep_get_note_top_layer(FILENAME_OPTION.'?g_id='.$_POST['g_id']);
}else if ($_GET['action'] == 'status_setting') {
/* -----------------------------------------------------
    功能: 获取日历状态的信息
    参数: $_GET['date'] 日期 
 -----------------------------------------------------*/
  $cl_status_array = array();
  $calendar_status_query = tep_db_query("select id,color,is_show from ". TABLE_CALENDAR_STATUS);
  while($calendar_status_array = tep_db_fetch_array($calendar_status_query)){

    $cl_status_array[$calendar_status_array['id']] = array('is_show'=>$calendar_status_array['is_show'],'color'=>$calendar_status_array['color']);
  }
  tep_db_free_result($calendar_status_query);
 
  $repeat_array = array(); 
  $repeat_date_query = tep_db_query("select id,cl_date,type,repeat_type,is_show,date_update from ". TABLE_CALENDAR_DATE ." where repeat_type!=0 order by date_update asc");    
  while($repeat_date_array = tep_db_fetch_array($repeat_date_query)){

    $repeat_sort_query = tep_db_query("select sort from ". TABLE_CALENDAR_STATUS ." where id='".$repeat_date_array['type']."'");
    $repeat_sort_array = tep_db_fetch_array($repeat_sort_query);
    tep_db_free_result($repeat_sort_query);
    $repeat_array[$repeat_date_array['id']] = array('cl_date'=>$repeat_date_array['cl_date'],'repeat'=>$repeat_date_array['repeat_type'],'type'=>$repeat_date_array['type'],'is_show'=>$repeat_date_array['is_show'],'date_update'=>$repeat_date_array['date_update'],'sort'=>($repeat_date_array['type'] == 0 ? -1 :$repeat_sort_array['sort']));
  }
  tep_db_free_result($repeat_date_query);
  //分类处理特殊重复设置

  foreach($repeat_array as $cl_key=>$cl_value){

    if($cl_value['repeat'] == 1){

       $cl_repeat_array[1][$cl_key] = tep_get_repeat_date(1,$cl_value['cl_date']);
    }

    if($cl_value['repeat'] == 2){

       $cl_repeat_array[2][$cl_key] = tep_get_repeat_date(2,$cl_value['cl_date']);
    }

    if($cl_value['repeat'] == 3){

       $cl_repeat_array[3][$cl_key] = tep_get_repeat_date(3,$cl_value['cl_date']);
    }

    if($cl_value['repeat'] == 4){

       $cl_repeat_array[4][$cl_key] = tep_get_repeat_date(4,$cl_value['cl_date']);
    }
  }

  //重复周
  $cl_week_array = array();
  foreach($cl_repeat_array[1] as $cl_week_key=>$cl_week_value){

      $cl_week_array[$cl_week_value] = $cl_week_key;
  }

  //每月重复的日
  $cl_month_day_array = array();
  foreach($cl_repeat_array[2] as $cl_month_key=>$cl_month_value){

      $cl_month_day_array[$cl_month_value] = $cl_month_key;
  }

  //每月重复固定周
  $cl_month_week_array = array();
  foreach($cl_repeat_array[3] as $cl_month_week_key=>$cl_month_week_value){

      $cl_month_week_array[$cl_month_week_value[0]][$cl_month_week_value[1]] = $cl_month_week_key;
  }

  //每年重复的月日
  $cl_year_month_array = array();
  foreach($cl_repeat_array[4] as $cl_year_month_key=>$cl_year_month_value){

      $cl_year_month_array[$cl_year_month_value] = $cl_year_month_key; 
  }

  //读取相应日期的数据
  $calendar_date_query = tep_db_query("select * from ". TABLE_CALENDAR_DATE ." where cl_date='".$_GET['date']."'");
  $calendar_date_array = tep_db_fetch_array($calendar_date_query);
  $calendar_date_num = tep_db_num_rows($calendar_date_query);
  tep_db_free_result($calendar_date_query); 
  $repeat_sort_query = tep_db_query("select sort from ". TABLE_CALENDAR_STATUS ." where id='".$calendar_date_array['type']."'");
  $repeat_sort_array = tep_db_fetch_array($repeat_sort_query);
  tep_db_free_result($repeat_sort_query);

  $calendar_date_id = $calendar_date_array['id'];
  $day = tep_get_repeat_date(2,$_GET['date']);
  
  $wday = tep_get_repeat_date(1,$_GET['date']);
  
  $temp_num_week = ceil($day/7);
  
  $temp_year_month_day = substr($_GET['date'],4,4); 

  //状态重复设置，冲突时，以状态排序最小的一个为准 
  $date_time_array = array();
  $date_time_array = array('month'=>$repeat_array[$cl_month_day_array[$day]]['sort'],
                           'week'=>$repeat_array[$cl_week_array[$wday]]['sort'],                     
                           'month_week'=>$repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort'],
                           'year'=>$repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort']
                           );
  arsort($date_time_array);
  $date_time_array = array_filter($date_time_array);
  $first_value_array = array_slice($date_time_array,0,1);
  $first_value_type = array_keys($first_value_array);
  if($first_value_array[$first_value_type[0]] != ''){
    switch($first_value_type[0]){

      case 'month':
        $cl_date_temp = isset($calendar_date_array['type']) && $calendar_date_array['type'] == 0 && $repeat_array[$cl_month_day_array[$day]]['sort'] == -1 ? true : false;
        if(($repeat_sort_array['sort'] != '' && $repeat_array[$cl_month_day_array[$day]]['sort'] != '' && $repeat_sort_array['sort'] < $repeat_array[$cl_month_day_array[$day]]['sort']) || ($repeat_sort_array['sort'] == '' && $repeat_array[$cl_month_day_array[$day]]['sort'] != '') || ($repeat_array[$cl_month_day_array[$day]]['sort'] == -1)){
          $calendar_date_id  = array_key_exists($day,$cl_month_day_array) && $cl_date_temp == false ?  $cl_month_day_array[$day] : $calendar_date_id;
        } 
        break;
      case 'week':
        $cl_date_temp = isset($calendar_date_array['type']) && $calendar_date_array['type'] == 0 && $repeat_array[$cl_week_array[$wday]]['sort'] == -1 ? true : false;
        if(($repeat_sort_array['sort'] != '' && $repeat_array[$cl_week_array[$wday]]['sort'] != '' && $repeat_sort_array['sort'] < $repeat_array[$cl_week_array[$wday]]['sort']) || ($repeat_sort_array['sort'] == '' && $repeat_array[$cl_week_array[$wday]]['sort'] != '') || ($repeat_array[$cl_week_array[$wday]]['sort'] == -1)){
          $calendar_date_id = array_key_exists($wday,$cl_week_array) && $cl_date_temp == false ? $cl_week_array[$wday] : $calendar_date_id; 
        }          
        break;
      case 'month_week':
        $cl_date_temp = isset($calendar_date_array['type']) && $calendar_date_array['type'] == 0 && $repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort'] == -1 ? true : false;
        if(($repeat_sort_array['sort'] != '' && $repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort'] != '' && $repeat_sort_array['sort'] < $repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort']) || ($repeat_sort_array['sort'] == '' && $repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort'] != '') || ($repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort'] == -1)){ 
          $temp_week_array = array_slice($cl_month_week_array,0,1);
          $calendar_date_id = is_array($cl_month_week_array[$temp_num_week]) && $cl_date_temp == false ? $cl_month_week_array[$temp_num_week][$wday] : $calendar_date_id;
        }          
        break;
      case 'year':
        $cl_date_temp = isset($calendar_date_array['type']) && $calendar_date_array['type'] == 0 && $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] == -1 ? true : false;
        if(($repeat_sort_array['sort'] != '' && $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] != '' && $repeat_sort_array['sort'] < $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort']) || ($repeat_sort_array['sort'] == '' && $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] != '') || ($repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] == -1)){ 
          $calendar_date_id = array_key_exists($temp_year_month_day,$cl_year_month_array) && $cl_date_temp == false ? $cl_year_month_array[$temp_year_month_day] : $calendar_date_id;
        }          
        break;
    }
  }

  //读取相应日期的数据
  $calendar_date_query = tep_db_query("select * from ". TABLE_CALENDAR_DATE ." where id='".$calendar_date_id."'");
  $calendar_date_array = tep_db_fetch_array($calendar_date_query);
  tep_db_free_result($calendar_date_query);

  //显示日期编辑的弹出框 
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_BANK_CL);
  include(DIR_FS_ADMIN.'classes/notice_box.php'); 

  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 
  //头部内容
  $heading = array();
  $date_str = substr($_GET['date'],0,4).'-'.substr($_GET['date'],4,2).'-'.substr($_GET['date'],6,2);
  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $date_str);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  //主体内容
  $category_info_row = array();
  
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_COMMENTS.'<input type="hidden" name="cl_date" value="'.$_GET['date'].'"><input type="hidden" id="repeat_flag" value="'.($calendar_date_num > 0 && $first_value_array[$first_value_type[0]] != '' && $cl_date_temp == false ? 1 : 0).'"><input type="hidden" id="special_flag" value="'.$calendar_date_array['is_special'].'">'), 
        array('align' => 'left', 'params' => 'colspan="2"', 'text' => '&nbsp;') 
      );

  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_SPECIAL), 
        array('align' => 'left', 'params' => 'width="55" nowrap="nowrap"', 'text' => '<input type="radio" name="is_special" style="padding-left:0;margin-left:0;" value="1"'.($calendar_date_array['is_special'] == 1 ? ' checked="checked"' : '').' onclick="change_repeat_type(1);">'.TEXT_CALENDAR_YES),
        array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input type="radio" name="is_special" value="0"'.($calendar_date_array['is_special'] == 0 ? ' checked="checked"' : '').' onclick="change_repeat_type(0);">'.TEXT_CALENDAR_NO)
      );

  //银行营业状态下拉框
  $status_select_list = '<select name="type">';
  $status_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  $calendar_status_query = tep_db_query("select id,title from ". TABLE_CALENDAR_STATUS ." order by sort asc,id asc");
  $selected = '';
  while($calendar_status_array = tep_db_fetch_array($calendar_status_query)){

    $selected = $calendar_date_array['type'] == $calendar_status_array['id'] ? ' selected="selected"' : '';
    $status_select_list .= '<option value="'.$calendar_status_array['id'].'"'.$selected.'>'.$calendar_status_array['title'].'</option>';
  } 
  tep_db_free_result($calendar_status_query);
  $status_select_list .= '</select>';
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_TYPE), 
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $status_select_list.'&nbsp;<span id="status_type"></span>')
      );
  //重复类型 
  $disabled = '';
  $disabled = $calendar_date_array['is_special'] == 1 ? 'disabled="disabled"' : ''; 
  $repeat_select_list = '<select name="repeat_type"'.$disabled.'>';
  $repeat_select_list .= '<option value="0"'.($calendar_date_array['repeat_type'] == 0 ? ' selected="selected"' : '').'>'.TEXT_CALENDAR_REPEAT_TYPE_NO.'</option>';
  $repeat_select_list .= '<option value="1"'.($calendar_date_array['repeat_type'] == 1 ? ' selected="selected"' : '').'>'.TEXT_CALENDAR_REPEAT_TYPE_WEEK.'</option>';
  $repeat_select_list .= '<option value="2"'.($calendar_date_array['repeat_type'] == 2 ? ' selected="selected"' : '').'>'.TEXT_CALENDAR_REPEAT_TYPE_MONTH.'</option>';
  $repeat_select_list .= '<option value="3"'.($calendar_date_array['repeat_type'] == 3 ? ' selected="selected"' : '').'>'.TEXT_CALENDAR_REPEAT_TYPE_MONTH_WEEK.'</option>';
  $repeat_select_list .= '<option value="4"'.($calendar_date_array['repeat_type'] == 4 ? ' selected="selected"' : '').'>'.TEXT_CALENDAR_REPEAT_TYPE_YEAR.'</option>';
  $repeat_select_list .= '</select>';
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_REPEAT_TYPE), 
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $repeat_select_list)
     ); 

  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_SHOW), 
        array('align' => 'left', 'params' => 'width="55" nowrap="nowrap"', 'text' => '<input type="radio" name="is_show" style="padding-left:0;margin-left:0;" value="1"'.($calendar_date_array['is_show'] == 1 ? ' checked="checked"' : '').'>'.TEXT_CALENDAR_SHOW_YES),
        array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input type="radio" name="is_show" value="0"'.($calendar_date_array['is_show'] == 0 ? ' checked="checked"' : '').'>'.TEXT_CALENDAR_SHOW_NO)
      );
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_ADDED), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => tep_not_null($calendar_date_array['user_added']) ? $calendar_date_array['user_added'] : TEXT_UNSET_DATA)
      );

  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_DATE_ADDED), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => tep_not_null($calendar_date_array['date_added']) && tep_not_null($calendar_date_array['user_added']) ? $calendar_date_array['date_added'] : TEXT_UNSET_DATA)
      );

  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_UPDATE), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => tep_not_null($calendar_date_array['user_update']) ? $calendar_date_array['user_update'] : TEXT_UNSET_DATA)
      );

  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_DATE_UPDATE), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => tep_not_null($calendar_date_array['date_update']) && tep_not_null($calendar_date_array['user_update']) ? $calendar_date_array['date_update'] : TEXT_UNSET_DATA)
      );

  //底部内容
  $buttons = array();
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="if(document.getElementById(\'repeat_flag\').value == 1){if(confirm(\''.TEXT_CALENDAR_REPEAT_COMMENT.'\')){save_submit();}}else{save_submit();}"').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="hidden_info_box();"').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_RESET, 'onclick="date_reset();"').'</a></form>'; 

  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $action_url_date = substr($_GET['date'],0,4) == date('Y') ? '' : '&y='.substr($_GET['date'],0,4);
  $action_url = $calendar_date_num == 1 ? 'action=date_edit' : 'action=date';
  $action_url .= $action_url_date;
  $form_str = tep_draw_form('calendar_date', FILENAME_BANK_CL, $action_url);

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($category_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if ($_GET['action'] == 'status_edit') {
/* -----------------------------------------------------
    功能: 显示银行状态编辑的弹出框
    参数: $_GET['id'] 状态id 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_BANK_CL);
  include(DIR_FS_ADMIN.'classes/notice_box.php'); 

  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 
  //获取银行营业状态信息
  $calendar_status_query = tep_db_query("select * from ". TABLE_CALENDAR_STATUS ." where id='".(int)$_GET['id']."'");
  $calendar_status_array = tep_db_fetch_array($calendar_status_query);
  tep_db_free_result($calendar_status_query);
  
  //头部内容
  $heading = array();
  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $calendar_status_array['title']);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  //主体内容
  $category_info_row = array();
  
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_COMMENTS.'<input type="hidden" name="cl_id" value="'.$_GET['id'].'">'), 
        array('align' => 'left', 'params' => 'colspan="2"', 'text' => '&nbsp;') 
      );
  
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_TITLE), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="text" class="option_input" name="title" value="'.$calendar_status_array['title'].'"><span id="title_error">'.TEXT_FIELD_REQUIRED.'</span>')
      );

  //名称
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_NAME), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="text" class="option_input" name="name" value="'.$calendar_status_array['name'].'"><span id="name_error">'.TEXT_FIELD_REQUIRED.'</span>')
      );

  //是否在前台显示
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_FRONT_DESK_SHOW), 
       array('align' => 'left', 'params' => 'width="55" nowrap="nowrap"', 'text' => '<input type="radio" name="front_desk_show" value="1" style="padding-left:0;margin-left:0;"'.($calendar_status_array['front_desk_show'] == 1 ? 'checked="checked"' : '').'>'.TEXT_CALENDAR_YES),
       array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input type="radio" name="front_desk_show" value="0"'.($calendar_status_array['front_desk_show'] == 0 ? 'checked="checked"' : '').'>'.TEXT_CALENDAR_NO)
     );

  //是否在前台注释显示
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_FRONT_DESK_COMMENT_SHOW), 
       array('align' => 'left', 'params' => 'width="55" nowrap="nowrap"', 'text' => '<input type="radio" name="front_desk_comment_show" value="1" style="padding-left:0;margin-left:0;"'.($calendar_status_array['front_desk_comment_show'] == 1 ? 'checked="checked"' : '').'>'.TEXT_CALENDAR_YES),
       array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input type="radio" name="front_desk_comment_show" value="0"'.($calendar_status_array['front_desk_comment_show'] == 0 ? 'checked="checked"' : '').'>'.TEXT_CALENDAR_NO)
     );
  //是否在日历上标记
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_SHOW), 
        array('align' => 'left', 'params' => 'width="55" nowrap="nowrap"', 'text' => '<input type="radio" name="is_show" style="padding-left:0;margin-left:0;" value="1"'.($calendar_status_array['is_show'] == 1 ? ' checked="checked"' : '').'>'.TEXT_CALENDAR_SHOW_YES),
        array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input type="radio" name="is_show" value="0"'.($calendar_status_array['is_show'] == 0 ? ' checked="checked"' : '').'>'.TEXT_CALENDAR_SHOW_NO)
      );

  //银行营业状态颜色选项 
  $color_array = array('#FFFFFF','#DD1F2C','#DD6E1F','#FFFFCC','#82C31C','#1F67DD','#982DAC','#F1A9EB','#B36520','#BEBEBE');
  $color_font_array = array(TEXT_CALENDAR_COLOR_WHITE,TEXT_CALENDAR_COLOR_RED,TEXT_CALENDAR_COLOR_BLUE_ORANGE,TEXT_CALENDAR_COLOR_BLUE_YELLOW,TEXT_CALENDAR_COLOR_BLUE_GREEN,TEXT_CALENDAR_COLOR_BLUE,TEXT_CALENDAR_COLOR_BLUE_PURPLE,TEXT_CALENDAR_COLOR_BLUE_PINK,TEXT_CALENDAR_COLOR_BLUE_BROWN,TEXT_CALENDAR_COLOR_BLUE_GRAY);
  $color_select_list = '<select name="color">';
  foreach($color_array as $color_key=>$color_value){
    $selected = $color_value == $calendar_status_array['color'] ? ' selected="selected"' : '';
    $color_select_list .= '<option value="'.$color_value.'"'.$selected.'>'.$color_font_array[$color_key].'</option>';
  }
  $color_select_list .= '</select>';
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_COLOR), 
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $color_select_list)
      );
  //是否受理  
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_HANDLE), 
       array('align' => 'left', 'params' => 'width="55" nowrap="nowrap"', 'text' => '<input type="radio" name="is_handle" style="padding-left:0;margin-left:0;" value="1"'.($calendar_status_array['is_handle'] == 1 ? ' checked="checked"' : '').' onclick="change_is_handle(1);">'.TEXT_CALENDAR_YES),
       array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input type="radio" name="is_handle" value="0"'.($calendar_status_array['is_handle'] == 0 ? ' checked="checked"' : '').' onclick="change_is_handle(0);">'.TEXT_CALENDAR_NO)
     ); 

  //开始时间下拉框
  $disabled = $calendar_status_array['is_handle'] == 1 ? '' : ' disabled="disabled"';
  $start_time_select_list = '<select name="start_time"'.$disabled.'>';
  //$start_time_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  $selected = '';
  for($i = 0;$i <= 23;$i++){

    if($calendar_status_array['is_handle'] == 1){ 
      $selected = $calendar_status_array['start_time'] == $i && $calendar_status_array['start_time'] != '' ? ' selected="selected"' : '';
    }
    $start_time_select_list .= '<option value="'.$i.'"'.$selected.'>'.($i < 10 ? '0'.$i : $i).'</option>';
  }
  $start_time_select_list .= '</select>';

  if($calendar_status_array['is_handle'] == 1){
    $start_min = $calendar_status_array['start_min'];
    $start_min_left = $start_min != -1 ? $start_min < 10 ? 0 : substr($start_min,0,1) : '';
    $start_min_right = $start_min < 10 ? $start_min : substr($start_min,1,1);
    $end_min = $calendar_status_array['end_min'];
    $end_min_left = $end_min < 10 ? 0 : substr($end_min,0,1);
    $end_min_right = $end_min < 10 ? $end_min : substr($end_min,1,1);
  }
  //开始时间下拉框---前半部分 分
  $left_min_select_list = '<select name="start_left_min"'.$disabled.'>';
  //$left_min_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  for($i = 0;$i <= 5;$i++){

    $selected = $calendar_status_array['is_handle'] == 1 && $start_min_left == $i && $start_min_left != '' ? ' selected="selected"' : '';
    $left_min_select_list .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $left_min_select_list .= '</select>';

  //开始时间下拉框---后半部分 分
  $right_min_select_list = '<select name="start_right_min"'.$disabled.'>';
  //$right_min_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  for($i = 0;$i <= 9;$i++){
    
    $selected = $calendar_status_array['is_handle'] == 1 && $start_min_right == $i ? ' selected="selected"' : '';
    $right_min_select_list .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $right_min_select_list .= '</select>';
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_START_TIME), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $start_time_select_list.'&nbsp;'.TEXT_TORIHIKI_HOUR_STR.'&nbsp;'.$left_min_select_list.$right_min_select_list.'&nbsp;'.TEXT_TORIHIKI_MIN_STR.'<span id="start_time_error"></span>')
      );

  //结束时间下拉框
  $end_time_select_list = '<select name="end_time"'.$disabled.'>';
  //$end_time_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  $selected = '';
  for($i = 0;$i <= 23;$i++){

    if($calendar_status_array['is_handle'] == 1){ 
      $selected = $calendar_status_array['end_time'] == $i && $calendar_status_array['start_time'] != '' ? ' selected="selected"' : '';
    }
    $end_time_select_list .= '<option value="'.$i.'"'.$selected.'>'.($i < 10 ? '0'.$i : $i).'</option>';
  }
  $end_time_select_list .= '</select>';

  //结束时间下拉框---前半部分 分
  $left_min_select_list = '<select name="end_left_min"'.$disabled.'>';
  //$left_min_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  for($i = 0;$i <= 5;$i++){

    $selected = $calendar_status_array['is_handle'] == 1 && $end_min_left == $i ? ' selected="selected"' : '';
    $left_min_select_list .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $left_min_select_list .= '</select>';

  //结束时间下拉框---后半部分 分
  $right_min_select_list = '<select name="end_right_min"'.$disabled.'>';
  //$right_min_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  for($i = 0;$i <= 9;$i++){

    $selected = $calendar_status_array['is_handle'] == 1 && $start_min_left == $i ? ' selected="selected"' : '';
    $right_min_select_list .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $right_min_select_list .= '</select>';
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_END_TIME), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $end_time_select_list.'&nbsp;'.TEXT_TORIHIKI_HOUR_STR.'&nbsp;'.$left_min_select_list.$right_min_select_list.'&nbsp;'.TEXT_TORIHIKI_MIN_STR.'<span id="end_time_error"></span>')
      );

  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_SORT), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="text" name="sort" value="'.$calendar_status_array['sort'].'" style="text-align: right;">')
      );

  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_ADDED), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => tep_not_null($calendar_status_array['user_added']) ? $calendar_status_array['user_added'] : TEXT_UNSET_DATA)
      );

  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_DATE_ADDED), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => tep_not_null($calendar_status_array['date_added']) && tep_not_null($calendar_status_array['user_added']) ? $calendar_status_array['date_added'] : TEXT_UNSET_DATA)
      );

  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_UPDATE), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => tep_not_null($calendar_status_array['user_update']) ? $calendar_status_array['user_update'] : TEXT_UNSET_DATA)
      );

  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_DATE_UPDATE), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => tep_not_null($calendar_status_array['date_update']) && tep_not_null($calendar_status_array['user_update']) ? $calendar_status_array['date_update'] : TEXT_UNSET_DATA)
      );

  //底部内容
  $buttons = array();
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_submit(IMAGE_SAVE, '').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="if(confirm(\''.TEXT_CALENDAR_DELETE_COMMENTS.'\')){status_delete();}"').'</a></form>'; 

  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $form_str = tep_draw_form('status_edit_form', FILENAME_BANK_CL, 'action=status_edit', 'post', 'onsubmit="return status_add_submit();"');

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($category_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if ($_GET['action'] == 'status_add') {
/* -----------------------------------------------------
    功能: 显示银行状态添加的弹出框
    参数: 无 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_BANK_CL);
  include(DIR_FS_ADMIN.'classes/notice_box.php'); 

  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 
  //头部内容
  $heading = array();
  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_CALENDAR_ADD);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  //主体内容
  $category_info_row = array();
  
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_COMMENTS.'<input type="hidden" name="cl_id" value="'.$_GET['id'].'">'), 
        array('align' => 'left', 'params' => 'colspan="2"', 'text' => '&nbsp;') 
      );
  
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_TITLE), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="text" class="option_input" name="title" value=""><span id="title_error">'.TEXT_FIELD_REQUIRED.'</span>')
      );

  //名称
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_NAME), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="text" class="option_input" name="name" value=""><span id="name_error">'.TEXT_FIELD_REQUIRED.'</span>')
      );

  //是否在前台显示
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_FRONT_DESK_SHOW), 
       array('align' => 'left', 'params' => 'width="55" nowrap="nowrap"', 'text' => '<input type="radio" name="front_desk_show" value="1" style="padding-left:0;margin-left:0;" checked="checked">'.TEXT_CALENDAR_YES),
       array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input type="radio" name="front_desk_show" value="0">'.TEXT_CALENDAR_NO)
     );

  //是否在前台注释显示
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_FRONT_DESK_COMMENT_SHOW), 
       array('align' => 'left', 'params' => 'width="55" nowrap="nowrap"', 'text' => '<input type="radio" name="front_desk_comment_show" value="1" style="padding-left:0;margin-left:0;" checked="checked">'.TEXT_CALENDAR_YES),
       array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input type="radio" name="front_desk_comment_show" value="0">'.TEXT_CALENDAR_NO)
     );

  //是否在日期上显示
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_SHOW), 
        array('align' => 'left', 'params' => 'width="55" nowrap="nowrap"', 'text' => '<input type="radio" name="is_show" style="padding-left:0;margin-left:0;" value="1">'.TEXT_CALENDAR_SHOW_YES),
        array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input type="radio" name="is_show" value="0" checked="checked">'.TEXT_CALENDAR_SHOW_NO)
      );

  //银行营业状态颜色选项 
  $color_array = array('#FFFFFF','#DD1F2C','#DD6E1F','#FFFFCC','#82C31C','#1F67DD','#982DAC','#F1A9EB','#B36520','#BEBEBE'); 
  $color_font_array = array(TEXT_CALENDAR_COLOR_WHITE,TEXT_CALENDAR_COLOR_RED,TEXT_CALENDAR_COLOR_BLUE_ORANGE,TEXT_CALENDAR_COLOR_BLUE_YELLOW,TEXT_CALENDAR_COLOR_BLUE_GREEN,TEXT_CALENDAR_COLOR_BLUE,TEXT_CALENDAR_COLOR_BLUE_PURPLE,TEXT_CALENDAR_COLOR_BLUE_PINK,TEXT_CALENDAR_COLOR_BLUE_BROWN,TEXT_CALENDAR_COLOR_BLUE_GRAY);  
  $color_select_list = '<select name="color">';
  foreach($color_array as $color_key=>$color_value){
    $color_select_list .= '<option value="'.$color_value.'">'.$color_font_array[$color_key].'</option>';
  }
  $color_select_list .= '</select>';
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_COLOR), 
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $color_select_list)
      );
  //是否受理  
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_HANDLE), 
       array('align' => 'left', 'params' => 'width="55" nowrap="nowrap"', 'text' => '<input type="radio" name="is_handle" value="1" style="padding-left:0;margin-left:0;" checked="checked" onclick="change_is_handle(1);">'.TEXT_CALENDAR_YES),
       array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input type="radio" name="is_handle" value="0" onclick="change_is_handle(0);">'.TEXT_CALENDAR_NO)
     ); 

  //开始时间下拉框---时
  $start_time_select_list = '<select name="start_time">';
  //$start_time_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  for($i = 0;$i <= 23;$i++){

    $start_time_select_list .= '<option value="'.$i.'">'.($i < 10 ? '0'.$i : $i).'</option>';
  }
  $start_time_select_list .= '</select>';

  //开始时间下拉框---前半部分 分
  $left_min_select_list = '<select name="start_left_min">';
  //$left_min_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  for($i = 0;$i <= 5;$i++){

    $left_min_select_list .= '<option value="'.$i.'">'.$i.'</option>';
  }
  $left_min_select_list .= '</select>';

  //开始时间下拉框---后半部分 分
  $right_min_select_list = '<select name="start_right_min">';
  //$right_min_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  for($i = 0;$i <= 9;$i++){

    $right_min_select_list .= '<option value="'.$i.'">'.$i.'</option>';
  }
  $right_min_select_list .= '</select>';
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_START_TIME), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $start_time_select_list.'&nbsp;'.TEXT_TORIHIKI_HOUR_STR.'&nbsp;'.$left_min_select_list.$right_min_select_list.'&nbsp;'.TEXT_TORIHIKI_MIN_STR.'<span id="start_time_error"></span>')
      );

  //结束时间下拉框
  $end_time_select_list = '<select name="end_time">';
  //$end_time_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  for($i = 0;$i <= 23;$i++){
  
    $end_time_select_list .= '<option value="'.$i.'">'.($i < 10 ? '0'.$i : $i).'</option>';
  }
  $end_time_select_list .= '</select>';

  //结束时间下拉框---前半部分 分
  $left_min_select_list = '<select name="end_left_min">';
  //$left_min_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  for($i = 0;$i <= 5;$i++){

    $left_min_select_list .= '<option value="'.$i.'">'.$i.'</option>';
  }
  $left_min_select_list .= '</select>';

  //结束时间下拉框---后半部分 分
  $right_min_select_list = '<select name="end_right_min">';
  //$right_min_select_list .= '<option value="">'.TEXT_CALENDAR_NOT_SETTING.'</option>';
  for($i = 0;$i <= 9;$i++){

    $right_min_select_list .= '<option value="'.$i.'">'.$i.'</option>';
  }
  $right_min_select_list .= '</select>';
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_END_TIME), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $end_time_select_list.'&nbsp;'.TEXT_TORIHIKI_HOUR_STR.'&nbsp;'.$left_min_select_list.$right_min_select_list.'&nbsp;'.TEXT_TORIHIKI_MIN_STR.'<span id="end_time_error"></span>')
      );

  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_CALENDAR_SORT), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="text" name="sort" value="1000" style="text-align: right;">')
      );

  //底部内容
  $buttons = array();
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_submit(IMAGE_SAVE, '').'</a>'; 

  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $form_str = tep_draw_form('status_add_form', FILENAME_BANK_CL, 'action=status_add', 'post', 'onsubmit="return status_add_submit();"');

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($category_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if ($_GET['action'] == 'edit_tags') {
/* -----------------------------------------------------
    功能: 显示标签编辑的弹出框
    参数: $_POST['tags_id'] 标签ID 
    参数: $_POST['param_str'] URL参数
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_TAGS);
  include(DIR_FS_ADMIN.'classes/notice_box.php'); 

  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  //根据tags_id来读取相应的标签信息 
  $tags_id = $_POST['tags_id'];
  $param_str = $_POST['param_str'];
  if(trim($param_str) != ''){
    $param_str_array = explode('|||',$param_str);
    foreach($param_str_array as $param_value){

      $param_value_array = explode('=',$param_value); 
      if($param_value_array[0] == 'sort'){

        $sort_str = $param_value_array[1];
      }
      if($param_value_array[0] == 'page'){

        $page_str = $param_value_array[1];
      } 
    }
  }
  $tags_query = tep_db_query("select * from ". TABLE_TAGS ." where tags_id='".$tags_id."'");
  $tags_array = tep_db_fetch_array($tags_query);
  tep_db_free_result($tags_query);

  //生成tags的上一个，下一个的tags_id
  $tags_query_raw = "select tags_id from " . TABLE_TAGS . " order by tags_order,tags_name";
  if(isset($sort_str) && $sort_str != ''){
    $tags_query_raw = "select tags_id from " . TABLE_TAGS;
    switch($sort_str){
    /*----------------------------
    case '4a'  排列顺序(a-z) 递增
    case '4d'  排列顺序(z-a) 递减
    case '5a'  排列顺序(あ-ん) 递增
    case '5d'  排列顺序(ん-あ) 递减
    ---------------------------*/
      case '4a':
        $tags_query_raw .=' order by tags_name asc'; 
        break;
      case '4d':
        $tags_query_raw .=' order by tags_name desc'; 
        break;
      case '5a':
        $tags_query_raw .=' order by tags_name asc'; 
        break;
      case '5d':
        $tags_query_raw .=' order by tags_name desc'; 
        break;
    }
  }
  $tags_id_array = array();
  $tags_id_query = tep_db_query($tags_query_raw);
  while($tags_query_array = tep_db_fetch_array($tags_id_query)){

    $tags_id_array[] = $tags_query_array['tags_id']; 
  } 
  tep_db_free_result($tags_id_query);

  $page_str = isset($page_str) && $page_str != '' ? $page_str : 1;
  $page_num_start = ($page_str-1) * MAX_DISPLAY_SEARCH_RESULTS;
  $page_num_end = $page_str * MAX_DISPLAY_SEARCH_RESULTS - 1;
  $tags_id_page_array = array();
  for($i = $page_num_start;$i <= $page_num_end;$i++){

    $tags_id_page_array[] = $tags_id_array[$i];
  }
  $tags_id_num = array_search($tags_id,$tags_id_page_array);
  if($tags_id_num > 0){
    $tags_id_prev = $tags_id_page_array[$tags_id_num-1];
  }
  if($tags_id_num < count($tags_id_page_array) - 1){
    $tags_id_next = $tags_id_page_array[$tags_id_num+1];
  }

  //头部内容
  $heading = array();

  //显示上一个，下一个按钮
  $page_str = '';
  
  if ($tags_id_num > 0) {
    $page_str .= '<a id="tags_prev" onclick="show_link_tags_info(\''.$tags_id_prev.'\',\''.$param_str.'\')" href="javascript:void(0);" ><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
 
  if ($tags_id_num < (count($tags_id_page_array) - 1)) {
    $page_str .= '<a id="tags_next" onclick="show_link_tags_info(\''.$tags_id_next.'\',\''.$param_str.'\')" href="javascript:void(0);">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }else{
    $page_str .= '<font color="#000000">'.IMAGE_NEXT.'></font>&nbsp;&nbsp;';
  }

  $page_str .= '<a onclick="close_tags_info();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $tags_array['tags_name']);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  //主体内容
  $category_info_row = array();
   
  //标签名称编辑框 
  $tags_images_array = explode('/',$tags_array['tags_images']);
  $tags_images_str = end($tags_images_array);
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="25%" nowrap="nowrap"', 'text' => TEXT_INFO_TAGS_NAME.'<input type="hidden" name="tags_id" value="'.$tags_array['tags_id'].'"><input type="hidden" name="param_str" value="'.$param_str.'"><input type="hidden" id="tags_images_id" value="'.$tags_images_str.'">'), 
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="text" class="option_input" name="tags_name" value="'.$tags_array['tags_name'].'"><span id="tags_name_error">'.TEXT_FIELD_REQUIRED.'</span>')
      );
  //标签图片上传框  
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="25%" nowrap="nowrap"', 'text' => TEXT_INFO_TAGS_IMAGE), 
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => tep_draw_file_field('tags_images'))
     ); 
  if(!is_dir(tep_get_upload_dir().$tags_array['tags_images']) && file_exists(tep_get_upload_dir().$tags_array['tags_images'])){
    //显示图片
    $category_info_row[]['text'] = array(
         array('align' => 'left', 'params' => 'width="25%" nowrap="nowrap"', 'text' => '&nbsp;'), 
         array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => tep_image(DIR_WS_CATALOG_IMAGES . $tags_array['tags_images'], $tags_array['tags_name']))
       ); 
    //显示是否删除图片的选择框
    $category_info_row[]['text'] = array(
         array('align' => 'left', 'params' => 'width="25%" nowrap="nowrap"', 'text' => '&nbsp;'), 
         array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="checkbox" name="delete_image" value="1" >'.TEXT_CONFIRM_DELETE_TAG)
       );
  }

  //作成者，作成时间，更新者，更新时间 
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="25%" nowrap="nowrap"', 'text' => TEXT_USER_ADDED),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => ((tep_not_null($tags_array['user_added'])?$tags_array['user_added']:TEXT_UNSET_DATA)))
      );
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="25%" nowrap="nowrap"', 'text' => TEXT_DATE_ADDED),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => ((tep_not_null(tep_datetime_short($tags_array['date_added'])))?tep_datetime_short($tags_array['date_added']):TEXT_UNSET_DATA))
      );
  
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="25%" nowrap="nowrap"', 'text' => TEXT_USER_UPDATE),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => ((tep_not_null($tags_array['user_update'])?$tags_array['user_update']:TEXT_UNSET_DATA)))
      );
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="25%" nowrap="nowrap"', 'text' => TEXT_DATE_UPDATE),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => ((tep_not_null(tep_datetime_short($tags_array['date_update'])))?tep_datetime_short($tags_array['date_update']):TEXT_UNSET_DATA))
      );
  //底部内容
  $buttons = array();
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="edit_tags_submit(\'save\');"').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="edit_tags_submit(\'deleteconfirm\');"').'</a>';

  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $form_str = tep_draw_form('tags_form', FILENAME_TAGS, '', 'post', 'enctype="multipart/form-data" onsubmit="return edit_tags_submit();"');

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($category_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if ($_GET['action'] == 'create_tags') {
/* -----------------------------------------------------
    功能: 显示标签添加的弹出框
    参数: $_POST['param_str'] URL参数
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_TAGS);
  include(DIR_FS_ADMIN.'classes/notice_box.php'); 

  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  //头部内容
  $heading = array();

  $page_str = '';
  
  $page_str .= '<a onclick="close_tags_info();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => IMAGE_NEW_PROJECT);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  //主体内容
  $category_info_row = array();
   
  //标签名称编辑框 
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="25%" nowrap="nowrap"', 'text' => TEXT_INFO_TAGS_NAME.'<input type="hidden" name="param_str" value="'.$param_str.'">'), 
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="text" class="option_input" name="tags_name" value=""><span id="tags_name_error">'.TEXT_FIELD_REQUIRED.'</span>')
      );
  //标签图片上传框  
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="25%" nowrap="nowrap"', 'text' => TEXT_INFO_TAGS_IMAGE), 
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => tep_draw_file_field('tags_images'))
     ); 
   
  //底部内容
  $buttons = array();
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_submit(IMAGE_SAVE, '').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="close_tags_info();"').'</a>'; 

  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $form_str = tep_draw_form('tags_form', FILENAME_TAGS, 'action=insert', 'post', 'enctype="multipart/form-data" onsubmit="return create_tags_submit();"');

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($category_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if ($_GET['action'] == 'edit_products_tags') {
/* -----------------------------------------------------
    功能: 显示商品关联标签弹出框
    参数: $_POST['tags_id_list'] 标签列表 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php'); 

  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  //头部内容
  $heading = array();

  $page_str = '';
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_EDIT_TAGS_TITLE);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  //主体内容
  $category_info_row = array();
   
  //标签列表 
  $tags_id_list = $_POST['tags_id_list'];
  $tags_type = $_POST['type'];
  $tags_url = $_POST['url'];
  $products_id = $_POST['pid'];
  $tags_url_array = explode('|||',$tags_url);
  foreach($tags_url_array as $tags_value){

    $tags_site_array = explode('=',$tags_value);
    if($tags_site_array[0] == 'site_id'){

      $site_id = $tags_site_array[1]; 
      break;
    }
  }

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

  $checked_temp_array = array();
  if($tags_type == 1){
          
          $checked_temp_array = $_SESSION['pid_tags_id_list_array'][$tags_key]; 
  }else{ 
          $checked_temp_array = $_SESSION['carttags_id_list_array'][$tags_key];  
  } 

  $site_id_flag = !isset($site_id) || $site_id == '0' ? true : false;
  $checked_array = array();
  $table_str = $tags_type == 1 ? TABLE_PRODUCTS_TO_TAGS : 'products_to_carttag';
  $checked_tags_query = tep_db_query("select tags_id from ". $table_str ." where products_id='".$products_id."'");   
  while($checked_tags_array = tep_db_fetch_array($checked_tags_query)){

    $checked_array[] = $checked_tags_array['tags_id'];
  }
  tep_db_free_result($checked_tags_query);

  $tags_query = tep_db_query("select * from ". TABLE_TAGS ." where tags_id in (".$tags_id_list.")");
  $tags_list_str = '<table width="100%" cellspacing="0" cellpadding="2" border="0"><tr>';
  $tags_i = 1;
  $disabled = $site_id_flag == false ? ' disabled="disabled"' : '';
  while($tags_array = tep_db_fetch_array($tags_query)){

    $checked_str = in_array($tags_array['tags_id'],$checked_array) ? ' checked="checked"' : '';  
    if(!empty($checked_temp_array)){
      $checked_str = in_array($tags_array['tags_id'],$checked_temp_array) ? ' checked="checked"' : '';
    }
    $tags_list_str .= '<td width="20%"><input type="checkbox" name="tags_id[]" value="'.$tags_array['tags_id'].'"'.$checked_str.$disabled.'>'.$tags_array['tags_name'].'</td>';
    if($tags_i % 5 == 0){

      $tags_list_str .= '</tr><tr>';
    }
    $tags_i++;
  } 
  $tags_list_str .= '</tr></table>';
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => '', 'text' => $tags_list_str.'<input type="hidden" name="tags_type" value="'.$tags_type.'"><input type="hidden" name="tags_url" value="'.$tags_url.'">'), 
      );
   
  //底部内容
  $buttons = array();
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(TEXT_EDIT_TAGS_SAVE, 'onclick="return edit_products_tags_check(\'tags_id[]\');"'.$disabled).'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(TEXT_EDIT_TAGS_ALL_SELECT, 'onclick="all_select_tags(\'tags_id[]\');"'.$disabled).'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(OPTION_CLEAR, 'onclick="all_reset_tags(\'tags_id[]\');"'.$disabled).'</a>';

  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $form_str = tep_draw_form('edit_tags', FILENAME_CATEGORIES, 'action=edit_products_tags', 'post', 'id="edit_tags_id"');

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($category_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if ($_GET['action'] == 'edit_configuration'){
/*-------------------------------------------------
  功能：显示配置弹出框 
  参数：$_GET['cID'] 获取configuration表的ID值
  参数：$_GET['gID'] 获取configuration_group表的ID值
 ------------------------------------------------*/
include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_CONFIGURATION);
include(DIR_FS_ADMIN.'classes/notice_box.php');
$notice_box = new notice_box('popup_order_title','popup_order_info');
$configuration_query = tep_db_query(" select configuration_id, configuration_title, configuration_key, configuration_value, use_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '" . $_GET['gID'] . "' and `site_id` = '0'  order by sort_order");
$site_id = $_GET['site_id'];
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$_SESSION['loginuid']."' limit 0,1");
while($userslist= tep_db_fetch_array($sites_id)){
     $site_permission = $userslist['site_permission']; 
}
if(isset($site_permission)) $site_arr=$site_permission;//权限判断
else $site_arr="";
$site_array = explode(',',$site_arr);
while ($configuration = tep_db_fetch_array($configuration_query)) {
   $cid_array[] = $configuration['configuration_id'];
    if (tep_not_null($configuration['use_function'])) {
  $use_function = $configuration['use_function'];
  if (ereg('->', $use_function)) {
      $class_method = explode('->', $use_function);
      if (!is_object(${$class_method[0]})) {
    include(DIR_WS_CLASSES . $class_method[0] . '.php');
    ${$class_method[0]} = new $class_method[0]();
      }
      $cfgValue = tep_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
  } else {
      $cfgValue = tep_call_function($use_function, $configuration['configuration_value']);
  }
    } else {
  $cfgValue = $configuration['configuration_value'];
    }
    if (
        ((!isset($_GET['cID']) || !$_GET['cID']) || ($_GET['cID'] == $configuration['configuration_id'])) 
        && (!isset($cInfo) || !$cInfo) 
        && (!isset($_GET['action']) or substr($_GET['action'], 0, 3) != 'new')
    ) {
  $cfg_extra_query = tep_db_query("select  configuration_key, configuration_description, date_added, last_modified, use_function, set_function,user_added,user_update from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
  $cfg_extra= tep_db_fetch_array($cfg_extra_query);
  $cInfo_array = tep_array_merge($configuration, $cfg_extra);
  $cInfo = new objectInfo($cInfo_array);
    }
      if($configuration['configuration_key'] == 'DS_ADMIN_SIGNAL_TIME'){
       $tmp_setting_array = @unserialize(stripslashes($cfgValue));
       $configuration_key_tmp = '';
       $configuration_key_tmp .= SIGNAL_GREEN.'&nbsp;&nbsp;'.NOW_TIME_TEXT.'<br>'.(int)($tmp_setting_array['green'][0].$tmp_setting_array['green'][1].$tmp_setting_array['green'][2].$tmp_setting_array['green'][3]).'&nbsp;&nbsp;'.NOW_TIME_LINK_TEXT.'<br>';
       $configuration_key_tmp .= SIGNAL_YELLOW.'&nbsp;&nbsp;'.NOW_TIME_TEXT.'<br>'.(int)($tmp_setting_array['yellow'][0].$tmp_setting_array['yellow'][1].$tmp_setting_array['yellow'][2].$tmp_setting_array['yellow'][3]).'&nbsp;&nbsp;'.NOW_TIME_LINK_TEXT.'<br>';
       $configuration_key_tmp .= SIGNAL_RED.'&nbsp;&nbsp;'.NOW_TIME_TEXT.'<br>'.(int)($tmp_setting_array['red'][0].$tmp_setting_array['red'][1].$tmp_setting_array['red'][2].$tmp_setting_array['red'][3]).'&nbsp;&nbsp;'.NOW_TIME_LINK_TEXT.'<br>';
      } 
  }
  $heading = array();
  $contents = array();
  foreach ($cid_array as $c_key => $c_value) {
    if ($_GET['cID'] == $c_value) {
      break; 
    }
  }
  $page_str = '';
  if ($c_key > 0) {
    $page_str .= '<a id="option_prev"" onclick=\'show_text_configuration("",'.$_GET['gID'].','.$cid_array[$c_key-1].','.$site_id.')\' href="javascript:void(0);" id="option_next">'.TEXT_CAMPAIGN_PREV.'</a>&nbsp;&nbsp;'; 
  }
 
  if ($c_key < (count($cid_array) - 1)) { 
    $page_str .= '<a id="option_next" onclick=\'show_text_configuration("",'.$_GET['gID'].','.$cid_array[$c_key+1].','.$site_id.')\' href="javascript:void(0);" id="option_next">'.TEXT_CAMPAIGN_NEXT.'</a>&nbsp;&nbsp;'; 
  }
    $configuration_key_array = array(
            'MAX_DISPLAY_CUSTOMER_MAIL_RESULTS',
            'MAX_DISPLAY_FAQ_ADMIN',
            'POINT_EMAIL_TEMPLATE',
            'POINT_EMAIL_DATE',
            'ADMINPAGE_LOGO_IMAGE',
            'MAX_DISPLAY_PW_MANAGER_RESULTS',
            'MAX_DISPLAY_ORDERS_RESULTS',
            'USER_AGENT_LIGHT_KEYWORDS',
            'HOST_NAME_LIGHT_KEYWORDS',
            'USER_AGENT_LIGHT_KEYWORDS',
            'HOST_NAME_LIGHT_KEYWORDS',
            'IP_LIGHT_KEYWORDS',
            'OS_LIGHT_KEYWORDS',
            'BROWSER_LIGHT_KEYWORDS',
            'HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS',
            'SYSTEM_LANGUAGE_LIGHT_KEYWORDS',
            'USER_LANGUAGE_LIGHT_KEYWORDS',
            'SCREEN_RESOLUTION_LIGHT_KEYWORDS',
            'COLOR_DEPTH_LIGHT_KEYWORDS',
            'FLASH_LIGHT_KEYWORDS',
            'FLASH_VERSION_LIGHT_KEYWORDS',
            'DIRECTOR_LIGHT_KEYWORDS',
            'QUICK_TIME_LIGHT_KEYWORDS',
            'REAL_PLAYER_LIGHT_KEYWORDS',
            'WINDOWS_MEDIA_LIGHT_KEYWORDS',
            'PDF_LIGHT_KEYWORDS',
            'JAVA_LIGHT_KEYWORDS',
            'TELNO_KEYWORDS', 
            'ORDER_INFO_TRANS_NOTICE', 
            'ORDER_INFO_TRANS_WAIT', 
            'ORDER_INFO_INPUT_FINISH', 
            'ORDER_INFO_ORDER_INFO', 
            'ORDER_INFO_CUSTOMER_INFO', 
            'ORDER_INFO_REFERER_INFO', 
            'ORDER_INFO_ORDER_HISTORY', 
            'ORDER_INFO_REPUTAION_SEARCH', 
            'ORDER_INFO_PRODUCT_LIST', 
            'ORDER_INFO_ORDER_COMMENT', 
            'ORDER_INFO_BASIC_TEXT',
            'DB_CALC_PRICE_HISTORY_DATE',
            'ORDERS_EMPTY_EMAIL_TITLE',
            'ORDERS_EMPTY_EMAIL_TEXT',
            'ORDER_EFFECTIVE_DATE',
            'DS_ADMIN_SIGNAL_TIME',
            'SEG_CRONTAB_ROW',
            'SEG_CRONTAB_SLEEP',
            'REVIEWS_BAN_CHARACTER',
            );
  //头部内容
  if(constant($cInfo->configuration_title) == null){
     $cInfo_configuration_title = $cInfo->configuration_title;
  }else{
     $cInfo_configuration_title = strip_tags(str_replace('F_','',constant($cInfo->configuration_title)));
  }
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $cInfo_configuration_title.'&nbsp;&nbsp;');
  $heading[] = array('align' => 'right', 'text' => $page_str);
    if ($cInfo->set_function) {
      if ($cInfo->configuration_key == 'DS_ADMIN_SIGNAL_TIME') {
        eval('$value_field = '.$cInfo->set_function."'".$cInfo->configuration_value."');"); 
      } else {
        if(in_array($site_id,$site_array)){
        eval('$value_field = ' . $cInfo->set_function . '\'' .  htmlspecialchars(addcslashes($cInfo->configuration_value, '\'')) . '\');');
        }else{
        eval('$value_field = ' . $cInfo->set_function . '\'' .  htmlspecialchars(addcslashes($cInfo->configuration_value, '\'')) .  '\',\'\',\'disabled="disabled"\');');
        }
        $value_field = str_replace('<br>','',$value_field);
        $value_field .= '<input type="hidden" name="hidden_configuration_value" value="'.$cInfo->configuration_value.'">';
      }
      $value_field = htmlspecialchars_decode($value_field);
    } else {
      if($cInfo->configuration_key == 'ADMINPAGE_LOGO_IMAGE') {
        if(in_array($site_id,$site_array)) { 
          $value_field = tep_draw_file_field('upfile') . '<br>' . $cInfo->configuration_value;
        }else{
          $value_field = tep_draw_file_field('upfile','','disabled="disabled"') . '<br>' . $cInfo->configuration_value;
        }
      } else {
       if(in_array($site_id,$site_array)) { 
        $value_field = '<textarea name="configuration_value" rows="5" cols="35">'. $cInfo->configuration_value .'</textarea><input type="hidden" name="hidden_configuration_value" value="'.$cInfo->configuration_value.'">';
       }else{
        $value_field = '<textarea name="configuration_value" rows="5" cols="35" disabled="disabled">'. $cInfo->configuration_value .'</textarea><input type="hidden" name="hidden_configuration_value" value="'.$cInfo->configuration_value.'">';
       }
      }
    }
// 针对 logo—image 做特殊处理
   if($cInfo->configuration_key == 'ADMINPAGE_LOGO_IMAGE') {
  $form_str = tep_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id . '&action=save', 'post', 'enctype="multipart/form-data"');
    } else {
  $form_str = tep_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id . '&action=save');
    }
   //主体all内容
    $configuration_contents[]['text'] = array(
      array('align' => 'center','params' => 'colspan="3"','text' => '<input type="hidden" name="user_update" value="'.$user_info['name'].'">'),
   );
    $configuration_contents[]['text'] = array(
      array('text' => str_replace('&nbsp;','',$cInfo_configuration_title)),
      array('text' => $value_field.'<br><font size="1">'.$cInfo->configuration_description.'</font>')
   );
  $configuration_contents[]['text'] = array(
        array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_USER_ADDED.((tep_not_null($cInfo->user_added))?$cInfo->user_added:TEXT_UNSET_DATA)), 
        array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_DATE_ADDED.((tep_not_null($cInfo->date_added))?$cInfo->date_added:TEXT_UNSET_DATA))
      );
  
  $configuration_contents[]['text'] = array(
        array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_USER_UPDATE.((tep_not_null($cInfo->user_update))?$cInfo->user_update:TEXT_UNSET_DATA)),
        array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_DATE_UPDATE.((tep_not_null($cInfo->last_modified))?$cInfo->last_modified:TEXT_UNSET_DATA))
      );
  
    //button 内容 
    if(in_array($site_id,$site_array)) { 
    if ($cInfo->configuration_key == 'DS_ADMIN_SIGNAL_TIME') {
      $configuration_button[] = '<br>' .  tep_html_element_button(IMAGE_UPDATE, 'onclick="check_signal_time_select()"') . '&nbsp;';
    } else {
      $configuration_button[] = '<br>' .  tep_html_element_submit(IMAGE_UPDATE) . '&nbsp;';
    }
    }else{
    if ($cInfo->configuration_key == 'DS_ADMIN_SIGNAL_TIME') {
      $configuration_button[] = '<br>' .  tep_html_element_button(IMAGE_UPDATE, 'disabled="disabled";onclick="check_signal_time_select()"') . '&nbsp;';
    } else {
      $configuration_button[] = '<br>' .
        tep_html_element_submit(IMAGE_UPDATE,'disabled="disabled"') . '&nbsp;';
    }
 
    }
   if(!empty($configuration_button)){
      $configuration_buttons = array('align' => 'center', 'button' => $configuration_button); 
   }
   if($site_id == 0){
   $notice_box->get_form($form_str);
   $notice_box->get_heading($heading);
   $notice_box->get_contents($configuration_contents, $configuration_buttons);
   $notice_box->get_eof(tep_eof_hidden());
   echo $notice_box->show_notice();
   }
    $contents_sites_array = array();
    $select_site_configure = tep_db_query('select * from sites order by order_num');
    // configuration admin page only
    if(!in_array($cInfo->configuration_key,$configuration_key_array)) 
  $site = tep_db_fetch_array($select_site_configure);
  $site_romaji[] = $site['romaji'];
  $select_configurations = tep_db_query('select * from configuration where configuration_key =\''.$cInfo->configuration_key.'\' and site_id = '.$_GET['site_id']);
        $fetch_result = tep_db_fetch_array($select_configurations);
  // if not exist ,copy from which site_id = 0
        if (!$fetch_result){
      $fetch_result = tep_db_fetch_array(tep_db_query('select * from configuration where configuration_key=\''.$cInfo->configuration_key.'\' and site_id = 0'));
      $fetch_result['configuration_id'].='_'.$site_id;
      $fetch_result['site_id']=$site['id'];
  }
  if($fetch_result['set_function']) {
   if(in_array($cInfo->configuration_key,$configuration_key_array)){
      if($cInfo->configuration_key == 'DS_ADMIN_SIGNAL_TIME'){
      $value_field = $configuration_key_tmp; 
      }else{
       $value_field = $fetch_result['configuration_value'];
      }
   }else{
      if(in_array($site_id,$site_array)){
      eval('$value_field = ' . $fetch_result['set_function'] . '\'' .  htmlspecialchars(addcslashes($fetch_result['configuration_value'], '\'')) . '\');');
      }else{
      eval('$value_field = ' . $fetch_result['set_function'] . '\'' .  htmlspecialchars(addcslashes($fetch_result['configuration_value'], '\'')) . '\',\'\',\'disabled="disabled"\');');
      }
        $value_field = str_replace('<br>','',$value_field);
        $value_field .= '<input type="hidden" name="hidden_configuration_value" value="'.$cInfo->configuration_value.'">';
   }
  } else {
      if($fetch_result['configuration_key'] == 'ADMINPAGE_LOGO_IMAGE') {
       if(in_array($site_id,$site_array)) { 
         $value_field = tep_draw_file_field('upfile'). '<br>' . $fetch_result['configuration_value'];
       }else{
         $value_field = tep_draw_file_field('upfile','','disabled="disabled"'). '<br>' . $fetch_result['configuration_value'];
       }
      } else {
          if(in_array($cInfo->configuration_key,$configuration_key_array)){
            if($cInfo->configuration_key == 'TELNO_KEYWORDS'){
              $value_field = str_replace('|',' | ',$fetch_result['configuration_value']);
            }else{
              $value_field = $fetch_result['configuration_value'];
            }
          }else{
       if(in_array($site_id,$site_array)) { 
          $value_field = '<textarea name="configuration_value" rows="5" cols="35">'. $fetch_result['configuration_value'] .'</textarea> <input type="hidden" name="hidden_configuration_value" value="'.$fetch_result['configuration_value'].'">
            ';
       }else{
          $value_field = '<textarea name="configuration_value" rows="5" cols="35" disabled="disabled">'.  $fetch_result['configuration_value'] .'</textarea> <input type="hidden" name="hidden_configuration_value" value="'.$fetch_result['configuration_value'].'">';
       }
          }
      }
  }

  if($fetch_result['configuration_key'] == 'ADMINPAGE_LOGO_IMAGE') {
      $configuration_form = tep_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $fetch_result['configuration_id'] . '&action=save', 'post', 'enctype="multipart/form-data"');
  } else {
      $configuration_form = tep_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $fetch_result['configuration_id'] . '&action=save');
  }
  //主体内容
  if(constant($fetch_result['configuration_title']) == null){
     $fetch_result_configuration_title = $fetch_result['configuration_title']; 
  }else{
     $fetch_result_configuration_title = strip_tags(str_replace('F_','',constant($fetch_result['configuration_title']))); 
  }
  $configuration_user_update = tep_db_fetch_array(tep_db_query('select * from configuration where configuration_key="'.$cInfo->configuration_key.'" and site_id = "'.$site_id.'"'));
  $contents[]['text'] = array(
    array('text' => $fetch_result_configuration_title),
    array('text' => $value_field.'<br><font size="1">'.$cInfo->configuration_description.'</font>')
    );
  $contents[]['text'] = array(
        array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_USER_ADDED.((tep_not_null($configuration_user_update['user_added']))?$configuration_user_update['user_added']:TEXT_UNSET_DATA)), 
        array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_DATE_ADDED.((tep_not_null($configuration_user_update['date_added']))?$configuration_user_update['date_added']:TEXT_UNSET_DATA))
      );
  
  $contents[]['text'] = array(
        array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_USER_UPDATE.((tep_not_null($configuration_user_update['user_update']))?$configuration_user_update['user_update']:TEXT_UNSET_DATA)),
        array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_DATE_UPDATE.((tep_not_null($configuration_user_update['last_modified']))?$configuration_user_update['last_modified']:TEXT_UNSET_DATA))
      );
 
  //if exists ,can be delete ,or  can not 
  if(in_array($site_id,$site_array)) { 
  if (is_numeric($fetch_result['configuration_id'])){
  $button[] = '<br>' .  tep_html_element_submit(IMAGE_UPDATE) .'&nbsp;<a href="' .  tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] .  '&action=tdel&cID=' .  $fetch_result['configuration_id'].'_'.$cInfo->configuration_id) .  '">'.tep_html_element_button(IMAGE_DEFFECT).'</a>'. '&nbsp;';
  }else {
    $button[] = '<br>' .  tep_html_element_submit(IMAGE_EFFECT) . '&nbsp;';
  }
  }else{
    if (is_numeric($fetch_result['configuration_id'])){
  $button[] = '<br>' .  tep_html_element_submit(IMAGE_UPDATE,'disabled="disabled"') .'&nbsp;'.tep_html_element_button(IMAGE_DEFFECT,'disabled="disabled"').'&nbsp;';
  }else {
    $button[] = '<br>' .  tep_html_element_submit(IMAGE_EFFECT,'disabled="disabled"') . '&nbsp;';
  }

  }
   if(!empty($button)){
     if(!in_array($cInfo->configuration_key,$configuration_key_array)){
      $buttons = array('align' => 'center', 'button' => $button); 
     }
   }
   if($site_id != 0){
   $notice_box->get_form($configuration_form);
   $notice_box->get_heading($heading);
   $notice_box->get_contents($contents, $buttons);
   $notice_box->get_eof(tep_eof_hidden());
   echo $notice_box->show_notice();
   }
}else if ($_GET['action'] == 'edit_reviews'){
 if(isset($_GET['default_value'])&&$_GET['default_value']=='save'){
   $_SESSION['r_default_value'] = array(
       'df_status' => $_GET['df_status'],
       'df_rating' => $_GET['df_rating'],
       'df_year' => $_GET['df_year'],
       'df_m' => $_GET['df_m'],
       'df_d' => $_GET['df_d'],
       'df_h' => $_GET['df_h'],
       'df_i' => $_GET['df_i'],
       'df_cid' => $_GET['df_cid'],
       'df_pid' => $_GET['df_pid']
       );
   exit;
  }
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_REVIEWS);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$_SESSION['loginuid']."' limit 0,1");
  while($userslist= tep_db_fetch_array($sites_id)){
    $site_arr = $userslist['site_permission']; 
  }
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
    $rID = tep_db_prepare_input($_GET['rID']);
    $reviews_query = tep_db_query("
        select r.reviews_id, 
               r.products_id, 
               r.customers_name, 
               r.date_added, 
               r.last_modified, 
               r.user_added,
               r.user_update,
               r.reviews_read, 
               rd.reviews_text, 
               r.reviews_rating, 
               r.reviews_status,
               s.romaji,
               s.name as site_name,
               r.site_id
        from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd , ".TABLE_SITES." s
        where r.reviews_id = '" . tep_db_input($rID) . "' 
          and s.id = r.site_id
          and r.reviews_id = rd.reviews_id");
    $reviews = tep_db_fetch_array($reviews_query);
    //判断新建还是 编辑
    if(isset($rID)&&$rID){
       $action_type = 'update'; 
       $site_permission = editPermission($site_arr, $reviews['site_id'],true);
    }else{
       $action_type = 'insert'; 
       $site_permission = editPermission($site_arr, $_GET['site_id'],true);
    }
    if(!$site_permission){
      $str_disabled = ' disabled="disabled" ';
    }else{
      $str_disabled = '';
    }
    $products_query = tep_db_query("
        select products_image 
        from " . TABLE_PRODUCTS . " 
        where products_id = '" . $reviews['products_id'] . "'");
    $products = tep_db_fetch_array($products_query);

    $products_name_query = tep_db_query("
        select *
        from " . TABLE_PRODUCTS_DESCRIPTION . " 
        where products_id = '" . $reviews['products_id'] . "' 
          and site_id = '0'
          and language_id = '" . $languages_id . "'");
    $products_name = tep_db_fetch_array($products_name_query);
     
    if(isset($_GET['product_name']) && $_GET['product_name']){
       $p_list_arr = array();
       $p_list_arr_site = array();
       if(isset($_GET['site_id'])&&$_GET['site_id']){
         $p_list_arr_site_sql = "select products_id,products_name from ".
           TABLE_PRODUCTS_DESCRIPTION." where 
           products_name like '%".trim($_GET['product_name'])."%' 
           and site_id = '".$_GET['site_id']."'";
         $p_list_arr_site_query = tep_db_query($p_list_arr_site_sql);
         while($p_list_arr_site_res = tep_db_fetch_array($p_list_arr_site_query)){
           $p_list_arr[] = $p_list_arr_site_res['products_id'];
           $p_list_arr_site[$p_list_arr_site_res['products_id']] =
           $p_list_arr_site_res['products_name'];
         }
       }
       if(isset($_GET['site_id'])&&$_GET['site_id']){
         $p_list_arr_sql = "SELECT products_id FROM ".TABLE_PRODUCTS_DESCRIPTION." 
           WHERE site_id = 0 
           and products_name like '%".trim($_GET['product_name'])."%'
           and products_id not in 
           (select products_id FROM ".TABLE_PRODUCTS_DESCRIPTION." 
            where site_id ='".$_GET['site_id']."')";
       }else{
         $p_list_arr_sql = "select products_id from ".
           TABLE_PRODUCTS_DESCRIPTION." where 
           products_name like '%".trim($_GET['product_name'])."%' 
           and site_id = 0";
       }
         $p_list_arr_query = tep_db_query($p_list_arr_sql);
         while($p_list_arr_res = tep_db_fetch_array($p_list_arr_query)){
           if(!in_array($p_list_arr_res['products_id'],$p_list_arr)){
             $p_list_arr[] = $p_list_arr_res['products_id'];
             $p_list_arr_site[$p_list_arr_res['products_id']] =
             $p_list_arr_res['products_name'];
           }
         }
         $where_str = ' and r.products_id in ('.implode(',',$p_list_arr).') ';
    }
    $reviews_query_raw = "
      select r.reviews_id, 
             r.products_id, 
             r.date_added, 
             r.last_modified, 
	     r.user_added,
	     r.user_update,
             r.site_id,
             r.reviews_rating, 
             r.reviews_status ,
             s.romaji,
             s.name as site_name
     from " . TABLE_REVIEWS . " r, ".TABLE_SITES." s
     where r.site_id = s.id
        " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and s.id = '" .  intval($_GET['site_id']) . "' " : '') . "".$where_str."
     order by date_added DESC";

    $reviews_raw_query = tep_db_query($reviews_query_raw);
    while ($reviews_id = tep_db_fetch_array($reviews_raw_query)) {
         $rid_array[] = $reviews_id['reviews_id']; 
    }
   
    $rInfo_array = tep_array_merge($reviews, $products, $products_name);
    $rInfo = new objectInfo($rInfo_array);
//编辑的时候有默认值 新建的时候没有默认值 
//AJAX 验证的时候有默认值
 if(isset($_GET['validate'])&&$_GET['validate']){
  $df_status = $_SESSION['r_default_value']['df_status'];
  $df_rating = $_SESSION['r_default_value']['df_rating'];
  $df_year = $_SESSION['r_default_value']['df_year'];
  $df_m = $_SESSION['r_default_value']['df_m'];
  $df_d = $_SESSION['r_default_value']['df_d'];
  $df_h = $_SESSION['r_default_value']['df_h'];
  $df_i = $_SESSION['r_default_value']['df_i'];
  $df_cid = $_SESSION['r_default_value']['df_cid'];
  $df_pid = $_SESSION['r_default_value']['df_pid'];
}else if(isset($rID)&&$rID){
  $df_pinfo = tep_db_fetch_array(tep_db_query("select products_id from ".  TABLE_REVIEWS ." where reviews_id='".$rID."' limit 1"));
  if(!empty($df_pinfo)){
    $df_pid = $df_pinfo['products_id'];
  }else{
    $df_pid = 0;
  }
  $df_cinfo = tep_db_fetch_array(tep_db_query("select categories_id from ".  TABLE_PRODUCTS_TO_CATEGORIES  ." where products_id='".$df_pid."' limit 1"));
  if(!empty($df_cinfo)){
    $df_cid = $df_cinfo['categories_id'];
  }else{
    $df_cid = 0;
  }
  $df_year = intval(date('Y', strtotime($rInfo->date_added)));
  $df_m = intval(date('m', strtotime($rInfo->date_added)));
  $df_d = intval(date('d', strtotime($rInfo->date_added)));
  $df_h = intval(date('H', strtotime($rInfo->date_added)));
  $df_i = intval(date('i', strtotime($rInfo->date_added)));
  $df_rating = $rInfo->reviews_rating;
  $df_status = $rInfo->reviews_status;
  $df_title = tep_output_string_protected($rInfo->customers_name);
  $df_text = $rInfo->reviews_text;
}
// 输出表格
  $heading   = array(); 
  foreach ($rid_array as $r_key => $r_value) {
    if ($rID == $r_value) {
      break;
    }
  }
  $page_str = '';
  //标题
  if(isset($rID)&&$rID){
     if ($r_key > 0) { 
       $page_str .= '<a id="option_prev" onclick=\'show_text_reviews("",'.$_GET['page'].','.$rid_array[$r_key-1].','.$_GET['site_id'].')\' href="javascript:void(0);" id="option_next">'.TEXT_CAMPAIGN_PREV.'</a>&nbsp;&nbsp;';
     }
     if ($r_key < (count($rid_array) - 1)) {
       $page_str .= '<a id="option_next" onclick=\'show_text_reviews("",'.$_GET['page'].','.$rid_array[$r_key+1].','.$_GET['site_id'].')\' href="javascript:void(0);" id="option_next">'.TEXT_CAMPAIGN_NEXT.'</a>&nbsp;&nbsp;';
     }
  }
  if(isset($rID)&&$rID){
    $products_name = TEXT_CATEGORY_NAME;
  }else{
    $products_name = $rInfo->products_name;
  }
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $products_name.'&nbsp;&nbsp;');
  $heading[] = array('align' => 'right', 'text' => $page_str);
//信息列表
  if(isset($_GET['site_id'])&&$_GET['site_id']){
    $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$_GET['site_id']));
    $reviews['romaji'] = $site_name['romaji'];
  }
  $contents[]['text'] = array( 
      array('text' => ENTRY_SITE.':<input type="hidden" name="action_type" value="'.$action_type.'">'),
      array('text' => $reviews['romaji'].'<input id="site_id" name="site_id" type="hidden" value="'.$_GET['site_id'].'"><input id="site_hidden" name="site_hidden" type="hidden" value="'.$_GET['site_id'].'">')
  );
  if(isset($_GET['review_products_id_info']) && $_GET['review_products_id_info']){
    $review_products_id_info = $_GET['review_products_id_info']; 
  }else if(isset($df_cid) && $df_cid){
    $review_products_id_info = $df_cid; 
  }else{
    $review_products_id_info = 0;
  }
  $contents[]['text'] = array(
        array('text' => TEXT_CATEGORY_SELECT),
        array('text' => tep_draw_pull_down_menu('review_products_id', tep_get_category_tree(),$review_products_id_info,'id="review_products_id" onchange="change_review_products_id(this,'.$_GET['page'].','.$rID.','.$_GET['site_id'].')"'.$str_disabled) .'<input type="hidden" id="r_cid" value="'.$df_cid.'">') 
    );
   $result = tep_db_query(" SELECT products_name, p.products_id, cd.categories_name, ptc.categories_id FROM " . TABLE_PRODUCTS . " p LEFT JOIN " .  TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id=p.products_id LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON ptc.products_id=p.products_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id=ptc.categories_id where pd.language_id = '" . (int)$languages_id . "' and cd.site_id = '0' and pd.site_id = '0' ORDER BY categories_name");
    while($row = tep_db_fetch_array($result)){
          extract($row,EXTR_PREFIX_ALL,"db");
          $ProductList[$db_categories_id][$db_products_id] =
          $db_products_name;
          $CategoryList[$db_categories_id] = $db_categories_name;
          $LastCategory = $db_categories_name;
          }
    $LastOptionTag = "";
    $ProductSelectOptions = "<option value='0'>Don't Add New Product" .  $LastOptionTag . "\n";
    $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
    foreach($ProductList as $Category => $Products){
       $ProductSelectOptions .= "<option value='0'>$Category" .  $LastOptionTag . "\n";
       $ProductSelectOptions .= "<option value='0'>---------------------------" . $LastOptionTag . "\n";
       asort($Products);
       foreach($Products as $Product_ID => $Product_Name){
            $ProductSelectOptions .= "<option value='$Product_ID'> &nbsp; $Product_Name" . $LastOptionTag . "\n";
           }
       if($Category != $LastCategory){
          $ProductSelectOptions .= "<option value='0'>&nbsp;" .  $LastOptionTag . "\n";
          $ProductSelectOptions .= "<option value='0'>&nbsp;" .  $LastOptionTag . "\n";
          }
     }
      if(!isset($_GET['review_products_id_info'])){
        if(isset($review_products_id_info) && $review_products_id_info){
          $add_product_categories_id = $review_products_id_info;
        }else{
          $add_product_categories_id = 0;
        }
      }else{
          $add_product_categories_id = $_GET['review_products_id_info'];
      }
      $select_value = "<option value='0'>".TEXT_SELECT_PRODUCT;      
      $review_select = "<select id='add_product_products_id' name=\"add_product_products_id\" onchange='change_hidden_select(this)' ".$str_disabled.">";
      $ProductOptions = $select_value;
             asort($ProductList[$add_product_categories_id]);
             foreach($ProductList[$add_product_categories_id] as $ProductID => $ProductName){
               $ProductName  =
                 tep_get_products_name($ProductID,$languages_id,$_GET['site_id'],true);
                 if($df_pid == $ProductID){
                 $ProductOptions .= "<option value='$ProductID' selected> $ProductName\n";
                 }else{
                 $ProductOptions .= "<option value='$ProductID'> $ProductName\n";
                 }
             }
             $ProductOptions = str_replace("value='$add_product_products_id'","value='$add_product_products_id' selected", $ProductOptions);
    $review_select_end = "</select>";
    if(!isset($df_pid)||$df_pid==0){
      $error_add_id = '<span id="p_error" style="color:#ff0000;">'.TEXT_CLEAR_SELECTION.'</span>'; 
    }
    $contents[]['text'] = array(
        array('text' => ENTRY_PRODUCT),
        array('text' => $review_select.$ProductOptions.$review_select_end.$error_add_id),
        array('text' => '<input type="hidden" id="hidden_select" name="hidden_select" value="'.$df_pid.'"><input type="hidden" name="hidden_products_name" value="'.$rInfo->products_id.'">'.'<input type="hidden" id="r_pid" value="'.$df_pid.'">')
    );


  $date_posted = '';
  if(isset($df_year)&&$df_year){
    $date_posted .= '<input type="hidden" id="r_year" value="'.$df_year.'">';
  }else{
    $date_posted .= '<input type="hidden" id="r_year" value="'.date('Y').'">';
    $df_year = date('Y');
  }
  $date_posted .= '<select name="year" onchange="set_ryear(this)"'.$str_disabled.'>'; 
  $now_year = date('Y');
  for ($i=0;$i<10;$i++) {
    $date_y = intval($now_year - $i);
    if($date_y == $df_year){
      $selected = 'selected';
    }else{
      $selected = '';
    }
    $date_posted .= '<option value="'.$date_y.'" '.$selected.'>'.$date_y.'</option>';
  }
  $date_posted .= '</select>';
  if(isset($df_m)&&$df_m){
    $date_posted .= '<input type="hidden" id="r_month" value="'.$df_m.'">';
  }else{
    $date_posted .= '<input type="hidden" id="r_month" value="'.date('m').'">';
    $df_m = date('m');
  }
  $date_posted .= '<select name="m" onchange="set_rmonth(this)"'.$str_disabled.'>';
  for ($i=01;$i<13;$i++) {
    if($i==$df_m){
      $selected = 'selected';
    }else{
      $selected = '';
    }
    $date_posted .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
  }
  $date_posted .= '</select>';
  if(isset($df_d)&&$df_d){
    $date_posted .= '<input type="hidden" id="r_day" value="'.$df_d.'">';
  }else{
    $date_posted .= '<input type="hidden" id="r_day" value="'.date('d').'">';
    $df_d = date('d');
  }
  $date_posted .= '<select name="d" onchange="set_rday(this)"'.$str_disabled.'>';
  for ($i=1;$i<31;$i++) {
    if($i==$df_d){
      $selected = 'selected'; 
    }else{
      $selected = '';
    }
    $date_posted .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $date_posted .= '</select>';
  if(isset($df_h)&&$df_h!=''){
    $date_posted .= '<input type="hidden" id="r_hour" value="'.$df_h.'">';
  }else{
    $date_posted .= '<input type="hidden" id="r_hour" value="'.date('H').'">';
    $df_h = date('H');
  }
  $date_posted .= '<select name="h" onchange="set_rhour(this)"'.$str_disabled.'>';
  for ($i=0;$i<24;$i++) {
    if($i==$df_h){
      $selected = 'selected';
    }else{
      $selected = '';
    }
    $date_posted .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
  }
  $date_posted .= '</select>:';
  if(isset($df_i)&&$df_i!=''){
    $date_posted .= '<input type="hidden" id="r_minute" value="'.$df_i.'">';
  }else{
    $date_posted .= '<input type="hidden" id="r_minute" value="'.date('i').'">';
    $df_i = date('i');
  }
  $date_posted .= '<select name="i" onchange="set_rminute(this)"'.$str_disabled.'>';
  for ($i=0;$i<60;$i++) {
    if($i==$df_i){
      $selected = 'selected';
    }else{
      $selected = '';
    } 
    $date_posted .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
  }
  $date_posted .= '</select>';

  $contents[]['text'] = array(
        array('text' => ENTRY_DATE),
        array('text' => $date_posted)
    );
  $contents[]['text'] = array(
        array('text' => ENTRY_FROM),
        array('text' => '<input type="text" id="customers_name" name="customers_name" value="'.tep_output_string_protected($rInfo->customers_name).'"'.$str_disabled.' />')
    );
    $review_radio = '';
    if(isset($df_rating)&&$df_rating){
      $review_radio = '<input type="hidden" value="'.$df_rating.'" id="r_rating">';
    }else{
      $review_radio = '<input type="hidden" value="5" id="r_rating">';
      $df_rating = 5;
    }
    for ($i=1; $i<=5; $i++) {
     if($i==$df_rating){
       $review_radio .= tep_draw_radio_field('reviews_rating', $i, true,'',' onclick="set_rating(this)"'.$str_disabled );
     }else{
       $review_radio .= tep_draw_radio_field('reviews_rating', $i, false,'',' onclick="set_rating(this)"'.$str_disabled );
     }
    }
    $contents[]['text'] = array(
        array('text' => ENTRY_RATING),
        array('text' =>  TEXT_BAD.$review_radio.TEXT_GOOD)
    );
    $contents[]['text'] = array(
        array('text' => TEXT_INFO_REVIEW_READ),
        array('text' =>  $rInfo->reviews_read)
    );
    if($rInfo->reviews_text_size == null){
       $reviews_text_query = tep_db_query(" select r.reviews_read, r.customers_name, r.site_id, length(rd.reviews_text) as reviews_text_size from " . TABLE_REVIEWS . " r, " .  TABLE_REVIEWS_DESCRIPTION . " rd where r.reviews_id = '" . $reviews['reviews_id'] . "' and r.reviews_id = rd.reviews_id");
        $reviews_text_row = tep_db_fetch_array($reviews_text_query);
        $reviews_average_query = tep_db_query(" select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . $reviews['products_id'] . "' ");
        $reviews_average_row = tep_db_fetch_array($reviews_average_query);
    $contents[]['text'] = array(
        array('text' => TEXT_INFO_REVIEW_SIZE),
        array('text' =>  $reviews_text_row['reviews_text_size'] . ' bytes')
    );
    $contents[]['text'] = array(
        array('text' => TEXT_INFO_PRODUCTS_AVERAGE_RATING),
        array('text' =>  number_format($reviews_average_row['average_rating'], 2) . '%')
    );
    }
  if($df_status){
    $str_rstatus = tep_draw_radio_field('reviews_status','1',true,'',' onclick="set_rstatus(this)" '.$str_disabled)
      .'&nbsp;'.TEXT_PRODUCT_AVAILABLE . '&nbsp;' .
      tep_draw_radio_field('reviews_status','0',false,'',' onclick="set_rstatus(this)" '.$str_disabled).
      '&nbsp;' .  TEXT_PRODUCT_NOT_AVAILABLE;
    $str_rstatus .= '<input type="hidden" value="1" id="r_status">';
  }else{
    $str_rstatus = tep_draw_radio_field('reviews_status','1',false,'',' onclick="set_rstatus(this)" '.$str_disabled)
      .'&nbsp;'.TEXT_PRODUCT_AVAILABLE . '&nbsp;' .
      tep_draw_radio_field('reviews_status','0',true,'',' onclick="set_rstatus(this)" '.$str_disabled).
      '&nbsp;' .  TEXT_PRODUCT_NOT_AVAILABLE;
    $str_rstatus .= '<input type="hidden" value="0" id="r_status">';
  }
  $contents[]['text'] = array(
    array('text' => TEXT_PRODUCTS_STATUS),
    array('text' => $str_rstatus)
  );
  $contents[]['text'] = array(
      array('text' => ENTRY_REVIEW),
      array('text' => tep_draw_textarea_field('reviews_text', 'soft', '60', '15', $rInfo->reviews_text, 'style="resize: vertical;" id="reviews_text" onkeypress="word_count(this)" onchange="word_count(this)"'.$str_disabled))
  );

//信息输出结束
  $contents[]['text'] = array(
    array('text' => ''),
    array('align' => 'right','params' => 'class="smallText"','text' => '<span style="float:left">'.REVIEWS_CHARACTER_TOTAL.'</span><span style="float:left"id="count_box"></span>')
  );
  $contents[]['text'] = array(
    array('text' => ''),
    array('params' => 'class="smallText"','text' => ENTRY_REVIEW_TEXT)
  );
 $contents[]['text'] = array(
   array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_USER_ADDED.((tep_not_null($rInfo->user_added))?$rInfo->user_added:TEXT_UNSET_DATA)), 
   array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_DATE_ADDED.((tep_not_null($rInfo->date_added))?$rInfo->date_added:TEXT_UNSET_DATA))
  );
  $contents[]['text'] = array(
    array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_USER_UPDATE.((tep_not_null($rInfo->user_update))?$rInfo->user_update:TEXT_UNSET_DATA)),
    array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_DATE_UPDATE.((tep_not_null($rInfo->last_modified))?$rInfo->last_modified:TEXT_UNSET_DATA))
  );


  if($ocertify->npermission == 15){
   $reviews_button[] =
     tep_html_element_button(IMAGE_SAVE,$str_disabled.'onclick="check_review_submit('.$_GET['rID'].','.$_GET['page'].')"').  '&nbsp;<a href="'.tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] .  '&rID=' .  $rInfo->reviews_id) .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').  (isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'').  '&action=deleteconfirm">'.tep_html_element_button(IMAGE_DELETE,$str_disabled).'</a>';
    if(!empty($reviews_button)){
        $buttons = array('align' => 'center', 'button' => $reviews_button);
     }
  }else{
   $reviews_button[] = tep_html_element_button(IMAGE_SAVE,$str_disabled.'onclick="check_review_submit('.$_GET['rID'].','.$_GET['page'].')"'.$str_disabled);
    if(!empty($reviews_button)){
        $buttons = array('align' => 'center', 'button' => $reviews_button);
     }

  }
  if($_GET['site_id'] == 0){
       $_GET['site_id'] = $reviews['site_id']; 
  }

//生产 表格
$reviews_form =  tep_draw_form('review', FILENAME_REVIEWS, 'page=' .  $_GET['page'] .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').'&rID=' .  $_GET['rID'] .  (isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):''). '&action=update', 'post' , 'onsubmit="return check_review()"');

 if(!isset($_GET['default_value'])||!$_GET['default_value']){
   unset($_SESSION['r_default_value']);
 }
  
$notice_box->get_form($reviews_form);
$notice_box->get_heading($heading);
$notice_box->get_contents($contents, $buttons);
$notice_box->get_eof(tep_eof_hidden());
echo $notice_box->show_notice();
}else if ($_GET['action'] == 'edit_latest_news'){
 include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_NEWS);
 include(DIR_FS_ADMIN.'classes/notice_box.php');
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$_SESSION['loginuid']."' limit 0,1");
while($userslist= tep_db_fetch_array($sites_id)){
     $site_permission = $userslist['site_permission']; 
}
if(isset($site_permission)) $site_arr=$site_permission;//权限判断
else $site_arr="";
$site_array = explode(',',$site_arr);
if(!in_array($site_id,$site_array)){
   $disable = 'disabled="disabled"';
}
 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 $get_news_id = $_GET['latest_news_id'];
 $site_id = $_GET['site_id'];
   if ( isset($_GET['latest_news_id']) ) { 
    $latest_news_query = tep_db_query("
          select news_id, 
                 headline,  
                 content, 
                 news_image, 
                 news_image_description,
                 site_id,
                 date_added,
                 latest_update_date,
                 update_editor,
                 author
          from " . TABLE_LATEST_NEWS . " 
          where news_id = '" . (int)$_GET['latest_news_id'] . "'");
      $latest_news = tep_db_fetch_array($latest_news_query);
    $nInfo = new objectInfo($latest_news);
    } else {
      $latest_news = array();
    }
     $latest_news_query_raw = ' select n.news_id, n.headline, n.date_added, n.author, n.update_editor, n.latest_update_date, n.content, n.status, n.news_image, n.news_image_description, n.isfirst, n.site_id from ' . TABLE_LATEST_NEWS . ' n where 1 ' . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and (n.site_id = '" .  intval($_GET['site_id']) . "') " : '') . ' order by date_added desc ';
     $latest_news_id_query = tep_db_query($latest_news_query_raw);
     while ($latest_news_id = tep_db_fetch_array($latest_news_id_query)) {
         $cid_array[] = $latest_news_id['news_id'];
     }

 foreach ($cid_array as $c_key => $c_value) {
           if ($_GET['latest_news_id'] == $c_value) {
            break;
          }
 }
 $page_str = '';
 if($get_news_id != -1){
 if ($c_key > 0) {
   $page_str .= '<a id="option_prev" onclick=\'show_latest_news("",'.$_GET['page'].','.$cid_array[$c_key-1].','.$_GET['site_id'].')\' href="javascript:void(0);" id="option_next">'.TEXT_CAMPAIGN_PREV.'</a>&nbsp;&nbsp;';
 }
 if ($c_key < (count($cid_array) - 1)) {
   $page_str .= '<a id="option_next" onclick=\'show_latest_news("",'.$_GET['page'].','.$cid_array[$c_key+1].','.$_GET['site_id'].')\' href="javascript:void(0);" id="option_next">'.TEXT_CAMPAIGN_NEXT.'</a>&nbsp;&nbsp;';
 }
 }
 $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
 $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
 $heading[] = array('align' => 'left', 'text' => isset($latest_news['headline'])?$latest_news['headline']:HEADING_TITLE);
 $heading[] = array('align' => 'right', 'text' => $page_str);
 $form_str = tep_draw_form('new_latest_news', FILENAME_NEWS, (isset($_GET['latest_news_id']) && $_GET['latest_news_id'] != '-1' ? ('latest_news_id=' . $_GET['latest_news_id'] . '&action=update_latest_news') : 'action=insert_latest_news').(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').(isset($_GET['page'])?('&page='.$_GET['page']):''), 'post', 'enctype="multipart/form-data"'); 

 $latest_news_contents[]['text'] = array(
      array('text' => '<input type="hidden" name="author" value="'.$_SESSION['user_name'].'"><input type="hidden" name="update_editor" value="'.$_SESSION['user_name'].'">')
 );
 if($site_id == 0){
      $site_id_name = 'all';
 }else{
      $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$site_id));
      $site_id_name = $site_name['romaji'];
 }
 if($get_news_id != -1){
      $site_romaji = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$latest_news['site_id']));
      if($latest_news['site_id'] == 0){
       $site_romaji['romaji'] = 'all';
      }
 }
 $latest_news_contents[]['text'] = array(
      array('text' => ENTRY_SITE),
      array('text' => (isset($_GET['latest_news_id']) && $_GET['latest_news_id'] && $latest_news?$site_romaji['romaji']:$site_id_name.'<input type="hidden" name="site_id" value="'.$site_id.'">'))
 );
 $latest_news_contents[]['text'] = array(
     array('text' => TEXT_LATEST_NEWS_HEADLINE),
     array('text' => tep_draw_input_field('headline', isset($latest_news['headline'])?$latest_news['headline']:'', 'id="headline" style="margin-left:0"'.$disable, false).'&nbsp;&nbsp;<font color="red" id="title_error"></font>')
     );
 $latest_news_contents[]['text'] = array(
     array('text' => TEXT_LATEST_NEWS_CONTENT),
     array('text' => tep_draw_textarea_field('content', 'soft', '70', '15',isset($latest_news['content'])?stripslashes($latest_news['content']):'','id="content" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="resize: vertical;"'.$disable))
     );
$latest_news_contents[]['text'] = array(
     array('text' => ''),
     array('text' => SHOP_NAME.'&nbsp;&nbsp;#STORE_NAME#')
     );
 $latest_news_contents[]['text'] = array(
     array('text' => TEXT_LATEST_NEWS_IMAGE),
     array('text' => tep_draw_file_field('news_image','',$disable) . '<br>' .  tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' .  (isset($latest_news['news_image'])?$latest_news['news_image']:'') .  tep_draw_hidden_field('news_image',isset($latest_news['news_image'])?$latest_news['news_image']:''))
     );
 $latest_news_contents[]['text'] = array(
     array('text' => TEXT_LATEST_NEWS_IMAGE_DESCRIPTION),
     array('text' => tep_draw_textarea_field('news_image_description', 'soft', '70', '7',isset($latest_news['news_image_description'])?stripslashes($latest_news['news_image_description']):'','onfocus="o_submit_single = false;" onblur="o_submit_single = true;" id="news_image_description" style="resize: vertical;"'.$disable))
     );
 $latest_news_contents[]['text'] = array(
     array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_USER_ADDED.((tep_not_null($latest_news['author']))?$latest_news['author']:TEXT_UNSET_DATA)), 
     array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_DATE_ADDED.((tep_not_null($latest_news['date_added']))?$latest_news['date_added']:TEXT_UNSET_DATA))
     );
 $latest_news_contents[]['text'] = array(
     array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_USER_UPDATE.((tep_not_null($latest_news['update_editor']))?$latest_news['update_editor']:TEXT_UNSET_DATA)),
     array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_DATE_UPDATE.((tep_not_null($latest_news['latest_update_date']))?date('Y-m-d H:i:s',$latest_news['latest_update_date']):TEXT_UNSET_DATA))
     );
if($ocertify->npermission == 15){
if(isset($disable) && $disable){
 isset($_GET['latest_news_id']) ? $cancel_button = tep_html_element_button(IMAGE_DELETE,$disable) : $cancel_button = '';
}else{
 isset($_GET['latest_news_id']) ? $cancel_button = '&nbsp;&nbsp;<a class="new_product_reset" href="' . tep_href_link(FILENAME_NEWS, 'action=delete_latest_news_confirm&latest_news_id='.  $_GET['latest_news_id'].(isset($_GET['site_id']) ?  '&site_id='.$_GET['site_id']:'').(isset($_GET['page']) ?  '&page='.$_GET['page']:'')) . '">' .  tep_html_element_button(IMAGE_DELETE) . '</a>' : $cancel_button = '';
}
}
 $button[] = tep_html_element_button(IMAGE_SAVE,'onclick="check_news_info()"'.$disable). $cancel_button;
if(!empty($button)){
       $buttons = array('align' => 'center', 'button' => $button);
 }
 $notice_box->get_form($form_str);
 $notice_box->get_heading($heading);
 $notice_box->get_contents($latest_news_contents, $buttons);
 $notice_box->get_eof(tep_eof_hidden());
 echo $notice_box->show_notice();
}else if ($_GET['action'] == 'edit_pw_manager'){
include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_PW_MANAGER);
include(DIR_FS_ADMIN.'classes/notice_box.php');
$notice_box = new notice_box('popup_order_title', 'popup_order_info');
$pw_id = $_GET['pw_id'];
$site_id = $_GET['site_id'];
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$_SESSION['loginuid']."' limit 0,1");
while($userslist= tep_db_fetch_array($sites_id)){
     $site_permission = $userslist['site_permission']; 
}
if(isset($site_permission)) $site_arr=$site_permission;//权限判断
else $site_arr="";
$site_array = explode(',',$site_arr);
if(!in_array($site_id,$site_array)){
   $disable = 'disabled="disabled"';
}
if($pw_id != -1){
      //add order 
      $order_str = ''; 
      if (!isset($HTTP_GET_VARS['sort'])||$HTTP_GET_VARS['sort']=='') {
        $next_str = '';
        $order_str = '`nextdate` asc, `title` asc'; 
      } else {
        if($HTTP_GET_VARS['sort'] == 'nextdate'){
          $next_str = 'nextdate as ';
          $order_str = 'nextdate '.$HTTP_GET_VARS['type']; 
        }else if($HTTP_GET_VARS['sort'] == 'operator'){
        $order_str = '`self` '.$HTTP_GET_VARS['type'].', `privilege` '.$HTTP_GET_VARS['type']; 
        }else{
        $order_str = '`'.$HTTP_GET_VARS['sort'].'` '.$HTTP_GET_VARS['type']; 
        }
      }
      
      if ($HTTP_GET_VARS['type'] == 'asc') {
        $type_str = 'desc'; 
      } else {
        $type_str = 'asc'; 
      }
   //add order end


   // sort sql 

    if(isset($site_id)&&$site_id){
     if(isset($_GET['search_type'])&&$_GET['search_type']&& isset($_GET['keywords'])&&$_GET['keywords']){
      if($_GET['search_type'] == 'operator'){
        $user_list = tep_get_user_list_by_username(trim($_GET['keywords']));
        if(isset($user_list)&&count($user_list)>=1){
          $user_list_str = "where (self in ('".implode("','",$user_list)."') ";
        }else{
          $user_list_str = "where (false ";
        }
        if(trim(strtolower($_GET['keywords'])) == 'staff'){
          $sort_where_permission = " or  privilege = '7')";
        }else if (trim(strtolower($_GET['keywords'])) == 'chief'){
          $sort_where_permission = " or  privilege = '10')";
        }else{
          $sort_where_permission = " or  false)";
        }
      $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."
                             nextdate
                             ,privilege,self,operator,user_added,created_at,
                             updated_at,onoff,update_user
                              from 
                             ".TABLE_IDPW." " 
                             .$user_list_str." "
                             .$sort_where_permission." 
                             and onoff = '1' 
                             and site_id = '".$site_id."' 
                             " .$sort_where . "
                             order by ".$order_str;
      }else{
      $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."
                             nextdate
                             ,privilege,self,operator,user_added,created_at,
                             updated_at,onoff,update_user
                              from 
                             ".TABLE_IDPW." 
                             where ".$_GET['search_type']." like '%".
                             $_GET['keywords']."%'
                             and onoff = '1' 
                             and site_id = '".$site_id."' 
                             " .$sort_where . "
                             order by ".$order_str;
      }
    }else{
    $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."                             nextdate
                             ,privilege,self,operator,user_added,created_at,
                             updated_at,onoff,update_user
                              from 
                             ".TABLE_IDPW." where site_id='".$site_id."'
                             and onoff = '1' 
                             " .$sort_where . "
                             order by ".$order_str;
    }
    }else if(isset($_GET['search_type'])&&$_GET['search_type']&& isset($_GET['keywords'])&&$_GET['keywords']){
      if($_GET['search_type'] == 'operator'){
        $user_list = tep_get_user_list_by_username(trim($_GET['keywords']));
        if(isset($user_list)&&count($user_list)>=1){
          $user_list_str = "where (self in ('".implode("','",$user_list)."') ";
        }else{
          $user_list_str = "where (false ";
        }
        if(trim(strtolower($_GET['keywords'])) == 'staff'){
          $sort_where_permission = " or  privilege = '7')";
        }else if (trim(strtolower($_GET['keywords'])) == 'chief'){
          $sort_where_permission = " or  privilege = '10')";
        }else{
          $sort_where_permission = " or  false)";
        }
      $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."
                             nextdate
                             ,privilege,self,operator,user_added,created_at,
                             updated_at,onoff,update_user
                              from 
                             ".TABLE_IDPW." " 
                             .$user_list_str." "
                             .$sort_where_permission." 
                             and onoff = '1' 
                             " .$sort_where . "
                             order by ".$order_str;
      }else{
      $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."
                             nextdate
                             ,privilege,self,operator,user_added,created_at,
                             updated_at,onoff,update_user
                             from
                             ".TABLE_IDPW." 
                             where ".$_GET['search_type']." like '%".
                             $_GET['keywords']."%'
                             and onoff = '1' 
                             " .$sort_where . "
                             order by ".$order_str;
      }
    }else{
    $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."
                             nextdate,
                             privilege,self,operator,user_added,created_at,
                             updated_at,onoff,update_user
                              from 
                             ".TABLE_IDPW." 
                             where onoff = '1' 
                             " .$sort_where . "
                             order by ".$order_str;
    }
        $pw_manager_query = tep_db_query($pw_manager_query_raw);
        $cid_array = array();
           $i=0;
           while($pw_manager_row = tep_db_fetch_array($pw_manager_query)){
           $i++;
                    $cid_array[] = $pw_manager_row['id']; 
               if (( (!@$_GET['pw_id']) || (@$_GET['pw_id'] == $pw_manager_row['id'])) && (!@$pwInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
                      $pwInfo = new objectInfo($pw_manager_row);
               }
          }
  foreach ($cid_array as $c_key => $c_value) {
    if ($_GET['pw_id'] == $c_value) {
      break; 
    }
  }
  $page_str = '';
  if ($c_key > 0) {
    $page_str .= '<a onclick="show_pw_manager(\'\','.$cid_array[$c_key-1].','.$_GET['page'].','.$site_id.')" href="javascript:void(0)" id="option_prev">'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
 }
 
  if ($c_key < (count($cid_array) - 1)) {
   $page_str .= '<a onclick="show_pw_manager(\'\','.$cid_array[$c_key+1].','.$_GET['page'].','.$site_id.')" href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'</a>&nbsp;&nbsp;'; 
  }   
      $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
      $heading = array();
      $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">'); 
      $heading[] = array('align' => 'left', 'text' => $pwInfo->title.'&nbsp;&nbsp;');
      $heading[] = array('align' => 'right', 'text' => $page_str);

      $contents[]['text'] = array(
           array('text' => TEXT_INFO_TITLE),
           array('text' => tep_draw_input_field('title',$pwInfo->title,'id="title"'.$disable))
          );
      $contents[]['text'] = array(
           array('text' => TEXT_INFO_PRIORITY),
           array('params' => 'calss="td_input"','text' => tep_draw_radio_field('priority',1,$pwInfo->priority == '1'?true:false,'',$disable).TEXT_PRIORITY_1.tep_draw_radio_field('priority',2,$pwInfo->priority == '2'?true:false,'',$disable).TEXT_PRIORITY_2.tep_draw_radio_field('priority',3,$pwInfo->priority == '3'?true:false,'',$disable).TEXT_PRIORITY_3)
          );
      if($pwInfo->site_id != 0){
      $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$pwInfo->site_id));
      $site_id_name = $site_name['romaji'];
      }else{
      $site_id_name = 'all';
      }
      $contents[]['text'] = array(
           array('text' => TEXT_INFO_SITE_ID),
           array('text' => '&nbsp;'.$site_id_name.'<input type="hidden" name="site_id" value="'.$pwInfo->site_id.'">')
          );
      $contents[]['text'] = array(
           array('text' => TEXT_INFO_URL),
           array('text' => tep_draw_input_field('url',$pwInfo->url,'id="url"'.$disable).tep_draw_hidden_field('old_url',$pwInfo->url,$disable))
          );
      $contents[]['text'] = array(
           array('text' => TEXT_INFO_LOGINURL),
           array('text' => tep_draw_input_field('loginurl',$pwInfo->loginurl,'id="loginurl"'.$disable).tep_draw_hidden_field('old_loginurl',$pwInfo->loginurl,$disable))
          );
      $contents[]['text'] = array(
           array('text' => TEXT_INFO_USERNAME),
           array('text' => tep_draw_input_field('username',$pwInfo->username,'id="username"'.$disable).tep_draw_hidden_field('old_username',$pwInfo->username,$disable))
          );
      $pwd_pattern = tep_get_pwd_pattern();
      $pwd_len = tep_get_pwd_len();
      $pwd_pattern_arr = explode(',',$pwd_pattern);
       $contents[]['text'] = array(
           array('text' => TEXT_PWD_LEN),
           array('text' => tep_draw_input_field('pwd_len',$pwd_len,'id="pwd_len" maxlength="2" size="2"'.$disable)."&nbsp;".  "<button type='button'".$disable." onclick=\"mk_pwd()\">" .  TEXT_BUTTON_MK_PWD."</button>".  tep_draw_input_field('password',$pwInfo->password,'id="password"'.$disable) .tep_draw_hidden_field('old_password',$pwInfo->password,$disable))
          );
      $contents[]['text'] = array(
           array('text' => TEXT_INFO_PASSWORD),
           array('text' => tep_draw_checkbox_field('pattern[]','english', in_array('english',$pwd_pattern_arr)?true:false,'',$disable).TEXT_LOWER_ENGLISH.  tep_draw_checkbox_field('pattern[]','ENGLISH', in_array('ENGLISH',$pwd_pattern_arr)?true:false,'',$disable).TEXT_POWER_ENGLISH. tep_draw_checkbox_field('pattern[]','NUMBER', in_array('NUMBER',$pwd_pattern_arr)?true:false,'',$disable).TEXT_NUMBER)
               );
      $contents[]['text'] = array(
           array('text' => TEXT_INFO_COMMENT),
           array('text' => tep_draw_textarea_field('comment', 'soft', '30', '5', $pwInfo->comment, 'style="resize: vertical;"onblur="o_submit_single = true;" onfocus="o_submit_single = false;" class="pw_textarea"'.$disable).tep_draw_hidden_field('old_comment',$pwInfo->comment,$disable))
          );
       $contents[]['text'] = array(
           array('text' => TEXT_INFO_MEMO),
           array('text' => tep_draw_textarea_field('memo', 'soft', '30', '5', $pwInfo->memo, 'style="resize: vertical;"class="pw_textarea" onblur="o_submit_single = true;" onfocus="o_submit_single = false;"'.$disable))
          );
        $contents[]['text'] = array(
           array('text' => TEXT_INFO_NEXTDATE),
           array('text' => '<div class="nextdate_info">' .  '<div class="yui3-skin-sam yui3-g">'.  tep_draw_input_field('nextdate',$pwInfo->nextdate, 'id="input_nextdate"'.$disable).  '<a href="javascript:void(0);" onclick="open_new_calendar();" class="dpicker"></a> <input type="hidden" name="toggle_open" value="0" id="toggle_open"> <div class="yui3-u" id="new_yui3"><div id="mycalendar"></div></div>' ."</div>")
          );
        $contents[]['text'] = array(
           array('text' => TEXT_INFO_PRIVILEGE),
           array('params' => 'calss="td_input"','text' => tep_draw_radio_field('privilege','15',$pwInfo->privilege==15?true:false,'','onclick="self_radio()" id="self" class="privilege"'.$disable).TEXT_SELF.  tep_draw_radio_field('privilege','7',$pwInfo->privilege==7?true:false,'','class="privilege" id="privilege_s" onclick="privilege_s_radio()"'.$disable).TEXT_PERMISSION_STAFF.  tep_draw_radio_field('privilege','10',$pwInfo->privilege==10?true:false,'','class="privilege" id="privilege_c" onclick="privilege_c_radio()"'.$disable).TEXT_PERMISSION_CHIEF.'&nbsp;&nbsp;'.TEXT_OPERATOR_INFO)
          );
      if($pwInfo->self!=''){
        $pw_select_display = 'block';
      }else{
        $pw_select_display = 'none';
      }
      if($pwInfo->self==''||$pwInfo->self==null){
        $selected_user = $ocertify->auth_user;
      }else{
        $selected_user = $pwInfo->self;
      }
       $contents[]['text'] = array( array('text' => '&nbsp;'), array('text' =>  '<div id="user_select" class="user_select" style="display:'.$pw_select_display.'" >'.tep_get_user_select($selected_user).'</div>'));
       $contents[]['text'] = array(
           array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_USER_ADDED.((tep_not_null($pwInfo->user_added))?$pwInfo->user_added:TEXT_UNSET_DATA)), 
           array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_DATE_ADDED.((tep_not_null($pwInfo->created_at))?$pwInfo->created_at:TEXT_UNSET_DATA))
         );
       $contents[]['text'] = array(
           array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_USER_UPDATE.((tep_not_null($pwInfo->update_user))?$pwInfo->update_user:TEXT_UNSET_DATA)),
           array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_DATE_UPDATE.((tep_not_null($pwInfo->updated_at))?$pwInfo->updated_at:TEXT_UNSET_DATA))
        );
    if($ocertify->npermission == 15){
     if(isset($disable) && $disable){
       $button_del = "<input style='font-size:12px' type='button' ".$disable." value='".TEXT_BUTTON_DELETE."'>";
     }else{
       $button_del = "<input type='button' style='font-size:12px' onclick=\"location.href='".  tep_href_link(FILENAME_PW_MANAGER, 'page=' . $_GET['page'] .  '&site_id='.$_GET['site_id'].'&pw_id=' .  $pwInfo->id .  '&action=deleteconfirm')  ."'\" value='".TEXT_BUTTON_DELETE."'>";
     }
    }

      $button[] = "<input ".$disable." style='font-size:12px'type='submit' value='".IMAGE_SAVE."'>" .  '&nbsp;'.$button_del."&nbsp;<input style='font-size:12px' type='button' ".$disable." onclick=\"location.href='".  tep_href_link(FILENAME_PW_MANAGER, 'log=pw_manager_log&pw_id='.$pwInfo->id.'&site_id='.$site_id) ."'\" value='".TEXT_BUTTON_HISTORY."'>";
      if(!empty($button)){
        $buttons = array('align' => 'center', 'button' => $button);
      }
      $form_str = tep_draw_form('pw_manager', FILENAME_PW_MANAGER, 'page=' . $_GET['page'] . '&site_id='.$site_id.'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&action=update&pw_id='.$pwInfo->id, 'post', 'enctype="multipart/form-data" onsubmit="return valdata(this)"');
      $notice_box->get_form($form_str);
      $notice_box->get_heading($heading);
      $notice_box->get_contents($contents, $buttons);
      $notice_box->get_eof(tep_eof_hidden());
      echo $notice_box->show_notice();
         
}else{      
      $heading = array();
      $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
      $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
      $heading[] = array('align' => 'left', 'text' => HEADING_TITLE.'&nbsp;&nbsp;');
      $heading[] = array('align' => 'right', 'text' => $page_str);
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_TITLE),
          array('text' => tep_draw_input_field('title','','id="title"'.$disable))
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_PRIORITY),
          array('params' => 'calss="td_input"','text' => tep_draw_radio_field('priority',1,true,'',$disable).TEXT_PRIORITY_1.tep_draw_radio_field('priority',2,false,'',$disable).TEXT_PRIORITY_2.tep_draw_radio_field('priority',3,false,'',$disable).TEXT_PRIORITY_3)
      );
if($site_id == 0){
      $site_id_name = 'all';
 }else{
      $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$_GET['site_id']));
      $site_id_name = $site_name['romaji'];
 }
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_SITE_ID),
          array('text' => $site_id_name.'<input type="hidden" name="site_id" value="'.$_GET['site_id'].'"><input type="hidden" name="user_added" value="'.$_SESSION['user_name'].'">')
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_URL),
          array('text' => tep_draw_input_field('url','','id="url"'.$disable))
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_LOGINURL),
          array('text' => tep_draw_input_field('loginurl','','id="loginurl"'.$disable))
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_USERNAME),
          array('text' => tep_draw_input_field('username','','id="username"'.$disable))
      );
      $pwd_pattern = tep_get_pwd_pattern();
      $pwd_len = tep_get_pwd_len();
      $pwd_pattern_arr = explode(',',$pwd_pattern);
      $contents[]['text'] = array(
          array('text' => TEXT_PWD_LEN),
          array('text' => tep_draw_input_field('pwd_len',$pwd_len,'id="pwd_len" maxlength="2" size="2"'.$disable)."&nbsp;".  "<button type='button'".$disable." onclick=\"mk_pwd()\">" .  TEXT_BUTTON_MK_PWD."</button>".  tep_draw_input_field('password',tep_get_new_random($pwd_pattern,$pwd_len),'id="password"'.$disable))
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_PASSWORD),
          array('text' => tep_draw_checkbox_field('pattern[]','english',
              in_array('english',$pwd_pattern_arr)?true:false,'',$disable).TEXT_LOWER_ENGLISH.
            tep_draw_checkbox_field('pattern[]','ENGLISH',
              in_array('ENGLISH',$pwd_pattern_arr)?true:false,'',$disable).TEXT_POWER_ENGLISH.
            tep_draw_checkbox_field('pattern[]','NUMBER',
              in_array('NUMBER',$pwd_pattern_arr)?true:false,'',$disable).TEXT_NUMBER)
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_COMMENT),
          array('text' => tep_draw_textarea_field('comment', 'soft', '30', '5', '','style="resize: vertical;"onblur="o_submit_single = true;" onfocus="o_submit_single = false;" class="pw_textarea"'.$disable))
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_MEMO),
          array('text' => tep_draw_textarea_field('memo', 'soft', '30', '5', '', 'style="resize: vertical;"onblur="o_submit_single = true;" onfocus="o_submit_single = false;" class="pw_textarea"'.$disable))
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_NEXTDATE),
          array('text' => '<div class="nextdate_info">' .  '<div class="yui3-skin-sam yui3-g">'.  tep_draw_input_field('nextdate','','id="input_nextdate"'.$disable).  '<a href="javascript:void(0);" onclick="open_new_calendar();" class="dpicker"></a> <input type="hidden" name="toggle_open" value="0" id="toggle_open"> <div class="yui3-u" id="new_yui3"><div id="mycalendar"></div></div>' ."</div>")
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_PRIVILEGE),
          array('params' => 'calss="td_input"','text' => tep_draw_radio_field('privilege','15',false,'','id="self" class="privilege" onclick="self_radio()"'.$disable).TEXT_SELF.  tep_draw_radio_field('privilege','7',true,'','class="privilege" id="privilege_s" onclick="privilege_s_radio()"'.$disable).TEXT_PERMISSION_STAFF.  tep_draw_radio_field('privilege','10',false,'','class="privilege" id="privilege_c" onclick="privilege_c_radio()"').TEXT_PERMISSION_CHIEF."&nbsp;&nbsp;".TEXT_OPERATOR_INFO)
      );
      $selected_user = $ocertify->auth_user;
      $contents[]['text'] = array( 
          array('text' => ''),
          array('text' => '<div id="user_select" class="user_select" style="display:none">'.  tep_get_user_select($selected_user) ."</div>")
      );
     $contents[]['text'] = array(
           array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_USER_ADDED.TEXT_UNSET_DATA), 
           array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_DATE_ADDED.TEXT_UNSET_DATA)
         );
     $contents[]['text'] = array(
           array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_USER_UPDATE.TEXT_UNSET_DATA),
           array('align' => 'left', 'params' => 'width="50%"', 'text' => TEXT_DATE_UPDATE.TEXT_UNSET_DATA)
      );
      $button[] = "<input ".$disable." style='font-size:12px' type='submit' value='".IMAGE_SAVE."'>" .  '&nbsp;' .  "<input style='font-size:12px' type='button' ".$disable."  onclick=\"location.href='".  tep_href_link(FILENAME_PW_MANAGER, 'page=' . $_GET['page']) ."'\" value='".TEXT_BUTTON_CLEAR."'>"; 
      if(!empty($button)){
       $buttons = array('align' => 'center', 'button' => $button);  
      }
      $form_str = tep_draw_form('pw_manager', FILENAME_PW_MANAGER, '&site_id='.$_GET['site_id'].'&page=' . $_GET['page'] . '&type='.$_GET['type'].'&sort='.$_GET['sort'].'&action=insert', 'post', 'enctype="multipart/form-data" onsubmit="return valdata()"');
      $notice_box->get_form($form_str);
      $notice_box->get_heading($heading);
      $notice_box->get_contents($contents, $buttons);
      $notice_box->get_eof(tep_eof_hidden());
      echo $notice_box->show_notice();
 }
}else if ($_GET['action'] == 'edit_pw_manager_log'){
include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_PW_MANAGER);
include(DIR_FS_ADMIN.'classes/notice_box.php');

$notice_box = new notice_box('popup_order_title', 'popup_order_info');
$pw_id = $_GET['pw_id'];
$site_id = $_GET['site_id'];
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$_SESSION['loginuid']."' limit 0,1");
while($userslist= tep_db_fetch_array($sites_id)){
     $site_permission = $userslist['site_permission']; 
}
if(isset($site_permission)) $site_arr=$site_permission;//权限判断
else $site_arr="";
$site_array = explode(',',$site_arr);
if(!in_array($site_id,$site_array)){
   $disable = 'disabled="disabled"';
}
$site_id = $_GET['site_id'];
  //add order 
$order_str = '';
if (!isset($HTTP_GET_VARS['sort'])||$HTTP_GET_VARS['sort']=='') {
  $next_str = '';
  $order_str = '`nextdate` desc, `title` asc';
} else {
  if($HTTP_GET_VARS['sort'] == 'nextdate'){
  $next_str = 'nextdate as ';
  $order_str = 'nextdate '.$HTTP_GET_VARS['type'];
}else{
  $next_str = 'nextdate as ';
  $order_str = '`'.$HTTP_GET_VARS['sort'].'` '.$HTTP_GET_VARS['type'];
}
}
 if ($HTTP_GET_VARS['type'] == 'asc') {
  $type_str = 'desc';
 } else {
  $type_str = 'asc';
 }
    if(isset($site_id)&&$site_id != 0){
    $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."nextdate,privilege,operator,created_at,
                             updated_at,onoff,update_user from
                             ".TABLE_IDPW_LOG." where site_id='".$site_id."'
                             order by ".$order_str;
    }else if(isset($_GET['search_type'])&&$_GET['search_type']&& isset($_GET['keywords'])&&$_GET['keywords']){
      $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."nextdate,privilege,operator,created_at,
                             updated_at,onoff,update_user from
                             ".TABLE_IDPW_LOG." 
                             where ".$_GET['search_type']." like '%".
                             $_GET['keywords']."%'
                             order by ".$order_str;
    }else if(isset($pwid)&&$pwid){
    $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."nextdate,privilege,operator,created_at,
                             updated_at,onoff,update_user from 
                             ".TABLE_IDPW_LOG." where idpw_id = '".$pwid."'
                             order by ".$order_str;

    }else{
    $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."nextdate,privilege,operator,created_at,
                             updated_at,onoff,update_user from
                             ".TABLE_IDPW_LOG." order by ".$order_str;
    }
   $pw_manager_query = tep_db_query($pw_manager_query_raw);
   while($pw_manager_row = tep_db_fetch_array($pw_manager_query)){
     $cid_array[] = $pw_manager_row['id'];
     if (( (!@$_GET['pw_l_id']) || (@$_GET['pw_l_id'] == $pw_manager_row['id'])) && (!@$pwInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
       $pwInfo = new objectInfo($pw_manager_row); 
     }
   }
  foreach ($cid_array as $c_key => $c_value) {
    if ($_GET['pw_l_id'] == $c_value) {
      break; 
    }
  }
  $page_str = '';
  if ($c_key > 0) {
    $page_str .= '<a onclick=\'show_pw_manager_log("",'.$_GET['pw_id'].','.$_GET['page'].','.$site_id.','.$cid_array[$c_key-1].')\' href="javascript:void(0);" id="option_prev">'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
 
  if ($c_key < (count($cid_array) - 1)) {
    $page_str .= '<a onclick=\'show_pw_manager_log("",'.$_GET['pw_id'].','.$_GET['page'].','.$site_id.','.$cid_array[$c_key+1].')\' href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'</a>&nbsp;&nbsp;'; 
  }
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading = array();
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => $pwInfo->title);
    $heading[] = array('align' => 'right', 'text' => $page_str);
    $contents[]['text'] = array(
         array('text' => TEXT_INFO_TITLE),
         array('text' => $pwInfo->title)
        );
    $contents[]['text'] = array(
         array('text' => TEXT_INFO_PRIORITY),
         array('text' => $pwInfo->priority)
        );
    $site_str = tep_get_site_info($pwInfo->site_id);
    $contents[]['text'] = array(
         array('text' => TEXT_INFO_SITE_ID),
         array('text' => $site_str['romaji'])
        );
     $contents[]['text'] = array(
         array('text' => TEXT_INFO_URL),
         array('text' => $pwInfo->url)
        );
     $contents[]['text'] = array(
         array('text' => TEXT_INFO_LOGINURL),
         array('text' => $pwInfo->loginurl)
        );
     $contents[]['text'] = array(
         array('text' => TEXT_USERNAME),
         array('text' => $pwInfo->username)
        );
     $contents[]['text'] = array(
         array('text' => TEXT_PASSWORD),
         array('text' => $pwInfo->password)
        );
      $contents[]['text'] = array(
         array('text' => TEXT_NEXTDATE),
         array('text' => $pwInfo->nextdate)
        );
      $contents[]['text'] = array(
         array('text' => TEXT_INFO_COMMENT),
         array('text' => $pwInfo->comment)
        );
      $contents[]['text'] = array(
         array('text' => TEXT_INFO_MEMO),
         array('text' => $pwInfo->memo)
        );
      $contents[]['text'] = array(
         array('text' => TEXT_INFO_CREATED),
         array('text' => $pwInfo->created_at)
        );
      $contents[]['text'] = array(
         array('text' => TEXT_INFO_UPDATED),
         array('text' => $pwInfo->updated_at)
        );
      $contents[]['text'] = array(
         array('text' => TEXT_INFO_OPRATER),
         array('text' => $pwInfo->update_user)
        );
      if($ocertify->npermission == 15){
        if(isset($disable) && $disable){
         $button[] = "<button type='button'".$disable." >" .  TEXT_BUTTON_DELETE."</button>";
        }else{
         $button[] = "<button type='button' onclick=\"location.href='".  tep_href_link(FILENAME_PW_MANAGER, 'action=deleteconfirm&log=pw_manager_log&pw_l_id='.$pwInfo->id.'&'.tep_get_all_get_params(array('pw_l_id','action','search_type','keywords'))) ."'\">" .  TEXT_BUTTON_DELETE."</button>";
        }
      }
      if(!empty($button)){
        $buttons = array('align' => 'center', 'button' => $button);
      }
      $notice_box->get_heading($heading);
      $notice_box->get_contents($contents, $buttons);
      $notice_box->get_eof(tep_eof_hidden());
      echo $notice_box->show_notice();
 
}
