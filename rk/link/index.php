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
