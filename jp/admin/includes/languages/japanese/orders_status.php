<?php
/*
  $Id$
*/

define('HEADING_TITLE', '注文ステータス設定');

define('TABLE_HEADING_ORDERS_STATUS', '注文ステータス');
define('TABLE_HEADING_ACTION', 'ステータス');

define('TEXT_INFO_EDIT_INTRO', '必要な変更を加えてください');
define('TEXT_INFO_ORDERS_STATUS_NAME', '注文ステータス:');
define('TEXT_INFO_INSERT_INTRO', '新しい注文ステータスと関連するデータを入力してください');
define('TEXT_INFO_DELETE_INTRO', '本当にこの注文ステータスを削除しますか?');
define('TEXT_INFO_HEADING_NEW_ORDERS_STATUS', '注文ステータス');
define('TEXT_INFO_HEADING_EDIT_ORDERS_STATUS', '注文ステータスを編集');
define('TEXT_INFO_HEADING_DELETE_ORDERS_STATUS', '注文ステータスを削除');

define('ERROR_REMOVE_DEFAULT_ORDER_STATUS', 'エラー: デフォルトの注文ステータスは削除できません。他の注文ステータスをデフォルトに設定してから、もう一度試してください。');
define('ERROR_STATUS_USED_IN_ORDERS', 'エラー: この注文ステータスは、現在注文に使用されています。');
define('ERROR_STATUS_USED_IN_HISTORY', 'エラー: この注文ステータスは、現在注文履歴に使用されています。');

//mail本文 add
define('TEXT_INFO_ORDERS_STATUS_MAIL', 'メール本文');
define('TEXT_INFO_ORDERS_STATUS_TITLE', 'メールタイトル');

//define('TEXT_EDIT_ORDERS_STATUS_IMAGE', 'メール&#22270;像');
define('TEXT_EDIT_ORDERS_STATUS_IMAGE', 'アイコン');

define('TEXT_ORDERS_STATUS_FINISHED', '完了注文');
define('TEXT_ORDERS_STATUS_SET_PRICE_CALCULATION','平均単価の算出条件に設定');
//mail本文 add end

define('TEXT_ORDERS_FETCH_CONDITION', '取引状況');
define('TEXT_USER_ADDED','作成者');
define('TEXT_USER_UPDATE','更新者');
define('TEXT_DATE_ADDED','作成日');
define('TEXT_DATE_UPDATE','更新日');
define('TEXT_DEL_IMAGE','アイコンを削除');
define('TEXT_ORDERS_STATUS_OPTION', 'オプション');
define('TEXT_ORDERS_STATUS_OPTION_NORMAL', '取引最中');
define('TEXT_ORDERS_STATUS_OPTION_SUCCESS', '取引成功');
define('TEXT_ORDERS_STATUS_OPTION_FAIL', '取引失敗');
define('TEXT_TRANSACTION_EXPIRED_COMMENT', 'お届け日時を過ぎた際に、選択したステータスになっていれば警告アイコンを表示します。複数選択可。');
define('TEXT_ORDERS_STATUS_REORDER_TEXT', '再配達受付のデフォルトステータス');
define('TEXT_ORDERS_STATUS_CONFIRM_TIME_TEXT', '支払時間記録ステータス');
define('TEXT_ORDERS_STATUS_EDIT_ORDERS_TEXT', '注文内容編集のデフォルトステータス');
