<?php
/* 
$Id$
*/

//
// mb_internal_encoding() is set for PHP-4.3.x(Zend Multibyte)
//
// A compatible module is loaded for environment without mbstring-extension
//
if (extension_loaded('mbstring')) {
  mb_internal_encoding('UTF-8'); // 指定内部代码
} else {
  include_once(DIR_WS_LANGUAGES . $language . '/jcode.phps');
  include_once(DIR_WS_LANGUAGES . $language . '/mbstring_wrapper.php');
}

// look in your $PATH_LOCALE/locale directory for available locales..
// on RedHat6.0 I used 'en_US'
// on FreeBSD 4.0 I use 'en_US.ISO_8859-1'
// this may not work under win32 environments..
setlocale(LC_TIME, 'ja_JP.UTF-8');
define('DATE_FORMAT_SHORT', '%Y/%m/%d');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%Y年%B%e日 %A'); // this is used for strftime()
define('DATE_FORMAT', 'Y/m/d'); // this is used for date()
define('PHP_DATE_TIME_FORMAT', 'Y/m/d H:i:s'); // this is used for date()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');
define('DATE_TIME_FORMAT_TORIHIKI', '%Y/%m/%d %H:%M');

////
// Return date in raw format
// $date should be in format mm/dd/yyyy
// raw date is in format YYYYMMDD, or DDMMYYYY
function tep_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 8, 2) . substr($date, 5, 2) . substr($date, 0, 4);
  } else {
    return substr($date, 0, 4) . substr($date, 5, 2) . substr($date, 8, 2);
  }
}

// Global entries for the <html> tag
define('HTML_PARAMS','dir="ltr" lang="ja"');

// charset for web pages and emails
define('CHARSET', 'UTF-8');    // Shift_JIS / euc-jp / iso-2022-jp

// page title
define('TITLE', STORE_NAME);  //请输入商店名。

// header text in includes/header.php
define('HEADER_TITLE_TOP', '首页');
define('HEADER_TITLE_SUPPORT_SITE', '支持网站');
define('HEADER_TITLE_ONLINE_CATALOG', '在线商品');
define('HEADER_TITLE_ADMINISTRATION', '管理菜单');

// text for gender
define('MALE', '男性');
define('FEMALE', '女性');

// text for date of birth example
define('DOB_FORMAT_STRING', 'yyyy-mm-dd');

// configuration box text in includes/boxes/configuration.php
define('BOX_HEADING_CONFIGURATION', '基本设置');
define('BOX_CONFIGURATION_MYSTORE', '店铺');
define('BOX_CONFIGURATION_LOGGING', '日志');
define('BOX_CONFIGURATION_CACHE', '缓存');

// modules box text in includes/boxes/modules.php
define('BOX_HEADING_MODULES', '模块设置');
define('BOX_MODULES_PAYMENT', '支付模块设置');
define('BOX_MODULES_SHIPPING', '配送模块');
define('BOX_MODULES_ORDER_TOTAL', '合计模块设置');
define('BOX_MODULES_METASEO', 'SEO模块设置');

// categories box text in includes/boxes/catalog.php
define('BOX_HEADING_CATALOG', '商品管理');
define('BOX_CATALOG_CATEGORIES_PRODUCTS', '商品分类/商品注册');
define('BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES', '商品选项');
define('BOX_CATALOG_MANUFACTURERS', '制造商登记');
define('BOX_CATALOG_REVIEWS', '评论管理');
define('BOX_CATALOG_SPECIALS', '特价商品注册');
define('BOX_CATALOG_PRODUCTS_EXPECTED', '预定到货商品管理');

// customers box text in includes/boxes/customers.php
define('BOX_HEADING_CUSTOMERS', '顾客管理');
define('BOX_CUSTOMERS_CUSTOMERS', '顾客管理');
define('BOX_CUSTOMERS_ORDERS', '订单管理');

// taxes box text in includes/boxes/taxes.php
define('BOX_HEADING_LOCATION_AND_TAXES', '地区/ 税率设置');
define('BOX_TAXES_COUNTRIES', '国名设置');
define('BOX_TAXES_ZONES', '地区设置');
define('BOX_TAXES_GEO_ZONES', '地税设置');
define('BOX_TAXES_TAX_CLASSES', '税种设置');
define('BOX_TAXES_TAX_RATES', '税率设置');

// reports box text in includes/boxes/reports.php
define('BOX_HEADING_REPORTS', '统计排名');
define('BOX_REPORTS_PRODUCTS_VIEWED', '按浏览次数排序');
define('BOX_REPORTS_PRODUCTS_PURCHASED', '按销售量排序');
define('BOX_REPORTS_ORDERS_TOTAL', '按销售额排序');
define('BOX_REPORTS_SALES_REPORT2', '销售额管理');
define('BOX_REPORTS_NEW_CUSTOMERS', '新顾客');
define('BOX_REPORTS_ASSETS', '资产管理');

// tools text in includes/boxes/tools.php
define('BOX_HEADING_TOOLS', '工具');
define('BOX_TOOLS_BACKUP', '数据库备份管理');
define('BOX_TOOLS_BANNER_MANAGER', '广告管理');
define('BOX_TOOLS_CACHE', '缓存控制');
define('BOX_TOOLS_DEFINE_LANGUAGE', '语言文件管理');
define('BOX_TOOLS_FILE_MANAGER', '文件管理');
define('BOX_TOOLS_MAIL', '发送邮件');
define('BOX_TOOLS_NEWSLETTER_MANAGER', '邮件杂志管理');
define('BOX_TOOLS_SERVER_INFO', '服务器信息');
define('BOX_TOOLS_WHOS_ONLINE', '在线用户');
define('BOX_TOOLS_PRESENT','赠品功能');

// localizaion box text in includes/boxes/localization.php
define('BOX_HEADING_LOCALIZATION', '本地化');
define('BOX_LOCALIZATION_CURRENCIES', '货币设置');
define('BOX_LOCALIZATION_LANGUAGES', '语言设置');
define('BOX_LOCALIZATION_ORDERS_STATUS', '订单状态设置');

// javascript messages
define('JS_ERROR', '处理样式时发生了错误!\n请进行下列修改:\n\n');

define('JS_OPTIONS_VALUE_PRICE', '* 请指定新商品属性的价格。\n');
define('JS_OPTIONS_VALUE_PRICE_PREFIX', '* 请指定新商品属性的价格的接头词。\n');

define('JS_PRODUCTS_NAME', '* 请指定新商品的名字。\n');
define('JS_PRODUCTS_DESCRIPTION', '* 请输入新商品的说明书。\n');
define('JS_PRODUCTS_PRICE', '* 请指定新商品的价格。\n');
define('JS_PRODUCTS_WEIGHT', '* 请指定新商品的重量。\n');
define('JS_PRODUCTS_QUANTITY', '* 请指定新商品的数量。\n');
define('JS_PRODUCTS_MODEL', '* 请指定新商品的型号。\n');
define('JS_PRODUCTS_IMAGE', '* 请指定新商品的图像。\n');

define('JS_SPECIALS_PRODUCTS_PRICE', '* 请指定这个商品的新价格。\n');

define('JS_GENDER', '* \'性别\' 未选。\n');
define('JS_FIRST_NAME', '* \'名字\' 至少 ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' 字以上。\n');
define('JS_LAST_NAME', '* \'姓\' 至少 ' . ENTRY_LAST_NAME_MIN_LENGTH . ' 字以上。\n');

define('JS_FIRST_NAME_F', '* \'名字(注音假名)\' 至少 ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' 字以上。\n');
define('JS_LAST_NAME_F', '* \'姓(注音假名)\' 至少 ' . ENTRY_LAST_NAME_MIN_LENGTH . ' 字以上。\n');

define('JS_DOB', '* \'生年月日\' 请按下面的形式输入 : xxxx/xx/xx (年/月/日)。\n');
define('JS_EMAIL_ADDRESS', '* \'E-Mail 地址\' 至少 ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' 字以上。\n');
define('JS_EMAIL_ADDRESS_MATCH_ERROR','*  输入的邮箱地址不正确!');
define('JS_ADDRESS', '* \'住处１\' 至少' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' 字以上。\n');
define('JS_POST_CODE', '* \'邮政编码\' 至少 ' . ENTRY_POSTCODE_MIN_LENGTH . ' 字以上。\n');
define('JS_CITY', '* \'市区町村\' 至少 ' . ENTRY_CITY_MIN_LENGTH . ' 字以上。\n');
define('JS_STATE', '* \'都道府县\' 未选。\n');
define('JS_STATE_SELECT', '-- 从上面选择 --');
define('JS_ZONE', '* \'都道府县\' 请从目录中选择。');
define('JS_COUNTRY', '* \'国\' 请选择。\n');
define('JS_TELEPHONE', '* \'电话号码\' 至少 ' . ENTRY_TELEPHONE_MIN_LENGTH . ' 字以上。\n');
define('JS_PASSWORD', '* \'密码\' 和 \'确认密码\' 至少 ' . ENTRY_PASSWORD_MIN_LENGTH . ' 字以上。\n');

define('JS_ORDER_DOES_NOT_EXIST', '订单号 %s 不存在!');

define('CATEGORY_PERSONAL', '个人信息');
define('CATEGORY_ADDRESS', '住处');
define('CATEGORY_CONTACT', '联系方式');
define('CATEGORY_COMPANY', '公司名');
define('CATEGORY_PASSWORD', '密码');
define('CATEGORY_OPTIONS', '选择');
define('ENTRY_GENDER', '性别:');
define('ENTRY_FIRST_NAME', '名:');
define('ENTRY_LAST_NAME', '姓:');
//add
define('TEXT_ADDRESS','住处');
define('TEXT_CLEAR','清空');
define('TABLE_OPTION_NEW','投递到登记处');
define('TABLE_OPTION_OLD','指定过去的送货地址'); 
define('TABLE_ADDRESS_SHOW','从送货地址目录中选择:');
define('ENTRY_FIRST_NAME_F', '名(注音假名):');
define('ENTRY_LAST_NAME_F', '姓(注音假名):');
define('ENTRY_DATE_OF_BIRTH', '生年月日:');
define('ENTRY_EMAIL_ADDRESS', 'E-Mail 地址:');
define('ENTRY_QUITED_DATE','退会日期:');
define('ENTRY_COMPANY', '公司名:');
define('ENTRY_STREET_ADDRESS', '住处1:');
define('ENTRY_SUBURB', '住处2:');
define('ENTRY_POST_CODE', '邮政编码:');
define('ENTRY_CITY', '市区町村:');
define('ENTRY_STATE', '都道府县:');
define('ENTRY_COUNTRY', '国名:');
define('ENTRY_TELEPHONE_NUMBER', '电话号码:');
define('ENTRY_FAX_NUMBER', '传真号码:');
define('ENTRY_NEWSLETTER', '电子杂志:');
define('ENTRY_NEWSLETTER_YES', '订阅');
define('ENTRY_NEWSLETTER_NO', '不订阅');
define('ENTRY_PASSWORD', '密码:');
define('ENTRY_PASSWORD_CONFIRMATION', '密码再确认:');
define('PASSWORD_HIDDEN', '********');

// images
define('IMAGE_ANI_SEND_EMAIL', '发送E-Mail');
define('IMAGE_BACK', '返回');
define('IMAGE_NEXT', '向下');
define('IMAGE_BACKUP', '备份');
define('IMAGE_CANCEL', '取消');
define('IMAGE_CONFIRM', '确认');
define('IMAGE_CONFIRM_NEXT', '下一步');
define('IMAGE_COPY', '复制');
define('IMAGE_COPY_TO', '复制到');
define('IMAGE_DEFINE', '定义');
define('IMAGE_DELETE', '删除');
define('IMAGE_EDIT', '编辑');
define('IMAGE_EMAIL', 'E-Mail');
define('IMAGE_FILE_MANAGER', '文件管理');
define('IMAGE_ICON_STATUS_GREEN', '有效');
define('IMAGE_ICON_STATUS_GREEN_LIGHT', '使有效');
define('IMAGE_ICON_STATUS_RED', '无效');
define('IMAGE_ICON_STATUS_RED_LIGHT', '使无效');
define('IMAGE_ICON_INFO', '信息');
define('IMAGE_INSERT', '插入');
define('IMAGE_LOCK', '锁定');
define('IMAGE_MOVE', '移动');
define('IMAGE_NEW_PROJECT','新建');
define('IMAGE_NEW_CATEGORY', '新分类');
define('IMAGE_NEW_COUNTRY', '新国名');
define('IMAGE_NEW_CURRENCY', '新通货');
define('IMAGE_NEW_FILE', '新文件');
define('IMAGE_NEW_FOLDER', '新文件夹');
define('IMAGE_NEW_LANGUAGE', '新语言');
define('IMAGE_NEW_PRODUCT', '新商品');
define('IMAGE_NEW_TAX_CLASS', '新税种分类');
define('IMAGE_NEW_TAX_RATE', '新税率');
define('IMAGE_NEW_TAX_ZONE', '新税地域');
define('IMAGE_NEW_ZONE', '新地域');
define('IMAGE_NEW_TAG', '新标签'); 
define('IMAGE_ORDERS', '订单');
define('IMAGE_ORDERS_INVOICE', '交货单');
define('IMAGE_ORDERS_PACKINGSLIP', '配送单');
define('IMAGE_PREVIEW', '预览');
define('IMAGE_RESTORE', '恢复');
define('IMAGE_RESET', '重置');
define('IMAGE_SAVE', '保存');
define('IMAGE_SEARCH', '搜索');
define('IMAGE_SELECT', '选择');
define('IMAGE_SEND', '发送');
define('IMAGE_SEND_EMAIL', '发送E-Mail');
define('IMAGE_UNLOCK', '解除锁定');
define('IMAGE_UPDATE', '更新');
define('IMAGE_UPDATE_CURRENCIES', '汇率的更新');
define('IMAGE_UPLOAD', '上传');
define('IMAGE_EFFECT', '有效');
define('IMAGE_DEFFECT', '无效');

define('ICON_CROSS', '无效(False)');
define('ICON_CURRENT_FOLDER', '现在的文件夹');
define('ICON_DELETE', '删除');
define('ICON_ERROR', '错误');
define('ICON_FILE', '文件');
define('ICON_FILE_DOWNLOAD', '下载');
define('ICON_FOLDER', '文件夹');
define('ICON_LOCKED', '锁定');
define('ICON_PREVIOUS_LEVEL', '之前的水平');
define('ICON_PREVIEW', '预览');
define('ICON_STATISTICS', '统计');
define('ICON_SUCCESS', '成功');
define('ICON_TICK', '有效(True)');
define('ICON_UNLOCKED', '解除锁定');
define('ICON_WARNING', '警告');

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', ' %s / %d 页');
define('TEXT_DISPLAY_NUMBER_OF_USELESS_ITEM', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b>项 )');
define('TEXT_DISPLAY_NUMBER_OF_USELESS_OPTION', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b>项 )');
define('TEXT_DISPLAY_NUMBER_OF_OPTION_GROUP', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b>项)');
define('TEXT_DISPLAY_NUMBER_OF_OPTION_ITEM', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b>项)');
define('TEXT_DISPLAY_NUMBER_OF_ADDRESS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b>项 )');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRY_FEE', '当前显示<b>%d</b> &sim; <b>%d</b> (共<b>%d</b>项 )');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRY_AREA', '当前显示<b>%d</b> &sim; <b>%d</b> (共<b>%d</b>项 )');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRY_CITY', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b>项)');
define('TEXT_DISPLAY_NUMBER_OF_SHIPPING_TIME', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b>项)');
define('TEXT_DISPLAY_NUMBER_OF_PREORDERS_STATUS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b>项 )');
define('TEXT_DISPLAY_NUMBER_OF_PREORDERS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b>项 )');
define('TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS', '当前显示<b>%d</b> &sim; <b>%d</b> (共<b>%d</b>项 )');
define('TEXT_DISPLAY_NUMBER_OF_CAMPAIGN', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b>项 )');
define('TEXT_DISPLAY_NUMBER_OF_HELP_INFO', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b>项 )');
define('TEXT_DISPLAY_NUMBER_OF_CATEGORIES', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b>项)');
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRIES', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_FAQ', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_CURRENCIES', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项 )');
define('TEXT_DISPLAY_NUMBER_OF_LANGUAGES', '当前显示<b>%d</b> &sim; <b>%d</b>  ( 共<b>%d</b>  项)');
define('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_PW_MANAGERS', '当前显示<b>%d</b> &sim; <b>%d</b> (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_MAIL', '当前显示<b>%d</b> &sim; <b>%d</b> (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_PW_MANAGER_LOG', '当前显示<b>%d</b> &sim; <b>%d</b> (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_NIVENTORY', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_ZONES', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_RATES', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');
define('TEXT_DISPLAY_NUMBER_OF_ZONES', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');

define('PREVNEXT_BUTTON_PREV', '&lt;&lt;');
define('PREVNEXT_BUTTON_NEXT', '&gt;&gt;');
define('BUTTON_PREV', 'Prev');
define('BUTTON_NEXT', 'Next');
//==============================================================


define('PREVNEXT_TITLE_FIRST_PAGE', '首页');
define('PREVNEXT_TITLE_PREVIOUS_PAGE', '前页');
define('PREVNEXT_TITLE_NEXT_PAGE', '下页');
define('PREVNEXT_TITLE_LAST_PAGE', '最后一页');
define('PREVNEXT_TITLE_PAGE_NO', '页 %d');
define('PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', '上 %d 页');
define('PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', '下 %d 页');
define('PREVNEXT_BUTTON_FIRST', '&lt;&lt;最初');
define('PREVNEXT_BUTTON_LAST', '最后&gt;&gt;');

define('TEXT_DEFAULT', '默认');
define('TEXT_SET_DEFAULT', '默认设置');
define('TEXT_FIELD_REQUIRED', '&nbsp;<span class="fieldRequired">* 必须</span>');

define('ERROR_NO_DEFAULT_CURRENCY_DEFINED', '错误: 没有设置基本通货。 管理菜单->定位->通货设置: 请确认设置。');
define('ERROR_INPUT_RIGHT_DATE', '请输入正确日期。');

define('TEXT_CACHE_CATEGORIES', '分类区');
define('TEXT_CACHE_MANUFACTURERS', '制造商区');
define('TEXT_CACHE_ALSO_PURCHASED', '关联商品模块');

define('TEXT_NONE', '--没有--');
define('TEXT_TOP', '首页');

define('EMAIL_SIGNATURE',C_EMAIL_FOOTER);

//Add languages
//------------------------
//contents
define('BOX_TOOLS_CONTENTS', '内容管理');
define('TEXT_DISPLAY_NUMBER_OF_CONTENS', '当前显示<b>%d</b> &sim; <b>%d</b>  (共<b>%d</b> 项)');

//latest news
define('BOX_TOOLS_LATEST_NEWS', '最新信息管理');

//faq
define('BOX_TOOLS_FAQ', 'FAQ');

//leftbox
define('BOX_CATALOG_PRODUCTS_UP', '商品数据上传');
define('BOX_CATALOG_PRODUCTS_DL', '商品数据下载');
define('BOX_TOOLS_CL', '日历');
define('BOX_CATALOG_PRODUCTS_TAGS', '标签设置');
define('BOX_CATALOG_IMAGE_DOCUMENT', 'image文件管理');


define('TABLE_HEADING_SITE', '网站');

define('IMAGE_BUTTON_BACK', '');
define('IMAGE_BUTTON_CONFIRM', '');
define('IMAGE_DETAILS', '详细');

define('CATEGORY_SITE', '所属网站');
define('ENTRY_SITE', '网站');
define('ENTRY_SITE_TEXT', '所属网站');

define('TEXT_IMAGE_NONEXISTENT', '图片不存在');
define('SITE_ID_NOT_NULL', '请选择网站');
define('IMAGE_NEW_DOCUMENT_TYPE', '');
define('MSG_UPLOAD_IMG', '');
define('JS_ERROR_SUBMITTED', '');

define('BOX_CATALOG_COLORS', '商品颜色登记');
define('BOX_CATALOG_CATEGORIES_ADMIN', '商品批发价格管理');

define('HEADING_TITLE_SEARCH', '搜索');
define('HEADING_TITLE_GOTO', '跳到');
define('TABLE_HEADING_ACTION', '操作');
define('TABLE_HEADING_STATUS', '状态');
define('TABLE_HEADING_CATEGORIES_PRODUCTS', '分类 / 商品');
define('OROSHI_DATA_MANAGE','数据');
define('BOX_ONCE_PWD_LOG','日志');
define('BANK_CL_TITLE_TEXT', '编辑日历');
define('BANK_CL_COMMON_WORK_TIME', '正常营业');
define('BANK_CL_REST_TIME', '银行休业');
define('BANK_CL_SEND_MAIL', '邮件回复停止');
define('HISTORY_TITLE_ONE', '同行历史记录登记');
define('HISTORY_TITLE_TWO', '批发商的历史记录显示');
define('HISTORY_TITLE_THREE', '批发商的历史记录登记');
define('KEYWORDS_TITLE_TEXT', '关键字排名');
define('KEYWORDS_SEARCH_START_TEXT', '开始日期:');
define('KEYWORDS_SEARCH_END_TEXT', '结束日期:');
define('KEYWORDS_TABLE_COLUMN_ONE_TEXT', '关键字');
define('KEYWORDS_TABLE_COLUMN_TWO_TEXT', '次数');
define('KEYWORDS_TABLE_COLUMN_THREE_TEXT', '名次');
define('LIST_DISPLAY_PRODUCT_SELECT', '选择商品');
define('LIST_DISPLAY_JIAKONGZAIKU', '虚拟库存');
define('LIST_DISPLAY_YEZHE_PRICE', '同业单价');
define('MAG_DL_TITLE_TEXT', '电子杂志数据下载');
define('MAG_UP_TITLE_TEXT', '电子杂志统一登记');
define('PRODUCTS_TO_TAGS_TITLE', '标签关联设置');
define('REFERER_TITLE_TEXT', '访问排名');
define('REFERER_TITLE_URL', '访问来源');
define('REFERER_TITLE_NUM', '次数');
define('REFERER_TITLE_SORT_NUM', '名次');
define('TELECOM_UNKNOW_TITLE', '结算管理');
define('TELECOM_UNKNOW_SEARCH_SUCCESS', '成功');
define('TELECOM_UNKNOW_SEARCH_FAIL', '失败');
define('TELECOM_UNKNOW_TABLE_CAL_METHOD', '结算方法');
define('TELECOM_UNKNOW_TABLE_TIME', '时间');
define('TELECOM_UNKNOW_TABLE_CAL', '结算');
define('TELECOM_UNKNOW_TABLE_DISPLAY','整体不显示');
//==================================================================
define('TELECOM_UNKNOW_TABLE_YIN', '担保');
define('TELECOM_UNKNOW_TABLE_SURNAME', '姓名');
define('TELECOM_UNKNOW_TABLE_TEL', '电话');
define('TELECOM_UNKNOW_TABLE_EMAIL', '邮箱地址');
define('TELECOM_UNKNOW_TABLE_PRICE', '金额');
define('TELECOM_UNKNOW_SELECT_NOTICE', '是否隐藏选择的行？');
define('CLEATE_DOUGYOUSYA_TITLE', '同行名称设置');
define('CLEATE_DOUGYOUSYA_ALERT', '首先,请创建输入框');
define('CLEATE_DOUGYOUSYA_ADD_BUTTON', '添加输入形式');
define('CLEATE_DOUGYOUSYA_TONGYE', '同行：');
define('CLEATE_DOUGYOUSYA_EDIT', '编辑');
define('CLEATE_DOUGYOUSYA_DEL', '删除');
define('CLEATE_DOUGYOUSYA_HISTORY', '历史记录');
define('CLEATE_DOUGYOUSYA_LOGIN', '同行注册');
define('CLEATE_DOUGYOUSYA_UPDATE_SORT', '更新顺序');
define('CLEATE_LIST_TITLE', '批发商的数据登记');
define('CLEATE_LIST_SETNAME_BUTTON', '批发商名称设置');
define('CLEATE_LIST_LOGIN_BUTTON', '批发商注册');
define('CUSTOMERS_CSVEXE_TITLE', '下载客户数据');
define('CUSTOMERS_CSVEXE_READ_TEXT', '下载过程中对服务器造成高负荷。请在空闲时间执行。');
define('YEAR_TEXT', '年');
define('MONTH_TEXT', '月');
define('DAY_TEXT', '日');
define('CUSTOMERS_CSV_EXE_NOTICE_TITLE', '客户信息里以下信息作为CSV文件被下载。');
define('CUSTOMERS_CSV_EXE_READ_ONE', '<tr> <td width="20" align="center" class="infoBoxContent">&nbsp;</td> <td width="120" class="menuBoxHeading">项目</td> <td class="menuBoxHeading">说明</td> </tr> <tr> <td align="center" class="infoBoxContent">A</td> <td class="menuBoxHeading">账户创建日期</td> <td class="menuBoxHeading">输出创建账户的日期和时间（形式：2005/11/11 10:15:32）</td> </tr> <tr> <td align="center" class="infoBoxContent">B</td> <td class="menuBoxHeading">性别</td> <td class="menuBoxHeading">输出客户的性别「男」/「女」</td> </tr> <tr> <td align="center" class="infoBoxContent">C</td> <td class="menuBoxHeading">姓</td> <td class="menuBoxHeading">输出客户的姓</td> </tr> <tr> <td align="center" class="infoBoxContent">D</td> <td class="menuBoxHeading">名</td> <td class="menuBoxHeading">输出客户的名</td> </tr> <tr> <td align="center" class="infoBoxContent">E</td> <td class="menuBoxHeading">出生年月日</td> <td class="menuBoxHeading">输出客户的出生年月日（形式：1999/11/11）</td> </tr> <tr> <td align="center" class="infoBoxContent">F</td> <td class="menuBoxHeading">邮箱地址</td> <td class="menuBoxHeading">输出邮箱地址</td> </tr> <tr> <td align="center" class="infoBoxContent">G</td> <td class="menuBoxHeading">公司名</td> <td class="menuBoxHeading">如果输入公司名的话就会输出对应数据</td> </tr> <tr> <td align="center" class="infoBoxContent">H</td> <td class="menuBoxHeading">邮政编码</td> <td class="menuBoxHeading">输出邮政编码。</td> </tr> <tr> <td align="center" class="infoBoxContent">I</td> <td class="menuBoxHeading">都道府县</td> <td class="menuBoxHeading">输出都道府县名（例：东京都）</td> </tr> <tr> <td align="center" class="infoBoxContent">J</td> <td class="menuBoxHeading">市区镇村</td> <td class="menuBoxHeading">输出市区镇村名（例：港区）</td> </tr> <tr> <td align="center" class="infoBoxContent">K</td> <td class="menuBoxHeading">住址1</td> <td class="menuBoxHeading">输出自己的家（公司）住址（例： 芝公园〇〇 ）</td> </tr> <tr> <td align="center" class="infoBoxContent">L</td> <td class="menuBoxHeading">住址2</td> <td class="menuBoxHeading">如果输入楼·高级公寓名的话就会输出对应数据（例：〇〇楼5F）</td> </tr> <tr> <td align="center" class="infoBoxContent">M</td> <td class="menuBoxHeading">国名</td> <td class="menuBoxHeading">输出国名（Japan等）</td> </tr> <tr> <td align="center" class="infoBoxContent">N</td> <td class="menuBoxHeading">电话号码</td> <td class="menuBoxHeading">输出电话号码</td> </tr> <tr> <td align="center" class="infoBoxContent">O</td>'); 

define('CUSTOMERS_CSV_EXE_READ_TWO', '<td class="menuBoxHeading">FAX序号</td> <td
    class="menuBoxHeading">如果输入FAX序号的话就会输出对应数据</td> </tr> <tr> <td
    align="center" class="infoBoxContent">P</td> <td
    class="menuBoxHeading">电子杂志</td> <td
    class="menuBoxHeading">输出电子杂志的行动区情况。<br>
    订阅时：「订阅」｜未订阅时：「未订阅」</td> </tr> <tr> <td align="center" class="infoBoxContent">Q</td> <td class="menuBoxHeading">点数</td> <td class="menuBoxHeading">输出客户现在持有的点数。</td> </tr>');
define('BOX_TOOLS_POINT_EMAIL_MANAGER','点数通知邮件');
define('BOX_CAL_SITES_INFO_TEXT', '统计');

//catalog language
define('FILENAME_PRODUCTS_TAGS_TEXT','标签关联设置');
define('FILENAME_CLEATE_OROSHI_TEXT','批发商名称设置');
define('FILENAME_CLEATE_DOUGYOUSYA_TEXT','同行名称设置');
define('FILENAME_CATEGORIES_ADMIN_TEXT','商品批发价格管理');

//coustomers language
define('FILENAME_TELECOM_UNKNOW_TEXT','结算管理');
define('FILENAME_BILL_TEMPLATES_TEXT','账单模板设置');

//reports language
define('FILENAME_REFERER_TEXT','访问排名');
define('FILENAME_KEYWORDS_TEXT','关键字排名');

//tools language 
define('FILENAME_BANK_CL_TEXT','编辑日历');
define('FILENAME_PW_MANAGER_TEXT','ID管理');
define('FILENAME_COMPUTERS_TEXT','按钮设置');
define('FILENAME_MAG_UP_TEXT','电子杂志统一登记');
define('FILENAME_MAG_DL_TEXT','电子杂志数据下载');
define('BUTTON_MAG_UP','上传');
define('BUTTON_MAG_DL','下载');

//header language
define('HEADER_TEXT_SITE_NAME',COMPANY_NAME);
define('HEADER_TEXT_LOGINED','正在登录中。');
define('HEADER_TEXT_ORDERS','订单管理');
define('HEADER_TEXT_TELECOM_UNKNOW','结算管理');
define('HEADER_TEXT_TUTORIALS','商品调整▼');
define('HEADER_TEXT_CATEGORIES','商品注册');
define('HEADER_TEXT_LOGOUT','退出');
define('HEADER_TEXT_REDIRECTURL','相关网站▼');
define('HEADER_TEXT_USERS','更改密码');
define('HEADER_TEXT_PW_MANAGER','ID管理');
define('HEADER_TEXT_MANAGERMENU','工具▼');
define('HEADER_TEXT_MICRO_LOG','备忘录');
define('HEADER_TEXT_LATEST_NEWS','最新信息管理');
define('HEADER_TEXT_CUSTOMERS','顾客管理');
define('HEADER_TEXT_CREATE_ORDER2','创建进货订单');
define('HEADER_TEXT_CREATE_ORDER','创建订单');
define('HEADER_TEXT_ORDERMENU','订单▼');
define('HEADER_TEXT_INVENTORY','库存标准');
define('HEADER_TEXT_CATEGORIES_ADMIN','价格调整');
//footer start 
define('TEXT_FOOTER_ONE_TIME','使用被选中的权限可以访问该网站');
define('TEXT_FOOTER_CHECK_SAVE','保存');
//footer end
define('RIGHT_ORDER_INFO_ORDER_FROM', '订单网站：');
define('RIGHT_ORDER_INFO_ORDER_FETCH_TIME', '交易日期：');
define('RIGHT_ORDER_INFO_ORDER_OPTION', '选项：');
define('RIGHT_ORDER_INFO_ORDER_ID', '订单号码：');
define('RIGHT_ORDER_INFO_ORDER_DATE', '订购日期：');
define('RIGHT_ORDER_INFO_ORDER_CUSTOMER_TYPE', '客户类别：');
define('RIGHT_CUSTOMER_INFO_ORDER_IP', 'IP地址：');
define('RIGHT_CUSTOMER_INFO_ORDER_HOST', '主机名：');
define('RIGHT_CUSTOMER_INFO_ORDER_USER_AGEMT', '用户代理：');
define('RIGHT_CUSTOMER_INFO_ORDER_OS', 'OS：');
define('RIGHT_CUSTOMER_INFO_ORDER_BROWSE_TYPE', '浏览器的种类：');
define('RIGHT_CUSTOMER_INFO_ORDER_BROWSE_LAN', '浏览器的语言：');
define('RIGHT_CUSTOMER_INFO_ORDER_COMPUTER_LAN', '计算机的语言环境：');
define('RIGHT_CUSTOMER_INFO_ORDER_USER_LAN', '用户的语言环境：');
define('RIGHT_CUSTOMER_INFO_ORDER_PIXEL', '画面的分辨率：');
define('RIGHT_CUSTOMER_INFO_ORDER_COLOR', '画面的颜色：');
define('RIGHT_CUSTOMER_INFO_ORDER_FLASH', 'Flash：');
define('RIGHT_CUSTOMER_INFO_ORDER_FLASH_VERSION', 'Flash版本：');
define('RIGHT_CUSTOMER_INFO_ORDER_DIRECTOR', 'Director：');
define('RIGHT_CUSTOMER_INFO_ORDER_QUICK_TIME', 'Quick time：');
define('RIGHT_CUSTOMER_INFO_ORDER_REAL_PLAYER', 'Real player：');
define('RIGHT_CUSTOMER_INFO_ORDER_WINDOWS_MEDIA', 'Windows media：');
define('RIGHT_CUSTOMER_INFO_ORDER_PDF', 'Pdf：');
define('RIGHT_CUSTOMER_INFO_ORDER_JAVA', 'Java：');
define('RIGHT_TICKIT_ID_TITLE', '新建咨询号码');
define('RIGHT_TICKIT_EMAIL', '邮件');
define('RIGHT_TICKIT_CARD', '信用卡');
define('RIGHT_ORDER_INFO_ORDER_CUSTOMER_NAME', '顾客名：');
define('RIGHT_ORDER_INFO_ORDER_EMAIL', 'E-Mail 地址：');
define('RIGHT_ORDER_INFO_ORDER_CREDITCARD_TYPE', '信用卡类别：');
define('RIGHT_ORDER_INFO_ORDER_CREDITCARD_OWNER', '信用卡持有者：');
define('RIGHT_ORDER_INFO_ORDER_CREDITCARD_ID', '信用卡号：');
define('RIGHT_ORDER_INFO_ORDER_CREDITCARD_EXPIRE_TIME', '信用卡有效期限：');
define('RIGHT_ORDER_INFO_TRANS_NOTICE', '小心处理');
define('RIGHT_ORDER_INFO_TRANS_WAIT', '等待交易');
define('RIGHT_ORDER_INFO_INPUT_FINISH', '输入完毕');
define('RIGHT_ORDER_INFO_REPUTAION_SEARCH', '信用度调查：');
//user pama
define('TEXT_ECECUTE_PASSWORD_USER','更改密码');
define('RIGHT_ORDER_COMMENT_TITLE', '评论：');
define('BOX_LOCALIZATION_PREORDERS_STATUS', '预约状态设置');
define('BUTTON_MANUAL','手册');
define('HEADER_TEXT_PREORDERS', '预约管理');


//order div 
define('FILENAME_ORDER_DOWNLOAD','导出订单数据');
define('TEXT_FUNCTION_ORDER_ORDER_DATE','交易日期：');
define('TEXT_FUNCTION_HEADING_CUSTOMERS', '顾客名：');
define('TEXT_FUNCTION_HEADING_ORDER_TOTAL', '订单总额：');
define('TEXT_FUNCTION_HEADING_DATE_PURCHASED', '订购日期：');
define('TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_MEMBER','会员');
define('TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_CUSTOMER','非会员');


define('TEXT_MONEY_SYMBOL','日元');

define('FILENAME_ORDER_DOWNLOPAD','订单数据导出');
define('FRONT_CONFIGURATION_TITLE_TEXT', '前台：');
define('ADMIN_CONFIGURATION_TITLE_TEXT', '后台：');
define('FRONT_OR_ADMIN_CONFIGURATION_TITLE_TEXT', '前台・后台：');
define('HEADER_TEXT_ORDER_INFO', '订单信息▼');


//note

define('TEXT_ADD_NOTE','添加备忘');
define('TEXT_COMMENT_NOTE','内容');
define('TEXT_COLOR','备忘的颜色');
define('TEXT_TITLE_NOTE','标题');
define('HEADER_TEXT_CREATE_PREORDER', '创建预约订单');

define('TEXT_TORIHIKI_REPLACE_STR','～');
define('TEXT_TORIHIKI_HOUR_STR','时');
define('TEXT_TORIHIKI_MIN_STR','分');
define('TEXT_PREORDER_PAYMENT_METHOD', '支付方式：');
define('TEXT_PREORDER_NOT_COST', '未进款');
define('TEXT_PREORDER_COST_DATE', '进款日期：');
define('TEXT_PREORDER_PRODUCTS_NAME', '商品：');
define('TEXT_PREORDER_PRODUCTS_NOENTRANCE', '未进项'); 
define('TEXT_PREORDER_PRODUCTS_ENTRANCE', '进项');
define('TEXT_PREORDER_PRODUCTS_NUM', '个数：');
define('TEXT_PREORDER_PRODUCTS_UNIT', '个');
define('TEXT_PREORDER_PRODUCTS_CHARACTER_NAME', '交易人物名：');




define('TEXT_PAYMENT_NULL_TXT','请选择支付方式');
define('TEXT_TORIHIKI_LIST_DEFAULT_TXT','请选择');
define('BOX_TOOLS_CAMPAIGN', '优惠券设置');
define('TEXT_CURRENT_CHARACTER_NAME', '本公司交易人物名：');
define('BOX_CATALOG_SHOW_USELESS_OPTION','删除未使用选项');
define('TEXT_ORDER_ALARM_LINK', '警报');
define('HOUR_TEXT', '时');
define('MINUTE_TEXT', '分');
define('NOTICE_EXTEND_TITLE', '备忘录');
define('NOTICE_ALARM_TITLE', '警报');
define('NOTICE_DIFF_TIME_TEXT', '剩余');
define('TEXT_DISPLAY_NUMBER_OF_MANUAL', '当前显示<b>%d</b> &sim; <b>%d</b> (共<b>%d</b> 项)');
define('FILENAME_FILENAME_RESET_PWD_TEXT','统一重置密码');
define('FILENAME_CUSTOMERS_EXIT_TEXT','退会客户管理');
define('OPTION_CHARACTER_NAME', '客户的交易人物名');
define('NEXT_ORDER_TEXT', '下面的订单');
define('BEFORE_ORDER_TEXT', '历史订单');
define('CUSTOMER_INFO_TEXT', '客户信息');
define('BOX_CREATE_ADDRESS', '新建地址设置');
define('BOX_COUNTRY_FEE', '费用设置');
define('BOX_SHIPPING_TIME', '到货时间');
define('TEXT_REQUIRED', '必须');
define('STR_LAST_DAY_OF_FAULT_LINE','线路故障的最终日期：');
define('SECOND_TEXT','秒');


define('PAYMENT_METHOD','支付方法：');
define('DEPOSIT_STILL','未付款');
define('PAYMENT_DAY','付款日：');
define('PRODUCT','商品：');
define('INPUT','「入」');
define('NOT','「未」');
define('MANUAL','手册');
define('NUMBERS','个数：');
define('MONTHS','个');
define('OPTION','选项:');

define('DB_CONFIGURATION_TITLE_SHOP','店铺信息');
define('DB_CONFIGURATION_TITLE_MIN','最小值');
define('DB_CONFIGURATION_TITLE_MAX','最大值');
define('DB_CONFIGURATION_TITLE_IMAGE_DISPLAY','图像显示');
define('DB_CONFIGURATION_TITLE_DISPLAY_ACCOUNT','账号显示');
define('DB_CONFIGURATION_TITLE_MODULE_OPTIONS','模块・选择');
define('DB_CONFIGURATION_TITLE_CKING_DELIVERY','发货/包装');
define('DB_CONFIGURATION_TITLE_PRODUCT_LIST','商品一览显示');
define('DB_CONFIGURATION_TITLE_INVENTORY_MANAGEMENT','库存管理');
define('DB_CONFIGURATION_TITLE_RECORDING_LOG','登录显示/记录');
define('DB_CONFIGURATION_TITLE_PAGE_CACHE','页面缓存');
define('DB_CONFIGURATION_TITLE_EMAIL','发送E-Mail');
define('DB_CONFIGURATION_TITLE_DOWNLOAD_SALES','下载销售');
define('DB_CONFIGURATION_TITLE_GZIP','GZip压缩');
define('DB_CONFIGURATION_TITLE_SESSION','Session');
define('DB_CONFIGURATION_TITLE_BUSINESS_CALENDAR','营业日历');
define('DB_CONFIGURATION_TITLE_SEO','SEO URLs');
define('DB_CONFIGURATION_TITLE_DOCUMENTS','文件管理器');
define('DB_CONFIGURATION_TITLE_TIME_SETING','时间设置');
define('DB_CONFIGURATION_TITLE_MAXIMUM_VALUE','最大值');
define('DB_CONFIGURATION_TITLE_NEW_REVIEW','新评论设置');
define('DB_CONFIGURATION_TITLE_INSTALL_SAFETY_REVIEW','设置评论安全');
define('DB_CONFIGURATION_TITLE_PROGRAM','联盟计划');
define('DB_CONFIGURATION_TITLE_WARNING_SETTINGS','警告字符串设置');
define('DB_CONFIGURATION_TITLE_SIMPLE_INFORMATION','简易订单信息 ');
define('DB_CONFIGURATION_TITLE_GRAPH_SET',' 混合图表设置');
define('DB_CONFIGURATION_TITLE_INITIAL_SETTING_SHOP','商店初期设置');


define('DB_CONFIGURATION_DESCRIPTION_SHOP','商品的一般信息');
define('DB_CONFIGURATION_DESCRIPTION_MAX','函数/数据的最小值');
define('DB_CONFIGURATION_DESCRIPTION_MIN','函数/数据的最大值');
define('DB_CONFIGURATION_DESCRIPTION_IMAGE_DISPLAY','图像・参数');
define('DB_CONFIGURATION_DESCRIPTION_DISPLAY_ACCOUNT','顾客账号设置');
define('DB_CONFIGURATION_DESCRIPTION_MODULE_OPTIONS','不显示设置菜单');
define('DB_CONFIGURATION_DESCRIPTION_CKING_DELIVERY','商店受理的发货选择');
define('DB_CONFIGURATION_DESCRIPTION_PRODUCT_LIST','商品一览设置');
define('DB_CONFIGURATION_DESCRIPTION_INVENTORY_MANAGEMENT','库存设置');
define('DB_CONFIGURATION_DESCRIPTION_RECORDING_LOG','登录设置');
define('DB_CONFIGURATION_DESCRIPTION_PAGE_CACHE','缓存设置');
define('DB_CONFIGURATION_DESCRIPTION_EMAIL','发送E-Mail和HTML邮件的一般设置');
define('DB_CONFIGURATION_DESCRIPTION_DOWNLOAD_SALES','现在销售商品的选择');
define('DB_CONFIGURATION_DESCRIPTION_GZIP','GZip压缩的选择');
define('DB_CONFIGURATION_DESCRIPTION_SESSION','关于时域控制的选择');
define('DB_CONFIGURATION_DESCRIPTION_INITIAL_SETTING_SHOP','主页的初期设置');
define('DB_CONFIGURATION_DESCRIPTION_BUSINESS_CALENDAR','营业日历设置');
define('DB_CONFIGURATION_DESCRIPTION_SEO','Options for Ultimate SEO URLs by Chemo');

define('TEXT_TIME_LINK','到');
define('TEXT_BUTTON_ADD','添加输入框');
define('TEXT_ATTRIBUTE','属性');
define('TEXT_ATTRIBUTE_PUBLIC','公有');
define('TEXT_ATTRIBUTE_PRIVATE','私有');
define('TEXT_KEYWORD','关键词');
define('TEXT_GOOGLE_SEARCH','用GOOGLE搜%s关键词的结果');
define('TEXT_RENAME','重命名');
define('TEXT_INFO_KEYWORD','更改关键词');
define('TEXT_NO_SET_KEYWORD','不设置关键词');
define('TEXT_NO_DATA','找不到符合的信息');
define('TEXT_LAST_SEARCH_DATA','&nbsp;%s&nbsp;个检索结果');
define('TEXT_FIND_DATA_STOP','检索%s但是显示停止。');
define('TEXT_NOT_ENOUGH_DATA','从前面&nbsp;50&nbsp;个检索结果中不重复的检索结果有&nbsp;%s&nbsp;个');

define('HEADER_TEXT_PERSONAL_SETTING','个人设定');
define('BOX_HEADING_USER', '用户');
define('BOX_USER_ADMIN', '用户管理');
define('TEXT_ONE_TIME_CONFIG_SAVE','已保存');
define('BOX_USER_LOG', '访问日志');
define('BOX_USER_LOGOUT', '退出');

define('TEXT_SITE_COPYRIGHT' ,'Copyright © %s Haomai');

define('JUMP_PAGE_TEXT', 'Page');
define('JUMP_PAGE_BUTTON_TEXT', 'Go');

// javascript language
define('JS_TEXT_ONETIME_PWD_ERROR','密码有误');
define('JS_TEXT_INPUT_ONETIME_PWD','请输入一次性密码\r\n');
define('JS_TEXT_POSTAL_NUMBER_ERROR','邮编错误。');
// cleate_list
define('TEXT_CLEATE_LIST','登录列表');
define('TEXT_CLEATE_HISTORY','查看历史记录');
// products_tags
define('TEXT_P_TAGS_NO_TAG','无标签数据，请添加');
define('UPDATE_MSG_TEXT', '更新');
define('CL_TEXT_DATE_MONDAY', '一');
define('CL_TEXT_DATE_TUESDAY', '二');
define('CL_TEXT_DATE_WEDNESDAY', '三');
define('CL_TEXT_DATE_THURSDAY', '四');
define('CL_TEXT_DATE_FRIDAY', '五');
define('CL_TEXT_DATE_STATURDAY', '六');
define('CL_TEXT_DATE_SUNDAY', '日');
define('BUTTON_ADD_TEXT', '添加');
define('CSV_HEADER_TEXT', '账号创建日期，性别，姓，名，出生年月日，邮箱地址，公司名称，邮编号码，省市县，市区镇村，地址1，地址2，国名，电话号码，FAX号码，邮件杂志订阅，点数');
define('CSV_EXPORT_TEXT', 'CSV输出');
define('TEXT_ALL','全部');
define('TEXT_USER_ADDED','创建者:');
define('TEXT_USER_UPDATE','更新者:');
define('TEXT_DATE_ADDED','创建日:');
define('TEXT_DATE_UPDATE','更新日:');
define('MESSAGE_FINISH_ORDER_TEXT', '订单ID%s的成功：交易完了');
define('TEXT_UNSET_DATA','没有数据');
