<?php
/*
   $Id$
 */
require('includes/application_top.php');
$module_total_directory = DIR_FS_CATALOG_MODULES .'order_total/';
$module_total_key = 'MODULE_ORDER_TOTAL_INSTALLED';

require(DIR_FS_ADMIN . '/classes/notice_box.php');

if (isset($_GET['action'])) {
  switch ($_GET['action']) {
  /*-----------------------------------
   case 'save_total'  更新模块
   ----------------------------------*/
    case 'save_total':
      tep_isset_eof(); 
      $post_configuration = $_POST['configuration'];
      $site_id = isset($_POST['site_id'])?(int)$_POST['site_id']:0;
      if(isset($_SESSION['site_permission'])) {
        //权限判断
        $site_arr = $_SESSION['site_permission'];
      } else {
        $site_arr = "";
      }
      forward401Unless(editPermission($site_arr, $site_id));
      
      $class = basename($_GET['module']);
      $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
      if (file_exists($module_total_directory . $class . $file_extension)) {
        include($module_total_directory . $class . $file_extension);
      }
      if(!tep_module_installed($class, $site_id)) {
        $module = new $class($site_id);
        $module->install();
      }
   
      tep_db_query("update " . TABLE_CONFIGURATION . " set user_update =  '" .  $_SESSION['user_name'] . "', last_modified = '" . date('Y-m-d H:i:s',time()) . "' where configuration_key =  'MODULE_ORDER_TOTAL_" . str_replace('OT_', '', strtoupper($_GET['module'])) . "_STATUS' and site_id = '" . $_POST['site_id'] . "'");

      $key = '';
      $value = '';
      
      foreach($post_configuration as $key => $value) {
        if (!tep_db_num_rows(tep_db_query("select * from " . TABLE_CONFIGURATION . " where configuration_key = '" . $key . "' and site_id = '" . $site_id . "'"))) {
          $cp_configuration = tep_db_fetch_array(tep_db_query("select * from " . TABLE_CONFIGURATION . " where configuration_key = '" . $key . "' and site_id = '0'"));
          if ($cp_configuration) {
            tep_db_query("
                INSERT INTO `configuration` (
                `configuration_id` ,
                `configuration_title` ,
                `configuration_key` ,
                `configuration_value` ,
                `configuration_description` ,
                `configuration_group_id` ,
                `sort_order` ,
                `last_modified` ,
                `date_added` ,
                `use_function` ,
                `set_function` ,
                `site_id`
                )
                VALUES (
                NULL , 
                '".mysql_real_escape_string($cp_configuration['configuration_title'])."', 
                '".$cp_configuration['configuration_key']."', 
                '".$cp_configuration['configuration_value']."', 
                '".mysql_real_escape_string($cp_configuration['configuration_description'])."', 
                '".$cp_configuration['configuration_group_id']."', 
                '".$cp_configuration['sort_order']."' , 
                '".$cp_configuration['last_modified']."' , 
                '".$cp_configuration['date_added']."', 
                '".mysql_real_escape_string($cp_configuration['use_function'])."' , 
                '".mysql_real_escape_string($cp_configuration['set_function'])."' , 
                '".$site_id."'
                )
              ");
          }
        }

        tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value . "' where configuration_key = '" . $key . "' and site_id = '" . $site_id . "'");
      }
      $redirect_str = '';
      if (isset($_GET['current_module'])) {
        $redirect_str = 'module='.$_GET['current_module'].(!empty($site_id)?'&site_id='.$site_id:'');  
      } else {
        $redirect_str = (!empty($site_id)?'&site_id='.$site_id:'');  
      }
      tep_redirect(tep_href_link(FILENAME_MODULE_TOTAL, $redirect_str));
      break;
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE_MODULES_ORDER_TOTAL; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/all_page.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script type="text/javascript">
var box_warp_height = 0;
var origin_offset_symbol = 0;
window.onresize = resize_total_page;
<?php //缩放?>
function resize_total_page()
{
  if ($(".box_warp").height() < ($(".compatible").height() + $("#show_popup_info").height())) {
    $(".box_warp").height($(".compatible").height() + $("#show_popup_info").height()); 
  }
}
<?php //弹出页面?>
function show_popup_info(ele, current_module, other_param_str, module_list_info) {
  ele = ele.parentNode;
  other_param_str = decodeURIComponent(other_param_str);
  origin_offset_symbol = 1;
  $.ajax({
    url: 'ajax.php?action=edit_module_total',     
    data: 'current_module='+current_module+'&list_info='+module_list_info+'&'+other_param_str,
    type: 'POST',
    dataType: 'text',
    async: false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      head_top = $('.compatible_head').height();
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
       if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
         if (ele.offsetTop < $('#show_popup_info').height()) {
           offset = ele.offsetTop+$("#total_list_box").position().top+ele.offsetHeight+head_top;
           box_warp_height = offset-head_top;
         } else {
           if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#total_list_box").position().top-1)) {
             offset = ele.offsetTop+$("#total_list_box").position().top-1-$('#show_popup_info').height()+head_top;
           } else {
             offset = ele.offsetTop+$("#total_list_box").position().top+$(ele).height()+head_top;
             offset = offset + parseInt($('#total_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
           }
           box_warp_height = offset-head_top;
         }
       } else {
        if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#total_list_box").position().top-1)) {
          offset = ele.offsetTop+$("#total_list_box").position().top-1-$('#show_popup_info').height()+head_top;
        } else {
          offset = ele.offsetTop+$("#total_list_box").position().top+$(ele).height()+head_top;
          offset = offset + parseInt($('#total_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
        }
      }
      $('#show_popup_info').css('top',offset);
      } else {
      if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
        if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#total_list_box").position().top-1)) {
          offset = ele.offsetTop+$("#total_list_box").position().top-1-$('#show_popup_info').height()+head_top;
        } else {
          offset = ele.offsetTop+$("#total_list_box").position().top+$(ele).height()+head_top;
          offset = offset + parseInt($('#total_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
        }
        box_warp_height = offset-head_top;
      } else {
        offset = ele.offsetTop+$("#total_list_box").position().top+ele.offsetHeight+head_top;
        box_warp_height = offset-head_top;
      }
      $('#show_popup_info').css('top',offset);
      }

      
      if ($('.show_left_menu').width()) {
        leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
      } else {
        leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
      }
      
      $('#show_popup_info').css('left',leftset);
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      $('#show_popup_info').show();
    }
  }); 
  if (box_warp_height < (offset+$("#show_popup_info").height())) {
    $(".box_warp").height(offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height); 
  }
}
<?php //隐藏弹出页面?>
function hidden_info_box(){
  $('#show_popup_info').html(''); 
  $('#show_popup_info').css('display','none');
  $(".box_warp").height(box_warp_height); 
}
<?php //显示信息?>
function show_module_total_info(current_module, other_param_str) {
  other_param_str = decodeURIComponent(other_param_str);
  $.ajax({
    url: 'ajax.php?action=edit_module_total',     
    data: 'current_module='+current_module+'&'+other_param_str,
    type: 'POST',
    dataType: 'text',
    async: false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      
      if (origin_offset_symbol == 1) {
        c_offset = $("#show_popup_info").css("top");
        c_offset = c_offset.replace('px', '');
        tmp_c_offset = parseInt(c_offset, 10); 
        $(".box_warp").height(tmp_c_offset+$("#show_popup_info").height()); 
      } else {
        $(".box_warp").height(box_warp_height+$("#show_popup_info").height()); 
      }
    }
  });
}
$(function() {
  box_warp_height = $(".box_warp").height();    
});
<?php //监听事件?>
$(document).ready(function() {
  $(document).keyup(function(event) {
     if (event.which == 27) {
       if ($("#show_popup_info").css("display") != "none") {
         hidden_info_box();
       }
     }
  
     if (event.which == 13) {
       if ($("#show_popup_info").css("display") != "none") {
         $("#button_save").trigger("click"); 
       }
     }
     
     if (event.ctrlKey && event.which == 37) {
       if ($("#show_popup_info").css("display") != "none") {
         if ($("#total_prev")) {
           $("#total_prev").trigger("click"); 
         }
       }
     }
     
     if (event.ctrlKey && event.which == 39) {
       if ($("#show_popup_info").css("display") != "none") {
         if ($("#total_next")) {
           $("#total_next").trigger("click"); 
         }
       }
     }
  });    
});
</script>
<?php 
$belong = FILENAME_MODULE_TOTAL;
require("includes/note_js.php");
$site_id = (isset($_GET['site_id'])?(int)$_GET['site_id']:'0');
$site_info = tep_get_sites();
$link_site = $site_info[0];
?>
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
<input type="hidden" id="show_info_id" value="show_popup_info" name="show_info_id">
<div id="show_popup_info" style="background-color:#FFFF00;position:absolute;width:70%;min-width:550px;margin-left:0;display:none;"></div> 
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
    <td width="100%" valign="top" id="categories_right_td">
      <div class="box_warp">
      <?php echo $notes;?>
      <div class="compatible">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td width="100%" height="40">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading">
                <?php echo HEADING_TITLE_MODULES_ORDER_TOTAL;?> 
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <?php echo tep_site_filter(FILENAME_MODULE_TOTAL);?> 
            <div id="toggle_width" style="min-width:726px;"></div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top">
                <?php
                  $module_total_params = array('width' => '100%', 'cellpadding' => '2', 'cellspacing' => '0', 'parameters' => 'id="total_list_box"'); 
                  $notice_box = new notice_box('', '', $module_total_params);  
                  
                  $module_total_title_row = array();

                  $module_total_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_MODULES);
                  $module_total_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => '&nbsp;');
                  $module_total_title_row[] = array('align' => 'left', 'params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_SORT_ORDER);
                  $module_total_title_row[] = array('align' => 'right', 'params' => 'class="dataTableHeadingContent" width="30"', 'text' => TABLE_HEADING_ACTION);
                  
                  $module_total_table_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $module_total_title_row);
                  
                  
                  $directory_array = array();
                  $directory_file_list_array = array();
                  $directory_file_list_str = '';
                  $installed_total_modules = array(); 
                  $directory_array_sorted = array();
                  $directory_array_tmp_sorted = array();
                  
                  if ($dir = @dir($module_total_directory)) {
                    while ($file = $dir->read()) {
                      if (!is_dir($module_total_directory . $file)) {
                        if (substr($file, strrpos($file, '.')) == '.php') {
                          $directory_array[] = $file;
                        }
                      }
                    }
                    sort($directory_array);
                    $dir->close();
                  }
                    
                  for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++) {
                    $file = $directory_array[$i];
                    include(DIR_WS_LANGUAGES . $language . '/modules/order_total/' . $file);
                    include($module_total_directory . $file);
                    $class = substr($file, 0, strrpos($file, '.'));
                    if (tep_class_exists($class)) {
                      $module = new $class;
                      $sort_order_query = tep_db_query("select * from " .  TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_".str_replace('OT_', '', strtoupper($class))."_SORT_ORDER' and site_id = '".$site_id."'"); 
                      $sort_order_info = tep_db_fetch_array($sort_order_query);
                      if ($sort_order_info) {
                        $directory_array_sorted[$sort_order_info['configuration_value']][] = $file;
                      } else {
                        $directory_array_sorted[$module->sort_order][] = $file;
                      }
                      $directory_array_tmp_sorted[$module->sort_order][] = $file;
                    }
                  }
                  
                  ksort($directory_array_tmp_sorted);
                  ksort($directory_array_sorted);
                  
                  foreach ($directory_array_sorted as $l_key => $l_files) {
                    foreach ($l_files as $tmp_l_key => $tmp_l_file) {
                      $directory_file_list_array[] = $tmp_l_file;
                    }
                  }
                 
                  if (!empty($directory_file_list_array)) {
                    $directory_file_list_str = implode('|||', $directory_file_list_array);
                  }
                 
                  foreach ($directory_array_tmp_sorted as $i_tmp_key => $i_tmp_files) {
                    foreach ($i_tmp_files as $j_tmp_key => $j_tmp_file) {
                      $tmp_class = substr($j_tmp_file, 0, strrpos($j_tmp_file, '.'));
                      if (tep_class_exists($tmp_class)) {
                        $tmp_module = new $tmp_class;
                        if ($tmp_module->check() > 0) {
                          if ($tmp_module->sort_order > 0) {
                            $installed_total_modules[$tmp_module->sort_order] = $j_tmp_file;
                          } else {
                            $installed_total_modules[] = $j_tmp_file;
                          }
                        }
                      }
                    }
                  }
                  
                  foreach ($directory_array_sorted as $i_key => $i_files) {
                    foreach ($i_files as $j_key => $j_file) {
                      $class = substr($j_file, 0, strrpos($j_file, '.'));
                      if (tep_class_exists($class)) {
                        $module = new $class;
                        
                        if (((!@$_GET['module']) || ($_GET['module'] == $class)) && (!@$mInfo)) {
                          $module_info = array(
                              'code' => $module->code,
                              'title' => $module->title,
                              'description' => $module->description,
                              'status' => $module->check()
                              );
                          $module_keys = $module->keys();

                          $keys_extra = array();
                          $get_site_id = tep_module_installed($class, $site_id) ? $site_id : 0;
                          for ($j = 0, $k = sizeof($module_keys); $j < $k; $j++) {
                            $key_value_query = tep_db_query("select configuration_title, configuration_value, configuration_description, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_key = '" . $module_keys[$j] . "' and site_id = '".$get_site_id."'");
                            $key_value = tep_db_fetch_array($key_value_query);

                            $keys_extra[$module_keys[$j]]['title'] = $key_value['configuration_title'];
                            $keys_extra[$module_keys[$j]]['value'] = $key_value['configuration_value'];
                            $keys_extra[$module_keys[$j]]['description'] = $key_value['configuration_description'];
                            $keys_extra[$module_keys[$j]]['use_function'] = $key_value['use_function'];
                            $keys_extra[$module_keys[$j]]['set_function'] = $key_value['set_function'];
                          }

                          $module_info['keys'] = $keys_extra;
                          $mInfo = new objectInfo($module_info);
                        }
                        
                        $even = 'dataTableSecondRow';
                        $odd = 'dataTableRow';
                       
                        if (isset($nowColor) && $nowColor == $odd) {
                          $nowColor = $even; 
                        } else {
                          $nowColor = $odd; 
                        }
                        
                        if ($_GET['module'] == $module->code) {
                          $module_total_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
                        } else {
                          $module_total_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
                        }
                        
                        
                        $module_total_info_row = array();
                        $module_total_table_title_info = '';
                        if (isset($module->link) && $module->link) {
                          $module_total_table_title_info .= '<a href="'.$link_site['url'].'/'.$module->link.'" target="_blank">'.tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW).'</a>';  
                        }
                        $module_total_table_title_info .= $module->title;
                        $module_total_info_row[] = array(
                            'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_MODULE_TOTAL, tep_get_all_get_params(array('action', 'module')).'module='.$module->code).'\';"',
                            'text' => $module_total_table_title_info
                            );
                        
                        $module_total_info_row[] = array(
                            'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_MODULE_TOTAL, tep_get_all_get_params(array('action', 'module')).'module='.$module->code).'\';"',
                            'text' => (($module->link)?$link_site['url'].'/'.$module->link:'')
                            );
                      
                        $show_sort_order_query = tep_db_query("select * from " .  TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_".str_replace('OT_', '', strtoupper($class))."_SORT_ORDER' and site_id = '".$site_id."'"); 
                        $show_sort_order_info = tep_db_fetch_array($show_sort_order_query);
                        $sort_order_str = '';
                        if ($show_sort_order_info) {
                          $sort_order_str = $show_sort_order_info['configuration_value']; 
                        } else {
                          $sort_order_str = (is_numeric($module->sort_order)?$module->sort_order:''); 
                        }
                        $module_total_info_row[] = array(
                            'align' => 'left', 
                            'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_MODULE_TOTAL, tep_get_all_get_params(array('action', 'module')).'module='.$module->code).'\';"',
                            'text' => $sort_order_str
                            );
                        
                        $total_date_query = tep_db_query("select * from " .  TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_".str_replace('OT_', '', strtoupper($class))."_STATUS' and site_id = '".$site_id."'"); 
                        $total_date_info = tep_db_fetch_array($total_date_query); 
                        $module_total_info_row[] = array(
                            'align' => 'right', 
                            'params' => 'class="dataTableContent"',
                            'text' => '<a href="javascript:void(0);" onclick="show_popup_info(this, \''.$module->code.'\', \''.urlencode(tep_get_all_get_params(array('action'))).'\', \''.$directory_file_list_str.'\');">'.tep_get_signal_pic_info($total_date_info['last_modified']).'</a>'
                            );
                        $module_total_table_row[] = array('params' => $module_total_params, 'text' => $module_total_info_row);  
                      }
                    }
                  }

                  $module_total_read_info[] = array(
                      'params' => 'colspan="4"', 
                      'text' => TEXT_MODULE_DIRECTORY . ' ' . $module_total_directory, 
                      ); 
                  $module_total_table_row[] = array('text' => $module_total_read_info);  
                  
                  $notice_box->get_contents($module_total_table_row);
                  echo $notice_box->show_notice();
                
                  ksort($installed_total_modules);
                  $check_query = tep_db_query("select configuration_value from " .  TABLE_CONFIGURATION . " where configuration_key = '" .  $module_total_key . "' and site_id = '0'");
                  if (tep_db_num_rows($check_query)) {
                    $check = tep_db_fetch_array($check_query);
                    if ($check['configuration_value'] != implode(';', $installed_total_modules)) {
                      tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . implode(';', $installed_total_modules) . "', last_modified = now() where configuration_key = '" . $module_total_key . "'");
                    }
                  } else {
                    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added , user_added) values ('Installed Modules', '".  $module_total_key . "', '" . implode(';', $installed_total_modules) . "', 'This is automatically updated. No need to edit.', '6', '0', now(), '".$_SESSION['user_name']."')");
                  }
                ?>
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
