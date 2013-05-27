<?php
require('includes/application_top.php');
require(DIR_FS_ADMIN . 'classes/notice_box.php');
if (isset($_GET['log']) && $_GET['log'] == 'id_manager_log') {
  define('MAX_DISPLAY_PW_MANAGER_LOG_RESULTS',20);
  if($ocertify->npermission < 15){
    forward404();
  }
  if(isset($_GET['pw_id'])&&$_GET['pw_id']){
    $pwid = tep_db_prepare_input($_GET['pw_id']);
  }
  if(isset($_GET['site_id'])&&$_GET['site_id']){
    $site_id = tep_db_prepare_input($_GET['site_id']);
  }else{
    $site_id = '';
  }
  if(isset($_GET['pw_l_id'])&&$_GET['pw_l_id']){
    $pwlid = tep_db_prepare_input($_GET['pw_l_id']);
  }
  if (isset($_GET['action']) && $_GET['action']) {
    $user_info = tep_get_user_info($ocertify->auth_user);
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'deleteconfirm' 删除idpw的历史记录      
------------------------------------------------------*/
      case 'deleteconfirm':
        if(!empty($_POST['pw_manager_log_id'])){
        foreach ($_POST['pw_manager_log_id'] as $ge_key => $ge_value){
        tep_db_query("delete from " . TABLE_IDPW_LOG . " where id = '" . $ge_value . "'");
         }
        }
        if(tep_has_pw_manager_log($pwid)){
        if($_GET['select']=='all'){
        tep_db_query("delete from " . TABLE_IDPW_LOG. " where idpw_id='".$pwid."'");
        }else{
        tep_db_query("delete from " . TABLE_IDPW_LOG . " where id = '" .tep_db_input($pwlid) . "'");
        }
        }
        tep_redirect(tep_href_link(FILENAME_PW_MANAGER, 'log='.$_GET['log'].'&page='. $_GET['page'].'&pw_id='.$pwid.'&site_id='.$site_id));
        break;
    }
  }
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  # 永远是改动过的
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TEXT_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery.form.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" >
<?php //执行动作?>
function toggle_idpw_log_action(idpwd_url_str, c_permission)
{
  if (c_permission == 31) {
    window.location.href = idpwd_url_str; 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      async: false,
      success: function(msg) {
        pwd_list_array = msg.split(','); 
        var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
        if (in_array(input_pwd_str, pwd_list_array)) {
          window.location.href = idpwd_url_str; 
        } else {
          alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
        }
      }
    });
  }
}
<?php //AJAX弹出页面?>
function show_pw_manager_log(ele,pw_id,page,site_id,pw_l_id){
 sort = document.getElementById('pw_manager_sort').value;
 type = document.getElementById('pw_manager_type').value;
 search_type = document.getElementById('pw_manager_search_type').value;
 keywords = document.getElementById('pw_manager_keywords').value;
 $.ajax({
 url: 'ajax.php?&action=edit_pw_manager_log',
 data: {pw_id:pw_id,page:page,site_id:site_id,sort:sort,type:type,pw_l_id:pw_l_id,keywords:keywords,search_type:search_type} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_pw_manager_log").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_pw_manager_log').height()){
offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_pw_manager_log').height()) > $('.box_warp').height())&&($('#show_pw_manager_log').height()<ele.offsetTop+parseInt(head_top)-$("#orders_list_table").position().top-1)) {
offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#show_pw_manager_log').height()+head_top;
} else {
offset = ele.offsetTop+$("#orders_list_table").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#orders_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_pw_manager_log').height()) > $('.box_warp').height())&&($('#show_pw_manager_log').height()<ele.offsetTop+parseInt(head_top)-$("#orders_list_table").position().top-1)) {
    offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#show_pw_manager_log').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#orders_list_table").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#orders_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_pw_manager_log').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_pw_manager_log').height()) > $('.box_warp').height())&&($('#show_pw_manager_log').height()<ele.offsetTop+parseInt(head_top)-$("#orders_list_table").position().top-1)) {
      offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#show_pw_manager_log').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#orders_list_table").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#orders_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_pw_manager_log').css('top',offset);
}
box_warp_height = box_warp_height + $('#show_pw_manager_log').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
$('#show_pw_manager_log').css('z-index','1');
$('#show_pw_manager_log').css('left',leftset);
$('#show_pw_manager_log').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
<?php //是否确认删除数据?>
function delete_select_pw_manager(pw_manager_str, c_permission){
         sel_num = 0;
         if (document.del_pw_manager_log.elements[pw_manager_str].length == null) {
                if (document.del_pw_manager_log.elements[pw_manager_str].checked == true){
                     sel_num = 1;
                 }
          } else {
             for (i = 0; i < document.del_pw_manager_log.elements[pw_manager_str].length; i++) {
                 if(document.del_pw_manager_log.elements[pw_manager_str][i].checked == true) {
                     sel_num = 1;
                     break;
                  }
            }
         }
         if (sel_num == 1) {
          if (confirm('<?php echo TEXT_DEL_PW_MANAGER;?>')) {
            if (c_permission == 31) {
              document.forms.del_pw_manager_log.submit(); 
            } else {
              $.ajax({
                url: 'ajax_orders.php?action=getallpwd',   
                type: 'POST',
                dataType: 'text',
                async: false,
                success: function(msg) {
                  pwd_list_array = msg.split(','); 
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    document.forms.del_pw_manager_log.submit(); 
                  } else {
                    document.getElementsByName('pw_manager_action')[0].value = 0;
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                  }
                }
              });
            }
          }else{
             document.getElementsByName('pw_manager_action')[0].value = 0;
          }
         } else {
                 document.getElementsByName('pw_manager_action')[0].value = 0;
                 alert('<?php echo TEXT_PW_MANAGER_MUST_SELECT;?>'); 
        }
    }
<?php //选择动作?>
function pw_manager_change_action(r_value, r_str) {
  if (r_value == '1') {
    delete_select_pw_manager(r_str, '<?php echo $ocertify->npermission;?>');
  }
}
$(document).ready(function() {
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_pw_manager_log').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           <?php //回车?>
        if ($('#show_pw_manager_log').css('display') != 'none') {
            if (o_submit_single){
               $("#show_pw_manager_log").find('input:submit').first().trigger("click");
             }
            }
        }

     if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_pw_manager_log').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_pw_manager_log').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});
<?php //关闭弹出页面?>
function hidden_info_box(){
   $('#show_pw_manager_log').css('display','none');
}
<?php //搜索表单提交?>
function search_type_changed(elem){
   if ($('#keywords').val() && elem.selectedIndex != 0) 
        document.forms.orders1.submit();
}
<?php //选择删除全部数据?>
function all_select_pw_manager_log(pw_manager_str){
        var check_flag = document.del_pw_manager_log.all_check.checked;
           if (document.del_pw_manager_log.elements[pw_manager_str]) {
              if (document.del_pw_manager_log.elements[pw_manager_str].length == null){
                   if (check_flag == true) {
                         document.del_pw_manager_log.elements[pw_manager_str].checked = true;
                    } else {
                       document.del_pw_manager_log.elements[pw_manager_str].checked = false;
                     }
               } else {
                 for (i = 0; i < document.del_pw_manager_log.elements[pw_manager_str].length; i++){
                      if (check_flag == true) {
                           document.del_pw_manager_log.elements[pw_manager_str][i].checked = true;
                      } else {
                           document.del_pw_manager_log.elements[pw_manager_str][i].checked = false;
                      }
                  }
              }
        }
}

</script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header -->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof -->
<!-- body -->
<input type="hidden" id="show_info_id" value="show_pw_manager_log" name="show_info_id">
<div id="show_pw_manager_log" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display: none; top: 212px; z-index: 1; left: 22px;"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
<?php
    echo '<td width="' . BOX_WIDTH . '" valign="top">';
    echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
    echo '<!-- left_navigation -->';
    require(DIR_WS_INCLUDES . 'column_left.php');
    echo '<!-- left_navigation_eof -->';
    echo '</table>';
    echo '</td>';
?>
<!-- body_text -->
<td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><div class="compatible"><?php echo $notes;?><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td width="100%" colspan='2'>
  
  <table border="0" width="100%" cellspacing="2" cellpadding="0">
    <tr>
      <td class="pageHeading" height="40"><?php echo HEADING_TITLE; ?></td>
      <td align="right" class="smallText">
        <table width=""  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td class="smallText" valign='top'>
              <?php echo tep_draw_form('orders1', FILENAME_PW_MANAGER, '', 'get','id="orders1" onsubmit="return false"'); ?><?php echo IMAGE_SEARCH;?> : 
              <input name="keywords" type="text" id="keywords" size="40" value="<?php if(isset($_GET['keywords'])) echo stripslashes($_GET['keywords']); ?>">
              <select name="search_type" onChange='search_type_changed(this)'>
                <option value="none"><?php echo PW_MANAGER_SELECT_NONE;?></option>
                <option value="priority"><?php echo PW_MANAGER_SELECT_ONE;?></option>
                <option value="loginurl"><?php echo PW_MANAGER_SELECT_TWO;?></option>
                <option value="title"><?php echo PW_MANAGER_SELECT_THREE;?></option>
                <option value="url"><?php echo PW_MANAGER_SELECT_FOUR;?></option>
                <option value="username"><?php echo PW_MANAGER_SELECT_FIVE;?></option>
                <option value="password"><?php echo PW_MANAGER_SELECT_SIX;?></option>
                <option value="operator"><?php echo PW_MANAGER_SELECT_SEVEN;?></option>
                <option value="comment"><?php echo PW_MANAGER_SELECT_EIGHT;?></option>
                <option value="memo"><?php echo PW_MANAGER_SELECT_NINE;?></option>
              </select>
              </form>
            </td>
            <td valign="top"></td>
          </tr>
        </table>
      </td>
      <td align="right">
      </td>
    </tr>
  </table>

      </td>
    </tr>
    <tr>
      <td valign="top">
    <table width="100%">
      <tr>
        <td>
    <?php tep_site_filter(FILENAME_PW_MANAGER);?>
        </td>
        <td align="right">
        </td>
      </tr>
    </table>
    <?php
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
    ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2" id='orders_list_table'>
      <tr>
       <td>
       <?php 
       echo'<input type="hidden" id="pw_manager_sort" value="'.$_GET['sort'].'"><input type="hidden" id="pw_manager_type" value="'.$_GET['type'].'"><input type="hidden" id="pw_manager_keywords" value="'.$_GET['keywords'].'"><input type="hidden" id="pw_manager_search_type" value="'.$_GET['search_type'].'">';
       $manager_table_params = array('width'=>'100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
       $notice_box = new notice_box('','',$manager_table_params);
       $manager_table_row = array();
       $manager_title_row = array();
       $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<input type="checkbox" name="all_check" onclick="all_select_pw_manager_log(\'pw_manager_log_id[]\');">');
      if ($HTTP_GET_VARS['sort'] == 'priority') {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=priority&type='.$type_str).'">'.TEXT_PRIORITY.'</a> ');
      } else {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=priority&type=asc').'">'.TEXT_PRIORITY.'</a> ');
      }
      if ($HTTP_GET_VARS['sort'] == 'loginurl') {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=loginurl&type='.$type_str).'">'.TEXT_LOGINURL.'</a>');
      } else {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=loginurl&type=asc').'">'.TEXT_LOGINURL.'</a> ');
      }
      if ($HTTP_GET_VARS['sort'] == 'title') {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=title&type='.$type_str).'">'.TEXT_INFO_TITLE.'</a>');
      } else {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=title&type=asc').'">'.TEXT_INFO_TITLE.'</a>');
      }
      if ($HTTP_GET_VARS['sort'] == 'username') {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.tep_href_link(FILENAME_PW_MANAGER, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=username&type='.$type_str).'">'.TEXT_USERNAME.'</a>');
      } else {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link(FILENAME_PW_MANAGER, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=username&type=asc').'">'.TEXT_USERNAME.'</a>');
      }
      if ($HTTP_GET_VARS['sort'] == 'password') {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link(FILENAME_PW_MANAGER, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=password&type='.$type_str).'">'.TEXT_PASSWORD.'</a>');
      } else {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link(FILENAME_PW_MANAGER, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=password&type=asc').'">'.TEXT_PASSWORD.'</a>');
      }
      if ($HTTP_GET_VARS['sort'] == 'operator') {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link(FILENAME_PW_MANAGER, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=operator&type='.$type_str).'">'.TEXT_PRIVILEGE.'</a>');
      } else {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link(FILENAME_PW_MANAGER, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=operator&type=asc').'">'.TEXT_PRIVILEGE.'</a>');
      }
      if ($HTTP_GET_VARS['sort'] == 'nextdate') {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link(FILENAME_PW_MANAGER, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=nextdate&type='.$type_str).'">'.TEXT_NEXTDATE.'</a>');
      } else {
        $manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.tep_href_link(FILENAME_PW_MANAGER, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=nextdate&type=asc').'">'.TEXT_NEXTDATE.'</a>');
      }
       $manager_title_row[] = array('params' => 'class="dataTableHeadingContent" align="right"','text' => TABLE_HEADING_ACTION);
       $manager_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $manager_title_row);
    if(isset($site_id)&&$site_id){
    $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."nextdate,privilege,operator,created_at,
                             updated_at,onoff,update_user from
                             ".TABLE_IDPW_LOG." where site_id='".$site_id."'
                             order by ".$order_str;
    }else if(isset($_GET['search_type'])&&$_GET['search_type']&&
        isset($_GET['keywords'])&&$_GET['keywords']){
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
    $pw_manager_split = new splitPageResults($_GET['page'],
        MAX_DISPLAY_PW_MANAGER_LOG_RESULTS, $pw_manager_query_raw, $pw_manager_query_numrows);
       
    $pw_manager_query = tep_db_query($pw_manager_query_raw);
    $pw_manager_numrows = tep_db_num_rows($pw_manager_query);
    while($pw_manager_row = tep_db_fetch_array($pw_manager_query)){
      if (( (!@$_GET['pw_l_id']) || (@$_GET['pw_l_id'] == $pw_manager_row['id'])) && (!@$pwInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $pwInfo = new objectInfo($pw_manager_row);
    }
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
    if (isset($pwInfo) && (is_object($pwInfo)) && ($pw_manager_row['id'] == $pwInfo->id) ) {
        $manager_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"' . "\n";
    } else {
        $manager_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }
      $priority_str = "<font color='";
      switch($pw_manager_row['priority']){
        case '1':
            $priority_str .="black";
          break;
        case '2':
            $priority_str .="orange";
          break;
        case '3':
            $priority_str .="red";
          break;

      }
      $priority_str .= "' >".$pw_manager_row['priority']."</font>";
      $manager_info = array();
      $manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => '<input type="checkbox" name="pw_manager_log_id[]" value="'.$pw_manager_row['id'].'">'
          );
      $manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $priority_str
          );
      $manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $pw_manager_row['loginurl']
          );
      $manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => mb_substr($pw_manager_row['title'],0,12,'utf-8')
          );
      $manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => mb_substr($pw_manager_row['username'],0,8,'utf-8')
          );
      $manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => mb_substr($pw_manager_row['password'],0,8,'utf-8')
          );
        if($pw_manager_row['privilege'] =='7'){
         $info = TEXT_PERMISSION_STAFF;
        }else if($pw_manager_row['privilege'] =='10'){
         $info = TEXT_PERMISSION_CHIEF;
        }else{
         if($pw_manager_row['self']!=''){
         $self_info = tep_get_user_info($pw_manager_row['self']);
         $info = mb_substr($self_info['name'],0,5,'utf-8');
         }else{
         $info = mb_substr($pw_manager_row['operator'],0,5,'utf-8');
         }
        }
      $manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $info
          );
      $manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $pw_manager_row['nextdate']
          );
      if($site_id == ''){
       $site_id = 0;
      }
      $pw_manager_date_info = (tep_not_null($pw_manager_row['updated_at']) && ($pw_manager_row['updated_at'] != '0000-00-00 00:00:00'))?$pw_manager_row['updated_at']:$pw_manager_row['created_at'];
      $manager_info[] = array(
          'params' => 'class="dataTableContent" align="right"',
          'text'   => '<a href="javascript:void(0)" onclick="show_pw_manager_log(this,'.$pw_id.','.$_GET['page'].','.$site_id.','.$pw_manager_row['id'].')">' . tep_get_signal_pic_info($pw_manager_date_info) . '</a>'
          );
$manager_table_row[] = array('params' => $manager_params ,'text' => $manager_info);
    }
       $manager_form = tep_draw_form('del_pw_manager_log', FILENAME_PW_MANAGER, 'log='.$_GET['log'].'&page=' .  $_GET['page'] . '&pw_id='.$_GET['pw_id'].'&site_id='.$_GET['site_id'].'&action=deleteconfirm');
       $notice_box->get_form($manager_form);
       $notice_box->get_contents($manager_table_row);
       $notice_box->get_eof(tep_eof_hidden());
       echo $notice_box->show_notice();


    ?>
    </table>
    </td></tr></table>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
       <td>
        <?php
   $sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
   while($userslist= tep_db_fetch_array($sites_id)){
    $site_permission = $userslist['site_permission'];
   }
   if(isset($site_permission))
   $site_arr=$site_permission;//权限判断
   else $site_arr="";
   $site_array = explode(',',$site_arr);
     if($_GET['site_id'] != null){
      $site_id = $_GET['site_id'];
     }else{
      $site_id = 0;
     }
      if($pw_manager_numrows > 0){
      if($ocertify->npermission >= 15){
         if(in_array($site_id,$site_array)){
             echo '<select name="pw_manager_action" onchange="pw_manager_change_action(this.value, \'pw_manager_log_id[]\');">';
         }else{
             echo '<select name="pw_manager_action" disabled="disabled">';
         }
             echo '<option value="0">'.TEXT_PW_MANAGER_SELECT_ACTION.'</option>';
             echo '<option value="1">'.TEXT_PW_MANAGER_DELETE_ACTION.'</option>';
             echo '</select>';
       }
      }
       ?>
       </td>
       <td colspan="9" align="right">
         <?php
              echo "<button type='button' style='font-size:12px' onclick=\"location.href='".  tep_href_link(FILENAME_PW_MANAGER,'pw_id='.$pwid.'&site_id='.$site_id) ."'\">".TEXT_BUTTON_BACK."</button>"; 
         ?>
       </td>
    </tr>
    <tr>
      <td colspan="9">
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText" valign="top"><?php echo
            $pw_manager_split->display_count($pw_manager_query_numrows,
                MAX_DISPLAY_PW_MANAGER_LOG_RESULTS, $_GET['page'],
                TEXT_DISPLAY_NUMBER_OF_PW_MANAGER_LOG); ?></td>
            <td class="smallText" align="right"><?php echo
            $pw_manager_split->display_links($pw_manager_query_numrows,
                MAX_DISPLAY_PW_MANAGER_LOG_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],
                tep_get_all_get_params(array('page', 'site_id', 'action','pwid'))); ?></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
      </td>
    </tr>
  </table>
  </div></div>
      </td>
    </tr>

    </table></td>
<!-- body_text_eof -->
  </tr>
</table>
<!-- body_eof -->

<!-- footer -->
<?php
    require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php');
}else{
  $sort_where = '';
  if($ocertify->npermission < 15){
    $sort_where = " and ((privilege <= '".$ocertify->npermission."' and self='') or
     self='".$ocertify->auth_user."' )";
  }else{
    $sort_where = '';
  }

  if(isset($_GET['site_id'])&&$_GET['site_id']){
    $site_id = tep_db_prepare_input($_GET['site_id']);
  }else{
    $site_id = '';
  }
  if(isset($_GET['pw_id'])&&$_GET['pw_id']){
    $pwid = tep_db_prepare_input($_GET['pw_id']);
  }
  
  //403
if(isset($pwid)&&$pwid&&!tep_can_edit_pw_manager($pwid,$ocertify->auth_user,$ocertify->npermission)){
  header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
  exit;
}
if(isset($_GET['action']) &&
    ($_GET['action']=='delete'
     ||$_GET['action']=='deleteconfirm')&&
    $ocertify->npermission<15){
  header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
  exit;
}

  if (isset($_GET['action']) && $_GET['action']) { switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'redirect' 跳转到指定页    
   case 'insert' 新建idpw  
   case 'update' 更新idpw   
   case 'deleteconfirm' 删除idpw    
------------------------------------------------------*/
      case 'redirect':
          tep_redirect(urldecode($_GET['url']));
        break;
      case 'insert':
      case 'update':
          $user_info = tep_get_user_info($ocertify->auth_user);
          if(tep_db_prepare_input($_POST['privilege'])==15){
            if(tep_db_prepare_input($_POST['user_self'])!=''){
            $user_self = tep_db_prepare_input($_POST['user_self']);
            $tmp_user_info = tep_get_user_info($user_self);
            $pw_operator = $tmp_user_info['name'];
            }else{
            $pw_operator = $user_info['name'];
            $user_self = $ocertify->auth_user;
            }
          }else{
            $pw_operator = $user_info['name'];
            $user_self = '';
          }
          $privilege_str .= 'admin';
          $sql_data_array = array(
            'title' => tep_db_prepare_input($_POST['title']),
            'url' => tep_db_prepare_input($_POST['url']),
            'priority' => tep_db_prepare_input($_POST['priority']),
            'loginurl' => tep_db_prepare_input($_POST['loginurl']),
            'username' => tep_db_prepare_input($_POST['username']),
            'password' => tep_db_prepare_input($_POST['password']),
            'comment' => tep_db_prepare_input($_POST['comment']),
            'memo' => tep_db_prepare_input($_POST['memo']),
            'nextdate' => tep_db_prepare_input($_POST['nextdate']),
            'update_user' => $user_info['name'],
            'updated_at' => date('Y-m-d H:i:s',time()),
            'site_id' => tep_db_prepare_input($_POST['site_id']),
            'onoff' => '1',
            );
        if($_GET['action']=='update'){
          if(tep_db_prepare_input($_POST['privilege'])==15&&
              tep_db_prepare_input($_POST['user_self'])!=''){
            
          $update_sql_data = array(
            'privilege' => tep_db_prepare_input($_POST['privilege']),
            'operator' => $pw_operator,
            'self' => $user_self,
            );
          $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
          }else if(tep_db_prepare_input($_POST['privilege'])==7||
          tep_db_prepare_input($_POST['privilege'])==10){
          $update_sql_data = array(
            'privilege' => tep_db_prepare_input($_POST['privilege']),
            'self' => $user_self,
            );
          $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
          }
          tep_db_perform(TABLE_IDPW, $sql_data_array, 'update', 'id = \'' . $pwid . '\'');
          if(tep_db_prepare_input($_POST['url'])!=tep_db_prepare_input($_POST['old_url'])||tep_db_prepare_input($_POST['loginurl'])!=tep_db_prepare_input($_POST['old_loginurl'])||tep_db_prepare_input($_POST['username'])!=tep_db_prepare_input($_POST['old_username'])||tep_db_prepare_input($_POST['password'])!=tep_db_prepare_input($_POST['old_password'])||tep_db_prepare_input($_POST['comment'])!=tep_db_prepare_input($_POST['old_comment'])

            ){
          $res = tep_db_query("select * from ".TABLE_IDPW. " where id =
              '".$pwid."'");
          $sql_data_array_log = array();
          if($row = tep_db_fetch_array($res)){
            foreach($row as $key => $value){
              if($key == 'id'){
                $sql_data_array_log['idpw_id'] = $value;
              }else{
                $sql_data_array_log[$key] = $value;
              }
            }
          }
          tep_db_perform(TABLE_IDPW_LOG,$sql_data_array_log);
          }
          tep_redirect(tep_href_link(FILENAME_PW_MANAGER,
                'pw_id='.$pwid.'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page='
                . $_GET['page'].'&site_id='.$site_id));
        }
        if($_GET['action']=='insert'){
		
          $insert_sql_data = array(
            'self' => $user_self,
            'privilege' => tep_db_prepare_input($_POST['privilege']),
            'user_added' => $_POST['user_added'],
            'created_at' => date('Y-m-d H:i:s',time()),
	    'operator' => $pw_operator,
            );
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_IDPW, $sql_data_array);
          $last_insert_id = mysql_insert_id();
          $insert_sql_data_log = array(
            'idpw_id' => tep_db_insert_id(), 
              );
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data_log);
          tep_db_perform(TABLE_IDPW_LOG, $sql_data_array);
	            tep_redirect(tep_href_link(FILENAME_PW_MANAGER,'sort='.$_GET['sort'].'&type='.$_GET['type'].'&pw_id='.$last_insert_id.'&site_id='.$_GET['site_id']));
        }
        break;
      case 'deleteconfirm':
        if(!empty($_POST['pw_manager_id'])){
          foreach ($_POST['pw_manager_id'] as $ge_key => $ge_value){
            $sql_del = 'delete from '.TABLE_IDPW.' where id = "'.$ge_value.'"';
            tep_db_query($sql_del);
            $sql_del_log = 'delete from '.TABLE_IDPW_LOG.' where idpw_id = "'.$ge_value.'"';
            tep_db_query($sql_del_log);
          }
        }
        $sql_del = 'delete from '.TABLE_IDPW.' where id = "'.$pwid.'"';
        tep_db_query($sql_del);
        $sql_del_log = 'delete from '.TABLE_IDPW_LOG.' where idpw_id = "'.$pwid.'"';
        tep_db_query($sql_del_log);
        tep_redirect(tep_href_link(FILENAME_PW_MANAGER, 'page=' .  $_GET['page'].'&site_id='.$_GET['site_id']));
        break;

    }
  }
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
                             where site_id ='".$_GET['site_id']."' and onoff = '1' 
                             " .$sort_where . "
                             order by ".$order_str;
    }
    if($pw_id){
      $has_pw_sql = str_replace('where',"where id='".$pw_id."' and
          ",$pw_manager_query_raw);
      $has_pw_query = tep_db_query($has_pw_sql);
    }
    if($pw_id){
      if(tep_db_fetch_array($has_pw_query)){
      $pw_selected_sql = $pw_manager_query_raw;
      $pw_selected_query = tep_db_query($pw_selected_sql);
      $pw_selected_row_number = 0;
      $pw_selected_rows = 0;
      while($pw_info_row = tep_db_fetch_array($pw_selected_query)){
        $pw_selected_rows++;
        if($pw_id == $pw_info_row['id']){
          $pw_selected_row_number = $pw_selected_rows;
        }
      }
        $pw_selected_page = ceil($pw_selected_row_number/MAX_DISPLAY_PW_MANAGER_RESULTS);
      }else{
       tep_redirect(tep_href_link(FILENAME_PW_MANAGER,'page=' .
          $pw_selected_page .'&'.
          tep_get_all_get_params(array('page','pw_id','action'))));
      }
    }
    if($pw_selected_page != $_GET['page']){
    if(isset($pw_selected_page)&&$pw_selected_page){
       tep_redirect(tep_href_link(FILENAME_PW_MANAGER,'page=' .
          $pw_selected_page .'&'. tep_get_all_get_params(array('page'))));
    }
    }


  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  # 永远是改动过的
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TEXT_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery.form.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" src="includes/3.4.1/build/yui/yui.js"></script>
<script language="javascript" >
$(document).ready(function() {
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_pw_manager').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           <?php //回车?>
        if ($('#show_pw_manager').css('display') != 'none') {
            if (o_submit_single){
               $("#show_pw_manager").find('input:submit').first().trigger("click");
             }
            }
        }

     if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_pw_manager').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_pw_manager').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});
<?php //选择删除全部数据?>
function all_select_pw_manager(pw_manager_str){
        var check_flag = document.del_pw_manager.all_check.checked;
           if (document.del_pw_manager.elements[pw_manager_str]) {
              if (document.del_pw_manager.elements[pw_manager_str].length == null){
                   if (check_flag == true) {
                         document.del_pw_manager.elements[pw_manager_str].checked = true;
                    } else {
                       document.del_pw_manager.elements[pw_manager_str].checked = false;
                     }
               } else {
                 for (i = 0; i < document.del_pw_manager.elements[pw_manager_str].length; i++){
                      if (check_flag == true) {
                           document.del_pw_manager.elements[pw_manager_str][i].checked = true;
                      } else {
                           document.del_pw_manager.elements[pw_manager_str][i].checked = false;
                      }
                  }
              }
        }
}
<?php //AJAX弹出页面 ?>
function show_pw_manager(ele,pw_id,page,site_id){
 sort = document.getElementById('pw_manager_sort').value;
 type = document.getElementById('pw_manager_type').value;
 search_type = document.getElementById('pw_manager_search_type').value;
 keywords = document.getElementById('pw_manager_keywords').value;
 $.ajax({
 url: 'ajax.php?&action=edit_pw_manager',
 data: {pw_id:pw_id,page:page,site_id:site_id,sort:sort,type:type,keywords:keywords,search_type:search_type} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("#show_pw_manager").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(pw_id != -1){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_pw_manager').height()){
offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_pw_manager').height()) > $('.box_warp').height())&&($('#show_pw_manager').height()<ele.offsetTop+parseInt(head_top)-$("#orders_list_table").position().top-1)) {
offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#show_pw_manager').height()+head_top;
} else {
offset = ele.offsetTop+$("#orders_list_table").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#orders_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_pw_manager').height()) > $('.box_warp').height())&&($('#show_pw_manager').height()<ele.offsetTop+parseInt(head_top)-$("#orders_list_table").position().top-1)) {
    offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#show_pw_manager').height()+head_top;
  } else {
    offset = ele.offsetTop+$('#orders_list_table').position().top+ele.offsetHeight+head_top;
  }
}
$('#show_pw_manager').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_pw_manager').height()) > $('.box_warp').height())&&($('#show_pw_manager').height()<ele.offsetTop+parseInt(head_top)-$("#orders_list_table").position().top-1)) {
      offset = ele.offsetTop+$("#orders_list_table").position().top-1-$('#show_pw_manager').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#orders_list_table").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#orders_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#orders_list_table").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_pw_manager').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_pw_manager').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(pw_id == -1){
  $('#show_pw_manager').css('top',$('#orders_list_table').offset().top);
}
$('#show_pw_manager').css('z-index','1');
$('#show_pw_manager').css('left',leftset);
$('#show_pw_manager').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
<?php //关闭弹出页面?>
function hidden_info_box(){
   $('#show_pw_manager').css('display','none');
}
<?php //弹出日历?>
function open_new_calendar(){
  var is_open = $('#toggle_open').val(); 
  if (is_open == 0) {
    browser_str = navigator.userAgent.toLowerCase(); 
    if (browser_str.indexOf("msie 9.0") > 0) {
      $('#new_yui3').css('margin-left', '-170px'); 
    }
    $('#toggle_open').val('1'); 
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
            contentBox: "#mycalendar",
            width:'170px',

        }).render();
      var dtdate = Y.DataType.Date;
      calendar.on("selectionChange", function (ev) {
        var newDate = ev.newSelection[0];
        $("#input_nextdate").val(dtdate.format(newDate)); 
        $('#toggle_open').val('0');
        $('#toggle_open').next().html('<div id="mycalendar"></div>');
      });
    });
  }
}
<?php //管理者选择权限?>
function self_radio(){
     if($('#self').attr("checked")){ 
      $("#user_select").css('display', 'block');
      }
 
}
<?php //管理者选择权限?>
function privilege_s_radio(){
     if($('#privilege_s').attr("checked")){ 
      $("#user_select").css('display', 'none');
      }
}
<?php //管理者选择权限?>
function privilege_c_radio(){
     if($('#privilege_c').attr("checked")){ 
     $("#user_select").css('display', 'none');
     }
}
<?php //复制代码?>
function copyCode(idpw,name){
  var testCode;
  $.post('<?php echo
      tep_href_link(FILENAME_PWD_AJAX);?>',{'action':'load','idpw':idpw,'from':name}, function(data) {
      testCode = data;
    if(copy2Clipboard(testCode)!=false){
        alert('<?php echo TEXT_COPY_OK;?>');
    }
  });
}
copy2Clipboard=function(txt){
    if(window.clipboardData){
        window.clipboardData.clearData();
        window.clipboardData.setData("Text",txt);
    }
    else if(navigator.userAgent.indexOf("Opera")!=-1){
        window.location=txt;
    }
    else if(window.netscape){
        try{
            netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        }
        catch(e){
            alert('<?php echo TEXT_FIREFOX_ERROR;?>');
            return false;
        }
        var clip=Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
        if(!clip)return;
        var trans=Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
        if(!trans)return;
        trans.addDataFlavor('text/unicode');
        var str=new Object();
        var len=new Object();
        var str=Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
        var copytext=txt;str.data=copytext;
        trans.setTransferData("text/unicode",str,copytext.length*2);
        var clipid=Components.interfaces.nsIClipboard;
        if(!clip)return false;
        clip.setData(trans,null,clipid.kGlobalClipboard);
    }
}
<?php //搜索表单提交?>
function search_type_changed(elem){
	if ($('#keywords').val() && elem.selectedIndex != 0) 
      document.forms.pw_manager1.submit();
}
<?php //检查url?>
function checkurl(url){
  var str = url;
  var objExp = new RegExp(/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w-.\/?%&=]*)?/);
  if(objExp.test(str)){
    return true;
  }else{
    return false;
  }
}
<?php //验证表单?>
function valdata(c_permission){
  id_pw_error = false; 
  if (document.getElementById('url').value!=''&& !checkurl(document.getElementById('url').value)) {
    id_pw_error = true; 
  }
  if (document.getElementById('loginurl').value!=''&& !checkurl(document.getElementById('loginurl').value)) {
    id_pw_error = true; 
  }
  if (id_pw_error == false) {
    if (c_permission == 31) {
      document.forms.pw_manager.submit(); 
    } else {
      $.ajax({
        url: 'ajax_orders.php?action=getallpwd',   
        type: 'POST',
        dataType: 'text',
        async: false,
        success: function(msg) {
          pwd_list_array = msg.split(','); 
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            document.forms.pw_manager.submit(); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
          }
        }
      });
    }
  } else {
    alert('<?php echo TEXT_URL_EXAMPLE;?>'); 
  }
}
<?php //创建密码?>
function mk_pwd(){
  var len = $('input:checkbox[name=pattern[]]:checked').length;
  var check = '';
  $('input:checkbox[name=pattern[]]:checked').each(function(index) {
    if (index < len-1){
      check += $(this).val()+",";
    }else{
      check += $(this).val();
    }
  });
  var pwd_len = $('#pwd_len').val();
  $.post('<?php echo tep_href_link(FILENAME_PWD_AJAX);?>',{'action':'make_pw','pattern':check,'pwd_len':pwd_len}, function(data) {
      $('#password').val(data);
  });
}
<?php //是否确认删除数据?>
function delete_select_pw_manager(pw_manager_str, c_permission){
         sel_num = 0;
         if (pw_manager_str == null){
                  document.getElementsByName('pw_manager_action')[0].value = 0;
                 alert('<?php echo TEXT_PW_MANAGER_MUST_SELECT;?>'); 
         }
         if (document.del_pw_manager.elements[pw_manager_str].length == null) {
                if (document.del_pw_manager.elements[pw_manager_str].checked == true){
                     sel_num = 1;
                 }
          } else {
             for (i = 0; i < document.del_pw_manager.elements[pw_manager_str].length; i++) {
                 if(document.del_pw_manager.elements[pw_manager_str][i].checked == true) {
                     sel_num = 1;
                     break;
                  }
            }
         }
         if (sel_num == 1) {
          if (confirm('<?php echo TEXT_DEL_PW_MANAGER;?>')) {
            if (c_permission == 31) {
              document.forms.del_pw_manager.submit(); 
            } else {
              $.ajax({
                url: 'ajax_orders.php?action=getallpwd',   
                type: 'POST',
                dataType: 'text',
                async: false,
                success: function(msg) {
                  pwd_list_array = msg.split(','); 
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    document.forms.del_pw_manager.submit(); 
                  } else {
                    document.getElementsByName('pw_manager_action')[0].value = 0;
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                  }
                }
              });
            }
          }else{
             document.getElementsByName('pw_manager_action')[0].value = 0;
          }
         } else {
                 document.getElementsByName('pw_manager_action')[0].value = 0;
                 alert('<?php echo TEXT_PW_MANAGER_MUST_SELECT;?>'); 
        }
    }
<?php //选择动作?>
function pw_manager_change_action(r_value, r_str) {
  if (r_value == '1') {
    delete_select_pw_manager(r_str, '<?php echo $ocertify->npermission;?>');
  }
}
<?php //执行动作?>
function toggle_idpw_action(idpwd_url_str, c_permission)
{
  if (c_permission == 31) {
    window.location.href = idpwd_url_str; 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      async: false,
      success: function(msg) {
        pwd_list_array = msg.split(','); 
        var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
        if (in_array(input_pwd_str, pwd_list_array)) {
          window.location.href = idpwd_url_str; 
        } else {
          alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
        }
      }
    });
  }
}
</script>
<style type="text/css">
.yui3-skin-sam {
  width:150px;
}
@media screen and (-webkit-min-device-pixel-ratio:0) {
.yui3-skin-sam {
  width:150px;
}
}
.yui3-skin-sam input {
  float:left;
}
a.dpicker {
	width: 16px;
	height: 18px;
	border: none;
	color: #fff;
	padding: 0;
	margin:1px 0 0 1px;
	overflow: hidden;
        display:block;	
        cursor: pointer;
	background: url(./includes/calendar.png) no-repeat; 
	float:left;
} 
.popup-calendar {
top:20px;
left:-95px;
left:-163px;
}
.number{
font-size:24px;
font-weight:bold;
width:20px;
text-align:center;
}
form{
margin:0;
padding:0;
}
.alarm_input{
width:75px;
}
.log{
  border:#999 solid 1px;
  background:#eee;
  clear: both;
}
.log .content{
  padding:3px 0;
  font-size:12px;
}
.log .alarm{
  display:none;
  background:url(images/icons/alarm.gif) no-repeat left center;
}
.log .level{
  font-size:10px;
  font-weight:bold;
  display:none;
  width:99px;
}
.log .level input{
margin:0;
padding:0;
}
.log .info{
  font-size:10px;
  background:#fff;
  text-align:right;
}
.info02{
width:50px;
padding:0 5px;
}
.log .action{
text-align:center;
  font-size:10px;
}
.edit_action{
  display:none;
  font-size:10px;
line-height:24px;
padding-right:5px;
}
.action a{
padding:0 3px;
}
textarea,input{
  font-size:14px;
}
textarea{
  width:100%;
  padding:0;
  margin:0;
}
.alarm_on{
  border:2px solid #ff8e90;
  background:#ffe6e6;
}
.clr{
clear:both;
width:100%;
height:5px;
overflow:hidden;
}
.popup-calendar-wrapper{
float:left;
}

#new_yui3 {
	margin-left:-168px;
	*margin-left:-28px;
	margin-left:-28px\9;
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
#input_nextdate{ width:120px; float:left;}
a.date-picker {
    display: block;
    float: none;
}
.number{
font-size:24px;
font-weight:bold;
width:20px;
text-align:center;
}
form{
margin:0;
padding:0;
}
.alarm_input{
width:80px;
}
.log{
  border:#999 solid 1px;
  background:#eee;
  clear: both;
}
.log .content{
  padding:3px;
  font-size:12px;
}
.log .alarm{
  display:none;
  font-size:10px;
  background:url(images/icons/alarm.gif) no-repeat left center;
}
.log .level{
  font-size:10px;
  font-weight:bold;
  display:none;
  width:100px;
  *width:120px;
}
.log .level input{
margin:0;
padding:0;
}
.log .info{
  font-size:10px;
  background:#fff;
  text-align:right;
}
.info02{
width:50px;
}
.log .action{
text-align:center;
  font-size:10px;
}
.edit_action{
  display:none;
  font-size:10px;
line-height:24px;
padding-right:5px;
}
.action a{
padding:0 3px;
}
textarea,input{
  font-size:14px;
}
textarea{
  width:100%;
}
.alarm_on{
  border:2px solid #ff8e90;
  background:#ffe6e6;
}
.clr{
clear:both;
width:100%;
height:5px;
overflow:hidden;
}
.popup-calendar {
	top:850px;
	*top:755px!important;
	top:735px\9;
	right:163px;
	position: absolute;
}
.popup-calendar-wrapper{
float:left;
}
.popup-calendar-wrapper table{ width:100%;}
.weekend ,.weekday,.inactive  { text-align:center; background-color:#eee;}
.link-next{ text-align:right;}
.link-close{ color:#000; text-align:right; position:absolute; top:5px; font-weight:bold; left:134px;}
</style>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header -->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof -->
<!-- body -->
<input type="hidden" id="show_info_id" value="show_pw_manager" name="show_info_id">
<div id="show_pw_manager" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display: none; top: 192px; z-index: 1; left: 22px;"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
<?php
    echo '<td width="' . BOX_WIDTH . '" valign="top">';
    echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
    echo '<!-- left_navigation -->';
    require(DIR_WS_INCLUDES . 'column_left.php');
    echo '<!-- left_navigation_eof -->';
    echo '</table>';
    echo '</td>';
?>
<!-- body_text -->
    <td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td width="100%" colspan='2'>
  
  <table border="0" width="100%" cellspacing="0" cellpadding="3">
    <tr>
      <td colspan="2" class="pageHeading" height="40"><?php echo HEADING_TITLE; ?></td>
    </tr>
      <tr>
        <td colspan="2">
        <?php echo PW_MANAGER_NOTICE_TEXT;?> 
        </td>
      </tr>
      <tr>
      <td align="left" class="smallText">
        <table width=""  border="0" cellspacing="1" cellpadding="0"
        style="margin:10px 0;">
          <tr>
            <td class="smallText" valign='top'>
              <?php echo tep_draw_form('pw_manager1', FILENAME_PW_MANAGER, '',
                  'get','id="pw_manager1" onsubmit="return false"').IMAGE_SEARCH; ?> : 
              <input name="keywords" type="text" id="keywords" size="40" value="<?php if(isset($_GET['keywords'])) echo stripslashes($_GET['keywords']); ?>">
              <input name="site_id" type="hidden" id="site_id" size="40" value="<?php
              echo isset($site_id)?$site_id:'0'; ?>">
              <select name="search_type" onChange='search_type_changed(this)'>
                <option value="none"><?php echo PW_MANAGER_SELECT_NONE;?></option>
                <option value="priority"><?php echo PW_MANAGER_SELECT_ONE;?></option>
                <option value="loginurl"><?php echo PW_MANAGER_SELECT_TWO;?></option>
                <option value="title"><?php echo PW_MANAGER_SELECT_THREE;?></option>
                <option value="url"><?php echo PW_MANAGER_SELECT_FOUR;?></option>
                <option value="username"><?php echo PW_MANAGER_SELECT_FIVE;?></option>
                <option value="password"><?php echo PW_MANAGER_SELECT_SIX;?></option>
                <option value="operator"><?php echo PW_MANAGER_SELECT_SEVEN;?></option>
                <option value="comment"><?php echo PW_MANAGER_SELECT_EIGHT;?></option>
                <option value="memo"><?php echo PW_MANAGER_SELECT_NINE;?></option>
              </select>
              </form>
            </td>
            <td valign="top"></td>
          </tr>
        </table>
      </td>
      <td align="right">
      </td>
    </tr>
  </table>

      </td>
    </tr>
    <tr>
      <td valign="top">
       <?php tep_pw_site_filter(FILENAME_PW_MANAGER);?>
       <table border="0" width="100%" cellspacing="0" cellpadding="0" id="orders_list_table">
         <tr>
          <td valign="top">
       <?php 
       echo'<input type="hidden" id="pw_manager_sort" value="'.$_GET['sort'].'"><input type="hidden" id="pw_manager_type" value="'.$_GET['type'].'"><input type="hidden" id="pw_manager_keywords" value="'.$_GET['keywords'].'"><input type="hidden" id="pw_manager_search_type" value="'.$_GET['search_type'].'">';
       $pw_manager_table_params = array('width'=>'100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
       $notice_box = new notice_box('','',$pw_manager_table_params);
       $pw_manager_table_row = array(); 
       $pw_manager_title_row = array();
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<input type="checkbox" name="all_check" onclick="all_select_pw_manager(\'pw_manager_id[]\');">');
       if ($HTTP_GET_VARS['sort'] == 'priority') {
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=priority&type='.$type_str).'">'.TEXT_PRIORITY.'</a>');
       }else{
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=priority&type=asc').'">'.TEXT_PRIORITY.'</a>');
       }
       if ($HTTP_GET_VARS['sort'] == 'loginurl') {
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('pw_id','x', 'y', 'type', 'sort')).'sort=loginurl&type='.$type_str).'">'.TEXT_LOGINURL.'</a>');
       }else{
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('pw_id','x', 'y', 'type', 'sort')).'sort=loginurl&type=asc').'">'.TEXT_LOGINURL.'</a>');
       }
       if ($HTTP_GET_VARS['sort'] == 'title') {
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=title&type='.$type_str).'">'.TEXT_INFO_TITLE.'</a>');
       }else{
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=title&type=asc').'">'.TEXT_INFO_TITLE.'</a>');
       }
      if ($HTTP_GET_VARS['sort'] == 'username') {
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=username&type='.$type_str).'">'.TEXT_USERNAME.'</a>');
      }else{
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=username&type=asc').'">'.TEXT_USERNAME.'</a>');
      }
      if ($HTTP_GET_VARS['sort'] == 'password') {
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' =>'<a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=password&type='.$type_str).'">'.TEXT_PASSWORD.'</a>');
      } else {
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' =>' <a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=password&type=asc').'">'.TEXT_PASSWORD.'</a>');
      }
      if ($HTTP_GET_VARS['sort'] == 'operator') {
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.  tep_href_link('id_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=operator&type='.$type_str).'">'.TEXT_PRIVILEGE.'</a>');
      } else {
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=operator&type=asc').'">'.TEXT_PRIVILEGE.'</a>');
      }
      if ($HTTP_GET_VARS['sort'] == 'nextdate') {
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => ' <a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=nextdate&type='.$type_str).'">'.TEXT_NEXTDATE.'</a>');
      } else {
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent_pw"','text' => '<a href="'.tep_href_link('id_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=nextdate&type=desc').'">'.TEXT_NEXTDATE.'</a>');
      }
       $pw_manager_title_row[] = array('params' => 'class="dataTableHeadingContent"','align' => 'right','text' => TABLE_HEADING_ACTION);
       $pw_manager_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $pw_manager_title_row);
      ?>
    <?php 
    $pw_manager_split = new splitPageResults($_GET['page'],
        MAX_DISPLAY_PW_MANAGER_RESULTS, $pw_manager_query_raw, $pw_manager_query_numrows);
       
    $pw_manager_query = tep_db_query($pw_manager_query_raw);
    $i=0;
    $pw_manager_numrows = tep_db_num_rows($pw_manager_query);
    while($pw_manager_row = tep_db_fetch_array($pw_manager_query)){
      $i++;
      if (( (!@$_GET['pw_id']) || (@$_GET['pw_id'] == $pw_manager_row['id'])) && (!@$pwInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $pwInfo = new objectInfo($pw_manager_row);
      }
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
    $pw_manager_info = array();
    if (isset($pwInfo) && (is_object($pwInfo)) && ($pw_manager_row['id'] == $pwInfo->id) ) {
      $pw_manager_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ';
    } else {
      $pw_manager_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }
      $priority_str = "<font color='";
      switch($pw_manager_row['priority']){
        case '1':
            $priority_str .="black";
          break;
        case '2':
            $priority_str .="orange";
          break;
        case '3':
            $priority_str .="red";
          break;

      }
      $priority_str .= "' >".$pw_manager_row['priority']."</font>";
      $pw_manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => '<input type="checkbox" name="pw_manager_id[]" value="'.$pw_manager_row['id'].'">'
      );
 
      $pw_manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $priority_str 
      );
      $pw_manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => '<a target="_blank" href="'.make_blank_url($pw_manager_row['loginurl'],FILENAME_REDIREC_URL).'">'.tep_image_button('button_url.gif',IMAGE_CREATE).  '<a>'
      );
      $pw_manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => '<a target="_blank" href="'.make_blank_url($pw_manager_row['url'],FILENAME_REDIREC_URL).'">'.mb_substr($pw_manager_row['title'],0,12,'utf-8').'</a>'
      );
      $pw_manager_info[] = array(
          'params' => 'class="dataTableContent_line" id="user_'.$i.'"onclick="copyCode(\''.$pw_manager_row['id'].'\',\'username\')"',
          'text'   => mb_substr($pw_manager_row['username'],0,8,'utf-8')
      );
      $pw_manager_info[] = array(
          'params' => 'class="dataTableContent_line" id="pwd_'.$i.'"onclick="copyCode(\''.$pw_manager_row['id'].'\',\'password\')"',
          'text'   => mb_substr($pw_manager_row['password'],0,8,'utf-8')
      );
        if($pw_manager_row['privilege'] =='7'){
         $info = TEXT_PERMISSION_STAFF;
        }else if($pw_manager_row['privilege'] =='10'){
         $info = TEXT_PERMISSION_CHIEF;
        }else{
         if($pw_manager_row['self']!=''){
         $self_info = tep_get_user_info($pw_manager_row['self']);
          $info = mb_substr($self_info['name'],0,5,'utf-8');
         }else{
          $info =  mb_substr($pw_manager_row['operator'],0,5,'utf-8');
         }
        }
      $pw_manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $info
      );
      $pw_manager_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $pw_manager_row['nextdate'] 
      );

      if($_GET['site_id'] == null){
          $_GET['site_id'] = 0;
       }
      $pw_manager_date_info = (tep_not_null($pw_manager_row['updated_at']) && ($pw_manager_row['updated_at'] != '0000-00-00 00:00:00'))?$pw_manager_row['updated_at']:$pw_manager_row['created_at'];
      $pw_manager_info[] = array(
          'params' => 'class="dataTableContent" align="right"',
          'text'   => '<a href="javascript:void(0)" onclick="show_pw_manager(this,'.$pw_manager_row['id'].','.$_GET['page'].','.$_GET['site_id'].')">' .tep_get_signal_pic_info($pw_manager_date_info). '</a>'
      );
    $pw_manager_table_row[] = array('params' => $pw_manager_params,'text' => $pw_manager_info);
    }
   $pw_manager_form = tep_draw_form('del_pw_manager', FILENAME_PW_MANAGER, 'page=' .  $_GET['page'] . '&pw_id=' . $pwInfo->id . '&site_id='.$_GET['site_id'].'&action=deleteconfirm');
   $notice_box->get_form($pw_manager_form);
   $notice_box->get_contents($pw_manager_table_row);
   $notice_box->get_eof(tep_eof_hidden());
   echo $notice_box->show_notice();
   ?>
   </td>
   </tr>
  </table>
  <?php
   $sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
   while($userslist= tep_db_fetch_array($sites_id)){
    $site_permission = $userslist['site_permission'];
   }
   if(isset($site_permission))
   $site_arr=$site_permission;//权限判断
   else $site_arr="";
   $site_array = explode(',',$site_arr);
  ?>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td>
      <?php
      if($_GET['site_id'] == ''){
      $site_id = 0;
      }else{
      $site_id = $_GET['site_id'];
      }
      if($pw_manager_numrows > 0){
      if($ocertify->npermission >= 15){
         if(in_array($site_id,$site_array)){
             echo '<select name="pw_manager_action" onchange="pw_manager_change_action(this.value, \'pw_manager_id[]\');">';
         }else{
             echo '<select name="news_action" disabled="disabled">';
         }
             echo '<option value="0">'.TEXT_PW_MANAGER_SELECT_ACTION.'</option>';
             echo '<option value="1">'.TEXT_PW_MANAGER_DELETE_ACTION.'</option>';
             echo '</select>';
       }
      }
       ?>
      </td>
      <td></td>
    </tr>
    <tr>
      <td class="smallText" valign="top"><?php echo $pw_manager_split->display_count($pw_manager_query_numrows, MAX_DISPLAY_PW_MANAGER_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PW_MANAGERS); ?></td>
      <td class="smallText" align="right"><div class="td_box"><?php echo $pw_manager_split->display_links($pw_manager_query_numrows, MAX_DISPLAY_PW_MANAGER_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page',  'action','pw_id'))); ?></div></td>
    </tr>
    <tr>
      <td colspan="2" align="right" class="smallText">
      <div class="td_button"> 
      <?php 
      if($_GET['site_id'] == ''){
        $_GET['site_id'] = 0;
      }
      if(in_array($site_id,$site_array)){
       echo '<button type=\'button\' onclick="show_pw_manager(this,-1,'.$_GET['page'].','.$_GET['site_id'].')" >'; echo IMAGE_NEW_PROJECT; echo "</button>";
      }else{
       echo '<button type=\'button\' disabled="disabled">'.IMAGE_NEW_PROJECT.'</button>';
      }
      ?> 
      </div> 
      </td>
    </tr>
  </table>    
  </td>
    </tr>
  </table>
    </div> </div>
    </td>
<!-- body_text_eof -->
  </tr>
</table>
<!-- body_eof -->

<!-- footer -->
<?php
    require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); 
}
?>
