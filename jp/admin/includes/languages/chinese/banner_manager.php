<?php
/*
  $Id$
*/

define('HEADING_TITLE', 'Banner管理');

define('TABLE_HEADING_BANNERS', 'banner');
define('TABLE_HEADING_GROUPS', '组');
define('TABLE_HEADING_STATISTICS', '显示 / 点击');
define('TABLE_HEADING_STATUS', '状态');
define('TABLE_HEADING_ACTION', '操作');

define('TEXT_BANNERS_TITLE', 'banner・标题:');
define('TEXT_BANNERS_URL', 'banner URL:');
define('TEXT_BANNERS_GROUP', 'banner・分组:');
define('TEXT_BANNERS_NEW_GROUP', ' 或在下面注册一个新的banner・分组');
define('TEXT_BANNERS_IMAGE', '图片:');
define('TEXT_BANNERS_IMAGE_LOCAL', ' 或在下面填入服务器上的图片名');
define('TEXT_BANNERS_IMAGE_TARGET', '图片的保存路径:');
define('TEXT_BANNERS_HTML_TEXT', 'HTML 文本:');
define('TEXT_BANNERS_EXPIRES_ON', '结束日期:');
define('TEXT_BANNERS_OR_AT', ' 或');
define('TEXT_BANNERS_IMPRESSIONS', '显示次数');
define('TEXT_BANNERS_SCHEDULED_AT', '开始日期:');
define('TEXT_BANNERS_BANNER_NOTE', 'banner可以使用图片和HTML文本中的任意一个，但两者不能同时使用。<br>HTML文本优先于图片使用。');
define('TEXT_BANNERS_INSERT_NOTE', '请在banner图片保存路径目录下给予适当的编辑权限。<br>不在网站上传banner图片时,&quot;图片的保存路径&quot; 输入框里请不要输入任何值 。(这种情况下，会默认使用服务器端的图片)<br>&quot;图片的保存路径&quot; 未被指定的时候、需要用已存在的目录或者是新建一个目录。另外，目录要用“/”结尾。(例: banners/)>');
define('TEXT_BANNERS_EXPIRCY_NOTE', '结束日期和显示次数的两个输入框里面，只有一个能被保存。<br>如果不想让banner自动终止，请保持这些输入框空白。');
define('TEXT_BANNERS_SCHEDULE_NOTE', '如果保存了开始日期,banner就从保存的日期开始有效。<br>如果保存了开始日期,不到开始日期banner不显示。');

define('TEXT_BANNERS_DATE_ADDED', '创建日:');
define('TEXT_BANNERS_SCHEDULED_AT_DATE', '开始日期: %s');
define('TEXT_BANNERS_EXPIRES_AT_DATE', '结束日期: %s');
define('TEXT_BANNERS_EXPIRES_AT_IMPRESSIONS', '结束日期: %s 点击次数');
define('TEXT_BANNERS_STATUS_CHANGE', '状态变更: %s');

define('TEXT_BANNERS_DATA', '次<br>数');
define('TEXT_BANNERS_LAST_3_DAYS', '最近三天');
define('TEXT_BANNERS_BANNER_VIEWS', 'banner显示');
define('TEXT_BANNERS_BANNER_CLICKS', 'banner・点击');

define('TEXT_INFO_DELETE_INTRO', '确定删除banner吗？');
define('TEXT_INFO_DELETE_IMAGE', '同时删除banner图片');

define('SUCCESS_BANNER_INSERTED', "成功: banner已插入。");
define('SUCCESS_BANNER_UPDATED', "成功: banner已更新。");
define('SUCCESS_BANNER_REMOVED', "成功: banner已删除");
define('SUCCESS_BANNER_STATUS_UPDATED', "成功: banner状态已更新。");

define('ERROR_BANNER_TITLE_REQUIRED', "错误: 必须填写banner标题。");
define('ERROR_BANNER_GROUP_REQUIRED', "错误: 必须填写banner・分组。");
define('ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST', "错误: 保存路径目录不存在。");
define('ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE', "错误: 无法编辑保存路径目录: %s");
define('ERROR_IMAGE_DOES_NOT_EXIST', "错误: 图片不存在。");
define('ERROR_IMAGE_IS_NOT_WRITEABLE', "错误: 无法删除图片。");
define('ERROR_UNKNOWN_STATUS_FLAG', "错误: 状态不详。");

define('ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST', "错误:  'graphs' 目录不存在。 请在'images' 目录里创建'graphs'目录。");
define('ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE', "错误:  '无法编辑images/graphs' 目录。请设置正确的用户权限。");
define('TEXT_USER_ADDED','创建者:');
define('TEXT_USER_UPDATE','更新者:');
define('TEXT_DATE_ADDED','创建日:');
define('TEXT_DATE_UPDATE','更新日:');

define('TEXT_ADVERTISEMENT_INFO','如果想使用banner功能的话，【注册新banner/组】的命名一定要以adv开头。');
define('TEXT_REVIEWS_SELECT_ACTION','请选择');
define('TEXT_REVIEWS_DELETE_ACTION', '删除勾选项目');
define('TEXT_NEWS_MUST_SELECT', '请至少选择一项');
define('TEXT_DEL_NEWS', '确定要删除吗？');
define('BANNER_TITLE_ERROR','&nbsp;&nbsp;<font color=\'red\'>请输入banner・标题。</font>');
define('BANNER_URL_ERROR','&nbsp;&nbsp;<font color=\'red\'>请输入banner URL。</font>');
define('BANNER_GROUP_ERROR','&nbsp;&nbsp;<font color=\'red\'>请输入banner ・分组。</font>');
define('TEXT_CONTENTS','内容');
?>
