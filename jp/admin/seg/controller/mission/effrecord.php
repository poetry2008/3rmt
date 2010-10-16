<?php
$conn = db::getConn();
$mission_id = $_GET['mission_id'];
$sql = "select r.* 
       from record as r left join session_log as s 
       on r.session_id=s.id 
       where s.mission_id= '".$mission_id."' 
       and r.mission_id = '".$mission_id."'
       and r.show = '1'
       and s.forced='0'
       and s.start_at not in ('',0)
       and s.end_at is not null
       group by r.siteurl
       ";
$res = $conn->query($sql);
$records = array();
while($row = $res->fetch_object()){
  $records[] = $row;
}
$template->display(array('records' => $records));
