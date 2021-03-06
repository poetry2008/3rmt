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
  $site_id = isset($_GET['s_site_id'])?$_GET['s_site_id']:0; 
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  //SITE ID
  if (isset($_GET['show_type'])&&$_GET['show_type'] == 'one'){
    if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
      $sql_site_where = "site_id = '".$_GET['site_id']."'"; 
    }else{
      $sql_site_where = "site_id=0";
    }
  }else{
    if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
      $sql_site_where = "site_id in (".str_replace('-', ',', $_GET['site_id']).")"; 
    } else {
      $show_list_str = tep_get_setting_site_info('categories.php');
      $sql_site_where = "site_id in (".$show_list_str.")"; 
    }
  }

  //SORT
  if(isset($_GET['order_sort']) && isset($_GET['order_type'])){
    if($_GET['order_type'] == 'asc'){

      $order_type = 'asc';
    }else{

      $order_type = 'desc';
    }

    $order_sort_str = '';
    switch($_GET['order_sort']){

      case 'site_romaji':
        $order_sort_str .= ' s.romaji '.$order_type.',';
      break;
      case 'name':
        $order_sort_str .= ' cd.categories_name '.$order_type.',';
      break;
      case 'status':
        $order_sort_str .= ' cd.categories_status '.$order_type.',';
      break;
      case 'time':
        $order_sort_str .= ' cd.last_modified '.$order_type.',';
      break;
    } 
  }
  if (isset($_GET['search']) && $_GET['search']) {
    $categories_query_raw = "
      select c.categories_id, 
            cd.site_id,
            cd.categories_name,
            c.sort_order
      from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd, (select id,romaji from ".TABLE_SITES." union select 0 id ,'ALL' romaji) s 
      where c.categories_id = cd.categories_id and cd.site_id = s.id 
        and cd.language_id = '" . $languages_id . "' 
        and cd.search_info like '%" . $_GET['search'] . "%' ";
    $categories_query_raw .= 'and '.$sql_site_where; 
    $categories_query_raw .= " order by ".$order_sort_str."c.sort_order, cd.categories_name";
  } else {
    $categories_query_raw = "
          select c.categories_id,
            cd.site_id,
            cd.categories_name,
            c.sort_order
          from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd, (select id,romaji from ".TABLE_SITES." union select 0 id ,'ALL' romaji) s 
          where
            c.parent_id = '".$current_category_id."' 
            and c.categories_id = cd.categories_id and cd.site_id = s.id 
            and cd.language_id='" . $languages_id ."' 
            and ".$sql_site_where." 
            order by ".$order_sort_str."c.sort_order, cd.categories_name
      ";
  }
  
  $cid_array = array();
  $site_array = array();
 
  $categories_tmp_raw = tep_db_query($categories_query_raw);
  while ($category_info = tep_db_fetch_array($categories_tmp_raw)) {
    $cid_array[] = $category_info['categories_id']; 
    $site_array[] = $category_info['site_id']; 
  }
  foreach ($cid_array as $c_key => $c_value) {
    if ($_GET['current_cid'] == $c_value && $site_array[$c_key] == $_GET['s_site_id']) {
      break; 
    }
  }
  
  $page_str = '';
  
  if ($c_key > 0) {
    $page_str .= '<a onclick="show_category_info(\''.$cid_array[$c_key-1].'\',\'\','.$site_array[$c_key-1].')" href="javascript:void(0);" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
 
  if ($c_key < (count($cid_array) - 1)) {
    $page_str .= '<a onclick="show_category_info(\''.$cid_array[$c_key+1].'\',\'\','.$site_array[$c_key+1].')" href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
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
      $button[] = '<a href="'.tep_href_link(FILENAME_PRODUCTS_MANUAL, 'cPath='.$_GET['cPath'].'&cID='.$_GET['current_cid'].'&action=show_categories_manual&site_id='.$site_id).($_GET['search'] != '' ? '&search='.$_GET['search'] : '').'">'.tep_html_element_button(IMAGE_MANUAL).'</a>'; 
    } 
    if (empty($site_id)) { 
      if ($ocertify->npermission >= 15) {
        $button[] = '<a
          href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE,
              'onclick="delete_category_info(\''.$_GET['current_cid'].'\',
          \'0\','.$site_id.');"').'</a>'; 
      }
    }
  }

  $button[] = '<a href="'.tep_href_link(FILENAME_CATEGORIES, 'cPath='.$_GET['cPath'].'&cID='.$_GET['current_cid'].'&action=edit_category'.'&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).'&s_site_id='.$site_id.(isset($_GET['search'])?'&search='.$_GET['search']:'').(isset($_GET['show_type'])?'&show_type='.$_GET['show_type']:'')).'">'.tep_html_element_button(IMAGE_DETAILS, '').'</a>'; 
  
  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }
  
  $category_info_row = array();

  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'colspan="2"', 'text' => TEXT_SUBCATEGORIES.'&nbsp;'.tep_childs_in_category_count($_GET['current_cid'])), 
      );
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'colspan="2"', 'text' => TEXT_PRODUCTS.'&nbsp;'.tep_products_in_category_count($_GET['current_cid'])), 
      );
  
  $category_info_row[]['text'] = array(
        array('align' => 'left','params' => 'width="30%"', 'text' => TEXT_USER_ADDED.'&nbsp;'.((tep_not_null($category_info_res['user_added'])?$category_info_res['user_added']:TEXT_UNSET_DATA))), 
        array('align' => 'left','params' => 'width="70%"','text' => TEXT_DATE_ADDED.'&nbsp;'.((tep_not_null(tep_datetime_short($category_info_res['date_added'])))?tep_datetime_short($category_info_res['date_added']):TEXT_UNSET_DATA)), 
      );
  
  $category_info_row[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_USER_UPDATE.'&nbsp;'.((tep_not_null($category_info_res['user_last_modified'])?$category_info_res['user_last_modified']:TEXT_UNSET_DATA))), 
        array('align' => 'left', 'text' => TEXT_LAST_MODIFIED.'&nbsp;'.((tep_not_null(tep_datetime_short($category_info_res['last_modified'])))?tep_datetime_short($category_info_res['last_modified']):TEXT_UNSET_DATA)), 
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

  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_MOVE, 'onclick="toggle_category_form(\''.$ocertify->npermission.'\', \'5\')"').'</a>';
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
  $site_id = isset($_GET['s_site_id'])?$_GET['s_site_id']:0; 
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
  $category_info_raw = tep_db_query("select cd.categories_name from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = '".$_GET['current_cid']."' and c.categories_id = cd.categories_id and (cd.site_id = '0' or cd.site_id = '".$site_id."') order by cd.site_id desc limit 1");
  $category_info_res = tep_db_fetch_array($category_info_raw); 
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_INFO_HEADING_DELETE_CATEGORY);
  $heading[] = array('align' => 'right', 'text' => '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>');

  $buttons = array();

  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="toggle_category_form(\''.$ocertify->npermission.'\', \'0\')"').'</a>';
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="hidden_info_box();"').'</a>';

  $buttons = array('align' => 'center', 'button' => $button); 
  
  $delete_category_info = array();

  $delete_category_info[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_DELETE_CATEGORY_INTRO), 
      );
  
  $delete_category_info[]['text'] = array(
        array('align' => 'left', 'text' => $category_info_res['categories_name'].(empty($site_id) ? tep_draw_hidden_field('categories_id', $_GET['current_cid']) : '')), 
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
    $form_str = tep_draw_form('delete_category', FILENAME_CATEGORIES, 'action=delete_category_confirm&cPath='.$_GET['cPath'].'&site_id='.$_GET['site_id'].(isset($_GET['page'])?'&page='.$_GET['page']:'').($_GET['search']?'&search='.$_GET['search']:'').(isset($_GET['s_site_id'])
            ? '&s_site_id='.$_GET['s_site_id'] : '').(isset($_GET['show_type']) ? '&show_type='.$_GET['show_type'] : ''));
  } else {
    if (isset($_GET['rdirect'])) {
      $form_str = tep_draw_form('delete_category', FILENAME_CATEGORIES,
          'action=delete_category_description_confirm&cID='.$_GET['current_cid'].'&cPath='.$_GET['cPath'].'&site_id='.$_GET['site_id'].'&rdirect=all'.(isset($_GET['page'])?'&page='.$_GET['page']:'').($_GET['search']?'&search='.$_GET['search']:'').(isset($_GET['s_site_id'])
            ? '&s_site_id='.$_GET['s_site_id'] : '').(isset($_GET['show_type']) ? '&show_type='.$_GET['show_type'] : ''));
    } else {
      $form_str = tep_draw_form('delete_category', FILENAME_CATEGORIES,
          'action=delete_category_description_confirm&cID='.$_GET['current_cid'].'&cPath='.$_GET['cPath'].'&site_id='.$_GET['site_id'].(isset($_GET['page'])?'&page='.$_GET['page']:'').($_GET['search']?'&search='.$_GET['search']:'').(isset($_GET['s_site_id'])
            ? '&s_site_id='.$_GET['s_site_id'] : '').(isset($_GET['show_type']) ? '&show_type='.$_GET['show_type'] : ''));
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
} else if ($_GET['action'] == 'update_quantity') {
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
        array('align' => 'left', 'text' => TEXT_POPUP_WINDOW_SHOW), 
        array('align' => 'left', 'text' => $_POST['origin_num'].
          '<input type="hidden" id="is_radices" value="1">'), 
      );
  
  $update_info_array[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_POPUP_WINDOW_EDIT), 
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
  $heading[] = array('align' => 'left', 'text' => TABLE_HEADING_CATEGORIES_PRODUCT_NOW_PRICE);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  $buttons = array();
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CONFIRM, 'id="new_price_button" onclick="set_new_price(\''.$_POST['pid'].'\', \''.$_POST['cnt_num'].'\')"').'</a>'; 
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
  $site_id = isset($_GET['s_site_id'])?$_GET['s_site_id']:0; 
  $isstaff = true;;
  if ($ocertify->npermission >= 10) {
    $isstaff = false;
  } 
  $pInfo = tep_get_pinfo_by_pid($_GET['pID'], $site_id);
  $cPath = $_GET['cPath'];
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  //SITE ID
  if (isset($_GET['show_type'])&&$_GET['show_type'] == 'one'){
    if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
      $sql_site_where = "site_id = '".$_GET['site_id']."'"; 
    }else{
      $sql_site_where = "site_id=0";
    }
  }else{
    if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
      $sql_site_where = "site_id in (".str_replace('-', ',', $_GET['site_id']).")"; 
    } else {
      $show_list_str = tep_get_setting_site_info('categories.php');
      $sql_site_where = "site_id in (".$show_list_str.")"; 
    }
  }

  //SORT
  if(isset($_GET['order_sort']) && isset($_GET['order_type'])){
    if($_GET['order_type'] == 'asc'){

      $order_type = 'asc';
    }else{

      $order_type = 'desc';
    }

    $order_sort_str = '';
    switch($_GET['order_sort']){

      case 'site_romaji':
         $order_sort_str .= ' s.romaji '.$order_type.',';
      break;
      case 'name':
         $order_sort_str .= ' pd.products_name '.$order_type.',';
      break;
      case 'status':
         $order_sort_str .= ' pd.products_status '.$order_type.',';
      break;
      case 'time':
         $order_sort_str .= ' pd.products_last_modified '.$order_type.',';
      break;
      case 'price':
         $order_sort_str .= ' p.products_price '.$order_type.',';
      break;
    } 
  }
  
  if (isset($_GET['search']) && $_GET['search']) {
    $products_query_raw = "
      select p.products_id,pd.site_id 
      from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c,(select id,romaji from ".TABLE_SITES." union select 0 id ,'ALL' romaji) s 
      where p.products_id = pd.products_id 
        and pd.language_id = '" . $languages_id . "' 
        and p.products_id = p2c.products_id 
        and pd.site_id = s.id 
        and pd.search_info like '%" . $_GET['search'] . "%' ";
    $products_query_raw .= 'and '.$sql_site_where; 
    $products_query_raw .= " order by ".$order_sort_str."p.sort_order,pd.products_name, p.products_id";
  } else {
    $products_query_raw = "
      select p.products_id, 
             pd.products_name, 
             pd.site_id, 
             p.sort_order, 
             p.products_small_sum 
      from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c,(select id,romaji from ".TABLE_SITES." union select 0 id ,'ALL' romaji) s 
      where p.products_id = pd.products_id 
        and pd.language_id = '" . $languages_id . "' 
        and p.products_id = p2c.products_id 
        and pd.site_id = s.id 
        and p2c.categories_id = '" . $current_category_id . "' 
        and ".$sql_site_where." 
        order by ".$order_sort_str."p.sort_order, pd.products_name, p.products_id";
  }
  $pid_arr = array();
  $site_arr = array();
  $products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_PRODUCTS_ADMIN, $products_query_raw, $products_query_numrows);
  $products_query = tep_db_query($products_query_raw);
  while($products_row = tep_db_fetch_array($products_query)){
    $pid_arr[] = $products_row['products_id'];
    $site_arr[] = $products_row['site_id'];
  }
  foreach($pid_arr as $p_key => $p_value){
    if($_GET['pID'] == $p_value && $site_arr[$p_key] == $_GET['s_site_id']){
      break;
    }
  }
  $page_str = '';

  if($p_key > 0){ 
    $page_str .= '<a onclick="show_product_info(\''.$pid_arr[$p_key - 1].'\', \'\','.$site_arr[$p_key - 1].');" href="javascript:void(0);" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp';
  }
  
  if($p_key < count($pid_arr)-1){
    $page_str .= '<a onclick="show_product_info(\''.$pid_arr[$p_key + 1].'\', \'\','.$site_arr[$p_key + 1].');" href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp';
  } 
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';

  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_PRODUCT_INFO);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  $buttons = array();
 
  if (!$isstaff) {
    if (empty($site_id)) {
      $button[] = '<a href="' .  tep_href_link(FILENAME_PRODUCTS_MANUAL, 'cPath=' .  $cPath . '&pID=' .  $pInfo->products_id .  '&action=show_products_manual'.  '&site_id='.  $site_id.  '&page='.$_GET['page']) .($_GET['search'] != '' ? '&search='.$_GET['search'] : '').'">'.tep_html_element_button(IMAGE_MANUAL).'</a>';
    } 
      if($site_id == 0){
         $show_site_list_array = array();
         $site_list_info_query = tep_db_query("select * from ".TABLE_SITES);
         while ($site_list_info = tep_db_fetch_array($site_list_info_query)) {
           $show_site_list_array[] = $site_list_info['id'];
         }
         $all_site_id = implode('-',$show_site_list_array);
      }else{
         $all_site_id = $site_id;
      }
      $button[] = '<a href="' . tep_href_link(FILENAME_REVIEWS, 'product_name=' .  $pInfo->products_name . '&site_id='.$all_site_id) .  '">'.tep_html_element_button(IMAGE_REVIEWS).'</a>';
    if (empty($site_id)) {
      $button[] = '<input class="element_button" type="button" value="'.IMAGE_MOVE.'" onclick="show_product_move(\''.$pInfo->products_id.'\')">';
      $button[] = '<input class="element_button" type="button" value="'.IMAGE_COPY.'" onclick="show_product_copy(\'copy\',\''.$pInfo->products_id.'\')">';
      $button[] = '<input class="element_button" type="button" value="'.IMAGE_LINK.'" onclick="show_product_copy(\'link\',\''.$pInfo->products_id.'\')">';
    }
    if ($ocertify->npermission >= 15) {
      if(empty($site_id)){ 
        $button[] = '<input class="element_button" type="button" value="'.IMAGE_DELETE.'" onclick="show_product_delete(\''.$pInfo->products_id.'\')">';
      }
    }
  } else {
    $button[] = '<a href="' . tep_href_link(FILENAME_REVIEWS, 'product_name=' . $pInfo->products_name . '&site_id='.(int)$site_id) .  '">'.tep_html_element_button(IMAGE_REVIEWS).'</a>';
  }
  if (empty($_GET['s_site_id'])) {
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'id="button_save_product" onclick="check_single_product_price(\''.$pInfo->products_id.'\', \''.$ocertify->npermission.'\', \'3\')"').'</a>'; 
  }
  
  $buttons = array('align' => 'center', 'type' => 'div', 'id' => 'order_del', 'params' => 'class="main"' , 'button' => $button);

  $product_info_params = array('width' => '95%', 'cellpadding' => '0', 'cellspacing' => '0', 'border' => '0', 'parameters' => 'style="margin-bottom:10px;"');
  
  $product_info_array = array();
  
  if ($pInfo->products_bflag == 1) {
    $product_tmp_price = array(
        'price' => tep_get_price($pInfo->products_price,
          $pInfo->products_price_offset, $pInfo->products_small_sum,
          $pInfo->products_bflag,$pInfo->price_type),
        'sprice' => tep_get_special_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum)
        );
  } else {
    $product_tmp_price = array(
        'price' => tep_get_price($pInfo->products_price,
          $pInfo->products_price_offset,
          $pInfo->products_small_sum,$pInfo->price_type),
        'sprice' => tep_get_special_price($pInfo->products_price, $pInfo->products_price_offset, $pInfo->products_small_sum)
        );
  }
  
  $inventory = array();
  $max_inventory_array = explode('|||',$pInfo->max_inventory);
  $min_inventory_array = explode('|||',$pInfo->min_inventory);
  if(empty($site_id)){

    $max_inventory_num_1 = tep_inventory_operations($max_inventory_array[0],$pInfo->products_id,$site_id);
    $max_inventory_num_2 = tep_inventory_operations($max_inventory_array[1],$pInfo->products_id,$site_id);
    $min_inventory_num_1 = tep_inventory_operations($min_inventory_array[0],$pInfo->products_id,$site_id);
    $min_inventory_num_2 = tep_inventory_operations($min_inventory_array[1],$pInfo->products_id,$site_id);

    if($max_inventory_array[2] == 'min'){

      $max_inventory_num = $max_inventory_num_1 < $max_inventory_num_2 ? $max_inventory_num_1 : $max_inventory_num_2;
    }else{
      $max_inventory_num = $max_inventory_num_1 > $max_inventory_num_2 ? $max_inventory_num_1 : $max_inventory_num_2;
    }
    
    if($min_inventory_array[2] == 'min'){
 
      $min_inventory_num = $min_inventory_num_1 < $min_inventory_num_2 ? $min_inventory_num_1 : $min_inventory_num_2;
    }else{
      $min_inventory_num = $min_inventory_num_1 > $min_inventory_num_2 ? $min_inventory_num_1 : $min_inventory_num_2;
    }
  }
  $arr_td_title = array();
  $arr_td_product = array();
  $arr_td_relate = array();
  $arr_td_title[] = TEXT_PRODUCTS_NAME_TEXT;
  
  $arr_td_title[] = TEXT_PRODUCTS_PRICE_INFO;
  
  $arr_td_title[] = TEXT_PRODUCT_ADDORSUB_VALUE;
  
  $arr_td_title[] = CATEGORY_AVERAGE_PRICE;
  
  $arr_td_title[] = TEXT_PRODUCT_ORDER_HISTORY;
  
  $arr_td_title[] = TEXT_PRODUCTS_QUANTITY_TEXT;
  
  $arr_td_title[] = TEXT_PRODUCTS_REAL_QUANTITY_TEXT;
  
  $arr_td_title[] = TABLE_HEADING_CATEGORIES_PRODUCT_VIRTUAL_STORE;
  
  //这里预留插入在库变数
  
  if(empty($site_id)) {
    $arr_td_title[] = TEXT_MAX;
    $arr_td_title[] = TEXT_MIN;
  }
  $arr_td_title[] = TEXT_PRODUCTS_AVERAGE_RATING;
  $arr_td_title[] = TEXT_PRODUCT_RATE;
  //商品履历 数字
  
  
  $arr_td_product[] = $pInfo->products_name;
  
  $arr_td_product[] = (($product_tmp_price['sprice'])?'<s>'.$currencies->format($product_tmp_price['price']).'</s>&nbsp;&nbsp;':'').(empty($_GET['s_site_id'])?number_format((int)$pInfo->products_price, 0, '.', ',').  '&rarr;':'') .((!empty($_GET['s_site_id']))?number_format(abs($pInfo->products_price)?abs($pInfo->products_price):'0',0,'.',','):tep_draw_input_field('products_price', number_format(abs($pInfo->products_price)?abs($pInfo->products_price):'0',0,'.',''),'onkeyup="clearNoNum(this)" id="pp" size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;"')) . '&nbsp;' . CATEGORY_MONEY_UNIT_TEXT;
  
  $arr_td_product[] = number_format($pInfo->products_price_offset).'&nbsp;&nbsp;&nbsp;&nbsp;';
  
  $product_td_avg_price = '';
  if (!$pInfo->products_bflag && $pInfo->relate_products_id) {
    $product_td_avg_price = @display_price(tep_new_get_avg_by_pid($pInfo)).'&nbsp;'.CATEGORY_MONEY_UNIT_TEXT;
  }
  //判断汇率 是否是空 0 或者1 如果不是 显示两个商品数量
  if (isset($pInfo->products_exchange_rate)) {
    $radices = (int)$pInfo->products_exchange_rate;
  } else {
    $radices = 1;
  }
  $arr_td_product[] = $product_td_avg_price;
  $product_sub_date = get_configuration_by_site_id('DB_CALC_PRICE_HISTORY_DATE', 0);
  $product_row_count = tep_get_relate_product_history_sum($pInfo->products_id, $product_sub_date, 0,$radices);
  $arr_td_product[] = sprintf(TEXT_PRODUCT_ORDER_HISTORY_INFO,$product_sub_date,number_format($product_row_count));
  if($radices!=''&&$radices!=1&&$radices!=0){
    $product_td_real_quantity = (empty($_GET['s_site_id'])?number_format((int)($pInfo->products_real_quantity/$radices)).  '&rarr;':'') .  ((!empty($_GET['s_site_id']))?number_format(tep_new_get_quantity($pInfo)):tep_draw_input_field('products_quantity', strval(tep_new_get_quantity($pInfo)),'size="8" id="product_qt" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);rsync_num(this);"')) . '&nbsp;' .CATEGORY_GE_UNIT_TEXT;
    $product_td_quantity = (empty($_GET['s_site_id'])?number_format($pInfo->products_real_quantity).  '&rarr;':'') .  ((!empty($_GET['s_site_id']))?number_format($pInfo->products_real_quantity):tep_draw_input_field('products_real_quantity', $pInfo->products_real_quantity,'size="8" id="product_qtr" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);rsync_num(this);"')).'&nbsp;&nbsp;&nbsp;&nbsp;';
  }else{
    $product_td_real_quantity = (empty($_GET['s_site_id'])?number_format($pInfo->products_real_quantity).  '&rarr;':'') .  ((!empty($_GET['s_site_id']))?number_format($pInfo->products_real_quantity):tep_draw_input_field('products_real_quantity', $pInfo->products_real_quantity,'size="8" id="qt" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"')) . '&nbsp;' .CATEGORY_GE_UNIT_TEXT;
    $product_td_quantity = (empty($_GET['s_site_id'])?number_format($pInfo->products_real_quantity).  '&rarr;':'') .((!empty($_GET['s_site_id']))?number_format($pInfo->products_real_quantity):tep_draw_input_field('products_real_quantity', $pInfo->products_real_quantity,'size="8" id="qt" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"')).'&nbsp;&nbsp;&nbsp;&nbsp;';
  }
  $arr_td_product[] = $product_td_real_quantity.'<input id="product_radices" type="hidden" value="'.$radices.'">';
  $arr_td_product[] = $product_td_quantity;
  $arr_td_product[] = (empty($_GET['s_site_id'])?number_format($pInfo->products_virtual_quantity) .'&rarr;':'').((!empty($_GET['s_site_id']))?number_format($pInfo->products_virtual_quantity):tep_draw_input_field('products_virtual_quantity', $pInfo->products_virtual_quantity,' size="8" id="qt" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"')) . '&nbsp;'.CATEGORY_GE_UNIT_TEXT;

  
  if(empty($site_id)) {
    $max_inventory_num = $max_inventory_num < 0 ? 0 : $max_inventory_num;
    $min_inventory_num = $min_inventory_num < 0 ? 0 : $min_inventory_num;
    $arr_td_product[] = (empty($_GET['s_site_id'])?number_format($max_inventory_num):'').'&nbsp'.CATEGORY_GE_UNIT_TEXT;
    $arr_td_product[] = (empty($_GET['s_site_id'])?number_format($min_inventory_num):'').'&nbsp'.CATEGORY_GE_UNIT_TEXT;
  }
  $inventory['max'] = $max_inventory_num;
  $inventory['min'] = $min_inventory_num;

  $arr_td_product[] = number_format($pInfo->average_rating,2).'%'.((!empty($site_id) || $isstaff)?tep_draw_hidden_field('inventory_max',$inventory['max']).tep_draw_hidden_field('inventory_min',$inventory['min']):'').'&nbsp;&nbsp;&nbsp;&nbsp;';
  if($radices!=''){
    $arr_td_product[] = sprintf(TEXT_RADICES_PRODUCT_INFO, number_format($pInfo->products_exchange_rate)).'&nbsp;&nbsp;&nbsp;&nbsp;';
  }else{
    $arr_td_product[] = $pInfo->products_exchange_rate;
  }
  
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
    
    if ($relate_pInfo->products_bflag == 1) {
      $relate_product_tmp_price = array(
        'price' => tep_get_price($relate_pInfo->products_price,
          $relate_pInfo->products_price_offset, $relate_pInfo->products_small_sum,
          $relate_pInfo->products_bflag,$relate_pInfo->price_type),
        'sprice' => tep_get_special_price($relate_pInfo->products_price, $relate_pInfo->products_price_offset, $relate_pInfo->products_small_sum)
        );
    } else {
      $relate_product_tmp_price = array(
          'price' => tep_get_price($relate_pInfo->products_price,
            $relate_pInfo->products_price_offset,
            $relate_pInfo->products_small_sum,$relate_pInfo->price_type),
          'sprice' => tep_get_special_price($relate_pInfo->products_price, $relate_pInfo->products_price_offset, $relate_pInfo->products_small_sum)
          );
    }
    
    $inventory = array();
    $max_inventory_array = explode('|||',$relate_pInfo->max_inventory);
    $min_inventory_array = explode('|||',$relate_pInfo->min_inventory);
    if(empty($site_id)){

      $max_inventory_num_1 = tep_inventory_operations($max_inventory_array[0],$relate_pInfo->products_id,$site_id);
      $max_inventory_num_2 = tep_inventory_operations($max_inventory_array[1],$relate_pInfo->products_id,$site_id);
      $min_inventory_num_1 = tep_inventory_operations($min_inventory_array[0],$relate_pInfo->products_id,$site_id);
      $min_inventory_num_2 = tep_inventory_operations($min_inventory_array[1],$relate_pInfo->products_id,$site_id);
      if($max_inventory_array[2] == 'min'){

        $max_inventory_num = $max_inventory_num_1 < $max_inventory_num_2 ? $max_inventory_num_1 : $max_inventory_num_2;
      }else{
        $max_inventory_num = $max_inventory_num_1 > $max_inventory_num_2 ? $max_inventory_num_1 : $max_inventory_num_2;
      }
    
      if($min_inventory_array[2] == 'min'){

        $min_inventory_num = $min_inventory_num_1 < $min_inventory_num_2 ? $min_inventory_num_1 : $min_inventory_num_2;
      }else{
        $min_inventory_num = $min_inventory_num_1 > $min_inventory_num_2 ? $min_inventory_num_1 : $min_inventory_num_2;
      }
    } 
    
    $arr_td_relate[] = $relate_pInfo->products_name;
    
    $arr_td_relate[] = tep_draw_hidden_field('relate_products_id', $relate_pInfo->products_id).(($relate_product_tmp_price['sprice'])?'<s>'.$currencies->format($relate_product_tmp_price['price']).'</s>&nbsp;&nbsp;':'').(empty($_GET['s_site_id'])?number_format((int)$relate_pInfo->products_price,0,'.',',').'&rarr;':'').((!empty($_GET['s_site_id']))?number_format(abs($relate_pInfo->products_price)?abs($relate_pInfo->products_price):'0',0,'.',','):tep_draw_input_field('relate_products_price', number_format(abs($relate_pInfo->products_price)?abs($relate_pInfo->products_price):'0',0,'.',''),'onkeyup="clearNoNum(this)" size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" id="r_price"')) . '&nbsp;' .  CATEGORY_MONEY_UNIT_TEXT;
    
    $arr_td_relate[] =  number_format($relate_pInfo->products_price_offset).'&nbsp;&nbsp;&nbsp;&nbsp;';
    
    $relate_td_avg_price = '';
    if (!$relate_pInfo->products_bflag && $relate_pInfo->relate_products_id) {
      $relate_td_avg_price = @display_price(tep_new_get_avg_by_pid($relate_pInfo)).'&nbsp;'.CATEGORY_MONEY_UNIT_TEXT;
    }

  if (isset($relate_pInfo->products_exchange_rate)) {
    $relate_radices = (int)$relate_pInfo->products_exchange_rate;
  } else {
    $relate_radices = 1;
  }
    $arr_td_relate[] = $relate_td_avg_price;
    $relate_sub_date = get_configuration_by_site_id('DB_CALC_PRICE_HISTORY_DATE', 0);
    $relate_row_count = tep_get_relate_product_history_sum($pInfo->relate_products_id, $relate_sub_date, 0,$relate_radices);
    $arr_td_relate[] = sprintf(TEXT_PRODUCT_ORDER_HISTORY_INFO,$relate_sub_date,number_format($relate_row_count));
  
  if($relate_radices!=''&&$relate_radices!=1&&$relate_radices!=0){
    $relate_td_real_quantity = (empty($_GET['s_site_id'])?number_format((int)($relate_pInfo->products_real_quantity/$relate_radices)).'&rarr;':'').((!empty($_GET['s_site_id']))?number_format(tep_new_get_quantity($relate_pInfo)):tep_draw_input_field('relate_products_quantity', strval(tep_new_get_quantity($relate_pInfo)),'size="8" id="relate_qt" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);rsync_num(this);"')) . '&nbsp;' .CATEGORY_GE_UNIT_TEXT;
    $relate_td_quantity = (empty($_GET['s_site_id'])?number_format($relate_pInfo->products_real_quantity).'&rarr;':'').((!empty($_GET['s_site_id']))?number_format($relate_pInfo->products_real_quantity):tep_draw_input_field('relate_products_real_quantity', $relate_pInfo->products_real_quantity,'size="8" id="relate_qtr" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);rsync_num(this);"')).'&nbsp;&nbsp;&nbsp;&nbsp;';
  }else{
    $relate_td_real_quantity = (empty($_GET['s_site_id'])?number_format($relate_pInfo->products_real_quantity).'&rarr;':'').((!empty($_GET['s_site_id']))?number_format($relate_pInfo->products_real_quantity):tep_draw_input_field('relate_products_real_quantity', $relate_pInfo->products_real_quantity,'size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"')) . '&nbsp;' .CATEGORY_GE_UNIT_TEXT;
    $relate_td_quantity = (empty($_GET['s_site_id'])?number_format($relate_pInfo->products_real_quantity).'&rarr;':'').((!empty($_GET['s_site_id']))?number_format($relate_pInfo->products_real_quantity):tep_draw_input_field('relate_products_real_quantity', $relate_pInfo->products_real_quantity,'size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"')).'&nbsp;&nbsp;&nbsp;&nbsp;';
  }
    $arr_td_relate[] = $relate_td_real_quantity.'<input id="relate_radices" type="hidden" value="'.$relate_radices.'">';
    $arr_td_relate[] = $relate_td_quantity;
    $arr_td_relate[] = (empty($_GET['s_site_id'])?number_format($relate_pInfo->products_virtual_quantity) .'&rarr;':'').((!empty($_GET['s_site_id']))?number_format($relate_pInfo->products_virtual_quantity):tep_draw_input_field('relate_products_virtual_quantity', $relate_pInfo->products_virtual_quantity,' size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"')) . '&nbsp;'.CATEGORY_GE_UNIT_TEXT;
    

    if(empty($site_id)){  
      $max_inventory_num = $max_inventory_num < 0 ? 0 : $max_inventory_num;
      $min_inventory_num = $min_inventory_num < 0 ? 0 : $min_inventory_num;
      $arr_td_relate[] = (empty($_GET['s_site_id'])?number_format($max_inventory_num):'').'&nbsp'.CATEGORY_GE_UNIT_TEXT;
      $arr_td_relate[] = (empty($_GET['s_site_id'])?number_format($min_inventory_num):'').'&nbsp'.CATEGORY_GE_UNIT_TEXT;
    }
    $inventory['max'] = $max_inventory_num;
    $inventory['min'] = $min_inventory_num;
    
    $arr_td_relate[] =  number_format($relate_pInfo->average_rating,2).'%'.((!empty($site_id) || $isstaff)?tep_draw_hidden_field('relate_inventory_max',$inventory['max']).tep_draw_hidden_field('relate_inventory_min',$inventory['min']):'').'&nbsp;&nbsp;&nbsp;&nbsp;';
    if($relate_radices!=''){
      $arr_td_relate[] = sprintf(TEXT_RADICES_PRODUCT_INFO, number_format($relate_pInfo->products_exchange_rate)).'&nbsp;&nbsp;&nbsp;&nbsp;';
    }else{
      $arr_td_relate[] = $relate_pInfo->products_exchange_rate;
    }

  }


  

  

  $history_table_params = array('width' => '100%', 'cellpadding' => '0', 'cellspacing' => '0');
  $product_history_info_str = '';

  //商品历史记录 
  $order_history_query = tep_db_query("
    select orders_id, products_rate, final_price, products_quantity 
    from ".TABLE_ORDERS_PRODUCTS." 
    where 
    products_id='".$pInfo->products_id."'
    order by torihiki_date desc
    limit 5
  ");
  
  $product_history_array = array();
  $product_history_array[] = array('text' => array(
        array('align' => 'left', 'params' => 'width="35%"', 'text' => '<b>'.RIGHT_ORDER_INFO_ORDER_FETCH_TIME.'</b>'), 
        array('align' => 'left', 'params' => 'width="20%"', 'text' => '<b>'.TABLE_HEADING_OSTATUS.'</b>'),
        array('align' => 'right', 'params' => 'width="15%"', 'text' => '<b>'.TABLE_HEADING_GESHU.'</b>'), 
        array('align' => 'right', 'params' => 'width="30%"', 'text' => '<b>'.TABLE_HEADING_DANJIA_TEXT.'</b>'),
        ),'mouse' => true 
      );      

  if (tep_db_num_rows($order_history_query)) {
    $sum_price = 0;
    $sum_quantity = 0;
    $rel_sum_quantity = 0;
    $sum_i = 0;
    while($order_history = tep_db_fetch_array($order_history_query)){
      $orders_query = tep_db_query("select torihiki_date,orders_status from ".TABLE_ORDERS." where orders_id='".$order_history['orders_id']."'");
      $orders_array = tep_db_fetch_array($orders_query);
      tep_db_free_result($orders_query);
      $order_status_query = tep_db_query("select calc_price,orders_status_name from ".TABLE_ORDERS_STATUS." where orders_status_id='".$orders_array['orders_status']."'");
      $order_status_array = tep_db_fetch_array($order_status_query);
      tep_db_free_result($order_status_query);
      $sum_i++;
      if(isset($order_history['products_rate']) &&$order_history['products_rate']!=0 &&$order_history['products_rate']!=''){
        if($radices!=''&&$radices!=1&&$radices!=0&&$order_history['products_rate']!=$radices){
          $oh_fp = tep_number_format($order_history['final_price']*$radices/$order_history['products_rate']);
          $oh_pq = tep_number_format($order_history['products_quantity']*$order_history['products_rate']/$radices);
          if($oh_fp == 0){
            $oh_fp = 0;
          }
          if($oh_pq == 0){
            $oh_pq = 0;
          }
        }else{
          $oh_fp = $order_history['final_price'];
          $oh_pq = $order_history['products_quantity'];
        }
      }else{
        $oh_fp = $order_history['final_price'];
        $oh_pq = $order_history['products_quantity'];
      }
      if ($order_status_array['calc_price'] == '1') {
        $sum_price += $order_history['final_price'] * $order_history['products_quantity'];
        $sum_quantity += $oh_pq; 
      }
      $oh_pq = tep_number_format($oh_pq,',');
      $oh_fp = tep_number_format($oh_fp,',');
      $tmp_oh_pq = display_quantity($oh_pq);
      $tmp_pq_pos = substr($tmp_oh_pq, -1); 
      $tmp_oh_fp = display_quantity($oh_fp);
      $tmp_fp_pos = substr($tmp_oh_fp, -1); 
      $product_history_array[]['text'] = array(
            array('params' => 'class="main" width="120"', 'text' => substr($orders_array['torihiki_date'],0,strlen($orders_array['torihiki_date'])-3)), 
            array('params' => 'class="main" width="80"', 'text' => '<a style="text-decoration:underline" href="'.tep_href_link(FILENAME_ORDERS, 'oID='.$order_history['orders_id'].'&action=edit').'" target="_blank">'.$order_status_array['orders_status_name'].'</a>'),
            array('align' => 'right', 'params' => 'class="main" width="100"', 'text' => (($tmp_pq_pos == '.')?substr($tmp_oh_pq, 0, -1):$tmp_oh_pq)), 
            array('align' => 'right', 'params' => 'class="main"', 'text' => (($tmp_fp_pos == '.')?substr($tmp_oh_fp, 0, -1):$tmp_oh_fp))
           );   
    }

    $sum_vga = 0;
    $sum_vga = tep_number_format($sum_price/$sum_quantity,',');
    $sum_vga = display_quantity($sum_vga);
    $sum_quantity = tep_number_format($sum_quantity,',');
    $sum_quantity = display_quantity($sum_quantity);

    $product_history_array[]['text'] = array(
      array('params' => 'colspan="3" ','align' => 'right' ,'text' => CATEGORY_TOTALNUM_TEXT.' '.$sum_quantity),
      array('align' => 'right' ,'text' => CATEGORY_AVERAGENUM_TEXT.'  '.$sum_vga));
    	
  } else {
    $product_history_array[]['text'] = array(
          array('params' => 'colspan="4"', 'text' => TEXT_DATA_IS_EMPTY) 
        ); 
  }
  $product_history_info_str .= $notice_box->get_table($product_history_array, '', $history_table_params,false,true);
  $relate_history_info_str = '';
  //关联商品历史记录
  if ($relate_exists_single) {
    $history_info_str .= '<br>'; 
    $relate_order_history_query = tep_db_query("
      select orders_id, products_rate, final_price, products_quantity 
      from ".TABLE_ORDERS_PRODUCTS." 
      where 
      products_id='".$pInfo->relate_products_id."'
      order by torihiki_date desc
      limit 5
    ");
    $relate_products_name = $relate_pInfo->products_name;
    
    $relate_product_history_array = array();
    $relate_product_history_array[] = array('text' => array(
        array('align' => 'left', 'params' => 'width="35%"', 'text' => '<b>'.RIGHT_ORDER_INFO_ORDER_FETCH_TIME.'</b>'), 
        array('align' => 'left', 'params' => 'width="20%"', 'text' => '<b>'.TABLE_HEADING_OSTATUS.'</b>'),
        array('align' => 'right', 'params' => 'width="15%"', 'text' => '<b>'.TABLE_HEADING_GESHU.'</b>'), 
        array('align' => 'right', 'params' => 'width="30%"', 'text' => '<b>'.TABLE_HEADING_DANJIA_TEXT.'</b>')
        ),'mouse' => true
      );      
    if (tep_db_num_rows($relate_order_history_query)) {
      $sum_price = 0;
      $sum_quantity = 0;
      $sum_i = 0;
      while($relate_order_history = tep_db_fetch_array($relate_order_history_query)){
        $orders_query = tep_db_query("select torihiki_date,orders_status from ".TABLE_ORDERS." where orders_id='".$relate_order_history['orders_id']."'");
        $orders_array = tep_db_fetch_array($orders_query);
        tep_db_free_result($orders_query);
        $order_status_query = tep_db_query("select calc_price,orders_status_name from ".TABLE_ORDERS_STATUS." where orders_status_id='".$orders_array['orders_status']."'");
        $order_status_array = tep_db_fetch_array($order_status_query);
        tep_db_free_result($order_status_query);
        $sum_i++;
        if(isset($relate_order_history['products_rate']) &&$relate_order_history['products_rate']!=0 &&$relate_order_history['products_rate']!=''){
          if($relate_radices!=''&&$relate_radices!=1&&$relate_radices!=0&&$relate_order_history['products_rate']!=$relate_radices){
            $relate_oh_fp = tep_number_format($relate_order_history['final_price']*$relate_radices/$relate_order_history['products_rate']);
            $relate_oh_pq = tep_number_format($relate_order_history['products_quantity']*$relate_order_history['products_rate']/$relate_radices);
            if($relate_oh_fp == 0){
              $relate_oh_fp = 0;
            }
            if($relate_oh_pq == 0){
              $relate_oh_pq = 0;
            }
          }else{
            $relate_oh_fp = $relate_order_history['final_price'];
            $relate_oh_pq = $relate_order_history['products_quantity'];
          }
        }else{
          $relate_oh_fp = $relate_order_history['final_price'];
          $relate_oh_pq = $relate_order_history['products_quantity'];
        }
        if ($order_status_array['calc_price'] == '1') {
          $sum_price += $relate_order_history['final_price'] * $relate_order_history['products_quantity'];
          $sum_quantity += $relate_oh_pq; 
        }
        $relate_oh_pq = tep_number_format($relate_oh_pq,',');
        $relate_oh_fp = tep_number_format($relate_oh_fp,',');
        $tmp_relate_oh_pq = display_quantity($relate_oh_pq);
        $tmp_relate_pq_pos = substr($tmp_relate_oh_pq, -1); 
        $tmp_relate_oh_fp = display_quantity($relate_oh_fp);
        $tmp_relate_fp_pos = substr($tmp_relate_oh_fp, -1); 
        $relate_product_history_array[]['text'] = array(
              array('params' => 'class="main" width="120"', 'text' => substr($orders_array['torihiki_date'],0,strlen($orders_array['torihiki_date'])-3)), 
              array('params' => 'class="main" width="80"', 'text' => '<a style="text-decoration:underline" href="'.tep_href_link(FILENAME_ORDERS, 'oID='.$relate_order_history['orders_id'].'&action=edit').'" target="_blank">'.$order_status_array['orders_status_name'].'</a>') ,
              array('align' => 'right', 'params' => 'class="main" width="100"', 'text' => (($tmp_relate_pq_pos == '.')?substr($tmp_relate_oh_pq, 0, -1):$tmp_relate_oh_pq)), 
              array('align' => 'right', 'params' => 'class="main"', 'text' => (($tmp_relate_fp_pos == '.')?substr($tmp_relate_oh_fp, 0, -1):$tmp_relate_oh_fp))
              
            );   
      } 
      

      $sum_vga = 0;
      $sum_vga = tep_number_format($sum_price/$sum_quantity,',');
      $sum_vga = display_quantity($sum_vga);
      $sum_quantity = tep_number_format($sum_quantity,',');
      $sum_quantity = display_quantity($sum_quantity);
      $relate_product_history_array[]['text'] = array(
        array('params' => 'colspan="3" ','align' => 'right' ,'text' => CATEGORY_TOTALNUM_TEXT.' '.$sum_quantity),
        array('align' => 'right' ,'text' => CATEGORY_AVERAGENUM_TEXT.'  '.$sum_vga));
      
    } else {
      $relate_product_history_array[]['text'] = array(
            array('params' => 'colspan="4"', 'text' => TEXT_DATA_IS_EMPTY) 
          ); 
    }
    $relate_history_info_str .= $notice_box->get_table($relate_product_history_array, '', $history_table_params,false,true);
  }
  

  $contents  = array();
  
  $contents[]['text'] = array(
        array('text' => $product_info_str), 
        array('text' => $history_info_str) 
      );
  $contents[]['text'] = array(
        array('params' => 'colspan="2"', 'text' => $data_info_str), 
      );
  if (empty($_GET['s_site_id'])) {
    $form_action = 'simple_update_product';
    $form_str = tep_draw_form($form_action, FILENAME_CATEGORIES, 'cPath=' .
        $_GET['cPath'] .  '&pID=' .  $_GET['pID'] . '&page='.$_GET['page'].
        '&action=' .  $form_action.($_GET['search']?'&search='.
          $_GET['search']:'').(!empty($_GET['site_id'])?'&site_id='.$_GET['site_id']:'&site_id=0').(!empty($_GET['s_site_id'])?'&s_site_id='.$_GET['s_site_id']:'&s_site_id=0').(isset($_GET['show_type'])
            ? '&show_type='.$_GET['show_type'] : ''), 'post', '');
    $notice_box->get_form($form_str);
  }
  
  $contents = array();
  if($relate_exists_single){
    foreach($arr_td_title as $tk => $tv){
      $countents[] = array();
      $left_td = '';
      $left_td .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';
      $left_td .= '<td align="left" nowrap="nowrap">'.'<b>'.$tv.'</b>'.'</td>';
      $left_td .= '<td align="right">'.$arr_td_product[$tk].'</td>';
      $left_td .= '</tr></table>';
      $right_td = '';
      $right_td .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';
      $right_td .= '<td align="left"></td>';
      $right_td .= '<td align="right">'.$arr_td_relate[$tk].'</td>';
      $right_td .= '</tr></table>';
      if($tk == 0){
        $contents[] = array('text' => array(
        array('text' => $left_td,'params' => 'width="50%"'), 
        array('text' => '','params' => 'width="30px"','align'=>'right'),
        array('text' => $right_td)
        ),'mouse' => true);
      }else{
        $contents[]['text'] = array(
        array('text' => $left_td,'params' => 'width="50%"'), 
        array('text' => '','params' => 'width="30px"','align'=>'right'),
        array('text' => $right_td)
        );
      }
    }
    $contents[]['text'] = array(array('text' => '<b>'.TEXT_PRODUCTS_ORDER_INFO.'</b>', 'params' => 'colspan = "3"'));;
    $contents[] = array('text' => array(
    	  array('text' => $product_history_info_str,'params' => ' style="" '),
    	  array('text' => '','params' => 'width="30px"'),
    	  array('text' => $relate_history_info_str)),'params' => ' style="" ');
    $contents[]['text'] = array(
    	  array('text' =>  '<b>'.TEXT_USER_ADDED.'</b>'.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(!empty($pInfo->products_user_added)?$pInfo->products_user_added:TEXT_UNSET_DATA),'params'=>' colspan="2" '),
    	  array('text' => '<b>'.TEXT_DATE_ADDED.'</b>'.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(!empty($pInfo->products_date_added)?tep_datetime_short($pInfo->products_date_added):TEXT_UNSET_DATA)));
    $contents[]['text'] = array(
          array('text' => '<b>'.TEXT_USER_UPDATE.'</b>'.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(!empty($pInfo->products_user_update)?$pInfo->products_user_update:TEXT_UNSET_DATA),'params'=>' colspan="2" '),
          array('text' => '<b>'.TEXT_LAST_MODIFIED.'</b>'.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(!empty($pInfo->products_last_modified)?tep_datetime_short($pInfo->products_last_modified):TEXT_UNSET_DATA)));
  }else{
    foreach($arr_td_title as $tk => $tv){
      $countents[] = array();
      $left_td = '';
      $left_td .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';
      $left_td .= '<td align="left" nowrap="nowrap">'.'<b>'.$tv.'</b>'.'</td>';
      $left_td .= '</tr></table>';
      $right_td = '';
      $right_td .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';
      $right_td .= '<td align="right">'.$arr_td_product[$tk].'</td>';
      $right_td .= '</tr></table>';
      if($tk == 0){
      	$contents[] = array('text' => array(
          array('params' => 'width="50%"','text' => $left_td), 
          array('text' => $right_td)
        ),'mouse' => true);
      }else{
        $contents[]['text'] = array(
          array('params' => 'width="50%"','text' => $left_td), 
          array('text' => $right_td)
        );
      }
    }
    $contents[]['text'] = array(array('text' => '<b>'.TEXT_PRODUCTS_ORDER_INFO.'</b>', 'params' => 'colspan = "2"'));
    $contents[] = array('text' => array(
    	  array('text' => $product_history_info_str,'params' => ' colspan = "2"')),'params' => ' style="" ');
    $contents[]['text'] = array(
    	  array('text' =>  '<b>'.TEXT_USER_ADDED.'</b>'.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(!empty($pInfo->products_user_added)?$pInfo->products_user_added:TEXT_UNSET_DATA)),
    	  array('text' => '<b>'.TEXT_DATE_ADDED.'</b>'.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(!empty($pInfo->products_date_added)?tep_datetime_short($pInfo->products_date_added):TEXT_UNSET_DATA)));
    $contents[]['text'] = array(
          array('text' => '<b>'.TEXT_USER_UPDATE.'</b>'.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(!empty($pInfo->products_user_update)?$pInfo->products_user_update:TEXT_UNSET_DATA)),
          array('text' => '<b>'.TEXT_LAST_MODIFIED.'</b>'.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.(!empty($pInfo->products_last_modified)?tep_datetime_short($pInfo->products_last_modified):TEXT_UNSET_DATA)));
  }

  
  $notice_box->get_heading($heading);
  $notice_box->get_contents($contents, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice(true);
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
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="toggle_category_form(\''.$ocertify->npermission.'\', \'4\');"').'</a>'; 
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
    $form_str = tep_draw_form('delete_product', FILENAME_CATEGORIES,
        'action=delete_product_description_confirm&site_id=' .  $_GET['site_id'] .
        '&pID=' . $_GET['pID'] . '&cPath=' .
        $cPath.'&rdirect=all'.$d_page.($_GET['search']?'&search='.$_GET['search']:'').(isset($_GET['s_site_id'])
          ? '&s_site_id='.$_GET['s_site_id'] : '').(isset($_GET['show_type']) ? '&show_type='.$_GET['show_type'] : ''), 'post');
  } else {
    $form_str = tep_draw_form('delete_product', FILENAME_CATEGORIES,
        'action=delete_product_description_confirm&site_id=' .  $_GET['site_id'] .
        '&pID=' . $_GET['pID'] . '&cPath=' .
        $cPath.$d_page.($_GET['search']?'&search='.$_GET['search']:'').(isset($_GET['s_site_id'])
          ? '&s_site_id='.$_GET['s_site_id'] : ''), 'post');
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
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="toggle_category_form(\''.$ocertify->npermission.'\', \'4\')"').'</a>'; 
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

  $form_str = tep_draw_form('delete_product', FILENAME_CATEGORIES, 'action=delete_product_confirm&cPath=' . $cPath.$d_page.($_GET['search']?'&search='.$_GET['search']:''));

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
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_MOVE, 'onclick="toggle_category_form(\''.$ocertify->npermission.'\', \'6\')"').'</a>'; 
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
  
  $form_str = tep_draw_form('move_products', FILENAME_CATEGORIES, 'action=move_product_confirm&cPath=' . $cPath);

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
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="toggle_category_form(\''.$ocertify->npermission.'\', \'7\')"').'</a>'; 
  
  $buttons = array('align' => 'center', 'button' => $button); 
  
  $copy_product_info = array();
  $copy_product_info[]['text'] = array(
        array('text' => TEXT_INFO_CURRENT_CATEGORIES.tep_draw_hidden_field('products_id', $pInfo->products_id)),
        array('text' => tep_output_generated_category_path($pInfo->products_id, 'product'))
      );
  $copy_product_info[]['text'] = array(
        array('text' => TEXT_INFO_HEADING_COPY_TO), 
        array('text' => tep_draw_pull_down_menu('categories_id', tep_get_category_tree('0','','','',false), $current_category_id))
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
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="toggle_marks_form(\''.$ocertify->npermission.'\');"').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="hidden_info_box();"').'</a>';
  
  $buttons = array('align' => 'center', 'button' => $button); 
  
  $pic_info_row = array();

  $pic_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'colspan="2"', 'text' => MARKS_UPDATE_NOTICE_TEXT.tep_draw_hidden_field('pic_id', $_POST['pic_id'])), 
      );
  
  $pic_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="220"', 'text' => TABLE_HEADING_MARKS_PIC_LIST_TITLE),
        array('text' => tep_draw_input_field('pic_alt', $pic_info_res['pic_alt']))
      );
  
  $pic_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="220"', 'text' => TABLE_HEADING_MARKS_PIC_LIST_SORT),
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
  if ($ocertify->npermission >= 15) {
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="if(confirm(\''.TEXT_DEL_OPTION.'\')) toggle_option_action(\''.tep_href_link(FILENAME_OPTION, 'action=delete_group_confirm&group_id='.$group['id'].'&'.$param_str).'\');"').'</a>'; 
  } 
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
  if ($ocertify->npermission >= 15) {
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="if(confirm(\''.TEXT_DEL_OPTION.'\')) toggle_option_action(\''.tep_href_link(FILENAME_OPTION, 'action=delete_item_confirm&item_id='.$item['id'].'&g_id='.$_POST['g_id'].(!empty($_POST['gpage'])?'&gpage='.$_POST['gpage']:'').(isset($_POST['keyword'])?'&keyword='.$_POST['keyword']:'').(isset($_POST['search'])?'&search='.$_POST['search']:'').(!empty($_POST['page'])?'&page='.$_POST['page']:'').(isset($_POST['sort_name'])?'&sort_name='.$_POST['sort_name']:'').(isset($_POST['sort_type'])?'&sort_type='.$_POST['sort_type']:'')).'\');"').'</a>'; 
  } 
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
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'id="button_save" onclick="if(document.getElementById(\'repeat_flag\').value == 1){if(confirm(\''.TEXT_CALENDAR_REPEAT_COMMENT.'\')){save_submit(\''.$ocertify->npermission.'\');}}else{save_submit(\''.$ocertify->npermission.'\');}"').'</a>'; 
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
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="status_add_submit(\''.$ocertify->npermission.'\', 1);" id="button_save"').'</a>'; 
  if ($ocertify->npermission >= 15) {
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="if(confirm(\''.TEXT_CALENDAR_DELETE_COMMENTS.'\')){status_delete(\''.$ocertify->npermission.'\');}"').'</a></form>'; 
  }

  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $form_str = tep_draw_form('status_edit_form', FILENAME_BANK_CL, 'action=status_edit', 'post');

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
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="status_add_submit(\''.$ocertify->npermission.'\', 0);" id="button_save"').'</a>'; 

  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $form_str = tep_draw_form('status_add_form', FILENAME_BANK_CL, 'action=status_add', 'post');

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
  if($page_num_end > count($tags_id_array)-1){

    $page_num_end = count($tags_id_array)-1;
  }
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
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'id="button_save" onclick="edit_tags_submit(\'save\');"').'</a>'; 
  if ($ocertify->npermission >= 15) {
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="edit_tags_submit(\'deleteconfirm\');"').'</a>';
  }
  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $form_str = tep_draw_form('tags_form', FILENAME_TAGS, '', 'post', 'enctype="multipart/form-data" onsubmit="return false;"');

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
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'id="button_save" onclick="create_tags_submit(0);"').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="close_tags_info();"').'</a>'; 

  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $form_str = tep_draw_form('tags_form', FILENAME_TAGS, 'action=insert', 'post', 'enctype="multipart/form-data" onsubmit="return false;"');

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
    $tags_list_str .= '<td width="20%"><input class="carttags" type="checkbox" name="tags_id[]" value="'.$tags_array['tags_id'].'"'.$checked_str.$disabled.'>'.$tags_array['tags_name'].'</td>';
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
$configuration_query = tep_db_query(" select configuration_id, configuration_title, configuration_key, configuration_value, use_function, type_info from " . TABLE_CONFIGURATION . " where configuration_group_id = '" . $_GET['gID'] . "' and `site_id` = '0'  order by sort_order");
$site_id = $_GET['site_id'];
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
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
  $cfg_extra_query = tep_db_query("select  configuration_key, configuration_description, date_added, last_modified, use_function, set_function,user_added,user_update,type_info from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
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
            'MIN_PROFIT_SETTING',
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
      array('text' => $value_field.'<br>'.$cInfo->configuration_description)
   );
  $configuration_contents[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($cInfo->user_added))?$cInfo->user_added:TEXT_UNSET_DATA)), 
        array('align' => 'left', 'params' => 'width="70%"', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($cInfo->date_added))?$cInfo->date_added:TEXT_UNSET_DATA))
      );
  
  $configuration_contents[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($cInfo->user_update))?$cInfo->user_update:TEXT_UNSET_DATA)),
        array('align' => 'left', 'params' => 'width="70%"', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($cInfo->last_modified))?$cInfo->last_modified:TEXT_UNSET_DATA))
      );
    //button 内容 
    if(in_array($site_id,$site_array)) { 
    if ($cInfo->configuration_key == 'DS_ADMIN_SIGNAL_TIME') {
      $configuration_button[] = '<br>' .  tep_html_element_button(IMAGE_UPDATE, 'onclick="check_signal_time_select(\''.$ocertify->npermission.'\')"') . '&nbsp;';
    } else {
      if ($cInfo->type_info == '1') {
        $configuration_button[] = '<br>' .  tep_html_element_button(IMAGE_UPDATE, 'onclick="new_update_configuration_info(\''.$ocertify->npermission.'\');"') . '&nbsp;';
      } else {
        $configuration_button[] = '<br>' .  tep_html_element_button(IMAGE_UPDATE, 'onclick="update_configuration_info(\''.$ocertify->npermission.'\');"') . '&nbsp;';
      }
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
    array('text' => $value_field.'<br>'.$cInfo->configuration_description)
    );
  $contents[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_ADDED.((tep_not_null($configuration_user_update['user_added']))?$configuration_user_update['user_added']:TEXT_UNSET_DATA)), 
        array('align' => 'left', 'params' => 'width="70%"', 'text' => TEXT_DATE_ADDED.((tep_not_null($configuration_user_update['date_added']))?$configuration_user_update['date_added']:TEXT_UNSET_DATA))
      );
  
  $contents[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_UPDATE.((tep_not_null($configuration_user_update['user_update']))?$configuration_user_update['user_update']:TEXT_UNSET_DATA)),
        array('align' => 'left', 'params' => 'width="70%"', 'text' => TEXT_DATE_UPDATE.((tep_not_null($configuration_user_update['last_modified']))?$configuration_user_update['last_modified']:TEXT_UNSET_DATA))
      );
 
  //if exists ,can be delete ,or  can not 
  if(in_array($site_id,$site_array)) { 
  if (is_numeric($fetch_result['configuration_id'])){
    $button[] = '<br>' .  tep_html_element_button(IMAGE_UPDATE, 'onclick="update_configuration_info(\''.$ocertify->npermission.'\');"') .'&nbsp;' .  tep_html_element_button(IMAGE_DEFFECT, 'onclick="set_invalid_configuration(\''.$ocertify->npermission.'\', \''.$_GET['gID'].'\', \''.$fetch_result['configuration_id'].'_'.$cInfo->configuration_id.'\')"').'&nbsp;';
  }else {
    $button[] = '<br>' .  tep_html_element_button(IMAGE_EFFECT, 'onclick="update_configuration_info(\''.$ocertify->npermission.'\');"') . '&nbsp;';
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
  $sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
  while($userslist= tep_db_fetch_array($sites_id)){
    $site_arr = $userslist['site_permission']; 
  }
  $sites_sql = tep_db_query("SELECT * FROM `sites`");
  while($sites_row = tep_db_fetch_array($sites_sql)){
     $show_site_arr[] = $sites_row['id']; 
  }
  $site_array = explode(',',$site_arr);
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
       $site_permission = editPermission($site_arr, $_GET['action_sid'],true);
    }
    if(!$site_permission&&isset($_GET['action_sid'])&&$_GET['action_sid']){
      $str_disabled = ' disabled="disabled" ';
    }else{
      $str_disabled = '';
    }
    $img_array = tep_products_images($reviews['products_id'],$reviews['site_id']);
  
    $products_name_query = tep_db_query("
        select *
        from " . TABLE_PRODUCTS_DESCRIPTION . " 
        where products_id = '" . $reviews['products_id'] . "' 
          and site_id = '0'
          and language_id = '" . $languages_id . "'");
    $products_name = tep_db_fetch_array($products_name_query);
    if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
      $sql_site_where = ' site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
      $sql_list_site_where = ' r.site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
      $tmp_site_list_array = explode('-', $_GET['site_id']); 
    } else {
      $sql_site_where = ' site_id in ('.tep_get_setting_site_info(FILENAME_REVIEWS).')';
      $sql_list_site_where = ' r.site_id in ('.tep_get_setting_site_info(FILENAME_REVIEWS).')';
      $tmp_site_list_array = explode('-', tep_get_setting_site_info(FILENAME_REVIEWS)); 
    }
    $tmp_list_or_str = '';
    foreach ($tmp_site_list_array as $or_key => $or_value) {
      $tmp_list_or_str .= "pd.site_id = '".$or_value."' or ";
    }
    $tmp_list_or_str = substr($tmp_list_or_str, 0, -3);
    
    if(isset($_GET['site_id'])&&$_GET['site_id']==''){
      $_GET['site_id'] = str_replace(',','-',tep_get_setting_site_info(FILENAME_REVIEWS));
    }

     
    if(isset($_GET['product_name']) && $_GET['product_name']){
       $p_list_arr = array();
       $p_list_arr_site = array();
       if(isset($_GET['site_id'])&&$_GET['site_id']){
         $p_list_arr_site_sql = "select products_id,products_name from ".
           TABLE_PRODUCTS_DESCRIPTION." where 
           products_name like '%".trim($_GET['product_name'])."%' 
           and ".$sql_site_where;
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
            where ".$sql_site_where.")";
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
    $reviews_order_sort_name = ' date_added'; 
    $reviews_order_sort = 'desc'; 
    $reviews_order_help_sort = ' reviews_id';
    if (!empty($_GET['r_sort'])) {
      switch ($_GET['r_sort']) {
        case 'r_site':
          $reviews_order_sort_name = ' romaji'; 
          break;
        case 'r_name':
          $reviews_order_sort_name = ' products_name'; 
          break;
        case 'r_rate':
          $reviews_order_sort_name = ' reviews_rating'; 
          break;
        case 'r_added':
          $reviews_order_sort_name = ' date_added'; 
          break;
        case 'r_status':
          $reviews_order_sort_name = ' reviews_status'; 
          break;
        case 'r_update':
          $reviews_order_sort_name = ' last_modified'; 
          break;
      }
    }
    if (!empty($_GET['r_sort_type'])) {
      if ($_GET['r_sort_type'] == 'asc') {
        $reviews_order_sort = 'asc'; 
      } else {
        $reviews_order_sort = 'desc'; 
      }
    }
    $reviews_order_sql = $reviews_order_sort_name.' '.$reviews_order_sort.' , '.$reviews_order_help_sort.' '.$reviews_order_sort; 
    $reviews_list_query_raw = "
      select * from (select r.reviews_id, 
             r.products_id, 
             r.date_added, 
             r.last_modified, 
	     r.user_added,
	     r.user_update,
             r.site_id,
             r.reviews_rating, 
             r.reviews_status ,
             s.romaji,
             s.name as site_name,
             pd.products_name
     from " . TABLE_REVIEWS . " r, ".TABLE_SITES." s, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
     where (".$tmp_list_or_str." or pd.site_id = '0') and r.site_id = s.id
        and p.products_id = r.products_id
        and p.products_id = pd.products_id
        and pd.language_id = '".$languages_id."'
        and " .$sql_list_site_where. "".$where_str."
        order by pd.site_id desc) p group by reviews_id order by ".$reviews_order_sql;
    
    $reviews_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $reviews_list_query_raw, $reviews_query_numrows); 
    $reviews_raw_query = tep_db_query($reviews_list_query_raw);
    while ($reviews_res = tep_db_fetch_array($reviews_raw_query)) {
         $rid_array[] = $reviews_res['reviews_id']; 
         $rsid_array[] = $reviews_res['site_id'];
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
      $page_str .= '<a id="option_prev" onclick=\'show_text_reviews("","'.$_GET['page'].'","'.$rid_array[$r_key-1].'","'.$_GET['site_id'].'","'.$rsid_array[$r_key-1].'", "'.(!empty($_GET['r_sort'])?$_GET['r_sort']:'').'", "'.(!empty($_GET['r_sort_type'])?$_GET['r_sort_type']:'').'")\' href="javascript:void(0);" id="option_next">'.IMAGE_PREV.'</a>&nbsp;&nbsp;';
    } else {
      $page_str .= '<font color="#000000">'.IMAGE_PREV.'</font>&nbsp;&nbsp;';   
    }
     if ($r_key < (count($rid_array) - 1)) {
       $page_str .= '<a id="option_next" onclick=\'show_text_reviews("","'.$_GET['page'].'","'.$rid_array[$r_key+1].'","'.$_GET['site_id'].'","'.$rsid_array[$r_key+1].'","'.(!empty($_GET['r_sort'])?$_GET['r_sort']:'').'", "'.(!empty($_GET['r_sort_type'])?$_GET['r_sort_type']:'').'")\' href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'</a>&nbsp;&nbsp;';
     } else {
       $page_str .= '<font color="#000000">'.IMAGE_NEXT.'</font>&nbsp;&nbsp;';   
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
  if(isset($_GET['action_sid'])&&$_GET['action_sid']){
    $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$_GET['action_sid']));
    $site_id_name = $site_name['romaji'];
  }else{
   $site_id_name = "<select id='add_site_id' name='insert_site_id'>";
   $new_site_arr = array_intersect($show_site_arr,$site_array);

   foreach($new_site_arr as $value){
     if($value!=0){
       $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$value));
       $site_id_name .= "<option value='".$site_name['id']."' ";
       if(isset($_GET['add_site_id'])&&$_GET['add_site_id']
           &&$_GET['add_site_id'] == $site_name['id']){
         $site_id_name .= " selected ";
       }
       $site_id_name .= ">".$site_name['name']."</option>";
     }
   }
   $site_id_name .= "</select>";

  }
  $contents[]['text'] = array( 
      array('text' => ENTRY_SITE.'<input type="hidden" name="action_type" value="'.$action_type.'">'),
      array('text' => $site_id_name.'<input id="site_id" name="site_id" type="hidden" value="'.$_GET['site_id'].'"><input id="site_hidden" name="site_hidden" type="hidden" value="'.$_GET['site_id'].'">')
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
        array('text' => tep_draw_pull_down_menu('review_products_id', tep_get_category_tree(),$review_products_id_info,'id="review_products_id" class="td_select" onchange="change_review_products_id(this,'.$_GET['page'].','.$rID.','.$_GET['site_id'].')"'.$str_disabled) .'<input type="hidden" id="r_cid" value="'.$df_cid.'">') 
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
      if(isset($_GET['action_sid'])&&$_GET['action_sid']){
        $p_real_sid = $_GET['action_sid'];
      }else{
        $p_real_sid = 0;
      }
      $select_value = "<option value='0'>".TEXT_SELECT_PRODUCT;      
      $review_select = "<select class='td_select' id='add_product_products_id' name=\"add_product_products_id\" onchange='change_hidden_select(this)' ".$str_disabled.">";
      $ProductOptions = $select_value;
             asort($ProductList[$add_product_categories_id]);
             foreach($ProductList[$add_product_categories_id] as $ProductID => $ProductName){
               $ProductName  = tep_get_products_name($ProductID,$languages_id,$p_real_sid,true);
                 if($df_pid == $ProductID){
                 $ProductOptions .= "<option value='$ProductID' selected> $ProductName\n";
                 }else{
                 $ProductOptions .= "<option value='$ProductID'> $ProductName\n";
                 }
             }
             $ProductOptions = str_replace("value='$add_product_products_id'","value='$add_product_products_id' selected", $ProductOptions);
    $review_select_end = "</select>";
    if(!isset($df_pid)||$df_pid==0){
      $error_add_id = '<br><span id="p_error" style="color:#ff0000;">'.TEXT_CLEAR_SELECTION.'</span>'; 
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
   array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($rInfo->user_added))?$rInfo->user_added:TEXT_UNSET_DATA)), 
   array('align' => 'left', 'params' => 'width="70%"', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($rInfo->date_added))?$rInfo->date_added:TEXT_UNSET_DATA))
  );
  $contents[]['text'] = array(
    array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($rInfo->user_update))?$rInfo->user_update:TEXT_UNSET_DATA)),
    array('align' => 'left', 'params' => 'width="70%"', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($rInfo->last_modified))?$rInfo->last_modified:TEXT_UNSET_DATA))
  );


  if($ocertify->npermission >= 15){
   $reviews_button[] = tep_html_element_button(IMAGE_SAVE,$str_disabled.'onclick="check_review_submit('.$_GET['rID'].','.$_GET['page'].')" id="button_save"').  '&nbsp;<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE,$str_disabled.' onclick="delete_reviews_action(\''.tep_href_link(FILENAME_REVIEWS, 'page=' .  $_GET['page'] .  '&rID=' .  $rInfo->reviews_id) .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').  (isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'').(isset($_GET['r_sort'])?'&r_sort='.$_GET['r_sort']:'').(isset($_GET['r_sort_type'])?'&r_sort_type='.$_GET['r_sort_type']:'').'&action=deleteconfirm'.'\');"').'</a>';
    if(!empty($reviews_button)){
        $buttons = array('align' => 'center', 'button' => $reviews_button);
     }
  }else{
   $reviews_button[] = tep_html_element_button(IMAGE_SAVE,$str_disabled.'onclick="check_review_submit('.$_GET['rID'].','.$_GET['page'].')" id="button_save"'.$str_disabled);
    if(!empty($reviews_button)){
        $buttons = array('align' => 'center', 'button' => $reviews_button);
     }

  }
  if($_GET['site_id'] == 0){
       $_GET['site_id'] = $reviews['site_id']; 
  }

//生产 表格
$reviews_form =  tep_draw_form('review', FILENAME_REVIEWS, 'page=' .  $_GET['page'] .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').'&rID=' .  $_GET['rID'] .  (isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'').(isset($_GET['r_sort'])?'&r_sort='.$_GET['r_sort']:'').(isset($_GET['r_sort_type'])?'&r_sort_type='.$_GET['r_sort_type']:''). '&action=update', 'post' , 'onsubmit="return check_review()"');

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
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
while($userslist= tep_db_fetch_array($sites_id)){
     $site_permission = $userslist['site_permission']; 
}
$site_id = $_GET['action_sid'];
if($_GET['site_id'] == -1){
  $_GET['site_id'] = '';
}
if(isset($site_permission)) $site_arr=$site_permission;//权限判断
else $site_arr="";
$site_array = explode(',',$site_arr);
if(!in_array($site_id,$site_array)&&$site_id!=-1){
   $disable = 'disabled="disabled"';
}
 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 $get_news_id = $_GET['latest_news_id'];
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
          from " . TABLE_NEWS . " 
          where news_id = '" . (int)$_GET['latest_news_id'] . "'");
      $latest_news = tep_db_fetch_array($latest_news_query);
    $nInfo = new objectInfo($latest_news);
    } else {
      $latest_news = array();
    }

    $news_order_sort_name = ' date_added';
    $news_order_sort = 'desc';
    if ($_GET['sort_name'] != '') {
      switch ($_GET['sort_name']) {
         case 'site':
           $news_order_sort_name = ' site_id';
           break;
         case 'title':
           $news_order_sort_name = ' headline';
           break;
         case 'add_date':
           $news_order_sort_name = ' date_added';
           break;
         case 'status':
           $news_order_sort_name = ' status';
           break;
         case 'isfirst':
           $news_order_sort_name = ' isfirst';
           break;
         case 'news_update':
           $news_order_sort_name = ' latest_update_date';
           break;
      }
    }
    if ($_GET['sort_type'] != '') {
      if ($_GET['sort_type'] == 'asc') {
        $news_order_sort = 'asc';
      } else {
        $news_order_sort = 'desc';
      }
    }
    $news_order_sql = $news_order_sort_name.' '.$news_order_sort;
    if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
      $sql_site_where = 'site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
    } else {
      $show_site_str = tep_get_setting_site_info(FILENAME_NEWS);
      $sql_site_where = 'site_id in ('.$show_site_str.')';
    }
    $sites_sql = tep_db_query("SELECT * FROM `sites`");
    $show_site_arr = array();
    $show_site_arr[0] = '0'; 
    while($sites_row = tep_db_fetch_array($sites_sql)){
      $show_site_arr[] = $sites_row['id']; 
    }
     $latest_news_query_raw = ' select n.news_id, n.headline, n.date_added,
     n.author, n.update_editor, n.latest_update_date, n.content, n.status,
     n.news_image, n.news_image_description, n.isfirst, n.site_id from ' .
     TABLE_NEWS . ' n where '.$sql_site_where.' order by '.$news_order_sql;
     $news_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $latest_news_query_raw, $latest_news_query_numrows);
     
     $latest_news_id_query = tep_db_query($latest_news_query_raw);
     while ($latest_news_id = tep_db_fetch_array($latest_news_id_query)) {
         $cid_array[] = $latest_news_id['news_id'];
         $sid_array[] = $latest_news_id['site_id'];
     }
 foreach ($cid_array as $c_key => $c_value) {
           if ($_GET['latest_news_id'] == $c_value) {
            break;
          }
 }
 $page_str = '';
 if($get_news_id != -1){
 if ($c_key > 0) {
   $page_str .= '<a id="option_prev" onclick=\'show_latest_news("",'.$_GET['page'].',"'.$cid_array[$c_key-1].'","'.$_GET['site_id'].'",'.$sid_array[$c_key-1].', "'.(($_GET['sort_name'] != '')?$_GET['sort_name']:'').'", "'.(($_GET['sort_type'] != '')?$_GET['sort_type']:'').'")\' href="javascript:void(0);" id="option_next">'.TEXT_CAMPAIGN_PREV.'</a>&nbsp;&nbsp;'; 
 }
 if ($c_key < (count($cid_array) - 1)) {
   $page_str .= '<a id="option_next" onclick=\'show_latest_news("",'.$_GET['page'].',"'.$cid_array[$c_key+1].'","'.$_GET['site_id'].'",'.$sid_array[$c_key+1].', "'.(($_GET['sort_name'] != '')?$_GET['sort_name']:'').'", "'.(($_GET['sort_type'] != '')?$_GET['sort_type']:'').'")\' href="javascript:void(0);" id="option_next">'.TEXT_CAMPAIGN_NEXT.'</a>&nbsp;&nbsp;';
 }
 }
 $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
 $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
 $heading[] = array('align' => 'left', 'text' => isset($latest_news['headline'])?$latest_news['headline']:HEADING_TITLE);
 $heading[] = array('align' => 'right', 'text' => $page_str);
 $form_str = tep_draw_form('new_latest_news', FILENAME_NEWS, (isset($_GET['latest_news_id']) && $_GET['latest_news_id'] != '-1' ?  ('latest_news_id=' . $_GET['latest_news_id'] . '&action=update_latest_news') : 'action=insert_latest_news').(!empty($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').(isset($_GET['page'])?('&page='.$_GET['page']):'').(($_GET['sort_name'] != '')?'&news_sort='.$_GET['sort_name']:'').(($_GET['sort_type'] != '')?'&news_sort_type='.$_GET['sort_type']:''), 'post', 'enctype="multipart/form-data" onSubmit="return false;"'); 

 $latest_news_contents[]['text'] = array(
      array('text' => '<input type="hidden" name="author" value="'.$_SESSION['user_name'].'"><input type="hidden" name="update_editor" value="'.$_SESSION['user_name'].'">')
 );
 if($_GET['latest_news_id'] != '-1'){
 if($site_id == 0){
      $site_id_name = 'all';
 }else{
      $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$site_id));
      $site_id_name = $site_name['romaji'];
 }
 }else{
   $site_list_raw = tep_db_query("select * from sites order by id asc"); 
   $site_id_name = '<table border="0" width="100%" cellpadding="0" cellspacing="0">'; 
   if ((trim($site_arr)) != '' && ($site_arr != '0')) {
     $site_id_name .= '<tr class="td_input"><td><input type="radio" name="select_site_type" id="select_site_type" onclick="change_site_type(0, \''.$site_arr.'\');" value="0" checked>'.NEWS_FIX_SITE_TEXT.'&nbsp;&nbsp;<input type="radio" name="select_site_type" id="select_site_type" onclick="change_site_type(1, \''.$site_arr.'\');" value="1"'.((!in_array('0', $site_array))?' disabled':'').'>ALL<input type="hidden" value="0" id="site_type_hidden" type="site_type_hidden"></td></tr>'; 
   } else {
     $site_id_name .= '<tr class="td_input"><td><input type="radio" name="select_site_type" id="select_site_type" value="0" disabled>'.NEWS_FIX_SITE_TEXT.'&nbsp;&nbsp;<input type="radio" name="select_site_type" id="select_site_type" value="1" checked>ALL<input type="hidden" value="1" id="site_type_hidden" type="site_type_hidden"></td></tr>'; 
   }
   $site_id_name .= '</table>'; 
   $site_id_name .= '<div id="select_site">'; 
   $site_id_name .= '<table border="0" width="100%" cellpadding="0" cellspacing="0">'; 
   $s_num = 0;
   $s_flag = false;
   $is_disabled_single = false; 
   if (($site_arr == '0') || ($site_arr == '')) {
     $is_disabled_single = true; 
   }
   $site_num_total = tep_db_num_rows($site_list_raw); 
   while ($site_list_res = tep_db_fetch_array($site_list_raw)) {
     if ($s_num % 2 == 0) {
       $site_id_name .= '<tr>'; 
     }
     $site_id_name .= '<td><input type="checkbox" name="site_id_info[]" value="'.$site_list_res['id'].'"'.(($is_disabled_single)?' disabled="disabled"':((!in_array($site_list_res['id'], $site_array)?'disabled="disabled"':''))).'>'.$site_list_res['name'].'</td>'; 
     
       
     if (($s_num + 1) % 2 == 0) {
       if ($s_flag == false) {
         $site_id_name .= '<td><a href="javascript:void(0);">'.tep_html_element_button(SELECT_ALL_TEXT, 'onclick="select_all_news_site()" id="all_site_button"'.(($is_disabled_single)?' disabled="disabled"':'')).'</a></td>'; 
         $s_flag = true; 
       } else {
         $site_id_name .= '<td></td>'; 
       }
       $site_id_name .= '</tr>'; 
     }
     $s_num++;
     if ($s_num == $site_num_total) {
       if ($s_num % 2 != 0) {
         if ($s_num == 1) {
           $site_id_name .= '<td></td>'; 
           $site_id_name .= '<td><a href="javascript:void(0);">'.tep_html_element_button(SELECT_ALL_TEXT, 'onclick="select_all_news_site()" id="all_site_button"'.(($is_disabled_single)?' disabled="disabled"':'')).'</a></td>'; 
         } else {
           $site_id_name .= '<td></td><td></td>'; 
         }
       }
     }
   }
   $site_id_name .= '</table>'; 
   $site_id_name .= '<span id="site_error" style="color:#ff0000;"></span><input type="hidden" name="is_select" id="is_select" value="0">'; 
   $site_id_name .= '</div>'; 
 }
 if($get_news_id != -1){
      $site_romaji = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$latest_news['site_id']));
      if($latest_news['site_id'] == 0){
       $site_romaji['romaji'] = 'all';
      }
 }
 $latest_news_contents[]['text'] = array(
      array('text' => ENTRY_SITE),
      array('params' => 'class="td_input"', 'text' => (isset($_GET['latest_news_id']) && $_GET['latest_news_id'] && $latest_news?$site_romaji['romaji']:$site_id_name.'<input type="hidden" name="site_id" value="'.$site_id.'">'))
 );
 $latest_news_contents[]['text'] = array(
     array('text' => TEXT_LATEST_NEWS_HEADLINE),
     array('text' => tep_draw_input_field('headline', isset($latest_news['headline'])?$latest_news['headline']:'', 'class="option_text" id="headline" style="margin-left:0"'.$disable, false).'&nbsp;&nbsp;<font color="red" id="title_error"></font>')
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
     array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($latest_news['author']))?$latest_news['author']:TEXT_UNSET_DATA)), 
     array('align' => 'left', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($latest_news['date_added']))?$latest_news['date_added']:TEXT_UNSET_DATA))
     );
 $latest_news_contents[]['text'] = array(
     array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($latest_news['update_editor']))?$latest_news['update_editor']:TEXT_UNSET_DATA)),
     array('align' => 'left', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($latest_news['latest_update_date']))?date('Y-m-d H:i:s',$latest_news['latest_update_date']):TEXT_UNSET_DATA))
     );
if($ocertify->npermission >= 15){
if(isset($disable) && $disable){
 isset($_GET['latest_news_id']) ? $cancel_button = tep_html_element_button(IMAGE_DELETE,$disable) : $cancel_button = '';
}else{
 isset($_GET['latest_news_id']) ? $cancel_button = '&nbsp;&nbsp;<a class="new_product_reset" href="javascript:void(0);">' .  tep_html_element_button(IMAGE_DELETE, 'onclick="toggle_news_action(\''.tep_href_link(FILENAME_NEWS, 'action=delete_latest_news_confirm&latest_news_id='.  $_GET['latest_news_id'].(!empty($_GET['site_id']) ?  '&site_id='.$_GET['site_id']:'').(isset($_GET['page']) ?  '&page='.$_GET['page']:'').(($_GET['sort_name'] != '')?'&news_sort='.$_GET['sort_name']:'').(($_GET['sort_type'] != '')?'&news_sort_type='.$_GET['sort_type']:'')).'\');"') . '</a>' : $cancel_button = '';
}
}
if($_GET['latest_news_id'] != '-1'){
  $button[] = tep_html_element_button(IMAGE_SAVE,'id="button_save" onclick="check_news_info()"'.$disable). $cancel_button;
} else {
  $button[] = tep_html_element_button(IMAGE_SAVE,'id="button_save" onclick="check_news_info()"'.$disable);
}
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
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
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
    if($_GET['site_id'] == ''){ $_GET['site_id'] = 0; }
    $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."
                             nextdate,
                             privilege,self,operator,user_added,created_at,
                             updated_at,onoff,update_user
                              from 
                             ".TABLE_IDPW." 
                             where site_id='".$_GET['site_id']."' and onoff = '1' 
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
    $page_str .= '<a onclick="show_pw_manager(\'\','.$cid_array[$c_key-1].','.$_GET['page'].','.$site_id.')" href="javascript:void(0)" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
 }
 
  if ($c_key < (count($cid_array) - 1)) {
   $page_str .= '<a onclick="show_pw_manager(\'\','.$cid_array[$c_key+1].','.$_GET['page'].','.$site_id.')" href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }   
      $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
      $heading = array();
      $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">'); 
      $heading[] = array('align' => 'left', 'text' => $pwInfo->title.'&nbsp;&nbsp;');
      $heading[] = array('align' => 'right', 'text' => $page_str);

      $contents[]['text'] = array(
           array('text' => TEXT_INFO_TITLE),
           array('text' => tep_draw_input_field('title',$pwInfo->title,'id="title" style="font-size:12px"'.$disable))
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
           array('text' => tep_draw_input_field('url',$pwInfo->url,'id="url" style="font-size:12px"'.$disable).tep_draw_hidden_field('old_url',$pwInfo->url,$disable))
          );
      $contents[]['text'] = array(
           array('text' => TEXT_INFO_LOGINURL),
           array('text' =>
             tep_draw_input_field('loginurl',$pwInfo->loginurl,'id="loginurl" style="font-size:12px"'.$disable).tep_draw_hidden_field('old_loginurl',$pwInfo->loginurl,$disable))
          );
      $contents[]['text'] = array(
           array('text' => TEXT_INFO_USERNAME),
           array('text' => tep_draw_input_field('username',$pwInfo->username,'id="username"style="font-size:12px"'.$disable).tep_draw_hidden_field('old_username',$pwInfo->username,$disable))
          );
      $pwd_pattern = tep_get_pwd_pattern();
      $pwd_len = tep_get_pwd_len();
      $pwd_pattern_arr = explode(',',$pwd_pattern);
       $contents[]['text'] = array(
           array('text' => TEXT_PWD_LEN),
           array('text' => tep_draw_input_field('pwd_len',$pwd_len,'style="font-size:12px"id="pwd_len" maxlength="2" size="2"'.$disable)."&nbsp;".  tep_html_element_button(TEXT_BUTTON_MK_PWD,'onclick="mk_pwd()"'.$disable) .  tep_draw_input_field('password',$pwInfo->password,'id="password"style="font-size:12px"'.$disable) .tep_draw_hidden_field('old_password',$pwInfo->password,$disable))
          );
      $contents[]['text'] = array(
           array('text' => TEXT_INFO_PASSWORD),
           array('text' => tep_draw_checkbox_field('pattern[]','english', in_array('english',$pwd_pattern_arr)?true:false,'',$disable).TEXT_LOWER_ENGLISH.  tep_draw_checkbox_field('pattern[]','ENGLISH', in_array('ENGLISH',$pwd_pattern_arr)?true:false,'',$disable).TEXT_POWER_ENGLISH. tep_draw_checkbox_field('pattern[]','NUMBER', in_array('NUMBER',$pwd_pattern_arr)?true:false,'',$disable).TEXT_NUMBER)
               );
      $contents[]['text'] = array(
           array('text' => TEXT_INFO_COMMENT),
           array('text' => tep_draw_textarea_field('comment', 'soft', '30', '5', $pwInfo->comment, 'style="resize: vertical;font-size:12px"onblur="o_submit_single = true;" onfocus="o_submit_single = false;" class="pw_textarea"'.$disable).tep_draw_hidden_field('old_comment',$pwInfo->comment,$disable))
          );
       $contents[]['text'] = array(
           array('text' => TEXT_INFO_MEMO),
           array('text' => tep_draw_textarea_field('memo', 'soft', '30', '5', $pwInfo->memo, 'style="resize: vertical;font-size:12px"class="pw_textarea" onblur="o_submit_single = true;" onfocus="o_submit_single = false;"'.$disable))
          );
      if($disable){
        $open_new_calendar = '<a class="dpicker"></a>';
      }else{
        $open_new_calendar = '<a href="javascript:void(0);" onclick="open_new_calendar();" class="dpicker"></a>';
      }
        $contents[]['text'] = array(
           array('text' => TEXT_INFO_NEXTDATE),
           array('text' => '<div class="nextdate_info">' .  '<div class="yui3-skin-sam yui3-g">'.  tep_draw_input_field('nextdate',$pwInfo->nextdate, 'id="input_nextdate"'.$disable).  $open_new_calendar.'<input type="hidden" name="toggle_open" value="0" id="toggle_open"> <div class="yui3-u" id="new_yui3"><div id="mycalendar"></div></div>' ."</div>")
          );
        $contents[]['text'] = array(
           array('text' => TEXT_INFO_PRIVILEGE),
           array('params' => 'calss="td_input"','text' => tep_draw_radio_field('privilege','15',$pwInfo->privilege==15?true:false,'','onclick="self_radio()" id="self" class="privilege"'.$disable).TEXT_SELF.  tep_draw_radio_field('privilege','7',$pwInfo->privilege==7?true:false,'','class="privilege" id="privilege_s" onclick="privilege_s_radio()"'.$disable).TEXT_PERMISSION_STAFF.  tep_draw_radio_field('privilege','10',$pwInfo->privilege==10?true:false,'','class="privilege" id="privilege_c" onclick="privilege_c_radio()"'.$disable).TEXT_PERMISSION_CHIEF)
          );
        $contents[]['text'] = array(
            array('text' => ''),
            array('text' => TEXT_OPERATOR_INFO)
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
       $contents[]['text'] = array( array('text' => '&nbsp;'), array('text' => '<div id="user_select" class="user_select" style="display:'.$pw_select_display.'" > '.tep_get_user_select($selected_user).'</div>'));
       $contents[]['text'] = array(
           array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($pwInfo->user_added))?$pwInfo->user_added:TEXT_UNSET_DATA)), 
           array('align' => 'left', 'params' => 'width="70%"', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($pwInfo->created_at))?$pwInfo->created_at:TEXT_UNSET_DATA))
         );
       $contents[]['text'] = array(
           array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($pwInfo->update_user))?$pwInfo->update_user:TEXT_UNSET_DATA)),
           array('align' => 'left', 'params' => 'width="70%"', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($pwInfo->updated_at))?$pwInfo->updated_at:TEXT_UNSET_DATA))
        );
    if($ocertify->npermission >= 15){
     if(isset($disable) && $disable){
       $button_del = "<input style='font-size:12px' type='button' ".$disable." value='".TEXT_BUTTON_DELETE."'>";
       $button_history = "<input style='font-size:12px' type='button' ".$disable." value='".TEXT_BUTTON_HISTORY."'>";
     }else{
       $button_history = "<input style='font-size:12px' type='button' onclick=\"location.href='".  tep_href_link(FILENAME_PW_MANAGER, 'log=id_manager_log&pw_id='.$pwInfo->id.'&site_id='.$site_id) ."'\" value='".TEXT_BUTTON_HISTORY."'>";
       $button_del = "<input type='button' style='font-size:12px' onclick=\"toggle_idpw_action('".  tep_href_link(FILENAME_PW_MANAGER, 'page=' . $_GET['page'] .  '&sort='.$_GET['sort'].'&type='.$_GET['type'].'&site_id='.$_GET['site_id'].'&pw_id=' .  $pwInfo->id .  '&action=deleteconfirm')  ."', '".$ocertify->npermission."');\" value='".TEXT_BUTTON_DELETE."'>";
     }
    }   
      $button[] = "<input ".$disable." style='font-size:12px'type='button' value='".IMAGE_SAVE."' onclick=\"valdata('".$ocertify->npermission."')\" id='button_save'>" .  '&nbsp;'.$button_del."&nbsp;".$button_history;
      if(!empty($button)){
        $buttons = array('align' => 'center', 'button' => $button);
      }
      $form_str = tep_draw_form('pw_manager', FILENAME_PW_MANAGER, 'page=' . $_GET['page'] . '&site_id='.$site_id.'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&action=update&pw_id='.$pwInfo->id, 'post', 'enctype="multipart/form-data"');
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
          array('text' => tep_draw_input_field('title','','id="title"style="font-size:12px"'.$disable))
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
          array('text' => tep_draw_input_field('url','','id="url"style="font-size:12px"'.$disable))
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_LOGINURL),
          array('text' => tep_draw_input_field('loginurl','','id="loginurl"style="font-size:12px"'.$disable))
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_USERNAME),
          array('text' => tep_draw_input_field('username','','id="username"style="font-size:12px"'.$disable))
      );
      $pwd_pattern = tep_get_pwd_pattern();
      $pwd_len = tep_get_pwd_len();
      $pwd_pattern_arr = explode(',',$pwd_pattern);
      $contents[]['text'] = array(
          array('text' => TEXT_PWD_LEN),
          array('text' => tep_draw_input_field('pwd_len',$pwd_len,'id="pwd_len" maxlength="2" size="2"style="font-size:12px"'.$disable)."&nbsp;".  tep_html_element_button(TEXT_BUTTON_MK_PWD,'onclick="mk_pwd()"'.$disable). tep_draw_input_field('password',tep_get_new_random($pwd_pattern,$pwd_len),'id="password"'.$disable))
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
          array('text' => tep_draw_textarea_field('comment', 'soft', '30', '5', '','style="resize: vertical;font-size:12px"onblur="o_submit_single = true;" onfocus="o_submit_single = false;" class="pw_textarea"'.$disable))
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_MEMO),
          array('text' => tep_draw_textarea_field('memo', 'soft', '30', '5', '', 'style="resize: vertical;font-size:12px"onblur="o_submit_single = true;" onfocus="o_submit_single = false;" class="pw_textarea"'.$disable))
      );
      if($disable){
        $open_new_calendar = '<a class="dpicker"></a>';
      }else{
        $open_new_calendar = '<a href="javascript:void(0);" onclick="open_new_calendar();" class="dpicker"></a>';
      }
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_NEXTDATE),
          array('text' => '<div class="nextdate_info"><div class="yui3-skin-sam yui3-g">'.  tep_draw_input_field('nextdate','','id="input_nextdate"'.$disable).$open_new_calendar.'<input type="hidden" name="toggle_open" value="0" id="toggle_open"> <div class="yui3-u" id="new_yui3"><div id="mycalendar"></div></div>' ."</div>")
      );
      $contents[]['text'] = array(
          array('text' => TEXT_INFO_PRIVILEGE),
          array('params' => 'calss="td_input"','text' => tep_draw_radio_field('privilege','15',false,'','id="self" class="privilege" onclick="self_radio()"'.$disable).TEXT_SELF.  tep_draw_radio_field('privilege','7',true,'','class="privilege" id="privilege_s" onclick="privilege_s_radio()"'.$disable).TEXT_PERMISSION_STAFF.  tep_draw_radio_field('privilege','10',false,'','class="privilege" id="privilege_c" onclick="privilege_c_radio()"').TEXT_PERMISSION_CHIEF)
      );
      $contents[]['text'] = array(
          array('text' => ''),
          array('text' => TEXT_OPERATOR_INFO)
      );
      $selected_user = $ocertify->auth_user;
      $contents[]['text'] = array( 
          array('text' => ''),
          array('text' => '<div id="user_select" class="user_select" style="display:none">'.tep_get_user_select($selected_user).'</div>')
      );
     $contents[]['text'] = array(
           array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA), 
           array('align' => 'left', 'params' => 'width="70%"', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA)
         );
     $contents[]['text'] = array(
           array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA),
           array('align' => 'left', 'params' => 'width="70%"', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA)
      );
      $button[] = "<input ".$disable." style='font-size:12px' type='button' value='".IMAGE_SAVE."' onclick=\"valdata('".$ocertify->npermission."')\" id='button_save'>" .  '&nbsp;' .  "<input style='font-size:12px' type='button' ".$disable."  onclick='hidden_info_box()' value='".TEXT_BUTTON_CLEAR."'>"; 
      if(!empty($button)){
       $buttons = array('align' => 'center', 'button' => $button);  
      }
      $form_str = tep_draw_form('pw_manager', FILENAME_PW_MANAGER, '&site_id='.$_GET['site_id'].'&page=' . $_GET['page'] . '&type='.$_GET['type'].'&sort='.$_GET['sort'].'&action=insert', 'post', 'enctype="multipart/form-data"');
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
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
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
                             ".TABLE_IDPW_LOG." where idpw_id='".$_GET['pw_id']."' order by ".$order_str;
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
           array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($pwInfo->operator))?$pwInfo->operator:TEXT_UNSET_DATA)), 
           array('align' => 'left', 'params' => 'width="70%"', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($pwInfo->created_at))?$pwInfo->created_at:TEXT_UNSET_DATA))
         );
       $contents[]['text'] = array(
           array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($pwInfo->update_user))?$pwInfo->update_user:TEXT_UNSET_DATA)),
           array('align' => 'left', 'params' => 'width="70%"', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($pwInfo->updated_at))?$pwInfo->updated_at:TEXT_UNSET_DATA))
        );
 
      if($ocertify->npermission >= 15){
        if(isset($disable) && $disable){
         $button[] = tep_html_element_button(TEXT_BUTTON_DELETE,$disable);
        }else{
          $button[] = '<a href="javascript:void(0)"> '.tep_html_element_button(TEXT_BUTTON_DELETE,'onclick="toggle_idpw_log_action(\''.tep_href_link(FILENAME_PW_MANAGER,'action=deleteconfirm&log=id_manager_log&pw_l_id='.$pwInfo->id.'&'.tep_get_all_get_params(array('pw_l_id','action','search_type','keywords'))).'\', \''.$ocertify->npermission.'\');"').'</a>';
        }
      }
      if(!empty($button)){
        $buttons = array('align' => 'center', 'button' => $button);
      }
      $notice_box->get_heading($heading);
      $notice_box->get_contents($contents, $buttons);
      $notice_box->get_eof(tep_eof_hidden());
      echo $notice_box->show_notice();
 
}else if ($_GET['action'] == 'edit_customers'){
include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_CUSTOMERS);
include(DIR_FS_ADMIN.'classes/notice_box.php');
$notice_box = new notice_box('popup_order_title', 'popup_order_info');
$search = '';
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
$action_sid = $_GET['action_sid'];
while($userslist= tep_db_fetch_array($sites_id)){
     $site_permission = $userslist['site_permission']; 
}
if(isset($site_permission)) $site_arr=$site_permission;//权限判断
else $site_arr="";
$site_array = explode(',',$site_arr);
if(!in_array($action_sid,$site_array) && $action_sid != -1){
   $disabled = 'disabled="disabled"'; 
}
if($_GET['site_id'] == -1){
  $_GET['site_id'] = '';
}
if ( isset($_GET['search']) && ($_GET['search']) && (tep_not_null($_GET['search'])) ) {
    $keywords = tep_db_input(tep_db_prepare_input($_GET['search']));
    $keywords = explode(" ",$keywords);
    $key_search = '';
    $i = 0;
    foreach($keywords as $key => $key_value){
     $key_search .= 'c.customers_lastname like \'%'.$key_value.'%\' or c.customers_firstname like \'%'.$key_value.'%\' or c.customers_firstname_f like \'%'.$key_value.'%\'or c.customers_lastname_f like \'%'.$key_value.'%\'or ';
     $i ++;
  }
    $search = "and (".$key_search." c.customers_email_address like '%" .  trim($_GET['search']) . "%' or c.customers_id = '".trim($_GET['search'])."')";
}
    if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
      $sql_site_where = 'site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
    } else {
      $show_site_str = tep_get_setting_site_info(FILENAME_CUSTOMERS);
      $sql_site_where = 'site_id in ('.$show_site_str.')';
    }
    $sites_sql = tep_db_query("SELECT * FROM `sites`");
    while($sites_row = tep_db_fetch_array($sites_sql)){
      $show_site_arr[] = $sites_row['id']; 
    }
    $customers_order_sort_name = ' c.customers_id'; 
    $customers_order_sort = 'desc'; 
    if (!empty($_GET['customers_sort'])) {
      switch ($_GET['customers_sort']) {
        case 'site_id':
          $customers_order_sort_name = ' s.romaji'; 
          break;
        case 'm_type':
          $customers_order_sort_name = ' c.customers_guest_chk'; 
          break;
        case 'has_exit':
          $customers_order_sort_name = ' c.is_exit_history'; 
          break;
        case 'lastname':
          $customers_order_sort_name = ' c.customers_lastname'; 
          break;
        case 'firstname':
          $customers_order_sort_name = ' c.customers_firstname'; 
          break;
        case 'create_at':
          $customers_order_sort_name = ' date_account_created'; 
          break;
        case 'update_at':
          $customers_order_sort_name = ' date_account_last_modified'; 
          break;
      }
    }
    if (!empty($_GET['customers_sort_type'])) {
      if ($_GET['customers_sort_type'] == 'asc') {
        $customers_order_sort = 'asc'; 
      } else {
        $customers_order_sort = 'desc'; 
      }
    }
    $customers_order_sql = $customers_order_sort_name.' '.$customers_order_sort; 
    $customers_query_raw = "
      select c.customers_id, 
             c.site_id,
             c.customers_lastname, 
             c.customers_firstname, 
             c.customers_email_address, 
             a.entry_country_id, 
             c.customers_guest_chk,
	     c.is_quited,
	     ci.user_update,
             ci.customers_info_date_account_created as date_account_created, 
             ci.customers_info_date_account_last_modified as date_account_last_modified, 
             ci.customers_info_date_of_last_logon as date_last_logon, 
             ci.customers_info_number_of_logons as number_of_logons,
             c.is_exit_history,
             s.romaji
      from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " a on
      c.customers_id = a.customers_id and c.customers_default_address_id =
      a.address_book_id, ".TABLE_CUSTOMERS_INFO." ci, ".TABLE_SITES." s where c.customers_id = ci.customers_info_id and c.site_id = s.id and " .$sql_site_where. " " . $search . " 
      order by ".$customers_order_sql;
    $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows); 
  
    $customers_query_cid = tep_db_query($customers_query_raw);
    $cid_array = array();
    while ($customers_cid = tep_db_fetch_array($customers_query_cid)) {
        $cid_array[] = $customers_cid['customers_id'];
        $site_id_array[] = $customers_cid['site_id'];
      if ( ((!isset($_GET['cID']) || !$_GET['cID']) || (@$_GET['cID'] == $customers_cid['customers_id'])) && (!isset($cInfo) || !$cInfo)) {
        $country_query = tep_db_query(" select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . $customers_cid['entry_country_id'] . "'
        ");
        $country = tep_db_fetch_array($country_query);

        $reviews_query = tep_db_query(" select count(*) as number_of_reviews from " . TABLE_REVIEWS . " where customers_id = '" . $customers_cid['customers_id'] . "'");
        $reviews = tep_db_fetch_array($reviews_query);
        $customer_info = tep_array_merge($country, $customers_cid, $reviews);
        $cInfo_array = tep_array_merge($customers, $customer_info);
        $nInfo = new objectInfo($cInfo_array);
      }
    }
    $customers_query = tep_db_query("
        select c.customers_id, 
               c.customers_gender, 
               c.customers_firstname, 
               c.customers_lastname, 
               c.customers_firstname_f, 
               c.customers_lastname_f, 
               c.customers_dob, 
               c.customers_email_address, 
               a.entry_company, 
               a.entry_street_address, 
               a.entry_suburb, 
               a.entry_postcode, 
               a.entry_city, 
               a.entry_state, 
               a.entry_zone_id, 
               a.entry_country_id, 
               c.customers_telephone, 
               c.customers_fax, 
               c.customers_newsletter, 
               c.customers_default_address_id,
               c.is_seal,
	       c.is_quited,
	       c.quited_date,
	       c.pic_icon,
	       c.is_active,
	       c.is_send_mail,
	       c.is_calc_quantity,
               c.customers_guest_chk,
               c.site_id,
               s.romaji,
               s.name as site_name
        from " . TABLE_CUSTOMERS . " c 
          left join " . TABLE_ADDRESS_BOOK . " a on c.customers_default_address_id = a.address_book_id ,".TABLE_SITES." s
        where a.customers_id = c.customers_id 
          and s.id = c.site_id
          and c.customers_id = '" . (int)$_GET['cID'] . "' 
    ");
    $customers_lastname_row = tep_db_fetch_array(tep_db_query("select * from " .  TABLE_CUSTOMERS . " where customers_id ='".(int)$_GET['cID']."'"));
    if($_GET['cID'] != -1){
    $customers_info = tep_db_query("select * from ".TABLE_CUSTOMERS_INFO." where customers_info_id=".$_GET['cID']);  
    $customers_info_row = tep_db_fetch_array($customers_info);
    }
    $customers = tep_db_fetch_array($customers_query);
    $cInfo = new objectInfo($customers);
    $newsletter_array = array(array('id' => '1', 'text' => ENTRY_NEWSLETTER_YES),
                              array('id' => '0', 'text' => ENTRY_NEWSLETTER_NO));

    include_once(DIR_WS_CLASSES . 'address_form.php');
    $address_form = new addressForm;
    // gender
    $customers_gender_html = tep_draw_radio_field('customers_gender', 'm', false, $cInfo->customers_gender) . '&nbsp;&nbsp;' . MALE . '&nbsp;&nbsp;'
    . tep_draw_radio_field('customers_gender', 'f', false, $cInfo->customers_gender) . '&nbsp;&nbsp;' . FEMALE;
    $address_form->setFormLine('gender',ENTRY_GENDER,$customers_gender_html);

    // firstname
    $customers_firstname_html = tep_draw_input_field('customers_firstname', $cInfo->customers_firstname, 'maxlength="32"', false);
    $address_form->setFormLine('firstname',ENTRY_FIRST_NAME,$customers_firstname_html);

    // lastname
    $customers_lastname_html = tep_draw_input_field('customers_lastname', $cInfo->customers_lastname, 'maxlength="32"', false);
    $address_form->setFormLine('lastname',ENTRY_LAST_NAME,$customers_lastname_html);
  
  // firstname_f
    $customers_firstname_f_html = tep_draw_input_field('customers_firstname_f', $cInfo->customers_firstname_f, 'maxlength="32"', false);
    $address_form->setFormLine('firstname_f',ENTRY_FIRST_NAME_F,$customers_firstname_f_html);

    // lastname_f
    $customers_lastname_f_html = tep_draw_input_field('customers_lastname_f', $cInfo->customers_lastname_f, 'maxlength="32"', false);
    $address_form->setFormLine('lastname_f',ENTRY_LAST_NAME_F,$customers_lastname_f_html);

    // dob
    $customers_dob_html = tep_draw_input_field('customers_dob', tep_date_short($cInfo->customers_dob), 'maxlength="10"', false);
    $address_form->setFormLine('dob',ENTRY_DATE_OF_BIRTH,$customers_dob_html);

    // email_address
    $customers_email_address_html = tep_draw_input_field('customers_email_address', $cInfo->customers_email_address, 'maxlength="96"', false);
    $address_form->setFormLine('email_address',ENTRY_EMAIL_ADDRESS,$customers_email_address_html);
    //quited_date
    if($cInfo->is_quited==1){
    $quited_date_html = date("Y/m/d H:i",strtotime($cInfo->quited_date));

    $address_form->setFormLine('quited_date',ENTRY_QUITED_DATE,$quited_date_html);
    }
    // company
    $entry_company_html = tep_draw_input_field('entry_company', $cInfo->entry_company, 'maxlength="32"');
    $address_form->setFormLine('company',ENTRY_COMPANY,$entry_company_html);

    // street_address
    $entry_street_address_html = tep_draw_input_field('entry_street_address', $cInfo->entry_street_address, 'maxlength="64"', true);
    $address_form->setFormLine('street_address',ENTRY_STREET_ADDRESS,$entry_street_address_html);

    // suburb
    $entry_suburb_html = tep_draw_input_field('entry_suburb', $cInfo->entry_suburb, 'maxlength="32"');
    $address_form->setFormLine('suburb',ENTRY_SUBURB,$entry_suburb_html);

    // postcode
    $entry_postcode_html = tep_draw_input_field('entry_postcode', $cInfo->entry_postcode, 'maxlength="8"', true);
    $address_form->setFormLine('postcode',ENTRY_POST_CODE,$entry_postcode_html);

    // city
    $entry_city_html = tep_draw_input_field('entry_city', $cInfo->entry_city, 'maxlength="32"', true);
    $address_form->setFormLine('city',ENTRY_CITY,$entry_city_html);
    $address_form->setCountry($cInfo->entry_country_id);
    $entry_country_id_html = tep_draw_pull_down_menu('entry_country_id', tep_get_countries(), $cInfo->entry_country_id, 'onChange="update_zone(this.form);"');
    $address_form->setFormLine('country',ENTRY_COUNTRY,$$entry_country_id_html);
    $a_hidden = tep_draw_hidden_field('entry_country_id',$cInfo->entry_country_id);
    $address_form->setFormHidden('country',$a_hidden); // in case without country
    $a_hidden = tep_draw_hidden_field('user_update',$user_info['name']);
    $address_form->setFormHidden('user_update',$a_hidden);
    // state
    $entry_zone_id_html = tep_draw_pull_down_menu('entry_zone_id', tep_prepare_country_zones_pull_down($cInfo->entry_country_id), $cInfo->entry_zone_id, 'onChange="resetStateText(this.form);"');
    $address_form->setFormLine('zone_id',ENTRY_STATE,$entry_zone_id_html);
    $entry_state_html = tep_draw_input_field('entry_state', $cInfo->entry_state, 'maxlength="32" onChange="resetZoneSelected(this.form);"');
    $address_form->setFormLine('state','&nbsp;',$entry_state_html);
    if($_GET['cID'] == -1){
      $action = 'insert';
      $page = 'page='.$_GET['page'];
    }else{
      $action = 'update';
      if (empty($_GET['customers_sort']) || empty($_GET['customers_sort_type'])) {
        $page = tep_get_all_get_params(array('action', 'customers_sort', 'customers_sort_type'));
      } else {
        $page = tep_get_all_get_params(array('action'));
      }
    }
    $form_str = tep_draw_form('customers', FILENAME_CUSTOMERS, $page. '&action='.$action, 'post', 'onSubmit="return check_form();"') .  tep_draw_hidden_field('default_address_id', $cInfo->customers_default_address_id) .  tep_draw_hidden_field('entry_country_id', $cInfo->entry_country_id)."\n"; 
    $page_str = '';
  foreach ($cid_array as $c_key => $c_value) {
    if ($_GET['cID'] == $c_value) {
      break; 
    }
  }
 $is_actvie_single = ''; 
 if (isset($cInfo->is_active)) {
   if ($cInfo->is_active == '0') {
     $is_active_single = 'disabled="disabled"'; 
   }
 }
 if($_GET['cID'] != '-1'){
 if($action_sid != 0 || !isset($action_sid)){
      $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$cInfo->site_id));
      $site_id_name = $site_name['romaji'].'<input id=\'customers_site_id\' name="site_id" type="hidden" value="'.$site_name['id'].'">';
 }
 }else{
   $customers_site_arr = array_intersect($show_site_arr,$site_array);
   $site_id_name = "<select id='customers_site_id' name='site_id' $disabled>";
   foreach($customers_site_arr as $value){
     if($value!=0){
       $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$value));
       $site_id_name .= "<option value='".$site_name['id'] ."'>".$site_name['name']."</option>";
     }
   }
   $site_id_name .= "</select>";
   $site_id_name .= '&nbsp;<font color="#ff0000;">*'.TEXT_REQUIRED.'</font>'; 
 }
  $page_str = '';
if($_GET['cID'] != -1){
  if ($c_key > 0) {
    $prev_customer_query = tep_db_query("select customers_id, site_id from ".TABLE_CUSTOMERS." where customers_id = '".$cid_array[$c_key-1]."'"); 
    $prev_customer_res = tep_db_fetch_array($prev_customer_query); 
    $page_str .= '<a onclick="show_customers(\'\','.$cid_array[$c_key-1].','.$_GET['page'].', '.$prev_customer_res['site_id'].', \''.(isset($_GET['customers_sort'])?$_GET['customers_sort']:'0').'\', \''.(isset($_GET['customers_sort_type'])?$_GET['customers_sort_type']:'0').'\')" href="javascript:void(0)" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  } else {
    $page_str .= '<font color="#000000"><'.IMAGE_PREV.'</font>'; 
  }
  if ($c_key < (count($cid_array) - 1)) {
    $next_customer_query = tep_db_query("select customers_id, site_id from ".TABLE_CUSTOMERS." where customers_id = '".$cid_array[$c_key+1]."'"); 
    $next_customer_res = tep_db_fetch_array($next_customer_query); 
    $page_str .= '<a onclick="show_customers(\'\','.$cid_array[$c_key+1].','.$_GET['page'].', '.$next_customer_res['site_id'].', \''.(isset($_GET['customers_sort'])?$_GET['customers_sort']:'0').'\', \''.(isset($_GET['customers_sort_type'])?$_GET['customers_sort_type']:'0').'\')" href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  } else {
    $page_str .= '<font color="#000000">'.IMAGE_NEXT.'></font>'; 
  }
}
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading = array();
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => htmlspecialchars(html_entity_decode(($_GET['cID'] != -1?$customers_lastname_row['customers_firstname'].$customers_lastname_row['customers_lastname']:HEADING_TITLE))).'&nbsp;&nbsp;');
    $heading[] = array('align' => 'right', 'text' => $page_str);
    if($_GET['cID'] != -1){
    if(isset($cInfo->customers_guest_chk) && $cInfo->customers_guest_chk == 0){
         $guest_member = 'checked=""';
    }else{
         $guest_no_member = 'checked=""';
    }
    }else{
         $guest_no_member = 'checked=""';
    }
     $customers_guest_row = array();
     $customers_gues_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0');
    if($_GET['cID'] == -1){
    $customers_guest_row[]['text'] = array(
         array('params' => 'colspan="3"','text' => '<input type="hidden" id="check_is_active" value="1">')
       );
    }else{
     $customers_guest_row[]['text'] = array(
         array('params' => 'colspan="3"','text' => '<input type="hidden" id="check_is_active" value="0">')
       );
    }

    if($_GET['cID'] == -1){
    $customers_guest_row[]['text'] = array(
         array('params' => 'nowrap="nowrap" width="30%"','text' => '<input type="hidden" id="hidden_cid" value="'.$_GET['cID'].'"><input type="hidden" id="hidden_page" value="'.$_GET['page'].'">'.TEXT_GUEST_CHK),
         array('text' => '<input type="radio" '.($disabled?$disabled:$is_active_single).' '.$guest_no_member.' name="guest_radio"  value="1" onclick="check_guest(this.value)">'.TEXT_NO_MEMBER.'<input '.$guest_member.'type="radio"  name="guest_radio" value="0" '.($disabled?$disabled:$is_active_single).' onclick="check_guest(this.value)">'.TEXT_MEMBER)
       );
    }else{
      if(isset($cInfo->customers_guest_chk) && $cInfo->customers_guest_chk == 0){
        $text_guest_member = TEXT_MEMBER; 
      }else{
        $text_guest_member = TEXT_NO_MEMBER; 
      }
     $customers_guest_row[]['text'] = array(
         array('params' => 'nowrap="nowrap" width="30%"','text' => '<input type="hidden" id="hidden_cid" value="'.$_GET['cID'].'"><input type="hidden" id="hidden_page" value="'.$_GET['page'].'">'.TEXT_GUEST_CHK),
         array('text' => $text_guest_member)
       );
    }
    $customers_guest_row[]['text'] = array(
         array('params' => 'nowrap="nowrap" width="30%"','text' => ENTRY_SITE),
         array('text' => $site_id_name)
       );
    $customers_guest_row[]['text'] = array(
         array('params' => 'nowrap="nowrap"','text' => ENTRY_FIRST_NAME),
         array('text' => tep_draw_input_field('customers_firstname',html_entity_decode($customers_lastname_row['customers_firstname']), 'id="customers_firstname"style="width:60%"  onfocus="o_submit_single = false;" onblur="o_submit_single = true;"'.($disabled?$disabled:$is_active_single), false).'<br><span id="customers_firstname_error"></span>')
       );
    $customers_guest_row[]['text'] = array(
         array('params' => 'nowrap="nowrap"','text' => ENTRY_LAST_NAME),
         array('text' => tep_draw_input_field('customers_lastname', html_entity_decode($customers_lastname_row['customers_lastname']), 'id="customers_lastname" style="width:60%"  onfocus="o_submit_single = false;" onblur="o_submit_single = true;"'.($disabled?$disabled:$is_active_single), false).'<br><span id="customers_lastname_error"></span>')
       );
      $customers_guest_row[]['text'] = array(
         array('params' => 'nowrap="nowrap"','text' => ENTRY_EMAIL_ADDRESS),
         array('text' => tep_draw_input_field('customers_email_address', html_entity_decode($cInfo->customers_email_address), 'id="customers_email_address" style="width:60%" maxlength="96" onfocus="o_submit_single = false;" onblur="o_submit_single = true;"'.($disabled?$disabled:$is_active_single), false).'<br><span id="error_email"></span><span id="check_email"></span> <span id="error_email_info"></span><input type="hidden" id="customers_email_address_value" value="'.$cInfo->customers_email_address.'">')
       );
     $customers_guest_str = $notice_box->get_table($customers_guest_row, '', $customers_guest_params);  
     $contents[]['text'] = array(
         array('params' => 'width="100%" colspan="3"','text' => $customers_guest_str)
      );
     if($_GET['cID'] == -1){
     $customers_info_row = array();
     $customers_info_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0', 'parameters' => 'id="password_hide"');
     $customers_info_row[]['text'] = array(
         array('params' => 'nowrap="nowrap" width="30%"','text' => TEXT_PASSWORD),
         array('text' => tep_draw_password_field('password','','','id="password" style="width:60%" onfocus="o_submit_single = false;" onblur="o_submit_single = true;"'.($disabled?$disabled:$is_active_single)).'<br><span id="error_info_f"></span>')
       );
      $customers_info_row[]['text'] = array(
         array('params' => 'nowrap"','text' => TEXT_ONCE_AGAIN_PASSWORD),
         array('text' => tep_draw_password_field('once_again_password','','','id="once_again_password" style="width:60%"onfocus="o_submit_single = false;" onblur="o_submit_single = true;"'.$disabled).'<br><span id="error_info_o"></span>')
       );
      $customers_info_str = $notice_box->get_table($customers_info_row, '', $customers_info_params);  
       $contents[]['text'] = array(
         array('params' => 'width="100%" colspan="3"','text' => $customers_info_str)
       );
  }
     $customers_newsletter_row = array();
     $customers_newsletter_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0');
     $customers_newsletter_row[]['text'] = array(
         array('params' => 'nowrap="nowrap" width="30%"','text' => ENTRY_NEWSLETTER),
         array('text' => '<span>'.tep_draw_pull_down_menu('customers_newsletter', $newsletter_array, $cInfo->customers_newsletter,($disabled?$disabled:$is_active_single)).'</span>')
       );
      if ($cInfo->is_quited == 1) {
       $customers_newsletter_row[]['text'] = array(
         array('params' => 'nowrap="nowrap"','text' => ENTRY_QUITED_DATE),
         array('text' => '<span class="table_space_left">'.date("Y/m/d H:i", strtotime($cInfo->quited_date)).'</span>')
       );
      }

      $customers_newsletter_str = $notice_box->get_table($customers_newsletter_row, '', $customers_newsletter_params);  
       $contents[]['text'] = array(
         array('params' => 'width="100%" colspan="3"','text' => $customers_newsletter_str)
       );
    if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
    $cpoint_query = tep_db_query("select point ,reset_flag,reset_success from " . TABLE_CUSTOMERS . " where customers_id = '".$_GET['cID']."'");
    $cpoint = tep_db_fetch_array($cpoint_query);
    if($cInfo->customers_guest_chk == 0){
     $customers_reset_row = array();
     $customers_reset_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0','parameters' => 'id="reset_flag_hide"');
     $customers_reset_row[]['text'] = array(
       array('params' => 'width="30%" nowrap="nowrap"','text' => CUSTOMER_RESET),
       array('params' => 'class="td_input"','text' => tep_draw_checkbox_field('reset_flag', 'on', $cpoint['reset_flag']==1 and $cpoint['reset_success']!=1,'',($disabled?$disabled:$is_active_single) ))
       );
     $customers_reset_str = $notice_box->get_table($customers_reset_row, '', $customers_reset_params);  
       $contents[]['text'] = array(
         array('params' => 'width="100%" colspan="3"','text' => $customers_reset_str)
       );
    }
     $customers_seal_row = array();
     $customers_seal_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0');
       $customers_seal_row[]['text'] = array(
         array('params' => 'nowrap="nowrap" width="30%"','text' => CUSTOMER_IS_SEAL),
         array('params' => 'class="td_input"','text' => tep_draw_checkbox_field('is_seal', '1', $cInfo->is_seal,'',($disabled?$disabled:$is_active_single)))
       );
       if($cInfo->is_send_mail){
          $checked = 'checked'; 
       }
        $customers_seal_row[]['text'] = array(
         array('params' => 'nowrap="nowrap"','text' => CUSTOMER_NO_SEND_MAIL_TEXT),
         array('params' => 'class="td_input"','text' => '<input type="checkbox" name="is_send_mail" '.($disabled?$disabled:$is_active_single).$checked.' value="1">')
       );
       if($cInfo->is_calc_quantity){
          $calc_checked = 'checked'; 
       }
       $customers_seal_row[]['text'] = array(
         array('text' => CUSTOMER_CALC_QUANTITY_TEXT),
         array('params' => 'class="td_input"','text' => '<input type="checkbox" name="is_calc_quantity" '.($disabled?$disabled:$is_active_single).$calc_checked.' value="1">')
       );
      $customers_seal_str = $notice_box->get_table($customers_seal_row, '', $customers_seal_params);  
       $contents[]['text'] = array(
         array('params' => 'width="100%" colspan="3"','text' => $customers_seal_str)
       );
    if($cInfo->customers_guest_chk == 0){  
     $customers_point_row = array();
     $customers_point_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0','parameters' => 'id="point_hide"');
     $customers_point_row[]['text'] = array(
         array('params' => 'width="30%"','text' => ENTRY_POINT),
         array('text' => tep_draw_input_field('point', $cpoint['point'], 'maxlength="32" size="4" style="text-align:right"'.($disabled?$disabled:$is_active_single)).'P')
       );
     $customers_point_str = $notice_box->get_table($customers_point_row, '', $customers_point_params);  
       $contents[]['text'] = array(
         array('params' => 'width="100%" colspan="3"','text' => $customers_point_str)
       );
    }
       $pic_list_raw = tep_db_query("select * from ".TABLE_CUSTOMERS_PIC_LIST." order by sort_order asc"); 
        $table_img_list = '<ul class="table_img_list" style="width:100%">'; 
        $pic_icon_array = explode(',',$cInfo->pic_icon);
        while ($pic_list_res = tep_db_fetch_array($pic_list_raw)) {
         if(in_array($pic_list_res['pic_name'],$pic_icon_array)){
            $table_img_list .= '<li><input type="checkbox" name="pic_icon[]" '.($disabled?$disabled:$is_active_single).'checked style="padding-left:0;margin-left:0;" value="'.$pic_list_res['pic_name'].'" checked onclick="check_radio_status(this);"><img src="images/icon_list/'.$pic_list_res['pic_name'].'" alt="'.$pic_list_res['pic_alt'].'" title="'.$pic_list_res['pic_alt'].'"></li>'; 
         }else{
         $table_img_list .= '<li><input type="checkbox" name="pic_icon[]" '.($disabled?$disabled:$is_active_single).' style="padding-left:0;margin-left:0;" value="'.$pic_list_res['pic_name'].'" onclick="check_radio_status(this);"><img src="images/icon_list/'.$pic_list_res['pic_name'].'" alt="'.$pic_list_res['pic_alt'].'" title="'.$pic_list_res['pic_alt'].'"></li>'; 
         }
        }
        $table_img_list .='</ul>'; 
        $customers_fax_row = array();
        $customers_fax_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0');
        $customers_fax_row[]['text'] = array(
         array('params' =>'width="30%"','text' => CUSTOMER_PIC_TEXT),
         array('params' => 'class="td_input"','text' => $table_img_list.'<input type="hidden" id="s_radio" nacIDme="s_radio" value="'.$cInfo->pic_icon.'">')
       );
       if(isset($_POST['customers_fax'])){
          $customers_fax = $_POST['customers_fax']; 
        }else{
          $customers_fax = $cInfo->customers_fax;
        }
        $customers_fax_row[]['text'] = array(
         array('params' => 'nowrap="nowrap" width="30%"','text' => CUSTOMER_COMMUNITY_SEARCH_TEXT),
         array('text' => '<textarea '.($disabled?$disabled:$is_active_single).' name="customers_fax" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="resize: vertical;width:60%;height:42px;*height:40px;">'.html_entity_decode($customers_fax).'</textarea>')
       );
      }
        $customers_fax_row[]['text'] = array(
         array('params' => 'nowrap="nowrap"','text' => TEXT_INFO_DATE_LAST_LOGON),
         array('text' => tep_date_short($nInfo->date_last_logon))
       );
        $customers_fax_row[]['text'] = array(
         array('params' => 'nowrap="nowrap"','text' => TEXT_INFO_NUMBER_OF_LOGONS),
         array('text' => $nInfo->number_of_logons)
       );
        $customers_fax_row[]['text'] = array(
         array('params' => 'nowrap="nowrap"','text' => TEXT_CUSTOMERS_ORDER_COUNT),
         array('text' => tep_get_orders_by_customers_id($nInfo->customers_id,$nInfo->site_id))
       );
        //预约次数
        $customers_fax_row[]['text'] = array(
         array('params' => 'nowrap="nowrap"','text' => TEXT_CUSTOMERS_PREORDERS_SUM),
         array('text' => tep_get_preorders_by_customers_id($nInfo->customers_id,$nInfo->site_id))
       );
        $customers_fax_row[]['text'] = array(
         array('params' => 'nowrap="nowrap"','text' => TEXT_INFO_NUMBER_OF_REVIEWS),
         array('text' => $nInfo->number_of_reviews)
       );
       $customers_fax_row[]['text'] = array(
           array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($customers_info_row['user_added']))?$customers_info_row['user_added']:TEXT_UNSET_DATA)), 
           array('align' => 'left','text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($customers_info_row['customers_info_date_account_created']))?$customers_info_row['customers_info_date_account_created']:TEXT_UNSET_DATA))
         );
       $customers_fax_row[]['text'] = array(
           array('align' => 'left', 'params' => 'width="30%"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($customers_info_row['user_update']))?$customers_info_row['user_update']:TEXT_UNSET_DATA)),
           array('align' => 'left','text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($customers_info_row['customers_info_date_account_last_modified']))?$customers_info_row['customers_info_date_account_last_modified']:TEXT_UNSET_DATA))
        );
     $customers_fax_str = $notice_box->get_table($customers_fax_row, '', $customers_fax_params);  
       $contents[]['text'] = array(
         array('params' => 'width="100%" colspan="3"','text' => $customers_fax_str)
       );
 
      if($disabled){
        if ($cInfo->is_active != '0') {
          $submit = '<input type="hidden" id="cid" value="'.$_GET['cID'].'">'.tep_html_element_button(IMAGE_SAVE,$disabled); 
        }
       }else{
         if (!isset($cInfo->is_active)) {
           $submit = '<input type="hidden" id="cid" value="'.$_GET['cID'].'">'.tep_html_element_button(IMAGE_SAVE,'id="button_save" onclick="check_password(\'3\', \''.$ocertify->npermission.'\')"'); 
         } else if ($cInfo->is_active != '0') {
           $submit = '<input type="hidden" id="cid" value="'.$_GET['cID'].'">'.tep_html_element_button(IMAGE_SAVE,'id="button_save" onclick="check_password(\'3\', \''.$ocertify->npermission.'\')"'); 
         }
       }
   if($_GET['cID'] != -1){
    if($disabled){
     $customers_del = tep_html_element_button(IMAGE_DELETE,$disabled);
     if ($cInfo->is_active == '1') {
       if ($ocertify->npermission >= 15) {
         $customers_orders = tep_html_element_button(IMAGE_ORDERS,$disabled);
         $customers_orders .= tep_html_element_button(BUTTON_EXIT_HISTORY_TEXT,$disabled);
         $customers_products = tep_html_element_button(BUTTON_CUSTOMERS_PRODUCTS_TEXT,$disabled);
       } 
       $customers_email = tep_html_element_button(IMAGE_EMAIL,$disabled);
     } 
    }else{
     if (empty($_GET['customers_sort']) || empty($_GET['customers_sort_type'])) {
        $tmp_ex_array = array('cID', 'action', 'customers_sort', 'customers_sort_type');
      } else {
        $tmp_ex_array = array('cID', 'action');
      }
     $customers_del =  ' <a class = "new_product_reset" href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="'.(tep_get_orders_by_customers_id($nInfo->customers_id,$nInfo->site_id) > 0 || tep_get_preorders_by_customers_id($nInfo->customers_id,$nInfo->site_id) > 0 ? 'if(confirm(\''.TEXT_CUSTOMERS_DELETE_CONFIRM_INFO.'\n'.tep_customers_name($nInfo->customers_id).'\')){if(confirm(\''.TEXT_DEL_NEWS.'\'))' : 'if (confirm(\''.TEXT_DEL_NEWS.'\')){').' toggle_customers_action(\''.tep_href_link(FILENAME_CUSTOMERS, tep_get_all_get_params($tmp_ex_array) . 'cID=' .  $cInfo->customers_id .  '&action=deleteconfirm').'\', \''.$ocertify->npermission.'\');}"').'</a>';
     if ($cInfo->is_active == '1') {
       if ($ocertify->npermission >= 15) {
         $customers_orders = ' <a href="' .  tep_href_link(FILENAME_ORDERS, 'cID=' .  $cInfo->customers_id) . '">' .  tep_html_element_button(IMAGE_ORDERS) .  '</a>';
         $exit_history_query = tep_db_query("select * from ".TABLE_CUSTOMERS_EXIT_HISTORY." where customers_id = '".$cInfo->customers_id."'"); 
         $exit_history_num = tep_db_num_rows($exit_history_query);
         if ($exit_history_num > 0) {
           $customers_orders .= '&nbsp;<a href="' .  tep_href_link(FILENAME_CUSTOMERS_EXIT_HISTORY, 'customers_id=' .  $cInfo->customers_id) . '">' .  tep_html_element_button(BUTTON_EXIT_HISTORY_TEXT) .  '</a>';
         }
         $customers_products = '&nbsp;<a href="'.tep_href_link('customers_products.php', str_replace('page', 'cpage', tep_get_all_get_params(array('cID', 'action')).'cID='.$cInfo->customers_id)).'">'.tep_html_element_button(BUTTON_CUSTOMERS_PRODUCTS_TEXT).'</a>';
       }
       $customers_email = '&nbsp;<a href="' . tep_href_link(FILENAME_MAIL, 'selected_box=tools&customer=' .  $cInfo->customers_email_address.'&'.tep_get_all_get_params(array('page')).'&customer_page='.$_GET['page']) .  '">' .tep_html_element_button(IMAGE_EMAIL).'</a>';
     }
    }
   }
     if(isset($cInfo->customers_email_address) && $cInfo->customers_email_address != ''){
        if($disabled){
           $orders_products = tep_html_element_button(TEXT_ORDER_MADE,$disabled);
           $preorders_products = tep_html_element_button(TEXT_PREORDER_MADE,$disabled);
        }else{
           $orders_products = '<input type="hidden" name="check_order" value="" id="check_order"><a href="'. tep_href_link('create_order.php','Customer_mail='.$cInfo->customers_email_address.'&site_id='.$cInfo->site_id).'">'.tep_html_element_button(TEXT_ORDER_MADE).'</a>';
           $preorders_products = ' <a href="'.tep_href_link('create_preorder.php','Customer_mail='.$cInfo->customers_email_address.'&site_id='.$cInfo->site_id).'">'.tep_html_element_button(TEXT_PREORDER_MADE).'</a>';
        }
     }else{
     $orders_products = '<input type="hidden" name="check_order" value="" id="check_order"><a href="javascript:void(0)" onclick="check_password(0, \''.$ocertify->npermission.'\')">'.tep_html_element_button(TEXT_KEEP_ORDER,($disabled?$disabled:$is_active_single)).'</a>';
     $preorders_products = '<a href="javascript:void(0)" onclick="check_password(1, \''.$ocertify->npermission.'\')">'.tep_html_element_button(TEXT_KEEP_PREORDER,($disabled?$disabled:$is_active_single)).'</a>';
     }
     $button[] = '<input type="hidden" name="user_update" value="'.$_SESSION['user_name'].'">'.$orders_products.$preorders_products.$customers_orders.$customers_products.$customers_email.($ocertify->npermission >= 15 ? ($customers_del):'').$submit;
    if(!empty($button)){
       $buttons = array('align' => 'center', 'button' => $button);
    }
    $notice_box->get_form($form_str);
    $notice_box->get_heading($heading);
    $notice_box->get_contents($contents, $buttons);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice(); 
}else if ($_GET['action'] == 'edit_buttons') {
/* -----------------------------------------------------
    功能: 显示编辑buttons弹出框
    参数: $_POST['buttons_id'] buttons ID 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_BUTTONS);
  include(DIR_FS_ADMIN.'classes/notice_box.php'); 

  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  //读取buttons的相应数据
  $buttons_id = $_POST['buttons_id'];
  $param_str = $_POST['param_str'];
  $show = $_POST['show'];
  $buttons_query = tep_db_query("select * from ". TABLE_BUTTONS ." where buttons_id='".$buttons_id."'"); 
  $buttons_array = tep_db_fetch_array($buttons_query);
  tep_db_free_result($buttons_query);

  $buttons_id_num_array = array();
  $buttons_id_query = tep_db_query("select * from ". TABLE_BUTTONS ." order by sort_order asc"); 
  while($buttons_id_array = tep_db_fetch_array($buttons_id_query)){

    $buttons_id_num_array[] = $buttons_id_array['buttons_id'];
  }
  tep_db_free_result($buttons_id_query);

  //头部内容
  $heading = array();

  $page_str = '';

  //显示上一个，下一个按钮
  $page_str = '';

  $page_string = $param_str;
  $page_string = isset($page_string) && $page_string != '' ? $page_string : 1;
  $page_num_start = ($page_string-1) * MAX_DISPLAY_SEARCH_RESULTS;
  if(count($buttons_id_num_array) < MAX_DISPLAY_SEARCH_RESULTS){
    $page_num_end = count($buttons_id_num_array)-1; 
  }else{
    $page_num_end = $page_string * MAX_DISPLAY_SEARCH_RESULTS - 1; 
  }
  $buttons_id_page_array = array();
  for($i = $page_num_start;$i <= $page_num_end;$i++){

    $buttons_id_page_array[] = $buttons_id_num_array[$i];
  }
  $buttons_id_num = array_search($buttons_id,$buttons_id_page_array);

  $buttons_id_prev = $buttons_id_page_array[$buttons_id_num - 1];
  $buttons_id_next = $buttons_id_page_array[$buttons_id_num + 1];
  if ($buttons_id_num > 0) {
    $page_str .= '<a id="buttons_prev" onclick="show_link_buttons_info(\''.$buttons_id_prev.'\',\''.$param_str.'\',\''.$show.'\')" href="javascript:void(0);" ><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
 
  if ($buttons_id_num < (count($buttons_id_page_array) - 1)) {
    $page_str .= '<a id="buttons_next" onclick="show_link_buttons_info(\''.$buttons_id_next.'\',\''.$param_str.'\',\''.$show.'\')" href="javascript:void(0);">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }else{
    $page_str .= '<font color="#000000">'.IMAGE_NEXT.'></font>&nbsp;&nbsp;';
  }

  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => mb_strlen($buttons_array['buttons_name']) > 30 ? mb_substr($buttons_array['buttons_name'],0,30,'utf-8').'...' : $buttons_array['buttons_name']);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  //主体内容
  $category_info_row = array();
   
  //编辑buttons项目   
  $disabled = $show == '0' ? ' disabled="disabled"' : '';
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TABLE_HEADING_BUTTONS_NAME.'<input type="hidden" name="buttons_id" value="'.$buttons_array['buttons_id'].'"><input type="hidden" name="param_str" value="'.$param_str.'">'), 
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="text" value="'.$buttons_array['buttons_name'].'" name="buttons_name" class="option_input"'.$disabled.'><span id="buttons_name_error">'.TEXT_FIELD_REQUIRED.'</span>')
     );
 
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TABLE_HEADING_BUTTONS_ORDER), 
       array('align' => 'left', 'params' => 'colspan="2"', 'text' => '<input type="text" style="text-align:right;width:20%;" size="31" value="'.$buttons_array['sort_order'].'" name="sort_order"'.$disabled.'>')
     );

  //作成者，作成时间，更新者，更新时间 
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($buttons_array['user_added'])?$buttons_array['user_added']:TEXT_UNSET_DATA))),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null(tep_datetime_short($buttons_array['date_added'])))?tep_datetime_short($buttons_array['date_added']):TEXT_UNSET_DATA))
      );
   
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($buttons_array['user_update'])?$buttons_array['user_update']:TEXT_UNSET_DATA))),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null(tep_datetime_short($buttons_array['date_update'])))?tep_datetime_short($buttons_array['date_update']):TEXT_UNSET_DATA))
      );
    
  //底部内容
  $buttons = array();

  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_NEW_PROJECT, $disabled.'onclick="create_buttons_info(this);"').'</a>';
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, $disabled.'id="button_save" onclick="edit_buttons_check(\'save\', \''.$ocertify->npermission.'\');"').'</a>'; 
  if($ocertify->npermission >= 15){
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, $disabled.'onclick="if(confirm(\''.TEXT_INFO_DELETE_INTRO.'\')){delete_buttons(\''.$ocertify->npermission.'\');}"').'</a>'; 
  }

  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $form_str = tep_draw_form('edit_buttons', FILENAME_BUTTONS, '', 'post', 'id="edit_buttons_id"');

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($category_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if ($_GET['action'] == 'create_buttons') {
/* -----------------------------------------------------
    功能: 显示新建buttons弹出框
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_BUTTONS);
  include(DIR_FS_ADMIN.'classes/notice_box.php'); 

  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  //头部内容
  $heading = array();

  $page_str = '';

  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => IMAGE_NEW_PROJECT);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  //主体内容
  $category_info_row = array();
   
  //添加buttons项目   
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TABLE_HEADING_BUTTONS_NAME), 
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="text" value="" name="buttons_name" class="option_input"><span id="buttons_name_error">'.TEXT_FIELD_REQUIRED.'</span>')
     );
 
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TABLE_HEADING_BUTTONS_ORDER), 
       array('align' => 'left', 'params' => 'colspan="2"', 'text' => '<input type="text" style="text-align:right;width:20%;" size="31" value="1000" name="sort_order">')
     );
   
  //底部内容
  $buttons = array();

  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'id="button_save" onclick="edit_buttons_check(\'insert\', \''.$ocertify->npermission.'\');"').'</a>'; 

  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $form_str = tep_draw_form('create_buttons', FILENAME_BUTTONS, '', 'post', 'id="create_buttons_id"');

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($category_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if ($_GET['action'] == 'new_user_info'){
/* -----------------------------------------------------
    功能: 创建用户
    参数: 无 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_USERS);
  include(DIR_FS_ADMIN.'classes/notice_box.php');

  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 
  $page_str = '<a onclick="close_user_info();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => NEW_USER_HEADING_TITLE); 
  $heading[] = array('align' => 'right', 'text' => $page_str); 
  
  $buttons = array();
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_user_info(0, 0);" id="button_save"').'</a>'; 
  $buttons = array('align' => 'center', 'button' => $button); 
 
  $new_user_row = array();
  
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_ID_TEXT), 
        array('align' => 'left', 'text' => tep_draw_input_field('userid', '', 'id="userid" style="width:60%;"').'<br><span id="userid_error" style="color:#ff0000;"></span>') 
      );
  
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_USER_INFO_NAME), 
        array('align' => 'left', 'text' => tep_draw_input_field('name', '', 'id="name" style="width:60%;"').'<br><span id="name_error" style="color:#ff0000;"></span>') 
      );
  
  
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_EMAIl_TEXT), 
        array('align' => 'left', 'text' => tep_draw_input_field('user_email', '', 'style="width:60%;" id="user_email"').'<br><span id="email_error" style="color:#ff0000;"></span>') 
      );
  $user_permission_array = array();

 
  $user_permission_str = '';

  if ($ocertify->npermission == '7') {
    $user_permission_str = tep_draw_radio_field('u_permission', 'staff', true).'Staff';
  } else if ($ocertify->npermission == '10') {
    $user_permission_str = tep_draw_radio_field('u_permission', 'staff', true).'Staff&nbsp;'.tep_draw_radio_field('u_permission', 'chief', false).'Chief';
  } else {
    $user_permission_str = tep_draw_radio_field('u_permission', 'staff', true).'Staff&nbsp;'.tep_draw_radio_field('u_permission', 'chief', false).'Chief&nbsp;'.tep_draw_radio_field('u_permission', 'admin', false).'Admin';
  }
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_USER_INFO_PERMISSION), 
        array('align' => 'left', 'params' => 'class="td_input"', 'text' => $user_permission_str) 
      );
  $c_permission_query = tep_db_query("select * from ".TABLE_PERMISSIONS." where userid = '".$ocertify->auth_user."'"); 
  $c_permission_res = tep_db_fetch_array($c_permission_query); 
  $c_permission_array = explode(',', $c_permission_res['site_permission']); 
  if ($ocertify->npermission == 31) {
    $user_site_permission_str = '<input type="checkbox" name="user_permission_info[]" value="0">all&nbsp;';  
  } else {
    $user_site_permission_str = '<input type="checkbox" name="user_permission_info[]" value="0"'.(in_array('0', $c_permission_array)?'':' disabled').'>all&nbsp;';  
  }
  $site_list_query = tep_db_query("select * from ".TABLE_SITES." order by id asc"); 
  while ($site_list_info = tep_db_fetch_array($site_list_query)) {
    if ($ocertify->npermission == 31) {
      $user_site_permission_str .= '<input type="checkbox" name="user_permission_info[]" value="'.$site_list_info['id'].'">'.$site_list_info['romaji'].'&nbsp;';  
    } else {
      $user_site_permission_str .= '<input type="checkbox" name="user_permission_info[]" value="'.$site_list_info['id'].'"'.(in_array($site_list_info['id'], $c_permission_array)?'':' disabled').'>'.$site_list_info['romaji'].'&nbsp;';  
    }
  }
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_USER_INFO_SITE_PERMISSION), 
        array('align' => 'left', 'params' => 'class="td_input"', 'text' => $user_site_permission_str.'<br>'.TABLE_USER_INFO_SITE_PERMISSION_READ) 
      );
  
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_USER_INFO_PASSWORD), 
        array('align' => 'left', 'text' => tep_draw_password_field('user_password', '', false, 'id="user_password" style="width:60%;"').'<br><span id="password_error" style="color:#ff0000;"></span>') 
      );
  $user_letter_query = tep_db_query("select * from ".TABLE_LETTERS." where userid = '' or userid is null");  
  if (tep_db_num_rows($user_letter_query) > 0) {
    $user_calc_str = tep_show_pw_start().'&nbsp;'.tep_draw_input_field('user_rule', '', 'style="width:60%;" id="user_rule"').'<br><span id="rule_error" style="color:#ff0000;"></span>';
    $new_user_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_CALC_TEXT), 
          array('align' => 'left', 'text' => $user_calc_str) 
        );
    
    $new_user_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_ONETIME_PWD), 
          array('align' => 'left', 'text' => tep_draw_input_field('user_onetime', '', 'id="user_onetime" class="readonly" style="width:60%" readonly').'&nbsp;<a href="javascript:void(0);" onclick="user_preview_onetime_pwd();">'.tep_html_element_button(USER_ONETIME_PWD_PREVIEW).'</a>') 
        );
    
    $new_user_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => '&nbsp;'), 
          array('align' => 'left', 'text' => USER_INFO_ONETIME_PWD_READ) 
        );
  } else {
    $new_user_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => '&nbsp;'), 
          array('align' => 'left', 'text' => USER_INFO_NO_ONETIME_PWD_READ) 
        );
  }
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_IP_LIMIT_TEXT), 
        array('align' => 'left', 'text' => tep_draw_textarea_field('ip_limit', 'hard', '30', '10', '', 'style="width:60%;" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="resize: vertical;"')) 
      );

  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_IP_WARN_EMAIL_TEXT), 
        array('align' => 'left', 'text' => tep_draw_textarea_field('ip_limit_warn_email', 'hard', '30', '10', '', 'style="width:60%;" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="resize: vertical;"')) 
      );
  
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_LOGIN_NUM), 
        array('align' => 'left', 'text' => '0') 
      );
  
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_LAST_LOGIN_DATE), 
        array('align' => 'left', 'text' => TEXT_UNSET_DATA) 
      );
  
  $new_user_row[]['text'] = array(
            array('align' => 'left', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA), 
            array('align' => 'left', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA)
      );
      
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA),
        array('align' => 'left', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA)
      );
  
  $form_str = tep_draw_form('new_user_form', FILENAME_USERS, 'action=insert_user_info'); 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($new_user_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  
  echo $notice_box->show_notice().'||||||'.tep_get_note_top_layer(FILENAME_USERS);
}else if ($_GET['action'] == 'edit_user_info'){
/* -----------------------------------------------------
    功能: 编辑用户
    参数: $_POST['user_e_id'] 用户id 
    参数: $_POST['param_str'] 其他参数 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_USERS);
  include(DIR_FS_ADMIN.'classes/notice_box.php');

  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  $page_str = ''; 
  $user_array = array(); 
  $param_str = '';
  $is_disabled_single = false;
  $c_user_permission_raw = tep_db_query("select * from ".TABLE_PERMISSIONS." where userid = '".$ocertify->auth_user."'");
  $c_user_permission_res = tep_db_fetch_array($c_user_permission_raw); 
  if ($ocertify->npermission != 31) {
    $tmp_s_array = explode(',', $c_user_permission_res['site_permission']); 
    if (!in_array('0', $tmp_s_array)) {
      $is_disabled_single = true;
    }
  }
  foreach ($_POST as $p_key => $p_value) {
    if (($p_key != 'user_e_id') && ($p_key != 'action')) {
      $param_str .= $p_key.'='.$p_value.'&'; 
    }
  }
  $param_str = substr($param_str, 0, -1); 
 
  $user_info_query = tep_db_query("select * from ".TABLE_USERS." where userid = '".$_POST['user_e_id']."'");
  $user_info_res = tep_db_fetch_array($user_info_query);
  
  $user_order_sort_name = ' u.name';
  $user_order_sort = 'asc';
  if (isset($_POST['user_sort'])) {
    switch ($_POST['user_sort']) {
       case 'user_name':
         $user_order_sort_name = ' u.name';
         break;
       case 'user_id':
         $user_order_sort_name = ' u.userid';
         break;
       case 'user_permission':
         $user_order_sort_name = ' p.permission';
         break;
       case 'user_site_permission':
          $user_order_sort_name = ' p.site_permission';
         break;
       case 'user_status':
         $user_order_sort_name = ' u.status';
         break;
       case 'user_update':
         $user_order_sort_name = ' u.date_update';
         break;
    }
  }
  if (isset($_POST['user_sort_type'])) {
    if ($_POST['user_sort_type'] == 'asc') {
      $user_order_sort = 'asc';
    } else {
      $user_order_sort = 'desc';
    }
  }
  $user_order_sql = $user_order_sort_name.' '.$user_order_sort;

  $user_query_raw = 'select u.* from '.TABLE_USERS.' u, '.TABLE_PERMISSIONS.' p where u.userid = p.userid and p.permission <= \''.$ocertify->npermission.'\' order by '.$user_order_sql;
  $user_split = new splitPageResults($_POST['page'], MAX_DISPLAY_PRODUCTS_ADMIN, $user_query_raw, $user_query_numrows);
  $user_list_query = tep_db_query($user_query_raw);
  while($user_row = tep_db_fetch_array($user_list_query)){
    $user_array[] = $user_row['userid'];
  }
  foreach($user_array as $u_key => $u_value){
    if($_POST['user_e_id'] == $u_value){
      break;
    }
  }

  if($u_key > 0){ 
    $page_str .= '<a onclick="show_link_user_info(\''.$user_array[$u_key - 1].'\', \''.urlencode($param_str).'\');" href="javascript:void(0);" id="user_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;';
  } else {
    $page_str .= '<font color="#000000">'.IMAGE_PREV.'</font>&nbsp;&nbsp;'; 
  }
  
  if($u_key < count($user_array)-1){
    $page_str .= '<a onclick="show_link_user_info(\''.$user_array[$u_key + 1].'\', \''.urlencode($param_str).'\');" href="javascript:void(0);" id="user_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;';
  } else {
    $page_str .= '<font color="#000000">'.IMAGE_NEXT.'</font>&nbsp;&nbsp;'; 
  }
  
  $page_str .= '<a onclick="close_user_info();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $user_info_res['name']); 
  $heading[] = array('align' => 'right', 'text' => $page_str); 
  
  $user_permission_query = tep_db_query("select * from ".TABLE_PERMISSIONS." where userid = '".$_POST['user_e_id']."'");
  $user_permission_res = tep_db_fetch_array($user_permission_query);
  
  $buttons = array();
  if ($is_disabled_single) {
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'disabled="disabled" id="button_save"').'</a>'; 
  } else {
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="check_user_info(\''.$_POST['user_e_id'].'\', 1);" id="button_save"').'</a>'; 
  }
  if (($ocertify->npermission >= 15) && ($user_permission_res['permission'] != 31)) {
    if ($is_disabled_single) {
      $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'disabled="disabled"').'</a>'; 
    } else {
      $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="delete_fix_user(\''.$user_info_res['userid'].'\', \''.urlencode($param_str).'\');"').'</a>'; 
    }
  } 
  $buttons = array('align' => 'center', 'button' => $button); 
 
  $new_user_row = array();
  
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_ID_TEXT), 
        array('align' => 'left', 'text' => $user_info_res['userid'].tep_draw_hidden_field('userid', $user_info_res['userid'], 'id="userid"').'<br><span id="userid_error" style="color:#ff0000;"></span>') 
      );
  
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_USER_INFO_NAME), 
        array('align' => 'left', 'text' => tep_draw_input_field('name', $user_info_res['name'], 'id="name" style="width:60%;"'.(($is_disabled_single)?' disabled="disabled"':'')).'<br><span id="name_error" style="color:#ff0000;"></span>') 
      );
  
  
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_EMAIl_TEXT), 
        array('align' => 'left', 'text' => tep_draw_input_field('user_email', $user_info_res['email'], 'style="width:60%;" id="user_email"'.(($is_disabled_single)?' disabled="disabled"':'')).'<br><span id="email_error" style="color:#ff0000;"></span>') 
      );
  $user_permission_array = array();
  $user_permission_str = '';

  if ($ocertify->npermission == '7') {
    $user_permission_str = tep_draw_radio_field('u_permission', 'staff', true, '', (($is_disabled_single)?' disabled="disabled"':'')).'Staff';
  } else if ($ocertify->npermission == '10') {
    $user_permission_str = tep_draw_radio_field('u_permission', 'staff', (($user_permission_res['permission'] == '7')?true:false), '', (($is_disabled_single)?' disabled="disabled"':'')).'Staff&nbsp;'.tep_draw_radio_field('u_permission', 'chief', (($user_permission_res['permission'] == '10')?true:false), '', (($is_disabled_single)?' disabled="disabled"':'')).'Chief';
  } else {
    $user_permission_str = tep_draw_radio_field('u_permission', 'staff', (($user_permission_res['permission'] == '7')?true:false), '', (($is_disabled_single)?' disabled="disabled"':'')).'Staff&nbsp;'.tep_draw_radio_field('u_permission', 'chief', (($user_permission_res['permission'] == '10')?true:false), '', (($is_disabled_single)?' disabled="disabled"':'')).'Chief&nbsp;'.tep_draw_radio_field('u_permission', 'admin', (($user_permission_res['permission'] == '15')?true:false), '', (($is_disabled_single)?' disabled="disabled"':'')).'Admin';
  }
  
  if ($user_permission_res['permission'] != '31') {
    $new_user_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_USER_INFO_PERMISSION), 
          array('align' => 'left', 'params' => 'class="td_input"', 'text' => $user_permission_str) 
        );
  } 
  $user_site_array = explode(',', $user_permission_res['site_permission']);
  $tmp_c_site_list = explode(',', $c_user_permission_res['site_permission']); 
  $tmp_diff_array = array_diff($user_site_array, $tmp_c_site_list);  
  $tmp_merge_site_array = array_merge($tmp_c_site_list, $user_site_array); 
  $merge_site_array = array_unique($tmp_merge_site_array); 
  $tmp_merge_array = array(); 
  if (!empty($merge_site_array)) {
    foreach ($merge_site_array as $m_key => $m_value) {
      if (trim($m_value) != '') {
        $tmp_merge_array[] = $m_value; 
      }
    }
  }
  $merge_site_array = $tmp_merge_array; 
  if ($ocertify->npermission == 31) {
    $user_site_permission_str = '<input type="checkbox" name="user_permission_info[]" value="0"'.((in_array('0', $user_site_array)?' checked':'')).'>all&nbsp;';  
  } else {
    $tmp_check_str = ''; 
    if ($is_disabled_single) {
      $tmp_check_str .= 'disabled="disabled"'; 
      $tmp_check_str .= (in_array('0', $user_site_array)?' checked':''); 
    } else {
      if (in_array('0', $merge_site_array)) {
        if (in_array('0', $tmp_c_site_list)) {
          if (in_array('0', $user_site_array)) {
            $tmp_check_str .= 'checked'; 
          } 
        } else {
          $tmp_check_str .= 'disabled="disabled"'; 
          if (in_array('0', $user_site_array)) {
            $tmp_check_str .= ' checked'; 
          } 
        }
      } else {
        $tmp_check_str .= 'disabled="disabled"'; 
      }
    }
    $user_site_permission_str = '<input type="checkbox" name="user_permission_info[]" value="0" '.$tmp_check_str.'>all&nbsp;';  
  }
  $site_list_query = tep_db_query("select * from ".TABLE_SITES." order by id asc"); 
  while ($site_list_info = tep_db_fetch_array($site_list_query)) {
    if ($ocertify->npermission == 31) {
      $user_site_permission_str .= '<input type="checkbox" name="user_permission_info[]" value="'.$site_list_info['id'].'"'.((in_array($site_list_info['id'], $user_site_array)?' checked':'')).'>'.$site_list_info['romaji'].'&nbsp;';  
    } else {
      $tmp_check_str = ''; 
      if ($is_disabled_single) {
        $tmp_check_str .= 'disabled="disabled"'; 
        $tmp_check_str .= (in_array($site_list_info['id'], $user_site_array)?' checked':''); 
      } else {
        if (in_array($site_list_info['id'], $merge_site_array)) {
          if (in_array($site_list_info['id'], $tmp_c_site_list)) {
            if (in_array($site_list_info['id'], $user_site_array)) {
              $tmp_check_str .= 'checked'; 
            } 
          } else {
            $tmp_check_str .= 'disabled="disabled"'; 
            if (in_array($site_list_info['id'], $user_site_array)) {
              $tmp_check_str .= ' checked'; 
            } 
          }
        } else {
          $tmp_check_str .= 'disabled="disabled"'; 
        }
      }
      $user_site_permission_str .= '<input type="checkbox" name="user_permission_info[]" value="'.$site_list_info['id'].'" '.$tmp_check_str.'>'.$site_list_info['romaji'].'&nbsp;';  
    }
  }
  if ($ocertify->npermission != '31') {
    $user_site_permission_str .= '<input type="hidden" name="other_site" value="'.implode(',', $tmp_diff_array).'">'; 
  }
  if ($user_permission_res['permission'] != '31') {
    $new_user_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_USER_INFO_SITE_PERMISSION), 
          array('align' => 'left', 'params' => 'class="td_input"', 'text' => $user_site_permission_str.'<br>'.TABLE_USER_INFO_SITE_PERMISSION_READ) 
        );
  } 
  if (check_input_user_password($user_permission_res['permission'], $_POST['user_e_id'])) {
    $new_user_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_USER_INFO_PASSWORD), 
          array('align' => 'left', 'text' => tep_draw_password_field('user_password', '', false, 'id="user_password" style="width:60%;"'.(($is_disabled_single)?' disabled="disabled"':'')).'<br><span id="password_error" style="color:#ff0000;"></span>') 
        );
  }
  $letter_info_query = tep_db_query("select * from ".TABLE_LETTERS." where userid = '' or userid is null or userid = '".$_POST['user_e_id']."'");  
  $user_letter_query = tep_db_query("select * from ".TABLE_LETTERS. " where userid = '".$_POST['user_e_id']."'"); 
  $user_letter_res = tep_db_fetch_array($user_letter_query); 
  if (tep_db_num_rows($letter_info_query) > 0) {
    $user_calc_str = tep_show_pw_start($user_info_res['userid'], $user_letter_res['letter'], (($is_disabled_single)?' disabled="disabled"':(($user_info_res['status'] == '1')?'':' disabled="disabled"'))).'&nbsp;'.tep_draw_input_field('user_rule', $user_info_res['rule'], 'style="width:60%;" id="user_rule"'.(($is_disabled_single)?' disabled="disabled"':(($user_info_res['status'] == '1')?'':' disabled="disabled"'))).'<br><span id="rule_error" style="color:#ff0000;"></span>';
    $new_user_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_CALC_TEXT), 
          array('align' => 'left', 'text' => $user_calc_str) 
        );
   
    if ($is_disabled_single) {
      $new_user_row[]['text'] = array(
            array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_ONETIME_PWD), 
            array('align' => 'left', 'text' => tep_draw_input_field('user_onetime', $user_letter_res['letter'].make_rand_pwd($user_info_res['rule']), 'id="user_onetime" class="readonly" style="width:60%" readonly').'&nbsp;<a href="javascript:void(0);">'.tep_html_element_button(USER_ONETIME_PWD_PREVIEW, 'disabled="disabled"').'</a>') 
          );
    } else {
      if ($user_info_res['status'] == '1') {
        $new_user_row[]['text'] = array(
              array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_ONETIME_PWD), 
              array('align' => 'left', 'text' => tep_draw_input_field('user_onetime', $user_letter_res['letter'].make_rand_pwd($user_info_res['rule']), 'id="user_onetime" class="readonly" style="width:60%" readonly').'&nbsp;<a href="javascript:void(0);" onclick="user_preview_onetime_pwd();">'.tep_html_element_button(USER_ONETIME_PWD_PREVIEW).'</a>') 
            );
      } else {
        $new_user_row[]['text'] = array(
              array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_ONETIME_PWD), 
              array('align' => 'left', 'text' => tep_draw_input_field('user_onetime', $user_letter_res['letter'].make_rand_pwd($user_info_res['rule']), 'id="user_onetime" class="readonly" style="width:60%" readonly').'&nbsp;<a href="javascript:void(0);">'.tep_html_element_button(USER_ONETIME_PWD_PREVIEW, 'disabled="disabled"').'</a>') 
            );
      }
    }
    
    $new_user_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => '&nbsp;'), 
          array('align' => 'left', 'text' => USER_INFO_ONETIME_PWD_READ) 
        );
  } else {
    $new_user_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => '&nbsp;'), 
          array('align' => 'left', 'text' => USER_INFO_NO_ONETIME_PWD_READ) 
        );
  }
  $ip_limit_str = '';
  $user_ip_list_query = tep_db_query("select * from user_ip where userid = '".$_POST['user_e_id']."' and type='0'");
  while ($user_ip_list = tep_db_fetch_array($user_ip_list_query)) {
    $ip_limit_str .= $user_ip_list['limit_ip']."\n"; 
  }
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_IP_LIMIT_TEXT), 
        array('align' => 'left', 'text' => tep_draw_textarea_field('ip_limit', 'hard', '30', '10', $ip_limit_str, 'style="width:60%;" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="resize: vertical;"'.(($is_disabled_single)?' disabled="disabled"':''))) 
      );

  $ip_warn_email_str = '';
  $user_ip_warn_email_query = tep_db_query("select * from user_ip where userid = '".$_POST['user_e_id']."' and type='1'");
  while ($user_ip_warn_email = tep_db_fetch_array($user_ip_warn_email_query)) {
    $ip_warn_email_str .= $user_ip_warn_email['limit_ip']."\n"; 
  }
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_IP_WARN_EMAIL_TEXT), 
        array('align' => 'left', 'text' => tep_draw_textarea_field('ip_limit_warn_email', 'hard', '30', '10', $ip_warn_email_str, 'style="width:60%;" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="resize: vertical;"'.(($is_disabled_single)?' disabled="disabled"':''))) 
      );

  $login_count_query = tep_db_query("select count(sessionid) as len from login where date(`logintime`) = date(now()) and account = '".$user_info_res['userid']."'"); 
  $login_count_res = tep_db_fetch_array($login_count_query);
  $login_count = 0; 
  if ($login_count_res) {
    $login_count = $login_count_res['len']; 
  }
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_LOGIN_NUM), 
        array('align' => 'left', 'text' => $login_count) 
      );
 
  $login_time_str = '';
  $login_time_query = tep_db_query("select * from login where account = '".$user_info_res['userid']."' order by logintime desc limit 1");
  $login_time_res = tep_db_fetch_array($login_time_query);
  if ($login_time_res) {
    $login_time_str = $login_time_res['logintime']; 
  }
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => USER_INFO_LAST_LOGIN_DATE), 
        array('align' => 'left', 'text' => (($login_time_str != '')?$login_time_str:TEXT_UNSET_DATA)) 
      );
  
  $new_user_row[]['text'] = array(
            array('align' => 'left', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($user_info_res['user_added'])?$user_info_res['user_added']:TEXT_UNSET_DATA)), 
            array('align' => 'left', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($user_info_res['date_added']) || ($user_info_res['date_added'] != '0000-00-00 00:00:00'))?$user_info_res['date_added']:TEXT_UNSET_DATA))
      );
      
  $new_user_row[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($user_info_res['user_update'])?$user_info_res['user_update']:TEXT_UNSET_DATA)),
        array('align' => 'left', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($user_info_res['date_update']) || ($user_info_res['date_update'] != '0000-00-00 00:00:00'))?$user_info_res['date_update']:TEXT_UNSET_DATA))
      );
  
  $form_str = tep_draw_form('new_user_form', FILENAME_USERS, 'user_e_id='.$_POST['user_e_id'].'&action=update_user_info&'.$param_str); 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($new_user_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  
  echo $notice_box->show_notice().'||||||'.tep_get_note_top_layer(FILENAME_USERS);
}else if($_GET['action'] == 'edit_contents'){
include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_CONTENTS);
include(DIR_FS_ADMIN.'classes/notice_box.php');
$notice_box = new notice_box('popup_order_title', 'popup_order_info');
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
$action_sid = $_GET['action_sid'];
while($userslist= tep_db_fetch_array($sites_id)){
     $site_permission = $userslist['site_permission']; 
}
if(isset($site_permission)) $site_arr=$site_permission;//权限判断
else $site_arr="";
$site_array = explode(',',$site_arr);
if(!in_array($action_sid,$site_array) && $action_sid != -1){
   $disabled = 'disabled="disabled"'; 
}
  if(!isset($_GET['sort']) || $_GET['sort'] == ''){
       $contents_str = 'i.sort_id, i.heading_title ';
       $_GET['type'] = '';
  }else if($_GET['sort'] == 'site_romaji'){
       $contents_str = 's.romaji '; 
  }else if($_GET['sort'] == 'title'){
       $contents_str = 'i.heading_title '; 
  }else if($_GET['sort'] == 'status'){
       $contents_str = 'i.status '; 
  }else if($_GET['sort'] == 'sort_id'){
       $contents_str = 'i.sort_id '; 
  }else if($_GET['sort'] == 'date_update'){
       $contents_str = 'i.date_update '; 
  }
  //更新内容 
$cID = $_GET['cID'];
if($_GET['site_id'] == -1){
  $_GET['site_id'] = '';
}
if($cID && tep_not_null($cID)) {
 $cquery = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '".$cID."'");
 $cresult = tep_db_fetch_array($cquery);
 $c_title = $cresult['heading_title'];
} else {
 $c_title = '&nbsp;';
}
    if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
      $sql_site_where = 'site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
    } else {
      $show_site_str = tep_get_setting_site_info(FILENAME_CONTENTS);
      $sql_site_where = 'site_id in ('.$show_site_str.')';
    }
    $sites_sql = tep_db_query("SELECT * FROM `sites`");
    while($sites_row = tep_db_fetch_array($sites_sql)){
      $show_site_arr[] = $sites_row['id']; 
    }
if($cID != -1){
$contents_query_raw = " select i.pID, i.navbar_title, i.heading_title, i.text_information, i.status, i.sort_id, i.romaji, i.site_id, i.date_added, i.date_update,i.show_status, s.romaji as sromaji from ".TABLE_INFORMATION_PAGE." i , ".TABLE_SITES." s where s.id = i.site_id and ".$sql_site_where." order by ".$contents_str.$_GET['type'];
$contents_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $contents_query_raw, $contents_query_numrows);
$contents_query = tep_db_query($contents_query_raw);
$cid_array = array();
while ($contents = tep_db_fetch_array($contents_query)) {
  $cid_array[] = $contents['pID'];
}

  $detail_query = tep_db_query(" select * from ".TABLE_INFORMATION_PAGE." where pID = '".$cID."'");
  $detail = tep_db_fetch_array($detail_query);
    switch ($detail['status']) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
    }
    $page_str  = '';
 foreach ($cid_array as $c_key => $c_value) {
    if ($_GET['cID'] == $c_value) {
      break; 
    }
  }
  $page_str = '';
  if ($c_key > 0) {
    $contents_site_id = tep_db_query(" select * from ".TABLE_INFORMATION_PAGE." where pID = '".$cid_array[$c_key-1]."'");
    $contents_site_id_row = tep_db_fetch_array($contents_site_id); 
    $page_str .= '<a onclick=\'show_contents("",'.$cid_array[$c_key-1].','.$_GET['page'].','.$contents_site_id_row['site_id'].')\'
      href="javascript:void(0);" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
 
  if ($c_key < (count($cid_array) - 1)) {
    $contents_site_id = tep_db_query(" select * from ".TABLE_INFORMATION_PAGE." where pID = '".$cid_array[$c_key+1]."'");
    $contents_site_id_row = tep_db_fetch_array($contents_site_id); 
    $page_str .= '<a onclick=\'show_contents("",'.$cid_array[$c_key+1].','.$_GET['page'].','.$contents_site_id_row['site_id'].')\' href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }else{
    $page_str .= '<font color="#000000">'.IMAGE_NEXT.'></font>'; 
  }

    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => $detail['heading_title']);
    $heading[] = array('align' => 'right', 'text' => $page_str); 
    $form_str = tep_draw_form('content_form', FILENAME_CONTENTS, 'cID='.$cID.'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&act=update' .((isset($_GET['site_id'])&&$_GET['site_id'])?'&site_id='.$_GET['site_id']:''));
    $contents[]['text'] = array(
         array('text' => '<input type="hidden" name="user_update" value="'.$_SESSION['user_name'].'">'),  
         array('text' => '<input type="hidden" name="action_sid" value="'.$_GET['action_sid'].'"><input type="hidden" name="status" value="'.$detail['status'].'">')    
    );
    $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$detail['site_id']));
    $contents[]['text'] = array(
         array('text' => ENTRY_SITE),
         array('text' => $site_name['romaji'])
    );
    $contents[]['text'] = array(
         array('text' => TEXT_DETAIL_SORT),
         array('text' => tep_draw_input_field('sort_id', $detail['sort_id'],$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="width:60%"'))
    );
 
    if($detail['show_status'] == '1'){
        $show_status_input = tep_draw_input_field('show_romaji', $detail['romaji'],$disabled.'disabled="disabled" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="width:60%" id="romaji"').'<input type="hidden" name="romaji" value="'.$detail['romaji'].'">';
    }else{
        $show_status_input = tep_draw_input_field('romaji', $detail['romaji'],$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="width:60%" id="romaji"'); 
    }
    if (isset($error_message)) { $error_message = $error_message; }
    $contents[]['text'] = array(
         array('text' =>'TEXT_DETAIL_LETTERS'),
         array('params' => 'nowrap','text' => '<input type="hidden" id="romaji_hidden_value" value="update">'.  $show_status_input.$error_message.'&nbsp;&nbsp;<span id="error_romaji"></span><span id="error_romaji_info"></span>')
    );
    $contents[]['text'] = array(
         array('text' => TEXT_DETAIL_NAVBAR_TITLE),
         array('text' => tep_draw_input_field('navbar_title', $detail['navbar_title'],$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;"style="width:60%"'))
    );
    $contents[]['text'] = array(
         array('text' => TEXT_DETAIL_HEADING_TITLE),
         array('params' => 'nowrap','text' => tep_draw_input_field('heading_title', $detail['heading_title'],$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;"style="width:60%" id="heading_title"').'&nbsp;&nbsp;<span id="heading_title_error"></span>')
    );
     if($detail['show_status'] == '1'){
        $note_params = TEXT_CONTENT_INFO; 
        $note_params_next = TEXT_CONTENT_NEXT;
        if($detail['romaji'] != 'present_success.php'){
           $note_params_order = '<div style="float:left">'.TEXT_CONTENT_ORDER.'</div>';
           $note_params_info =  '<div style="float:right;width:30%">'.TEXT_CONTENT_PRODUCTS_INFO.'</div><br>';
        }
     }
     $contents[]['text'] = array(
         array('text' => TEXT_DETAIL_CONTENTS),
         array('params' => 'nowrap','text' => tep_draw_textarea_field('text_information', 'soft', '70', '20', stripslashes($detail['text_information']),' style="resize: vertical;"'.$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;"'))
    );
    if($detail['show_status'] == '1'){
         $note_content_params = '<div style="float:left">'.$note_params.'</div><div style="float:right;width:30%">'.$note_params_next.'</div><br>'.$note_params_order.$note_params_info;
    }
    $contents[]['text'] = array(
         array('params' => 'width="30%"','text' => ''),
         array('text' => $note_content_params.TEXT_DETAIL_INPUT)
    );
    if($detail['show_status'] != '1'){
    $contents[]['text'] = array(
         array('params' => 'width="30%"','text' => TEXT_LINK),
         array('text' => TEXT_CONTENT_MSG.'<br>< a href="'.HTTP_SERVER.'/info/'.($detail['romaji']).'.html">'.$c_title.'< /a>')
    );
    }
    $info_query = tep_db_query("select * from information_page where PID='".$cID."'");
    $info_array = tep_db_fetch_array($info_query);
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($info_array['user_added'])?$info_array['user_added']:TEXT_UNSET_DATA)), 
            array('align' => 'left','text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($info_array['date_added']))?$info_array['date_added']:TEXT_UNSET_DATA))
      );
      
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($info_array['user_update'])?$info_array['user_update']:TEXT_UNSET_DATA)),
            array('align' => 'left','text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($info_array['date_update']))?$info_array['date_update']:TEXT_UNSET_DATA))
      );
    if($disabled){
        $submit = tep_html_element_button(IMAGE_SAVE,$disabled);
    }else{
        $submit = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'id="button_save" onclick="check_contents('.$ocertify->npermission.');"').'</a>';
    }
    if($ocertify->npermission >= 15){
     if($disabled){
        $delete = tep_html_element_button(IMAGE_DELETE,$disabled).tep_draw_hidden_field('cID', $cID).tep_draw_hidden_field('page', htmlspecialchars($_GET['page']));
      }else{
      if($info_array['show_status'] == '1'){
      $delete = tep_html_element_button(IMAGE_DELETE,'disabled="disabled"').tep_draw_hidden_field('cID', $cID).tep_draw_hidden_field('page', htmlspecialchars($_GET['page']));
      }else{
      $delete = '<a href="javascript:void(0)" onclick="check_del(\''.$ocertify->npermission.'\',\''.$cID.'\',\''.$_GET['page'].'\',\''.$_GET['site_id'].'\')">'.tep_html_element_button(IMAGE_DELETE).'</a>'.tep_draw_hidden_field('cID', $cID).tep_draw_hidden_field('page', htmlspecialchars($_GET['page']));
      }
      }
    }
    $button[] = $submit.'&nbsp;&nbsp;'.$delete;
     if(!empty($button)){
           $buttons = array('align' => 'center', 'button' => $button);
     }
    $notice_box->get_form($form_str);
    $notice_box->get_heading($heading);
    $notice_box->get_contents($contents, $buttons);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
}else{
    $page_str  = '';
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => HEADING_TITLE);
    $heading[] = array('align' => 'right', 'text' => $page_str);
    $form_str = tep_draw_form('content_form', FILENAME_CONTENTS, 'act=insert&site_id='.$_GET['site_id'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page']);
    $contents[]['text'] = array(
         array('text' => '<input type="hidden" name="user_added" value="'.$_SESSION['user_name'].'"><input type="hidden" name="user_update" value="'.$_SESSION['user_name'].'">'),
         array('text' => '')
    );
   $customers_site_arr = array_intersect($show_site_arr,$site_array);
   $site_id_name = "<select id='customers_site_id' name='site_id' $disabled>";
   foreach($customers_site_arr as $value){
     if($value!=0){
       $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$value));
       $site_id_name .= "<option value='".$site_name['id'] ."'>".$site_name['name']."</option>";
     }
   }
   $site_id_name .= "</select>";
   $site_id_name .= '&nbsp;<font color="#ff0000;">*'.TEXT_REQUIRED.'</font>'; 

    $contents[]['text'] = array(
         array('text' => ENTRY_SITE),
         array('params' => 'class="td_input"','text' => $site_id_name)
    );
    $contents[]['text'] = array(
         array('text' => ''),
         array('text' => '<input type="hidden" name="status" value="1">')
    );
    $contents[]['text'] = array(
         array('params' => 'style="width:30%"','text' => TEXT_DETAIL_SORT),
         array('text' => tep_draw_input_field('sort_id', '',$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;"style="width:60%"'))
    );
    if (isset($error_message)) { $error_message = $error_message; }
    $contents[]['text'] = array(
         array('text' => TEXT_DETAIL_LETTERS),
         array('params' => 'nowrap','text' => '<input type="hidden" id="romaji_hidden_value" value="insert">'.tep_draw_input_field('romaji', '',$disabled.'id="romaji"onfocus="o_submit_single = false;" onblur="o_submit_single = true;"style="width:60%"').$error_message.'&nbsp;&nbsp;<span id="error_romaji"></span><span id="error_romaji_info"></span>')
    );
    $contents[]['text'] = array(
         array('text' => TEXT_DETAIL_NAVBAR_TITLE),
         array('text' => tep_draw_input_field('navbar_title', '',$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;"style="width:60%"'))
    );
    $contents[]['text'] = array(
         array('text' => TEXT_DETAIL_HEADING_TITLE),
         array('params' => 'nowrap','text' => tep_draw_input_field('heading_title', '',$disabled.'id="heading_title"onfocus="o_submit_single = false;" onblur="o_submit_single = true;"style="width:60%"').'&nbsp;&nbsp;<span id="heading_title_error"></span>')
    );
    $contents[]['text'] = array(
         array('text' => TEXT_DETAIL_CONTENTS),
         array('text' => tep_draw_textarea_field('text_information', 'soft', '70', '20', '',$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;"style="resize: vertical;"'))
    );
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($info_array['user_added'])?$info_array['user_added']:TEXT_UNSET_DATA)), 
            array('align' => 'left','text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($info_array['date_added']))?$info_array['date_added']:TEXT_UNSET_DATA))
      );
      
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($info_array['user_update'])?$info_array['user_update']:TEXT_UNSET_DATA)),
            array('align' => 'left','text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($info_array['date_update']))?$info_array['date_update']:TEXT_UNSET_DATA))
      );

    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'id="button_save" onclick="check_contents('.$ocertify->npermission.');"').'</a>';
    if(!empty($button)){
         $buttons = array('align' => 'center', 'button' => $button);
    }
    $notice_box->get_form($form_str);
    $notice_box->get_heading($heading);
    $notice_box->get_contents($contents, $buttons);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
 }
}else if($_GET['action'] == 'edit_manufacturers'){
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_MANUFACTURERS);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
$is_u_disabled = false;
if ($ocertify->npermission != 31) {
  if (!empty($_SESSION['site_permission'])) {
    $tmp_u_array = explode(',', $_SESSION['site_permission']);
    if (!in_array('0', $tmp_u_array)) {
      $is_u_disabled = true;
    }
  } else {
    $is_u_disabled = true;
  }
}
if($_GET['mID'] != -1){
if(!isset($_GET['sort']) || $_GET['sort'] == ''){
   $manufacturers_str = 'manufacturers_name';
}else if($_GET['sort'] == 'm_name'){
   if($_GET['type'] == 'desc'){
      $manufacturers_str = 'manufacturers_name desc';
   }else{
      $manufacturers_str = 'manufacturers_name asc';
   }
}else if($_GET['sort'] == 'last_modified'){
   if($_GET['type'] == 'desc'){
      $manufacturers_str = 'last_modified desc';
   }else{
      $manufacturers_str = 'last_modified asc';
   }
}
  $manufacturers_query_raw = "select manufacturers_id, manufacturers_name,manufacturers_alt, manufacturers_image, date_added, last_modified,user_added,user_update from " .  TABLE_MANUFACTURERS . " order by ".$manufacturers_str;
  $manufacturers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $manufacturers_query_raw, $manufacturers_query_numrows);
  $manufacturers_query = tep_db_query($manufacturers_query_raw);
  $cid_array = array();
  while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
    $cid_array[] = $manufacturers['manufacturers_id'];
    if (((!isset($_GET['mID']) || !$_GET['mID']) || (@$_GET['mID'] == $manufacturers['manufacturers_id'])) && (!isset($mInfo) || !$mInfo) && (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new')) {
      $manufacturer_products_query = tep_db_query("select count(*) as products_count from " . TABLE_PRODUCTS . " where manufacturers_id = '" . $manufacturers['manufacturers_id'] . "'");
      $manufacturer_products = tep_db_fetch_array($manufacturer_products_query);

      $mInfo_array = tep_array_merge($manufacturers, $manufacturer_products);
      $mInfo = new objectInfo($mInfo_array);
    }
  }
 foreach ($cid_array as $c_key => $c_value) {
    if ($_GET['mID'] == $c_value) {
      break; 
    }
  }
  $page_str = '';
  if ($c_key > 0) {
    $page_str .= '<a onclick=\'show_manufacturers("",'.$cid_array[$c_key-1].','.$_GET['page'].')\' href="javascript:void(0);" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
 
  if ($c_key < (count($cid_array) - 1)) {
    $page_str .= '<a onclick=\'show_manufacturers("",'.$cid_array[$c_key+1].','.$_GET['page'].')\' href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }else{
    $page_str .= '<font color="#000000">&nbsp;&nbsp;'.IMAGE_NEXT.'></font>'; 
  }
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => $mInfo->manufacturers_name);
    $heading[] = array('align' => 'right', 'text' => $page_str);
    $form_str = tep_draw_form('manufacturers', FILENAME_MANUFACTURERS, 'page=' .  $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=save', 'post', 'enctype="multipart/form-data"');
    $contents = array();
    $contents[]['text'] = array(
           array('text' => ''),
           array('text' => '<input type="hidden" name="user_update" value="'.$_SESSION['user_name'].'">')
    );
    $contents[]['text'] = array(
           array('text' => TEXT_MANUFACTURERS_NAME),
           array('text' => tep_draw_input_field('manufacturers_name',$mInfo->manufacturers_name,'onfocus="o_submit_single = false;" onblur="o_submit_single = true;"id="manufacturers_name"'.(isset($is_u_disabled) && $is_u_disabled?'disabled="disabled"':'')).'&nbsp;&nbsp;<span id="manufacturers_name_error"></span>')
    );
    $manufacturer_inputs_string = '';
    $languages = tep_get_languages();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
       $manufacturer_inputs_string .= tep_draw_input_field('manufacturers_url[' .  $languages[$i]['id'] . ']', tep_get_manufacturer_url($mInfo->manufacturers_id, $languages[$i]['id']),' id="manufac_url_'.$languages[$i]['id'].'" onfocus="o_submit_single = false;" onblur="o_submit_single = true;"'.(isset($is_u_disabled) && $is_u_disabled?'disabled="disabled"':''));
    }
    $contents[]['text'] = array(
          array('params' => 'style="width:30%"','text' => TEXT_MANUFACTURERS_URL),
          array('text' => $manufacturer_inputs_string)
    );

    $contents[]['text'] = array(
           array('text' => TEXT_MANUFACTURERS_IMAGE),
           array('params' => 'class="td_img"','text' => tep_draw_file_field('manufacturers_image','','onfocus="o_submit_single = false;" onblur="o_submit_single = true;"'.(isset($is_u_disabled) && $is_u_disabled?'disabled="disabled"':'')) .'<br>'.tep_info_image('manufacturers/' .  $mInfo->manufacturers_image, $mInfo->manufacturers_name,'50','50')
             )
    );
    $contents[]['text'] = array(
          array('text' => TEXT_ALT),
          array('text' => tep_draw_input_field('manufacturers_alt',$mInfo->manufacturers_alt,'onfocus="o_submit_single = false;"onblur="o_submit_single = true;"'.(isset($is_u_disabled) && $is_u_disabled?'disabled="disabled"':'')))
    );
    $contents[]['text'] = array(
          array('text' => TEXT_PRODUCTS),
          array('text' => $mInfo->products_count)
    );
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($mInfo->user_added)?$mInfo->user_added:TEXT_UNSET_DATA)), 
            array('align' => 'left','text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($mInfo->date_added))?$mInfo->date_added:TEXT_UNSET_DATA))
    );
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($mInfo->user_update)?$mInfo->user_update:TEXT_UNSET_DATA)),
            array('align' => 'left','text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($mInfo->last_modified))?$mInfo->last_modified:TEXT_UNSET_DATA))
    );
    if ($mInfo->products_count > 0) {
      $delete_products = 'on';
    }
    if($is_u_disabled){
    $m_save = tep_html_element_button(IMAGE_SAVE, 'id="button_save" disabled="disabled"');
    }else{
    $m_save = '<a href="javascript:void(0);">' .  tep_html_element_button(IMAGE_SAVE, 'id="button_save" onclick="toggle_manufacturers_form(\''.$ocertify->npermission.'\')"') .  '</a> ';
    }
    if($ocertify->npermission >= 15){
    if($is_u_disabled){
    $m_save = tep_html_element_button(IMAGE_SAVE, 'id="button_save" disabled="disabled"');
    $m_del = tep_html_element_button(IMAGE_DELETE,'disabled="disabled"');
    }else{
    $m_save = '<a href="javascript:void(0);">' .  tep_html_element_button(IMAGE_SAVE, 'id="button_save" onclick="toggle_manufacturers_form(\''.$ocertify->npermission.'\')"') .  '</a> ';
    $m_del = '<a href="javascript:void(0);" onclick="check_del('.$mInfo->manufacturers_id.','.$_GET['page'].','.$ocertify->npermission.')">' .  tep_html_element_button(IMAGE_DELETE) . '</a>';
    }
    }
    $button[] = $m_save.$m_del;
    if(!empty($button)){
         $buttons = array('align' => 'center', 'button' => $button);
    }
    $notice_box->get_form($form_str);
    $notice_box->get_heading($heading);
    $notice_box->get_contents($contents, $buttons);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
 }else{
    $page_str  = '';
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => HEADING_TITLE);
    $heading[] = array('align' => 'right', 'text' => $page_str);
    $form_str = tep_draw_form('manufacturers', FILENAME_MANUFACTURERS, 'action=insert', 'post', 'enctype="multipart/form-data"');
    $contents = array();
    $contents[]['text'] = array(
           array('text' => '<input type="hidden" name="user_added" value="'.$_SESSION['user_name'].'">'),    
           array('text' => '<input type="hidden" name="user_update" value="'.$_SESSION['user_name'].'">')
    ); 
    $contents[]['text'] = array(
           array('params' => 'style="width:30%"','text' => TEXT_MANUFACTURERS_NAME),    
           array('text' => tep_draw_input_field('manufacturers_name','',(isset($is_u_disabled) && $is_u_disabled?'disabled="disabled"':'').'id="manufacturers_name"').'&nbsp;&nbsp;<span id="manufacturers_name_error"></span>')
    ); 
    $contents[]['text'] = array(
           array('text' => TEXT_MANUFACTURERS_IMAGE),    
           array('text' => tep_draw_file_field('manufacturers_image','',(isset($is_u_disabled) && $is_u_disabled?'disabled="disabled"':'')))
    ); 
    $manufacturer_inputs_string = '';
    $languages = tep_get_languages();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
       $manufacturer_inputs_string .= tep_draw_input_field('manufacturers_url[' .  $languages[$i]['id'] . ']', tep_get_manufacturer_url($mInfo->manufacturers_id, $languages[$i]['id']),' id="manufac_url_'.$languages[$i]['id'].'" onfocus="o_submit_single = false;" onblur="o_submit_single = true;"'.(isset($is_u_disabled) && $is_u_disabled?'disabled="disabled"':''));
    }

    $contents[]['text'] = array(
           array('text' => TEXT_MANUFACTURERS_URL),    
           array('text' => $manufacturer_inputs_string)
    ); 
    $contents[]['text'] = array(
          array('text' => TEXT_ALT),
          array('text' => tep_draw_input_field('manufacturers_alt','','onfocus="o_submit_single = false;"onblur="o_submit_single = true;"'.(isset($is_u_disabled) && $is_u_disabled?'disabled="disabled"':'')))
    );
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($mInfo->user_added)?$mInfo->user_added:TEXT_UNSET_DATA)), 
            array('align' => 'left','text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($mInfo->date_added))?$mInfo->date_added:TEXT_UNSET_DATA))
    );
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($mInfo->user_update)?$mInfo->user_update:TEXT_UNSET_DATA)),
            array('align' => 'left','text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($mInfo->last_modified))?$mInfo->last_modified:TEXT_UNSET_DATA))
    );
    $button[] = '<a href="javascript:void(0);">' .  tep_html_element_button(IMAGE_SAVE, 'id="button_save" onclick="toggle_manufacturers_form(\''.$ocertify->npermission.'\')"') .  '</a>';
    if(!empty($button)){
         $buttons = array('align' => 'center', 'button' => $button);
    }
    $notice_box->get_form($form_str);
    $notice_box->get_heading($heading);
    $notice_box->get_contents($contents, $buttons);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
 }
}else if ($_GET['action'] == 'edit_mail') {
/* -----------------------------------------------------
    功能: 显示编辑mail templates弹出框
    参数: $_POST['mail_id'] mail templates ID 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_MAIL_TEMPLATES);
  include(DIR_FS_ADMIN.'classes/notice_box.php'); 

  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  //获取当前用户的网站管理权限
  $sites_id_sql = tep_db_query("select site_permission from ".TABLE_PERMISSIONS." where userid= '".$ocertify->auth_user."'");
  $userslist= tep_db_fetch_array($sites_id_sql);
  tep_db_free_result($sites_id_sql);
  $site_permission_array = explode(',',$userslist['site_permission']); 
  $site_permission_flag = false;
  if(in_array('0',$site_permission_array)){

    $site_permission_flag = true;
  }

  //读取mail templates的相应数据
  $mail_id = $_POST['mail_id'];
  $keyword = $_POST['search'];
  $param_str = $_POST['param_str'];
  $url = $_POST['url'];
  $url = str_replace('|||','&',$url);
  $mail_query = tep_db_query("select * from ". TABLE_MAIL_TEMPLATES ." where id='".$mail_id."'"); 
  $mail_array = tep_db_fetch_array($mail_query);
  tep_db_free_result($mail_query);
  //邮件模板无效时的处理
  if($mail_array['valid'] == 0){

    $mail_templates_str = tep_get_mail_templates($mail_array['flag'],0);
    $mail_array['title'] = $mail_templates_str['title'];
    $mail_array['contents'] = $mail_templates_str['contents'];
  }

  $site_romaji_str = '';
  $field_str = '*';
  if(isset($_POST['order_sort']) && $_POST['order_sort'] != '' && isset($_POST['order_type']) && $_POST['order_type'] != ''){
    switch($_POST['order_sort']){

    case 'name':
      $order_sort = 'templates_title';
      $order_type = $_POST['order_type'];
      break;
    case 'title':
      $order_sort = 'title';
      $order_type = $_POST['order_type'];
      break;
    case 'contents':
      $order_sort = 'contents';
      $order_type = $_POST['order_type'];
      break;
    case 'scope':
      $order_sort = 'use_scope';
      $order_type = $_POST['order_type'];
      break; 
    case 'action':
      $order_sort = 'date_update';
      $order_type = $_POST['order_type'];
      break;
    default:
      $order_sort = 'id';
      $order_type = 'asc';
    }
  }else{
    $order_sort = 'id';
    $order_type = 'asc'; 
  }

  if($keyword != ''){

    $keyword_str = " where templates_title like '%".$keyword."%' or title like '%".$keyword."%' or contents like '%".$keyword."%'";
  }
  $mail_id_num_array = array();
  $mail_id_query = tep_db_query("select ".$field_str." from ". TABLE_MAIL_TEMPLATES . $site_romaji_str . $keyword_str ." order by ".$order_sort." ".$order_type); 
  while($mail_id_array = tep_db_fetch_array($mail_id_query)){

    $mail_id_num_array[] = $mail_id_array['id'];
  }
  tep_db_free_result($mail_id_query);

  //头部内容
  $heading = array();

  $page_str = '';

  //显示上一个，下一个按钮
  $page_str = '';

  $page_str_array = explode('=',$param_str);
  $page_string = $page_str_array[1];
  $page_string = isset($page_string) && $page_string != '' ? $page_string : 1;
  $page_num_start = ($page_string-1) * MAX_DISPLAY_SEARCH_RESULTS;
  if(count($mail_id_num_array) < MAX_DISPLAY_SEARCH_RESULTS){
    $page_num_end = count($mail_id_num_array)-1; 
  }else{
    $page_num_end = $page_string * MAX_DISPLAY_SEARCH_RESULTS - 1; 
    if($page_num_end > count($mail_id_num_array)){
      $page_num_end = count($mail_id_num_array)-1; 
    }
  }
  $mail_id_page_array = array();
  for($i = $page_num_start;$i <= $page_num_end;$i++){

    $mail_id_page_array[] = $mail_id_num_array[$i];
  }
  $mail_id_num = array_search($mail_id,$mail_id_page_array);

  $mail_id_prev = $mail_id_page_array[$mail_id_num - 1];
  $mail_id_next = $mail_id_page_array[$mail_id_num + 1];
  $mail_id_page_array = array_filter($mail_id_page_array);
  if ($mail_id_num > 0) {
    $page_str .= '<a id="mail_prev" onclick="show_link_mail_info(\''.$mail_id_prev.'\',\''.$param_str.'\')" href="javascript:void(0);" ><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
 
  if ($mail_id_num < (count($mail_id_page_array) - 1)) {
    $page_str .= '<a id="mail_next" onclick="show_link_mail_info(\''.$mail_id_next.'\',\''.$param_str.'\')" href="javascript:void(0);">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }else{
    $page_str .= '<font color="#000000">'.IMAGE_NEXT.'></font>&nbsp;&nbsp;';
  }

  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $mail_array['templates_title']);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  //主体内容
  $category_info_row = array();
   
  //编辑mail templates项目   
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_MAIL_NAME.'<input type="hidden" name="mail_id" value="'.$mail_array['id'].'"><input type="hidden" name="param_str" value="'.$param_str.'"><input type="hidden" name="url" value="'.$url.'"><input type="hidden" name="valid" value="'.($mail_array['valid'] == 1 ?  0 : 1).'">'), 
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="text" class="option_input" name="templates_title" value="'.$mail_array['templates_title'].'"><span id="mail_name_error">'.TEXT_FIELD_REQUIRED.'</span>')
     );

  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => '&nbsp;'), 
       array('align' => 'left', 'params' => 'colspan="2"', 'text' => nl2br($mail_array['use_description']))
     );

  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_MAIL_TITLE), 
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<input type="text" class="option_input" name="title" value="'.$mail_array['title'].'"><span id="mail_title_error">'.TEXT_FIELD_REQUIRED.'</span>')
     );

   //格式化邮件模板说明
   $mail_templates_array = preg_split('/<br>|<\/br>/',$mail_array['contents_description']);
   $mail_templates_array = array_filter($mail_templates_array);
   $mail_templates_start_array = '';
   $mail_templates_end_array = '';
   $mail_templates_start_str = '';
   $mail_templates_end_str = '';
   $i = 0;
   foreach($mail_templates_array as $mail_value){

     if($i % 2 == 0){

       if(trim($mail_value) != ''){ 
         $mail_templates_end_array[] = $mail_value;
       }
     }else{
       if(trim($mail_value) != ''){
         $mail_templates_start_array[] = $mail_value;
       }
     }
     $i++;
   }
   $mail_templates_start_array = array_filter($mail_templates_start_array);
   $mail_templates_end_array = array_filter($mail_templates_end_array);
   if(count($mail_templates_end_array) > count($mail_templates_start_array)){

     $mail_templates_start_array[] = array_pop($mail_templates_end_array); 

   }

   $mail_templates_start_str = implode('<br>',$mail_templates_start_array);
   $mail_templates_end_str = implode('<br>',$mail_templates_end_array);
   $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_MAIL_CONTENTS), 
       array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<textarea name="contents" rows="15" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="resize:vertical; width:100%;">'.$mail_array['contents'].'</textarea>'),
      array('align' => 'left', 'params' => 'valign="top" nowrap="nowrap"', 'text' => '<span id="mail_contents_error">'.TEXT_FIELD_REQUIRED.'</span>')
     );
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => ''), 
       array('align' => 'left', 'params' => 'colspan="2"', 'text' => '<table width="100%" cellspacing="0" cellpadding="2" border="0"><tr><td>'.$mail_templates_start_str.'</td><td>'.$mail_templates_end_str.'</td></tr></table>'),
     );
  
  //作成者，作成时间，更新者，更新时间 
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($mail_array['user_added'])?$mail_array['user_added']:TEXT_UNSET_DATA))),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null(tep_datetime_short($mail_array['date_added'])))?tep_datetime_short($mail_array['date_added']):TEXT_UNSET_DATA))
      );
   
  $category_info_row[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($mail_array['user_update'])?$mail_array['user_update']:TEXT_UNSET_DATA))),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null(tep_datetime_short($mail_array['date_update'])))?tep_datetime_short($mail_array['date_update']):TEXT_UNSET_DATA))
      );
    
  //底部内容
  $buttons = array();

  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'id="button_save" onclick="edit_mail_check(\''.$ocertify->npermission.'\');"'.($site_permission_flag == false  ? 'disabled="disabled"' : '')).'</a>'; 
 
  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }

  $form_str = tep_draw_form('edit_mail', FILENAME_MAIL_TEMPLATES, '', 'post', 'id="edit_mail_id"');

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($category_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if($_GET['action'] == 'edit_faq'){
include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_FAQ);
include(DIR_FS_ADMIN.'classes/notice_box.php');
$notice_box = new notice_box('popup_order_title', 'popup_order_info');
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
$action_sid = $_GET['action_sid'];
while($userslist= tep_db_fetch_array($sites_id)){
     $site_permission = $userslist['site_permission']; 
}
$sites_sql = tep_db_query("SELECT * FROM `sites`");
$show_site_arr = array();
$show_site_arr[0] = '0'; 
while($sites_row = tep_db_fetch_array($sites_sql)){
    $show_site_arr[] = $sites_row['id']; 
}
if(isset($site_permission)) $site_arr=$site_permission;//权限判断
else $site_arr="";
$site_array = explode(',',$site_arr);
if(!in_array($action_sid,$site_array) && $action_sid != -1){
   $disabled = 'disabled="disabled"'; 
}
if($_GET['site_id'] == -1){
   $_GET['site_id'] = '';
}
if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
   $sql_site_where = 'site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
} else {
   $show_site_str = tep_get_setting_site_info(FILENAME_FAQ);
   $sql_site_where = 'site_id in ('.$show_site_str.')';
}
if($_GET['qID'] != -1 && $_GET['cID'] != -1){
                if(!isset($_GET['type']) || $_GET['type'] == ''){
                       $_GET['type'] = 'asc';
                 }
                if($faq_type == ''){
                      $faq_type = 'asc';
                }
                if($_GET['sort'] == 'site_romaji'){
                  if($_GET['type'] == 'desc'){
                    $faq_str = 'site_id desc';
                    }else{
                    $faq_str = 'site_id asc';
                    }
                }else if($_GET['sort'] == 'title'){
                  if($_GET['type'] == 'desc'){
                    $faq_str = 'info_type desc,title desc';
                    }else{
                    $faq_str = 'info_type asc,title asc';
                    }
                }else if($_GET['sort'] == 'is_show'){
                  if($_GET['type'] == 'desc'){
                    $faq_str = 'is_show desc';
                    }else{
                    $faq_str = 'is_show asc';
                    }
                }else if($_GET['sort'] == 'updated_at'){
                  if($_GET['type'] == 'desc'){
                    $faq_str = 'updated_at desc';
                    }else{
                    $faq_str = 'updated_at asc';
                    }
                }
                if(isset($_GET['search'])&&$_GET['search']!=''){
                    $sql_search_where = " and search_text like '%".$_GET['search']."%' ";
                    $faq_category_query_raw = "select * from faq_sort where 1 ".  $sql_search_where." and ".$sql_site_where." order by ";
                 }else{
                    $faq_category_query_raw = "select * from faq_sort where
                      parent_id = '".$current_category_id."' and
                      ".$sql_site_where."order by ";
                 }
		 if(isset($faq_str)&&$faq_str!=''){
		   $faq_category_query_raw .= $faq_str;
		 }else{
                   $faq_category_query_raw .= 'info_type,sort_order asc';
                 }
                 // $faq_query_raw = "select * from faq_sort where parent_id = '".$current_category_id."' and title like '%".$_GET['search']."%' and ".$sql_site_where." order by ".$faq_str;
                  $faq_split = new splitPageResults($_GET['page'],MAX_DISPLAY_FAQ_ADMIN,$faq_category_query_raw,$faq_query_number);
                  $_faq_query = tep_db_query($faq_category_query_raw);
                  $qid_array = array();
                  while($_faq_info = tep_db_fetch_array($_faq_query)){
                    $id_array[] = $_faq_info['id'];
                    if((isset($_GET['qID']) && $_GET['qID'] == $_faq_info['info_id'] && $_GET['info_type'] == 'q') && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_')){
                      $qInfo = new objectInfo($_faq_info);
                    }
                    if((isset($_GET['cID']) && $_GET['cID'] == $_faq_info['info_id'] && $_GET['info_type'] == 'c') && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_')){
                      $qInfo = new objectInfo($_faq_info);
                    }
                  }
    if($qInfo->info_type == 'q'){
    $faq_q = tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_QUESTION." where id = '".$qInfo->info_id."'"));
    $faq_q_raw = tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_QUESTION_DESCRIPTION." where faq_question_id = '".$qInfo->info_id."' and site_id = '".$qInfo->site_id."'"));
    }else if($qInfo->info_type == 'c'){
    $faq_c = tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_CATEGORIES." where id = '".$qInfo->info_id."'"));
    $faq_c_raw = tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_CATEGORIES_DESCRIPTION." where faq_category_id = '".$qInfo->info_id."' and site_id = '".$qInfo->site_id."'"));
    }
    $page_str  = '';
    foreach ($id_array as $q_key => $q_value) {
      if ($_GET['faq_id'] == $q_value) {
        break;
      }
    }
    if($_GET['page'] == ''){$_GET['page'] = '1';}
    if ($q_key > 0) {
      $qid_site_id = tep_db_query("select * from `faq_sort` where id = '".$id_array[$q_key-1]."'");
      $qid_site_id_row = tep_db_fetch_array($qid_site_id); 
      if($qid_site_id_row['info_type'] == 'c'){
      $page_str .= '<a onclick="show_faq(\'\','.$qid_site_id_row['info_id'].',\'\','.$_GET['page'].','.$qid_site_id_row['site_id'].','.$id_array[$q_key-1].',\''.$qid_site_id_row['info_type'].'\')" href="javascript:void(0);" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
      }else{
      $page_str .= '<a onclick="show_faq(\'\',\'\','.$qid_site_id_row['info_id'].','.$_GET['page'].','.$qid_site_id_row['site_id'].','.$id_array[$q_key-1].',\''.$qid_site_id_row['info_type'].'\')" href="javascript:void(0);" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
      }
    }
    if ($q_key < (count($id_array) - 1)) {
      $qid_site_id = tep_db_query(" select * from `faq_sort` where id  = '".$id_array[$q_key+1]."'");
      $qid_site_id_row = tep_db_fetch_array($qid_site_id); 
      if($qid_site_id_row['info_type'] == 'c'){
      $page_str .= '<a onclick="show_faq(\'\','.$qid_site_id_row['info_id'].',\'\','.$_GET['page'].','.$qid_site_id_row['site_id'].','.$id_array[$q_key+1].',\''.$qid_site_id_row['info_type'].'\')" href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
      }else{
      $page_str .= '<a onclick="show_faq(\'\',\'\','.$qid_site_id_row['info_id'].','.$_GET['page'].','.$qid_site_id_row['site_id'].','.$id_array[$q_key+1].',\''.$qid_site_id_row['info_type'].'\')" href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
      }
    }else{
      $page_str .= '<font color="#000000">&nbsp;&nbsp;'.IMAGE_NEXT.'></font>'; 
    }
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    if($qInfo->info_type == 'q'){
    $heading[] = array('align' => 'left', 'text' => $faq_q_raw['ask']);
    }else if($qInfo->info_type == 'c'){
    $heading[] = array('align' => 'left', 'text' => $faq_c_raw['title']);
    }
    $heading[] = array('align' => 'right', 'text' => $page_str);
    if($qInfo->info_type == 'q'){
    $url_str = '&cPath='.$_GET['cPath'].  '&site_id='.$_GET['site_id'].'&qID='.$_GET['qID'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page'];
    $form_str = tep_draw_form('newfaqcategory',FILENAME_FAQ,'action=update_faq_question'.$url_str,'post');
    }else if($qInfo->info_type == 'c'){
    $url_str = '&cPath='.$_GET['cPath'].  '&site_id='.$_GET['site_id'].'&cID='.$_GET['cID'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page'];
    $form_str = tep_draw_form('newfaqcategory',FILENAME_FAQ,'action=update_faq_category'.$url_str,'post');
    }
    $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
    $contents = array();
    if($qInfo->info_type == 'q'){
    $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$faq_q_raw['site_id']));
    }else if($qInfo->info_type == 'c'){
    $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$faq_c_raw['site_id']));
    }
    $contents[]['text'] = array(
        array('text' => ENTRY_SITE),
        array('text' => $site_name['romaji'])
        ); 
    if($qInfo->info_type == 'q'){
     $contents[]['text'] = array(
        array('text' => '<input type="hidden" name="user_update" value="'.$_SESSION['user_name'].'">URL'),
        array('text' => tep_draw_input_field('url_words',$faq_q_raw['url_words'],'id="q_letters"size="40" onfocus="o_submit_single = false;"onblur="o_submit_single = true;" '.$disabled).TEXT_MUST.'<input type="button" '.$disabled.'onclick = "faq_q_is_set_romaji(\''.$current_category_id.'\',\''.$faq_q_raw['faq_question_id'].'\',\''.$faq_q_raw['site_id'].'\')" value="'.TEXT_LETTERS_IS_SET.'">'.  '<input type="button" '.$disabled.' onclick = "faq_q_is_set_error_char()" value="'.IS_SET_ERROR_CHAR.'"><br><span id="q_letters_error"></span>')
        );
        
    }else if($qInfo->info_type == 'c'){
    $contents[]['text'] = array(
        array('text' => '<input type="hidden" name="user_update" value="'.$_SESSION['user_name'].'">URL'),
        array('text' => tep_draw_input_field('url_words',$faq_c_raw['url_words'],'id="c_letters"size="40" onfocus="o_submit_single = false;"onblur="o_submit_single = true;" '.$disabled). TEXT_MUST. '<input type="button" '.$disabled.'onclick = "faq_c_is_set_romaji(\''.$current_category_id.'\',\''.$faq_c_raw['faq_question_id'].'\',\''.$faq_c_raw['site_id'].'\')" value="'.TEXT_LETTERS_IS_SET.'">'.  '<input type="button" '.$disabled.' onclick = "faq_c_is_set_error_char()" value="'.IS_SET_ERROR_CHAR.'"><br><span id="c_letters_error"></span>')
        );
    }
    if($qInfo->info_type == 'q'){
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => TEXT_NEW_FAQ_QUESTION_KEYWORDS),
        array('text' => tep_draw_textarea_field('keywords','soft',30,7,$faq_q_raw['keywords'],$disabled.'onfocus="o_submit_single = false;"onblur="o_submit_single = true;"style="resize: vertical;"'))
        );
    }else if($qInfo->info_type == 'c'){
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => TEXT_NEW_FAQ_CATEGORY_TITLE),
        array('text' => tep_draw_input_field('title',$faq_c_raw['title'],'id="title"onfocus="o_submit_single = false;"onblur="o_submit_single = true;"size="40"'.$disabled).TEXT_MUST.'<br><span id="title_error"></span>')
        );
    }
    if($qInfo->info_type == 'q'){
    $contents[]['text'] = array(
        array('text' => TEXT_NEW_FAQ_QUESTION_ASK),
        array('text' => tep_draw_input_field('ask',$faq_q_raw['ask'],'id="title"'.$disabled.'onfocus="o_submit_single = false;"onblur="o_submit_single = true;"size="40"').TEXT_MUST.'<br><span id="title_error"></span>')
        );
    }else if($qInfo->info_type == 'c'){
    $contents[]['text'] = array(
        array('text' => TEXT_NEW_FAQ_CATEGORY_KEYWORDS),
        array('text' => tep_draw_textarea_field('keywords','soft',30,7,$faq_c_raw['keywords'],$disabled.'onfocus="o_submit_single = false;"onblur="o_submit_single = true;" style="resize: vertical;"'))
       );
    }
    if($qInfo->info_type == 'q'){
    $contents[]['text'] = array(
        array('text' => TEXT_NEW_FAQ_QUESTION_ANSWER),
        array('text' => tep_draw_textarea_field('answer','soft',30,7,$faq_q_raw['answer'],$disabled.'onfocus="o_submit_single = false;"onblur="o_submit_single = true;" style="resize: vertical;"').'<br>'.TEXT_NEW_FAQ_QUESTION_ANSWER_HELP)
        );
    }else if($qInfo->info_type == 'c'){
    $contents[]['text'] = array(
        array('text' => TEXT_NEW_FAQ_CATEGORY_DESCRIPTION),
        array('text' => tep_draw_textarea_field('description','soft',30,7,$faq_c_raw['description'],$disabled.'onfocus="o_submit_single = false;"onblur="o_submit_single = true;" style="resize: vertical;"'))
        );
    }
    if($qInfo->info_type == 'q'){
     $contents[]['text'] = array(
        array('text' => TEXT_NEW_FAQ_QUESTION_SORT_ORDER),
        array('text' => tep_draw_input_field('sort_order',$faq_q['sort_order'],'size="5"style="text-align:right"'.$disabled.'onfocus="o_submit_single = false;"onblur="o_submit_single = true;"'))
        );
    }else if($qInfo->info_type == 'c'){
    $contents[]['text'] = array(
        array('text' => TEXT_NEW_FAQ_QUESTION_SORT_ORDER),
        array('text' => tep_draw_input_field('sort_order',$faq_c['sort_order'],'size="5"style="text-align:right"'.$disabled.'onfocus="o_submit_single = false;"onblur="o_submit_single = true;"'))
        );
    }
    if($qInfo->info_type == 'q'){
    $contents[]['text'] = array(
        array('text' => $faq_question_inputs_string),
        array('text' => '<input type="hidden" name="faq_question_id" id="faq_q_id" value="'.$faq_q_raw['faq_question_id'].'">'.
          tep_draw_hidden_field('site_id',$qInfo->site_id))
        );
    }else if($qInfo->info_type == 'c'){
    $contents[]['text'] = array(
        array('text' => $faq_question_inputs_string),
        array('text' => '<input type="hidden" name="faq_category_id" id="faq_c_id" value="'.$faq_c_raw['faq_category_id'].'">'.
          tep_draw_hidden_field('site_id',$qInfo->site_id))
        );
    }
    if($qInfo->info_type == 'q'){
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($faq_q['user_added'])?$faq_q['user_added']:TEXT_UNSET_DATA)), 
            array('align' => 'left','text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($faq_q['created_at']))?$faq_q['created_at']:TEXT_UNSET_DATA))
    );
    }else if($qInfo->info_type == 'c'){
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($faq_c['user_added'])?$faq_c['user_added']:TEXT_UNSET_DATA)), 
            array('align' => 'left','text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($faq_c['created_at']))?$faq_c['created_at']:TEXT_UNSET_DATA))
    );
    }
    if($qInfo->info_type == 'q'){
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($faq_q['user_update'])?$faq_q['user_update']:TEXT_UNSET_DATA)),
            array('align' => 'left','text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($faq_q['updated_at']))?$faq_q['updated_at']:TEXT_UNSET_DATA))
    );
    }else if($qInfo->info_type == 'c'){
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($faq_c['user_update'])?$faq_c['user_update']:TEXT_UNSET_DATA)),
            array('align' => 'left','text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($faq_c['updated_at']))?$faq_c['updated_at']:TEXT_UNSET_DATA))
    );
    }
    if($qInfo->info_type == 'q'){
    if($disabled){
        $faq_qid_save = tep_html_element_button(TEXT_SAVE,$disabled);
    }else{
        $faq_qid_save = '<a href="javascript:void(0);">'.tep_html_element_button(TEXT_SAVE, 'onclick="faq_question_form_validator(\''.$current_category_id.'\',\''.$faq_q_raw['faq_question_id'].'\',\''.$faq_q_raw['site_id'].'\',\''.$ocertify->npermission.'\');"').'</a>';
    }
    if($ocertify->npermission >= 15){
      if($disabled){
        $faq_qid_del  = tep_html_element_button(IMAGE_DELETE,$disabled);
      }else{
        $faq_qid_del = '<a href="javascript:void(0);" onclick="delete_fix_faq_category(\'q\',\''.urlencode('site_id='.$faq_q_raw['site_id'].'&cPath=' . $cPath .  '&cID=' .  $faq_q_raw['faq_question_id']).'\');">'.tep_html_element_button(IMAGE_DELETE).'</a>';
      }
    }
   $button[] = $faq_qid_save.$faq_qid_del;
    }else if($qInfo->info_type == 'c'){
     if($disabled){
         $faq_save = tep_html_element_button(TEXT_SAVE,$disabled);
     }else{
         $faq_save = '<a href="javascript:void(0);">'.tep_html_element_button(TEXT_SAVE, 'id="button_save" onclick="faq_category_form_validator(\''.$current_category_id.'\',\''.$faq_c_raw['faq_category_id'].'\',\''.$faq_q_raw['site_id'].'\', \''.$ocertify->npermission.'\')"').  '</a>';
     }
     if($ocertify->npermission >= 15){
         if($disabled){
            $faq_del =tep_html_element_button(IMAGE_DELETE,$disabled);
         }else{
            $faq_del = '<a href="javascript:void(0);" onclick="delete_fix_faq_category(\'c\',\''.urlencode('site_id='.$faq_c_raw['site_id'].'&cPath=' . $cPath .  '&cID=' .  $faq_c_raw['faq_category_id']).'\');">'.tep_html_element_button(IMAGE_DELETE).'</a>';
         }
     } 
    $button[] = $faq_save.$faq_del;
    }
   if(!empty($button)){
         $buttons = array('align' => 'center', 'button' => $button);
    }
    $notice_box->get_form($form_str);
    $notice_box->get_heading($heading);
    $notice_box->get_contents($contents, $buttons);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
    echo tep_draw_form('question', FILENAME_FAQ, 'action=delete_faq_confirm&cPath='.$_GET['cPath'].$d_page, 'post').  tep_draw_hidden_field('faq_question_id',$qInfo->faq_question_id).  tep_draw_hidden_field('site_id',$qInfo->site_id);
    $question_categories_string = '';
    $question_categories = tep_generate_faq_category_path($qInfo->faq_question_id, 'question');
    for ($i = 0, $n = sizeof($question_categories); $i < $n; $i++) {
      $question_categories_string .= tep_draw_hidden_field('question_categories[]', $question_categories[$i][sizeof($question_categories[$i])-1]['id'], true); 
    }    
    $question_categories_string = substr($question_categories_string, 0, -4); 
    echo $question_categories_string;
    echo '</form>';
}
if($_GET['cID'] == -1){
    $page_str  = '';
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => TEXT_INFO_HEADING_NEW_FAQ_CATEGORY);
    $heading[] = array('align' => 'right', 'text' => $page_str);
    $url_str = '&cPath='.$_GET['cPath'].  '&site_id='.$_GET['site_id'].'&cID='.$_GET['cID'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page'];
    $form_str = tep_draw_form('newfaqcategory',FILENAME_FAQ,'action=insert_faq_category&'.$url_str, 'post');
    $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
    $faq_site_arr = array_intersect($show_site_arr,$site_array);
    if(isset($_GET['cPath']) && $_GET['cPath'] != ''){
      $site_id_name = "<select id='faq_site_id' name='site_id' ".$disabled." onchange='faq_c_is_set_romaji(\"".$current_category_id."\",\"\",\"".$site_id."\")'>";
      $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$_GET['action_sid']));
      $site_id_name .= "<option value='".$site_name['id'] ."'>".$site_name['name']."</option>";
      $site_id_name .= "</select>";
      $site_id_name .= '&nbsp;<font color="#ff0000;">*'.TEXT_REQUIRED.'</font>'; 
    }else{
    $site_id_name = "<select id='faq_site_id' name='site_id' ".$disabled." onchange='faq_c_is_set_romaji(\"".$current_category_id."\",\"\",\"".$site_id."\")'>";
    foreach($faq_site_arr as $value){
      if($value!=0){
        $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$value));
        $site_id_name .= "<option value='".$site_name['id'] ."'>".$site_name['name']."</option>";
      }
    }
    $site_id_name .= "</select>";
    $site_id_name .= '&nbsp;<font color="#ff0000;">*'.TEXT_REQUIRED.'</font>'; 
    }
    $contents = array();
    $contents[]['text'] = array(
        array('text' => ENTRY_SITE),
        array('text' => $site_id_name)
        ); 
    $contents[]['text'] = array(
        array('text' => '<input type="hidden" name="user_update" value="'.$_SESSION['user_name'].'"><input type="hidden" name="user_added" value="'.$_SESSION['user_name'].'">URL'),
        array('text' => tep_draw_input_field('url_words','','id="c_letters" onfocus="o_submit_single = false;"onblur="o_submit_single = true;" size="40"').TEXT_MUST.'<input type="button" onclick = "faq_c_is_set_romaji(\''.$current_category_id.'\',\'\',\''.$site_id.'\')" value="'.TEXT_LETTERS_IS_SET.'">'.  '<input type="button" onclick = "faq_c_is_set_error_char(\'\')" value="'.IS_SET_ERROR_CHAR.'"><br><span id="c_letters_error"></span>')
        );
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => TEXT_NEW_FAQ_CATEGORY_TITLE),
        array('text' => tep_draw_input_field('title','','id="title"onfocus="o_submit_single = false;"onblur="o_submit_single = true;" size="40"').TEXT_MUST.'<br><span id="title_error"></span>')
        );
    $contents[]['text'] = array(
        array('text' => TEXT_NEW_FAQ_CATEGORY_KEYWORDS),
        array('text' => tep_draw_textarea_field('keywords','soft',30,7,'','onfocus="o_submit_single = false;"onblur="o_submit_single = true;" style="resize: vertical;"'))
        );
    $contents[]['text'] = array(
        array('text' => TEXT_NEW_FAQ_CATEGORY_DESCRIPTION),
        array('text' => tep_draw_textarea_field('description','soft',30,7,'','onfocus="o_submit_single = false;"onblur="o_submit_single = true;" style="resize: vertical;"'))
        );
    $contents[]['text'] = array(
        array('text' => TEXT_NEW_FAQ_CATEGORY_SORT_ORDER),
        array('text' => tep_draw_input_field('sort_order','1000','size="5" style="text-align:right"onfocus="o_submit_single = false;"onblur="o_submit_single = true;"'))
        );
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA), 
            array('align' => 'left','text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA)
    );
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA),
            array('align' => 'left','text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA)
    );
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(TEXT_SAVE, 'id="button_save"onclick="faq_category_form_validator(\''.$current_category_id.'\',\'\',\''.$site_id.'\', \''.$ocertify->npermission.'\')"').  '</a>';
   if(!empty($button)){
         $buttons = array('align' => 'center', 'button' => $button);
    }
    $notice_box->get_form($form_str);
    $notice_box->get_heading($heading);
    $notice_box->get_contents($contents, $buttons);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
}
if($_GET['qID'] == -1){
    $page_str  = '';
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => TEXT_INFO_HEADING_NEW_FAQ_QUESTION);
    $heading[] = array('align' => 'right', 'text' => $page_str);
    $url_str = '&cPath='.$_GET['cPath'].  '&site_id='.$_GET['site_id'].'&qID='.$_GET['qID'].'&search='.$_GET['search'].'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='.$_GET['page'];
    $form_str = tep_draw_form('newfaqcategory',FILENAME_FAQ,'action=insert_faq_question'.$url_str,'post');
    $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
    if(isset($_GET['cPath']) && $_GET['cPath'] != ''){
      $site_id_name = "<select id='faq_site_id' name='site_id' ". $disabled ." onchange='faq_q_is_set_romaji(\"".$current_category_id."\",\"\",\"".$site_id."\")'>";
      $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$_GET['action_sid']));
      $site_id_name .= "<option value='".$site_name['id'] ."'>".$site_name['name']."</option>";
      $site_id_name .= "</select>";
      $site_id_name .= '&nbsp;<font color="#ff0000;">*'.TEXT_REQUIRED.'</font>'; 
    }else{
    $site_id_name = "<select id='faq_site_id' name='site_id' ".$disabled ."  onchange='faq_q_is_set_romaji(\"".$current_category_id."\",\"\",\"".$site_id."\")'>";
    $faq_site_arr = array_intersect($show_site_arr,$site_array);
    foreach($faq_site_arr as $value){
      if($value!=0){
        $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$value));
        $site_id_name .= "<option value='".$site_name['id'] ."'>".$site_name['name']."</option>";
      }
    }
    $site_id_name .= "</select>";
    $site_id_name .= '&nbsp;<font color="#ff0000;">*'.TEXT_REQUIRED.'</font>'; 
    }
    $contents = array();
    $contents[]['text'] = array(
        array('text' => ENTRY_SITE),
        array('text' => $site_id_name)
        ); 
    $contents[]['text'] = array(
        array('text' => '<input type="hidden" name="user_update" value="'.$_SESSION['user_name'].'"><input type="hidden" name="user_added" value="'.$_SESSION['user_name'].'">URL'),
        array('text' => tep_draw_input_field('url_words','','id="q_letters"onfocus="o_submit_single = false;"onblur="o_submit_single = true;" size="40"').TEXT_MUST.'<input type="button" onclick = "faq_q_is_set_romaji(\''.$current_category_id.'\',\'\',\''.$site_id.'\')" value="'.TEXT_LETTERS_IS_SET.'">'.  '<input type="button" onclick = "faq_q_is_set_error_char(\'\')" value="'.IS_SET_ERROR_CHAR.'"><br><span id="q_letters_error"></span>')
        );
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => TEXT_NEW_FAQ_QUESTION_KEYWORDS),
        array('text' => tep_draw_textarea_field('keywords','soft',30,7,'','onfocus="o_submit_single = false;"onblur="o_submit_single = true;" style="resize: vertical;"'))
        ); 
    $contents[]['text'] = array(
        array('text' => TEXT_NEW_FAQ_QUESTION_ASK),
        array('text' => tep_draw_input_field('ask','','id="title"onfocus="o_submit_single = false;"onblur="o_submit_single = true;" size="40"').TEXT_MUST.'<br><span id="title_error"></span>')
        ); 
    $contents[]['text'] = array(
        array('text' => TEXT_NEW_FAQ_QUESTION_ANSWER),
        array('text' => tep_draw_textarea_field('answer','soft',30,7).'<br>'.TEXT_NEW_FAQ_QUESTION_ANSWER_HELP)
        ); 
    $contents[]['text'] = array(
        array('text' => TEXT_NEW_FAQ_QUESTION_SORT_ORDER),
        array('text' => tep_draw_input_field('sort_order','1000','size="5"style="text-align:right"'))
        ); 
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA), 
            array('align' => 'left','text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp'.TEXT_UNSET_DATA)
    );
    $contents[]['text'] = array(
            array('align' => 'left','text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp'.TEXT_UNSET_DATA),
            array('align' => 'left','text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp'.TEXT_UNSET_DATA)
    );
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(TEXT_SAVE, 'id="button_save" onclick="faq_question_form_validator(\''.$current_category_id.'\',\'\',\''.$site_id.'\', \''.$ocertify->npermission.'\');"').  '</a>';
    if(!empty($button)){
         $buttons = array('align' => 'center', 'button' => $button);
    }
    $notice_box->get_form($form_str);
    $notice_box->get_heading($heading);
    $notice_box->get_contents($contents, $buttons);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
}
}else if($_GET['action'] == 'edit_data'){
include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_DATA_MANAGEMENT);
include(DIR_FS_ADMIN.'classes/notice_box.php');
if ($ocertify->npermission != 31) {
       $c_site_query = tep_db_query("select * from ".TABLE_PERMISSIONS." where userid = '".$ocertify->auth_user."'");
       $c_site_res = tep_db_fetch_array($c_site_query);
       $tmp_c_site_array = explode(',',$c_site_res['site_permission']);
          if (!empty($tmp_c_site_array)) {
               if (!in_array('0', $tmp_c_site_array)) {
                    $is_disabled_single = true;
               }
          } else {
               $is_disabled_single = true;
          }
}
if($_GET['sort'] == 'update_at'){
  $data_m = tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'DATA_MANAGEMENT' order by ".$_GET['sql_type']);
     $c_id = array();
  while($data_row = tep_db_fetch_array($data_m)){
     $c_id[] = $data_row['configuration_id'];
  }
}
$notice_box = new notice_box('popup_order_title', 'popup_order_info');
if($_GET['type'] == 'mag_orders'){
    $all_orders_statuses =  array();
    $all_preorders_statuses =  array();
    $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");
    while ($orders_status = tep_db_fetch_array($orders_status_query)) {
      $all_orders_statuses[] = array('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
     }
    $preorders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_PREORDERS_STATUS . " where language_id = '" .  $languages_id . "'");
    while ($preorders_status = tep_db_fetch_array($preorders_status_query)) {
      $all_preorders_statuses[] = array('id' => $preorders_status['orders_status_id'], 'text' => $preorders_status['orders_status_name']);
    }
    $page_str  = '';
    if($_GET['sort'] == 'update_at'){
    foreach ($c_id as $q_key => $q_value) {
      if ($_GET['c_id'] == $q_value) {
        break;
      }
    }
    }
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => TEXT_MAG_ORDERS);
    $heading[] = array('align' => 'right', 'text' => $page_str);
    $contents = array();
    $form_str = '<form id="orders_download" action="'.tep_href_link('orders_csv_exe.php','csv_exe=true','SSL').'" method="post">';
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => TEXT_ORDER_SITE_TEXT),
        array('text' => tep_site_pull_down_menu_with_all(isset($_GET['site_id']) ?  $_GET['site_id'] :'', false,'all',(($is_disabled_single)?' disabled="disabled"':'')))
        );
    $select_y = '<select name="s_y" '.(($is_disabled_single)?' disabled="disabled"':'').'>';
    for($i=2002; $i<=date('Y'); $i++) { 
      if($i == date('Y')){ 
        $select_y .= '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ; 
      }else{ 
        $select_y .='<option value="'.$i.'">'.$i.'</option>'."\n" ;
      }
    }
    $select_y .='</select>';
    $select_m = '<select name="s_m" '.(($is_disabled_single)?' disabled="disabled"':'').'>';
    $select_d = '<select name="s_d" '.(($is_disabled_single)?' disabled="disabled"':'').'>';
    for($i=1; $i<32; $i++) {
      if($i == date('d')){
        $select_d .='<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
      }else{
        $select_d .='<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
      } 
    }
    $select_d .='</select>';
    for($i=1; $i<13; $i++) { 
      if($i == date('m')-1){ 
        $select_m .='<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; 
      }else{ 
        $select_m .='<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; 
      }  
    }    
    $select_m .='</select>';
    $select_e_y ='<select name="e_y" '.(($is_disabled_single)?' disabled="disabled"':'').'>';
    for($i=2002; $i<=date('Y'); $i++) {
      if($i == date('Y')){
        $select_e_y .='<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
      }else{
        $select_e_y .='<option value="'.$i.'">'.$i.'</option>'."\n" ;
      } 
    }
    $select_e_y .='</select>';
    $select_e_m ='<select name="e_m" '.(($is_disabled_single)?' disabled="disabled"':'').'>';
    for($i=1; $i<13; $i++) {
      if($i == date('m')){
        $select_e_m .= '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
      }else{
        $select_e_m .='<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
      } 
    }
    $select_e_m .='</select>';
    $select_e_d ='<select name="e_d" '.(($is_disabled_single)?' disabled="disabled"':'').'>';
    for($i=1; $i<32; $i++) {
      if($i == date('d')){
        $select_e_d .= '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
      }else{
        $select_e_d .= '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
      } 
    }
    $select_e_d .='</select>';
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => TEXT_ORDER_START_DATE),
        array('text' => $select_y.TEXT_ORDER_YEAR.$select_m.TEXT_ORDER_MONTH.$select_d.TEXT_ORDER_DAY)
        );
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => TEXT_ORDER_END_DATE),
        array('text' => $select_e_y.TEXT_ORDER_YEAR.$select_e_m.TEXT_ORDER_MONTH.$select_e_d.TEXT_ORDER_DAY)
        );
 
    $contents[]['text'] = array(
        array('text' => HEADING_TITLE_ORDER_STATUS),
        array('text' => tep_draw_pull_down_menu('order_status', tep_array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $all_orders_statuses),'',(($is_disabled_single)?' disabled="disabled"':''), '').'&nbsp;&nbsp;&nbsp;&nbsp;'.tep_html_element_button(TEXT_ORDER_CSV_OUTPUT,(($is_disabled_single)?' disabled="disabled"':'')."onclick=' orders_csv_exe(".$ocertify->npermission.") '"))
        );
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => HEADING_TITLE_PREORDER_STATUS),
        array('text' =>tep_draw_pull_down_menu('preorder_status', tep_array_merge(array(array('id' => '', 'text' => TEXT_ALL_PREORDERS)), $all_preorders_statuses),'',(($is_disabled_single)?' disabled="disabled"':''), '').'&nbsp;&nbsp;&nbsp;&nbsp;'.tep_html_element_button(TEXT_PREORDER_CSV_OUTPUT,(($is_disabled_single)?' disabled="disabled"':'')."onclick=' preorders_csv_exe(".$ocertify->npermission.") '"))
        );
    $update_data  = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'DATA_MANAGEMENT' and configuration_value = 'mag_orders'"));
    $contents[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($update_data['user_added'])?$update_data['user_added']:TEXT_UNSET_DATA))),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null(tep_datetime_short($update_data['date_added'])))?tep_datetime_short($update_data['date_added']):TEXT_UNSET_DATA))
      );
    $contents[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($update_data['user_update'])?$update_data['user_update']:TEXT_UNSET_DATA))),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null(tep_datetime_short($update_data['last_modified'])))?tep_datetime_short($update_data['last_modified']):TEXT_UNSET_DATA))
      );
    $notice_box->get_form($form_str);
    $notice_box->get_heading($heading);
    $notice_box->get_contents($contents, $buttons);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
  }
}else if($_GET['action'] == 'edit_present'){
include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_PRESENT);
include(DIR_FS_ADMIN.'classes/notice_box.php');
$notice_box = new notice_box('popup_order_title', 'popup_order_info');
$today = getdate();
$yyyy = $today['year'];
$mm = $today['mon'];
$dd = $today['mday'];
$pd = $dd + 1;
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
$action_sid = $_GET['site_id'];
while($userslist= tep_db_fetch_array($sites_id)){
     $site_permission = $userslist['site_permission']; 
}
$sites_sql = tep_db_query("SELECT * FROM `sites`");
if(isset($site_permission)) $site_arr=$site_permission;//权限判断
else $site_arr="";
$site_array = explode(',',$site_arr);
if(!in_array($action_sid,$site_array) && $action_sid != -1){
   $disabled = 'disabled="disabled"'; 
}
if($_GET['site_id'] == -1){
   $_GET['site_id'] = '';
}
if($_GET['type'] == 'view'){
    $present_query_raw = "
      select g.goods_id,
             g.html_check,
             g.title,
             g.image,
             g.text,
             g.start_date,
             g.limit_date,
             s.romaji,
             g.site_id,
             g.goods_id,
             g.date_added,
             g.date_update
      from ".TABLE_PRESENT_GOODS." g , ".TABLE_SITES." s
      where s.id = g.site_id  and ".$_GET['sql']. " order by ".$_GET['str'];
    $present_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $present_query_raw, $present_query_numrows);
    $present_query = tep_db_query($present_query_raw);
    $cid_array = array();
    while ($present = tep_db_fetch_array($present_query)) {
         $cid_array[] = $present['goods_id'];
    }
$sele1_id = (int)$_GET['cID'];
$sele1 = tep_db_query("
    select g.goods_id,
           g.html_check,
           g.title,
           g.image,
           g.text,
           g.start_date,
           g.limit_date,
           g.site_id,
           g.user_added,
           g.user_update,
           g.date_added,
           g.date_update,
           s.romaji,
           s.name as site_name
    from ".TABLE_PRESENT_GOODS." g, ".TABLE_SITES." s
    where g.goods_id = '".$sele1_id."'
      and g.site_id = s.id
    ");
$sql1 = tep_db_fetch_array($sele1);
//期间
$sele_sty = substr($sql1['start_date'],0,4);
$sele_stm = substr($sql1['start_date'],5,2);
$sele_std = substr($sql1['start_date'],8,2);
$sele_liy = substr($sql1['limit_date'],0,4);
$sele_lim = substr($sql1['limit_date'],5,2);
$sele_lid = substr($sql1['limit_date'],8,2);
   
 foreach ($cid_array as $c_key => $c_value) {
    if ($_GET['cID'] == $c_value) {
      break; 
    }
  }
  $page_str = '';
  if ($c_key > 0) {
    $present_site_id = tep_db_query(" select * from ".TABLE_PRESENT_GOODS." where goods_id= '".$cid_array[$c_key-1]."'");
    $present_site_id_row = tep_db_fetch_array($present_site_id); 
    $page_str .= '<a onclick=\'show_present("",'.$cid_array[$c_key-1].','.$present_site_id_row['site_id'].',"view",'.$_GET['page'].')\' href="javascript:void(0);" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
  if ($c_key < (count($cid_array) - 1)) {
    $present_site_id = tep_db_query(" select * from ".TABLE_PRESENT_GOODS." where goods_id= '".$cid_array[$c_key+1]."'");
    $present_site_id_row = tep_db_fetch_array($present_site_id); 
    $page_str .= '<a onclick=\'show_present("",'.$cid_array[$c_key+1].','.$present_site_id_row['site_id'].',"view",'.$_GET['page'].')\' href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }else{
    $page_str .= '<font color="#000000">&nbsp;&nbsp;'.IMAGE_NEXT.'></font>'; 
  }
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => $sql1['title']);
    $heading[] = array('align' => 'right', 'text' => $page_str);
    $contents = array();
    $form_str = '<form name="apply" name="view" method="post" action="present.php?action=update&cID='.$sele1_id.'" enctype="multipart/form-data">';
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => ENTRY_SITE),
        array('text' => tep_get_site_romaji_by_id($sql1['site_id']))
    ); 
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => PRESENT_NAME_TEXT),
        array('text' => '<input name="title" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" '.($disabled?$disabled:'').'type="text" value="'.$sql1['title'].'" id="title"><span id="title_error"></span>')
    ); 

    if($sql1['image']){ $present_image =  tep_info_image('present/'.$sql1['image'], $sql1['title'],'','',$sql1['site_id']) ; }
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => PRESENT_IMAGE_TEXT),
        array('text' => '<input type="file" '.($disabled?$disabled:'').'name="file"><br>'.$present_image)
    ); 
    if($sql1['html_check'] == 1){
       $present_input =  '<input type="checkbox" name="ht" value="1" '.($disabled?$disabled:'').' checked>'.PRESENT_HTML_READ."\n";
       $present_input .= '<br><textarea onfocus="o_submit_single = false;" onblur="o_submit_single = true;" name="text" '.($disabled?$disabled:'').' style="width:95%; height:300px;resize:vertical;">'.stripslashes($sql1['text']).'</textarea><span id="text_error"></span>'."\n";
    }else{
       $present_input =  '<input type="checkbox" name="ht" '.($disabled?$disabled:'').' value="1">'.PRESENT_HTML_READ."\n";
       $present_input .='<br><textarea onfocus="o_submit_single = false;" onblur="o_submit_single = true;" name="text" '.($disabled?$disabled:'').' style="width:95%; height:300px;
resize:vertical;">'.stripslashes($sql1['text']).'</textarea><br><span id="text_error"></span>'."\n";
    }
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => PRESENT_COMMENT_TEXT),
        array('text' => $present_input)
    ); 
          $present_y = '<select name="start_y" '.($disabled?$disabled:'').'>';
          for($y=$sele_sty;$y<=$yyyy+5;$y++){
            $nen = $y;
            if($sele_sty == $nen){
              $present_y .= "<option value=\"$nen\" selected>$nen</option>"."\n";
            }else{ $present_y .= "<option value=\"$nen\">$nen</option>"."\n"; }
          }
          $present_y .= '</select>';
          $present_m = '<select name="start_m" '.($disabled?$disabled:'').'>';
          for($m=1;$m<=9;$m++){
            if($sele_stm == $m){
              $present_m .=  "<option value=\"0$m\" selected>$m</option>"."\n";
            }else{
              $present_m .= "<option value=\"0$m\">$m</option>"."\n";
            }
          }
          for($m=10;$m<=12;$m++){
            if($sele_stm == $m){
              $present_m .= "<option value=\"$m\" selected>$m</option>"."\n";
            }else{
              $present_m .= "<option value=\"$m\">$m</option>"."\n";
            }
          }
              $present_m .= '</select>';
              $present_d = '<select name="start_d" '.($disabled?$disabled:'').'>';
          for($d=1;$d<=9;$d++){
            if($sele_std == $d){
              $present_d .= "<option value=\"0$d\" selected>$d</option>"."\n";
            }else{
              $present_d .= "<option value=\"0$d\">$d</option>"."\n";
            }
          }
          for($d=10;$d<=31;$d++){
            if($sele_std == $d){
              $present_d .="<option value=\"$d\" selected>$d</option>"."\n";
            }else{
              $present_d .="<option value=\"$d\">$d</option>"."\n";
            }
          }
              $present_d .= '</select>';
              $present_d .= DAY_TEXT.'<br>';
              $present_l_y = KEYWORDS_SEARCH_END_TEXT;
              $present_l_y .= '<select name="limit_y" '.($disabled?$disabled:'').'>';
          for($y=$sele_sty;$y<=$yyyy+5;$y++){
            $nen = $y;
            if($sele_liy == $nen){
              $present_l_y .= "<option value=\"$nen\" selected>$nen</option>"."\n";
            }else{
              $present_l_y .=  "<option value=\"$nen\">$nen</option>"."\n";
            }
          }
              $present_l_y .= '</select>';
              $present_l_m = YEAR_TEXT;
              $present_l_m .= '<select name="limit_m" '.($disabled?$disabled:'').'>';
          for($m=1;$m<=9;$m++){
            if($sele_lim == $m){
              $present_l_m .= "<option value=\"0$m\" selected>$m</option>"."\n";
            }else{
              $present_l_m .= "<option value=\"0$m\">$m</option>"."\n";
            }
          }
          for($m=10;$m<=12;$m++){
            if($sele_lim == $m){
              $present_l_m .= "<option value=\"$m\" selected>$m</option>"."\n";
            }else{
              $present_l_m .= "<option value=\"$m\">$m</option>"."\n";
            }
          }
              $present_l_m .= '</select>';
              $present_l_d  = MONTH_TEXT;
              $present_l_d .= '<select name="limit_d" '.($disabled?$disabled:'').'>';
          for($d=1;$d<=9;$d++){
            if($sele_lid == $d){
              $present_l_d .= "<option value=\"0$d\" selected>$d</option>"."\n";
            }else{
              $present_l_d .= "<option value=\"0$d\">$d</option>"."\n";
            }
          }
          for($d=10;$d<=31;$d++){
            if($sele_lid == $d){
              $present_l_d .="<option value=\"$d\" selected>$d</option>"."\n";
            }else{
              $present_l_d .= "<option value=\"$d\">$d</option>"."\n";
            }
          }
              $present_l_d .= '</select>';
              $present_l_d .= DAY_TEXT;
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => PRESENT_DATE_TEXT),
        array('text' => KEYWORDS_SEARCH_START_TEXT.$present_y.YEAR_TEXT.$present_m.MONTH_TEXT.$present_d.$present_l_y.$present_l_m.$present_l_d.'<br><span id="select_error"></span>')
    );
    $contents[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($sql1['user_added'])?$sql1['user_added']:TEXT_UNSET_DATA))),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null(tep_datetime_short($sql1['date_added'])))?tep_datetime_short($sql1['date_added']):TEXT_UNSET_DATA))
      );
    $contents[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($sql1['user_update'])?$sql1['user_update']:TEXT_UNSET_DATA))),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null(tep_datetime_short($sql1['date_update'])))?tep_datetime_short($sql1['date_update']):TEXT_UNSET_DATA))
      );
    if($disabled){
    $button[] = '<a>'.tep_html_element_button(IMAGE_SAVE,$disabled.'id="button_save"').'</a>'.(($ocertify->npermission >= 15)?'<a>' .  tep_html_element_button(IMAGE_DELETE,$disabled) .  '</a>':'').'<a>' .  tep_html_element_button(PRESENT_LIST,$disabled) . '</a>';
    }else{
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'id="button_save" onclick="msg(\''.$ocertify->npermission.'\')"').'</a>'.  (($ocertify->npermission >= 15)?' <a href="javascript:void(0)" onclick="view_delete('.$ocertify->npermission.','.$cID.')"> ' .  tep_html_element_button(IMAGE_DELETE) .  '</a>':'').'<a href="' .  tep_href_link(FILENAME_PRESENT, 'site_id='.$sql1['site_id'].'&cID=' . $cID .  '&action=list') . '">' .  tep_html_element_button(PRESENT_LIST) . '</a>';
    }
    if(!empty($button)){
         $buttons = array('align' => 'center', 'button' => $button);
    }
    $notice_box->get_form($form_str);
    $notice_box->get_heading($heading);
    $notice_box->get_contents($contents, $buttons);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
}else if($_GET['type'] == 'input'){
   $sites_sql = tep_db_query("SELECT * FROM `sites`");
   $show_site_arr = array();
   while($sites_row = tep_db_fetch_array($sites_sql)){
     $show_site_arr[] = $sites_row['id']; 
   }
   $present_site_arr = array_intersect($show_site_arr,$site_array);
   $site_id_name = "<select id='present_site_id' name='site_id' $disabled>";
   foreach($present_site_arr as $value){
     if($value!=0){
       $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$value));
       $site_id_name .= "<option value='".$site_name['id'] ."'>".$site_name['name']."</option>";
     }
   }
   $site_id_name .= "</select>";
   $site_id_name .= '&nbsp;<font color="#ff0000;">*'.TEXT_REQUIRED.'</font>'; 

    $page_str  = '';
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => PRESENT_CREATE_TITLE);
    $heading[] = array('align' => 'right', 'text' => $page_str);
    $contents = array();
    $form_str = '<form name="apply" action="present.php?action=insert&page='.$_GET['page'].'" method="post" enctype="multipart/form-data">';
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => ENTRY_SITE),
        array('text' => $site_id_name.'<input type="hidden" name="user_added" value="'.$_SESSION['user_name'].'"><input type="hidden" name="user_update" value="'.$_SESSION['user_name'].'">')
    ); 
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => PRESENT_NAME_TEXT),
        array('text' => '<input name="title" type="text" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" id="title"><span id="title_error"></span>')
    ); 
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => PRESENT_IMAGE_TEXT),
        array('text' => '<input type="file" name="file">')
    ); 
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => PRESENT_COMMENT_TEXT),
        array('text' => '<input type="checkbox" name="ht" value="1">'.PRESENT_HTML_READ.'<br> <textarea onfocus="o_submit_single = false;" onblur="o_submit_single = true;" name="text" style="width:65%; height:100px;resize:vertical;"></textarea><br><span id="text_error"></span>')
    ); 
          $present_y = '<select name="start_y">';
          for($y=0;$y<=5;$y++){
            $nen = $yyyy + $y;
            if($yyyy == $nen){
              $present_y .= "<option value=\"$nen\" selected>$nen</option>"."\n";
            }else{
              $present_y .= "<option value=\"$nen\">$nen</option>"."\n";
            }
          }
          $present_y .= '</select>';
          $present_y .=  YEAR_TEXT;
          $present_m = '<select name="start_m">';
          for($m=1;$m<=9;$m++){
            if($mm == $m){
              $present_m .= "<option value=\"0$m\" selected>$m</option>"."\n";
            }else{
              $present_m .= "<option value=\"0$m\">$m</option>"."\n";
            }
          }
          for($m=10;$m<=12;$m++){
            if($mm == $m){
              $present_m .= "<option value=\"$m\" selected>$m</option>"."\n";
            }else{
              $present_m .= "<option value=\"$m\">$m</option>"."\n";
            }
          }
              $present_m .= '</select>';
              $present_m .=  MONTH_TEXT;
              $present_d = '<select name="start_d">';
          for($d=1;$d<=9;$d++){
            if($dd == $d){
              $present_d .= "<option value=\"0$d\" selected>$d</option>"."\n";
            }else{
              $present_d .= "<option value=\"0$d\">$d</option>"."\n";
            }
          }
          for($d=10;$d<=31;$d++){
            if($dd == $d){
              $present_d .= "<option value=\"$d\" selected>$d</option>"."\n";
            }else{
              $present_d .= "<option value=\"$d\">$d</option>"."\n";
            }
          }
              $present_d .= '</select>';
              $present_d .=  DAY_TEXT.'<br>';
              $present_l_y =  KEYWORDS_SEARCH_END_TEXT;
              $present_l_y .= '<select name="limit_y">';
          for($y=0;$y<=5;$y++){
            $nen = $yyyy + $y;
            if($yyyy == $nen){
              $present_l_y .= "<option value=\"$nen\" selected>$nen</option>"."\n";
            }else{
              $present_l_y .= "<option value=\"$nen\">$nen</option>"."\n";
            }
          }
              $present_l_y .= '</select>';
              $present_l_y .=  YEAR_TEXT;
              $present_l_m = '<select name="limit_m">';
          for($m=1;$m<=9;$m++){
            if($mm == $m){
              $present_l_m .= "<option value=\"0$m\" selected>$m</option>"."\n";
            }else{
              $present_l_m .= "<option value=\"0$m\">$m</option>"."\n";
            }
          }
          for($m=10;$m<=12;$m++){
            if($mm == $m){
              $present_l_m .= "<option value=\"$m\" selected>$m</option>"."\n";
            }else{
              $present_l_m .= "<option value=\"$m\">$m</option>"."\n";
            }
          }
              $present_l_m .= '</select>';
              $present_l_m .= MONTH_TEXT;
              $present_l_d = '<select name="limit_d">';
          for($d=1;$d<=9;$d++){
            if($pd == $d){
              $present_l_d .= "<option value=\"0$d\" selected>$d</option>"."\n";
            }else{
              $present_l_d .= "<option value=\"0$d\">$d</option>"."\n";
            }
          }
          for($d=10;$d<=31;$d++){
            if($pd == $d){
              $present_l_d .= "<option value=\"$d\" selected>$d</option>"."\n";
            }else{
              $present_l_d .=  "<option value=\"$d\">$d</option>"."\n";
            }
          }
             $present_l_d .= '</select>';
             $present_l_d .= DAY_TEXT;
     $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => PRESENT_DATE_TEXT),
        array('text' => KEYWORDS_SEARCH_START_TEXT.$present_y.$present_m.$present_d.$present_l_y.$present_l_m.$present_l_d.'<br><span id="select_error"></span>')
     ); 
    $contents[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($sql1['user_added'])?$sql1['user_added']:TEXT_UNSET_DATA))),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null(tep_datetime_short($sql1['date_added'])))?tep_datetime_short($sql1['date_added']):TEXT_UNSET_DATA))
      );
    $contents[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($sql1['user_update'])?$sql1['user_update']:TEXT_UNSET_DATA))),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null(tep_datetime_short($sql1['date_update'])))?tep_datetime_short($sql1['date_update']):TEXT_UNSET_DATA))
      );
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(PRESENT_SAVE, 'id="button_save" onclick="msg(\''.$ocertify->npermission.'\');"').'</a>';
    if(!empty($button)){
         $buttons = array('align' => 'center', 'button' => $button);
    }
    $notice_box->get_form($form_str);
    $notice_box->get_heading($heading);
    $notice_box->get_contents($contents, $buttons);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
}else if($_GET['type'] == 'listview'){
   $list_query_raw = "
      select p.id ,
             p.goods_id, 
             p.customer_id, 
             p.family_name, 
             p.first_name, 
             p.mail, 
             p.tourokubi, 
             s.romaji,
             g.site_id
      from ".TABLE_PRESENT_APPLICANT." p , ".TABLE_SITES." s, ".TABLE_PRESENT_GOODS." g
      where p.goods_id='".$_GET['cID']."' 
        and g.goods_id = p.goods_id
        and s.id = g.site_id
      order by ".$_GET['str'];
    $present_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $list_query_raw, $present_query_numrows);
    $list_query = tep_db_query($list_query_raw);
    $cid_array = array();
    while ($list = tep_db_fetch_array($list_query)) {
      $cid_array[] = $list['id'];
    }
$lv_id = tep_db_prepare_input($_GET['list_id']);
$sele2 = tep_db_query(" select * from ".TABLE_PRESENT_APPLICANT." where id = '".$lv_id."'");
$sql2 = tep_db_fetch_array($sele2);
    $page_str  = '';
    foreach ($cid_array as $c_key => $c_value) {
       if ($_GET['list_id'] == $c_value) {
           break; 
        }
    }
    if ($c_key > 0) {
      $page_str .= '<a onclick=\'show_present("",'.$_GET['cID'].','.$_GET['site_id'].',"listview",'.$_GET['page'].','.$cid_array[$c_key-1].')\' href="javascript:void(0);" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
    }
    if ($c_key < (count($cid_array) - 1)) {
      $page_str .= '<a onclick=\'show_present("",'.$_GET['cID'].','.$_GET['site_id'].',"listview",'.$_GET['page'].','.$cid_array[$c_key+1].')\' href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
    }else{
     $page_str .= '<font color="#000000">&nbsp;&nbsp;'.IMAGE_NEXT.'></font>'; 
    }
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => PRESENT_CUSTOMER_TITLE);
    $heading[] = array('align' => 'right', 'text' => $page_str);
    $contents = array();
    $form_str = '<form name="listview" method="post">';
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => TEXT_CUSTOMERS_ID),
        array('text' => $sql2['customer_id'])
        );
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => PRESENT_CUSTOMER_NAME),
        array('text' => htmlspecialchars($sql2['family_name'].$sql2['first_name']))
        );
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => TEXT_EMAIL),
        array('text' => $sql2['mail'])
        );
    $zone_query = tep_db_query(" select zone_name from " . TABLE_ZONES . " where zone_country_id = '".STORE_COUNTRY."' and zone_id = '".$sql2['zone_name']."' ");
    $zone = tep_db_fetch_array($zone_query);
    //礼物配送地址 
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => PRESENT_CUSTOMER_ADDRESS),
        array('text' => $sql2['address'])
        ); 
    $contents[]['text'] = array(
        array('params' => 'width="30%"','text' => PRESENT_CUSTOMER_APPLYDAY),
        array('text' => $sql2['tourokubi'])
        );
    $button[] = (($ocertify->npermission >= 15)?'<a href="javascript:void(0)" onclick="list_delete('.$ocertify->npermission.','.$cID.','.$sql2['id'].')">' .  tep_html_element_button(IMAGE_DELETE) .  '</a>':'');
    if(!empty($button)){
         $buttons = array('align' => 'center', 'button' => $button);
    }
    $notice_box->get_form($form_str);
    $notice_box->get_heading($heading);
    $notice_box->get_contents($contents, $buttons);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
}
} else if ($_GET['action'] == 'product_link_to_box') {
/* -----------------------------------------------------
    功能: 链接商品的弹出框
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
  $heading[] = array('align' => 'left', 'text' => IMAGE_LINK_TO);
  $heading[] = array('align' => 'right', 'text' => $page_str);
  
  $buttons = array();
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick="toggle_category_form(\''.$ocertify->npermission.'\', \'7\')"').'</a>'; 
  
  $buttons = array('align' => 'center', 'button' => $button); 
  
  $copy_product_info = array();
  
  $copy_product_info[]['text'] = array(
        array('text' => str_replace(':','',TEXT_INFO_CURRENT_CATEGORIES) . tep_draw_hidden_field('products_id', $pInfo->products_id)), 
        array('text' => tep_output_generated_category_path($pInfo->products_id, 'product'))
      );
  $products_link_array = tep_generate_category_path($pInfo->products_id, 'product');
  $categories_products_array = array();
  foreach($products_link_array as $value){

    $products_link_temp_array = end($value);
    $categories_products_array[] = $products_link_temp_array['id'];
  }
  $copy_product_info[]['text'] = array(
        array('text' => IMAGE_LINK_TO), 
        array('text' => tep_draw_pull_down_menu('categories_id', tep_get_category_tree('0','',$categories_products_array,'',false), ''))
      ); 

  $form_str = tep_draw_form('copy_to', FILENAME_CATEGORIES, 'action=link_to_confirm&cPath=' . $cPath);
  
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
   
  $notice_box->get_contents($copy_product_info, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if($_GET['action'] == 'edit_banner'){
/* -----------------------------------------------------
    功能: Banner管理的弹出框
    参数: $_GET['bID'] banner id 
    参数: $_GET['site_id'] 网站id 
    参数: $_GET['page'] 当前页 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_BANNER_MANAGER);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
$sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
$action_sid = $_GET['site_id'];
while($userslist= tep_db_fetch_array($sites_id)){
     $site_permission = $userslist['site_permission']; 
}
$sites_sql = tep_db_query("SELECT * FROM `sites`");
if(isset($site_permission)) $site_arr=$site_permission;//权限判断
else $site_arr="";
$site_array = explode(',',$site_arr);
if(!in_array($action_sid,$site_array) && $action_sid != -1){
   $disabled = 'disabled="disabled"'; 
}
  if($_GET['bID'] == '-1'){ $_GET['bID'] = '';}
  if($_GET['site_id'] == '-1'){ $_GET['site_id'] = '';}
    $banners_query_raw = "
      select sum(h.banners_shown) as banners_shown,
             sum(h.banners_clicked) as banners_clicked,
             b.banners_id, 
             b.banners_title, 
             b.banners_image, 
             b.banners_group, 
             b.status, 
             b.expires_date, 
             b.expires_impressions, 
             b.date_status_change, 
             b.date_scheduled, 
             b.date_added,
	     b.user_added,
	     b.user_update,
	     b.date_update,
             b.site_id,
             s.romaji,
             s.name as site_name
      from " . TABLE_BANNERS . " b left join  " . TABLE_BANNERS_HISTORY . " h on b.banners_id = h.banners_id , ".TABLE_SITES." s 
      where s.id = b.site_id and 
        " . $_GET['sql'] . "  group by b.banners_id 
      order by ".$_GET['str'];
    $banners_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $banners_query_raw, $banners_query_numrows);
    $banners_query = tep_db_query($banners_query_raw);
    $banner_num = tep_db_num_rows($banners_query);
    $cid_array = array();
   while ($banners = tep_db_fetch_array($banners_query)) {
       $cid_array[] = $banners['banners_id']; 
   } 
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
    //新建/编辑页面 
    $form_action = 'insert';
    if (isset($_GET['bID']) && $_GET['bID']) {
      $bID = tep_db_prepare_input($_GET['bID']);
      $site_id = tep_db_prepare_input($_GET['lsite_id']);
      $form_action = 'update';
$banner_query = tep_db_query("
          select b.banners_title, 
                 b.banners_url, 
                 b.banners_image, 
                 b.banners_group, 
                 b.banners_html_text, 
                 b.status,
                 b.user_added,
                 b.user_update,
                 b.date_update,
                 b.date_added,
                 b.banners_id,
                 b.date_scheduled,
                 b.expires_date, 
                 b.expires_impressions, 
                 b.date_status_change,
                 b.site_id,
                 b.banners_show_type,
                 s.romaji,
                 s.name as site_name
          from " . TABLE_BANNERS . " b, ".TABLE_SITES." s
          where banners_id = '" . tep_db_input($bID) . "'
            and s.id = b.site_id  and ".$_GET['sql']."
          ");
      $banner = tep_db_fetch_array($banner_query);
      $bInfo = new objectInfo($banner);
    } elseif ($_POST) {
      $bInfo = new objectInfo($_POST);
    } else {
      $bInfo = new objectInfo(array());
    }

    $groups_array = array();
    $groups_query = tep_db_query("
        select distinct banners_group 
        from " . TABLE_BANNERS . " 
        order by banners_group");
    while ($groups = tep_db_fetch_array($groups_query)) {
      $groups_array[] = array('id' => $groups['banners_group'], 'text' => $groups['banners_group']);
    }
?>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript">
  var dateExpires = new ctlSpiffyCalendarBox("dateExpires", "new_banner", "expires_date","btnDate1","<?php echo isset($bInfo->expires_date)?$bInfo->expires_date:''; ?>",scBTNMODE_CUSTOMBLUE);
  var dateScheduled = new ctlSpiffyCalendarBox("dateScheduled", "new_banner", "date_scheduled","btnDate2","<?php echo isset($bInfo->date_scheduled)?$bInfo->date_scheduled:''; ?>",scBTNMODE_CUSTOMBLUE);
</script>
      <?php
 foreach ($cid_array as $c_key => $c_value) {
    if ($_GET['bID'] == $c_value) {
      break; 
    }
  }
  $page_str = '';
  if($_GET['bID'] != ''){
  if ($c_key > 0) {
    $banner_site_id = tep_db_query(" select * from ".TABLE_BANNERS." where banners_id = '".$cid_array[$c_key-1]."'");
    $banner_site_id_row = tep_db_fetch_array($banner_site_id); 
    $page_str .= '<a onclick=\'show_banner("",'.$cid_array[$c_key-1].','.$_GET['page'].','.$banner_site_id_row['site_id'].')\' href="javascript:void(0);" id="option_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
  if ($c_key < (count($cid_array) - 1)) {
    $banner_site_id = tep_db_query(" select * from ".TABLE_BANNERS." where banners_id= '".$cid_array[$c_key+1]."'");
    $banner_site_id_row = tep_db_fetch_array($banner_site_id); 
    $page_str .= '<a onclick=\'show_banner("",'.$cid_array[$c_key+1].','.$_GET['page'].','.$banner_site_id_row['site_id'].')\' href="javascript:void(0);" id="option_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }else{
    $page_str .= '<font color="#000000">&nbsp;&nbsp;'.IMAGE_NEXT.'></font>'; 
  }
  }
    $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
    $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
    $heading[] = array('align' => 'left', 'text' => ($bInfo->banners_title?$bInfo->banners_title:HEADING_TITLE));
    $heading[] = array('align' => 'right', 'text' => $page_str);
   $sites_sql = tep_db_query("SELECT * FROM `sites`");
   $show_site_arr = array();
   while($sites_row = tep_db_fetch_array($sites_sql)){
     $show_site_arr[] = $sites_row['id']; 
   }
   $present_site_arr = array_intersect($show_site_arr,$site_array);
   $site_id_name = "<select id='present_site_id' name='site_id' $disabled>";
   foreach($present_site_arr as $value){
     if($value!=0){
       $site_name = tep_db_fetch_array(tep_db_query("select * from `sites` where id=".$value));
       $site_id_name .= "<option value='".$site_name['id'] ."'>".$site_name['name']."</option>";
     }
   }
   $site_id_name .= "</select>";
   $site_id_name .= '&nbsp;<font color="#ff0000;">*'.TEXT_REQUIRED.'</font>'; 
    $contents = array();
    if ($form_action == 'update') {
    $contents_banners[]['text'] = array(
        array('text' => ''), 
        array('text' => tep_draw_hidden_field('banners_id', $bID).tep_draw_hidden_field('site_id', $banner['site_id']))
        );
    }
    $contents_banners[]['text'] = array(
        array('params' => 'class="main" nowrap width="30%"','text' => '<input type="hidden" name="user_update" value="'.$user_info['name'].'"><input type="hidden" name="user_added" value="'.$user_info['name'].'">'.ENTRY_SITE), 
        array('params' => 'class="main"','text' => (isset($_GET['bID']) && $_GET['bID'])?tep_get_site_romaji_by_id($banner['site_id']):$site_id_name)
        );
    $contents_banners[]['text'] = array(
        array('text' => str_replace(':','',TEXT_BANNERS_TITLE)), 
        array('text' => tep_draw_input_field('banners_title', isset($bInfo->banners_title)?$bInfo->banners_title:'',$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;"style="width:60%"', true).'<span id="title_error"></span>')
        );
    $contents_banners[]['text'] = array(
        array('text' => str_replace(':','',TEXT_BANNERS_URL)), 
        array('text' => tep_draw_input_field('banners_url', isset($bInfo->banners_url)?$bInfo->banners_url:'',$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;"style="width:60%"').'<span id="url_error"></span>')
        );
    $contents_banners[]['text'] = array(
        array('text' => str_replace(':','',TEXT_BANNERS_GROUP)), 
        array('text' => tep_draw_pull_down_menu('banners_group', $groups_array, isset($bInfo->banners_group)?$bInfo->banners_group:'',$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;"') .  TEXT_BANNERS_NEW_GROUP .  '<br>' .  tep_draw_input_field('new_banners_group', '', $disabled.'style="width:60%"', ((sizeof($groups_array) > 0) ? false : true)).'<span id="group_error"></span><br>'.TEXT_ADVERTISEMENT_INFO)
        );
    $contents_banners[]['text'] = array(
        array('text' => ''), 
        array('text' => TEXT_BANNERS_BANNER_NOTE)
        );
    if($_GET['bID'] != ''){
    if(isset($bInfo->banners_show_type) && $bInfo->banners_show_type == 0){
         $checked_img = 'checked=""';
    }else{
         $checked_html = 'checked=""';
    }
    }else{
         $checked_img = 'checked=""';
    }
    $contents_banners[]['text'] = array(
        array('text' => TEXT_CONTENTS), 
        array('text' => '<input class="td_input_radio" type="radio" '.$checked_img.' onclick="check_radio(this.value)" value="0" class="td_input_radio" name="banner_show_type">'.str_replace(':','',TEXT_BANNERS_IMAGE).'<input class="td_input_radio" type="radio" '.$checked_html.' onclick="check_radio(this.value)" value="1" class="td_input_radio" name="banner_show_type">'.str_replace(':','',TEXT_BANNERS_HTML_TEXT))
        );
    $banners_start = $notice_box->get_table($contents_banners);  
    $contents[]['text'] = array(
        array('params' => 'width="100%" colspan="3"','text' => $banners_start)
     );
    $contents_img_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0', 'parameters' => 'id="banners_image_hide"');
    $contents_img[]['text'] = array(
        array('params' => 'class="main" valign="top" nowrap width="30%"'), 
        array('text' => tep_draw_file_field('banners_image','',$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;"') . ' ' .  TEXT_BANNERS_IMAGE_LOCAL . '<br>' . (tep_get_upload_root().'x/') .  tep_draw_input_field('banners_image_local', isset($bInfo->banners_image)?$bInfo->banners_image:'',$disabled).'<br>'.(isset($bInfo->banners_image) && $bInfo->banners_image?tep_get_new_image('upload_images/'.$bInfo->site_id.'/'.$bInfo->banners_image, $bInfo->banners_title, '180', '120'):'').'<br>')
        );
    $banners_img = $notice_box->get_table($contents_img,'',$contents_img_params);  
    $contents[]['text'] = array(
        array('params' => 'width="100%" colspan="3"','text' => $banners_img)
     );
    $contents_html_params = array('width' => '100%', 'border' => '0', 'cellspacing' => '0', 'cellpadding' => '0', 'parameters' => 'id="banners_html_hide"');
    $contents_html[]['text'] = array(
        array('params' => 'valign="top" class="main" nowrap width="30%"'), 
        array('text' => tep_draw_textarea_field('html_text', 'soft', '60', '5', isset($bInfo->banners_html_text)?$bInfo->banners_html_text:'',$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="resize:vertical;"'))
        );
    $banners_html = $notice_box->get_table($contents_html,'',$contents_html_params);  
    $contents[]['text'] = array(
        array('params' => 'width="100%" colspan="3"','text' => $banners_html)
     ); 
    $text_date_scheduled = explode('/',$bInfo->date_scheduled); 
       if($text_date_scheduled[2] != null){ 
          $banner_date = $text_date_scheduled[2].'-'.$text_date_scheduled[1].'-'.$text_date_scheduled[0];
       } 
    if($disabled){
     $contents_end[]['text'] = array(
        array('text' => str_replace(':','',TEXT_BANNERS_SCHEDULED_AT)), 
        array('text' => '<div class="yui3-skin-sam yui3-g"><input type="text"'.$disabled.' onfocus="o_submit_single = false;" onblur="o_submit_single = true;" name="date_scheduled" id="input_date_scheduled" value="'.($bInfo->date_scheduled == '0000-00-00 00:00:00' || $bInfo->date_scheduled == ''?'':date('Y-m-d',strtotime($bInfo->date_scheduled))).' "/><img src="includes/calendar.png" '.$disabled.' onfocus="o_submit_single = false;" onblur="o_submit_single = true;"> <input type="hidden" name="toggle_open" value="0" id="toggle_open"> <div class="yui3-u" id="new_yui3"> <div id="mycalendar"></div> </div> </div>')
        );
    }else{
    $contents_end[]['text'] = array(
        array('text' => str_replace(':','',TEXT_BANNERS_SCHEDULED_AT)), 
        array('text' => '<div class="yui3-skin-sam yui3-g"><input type="text"'.$disabled.' onfocus="o_submit_single = false;" onblur="o_submit_single = true;" name="date_scheduled" id="input_date_scheduled" value="'.($bInfo->date_scheduled == '0000-00-00 00:00:00' || $bInfo->date_scheduled == ''?'':date('Y-m-d',strtotime($bInfo->date_scheduled))).' "/><a href="javascript:void(0);" onclick="open_new_calendar();" class="dpicker"><img src="includes/calendar.png" '.$disabled.' onfocus="o_submit_single = false;" onblur="o_submit_single = true;"></a> <input type="hidden" name="toggle_open" value="0" id="toggle_open"> <div class="yui3-u" id="new_yui3"> <div id="mycalendar"></div> </div> </div>')
        );
    }
    $contents_end[]['text'] = array(
        array('text' => ''), 
        array('text' => TEXT_BANNERS_SCHEDULE_NOTE)
        );
    $text_expires_date = explode('/',$bInfo->expires_date); 
       if($text_expires_date[2] != null && $text_expires_date[2] != 0000 ){ 
           $banner_end_date = $text_expires_date[2].'-'.$text_expires_date[1].'-'.$text_expires_date[0];
       }

    if($disabled){
    $contents_end[]['text'] = array(
        array('text' => str_replace(':','',TEXT_BANNERS_EXPIRES_ON)), 
        array('text' => ' <div class="yui3-skin-sam yui3-g"><input type="text" name="expires_date" id="input_expires_date" value="'.
          ($bInfo->expires_date == '0000-00-00 00:00:00' || $bInfo->expires_date == ''?'':date('Y-m-d',strtotime($bInfo->expires_date))).' " '.$disabled.' onfocus="o_submit_single = false;" onblur="o_submit_single = true;" /><img src="includes/calendar.png" '.$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;" ><input type="hidden" name="toggle_open_end" value="0" id="toggle_open_end"> <div class="yui3-u" id="end_yui3"> <div id="mycalendar_end"></div> </div> </div>'.TEXT_BANNERS_OR_AT.'<br>' . tep_draw_input_field('impressions', isset($bInfo->expires_impressions)?$bInfo->expires_impressions:'', 'maxlength="7" size="7"'.$disabled) . ' ' . TEXT_BANNERS_IMPRESSIONS)
        );
    }else{
    $contents_end[]['text'] = array(
        array('text' => str_replace(':','',TEXT_BANNERS_EXPIRES_ON)), 
        array('text' => ' <div class="yui3-skin-sam yui3-g"><input type="text" name="expires_date" id="input_expires_date" value="'.($bInfo->expires_date == '0000-00-00 00:00:00' || $bInfo->expires_date == ''?'':date('Y-m-d',strtotime($bInfo->expires_date))).' " '.$disabled.' onfocus="o_submit_single = false;" onblur="o_submit_single = true;" /><a href="javascript:void(0);" onclick="open_update_calendar();" class="dpicker"><img src="includes/calendar.png" '.$disabled.'onfocus="o_submit_single = false;" onblur="o_submit_single = true;" ></a> <input type="hidden" name="toggle_open_end" value="0" id="toggle_open_end"> <div class="yui3-u" id="end_yui3"> <div id="mycalendar_end"></div> </div> </div>'.TEXT_BANNERS_OR_AT.'<br>' . tep_draw_input_field('impressions', isset($bInfo->expires_impressions)?$bInfo->expires_impressions:'', 'maxlength="7" size="7"'.$disabled) . ' ' . TEXT_BANNERS_IMPRESSIONS)
        );
    }
    $contents_end[]['text'] = array(
        array('text' => ''), 
        array('text' => TEXT_BANNERS_EXPIRCY_NOTE)
        );
    if($_GET['bID'] != ''){
       $dir_ok = false;
    $banner_extension = tep_banner_image_extension();
        if ( (function_exists('imagecreate')) && ($banner_extension) ) {
           if (is_dir(DIR_WS_IMAGES . 'graphs')) {
             if (is_writeable(DIR_WS_IMAGES . 'graphs')) {
                  $dir_ok = true;
              } else {
                  $messageStack->add(ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE, 'error');
              }
            } else {
                  $messageStack->add(ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST, 'error');
            }
    }
    if ( (function_exists('imagecreate')) && ($dir_ok) && ($banner_extension)) {
           $banner_id = $bID;
           $days = '3';
           include(DIR_WS_INCLUDES . 'graphs/banner_infobox.php');
    $contents_end[]['text'] = array(
        array('text' => tep_image(DIR_WS_IMAGES .  'graph_hbar_blue.gif', 'Blue', '5', '5') . ' ' .  TEXT_BANNERS_BANNER_VIEWS . '/' .  tep_image(DIR_WS_IMAGES .  'graph_hbar_red.gif', 'Red', '5', '5') . ' ' .  TEXT_BANNERS_BANNER_CLICKS),
        array('text' => tep_image(DIR_WS_IMAGES . 'graphs/banner_infobox-' .  $banner_id .  '.' . $banner_extension))
        );
     } else {
    $banner_stats_query = tep_db_query("select dayofmonth(banners_history_date) as
        name, banners_shown as value, banners_clicked as dvalue from " .
        TABLE_BANNERS_HISTORY . " where banners_id = '" . $bInfo->banners_id . "' and
        to_days(now()) -to_days(banners_history_date) < 3 order by
        banners_history_date");
    while ($banner_stats = tep_db_fetch_array($banner_stats_query)) {
          $values[] = $banner_stats['value'];
          $dvalues[] = $banner_stats['dvalue'];
    }
    if($values[0] == 0 && $dvalues[0] == 0){
     $contents_end[]['text'] = array(
        array('text' => tep_image(DIR_WS_IMAGES .  'graph_hbar_blue.gif', 'Blue', '5', '5') . ' ' .  TEXT_BANNERS_BANNER_VIEWS . '/' .  tep_image(DIR_WS_IMAGES .  'graph_hbar_red.gif', 'Red', '5', '5') . ' ' .  TEXT_BANNERS_BANNER_CLICKS),
        array('text' => TEXT_UNSET_DATA)
        );
    }else{

           include(DIR_WS_FUNCTIONS . 'html_graphs.php');
    $contents_end[]['text'] = array(
        array('text' => tep_image(DIR_WS_IMAGES .  'graph_hbar_blue.gif', 'Blue', '5', '5') . ' ' .  TEXT_BANNERS_BANNER_VIEWS . '/' .  tep_image(DIR_WS_IMAGES .  'graph_hbar_red.gif', 'Red', '5', '5') . ' ' .  TEXT_BANNERS_BANNER_CLICKS),
        array('text' => tep_banner_graph_infoBox($bInfo->banners_id, '3'))
        );
     }
    }
    }
    $contents_end[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($bInfo->user_added)?$bInfo->user_added:TEXT_UNSET_DATA))),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.((tep_not_null(tep_datetime_short($bInfo->date_added)))?tep_datetime_short($bInfo->date_added):TEXT_UNSET_DATA))
      );
    $contents_end[]['text'] = array(
       array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null($bInfo->user_update)?$bInfo->user_update:TEXT_UNSET_DATA))),
       array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.((tep_not_null(tep_datetime_short($bInfo->date_update)))?tep_datetime_short($bInfo->date_update):TEXT_UNSET_DATA))
      );
  $buttons = array();
  if($disabled){
   if($_GET['bID'] != ''){
       if($ocertify->npermission >= 15){
         $delete_banner = tep_html_element_button(IMAGE_DELETE,$disabled);
       }
   }
  $button[] = (($form_action == 'insert') ?  tep_html_element_button(IMAGE_INSERT, ' id="button_save" '.$disabled) : tep_html_element_button(IMAGE_SAVE, $disabled)).$delete_banner;
  }else{
  $button[] = '<a href="javascript:void(0);">'.(($form_action == 'insert') ?  tep_html_element_button(IMAGE_INSERT, ' id="button_save" onclick="check_banner_form(0);"') : tep_html_element_button(IMAGE_SAVE, 'onclick="check_banner_form(0);"')).($_GET['bID'] != ''?($ocertify->npermission >= 15 ? ( '<a href="javascript:void(0)" onclick="msg('.$ocertify->npermission.','.$_GET['page'].','.$_GET['bID'].')">' .tep_html_element_button(IMAGE_DELETE) . '</a> '):''):'');
  }
  $banners_end = $notice_box->get_table($contents_end);  
  $contents[]['text'] = array(
      array('params' => 'width="100%" colspan="3"','text' => $banners_end)
   ); 
  $form_str = tep_draw_form('new_banner', FILENAME_BANNER_MANAGER, 'page=' .  (isset($_GET['page'])?$_GET['page']:'') .  '&sort='.$_GET['sort'].'&type='.$_GET['type'].'&action=' . $form_action .  (isset($_GET['lsite_id'])?('&lsite_id='.$_GET['lsite_id']):''), 'post', 'enctype="multipart/form-data"');
  $buttons = array('align' => 'center', 'button' => $button); 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($contents, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if($_GET['action'] == 'new_messages'){
 include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.'messages.php');
 include(DIR_FS_ADMIN.'classes/notice_box.php');
 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 $page_str = '<a onclick="hidden_info_box('.($_GET['messages_sta'] == 'drafts' && $_GET['latest_messages_id'] > 0 ? '1' : ($_GET['latest_messages_id'] < 0 ? '2' : '3')).');" href="javascript:void(0);">X</a>';
 if($_GET['latest_messages_id']<0){
	$heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
 	$heading[] = array('text' => NEW_MESSAGES);
	$form_str = tep_draw_form('new_latest_messages', 'messages.php','action=new_messages&messages_sort='.$_GET['messages_sort'].'&messages_sort_type='.$_GET['messages_sort_type'].'&page='.$_GET['page'].'&status='.$_GET['messages_sta'], 'post', 'enctype="multipart/form-data" onSubmit="return false;"');
 }else{
	//if($_GET['messages_sta'] != 'sent'){
		//tep_db_query('update messages set read_status = "1" where id = '.$_GET['latest_messages_id']);
	//}
 	$heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
	$heading[] = array('text' => $_GET['sender_name'].MESSAGES_SENDER);
	$form_str = tep_draw_form('new_latest_messages', 'messages.php','action=back_messages&messages_sort='.$_GET['messages_sort'].'&messages_sort_type='.$_GET['messages_sort_type'].'&id='.$_GET['latest_messages_id'].'&page='.$_GET['page'].'&status='.$_GET['messages_sta'], 'post', 'enctype="multipart/form-data" onSubmit="return false;"');
 } 
 $heading[] = array('align' => 'right', 'text' => '<span id="next_prev"></span>&nbsp&nbsp'.$page_str);
 
 $messages_content_table = array();
 $messages_content_row_from = array();
 $messages_content_row_from[] = array('params'=>'width="20%"','text'=>'From');
 if($_GET['latest_messages_id']<0){
   $messages_content_row_from[] = array('text'=>$_SESSION['user_name'].'<input type="hidden" name="messages_flag" id="messages_flag_id" value="0">');
 }else{
   $messages_content_row_from[] = array('text'=>$_GET['sender_name'].'<input type="hidden" name="messages_flag" id="messages_flag_id" value="0">');
 }
 $messages_content_table[] = array('text'=> $messages_content_row_from);
 $messages_content_row_to = array();
 $messages_content_row_to [] = array('text'=>'To');
 //groups 选中
 $groups_selected = ($_GET['messages_sta'] == 'drafts' || $_GET['messages_sta'] == 'sent') && $_GET['latest_messages_id'] >= 0 && trim($_GET['groups']) != '' ? ' checked="checked"' : '';
 $messages_to_all = '<input id="message_to_all" type="radio" value="0" name="messages_to" onclick="messages_to_all_radio()"><label for="message_to_all">ALL</label>';
 $messages_to_groups = '<input id="message_to_groups" type="radio" value="2" name="messages_to" onclick="messages_to_groups_radio()"'.$groups_selected.'><label for="message_to_groups">'.MESSAGE_SELECT_GROUPS.'</label>';
 $messages_to_appoint = '<input id="message_to_appoint" type="radio" value="1"'.($groups_selected == '' ? 'checked="checked"' : '').' name="messages_to" onclick="messages_to_appoint_radio()"><label for="message_to_appoint">'.MESSAGES_APPOINT_SB.'</label>';
 $messages_content_row_to [] = array('text'=>$messages_to_all.$messages_to_groups.$messages_to_appoint.'<input type="hidden" id="old_messages_to" value="'.($groups_selected != '' ? 2 : 1).'"><input type="hidden" id="current_messages_to" value="'.($groups_selected != '' ? 2 : 1).'">');
 $messages_content_table[] = array('text'=> $messages_content_row_to);
 $messages_content_row_choose = array();
 $messages_content_row_choose [] =  array('text'=> '');
 $sql_for_all_users = 'select userid, name,email from users where status=1 order by name asc';
 $sql_for_all_users_query = tep_db_query($sql_for_all_users);
 //组选择
 $all_user_to_td = '';
 $all_groups_to_td = '';
 $old_users_array = array();
 $old_groups_array = array();
   if(($_GET['messages_sta'] == 'drafts' || $_GET['messages_sta'] == 'sent') && $_GET['latest_messages_id'] >= 0){
	if($_GET['recipient_name'] == 'ALL'){
		while($message_all_users = tep_db_fetch_array($sql_for_all_users_query)){
                  $recipient .= '<div style="cursor:pointer;-moz-user-select:none;" onclick="checkbox_event(this,event)" value="'.$message_all_users['name'].'"><input hidden value="'.$message_all_users['userid'].'|||'.$message_all_users['name'].'" type="checkbox" name="selected_staff[]">'.$message_all_users['name'].'</div>';
                  $old_users_array[] = $message_all_users['userid'];
		}
	}else{
		$recipient_name_all = explode(';',$_GET['recipient_name']);
		while($message_all_users = tep_db_fetch_array($sql_for_all_users_query)){
			$n_flag = 0;
			foreach($recipient_name_all as $value){
				if($message_all_users['name'] == $value){
                                  $old_users_array[] = $message_all_users['userid'];
                                  $recipient .= '<div style="cursor:pointer;-moz-user-select:none;" onclick="checkbox_event(this,event)" value="'.$message_all_users['name'].'"><input hidden value="'.$message_all_users['userid'].'|||'.$message_all_users['name'].'" type="checkbox" name="selected_staff[]">'.$message_all_users['name'].'</div>';     
					$n_flag = 1;
					break;
				}
			}
			if($n_flag == 1){
				continue;
			}else{
				$all_user_to_td .= '<div style="cursor:pointer;-moz-user-select:none;" onclick="checkbox_event(this,event)" value="'.$message_all_users['name'].'"><input hidden value="'.$message_all_users['userid'].'|||'.$message_all_users['name'].'" type="checkbox" name="all_staff">'.$message_all_users['name'].'</div>';
			}
		}
	}
   }else{
	while($message_all_users = tep_db_fetch_array($sql_for_all_users_query)){
		if($_GET['latest_messages_id']>0&&$message_all_users['userid'] == $_GET['sender_id']){
                        $old_users_array[] = $message_all_users['userid'];
			$recipient = '<div style="cursor:pointer;-moz-user-select:none;" onclick="checkbox_event(this,event)" value="'.$message_all_users['name'].'"><input hidden value="'.$message_all_users['userid'].'|||'.$message_all_users['name'].'" type="checkbox" name="selected_staff[]">'.$message_all_users['name'].'</div>';
			continue;
		}
		$all_user_to_td .= '<div style="cursor:pointer;-moz-user-select:none;" onclick="checkbox_event(this,event)" value="'.$message_all_users['name'].'"><input hidden value="'.$message_all_users['userid'].'|||'.$message_all_users['name'].'" type="checkbox" name="all_staff">'.$message_all_users['name'].'</div>';
        } 
   } 
   if(trim($_GET['groups']) != ''){
     $groups_list_array = explode(',',$_GET['groups']);
     foreach($groups_list_array as $g_value){
       group_parent_id_list($g_value,$group_id_list_array);
       $group_id_list_array[] = $g_value;
       group_id_list($g_value,$group_id_list_array);
     }
     $groups_list = '';
     tep_groups_list(0,$groups_list,$level_num,$group_id_list_array);
     $recipient_groups .= $groups_list;
     $send_groups_list_str = $_GET['groups'];
   }else{
     $recipient_groups = ''; 
     $send_groups_list_str = '';
   }
   //获取组列表 
   $all_groups_array = array();
   $all_child_array = array();
   $groups_id_query = tep_db_query("select id from ".TABLE_GROUPS);
   while($groups_id_array = tep_db_fetch_array($groups_id_query)){

     $all_child_array[] = $groups_id_array['id'];
     $groups_child_query = tep_db_query("select id from ".TABLE_GROUPS." where parent_id='".$groups_id_array['id']."'");
     if(tep_db_num_rows($groups_child_query) <= 0){
       $all_groups_array[] = $groups_id_array['id']; 
     }
   }
   tep_db_free_result($groups_id_query);
   $groups_list = '';
   if(trim($_GET['groups']) != ''){
     $group_parent_list_array = array();
     foreach($groups_list_array as $groups_v){

       $group_parent_list_array[] = $groups_v;
       group_id_list($groups_v,$group_parent_list_array);
     }
     $child_diff = array_diff($all_child_array,$group_parent_list_array);
     $groups_diff = array_diff($all_groups_array,$group_parent_list_array);
     if(!empty($groups_diff)){
       tep_groups_list(0,$groups_list,$level_num,$child_diff,'delete'); 
     }
     $all_groups_str = implode(',',$groups_diff);
   }else{
     tep_groups_list(0,$groups_list); 
     $all_groups_str = implode(',',$all_groups_array);
   }
   $all_groups_to_td .= $groups_list;
 $messages_choose_table = '
<div width="100%" id="select_user"'.($groups_selected != '' ? ' style="display:none;"' : '').'><table width="100%">
	<tr>
		<td align="center" width="45%">'.MESSAGES_TO_BODY.'</td>
		<td align="center" width="10%"></td>
		<td align="center" width="45%">'.MESSAGES_STAFF.'</td>
	</tr>
	<tr>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div id="send_to" width="100%" style="overflow-y:scroll;height:105px;">'.$recipient.'</div></td>
		<td align="center" style="vertical-align:middle;">
			<button onclick="add_select_user()">&lt&lt'.ADD_STAFF.'</button><br>
			<button onclick="delete_select_user()">'.DELETE_STAFF.'&gt&gt</button>
		</td>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div width="100%" id="delete_to" style="overflow-y:scroll;height:105px;">'.$all_user_to_td.'</div></td>
	</tr>
</table></div>';
 //组选择
 $messages_content_row_group = array();
 $messages_content_row_group[] =  array('text'=> '');
 $messages_group_table = '
<div width="100%" id="select_groups"'.($groups_selected == '' ? ' style="display:none;"' : '').'><table width="100%">
	<tr>
		<td align="center" width="45%">'.MESSAGES_TO_BODY.'</td>
		<td align="center" width="10%"></td>
		<td align="center" width="45%">'.MESSAGES_STAFF.'</td>
	</tr>
	<tr>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><input type="hidden" id="send_groups_list" name="groups_id_list" value="'.$send_groups_list_str.'"><div id="send_to_groups" width="100%" style="overflow-y:scroll;height:105px;">'.$recipient_groups.'</div></td>
		<td align="center" style="vertical-align:middle;">
			<button onclick="add_select_groups()">&lt&lt'.ADD_STAFF.'</button><br>
			<button onclick="delete_select_groups()">'.DELETE_STAFF.'&gt&gt</button>
		</td>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><input type="hidden" id="delete_groups_list" value="'.$all_groups_str.'"><div width="100%" id="delete_to_groups" style="overflow-y:scroll;height:105px;">'.$all_groups_to_td.'</div></td>
	</tr>
</table></div>';
 $old_groups_array = $send_groups_list_str;
 $messages_content_row_choose [] = array('text'=> $messages_choose_table.$messages_group_table.'<input type="hidden" id="old_groups_str" value="'.$old_groups_array.'"><input type="hidden" id="old_users_str" value="'.implode(',',$old_users_array).'">');
 $messages_content_table[] = array('text'=> $messages_content_row_choose); 

 $messages_content_row_must_selected = array();
 //ALL 的数据
 $recipient_all = '';
 $all_users_list_query = tep_db_query($sql_for_all_users);
 while($message_all_users = tep_db_fetch_array($all_users_list_query)){
                  $recipient_all .= '<input hidden value="'.$message_all_users['userid'].'|||'.$message_all_users['name'].'" type="checkbox" name="all_users_list[]" checked="checked">';
 }
 tep_db_free_result($all_users_list_query);
 $messages_content_row_must_selected[] = array('params' => 'style="display:none"','text'=> $recipient_all);
 $messages_content_row_must_selected[] = array('text'=> '');
 $messages_content_row_must_selected[] = array('text'=> '<div id="messages_to_must_select" style="display: none;"><span style="color:#ff0000;">'.MESSAGES_TO_MUST_SELECTED.'</span></div>');
 $messages_content_table[] = array('text'=> $messages_content_row_must_selected);
 $mark_array = explode(',',$_GET['mark']);
 $pic_list_raw = tep_db_query("select * from ".TABLE_CUSTOMERS_PIC_LIST." order by sort_order asc"); 
   $users_icon = '<ul class="table_img_list" style="width:100%">'; 
   while ($pic_list_res = tep_db_fetch_array($pic_list_raw)) {
     $users_icon .= '<li><input type="checkbox" name="pic_icon[]" value="'.$pic_list_res['id'].'"'.(in_array($pic_list_res['id'],$mark_array) ? ' checked="checked"' : '').' style="padding-left:0;margin-left:0;"><img src="images/icon_list/'.$pic_list_res['pic_name'].'" alt="'.$pic_list_res['pic_alt'].'" title="'.$pic_list_res['pic_alt'].'"></li>'; 
   }
 $users_icon .= '</ul>';
 $messages_content_row_mark = array();
 $messages_content_row_mark[] = array('text'=> MESSAGES_MARK);
 $messages_content_row_mark[] = array('text'=> $users_icon.'<input type="hidden" id="old_mark_str" value="'.$_GET['mark'].'">');
 $messages_content_table[] = array('text'=> $messages_content_row_mark);
 $messages_content_row_text = array();
 $messages_content_row_text[] = array('text'=> MESSAGES_TEXT);
 if($_GET['latest_messages_id']>0){
	$sql_message_content = tep_db_query('select * from messages where id = "'.$_GET['latest_messages_id'].'"');
	$sql_message_content_res = tep_db_fetch_array($sql_message_content);
        $messages_text_area = '<textarea id="messages_text" style="overflow-y:hidden;width:100%;height:163px;" disabled="disabled" name="contents">'.$sql_message_content_res['content'].'</textarea><input type="hidden" name="drafts_contents" value="'.$sql_message_content_res['content'].'">';
 }else{
 	$messages_text_area =  '<textarea style="resize:vertical; width:100%;" class="textarea_width" rows="10" id="current_contents" name="contents"></textarea><input type="hidden" id="old_contents" value="">';
 }
 $messages_content_row_text[] = array('text'=> $messages_text_area);
 $messages_content_table[] = array('text'=> $messages_content_row_text);
 $messages_content_row_must_write = array();
 $messages_content_row_must_write[] = array('text'=> '');
 $messages_content_row_must_write[] = array('text'=> '<div id="messages_must_write" style="display: none;"><span style="color:#ff0000;">'.CONTENT_MUST_WRITE.'</span></div>');
 $messages_content_table[] = array('text'=> $messages_content_row_must_write);
 $messages_content_row_addfile = array();
 if($_GET['latest_messages_id']>0){
 }else{
 	$messages_content_row_addfile[] = array('text'=> MESSAGES_ADDFILE);
 	$messages_content_row_addfile[] = array('text'=> '<div id="messages_file_boder"><input type="file" id="messages_file" name="messages_file[]"><a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="file_cancel(\'messages_file\')">'.DELETE_STAFF.'</a>&nbsp;&nbsp;<a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="add_email_file(\'messages_file\')">'.BUTTON_ADD_TEXT.'</a></div>');
 }
 $messages_content_table[] = array('text'=> $messages_content_row_addfile);
 if($_GET['latest_messages_id']>0){
        $messages_email_array = array();
        $messages_users_query = tep_db_query("select userid,name,email from ".TABLE_USERS." where userid='".$sql_message_content_res['sender_id']."' or userid='".$sql_message_content_res['recipient_id']."'");
        while($messages_users_array = tep_db_fetch_array($messages_users_query)){

          $messages_email_array[$messages_users_array['userid']] = array('name'=>$messages_users_array['name'],
          
                                                                         'email'=>$messages_users_array['email'] 
                                                                         );
        }
        tep_db_free_result($messages_users_query);
        $messages_contents_back = "\r\n\r\n\r\n".'---------- Forwarded message ----------'."\r\n";
        $messages_contents_back .= 'From: '.$messages_email_array[$sql_message_content_res['sender_id']]['name'].' <'.$messages_email_array[$sql_message_content_res['sender_id']]['email'].'>'."\r\n";
        $messages_date_array = explode(' ',tep_date_long($sql_message_content_res['time']));
        $messages_contents_back .= 'Date: '.date(DATE_FORMAT_TEXT,strtotime($sql_message_content_res['time'])).' '.end($messages_date_array)."\r\n";
        //收件人信息列表
        $to_name = '';
        $sql_for_all_users_query = tep_db_query($sql_for_all_users); 
        if($_GET['recipient_name'] == 'ALL'){
		while($message_all_users = tep_db_fetch_array($sql_for_all_users_query)){
                  $to_name .= $message_all_users['name'].' <'.$message_all_users['email'].'>;';
		}
        }else{
                if(trim($_GET['groups']) != ''){
                  $recipient_name_array = explode('||||||',$_GET['recipient_name']);
                  $recipient_name_all = explode(';',$recipient_name_array[1]);
                }else{
                  $recipient_name_all = explode(';',$_GET['recipient_name']);
                }
		while($message_all_users = tep_db_fetch_array($sql_for_all_users_query)){
			foreach($recipient_name_all as $value){
				if($message_all_users['name'] == $value){
                                  $to_name .= $message_all_users['name'].' <'.$message_all_users['email'].'>;';
				}
			}	
		}
	}
        $messages_contents_back .= 'To: '.mb_substr($to_name,0,-1)."\r\n";
        $messages_contents_replace = str_replace("\r\n","\r\n>",$sql_message_content_res['content']);
        $messages_contents_back .= '>'.$messages_contents_replace;

        if($_GET['messages_sta'] == 'drafts'){

          $messages_contents_back = $sql_message_content_res['content'];
        }
	$messages_content_row_back = array();
	$messages_content_row_back[] = array('text'=> MESSAGES_BACK_CONTENT);
	$messages_content_row_back[] = array('text'=> '<textarea style="resize:vertical; width:100%;" class="textarea_width" rows="10" id="current_contents" name="back_contents">'.$messages_contents_back.'</textarea><input type="hidden" id="old_contents" value="'.$messages_contents_back.'">');
	$messages_content_table[] = array('text'=> $messages_content_row_back);
	$messages_content_row_back_must_write = array();
 	$messages_content_row_back_must_write[] = array('text'=> '');
 	$messages_content_row_back_must_write[] = array('text'=> '<div id="messages_back_must_write" style="display: none;"><span style="color:#ff0000;">'.BACK_CONTENT_MUST_WRITE.'</span></div>');
 	$messages_content_table[] = array('text'=> $messages_content_row_back_must_write);
	$messages_content_row_back_file = array();
	$messages_content_row_back_file[] = array('text'=> MESSAGES_BACK_FILE);
   $messages_attach_file = '';
   $file_list_arr = tep_get_messages_file($_GET['latest_messages_id']);
   foreach($file_list_arr as $f_index => $file_info){
	if($sql_message_content_res['attach_file'] == 1){
		$messages_file_name = $file_info['name'];
                if(file_exists('messages_upload/'.$messages_file_name)){
                        $messages_file_name = str_replace('*','/',$messages_file_name);
			$messages_file_name = base64_decode($messages_file_name);
			$messages_file_name = explode('|||',$messages_file_name);
                        $messages_attach_file .= '<a style="text-decoration:underline;color:#0000FF;" href="message_file_download.php?file_id='.$file_info['name'].'">'.$messages_file_name[0].'</a>';
                        $messages_attach_file .= '&nbsp;';
                        $messages_attach_file .= '<a style="text-decoration:underline;color:#0000FF;" href="javascript:void(0)" onclick="remove_email_file(\''.$_GET['latest_messages_id'].'\',\''.$file_info['file_index'].'\')">X</a>&nbsp;&nbsp;&nbsp;';
                        $messages_attach_file .= '<input type="hidden" name="back_file_list[]" value="'.$file_info['name'].'">';
		}	
 	}
   }
   $messages_attach_file = '<div id="back_file_list" style="word-break:break-all">'.$messages_attach_file.'</div>';




	$messages_content_row_back_file[] = array('text'=> $messages_attach_file.'<div id="messages_file_back_boder"><input type="file" id="messages_file_back" name="messages_file_back[]"><a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="file_cancel(\'messages_file_back\')">'.DELETE_STAFF.'</a>&nbsp;&nbsp;<a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="add_email_file(\'messages_file_back\')">'.BUTTON_ADD_TEXT.'</a></div>');
	$messages_content_table[] = array('text'=> $messages_content_row_back_file);
 }
 $messages_content_row_type = array();
 $messages_content_row_type[] = array('text' => MESSAGES_TYPE);
 $messages_content_row_type[] = array('text' => '<input type="radio" name="messages_type" value="0"'.($_GET['messages_type'] == '0' || $_GET['messages_type'] == '' || !isset($_GET['messages_type']) ? ' checked' : '').'>'.MESSAGES_RADIO.'<input type="radio" name="messages_type" value="1"'.($_GET['messages_type'] == '1' ? ' checked' : '').'>'.EMAIL_RADIO);
 $messages_content_table[] = array('text'=> $messages_content_row_type);
if($_GET['latest_messages_id']>0){
 $messages_content_row_author = array();
 $messages_content_row_author[] = array('text'=> MESSAGES_AUTHOR.'&nbsp&nbsp'.$sql_message_content_res['sender_name']);
 $messages_content_row_author[] = array('text'=> MESSAGES_EDIT_DATE.'&nbsp&nbsp'.$sql_message_content_res['time']);
 $messages_content_table[] = array('text'=> $messages_content_row_author);
 $messages_content_row_author = array();
 $messages_content_row_author[] = array('text'=> TEXT_USER_UPDATE.'&nbsp&nbsp'.(tep_not_null($sql_message_content_res['user_update'])?$sql_message_content_res['user_update']:TEXT_UNSET_DATA));
 $messages_content_row_author[] = array('text'=> TEXT_DATE_UPDATE.'&nbsp&nbsp'.(tep_not_null($sql_message_content_res['date_update']) && $sql_message_content_res['date_update'] != '0000-00-00 00:00:00' && tep_not_null($sql_message_content_res['user_update'])?str_replace('-','/',$sql_message_content_res['date_update']):TEXT_UNSET_DATA));
 $messages_content_table[] = array('text'=> $messages_content_row_author);
}
 if($_GET['latest_messages_id']>0){
 	$is_back = '1';
 }else{
	$is_back = '0';
 }
 if($_GET['latest_messages_id']>0){
   $messages_status = $_GET['messages_sta'];
   switch($messages_status){

   case 'sent': 
     $messages_buttons = '<input type="submit" onclick="messages_check('.$is_back.',2)" value="'.MESSAGE_TRASH_SAVE.'"><input type="submit" onclick="messages_check('.$is_back.',1)" value="'.MESSAGE_DRAFTS_SAVE.'">';
     break;
   case 'drafts':
     $messages_buttons .= '<input type="button" onclick="messages_delete(\'delete\');" value="'.IMAGE_DELETE.'">';
     $messages_buttons .= '<input type="submit" onclick="messages_check('.$is_back.',4)" value="'.IMAGE_SAVE.'">';
     break;
   case 'trash':
     $messages_buttons .= '<input type="submit" onclick="messages_check('.$is_back.',3)" value="'.MESSAGE_RECOVERY.'">';
     $messages_buttons .= '<input type="button" onclick="messages_delete(\'delete\');" value="'.IMAGE_DELETE.'">';
     break;
   default:
     $messages_buttons = '<input type="submit" onclick="messages_check('.$is_back.',2)" value="'.MESSAGE_TRASH_SAVE.'"><input type="submit" onclick="messages_check('.$is_back.',1)" value="'.MESSAGE_DRAFTS_SAVE.'">';
     break;
   }
 }else{
   $messages_buttons = '<input type="submit" onclick="messages_check('.$is_back.',1)" value="'.MESSAGE_DRAFTS_SAVE.'">';
 }
 $messages_content_row_submit[] = array('params' => 'colspan="2" align="center"','text'=> '<input type="submit" onclick="messages_check('.$is_back.',0)" value="'.MESSAGES_SUBMIT.'">'.$messages_buttons);
 $messages_content_table[] = array('text'=> $messages_content_row_submit);
 $notice_box->get_heading($heading);
 $notice_box->get_form($form_str);
 $notice_box->get_contents($messages_content_table); 
 echo $notice_box->show_notice();

}else if($_GET['action'] == 'edit_meta_info'){
/* -----------------------------------------------------
    功能: meta的弹出框
    参数: $_GET['meta_e_id'] meta id 
 -----------------------------------------------------*/

  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CONFIGURATION_META);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 
  $meta_info_raw = tep_db_query("select cm.*, s.romaji from ".TABLE_CONFIGURATION_META." cm, ".TABLE_SITES." s  where s.id = cm.site_id and cm.id = '".$_POST['meta_e_id']."'");
  $meta_info_res = tep_db_fetch_array($meta_info_raw);
  
  $site_list_raw = tep_db_query("select `site_permission`, `permission` from `permissions` where `userid` = '".$ocertify->auth_user."'");
  while ($site_list_res = tep_db_fetch_array($site_list_raw)) {
    $site_permission_list = $site_list_res['site_permission']; 
  }
  if (isset($site_permission_list)) {
    $site_list_str = $site_permission_list;  
  } else {
    $site_list_str = '';  
  }
  $site_list_array = explode(',', $site_list_str); 
  if (!in_array($meta_info_res['site_id'], $site_list_array)) {
    $disabled_str = 'disabled="disabled"'; 
  }
  
  $param_str = '';
  $meta_array = array();

  foreach ($_POST as $p_key => $p_value) {
    if (($p_key != 'meta_e_id') && ($p_key != 'action')) {
      $param_str .= $p_key.'='.urlencode($p_value).'&'; 
    }
  }
  $param_str = substr($param_str, 0, -1); 
  
  $meta_order_sort_name = ' cm.site_id';
  $meta_order_sort = 'asc';
  if (isset($_POST['meta_sort'])) {
    switch ($_POST['meta_sort']) {
       case 'meta_site':
         $meta_order_sort_name = ' s.romaji';
         break;
       case 'meta_title':
         $meta_order_sort_name = ' cm.title';
         break;
       case 'meta_url':
         $meta_order_sort_name = ' cm.link_url';
         break;
       case 'meta_update':
          $meta_order_sort_name = ' cm.last_modified';
         break;
    }
  }
  if (isset($_POST['meta_sort_type'])) {
    if ($_POST['meta_sort_type'] == 'asc') {
      $meta_order_sort = 'asc';
    } else {
      $meta_order_sort = 'desc';
    }
  }
  $meta_order_sql = $meta_order_sort_name.' '.$meta_order_sort;

  if (isset($_POST['search'])) {
    $meta_query_raw = 'select cm.* from '.TABLE_CONFIGURATION_META.' cm, '.TABLE_SITES.' s where (title like \'%'.trim($_POST['search']).'%\' or meta_title like \'%'.$_POST['search'].'%\' or meta_keywords like \'%'.trim($_POST['search']).'%\' or meta_description like \'%'.trim($_POST['search']).'%\' or meta_copyright like \'%'.trim($_POST['search']).'%\') and cm.site_id = s.id and cm.site_id in ('.tep_get_setting_site_info(FILENAME_CONFIGURATION_META).') order by '.$meta_order_sql;
  } else {
    $meta_query_raw = 'select cm.* from '.TABLE_CONFIGURATION_META.' cm, '.TABLE_SITES.' s where cm.site_id = s.id and cm.site_id in ('.tep_get_setting_site_info(FILENAME_CONFIGURATION_META).') order by '.$meta_order_sql;
  }
  $meta_split = new splitPageResults($_POST['page'], MAX_DISPLAY_SEARCH_RESULTS, $meta_query_raw, $meta_query_numrows);
  $meta_list_query = tep_db_query($meta_query_raw);
  $meta_array = array(); 
  while ($meta_row = tep_db_fetch_array($meta_list_query)) {
    $meta_array[] = $meta_row['id'];
  }
  foreach ($meta_array as $m_key => $m_value) {
    if ($_POST['meta_e_id'] == $m_value) {
      break;
    }
  }

  if($m_key > 0){ 
    $page_str .= '<a onclick="show_link_meta_info(\''.$meta_array[$m_key - 1].'\', \''.urlencode($param_str).'\');" href="javascript:void(0);" id="meta_prev"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;';
  } else {
    $page_str .= '<font color="#000000">'.IMAGE_PREV.'</font>&nbsp;&nbsp;'; 
  }
  
  if($m_key < count($meta_array)-1){
    $page_str .= '<a onclick="show_link_meta_info(\''.$meta_array[$m_key + 1].'\', \''.urlencode($param_str).'\');" href="javascript:void(0);" id="meta_next">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;';
  } else {
    $page_str .= '<font color="#000000">'.IMAGE_NEXT.'</font>&nbsp;&nbsp;'; 
  }
  
  $page_str .= '<a onclick="close_meta_info();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $meta_info_res['title']); 
  $heading[] = array('align' => 'right', 'text' => $page_str); 
 
  $button = array();
  if (isset($disabled_str)) {
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(META_INFO_COPY_TEXT, $disabled_str.' id="button_save"').'</a>'; 
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, $disabled_str.' id="button_save"').'</a>'; 
  } else {
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(META_INFO_COPY_TEXT, $disabled_str.' onclick="copy_meta(\''.$_POST['meta_e_id'].'\', \''.urlencode($param_str).'\')"').'</a>'; 
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, $disabled_str.' onclick="submit_meta_form()" id="button_save"').'</a>'; 
  }
  $buttons = array('align' => 'center', 'button' => $button); 

  $meta_info_row = array();
  $meta_info_params = array('width' => '60%', 'cellpadding' => '0', 'cellspacing' => '0', 'border' => '0'); 
  $meta_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => TABLE_META_SITE_TEXT),
        array('align' => 'left', 'text' => $meta_info_res['romaji'])
      );
  
  $meta_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => META_INFO_TITLE_TEXT),
        array('align' => 'left', 'text' => tep_draw_input_field('meta_title', $meta_info_res['meta_title'], 'style="width:60%;" '.$disabled_str))
      );
  $meta_title_info_str = '';
  $meta_title_info_array = explode('<br>', $meta_info_res['meta_title_info']);
  if (!preg_match('#[A-Z_]+#', $meta_title_info_array[0])) {
    unset($meta_title_info_array[0]); 
  }
  if (!empty($meta_title_info_array)) {
    $mt_num = 1; 
    $meta_title_table_info_array = array();
    $calc_mt_num = 0; 
    foreach ($meta_title_info_array as $mt_key => $mt_value) {
      $meta_title_table_info_array[$calc_mt_num]['text'][] =  array('align' => 'left', 'text' => $mt_value); 
      if ($mt_num % 2 == 0) {
        $calc_mt_num++; 
      }
      $mt_num++; 
    }
    if (!empty($meta_title_table_info_array)) {
      if (count($meta_title_info_array) % 2 != 0) {
        $meta_title_table_info_array[$calc_mt_num]['text'][] =  array('align' => 'left', 'text' => ''); 
      }
    }
    $meta_title_info_str = $notice_box->get_table($meta_title_table_info_array, '', $meta_info_params);
 
    $meta_info_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => '&nbsp;'),
          array('align' => 'left', 'text' => $meta_title_info_str)
        );
  }
  
  $meta_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => META_INFO_KEYWORD_TEXT),
        array('align' => 'left', 'text' => tep_draw_textarea_field('meta_keywords', 'soft', '30', '10', $meta_info_res['meta_keywords'], 'style="width:60%; resize:vertical;"; onfocus="o_submit_single = false;" onblur="o_submit_single = true;" '.$disabled_str))
      );
  
  $meta_keywords_info_str = '';
  $meta_keywords_info_array = explode('<br>', $meta_info_res['meta_keywords_info']);
  if (!preg_match('#[A-Z_]+#', $meta_keywords_info_array[0])) {
    unset($meta_keywords_info_array[0]); 
  }
  if (!empty($meta_keywords_info_array)) {
    $mk_num = 1; 
    $meta_keywords_table_info_array = array();
    $calc_mk_num = 0; 
    foreach ($meta_keywords_info_array as $mk_key => $mk_value) {
      $meta_keywords_table_info_array[$calc_mk_num]['text'][] =  array('align' => 'left', 'text' => $mk_value); 
      if ($mk_num % 2 == 0) {
        $calc_mk_num++; 
      }
      $mk_num++; 
    }
    if (!empty($meta_keywords_table_info_array)) {
      if (count($meta_keywords_info_array) % 2 != 0) {
        $meta_keywords_table_info_array[$calc_mk_num]['text'][] =  array('align' => 'left', 'text' => ''); 
      }
    }
    $meta_keywords_info_str = $notice_box->get_table($meta_keywords_table_info_array, '', $meta_info_params);
    $meta_info_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => '&nbsp;'),
          array('align' => 'left', 'text' => $meta_keywords_info_str)
        );
  }
  
  $meta_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => META_INFO_DESCRIPTION_TEXT),
        array('align' => 'left', 'text' => tep_draw_textarea_field('meta_description', 'soft', '30', '10', $meta_info_res['meta_description'], 'style="width:60%; resize:vertical;"; onfocus="o_submit_single = false;" onblur="o_submit_single = true;" '.$disabled_str))
      );
  
  $meta_description_info_str = '';
  $meta_description_info_array = explode('<br>', $meta_info_res['meta_description_info']);
  if (!preg_match('#[A-Z_]+#', $meta_description_info_array[0])) {
    unset($meta_description_info_array[0]); 
  }
  if (!empty($meta_description_info_array)) {
    $md_num = 1; 
    $meta_description_table_info_array = array();
    $calc_md_num = 0; 
    foreach ($meta_description_info_array as $md_key => $md_value) {
      $meta_description_table_info_array[$calc_md_num]['text'][] =  array('align' => 'left', 'text' => $md_value); 
      if ($md_num % 2 == 0) {
        $calc_md_num++; 
      }
      $md_num++; 
    }
    if (!empty($meta_description_table_info_array)) {
      if (count($meta_description_info_array) % 2 != 0) {
        $meta_description_table_info_array[$calc_md_num]['text'][] =  array('align' => 'left', 'text' => ''); 
      }
    }
    $meta_description_info_str = $notice_box->get_table($meta_description_table_info_array, '', $meta_info_params);

    $meta_info_row[]['text'] = array(
          array('align' => 'left', 'params' => 'width="25%"', 'text' => '&nbsp;'),
          array('align' => 'left', 'text' => $meta_description_info_str)
        );
  }
  
  $meta_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => META_INFO_ROBOT_TEXT),
        array('align' => 'left', 'text' => tep_draw_radio_field('meta_robots', 'index,follow',(($meta_info_res['meta_robots'] == 'index,follow')?true:false), '', 'style="padding-left:0;margin-left:0;"').'index,follow&nbsp;'.tep_draw_radio_field('meta_robots', 'noindex',(($meta_info_res['meta_robots'] == 'noindex')?true:false)).'noindex')
      );
  $meta_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => META_INFO_COPYRIGHT_TEXT),
        array('align' => 'left', 'text' => tep_draw_input_field('meta_copyright', $meta_info_res['meta_copyright'], 'style="width:60%;" '.$disabled_str))
      );
  
  $meta_info_row[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($meta_info_res['user_added'])?$meta_info_res['user_added']:TEXT_UNSET_DATA)),
        array('align' => 'left', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($meta_info_res['date_added'])?$meta_info_res['date_added']:TEXT_UNSET_DATA))
      );
  $meta_info_row[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($meta_info_res['user_update'])?$meta_info_res['user_update']:TEXT_UNSET_DATA)),
        array('align' => 'left', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($meta_info_res['last_modified'])?$meta_info_res['last_modified']:TEXT_UNSET_DATA))
      );
  
  $form_str = tep_draw_form('meta_form', FILENAME_CONFIGURATION_META, 'meta_e_id='.$_POST['meta_e_id'].'&action=update_meta_info&'.$param_str);
  $notice_box->get_form($form_str); 
  $notice_box->get_heading($heading); 
  $notice_box->get_contents($meta_info_row, $buttons); 
  $notice_box->get_eof(tep_eof_hidden()); 
 
  echo $notice_box->show_notice().'||||||'.tep_get_note_top_layer(FILENAME_CONFIGURAION_META);
}else if($_GET['action'] == 'copy_meta_info'){
/* -----------------------------------------------------
    功能: meta的复制弹出框
    参数: $_GET['meta_e_id'] meta id 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CONFIGURATION_META);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  $meta_info_raw = tep_db_query("select cm.*, s.romaji from ".TABLE_CONFIGURATION_META." cm, ".TABLE_SITES." s  where s.id = cm.site_id and cm.id = '".$_POST['meta_e_id']."'");
  $meta_info_res = tep_db_fetch_array($meta_info_raw);
  
  $site_list_raw = tep_db_query("select `site_permission`, `permission` from `permissions` where `userid` = '".$ocertify->auth_user."'");
  while ($site_list_res = tep_db_fetch_array($site_list_raw)) {
    $site_permission_list = $site_list_res['site_permission']; 
  }
  if (isset($site_permission_list)) {
    $site_list_str = $site_permission_list;  
  } else {
    $site_list_str = '';  
  }
  $site_list_array = explode(',', $site_list_str); 
  if (!in_array($meta_info_res['site_id'], $site_list_array)) {
    $disabled_str = 'disabled="disabled"'; 
  }
  
  $page_str = '<a onclick="close_meta_info();" href="javascript:void(0);">X</a>';
  $meta_array = array();

  foreach ($_POST as $p_key => $p_value) {
    if (($p_key != 'meta_e_id') && ($p_key != 'action')) {
      $param_str .= $p_key.'='.urlencode($p_value).'&'; 
    }
  }
  $param_str = substr($param_str, 0, -1); 
  
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => sprintf(META_INFO_COPY_TITLE, $meta_info_res['title'])); 
  $heading[] = array('align' => 'right', 'text' => $page_str); 

  $button = array();
  if (isset($disabled_str)) {
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, $disabled_str.' id="button_save"').'</a>'; 
  } else {
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, $disabled_str.' onclick="check_copy_meta()" id="button_save"').'</a>'; 
  }
  $buttons = array('align' => 'center', 'button' => $button); 

  $meta_info_row = array();
  $site_info_str = ''; 
  $site_list_raw = tep_db_query("select * from ".TABLE_SITES." order by id asc"); 
  while ($site_list_res = tep_db_fetch_array($site_list_raw)) {
    if ($site_list_res['id'] == $meta_info_res['site_id']) {
      $site_info_str .= tep_draw_checkbox_field('select_site[]', $site_list_res['id'], false, '', 'disabled=disabled').$site_list_res['romaji'].'&nbsp;';   
    } else {
      if (isset($disabled_str)) {
        $site_info_str .= tep_draw_checkbox_field('select_site[]', $site_list_res['id'], false, '', 'disabled=disabled').$site_list_res['romaji'].'&nbsp;';   
      } else {
        $site_info_str .= tep_draw_checkbox_field('select_site[]', $site_list_res['id']).$site_list_res['romaji'].'&nbsp;';   
      }
    }
  }
  $meta_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="25%"', 'text' => META_INFO_COPY_SITE),
        array('align' => 'left', 'text' => $site_info_str.'<br><span id="site_error" style="color:#ff0000;"></span>')
      );

  $form_str = tep_draw_form('copy_meta', FILENAME_CONFIGURATION_META, 'meta_e_id='.$_POST['meta_e_id'].'&action=copy_meta_info&'.$param_str);
  $notice_box->get_form($form_str); 
  $notice_box->get_heading($heading); 
  $notice_box->get_contents($meta_info_row, $buttons); 
  $notice_box->get_eof(tep_eof_hidden()); 
 
  echo $notice_box->show_notice().'||||||'.tep_get_note_top_layer(FILENAME_CONFIGURAION_META);
}else if($_GET['action'] == 'show_customer_other_info'){
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CUSTOMERS);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
  $customers_info_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$_GET['customers_id']."'"); 
  $customers_info = tep_db_fetch_array($customers_info_raw); 
  
  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  if ($_GET['show_type'] == '1') {
    $heading[] = array('align' => 'left', 'text' => sprintf(CUSTOMERS_ORDER_TITLE, $customers_info['customers_lastname'].' '.$customers_info['customers_firstname']));
  } else {
    $heading[] = array('align' => 'left', 'text' => sprintf(CUSTOMERS_PREORDER_TITLE, $customers_info['customers_lastname'].' '.$customers_info['customers_firstname']));
  }
  $heading[] = array('align' => 'right', 'text' => $page_str);
 
  $customer_info_row = array();
  $buttons = array();
  $show_text_str = ''; 
  $show_list_str = ''; 
  if ($_GET['show_type'] == '1') {
    $show_text_str = CUSTOMERS_ORDER_ID_TEXT; 
    $order_list_raw = tep_db_query("select orders_id, customers_id from ".TABLE_ORDERS." where customers_id = '".$_GET['customers_id']."'");  
    while ($order_list = tep_db_fetch_array($order_list_raw)) {
      $show_list_str .= '<a href="'.tep_href_link(FILENAME_ORDERS, 'keywords='.$order_list['orders_id'].'&search_type=orders_id&oID='.$order_list['orders_id'].'&action=edit').'" target="_blank">'.$order_list['orders_id'].'</a><br>'; 
    }
  } else {
    $show_text_str = CUSTOMERS_PREORDER_ID_TEXT; 
    $preorder_list_raw = tep_db_query("select orders_id, customers_id from ".TABLE_PREORDERS." where customers_id = '".$_GET['customers_id']."'");  
    while ($preorder_list = tep_db_fetch_array($preorder_list_raw)) {
      $show_list_str .= '<a href="'.tep_href_link(FILENAME_PREORDERS, 'keywords='.$preorder_list['orders_id'].'&search_type=orders_id&oID='.$preorder_list['orders_id'].'&action=edit').'" target="_blank">'.$preorder_list['orders_id'].'</a><br>'; 
    }
  }
  $customer_info_row[]['text'] = array(
        array('align' => 'left','params' => 'width="30%"', 'text' => $show_text_str), 
        array('align' => 'left','params' => 'width="70%"','text' => $show_list_str), 
      );
  
  $notice_box->get_heading($heading);
   
  $notice_box->get_contents($customer_info_row, $buttons);
  echo $notice_box->show_notice();
 
}else if($_GET['action'] == 'show_group_info'){
 include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_GROUPS);
 include(DIR_FS_ADMIN.'classes/notice_box.php');
 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
 $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
 if($_POST['group_id'] < 0){
 	$heading[] = array('text' => GROUP_CREATE);
 	$form_str = tep_draw_form('new_latest_group', FILENAME_GROUPS,'action=new_group&id='.$_POST['group_id'].'&parent_id='.$_POST['parent_group_id'], 'post', 'enctype="multipart/form-data" onSubmit="return false;"');
 }else{
	$heading[] = array('text' => $_POST['group_name']);
 	$form_str = tep_draw_form('new_latest_group', FILENAME_GROUPS,'action=update_group&id='.$_POST['group_id'].'&parent_id='.$_POST['parent_group_id'], 'post', 'enctype="multipart/form-data" onSubmit="return false;"');
 }

//管理员的隐藏 
$manager_list = tep_db_query("select u.userid userid,u.name name from ".TABLE_USERS." u left join ".TABLE_PERMISSIONS." p on u.userid=p.userid where u.status=1 and p.permission<15 order by u.name asc"); 
 $all_manager_add ='<div id="add_manager_hidden" style="display:none;">';
	$all_manager_add .='<li><select class="manager_select" name="managers_list[]">';	
	while($manager_list_res = tep_db_fetch_array($manager_list)){
       $all_manager_add .='<option value="'.$manager_list_res['userid'].'">'.$manager_list_res['name'].'</option>';
	}
	$all_manager_add .='</select><input onclick="del_manager_row(this)" type="button" value="'.TEXT_DEL_ADL.'"></li>';
 $all_manager_add .='</div>';

 $heading[] = array('align' => 'right', 'text' => '<span id="next_prev"></span>&nbsp&nbsp'.$page_str.$all_manager_add);
 //读取groups数据
 $groups_query = tep_db_query("select * from ".TABLE_GROUPS." where id='".$_POST['group_id']."'");
 $groups_array = tep_db_fetch_array($groups_query);
 tep_db_free_result($groups_query);
 $group_content_table = array();
 $group_content_row_name = array();
 $group_content_row_name[] = array('params'=>'width="20%"','text'=> GROUP_NAME );
 $group_content_row_contents = array();
 $group_content_row_contents[] = array('params'=>'width="20%"','text'=> TEXT_GROUP_CONTENTS);
 if($_POST['group_id'] < 0){
 	$group_content_row_name[] = array('text' => '<input type="text" name="group_name" class="td_input" value=""><span id="group_name_error">'.TEXT_FIELD_REQUIRED.'</span>');
 	$group_content_row_contents[] = array('text' => '<textarea name="group_contents" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="resize: vertical;width:300px;height:42px;*height:40px;"></textarea>');
 }else{
	$group_content_row_name[] = array('text' => '<input type="text" name="group_name" class="td_input" value="'.$_POST['group_name'].'"><span id="group_name_error">'.TEXT_FIELD_REQUIRED.'</span>');
 	$group_content_row_contents[] = array('text' => '<textarea name="group_contents" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="resize: vertical;width:300px;height:42px;*height:40px;">'.html_entity_decode($groups_array['group_contents']).'</textarea>');
 }
 $group_content_table[] = array('text'=>$group_content_row_name); 
 $group_content_table[] = array('text'=>$group_content_row_contents); 
 


 //管理员
 $group_manager[] = array('params'=>'width="20%"','text'=> GROUP_MANAGERS );
 if($_POST['group_id'] < 0){
 	$manager_list = tep_db_query("select u.userid userid,u.name name from ".TABLE_USERS." u left join ".TABLE_PERMISSIONS." p on u.userid=p.userid where u.status=1 and p.permission<15 order by u.name asc"); 
   /*	$all_manager = '<ul class="table_img_list" style="width:100%">'; 
   	while ($manager_list_res = tep_db_fetch_array($manager_list)) {
 	$res_tep_row = tep_db_query("select permission from ".TABLE_PERMISSIONS." where userid='".$manager_list_res['userid']."'"); 
    $permission = tep_db_fetch_array($res_tep_row);
	$hidden =  $permission['permission']>10 ? 'style="display:none"':'';
        $all_manager .= '<li '.$hidden.'><input type="checkbox" name="managers_list[]" value="'.$manager_list_res['userid'].'" style="padding-left:0;margin-left:0;" id="managers_id_'.$manager_list_res['userid'].'"><label for="managers_id_'.$manager_list_res['userid'].'">'.$manager_list_res['name'].'</label></li>'; 
   	}
	$all_manager .= '</ul>';*/

   	$all_manager = '<ul id="add_manager_list" class="table_img_list" style="width:100%">'; 
	$all_manager .='<li><select class="manager_select" name="managers_list[]">';	
	while($manager_list_res = tep_db_fetch_array($manager_list)){
       $all_manager .='<option value="'.$manager_list_res['userid'].'">'.$manager_list_res['name'].'</option>';
	}
	$all_manager .='</select><input type="button" onclick="add_manager_row();" value="'.TEXT_ADD_ADL.'"></li>';
   	$all_manager .= '</ul>'; 
	
 }else{
     $group_all_manager = tep_db_fetch_array(tep_db_query('select all_managers_id from '.TABLE_GROUPS.' where id = "'.$_POST['group_id'].'"'));
	 $group_all_manager = explode('|||',$group_all_manager['all_managers_id']);
	 $manager_list = tep_db_query("select u.userid userid,u.name name from ".TABLE_USERS." u left join ".TABLE_PERMISSIONS." p on u.userid=p.userid where u.status=1 and p.permission<15 order by u.name asc");
/*	 $all_manager = '<ul class="table_img_list" style="width:100%">'; 
   	 while ($manager_list_res = tep_db_fetch_array($manager_list)) {
 	     $res_tep_row = tep_db_query("select permission from ".TABLE_PERMISSIONS." where userid='".$manager_list_res['userid']."'"); 
         $permission = tep_db_fetch_array($res_tep_row);
		 $hidden =  $permission['permission']>10 ? 'style="display:none"':'';
     		$all_manager .= '<li '.$hidden.'><input type="checkbox" name="managers_list[]" value="'.$manager_list_res['userid'].'" style="padding-left:0;margin-left:0;" id="managers_id_'.$manager_list_res['userid'].'"'.(in_array($manager_list_res['userid'],$group_all_manager) ? ' checked="checked"' : '').'><label for="managers_id_'.$manager_list_res['userid'].'">'.$manager_list_res['name'].'</label></li>'; 
   	}
 	$all_manager .= '</ul>';
 */
    $manager_list_res = array();
	 while($row = tep_db_fetch_array($manager_list)){
		 $manager_list_res[]=$row;
	 }
   	$all_manager = '<ul id="add_manager_list" class="table_img_list" style="width:100%">'; 
	for($i=0; $i<count($group_all_manager); $i++){
	  $all_manager .='<li><select class="manager_select" name="managers_list[]">';	
	  foreach($manager_list_res as $manager_info){
        $all_manager .='<option value="'.$manager_info['userid'].'"'.($manager_info['userid']==$group_all_manager[$i] ? ' selected="selected"' : '').'>'.$manager_info['name'].'</option>';
	 }
	    $all_manager .='</select>'.($i == 0 ? '<input type="button" onclick="add_manager_row();" value="'.TEXT_ADD_ADL.'">' : '').'<input '.($i==0 ? 'style="display:none;"':'').' onclick="del_manager_row(this)" type="button" value="'.TEXT_DEL_ADL.'"></li>';
	}
   	$all_manager .= '</ul>'; 

 }
 $group_manager[] = array('text' => $all_manager);

 $group_content_table[] = array('text'=>$group_manager);

 //工资计算管理
 $group_content_row_staff = array();
 $group_content_row_staff[] = array('params'=>'width="20%"','text'=> TEXT_GROUP_PAYROLL_ADMIN);
 if($_POST['group_id'] < 0){
        $users_list = tep_db_query("select u.userid userid,u.name name from ".TABLE_USERS." u left join ".TABLE_PERMISSIONS." p on u.userid=p.userid where u.status=1 and p.permission!=31 order by u.name asc"); 
        $all_users .= '<ul id="add_payroll_list" class="table_img_list" style="width:100%">'; 
   	$all_users .= '<li><select name="payroll_admin[]">'; 
   	$all_users .= '<option vlaue="">--</option>'; 
   	while ($users_list_res = tep_db_fetch_array($users_list)) {
     		$all_users .= '<option value="'.$users_list_res['userid'].'">'.$users_list_res['name'].'</option>'; 
        }
        tep_db_free_result($users_list);
 	$all_users .= '</select>';
 	$all_users .= '<input type="button" id="add_admin_button" value="'.BUTTON_ADD_TEXT.'" onclick="add_admin_list(this,\''.IMAGE_DELETE.'\');"></li></ul>';
 }else{
        $payrolls_admin = $groups_array['payrolls_admin'];

        if(trim($payrolls_admin) == ''){
          $users_list = tep_db_query("select u.userid userid,u.name name from ".TABLE_USERS." u left join ".TABLE_PERMISSIONS." p on u.userid=p.userid where u.status=1 and p.permission!=31 order by u.name asc"); 
          $all_users = '<ul id="add_payroll_list" class="table_img_list" style="width:100%">';
   	  $all_users .= '<li><select name="payroll_admin[]">'; 
   	  $all_users .= '<option vlaue="">--</option>'; 
   	  while ($users_list_res = tep_db_fetch_array($users_list)) {
     		$all_users .= '<option value="'.$users_list_res['userid'].'">'.$users_list_res['name'].'</option>'; 
          }
          tep_db_free_result($users_list);
 	  $all_users .= '</select>';
          $all_users .= '<input type="button" id="add_admin_button" value="'.BUTTON_ADD_TEXT.'" onclick="add_admin_list(this,\''.IMAGE_DELETE.'\');"></li>';
          $all_users .= '</ul>';
        }else{

          $payrolls_admin_array = explode('|||',$payrolls_admin);
          $admin_i = 0;
          $all_users = '<ul id="add_payroll_list" class="table_img_list" style="width:100%">';
          foreach($payrolls_admin_array as $payrolls_admin_value){
            
            $users_list = tep_db_query("select u.userid userid,u.name name from ".TABLE_USERS." u left join ".TABLE_PERMISSIONS." p on u.userid=p.userid where u.status=1 and p.permission!=31 order by u.name asc"); 
   	    $all_users .= '<li><select name="payroll_admin[]">'; 
   	    $all_users .= '<option vlaue="">--</option>'; 
   	    while ($users_list_res = tep_db_fetch_array($users_list)) {
     		$all_users .= '<option value="'.$users_list_res['userid'].'"'.($users_list_res['userid'] == $payrolls_admin_value ? ' selected' : '').'>'.$users_list_res['name'].'</option>'; 
            }
            tep_db_free_result($users_list);
 	    $all_users .= '</select>';
            $all_users .= ($admin_i == 0 ? '<input type="button" id="add_admin_button" value="'.BUTTON_ADD_TEXT.'" onclick="add_admin_list(this,\''.IMAGE_DELETE.'\');"></li>' : '<input type="button" onclick="del_admin_list(this);" value="'.IMAGE_DELETE.'"></li>'); 
             
            $admin_i++;
          }
          $all_users .= '</ul>';
        }
 }
 $group_content_row_staff[] = array('text' => $all_users);
 $group_content_table[] = array('text'=>$group_content_row_staff);
 //用户
 $group_content_row_staff = array();
 $group_content_row_staff[] = array('params'=>'width="20%"','text'=> GROUP_STAFF );
 if($_POST['group_id'] < 0){
 	$users_list = tep_db_query("select * from ".TABLE_USERS." where status=1 order by name asc"); 
   	$all_users = '<ul class="table_img_list" style="width:100%">'; 
   	while ($users_list_res = tep_db_fetch_array($users_list)) {
     		$all_users .= '<li class="table_img_user"><input type="checkbox" name="users_list[]" value="'.$users_list_res['userid'].'" style="padding-left:0;margin-left:0;" id="users_id_'.$users_list_res['userid'].'"><label for="users_id_'.$users_list_res['userid'].'">'.$users_list_res['name'].'</label></li>'; 
   	}
 	$all_users .= '</ul>';
 }else{
	$group_all_user = tep_db_fetch_array(tep_db_query('select all_users_id from '.TABLE_GROUPS.' where id = "'.$_POST['group_id'].'"'));
	$group_all_user = explode('|||',$group_all_user['all_users_id']);
	$users_list = tep_db_query('select * from '.TABLE_USERS.' where status=1 order by name asc');
	$all_users = '<ul class="table_img_list" style="width:100%">'; 
   	while ($users_list_res = tep_db_fetch_array($users_list)) {
     		$all_users .= '<li class="table_img_user"><input type="checkbox" name="users_list[]" value="'.$users_list_res['userid'].'" style="padding-left:0;margin-left:0;" id="users_id_'.$users_list_res['userid'].'"'.(in_array($users_list_res['userid'],$group_all_user) ? ' checked="checked"' : '').'><label for="users_id_'.$users_list_res['userid'].'">'.$users_list_res['name'].'</label></li>'; 
   	}
 	$all_users .= '</ul>';
 }
 $group_content_row_staff[] = array('text' => $all_users);
 $group_content_table[] = array('params'=>'id="add_end"','text'=>$group_content_row_staff); 
 //货币类型
 $group_content_row_currency = array();
 $group_content_row_currency[] = array('params'=>'width="20%"','text'=> TEXT_GROUP_CURRENCY_TYPE);
 $currency_str = '<select name="currency_type">';
 $currency_str .= '<option value="0"'.($groups_array['currency_type'] == 0 ? ' selected' : '').'>'.TEXT_GROUP_CURRENCY_TYPE_JPY.'</option>';
 $currency_str .= '<option value="1"'.($groups_array['currency_type'] == 1 ? ' selected' : '').'>'.TEXT_GROUP_CURRENCY_TYPE_RMB.'</option>';
 $currency_str .= '<option value="2"'.($groups_array['currency_type'] == 2 ? ' selected' : '').'>'.TEXT_GROUP_CURRENCY_TYPE_USD.'</option>';
 $currency_str .= '<option value="3"'.($groups_array['currency_type'] == 3 ? ' selected' : '').'>'.TEXT_GROUP_CURRENCY_TYPE_VND.'</option>';
 $currency_str .= '</select>';
 $group_content_row_currency[] = array('text' => $currency_str);
 $group_content_table[] = array('text'=>$group_content_row_currency);
 //终始日 
 if($_POST['group_id'] < 0){
   $date_i = 1;
   $group_content_row_date = array();
   $group_content_row_date[] = array('params'=>'width="20%"','text'=> TEXT_GROUP_BEGIN_END_DATE.'<input type="hidden" id="date_num_id" value="0">'); 
   $date_str = '<input type="radio" name="cycle_flag" id="cycle_flag_false" value="0" checked style="padding-left:0;margin-left:0;">'.TEXT_GROUP_BEGIN_DATE; 
   $date_str .= '<select name="start_date[]" onchange="start_date_select(this,0);">';
   $date_str .= '<option value="0">--</option>';
   for($i=1;$i<=28;$i++){

     $date_str .= '<option value="'.$i.'">'.($i == 28 ? '28~31' : $i).TEXT_GROUP_DAY.'</option>'; 
   }
   $date_str .= '</select>';
   $date_str .= TEXT_GROUP_END_DATE; 
   $date_str .= '<select name="end_date[]" onchange="date_select(this,\''.TEXT_GROUP_END_DATE.'\',\''.TEXT_GROUP_BEGIN_DATE.'\',\''.TEXT_GROUP_DAY.'\');" disabled>';
   for($i=1;$i<=28;$i++){

     $date_str .= '<option value="'.$i.'"'.($i == 28 ? ' selected' : '').'>'.($i == 28 ? '28~31' : $i).TEXT_GROUP_DAY.'</option>'; 
   }
   $date_str .= '</select>';
   $date_str .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick="check_reset();" value="'.TEXT_GROUP_RESET.'">'; 
   $group_content_row_date[] = array('text' => $date_str);
   $group_content_table[] = array('params'=>'id="tr_date_0"','text'=>$group_content_row_date);

   $group_content_row_date = array();
   $group_content_row_date[] = array('params'=>'width="20%"','text'=> '');
   $date_str = '';
   $date_str .= '<div class="yui3-skin-sam yui3-g"><input type="radio" name="cycle_flag" id="cycle_flag_true" value="1" style="padding-left:0;margin-left:0;">'.tep_draw_input_field('payroll_date',date('Y-m-d',time()),'size="8" class="readonly" disabled').'<input id="date_orders" type="hidden" name="select_date" size="10" value="'.date('Y-m-d',time()).'">
                <div class="date_box">
                <a href="javascript:void(0);" onclick="open_new_calendar();" class="dpicker"></a> 
                </div>
                <input type="hidden" id="date_order" name="update_date" value="">
                <input type="hidden" name="toggle_open" value="0" id="toggle_open"> 
                <div class="yui3-u" id="new_yui3">
                <div id="mycalendar"></div> 
                </div>
                </div>';
   $date_str .= TEXT_TIME_LINK.'<input type="text" name="cycle_date" size="5">'.TEXT_GROUP_DAYS.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label for="cycle_flag_true">'.TEXT_GROUP_LOOP.'</label>';
   $group_content_row_date[] = array('text' => $date_str);
   $group_content_table[] = array('text'=>$group_content_row_date);
 }else{
   $groups_start_end_date = $groups_array['begin_end_date'];
   $groups_start_end_date_array = explode('|||',$groups_start_end_date);

  if($groups_array['cycle_flag'] == 0){
   $date_i = 0;
   foreach($groups_start_end_date_array as $start_end_date_key=>$start_end_date_value){

     $group_content_row_date = array();
     $group_content_row_date[] = array('params'=>'width="20%"','text'=> $start_end_date_key == 0 ? TEXT_GROUP_BEGIN_END_DATE : '');
     $start_end_date = explode('-',$start_end_date_value); 
     if($start_end_date_key == 0){
       $date_str = '<input type="radio" name="cycle_flag" id="cycle_flag_false" value="0" checked style="padding-left:0;margin-left:0;">'.TEXT_GROUP_BEGIN_DATE; 
     }else{
       $date_str = TEXT_GROUP_BEGIN_DATE; 
     }
     $date_str .= '<select name="start_date[]" onchange="start_date_select(this,0);" disabled>';
     $date_str .= '<option value="0">--</option>';
     for($i=1;$i<=28;$i++){

       $date_str .= '<option value="'.$i.'"'.($start_end_date[1] == $i ? ' selected' : '').'>'.($i == 28 ? '28~31' : $i).TEXT_GROUP_DAY.'</option>'; 
     }
     $date_str .= '</select>';
     $date_str .= TEXT_GROUP_END_DATE; 
     $date_str .= '<select name="end_date[]" onchange="date_select(this,\''.TEXT_GROUP_END_DATE.'\',\''.TEXT_GROUP_BEGIN_DATE.'\',\''.TEXT_GROUP_DAY.'\');" disabled>';
     for($i=1;$i<=28;$i++){

       $date_str .= '<option value="'.$i.'"'.($start_end_date[0] == $i ? ' selected' : '').'>'.($i == 28 ? '28~31' : $i).TEXT_GROUP_DAY.'</option>'; 
     }
     $date_str .= '</select>';
     if($start_end_date_key == 0){
       $date_str .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick="check_reset();" value="'.TEXT_GROUP_RESET.'">';
     }
     $group_content_row_date[] = array('text' => $date_str);
     $group_content_table[] = array('params'=>'id="tr_date_'.$date_i.'"','text'=>$group_content_row_date);
     $date_i++;
   } 
  }else{
   $date_i = 1;
   $group_content_row_date = array();
   $group_content_row_date[] = array('params'=>'width="20%"','text'=> TEXT_GROUP_BEGIN_END_DATE.'<input type="hidden" id="date_num_id" value="0">'); 
   $date_str = '<input type="radio" name="cycle_flag" id="cycle_flag_false" value="0" checked style="padding-left:0;margin-left:0;">'.TEXT_GROUP_BEGIN_DATE; 
   $date_str .= '<select name="start_date[]" onchange="start_date_select(this,0);">';
   $date_str .= '<option value="0">--</option>';
   for($i=1;$i<=28;$i++){

     $date_str .= '<option value="'.$i.'">'.($i == 28 ? '28~31' : $i).TEXT_GROUP_DAY.'</option>'; 
   }
   $date_str .= '</select>';
   $date_str .= TEXT_GROUP_END_DATE; 
   $date_str .= '<select name="end_date[]" onchange="date_select(this,\''.TEXT_GROUP_END_DATE.'\',\''.TEXT_GROUP_BEGIN_DATE.'\',\''.TEXT_GROUP_DAY.'\');" disabled>';
   for($i=1;$i<=28;$i++){

     $date_str .= '<option value="'.$i.'"'.($i == 28 ? ' selected' : '').'>'.($i == 28 ? '28~31' : $i).TEXT_GROUP_DAY.'</option>'; 
   }
   $date_str .= '</select>';
   $date_str .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick="check_reset();" value="'.TEXT_GROUP_RESET.'">'; 
   $group_content_row_date[] = array('text' => $date_str);
   $group_content_table[] = array('params'=>'id="tr_date_0"','text'=>$group_content_row_date);  
  
  }

   $group_content_row_date = array();
   $group_content_row_date[] = array('params'=>'width="20%"','text'=> '');
   $date_str = '';
   $date_str .= '<div class="yui3-skin-sam yui3-g"><input type="radio" name="cycle_flag" id="cycle_flag_true" value="1" style="padding-left:0;margin-left:0;"'.($groups_array['cycle_flag'] == 1 ? ' checked' : '').'>'.tep_draw_input_field('payroll_date',($groups_array['cycle_flag'] == 1 ? $groups_start_end_date_array[0] : date('Y-m-d',time())),'size="8" class="readonly" disabled').'<input id="date_orders" type="hidden" name="select_date" size="10" value="'.($groups_array['cycle_flag'] == 1 ? $groups_start_end_date_array[0] : date('Y-m-d',time())).'">
                <div class="date_box">
                <a href="javascript:void(0);" onclick="open_new_calendar();" class="dpicker"></a> 
                </div>
                <input type="hidden" id="date_order" name="update_date" value="">
                <input type="hidden" name="toggle_open" value="0" id="toggle_open"> 
                <div class="yui3-u" id="new_yui3">
                <div id="mycalendar"></div> 
                </div>
                </div>';
   $date_str .= TEXT_TIME_LINK.'<input type="text" name="cycle_date" size="5" value="'.($groups_array['cycle_flag'] == 1 ? $groups_start_end_date_array[1] : '').'">'.TEXT_GROUP_DAYS.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label for="cycle_flag_true">'.TEXT_GROUP_LOOP.'</label>';
   $group_content_row_date[] = array('text' => $date_str);
   $group_content_table[] = array('text'=>$group_content_row_date);
 } 
 //終始時
 $group_content_row_hour = array();
 $group_content_row_hour[] = array('params'=>'width="20%"','text'=> TEXT_GROUP_BEGIN_END_HOUR.'<input type="hidden" id="tr_date_id" value="'.$date_i.'">');
 $hour_str = '<select name="start_end_hour">';
 for($i=0;$i<=23;$i++){

   $hour_str .= '<option value="'.$i.'"'.($groups_array['begin_end_hour'] == $i ? ' selected' : '').'>'.$i.TEXT_GROUP_HOUR.'</option>'; 
 }
 $hour_str .= '</select>';
 $group_content_row_hour[] = array('text' => $hour_str);
 $group_content_table[] = array('text'=>$group_content_row_hour); 
 //工资计算
 $payroll_query = tep_db_query("select * from ".TABLE_PAYROLL_SETTLEMENT." where group_id='".$_POST['group_id']."' order by sort"); 
 $group_content_row_payroll = array();
 $group_content_row_payroll[] = array('params'=>'width="20%"','text'=> TEXT_GROUP_PAYROLL_OBJECT);
 $payroll_str = '<select id="payroll_select">';
 $payroll_str .= '<option value="0">'.TEXT_GROUP_PAYROLL_OBJECT_VALUE.'</option>'; 
 $payroll_str .= '<option value="1">'.TEXT_GROUP_PAYROLL_OBJECT_FORMULA.'</option>'; 
 $payroll_str .= '</select>';
 $payroll_str .= '&nbsp;&nbsp;<input type="button" value="'.BUTTON_ADD_TEXT.'" onclick="add_obj(this,\''.TEXT_GROUP_PAYROLL_OBJECT_VALUE.'\',\''.TEXT_GROUP_PAYROLL_OBJECT_FORMULA.'\',\''.IMAGE_DELETE.'\');">';
 $group_content_row_payroll[] = array('text' => $payroll_str.'<input type="hidden" id="obj_num" value="'.($_POST['group_id'] < 0 ? '0' : tep_db_num_rows($payroll_query)).'">');
 $group_content_table[] = array('text'=>$group_content_row_payroll);
 //计算工资的项目
 if(tep_db_num_rows($payroll_query) > 0){
   $i = 1;
   //已有的项目ID
   $old_project_id_array = array();
   $old_formula_id_array = array();
   $text_array = array();
   while($payroll_array = tep_db_fetch_array($payroll_query)){

     $group_content_row_payroll = array();
     $group_content_row_payroll[] = array('params'=>'width="20%"','text'=> '<input type="hidden" value="'.$payroll_array['id'].'" name="payroll_sort[]">&nbsp;&nbsp;&nbsp;&nbsp;'.($payroll_array['project_id'] == 0 ? TEXT_GROUP_PAYROLL_OBJECT_VALUE : TEXT_GROUP_PAYROLL_OBJECT_FORMULA)); 
     if($payroll_array['project_id'] == 0){
       $old_project_id_array[] = $payroll_array['id'];
       $group_content_row_payroll[] = array('text' => '<input type="text" style="width: 145px;" value="'.$payroll_array['title'].'" name="old_object_title['.$payroll_array['id'].']"><input type="text" style="width: 150px;" value="'.$payroll_array['contents'].'" name="old_object_contents['.$payroll_array['id'].']"><input type="button" onclick="delete_obj('.$i.');" value="'.IMAGE_DELETE.'">&nbsp;&nbsp;<input type="button" value="'.BUTTON_ADD_TEXT.'" onclick="add_obj(this,\''.TEXT_GROUP_PAYROLL_OBJECT_VALUE.'\',\''.TEXT_GROUP_PAYROLL_OBJECT_FORMULA.'\',\''.IMAGE_DELETE.'\',1);"><br><textarea style="overflow-y: hidden; width: 299px; height: 15px;" name="old_object_value['.$payroll_array['id'].']" id="textarea_'.$i.'">'.$payroll_array['project_value'].'</textarea>');
     }else{
       $old_formula_id_array[] = $payroll_array['id'];
       $group_content_row_payroll[] = array('text' => '<input type="text" value="'.$payroll_array['title'].'" style="width: 145px;" name="old_formula_title['.$payroll_array['id'].']"><input type="text" value="'.$payroll_array['contents'].'" style="width: 150px;" name="old_formula_contents['.$payroll_array['id'].']"><input type="button" onclick="delete_obj('.$i.');" value="'.IMAGE_DELETE.'">&nbsp;&nbsp;<input type="button" value="'.BUTTON_ADD_TEXT.'" onclick="add_obj(this,\''.TEXT_GROUP_PAYROLL_OBJECT_VALUE.'\',\''.TEXT_GROUP_PAYROLL_OBJECT_FORMULA.'\',\''.IMAGE_DELETE.'\',1);"><br><textarea style="overflow-y: hidden; width: 299px; height: 15px;" name="old_formula_value['.$payroll_array['id'].']" id="textarea_'.$i.'">'.$payroll_array['project_value'].'</textarea>');
       $text_array[] = $i;
     }
     $group_content_table[] = array('params'=>'id="obj_tr_'.$i.'"','text'=>$group_content_row_payroll);
     $i++;
   }
 }
 tep_db_free_result($payroll_query); 
 //排序
 $group_content_row_order = array();
 $group_content_row_order[] = array('params'=>'width="20%"','text'=> TEXT_GROUP_ORDER_SORT.'<input type="hidden" id="text_num" value="'.implode(',',$text_array).'"><input type="hidden" name="old_project_str" value="'.implode(',',$old_project_id_array).'"><input type="hidden" name="old_formula_str" value="'.implode(',',$old_formula_id_array).'">');
 $group_content_row_order[] = array('text' => '<input type="text" style="text-align:right;width:20%;" size="31" value="'.(isset($groups_array['order_sort']) ? $groups_array['order_sort'] : '1000').'" name="order_sort">');
 $group_content_table[] = array('params'=>'id="end_tr"','text'=>$group_content_row_order);

 if($_POST['group_id'] > 0){
	$group_content_row_subgroup = array();
	$group_content_row_subgroup[] = array('params'=>'width="20%"','text'=> GROUP_SUB);
	$sub_group_list = array();
	$sql_group_id = $_POST['group_id'];
	function sub_group_method($sql_group_id,&$sub_group_list){
		$sub_group_sql = tep_db_query('select * from '.TABLE_GROUPS.' where parent_id = "'.$sql_group_id.'"');
		while($sub_group_res = tep_db_fetch_array($sub_group_sql)){
			$sub_group_list[] = $sub_group_res;
			sub_group_method($sub_group_res['id'],&$sub_group_list);
		}
	}
        sub_group_method($sql_group_id,$sub_group_list);
        /*
	$all_sub_group = '<ul class="table_img_list" style="width:100%">';
	foreach($sub_group_list as $value){
		$all_sub_group .= '<li><input type="checkbox" name="sub_group_list[]" value="'.$value['id'].'" style="padding-left:0;margin-left:0;">'.$value['name'].'</li>';
	}
        $all_sub_group .= '</ul>';
         */
	$group_content_row_subgroup[] = array('text'=> count($sub_group_list));
	$group_content_table[] = array('text'=>$group_content_row_subgroup);
 }
 if($_POST['group_id'] > 0){
   //作成者、作成时间、更新者、更新时间
   $group_content_table[]['text'] = array(
        array('align' => 'left','params' => 'width="30%"', 'text' => TEXT_USER_ADDED.'&nbsp;'.((tep_not_null($groups_array['create_user'])?$groups_array['create_user']:TEXT_UNSET_DATA))), 
        array('align' => 'left','params' => 'width="70%"','text' => TEXT_DATE_ADDED.'&nbsp;'.((tep_not_null(tep_datetime_short($groups_array['create_time'])))?tep_datetime_short($groups_array['create_time']):TEXT_UNSET_DATA)), 
      );
  
   $group_content_table[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_USER_UPDATE.'&nbsp;'.((tep_not_null($groups_array['update_user'])?$groups_array['update_user']:TEXT_UNSET_DATA))), 
        array('align' => 'left', 'text' => TEXT_DATE_UPDATE.'&nbsp;'.((tep_not_null(tep_datetime_short($groups_array['update_time']))) && tep_not_null($groups_array['update_user'])?tep_datetime_short($groups_array['update_time']):TEXT_UNSET_DATA)), 
      ); 
 }
 $group_content_row_opt = array();
 $move_copy_str = '';
 if($_POST['group_id'] > 0 && $ocertify->npermission >= 10){
   $move_copy_str = '<input class="element_button" type="button" value="'.IMAGE_MOVE.'" onclick="move_group_id('.$groups_array['id'].');">&nbsp;&nbsp;<input class="element_button" type="button" value="'.IMAGE_COPY.'" onclick="copy_group_id('.$groups_array['id'].')">&nbsp;&nbsp;';
 }
 $group_content_row_opt[] = array('params'=>'align="center" colspan="2"','text'=> ($_POST['group_id'] < 0 && $ocertify->npermission >= 15 ? '' : '<input class="element_button" type="button" value="'.IMAGE_DELETE.'" onclick="delete_group('.$_POST['group_id'].','.$_POST['parent_group_id'].');">').'&nbsp;&nbsp;'.$move_copy_str.'<input class="element_button" type="submit" onclick="check_group();" value="'.GROUP_SAVE.'">');
 $group_content_table[] = array('text'=>$group_content_row_opt);
 $notice_box->get_heading($heading);
 $notice_box->get_form($form_str);
 $notice_box->get_contents($group_content_table);
 echo $notice_box->show_notice();
}else if($_GET['action'] == 'valadate_user_email'){
 include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.'messages.php');
  $select_arr =  json_decode(stripslashes($_POST['select_json']));
  $error_user = '';
  if($_GET['type'] == 'user'){
    foreach($select_arr as $value){
      $user_arr = explode('|||',$value);
      $user_info = tep_get_user_info($user_arr[0]);
      if($user_info['email'] == ''){
        $error_user .= $user_info['name'].",";
      }
    }
  }else{
      foreach($select_arr as $groups_value){
       $groups_query = tep_db_query("select id from ".TABLE_GROUPS." where parent_id='".$groups_value."'");
       if(tep_db_num_rows($groups_query) == 0){

         $users_query = tep_db_query("select id,all_users_id from ".TABLE_GROUPS." where id='".$groups_value."'");
         $users_array = tep_db_fetch_array($users_query);

         if(trim($users_array['all_users_id']) != ''){
           $users_id_temp = explode('|||',$users_array['all_users_id']);
         }else{
           //如果此组包含用户为空，取上一级组的用户，以此类推 
           group_users_id_list($users_array['id'],$users_id_list);
           $users_id_temp = $users_id_list;
         }
         foreach($users_id_temp as $temp_value){
           $users_id_array[] = $temp_value;
         }
         tep_db_free_result($users_query);
       }
       tep_db_free_result($groups_query);
     }
     $users_id_array = array_unique($users_id_array);

     $users_id_str = implode("','",$users_id_array);
     $users_list_array = array();
     $users_name_query = tep_db_query("select userid,name,email from ".TABLE_USERS." where userid in ('".$users_id_str."')");
     while($users_name_array = tep_db_fetch_array($users_name_query)){
      if($users_name_array['email'] == ''){
        $error_user .= $users_name_array['name'].",";
      }
     }
  }
  if($error_user!=''){
    $error_user = substr($error_user,0,-1);
    $error_user .= " ".TEXT_USER_NO_EMAIL;
    $error_user .= "\n".TEXT_SEND_MAIL;
  }
  echo $error_user;

}else if($_GET['action'] == 'del_messages_file'){
   $sql = 'select * from message_file where message_id = "'.$_POST['latest_messages_id'].'" and file_index="'.$_POST['f_index'].'"';
   $query = tep_db_query($sql);
   $res = tep_db_fetch_array($query);
   $sql_del = 'select * from message_file where name="'.$res['name'].'"'; 
   $query_del = tep_db_query($sql_del);
   if(tep_db_num_rows($query_del) == 1){
     if($res['name']!=''&&file_exists('messages_upload/'.$res['name'])){
       unlink('messages_upload/'.$res['name']);
     }
   }
   $del_sql = 'delete from message_file where message_id = "'.$_POST['latest_messages_id'].'" and file_index="'.$_POST['f_index'].'"';
   tep_db_query($del_sql);

   $sql_message_content = tep_db_query('select * from messages where id = "'.$_POST['latest_messages_id'].'"');
   $sql_message_content_res = tep_db_fetch_array($sql_message_content);
   $messages_attach_file = '';
   $file_list_arr = tep_get_messages_file($_POST['latest_messages_id']);
   if(count($file_list_arr) == 0){

     tep_db_query("update messages set attach_file='0' where id='".$_POST['latest_messages_id']."'");
   }
   foreach($file_list_arr as $f_index => $file_info){
	if($sql_message_content_res['attach_file'] == 1){
		$messages_file_name = $file_info['name'];
                if(file_exists('messages_upload/'.$messages_file_name)){
                        $messages_file_name = str_replace('*','/',$messages_file_name);
			$messages_file_name = base64_decode($messages_file_name);
			$messages_file_name = explode('|||',$messages_file_name);
                        $messages_attach_file .= '<a style="text-decoration:underline;color:#0000FF;" href="message_file_download.php?file_id='.$file_info['name'].'">'.$messages_file_name[0].'</a>';
                        $messages_attach_file .= '&nbsp;';
                        $messages_attach_file .= '<a style="text-decoration:underline;color:#0000FF;" href="javascript:void(0)" onclick="remove_email_file(\''.$_POST['latest_messages_id'].'\',\''.$file_info['file_index'].'\')">X</a>&nbsp;&nbsp;&nbsp;';
                        $messages_attach_file .= '<input type="hidden" name="back_file_list[]" value="'.$file_info['name'].'">';
		}	
 	}
   }
   if($messages_attach_file != ''){
     echo $messages_attach_file; 
   }else{
     echo '';
   }
}else if($_GET['action'] == 'change_attendance_login'){
/**
 * uid 用户的id
 * 添加,更新出勤和退勤时间
*/

    $uid = $_POST['user_name'];
    $tep_res = tep_change_attendance_login($uid); 
    if($tep_res){
        echo $tep_res; 
    } 
}else if($_GET['action'] == 'change_attendance_logout') {

    $uid = $_POST['user_name'];
    $tep_res = tep_change_attendance_logout($uid);
    echo $tep_res;


}
//检测同一天同一组是否有多个排班
/*
else if($_GET['action'] == 'check_same_group_att') {

  $group_arr = explode('||',$_POST['group_id']);
  $attendance_arr = explode('||',$_POST['att_id']);
  $group_arr = array_filter($group_arr);
  $attendance_arr = array_filter($attendance_arr);

  for($i=0; $i<count($group_arr); $i++) {
    for ($j=count($group_arr)-1; $j>$i; $j--) {
		//组或个人有相同的
		if($group_arr[$i] == $group_arr[$j]){
			 $attendance_sql_i = "select set_time from ".TABLE_ATTENDANCE_DETAIL." where id = ".$attendance_arr[$i]."";
             $attendance_info_i = tep_db_fetch_array(tep_db_query($attendance_sql_i)); 

			 $attendance_sql_j = "select set_time from ".TABLE_ATTENDANCE_DETAIL." where id = ".$attendance_arr[$j]."";
             $attendance_info_j = tep_db_fetch_array(tep_db_query($attendance_sql_j)); 

			 //判断相同的组或者个人是否有相同类型的排班
    		 if($attendance_info_i['set_time']!=$attendance_info_j['set_time']) {
				 if(is_numeric($group_arr[$i])){
					 //是组
			       $sql_get_group = "select name from ".TABLE_GROUPS." where id = ".$group_arr[$i]."";
                   $group_info = tep_db_fetch_array(tep_db_query($sql_get_group)); 
			       echo  $group_info['name'];
				   break;
				 }else{
					 //是个人
			       $sql_get_uname = "select name from ".TABLE_USERS." where userid = '".$group_arr[$i]."'";
                   $user_info = tep_db_fetch_array(tep_db_query($sql_get_uname)); 
			       echo  $user_info['name'];
				   break;
		         }
			  
			 }
	     } 
     }
  } 

}
 */
elseif($_GET['action']=='check_change_ros_rest'){
//请假时间不能重叠
	$user_id = $_POST['user_id'];
	$date = $_POST['date_str'];
	$start_time = $_POST['start_time'];
	$end_time = $_POST['end_time'];
	$user_all_att = tep_all_attenande_by_uid($user_id,$date);
	$n=0;
	$m=0;
	foreach($user_all_att as $key=>$val) {
		
		if($val['set_time']==1){
			unset($user_all_att[$key]);
		}
		//work_start 大于开始时间小于结束时间

		if(strtotime($val['work_start'])> strtotime($start_time)&& strtotime($val['work_start'])< strtotime($end_time) )	{
			$work_start = strtotime($val['work_start']);
			$n++;
		}
		if(strtotime($val['work_end'])< strtotime($end_time)&& strtotime($val['work_end'])> strtotime($start_time) )	{
			$work_end = strtotime($val['work_end']);
			$m++;
		}
	}
	if($n>1||$m>1){
		echo 'error'; 
	}if($n==1 && $m==1){
		if($work_start>$work_end){
		echo  'error';
		}
	
	}

}
/**
 *@20141015
 *新版排班设定
 */
else if($_GET['action'] == 'set_attendance_info') {

  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_ROSTER_RECORDS);

  include(DIR_FS_ADMIN.'classes/notice_box.php');
  if($ocertify->npermission <15 ){
	  $show_style =' disabled="disabled" ';
  }

  $site_id = isset($_GET['s_site_id'])?$_GET['s_site_id']:0; 
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  $action ='';
  if($_POST['param_y']!='undefined'){
      $action .=',y='.$_POST['param_y'];
      $action .=',m='.$_POST['param_m'];
  }else{
  $action =',0,0';
  }

  //排版
  $attendance_detail_list = array();
  $attendance_detail_sql = "select * from ".TABLE_ATTENDANCE_DETAIL." order by sort asc";
  $attendance_detail_query = tep_db_query($attendance_detail_sql);
  while($attendance_detail_row = tep_db_fetch_array($attendance_detail_query)){
    $attendance_detail_list[] = $attendance_detail_row;
  }

  if($id==0){
       $attendance_select_sql = "select * from " . TABLE_ATTENDANCE_DETAIL . " order by sort asc limit 1";
  }else{
      $attendance_select_sql = "select * from " . TABLE_ATTENDANCE_DETAIL . " where id=".$id;
  }
  $attendance_info_tep = tep_db_query($attendance_select_sql); 
  $attendance_info_res = tep_db_fetch_array($attendance_info_tep); 
  $id = $attendance_info_res['id'];
  $cid_array = array();
  $site_array = array();
  $buttons = array();
  $page_str = '';

  
  $form_str = tep_draw_form('attendances', FILENAME_ROSTER_RECORDS, '&action='.$action, 'post','enctype="multipart/form-data"', 'onSubmit="return check_form();"') ."\n"; 
  $page_s = TEXT_ATTENDANCE_SETTING_MOVE; 
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $page_s);
  $heading[] = array('align' => 'right', 'text' => $page_str);
  
  $attendance_info_row = array();
  //排版类型
  $adl_select = '<select onchange="set_attendance_info(this, this.value,0)" >';
  foreach($attendance_detail_list as $att_value){
    $selected = $att_value['id']!=$id ?'':' selected ';
    $adl_select .= '<option value="'.$att_value['id'].'" '.$selected.'>'.$att_value['title'].'</option>';
  }
  $adl_select .= '</select>';

      $attendance_info_row[]['text'] = array(
           array('params' => 'width="20%"','text' => TEXT_ATTENDANCE_SETTING_TITLE),
           array('text' => $adl_select)
     ); 
      $attendance_info_row[]['text'] = array(
           array('text' => TEXT_TITLE_NOTE),
           array('text' => tep_draw_input_field('title',$attendance_info_res['title'],'id="attendance_title" class="input_text_width"' .$show_style).'&nbsp;&nbsp;<font color="red" id="title_text_error"></font>'.tep_draw_hidden_field('id', $id)) 
     ); 
      $attendance_info_row[]['text'] = array(
           array('text' => ATTENDANCE_ABBREVIATION),
           array('text' => tep_draw_input_field('short_language',$attendance_info_res['short_language'],'id="short_language" class="input_text_width"' .$show_style).'&nbsp;&nbsp;<font color="red" id="short_lan_error"></font>'),
     ); 
	  //sort
      $attendance_info_row[]['text'] = array(
           array('text' => ATTENDANCE_SORT),
           array('text' => tep_draw_input_field('sort',$attendance_info_res['sort'],'class="input_text_width" style="text-align:right;"'.$show_style))
     ); 
//排班类型
    $attendance_select_type = '<select name="scheduling_type" onchange="change_type_text()" id="type_id" style="width: 109px;" '.$show_style.'>';
    $attendance_select_type .= '<option value="0" '.($attendance_info_res['scheduling_type']=='0'?' selected="selected"' : '').'>'.ATTENDANCE_SCHEDULING_TYPE_IMAGE.'</option>';
    $attendance_select_type .= '<option value="1" '.($attendance_info_res['scheduling_type']=='1'?' selected="selected"' : '').'>'.ATTENDANCE_SCHEDULING_TYPE_COLOR.'</option>';
    $attendance_select_type .= '</select>';
	//被隐藏的上传图片按钮
    $attendance_select_type .= tep_draw_file_field('src_image','','id="upload_file_image" onchange=change_image_text(this) style="opacity:0; width:0px"');

      $attendance_info_row[]['text'] = array(
           array('text' => ATTENDANCE_SCHEDULING_TYPE),
           array('text' => $attendance_select_type)
     ); 
//颜色
  $color_array = array('#000000','#808080','#800000','#800080','#008000','#808000','#000080','#008080','#C0C0C0','#FFFFFF','#FF0000','#FF00FF','#00FF00','#FFFF00','#0000FF','#00FFFF');
  if(!empty($id) && $attendance_info_res['scheduling_type']==1){
     $style_color = 'style="display:block;"'; 
  }else{
     $style_color = 'style="display:none;"'; 
  }
  $select_type_color .='<table id="src_text_color" '. $style_color.'><tr>';
  foreach($color_array as $color_key=>$color_value){
	$border_style = $attendance_info_res['src_text']==$color_value ? 'border: 2px solid #4F4F4F;':'border: 1px solid #CCCCCC;';
    $select_type_color .= '<td><a href="javascript:void(0);" onclick="select_color(this,\''.$color_value.'\')"><div class="color_div" style="float: left; background-color:'.$color_value.'; '. $border_style .' padding: 8px;"></div></a></td>';
 }

  $select_type_color .='<td><input type="hidden" name="scheduling_type_color" id="color_val" value=\''.$attendance_info_res['src_text'].'\'></td></tr></table>';
  
	  

//图片
  if((!empty($id) && $attendance_info_res['scheduling_type']==0) || empty($id)){
  $src_text = $attendance_info_res['src_text'];
   $style_image = 'style="display:block; float:left; margin:0;"';
  }else{
   $style_image = 'style="display:none;"';
  }
	$select_type_image = '<div class="upload_image" '.$style_image.'>'.tep_draw_input_field('src_image_input',$src_text, 'id="src_text_image" class="input_text_width"'. $show_style .'');
    $select_type_image .= tep_html_element_button(IMAGE_SELECT,'onclick="document.attendances.upload_file_image.click()" id="upload_button" '. $show_style .'').'</div>'; 
	 
	$div_image ='<div id="image_div" '.$style_image.'>'.ATTENDANCE_SCHEDULING_TYPE_IMAGE.'</div>';
	$div_color ='<div id="color_div" '.$style_color.'>'.ATTENDANCE_SCHEDULING_TYPE_COLOR.'</div>';
      $attendance_info_row[]['text'] = array(
          array('text' => $div_image),
		  array('text' =>$select_type_image), 
     ); 

      $attendance_info_row[]['text'] = array(
          array('text' => $div_color),
		  array('text' =>$select_type_color) 
     ); 


     if($attendance_info_res['set_time']==0 || empty($id) ) {
	   $selected_1='true';
     }else{
	   $selected_2='false';
     }
      $attendance_info_row[]['text'] = array(
	       array('text' => ATTENDANCE_SET_TIME),
           array('text' => tep_draw_radio_field('set_time',0,$selected_1,'','id="set_left" onclick=change_set_time(0)'.$show_style).ATTENDANCE_SET_TIME_FIELD.'&nbsp;'.tep_draw_radio_field('set_time',1,$selected_2,'','id="set_right" onclick=change_set_time(1)' .$show_style).ATTENDANCE_SET_FIELD_TIME)
	  );

//工作开始时间
	  $work_start_array = explode(':',$attendance_info_res['work_start']);
	  
	  $work_start_min_left= substr($work_start_array[1],0,1);
	  $work_start_min_right= substr($work_start_array[1],1,2);
      $work_start = '<select name="work_start_hour" id="work_start_hour" '.$show_style.'>';
	  for($i=0;$i<=23;$i++){
          $selected = $work_start_array['0']!=$i ?'':' selected ';
          $work_start .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
	  }
      $work_start .= '</select>&nbsp;'.HOUR_TEXT;

      $work_start .= '<select name="work_start_minute_a" id="work_start_min_l" '.$show_style.'>';
	  for($i=0;$i<=5;$i++){
          $selected = $work_start_min_left!=$i ?'':' selected ';
          $work_start .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	  }
      $work_start .= '</select>';

      $work_start .= '<select name="work_start_minute_b" id="work_start_min_r" '.$show_style.'>';
	  for($i=0;$i<=9;$i++){
          $selected = $work_start_min_right!=$i ?'':' selected ';
          $work_start .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	  }
      $work_start .= '</select>&nbsp;'.MINUTE_TEXT.'&nbsp;&nbsp;<font color="red" id="work_start_error"></font>';

//工作结束时间
	  
	  $work_end_array = explode(':',$attendance_info_res['work_end']);
	  $work_end_min_left= substr($work_end_array[1],0,1);
	  $work_end_min_right= substr($work_end_array[1],1,2);
      $work_end = '<select name="work_end_hour" id="work_end_hour" '.$show_style.'>';
	  for($i=0;$i<=23;$i++){
          $selected = $work_end_array['0']!=$i ?'':' selected ';
          $work_end .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	  }
      $work_end .= '</select>&nbsp;'.HOUR_TEXT;

      $work_end .= '<select name="work_end_minute_a" id="work_end_min_l" '.$show_style.'>';
	  for($i=0;$i<=5;$i++){
          $selected = $work_end_min_left!=$i ?'':' selected ';
          $work_end .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	  }
      $work_end .= '</select>';

      $work_end .= '<select name="work_end_minute_b" id="work_end_min_r" '.$show_style.'>';
	  for($i=0;$i<=9;$i++){
          $selected = $work_end_min_right!=$i ?'':' selected ';
          $work_end .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	  }
      $work_end .= '</select>&nbsp;'.MINUTE_TEXT.'&nbsp;&nbsp;<font color="red" id="work_end_error"></font>';


//休息开始时间
	  $rest_start_array = explode(':',$attendance_info_res['rest_start']);
	  $rest_start_min_left= substr($rest_start_array[1],0,1);
	  $rest_start_min_right= substr($rest_start_array[1],1,2);
      $rest_start = '<select name="rest_start_hour" '.$show_style.'>';
	  for($i=0;$i<=23;$i++){
          $selected = $rest_start_array['0']!=$i ?'':' selected ';
          $rest_start .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	  }
      $rest_start .= '</select>&nbsp;'.HOUR_TEXT;

      $rest_start .= '<select name="rest_start_minute_a" '.$show_style.'>';
	  for($i=0;$i<=5;$i++){
          $selected = $rest_start_min_left!=$i ?'':' selected ';
          $rest_start .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	  }
      $rest_start .= '</select>';
      $rest_start .= '<select name="rest_start_minute_b" '.$show_style.'>';
	  for($i=0;$i<=9;$i++){
          $selected = $rest_start_min_right!=$i ?'':' selected ';
          $rest_start .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	  }
      $rest_start .= '</select>&nbsp;'.MINUTE_TEXT;


//休息结束时间
	  $rest_end_array = explode(':',$attendance_info_res['rest_end']);
	  $rest_end_min_left= substr($rest_end_array[1],0,1);
	  $rest_end_min_right= substr($rest_end_array[1],1,2);
      $rest_end = '<select name="rest_end_hour" '.$show_style.'>';
	  for($i=0;$i<=24;$i++){
          $selected = $rest_end_array['0']!=$i ?'':' selected ';
          $rest_end .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	  }
      $rest_end .= '</select>&nbsp;'.HOUR_TEXT;

      $rest_end .= '<select name="rest_end_minute_a" '.$show_style.'>';
	  for($i=0;$i<=5;$i++){
          $selected = $rest_end_min_left!=$i ?'':' selected ';
          $rest_end .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	  }
      $rest_end .= '</select>';
      $rest_end .= '<select name="rest_end_minute_b" '.$show_style.'>';
	  for($i=0;$i<=9;$i++){
          $selected = $rest_end_min_right!=$i ?'':' selected ';
          $rest_end .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
	  }
      $rest_end .= '</select>&nbsp;'.MINUTE_TEXT;
      
	  if($attendance_info_res['set_time']==0 || empty($id)){
	      $time_field_style = 'style="display:block;"';
	  }else{
	      $time_field_style = 'style="display:none;"';
	  }

      $left_t = '';
      $left_t .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="set_time_field_title"'.$time_field_style.'><tr>';
      $left_t .= '<td>'.ATTENDANCE_WORK_START.'</td></tr><tr><td>'.ATTENDANCE_WORK_END.'</td></tr><tr><td>'.ATTENDANCE_REST_START.'</td></tr><tr><td>'.ATTENDANCE_REST_END.'</td>';
      $left_t .= '</tr></table>';
      $right_t = '';
      $right_t .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="set_time_field_content"'.$time_field_style.'><tr>';
      $right_t .= '<td>'.$work_start.'</td></tr><tr><td>'.$work_end.'</td></tr><tr><td>'.$rest_start.'</td></tr><tr><td>'.$rest_end.'</td>';
      $right_t .= '</tr></table>';
      $attendance_info_row[]['text'] = array(
        array('text' => $left_t), 
        array('text' => $right_t)
       );


	  //时间数
     if(!empty($id) && $attendance_info_res['set_time']==1){
	    $attendance_work_hours= del_zero($attendance_info_res['work_hours']);
	    $attendance_rest_hours= del_zero($attendance_info_res['rest_hours']);
	 }
      $work_hours=  tep_draw_input_field('work_hours',$attendance_work_hours,'id="work_hours" class="input_text_width" style="text-align:right;"' .$show_style) . TELECOM_UNKNOW_TABLE_TIME .'&nbsp;&nbsp;<font color="red" id="work_hours_error"></font>';
      $rest_hours =  tep_draw_input_field('rest_hours',$attendance_rest_hours,'id="rest_hours" class="input_text_width" style="text-align:right;"' .$show_style) . TELECOM_UNKNOW_TABLE_TIME .'&nbsp;&nbsp;<font color="red" id="rest_hours_error"></font>';
	  
	  if($attendance_info_res['set_time']==1 && !empty($id)){
	      $time_numbers_style = 'style="display:block;"';
	  }else{
	      $time_numbers_style = 'style="display:none;"';
	  }

      $left_td = '';
      $left_td .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="set_time_numbers_title"'.$time_numbers_style.'><tr>';
      $left_td .= '<td align="">'.ATTENDANCE_WORK_HOURS.'</td></tr><tr><td>'.ATTENDANCE_REST_HOURS.'</td>';
      $left_td .= '</tr></table>';
      $right_td = '';
      $right_td .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="set_time_numbers_content"'.$time_numbers_style.'><tr>';
      $right_td .= '<td align="">'.$work_hours.'</td></tr><tr><td>'.$rest_hours.'</td>';
      $right_td .= '</tr></table>';
        $attendance_info_row[]['text'] = array(
        array('params' => 'width="20%"','text' => $left_td), 
        array('text' => $right_td)
        );
   
    $add_user_text= TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.$attendance_info_res['add_user'];
    $update_user_text= TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.$attendance_info_res['add_time'];
    $add_time_text= TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.$attendance_info_res['update_user'];
    $update_time_text= TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.$attendance_info_res['update_time'];
     $hidden_add_user = tep_draw_input_field('add_user',$attendance_info_res['add_user'],'style="display:none"');
     $hidden_add_time = tep_draw_input_field('add_time',$attendance_info_res['add_time'],'style="display:none"');

	 if(!empty($id)) {
      $attendance_info_row[]['text'] = array(
		  array('params' => 'colspan="2"','text' => $hidden_add_user)
      );
      $attendance_info_row[]['text'] = array(
		  array('params' => 'colspan="2"','text' => $hidden_add_time)
      );
      $attendance_info_row[]['text'] = array(
           array('text' => $add_user_text),
           array('text' =>$update_user_text)
     ); 
      $attendance_info_row[]['text'] = array(
           array('text' => $add_time_text),
           array('text' =>$update_time_text)
	   ); 
	 }

  $button[] = '<input type="hidden" id="flag_num" value="1"><a href="javascript:void(0);">'.tep_html_element_button(BUTTON_ADD_TEXT,'onclick="clear_data();"'.$show_style).'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE,'onclick="delete_attendance_info(\''.$id.'\');"'.$show_style).'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE,'onclick="check_attendance_info(1'.$action.');"'.$show_style).'</a>'; 
  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
   
  $notice_box->get_contents($attendance_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
} else if($_GET['action'] == 'set_payrols_info') {

  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_ROSTER_RECORDS);

  include(DIR_FS_ADMIN.'classes/notice_box.php');
  if($ocertify->npermission <15 ){
	  $show_style =' disabled="disabled" ';
  }

  $site_id = isset($_GET['s_site_id'])?$_GET['s_site_id']:0; 
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  $action ='update';
  if($_POST['param_y']!='undefined'){
      $action .='&y='.$_POST['param_y'];
      $action .='&m='.$_POST['param_m'];
  }
  $attendance_select_sql = "select * from " . TABLE_ATTENDANCE_DETAIL . " where id=".$id;
  $attendance_info_tep = tep_db_query($attendance_select_sql); 
  $attendance_info_res = tep_db_fetch_array($attendance_info_tep); 
  $cid_array = array();
  $site_array = array();
  $buttons = array();
  $page_str = '';
  
  $form_str = tep_draw_form('attendances', FILENAME_ROSTER_RECORDS, '&action='.$action, 'post','enctype="multipart/form-data"', 'onSubmit="return check_form();"') ."\n"; 
  $page_s = ATTENDANCE_HEAD_TITLE; 
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $page_s);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  //排班
  $attendance_detail_list = array();
  $attendance_detail_sql = "select * from ".TABLE_ATTENDANCE_DETAIL." order by sort asc";
  $attendance_detail_query = tep_db_query($attendance_detail_sql);
  while($attendance_detail_row = tep_db_fetch_array($attendance_detail_query)){
    $attendance_detail_list[] = $attendance_detail_row;
  }
  if($id==0){
       $attendance_select_sql = "select * from " . TABLE_ATTENDANCE_DETAIL . " order by sort asc limit 1";
  }else{
      $attendance_select_sql = "select * from " . TABLE_ATTENDANCE_DETAIL . " where id=".$id;
  }
  $attendance_info_tep = tep_db_query($attendance_select_sql); 
  $attendance_info_res = tep_db_fetch_array($attendance_info_tep); 
  $id = $attendance_info_res['id'];
  $cid_array = array();
  $site_array = array();
  $buttons = array();
  $page_str = '';

  
  $form_str = tep_draw_form('attendances', FILENAME_ROSTER_RECORDS, '&action='.$action, 'post','enctype="multipart/form-data"', 'onSubmit="return check_form();"') ."\n"; 
  $page_s = TEXT_ATTENDANCE_SETTING_PAYROLLS; 
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $page_s);
  $heading[] = array('align' => 'right', 'text' => $page_str);
  
  $attendance_info_row = array();
  //排版类型
  $adl_select = '<select onchange="set_attendance_info(this, this.value,1)" >';
  foreach($attendance_detail_list as $att_value){
    $selected = $att_value['id']!=$id ?'':' selected ';
    $adl_select .= '<option value="'.$att_value['id'].'" '.$selected.'>'.$att_value['title'].'</option>';
  }
  $adl_select .= '</select>';
  $attendance_info_row[]['text'] = array(
      array('text' => TEXT_ATTENDANCE_SETTING_TITLE),
      array('text' => $adl_select),
      array('text' => tep_draw_hidden_field('id', $id)) 
   ); 
  //参数标题

  $param_array = array(
	  'SHIFT'   =>'出勤',
	  'DAYOFF'  =>'休み',
	  'HOLIDAY' =>'休暇',
	  'OVERTIME'=>'残業',
  );
  $param_mod = '<select onchange="set_param_style('.$id.',this.value)">';
  $param_mod .= '<option >--</option>';
  foreach($param_array as $key=>$param_val){
	if(!is_bool(strpos($attendance_info_res['param_a'],$key.'_')) && !is_bool(strpos($attendance_info_res['param_b'],'ACTUAL_'.$key.'_'))){
    $selected = 'selected = selected';
	}else{
    $selected = '';
	}
    $param_mod .= '<option value="'.$key.'" '.$selected.'>'.$param_val.'</option>';
  }
  $param_mod .= '</select>';
      $attendance_info_row[]['text'] = array(
           array('text' => TEXT_PARAM_TYPE),
           array('text' => $param_mod)
     ); 
  
	//param
      $attendance_info_row[]['text'] = array(
           array('text' => TEXT_ADL_SELECT_USER_TEXT),
           array('text' => '<div>${ '.tep_draw_input_field('param_a',$attendance_info_res['param_a'],'class="input_text_width"'.$show_style).' }</div>')
     ); 
	  $attendance_info_row[]['text'] =array(
	       array('text' => TEXT_PRRAM_ATTENDANCE_NUM), 
           array('text' => '<div>${ '.tep_draw_input_field('param_b',$attendance_info_res['param_b'],'class="input_text_width"'.$show_style).' }</div>')
     ); 


    $add_user_text= TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.$attendance_info_res['add_user'];
    $update_user_text= TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.$attendance_info_res['add_time'];
    $add_time_text= TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.$attendance_info_res['update_user'];
    $update_time_text= TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.$attendance_info_res['update_time'];
     $hidden_add_user = tep_draw_input_field('add_user',$attendance_info_res['add_user'],'style="display:none"');
     $hidden_add_time = tep_draw_input_field('add_time',$attendance_info_res['add_time'],'style="display:none"');

	 if(!empty($id)) {
      $attendance_info_row[]['text'] = array(
		  array('text' => $hidden_add_user)
      );
      $attendance_info_row[]['text'] = array(
		  array('text' => $hidden_add_time)
      );
      $attendance_info_row[]['text'] = array(
           array('text' => $add_user_text),
           array('text' =>$update_user_text)
     ); 
      $attendance_info_row[]['text'] = array(
           array('text' => $add_time_text),
           array('text' =>$update_time_text)
	   ); 
	 }

  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE,'onclick="delete_attendance_info(\''.$id.'\');"'.$show_style).'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE,'onclick="check_attendance_info();"'.$show_style).'</a>'; 
  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
   
  $notice_box->get_contents($attendance_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();

  //排版参数默认值
}else if($_GET['action'] == 'select_param_sigle'){
	$id = $_POST['id'];
    $attendance_detail_query = tep_db_query("select * from " . TABLE_ATTENDANCE_DETAIL . " where id=".$id);
    $attendance_res = tep_db_fetch_array($attendance_detail_query);
	$param =$_POST['param']; 
	//原有数据符合

	if(!is_bool(strpos($attendance_res['param_a'],$param)) && !is_bool(strpos($attendance_res['param_b'],'ACTUAL_'.$param.'_'))){
       echo  $attendance_res['param_a'].'||'.$attendance_res['param_b'];	
	   exit;
	
	}else{
	$param_search_sql = "select * from ". TABLE_ATTENDANCE_DETAIL."
        where `param_a` like '%".$param."%'
        or `param_b` like '%".$param."%'";
	$param_array_ext = array(
		A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,AA,BB,CC,DD,EE,FF,GG,HH,II,JJ,KK,LL,MM,NN,OO,PP,QQ,RR,SS,TT,UU,VV,WW,XX,YY,ZZ	
	);
    $param_query = tep_db_query($param_search_sql);
    while($param_row = tep_db_fetch_array($param_query)){
		$param_list[]=$param_row['param_a'];
		$param_list[]=$param_row['param_b'];
	}
	foreach($param_array_ext as $key=>$val){
		if(!in_array($param.'_'.$val,$param_list) and !in_array('ACTUAL_'.$param.'_'.$val,$param_list)){
		   echo  $param.'_'.$val.'||ACTUAL_'.$param.'_'.$val;
		   exit;
		}
	   
	}
}

}else if($_GET['action'] == 'set_attendance_group_info'){
 include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_ROSTER_RECORDS);
 include(DIR_FS_ADMIN.'classes/notice_box.php');
 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
 $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
 $page_s = TEXT_GROUP_USER_LIST; 
 $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
 $heading[] = array('align' => 'left', 'text' => $page_s);
 $heading[] = array('align' => 'right', 'text' => $page_str);
$action = 'update_show_user';
  if($_POST['param_y']!='undefined'){
      $action .='&y='.$_POST['param_y'];
      $action .='&m='.$_POST['param_m'];
  }
 $form_str = tep_draw_form('attendances', FILENAME_ROSTER_RECORDS, 'action='.$action, 'post','enctype="multipart/form-data"', 'onSubmit="return check_form();"') ."\n"; 
 $group_content_row_staff = array();
 $user_info = tep_get_user_info($ocertify->auth_user);
        $group_list = tep_get_group_tree();
/*--------------------------------------------------------------*/
//当前用户所有可操作的用户(用于权限小于15的)
//当前用户所在的组
     $user_group_list = tep_get_groups_by_user($ocertify->auth_user);
//组长对应的组
     $is_manage_group = tep_is_group_manager($ocertify->auth_user,true);
//用所有可看到的组
     if(!empty($is_manage_group)){
        $user_group_list = array_merge($user_group_list,$is_manage_group);
     }
//当个人不是root和admin用户的时候显示相应权限的用户
     $all_users_list ='';
     foreach($user_group_list as $user_all_group_id){
        $user_query = tep_db_query("select * from ".TABLE_GROUPS." where id='".$user_all_group_id."' and group_status=1");
        $user_row = tep_db_fetch_array($user_query);
        $all_users_list .= $user_row['all_users_id'].'|||';
     }
     $all_users_tep_array = explode('|||',$all_users_list.'|||'.$ocertify->auth_user);
     $show_group_user_all = array_unique($all_users_tep_array);
     $user_sql = 'select u.*, p.permission from ' . TABLE_USERS . ' u, ' .  TABLE_PERMISSIONS . " p where u.userid = p.userid and u.status=1"; 
     $user_query = tep_db_query($user_sql);
     while($user_row = tep_db_fetch_array($user_query)){
        $show_group_user_true[] = $user_row['userid'];
     }
     $show_group_user_staff = array_intersect($show_group_user_true,$show_group_user_all);

/*--------------------------------------------------------------*/
        $show_group_id=0;
        $show_checked_user_list = array();
        $show_group_user = array();
        $show_select_group_user = array();
		//当前用户最后一次保存显示的组(并且组是有效的)
        //$show_group_sql = "select * from ".TABLE_ATTENDANCE_GROUP_SHOW." s, ".TABLE_GROUPS." g  WHERE s.gid=g.id and g.group_status=1 and is_select='1' and operator_id='".$ocertify->auth_user."'";
		$show_group_sql = "select * from ".TABLE_ATTENDANCE_GROUP_SHOW."  WHERE is_select='1' and operator_id='".$ocertify->auth_user."'";

        $show_group_query = tep_db_query($show_group_sql);
        $has_default = false;
        while($show_group_row = tep_db_fetch_array($show_group_query)){
          $has_default = true;
          $show_group_id = $show_group_row['gid'];
          $check_sql = tep_db_query("select * from ".TABLE_GROUPS." where id='".$show_group_id."' and group_status=1");
          $user_group_list = tep_get_groups_by_user($ocertify->auth_user);
		  //组被禁止||管理员
		  if(tep_db_num_rows($check_sql)==0 && $show_group_id!=0){ 
                 $has_default = false;
				 $show_group_id=0;
		  }else if($show_group_row['user_id']!=''){
			  $show_select_group_user[]=$show_group_row['user_id'];
		  
		  }
		  $show_att_status =$show_group_row['att_status'];
		  $show_type = $show_group_row['show_type'];
        }
        if($has_default){
          if($show_group_id==0){
           // $user_sql = "select * from ".TABLE_USERS." where status='1'";
            $user_sql = 'select u.*, p.permission from ' . TABLE_USERS . ' u, ' .  TABLE_PERMISSIONS . " p where u.userid = p.userid and u.status=1"; 
            $user_query = tep_db_query($user_sql);
            while($user_row = tep_db_fetch_array($user_query)){
              $show_group_user[] = $user_row['userid'];
            }
          } else {
            $user_sql = "select * from ".TABLE_GROUPS." 
               where id='".$show_group_id."' and group_status=1";
            $user_query = tep_db_query($user_sql);
            if($user_row = tep_db_fetch_array($user_query)){
              $show_group_user = explode('|||',$user_row['all_users_id']);
            }
			if(empty($show_group_user)){
			    $show_group_user[]=$ocertify->auth_user;
			
			}
          }
        }else{
          if($ocertify->npermission>10){
            //$user_sql = "select * from ".TABLE_USERS." where status='1'";
            $user_sql = 'select u.*, p.permission from ' . TABLE_USERS . ' u, ' .  TABLE_PERMISSIONS . " p where u.userid = p.userid and u.status=1"; 
            $user_query = tep_db_query($user_sql);
            while($user_row = tep_db_fetch_array($user_query)){
              $show_group_user[] = $user_row['userid'];
              $show_select_group_user[] = $user_row['userid'];
            }
          }else{
			  $show_group_user=$show_group_user_staff;
              $show_select_group_user = $show_group_user_staff;
          }
          //$show_select_group_user[] = $ocertify->auth_user;
        }
        $show_select_group_user = array_unique($show_select_group_user);
		//保证被禁止的组的数据/禁用用户不会显示了
        if($ocertify->npermission<15){
            $show_select_group_user= array_intersect($show_group_user_staff,$show_select_group_user);
			$show_group_user =  array_intersect($show_group_user_staff,$show_group_user);
        }

 $group_str .= '<select name="show_group" onchange="change_user_list(this)">';

//当前用户所在的组
 $user_group_list = tep_get_groups_by_user($ocertify->auth_user);
 //组长对应的组
$is_manage_group = tep_is_group_manager($ocertify->auth_user,true);
//用所有可看到的组
if(!empty($is_manage_group)){
$user_group_list = array_merge($user_group_list,$is_manage_group);
}
     foreach($user_group_list as $group_id){
	 //用户所在组的父级组
        $g_prarent_id_list= group_parent_id_list($group_id);
	    foreach($g_prarent_id_list as $val_tep){
           $group_parents_list[] = $val_tep; 	
	    }
    }
     $group_parents_list = array_unique($group_parents_list);
     if(!empty($group_parents_list)){
        $group_parents_list = array_merge($group_parents_list,$user_group_list);
     }else{
        $group_parents_list = $user_group_list; 
    }
	 //全
       $group_str .= '<option value="0" ';
       if($show_group_id==0){
         $group_str .= ' selected ';
        }
       $group_str .= ' >'.TEXT_ALL_GROUP.'</option>';
//当个人不是root和admin用户的时候显示相应权限的用户
/*--------------------------------------------------------------*/
 $all_users_list ='';
foreach($user_group_list as $user_all_group_id){
    $user_query = tep_db_query("select * from ".TABLE_GROUPS." where id='".$user_all_group_id."' and group_status=1");
    $user_row = tep_db_fetch_array($user_query);
    $all_users_list .= $user_row['all_users_id'].'|||';
}
$all_users_tep_array = explode('|||',$all_users_list.'|||'.$ocertify->auth_user);
$all_users_array = array_unique($all_users_tep_array);
/*--------------------------------------------------------------*/
	
 foreach($group_list as $group){
    if($ocertify->npermission <15 && in_array($group['id'],$group_parents_list)){
		if(!in_array($group['id'],$user_group_list)){
			$disabled_group = 'disabled = "disabled"';
		}else{
			$disabled_group = '';
		}
           $group_str .= '<option '.$disabled_group.' value="'.$group['id'].'"';
           if($show_group_id == $group['id']){
               $group_str .= ' selected ';
           }
         $group_str .= '>'.$group['text'].'</option>';
	}else if($ocertify->npermission >=15){
      $group_query = tep_db_query("select * from ".TABLE_GROUPS." where id='".$group['id']."' and group_status=1");
      $group_array = tep_db_fetch_array($group_query);

      $group_child_query = tep_db_query("select * from ".TABLE_GROUPS." where parent_id='".$group['id']."' and group_status=1");
	  $group_child_num = tep_db_num_rows($group_child_query);
		     $flag = 0; 
      while($group_child_array = tep_db_fetch_array($group_parent_query)){
          $group_tep_query = tep_db_query("select * from ".TABLE_GROUPS." where id='".$group_child_array['id']."' and group_status=1");
          $group_tep = tep_db_fetch_array($group_tep_query);
		  if($group_tep['all_users_id']!=''){
		     $flag = 1; 
		  }
	  }
	  
	  if($group_array['all_users_id']=='' && $group_child_num==0){
          $dis = 'style="display:none;"';    
	  }else if($group_array['all_users_id']=='' && $group_child_num!=0 & $flag = 1 ){
          $dis = 'disabled="disabled"';    
	  }else{
          $dis = '';    
	  
	  }

       $group_str .= '<option '.$dis.' value="'.$group['id'].'"';
       if($show_group_id == $group['id']){
           $group_str .= ' selected ';
       }
      $group_str .= '>'.$group['text'].'</option>';
	
	}
}
$group_str .= '</select>';

 $group_content[]['text'] = array(
       array('params'=>'width="20%" nowrap="nowrap"','text'=> TEXT_GROUP_SELECT),
       array('text' => $group_str)
  ); 
$select_all ='<input type="checkbox" value="1" onclick="select_all_box(this.value)" id="select_all_users">';
$group_content[]['text'] = array(
      array('params'=>'width="20%" nowrap="nowrap"','text' => TEXT_ATTENDANCE_SELECT_TITLE),
      array('text' => $select_all.TEXT_ALL_GROUP)
  ); 

$user_sql = 'select u.*, p.permission from ' . TABLE_USERS . ' u, ' .  TABLE_PERMISSIONS . " p where u.userid = p.userid and u.status=1"; 
$user_query = tep_db_query($user_sql);
$all_users =array();
while($user_row = tep_db_fetch_array($user_query)){
   $all_users[]=$user_row['userid'];
}
//用户合法
$show_group_user = array_intersect($show_group_user,$all_users);
        foreach($show_group_user as $show_list_uid){
          if($show_list_uid!=''){
			$tep_array= tep_get_user_info($show_list_uid);
			$uname_arr[] = $tep_array['name'];

          }
        }
		$group_user_list = array_combine($show_group_user,$uname_arr);
		 $group_user_list = array_filter($group_user_list);
		asort($group_user_list);
        $user_str = '<table id="show_user_list" width="80%" cellspacing="0" cellpadding="0" border="0">';
        $user_str .= '<tr>';
		
	    $i=1;
		foreach($group_user_list as $key=>$val) {
	 	    $i++;
			if($i>1 && $i%2 ==0 ){
                $user_str .= '</tr><tr>';
			}
             $user_str .= '<td width="40%" style="min-width:220px;'.$display.'"><input type="checkbox" name="show_group_user_list[]" onclick="select_all_box(5)" id="'.$key.'"';
             if(in_array($key,$show_select_group_user)){
                $user_str .= ' checked="checked" ';
                $user_atted[$key] = tep_is_attenandced_date($key);
                $show_checked_user_list[] = $key;
             }
              $user_str .= ' value="'.$key.'" >';
              $user_str .=  '<label for="'.$key.'">'.$val.'</label>';
              $user_str .= '</td>';
		  
		}
          $user_str .= '</tr>';
       $user_str .= '</table>';

 if($_POST['att_show_status']!='undefined'){
   $att_status ='<input type="hidden" name="att_status" value='.$_POST['att_show_status'].'>';
 }

    $group_content[]['text'] = array(
           array('params'=>'width="20%"','text'=> ''),
           array('text' =>$user_str.$att_status)
	); 

 $button[] = array('params'=>'align="center" colspan="2"','text'=>'<input type="submit" value="'.IMAGE_SAVE.'">');
    $group_content[]= array(
          'text'=> $button,
	); 

 $notice_box->get_heading($heading);
 $notice_box->get_form($form_str);
 $notice_box->get_contents($group_content);
 echo $notice_box->show_notice();
} else if($_GET['action']=='delete_attendance_info') {
	$id = $_POST['attendance_id'];
//删除排班
   $del_sql = "delete from ".TABLE_ATTENDANCE_DETAIL." where id=".$id;
   tep_db_query($del_sql);
//删除排班对应的人或组
   $del_info = "delete from ".TABLE_ATTENDANCE_DETAIL_DATE." where attendance_detail_id = ".$id;
   tep_db_query($del_info);
//删除请假和加班对应的排班
   $del_replace = "delete from ".TABLE_ATTENDANCE_DETAIL_REPLACE." where attendance_detail_id = ".$id." and replace_attendance_detail_id = ".$id."";
   tep_db_query($del_replace);


 echo   mysql_affected_rows();
}else if($_GET['action']=='change_model_get_time'){
    $id=$_POST['id'];
    $sql_get_time = "select * from `".TABLE_ATTENDANCE_DETAIL."` where id=".$id;
    $res_tep = tep_db_query($sql_get_time);
    $rows=tep_db_fetch_array($res_tep);
    if($rows['set_time']==0){
    $return_res = '<span>'.$rows['work_start'].'--'.$rows['work_end'].'</span>';
    }elseif($rows['set_time']==1){
         $work_time = $rows['work_hours']+$rows['rest_hours'];
         $return_res = '<span>'.$work_time .TELECOM_UNKNOW_TABLE_TIME. '</span>';
     }

echo  $return_res;
}else if($_GET['action']=='change_att_date'){
  include(DIR_FS_ADMIN.'classes/notice_box.php'); 
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_ROSTER_RECORDS);

  $heading = array();
  $date_str = substr($_GET['date'],0,4).'-'.substr($_GET['date'],4,2).'-'.substr($_GET['date'],6,2);
  if(isset($_GET['uid'])&&$_GET['uid']!=''){ 
    $user_info_self = tep_get_user_info($_GET['uid']);
    $date_str .= '&nbsp;&nbsp;'.$user_info_self['name'];
  }
  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $date_str);
  $heading[] = array('align' => 'right', 'text' => $page_str);

  $attendance_sql = "select * from " .TABLE_ATTENDANCE_RECORD. " WHERE 
    id ='".$_GET['aid']."' and user_name = '".$_GET['uid']."'";
  $attendance_query = tep_db_query($attendance_sql);
  if($attendance_row = tep_db_fetch_array($attendance_query)){
    $login_end = substr($attendance_row['login_time'],11,5);
    $logout_end = substr($attendance_row['logout_time'],11,5);
    $aid = $attendance_row['id'];
  }


  $attendance_start_array = explode(':',$login_end);
  $attendance_start_min_left= substr($attendance_start_array[1],0,1);
  $attendance_start_min_right= substr($attendance_start_array[1],1,2);
  $attendance_start = '<select name="att_start_hour" id="att_start_hour" '.$disabled.'>';
  for($i=0;$i<=23;$i++){
    $selected = $attendance_start_array['0']!=$i ?'':' selected ';
    $attendance_start .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
  }
  $attendance_start .= '</select>';

  $attendance_start .= '<select name="att_start_minute_a" id="att_start_min_l" '.$disabled.'>';
  for($i=0;$i<=5;$i++){
    $selected = $attendance_start_min_left!=$i ?'':' selected ';
    $attendance_start .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $attendance_start .= '</select>';

  $attendance_start .= '<select name="att_start_minute_b" id="att_start_min_r" '.$disabled.'>';
  for($i=0;$i<=9;$i++){
    $selected = $attendance_start_min_right!=$i ?'':' selected ';
    $attendance_start .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $attendance_start .= '</select>&nbsp;&nbsp;<font color="red" id="att_start_error"></font>';

  $attendance_end_array = explode(':',$logout_end);
  $attendance_end_min_left= substr($attendance_end_array[1],0,1);
  $attendance_end_min_right= substr($attendance_end_array[1],1,2);
  $attendance_end = '<select name="att_end_hour" id="att_end_hour" '.$disabled.'>';
  for($i=0;$i<=23;$i++){
    $selected = $attendance_end_array['0']!=$i ?'':' selected ';
    $attendance_end .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $attendance_end .= '</select>';

  $attendance_end .= '<select name="att_end_minute_a" id="att_end_min_l" '.$disabled.'>';
  for($i=0;$i<=5;$i++){
    $selected = $attendance_end_min_left!=$i ?'':' selected ';
    $attendance_end .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $attendance_end .= '</select>';

  $attendance_end .= '<select name="att_end_minute_b" id="att_end_min_r" '.$disabled.'>';
  for($i=0;$i<=9;$i++){
    $selected = $attendance_end_min_right!=$i ?'':' selected ';
    $attendance_end .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $attendance_end .= '</select>&nbsp;&nbsp;<font color="red" id="att_end_error"></font>';
  $attendance_end .= '<input type="hidden" name="get_date" value="'.$_GET['date'].'">';
  $attendance_end .= '<input type="hidden" name="uid" value="'.$_GET['uid'].'">';
  $attendance_end .= '<input type="hidden" name="aid" value="'.$aid.'">';

  $as_info_row[]['text'] = array(
    array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_ADL_SELECT_USER_TEXT), 
    array('align' => 'left', 'params' => ' nowrap="nowrap"', 'text' =>
      $attendance_start.'&nbsp;～&nbsp'.$attendance_end)
  );
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, $disabled.'id="button_save" onclick="save_submit(\''.$ocertify->npermission.'\');"').'</a>'; 
  
  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }
  $action_url_date = substr($_GET['date'],0,4) == date('Y') ? '' : '&y='.substr($_GET['date'],0,4);
  $action_url_month = substr($_GET['date'],4,2) == date('m') ? '' : '&m='.substr($_GET['date'],4,2);
  $action_url = 'action=save_att_date'.$action_url_date.$action_url_month;
  $form_str = tep_draw_form('attendance_setting_form', FILENAME_ROSTER_RECORDS, $action_url);

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($as_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();

}elseif($_GET['action']=='get_scheduling_time') {
	$id = $_POST['mould_id'];
$res_tep = tep_db_query("select set_time,work_start,work_end from ".TABLE_ATTENDANCE_DETAIL." where id=".$id."");	

$row_array = tep_db_fetch_array($res_tep);
//if($row_array)
if($row_array['set_time']==0){
	$work_start_array = explode(':',$row_array['work_start']);
	$work_start_min_left= substr($work_start_array[1],0,1);
	$work_start_min_right= substr($work_start_array[1],1,2);
    $work_end_array = explode(':',$row_array['work_end']);
    $work_end_min_left= substr($work_start_array[1],0,1);
    $work_end_min_right= substr($work_start_array[1],1,2);
    echo  $work_start_array[0].','.$work_start_min_left.','.$work_start_min_right.','.$work_end_array[0].','.$work_end_min_left.','.$work_end_min_right;
}elseif($row_array['set_time']!=0) {
    echo "0,0";
}

}else if($_GET['action'] == 'tep_show_info_attendance') {
	/*@date 20140821 验证当前用户是不是组长
	 * 以便控制是否能够查看组排班信息
	 * */
	if(tep_is_manager_by_gid($ocertify->auth_user,$_POST['gid'])) {
	  $flag ='true';	
	}else{
      $flag='false';
	}
	echo $flag;

}else if($_GET['action'] == 'show_user_payroll'){
 include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_PAYROLLS);
 include(DIR_FS_ADMIN.'classes/notice_box.php');
 $notice_box = new notice_box('popup_order_title', 'popup_order_info'); 
 $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
 //头部
 $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
 $heading[] = array('text' => $_POST['user_name']);
 $form_str = tep_draw_form('save_user_payroll', FILENAME_PAYROLLS,'action=save_user_payroll', 'post', 'enctype="multipart/form-data"').'<input type="hidden" name="user_id" value="'.$_POST['user_id'].'"><input type="hidden" name="user_payroll_list" value="'.$_POST['user_payroll_list'].'"><input type="hidden" name="save_date" value="'.$_POST['save_date'].'">';
 
 $heading[] = array('align' => 'right', 'text' => '<span id="next_prev"></span>&nbsp&nbsp'.$page_str);

 //基本工资
 //获取组对应的工资项目
 $groups_id = $_POST['groups_id'];
 $user_payroll_list = $_POST['user_payroll_list'];
 $user_payroll_list_array = array();
 $user_payroll_date_array = array();
 $show_tep_info = array();
 $user_payroll_contents = '';
 if($user_payroll_list != ''){

   $user_payroll_query = tep_db_query("select payroll_id,payroll_value,start_date,end_date,contents,create_date,update_date,create_user,update_user from ".TABLE_USER_PAYROLL_INFO." where id in (".$user_payroll_list.")"); 
   while($user_payroll_array = tep_db_fetch_array($user_payroll_query)){
   $show_tep_info[]=$user_payroll_array;
     $user_payroll_list_array[$user_payroll_array['payroll_id']] = $user_payroll_array['payroll_value'];
     $user_payroll_date_array[$user_payroll_array['payroll_id']] = array('start'=>$user_payroll_array['start_date'],'end'=>$user_payroll_array['end_date']);
     if($user_payroll_contents == ''){
       $user_payroll_contents = $user_payroll_array['contents'];
     }
   }
   tep_db_free_result($user_payroll_query);
 }
 $groups_users_id = array();
 if($groups_id != 0){
   $groups_payroll_query = tep_db_query("select * from ".TABLE_PAYROLL_SETTLEMENT." where group_id='".$groups_id."' and project_id=0 order by sort");
   $date_i = 0;
   while($groups_payroll_array = tep_db_fetch_array($groups_payroll_query)){
     $group_content_row_payroll = array();
     $date_str = '<td>';
     $date_str .= '<style type="text/css">
  #new_yui3_'.$date_i.' {
  margin-left:-168px;
  *margin-left:-28px;
  margin-left:-170px\9;
  position: absolute;
  z-index:200;
  margin-top:15px;
  }
  @media screen and (-webkit-min-device-pixel-ratio:0) {
  #new_yui3_'.$date_i.'{
  position: absolute;
  z-index:200;
  margin-top:17px;
  }
  }
  </style>';
     $date_str .= '<div class="yui3-skin-sam yui3-g">'.tep_draw_input_field('payroll_date['.$date_i.']',($user_payroll_date_array[$groups_payroll_array['id']]['start'] != '' ? $user_payroll_date_array[$groups_payroll_array['id']]['start'] : ''),'size="10" onchange="date_change(this,'.$date_i.');"').'<input id="date_orders_'.$date_i.'" type="hidden" name="user_payroll_start_date['.$groups_payroll_array['id'].']" size="10" value="'.($user_payroll_date_array[$groups_payroll_array['id']]['start'] != '' ? $user_payroll_date_array[$groups_payroll_array['id']]['start'] : '').'">
                <div class="date_box">
                <a href="javascript:void(0);" onclick="open_new_calendar_num('.$date_i.');" class="dpicker"></a> 
                </div>
                <input type="hidden" id="date_order_'.$date_i.'" name="update_date" value="">
                <input type="hidden" name="toggle_open" value="0" id="toggle_open_'.$date_i.'"> 
                <div class="yui3-u" id="new_yui3_'.$date_i.'">
                <div id="mycalendar_'.$date_i.'"></div> 
                </div>
                </div></td>';
     $date_i++;
     $date_str .= '<style type="text/css">
  #new_yui3_'.$date_i.' {
  margin-left:-168px;
  *margin-left:-28px;
  margin-left:-170px\9;
  position: absolute;
  z-index:200;
  margin-top:15px;
  }
  @media screen and (-webkit-min-device-pixel-ratio:0) {
  #new_yui3_'.$date_i.'{
  position: absolute;
  z-index:200;
  margin-top:17px;
  }
  }
  </style>';
     $date_str .= '<td><div class="yui3-skin-sam yui3-g">'.tep_draw_input_field('payroll_date['.$date_i.']',($user_payroll_date_array[$groups_payroll_array['id']]['end'] != '' ? $user_payroll_date_array[$groups_payroll_array['id']]['end'] : ''),'size="10" onchange="date_change(this,'.$date_i.');"').'<input id="date_orders_'.$date_i.'" type="hidden" name="user_payroll_end_date['.$groups_payroll_array['id'].']" size="10" value="'.($user_payroll_date_array[$groups_payroll_array['id']]['end'] != '' ? $user_payroll_date_array[$groups_payroll_array['id']]['end'] : '').'">
                <div class="date_box">
                <a href="javascript:void(0);" onclick="open_new_calendar_num('.$date_i.');" class="dpicker"></a> 
                </div>
                <input type="hidden" id="date_order_'.$date_i.'" name="update_date" value="">
                <input type="hidden" name="toggle_open" value="0" id="toggle_open_'.$date_i.'"> 
                <div class="yui3-u" id="new_yui3_'.$date_i.'">
                <div id="mycalendar_'.$date_i.'"></div> 
                </div>
                </div></td>';
     $group_content_row_payroll = array(
        array('align' => 'left','params' => 'width="30%"', 'text' => $groups_payroll_array['title']), 
        array('align' => 'left','params' => 'width="70%"', 'text' => '<table width="100%" cellspacing="0" cellpadding="0" border="0"><tr><td><input type="text" name="user_payroll['.$groups_payroll_array['id'].']" value="'.($user_payroll_list_array[$groups_payroll_array['id']] != '' ? $user_payroll_list_array[$groups_payroll_array['id']] : '').'"></td><td>'.TEXT_PAYROLLS_EFFECTIVE_PERIOD.'</td>'.$date_str.'</tr></table>'), 
     );
     $group_content_table[] = array('text'=>$group_content_row_payroll);
     $groups_users_id[] = $groups_payroll_array['id'];
     $date_i++;
   }
   tep_db_free_result($groups_payroll_query);
 } 

 //备注
 $group_content_row_payroll = array();
 $group_content_row_payroll = array(
        array('align' => 'left','params' => 'width="30%"', 'text' => TEXT_PAYROLLS_CONTENTS), 
        array('align' => 'left','params' => 'width="70%"', 'text' => '<textarea name="payroll_contents" onfocus="o_submit_single = false;" onblur="o_submit_single = true;" style="resize: vertical;width:300px;height:42px;*height:40px;">'.$user_payroll_contents.'</textarea>'), 
     );
 $group_content_table[] = array('text'=>$group_content_row_payroll);

 //获取工资计算的开始日、结束日
 $payroll_date_array = tep_start_end_date($_POST['group_id'],$_POST['save_date']);
 //开始日
 $payroll_start_date = $payroll_date_array['start_date'];
 //结束日
 $payroll_end_date = $payroll_date_array['end_date'];

 $payroll_start_arr = explode('-',$payroll_start_date);
 $payroll_end_arr = explode('-',$payroll_end_date);

 //上班时间段日期列表
 $date_arr = array();
 $i=0;
 while(mktime(0,0,0,$payroll_start_arr[1],$payroll_start_arr[2]+$i,$payroll_start_arr[0]) != mktime(0,0,0,$payroll_end_arr[1],$payroll_end_arr[2],$payroll_end_arr[0])){
  $date_arr[] = date('Ymd',mktime(0,0,0,$payroll_start_arr[1],$payroll_start_arr[2]+$i,$payroll_start_arr[0]));
  $i++;
 }
 $date_arr[] = date('Ymd',mktime(0,0,0,$payroll_start_arr[1],$payroll_start_arr[2]+$i,$payroll_start_arr[0]));

 //所有排班列表


 $user = $_POST['user_id'];
 //要求出勤时间 不见算变更出勤
 $all_att_time = 0;
 //实际出勤时间 包括变更排班
 $all_real_time = 0;
 $attendance_list_arr = array();
 foreach($date_arr as $_date){
   //用户当天出勤信息
   $attendance_array = tep_all_attenande_by_uid($user,$_date);
   $real_time = tep_attendance_record_time($user,$_date,$attendance_array);
   $all_real_time += $real_time;
   foreach($attendance_array as $attendance_value){
     if($attendance_value['type']=='replace'){
       if(!isset($attendance_list_arr[$attendance_value['replace_attendance_detail_id']])){
         $attendance_list_arr[$attendance_value['replace_attendance_detail_id']]['time'] = 0;
         $attendance_list_arr[$attendance_value['replace_attendance_detail_id']]['real_time'] = 0;
       }
     }else{
       if(!isset($attendance_list_arr[$attendance_value['attendance_detail_id']])){
         $attendance_list_arr[$attendance_value['attendance_detail_id']]['time'] = 0;
         $attendance_list_arr[$attendance_value['attendance_detail_id']]['real_time'] = 0;
       }
     }
     $real_time_tmp = tep_attendance_record_time($user,$_date,$attendance_array,$attendance_value['attendance_detail_id']);
     if($attendance_value['type']=='replace'){
       $attendance_list_arr[$attendance_value['replace_attendance_detail_id']]['real_time'] += $real_time_tmp;
     }else{
       $attendance_list_arr[$attendance_value['attendance_detail_id']]['real_time'] += $real_time_tmp;
     }
     if($attendance_value['set_time']==0){
       $t_work_time = time_diff($attendance_value['work_start'],$attendance_value['work_end']);
       if($attendance_value['type'] == 'replace'||$attendance_value['rest_start']==$attendance_value['rest_end']){
         $t_rest_time = 0;
       }else{
         $t_rest_time = time_diff($attendance_value['rest_start'],$attendance_value['rest_end']);
       }
       $t_rwork_time = $t_work_time - $t_rest_time;
     }else{
       $t_rwork_time = $attendance_value['work_hours'] - $attendance_value['rest_hours'];
     }
     if($attendance_value['type']=='replace'){
       $attendance_list_arr[$attendance_value['replace_attendance_detail_id']]['time'] += $t_rwork_time;
     }else{
       $attendance_list_arr[$attendance_value['attendance_detail_id']]['time'] += $t_rwork_time;
     }
     $tmp_att_info = array();
     if($attendance_value['type']=='replace'){
       $tmp_att_info = tep_get_attendance_by_id($attendance_value['replace_attendance_detail_id']);
       if($tmp_att_info['set_time']==0){
         $w_time = time_diff($tmp_att_info['work_start'],$tmp_att_info['work_end']);
         if($tmp_att_info['rest_start'] == $tmp_att_info['rest_end']){
           $r_time = 0;
         }else{
           $r_time = time_diff($tmp_att_info['rest_start'],$tmp_att_info['rest_end']);
         }
         $all_att_time += $w_time - $r_time;
       }else{
         $all_att_time += $tmp_att_info['work_hours'] - $tmp_att_info['rest_hours'];
       }
     }else{
       $all_att_time += $t_rwork_time;
     }
   }
 }

 
 //总计时间
 $group_content_row_payroll = array();
 $group_content_row_payroll = array(
        array('align' => 'left','params' => 'width="30%"', 'text' => TEXT_PAYROLLS_DATE_TOTAL), 
        array('align' => 'left','params' => 'width="70%"', 'text' => $payroll_start_date.'～'.$payroll_end_date), 
     );
 $group_content_table[] = array('text'=>$group_content_row_payroll);



 foreach($attendance_list_arr as $a_key => $a_value){
   $a_info = tep_get_attendance_by_id($a_key);
   $group_content_row_payroll = array();
   $group_content_row_payroll = array(
          array('align' => 'left','params' => 'width="30%"', 'text' => $a_info['title']), 
          array('align' => 'left','params' => 'width="70%"', 'text' => TEXT_ATT_SET_VALUE .'&nbsp;&nbsp;'. $a_value['time'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'. TEXT_ATT_ACTUAL_VALUE .'&nbsp;&nbsp;'. $a_value['real_time']), 
       );
   $group_content_table[] = array('text'=>$group_content_row_payroll);
 }
 if($_POST['group_id'] > 0){
   //作成者、作成时间、更新者、更新时间
   $group_content_table[]['text'] = array(
        array('align' => 'left','params' => 'width="30%"', 'text' => TEXT_USER_ADDED.'&nbsp;'.((tep_not_null($show_tep_info[0]['create_user'])?$show_tep_info[0]['create_user']:TEXT_UNSET_DATA))), 
        array('align' => 'left','params' => 'width="70%"','text' => TEXT_DATE_ADDED.'&nbsp;'.((tep_not_null(tep_datetime_short($show_tep_info[0]['create_date'])))?tep_datetime_short($show_tep_info[0]['create_date']):TEXT_UNSET_DATA)), 
      );
  
   $group_content_table[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_USER_UPDATE.'&nbsp;'.((tep_not_null($show_tep_info[0]['update_user'])?$show_tep_info[0]['update_user']:TEXT_UNSET_DATA))), 
        array('align' => 'left', 'text' => TEXT_DATE_UPDATE.'&nbsp;'.((tep_not_null(tep_datetime_short($show_tep_info[0]['update_date'])))?tep_datetime_short($show_tep_info[0]['update_date']):TEXT_UNSET_DATA)), 
      ); 
 }
 $group_content_row_opt = array();
 $group_content_row_opt[] = array('params'=>'align="center" colspan="2"','text'=>'<input class="element_button" type="submit" value="'.IMAGE_SAVE.'">');
 $group_content_table[] = array('text'=>$group_content_row_opt);
 $notice_box->get_heading($heading);
 $notice_box->get_form($form_str);
 $notice_box->get_contents($group_content_table);
 echo $notice_box->show_notice();
} else if ($_GET['action'] == 'move_group') {
/* -----------------------------------------------------
    功能: 移动组信息的弹出框
    参数: $_GET['id'] 组id 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_GROUPS);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  //获取组的信息
  $group_query = tep_db_query("select * from ".TABLE_GROUPS." where id='".$_GET['id']."'");
  $group_array = tep_db_fetch_array($group_query);
  tep_db_free_result($group_query);
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_INFO_HEADING_MOVE_GROUP);
  $heading[] = array('align' => 'right', 'text' => '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>');

  $buttons = array();

  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_MOVE, 'onclick="toggle_group_form(\''.tep_href_link(FILENAME_GROUPS,tep_get_all_get_params(array('action'))).'\',\'move_group\');"').'</a>';
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="hidden_info_box();"').'</a>';

  $buttons = array('align' => 'center', 'button' => $button); 
  
  $move_category_info = array();
  
  $move_category_info[]['text'] = array(
        array('align' => 'left', 'text' => sprintf(TEXT_MOVE_GROUP_INTRO, $group_array['name'])), 
      );
  $move_category_info[]['text'] = array(
        array('align' => 'left', 'text' => sprintf(TEXT_MOVE, $group_array['name']).tep_draw_hidden_field('group_id', $_GET['id'])), 
      );
  $move_category_info[]['text'] = array(
        array('align' => 'left', 'text' => tep_draw_pull_down_menu('move_to_group_id', tep_get_group_tree(0,'',array(0=>array('id'=>0,'text'=>HEADER_TITLE_TOP)),(int)$group_array['id']), 0)), 
      );
 
  $form_str = tep_draw_form('move_group', FILENAME_GROUPS, 'action=move_group_confirm');
  
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($move_category_info, $buttons);
  $notice_box->get_eof(tep_eof_hidden());

  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'copy_group') {
/* -----------------------------------------------------
    功能: 复制组信息的弹出框
    参数: $_GET['id'] 组id 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_GROUPS);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  //获取组的信息
  $group_query = tep_db_query("select * from ".TABLE_GROUPS." where id='".$_GET['id']."'");
  $group_array = tep_db_fetch_array($group_query);
  tep_db_free_result($group_query);
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => IMAGE_COPY_TO);
  $heading[] = array('align' => 'right', 'text' => '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>');

  $buttons = array();

  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_COPY, 'onclick="toggle_group_form(\''.tep_href_link(FILENAME_GROUPS,tep_get_all_get_params(array('action'))).'\',\'copy_group\');"').'</a>';
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_CANCEL, 'onclick="hidden_info_box();"').'</a>';

  $buttons = array('align' => 'center', 'button' => $button); 
  
  $move_category_info = array();

  //获取组的父类名称 
  $parent_id_array = array();
  $parent_query = tep_db_query("select id,name from ".TABLE_GROUPS);
  while($parent_array = tep_db_fetch_array($parent_query)){

    $parent_id_array[$parent_array['id']] = $parent_array['name'];
  }
  tep_db_free_result($parent_query);
  group_parent_id_list($group_array['id'],$group_parent_id_list);
  $group_list_str = HEADER_TITLE_TOP;
  if(!empty($group_parent_id_list)){

    krsort($group_parent_id_list);
    foreach($group_parent_id_list as $group_id_value){

      $group_list_str .= '&nbsp;>&nbsp;'.$parent_id_array[$group_id_value];
    }
  }
  $group_list_str .= '&nbsp;>&nbsp;'.$group_array['name'];
  $move_category_info[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_CURRENT_GROUPS.tep_draw_hidden_field('group_id', $_GET['id'])), 
        array('align' => 'left', 'text' => $group_list_str), 
      ); 
  $move_category_info[]['text'] = array(
        array('align' => 'left', 'text' => IMAGE_COPY_TO), 
        array('align' => 'left', 'text' => tep_draw_pull_down_menu('copy_to_group_id', tep_get_group_tree(0,'',array(0=>array('id'=>0,'text'=>HEADER_TITLE_TOP)),(int)$group_array['id']), 0)), 
      );
 
  $form_str = tep_draw_form('copy_group', FILENAME_GROUPS, 'action=copy_group_confirm');
  
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($move_category_info, $buttons);
  $notice_box->get_eof(tep_eof_hidden());

  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'show_order_info') {
/* -----------------------------------------------------
    功能: 显示订单信息
    参数: $_POST['id']
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_ALERT_LOG);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
	 $order_sql="select * from ".TABLE_NOTICE." where from_notice=".$_POST['id']." and type=0";
	 $order_row=tep_db_fetch_array(tep_db_query($order_sql));
	 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
	 $order_id=$_POST['order_id'];
	 $page_str = '<a onclick="hidden_info_box()" href="javascript:void(0);">X</a>';
	 $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
	 $heading[] = array('text' => TEXT_ALERT_INFO);
	 $heading[] = array('align' => 'right', 'text' => '<span id="next_prev"></span>&nbsp&nbsp'.$page_str);
 
	 $order_content_table = array();
	 $order_content_row_type = array();
	 $order_content_row_type[] = array('params'=>'width="25%"','text'=>TABLE_HEADING_TYPE );
	 $order_content_row_type[] = array('text'=> TEXT_ORDER);
	 $order_content_row_type[] = array('params'=>'width="20%"','text'=>'');
	 $order_content_table[] = array('text'=> $order_content_row_type);

    
	 $order_content_row_text[] = array('text'=>TABLE_HEADING_BUTTON_NAME);
 	 $order_text_area =  '<div style="width: 100%;min-height:100px;display:block;word-break: break-all;word-wrap: break-word;">'.$order_row['title'].'&nbsp;&nbsp;&nbsp;&nbsp;'.$order_row['created_at'].'</div>';
	 $order_content_row_text[] = array('text'=> $order_text_area);
	 $order_content_row_text[] = array('params'=>'style="color:#FF0000;"');
	 $order_content_table[] = array('text'=> $order_content_row_text);
	 $order_content_row_author = array();
	 $order_content_row_author[] = array('text'=> TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.$order_row['user']);
	 $order_content_row_author[] = array('text'=> TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.(tep_not_null($order_row['created_at'])?$order_row['created_at']:TEXT_UNSET_DATA));
	 $order_content_table[] = array('text'=> $order_content_row_author);
	 $order_content_row_update = array();
	 $order_content_row_update[] = array('text'=> TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.$order_row['user']);
	 $order_content_row_update[] = array('text'=> TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.(tep_not_null($order_row['set_time'])?$order_row['set_time']:TEXT_UNSET_DATA));
         $order_content_table[] = array('text'=> $order_content_row_update);
         if($ocertify->npermission >= 15){
	   $order_content_row_submit[] = array('params' => 'colspan="2" align="center"','text'=> '<input type="button" onclick="delete_alert('.$_POST['id'].',0)"  value="'.IMAGE_DELETE.'">');
           $order_content_table[] = array('text'=> $order_content_row_submit);
         }
	 $notice_box->get_heading($heading);
	 $notice_box->get_form($form_str);
	 $notice_box->get_contents($order_content_table); 
	 echo $notice_box->show_notice();
} else if ($_GET['action'] == 'show_bulletin_info') {
/* -----------------------------------------------------
    功能: 显示告示板信息
    参数: $_POST['id']
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_ALERT_LOG);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
	 $bulletin_sql="select * from ".TABLE_BULLETIN_BOARD." where id=".$_POST['id'];
	 $bulletin_row=tep_db_fetch_array(tep_db_query($bulletin_sql));
	 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
	 $bulletin_id=$_POST['bulletin_id'];
	 $page_str = '<a onclick="hidden_info_box()" href="javascript:void(0);">X</a>';
	 $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
	 $heading[] = array('text' => TEXT_ALERT_INFO);
	 $heading[] = array('align' => 'right', 'text' => '<span id="next_prev"></span>&nbsp&nbsp'.$page_str);
 
	 $bulletin_content_table = array();
	 $bulletin_content_row_type = array();
	 $bulletin_content_row_type[] = array('params'=>'width="25%"','text'=> TABLE_HEADING_TYPE);
	 $bulletin_content_row_type[] = array('text'=> TEXT_BULLETIN_BOARD);
	 $bulletin_content_row_type[] = array('params'=>'width="20%"','text'=> '');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_type);


         $bulletin_row['title'] = str_replace(array('>','<',"\n"),array('&gt;','&lt;','<br>'),$bulletin_row['title']); 
         $bulletin_row['content'] = str_replace(array('>','<',"\n"),array('&gt;','&lt;','<br>'),$bulletin_row['content']);
	 $bulletin_content_row_text[] = array('params'=>'valign="top"','text'=> TABLE_HEADING_BUTTON_NAME);
 	 $bulletin_text_area =  '<div style="width: 100%;min-height:100px;display:block;word-break: break-all;word-wrap: break-word;">'.TEXT_TITLE_NOTE.'：'.$bulletin_row['title'].'<br>'.TEXT_COMMENT_NOTE.'：'.str_replace("\n","<br />",$bulletin_row['content']).'</div>';
	 $bulletin_content_row_text[] = array('text'=> $bulletin_text_area);
	 $bulletin_content_row_text[] = array('params'=>'style="color:#FF0000;"');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_text);
	 $bulletin_content_row_author = array();
	 $bulletin_content_row_author[] = array('text'=> TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.$bulletin_row['add_user']);
	 $bulletin_content_row_author[] = array('text'=> TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.(tep_not_null($bulletin_row['add_time'])?$bulletin_row['add_time']:TEXT_UNSET_DATA));
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_author);
	 $bulletin_content_row_update = array();
	 $bulletin_content_row_update[] = array('text'=> TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.$bulletin_row['update_user']);
	 $bulletin_content_row_update[] = array('text'=> TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.(tep_not_null($bulletin_row['update_time'])?$bulletin_row['update_time']:TEXT_UNSET_DATA));
         $bulletin_content_table[] = array('text'=> $bulletin_content_row_update);
         if($ocertify->npermission >= 15){
	   $bulletin_content_row_submit[] = array('params' => 'colspan="2" align="center"','text'=> '<input type="button" onclick="delete_alert('.$_POST['id'].',1)"  value="'.IMAGE_DELETE.'">');
           $bulletin_content_table[] = array('text'=> $bulletin_content_row_submit);
         }
	 $notice_box->get_heading($heading);
	 $notice_box->get_form($form_str);
	 $notice_box->get_contents($bulletin_content_table); 
	 echo $notice_box->show_notice();
} else if ($_GET['action'] == 'show_bulletin_reply_info') {
/* -----------------------------------------------------
    功能: 显示告示板信息
    参数: $_POST['id']
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_ALERT_LOG);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
	 $reply_sql="select * from ".TABLE_BULLETIN_BOARD_REPLY." where id=".$_POST['id'];
	 $reply_row=tep_db_fetch_array(tep_db_query($reply_sql));
	 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
	 $reply_id=$_POST['reply_id'];
	 $page_str = '<a onclick="hidden_info_box()" href="javascript:void(0);">X</a>';
	 $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
	 $heading[] = array('text' => TEXT_ALERT_INFO);
	 $heading[] = array('align' => 'right', 'text' => '<span id="next_prev"></span>&nbsp&nbsp'.$page_str);
 
	 $reply_content_table = array();
	 $reply_content_row_type = array();
	 $reply_content_row_type[] = array('params'=>'width="25%"','text'=> TABLE_HEADING_TYPE);
	 $reply_content_row_type[] = array('text'=> TEXT_BULLETIN_BOARD);
	 $reply_content_row_type[] = array('params'=>'width="20%"','text'=> ' ');
	 $reply_content_table[] = array('text'=> $reply_content_row_type);


         $reply_row['content'] = str_replace(array('>','<',"\n"),array('&gt;','&lt;','<br>'),$reply_row['content']); 
	 $reply_content_row_text[] = array('params'=>'valign="top"','text'=>TABLE_HEADING_BUTTON_NAME);
 	 $reply_text_area =  '<div style="width: 100%; min-height:100px;display:block;word-break: break-all;word-wrap: break-word;">'.str_replace("\n","<br />",$reply_row['content']).'</div>';
	 $reply_content_row_text[] = array('text'=> $reply_text_area);
	 $reply_content_row_text[] = array('params'=>'style="color:#FF0000;"');
	 $reply_content_table[] = array('text'=> $reply_content_row_text);
	 $reply_content_row_author = array();
	 $reply_row['add_user']==''?TEXT_UNSET_DATA:$reply_row['add_user'];
	 $reply_content_row_author[] = array('text'=> TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.$reply_row['add_user']);
	 $reply_content_row_author[] = array('text'=> TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.(tep_not_null($reply_row['add_time'])?$reply_row['add_time']:TEXT_UNSET_DATA));
	 $reply_content_table[] = array('text'=> $reply_content_row_author);
	 $reply_content_row_update = array();
	 $reply_content_row_update[] = array('text'=> TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.$reply_row['update_user']);
	 $reply_content_row_update[] = array('text'=> TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.(tep_not_null($reply_row['update_time'])?$reply_row['update_time']:TEXT_UNSET_DATA));
         $reply_content_table[] = array('text'=> $reply_content_row_update);
         if($ocertify->npermission >= 15){
	   $reply_content_row_submit[] = array('params' => 'colspan="2" align="center"','text'=> '<input type="button" onclick="delete_alert('.$_POST['id'].',2)"  value="'.IMAGE_DELETE.'">');
           $reply_content_table[] = array('text'=> $reply_content_row_submit);
         }
	 $notice_box->get_heading($heading);
	 $notice_box->get_form($form_str);
	 $notice_box->get_contents($reply_content_table); 
	 echo $notice_box->show_notice();
} else if ($_GET['action'] == 'show_messages_info') {
/* -----------------------------------------------------
    功能: 显示messages信息
    参数: $_POST['id']
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.'alert_log.php');
  include(DIR_FS_ADMIN.'classes/notice_box.php');
	 $messages_sql="select * from messages  where id=".$_POST['id'];
	 $messages_row=tep_db_fetch_array(tep_db_query($messages_sql));
	 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
	 $messages_id=$_POST['messages_id'];
	 $page_str = '<a onclick="hidden_info_box()" href="javascript:void(0);">X</a>';
	 $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
	 $heading[] = array('text' => TEXT_ALERT_INFO);
	 $heading[] = array('align' => 'right', 'text' => '<span id="next_prev"></span>&nbsp&nbsp'.$page_str);
 
	 $messages_content_table = array();
	 $messages_content_row_type = array();
	 $messages_content_row_type[] = array('params'=>'width="25%"','text'=> TABLE_HEADING_TYPE);
	 $messages_content_row_type[] = array('text'=> MESSAGES_PAGE_LINK_NAME);
	 $messages_content_row_type[] = array('params'=>'width="20%"','text'=> ' ');
	 $messages_content_table[] = array('text'=> $messages_content_row_type);
	 $messages_content_row_text[] = array('params'=>'valign="top"','text'=> TABLE_HEADING_BUTTON_NAME);
 	 $messages_text_area =  '<div style="width: 100%;min-height:100px;display:block;word-break: break-all;word-wrap: break-word;">'.nl2br($messages_row['content']).'</div>';
	 $messages_content_row_text[] = array('text'=> $messages_text_area);
	 $messages_content_row_text[] = array('params'=>'style="color:#FF0000;"');
	 $messages_content_table[] = array('text'=> $messages_content_row_text);
	 $messages_content_row_author = array();
	 $messages_time=tep_not_null($messages_row['time'])?$messages_row['time']:TEXT_UNSET_DATA;
	 if($messages_time=='0000-00-00 00:00:00')$messages_time=TEXT_UNSET_DATA;
	 if($messages_row['sender_name']=='')$messages_row['sender_name']=TEXT_UNSET_DATA;
	 $messages_content_row_author[] = array('text'=> TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.$messages_row['sender_name']);
	 $messages_content_row_author[] = array('text'=> TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.$messages_time);
	 $messages_content_table[] = array('text'=> $messages_content_row_author);
	 $messages_content_row_update = array();
	 $user_name=$messages_row['user_update'];
	 //if(!$user_name)$user_name=TEXT_UNSET_DATA;
//	 $messages_time=$messages_row['date_update'];
	 if($messages_time=='0000-00-00 00:00:00')$messages_time=TEXT_UNSET_DATA;
	 $messages_content_row_update[] = array('text'=> TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.$messages_row['sender_name']);
	 $messages_content_row_update[] = array('text'=> TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.$messages_time);
         $messages_content_table[] = array('text'=> $messages_content_row_update);
         if($ocertify->npermission >= 15){
	   $messages_content_row_submit[] = array('params' => 'colspan="2" align="center"','text'=> '<input type="button" onclick="delete_alert('.$_POST['id'].',3)"  value="'.IMAGE_DELETE.'">');
           $messages_content_table[] = array('text'=> $messages_content_row_submit);
         }
	 $notice_box->get_heading($heading);
	 $notice_box->get_form($form_str);
	 $notice_box->get_contents($messages_content_table); 
	 echo $notice_box->show_notice();
}else if($_GET['action'] == 'edit_user_attendance_info'){


  $_GET['date'] = $_GET['date'] == '' ? date('Ymd') : $_GET['date'];
  $_GET['uid'] = $_GET['uid'] == '' ? $ocertify->auth_user : $_GET['uid'];
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_ROSTER_RECORDS);
  //获得 所有排班表
  $attendance_detail_list = array();
  $attendance_detail_sql = "select * from ".TABLE_ATTENDANCE_DETAIL;
  $attendance_detail_query = tep_db_query($attendance_detail_sql);
  while($attendance_detail_row = tep_db_fetch_array($attendance_detail_query)){
    $attendance_detail_list[] = $attendance_detail_row;
  }
  //获得所有用户
  $all_user = array();
  $operator = $ocertify->auth_user;
  $show_user_id_list = array();
  if($ocertify->npermission >= '15'||tep_is_group_manager($ocertify->auth_user)){
    //如果权限小于等于10，并且是组长的情况
    $group_user_array = array($ocertify->auth_user);
    $groups_id_list_array = array();
    if($ocertify->npermission <= 10 && tep_is_group_manager($ocertify->auth_user)){

      $groups_id_list_array = tep_is_group_manager($ocertify->auth_user,true);
      if(count($groups_id_list_array) > 1){
        $groups_query = tep_db_query("select all_users_id from ".TABLE_GROUPS." where id in (".implode(',',$groups_id_list_array).")");
      }else{
        $groups_query = tep_db_query("select all_users_id from ".TABLE_GROUPS." where id='".$groups_id_list_array[0]."'");
      }
      while($groups_array = tep_db_fetch_array($groups_query)){

        $group_user_temp_array = explode('|||',$groups_array['all_users_id']);
        foreach($group_user_temp_array as $value){

          $group_user_array[] = $value;
        }
      }
      tep_db_free_result($groups_query);
    }
    $group_user_array = array_unique($group_user_array);
	  //选中的
	$sql_all_check_user = "select user_id as userid from ".TABLE_ATTENDANCE_GROUP_SHOW." where operator_id='". $operator ."' and is_select=1";
    $query_all_check_user = tep_db_query($sql_all_check_user);
    while($row_all_check_user = tep_db_fetch_array($query_all_check_user)){
      $all_check_user[] = $row_all_check_user;
      $show_user_id_list[] = $row_all_check_user['userid'];
    }
	//全部的
    $sql_all_user = 'select u.*, p.permission from ' . TABLE_USERS . ' u, ' .  TABLE_PERMISSIONS . " p where u.userid = p.userid and u.status=1 order by u.name asc"; 
    $query_all_user = tep_db_query($sql_all_user);
    while($row_all_user = tep_db_fetch_array($query_all_user)){
      //如果权限小于等于10，并且是组长的情况
      if($ocertify->npermission <= 10 && tep_is_group_manager($ocertify->auth_user)){
        if(!in_array($row_all_user['userid'],$group_user_array)){

          continue;
        }
      }
		//如果没有操作过的用户
      if(in_array($row_all_user['userid'],$show_user_id_list) && tep_db_num_rows($query_all_check_user)>0){
        $all_user[] = $row_all_user;
	    $all_user = array_intersect($all_user,$all_check_user);
	  }else{
        $show_user_id_list[] = $row_all_user['userid'];
        $all_user[] = $row_all_user;
	  
	  }
    }
  }else{

    $show_user_id_list[] = $operator;
  }
  //获得所有循环方式
  $type_list = array(
     TEXT_CALENDAR_REPEAT_TYPE_NO,
	 TEXT_CALENDAR_REPEAT_TYPE_EVERY_WEEK,
     TEXT_CALENDAR_REPEAT_TYPE_WEEK,
     TEXT_CALENDAR_REPEAT_TYPE_MONTH,
     TEXT_CALENDAR_REPEAT_TYPE_MONTH_WEEK,
     TEXT_CALENDAR_REPEAT_TYPE_YEAR
      );
  //判断管理员修改
  if(!tep_is_group_manager($ocertify->auth_user)){
    $disabled =  ' disabled="disabled" ';
  }

  include(DIR_FS_ADMIN.'classes/notice_box.php'); 
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

 
  //头部内容
  $heading = array();
  $date_str = TEXT_ATTENDANCE_SETTING_USER;
  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');

  /*
  获得 应该在浮动IDV 里显示的数据 管理员显示 可用用户
  当前用户显示当前用过显示的那个
  */
  if(isset($_GET['u_att_id'])&&$_GET['u_att_id']!=''){
    $attendance_dd_arr = tep_get_attendance_user($_GET['date'],0,false,0,$_GET['u_att_id']);
    $self_user = tep_get_user_info($_GET['uid']);
    if($ocertify->npermission<15){
      foreach($attendance_dd_arr as $u_att){
        $all_user[] = tep_get_user_info($u_att['user_id']);
      }
    }
    $date_str .= '&nbsp;&nbsp;'.$self_user['name'];
  }else{
    $attendance_dd_arr = array();
    if($_GET['index'] == ''){
      foreach($all_user as $user_info){
        $attendance_dd_arr = array_merge($attendance_dd_arr,tep_get_attendance_user($_GET['date'],$user_info['userid']));
      }
    }else{
      $attendance_dd_arr = array_merge($attendance_dd_arr,tep_get_attendance_user($_GET['date'],$_GET['uid']));
    }
  }

  //处理数据排序
  $attendance_temp_arr = array();
  foreach($attendance_dd_arr as $attendance_temp){
     $attendance_temp_arr[$attendance_temp['id']] = $attendance_temp;
   }
  krsort($attendance_temp_arr);
  $attendance_dd_arr = $attendance_temp_arr;

  //默认的排班模板
  $adl_select = '<select onchange="auto_add_attendance(this);" '.$disabled.' name="attendance_id[]" '.$show_only.' >';
  $adl_select .= '<option value="">--</option>';
  foreach($attendance_detail_list as $a_value){
    $adl_select .= '<option value="'.$a_value['id'].'">'.$a_value['title'].'</option>';
  }
  $adl_select .= '</select><input type="hidden" value="" class="tep_index_num">&nbsp;&nbsp;<font color="red">'.TEXT_REMIND_CHOICE_SELECT.'</font>';

  //默认的用户显示
  $user_select = '<select id="user_default" name="user[1][]" '.$disabled.'>';
  $user_select .= '<option value="">--</option>';
  $hidden_user_select = '<select name="user[1][]" id="user_default" '.$disabled.' onchange="auto_add_user(this,\'\');">';
  $hidden_user_select .= '<option value="">--</option>';
  $user_select_hidden = '';
  $default_hidden = '';
  foreach($all_user as $user){
    if($default_hidden == ''){
      $default_hidden = '<input type="hidden" name="user_hidden[]" value="'.$user['userid'].'">';
    }
    $user_select .= '<option value="'.$user['userid'].'"';
    $hidden_user_select .= '<option value="'.$user['userid'].'"';
    if($user['userid']==$_GET['uid']){
		/*
      $user_select .= ' selected ';
      $hidden_user_select .= ' selected ';
		 */
      $user_select_hidden = '<input type="hidden" name="user_hidden[]" value="'.$user['user'].'">';
    }
    $user_select .= ($user['userid']==$_GET['uid'] ? ' selected' :'').'>'.$user['name'].'</oprion>';
    if($_GET['u_att_id'] == ''){

      $hidden_user_select .= $user['userid']==$_GET['uid'] ? ' selected' :'';
    }
    $hidden_user_select .= '>'.$user['name'].'</oprion>';
  }
  $user_select .= '</select></select><input type="hidden" value="1" class="tep_index_num">&nbsp;&nbsp;<font color="red">'.TEXT_REMIND_CHOICE_SELECT.'</font><td><input disabled="disabled" style="opacity:0;" type="button" value="'.TEXT_DEL_ADL.'"></td>';
  $hidden_user_select .= '</select><input type="hidden" value="1" class="tep_index_num">&nbsp;&nbsp;<font color="red">'.TEXT_REMIND_CHOICE_SELECT.'</font><td><input disabled="disabled" style="opacity:0;" type="button" value="'.TEXT_DEL_ADL.'"></td>';


  //追加个人
  $hidden_user_add = '<select id="user_tep" onchange="auto_add_user(this,\'\');">';
  $hidden_user_add .= '<option value="">--</option>';
  foreach($all_user as $user){
    $hidden_user_add .= '<option value="'.$user['userid'].'">'.$user['name'].'</oprion>';
  }
  $hidden_user_add .= '</select><input type="hidden" value="1" class="tep_index_num">&nbsp;&nbsp;<font color="red">'.TEXT_REMIND_CHOICE_SELECT.'</font><td><input  '.$disabled.' type="button" onclick="del_as_user(this)" value="'.TEXT_DEL_ADL.'"></td>';

  //循环模式
  $type_select = '<select name="type[]" '.$disabled.' onchange="edit_space_nums(this,this.value);">';
  foreach($type_list as $t_key => $t_value){
	  if($t_key==1){
	    $t_key =9;
	  }
	  if($t_key>1){
	    $t_key = $t_key-1;
	  }
    $type_select .= '<option value="'.$t_key.'">'.$t_value.'</option>';
  }
  $type_select .= '</select><span class="space" style="display:none" >'.TEXT_CALENDAR_REPEAT_TYPE_WEEK_HEAD.'<input class="limit_input_width" type="text" name="space[]" value="1" '.$disabled.' onkeyup="if(this.value!=\'\'){if(!/^[1-9]{1}[0-9]{0,1}$/.test(this.value)){this.value=\'1\'}}">'.TEXT_CALENDAR_REPEAT_TYPE_WEEK_TAIL.'</span>';


  $hidden_div = '<div style="display:none">';
  $hidden_div .= '<table id="add_source">';
  $hidden_div .= '<tr><td class="tr_#line_num" width="30%" nowrap="nowrap" align="left">'.TEXT_ATTENDANCE_PLAN.'#line_num_1</td><td nowrap="nowrap" align="left" >&nbsp;</td></tr><td class="tr_#line_num" width="30%" nowrap="nowrap" align="left">'.TEXT_SELECT_USER.'</td><td nowrap="nowrap" align="left" >'.$hidden_user_select.'</td></tr><tr><td class="tr_#line_num" width="30%" nowrap="nowrap" align="left">'.TEXT_ADL_SELECT.'</td><td nowrap="nowrap" align="left">'.$adl_select.'</td><td nowrap="nowrap" align="left"></td></tr><tr><td class="tr_#line_num" width="30%" nowrap="nowrap" align="left">'.TEXT_TYPE_SELECT.'</td><td nowrap="nowrap" align="left" colspan="2">'.$type_select.'</td></tr><tr><td class="tr_#line_num"></td><td></td><td><input type="hidden" name="line_num_array[]" value="#line_num"><input type="button" value="'.TEXT_DEL_ADL.'" onclick="del_as_group(\'#line_num\',this,\'temp_del_group_id\')"></td></tr><tr><td class="tr_#line_num" nowrap="nowrap" align="left" colspan="3">------------------------------------------------------------------------</td></tr>';
  $hidden_div .= '</table></div>';
  $hidden_date .= '<input id="get_att_date" type="hidden" name="get_date" value="'.$_GET['date'].'">';
  $hidden_date .= '<div id="tep_data" style="display:none;"><input type="button" value="'.TEXT_DEL_ADL.'" onclick="del_as_group(\'\',this,\'1\')"></div>';
  $hidden_date .= '<span id="add_user_group" style="display:none;"></span>';


  //追加个人
  $hidden_tep = '<div style="display:none">';
  $hidden_tep .= '<table id="add_person">';
  $hidden_tep .= '<tr><td class="tr_#line_num" nowrap="nowrap" align="left">'.TEXT_SELECT_USER.'</td><td>'.$hidden_user_add.'</td></tr>';
  $hidden_tep .= '</table></div>';

  if($group_disabled!=''){
    $hidden_date .= '<input type="hidden" name="default_uid" value="'.$_GET['uid'].'">';
  }

  $heading[] = array('align' => 'left', 'text' => $date_str);
  $heading[] = array('align' => 'right', 'text' => $page_str.$hidden_div.$hidden_tep);

  //主体内容
  $as_info_row = array();
  //选择日期
  $date_select_str = '';
  $date_select_str .= '<input type="text" name="select_date_text" onchange="change_fetch_date(this.value,\'user\');" value="'.date('Y/m/d',strtotime($_GET['date'])).'">';
  /*
  $date_select_str .= '<select name="select_year" id="fetch_year" onchange="change_fetch_date(\'user\');">';
  for($i=2007;$i<=date('Y')+10;$i++){
    $select_year_selected = $i == date('Y',strtotime($_GET['date'])) ? ' selected' : '';
    $date_select_str .= '<option value="'.$i.'"'.$select_year_selected.'>'.$i.'</option>';
  }
  $date_select_str .= '</select>'.YEAR_TEXT;
  $date_select_str .= '<select name="select_month" id="fetch_month" onchange="change_fetch_date(\'user\');">';
  for($i=1;$i<=12;$i++){
    $select_year_selected = $i == date('m',strtotime($_GET['date'])) ? ' selected' : '';
    $i_str = $i < 10 ? '0'.$i : $i;
    $date_select_str .= '<option value="'.$i_str.'"'.$select_year_selected.'>'.$i_str.'</option>';
  }
  $date_select_str .= '</select>'.MONTH_TEXT;
  $date_select_str .= '<select name="select_day" id="fetch_day" onchange="change_fetch_date(\'user\');">';
  for($i=1;$i<=31;$i++){
    $i_str = $i < 10 ? '0'.$i : $i;
    $select_year_selected = $i == date('d',strtotime($_GET['date'])) ? ' selected' : '';
    $date_select_str .= '<option value="'.$i_str.'"'.$select_year_selected.'>'.$i_str.'</option>';
  }
  $date_select_str .= '</select>'.DAY_TEXT;
   */
  $date_click_str = '';
  
  $date_click_str .= '<div class="yui3-skin-sam yui3-g"><input id="date_orders" type="hidden" name="" size="10" value="'.date('Y-m-d',strtotime($_GET['date'])).'">
                <div class="date_box">
                <a href="javascript:void(0);" onclick="open_new_calendar(\'user\');" class="dpicker"></a> 
                </div>
                <input type="hidden" id="date_order" name="update_date" value="">
                <input type="hidden" name="toggle_open" value="0" id="toggle_open"> 
                <div class="yui3-u" id="new_yui3">
                <div id="mycalendar"></div> 
                </div>
                </div></td>';
  $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'id="add_start" nowrap="nowrap"', 'text' => TEXT_ATTENDANCE_DATE), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<table cellspacing="0" cellpadding="0" border="0"><tr><td>'.$date_select_str.'</td><td align="left">'.$date_click_str.'</td></tr></table>')
      );
  $show_arr = true;
  $attendane_temp_user_list_arr = array();
  foreach($attendance_dd_arr as $k=>$val) {
	$kkey = $val['u_group'];
	if(!isset($attendane_temp_user_list_arr[$kkey])){
	  $attendane_temp_user_list_arr[$kkey] = array();
	}
    $attendane_temp_user_list_arr[$kkey][]=$val;
  }
  $line_i = 0;
  $line_count = count($attendane_temp_user_list_arr);

  //没有个人排班的时候显示新数据 
  $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_count.'" nowrap="nowrap"', 'text' => TEXT_ATTENDANCE_PLAN.($line_count != 0 ? $line_count+1 : '')), 
        array('align' => 'left', 'params' => '"nowrap="nowrap"', 'text' => '')
      ); 
  
    $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_count.'" nowrap="nowrap"', 'text' => TEXT_SELECT_USER), 
        array('align' => 'left', 'params' => '"nowrap="nowrap"', 'text' => $hidden_user_select)
      );
    $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_count.'" nowrap="nowrap"', 'text' => TEXT_ADL_SELECT), 
        array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => $adl_select),
        array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '')
      );
    $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_count.'" width="30%" nowrap="nowrap"', 'text' => TEXT_TYPE_SELECT), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $type_select),
      );
  $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_count.'" width="30%" nowrap="nowrap"', 'text' => ''), 
        array('text'=>''), 
        array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input type="hidden" name="line_num_array[]" value="'.$line_count.'"><input type="button" '.$disabled.' value="'.TEXT_DEL_ADL.'" onclick="del_as_group(\''.$line_count.'\',this,\'\')">')
      );
   if($line_count>0){
    $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_count.'" nowrap="nowrap" colspan="3"', 'text' => '------------------------------------------------------------------------') 
      );
  }

  foreach($attendane_temp_user_list_arr as $a_info){

    if($a_info[0]['end_date'] != 0 && $a_info[0]['type'] != 0 && $a_info[0]['end_date'] <= $_GET['date']){

      continue; 
    }  
    $has_adl_select = '<select onchange="auto_add_attendance(this);" name="has_attendance_id[]" '.$disabled.' >';
    $has_adl_select .= '<option value="">--</option>';
    foreach($attendance_detail_list as $a_value){
      $has_adl_select .= '<option value="'.$a_value['id'].'"';
      if($a_info[0]['attendance_detail_id'] == $a_value['id']){
        $has_adl_select .= ' selected ';
      }
      $has_adl_select .=' >'.$a_value['title'].'</option>';
    }
    $has_adl_select .= '</select>';

    $as_info_row_tmp = array(); 
    $as_info_row_tmp[] = array('align' => 'left', 'params' => 'class="tr_'.$line_i.'" nowrap="nowrap"', 'text' => TEXT_ADL_SELECT);
    $as_info_row_tmp[] = array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => $has_adl_select.'<input type="hidden" name="u_group[]" value="'.$a_info[0]['u_group'].'"><input type="hidden" name="type_array[]" value="'.$a_info['type'].'">');
    if($show_arr){
      $as_user_added = $a_info[0]['add_user'];
      $as_date_added = $a_info[0]['add_time'];
      $as_user_update = $a_info[0]['update_user'];
      $as_last_modified = $a_info[0]['update_time'];
    }

  $temp_as_info_row = array();
  $show_user = true;
  $temp_count = 0;
  $show_flag = true;
  for($j=0; $j<count($a_info);$j++){
    $has_user_select = '<select name="has_user['.$a_info[$j]["u_group"].'][]" '.$disabled.' style="max-width:280px;">';
    $has_user_select .= '<option value="">--</option>';
    $has_user_select_hidden = '';
    $default_has_user = '';
    if(!in_array($a_info[$j]['user_id'],$show_user_id_list)){
      $temp_count++;
      $show_flag = false;
      continue;
    }
    foreach($all_user as $user){
      if($default_has_user == ''){
      $default_has_user = '<input type="hidden" name="has_user_hidden[]" value="'.$user['userid'].'">';
      }
      $has_user_select .= '<option value="'.$user['userid'].'" ';
      if($a_info[$j]['user_id'] == $user['userid']){
        $has_user_select .= 'selected ';
        $has_user_select_hidden = '<input type="hidden" name="has_user_hidden[]" value="'.$user['userid'].'">';
      }
      $has_user_select .= ' >'.$user['name'].'</option>';
    }
	if($j!=0){
	    //隐藏除第一个以外的add按钮
	  $style_hidden_add = 'style="display:none;"';
	    //隐藏第一个删除按钮
	  $style_hidden_del = 'style="display:block inline;"';
	}else{
	  $style_hidden_add = 'style="display:block inline;"';
	  $style_hidden_del = 'style="opacity:0;"'.'disabled="disabled"';
	}
    $has_user_select .= '</select><input type="hidden" name="data_as['.$a_info[$j]['u_group'].'][]" value="'.$a_info[$j]['id'].'"><td nowrap="nowrap"><input '.$style_hidden_del.' type="button" value="'.TEXT_DEL_ADL.'" onclick="del_as_user(this,\''.$a_info[$j]['id'].'\',false)"></td>';
    $temp_as_info_row[]['text'] = array(
      array('align' => 'left', 'params' => 'class="tr_'.$line_i.'" nowrap="nowrap"', 'text' => TEXT_SELECT_USER), 
      array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => $has_user_select)
    );


  }

  if($show_flag == true){

    if($line_i != 0){

      $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.($line_i-1).'" nowrap="nowrap"  colspan="3"', 'text' => '------------------------------------------------------------------------') 
      );     
    }
    $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_i.'" nowrap="nowrap"', 'text' => TEXT_ATTENDANCE_PLAN.($line_count-$line_i != 1 ? $line_count-$line_i : '')), 
        array('align' => 'left', 'params' => '"nowrap="nowrap"', 'text' => '')
      );
  }

  $hidden_users_select = '<select name="has_user[new]['.$a_info[0]["u_group"].'][]" id="user_default" '.$disabled.' onchange="auto_add_user(this,\''.$a_info[0]['u_group'].'\','.($line_i+1).');">';
  $hidden_users_select .= '<option value="">--</option>';
  $user_select_hidden = '';
  $default_hidden = '';
  foreach($all_user as $user){
    if($default_hidden == ''){
      $default_hidden = '<input type="hidden" name="user_hidden[]" value="'.$user['userid'].'">';
    }
    $user_select .= '<option value="'.$user['userid'].'"';
    $hidden_users_select .= '<option value="'.$user['userid'].'"';
    if($user['userid']==$_GET['uid']){
		/*
      $user_select .= ' selected ';
      $hidden_users_select .= ' selected ';
		 */
      $user_select_hidden = '<input type="hidden" name="user_hidden[]" value="'.$user['user'].'">';
    }
    $user_select .= ($user['userid']==$_GET['uid'] ? ' selected' :'').'>'.$user['name'].'</oprion>';
    $hidden_users_select .= '>'.$user['name'].'</oprion>';
  }
  $user_select .= '</select></select><input type="hidden" value="1" class="tep_index_num">&nbsp;&nbsp;<font color="red">'.TEXT_REMIND_CHOICE_SELECT.'</font><td><input disabled="disabled" style="opacity:0;" type="button" value="'.TEXT_DEL_ADL.'"></td>';
  $hidden_users_select .= '</select><input type="hidden" value="1" class="tep_index_num">&nbsp;&nbsp;<font color="red">'.TEXT_REMIND_CHOICE_SELECT.'</font><td><input disabled="disabled" style="opacity:0;" type="button" value="'.TEXT_DEL_ADL.'"></td>';
  $temp_as_info_row[]['text'] = array(
      array('align' => 'left', 'params' => 'class="tr_'.$line_i.'" nowrap="nowrap"', 'text' => TEXT_SELECT_USER), 
      array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => $hidden_users_select)
    );

    if($j==$temp_count){
      continue;
    }
    $show_arr = false;
    $as_info_row = array_merge($as_info_row,$temp_as_info_row);
    if($disabled){
      if($has_user_select_hidden!=''){
        $has_user_select .= $has_user_select_hidden;
      }else{
        $has_user_select .= $default_has_user;
      }
    }
    $as_info_row[]['text'] = $as_info_row_tmp;
    $has_type_select = '<select name="has_type[]" '.$disabled.'  onchange="edit_space_nums(this,this.value);" >';
    foreach($type_list as $t_key => $t_value){
	   if($t_key==1){
	     $t_key =9;
	   }
		if($t_key>1){
		  $t_key = $t_key-1;
		}
      $has_type_select .= '<option value="'.$t_key.'" ';
	 //判断是不是每周
	  if($a_info[0]['type']==1 && $a_info[0]['space']==0){
		   $a_info[0]['type']=8;
	  }
      if($a_info[0]['type'] == $t_key){
        $has_type_select .= ' selected ';
      }
      $has_type_select .= ' >'.$t_value.'</option>';
    }
	//隔周
    $style_space=($a_info[0]['type']==1 && $a_info[0]['space']!=0)?'':'style="display:none"';

    if($a_info[0]['space']==0 || $a_info[0]['space']==''){
	    $a_info[0]['space']=1;
	  }
	$has_type_select .= '</select><span class="space" '.$style_space.' >'.TEXT_CALENDAR_REPEAT_TYPE_WEEK_HEAD.'<input class="limit_input_width" type="text" name="has_space[]" value='.$a_info[0]['space'].' '.$disabled.' onkeyup="if(this.value!=\'\'){if(!/^[1-9]{1}[0-9]{0,1}$/.test(this.value)){this.value=\'1\'}}">'.TEXT_CALENDAR_REPEAT_TYPE_WEEK_TAIL.'</span>';

    $as_info_row[]['text'] = array(
      array('align' => 'left', 'params' => 'class="tr_'.$line_i.'" width="30%" nowrap="nowrap"', 'text' => TEXT_TYPE_SELECT), 
      array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $has_type_select),
    );
    $as_info_row[]['text'] = array(
      array('params' => 'class="tr_'.$line_i.'" width="30%" nowrap="nowrap"','text'=>''),
      array('text'=>''),
      array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input type="hidden" name="line_num_array[]" value="'.$line_i.'"><input  '.$disabled.' type="button" onclick="del_as_group(\''.$line_i.'\',this,\''.$a_info[0]['u_group'].'\',false,\''.$ocertify->npermission.'\')" value="'.TEXT_DEL_ADL.'">'.($a_info[0]['type'] != 0 && $_GET['date'] >= date('Ymd') ? '<input '.$disabled.' type="button" value="'.TEXT_ATTENDANCE_DATE_END.'" onclick="end_date(\'user\',\''.$a_info[0]['u_group'].'\',\''.$_GET['date'].'\');">' : ''))
    );
  $line_i++;
  }
		  
    $as_info_row[]['text'] = array(array('text'=>'&nbsp;'));

  //更新日创建日 更新时间 创建时间
  $as_info_row[] = array('params'=> 'id="add_end"','text' => array(
        array('align' => 'left','params'=>'style="padding:0;margin:0;"', 'text' => '<input type="hidden" id="line_num" value="'.($line_i+1).'">'.$hidden_date.TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($as_user_added)?$as_user_added:TEXT_UNSET_DATA)),
        array('align' => 'left', 'params'=>'style="padding:0;margin:0;"','text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($as_date_added)?$as_date_added:TEXT_UNSET_DATA))
      ));
  $as_info_row[]['text'] = array(
        array('align' => 'left','params'=>'style="padding:0;margin:0;"', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($as_user_update)?$as_user_update:TEXT_UNSET_DATA)),
        array('align' => 'left', 'params'=>'style="padding:0;margin:0;"', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($as_last_modified)?$as_last_modified:TEXT_UNSET_DATA))
      );


  //底部内容
  $buttons = array();
  
  //点排班个人单个排班
  if(($ocertify->npermission>10||tep_is_group_manager($ocertify->auth_user))){
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(TEXT_ATTENDANCE_SETTING_GROUP, ' onclick="show_group_attendance_info(\'\',\''.$_GET['date'].'\', \''.  $_GET['index'].'\',\''.$_GET['group_id'].'\',\''.$_GET['u_att_id'].'\',\''.$_GET['uid'].'\')"').'</a>'; 
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(TEXT_ATTENDANCE_SETTING_CHANGE, 'onclick="show_replace_attendance_info(\'\',\''.$_GET['date'].'\',\''.$_GET['index'].'\',\''.$_GET['uid'].'\',\''.$_GET['u_att_id'].'\',\''.$_GET['group_id'].'\')"').'</a>'; 
  }
  
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE,$disabled.'id="button_delete" onclick="delete_submit(\''.$ocertify->npermission.'\',\'user\');"').'</a>'; 

  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, $disabled.'id="button_save" onclick="save_submit(\''.$ocertify->npermission.'\');"').'</a>'; 
  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }


  $action_url_date = substr($_GET['date'],0,4) == date('Y') ? '' : '&y='.substr($_GET['date'],0,4);
  $action_url_month = substr($_GET['date'],4,2) == date('m') ? '' : '&m='.substr($_GET['date'],4,2);
  $action_url = 'action=save_as_user_list'.$action_url_date.$action_url_month;
  $form_str = tep_draw_form('attendance_setting_form', FILENAME_ROSTER_RECORDS, $action_url);

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($as_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if($_GET['action']=='edit_group_attendance_info'){
/* -----------------------------------------------------
    功能: 获取出勤状态的信息
    参数: $_GET['date'] 日期 
 -----------------------------------------------------*/
  $group_disabled = '';
  $_GET['date'] = $_GET['date'] == '' ? date('Ymd') : $_GET['date'];

  $show_only = '';
  if(isset($_GET['gid'])&&$_GET['gid']!=''){
    $attendance_dd_arr = tep_get_attendance($_GET['date'],$_GET['gid'],false,$_GET['add_id']);
    $gid_flag = false;
  }else{
    $gid_flag = true;
    $g_list = tep_is_group_manager($ocertify->auth_user,true);
    $attendance_dd_arr = array();
    foreach($g_list as $g_value){
      $attendance_dd_arr = array_merge($attendance_dd_arr,tep_get_attendance($_GET['date'],$g_value,true));
    }
  }
  //处理数据排序
  $attendance_temp_arr = array();
   foreach($attendance_dd_arr as $attendance_temp){
     $attendance_temp_arr[$attendance_temp['id']] = $attendance_temp;
   }
   krsort($attendance_temp_arr);
   $attendance_dd_arr = $attendance_temp_arr;

  //if($_GET['gid']!=''&&!tep_is_manager_by_gid($ocertify->auth_user,$_GET['gid'])&&$ocertify->npermission<= '10'&&!empty($attendance_dd_arr)){
    //$show_only = ' disabled="disabled" ';
    //$group_disabled = ' disabled="disabled" ';
  //}
  //if($ocertify->npermission <= '10' || ($_GET['gid'] != '' && $ocertify->npermission <= '10'  )){
    //$group_disabled = ' disabled="disabled" ';
  //}
  //if(empty($attendance_dd_arr)){
    //$group_disabled = '';
  //}
   if(!tep_is_group_manager($ocertify->auth_user)){

     $group_disabled = ' disabled="disabled" ';
     $show_only = ' disabled="disabled" ';
   }

  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_ROSTER_RECORDS);
  //获得 所有排班表
  $attendance_detail_list = array();
  $attendance_detail_sql = "select * from ".TABLE_ATTENDANCE_DETAIL." order by sort asc";
  $attendance_detail_query = tep_db_query($attendance_detail_sql);
  while($attendance_detail_row = tep_db_fetch_array($attendance_detail_query)){
    $attendance_detail_list[] = $attendance_detail_row;
  }
  //生成 所有组列表
  $group_list = tep_get_group_tree();
  $show_manage_group = tep_is_group_manager($ocertify->auth_user,true);

  //获得所有循环方式
  $type_list = array(
     TEXT_CALENDAR_REPEAT_TYPE_NO,
	 TEXT_CALENDAR_REPEAT_TYPE_EVERY_WEEK,
     TEXT_CALENDAR_REPEAT_TYPE_WEEK,
     TEXT_CALENDAR_REPEAT_TYPE_MONTH,
     TEXT_CALENDAR_REPEAT_TYPE_MONTH_WEEK,
     TEXT_CALENDAR_REPEAT_TYPE_YEAR
      );
  include(DIR_FS_ADMIN.'classes/notice_box.php'); 
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

 
  //头部内容
  $heading = array();
  $date_str = TEXT_ATTENDANCE_SETTING_GROUP;
  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  if(!empty($attendance_dd_arr)){
    $group_info_sql = "select * from ".TABLE_GROUPS." WHERE id ='".$_GET['gid']."' and group_status=1";
    $group_info_query = tep_db_query($group_info_sql);
    if($group_info_row = tep_db_fetch_array($group_info_query)){
      $date_str .= '&nbsp;&nbsp;'.$group_info_row['name'];
    }
  }
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');

  $adl_select = '<select onchange="auto_add_attendance(this);" name="attendance_id[]" '.$show_only.' >';
  $adl_select .= '<option value="">--</option>';
  foreach($attendance_detail_list as $a_value){
    $adl_select .= '<option value="'.$a_value['id'].'">'.$a_value['title'].'</option>';
  }
  $adl_select .= '</select>&nbsp;&nbsp;<font color="red">'.TEXT_REMIND_CHOICE_SELECT.'</font>';

  $group_select = '<select name="group[]" '.$group_disabled.' style="max-width:280px;">';
  $hidden_group_select = '<select name="group[]" style="max-width:280px;" >';
  $group_select .= '<option value="">--</option>';
  $group_select_hidden = '';
  $hidden_group_select .= '<option value="">--</option>';
  $default_hidden = '';
  foreach($group_list as $group){
    if(in_array($group['id'],$show_manage_group)||$ocertify->npermission>10){
    if($default_hidden == ''){
      $default_hidden = '<input type="hidden" name="group_hidden[]" value="'.$group['id'].'">';
    }
    $group_select .= '<option value="'.$group['id'].'"';
    $hidden_group_select .= '<option value="'.$group['id'].'"';
    if($group_disabled!=''&&$group['id']==$_GET['gid']){
      $group_select .= ' selected ';
      $hidden_group_select .= ' selected ';
      $group_select_hidden = '<input type="hidden" name="group_hidden[]" value="'.$group['id'].'">';
    }
    $group_select .= '>'.$group['text'].'</option>';
    $hidden_group_select .= '>'.$group['text'].'</option>';
    }
  }
  $group_select .= '</select>&nbsp;&nbsp;<font color="red">'.TEXT_REMIND_CHOICE_SELECT.'</font>';
  if($group_disabled){
    if($group_select_hidden!=''){
      $group_select .= $group_select_hidden;
    }else{
      $group_select .= $default_hidden;
    }
  }
  $hidden_group_select .= '</select>&nbsp;&nbsp;<font color="red">'.TEXT_REMIND_CHOICE_SELECT.'</font>';

  $type_select = '<select name="type[]" '.$show_only.' onchange="edit_space_nums(this,this.value);">';
  foreach($type_list as $t_key => $t_value){
	  if($t_key==1){
	    $t_key =9;
	  }
	  if($t_key>1){
		$t_key = $t_key-1;
	  }	
        $type_select .= '<option value="'.$t_key.'">'.$t_value.'</option>';
  }
  $type_select .= '</select><span class="space" style="display:none" >'.TEXT_CALENDAR_REPEAT_TYPE_WEEK_HEAD.'<input class="limit_input_width" type="text" name="space[]" value="1" '.$show_only.' onkeyup="if(this.value!=\'\'){if(!/^[1-9][0-9]{0,1}$/.test(this.value)){this.value=\'1\'}}">'.TEXT_CALENDAR_REPEAT_TYPE_WEEK_TAIL.'</span>';

  $hidden_div = '<div style="display:none">';
  $hidden_div .= '<table id="add_source">';
  $hidden_div .= '<tr><td class="tr_#line_num" width="30%" nowrap="nowrap" align="left">'.TEXT_ATTENDANCE_PLAN.'#line_num_1</td><td nowrap="nowrap" align="left" colspan="2">&nbsp;</td></tr><tr><td class="tr_#line_num" width="30%" nowrap="nowrap" align="left">'.COMPANY_SYSTEM_SELECT.'</td><td nowrap="nowrap" align="left" colspan="2">'.$hidden_group_select.'</td></tr><tr><td class="tr_#line_num" width="30%" nowrap="nowrap" align="left">'.TEXT_ADL_SELECT.'</td><td nowrap="nowrap" align="left">'.$adl_select.'</td><td nowrap="nowrap" align="left">&nbsp;</td></tr><tr><td class="tr_#line_num" width="30%" nowrap="nowrap" align="left">'.TEXT_TYPE_SELECT.'</td><td nowrap="nowrap" align="left" colspan="2">'.$type_select.'</td></tr><tr><td class="tr_#line_num"></td><td></td><td nowrap="nowrap" align="left"><input type="button" value="'.TEXT_DEL_ADL.'" onclick="del_as(\'#line_num\',this,\'\')"></td></tr><tr><td class="tr_#line_num" width="30%" nowrap="nowrap" align="left" colspan="3">------------------------------------------------------------------------</td></tr>';
  $hidden_div .= '</table></div>';
  $hidden_date .= '<input id="get_att_date" type="hidden" name="get_date" value="'.$_GET['date'].'">';
  $hidden_date .= '<div id="tep_data" style="display:none;"><input type="button" value="'.TEXT_DEL_ADL.'" onclick="del_as(\'\',this,\'1\')"></div>';
  if($group_disabled!=''){
    $hidden_date .= '<input type="hidden" name="default_gid" value="'.$_GET['gid'].'">';
  }


  $heading[] = array('align' => 'left', 'text' => $date_str);
  $heading[] = array('align' => 'right', 'text' => $page_str.$hidden_div);

  //主体内容
  $as_info_row = array();
  //选择日期
  $date_select_str = '';
  $date_select_str .= '<input type="text" name="select_date_text" onchange="change_fetch_date(this.value,\'group\');" value="'.date('Y/m/d',strtotime($_GET['date'])).'">';
  /*
  $date_select_str .= '<select name="select_year" id="fetch_year" onchange="change_fetch_date(\'group\');">';
  for($i=2007;$i<=date('Y')+10;$i++){
    $select_year_selected = $i == date('Y',strtotime($_GET['date'])) ? ' selected' : '';
    $date_select_str .= '<option value="'.$i.'"'.$select_year_selected.'>'.$i.'</option>';
  }
  $date_select_str .= '</select>'.YEAR_TEXT;
  $date_select_str .= '<select name="select_month" id="fetch_month" onchange="change_fetch_date(\'group\');">';
  for($i=1;$i<=12;$i++){
    $select_year_selected = $i == date('m',strtotime($_GET['date'])) ? ' selected' : '';
    $i_str = $i < 10 ? '0'.$i : $i;
    $date_select_str .= '<option value="'.$i_str.'"'.$select_year_selected.'>'.$i_str.'</option>';
  }
  $date_select_str .= '</select>'.MONTH_TEXT;
  $date_select_str .= '<select name="select_day" id="fetch_day" onchange="change_fetch_date(\'group\');">';
  for($i=1;$i<=31;$i++){
    $i_str = $i < 10 ? '0'.$i : $i;
    $select_year_selected = $i == date('d',strtotime($_GET['date'])) ? ' selected' : '';
    $date_select_str .= '<option value="'.$i_str.'"'.$select_year_selected.'>'.$i_str.'</option>';
  }
  $date_select_str .= '</select>'.DAY_TEXT;
   */
  $date_click_str = '';
  
  $date_click_str .= '<div class="yui3-skin-sam yui3-g"><input id="date_orders" type="hidden" name="" size="10" value="'.date('Y-m-d',strtotime($_GET['date'])).'">
                <div class="date_box">
                <a href="javascript:void(0);" onclick="open_new_calendar(\'group\',\''.$_GET['gid'].'\');" class="dpicker"></a> 
                </div>
                <input type="hidden" id="date_order" name="update_date" value="">
                <input type="hidden" name="toggle_open" value="0" id="toggle_open"> 
                <div class="yui3-u" id="new_yui3">
                <div id="mycalendar"></div> 
                </div>
                </div></td>';
  $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'id="add_start" width="30%" nowrap="nowrap"', 'text' => TEXT_ATTENDANCE_DATE), 
        array('align' => 'left', 'params' => '"nowrap="nowrap"', 'text' => '<table cellspacing="0" cellpadding="0" border="0"><tr><td>'.$date_select_str.'</td><td align="left">'.$date_click_str.'</td></tr></table>')
      );

  $line_count = count($attendance_dd_arr);
  
    $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_count.'" width="30%" nowrap="nowrap"', 'text' => TEXT_ATTENDANCE_PLAN.($line_count != 0 ? $line_count+1 : '')), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '')
      );
    $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_count.'" width="30%" nowrap="nowrap"', 'text' => COMPANY_SYSTEM_SELECT), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $group_select)
      );
    $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_count.'" width="30%" nowrap="nowrap"', 'text' => TEXT_ADL_SELECT), 
        array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => $adl_select),
        array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '')
      ); 
    $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_count.'" width="30%" nowrap="nowrap"', 'text' => TEXT_TYPE_SELECT), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $type_select),
        
      );
  $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_count.'" width="30%" nowrap="nowrap"', 'text' => ''), 
        array('text'=>''),
        array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input '.$show_only.' type="button" value="'.TEXT_DEL_ADL.'" onclick="del_as(\''.$line_count.'\',this,\'\')">'),
      );

  if($line_count > 0){
    $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_count.'"nowrap="nowrap" colspan="3"', 'text' => '------------------------------------------------------------------------') 
      );
  }

  //是否有出勤数据
  if(!empty($attendance_dd_arr)){
    //如果当前管理员权限为root，显示全部组 其他权限则显示当前管理员可控制的组
    $group_list_select = $group_list;

    if(empty($group_list_select)){
      $group_list_select = $group_list;
      $group_disabled = ' disabled="disabled" ';
    }
    $show_arr = true;
    $line_i = 0;
    foreach($attendance_dd_arr as $a_info){
      if($a_info['end_date'] != 0 && $a_info['type'] != 0 && $a_info['end_date'] <= $_GET['date']){

        continue;
      }

      if($line_i != 0){
        $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.($line_i-1).'" nowrap="nowrap" colspan="3"', 'text' => '------------------------------------------------------------------------') 
        );
      }
      $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_i.'" width="30%" nowrap="nowrap"', 'text' => TEXT_ATTENDANCE_PLAN.($line_count-$line_i != 1 ? $line_count-$line_i : '')), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '')
      );

      if($gid_flag&&!in_array($a_info['group_id'],$show_manage_group)&&$ocertify->npermission<15){
        continue;
      }
      $has_adl_select = '<select onchange="auto_add_attendance(this);" name="has_attendance_id[]" '.$show_only.' >';
      $has_adl_select .= '<option>--</option>';
      foreach($attendance_detail_list as $a_value){
        $has_adl_select .= '<option value="'.$a_value['id'].'"';
        if($a_info['attendance_detail_id'] == $a_value['id']){
          $has_adl_select .= ' selected ';
        }
        $has_adl_select .=' >'.$a_value['title'].'</option>';
      }
      $has_adl_select .= '</select>';
 
      $has_group_select = '<select name="has_group[]" '.$group_disabled.'>';
      $has_group_select .= '<option>--</option>';
      $has_group_select_hidden = '';
      $default_has_group = '';
      foreach($group_list_select as $group){
        if(!(in_array($group['id'],$show_manage_group) || $ocertify->npermission>10) && $a_info['group_id'] != $group['id']){
          continue;
        }
        if($default_has_group == ''){
        $default_has_group = '<input type="hidden" name="has_group_hidden[]" value="'.$group['id'].'">';
        }
        $has_group_select .= '<option value="'.$group['id'].'" ';
        if($a_info['group_id'] == $group['id']){
          $has_group_select .= 'selected ';
          $has_group_select_hidden = '<input type="hidden" name="has_group_hidden[]" value="'.$group['id'].'">';
        }
        $has_group_select .= ' >'.$group['text'].'</oprion>';
      }
      $has_group_select .= '</select>';
      if($group_disabled){
        if($has_group_select_hidden!=''){
          $has_group_select .= $has_group_select_hidden;
        }else{
          $has_group_select .= $default_has_group;
        }
      }

      $has_type_select = '<select name="has_type[]" '.$show_only.'  onchange="edit_space_nums(this,this.value);" >';
      foreach($type_list as $t_key => $t_value){
		  //给每周一个value
	     if($t_key==1){
	       $t_key =9;
	     }
		 if($t_key>1){
		   $t_key = $t_key-1;
		 }
        $has_type_select .= '<option value="'.$t_key.'" ';
		 //判断是不是每周
		 if($a_info['type']==1 && $a_info['space']==0){
		   $a_info['type']=8;
		 }
        if($a_info['type'] == $t_key){
          $has_type_select .= ' selected ';
        }
        $has_type_select .= ' >'.$t_value.'</option>';
      }
	  $style_space=($a_info['type']==1&&$a_info['space']!=0)?'':'style="display:none"' ;
	  if($a_info['space']==0 || $a_info['space']==''){
	    $a_info['space']=1;
	  }
	  $has_type_select .= '</select><span class="space" '.$style_space.' >'.TEXT_CALENDAR_REPEAT_TYPE_WEEK_HEAD.'<input class="limit_input_width" type="text" name="has_space[]" value='.$a_info['space'].' '.$show_only.'  onkeyup="if(this.value!=\'\'){if(!/^[1-9]{1}[0-9]{0,1}$/.test(this.value)){this.value=\'1\'}}">'.TEXT_CALENDAR_REPEAT_TYPE_WEEK_TAIL.'</span>';
      $as_info_row_tmp = array(); 
      $as_info_row_tmp[] = array('align' => 'left', 'params' => 'class="tr_'.$line_i.'" width="30%" nowrap="nowrap"', 'text' => TEXT_ADL_SELECT);
      $as_info_row_tmp[] = array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => $has_adl_select.'<input type="hidden" name="data_as[]" value="'.$a_info['id'].'"><input type="hidden" name="type_array[]" value="'.$a_info['type'].'">');
      if($show_arr){
        $as_user_added = $a_info['add_user'];
        $as_date_added = $a_info['add_time'];
        $as_user_update = $a_info['update_user'];
        $as_last_modified = $a_info['update_time'];
        $show_arr = false;
      }
      $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_i.'" width="30%" nowrap="nowrap"', 'text' => COMPANY_SYSTEM_SELECT), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $has_group_select)
      );
      $as_info_row[]['text'] = $as_info_row_tmp;
      $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_i.'" width="30%" nowrap="nowrap"', 'text' => TEXT_TYPE_SELECT), 
        array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $has_type_select),
      );
      $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'class="tr_'.$line_i.'" width="30%" nowrap="nowrap"', 'text' => ''), 
        array('text'=>''),
        array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => '<input  '.$show_only.' type="button" onclick="del_as(\''.$line_i.'\',this,\''.$a_info['id'].'\',\''.$ocertify->npermission.'\')" value="'.TEXT_DEL_ADL.'">'.($a_info['type'] != 0 && $_GET['date'] >= date('Ymd') ? '<input '.$show_only.' type="button" value="'.TEXT_ATTENDANCE_DATE_END.'" onclick="end_date(\'group\',\''.$a_info['id'].'\',\''.$_GET['date'].'\');">' : ''))
      );
      $line_i++;
    }
  }
  
  $as_info_row[]['text'] = array(array('text'=>'&nbsp;'));
  $as_info_row[] = array('params'=> 'id="add_end"','text' => array(
        array('align' => 'left', 'text' => '<input type="hidden" id="line_num" value="'.($line_i+1).'">'.$hidden_date.TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($as_user_added)?$as_user_added:TEXT_UNSET_DATA)),
        array('align' => 'left', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($as_date_added)?$as_date_added:TEXT_UNSET_DATA))
      ));
  $as_info_row[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($as_user_update)?$as_user_update:TEXT_UNSET_DATA)),
        array('align' => 'left', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($as_last_modified)?$as_last_modified:TEXT_UNSET_DATA))
      );


  //底部内容
  $buttons = array();
    //判断出勤变更按钮是否可用
    //当天已排班的员工
    $already_add_group_array = array();
    $already_add_user_array = array();
    $already_add_group = tep_get_attendance($_GET['date']);
    foreach($already_add_group as $already_group_user_value){

      $already_add_group_array[] = $already_group_user_value['group_id']; 
    }
    if(!empty($already_add_group_array)){
      $already_group_query = tep_db_query("select all_users_id from ".TABLE_GROUPS." where id in (".implode(',',$already_add_group_array).")");
      while($already_group_array = tep_db_fetch_array($already_group_query)){

        $already_add_user_temp = explode('|||',$already_group_array['all_users_id']);
        foreach($already_add_user_temp as $already_add_user_temp_value){
          $already_add_user_array[] = $already_add_user_temp_value; 
        }
      }
      tep_db_free_result($already_group_query);
    }
    //当天请假的员工
    $leave_user_query = tep_db_query("select user from ".TABLE_ATTENDANCE_DETAIL_REPLACE." where date='".$_GET['date']."'");
    while($leave_user_array = tep_db_fetch_array($leave_user_query)){

      $already_add_user_array[] = $leave_user_array['user'];
    }
    tep_db_free_result($leave_user_query);
    $already_add_user_array = array_unique($already_add_user_array);
    $current_users_list = array();
    if($ocertify->npermission >= '15'){
      $sql_all_user = 'select u.*, p.permission from ' . TABLE_USERS . ' u, ' .  TABLE_PERMISSIONS . " p where u.userid = p.userid and u.status=1 order by u.name asc"; 
      $query_all_user = tep_db_query($sql_all_user);
      while($row_all_user = tep_db_fetch_array($query_all_user)){
        if(!in_array($row_all_user['userid'],$already_add_user_array)){
          $current_users_list[] = $row_all_user['userid'];
        }
      }
    }else{
 
      $group_show_query = tep_db_query("select all_users_id,all_managers_id from ".TABLE_GROUPS." where group_status=1");
      while($group_show_array = tep_db_fetch_array($group_show_query)){

        $group_list_select_array = explode('|||',$group_show_array['all_managers_id']); 
        if(in_array($ocertify->auth_user,$group_list_select_array)&&!empty($group_list_select_array)){

          $all_user_select_array = explode('|||',$group_show_array['all_users_id']);
          foreach($all_user_select_array as $all_user_select_value){
            $row_all_user[] = $all_user_select_value;
          }
        }
      }
      tep_db_free_result($group_show_query);
      $row_all_user = array_unique($row_all_user);
      foreach($row_all_user as $row_all_user_value){

        $row_all_user_value_name = tep_get_user_info($row_all_user_value);
        if(!in_array($row_all_user_value,$already_add_user_array)){
          $current_users_list[] = $row_all_user_value;
        }
      }
    }
    //end
  
  //$button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_HISTORY, ' '.$show_only.' onclick="hidden_info_box();"').'</a>'; 
  if($ocertify->npermission > 10 || tep_is_group_manager($ocertify->auth_user)){
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(TEXT_ATTENDANCE_SETTING_USER, 'onclick="show_user_attendance_info(\'\',\''.$date.'\',\'\',\''.$_GET['user'].'\',\'\',\'\',\''.$_GET['gid'].'\')"').'</a>'; 
  }
  if(!isset($_GET['gid'])||$_GET['gid']==''||tep_is_group_manager($ocertify->auth_user)){
    $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(TEXT_ATTENDANCE_SETTING_CHANGE, 'onclick="show_replace_attendance_info(\'\',\''.$date.'\',\''.$_GET['index'].'\',\''.$_GET['user'].'\',\'\',\''.$_GET['gid'].'\')"'.(empty($current_users_list) ? ' disabled' : '')).'</a>'; 
  }
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, ' '.$show_only.' id="button_delete" onclick="delete_submit(\''.$ocertify->npermission.'\',\'as\');"').'</a>'; 
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, ' '.$show_only.' id="button_save" onclick="save_submit(\''.$ocertify->npermission.'\');"').'</a>'; 
  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }


  $action_url_date = substr($_GET['date'],0,4) == date('Y') ? '' : '&y='.substr($_GET['date'],0,4);
  $action_url_month = substr($_GET['date'],4,2) == date('m') ? '' : '&m='.substr($_GET['date'],4,2);
  $action_url = 'action=save_as_list'.$action_url_date.$action_url_month;
  $form_str = tep_draw_form('attendance_setting_form', FILENAME_ROSTER_RECORDS, $action_url);

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($as_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if($_GET['action'] == 'edit_replace_attendance_info'){


  $_GET['date'] = $_GET['date'] == '' ? date('Ymd') : $_GET['date'];
  include(DIR_FS_ADMIN.'classes/notice_box.php'); 
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.FILENAME_ROSTER_RECORDS);

  //获取当前登录用户所属的组
  $group_id_array = tep_get_groups_by_user($ocertify->auth_user);
  /*
  //获取当前登录用户的当天排班
  $attendance_id_array = array();
  $date_attendance_query = tep_db_query("select attendance_detail_id,group_id from ".TABLE_ATTENDANCE_DETAIL_DATE." where `date`='".$_GET['date']."' order by add_time desc");
  $date_attendance_query = tep_db_query("select attendance_detail_id,group_id from ".TABLE_ATTENDANCE_DETAIL_DATE." where `date`='".$_GET['date']."' order by add_time desc");
  while($date_attendance_array = tep_db_fetch_array($date_attendance_query)){
  //组的排班
  $attendance_group_id_array = tep_get_attendance($_GET['date']);
  foreach($attendance_group_id_array as $date_attendance_array){

    if(in_array($date_attendance_array['group_id'],$group_id_array)){

      $attendance_id_array[] = $date_attendance_array['attendance_detail_id'];
    }
  }
  tep_db_free_result($date_attendance_query);
  */
  $attendance_id_array = array();
  $attendance_tmp_id_array = array();
  foreach($group_id_array as $g_list_id){
    $attendance_array = tep_get_attendance($_GET['date'],$g_list_id,false);
    $attendance_tmp_id_array = array_merge($attendance_array,$attendance_tmp_id_array);
  }
  foreach($attendance_tmp_id_array as $attendance_id_arr){
    $attendance_id_array[] = $attendance_id_arr['attendance_detail_id'];
  }

  //个人的排班
  $attendance_user_id_array = tep_get_attendance_user($_GET['date']);
  foreach($attendance_user_id_array as $date_attendance_array){

    if($date_attendance_array['user_id'] == $ocertify->auth_user){

      $attendance_id_array[] = $date_attendance_array['attendance_detail_id'];
    }
  }
  //获取当前登录用户的请假
  $replace_attendance_id_array = array();
  $replace_attendance_query = tep_db_query("select * from ".TABLE_ATTENDANCE_DETAIL_REPLACE." where user='".$_GET['uid']."' and `date`='".$_GET['date']."'");
  while($replace_attendance_array = tep_db_fetch_array($replace_attendance_query)){
    $replace_attendance_id_array[] = $replace_attendance_array['attendance_detail_id'];
  }
  tep_db_free_result($replace_attendance_query);
  if($_GET['att_id'] == ''){
    $_GET['att_id'] = !empty($replace_attendance_id_array) ? current(array_intersect($attendance_id_array,$replace_attendance_id_array)) : current($attendance_id_array);
  }
  $get_att_id = $_GET['att_id'];
  if(isset($_GET['uid'])&&$_GET['uid']!=''){
  $replace_sql = "select * from ".TABLE_ATTENDANCE_DETAIL_REPLACE." where 
    `date`='".$_GET['date']."' ";
    $replace_sql .= " and user ='".$_GET['uid']."' and attendance_detail_id = '".$_GET['att_id']."'";
  $change_flag = true;
  $replace_query = tep_db_query($replace_sql);
  if($replace_info_res = tep_db_fetch_array($replace_query)){
    $change_flag = false;
    $user_added = $replace_info_res['add_user'];
    $date_added = $replace_info_res['add_time'];
    $user_update = $replace_info_res['update_user'];
    $last_modified = $replace_info_res['update_time'];
  }
  }

  $user_list = tep_get_user_list_by_userid($_GET['uid']);
  if($_GET['date']<date('Ymd',time())){
    $disabled = ' disabled="disabled" ';
    $change_flag = false;
  }
  if($ocertify->npermission<15&&$replace_info_res['allow_status']==1){
    $disabled = ' disabled="disabled" ';
    $change_flag = false;
  }

  if($_GET['uid'] != ''){
    //判断选择的用户是不是当前登录用户的组员
    //获取选择用户所属的组
    $current_user_group_array = tep_get_groups_by_user($_GET['uid']);
    //如果当前登录用户是组长的话，获取组ID
    $current_admin_group_array = tep_is_group_manager($ocertify->auth_user,true);

    $group_intersect = array_intersect($current_user_group_array,$current_admin_group_array);

    if(empty($group_intersect)){

      $disabled = ' disabled="disabled" ';
      $change_flag = false;
    }
  }


  


  $replace_att_list = tep_get_attendance_by_user_date($_GET['date'],$ocertify->auth_user,$_GET['uid']);
  if($disabled){
    $attendance_select = '<select name="attendance_detail_id" disabled="disabled">';
  }else{
  if(count($replace_att_list)==1){
    $attendance_select = '<select name="attendance_detail_id" disabled="disabled">';
  }else{
    $attendance_select = '<select name="attendance_detail_id" onchange="change_model_get_time(this.value)">';
  }
  }
  $replace_select = '<select id ="att_detail_id" name="replace_attendance_detail_id" '.$disabled.' onchange="change_scheduling_time(this.value);">';
  if(!empty($replace_att_list)){
  $replace_select .= '<option value="0">'.TEXT_LEAVE_ONE_DAY.'</option>';
  foreach($replace_att_list as $attendance_info){
    $attendance_select .= '<option value="'.$attendance_info['id'].'"';
    if(isset($replace_info_res['attendance_detail_id'])&&$replace_info_res['attendance_detail_id']==$attendance_info['id']){
      $attendance_select .= ' selected ';
    }
    $attendance_select .= '>'.$attendance_info['title'].'</option>';

  }
  }else{
    $attendance_select .= '<option value="0">'.TEXT_LEAVE_ONE_DAY.'</option>';
  }

  //判断当前登录用户是否是组长
  $groups_flag = false;
  if(tep_is_group_manager($ocertify->auth_user) > 0){

    $groups_flag = true;
  }
  //判断当前登录用户是否是管理员
  $admin_flag = false;
  if($ocertify->npermission > 10){

    $admin_flag = true;
  }
  $attendance_select = '
	  
	  
<select name="attendance_detail_id" onchange="show_replace_attendance_info(\'\',\''.$_GET['date'].'\',\''.$_GET['index'].'\',\''.$_GET['uid'].'\',this.value,\''.$_GET['group_id'].'\');"'.($groups_flag == false && $admin_flag == false ? '' : ' disabled="disabled"').'>';
//是从指定日期指定排班(个人)过来的请假
	if(isset($get_att_id)&&$get_att_id!=''){
       $sql_get_user_by_date = "select user_id from ".TABLE_ATTENDANCE_DETAIL_DATE." where attendance_detail_id=".$get_att_id." and date='".$_GET['date']."'";
       $query_sql_get_user_by_date = tep_db_query($sql_get_user_by_date);
	    $user_by_arr_list=array();
       while($user_by_att = tep_db_fetch_array($query_sql_get_user_by_date)){
         $user_by_arr_list[]= $user_by_att['user_id']; 
       }
        }
  //start
  $current_user_list_array = array();
  if($ocertify->npermission >= '15'){
      $sql_all_user = 'select u.*, p.permission from ' . TABLE_USERS . ' u, ' .  TABLE_PERMISSIONS . " p where u.userid = p.userid and u.status=1 order by u.name asc"; 
      $query_all_user = tep_db_query($sql_all_user);
      while($row_all_user = tep_db_fetch_array($query_all_user)){
		  //没有指定的组
                  if($_GET['group_id']==0 || $_GET['group_id']==''){
                    $current_user_list_array[] = $row_all_user['userid'];
		  
		  }
		  //指定的人
		  else if(!in_array($row_all_user['userid'],$already_add_user_array)&& (!$_GET['group_id']==0)){
				
			if(isset($get_att_id)&&$get_att_id!=''){
                          if(in_array($row_all_user['userid'],$user_by_arr_list)){
                            $current_user_list_array[] = $row_all_user['userid'];
			  }
			}
			else{
                          $current_user_list_array[] = $row_all_user['userid'];
			}
			//组更改
		}else if(!empty($already_add_group_array) && $_GET['group_id']!=0){
			if(in_array($row_all_user['userid'],$already_add_user_array)){
		          $current_user_list_array[] = $row_all_user['userid'];	
			}
		
		}
      }
    }else{
 
      $group_show_query = tep_db_query("select all_users_id,all_managers_id from ".TABLE_GROUPS." where group_status=1");
      while($group_show_array = tep_db_fetch_array($group_show_query)){

        $group_list_select_array = explode('|||',$group_show_array['all_managers_id']); 
        if(in_array($ocertify->auth_user,$group_list_select_array)&&!empty($group_list_select_array)){

          $all_user_select_array = explode('|||',$group_show_array['all_users_id']);
          foreach($all_user_select_array as $all_user_select_value){
            $current_user_list_array[] = $all_user_select_value;
          }
        }
      }
      $current_user_list_array = array_unique($current_user_list_array);
      if(empty($current_user_list_array)){
        $current_user_list_array=array();
        $sql_all_user = 'select u.*, p.permission from ' . TABLE_USERS . ' u, ' .  TABLE_PERMISSIONS . " p where u.userid = p.userid and u.status=1 order by u.name asc"; 
        $query_all_user = tep_db_query($sql_all_user);
        while($all_user = tep_db_fetch_array($query_all_user)){
          if($ocertify->auth_user == $all_user['userid']){
             $current_user_list_array[]=$all_user['userid'];
          } 
        }
      }
      tep_db_free_result($group_show_query);
      $current_user_list_array = array_unique($current_user_list_array); 
    } 
  //end
  if(isset($_GET['uid'])&&$_GET['uid']!=''){
    $replace_att_list = tep_get_attendance_by_user_date($_GET['date'],$ocertify->auth_user,$_GET['uid']); 
    $select_att = '';
    $replace_show_array = array();
    $attendance_select .= '<option value="0">'.TEXT_LEAVE_ONE_DAY.'</option>';
    $attendance_select_hidden = '';
    if(!empty($replace_att_list)){
    $select_replace_attendance_info = array();
      foreach($replace_att_list as $attendance_info){
        if($attendance_select_hidden =='' ){
          $attendance_select_hidden = '<input type="hidden" name="attendance_detail_id_hidden" value="'.$attendance_info['id'].'">';
        }
        $replace_show_array[] = $attendance_info['id'];

        //当前登录用户不是管理员，也不是组长的情况下
        if($groups_flag == false && $admin_flag == false){
          if(in_array($attendance_info['id'],$attendance_id_array)){
            $attendance_select .= '<option value="'.$attendance_info['id'].'"';
            if($_GET['att_id']==$attendance_info['id']){
              $attendance_select .= ' selected ';
              $select_replace_attendance_info = $attendance_info;
              $select_att = $attendance_info['id'];
              $current_att_title = $attendance_info['title'];
              $attendance_select_hidden = '<input type="hidden" name="attendance_detail_id_hidden" value="'.$attendance_info['id'].'">';
            }
            $attendance_select .= '>'.$attendance_info['title'].'</option>';
          }
        }else{
          $attendance_select .= '<option value="'.$attendance_info['id'].'"';
          if(isset($_GET['att_id'])&&$_GET['att_id']==$attendance_info['id']){
            $attendance_select .= ' selected ';
            $select_replace_attendance_info = $attendance_info;
            $select_att = $attendance_info['id'];
            $current_att_title = $attendance_info['title'];
            $attendance_select_hidden = '<input type="hidden" name="attendance_detail_id_hidden" value="'.$attendance_info['id'].'">';
          }
          $attendance_select .= '>'.$attendance_info['title'].'</option>'; 
        }
 
      }
      if($select_att == ''){
        $select_att = 0;
      }
      if(!empty($select_replace_attendance_info)){
        if($select_replace_attendance_info['set_time']==0 && $select_replace_attendance_info['work_start']!=':'&& $select_replace_attendance_info['work_end']!=':'){
          $user_adl = '<span>'.$select_replace_attendance_info['work_start'].'--'.$select_replace_attendance_info['work_end'].'</span><input type="hidden" name="email_work_start" value="'.$select_replace_attendance_info['work_start'].'"><input type="hidden" name="email_work_end" value="'.$select_replace_attendance_info['work_end'].'">';
        }elseif($select_replace_attendance_info['set_time']==1){
          $work_time = $select_replace_attendance_info['work_hours']+$select_replace_attendance_info['rest_hours'];
          $user_adl = '<span>'.$work_time .TELECOM_UNKNOW_TABLE_TIME. '</span>';
        }
      }
    }
  }else{
	  if(isset($get_att_id)&&$get_att_id!=''){
		  $user_all_att = tep_all_attenande_by_uid($current_user_list_array[0],$_GET['date']);
		  foreach($user_all_att as $val){
		      $all_att_sql = "select * from ".TABLE_ATTENDANCE_DETAIL." where id=".$val['attendance_detail_id'];
		      $all_att_query = tep_db_query($all_att_sql);
              $all_att_row = tep_db_fetch_array($all_att_query);
              $attendance_select .= '<option value='.$all_att_row['id'].'>'.$all_att_row['title'].'</option>';
			  if($all_att_row['set_time']==0 && $all_att_row['work_start']!=':'&& $all_att_row['work_end']!=':'){
			      $user_adl = '<span>'.$all_att_row['work_start'].'--'.$all_att_row['work_end'].'</span>'; 
			  }else{
                  $work = $all_att_row['work_hours']+$all_att_row['rest_hours'];
                  $user_adl = '<span>'.$work .TELECOM_UNKNOW_TABLE_TIME. '</span>';
			  }

		  }
         
	  }else{
         $select_att = 0;
         $attendance_select .= '<option value="0">'.TEXT_LEAVE_ONE_DAY.'</option>';
         $attendance_select_hidden = '<input type="hidden" name="attendance_detail_id_hidden" value="0">';
	  }
  }


  $replace_att_list_rep = tep_get_attendance_by_user_date($_GET['date'],0,$_GET['uid'],true);

  foreach($replace_att_list_rep as $attendance_info_rep){

    if($groups_flag == false && $admin_flag == false && !in_array($attendance_info_rep['id'],$attendance_id_array)){
      if($select_att!=$attendance_info_rep['id']){
        $replace_select .= '<option value="'.$attendance_info_rep['id'].'"';
        if(isset($replace_info_res['replace_attendance_detail_id'])&&$replace_info_res['replace_attendance_detail_id']==$attendance_info_rep['id']){
          $replace_select .= ' selected ';
        }
        $replace_select .= '>'.$attendance_info_rep['title'].'</option>';
      }
    }else{
      if($select_att!=$attendance_info_rep['id']){
        $replace_select .= '<option value="'.$attendance_info_rep['id'].'"';
        if(isset($replace_info_res['replace_attendance_detail_id'])&&$replace_info_res['replace_attendance_detail_id']==$attendance_info_rep['id']){
          $replace_select .= ' selected ';
        }
        $replace_select .= '>'.$attendance_info_rep['title'].'</option>';
      } 
    }
  }
  $attendance_select .= '</select>&nbsp;&nbsp;<font color="red" id="attendance_detail_error"></font>';
  $attendance_select .= $attendance_select_hidden;
  $replace_select .= '</select>&nbsp;&nbsp;<font color="red" id="replace_attendance_detail_error"></font></div>';

  $leave_start_array = explode(':',$replace_info_res['leave_start']);
  $leave_start_min_left= substr($leave_start_array[1],0,1);
  $leave_start_min_right= substr($leave_start_array[1],1,2);

  $leave_start = '<select name="leave_start_hour" id="leave_start_hour" '.$disabled.'>';
  for($i=0;$i<=23;$i++){
    $selected = $leave_start_array['0']!=$i ?'':' selected ';
    $leave_start .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
  }
  $leave_start .= '</select>&nbsp;'.HOUR_TEXT;

  $leave_start .= '<select name="leave_start_minute_a" id="leave_start_min_l" '.$disabled.'>';
  for($i=0;$i<=5;$i++){
    $selected = $leave_start_min_left!=$i ?'':' selected ';
    $leave_start .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $leave_start .= '</select>';

  $leave_start .= '<select name="leave_start_minute_b" id="leave_start_min_r" '.$disabled.'>';
  for($i=0;$i<=9;$i++){
    $selected = $leave_start_min_right!=$i ?'':' selected ';
    $leave_start .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $leave_start .= '</select>&nbsp;'.MINUTE_TEXT.'';

  $leave_end_array = explode(':',$replace_info_res['leave_end']);
  $leave_end_min_left= substr($leave_end_array[1],0,1);
  $leave_end_min_right= substr($leave_end_array[1],1,2);

  $leave_end = '<select name="leave_end_hour" id="leave_end_hour" '.$disabled.'>';
  for($i=0;$i<=23;$i++){
    $selected = $leave_end_array['0']!=$i ?'':' selected ';
    $leave_end .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $leave_end .= '</select>&nbsp;'.HOUR_TEXT;

  $leave_end .= '<select name="leave_end_minute_a" id="leave_end_min_l" '.$disabled.'>';
  for($i=0;$i<=5;$i++){
    $selected = $leave_end_min_left!=$i ?'':' selected ';
    $leave_end .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $leave_end .= '</select>';

  $leave_end .= '<select name="leave_end_minute_b" id="leave_end_min_r" '.$disabled.'>';
  for($i=0;$i<=9;$i++){
    $selected = $leave_end_min_right!=$i ?'':' selected ';
    $leave_end .= '<option value="'.$i.'"'.$selected.'>'.$i.'</option>';
  }
  $leave_end .= '</select>&nbsp;'.MINUTE_TEXT.'&nbsp;&nbsp;<font color="red" id="leave_end_error">'.TEXT_REMIND_CHOICE_SELECT.'</font>';

  //头部内容
  $heading = array();
  $date_str = TEXT_ATTENDANCE_SETTING_CHANGE;
  if(isset($_GET['uid'])&&$_GET['uid']!=''){ 
    $user_info_self = tep_get_user_info($_GET['uid']);
    $date_str .= '&nbsp;&nbsp;'.$user_info_self['name'].'<span id="use_get_userid" style="display:none;">'.$_GET['uid'].'||'.$_GET['date'].'</span>';
  }
  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => $date_str);
  $heading[] = array('align' => 'right', 'text' => $page_str);
  $hidden_date .= '<input type="hidden" name="get_date" value="'.$_GET['date'].'"><input type="hidden" name="replace_id" value="'.$replace_info_res['id'].'">';

  //选择日期
  $date_select_str = '';
  $date_select_str .= '<input type="text" name="select_date_text" onchange="change_fetch_date(this.value,\'replace\');" value="'.date('Y/m/d',strtotime($_GET['date'])).'">';
  /*
  $date_select_str .= '<select name="select_year" id="fetch_year" onchange="change_fetch_date(\'replace\');">';
  for($i=2007;$i<=date('Y')+10;$i++){
    $select_year_selected = $i == date('Y',strtotime($_GET['date'])) ? ' selected' : '';
    $date_select_str .= '<option value="'.$i.'"'.$select_year_selected.'>'.$i.'</option>';
  }
  $date_select_str .= '</select>'.YEAR_TEXT;
  $date_select_str .= '<select name="select_month" id="fetch_month" onchange="change_fetch_date(\'replace\');">';
  for($i=1;$i<=12;$i++){
    $select_year_selected = $i == date('m',strtotime($_GET['date'])) ? ' selected' : '';
    $i_str = $i < 10 ? '0'.$i : $i;
    $date_select_str .= '<option value="'.$i_str.'"'.$select_year_selected.'>'.$i_str.'</option>';
  }
  $date_select_str .= '</select>'.MONTH_TEXT;
  $date_select_str .= '<select name="select_day" id="fetch_day" onchange="change_fetch_date(\'replace\');">';
  for($i=1;$i<=31;$i++){
    $i_str = $i < 10 ? '0'.$i : $i;
    $select_year_selected = $i == date('d',strtotime($_GET['date'])) ? ' selected' : '';
    $date_select_str .= '<option value="'.$i_str.'"'.$select_year_selected.'>'.$i_str.'</option>';
  }
  $date_select_str .= '</select>'.DAY_TEXT;
   */
  $date_click_str = '';
  
  $date_click_str .= '<div class="yui3-skin-sam yui3-g"><input id="date_orders" type="hidden" name="" size="10" value="'.date('Y-m-d',strtotime($_GET['date'])).'">
                <div class="date_box">
                <a href="javascript:void(0);" onclick="open_new_calendar(\'replace\');" class="dpicker"></a> 
                </div>
                <input type="hidden" id="date_order" name="update_date" value="">
                <input type="hidden" name="toggle_open" value="0" id="toggle_open"> 
                <div class="yui3-u" id="new_yui3">
                <div id="mycalendar"></div> 
                </div>
                </div></td>';
  $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap" colspan="3"', 'text' => TEXT_ATTENDANCE_CHANGE_USER), 
      );
  $as_info_row[]['text'] = array(
        array('align' => 'left', 'params' => 'width="30%" nowrap="nowrap"', 'text' => TEXT_ATTENDANCE_DATE), 
        array('align' => 'left', 'params' => '"nowrap="nowrap"', 'text' => '<table cellspacing="0" cellpadding="0" border="0"><tr><td>'.$date_select_str.'</td><td align="left">'.$date_click_str.'</td></tr></table>')
      );

  if((!isset($_GET['uid'])||$_GET['uid']=='')){
    //当天已排班的员工
    $already_add_group_array = array();
    $already_add_user_array = array();
    $already_add_group = tep_get_attendance($_GET['date']);
    foreach($already_add_group as $already_group_user_value){

      $already_add_group_array[] = $already_group_user_value['group_id']; 
    }
    if(!empty($already_add_group_array)){
      $already_group_query = tep_db_query("select all_users_id from ".TABLE_GROUPS." where id in (".implode(',',$already_add_group_array).")");
      while($already_group_array = tep_db_fetch_array($already_group_query)){

        $already_add_user_temp = explode('|||',$already_group_array['all_users_id']);
        foreach($already_add_user_temp as $already_add_user_temp_value){
          $already_add_user_array[] = $already_add_user_temp_value; 
        }
      }
      tep_db_free_result($already_group_query);
    }
    //当天请假的员工
    $leave_user_query = tep_db_query("select user from ".TABLE_ATTENDANCE_DETAIL_REPLACE." where date='".$_GET['date']."'");
    while($leave_user_array = tep_db_fetch_array($leave_user_query)){

      $already_add_user_array[] = $leave_user_array['user'];
    }
    tep_db_free_result($leave_user_query);
    $already_add_user_array = array_unique($already_add_user_array);
    $current_users_list = array();

    if($ocertify->npermission >= '15'){
      $sql_all_user = 'select u.*, p.permission from ' . TABLE_USERS . ' u, ' .  TABLE_PERMISSIONS . " p where u.userid = p.userid and u.status=1 order by u.name asc"; 
      $query_all_user = tep_db_query($sql_all_user);
      $all_user_select = '<select name="user_id" '.$disabled.' onchange="change_users_groups(this.value);" class="replace_user">';
      $all_user_select .= '<option value="">--</option>';
      while($row_all_user = tep_db_fetch_array($query_all_user)){
		  //没有指定的组
		  if($_GET['group_id']==0 || $_GET['group_id']==''){
                $all_user_select .= "<option value='".$row_all_user['userid']."'>".$row_all_user['name']."</option>";
		  
		  }
		  //指定的人
		  else if(!in_array($row_all_user['userid'],$already_add_user_array)&& (!$_GET['group_id']==0)){
				
			if(isset($get_att_id)&&$get_att_id!=''){
				if(in_array($row_all_user['userid'],$user_by_arr_list)){
                   $all_user_select .= "<option value='".$row_all_user['userid']."'>".$row_all_user['name']."</option>";
				}
			}
			else{

              $all_user_select .= "<option value='".$row_all_user['userid']."'>".$row_all_user['name']."</option>";
			}
          $current_users_list[] = $row_all_user['userid'];
			//组更改
		}else if(!empty($already_add_group_array) && $_GET['group_id']!=0){
			if(in_array($row_all_user['userid'],$already_add_user_array)){
              $all_user_select .= "<option value='".$row_all_user['userid']."'>".$row_all_user['name']."</option>";
			
			}
		
		}
      }
      $all_user_select .= '</select>';
    }else{
 
      $group_show_query = tep_db_query("select all_users_id,all_managers_id from ".TABLE_GROUPS." where group_status=1");
      while($group_show_array = tep_db_fetch_array($group_show_query)){

        $group_list_select_array = explode('|||',$group_show_array['all_managers_id']); 
        if(in_array($ocertify->auth_user,$group_list_select_array)&&!empty($group_list_select_array)){

          $all_user_select_array = explode('|||',$group_show_array['all_users_id']);
          foreach($all_user_select_array as $all_user_select_value){
            $row_all_user[] = $all_user_select_value;
          }
        }
      }
    $row_all_user = array_unique($row_all_user);
   if(empty($row_all_user)){
      $row_all_user=array();
      $sql_all_user = 'select u.*, p.permission from ' . TABLE_USERS . ' u, ' .  TABLE_PERMISSIONS . " p where u.userid = p.userid and u.status=1 order by u.name asc"; 
      $query_all_user = tep_db_query($sql_all_user);
      while($all_user = tep_db_fetch_array($query_all_user)){
         if($ocertify->auth_user == $all_user['userid']){
             $row_all_user[]=$all_user['userid'];
         } 
      }
   }
      tep_db_free_result($group_show_query);
      $row_all_user = array_unique($row_all_user);
      $all_user_select = '<select name="user_id" '.$disabled.' onchange="change_users_groups(this.value);" class="replace_user">';
      $all_user_select .= '<option value="">--</option>';
      foreach($row_all_user as $row_all_user_value){

        $row_all_user_value_name = tep_get_user_info($row_all_user_value);
        if($row_all_user_value_name['userid'] == ''){
          continue;
        }
   //     if(!in_array($row_all_user_value,$already_add_user_array)){
          $all_user_select .= "<option value='".$row_all_user_value."'>".$row_all_user_value_name['name']."</option>";
          $current_users_list[] = $row_all_user_value;
        }
    //  }
      $all_user_select .= '</select>';
    } 
    $as_info_row[]['text'] = array(
      array('align' => 'left', 'params' => 'width="20%" nowrap="nowrap"', 'text' => TEXT_SELECT_USER), 
      array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $all_user_select)
    );
  }else{
    $attendance_select .= '<input type="hidden" value="'.$_GET['uid'].'" name="user_id">';
  }
  $as_info_row[]['text'] = array(
    array('align' => 'left', 'params' => 'width="20%" nowrap="nowrap" colspan="3"', 'text' =>TEXT_ATTENDANCE_CHANGE_DETAIL_INFO), 
  );

  $as_info_row[]['text'] = array(
    array('align' => 'left', 'params' => 'width="20%" nowrap="nowrap"', 'text' => TEXT_ADL_SELECT_USER), 
    array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<div class="show_att_titile"'.(substr_count($attendance_select,'option')>=4 ? '' : 'style="display:none;"').'>'.$attendance_select.'</div>')
  );
  $as_info_row[]['text'] = array(
    array('align' => 'left', 'params' => 'width="20%" nowrap="nowrap"', 'text' => TEXT_ADL_SELECT_USER_TEXT), 
    array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => '<div id="show_user_adl"'.(substr_count($attendance_select,'option')>=4 ? '' : 'style="display:none;"').'>'.$user_adl.'</div>')
  );
  $as_info_row[]['text'] = array(
    array('align' => 'left', 'params' => 'width="20%" nowrap="nowrap"', 'text' => TEXT_REPLACE_ADL), 
    array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $replace_select)
  );
  $as_info_row[]['text'] = array(
    array('align' => 'left', 'params' => 'width="20%" nowrap="nowrap"', 'text' => TEXT_ADL_SELECT_USER_TEXT), 
    array('align' => 'left', 'params' => 'width="40%" nowrap="nowrap"', 'text' => $leave_start.'&nbsp;～&nbsp;'.$leave_end), 
  );

  $is_first = true;
  //获取许可者
  //员工对应的管理者

  if($_GET['uid'] == ''){ 
    $current_users = current($current_users_list); 
  }else{
    $current_users = $_GET['uid']; 
  }

  $allow_user_list = array_reverse(explode('|||',$replace_info_res['allow_user']));
  $current_users_array = tep_get_user_list_by_userid($current_users);
  //获取admin及ROOT
  $permissions_query = tep_db_query("select userid from ".TABLE_PERMISSIONS." where permission>=15 order by permission");
  while($permissions_array = tep_db_fetch_array($permissions_query)){

    $current_users_array[] = $permissions_array['userid'];
  }
  tep_db_free_result($permissions_query);
  $current_users_array= array_unique($current_users_array);
  $first_user = current($current_users_array);
  foreach($allow_user_list as $allow_user){
    $allow_user_select = '<select name="allow_user[]" style="width: 150px;" '.$disabled.' onchange="change_users_allow(this.value,'.(isset($replace_info_res['allow_status']) ? $replace_info_res['allow_status'] : 0).');">';
    foreach($current_users_array as $user_info){
      $t_user_info = tep_get_user_info($user_info);
      if($t_user_info['status'] == 1){
        $allow_user_select .= '<option value="'.$user_info.'"';
        if($allow_user == $user_info){
          $allow_user_select .= ' selected ';
        }
        $allow_user_select .= '>'.$t_user_info['name'].'</option>';
      }
    }
    $allow_user_select .= '</select>&nbsp;&nbsp;<font color="red" id="allow_user_error"></font>';
    if($is_first){
      $allow_user_text = TEXT_ALLOW_USER;
      $allow_user_button = '<input type="button" id="add_allow_user_button" value="'.TEXT_ADD_ADL.'" '.$disabled.' onclick="add_allow_user(this,\''.TEXT_DEL_ADL.'\')">';
    }else{
      $allow_user_text = TEXT_ALLOW_USER;
      $allow_user_button = '<input type="button" value="'.TEXT_DEL_ADL.'" '.$disabled.' onclick="del_allow_user(this)">';
    }
    $as_info_row[]['text'] = array(
      array('align' => 'left', 'params' => 'width="20%" nowrap="nowrap"', 'text' => $allow_user_text), 
      array('align' => 'left', 'params' => 'nowrap="nowrap" id="users_groups"', 'text' => $allow_user_select),
      array('align' => 'left', 'params' => 'nowrap="nowrap"', 'text' => $allow_user_button)
    );
    $is_first = false;

  }

  $allow_user_list = array_filter($allow_user_list); 
  if(!empty($allow_user_list)){
    if(in_array($ocertify->auth_user,$allow_user_list)){
      if($_GET['date']<date('Ymd',time())){
        $allow_disabled = ' disabled="disabled" '; 
      }else{
        $allow_disabled = ''; 
      }
    }else{

      $allow_disabled = ' disabled="disabled" ';
    }
  }else{
    if($ocertify->auth_user == $first_user){
      $allow_disabled = '';
    }else{
      $allow_disabled = ' disabled="disabled" '; 
    }
  }

  $status_str = '<select name="allow_status" '.$allow_disabled.'>';
  if(isset($replace_info_res['allow_status'])&&$replace_info_res['allow_status']==1){
    $status_str .= '<option value="0">'.TEXT_REPLACE_NOT_ALLOW.'</option>';
    $status_str .= '<option value="1" selected >'.TEXT_REPLACE_IS_ALLOW.'</option>';
    $change_flag = false;
  }else{
    $status_str .= '<option value="0" selected >'.TEXT_REPLACE_NOT_ALLOW.'</option>';
    $status_str .= '<option value="1" >'.TEXT_REPLACE_IS_ALLOW.'</option>';
  }
  $status_str .= '</select>&nbsp;&nbsp;<font color="red" id="allow_status_error"></font>';
  $as_info_row[] = array('params'=> 'id="add_end"','text' => array(
    array('align' => 'left', 'params' => 'width="20%" nowrap="nowrap"', 'text' => TEXT_ALLOW_STATUS), 
    array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => $status_str)
  ));


  $as_info_row[]['text'] = array(
    array('align' => 'left', 'params' => 'width="20%" nowrap="nowrap"', 'text' => TEXT_ADL_TEXT_INFO), 
    array('align' => 'left', 'params' => 'colspan="2" nowrap="nowrap"', 'text' => tep_draw_textarea_field('text_info', 'hard', '40', '5', $replace_info_res['text_info'], 'onfocus="o_submit_single = false;" onblur="o_submit_single = true;"'.$disabled))
  );

  $as_info_row[] = array('params'=> 'id="add_end"','text' => array(
	  array('align' => 'left', 'text' => $hidden_date.TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($user_added)?$user_added:TEXT_UNSET_DATA)),
	  array('align' => 'left', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($date_added)?$date_added:TEXT_UNSET_DATA))
      ));
  $as_info_row[]['text'] = array(
	  array('align' => 'left', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($user_update)?$user_update:TEXT_UNSET_DATA)),
	  array('align' => 'left', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;'.(tep_not_null($last_modified)?$last_modified:TEXT_UNSET_DATA))
      );

  //底部内容
  $buttons = array();
  
  if(!isset($_GET['uid'])||$_GET['uid']==''||tep_is_group_manager($ocertify->auth_user)){
    if(tep_is_group_manager($ocertify->auth_user)||$ocertify->npermission >= '15'){
	    $disabled='';
	}else{
            $disabled = ' disabled="disabled" ';
        }
     
    if($_GET['uid'] != ''){
      //判断选择的用户是不是当前登录用户的组员
      //获取选择用户所属的组
      $current_user_group_array = tep_get_groups_by_user($_GET['uid']);
      //如果当前登录用户是组长的话，获取组ID
      $current_admin_group_array = tep_is_group_manager($ocertify->auth_user,true);

      $group_intersect = array_intersect($current_user_group_array,$current_admin_group_array);

      if(empty($group_intersect)){

        $disabled = ' disabled="disabled" ';
      }
    }

       $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(TEXT_ATTENDANCE_SETTING_USER, $disabled.'onclick="show_user_attendance_info(\'\',\''.$_GET['date'].'\',\'\',\''.$_GET['uid'].'\',\'\',\''.$_GET['att_id'].'\',\''.$_GET['group_id'].'\')"').'</a>'; 
       $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(TEXT_ATTENDANCE_SETTING_GROUP, $disabled.' onclick="show_group_attendance_info(\'\',\''.$_GET['date'].'\', \''.  $_GET['index'].'\',\''.$_GET['group_id'].'\',\''.$_GET['att_id'].'\',\''.$_GET['uid'].'\')"').'</a>'; 
  }
  if($ocertify->npermission>10
    ||($ocertify->auth_user==$replace_info_res['user']&&$replace_info_res['allow_status'] ==0)
    ||(in_array($ocertify->auth_user,tep_is_group_manager($replace_info_res['user']))&&$replace_info_res['allow_status'] ==0)){
    $style_hide = '';
  }else{
    if($ocertify->npermission>10){
      $style_hide = '';
    }else{
      $style_hide = 'style="display:none;"';
    }
  }
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE,(!isset($replace_info_res['allow_status']) ? ' disabled="disabled" ' : $disabled).'id="button_delete" onclick="delete_submit(\''.$ocertify->npermission.'\',\'\');"').'</a>'; 

  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, $disabled.'id="button_save" onclick="return save_submit(\''.$ocertify->npermission.'\');"').'</a>'; 
  if (!empty($button)) {
    $buttons = array('align' => 'center', 'button' => $button); 
  }


  $action_url_date = substr($_GET['date'],0,4) == date('Y') ? '' : '&y='.substr($_GET['date'],0,4);
  $action_url_month = substr($_GET['date'],4,2) == date('m') ? '' : '&m='.substr($_GET['date'],4,2);
  $action_url = 'action=save_as_replace'.$action_url_date.$action_url_month;
  $form_str = tep_draw_form('attendance_setting_form', FILENAME_ROSTER_RECORDS, $action_url);

  //生成表单 
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);   
  $notice_box->get_contents($as_info_row, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
}else if($_GET['action'] == 'edit_basic_object_info'){
  /* -----------------------------------------------------
    功能: 编辑组的基本项目弹出框
    参数: group_id 组ID 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_PAYROLLS);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_PAYROLLS_SETTING_BASIC_OBJECT);
  $heading[] = array('align' => 'right', 'text' => '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>');

  $buttons = array();

  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick=""').'</a>';
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick=""').'</a>';

  $buttons = array('align' => 'center', 'button' => $button); 
  
  $user_payrolls_info = array();

  //生成组的下拉框
  $group_list = tep_get_group_tree();
  if(USER_PAYROLL_SETTING != ''){
    $user_payroll_array = unserialize(USER_PAYROLL_SETTING);
    if(isset($user_payroll_array[$ocertify->auth_user])){
      $show_group_id = $user_payroll_array[$ocertify->auth_user]['group'];
    }else{
      $show_group_id = $group_list[0]['id']; 
    }
  }else{
    $show_group_id = $group_list[0]['id']; 
  }
  $show_group_id = $_GET['group_id'] != '' ? $_GET['group_id'] : $show_group_id;

  $group_str .= '<select name="show_group" onchange="change_group_list(0,this.value)">';
  foreach($group_list as $group){
    $group_str .= '<option value="'.$group['id'].'"';
    if($show_group_id == $group['id']){
      $group_str .= ' selected ';
    }
    $group_str .= '>'.$group['text'].'</oprion>';
  }
  $group_str .= '</select>';

  $user_payrolls_info[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_GROUP_SELECT), 
        array('align' => 'left', 'text' => $group_str), 
      ); 
  //工资计算
  $payroll_query = tep_db_query("select * from ".TABLE_PAYROLL_SETTLEMENT." where group_id='".$show_group_id."' and project_id=0 order by sort"); 
  //计算工资的项目
  if(tep_db_num_rows($payroll_query) > 0){
    $i = 1;
    //已有的项目ID
    $old_project_id_array = array();
    $text_array = array();
    while($payroll_array = tep_db_fetch_array($payroll_query)){

      $group_content_row_payroll = array();
      $group_content_row_payroll[] = array('params'=>'width="20%"','text'=> '<input type="hidden" value="'.$payroll_array['id'].'" name="payroll_sort[]">'.TEXT_PAYROLLS_OBJECT_VALUE); 
      $old_project_id_array[] = $payroll_array['id'];
      $group_content_row_payroll[] = array('text' => '<input type="text" style="width: 145px;" value="'.$payroll_array['title'].'" name="old_object_title['.$payroll_array['id'].']"><input type="text" style="width: 150px;" value="'.$payroll_array['contents'].'" name="old_object_contents['.$payroll_array['id'].']"><input type="button" onclick="delete_obj('.$i.');" value="'.IMAGE_DELETE.'">&nbsp;&nbsp;<input type="button" value="'.BUTTON_ADD_TEXT.'" onclick="add_obj(this,\''.TEXT_GROUP_PAYROLL_OBJECT_VALUE.'\',\''.TEXT_GROUP_PAYROLL_OBJECT_FORMULA.'\',\''.IMAGE_DELETE.'\',1);"><br><textarea style="overflow-y: hidden; width: 299px; height: 15px;" name="old_object_value['.$payroll_array['id'].']" id="textarea_'.$i.'">'.$payroll_array['project_value'].'</textarea>'); 
      $user_payrolls_info[] = array('params'=>'id="obj_tr_'.$i.'"','text'=>$group_content_row_payroll);
      $i++;
    }
  }
  tep_db_free_result($payroll_query); 
  $form_str = tep_draw_form('copy_group', FILENAME_GROUPS, 'action=copy_group_confirm');
  
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($user_payrolls_info, $buttons);
  $notice_box->get_eof(tep_eof_hidden());

  echo $notice_box->show_notice();
}else if($_GET['action'] == 'edit_formula_object_info'){
  /* -----------------------------------------------------
    功能: 编辑组的公式项目弹出框
    参数: group_id 组ID 
 -----------------------------------------------------*/
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_PAYROLLS);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => TEXT_PAYROLLS_SETTING_BASIC_OBJECT);
  $heading[] = array('align' => 'right', 'text' => '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>');

  $buttons = array();

  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick=""').'</a>';
  $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_SAVE, 'onclick=""').'</a>';

  $buttons = array('align' => 'center', 'button' => $button); 
  
  $user_payrolls_info = array();

  //生成组的下拉框
  $group_list = tep_get_group_tree();
  if(USER_PAYROLL_SETTING != ''){
    $user_payroll_array = unserialize(USER_PAYROLL_SETTING);
    if(isset($user_payroll_array[$ocertify->auth_user])){
      $show_group_id = $user_payroll_array[$ocertify->auth_user]['group'];
    }else{
      $show_group_id = $group_list[0]['id']; 
    }
  }else{
    $show_group_id = $group_list[0]['id']; 
  }
  $show_group_id = $_GET['group_id'] != '' ? $_GET['group_id'] : $show_group_id;

  $group_str .= '<select name="show_group" onchange="change_group_list(1,this.value)">';
  foreach($group_list as $group){
    $group_str .= '<option value="'.$group['id'].'"';
    if($show_group_id == $group['id']){
      $group_str .= ' selected ';
    }
    $group_str .= '>'.$group['text'].'</oprion>';
  }
  $group_str .= '</select>';

  $user_payrolls_info[]['text'] = array(
        array('align' => 'left', 'text' => TEXT_GROUP_SELECT), 
        array('align' => 'left', 'text' => $group_str), 
      ); 
  //工资计算
  $payroll_query = tep_db_query("select * from ".TABLE_PAYROLL_SETTLEMENT." where group_id='".$show_group_id."' and project_id=1 order by sort"); 
  //计算工资的项目
  if(tep_db_num_rows($payroll_query) > 0){
    $i = 1;
    //已有的项目ID
    $old_project_id_array = array();
    $old_formula_id_array = array();
    $text_array = array();
    while($payroll_array = tep_db_fetch_array($payroll_query)){

      $group_content_row_payroll = array();
      $group_content_row_payroll[] = array('params'=>'width="20%"','text'=> '<input type="hidden" value="'.$payroll_array['id'].'" name="payroll_sort[]">'.TEXT_PAYROLLS_OBJECT_FORMULA); 
      $old_formula_id_array[] = $payroll_array['id'];
      $group_content_row_payroll[] = array('text' => '<input type="text" value="'.$payroll_array['title'].'" style="width: 145px;" name="old_formula_title['.$payroll_array['id'].']"><input type="text" value="'.$payroll_array['contents'].'" style="width: 150px;" name="old_formula_contents['.$payroll_array['id'].']"><input type="button" onclick="delete_obj('.$i.');" value="'.IMAGE_DELETE.'">&nbsp;&nbsp;<input type="button" value="'.BUTTON_ADD_TEXT.'" onclick="add_obj(this,\''.TEXT_GROUP_PAYROLL_OBJECT_VALUE.'\',\''.TEXT_GROUP_PAYROLL_OBJECT_FORMULA.'\',\''.IMAGE_DELETE.'\',1);"><br><textarea style="overflow-y: hidden; width: 299px; height: 15px;" name="old_formula_value['.$payroll_array['id'].']" id="textarea_'.$i.'">'.$payroll_array['project_value'].'</textarea>');
      $text_array[] = $i;
      $user_payrolls_info[] = array('params'=>'id="obj_tr_'.$i.'"','text'=>$group_content_row_payroll);
      $i++;
    }
  }
  tep_db_free_result($payroll_query); 
  $form_str = tep_draw_form('copy_group', FILENAME_GROUPS, 'action=copy_group_confirm');
  
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($user_payrolls_info, $buttons);
  $notice_box->get_eof(tep_eof_hidden());

  echo $notice_box->show_notice();
}
