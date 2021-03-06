<?php
// 页面标题
define('HEADING_TITLE', 'ワンタイムパスワードログ');

// 访问表错误信息
define('TEXT_ERRINFO_DB_NO_LOGINFO', 'アクセス情報が取得できませんでした');

// 信息
define('TEXT_INFO_DELETE_DAY', 'アクセス情報の削除');
define('TEXT_INFO_DELETE_FORMER_DAY', '日以前のデータ');
// Format: '(id1:val1,id2:val2)'
define('TEXT_INFO_STATUS_IN', 'a:認証,e:DBエラー,p:Passwordエラー,n:失敗');
define('TEXT_INFO_STATUS_OUT', 'i:ログイン,o:ログアウト,t:タイムアウト');
define('TEXT_PAGE', '( %s / %s Page [ %s / %s Rows ] )');

// 按钮
define('BUTTON_DELETE_LOGINLOG', '削除');
define('BUTTON_PREVIOUS_PAGE', '前ページ');
define('BUTTON_NEXT_PAGE', '次ページ');
define('BUTTON_JUMP_PAGE', 'ページへジャンプ');

// 项目名称
define('TABLE_HEADING_LOGINID', 'ID');
define('TABLE_HEADING_LOGINTIME', 'ログイン日時');
define('TABLE_HEADING_LAST_ACCESSTIME', '最終アクセス日時');
define('TABLE_HEADING_USER', 'ユーザー');
define('TABLE_HEADING_STATUS', 'ステータス');
define('TABLE_HEADING_ADDRESS', 'アドレス');
define('TABLE_HEADING_PAGE', 'ページ');

// JavaScript的确认信息
define('JAVA_SCRIPT_INFO_DELETE', 'アクセスログを削除します。\nよろしいですか？');

define('PAGE_TITLE_MENU_USER', '');
?>
