<?php
if(isset($_GET['action']) && $_GET['action'] == 'check_file_exists'){
  /* -----------------------------------------------------
    功能: 返回指定表中，指定字段，指定数据(图片)的总数 
    参数: $_POST['table'] 表名 
    参数: $_POST['field'] 字段 
    参数: $_POST['dir'] 指定数据(图片)前缀 
    参数: $_POST['file'] 指定数据(图片)
 -----------------------------------------------------*/
  $table = $_POST['table'];
  $field = $_POST['field'];
  $dir = $_POST['dir'];
  $file = $_POST['file'];
  $check_query = tep_db_query("select ".$field." from ".$table." where ".$field."='".$dir.$file."'");
  $check_num = tep_db_num_rows($check_query);
  tep_db_free_result($check_query);
  echo $check_num;
} else if ($_GET['action'] == 'read_flag') {
  /*------------------------------------------
 功能: 读取标志 
 参数: $_POST['user'] 用户
 参数: $_POST['flag'] 标志
 参数: $_POST['id'] memo编号
 -----------------------------------------*/
  $users_name = $_POST['user'];
  $read_flag = $_POST['flag'];
  $memo_id = $_POST['id'];
  $read_flag_query = tep_db_query("select read_flag from ". TABLE_BUSINESS_MEMO ." where id='".$memo_id."'");
  $read_flag_array = tep_db_fetch_array($read_flag_query);
  tep_db_free_result($read_flag_query);
  if($read_flag_array['read_flag'] == ''){

    if($read_flag == 0){
      tep_db_query("update ". TABLE_BUSINESS_MEMO ." set read_flag='".$users_name."' where id='".$memo_id."'"); 
    }
  }else{

    $read_flag_str_array = explode(',',$read_flag_array['read_flag']);
    if(!in_array($users_name,$read_flag_str_array) && $read_flag == 0){
      $read_flag_add = $read_flag_array['read_flag'].','.$users_name;
      tep_db_query("update ". TABLE_BUSINESS_MEMO ." set read_flag='".$read_flag_add."' where id='".$memo_id."'");
    }else{

      unset($read_flag_str_array[array_search($users_name,$read_flag_str_array)]);
      $read_flag_string = implode(',',$read_flag_str_array);
      tep_db_query("update ". TABLE_BUSINESS_MEMO ." set read_flag='".$read_flag_string."' where id='".$id."'");
    }
  }
}else if(isset($_GET['action']) && $_GET['action'] == 'check_email'){
  require('includes/step-by-step/new_application_top.php');
  $check_query = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address ='".$_POST['post_email']."' and site_id ='".$_POST['post_site']."'");
  $check_num = tep_db_num_rows($check_query);
  tep_db_free_result($check_query);
  if(!tep_validate_email($_POST['post_email'])){
     $check_email_error = '1';
  }else{
     $check_email_error = '0';
  }
  echo  $check_email_error.','.$check_num;
}
?>
