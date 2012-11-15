<?php
/*
  $Id$
*/

// 楽天銀行
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_TITLE', '楽天銀行');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_DESCRIPTION', '楽天銀行(代金に手数料が連動)');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_FIELDS_DESCRIPTION', '下記入力欄に電話番号をご記入ください');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_EXPLAIN','楽天銀行へ振り込みする場合は選択してください。<br>振込手数料はお客様のご負担となります。');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_PROCESS_CON', 'LAWSON、スリーエフ、MINI STOP、サークルK、SUNKUSがご利用いただけます。');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_FOOTER', '※ SmartPitシートの送信は、弊社営業時間内に行っております。');
  /*
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_IMG_FOOTER', '
  <table width="100%" cellspacing="3" cellpadding="0" border="0" id="convenience_img">
  <tr>
  <td align="center"><img src="images/rmt_cr.gif"></td>
  </tr>
  </table>
  ');
  */
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_FEE', '楽天銀行手数料:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_OVERFLOW_ERROR', 'お買い上げ金額が楽天銀行の制限を超えたためお取り扱いできません。');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_MAILFOOTER',nl2br(C_RAKUTEN_BANK."\n\n"));
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_TELNUMBER_FOOTER', '');
  
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_SID', '取引コード:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ZIP_CODE', '郵便番号:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ADDRESS', '住所:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_NAME', '氏名:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_L_NAME', '姓:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_F_NAME', '名:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_TEL', '電話番号:');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE', '楽天銀行の処理中にエラーが発生しました。入力内容を確認し、再度入力してください。');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE_NOE', '電話番号と電話番号(確認用)が一致しませんでした。');
  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE_NOM', '入力エラー、電話番号の形式が間違っているか使用できない文字が含まれています。入力内容を確認し、再度入力してください。');
  define('TS_MODULE_PAYMENT_RAKUTEN_TELNUMBER_TEXT', '電話番号:');
  define('TS_MODULE_PAYMENT_RAKUTEN_TELNUMBER_CONFIRMATION_TEXT', '電話番号(確認用):');
  if(NEW_STYLE_WEB===true){
  define('TS_MODULE_PAYMENT_RAKUTEN_MUST_INPUT', '<span><font color="red">*必須</font></span>');
  }else{
   define('TS_MODULE_PAYMENT_RAKUTEN_MUST_INPUT', '<small><font color="#AE0E30">(*必須)</font></small>');
  }

  define('TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_CONFIRMATION', '
電話番号:#TELNUMBER#

下記の口座へお振り込みください。
------------------------------------------
銀行名　　：　楽天銀行（旧イーバンク銀行）
支店名　　：　ワルツ支店
口座種別　：　普通
口座名義　：　カ）アイアイエムワイ
口座番号　：　7003965
------------------------------------------
※ 必ずご注文時に入力したお名前でお振り込みください。
※ 振込手数料はお客様のご負担となります。
※ 取引日時までにお支払いができない場合は、必ずご連絡ください。
　 ご連絡がない場合、在庫引き当てを解除することがあります。
※ お振り込みはご注文から7日以内にお願いいたします。
※ ご入金を'.COMPANY_NAME.'が確認した時点でご契約の成立となります。
');
define('TS_MODULE_PAYMENT_RAKUTEN_NORMAL', '普通');
