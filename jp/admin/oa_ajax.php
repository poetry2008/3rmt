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
    require_once(DIR_WS_CLASSES. 'payment.php'); 
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
          $orders_raw = tep_db_query("select customers_id, payment_method, site_id from ".TABLE_ORDERS." where orders_id = '".$id."'"); 
          $orders_res = tep_db_fetch_array($orders_raw); 
          if ($orders_res) {
            $tmp_setting = get_configuration_by_site_id_or_default('MODULE_ORDER_TOTAL_POINT_STATUS', $orders_res['site_id']);
            $tmp_tmp_setting = get_configuration_by_site_id_or_default('MODULE_ORDER_TOTAL_POINT_ADD_STATUS', $orders_res['site_id']);
            if ($tmp_setting == 'true' && $tmp_tmp_setting != '0') {
              $cpayment = payment::getInstance($orders_res['site_id']); 
              if ($cpayment->admin_is_get_point(payment::changeRomaji($orders_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $orders_res['site_id']) == '1') {
                $point_rate = $cpayment->admin_get_point_rate(payment::changeRomaji($orders_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $orders_res['site_id']); 
                
                $get_point = $cpayment->admin_calc_get_point(payment::changeRomaji($orders_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $id, $point_rate, $orders_res['site_id']);
                
                tep_db_query("update `".TABLE_CUSTOMERS."` set point = point + ".(int)$get_point." where customers_id = '".$orders_res['customers_id']."' and customers_guest_chk = '0'"); 
              }
              unset($cpayment); 
            }
          }
          $result = tep_db_query("update `".TABLE_ORDERS."` set `end_user` = '".$value."', `flag_qaf` = ".'1'." where orders_id = '".$id."'");  
          if($_POST['finish']){
            $messageStack->add_session(sprintf(MESSAGE_FINISH_ORDER_TEXT,$id) , 'success');
          }
        }
      }
    }else {
      $orders_raw = tep_db_query("select customers_id, payment_method, site_id from ".TABLE_ORDERS." where orders_id = '".$id."'"); 
      $orders_res = tep_db_fetch_array($orders_raw); 
      if ($orders_res) {
        $tmp_setting = get_configuration_by_site_id_or_default('MODULE_ORDER_TOTAL_POINT_STATUS', $orders_res['site_id']);
        $tmp_tmp_setting = get_configuration_by_site_id_or_default('MODULE_ORDER_TOTAL_POINT_ADD_STATUS', $orders_res['site_id']);
        if ($tmp_setting == 'true' && $tmp_tmp_setting != '0') {
          $cpayment = payment::getInstance($orders_res['site_id']); 
          if ($cpayment->admin_is_get_point(payment::changeRomaji($orders_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $orders_res['site_id']) == '1') {
            $point_rate = $cpayment->admin_get_point_rate(payment::changeRomaji($orders_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $orders_res['site_id']); 
            
            $get_point = $cpayment->admin_calc_get_point(payment::changeRomaji($orders_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $id, $point_rate, $orders_res['site_id']);
                
            tep_db_query("update `".TABLE_CUSTOMERS."` set point = point + ".(int)$get_point." where customers_id = '".$orders_res['customers_id']."' and customers_guest_chk = '0'"); 
          }
        }
      }
      $result = tep_db_query("update `".TABLE_ORDERS."` set `end_user` = '".$value."', `flag_qaf` = ".'1'." where orders_id = '".$id."'");  
    }
//tep_redirect(tep_href_link(FILENAME_ORDERS, 'oID='.$_GET['oID'].'&action=edit'));

    break;
  }
}
