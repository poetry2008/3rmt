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
  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_OVERFLOW_ERROR', 'お買い上げ金額がコンビニ決済の制限を超えたためお取り扱いできません。');
  define('MODULE_PAYMENT_POSTALMONEY_ORDER_TEXT_MAILFOOTER', '');
  define('MODULE_PAYMENT_POSTALMONEY_ORDER_TEXT_ERROR_MESSAGE', 'ゆうちょ銀行（郵便局）決済の処理中にエラーが発生しました. 入力内容を訂正しもう一度試してください。　');
