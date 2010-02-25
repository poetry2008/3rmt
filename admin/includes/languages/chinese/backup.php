<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', '数据库·备份管理');

define('TABLE_HEADING_TITLE', '标题');
define('TABLE_HEADING_FILE_DATE', '日期');
define('TABLE_HEADING_FILE_SIZE', '大小');
define('TABLE_HEADING_ACTION', '动作');

define('TEXT_INFO_HEADING_NEW_BACKUP', '新备份');
define('TEXT_INFO_HEADING_RESTORE_LOCAL', '从本地文件还原');
define('TEXT_INFO_NEW_BACKUP', '备份处理过程中请不要中断。处理过程有时需要几分钟。');
define('TEXT_INFO_UNPACK', '<br><br>(压缩文件解压后实行)');
define('TEXT_INFO_RESTORE', '还原处理过程中不要中断。<br><br>如果文件很大的话，还原过程需要花费时间!<br><br>※推荐使用MySQL客户端的功能还原。<br><br>命令例:<br><b>mysql -h ' . DB_SERVER . ' -u ' . DB_SERVER_USERNAME . ' -p ' . DB_DATABASE . ' < %s </b> %s');
define('TEXT_INFO_RESTORE_LOCAL', '还原处理过程中不要中断。<br><br>如果文件很大，还原需要花费一定时间!');
define('TEXT_INFO_RESTORE_LOCAL_RAW_FILE', '可以上传的文件，只有单纯的SQL文本文件。');
define('TEXT_INFO_DATE', '日期:');
define('TEXT_INFO_SIZE', '大小:');
define('TEXT_INFO_COMPRESSION', '压缩方法:');
define('TEXT_INFO_USE_GZIP', '以GZIP格式压缩保存');
define('TEXT_INFO_USE_ZIP', 'ZIP格式压缩保存');
define('TEXT_INFO_USE_NO_COMPRESSION', '不压缩保存 (单纯的SQL文件)');
define('TEXT_INFO_DOWNLOAD_ONLY', '下载保存 (不在服务器残留 )');
define('TEXT_INFO_BEST_THROUGH_HTTPS', '推荐HTTPS链接路径');
define('TEXT_DELETE_INTRO', '确定删除这个备份文件吗？');
define('TEXT_NO_EXTENSION', '无');
define('TEXT_BACKUP_DIRECTORY', '备份・目录:');
define('TEXT_LAST_RESTORATION', '最后的还原:');
define('TEXT_FORGET', '(<u>忘记</u>)');

define('ERROR_BACKUP_DIRECTORY_DOES_NOT_EXIST', '错误: 备份·目录不存在。请确认includes/configure.php的设定。');
define('ERROR_BACKUP_DIRECTORY_NOT_WRITEABLE', '错误: 无法写入备份·目录');
define('ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE', '错误: 不允许下载。');
define('ERROR_FILE_NOT_REMOVEABLE', '错误: 无法删除备份文件。请确认文件的用户权限。');	//Add Japanese osCommerce

define('SUCCESS_LAST_RESTORE_CLEARED', '成功: 最新的还原数据已消除。');
define('SUCCESS_DATABASE_SAVED', '成功: 数据库已保存。');
define('SUCCESS_DATABASE_RESTORED', '成功: データベースが復元されました。');
define('SUCCESS_BACKUP_DELETED', '成功: 备份·文件已删除。');
?>