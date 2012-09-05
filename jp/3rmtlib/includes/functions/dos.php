<?php
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
    if ($total_num > 0 && $total_num > $unit_total) {
      //send mail
      $dos_email_msg = 'Prebanlist '.date('Y-m-d H:i:s')."  ".$ip_info;
      send_mail(DOS_SEND_MAIL,'DoS Alert !',$dos_email_msg);
      return true; 
    }else{
      $pdo_con->exec("delete from accesslog where vtime<= '".date('Y-m-d H:i:s', time()-$unit_time)."'"); 
      return false;
    }
  }
  
  return false;
}

function analyze_ban_log($pdo_con, $ip_info)
{
  foreach( $pdo_con->query("select count(*) as con from prebanlist where ip =
        '".$ip_info."' and type='1' limit 1") as $res){
    $con = $res['con'];
  }
  // delete banlist ip
  $pdo_con->exec("delete from banlist where ip = '".$ip_info."'"); 
  if ($con  >= 2) {
    //close 24
    $pdo_con->exec("insert into banlist SET id= 'NULL', ip = '".$ip_info."', betime='".date('Y-m-d H:i:s', time()+60*60*24)."'");
    $pdo_con->exec("insert into prebanlist SET id= 'NULL', ip = '".$ip_info."', bstime='".date('Y-m-d H:i:s', time())."',type='24'");
    //send mail
    $dos_email_msg = 'Banlist '.date('Y-m-d H:i:s')."  ".$ip_info;
    send_mail(DOS_SEND_MAIL,'DoS Alert !',$dos_email_msg);
  } else {
    //close 1
    $pdo_con->exec("insert into banlist SET id= 'NULL', ip = '".$ip_info."', betime='".date('Y-m-d H:i:s', time()+60*60)."'");
    $pdo_con->exec("insert into prebanlist SET id= 'NULL', ip = '".$ip_info."', bstime='".date('Y-m-d H:i:s', time())."',type='1'");
    //send mail
    $dos_email_msg = 'Prebanlist '.date('Y-m-d H:i:s')."  ".$ip_info;
    send_mail(DOS_SEND_MAIL,'DoS Alert !',$dos_email_msg);
  }
  //delete accesslog ip 
  $pdo_con->exec("delete from accesslog where ip = '".$ip_info."'"); 
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

function is_reset_blocked_ip($pdo_con, $ip_info){
  $res = $pdo_con->query("select count(*) from banlist where ip = '".$ip_info."' 
    and betime < '".date('Y-m-d H:i:s', time())."'"); 
  if ($res) {
    $total_num = $res->fetchColumn(); 
    if($total_num > 0){
      $pdo_con->exec("delete from banlist where ip = '".$ip_info."'"); 
      $pdo_con->exec("delete from prebanlist where ip = '".$ip_info."'"); 
      return false;
    }else{
      return is_at_ban_list($pdo_con, $ip_info);
    }
  }
}

