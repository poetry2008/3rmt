<?php

/* ===============================================
  global 定数
 ============================================== */
// テーブル名
  define('TABLE_USERS', 'users');
  define('TABLE_PERMISSIONS', 'permissions');

/* ===============================================
  global 変数
 ============================================== */
  $TableBorder = 'border="0"';        // テーブル：線の太さ
  $TableCellspacing = 'cellspacing="1"';    // テーブル：セルの間隔
  $TableCellpadding = 'cellpadding="0"';    // テーブル：セルのマージン
  $TableBgcolor = 'bgcolor="#FFFFFF"';    // テーブル：背景色

  $ThBgcolor = 'bgcolor="Gainsboro"';     // ヘッダセル：背景色
  $TdnBgcolor = 'bgcolor="WhiteSmoke"';   // セル：項目名背景色



function checkNotnull($s_val) {

  // 値が入力されているときチェックを行う
  if ($s_val == "") {
    return TEXT_ERRINFO_INPUT_NOINPUT;
  }
  return '';        // 戻り値
}

function checkStringEreg($s_val, $s_ereg = "") {

  // 値が未入力のとき処理終了
  if ($s_val == "") return '';

  // エラー判定
  if ($s_ereg && (ereg($s_ereg,$s_val) == false)) {
    return TEXT_ERRINFO_INPUT_ERR;
  }

  return '';            // 戻り値
}

function checkLength_ge($s_val, $n_len) {

  // 値が未入力のとき処理終了
  if ($s_val == "") return '';

  // エラー判定
  $n_val_len = strlen($s_val);
  if ($n_len > 0 && $n_len > $n_val_len) {
    return sprintf(TEXT_ERRINFO_INPUT_LENGTH, $n_len);
  }

  return '';            // 戻り値
}

function print_err_message($a_error) {

  $stable_bgcolor = 'bgcolor="#FFFFFF"';    // テーブル背景色
  $sfont_color = 'color="#FF0000"';     // フォントカラー（エラー色）

  echo '<font class="main" ' . $sfont_color . '">';
  echo TABLE_HEADING_ERRINFO;   // エラーメッセージ表示タイトル
  echo "<br>\n";

  //-- エラー表示 --
  for ($i = 0 ; $i < count($a_error) ; $i++) {
    echo $a_error[$i];
    echo "<br>\n";
  }

  echo "</font>\n";

}

function set_errmsg_array(&$a_error,$s_errmsg) {

  $a_error[] = $s_errmsg;
}

function makeSelectUserInfo($s_user_ID = "") {

  $s_select = "select * from " . TABLE_USERS;
  $s_select .= ($s_user_ID == "" ? "" : " where userid = '$s_user_ID'");
  $s_select .= " order by userid;";     // ユーザＩＤの順番にデータを取得する
  return $s_select;

}

function makeSelectUserParmission($nmode=0) {

  // ユーザ権限を含む情報取得
  $s_select = "select u.userid as userid, u.name as name";
  $s_select .= " from " . TABLE_USERS . " u, " . TABLE_PERMISSIONS . " p";
  $s_select .= " where u.userid = p.userid";
  if ($nmode == 0) $s_select .= " and p.permission < 15";   // 生成モードにより where 句の条件を編集する
  else $s_select .= " and p.permission = '".$nmode."'";
  $s_select .= " order by u.userid";              // ユーザＩＤの順番にデータを取得する

  return $s_select;

}

function makeInsertUser($aval, $nmode=0) {

  $ssql = "insert into ";
  if ($nmode == 0) {
    // DES で暗号化する
    $cryot_password = (string) crypt($aval['password']);
    // ユーザ管理テーブルへの追加 sql 文字列生成
    $ssql .= TABLE_USERS . " values (";
    $ssql .= "'" . $aval['userid'] . "'";
    $ssql .= ",'$cryot_password'";
    $ssql .= ",'" . $aval['name'] . "'";
    $ssql .= ",'" . $aval['email'] . "'";
    /*
    $ssql .= ",'" . $aval['pwd_is_rand'] . "'";
    */
    $ssql .= ",'" . $aval['rule'] . "'";
    $ssql .= ")";
  } else {
    // ユーザ権限テーブルへの追加 sql 文字列生成
    $ssql .= TABLE_PERMISSIONS . " values (";
    $ssql .= "'" . $aval['userid'] . "'";
    $ssql .= ",7";
    $ssql.=",''";
    $ssql .= ")";
  }
  return $ssql;
}

function makeUpdateUser($aval, $nmode=0) {
  $ssql = "update " . TABLE_USERS . " set";
  if ($nmode == 0) {
    $ssql .= " name='" . $aval['name'] . "'";
    $ssql .= ", email='" . $aval['email'] . "'";
  } else {
    // DES で暗号化する
    $cryot_password = (string) crypt($aval['password']);
    $ssql .= " password='$cryot_password'";
    /*
    $ssql .= ",'" . $aval['pwd_is_rand'] . "'";
    $ssql .= ",'" . $aval['pwd_rules'] . "'";
    */
  }
  $ssql .= ",user_update = '".$_SESSION['user_name']."',date_update = '".date('Y-m-d H:i:s',time())."'  where userid='" . $GLOBALS['userid'] . "'";

  return $ssql;
}

function makeDeleteUser($nmode=0) {

  $ssql = "delete from ";
  if ($nmode == 0) {
    // DES で暗号化する
    $cryot_password = (string) crypt(isset($aval['password'])?$aval['password']:'');
    // ユーザ管理テーブルへの追加 sql 文字列生成
    $ssql .= TABLE_USERS;
  } else {
    // ユーザ権限テーブルへの追加 sql 文字列生成
    $ssql .= TABLE_PERMISSIONS;
  }
  $ssql .= " where userid='" . $GLOBALS['userid'] . "'";

  return $ssql;
}

function UserPassword_preview() {

  PageBody('t', PAGE_TITLE_PASSWORD);   // ユーザ管理画面のタイトル部表示（パスワード変更）

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));              // <form>タグの出力

  $ssql = makeSelectUserInfo($GLOBALS['userslist']);      // ユーザ情報取得
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // メッセージ表示
    echo "<br>\n";
    echo tep_draw_form('users', FILENAME_USERS);            // <form>タグの出力
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // レコード件数の取得
  if ($nrow != 1) {                     // 取得したレコード件数1件でないとき
    echo TEXT_ERRINFO_DB_NO_USER;             // メッセージ表示
    echo "<br>\n";
    echo tep_draw_form('users', FILENAME_USERS);            // <form>タグの出力
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;                     // 処理を抜ける
  }

  $arec = tep_db_fetch_array($oresult);
  if ($oresult) @tep_db_free_result($oresult);    // 結果オブジェクトを開放する

  // テーブルタグの開始
  echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
  echo "<tr>\n";
  // ユーザ名称（ユーザID）
  echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . ' colspan="2" nowrap>' .
    $arec['name'] . "（" . $GLOBALS['userslist'] . '）</td>' . "\n";
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_NEW_PASSWORD . '</td>';    // 新しいパスワード
  // 入力項目出力
  echo '<td>';
  echo tep_draw_password_field("aval[password]", '', TRUE," id='aval_password'");
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_CONFIRM_PASSWORD . '</td>';  // 確認のため再入力
  // 入力項目出力
  echo '<td>';
  echo tep_draw_password_field("aval[chk_password]", '', TRUE," id='aval_chk_password'");
  echo '</td>';
  echo "</tr>\n";
  $users = tep_db_fetch_array(tep_db_query("select * from ".TABLE_USERS." where userid  ='".$GLOBALS['userslist']."'"));
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_USER_ADDED . '</td>';  // 確認のため再入力
  // 入力項目出力
  echo '<td>';
  echo $users['user_added'];
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_DATE_ADDED . '</td>';  // 確認のため再入力
  // 入力項目出力
  echo '<td>';
  echo $users['date_added'];
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_USER_UPDATE . '</td>';  // 確認のため再入力
  // 入力項目出力
  echo '<td>';
  echo $users['user_update'];
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_DATE_UPDATE . '</td>';  // 確認のため再入力
  // 入力項目出力
  echo '<td>';
  echo $users['date_update'];
  echo '</td>';
  echo "</tr>\n";
  echo "</table>\n";

  echo '<br>';

  echo tep_draw_hidden_field("execute_password");         // 処理モードを隠し項目にセットする
  echo tep_draw_hidden_field("userid", $GLOBALS['userslist']);    // ユーザＩＤを隠し項目にセットする
  echo tep_draw_hidden_field("userslist", $GLOBALS['userslist']);    // ユーザＩＤを隠し項目にセットする

  // ボタン表示
  echo tep_draw_input_field("execute_update", BUTTON_CHANGE, "onClick=\"return formConfirm('password')\"", FALSE, "submit", FALSE); // 変更
  echo tep_draw_input_field("clear", BUTTON_CLEAR, '', FALSE, "reset", FALSE);  // クリア
  echo "\n";

  echo "</form>\n";                 // フォームのフッター

  /*
  echo '<a href="' . tep_href_link(FILENAME_USERS) . '">&laquo;&nbsp;' . BUTTON_BACK_MENU . '</a>'; // ユーザ管理メニューに戻る
  */

  return TRUE;
}

function UserInfor_execute() {

  PageBody('t', PAGE_TITLE_USERINFO);   // ユーザ管理画面のタイトル部表示（ユーザ情報）

  // 氏名 の入力チェック
  $ret_err = checkNotnull($GLOBALS['aval']['name']);
  if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NAME . '</b>:' . $ret_err);   // 氏名

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>タグの出力

  if (isset($aerror) && is_array($aerror)) {      // 入力エラーのとき
    print_err_message($aerror);   // エラーメッセージ表示
    echo "<br>\n";
    echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);            // ユーザＩＤを隠し項目にセットする
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";       // フォームのフッター
    return FALSE;
  }

  $ssql = makeUpdateUser($GLOBALS['aval']);         // ユーザ管理テーブルの氏名とE-Maiを更新する sql文字列を取得する
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    echo TEXT_ERRINFO_DB_UPDATE_USER;           // メッセージ表示
    echo "<br>\n";
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
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
  echo TEXT_SUCCESSINFO_UPDATE_USER;    // 完了メッセージ
  echo "<br><br>\n";
  echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
  echo "</form>\n";           // フォームのフッター

  if ($oresult) @tep_db_free_result($oresult);    // 結果オブジェクトを開放する
  if(isset($GLOBALS['letter'])&&$GLOBALS['letter']!=''
      &&isset($GLOBALS['rule'])&&$GLOBALS['rule']!=''){
    update_rules($GLOBALS['userid'],$GLOBALS['rule'],$GLOBALS['letter']);
  }

  return TRUE;
}

function UserPassword_execute() {

  PageBody('t', PAGE_TITLE_PASSWORD);   // ユーザ管理画面のタイトル部表示（パスワード変更）
  // 新しいパスワードの入力チェック
  $ret_err = checkNotnull($GLOBALS['aval']['password']);
  if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NEW_PASSWORD . '</b>:' . $ret_err);
  $ret_err = checkLength_ge($GLOBALS['aval']['password'], 2);
  if ($ret_err == "") $ret_err = checkStringEreg($GLOBALS['aval']['password'], "[[:print:]]");
  if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NEW_PASSWORD . '</b>:' . $ret_err);
  // 確認のため再入力の入力チェック
  if (strcmp($GLOBALS['aval']['password'],$GLOBALS['aval']['chk_password']) != 0)
    set_errmsg_array($aerror, TEXT_ERRINFO_CONFIRM_PASSWORD);

  echo tep_draw_form('users',FILENAME_CHANGEPWD);      // <form>タグの出力

  if (isset($aerror) && is_array($aerror)) {      // 入力エラーのとき
    print_err_message($aerror);   // エラーメッセージ表示
    echo "<br>\n";
    echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);    // ユーザＩＤを隠し項目にセットする
    echo tep_draw_hidden_field('execute_password', $GLOBALS['execute_password']);
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";       // フォームのフッター
    return FALSE;
  }

  $ssql = makeUpdateUser($GLOBALS['aval'], 1);    // ユーザ管理テーブルのパスワードを更新する sql文字列を取得する
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                  // エラーだったとき
    echo TEXT_ERRINFO_DB_CHANGE_PASSWORD;     // メッセージ表示
    echo "<br>\n";
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";               // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);  // 結果オブジェクトを開放する
    return FALSE;
  }

  echo "<br>\n";
  echo "&nbsp;&nbsp;&nbsp;&nbsp;";
  echo "<font size='4' color='red'>";
  echo TEXT_SUCCESSINFO_CHANGE_PASSWORD;    // 完了メッセージ
  echo "</font>";
  echo "<br><br>\n";
/*
  echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
  */
  echo "</form>\n";           // フォームのフッター

  if ($oresult) @tep_db_free_result($oresult);    // 結果オブジェクトを開放する

  return TRUE;

}

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
function PageHeader() {
  global $ocertify,$page_name,$notes;
  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n";
  echo '<html ' . HTML_PARAMS . '>' . "\n";
  echo '<head>' . "\n";
  echo '<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET . '">' . "\n";
  echo '<title>'.HEADING_TITLE.'  </title>' . "\n";
  echo '<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">' . "\n";

  // ユーザ情報、パスワード変更、管理者権限のとき確認メッセージ JavaScript 出力
  if ((isset($GLOBALS['execute_user']) && $GLOBALS['execute_user']) || (isset($GLOBALS['execute_password']) && $GLOBALS['execute_password']) || (isset($GLOBALS['execute_permission']) && $GLOBALS['execute_permission']) ) {
    putJavaScript_ConfirmMsg();           // 確認メッセージを表示する JavaScript
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

  PageHeader();       // ページ・ヘッダの表示
  PageBodyTable('t');     // ページのレイアウトテーブル：開始（ナビゲーションボックスを包括するテーブル開始）

  // 左ナビゲーションボックスの表示
  if($ocertify->npermission >= 10){
  echo "<!-- left_navigation //-->\n";    // 
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

  
// 画面表示、入力チェックＤＢ反映
  if ($ocertify->auth_user&&$change_pwd_flag) {
        // パスワード変更
    if (isset($execute_password) && $execute_password) {
      if (isset($execute_update) && $execute_update){
        UserPassword_execute();  // パスワード変更処理実行
      }else{
        UserPassword_preview();          // パスワード変更ページ表示
      }

    // 管理者権限
    }   
  }

  PageBody('u');        // ページボディの終了
  PageBodyTable('u');     // ページのレイアウトテーブル：終了
  PageFooter();       // ページフッタの表示

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
