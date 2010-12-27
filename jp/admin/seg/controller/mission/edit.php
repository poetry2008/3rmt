<?php
require_once('./class/mission.php');
if(isset($_POST['submit'])&&$_POST['submit']=="update"){
  $post=$_POST;
  $mission = mission::getObj($post['id']);
  $mission->edit($post);
  header("Location: ".$_SERVER['SCRIPT_NAME']);
  exit;
}
$conn = db::getConn();
$mid = $_GET['mission_id'];
$sql = "select * from mission where id ='".$mid."'";
$res = $conn->query($sql);
$row = $res->fetch_object();
$engineSelect = '<select name = "engine">';
foreach(mission::$engineArr as $k => $v){
  if($row->engine == $v){
  $engineSelect .= "<option value='".$v."' selected='selected'>";
  }else{
  $engineSelect .= "<option value='".$v."'>";
  }
  $engineSelect .= $k;
  $engineSelect .= "</option>";
}
$engineSelect .= "</select>";
$template->display(array('row' => $row,'engineSelect' => $engineSelect));
