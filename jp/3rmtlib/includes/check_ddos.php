<?php

$dsn = 'mysql:host='.DB_SERVER.';dbname='.DB_DATABASE;
$pdo_con = new PDO($dsn, DB_SERVER_USERNAME, DB_SERVER_PASSWORD);

$source_ip = $_SERVER['REMOTE_ADDR'];
$source_host = $_SERVER['HTTP_HOST'];
$unit_time = 1;
$unit_total = 5;
if ($pdo_con) {
  if (is_at_ban_list($pdo_con, $source_ip)) {
    header('http/1.1 503 Service Unavailable');
    require(DIR_FS_DOCUMENT_ROOT.'503-service-unavailable.html');
    exit;
  } else {
    write_vlog($pdo_con, $source_ip, $source_host);    
    if (is_large_visit($pdo_con, $source_ip, $unit_time, $unit_total)) {
      analyze_ban_log($pdo_con, $source_ip);
      header('http/1.1 503 Service Unavailable');
      require(DIR_FS_DOCUMENT_ROOT.'503-service-unavailable.html');
      exit;
    }
  }
}

function is_at_ban_list($pdo_con, $ip_info)
{
  $res = $pdo_con->query("select count(*) from banlist where ip = '".$ip_info."'"); 
  if ($res->fetchColumn() > 0) {
    $ban_info_res = $pdo_con->query("select count(*) from banlist where ip = '".$ip_info."' and betime >= '".date('Y-m-d H:i:s', time())."'"); 
    if ($ban_info_res->fetchColumn() > 0) {
      return true; 
    } else {
      $pdo_con->exec("delete from banlist where ip = '".$ip_info."'"); 
    }
  }
  return false;
}

function write_vlog($pdo_con, $ip_info, $host_info)
{
   $pdo_con->exec("insert into accesslog set id= 'NULL', ip = '".$ip_info."',vtime='".date('Y-m-d H:i:s', time())."', site='".$host_info."'");
}

function is_large_visit($pdo_con, $ip_info, $unit_time, $unit_total)
{
  $res = $pdo_con->query("select count(*) from accesslog where ip = '".$ip_info."' and vtime <= '".date('Y-m-d H:i:s', time())."' and vtime >= '".date('Y-m-d H:i:s', time()-$unit_time)."'"); 
  if ($res) {
    $total_num = $res->fetchColumn(); 
    if ($total_num > 0) {
      if ($total_num > $unit_total) {
        return true; 
      }
    }
  }
  
  return false;
}

function analyze_ban_log($pdo_con, $ip_info)
{
  $res = $pdo_con->query("select count(*) from prebanlist where ip = '".$ip_info."'"); 
  $pdo_con->exec("delete from banlist where ip = '".$ip_info."'"); 
  if ($res->fetchColumn() > 0) {
    $pdo_con->exec("insert into banlist SET id= 'NULL', ip = '".$ip_info."', betime='".date('Y-m-d H:i:s', time()+60*60*24)."'");
  } else {
    $pdo_con->exec("insert into banlist SET id= 'NULL', ip = '".$ip_info."', betime='".date('Y-m-d H:i:s', time()+60*60)."'");
  }
  $pdo_con->exec("insert into prebanlist SET id= 'NULL', ip = '".$ip_info."', bstime='".date('Y-m-d H:i:s', time())."'");
  $pdo_con->exec("delete from accesslog where ip = '".$ip_info."'"); 
}
