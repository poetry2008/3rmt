<?php
// 页面标题
define('HEADING_TITLE', 'アクセスログ');

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
define('TABLE_HEADING_USER', 'ユーザ');
define('TABLE_HEADING_STATUS', 'ステータス');
define('TABLE_HEADING_ADDRESS', 'アドレス');
define('TABLE_HEADING_PAGE', 'ページ');

// JavaScript的确认信息
define('JAVA_SCRIPT_INFO_DELETE', 'アクセスログを削除します。\nよろしいですか？');

define('PAGE_TITLE_MENU_USER', '');
define('PAGE_TITLE_MENU_IP','ロックされたIPリスト');
define('TABLE_HEADING_PERMISSIONS','管理者権限');
define('TABLE_HEADING_OPERATE','操作');
define('TEXT_IP_UNLOCK','ロック解除');
define('TEXT_DELETE_CONFIRM','本当にこのIPアドレスのロックを解除しますか？');
define('TEXT_CONFIRM','本当に該当IPアドレスをロックしますか？');
define('TEXT_IP_UNLOCK_NOTES','<font color="#FF0000">※</font>&nbsp;同じIPで管理者権限のAdmin,Staff,Chiefのユーザはロックされたとき、先にAdminを解除してください。');
define('TEXT_LOGS_EDIT_SELECT','選択したものを');
define('TEXT_LOGS_EDIT_DELETE','削除する');
define('TEXT_LOGS_EDIT_MUST_SELECT','少なくとも1つの選択肢を選んでください。');
define('TEXT_LOGS_EDIT_CONFIRM','本当に削除しますか？');
define('TEXT_SORT_ASC','▲');
define('TEXT_SORT_DESC','▼');
define('TEXT_LOCK','ロック');
?>
