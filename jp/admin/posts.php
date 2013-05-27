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

if ($ocertify->npermission == 31) {
  if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
    forward401();
  }
} else {
  if (count($request_one_time_arr) == 1 && $request_one_time_arr[0] == 'admin' && $ocertify->npermission != 15) {
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
    }
  }
  if (!$request_one_time_flag && $ocertify->npermission != 15) {
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
    }
  }
  if (!in_array('onetime', $request_one_time_arr) && $ocertify->npermission != 15) {
    if (!(in_array('chief', $request_one_time_arr) && in_array('staff', $request_one_time_arr))) {
      if($ocertify->npermission == 7 && in_array('chief', $request_one_time_arr)) {
        if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
          forward401();
        }
      }
      if ($ocertify->npermission && in_array('staff', $request_one_time_arr)) {
        if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
          forward401();
        }
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
$xyz = '0|0|'.$zIndex;
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
