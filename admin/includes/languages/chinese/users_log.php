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
define('HEADING_TITLE', '访问记录');

// テーブルアクセス错误メッセージ
define('TEXT_ERRINFO_DB_NO_LOGINFO', '无法获取访问信息');

// メッセージ
define('TEXT_INFO_DELETE_DAY', '删除访问信息');
define('TEXT_INFO_DELETE_FORMER_DAY', '··天以前的数据');
// Format: '(id1:val1,id2:val2)'
define('TEXT_INFO_STATUS_IN', 'a:认证,e:DB错误,p:Password错误,n:失败');
define('TEXT_INFO_STATUS_OUT', 'i:登录,o:退出,t:超时');
define('TEXT_PAGE', '( %s / %s Page [ %s / %s Rows ] )');

// 按钮
define('BUTTON_DELETE_LOGINLOG', '删除');
define('BUTTON_PREVIOUS_PAGE', '前一页');
define('BUTTON_NEXT_PAGE', '下一页');
define('BUTTON_JUMP_PAGE', '跳至··页');

// 項目名
define('TABLE_HEADING_LOGINID', 'ID');
define('TABLE_HEADING_LOGINTIME', '登录时间');
define('TABLE_HEADING_LAST_ACCESSTIME', '最后访问时间');
define('TABLE_HEADING_USER', '用户');
define('TABLE_HEADING_STATUS', '状态');
define('TABLE_HEADING_ADDRESS', '地址');
define('TABLE_HEADING_PAGE', '页');

// JavaScriptの確認メッセージ
define('JAVA_SCRIPT_INFO_DELETE', '删除访问记录。\n可以吗？');
?>