<?php
        
class resultSaver {
  private static $conn;
  function save($resultArray,$Filters){
    $this->conn =  self::getConn();
//`fullurl`,`title` ,`description` , 删除了这三个列
    $smtm =  $this->conn->prepare('insert into record 
        (`mission_id` ,`mission_name` ,`keyword` ,
         `order_number` ,`page_number` ,`order_total_number`,
         `siteurl` ,
         `created_at`,`session_id`,`show`)
         values(?,?,?,?,?,?,?,?,?,?)');
  $sum = 0;
  foreach($resultArray as $result){
    $flag = 1;
    $distinct_sql = 'select distinct(siteurl) as url from record 
                     where mission_id ="'.$result['mission_id'].'"';
    $distinct_res = $this->conn->query($distinct_sql);
    if($distinct_res){
      $distinct_arr = array();
      while($row = $distinct_res->fetch_Object()){
        if($result['siteurl'] == $row->url){
        $flag = 0;
        break;
        }
      }
    }
    $count_sql = 'select count(distinct(siteurl)) as count_url from record
                     where mission_id ="'.$result['mission_id'].'"';
    $count_res = $this->conn->query($count_sql);
    if($count_res){
      $count_row = $count_res->fetch_Object();
      if($count_row->count_url >= 10){
        $flag = 0;
      }
    }
    foreach( $Filters as $filter){
      if($filter == $result['siteurl']){
        $flag = 0;
        break;
      }
    }
/*
//输出插入数据库的所有 siteurl 以及状态
echo "<br>--------------------<br>";
var_dump($flag);
var_dump($result['siteurl']);
echo "<br>--------------------<br>";
*/
      //$smtm->bind_param("dssdddssssddd",$result['mission_id'],$result['mission_name'],$result['keyword'],$result['order_number'],$result['page_number'],$result['order_total_number'],$result['siteurl'],$result['fullurl'],$result['title'],$result['description'],$result['created_at'],$result['session_id'],$flag);
      $smtm->bind_param("dssdddsddd",$result['mission_id'],$result['mission_name'],$result['keyword'],$result['order_number'],$result['page_number'],$result['order_total_number'],$result['siteurl'],$result['created_at'],$result['session_id'],$flag);
      $sum ++;
    if($flag){
      $smtm->execute();
    }
    if($sum == 10){
      break;
    }
  }
    $smtm->close();
    return $sum;
  }
  public static function getConn(){
    if (self::$conn == NULL){
      self::$conn = db::getConn();
    }
    return self::$conn;
    
  }

}
