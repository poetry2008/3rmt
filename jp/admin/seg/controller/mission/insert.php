<?php
require_once('./class/mission.php');
if(isset($_POST['submit'])&&$_POST['submit']=='add'){
  $post = $_POST;
  $mission = mission::getConn();
  $mission->insert($post);
  header("Location: ".$_SERVER['SCRIPT_NAME']);
  exit;
}
$i=0;
$engineSelect = '<select name = "engine">';
foreach(mission::$engineArr as $k => $v){
  if($i==0){
  $engineSelect .= "<option value='".$v."' selected='selected'>";
  }else{
  $engineSelect .= "<option value='".$v."'>";
  }
  $engineSelect .= $k;
  $engineSelect .= "</option>";
  $i++;
}
$engineSelect .= "</select>";
$template->display(array('engineSelect' => $engineSelect));
