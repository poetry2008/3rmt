<?php
/*
  $Id: affiliate_payment.php,v v 2.00 2003/10/12

  OSC-Affiliate
  
  Contribution based on:
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 - 2003 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '支払い管理');
define('HEADING_TITLE_SEARCH', '検索:');
define('HEADING_TITLE_STATUS','ステータス:');

define('TEXT_ALL_PAYMENTS','すべて表示');
define('TEXT_NO_PAYMENT_HISTORY', '通知履歴は存在しません。');


define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_STATUS', 'ステータス');
define('TABLE_HEADING_AFILIATE_NAME', '会員名');
define('TABLE_HEADING_PAYMENT','支払額（税込み）');
define('TABLE_HEADING_NET_PAYMENT','支払額（税抜き）');
define('TABLE_HEADING_DATE_BILLED','確定日');
define('TABLE_HEADING_NEW_VALUE', '変更後（最新）');
define('TABLE_HEADING_OLD_VALUE', '変更前');
define('TABLE_HEADING_AFFILIATE_NOTIFIED', 'アフィリエイト会員に通知');
define('TABLE_HEADING_DATE_ADDED', '通知日');

define('TEXT_DATE_PAYMENT_BILLED','作成日:');
define('TEXT_DATE_ORDER_LAST_MODIFIED','最終更新日:');
define('TEXT_AFFILIATE_PAYMENT','支払い金額');
define('TEXT_AFFILIATE_BILLED','報酬確定日');
define('TEXT_AFFILIATE','会員名');
define('TEXT_INFO_DELETE_INTRO','この支払いコードを削除してもよろしいですか？');
define('TEXT_DISPLAY_NUMBER_OF_PAYMENTS', '<b>%d</b> 〜 <b>%d</b>を表示 (<b>%d</b> 件中)');

define('TEXT_AFFILIATE_PAYING_POSSIBILITIES','登録されている支払い先:');
define('TEXT_AFFILIATE_PAYMENT_CHECK','Check:');
define('TEXT_AFFILIATE_PAYMENT_CHECK_PAYEE','支払い先:');
define('TEXT_AFFILIATE_PAYMENT_PAYPAL','PayPal:');
define('TEXT_AFFILIATE_PAYMENT_PAYPAL_EMAIL','PayPalアカウントEmail:');
define('TEXT_AFFILIATE_PAYMENT_BANK_TRANSFER','支払い先:');
define('TEXT_AFFILIATE_PAYMENT_BANK_NAME','銀行名:');
define('TEXT_AFFILIATE_PAYMENT_BANK_ACCOUNT_NAME','口座名義人:');
define('TEXT_AFFILIATE_PAYMENT_BANK_ACCOUNT_NUMBER','口座番号:');
define('TEXT_AFFILIATE_PAYMENT_BANK_BRANCH_NUMBER','支店番号:');
define('TEXT_AFFILIATE_PAYMENT_BANK_SWIFT_CODE','支店名:');

define('TEXT_INFO_HEADING_DELETE_PAYMENT','削除');

define('IMAGE_AFFILIATE_BILLING','更新');

define('ERROR_PAYMENT_DOES_NOT_EXIST','支払いレコードが存在しません');


define('SUCCESS_BILLING','更新内容が正常に更新されました');
define('SUCCESS_PAYMENT_UPDATED','支払いステータスは正常に更新されました');

define('PAYMENT_STATUS','支払いステータス');
define('PAYMENT_NOTIFY_AFFILIATE', '会員に通知する');

define('EMAIL_SEPARATOR', '------------------------------------------------------');
define('EMAIL_TEXT_SUBJECT', ''. STORE_NAME . ' アフェリリエイトプログラム【お支払状況更新通知】');
define('EMAIL_TEXT_AFFILIATE_PAYMENT_NUMBER', 'お支払い番号:');
define('EMAIL_TEXT_INVOICE_URL', 'お支払い状況詳細:');
define('EMAIL_TEXT_PAYMENT_BILLED', '報酬確定日');
define('EMAIL_TEXT_STATUS_UPDATE', 'あなたの支払い状況が更新されました。' . "\n\n" . '現在のお支払い状況: %s' . "\n\n" . 'メールの内容についてご質問がある場合はお問合せください。'  . "\n\n"
. EMAIL_SIGNATURE);
define('EMAIL_TEXT_NEW_PAYMENT', 'あなたに対してのお支払い状況が更新されました。' . "\n");
?>
