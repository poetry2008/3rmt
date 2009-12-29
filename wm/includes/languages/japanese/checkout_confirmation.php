<?php
/*
  $Id: checkout_confirmation.php,v 1.7 2003/05/22 04:56:30 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', 'レジ');
define('NAVBAR_TITLE_2', '最終確認');

define('HEADING_TITLE', 'ご注文内容を確認してください&nbsp;&nbsp;（ご注文はまだ確定しておりません！）');

define('HEADING_DELIVERY_ADDRESS', 'お届け先');
define('HEADING_SHIPPING_METHOD', '配送方法');
define('HEADING_PRODUCTS', '数量 / 商品名');
define('HEADING_TAX', '消費税');
define('HEADING_TOTAL', '合計');
define('HEADING_BILLING_INFORMATION', 'ご請求について');
define('HEADING_BILLING_ADDRESS', 'ご請求先');
define('HEADING_PAYMENT_METHOD', 'お支払い方法');
define('HEADING_PAYMENT_INFORMATION', 'お支払いについて');
define('HEADING_ORDER_COMMENTS', 'ご注文についてのコメント');

define('TEXT_EDIT', '変更する');

//Add Point System
define('TEXT_POINT_NOW', '<b>買取はポイントがつきません</b>&nbsp;&nbsp;今回の獲得予定ポイント:');

define('TEXT_TORIHIKI_TITLE', '取引時間&nbsp;');

define('TEXT_CARACTOR', 'お届け先キャラクター名:');
define('TEXT_TORIHIKIHOUHOU', 'オプション:');
define('TEXT_TORIHIKIKIBOUBI', '取引希望日:');
define('TEXT_TORIHIKIKIBOUJIKAN', '取引希望時間:');

define('TABLE_HEADING_BANK', '振込先口座情報');
define('TEXT_BANK_NAME', '金融機関名:');
define('TEXT_BANK_SHITEN', '支店名:');
define('TEXT_BANK_KAMOKU', '口座種別:');
define('TEXT_BANK_KOUZA_NUM', '口座番号:');
define('TEXT_BANK_KOUZA_NAME', '口座名義:');

define('TEXT_BANK_SELECT_KAMOKU_F', '普通');
define('TEXT_BANK_SELECT_KAMOKU_T', '当座');

define('TEXT_BANK_ERROR_NAME', '【'.mb_substr(TEXT_BANK_NAME,0,(mb_strlen(TEXT_BANK_NAME)-1)).'】が入力されていません');
define('TEXT_BANK_ERROR_SHITEN', '【'.mb_substr(TEXT_BANK_SHITEN,0,(mb_strlen(TEXT_BANK_SHITEN)-1)).'】が入力されていません');
define('TEXT_BANK_ERROR_KOUZA_NUM', '【'.mb_substr(TEXT_BANK_KOUZA_NUM,0,(mb_strlen(TEXT_BANK_KOUZA_NUM)-1)).'】が入力されていません');
define('TEXT_BANK_ERROR_KOUZA_NUM2', '【'.mb_substr(TEXT_BANK_KOUZA_NUM,0,(mb_strlen(TEXT_BANK_KOUZA_NUM)-1)).'】は半角で入力してください。');
define('TEXT_BANK_ERROR_KOUZA_NAME', '【'.mb_substr(TEXT_BANK_KOUZA_NAME,0,(mb_strlen(TEXT_BANK_KOUZA_NAME)-1)).'】が入力されていません');
?>