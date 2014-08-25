<?php
/*
  $Id$

*/
require('includes/application_top.php');
include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_PAYROLLS);
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

if ($ocertify->npermission != 31) {
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
function precsv($query) {
    global $result;
    $result = $query;
    $result = str_replace('"', '""', $result);
    return $result;    
  }

if($_GET['csv_exe'] == 'true'){
  // 创建CSV文件名

  $filename = "payrolls_".date("Ymd_His", time()).".csv";

  $user_id = $_POST['user_id'];
  $users_payroll = $_POST['users_payroll'];
  $payroll_title = $_POST['payroll_title'];
  $payroll_date = tep_db_prepare_input($_POST['save_date']);
  $group_id = tep_db_prepare_input($_POST['group_id']);

  $str = '';
  $str .= '"'.precsv(TEXT_PAYROLLS_NAME).'"';
  foreach($payroll_title as $title_value){

    $str .= ',"'.precsv($title_value).'"';
  }
  $str .= "\r\n";
  $payroll_total = array();
  foreach($user_id as $key=>$value){

    $user_info = tep_get_user_info($value);
    $str .= '"'.precsv($user_info['name']).'"';
    foreach($users_payroll as $user_key=>$user_value){

      $str .= ',"'.precsv($user_value[$value]).'"'; 
      $payroll_total[$user_key] += $user_value[$value];
    }
    $str .= "\r\n";
  }
  $str .= '"'.precsv(TEXT_PAYROLLS_TOTAL.'('.$_POST['currency_type_str'].')').'"';
  foreach($payroll_total as $total_value){

    $str .= ',"'.precsv($total_value).'"';
  }
  $str .= "\r\n";

  header("Content-Type: application/force-download");
  header('Pragma: public');
  header('Content-Disposition: attachment; filename='.$filename); 
  echo chr(0xEF).chr(0xBB).chr(0xBF);
  echo $str;  
}
?>
