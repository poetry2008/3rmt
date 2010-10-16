#!/usr/bin/env php
<?php
define('PRO_ROOT_DIR','/home/szn/project/3rmt/jp/admin/seg/');
ini_set('display_errors', 'On');
error_reporting(E_ALL);
//加载公用库页面
require_once(PRO_ROOT_DIR."./config/db.config.php");
require_once(PRO_ROOT_DIR."./class/db.php");
require_once(PRO_ROOT_DIR."./class/mission.php");
define('LOG_FILE_NAME',LOG_DIR.date('Y-m-d_H_i_s',time()).'.log');
define('LOG_FILE_NAME_LAST',LOG_DIR.'last.log');
function cron_log($message){
  //如果文件不存在则建立

  if(!file_exists(LOG_FILE_NAME)){
  echo 'file not exist ,creating';
  $handle=fopen(LOG_FILE_NAME,"w"); //创建文件
  fclose($handle);
  }
  if (!file_exists(LOG_FILE_NAME)){
    echo 'create file failed'.LOG_FILE_NAME;
  }else {
    $str_write = '';
    $str_write .=date('H:i:s',time()).str_repeat(' ',5);
    $str_write .= $message."\n";

    $handle=fopen(LOG_FILE_NAME,"a+");
        //写日志
    echo $str_write;
    if(!fwrite($handle,$str_write)){//写日志失败
      echo "failed to write log";
    fclose($handle);
    }

  }
  //将message写入文件 
}
function getResult($sql){
  $conn = db::getConn();
  $result = $conn->query($sql);
  if ($conn->error){
      die($conn->error);
  }else{
    while($line = $result->fetch_array()){
      $rows[] = $line;
    }
    return $rows;
  }
}
//开启日志 以当前的时间为日志名字  
//LOG 某年某月某日 开始任务
//选出所有需要执行的categories
$sql = '
SELECT cm.categories_id as cid ,cm.mission_id, m.id, m.keyword AS mkey, cm.keyword AS cmkey, cm.keyword !=
m.keyword AS needupdate,cm.mission_id=0 as needinsert, isnull(m.id) and
cm.mission_id<> 0 as
needrebuild 
FROM categories_to_mission cm
LEFT JOIN mission m ON cm.mission_id = m.id
';
$ctms = getResult($sql);

cron_log('There is '.count($ctms).'  missions to process');
$needinsert=0;
$needupdate=0;
$needrebuild=0;

foreach($ctms as $key=> $ctm){
//需要插入新mission
  if ($ctm['needinsert'] ){
      $mid = addMission($ctm);
      $ctms[$key]['id']= $mid;
      $needinsert++;
      cron_log('inserted mission category'.$ctm['cid']);
  }
  if ($ctm['needupdate']){
      updateMission($ctm);
      $needupdate++;
      cron_log('updated mission category'.$ctm['cid']);
  }
  if ($ctm['needrebuild']){
      $mid = addMission($ctm,true);
      $ctms[$key]['id']= $mid;
      $needrebuild++;
      cron_log('rebuilded mission category'.$ctm['cid']);
  }
}

cron_log('INSERTED: '.$needinsert);
cron_log('UPDATED: '.$needupdate);
cron_log('REBUILD: '.$needrebuild);
//执行任务
cron_log('');
cron_log('MISSIONS  START');
cron_log('');

foreach ($ctms as $ctm){
  $m = mission::getObj($ctm['id']);
  cron_log('SUBMISSION '.$ctm['id'] .' starting');
cron_log('');
  if(trim($m->keyword)!=''){
  $end =  $m->start();
  }else{
  cron_log('MISSION '.$ctm['id'].'KEYWORD IS NULL');  
  }
  if($end){
  cron_log($ctm['id'] .' OK');
  }else {
  cron_log($ctm['id'] .' FAIED');
  }
cron_log('');
  
}
copy(LOG_FILE_NAME,LOG_FILE_NAME_LAST);
chmod(LOG_FILE_NAME_LAST,0777);

//根据 ctm数据新建一mission 返回mission id 并更新对应的 ctm 
function addMission($ctm,$rebuild=false){
  $defaultMission = array(
      "name"=>'category '.$ctm['cid'],
      "keyword"=>$ctm['cmkey'],
      "page_limit"=>"5",
      "result_limit"=>"50",
      "enabled"=>1,
      "engine"=>"google",
      );
  $d = $defaultMission;
  if ($rebuild){
    $rebuild = $ctm['mission_id'];
  }else {
    $rebuild = 'NULL';
  }
  $sql = 'insert into mission (id,name,keyword,page_limit,result_limit,enabled,engine)
    values('.$rebuild.',?,?,?,?,?,?)'; 
  $db = db::getConn();
  $m = $db->prepare($sql);

  $type = '';
  foreach($d as $key=>$value){
    $type .=is_numeric($value)?'i':'s';
  }
  $m->bind_param($type,$d['name'],$d['keyword'],$d['page_limit'],$d['result_limit'],$d['enabled'],$d['engine']);
  $m->execute();
  $mission_id =$m->insert_id;
  

  $db->query('update categories_to_mission set mission_id = '.$mission_id.' where
      categories_id = '.$ctm['cid']);
  return $mission_id;

}
//根据 ctm 数据 更新 mission
function updateMission($ctm){
  $db = db::getConn();
  $db->query('update mission set keyword="'.$ctm['cmkey'].'" where id ='.$ctm['id']);
  if($db->error){
    echo $db->error;
    cron_log($db->error);
  }
}
