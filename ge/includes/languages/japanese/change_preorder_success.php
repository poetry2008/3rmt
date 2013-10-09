<?php
/*
  $Id$
*/
define('CPREORDER_SUCCESS_HEADING_TITLE', 'ご注文の手続きが完了しました!!');
define('CPREORDER_SUCCESS_NAVBAR_TITLE', 'ご注文の手続きが完了しました!!');
define('CPREORDER_SUCCESS_NAVBAR_TITLE_FETCH', '注文');
define('CPREORDER_SUCCESS_NAVBAR_TITLE_CONFIRM', '最終確認');

define('PREORDER_TRADER_LINE_TITLE', '配送方法');
define('PREORDER_CONFIRM_LINE_TITLE', '最終確認');
define('PREORDER_FINISH_LINE_TITLE', '手続完了！');
define('TEXT_HEADER_INFO','<table width="90%" cellspacing="0" cellpadding="0"
    border="0" align="center"> <tbody><tr> <td width="20%"> <table width="100%"
    cellspacing="0" cellpadding="0" border="0"> <tbody><tr> <td width="30%"
    align="right"><img width="1" height="5" alt=""
    src="images/pixel_silver.gif"></td> <td width="70%"><img width="100%" height="1"
    alt="" src="images/pixel_silver.gif"></td> </tr> </tbody></table> </td> <td
    width="60%"> <img width="100%" height="1" alt="" src="images/pixel_silver.gif">
    </td> <td width="20%"> <table width="100%" cellspacing="0" cellpadding="0"
    border="0"> <tbody><tr> <td width="70%"> <img width="100%" height="1" alt=""
    src="images/pixel_silver.gif"> </td> <td width="30%"> <img alt=""
    src="images/checkout_bullet.gif"> </td> </tr> </tbody></table>  </td> </tr> <tr>
    <td width="20%" align="left" class="preorderBarFrom">'.PREORDER_TRADER_LINE_TITLE.'</td> <td
    width="60%" align="center" class="preorderBarFrom">'.PREORDER_CONFIRM_LINE_TITLE.'</td> <td width="20%"
    align="right" class="preorderBarCurrent">'.PREORDER_FINISH_LINE_TITLE.'</td> </tr> </tbody></table>');
define('TEXT_NOTIFY_PRODUCTS', '本日ご注文いただいた商品の最新情報を 電子メールでお届けしております。ご希望の方は、商品ごとにチェックして <b>[次へ進む]</b> を押してください。');
define('TEXT_SEE_ORDERS', 'あなたのご注文履歴は、<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">\'会員\'</a> ページの <a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">\'履歴\'</a>ボタンをクリックするとご覧になれます。');
define('TEXT_CONTACT_STORE_OWNER', 'もしご注文手続きについてご質問がございましたら、直接<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">店主</a>までお問い合わせください。');
