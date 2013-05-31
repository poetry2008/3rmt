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
    if (!(in_array('chief', $request_one_time_arr) && in_array('staff',$request_one_time_arr))) {
      if ($ocertify->npermission == 7 && in_array('chief', $request_one_time_arr)) {
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
if(isset($_GET['action'])){
  switch ($_GET['action']){
/* -----------------------------------------------------
   case 'updateoaorder' 更新oa组在表单里的序号  
   case 'updategrouporder' 更新oa组的序号   
   case 'updateitemorder' 更新oa元素的序号   
   case 'getTime' 获得当前时间   
   case 'finish' 更新订单完成标识 
   case 'complete' 判断oa信息的完整性,如果不完整，给出相应的提示   
   case 'stock' 判断提交过来的数据是否与数据库储存的数据一致
------------------------------------------------------*/
  case 'updateoaorder':
    $id = $_GET['id'];
    $order = substr($_GET['order'],1);
    $result = tep_db_query("update `".TABLE_OA_FORM_GROUP."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    break;
  case 'updategrouporder':     
    $id = $_GET['id'];
    $order = substr($_GET['order'],1);
    $result = tep_db_query("update `".TABLE_OA_GROUP."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    break;

  case 'updateitemorder':     
    $id = $_GET['id'];
    $order = substr($_GET['order'],1);
    $result = tep_db_query("update `".TABLE_OA_ITEM."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
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
          $orders_raw = tep_db_query("select customers_id, payment_method, site_id, orders_status from ".TABLE_ORDERS." where orders_id = '".$id."'"); 
          $orders_res = tep_db_fetch_array($orders_raw); 
          if ($orders_res) {
            if (check_order_latest_status($id)) {
              $tmp_setting = get_configuration_by_site_id_or_default('MODULE_ORDER_TOTAL_POINT_STATUS', $orders_res['site_id']);
              $tmp_tmp_setting = get_configuration_by_site_id_or_default('MODULE_ORDER_TOTAL_POINT_ADD_STATUS', $orders_res['site_id']);
              if ($tmp_setting == 'true' && $tmp_tmp_setting != '0') {
                $cpayment = payment::getInstance($orders_res['site_id']); 
                if ($cpayment->admin_is_get_point(payment::changeRomaji($orders_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $orders_res['site_id']) == 'True') {
                  $point_rate = $cpayment->admin_get_point_rate(payment::changeRomaji($orders_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $orders_res['site_id']); 
                  
                  $get_point = $cpayment->admin_calc_get_point(payment::changeRomaji($orders_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $id, $point_rate, $orders_res['site_id']);
                  
                  tep_db_query("update `".TABLE_CUSTOMERS."` set point = point + ".(int)$get_point." where customers_id = '".$orders_res['customers_id']."' and customers_guest_chk = '0'"); 
                }
                unset($cpayment); 
              }
            }
          }
          $result = tep_db_query("update `".TABLE_ORDERS."` set `end_user` = '".$value."', `flag_qaf` = ".'1'." where orders_id = '".$id."'");  
          if($_POST['finish']){
            $messageStack->add_session(sprintf(MESSAGE_FINISH_ORDER_TEXT,$id) , 'success');
          }
        }
      }
    }else {
      $orders_raw = tep_db_query("select customers_id, payment_method, site_id, orders_status from ".TABLE_ORDERS." where orders_id = '".$id."'"); 
      $orders_res = tep_db_fetch_array($orders_raw); 
      if ($orders_res) {
        if (check_order_latest_status($id)) {
          $tmp_setting = get_configuration_by_site_id_or_default('MODULE_ORDER_TOTAL_POINT_STATUS', $orders_res['site_id']);
          $tmp_tmp_setting = get_configuration_by_site_id_or_default('MODULE_ORDER_TOTAL_POINT_ADD_STATUS', $orders_res['site_id']);
          if ($tmp_setting == 'true' && $tmp_tmp_setting != '0') {
            $cpayment = payment::getInstance($orders_res['site_id']); 
            if ($cpayment->admin_is_get_point(payment::changeRomaji($orders_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $orders_res['site_id']) == 'True') {
              $point_rate = $cpayment->admin_get_point_rate(payment::changeRomaji($orders_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $orders_res['site_id']); 
              
              $get_point = $cpayment->admin_calc_get_point(payment::changeRomaji($orders_res['payment_method'], PAYMENT_RETURN_TYPE_CODE), $id, $point_rate, $orders_res['site_id']);
                  
              tep_db_query("update `".TABLE_CUSTOMERS."` set point = point + ".(int)$get_point." where customers_id = '".$orders_res['customers_id']."' and customers_guest_chk = '0'"); 
            }
          }
        } 
      }
      $result = tep_db_query("update `".TABLE_ORDERS."` set `end_user` = '".$value."', `flag_qaf` = ".'1'." where orders_id = '".$id."'");  
    }

    break;
  case 'complete':
    $orders_id = $_GET['oID'];
    $complete_flag = '';
    $oa_item_id_array = array();
    $oa_item_title_array = array();
    $oa_item_title_sort_array = array();
    $oa_item_form_id_array = array();
    $oa_item_id_value = '';
    //通过订单ID，来获取此订单的oa相关信息
    $formtype = tep_check_order_type($orders_id);
    $payment_romaji = tep_get_payment_code_by_order_id($orders_id);
    $oa_form_sql = tep_db_query("select id from ".TABLE_OA_FORM." where formtype = '".$formtype."' and payment_romaji = '".$payment_romaji."'");
    $oa_form_id_array = tep_db_fetch_array($oa_form_sql);
    tep_db_free_result($oa_form_sql);
    $oa_item_id_value = $oa_form_id_array['id'];
    $oa_group_query = tep_db_query("select form_id,item_id from ". TABLE_OA_FORMVALUE ." where orders_id='".$orders_id."'");
    while($oa_group_array = tep_db_fetch_array($oa_group_query)){
 
      $oa_item_form_id_array[] = $oa_group_array['item_id'];
    }
    $oa_item_num = tep_db_num_rows($oa_group_query);
    tep_db_free_result($oa_group_query);
    //获取oa子元素的相应信息
    $oa_group_form_query = tep_db_query("select group_id,ordernumber from ". TABLE_OA_FORM_GROUP ." where form_id='".$oa_item_id_value."'");
    while($oa_group_form_array = tep_db_fetch_array($oa_group_form_query)){

      $oa_item_form_query = tep_db_query("select id,title,group_id,`option` as option_value from ". TABLE_OA_ITEM ." where group_id='".$oa_group_form_array['group_id']."'");
      while($oa_item_form_array = tep_db_fetch_array($oa_item_form_query)){

        $oa_item_option_array = unserialize($oa_item_form_array['option_value']); 
        if($oa_item_option_array['require'] == 'on'){
          $oa_item_id_array[] = $oa_item_form_array['id']; 
          $oa_group_id_query = tep_db_query("select name from ". TABLE_OA_GROUP ." where id='".$oa_item_form_array['group_id']."'");
          $oa_group_id_array = tep_db_fetch_array($oa_group_id_query);
          tep_db_free_result($oa_group_id_query);
          $oa_item_title_array[$oa_item_form_array['id']] = $oa_group_id_array['name'];
          $oa_item_title_sort_array[$oa_item_form_array['id']] = $oa_group_form_array['ordernumber'];
        }
      }
    }
    //判断oa的完整性
    $oa_item_str = implode(',',$oa_item_id_array);
    $complete_temp_flag = false;
    $oa_item_value_array = array();
    $oa_form_query = tep_db_query("select item_id,value from ". TABLE_OA_FORMVALUE ." where orders_id='".$orders_id."' and item_id in (".$oa_item_str.")");
    while($oa_form_array = tep_db_fetch_array($oa_form_query)){

      if(trim($oa_form_array['value']) == ''){

        $complete_temp_flag = true;
        $oa_item_value_array[] = $oa_form_array['item_id'];
      }
    }
    tep_db_free_result($oa_form_query);
    $oa_diff_array = array_diff($oa_item_id_array,$oa_item_form_id_array);
    $oa_diff_array = array_merge($oa_diff_array,$oa_item_value_array);
    $oa_diff_name_array = array();
    foreach($oa_diff_array as $oa_value){

      $oa_diff_name_array[$oa_item_title_sort_array[$oa_value]] = $oa_item_title_array[$oa_value];
    }
    $oa_diff_name_array = array_unique($oa_diff_name_array);
    ksort($oa_diff_name_array);
    //如果数据不完整，给出错误信息
    if($oa_item_num < count($oa_item_id_array)){

      $complete_flag = '「';
      $complete_flag .= implode('」、「',$oa_diff_name_array);
      $complete_flag .= '」';
    }else{

      if($complete_temp_flag == true){

        $complete_flag = '「';
        $complete_flag .= implode('」、「',$oa_diff_name_array);
        $complete_flag .= '」';
      }
    }
    echo $complete_flag;
    break;
  case 'stock':
    $orders_id = $_POST['oID'];
    $oa_name = $_POST['name'];
    $oa_status = $_POST['status'];
    $products_id = $_POST['pid'];
    $products_num = $_POST['n'];
    $oa_form_query = tep_db_query("select * from ". TABLE_OA_FORMVALUE ." where orders_id='".$orders_id."' and name='".$oa_name."'");
    $oa_form_array = tep_db_fetch_array($oa_form_query);
    $oa_form_num = tep_db_num_rows($oa_form_query);
    tep_db_free_result($oa_form_query);

    $oa_form_array['value'] = substr($oa_form_array['value'],0,-1);
    $products_split_array = array();
    $split_array = array();
    $products_split_array = explode('_',$oa_form_array['value']);
    $split_array = explode('|',$products_split_array[$products_num]);
 
    if($oa_status == 1 && $oa_form_num == 1){
       
      if($split_array[0] == $products_id){

        echo 'true';
        exit;
      }
    }

    if($oa_status == 0 && $oa_form_num == 1){

      if($split_array[0] == 0){

        echo 'true';
        exit;
      }
    }
    break;
  }
}
