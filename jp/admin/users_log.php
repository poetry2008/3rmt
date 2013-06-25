<?php
/* ===============================================
  global 常量
 ============================================== */
// 表名
  define('TABLE_LOGIN', 'login');
// 列表显示行数
  define('LOGIN_LOG_MAX_LINE', '30');
// MySQL 的 limit （其他的DB里有不一样的语法所以需要修改）
  define('TABLE_LIMIT_OFFSET', 'limit %s,' . LOGIN_LOG_MAX_LINE);

/* ===============================================
  global 变量
 ============================================== */
  $TableBorder = 'border="0"';        // 表：线的宽度
  $TableCellspacing = 'cellspacing="3"';    // 表：间距
  $TableCellpadding = 'cellpadding="3"';    // 表：内间距
  $TableBgcolor = 'bgcolor="#FFFFFF"';    // 表：背景色

  $ThBgcolor = 'bgcolor="Gainsboro"';     // 头部：背景色
  $TdnBgcolor = 'bgcolor="WhiteSmoke"';   // 表格：项目名背景色

  $FontColor = 'color="#009900"';       // 字体：标志颜色

  if (isset($HTTP_POST_VERS['lm'])) { $lm = $HTTP_POST_VERS['lm']; }
  if (isset($HTTP_POST_VERS['jp'])) { $jp = $HTTP_POST_VERS['jp']; }
  if (isset($HTTP_POST_VERS['pp'])) { $pp = $HTTP_POST_VERS['pp']; }
  if (isset($HTTP_POST_VERS['np'])) { $np = $HTTP_POST_VERS['np']; }
  if (isset($HTTP_POST_VERS['aval'])) { $aval = $HTTP_POST_VERS['aval']; }
        if (isset($_POST['sp'])) { $sp = $_POST['sp']; }
        if (isset($_POST['execute_delete'])) { $execute_delete = $_POST['execute_delete']; }

/* ===============================================
  获取记录 sql 字符串生成函数（Select）
 ============================================== */
/*--------------------------------------
  功能: 访问日志列表显示
  参数: $oresult(resource) 记录项目
  返回值: 无
 --------------------------------------*/
function show_loginlog_list($oresult) {

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

    if ($rec_c % 2) echo "<tr " . $GLOBALS['TdnBgcolor'] . ">\n";
    else echo "<tr>\n";

    // 用户
    echo '<td class="main" >' . $arec['account'] . "</td>\n";
    // 登录日期时间
    echo '<td class="main" >' . $arec['logintime'] . "</td>\n"; 
    // 最终访问日期时间
    echo '<td class="main" >' . $arec['lastaccesstime'] . "</td>\n"; 
    // 退出登录状态
    if ($arec['logoutstatus']) {
      echo '<td class="main" >' .$arec['loginstatus'] . ' [' . $GLOBALS['a_sts_in'][$arec['loginstatus']] . ']&nbsp;&nbsp;'. $arec['logoutstatus'] . ' [' . $GLOBALS['a_sts_out'][$arec['logoutstatus']] . ']' . "</td>\n";
    }
    else {
      echo '<td class="main" >'.$arec['loginstatus'] . ' [' . $GLOBALS['a_sts_in'][$arec['loginstatus']] . ']' . "</td>\n";
    }
    // 地址
    echo '<td class="main" >' . $saddress . "</td>\n";

    echo "</tr>\n";
    $rec_c++;
  }
}

/*--------------------------------------
  功能: 页面控制按钮显示
  参数: $nrow(int) 记录件数（列表行数）
  返回值: 记录件数
 --------------------------------------*/
function show_page_ctl($nrow) {
  $c_page = 0;

  // 获取记录总件数
  $ssql = "select count(*) as rc from " . TABLE_LOGIN;
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      
    // 错误的时候
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }

  $arec = tep_db_fetch_array($oresult);           // 获取记录
  echo tep_draw_hidden_field("lm", $GLOBALS['lm']);     // 把现在的页面放在隐藏项目里

  // 显示按钮
  if ($GLOBALS['lm'] >= LOGIN_LOG_MAX_LINE) {
    echo tep_draw_input_field("pp", BUTTON_PREVIOUS_PAGE, '', FALSE, "submit", FALSE);  // 前一页
  }
  if ($GLOBALS['lm'] + LOGIN_LOG_MAX_LINE < $arec['rc']) {
    echo tep_draw_input_field("np", BUTTON_NEXT_PAGE, '', FALSE, "submit", FALSE);    // 后一页
  }

  $page_count = ceil($arec['rc'] / LOGIN_LOG_MAX_LINE);
  for ($i=1; $i<=$page_count; $i++) {
    $lm_ = ($i-1) * LOGIN_LOG_MAX_LINE;
    $asp[$i-1]['id'] = $lm_;
    $asp[$i-1]['text'] = $i;
  }
  echo '&nbsp;&nbsp;';
  $GLOBALS['sp'] = $GLOBALS['lm'];              // 设置下拉列表的选择值
  echo tep_draw_pull_down_menu("sp", $asp, $GLOBALS['lm']); // 显示下拉列表
  echo tep_draw_input_field("jp", BUTTON_JUMP_PAGE, '', FALSE, "submit", FALSE);    // 跳转页面

  if ($GLOBALS['lm']) $c_page = ceil((int)$GLOBALS['lm'] / LOGIN_LOG_MAX_LINE);
  $c_page++;
  echo '<font class="main">&nbsp;&nbsp;' . sprintf(TEXT_PAGE, $c_page,$page_count,$nrow,$arec['rc']) . '</font>' . "\n";
  echo "<br>\n";

}

/*--------------------------------------
  功能: 获取访问日志信息 sql 字符串生成
  参数: 无
  返回值: 语句字符串(string)
 --------------------------------------*/
function makeSelectLoginLog() {

  $s_select = "select * from " . TABLE_LOGIN;
  $s_select .= " order by logintime desc";    // 按照访问日期时间的倒序获取数据
  if (!isset($GLOBALS['lm'])) $GLOBALS['lm'] = 0;
  $s_select .= " " . sprintf(TABLE_LIMIT_OFFSET,$GLOBALS['lm']);

  return $s_select;

}

/*--------------------------------------
  功能: 登录ip用户列表
  参数: 无
  返回值: 无
 --------------------------------------*/
function UserLoginIp_list(){

  PageBody('t', PAGE_TITLE_MENU_IP,true); 
    echo TEXT_IP_UNLOCK_NOTES.'</td>';
    echo '</tr><tr><td>';
    echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
    echo "<tr>\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' .  TABLE_HEADING_ADDRESS . '</b></td>' . "\n";    
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' .  TABLE_HEADING_LOGINTIME . '</b></td>' . "\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' .  TABLE_HEADING_PERMISSIONS . '</b></td>' . "\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' . TABLE_HEADING_USER .  '</b></td>' . "\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' . TABLE_HEADING_OPERATE .  '</b></td>' . "\n";
    echo "</tr>\n";
    $j = 1;
    $user_login_query = tep_db_query("select address,count(*) as num from ". TABLE_LOGIN ." where loginstatus!='a' and time_format(timediff(now(),logintime),'%H')<24 and status='0' group by address having num>=5 order by logintime desc");
    while($user_login_array = tep_db_fetch_array($user_login_query)){

      $user_name_array = array();
      $user_time_array = array();
      $user_time_temp_array = array();
      $user_id_array = array();
      $user_login_list_query = tep_db_query("select * from ". TABLE_LOGIN ." where loginstatus!='a' and time_format(timediff(now(),logintime),'%H')<24 and address='". $user_login_array['address'] ."' and status='0' order by logintime asc");
      while($user_login_list_array = tep_db_fetch_array($user_login_list_query)){

        $user_name_array[] = $user_login_list_array['account'];
        $user_time_temp_array[$user_login_list_array['account']] = $user_login_list_array['logintime']; 
        $user_time_array[] = $user_login_list_array['logintime'];
        $user_id_array[] = $user_login_list_array['sessionid'];
      }
      foreach($user_name_array as $key=>$value){

        if(trim($value) == ''){
          unset($user_name_array[$key]); 
        } 
      }
      $user_name_temp_array = array_count_values($user_name_array);
      $user_admin_name_array = array();
      $user_admin_name_temp_array = array();
      foreach($user_name_temp_array as $k=>$v){

        $per_query = tep_db_query("select userid,permission from ". TABLE_PERMISSIONS ." where userid='".$k."'");
        $per_array = tep_db_fetch_array($per_query);
        tep_db_query($per_query);
        if($v >= 5){ 
          if($per_array['userid'] == $k && $per_array['permission'] > 15){

            $user_admin_name_array[] = $k;
          }
        }else{
          if($per_array['userid'] == $k && $per_array['permission'] > 15){
            $user_admin_name_temp_array[] = $k; 
          } 
        }
      }
      $user_name_array = array_unique($user_name_array);
      foreach($user_name_array as $user_key=>$user_value){

        if(in_array($user_value,$user_admin_name_array)){

          unset($user_name_array[$user_key]);
        }
        if(in_array($user_value,$user_admin_name_temp_array)){

          unset($user_name_array[$user_key]);
        }
      }
      $naddress = (int)$user_login_array['address'];    // IP地址复原
      $saddress = '';
      for ($i=0; $i<4; $i++) {
        if ($i) $saddress = ($naddress & 0xff) . '.' . $saddress;
        else $saddress = (string)($naddress & 0xff);
        $naddress >>= 8;
      }
      
      if ($j % 2){ 
        echo "<tr " . $GLOBALS['TdnBgcolor'] . " id='ip_".$j."'>\n";
      }else{
        echo '<tr id="ip_'.$j.'">'; 
      }
      $user_name_list_array = $user_name_array;
      if(count($user_name_list_array) == 6){

        array_pop($user_name_list_array);
      }
        echo '<td>'.$saddress.'</td>';
        echo '<td>'.max($user_time_array).'</td>'; 
        echo '<td>Staff,Chief,Admin</td>';
        echo '<td>'.implode(',',$user_name_list_array).'</td>';
        echo '<td>';
        if(empty($user_admin_name_array)){
          echo '<a href="javascript:void(0);" onclick="if(confirm(\''.TEXT_DELETE_CONFIRM.'\')){ip_unlock(\''.$user_login_array['address'].'\','.$j.',\'\');}"><u>'.TEXT_IP_UNLOCK.'</u></a>';
        }
        echo '</td>';
        echo '</tr>';
      tep_db_free_result($user_login_list_query);
      $k = 1; 
      foreach($user_admin_name_array as $admin_key=>$admin_value){
       
        if (($j+$k) % 2){ 
          echo "<tr " . $GLOBALS['TdnBgcolor'] . " id='ip_".($j+$k)."'>\n";
        }else{
          echo '<tr id="ip_'.($j+$k).'">'; 
        }
        echo '<td>'.$saddress.'</td>';
        echo '<td>'.$user_time_temp_array[$admin_value].'</td>'; 
        echo '<td>Root</td>';
        echo '<td>'.$admin_value.'</td>';
        echo '<td><a href="javascript:void(0);" onclick="if(confirm(\''.TEXT_DELETE_CONFIRM.'\')){ip_unlock(\''.$user_login_array['address'].'\','.($j+$k).',\''.$admin_value.'\');}"><u>'.TEXT_IP_UNLOCK.'</u></a></td>';
        echo '</tr>';   
        $k++;
      }
      $j += $k-1;
      $j++;
    }
    tep_db_free_result($user_login_query);
    // 自动删除过期数据
    $user_id_str = implode("','",$user_id_array);
    $alarm_day = get_configuration_by_site_id('USERS_EXPIRED_DATE_SETTING',0); 
    tep_db_query("delete from login where sessionid not in('".$user_id_str."') and time_format(timediff(now(),logintime),'%H')>".$alarm_day*24);
    echo "</table>\n</td></tr><tr>";
}
/* ==============================================
  画面显示函数（主要）
 ============================================= */
/*--------------------------------------
  功能: 访问日志信息列表显示
  参数: 无
  返回值: 无
 --------------------------------------*/
function UserLoginLog_list() {

  global $ocertify;           // 用户认证项目

  PageBody('t', HEADING_TITLE);  // 用户管理画面的标题显示（用户管理菜单）

  // 现在的页面（获取记录开始位置）
  if (isset($GLOBALS['jp']) && $GLOBALS['jp']) $GLOBALS['lm'] = (int)$GLOBALS['sp'];
  if (isset($GLOBALS['pp']) && $GLOBALS['pp']) (int)$GLOBALS['lm'] -= LOGIN_LOG_MAX_LINE;
  if (isset($GLOBALS['np']) && $GLOBALS['np']) (int)$GLOBALS['lm'] += LOGIN_LOG_MAX_LINE;

  // 获取访问日志信息
  $ssql = makeSelectLoginLog();
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      
    // 错误的时候
    echo TEXT_ERRINFO_DB_NO_LOGINFO;            // 显示信息
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // 获取记录件数
  if ($nrow > 0) {                      
    // 取不到记录的时候
    // 表标签的开始
    echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
    echo "<tr>\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' . TABLE_HEADING_USER .  '</b></td>' . "\n";       // 用户
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' . TABLE_HEADING_LOGINTIME . '</b></td>' . "\n";      // 登录日 
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' . TABLE_HEADING_LAST_ACCESSTIME . '</b></td>' . "\n";  // 最最终访问日期时间
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' . TABLE_HEADING_STATUS . '</b></td>' . "\n";       // 状态
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '><b>' . TABLE_HEADING_ADDRESS . '</b></td>' . "\n";      // 地址
    echo "</tr>\n";
    show_loginlog_list($oresult);   // 列表显示访问日志信息
    echo "</table>\n";

    echo tep_draw_form('users_form', basename($GLOBALS['PHP_SELF']));    // <form>标签的输出
    show_page_ctl($nrow);       // 页面控制按钮的显示

    // 表标签的开始
    echo '<table border="0" cellspacing="1" cellpadding="1">' . "\n";
    echo "<tr>\n";

    // 日志的删除
    echo '<td class="main">' . TEXT_INFO_DELETE_DAY . "</td>\n";
    echo '<td class="main">&nbsp;&nbsp;&nbsp;&nbsp;'; 
    echo tep_draw_input_field("aval[span]", $ocertify->login_log_span, 'size="1" maxlength="3"', FALSE, 'text', FALSE);
    echo TEXT_INFO_DELETE_FORMER_DAY . "</td>\n";

    echo '<td class="main">';
    // 按钮显示
    if ($ocertify->npermission >= 15) {
      echo tep_draw_input_field("execute_delete_button", BUTTON_DELETE_LOGINLOG, "onClick=\"return formConfirm('delete', '".$ocertify->npermission."')\"", FALSE, "button", FALSE); 
    }
    echo tep_draw_hidden_field("execute_delete", BUTTON_DELETE_LOGINLOG); 
    echo "</td></tr></table>\n";
    echo "</form>\n";           // form的footer
  }
  if ($oresult) @tep_db_free_result($oresult);          // 开放结果项目

  return TRUE;
}

/* ==============================================
  处理执行函数
 ============================================= */
/*--------------------------------------
  功能: 经过一定时间删除就的认证日志
  参数: 无
  返回值: 删除成功(boolean)
 --------------------------------------*/
function LoginLogDelete_execute() {

  if ( 0 < $GLOBALS['aval']['span']) {
    $sspan_date = date ("Y-m-d H:i:s", mktime (date(H), date(i), date(s),date(m), date(d) - (int)$GLOBALS['aval']['span'], date(Y)));
    $result = tep_db_query("delete from login where logintime < '$sspan_date'");
  }
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
function ip_unlock(ip,num,user){

  $.ajax({
          dataType: "text",
          type:"POST",
          data:"ip="+ip+"&user="+user,
          async:false, 
          url: "ajax_users_log.php?action=ip_unlock",
          success: function(data) {
            if (data == "success") {
              location.href="users_log.php";
            }
          }
  });
}
function formConfirm(type, c_permission) {
  if (type == "delete") {
      rtn = confirm("'. JAVA_SCRIPT_INFO_DELETE . '");
  }
  if (rtn) {
    if (c_permission != 31) {
      $.ajax({
        url: "ajax_orders.php?action=getallpwd",   
        type: "POST",
        dataType: "text",
        data: "current_page_name='.$_SERVER['PHP_SELF'].'", 
        async: false,
        success: function(msg) {
          var tmp_msg_arr = msg.split("|||"); 
          var pwd_list_array = tmp_msg_arr[1].split(",");
          if (tmp_msg_arr[0] == "0") {
            document.forms.users_form.submit(); 
          } else {
            var input_pwd_str = window.prompt("'.JS_TEXT_INPUT_ONETIME_PW.'", ""); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: "ajax_orders.php?action=record_pwd_log",   
                type: "POST",
                dataType: "text",
                data: "current_pwd="+input_pwd_str+"&url_redirect_str="+encodeURIComponent(document.forms.users_form.action),
                async: false,
                success: function(msg_info) {
                  document.forms.users_form.submit(); 
                }
              }); 
            } else {
              alert("'.JS_TEXT_ONETIME_PWD_ERROR.'"); 
            }
          }
        }
      });
    } else {
      document.forms.users_form.submit(); 
    }
  } 
}
//-->
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
  echo '<title>' .HEADING_TITLE . '</title>' . "\n";
  echo '<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">' . "\n";
  putJavaScript_ConfirmMsg();           // 显示确认信息 JavaScript
  echo '<script language="javascript" src="includes/javascript/jquery_include.js"></script>'."\n";
  echo '<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>';
  $belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
  require("includes/note_js.php");
  echo '</head>' . "\n";
  echo '<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">' . "\n";
  if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){
  echo "<script language='javascript'>
    one_time_pwd('".$page_name."', '".(!empty($_SERVER['HTTP_REFERER'])?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT)))."');
      </script>";
  }
  echo '<!-- header //-->' . "\n";
  require(DIR_WS_INCLUDES . 'header.php');
  echo '<!-- header_eof //-->' . "\n";
}

/*--------------------------------------
  功能: 显示页面布局
  参数: $mode(string) 模式（t:上、u:下）
  返回值: 无
 --------------------------------------*/
function PageBodyTable($mode='t') {
  switch ($mode) {
  case 't':
    echo '<!-- body //-->' . "\n";
    echo '<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">' . "\n";
    echo '  <tr>' . "\n";
    echo '    <td width="' . BOX_WIDTH . '" valign="top"><table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">' . "\n";
    break;
  case 'u':
    echo '  </tr>' . "\n";
    echo '</table>' . "\n";
    echo '<!-- body_eof //-->' . "\n";
    break;
  } 
}

/*--------------------------------------
  功能: 显示页面
  参数: $mode(string) 模式（t:上、u:下）
  参数: $stitle(string) body的标题
  参数: $notes_flag(string) note标识 
  返回值: 无
 --------------------------------------*/
function PageBody($mode='t', $stitle = "", $notes_flag='') {
  global $notes;
  if($notes_flag == ''){
    $notes = ''; 
  }
  switch ($mode) {
  case 't':
    echo '<!-- body_text //-->' . "\n";
    echo '    <td width="100%" valign="top"><div class="box_warp">'.$notes.'<table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n";
    echo '      <tr>' . "\n";
    echo '        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">' . "\n";
    echo '          <tr>' . "\n";
    echo '            <td class="pageHeading">' . $stitle . '</td>' . "\n";
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
    echo '    </table></div></td>' . "\n";
    echo '<!-- body_text_eof //-->' . "\n";
    break;
  } 
}

/*--------------------------------------
  功能: 显示页脚
  参数: 无
  返回值: 无
 --------------------------------------*/
function PageFooter() {
  global $ocertify; 
  echo "</table>";
  echo "<!-- footer //-->\n";
  require(DIR_WS_INCLUDES . 'footer.php');
  echo "\n<!-- footer_eof //-->\n";
  echo "<br>\n";
  echo "</body>\n";
  echo "</html>\n";
}

/* *************************************

   用户信息保护画面的程序控制（主要）

 ************************************* */

  require('includes/application_top.php');

  if (isset($execute_delete) && $execute_delete) {    
    // 删除访问日志信息
    LoginLogDelete_execute();
    $lm = 0;
  }

  PageHeader();       // 显示页面头部
  PageBodyTable('t');     // 页面布局表：开始（启动包括导航的表）

  // 显示左侧导航
  echo "<!-- left_navigation //-->\n";    // 
  include_once(DIR_WS_INCLUDES . 'column_left.php');
  echo "\n<!-- left_navigation_eof //-->\n";
  echo "    </table></td>\n";

  // 设置登录状态数组
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

  //IP 
  UserLoginIp_list();
  // 显示画面
  UserLoginLog_list();    // 显示访问日志

  PageBody('u');        // 页面：结束
  PageBodyTable('u');     // 页面布局表：结束

  PageFooter();       // 页脚的显示
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
