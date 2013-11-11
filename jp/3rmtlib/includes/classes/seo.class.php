<?php
define('URL_TYPE_CPATH',1);
define('URL_TYPE_PRODUCT',2);
class SEO_DataBase{
  /**
  * Database host (localhost, IP based, etc)
  * @var string
  */
  var $host;
  /**
  * Database user
  * @var string
  */
  var $user;
  /**
  * Database name
  * @var string
  */
  var $db;
  /**
  * Database password
  * @var string
  */
  var $pass;
  /**
  * Database link
  * @var resource
  */
  var $link_id;

/**
 * MySQL_DataBase class constructor 
 * @param string $host
 * @param string $user
 * @param string $db
 * @param string $pass  
 */ 
/*----------------------
 功能：MySQL_DataBase类的构造函数
 参数: $host(string) 主机
 参数：$user(string) 用户
 参数：$db(string) 数据库
 参数：$pass(string) 密码
 返回值：无
 ---------------------*/
  function SEO_DataBase($host, $user, $db, $pass){
    $this->host = $host;
    $this->user = $user;
    $this->db = $db;
    $this->pass = $pass;    
    $this->ConnectDB();
    $this->SelectDB();
  } # end function

/**
 * Function to connect to MySQL 
 */ 
/*----------------------
 功能：链接MYSQL
 参数：无
 返回值：无
 ---------------------*/
  function ConnectDB(){
    $this->link_id = mysql_connect($this->host, $this->user, $this->pass);
  } # end function
  
/**
 * Function to select the database
 * @return resoource 
 */ 
/*----------------------
 功能：选择数据库 
 参数：无
 返回值：无
 ---------------------*/
  function SelectDB(){
    return mysql_select_db($this->db);
  } # end function
  
/**
 * Function to perform queries
 * @param string $query SQL statement
 * @return resource 
 */ 
/*----------------------
 功能：查询MYSQL 
 参数：$query(string) SQL字符串
 返回值：返回sql字符串(string) 
 ---------------------*/
  function Query($query){
    return @mysql_query($query, $this->link_id);
  } # end function
  
/**
 * Function to fetch array
 * @param resource $resource_id
 * @param string $type MYSQL_BOTH or MYSQL_ASSOC
 * @return array 
 */ 
/*----------------------
 功能：获取数组
 参数：$resource_id(string) ID值
 参数：$type(string) 数据库类型
 返回值：数组(array)
 ---------------------*/
  function FetchArray($resource_id, $type = MYSQL_BOTH){
    return @mysql_fetch_array($resource_id, $type);
  } # end function
  
/**
 * Function to fetch the number of rows
 * @param resource $resource_id
 * @return mixed  
 */
/*---------------------
 功能：获取行数
 参数：$resource_id(string) ID值
 返回值：数据行数(string) 
 --------------------*/
  function NumRows($resource_id){
    return @mysql_num_rows($resource_id);
  } # end function

/**
 * Function to fetch the last insertID
 * @return integer  
 */ 
/*--------------------
 功能：获取最后插入的ID 
 参数：无
 返回值：返回值ID值(string) 
 -------------------*/
  function InsertID() {
    return mysql_insert_id();
  }
  
/**
 * Function to free the resource
 * @param resource $resource_id
 * @return boolean
 */ 
/*---------------------
 功能：释放资源
 参数：$resource_id(string) ID值
 返回值：释放的资源(string) 
 --------------------*/
  function Free($resource_id){
    return @mysql_free_result($resource_id);
  } # end function

/**
 * Function to add slashes
 * @param string $data
 * @return string 
 */ 
/*--------------------
 功能：添加字符串
 参数：$data(string) 数据
 返回值：返回字符串(string) 
 -------------------*/
  function Slashes($data){
    return addslashes($data);
  } # end function

/**
 * @param string $table Database table
 * @param array $data Associative array of columns / values
 * @param string $action insert or update
 * @param string $parameters
 * @return resource
 */ 
/*--------------------
 功能：添加或者更新数据 
 参数：$table(string) 数据库表名
 参数: $action(string) 插入或者更新
 参数：$parameters(string) 自定义参数
 返回值：添加或者更新的数据(string) 
 -------------------*/
  function DBPerform($table, $data, $action = 'insert', $parameters = '') {
    reset($data);
    if ($action == 'insert') {
      $query = 'INSERT INTO `' . $table . '` (';
      while (list($columns, ) = each($data)) {
      $query .= '`' . $columns . '`, ';
      }
      $query = substr($query, 0, -2) . ') values (';
      reset($data);
      while (list(, $value) = each($data)) {
      switch ((string)$value) {
        case 'now()':
        $query .= 'now(), ';
        break;
        case 'null':
        $query .= 'null, ';
        break;
        default:
        $query .= "'" . $this->Slashes($value) . "', ";
        break;
      }
      }
      $query = substr($query, 0, -2) . ')';
    } elseif ($action == 'update') {
      $query = 'UPDATE `' . $table . '` SET ';
      while (list($columns, $value) = each($data)) {
      switch ((string)$value) {
        case 'now()':
        $query .= '`' .$columns . '`=now(), ';
        break;
        case 'null':
        $query .= '`' .$columns .= '`=null, ';
        break;
        default:
        $query .= '`' .$columns . "`='" . $this->Slashes($value) . "', ";
        break;
      }
      }
      $query = substr($query, 0, -2) . ' WHERE ' . $parameters;
    }
    return $this->Query($query);
  } # end function  
} # end class

class SEO_URL{
  /**
  * $cache is the per page data array that contains all of the previously stripped titles
  * @var array
  */
  var $cache;
  /**
  * $languages_id contains the language_id for this instance
  * @var integer
  */
  var $languages_id;
  /**
  * $attributes array contains all the required settings for class
  * @var array
  */
  var $attributes;
  /**
  * $base_url is the NONSSL URL for site
  * @var string
  */
  var $base_url;
  /**
  * $base_url_ssl is the secure URL for the site
  * @var string
  */
  var $base_url_ssl;
  /**
  * $performance array contains evaluation metric data
  * @var array
  */
  var $performance;
  /**
  * $timestamp simply holds the temp variable for time calculations
  * @var float
  */
  var $timestamp;
  /**
  * $reg_anchors holds the anchors used by the .htaccess rewrites
  * @var array
  */
  var $reg_anchors;
  /**
  * $cache_query is the resource_id used for database cache logic
  * @var resource
  */
  var $cache_query;
  /**
  * $cache_file is the basename of the cache database entry
  * @var string
  */
  var $cache_file;
  /**
  * $data array contains all records retrieved from database cache
  * @var array
  */
  var $data;
  /**
  * $need_redirect determines whether the URL needs to be redirected
  * @var boolean
  */
  var $need_redirect;
  /**
  * $is_seopage holds value as to whether page is in allowed SEO pages
  * @var boolean
  */
  var $is_seopage;
  /**
  * $uri contains the $_SERVER['REQUEST_URI'] value
  * @var string
  */
  var $uri;
  /**
  * $real_uri contains the $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] value
  * @var string
  */
  var $real_uri;
  /**
  * $uri_parsed contains the parsed uri value array
  * @var array
  */
  var $uri_parsed;
  /**
  * $path_info contains the getenv('PATH_INFO') value
  * @var string
  */
  var $path_info;
  /**
  * $DB is the database object
  * @var object
  */
  var $DB;
  /**
  * $installer is the installer object
  * @var object
  */
  var $installer;
  
/**
 * SEO_URL class constructor 
 * @param integer $languages_id
 */ 
/*----------------------------
 功能：SEO_URL类的构造函数
 参数：$languages_id(string) 语言ID
 返回值：无
 ---------------------------*/
  function SEO_URL($languages_id){
      global $session_started, $SID;
    
    $this->DB = new SEO_DataBase(DB_SERVER, DB_SERVER_USERNAME, DB_DATABASE, DB_SERVER_PASSWORD);
    
    $this->languages_id = (int)$languages_id; 
    
    $this->data = array(); 
    
    $seo_pages = array(FILENAME_DEFAULT, 
                       FILENAME_PRODUCT_INFO, 
               FILENAME_POPUP_IMAGE,
               FILENAME_PAGE,
               FILENAME_REVIEWS,
               FILENAME_NEWS,
               FILENAME_PRODUCT_REVIEWS,
               FILENAME_PRODUCT_REVIEWS_INFO);
    if ( defined('FILENAME_ARTICLES') ) $seo_pages[] = FILENAME_ARTICLES;
    if ( defined('FILENAME_ARTICLE_INFO') ) $seo_pages[] = FILENAME_ARTICLE_INFO;
    if ( defined('FILENAME_INFORMATION') ) $seo_pages[] = FILENAME_INFORMATION;   
    
    $this->attributes = array('PHP_VERSION' => PHP_VERSION,
                              'SESSION_STARTED' => $session_started,
                  'SID' => $SID,
                  'SEO_ENABLED' => defined('SEO_ENABLED') ? SEO_ENABLED : 'false',
                  'SEO_ADD_CPATH_TO_PRODUCT_URLS' => defined('SEO_ADD_CPATH_TO_PRODUCT_URLS') ? SEO_ADD_CPATH_TO_PRODUCT_URLS : 'false',
                  'SEO_ADD_CAT_PARENT' => defined('SEO_ADD_CAT_PARENT') ? SEO_ADD_CAT_PARENT : 'true',
                  'USE_SEO_CACHE_GLOBAL' => defined('USE_SEO_CACHE_GLOBAL') ? USE_SEO_CACHE_GLOBAL : 'false',
                  'USE_SEO_CACHE_PRODUCTS' => defined('USE_SEO_CACHE_PRODUCTS') ? USE_SEO_CACHE_PRODUCTS : 'false',
                  'USE_SEO_CACHE_CATEGORIES' => defined('USE_SEO_CACHE_CATEGORIES') ? USE_SEO_CACHE_CATEGORIES : 'false',
                  'USE_SEO_CACHE_MANUFACTURERS' => defined('USE_SEO_CACHE_MANUFACTURERS') ? USE_SEO_CACHE_MANUFACTURERS : 'false',
                  'USE_SEO_CACHE_ARTICLES' => defined('USE_SEO_CACHE_ARTICLES') ? USE_SEO_CACHE_ARTICLES : 'false',
                  'USE_SEO_CACHE_TOPICS' => defined('USE_SEO_CACHE_TOPICS') ? USE_SEO_CACHE_TOPICS : 'false',
                  'USE_SEO_CACHE_INFO_PAGES' => defined('USE_SEO_CACHE_INFO_PAGES') ? USE_SEO_CACHE_INFO_PAGES : 'false',
                  'USE_SEO_REDIRECT' => defined('USE_SEO_REDIRECT') ? USE_SEO_REDIRECT : 'false',
                  'SEO_REWRITE_TYPE' => defined('SEO_REWRITE_TYPE') ? SEO_REWRITE_TYPE : 'false',
                  'SEO_URLS_FILTER_SHORT_WORDS' => defined('SEO_URLS_FILTER_SHORT_WORDS') ? SEO_URLS_FILTER_SHORT_WORDS : 'false',
                  'SEO_CHAR_CONVERT_SET' => defined('SEO_CHAR_CONVERT_SET') ? $this->expand(SEO_CHAR_CONVERT_SET) : 'false',
                  'SEO_REMOVE_ALL_SPEC_CHARS' => defined('SEO_REMOVE_ALL_SPEC_CHARS') ? SEO_REMOVE_ALL_SPEC_CHARS : 'false',
                  'SEO_PAGES' => $seo_pages,
                  );    
    
    $this->base_url = HTTP_SERVER . DIR_WS_CATALOG;
    $this->base_url_ssl = HTTPS_SERVER . DIR_WS_CATALOG;    
    $this->cache = array();
    $this->timestamp = 0;
    
    $this->reg_anchors = array('products_id' => 'p-',
                   'cPath' => 'c-',
                   'manufacturers_id' => 'm-',
                   'pID' => 'pi-',
                   'articles_id' => 'a-',
                   'products_id_review' => 'pr-',
                   'products_id_review_info' => 'pr-',
                   'info_id' => 'i-',
                   'colors' => 'co-',
                   'pID' => 'info-'
                   );
    
    $this->performance = array('NUMBER_URLS_GENERATED' => 0,
                   'NUMBER_QUERIES' => 0,                  
                   'CACHE_QUERY_SAVINGS' => 0,
                   'NUMBER_STANDARD_URLS_GENERATED' => 0,
                   'TOTAL_CACHED_PER_PAGE_RECORDS' => 0,
                   'TOTAL_TIME' => 0,
                   'TIME_PER_URL' => 0,
                   'QUERIES' => array()
                   );
    
    if ($this->attributes['USE_SEO_CACHE_GLOBAL'] == 'true'){
      $this->cache_file = 'seo_urls_v2_';
      $this->cache_gc();
      if ( $this->attributes['USE_SEO_CACHE_PRODUCTS'] == 'true' ) $this->generate_products_cache();
      if ( $this->attributes['USE_SEO_CACHE_CATEGORIES'] == 'true' ) $this->generate_categories_cache();
      if ( $this->attributes['USE_SEO_CACHE_MANUFACTURERS'] == 'true' ) $this->generate_manufacturers_cache();
      if ( $this->attributes['USE_SEO_CACHE_ARTICLES'] == 'true' && defined('TABLE_ARTICLES_DESCRIPTION')) $this->generate_articles_cache();
      if ( $this->attributes['USE_SEO_CACHE_TOPICS'] == 'true' && defined('TABLE_TOPICS_DESCRIPTION')) $this->generate_topics_cache();
      if ( $this->attributes['USE_SEO_CACHE_INFO_PAGES'] == 'true' && defined('TABLE_INFORMATION')) $this->generate_information_cache();
    } # end if

    if ($this->attributes['USE_SEO_REDIRECT'] == 'true'){
      $this->check_redirect();
    } # end if
  } # end constructor

/**
 * Function to return SEO URL link SEO'd with stock generattion for error fallback
 * @param string $page Base script for URL 
 * @param string $parameters URL parameters
 * @param string $connection NONSSL/SSL
 * @param boolean $add_session_id Switch to add sid
 * @return string Formed href link 
 */ 
/*----------------------------
 功能：错误回退SEO URL链接 
 参数：$page(string) 分页
 参数：$parameters(string) 自定义参数
 参数：$connection(string) NONSSL/ SSL
 参数：$add_session_id(string) 添加SID开关
 返回值：返回字符串形成href链接
 ---------------------------*/
  function href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true){
    global $request_type;
    $this->start($this->timestamp);
    $this->performance['NUMBER_URLS_GENERATED']++;
    if ( !in_array($page, $this->attributes['SEO_PAGES']) || $this->attributes['SEO_ENABLED'] == 'false' ) {
      return $this->stock_href_link($page, $parameters, $connection, $add_session_id);
    }
    if (defined('URL_SUB_SITE_ENABLED')) {
      if (URL_SUB_SITE_ENABLED) {
        $link = '';
      } else {
        $link = $connection == 'NONSSL' ? $this->base_url : $this->base_url_ssl;
      }
    } else {
      $link = $connection == 'NONSSL' ? $this->base_url : $this->base_url_ssl;
    }
    $separator = '?';
    if ($this->not_null($parameters)) { 
      $link .= $this->parse_parameters($page, $parameters, $separator); 
    } else {
      if ($page == FILENAME_NEWS) {
        $link .= 'news/';
      }else if ($page == FILENAME_REVIEWS) {
        $link .= 'reviews/';
      } else {
        $link .= $page;
      }
    }
  if(defined('URL_SUB_SITE_ENABLED') && URL_SUB_SITE_ENABLED){
    if (ENABLE_SSL) {
      if ($request_type == 'SSL') {
        $prelink = $connection == 'NONSSL' ? $this->base_url : '/';
      } else {
        $prelink = $connection == 'NONSSL' ? '/' : $this->base_url_ssl;
      }
    } else {
      $prelink = '/';
    }
    if(false===strpos($link,'http://')){
    $link = $prelink .$link;
    }
  }
    $link = $this->add_sid($link, $add_session_id, $connection, $separator); 
  
    // id 不显示 index.php
    if ($page == 'index.php' && $parameters == '' && !defined('NEW_TYPE_SYMBOL') && !isset($_sid)) {
      $link = HTTP_SERVER . DIR_WS_CATALOG;
    }
    
    $this->stop($this->timestamp, $time);
    $this->performance['TOTAL_TIME'] += $time;
    
    $urlString =  htmlspecialchars($link);
    $urlString = str_replace('&amp;', '&', $urlString);

    return $urlString;

  } # end function

/**
 * Stock function, fallback use 
 */ 
 
/*------------------------
 功能：备用功能
 参数：$page(string) 分页
 参数：$parameters(string) 自定义参数
 参数：$connection(string) NONSSL/ SSL
 参数：$add_session_id(string) 添加SID开关
 参数：$search_engine_safe(string) 安全开关
 返回值：链接(string) 
 -----------------------*/ 
  function stock_href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
    global $request_type, $session_started, $SID;
    if (!$this->not_null($page)) {
      forward404();
      exit;
    }
  if ($page == '/') $page = '';
    if ($connection == 'NONSSL') {
      $link = HTTP_SERVER . DIR_WS_CATALOG;
      if ($request_type == 'SSL') {
        $link = HTTP_SERVER . DIR_WS_CATALOG;
      } else {
        $link = DIR_WS_CATALOG;
      }
    } elseif ($connection == 'SSL') {
      if ($request_type == 'SSL') {
        $link = HTTPS_SERVER . DIR_WS_CATALOG;
      } else {
        if (ENABLE_SSL) {
          $link = HTTPS_SERVER . DIR_WS_CATALOG;
        } else {
          $link = HTTP_SERVER . DIR_WS_CATALOG;
        }
      }
    } else {
      forward404();
      exit;
    }
    if ($this->not_null($parameters)) {
      if ($page == FILENAME_TAGS) {
        $p = explode('=', $parameters);
        $link .= 'tags/page' . $p[1] . '.html';
      } else {
        $link .= $page . '?' . $this->output_string($parameters);
        $separator = '&';
      }
    } else {
      if ($page == FILENAME_NEWS) {
        $link .= 'news/';
      } else if ($page == FILENAME_REVIEWS) {
        $link .= 'reviews/';
      } else if ($page == FILENAME_TAGS) {
        $link .= 'tags/';
      } else {
        $link .= $page;
      }
      $separator = '?';
    }
    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);
    $session_started = true; 
    if ( ($add_session_id == true) && ($session_started == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
      if ($this->not_null($SID)) {
        if (SESSION_RECREATE == 'True') {
          $_sid = tep_session_name().'='.tep_session_id(); 
        } else {
          $_sid = $SID;
        }
      } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
        if (HTTP_COOKIE_DOMAIN != HTTPS_COOKIE_DOMAIN) {
          if (SESSION_RECREATE == 'True') {
            $_sid = tep_session_name().'='.tep_session_id(); 
          } else {
            $_sid = $this->SessionName() . '=' . $this->SessionID();
          }
        }
      }

    }
    if ( (SEARCH_ENGINE_FRIENDLY_URLS == 'true') && ($search_engine_safe == true) ) {
     //dont exchange '?, &, =' to '/' for redirect.php used in product_info.php
      if (!$page = FILENAME_REDIRECT) 
      {
      while (strstr($link, '&&')) $link = str_replace('&&', '&', $link);
      $link = str_replace('?', '/', $link);
      $link = str_replace('&', '/', $link);
      $link = str_replace('=', '/', $link);
      $separator = '?';
      }
    }
    if (isset($_sid)) {
      // 兼容同域名和不同域名的ssl跳转
        //cancel ssl to nossl session 
        if (!defined('NEW_TYPE_SYMBOL')) {
          if (($request_type == 'NONSSL' && $connection == 'SSL') || ($request_type == 'SSL' && tep_session_is_registered('customer_id'))){
          // id 特殊处理，未登录丢弃sid
            $link .= $separator . $_sid;
          }
        } else {
          if ($request_type == 'SSL' && $connection == 'SSL') {
            $link .= '';
          } else if ($request_type == 'NONSSL' && $connection == 'NONSSL') {
            $link .= '';
          } else {
            $link .= $separator . $_sid;
          }
        }
    }
  // id 不显示 index.php
  if ($page == 'index.php' && $parameters == '' && !defined('NEW_TYPE_SYMBOL') && !isset($_sid)) {
    $link = HTTP_SERVER . DIR_WS_CATALOG;
  }
  $this->performance['NUMBER_STANDARD_URLS_GENERATED']++;
  $this->cache['STANDARD_URLS'][] = $link;
  $time = 0;
  $this->stop($this->timestamp, $time);
  $this->performance['TOTAL_TIME'] += $time;
 
  //add new variable
  $link = str_replace('&amp;', '&', htmlspecialchars($link));
  return $link;

  } # end default tep_href function
  

/**
 * Function to append session ID if needed 
 * @param string $link 
 * @param boolean $add_session_id
 * @param string $connection
 * @param string $separator
 * @return string
 */ 

/*------------------------------
 功能：追加SID 
 参数：$link(string) 链接
 参数：$add_session_id(string) SID
 参数：$connection(string) 关联
 参数；$separator(string) 分离器
 返回值：链接(string) 
 -----------------------------*/
  function add_sid( $link, $add_session_id, $connection, $separator ){
    global $request_type; // global variable
    if ( ($add_session_id == true) && ($this->attributes['SESSION_STARTED'] == true) && (SESSION_FORCE_COOKIE_USE == 'False') ) {
      if ($this->not_null($SID)) {
        // add variable 
        if (SESSION_RECREATE == 'True') {
          $_sid = tep_session_name().'='.tep_session_id(); 
        } else {
          $_sid = $this->attributes['SID'];
        }
      } elseif ( ( ($request_type == 'NONSSL') && ($connection == 'SSL') && (ENABLE_SSL == true) ) || ( ($request_type == 'SSL') && ($connection == 'NONSSL') ) ) {
      if (HTTP_COOKIE_DOMAIN != HTTPS_COOKIE_DOMAIN) {
        if (SESSION_RECREATE == 'True') {
          $_sid = tep_session_name().'='.tep_session_id(); 
        } else {
          $_sid = $this->SessionName() . '=' . $this->SessionID();
        }
      }
      } 
    }
    
    if ( isset($_sid) ) {
      // 兼容同域名和不同域名的ssl跳转
        //cancel ssl to nossl session 
        if (!defined('NEW_TYPE_SYMBOL')) {
          if (($request_type == 'NONSSL' && $connection == 'SSL') || ($request_type == 'SSL' && tep_session_is_registered('customer_id'))) {
            // id 特殊处理，未登录丢弃sid
            return $link . $separator . $_sid;
          } else {
            return $link;
          }
        } else {
          if ($request_type == 'SSL' && $connection == 'SSL') {
            // 不同域名间ssl间跳转不加sid
            return $link; 
          } else if ($request_type == 'NONSSL' && $connection == 'NONSSL') {
            return $link; 
          } else {
            // 不同域名间ssl和非ssl互相跳转增加sid
            return $link . $separator . $_sid;
          }
        }
    } else {
      return $link; 
    }
  } # end function
  
/**
 * SFunction to parse the parameters into an SEO URL 
 * @param string $page
 * @param string $params
 * @param string $separator NOTE: passed by reference
 * @return string 
 */ 
/*---------------------------
 功能: 解析SEO URL的参数
 参数：$page(string) 分页
 参数：$params(string) 参数
 参数：$separator(string) 分离器
 返回值：URL(string) 
 --------------------------*/
  function parse_parameters($page, $params, &$separator){
    $p = @explode('&', $params);
    krsort($p);
    $container = array();
    foreach ($p as $index => $valuepair){
      $p2 = @explode('=', $valuepair); 
      if ($p2[0] == 'reviews_id') {
        $p3 = @explode('=', $p[0]);
        $url = $this->make_url($page, 'reviews/', 'products_id_review_info', $p3[1], '/'.$p2[1].'.html', $separator);
        break;
      } else if ($p2[0] == 'news_id') {
        $p3 = @explode('=', $p[0]);
        $url = $this->make_url($page, 'news/', 'news', $p3[1], '.html', $separator);
        break;
      } else if ($p2[0] == 'action' && $p2[1] == 'select'){
        $url = $this->make_url($page, '', $p2[0], $p2[1], '.html', $separator);
        break;
      } else {
      switch ($p2[0]){ 
        case 'products_id':
          switch(true){
            case ( $page == FILENAME_PRODUCT_INFO && !$this->is_attribute_string($p2[1]) ):
              $url = $this->make_url($page, REWRITE_PRODUCTS, $p2[0], $p2[1], '.html', $separator,URL_TYPE_PRODUCT);
              break;
            case ( $page == FILENAME_PRODUCT_REVIEWS ):
              //del URL_TYPE_PRODUCT 
              $url = $this->make_url($page, 'reviews/', 'products_id_review', $p2[1], '/', $separator);
              break;
            default:
              $container[$p2[0]] = $p2[1];
              break;
          } # end switch
          break;
        case 'cName': //add cName filter
          break;
        case 'page':
          switch(true){
            case ( $page == FILENAME_NEWS ):
              $url = $this->make_url($page, 'news/page', '', $p2[1], '.html', $separator);
              break;
            case ( $page == FILENAME_REVIEWS ):
              $url = $this->make_url($page, 'reviews/page', '', $p2[1], '.html', $separator);
              break;
            case ($page == FILENAME_DEFAULT && $_GET['cPath']):
            default:
              $container[$p2[0]] = $p2[1];
              break;
          }
          break;
        case 'cPath':
          switch(true){
            case ($page == FILENAME_DEFAULT):
              if (preg_match('/page=(\d+)/', $params, $out)) {
                $url = $this->make_url($page, REWRITE_CATEGORIES, $p2[0], $p2[1], '_page'.$out[1].'.html', $separator,'cpath');
                unset($container['page']);
              } else {
                $url = $this->make_url($page, REWRITE_CATEGORIES, $p2[0], $p2[1], '.html', $separator,'cpath');
              }
              break;
            case ( !$this->is_product_string($params) ):
              if ( $this->attributes['SEO_ADD_CPATH_TO_PRODUCT_URLS'] == 'true' ){
                $container[$p2[0]] = $p2[1];
              }
              break;
            default:
              $container[$p2[0]] = $p2[1];
              break;
            } # end switch
          break;
        case 'manufacturers_id':
          switch(true){
            case ($page == FILENAME_DEFAULT && !$this->is_cPath_string($params) && !$this->is_product_string($params) ):
              $url = $this->make_url($page, REWRITE_MANUFACTURERES, $p2[0], $p2[1], '.html', $separator);
              break;
            case (!$this->is_product_string($params)):
            default:
              $container[$p2[0]] = $p2[1];
              break;          
            } # end switch
          break;
        case 'pID':
          switch(true){
            case ($page == FILENAME_POPUP_IMAGE):
            $url = $this->make_url($page, REWRITE_PRODUCTS, $p2[0], $p2[1], '.html', $separator);
            break;
              // Contents page
              case ($page == FILENAME_PAGE):
            $url = $this->make_url($page, '', $p2[0], $p2[1], '.html', $separator);
            break;
          default:
            $container[$p2[0]] = $p2[1];
            break;
          } # end switch
          break;
        // 添加
        case 'colors':
          $url = $this->make_url($page, REWRITE_PRODUCTS, $p2[0], $p2[1], '.html', $separator);
          break;
        case 'tags_id':
          if (preg_match('/page=(\d+)/', $params, $out)) {
            $url = $this->make_url($page, 'tags/t-', $p2[0], $p2[1].'_page'.$out[1], '.html', $separator);
            unset($container['page']);
          } else {
            $url = $this->make_url($page, 'tags/t-', $p2[0], $p2[1], '.html', $separator);
          }
          break;
        default:
          $container[$p2[0]] = isset($p2[1]) ? $p2[1] : ''; 
          break;
      } # end switch
     }
    } # end foreach $p
    $url = isset($url) ? $url : $page;
    if ( sizeof($container) > 0 ){
      if ( $imploded_params = $this->implode_assoc($container) ){
        $url .= $separator . $this->output_string( $imploded_params );
        $separator = '&';
      }
    }
    return $url;
  } # end function

/**
 * Function to return the generated SEO URL  
 * @param string $page
 * @param string $string Stripped, formed anchor
 * @param string $anchor_type Parameter type (products_id, cPath, etc.)
 * @param integer $id
 * @param string $extension Default = .html
 * @param string $separator NOTE: passed by reference
 * @return string
 */
/*----------------------------
 功能：生成的搜索引擎优化的URL
 参数：$page(string) 分页
 参数：$string(string) 字符串
 参数：$anchor_type(string) 参数类型
 参数：$id(string) ID
 参数：$extension(string) Default = .html
 参数：$separator(string) 通过引用传递
 参数：$urlType(string) URL类型
 返回值：返回字符串(string) `
 ---------------------------*/
      function make_url($page, $string, $anchor_type, $id, $extension = '.html', &$separator,$urlType=null){
    // Right now there is but one rewrite method since cName was dropped
    // In the future there will be additional methods here in the switch
    if((defined('URL_SUB_SITE_ENABLED') && URL_SUB_SITE_ENABLED) || (defined('URL_ROMAJI_ENABLED') && URL_ROMAJI_ENABLED)){
      switch($urlType){
      case 'cpath': 
      case URL_TYPE_CPATH:
        $id_array = explode("_",$id);
        $id= $id_array[0];
        $romaji = tep_get_romaji_cpath($id); 
        $romajiSub =array();
        unset($id_array[0]);

        if (count($id_array))//如果有多个id 则说明....
          {
            foreach ($id_array as $category_id){
            $romajiSub[] = urlencode(tep_get_romaji_cpath($category_id));
            }
          }
        if (defined('URL_SUB_SITE_ENABLED') && URL_SUB_SITE_ENABLED) {
           $string = 'http://'.urlencode($romaji).'.'.URL_SUB_SITE.'/'.join('/',$romajiSub);
        } else {
           $string = urlencode($romaji).'/'.join('/',$romajiSub);
        }
        if(preg_match("/^_page(\d+)/",$extension,$pagenumber)){
          $string .='/page-'.$pagenumber[1].'.html';
        } else {
          if ($romajiSub) {
            $string .='/';
          }
        }
        return $string;
        break;
      case URL_TYPE_PRODUCT:
        $categories = tep_get_categories_by_pid($id);
        $mainID = $categories[0];
        $romaji = $mainID;
        unset($categories[0]);
        $categoriesToString ='';
        if(count($categories)){
          foreach($categories as $k => $v) {
            $categories[$k] = urlencode($v);
          }
          $categoriesToString = @join('/',$categories).'/';
        }
        $productRomaji = urlencode(tep_get_romaji_by_pid($id));
        if (defined('URL_SUB_SITE_ENABLED') && URL_SUB_SITE_ENABLED) {
          return $string = 'http://'.urlencode($romaji).'.'.URL_SUB_SITE.'/'.$categoriesToString.$productRomaji.'.html';
        } else {
          return $string = urlencode($romaji).'/'.$categoriesToString.$productRomaji.'.html';
        }
      }
    }


    switch ( $this->attributes['SEO_REWRITE_TYPE'] ){
      case 'Rewrite':
        return $string . $this->reg_anchors[$anchor_type] . $id . $extension;
        break;
      default:
        break;
    } # end switch
  } # end function

/**
 * Function to get the product name. Use evaluated cache, per page cache, or database query in that order of precedent  
 * @param integer $pID
 * @return string Stripped anchor text
 */ 
/*------------------------
 功能：获取产品名称
 参数：$pID(string) 产品ID值
 返回值：返回字符串(string)
 -----------------------*/
  function get_product_name($pID){
    switch(true){
      case ($this->attributes['USE_SEO_CACHE_GLOBAL'] == 'true' && defined('PRODUCT_NAME_' . $pID)):
        $this->performance['CACHE_QUERY_SAVINGS']++;
        $return = constant('PRODUCT_NAME_' . $pID);
        $this->cache['PRODUCTS'][$pID] = $return;
        break;
      case ($this->attributes['USE_SEO_CACHE_GLOBAL'] == 'true' && isset($this->cache['PRODUCTS'][$pID])):
        $this->performance['CACHE_QUERY_SAVINGS']++;
        $return = $this->cache['PRODUCTS'][$pID];
        break;
      default:
        $this->performance['NUMBER_QUERIES']++;
        $sql = "SELECT products_name as pName 
            FROM ".TABLE_PRODUCTS_DESCRIPTION." 
            WHERE products_id='".(int)$pID."' 
            AND language_id='".(int)$this->languages_id."' 
            AND (site_id = '".SITE_ID."' or site_id = '0')
            ORDER by site_id DESC
            LIMIT 1";
        $result = $this->DB->FetchArray( $this->DB->Query( $sql ) );
        $pName = $this->strip( $result['pName'] );
        $this->cache['PRODUCTS'][$pID] = $pName;
        $this->performance['QUERIES']['PRODUCTS'][] = $sql;
        $return = $pName;
        break;                
    } # end switch    
    return $return;
  } # end function
  
/**
 * Function to get the category name. Use evaluated cache, per page cache, or database query in that order of precedent 
 * @param integer $cID NOTE: passed by reference
 * @return string Stripped anchor text
 */ 
/*------------------------
 功能：获取功能类别名称
 参数：$cID(string) ID值
 返回值：返回名称(string) 
 -----------------------*/
  function get_category_name(&$cID){
    $full_cPath = $this->get_full_cPath($cID, $single_cID); // full cPath needed for uniformity
    switch(true){
      case ($this->attributes['USE_SEO_CACHE_GLOBAL'] == 'true' && defined('CATEGORY_NAME_' . $full_cPath)):
        $this->performance['CACHE_QUERY_SAVINGS']++;
        $return = constant('CATEGORY_NAME_' . $full_cPath);
        $this->cache['CATEGORIES'][$full_cPath] = $return;
        break;
      case ($this->attributes['USE_SEO_CACHE_GLOBAL'] == 'true' && isset($this->cache['CATEGORIES'][$full_cPath])):
        $this->performance['CACHE_QUERY_SAVINGS']++;
        $return = $this->cache['CATEGORIES'][$full_cPath];
        break;
      default:
        $this->performance['NUMBER_QUERIES']++;
        switch(true){
          case ($this->attributes['SEO_ADD_CAT_PARENT'] == 'true'):
            $sql = "
             SELECT c.categories_id, 
                    c.parent_id, 
                    cd.categories_name as cName, 
                    cd2.categories_name as pName  
            FROM ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd LEFT JOIN ".TABLE_CATEGORIES_DESCRIPTION." cd2 ON c.parent_id=cd2.categories_id AND cd2.language_id='".(int)$this->languages_id."' 
            WHERE c.categories_id='".(int)$single_cID."' 
              AND cd.categories_id='".(int)$single_cID."' 
              AND cd.language_id='".(int)$this->languages_id."' 
              AND (cd.site_id='".SITE_ID."' or cd.site_id = '0')
              AND (cd2.site_id='".SITE_ID."' or cd2.site_id = '0')
              ORDER BY cd.site_id DESC, cd2.site_id DESC
              LIMIT 1";
            $result = $this->DB->FetchArray( $this->DB->Query( $sql ) );
            $cName = $this->not_null($result['pName']) ? $result['pName'] . ' ' . $result['cName'] : $result['cName'];
            break;
          default:
            $sql = "
                SELECT categories_name as cName 
                FROM ".TABLE_CATEGORIES_DESCRIPTION." 
                WHERE categories_id='".(int)$single_cID."' 
                  AND language_id='".(int)$this->languages_id."' 
                  and (site_id = '".SITE_ID."' or site_id = '0')
                ORDER BY site_id DESC
                LIMIT 1";
            $result = $this->DB->FetchArray( $this->DB->Query( $sql ) );
            $cName = $result['cName'];
            break;
        }                   
        $cName = $this->strip($cName);
        $this->cache['CATEGORIES'][$full_cPath] = $cName;
        $this->performance['QUERIES']['CATEGORIES'][] = $sql;
        $return = $cName;
        break;                
    } # end switch    
    $cID = $full_cPath;
    return $return;
  } # end function

/**
 * Function to get the manufacturer name. Use evaluated cache, per page cache, or database query in that order of precedent.
 * @param integer $mID
 * @return string
 */ 
/*------------------------------
 功能：获取制造商名称
 参数：$mID(string) ID值
 返回值：返回名称(string) 
 -----------------------------*/
  function get_manufacturer_name($mID){
    switch(true){
      case ($this->attributes['USE_SEO_CACHE_GLOBAL'] == 'true' && defined('MANUFACTURER_NAME_' . $mID)):
        $this->performance['CACHE_QUERY_SAVINGS']++;
        $return = constant('MANUFACTURER_NAME_' . $mID);
        $this->cache['MANUFACTURERS'][$mID] = $return;
        break;
      case ($this->attributes['USE_SEO_CACHE_GLOBAL'] == 'true' && isset($this->cache['MANUFACTURERS'][$mID])):
        $this->performance['CACHE_QUERY_SAVINGS']++;
        $return = $this->cache['MANUFACTURERS'][$mID];
        break;
      default:
        $this->performance['NUMBER_QUERIES']++;
        $sql = "SELECT manufacturers_name as mName 
            FROM ".TABLE_MANUFACTURERS." 
            WHERE manufacturers_id='".(int)$mID."' 
            LIMIT 1";
        $result = $this->DB->FetchArray( $this->DB->Query( $sql ) );
        $mName = $this->strip( $result['mName'] );
        $this->cache['MANUFACTURERS'][$mID] = $mName;
        $this->performance['QUERIES']['MANUFACTURERS'][] = $sql;
        $return = $mName;
        break;
    } # end switch    
    return $return;
  } # end function


/**
 * Function to retrieve full cPath from category ID 
 * @param mixed $cID Could contain cPath or single category_id
 * @param integer $original Single category_id passed back by reference
 * @return string Full cPath string
 */ 
/*------------------------
 功能：类别ID检索 
 参数：$cID(string) ID值
 参数：$original(string) 原ID
 返回值：ID值(string) 
 -----------------------*/
  function get_full_cPath($cID, &$original){
    if ( is_numeric(strpos($cID, '_')) ){
      $temp = @explode('_', $cID);
      $original = $temp[sizeof($temp)-1];
      return $cID;
    } else {
      $c = array();
      $this->GetParentCategories($c, $cID);
      $c = array_reverse($c);
      $c[] = $cID;
      $original = $cID;
      $cID = sizeof($c) > 1 ? implode('_', $c) : $cID;
      return $cID;
    }
  } # end function

/**
 * Recursion function to retrieve parent categories from category ID 
 * @param mixed $categories Passed by reference
 * @param integer $categories_id
 */ 
/*-----------------------------
 功能：检索父类ID 
 参数：$categories(string) 通过引用传递值
 参数：$categories_id(string) categories_id值
 返回值：无
 ----------------------------*/
  function GetParentCategories(&$categories, $categories_id) {
    $sql = "SELECT parent_id 
            FROM " . TABLE_CATEGORIES . " 
        WHERE categories_id='" . (int)$categories_id . "'";
    $parent_categories_query = $this->DB->Query($sql);
    while ($parent_categories = $this->DB->FetchArray($parent_categories_query)) {
      if ($parent_categories['parent_id'] == 0) return true;
      $categories[sizeof($categories)] = $parent_categories['parent_id'];
      if ($parent_categories['parent_id'] != $categories_id) {
        $this->GetParentCategories($categories, $parent_categories['parent_id']);
      }
    }
  } # end function

/**
 * Function to check if a value is NULL 
 * @param mixed $value
 * @return boolean
 */ 
/*--------------------
 功能：检查是否为空
 参数：$value(string) 参数值
 返回值：判断是否为空(boolean)
 -------------------*/
  function not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  } # end function

/**
 * Function to check if the products_id contains an attribute 
 * @param integer $pID
 * @return boolean
 */ 
/*------------------------
 功能：检查的products_id包含一个属性
 参数：$pID(string) 产品ID
 返回值：判断是否成功(string) 
 -----------------------*/
  function is_attribute_string($pID){
    if ( is_numeric(strpos($pID, '{')) ){
      return true;
    } else {
      return false;
    }
  } # end function

/**
 * Function to check if the params contains a products_id 
 * @param string $params
 * @return boolean
 */ 
/*----------------------
 功能：检查参数中包含products_id
 参数：$params(string) 参数
 返回值：判断是否包含(boolean)
 ---------------------*/
  function is_product_string($params){
    if ( is_numeric(strpos('products_id', $params)) ){
      return true;
    } else {
      return false;
    }
  } # end function

/**
 * Function to check if cPath is in the parameter string  
 * @param string $params
 * @return boolean
 */ 
/*----------------------
 功能：检查参数字符串
 参数：$params(string) 参数
 返回值：判断是否检查成功(boolean)
 ---------------------*/
  function is_cPath_string($params){
    if ( eregi('cPath', $params) ){
      return true;
    } else {
      return false;
    }
  } # end function

/**
 * Function used to output class profile
 */ 
/*----------------------
 功能：输出类配置功能
 参数：无
 返回值：无
 ---------------------*/
  function profile(){
    $this->calculate_performance();
    $this->PrintArray($this->attributes, 'Class Attributes');
    $this->PrintArray($this->cache, 'Cached Data');
  } # end function

/**
 * Function used to calculate and output the performance metrics of the class
 * @return mixed Output of performance data wrapped in HTML pre tags
 */ 
/*---------------------
 功能：计算和输出的类标签
 参数：无
 返回值：输出标签(string) 
 --------------------*/
  function calculate_performance(){
    foreach ($this->cache as $type){
      $this->performance['TOTAL_CACHED_PER_PAGE_RECORDS'] += sizeof($type);     
    }
    $this->performance['TIME_PER_URL'] = $this->performance['TOTAL_TIME'] / $this->performance['NUMBER_URLS_GENERATED'];
    return $this->PrintArray($this->performance, 'Performance Data');
  } # end function
  
/**
 * Function to strip the string of punctuation and white space 
 * @param string $string
 * @return string Stripped text. Removes all non-alphanumeric characters.
 */ 
/*--------------------
 功能：去除标点符号和空格的字符串
 参数：$string(string) 字符串
 返回值：字符串(string)
 -------------------*/
  function strip($string){
    $pattern = $this->attributes['SEO_REMOVE_ALL_SPEC_CHARS'] == 'true'
            ? "([^[:alnum:]])+"
            : "([[:punct:]])+";
    $anchor = ereg_replace($pattern, '', strtolower($string));
    $pattern = "([[:space:]]|[[:blank:]])+"; 
    $anchor = ereg_replace($pattern, '-', $anchor);
    if ( is_array($this->attributes['SEO_CHAR_CONVERT_SET']) ) $anchor = strtr($anchor, $this->attributes['SEO_CHAR_CONVERT_SET']);
    return $this->short_name($anchor); // return the short filtered name 
  } # end function

/**
 * Function to expand the SEO_CONVERT_SET group 
 * @param string $set
 * @return mixed
 */ 
/*-------------------
 功能：功能扩大的SEO_CONVERT_SET组
 参数：$set(string) 组
 返回值：判断是否返回组(boolean)
 ------------------*/
  function expand($set){
    if ( $this->not_null($set) ){
      if ( $data = @explode(',', $set) ){
        foreach ( $data as $index => $valuepair){
          $p = @explode('=>', $valuepair);
          $container[trim($p[0])] = trim($p[1]);
        }
        return $container;
      } else {
        return 'false';
      }
    } else {
      return 'false';
    }
  } # end function
/**
 * Function to return the short word filtered string 
 * @param string $str
 * @param integer $limit
 * @return string Short word filtered
 */ 
/*----------------------
 功能：过滤的字符串
 参数：$str(string) 字符串
 参数：$limit(string) 参数整数
 返回值：字符串名称过滤(string) 
 ---------------------*/
  function short_name($str, $limit=3){
    $container = array();
    if ( $this->attributes['SEO_URLS_FILTER_SHORT_WORDS'] != 'false' ) $limit = (int)$this->attributes['SEO_URLS_FILTER_SHORT_WORDS'];
    $foo = @explode('-', $str);
    foreach($foo as $index => $value){
      switch (true){
        case ( strlen($value) <= $limit ):
          continue;
        default:
          $container[] = $value;
          break;
      }   
    } # end foreach
    $container = ( sizeof($container) > 1 ? implode('-', $container) : $str );
    return $container;
  }
  
/**
 * Function to implode an associative array 
 * @param array $array Associative data array
 * @param string $inner_glue
 * @param string $outer_glue
 * @return string
 */ 
/*--------------------------
 功能：关联数组
 参数：$array(array) 数组
 参数：$inner_glue(string) 等于
 参数：outer_glue(string) 并且
 返回值：关联数组(string) 
 -------------------------*/
  function implode_assoc($array, $inner_glue='=', $outer_glue='&') {
    $output = array();
    foreach( $array as $key => $item ){
      if ( $this->not_null($key) && $this->not_null($item) ){
        $output[] = $key . $inner_glue . $item;
      }
    } # end foreach 
    return @implode($outer_glue, $output);
  }

/**
 * Function to start time for performance metric 
 * @param float $start_time
 */ 
/*---------------------
 功能：开始时间  
 参数: $start_time(string) 开始时间
 返回值：无
 --------------------*/
  function start(&$start_time){
    $start_time = explode(' ', microtime());
  }
  
/**
 * Function to stop time for performance metric 
 * @param float $start
 * @param float $time NOTE: passed by reference
 */ 
/*----------------------------
 功能：停止时间
 参数：$start(string) 开始
 参数：$time(string) 时间
 返回值：无
 ---------------------------*/
  function stop($start, &$time){
    $end = explode(' ', microtime());
    $time = number_format( array_sum($end) - array_sum($start), 8, '.', '' );
  }

/**
 * Function to translate a string 
 * @param string $data String to be translated
 * @param array $parse Array of tarnslation variables
 * @return string
 */ 
/*--------------------------- 
 功能：字符串转换
 参数：$data(string) 字符串数据
 参数：$parse(string) 变量数组
 返回值：返回字符串(string) 
 --------------------------*/
  function parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
  }
  
/**
 * Function to output a translated or sanitized string 
 * @param string $sting String to be output
 * @param mixed $translate Array of translation characters
 * @param boolean $protected Switch for htemlspecialchars processing
 * @return string
 */ 
/*--------------------------
 功能：输出翻译或者消除字符串
 参数：$string(string) 字符串
 参数：$translate(string) 翻译字符串数组
 参数：$protected(string) 开关
 返回值：返回字符串(string) 
 -------------------------*/
  function output_string($string, $translate = false, $protected = false) {
    if ($protected == true) {
      return htmlspecialchars($string);
    } else {
      if ($translate == false) {
      return $this->parse_input_field_data($string, array('"' => '&quot;'));
      } else {
      return $this->parse_input_field_data($string, $translate);
      }
    }
  }

/**
 * Function to return the session ID 
 * @param string $sessid
 * @return string
 */ 
/*--------------------
 功能：SESSION ID
 参数：$sessid(string) SESSION ID
 返回值：SESSION ID(string)
 -------------------*/
  function SessionID($sessid = '') {
    if (!empty($sessid)) {
      return tep_session_id($sessid);
    } else {
      return tep_session_id();
    }
  }
  
/**
 * Function to return the session name 
 * @param string $name
 * @return string
 */ 
/*------------------
 功能：SESSION 名称
 参数: $name(string) 名称
 返回值：SESSION 名称
 -----------------*/
  function SessionName($name = '') {
    if (!empty($name)) {
      return tep_session_name($name);
    } else {
      return tep_session_name();
    }
  }

/**
 * Function to generate products cache entries 
 */ 
/*-----------------------------
 功能：生成产品缓存目录 
 参数：无
 返回值：无
 ----------------------------*/
  function generate_products_cache(){
    $this->is_cached($this->cache_file . 'products', $is_cached, $is_expired);    
    if ( !$is_cached || $is_expired ) {
    $sql = "
      select *
      from (
        SELECT pd.product_status, p.products_id as id, pd.products_name as name , pd.site_id
        FROM ".TABLE_PRODUCTS." p 
          LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd 
          ON p.products_id=pd.products_id 
          AND pd.language_id='".(int)$this->languages_id."' 
        ORDER BY pd.site_id DESC
      ) p
      where site_id = '0'
         or site_id = '".SITE_ID."'
      group by id
      having p.products_status != '0' and p.products_status != '3'  
      ";
    $product_query = $this->DB->Query( $sql );
    $prod_cache = '';
    while ($product = $this->DB->FetchArray($product_query)) {
      $define = 'define(\'PRODUCT_NAME_' . $product['id'] . '\', \'' . $this->strip($product['name']) . '\');';
      $prod_cache .= $define . "\n";
      eval("$define");
    }
    $this->DB->Free($product_query);
    $this->save_cache($this->cache_file . 'products', $prod_cache, 'EVAL', 1 , 1);
    unset($prod_cache);
    } else {
      $this->get_cache($this->cache_file . 'products');   
    }
  } # end function
    
/**
 * Function to generate manufacturers cache entries 
 */ 
/*--------------------------
 功能：生成制造商缓存目录 
 参数：无
 返回值：无
 -------------------------*/
  function generate_manufacturers_cache(){
    $this->is_cached($this->cache_file . 'manufacturers', $is_cached, $is_expired);   
    if ( !$is_cached || $is_expired ) { // it's not cached so create it
    $sql = "SELECT m.manufacturers_id as id, m.manufacturers_name as name 
            FROM ".TABLE_MANUFACTURERS." m 
        LEFT JOIN ".TABLE_MANUFACTURERS_INFO." md 
        ON m.manufacturers_id=md.manufacturers_id 
        AND md.languages_id='".(int)$this->languages_id."'";
    $manufacturers_query = $this->DB->Query( $sql );
    $man_cache = '';
    while ($manufacturer = $this->DB->FetchArray($manufacturers_query)) {
      $define = 'define(\'MANUFACTURER_NAME_' . $manufacturer['id'] . '\', \'' . $this->strip($manufacturer['name']) . '\');';
      $man_cache .= $define . "\n";
      eval("$define");
    }
    $this->DB->Free($manufacturers_query);
    $this->save_cache($this->cache_file . 'manufacturers', $man_cache, 'EVAL', 1 , 1);
    unset($man_cache);
    } else {
      $this->get_cache($this->cache_file . 'manufacturers');    
    }
  } # end function

/**
 * Function to generate categories cache entries 
 */ 
/*--------------------------------
 功能：生成类缓存目录
 参数：无
 返回值：无
 -------------------------------*/
  function generate_categories_cache(){
    $this->is_cached($this->cache_file . 'categories', $is_cached, $is_expired);    
    if ( !$is_cached || $is_expired ) { // it's not cached so create it
      switch(true){
        case ($this->attributes['SEO_ADD_CAT_PARENT'] == 'true'):
          $sql = "
           select *
           from * (
              SELECT c.categories_id as id, 
                     c.parent_id, 
                     cd.categories_name as cName, 
                     cd2.categories_name as pName,
                     cd.site_id as site_id,
                     cd2.site_id as site_id2
              FROM ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd LEFT JOIN ".TABLE_CATEGORIES_DESCRIPTION." cd2 ON c.parent_id=cd2.categories_id AND cd2.language_id='".(int)$this->languages_id."' 
              WHERE c.categories_id=cd.categories_id 
                AND cd.language_id='".(int)$this->languages_id."'
              ORDER by cd.site_id DESC, cd2.site_id DESC
            ) c 
            WHERE (site_id='".SITE_ID."' or site_id = '0')
              AND (site_id2='".SITE_ID."' or site_id2= '0')
            GROUP BY id
            ";
          break;
        default:
          $sql = "
            select * 
            from (
              SELECT categories_id as id, categories_name as cName, site_id
              FROM ".TABLE_CATEGORIES_DESCRIPTION."  
              WHERE language_id='".(int)$this->languages_id."'
              ORDER BY site_id DESC
            ) c
            where site_id = '0'
              AND site_id='".SITE_ID."'
            GROUP BY id
              ";
          break;
      } # end switch
    $category_query = $this->DB->Query( $sql );
    $cat_cache = '';
    while ($category = $this->DB->FetchArray($category_query)) {  
      $id = $this->get_full_cPath($category['id'], $single_cID);
      $name = $this->not_null($category['pName']) ? $category['pName'] . ' ' . $category['cName'] : $category['cName']; 
      $define = 'define(\'CATEGORY_NAME_' . $id . '\', \'' . $this->strip($name) . '\');';
      $cat_cache .= $define . "\n";
      eval("$define");
    }
    $this->DB->Free($category_query);
    $this->save_cache($this->cache_file . 'categories', $cat_cache, 'EVAL', 1 , 1);
    unset($cat_cache);
    } else {
      $this->get_cache($this->cache_file . 'categories');   
    }
  } # end function

/**
 * Function to save the cache to database 
 * @param string $name Cache name
 * @param mixed $value Can be array, string, PHP code, or just about anything
 * @param string $method RETURN, ARRAY, EVAL
 * @param integer $gzip Enables compression
 * @param integer $global Sets whether cache record is global is scope
 * @param string $expires Sets the expiration
 */ 
/*--------------------------
 功能：生成缓存保存到数据库
 参数：$name(string) 缓存名称
 参数：$value(string) 缓存值
 参数：$method(string) 方法
 参数：$gzip(string) Gzip已启用压缩
 参数：$global(string) 全局缓存记录
 参数：$expires(string) 过期设置
 返回值：无
 --------------------------*/
  function save_cache($name, $value, $method='RETURN', $gzip=1, $global=0, $expires = '30/days'){
    $expires = $this->convert_time($expires);   
    if ($method == 'ARRAY' ) $value = serialize($value);
    $value = ( $gzip === 1 ? base64_encode(gzdeflate($value, 1)) : addslashes($value) );
    $sql_data_array = array('cache_id' => md5($name),
                'cache_language_id' => (int)$this->languages_id,
                'cache_name' => $name,
                'cache_data' => $value,
                'cache_global' => (int)$global,
                'cache_gzip' => (int)$gzip,
                'cache_method' => $method,
                'cache_date' => date("Y-m-d H:i:s"),
                'cache_expires' => $expires
                );                
    $this->is_cached($name, $is_cached, $is_expired);
    $cache_check = ( $is_cached ? 'true' : 'false' );
    switch ( $cache_check ) {
      case 'true': 
        $this->DB->DBPerform('cache', $sql_data_array, 'update', "cache_id='".md5($name)."'");
        break;        
      case 'false':
        $this->DB->DBPerform('cache', $sql_data_array, 'insert');
        break;        
      default:
        break;
    } # end switch ($cache check)   
    # unset the variables...clean as we go
    unset($value, $expires, $sql_data_array);   
  }# end function save_cache()
  
/**
 * Function to get cache entry 
 * @param string $name
 * @param boolean $local_memory
 * @return mixed
 */ 
/*-------------------------
 功能：获取缓存
 参数：$name(string) 名称
 参数：$local_memory(boolean) 本地内存
 返回值：获取成功(boolean) 
 ------------------------*/
  function get_cache($name = 'GLOBAL', $local_memory = false){
    $select_list = 'cache_id, cache_language_id, cache_name, cache_data, cache_global, cache_gzip, cache_method, cache_date, cache_expires';
    $global = ( $name == 'GLOBAL' ? true : false ); // was GLOBAL passed or is using the default?
    switch($name){
      case 'GLOBAL': 
        $this->cache_query = $this->DB->Query("SELECT ".$select_list." FROM cache WHERE cache_language_id='".(int)$this->languages_id."' AND cache_global='1'");
        break;
      default: 
        $this->cache_query = $this->DB->Query("SELECT ".$select_list." FROM cache WHERE cache_id='".md5($name)."' AND cache_language_id='".(int)$this->languages_id."'");
        break;
    } # end switch ($name)
    $num_rows = $this->DB->NumRows($this->cache_query);
    if ( $num_rows ){ 
      $container = array();
      while($cache = $this->DB->FetchArray($this->cache_query)){
        $cache_name = $cache['cache_name']; 
        if ( $cache['cache_expires'] > date("Y-m-d H:i:s") ) { 
          $cache_data = ( $cache['cache_gzip'] == 1 ? gzinflate(base64_decode($cache['cache_data'])) : stripslashes($cache['cache_data']) );
          switch($cache['cache_method']){
            case 'EVAL': // must be PHP code
              eval("$cache_data");
              break;              
            case 'ARRAY': 
              $cache_data = unserialize($cache_data);             
            case 'RETURN': 
            default:
              break;
          } # end switch ($cache['cache_method'])         
          if ($global) $container['GLOBAL'][$cache_name] = $cache_data; 
          else $container[$cache_name] = $cache_data; // not global       
        } else { // cache is expired
          if ($global) $container['GLOBAL'][$cache_name] = false; 
          else $container[$cache_name] = false; 
        }# end if ( $cache['cache_expires'] > date("Y-m-d H:i:s") )     
        if ($local_memory ) {
          if ($global) $this->data['GLOBAL'][$cache_name] = $container['GLOBAL'][$cache_name]; 
          else $this->data[$cache_name] = $container[$cache_name]; 
        }             
      } # end while ($cache = $this->DB->FetchArray($this->cache_query))      
      unset($cache_data);
      $this->DB->Free($this->cache_query);      
      switch (true) {
        case ($num_rows == 1): 
          if ($global){
            if ($container['GLOBAL'][$cache_name] == false || !isset($container['GLOBAL'][$cache_name])) return false;
            else return $container['GLOBAL'][$cache_name]; 
          } else { // not global
            if ($container[$cache_name] == false || !isset($container[$cache_name])) return false;
            else return $container[$cache_name];
          } # end if ($global)          
        case ($num_rows > 1): 
        default: 
          return $container; 
          break;
      }# end switch (true)      
    } else { 
      return false;
    }# end if ( $num_rows )   
  } # end function get_cache()

/**
 * Function to get cache from memory
 * @param string $name
 * @param string $method
 * @return mixed
 */ 
/*--------------------------
 功能：获取内存中的缓存 
 参数：$name(string) 名称
 参数：$method(string) 方法
 返回值：是否获取成功(boolean)
 -------------------------*/
  function get_cache_memory($name, $method = 'RETURN'){
    $data = ( isset($this->data['GLOBAL'][$name]) ? $this->data['GLOBAL'][$name] : $this->data[$name] );
    if ( isset($data) && !empty($data) && $data != false ){ 
      switch($method){
        case 'EVAL': // data must be PHP
          eval("$data");
          return true;
          break;
        case 'ARRAY': 
        case 'RETURN':
        default:
          return $data;
          break;
      } # end switch ($method)
    } else { 
      return false;
    } # end if (isset($data) && !empty($data) && $data != false)
  } # end function get_cache_memory()

/**
 * Function to perform basic garbage collection for database cache system 
 */ 
/*------------------------
 功能：删除数据库缓存
 参数：无
 返回值：无
 -----------------------*/
  function cache_gc(){
          $this->DB->Query("DELETE FROM cache WHERE cache_expires <= '" . date("Y-m-d H:i:s") . "'" );
  }

/**
 * Function to convert time for cache methods 
 * @param string $expires
 * @return string
 */ 
/*-----------------------
 功能：转换时间为缓存方法
 参数：$expires(string) 参数字符串
 返回值：字符串(string) 
 ----------------------*/
  function convert_time($expires){ //expires date interval must be spelled out and NOT abbreviated !!
    $expires = explode('/', $expires);
    switch( strtolower($expires[1]) ){ 
      case 'seconds':
        $expires = mktime( date("H"), date("i"), date("s")+(int)$expires[0], date("m"), date("d"), date("Y") );
        break;
      case 'minutes':
        $expires = mktime( date("H"), date("i")+(int)$expires[0], date("s"), date("m"), date("d"), date("Y") );
        break;
      case 'hours':
        $expires = mktime( date("H")+(int)$expires[0], date("i"), date("s"), date("m"), date("d"), date("Y") );
        break;
      case 'days':
        $expires = mktime( date("H"), date("i"), date("s"), date("m"), date("d")+(int)$expires[0], date("Y") );
        break;
      case 'months':
        $expires = mktime( date("H"), date("i"), date("s"), date("m")+(int)$expires[0], date("d"), date("Y") );
        break;
      case 'years':
        $expires = mktime( date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")+(int)$expires[0] );
        break;
      default: // if something fudged up then default to 1 month
        $expires = mktime( date("H"), date("i"), date("s"), date("m")+1, date("d"), date("Y") );
        break;
    } # end switch( strtolower($expires[1]) )
    return date("Y-m-d H:i:s", $expires);
  } # end function convert_time()

/**
 * Function to check if the cache is in the database and expired  
 * @param string $name
 * @param boolean $is_cached NOTE: passed by reference
 * @param boolean $is_expired NOTE: passed by reference
 */ 
/*---------------------------
 功能：检查缓存是否在数据库中过期
 参数：$name(string) 名称
 参数：$is_cached(string) 缓存
 参数：$is_expired(string) 过期时间
 返回值：无
 --------------------------*/
  function is_cached($name, &$is_cached, &$is_expired){ // NOTE: $is_cached and $is_expired is passed by reference !!
    $this->cache_query = $this->DB->Query("SELECT cache_expires FROM cache WHERE cache_id='".md5($name)."' AND cache_language_id='".(int)$this->languages_id."' LIMIT 1");
    $is_cached = ( $this->DB->NumRows($this->cache_query ) > 0 ? true : false );
    if ($is_cached){ 
      $check = $this->DB->FetchArray($this->cache_query);
      $is_expired = ( $check['cache_expires'] <= date("Y-m-d H:i:s") ? true : false );
      unset($check);
    }
    $this->DB->Free($this->cache_query);
  }# end function is_cached()

/**
 * Function to initialize the redirect logic
 */ 
/*-------------------------
 功能：检查重定向 
 参数：无
 返回值：无
 ------------------------*/
  function check_redirect(){
    $this->need_redirect = false; 
    $this->path_info = ltrim(getenv('PATH_INFO'), '/');
    $this->uri = ltrim( $_SERVER['REQUEST_URI'], '/' );
    $this->real_uri = ltrim( $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'], '/' );
    $this->uri_parsed = strlen($this->path_info) > 0
                ? parse_url(basename($_SERVER['SCRIPT_NAME']) . '?' . $this->parse_path($this->path_info) )
                : parse_url(basename($_SERVER['REQUEST_URI']));     
    $this->need_redirect(); 
    $this->check_seo_page();    
    if ( $this->need_redirect && $this->is_seopage && $this->attributes['USE_SEO_REDIRECT'] == 'true') $this->do_redirect();      
  } # end function
  
/**
 * Function to check if the URL needs to be redirected 
 */ 
/*---------------------------
 功能：是否需要被重定向的URL
 参数：无
 返回值：无
 --------------------------*/
  function need_redirect(){
    if ( is_numeric(strpos($this->real_uri, '{')) ){
      $this->need_redirect = false;
    } else {
      foreach( $this->reg_anchors as $param => $value){
        $pattern[] = $param;
      }
    $this->uri != $this->real_uri 
      ? $this->need_redirect = false
      : eregi("(cName|pName|mName)", $this->uri) 
          ? $this->attributes['SEO_REWRITE_TYPE'] != 'cName' 
            ? $this->need_redirect = true 
            : $this->need_redirect = false 
          : eregi("(".implode('|', $pattern).")", $this->uri) 
            ? $this->need_redirect = true
            : $this->need_redirect = false;
    }
  } # end function set_seopage
  
/**
 * Function to check if it's a valid redirect page 
 */ 
/*------------------------
 功能：检查是否是一个重定向的页面 
 参数：无
 返回值：无
 -----------------------*/
  function check_seo_page(){
    !defined('SEO_URLS') 
      ? $this->is_seopage = false 
      : $this->attributes['SEO_ENABLED'] == 'false'
        ? $this->is_seopage = false
        : !in_array($this->uri_parsed['path'], $this->attributes['SEO_PAGES'])
          ? $this->is_seopage = false 
          : $this->is_seopage = true; 
  } # end function check_seo_page
  
/**
 * Function to parse the path for old SEF URLs 
 * @param string $path_info
 * @return array
 */ 
/*-------------------------
 功能：解析路径
 参数：$path_infoa(string) 路径信息
 返回值：路径数组(array)
 ------------------------*/
  function parse_path($path_info){ 
    $tmp = @explode('/', $path_info);     
    if ( sizeof($tmp) > 2 ){
      $container = array();       
      for ($i=0, $n=sizeof($tmp); $i<$n; $i++) {
        $container[] = $tmp[$i] . '=' . $tmp[$i+1]; 
        $i++; 
      }
      return @implode('&', $container);     
    } else { 
      return @implode('=', $tmp);
    }       
  } # end function parse_path
  
/**
 * Function to perform redirect 
 */ 
/*-------------------------
 功能：执行重定向
 参数：无
 返回值：无
 ------------------------*/
  function do_redirect(){
    $p = @explode('&', $this->uri_parsed['query']);
    foreach( $p as $index => $value ){              
      $tmp = @explode('=', $value);
        switch($tmp[0]){
          case 'products_id':
            if ( $this->is_attribute_string($tmp[1]) ){
              $pieces = @explode('{', $tmp[1]);             
              $params[] = $tmp[0] . '=' . $pieces[0];
            } else {
              $params[] = $tmp[0] . '=' . $tmp[1];
            }
            break;
          default:
            $params[] = $tmp[0].'='.$tmp[1];
            break;            
        }
    } # end foreach( $params as $var => $value )
    $params = ( sizeof($params) > 1 ? implode('&', $params) : $params[0] );   
    $url = $this->href_link($this->uri_parsed['path'], $params, 'NONSSL', false);
    if ( $this->attributes['USE_SEO_REDIRECT'] == 'true' ){     
      header("HTTP/1.0 301 Moved Permanently"); 
      header("Location: $url"); // redirect 
    }
  } # end function do_redirect  
} # end class
?>
