<?php
/*
   $Id$
*/
  require('includes/application_top.php');
  define('MAX_DISPLAY_PW_MANAGER_RESULTS',20);
  $sort_where = '';
  if($ocertify->npermission == 7){
    $sort_where = " and ((privilege_s = '1' and self='') or
     self='".$ocertify->auth_user."' )";
  }else if($ocertify->npermission == 10){
    $sort_where = " and ((privilege_c = '1'  and self='') or
     self='".$ocertify->auth_user."' )";
  }

  if(isset($_GET['site_id'])&&$_GET['site_id']){
    $site_id = tep_db_prepare_input($_GET['site_id']);
  }
  if(isset($_GET['pw_id'])&&$_GET['pw_id']){
    $pwid = tep_db_prepare_input($_GET['pw_id']);
  }
  
  //403
if(isset($pwid)&&$pwid&&!tep_can_edit_pw_manager($pwid,$ocertify->auth_user)){
  header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
  exit;
}

  if (isset($_GET['action']) && $_GET['action']) {

    $user_info = tep_get_user_info($ocertify->auth_user);
    switch ($_GET['action']) {
      case 'redirect':
          tep_redirect(urldecode($_GET['url']));
        break;
      case 'insert':
      case 'update':
          if(tep_db_prepare_input($_POST['nextdate'])!=''&&
              tep_db_prepare_input($_POST['nextdate'])!='0000-00-00'){
            $order_date = tep_db_prepare_input($_POST['nextdate']);
          }else{
            $order_date = '9999-12-30';
          }
          $privilege_str .= 'admin';
          $sql_data_array = array(
            'date_order'=> $order_date,
            'title' => tep_db_prepare_input($_POST['title']),
            'url' => tep_db_prepare_input($_POST['url']),
            'priority' => tep_db_prepare_input($_POST['priority']),
            'loginurl' => tep_db_prepare_input($_POST['loginurl']),
            'username' => tep_db_prepare_input($_POST['username']),
            'password' => tep_db_prepare_input($_POST['password']),
            'comment' => tep_db_prepare_input($_POST['comment']),
            'memo' => tep_db_prepare_input($_POST['memo']),
            'nextdate' => tep_db_prepare_input($_POST['nextdate']),
            'privilege_c' => tep_db_prepare_input($_POST['privilege_c']),
            'privilege_s' => tep_db_prepare_input($_POST['privilege_s']),
            'self' => tep_db_prepare_input($_POST['self']),
            'privilege_a' => '1',
            'updated_at' => 'now()',
            'operator' => $user_info['name'],
            'site_id' => tep_db_prepare_input($_POST['site_id']),
            'onoff' => '1',
            );
        if($_GET['action']=='update'){
          tep_db_perform(TABLE_IDPW, $sql_data_array, 'update', 'id = \'' . $pwid . '\'');
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
          tep_redirect(tep_href_link(FILENAME_PW_MANAGER,
                'pw_id='.$pwid.'&sort='.$_GET['sort'].'&type='.$_GET['type'].'&page=' . $_GET['page']));
        }
        if($_GET['action']=='insert'){
          $insert_sql_data = array(
            'created_at' => 'now()',
            );
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_IDPW, $sql_data_array);
          $insert_sql_data_log = array(
            'idpw_id' => tep_db_insert_id(), 
              );
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data_log);
          tep_db_perform(TABLE_IDPW_LOG, $sql_data_array);
          tep_redirect(tep_href_link(FILENAME_PW_MANAGER,'sort='.$_GET['sort'].'&type='.$_GET['type']));
        }
        break;
      case 'deleteconfirm':
        //unlink();
        tep_db_perform(TABLE_IDPW, array('onoff' => '0'), 'update', 'id = \'' . $pwid . '\'');
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
        tep_redirect(tep_href_link(FILENAME_PW_MANAGER, 'page=' . $_GET['page']));
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
<script language="javascript" src="includes/javascript/datePicker.js"></script>
<script language="javascript" >
$(function() {
  $.datePicker.setDateFormat('ymd','-');
  $('#input_nextdate').datePicker();
});
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
function search_type_changed(elem){
	if ($('#keywords').val() && elem.selectedIndex != 0) 
      document.forms.pw_manager1.submit();
}
function checkurl(url){
  var str = url;
  var objExp = new RegExp(/http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w-.\/?%&=]*)?/);
  if(objExp.test(str)){
    return true;
  }else{
    return false;
  }

}
function valdata(){
  if (document.getElementById('url').value!=''&&
      !checkurl(document.getElementById('url').value)) {
    alert('URL形式を正しく入力してください。例：http://iimy.co.jp'); 
    return false; 
  }
  if (document.getElementById('loginurl').value!=''&&
      !checkurl(document.getElementById('loginurl').value)) {
    alert('URL形式を正しく入力してください。例：http://iimy.co.jp'); 
    return false; 
  }
}
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
</script>
<style type="text/css">
a.date-picker {
    display: block;
    float: none;
}
/*a.date-picker{ left:1000px; height:20px;}
*/.popup-calendar {
top:850px;
right:163px;
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
  /*
  position:relative;
  right:0;
  bottom:0;
  */
  /*padding-left:18px;
  background:url(images/icons/info.gif) no-repeat left center;*/
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
/*float:right;*/
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
.popup-calendar-wrapper{
float:left;
}
</style>
</head>
<body>
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<?php
  if ($ocertify->npermission >= 10) {
    echo '<td width="' . BOX_WIDTH . '" valign="top">';
    echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
    echo '<!-- left_navigation //-->';
    require(DIR_WS_INCLUDES . 'column_left.php');
    echo '<!-- left_navigation_eof //-->';
    echo '</table>';
    echo '</td>';
  } else {
    echo '<td>&nbsp;</td>';
  }
?>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td width="100%" colspan='2'>
  
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
      <td align="center" class="smallText">
        <table width=""  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td class="smallText" valign='top'>
              <?php echo tep_draw_form('pw_manager1', FILENAME_PW_MANAGER, '',
                  'get','id="pw_manager1" onsubmit="return false"'); ?>検索 : 
              <input name="keywords" type="text" id="keywords" size="40" value="<?php if(isset($_GET['keywords'])) echo stripslashes($_GET['keywords']); ?>">
              <select name="search_type" onChange='search_type_changed(this)'>
                <option value="none">--選択してください--</option>
                <option value="title">タイトル</option>
                <option value="priority">重</option>
                <option value="url">URL</option>
                <option value="loginurl">Login</option>
                <option value="username">ID</option>
                <option value="password">PW</option>
                <option value="comment">コメント</option>
                <option value="memo">メモ</option>
                <option value="operator">管理者</option>
                <option value="site_id">サイト名</option>
              </select>
              </form>
            </td>
            <td valign="top"></td>
          </tr>
        </table>
      </td>
      <td align="right">
        <?php
        echo '<button type=\'button\'
        onclick="location.href=\''.tep_href_link(FILENAME_PW_MANAGER,'action=new&sort='.$_GET['sort'].'&type='.$_GET['type']).'\'" >';
        echo TEXT_NEW;
        echo "</button>";
        /*
          echo "<a href='".tep_href_link(FILENAME_PW_MANAGER,'action=new')."'>";
          echo tep_image_button('button_create.gif',IMAGE_CREATE);
          echo "</a>";
          */
          ?>
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
      if (!isset($HTTP_GET_VARS['sort'])) {
        $order_str = '`date_order` asc, `title` asc'; 
      } else {
        if($HTTP_GET_VARS['sort'] = 'nextdate'){
        $order_str = '`date_order` '.$HTTP_GET_VARS['type']; 
        }else{
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
    <tr class="dataTableHeadingRow">
<?php 
?>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'priority') {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=priority&type='.$type_str);?>"><?php echo TEXT_PRIORITY;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=priority&type=asc');?>"><?php echo TEXT_PRIORITY;?></a> 
      <?php
      }
      ?>
      </td>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'loginurl') {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=loginurl&type='.$type_str);?>"><?php echo TEXT_LOGINURL;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=loginurl&type=asc');?>"><?php echo TEXT_LOGINURL;?></a> 
      <?php
      }
      ?>
      </td>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'title') {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=title&type='.$type_str);?>"><?php echo TEXT_TITLE;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=title&type=asc');?>"><?php echo TEXT_TITLE;?></a> 
      <?php
      }
      ?>
      </td>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'username') {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=username&type='.$type_str);?>"><?php echo TEXT_USERNAME;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=username&type=asc');?>"><?php echo TEXT_USERNAME;?></a> 
      <?php
      }
      ?>
      </td>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'password') {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=password&type='.$type_str);?>"><?php echo TEXT_PASSWORD;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=password&type=asc');?>"><?php echo TEXT_PASSWORD;?></a> 
      <?php
      }
      ?>
      </td>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'privilege') {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=privilege&type='.$type_str);?>"><?php echo TEXT_PRIVILEGE;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=privilege&type=asc');?>"><?php echo TEXT_PRIVILEGE;?></a> 
      <?php
      }
      ?>
      </td>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'nextdate') {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=nextdate&type='.$type_str);?>"><?php echo TEXT_NEXTDATE;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link('pw_manager.php', tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=nextdate&type=asc');?>"><?php echo TEXT_NEXTDATE;?></a> 
      <?php
      }
      ?>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
    </tr>
    <?php 
    if(isset($site_id)&&$site_id){
    $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,nextdate,privilege_a,privilege_c,self,privilege_s,operator,created_at,
                             updated_at,onoff from
                             ".TABLE_IDPW." where site_id='".$site_id."'
                             and onoff = '1' 
                             " .$sort_where . "
                             order by ".$order_str;
    }else if(isset($_GET['search_type'])&&$_GET['search_type']&&
        isset($_GET['keywords'])&&$_GET['keywords']){
      $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,nextdate,privilege_a,privilege_c,self,privilege_s,operator,created_at,
                             updated_at,onoff from
                             ".TABLE_IDPW." 
                             where ".$_GET['search_type']." like '%".
                             $_GET['keywords']."%'
                             and onoff = '1' 
                             " .$sort_where . "
                             order by ".$order_str;
    }else{
    $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,nextdate,privilege_a,privilege_c,self,privilege_s,operator,created_at,
                             updated_at,onoff from
                             ".TABLE_IDPW." 
                             where onoff = '1' 
                             " .$sort_where . "
                             order by ".$order_str;
    }
    $pw_manager_split = new splitPageResults($_GET['page'],
        MAX_DISPLAY_PW_MANAGER_RESULTS, $pw_manager_query_raw, $pw_manager_query_numrows);
    //var_dump($pw_manager_query_raw);
       
    $pw_manager_query = tep_db_query($pw_manager_query_raw);
    $i=0;
    while($pw_manager_row = tep_db_fetch_array($pw_manager_query)){
      $i++;
      if (( (!@$_GET['pw_id']) || (@$_GET['pw_id'] == $pw_manager_row['id'])) &&
          (!@$pwInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $pwInfo = new objectInfo($pw_manager_row);
    }
    if (isset($pwInfo) && (is_object($pwInfo)) && ($pw_manager_row['id'] == $pwInfo->id) ) {
      echo '              <tr class="dataTableRowSelected"
        onmouseover="this.style.cursor=\'hand\'" >' . "\n";
      $onclick = ' onclick="document.location.href=\''.
        tep_href_link(FILENAME_PW_MANAGER, 'page=' . $_GET['page'] . '&pw_id=' .
            $pwInfo->id . '&action=edit') . '\'"';
    } else {
      echo '              <tr class="dataTableRow"
        onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'"
        onmouseout="this.className=\'dataTableRow\'">' . "\n";
      $onclick = 'onclick="document.location.href=\'' . tep_href_link(FILENAME_PW_MANAGER,
        'page=' . $_GET['page'] . '&pw_id=' . $pw_manager_row['id']) . '\'"';
    }
      echo "<td class='dataTableContent' ".$onclick." >".$pw_manager_row['priority']."</td>";
      echo "<td class='dataTableContent' >"
        ."<a target='_blank' href='" 
        .make_blank_url($pw_manager_row['loginurl'],FILENAME_REDIREC_URL)."'>"
        .tep_image_button('button_url.gif',IMAGE_CREATE).
        "<a></td>";
      echo "<td class='dataTableContent'>"
        ."<a target='_blank' href='" 
        .make_blank_url($pw_manager_row['url'],FILENAME_REDIREC_URL)."'>"
        .mb_substr($pw_manager_row['title'],0,12,'utf-8')."</a></td>";
      echo "<td class='dataTableContent' id='user_".$i."'
        onclick='copyCode(\"".$pw_manager_row['id']."\",\"username\")'>".mb_substr($pw_manager_row['username'],0,8,'utf-8')."</td>";
      echo "<td class='dataTableContent' id='pwd_".$i."'
        onclick='copyCode(\"".$pw_manager_row['id']."\",\"password\")'>".mb_substr($pw_manager_row['password'],0,8,'utf-8')."</td>";
      /*
      $privilege_arr = array();
      if($pw_manager_row['privilege_s']){
        $privilege_arr[] = 'staff';
      }
      if($pw_manager_row['privilege_c']){
        $privilege_arr[] = 'chief';
      }
      if(count($privilege_arr)>1){
        $privilege_str = implode(',',$privilege_arr);
      }else{
        $privilege_str = $privilege_arr[0];
      }
      echo "<td class='dataTableContent'".$onclick." >".$privilege_str."</td>";
      */
      echo "<td class='dataTableContent'".$onclick."
        >".mb_substr($pw_manager_row['operator'],0,5,'utf-8')."</td>";
      echo "<td class='dataTableContent'".$onclick." >".$pw_manager_row['nextdate']."</td>";
      echo '<td class="dataTableContent" align="right">';
      if ( isset($pwInfo) && (is_object($pwInfo)) && ($pw_manager_row['id'] == $pwInfo->id) ) { 
        echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); 
      } else { 
        echo '<a href="' . tep_href_link(FILENAME_PW_MANAGER, 'page=' .
          $_GET['page'] . '&pw_id=' . $pw_manager_row['id']) . '">' . 
          tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
      }
      echo '&nbsp;</td>';
      echo "</tr>";
    }



    ?>

    <tr>
      <td colspan="8">
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText" valign="top"><?php echo
            $pw_manager_split->display_count($pw_manager_query_numrows,
                MAX_DISPLAY_PW_MANAGER_RESULTS, $_GET['page'],
                TEXT_DISPLAY_NUMBER_OF_PW_MANAGERS); ?></td>
            <td class="smallText" align="right"><?php echo
            $pw_manager_split->display_links($pw_manager_query_numrows,
                MAX_DISPLAY_PW_MANAGER_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],
                tep_get_all_get_params(array('page', 'site_id', 'action','pwid'))); ?></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
      </td>
<?php
  $heading = array();
  $contents = array();
switch (isset($_GET['action'])? $_GET['action']:'') {
  case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW . '</b>');

      $contents = array('form' => tep_draw_form('pw_manager', FILENAME_PW_MANAGER,
            'page=' . $_GET['page'] . '&type='.$_GET['type'].'&sort='.$_GET['sort'].'&action=insert', 'post',
            'enctype="multipart/form-data" onsubmit="return valdata()"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_TITLE . '<br>' .
          tep_draw_input_field('title','','id="title"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_PRIORITY . '<br>' .
          tep_draw_radio_field('priority',1,true).TEXT_PRIORITY_1."".
          tep_draw_radio_field('priority',2,false).TEXT_PRIORITY_2."".
          tep_draw_radio_field('priority',3,false).TEXT_PRIORITY_3
          );
      $contents[] = array('text' => '<br>' . TEXT_INFO_SITE_ID . '<br>' .
          tep_site_pull_down("name='site_id'"));
      $contents[] = array('text' => '<br>' . TEXT_INFO_URL . '<br>' .
          tep_draw_input_field('url','','id="url"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_LOGINURL . '<br>' .
          tep_draw_input_field('loginurl','','id="loginurl"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_USERNAME . '<br>' .
          tep_draw_input_field('username','','id="username"'));
      $pwd_pattern = tep_get_pwd_pattern();
      $pwd_len = tep_get_pwd_len();
      $pwd_pattern_arr = explode(',',$pwd_pattern);
      $contents[] = array('text' => '<br>' . TEXT_INFO_PASSWORD . '<br>' .
          tep_draw_checkbox_field('pattern[]','english',
            in_array('english',$pwd_pattern_arr)?true:false).TEXT_LOWER_ENGLISH.
          tep_draw_checkbox_field('pattern[]','ENGLISH',
            in_array('ENGLISH',$pwd_pattern_arr)?true:false).TEXT_POWER_ENGLISH.
          tep_draw_checkbox_field('pattern[]','NUMBER',
            in_array('NUMBER',$pwd_pattern_arr)?true:false).TEXT_NUMBER."<br>".
          TEXT_PWD_LEN."&nbsp;".tep_draw_input_field('pwd_len',$pwd_len,'id="pwd_len"
            maxlength="2" size="2"')."&nbsp;".
          "<button type='button'
          onclick=\"mk_pwd()\">" .
          TEXT_BUTTON_MK_PWD."</button>".
          tep_draw_input_field('password',tep_get_new_random($pwd_pattern,$pwd_len),'id="password"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_COMMENT . '<br>' .
          tep_draw_textarea_field('comment', 'soft', '30', '5', '','class="pw_textarea"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_MEMO . '<br>' .
          tep_draw_textarea_field('memo', 'soft', '30', '5', '', 'class="pw_textarea"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_NEXTDATE . '<br><div
          class="nextdate_info">' .
          tep_draw_input_field('nextdate','','id="input_nextdate"')."</div>");
      $contents[] = array('text' => '<br>' . TEXT_INFO_PRIVILEGE . '<br>' .
          tep_draw_checkbox_field('self',$ocertify->auth_user,true).TEXT_SELF.
          tep_draw_checkbox_field('privilege_s','1',true)."Staff".
          tep_draw_checkbox_field('privilege_c','1',true)."Chief<br>"
          );
      /*
      $contents[] = array('text' => '<br>' . TEXT_INFO_OPERATOR . '<br>' .
          tep_draw_input_field('operator'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_ONOFF . '<br>' .
          tep_draw_input_field('onoff'));
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT) . '&nbsp;<a href="' . tep_href_link(FILENAME_PW_MANAGER, 'page=' . $_GET['page']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      */
      $contents[] = array('align' => 'center', 'text' => '<br>' . 
          "<button type='submit' >".TEXT_BUTTON_QUERY."</button>"
          . '&nbsp;' .
          "<button type='button'
          onclick=\"location.href='".
          tep_href_link(FILENAME_PW_MANAGER, 'page=' . $_GET['page'])
          ."'\">" .
          TEXT_BUTTON_CLEAR."</button>" 
          );
    break;
  case 'edit':
      
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT . '</b>');
      $contents = array('form' => tep_draw_form('pw_manager', FILENAME_PW_MANAGER,
            'page=' . $_GET['page'] . '&sort='.$_GET['sort'].'&type='.$_GET['type'].'&action=update&pw_id='.$pwInfo->id, 'post',
            'enctype="multipart/form-data" onsubmit="return valdata(this)"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_TITLE . '<br>' .
          tep_draw_input_field('title',$pwInfo->title,'id="title"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_PRIORITY . '<br>' .
          tep_draw_radio_field('priority',1,$pwInfo->priority == '1'?true:false).TEXT_PRIORITY_1."".
          tep_draw_radio_field('priority',2,$pwInfo->priority == '2'?true:false).TEXT_PRIORITY_2."".
          tep_draw_radio_field('priority',3,$pwInfo->priority == '3'?true:false).TEXT_PRIORITY_3
          );
      $contents[] = array('text' => '<br>' . TEXT_INFO_SITE_ID . '<br>' .
          tep_site_pull_down("name='site_id'",$pwInfo->site_id));
      $contents[] = array('text' => '<br>' . TEXT_INFO_URL . '<br>' .
          tep_draw_input_field('url',$pwInfo->url,'id="url"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_LOGINURL . '<br>' .
          tep_draw_input_field('loginurl',$pwInfo->loginurl,'id="loginurl"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_USERNAME . '<br>' .
          tep_draw_input_field('username',$pwInfo->username,'id="username"'));
      $pwd_pattern = tep_get_pwd_pattern();
      $pwd_len = tep_get_pwd_len();
      $pwd_pattern_arr = explode(',',$pwd_pattern);
      $contents[] = array('text' => '<br>' . TEXT_INFO_PASSWORD . '<br>' .
          tep_draw_checkbox_field('pattern[]','english',
            in_array('english',$pwd_pattern_arr)?true:false).TEXT_LOWER_ENGLISH.
          tep_draw_checkbox_field('pattern[]','ENGLISH',
            in_array('ENGLISH',$pwd_pattern_arr)?true:false).TEXT_POWER_ENGLISH.
          tep_draw_checkbox_field('pattern[]','NUMBER',
            in_array('NUMBER',$pwd_pattern_arr)?true:false).TEXT_NUMBER."<br>".
          TEXT_PWD_LEN."&nbsp;".tep_draw_input_field('pwd_len',$pwd_len,'id="pwd_len"
            maxlength="2" size="2"')."&nbsp;".
          "<button type='button'
          onclick=\"mk_pwd()\">" .
          TEXT_BUTTON_MK_PWD."</button>".
          tep_draw_input_field('password',$pwInfo->password,'id="password"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_COMMENT . '<br>' .
          tep_draw_textarea_field('comment', 'soft', '30', '5', $pwInfo->comment, 'class="pw_textarea"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_MEMO . '<br>' .
          tep_draw_textarea_field('memo', 'soft', '30', '5', $pwInfo->memo, 'class="pw_textarea"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_NEXTDATE . '<br><div
          class="nextdate_info">' .
          tep_draw_input_field('nextdate',$pwInfo->nextdate,'id="input_nextdate"')."</div>");
      $contents[] = array('text' => '<br>' . TEXT_INFO_PRIVILEGE . '<br>' .
          tep_draw_checkbox_field('self',$ocertify->auth_user,$pwInfo->self).TEXT_SELF.
          tep_draw_checkbox_field('privilege_s','1',$pwInfo->privilege_s?true:false).
          "Staff".
          tep_draw_checkbox_field('privilege_c','1',$pwInfo->privilege_c?true:false).
          "Chief<br>"
          );
      $contents[] = array('align' => 'center', 'text' => '<br>' . 
          "<button type='submit' >".TEXT_BUTTON_QUERY."</button>"
          . '&nbsp;' .
          "<button type='button'
          onclick=\"location.href='".
          tep_href_link(FILENAME_PW_MANAGER, 'page=' . $_GET['page'])
          ."'\">" .
          TEXT_BUTTON_CLEAR."</button>" 
          );
    break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PW_MANAGER . '</b>');

      $contents = array('form' => tep_draw_form('pw_manager', FILENAME_PW_MANAGER,
            'page=' . $_GET['page'] . '&pw_id=' . $pwInfo->id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $pwInfo->title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' .
          /*
          tep_image_submit('button_delete.gif', IMAGE_DELETE) 
          */ 
          "<button type='submit' >".TEXT_BUTTON_DELETE."</button>"
          . '&nbsp;' .
          "<button type='button'
          onclick=\"location.href='".
          tep_href_link(FILENAME_PW_MANAGER, 'page=' . $_GET['page'] . '&pw_id=' .
            $pwInfo->id)  
          ."'\">" .
          TEXT_BUTTON_CLEAR."</button>" 
          );
      break;
  default:
      $heading[] = array('text' => '');
      $contents[] = array('align' => 'center', 'text' => '<br>' .'' .
          "<button type='button'
          onclick=\"location.href='".tep_href_link(FILENAME_PW_MANAGER,
            'action=edit&pw_id='.$pwInfo->id.'&'.tep_get_all_get_params(array('pw_id','action','search_type','keywords')))."'\">" .
          TEXT_BUTTON_EDIT."</button>"
          . '&nbsp;'.
          "<button type='button'
          onclick=\"location.href='".
          tep_href_link(FILENAME_PW_MANAGER,'action=delete&pw_id='.$pwInfo->id.'&'.tep_get_all_get_params(array('pw_id','action','search_type','keywords')))
          ."'\">" .
          TEXT_BUTTON_DELETE."</button>"
          . '&nbsp;' . 
          "<button type='button'
          onclick=\"location.href='".
          tep_href_link(FILENAME_PW_MANAGER_LOG,
            'pw_id='.$pwInfo->id)
          ."'\">" .
          TEXT_BUTTON_HISTORY."</button>"
          );
      $contents[] = array('text' => '<br>' . TEXT_INFO_COMMENT . '<br>' .
          tep_draw_textarea_field('comment', 'soft', '30', '5', $pwInfo->comment, 'class="pw_textarea"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_MEMO . '<br>' .
          tep_draw_textarea_field('memo', 'soft', '30', '5', $pwInfo->memo, 'class="pw_textarea"'));
      $contents[] = array('align' => '','text' => '<br>' . TEXT_INFO_CREATED .  '&nbsp;&nbsp;&nbsp;' .
          $pwInfo->created_at);
      $contents[] = array('align' => '','text' => '<br>' . TEXT_INFO_UPDATED . '&nbsp;&nbsp;&nbsp;' .
          $pwInfo->updated_at);
      $contents[] = array('align' => '','text' => '<br>' . TEXT_INFO_OPRATER . '&nbsp;&nbsp;&nbsp;' .
          $pwInfo->operator);
    break;
}
  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td class="right_column01" width="20%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }

?>
    </tr>
  </table>
      </td>
    </tr>

    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php
    require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
