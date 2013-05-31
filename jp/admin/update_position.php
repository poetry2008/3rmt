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
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest"){
      forward401();
    }
  }
  if (!$request_one_time_flag && $ocertify->npermission !=15) {
    if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
      forward401();
    }
  }
  if (!in_array('onetime', $request_one_time_arr) && $ocertify->npermission != 15) {
    if (!(in_array('chief', $request_one_time_arr) && in_array('staff', $request_one_time_arr))) {
      if ($ocertify->npermission == 7 && in_array('chief', $request_one_time_arr)) {
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
if(is_numeric($_GET['id']) && is_numeric($_GET['x']) && is_numeric($_GET['y']) &&
    is_numeric($_GET['z'])){
$id = intval($_GET['id']);
$x = intval($_GET['x']);
$y = intval($_GET['y']);
$z = intval($_GET['z']);
$query = tep_db_query("select * from notes where id = '".$id."'");
$row = tep_db_fetch_array($query);
list($left,$top,$zindex,$xlen,$ylen) = explode('|',$row['xyz']);
tep_db_query("UPDATE notes SET xyz='".$x."|".$y."|".$z."|".$xlen."|".$ylen."' WHERE id=".$id);

echo "1";
}else if(isset($_GET['del_note'])&&$_GET['del_note']&&is_numeric($_GET['id']))
{
tep_db_query("delete from notes  WHERE id=".$_GET['id']);
tep_db_query("OPTIMIZE TABLE  `notes`");
}else if(isset($_POST['action'])&&$_POST['action']=='change_move'){
$query = tep_db_query("select * from notes where id = '".$id."'");
$row = tep_db_fetch_array($query);
list($left,$top,$zindex,$xlen,$ylen) = explode('|',$row['xyz']);
$xlen=$_POST['xlen'];
$ylen=$_POST['ylen'];
tep_db_query("UPDATE notes SET xyz='".$left."|".$top."|".$zindex."|".$xlen."|".$ylen."' WHERE id=".$id);
}else if(isset($_POST['action'])&&$_POST['action']=='save_text'){
  $id = $_POST['id'];
  $text = $_POST['text'];
  $time = date('Y-m-d H:i:s');
  $query  = tep_db_query("update notes set
      content='".trim($text)."', addtime='".$time."' where id =
      '".$id."'");
  if($query){
    $now_sql = "select * from notes where id='".$id."'";
    $now_query = tep_db_query($now_sql);
    $now_res = tep_db_fetch_array($now_query);
    echo
      "true|||".$now_res['title']."|||".substr($now_res['addtime'],0,strlen($now_res['addtime'])-3)."|||".$now_res['content'];
  }
}
?>
