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
if(isset($_GET['action'])){
  switch ($_GET['action']){
  case 'updateoaorder':
    $id = $_GET['id'];
    $order = substr($_GET['order'],1);
    $result = tep_db_query("update `".TABLE_OA_FORM_GROUP."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    break;
    //    var_dump($order);
  case 'updategrouporder':     
    $id = $_GET['id'];
    $order = substr($_GET['order'],1);
    $result = tep_db_query("update `".TABLE_OA_GROUP."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    //    var_dump("update `".TABLE_OA_ITEM."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    break;

  case 'updateitemorder':     
    $id = $_GET['id'];
    $order = substr($_GET['order'],1);
    $result = tep_db_query("update `".TABLE_OA_ITEM."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    //    var_dump("update `".TABLE_OA_ITEM."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    break;
  case 'getTime':
    echo date('Y/m/d H-i' ,time());
    break;
  case 'finish':

    $id = $_GET['oID'];
    if(!$id){
      $ids = $_POST['oID'];
    }
    if (strpos($ids,'_')){
      $m = true;
      $oids = explode('_',$ids);
    }
    $user_info = tep_get_user_info($ocertify->auth_user);
    $value =$user_info['name'];
    if($m){
      foreach ($oids as $id){
        if(trim($id)!=''){
        $result = tep_db_query("update `".TABLE_ORDERS."` set `end_user` = '".$value."', `flag_qaf` = ".'1'." where orders_id = '".$id."'");  
        if($_POST['finish']){
          $messageStack->add_session('注文ID'.$id . 'の成功：取り引きが完了致しました', 'success');
        }

        }
      }
    }else {
      $result = tep_db_query("update `".TABLE_ORDERS."` set `end_user` = '".$value."', `flag_qaf` = ".'1'." where orders_id = '".$id."'");  
    }
//tep_redirect(tep_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID'].'&action=edit'));

    break;
  }
}
