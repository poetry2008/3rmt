<?php
/*
  $Id$
*/

//
// mb_internal_encoding() is set for PHP-4.3.x(Zend Multibyte)
//
// A compatible module is loaded for environment without mbstring-extension
//
/*
if (extension_loaded('mbstring')) {
  mb_internal_encoding('EUC-JP'); // 指定内部代码;
} else {
  include_once(DIR_WS_LANGUAGES . $language . '/jcode.phps');
  include_once(DIR_WS_LANGUAGES . $language . '/mbstring_wrapper.php');
}
*/
// look in your $PATH_LOCALE/locale directory for available locales..
// on RedHat6.0 I used 'en_US'
// on FreeBSD 4.0 I use 'en_US.ISO_8859-1'
// this may not work under win32 environments..
setlocale(LC_TIME, 'ja_JP');
define('DATE_FORMAT_SHORT', '%Y/%m/%d');  // this is used for strftime()
define('DATE_FORMAT_LONG', '%Y年%B%e日 %A'); // this is used for strftime()
define('DATE_FORMAT', 'Y/m/d'); // this is used for date()
define('PHP_DATE_TIME_FORMAT', 'Y/m/d H:i:s'); // this is used for date()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');

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
define('TITLE', STORE_NAME); //输入店铺名。 
// header text in includes/header.php 
define('HEADER_TITLE_TOP', '管理菜单'); 
define('HEADER_TITLE_SUPPORT_SITE', '支持网站'); 
define('HEADER_TITLE_ONLINE_CATALOG', '在线目录'); 
define('HEADER_TITLE_ADMINISTRATION', '管理菜单'); 
// text for gender 
define('MALE', '男性'); 
define('FEMALE', '女性'); 
// text for date of birth example 
define('DOB_FORMAT_STRING', 'yyyy-mm-dd'); 
// configuration box text in includes/boxes/configuration.php 
define('BOX_HEADING_CONFIGURATION', '基本设定'); 
define('BOX_CONFIGURATION_MYSTORE', '店铺'); 
define('BOX_CONFIGURATION_LOGGING', '日志'); 
define('BOX_CONFIGURATION_CACHE', '缓存'); 
// modules box text in includes/boxes/modules.php 
define('BOX_HEADING_MODULES', '模块设定'); 
define('BOX_MODULES_PAYMENT', '支付模块'); 
define('BOX_MODULES_SHIPPING', '配送模块'); 
define('BOX_MODULES_ORDER_TOTAL', '合计模块'); 
define('BOX_MODULES_METASEO', 'SEO 模块');
// categories box text in includes/boxes/catalog.php 
define('BOX_HEADING_CATALOG', '目录管理'); 
define('BOX_CATALOG_CATEGORIES_PRODUCTS', '分类/商品登录'); 
define('BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES', '商品option登录'); 
define('BOX_CATALOG_MANUFACTURERS', 'maker登录'); 
define('BOX_CATALOG_REVIEWS', '评论管理'); 
define('BOX_CATALOG_SPECIALS', '特价商品登录'); 
define('BOX_CATALOG_PRODUCTS_EXPECTED', '预定进货商品管理'); 
// customers box text in includes/boxes/customers.php 
define('BOX_HEADING_CUSTOMERS', '顾客管理'); 
define('BOX_CUSTOMERS_CUSTOMERS', '顾客管理'); 
define('BOX_CUSTOMERS_ORDERS', '订购管理'); 
// taxes box text in includes/boxes/taxes.php 
define('BOX_HEADING_LOCATION_AND_TAXES', '地域 / 税率设定'); 
define('BOX_TAXES_COUNTRIES', '国名设定'); 
define('BOX_TAXES_ZONES', '地域设定'); 
define('BOX_TAXES_GEO_ZONES', '地域税设定'); 
define('BOX_TAXES_TAX_CLASSES', '税类别设定'); 
define('BOX_TAXES_TAX_RATES', '税率设定'); 
// reports box text in includes/boxes/reports.php 
define('BOX_HEADING_REPORTS', '报告'); 
define('BOX_REPORTS_PRODUCTS_VIEWED', '各商品的浏览次数'); 
define('BOX_REPORTS_PRODUCTS_PURCHASED', '各商品的贩卖数'); 
define('BOX_REPORTS_ORDERS_TOTAL', '各顾客的营业额设定'); 
define('BOX_REPORTS_SALES_REPORT2', '营业额管理'); 
// tools text in includes/boxes/tools.php 
define('BOX_HEADING_TOOLS', '各种工具'); 
define('BOX_TOOLS_BACKUP', 'DB备份管理'); 
define('BOX_TOOLS_BANNER_MANAGER', 'banner管理'); 
define('BOX_TOOLS_CACHE', '缓存控制'); 
define('BOX_TOOLS_DEFINE_LANGUAGE', '语言文件管理'); 
define('BOX_TOOLS_FILE_MANAGER', '文件管理'); 
define('BOX_TOOLS_MAIL', '邮件发送'); 
define('BOX_TOOLS_NEWSLETTER_MANAGER', '邮件杂志管理'); 
define('BOX_TOOLS_SERVER_INFO', '服务器信息'); 
define('BOX_TOOLS_WHOS_ONLINE', '在线用户'); 
define('BOX_TOOLS_PRESENT','礼物功能'); 
// localizaion box text in includes/boxes/localization.php 
define('BOX_HEADING_LOCALIZATION', '本地化'); 
define('BOX_LOCALIZATION_CURRENCIES', '货币设定'); 
define('BOX_LOCALIZATION_LANGUAGES', '语言设定'); 
define('BOX_LOCALIZATION_ORDERS_STATUS', '订单状态设定'); 
// javascript messages 
define('JS_ERROR', '格式处理过程中出现错误!\n请进行如下修改:\n\n'); 
define('JS_OPTIONS_VALUE_PRICE', '* 指定新商品属性的价格。\n'); 
define('JS_OPTIONS_VALUE_PRICE_PREFIX', '* 指定新商品属性价格的接头符（+ —）。\n'); 
define('JS_PRODUCTS_NAME', '* 指定新商品名称。\n'); 
define('JS_PRODUCTS_DESCRIPTION', '* 输入新商品的说明文。\n'); 
define('JS_PRODUCTS_PRICE', '* 指定新商品的价格。\n'); 
define('JS_PRODUCTS_WEIGHT', '* 指定商品的重量。\n'); 
define('JS_PRODUCTS_QUANTITY', '*指定新商品的数量。\n'); 
define('JS_PRODUCTS_MODEL', '* 指定新商品的型号。\n'); 
define('JS_PRODUCTS_IMAGE', '* 指定新商品的图片。\n'); 
define('JS_SPECIALS_PRODUCTS_PRICE', '* 指定商品的新价格。\n'); 
define('JS_GENDER', '* \'性別\' 没有选择。\n'); 
define('JS_FIRST_NAME', '* \'名前\' 至少 ' . ENTRY_FIRST_NAME_MIN_LENGTH .  ' 文字以上。\n'); 
define('JS_LAST_NAME', '* \'姓\' 至少 ' .  ENTRY_LAST_NAME_MIN_LENGTH . ' 文字以上。\n'); 
define('JS_FIRST_NAME_F', '* \'姓名(平假名)\' 至少 ' .  ENTRY_FIRST_NAME_MIN_LENGTH . ' 文字以上。\n'); 
define('JS_LAST_NAME_F', '* \'姓(平假名)\' 至少 ' .  ENTRY_LAST_NAME_MIN_LENGTH . ' 文字以上。\n'); 
define('JS_DOB', '* \'出生年月\' 按照以下形式输入: xxxx/xx/xx (年/月/日)。\n'); 
define('JS_EMAIL_ADDRESS', '* \'E-Mail 地址\'至少 ' .  ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' 文字以上。\n'); 
define('JS_ADDRESS', '* \'住所１\' 至少 ' .  ENTRY_STREET_ADDRESS_MIN_LENGTH . ' 文字以上。\n'); 
define('JS_POST_CODE', '* \'邮编\' 至少 ' .  ENTRY_POSTCODE_MIN_LENGTH . ' 文字以上。\n'); 
define('JS_CITY', '* \'市区町村\' 至少 ' . ENTRY_CITY_MIN_LENGTH .  ' 文字以上。\n'); 
define('JS_STATE', '* \'都道府県\' 没有选择。\n'); 
define('JS_STATE_SELECT', '-- 从上面选择 --'); 
define('JS_ZONE', '* \'都道府県\' 从列表中选择。'); 
define('JS_COUNTRY', '* \'国\' 选择国家。\n'); 
define('JS_TELEPHONE', '* \'电话号码\' 至少 ' .  ENTRY_TELEPHONE_MIN_LENGTH . ' 文字以上。\n'); 
define('JS_PASSWORD', '* \'秘密\' と \'确认密码\' 至少' .  ENTRY_PASSWORD_MIN_LENGTH . ' 文字以上。\n'); 
define('JS_ORDER_DOES_NOT_EXIST', '订单号 %s 不存在!'); 
define('CATEGORY_PERSONAL', '个人信息'); 
define('CATEGORY_ADDRESS', '住所'); 
define('CATEGORY_CONTACT', '联系地址'); 
define('CATEGORY_COMPANY', '公司名'); 
define('CATEGORY_PASSWORD', '密码'); 
define('CATEGORY_OPTIONS', 'option'); 
define('ENTRY_GENDER', '性別:'); 
define('ENTRY_FIRST_NAME', '名:'); 
define('ENTRY_LAST_NAME', '姓:'); 
//add 
define('ENTRY_FIRST_NAME_F', '名(平假名):'); 
define('ENTRY_LAST_NAME_F', '姓(平假名):'); 
define('ENTRY_DATE_OF_BIRTH', '出生年月:'); 
define('ENTRY_EMAIL_ADDRESS', 'E-Mail 地址:'); 
define('ENTRY_COMPANY', '公司名:'); 
define('ENTRY_STREET_ADDRESS', '住所1:'); 
define('ENTRY_SUBURB', '住所2:'); 
define('ENTRY_POST_CODE', '邮编:'); 
define('ENTRY_CITY', '市区町村:'); 
define('ENTRY_STATE', '都道府県:'); 
define('ENTRY_COUNTRY', '国名:'); 
define('ENTRY_TELEPHONE_NUMBER', '电话号码:'); 
define('ENTRY_FAX_NUMBER', '传真号:'); 
define('ENTRY_NEWSLETTER', '邮件杂志:'); 
define('ENTRY_NEWSLETTER_YES', '订阅'); 
define('ENTRY_NEWSLETTER_NO', '不订阅'); 
define('ENTRY_PASSWORD', '密码:'); 
define('ENTRY_PASSWORD_CONFIRMATION', '确认密码:'); 
define('PASSWORD_HIDDEN', '********'); 
// images 
define('IMAGE_ANI_SEND_EMAIL', '发送邮件'); 
define('IMAGE_BACK', '返回'); 
define('IMAGE_BACKUP', '备份'); 
define('IMAGE_CANCEL', '取消'); 
define('IMAGE_CONFIRM', '确认'); 
define('IMAGE_COPY', '复制'); 
define('IMAGE_COPY_TO', '复制地址'); 
define('IMAGE_
define', '定义'); 
define('IMAGE_DELETE', '删除'); 
define('IMAGE_EDIT', '编集'); 
define('IMAGE_EMAIL', 'E-Mail'); 
define('IMAGE_FILE_MANAGER', '文件管理'); 
define('IMAGE_ICON_STATUS_GREEN', '有效'); 
define('IMAGE_ICON_STATUS_GREEN_LIGHT', '设为有效'); 
define('IMAGE_ICON_STATUS_RED', '无效'); 
define('IMAGE_ICON_STATUS_RED_LIGHT', '设为无效'); 
define('IMAGE_ICON_INFO', '信息'); 
define('IMAGE_INSERT', '插入'); 
define('IMAGE_LOCK', '锁定'); 
define('IMAGE_MOVE', '移动'); 
define('IMAGE_NEW_BANNER', '新banner'); 
define('IMAGE_NEW_CATEGORY', '新分类'); 
define('IMAGE_NEW_COUNTRY', '新国名'); 
define('IMAGE_NEW_CURRENCY', '新货币'); 
define('IMAGE_NEW_FILE', '新文件'); 
define('IMAGE_NEW_FOLDER', '新文件夹'); 
define('IMAGE_NEW_LANGUAGE', '新语言'); 
define('IMAGE_NEW_NEWSLETTER', '新的邮件杂志'); 
define('IMAGE_NEW_PRODUCT', '新商品'); 
define('IMAGE_NEW_TAX_CLASS', '新税种'); 
define('IMAGE_NEW_TAX_RATE', '新税率'); 
define('IMAGE_NEW_TAX_ZONE', '新税地域'); 
define('IMAGE_NEW_ZONE', '新地域'); 
define('IMAGE_NEW_TAG', '新标签'); 



define('IMAGE_ORDERS', '订购'); 
define('IMAGE_ORDERS_INVOICE', '货单'); 
define('IMAGE_ORDERS_PACKINGSLIP', '发送票'); 
define('IMAGE_PREVIEW', '预览'); 
define('IMAGE_RESTORE', '复原'); 
define('IMAGE_RESET', '再设定'); 
define('IMAGE_SAVE', '保存'); 
define('IMAGE_SEARCH', '检索'); 
define('IMAGE_SELECT', '选择'); 
define('IMAGE_SEND', '发信'); 
define('IMAGE_SEND_EMAIL', '发送邮件'); 
define('IMAGE_UNLOCK', '解锁'); 
define('IMAGE_UPDATE', '更新'); 
define('IMAGE_UPDATE_CURRENCIES', '汇率更新'); 
define('IMAGE_UPLOAD', '上传'); 
define('ICON_CROSS', '假(False)'); 
define('ICON_CURRENT_FOLDER', '现在的文件夹'); 
define('ICON_DELETE', '删除'); 
define('ICON_ERROR', '错误'); 
define('ICON_FILE', '文件'); 
define('ICON_FILE_DOWNLOAD', '下载'); 
define('ICON_FOLDER', '文件夹'); 
define('ICON_LOCKED', '锁定'); 
define('ICON_PREVIOUS_LEVEL', '前一级'); 
define('ICON_PREVIEW', '预览'); 
define('ICON_STATISTICS', '统计'); 
define('ICON_SUCCESS', '成功'); 
define('ICON_TICK', '真(True)'); 
define('ICON_UNLOCKED', '解锁'); 
define('ICON_WARNING', '警告'); 
// constants for use in tep_prev_next_display function 
define('TEXT_RESULT_PAGE', ' %s / %d 页'); 
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b>个banner中)'); 
define('TEXT_DISPLAY_NUMBER_OF_COUNTRIES', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 个国家中)'); 
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b>个顾客中)'); 
define('TEXT_DISPLAY_NUMBER_OF_CURRENCIES', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 种货币中)'); 
define('TEXT_DISPLAY_NUMBER_OF_LANGUAGES', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 种语言中)'); 
define('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 个maker中)'); 
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 份邮件杂志中)'); 
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 份订单中)'); 
define('TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 个订单状态中)'); 
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b>个商品中)'); 
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 个预定进货商品中)'); 
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b>个商品评论中)'); 
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 个特价商品中)'); 
define('TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 种税收中)'); 
define('TEXT_DISPLAY_NUMBER_OF_TAX_ZONES', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 个税的地域中)'); 
define('TEXT_DISPLAY_NUMBER_OF_TAX_RATES', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 种税率中)'); 
define('TEXT_DISPLAY_NUMBER_OF_ZONES', '<b>%d</b> &sim; <b>%d</b>显示某号 (<b>%d</b> 个地域中)'); 
define('PREVNEXT_BUTTON_PREV', '<<'); 
define('PREVNEXT_BUTTON_NEXT', '>>'); 
define('TEXT_DEFAULT', '默认'); 
define('TEXT_SET_DEFAULT', '设为默认'); 
define('TEXT_FIELD_REQUIRED', ' * 必填'); 
define('ERROR_NO_DEFAULT_CURRENCY_D', '错误: 没有设定基本货币。 在 管理菜单ー->本地化->货币设定: 中确认设定。'); 
define('TEXT_CACHE_CATEGORIES', '分类box '); 
define('TEXT_CACHE_MANUFACTURERS', 'maker box'); 
define('TEXT_CACHE_ALSO_PURCHASED', '关联商品模块'); 
define('TEXT_NONE', '--无--'); 
define('TEXT_TOP', 'top'); 
define('EMAIL_SIGNATURE',C_EMAIL_FOOTER); 
//Add Japanese osCommerce 
// Include OSC-AFFILIATE include("affiliate_japanese.php"); 
//Add languages 
//------------------------ 
//contents 
define('BOX_TOOLS_CONTENTS', '内容管理'); 
define('TEXT_DISPLAY_NUMBER_OF_CONTENS', '<b>%d</b> &sim; <b>%d</b> 显示某号 (<b>%d</b>内容中)'); 
//latest news 
define('BOX_TOOLS_LATEST_NEWS', '新到消息管理'); 
define('BOX_CATALOG_PRODUCTS_UP', '上传商品数据'); 
define('BOX_CATALOG_PRODUCTS_DL', '下载商品数据'); 
define('BOX_TOOLS_CL', '日历'); 
define('BOX_CATALOG_CATEGORIES_TAGS', '标签登录'); 
define('BOX_CATALOG_IMAGE_DOCUMENTS', '图片文件管理'); 
define('BOX_CATALOG_IMAGE_DOCUMENT', '图片文件管理'); 
define('BOX_CATALOG_PRODUCTS_TAGS', 'タグ登録');


define('TABLE_HEADING_SITE', 'SITE');
?> 
