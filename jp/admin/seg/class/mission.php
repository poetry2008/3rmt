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

  function start(){
    //判断是否有进程正在进行,如果有则先不开始,询问后再做处理

    require_once CLASS_DIR.$this->engine.'.php';
    require_once CLASS_DIR.$this->dbdriver.'.php';


      //标记开始
        $this->session_id = $this->conn->insert_id;
            //删除一起的 session  和 record 

    $sql = "delete from record where mission_id ='".$this->id."'
           and session_id <> '".$this->session_id."'";
    $this->conn->query($sql);


    $this->dbdriver = new  $this->dbdriver;
    $this->engine = new $this->engine;

    $this->engine->init($this->keyword,$this->page_limit);
    self::msg($this->keyword);
    self::msg('mission start');
    
    self::msg('presearcing');
    
    $this->engine->preSearch();

    self::msg('there is about '.$this->engine->pageCountNumber.' page in result');

    self::msg('presearcing end');
    self::msg('start searching');
    
    $result = 0; 
    while(!$err_code = $this->shouldStop()){
      //      self::msg('searching '.$this->engine->currentPageNumber.'pager'.'<a target="_blank" href = "'.$this->engine->currentUrl.'" >link</a>');
      self::msg('searching '.$this->engine->currentPageNumber.' page');
      $currentResult  = $this->engine->getCurrentPageResult();
      $this->engine->currentPageNumber++;
      $result += $this->save($currentResult,$this->filters);
      if($this->result_limit!=0&&$result >= $this->result_limit){
        $err_code = 4;
        break;
      }
    }
    self::msg('end searcing');
    self::msg($err_code);



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
