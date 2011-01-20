<?php
//设置常量
//MYSQL_GROUP
//include(@realpath('./includes/configure.php'));
include(dirname(__FILE__).'/includes/configure.php');
define("LOG_LIMIT",60);

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

function db_query($sql)
{
  global $conn;
  if (!$conn){
    $conn =
                  mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
    if (!$conn){
      die('db error');
    }
    mysql_select_db(DB_DATABASE);
    mysql_query("set names 'utf8'");
  }
  return mysql_query($sql);
}

function getDomains(){
  //如果不从MYSQL里读取数据
  $domains = array();
  if(0 and isset($_SERVER["HTTP_USER_AGENT"])){
    //如果是从页面执行文件 查询所有
    $res = db_query('select * from monitor where enable="on"');
    while($domain = mysql_fetch_object($res,'Monitor')){
      $domains[] = $domain;
    }
  }else{
    //不从页面执行的时候 查找 next=1 的 也就是有标记的
    $res = db_query('select * from monitor where enable="on" and next="1" order by id');
    if($domain = mysql_fetch_object($res,'Monitor')){
      $domains[] = $domain;
      $run_id = $domain->id;
    }else{
      //如果没有标记 查找第一个
      $res = db_query('select * from monitor where enable="on" order by id limit 1');
      if($domain = mysql_fetch_object($res,'Monitor')){
        $domains[] = $domain;
        $run_id = $domain->id;
      }
    }
    //把当前标记去除
    db_query('update monitor set next="0" where enable="on" and id="'.$run_id.'"');
    //给下一个有效记录标记
    db_query('update monitor set next="1" where enable="on" and id>"'.$run_id.'" order by id limit 1');
    //    mysql_close($conn);
  }
  return $domains;
}
class Monitor {
  var $id;
  var $name;
  var $url = false;
  var $emailMsg = '';
  function __toString(){
    return serialize($this);
  }
  function report(){
    $sql = "
      INSERT INTO monitor_log (
          `id` ,`m_id` ,`name` ,`obj` ,`ng`,`created_at` 
          )VALUES (
            NULL ,".$this->id.',\''.$this->name.'\',\''.sql_injection($this).'\','.'1'.',"'.date('Y-m-d H:i:s')."\"
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

    if($haveToDel = ($limit = (int)$res['cnt']-LOG_LIMIT)>0){
      //                          WHERE m_id = ".$this->id."
      $sqlDel = "
        DELETE FROM monitor_log
        order by id limit ".$limit;
      //                echo $sqlDel;
      db_query($sqlDel);
    }
  }

  function isAlive(){
    return strtoupper(trim(file_get_contents($this->url)))=='WE ARE ALIVE.';
  }

}
$domains = getDomains();
$sites= array();

if(count($domains) != 0){
  foreach ($domains as $key=>$domain){
    $cHost= $domain;
    if ($cHost!=FALSE){
      if ($cHost->isAlive()){
        $sites[] = array('name'=>$cHost->name,'alive'=>true,'obj'=>$cHost);
      }else {
        $sites[] = array('name'=>$cHost->name,'alive'=>false,'obj'=>$cHost);
        $loglist[$cHost->name]= $cHost->emailMsg;
        if(!isset($_SERVER["HTTP_USER_AGENT"])){
          //如果页面执行 值显示记录不 生成日志
          $cHost->report();
        }
      }
    }else {
      $sites[] = array('name'=>$cHost->name,'alive'=>false,'obj'=>$cHost,'message'=>'');
    }
    unset($cHost);
  }
}
