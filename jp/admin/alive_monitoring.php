<?php
//error_reporting(E_ALL);
//ini_set('display_errors','On');

//这个程序由crontab调用 ,监视各种服务是否正常运行 
//设置常量
//MYSQL_GROUP
define('MYSQL_HOST','localhost');
define('MYSQL_USER','root');
define('MYSQL_PASSWORD','123456');
define('MYSQL_DATABASE','maker_3rmt');
define('MYSQL_DNS','mysql:host='.MYSQL_HOST.';dbname:'.MYSQL_DATABASE);
define('MINITOR_DOMAINS_FROM_MYSQL',true);//是否从MYSQL读取想要确认的数据
define('MSG_WEB_SUCCESS','web success');
define('EMAIL_EXP', "^[a-z'0-9]+([._-][a-z'0-9]+)*@([a-z0-9]+([._-][a-z0-9]+))+$");
define('URL_PARSE_EASY',true);
define("LOG_LIMIT",60);
define('SYSTEM_MAIL','bobhero.chen@gmail.com');

//define message template
define("MSG_HTTP_SUCCESS","HTTP OK");
define("MSG_HTTPS_SUCCESS","HTTPS OK");
define("MSG_HOST_SUCCESS","HOST OK");
define("MSG_PAGE_SUCCESS","PAGE OK");
define("MSG_MYSQL_SUCCESS","MYSQL OK");
define("MSG_HTTP_NG","HTTP NG");
define("MSG_HTTPS_NG","HTTPS NG");
define("MSG_HOST_NG","HOST NG");
define("MSG_PAGE_NG","PAGE NG");
define("MSG_MYSQL_NG","MYSQL NG");

function sql_injection($content)
{
  if (!get_magic_quotes_gpc()) {
    if (is_array($content)) {
      foreach ($content as $key=>$value) {
        $content[$key] = addslashes($value);
      }
    } else {
      addslashes($content);
    }
  }

  return $content;
}

function sMail($message){
  //  echo 'SYSTEM MAIL ';
  @mail(SYSTEM_MAIL,date('Y-m-d H:i:s'),$message);
}
function db_query($sql)
{
  global $conn;
  if (!$conn){
    $conn =  mysql_connect(MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD);
    if (!$conn){
      sMail('db connect error');
      die('db error');
    }
    mysql_select_db(MYSQL_DATABASE);
    mysql_query("set names utf-8");
  }
  return mysql_query($sql);
}

function getDomains(){
  //如果不从MYSQL里读取数据
  if (!MINITOR_DOMAINS_FROM_MYSQL){
    $domains = array(
                     'www.iimy.co.jp',
                     'http://haomai:haomai@jp.gamelife.jp',
                     'http://haomai:haomai@jp.gamelife.jp/xcixw.php',
                     'http://www.iimy.co.jp',
                     );
  }else{
    $res = db_query('select * from monitor where enable="on"');
    $domains = array();
    while($domain = mysql_fetch_object($res,'Monitor')){
      $domains[] = $domain;
    }
    //    mysql_close($conn);
  }
  return $domains;
}
class Monitor {
  const MSG_TYPE_ERROR      = '1';
  const MSG_TYPE_LOG        = '2';
  const MSG_TYPE_WARNING    = '3';
  var $id;
  var $name;
  var $checkparm;
  var $reportmethod;
  var $reportemails;
  var $dbstring;
  var $logfile;
  var $reportwhen;
  var $url = false;
  var $storelimit;
  var $shouldreport = 0;
  var $emailMsg = '';

  function __toString(){
    return serialize($this);
  }
  function __construct(){
    if(strpos($this->reportmethod,'log')>-1){//如果用到了log方法
      if(trim($this->logfile) ==''){
        sMail('logfile is null '.$this);
        $this->logfile = '/dev/null';
      }else {
        $pathinfo = pathinfo($this->logfile);
        if($pathinfo['dirname']=='.'){
          sMail('logfile is not base /'.$this);
          $this->logfile = '/dev/null';
        }else {
          if(!is_dir($pathinfo['dirname']) or !is_writeable($this->logfile)){
            if (file_put_contents($this->logfile,'')===false){
              sMail('logfile not writeable'.$this);
              $this->logfile = '/dev/null';
            }
          }
        }
      }
      
    }
    $this->parsedurl = parse_url($this->url);
    if (count($this->parsedurl) == 1){
      if (URL_PARSE_EASY){
        if (strpos($this->iurl,'.')===false){ //如果有.我们认为是一个域名,如果没有,直接报错
          $this->log("invalid domain quit");
          return false;
        }else {
          $this->parsedurl= parse_url('http://'.$url);//容错
        }
      }else{
        $this->log("invalid domain url quit");
        return false;
      }
    }
  }
  function getUrl(){
    $url = '';
    $p = $this->parsedurl;
    $url = $p['scheme']."://";
    $url.= isset($p['user'])?$p['user']:'';
    $url.= isset($p['pass'])?':'.$p['pass']:'';
    $url.= isset($p['user'])?'@':'';
    $url.= isset($p['host'])?$p['host']:'';
    $url.= isset($p['port'])?':'.$p['port']:'';
    $url.= isset($p['path'])?$p['path']:'';
    return $url;
  }
  function getHostUrl($https=false){
    $p = $this->parsedurl;
    $p['scheme'] = $https?'https':$p['scheme'];
    $url = $p['scheme']."://";
    $url.= isset($p['user'])?$p['user']:'';
    $url.= isset($p['pass'])?':'.$p['pass']:'';
    $url.= isset($p['user'])?'@':'';
    $url.= isset($p['host'])?$p['host']:'';
    $url.= isset($p['port'])?':'.$p['port']:'';
    return $url;
  }
  function report(){
    if($this->reportwhen == 'both' or $this->shouldreport)
      {
        $methods = explode(',',$this->reportmethod.',' );
        foreach($methods as $key=>$method)
          {
            switch($method){
            case 'email':
              //检查是否有非法email
              $emails = explode(';',$this->reportemails.';');
              $emailsString = '';
              foreach($emails as $email){
                if ($email !=''){
                  if(!eregi(EMAIL_EXP,$email)){
                    sMail('invalid email found '.$email);
                  }else{
                    $emailsString.= $email.';';
                  }
                }
              }
              $mailContent = $this->emailMsg;
              $mailContent.= '----------------PG USE --------------'."\r\n";
              $mailContent.= $this;
              mail($emailsString,$this->name.' Minitor Result '.date('Y-m-d H:i:s'),$mailContent);
              break;
            case 'db':
              $sql = "
                      INSERT INTO monitor_log (
                      `id` ,`m_id` ,`name` ,`obj` ,`ng`,`log` ,`created_at` 
                      )VALUES (
                      NULL ,".$this->id.',\''.$this->name.'\',\''.sql_injection($this).'\','.$this->shouldreport.',"'.$this->emailMsg.'","'.date('Y-m-d H:i:s')."\"
                      );
                     ";
              //              echo $sql;
              $result = db_query($sql);
              //              var_dump($result);
              //执行完成以后检查是否多于系统限制如果多于,则删除以前记录 
              $sqlCount = "
                          SELECT count(*) as cnt
                          FROM monitor_log ";
                      //    WHERE m_id = ".$this->id;
              $res = mysql_fetch_array(db_query($sqlCount));

              //              if($haveToDel = ($limit = (int)$res['cnt']-$this->storelimit)>0){
              if($haveToDel = ($limit = (int)$res['cnt']-LOG_LIMIT)>0){
                //                          WHERE m_id = ".$this->id."
                $sqlDel = "
                          DELETE FROM monitor_log
                          order by id limit ".$limit;
                //                echo $sqlDel;
                db_query($sqlDel);
              }
              break;
            case 'log':
              $file = fopen($this->logfile,'a');
              fwrite($file,'['.date("Y-m-d H:i:s").']    '."\r\n");
              fwrite($file,'obj:'."{{{{{\r\n");
              fwrite($file,$this);
              fwrite($file,"}}}}}\r\n");
              fwrite($file,$this->emailMsg."\r\n");
              fwrite($file,"\r\n");
              fclose($file);
              break;
            }
          }
      }
  }

  function isAlive(){
    $types = explode(',',$this->checkparm);
    if (!is_array($types)){
      $types = $this->checkparm;
    }
    if(is_array($types)){
      foreach($types as $key=>$type){
        if (call_user_func(array(&$this,'isAlive'.$type))){
          $this->log(constant(strtoupper('msg_'.$type.'_success')));
        }else{
          $this->shouldreport = true;
          $this->log(constant(strtoupper('msg_'.$type.'_ng' )));
        }
      }
    }else {
      $type = $types;
      if (call_user_func(array(&$this,'isAlive'.$type))){
          $this->log(constant(strtoupper('msg_'.$type.'_success')));
      }else{
        $this->log(constant(strtoupper('msg_'.$type.'_ng' )));
        $this->log($type.'faild');
      }
    }
    return !$this->shouldreport;
  }
  
  function log($message,$type=self::MSG_TYPE_LOG){
    $this->emailMsg .= $message ."\r\n";
    //    echo $this->name .' ' .$this->url. 'is '.$message;
    //    echo "\r\n";
  }
  function isAlivePage(){
    if (file_get_contents($this->getUrl())){
      return true;
    }else {
      return false;
    }
  }

  //todo  freespace check
  function isAliveFreespace(){
    return true;
  }
  function isAliveHttp(){
    if (file_get_contents($this->getHostUrl())){
      return true;
    }else {
      return false;
    }
  }
  function isAliveHttps(){
    if (file_get_contents($this->getHostUrl(true))){
      return true;
    }else {
      return false;
    }
  }
  //todo mysqlcheck
  function isAliveMysql(){
    return true;
  }

  function isAliveHost(){
    $ping_command_str = "ping -c 3 -w 5 ".$this->parsedurl['host'];
    if (!strstr(`$ping_command_str`, '100% packet loss')){
      return true;
    }else {
      return false;
    }
  }
}
//先file_get_content 如果成功 不用检查 机器是否开机,如果不成功再检查是否开机
$domains = getDomains();
$alivelist = array();
if(count($domains) != 0){
foreach ($domains as $key=>$domain){
  $cHost= $domain;
  if ($cHost!=FALSE){
    if ($cHost->isAlive()){
      $alivelist[] = $cHost->name;
    }else {
      $loglist[$cHost->name]= $cHost->emailMsg;
    }
    $cHost->report();
  }
  unset($cHost);
}
}else{
  sMail('There is no process to go');
}

if (count($alivelist)){
  echo "We are alive:";
  echo "</br>";
  ?>
<table>
<?php
  foreach ($alivelist as $value){
    echo "<tr><td>$value</td></tr>"; 
  }
?>
</table>
<?php 
}

if (count($loglist)){
  echo "We have problem:";
  echo "</br>";
  ?>
  <table>
<?php      
  foreach($loglist as $key=>$value){
     echo "<tr><td>$key</td><td>$value</td></tr>";
  }
?>
</table>
    </br>
<?php
}
