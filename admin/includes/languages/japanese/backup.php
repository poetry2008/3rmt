<?php
/*
  $Id: backup.php,v 1.5 2003/05/06 12:10:00 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'データベース・バックアップ管理');

define('TABLE_HEADING_TITLE', 'タイトル');
define('TABLE_HEADING_FILE_DATE', '日付');
define('TABLE_HEADING_FILE_SIZE', 'サイズ');
define('TABLE_HEADING_ACTION', '動作');

define('TEXT_INFO_HEADING_NEW_BACKUP', '新しいバックアップ');
define('TEXT_INFO_HEADING_RESTORE_LOCAL', 'ローカルファイルから復元');
define('TEXT_INFO_NEW_BACKUP', 'バックアップ処理は途中で中断しないでください。処理に数分かかる場合があります。');
define('TEXT_INFO_UNPACK', '<br><br>(圧縮ファイルの解凍後に実行)');
define('TEXT_INFO_RESTORE', '復元処理を途中で中断しないでください。<br><br>ファイルサイズが大きいと、復元処理に時間がかかります!<br><br>※ MySQLクライアントの機能を使用して復元する事を推奨します。<br><br>コマンド例:<br><b>mysql -h ' . DB_SERVER . ' -u ' . DB_SERVER_USERNAME . ' -p ' . DB_DATABASE . ' < %s </b> %s');
define('TEXT_INFO_RESTORE_LOCAL', '復元処理を途中で中断しないでください。<br><br>ファイルサイズが大きいと、復元処理に時間がかかります!');
define('TEXT_INFO_RESTORE_LOCAL_RAW_FILE', 'アップロード出来るファイルは、純粋なSQLテキストファイルのみです。');
define('TEXT_INFO_DATE', '日付:');
define('TEXT_INFO_SIZE', 'サイズ:');
define('TEXT_INFO_COMPRESSION', '圧縮方法:');
define('TEXT_INFO_USE_GZIP', 'GZIP圧縮で保存');
define('TEXT_INFO_USE_ZIP', 'ZIP圧縮で保存');
define('TEXT_INFO_USE_NO_COMPRESSION', '非圧縮保存 (純粋なSQLファイル)');
define('TEXT_INFO_DOWNLOAD_ONLY', 'ダウンロード保存 (サーバ側には残しません)');
define('TEXT_INFO_BEST_THROUGH_HTTPS', 'HTTPSコネクション経由を推奨');
define('TEXT_DELETE_INTRO', '本当にこのバックアップファイルを削除しますか?');
define('TEXT_NO_EXTENSION', 'なし');
define('TEXT_BACKUP_DIRECTORY', 'バックアップ・ディレクトリ:');
define('TEXT_LAST_RESTORATION', '最後の復元:');
define('TEXT_FORGET', '(<u>忘れてしまった</u>)');

define('ERROR_BACKUP_DIRECTORY_DOES_NOT_EXIST', 'エラー: バックアップ・ディレクトリが存在しません。includes/configure.phpの設定を確認してください。');
define('ERROR_BACKUP_DIRECTORY_NOT_WRITEABLE', 'エラー: バックアップ・ディレクトリに書き込みできません。');
define('ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE', 'エラー: ダウンロードが許されていません。');
define('ERROR_FILE_NOT_REMOVEABLE', 'エラー: バックアップファイルの削除ができませんでした。ファイルのユーザ権限を確認してください。');	//Add Japanese osCommerce

define('SUCCESS_LAST_RESTORE_CLEARED', '成功: 最新の復元データは消去されました。');
define('SUCCESS_DATABASE_SAVED', '成功: データベースが保存されました。');
define('SUCCESS_DATABASE_RESTORED', '成功: データベースが復元されました。');
define('SUCCESS_BACKUP_DELETED', '成功: バックアップ・ファイルが削除されました。');
?>