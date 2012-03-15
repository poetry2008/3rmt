<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '予約ステータス設定');

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
define('TEXT_EDIT_ORDERS_STATUS_IMAGE', 'ステータスに表示するアイコンを指定');

define('TEXT_ORDERS_STATUS_FINISHED', '完了注文');
//mail本文 add end
define('PREORDERS_STATUS_EXPLANATION_TEXT', '名前：${NAME}<br>メールアドレス：${MAIL}<br>注文日：${PREORDER_D}<br>注文番号：${PREORDER_N}<br>支払い方法：${PAY}<br>注文金額：${ORDER_M}<br>取引方法：${TRADING}<br>注文ステータス：${ORDER_S}<br>自社キャラ名：${ORDER_A}<br>サイト名：${SITE_NAME}<br>サイトのURL：${SITE_URL}<br>お問い合わせ用メールアドレス：${SUPPORT_EMAIL}<br>確保期限：${ENSURE_TIME}<br>希望個数:${PRODUCTS_QUANTITY}<br>期限:${EFFECTIVE_TIME}<br>商品名:${PRODUCTS_NAME}<br>商品単価:${PRODUCTS_PRICE}<br>小計:${SUB_TOTAL}');

define('TEXT_ORDERS_STATUS_NOMAIL', 'DON\'T SEND MAIL');
define('TEXT_ORDERS_STATUS_AVG_PRICE','平均単価の算出条件に設定');
define('TEXT_ORDERS_STATUS_PIC_DEL', 'アイコンを削除');
define('TEXT_PREORDER_NYUUKA', '入荷連絡');
