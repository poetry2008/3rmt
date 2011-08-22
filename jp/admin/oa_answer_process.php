<?php
/*
  $Id$
*/
require('includes/application_top.php');
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
    tep_db_query("delete from ".TABLE_OA_FORMVALUE." where orders_id = '".$_GET['oID']."' and form_id='".$form_id."'"." and item_id='".$item_id."'"." and group_id = '".$group_id."'");
  }else{
    tep_db_query("delete from ".TABLE_OA_FORMVALUE." where orders_id in (".$oidsString.") and form_id='".$form_id."'"." and item_id='".$item_id."'"." and group_id = '".$group_id."'");
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
    tep_db_query("insert into `".TABLE_OA_FORMVALUE."` values(NULL, '".$oid."', '".$form_id."', '".$item_id."', '".$group_id."', '".$key."','".$value."')");
  }else{
    if(!$fake){
    foreach($oidArray as $oid){
      echo ("insert into `".TABLE_OA_FORMVALUE."` values(NULL, '".$oid."', '".$form_id."', '".$item_id."', '".$group_id."', '".$key."','".$value."')");
      tep_db_query("insert into `".TABLE_OA_FORMVALUE."` values(NULL, '".$oid."', '".$form_id."', '".$item_id."', '".$group_id."', '".$key."','".$value."')");
    }}
  }
  if( $_GET['withz']){
    echo $value;
  }
}

//tep_redirect(tep_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID'].'&action=edit'));

