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
} else if ($_GET['action'] == 'update_virtual_quantity') {
  //更新商品虚拟库存的弹出框 
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
  $product_info_raw = tep_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$_POST['pid']."'");
  $product_info = tep_db_fetch_array($product_info_raw);
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => '<b>'.TABLE_HEADING_CATEGORIES_PRODUCT_VIRTUAL_STORE.'</b>');
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
  //更新商品数量的弹出框 
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
  $product_info_raw = tep_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$_POST['pid']."'");
  $product_info = tep_db_fetch_array($product_info_raw);
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => '<b>'.TABLE_HEADING_CATEGORIES_PRODUCT_REAL_QUANTITY.'</b>');
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
  //设置商品价格的弹出框 
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');
  
  $product_info_raw = tep_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$_POST['pid']."'");
  $product_info = tep_db_fetch_array($product_info_raw);
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => '<b>'.TABLE_HEADING_DANJIA.'</b>');
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
  //显示商品信息的弹出框
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
    $page_str .= '<a onclick="show_product_info(\''.$pid_arr[$p_key - 1].'\', \'\');" href="javascript:void(0);"><'.IMAGE_PREV.'</a>&nbsp;&nbsp';
  }
  
  if($p_key < count($pid_arr)-1){
    $page_str .= '<a onclick="show_product_info(\''.$pid_arr[$p_key + 1].'\', \'\');" href="javascript:void(0);">'.IMAGE_NEXT.'></a>&nbsp;&nbsp';
  } 
  
  $page_str .= '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';

  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => '<b>'.$pInfo->products_name.'</b>');
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
  $button[] = tep_html_element_submit(IMAGE_SAVE); 
  
  $buttons = array('align' => 'center', 'type' => 'div', 'id' => 'order_del', 'params' => 'class="main"' , 'button' => $button);

  $product_info_params = array('width' => '95%', 'cellpadding' => '0', 'cellspacing' => '0', 'border' => '0', 'parameters' => 'style="margin-bottom:10px;"');
  
  $product_info_array = array();
  
  $product_tmp_price = tep_get_products_price($pInfo->products_id);
  $inventory = tep_get_product_inventory($pInfo->products_id);
 
  $product_info_array[]['text'] = array(
        array('params' => 'width="30%" nowrap="nowrap"', 'text' => TABLE_HEADING_JIAGE_TEXT.':'),
        array('text' => (($product_tmp_price['sprice'])?'<s>'.$currencies->format($product_tmp_price['price']).'</s>&nbsp;':'').tep_draw_input_field('products_price', number_format(abs($pInfo->products_price)?abs($pInfo->products_price):'0',0,'.',''),'onkeyup="clearNoNum(this)" id="pp" size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;"') . '&nbsp;' . CATEGORY_MONEY_UNIT_TEXT .  '&nbsp;&nbsp;&larr;&nbsp;' . (int)$pInfo->products_price .  CATEGORY_MONEY_UNIT_TEXT)
      );
  if (!$pInfo->products_bflag && $pInfo->relate_products_id) {
    $product_info_array[]['text'] = array(
          array('params' => 'nowrap="nowrap"', 'text' => mb_substr(CATEGORY_AVERAGE_PRICE,0, -1).':'),
          array('text' => @display_price(tep_get_avg_by_pid($pInfo->products_id)).CATEGORY_MONEY_UNIT_TEXT) 
        );
  }
  
  $product_info_array[]['text'] = array(
        array('params' => 'nowrap="nowrap"', 'text' => TABLE_HEADING_CATEGORIES_PRODUCT_REAL_QUANTITY.':'),
        array('text' => tep_draw_input_field('products_real_quantity', $pInfo->products_real_quantity,'size="8" id="qt" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"') . '&nbsp;' .CATEGORY_GE_UNIT_TEXT.  '&nbsp;&nbsp;&larr;&nbsp;' . $pInfo->products_real_quantity .CATEGORY_GE_UNIT_TEXT)
      );
  
  $product_info_array[]['text'] = array(
        array('params' => 'nowrap="nowrap"', 'text' => TABLE_HEADING_CATEGORIES_PRODUCT_VIRTUAL_STORE.':'),
        array('text' => tep_draw_input_field('products_virtual_quantity', $pInfo->products_virtual_quantity,' size="8" id="qt" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"') . '&nbsp;'.CATEGORY_GE_UNIT_TEXT.  '&nbsp;&nbsp;&larr;&nbsp;' . $pInfo->products_virtual_quantity .  CATEGORY_GE_UNIT_TEXT)
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
          array('params' => 'colspan="2"', 'text' => '<b>'.TEXT_PRODUCT_LINK_PRODUCT_TEXT.$relate_pInfo->products_name.'</b>') 
        );
    
    $relate_product_info_array[]['text'] = array(
          array('params' => 'width="30%" nowrap="nowrap"', 'text' => TABLE_HEADING_JIAGE_TEXT.':'), 
          array('text' => tep_draw_hidden_field('relate_products_id', $relate_pInfo->products_id).(($relate_product_tmp_price['sprice'])?'<s>'.$currencies->format($relate_product_tmp_price['price']).'</s>&nbsp;':'').tep_draw_input_field('relate_products_price', number_format(abs($relate_pInfo->products_price)?abs($relate_pInfo->products_price):'0',0,'.',''),'onkeyup="clearNoNum(this)" size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;"') . '&nbsp;' .  CATEGORY_MONEY_UNIT_TEXT .  '&nbsp;&nbsp;&larr;&nbsp;' .  (int)$relate_pInfo->products_price . CATEGORY_MONEY_UNIT_TEXT)
        );
  
    if (!$relate_pInfo->products_bflag && $relate_pInfo->relate_products_id) {
      $relate_product_info_array[]['text'] = array(
            array('params' => 'nowrap="nowrap"', 'text' => mb_substr(CATEGORY_AVERAGE_PRICE,0, -1).':'),
            array('text' => @display_price(tep_get_avg_by_pid($relate_pInfo->products_id)).CATEGORY_MONEY_UNIT_TEXT) 
          );
    }
    
    $relate_product_info_array[]['text'] = array(
          array('params' => 'nowrap="nowrap"', 'text' => TABLE_HEADING_CATEGORIES_PRODUCT_REAL_QUANTITY.':'),
          array('text' => tep_draw_input_field('relate_products_real_quantity', $relate_pInfo->products_real_quantity,'size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"') . '&nbsp;' .CATEGORY_GE_UNIT_TEXT.  '&nbsp;&nbsp;&larr;&nbsp;' . $relate_pInfo->products_real_quantity . CATEGORY_GE_UNIT_TEXT)
        );
  
    $relate_product_info_array[]['text'] = array(
          array('params' => 'nowrap="nowrap"', 'text' => TABLE_HEADING_CATEGORIES_PRODUCT_VIRTUAL_STORE.':'),
          array('text' => tep_draw_input_field('relate_products_virtual_quantity', $relate_pInfo->products_virtual_quantity,' size="8" style="text-align: right;font: bold small sans-serif;ime-mode: disabled;" onkeyup="clearLibNum(this);"') . '&nbsp;'.CATEGORY_GE_UNIT_TEXT. '&nbsp;&nbsp;&larr;&nbsp;' . $relate_pInfo->products_virtual_quantity . CATEGORY_GE_UNIT_TEXT)
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
        array('align' => 'left', 'params' => 'colspan="4"', 'text' => '<b>'.TABLE_HEADING_PRODUCT_HISTORY.'</b>') 
      ); 
  $product_history_array[]['text'] = array(
        array('align' => 'center', 'params' => 'width="30%"', 'text' => '<b>'.TABLE_HEADING_FETCHTIME_TEXT.'</b>'), 
        array('align' => 'center', 'params' => 'width="15%"', 'text' => '<b>'.TABLE_HEADING_GESHU.'</b>'), 
        array('align' => 'center', 'params' => 'width="25%"', 'text' => '<b>'.TABLE_HEADING_DANJIA.'</b>'), 
        array('align' => 'center', 'text' => '<b>'.TABLE_HEADING_OSTATUS.'</b>'), 
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
          array('align' => 'left', 'params' => 'colspan="4"', 'text' => '<b>'.TEXT_PRODUCT_LINK_PRODUCT_TEXT.$relate_products_name.'</b>') 
        );
    $relate_product_history_array[]['text'] = array(
        array('align' => 'center', 'params' => 'width="30%"', 'text' => '<b>'.TABLE_HEADING_FETCHTIME_TEXT.'</b>'), 
        array('align' => 'center', 'params' => 'width="15%"', 'text' => '<b>'.TABLE_HEADING_GESHU.'</b>'), 
        array('align' => 'center', 'params' => 'width="25%"', 'text' => '<b>'.TABLE_HEADING_DANJIA.'</b>'), 
        array('align' => 'center', 'text' => '<b>'.TABLE_HEADING_OSTATUS.'</b>'), 
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
  $form_action = 'simple_update_product';
  $form_str = tep_draw_form($form_action, FILENAME_CATEGORIES, 'cPath=' .  $_GET['cPath'] .  '&pID=' .  $_GET['pID'] . '&page='.$_GET['page'].  '&action=' .  $form_action.($_GET['search']?'&search='.  $_GET['search']:'').(!empty($_GET['site_id'])?'&site_id='.$_GET['site_id']:'&site_id=0'), 'post', '');
  $notice_box->get_form($form_str);
  $notice_box->get_heading($heading);
  $notice_box->get_contents($contents, $buttons);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
} else if ($_GET['action'] == 'product_description_delete_box') {
  //删除商品的弹出框
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
  $heading[] = array('align' => 'left', 'text' => '<b>'.TEXT_INFO_HEADING_DELETE_PRODUCT.'</b>');
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
        array('text' => '<br><b>' . $pInfo->products_name . '</b>') 
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
  //删除商品的弹出框
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
  $heading[] = array('align' => 'left', 'text' => '<b>'.TEXT_INFO_HEADING_DELETE_PRODUCT.'</b>');
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
        array('text' => '<br><b>' . $pInfo->products_name . '</b>'.tep_draw_hidden_field('products_id', $pInfo->products_id)) 
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
  //移动商品的弹出框
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $site_id = isset($_GET['site_id'])?$_GET['site_id']:0; 
  $pInfo = tep_get_pinfo_by_pid($_GET['pID'],$site_id);
  $cPath = $_GET['cPath'];
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => '<b>'.TEXT_INFO_HEADING_MOVE_PRODUCT.'</b>');
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
        array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' . tep_output_generated_category_path($pInfo->products_id, 'product') .  '</b>'.tep_draw_hidden_field('products_id', $pInfo->products_id)) 
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
  //拷贝商品的弹出框
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_CATEGORIES);
  include(DIR_FS_ADMIN.'classes/notice_box.php');
  $site_id = isset($_GET['site_id'])?$_GET['site_id']:0; 
  $pInfo = tep_get_pinfo_by_pid($_GET['pID'],$site_id);
  $cPath = $_GET['cPath'];
  
  $notice_box = new notice_box('popup_order_title', 'popup_order_info');

  $page_str = '<a onclick="hidden_info_box();" href="javascript:void(0);">X</a>';
  
  $heading = array();
  $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
  $heading[] = array('align' => 'left', 'text' => '<b>'.TEXT_INFO_HEADING_COPY_TO.'</b>');
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
        array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' . tep_output_generated_category_path($pInfo->products_id, 'product') . '</b>') 
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
}
