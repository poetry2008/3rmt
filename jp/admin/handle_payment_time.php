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

$order_id = $_POST['oID'];
$status_id = $_POST['stid'];
if (isset($_POST['once_pwd'])) {
  tep_insert_pwd_log($_POST['once_pwd'],$ocertify->auth_user);
}
$orders_status_query = tep_db_query("select * from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_history_id = '".$status_id."'");
$orders_status_res = tep_db_fetch_array($orders_status_query);
if ($orders_status_res) {
  if ($orders_status_res['orders_status_id'] == '9') {
    $del_confirm_payemnt_time_query = tep_db_query("UPDATE `".TABLE_ORDERS."` set `confirm_payment_time` = '0000-00-00 00:00:00' where orders_id = '".$order_id."'");
  }
  
  $other_status_query = tep_db_query("select * from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_history_id != '".$status_id."' and orders_id = '".$order_id."' order by date_added desc limit 1");
  $other_status_res = tep_db_fetch_array($other_status_query); 
  
  if ($other_status_res) {
    $status_info_query = tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$other_status_res['orders_status_id']."'"); 
    $status_info_res = tep_db_fetch_array($status_info_query);
    if ($status_info_res) {
      tep_db_query("update `".TABLE_ORDERS."` set `orders_status` = '".$other_status_res['orders_status_id']."', `orders_status_name` = '".$status_info_res['orders_status_name']."' where orders_id = '".$order_id."'"); 
    }
  } else {
    $status_info_query = tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".DEFAULT_ORDERS_STATUS_ID."'"); 
    $status_info_res = tep_db_fetch_array($status_info_query);
    if ($status_info_res) {
      tep_db_query("update `".TABLE_ORDERS."` set `orders_status` = '".DEFAULT_ORDERS_STATUS_ID."', `orders_status_name` = '".$status_info_res['orders_status_name']."' where orders_id = '".$order_id."'"); 
    }
  }
  //销售处理
  $orders_oa_flag = false;
  $orders_status_flag = false;
  $end_orders_status_flag = false;
  $orders_status_id_flag = false;
  $status_list_array = array();
  $orders_status_finish_query = tep_db_query("select orders_status_id,finished from ". TABLE_ORDERS_STATUS);
  while($orders_status_finish_array = tep_db_fetch_array($orders_status_finish_query)){

        $status_list_array[$orders_status_finish_array['orders_status_id']] = $orders_status_finish_array['finished'];
  }
  tep_db_free_result($orders_status_finish_query);
  $orders_oa_flag = tep_orders_finishqa(tep_db_input($order_id)) == 1 ? true : $orders_oa_flag; 
  $orders_status_flag = $status_list_array[$orders_status_res['orders_status_id']] == 1 ? true : $orders_status_flag;

  //获取前一个订单状态
  $orders_status_id_query = tep_db_query("select orders_status_history_id,orders_status_id from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".tep_db_input($order_id)."' order by date_added desc limit 0,2");
  $orders_i = 0;
  while($orders_status_id_array = tep_db_fetch_array($orders_status_id_query)){

    if($orders_i == 0){

      $orders_status_id = $orders_status_id_array['orders_status_history_id'];
    }
    if($orders_i == 1){

      $orders_status_end_id = $orders_status_id_array['orders_status_id'];
    }
    $orders_i++;
  }
  tep_db_free_result($orders_status_id_query);
  $orders_status_id_flag = $status_id == $orders_status_id ? true : $orders_status_id_flag;
  $end_orders_status_flag = $status_list_array[$orders_status_end_id] == 1 ? true : $end_orders_status_flag;

  if($orders_oa_flag == true && $orders_status_flag == false && $end_orders_status_flag == true && $orders_status_id_flag == true){

        $orders_products_query = tep_db_query("select products_id,products_quantity from ". TABLE_ORDERS_PRODUCTS ." where orders_id='".tep_db_input($order_id)."'");
        while($orders_products_array = tep_db_fetch_array($orders_products_query)){
          tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%d', $orders_products_array['products_quantity']) . " where products_id = '" . (int)$orders_products_array['products_id'] . "'");
        }
        tep_db_free_result($orders_products_query);
  }

  if($orders_oa_flag == true && $orders_status_flag == true && $end_orders_status_flag == false && $orders_status_id_flag == true){

        $orders_products_query = tep_db_query("select products_id,products_quantity from ". TABLE_ORDERS_PRODUCTS ." where orders_id='".tep_db_input($order_id)."'");
        while($orders_products_array = tep_db_fetch_array($orders_products_query)){
          tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered - " . sprintf('%d', $orders_products_array['products_quantity']) . " where products_id = '" . (int)$orders_products_array['products_id'] . "'");
        }
        tep_db_free_result($orders_products_query);    
  } 

  tep_db_query("delete from `".TABLE_ORDERS_STATUS_HISTORY."` where `orders_status_history_id` = '".$status_id."'"); 

}
