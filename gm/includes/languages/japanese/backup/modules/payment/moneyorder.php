<?php
/*
	JP、GM共通ファイル
*/

  define('MODULE_PAYMENT_MONEYORDER_TEXT_TITLE', '銀行振込');// '代金先払い'/'郵便振替'/'現金書留'に変更して使用できます
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EXPLAIN', 'ジャパンネット銀行、イーバンク銀行またはセブン銀行へお振り込み。振込手数料はお客様のご負担となります。');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION',  nl2br(C_BANK));
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER', C_BANK."\n\n". EMAIL_SIGNATURE);	//Add Japanese osCommerce
?>
