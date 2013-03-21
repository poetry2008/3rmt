<?php

/* ===============================================
  global 常量
 ============================================== */
// 表名
  define('TABLE_USERS', 'users');
  define('TABLE_PERMISSIONS', 'permissions');

/* ===============================================
  global 变量
 ============================================== */
  $TableBorder = 'border="0"';        // 表：线的宽度
  $TableCellspacing = 'cellspacing="1"';    // 表：间距
  $TableCellpadding = 'cellpadding="0"';    // 表：内间距
  $TableBgcolor = 'bgcolor="#FFFFFF"';    // 表：背景色

  $ThBgcolor = 'bgcolor="Gainsboro"';     // 头部：背景色
  $TdnBgcolor = 'bgcolor="WhiteSmoke"';   // 表格：项目名背景色



/* -----------------------------------------------------
   功能: 判断字符串是否为空 
   参数: $s_val(string) 字符串 
   返回值: 为空的错误信息(string) 
 -----------------------------------------------------*/
function checkNotnull($s_val) {

  // 输入值的时候进行检查
  if ($s_val == "") {
    return TEXT_ERRINFO_INPUT_NOINPUT;
  }
  return '';        // 返回值
}

/* -----------------------------------------------------
   功能: 判断字符串是否为空并且是否符合指定规则 
   参数: $s_val(string) 字符串 
   参数: $s_ereg(string) 规则 
   返回值: 错误信息(string) 
 -----------------------------------------------------*/
function checkStringEreg($s_val, $s_ereg = "") {

  // 输入完成的时候，处理结束
  if ($s_val == "") return '';

  // 判断错误
  if ($s_ereg && (ereg($s_ereg,$s_val) == false)) {
    return TEXT_ERRINFO_INPUT_ERR;
  }

  return '';            // 返回值
}

/* -----------------------------------------------------
   功能: 判断字符串是否超出指定长度 
   参数: $s_val(string) 字符串 
   参数: $n_len(int) 长度 
   返回值: 错误信息(string) 
 -----------------------------------------------------*/
function checkLength_ge($s_val, $n_len) {

  // 输入完成的时候，处理结束
  if ($s_val == "") return '';

  // 判断错误
  $n_val_len = strlen($s_val);
  if ($n_len > 0 && $n_len > $n_val_len) {
    return sprintf(TEXT_ERRINFO_INPUT_LENGTH, $n_len);
  }

  return '';            // 返回值
}

/* -----------------------------------------------------
   功能: 输出错误信息 
   参数: $a_error(string) 错误信息 
   返回值: 错误信息的输出(string) 
 -----------------------------------------------------*/
function print_err_message($a_error) {

  $stable_bgcolor = 'bgcolor="#FFFFFF"';    // 表的背景色
  $sfont_color = 'color="#FF0000"';     // 字体颜色

  echo '<font class="main" ' . $sfont_color . '">';
  echo TABLE_HEADING_ERRINFO;   // 错误信息显示标题
  echo "<br>\n";

  //-- 错误显示 --
  for ($i = 0 ; $i < count($a_error) ; $i++) {
    echo $a_error[$i];
    echo "<br>\n";
  }

  echo "</font>\n";

}

/* -----------------------------------------------------
   功能: 赋值错误数组信息 
   参数: $a_error(array) 错误数组 
   参数: $s_errmsg(string) 错误信息 
   返回值: 错误数组(array) 
 -----------------------------------------------------*/
function set_errmsg_array(&$a_error,$s_errmsg) {

  $a_error[] = $s_errmsg;
}

/* -----------------------------------------------------
   功能: 按照用户id顺序获取用户 
   参数: $s_user_ID(int) 用户id 
   返回值: 获取用户的sql(string) 
 -----------------------------------------------------*/
function makeSelectUserInfo($s_user_ID = "") {

  $s_select = "select * from " . TABLE_USERS;
  $s_select .= ($s_user_ID == "" ? "" : " where userid = '$s_user_ID'");
  $s_select .= " order by userid;";     // 按照先后顺序获取用户id
  return $s_select;

}

/* -----------------------------------------------------
   功能: 获取指定权限的用户 
   参数: $nmode(int) 权限id 
   返回值: 获取指定权限的用户的sql(string) 
 -----------------------------------------------------*/
function makeSelectUserParmission($nmode=0) {

  // 获取用话权限信息
  $s_select = "select u.userid as userid, u.name as name";
  $s_select .= " from " . TABLE_USERS . " u, " . TABLE_PERMISSIONS . " p";
  $s_select .= " where u.userid = p.userid";
  if ($nmode == 0) $s_select .= " and p.permission < 15";   // 根据生成模式编辑 where 语句
  else $s_select .= " and p.permission = '".$nmode."'";
  $s_select .= " order by u.userid";              // 按照顺序获取用户id数据

  return $s_select;

}

/* -----------------------------------------------------
   功能: 新建用户/权限数据 
   参数: $aval(array) 新建信息数组 
   参数: $nmode(int) 新建哪个表的数据 
   返回值: 新建用户/权限数据的sql(string) 
 -----------------------------------------------------*/
function makeInsertUser($aval, $nmode=0) {

  $ssql = "insert into ";
  if ($nmode == 0) {
    // 用DES加密
    $cryot_password = (string) crypt($aval['password']);
    // 往用户管理表里添加数据用sql 生成字符串
    $ssql .= TABLE_USERS . " values (";
    $ssql .= "'" . $aval['userid'] . "'";
    $ssql .= ",'$cryot_password'";
    $ssql .= ",'" . $aval['name'] . "'";
    $ssql .= ",'" . $aval['email'] . "'";
    $ssql .= ",'" . $aval['rule'] . "'";
    $ssql .= ")";
  } else {
    // 往用户权限管理表里添加数据用sql 生成字符串
    $ssql .= TABLE_PERMISSIONS . " values (";
    $ssql .= "'" . $aval['userid'] . "'";
    $ssql .= ",7";
    $ssql.=",''";
    $ssql .= ")";
  }
  return $ssql;
}

/* -----------------------------------------------------
   功能: 更新用户数据 
   参数: $aval(array) 更新信息数组 
   参数: $nmode(int) 更新哪些信息 
   返回值: 更新用户数据的sql(string) 
 -----------------------------------------------------*/
function makeUpdateUser($aval, $nmode=0) {
  $ssql = "update " . TABLE_USERS . " set";
  if ($nmode == 0) {
    $ssql .= " name='" . $aval['name'] . "'";
    $ssql .= ", email='" . $aval['email'] . "'";
  } else {
    // 用DES加密
    $cryot_password = (string) crypt($aval['password']);
    $ssql .= " password='$cryot_password'";
  }
  $ssql .= ",user_update = '".$_SESSION['user_name']."',date_update = '".date('Y-m-d H:i:s',time())."'  where userid='" . $GLOBALS['userid'] . "'";

  return $ssql;
}

/* -----------------------------------------------------
   功能: 删除用户/权限 
   参数: $nmode(int) 删除哪个表 
   返回值: 删除用户/权限的sql(string) 
 -----------------------------------------------------*/
function makeDeleteUser($nmode=0) {

  $ssql = "delete from ";
  if ($nmode == 0) {
    // 用DES加密
    $cryot_password = (string) crypt(isset($aval['password'])?$aval['password']:'');
    // 往用户管理表里添加数据用sql 生成字符串
    $ssql .= TABLE_USERS;
  } else {
    // 往用户权限管理表里添加数据用sql 生成字符串
    $ssql .= TABLE_PERMISSIONS;
  }
  $ssql .= " where userid='" . $GLOBALS['userid'] . "'";

  return $ssql;
}

/* -----------------------------------------------------
   功能: 用户密码一览 
   参数: 无 
   返回值: 用户密码一览的html(string) 
 -----------------------------------------------------*/
function UserPassword_preview() {

  PageBody('t', PAGE_TITLE_PASSWORD);   // 用户后台的标题显示（修改密码）

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));              // <form>标签的输出

  $ssql = makeSelectUserInfo($GLOBALS['userslist']);      // 获取用户信息
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      
    // 错误的时候
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // 显示信息
    echo "<br>\n";
    echo tep_draw_form('users', FILENAME_USERS);            // <form>标签的输出
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // 获取记录件数
  if ($nrow != 1) {                     
    // 获取的记录件数不是一件的时候
    echo TEXT_ERRINFO_DB_NO_USER;             // 显示信息
    echo "<br>\n";
    echo tep_draw_form('users', FILENAME_USERS);            // <form>标签的输出
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // 返回到用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;                     // 退出处理
  }

  $arec = tep_db_fetch_array($oresult);
  if ($oresult) @tep_db_free_result($oresult);    // 开放结果项目

  // 表标签的开始
  echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
  echo "<tr>\n";
  // 用户名（用户ID）
  echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . ' colspan="2" nowrap>' .
    $arec['name'] . "（" . $GLOBALS['userslist'] . '）</td>' . "\n";
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_NEW_PASSWORD . '</td>';    // 新密码
  // 输入项目的输出
  echo '<td>';
  echo tep_draw_password_field("aval[password]", '', TRUE," id='aval_password'");
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_CONFIRM_PASSWORD . '</td>';  // 为了确认请再次输入
  // 输入项目的输出
  echo '<td>';
  echo tep_draw_password_field("aval[chk_password]", '', TRUE," id='aval_chk_password'");
  echo '</td>';
  echo "</tr>\n";
  $users = tep_db_fetch_array(tep_db_query("select * from ".TABLE_USERS." where userid  ='".$GLOBALS['userslist']."'"));
  if(tep_not_null($users['user_added'])){
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_USER_ADDED . '</td>';  // 为了确认请再次输入
  // 输入项目的输出
  echo '<td>';
  echo $users['user_added'];
  echo '</td>';
  echo "</tr>\n";
  }else{
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_USER_ADDED . '</td>';  // 为了确认请再次输入
  // 输入项目的输出
  echo '<td>';
  echo TEXT_UNSET_DATA;
  echo '</td>';
  echo "</tr>\n";
  }if(tep_not_null(tep_datetime_short($users['date_added']))){
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_DATE_ADDED . '</td>';  // 为了确认请再次输入
  // 输入项目的输出
  echo '<td>';
  echo $users['date_added'];
  echo '</td>';
  echo "</tr>\n";
  }else{
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_DATE_ADDED . '</td>';  // 为了确认请再次输入
  // 输入项目的输出
  echo '<td>';
  echo TEXT_UNSET_DATA;
  echo '</td>';
  echo "</tr>\n";
  }if(tep_not_null($users['user_update'])){
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_USER_UPDATE . '</td>';  // 为了确认请再次输入
  // 输入项目的输出
  echo '<td>';
  echo $users['user_update'];
  echo '</td>';
  echo "</tr>\n";
  }else{
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_USER_UPDATE . '</td>';  // 为了确认请再次输入
  // 输入项目的输出
  echo '<td>';
  echo TEXT_UNSET_DATA;
  echo '</td>';
  echo "</tr>\n";
  }if(tep_not_null($users['date_update'])){
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_DATE_UPDATE . '</td>';  // 为了确认请再次输入
  // 输入项目的输出
  echo '<td>';
  echo $users['date_update'];
  echo '</td>';
  echo "</tr>\n";
  }else{
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_DATE_UPDATE . '</td>';  // 为了确认请再次输入
  // 输入项目的输出
  echo '<td>';
  echo TEXT_UNSET_DATA;
  echo '</td>';
  echo "</tr>\n";
  }
  echo "</table>\n";

  echo '<br>';

  echo tep_draw_hidden_field("execute_password");         // 把处理模式放在隐藏项目里
  echo tep_draw_hidden_field("userid", $GLOBALS['userslist']);    //把用户id放在隐藏项目里
  echo tep_draw_hidden_field("userslist", $GLOBALS['userslist']);    // 把用户id放在隐藏项目里

  // 显示按钮
  echo tep_draw_input_field("execute_update", BUTTON_CHANGE, "onClick=\"return formConfirm('password')\"", FALSE, "submit", FALSE); // 变更
  echo tep_draw_input_field("clear", BUTTON_CLEAR, '', FALSE, "reset", FALSE);  // 清除
  echo "\n";

  echo "</form>\n";                 // form的footer


  return TRUE;
}

/* -----------------------------------------------------
   功能: 更新成功页面 
   参数: 无 
   返回值: 更新成功的html(string) 
 -----------------------------------------------------*/
function UserInfor_execute() {

  PageBody('t', PAGE_TITLE_USERINFO);   // 用户管理换面的标题显示（用户信息）

  // 名字的输入检查
  $ret_err = checkNotnull($GLOBALS['aval']['name']);
  if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NAME . '</b>:' . $ret_err);   // 名字

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>标签的输出

  if (isset($aerror) && is_array($aerror)) {      // 输入错误的时候
    print_err_message($aerror);   // 显示错误信息
    echo "<br>\n";
    echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);            // 把用户id方才隐藏项目里
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";       // form的footer
    return FALSE;
  }

  $ssql = makeUpdateUser($GLOBALS['aval']);         // 更新用户管理表的名字和邮件  获取sql字符串
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      
    // 错误的时候
    echo TEXT_ERRINFO_DB_UPDATE_USER;           // 显示信息
    echo "<br>\n";
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }
  
  tep_db_query("delete from user_ip where userid = '".$GLOBALS['userid']."'");
  if (!empty($GLOBALS['ip_limit'])) {
    $ip_limit_arr = explode("\n", $GLOBALS['ip_limit']); 
    foreach ($ip_limit_arr as $ip_key => $ip_value) {
      $split_ip = explode('.', $ip_value);
      $split_error = false; 
      if (count($split_ip) != 4) {
        continue; 
      }
      if ((is_numeric(trim($split_ip[0])) || trim($split_ip[0]) == '*') && (is_numeric(trim($split_ip[1])) || trim($split_ip[1]) == '*') && (is_numeric(trim($split_ip[2])) || trim($split_ip[2]) == '*') && (is_numeric(trim($split_ip[3])) || trim($split_ip[3]) == '*')) {
      } else {
        $split_error = true; 
      }
      if ($split_error) {
        continue; 
      }
      $ip_insert_sql = "insert user_ip values('".$GLOBALS['userid']."', '".$ip_value."')"; 
      tep_db_query($ip_insert_sql);
    }
  }
  echo "<br>\n";
  echo TEXT_SUCCESSINFO_UPDATE_USER;    // 完成信息
  echo "<br><br>\n";
  echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // 返回用户管理菜单
  echo "</form>\n";           // form的footer

  if ($oresult) @tep_db_free_result($oresult);    // 开放结果项目
  if(isset($GLOBALS['letter'])&&$GLOBALS['letter']!=''
      &&isset($GLOBALS['rule'])&&$GLOBALS['rule']!=''){
    update_rules($GLOBALS['userid'],$GLOBALS['rule'],$GLOBALS['letter']);
  }

  return TRUE;
}

/* -----------------------------------------------------
   功能: 更新密码成功页面 
   参数: 无 
   返回值: 更新密码成功的html(string) 
 -----------------------------------------------------*/
function UserPassword_execute() {

  PageBody('t', PAGE_TITLE_PASSWORD);   // 用户管理画面的标题显示（修改密码）
  // 新密码的输入检查
  $ret_err = checkNotnull($GLOBALS['aval']['password']);
  if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NEW_PASSWORD . '</b>:' . $ret_err);
  $ret_err = checkLength_ge($GLOBALS['aval']['password'], 2);
  if ($ret_err == "") $ret_err = checkStringEreg($GLOBALS['aval']['password'], "[[:print:]]");
  if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NEW_PASSWORD . '</b>:' . $ret_err);
  // 为了确认请再次输入的检查
  if (strcmp($GLOBALS['aval']['password'],$GLOBALS['aval']['chk_password']) != 0)
    set_errmsg_array($aerror, TEXT_ERRINFO_CONFIRM_PASSWORD);

  echo tep_draw_form('users',FILENAME_CHANGEPWD);      // <form>标签的输出

  if (isset($aerror) && is_array($aerror)) {      
    // 输入错误的时候
    print_err_message($aerror);   // 显示错误信息
    echo "<br>\n";
    echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);    // 把用户id方才隐藏项目里
    echo tep_draw_hidden_field('execute_password', $GLOBALS['execute_password']);
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";       // form的footer
    return FALSE;
  }

  $ssql = makeUpdateUser($GLOBALS['aval'], 1);    // 更新用户管理表的密码  获取sql字符串
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                  
    // 错误的时候
    echo TEXT_ERRINFO_DB_CHANGE_PASSWORD;     // 显示信息
    echo "<br>\n";
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";               // form的footer
    if ($oresult) @tep_db_free_result($oresult);  // 开放结果项目
    return FALSE;
  }

  echo "<br>\n";
  echo "&nbsp;&nbsp;&nbsp;&nbsp;";
  echo "<font size='4' color='red'>";
  echo TEXT_SUCCESSINFO_CHANGE_PASSWORD;    // 完成信息
  echo "</font>";
  echo "<br><br>\n";
  echo "</form>\n";           // form的footer

  if ($oresult) @tep_db_free_result($oresult);    // 开放结果项目

  return TRUE;

}

/* -----------------------------------------------------
   功能: 生成js代码 
   参数: 无 
   返回值: 无 
 -----------------------------------------------------*/
function putJavaScript_ConfirmMsg() {

echo '
<script language="JavaScript1.1">
<!--
function formConfirm(type) {
  switch (type) {
    case "update":
      rtn = confirm("'. JAVA_SCRIPT_INFO_CHANGE . '");
      break;
    case "delete":
      rtn = confirm("'. JAVA_SCRIPT_INFO_DELETE . '");
      break;
    case "password":
      if($("#aval_password").val()== $("#aval_chk_password").val()){
      rtn = confirm("'. JAVA_SCRIPT_INFO_PASSWORD . '");
      }else{
        alert("'.JAVA_SCRIPT_ERRINFO_CONFIRM_PASSWORD.'");
        rtn = false;
      }
      break;
    case "staff2chief":
      rtn = confirm("'. JAVA_SCRIPT_INFO_STAFF2CHIEF . '");
      break;
    case "chief2staff":
      rtn = confirm("'. JAVA_SCRIPT_INFO_CHIEF2STAFF . '");
      break;
    case "chief2admin":
      rtn = confirm("'. JAVA_SCRIPT_INFO_CHIEF2ADMIN . '");
      break;
    case "admin2chief":
      rtn = confirm("'. JAVA_SCRIPT_INFO_ADMIN2CHIEF . '");
      break;
    case "grant":
      rtn = confirm("'. JAVA_SCRIPT_INFO_REVOKE . '");
      break;
    case "revoke":
      rtn = confirm("'. JAVA_SCRIPT_INFO_REVOKE . '");
      break;
  }
  if (rtn) return true;
  else return false;
}
//-->
</script>
';

}

/* -----------------------------------------------------
   功能: 页面的头部 
   参数: 无 
   返回值: 无 
 -----------------------------------------------------*/
function PageHeader() {
  global $ocertify,$page_name,$notes;
  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n";
  echo '<html ' . HTML_PARAMS . '>' . "\n";
  echo '<head>' . "\n";
  echo '<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET . '">' . "\n";
  echo '<title>'.HEADING_TITLE.'  </title>' . "\n";
  echo '<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">' . "\n";

  // 修改用户信息，修改密码，管理者权限的时候，用JavaScript输出确认信息
  if ((isset($GLOBALS['execute_user']) && $GLOBALS['execute_user']) || (isset($GLOBALS['execute_password']) && $GLOBALS['execute_password']) || (isset($GLOBALS['execute_permission']) && $GLOBALS['execute_permission']) ) {
    putJavaScript_ConfirmMsg();           // 显示确认信息 JavaScript
  }

  echo '<script language="javascript" src="includes/javascript/jquery_include.js"></script>'."\n";
  echo '<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>';
  $belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
  require("includes/note_js.php");
  echo '</head>' . "\n";
  echo '<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">' . "\n";
  if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){
  echo "<script language='javascript'>
    one_time_pwd('".$page_name."');
      </script>";
  }

  echo '<!-- header //-->' . "\n";
  require(DIR_WS_INCLUDES . 'header.php');
  echo '<!-- header_eof //-->' . "\n";
}

/* -----------------------------------------------------
   功能: 页面内容的表格 
   参数: $mode(string) 表格的开始/结束 
   返回值: 无 
 -----------------------------------------------------*/
function PageBodyTable($mode='t') {
  global $ocertify;
  switch ($mode) {
  case 't':
    echo '<!-- body //-->' . "\n";
    echo '<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">' . "\n";
    echo '  <tr>' . "\n";
    if($GLOBALS['ocertify']->npermission >= 10){
    echo '    <td width="' . BOX_WIDTH . '" valign="top"><table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">' . "\n";
    }
    break;
  case 'u':
    echo '  </tr>' . "\n";
    echo '</table>' . "\n";
    echo '<!-- body_eof //-->' . "\n";
    break;
  } 
}

/* -----------------------------------------------------
   功能: 页面的内容 
   参数: $mode(string) 开始/结束 
   参数: $stitle(string) 标题 
   返回值: 无 
 -----------------------------------------------------*/
function PageBody($mode='t', $stitle = "") {
  global $notes;
  switch ($mode) {
  case 't':
    echo '<!-- body_text //-->' . "\n";
    echo '    <td width="100%" valign="top"><div class="box_warp">'.$notes.'<div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n";
    echo '      <tr>' . "\n";
    echo '        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">' . "\n";
    echo '          <tr>' . "\n";
    echo '            <td class="pageHeading">' . HEADING_TITLE . ' (' . $stitle . ')</td>' . "\n";
    echo '            <td class="pageHeading" align="right">';
    echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT);
    echo '</td>' . "\n";
    echo '          </tr>' . "\n";
    echo '        </table></td>' . "\n";
    echo '      </tr>' . "\n";
    echo '      <tr>' . "\n";
    echo '        <td>' . "\n";
    break;
  case 'u':
    echo '        </td>' . "\n";
    echo '      </tr>' . "\n";
    echo '    </table></div></div></td>' . "\n";
    echo '<!-- body_text_eof //-->' . "\n";
    break;
  } 
}

/* -----------------------------------------------------
   功能: 页面的尾部 
   参数: 无 
   返回值: 无 
 -----------------------------------------------------*/
function PageFooter() {
  echo "<!-- footer //-->\n";
  require(DIR_WS_INCLUDES . 'footer.php');
  echo "\n<!-- footer_eof //-->\n";
  echo "<br>\n";
  echo "</body>\n";
  echo "</html>\n";
}

//获取当前用户当天 登录次数
//修改规则 并插入 数据库



  require('includes/application_top.php');
  if (isset($_POST['userid'])) { $userid = $_POST['userid']; }
  if (isset($_POST['aval'])) { $aval = $_POST['aval']; }
  if (isset($_POST['userslist'])) { $userslist = $_POST['userslist']; }
  if (isset($_POST['execute_user'])) { $execute_user = $_POST['execute_user']; }
  if (isset($_POST['execute_password'])) { $execute_password = $_POST['execute_password']; }
//修改权限
if (isset($_POST['execute_change'])) { $execute_change = $_POST['execute_change'];}

  PageHeader();       // 显示页面的头部
  PageBodyTable('t');     // 页面的布局表：开始（启动包括导航的表）

  // 显示左侧导航
  if($ocertify->npermission >= 10){
  echo "<!-- left_navigation //-->\n";     
  include_once(DIR_WS_INCLUDES . 'column_left.php');
  echo "\n<!-- left_navigation_eof //-->\n";
  echo "    </table></td>\n";
  }
  $change_pwd_flag = false;
  if((isset($userslist)&&$userslist)||
      (isset($userid)&&$userid)){
    if($ocertify->npermission == 15){
      $change_pwd_flag = true;
    }else if($ocertify->auth_user == $userslist){
      $change_pwd_flag = true;
    }
  }

  
// 显示画面，输入检查，反应数据库
  if ($ocertify->auth_user&&$change_pwd_flag) {
        // 修改密码
    if (isset($execute_password) && $execute_password) {
      if (isset($execute_update) && $execute_update){
        UserPassword_execute();  // 执行密码变更处理
      }else{
        UserPassword_preview();          // 显示密码变更页面
      }

    // 管理者权限
    }   
  }

  PageBody('u');        // 页面终了
  PageBodyTable('u');     // 页面布局表：终了
  PageFooter();       // 页脚的显示

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
