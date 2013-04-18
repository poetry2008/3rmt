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
/* ===============================================
  输入检查函数
 ============================================== */

/*--------------------------------------
  功能: 未输入检查
  参数: $s_val(string) 值
  返回值: 错误信息(string)
 --------------------------------------*/
function checkNotnull($s_val) {

  // 输入值的时候进行检查
  if ($s_val == "") {
    return TEXT_ERRINFO_INPUT_NOINPUT;
  }
  return '';        // 返回值
}

/*--------------------------------------
  功能: 字符串项目检查（正规体现） 正则表达式模式和输入检查（全角半角混合）
  参数: $s_val(string) 字符串
  参数: $s_ereg(string) 正则表达式模式（省略的时候:不进行正则表达式检查）
  返回值: 错误信息(string)
 -------------------------------------*/
function checkStringEreg($s_val, $s_ereg = "") {

  // 未输入值的时候的处理
  if ($s_val == "") return '';

  if ($s_ereg && (ereg($s_ereg,$s_val) == false)) {
    // 错误判断
    return TEXT_ERRINFO_INPUT_ERR;
  }

  return '';            // 返回值
}

/*--------------------------------------
  功能: 字符串检查
  参数: $s_val(string) 字符串
  参数: $n_len(int)字节数（省略的时候：空文字）
  返回值: 错误信息(string)
 -------------------------------------*/
function checkLength_ge($s_val, $n_len) {

  // 未输入值的时候的处理
  if ($s_val == "") return '';

  // 错误判断
  $n_val_len = strlen($s_val);
  if ($n_len > 0 && $n_len > $n_val_len) {
    return sprintf(TEXT_ERRINFO_INPUT_LENGTH, $n_len);
  }

  return '';            // 返回值
}

/*--------------------------------------
  功能: 错误信息显示
  参数: $a_error(array) 错误信息
  返回值: 无
 --------------------------------------*/
function print_err_message($a_error) {

  $stable_bgcolor = 'bgcolor="#FFFFFF"';    // 表背景色
  $sfont_color = 'color="#FF0000"';     // 字体颜色（颜色）

  echo '<font class="main" ' . $sfont_color . '">';
  echo TABLE_HEADING_ERRINFO;   // 错误信息显示标题
  echo "<br>\n";

  //-- 显示错误 --
  for ($i = 0 ; $i < count($a_error) ; $i++) {
    echo $a_error[$i];
    echo "<br>\n";
  }

  echo "</font>\n";

}

/* -------------------------------------
  功能: 把错误信息放在错误信息数组里
  参数: $a_error(array) 错误信息数组
  参数: $s_errmsg(string) 错误信息
  返回值: 无
 ------------------------------------ */
function set_errmsg_array(&$a_error,$s_errmsg) {

  $a_error[] = $s_errmsg;
}

/* ===============================================
  获取记录 sql 字符串生成函数（Select）
 ============================================== */
/*--------------------------------------
  功能: 获取用户信息 sql 字符串生成
  参数: $s_user_ID(int) 用户id（可以省略）
  返回值: select语句字符串(string)
 --------------------------------------*/
function makeSelectUserInfo($s_user_ID = "") {

  $s_select = "select * from " . TABLE_USERS;
  $s_select .= ($s_user_ID == "" ? "" : " where userid = '$s_user_ID'");
  $s_select .= " order by userid;";     // 按照用户id的顺序获取数据
  return $s_select;

}

/*--------------------------------------
  功能: 获取包含用户权限的信息 sql 字符串生成
  参数: $nmode(int) 整数：生成模式（0:获取一般用户[默认值]、1:获取管理员）
  返回值: select语句字符串(string)
 --------------------------------------*/
function makeSelectUserParmission($nmode=0) {

  // 获取包含用户权限的信息
  $s_select = "select u.userid as userid, u.name as name";
  $s_select .= " from " . TABLE_USERS . " u, " . TABLE_PERMISSIONS . " p";
  $s_select .= " where u.userid = p.userid";
  if ($nmode == 0) $s_select .= " and p.permission < 15";   // 根据生成模式编辑 where 语句
  else $s_select .= " and p.permission = '".$nmode."'";
  $s_select .= " order by u.userid";              // 按照用户id的顺序获取数据

  return $s_select;

}

/* ==============================================
  表更新 sql 字符串生成函数（Insert、Update、Delete）
 ============================================= */
/*--------------------------------------
  功能: 新用户的注册（用户管理、用户权限表添加注册）
  参数: $aval(array) 添加数据
  参数: $nmode(int) 生成模式（0:用户管理表添加sql[默认值]、1:用户权限表添加sql）
  返回值: sql语句(string) 
 --------------------------------------*/
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
    $ssql .= ",'" . $_SESSION['user_name']."'";
    $ssql .= ",'" . date('Y-m-d H:i:s',time())."'";
    $ssql .= ",'" . $_SESSION['user_name']."'";
    $ssql .= ",'" . date('Y-m-d H:i:s',time())."'";
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

/*--------------------------------------
  功能: 用户信息表的更新
  参数: $aval(array) 更新数据
  参数: $nmode(int) 更新模式（0:姓名、e-mail、1:密码）
  返回值: sql语句(string)
 --------------------------------------*/
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
  $ssql .= " where userid='" . $GLOBALS['userid'] . "'";

  return $ssql;
}

/*--------------------------------------
  功能: 删除用户、（用户管理、从用户权限表里删除）
  参数:  $nmode(int) 生成模式（0:用户管理表删除sql[默认值]、1:用户权限表删除sql）
  返回值: sql语句(string)
 --------------------------------------*/
function makeDeleteUser($nmode=0) {

  $ssql = "delete from ";
  if ($nmode == 0) {
    // 用DES加密
    $cryot_password = (string) crypt(isset($aval['password'])?$aval['password']:'');
    // 用户管理表添加 sql 字符串生成
    $ssql .= TABLE_USERS;
  } else {
    // 用户权限表添加 sql 字符串生成
    $ssql .= TABLE_PERMISSIONS;
  }
  $ssql .= " where userid='" . $GLOBALS['userid'] . "'";

  return $ssql;
}

/*--------------------------------------
  功能: 用户权限表更新
  参数: $nmode(int) 更新模式（0:grant、1:revoke）
  参数: $susers(int) 用户ID
  返回值: sql语句(string)
 --------------------------------------*/
function makeUpdatePermission($nmode=0, $susers) {

  $ssql = "update " . TABLE_PERMISSIONS . " set";
  switch($nmode){
    case 'staff2chief':
       $ssql .= " permission=10";
      break;
    case 'chief2staff':
       $ssql .= " permission=7";
      break;
    case 'chief2admin':
       $ssql .= " permission=15";
      break;
    case 'admin2chief':
       $ssql .= " permission=10";
      break;
  }
  $ssql .= " where userid='$susers'";

  return $ssql;

}

/* ==============================================
  画面显示函数（主要）
 ============================================= */
/*--------------------------------------
  功能: 访问日志信息列表显示
  参数: 无
  返回值: 成功显示(boolean)
 --------------------------------------*/
function UserManu_preview() {

  global $ocertify;           // 用户认证项目

  PageBody('t', PAGE_TITLE_MENU_USER);      // 用户管理画面的标题显示（用户管理菜单）

  // 获取用户信息
  if ($ocertify->npermission < 15) $ssql = makeSelectUserInfo($ocertify->auth_user);    // 一般用户
  if ($ocertify->npermission == 15) $ssql = makeSelectUserInfo();   // 管理员

  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      
    //错误的时候
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // 显示错误信息
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));            // <form>标签的输出
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);                  // 获取记录件数
  // 启动表标签
  echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
  echo "<tr>\n";
  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']),'','post',' id=\'useraction_form\'');    // <form>标签的输出

  if ($nrow == 1) {                         
    // 对象数据是1件的时候
    // 项目标题输出（1单元格）
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' . TABLE_HEADING_USER .  '</b></td>' . "\n";   // 用户
    $nLsize = 'size="1"';                     // 列表的尺寸变量设为1
  } elseif ($nrow > 1) {                        
    // 对象数据超过1件的时候
    // 项目标题输出（1单元格）
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' .  TABLE_HEADING_USER_LIST . '</b></td>' . "\n";  // 用户列表
    $nLsize = 'size="5"';                     // 列表的尺寸变量设为5
  }
  echo "</tr>\n";

  // 把显示在列表框里的数据放在数组里
  $i=0;
  while ($arec = tep_db_fetch_array($oresult)) {      // 获取记录
    $ausers[$i]['id'] = $arec['userid'];
    $ausers[$i]['text'] = $arec['name'];
    $i++;
  }
  echo '<tr><td>';                          // 单元格数据
  echo tep_draw_pull_down_menu("userslist", $ausers, $ocertify->auth_user, $nLsize);  // 列表框的显示
  echo "</td></tr>\n";
  echo "</table>\n";
  echo '<br>';
  echo tep_draw_hidden_field("execute_password",BUTTON_CHANGE_PASSWORD);

  // 显示按钮
  if ($ocertify->npermission == 15) {     
    // 管理员
    echo tep_draw_input_field("execute_new", BUTTON_INSERT_USER, '', FALSE, "submit", FALSE); // 用户添加
    echo tep_draw_input_field("execute_user", BUTTON_INFO_USER, '', FALSE, "submit", FALSE);  // 用户信息
    echo tep_draw_input_field("execute_password_button", BUTTON_CHANGE_PASSWORD,
        'onclick="goto_changepwd(\'useraction_form\')"', FALSE,
        "button", FALSE);  // 修改密码
    echo tep_draw_input_field("execute_permission", BUTTON_PERMISSION, '', FALSE, "submit", FALSE); // 管理员权限
 echo tep_draw_input_field("execute_change",BUTTON_CHANGE_PERMISSION , '', FALSE, "submit", FALSE);
    echo "\n";
  } else {
    echo tep_draw_input_field("execute_password_button", BUTTON_CHANGE_PASSWORD, 
        'onclick="goto_changepwd(\'useraction_form\')"', FALSE,
        "button", FALSE);  // 修改密码
  }

  echo "</form>\n";           // form的footer

  if ($oresult) @tep_db_free_result($oresult);          // 开放结果项目

  return TRUE;
}

/*--------------------------------------
  功能: 用户添加（主要显示）
  参数: 无
  返回值: 显示成功(boolean)
 --------------------------------------*/
function UserInsert_preview() {

  PageBody('t', BUTTON_INSERT_USER);    // 用户管理画面的标题显示（添加用户）

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));              // <form>标签的输出

  // 启动表标签
  echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor']. '>' . "\n";
  echo "<tr>\n";
  // 项目标题输出（1单元格）
  echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' . TABLE_HEADING_COLUMN .  '</b></td>' . "\n"; // 列
  echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' . TABLE_HEADING_DATA .  '</b></td>' . "\n"; // 数据
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_USER_ID . '</td>';   // 用户ID
  // 输入项目输出
  echo '<td>';
  echo tep_draw_input_field("aval[userid]", '', 'size="18" maxlength="16"', TRUE, 'text', FALSE);
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_PASSWORD . '</td>';    // 密码
  // 输入项目输出
  echo '<td>';
  echo tep_draw_password_field("aval[password]", '', TRUE);
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_NAME . '</td>';    // 姓名
  // 输入项目输出
  echo '<td>';
  echo tep_draw_input_field("aval[name]", '', 'size="32" maxlength="64"', TRUE, 'text', FALSE);
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_EMAIL . '</td>';   // E-Mail
  // 输入项目输出
  echo '<td>';
  echo tep_draw_input_field("aval[email]", '', 'size="32" maxlength="96"', FALSE, 'text', FALSE);
  echo '</td>';
  echo "</tr>\n";



  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' .  TABLE_HEADING_IP_LIMIT . '</td>';
  echo '<td>';
  echo tep_draw_textarea_field('ip_limit', false, 20, 5); 
  echo '</td>';
  echo "</tr>\n";

  echo "</table>\n";

  echo '<br>';

  echo tep_draw_hidden_field("execute_new");        // 把处理模式放到隐藏项目里

  // 返回用户管理菜单
  echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) .  '">'.tep_html_element_button(IMAGE_BACK) . '</a>'; // 返回用户管理菜单
  // 显示按钮
  echo tep_draw_input_field("execute_insert", BUTTON_INSERT, 'class="element_button"', FALSE, "submit", FALSE);   // 添加
  echo tep_draw_input_field("clear", BUTTON_CLEAR, 'class="element_button"', FALSE, "reset", FALSE);        // 清除

  echo "</form>\n";           // form的footer


  return TRUE;

}

/*--------------------------------------
  功能: 保护用户信息（主要显示）
  参数: 无
  返回值: 显示成功(boolean)
 --------------------------------------*/
function UserInfo_preview() {

  global $ocertify;           // 用户认证项目

  PageBody('t', BUTTON_INFO_USER);    // 用户管理画面的标题显示（用户信息）

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));        // <form>标签的输出

  $ssql = makeSelectUserInfo($GLOBALS['userslist']);      // 获取用户信息
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      
    //错误的时候
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // 显示错误信息
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));            // <form>标签的输出
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // 获取记录件数
  if ($nrow != 1) {                     
    // 获取记录件数不是1件的时候
    echo TEXT_ERRINFO_DB_NO_USER;             // 显示错误信息
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));            // <form>标签的输出
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;                     // 退出处理
  }

  $arec = tep_db_fetch_array($oresult);
  if ($oresult) @tep_db_free_result($oresult);        // 开放结果项目

  // 启动表标签
  echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
  echo "<tr><td><table>\n";
  echo "<tr>\n";
  // 用户名称（用户ID）
  echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . ' colspan="2" nowrap><b>' .
    $arec['name'] . "（" . $_POST['userslist'] . '）</b></td>' . "\n";
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_NAME . '</td>';    // 姓名
  echo '<td>';
  echo tep_draw_input_field("aval[name]", $arec['name'], 'size="32" maxlength="64"', TRUE, 'text', FALSE);
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_EMAIL . '</td>';   // E-Mail
  // 输入项目输出
  echo '<td>';
  echo tep_draw_input_field("aval[email]", $arec['email'], 'size="32" maxlength="96"', FALSE, 'text', FALSE);
  echo '</td>';
  echo "</tr>\n";
  $ip_limit_query = tep_db_query("select * from user_ip where userid = '".$_POST['userslist']."'"); 
  $ip_limit_num = tep_db_num_rows($ip_limit_query);
  $ip_limit_str = ''; 
  if ($ip_limit_num > 0) {
    while ($ip_limit_res = tep_db_fetch_array($ip_limit_query)) {
      $ip_limit_str .= $ip_limit_res['limit_ip']."\n"; 
    }
  }
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' .  TABLE_HEADING_IP_LIMIT . '</td>'; 
  echo '<td>';
  echo tep_draw_textarea_field('ip_limit', false, 20, 5, $ip_limit_str); 
  echo '</td>';
  echo "</tr>\n";

  //设置密码
  echo "<tr>\n";
  echo "<td>\n";
  echo TEXT_LOGIN_COUNT;
  echo "</td>\n";
  echo "<td>\n";
  echo get_login_count($arec['name']);
  echo "</td>\n";
  echo "</tr>\n";


  echo "<tr>\n";
  echo "<td>\n";
  echo TEXT_RAND_PWD;
  echo "</td>\n";
  echo "<td>\n";
  if(isset($GLOBALS['letter'])&&isset($GLOBALS['rule'])
      &&$GLOBALS['rule']&&$GLOBALS['letter']){
  $temp_pwd = make_rand_pwd($GLOBALS['rule']);
  echo $GLOBALS['letter'].$temp_pwd; 
  }else{
  $temp_pwd = make_rand_pwd($arec['rule']);
  echo
    (tep_rand_pw_start($arec['userid'])?tep_rand_pw_start($arec['userid']):'').$temp_pwd;
  }
  echo "</td>\n";
  echo "</tr>\n";
  //规则
  echo "<tr>\n";
  echo "<td>\n";
  echo TEXT_RAND_RULES;
  echo "</td>\n";
  echo "<td>\n";
  if(isset($GLOBALS['letter'])&&$GLOBALS['letter']){
  echo tep_show_pw_start($arec['userid'],$GLOBALS['letter']);
  }else{
  echo tep_show_pw_start($arec['userid']);
  }
  if(isset($GLOBALS['rule'])&&$GLOBALS['rule']){
  echo tep_draw_input_field("rule", $GLOBALS['rule'], 'size="32" maxlength="64"', FALSE, 'text', FALSE);
  }else{
  echo tep_draw_input_field("rule", $arec['rule'], 'size="32" maxlength="64"', FALSE, 'text', FALSE);
  }
  echo "</td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td colspan='2' align='center'>\n";
  echo tep_draw_hidden_field("userslist", $arec['userid']);    
  echo tep_draw_input_field("execute_user", IMAGE_PREVIEW, 'class="element_button"', FALSE, "submit", FALSE);  // 用户信息
  echo tep_draw_input_field("reset", IMAGE_RESET, 'class="element_button"', FALSE, "reset", FALSE);  // 返回原来的值
  echo "</td>\n";
  echo "</tr>\n";
  echo "</table></td><td valign='top'>".TEXT_RAND_PWD_INFO."</td></tr>\n";
  echo "</table>\n";

  echo tep_draw_hidden_field("execute_user");           // 把处理模式放到隐藏项目里
  echo tep_draw_hidden_field("userid", $GLOBALS['userslist']);    // 把用户id放在隐藏项目里

  echo '<br>';

  echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) .  '">'.tep_html_element_button(IMAGE_BACK) . '</a>'; // 返回用户管理菜单
  // 显示按钮
  echo tep_draw_input_field("execute_update", BUTTON_UPDATE, "onClick=\"return formConfirm('update')\" class=\"element_button\"", FALSE, "submit", FALSE); // 更新

  // 管理员的时候，显示删除按钮
  if ($ocertify->npermission == 15) 
    echo tep_draw_input_field("execute_delete", BUTTON_DELETE, "onClick=\"return formConfirm('delete')\" class=\"element_button\"", FALSE, "submit", FALSE); // 删除

  echo tep_draw_input_field("reset", BUTTON_RESET, 'class="element_button"', FALSE, "reset", FALSE);  // 返回原来的值
  echo "\n";

  echo "</form>\n";                 // form的footer


  return TRUE;
}

/*--------------------------------------
  功能: 修改用户管理网站的权限
  参数: 无
  返回值: 无
 --------------------------------------*/
function ChangePermission(){
PageBody('t', PAGE_TITLE_CHANGE_PERMISSION); 

putJavaScript_ConfirmMsg();  
  $sql=" SELECT * FROM `permissions` ";
  $result =tep_db_query($sql);
  $site_sql="SELECT  id, romaji ,name  FROM `sites` ";
  $site_romaji = array();
  $site_result=tep_db_query($site_sql);
  while($site =tep_db_fetch_array($site_result)){
    $site_romaji[$site['id']]=$site['romaji'];//将网站siteid 与romaji 组合成数组 格式($site_id=>$romaji)
    }           


  echo tep_draw_form('users',basename($GLOBALS['PHP_SELF']),'execute_change=change');
  echo "<table>";
  echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
    echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' .USER. '</b></td>' . "\n"; 
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' .SITE_PREM. '</b></td>' . "\n";
    echo "</tr>\n";
while($userslist= tep_db_fetch_array($result)){
    echo "<tr><td>";
    echo $userslist['userid'];//输出用户名
   echo "</td><td>";
   $user_id=$userslist['userid'];
   $u_s_arr=array();
   if($userslist['site_permission']&&preg_match('/,/',$userslist['site_permission'])){
     $u_s_arr = explode(",",$userslist['site_permission']);//site_permission转为数组 exp:(1,6=>([0]=>1,[1]=>6 )
   }else{
     $u_s_arr[]=$userslist['site_permission'];
   }   
   //设置ALL的修改权限 并设置 admin 默认选择
     $site_str=  '<input name="'.$user_id.'[]" type="checkbox" id="0" value="0" ';
     if((is_array($u_s_arr)&&in_array('0',$u_s_arr))||
         (isset($userslist['permission'])&&$userslist['permission']==15)){ $site_str.=' checked />'; }//如果拥有权限  checkbox 属性为checked 显示为选中
     else {$site_str.='/>';}
     $site_str.= 'All';
     echo $site_str;

   foreach($site_romaji as $key =>$value){  
     $site_str=  '<input name="'.$user_id.'[]" type="checkbox" id="'.$key.'" value="'.$key.'" ';
     if(is_array($u_s_arr)&&in_array( $key,$u_s_arr)){ $site_str.=' checked />'; }//如果拥有权限  checkbox 属性为checked 显示为选中
     else {$site_str.='/>';}
     echo $site_str;
     echo $value;
   }
   echo "</td></tr>";
  //admin 权限 显示
}
echo "</table>";
//点击执行onclick 弹出y/n对话框
    echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) . '">' .  tep_html_element_button(IMAGE_BACK) . '</a>'; 
    echo tep_draw_input_field("execute_update", BUTTON_CHANGE, "onClick=\"return formConfirm('c_permission')\" class=\"element_button\"", FALSE, "submit", FALSE); // 变更


echo ' </form>';
}

/*--------------------------------------
  功能: 修改用户管理网站的权限的执行方法
  参数: 无
  返回值: 无
 --------------------------------------*/
function  ChangePermission_execute(){
  $y_n=true;
  PageBody('t', PAGE_TITLE_CHANGE_PERMISSION); 
  $sql=" SELECT * FROM `permissions` ";
  $result =tep_db_query($sql);  //获取用户的权限 （所有用户）
while($userslist= tep_db_fetch_array($result)){
  if($_POST[$userslist['userid']]){
    //获取页面 checkbox的值(数组)
    $u_s_id=$_POST[$userslist['userid']];
$u_id_str=implode(",",$u_s_id);
}else{
$u_id_str='';
}
//修改permission中 对应的userid的 site_permission
$permission_sid_sql="UPDATE ".TABLE_PERMISSIONS." SET `site_permission` = '".$u_id_str."' WHERE `permissions`.`userid` = '".$userslist['userid']. "' ";

if(tep_db_query($permission_sid_sql)){
  $y_n=true;
}else{ 
  $y_n= FALSE;
}
}
  if($y_n) {
    echo   TEXT_SUCCESSINFO_CHANGE_PERMISSION."<br><br>";//修改成功  输出成功语句

   }
  else {echo TEXT_ERRINFO_DB_CHANGE_PERMISSION."<br><br>";
   }
   echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) .  '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; 
}

/*--------------------------------------
  功能: 修改密码（主要显示）
  参数: 无
  返回值: 无
 --------------------------------------*/
function UserPassword_preview() {

  tep_redirect(tep_href_link(FILENAME_CHANGEPWD,
        'execute_password='.$GLOBALS['execute_password'].'&userslist='.$GLOBALS['userslist']));
}

/*--------------------------------------
  功能: 管理者権限（主要显示）
  参数: 无
  返回值: 显示成功(boolean)
 --------------------------------------*/
function UserPermission_preview() {

  PageBody('t', PAGE_TITLE_PERMISSION);   // 用户管理画面的标题显示（管理者権限）

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));  // <form>标签的输出

  // 获取一般用户信息
  $ssql = makeSelectUserParmission('7');             // 获取一般用户信息 sql 字符串生成

  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      
    //错误的时候
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // 显示错误信息
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>标签的输出
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // 获取记录件数
  if ($nrow > 0) {
    // 把在列表框里显示的数据放在数组里
    $i=0;
    while ($arec = tep_db_fetch_array($oresult)) {      // 获取记录
      $ausers[$i]['id'] = $arec['userid'];
      $ausers[$i]['text'] = $arec['name'];
      $i++;
    }
  }
  if ($oresult) @tep_db_free_result($oresult);        // 开放结果项目
  //chief start
  $ssql = makeSelectUserParmission('10');             // 获取一般用户信息 sql 字符串生成

  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      
    //错误的时候
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // 显示错误信息
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>标签的输出
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // 获取记录件数
  if ($nrow > 0) {
    // 把在列表框里显示的数据放在数组里
    $i=0;
    while ($arec = tep_db_fetch_array($oresult)) {      // 获取记录
      $ausers_chief[$i]['id'] = $arec['userid'];
      $ausers_chief[$i]['text'] = $arec['name'];
      $i++;
    }
  }
  if ($oresult) @tep_db_free_result($oresult);        // 开放结果项目

  //chief end


  // 获取拥有管理员权限的用户信息
  $ssql = makeSelectUserParmission('15');            // 获取拥有管理员权限的数据 sql 字符串生成

  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      
    //错误的时候
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // 显示错误信息
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>标签的输出
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // 获取记录件数
  if ($nrow > 0) {
    // 把在列表框里显示的数据放在数组里
    $i=0;
    while ($arec = tep_db_fetch_array($oresult)) {    // 获取记录
      $ausers_admin[$i]['id'] = $arec['userid'];
      $ausers_admin[$i]['text'] = $arec['name'];
      $i++;
    }
  }

  if ($oresult) @tep_db_free_result($oresult);          // 开放结果项目

  // 启动表标签
  echo '<table border="0" gbcolor="#FFFFFF" cellpadding="5" cellspacing="0">' . "\n";
  echo "<tr>\n";
  echo "<td>\n";                  // 数据单元格

    // 启动表标签（一般用户的列表框）
    echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
    echo "<tr>\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' .
      TABLE_HEADING_USER_STAFF . '</b></td>' . "\n"; // 一般用户
    echo "</tr>\n";

    echo "<td>\n";                  // 数据单元格
    echo tep_draw_pull_down_menu("staff_permission_list", $ausers, '', 'size="5"');  // 显示列表框
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

  echo "</td>\n";
  echo '<td align="center" valign="middle">' . "\n";                  // 数据单元格

    echo '<br>';
    echo tep_draw_input_field("execute_staff2chief", BUTTON_GRANT,  "onClick=\"return formConfirm('staff2chief')\"", FALSE, "submit", FALSE);  // 给予权利
    echo '<br>';
    echo tep_draw_input_field("execute_chief2staff", BUTTON_REVOKE, "onClick=\"return formConfirm('chief2staff')\"", FALSE, "submit", FALSE);  // 取消权利

  echo "</td>\n";
  echo "<td>\n";                  // 数据单元格

  //chief show start

    // 启动表标签（一般用户的列表框）
    echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
    echo "<tr>\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' .
      TABLE_HEADING_USER_CHIEF . '</b></td>' . "\n"; // 一般用户
    echo "</tr>\n";

    echo "<td>\n";                  // 数据单元格
    echo tep_draw_pull_down_menu("chief_permission_list", $ausers_chief, '', 'size="5"');  // 显示列表框
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

  echo "</td>\n";
  echo '<td align="center" valign="middle">' . "\n";                  // 数据单元格

    echo '<br>';
    echo tep_draw_input_field("execute_chief2admin", BUTTON_GRANT, "onClick=\"return formConfirm('chief2admin')\"", FALSE, "submit", FALSE);  // 给予权利
    echo '<br>';
    echo tep_draw_input_field("execute_admin2chief", BUTTON_REVOKE, "onClick=\"return formConfirm('admin2chief')\"", FALSE, "submit", FALSE);  // 取消权利

  echo "</td>\n";
  echo "<td>\n";                  // 数据单元格



  //chief show end

    // 启动表标签（拥有管理员权限的用户列表框）
    echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
    echo "<tr>\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' .
      TABLE_HEADING_USER_ADMIN . '</b></td>' . "\n";    // 网站管理员
    echo "</tr>\n";

    echo "<td>\n";                  // 数据单元格
    echo tep_draw_pull_down_menu("permission_list", $ausers_admin, '', 'size="5"'); // 显示列表框
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

  echo "</td>\n";
  echo "</tr>\n";
  echo "</table>\n";

  echo tep_draw_hidden_field("execute_permission");       // 把处理模式放到隐藏项目里

  echo "</form>\n";           // form的footer

  echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) . '">' .  tep_html_element_button(IMAGE_BACK) . '</a>'; // 返回用户管理菜单

  return TRUE;
}

/* ==============================================
  处理执行函数
 ============================================= */
/*--------------------------------------
  功能: 执行用户添加处理
  参数: 无
  返回值: 执行成功(boolean) 注:[:print:] 可以打印的人物名（=受限文字以外的人物名）
 --------------------------------------*/
function UserInsert_execute() {

  PageBody('t', PAGE_TITLE_INSERT_USER);    // 用户管理画面的标题显示（添加用户）

  // 用户ID的输入检查
  $aerror = "";
  $ret_err = checkLength_ge($GLOBALS['aval']['userid'], 2);
  if ($ret_err == "") $ret_err = checkNotnull($GLOBALS['aval']['userid']);
  if ($ret_err == "") $ret_err = checkStringEreg($GLOBALS['aval']['userid'], "[[:print:]]");
  if ($ret_err != "") set_errmsg_array($aerror, TABLE_HEADING_USER_ID . ':' . $ret_err);  // 用户ID

  // 密码的输入检查
  $ret_err = checkLength_ge($GLOBALS['aval']['password'], 2);
  if ($ret_err == "") $ret_err = checkNotnull($GLOBALS['aval']['password']);
  if ($ret_err == "") $ret_err = checkStringEreg($GLOBALS['aval']['password'], "[[:print:]]");
  if ($ret_err != "") set_errmsg_array($aerror, TABLE_HEADING_PASSWORD . ':' . $ret_err); // 密码

  // 姓名的输入检查
  $ret_err = checkNotnull($GLOBALS['aval']['name']);
  if ($ret_err != "") set_errmsg_array($aerror, TABLE_HEADING_NAME . ':' . $ret_err);   // 姓名

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>标签的输出

  if (is_array($aerror)) {      
    // 输入错误的时候
    print_err_message($aerror);   // 错误信息表示
    echo "<br>\n";
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";       // form的footer
    return FALSE;
  }

  // 检查添加的数据是否注册了
  $ssql = makeSelectUserInfo($GLOBALS['aval']['userid']);   // 获取用户信息
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      
    //错误的时候
    echo TEXT_ERRINFO_DB_USERCHACK;             // 显示错误信息
    echo "<br>\n";
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // 获取记录件数
  if ($nrow >= 1) {                     
    // 获取的数据不是0件的时候
    echo TEXT_ERRINFO_DB_EXISTING_USER;           // 显示错误信息
    echo "<br>\n";
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;                     // 退出处理
  }

  $ssql = makeInsertUser($GLOBALS['aval']);         // 获取用户管理表的添加sql字符串
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      
    //错误的时候
    echo TEXT_ERRINFO_DB_INSERT_USER;           // 显示错误信息
    echo "<br>\n";
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }

  $ssql = makeInsertUser($GLOBALS['aval'], 1);        // 获取用户权限表的添加sql字符串
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      
    //错误的时候
    echo TEXT_ERRINFO_DB_INSERT_PERMISSION;         // 显示错误信息
    echo "<br>\n";
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                   // form的footer
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }
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
      $ip_insert_sql = "insert user_ip values('".$GLOBALS['aval']['userid']."', '".$ip_value."')"; 
      tep_db_query($ip_insert_sql);
    }
  }
  echo "<br>\n";
  echo TEXT_SUCCESSINFO_INSERT_USER;    // 完成信息
  echo '<br><br>';
  echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
  echo "</form>\n";           // form的footer
  if($oresult){
    
    //获取个人设定中创建者，创建时间，更新者，更新时间的信息，进行更新
    $update_users_query = tep_db_query("select configuration_description from ". TABLE_CONFIGURATION ." where configuration_key='PERSONAL_SETTING_ORDERS_SITE'");
    $update_users_array = tep_db_fetch_array($update_users_query);
    tep_db_free_result($update_users_query);
    $users_array = array();
    if($update_users_array['configuration_description'] != ''){

      $users_array = unserialize($update_users_array['configuration_description']);
      
    }
    $users_array[$GLOBALS['aval']['userid']]['create_users'] = $_SESSION['user_name'];
    $users_array[$GLOBALS['aval']['userid']]['create_time'] = date('Y-m-d H:i:s');
    $update_users_str = serialize($users_array);
    tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_description='".$update_users_str."' where configuration_key='PERSONAL_SETTING_ORDERS_SITE'");
  }
  if ($oresult) @tep_db_free_result($oresult);    // 开放结果项目

  return TRUE;
}
/*--------------------------------------
  功能: 执行用户信息更新处理
  参数: 无
  返回值: 执行成功(boolean) 注:[:print:] 可以打印的人物名（=受限文字以外的人物名）
 --------------------------------------*/
function UserInfor_execute() {

  PageBody('t', PAGE_TITLE_USERINFO);   // 用户管理画面的标题显示（用户信息）

  // 姓名的输入检查
  $ret_err = checkNotnull($GLOBALS['aval']['name']);
  if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NAME . '</b>:' . $ret_err);   // 姓名

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>标签的输出

  if (isset($aerror) && is_array($aerror)) {      // 输入错误的时候
    print_err_message($aerror);   // 错误信息表示
    echo "<br>\n";
    echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);            // 把用户id放在隐藏项目里
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";       // form的footer
    return FALSE;
  }

  $ssql = makeUpdateUser($GLOBALS['aval']);         // 更新用户管理表的名字和邮件  获取sql字符串
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      //错误的时候
    echo TEXT_ERRINFO_DB_UPDATE_USER;           // 显示错误信息
    echo "<br>\n";
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
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
  echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
  echo "</form>\n";           // form的footer

  if ($oresult) @tep_db_free_result($oresult);    // 开放结果项目
  if(isset($GLOBALS['letter'])&&$GLOBALS['letter']!=''
      &&isset($GLOBALS['rule'])&&$GLOBALS['rule']!=''){
    update_rules($GLOBALS['userid'],$GLOBALS['rule'],$GLOBALS['letter']);
  }

  return TRUE;
}

/*--------------------------------------
  功能: 删除用户检查
  参数: 无
  返回值: 执行成功(boolean)
 --------------------------------------*/
function UserDelete_execute() {

  global $ocertify;           // 用户认证项目

  PageBody('t', PAGE_TITLE_USERINFO);   // 用户管理画面的标题显示（用户信息）

  if (strcmp($GLOBALS['userid'],$ocertify->auth_user) == 0)
    set_errmsg_array($aerror, TEXT_ERRINFO_USER_DELETE);      // 删除自己的信息会报错

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));    // <form>标签的输出

  if (isset($aerror)&&is_array($aerror)) {      
    // 输入错误的时候
    print_err_message($aerror);   // 错误信息表示
    echo "<br>\n";
    echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);  // 把用户id放在隐藏项目里
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";       // form的footer
    return FALSE;
  }

  $ssql = makeDeleteUser(1);              // 用户权限表から対象ユーザを删除する 获取sql字符串
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                  
    //错误的时候
    echo TEXT_ERRINFO_DB_DELETE_USER;       // 显示错误信息
    echo "<br>\n";
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";               // form的footer
    if ($oresult) @tep_db_free_result($oresult);  // 开放结果项目
    return FALSE;
  }

  $ssql = makeDeleteUser();             // 获取从用户管理表里删除用户的sql字符串
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                  
    //错误的时候
    echo TEXT_ERRINFO_DB_DELETE_USER;       // 显示错误信息
    echo "<br>\n";
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";               // form的footer
    if ($oresult) @tep_db_free_result($oresult);  // 开放结果项目
    return FALSE;
  }
  tep_db_query("delete from user_ip where userid = '".$GLOBALS['userid']."'");

  echo "<br>\n";
  echo TEXT_SUCCESSINFO_DELETE_USER;          // 完成信息
  echo "<br><br>\n";
  echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);    // 返回用户管理菜单
  echo "</form>\n";                 // form的footer

  if ($oresult) @tep_db_free_result($oresult);    // 开放结果项目
  
  $orders_users_query = tep_db_query("select orders_id,read_flag from ". TABLE_ORDERS ." where read_flag != ''");
  while($orders_users_array = tep_db_fetch_array($orders_users_query)){

    $orders_users_info_array = explode('|||',$orders_users_array['read_flag']);   
    if(in_array($GLOBALS['userid'],$orders_users_info_array)){

      unset($orders_users_info_array[array_search($GLOBALS['userid'],$orders_users_info_array)]);
      $orders_users_info_str = implode('|||',$orders_users_info_array);
      tep_db_query("update ". TABLE_ORDERS ." set read_flag='".$orders_users_info_str."' where orders_id='".$orders_users_array['orders_id']."'");
    }
  }
  tep_db_free_result($orders_users_query);

  $preorders_users_query = tep_db_query("select orders_id,read_flag from ". TABLE_PREORDERS ." where read_flag != ''");
  while($preorders_users_array = tep_db_fetch_array($preorders_users_query)){

    $preorders_users_info_array = explode('|||',$preorders_users_array['read_flag']);   
    if(in_array($GLOBALS['userid'],$preorders_users_info_array)){

      unset($preorders_users_info_array[array_search($GLOBALS['userid'],$preorders_users_info_array)]);
      $preorders_users_info_str = implode('|||',$preorders_users_info_array);
      tep_db_query("update ". TABLE_PREORDERS ." set read_flag='".$preorders_users_info_str."' where orders_id='".$preorders_users_array['orders_id']."'");
    }
  }
  tep_db_free_result($preorders_users_query);

  if(PERSONAL_SETTING_ORDERS_SITE != ''){

    $orders_site_array = unserialize(PERSONAL_SETTING_ORDERS_SITE);
    if(array_key_exists($GLOBALS['userid'],$orders_site_array)){

      unset($orders_site_array[$GLOBALS['userid']]);
      $orders_site_str = serialize($orders_site_array);
      tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$orders_site_str."' where configuration_key='PERSONAL_SETTING_ORDERS_SITE'");
    }
  }

  if(PERSONAL_SETTING_ORDERS_WORK != ''){

    $orders_work_array = unserialize(PERSONAL_SETTING_ORDERS_WORK);
    if(array_key_exists($GLOBALS['userid'],$orders_work_array)){

      unset($orders_work_array[$GLOBALS['userid']]);
      $orders_work_str = serialize($orders_work_array);
      tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$orders_work_str."' where configuration_key='PERSONAL_SETTING_ORDERS_WORK'");
    }
  }

  if(PERSONAL_SETTING_ORDERS_SORT != ''){

    $orders_sort_array = unserialize(PERSONAL_SETTING_ORDERS_SORT);
    if(array_key_exists($GLOBALS['userid'],$orders_sort_array)){

      unset($orders_sort_array[$GLOBALS['userid']]);
      $orders_sort_str = serialize($orders_sort_array);
      tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$orders_sort_str."' where configuration_key='PERSONAL_SETTING_ORDERS_SORT'");
    }
  }

  if(PERSONAL_SETTING_PREORDERS_SITE != ''){

    $preorders_site_array = unserialize(PERSONAL_SETTING_PREORDERS_SITE);
    if(array_key_exists($GLOBALS['userid'],$preorders_site_array)){

      unset($preorders_site_array[$GLOBALS['userid']]);
      $preorders_site_str = serialize($preorders_site_array);
      tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$preorders_site_str."' where configuration_key='PERSONAL_SETTING_PREORDERS_SITE'");
    }
  }

  if(PERSONAL_SETTING_PREORDERS_WORK != ''){

    $preorders_work_array = unserialize(PERSONAL_SETTING_PREORDERS_WORK);
    if(array_key_exists($GLOBALS['userid'],$preorders_work_array)){

      unset($preorders_work_array[$GLOBALS['userid']]);
      $preorders_work_str = serialize($preorders_work_array);
      tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$preorders_work_str."' where configuration_key='PERSONAL_SETTING_PREORDERS_WORK'");
    }
  }

  if(PERSONAL_SETTING_PREORDERS_SORT != ''){

    $preorders_sort_array = unserialize(PERSONAL_SETTING_PREORDERS_SORT);
    if(array_key_exists($GLOBALS['userid'],$preorders_sort_array)){

      unset($preorders_sort_array[$GLOBALS['userid']]);
      $preorders_sort_str = serialize($preorders_sort_array);
      tep_db_query("update ". TABLE_CONFIGURATION ." set configuration_value='".$preorders_sort_str."' where configuration_key='PERSONAL_SETTING_PREORDERS_SORT'");
    }
  }

  tep_db_query("delete from notes where author='".$GLOBALS['userid']."'");
  return TRUE;

}

/*--------------------------------------
  功能: 执行密码变更处理
  参数: 无
  返回值: 执行成功(boolean) 注:[:print:] 可以打印的人物名（=受限文字以外的人物名）
 --------------------------------------*/
function UserPassword_execute() {

  PageBody('t', PAGE_TITLE_PASSWORD);   // 用户管理画面的标题显示（密码変更）

  // 新密码的输入检查
  $ret_err = checkNotnull($GLOBALS['aval']['password']);
  if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NEW_PASSWORD . '</b>:' . $ret_err);
  $ret_err = checkLength_ge($GLOBALS['aval']['password'], 2);
  if ($ret_err == "") $ret_err = checkStringEreg($GLOBALS['aval']['password'], "[[:print:]]");
  if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NEW_PASSWORD . '</b>:' . $ret_err);
  // 为了确认再次输入的检查
  if (strcmp($GLOBALS['aval']['password'],$GLOBALS['aval']['chk_password']) != 0)
    set_errmsg_array($aerror, TEXT_ERRINFO_CONFIRM_PASSWORD);

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>标签的输出

  if (isset($aerror) && is_array($aerror)) {      
    // 输入错误的时候
    print_err_message($aerror);   // 错误信息表示
    echo "<br>\n";
    echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);    // 把用户id放在隐藏项目里
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";       // form的footer
    return FALSE;
  }

  $ssql = makeUpdateUser($GLOBALS['aval'], 1);    // 更新用户管理表的密码る 获取sql字符串
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                  
    //错误的时候
    echo TEXT_ERRINFO_DB_CHANGE_PASSWORD;     // 显示错误信息
    echo "<br>\n";
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";               // form的footer
    if ($oresult) @tep_db_free_result($oresult);  // 开放结果项目
    return FALSE;
  }

  echo "<br>\n";
  echo TEXT_SUCCESSINFO_CHANGE_PASSWORD;    // 完成信息
  echo "<br><br>\n";
  echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
  echo "</form>\n";           // form的footer

  if ($oresult) @tep_db_free_result($oresult);    // 开放结果项目

  return TRUE;

}

/*--------------------------------------
  功能: 用户权限选择检查
  参数: $nmode(int) 更新模式（0:grant、1:revoke）
  返回值: 检查成功(boolean)
 --------------------------------------*/
function UserPermission_execute($nmode=0) {

  global $ocertify;           // 用户认证项目

  PageBody('t', PAGE_TITLE_PERMISSION);   // 用户管理画面的标题显示（管理者権限）

  //add by szn chief permission  start
  if ($nmode == 'staff2chief' ) {    
    $suserid = $GLOBALS['staff_permission_list'];
    if ($suserid == "") set_errmsg_array($aerror, TEXT_ERRINFO_USER_STAFF);
  } else if ($nmode == 'chief2admin'||$nmode == 'chief2staff') {    
    $suserid = $GLOBALS['chief_permission_list'];
    if ($suserid == "") set_errmsg_array($aerror, TEXT_ERRINFO_USER_CHIEF);
  } else if ($nmode == 'admin2chief'){        
    $suserid = $GLOBALS['permission_list'];
    if ($suserid == "") set_errmsg_array($aerror, TEXT_ERRINFO_USER_ADMIN);
  }
  
  
  if (strcmp($suserid,$ocertify->auth_user) == 0) 
      set_errmsg_array($aerror, TEXT_ERRINFO_USER_REVOKE_ONESELF);

  //add by szn chief permission  end
  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));  // <form>标签的输出

  if (is_array($aerror)) {                    
    // 输入错误的时候
    print_err_message($aerror);                 // 错误信息表示
    echo "<br>\n";
    echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);            // 把用户id放在隐藏项目里
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                     // form的footer
    return FALSE;
  }

  $ssql = makeUpdatePermission($nmode, $suserid);         // 更新用户权限表 获取sql字符串
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                        
    //错误的时候
    echo TEXT_ERRINFO_DB_CHANGE_USER;             // 显示错误信息
    echo "<br>\n";
    echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);  // 返回用户管理菜单
    echo "</form>\n";                     // form的footer
    if ($oresult) @tep_db_free_result($oresult);        // 开放结果项目
    return FALSE;
  }

  if($nmode == 0){
    echo TEXT_SUCCESSINFO_PERMISSION_GIVE;
  }else{
    echo TEXT_SUCCESSINFO_PERMISSION_CLEAR;
  }
  echo "<br><br>\n";
  echo tep_draw_input_field("execute_permission", BUTTON_BACK_PERMISSION, '', FALSE, "submit", FALSE);  // 返回管理员权限
  echo tep_draw_input_field("back", IMAGE_BACK, 'class="element_button"', FALSE, "submit", FALSE);            // 返回用户管理菜单
  echo "</form>\n";                 // form的footer

  if ($oresult) @tep_db_free_result($oresult);    // 开放结果项目

  return TRUE;

}

/*--------------------------------------
  功能: 用于确认信息的JavaScript
  参数: 无
  返回值: 无
 --------------------------------------*/
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
      rtn = confirm("'. JAVA_SCRIPT_INFO_PASSWORD . '");
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
-->
</script>
';

}

/*--------------------------------------
  功能: 显示页面头部
  参数: 无
  返回值: 无
 --------------------------------------*/
function PageHeader() {
  global $ocertify,$page_name,$notes;
  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n";
  echo '<html ' . HTML_PARAMS . '>' . "\n";
  echo '<head>' . "\n";
  echo '<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET . '">' . "\n";
  if(isset($_POST['execute_new'])){echo '<title>'.HEADING_TITLE.'('.$_POST['execute_new'].')' . '</title>' . "\n";}
  else if(isset($_POST['execute_user'])){echo '<title>'.HEADING_TITLE.'('.$_POST['execute_user'].')' . '</title>' . "\n";}
  else if(isset($_POST['execute_permission'])){echo '<title>'.HEADING_TITLE.'('.$_POST['execute_permission'].')' . '</title>' . "\n";}
  else if(isset($_POST['execute_change'])){echo '<title>'.HEADING_TITLE.'('.$_POST['execute_change'].')' . '</title>' . "\n";}
  else{echo '<title>'.HEADING_TITLE.'(ユーザ管理メニュー)' . '</title>' . "\n"; }
    
  echo '<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">' . "\n";

  if ((isset($GLOBALS['execute_user']) && $GLOBALS['execute_user']) || (isset($GLOBALS['execute_password']) && $GLOBALS['execute_password']) || (isset($GLOBALS['execute_permission']) && $GLOBALS['execute_permission']) ) {
    // 修改用户信息、密码、管理员权限的时候显示确认信息 JavaScript 
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

  echo '<!-- header -->' . "\n";
  require(DIR_WS_INCLUDES . 'header.php');
  echo '<!-- header_eof -->' . "\n";
}

/*--------------------------------------
  功能: 显示页面布局
  参数: $mode(string) 模式（t:上、u:下）
  返回值: 无
 --------------------------------------*/
function PageBodyTable($mode='t') {
  global $ocertify;
  switch ($mode) {
  case 't':
    echo '<!-- body -->' . "\n";
    echo '<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">' . "\n";
    echo '  <tr>' . "\n";
    if($GLOBALS['ocertify']->npermission >= 10){
    echo '    <td width="' . BOX_WIDTH . '" valign="top"><table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">' . "\n";
    }
    break;
  case 'u':
    echo '  </tr>' . "\n";
    echo '</table>' . "\n";
    echo '<!-- body_eof -->' . "\n";
    break;
  } 
}

/*--------------------------------------
  功能: 显示页面
  参数: $mode(string) 模式（t:上、u:下）
  参数: $stitle(string) body的标题
  返回值: 无
 --------------------------------------*/
function PageBody($mode='t', $stitle = "") {
  global $notes;
  switch ($mode) {
  case 't':
    echo '<!-- body_text -->' . "\n";
    echo '    <td width="100%" valign="top" class="box"><div class="box_warp">'. $notes.'<div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n";
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
    echo '<!-- body_text_eof -->' . "\n";
    break;
  } 
}

/*--------------------------------------
  功能: 显示页脚
  参数: 无
  返回值: 无
 --------------------------------------*/
function PageFooter() {
  echo "<!-- footer -->\n";
  require(DIR_WS_INCLUDES . 'footer.php');
  echo "\n<!-- footer_eof -->\n";
  echo "<br>\n";
  echo "</body>\n";
  echo "</html>\n";
}

/*--------------------------------------
  功能: 获取当前用户当天 登录次数
  参数: $user(string) 用户名
  返回值: 登录次数(int)
 --------------------------------------*/
function get_login_count($user){
  $count_sql = "SELECT count( sessionid ) as len 
    FROM `login`
    WHERE date( `logintime` ) = date( now( ) )
    AND account = '".$user."'";
  $count_query = tep_db_query($count_sql);
  if($count_res = tep_db_fetch_array($count_query)){
    return $count_res['len'];
  }else{
    return 0;
  }
}

/*--------------------------------------
  功能: 修改规则 并插入数据库
  参数: $userid(int) 用户id
  参数: $rule(string) 规则
  参数: $letter(string) 算式
  返回值: 是否更新成功(boolean)
 --------------------------------------*/
function update_rules($userid,$rule,$letter){
  $sql_user = "update ".TABLE_USERS. " set rule ='".$rule."'
    where userid = '".$userid."'";
  $sql_s = "select * from ".TABLE_LETTERS." 
    WHERE userid='".$userid."' limit 1";
  $res_s = tep_db_query($sql_s);
  if($row = tep_db_fetch_array($res_s)){
    if($letter){
      $clear_sql = "update ".TABLE_LETTERS." set userid = null
          where userid='".$userid."'";
      tep_db_query($clear_sql);
    }
  }
  $sql_letter = "update ".TABLE_LETTERS." set userid = '".$userid."'
     where letter='".$letter."'";
  if(make_rand_pwd($rule)){
    tep_db_query($sql_user); 
    tep_db_query($sql_letter);
    return true;
  }else{
    return false;
  }

}


/* *************************************

   用户信息保护画面的程序控制（主要）

 ************************************* */

  require('includes/application_top.php');
  if (isset($_POST['userid'])) { $userid = $_POST['userid']; }
  if (isset($_POST['aval'])) { $aval = $_POST['aval']; }
  if (isset($_POST['userslist'])) { $userslist = $_POST['userslist']; }
  else if(isset($_GET['userslist'])) { $userslist = $_GET['userslist']; }
  if (isset($_POST['no_permission_list'])) { $no_permission_list = $_POST['no_permission_list']; }
  if (isset($_POST['staff_permission_list'])) { $staff_permission_list =
    $_POST['staff_permission_list']; }
  if (isset($_POST['chief_permission_list'])) { $chief_permission_list =
    $_POST['chief_permission_list']; }
  if (isset($_POST['permission_list'])) { $permission_list = $_POST['permission_list']; }
  if (isset($_POST['execute_user'])) { $execute_user = $_POST['execute_user']; }
  if (isset($_POST['execute_password'])) { $execute_password = $_POST['execute_password']; }
  else if(isset($_GET['execute_password'])) { $execute_password =
    $_GET['execute_password']; }
  if (isset($_POST['execute_permission'])) { $execute_permission = $_POST['execute_permission']; }
//修改权限
if (isset($_POST['execute_change'])) { $execute_change = $_POST['execute_change'];}
        if (isset($_POST['execute_new'])) { $execute_new = $_POST['execute_new']; }
        if (isset($_POST['execute_insert'])) { $execute_insert = $_POST['execute_insert']; }
        if (isset($_POST['execute_update'])) { $execute_update = $_POST['execute_update']; }
        if (isset($_POST['execute_delete'])) { $execute_delete = $_POST['execute_delete']; }
        if (isset($_POST['execute_grant'])) { $execute_grant = $_POST['execute_grant']; }
        if (isset($_POST['execute_reset'])) { $execute_reset = $_POST['execute_reset']; }
        if (isset($_POST['execute_staff2chief'])) { $execute_staff2chief =
          $_POST['execute_staff2chief']; }
        if (isset($_POST['execute_chief2staff'])) { $execute_chief2staff =
          $_POST['execute_chief2staff']; }
        if (isset($_POST['execute_chief2admin'])) { $execute_chief2admin =
          $_POST['execute_chief2admin']; }
        if (isset($_POST['execute_admin2chief'])) { $execute_admin2chief =
          $_POST['execute_admin2chief']; }
if (isset($_POST['execute_c_permission'])) { $execute_change = $_POST['execute_c_permission'];}
if (isset($_POST['rule'])) { $rule = $_POST['rule'];}
if (isset($_POST['letter'])) { $letter = $_POST['letter'];}
  PageHeader();       // 显示页面头部
  PageBodyTable('t');     // 页面布局表：开始（启动包括导航的表）

  // 显示左侧导航
  if($ocertify->npermission >= 10){
  echo "<!-- left_navigation -->\n";    // 
  include_once(DIR_WS_INCLUDES . 'column_left.php');
  echo "\n<!-- left_navigation_eof -->\n";
  echo "    </table></td>\n";
  }

  if ($ocertify->auth_user) {
  // 显示画面。输入检查，反应DB
    if (isset($execute_menu) && $execute_menu) {
    // 用户管理菜单
      UserManu_preview();               // 初期显示

    } else if (isset($execute_new) && $execute_new) {
    // 添加用户
      if (isset($execute_insert) && $execute_insert) {
        UserInsert_execute();    // 执行用户添加处理
      }else{
        UserInsert_preview();            // 添加用户页面显示
      }

    } else if (isset($execute_user) && $execute_user) {
    // 保护用户信息
      if (isset($execute_update) && $execute_update){
        UserInfor_execute();   // 执行用户信息更新处理
      }else if (isset($execute_delete) && $execute_delete){
        UserDelete_execute();  // 执行用户信息删除处理
      }else {
        UserInfo_preview();            // 用户信息页面显示
      }

    } else if (isset($execute_permission) && $execute_permission) {
    // 修改密码

//permission start

      if (isset($execute_staff2chief) && $execute_staff2chief){
        UserPermission_execute('staff2chief');   
      } else if (isset($execute_chief2staff) && $execute_chief2staff) {
        UserPermission_execute('chief2staff'); 
      } else if (isset($execute_chief2admin) && $execute_chief2admin){
        UserPermission_execute('chief2admin'); 
      } else if (isset($execute_admin2chief) && $execute_admin2chief){
        UserPermission_execute('admin2chief'); 
      } else { 
        UserPermission_preview();                // 管理员权限页面显示
      }

//permission end 
 
    } elseif (isset($execute_change) && $execute_change) {
      if (isset($execute_update) && $execute_update)   {
        // 修改用户管理网站的权限的执行
        ChangePermission_execute();  } 
      else{ 
        //用户权限页面 
        ChangePermission();}
    } else {
    // 用户管理菜单
      UserManu_preview();               // 初期显示
    }
  }

  PageBody('u');        // 页面：结束
  PageBodyTable('u');     // 页面布局表：结束
  PageFooter();       // 页脚的显示

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
