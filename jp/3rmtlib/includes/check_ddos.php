<?php

$db = '/tmp/hm.db';
$email_info = 'bobhero.chen@gmail.com';

$pdo_con = new PDO('sqlite:'.$db);

$source_ip = $_SERVER['REMOTE_ADDR'];
$source_host = $_SERVER['HTTP_HOST'];
$unit_time = 1;
$unit_total = 5;
if ($pdo_con) {
  if (is_at_ban_list($pdo_con, $source_ip)) {
    header('http/1.1 503 Service Unavailable');
    require(DIR_FS_3RMTLIB.'includes/503-service-unavailable.php');
    exit;
  } else {
    write_vlog($pdo_con, $source_ip, $source_host);    
    if (is_large_visit($pdo_con, $source_ip, $unit_time, $unit_total)) {
      analyze_ban_log($pdo_con, $source_ip);
      $message = $source_ip.','.date('Y-m-d H:i:s', time()); 
      send_mail($email_info, 'ddos_log', $message); 
      header('http/1.1 503 Service Unavailable');
      require(DIR_FS_3RMTLIB.'includes/503-service-unavailable.php');
      exit;
    }
  }
}

function is_at_ban_list($pdo_con, $ip_info)
{
  $res = $pdo_con->query("select count(*) from banlist where ip = '".ip2long($ip_info)."'"); 
  if ($res->fetchColumn() > 0) {
    $ban_info_res = $pdo_con->query("select count(*) from banlist where ip = '".ip2long($ip_info)."' and betime >= '".date('Y-m-d H:i:s', time())."'"); 
    if ($ban_info_res->fetchColumn() > 0) {
      return true; 
    } else {
      $pdo_con->query("delete from banlist where ip = '".ip2long($ip_info)."'"); 
    }
  }
  return false;
}

function write_vlog($pdo_con, $ip_info, $host_info)
{
  $pdo_con->query("insert into vlog ('id', 'ip', 'vtime', 'site') values(NULL, '".ip2long($ip_info)."', '".date('Y-m-d H:i:s', time())."', '".$host_info."')");
}

function is_large_visit($pdo_con, $ip_info, $unit_time, $unit_total)
{
  $res = $pdo_con->query("select count(*) from vlog where ip = '".ip2long($ip_info)."' and vtime <= '".date('Y-m-d H:i:s', time())."' and vtime >= '".date('Y-m-d H:i:s', time()-$unit_time)."'"); 
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
  $res = $pdo_con->query("select count(*) from banlog where ip = '".ip2long($ip_info)."'"); 
  if ($res->fetchColumn() > 0) {
    $pdo_con->query("insert into banlist('id', 'ip', 'betime') values(NULL, '".ip2long($ip_info)."', '".date('Y-m-d H:i:s', time()+60*60*24)."')");  
  } else {
    $pdo_con->query("insert into banlist('id', 'ip', 'betime') values(NULL, '".ip2long($ip_info)."', '".date('Y-m-d H:i:s', time()+60*60)."')");  
  }
  $pdo_con->query("insert into banlog('id', 'ip', 'bstime') values(NULL, '".ip2long($ip_info)."', '".date('Y-m-d H:i:s', time())."')");  
}

function send_mail($sTo, $sTitle, $sMessage, $sFrom = null, $sReply = null, $sName = NULL)
{
  $sTitle = stripslashes($sTitle);
  $sMessage = stripslashes($sMessage);
  if ($sName == NULL) {
    if ($sFrom) {
      $sFromName = "=?UTF-8?B?" . base64_encode($sFrom) . "?=";
    }
  } else {
    $sFromName = "=?UTF-8?B?" . base64_encode($sName) . "?=";
  }
  $sAdditionalheader = "From:" . $sFrom . "\r\n";
  $sAdditionalheader.= "Reply-To:" . $sFromName . " <" . $sReply . ">\r\n";
  $sAdditionalheader.= "Date:" . date("r") . "\r\n";
  $sAdditionalheader.= "MIME-Version: 1.0\r\n";
  $sAdditionalheader.= "Content-Type:text/plain; charset=UTF-8\r\n";
  $sAdditionalheader.= "Content-Transfer-Encoding:7bit";
  $sTitle = "=?UTF-8?B?" . base64_encode($sTitle) . "?=";
  return @mail($sTo, $sTitle, $sMessage, $sAdditionalheader);
}
