<?php
require('includes/application_top.php');
//one time pwd 
$http_referer = $_SERVER['HTTP_REFERER'];
$http_referer_arr = explode('?',$_SERVER['HTTP_REFERER']);
$http_referer_arr = explode('admin',$http_referer_arr[0]);
$request_page_name = '/admin'.$http_referer_arr[1];
$request_one_time_sql = "select * from ".TABLE_PWD_CHECK." where page_name='".$request_page_name."'";
$request_one_time_query = tep_db_query($request_one_time_sql);
$request_one_time_arr = array();
$request_one_time_flag = false; 
while($request_one_time_row = tep_db_fetch_array($request_one_time_query)){
  $request_one_time_arr[] = $request_one_time_row['check_value'];
  $request_one_time_flag = true; 
}

if(count($request_one_time_arr)==1&&$request_one_time_arr[0]=='admin'&&$_SESSION['user_permission']!=15){
  if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest"){
    forward401();
  }
}
if (!$request_one_time_flag && $_SESSION['user_permission']!=15) {
  if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
    forward401();
  }
}
if(!in_array('onetime',$request_one_time_arr)&&$_SESSION['user_permission']!=15){
  if(!(in_array('chief',$request_one_time_arr)&&in_array('staff',$request_one_time_arr))){
  if($_SESSION['user_permission']==7&&in_array('chief',$request_one_time_arr)){
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
    }
  }
  if($_SESSION['user_permission']==10&&in_array('staff',$request_one_time_arr)){
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
    }
  }
  }
}
//end one time pwd
if ($_SERVER["HTTP_X_REQUESTED_WITH"] == "XMLHttpRequest") {
$txt = stripslashes(trim($_POST['content']));
$title = stripslashes(trim($_POST['title']));
$author = stripslashes(trim($_POST['author']));
$belong = stripslashes(trim($_POST['belong']));
$attribute = $_POST['attribute'];

$color = $_POST['color'];
$time = date('Y-m-d H:i:s');
$zIndex = $_POST['zIndex'];
if($_COOKIE['tarrow']=="close" ){
$xyz = '20|104|'.$zIndex;
}else{
$xyz = '145|104|'.$zIndex;
}
$xyz = $belong == FILENAME_DEFAULT ? '0|0|'.$zIndex : $xyz;
$xlen = '460';
$ylen = '150';
$query = tep_db_query("insert into notes(title,content,color,xyz,addtime,attribute,author,belong)values
    ('".$title."','".$txt."','".$color."','".$xyz."|".$xlen."|".$ylen."','".$time."','".$attribute."','".$author."','".$belong."')");
if($query){
  echo tep_db_insert_id()."||".$time;
}else{
  echo TEXT_ERROR;
}
}
?>
