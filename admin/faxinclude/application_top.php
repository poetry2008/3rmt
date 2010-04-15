<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
/**
 * change $_GET ,$_POST to $HTTP_GET_VAR;$HTTP_POST_VAR
 */

// edit by bobhero start 
//session_start();
$GLOBALS['HTTP_GET_VARS']=$_GET;
$GLOBALS['HTTP_POST_VARS']=$_POST;
//$HTTP_SERVER_VARS= $_SERVER;
// edit by bobhero end
setlocale (LC_ALL, 'ja_JP.eucJP');
// Start the clock for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

// Set the level of error reporting
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
  //error_reporting(E_ALL & ~E_NOTICE);

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
  define('FILENAME_IMAGE_DOCUMENTS', 'image_documents.php');
  define('FILENAME_TAGS', 'tags.php');
  define('FILENAME_BACKUP', 'backup.php');
  define('FILENAME_BANNER_MANAGER', 'banner_manager.php');
  define('FILENAME_BANNER_STATISTICS', 'banner_statistics.php');
  define('FILENAME_CACHE', 'cache.php');
  define('FILENAME_CATALOG_ACCOUNT_HISTORY_INFO', 'account_history_info.php');
  define('FILENAME_CATEGORIES', 'categories.php');
  define('FILENAME_CONFIGURATION', 'configuration.php');
  define('FILENAME_COUNTRIES', 'countries.php');
  define('FILENAME_CURRENCIES', 'currencies.php');
  define('FILENAME_CUSTOMERS', 'customers.php');
  define('FILENAME_DEFAULT', 'index.php');
  define('FILENAME_DEFINE_LANGUAGE', 'define_language.php');
  define('FILENAME_FILE_MANAGER', 'file_manager.php');
  define('FILENAME_GEO_ZONES', 'geo_zones.php');
  define('FILENAME_LANGUAGES', 'languages.php');
  define('FILENAME_MAIL', 'mail.php');
  define('FILENAME_MANUFACTURERS', 'manufacturers.php');
  define('FILENAME_MODULES', 'modules.php');
  define('FILENAME_NEWSLETTERS', 'newsletters.php');
  define('FILENAME_ORDERS', 'orders.php');
  define('FILENAME_ORDERS_EDIT', 'edit_orders.php');//ÄÉ²Ã-order_editer 2005.10.20
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
  
  //add files
  define('FILENAME_CONTENTS', 'contents.php');//Add filenames
  define('FILENAME_LATEST_NEWS', 'latest_news.php'); //Add latest_news
  define('FILENAME_PRODUCTS_UP', 'products_up.php'); //Add products_up
  define('FILENAME_PRODUCTS_DL', 'products_dl.php'); //Add products_dl
  define('FILENAME_STATS_SALES_REPORT2', 'stats_sales_report2.php');// sales report
  define('FILENAME_CL', 'cl.php');
  define('FILENAME_OPTIONS_UP', 'options_up.php');
  define('FILENAME_OPTIONS_DL', 'options_dl.php');


// define the database table names used in the project
  define('TABLE_IMAGE_DOCUMENTS', 'iimy_image_documents');
  define('TABLE_IMAGE_DOCUMENT_TYPES', 'iimy_image_document_types');
  define('TABLE_PRODUCTS_TO_IMAGE_DOCUMENTS', 'iimy_products_to_image_documents');
  define('TABLE_TAGS', 'tags');
  define('TABLE_PRODUCTS_TO_TAGS', 'products_to_tags');
  define('TABLE_ADDRESS_BOOK', 'iimy_address_book');
  define('TABLE_ADDRESS_FORMAT', 'iimy_address_format');
  define('TABLE_BANNERS', 'iimy_banners');
  define('TABLE_BANNERS_HISTORY', 'iimy_banners_history');
  define('TABLE_CATEGORIES', 'categories');
  define('TABLE_CATEGORIES_DESCRIPTION', 'categories_description');
  define('TABLE_CONFIGURATION', 'iimy_configuration');
  define('TABLE_CONFIGURATION_GROUP', 'iimy_configuration_group');
  define('TABLE_COUNTRIES', 'iimy_countries');
  define('TABLE_CURRENCIES', 'iimy_currencies');
  define('TABLE_CUSTOMERS', 'iimy_customers');
  define('TABLE_CUSTOMERS_BASKET', 'iimy_customers_basket');
  define('TABLE_CUSTOMERS_BASKET_ATTRIBUTES', 'iimy_customers_basket_attributes');
  define('TABLE_CUSTOMERS_INFO', 'iimy_customers_info');
  define('TABLE_LANGUAGES', 'iimy_languages');
  define('TABLE_MANUFACTURERS', 'manufacturers');
  define('TABLE_MANUFACTURERS_INFO', 'manufacturers_info');
  define('TABLE_NEWSLETTERS', 'iimy_newsletters');
  define('TABLE_ORDERS', 'iimy_orders');
  define('TABLE_ORDERS_PRODUCTS', 'iimy_orders_products');
  define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', 'iimy_orders_products_attributes');
  define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', 'iimy_orders_products_download');
  define('TABLE_ORDERS_STATUS', 'iimy_orders_status');
  define('TABLE_ORDERS_STATUS_HISTORY', 'iimy_orders_status_history');
  define('TABLE_ORDERS_TOTAL', 'iimy_orders_total');
  define('TABLE_PRODUCTS', 'products');
  define('TABLE_PRODUCTS_ATTRIBUTES', 'products_attributes');
  define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', 'products_attributes_download');
  define('TABLE_PRODUCTS_DESCRIPTION', 'products_description');
  define('TABLE_PRODUCTS_NOTIFICATIONS', 'products_notifications');
  define('TABLE_PRODUCTS_OPTIONS', 'products_options');
  define('TABLE_PRODUCTS_OPTIONS_VALUES', 'products_options_values');
  define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', 'products_options_values_to_products_options');
  define('TABLE_PRODUCTS_TO_CATEGORIES', 'products_to_categories');
  define('TABLE_REVIEWS', 'iimy_reviews');
  define('TABLE_REVIEWS_DESCRIPTION', 'iimy_reviews_description');
  define('TABLE_SESSIONS', 'iimy_sessions');
  define('TABLE_SPECIALS', 'specials');
  define('TABLE_TAX_CLASS', 'iimy_tax_class');
  define('TABLE_TAX_RATES', 'iimy_tax_rates');
  define('TABLE_GEO_ZONES', 'iimy_geo_zones');
  define('TABLE_ZONES_TO_GEO_ZONES', 'iimy_zones_to_geo_zones');
  define('TABLE_WHOS_ONLINE', 'iimy_whos_online');
  define('TABLE_ZONES', 'iimy_zones');
  define('TABLE_CALENDAR', 'iimy_calendar'); //add calendar
  //Add DB - ds-style
  define('TABLE_INFORMATION_PAGE', 'iimy_information_page');//information box
  define('TABLE_LATEST_NEWS', 'iimy_latest_news'); //latest_news
  
  define('TABLE_PRESENT_GOODS', 'iimy_present_goods');
  define('TABLE_PRESENT_APPLICANT', 'iimy_present_applicant');
  define('TABLE_PRESENT_APPLICANT', 'iimy_present_applicant');
  define('TABLE_MAIL_MAGAZINE', 'iimy_mail_magazine');
  define('TABLE_ORDERS_MAIL', 'iimy_orders_mail');
  define('TABLE_CALENDER', 'iimy_calendar');

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
  define('TABLE_COLOR', 'iimy_color');//Color setting
  define('TABLE_COLOR_TO_PRODUCTS', 'iimy_color_to_products');//products_id <-> color_id

  define('TABLE_CONFIGURATION_DS', 'iimy_configuration_ds');
  define('TABLE_CONFIGURATION_DS_GROUP', 'iimy_configuration_ds_group');
  define('FILENAME_CONFIGURATION_DS', 'configuration_ds.php');
  
    
  //Language define
  define('BOX_CATALOG_COLORS', '¾¦ÉÊ¥«¥é¡¼ÅÐÏ¿');
  //=======================================================  

// initialize the logger class
  require(DIR_WS_CLASSES . 'logger.php');

// include the database functions
  require(DIR_WS_FUNCTIONS . 'database.php');

// make a connection to the database... now
  tep_db_connect() or die('Unable to connect to database server!');

// set application wide parameters
  $configuration_query = tep_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION . '');
  while ($configuration = tep_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
  }

// set application wide parameters
  $configuration_query = tep_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION_DS);
  while ($configuration = tep_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
  }

// include shopping cart class
  require(DIR_WS_CLASSES . 'shopping_cart.php');

// some code to solve compatibility issues
  require(DIR_WS_FUNCTIONS . 'compatibility.php');

// check to see if php implemented session management functions - if not, include php3/php4 compatible session class
  if (!function_exists('session_start')) {
    define('PHP_SESSION_NAME', 'sID');
    define('PHP_SESSION_SAVE_PATH', '/tmp');

    include(DIR_WS_CLASSES . 'sessions.php');
  }

// define how the session functions will be used
  require(DIR_WS_FUNCTIONS . 'sessions.php');
  tep_session_name('osCAdminID');

// lets start our session
  tep_session_start();
  if (function_exists('session_set_cookie_params')) {
    session_set_cookie_params(0, substr(DIR_WS_ADMIN, 0, -1));
  }
  if($HTTP_GET_VARS['string'] == ADMIN_FREE_PASS) {
    $adminaccs = ADMIN_FREE_PASS;
	tep_session_register('adminaccs');
  }

// language
  require(DIR_WS_FUNCTIONS . 'languages.php');
  if ( (!$language) || ($HTTP_GET_VARS['language']) ) {
    if (!$language) {
      tep_session_register('language');
      tep_session_register('languages_id');
    }

    $language = tep_get_languages_directory($HTTP_GET_VARS['language']);
    if (!$language) $language = tep_get_languages_directory(DEFAULT_LANGUAGE);
  }

// include the language translations
  require(DIR_WS_LANGUAGES . $language . '.php');
  $current_page = split('\?', basename($PHP_SELF)); $current_page = $current_page[0]; // for BadBlue(Win32) webserver compatibility
  if (file_exists(DIR_WS_LANGUAGES . $language . '/' . $current_page)) {
    include(DIR_WS_LANGUAGES . $language . '/' . $current_page);
  }

// define our general functions used application-wide
  require(DIR_WS_FUNCTIONS . 'general.php');
  require(DIR_WS_FUNCTIONS . 'html_output.php');

  require('faxinclude/functions/admin_functions.php');

// define our authenticate functions 2003/04/16
  //var_dump(!tep_session_is_registered('adminaccs'));
  if(!tep_session_is_registered('adminaccs')) {
    include('faxinclude/classes/' . 'user_certify.php');
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
  require('faxinclude/classes/split_page_results.php');

// entry/item info classes
  require(DIR_WS_CLASSES . 'object_info.php');

// email classes
  require(DIR_WS_CLASSES . 'mime.php');
  require(DIR_WS_CLASSES . 'email.php');

// calculate category path
  $cPath = $HTTP_GET_VARS['cPath'];
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
  if ($HTTP_GET_VARS['selected_box']) {
    $selected_box = $HTTP_GET_VARS['selected_box'];
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
// Include OSC-AFFILIATE
  require('includes/affiliate_application_top.php');
  //for sql_log
  $testArray = array();
  $logNumber = 1;
  //end for sql_log
?>
