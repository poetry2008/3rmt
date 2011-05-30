<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
define('NAVBAR_TITLE', 'アカウントがタイムアウトです');
define('HEADING_TITLE', 'メール認証失敗');
define('ACCOUNT_TIMEOUT_COMMENT', 'メール認証に失敗しました。考えられる原因は、以下のとおりです。<br><br>①、URLの有効期限が過ぎていた。（URLの有効期限は72時間）<br>②、既にメール認証が完了している。<br>③、無効のURLを使用した。 <br><br>もう一度メール認証をしていただくか、こちらから<a href="'.tep_href_link('open.php?products_name=メール認証について').'">お問い合わせください。</a>');
?>
