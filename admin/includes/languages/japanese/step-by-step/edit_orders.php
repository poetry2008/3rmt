<?php
/*
  $Id: edit_orders.php,v 1.25 2003/08/07 00:28:44 jwh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce
  
  Released under the GNU General Public License
*/

define('HEADING_TITLE', '注文の編集・詳細');
define('HEADING_TITLE_NUMBER', '注文番号:');
define('HEADING_TITLE_DATE', ' - ');
define('HEADING_SUBTITLE', '編集したい部分の内容を入力し、更新ボタンをクリックしてください。');
define('HEADING_TITLE_SEARCH', '注文 ID:');
define('HEADING_TITLE_STATUS', 'ステータス:');
define('ADDING_TITLE', '商品を追加する');

define('HINT_UPDATE_TO_CC', '<font color="#FF0000">ヒント: </font>Set payment to "Credit Card" to show some additional fields.');
define('HINT_DELETE_POSITION', '<font color="#FF0000">ヒント: </font>商品を削除する場合は個数に「0」と入力して更新してください。');
define('HINT_TOTALS', '<font color="#FF0000">ヒント: </font>Feel free to give discounts by adding negative amounts to the list.<br>Fields with "0" values are deleted when updating the order (exception: shipping).');
define('HINT_PRESS_UPDATE', '更新ボタンをクリックして、編集した内容を更新してください。');

define('TABLE_HEADING_COMMENTS', 'コメント');
define('TABLE_HEADING_CUSTOMERS', '顧客情報');
define('TABLE_HEADING_ORDER_TOTAL', '合計金額');
define('TABLE_HEADING_DATE_PURCHASED', '注文日');
define('TABLE_HEADING_STATUS', '新しいステータス');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_PRODUCTS_MODEL', '商品型番');
define('TABLE_HEADING_PRODUCTS', '商品');
define('TABLE_HEADING_TAX', '消費税');
define('TABLE_HEADING_TOTAL', '合計');
define('TABLE_HEADING_UNIT_PRICE', '価格 (税抜き)');
define('TABLE_HEADING_UNIT_PRICE_TAXED', '価格 (税込み)');
define('TABLE_HEADING_TOTAL_PRICE', '合計 (税抜き)');
define('TABLE_HEADING_TOTAL_PRICE_TAXED', '合計 (税込み)');
define('TABLE_HEADING_TOTAL_MODULE', '価格構成要素');
define('TABLE_HEADING_TOTAL_AMOUNT', '金額');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', '顧客に通知');
define('TABLE_HEADING_DATE_ADDED', '送信日');

define('ENTRY_CUSTOMER', '顧客情報');
define('ENTRY_CUSTOMER_NAME', 'お名前');
define('ENTRY_CUSTOMER_COMPANY', '会社名');
define('ENTRY_CUSTOMER_ADDRESS', '住所1');
define('ENTRY_CUSTOMER_SUBURB', '住所2');
define('ENTRY_CUSTOMER_CITY', '市町村');
define('ENTRY_CUSTOMER_STATE', '都道府県');
define('ENTRY_CUSTOMER_POSTCODE', '郵便番号');
define('ENTRY_CUSTOMER_COUNTRY', '国名');
define('ENTRY_CUSTOMER_PHONE', '電話番号');
define('ENTRY_CUSTOMER_EMAIL', 'Eメールアドレス');

define('ENTRY_SOLD_TO', '注文者:');
define('ENTRY_DELIVERY_TO', '送付先:');
define('ENTRY_SHIP_TO', 'Shipping to:');
define('ENTRY_SHIPPING_ADDRESS', '配送先住所');
define('ENTRY_BILLING_ADDRESS', '請求先住所');
define('ENTRY_PAYMENT_METHOD', '支払方法:');
define('ENTRY_CREDIT_CARD_TYPE', 'カードタイプ:');
define('ENTRY_CREDIT_CARD_OWNER', 'カード保有者:');
define('ENTRY_CREDIT_CARD_NUMBER', 'カード番号:');
define('ENTRY_CREDIT_CARD_EXPIRES', '有効期限:');
define('ENTRY_SUB_TOTAL', '小計:');
define('ENTRY_TAX', '消費税:');
define('ENTRY_SHIPPING', '送料:');
define('ENTRY_TOTAL', '合計:');
define('ENTRY_DATE_PURCHASED', '注文日:');
define('ENTRY_STATUS', 'ステータス:');
define('ENTRY_DATE_LAST_UPDATED', '最終更新日:');
define('ENTRY_NOTIFY_CUSTOMER', '顧客へ通知:');
define('ENTRY_NOTIFY_COMMENTS', 'コメントを送信:');
define('ENTRY_PRINTABLE', '納品書印刷');

define('TEXT_INFO_HEADING_DELETE_ORDER', '注文を削除');
define('TEXT_INFO_DELETE_INTRO', '本当に注文を削除しますか？');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '在庫を戻す');
define('TEXT_DATE_ORDER_CREATED', '作成日:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '最終更新日:');
define('TEXT_DATE_ORDER_ADDNEW', '新しい商品を追加');
define('TEXT_INFO_PAYMENT_METHOD', '支払方法:');

define('TEXT_ALL_ORDERS', '全ての注文');
define('TEXT_NO_ORDER_HISTORY', '注文は存在しません。');

define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'ご注文受付状況のお知らせ');
define('EMAIL_TEXT_ORDER_NUMBER', 'ご注文受付番号:');
define('EMAIL_TEXT_INVOICE_URL', 'ご注文についての情報を下記URLでご覧になれます。' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', '注文日:');
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


define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', '商品を選択');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', '商品オプションを選択');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', '商品オプションは存在しません。スキップ...');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', 'この商品の数量');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', '追加する');
define('ADDPRODUCT_TEXT_STEP', 'ステップ');
define('ADDPRODUCT_TEXT_STEP1', ' &laquo; カテゴリ選択. ');
define('ADDPRODUCT_TEXT_STEP2', ' &laquo; 商品選択. ');
define('ADDPRODUCT_TEXT_STEP3', ' &laquo; オプション選択. ');

define('MENUE_TITLE_CUSTOMER', '1. 顧客情報');
define('MENUE_TITLE_PAYMENT', '2. 支払方法');
define('MENUE_TITLE_ORDER', '3. 注文商品');
define('MENUE_TITLE_TOTAL', '4. 配送、決済、税金');
define('MENUE_TITLE_STATUS', '5. 注文ステータス、コメント通知');
define('MENUE_TITLE_UPDATE', '6. データを更新');
?>