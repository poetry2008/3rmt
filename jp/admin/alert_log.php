<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  if (isset($_POST['sp'])) { $sp = $_POST['sp']; }
  if (isset($_POST['execute_delete'])) { $execute_delete = $_POST['execute_delete']; }
  if (isset($_GET['action'])) {
  switch ($_GET['action']) {
  case 'deleteconfirm':
  $logs_list_array = $_POST['logs_list_id'];
  $logs_list_str = implode(',',$logs_list_array);
  $alert_query = tep_db_query("select from_notice from ".TABLE_NOTICE." where type='0' and id in (".$logs_list_str.")");
  while($alert_array = tep_db_fetch_array($alert_query)){
    tep_db_query("delete from ".TABLE_ALARM." where alarm_id='".$alert_array['from_notice']."'");
  }
  $alert_query = tep_db_query("select id,from_notice from ".TABLE_NOTICE." where type='1' and id in (".$logs_list_str.")");
  while($alert_array = tep_db_fetch_array($alert_query)){
    tep_db_query("delete from ".TABLE_BUSINESS_MEMO." where id='".$alert_array['from_notice']."'");
  }
  $result = tep_db_query("delete from ".TABLE_NOTICE." where id in (".$logs_list_str.")");
  tep_redirect(tep_href_link(FILENAME_ALERT_LOG,'page='.$_GET['page']));
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
<script language="javascript">
function formConfirm(type) {
  if (type == "delete") {
      rtn = confirm("<?php echo  JAVA_SCRIPT_INFO_DELETE;?>");
  }
  if (rtn) return true;
  else return false;
}
function all_select_logs(logs_list_id){
  var check_flag = document.edit_logs.all_check.checked;
  if (document.edit_logs.elements[logs_list_id]) {
    if (document.edit_logs.elements[logs_list_id].length == null) {
      if (!document.edit_logs.elements[logs_list_id].disabled) {
        if (check_flag == true) {
          document.edit_logs.elements[logs_list_id].checked = true;
        } else {
          document.edit_logs.elements[logs_list_id].checked = false;
        }
      }
    } else {
      for (i = 0; i < document.edit_logs.elements[logs_list_id].length; i++) {
        if (!document.edit_logs.elements[logs_list_id][i].disabled) {
          if (check_flag == true) {
            document.edit_logs.elements[logs_list_id][i].checked = true;
          } else {
            document.edit_logs.elements[logs_list_id][i].checked = false;
          }
        }
      }
    }
  }
}

function select_logs_change(value,logs_list_id,c_permission){
  sel_num = 0;
  if (document.edit_logs.elements[logs_list_id].length == null) {
    if (document.edit_logs.elements[logs_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_logs.elements[logs_list_id].length; i++) {
      if (document.edit_logs.elements[logs_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  } 

  if(sel_num == 1){
    if (confirm("<?php echo TEXT_LOGS_EDIT_CONFIRM;?>")) {
      if (c_permission == 31) {
        document.edit_logs.submit(); 
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
            document.edit_logs.submit(); 
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
                  document.edit_logs.submit(); 
                }
              }); 
            } else {
              document.getElementsByName("edit_logs_list")[0].value = 0;
              alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>"); 
            }
          }
        }
      });
      }
    }else{

      document.getElementsByName("edit_logs_list")[0].value = 0;
    } 
  }else{
    document.getElementsByName("edit_logs_list")[0].value = 0;
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
  }else if($_GET['sort'] == 'title'){
     if($_GET['type'] == 'desc'){
      $alert_log_str = 'title desc'; 
      $alert_log_type = 'asc';
     }else{
      $alert_log_str = 'title asc'; 
      $alert_log_type = 'desc';
     }
  }else if($_GET['sort'] == 'type'){
     if($_GET['type'] == 'desc'){
      $alert_log_str = 'type desc'; 
      $alert_log_type = 'asc';
     }else{
      $alert_log_str = 'type asc'; 
      $alert_log_type = 'desc';
     }
  }else if($_GET['sort'] == 'from_notice'){
     if($_GET['type'] == 'desc'){
      $alert_log_str = 'from_notice desc'; 
      $alert_log_type = 'asc';
     }else{
      $alert_log_str = 'from_notice asc'; 
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

 if($_GET['sort'] == 'title'){
     if($_GET['type'] == 'desc'){
       $alert_log_title = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
     }else{
       $alert_log_title = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
     }
  }
 if($_GET['sort'] == 'type'){
     if($_GET['type'] == 'desc'){
       $alert_type = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
     }else{
       $alert_type = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
     }
  }
 if($_GET['sort'] == 'from_notice'){
     if($_GET['type'] == 'desc'){
       $alert_from_notice = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
     }else{
       $alert_from_notice = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
     }
  }
  if($_GET['sort'] == 'created_at'){
     if($_GET['type'] == 'desc'){
       $alert_created_at = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
     }else{
       $alert_created_at = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
     }
  }
  // 获取提醒日志信息
  $alarm_day = get_configuration_by_site_id('ALARM_EXPIRED_DATE_SETTING',0);
  $s_select = "select * from " . TABLE_NOTICE ." where time_format(timediff(now(),created_at),'%H')<".$alarm_day*24;
  $s_select .= " order by ".$alert_log_str;    // 按照提醒日期时间的倒序获取数据
  $del_select = "select * from " . TABLE_NOTICE ." where time_format(timediff(now(),created_at),'%H')>".$alarm_day*24; 
  $del_select .= " order by created_at desc"; 
  $del_select_query = tep_db_query($del_select);
  $del_nrow = tep_db_num_rows($del_select_query);
  if($del_nrow > 0){
  while($alert_log_array = tep_db_fetch_array($del_select_query)){
        $logs_id[] = $alert_log_array['id'];    
  }
  $log_list_str = implode(",",$logs_id);
  $alert_query = tep_db_query("select from_notice from ".TABLE_NOTICE." where type='0' and id in (".$log_list_str.")");
  while($alert_array = tep_db_fetch_array($alert_query)){
    tep_db_query("delete from ".TABLE_ALARM." where alarm_id='".$alert_array['from_notice']."'");
  }
  $alert_query = tep_db_query("select id,from_notice from ".TABLE_NOTICE." where type='1' and id in (".$log_list_str.")");
  while($alert_array = tep_db_fetch_array($alert_query)){
    tep_db_query("delete from ".TABLE_BUSINESS_MEMO." where id='".$alert_array['from_notice']."'");
  }
    tep_db_query("delete from ".TABLE_NOTICE." where id in (".$log_list_str.")");
  }
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
    // 表标签的开始
    $alert_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
    $notice_box = new notice_box('','',$alert_table_params);
    $alert_table_row = array(); 
    $alert_title_row = array();
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="hidden" name="execute_delete" value="1"><input type="checkbox" onclick="all_select_logs(\'logs_list_id[]\');" name="all_check">');
    if(isset($_GET['sort']) && $_GET['sort'] == 'title'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_ALERT_LOG,'sort=title&page='.$_GET['page'].'&type='.$alert_log_type).'">'.TABLE_HEADING_USERNAME.$alert_log_title.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_ALERT_LOG,'sort=title&page='.$_GET['page'].'&type=desc').'">'.TABLE_HEADING_USERNAME.$alert_log_title.'</a>');
    }
    if(isset($_GET['sort']) && $_GET['sort'] == 'type'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_ALERT_LOG,'sort=type&page='.$_GET['page'].'&type='.$alert_log_type).'">'.TABLE_HEADING_TYPE.$alert_type.'<br>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_ALERT_LOG,'sort=type&page='.$_GET['page'].'&type=desc').'">'.TABLE_HEADING_TYPE.$alert_type.'<br>');
    }
    if(isset($_GET['sort']) && $_GET['sort'] == 'from_notice'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_ALERT_LOG,'sort=from_notice&page='.$_GET['page'].'&type='.$alert_log_type).'">'.TABLE_HEADING_BUTTON_NAME.$alert_from_notice.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_ALERT_LOG,'sort=from_notice&page='.$_GET['page'].'&type=desc').'">'.TABLE_HEADING_BUTTON_NAME.$alert_from_notice.'</a>');
    }
    if(isset($_GET['sort']) && $_GET['sort'] == 'created_at'){
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_ALERT_LOG,'sort=created_at&page='.$_GET['page'].'&type='.$alert_log_type).'">'.TABLE_HEADING_CREATED_AT.$alert_created_at.'</a>');
    }else{
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_ALERT_LOG,'sort=created_at&page='.$_GET['page'].'&type=desc').'">'.TABLE_HEADING_CREATED_AT.$alert_created_at.'</a>');
    }
    $alert_title_row[] = array('params' => 'class="dataTableHeadingContent" align="right"','text' => TABLE_HEADING_ACTION);
    $alert_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $alert_title_row);
if ($nrow > 0) {                      // 取不到记录的时候
  $rec_c = 1;
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
      $alert_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ';
    }else{
      $alert_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'' . $nowColor . '\'"';
    }
    $alert_button_comment = '';
    $alert_button_title = '';
    //根据不同的提醒类型，获取不同的内容
    if($arec['type'] == '0'){
      $alarm_info_query = tep_db_query("select * from ".TABLE_ALARM." where alarm_id='".$arec['from_notice']."'");
      $alarm_info_array = tep_db_fetch_array($alarm_info_query);

      $alert_user = $alarm_info_array['adminuser'];
      if($alarm_info_array['alarm_flag'] == '0'){
        $user_info = tep_get_user_info($alert_user);
        $alert_user = $user_info['name'];
      }
      if($alarm_info_array['alarm_flag'] == '1'){
        if($alarm_info_array['orders_flag'] == '1'){
          $alert_button_name = HEADER_TEXT_ALERT_TITLE;
        }else{
          $alert_button_name = HEADER_TEXT_ALERT_TITLE_PREORDERS; 
        }
        $alert_button_comment = '「'.(mb_strlen($alarm_info_array['title'],'utf-8') > 30 ? mb_substr($alarm_info_array['title'],0,30,'utf-8').'...' : $alarm_info_array['title']).'」/&nbsp;'.($alarm_info_array['alarm_show'] == '1' ? 'ON' : 'OFF');
      }else{
        $alert_button_name = NOTICE_ALARM_TITLE; 
        $alert_button_array = explode($alarm_info_array['orders_id'],$alarm_info_array['title']);
        if($alert_button_array[0] == ''){

          array_shift($alert_button_array);
          $alert_button_title = implode($alarm_info_array['orders_id'],$alert_button_array);
        }else{
          $alert_button_title = $alarm_info_array['title']; 
        }
        $alert_button_comment = $arec['set_time'];
      }
      $alert_orders_id = $alarm_info_array['orders_id'];
    }else{
      $micro_info_query = tep_db_query("select * from ".TABLE_BUSINESS_MEMO." where id='".$arec['from_notice']."'");
      $micro_info_array = tep_db_fetch_array($micro_info_query);

      $alert_user = $micro_info_array['from'];
      $user_info = tep_get_user_info($alert_user);
      $alert_user = $user_info['name'];
      $alert_button_name = NOTICE_EXTEND_TITLE;
      $alert_button_comment = mb_strlen($arec['title'],'utf-8') > 30 ? mb_substr($arec['title'],0,30,'utf-8').'...' : $arec['title'];
      $alert_orders_id = ''; 
    }
    $alert_info = array(); 
    $alert_info[] = array(
        'params' => ' class="main"',
        'text'   => '<input type="checkbox" value="'.$arec['id'].'" name="logs_list_id[]"'.(($is_disabled_single)?' disabled="disabled"':'').'>'
        ); 
    $alert_info[] = array(
        'params' => 'onClick="document.location.href=\'' .  tep_href_link(FILENAME_ALERT_LOG,"log_id=".$arec['id'].'&page='.$_GET['page']) .'\'" class="main"',
        'text'   => $alert_user 
        ); 
    $alert_info[] = array(
        'params' => ' onClick="document.location.href=\'' .  tep_href_link(FILENAME_ALERT_LOG,"log_id=".$arec['id'].'&page='.$_GET['page']) .'\'"class="main"',
        'text'   => $alert_button_name 
        ); 
    $alert_info[] = array(
        'params' => 'onClick="document.location.href=\'' .  tep_href_link(FILENAME_ALERT_LOG,"log_id=".$arec['id'].'&page='.$_GET['page']) .'\'" class="main"',
        'text'   =>  ($alert_orders_id != '' ? $alert_orders_id . '&nbsp;&nbsp;' : '') . ($alert_button_title != '' ? (mb_strlen($alert_button_title,'utf-8') > 30 ? mb_substr($alert_button_title,0,30,'utf-8').'...' : $alert_button_title).'&nbsp;&nbsp;' : '').$alert_button_comment 
        ); 
    $alert_info[] = array(
        'params' => 'onClick="document.location.href=\'' .  tep_href_link(FILENAME_ALERT_LOG,"log_id=".$arec['id'].'&page='.$_GET['page']) .'\'" class="main"',
        'text'   => $arec['created_at']
        ); 
    $alert_info[] = array(
        'params' => 'class="main" align="right"',
        'text'   => tep_image('images/icons/signal_blink_gray.gif')
        ); 
    $alert_table_row[] = array('params' => $alert_params, 'text' => $alert_info);
    $rec_c++;
  }

     $alert_log_form = tep_draw_form('edit_logs',FILENAME_ALERT_LOG,'action=deleteconfirm&page='.$_GET['page']);
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
    echo '<select name="edit_logs_list" onchange="select_logs_change(this.value,\'logs_list_id[]\',\''.$ocertify->npermission.'\');">';
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

