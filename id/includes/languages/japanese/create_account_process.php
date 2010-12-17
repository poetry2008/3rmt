<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE_1', 'アカウントの作成');
define('NAVBAR_TITLE_2', '手続き');
define('HEADING_TITLE', 'お客様情報');

define('EMAIL_SUBJECT', '会員登録が完了いたしました【RMTアイテムデポ】');
define('EMAIL_GREET_MR', stripslashes($_POST['lastname']) . ' ' . stripslashes($_POST['firstname']) . ' 様 ' . "\n\n");
define('EMAIL_GREET_MS', stripslashes($_POST['lastname']) . ' ' . stripslashes($_POST['firstname']) . ' 様 ' . "\n\n");
define('EMAIL_GREET_NONE', stripslashes($_POST['lastname']) . ' ' . stripslashes($_POST['firstname']) . ' 様 ' . "\n\n");
define('EMAIL_WELCOME', 'RMTアイテムデポへの会員登録が完了いたしました。' . "\n"
. '誠にありがとうございます。' . "\n\n");
define('EMAIL_TEXT',
'-----------------------------------------------------------------------' . "\n"
. '□　ご利用案内' . "\n"
. '-----------------------------------------------------------------------' . "\n"
. 'ユーザーID：　お客様のメールアドレスがIDとなります。' . "\n"
. 'パスワード：　*****' . "\n\n"
. 'ログインページ：' . "\n"
. '→ http://www.itemdepot.jp/login.php' . "\n\n"
. 'パスワードの再発行は、コチラからできます。' . "\n"
. '→ http://www.itemdepot.jp/password_forgotten.php' . "\n\n"
. 'セキュリティ上、ご登録時のパスワードを記載しておりません。' . "\n"
. 'パスワードを忘れた場合は、上記URLから再発行してください。' . "\n\n"
. '-----------------------------------------------------------------------' . "\n"
. '□　会員様限定の特典' . "\n"
. '-----------------------------------------------------------------------' . "\n"
. 'RMTアイテムデポは、会員様に次のようなサービスをご提供いたします。' . "\n\n"
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
'ご不明な点がございましたら「RMTアイテムデポ」までお問い合わせください。' . "\n\n\n\n");
define('EMAIL_WARNING',
'[ご連絡・お問い合わせ先]━━━━━━━━━━━━' . "\n"
. '株式会社iimy' . "\n"
. 'support@itemdepot.jp'."\n"
. 'http://www.itemdepot.jp/' . "\n"
. '━━━━━━━━━━━━━━━━━━━━━━━' . "\n");
?>
