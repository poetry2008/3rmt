<?php
/*
  $Id: cache.php,v 1.4 2003/05/06 12:10:00 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'キャッシュコントロール');

define('TABLE_HEADING_CACHE', 'キャッシュ・ブロック');
define('TABLE_HEADING_DATE_CREATED', '作成日');
define('TABLE_HEADING_ACTION', 'ファイル削除');

define('TEXT_FILE_DOES_NOT_EXIST', 'ファイルが存在しません');
define('TEXT_CACHE_DIRECTORY', 'キャッシュ・ディレクトリ:');

define('ERROR_CACHE_DIRECTORY_DOES_NOT_EXIST', 'エラー: キャッシュ・ディレクトリが存在しません。includes/configure.php の設定を確認してください。');
define('ERROR_CACHE_DIRECTORY_NOT_WRITEABLE', 'エラー: キャッシュ・ディレクトリに書き込みができません。正しいユーザ権限を設定してください。');
?>