<?php
/*
  $Id$

*/

define('HEADING_TITLE', '数据库备份管理');

define('TABLE_HEADING_TITLE', '标题');
define('TABLE_HEADING_FILE_DATE', '日期');
define('TABLE_HEADING_FILE_SIZE', '大小');
define('TABLE_HEADING_ACTION', '动作');

define('TEXT_INFO_HEADING_NEW_BACKUP', '新备份');
define('TEXT_INFO_HEADING_RESTORE_LOCAL', '从本地文件复原');
define('TEXT_INFO_NEW_BACKUP', '备份过程中请不要中断，可能需要几分钟。');
define('TEXT_INFO_UNPACK', '<br><br>(解压压缩文件后执行)');
define('TEXT_INFO_RESTORE', '请不要在复原过程中中断。<br><br>文件的大小与复原处理所花费的时间成正比!<br><br>※ 推荐复原时使用MySQL客户端的功能。<br><br>命令示例:<br><b>mysql -h ' . DB_SERVER . ' -u ' . DB_SERVER_USERNAME . ' -p ' . DB_DATABASE . ' < %s </b> %s');
define('TEXT_INFO_RESTORE_LOCAL', '请不要在复原过程中中断。<br><br>文件的大小与复原处理所花费的时间成正比!');
define('TEXT_INFO_RESTORE_LOCAL_RAW_FILE', '只能上传SQL文本文件。');
define('TEXT_INFO_DATE', '日期:');
define('TEXT_INFO_SIZE', '大小:');
define('TEXT_INFO_COMPRESSION', '压缩方法:');
define('TEXT_INFO_USE_GZIP', '用GZIP压缩包保存');
define('TEXT_INFO_USE_ZIP', '用ZIP压缩包保存');
define('TEXT_INFO_USE_NO_COMPRESSION', '不压缩保存 (SQL文件)');
define('TEXT_INFO_DOWNLOAD_ONLY', '下载保存 (服务器端不保存)');
define('TEXT_INFO_BEST_THROUGH_HTTPS', '推荐HTTPS连接路径');
define('TEXT_DELETE_INTRO', '确定删除这个备份文件吗?');
define('TEXT_NO_EXTENSION', '没有');
define('TEXT_BACKUP_DIRECTORY', '备份目录:');
define('TEXT_LAST_RESTORATION', '最终复原:');
define('TEXT_FORGET', '(忘记)');

define('ERROR_BACKUP_DIRECTORY_DOES_NOT_EXIST', '错误: 备份目录不存在。请确认includes/configure.php的设置。');
define('ERROR_BACKUP_DIRECTORY_NOT_WRITEABLE', '错误: 无法编辑备份目录。');
define('ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE', '错误: 无法下载。');
define('ERROR_FILE_NOT_REMOVEABLE', '错误: 无法删除备份文件。请确认文件的用户权限。');	//Add Japanese osCommerce

define('SUCCESS_LAST_RESTORE_CLEARED', '成功: 最新复原数据已删除。');
define('SUCCESS_DATABASE_SAVED', '成功: 数据库已保存。');
define('SUCCESS_DATABASE_RESTORED', '成功: 数据库已复原。');
define('SUCCESS_BACKUP_DELETED', '成功: 备份文件已删除。');
?>
