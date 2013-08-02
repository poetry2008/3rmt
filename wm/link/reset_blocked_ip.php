<?php
ini_set("display_errors", "Off");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
header("Cache-Control:");
header("Pragma:");
header("Expires:".date("D, d M Y H:i:s",0)." GMT");
define('NO_LEGACY_FLEAPHP', true);
define('DEPLOY_MODE', false);
require('./Fleaphp/FLEA.php');
define('INDEX_DIR',dirname(__FILE__));
define('APP_DIR', dirname(__FILE__) . DS . 'APP');
FLEA::loadAppInf(APP_DIR . '/Config/Global.php');
require(APP_DIR.'/Class/dos.php');
$r_url = FLEA::getAppInf('site_url');
$r_url .= 'link/';
// connect db
require(INDEX_DIR.'/../includes/configure.php');
// connect db
$dsn = 'mysql:host='.DB_SERVER.';dbname='.DB_DATABASE;
$pdo_con = new PDO($dsn, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);

$source_ip = $_SERVER['REMOTE_ADDR'];
$source_host = $_SERVER['HTTP_HOST'];
$clear_banlist = false;
foreach( $pdo_con->query("select type from prebanlist where ip = '".$source_ip."'
      order by bstime desc limit 1") as $row){
if( $row['type'] == '1'){
  $clear_banlist = true;
}
}
if($clear_banlist){
  $pdo_con->exec("delete from banlist where ip = '".$source_ip."'");
  header('Location:'.$r_url);
  exit;
}else{
  $res = $pdo_con->query("select count(*) from banlist where ip = '".$source_ip."'");
  if ($res->fetchColumn() > 0) {
    header('http/1.1 503 Service Unavailable');
    require(INDEX_DIR.'/error/503-service-unavailable.html');
    exit;
  }else{
    header('Location:'.$r_url);
    exit;
  }
}
