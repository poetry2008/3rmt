<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('NAVBAR_TITLE', '詳細検索');
define('HEADING_TITLE', '詳細検索');

define('HEADING_SEARCH_CRITERIA', '検索条件 (キーワード) を入力してください');

define('TEXT_SEARCH_IN_DESCRIPTION', '商品説明からも探す');
define('ENTRY_CATEGORIES', 'カテゴリー:');
define('ENTRY_INCLUDE_SUBCATEGORIES', 'サブカテゴリーも含む');
define('ENTRY_MANUFACTURERS', 'メーカー:');
define('ENTRY_PRICE_FROM', '価格（最低）:');
define('ENTRY_PRICE_TO', '価格（最高）:');
define('ENTRY_DATE_FROM', '登録日（開始日）:');
define('ENTRY_DATE_TO', '登録日（終了日）:');

define('TEXT_SEARCH_HELP_LINK', '<u>詳細検索の使い方</u> [?]');

define('TEXT_ALL_CATEGORIES', '全カテゴリー');
define('TEXT_ALL_MANUFACTURERS', '全メーカー');

define('HEADING_SEARCH_HELP', '詳細検索の使い方');
define('TEXT_SEARCH_HELP', '詳細検索では、単語や文字列を AND や OR で区切ることができます。<br><br>例えば、<u>マイクロソフト AND マウス</u>と入力すると、両方の単語をふくんだ商品を探します。それに対して、<u>マウス OR キーボード</u>と入力すると、どちらかの単語をふくんだ商品を探します。<br><br>いくつかの単語を引用符で囲んで検索すると、入力した文字列に正確に一致するものを探します。<br><br>例えば、<u>"ノート パソコン"</u>として検索すると、そのままの文字列をふくんだ商品を探すことができます。<br><br>括弧を使用して、論理的な組み合わせを指定することができます。<br><br>例えば、<u>マイクロソフト AND (キーボード OR マウス OR "visual basic")</u>として検索すると、"マイクロソフト" + "キーボード"か"マウス"か（正確に!）"visual basic"というキーワードをふくんだ商品を探します。');
define('TEXT_CLOSE_WINDOW', 'ウィンドウを閉じる[x]');

define('JS_AT_LEAST_ONE_INPUT', '* 次のフィールドのいずれかを入力してください:\n    キーワード\n    データ登録日(開始日)\n    データ登録日(終了日)\n    価格(最低)\n    価格(最高)\n');
define('JS_INVALID_FROM_DATE', '* 登録日(開始日)が正しくありません。\n');
define('JS_INVALID_TO_DATE', '* 登録日(終了日)が正しくありません。\n');
define('JS_TO_DATE_LESS_THAN_FROM_DATE', '* 登録日(終了日)は登録日(開始日)以降を入力してください。\n');
define('JS_PRICE_FROM_MUST_BE_NUM', '* 価格(最低)は数字を入力してください。\n');
define('JS_PRICE_TO_MUST_BE_NUM', '* 価格(最高)は数字を入力してください。\n');
define('JS_PRICE_TO_LESS_THAN_PRICE_FROM', '* 価格(最高)は価格(最低)以上を入力してください。\n');
define('JS_INVALID_KEYWORDS', '* キーワードが正しくありません。\n');
?>
