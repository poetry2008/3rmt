<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '注文管理');
define('HEADING_TITLE_SEARCH', '注文ID:');
define('HEADING_TITLE_STATUS', 'ステータス:');

define('TABLE_HEADING_COMMENTS', 'コメント');
define('TABLE_HEADING_CUSTOMERS', '顧客名');
define('TABLE_HEADING_ORDER_TOTAL', '注文総額');
define('TABLE_HEADING_DATE_PURCHASED', '注文日');
define('TABLE_HEADING_STATUS', 'ステータス');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_PRODUCTS_MODEL', '型番');
define('TABLE_HEADING_PRODUCTS', '数量 / 商品名');
define('TABLE_HEADING_CHARACTER', 'キャラクター名');
define('TABLE_HEADING_TAX', '税率');
define('TABLE_HEADING_TOTAL', '合計');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', '価格(税別)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', '価格(税込)');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', '合計(税別)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', '合計(税込)');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', '顧客に通知');
define('TABLE_HEADING_DATE_ADDED', '処理日');

define('ENTRY_CUSTOMER', '顧客名:');
define('ENTRY_SOLD_TO', 'ご注文者名:');
!defined('ENTRY_STREET_ADDRESS') && define('ENTRY_STREET_ADDRESS', '住所１:');
!defined('ENTRY_SUBURB')          && define('ENTRY_SUBURB', '住所２:');
!defined('ENTRY_CITY')            && define('ENTRY_CITY', '市区町村:');
!defined('ENTRY_POST_CODE')       && define('ENTRY_POST_CODE', '郵便番号:');
!defined('ENTRY_STATE')           && define('ENTRY_STATE', '都道府県:');
!defined('ENTRY_COUNTRY')         && define('ENTRY_COUNTRY', '国名:');
!defined('ENTRY_TELEPHONE')       && define('ENTRY_TELEPHONE', '電話番号:');
!defined('ENTRY_EMAIL_ADDRESS')   && define('ENTRY_EMAIL_ADDRESS', 'E-Mail アドレス:');
define('ENTRY_DELIVERY_TO', '配送先:');
define('ENTRY_SHIP_TO', '配送先:');
define('ENTRY_SHIPPING_ADDRESS', '配送先:');
define('ENTRY_BILLING_ADDRESS', '請求先:');
define('ENTRY_PAYMENT_METHOD', '支払方法:');
define('ENTRY_CREDIT_CARD_TYPE', 'クレジットカード種別:');
define('ENTRY_CREDIT_CARD_OWNER', 'クレジットカード所有者:');
define('ENTRY_CREDIT_CARD_NUMBER', 'クレジットカード番号:');
define('ENTRY_CREDIT_CARD_EXPIRES', 'クレジットカード有効期限:');
define('ENTRY_SUB_TOTAL', '小計:');
define('ENTRY_TAX', '税金:');
define('ENTRY_SHIPPING', '配送:');
define('ENTRY_TOTAL', '合計:');
define('ENTRY_DATE_PURCHASED', '注文日:');
define('ENTRY_STATUS', 'ステータス:');
define('ENTRY_DATE_LAST_UPDATED', '更新日:');
define('ENTRY_NOTIFY_CUSTOMER', '処理状況を通知:');
define('ENTRY_NOTIFY_COMMENTS', 'コメントを追加:');
define('ENTRY_PRINTABLE', '納品書をプリント');

define('TEXT_INFO_HEADING_DELETE_ORDER', '注文を削除');
define('TEXT_INFO_DELETE_INTRO', '本当にこの注文を削除しますか?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '在庫数を元に戻す'); // 'Restock product quantity'
define('TEXT_DATE_ORDER_CREATED', '注文日:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '更新日:');
define('TEXT_INFO_PAYMENT_METHOD', '支払方法:');

define('TEXT_ALL_ORDERS', '全ての注文');
define('TEXT_NO_ORDER_HISTORY', '注文履歴はありません');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'ご注文受付状況のお知らせ');
define('EMAIL_TEXT_ORDER_NUMBER', 'ご注文受付番号:');
define('EMAIL_TEXT_INVOICE_URL', 'ご注文についての情報を下記URLでご覧になれます。' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', 'ご注文日:');
define('EMAIL_TEXT_STATUS_UPDATE',
'ご注文の受付状況は次のようなっております。' . "\n"
.'現在の受付状況: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[ご連絡事項]' . "\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', 'エラー: 注文が存在しません。');
define('SUCCESS_ORDER_UPDATED', '成功: 注文状態が更新されました。');
define('WARNING_ORDER_NOT_UPDATED', '警告: 注文状態はなにも変更されませんでした。');

// Add Japanese osCommerce
define('EMAIL_TEXT_STORE_CONFIRMATION', ' へのご注文、誠にありがとうございます。' . "\n\n"
.'ご注文の受付状況及びご連絡事項を、下記にご案内申し上げます。');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'受付状況に関してご質問等がございましたら、当店宛にご連絡頂きますようお願い申し' . "\n"
.'上げます。' . "\n\n"
. EMAIL_SIGNATURE);

define('ENTRY_EMAIL_TITLE', 'メールタイトル：');
?>
