<?php
/*
  $Id$
*/

  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_TITLE', 'ゆうちょ銀行（郵便局）');// '代金先払い'/'郵便振替'/'現金書留'に変更して使用できます
  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_EXPLAIN', '郵便局の窓口およびATMから送金できます。<br>送金手数料はお客様のご負担となります。');
  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_DESCRIPTION',  nl2br(C_POSTAL));
  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_EMAIL_FOOTER', C_POSTAL."\n\n". EMAIL_SIGNATURE);  //Add Japanese osCommerce
  define('MODULE_PAYMENT_POSTALMONEY_ORDER_TEXT_FEE', 'ゆうちょ銀行（郵便局）決済手数料:');
  define('MODULE_PAYMENT_POSTALMONEY_ORDER_TEXT_PROCESS', 'ゆうちょ銀行（郵便局）決済手数料が別途かかります。');
  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_OVERFLOW_ERROR','お買い上げ金額がゆうちょ銀行（郵便局）の制限を超えたためお取り扱いできません。');
  define('MODULE_PAYMENT_POSTALMONEY_ORDER_TEXT_MAILFOOTER', '');
  define('MODULE_PAYMENT_POSTALMONEY_ORDER_TEXT_ERROR_MESSAGE', 'ゆうちょ銀行（郵便局）決済の処理中にエラーが発生しました. 入力内容を訂正しもう一度試してください。　');
  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_CONFIRMATION', 'ゆうちょ銀行から送金する場合は下記の口座へ
------------------------------------------
銀行名　　：　ゆうちょ銀行 
口座名義　：　カ）アイアイエムワイ
記号番号　：　16350-15995881
------------------------------------------
他の銀行から振り込む場合は下記の口座へ
銀行名　　：　ゆうちょ銀行 
支店名　　：　六三八（ロクサンハチ）
口座種別　：　普通
口座名義　：　カ）アイアイエムワイ
口座番号　：　1599588
------------------------------------------
※ 必ずご注文時に入力したお名前で送金してください。
※ 送金手数料はお客様のご負担となります。
※ 取引日時までにお支払いができない場合は、必ずご連絡ください。
　 ご連絡がない場合、在庫引き当てを解除することがあります。
※ 送金はご注文から７日以内にお願いいたします。
※ ご入金を株式会社iimyが確認した時点でご契約の成立となります。');
