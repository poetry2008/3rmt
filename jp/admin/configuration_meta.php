<?php
/*
   $Id$
 */
require('includes/application_top.php');
require(DIR_FS_ADMIN . '/classes/notice_box.php');

if (isset($_GET['site_id']) && ($_GET['site_id'] != '')) {
  $sql_site_where = 'cm.site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
  $show_list_array = explode('-', $_GET['site_id']);
} else {
  $show_list_str = tep_get_setting_site_info(FILENAME_CONFIGURATION_META);
  $sql_site_where = 'cm.site_id in ('.$show_list_str.')';
  $show_list_array = explode(',', $show_list_str);
}

if(isset($_GET['site_id']) && ($_GET['site_id'] == '')){
  $_GET['site_id'] = str_replace(',', '-', tep_get_setting_site_info(FILENAME_CONFIGURATION_META));
}

if (isset($_GET['action'])) {
  switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'update_meta_info' 更新信息    
   case 'copy_meta_info' 复制信息    
------------------------------------------------------*/
    case 'update_meta_info':
      tep_isset_eof();  
      $meta_robots_str = ''; 
      if (isset($_POST['meta_robots'])) {
        $meta_robots_str = $_POST['meta_robots']; 
      }
      $sql_data_array = array(
         'meta_title' => tep_db_prepare_input($_POST['meta_title']), 
         'meta_keywords' => tep_db_prepare_input($_POST['meta_keywords']), 
         'meta_description' => tep_db_prepare_input($_POST['meta_description']), 
         'meta_robots' => tep_db_prepare_input($meta_robots_str), 
         'meta_copyright' => tep_db_prepare_input($_POST['meta_copyright']), 
         'user_update' => tep_db_prepare_input($_SESSION['user_name']), 
         'last_modified' => 'now()', 
      ); 
      tep_db_perform(TABLE_CONFIGURATION_META, $sql_data_array , 'update', 'id = \''.tep_db_prepare_input($_GET['meta_e_id']).'\''); 
      tep_redirect(tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('meta_e_id', 'site_id', 'action')))); 
      break;
    case 'copy_meta_info':
      tep_isset_eof();  
      $meta_info_raw = tep_db_query("select * from ".TABLE_CONFIGURATION_META." where id = '".$_GET['meta_e_id']."'"); 
      if (!tep_db_num_rows($meta_info_raw)) {
        forward404(); 
      }
      $meta_info_res = tep_db_fetch_array($meta_info_raw); 
      if (isset($_POST['select_site'])) {
        $sql_data_array = array(
         'meta_title' => tep_db_prepare_input($meta_info_res['meta_title']), 
         'meta_keywords' => tep_db_prepare_input($meta_info_res['meta_keywords']), 
         'meta_description' => tep_db_prepare_input($meta_info_res['meta_description']), 
         'meta_robots' => tep_db_prepare_input($meta_info_res['meta_robots']), 
         'meta_copyright' => tep_db_prepare_input($meta_info_res['meta_copyright']), 
         'user_update' => tep_db_prepare_input($_SESSION['user_name']), 
         'last_modified' => 'now()', 
        );
        $site_list_str = implode(',', $_POST['select_site']); 
        $meta_list_raw = tep_db_query("select * from ".TABLE_CONFIGURATION_META." where site_id in (".$site_list_str.") and key_info = '".$meta_info_res['key_info']."'"); 
        while ($meta_list_res = tep_db_fetch_array($meta_list_raw)) {
          tep_db_perform(TABLE_CONFIGURATION_META, $sql_data_array , 'update', 'id = \''.tep_db_prepare_input($meta_list_res['id']).'\''); 
        }
      }
      tep_redirect(tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('meta_e_id', 'site_id', 'action')))); 
      break;
  }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/all_page.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script type="text/javascript">
var box_warp_height = 0;
var origin_offset_symbol = 0;
var o_submit_single = true;
window.onresize = resize_meta_page;
<?php //缩放页面?>
function resize_meta_page()
{
  var s_offset = $('#show_popup_info').css('top'); 
  s_offset = s_offset.replace('px', '');
  tmp_s_offset = parseInt(s_offset, 10)
  if ($('#show_popup_info').height() + tmp_s_offset > $('.box_warp').height()) {
    $('.box_warp').height($('#show_popup_info').height() + tmp_s_offset); 
  }
}

<?php //关闭弹出页面?>
function close_meta_info()
{
  $('#show_popup_info').html('');
  $('#show_popup_info').css('display', 'none');
  $('#show_popup_info').css('top', '');
  $('.box_warp').height('');
}

<?php //提交表单?>
function submit_meta_form()
{
  <?php
  if ($ocertify->npermission > 15) {
  ?>
  document.forms.meta_form.submit();
  <?php
  } else {
  ?>
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      if (tmp_msg_arr[0] == '0') {
        document.forms.meta_form.submit();
      } else {
        $("#button_save").attr('id', 'tmp_button_save'); 
        var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
        if (in_array(input_pwd_str, pwd_list_array)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.meta_form.action),
            async: false,
            success: function(msg_info) {
              document.forms.meta_form.submit();
            }
          }); 
        } else {
          alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
          setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
        }
      }
    }
  }); 
  <?php
  }
  ?>
}

<?php //弹出页面?>
function show_meta_info(ele, meta_id, param_str)
{
  ele = ele.parentNode;
  param_str = decodeURIComponent(param_str);
  origin_offset_symbol = 1;
  $.ajax({
    url: 'ajax.php?action=edit_meta_info',      
    data: 'meta_e_id='+meta_id+'&'+param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
       if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
         if (ele.offsetTop < $('#show_popup_info').height()) {
           offset = ele.offsetTop+$("#meta_list_box").position().top+ele.offsetHeight;
           box_warp_height = offset;
         } else {
           if (((ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop-$("#meta_list_box").position().top-1)) {
             offset = ele.offsetTop+$("#meta_list_box").position().top-1-$('#show_popup_info').height();
           } else {
             offset = ele.offsetTop+$("#meta_list_box").position().top+$(ele).height();
             offset = offset + parseInt($('#meta_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
           }
           box_warp_height = offset;
         }
       } else {
        if (((ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop-$("#meta_list_box").position().top-1)) {
          offset = ele.offsetTop+$("#meta_list_box").position().top-1-$('#show_popup_info').height();
        } else {
          offset = ele.offsetTop+$("#meta_list_box").position().top+$(ele).height();
          offset = offset + parseInt($('#meta_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
        }
      }
      $('#show_popup_info').css('top',offset);
      } else {
      if ((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop) {
        if (((ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop-$("#meta_list_box").position().top-1)) {
          offset = ele.offsetTop+$("#meta_list_box").position().top-1-$('#show_popup_info').height();
        } else {
          offset = ele.offsetTop+$("#meta_list_box").position().top+$(ele).height();
          offset = offset + parseInt($('#meta_list_box').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
        }
        box_warp_height = offset;
      } else {
        offset = ele.offsetTop+$("#meta_list_box").position().top+ele.offsetHeight;
        box_warp_height = offset;
      }
      $('#show_popup_info').css('top',offset);
      }
      $('#show_popup_info').show(); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      o_submit_single = true;
    }
  });
  if (box_warp_height < (offset+$("#show_popup_info").height())) {
    $(".box_warp").height(offset+$("#show_popup_info").height()); 
  } else {
    $(".box_warp").height(box_warp_height); 
  }
}

<?php //显示meta信息?>
function show_link_meta_info(meta_id, other_param)
{
  other_param = decodeURIComponent(other_param);
  $.ajax({
    url: 'ajax.php?action=edit_meta_info',      
    data: 'meta_e_id='+meta_id+'&'+other_param,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      $('#show_popup_info').show(); 
      o_submit_single = true;
    
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
<?php //复制meta?>
function copy_meta(meta_id, other_param)
{
  other_param = decodeURIComponent(other_param);
  $.ajax({
    url: 'ajax.php?action=copy_meta_info',      
    data: 'meta_e_id='+meta_id+'&'+other_param,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      data_info_array = data.split('||||||'); 
      $('#show_popup_info').html(data_info_array[0]); 
      $('#show_popup_info').css('z-index', data_info_array[1]); 
      $('#show_popup_info').show(); 
      o_submit_single = true;
    } 
  });
}

<?php //检查form?>
function check_copy_meta()
{
  var check_flag = true; 
  if (document.copy_meta.elements['select_site[]']) {
    if (document.copy_meta.elements['select_site[]'].length == null) {
      if (document.copy_meta.elements['select_site[]'].checked == true) {
        check_flag = false;
      }
    } else {
      for (var i = 0; i < document.copy_meta.elements['select_site[]'].length; i++) {
        if (document.copy_meta.elements['select_site[]'][i].checked == true) {
          check_flag = false; 
        }
      }
    }
  } 
  if (check_flag == false) {
  <?php
  if ($ocertify->npermission > 15) {
  ?>
  document.forms.copy_meta.submit();
  <?php
  } else {
  ?> 
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      if (tmp_msg_arr[0] == '0') {
        document.forms.copy_meta.submit();
      } else {
        $("#button_save").attr('id', 'tmp_button_save'); 
        var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
        if (in_array(input_pwd_str, pwd_list_array)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.copy_meta.action),
            async: false,
            success: function(msg_info) {
              document.forms.copy_meta.submit();
            }
          }); 
        } else {
          alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
          setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
        }
      }
    }
  });
  <?php 
  }
  ?>
  } else {
    $('#site_error').html('<?php echo META_INFO_SELECT_SITE_WARING;?>'); 
  }
}

$(function() {
  box_warp_height = $('.box_warp').height();    
});

<?php //监听事件?>
$(document).ready(function() {
  $(document).keyup(function(event) {
     if (event.which == 27) {
       if ($("#show_popup_info").css("display") != "none") {
         close_meta_info();
         o_submit_single = true;
       }
     }
  
     if (event.which == 13) {
       if ($("#show_popup_info").css("display") != "none") {
         if (o_submit_single) {
           $("#button_save").trigger("click"); 
         }
       }
     }
     
     if (event.ctrlKey && event.which == 37) {
       if ($("#show_popup_info").css("display") != "none") {
         if ($("#meta_prev")) {
           $("#meta_prev").trigger("click"); 
         }
       }
     }
     
     if (event.ctrlKey && event.which == 39) {
       if ($("#show_popup_info").css("display") != "none") {
         if ($("#meta_next")) {
           $("#meta_next").trigger("click"); 
         }
       }
     }
  });    
});
</script>
<?php 
$belong = FILENAME_CONFIGURATION_META;
require("includes/note_js.php");
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
  one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
</script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
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
            <?php echo tep_draw_form('search', FILENAME_CONFIGURATION_META, '', 'get');?> 
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading">
                <?php echo HEADING_TITLE;?> 
                </td>
                <td class="smallText" align="right">
                <?php echo tep_draw_input_field('search', (isset($_GET['search'])?$_GET['search']:''));?>
                <input type="submit" value="<?php echo IMAGE_SEARCH;?>">
                </td>
              </tr>
            </table>
            </form>
          </td>
        </tr>
        <tr>
          <td>
            <?php
              echo tep_show_site_filter(FILENAME_CONFIGURATION_META, false, array(0)); 
            ?>
            <div id="show_popup_info" style="background-color:#FFFF00;position:absolute;width:70%;min-width:550px;margin-left:0;display:none;"></div> 
            <div id="toggle_width" style="min-width:726px;"></div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top">
                <?php
                  $meta_table_site_str = '';
                  $meta_table_title_str = '';
                  $meta_table_url_str = '';
                  $meta_table_operate_str = '';
                  $meta_order_sort_name = ' cm.site_id'; 
                  $meta_order_sort = 'asc'; 
                 
                  if (isset($_GET['meta_sort'])) {
                    if ($_GET['meta_sort_type'] == 'asc') {
                      $type_str = '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>'; 
                      $tmp_type_str = 'desc'; 
                    } else {
                      $type_str = '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>'; 
                      $tmp_type_str = 'asc'; 
                    }
                    
                    switch ($_GET['meta_sort']) {
                      case 'meta_site':
                          $meta_table_site_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_site&meta_sort_type='.$tmp_type_str).'">'.TABLE_META_SITE_TEXT.$type_str.'</a>';
                          $meta_table_title_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_title&meta_sort_type=desc').'">'.TABLE_META_TITLE_TEXT.'</a>';
                          $meta_table_url_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_url&meta_sort_type=desc').'">'.TABLE_META_URL_TEXT.'</a>';
                          $meta_table_operate_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_update&meta_sort_type=desc').'">'.TABLE_META_OPERATE_TEXT.'</a>';
                          $meta_order_sort_name = 's.romaji'; 
                          break;
                      case 'meta_title':
                          $meta_table_site_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_site&meta_sort_type=desc').'">'.TABLE_META_SITE_TEXT.'</a>';
                          $meta_table_title_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_title&meta_sort_type='.$tmp_type_str).'">'.TABLE_META_TITLE_TEXT.$type_str.'</a>';
                          $meta_table_url_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_url&meta_sort_type=desc').'">'.TABLE_META_URL_TEXT.'</a>';
                          $meta_table_operate_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_update&meta_sort_type=desc').'">'.TABLE_META_OPERATE_TEXT.'</a>';
                          $meta_order_sort_name = 'cm.title'; 
                          break;
                      case 'meta_url':
                          $meta_table_site_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_site&meta_sort_type=desc').'">'.TABLE_META_SITE_TEXT.'</a>';
                          $meta_table_title_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_title&meta_sort_type=desc').'">'.TABLE_META_TITLE_TEXT.'</a>';
                          $meta_table_url_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_url&meta_sort_type='.$tmp_type_str).'">'.TABLE_META_URL_TEXT.$type_str.'</a>';
                          $meta_table_operate_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_update&meta_sort_type=desc').'">'.TABLE_META_OPERATE_TEXT.'</a>';
                          $meta_order_sort_name = 'cm.link_url'; 
                          break;
                      case 'meta_update':
                          $meta_table_site_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_site&meta_sort_type=desc').'">'.TABLE_META_SITE_TEXT.'</a>'; 
                          $meta_table_title_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_title&meta_sort_type=desc').'">'.TABLE_META_TITLE_TEXT.'</a>'; 
                          $meta_table_url_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_url&meta_sort_type=desc').'">'.TABLE_META_URL_TEXT.'</a>';
                          $meta_table_operate_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_update&meta_sort_type='.$tmp_type_str).'">'.TABLE_META_OPERATE_TEXT.$type_str.'</a>';
                          $meta_order_sort_name = 'cm.last_modified'; 
                          break;
                    }
                  } 
                  
                  if (isset($_GET['meta_sort_type'])) {
                    if ($_GET['meta_sort_type'] == 'asc') {
                      $meta_order_sort = 'asc'; 
                    } else {
                      $meta_order_sort = 'desc'; 
                    }
                  }
                  
                  $meta_order_sql = $meta_order_sort_name.' '.$meta_order_sort;
                  
                  if (!isset($_GET['meta_sort_type'])) {
                    $meta_table_site_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_site&meta_sort_type=desc').'">'.TABLE_META_SITE_TEXT.'</a>'; 
                    $meta_table_title_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_title&meta_sort_type=desc').'">'.TABLE_META_TITLE_TEXT.'</a>'; 
                    $meta_table_url_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_url&meta_sort_type=desc').'">'.TABLE_META_URL_TEXT.'</a>';
                    $meta_table_operate_str = '<a href="'.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'meta_id', 'site_id', 'meta_sort', 'meta_sort_type')).'meta_sort=meta_update&meta_sort_type=desc').'">'.TABLE_META_OPERATE_TEXT.'</a>';
                  }
                 
                  $meta_info_params = array('width' => '100%', 'cellpadding' => '2', 'cellspacing' => '0', 'parameters' => 'id="meta_list_box"');
                  $notice_box = new notice_box('', '', $meta_info_params); 
                  
                  $meta_table_title_row = array();
                  $meta_table_info_row = array();
                   
                  $meta_table_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => '<input type="checkbox" name="all_meta_select" disabled="disabled">');
                  $meta_table_title_row[] = array('params' => 'class="dataTableHeadingContent_order"', 'text' => $meta_table_site_str);
                  $meta_table_title_row[] = array('params' => 'class="dataTableHeadingContent_order"', 'text' => $meta_table_title_str);
                  $meta_table_title_row[] = array('params' => 'class="dataTableHeadingContent_order"', 'text' => $meta_table_url_str);
                  $meta_table_title_row[] = array('params' => 'class="dataTableHeadingContent_order" width="53"', 'text' => $meta_table_operate_str);
                 
                  $meta_table_info_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $meta_table_title_row); 
                  
                  if (isset($_GET['search'])) {
                    $meta_list_query_raw = "select cm.*, s.romaji, s.url from ".TABLE_CONFIGURATION_META." cm, ".TABLE_SITES." s where (title like '%".trim($_GET['search'])."%' or meta_title like '%".$_GET['search']."%' or meta_keywords like '%".trim($_GET['search'])."%' or meta_description like '%".trim($_GET['search'])."%' or meta_copyright like '%".trim($_GET['search'])."%') and cm.site_id = s.id and ".$sql_site_where." order by ".$meta_order_sql;
                  } else {
                    $meta_list_query_raw = "select cm.*, s.romaji, s.url from ".TABLE_CONFIGURATION_META." cm, ".TABLE_SITES." s where cm.site_id = s.id and ".$sql_site_where." order by ".$meta_order_sql;
                  }
                  $meta_list_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $meta_list_query_raw, $meta_list_query_numrows); 
                  $meta_list_query = tep_db_query($meta_list_query_raw); 
                  
                  while ($meta_list_info = tep_db_fetch_array($meta_list_query)) {
                    $meta_list_row = array();
                    $even = 'dataTableSecondRow';
                    $odd = 'dataTableRow';
                    if (isset($nowColor) && $nowColor == $odd) {
                      $nowColor = $even; 
                    } else {
                      $nowColor = $odd; 
                    }
                    if ($_GET['meta_id'] == $meta_list_info['id']) {
                      $meta_list_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\';"'; 
                    } else {
                      $meta_list_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\'; this.style.cursor=\'hand\';" onmouseout="this.className=\''.$nowColor.'\';"'; 
                    }
                  
                    $meta_list_row[] = array(
                        'params' => 'class="dataTableContent"',
                        'text' => '<input type="checkbox" name="meta_list_id[]" disabled="disabled">'
                        ); 
                    
                    $meta_list_row[] = array(
                        'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'site_id', 'meta_id')).'meta_id='.$meta_list_info['id']).'\';"',
                        'text' => $meta_list_info['romaji'] 
                        ); 
                    
                    $meta_list_row[] = array(
                        'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'site_id', 'meta_id')).'meta_id='.$meta_list_info['id']).'\';"',
                        'text' => '<a href="'.$meta_list_info['url'].'/'.$meta_list_info['link_url'].'" target="_blank">'.tep_image(DIR_WS_ICONS.'preview.gif', ICON_PREVIEW).'</a>&nbsp;'.$meta_list_info['title'] 
                        ); 
                    
                    $meta_list_row[] = array(
                        'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CONFIGURATION_META, tep_get_all_get_params(array('action', 'site_id', 'meta_id')).'meta_id='.$meta_list_info['id']).'\';"',
                        'text' => '/'.$meta_list_info['link_url'] 
                        ); 
                    
                    $meta_list_row[] = array(
                        'params' => 'class="dataTableContent"',
                        'text' => '<a href="javascript:void(0);" onclick="show_meta_info(this, \''.$meta_list_info['id'].'\', \''.urlencode(tep_get_all_get_params(array('action', 'site_id'))).'\')">'.tep_get_signal_pic_info($meta_list_info['last_modified']).'</a>' 
                        ); 
                    $meta_table_info_row[] = array('params' => $meta_list_params, 'text' => $meta_list_row); 
                  }
                  $notice_box->get_contents($meta_table_info_row);
                  echo $notice_box->show_notice();
                  if (!$meta_list_query_numrows) {
                    echo '<font color="#ff0000"><b>'.TEXT_DATA_IS_EMPTY.'</b></font>'; 
                  }
                ?>
                </td>
              </tr>
              <tr>
                <td>
                  <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
                    <?php
                      if ($ocertify->npermission >= 15) {
                    ?>
                    <tr>
                      <td colspan="2">
                      <select name="meta_list_action" disabled="disabled">
                        <option value="0"><?php echo META_LIST_SELECT_ACTION;?></option> 
                      </select>
                      </td>
                    </tr>
                    <?php
                      } 
                    ?>
                    <tr>
                      <td class="smallText" valign="top">
                      <?php   
                        echo $meta_list_split->display_count($meta_list_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_META_LIST); 
                      ?> 
                      </td>
                      <td class="smallText" align="right">
                      <div class="td_box">
                      <?php 
                        echo $meta_list_split->display_links($meta_list_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'meta_id', 'site_id'))); 
                      ?>
                      </div>
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
