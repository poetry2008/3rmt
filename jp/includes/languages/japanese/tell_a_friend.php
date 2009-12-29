<?php
/*
  $Id: tell_a_friend.php,v 1.7 2003/05/06 12:10:03 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE', '友達に知らせる');
define('HEADING_TITLE', '\'%s\' を友達に知らせる');
define('HEADING_TITLE_ERROR', '友達に知らせる');
define('ERROR_INVALID_PRODUCT', '商品が見つかりません...');

define('FORM_TITLE_CUSTOMER_DETAILS', 'お客様について');
define('FORM_TITLE_FRIEND_DETAILS', 'お友だちについて');
define('FORM_TITLE_FRIEND_MESSAGE', 'メッセージ');

define('FORM_FIELD_CUSTOMER_NAME', 'お名前:');
define('FORM_FIELD_CUSTOMER_EMAIL', 'メールアドレス:');
define('FORM_FIELD_FRIEND_NAME', 'お名前("様"や"さん"などの敬称も必要):');
define('FORM_FIELD_FRIEND_EMAIL', 'メールアドレス:');

define('TEXT_EMAIL_SUCCESSFUL_SENT', '<b>%s</b> を紹介するメッセージを <b>%s</b> に送信しました。');

define('TEXT_EMAIL_SUBJECT', 'お友達の %s さんが %s のこの商品をお勧めしています');
define('TEXT_EMAIL_INTRO', 'こんにちは、%s' . "\n\n"
. '＊あなたのお友達の%sさんは、' . "\n"
. '  あなたが [%s] に興味を持つだろうと思って紹介されました。' . "\n"
. '  この商品は %s でご覧になれます。');
define('TEXT_EMAIL_LINK', '＊この商品をご覧になるには、下のリンクをクリックするか、' . "\n"
. '  リンクをブラウザにコピー＆ペーストしてください:' . "\n\n" . '%s');
define('TEXT_EMAIL_SIGNATURE', 'ご来店をお待ちいたします。' . "\n\n" . EMAIL_SIGNATURE);
?>
