<?php
/*
  $Id$
*/

define('NAVBAR_TITLE_1', 'ショッピングカート');
define('NAVBAR_TITLE_2', '手続完了');

define('HEADING_TITLE', 'ご注文の手続きが完了しました!!');

define('TEXT_NOTIFY_PRODUCTS', '本日ご注文いただいた商品の最新情報を 
電子メールでお届けしております。ご希望の方は、商品ごとにチェックして <b>[次へ進む]</b> を押してください。');
define('TEXT_SEE_ORDERS', 'あなたのご注文履歴は、<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">\'会員\'</a> ページの <a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">\'履歴\'</a>ボタンをクリックするとご覧になれます。');
define('TEXT_CONTACT_STORE_OWNER', 'もしご注文手続きについてご質問がございましたら、直接<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">店主</a>までお問い合わせください。');
define('TEXT_THANKS_FOR_SHOPPING', 'ご注文ありがとうございました。');

define('TABLE_HEADING_COMMENTS', 'ご注文についてのコメント');

define('TABLE_HEADING_DOWNLOAD_DATE', 'ダウンロード有効期限: ');
define('TABLE_HEADING_DOWNLOAD_COUNT', ' 回ダウンロードできます');
define('HEADING_DOWNLOAD', 'こちらから商品をダウンロードしてください:');
define('FOOTER_DOWNLOAD', '後で [%s] ページから商品をダウンロードすることもできます。');
define('TEXT_HEADER_INFO','<table width="100%" cellspacing="0" cellpadding="0"
    border="0"> <tbody><tr> <td width="20%"> <table width="100%" cellspacing="0"
    cellpadding="0" border="0"> <tbody><tr> <td width="50%" align="right"><img
    width="1" height="5" src="images/pixel_silver.gif" alt=""></td> <td
    width="50%"><img width="100%" height="1" src="images/pixel_silver.gif"
    alt=""></td> </tr> </tbody></table> </td> <td width="20%"><img width="100%"
    height="1" src="images/pixel_silver.gif" alt=""></td> <td width="20%"><img
    width="100%" height="1" src="images/pixel_silver.gif" alt=""></td> <td
    width="20%"><img width="100%" height="1" src="images/pixel_silver.gif"
    alt=""></td> <td width="20%"> <table width="100%" cellspacing="0"
    cellpadding="0" border="0"> <tbody><tr> <td width="50%"><img width="100%"
    height="1" src="images/pixel_silver.gif" alt=""></td> <td width="50%"><img
    src="images/checkout_bullet.gif" alt=""></td> </tr> </tbody></table> </td> </tr>
    <tr> <td width="20%" align="center" class="checkoutBarFrom">商品オプション</td>
    <td width="20%" align="center" class="checkoutBarFrom">配送方法</td> <td
    width="20%" align="center" class="checkoutBarFrom">支払方法</td> <td width="20%"
    align="center" class="checkoutBarFrom">最終確認</td> <td width="20%"
    align="center" class="checkoutBarCurrent">手続完了!</td> </tr> </tbody></table>');

?>
