<?php
/* *********************************************************
  モジュール名: users_log.php
 * 2002-05-13
 * Naomi Suzukawa
 * suzukawa@bitscope.co.jp
  ----------------------------------------------------------
ユーザアクセスログの言語定義

  ■変更履歴
********************************************************* */
// ページタイトル
define('HEADING_TITLE', 'アクセスログ');

// テーブルアクセスエラーメッセージ
define('TEXT_ERRINFO_DB_NO_LOGINFO', 'アクセス情報が取得できませんでした');

// メッセージ
define('TEXT_INFO_DELETE_DAY', 'アクセス情報の削除');
define('TEXT_INFO_DELETE_FORMER_DAY', '日以前のデータ');
// Format: '(id1:val1,id2:val2)'
define('TEXT_INFO_STATUS_IN', 'a:認証,e:DBエラー,p:Passwordエラー,n:失敗');
define('TEXT_INFO_STATUS_OUT', 'i:ログイン,o:ログアウト,t:タイムアウト');
define('TEXT_PAGE', '( %s / %s Page [ %s / %s Rows ] )');

// ボタン
define('BUTTON_DELETE_LOGINLOG', '削除');
define('BUTTON_PREVIOUS_PAGE', '前ページ');
define('BUTTON_NEXT_PAGE', '次ページ');
define('BUTTON_JUMP_PAGE', 'ページへジャンプ');

// 項目名
define('TABLE_HEADING_LOGINID', 'ID');
define('TABLE_HEADING_LOGINTIME', 'ログイン日時');
define('TABLE_HEADING_LAST_ACCESSTIME', '最終アクセス日時');
define('TABLE_HEADING_USER', 'ユーザ');
define('TABLE_HEADING_STATUS', 'ステータス');
define('TABLE_HEADING_ADDRESS', 'アドレス');
define('TABLE_HEADING_PAGE', 'ページ');

// JavaScriptの確認メッセージ
define('JAVA_SCRIPT_INFO_DELETE', 'アクセスログを削除します。\nよろしいですか？');
define('PAGE_TITLE_MENU_USER', '');
?>
