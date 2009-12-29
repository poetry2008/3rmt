<?php
/*
  $Id: privacy.php,v 1.3 2003/05/06 12:10:02 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE1', 'プレゼント');
define('NAVBAR_TITLE2', 'プレゼント商品');
define('NAVBAR_TITLE3', 'プレゼント応募');

define('HEADING_TITLE', 'プレゼント応募確認画面');
define('TEXT1', STORE_NAME.'では、会員登録をされていなくてもプレゼントに応募できます。 下のフォームにお客様のメールアドレス、お名前、ご住所、電話番号を入力してください。<br><font color="red">住所にはプレセントが当選した場合の配送先の住所を入力してください。</font>');
define('TEXT_PRESENT_ERROR_NOT_SELECTED','プレゼント商品が選択されていません');
define('HEADING_RETURNING_CUSTOMER', 'アカウントをお持ちのお客様');
define('TEXT_RETURNING_CUSTOMER', 'メールアドレスとパスワードを入力して、ログインしてください。');
define('ENTRY_EMAIL_ADDRESS', 'メールアドレス:');
define('ENTRY_PASSWORD', 'パスワード:');

define('TEXT_PASSWORD_FORGOTTEN', 'パスワードをお忘れの場合はクリック!');

define('TEXT_LOGIN_ERROR', '<font color="#ff0000"><b>エラー:</b></font> &quot;メールアドレス&quot; または &quot;パスワード&quot; が一致しませんでした。');
define('TEXT_VISITORS_CART', '<font color="#ff0000"><b>ご注意:</b></font> ログインすると、[ショッピングカート] の商品は [メンバーズ・ショッピングカート] へ自動的に移動します。 <a href="javascript:session_win();"> [詳細情報]</a>');
?>