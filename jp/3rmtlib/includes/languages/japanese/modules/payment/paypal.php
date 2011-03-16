<?php
/*
  $Id$
  PAYPAL module
*/

  define('MODULE_PAYMENT_PAYPAL_TEXT_TITLE', 'ペイパル決済');
  define('MODULE_PAYMENT_PAYPAL_TEXT_DESCRIPTION', 'カード情報の入力画面ではフリーメールアドレスのご入力をご遠慮ください。');
  define('MODULE_PAYMENT_PAYPAL_TEXT_EXPLAIN','
  
PayPal (ペイパル) は、世界中で利用されているオンライン決済システムです。どなたでも簡単な手続きで安全・簡単にクレジット決済での支払いが可能です。
<br>
<br>
<font color="red">ペイパル決済は試験導入中です。正常に決済が行えない場合は、「クレジットカード決済」をご利用ください。</font>

  
  ');
  define('MODULE_PAYMENT_PAYPAL_TEXT_ERROR', 'クレジットカードエラー');
  define('MODULE_PAYMENT_PAYPAL_TEXT_ERROR_MESSAGE', 'エラーが発生しました. もう一度試してください。');
  define('MODULE_PAYMENT_PAYPAL_TEXT_EMAIL_FOOTER', C_CC."\n\n".EMAIL_SIGNATURE);
  define('MODULE_PAYMENT_PAYPAL_TEXT_OVERFLOW_ERROR', 'お買い上げ金額がコンビニ決済の制限を超えたためお取り扱いできません。');
  define('MODULE_PAYMENT_PAYPAL_TEXT_FEE', 'クレジットカード決済決済手数料:');
  define('MODULE_PAYMENT_PAYPAL_TEXT_PROCESS', 'クレジットカード決済手数料が別途かかります。');
