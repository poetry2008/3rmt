<?php
/*
  $Id$
*/

  ini_set("display_errors", "Off");
  error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
// ddos start 
require(DIR_WS_FUNCTIONS . 'dos.php');
// ip 
$source_ip = $_SERVER['REMOTE_ADDR'];
// host
$source_host = $_SERVER['HTTP_HOST'];
// check if sessions are supported, otherwise use the php3 compatible session class
  if (!function_exists('session_start')) {
    if((defined('SID_SYMBOL')) && SID_SYMBOL){
      define('PHP_SESSION_NAME', 'sid');
    } else {
      define('PHP_SESSION_NAME', 'cmd');
    }
    
    define('PHP_SESSION_SAVE_PATH', '/tmp/'); 
    include(DIR_WS_CLASSES . 'sessions.php');
  }

// define how the session functions will be used
  require(DIR_WS_FUNCTIONS . 'sessions.php');
  //tep_session_name('SID');
  
  if((defined('SID_SYMBOL')) && SID_SYMBOL){
    tep_session_name('sid');
  } else {
    tep_session_name('cmd');
  }
  tep_session_save_path('/tmp/');


  tep_session_start();
  $old_sid = tep_session_id();
  session_write_close();

  $today = date("Ymd",time());
  tep_session_id('sessbanlist'.$today);

  tep_session_start();
// 使用SESSION 判断IP 是否被封
  if(in_array($source_ip,$_SESSION['banlist_ip'])&&
       is_reset_session_blocked_ip($source_ip)){
    session_write_close();
    tep_session_id($old_sid);
    tep_session_start();
    header("Cache-Control:");
    header("Pragma:");
    header("Expires:".date("D, d M Y H:i:s",0)." GMT");
    header('http/1.1 503 Service Unavailable');
    require(DIR_FS_DOCUMENT_ROOT.'error/503-service-unavailable.html');
    exit;
  }

// config time 
$unit_time = 3;
// confi total
$unit_total = 15;

// config time 
$unit_min_time = 1;
// confi total
$unit_min_total = 120;

// config time 
$unit_hour_time = 1;
// confi total
$unit_hour_total = 600;


// connect db
$dsn = 'mysql:host='.DB_SERVER.';dbname='.DB_DATABASE;
$pdo_con = new PDO($dsn, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);

if ($pdo_con) {
  if(is_reset_blocked_ip($pdo_con, $source_ip)){
    // go to 503
    save_block_ip($pdo_con);
    session_write_close();
    tep_session_id($old_sid);
    tep_session_start();
    $pdo_con = null;
    header("Cache-Control:");
    header("Pragma:");
    header("Expires:".date("D, d M Y H:i:s",0)." GMT");
    header('http/1.1 503 Service Unavailable');
    require(DIR_FS_DOCUMENT_ROOT.'error/503-service-unavailable.html');
    exit;
  } else {
    // write ip to accresslog 
    write_vlog($pdo_con, $source_ip, $source_host);    
    if (is_large_visit($pdo_con, $source_ip, $unit_time, $unit_total)) {
      // write ip to banlist prebanlist
      analyze_ban_log($pdo_con, $source_ip);
      // go to 503
      save_block_ip($pdo_con);
      session_write_close();
      tep_session_id($old_sid);
      tep_session_start();
      $pdo_con = null;
      header("Cache-Control:");
      header("Pragma:");
      header("Expires:".date("D, d M Y H:i:s",0)." GMT");
      header('http/1.1 503 Service Unavailable');
      require(DIR_FS_DOCUMENT_ROOT.'error/503-service-unavailable.html');
      exit;
    }
   //minite
    if (is_large_visit($pdo_con, $source_ip, $unit_min_time, $unit_min_total,'i')) {
      // write ip to banlist prebanlist
      analyze_ban_log($pdo_con, $source_ip);
      // go to 503
      save_block_ip($pdo_con);
      session_write_close();
      tep_session_id($old_sid);
      tep_session_start();
      $pdo_con = null;
      header("Cache-Control:");
      header("Pragma:");
      header("Expires:".date("D, d M Y H:i:s",0)." GMT");
      header('http/1.1 503 Service Unavailable');
      require(DIR_FS_DOCUMENT_ROOT.'error/503-service-unavailable.html');
      exit;
    }
    //hour
    if (is_large_visit($pdo_con, $source_ip, $unit_hour_time, $unit_hour_total,'h')) {
      // write ip to banlist prebanlist
      analyze_ban_log($pdo_con, $source_ip);
      // go to 503
      save_block_ip($pdo_con);
      session_write_close();
      tep_session_id($old_sid);
      tep_session_start();
      $pdo_con = null;
      header("Cache-Control:");
      header("Pragma:");
      header("Expires:".date("D, d M Y H:i:s",0)." GMT");
      header('http/1.1 503 Service Unavailable');
      require(DIR_FS_DOCUMENT_ROOT.'error/503-service-unavailable.html');
      exit;
    }
  }
}

  session_write_close();
// ddos end 

  $GLOBALS['HTTP_GET_VARS']    = $_GET;
  $GLOBALS['HTTP_POST_VARS']   = $_POST;
  $GLOBALS['HTTP_SERVER_VARS'] = $_SERVER;

//Japan location
  setlocale (LC_ALL, 'ja_JP.UTF-8');

// start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());


// check if register_globals is enabled.
// since this is a temporary measure this message is hardcoded. The requirement will be removed before 2.2 is finalized.
  if (function_exists('ini_get')) {
    ini_get('register_globals') or exit('FATAL ERROR: register_globals is disabled in php.ini, please enable it!');
  }

// disable use_trans_sid as tep_href_link() does this manually
  if (function_exists('ini_set')) @ini_set('session.use_trans_sid', 0);


// Set lib path
  ini_set('include_path',ini_get('include_path').':'.DIR_FS_3RMTLIB);

// set the type of request (secure or not)
  $request_type = (getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

// set which precautions should be checked
  define('WARN_INSTALL_EXISTENCE', 'true');
  define('WARN_CONFIG_WRITEABLE', 'true');
  define('WARN_SESSION_DIRECTORY_NOT_WRITEABLE', 'true');
  define('WARN_SESSION_AUTO_START', 'true');
  define('WARN_DOWNLOAD_DIRECTORY_NOT_READABLE', 'true');

// define the filenames used in the project
  define('FILENAME_TELL_A_FRIEND_SUCCESS','tell_a_friend_success.php');
  define('FILENAME_OPEN','open.php');
  define('FILENAME_CREATE_INDEX', 'index.php');
  define('FILENAME_CHECKOUT_ATTRIBUTES', 'checkout_attributes.php'); 
  define('FILENAME_CHECKOUT_OPTION', 'checkout_option.php');
  define('FILENAME_PREORDER_PAYMENT', 'preorder_payment.php');
  define('FILENAME_PREORDER_SUCCESS', 'preorder_success.php');
  define('FILENAME_PREORDER_UNSUCCESS', 'preorder_unsuccess.php');
  define('FILENAME_FAQ_INFO', 'faq_info.php');
  define('FILENAME_ACCOUNT', 'account.php');
  define('FILENAME_ACCOUNT_EXIT', 'account_exit.php');
  define('FILENAME_TAGS', 'tags.php');
  define('FILENAME_SEND_MAIL', 'send_mail.php');
  define('FILENAME_SEND_SUCCESS', 'send_success.php');
  define('FILENAME_ACCOUNT_EDIT', 'account_edit.php');
  define('FILENAME_ACCOUNT_EDIT_PROCESS', 'account_edit_process.php');
  define('FILENAME_ACCOUNT_HISTORY', 'account_history.php');
  define('FILENAME_ACCOUNT_HISTORY_INFO', 'account_history_info.php');
  define('FILENAME_ADDRESS_BOOK', 'address_book.php');
  define('FILENAME_ADDRESS_BOOK_PROCESS', 'address_book_process.php');
  define('FILENAME_ADVANCED_SEARCH', 'advanced_search.php');
  define('FILENAME_ADVANCED_SEARCH_RESULT', 'advanced_search_result.php');
  
  define('FILENAME_AJAX', 'ajax.php');
  define('FILENAME_ALSO_PURCHASED_PRODUCTS', 'also_purchased_products.php'); // This is the bottom of product_info.php (found in modules)
  define('FILENAME_BROWSER_IE6X', 'browser_ie6x.php');
  define('FILENAME_COLOR_LISTING', 'color_listing.php');
  define('FILENAME_CHECKOUT_CONFIRMATION', 'checkout_confirmation.php');
  define('FILENAME_CHECKOUT_PAYMENT', 'checkout_payment.php');
  define('FILENAME_CHECKOUT_PAYMENT_ADDRESS', 'checkout_payment_address.php');
  define('FILENAME_CHECKOUT_PROCESS', 'checkout_process.php');
  define('FILENAME_CHECKOUT_SHIPPING', 'checkout_shipping.php');
  define('FILENAME_CHECKOUT_SHIPPING_ADDRESS', 'checkout_shipping_address.php');
  define('FILENAME_CHECKOUT_SUCCESS', 'checkout_success.php');
  define('FILENAME_CHECKOUT_UNSUCCESS', 'checkout_unsuccess.php');
  define('FILENAME_CHECKOUT_PRODUCTS', 'checkout_products.php');
  define('FILENAME_CONTACT_US', 'contact_us.php');
  define('FILENAME_CONDITIONS', 'conditions.php');
  define('FILENAME_CREATE_ACCOUNT', 'create_account.php');
  define('FILENAME_CREATE_ACCOUNT_PROCESS', 'create_account_process.php');
  define('FILENAME_CREATE_ACCOUNT_SUCCESS', 'create_account_success.php');
  define('FILENAME_DEFAULT', 'index.php');
  define('FILENAME_CATEGORY', FILENAME_DEFAULT);
  define('FILENAME_MANUFACTURER', FILENAME_DEFAULT);
  define('FILENAME_DOWNLOAD', 'download.php');
  define('FILENAME_FAQ', 'faq.php');
  define('FILENAME_GGSITEMAP', 'ggsitemap.php');
  define('FILENAME_INFO_SHOPPING_CART', 'info_shopping_cart.php');
  define('FILENAME_LOGIN', 'login.php');
  define('FILENAME_LOGOFF', 'logoff.php');
  define('FILENAME_NEWS', 'news.php');
  define('FILENAME_MAGAZINE', 'mail_magazine.php');
  define('FILENAME_MANUFACTURERS', 'manufacturers.php');
  define('FILENAME_NEW_PRODUCTS', 'new_products.php'); // This is the middle of default.php (found in modules)
  define('FILENAME_PASSWORD_FORGOTTEN', 'password_forgotten.php');
  define('FILENAME_POPUP_IMAGE', 'popup_image.php');
  define('FILENAME_POPUP_IMAGE_NEWS', 'popup_image_news.php');
  define('FILENAME_POPUP_SEARCH_HELP', 'popup_search_help.php');
  define('FILENAME_PRIVACY', 'privacy.php');
  define('FILENAME_PRODUCT_INFO', 'product_info.php');
  define('FILENAME_PRODUCT_NOTIFICATIONS', 'product_notifications.php');
  define('FILENAME_PRODUCT_REVIEWS', 'product_reviews.php');
  define('FILENAME_PRODUCT_REVIEWS_INFO', 'product_reviews_info.php');
  define('FILENAME_PRODUCT_REVIEWS_WRITE', 'product_reviews_write.php');
  define('FILENAME_PRODUCTS_NEW', 'products_new.php');
  define('FILENAME_PRODUCT_LISTING', 'product_listing.php');
  define('FILENAME_PRESENT','present.php');
  define('FILENAME_PRESENT_ORDER',        'present_order.php');
  define('FILENAME_PRESENT_POPUP_IMAGE',  'present_popup_image.php');
  define('FILENAME_PRESENT_CONFIRMATION', 'present_confirmation.php');
  define('FILENAME_PRESENT_SUCCESS',      'present_success.php');
  define('FILENAME_PDF_DATASHEET',        'pdf_datasheet.php');
  define('FILENAME_PAGE',                 'page.php');
  define('FILENAME_REORDER',  'reorder.php');
  define('FILENAME_REORDER2', 'reorder2.php');
  define('FILENAME_PREORDER', 'preorder.php');
  define('FILENAME_REDIRECT', 'redirect.php');
  define('FILENAME_REVIEWS', 'reviews.php');
  define('FILENAME_RSS', 'rss.php');
  define('FILENAME_SHIPPING', 'shipping.php');
  define('FILENAME_SITEMAP','sitemap.php');
  define('FILENAME_SHOPPING_CART', 'shopping_cart.php');
  define('FILENAME_SPECIALS', 'specials.php');
  define('FILENAME_TELL_A_FRIEND', 'tell_a_friend.php');
  define('FILENAME_UPCOMING_PRODUCTS', 'upcoming_products.php'); // This is the bottom of default.php (found in modules)
  define('FILENAME_EMAIL_TROUBLE', 'email_trouble.php');
// define the database table names used in the project
  define('TABLE_CUSTOMERS_EXIT_HISTORY', 'customers_exit_history');
  define('TABLE_OPTION_GROUP', 'option_group');
  define('TABLE_OPTION_ITEM', 'option_item');
  define('TABLE_CUSTOMERS_BASKET_OPTIONS', 'customers_basket_options');
  define('TABLE_OTHER_CONFIG', 'other_config'); 
  define('TABLE_CAMPAIGN', 'campaign'); 
  define('TABLE_CUSTOMER_TO_CAMPAIGN', 'customer_to_campaign'); 
  define('TABLE_PREORDERS_OA_FORMVALUE', 'preorders_oa_formvalue'); 
  define('TABLE_PREORDERS', 'preorders');
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
  define('TABLE_ORDERS_TO_BUTTONS', 'orders_to_buttons'); 
  define('TABLE_FAQ_CATEGORIES', 'faq_categories');
  define('TABLE_FAQ_QUESTION',  'faq_question');
  define('TABLE_FAQ_CATEGORIES_DESCRIPTION', 'faq_categories_description');
  define('TABLE_FAQ_QUESTION_DESCRIPTION',  'faq_question_description');
  define('TABLE_FAQ_QUESTION_TO_CATEGORIES','faq_question_to_categories');
  define('TABLE_ADDRESS_BOOK', 'address_book');
  define('TABLE_TAGS', 'tags');
  define('TABLE_PRODUCTS_TO_TAGS', 'products_to_tags');
  define('TABLE_MAIL_TEMPLATES', 'mail_templates');
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
  define('TABLE_USER_LOGIN','user_login');
  define('TABLE_LANGUAGES', 'languages');
  define('TABLE_MANUFACTURERS', 'manufacturers');
  define('TABLE_MANUFACTURERS_INFO', 'manufacturers_info');
  define('TABLE_ORDERS', 'orders');
  define('TABLE_ORDERS_PRODUCTS', 'orders_products');
  define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', 'orders_products_attributes');
  define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', 'orders_products_download');
  define('TABLE_ORDERS_STATUS', 'orders_status');
  define('TABLE_ORDERS_STATUS_HISTORY', 'orders_status_history');
  define('TABLE_ORDERS_TOTAL', 'orders_total');
  define('TABLE_PRODUCTS', 'products');
  define('TABLE_COUNTRY_FEE','country_fee');
  define('TABLE_COUNTRY_AREA','country_area');
  define('TABLE_COUNTRY_CITY','country_city');
  define('TABLE_PRODUCTS_ATTRIBUTES', 'products_attributes');
  define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', 'products_attributes_download');
  define('TABLE_PRODUCTS_DESCRIPTION', 'products_description');
  define('TABLE_PRODUCTS_NOTIFICATIONS', 'products_notifications');
  define('TABLE_PRODUCTS_OPTIONS', 'products_options');
  define('TABLE_PRODUCTS_OPTIONS_VALUES', 'products_options_values');
  define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', 'products_options_values_to_products_options');
  define('TABLE_PRODUCTS_TO_CATEGORIES', 'products_to_categories');
  define('TABLE_RECORD', 'record');
  define('TABLE_REVIEWS', 'reviews');
  define('TABLE_REVIEWS_DESCRIPTION', 'reviews_description');
  define('TABLE_SESSIONS', 'sessions');
  define('TABLE_SESSIONS_LOG', 'sessions_log');
  define('TABLE_SPECIALS', 'specials');
  define('TABLE_TAX_CLASS', 'tax_class');
  define('TABLE_TAX_RATES', 'tax_rates');
  define('TABLE_GEO_ZONES', 'geo_zones');
  define('TABLE_ZONES_TO_GEO_ZONES', 'zones_to_geo_zones');
  define('TABLE_WHOS_ONLINE', 'whos_online');
  define('TABLE_ZONES', 'zones');
  define('TABLE_CL', 'calendar'); //add calendar
  define('TABLE_CATEGORIES_RSS', 'categories_rss');
  define('TABLE_PRESENT_GOODS', 'present_goods');
  define('TABLE_PRESENT_APPLICANT', 'present_applicant');
  define('TABLE_MAIL_MAGAZINE', 'mail_magazine');
  define('TABLE_INFORMATION_PAGE', 'information_page');//Information box
  define('TABLE_NEWS', 'news'); //latest_news
  define('TABLE_COLOR', 'color');//Color setting
  define('TABLE_COLOR_TO_PRODUCTS', 'color_to_products');//products_id <-> color_id

// customization for the design layout
  define('BOX_WIDTH', 171); // how wide the boxes should be in pixels (default: 125)

// include the database functions
  require(DIR_WS_FUNCTIONS . 'database.php');

// make a connection to the database... now
  tep_db_connect() or die('Unable to connect to database server!');

// set the application parameters (can be modified through the administration tool)
  $configuration_query = mysql_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION . ' where site_id = ' . SITE_ID);
  while ($configuration = mysql_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
  }
// 将其它设置加入到本站，即主站的信息
  $configuration_query = mysql_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION . ' where site_id = 0' );
  while ($configuration = mysql_fetch_array($configuration_query)) {
      if (!defined($configuration['cfgKey'])) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
      }
  } 
// if gzip_compression is enabled, start to buffer the output
  if ( (GZIP_COMPRESSION == 'true') && ($ext_zlib_loaded = extension_loaded('zlib')) && (PHP_VERSION >= '4') ) {
    if (($ini_zlib_output_compression = (int)ini_get('zlib.output_compression')) < 1) {
      if (PHP_VERSION >= '4.0.4') {
        ob_end_clean();
        ob_start('ob_gzhandler');
      } else {
        include(DIR_WS_FUNCTIONS . 'gzip_compression.php');
        ob_start();
        ob_implicit_flush();
      }
    } else {
      ini_set('zlib.output_compression_level', GZIP_LEVEL);
    }
  }

// set the HTTP GET parameters manually if search_engine_friendly_urls is enabled
  if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
    if (strlen(getenv('PATH_INFO')) > 1) {
      $GET_arrays = array();
      $PHP_SELF = str_replace(getenv('PATH_INFO'), '', $_SERVER['PHP_SELF']);
      $vars = explode('/', substr(getenv('PATH_INFO'), 1));
      for ($i=0, $n=sizeof($vars); $i<$n; $i++) {
        if (strpos($vars[$i], '[]')) {
          $GET_arrays[substr($vars[$i], 0, -2)][] = $vars[$i+1];
        } else {
          $_GET[$vars[$i]] = $vars[$i+1];
        }
        $i++; 
      }

      if (sizeof($GET_arrays) > 0) {
        while (list($key, $value) = each($GET_arrays)) {
          $_GET[$key] = $value;
        }
      }
    }
  } else {
    $PHP_SELF = $_SERVER['PHP_SELF'];
  }

// include cache functions if enabled
  if (USE_CACHE == 'true') include(DIR_WS_FUNCTIONS . 'cache.php');

// include shopping cart class
  require(DIR_WS_CLASSES . 'shopping_cart.php');

// include navigation history class
  require(DIR_WS_CLASSES . 'navigation_history.php');

// some code to solve compatibility issues
  require(DIR_WS_FUNCTIONS . 'compatibility.php');

   //add new panduan 
   if (isset($_POST[tep_session_name()])) {
     tep_session_id($_POST[tep_session_name()]);
   } elseif ((SESSION_RECREATE == 'False') && (getenv('HTTPS') == 'on') && isset($_GET[tep_session_name()]) ) {
     tep_session_id($_GET[tep_session_name()]);
   } elseif (ENABLE_SSL == true && (SESSION_RECREATE == 'True') && isset($_GET[tep_session_name()])) {
     tep_session_id($_GET[tep_session_name()]);
   }else {
     tep_session_id($old_sid);
   }
  
  if (function_exists('session_set_cookie_params')) {
    if ($request_type == 'SSL'){
      session_set_cookie_params(0, '/', $_SERVER['HTTP_SERVER']);
    } else {
      session_set_cookie_params(0, '/');
    }
  }

  tep_session_start();
  //add variable new 
  $session_started = true;
  $SID = (defined('SID') ? SID : ''); 
// Create the cart & Fix the cart if necesary
  if (tep_session_is_registered('cart') && is_object($cart)) {
    if (PHP_VERSION < 4) {
      $broken_cart = $cart;
      $cart = new shoppingCart;
      $cart->unserialize($broken_cart);
    }
  } else {
    tep_session_register('cart');
    $cart = new shoppingCart;
  }

// include currencies class and create an instance
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

// include the mail classes
  require(DIR_WS_CLASSES . 'mime.php');
if(!isset($_noemailclass)){require(DIR_WS_CLASSES . 'email.php');};

// language
  if (!tep_session_is_registered('language') || isset($_GET['language'])) {
    if (!tep_session_is_registered('language')) {
      tep_session_register('language');
      tep_session_register('languages_id');
    }

    include(DIR_WS_CLASSES . 'language.php');
    
    if (isset($_GET['language'])) {
      $lng = new language($_GET['language']);
    } else {
      $lng = new language();
    }

    if (!isset($_GET['language'])) $lng->get_browser_language();

    $language = $lng->language['directory'];
    $languages_id = $lng->language['id'];
  }

// include the language translations
  require(DIR_WS_LANGUAGES . $language . '.php');

// Ultimate SEO URLs v2.1
    include_once(DIR_WS_CLASSES . 'seo.class.php');

    $seo_urls = new SEO_URL($languages_id);

// define our general functions used application-wide
  require(DIR_WS_FUNCTIONS . 'general.php');
  require(DIR_WS_FUNCTIONS . 'generalBoth.php');
  require(DIR_WS_FUNCTIONS . 'html_output.php');

// currency
  if (!tep_session_is_registered('currency') || isset($_GET['currency']) || ( (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') && (LANGUAGE_CURRENCY != $currency) ) ) {
    if (!tep_session_is_registered('currency')) tep_session_register('currency');

    if (isset($_GET['currency'])) {
      if (!$currency = tep_currency_exists($_GET['currency'])) $currency = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
    } else {
      $currency = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true') ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY;
    }
  }

  check_uri('/\/\//');
  if (defined('URL_SUB_SITE_ENABLED') && URL_SUB_SITE_ENABLED) {
    if (
         basename($_SERVER['SCRIPT_NAME']) != FILENAME_NEWS
      && basename($_SERVER['SCRIPT_NAME']) != FILENAME_REVIEWS
      && basename($_SERVER['SCRIPT_NAME']) != FILENAME_PRODUCT_REVIEWS
      && basename($_SERVER['SCRIPT_NAME']) != FILENAME_PRODUCT_REVIEWS_INFO
      && basename($_SERVER['SCRIPT_NAME']) != FILENAME_PAGE
      && basename($_SERVER['SCRIPT_NAME']) != FILENAME_SHOPPING_CART
      && basename($_SERVER['SCRIPT_NAME']) != FILENAME_FAQ
      && basename($_SERVER['SCRIPT_NAME']) != FILENAME_FAQ_INFO
      && basename($_SERVER['SCRIPT_NAME']) != FILENAME_PREORDER
    ) {
      tep_parseURI();
    }
  } elseif ((defined('URL_ROMAJI_ENABLED') && URL_ROMAJI_ENABLED)) {
    if (
           basename($_SERVER['SCRIPT_NAME']) != FILENAME_NEWS
        && basename($_SERVER['SCRIPT_NAME']) != FILENAME_REVIEWS
        && basename($_SERVER['SCRIPT_NAME']) != FILENAME_PRODUCT_REVIEWS
        && basename($_SERVER['SCRIPT_NAME']) != FILENAME_PRODUCT_REVIEWS_INFO
        && basename($_SERVER['SCRIPT_NAME']) != FILENAME_PAGE
        && basename($_SERVER['SCRIPT_NAME']) != FILENAME_PREORDER
        && basename($_SERVER['SCRIPT_NAME']) != FILENAME_FAQ
        && basename($_SERVER['SCRIPT_NAME']) != FILENAME_FAQ_INFO
        && !isset($_GET['manufacturers_id']) 
      ) {
        tep_parseURI();
    }
    if (defined(URL_ROMAJI_ENABLED_TAG)&&URL_ROMAJI_ENABLED_TAG) {
      if ( basename($_SERVER['SCRIPT_NAME']) != FILENAME_TAGS && !isset($_GET['tags_id']) ) {
        tep_parseURI();
      }
    }
  }



// navigation history
  if (tep_session_is_registered('navigation')) {
    if (PHP_VERSION < 4) {
      $broken_navigation = $navigation;
      $navigation = new navigationHistory;
      $navigation->unserialize($broken_navigation);
    }
  } else {
    tep_session_register('navigation');
    $navigation = new navigationHistory();
  }
  $navigation->add_current_page();

// Shopping cart actions
  if (isset($_GET['action'])) {
    if (DISPLAY_CART == 'true') {
      $goto =  FILENAME_SHOPPING_CART;
      $parameters = array('action', 'cPath', 'products_id', 'pid');
    } else {
      $goto = basename($PHP_SELF);
      if ($_GET['action'] == 'buy_now') {
        $parameters = array('action', 'pid', 'products_id');
      } else {
        $parameters = array('action', 'pid');
      }
    }

    switch ($_GET['action']) {
      // customer wants to update the product quantity in their shopping cart
      case 'update_product' : $check_products_option_delete = $_POST['cart_products_id_list'];for ($i=0, $n=sizeof($_POST['products_id']); $i<$n; $i++) {
                                if (in_array($_POST['products_id'][$i], (is_array($_POST['cart_delete']) ? $_POST['cart_delete'] : array()))) {
                                  $cart->remove($_POST['products_id'][$i]);
                                }elseif(in_array($_POST['products_id'][$i],$check_products_option_delete)){
                                  $cart->remove($_POST['products_id'][$i]);
                                } else {
                                  $hide_option_info = array(); 
                                  if (isset($_POST['option_info'][$i])) {
                                    $hide_option_info = @unserialize($_POST['option_info'][$i]); 
                                    if ($hide_option_info === false) {
                                      $hide_option_info = @unserialize(stripslashes($_POST['option_info'][$i])); 
                                    }
                                  }
                                  // 全角的英数字改成半角
                                  $_POST['cart_quantity'][$i] = tep_an_zen_to_han($_POST['cart_quantity'][$i]);                 
                                  if ($_POST['cart_quantity'][$i] == 0) {
                                    $cart->remove($_POST['products_id'][$i]);
                                    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL')); 
                                  } else {
                                    $cart->add_cart((int)$_POST['products_id'][$i], $_POST['cart_quantity'][$i], '', false, $hide_option_info);
                                  }
                                }
                              }
                              $weight_count = 0;                        
                              foreach($_POST['products_id'] as $p_key=>$p_value){

                                $p_array = explode("_",$p_value);
                                $products_id_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id='". $p_array[0] ."'");
                                $products_id_array = tep_db_fetch_array($products_id_query);
                                tep_db_free_result($products_id_query);
                                $weight_count += $products_id_array['products_weight']*$_POST['cart_quantity'][$p_key];
                              }

                              $max_weight_array  = array();
                              $products_error = false;
                              $country_fee_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_FEE ." where status='0'");
                              while($country_fee_array = tep_db_fetch_array($country_fee_query)){ 
                                $max_weight_array[] = $country_fee_array['weight_limit'];
                              }

                              $max_area_weight_array = array();
                              $country_area_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_AREA ." where status='0'");
                              while($country_area_array = tep_db_fetch_array($country_area_query)){ 
                                $max_area_weight_array[] = $country_area_array['weight_limit'];
                              }

                              $max_city_weight_array = array();
                              $country_city_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_CITY ." where status='0'");
                              while($country_city_array = tep_db_fetch_array($country_city_query)){ 
                                $max_city_weight_array[] = $country_city_array['weight_limit'];
                              } 

                              $max_weight = max($max_weight_array);
                              $max_area_weight = max($max_area_weight_array);
                              $max_city_weight = max($max_city_weight_array);

                              $max_weight_count = 0;
                              $max_weight_count = max($max_weight,$max_area_weight,$max_city_weight);
                              if($weight_count > $max_weight_count){

                                $products_error = true;
                              }
                              if($products_error == false){
                                if (isset($_POST['continue']) && $_POST['goto']) {
                                  tep_redirect($_POST['goto']);
                                } else if (isset($_POST['checkout'])) {
                                  tep_redirect(tep_href_link(FILENAME_CHECKOUT_ATTRIBUTES, '', 'SSL'));
                                } else {
                                  tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
                                }
                              }
                              break;
      // performed by the 'buy now' button in product listings and review page
      case 'buy_now' :        forward404();
                              if (isset($_GET['products_id'])) {
                                if (tep_has_product_attributes($_GET['products_id'])) {
                                  tep_redirect(tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $_GET['products_id']));
                                } else {
                                  $cart->add_cart($_GET['products_id'], $cart->get_quantity($_GET['products_id'])+1);
                                }
                              }
                              tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
                              break;
      case 'notify' :         if (tep_session_is_registered('customer_id')) {
                                if (isset($_GET['products_id'])) {
                                  $notify = $_GET['products_id'];
                                } elseif (isset($_GET['notify'])) {
                                  $notify = $_GET['notify'];
                                } elseif (isset($_POST['notify'])) {
                                  $notify = $_POST['notify'];
                                } else {
                                  tep_redirect(tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action', 'notify'))));
                                }
                                if (!is_array($notify)) $notify = array($notify);
                                for ($i=0, $n=sizeof($notify); $i<$n; $i++) {
                                  $check_query = tep_db_query("
                                      select count(*) as count 
                                      from " . TABLE_PRODUCTS_NOTIFICATIONS . " 
                                      where products_id = '" . $notify[$i] . "' 
                                        and customers_id = '" . $customer_id . "'
                                  ");
                                  $check = tep_db_fetch_array($check_query);
                                  if ($check['count'] < 1) {
                                    tep_db_query("
                                        insert into " . TABLE_PRODUCTS_NOTIFICATIONS . " (
                                          products_id, 
                                          customers_id, 
                                          date_added
                                        ) values (
                                          '" . $notify[$i] . "', 
                                          '" . $customer_id . "', 
                                          now()
                                        )
                                    ");
                                  }
                                }
                                tep_redirect(tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action', 'notify'))));
                              } else {
                                $navigation->set_snapshot();
                                tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
                              }
                              break;
      case 'notify_remove' :  if (tep_session_is_registered('customer_id') && isset($_GET['products_id'])) {
                                $check_query = tep_db_query("
                                    select count(*) as count 
                                    from " . TABLE_PRODUCTS_NOTIFICATIONS . " 
                                    where products_id = '" . $_GET['products_id'] . "' 
                                      and customers_id = '" . $customer_id . "'
                                ");
                                $check = tep_db_fetch_array($check_query);
                                if ($check['count'] > 0) {
                                  tep_db_query("
                                      delete from " . TABLE_PRODUCTS_NOTIFICATIONS . " 
                                      where products_id = '" . $_GET['products_id'] . "' 
                                        and customers_id = '" . $customer_id . "'
                                  ");
                                }
                                tep_redirect(tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action'))));
                              } else {
                                $navigation->set_snapshot();
                                tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
                              }
                              break;
      case 'cust_order' :     if (tep_session_is_registered('customer_id') && isset($_GET['pid'])) {
                                if (tep_has_product_attributes($_GET['pid'])) {
                                  tep_redirect(tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $_GET['pid']));
                                } else {
                                  $cart->add_cart($_GET['pid'], $cart->get_quantity($_GET['pid'])+1);
                                }
                              }
                              tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
                              break;
    }
  }
  
  // 统计 REFERER
  if (!isset($_SESSION['referer']) && $_SERVER["HTTP_REFERER"]) {
if(!preg_match ("#".HTTP_SERVER."#", $_SERVER["HTTP_REFERER"]) && !preg_match ("#".HTTPS_SERVER."#", $_SERVER["HTTP_REFERER"])){
    $_SESSION['referer'] = $_SERVER["HTTP_REFERER"];
	  }
    // 统计 Google Adsense
    if (isset($_GET['from']) && $_GET['from'] == 'adwords') {
      $_SESSION['referer_adurl'] = '1';
    }
  }


// include the who's online functions
  require(DIR_WS_FUNCTIONS . 'whos_online.php');
  tep_update_whos_online();

// include the password crypto functions
  require(DIR_WS_FUNCTIONS . 'password_funcs.php');

// include validation functions (right now only email address)
  require(DIR_WS_FUNCTIONS . 'validations.php');

// split-page-results
  require(DIR_WS_CLASSES . 'split_page_results.php');

// infobox
  require(DIR_WS_CLASSES . 'boxes.php');

// auto activate and expire banners
  require(DIR_WS_FUNCTIONS . 'banner.php');
  tep_activate_banners();

// auto expire special products
  require(DIR_WS_FUNCTIONS . 'specials.php');
  tep_expire_specials();

// calculate category path
  if (isset($_GET['cPath'])) {
    $cPath = $_GET['cPath'];
  } elseif (isset($_GET['products_id']) && !isset($_GET['manufacturers_id'])) {
    $cPath = tep_get_product_path($_GET['products_id']);
  } else {
    $cPath = '';
  }

  if (tep_not_null($cPath)) {
    $cPath_array = tep_parse_category_path($cPath);
    $cPath = implode('_', $cPath_array);
    $current_category_id = $cPath_array[(sizeof($cPath_array)-1)];
  } else {
    $current_category_id = 0;
  }
  
  if (tep_not_null($cPath)) {
    if (tep_check_black_category($current_category_id)) {
      if(!preg_match('/'.FILENAME_SHOPPING_CART.'/',$PHP_SELF)){
        forward404(); 
      }
    }
  }

  // include the breadcrumb class and start the breadcrumb trail
  require(DIR_WS_CLASSES . 'breadcrumb.php');
  $breadcrumb = new breadcrumb;

  
  //add new variable 
  $breadcrumb->add(HEADER_TITLE_TOP, HTTP_SERVER);

// add category names or the manufacturer name to the breadcrumb trail
  if (isset($cPath_array)) {
    for ($i=0, $n=sizeof($cPath_array); $i<$n; $i++) {
      $categories_query = tep_db_query("
          select categories_name, categories_status 
          from " .  TABLE_CATEGORIES_DESCRIPTION . " 
          where categories_id = '" .  $cPath_array[$i] . "' 
            and language_id='" . $languages_id . "' 
            and (site_id = ".SITE_ID." or site_id = 0)
          order by site_id DESC
          limit 1" 
      );
      if (tep_db_num_rows($categories_query) > 0) {
        $categories = tep_db_fetch_array($categories_query);
        if ($_SERVER['PHP_SELF'] == '/'.FILENAME_PRODUCT_INFO) {
          $check_pro_status_raw = tep_db_query("select products_status from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".(int)$_GET['products_id']."' and (site_id = 0 or site_id = ".SITE_ID.") order by site_id DESC limit 1"); 
          $check_pro_status = tep_db_fetch_array($check_pro_status_raw); 
          if ($check_pro_status['products_status'] == 0) {
            if ($categories['categories_status'] == 1) {
            $breadcrumb->add($categories['categories_name']);
            } else {
            $breadcrumb->add($categories['categories_name'], tep_href_link(FILENAME_DEFAULT, 'cPath=' . implode('_', array_slice($cPath_array, 0, ($i+1)))));
          }
          } else {
            if ($categories['categories_status'] == 1) { 
            $breadcrumb->add($categories['categories_name']);
            }else{
            $breadcrumb->add($categories['categories_name'], tep_href_link(FILENAME_DEFAULT, 'cPath=' . implode('_', array_slice($cPath_array, 0, ($i+1)))));
            }
          }
        } else {
          if ($categories['categories_status'] == 1) {
            $breadcrumb->add($categories['categories_name']);
          } else {
            $breadcrumb->add($categories['categories_name'], tep_href_link(FILENAME_DEFAULT, 'cPath=' . implode('_', array_slice($cPath_array, 0, ($i+1)))));
          }
        }
      } else {
        break;
      }
    }
  } elseif (isset($_GET['manufacturers_id'])) {
    $manufacturers_query = tep_db_query("
        select manufacturers_name 
        from " . TABLE_MANUFACTURERS . " 
        where manufacturers_id = '" . $_GET['manufacturers_id'] . "'
    ");
    $manufacturers = tep_db_fetch_array($manufacturers_query);
    $breadcrumb->add($manufacturers['manufacturers_name'], tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $_GET['manufacturers_id']));
  } elseif (isset($_GET['action']) && $_GET['action'] == 'select') {
    $breadcrumb->add('マイゲーム', tep_href_link(FILENAME_DEFAULT, 'action=select'));
  }

// add the products model to the breadcrumb trail
  if (isset($_GET['products_id'])) {
    $model_query = tep_db_query("
        select products_name 
        from " .  TABLE_PRODUCTS_DESCRIPTION . " 
        where products_id = '" .  $_GET['products_id'] . "' 
          and language_id ='" . $languages_id . "' 
          and (site_id     = ".SITE_ID." or site_id = 0)
        order by site_id DESC
        limit 1
        ");
    $model = tep_db_fetch_array($model_query);
    $breadcrumb->add($model['products_name'], tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $_GET['products_id']));
  }
  // add tags
  if (isset($_GET['tags_id'])) {
    $tags_query = tep_db_query("select * from ".TABLE_TAGS." where tags_id = '".$_GET['tags_id']."'");
    $tags_res = tep_db_fetch_array($tags_query);
    if ($tags_res) {
      $breadcrumb->add(TEXT_TAGS, 'tags/');
    }
   }

// SESSION REGISTER
  switch($_GET['ajax']){
    case 'on' :
      $ajax = 'on' ;
      break;
    case 'off' :
      $ajax = 'off' ;
      break;
  }

  tep_session_register('ajax');
  //检测商品OPTION是否改动
  $check_products_option = tep_check_less_product_option();
  $products_cart_array = $cart->get_products();
  if($_GET['action'] != 'update_product'){
    unset($_SESSION['change_option_id']);
    unset($_SESSION['change_option_flag']);
    //记录OPTION有变化的商品
    for ($i=0, $n=sizeof($products_cart_array); $i<$n; $i++) { 
      if(in_array($products_cart_array[$i]['id'],$check_products_option)){
        $_SESSION['change_option_id'][] = $products_cart_array[$i]['id']; 
      }else{
        $_SESSION['change_option_flag'][] = $products_cart_array[$i]['id']; 
      }
    }
  }
# 订单上限金额设置
  if(substr(basename($PHP_SELF),0,9) == 'checkout_') {
    if(DS_LIMIT_PRICE < $cart->show_total()) {
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, 'limit_error=true', 'SSL'));
    }
    if(substr(basename($PHP_SELF),0,16) != 'checkout_success') {
        $limit_price = explode(',', LIMIT_MIN_PRICE);
        if (count($limit_price) == 2) {
          if (($cart->show_total() <= $limit_price[1]) && ($cart->show_total() >= $limit_price[0])) {
            tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, 'limit_min_error=true', 'SSL'));
          }
        } else {
          if(LIMIT_MIN_PRICE &&
              (($cart->show_total() <= LIMIT_MIN_PRICE) && ($cart->show_total() >= 1))) {
            tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, 'limit_min_error=true', 'SSL'));
          }
        }
    }
  }
  define('TABLE_OA_GROUP', 'oa_group'); 
  define('TABLE_OA_FORM', 'oa_form'); 
  define('TABLE_OA_FORM_GROUP', 'oa_form_group'); 
  define('TABLE_OA_ITEM', 'oa_item'); 
  define('TABLE_OA_FORMVALUE', 'oa_formvalue');
  define('TABLE_ADDRESS','address');
  define('TABLE_OPTION_GROUP','option_group');
  define('TABLE_OPTION_ITEM','option_item');
  define('TABLE_PRODUCTS_SHIPPING_TIME','products_shipping_time');
  define('TABLE_COUNTRY_FEE','country_fee');
  define('TABLE_COUNTRY_AREA','country_area');
  define('TABLE_COUNTRY_CITY','country_city');
  define('TABLE_ADDRESS_ORDERS','address_orders');
  define('TABLE_ADDRESS_HISTORY','address_history');
  define('TABLE_CALENDAR_STATUS','calendar_status');
  define('TABLE_CALENDAR_DATE','calendar_date'); 
  
  if(!preg_match('/^\d+$/',trim($_GET['page']))&&trim($_GET['page'])){
    forward404();
  }
  if (!empty($_GET)) {
    foreach ($_GET as $g_c_key => $g_c_value) {
      $check_sel_pos = stripos($g_c_value, 'select'); 
      $check_union_pos = stripos($g_c_value, 'union'); 
      $check_from_pos = stripos($g_c_value, 'from'); 
      $check_two_c_pos = stripos($g_c_value, '%2C'); 
      $check_nine_pos = strpos($g_c_value, '9999999999'); 
      $check_order_by_pos = stripos($g_c_value, 'order by'); 
      $check_ascii_pos = stripos($g_c_value, 'ascii'); 
      $check_char_pos = stripos($g_c_value, 'char('); 

      if ($check_ascii_pos !== false) {
        forward404(); 
        break; 
      }

      if ($check_nine_pos !== false) {
        forward404(); 
        break; 
      }
      
      if ($check_order_by_pos !== false) {
        forward404(); 
        break; 
      }

      if ($check_two_c_pos !== false) {
        forward404(); 
        break; 
      }

      if ($check_char_pos !== false) {
        forward404(); 
        break; 
      }
      
      if (($check_sel_pos !== false) && $check_union_pos !== false) {
        forward404(); 
        break; 
      }

      if (($check_sel_pos !== false) && $check_from_pos !== false) {
        forward404(); 
        break; 
      }
    }
  }
// this word for ie 6 7 javascript:viod()
$has_a_link = true;
if(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0')){
  $has_a_link = false;
}
if(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 7.0')){
  $has_a_link = false;
}
$void_href = '';
if($has_a_link){
  $void_href = ' href="javascript:void(0)" ';
}
