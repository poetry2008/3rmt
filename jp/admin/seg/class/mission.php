<?php

class mission {
  public static $engineArr = array('yahoo' => 'yahoo',
                                       'google' => 'google',
                                       );
  var $engineDriver;
  var $keyword;
  var $id;
  var $name;
  var $page_limit;
  var $result_limit;
  var $enabled;
  var $dbdriver='resultSaver';
  public  static function getObj($id){
    $db = db::getConn();
    $r = $db->query('select * from mission where id='.$id);
    $m =  $r->fetch_object("mission");
    $r = $db->query('select distinct(record_siteurl) siteurl 
                     from site_filter where state = 1');
    $arr = array();
    while($row = $r->fetch_object()){
      $arr[] = $row->siteurl;
    }
    $m->conn= $db;
    $m->filters= $arr;
    return $m;
    } 
  public static function getConn(){
    $m = new mission();
    $m->conn = db::getConn();
    return $m;
  }
  function __construct(){
  }

  public static function msg($message='')
  {
    if(!CLI){

    echo date('H:i:s',time()),str_repeat(' ',5);
    echo $message."</br>";
    ob_flush();
    flush();
    }else {
      cron_log($message);

    }
  }
  function couldStart(){
    $sql = 'select * from session_log s ,mission m 
      where m.enabled = 1 
      and s.end_at = 0 
      and s.forced = 0 
      and s.mission_id = m.id 
      and s.mission_id ="'.$this->id.'"';
   $r =  $this->conn->query($sql);
   // 如果大于一行 ,说明可以开始
   if($r->num_rows > 0){
     return false;
   }else {
     $this->session = $r->fetch_Object();
     return true;
   }

  }

  function start(){
    //判断是否有进程正在进行,如果有则先不开始,询问后再做处理

    require_once CLASS_DIR.$this->engine.'.php';
    require_once CLASS_DIR.$this->dbdriver.'.php';

    if (!$this->couldStart()){
      if(CLI){
        $this->msg('there is a process running');
        return 0;
      }else {
      die('there is a process loading ');
      }
      
    }else {
      //标记开始
        $this->conn->query('insert into session_log  (`mission_id`,`start_at`) values ( "'.$this->id.'" ,"'.time().'" )');
        $this->session_id = $this->conn->insert_id;
    }

    $this->dbdriver = new  $this->dbdriver;
    $this->engine = new $this->engine;

    $this->engine->init($this->keyword,$this->page_limit);
    $this::msg($this->keyword);
    $this::msg('mission start');
    
    $this::msg('presearcing');
    
    $this->engine->preSearch();

    $this::msg('there is about '.$this->engine->pageCountNumber.' page in result');

    $this::msg('presearcing end');
    $this::msg('start searching');
    
    $result = 0; 
    while(!$err_code = $this->shouldStop()){
      //      $this::msg('searching '.$this->engine->currentPageNumber.'pager'.'<a target="_blank" href = "'.$this->engine->currentUrl.'" >link</a>');
      $this::msg('searching '.$this->engine->currentPageNumber.' page');
      $currentResult  = $this->engine->getCurrentPageResult();
      $this->engine->currentPageNumber++;
      $result += $this->save($currentResult,$this->filters);
      if($this->result_limit!=0&&$result >= $this->result_limit){
        $err_code = 4;
        break;
      }
    }
    $this::msg('end searcing');
    $this::msg($err_code);

    //结束后需要标记结束
    $sql = 'update session_log set end_at ='.time()
           .',stopation="'.$err_code.'" where id =
           '.$this->session_id;
    $this->conn->query($sql);

    return 1;
  } 



  function shouldStop(){
    $stop_condiction_1 = 1;
    $stop_condiction_2 = 2;
    if ($this->engine->currentPageNumber < $this->engine->pageCountNumber){
      $stop_condiction_1 = false;
    }
    
    if ($this->engine->currentPageNumber <= $this->page_limit){
      $stop_condiction_2 = false;
    }else if($this->page_limit == 0){
      $stop_condiction_2 = false;
    }
    $sql = 'select count(id) sum from session_log where  forced=1 
           and end_at <> 0 and id = '.$this->session_id;
    $res = $this->conn->query($sql);
    $row = $res->fetch_object();
    if($row->sum >= 1){
      return 3;
    }
    if($stop_condiction_2){
      return $stop_condiction_2;
    }else if($stop_condiction_1){
      return $stop_condiction_1;
    }else{
    return false;
    }

  }
  function save($resultArray,$Filters){
    $newarray = array();
    foreach ($resultArray as $result){
      if(!is_null($result['title'])){
        $newarray[] = array_merge($result,array('mission_id'=>$this->id,'mission_name'=>$this->name,'session_id'=>$this->session_id));
        }
    }
    return $this->dbdriver->save($newarray,$Filters);
  }
  //取得当前任务状态
  function getStatus($id){
    $conn = db::getConn();
    $sql = "select * from session_log where
           mission_id='".$id."' 
           and end_at=0
           and forced=0
           order by id desc";
    $res = $conn->query($sql);
    $row = $res->fetch_object();
    if($row){
      return 'run';
    }
    return false;
  }
  
  function stop(){
    $sql = "select id from session_log where forced=0 and end_at=0
           and
           mission_id='".$this->id."'
           order by id desc";
    $res = $this->conn->query($sql);
    $str = '';
    while($row = $res->fetch_object()){
      $str .= "'".$row->id."',";
    }
    $str = substr($str,0,-1);
    $sql = "update session_log set
           end_at='".time()."',
           stopation=3,
           forced=1 where
           id in (".$str.")";
    $this->conn->query($sql);
  }
  function insert($arr){
    $sql = "insert into mission 
           (`id`,`name`,`keyword`,`page_limit`,
            `result_limit`,`enabled`,`engine`)
           values
           ('','".$arr['name']."','".$arr['keyword']."','".
            $arr['page_limit']."','".$arr['result_limit']."','".
            $arr['enabled']."','".$arr['engine']."')";
    $this->conn->query($sql);
  }
  function edit($arr){
    $sql = "update mission set
            name='".$arr['name']."',
            keyword='".$arr['keyword']."',
            page_limit='".$arr['page_limit']."',
            result_limit='".$arr['result_limit']."',
            enabled='".$arr['enabled']."',
            engine='".$arr['engine']."' 
            where `id`='".$arr['id']."'";
    $this->conn->query($sql);
  } 
  function delete($mid){
    $sql = "delete from mission 
           where `id`='".$mid."'";
    $this->conn->query($sql);
  } 
  function editForEnabled($mid){
    $sql = "select enabled from mission where id ='".$mid."'";
    $res = $this->conn->query($sql);
    $row = $res->fetch_object();
    if($row->enabled){
      $sql = "update mission set enabled = '0' where id='".$mid."'";
    }else{
      $sql = "update mission set enabled = '1' where id='".$mid."'";
    }
    $this->conn->query($sql);
  }
}
