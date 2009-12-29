<?php
/*
	JP、GM共通ファイル
*/

  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_TITLE', 'ゆうちょ銀行（郵便局）');// '代金先払い'/'郵便振替'/'現金書留'に変更して使用できます
  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_EXPLAIN', '郵便局の窓口およびATMから送金できます。<br>送金手数料はお客様のご負担となります。');
  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_DESCRIPTION',  nl2br(C_POSTAL));
  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_EMAIL_FOOTER', C_POSTAL."\n\n". EMAIL_SIGNATURE);	//Add Japanese osCommerce
?>
