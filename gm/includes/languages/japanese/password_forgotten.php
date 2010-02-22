<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', 'ログイン');
define('NAVBAR_TITLE_2', 'パスワード再発行');
define('HEADING_TITLE', 'パスワード再発行手続き');
define('ENTRY_FORGOTTEN_EMAIL_ADDRESS', 'ご登録のメールアドレス:'); // 2003.03.06 nagata Edit Japanese osCommerce
define('TEXT_NO_EMAIL_ADDRESS_FOUND', '<font color="#ff0000"><b>ご注意:</b></font> ご入力されたメールアドレスは見つかりませんでした。もう一度入力してください。');
define('EMAIL_PASSWORD_REMINDER_SUBJECT', STORE_NAME . 'の新しいパスワード');
define('EMAIL_PASSWORD_REMINDER_BODY',
'新しいパスワードの発行依頼が ' . $REMOTE_ADDR . ' からありました。' . "\n\n"
. 'あなたの \'' . STORE_NAME . '\' への新しいパスワードは' . "\n"
. '---------------------------------------------------------------------------' . "\n"
. '   %s' . "\n"
. '---------------------------------------------------------------------------' . "\n"
. 'となります。' . "\n\n"
. 'このメールに関してお心覚えがない場合は、至急当社までご連絡ください。' . "\n\n"
. EMAIL_SIGNATURE);
define('TEXT_PASSWORD_SENT', '新しいパスワードをご登録のメールアドレスに送信しました。');
?>