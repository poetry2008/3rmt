<?php
/*
  $Id$
*/

// コンビニ決済
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TITLE', 'コンビニ決済');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_DESCRIPTION', 'コンビニ決済(代金に手数料が連動)');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_PROCESS', 'LAWSON、スリーエフ、MINI STOP、サークルK、SUNKUSがご利用いただけます。<br>30,000円未満の決済の場合200円、30,000円以上の決済の場合は350円の手数料が別途必要となります。');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_PROCESS_CON', 'LAWSON、スリーエフ、MINI STOP、サークルK、SUNKUSがご利用いただけます。');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FOOTER', '※ SmartPitシートの送信は、弊社営業時間内に行っております。');
  /*
  define('MODULE_PAYMENT_CONVENIENCE_STORE_IMG_FOOTER', '
  <table width="100%" cellspacing="3" cellpadding="0" border="0" id="convenience_img">
  <tr>
  <td align="center"><img src="images/rmt_cr.gif"></td>
  </tr>
  </table>
  ');
  */
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_FEE', 'コンビニ決済手数料:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_OVERFLOW_ERROR', 'お買い上げ金額がコンビニ決済の制限を超えたためお取り扱いできません。');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_MAILFOOTER',C_CONVENIENCE_STORE."\n\n". EMAIL_SIGNATURE);
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_EMAIL_FOOTER', '');
  
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_SID', '取引コード:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ZIP_CODE', '郵便番号:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ADDRESS', '住所:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_NAME', '氏名:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_L_NAME', '姓:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_F_NAME', '名:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TEL', '電話番号:');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE', 'コンビニ決済の処理中にエラーが発生しました。入力内容を確認し、再度入力してください。');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE_NOE', 'PCメールアドレスとPCメールアドレス(確認用)が一致しませんでした。');
  define('MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_ERROR_MESSAGE_NOM', 'ご入力されたメールアドレスは登録できません。PCメールをご入力ください。');
  define('MODULE_PAYMENT_CONVENIENCE_EMAIL_TEXT', 'PCメールアドレス:');
  define('MODULE_PAYMENT_CONVENIENCE_EMAIL_CONFIRMATION_TEXT', 'PCメールアドレス(確認用):');
  define('MODULE_PAYMENT_CONVENIENCE_MUST_INPUT', '<small><font color="#AE0E30">(必須)</font></small>');
  define('MODULE_PAYMENT_CONVENIENCE_INFO_TEXT', '携帯メールアドレスはご利用いただけません。下記入力欄にPCメールアドレスをご記入ください。<br>');
