<?php
/*
  $Id$
*/

// 楽天銀行
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_TITLE', '楽天銀行');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_DESCRIPTION', '楽天銀行(代金に手数料が連動)');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_PROCESS', 'LAWSON、スリーエフ、MINI STOP、サークルK、SUNKUSがご利用いただけます。<br>30,000円未満の決済の場合200円、30,000円以上の決済の場合は350円の手数料が別途必要となります。');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_PROCESS_CON', 'LAWSON、スリーエフ、MINI STOP、サークルK、SUNKUSがご利用いただけます。');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_FOOTER', '※ SmartPitシートの送信は、弊社営業時間内に行っております。');
  /*
  define('MODULE_PAYMENT_RAKUTEN_BANK_IMG_FOOTER', '
  <table width="100%" cellspacing="3" cellpadding="0" border="0" id="convenience_img">
  <tr>
  <td align="center"><img src="images/rmt_cr.gif"></td>
  </tr>
  </table>
  ');
  */
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_FEE', '楽天銀行手数料:');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_OVERFLOW_ERROR', 'お買い上げ金額が楽天銀行の制限を超えたためお取り扱いできません。');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_MAILFOOTER',C_RAKUTEN_BANK."\n\n". TELNUMBER_SIGNATURE);
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_TELNUMBER_FOOTER', '');
  
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_SID', '取引コード:');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ZIP_CODE', '郵便番号:');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ADDRESS', '住所:');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_NAME', '氏名:');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_L_NAME', '姓:');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_F_NAME', '名:');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_TEL', '電話番号:');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE', '楽天銀行の処理中にエラーが発生しました。入力内容を確認し、再度入力してください。');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE_NOE', '電話番号と電話番号(確認用)が一致しませんでした。');
  define('MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE_NOM', 'ご入力された電話番号は正しくないですが。電話番号をご入力ください。');
  define('MODULE_PAYMENT_RAKUTEN_TELNUMBER_TEXT', '電話番号:');
  define('MODULE_PAYMENT_RAKUTEN_TELNUMBER_CONFIRMATION_TEXT', '電話番号(確認用):');
  define('MODULE_PAYMENT_RAKUTEN_MUST_INPUT', '<small><font color="#AE0E30">(必須)</font></small>');
  define('MODULE_PAYMENT_RAKUTEN_INFO_TEXT', '下記入力欄に電話番号をご記入ください。<br>');
