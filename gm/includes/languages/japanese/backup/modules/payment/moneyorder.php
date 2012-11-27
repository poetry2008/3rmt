<?php
/*
	JP、GM共同文件
*/

  define('MODULE_PAYMENT_MONEYORDER_TEXT_TITLE', '銀行振込');// 可更改为'预付货款'/'邮政转账'/'现金挂号信'使用
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EXPLAIN', 'ジャパンネット銀行、イーバンク銀行またはセブン銀行へお振り込み。振込手数料はお客様のご負担となります。');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION',  nl2br(C_BANK));
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER', C_BANK."\n\n". EMAIL_SIGNATURE);
?>
