<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '予約管理');
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
define('TEXT_CODE_HANDLE_FEE', '手数料:');

// old oa 
define('TEXT_ORDER_ANSWER','Order Answer');
define('TEXT_BUY_BANK','買取：銀行支払');
define('TEXT_SELL_BANK','販売：銀行振込');
define('TEXT_SELL_CARD','販売：クレカ');
define('TEXT_CREDIT_FIND','信用調査');

define('TEXT_ORDER_SAVE','保存');
define('TEXT_ORDER_TEST_TEXT','試験運用中<font color="red">（上記の数値と一致しているか確認するように）</font>買取コピペ用:');
define('TEXT_FEE_TEXT','この注文は5,000円未満です。買取なら手数料168円引く');
define('TEXT_MAIL_CONTENT_INFO',' 自動的に改行して表示し、送信されるメールにも改行が入ります。');
define('TEXT_ORDER_COPY','コピペ用:');
define('TEXT_ORDER_LOGIN','ただ今よりログインいたします。');
define('TEXT_ORDER_SEND_MAIL','メール送信');
define('TEXT_ORDER_STATUS','ステータス通知');
define('TEXT_ORDER_HAS_ERROR','間違い探しはしましたか？');
define('TEXT_ORDER_FIND','検索 :');
define('TEXT_ORDER_FIND_SELECT','--選択してください--');
define('TEXT_ORDER_FIND_NAME','名前');
define('TEXT_ORDER_FIND','検索 :');
define('TEXT_ORDER_FIND_PRODUCT_NAME','商品名');
define('TEXT_ORDER_FIND_MAIL_ADD','メールアドレス');
define('TEXT_ORDER_QUERYER_NAME','確認者名:');
define('TEXT_ORDER_OK_ORDER_NIMBER','済 受付番号:');
define('TEXT_ORDER_BANK','銀行:');
define('TEXT_ORDER_JNB','JNB');
define('TEXT_ORDER_EBANK','eBank');
define('TEXT_ORDER_POST_BANK','ゆうちょ');
define('TEXT_EDIT_MAIL_TEXT','メール本文編集');
define('TEXT_SELECT_MORE','複数の選択はできません。');
define('TEXT_ORDER_SELECT','注文書はまだ選択していません。');
define('TEXT_ORDER_WAIT','取引待ち');
define('TEXT_ORDER_CARE','取り扱い注意');
define('TEXT_ORDER_OROSHI','卸業者');
define('TEXT_ORDER_CUSTOMER_INFO','顧客情報');
define('TEXT_ORDER_HISTORY_ORDER','過去の注文');
define('TEXT_ORDER_NEXT_ORDER','次の注文');
define('TEXT_ORDER_ORDER_DATE','取引日');
define('TEXT_ORDER_CONVENIENCE','コンビニ決済');
define('TEXT_ORDER_CREDIT_CARD','クレジットカード決済');
define('TEXT_ORDER_POST','ゆうちょ銀行（郵便局）');
define('TEXT_ORDER_BANK_REMIT_MONEY','銀行振込');
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
define('TEXT_ORDER_DOWNLOPAD','注文データダウンロード');

define('DEL_CONFIRM_PAYMENT_TIME', '削除');
define('NOTICE_DEL_CONFIRM_PAYEMENT_TIME', '時間を削除しますか？');
define('NOTICE_DEL_CONFIRM_PAYMENT_TIME_SUCCESS', '削除成功');
define('NOTICE_ORDER_ID_TEXT', '注文ID');
define('NOTICE_ORDER_ID_LINK_TEXT', 'の');
define('NOTICE_ORDER_INPUT_PASSWORD', 'ワンタイムパスワードを入力してください\r\n');
define('NOTICE_ORDER_INPUT_WRONG_PASSWORD', 'パスワードが違います');
define('TEXT_ORDER_INPUTED_FLAG', '入力済み');
define('TEXT_ORDER_DATE_LONG', '有効期限:');
define('TEXT_ORDER_HOUHOU', 'オプション:');
define('TEXT_PREORDER_ID_TEXT', '予約番号:');
define('TEXT_PREORDER_DAY', '予約日:');
define('TEXT_ORDER_CUSTOMER_TYPE', '顧客種別:');
define('TEXT_ORDER_CUSTOMER_VIP', '会員');
define('TEXT_ORDER_GUEST', 'ゲスト');
define('TEXT_ORDER_CONCAT_OID_CREATE', '問合番号を新規作成します');
define('TEXT_ORDER_EMAIL_LINK', 'メール');
define('TEXT_ORDER_CREDIT_LINK', 'クレカ');
define('TEXT_ORDER_IP_ADDRESS', 'IPアドレス:');
define('TEXT_ORDER_HOSTNAME', 'ホスト名:');
define('TEXT_ORDER_USERAGENT', 'ユーザーエージェント:');
define('TEXT_ORDER_OS', 'OS:');
define('TEXT_ORDER_BROWSER_INFO', 'ブラウザの種類:');
define('TEXT_ORDER_HTTP_LAN', 'ブラウザの言語:');
define('TEXT_ORDER_SYS_LAN', 'パソコンの言語環境:');
define('TEXT_ORDER_USER_LAN', 'ユーザーの言語環境:');
define('TEXT_ORDER_SCREEN_RES', '画面の解像度:');
define('TEXT_ORDER_COLOR_DEPTH', '画面の色:');
define('TEXT_ORDER_FLASH_VERS', 'Flashのバージョン:');
define('TEXT_ORDER_CREDITCARD_TITLE', 'クレジットカード情報');
define('TEXT_ORDER_CREDITCARD_NAME', 'カード名義:');
define('TEXT_ORDER_CREDITCARD_TEL', '電話番号:');
define('TEXT_ORDER_CREDITCARD_EMAIL', 'メールアドレス:');
define('TEXT_ORDER_CREDITCARD_MONEY', '金額:');
define('TEXT_ORDER_CREDITCARD_COUNTRY', '居住国:');
define('TEXT_ORDER_CREDITCARD_STATUS', '認証:');
define('TEXT_ORDER_CREDITCARD_PAYMENTSTATUS', '支払ステータス:');
define('TEXT_ORDER_CREDITCARD_PAYMENTTYPE', '支払タイプ:');
define('ENTRY_ENSURE_DATE', '確保期限:');
define('TEXT_ORDER_EXPECTET_COMMENT', '要望:');
?>
