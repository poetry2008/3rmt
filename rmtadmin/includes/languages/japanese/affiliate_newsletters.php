<?php
/*
  $Id: affiliate_newsletters.php,v 2.00 2003/10/12

  OSC-Affiliate

  Contribution based on:

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 - 2003 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'ニュースレター');

define('TABLE_HEADING_NEWSLETTERS', 'ニュースレター名');
define('TABLE_HEADING_SIZE', 'サイズ');
define('TABLE_HEADING_MODULE', '送信内容');
define('TABLE_HEADING_SENT', '送信状態');
define('TABLE_HEADING_STATUS', 'ステータス');
define('TABLE_HEADING_ACTION', '操作');

define('TEXT_NEWSLETTER_MODULE', '送信内容:');
define('TEXT_NEWSLETTER_TITLE', 'タイトル:');
define('TEXT_NEWSLETTER_CONTENT', 'コメント:');

define('TEXT_NEWSLETTER_DATE_ADDED', '登録日:');
define('TEXT_NEWSLETTER_DATE_SENT', '送信日:');

define('TEXT_INFO_DELETE_INTRO', '本当に削除してもよろしいですか？');

define('TEXT_PLEASE_WAIT', 'しばらくお待ちください .. Eメール送信中 ..<br><br>送信が完了するまでページを移動しないでください!');
define('TEXT_FINISHED_SENDING_EMAILS', '正常にメールマガジンは送信されました!');

define('ERROR_NEWSLETTER_TITLE', 'エラー：メールマガジンのタイトルは必須です。');
define('ERROR_NEWSLETTER_MODULE', 'エラー: 送信する件名（内容）が選択されていません。');
define('ERROR_REMOVE_UNLOCKED_NEWSLETTER', 'エラー: 削除する前にロックをしてください。');
define('ERROR_EDIT_UNLOCKED_NEWSLETTER', 'エラー: 編集する前にロックをしてください。');
define('ERROR_SEND_UNLOCKED_NEWSLETTER', 'エラー: 送信する前にロックをしてください。');
?>