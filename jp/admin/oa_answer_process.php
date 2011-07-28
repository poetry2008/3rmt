<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  //tep_db_query("delete from ".TABLE_OA_FORMVALUE." where orders_id = '".$_GET['oID']."'"); 
  //foreach ($_POST as $key => $value) {
    //if (preg_match('/^(tif)\d{1,}$/', $key)) {
      //$ga_arr = explode('_', $value);
      //$item_id = $ga_arr[0];
      //$form_id = $ga_arr[1];
      //$item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".$item_id."'"); 
      //$item_res = tep_db_fetch_array($item_raw); 
      //$group_id = $item_res['group_id']; 
      //$item_option = unserialize($item_res['option']);
      //$info_value = $_POST['ti'.$item_id]; 
  
    //}
  //}

  $form_id = $_POST['form_id'];
  $oid = $_GET['oID'];
  unset($_POST['form_id']);

  foreach ($_POST as $key=>$value){
    if (substr($key,0,1)=='0' and !$_GET['withz']){
      continue;
    }
  $ids = explode('_',$key);
  $item_id = $ids['3'];
  $group_id = $ids['2'];

  tep_db_query("delete from ".TABLE_OA_FORMVALUE." where orders_id = '".$_GET['oID']."' and form_id='".$form_id."'"." and item_id='".$item_id."'"." and group_id = '".$group_id."'");
  //针对 date 做特殊处理
  if($_GET['fix']=='date'){
  $value = date('Y/m/d H:i',time());
  }
  if($_GET['fix']=='user'){
    $user_info = tep_get_user_info($ocertify->auth_user);
    $value =$user_info['name'];
  }

  tep_db_query("insert into `".TABLE_OA_FORMVALUE."` values(NULL, '".$oid."', '".$form_id."', '".$item_id."', '".$group_id."', '".$key."','".$value."')");
  if( $_GET['withz']){
    echo $value;
  }

}

//tep_redirect(tep_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID'].'&action=edit'));

