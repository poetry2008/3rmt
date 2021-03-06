<?php
/*
  $Id$
*/

define('HEADING_TITLE', '予約内容編集');
define('HEADING_TITLE_SEARCH', '予約ID:');
define('HEADING_TITLE_STATUS', 'ステータス:');
define('ADDING_TITLE', '商品の追加');

define('ENTRY_UPDATE_TO_CC', '(Update to <b>Credit Card</b> to view CC fields.)');
define('TABLE_HEADING_COMMENTS', 'コメント');
define('TABLE_HEADING_CUSTOMERS', '顧客名');
define('TABLE_HEADING_ORDER_TOTAL', '予約総額');
define('TABLE_HEADING_DATE_PURCHASED', '予約日');
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
define('ENTRY_DATE_PURCHASED', '予約日:');
define('ENTRY_STATUS', 'ステータス:');
define('ENTRY_DATE_LAST_UPDATED', '更新日:');
define('ENTRY_NOTIFY_CUSTOMER', '処理状況を通知:');
define('ENTRY_NOTIFY_COMMENTS', 'コメントを追加:');
define('ENTRY_PRINTABLE', '納品書をプリント');

define('TEXT_INFO_HEADING_DELETE_ORDER', '予約削除');
define('TEXT_INFO_DELETE_INTRO', '本当にこの予約を削除しますか?');
define('TEXT_INFO_RESTOCK_PRODUCT_QUANTITY', '在庫数を元に戻す');
define('TEXT_DATE_ORDER_CREATED', '作成日:');
define('TEXT_DATE_ORDER_LAST_MODIFIED', '更新日:');
define('TEXT_DATE_ORDER_ADDNEW', '商品を追加');
define('TEXT_INFO_PAYMENT_METHOD', '支払方法:');

define('TEXT_ALL_ORDERS', 'すべての予約');
define('TEXT_NO_ORDER_HISTORY', '予約履歴はありません');

define('EMAIL_SEPARATOR', '--------------------------------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', 'ご予約受付状況のお知らせ');
define('EMAIL_TEXT_ORDER_NUMBER', 'ご予約受付番号: ');
define('EMAIL_TEXT_INVOICE_URL', 'ご予約についての情報を下記URLでご覧になれます。' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', 'ご予約日: ');
define('EMAIL_TEXT_STATUS_UPDATE',
'ご予約の受付状況は次のようなっております。' . "\n"
.'現在の受付状況: [ %s ]' . "\n\n");
define('EMAIL_TEXT_COMMENTS_UPDATE', '[ご連絡事項]' . "\n\n%s");

define('ERROR_ORDER_DOES_NOT_EXIST', 'エラー: 予約が存在しません。');
define('SUCCESS_ORDER_UPDATED', '成功: 予約状態が更新されました。');
define('WARNING_ORDER_NOT_UPDATED', '警告: 予約状態はなにも変更されませんでした。');

define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', '商品を選択');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', 'オプションを選択');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', 'オプションはありません: スキップします..');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', '数量');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', '追加');
define('CREATE_ORDER_PRODUCTS_WEIGHT','総重量が規定の範囲を超えました。商品を削除するか、または個数を変更して（');
define('CREATE_ORDER_PRODUCTS_WEIGHT_ONE','）kg以内にしてください。');
define('PRODUCTS_WEIGHT_ERROR_ONE','総重量が（');
define('PRODUCTS_WEIGHT_ERROR_TWO','）の規定の重量を超えました。');
define('PRODUCTS_WEIGHT_ERROR_THREE','商品を削除するか、または個数を変更して（');
define('PRODUCTS_WEIGHT_ERROR_FOUR','）kg以内にしてください。');

define('EMAIL_TEXT_STORE_CONFIRMATION', ' へのご予約、誠にありがとうございます。' . "\n" . 
'ご予約の受付状況及びご連絡事項を、下記にご案内申し上げます。');
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
define('EDIT_ORDERS_UPDATE_NOTICE', '変更したい内容を慎重に入力してください。空白などの余分な文字が入力されていないかチェックするように！');
define('EDIT_ORDERS_ID_TEXT', '予約番号:');
define('EDIT_ORDERS_DATE_TEXT', '予約日:');
define('EDIT_ORDERS_CUSTOMER_NAME', '顧客名:');
define('EDIT_ORDERS_EMAIL', 'メールアドレス:');
define('EDIT_ORDERS_PAYMENT_METHOD', '支払方法:');
define('EDIT_ORDERS_FETCHTIME', 'お届け日時:');
define('EDIT_ORDERS_TORI_TEXT', 'オプション:');
define('EDIT_ORDERS_CUSTOMER_NAME_READ', '<font color="red">※</font>&nbsp;姓と名の間には<font color="red">半角スペース</font>を入力してください。');
define('EDIT_ORDERS_FETCHTIME_READ', 
    '<font color="red">※</font>&nbsp;日付・時間の書式:&nbsp;2008-01-01 10:30:00～10:45:00');
define('EDIT_ORDERS_TORI_READ', '<table><tr class="smalltext"><td><font color="red">※</font>&nbsp;コピペ用:</td><td>指定した時間どおりに取引して欲しい</td><td>指定した時間より早くできるなら早く来て欲しい</td></tr></table>');
define('EDIT_ORDERS_PRO_LIST_TITLE', '2. 予約商品');
define('TABLE_HEADING_NUM_PRO_NAME', '数量 / 商品名');
define('TABLE_HEADING_CURRENICY', '税率');
define('TABLE_HEADING_PRICE_BEFORE', '価格(税別)');
define('TABLE_HEADING_PRICE_AFTER', '価格(税込)');
define('TABLE_HEADING_TOTAL_BEFORE', '合計(税別)');
define('TABLE_HEADING_TOTAL_AFTER', '合計(税込)');
define('EDIT_ORDERS_ADD_PRO_READ', '商品追加と他の項目は同時に変更できません。<b>「 商品の追加 」は単体で行ってください。</b>');
define('EDIT_ORDERS_FEE_TITLE_TEXT', '3. ポイント割引、手数料、値引き');
define('TABLE_HEADING_FEE_MUST', '注意事項');
define('EDIT_ORDERS_OTTOTAL_READ', '合計金額が合っているか必ず確認してください。');
define('EDIT_ORDERS_TOTALDETAIL_READ', 'このお客様はゲストです。ポイント割引の入力はできません。');
define('EDIT_ORDERS_TOTALDETAIL_READ_ONE', '値引きする場合は、−（マイナス）符号を入力してください。');
define('EDIT_ORDERS_CONFIRMATION_READ', '<font color="red">重要:</font>&nbsp;<b>価格構成要素を変更した場合は「<font color="red">予約内容確認</font>」ボタンをクリックして合計金額が一致するか確認してください。&nbsp;⇒</b>');
define('EDIT_ORDERS_CONFIRM_BUTTON', '予約内容確認');
define('EDIT_ORDERS_ITEM_FOUR_TITLE', '4. 予約ステータス、コメント通知');
define('EDIT_ORDERS_SEND_MAIL_TEXT', 'メール送信:');
define('EDIT_ORDERS_RECORD_TEXT', 'コメント記録:');
define('EDIT_ORDERS_RECORD_READ', '←ここはチェックしないように');
define('EDIT_ORDERS_RECORD_ARTICLE', 'こちらに入力した文章はメール本文に挿入されます。');
define('EDIT_ORDERS_ITEM_FIVE_TITLE', '5. データを更新');
define('EDIT_ORDERS_FINAL_CONFIRM_TEXT', '最終確認はしましたか？');
define('EDIT_NEW_ORDERS_CREATE_TITLE', '予約書の作成');
define('EDIT_NEW_ORDERS_CREATE_READ', '【重要】予約編集ではありません。新規予約作成システムです。');
define('EDIT_ORDERS_ORIGIN_VALUE_TEXT', '（初期値）');
define('ERROR_INPUT_PRICE_NOTICE', '単価を書いてください');
define('EDIT_ORDERS_PRICE_UNIT', '円');
define('EDIT_ORDERS_NUM_UNIT', '個');
define('EDIT_ORDERS_PREDATE_TEXT', '有効期限:');
define('ERROR_VILADATE_NEW_PREORDERS', '更新をキャンセルしました。');
define('ERROR_NEW_PREORDERS_POINT', 'ポイントが足りません。入力可能なポイントは <b>%s</b> です。');
define('NOTICE_NEW_PRERODERS_PRODUCTS_DEL', '商品を削除しました。<font color="red">メールは送信されていません。</font>');
define('ERROR_NEW_PREORDERS_UPDATE', 'エラーが発生しました。正常に処理が行われていない可能性があります。');
define('NEW_PREORDERS_NOTE_TEXT', 'メールに記載する注意書き:');
define('TEXT_EMAIL_TITLE','メールタイトル：');
define('TEXT_DATE_NUM_ERROR','お届け日時を正しく入力してください');
define('NOTICE_NEW_PREORDERS_PRODUCTS_DEL','商品を削除しました。');
define('TABLE_HEADING_PRODUCTS_PRICE', '単価');
define('TEXT_REQUIRE','*必須');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_NULL','必須項目');
define('ADDRESS_ERROR_OPTION_ITEM_TEXT_TYPE_WRONG','正しく入力してください');
define('TEXT_SELECT_PAYMENT_ERROR','<font color="red">選択された支払方法は、現在無効になっています。選びなおしてください。</font>');
define('TEXT_NO_PAYMENT_ENABLED','<font color="red">有効な支払方法がありません。</font>');
?>
