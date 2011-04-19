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
define('HEADING_TITLE', 'ログ');

// テーブルアクセスエラーメッセージ
define('TEXT_ERRINFO_DB_NO_LOGINFO', 'アクセス情報が取得できませんでした');

// メッセージ
define('TEXT_INFO_DELETE_DAY', 'アクセス情報の削除');
define('TEXT_INFO_DELETE_FORMER_DAY', '日以前のデータ');
// Format: '(id1:val1,id2:val2)'
define('TEXT_PAGE', '( %s / %s Page [ %s / %s Rows ] )');

// ボタン
define('BUTTON_DELETE_ONCE_PWD_LOG', '削除');
define('BUTTON_PREVIOUS_PAGE', '前ページ');
define('BUTTON_NEXT_PAGE', '次ページ');
define('BUTTON_JUMP_PAGE', 'ページへジャンプ');

// 項目名
define('TABLE_HEADING_USERNAME','ユーザ名');
define('TABLE_HEADING_PWD_USERNAME','ワンタイムパスワード名');
define('TABLE_HEADING_URL','操作説明');
define('TABLE_HEADING_CREATED_AT','操作日時');

// JavaScriptの確認メッセージ
define('JAVA_SCRIPT_INFO_DELETE', 'アクセスログを削除します。\nよろしいですか？');

define('PAGE_TITLE_MENU_USER', '');
?>
