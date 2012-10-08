<?php
/*
  $Id$
*/

define('HEADING_TITLE', '广告管理');

define('TABLE_HEADING_BANNERS', '广告');
define('TABLE_HEADING_GROUPS', '组');
define('TABLE_HEADING_STATISTICS', '显示 / 点击');
define('TABLE_HEADING_STATUS', '状态');
define('TABLE_HEADING_ACTION', '操作');

define('TEXT_BANNERS_TITLE', '广告・标题:');
define('TEXT_BANNERS_URL', '广告 URL:');
define('TEXT_BANNERS_GROUP', '广告・分组:');
define('TEXT_BANNERS_NEW_GROUP', ' 或在下面注册一个新的广告・分组');
define('TEXT_BANNERS_IMAGE', '图片:');
define('TEXT_BANNERS_IMAGE_LOCAL', ' 或在下面填入服务器上的图片名');
define('TEXT_BANNERS_IMAGE_TARGET', '图片的保存路径:');
define('TEXT_BANNERS_HTML_TEXT', 'HTML 文本:');
define('TEXT_BANNERS_EXPIRES_ON', '结束日期:');
define('TEXT_BANNERS_OR_AT', ' 或');
define('TEXT_BANNERS_IMPRESSIONS', '显示次数');
define('TEXT_BANNERS_SCHEDULED_AT', '开始日期:');
define('TEXT_BANNERS_BANNER_NOTE', '<b>广告:</b><ul><li>广告可以使用图片和HTML文本中的任意一个，但两者不能同时使用。</li><li>HTML文本优先于图片使用。</li></ul>');
define('TEXT_BANNERS_INSERT_NOTE', '<b>图片:</b><ul><li>请在广告图片保存路径目录下给予适当的编辑权限。</li><li>不在网站上传广告图片时,&quot;图片的保存路径&quot; 输入框里请不要输入任何值 。(这种情况下，会默认使用服务器端的图片)</li><li>&quot;图片的保存路径&quot; 未被指定的时候、需要用已存在的目录或者是新建一个目录。另外，目录要用“/”结尾。(例: banners/)</li></ul>');
define('TEXT_BANNERS_EXPIRCY_NOTE', '<b>结束日期:</b><ul><li>结束日期和显示次数的两个输入框里面，只有一个能被保存。</li><li>如果不想让广告自动终止，请保持这些输入框空白。</li></ul>');
define('TEXT_BANNERS_SCHEDULE_NOTE', '<b>开始日期:</b><ul><li>如果保存了开始日期,广告就从保存的日期开始有效。</li><li>如果保存了开始日期,不到开始日期广告不显示。</li></ul>');

define('TEXT_BANNERS_DATE_ADDED', '创建日期:');
define('TEXT_BANNERS_SCHEDULED_AT_DATE', '开始日期: <b>%s</b>');
define('TEXT_BANNERS_EXPIRES_AT_DATE', '结束日期: <b>%s</b>');
define('TEXT_BANNERS_EXPIRES_AT_IMPRESSIONS', '结束日期: <b>%s</b> 点击次数');
define('TEXT_BANNERS_STATUS_CHANGE', '状态变更: %s');

define('TEXT_BANNERS_DATA', '次<br>数');
define('TEXT_BANNERS_LAST_3_DAYS', '最近三天');
define('TEXT_BANNERS_BANNER_VIEWS', '广告显示');
define('TEXT_BANNERS_BANNER_CLICKS', '广告・点击');

define('TEXT_INFO_DELETE_INTRO', '确定删除广告吗？');
define('TEXT_INFO_DELETE_IMAGE', '同时删除广告图片');

define('SUCCESS_BANNER_INSERTED', "成功: 广告已插入。");
define('SUCCESS_BANNER_UPDATED', "成功: 广告已更新。");
define('SUCCESS_BANNER_REMOVED', "成功: 广告已删除");
define('SUCCESS_BANNER_STATUS_UPDATED', "成功: 广告状态已更新。");

define('ERROR_BANNER_TITLE_REQUIRED', "错误: 必须填写广告标题。");
define('ERROR_BANNER_GROUP_REQUIRED', "错误: 必须填写广告・分组。");
define('ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST', "错误: 保存路径目录不存在。");
define('ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE', "错误: 无法编辑保存路径目录: %s");
define('ERROR_IMAGE_DOES_NOT_EXIST', "错误: 图片不存在。");
define('ERROR_IMAGE_IS_NOT_WRITEABLE', "错误: 无法删除图片。");
define('ERROR_UNKNOWN_STATUS_FLAG', "错误: 状态不详。");

define('ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST', "错误:  'graphs' 目录不存在。 请在'images' 目录里创建'graphs'目录。");
define('ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE', "错误:  '无法编辑images/graphs' 目录。请设置正确的用户权限。");
define('TEXT_USER_ADDED','创建者:');
define('TEXT_USER_UPDATE','更新者:');
define('TEXT_DATE_ADDED','创建日期:');
define('TEXT_DATE_UPDATE','更新日期:');

?>
