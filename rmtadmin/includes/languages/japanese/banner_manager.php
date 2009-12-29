<?php
/*
  $Id: banner_manager.php,v 1.7 2003/05/06 12:10:00 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'バナー管理');

define('TABLE_HEADING_BANNERS', 'バナー');
define('TABLE_HEADING_GROUPS', 'グループ');
define('TABLE_HEADING_STATISTICS', '表示 / クリック');
define('TABLE_HEADING_STATUS', 'ステータス');
define('TABLE_HEADING_ACTION', '操作');

define('TEXT_BANNERS_TITLE', 'バナー・タイトル:');
define('TEXT_BANNERS_URL', 'バナー URL:');
define('TEXT_BANNERS_GROUP', 'バナー・グループ:');
define('TEXT_BANNERS_NEW_GROUP', ' または下に新しいバナー・グループを登録');
define('TEXT_BANNERS_IMAGE', '画像ファイル:');
define('TEXT_BANNERS_IMAGE_LOCAL', ' または下にサーバ上の画像ファイル名を入力');
define('TEXT_BANNERS_IMAGE_TARGET', '画像の保存先:');
define('TEXT_BANNERS_HTML_TEXT', 'HTML テキスト:');
define('TEXT_BANNERS_EXPIRES_ON', '終了日:');
define('TEXT_BANNERS_OR_AT', ' または');
define('TEXT_BANNERS_IMPRESSIONS', '表示回数');
define('TEXT_BANNERS_SCHEDULED_AT', '開始日:');
define('TEXT_BANNERS_BANNER_NOTE', '<b>バナーについて:</b><ul><li>バナーには、画像かHTMLテキストのいずれかを使用します。両方は使用できません。</li><li>HTMLテキストが画像よりも優先されます。</li></ul>');
define('TEXT_BANNERS_INSERT_NOTE', '<b>画像について:</b><ul><li>バナー画像保存先ディレクトリには、適切な書き込み権限を与えてください。</li><li>ウェブサーバにバナー画像をアップロードしない場合は、&quot;画像の保存先&quot; 欄は入力しないでください 。(この場合は、サーバ側の画像を使用することになります)</li><li>&quot;画像の保存先&quot; を指定する場合は、存在するディレクトリ、またはディレクトリを先に作成しておく必要があります。また、ディレクトリの末尾にスラッシュが必要となります。(例: banners/)</li></ul>');
define('TEXT_BANNERS_EXPIRCY_NOTE', '<b>終了日について:</b><ul><li>終了日と表示回数の２つの入力欄のうち、ひとつだけが登録されます。</li><li>バナーを自動的に終了させない場合は、これらの欄を空欄のままにしてください。</li></ul>');
define('TEXT_BANNERS_SCHEDULE_NOTE', '<b>開始日について:</b><ul><li>開始日が登録されると、バナーは登録された日付から有効になります。</li><li>開始日が登録されたバナーは、開始日が来るまで表示されません。</li></ul>');

define('TEXT_BANNERS_DATE_ADDED', '登録日:');
define('TEXT_BANNERS_SCHEDULED_AT_DATE', '開始日: <b>%s</b>');
define('TEXT_BANNERS_EXPIRES_AT_DATE', '終了日: <b>%s</b>');
define('TEXT_BANNERS_EXPIRES_AT_IMPRESSIONS', '終了日: <b>%s</b> クリック回数');
define('TEXT_BANNERS_STATUS_CHANGE', 'ステータス変更: %s');

define('TEXT_BANNERS_DATA', '回<br>数');
define('TEXT_BANNERS_LAST_3_DAYS', '最近３日間');
define('TEXT_BANNERS_BANNER_VIEWS', 'バナー表示');
define('TEXT_BANNERS_BANNER_CLICKS', 'バナー・クリック');

define('TEXT_INFO_DELETE_INTRO', '本当にこのバナーを削除しますか？');
define('TEXT_INFO_DELETE_IMAGE', 'バナー画像も削除');

define('SUCCESS_BANNER_INSERTED', "成功: バナーが挿入されました。");
define('SUCCESS_BANNER_UPDATED', "成功: バナーが更新されました。");
define('SUCCESS_BANNER_REMOVED', "成功: バナーが削除されました。");
define('SUCCESS_BANNER_STATUS_UPDATED', "成功: バナーのステータスが更新されました。");

define('ERROR_BANNER_TITLE_REQUIRED', "エラー: バナーのタイトルが必要です。");
define('ERROR_BANNER_GROUP_REQUIRED', "エラー: バナーのグループが必要です。");
define('ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST', "エラー: 保存先ディレクトリが存在しません。");
define('ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE', "エラー: 保存先ディレクトリに書き込みができません: %s");
define('ERROR_IMAGE_DOES_NOT_EXIST', "エラー: 画像が存在しません。");
define('ERROR_IMAGE_IS_NOT_WRITEABLE', "エラー: 画像が削除できません。");
define('ERROR_UNKNOWN_STATUS_FLAG', "エラー: 不明なステータスです。");

define('ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST', "エラー:  'graphs' ディレクトリが存在しません。 'images' ディレクトリ内に'graphs'ディレクトリを作成してください。");
define('ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE', "エラー:  'images/graphs' ディレクトリに書き込みができません。正しいユーザ権限を設定してください。");
?>