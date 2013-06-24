<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  // 获取访问日志信息
  if (isset($_POST['sp'])) { $sp = $_POST['sp']; }
  if (isset($_POST['execute_delete'])) { $execute_delete = $_POST['execute_delete']; }
  if (isset($execute_delete) && $execute_delete) {    
  // 删除访问日志信息
  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
     case 'deleteconfirm':
       $logs_list_array = $_POST['logs_list_id'];
       $logs_list_str = implode(',',$logs_list_array);
       tep_db_query("delete from ".TABLE_ONCE_PWD_LOG." where id in (".$logs_list_str.")");
       tep_redirect(tep_href_link(FILENAME_PWD_LOG,'page='.$_GET['page']));
     break;
    }
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
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" src="includes/javascript/show_site.js.php"></script>
<script language="JavaScript1.1">
function formConfirm(type, c_permission) {
  if (type == "delete") {
      rtn = confirm("<?php echo JAVA_SCRIPT_INFO_DELETE;?>");
  }
  if (rtn) {
    if (c_permission != 31) {
      $.ajax({
        url: "ajax_orders.php?action=getallpwd",   
        type: "POST",
        dataType: "text",
        data: "current_page_name=<?php echo $_SERVER['PHP_SELF'];?>", 
        async: false,
        success: function(msg) {
          var tmp_msg_arr = msg.split("|||"); 
          var pwd_list_array = tmp_msg_arr[1].split(",");
          if (tmp_msg_arr[0] == "0") {
            $("#hidden_execute").html('<?php echo tep_draw_hidden_field('execute_delete', BUTTON_DELETE_ONCE_PWD_LOG);?>'); 
            document.forms.user_form.submit(); 
          } else {
            var input_pwd_str = window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>", ""); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: "ajax_orders.php?action=record_pwd_log",   
                type: "POST",
                dataType: "text",
                data: "current_pwd="+input_pwd_str+"&url_redirect_str="+encodeURIComponent(document.forms.user_form.action),
                async: false,
                success: function(msg_info) {
                  $("#hidden_execute").html('<?php echo tep_draw_hidden_field('execute_delete', BUTTON_DELETE_ONCE_PWD_LOG);?>'); 
                  document.forms.user_form.submit(); 
                }
              }); 
            } else {
              alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>"); 
            }
          }
        }
      });
    } else {
      $("#hidden_execute").html('<?php echo tep_draw_hidden_field('execute_delete', BUTTON_DELETE_ONCE_PWD_LOG);?>'); 
      document.forms.user_form.submit(); 
    }
  }
}
function all_select_logs(logs_list_id){
   var check_flag = document.users_form.all_check.checked;
   if (document.users_form.elements[logs_list_id]) {
     if (document.users_form.elements[logs_list_id].length == null) {
        if (!document.users_form.elements[logs_list_id].disabled) {
           if (check_flag == true) {
              document.users_form.elements[logs_list_id].checked = true;
            } else {
               document.users_form.elements[logs_list_id].checked = false;
            }
         }
      } else {
         for (i = 0; i < document.users_form.elements[logs_list_id].length; i++) {
             if(!document.users_form.elements[logs_list_id][i].disabled) {
                 if (check_flag == true) {
                    document.users_form.elements[logs_list_id][i].checked = true;
                 } else {
                    document.users_form.elements[logs_list_id][i].checked = false;
                 }
              }
         }
      }
    }
}
function select_logs_change(value,logs_list_id,c_permission){
  sel_num = 0;
  if (document.users_form.elements[logs_list_id].length == null) {
    if (document.users_form.elements[logs_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.users_form.elements[logs_list_id].length; i++) {
      if (document.users_form.elements[logs_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  } 

  if(sel_num == 1){
    if (confirm("<?php echo TEXT_LOGS_EDIT_CONFIRM;?>")) {
      if (c_permission == 31) {
        document.users_form.submit(); 
      } else {
      $.ajax({
        url: "ajax_orders.php?action=getallpwd",   
        type: "POST",
        dataType: "text",
        data: "current_page_name=<?php echo $_SERVER['PHP_SELF'];?>", 
        async: false,
        success: function(msg) {
          var tmp_msg_arr = msg.split("|||"); 
          var pwd_list_array = tmp_msg_arr[1].split(",");
          if (tmp_msg_arr[0] == "0") {
            document.users_form.submit(); 
          } else {
            var input_pwd_str = window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>", ""); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: "ajax_orders.php?action=record_pwd_log",   
                type: "POST",
                dataType: "text",
                data: "current_pwd="+input_pwd_str+"&url_redirect_str="+encodeURIComponent("<?php echo tep_href_link(FILENAME_ALERT_LOG, ($_GET['page'] != '' ?  'page='.$_GET['page'] : ''));?>"),
                async: false,
                success: function(msg_info) {
                  document.users_form.submit(); 
                }
              }); 
            } else {
              document.getElementsByName("users_form_list")[0].value = 0;
              alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>"); 
            }
          }
        }
      });
      }
    }else{
      document.getElementsByName("users_form_list")[0].value = 0;
    } 
  }else{
    document.getElementsByName("users_form_list")[0].value = 0;
    alert("<?php echo TEXT_LOGS_EDIT_MUST_SELECT;?>"); 
  }
}
</script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
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
      <td width="100%" valign="top" id="categories_right_td">
          <div class="box_warp"><?php echo $notes;?>
             <div class="compatible">
              <table width="100%" cellspacing="0" cellpadding="2" border="0">
                    <tr>
                      <td>
                         <table border="0" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                               <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                               <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
                            </tr>
                            <tr>
                               <td>
                                  <table width="100%" cellspacing="0" cellpadding="0" border="0"> 
                                   <tr>      
                                     <td>
<?php 
  if(!isset($_GET['type']) || $_GET['type'] == ''){
      $_GET['type'] = 'asc';
  }
  if($contents_type == ''){
      $alert_log_type = 'asc';
  }
  if(!isset($_GET['sort']) || $_GET['sort'] == ''){
      $alert_log_str = ' created_at desc';
  }else if($_GET['sort'] == 'username'){
      if($_GET['type'] == 'desc'){
        $alert_log_str = 'username desc';
        $alert_log_type = 'asc';
      }else{
        $alert_log_str = 'username asc';
        $alert_log_type = 'desc';
      }
  }else if($_GET['sort'] == 'pwd_username'){
      if($_GET['type'] == 'desc'){
        $alert_log_str = 'pwd_username desc';
        $alert_log_type = 'asc';
      }else{
        $alert_log_str = 'pwd_username asc';
        $alert_log_type = 'desc';
      }
  }else if($_GET['sort'] == 'url'){
      if($_GET['type'] == 'desc'){
        $alert_log_str = 'url desc';
        $alert_log_type = 'asc';
      }else{
        $alert_log_str = 'url asc';
        $alert_log_type = 'desc';
      }
  }else if($_GET['sort'] == 'created_at'){
      if($_GET['type'] == 'desc'){
        $alert_log_str = 'created_at desc';
        $alert_log_type = 'asc';
      }else{
        $alert_log_str = 'created_at asc';
        $alert_log_type = 'desc';
      }
  }
  if($_GET['sort'] == 'username'){
       if($_GET['type'] == 'desc'){
          $alert_log_username = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
        }else{
          $alert_log_username = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
        }
  }
  if($_GET['sort'] == 'pwd_username'){
       if($_GET['type'] == 'desc'){
          $alert_log_pwd_username = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
        }else{
          $alert_log_pwd_username = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
        }
  }
  if($_GET['sort'] == 'url'){
       if($_GET['type'] == 'desc'){
          $alert_log_url = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
        }else{
          $alert_log_url = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
        }
  }
 if($_GET['sort'] == 'created_at'){
       if($_GET['type'] == 'desc'){
          $alert_log_created_at = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
        }else{
          $alert_log_created_at = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
        }
  }
  // 现在的页面（获取记录开始位置）
  $alarm_day = get_configuration_by_site_id('ALARM_EXPIRED_DATE_SETTING',0);
  $del_select = "select * from " . TABLE_ONCE_PWD_LOG ." where time_format(timediff(now(),created_at),'%H')>".$alarm_day*24;
  $del_select .= " order by created_at desc";
  $del_select_query = tep_db_query($del_select);
  $del_nrow = tep_db_num_rows($del_select_query);
  if($del_nrow > 0){
    while($alert_log_array = tep_db_fetch_array($del_select_query)){
         $logs_id[] = $alert_log_array['id'];
    }
    $log_list_str = implode(",",$logs_id);
    tep_db_query("delete from ".TABLE_ONCE_PWD_LOG." where id in (".$log_list_str.")");
  }
  $s_select = "select * from " . TABLE_ONCE_PWD_LOG;
  $s_select .= " order by ".$alert_log_str;    // 按照访问日期时间的倒序获取数据
  $ssql = $s_select;
  $alert_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $ssql, $alert_query_numrows);
  $oresult = tep_db_query($ssql);
    $nrow = tep_db_num_rows($oresult);              // 获取记录件数
    $site_query = tep_db_query("select id from ".TABLE_SITES);
    $site_list_array = array();
    while($site_array = tep_db_fetch_array($site_query)){
          $site_list_array[] = $site_array['id'];
    }
    echo tep_show_site_filter(FILENAME_ALERT_LOG,false,$site_list_array);
    $alert_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
    $notice_box = new notice_box('','',$alert_table_params);
    $alert_table_row = array();
    $alert_title_row = array();
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="hidden" name="execute_delete" value="1"><input type="checkbox" onclick="all_select_logs(\'logs_list_id[]\');" name="all_check">');
    if(isset($_GET['sort']) && $_GET['sort'] == 'username'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PWD_LOG,'sort=username&page='.$_GET['page'].'&type='.$alert_log_type).'">'.TABLE_HEADING_USERNAME.$alert_log_username.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PWD_LOG,'sort=username&page='.$_GET['page'].'&type=desc').'">'.TABLE_HEADING_USERNAME.$alert_log_username.'</a>');
    }
    if(isset($_GET['sort']) && $_GET['sort'] == 'pwd_username'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PWD_LOG,'sort=pwd_username&page='.$_GET['page'].'&type='.$alert_log_type).'">'.TABLE_HEADING_PWD_USERNAME.$alert_log_pwd_username.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PWD_LOG,'sort=pwd_username&page='.$_GET['page'].'&type=desc').'">'.TABLE_HEADING_PWD_USERNAME.$alert_log_pwd_username.'</a>');
    }
    if(isset($_GET['sort']) && $_GET['sort'] == 'url'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PWD_LOG,'sort=url&page='.$_GET['page'].'&type='.$alert_log_type).'">'.TABLE_HEADING_URL.$alert_log_url.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PWD_LOG,'sort=url&page='.$_GET['page'].'&type=desc').'">'.TABLE_HEADING_URL.$alert_log_url.'</a>');
    }
    if(isset($_GET['sort']) && $_GET['sort'] == 'created_at'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PWD_LOG,'sort=created_at&page='.$_GET['page'].'&type='.$alert_log_type).'">'.TABLE_HEADING_CREATED_AT.$alert_log_created_at.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_PWD_LOG,'sort=created_at&page='.$_GET['page'].'&type=desc').'">'.TABLE_HEADING_CREATED_AT.$alert_log_created_at.'</a>');
    }
    $alert_title_row[] = array('params' => 'align="right" class="dataTableHeadingContent"','text' => TABLE_HEADING_ACTION); 
    $alert_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $alert_title_row);
  // 列表显示数据
  if ($nrow > 0) {                      // 取不到记录的时候
    $is_disabled_single = false;
    if ($ocertify->npermission != 31) {
       $c_site_query = tep_db_query("select * from ".TABLE_PERMISSIONS." where userid = '".$ocertify->auth_user."'");
       $c_site_res = tep_db_fetch_array($c_site_query);
       $tmp_c_site_array = explode(',', $c_site_res['site_permission']);
       if (!empty($tmp_c_site_array)) {
          if (!in_array('0', $tmp_c_site_array)) {
              $is_disabled_single = true;
          }
       } else {
         $is_disabled_single = true;
       }
    }
 
  $rec_c = 1;
  while ($arec = tep_db_fetch_array($oresult)) {      // 获取记录
    $naddress = (int)$arec['address'];    // IP地址复原
    $saddress = '';
    for ($i=0; $i<4; $i++) {
      if ($i) $saddress = ($naddress & 0xff) . '.' . $saddress;
      else $saddress = (string)($naddress & 0xff);
      $naddress >>= 8;
    }
  $even = 'dataTableSecondRow';
  $odd = 'dataTableRow';
  if ($rec_c % 2) {
    $nowColor = $even;
  } else {
    $nowColor = $odd;
  }
    if(isset($_GET['log_id'])&&$_GET['log_id']==$arec['id']){
      $alert_params ='class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ';
    }else{
      $alert_params ='class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'' . $nowColor . '\'"';
    }
    $alert_info = array();
    $alert_info[] = array(
        'params' => 'class="main"',
        'text'   => '<input type="checkbox" value="'.$arec['id'].'" name="logs_list_id[]"'.(($is_disabled_single)?' disabled="disabled"':'').'>'
        );
    $alert_info[] = array(
        'params' => 'onClick="document.location.href=\'' .  tep_href_link(FILENAME_PWD_LOG,"log_id=".$arec['id'].'&page='.$_GET['page']) .'\'" class="main"',
        'text'   => $arec['username']
        );
    $alert_info[] = array(
        'params' => 'onClick="document.location.href=\'' .  tep_href_link(FILENAME_PWD_LOG,"log_id=".$arec['id'].'&page='.$_GET['page']) .'\'" class="main"',
        'text'   => $arec['pwd_username']
        );
    $alert_info[] = array(
        'params' => 'onClick="document.location.href=\'' .  tep_href_link(FILENAME_PWD_LOG,"log_id=".$arec['id'].'&page='.$_GET['page']) .'\'" class="main"',
        'text'   => $arec['url']
        );
    $alert_info[] = array(
        'params' => 'onClick="document.location.href=\'' .  tep_href_link(FILENAME_PWD_LOG,"log_id=".$arec['id'].'&page='.$_GET['page']) .'\'" class="main"',
        'text'   => $arec['created_at']
        );
    $alert_info[] = array(
        'params' => 'class="main" align="right"',
        'text'   => tep_image('images/icons/info_gray.gif') 
        );
    $alert_table_row[] = array('params' => $alert_params, 'text' => $alert_info);
    $rec_c++;
   }
    $alert_log_form = tep_draw_form('users_form',FILENAME_PWD_LOG,'action=deleteconfirm&page='.$_GET['page']);
    $notice_box->get_form($alert_log_form);
    $notice_box->get_contents($alert_table_row);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
    echo tep_draw_form('user_form',FILENAME_PWD_LOG);
    echo '<table width="100%" cellspacing="0" cellpadding="0" border="0"> ';
    echo '<tr>';
    echo '<td>';
    if($ocertify->npermission >= 15 && $nrow > 0){
       echo '<div class="td_box">';
       echo '<select name="users_form_list" onchange="select_logs_change(this.value,\'logs_list_id[]\',\''.$ocertify->npermission.'\');">';
       echo '<option value="0">'.TEXT_LOGS_EDIT_SELECT.'</option>';
       echo '<option value="1">'.TEXT_LOGS_EDIT_DELETE.'</option>';
       echo '</select>';
       echo '</div>';
    }
    echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo '<td>';
    echo $alert_split->display_count($alert_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ALERT);
    echo '<div class="td_box">'.$alert_split->display_links($alert_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))).'</div>';
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo "</form>\n";           // form的footer
  }
?>

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
       </td>
<!-- body_text_eof -->
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
