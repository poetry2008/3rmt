<?php
/* *********************************************************
  モジュール名: users.php
 * 2001/5/29
 *   modi 2002-05-10
 * Naomi Suzukawa
 * suzukawa@bitscope.co.jp
  ----------------------------------------------------------
ユーザ管理の言語定義

  ■変更履歴
********************************************************* */
// ページタイトル
define('HEADING_TITLE', 'ユーザ管理');

// エラーメッセージ表示タイトル
define('TABLE_HEADING_ERRINFO', '!!!!! エラーメッセージ !!!!!');

// 入力エラーメッセージ
define('TEXT_ERRINFO_INPUT_NOINPUT', '未入力です');
define('TEXT_ERRINFO_INPUT_ERR', '正しく入力されていません');
define('TEXT_ERRINFO_INPUT_LENGTH', '%d 文字以上入力してください');
define('TEXT_ERRINFO_USER_DELETE', '<b>ユーザ情報の削除</b>:ユーザ本人の情報は削除できません');
define('TEXT_ERRINFO_USER_GRANT', '<b>権限を与える</b>:ユーザを選択してください');
define('TEXT_ERRINFO_USER_REVOKE', '<b>権限を取消す</b>:ユーザを選択してください');
define('TEXT_ERRINFO_USER_REVOKE_ONESELF', '<b>権限を取消す</b>:ユーザ本人の権限を取消すことはできません');
define('TEXT_ERRINFO_CONFIRM_PASSWORD', '<b>確認のため再入力</b>:確認のため再入力したパスワードが違います');

// テーブルアクセスエラーメッセージ
define('TEXT_ERRINFO_DB_NO_USERINFO', 'ユーザ情報が取得できませんでした');
define('TEXT_ERRINFO_DB_NO_USER', '対象となるユーザが存在しません');
define('TEXT_ERRINFO_DB_USERCHACK', 'ユーザの存在チェックでエラーが発生しました');
define('TEXT_ERRINFO_DB_EXISTING_USER', '既に登録されているユーザです');
define('TEXT_ERRINFO_DB_INSERT_USER', 'ユーザ情報の登録ができませんでした');
define('TEXT_ERRINFO_DB_INSERT_PERMISSION', 'ユーザ権限情報の登録ができませんでした');
define('TEXT_ERRINFO_DB_UPDATE_USER', 'ユーザ情報の更新ができませんでした');
define('TEXT_ERRINFO_DB_DELETE_USER', 'ユーザ情報の削除ができませんでした');
define('TEXT_ERRINFO_DB_CHANGE_PASSWORD', 'パスワードの変更ができませんでした');
define('TEXT_ERRINFO_DB_CHANGE_USER', 'ユーザ権限の変更ができませんでした');

// 完了メッセージ
define('TEXT_SUCCESSINFO_INSERT_USER', 'ユーザを追加しました');
define('TEXT_SUCCESSINFO_UPDATE_USER', 'ユーザ情報を更新しました');
define('TEXT_SUCCESSINFO_DELETE_USER', 'ユーザ情報を削除しました');
define('TEXT_SUCCESSINFO_CHANGE_PASSWORD', 'パスワードを変更しました');
define('TEXT_SUCCESSINFO_PERMISSION', 'ユーザ権限を%sました');

// ページタイトル
define('PAGE_TITLE_MENU_USER', 'ユーザ管理メニュー');
define('PAGE_TITLE_INSERT_USER', 'ユーザの追加');
define('PAGE_TITLE_USERINFO', 'ユーザ情報');
define('PAGE_TITLE_PASSWORD', 'パスワード変更');
define('PAGE_TITLE_PERMISSION', '管理者権限');

// ボタン
define('BUTTON_BACK_MENU', 'ユーザ管理メニューに戻る');
define('BUTTON_INSERT_USER', 'ユーザの追加');
define('BUTTON_INFO_USER', 'ユーザ情報');
define('BUTTON_CHANGE_PASSWORD', 'パスワード変更');
define('BUTTON_PERMISSION', '管理者権限');
define('BUTTON_INSERT', '追加');
define('BUTTON_CLEAR', 'クリア');
define('BUTTON_UPDATE', '更新');
define('BUTTON_DELETE', '削除');
define('BUTTON_RESET', '元の値に戻す');
define('BUTTON_CHANGE', '変更');
define('BUTTON_GRANT', '権限を与える >>');
define('BUTTON_REVOKE', '<< 権限を取消す');
define('BUTTON_BACK_PERMISSION', '管理者権限に戻る');

// 項目名
define('TABLE_HEADING_COLUMN', 'カラム');
define('TABLE_HEADING_DATA', 'データ');
define('TABLE_HEADING_USER', 'ユーザ');
define('TABLE_HEADING_USER_LIST', 'ユーザ一覧');
define('TABLE_HEADING_USER_ID', 'ユーザID');
define('TABLE_HEADING_PASSWORD', 'パスワード');
define('TABLE_HEADING_NAME', '氏名');
define('TABLE_HEADING_EMAIL', 'E-Mail');
define('TABLE_HEADING_NEW_PASSWORD', '新しいパスワード');
define('TABLE_HEADING_CONFIRM_PASSWORD', '確認のため再入力');
!defined('TABLE_HEADING_USER')&& define('TABLE_HEADING_USER', '一般ユーザ');
define('TABLE_HEADING_ADMIN', 'サイト管理者');

// JavaScriptの確認メッセージ
define('JAVA_SCRIPT_INFO_CHANGE', 'ユーザ管理情報を変更します。\nよろしいですか？');
define('JAVA_SCRIPT_INFO_DELETE', 'ユーザ管理情報を削除します。\nよろしいですか？');
define('JAVA_SCRIPT_INFO_PASSWORD', 'パスワードを変更します。\nよろしいですか？');
define('JAVA_SCRIPT_INFO_GRANT', '管理者権限を与えます。\nよろしいですか？');
define('JAVA_SCRIPT_INFO_REVOKE', '管理者権限を取り消します。\nよろしいですか？');
?>
