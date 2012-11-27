<?php
if ($_GET['action'] == 'show_category_info') {
  //显示分类信息的弹出框 
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
      $categories_query_raw .= " and cd.site_id = '".$site_id."' ";
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
      where site_id = ".$site_id."
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
    $page_str .= '<a onclick="show_category_info(\''.$cid_array[$c_key-1].'\')" href="javascript:void(0);"><'.IMAGE_PREV.'</a>&nbsp;&nbsp;'; 
  }
 
  if ($c_key < (count($cid_array) - 1)) {
    $page_str .= '<a onclick="show_category_info(\''.$cid_array[$c_key+1].'\')" href="javascript:void(0);">'.IMAGE_NEXT.'></a>&nbsp;&nbsp;'; 
  }
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
 
  $category_info_raw = tep_db_query("select cd.categories_name, c.date_added, c.user_added, cd.last_modified, cd.user_last_modified from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = '".$_GET['current_cid']."' and c.categories_id = cd.categories_id and (cd.site_id = '0' or cd.site_id = '".$site_id."') order by cd.site_id desc limit 1");
  $category_info_res = tep_db_fetch_array($category_info_raw); 
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => '<b>'.$category_info_res['categories_name'].'</b>');
  $heading[] = array('align' => 'right', 'text' => $page_str);
  
  $buttons = array();
  
  if ($ocertify->npermission >= 10) {
    if (empty($site_id)) {
      $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_MOVE, 'onclick="move_category_id(\''.$_GET['current_cid'].'\')"').'</a>'; 
      $button[] = '<a href="'.tep_href_link(FILENAME_PRODUCTS_MANUAL, 'cPath='.$_GET['cPath'].'&cID='.$_GET['current_cid'].'&action=show_categories_manual&site_id='.$site_id).'">'.tep_html_element_button(IMAGE_MANUAL).'</a>'; 
    } 
    if (!empty($site_id)) {
      if (tep_db_num_rows(tep_db_query("select categories_id from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$_GET['current_cid']."' and site_id = '".$site_id."'"))) {
        $button[] = '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="delete_category_info(\''.$_GET['current_cid'].'\', \'1\');"').'</a>'; 
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
        array('align' => 'left', 'text' => TEXT_LAST_MODIFIED.'&nbsp;'.((tep_not_null(tep_datetime_short($category_info_res['last_mofified'])))?tep_datetime_short($category_info_res['last_mofified']):TEXT_UNSET_DATA)), 
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
  //移动分类信息的弹出框 
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $site_id = isset($_GET['site_id'])?$_GET['site_id']:0; 
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  $category_info_raw = tep_db_query("select c.parent_id, cd.categories_name from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = '".$_GET['current_cid']."' and c.categories_id = cd.categories_id and (cd.site_id = '0' or cd.site_id = '".$site_id."') order by cd.site_id desc limit 1");
  $category_info_res = tep_db_fetch_array($category_info_raw); 
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => '<b>'.TEXT_INFO_HEADING_MOVE_CATEGORY.'</b>');
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
  //删除分类的弹出框 
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $site_id = isset($_GET['site_id'])?$_GET['site_id']:0; 
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
  $category_info_raw = tep_db_query("select cd.categories_name from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = '".$_GET['current_cid']."' and c.categories_id = cd.categories_id and (cd.site_id = '0' or cd.site_id = '".$site_id."') order by cd.site_id desc limit 1");
  $category_info_res = tep_db_fetch_array($category_info_raw); 
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => '<b>'.TEXT_INFO_HEADING_DELETE_CATEGORY.'</b>');
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
}
