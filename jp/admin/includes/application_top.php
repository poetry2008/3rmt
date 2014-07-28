<?php
/*
  $Id$
*/
/* -------------------------------------
    功能: 压缩网页  
    参数: $content(string) 网页内容
    返回值: 压缩后的网页内容(string) 
 ------------------------------------ */
function ob_gzip($content)
{  
    //如果页面头部信息还没有输出 
    //并且zlib扩展已经加载到PHP中
    //并且浏览器支持GZIP的页面 
    if(!headers_sent() && extension_loaded("zlib") && strstr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip")){
      //开始压缩网页
      $content = gzencode($content,6); //压缩级别为6(最小为0，最大为9)
                                       
      header("Content-Encoding: gzip");
      header("Vary: Accept-Encoding");
      header("Content-Length: ".strlen($content));
    }
    return $content;
}
//启用页面压缩输出
ob_start('ob_gzip');
$GLOBALS['HTTP_GET_VARS']  = $_GET;
$GLOBALS['HTTP_POST_VARS'] = $_POST;

  setlocale (LC_ALL, 'ja_JP.UTF-8');
if(function_exists('date_default_timezone_set'))date_default_timezone_set('Asia/Shanghai');
// Start the clock for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

// Set the level of error reporting
  error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE & ~E_WARNING);
  ini_set("display_errors", "On");

// Check if register_globals is enabled.
// Since this is a temporary measure this message is hardcoded. The requirement will be removed before 2.2 is finalized.
  if (function_exists('ini_get')) {
    ini_get('register_globals') or exit('FATAL ERROR: register_globals is disabled in php.ini, please enable it!');
  }

// Disable use_trans_sid as tep_href_link() does this manually
  if (function_exists('ini_set')) {
    @ini_set('session.use_trans_sid', 0);
  }


// Set the local configuration parameters - mainly for developers
  if (file_exists('includes/local/configure.php')) include('includes/local/configure.php');

// Include application configuration parameters
  require('includes/configure.php');

// Define the project version
  define('PROJECT_VERSION', 'Preview Release 2.2-MS1');

// Used in the "Backup Manager" to compress backups
  define('LOCAL_EXE_GZIP', '/usr/bin/gzip');
  define('LOCAL_EXE_GUNZIP', '/usr/bin/gunzip');
  define('LOCAL_EXE_ZIP', '/usr/local/bin/zip');
  define('LOCAL_EXE_UNZIP', '/usr/local/bin/unzip');

// define the filenames used in the project
  define('FILENAME_MODULE_TOTAL', 'module_total.php');
  define('FILENAME_CONFIGURATION_META', 'configuration_meta.php');
  define('FILENAME_CUSTOMERS_EXIT_HISTORY', 'customers_exit_history.php');
  define('FILENAME_OPTION', 'option.php');
  define('FILENAME_MARKS', 'marks.php');
  define('FILENAME_RESET_PWD','reset_pwd.php');
  define('FILENAME_ALARM', 'alarm.php');
  define('FILENAME_CAMPAIGN', 'campaign.php');
  define('FILENAME_ASSETS', 'assets.php');
  define('FILENAME_PRINT_ASSETS', 'print_assets.php');
  define('FILENAME_ORDERS_DOWNLOAD', 'orders_download.php');
  define('FILENAME_PREORDERS', 'preorders.php');
  define('FILENAME_PREORDERS_STATUS', 'preorders_status.php');
  define('FILENAME_FINAL_PREORDERS', 'final_preorders.php');
  define('FILENAME_CHANGEPWD', 'changepwd.php');
  define('FILENAME_POINT_EMAIL', 'point_email.php');
  define('FILENAME_OA_FORM', 'oa_form.php'); 
  define('FILENAME_OA_GROUP', 'oa_group.php'); 
  define('FILENAME_OA_LINK_GROUP', 'oa_link_group.php'); 
  define('FILENAME_OA_ITEM', 'oa_item.php'); 
  define('FILENAME_PWD_LOG', 'pwd_log.php');
  define('FILENAME_PRODUCTS_PRICE','products_price.php');
  define('FILENAME_PW_MANAGER', 'id_manager.php');
  define('FILENAME_REDIREC_URL','redirec_url.php');
  define('FILENAME_PWD_AJAX', 'pwd_ajax.php');
  define('FILENAME_PW_MANAGER_LOG', 'pw_manager_log.php');
  define('FILENAME_RECORD', 'record.php');
  define('FILENAME_INVENTORY', 'inventory.php');
  define('FILENAME_IMAGE_DOCUMENT', 'image_documents.php');
  define('FILENAME_IMAGE_DOCUMENTS', 'image_documents.php');
  define('FILENAME_TAGS', 'tags.php');
  define('FILENAME_BACKUP', 'backup.php');
  define('FILENAME_BANNER_MANAGER', 'banner_manager.php');
  define('FILENAME_BANNER_STATISTICS', 'banner_statistics.php');
  define('FILENAME_CACHE', 'cache.php');
  define('FILENAME_CATALOG_ACCOUNT_HISTORY_INFO', 'account_history_info.php');
  define('FILENAME_CATEGORIES', 'categories.php');
  define('FILENAME_CATEGORIES_ADMIN', 'categories_admin.php');
  define('FILENAME_CONFIGURATION', 'configuration.php');
  define('FILENAME_COUNTRIES', 'countries.php');
  define('FILENAME_CURRENCIES', 'currencies.php');
  define('FILENAME_CUSTOMERS', 'customers.php');
  define('FILENAME_SEARCH', 'search.php');
  define('FILENAME_DEFAULT', 'index.php');
  define('FILENAME_USERS', 'users.php');
  define('FILENAME_LOGIN', 'users_login.php');
  define('FILENAME_DEFINE_LANGUAGE', 'define_language.php');
  define('FILENAME_FILE_MANAGER', 'file_manager.php');
  define('FILENAME_GEO_ZONES', 'geo_zones.php');
  define('FILENAME_LANGUAGES', 'languages.php');
  define('FILENAME_MAIL', 'mail.php');
  define('FILENAME_MANUFACTURERS', 'manufacturers.php');
  define('FILENAME_MODULES', 'modules.php');
  define('FILENAME_NEWSLETTERS', 'newsletters.php');
  define('FILENAME_ORDERS', 'orders.php');
  define('FILENAME_ORDERS_EDIT', 'edit_orders.php');
  define('FILENAME_ORDERS_INVOICE', 'invoice.php');
  define('FILENAME_ORDERS_PACKINGSLIP', 'packingslip.php');
  define('FILENAME_ORDERS_STATUS', 'orders_status.php');
  define('FILENAME_POPUP_IMAGE', 'popup_image.php');
  define('FILENAME_PRESENT','present.php');
  define('FILENAME_PRODUCTS_ATTRIBUTES', 'products_attributes.php');
  define('FILENAME_PRODUCTS_EXPECTED', 'products_expected.php');
  define('FILENAME_REVIEWS', 'reviews.php');
  define('FILENAME_SERVER_INFO', 'server_info.php');
  define('FILENAME_SHIPPING_MODULES', 'shipping_modules.php');
  define('FILENAME_SPECIALS', 'specials.php');
  define('FILENAME_STATS_CUSTOMERS', 'stats_customers.php');
  define('FILENAME_STATS_PRODUCTS_PURCHASED', 'stats_products_purchased.php');
  define('FILENAME_STATS_PRODUCTS_VIEWED', 'stats_products_viewed.php');
  define('FILENAME_TAX_CLASSES', 'tax_classes.php');
  define('FILENAME_TAX_RATES', 'tax_rates.php');
  define('FILENAME_WHOS_ONLINE', 'whos_online.php');
  define('FILENAME_ZONES', 'zones.php');
  define('FILENAME_FAQ', 'faq.php');
  define('FILENAME_REFERER', 'referer.php');
  define('FILENAME_BUTTONS', 'buttons.php');
  define('FILENAME_ROSTER_RECORDS','roster_records.php');
  
  //add files
  define('FILENAME_DATA_MANAGEMENT','data_management.php');
  define('FILENAME_BANK_CL', 'calendar.php');
  define('FILENAME_CONTENTS', 'contents.php');
  define('FILENAME_NEWS', 'news.php'); 
  define('FILENAME_PRODUCTS_UP', 'products_up.php'); 
  define('FILENAME_PRODUCTS_DL', 'products_dl.php'); 
  define('FILENAME_STATS_SALES_REPORT', 'stats_sales_report.php');
  define('FILENAME_OPTIONS_UP', 'options_up.php');
  define('FILENAME_OPTIONS_DL', 'options_dl.php');
  define('FILENAME_NEW_CUSTOMERS', 'new_customers.php');
  define('FILENAME_BILL_TEMPLATES', 'bill_templates.php');
  define('FILENAME_ADDRESS', 'address.php');
  define('FILENAME_HELP_INFO','help_info.php');
  define('FILENAME_PERSONAL_SETTING','personal_setting.php');
  define('FILENAME_ALERT_LOG','alert_log.php');
  define('FILENAME_BUSINESS_MEMO','business_memo.php');
  define('FILENAME_MAIL_TEMPLATES','mail_templates.php');
  define('FILENAME_ALL_ORDERS','all_orders.php');
  define('FILENAME_GROUPS','groups.php');

// define the database table names used in the project
  define('TABLE_CUSTOMERS_BASKET_OPTIONS', 'customers_basket_options');
  define('TABLE_OPTION_GROUP', 'option_group');
  define('TABLE_OPTION_ITEM', 'option_item');
  define('FILENAME_PRODUCTS_MANUAL','products_manual.php');

// define the database table names used in the project
  define('TABLE_CUSTOMERS_EXIT_HISTORY', 'customers_exit_history');
  define('TABLE_ALARM', 'alarm');
  define('TABLE_NOTICE', 'notice');
  define('TABLE_CAMPAIGN', 'campaign');
  define('TABLE_CUSTOMER_TO_CAMPAIGN', 'customer_to_campaign');
  define('TABLE_PREORDERS', 'preorders');
  define('TABLE_PREORDERS_OA_FORMVALUE', 'preorders_oa_formvalue'); 
  define('TABLE_PREORDERS_OPERATOR', 'preorders_operator');
  define('TABLE_PREORDERS_PRODUCTS', 'preorders_products');
  define('TABLE_PREORDERS_PRODUCTS_ATTRIBUTES', 'preorders_products_attributes');
  define('TABLE_PREORDERS_PRODUCTS_DOWNLOAD', 'preorders_products_download');
  define('TABLE_PREORDERS_PRODUCTS_TO_ACTOR', 'preorders_products_to_actor');
  define('TABLE_PREORDERS_QUESTIONS', 'preorders_questions');
  define('TABLE_PREORDERS_QUESTIONS_PRODUCTS', 'preorders_questions_products');
  define('TABLE_PREORDERS_STATUS', 'preorders_status');
  define('TABLE_PREORDERS_STATUS_HISTORY', 'preorders_status_history');
  define('TABLE_PREORDERS_TEMP', 'preorders_temp');
  define('TABLE_PREORDERS_TOTAL', 'preorders_total');
  define('TABLE_PREORDERS_TO_BUTTONS', 'preorders_to_buttons');
  define('TABLE_PWD_CHECK', 'pwd_check');
  define('TABLE_FAQ_CATEGORIES', 'faq_categories');
  define('TABLE_FAQ_QUESTION',  'faq_question');
  define('TABLE_FAQ_CATEGORIES_DESCRIPTION', 'faq_categories_description');
  define('TABLE_FAQ_QUESTION_DESCRIPTION',  'faq_question_description');
  define('TABLE_FAQ_QUESTION_TO_CATEGORIES','faq_question_to_categories');
  define('TABLE_FAQ_SORT','faq_sort');
  define('TABLE_OA_GROUP', 'oa_group'); 
  define('TABLE_OA_FORM', 'oa_form'); 
  define('TABLE_OA_FORM_GROUP', 'oa_form_group'); 
  define('TABLE_OA_ITEM', 'oa_item'); 
  define('TABLE_OA_FORMVALUE', 'oa_formvalue'); 
  define('TABLE_POINT_MAIL','point_mail');
  define('TABLE_BESTSELLERS_TIME_TO_CATEGORY','bestsellers_time_to_category');
  define('TABLE_ONCE_PWD_LOG','once_pwd_log');
  define('TABLE_LETTERS','letters');
  define('TABLE_IDPW','idpw');
  define('TABLE_IDPW_LOG','idpw_log');
  define('TABLE_SITENAME','sitename');
  define('TABLE_USERS','users');
  define('TABLE_CATEGORIES_TO_MISSION', 'categories_to_mission');
  define('TABLE_PRODUCTS_TO_INVENTORY', 'products_to_inventory');
  define('TABLE_SESSION_LOG', 'session_log');
  define('TABLE_RECORD', 'record');
  define('TABLE_MISSION', 'mission');
  define('TABLE_PRODUCTS_SHIPPING_TIME','products_shipping_time');
  define('TABLE_IMAGE_DOCUMENTS', 'image_documents');
  define('TABLE_IMAGE_DOCUMENT_TYPES', 'image_document_types');
  define('TABLE_PRODUCTS_TO_IMAGE_DOCUMENTS', 'products_to_image_documents');
  define('TABLE_TAGS', 'tags');
  define('TABLE_PRODUCTS_TO_TAGS', 'products_to_tags');
  define('TABLE_ADDRESS_BOOK', 'address_book');
  define('TABLE_ADDRESS_FORMAT', 'address_format');
  define('TABLE_BANNERS', 'banners');
  define('TABLE_BANNERS_HISTORY', 'banners_history');
  define('TABLE_CATEGORIES', 'categories');
  define('TABLE_CATEGORIES_DESCRIPTION', 'categories_description');
  define('TABLE_CONFIGURATION', 'configuration');
  define('TABLE_CONFIGURATION_GROUP', 'configuration_group');
  define('TABLE_COUNTRIES', 'countries');
  define('TABLE_CURRENCIES', 'currencies');
  define('TABLE_CUSTOMERS', 'customers');
  define('TABLE_CUSTOMERS_BASKET', 'customers_basket');
  define('TABLE_CUSTOMERS_BASKET_ATTRIBUTES', 'customers_basket_attributes');
  define('TABLE_CUSTOMERS_INFO', 'customers_info');
  define('TABLE_LANGUAGES', 'languages');
  define('TABLE_MANUFACTURERS', 'manufacturers');
  define('TABLE_MANUFACTURERS_INFO', 'manufacturers_info');
  define('TABLE_NEWSLETTERS', 'newsletters');
  define('TABLE_ORDERS', 'orders');
  define('TABLE_ORDERS_PRODUCTS', 'orders_products');
  define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', 'orders_products_attributes');
  define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', 'orders_products_download');
  define('TABLE_ORDERS_STATUS', 'orders_status');
  define('TABLE_ORDERS_STATUS_HISTORY', 'orders_status_history');
  define('TABLE_ORDERS_TOTAL', 'orders_total');
  define('TABLE_PRODUCTS', 'products');
  define('TABLE_PRODUCTS_IMAGES', 'products_images');
  define('TABLE_PRODUCTS_ATTRIBUTES', 'products_attributes');
  define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', 'products_attributes_download');
  define('TABLE_PRODUCTS_DESCRIPTION', 'products_description');
  define('TABLE_PRODUCTS_NOTIFICATIONS', 'products_notifications');
  define('TABLE_PRODUCTS_OPTIONS', 'products_options');
  define('TABLE_PRODUCTS_OPTIONS_VALUES', 'products_options_values');
  define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', 'products_options_values_to_products_options');
  define('TABLE_PRODUCTS_TO_CATEGORIES', 'products_to_categories');
  define('TABLE_REVIEWS', 'reviews');
  define('TABLE_REVIEWS_DESCRIPTION', 'reviews_description');
  define('TABLE_SESSIONS', 'sessions');
  define('TABLE_SITES', 'sites');
  define('TABLE_SPECIALS', 'specials');
  define('TABLE_TAX_CLASS', 'tax_class');
  define('TABLE_TAX_RATES', 'tax_rates');
  define('TABLE_GEO_ZONES', 'geo_zones');
  define('TABLE_ZONES_TO_GEO_ZONES', 'zones_to_geo_zones');
  define('TABLE_WHOS_ONLINE', 'whos_online');
  define('TABLE_ZONES', 'zones');
  define('TABLE_CALENDAR', 'calendar'); //add calendar
  define('TABLE_BANK_CALENDAR', 'bank_calendar'); //add calendar
  
  //Add DB - ds-style
  define('TABLE_INFORMATION_PAGE', 'information_page');//information box
  define('TABLE_NEWS', 'news'); //latest_news
define('TABLE_PERMISSIONS','permissions');


  define('TABLE_PRESENT_GOODS', 'present_goods');
  define('TABLE_PRESENT_APPLICANT', 'present_applicant');
  define('TABLE_MAIL_MAGAZINE', 'mail_magazine');
  define('TABLE_CALENDER', 'calendar');
  define('TABLE_BUTTONS',  'buttons');
  define('TABLE_ORDERS_TO_BUTTONS',  'orders_to_buttons');
  define('TABLE_BILL_TEMPLATES',  'bill_templates');
  define('TABLE_ADDRESS','address');
  define('TABLE_ADDRESS_ORDERS','address_orders');
  define('TABLE_COUNTRY_FEE','country_fee');
  define('TABLE_AREA_FEE','country_area');
  define('TABLE_COUNTRY_CITY','country_city');
  define('TABLE_ADDRESS_HISTORY','address_history');
  define('TABLE_PRODUCTS_SHIPPING_TIME','products_shipping_time');
  define('TABLE_COUNTRY_AREA','country_area');
  define('TABLE_COUNTRY_FEE','country_fee');
  define('TABLE_COUNTRY_CITY','country_city');
  define('TABLE_OCONFIG',  'other_config');
  define('TABLE_CUSTOMERS_PIC_LIST', 'customers_pic_list'); 
  define('TABLE_CALENDAR_STATUS','calendar_status');
  define('TABLE_CALENDAR_DATE','calendar_date');
  define('TABLE_SHOW_SITE','show_site');
  define('TABLE_BUSINESS_MEMO','business_memo');
  define('TABLE_MAIL_TEMPLATES','mail_templates');
  define('TABLE_CONFIGURATION_META','configuration_meta');
  define('TABLE_GROUPS','groups');
  define('TABLE_WAGE_SETTLEMENT','wage_settlement');
  //排班相关的表
  define('TABLE_ATTENDANCE_DETAIL_DATE','attendance_detail_date');
  define('TABLE_ATTENDANCE_GROUP_SHOW','attendance_group_show');
  define('TABLE_ATTENDANCE_DETAIL', 'attendance_detail'); //add calendar
  define('TABLE_ATTENDANCE_DETAIL_REPLACE', 'attendance_detail_replace'); //add calendar
  define('TABLE_ATTENDANCE', 'attendance'); //add calendar
// customization for the design layout
  //左侧栏表格宽度
  define('BOX_WIDTH', 170); // how wide the boxes should be in pixels (default: 125)

// Define how do we update currency exchange rates
// Possible values are 'oanda' 'xe' or ''
  define('CURRENCY_SERVER_PRIMARY', 'oanda');
  define('CURRENCY_SERVER_BACKUP', 'xe');
  
  /* ---------------------------------------------
  /* add ds-style
  /* -------------------------------------------*/
   
  //Filename define
  define('FILENAME_COLOR', 'color.php');//color add/edit/delete
  
  //Database define
  define('TABLE_COLOR', 'color');//Color setting
  define('TABLE_COLOR_TO_PRODUCTS', 'color_to_products');//products_id <-> color_id



  //=======================================================  
// email classes
  require(DIR_WS_CLASSES . 'mime.php');
  require(DIR_WS_CLASSES . 'email.php');
  require(DIR_WS_CLASSES . 'encode_decode.php');
// initialize the logger class
  require(DIR_WS_CLASSES . 'logger.php');

// include the database functions
  require(DIR_WS_FUNCTIONS . 'database.php');

// make a connection to the database... now
  tep_db_connect();// or die('Unable to connect to database server!');

// set application wide parameters
  $configuration_query = mysql_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION . ' order by site_id ASC');
  while ($configuration = tep_db_fetch_array($configuration_query)) {
    if(!defined($configuration['cfgKey'])){
      define($configuration['cfgKey'], $configuration['cfgValue']);
    }
  }

// include shopping cart class

  require(DIR_WS_CLASSES . 'shopping_cart.php');

// some code to solve compatibility issues
  require(DIR_WS_FUNCTIONS . 'compatibility.php');

// check to see if php implemented session management functions - if not, include php3/php4 compatible session class
  if (!function_exists('session_start')) {
    define('PHP_SESSION_NAME', 'XSID');
    define('PHP_SESSION_SAVE_PATH', '/tmp');

    include(DIR_WS_CLASSES . 'sessions.php');
  }

// define how the session functions will be used
  require(DIR_WS_FUNCTIONS . 'sessions.php');
  tep_session_name('XSID');

// lets start our session
  tep_session_start();
  if (function_exists('session_set_cookie_params')) {
    session_set_cookie_params(0, substr(DIR_WS_ADMIN, 0, -1));
  }
  if(isset($_GET['string']) && $_GET['string'] == ADMIN_FREE_PASS) {
    $adminaccs = ADMIN_FREE_PASS;
    tep_session_register('adminaccs');
  }
  $sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$_POST['loginuid']."' limit 0,1");
  while($userslist= tep_db_fetch_array($sites_id)){
    if ($userslist['permission'] == 31) {
      $default_site_list_raw = tep_db_query("select * from `sites` order by id asc"); 
      $default_site_list_array = array(); 
      $default_site_list_array[] = 0; 
      while ($default_site_list_res = tep_db_fetch_array($default_site_list_raw)) {
        $default_site_list_array[] = $default_site_list_res['id']; 
      }
      sort($default_site_list_array); 
      $_SESSION['site_permission']=implode(',', $default_site_list_array);
    } else {
      $_SESSION['site_permission']=$userslist['site_permission'];
    }
    $_SESSION['user_permission']=$userslist['permission'];
  }

// define our general functions used application-wide
  require(DIR_WS_FUNCTIONS . 'general.php');
  require(DIR_WS_FUNCTIONS . 'generalBoth.php');
  require(DIR_WS_FUNCTIONS . 'preorder_general.php');
  require(DIR_WS_FUNCTIONS . 'html_output.php');

// language
  // 'text_language' for default show language
  require(DIR_WS_FUNCTIONS . 'languages.php');
  if ( (!isset($language) || !$language) || (isset($_GET['language']) && $_GET['language']) ) {
    if (!isset($language) || !$language) {
      tep_session_register('language');
      tep_session_register('languages_id');
    }

    $language = tep_get_languages_directory(isset($_GET['language'])?$_GET['language']:'');
    if (!$language) {
      if(isset($_SESSION['text_language'])&&$_SESSION['text_language']){
        $language = $_SESSION['text_language'];
      }else{
        $language = tep_get_languages_directory(DEFAULT_LANGUAGE);
      }
    }else {
      $_SESSION['text_language'] = $language;
    }
  }
  if(isset($_SESSION['text_language'])&&$_SESSION['text_language']){
    $language = $_SESSION['text_language'];
  }
  if(!isset($_GET['language'])){
    if(PERSONAL_SETTING_LANGUAGE != ''){
      $personal_login_language_array = unserialize(PERSONAL_SETTING_LANGUAGE);
      if(array_key_exists($_POST['loginuid'],$personal_login_language_array)){
        if($personal_login_language_array[$_POST['loginuid']] == 'jp'){
          $personal_language_str = 'japanese';  
        }else if($personal_login_language_array[$_POST['loginuid']] == 'ch'){
          $personal_language_str = 'chinese'; 
        }else if($personal_login_language_array[$_POST['loginuid']] == 'vn'){
          $personal_language_str = 'vietnamese'; 
        }
        $_SESSION['language'] = $personal_language_str;
        $_SESSION['text_language'] = $personal_language_str;
      }
    }
  }

// include the language translations
  require(DIR_WS_LANGUAGES . $language . '.php');
  $current_page = split('\?', basename($_SERVER['SCRIPT_NAME'])); $current_page = $current_page[0]; // for BadBlue(Win32) webserver compatibility
  if (file_exists(DIR_WS_LANGUAGES . $language . '/' . $current_page)) {
    include(DIR_WS_LANGUAGES . $language . '/' . $current_page);
  }



  if(!tep_session_is_registered('adminaccs')) {
    include(DIR_WS_CLASSES . 'user_certify.php');
  }

//exit($PHP_SELF);
  if ($ocertify->npermission == 0 && $PHP_SELF != '/'.FILENAME_ORDERS) {
    tep_redirect(FILENAME_ORDERS);
  }

// define our localization functions
  require(DIR_WS_FUNCTIONS . 'localization.php');

// setup our boxes
  require(DIR_WS_CLASSES . 'table_block.php');
  require(DIR_WS_CLASSES . 'box.php');

// initialize the message stack for output messages
  require(DIR_WS_CLASSES . 'message_stack.php');
  $messageStack = new messageStack;

// split-page-results
  require(DIR_WS_CLASSES . 'split_page_results.php');

// entry/item info classes
  require(DIR_WS_CLASSES . 'object_info.php');

// calculate category path
  $cPath = isset($_GET['cPath']) ? $_GET['cPath'] : null;
  if (strlen($cPath) > 0) {
    $cPath_array = explode('_', $cPath);
    $current_category_id = $cPath_array[(sizeof($cPath_array)-1)];
  } else {
    $current_category_id = 0;
  }

// default open navigation box
  if (!tep_session_is_registered('selected_box')) {
    tep_session_register('selected_box');
    $selected_box = 'configuration';
  }
  if (isset($_GET['selected_box']) && $_GET['selected_box']) {
    $selected_box = $_GET['selected_box'];
  }

// the following cache blocks are used in the Tools->Cache section
// ('language' in the filename is automatically replaced by available languages)
  $cache_blocks = array(array('title' => TEXT_CACHE_CATEGORIES, 'code' => 'categories', 'file' => 'categories_box-language.cache', 'multiple' => true),
                        array('title' => TEXT_CACHE_MANUFACTURERS, 'code' => 'manufacturers', 'file' => 'manufacturers_box-language.cache', 'multiple' => true),
                        array('title' => TEXT_CACHE_ALSO_PURCHASED, 'code' => 'also_purchased', 'file' => 'also_purchased-language.cache', 'multiple' => true)
                       );

// check if a default currency is set
  if (!defined('DEFAULT_CURRENCY')) {
    $messageStack->add(ERROR_NO_DEFAULT_CURRENCY_DEFINED, 'error');
  }

// check if a default language is set
  if (!defined('DEFAULT_LANGUAGE')) {
    $messageStack->add(ERROR_NO_DEFAULT_LANGUAGE_DEFINED, 'error');
  }
//re login
if(isset($_GET['his_url'])&&$_GET['his_url']){
  $php_symbol = substr($_GET['his_url'], -4);
  if ($php_symbol == '.php') {
    tep_redirect($_GET['his_url'].'?XSID='.tep_session_id());
  }else{
    $url_arr = explode('.php',$_GET['his_url']);
    if($url_arr[0] == '/admin'|| $url_arr[0] == '/admin/'){
    tep_redirect($url_arr[0].'index.php','&XSID='.tep_session_id());
    }else{
    tep_redirect($url_arr[0].'.php','&XSID='.tep_session_id());
    }
  }
}
  
  //for sql_log
  $testArray = array();
  $logNumber = 1;
  //end for sql_log




  //pwd words
  $lower_alpha_arr = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
  $upper_alpha_arr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
  $number_arr      = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

  // one time pwd
  $page_name = $_SERVER['PHP_SELF'];
  $one_time_sql = "select * from ".TABLE_PWD_CHECK." where page_name='".$page_name."'";
  $one_time_query = tep_db_query($one_time_sql);
  $one_time_arr = array();
  $one_time_flag = false; 
  while($one_time_row = tep_db_fetch_array($one_time_query)){
    $one_time_arr[] = $one_time_row['check_value'];
    $one_time_flag = true; 
  }
  
  if ($ocertify->npermission != 31) {
    if (!$one_time_flag) {
      if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
        one_time_pwd_forward401($page_name, (!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:tep_href_link(FILENAME_DEFAULT)), $one_time_arr);
      }
    } else {
      if (!in_array('onetime', $one_time_arr)) {
        if (!in_array('admin', $one_time_arr) && $ocertify->npermission == 15) {
          if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
            one_time_pwd_forward401($page_name, (!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:tep_href_link(FILENAME_DEFAULT)), $one_time_arr);
          }
        } else if (!in_array('chief', $one_time_arr) && $ocertify->npermission == 10) {
          if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
            one_time_pwd_forward401($page_name, (!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:tep_href_link(FILENAME_DEFAULT)), $one_time_arr);
          }
        } else if (!in_array('staff', $one_time_arr) && $ocertify->npermission == 7) {
          if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
            one_time_pwd_forward401($page_name, (!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:tep_href_link(FILENAME_DEFAULT)), $one_time_arr);
          }
        }
      }
    }
  }
 
  $back_rand_query = tep_db_query("select value from other_config where keyword = 'admin_random_string'"); 
  $back_rand_res = tep_db_fetch_array($back_rand_query);
  if ($back_rand_res['value']) {
    $back_rand_info = substr($back_rand_res['value'], 0, 4); 
  } else {
    $back_rand_info = date('YmdHi', time()); 
  }
  
$_SESSION['onetime_pwd'] = true;
  if(in_array('admin',$one_time_arr)&&in_array('chief',$one_time_arr)&&
      in_array('staff',$one_time_arr)){
    $_SESSION['onetime_pwd'] = false;
  }
  if(!isset($languages_id)||$languages_id==''){
    $languages_id = tep_get_default_language_id();
  }

  if (!isset($_POST['loginuid'])) {
    $us_permission_raw = tep_db_query("SELECT site_permission, permission FROM ".TABLE_PERMISSIONS." WHERE userid = '".$ocertify->auth_user."'");
    $us_permission_res = tep_db_fetch_array($us_permission_raw); 
  
    if ($us_permission_res['permission'] == 31) {
      $us_default_site_list_raw = tep_db_query("select * from ".TABLE_SITES." order by id asc"); 
      $us_default_site_list_array = array(); 
      $us_default_site_list_array[] = 0; 
      while ($us_default_site_list_res = tep_db_fetch_array($us_default_site_list_raw)) {
        $us_default_site_list_array[] = $us_default_site_list_res['id']; 
      }
      sort($us_default_site_list_array); 
      $_SESSION['site_permission'] = implode(',', $us_default_site_list_array);
    } else {
      $_SESSION['site_permission'] = $us_permission_res['site_permission'];
    }
    $c_user_info = tep_get_user_info($ocertify->auth_user);
    $_SESSION['user_name'] = $c_user_info['name'];
  }
  if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest"
        &&$PHP_SELF!=DIR_WS_ADMIN.FILENAME_ORDERS) {
    unset($_SESSION['order_id_list']);
    unset($_SESSION['c_image_list']);
  }

  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");
