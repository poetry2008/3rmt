<?php
/*
  $Id$
*/

  define('MODULE_PAYMENT_MONEYORDER_TEXT_TITLE', '銀行振込');// '代金先払い'/'郵便振替'/'現金書留'に変更して使用できます
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EXPLAIN', 'ジャパンネット銀行、イーバンク銀行またはセブン銀行へお振り込み。<br>振込手数料はお客様のご負担となります。');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION',  nl2br(C_BANK));
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER', C_BANK."\n\n". EMAIL_SIGNATURE);  //Add Japanese osCommerce
  
  define('MODULE_PAYMENT_MONEY_ORDER_TEXT_FEE', '銀行振込決済手数料:');
  define('MODULE_PAYMENT_MONEY_ORDER_TEXT_PROCESS', '銀行振込決済手数料が別途かかります。');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_OVERFLOW_ERROR', 'お買い上げ金額がコンビニ決済の制限を超えたためお取り扱いできません。');
  define('MODULE_PAYMENT_MONEY_ORDER_TEXT_MAILFOOTER', '');
  define('MODULE_PAYMENT_MONEY_ORDER_TEXT_ERROR_MESSAGE', '銀行振込決済の処理中にエラーが発生しました. 入力内容を訂正しもう一度試してください。　');
