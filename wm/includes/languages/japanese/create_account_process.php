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
define('EMAIL_TEXT',
'-----------------------------------------------------------------------' . "\n"
. '□　ご利用案内' . "\n"
. '-----------------------------------------------------------------------' . "\n"
. 'ユーザーID：　お客様のメールアドレスがIDとなります。' . "\n"
. 'パスワード：　*****' . "\n\n"
. 'ログインページ：' . "\n"
. '→ http://rmt.worldmoney.jp/login.php' . "\n\n"
. 'パスワードの再発行は、コチラからできます。' . "\n"
. '→ http://rmt.worldmoney.jp/password_forgotten.php' . "\n\n"
. 'セキュリティ上、ご登録時のパスワードを記載しておりません。' . "\n"
. 'パスワードを忘れた場合は、上記URLから再発行してください。' . "\n\n"
. '-----------------------------------------------------------------------' . "\n"
. '□　会員様限定の特典' . "\n"
. '-----------------------------------------------------------------------' . "\n"
. STORE_NAME.'は、会員様に次のようなサービスをご提供いたします。' . "\n\n"
. '■お買い物に使えるポイントサービス' . "\n"
. '　当サイトでお買い物をされました購入金額の1%をポイントとして還元してお' . "\n"
. '　ります。' . "\n"
. '　溜まったポイントは次回のお買い物に1ポイント＝1円として使えます。' . "\n"
. '　ポイントの有効期限は最終購入日より50日間有効です。' . "\n\n"
. '■専用ショッピングカート' . "\n"
. '　カートに入れた商品は、削除するか注文するまで残ります。' . "\n\n"
. '■ご注文履歴' . "\n"
. '　当サイトへログインすることにより、ご注文されました商品の履歴を閲覧する' . "\n"
. '　ことができます。' . "\n"
. '-----------------------------------------------------------------------' . "\n\n");
define('EMAIL_CONTACT',
'ご不明な点がございましたら「'.STORE_NAME.'」までお問い合わせください。' . "\n\n\n\n");
define('EMAIL_WARNING',
'[ご連絡・お問い合わせ先]━━━━━━━━━━━━' . "\n"
. '株式会社iimy' . "\n"
. SUPPORT_EMAIL_ADDRESS."\n"
. 'http://rmt.worldmoney.jp/' . "\n"
. '━━━━━━━━━━━━━━━━━━━━━━━' . "\n");

define('EMAIL_NAME_COMMENT_LINK', ' 様 ');
define('ENTRY_PASSWORD_ENGLISH','英字（abcdef...z）が1文字以上必要です。英字と数字を組み合わせて設定してください。');
?>
