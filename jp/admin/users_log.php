<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  // 自动删除过期数据
  $alarm_day = get_configuration_by_site_id('USERS_EXPIRED_DATE_SETTING',0); 
  tep_db_query("delete from login where  is_locked='0' and time_format(timediff(now(),logintime),'%H')>".$alarm_day*24);
  define('TABLE_LOGIN', 'login');
  if (isset($_POST['sp'])) { $sp = $_POST['sp']; }
  if (isset($_POST['execute_delete'])) { $execute_delete = $_POST['execute_delete']; }
  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
    case 'deleteconfirm':
       $logs_list_array = $_POST['logs_list_id'];
       if(isset($_POST['logs_list_id'])&&!empty($_POST['logs_list_id'])){
            foreach($_POST['logs_list_id'] as $ge_key => $ge_value){
               tep_db_query("delete from ".TABLE_LOGIN." where sessionid = '".$ge_value."'"); 
            }
       }
       tep_redirect(tep_href_link('users_log.php','page='.$_GET['page']));
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
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" src="includes/javascript/show_site.js.php"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script language="JavaScript1.1">
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
function ip_unlock(ip,user){
  $.ajax({
          dataType: "text",
          type:"POST",
          data:"ip="+ip+"&user="+user,
          async:false, 
          url: "ajax_users_log.php?action=ip_unlock",
          success: function(data) {
            if (data == "success") {
              location.href="users_log.php?sort=<?php echo $_GET['sort'];?>&type=<?php echo $_GET['type'];?>&page=<?php echo $_GET['page'];?>";
            }
          }
  });
}
function ip_lock(ip,user){
  $.ajax({
          dataType: "text",
          type:"POST",
          data:"ip="+ip+"&user="+user,
          async:false, 
          url: "ajax_users_log.php?action=ip_lock",
          success: function(data) {
            if (data == "success") {
              location.href="users_log.php?sort=<?php echo $_GET['sort'];?>&type=<?php echo $_GET['type'];?>&page=<?php echo $_GET['page'];?>";
            }
          }
  });
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
</script>
<?php 
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
    if(!isset($_GET['sort']) || $_GET['sort'] == ''){
       $contents_str = 'logintime desc'; 
    }else if($_GET['sort'] == 'account'){
      if($_GET['type'] == 'desc'){
        $contents_str = 'account desc';
        $contents_type = 'asc';
      }else{
        $contents_str = 'account asc';
        $contents_type = 'desc';
      }
    }else if($_GET['sort'] == 'logintime'){
      if($_GET['type'] == 'desc'){
        $contents_str = 'logintime desc';
        $contents_type = 'asc';
      }else{
        $contents_str = 'logintime asc';
        $contents_type = 'desc';
      }
    }else if($_GET['sort'] == 'lastaccesstime'){
      if($_GET['type'] == 'desc'){
        $contents_str = 'lastaccesstime desc';
        $contents_type = 'asc';
      }else{
        $contents_str = 'lastaccesstime asc';
        $contents_type = 'desc';
      }
    }else if($_GET['sort'] == 'status'){
      if($_GET['type'] == 'desc'){
        $contents_str = 'loginstatus  desc';
        $contents_type = 'asc';
      }else{
        $contents_str = 'logoutstatus desc';
        $contents_type = 'desc';
      }
    }else if($_GET['sort'] == 'address'){
      if($_GET['type'] == 'desc'){
        $contents_str = 'address desc';
        $contents_type = 'asc';
      }else{
        $contents_str = 'address asc';
        $contents_type = 'desc';
      }
    }else if($_GET['sort'] == 'is_locked'){
      if($_GET['type'] == 'desc'){
        $contents_str = 'is_locked desc';
        $contents_type = 'asc';
      }else{
        $contents_str = 'is_locked asc';
        $contents_type = 'desc';
      }
    }
   if($_GET['sort'] == 'account'){
        if($_GET['type'] == 'desc'){
            $alert_account = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
        }else{
            $alert_account = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
        }
   }
   if($_GET['sort'] == 'logintime'){
        if($_GET['type'] == 'desc'){
            $alert_logintime = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
        }else{
            $alert_logintime = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
        }
   }
   if($_GET['sort'] == 'lastaccesstime'){
        if($_GET['type'] == 'desc'){
            $alert_lastaccesstime = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
        }else{
            $alert_lastaccesstime = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
        }
   }
   if($_GET['sort'] == 'status'){
        if($_GET['type'] == 'desc'){
            $alert_status = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
        }else{
            $alert_status = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
        }
   }
   if($_GET['sort'] == 'address'){
        if($_GET['type'] == 'desc'){
            $alert_address = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
        }else{
            $alert_address = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
        }
   }
   if($_GET['sort'] == 'is_locked'){
        if($_GET['type'] == 'desc'){
            $alert_is_locked = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
        }else{
            $alert_is_locked = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
        }
   }
  $aval = explode(',',TEXT_INFO_STATUS_IN);
  if (is_array($aval)) {
    while (list($key,$val) = each($aval)) {
      $sts = explode(':',$val);
      $a_sts_in[$sts[0]] = $sts[1];
    }
  }
  // 设置退出登录状态数组
  $aval = explode(',',TEXT_INFO_STATUS_OUT);
  if (is_array($aval)) {
    while (list($key,$val) = each($aval)) {
      $sts = explode(':',$val);
      $a_sts_out[$sts[0]] = $sts[1];
    }
  }
  // 现在的页面（获取记录开始位置）
  if (isset($jp) && $jp) $lm = (int)$sp;
  if (isset($pp) && $pp) (int)$lm -= LOGIN_LOG_MAX_LINE;
  if (isset($np) && $np) (int)$lm += LOGIN_LOG_MAX_LINE;

  // 获取访问日志信息
  $s_select = "select * from " . TABLE_LOGIN;
  $s_select .= " order by ".$contents_str; 
  if (!isset($lm)) $lm = 0;
  $ssql = $s_select;
  $alert_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $ssql, $alert_query_numrows);
  $oresult = tep_db_query($ssql);
  $nrow = tep_db_num_rows($oresult);              // 获取记录件数
  if ($nrow > 0) {                      
    if ($ocertify->npermission != 31) {
        $c_site_query = tep_db_query("select * from ".TABLE_PERMISSIONS." where userid = '".$ocertify->auth_user."'");
        $c_site_res = tep_db_fetch_array($c_site_query);
        $tmp_c_site_array = explode(',',
        $c_site_res['site_permission']);
        if (!empty($tmp_c_site_array)) {
            if (!in_array('0', $tmp_c_site_array)) {
                $is_disabled_single = true;
             }
        } else {
            $is_disabled_single = true;
        }
    }
    $site_query = tep_db_query("select id from ".TABLE_SITES);
    $site_list_array = array();
    while($site_array = tep_db_fetch_array($site_query)){
          $site_list_array[] = $site_array['id'];
    }
    echo tep_show_site_filter('users_log.php',false,$site_list_array);
    $alert_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
    $notice_box = new notice_box('','',$alert_table_params);
    $alert_table_row = array();
    $alert_title_row = array();
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="hidden" name="execute_delete" value="1"><input type="checkbox" onclick="all_select_logs(\'logs_list_id[]\');" name="all_check">');
    if(isset($_GET['sort']) && $_GET['sort'] == 'account'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('users_log.php','page='.$_GET['page'].'&sort=account&type='.$contents_type).'">'.TABLE_HEADING_USER.$alert_account.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('users_log.php','page='.$_GET['page'].'&sort=account&type=desc').'">'.TABLE_HEADING_USER.$alert_account.'</a>');
    }
    if(isset($_GET['sort']) && $_GET['sort'] == 'logintime'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('users_log.php','page='.$_GET['page'].'&sort=logintime&type='.$contents_type).'">'.TABLE_HEADING_LOGINTIME.$alert_logintime.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('users_log.php','page='.$_GET['page'].'&sort=logintime&type=desc').'">'.TABLE_HEADING_LOGINTIME.$alert_logintime.'</a>');
    }
    if(isset($_GET['sort']) && $_GET['sort'] == 'lastaccesstime'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('users_log.php','page='.$_GET['page'].'&sort=lastaccesstime&type='.$contents_type).'">'.TABLE_HEADING_LAST_ACCESSTIME.$alert_lastaccesstime.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('users_log.php','page='.$_GET['page'].'&sort=lastaccesstime&type=desc').'">'.TABLE_HEADING_LAST_ACCESSTIME.$alert_lastaccesstim.'</a>');
    }
    if(isset($_GET['sort']) && $_GET['sort'] == 'status'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('users_log.php','page='.$_GET['page'].'&sort=status&type='.$contents_type).'">'.TABLE_HEADING_STATUS.$alert_status.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('users_log.php','page='.$_GET['page'].'&sort=status&type=desc').'">'.TABLE_HEADING_STATUS.$alert_status.'</a>');
    }
    if(isset($_GET['sort']) && $_GET['sort'] == 'address'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('users_log.php','page='.$_GET['page'].'&sort=address&type='.$contents_type).'">'.TABLE_HEADING_ADDRESS.$alert_address.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('users_log.php','page='.$_GET['page'].'&sort=address&type=desc').'">'.TABLE_HEADING_ADDRESS.$alert_address.'</a>');
    }
    if(isset($_GET['sort']) && $_GET['sort'] == 'is_locked'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('users_log.php','page='.$_GET['page'].'&sort=is_locked&type='.$contents_type).'">'.TEXT_LOCK.$alert_is_locked.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('users_log.php','page='.$_GET['page'].'&sort=is_locked&type=desc').'">'.TEXT_LOCK.$alert_is_locked.'</a>');
    }
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent" align="right"','text' => TABLE_HEADING_ACTION);
    $alert_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $alert_title_row);
  // 列表显示数据
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
    if(isset($_GET['sid'])&&$_GET['sid']==$arec['sessionid']){
       $alert_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ';
    }else{
       $alert_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'' . $nowColor . '\'"';
    }
    $alert_info = array();
    $alert_info[] = array(
        'params' => 'class="main"',
        'text'   => '<input type="checkbox" value="'.$arec['sessionid'].'" name="logs_list_id[]"'.(($is_disabled_single)?' disabled="disabled"':'').'>'
        );
    $alert_info[] = array(
        'params' => 'class="main" onClick="document.location.href=\'' .  tep_href_link('users_log.php',"sid=".$arec['sessionid'].'&page='.$_GET['page']) .'\'"',
        'text'   => $arec['account']
        );
    $alert_info[] = array(
        'params' => 'class="main" onClick="document.location.href=\'' .  tep_href_link('users_log.php',"sid=".$arec['sessionid'].'&page='.$_GET['page']) .'\'"',
        'text'   => $arec['logintime']
        );
    $alert_info[] = array(
        'params' => 'class="main" onClick="document.location.href=\'' .  tep_href_link('users_log.php',"sid=".$arec['sessionid'].'&page='.$_GET['page']) .'\'"',
        'text'   => $arec['lastaccesstime']
        );
    // 退出登录状态
    if ($arec['logoutstatus']) {
    $alert_info[] = array(
        'params' => 'class="main" onClick="document.location.href=\'' .  tep_href_link('users_log.php',"sid=".$arec['sessionid'].'&page='.$_GET['page']) .'\'"',
        'text'   =>  ' [' . $a_sts_in[$arec['loginstatus']] .  ']&nbsp;&nbsp;'. ' [' .  $a_sts_out[$arec['logoutstatus']] . ']'
        );
    }else {
    $alert_info[] = array(
        'params' => 'class="main" onClick="document.location.href=\'' .  tep_href_link('users_log.php',"sid=".$arec['sessionid'].'&page='.$_GET['page']) .'\'"',
        'text'   => ' [' . $a_sts_in[$arec['loginstatus']] .  ']'
        );
    }
    $alert_info[] = array(
        'params' => 'class="main" onClick="document.location.href=\'' .  tep_href_link('users_log.php',"sid=".$arec['sessionid'].'&page='.$_GET['page']) .'\'"',
        'text'   => $saddress
        );
    if($arec['is_locked'] == '0'){
    if($is_disabled_single){
    $alert_info[] = array(
        'params' => 'class="main"',
        'text'   => tep_image('images/icons/unlock.gif','','','','disabled="disabled"')
        );
    }else{
    $alert_info[] = array(
        'params' => 'class="main"',
        'text'   => '<a href="javascript:void(0)" onclick="if(confirm(\''.TEXT_CONFIRM.'\')){ip_lock(\''.$arec['address'].'\',\''.$arec['account'].'\');}">'.tep_image('images/icons/unlock.gif').'</a>'
        );
    }
    }else{
    if($is_disabled_single){
     $alert_info[] = array(
        'params' => 'class="main"',
        'text'   => tep_image('images/icons/lock.gif','','','','disabled="disabled"')
        );
    }else{
     $alert_info[] = array(
        'params' => 'class="main"',
        'text'   => '<a href="javascript:void(0)" onclick="if(confirm(\''.TEXT_DELETE_CONFIRM.'\')){ip_unlock(\''.$arec['address'].'\',\''.$arec['account'].'\');}">'.tep_image('images/icons/lock.gif').'</a>' 
        );
    }
    }
    $alert_info[] = array(
        'params' => 'class="main" align="right"',
        'text'   => tep_image('images/icons/info_gray.gif')
        );
    $alert_table_row[] = array('params' => $alert_params, 'text' => $alert_info);
    $rec_c++;
  }
  $alert_log_form = tep_draw_form('users_form','users_log.php','action=deleteconfirm&page='.$_GET['page']);
  $notice_box->get_form($alert_log_form);
  $notice_box->get_contents($alert_table_row);
  $notice_box->get_eof(tep_eof_hidden());
  echo $notice_box->show_notice();
  }else{
    echo '<tr><td><font color="red"><b>'.TEXT_DATA_IS_EMPTY.'</b></font></td></tr>';
  }
    echo "</table>\n";
    echo '</td></tr><tr><td>';
  if($ocertify->npermission >= 15 && $nrow > 0){
    echo '<div class="td_box">';
    echo '<select name="users_form_list" onchange="select_logs_change(this.value,\'logs_list_id[]\',\''.$ocertify->npermission.'\');">';
    echo '<option value="0">'.TEXT_LOGS_EDIT_SELECT.'</option>';
    echo '<option value="1">'.TEXT_LOGS_EDIT_DELETE.'</option>';
    echo '</select>';
    echo '</div>';
  }
  echo $alert_split->display_count($alert_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ALERT);
  echo '<div class="td_box">'.$alert_split->display_links($alert_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))).'</div>';
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

