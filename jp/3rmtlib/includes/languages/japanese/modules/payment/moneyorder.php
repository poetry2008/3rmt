<?php
/*
  $Id$
*/

  define('MODULE_PAYMENT_MONEYORDER_TEXT_TITLE', '銀行振込');// '代金先払い'/'郵便振替'/'現金書留'に変更して使用できます
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EXPLAIN', 'ジャパンネット銀行、イーバンク銀行またはセブン銀行へお振り込み。<br>振込手数料はお客様のご負担となります。');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION', 'ジャパンネット銀行、イーバンク銀行またはセブン銀行へお振り込み。<br>振込手数料はお客様のご負担となります。'); 
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER','ジャパンネット銀行、イーバンク銀行またはセブン銀行へお振り込み。<br>振込手数料はお客様のご負担となります。'. EMAIL_SIGNATURE);  //Add Japanese osCommerce
  
  define('MODULE_PAYMENT_MONEY_ORDER_TEXT_FEE', '銀行振込決済手数料:');
  define('MODULE_PAYMENT_MONEY_ORDER_TEXT_PROCESS', '銀行振込決済手数料が別途かかります。');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_OVERFLOW_ERROR', 'お買い上げ金額がコンビニ決済の制限を超えたためお取り扱いできません。');
  define('MODULE_PAYMENT_MONEY_ORDER_TEXT_MAILFOOTER', '');
  define('MODULE_PAYMENT_MONEY_ORDER_TEXT_ERROR_MESSAGE', '銀行振込決済の処理中にエラーが発生しました. 入力内容を訂正しもう一度試してください。　');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_CONFIRMATION',"下記いずれかの口座へお振り込みください。
------------------------------------------
銀行名　　：　ジャパンネット銀行
支店名　　：　本店営業部
口座種別　：　普通
口座名義　：　カ）アイアイエムワイ
口座番号　：　1164394
------------------------------------------
銀行名　　：　楽天銀行（旧イーバンク銀行）
支店名　　：　ワルツ支店
口座種別　：　普通
口座名義　：　カ）アイアイエムワイ
口座番号　：　7003965
------------------------------------------
銀行名　　：　セブン銀行
支店名　　：　アイリス支店
口座種別　：　普通
口座名義　：　イイマ　ユキオ
口座番号　：　0560153
------------------------------------------
※ 必ずご注文時に入力したお名前でお振り込みください。
※ 振込手数料はお客様のご負担となります。
※ お振り込みはご注文から７日以内にお願いいたします。
※ ご入金を株式会社iimyが確認した時点でご契約の成立となります。
");