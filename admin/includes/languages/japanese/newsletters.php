<?php
/*
  $Id: newsletters.php,v 1.3 2003/03/03 16:10:29 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'メールマガジン管理');

define('TABLE_HEADING_NEWSLETTERS', 'メールマガジン名');
define('TABLE_HEADING_SIZE', 'サイズ');
define('TABLE_HEADING_MODULE', 'モジュール');
define('TABLE_HEADING_SENT', '送信状態');
define('TABLE_HEADING_STATUS', 'ステータス');
define('TABLE_HEADING_ACTION', '操作');

define('TEXT_NEWSLETTER_MODULE', 'モジュールの選択:');
define('TEXT_NEWSLETTER_TITLE', 'メールマガジンの題名:');
define('TEXT_NEWSLETTER_CONTENT', '内容:');

define('TEXT_NEWSLETTER_DATE_ADDED', '作成日:');
define('TEXT_NEWSLETTER_DATE_SENT', '送信日:');

define('TEXT_INFO_DELETE_INTRO', '本当にこのメールマガジンを削除しますか?');

define('TEXT_PLEASE_WAIT', 'しばらくお待ちください.. メールを送信しています..<br><br>この処理を中断しないでください!');
define('TEXT_FINISHED_SENDING_EMAILS', 'メールの送信を終了しました!');

define('ERROR_NEWSLETTER_TITLE', 'エラー: メールマガジンの題名が必要です。');
define('ERROR_NEWSLETTER_MODULE', 'エラー: メールマガジン・モジュールが必要です。');
define('ERROR_REMOVE_UNLOCKED_NEWSLETTER', 'エラー: 削除するにはメールマガジンのロックが必要です。');
define('ERROR_EDIT_UNLOCKED_NEWSLETTER', 'エラー: 編集するにはメールマガジンのロックが必要です。');
define('ERROR_SEND_UNLOCKED_NEWSLETTER', 'エラー: 送信するにはメールマガジンのロックが必要です。');
?>