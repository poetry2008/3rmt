<?php
/*
   $Id$
 */
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies(2);
require(DIR_FS_ADMIN . '/classes/notice_box.php');
if (isset($_GET['action']) && $_GET['action']) {
  switch ($_GET['action']) {
    case 'update_group':
    case 'insert_group':
      //添加／删除组 
      tep_isset_eof(); 
      $error = false;
      if (empty($_POST['name'])) {
        $error = true; 
      }

      if (empty($_POST['title'])) {
        $error = true; 
      }

      if (!$error) {
        $sql_data_array = array(
            'name' => tep_db_prepare_input($_POST['name']),
            'title' => tep_db_prepare_input($_POST['title']),
            'comment' => tep_db_prepare_input($_POST['comment']),
            'is_preorder' => tep_db_prepare_input($_POST['is_preorder']),
            'sort_num' => tep_db_prepare_input($_POST['sort_num']),
            );  
        if ($_GET['action'] == 'update_group') {
          $update_sql_data = array(
              'user_update' => $_SESSION['user_name'],
              'date_update' => 'now()'
              );
          $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
          tep_db_perform(TABLE_OPTION_GROUP, $sql_data_array, 'update', 'id=\''.$_POST['group_id'].'\'');
        } else if ($_GET['action'] == 'insert_group') {
          $insert_sql_data = array(
              'created_at' => 'now()',
              'user_added' => $_SESSION['user_name'],
              'user_update' => $_SESSION['user_name'],
              'date_update' => 'now()'
              ); 
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data); 
          tep_db_perform(TABLE_OPTION_GROUP, $sql_data_array); 
        }
      }
      $param_str = substr(tep_get_all_get_params(array('group_id', 'action')), 0, -1);
      tep_redirect(tep_href_link(FILENAME_OPTION, $param_str)); 
      break; 
    case 'delete_group_confirm':
      //删除组 
      tep_db_query('delete from '.TABLE_OPTION_GROUP.' where id = \''.$_GET['group_id'].'\''); 
      tep_db_query('delete from '.TABLE_OPTION_ITEM.' where group_id = \''.$_GET['group_id'].'\''); 
      tep_db_query('update `'.TABLE_PRODUCTS.'` set `belong_to_option` = \'\' where `belong_to_option` = \''.$_GET['group_id'].'\''); 
      $param_str = substr(tep_get_all_get_params(array('group_id', 'action')), 0, -1);
      tep_redirect(tep_href_link(FILENAME_OPTION, $param_str)); 
      break; 
    case 'del_select_group':
      //删除指定组 
      tep_isset_eof(); 
      $param_str = substr(tep_get_all_get_params(array('group_id', 'action')), 0, -1);
      if (!empty($_POST['option_group_id'])) {
        foreach ($_POST['option_group_id'] as $ge_key => $ge_value) {
          tep_db_query('delete from '.TABLE_OPTION_GROUP.' where id = \''.$ge_value.'\''); 
          tep_db_query('delete from '.TABLE_OPTION_ITEM.' where group_id = \''.$ge_value.'\''); 
          tep_db_query('update `'.TABLE_PRODUCTS.'` set `belong_to_option` = \'\' where `belong_to_option` = \''.$ge_value.'\''); 
        }
      }
      tep_redirect(tep_href_link(FILENAME_OPTION, $param_str)); 
      break;
    case 'insert_item':
    case 'update_item':
      //新建或编辑item 
      tep_isset_eof(); 
      $error = false;
      if (empty($_POST['front_title'])) {
        $error = true;
      }
      if (empty($_POST['title'])) {
        $error = true;
      }
      
      $option_array = array(); 
      if (isset($_POST['itext'])) {
        $option_array['itext'] = tep_db_prepare_input($_POST['itext']); 
      }
      
      if (isset($_POST['itextarea'])) {
        $option_array['itextarea'] = tep_db_prepare_input($_POST['itextarea']); 
      }
      
      if (isset($_POST['require'])) {
        $option_array['require'] = tep_db_prepare_input($_POST['require']); 
      }
      
      if (isset($_POST['dselect'])) {
        $d_str = substr($_POST['dselect'], 3); 
      } 
      
      foreach ($_POST as $pekey => $pevalue) {
        if (preg_match('/^(op_)\d{1,}/',$pekey)) {
          if (empty($pevalue)) {
            unset($_POST[$pekey]); 
          }
        } 
      } 
      
      $o_s_array = array();
      
      foreach ($_POST as $pskey => $psvalue) {
        if (preg_match('/^(op_)\d{1,}/',$pskey)) {
          $o_s_array[] = $psvalue; 
        }
      }
      
      if (!empty($o_s_array)) {
        $option_array['se_option'] = $o_s_array; 
      }
      
      if (isset($_POST['secomment'])) {
        $option_array['secomment'] = $_POST['secomment']; 
      }
      
      if (isset($_POST['sedefault'])) {
        $option_array['sedefault'] = $_POST['sedefault']; 
      }
      
      if (isset($_POST['icomment'])) {
        $option_array['icomment'] = $_POST['icomment']; 
      }
      
      if (isset($_POST['iline'])) {
        if (is_numeric($_POST['iline'])) {
          if ($_POST['iline'] == 0) {
            $option_array['iline'] = 1; 
          } else {
            $option_array['iline'] = $_POST['iline']; 
          }
        } else {
          $option_array['iline'] = 1; 
        }
      }
     
      if (isset($_POST['icomment'])) {
        $option_array['ictype'] = $_POST['ictype']; 
      }

      if (isset($_POST['imax_num'])) {
        $option_array['imax_num'] = $_POST['imax_num']; 
      }
     
      if (isset($_POST['racomment'])) {
        $option_array['racomment'] = $_POST['racomment']; 
      }
      
      if (isset($_POST['default_radio'])) {
        $option_array['default_radio'] = $_POST['default_radio']; 
      }
      
      $radio_option_array = array();
      $r_num = 0; 
      $image_directory = DIR_FS_CATALOG_IMAGES.'0/option_image/'; 
     
      if (($_GET['action'] == 'insert_item') && ($_POST['is_copy'] == '0')) {
        foreach ($_POST as $r_key => $r_value) {
          if (preg_match('/^(ro_)\d{1,}/',$r_key)) {
            $r_value = rtrim($r_value); 
            if ($r_value == '') {
              continue; 
            }
            
            $radio_option_array[$r_num]['title'] = $r_value;
            $re_num = substr($r_key, 3); 
            $radio_option_array[$r_num]['money'] = (int)$_POST['rom_'.$re_num];
            
            $radio_option_array[$r_num]['images'] = array();
            if (!empty($_FILES['rop_'.$re_num])) {
              foreach ($_FILES['rop_'.$re_num]['name'] as $pic_key => $pic_value) {
                if (!empty($_FILES['rop_'.$re_num]['name'][$pic_key])) {
                  $radio_option_array[$r_num]['images'][] = $_FILES['rop_'.$re_num]['name'][$pic_key];  
                } else {
                  $radio_option_array[$r_num]['images'][] = '';  
                }
                if (is_uploaded_file($_FILES['rop_'.$re_num]['tmp_name'][$pic_key])) {
                  move_uploaded_file($_FILES['rop_'.$re_num]['tmp_name'][$pic_key], $image_directory.$_FILES['rop_'.$re_num]['name'][$pic_key]); 
                  chmod($image_directory.$_FILES['rop_'.$re_num]['name'][$pic_key], 0666); 
                }
              }
            }
            $r_num++; 
          }  
        }
      } 
      
      $ur_num = 0; 
      if (($_GET['action'] == 'update_item') || ($_POST['is_copy'] == '1')) {
        foreach ($_POST as $ur_key => $ur_value) {
          if (preg_match('/^(ro_)\d{1,}/',$ur_key)) {
            $ur_value = rtrim($ur_value); 
            if ($ur_value == '') {
              continue; 
            }

            $radio_option_array[$ur_num]['title'] = $ur_value;
            $re_num = substr($ur_key, 3); 
            
            $radio_option_array[$ur_num]['money'] = (int)$_POST['rom_'.$re_num];
            $radio_option_array[$ur_num]['images'] = array();
            
            if (!empty($_FILES['rop_'.$re_num])) {
              foreach ($_FILES['rop_'.$re_num]['name'] as $pic_key => $pic_value) {
                if (!empty($_FILES['rop_'.$re_num]['name'][$pic_key])) {
                  $radio_option_array[$ur_num]['images'][] = $_FILES['rop_'.$re_num]['name'][$pic_key];  
                } else {
                  $radio_option_array[$ur_num]['images'][] = isset($_POST['rou_'.$re_num][$pic_key])?$_POST['rou_'.$re_num][$pic_key]:'';  
                }
                
                if (is_uploaded_file($_FILES['rop_'.$re_num]['tmp_name'][$pic_key])) {
                  move_uploaded_file($_FILES['rop_'.$re_num]['tmp_name'][$pic_key], $image_directory.$_FILES['rop_'.$re_num]['name'][$pic_key]); 
                  chmod($image_directory.$_FILES['rop_'.$re_num]['name'][$pic_key], 0666); 
                }
              } 
            }
            $ur_num++; 
          } 
        }
      } 
      
      if (!empty($radio_option_array)) {
        $option_array['radio_image'] =$radio_option_array;  
      }
      
      if (!$error) {
        if ($_GET['action'] == 'update_item') {
          $option_array['eid'] = $_POST['item_id'];
          $update_sql = "update `".TABLE_OPTION_ITEM."` set `title` =
            '".tep_db_prepare_input($_POST['title'])."', `front_title` =
            '".tep_db_prepare_input($_POST['front_title'])."', `option` =
            '".addslashes(serialize($option_array))."', `type` =
            '".tep_db_prepare_input(strtolower($_POST['type']))."', `price` =
            '".tep_db_prepare_input($_POST['price'])."', `sort_num` =
            '".tep_db_prepare_input((int)$_POST['sort_num'])."', `place_type` =
            '".tep_db_prepare_input($_POST['place_type'])."', `user_update` =
            '".$_SESSION['user_name']."', `date_update` = '".date('Y-m-d H:i:s',time())."' where id =
            '".$_POST['item_id']."'"; 
          tep_db_query($update_sql); 
        } else if ($_GET['action'] == 'insert_item') {
          $insert_sql = "insert into `".TABLE_OPTION_ITEM."` values(NULL,
            '".$_GET['g_id']."', '".tep_db_prepare_input($_POST['title'])."',
            '".tep_db_prepare_input($_POST['front_title'])."',
            '".tep_db_prepare_input(tep_get_random_option_item_name())."', '',
            '".addslashes(serialize($option_array))."',
            '".tep_db_prepare_input(strtolower($_POST['type']))."',
            '".tep_db_prepare_input($_POST['price'])."', '1',
            '".tep_db_prepare_input((int)$_POST['sort_num'])."',
            '".tep_db_prepare_input($_POST['place_type'])."', '".date('Y-m-d
              H:i:s',time())."' ,'".$_SESSION['user_name']."','".$_SESSION['user_name']."','".date('Y-m-d H:i:s',time())."')"; 
           tep_db_query($insert_sql); 
           $item_id = tep_db_insert_id(); 
           $option_array['eid'] = $item_id;
           tep_db_query("update `".TABLE_OPTION_ITEM."` set `option` = '".addslashes(serialize($option_array))."' where `id` = '".$item_id."'");
        }
      }
      
      $param_str = substr(tep_get_all_get_params(array('info', 'x', 'y', 'action', 'item_id')), 0, -1);
      tep_redirect(tep_href_link(FILENAME_OPTION, $param_str));
      break;
    case 'delete_item_confirm':
      //删除item 
      $param_str = substr(tep_get_all_get_params(array('info', 'x', 'y', 'action', 'item_id')), 0, -1);
      if (isset($_GET['item_id'])) {
        tep_db_query('delete from '.TABLE_OPTION_ITEM.' where id = \''.(int)$_GET['item_id'].'\''); 
      }
      tep_redirect(tep_href_link(FILENAME_OPTION, $param_str)); 
      break;
    case 'setflag':
      //设置item的状态 
      $param_str = substr(tep_get_all_get_params(array('info', 'x', 'y', 'action', 'item_id', 'flag')), 0, -1);
      tep_db_query("update `".TABLE_OPTION_ITEM."` set `status` = '".(int)$_GET['flag']."', `user_update` = '".$_SESSION['user_name']."', `date_update` = '".date('Y-m-d H:i:s',time())."' where id = '".$_GET['item_id']."'"); 
      tep_redirect(tep_href_link(FILENAME_OPTION, $param_str));  
      break;
    case 'del_select_item':
      //删除选中的item 
      tep_isset_eof(); 
      $param_str = substr(tep_get_all_get_params(array('info', 'x', 'y', 'action', 'item_id')), 0, -1);
      if (!empty($_POST['option_item_id'])) {
        foreach ($_POST['option_item_id'] as $i_key => $i_value) {
          tep_db_query('delete from '.TABLE_OPTION_ITEM.' where id = \''.(int)$i_value.'\''); 
        }
      }
      tep_redirect(tep_href_link(FILENAME_OPTION, $param_str));  
      break;
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/jquery.autocomplete.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/all_page.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=option&type=js"></script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
if (isset($_GET['g_id'])) {
  $belong = $belong.'?g_id='.$_GET['g_id'];
}
require("includes/note_js.php");
?>
<script language="javascript" src="includes/javascript/jquery.autocomplete.js"></script>
</head>
<?php
if (isset($_GET['eof']) && $_GET['eof'] == 'error') {
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="show_error_message()">
<div id="popup_info">
<div class="popup_img"><img onclick="close_error_message()" src="images/close_error_message.gif" alt="close"></div>
<span><?php echo TEXT_EOF_ERROR_MSG;?></span>
</div>
<div id="popup_box"></div>
<?php
} else {
?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php
}
?>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
<script language='javascript'>
  one_time_pwd('<?php echo $page_name;?>');
</script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
      <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
      <!-- left_navigation -->
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      <!-- left_navigation_eof -->
      </table>
    </td>
    <!-- body_text -->
    <td width="100%" valign="top">
      <div class="box_warp">
      <?php echo $notes;?>
      <div class="compatible">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td width="100%" height="40">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading">
                <?php 
                if (isset($_GET['g_id'])) {
                  $head_option_group_raw = tep_db_query("select name from ".TABLE_OPTION_GROUP." where id = '".$_GET['g_id']."'");  
                  $head_option_group = tep_db_fetch_array($head_option_group_raw); 
                  echo $head_option_group['name']; 
                } else {
                  echo HEADING_TITLE;
                }
                ?>
                </td>
                <td class="pageHeading" align="right">
                <div>
                <?php echo tep_draw_form('form', FILENAME_OPTION, '', 'get');?> 
                <input type="text" name="keyword" id="keyword">
                <input type="hidden" name="search" value="1">
                <?php echo tep_html_element_submit(IMAGE_SEARCH);?>
                </form> 
                </div>
                </td> 
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <div id="show_popup_info" style="background-color:#FFFF00;position:absolute;width:70%;min-width:550px;margin-left:0;display:none;"></div> 
            <div id="toggle_width" style="min-width:726px;">
            </div>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td valign="top">
                <?php
                  $option_table_params = array('width' => '100%', 'cellpadding' => '2', 'cellspacing' => '0', 'parameters' => (isset($_GET['g_id'])?'id="item_list_box"':'id="group_list_box"')); 
                  $notice_box = new notice_box('', '', $option_table_params); 
                  $option_table_row = array();
                  $option_title_row = array();
                  if (isset($_GET['g_id'])) {
                    //item列表 
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => '<input type="checkbox" name="all_check" onclick="all_select_option(\'option_item_id[]\');">');
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_NAME);
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_TITLE);
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_ITEM_TYPE);
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_ITEM_REQUIRE);
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_ITEM_CONTENT);
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_ITEM_PRICE);
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_SORT_NUM);
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_ITEM_STATUS);
                    $option_title_row[] = array('align' => 'right','params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_ACTION);
                    
                    $option_table_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $option_title_row);
                   
                    $rows = 0;
                    $item_query_raw = 'select * from '.TABLE_OPTION_ITEM.' where group_id = \''.$_GET['g_id'].'\' order by sort_num, title asc';
                    
                    $item_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $item_query_raw, $item_query_numrows);
                    $item_query = tep_db_query($item_query_raw);
                   
                    while ($item = tep_db_fetch_array($item_query)) {
                      $rows++; 
                      if ($_GET['item_id'] == $item['id']) {
                        $selected_item = $item;
                      }
                      $even = 'dataTableSecondRow';
                      $odd  = 'dataTableRow';
                      if (isset($nowColor) && $nowColor == $odd) {
                        $nowColor = $even; 
                      } else {
                        $nowColor = $odd; 
                      }
                      
                      if ( (isset($selected_item) && is_array($selected_item)) && ($item['id'] == $selected_item['id']) ) {
                        $option_item_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
                      } else {
                        $option_item_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
                      }
                      
                      $option_item_info = array(); 
                      $option_item_info[] = array(
                          'params' => 'class="dataTableContent"', 
                          'text' => '<input type="checkbox" name="option_item_id[]" value="'.$item['id'].'">' 
                          ); 
                      
                      $option_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('item_id', 'action')).'item_id='.$item['id']).'\'"', 'text' => $item['title'] 
                          ); 
                      
                      $option_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('item_id', 'action')).'item_id='.$item['id']).'\'"', 
                          'text' => $item['front_title'] 
                          ); 
                      $item_type_str = ''; 
                      if ($item['type'] == 'text') {
                        $item_type_str = OPTION_ITEM_OPTION_TEXT_TYPE; 
                      } else if ($item['type'] == 'textarea'){
                        $item_type_str = OPTION_ITEM_OPTION_TEXTAREA_TYPE; 
                      } else if ($item['type'] == 'select'){
                        $item_type_str = OPTION_ITEM_OPTION_SELECT_TYPE; 
                      } else if ($item['type'] == 'radio') {
                        $item_type_str = OPTION_ITEM_OPTION_RADIO_TYPE; 
                      }
                      
                      $option_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('item_id', 'action')).'item_id='.$item['id']).'\'"', 
                          'text' => $item_type_str 
                          ); 
                       
                      $item_option = @unserialize($item['option']);
                      if (isset($item_option['require']) && $item_option['require'] == '1') {
                        $item_require_str = TEXT_OPTION_ITEM_IS_REQUIRE; 
                      } else {
                        $item_require_str = TEXT_OPTION_ITEM_IS_NOT_REQUIRE; 
                      }
                      $option_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('item_id', 'action')).'item_id='.$item['id']).'\'"', 
                          'text' => $item_require_str 
                          ); 
                      
                      $item_content_str = ''; 
                      if (isset($item_option['itext'])) {
                       $item_content_str = $item_option['itext'];  
                      } else if (isset($item_option['itextarea'])) {
                       $item_content_str = $item_option['itextarea'];  
                      } else if (isset($item_option['se_option'])) {
                        if (is_array($item_option['se_option'])) {
                          if (!empty($item_option['se_option'])) {
                            foreach ($item_option['se_option'] as $sokey => $sovalue) {
                              $item_content_str .= $sovalue.'&nbsp;'; 
                            }
                          }
                        }
                      }
                      $option_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('item_id', 'action')).'item_id='.$item['id']).'\'"', 
                          'text' => $item_content_str 
                          ); 
                      
                      $option_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('item_id', 'action')).'item_id='.$item['id']).'\'"', 
                          'text' => $currencies->format($item['price'], true, DEFAULT_CURRENCY, '', false) 
                          ); 
                      
                      $option_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('item_id', 'action')).'item_id='.$item['id']).'\'"', 
                          'text' => $item['sort_num'] 
                          ); 
                      
                      if ($item['status'] == '1') {
                        $item_status_str = tep_image(DIR_WS_IMAGES .  'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' .  tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('item_id', 'action', 'flag')).'action=setflag&flag=0&item_id='.$item['id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                      } else {
                        $item_status_str = '<a href="' .  tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('item_id', 'action', 'flag')).'action=setflag&flag=1&item_id='.$item['id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                      }
                      $option_item_info[] = array(
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('item_id', 'action')).'item_id='.$item['id']).'\'"', 
                          'text' => $item_status_str 
                          ); 
                      $option_item_info[] = array(
                          'align' => 'right', 
                          'params' => 'class="dataTableContent"', 
                          'text' => '<a href="javascript:void(0);" onclick="show_item_info(this, \''.$item['id'].'\', \''.urlencode(tep_get_all_get_params(array('item_id', 'action'))).'\')">'.tep_image(DIR_WS_IMAGES.'icon_info.gif', IMAGE_ICON_INFO).'</a>' 
                          ); 
                      
                      $option_table_row[] = array('params' => $option_item_params, 'text' => $option_item_info);
                       
                    }
                    
                    $form_str = tep_draw_form('del_option', FILENAME_OPTION, tep_get_all_get_params(array('item_id', 'action')).'action=del_select_item');  
                    $notice_box->get_form($form_str); 
                    $notice_box->get_contents($option_table_row);
                    $notice_box->get_eof(tep_eof_hidden()); 
                    echo $notice_box->show_notice();
                  } else {
                    //group列表 
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => '<input type="checkbox" name="all_check" onclick="all_select_option(\'option_group_id[]\');">');
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_NAME);
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_TITLE);
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_GROUP_IS_PREORDER);
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_SORT_NUM);
                    
                    //判断改组是否被使用的排序连接和显示
                    $belong_arrow_str = ''; 
                    if (isset($_GET['sort_name'])) {
                      if (isset($_GET['sort_type'])) {
                        if ($_GET['sort_type'] == 'desc') {
                          $belong_arrow_str = TABLE_HEADING_OPTION_IS_USE.'<font color="c0c0c0">▲</font><font color="facb9c">▼</font>'; 
                          $belong_sort_url = tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('x', 'y', 'info', 'sort_name', 'sort_type', 'action', 'group_id')).'sort_name=is_use&sort_type=asc'); 
                        } else {
                          $belong_arrow_str = TABLE_HEADING_OPTION_IS_USE.'<font color="facb9c">▲</font><font color="c0c0c0">▼</font>'; 
                          $belong_sort_url = tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('x', 'y', 'info', 'sort_name', 'sort_type', 'action', 'group_id')).'sort_name=is_use&sort_type=desc'); 
                        }
                      } else {
                        $belong_arrow_str = TABLE_HEADING_OPTION_IS_USE.'<font color="facb9c">▲</font><font color="c0c0c0">▼</font>'; 
                        $belong_sort_url = tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('x', 'y', 'info', 'sort_name', 'sort_type', 'action', 'group_id')).'sort_name=is_use&sort_type=desc'); 
                      }
                    } else {
                      $belong_arrow_str = TABLE_HEADING_OPTION_IS_USE; 
                      $belong_sort_url = tep_href_link(FILENAME_OPTION, tep_get_all_get_params(array('x', 'y', 'info', 'sort_name', 'sort_type', 'action', 'group_id')).'sort_name=is_use&sort_type=asc'); 
                    }
                    $option_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => '<a class="title_link" href="'.$belong_sort_url.'">'.$belong_arrow_str.'</a>');
                    $option_title_row[] = array('align' => 'right','params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_OPTION_ACTION);
                    
                    $option_table_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $option_title_row);
                  
                    $rows = 0;

                    if (isset($_GET['search'])) {
                      if (isset($_GET['sort_name'])) {
                        $sort_type = isset($_GET['sort_type'])?$_GET['sort_type']:'asc'; 
                        if ($_GET['search'] == '2') {
                          $group_query_raw = 'select og.*, if((select p.products_id from products p where p.belong_to_option = og.id limit 1), 1, 0) as is_belong_to from '.TABLE_OPTION_GROUP.' og where og.name = \''.tep_replace_full_character($_GET['keyword']).'\' order by is_belong_to '.$sort_type.', og.sort_num, og.name asc';
                        } else {
                          $group_query_raw = 'select og.*, if((select p.products_id from products p where p.belong_to_option = og.id limit 1), 1, 0) as is_belong_to from '.TABLE_OPTION_GROUP.' og where og.name like \'%'.tep_replace_full_character($_GET['keyword']).'%\' order by is_belong_to '.$sort_type.', og.sort_num, og.name asc';
                        }
                      } else {
                        if ($_GET['search'] == '2') {
                          $group_query_raw = 'select * from '.TABLE_OPTION_GROUP.' where name = \''.tep_replace_full_character($_GET['keyword']).'\' order by sort_num,name asc';
                        } else {
                          $group_query_raw = 'select * from '.TABLE_OPTION_GROUP.' where name like \'%'.tep_replace_full_character($_GET['keyword']).'%\' order by sort_num,name asc';
                        }
                      }
                    } else {
                      if (isset($_GET['sort_name'])) {
                        $sort_type = isset($_GET['sort_type'])?$_GET['sort_type']:'asc'; 
                        $group_query_raw = 'select og.*, if((select p.products_id from products p where p.belong_to_option = og.id limit 1), 1, 0) as is_belong_to from '.TABLE_OPTION_GROUP.' og order by is_belong_to '.$sort_type.', og.sort_num asc ,og.name asc';
                      } else {
                        $group_query_raw = 'select * from '.TABLE_OPTION_GROUP.' order by sort_num,name asc';
                      }
                    }
                    $group_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $group_query_raw, $group_query_numrows);
                    $group_query = tep_db_query($group_query_raw);
                    
                    while ($group = tep_db_fetch_array($group_query)) {
                      $row++;
                      if ($_GET['group_id'] == $group['id']) {
                        $selected_item = $group;
                      }
                      $even = 'dataTableSecondRow';
                      $odd  = 'dataTableRow';
                      if (isset($nowColor) && $nowColor == $odd) {
                        $nowColor = $even; 
                      } else {
                        $nowColor = $odd; 
                      }
                      
                      if ( (isset($selected_item) && is_array($selected_item)) && ($group['id'] == $selected_item['id']) ) {
                        $option_group_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
                      } else {
                        $option_group_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
                      }
                      $option_group_info = array(); 
                      $option_group_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent"', 
                          'text' => '<input type="checkbox" name="option_group_id[]" value="'.$group['id'].'">' 
                          ); 
                      $option_group_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, 'page='.$_GET['page'].'&group_id='.$group['id'].(isset($_GET['keyword'])?'&keyword='.$_GET['keyword']:'').(isset($_GET['search'])?'&search='.$_GET['search']:'').(isset($_GET['sort_name'])?'&sort_name='.$_GET['sort_name']:'').(isset($_GET['sort_type'])?'&sort_type='.$_GET['sort_type']:'')).'\'"', 
                          'text' => '<a href="'.tep_href_link(FILENAME_OPTION, 'g_id='.$group['id'].(isset($_GET['page'])?'&gpage='.$_GET['page']:'').(isset($_GET['keyword'])?'&keyword='.$_GET['keyword']:'').(isset($_GET['search'])?'&search='.$_GET['search']:'').(isset($_GET['sort_name'])?'&sort_name='.$_GET['sort_name']:'').(isset($_GET['sort_type'])?'&sort_type='.$_GET['sort_type']:'')).'">'.tep_image(DIR_WS_ICONS.'folder.gif',ICON_FOLDER).'</a>'.'&nbsp;'.$group['name']
                          ); 
                      $option_group_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, 'page='.$_GET['page'].'&group_id='.$group['id'].(isset($_GET['keyword'])?'&keyword='.$_GET['keyword']:'').(isset($_GET['search'])?'&search='.$_GET['search']:'').(isset($_GET['sort_name'])?'&sort_name='.$_GET['sort_name']:'').(isset($_GET['sort_type'])?'&sort_type='.$_GET['sort_type']:'')).'\'"', 
                          'text' => $group['title'] 
                          ); 
                      $option_group_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, 'page='.$_GET['page'].'&group_id='.$group['id'].(isset($_GET['keyword'])?'&keyword='.$_GET['keyword']:'').(isset($_GET['search'])?'&search='.$_GET['search']:'').(isset($_GET['sort_name'])?'&sort_name='.$_GET['sort_name']:'').(isset($_GET['sort_type'])?'&sort_type='.$_GET['sort_type']:'')).'\'"', 
                          'text' => (($group['is_preorder'])?OPTION_GROUP_IS_PREORDER:OPTION_GROUP_IS_NOT_PREORDER) 
                          ); 
                      $option_group_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, 'page='.$_GET['page'].'&group_id='.$group['id'].(isset($_GET['keyword'])?'&keyword='.$_GET['keyword']:'').(isset($_GET['search'])?'&search='.$_GET['search']:'').(isset($_GET['sort_name'])?'&sort_name='.$_GET['sort_name']:'').(isset($_GET['sort_type'])?'&sort_type='.$_GET['sort_type']:'')).'\'"', 
                          'text' => $group['sort_num'] 
                          ); 
                      $group_is_use = false;
                      $use_group_raw = tep_db_query("select * from ".TABLE_PRODUCTS." where belong_to_option = '".$group['id']."' limit 1");
                      $use_group_res = tep_db_fetch_array($use_group_raw);
                      if ($use_group_res) {
                        $group_is_use = true;
                      }
                      $option_group_info[] = array(
                          'align' => 'left', 
                          'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_OPTION, 'page='.$_GET['page'].'&group_id='.$group['id'].(isset($_GET['keyword'])?'&keyword='.$_GET['keyword']:'').(isset($_GET['search'])?'&search='.$_GET['search']:'').(isset($_GET['sort_name'])?'&sort_name='.$_GET['sort_name']:'').(isset($_GET['sort_type'])?'&sort_type='.$_GET['sort_type']:'')).'\'"', 
                          'text' => (($group_is_use)?'Y':'N') 
                          ); 
                      $option_group_info[] = array(
                          'align' => 'right', 
                          'params' => 'class="dataTableContent"', 
                          'text' => '<a href="javascript:void(0);" onclick="show_group_info(this, \''.$group['id'].'\', \''.urlencode(tep_get_all_get_params(array('group_id', 'action'))).'\')">'.tep_image(DIR_WS_IMAGES.'icon_info.gif', IMAGE_ICON_INFO).'</a>' 
                          ); 
                      $option_table_row[] = array('params' => $option_group_params, 'text' => $option_group_info);
                    }
                    $form_str = tep_draw_form('del_option', FILENAME_OPTION, tep_get_all_get_params(array('group_id', 'action')).'action=del_select_group');  
                    $notice_box->get_form($form_str); 
                    $notice_box->get_contents($option_table_row);
                    $notice_box->get_eof(tep_eof_hidden()); 
                    echo $notice_box->show_notice();
                  }
                ?>
                </td>
              </tr>
              <tr>
                <td>
                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                      <td class="smallText" valign="top">
                      <?php 
                        if (isset($_GET['g_id'])) {
                          echo $item_split->display_count($item_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_OPTION_GROUP); 
                        } else {
                          echo $group_split->display_count($group_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_OPTION_GROUP); 
                        }
                      ?>
                      </td>
                      <td class="smallText" align="right">
                      <?php 
                        if (isset($_GET['g_id'])) {
                          echo $item_split->display_links($item_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'item_id'))); 
                        } else {
                          echo $group_split->display_links($group_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'group_id'))); 
                        }
                      ?>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="2" align="right" class="smallText">
                      <?php 
                      if (isset($_GET['g_id'])) {
                        echo '&nbsp;<a href="'.tep_href_link(FILENAME_OPTION, str_replace('gpage=', 'page=', tep_get_all_get_params(array('page', 'info', 'x', 'y', 'item_id', 'action','g_id')))).'">'.tep_html_element_button(IMAGE_BACK).'</a>'; 
                        if ($item_query_numrows) {
                          echo '&nbsp;<a href="javascript:void(0);" onclick="delete_select_option(\'option_item_id[]\');">'.tep_html_element_button(IMAGE_DELETE, 'onclick=""').'</a>'; 
                        }
                        
                        echo '&nbsp;<a href="javascript:void(0);" onclick="create_option_item(\''.$_GET['g_id'].'\', \''.urlencode(tep_get_all_get_params(array('page', 'info', 'x', 'y', 'item_id', 'action', 'g_id'))).'\')">'.tep_html_element_button(IMAGE_NEW_PROJECT, 'onclick=""').'</a>';
                      
                      } else {
                        if ($group_query_numrows) {
                          echo '<a href="javascript:void(0);" onclick="delete_select_option(\'option_group_id[]\');">'.tep_html_element_button(IMAGE_DELETE, 'onclick=""').'</a>'; 
                        }
                        echo '&nbsp;<a href="javascript:void(0);" onclick="create_option_group();">' .tep_html_element_button(IMAGE_NEW_PROJECT, 'onclick=""') . '</a>'; 
                      }
                      ?>
                      &nbsp;
                      </td>
                    </tr> 
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      </div>
      </div>
      <!-- body_text_eof -->
    </td>
  </tr>
</table>
<!-- body_eof -->
<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
