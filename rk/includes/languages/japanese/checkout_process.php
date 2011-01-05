<?php
/*
  $Id$
*/

define('EMAIL_TEXT_SUBJECT', 'ご注文ありがとうございます【RMTカメズ】　');
define('EMAIL_TEXT_SUBJECT2','ご注文ありがとうございます【RMTカメズ】　');

define('EMAIL_TEXT_STORE_CONFIRMATION', ' へご注文をいただきまして、誠にありがとうございます。' . "\n\n" . 
'下記の内容にてご注文を承りましたので、ご確認ください。' . "\n\n" . 
'なお、本メールに記載されたご注文内容の誤りや、ご質問等がございましたら、' . "\n" .
'お手数ですが早急に当店までお問い合わせくださいますよう、お願い申し上げます。' . "\n"); //Add Japanese osCommerce

define('EMAIL_TEXT_ORDER_NUMBER', 'ご注文受付番号:');
define('EMAIL_TEXT_INVOICE_URL', 'ご注文についての情報を下記URLでご覧になれます。' . "\n");
define('EMAIL_TEXT_DATE_ORDERED', 'ご注文日:');
define('EMAIL_TEXT_PRODUCTS', '数量 / 商品名');
define('EMAIL_TEXT_SUBTOTAL', '小　計:');
define('EMAIL_TEXT_TAX', '消費税:');
define('EMAIL_TEXT_SHIPPING', '送　料:');
define('EMAIL_TEXT_TOTAL', '合計額:');
define('EMAIL_TEXT_DELIVERY_ADDRESS', 'お届け先');
define('EMAIL_TEXT_BILLING_ADDRESS', 'ご請求先');
define('EMAIL_TEXT_PAYMENT_METHOD', 'お支払い方法');

define('EMAIL_SEPARATOR', '---------------------------------------------------------------------------');
define('TEXT_EMAIL_VIA', '(配送方法)');

//Add Point System
define('TEXT_POINT_NOW', '今回の獲得ポイント:');

//在庫切れアラート
define('ZAIKO_ALART_TITLE','在庫が切れました。');
define('ZAIKO_ALART_TITLE2','オプション在庫が切れました。');
define('ZAIKO_ARART_BODY',
'商品在庫が切れています。管理画面にログインしていただき
在庫を増やしていただくか、商品を削除してください。現在
商品はオンライン上からは非表示になっています。

在庫を増やし、表示ステータスをONにすると再度、表示され
ます。以下のリストは在庫がゼロの商品です。
'.EMAIL_SEPARATOR."\n");

define('TEXT_BANK_NAME', '金融機関名　　　　');
define('TEXT_BANK_SHITEN', '支店名　　　　　　');
define('TEXT_BANK_KAMOKU', '口座種別　　　　　');
define('TEXT_BANK_KOUZA_NUM', '口座番号　　　　　');
define('TEXT_BANK_KOUZA_NAME', '口座名義　　　　　');


?>
