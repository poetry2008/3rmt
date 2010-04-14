<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'banner管理');

define('TABLE_HEADING_BANNERS', 'banner');
define('TABLE_HEADING_GROUPS', '组');
define('TABLE_HEADING_STATISTICS', '显示 / 点击');
define('TABLE_HEADING_STATUS', '状态');
define('TABLE_HEADING_ACTION', '操作');

define('TEXT_BANNERS_TITLE', 'banner・标题:');
define('TEXT_BANNERS_URL', 'bannerURL:');
define('TEXT_BANNERS_GROUP', 'banner・组:');
define('TEXT_BANNERS_NEW_GROUP', ' 或者登录下面的新的banner·组');
define('TEXT_BANNERS_IMAGE', '图片文件:');
define('TEXT_BANNERS_IMAGE_LOCAL', ' 或者在下面输入banner上的图片文件名');
define('TEXT_BANNERS_IMAGE_TARGET', '图片保存位置:');
define('TEXT_BANNERS_HTML_TEXT', 'HTML 文本:');
define('TEXT_BANNERS_EXPIRES_ON', '结束日期:');
define('TEXT_BANNERS_OR_AT', ' 或者');
define('TEXT_BANNERS_IMPRESSIONS', '显示次数');
define('TEXT_BANNERS_SCHEDULED_AT', '开始时间:');
define('TEXT_BANNERS_BANNER_NOTE', '<b>关于banner:</b><ul><li>在banner上使用图片或者HTML文本。不可同时使用。</li><li>HTML文本优先于图片。</li></ul>');
define('TEXT_BANNERS_INSERT_NOTE', '<b>关于图片:</b><ul><li>在banner图片保存地址的目录中，要给适当的写入权限。</li><li>web网站不上传banner图片时、&quot;图片的保存地址&quot;栏不输入 。(这种情况下，使用服务器的图片 )</li><li>&quot;图片的保存地址&quot; 指定的情况、需要有存在的目录、或者是先创建目录。另外，目录的末尾需要有斜线。(例: banners/)</li></ul>');
define('TEXT_BANNERS_EXPIRCY_NOTE', '<b>关于结束日期:</b><ul><li>结束日期和显示次数的2栏中，只输入一个即可。</li><li>banner不是自动结束的时候、这2栏空着即可。</li></ul>');
define('TEXT_BANNERS_SCHEDULE_NOTE', '<b>关于开始日期:</b><ul><li>输入了开始日期，banner就从输入的日期开始生效。</li><li>输入了开始日期的banner在开始日期之前是不显示的。</li></ul>');

define('TEXT_BANNERS_DATE_ADDED', '登录日期:');
define('TEXT_BANNERS_SCHEDULED_AT_DATE', '开始日期: <b>%s</b>');
define('TEXT_BANNERS_EXPIRES_AT_DATE', '结束日期: <b>%s</b>');
define('TEXT_BANNERS_EXPIRES_AT_IMPRESSIONS', '结束日期: <b>%s</b> 点击数');
define('TEXT_BANNERS_STATUS_CHANGE', '更改状态: %s');

define('TEXT_BANNERS_DATA', '回<br>数');
define('TEXT_BANNERS_LAST_3_DAYS', '最近3天');
define('TEXT_BANNERS_BANNER_VIEWS', 'banner显示');
define('TEXT_BANNERS_BANNER_CLICKS', 'banner·点击');

define('TEXT_INFO_DELETE_INTRO', '确认删除banner吗？');
define('TEXT_INFO_DELETE_IMAGE', 'banner图片也删除');

define('SUCCESS_BANNER_INSERTED', "成功: banner已插入");
define('SUCCESS_BANNER_UPDATED', "成功: banner已更新。");
define('SUCCESS_BANNER_REMOVED', "成功: banner已删除。");
define('SUCCESS_BANNER_STATUS_UPDATED', "成功: banner状态已更新。");

define('ERROR_BANNER_TITLE_REQUIRED', "错误: 需要填写banner的标题。");
define('ERROR_BANNER_GROUP_REQUIRED', "错误: 需要banner的组");
define('ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST', "错误: 保存地址的目录不存在。");
define('ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE', "错误: 保存地址目录无法写入: %s");
define('ERROR_IMAGE_DOES_NOT_EXIST', "错误: 图片不存在。");
define('ERROR_IMAGE_IS_NOT_WRITEABLE', "错误: 图片无法删除。");
define('ERROR_UNKNOWN_STATUS_FLAG', "错误: 不明状态。");

define('ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST', "错误:  'graphs' 目录不存在。在 'images' 目录中创建'graphs'目录。");
define('ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE', "错误:  'images/graphs' 目录无法写入。设置正确的用户权限。");
?>