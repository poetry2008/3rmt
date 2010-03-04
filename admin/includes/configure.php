<?php
/*
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
$libpath = "/home/maker/project/3rmt/3rmtlib/";
ini_set('include_path',ini_get('include_path').':'.$libpath);

// Define the webserver and path parameters
// * DIR_FS_* = Filesystem directories (local/physical)
// * DIR_WS_* = Webserver directories (virtual/URL)
  define('HTTP_SERVER', 'http://3admin.maker.200.com'); // eg, http://localhost or - https://localhost should not be empty for productive servers
  define('HTTP_CATALOG_SERVER', 'http://3admin.maker.200.com');
  define('HTTPS_CATALOG_SERVER', 'https://3admin.maker.200.com');
  define('ENABLE_SSL_CATALOG', 'false'); // secure webserver for catalog module
  define('DIR_FS_DOCUMENT_ROOT', '/home/maker/project/3rmt/'); // where the pages are located on the server
  define('DIR_WS_ADMIN', '/'); // absolute path required
  define('DIR_FS_ADMIN', '/home/maker/project/3rmt/admin/'); // absolute pate required
  define('DIR_WS_CATALOG', './'); // absolute path required
  define('DIR_FS_CATALOG', ''); // absolute path required
  define('DIR_WS_IMAGES', 'images/');
  define('DIR_WS_ICONS', DIR_WS_IMAGES . 'icons/');
  define('DIR_WS_CATALOG_IMAGES', DIR_WS_CATALOG . 'images/');
  define('DIR_WS_INCLUDES', 'includes/');
  define('DIR_WS_BOXES', DIR_WS_INCLUDES . 'boxes/');
  define('DIR_WS_FUNCTIONS', DIR_WS_INCLUDES . 'functions/');
  define('DIR_WS_CLASSES', DIR_WS_INCLUDES . 'classes/');
  define('DIR_WS_MODULES', DIR_WS_INCLUDES . 'modules/');
  define('DIR_WS_LANGUAGES', DIR_WS_INCLUDES . 'languages/');
  define('DIR_WS_CATALOG_LANGUAGES', DIR_WS_CATALOG . 'includes/languages/');
  define('DIR_FS_CATALOG_LANGUAGES', DIR_FS_CATALOG . 'includes/languages/');
  define('DIR_FS_CATALOG_IMAGES', DIR_FS_CATALOG . 'images/');
  define('DIR_FS_CATALOG_MODULES', $libpath. 'includes/modules/');
  define('DIR_FS_BACKUP', DIR_FS_ADMIN . 'backups/');

// define our database connection
  define('DB_SERVER', 'localhost'); // eg, localhost - should not be empty for productive servers
  define('DB_SERVER_USERNAME', 'root');
  define('DB_SERVER_PASSWORD', '123456');
  define('DB_DATABASE', 'maker_3rmt');
  define('USE_PCONNECT', 'false'); // use persisstent connections?
  define('STORE_SESSIONS', ''); // leave empty '' for default handler or set to 'mysql'

  //control sql_log
  define('SQL_LOG', false);
  //add image document
  define('DIR_WS_IMAGE_DOCUMENTS', 'imageDocuments/');
  define('DIR_FS_CATALOG_IMAGE_DOCUMENTS', DIR_FS_CATALOG.'imageDocuments/');
?>
