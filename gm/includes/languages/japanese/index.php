<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

define('TEXT_MAIN', 'このページは、オンラインショップのデモンストレーションです。<b>ご購入になった商品は、配送も請求もされません。</b>商品その他についての情報は全て架空のものです。<br><br>もし、あなたがこのオンラインショップ・デモをダウンロードして使ってみたいと思ったり、このプロジェクトに貢献しようと思ったなら、ぜひ<a href="http://oscommerce.com"><u> サポートサイト </u></a>を訪れてください。このオンラインショップは、<font color="#f0000"><b>' . PROJECT_VERSION . '</b></font>で構築されています。<br><br>今表示されている（読んでいる）テキストは、次のファイルに記述されています。例:[catalogディレクトリ]/includes/languages/[japanese]/default.php.<br><br>これは、管理メニュー経由、各種ツール-言語定義->[language]->Defineオプション、または各種ツール->ファイル管理機能を使うことによって手動で編集することができます。');
define('TABLE_HEADING_NEW_PRODUCTS', '新着商品-RMTゲームマネー');
define('TABLE_HEADING_UPCOMING_PRODUCTS', '入荷予定の商品');
define('TABLE_HEADING_DATE_EXPECTED', '入荷予定日');

define('HEADING_COLOR_TITLE', 'カラーから選択: ');
if (!isset($_GET['colors'])) $_GET['colors']= NULL;
if ( ($category_depth == 'products') || ($_GET['manufacturers_id']) ||  ($_GET['colors'])) {
  define('HEADING_TITLE', '取扱い商品');
  define('TABLE_HEADING_IMAGE', '');
  define('TABLE_HEADING_MODEL', '型番');
  define('TABLE_HEADING_PRODUCTS', '商品名');
  define('TABLE_HEADING_MANUFACTURER', 'メーカー');
  define('TABLE_HEADING_QUANTITY', '数量');
  define('TABLE_HEADING_PRICE', '価格');
  define('TABLE_HEADING_WEIGHT', '重量');
  define('TABLE_HEADING_BUY_NOW', '今すぐ購入');
  //define('TEXT_NO_PRODUCTS', 'このカテゴリーの商品はありません...');
  define('TEXT_NO_PRODUCTS2', 'このメーカーの商品はありません...');
  define('TEXT_NUMBER_OF_PRODUCTS', '在庫数: ');
  define('TEXT_SHOW', '<b>絞込み:</b> ');
  define('TEXT_BUY', '今すぐ ');
  define('TEXT_NOW', ' を購入する');	
  define('TEXT_ALL', '全て');
  define('TEXT_NO_COLORS', 'このカラーの商品はありません...');
} elseif ($category_depth == 'top') {
  define('HEADING_TITLE', 'What\'s New!');
} elseif ($category_depth == 'nested') {
  define('HEADING_TITLE', 'カテゴリー');
}
?>
