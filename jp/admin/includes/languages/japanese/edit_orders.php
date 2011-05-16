<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '注文内容編集');
define('HEADING_TITLE_SEARCH', '注文ID:');
define('HEADING_TITLE_STATUS', 'ステータス:');
define('ADDING_TITLE', '商品の追加');

define('ENTRY_UPDATE_TO_CC', '(Update to <b>Credit Card</b> to view CC fields.)');
define('TABLE_HEADING_COMMENTS', 'コメント');
define('TABLE_HEADING_CUSTOMERS', '顧客名');
define('TABLE_HEADING_ORDER_TOTAL', '注文総額');
define('TABLE_HEADING_DATE_PURCHASED', '注文日');
define('TABLE_HEADING_STATUS', 'ステータス');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_PRODUCTS_MODEL', '型番');
define('TABLE_HEADING_PRODUCTS', '商品名');
define('TABLE_HEADING_TAX', '消費税');
define('TABLE_HEADING_TOTAL', '合計');
define('TABLE_HEADING_UNIT_PRICE', '単価');
define('TABLE_HEADING_TOTAL_PRICE', '合計');

define('TABLE_HEADING_CUSTOMER_NOTIFIED', '処理状況を通知');
define('TABLE_HEADING_DATE_ADDED', '更新日');

define('ENTRY_CUSTOMER', '顧客名:');
define('ENTRY_CUSTOMER_NAME', '名前');
//add
define('ENTRY_CUSTOMER_NAME_F', '名前(フリガナ)');
define('ENTRY_CUSTOMER_COMPANY', '会社名');
define('ENTRY_CUSTOMER_ADDRESS', '住所');
define('ENTRY_CUSTOMER_SUBURB', '建物名');
define('ENTRY_CUSTOMER_CITY', '市区町村');
define('ENTRY_CUSTOMER_STATE', '都道府県');
define('ENTRY_CUSTOMER_POSTCODE', '郵便番号');
define('ENTRY_CUSTOMER_COUNTRY', '国名');

define('ENTRY_SOLD_TO', '購入者:');
define('ENTRY_DELIVERY_TO', 'お届け先:');
define('ENTRY_SHIP_TO', 'お届け先:');
define('ENTRY_SHIPPING_ADDRESS', 'お届け先:');
define('ENTRY_BILLING_ADDRESS', '請求先:');
define('ENTRY_PAYMENT_METHOD', '支払方法:');
define('ENTRY_CREDIT_CARD_TYPE', 'クレジットカードタイプ:');
define('ENTRY_CREDIT_CARD_OWNER', 'カード名義:');
define('ENTRY_CREDIT_CARD_NUMBER', 'カード番号:');
define('ENTRY_CREDIT_CARD_EXPIRES', 'カード有効期限:');
define('ENTRY_SUB_TOTAL', '小計:');
define('ENTRY_TAX', '消費税:');
define('ENTRY_SHIPPING', '配送方法:');
define('ENTRY_TOTAL', '合計:');
define('ENTRY_DATE_PURCHASED', '注文日:');
define('ENTRY_STATUS', 'ステータス:');
define('ENTRY_DATE_LAST_UPDATED', '更新日:');
define('ENTRY_NOTIFY_CUSTOMER', '処理状況を通知:');
define('ENTRY_NOTIFY_COMMENTS', 'コメントを追加:');
define('ENTRY_PRINTABLE', '納品書をプリント');

define('TEXT_INFO_HEADING_DELETE_ORDER', '注文削除');
define('TEXT_INFO_DELETE_INTRO', '本当にこの注文を削除しますか?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '在庫数を元に戻す');
define('TEXT_DATE_ORDER_CREATED', '作成日:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '更新日:');
define('TEXT_DATE_ORDER_ADDNEW', '商品を追加');
define('TEXT_INFO_PAYMENT_METHOD', '支払方法:');

define('TEXT_ALL_ORDERS', 'すべての注文');
define('TEXT_NO_ORDER_HISTORY', '注文履歴はありません');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'ご注文受付状況のお知らせ');
define('EMAIL_TEXT_ORDER_NUMBER', 'ご注文受付番号: ');
define('EMAIL_TEXT_INVOICE_URL', 'ご注文についての情報を下記URLでご覧になれます。' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', 'ご注文日: ');
define('EMAIL_TEXT_STATUS_UPDATE',
'ご注文の受付状況は次のようなっております。' . "\n"
.'現在の受付状況: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[ご連絡事項]' . "\n\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', 'エラー: 注文が存在しません。');
define('SUCCESS_ORDER_UPDATED', '成功: 注文状態が更新されました。');
define('WARNING_ORDER_NOT_UPDATED', '警告: 注文状態はなにも変更されませんでした。');

define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', '商品を選択');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', 'オプションを選択');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', 'オプションはありません: スキップします..');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', '数量');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', '追加');

// Add Japanese osCommerce
define('EMAIL_TEXT_STORE_CONFIRMATION', ' へのご注文、誠にありがとうございます。' . "\n" . 
'ご注文の受付状況及びご連絡事項を、下記にご案内申し上げます。');
define('TABLE_HEADING_COMMENTS_ADMIN', '[ご連絡事項]');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'受付状況に関してご質問等がございましたら、当店宛にご連絡頂きますようお願い申し' . "\n"
.'上げます。' . "\n\n"
. EMAIL_SIGNATURE);
define('ADD_A_NEW_PRODUCT', '商品の追加');
define('CHOOSE_A_CATEGORY', ' --- 商品カテゴリの選択 --- ');
define('SELECT_THIS_CATECORY', 'カテゴリ選択実行');
define('CHOOSE_A_PRODUCT', ' --- 商品の選択 --- ');
define('SELECT_THIS_PRODUCT', '商品選択実行');
define('NO_OPTION_SKIPPED', 'オプションはありません - スキップします....');
define('SELECT_THESE_OPTIONS', 'オプション選択実行');
define('SELECT_QUANTITY', ' 数量');
define('SELECT_ADD_NOW', '追加実行');
define('SELECT_STEP_ONE', 'STEP 1:');
define('SELECT_STEP_TWO', 'STEP 2:');
define('SELECT_STEP_THREE', 'STEP 3:');
define('SELECT_STEP_FOUR', 'STEP 4:');
define('TEXT_CODE_HANDLE_FEE', '手数料:');
define('EDIT_ORDERS_UPDATE_NOTICE', '変更したい内容を慎重に入力してください。<b>空白などの余分な文字が入力されていないかチェックするように！</b>');
define('EDIT_ORDERS_ID_TEXT', '注文番号:');
define('EDIT_ORDERS_DATE_TEXT', '注文日:');
define('EDIT_ORDERS_CUSTOMER_NAME', '顧客名:');
define('EDIT_ORDERS_EMAIL', 'メールアドレス:');
define('EDIT_ORDERS_PAYMENT_METHOD', '支払方法:');
define('EDIT_ORDERS_FETCHTIME', '取引日時:');
define('EDIT_ORDERS_TORI_TEXT', 'オプション:');
define('EDIT_ORDERS_CUSTOMER_NAME_READ', '<font color="red">※</font>&nbsp;性と名の間には<font color="red">半角スペース</font>を入力してください。');
define('EDIT_ORDERS_PAYMENT_METHOD_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;コピペ用:</td><td>銀行振込</td><td>クレジットカード決済</td><td>銀行振込(買い取り)</td><td>ゆうちょ銀行（郵便局）</td><td>コンビニ決済</td></tr></table>');
define('EDIT_ORDERS_FETCHTIME_READ', '<font color="red">※</font>&nbsp;日付・時間の書式:&nbsp;2008-01-01 10:30:00');
define('EDIT_ORDERS_TORI_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;コピペ用:</td><td>指定した時間どおりに取引して欲しい</td><td>指定した時間より早くできるなら早く来て欲しい</td></tr></table>');
define('EDIT_ORDERS_PRO_LIST_TITLE', '2. 注文商品');
define('TABLE_HEADING_NUM_PRO_NAME', '数量 / 商品名');
define('TABLE_HEADING_CURRENICY', '税率');
define('TABLE_HEADING_PRICE_BEFORE', '価格(税別)');
define('TABLE_HEADING_PRICE_AFTER', '価格(税込)');
define('TABLE_HEADING_TOTAL_BEFORE', '合計(税別)');
define('TABLE_HEADING_TOTAL_AFTER', '合計(税込)');
define('EDIT_ORDERS_DUMMY_TITLE', 'キャラ名：');
define('EDIT_ORDERS_ADD_PRO_READ', '商品追加と他の項目は同時に変更できません。<b>「 商品の追加 」は単体で行ってください。</b>');
define('EDIT_ORDERS_FEE_TITLE_TEXT', '3. ポイント割引、手数料、値引き');
define('TABLE_HEADING_FEE_MUST', '注意事項');
define('EDIT_ORDERS_OTTOTAL_READ', '合計金額が合っているか必ず確認してください。');
define('EDIT_ORDERS_OTSUBTOTAL_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;コピペ用:</td><td>調整額</td><td>事務手数料</td><td>値引き</td></tr></table>');
define('EDIT_ORDERS_TOTALDETAIL_READ', 'このお客様はゲストです。ポイント割引の入力はできません。');
define('EDIT_ORDERS_TOTALDETAIL_READ_ONE', '値引きする場合は、−（マイナス）符号を入力してください。');
define('EDIT_ORDERS_PRICE_CONSTRUCT_READ', '<font color="red">ヒント:</font>&nbsp;価格構成要素を削除する場合は金額に「0」と入力して更新してください。');
define('EDIT_ORDERS_CONFIRMATION_READ', '<font color="red">重要:</font>&nbsp;<b>価格構成要素を変更した場合は「<font color="red">注文内容確認</font>」ボタンをクリックして合計金額が一致するか確認してください。&nbsp;⇒</b>');
define('EDIT_ORDERS_CONFIRM_BUTTON', '注文内容確認');
define('EDIT_ORDERS_ITEM_FOUR_TITLE', '4. 注文ステータス、コメント通知');
define('EDIT_ORDERS_SEND_MAIL_TEXT', 'メール送信:');
define('EDIT_ORDERS_RECORD_TEXT', 'コメント記録:   ');
define('EDIT_ORDERS_RECORD_READ', '←ここはチェックしないように');
define('EDIT_ORDERS_RECORD_ARTICLE', 'こちらに入力した文章はメール本文に挿入されます。');
define('EDIT_ORDERS_ITEM_FIVE_TITLE', '5. データを更新');
define('EDIT_ORDERS_FINAL_CONFIRM_TEXT', '最終確認はしましたか？');
define('EDIT_ORDERS_FINAL_CONFIRM_TEMPLATE', '<table cellspacing="0" cellpadding="2" width="100%"><tr class="smalltext"><td valign="top" colspan="3"><font color="red">※</font>&nbsp;コピペ用フレーズです。トリプルクリックをすると全選択できます。</td></tr> <tr bgcolor="#999999" class="smalltext"><td>商品の変更</td><td>支払方法の変更（販売用）</td><td>支払方法の変更（販売用）</td></tr> <tr bgcolor="#cccccc" class="smalltext"> <td valign="top">弊社のキャラクター名は【】となります。</td> <td valign="top"> 下記の金融機関へお振り込みください。<br> ------------------------------------------<br> 銀行名　　：　ジャパンネット銀行<br> 支店名　　：　本店営業部<br> 口座種別　：　普通<br> 口座名　　：　カ）アイアイエムワイ<br> 口座番号　：　1164394<br> ------------------------------------------<br> 銀行名　　：　イーバンク銀行<br> 支店名　　：　ワルツ支店<br> 支店番号　：　204<br> 口座名　　：　カ）アイアイエムワイ<br> 口座番号　：　7003965<br> ------------------------------------------<br> ※ 必ずご注文時に入力したお名前でお振り込みください。<br> ※ 振込手数料はお客様のご負担となります。<br> ※ お振り込みはご注文から７日以内にお願いいたします。<br> ※ ご入金を株式会社iimyが確認した時点でご契約の成立となります。 </td> <td valign="top"> 10分程度でお客様専用のクレジットカード決済URLを電子メールにてご連絡い<br> たします。<br> メール本文に記載していますURLをクリックし、クレジットカード決済を完了<br> してください。 </td> </tr> </table>');
define('EDIT_ORDERS_PRO_DUMMY_NAME', 'キャラクター名:');
?>
