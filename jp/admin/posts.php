<?php
require('includes/application_top.php');
$txt = stripslashes(trim($_POST['content']));
$title = stripslashes(trim($_POST['title']));

$color = $_POST['color'];
$time = date('Y-m-d H:i:s');
$zIndex = $_POST['zIndex'];
$xyz = '0|0|'.$zIndex;
$xlen = '150';
$ylen = '150';
$query = tep_db_query("insert into notes(title,content,color,xyz,addtime)values
    ('".$title."','".$txt."','".$color."','".$xyz."|".$xlen."|".$ylen."','".$time."')");
if($query){
  echo tep_db_insert_id()."||".$time;
}else{
  echo TEXT_ERROR;
}
?>
