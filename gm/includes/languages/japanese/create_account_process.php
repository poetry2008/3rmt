<?php
/*
  $Id$
*/

define('NAVBAR_TITLE_1', '会員登録');
define('NAVBAR_TITLE_2', '手続き');
define('HEADING_TITLE', 'お客様情報');

define('EMAIL_SUBJECT', '会員登録が完了いたしました【'.STORE_NAME.'】');
define('EMAIL_GREET_MR', stripslashes($_POST['lastname']) . ' ' . stripslashes($_POST['firstname']) . ' 様 ' . "\n\n");
define('EMAIL_GREET_MS', stripslashes($_POST['lastname']) . ' ' . stripslashes($_POST['firstname']) . ' 様 ' . "\n\n");
define('EMAIL_GREET_NONE', stripslashes($_POST['lastname']) . ' ' . stripslashes($_POST['firstname']) . ' 様 ' . "\n\n");
define('EMAIL_WELCOME', STORE_NAME.'への会員登録が完了いたしました。' . "\n"
. '誠にありがとうございます。' . "\n\n");
define('EMAIL_CONTACT',
'ご不明な点がございましたら「'.STORE_NAME.'」までお問い合わせください。' . "\n\n\n\n");
define('EMAIL_WARNING',
'[ご連絡・お問い合わせ先]━━━━━━━━━━━━' . "\n"
. COMPANY_NAME . "\n"
. SUPPORT_EMAIL_ADDRESS."\n"
. HTTP_SERVER. "\n"
. '━━━━━━━━━━━━━━━━━━━━━━━' . "\n");

define('EMAIL_NAME_COMMENT_LINK', ' 様 ');
define('ENTRY_PASSWORD_ENGLISH','英字（abcdef...z）が1文字以上必要です。英字と数字を組み合わせて設定してください。');
?>
