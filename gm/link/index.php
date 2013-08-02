<?php
error_reporting("E_ALL");
ini_set("display_errors","On");
define('NO_LEGACY_FLEAPHP', true);
define('DEPLOY_MODE', false);
require('./Fleaphp/FLEA.php');
define('INDEX_DIR',dirname(__FILE__));
define('APP_DIR', dirname(__FILE__) . DS . 'APP');
FLEA::loadAppInf(APP_DIR . '/Config/APP_INF.php');
FLEA::loadAppInf(APP_DIR . '/Config/DATABASE.php');
FLEA::loadAppInf(APP_DIR . '/Config/Global.php');
require(APP_DIR.'/Class/dos.php');
require(INDEX_DIR.'/../includes/configure.php');
// connect db
$dsn = 'mysql:host='.DB_SERVER.';dbname='.DB_DATABASE;
$pdo_con = new PDO($dsn, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);

// ip 
$source_ip = $_SERVER['REMOTE_ADDR'];
// host
$source_host = $_SERVER['HTTP_HOST'];
// config time 
$unit_time = 3;
// confi total
$unit_total = 15;
if ($pdo_con) {
  if(is_reset_blocked_ip($pdo_con, $source_ip)){
    // go to 503
    header("Cache-Control:");
    header("Pragma:");
    header("Expires:".date("D, d M Y H:i:s",0)." GMT");
    header('http/1.1 503 Service Unavailable');
    require(INDEX_DIR.'/error/503-service-unavailable.html');
    exit;
  } else {
    // write ip to accresslog 
    write_vlog($pdo_con, $source_ip, $source_host);    
    if (is_large_visit($pdo_con, $source_ip, $unit_time, $unit_total)) {
      // write ip to banlist prebanlist
      analyze_ban_log($pdo_con, $source_ip);
      // go to 503
      header("Cache-Control:");
      header("Pragma:");
      header("Expires:".date("D, d M Y H:i:s",0)." GMT");
      header('http/1.1 503 Service Unavailable');
      require(INDEX_DIR.'/error/503-service-unavailable.html');
      exit;
    }
  }
}
function forward404(){
      header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
      require(dirname(__FILE__) . '/../../404.html');
      exit;
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

FLEA::import(APP_DIR);
FLEA::runMVC();
function ON_ACCESS_DENIED($controller, $action)
{
  echo "您不被允许访问该页 或该页不存在";
  
//  if(isset($_SESSION['BACKURL'])){
//    redirect($_SESSION['BACKURL']);
//  }else {
    redirect("/index.php");
//  }
}
