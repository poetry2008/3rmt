<?php
/*
  $Id$
*/
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
      if ($ocertify->npermission == 7 && in_array('chief', $request_one_time_arr)) {
        if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
          forward401();
        }
      }
      if ($ocertify->npermission == 10 && in_array('staff', $request_one_time_arr)) {
        if ($_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
          forward401();
        }
      }
    }
  }
}
//end one time pwd
$form_id = $_POST['form_id'];
$oid = $_GET['oID'];
if(isset($_GET['fake']) or isset($_POST['fake'])){
  $fake = $_GET['fake'];
  if(!$fake){
    $fake = $_POST['fake'];
  }
}
if($_POST['oID']){
$oid = $_POST['oID'];
}
if(strpos($oid,'_')){
  $mulit = true;
  $oids = substr($oid,0,-1);
  $oidArray = explode('_',$oid);
  foreach($oidArray as $key=>$value){
    $oidsString .= "'".$value."'";
    if($key!=count($oidArray)-1) {
      $oidsString .=',';
    }
  }
  unset($_POST['oID']);
}

unset($_POST['form_id']);
foreach ($_POST as $key=> $value){
  if (substr($key,0,1)=='0' and !$_GET['withz']){
    continue;
  }
  $ids = explode('_',$key);
  $item_id = $ids['3'];
  $group_id = $ids['2'];
  if(!$mulit ){
    tep_db_query("delete from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id = '".$_GET['oID']."' and form_id='".$form_id."'"." and item_id='".$item_id."'"." and group_id = '".$group_id."'");
  }else{
    tep_db_query("delete from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id in (".$oidsString.") and form_id='".$form_id."'"." and item_id='".$item_id."'"." and group_id = '".$group_id."'");
  }
  //针对 date 做特殊处理
  if($_GET['fix']=='date'){
    $value = date('Y/m/d H:i',time());
  }
  if($_GET['fix']=='user' ){
    $user_info = tep_get_user_info($ocertify->auth_user);
    $value =$user_info['name'];
  }
  if(!$mulit and !$fake){
    tep_db_query("insert into `".TABLE_PREORDERS_OA_FORMVALUE."` values(NULL, '".$oid."', '".$form_id."', '".$item_id."', '".$group_id."', '".$key."','".$value."')");
  }else{
    if(!$fake){
    foreach($oidArray as $oid){
      if($oid!=''){
      echo ("insert into `".TABLE_PREORDERS_OA_FORMVALUE."` values(NULL, '".$oid."', '".$form_id."', '".$item_id."', '".$group_id."', '".$key."','".$value."')");
      tep_db_query("insert into `".TABLE_PREORDERS_OA_FORMVALUE."` values(NULL, '".$oid."', '".$form_id."', '".$item_id."', '".$group_id."', '".$key."','".$value."')");
      }
    }}
  }
  if( $_GET['withz']){
    echo $value;
  }

}

