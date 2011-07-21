<?php
/*
  $Id$
*/
$GLOBALS['HTTP_GET_VARS']  = $_GET;
$GLOBALS['HTTP_POST_VARS'] = $_POST;

  setlocale (LC_ALL, 'ja_JP.UTF-8');
// Set default timezone

  if(function_exists('date_default_timezone_set')) date_default_timezone_set('Asia/Tokyo');


// Start the clock for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

// Set the level of error reporting
  //error_reporting(E_ALL & ~E_NOTICE);
  error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
  ini_set("display_errors", "On");

// Check if register_globals is enabled.
// Since this is a temporary measure this message is hardcoded. The requirement will be removed before 2.2 is finalized.
  if (function_exists('ini_get')) {
    ini_get('register_globals') or exit('FATAL ERROR: register_globals is disabled in php.ini, please enable it!');
  }

// Disable use_trans_sid as tep_href_link() does this manually
  if (function_exists('ini_set')) {
    //edit by bobhero start  add @ at ini_set
    @ini_set('session.use_trans_sid', 0);
    //ini_set('session.use_trans_sid', 0);
    //edit by bobhero end 
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
  define('FILENAME_POINT_EMAIL', 'point_email.php');
  define('FILENAME_OA_FORM', 'oa_form.php'); 
  define('FILENAME_OA_GROUP', 'oa_group.php'); 
  define('FILENAME_OA_LINK_GROUP', 'oa_link_group.php'); 
  define('FILENAME_OA_ITEM', 'oa_item.php'); 
  define('FILENAME_PWD_LOG', 'pwd_log.php');
  define('FILENAME_PRODUCTS_PRICE','products_price.php');
  define('FILENAME_PW_MANAGER', 'pw_manager.php');
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
  define('FILENAME_ORDERS_EDIT', 'edit_orders.php');//追加-order_editer 2005.10.20
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
  define('FILENAME_COMPUTERS', 'computers.php');
  
  //add files
  define('FILENAME_BANK_CL', 'bank_cl.php');
  define('FILENAME_CONTENTS', 'contents.php');
  define('FILENAME_LATEST_NEWS', 'latest_news.php'); 
  define('FILENAME_PRODUCTS_UP', 'products_up.php'); 
  define('FILENAME_PRODUCTS_DL', 'products_dl.php'); 
  define('FILENAME_STATS_SALES_REPORT2', 'stats_sales_report2.php');
  define('FILENAME_OPTIONS_UP', 'options_up.php');
  define('FILENAME_OPTIONS_DL', 'options_dl.php');
  define('FILENAME_NEW_CUSTOMERS', 'new_customers.php');
  define('FILENAME_BILL_TEMPLATES', 'bill_templates.php');


// define the database table names used in the project
  define('TABLE_PWD_CHECK', 'pwd_check');
  define('TABLE_FAQ_CATEGORIES', 'faq_categories');
  define('TABLE_FAQ_QUESTION',  'faq_question');
  define('TABLE_FAQ_CATEGORIES_DESCRIPTION', 'faq_categories_description');
  define('TABLE_FAQ_QUESTION_DESCRIPTION',  'faq_question_description');
  define('TABLE_FAQ_QUESTION_TO_CATEGORIES','faq_question_to_categories');
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
  define('TABLE_LATEST_NEWS', 'latest_news'); //latest_news
define('TABLE_PERMISSIONS','permissions');


  define('TABLE_PRESENT_GOODS', 'present_goods');
  define('TABLE_PRESENT_APPLICANT', 'present_applicant');
  define('TABLE_MAIL_MAGAZINE', 'mail_magazine');
  define('TABLE_ORDERS_MAIL', 'orders_mail');
  define('TABLE_CALENDER', 'calendar');
  define('TABLE_COMPUTERS',  'computers');
  define('TABLE_ORDERS_TO_COMPUTERS',  'orders_to_computers');
  define('TABLE_BILL_TEMPLATES',  'bill_templates');
  
// customization for the design layout
  define('BOX_WIDTH', 125); // how wide the boxes should be in pixels (default: 125)

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

// initialize the logger class
  require(DIR_WS_CLASSES . 'logger.php');

// include the database functions
  require(DIR_WS_FUNCTIONS . 'database.php');

// make a connection to the database... now
  tep_db_connect() or die('Unable to connect to database server!');

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
  $_SESSION['site_permission']=$userslist['site_permission'];
  $_SESSION['user_permission']=$userslist['permission'];
}


// language
  require(DIR_WS_FUNCTIONS . 'languages.php');
  if ( (!isset($language) || !$language) || (isset($_GET['language']) && $_GET['language']) ) {
    if (!isset($language) || !$language) {
      tep_session_register('language');
      tep_session_register('languages_id');
    }

    $language = tep_get_languages_directory(isset($_GET['language'])?$_GET['language']:'');
    if (!$language) $language = tep_get_languages_directory(DEFAULT_LANGUAGE);
  }

// include the language translations
  require(DIR_WS_LANGUAGES . $language . '.php');
  $current_page = split('\?', basename($_SERVER['SCRIPT_NAME'])); $current_page = $current_page[0]; // for BadBlue(Win32) webserver compatibility
  if (file_exists(DIR_WS_LANGUAGES . $language . '/' . $current_page)) {
    include(DIR_WS_LANGUAGES . $language . '/' . $current_page);
  }

// define our general functions used application-wide
  require(DIR_WS_FUNCTIONS . 'general.php');
  require(DIR_WS_FUNCTIONS . 'html_output.php');


// define our authenticate functions 2003/04/16
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

// email classes
  require(DIR_WS_CLASSES . 'mime.php');
  require(DIR_WS_CLASSES . 'email.php');

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
  $one_time_sql = "select * from ".TABLE_PWD_CHECK;
  $one_time_query = tep_db_query($one_time_sql);
  $one_time_arr = array();
  while($one_time_row = tep_db_fetch_array($one_time_query)){
    $one_time_arr[] = $one_time_row['check_value'];
  }
  if(count($one_time_arr)==1&&$one_time_arr[0]=='admin'&&$_SESSION['user_permission']!=15){
    forward401();
  }
  $_SESSION['onetime_pwd'] = true;
  if(in_array('admin',$one_time_arr)&&in_array('chief',$one_time_arr)&&
      in_array('staff',$one_time_arr)){
    $_SESSION['onetime_pwd'] = false;
  }



  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  # 永远是改动过的
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");
