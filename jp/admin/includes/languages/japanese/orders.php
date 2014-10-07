<?php
/*
  $Id$
*/

define('HEADING_TITLE', '注文管理');
define('HEADING_TITLE_SEARCH', '注文ID:');
define('HEADING_TITLE_STATUS', 'ステータス:');

define('TEXT_TRANSACTION_FINISH', '取引完了');
define('TABLE_HEADING_COMMENTS', 'コメント');
define('TABLE_HEADING_CUSTOMERS', '顧客名');
define('TABLE_HEADING_ORDER_TOTAL', '注文総額');
define('TABLE_HEADING_DATE_PURCHASED', '注文日');
define('TABLE_HEADING_STATUS', 'ステータス');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_QUANTITY', '数量');
define('TABLE_HEADING_PRODUCTS_MODEL', '型番');
define('TABLE_HEADING_PRODUCTS', '数量 / 商品名');
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

define('EMAIL_TEXT_STORE_CONFIRMATION', ' へのご注文、誠にありがとうございます。' . "\n\n"
.'ご注文の受付状況及びご連絡事項を、下記にご案内申し上げます。');
define('EMAIL_TEXT_STORE_CONFIRMATION_FOOTER', 
'受付状況に関してご質問等がございましたら、当店宛にご連絡頂きますようお願い申し' . "\n"
.'上げます。' . "\n\n"
. EMAIL_SIGNATURE);

define('ENTRY_EMAIL_TITLE', 'メールタイトル：');
define('TEXT_CODE_HANDLE_FEE', '手数料:');
define('TEXT_SHIPPING_FEE','配送料:');

// old oa 
define('TEXT_ORDER_ANSWER','Order Answer');
define('TEXT_BUY_BANK','買取：銀行支払');
define('TEXT_SELL_BANK','販売：銀行振込');
define('TEXT_SELL_CARD','販売：クレカ');
define('TEXT_CREDIT_FIND','信用調査');

define('TEXT_ORDER_SAVE','保存');
define('TEXT_ORDER_TEST_TEXT','試験運用中<font color="red">（上記の数値と一致しているか確認するように）</font>買取コピペ用:');
define('TEXT_MAIL_CONTENT_INFO',' 自動的に改行して表示し、送信されるメールにも改行が入ります。');
define('TEXT_ORDER_COPY','コピペ用:');
define('TEXT_ORDER_LOGIN','ただ今よりログインいたします。');
define('TEXT_ORDER_SEND_MAIL','メール送信');
define('TEXT_ORDER_STATUS','ステータス通知');
define('TEXT_ORDER_HAS_ERROR','間違い探しはしましたか？');
define('TEXT_ORDER_FIND','検索 :');
define('TEXT_ORDER_AMOUNT_SEARCH','注文金額検索');
define('TEXT_ORDER_FIND_SELECT','--------選択してください--------');
define('TEXT_ORDER_FIND_NAME','名前から検索');
define('TEXT_ORDER_FIND','検索 :');
define('TEXT_ORDER_FIND_PRODUCT_NAME','商品名から検索');
define('TEXT_ORDER_FIND_MAIL_ADD','メールアドレスから検索');
define('TEXT_ORDER_QUERYER_NAME','確認者名:');
define('TEXT_EDIT_MAIL_TEXT','メール本文編集');
define('TEXT_SELECT_MORE','複数の選択はできません。');
define('TEXT_ORDER_SELECT','注文書はまだ選択していません。');
define('TEXT_ORDER_WAIT','取引待ち');
define('TEXT_ORDER_CARE','取り扱い注意');
define('TEXT_ORDER_WHOLESALE','卸業者');
define('TEXT_ORDER_CUSTOMER_INFO','顧客情報');
define('TEXT_ORDER_HISTORY_ORDER','過去の注文');
define('TEXT_ORDER_NEXT_ORDER','次の注文');
define('TEXT_ORDER_ORDER_DATE','お届け日時');
define('TEXT_ORDER_MIX','混');
define('TEXT_ORDER_BUY','買');
define('TEXT_ORDER_SELL','売');
define('TEXT_ORDER_NOTICE','【注意】');
define('TEXT_ORDER_AUTO_RUN_ON','現在自動リロード機能が有効になっています　→ ');
define('TEXT_ORDER_AUTO_POWER_OFF','無効にする');
define('TEXT_ORDER_AUTO_RUN_OFF','現在自動リロード機能が無効になっています　→ ');
define('TEXT_ORDER_AUTO_POWER_ON','有効にする');
define('TEXT_ORDER_SHOW_LIST','一覧に表示する');
define('TEXT_ORDER_STATUS_SET','注文ステータス設定');
define('TEXT_ORDER_CSV_OUTPUT','CSVエクスポート');
define('TEXT_ORDER_DAY','日');
define('TEXT_ORDER_MONTH','月');
define('TEXT_ORDER_YEAR','年');
define('TEXT_ORDER_END_DATE','終了日:');
define('TEXT_ORDER_START_DATE','開始日:');
define('TEXT_ORDER_SITE_TEXT','注文書サイト');
define('TEXT_ORDER_SERVER_BUSY','ダウンロード中はサーバに対して高負荷となります。アクセスの少ない時間に実行してください。');
define('TEXT_ORDER_DOWNLOAD','注文データエクスポート');

define('DEL_CONFIRM_PAYMENT_TIME', '削除');
define('NOTICE_DEL_CONFIRM_PAYEMENT_TIME', '時間を削除しますか？');
define('NOTICE_DEL_CONFIRM_PAYMENT_TIME_SUCCESS', '削除成功');

//for function
define('TEXT_FUNCTION_INPUT_FINISH','入力済み');
define('TEXT_FUNCTION_NOTICE','取扱注意');
define('TEXT_FUNCTION_HAVE_HISTORY','メモ有り');
define('TEXT_FUNCTION_PAYMENT_METHOD','支払方法：');
define('TEXT_FUNCTION_DATE_STRING','Y年n月j日');
define('TEXT_FUNCTION_UN_GIVE_MONY','入金まだ');
define('TEXT_FUNCTION_UN_GIVE_MONY_DAY','入金日：');
define('TEXT_FUNCTION_OPTION','オプション：');
define('TEXT_FUNCTION_CATEGORY','商品：');
define('TEXT_FUNCTION_FINISH','「入」');
define('TEXT_FUNCTION_UNFINISH','「未」');
define('TEXT_FUNCTION_NUMBER','個数：');
define('TEXT_FUNCTION_NUM','個');
define('TEXT_FUNCTION_PC','PC：');
define('ORDERS_STATUS_SELECT_PRE', 'ステータス「');
define('ORDERS_STATUS_SELECT_LAST', '」から検索');
define('TEXT_ORDER_FIND_OID', '注文番号から検索');
define('TEXT_SORT_ASC','▲');
define('TEXT_SORT_DESC','▼');
define('ORDERS_PAYMENT_METHOD_PRE', '支払方法「');
define('ORDERS_PAYMENT_METHOD_LAST', '」から検索');

define('TEXT_ORDER_TYPE_PRE', '注文種別「');
define('TEXT_ORDER_TYPE_LAST', '」から検索');
define('TEXT_ORDER_TYPE_SELL', '販売');
define('TEXT_ORDER_TYPE_BUY', '買取');
define('TEXT_ORDER_TYPE_MIX', '混合');
define('TEXT_ORDER_HISTORY_FROM_ORDER', '注文');
define('TEXT_ORDER_HISTORY_FROM_PREORDER', '予約');
define('TEXT_SHIPPING_METHOD','配達方法');
define('TEXT_SHIPPING_ADDRESS','配達先');
define('SHOW_MANUAL','マニュアル');
define('SHOW_MANUAL_TITLE','のマニュアル');
define('SHOW_MANUAL_SEARCH','検索');
define('SHOW_MANUAL_NONE','マニュアルが設定されていません！！！');
define('SHOW_MANUAL_RETURN','戻る');
define('SEARCH_MANUAL_PRODUCTS_FAIL','検索されたマニュアルがありません！！！');
define('SEARCH_CAT_PRO_TITLE','カテゴリー / 商品');
define('SEARCH_MANUAL_CONTENT','マニュアル');
define('SEARCH_MANUAL_LOOK','操作');
define('MANUAL_SEARCH_HEAD', 'の検索結果');
define('MANUAL_SEARCH_EDIT', '編集');
define('MANUAL_SEARCH_NORES','現在マニュアルは登録されていません... ');
define('TEXT_NO_RECEIVABLES','入金まだ');
define('TEXT_YEN','円');
define('TEXT_HOUR','時');
define('TEXT_MIN','分');
define('TEXT_TWENTY_FOUR_HOUR','　（24時間表記）');
define('TEXT_SEND_MAIL','送信済：');
define('TEXT_ORDERS_ID','注文ID');
define('TEXT_OF','の');
define('TEXT_PAYMENT_NOTICE','支払通知*');
define('TEXT_INPUT_ONE_TIME_PASSWORD','ワンタイムパスワードを入力してください');
define('TEXT_INPUT_PASSWORD_ERROR','パスワードが違います');
define('TEXT_STATUS_HANDLING_WARNING','取り扱い注意');
define('TEXT_STATUS_WAIT_TRADE','取引待ち');
define('TEXT_STATUS_READY_ENTER','入力済み');
define('TEXT_SITE_ORDER_FORM','注文書サイト:');
define('TEXT_TRADE_DATE','お届け日時:');
define('TEXT_ORDERS_OID','ご注文番号:');
define('TEXT_ORDERS_DATE','注文日:');
define('TEXT_CUSTOMER_CLASS','顧客種別:');
define('TEXT_GUEST','ゲスト');
define('TEXT_MEMBER','会員');
define('TEXT_CREATE_NEW_NUMBER_SEARCH','問合番号を新規作成します');
define('TEXT_EMAIL_ADDRESS','メール');
define('TEXT_TEL_UNKNOW','クレカ');
define('TEXT_ADDRESS_INFO','住所情報');
define('TEXT_IP_ADDRESS','IPアドレス:');
define('TEXT_HOST_NAME','ホスト名:');
define('TEXT_USER_AGENT','ユーザーエージェント:');
define('TEXT_BROWSER_TYPE','ブラウザの種類:');
define('TEXT_BROWSER_LANGUAGE','ブラウザの言語:');
define('TEXT_PC_LANGUAGE','パソコンの言語環境:');
define('TEXT_USERS_LANGUAGE','ユーザーの言語環境:');
define('TEXT_SCREEN_RESOLUTION','画面の解像度:');
define('TEXT_SCREEN_COLOR','画面の色:');
define('TEXT_FLASH_VERSION','Flashのバージョン:');
define('TEXT_CART_INFO','クレジットカード情報');
define('TEXT_CART_HOLDER','カード名義:');
define('TEXT_TEL_NUMBER','電話番号:');
define('TEXT_EMAIL_ADDRESS_INFO','メールアドレス:');
define('TEXT_PRICE','金額:');
define('TEXT_COUNTRY_CODE','居住国:');
define('TEXT_PAYER_STATUS','認証:');
define('TEXT_PAYMENT_STATUS','支払ステータス:');
define('TEXT_PAYMENT_TYPE','支払タイプ:');
define('TEXT_SAVE','保存');
define('TEXT_POINT','割引　　　　　　：-');
define('TEXT_HANDLE_FEE','手数料　　　　　：');
define('TEXT_PAYMENT_AMOUNT','お支払金額　　　：');
define('TEXT_TRANSACTION_FEE','決済手数料');
define('TEXT_REPLACE_HANDLE_FEE','手数料');
define('ORDERS_PRODUCTS', '注文商品');
define('QTY_NUM', '個数');
define('ORDERS_NUM_UNIT', '個');
define('PRODUCT_SINGLE_PRICE', '単価');
define('TEXT_CHARACTER_NAME_SEND_MAIL','※ 当社キャラクター名は、お取引10分前までに電子メールにてお知らせいたします。');
define('TEXT_CHARACTER_NAME_CONFIRM_SEND_MAIL','※ 当社キャラクター名は、お支払い確認後に電子メールにてお知らせいたします。');
define('ORDER_TOP_MANUAL_TEXT', 'トップ');
define('ORDER_MANUAL_ALL_SHOW', '続きを読む');
define('ORDER_MANUAL_ALL_HIDE', 'たたむ');
?>
