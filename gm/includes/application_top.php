<?php
/*
  $Id$
*/
  $GLOBALS['HTTP_GET_VARS']    = $_GET;
  $GLOBALS['HTTP_POST_VARS']   = $_POST;
  $GLOBALS['HTTP_SERVER_VARS'] = $_SERVER;

//Japan location
  setlocale (LC_ALL, 'ja_JP.UTF-8');

// start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime());

// set the level of error reporting
  error_reporting(0);
  ini_set("display_errors", "Off");
  //error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
  //ini_set("display_errors", "On");

// check if register_globals is enabled.
// since this is a temporary measure this message is hardcoded. The requirement will be removed before 2.2 is finalized.
  if (function_exists('ini_get')) {
    ini_get('register_globals') or exit('FATAL ERROR: register_globals is disabled in php.ini, please enable it!');
  }

// disable use_trans_sid as tep_href_link() does this manually
  if (function_exists('ini_set')) @ini_set('session.use_trans_sid', 0);

// Set the local configuration parameters - mainly for developers
  if (file_exists('includes/local/configure.php')) include('includes/local/configure.php');

// include server parameters
  require('includes/configure.php');

// Set lib path
  ini_set('include_path',ini_get('include_path').':'.DIR_FS_3RMTLIB);

// define the project version
  define('PROJECT_VERSION', 'osCommerce 2.2-MS1');

// set the type of request (secure or not)
  $request_type = (getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';

// define the filenames used in the project
  define('FILENAME_ACCOUNT', 'account.php');
  define('FILENAME_TAGS', 'tags.php');
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
  define('FILENAME_CHECKOUT_PRODUCTS', 'checkout_products.php');
  define('FILENAME_CONTACT_US', 'contact_us.php');
  define('FILENAME_CONDITIONS', 'conditions.php');
  define('FILENAME_CREATE_ACCOUNT', 'create_account.php');
  define('FILENAME_CREATE_ACCOUNT_PROCESS', 'create_account_process.php');
  define('FILENAME_CREATE_ACCOUNT_SUCCESS', 'create_account_success.php');
  define('FILENAME_DEFAULT', 'index.php');
  define('FILENAME_CATEGORY', FILENAME_DEFAULT);
  define('FILENAME_MANFACTURER', FILENAME_DEFAULT);
  define('FILENAME_DOWNLOAD', 'download.php');
  define('FILENAME_FAQ', 'faq.php');
  define('FILENAME_GGSITEMAP', 'ggsitemap.php');
  define('FILENAME_INFO_SHOPPING_CART', 'info_shopping_cart.php');
  define('FILENAME_LOGIN', 'login.php');
  define('FILENAME_LOGOFF', 'logoff.php');
  define('FILENAME_LATEST_NEWS', 'latest_news.php'); //Add latest_news
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
  define('FILENAME_PRESENT_ORDER','present_order.php');
  define('FILENAME_PRESENT_POPUP_IMAGE','present_popup_image.php');
  define('FILENAME_PRESENT_CONFIRMATION','present_confirmation.php');
  define('FILENAME_PRESENT_SUCCESS','present_success.php');
  define('FILENAME_PDF_DATASHEET', 'pdf_datasheet.php');
  define('FILENAME_PAGE', 'page.php');//Add Filename

  define('FILENAME_REORDER', 'reorder.php');
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
  define('FILENAME_SEND_MAIL', 'send_mail.php');
  define('FILENAME_EMAIL_TROUBLE', 'email_trouble.php');

// define the database table names used in the project
  define('TABLE_ADDRESS_BOOK', 'address_book');
  define('TABLE_TAGS', 'tags');
  define('TABLE_PRODUCTS_TO_TAGS', 'products_to_tags');
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
  //define('TABLE_PRESENT_APPLICANT', 'gm_present_applicant');
  define('TABLE_MAIL_MAGAZINE', 'mail_magazine');
  define('TABLE_ORDERS_MAIL', 'orders_mail');
  
  //Database
  define('TABLE_INFORMATION_PAGE', 'information_page');//Information box
  define('TABLE_LATEST_NEWS', 'latest_news'); //latest_news
  
  define('TABLE_COLOR', 'color');//Color setting
  define('TABLE_COLOR_TO_PRODUCTS', 'color_to_products');//products_id <-> color_id

// customization for the design layout
  define('BOX_WIDTH', 171); // how wide the boxes should be in pixels (default: 125)

// check if sessions are supported, otherwise use the php3 compatible session class
  if (!function_exists('session_start')) {
    define('PHP_SESSION_NAME', 'SID');
    define('PHP_SESSION_SAVE_PATH', '/tmp/');

    include(DIR_WS_CLASSES . 'sessions.php');
  }

// define how the session functions will be used
  require(DIR_WS_FUNCTIONS . 'sessions.php');
  tep_session_name('SID');

// include the database functions
  require(DIR_WS_FUNCTIONS . 'database.php');

// make a connection to the database... now
  tep_db_connect() or die('Unable to connect to database server!');

// set the application parameters (can be modified through the administration tool)
  // ccdd
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

// lets start our session
   if (isset($_POST[tep_session_name()])) {
     tep_session_id($_POST[tep_session_name()]);
   } elseif ( (getenv('HTTPS') == 'on') && isset($_GET[tep_session_name()]) ) {
     tep_session_id($_GET[tep_session_name()]);
   }

   if (function_exists('session_set_cookie_params')) {
    //session_set_cookie_params(0, substr(DIR_WS_CATALOG, 0, -1));
  session_set_cookie_params(0, '/');
  }

  tep_session_start();

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
  require(DIR_WS_CLASSES . 'email.php');

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

// navigation history
  if (tep_session_is_registered('navigation')) {
    if (PHP_VERSION < 4) {
      $broken_navigation = $navigation;
      $navigation = new navigationHistory;
      $navigation->unserialize($broken_navigation);
    }
  } else {
    tep_session_register('navigation');
    $navigation = new navigationHistory;
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
      case 'update_product' : for ($i=0, $n=sizeof($_POST['products_id']); $i<$n; $i++) {
                                if (in_array($_POST['products_id'][$i], (is_array($_POST['cart_delete']) ? $_POST['cart_delete'] : array()))) {
                                  $cart->remove($_POST['products_id'][$i]);
                                } else {
                                  if (PHP_VERSION < 4) {
                                    // if PHP3, make correction for lack of multidimensional array.
                                    reset($_POST);
                                    while (list($key, $value) = each($_POST)) {
                                      if (is_array($value)) {
                                        while (list($key2, $value2) = each($value)) {
                                          if (ereg ("(.*)\]\[(.*)", $key2, $var)) {
                                            $id2[$var[1]][$var[2]] = $value2;
                                          }
                                        }
                                      }
                                    }
                                    $attributes = ($id2[$_POST['products_id'][$i]]) ? $id2[$_POST['products_id'][$i]] : '';
                                  } else {
                                    $attributes = ($_POST['id'][$_POST['products_id'][$i]]) ? $_POST['id'][$_POST['products_id'][$i]] : '';
                                  }
                                  // tamura 2002/12/30 「全角」英数字を「半角」に変換
                                  $_POST['cart_quantity'][$i] = tep_an_zen_to_han($_POST['cart_quantity'][$i]);                 
                                  $cart->add_cart($_POST['products_id'][$i], $_POST['cart_quantity'][$i], $attributes, false);
                                }
                              }
                              if (isset($_POST['continue']) && $_POST['goto']) {
                                tep_redirect($_POST['goto']);
                              } else if (isset($_POST['checkout'])) {
                                tep_redirect(tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL'));
                              } else {
                                tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
                              }
                              break;
      // customer adds a product from the products page
      case 'add_product' :    if (isset($_POST['products_id']) && is_numeric($_POST['products_id'])) {
                                $cart->add_cart($_POST['products_id'], $cart->get_quantity(tep_get_uprid($_POST['products_id'], $_POST['id']))+$_POST['quantity'], $_POST['id']);
                              }
                              tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
                              break;
      // performed by the 'buy now' button in product listings and review page
      case 'buy_now' :        if (isset($_GET['products_id'])) {
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
                                  // ccdd
                                  $check_query = tep_db_query("
                                      select count(*) as count 
                                      from " . TABLE_PRODUCTS_NOTIFICATIONS . " 
                                      where products_id = '" . $notify[$i] . "' 
                                        and customers_id = '" . $customer_id . "'
                                  ");
                                  $check = tep_db_fetch_array($check_query);
                                  if ($check['count'] < 1) {
                                    // ccdd
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
                                // ccdd
                                $check_query = tep_db_query("
                                    select count(*) as count 
                                    from " . TABLE_PRODUCTS_NOTIFICATIONS . " 
                                    where products_id = '" . $_GET['products_id'] . "' 
                                      and customers_id = '" . $customer_id . "'
                                ");
                                $check = tep_db_fetch_array($check_query);
                                if ($check['count'] > 0) {
                                  // ccdd
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
  //tep_expire_banners();

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

// include the breadcrumb class and start the breadcrumb trail
  require(DIR_WS_CLASSES . 'breadcrumb.php');
  $breadcrumb = new breadcrumb;

  $breadcrumb->add(HEADER_TITLE_TOP, HTTP_SERVER);
  //$breadcrumb->add(HEADER_TITLE_CATALOG, tep_href_link(FILENAME_DEFAULT));

// add category names or the manufacturer name to the breadcrumb trail
  if (isset($cPath_array)) {
    for ($i=0, $n=sizeof($cPath_array); $i<$n; $i++) {
      // ccdd
      $categories_query = tep_db_query("
          select categories_name 
          from " .  TABLE_CATEGORIES_DESCRIPTION . " 
          where categories_id = '" .  $cPath_array[$i] . "' 
            and language_id='" . $languages_id . "' 
            and (site_id = ".SITE_ID." or site_id = 0)
          order by site_id DESC
          limit 1" 
      );
      if (tep_db_num_rows($categories_query) > 0) {
        $categories = tep_db_fetch_array($categories_query);
        $breadcrumb->add($categories['categories_name'], tep_href_link(FILENAME_DEFAULT, 'cPath=' . implode('_', array_slice($cPath_array, 0, ($i+1)))));
      } else {
        break;
      }
    }
  } elseif (isset($_GET['manufacturers_id'])) {
    // ccdd
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
  // ccdd
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
      $breadcrumb->add($tags_res['tags_name'], tep_href_link(FILENAME_DEFAULT, 'tags_id='.$_GET['tags_id']));
    }
   }
    

// set which precautions should be checked
  define('WARN_INSTALL_EXISTENCE', 'true');
  define('WARN_CONFIG_WRITEABLE', 'true');
  define('WARN_SESSION_DIRECTORY_NOT_WRITEABLE', 'true');
  define('WARN_SESSION_AUTO_START', 'true');
  define('WARN_DOWNLOAD_DIRECTORY_NOT_READABLE', 'true');

  //for sql_log
  $testArray = array();
  $logNumber = 1;
  //end for sql_log

// SESSION REGISTER
if (!isset($_GET['ajax'])) $_GET['ajax']= NULL;
switch($_GET['ajax']){
  case 'on' :
    $ajax = 'on' ;
    break;
  case 'off' :
    $ajax = 'off' ;
    break;
}

tep_session_register('ajax');

# 注文上限金額設定
  if(substr(basename($PHP_SELF),0,9) == 'checkout_') {
    if(DS_LIMIT_PRICE < $cart->show_total()) {
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, 'limit_error=true'));
    }
  }
