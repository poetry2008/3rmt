<?php
/*
	JP、GM共同文件
*/

  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_TITLE', 'ゆうちょ銀行（郵便局）');// 可更改为'预付货款'/'邮政转账'/'现金挂号信'使用
  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_EXPLAIN', '郵便局の窓口およびATMから送金できます。<br>送金手数料はお客様のご負担となります。');
  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_DESCRIPTION',  nl2br(C_POSTAL));
  define('MODULE_PAYMENT_POSTALMONEYORDER_TEXT_EMAIL_FOOTER', C_POSTAL."\n\n". EMAIL_SIGNATURE);
?>
