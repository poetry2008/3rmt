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
      if ($ocertify->npermission == 7 && in_array('chief',$request_one_time_arr)) {
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

$order_id = $_POST['oID'];
$status_id = $_POST['stid'];
$orders_status_query = tep_db_query("select * from ".TABLE_PREORDERS_STATUS_HISTORY." where orders_status_history_id = '".$status_id."'");
$orders_status_res = tep_db_fetch_array($orders_status_query);
if ($orders_status_res) {
  $other_status_query = tep_db_query("select * from ".TABLE_PREORDERS_STATUS_HISTORY." where orders_status_history_id != '".$status_id."' and orders_id = '".$order_id."' order by date_added desc limit 1");
  $other_status_res = tep_db_fetch_array($other_status_query); 
  
  if ($other_status_res) {
    $status_info_query = tep_db_query("select * from ".TABLE_PREORDERS_STATUS." where orders_status_id = '".$other_status_res['orders_status_id']."'"); 
    $status_info_res = tep_db_fetch_array($status_info_query);
    if ($status_info_res) {
      tep_db_query("update `".TABLE_PREORDERS."` set `orders_status` = '".$other_status_res['orders_status_id']."', `orders_status_name` = '".$status_info_res['orders_status_name']."' where orders_id = '".$order_id."'"); 
    }
  } else {
    $status_info_query = tep_db_query("select * from ".TABLE_PREORDERS_STATUS." where orders_status_id = '".DEFAULT_ORDERS_STATUS_ID."'"); 
    $status_info_res = tep_db_fetch_array($status_info_query);
    if ($status_info_res) {
      tep_db_query("update `".TABLE_PREORDERS."` set `orders_status` = '".DEFAULT_ORDERS_STATUS_ID."', `orders_status_name` = '".$status_info_res['orders_status_name']."' where orders_id = '".$order_id."'"); 
    }
  }

  tep_db_query("delete from `".TABLE_PREORDERS_STATUS_HISTORY."` where `orders_status_history_id` = '".$status_id."'"); 

}
