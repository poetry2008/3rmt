<?php
        
class resultSaver {
  private static $conn;
  function save($resultArray,$Filters){
    var_dump(count($resultArray));
    $this->conn =  self::getConn();
    $smtm =  $this->conn->prepare('insert into record 
        (`mission_id` ,`mission_name` ,`keyword` ,
         `order_number` ,`page_number` ,`order_total_number`,
         `siteurl` ,`fullurl`,`title` ,`description` ,
         `created_at`,`session_id`,`show`)
         values(?,?,?,?,?,?,?,?,?,?,?,?,?)');
  $sum = 0;
  foreach($resultArray as $result){
    $flag = 1;
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
      $smtm->bind_param("dssdddssssddd",$result['mission_id'],$result['mission_name'],$result['keyword'],$result['order_number'],$result['page_number'],$result['order_total_number'],$result['siteurl'],$result['fullurl'],$result['title'],$result['description'],$result['created_at'],$result['session_id'],$flag);
      $smtm->execute();
    if($flag){
      $sum ++;
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
