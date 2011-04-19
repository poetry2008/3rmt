<?php
  define('TABLE_ONCE_PWD_LOG', 'once_pwd_log');
// 一覧表示行数
  define('ONCE_PWD_LOG_MAX_LINE', '30');
// MySQL の limit （他のDBでは異なる文法なので修正が必要）
  define('TABLE_LIMIT_OFFSET', 'limit %s,' . ONCE_PWD_LOG_MAX_LINE);

/* ===============================================
  global 変数
 ============================================== */
  $TableBorder = 'border="0"';        // テーブル：線の太さ
  $TableCellspacing = 'cellspacing="3"';    // テーブル：セルの間隔
  $TableCellpadding = 'cellpadding="3"';    // テーブル：セルのマージン
  $TableBgcolor = 'bgcolor="#FFFFFF"';    // テーブル：背景色

  $ThBgcolor = 'bgcolor="Gainsboro"';     // ヘッダセル：背景色
  $TdnBgcolor = 'bgcolor="WhiteSmoke"';   // セル：項目名背景色

  $FontColor = 'color="#009900"';       // フォント：マーク色

/* --------------------------------
2003-04-07 add 
$HTTP_POST_VERS に対応させる
（PHP スーパーグローバル変数[$_POST]への対応は次回とする）
-------------------------------- */
  if (isset($HTTP_POST_VERS['lm'])) { $lm = $HTTP_POST_VERS['lm']; }
  if (isset($HTTP_POST_VERS['jp'])) { $jp = $HTTP_POST_VERS['jp']; }
  if (isset($HTTP_POST_VERS['pp'])) { $pp = $HTTP_POST_VERS['pp']; }
  if (isset($HTTP_POST_VERS['np'])) { $np = $HTTP_POST_VERS['np']; }
  if (isset($HTTP_POST_VERS['aval'])) { $aval = $HTTP_POST_VERS['aval']; }
//2003-07-16 hiroshi_sato add 2 line
        if (isset($_POST['sp'])) { $sp = $_POST['sp']; }
        if (isset($_POST['execute_delete'])) { $execute_delete = $_POST['execute_delete']; }

/* ===============================================
  レコード取得 sql 文字列生成関数（Select）
 ============================================== */
/*--------------------------------------
  機  能 : アクセスログ情報の一覧表示
  引  数 : $oresult   - (i) レコードオブジェクト
  戻り値 : なし
 --------------------------------------*/
function show_once_pwd_log_list($oresult) {

  // データを一覧表示する
  $rec_c = 1;
  while ($arec = tep_db_fetch_array($oresult)) {      // レコードを取得
    $naddress = (int)$arec['address'];    // IPアドレス復元
    $saddress = '';
    for ($i=0; $i<4; $i++) {
      if ($i) $saddress = ($naddress & 0xff) . '.' . $saddress;
      else $saddress = (string)($naddress & 0xff);
      $naddress >>= 8;
    }

    if ($rec_c % 2) echo "<tr " . $GLOBALS['TdnBgcolor'] . ">\n";
    else echo "<tr>\n";
//    echo '<td class="main">' . $arec['sessionid'] . "</td>\n";    // Session ID

    echo '<td class="main" >' . $arec['username'] . "</td>\n";
    echo '<td class="main" >' . $arec['pwd_username'] . "</td>\n";
    echo '<td class="main" >' . $arec['url'] . "</td>\n";
    echo '<td class="main" >' . $arec['created_at'] . "</td>\n";

    echo "</tr>\n";
    $rec_c++;
  }
}

/*--------------------------------------
  機  能 : ページ制御ボタン表示
  引  数 : $nrow    - (i) レコード件数（一覧行数）
  戻り値 : レコード件数
 --------------------------------------*/
function show_page_ctl($nrow) {
  $c_page = 0;

  // 総レコード件数取得
  $ssql = "select count(*) as rc from " . TABLE_ONCE_PWD_LOG;
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;
  }

  $arec = tep_db_fetch_array($oresult);           // レコードの取得
  echo tep_draw_hidden_field("lm", $GLOBALS['lm']);     // 現在のページを隠し項目にセットする

  // ボタン表示
  if ($GLOBALS['lm'] >= ONCE_PWD_LOG_MAX_LINE) {
    echo tep_draw_input_field("pp", BUTTON_PREVIOUS_PAGE, '', FALSE, "submit", FALSE);  // 前ページ
  }
  if ($GLOBALS['lm'] + ONCE_PWD_LOG_MAX_LINE < $arec['rc']) {
    echo tep_draw_input_field("np", BUTTON_NEXT_PAGE, '', FALSE, "submit", FALSE);    // 次ページ
  }

  $page_count = ceil($arec['rc'] / ONCE_PWD_LOG_MAX_LINE);
  for ($i=1; $i<=$page_count; $i++) {
    $lm_ = ($i-1) * ONCE_PWD_LOG_MAX_LINE;
    $asp[$i-1]['id'] = $lm_;
    $asp[$i-1]['text'] = $i;
  }
  echo '&nbsp;&nbsp;';
  $GLOBALS['sp'] = $GLOBALS['lm'];              // プルダウンメニューの選択値セット
  echo tep_draw_pull_down_menu("sp", $asp, $GLOBALS['lm']); // プルダウンメニューの表示
  echo tep_draw_input_field("jp", BUTTON_JUMP_PAGE, '', FALSE, "submit", FALSE);    // ページへジャンプ

  if ($GLOBALS['lm']) $c_page = ceil((int)$GLOBALS['lm'] / ONCE_PWD_LOG_MAX_LINE);
  $c_page++;
  echo '<font class="main">&nbsp;&nbsp;' . sprintf(TEXT_PAGE, $c_page,$page_count,$nrow,$arec['rc']) . '</font>' . "\n";
  echo "<br>\n";

}

/*--------------------------------------
  機  能 : アクセスログ情報取得 sql 文字列生成
  引  数 : なし
  戻り値 : select 句文字列
 --------------------------------------*/
function makeSelectOncePwdLog() {

  $s_select = "select * from " . TABLE_ONCE_PWD_LOG;
  $s_select .= " order by created_at desc";    // アクセス日時の逆順番にデータを取得する
  if (!isset($GLOBALS['lm'])) $GLOBALS['lm'] = 0;
  $s_select .= " " . sprintf(TABLE_LIMIT_OFFSET,$GLOBALS['lm']);

  return $s_select;

}

/* ==============================================
  画面表示関数（メイン）
 ============================================= */
/*--------------------------------------
  機  能 : アクセスログ情報一覧表示
  引  数 : なし
  戻り値 : なし
 --------------------------------------*/
function UserOncePwdLog_list() {

  global $ocertify;           // ユーザ認証オブジェクト

  PageBody('t', PAGE_TITLE_MENU_ONCE_PWD_LOG);  // ユーザ管理画面のタイトル部表示（ユーザ管理メニュー）

  // 現在のページ（レコード取得開始位置）
  if (isset($GLOBALS['jp']) && $GLOBALS['jp']) $GLOBALS['lm'] = (int)$GLOBALS['sp'];
  if (isset($GLOBALS['pp']) && $GLOBALS['pp']) (int)$GLOBALS['lm'] -= ONCE_PWD_LOG_MAX_LINE;
  if (isset($GLOBALS['np']) && $GLOBALS['np']) (int)$GLOBALS['lm'] += ONCE_PWD_LOG_MAX_LINE;

  // アクセスログ情報取得
  $ssql = makeSelectOncePwdLog();
  @$oresult = tep_db_query($ssql);
  if (!$oresult) {                      // エラーだったとき
    echo TEXT_ERRINFO_DB_NO_ONCE_PWD_LOG;            // メッセージ表示
    if ($oresult) @tep_db_free_result($oresult);      // 結果オブジェクトを開放する
    return FALSE;
  }

  $nrow = tep_db_num_rows($oresult);              // レコード件数の取得
  if ($nrow > 0) {                      // レコードが取得できたとき

    // テーブルタグの開始
    echo '<table ' . $GLOBALS['TableBorder'] . " " . $GLOBALS['TableCellspacing'] . " " . $GLOBALS['TableCellpadding'] . " " . $GLOBALS['TableBgcolor'] . '>' . "\n";
    echo "<tr>\n";
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_USERNAME . '</td>' . "\n";      
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' .
      TABLE_HEADING_PWD_USERNAME . '</td>' . "\n";       
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TABLE_HEADING_URL . '</td>' . "\n"; 
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' .
      TABLE_HEADING_CREATED_AT . '</td>' . "\n";       
    echo "</tr>\n";
    show_once_pwd_log_list($oresult);   
    echo "</table>\n";

    echo tep_draw_form('users', basename($GLOBALS['PHP_SELF']));    // <form>タグの出力
    echo "<br>\n";
    show_page_ctl($nrow);       // ページ制御ボタンの表示
    echo "<br>\n";

    // テーブルタグの開始
    echo '<table border="0" cellspacing="1" cellpadding="1">' . "\n";
    echo "<tr>\n";

    // ログの削除
    echo '<td class="main" ' . $GLOBALS['ThBgcolor'] . '>' . TEXT_INFO_DELETE_DAY . "</td>\n";
    echo '<td class="main" colspan="2">';
    echo tep_draw_input_field("aval[span]", $ocertify->login_log_span, 'size="1" maxlength="3"', FALSE, 'text', FALSE);
    echo TEXT_INFO_DELETE_FORMER_DAY . "</td>\n";

    echo '<td class="main">';
    // ボタン表示
    echo tep_draw_input_field("execute_delete", BUTTON_DELETE_ONCE_PWD_LOG, "onClick=\"return formConfirm('delete')\"", FALSE, "submit", FALSE);  // ログの削除
    echo "</td></tr></table>\n";
    echo "</form>\n";           // フォームのフッター
  }
  if ($oresult) @tep_db_free_result($oresult);          // 結果オブジェクトを開放する

  return TRUE;
}

/* ==============================================
  処理実行関数
 ============================================= */
/*--------------------------------------
  機  能 : 一定期間よりも古い認証ログを削除する
  引  数 : なし
  戻り値 : true/false
 --------------------------------------*/
function OncePwdLogDelete_execute() {

  if ( 0 < $GLOBALS['aval']['span']) {
    $sspan_date = date ("Y-m-d H:i:s", mktime (date(H), date(i), date(s),date(m), date(d) - (int)$GLOBALS['aval']['span'], date(Y)));
    $result = tep_db_query("delete from ".TABLE_ONCE_PWD_LOG." where created_at < '$sspan_date'");
  }
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
  機  能 : ページヘッダの表示
  引  数 : なし
  戻り値 : なし
 --------------------------------------*/
function PageHeader() {
  echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">' . "\n";
  echo '<html ' . HTML_PARAMS . '>' . "\n";
  echo '<head>' . "\n";
  echo '<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET . '">' . "\n";
  echo '<title>' . TITLE . '</title>' . "\n";
  echo '<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">' . "\n";
  putJavaScript_ConfirmMsg();           // 確認メッセージを表示する JavaScript
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

/* *************************************

   ユーザ情報保守画面のプログラム制御（メイン）

 ************************************* */

  require('includes/application_top.php');

  if (isset($execute_delete) && $execute_delete) {    // アクセスログ情報の削除
    OncePwdLogDelete_execute();
    $lm = 0;
  }

  PageHeader();       // ページ・ヘッダの表示
  PageBodyTable('t');     // ページのレイアウトテーブル：開始（ナビゲーションボックスを包括するテーブル開始）

  // 左ナビゲーションボックスの表示
  echo "<!-- left_navigation //-->\n";    // 
  include_once(DIR_WS_INCLUDES . 'column_left.php');
  echo "\n<!-- left_navigation_eof //-->\n";
  echo "    </table></td>\n";

  // ログインステータス配列セット
  $aval = explode(',',TEXT_INFO_STATUS_IN);
  if (is_array($aval)) {
    while (list($key,$val) = each($aval)) {
      $sts = explode(':',$val);
      $a_sts_in[$sts[0]] = $sts[1];
    }
  }
  // ログアウトステータス配列セット
  $aval = explode(',',TEXT_INFO_STATUS_OUT);
  if (is_array($aval)) {
    while (list($key,$val) = each($aval)) {
      $sts = explode(':',$val);
      $a_sts_out[$sts[0]] = $sts[1];
    }
  }

  // 画面表示
  UserOncePwdLog_list();    // アクセスログ表示

  PageBody('u');        // ページボディの終了
  PageBodyTable('u');     // ページのレイアウトテーブル：終了
  PageFooter();       // ページフッタの表示

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
