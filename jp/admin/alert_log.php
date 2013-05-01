<?php
// 列表显示行数
  define('ONCE_ALERT_LOG_MAX_LINE', '30');
// MySQL 的 limit （其他的DB里有不同的语法所以需要修改）
  define('TABLE_LIMIT_OFFSET', 'limit %s,' . ONCE_ALERT_LOG_MAX_LINE);

/* ===============================================
  global 变量
 ============================================== */
  $TableBorder = 'border="0"';        // 表：线的宽度
  $TableCellspacing = 'cellspacing="0"';    // 表：间距
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
  if (isset($HTTP_POST_VERS['log_id'])) { $log_id = $HTTP_POST_VERS['log_id']; }
        if (isset($_POST['sp'])) { $sp = $_POST['sp']; }
        if (isset($_POST['execute_delete'])) { $execute_delete = $_POST['execute_delete']; }

/* ===============================================
  获取记录 sql 字符串生成函数（Select）
 ============================================== */
/*--------------------------------------
  功能: 提醒日志列表显示
  参数: $oresult(resource) 记录项目
  返回值: 无
 --------------------------------------*/
function show_alert_log_list($oresult) {

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
    if(isset($GLOBALS['log_id'])&&$GLOBALS['log_id']==$arec['id']){
      echo '<tr class="dataTableRowSelected"
        onmouseover="this.style.cursor=\'hand\'"
        onDblClick="document.location.href=\'' .
        tep_href_link(FILENAME_ALERT_LOG,"log_id=".$arec['id'])
        .'\'" >';
    }else if(!$GLOBALS['log_id']&&$rec_c == 1){
      echo '<tr class="dataTableRowSelected"
        onmouseover="this.style.cursor=\'hand\'"
        onDblClick="document.location.href=\'' .
        tep_href_link(FILENAME_ALERT_LOG,"log_id=".$arec['id'])
        .'\'" >';

    }else{
      echo '<tr class="'.$nowColor.'"
        onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'"
        onmouseout="this.className=\'' . $nowColor . '\'"
        onDblClick="document.location.href=\'' .
        tep_href_link(FILENAME_ALERT_LOG,"log_id=".$arec['id'])
        .'\'" >';
    }
    $alert_button_comment = '';
    //根据不同的提醒类型，获取不同的内容
    if($arec['type'] == '0'){

      $alarm_info_query = tep_db_query("select * from ".TABLE_ALARM." where alarm_id='".$arec['from_notice']."'");
      $alarm_info_array = tep_db_fetch_array($alarm_info_query);
      tep_db_free_result($alarm_info_query);

      $alert_user = $alarm_info_array['adminuser'];
      if($alarm_info_array['alarm_flag'] == '1'){
        if($alarm_info_array['orders_flag'] == '1'){
          $alert_button_name = HEADER_TEXT_ALERT_TITLE;
        }else{
          $alert_button_name = HEADER_TEXT_ALERT_TITLE_PREORDERS; 
        }
        $alert_button_comment = $alarm_info_array['title'];
      }else{
        $alert_button_name = NOTICE_ALARM_TITLE; 
      }
      $alert_orders_id = $alarm_info_array['orders_id'];
    }else{
      $micro_info_query = tep_db_query("select * from ".TABLE_MICRO_LOGS." where log_id='".$arec['from_notice']."'");
      $micro_info_array = tep_db_fetch_array($micro_info_query);
      tep_db_free_result($micro_info_query);

      $alert_user = $micro_info_array['author'];
      $alert_button_name = NOTICE_EXTEND_TITLE;
      $alert_orders_id = ''; 
    }

    echo '<td class="main" >' . $alert_user . "</td>\n";
    echo '<td class="main" >' . $alert_button_name . "</td>\n";
    echo '<td class="main" >' . $alert_button_comment . "</td>\n";
    echo '<td class="main" >' . $alert_orders_id . "</td>\n";
    echo '<td class="main" >' . $arec['created_at'] . "</td>\n";

    echo "</tr>\n";
    $rec_c++;
  }
}

/*--------------------------------------
  功能: 页面控制按钮显示
  参数: $nrow(int) 记录件数（列表行数）
  返回值: 记录件数(int)
 --------------------------------------*/
function show_page_ctl($nrow) {
  $c_page = 0;

  // 获取记录总件数
  $alarm_day = get_configuration_by_site_id('ALARM_EXPIRED_DATE_SETTING',0);
  $ssql = "select count(*) as rc from " . TABLE_NOTICE ." where time_format(timediff(now(),created_at),'%H')<".$alarm_day*24;
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // 错误的时候
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }

  $arec = tep_db_fetch_array($oresult);           // 获取记录
  echo tep_draw_hidden_field("lm", $GLOBALS['lm']);     // 把现在的页面放在隐藏项目里

  // 显示按钮
  if ($GLOBALS['lm'] >= ONCE_ALERT_LOG_MAX_LINE) {
    echo tep_draw_input_field("pp", BUTTON_PREVIOUS_PAGE, '', FALSE, "submit", FALSE);  // 前一页
  }
  if ($GLOBALS['lm'] + ONCE_ALERT_LOG_MAX_LINE < $arec['rc']) {
    echo tep_draw_input_field("np", BUTTON_NEXT_PAGE, '', FALSE, "submit", FALSE);    // 后一页
  }

  $page_count = ceil($arec['rc'] / ONCE_ALERT_LOG_MAX_LINE);
  for ($i=1; $i<=$page_count; $i++) {
    $lm_ = ($i-1) * ONCE_ALERT_LOG_MAX_LINE;
    $asp[$i-1]['id'] = $lm_;
    $asp[$i-1]['text'] = $i;
  }
  $GLOBALS['sp'] = $GLOBALS['lm'];              // 设置下拉列表的选择值
  echo tep_draw_pull_down_menu("sp", $asp, $GLOBALS['lm']); // 显示下拉列表
  echo tep_draw_input_field("jp", BUTTON_JUMP_PAGE, '', FALSE, "submit", FALSE);    // 跳转页面

  if ($GLOBALS['lm']) $c_page = ceil((int)$GLOBALS['lm'] / ONCE_ALERT_LOG_MAX_LINE);
  $c_page++;
  echo '<font class="main">&nbsp;&nbsp;' . sprintf(TEXT_PAGE, $c_page,$page_count,$nrow,$arec['rc']) . '</font>' . "\n";
  echo "<br>\n";

}

/*--------------------------------------
  功能: 获取提醒日志信息 sql 字符串生成
  参数: 无
  返回值: 语句字符串(string)
 --------------------------------------*/
function makeSelectOnceAlertLog() {

  $alarm_day = get_configuration_by_site_id('ALARM_EXPIRED_DATE_SETTING',0);
  $s_select = "select * from " . TABLE_NOTICE ." where time_format(timediff(now(),created_at),'%H')<".$alarm_day*24;
  $s_select .= " order by created_at desc";    // 按照提醒日期时间的倒序获取数据
  if (!isset($GLOBALS['lm'])) $GLOBALS['lm'] = 0;
  $s_select .= " " . sprintf(TABLE_LIMIT_OFFSET,$GLOBALS['lm']);

  return $s_select;

}

/* ==============================================
  画面显示函数（主要）
 ============================================= */
/*--------------------------------------
  功能: 提醒日志信息列表显示
  参数: 无
  返回值: 无
 --------------------------------------*/
function UserOnceAlertLog_list() {

  global $ocertify;           // 用户认证项目

  PageBody('t', PAGE_TITLE_MENU_ONCE_PWD_LOG);  // 用户管理画面的标题显示（用户管理菜单）

  // 现在的页面（获取记录开始位置）
  if (isset($GLOBALS['jp']) && $GLOBALS['jp']) $GLOBALS['lm'] = (int)$GLOBALS['sp'];
  if (isset($GLOBALS['pp']) && $GLOBALS['pp']) (int)$GLOBALS['lm'] -= ONCE_ALERT_LOG_MAX_LINE;
  if (isset($GLOBALS['np']) && $GLOBALS['np']) (int)$GLOBALS['lm'] += ONCE_ALERT_LOG_MAX_LINE;

  // 获取提醒日志信息
  $ssql = makeSelectOnceAlertLog();
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // 错误的时候
    echo TEXT_ERRINFO_DB_NO_ONCE_PWD_LOG;            // 显示信息
    if ($oresult) @tep_db_free_result($oresult);      // 开放结果项目
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // 获取记录件数
  if ($nrow > 0) {                      // 取不到记录的时候

    // 表标签的开始
    echo '<table width="100%" ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
    echo "<tr class='dataTableHeadingRow'>\n";
    echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_USERNAME . '</td>' . "\n";      
    echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_TYPE . '</td>' . "\n";
    echo '<td class="dataTableHeadingContent">' .
      TABLE_HEADING_BUTTON_NAME . '</td>' . "\n";       
    echo '<td class="dataTableHeadingContent">' . TABLE_HEADING_ORDERS_ID . '</td>' . "\n"; 
    echo '<td class="dataTableHeadingContent">' .
      TABLE_HEADING_CREATED_AT . '</td>' . "\n";       
    echo "</tr>\n";
    show_alert_log_list($oresult);   
    echo "</table>\n";

    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));    // <form>标签的输出
    show_page_ctl($nrow);       // 页面控制按钮的显示
 
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
  返回值: 删除执行(boolean)
 --------------------------------------*/
function OncePwdLogDelete_execute() {

  if ( 0 < $GLOBALS['aval']['span']) {
    $sspan_date = date ("Y-m-d H:i:s", mktime (date(H), date(i), date(s),date(m), date(d) - (int)$GLOBALS['aval']['span'], date(Y)));

    $alert_query = tep_db_query("select from_notice from ".TABLE_NOTICE." where type='0' and created_at < '$sspan_date'");
    while($alert_array = tep_db_fetch_array($alert_query)){
      tep_db_query("delete from ".TABLE_ALARM." where alarm_id='".$alert_array['from_notice']."'");
    }
    tep_db_free_result($alert_query);

    $alert_query = tep_db_query("select id,from_notice from ".TABLE_NOTICE." where type='1' and created_at < '$sspan_date'");
    while($alert_array = tep_db_fetch_array($alert_query)){
      tep_db_query("delete from micro_logs where log_id='".$alert_array['from_notice']."'");
      tep_db_query("delete from ".TABLE_NOTICE_TO_MICRO_USER." where notice_id = '".$alert_array['id']."'");
      tep_db_query("delete from micro_to_user where micro_id = '".$alert_array['from_notice']."'");
    }
    tep_db_free_result($alert_query);
    $result = tep_db_query("delete from ".TABLE_NOTICE." where created_at < '$sspan_date'");
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
function formConfirm(type) {
  if (type == "delete") {
      rtn = confirm("'. JAVA_SCRIPT_INFO_DELETE . '");
  }
  if (rtn) return true;
  else return false;
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
  echo '<title>' . HEADING_TITLE . '</title>' . "\n";
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
    one_time_pwd('".$page_name."');
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
  返回值: 无
 --------------------------------------*/
function PageBody($mode='t', $stitle = "") {
  global $notes;
  switch ($mode) {
  case 't':
    echo '<!-- body_text //-->' . "\n";
    echo '    <td width="100%" valign="top"><div class="box_warp">'.$notes.'<div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n";
    echo '      <tr>' . "\n";
    echo '        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">' . "\n";
    echo '          <tr>' . "\n";
    echo '            <td class="pageHeading">' . HEADING_TITLE . '</td>' . "\n";
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

/*--------------------------------------
  功能: 显示页脚
  参数: 无
  返回值: 无
 --------------------------------------*/
function PageFooter() {
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
    // 删除提醒日志信息
    OncePwdLogDelete_execute();
    $lm = 0;
  }

  PageHeader();       // 显示页面头部
  PageBodyTable('t');     // 页面布局表：开始（启动包括导航的表）

  //显示左侧导航
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

  // 显示画面
  UserOnceAlertLog_list();    // 显示提醒日志

  PageBody('u');        // 页面：结束
  PageBodyTable('u');     // 页面布局表：结束
  PageFooter();       // 页脚的显示

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
