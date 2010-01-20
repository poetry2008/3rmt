<?php
/*
  $Id: currencies.php,v 1.4 2003/05/06 12:10:00 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '通貨設定');

define('TABLE_HEADING_CURRENCY_NAME', '通貨');
define('TABLE_HEADING_CURRENCY_CODES', 'コード');
define('TABLE_HEADING_CURRENCY_VALUE', '設定値');
define('TABLE_HEADING_ACTION', '操作');

define('TEXT_INFO_EDIT_INTRO', '必要な変更を加えてください');
define('TEXT_INFO_CURRENCY_TITLE', 'タイトル:');
define('TEXT_INFO_CURRENCY_CODE', 'コード:');
define('TEXT_INFO_CURRENCY_SYMBOL_LEFT', '左側シンボル:');
define('TEXT_INFO_CURRENCY_SYMBOL_RIGHT', '右側シンボル:');
define('TEXT_INFO_CURRENCY_DECIMAL_POINT', '小数点:');
define('TEXT_INFO_CURRENCY_THOUSANDS_POINT', '3桁ごとの区切り:');
define('TEXT_INFO_CURRENCY_DECIMAL_PLACES', '小数点位置:');
define('TEXT_INFO_CURRENCY_LAST_UPDATED', '更新日:');
define('TEXT_INFO_CURRENCY_VALUE', '設定値:');
define('TEXT_INFO_CURRENCY_EXAMPLE', '表示例:');
define('TEXT_INFO_INSERT_INTRO', '新しい通貨と関連するデータを入力してください');
define('TEXT_INFO_DELETE_INTRO', '本当にこの通貨を削除しますか?');
define('TEXT_INFO_HEADING_NEW_CURRENCY', '新しい通貨');
define('TEXT_INFO_HEADING_EDIT_CURRENCY', '通貨を編集');
define('TEXT_INFO_HEADING_DELETE_CURRENCY', '通貨を削除');
define('TEXT_INFO_SET_AS_DEFAULT', TEXT_SET_DEFAULT . ' (通貨の値を直接入力して更新する必要があります)');

define('ERROR_REMOVE_DEFAULT_CURRENCY', 'エラー: デフォルトの通貨は削除できません。他の通貨をデフォルトに設定して、もう一度操作してください。');
?>