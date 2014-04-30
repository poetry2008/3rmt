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
// 创建CSV文件名
  $filename = ((isset($_POST['site_id'])&&$_POST['site_id']) ?  (tep_get_site_romaji_by_id(intval($_POST['site_id'])).'_') :'')."customers_".date("Ymd_His", time()).".csv";
//获取下载范围
   if($_POST['site_id'] == ''){
      $show_site_list_array = array();
      $site_list_info_query = tep_db_query("select * from ".TABLE_SITES);
      while ($site_list_info = tep_db_fetch_array($site_list_info_query)) {
            $show_site_list_array[] = $site_list_info['id'];
      }
     $all_site_id = implode(',',$show_site_list_array);
     $site_id = "site_id in (".$all_site_id.")";
    }else{
     $site_id = " site_id =".$_POST['site_id'];
    }
  $csv_query = tep_db_query(" select * from ".TABLE_CUSTOMERS);
  header("Content-Type: application/force-download");
  header('Pragma: public');
  header('Content-Disposition: attachment; filename='.$filename);

  $csv_header = TEXT_CUSTOMERS_CSV;
  $c_sql = tep_db_num_rows(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'DATA_MANAGEMENT' and configuration_value = 'mag_customers'"));
  if($c_sql > 0){
     tep_db_query("update ".TABLE_CONFIGURATION." set last_modified = now(),user_update = '".$_SESSION['user_name']."' where configuration_key ='DATA_MANAGEMENT' and configuration_value = 'mag_customers'");
  }else{
     tep_db_query("insert into ".TABLE_CONFIGURATION." (configuration_key,configuration_value,last_modified,date_added,user_update,user_added) values ('DATA_MANAGEMENT','mag_customers',now(),now(),'".$_SESSION['user_name']."','".$_SESSION['user_name']."')");
  }
  print chr(0xEF).chr(0xBB).chr(0xBF);
  print $csv_header."\r\n";
  while ($csv_customers = tep_db_fetch_array($csv_query)) {
    $csv  = "";
    $csv .= '"'. precsv($csv_customers['customers_id']).'"';
    $csv .= ',"'.precsv($csv_customers['customers_gender']).'"';
    $csv .= ',"'.precsv($csv_customers['customers_firstname']).'"';
    $csv .= ',"'.precsv($csv_customers['customers_lastname']).'"';
    $csv .= ',"'.precsv($csv_customers['customers_firstname_f']).'"';
    $csv .= ',"'.precsv($csv_customers['customers_lastname_f']).'"';
    $csv .= ',"'.precsv($csv_customers['customers_dob']).'"';
    $csv .= ',"'.precsv($csv_customers['customers_email_address']).'"';
    $csv .= ',"'.precsv($csv_customers['customers_default_address_id']).'"';
    $csv .= ',"'.precsv($csv_customers['customers_telephone']).'"';
    $csv .= ',"'.precsv(str_replace(array("\r","\n","\t"),array("","",""),$csv_customers['customers_fax'])).'"';
    $csv .= ',"'.precsv($csv_customers['customers_password']).'"';
    $csv .= ',"'.precsv($csv_customers['customers_newsletter']).'"';
    $csv .= ',"'.precsv($csv_customers['point']).'"';
    $csv .= ',"'.precsv($csv_customers['site_id']).'"';
    $csv .= ',"'.precsv($csv_customers['customers_guest_chk']).'"';
    $csv .= ',"'.precsv($csv_customers['customers_firstorderat']).'"';
    $csv .= ',"'.precsv($csv_customers['is_active']).'"';
    $csv .= ',"'.precsv($csv_customers['origin_password']).'"';
    $csv .= ',"'.precsv($csv_customers['send_mail_time']).'"';
    $csv .= ',"'.precsv($csv_customers['check_login_str']).'"';
    $csv .= ',"'.precsv($csv_customers['new_email_address']).'"';
    $csv .= ',"'.precsv($csv_customers['new_customers_lastname']).'"';
    $csv .= ',"'.precsv($csv_customers['new_customers_firstname']).'"';
    $csv .= ',"'.precsv($csv_customers['new_customers_password']).'"';
    $csv .= ',"'.precsv($csv_customers['new_customers_newsletter']).'"';
    $csv .= ',"'.precsv($csv_customers['reset_flag']).'"';
    $csv .= ',"'.precsv($csv_customers['reset_success']).'"';
    $csv .= ',"'.precsv($csv_customers['is_seal']).'"';
    $csv .= ',"'.precsv(str_replace(array("\r","\n","\t"),array("","",""),$csv_customers['referer'])).'"';
    $csv .= ',"'.precsv($csv_customers['pic_icon']).'"';
    $csv .= ',"'.precsv($csv_customers['is_send_mail']).'"';
    $csv .= ',"'.precsv($csv_customers['is_calc_quantity']).'"';
    $csv .= ',"'.precsv($csv_customers['is_quited']).'"';
    $csv .= ',"'.precsv($csv_customers['quited_date']).'"';
    $csv .= ',"'.precsv($csv_customers['is_exit_history']).'"';
    print $csv."\r\n";

  }
  function precsv($query) {
    global $result;
    $result = $query;
    $result = str_replace('"', '""', $result);
    return $result;    
  }

?>
