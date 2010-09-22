<?php
require_once("./class/helper_pager.php");
$pagesize = 20;
$page = isset($_GET['page'])&&$_GET['page']!=''?$_GET['page']:1;
if (isset($_GET['mission_id'])){
  $conditions = "s.mission_id='".$_GET['mission_id']."'";
}else{
  $conditions = "1";
}
$pageurl = "index.php?action=index&controller=session";
$tables = "session_log s left join mission m on s.mission_id=m.id";
$pager = new helper_pager($tables,$page,$pagesize,$conditions);
$find = "s.id s_id,s.*,m.*";
$allres = $pager->find($find);
/*
$conn = db::getConn();
if(isset($_GET['mission_id'])){
$sql = "select * from session_log where mission_id='".$_GET['mission_id']."' order by id desc";
}else{
$sql = "select * from session_log order by id desc";
}
$result = $conn->query($sql);
$sessions = array();
$missions = array();
while($session = $result->fetch_object()){
  $mission_id = $session->mission_id;
  $sql = "select * from mission where id = '".$mission_id."'";
  $res = $conn->query($sql);
  $missions[] = $res->fetch_object();
  $sessions[] = $session;
}
$template->display(array('sessions' => $sessions,'missions' => $missions,'allres'=>$arr));
*/
$template->display(array('allres' => $allres,'pager'=>$pager,'pageurl'=>$pageurl));
