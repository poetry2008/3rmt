<?php
/* *********************************************************
  モジュール名: users.php
 * 2001/5/29
 *   modi 2002-05-10
 * Naomi Suzukawa
 * suzukawa@bitscope.co.jp
  ----------------------------------------------------------
ユーザ管理

  ■変更履歴
  2003-04-07 add  $HTTP_POST_VERS に対応させる（PHP スーパーグローバル変数[$_POST]への対応は次回とする）
********************************************************* */

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

/* --------------------------------
2003-04-07 add 
$HTTP_POST_VERS に対応させる
（PHP スーパーグローバル変数[$_POST]への対応は次回とする）
-------------------------------- */
  /*
  if (isset($_POST['userid'])) { $userid = $_POST['userid']; }
  if (isset($_POST['aval'])) { $aval = $_POST['aval']; }
  if (isset($_POST['userslist'])) { $userslist = $_POST['userslist']; }
  if (isset($_POST['no_permission_list'])) { $no_permission_list = $_POST['no_permission_list']; }
  if (isset($_POST['staff_permission_list'])) { $staff_permission_list =
    $_POST['staff_permission_list']; }
  if (isset($_POST['chief_permission_list'])) { $chief_permission_list =
    $_POST['chief_permission_list']; }
  if (isset($_POST['permission_list'])) { $permission_list = $_POST['permission_list']; }
  if (isset($_POST['execute_user'])) { $execute_user = $_POST['execute_user']; }
  if (isset($_POST['execute_password'])) { $execute_password = $_POST['execute_password']; }
  if (isset($_POST['execute_permission'])) { $execute_permission = $_POST['execute_permission']; }
//修改权限
if (isset($_POST['execute_change'])) { $execute_change = $_POST['execute_change'];}
//2003-07-16 hiroshi_sato add 6 lines
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
*/
/* ===============================================
  入力チェック関数
 ============================================== */

/*--------------------------------------
  機  能 : 未入力チェック
  引  数 : $s_val - (i) 値
  戻り値 : "":ＯＫ,エラーメッセージ:ＮＧ
 --------------------------------------*/
function checkNotnull($s_val) {

  // 値が入力されているときチェックを行う
  if ($s_val == "") {
    return TEXT_ERRINFO_INPUT_NOINPUT;
  }
  return '';        // 戻り値
}

/*--------------------------------------
  機  能 : 文字列項目のチェック（正規表現）
       正規表現パターンとの入力チェック（全半角混在）
  引  数 : $s_val   -(i)  文字列. 文字列
       $s_ereg  -(i)  文字列. 正規表現パターン（省略時:正規表現チェックをしない）
  戻り値 : "":ＯＫ,エラーメッセージ:ＮＧ
 -------------------------------------*/
function checkStringEreg($s_val, $s_ereg = "") {

  // 値が未入力のとき処理終了
  if ($s_val == "") return '';

  // エラー判定
  if ($s_ereg && (ereg($s_ereg,$s_val) == false)) {
    return TEXT_ERRINFO_INPUT_ERR;
  }

  return '';            // 戻り値
}

/*--------------------------------------
  機  能 : 文字数チェック
  引  数 : $s_val     -(i)  文字列. 文字列
       $n_len     -(i)  整数. バイト数（省略時:空文字）
  戻り値 : "":ＯＫ,エラーメッセージ:ＮＧ
 -------------------------------------*/
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

/*--------------------------------------
  機  能 : エラーメッセージ表示
  引  数 : $a_error -(i) エラーメッセージ
  戻り値 : なし
 --------------------------------------*/
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

/* -------------------------------------
  機  能 : エラーメッセージ配列にエラーメッセージセット
  引  数 : $a_error - (o) 配列エラーメッセージ
       $s_errmsg - (i) エラーメッセージ
  戻り値 : なし
 ------------------------------------ */
function set_errmsg_array(&$a_error,$s_errmsg) {

  $a_error[] = $s_errmsg;
}

/* ===============================================
  レコード取得 sql 文字列生成関数（Select）
 ============================================== */
/*--------------------------------------
  機  能 : ユーザ情報取得 sql 文字列生成
  引  数 : $s_user_ID - (i) ユーザＩＤ（省略可）
  戻り値 : select 句文字列
 --------------------------------------*/
function makeSelectUserInfo($s_user_ID = "") {

  $s_select = "select * from " . TABLE_USERS;
  $s_select .= ($s_user_ID == "" ? "" : " where userid = '$s_user_ID'");
  $s_select .= " order by userid;";     // ユーザＩＤの順番にデータを取得する
  return $s_select;

}

/*--------------------------------------
  機  能 : ユーザ権限を含む情報取得 sql 文字列生成
  引  数 : $nmode   - (i) 整数：生成モード（0:一般ユーザ取得[既定値]、1:管理者取得）
  戻り値 : select 句文字列
 --------------------------------------*/
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

/* ==============================================
  テーブル更新 sql 文字列生成関数（Insert、Update、Delete）
 ============================================= */
/*--------------------------------------
  機  能 : 新規ユーザの登録（ユーザ管理、ユーザ権限テーブルに追加登録）
  引  数 : $aval    -(i)  連想配列：追加するデータ
       $nmode   -(i)  整数：生成モード（0:ユーザ管理テーブル追加sql[既定値]、1:ユーザ権限テーブル追加sql）
  戻り値 : なし
 --------------------------------------*/
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
    $ssql .= ",'" . $aval['pwd_rules'] . "'";
    */
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

/*--------------------------------------
  機  能 : ユーザ情報テーブルの更新
  引  数 : $aval    -(i)  連想配列：更新するデータ
       $nmode   -(i)  更新モード（0:氏名、e-mail、1:パスワード）
  戻り値 : なし
 --------------------------------------*/
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
  $ssql .= " where userid='" . $GLOBALS['userid'] . "'";

  return $ssql;
}

/*--------------------------------------
  機  能 : ユーザの削除、（ユーザ管理、ユーザ権限テーブルから削除）
  引  数 :  $nmode  -(i)  整数：生成モード（0:ユーザ管理テーブル削除sql[既定値]、1:ユーザ権限テーブル削除sql）
  戻り値 : なし
 --------------------------------------*/
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

/*--------------------------------------
  機  能 : ユーザ権限テーブルの更新
  引  数 : $nmode   -(i) 更新モード（0:grant、1:revoke）
       $susers  -(i) ユーザID
  戻り値 : なし
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
  /*
  if ($nmode == 0)            // 権限を与える
    $ssql .= " permission=15";
  else $ssql .= " permission=7";      // 権限を取消す
  */
  $ssql .= " where userid='$susers'";

  return $ssql;

}

/* ==============================================
  画面表示関数（メイン）
 ============================================= */
/*--------------------------------------
  機  能 : ユーザ管理メニュー（表示）
  引  数 : なし
  戻り値 : なし
 --------------------------------------*/
function UserManu_preview() {

  global $ocertify;           // ユーザ認証オブジェクト

  PageBody('t', PAGE_TITLE_MENU_USER);      // ユーザ管理画面のタイトル部表示（ユーザ管理メニュー）

  // ユーザ情報取得
  if ($ocertify->npermission < 15) $ssql = makeSelectUserInfo($ocertify->auth_user);    // 一般ユーザのとき
  if ($ocertify->npermission == 15) $ssql = makeSelectUserInfo();   // 管理者のとき

  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // メッセージ表示
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));            // <form>タグの出力
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);                  // レコード件数の取得
  // テーブルタグの開始
  echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
  echo "<tr>\n";
  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));    // <form>タグの出力

  if ($nrow == 1) {                         // 対象データが1件だったとき
    // 項目タイトル（1セル）の出力
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_USER . '</td>' . "\n";   // ユーザ
    $nLsize = 'size="1"';                     // リストのサイズ変数に1をセット
  } elseif ($nrow > 1) {                        // 対象データが1件以上だったとき
    // 項目タイトル（1セル）の出力
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_USER_LIST . '</td>' . "\n";  // ユーザ一覧
    $nLsize = 'size="5"';                     // リストのサイズ変数に5をセット
  }
  echo "</tr>\n";

  // リストボックスに表示するデータを配列にセットする
  $i=0;
  while ($arec = tep_db_fetch_array($oresult)) {      // レコードを取得
    $ausers[$i]['id'] = $arec['userid'];
    $ausers[$i]['text'] = $arec['name'];
    $i++;
  }

  echo '<tr><td>';                          // データセル
  echo tep_draw_pull_down_menu("userslist", $ausers, $ocertify->auth_user, $nLsize);  // リストボックスの表示
  echo "</td></tr>\n";
  echo "</table>\n";

  echo '<br>';

  // ボタン表示
  if ($ocertify->npermission == 15) {     // 管理者のとき
    echo tep_draw_input_field("execute_new", BUTTON_INSERT_USER, '', FALSE, "submit", FALSE); // ユーザの追加
    echo tep_draw_input_field("execute_user", BUTTON_INFO_USER, '', FALSE, "submit", FALSE);  // ユーザ情報
    echo tep_draw_input_field("execute_password", BUTTON_CHANGE_PASSWORD, '', FALSE, "submit", FALSE);  // パスワード変更
    echo tep_draw_input_field("execute_permission", BUTTON_PERMISSION, '', FALSE, "submit", FALSE); // 管理者権限
 echo tep_draw_input_field("execute_change",BUTTON_CHANGE_PERMISSION , '', FALSE, "submit", FALSE);
    echo "\n";
  } else {
    echo tep_draw_input_field("execute_user", BUTTON_INFO_USER, '', FALSE, "submit", FALSE);  // ユーザ情報
    echo tep_draw_input_field("execute_password", BUTTON_CHANGE_PASSWORD, '', FALSE, "submit", FALSE);  // パスワード変更
  }

  echo "</form>\n";           // フォームのフッター

  if ($oresult) @tep_db_free_result($oresult);          // 結果オブジェクトを開放する

  return TRUE;
}

/*--------------------------------------
  機  能 : ユーザの追加（表示メイン）
  引  数 : なし
  戻り値 : なし
 --------------------------------------*/
function UserInsert_preview() {

  PageBody('t', BUTTON_INSERT_USER);    // ユーザ管理画面のタイトル部表示（ユーザの追加）

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));              // <form>タグの出力

  // テーブルタグの開始
  echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor']. '>' . "\n";
  echo "<tr>\n";
  // 項目タイトル（1セル）の出力
  echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_COLUMN . '</td>' . "\n"; // カラム
  echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_DATA . '</td>' . "\n"; // データ
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_USER_ID . '</td>';   // ユーザID
  // 入力項目出力
  echo '<td>';
  echo tep_draw_input_field("aval[userid]", '', 'size="18" maxlength="16"', TRUE, 'text', FALSE);
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_PASSWORD . '</td>';    // パスワード
  // 入力項目出力
  echo '<td>';
  echo tep_draw_password_field("aval[password]", '', TRUE);
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_NAME . '</td>';    // 氏名
  // 入力項目出力
  echo '<td>';
  echo tep_draw_input_field("aval[name]", '', 'size="32" maxlength="64"', TRUE, 'text', FALSE);
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_EMAIL . '</td>';   // E-Mail
  // 入力項目出力
  echo '<td>';
  echo tep_draw_input_field("aval[email]", '', 'size="32" maxlength="96"', FALSE, 'text', FALSE);
  echo '</td>';
  echo "</tr>\n";

  /*
  //pwd pwd_is_rand 
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_IS_RAND_PWD . '</td>'; 
  echo '<td>';
  echo  tep_draw_selection_field('aval[pwd_is_rand]', 'radio', '1', $arec['pwd_is_arnd']);
  echo "ON";
  echo  tep_draw_selection_field('aval[pwd_is_rand]', 'radio', '0', !$arec['pwd_is_rand']);
  echo "OFF";
  echo '</td>';
  echo "</tr>\n";

  //pwd rules
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_PWD_RULES . '</td>';   
  echo '<td>';
  echo tep_draw_input_field("aval['pwd_rules']", '', 'size="32" maxlength="64"',
      FALSE, 'text', FALSE);
  echo '</td>';
  echo "</tr>\n";
  */



  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' .  TABLE_HEADING_IP_LIMIT . '</td>';
  echo '<td>';
  echo tep_draw_textarea_field('ip_limit', false, 20, 5); 
  echo '</td>';
  echo "</tr>\n";

  echo "</table>\n";

  echo '<br>';

  echo tep_draw_hidden_field("execute_new");        // 処理モードを隠し項目にセットする

  // ボタン表示
  echo tep_draw_input_field("execute_insert", BUTTON_INSERT, '', FALSE, "submit", FALSE);   // 追加
  echo tep_draw_input_field("clear", BUTTON_CLEAR, '', FALSE, "reset", FALSE);        // クリア

  echo "</form>\n";           // フォームのフッター

  // ユーザ管理メニューに戻る
  echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) . '">&laquo;&nbsp;' . BUTTON_BACK_MENU . '</a>'; // ユーザ管理メニューに戻る

  return TRUE;

}

/*--------------------------------------
  機  能 : ユーザ情報保守（表示メイン）
  引  数 : なし
  戻り値 : なし

2000.04.20 対象ユーザが存在しないとき、メッセージ表示するように変更する。

 --------------------------------------*/
function UserInfo_preview() {

  global $ocertify;           // ユーザ認証オブジェクト

  PageBody('t', BUTTON_INFO_USER);    // ユーザ管理画面のタイトル部表示（ユーザ情報）

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));        // <form>タグの出力

  $ssql = makeSelectUserInfo($GLOBALS['userslist']);      // ユーザ情報取得
  if(isset($GLOBALS['aval']['name'])&&$GLOBALS['aval']['name']!='')
  {
    $ssql = makeSelectUserInfo($_POST['aval']['name']);
  }
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // メッセージ表示
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));            // <form>タグの出力
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // レコード件数の取得
  if ($nrow != 1) {                     // 取得したレコード件数1件でないとき
    echo TEXT_ERRINFO_DB_NO_USER;             // メッセージ表示
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));            // <form>タグの出力
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;                     // 処理を抜ける
  }

  $arec = tep_db_fetch_array($oresult);
  if ($oresult) @tep_db_free_result($oresult);        // 結果オブジェクトを開放する

  // テーブルタグの開始
  echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
  echo "<tr><td><table>\n";
  echo "<tr>\n";
  // ユーザ名称（ユーザID）
  echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . ' colspan="2" nowrap>' .
    $arec['name'] . "（" . $_POST['userslist'] . '）</td>' . "\n";
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_NAME . '</td>';    // 氏名
  echo '<td>';
  echo tep_draw_input_field("aval[name]", $arec['name'], 'size="32" maxlength="64"', TRUE, 'text', FALSE);
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_EMAIL . '</td>';   // E-Mail
  // 入力項目出力
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

  //设置秘密
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
  $h_rule = get_rule();
  echo make_rand_pwd($h_rule)?make_rand_pwd($h_rule):TEXT_ERROR_RULE;
  echo "</td>\n";
  echo "</tr>\n";
  //规则
  echo "<tr>\n";
  echo "<td>\n";
  echo TEXT_RAND_RULES;
  echo "</td>\n";
  echo "<td>\n";
  $rule = get_rule()?get_rule():'';
  echo tep_draw_input_field("config_rules", $rule, 'size="32" maxlength="64"', FALSE, 'text', FALSE);
  echo "</td>\n";
  echo "</tr>\n";
  echo "<tr>\n";
  echo "<td colspan='2' align='center'>\n";
  echo tep_draw_input_field("execute_user", MAKE_PWD, '', FALSE, "submit", FALSE);  // ユーザ情報
  echo tep_draw_input_field("reset", RESET_PWD, '', FALSE, "reset", FALSE);  // 元の値に戻す
  echo "</td>\n";
  echo "</tr>\n";
  echo "</table></td><td valign='top'>".TEXT_RAND_PWD_INFO."</td></tr>\n";
  echo "</table>\n";

  echo tep_draw_hidden_field("execute_user");           // 処理モードを隠し項目にセットする
  echo tep_draw_hidden_field("userid", $_POST['userslist']);    // ユーザＩＤを隠し項目にセットする

  echo '<br>';

  // ボタン表示
  echo tep_draw_input_field("execute_update", BUTTON_UPDATE, "onClick=\"return formConfirm('update')\"", FALSE, "submit", FALSE); // 更新

  // 管理者のとき、削除ボタンを表示する
  if ($ocertify->npermission == 15) 
    echo tep_draw_input_field("execute_delete", BUTTON_DELETE, "onClick=\"return formConfirm('delete')\"", FALSE, "submit", FALSE); // 削除

  echo tep_draw_input_field("reset", BUTTON_RESET, '', FALSE, "reset", FALSE);  // 元の値に戻す
  echo "\n";

  echo "</form>\n";                 // フォームのフッター

  echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) . '">&laquo;&nbsp;' . BUTTON_BACK_MENU . '</a>'; // ユーザ管理メニューに戻る

  return TRUE;
}


//修改用户管理网站的权限
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

  //  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>

  echo
    tep_draw_form('users',basename($GLOBALS['PHP_SELF']),'execute_change=change');
  /*
  echo '<form name="users" action="'.HTTP_SERVER.'/admin/users.php?execute_change=change" method="post">';
  */
  echo "<table>";
  echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
    echo "<tr>\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . 'ユーザー' . '</td>' . "\n"; 
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . "サイト権限" . '</td>' . "\n";
    echo "</tr>\n";
while($userslist= tep_db_fetch_array($result)){
  /*
  if($userslist['userid']=='admin'){//admin 用户 不显示 默认拥有所有权限
  }else{
  */
    echo "<tr><td>";
    echo $userslist['userid'];//输出用户名
   echo "</td><td>";
   $user_id=$userslist['userid'];
   $u_s_arr=array();
   if($userslist['site_permission']){
     $u_s_arr = explode(",",$userslist['site_permission']);//site_permission转为数组 exp:(1,6=>([0]=>1,[1]=>6 )
   }else{
     //$u_s_arr[]="";
   }   
   //设置ALL的修改权限 并设置 admin 默认选择
     $site_str=  '<input name="'.$user_id.'[]" type="checkbox" id="0" value="0" ';
     if((is_array($u_s_arr)&&in_array( 0,$u_s_arr))||
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
 // }
}
echo "</table>";
//点击执行onclick 弹出y/n对话框
    echo tep_draw_input_field("execute_update", BUTTON_CHANGE, "onClick=\"return formConfirm('c_permission')\"", FALSE, "submit", FALSE); // 変更


echo ' </form>';
 echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) . '">&laquo;&nbsp;' . BUTTON_BACK_MENU . '</a>'; 
}
//修改用户管理网站的权限的执行方法
function  ChangePermission_execute(){
  $y_n=true;
  PageBody('t', PAGE_TITLE_CHANGE_PERMISSION); 
  $sql=" SELECT * FROM `permissions` ";
  $result =tep_db_query($sql);  //获取用户的权限 （所有用户）
while($userslist= tep_db_fetch_array($result)){
  if($_POST[$userslist['userid']]){//获取页面 checkbox的值(数组)
    $u_s_id=$_POST[$userslist['userid']];
$u_id_str=implode(",",$u_s_id);
//修改permission中 对应的userid的 site_permission
$permission_sid_sql="UPDATE ".TABLE_PERMISSIONS." SET `site_permission` = '".$u_id_str."' WHERE `permissions`.`userid` = '".$userslist['userid']. "' ";

if(tep_db_query($permission_sid_sql)){
}else{ $y_n= FALSE;}
}
}
  if($y_n) {
    echo   TEXT_SUCCESSINFO_CHANGE_PERMISSION."<br>";//修改成功  输出成功语句

   }
  else {echo TEXT_ERRINFO_DB_CHANGE_PERMISSION."<BR>";
   }
   echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) . '">&laquo;&nbsp;' . BUTTON_BACK_MENU . '</a>'; 
}

/*--------------------------------------
  機  能 : パスワード変更（表示メイン）
  引  数 : なし
  戻り値 : なし
 --------------------------------------*/
function UserPassword_preview() {

  PageBody('t', PAGE_TITLE_PASSWORD);   // ユーザ管理画面のタイトル部表示（パスワード変更）

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));              // <form>タグの出力

  $ssql = makeSelectUserInfo($GLOBALS['userslist']);      // ユーザ情報取得
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // メッセージ表示
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));            // <form>タグの出力
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // レコード件数の取得
  if ($nrow != 1) {                     // 取得したレコード件数1件でないとき
    echo TEXT_ERRINFO_DB_NO_USER;             // メッセージ表示
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));            // <form>タグの出力
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
  echo tep_draw_password_field("aval[password]", '', TRUE);
  echo '</td>';
  echo "</tr>\n";

  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TABLE_HEADING_CONFIRM_PASSWORD . '</td>';  // 確認のため再入力
  // 入力項目出力
  echo '<td>';
  echo tep_draw_password_field("aval[chk_password]", '', TRUE);
  echo '</td>';
  echo "</tr>\n";

  /*
  //pwd pwd_is_rand 
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_IS_RAND_PWD . '</td>'; 
  echo '<td>';
  echo  tep_draw_selection_field('aval[pwd_is_rand]', 'radio', '1', $arec['pwd_is_arnd']);
  echo "ON";
  echo  tep_draw_selection_field('aval[pwd_is_rand]', 'radio', '0', !$arec['pwd_is_rand']);
  echo "OFF";
  echo '</td>';
  echo "</tr>\n";

  //pwd rules
  echo "<tr>\n";
  echo '<td class="main" ' . $GLOBALS['TdnBgcolor'] . ' nowrap>' . TEXT_PWD_RULES . '</td>';   
  echo '<td>';
  echo tep_draw_input_field("aval['pwd_rules']", '', 'size="32" maxlength="64"',
      FALSE, 'text', FALSE);
  echo '</td>';
  echo "</tr>\n";
  */
  

  echo "</table>\n";

  echo '<br>';

  echo tep_draw_hidden_field("execute_password");         // 処理モードを隠し項目にセットする
  echo tep_draw_hidden_field("userid", $GLOBALS['userslist']);    // ユーザＩＤを隠し項目にセットする

  // ボタン表示
  echo tep_draw_input_field("execute_update", BUTTON_CHANGE, "onClick=\"return formConfirm('password')\"", FALSE, "submit", FALSE); // 変更
  echo tep_draw_input_field("clear", BUTTON_CLEAR, '', FALSE, "reset", FALSE);  // クリア
  echo "\n";

  echo "</form>\n";                 // フォームのフッター

  echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) . '">&laquo;&nbsp;' . BUTTON_BACK_MENU . '</a>'; // ユーザ管理メニューに戻る

  return TRUE;
}

/*--------------------------------------
  機  能 : 管理者権限（表示メイン）
  引  数 : なし
  戻り値 : なし
 --------------------------------------*/
function UserPermission_preview() {

  PageBody('t', PAGE_TITLE_PERMISSION);   // ユーザ管理画面のタイトル部表示（管理者権限）

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));  // <form>タグの出力

  // 一般ユーザ情報取得
  $ssql = makeSelectUserParmission('7');             // 一般ユーザのデータを取得する sql 文字列生成

  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // メッセージ表示
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>タグの出力
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // レコード件数の取得
  if ($nrow > 0) {
    // リストボックスに表示するデータを配列にセットする
    $i=0;
    while ($arec = tep_db_fetch_array($oresult)) {      // レコードを取得
      $ausers[$i]['id'] = $arec['userid'];
      $ausers[$i]['text'] = $arec['name'];
      $i++;
    }
  }
  if ($oresult) @tep_db_free_result($oresult);        // 結果オブジェクトを開放する
  //chief start
  $ssql = makeSelectUserParmission('10');             // 一般ユーザのデータを取得する sql 文字列生成

  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // メッセージ表示
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>タグの出力
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // レコード件数の取得
  if ($nrow > 0) {
    // リストボックスに表示するデータを配列にセットする
    $i=0;
    while ($arec = tep_db_fetch_array($oresult)) {      // レコードを取得
      $ausers_chief[$i]['id'] = $arec['userid'];
      $ausers_chief[$i]['text'] = $arec['name'];
      $i++;
    }
  }
  if ($oresult) @tep_db_free_result($oresult);        // 結果オブジェクトを開放する

  //chief end


  // 管理者権限を持つユーザ情報取得
  $ssql = makeSelectUserParmission('15');            // 管理者権限を持つデータを取得する sql 文字列生成

  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    echo TEXT_ERRINFO_DB_NO_USERINFO;           // メッセージ表示
    echo "<br>\n";
    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>タグの出力
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // レコード件数の取得
  if ($nrow > 0) {
    // リストボックスに表示するデータを配列にセットする
    $i=0;
    while ($arec = tep_db_fetch_array($oresult)) {    // レコードを取得
      $ausers_admin[$i]['id'] = $arec['userid'];
      $ausers_admin[$i]['text'] = $arec['name'];
      $i++;
    }
  }

  if ($oresult) @tep_db_free_result($oresult);          // 結果オブジェクトを開放する

  // テーブルタグの開始
  echo '<table border="0" gbcolor="#FFFFFF" cellpadding="5" cellspacing="0">' . "\n";
  echo "<tr>\n";
  echo "<td>\n";                  // データセル

    // テーブルタグの開始（一般ユーザのリストボックス）
    echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
    echo "<tr>\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' .
      TABLE_HEADING_USER_STAFF . '</td>' . "\n"; // 一般ユーザ
    echo "</tr>\n";

    echo "<td>\n";                  // データセル
    echo tep_draw_pull_down_menu("staff_permission_list", $ausers, '', 'size="5"');  // リストボックスの表示
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

  echo "</td>\n";
  echo '<td align="center" valign="middle">' . "\n";                  // データセル

    echo '<br>';
    echo tep_draw_input_field("execute_staff2chief", BUTTON_GRANT,  "onClick=\"return formConfirm('staff2chief')\"", FALSE, "submit", FALSE);  // 権限を与える >>
    echo '<br>';
    echo tep_draw_input_field("execute_chief2staff", BUTTON_REVOKE, "onClick=\"return formConfirm('chief2staff')\"", FALSE, "submit", FALSE);  // << 権限を取消す

  echo "</td>\n";
  echo "<td>\n";                  // データセル

  //chief show start

    // テーブルタグの開始（一般ユーザのリストボックス）
    echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
    echo "<tr>\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' .
      TABLE_HEADING_USER_CHIEF . '</td>' . "\n"; // 一般ユーザ
    echo "</tr>\n";

    echo "<td>\n";                  // データセル
    echo tep_draw_pull_down_menu("chief_permission_list", $ausers_chief, '', 'size="5"');  // リストボックスの表示
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

  echo "</td>\n";
  echo '<td align="center" valign="middle">' . "\n";                  // データセル

    echo '<br>';
    echo tep_draw_input_field("execute_chief2admin", BUTTON_GRANT, "onClick=\"return formConfirm('chief2admin')\"", FALSE, "submit", FALSE);  // 権限を与える >>
    echo '<br>';
    echo tep_draw_input_field("execute_admin2chief", BUTTON_REVOKE, "onClick=\"return formConfirm('admin2chief')\"", FALSE, "submit", FALSE);  // << 権限を取消す

  echo "</td>\n";
  echo "<td>\n";                  // データセル



  //chief show end

    // テーブルタグの開始（管理権限を持っているユーザのリストボックス）
    echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
    echo "<tr>\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' .
      TABLE_HEADING_USER_ADMIN . '</td>' . "\n";    // サイト管理者
    echo "</tr>\n";

    echo "<td>\n";                  // データセル
    echo tep_draw_pull_down_menu("permission_list", $ausers_admin, '', 'size="5"'); // リストボックスの表示
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";

  echo "</td>\n";
  echo "</tr>\n";
  echo "</table>\n";

  echo tep_draw_hidden_field("execute_permission");       // 処理モードを隠し項目にセットする

  echo "</form>\n";           // フォームのフッター

  echo '<a href="' . tep_href_link(basename($GLOBALS['PHP_SELF'])) . '">&laquo;&nbsp;' . BUTTON_BACK_MENU . '</a>'; // ユーザ管理メニューに戻る

  return TRUE;
}

/* ==============================================
  処理実行関数
 ============================================= */
/*--------------------------------------
  機  能 : ユーザの追加処理実行
  引  数 : なし
  戻り値 : true/false
  補  足 : [:print:] 印字可能なキャラクタ(=制御文字以外のキャラクタ) 
 --------------------------------------*/
function UserInsert_execute() {

  PageBody('t', PAGE_TITLE_INSERT_USER);    // ユーザ管理画面のタイトル部表示（ユーザの追加）

  // ユーザID の入力チェック
  $aerror = "";
  $ret_err = checkLength_ge($GLOBALS['aval']['userid'], 2);
  if ($ret_err == "") $ret_err = checkNotnull($GLOBALS['aval']['userid']);
  if ($ret_err == "") $ret_err = checkStringEreg($GLOBALS['aval']['userid'], "[[:print:]]");
  if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_USER_ID . '</b>:' . $ret_err);  // ユーザID

  // パスワードの入力チェック
  $ret_err = checkLength_ge($GLOBALS['aval']['password'], 2);
  if ($ret_err == "") $ret_err = checkNotnull($GLOBALS['aval']['password']);
  if ($ret_err == "") $ret_err = checkStringEreg($GLOBALS['aval']['password'], "[[:print:]]");
  if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_PASSWORD . '</b>:' . $ret_err); // パスワード

  // 氏名 の入力チェック
  $ret_err = checkNotnull($GLOBALS['aval']['name']);
  if ($ret_err != "") set_errmsg_array($aerror, '<b>' . TABLE_HEADING_NAME . '</b>:' . $ret_err);   // 氏名

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>タグの出力

  if (is_array($aerror)) {      // 入力エラーのとき
    print_err_message($aerror);   // エラーメッセージ表示
    echo "<br>\n";
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";       // フォームのフッター
    return FALSE;
  }

  // 追加するデータが登録されていないかチェックする
  $ssql = makeSelectUserInfo($GLOBALS['aval']['userid']);   // ユーザ情報取得
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    echo TEXT_ERRINFO_DB_USERCHACK;             // メッセージ表示
    echo "<br>\n";
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // レコード件数の取得
  if ($nrow >= 1) {                     // 取得したレコード件数0件でないとき
    echo TEXT_ERRINFO_DB_EXISTING_USER;           // メッセージ表示
    echo "<br>\n";
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;                     // 処理を抜ける
  }

  $ssql = makeInsertUser($GLOBALS['aval']);         // ユーザ管理テーブルの追加sql文字列を取得する
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    echo TEXT_ERRINFO_DB_INSERT_USER;           // メッセージ表示
    echo "<br>\n";
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;
  }

  $ssql = makeInsertUser($GLOBALS['aval'], 1);        // ユーザ権限テーブルの追加sql文字列を取得する
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    echo TEXT_ERRINFO_DB_INSERT_PERMISSION;         // メッセージ表示
    echo "<br>\n";
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                   // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;
  }
  //var_dump($GLOBALS['ip_limit']);
  //var_dump(!empty($GLOBALS['ip_limit']));
  //var_dump(explode("\n", $GLOBALS['ip_limit']));
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
  echo TEXT_SUCCESSINFO_INSERT_USER;    // 完了メッセージ
  echo '<br><br>';
  echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
  echo "</form>\n";           // フォームのフッター

  if ($oresult) @tep_db_free_result($oresult);    // 結果オブジェクトを開放する

  return TRUE;
}
/*--------------------------------------
  機  能 : ユーザ情報の更新処理実行
  引  数 : なし
  戻り値 : true/false
  補  足 : [:print:] 印字可能なキャラクタ(=制御文字以外のキャラクタ) 
 --------------------------------------*/
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
  if(isset($_POST['config_rules'])&&$_POST['config_rules']!=''){
    update_rules($_POST['config_rules']);
  }

  return TRUE;
}

/*--------------------------------------
  機  能 : ユーザ削除チェック
  引  数 : なし
  戻り値 : true/false
 --------------------------------------*/
function UserDelete_execute() {

  global $ocertify;           // ユーザ認証オブジェクト

  PageBody('t', PAGE_TITLE_USERINFO);   // ユーザ管理画面のタイトル部表示（ユーザ情報）

  if (strcmp($GLOBALS['userid'],$ocertify->auth_user) == 0)
    set_errmsg_array($aerror, TEXT_ERRINFO_USER_DELETE);      // 本人の情報を削除はエラー

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));    // <form>タグの出力

  if (isset($aerror)&&is_array($aerror)) {      // 入力エラーのとき
    print_err_message($aerror);   // エラーメッセージ表示
    echo "<br>\n";
    echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);  // ユーザＩＤを隠し項目にセットする
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";       // フォームのフッター
    return FALSE;
  }

  $ssql = makeDeleteUser(1);              // ユーザ権限テーブルから対象ユーザを削除する sql文字列を取得する
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                  // エラーだったとき
    echo TEXT_ERRINFO_DB_DELETE_USER;       // メッセージ表示
    echo "<br>\n";
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";               // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);  // 結果オブジェクトを開放する
    return FALSE;
  }

  $ssql = makeDeleteUser();             // ユーザ管理テーブルから対象ユーザを削除する sql文字列を取得する
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                  // エラーだったとき
    echo TEXT_ERRINFO_DB_DELETE_USER;       // メッセージ表示
    echo "<br>\n";
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";               // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);  // 結果オブジェクトを開放する
    return FALSE;
  }
  tep_db_query("delete from user_ip where userid = '".$GLOBALS['userid']."'");

  echo "<br>\n";
  echo TEXT_SUCCESSINFO_DELETE_USER;          // 完了メッセージ
  echo "<br><br>\n";
  echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);    // ユーザ管理メニューに戻る
  echo "</form>\n";                 // フォームのフッター

  if ($oresult) @tep_db_free_result($oresult);    // 結果オブジェクトを開放する

  return TRUE;

}

/*--------------------------------------
  機  能 : パスワード変更処理実行
  引  数 : なし
  戻り値 : true/false
  補  足 : [:print:] 印字可能なキャラクタ(=制御文字以外のキャラクタ) 
 --------------------------------------*/
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

  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));      // <form>タグの出力

  if (isset($aerror) && is_array($aerror)) {      // 入力エラーのとき
    print_err_message($aerror);   // エラーメッセージ表示
    echo "<br>\n";
    echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);    // ユーザＩＤを隠し項目にセットする
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
  echo TEXT_SUCCESSINFO_CHANGE_PASSWORD;    // 完了メッセージ
  echo "<br><br>\n";
  echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
  echo "</form>\n";           // フォームのフッター

  if ($oresult) @tep_db_free_result($oresult);    // 結果オブジェクトを開放する

  return TRUE;

}

/*--------------------------------------
  機  能 : ユーザ権限選択チェック
  引  数 : $nmode - (i) 更新モード（0:grant、1:revoke）
  戻り値 : true/false
 --------------------------------------*/
function UserPermission_execute($nmode=0) {

  global $ocertify;           // ユーザ認証オブジェクト

  PageBody('t', PAGE_TITLE_PERMISSION);   // ユーザ管理画面のタイトル部表示（管理者権限）

  /*
  if ($nmode == 0) {    // 権限を与える処理：ユーザが選択されていない
    $suserid = $GLOBALS['no_permission_list'];
    if ($suserid == "") set_errmsg_array($aerror, TEXT_ERRINFO_USER_GRANT);
  } else {        // 権限を取消す処理：ユーザが選択されていない
    $suserid = $GLOBALS['permission_list'];
    if ($suserid == "") set_errmsg_array($aerror, TEXT_ERRINFO_USER_REVOKE);
  }
  // 権限を取消す処理：ユーザ本人のとき
  if ($nmode == 1 && strcmp($suserid,$ocertify->auth_user) == 0) 
      set_errmsg_array($aerror, TEXT_ERRINFO_USER_REVOKE_ONESELF);
  */
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
  echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));  // <form>タグの出力

  if (is_array($aerror)) {                    // 入力エラーのとき
    print_err_message($aerror);                 // エラーメッセージ表示
    echo "<br>\n";
    echo tep_draw_hidden_field('userslist', $GLOBALS['userid']);            // ユーザＩＤを隠し項目にセットする
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                     // フォームのフッター
    return FALSE;
  }

  $ssql = makeUpdatePermission($nmode, $suserid);         // ユーザ権限テーブルの権限を更新する sql文字列を取得する
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                        // エラーだったとき
    echo TEXT_ERRINFO_DB_CHANGE_USER;             // メッセージ表示
    echo "<br>\n";
    echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);  // ユーザ管理メニューに戻る
    echo "</form>\n";                     // フォームのフッター
    if ($oresult) @tep_db_free_result($oresult);        // 結果オブジェクトを開放する
    return FALSE;
  }

  printf(TEXT_SUCCESSINFO_PERMISSION, ($nmode == 0 ? '与え' : '取消し'));
  echo "<br><br>\n";
  echo tep_draw_input_field("execute_permission", BUTTON_BACK_PERMISSION, '', FALSE, "submit", FALSE);  // 管理者権限に戻る
  echo tep_draw_input_field("back", BUTTON_BACK_MENU, '', FALSE, "submit", FALSE);            // ユーザ管理メニューに戻る
  echo "</form>\n";                 // フォームのフッター

  if ($oresult) @tep_db_free_result($oresult);    // 結果オブジェクトを開放する

  return TRUE;

}

/*--------------------------------------
  機  能 : 確認メッセージのための JavaScript
  引  数 : なし
  戻り値 : true/false
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
//-->
</script>
';

}

/*--------------------------------------
  機  能 : ページヘッダの表示
  引  数 : なし
  戻り値 : なし
 --------------------------------------*/
function PageHeader() {
  global $ocertify;
  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n";
  echo '<html ' . HTML_PARAMS . '>' . "\n";
  echo '<head>' . "\n";
  echo '<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET . '">' . "\n";
  echo '<title>' . TITLE . '</title>' . "\n";
  echo '<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">' . "\n";

  // ユーザ情報、パスワード変更、管理者権限のとき確認メッセージ JavaScript 出力
  if ((isset($GLOBALS['execute_user']) && $GLOBALS['execute_user']) || (isset($GLOBALS['execute_password']) && $GLOBALS['execute_password']) || (isset($GLOBALS['execute_permission']) && $GLOBALS['execute_permission']) ) {
    putJavaScript_ConfirmMsg();           // 確認メッセージを表示する JavaScript
  }

  echo '</head>' . "\n";
  echo '<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">' . "\n";
  echo '<!-- header //-->' . "\n";
  require(DIR_WS_INCLUDES . 'header.php');
  echo '<!-- header_eof //-->' . "\n";
}

/*--------------------------------------
  機  能 : ページのレイアウトテーブル表示
  引  数 : $mode    -(i)  文字列：モード（t:上、u:下）
  戻り値 : なし
 --------------------------------------*/
function PageBodyTable($mode='t') {
  switch ($mode) {
  case 't':
    echo '<!-- body //-->' . "\n";
    echo '<table border="0" width="100%" cellspacing="2" cellpadding="2">' . "\n";
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
  機  能 : ページボディの表示
  引  数 : $mode    -(i)  文字列：モード（t:上、u:下）
       $stitle  -(i)  文字列：ボディのタイトル
  戻り値 : なし
 --------------------------------------*/
function PageBody($mode='t', $stitle = "") {
  switch ($mode) {
  case 't':
    echo '<!-- body_text //-->' . "\n";
    echo '    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n";
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
    echo '    </table></td>' . "\n";
    echo '<!-- body_text_eof //-->' . "\n";
    break;
  } 
}

/*--------------------------------------
  機  能 : ページフッタの表示
  引  数 : なし
  戻り値 : なし
 --------------------------------------*/
function PageFooter() {
  echo "<!-- footer //-->\n";
  require(DIR_WS_INCLUDES . 'footer.php');
  echo "\n<!-- footer_eof //-->\n";
  echo "<br>\n";
  echo "</body>\n";
  echo "</html>\n";
}

//获取当前用户当天 登录次数
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
//修改规则 并插入 数据库
function update_rules($rules){
  $sql = "select configuration_value from configuration
    where configuration_key = 'CONFIG_RULES_KEY' ";
  $query = tep_db_query($sql);
  if($row = tep_db_fetch_array($query)){
   $sql_rule = "update `configuration` SET 
      configuration_value='".$rules."' 
      where configuration_key ='CONFIG_RULES_KEY'";
  }else{
   $sql_rule = "insert into `configuration` (`configuration_id`,
     `configuration_title`, `configuration_key`, `configuration_value`,
     `configuration_description`, `configuration_group_id`, `sort_order`,
     `last_modified`, `date_added`, `use_function`, `set_function`, `site_id`)
       VALUES (NULL,'', 'CONFIG_RULES_KEY', '".$rules."', '', '0', NULL, NULL,
           '0000-00-00 00:00:00', NULL, NULL, '0')";
  }
  if(make_rand_pwd($rules)){
    return tep_db_query($sql_rule);
  }else{
    return false;
  }

}


/* *************************************

   ユーザ情報保守画面のプログラム制御（メイン）

 ************************************* */

  require('includes/application_top.php');
  if (isset($_POST['userid'])) { $userid = $_POST['userid']; }
  if (isset($_POST['aval'])) { $aval = $_POST['aval']; }
  if (isset($_POST['userslist'])) { $userslist = $_POST['userslist']; }
  if (isset($_POST['no_permission_list'])) { $no_permission_list = $_POST['no_permission_list']; }
  if (isset($_POST['staff_permission_list'])) { $staff_permission_list =
    $_POST['staff_permission_list']; }
  if (isset($_POST['chief_permission_list'])) { $chief_permission_list =
    $_POST['chief_permission_list']; }
  if (isset($_POST['permission_list'])) { $permission_list = $_POST['permission_list']; }
  if (isset($_POST['execute_user'])) { $execute_user = $_POST['execute_user']; }
  if (isset($_POST['execute_password'])) { $execute_password = $_POST['execute_password']; }
  if (isset($_POST['execute_permission'])) { $execute_permission = $_POST['execute_permission']; }
//修改权限
if (isset($_POST['execute_change'])) { $execute_change = $_POST['execute_change'];}
//2003-07-16 hiroshi_sato add 6 lines
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

  PageHeader();       // ページ・ヘッダの表示
  PageBodyTable('t');     // ページのレイアウトテーブル：開始（ナビゲーションボックスを包括するテーブル開始）

  // 左ナビゲーションボックスの表示
  echo "<!-- left_navigation //-->\n";    // 
  include_once(DIR_WS_INCLUDES . 'column_left.php');
  echo "\n<!-- left_navigation_eof //-->\n";
  echo "    </table></td>\n";

// 画面表示、入力チェックＤＢ反映
  if ($ocertify->auth_user) {
    // ユーザ管理メニュー
    if (isset($execute_menu) && $execute_menu) {
      UserManu_preview();               // 初期表示

    // ユーザの追加
    } else if (isset($execute_new) && $execute_new) {
      if (isset($execute_insert) && $execute_insert) {
        UserInsert_execute();    // ユーザの追加処理実行
      }else{
        UserInsert_preview();            // ユーザの追加ページ表示
      }

    // ユーザ情報保守
    } else if (isset($execute_user) && $execute_user) {
      if (isset($execute_update) && $execute_update){
        UserInfor_execute();   // ユーザ情報更新処理実行
      }else if (isset($execute_delete) && $execute_delete){
        UserDelete_execute();  // ユーザ情報削除処理実行
      }else {
        UserInfo_preview();            // ユーザ情報ページ表示
      }

    // パスワード変更
    } else if (isset($execute_password) && $execute_password) {
      if (isset($execute_update) && $execute_update){
        UserPassword_execute();  // パスワード変更処理実行
      }else{
        UserPassword_preview();          // パスワード変更ページ表示
      }

    // 管理者権限
    } else if (isset($execute_permission) && $execute_permission) {

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
        UserPermission_preview();                // 管理者権限ページ表示
      }

//permission end 
 
    } elseif (isset($execute_change) && $execute_change) {
      if (isset($execute_update) && $execute_update)   {
        ChangePermission_execute();  } // 修改用户管理网站的权限的执行
      else{ ChangePermission();}//用户权限页面
    // ユーザ管理メニュー
    } else {
      UserManu_preview();               // 初期表示
    }
  }

  PageBody('u');        // ページボディの終了
  PageBodyTable('u');     // ページのレイアウトテーブル：終了
  PageFooter();       // ページフッタの表示

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
