<?php
require('includes/application_top.php');
$txt = stripslashes(trim($_POST['content']));
/*
$txt = htmlspecialchars($txt, ENT_QUOTES);
$txt = mysql_real_escape_string(strip_tags($txt),$link); //过滤HTML标签，并转义特殊字符
if(strlen($txt)<1 || strlen($txt)>100){
  echo '内容长度为1~100字符之间';
  exit;
}   

$user = stripslashes(trim($_POST['user']));
$user = htmlspecialchars($user, ENT_QUOTES);
$user = mysql_real_escape_string(strip_tags($user),$link);
if(strlen($user)<2 || strlen($user)>30){
  echo '姓名长度为2~10字符之间';
  exit;
}
*/

$color = $_POST['color'];
$time = date('Y-m-d H:i:s');
$zIndex = $_POST['zIndex'];
$xyz = '0|0|'.$zIndex;
$xlen = '150';
$ylen = '150';
$query = tep_db_query("insert into notes(content,color,xyz,addtime)values
    ('".$txt."','".$color."','".$xyz."|".$xlen."|".$ylen."','$time')");
if($query){
  echo tep_db_insert_id();
}else{
  echo TEXT_ERROR;
}
?>
