<?php
/* -------------------------------------
    功能: 是否在禁止列表里  
    参数: $pdo_con(object) pdo对象   
    参数: $ip_info(string) ip地址   
    返回值: 是否在禁止列表里(boolean)  
------------------------------------ */
function is_at_ban_list($pdo_con, $ip_info)
{
  $res = $pdo_con->query("select count(ip) from banlist where ip = '".$ip_info."'"); 
  if ($res->fetchColumn() > 0) {
    $ban_info_res = $pdo_con->query("select count(ip) from banlist where ip = '".$ip_info."' and betime >= '".date('Y-m-d H:i:s', time())."'"); 
    if ($ban_info_res->fetchColumn() > 0) {
      return true; 
    } else {
      $pdo_con->exec("delete from banlist where ip = '".$ip_info."'"); 
    }
  }
  return false;
}

/* -------------------------------------
    功能: 新建日志记录  
    参数: $pdo_con(object) pdo对象   
    参数: $ip_info(string) ip地址   
    参数: $host_info(string) 网站地址   
    返回值: 无 
------------------------------------ */
function write_vlog($pdo_con, $ip_info, $host_info)
{
   $pdo_con->exec("insert into accesslog set ip = '".$ip_info."',vtime='".date('Y-m-d H:i:s', time())."', site='".$host_info."'");
}

/* -------------------------------------
    功能: 是否超过访问次数  
    参数: $pdo_con(object) pdo对象   
    参数: $ip_info(string) ip地址   
    参数: $unit_time(string) 间隔时间   
    参数: $unit_total(string) 间隔次数   
    参数: $type(string) 间隔时间类型 s 秒 i分 h小时
    返回值: 是否超过访问次数(boolean) 
------------------------------------ */
function is_large_visit($pdo_con, $ip_info, $unit_time, $unit_total,$type='s')
{
  if($type=='i'){
    $unit_time = $unit_time*60;
  }else if($type=='h'){
    $unit_time = $unit_time*60*60;
  }
  $res = $pdo_con->query("select count(ip) from accesslog where ip = '".$ip_info."' and vtime <= '".date('Y-m-d H:i:s', time())."' and vtime >= '".date('Y-m-d H:i:s', time()-$unit_time)."'"); 
  if ($res) {
    $total_num = $res->fetchColumn(); 
    if ($total_num > 0 && $total_num > $unit_total) {
      return true; 
    }else{
      // delete  accesslog  rows by time
      if($type=='h'){
        $pdo_con->exec("delete from accesslog where vtime< '".date('Y-m-d H:i:s', time()-$unit_time)."'"); 
      }
      return false;
    }
  }
  return false;
}

/* -------------------------------------
    功能: 分析日志  
    参数: $pdo_con(object) pdo对象   
    参数: $ip_info(string) ip地址   
    返回值: 无 
------------------------------------ */
function analyze_ban_log($pdo_con, $ip_info)
{
  foreach( $pdo_con->query("select count(ip) as con from prebanlist where ip =
        '".$ip_info."' and type='1' limit 1") as $res){
    $con = $res['con'];
  }
  // delete banlist ip
  $pdo_con->exec("delete from banlist where ip = '".$ip_info."'"); 
  if ($con  >= 2) {
    //close 24
    $pdo_con->exec("insert into banlist SET ip = '".$ip_info."', betime='".date('Y-m-d H:i:s', time()+60*60*24)."'");
    $pdo_con->exec("insert into prebanlist SET ip = '".$ip_info."', bstime='".date('Y-m-d H:i:s', time())."',type='24'");
    //send mail
    $dos_email_msg = 'Banlist '.date('Y-m-d H:i:s')."  ".$ip_info;
    send_mail(DOS_SEND_MAIL,'DoS Alert !',$dos_email_msg);
  } else {
    //close 1
    $pdo_con->exec("insert into banlist SET ip = '".$ip_info."', betime='".date('Y-m-d H:i:s', time()+60*60)."'");
    $pdo_con->exec("insert into prebanlist SET ip = '".$ip_info."', bstime='".date('Y-m-d H:i:s', time())."',type='1'");
    //send mail
    $dos_email_msg = 'Prebanlist '.date('Y-m-d H:i:s')."  ".$ip_info;
    send_mail(DOS_SEND_MAIL,'DoS Alert !',$dos_email_msg);
  }
  //delete accesslog ip 
  $pdo_con->exec("delete from accesslog where ip = '".$ip_info."'"); 
}

/* -------------------------------------
    功能: 发送邮件  
    参数: $sTo(string) 收信人邮箱   
    参数: $sTitle(string) 标题   
    参数: $sMessage(string) 内容   
    参数: $sFrom(string) 寄信人名   
    参数: $sReply(string) 寄信人邮箱   
    参数: $sName(string) 寄信人邮箱   
    返回值: 是否发送成功(boolean) 
------------------------------------ */
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

/* -------------------------------------
    功能: 是否重置限制ip  
    参数: $pdo_con(object) pdo对象   
    参数: $ip_info(string) ip地址   
    返回值: 是否重置成功(boolean) 
------------------------------------ */
function is_reset_blocked_ip($pdo_con, $ip_info){
  $res = $pdo_con->query("select count(ip) from banlist where ip = '".$ip_info."' 
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

/* -------------------------------------
    功能: 限制ip存储到SESSION
    参数: $pdo_con(object) pdo对象   
    参数: $ip_info(string) ip地址   
------------------------------------ */
function save_block_ip($pdo_con){
  $res = $pdo_con->query("select ip,betime from banlist"); 
  while($ban_info = $res->fetch()){
    $_SESSION['banlist'][$ban_info['ip']] = array(
        'ip' => $ban_info['ip'],
        'relock_time' => $ban_info['betime']);
    $_SESSION['banlist_ip'][] = $ban_info['ip'];
  }
}

/* -------------------------------------
    功能: 是否重置限制ip  
    参数: $ip_info(string) ip地址   
    返回值: 是否重置成功(boolean) 
------------------------------------ */
function is_reset_session_blocked_ip($ip_info){
  $relock_time = strtotime($_SESSION['banlist'][$ip_info]['relock_time']);
  if ($relock_time<time()) {
    unset($_SESSION['banlist'][$ip_info]['relock_time']);
    $key = array_search($ip_info,$_SESSION['banlist_ip']);
    unset($_SESSION['banlist_ip'][$key]);
    return false;
  }else{
    return true;
  }
}
