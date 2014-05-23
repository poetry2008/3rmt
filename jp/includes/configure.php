<?php
/*
 $Id$
*/

// Define the webserver and path parameters
// * DIR_FS_* = Filesystem directories (local/physical)
// * DIR_WS_* = Webserver directories (virtual/URL)
//  define('HTTP_SERVER', 'http://3jp.bobhero.200.com'); // eg, http://localhost - should not be empty for productive servers
  define('HTTP_SERVER', 'http://jp.hm1004.200.com'); // eg, http://localhost - should not be empty for productive servers
//  define('HTTP_SERVER', 'http://hmtest9.3322.org');

  define('HTTPS_SERVER', 'https://cchm.vicp.net'); // eg, https://localhost - should not be empty for productive servers
  define('ENABLE_SSL', false); // secure webserver for checkout procedure?
  define('DIR_WS_CATALOG', '/'); // absolute path required
  define('DIR_WS_IMAGES', 'images/');
  define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');
  define('DIR_WS_INCLUDES', 'includes/');
  define('DIR_WS_ACTIONS', DIR_WS_INCLUDES.'actions/'); 
  define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/');
  define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
  define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
  define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');
  define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/');

  define('DIR_WS_DOWNLOAD_PUBLIC', DIR_WS_CATALOG . 'pub/');
  define('DIR_FS_DOCUMENT_ROOT', '/home/hm1004/project/rmt/jp/');
  define('DIR_FS_CATALOG', '/home/hm1004/project/rmt/jp/');
  define('DIR_FS_DOWNLOAD', DIR_FS_CATALOG . 'download/');
  define('DIR_FS_DOWNLOAD_PUBLIC', DIR_FS_CATALOG . 'pub/');
//$libpath = "/home/szn/project/3rmt/3rmtlib/";
  define('DIR_FS_3RMTLIB', '/home/hm1004/project/rmt/jp/3rmtlib/');

// define our database connection
  define('DB_SERVER', 'localhost'); // eg, localhost - should not be empty for productive servers
  define('DB_SERVER_USERNAME', 'root');
  define('DB_SERVER_PASSWORD', '123456');
  define('DB_DATABASE', '3rmt_gt');
  define('USE_PCONNECT', 'false'); // use persistent connections?
  define('STORE_SESSIONS', ''); // leave empty '' for default handler or set to 'mysql'

  define('REWRITE_PRODUCTS', 'item/');//Add Ultimate_SEO_URLS
  define('REWRITE_CATEGORIES', 'rmt/');//Add Ultimate_SEO_URLS
  define('REWRITE_MANUFACTURERES', 'game/');//Add Ultimate_SEO_URLS

  define('SITE_ID', '1'); 
  //control sql_log
  define('SQL_LOG', false);



  ini_set('include_path',ini_get('include_path').':'.DIR_FS_3RMTLIB);
//osticket start
  //define('DIR_OST',DIR_FS_DOCUMENT_ROOT.DIR_WS_INCLUDES.'ost/');
  define('DIR_OST','includes/ost/');
  ini_set('include_path',ini_get('include_path').':'.DIR_FS_3RMTLIB.'includes/ost/include/pear');
  define('SITE_TOPIC_ID',3); 
  
//paypal 
  define("paypal_environment",'sandbox');
//define("paypal_environment",'live');
//  define("my_api_username","testanaka99_api1.gmail.com");
  define("my_api_username","testanaka99_api1.gmail.com");
  define("my_api_password","THW5VLVMGN6EBN2Q");
  define("my_api_signature","AFcWxV21C7fd0v3bYYYRCpSSRl31A-xnmo0jnYaLEu6YtiFKRwhz.27E");
  define('DDOS_SEND_MAIL', 'bobhero.chen@gmail.com');
  define('DDOS_SEND_MAIL_TITLE', 'ddos info');
  define('NEW_TYPE_SYMBOL', true);
