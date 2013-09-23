<?php
ini_set("display_errors", "Off");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
include('includes/configure.php');
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
  unset($_SESSION['banlist'][$ip_info]['relock_time']);
  $key = array_search($ip_info,$_SESSION['banlist_ip']);
  unset($_SESSION['banlist_ip'][$key]);
  session_write_close();
  tep_session_id($old_sid);
  tep_session_start();


header("Cache-Control:");
header("Pragma:");
header("Expires:".date("D, d M Y H:i:s",0)." GMT");
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
  header('Location:'.HTTP_SERVER);
  exit;
}else{
  $res = $pdo_con->query("select count(*) from banlist where ip = '".$source_ip."'");
  if ($res->fetchColumn() > 0) {
    header('http/1.1 503 Service Unavailable');
    require(DIR_FS_DOCUMENT_ROOT.'error/503-service-unavailable.html');
    exit;
  }else{
    header('Location:'.HTTP_SERVER);
    exit;
  }
}
