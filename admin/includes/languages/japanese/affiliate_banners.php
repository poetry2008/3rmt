<?php
/*
  $Id: affiliate_banners.php,v 2.00 2003/10/12

  OSC-Affiliate

  Contribution based on:

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 - 2003 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'バナー管理');

define('TABLE_HEADING_BANNERS', 'バナー');
define('TABLE_HEADING_GROUPS', 'グループ');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_STATISTICS', '表示/クリック');
define('TABLE_HEADING_PRODUCT_ID', '商品コード');

define('TEXT_BANNERS_TITLE', 'バナータイトル:');
define('TEXT_BANNERS_GROUP', 'バナーグループ:');
define('TEXT_BANNERS_NEW_GROUP', ', または下に新しいバナー・グループを登録');
define('TEXT_BANNERS_IMAGE', '画像ファイル:');
define('TEXT_BANNERS_IMAGE_LOCAL', ', または下にサーバ上の画像ファイル名を入力');
define('TEXT_BANNERS_IMAGE_TARGET', '画像の保存先:');
define('TEXT_BANNERS_HTML_TEXT', 'HTMLテキスト:');
define('TEXT_AFFILIATE_BANNERS_NOTE', '<b>エラー:</b><ul><li>バナーとして使用する画像またはHTMLテキストを入力してください。</li><li>HTMLテキストは、イメージに対する優先権を持っています。</li></ul>');

define('TEXT_BANNERS_LINKED_PRODUCT','商品コード');
define('TEXT_BANNERS_LINKED_PRODUCT_NOTE','商品コードを入力することにより、その商品へのダイレクトリンクが作成できます。トップページへのリンクの場合は"0"を入力してください。');

define('TEXT_BANNERS_DATE_ADDED', '登録日:');
define('TEXT_BANNERS_STATUS_CHANGE', '最終更新日: %s');

define('TEXT_AFFILIATE_VALIDPRODUCTS', '商品コードはここをクリック:');
define('TEXT_AFFILIATE_INDIVIDUAL_BANNER_VIEW', '商品コード一覧が表示されます。');
define('TEXT_AFFILIATE_INDIVIDUAL_BANNER_HELP', '新しく開いたウィンドウに商品コードが記載されているので、商品リンクを作成する場合は商品コードを入力してください。');

define('TEXT_VALID_PRODUCTS_LIST', '登録できる商品リスト');
define('TEXT_VALID_PRODUCTS_ID', '商品 #');
define('TEXT_VALID_PRODUCTS_NAME', '商品名');

define('TEXT_CLOSE_WINDOW', '<u>ウィンドウを閉じる</u> [x]');

define('TEXT_INFO_DELETE_INTRO', '本当にバナーを削除してもよろしいですか？');
define('TEXT_INFO_DELETE_IMAGE', 'バナー画像も削除する');

define('SUCCESS_BANNER_INSERTED', 'バナーを登録しました。');
define('SUCCESS_BANNER_UPDATED', 'バナーを更新しました。');
define('SUCCESS_BANNER_REMOVED', 'バナーを削除しました。');

define('ERROR_BANNER_TITLE_REQUIRED', 'エラー：バナー画像は必須です。');
define('ERROR_BANNER_GROUP_REQUIRED', 'エラーバナーグループが選択されていません。');
define('ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST', 'エラー：ディレクトリは存在しません。');
define('ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE', 'エラー：ディレクトリへの書込みができません。');
define('ERROR_IMAGE_DOES_NOT_EXIST', 'エラー：画像は存在しません。');
define('ERROR_IMAGE_IS_NOT_WRITEABLE', 'エラー：画像は削除できません。');
?>