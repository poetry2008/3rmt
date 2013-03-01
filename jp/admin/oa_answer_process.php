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
//判断请求是否成功，如果失败，终止程序，并返回错误信息
if(!isset($_POST['eof'])||$_POST['eof']!='eof'){
  echo 'eof_error';
  exit;
}
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
unset($_POST['eof']);
$stock_flag = $_POST['stock_flag'];
unset($_POST['stock_flag']);
foreach ($_POST as $key=> $value){
  if (substr($key,0,1)=='0' and !$_GET['withz']){
    continue;
  }
  $ids = explode('_',$key);
  $item_id = $ids['3'];
  $group_id = $ids['2'];
  //针对 stock 做特殊处理
  $oa_item_query = tep_db_query("select `type` item_type from ". TABLE_OA_ITEM ." where id='".$item_id."'");
  $oa_item_array = tep_db_fetch_array($oa_item_query);
  tep_db_free_result($oa_item_query);
  if(!$mulit ){
    if(!($oa_item_array['item_type'] == 'autocalculate' && $stock_flag == '0')){
      tep_db_query("delete from ".TABLE_OA_FORMVALUE." where orders_id = '".$_GET['oID']."' and form_id='".$form_id."'"." and item_id='".$item_id."'"." and group_id = '".$group_id."'");
    }
  }else{
    if(!($oa_item_array['item_type'] == 'autocalculate' && $stock_flag == '0')){
      tep_db_query("delete from ".TABLE_OA_FORMVALUE." where orders_id in (".$oidsString.") and form_id='".$form_id."'"." and item_id='".$item_id."'"." and group_id = '".$group_id."'");
    }
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
    if($oa_item_array['item_type'] == 'autocalculate'){

      if($stock_flag == '1'){
        tep_db_query("insert into `".TABLE_OA_FORMVALUE."` values(NULL, '".$oid."', '".$form_id."', '".$item_id."', '".$group_id."', '".$key."','".$value."')");
      }
    }else{
      tep_db_query("insert into `".TABLE_OA_FORMVALUE."` values(NULL, '".$oid."', '".$form_id."', '".$item_id."', '".$group_id."', '".$key."','".$value."')"); 
    }
  }else{
    if(!$fake){
    foreach($oidArray as $oid){
      if($oid!=''){
      echo ("insert into `".TABLE_OA_FORMVALUE."` values(NULL, '".$oid."', '".$form_id."', '".$item_id."', '".$group_id."', '".$key."','".$value."')");
      if($oa_item_array['item_type'] == 'autocalculate'){

        if($stock_flag == '1'){
          tep_db_query("insert into `".TABLE_OA_FORMVALUE."` values(NULL, '".$oid."', '".$form_id."', '".$item_id."', '".$group_id."', '".$key."','".$value."')");
        }
      }else{

        tep_db_query("insert into `".TABLE_OA_FORMVALUE."` values(NULL, '".$oid."', '".$form_id."', '".$item_id."', '".$group_id."', '".$key."','".$value."')");
      }
      }
    }}
  }
  if( $_GET['withz']){
    echo $value;
  }

}

