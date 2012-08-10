<?php
/*
  $Id$
*/
define('HEADING_TITLE', '手動注文手続き');
define('HEADING_CREATE', '手動注文する顧客の詳細を確認:'); 

define('TEXT_SELECT_CUST', '顧客選択:'); 
define('TEXT_SELECT_CURRENCY', '通貨選択:');
define('BUTTON_TEXT_SELECT_CUST', '顧客選択:'); 
define('TEXT_OR_BY', 'または顧客ID:'); 
define('TEXT_STEP_1', 'ステップ 1 - 顧客を選択し詳細を確認してください');
define('BUTTON_SUBMIT', '確認する');
define('ENTRY_CURRENCY','決済通貨');
define('CATEGORY_ORDER_DETAILS','通貨設定');

define('CATEGORY_CORRECT', '顧客情報');

define('CREATE_ORDER_STEP_ONE', 'ステップ 1 - 顧客を検索します');
define('CREATE_ORDER_TITLE_TEXT', '登録データの有無を確認:');
define('CREATE_ORDER_SEARCH_TEXT',
    '顧客のメールアドレスを入力し「検索」ボタンをクリックしてください。<br>
    顧客のメールアドレスが、存在する場合自動で顧客情報が入力されます。<br>
    顧客のメールアドレスが存在しない場合は、ゲストアカウント作成ページに移動します。');
define('CREATE_ORDER_EMAIL_TEXT', 'メールアドレス:');
define('CREATE_ORDER_SEARCH_BUTTON_TEXT', '検索');
define('CREATE_ORDER_NOTICE_ONE', '変更があれば修正してください');
define('CREATE_ORDER_CUSTOMERS_ERROR','注文の顧客を指定してください');
define('CREATE_ORDER_PAYMENT_TITLE', '支払方法');
define('CREATE_ORDER_PRODUCTS_ADD_TITLE','商品の追加');
define('ADDPRODUCT_TEXT_STEP', 'ステップ');
define('ADDPRODUCT_TEXT_STEP1', ' &laquo; カテゴリ選択. ');
define('ADDPRODUCT_TEXT_STEP2', ' &laquo; 商品選択. ');
define('ADDING_TITLE', '商品の追加');
define('ADDPRODUCT_TEXT_CATEGORY_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_PRODUCT', '商品を選択');
define('ADDPRODUCT_TEXT_PRODUCT_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_SELECT_OPTIONS', 'オプションを選択');
define('ADDPRODUCT_TEXT_OPTIONS_CONFIRM', 'OK');
define('ADDPRODUCT_TEXT_OPTIONS_NOTEXIST', 'オプションはありません: スキップします..');
define('ADDPRODUCT_TEXT_CONFIRM_QUANTITY', '数量');
define('ADDPRODUCT_TEXT_CONFIRM_ADDNOW', '追加');
define('EDIT_ORDERS_NUM_UNIT', '個');
define('TABLE_HEADING_UNIT_PRICE', '単価');
define('EDIT_ORDERS_PRICE_UNIT', '円');
define('EDIT_ORDERS_PRO_LIST_TITLE', '注文商品');
define('TABLE_HEADING_NUM_PRO_NAME', '数量 / 商品名');
define('TABLE_HEADING_CURRENICY', '税率');
define('TABLE_HEADING_PRODUCTS_PRICE', '単価');
define('TABLE_HEADING_PRICE_BEFORE', '価格(税別)');
define('TABLE_HEADING_PRICE_AFTER', '価格(税込)');
define('TABLE_HEADING_TOTAL_BEFORE', '合計(税別)');
define('TABLE_HEADING_TOTAL_AFTER', '合計(税込)');
define('EDIT_ORDERS_ADD_PRO_READ', '商品追加と他の項目は同時に変更できません。<b>「 商品の追加 」は単体で行ってください。</b>');
define('TABLE_HEADING_PRODUCTS_MODEL', '型番');
define('HINT_DELETE_POSITION', '<font color="#FF0000">ヒント: </font>商品を削除する場合は個数に「0」と入力して更新してください。');
define('CREATE_ORDER_PRODUCTS_WEIGHT','総重量が規定の範囲を超えました。商品を削除するか、または個数を変更して（');
define('CREATE_ORDER_PRODUCTS_WEIGHT_ONE','）kg以内にしてください。');
define('PRODUCTS_WEIGHT_ERROR_ONE','総重量が（');
define('PRODUCTS_WEIGHT_ERROR_TWO','）の規定の重量を超えました。');
define('PRODUCTS_WEIGHT_ERROR_THREE','商品を削除するか、または個数を変更して（');
define('PRODUCTS_WEIGHT_ERROR_FOUR','）kg以内にしてください。');
define('CREATE_ORDER_FETCH_TIME_TITLE_TEXT', 'お届け日時');
define('CREATE_ORDER_FETCH_DATE_TEXT', 'お届け日:');
define('CREATE_ORDER_FETCH_TIME_TEXT', 'お届け時間:');
define('CREATE_ORDER_FETCH_ALLTIME_TEXT', '（24時間表記）');
define('CREATE_ORDER_FETCH_TIME_SELECT_TEXT', 'オプション:');
define('CREATE_ORDER_COMMUNITY_TITLE_TEXT', '当社使用欄');
define('CREATE_ORDER_COMMUNITY_SEARCH_TEXT', ' 信用調査:');
define('CREATE_ORDER_COMMUNITY_SEARCH_READ', '常連客【HQ】&nbsp;&nbsp;注意【WA】&nbsp;&nbsp;発送禁止【BK】');
define('CREATE_ORDER_COMMUNITY_SEARCH_READ_ONE', 'クレカ初回決済日：C2007/01/01&nbsp;&nbsp;&nbsp;&nbsp;エリア一致：Aok&nbsp;&nbsp;&nbsp;&nbsp;本人確認済：Hok&nbsp;&nbsp;&nbsp;&nbsp;YahooID更新日：Y2007/01 /01&nbsp;&nbsp;&nbsp;&nbsp;リファラー：R');
define('CREATE_ORDER_COMMUNITY_SEARCH_READ_TWO', '記入例：WA-Aok-C2007/01/01-Hok-RグーグルFF11 RMT');
define('CREATE_ORDER_SEARCH_TWO_TEXT','業者名を選択し「検索」ボタンをクリックしてください。');
define('CREATE_ORDER_YEZHE_NAME_TEXT', '業者名：');
define('CREATE_ORDER_SHIRU_TEXT', '仕入れ注文');
define('CREATE_ORDER_TEL_TEXT', '電話番号:');
define('TEXT_CREATE_ADDRESS_BOOK','お届け先を指定する');
define('TEXT_USE_ADDRESS_BOOK','登録先に届ける');
define('TEXT_TORIHIKIBOUBI_DEFAULT_SELECT','ご希望のお届け日時を指定してください');
define('ERROR_ORDER_DOES_NOT_EXIST','エラー: 注文が存在しません。');
define('PRODUCT_ERROR','商品がありません。注文作成するには、まず商品を追加してください。');
define('ADDPRODUCT_TEXT_STEP1_TITLE', 'カテゴリ選択:');
define('ADDPRODUCT_TEXT_STEP2_TITLE', '商品選択:');
?>
