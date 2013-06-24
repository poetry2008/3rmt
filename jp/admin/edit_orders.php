<?php
/*
   $Id$

   编辑订单
 */

require('includes/application_top.php');

require('includes/step-by-step/new_application_top.php');
require('includes/address/AD_Option.php');
require('includes/address/AD_Option_Group.php');
require('option/HM_Option.php');
require('option/HM_Option_Group.php');
include(DIR_FS_ADMIN . DIR_WS_LANGUAGES .  '/default.php');
$ad_option = new AD_Option();
require(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_EDIT_ORDERS);

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies(2);

include(DIR_WS_CLASSES . 'order.php');

$orders_update_time_query = tep_db_query("select site_id,payment_method,code_fee,last_modified from ". TABLE_ORDERS ." where orders_id='".$_GET['oID']."'");
$orders_update_time_array = tep_db_fetch_array($orders_update_time_query);
tep_db_free_result($orders_update_time_query);
if(!isset($_SESSION['orders_update_time'][$_GET['oID']])){
  $_SESSION['orders_update_time'][$_GET['oID']] = $orders_update_time_array['last_modified'];
}else{
  if($_SESSION['orders_update_time'][$_GET['oID']] != $orders_update_time_array['last_modified']){
    unset($_SESSION['orders_update_products'][$_GET['oID']]);
    unset($_SESSION['new_products_list'][$_GET['oID']]);  
    $_SESSION['orders_update_time'][$_GET['oID']] = $orders_update_time_array['last_modified'];
  }
}
if(isset($_GET['clear_products']) && isset($_SESSION['clear_products_flag'])){ 
  //重新计算新添加商品之后订单的小计,手续费,总计等相关信息 
  if($_GET['clear_products'] == 1){
    $orders_products_price_sum = 0;
    $orders_price_flag = $_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal'] > 0 ? true : false;
    $orders_price_exists_flag = isset($_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal']) ? true : false;
    foreach($_SESSION['new_products_list_add'][$_GET['oID']]['orders_products'] as $orders_product_value){
      $_SESSION['new_products_list'][$_GET['oID']]['orders_products'][] = $orders_product_value;
      $orders_products_price_sum += $orders_product_value['products_quantity']*$orders_product_value['final_price'];
    }
    $orders_products_price_sum_subtotal = $orders_products_price_sum;
    $orders_products_price_sum_subtotal += isset($_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal']) ? 0 : $_SESSION['orders_products_price_subtotal'][$_GET['oID']];
    $orders_products_price_sum_total = $orders_products_price_sum; 
    $orders_products_price_sum_total += isset($_SESSION['orders_update_products'][$_GET['oID']]['ot_total']) ? 0 : $_SESSION['orders_products_price_total'][$_GET['oID']];
        if(isset($_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal'])){ 
          $_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal'] += $orders_products_price_sum_subtotal;
        }else{
          $_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal'] = $orders_products_price_sum_subtotal; 
        }
        if(isset($_SESSION['orders_update_products'][$_GET['oID']]['ot_total'])){
          $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] += $orders_products_price_sum_total;
        }else{
          $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] = $orders_products_price_sum_total; 
        }
        if($_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal'] < 0){

          if(!isset($_SESSION['orders_update_products'][$_GET['oID']]['point'])){

            $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] += $_SESSION['orders_products_price_point'][$_GET['oID']]; 
          }else{
            if($orders_price_exists_flag == false){
              if($_SESSION['orders_products_price_subtotal'][$_GET['oID']] > 0){
                $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] += $_SESSION['orders_update_products'][$_GET['oID']]['point']; 
              }
            }else{
              if($orders_price_flag == true){
                $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] += $_SESSION['orders_update_products'][$_GET['oID']]['point']; 
              } 
            }
          }
        }else{
          if($orders_price_flag == false){

            if(isset($_SESSION['orders_update_products'][$_GET['oID']]['point'])){
              $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] -= $_SESSION['orders_update_products'][$_GET['oID']]['point']; 
            }
          } 
        } 
        $campaign_flag = false;
        $campaign_fee = 0;
        $camp_exists_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$_GET['oID']."' and site_id = '". $orders_update_time_array['site_id'] ."'");
        if(tep_db_num_rows($camp_exists_query)){
          $campaign_flag = true;
          $campaign_fee = get_campaion_fee($_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal'],$_GET['oID'],$orders_update_time_array['site_id']);
        }
        tep_db_free_result($camp_exists_query);
        if($campaign_flag == true){
          $orders_total_query = tep_db_query("select value from ". TABLE_ORDERS_TOTAL ." where class='ot_subtotal' and orders_id='". $_GET['oID'] ."'");
          $orders_total_array = tep_db_fetch_array($orders_total_query);
          $orders_total_value = $orders_total_array['value'];
          tep_db_free_result($orders_total_query);
          $campaign_value = get_campaion_fee($orders_total_value,$_GET['oID'],$orders_update_time_array['site_id']);
          $campaign_value = isset($_SESSION['orders_update_products'][$_GET['oID']]['point']) ? $_SESSION['orders_update_products'][$_GET['oID']]['point'] : $campaign_value;
          $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] += abs($campaign_value);
          if($orders_price_flag == true){
            if($_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal'] < 0){
              $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] += abs($campaign_value); 
            }
          }
          $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] -= abs($campaign_fee);
          $_SESSION['orders_update_products'][$_GET['oID']]['point'] = $campaign_fee;
        }
        $payment_handle = payment::getInstance($orders_update_time_array['site_id']);
        $payment_value = isset($_SESSION['orders_update_products'][$_GET['oID']]['payment_method']) ? $_SESSION['orders_update_products'][$_GET['oID']]['payment_method'] : payment::changeRomaji($orders_update_time_array['payment_method'],PAYMENT_RETURN_TYPE_CODE); 
        $handle_fee = $payment_handle->handle_calc_fee($payment_value, $_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal']);
        $handle_fee = $handle_fee == '' ? 0 : $handle_fee;
        $handle_fee_temp = $handle_fee;
        $handle_fee_num = isset($_SESSION['orders_update_products'][$_GET['oID']]['code_fee']) ? $_SESSION['orders_update_products'][$_GET['oID']]['code_fee'] : $orders_update_time_array['code_fee']; 
        if($handle_fee_temp != $handle_fee_num){

            $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] -= $handle_fee_num;
            $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] += $handle_fee_temp; 
        }
        $shipping_fee_new = tep_products_shipping_fee($_GET['oID'],$_SESSION['orders_update_products'][$_GET['oID']]['ot_total']-$handle_fee_temp);
        $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] += $shipping_fee_new;
        $_SESSION['orders_update_products'][$_GET['oID']]['code_fee'] = $handle_fee_temp != $handle_fee_num ? $handle_fee_temp : $handle_fee_num;  
  } 
  unset($_SESSION['orders_products_price_subtotal'][$_GET['oID']]);
  unset($_SESSION['orders_products_price_point'][$_GET['oID']]);
  unset($_SESSION['orders_products_price_total'][$_GET['oID']]);
  unset($_SESSION['new_products_list_add'][$_GET['oID']]);
  unset($_SESSION['clear_products_flag']);
}
// START CONFIGURATION ################################

// Optional Tax Rates, e.g. shipping tax of 17.5% is "17.5"
$AddCustomTax = "19.6";  // new
$AddShippingTax = "19.6";  // new
$AddLevelDiscountTax = "19.6";  // new
$AddCustomerDiscountTax = "19.6";  // new

// END OF CONFIGURATION ################################


// New "Status History" table has different format.
$OldNewStatusValues = (tep_field_exists(TABLE_ORDERS_STATUS_HISTORY, "old_value") && tep_field_exists(TABLE_ORDERS_STATUS_HISTORY, "new_value"));
$CommentsWithStatus = tep_field_exists(TABLE_ORDERS_STATUS_HISTORY, "comments");
$SeparateBillingFields = tep_field_exists(TABLE_ORDERS, "billing_name");

$orders_statuses = array();
$orders_status_array = array();
$orders_status_query = tep_db_query("
    select orders_status_id, 
    orders_status_name 
    from " . TABLE_ORDERS_STATUS . " 
    where language_id = '" . (int)$languages_id . "'
    ");
while ($orders_status = tep_db_fetch_array($orders_status_query)) {
  $orders_statuses[] = array('id' => $orders_status['orders_status_id'],
      'text' => $orders_status['orders_status_name']);
  $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
}

$action = (isset($_GET['action']) ? $_GET['action'] : 'edit');

// Update Inventory Quantity
$order_query = tep_db_query("
    select products_id, 
    products_quantity
    from " . TABLE_ORDERS_PRODUCTS . " 
    where orders_id = '" . tep_db_input($oID) . "'");

// 获取最新订单信息
$order = new order($oID);
$cpayment = payment::getInstance($order->info['site_id']);
// 获取返点
$customer_point_query = tep_db_query("
    select point 
    from " . TABLE_CUSTOMERS . " 
    where customers_id = '" . $order->customer['id'] . "'");
$customer_point = tep_db_fetch_array($customer_point_query);
// 游客检查
$customer_guest_query = tep_db_query("
    select customers_guest_chk,is_send_mail,is_calc_quantity 
    from " . TABLE_CUSTOMERS . " 
    where customers_id = '" . $order->customer['id'] . "'");
$customer_guest = tep_db_fetch_array($customer_guest_query);

if (tep_not_null($action)) {
  $payment_modules = payment::getInstance($order->info['site_id']);
  switch ($action) {
/* -----------------------------------------------------
   case 'update_order' 更新订单信息    
   case 'add_product' 更新添加商品后的订单的信息    
------------------------------------------------------*/
    // 1. UPDATE ORDER ###############################################################################################
  case 'update_order':

      $orders_status_flag = tep_orders_finished($oID) == '1' ? true : false;
      $update_user_info = tep_get_user_info($ocertify->auth_user);
      if (empty($_POST['s_status']) || empty($update_user_info['name'])) {
        $_SESSION['error_edit_orders_status'] = WARNING_LOSING_INFO_TEXT; 
        $messageStack->add_session(WARNING_LOSING_INFO_TEXT, 'warning');
        tep_redirect(tep_href_link("edit_orders.php", tep_get_all_get_params(array('action')) . 'action=edit'));
      }
      $year = $_POST['fetch_year']; 
      $month = $_POST['fetch_month'];
      $day = $_POST['fetch_day'];
      $start_hour = $_POST['start_hour'];
      $start_min = $_POST['start_min'];
      $start_min_1 = $_POST['start_min_1'];
      $end_hour = $_POST['end_hour'];
      $end_min = $_POST['end_min'];
      $end_min_1 = $_POST['end_min_1'];
      $date_time = $year.$month.$day;
      $date_now = date('Ymd');
      $date_start_hour = $start_hour.$start_min.$start_min_1;
      $date_end_hour = $end_hour.$end_min.$end_min_1; 
      $shipping_array = array();
    foreach($update_products as $products_key=>$products_value){

      $products_session_str_array = explode('_',$products_key);
      if(count($products_session_str_array) <= 1){
        $shipping_products_query = tep_db_query("select * from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='". $products_key."'");
        $shipping_products_array = tep_db_fetch_array($shipping_products_query);
        tep_db_free_result($shipping_products_query);
      }else{
        $shipping_products_array['final_price'] = $_SESSION['new_products_list'][$_GET['oID']]['orders_products'][$products_session_str_array[1]]['final_price']; 
        $shipping_products_array['products_id'] = $_SESSION['new_products_list'][$_GET['oID']]['orders_products'][$products_session_str_array[1]]['products_id'];
      }
      $products_value['final_price'] = $shipping_products_array['final_price'] < 0 ? -$products_value['final_price'] : $products_value['final_price'];
      $shipping_array[] = array('id'=>$shipping_products_array['products_id'],'qty'=>$products_value['qty'],'final_price'=>$products_value['final_price']);
    }
    //计算配送费用
    $shipping_weight_total = 0;
    $shipping_money_sum = 0;
    foreach($shipping_array as $shipping_value){

      $shipping_fee_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id=". $shipping_value['id']);
      $shipping_fee_array = tep_db_fetch_array($shipping_fee_query);
      $shipping_weight_total += $shipping_value['qty'] * $shipping_fee_array['products_weight'];
      $shipping_money_sum += $shipping_value['final_price']*$shipping_value['qty'];
      tep_db_free_result($shipping_fee_query);
    }

    $weight = $shipping_weight_total;

    $shipping_orders_array = array();
    foreach($_POST as $post_key=>$post_value){

      if(substr($post_key,0,3) == 'ad_'){

        $shipping_orders_array[substr($post_key,3)] = $post_value;
      }
    }

    $country_fee_array = array();
    $country_fee_id_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
    while($country_fee_id_array = tep_db_fetch_array($country_fee_id_query)){

      $country_fee_array[$country_fee_id_array['fixed_option']] = $country_fee_id_array['name_flag'];
    }
    tep_db_free_result($country_fee_id_query);
  foreach($shipping_orders_array  as $op_key=>$op_value){
     if($op_key == $country_fee_array[3]){
       $city_query = tep_db_query("select * from ". TABLE_COUNTRY_CITY ." where name='". $op_value ."' and status='0'");
       $city_num = tep_db_num_rows($city_query);
     }

     
     if($op_key == $country_fee_array[2]){
       $address_query = tep_db_query("select * from ". TABLE_COUNTRY_AREA ." where name='". $op_value ."' and status='0'");
       $address_num = tep_db_num_rows($address_query);
     }

      
     if($op_key == $country_fee_array[1]){
       $country_query = tep_db_query("select * from ". TABLE_COUNTRY_FEE ." where name='". $op_value ."' and status='0'");
       $address_country_num = tep_db_num_rows($country_query);
     }

  if($city_num > 0 && $op_key == $country_fee_array[3]){
    $city_array = tep_db_fetch_array($city_query);
    tep_db_free_result($city_query);
    $city_free_value = $city_array['free_value'];
    $city_weight_fee_array = unserialize($city_array['weight_fee']);

  //根据重量来获取相应的配送费用
  foreach($city_weight_fee_array as $key=>$value){
    
    if(strpos($key,'-') > 0){

      $temp_array = explode('-',$key);
      $city_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
    }else{
  
      $city_weight_fee = $weight <= $key ? $value : 0;
    }

    if($city_weight_fee > 0){

      break;
    }
  }    
  }elseif($address_num > 0 && $op_key == $country_fee_array[2]){
    $address_array = tep_db_fetch_array($address_query);
    tep_db_free_result($address_query);
    $address_free_value = $address_array['free_value'];
    $address_weight_fee_array = unserialize($address_array['weight_fee']);

  //根据重量来获取相应的配送费用
  foreach($address_weight_fee_array as $key=>$value){
    
    if(strpos($key,'-') > 0){

      $temp_array = explode('-',$key);
      $address_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
    }else{
  
      $address_weight_fee = $weight <= $key ? $value : 0;
    }

    if($address_weight_fee > 0){

      break;
    }
  }
  }else{
    if($address_country_num > 0 && $op_key == $country_fee_array[1]){
    $country_array = tep_db_fetch_array($country_query);
    tep_db_free_result($country_query);
    $country_free_value = $country_array['free_value'];
    $country_weight_fee_array = unserialize($country_array['weight_fee']);

  //根据重量来获取相应的配送费用
  foreach($country_weight_fee_array as $key=>$value){
    
    if(strpos($key,'-') > 0){

      $temp_array = explode('-',$key);
      $country_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
    }else{
  
      $country_weight_fee = $weight <= $key ? $value : 0;
    }

    if($country_weight_fee > 0){

      break;
    }
  }
  }
 }

}

  $shipping_money_total = $shipping_money_sum;
    if($city_weight_fee != ''){

      $weight_fee = $city_weight_fee;
    }else{
      $weight_fee = $address_weight_fee != '' ? $address_weight_fee : $country_weight_fee;
    }
    if($city_free_value != ''){

      $free_value = $city_free_value;
    }else{
      $free_value = $address_free_value != '' ? $address_free_value : $country_free_value;
    }

  foreach($update_totals as $total_value){

    if($total_value['class'] == 'ot_custom'){

      $fee_total += $total_value['value'];
    }
  }
  $fee_total = isset($_SESSION['orders_update_products'][$oID]['fee_total']) ? $_SESSION['orders_update_products'][$oID]['fee_total'] : $fee_total;

  $point_fee = 0;
  foreach($update_totals as $total_value){

    if($total_value['class'] == 'ot_point'){

      $point_fee = $total_value['value'];
      break;
    }
  }
  $shipping_fee = $shipping_money_total+$fee_total-$point_fee > $free_value ? 0 : $weight_fee;

      $oID = tep_db_prepare_input($_GET['oID']);
      $order = new order($oID);
      $payment_method = tep_db_prepare_input($_POST['payment_method']);
      $status = tep_db_prepare_input($_POST['s_status']);
      $comments_text = tep_db_prepare_input($_POST['comments_text']);
      $start_hour = tep_db_prepare_input($_POST['start_hour']);
      $start_min_1 = tep_db_prepare_input($_POST['start_min_1']);
      $start_min_2 = tep_db_prepare_input($_POST['start_min_2']);
      $end_hour = tep_db_prepare_input($_POST['end_hour']);
      $end_min_1 = tep_db_prepare_input($_POST['end_min_1']);
      $end_min_2 = tep_db_prepare_input($_POST['end_min_2']);
      $goods_check = $order_query;
 
      //viladate
      $viladate = tep_db_input($_POST['update_viladate']);//viladate pwd 
      if($viladate!='_false'&&$viladate!=''){
        tep_insert_pwd_log($viladate,$ocertify->auth_user);
        $viladate = true;
      }else if($viladate=='_false'){
        $viladate = false;
        $messageStack->add_session(TEXT_CANCEL_UPDATE, 'error');
        tep_redirect(tep_href_link("edit_orders.php", tep_get_all_get_params(array('action')) . 'action=edit'));
        break;
      }
      
      $update_tori_torihiki_start_date = $start_hour.':'.$start_min_1.$start_min_2.':00';
      $update_tori_torihiki_end_date = $end_hour.':'.$end_min_1.$end_min_2.':00';
      $update_tori_torihiki_start_date = $update_tori_torihiki_date.' '.$update_tori_torihiki_start_date;
      $update_tori_torihiki_end_date = $update_tori_torihiki_date.' '.$update_tori_torihiki_end_date; 
      if (isset($update_tori_torihiki_start_date)) { 
        //判断配送开始时间是否正确
        if (!preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)$/', $update_tori_torihiki_start_date, $m)) { 
          //判断配送开始时间是否符合规则
          $messageStack->add(TEXT_DATE_ERROR.'"2008-01-01 10:30:00"', 'error');
          $action = 'edit';
          break;
        } elseif (!checkdate($m[2], $m[3], $m[1]) || $m[4] >= 24 || $m[5] >= 60 || $m[6] >= 60) { 
          //判断配送开始时间是否是有效时间
          $messageStack->add(TEXT_DATE_NUM_ERROR.'"23:59:59"', 'error');
          $action = 'edit';
          break;
        }
      } else {
        $messageStack->add(TEXT_INPUT_DATE_ERROR, 'error');
        $action = 'edit';
        break;
      }

      if (isset($update_tori_torihiki_end_date)) { 
        //判断配送结束时间是否正确
        if (!preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)$/', $update_tori_torihiki_end_date, $m)) { 
          //判断配送结束时间是否符合规则
          $messageStack->add(TEXT_DATE_ERROR.'"2008-01-01 10:30:00"', 'error');
          $action = 'edit';
          break;
        } elseif (!checkdate($m[2], $m[3], $m[1]) || $m[4] >= 24 || $m[5] >= 60 || $m[6] >= 60) { 
          //判断配送结束时间是否是有效时间
          $messageStack->add(TEXT_DATE_NUM_ERROR.'"23:59:59"', 'error');
          $action = 'edit';
          break;
        }
      } else {
        $messageStack->add(TEXT_INPUT_DATE_ERROR, 'error');
        $action = 'edit';
        break;
      }
      //住所信息
      $error_str = false;
      $option_info_array = array(); 
      if (!$ad_option->check()) {
        foreach ($_POST as $p_key => $p_value) {
          $op_single_str = substr($p_key, 0, 3);
          if ($op_single_str == 'ad_') {
            $option_info_array[$p_key] = tep_db_prepare_input($p_value); 
          } 
        }
      }else{
        $error_str = true;
        $address_style = 'display: block;';
      }      


      if ($error_str == true){

        $action = 'edit';
        break;
      } 
    $comment_arr = $payment_modules->dealComment($payment_method,$comment); 
       
      //end 
      foreach ($update_totals as $up_key => $up_total) {
        if ($up_total['class'] == 'ot_point') {
          $camp_exists_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$oID."' and site_id = '".$order->info['site_id']."'"); 
          if (tep_db_num_rows($camp_exists_query)) {
            $update_totals[$up_key]['value'] = 0; 
          }
        }
      }
      
      foreach ($update_totals as $total_index => $total_details) {    
        extract($total_details,EXTR_PREFIX_ALL,"ot");
        if ($ot_class == "ot_point" && (int)$ot_value > 0) {
          $current_point = $customer_point['point'] + $before_point;
          if ((int)$ot_value > $current_point) {
            $messageStack->add(TEXT_NO_ENOUGH_POINT.'<b>' . $current_point . '</b>'.TEXT_LS, 'error');
            $action = 'edit';
            break 2;
          }
        }
      }

      // 1.1 UPDATE ORDER INFO #####
      $UpdateOrders = "update " . TABLE_ORDERS . " set 
                       customers_name = '" . tep_db_input(stripslashes($update_customer_name)) . "',
                       customers_name_f = '" . tep_db_input(stripslashes($update_customer_name_f)) . "',
                       customers_company = '" . tep_db_input(stripslashes($update_customer_company)) . "',
                       customers_street_address = '" . tep_db_input(stripslashes($update_customer_street_address)) . "',
                       customers_suburb = '" . tep_db_input(stripslashes($update_customer_suburb)) . "',
                       customers_city = '" . tep_db_input(stripslashes($update_customer_city)) . "',
                       customers_state = '" . tep_db_input(stripslashes($update_customer_state)) . "',
                       customers_postcode = '" . tep_db_input($update_customer_postcode) . "',
                       customers_country = '" . tep_db_input(stripslashes($update_customer_country)) . "',
                       customers_telephone = '" . tep_db_input($update_customer_telephone) . "',
                       shipping_fee = '". tep_db_prepare_input($shipping_fee) ."',
                       customers_email_address = '" . tep_db_input($update_customer_email_address) . "',";

      if($SeparateBillingFields) {
        // Original: all database fields point to $update_billing_xxx, now they are updated with the same values as the customer fields
        $UpdateOrders .= "billing_name = '" . tep_db_input(stripslashes($update_customer_name)) . "',
          billing_name_f = '" . tep_db_input(stripslashes($update_customer_name_f)) . "',
          billing_company = '" . tep_db_input(stripslashes($update_customer_company)) . "',
          billing_street_address = '" . tep_db_input(stripslashes($update_customer_street_address)) . "',
          billing_suburb = '" . tep_db_input(stripslashes($update_customer_suburb)) . "',
          billing_city = '" . tep_db_input(stripslashes($update_customer_city)) . "',
          billing_state = '" . tep_db_input(stripslashes($update_customer_state)) . "',
          billing_postcode = '" . tep_db_input($update_customer_postcode) . "',
          billing_country = '" . tep_db_input(stripslashes($update_customer_country)) . "',";
      }

      $UpdateOrders .= "delivery_name = '" . tep_db_input(stripslashes($update_delivery_name)) . "',
        delivery_name_f = '" . tep_db_input(stripslashes($update_delivery_name_f)) . "',
        delivery_company = '" . tep_db_input(stripslashes($update_delivery_company)) . "',
        delivery_street_address = '" . tep_db_input(stripslashes($update_delivery_street_address)) . "',
        delivery_suburb = '" . tep_db_input(stripslashes($update_delivery_suburb)) . "',
        delivery_city = '" . tep_db_input(stripslashes($update_delivery_city)) . "',
        delivery_state = '" . tep_db_input(stripslashes($update_delivery_state)) . "',
        delivery_postcode = '" . tep_db_input($update_delivery_postcode) . "',
        delivery_country = '" . tep_db_input(stripslashes($update_delivery_country)) . "',
        payment_method = '" .  tep_db_input(payment::changeRomaji($_POST['payment_method'], PAYMENT_RETURN_TYPE_TITLE)) . "',
        torihiki_date = '" . tep_db_input($update_tori_torihiki_start_date) . "',
        torihiki_date_end = '" . tep_db_input($update_tori_torihiki_end_date) . "',
        torihiki_houhou = '" . tep_db_input($update_tori_torihiki_houhou) . "',
        cc_type = '" . tep_db_input($update_info_cc_type) . "',
        cc_owner = '" . tep_db_input($update_info_cc_owner) . "',";
 
      if(substr($update_info_cc_number,0,8) != "(Last 4)") {
        $UpdateOrders .= "cc_number = '$update_info_cc_number',";
      }   
      $UpdateOrders .= "cc_expires = '$update_info_cc_expires',
        orders_status = '" . tep_db_input($status) . "'";

      if(!$CommentsWithStatus) {
        $UpdateOrders .= ", comments = '" . tep_db_input($comments) . "'";
      }
      $UpdateOrders .= " where orders_id = '" . tep_db_input($oID) . "';";

      tep_db_query($UpdateOrders);

      orders_updated($oID);
      $order_updated = true;

      $check_status_query = tep_db_query("select customers_id, customers_name, customers_email_address, orders_status, date_purchased from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
      $check_status = tep_db_fetch_array($check_status_query);

      //住所信息入库

      tep_db_query("delete from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and customers_id='".$check_status['customers_id']."'");
      tep_db_query("update `".TABLE_ORDERS."` set `user_update` = '".$_SESSION['user_name']."' where `orders_id` = '".$oID."'");
      foreach($option_info_array as $ad_key=>$ad_value){
        
        $address_list_query = tep_db_query("select * from ". TABLE_ADDRESS ." where name_flag='". substr($ad_key,3) ."'");
        $address_list_array = tep_db_fetch_array($address_list_query);
        $ad_value = $address_list_array['comment'] == $ad_value && $address_list_array['type'] == 'textarea' ? '' : $ad_value;
   
        $ad_sql = "insert into ". TABLE_ADDRESS_ORDERS ." values(NULL,'".$oID."','{$check_status['customers_id']}','{$address_list_array['id']}','". substr($ad_key,3) ."','$ad_value')";
        $ad_query = tep_db_query($ad_sql);
        tep_db_free_result($address_list_query);
        tep_db_free_result($ad_query);
      }
 
      
      $address_show_array = array(); 
  $address_show_list_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_show_list_array = tep_db_fetch_array($address_show_list_query)){

    $address_show_array[$address_show_list_array['id']] = $address_show_list_array['name_flag'];
  }
  tep_db_free_result($address_show_list_query);
  $address_temp_str = '';
  foreach($option_info_array as $address_his_key=>$address_his_value){
    
      if(in_array(substr($address_his_key,3),$address_show_array)){

         $address_temp_str .= $address_his_value;
      }
  }
  
  $address_error = false;
  $address_sh_his_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id='{$check_status['customers_id']}' group by orders_id");
  while($address_sh_his_array = tep_db_fetch_array($address_sh_his_query)){

    $address_sh_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where customers_id='{$check_status['customers_id']}' and orders_id='". $address_sh_his_array['orders_id'] ."' order by id");
    $add_temp_str = '';
    while($address_sh_array = tep_db_fetch_array($address_sh_query)){
     
      if(in_array($address_sh_array['name'],$address_show_array)){

        $add_temp_str .= $address_sh_array['value'];
      }  
    }
    if($address_temp_str == $add_temp_str){

      $address_error = true;
      break;
    }
    tep_db_free_result($address_sh_query);
  }
  tep_db_free_result($address_sh_his_query);
if($address_error == false){
  foreach($option_info_array as $address_history_key=>$address_history_value){
      $address_history_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where name_flag='". substr($address_history_key,3) ."'");
      $address_history_array = tep_db_fetch_array($address_history_query);
      tep_db_free_result($address_history_query);
      $address_history_id = $address_history_array['id'];
      $address_history_add_query = tep_db_query("insert into ". TABLE_ADDRESS_HISTORY ." values(NULL,'$oID',{$check_status['customers_id']},$address_history_id,'{$address_history_array['name_flag']}','$address_history_value')");
      tep_db_free_result($address_history_add_query);
  }
}

      // fin mise ・jour
      // 1.3 UPDATE PRODUCTS #####

      $RunningSubTotal = 0;
      $RunningTax = 0;

      // Do pre-check for subtotal field existence (CWS)
      $ot_subtotal_found = false;

      foreach ($update_totals as $total_details) {
        extract($total_details,EXTR_PREFIX_ALL,"ot");
        if($ot_class == "ot_subtotal") {
          $ot_subtotal_found = true;
          break;
        }
      }

      // 1.3.1 Update orders_products Table
      //$products_delete = false;
      foreach ($update_products as $orders_products_id => $products_details) {
        // 1.3.1.1 Update Inventory Quantity
        $products_session_string_array = explode('_',$orders_products_id);
        $products_qty_add = false;
        if(count($products_session_string_array) <= 1){
          $op_query = tep_db_query("
            select products_id, 
            products_quantity
            from " . TABLE_ORDERS_PRODUCTS . " 
            where orders_id = '" . tep_db_input($oID) . "' 
            and orders_products_id = '".$orders_products_id."'");

          // 1.3.1.1 Update Inventory Quantity
          $order = tep_db_fetch_array($op_query);
        }else{
          $order = array();
          $order['products_id'] = $_SESSION['new_products_list'][$_GET['oID']]['orders_products'][$products_session_string_array[1]]['products_id']; 
          $order['products_quantity'] = 0;
          $products_qty_add = true;
        }
        if (($products_details["qty"] != $order['products_quantity']) || $products_qty_add == true) {
          $quantity_difference = ($products_details["qty"] - $order['products_quantity']);
          $p = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$order['products_id']."'"));

          $pr_quantity = $p['products_real_quantity'];
          $pv_quantity = $p['products_virtual_quantity'];
          // 增加库存
          $radices = tep_get_radices($order['products_id']);
          if($quantity_difference < 0){
            if ($_POST['update_products_real_quantity'][$orders_products_id]) {
              // 增加实数
              $pr_quantity = $pr_quantity - $quantity_difference*$radices;
            } else {
              // 增加架空
              $pv_quantity = $pv_quantity - $quantity_difference;
            }
            // 减少库存
          } else {
            // 实数卖空
            if ($pr_quantity - $quantity_difference*$radices < 0) {
              $pr_quantity = 0;
              $pv_quantity += ($pr_quantity - $quantity_difference);
            } else {
              $pr_quantity -= $quantity_difference*$radices;
            }
          }
          if($customer_guest['is_calc_quantity'] != '1') {
            tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = ".$pr_quantity.", products_virtual_quantity = ".$pv_quantity." where products_id = '" . (int)$order['products_id'] . "'");
          }
          tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = 0 where products_real_quantity < 0 and products_id = '" . (int)$order['products_id'] . "'");
          tep_db_query("update " . TABLE_PRODUCTS . " set products_virtual_quantity = 0 where products_virtual_quantity < 0 and products_id = '" . (int)$order['products_id'] . "'");
        }
        
        if($products_details["qty"] > 0) { 
          // a.) quantity found --> add to list & sum
          if(count($products_session_string_array) <= 1){
            $Query = "update " . TABLE_ORDERS_PRODUCTS . " set
                           products_model = '" . $products_details["model"] . "',
                           products_name = '" . str_replace("'", "&#39;", $products_details["name"]) . "',
                           products_price = '" .  (tep_check_product_type($orders_products_id) ? 0 - $products_details["p_price"] : $products_details["p_price"]) . "',
                           final_price = '" . (tep_get_bflag_by_product_id((int)$order['products_id']) ? 0 - $products_details["final_price"] : $products_details["final_price"]) . "',
                           products_tax = '" . $products_details["tax"] . "',
                           products_quantity = '" . $products_details["qty"] . "'
                           where orders_products_id = '$orders_products_id';";
            tep_db_query($Query);
          }else{
            $Query = "insert into " . TABLE_ORDERS_PRODUCTS . " set
                    orders_id = '$oID',
                    products_id = ".$order['products_id'].",
                    products_model = '".$products_details["model"]."',
                    products_name = '" . str_replace("'", "&#39;", $products_details["name"]) . "',
                    products_price = '". (tep_get_bflag_by_product_id((int)$order['products_id']) ? 0 - $products_details["p_price"] : $products_details["p_price"]) ."',
                    final_price = '" . (tep_get_bflag_by_product_id((int)$order['products_id']) ? 0 - $products_details["final_price"] : $products_details["final_price"]) . "',
                    products_tax = '". $products_details["tax"] ."',
                    site_id = '".tep_get_site_id_by_orders_id($oID)."',
                    products_rate = '".tep_get_products_rate($order['products_id'])."',
                    products_quantity = '" . (int)$products_details["qty"] . "';";
           tep_db_query($Query);
           $new_products_id = tep_db_insert_id();

           orders_updated($oID); 
          }

          $RunningSubTotal += $products_details["qty"] * $products_details["final_price"]; // version WITHOUT tax
          $RunningTax += (($products_details["tax"]/100) * ($products_details["qty"] * $products_details["final_price"]));

          // Update Any Attributes
          if (IsSet($products_details[attributes])) {
            foreach ($products_details["attributes"] as $orders_products_attributes_id => $attributes_details) {
              $input_option = array('title' => $attributes_details["option"], 'value' => $attributes_details["value"]); 
              $orders_products_attributes_array = explode('_', $orders_products_attributes_id);
              if(count($products_session_string_array) <= 1){
                $Query = "update " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set option_info = '".tep_db_input(serialize($input_option))."', options_values_price = '".$attributes_details['price']."' where orders_products_attributes_id = '$orders_products_attributes_id';"; 
                tep_db_query($Query);
              }else{
                $Query = "insert into " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set
                        orders_id = '$oID',
                        orders_products_id = $new_products_id,
                        options_values_price = '" .tep_db_input($attributes_details['price']) ."',
                        option_group_id = '" . $_SESSION['new_products_list'][$oID]['orders_products'][$products_session_string_array[1]]['products_attributes'][$orders_products_attributes_array[1]]['option_group_id'] . "',
                        option_item_id = '" . $_SESSION['new_products_list'][$oID]['orders_products'][$products_session_string_array[1]]['products_attributes'][$orders_products_attributes_array[1]]['option_item_id'] . "',
                        option_info = '".tep_db_input(serialize($input_option))."';";
                tep_db_query($Query); 
              }
            }
          }
        } else { 
          // b.) null quantity found --> delete
          if(count($products_session_string_array) <= 1){
            $Query = "delete from " . TABLE_ORDERS_PRODUCTS . " where orders_products_id = '$orders_products_id';";
            tep_db_query($Query);
            $Query = "delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_products_id = '$orders_products_id';";
            tep_db_query($Query);
            if($orders_status_flag && tep_orders_finishqa($oID) == '1'){
              tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered - " .  (int)($order['products_quantity']) ." where products_id = '" . $order['products_id'] . "'");
            }
          }
        }
      }

      $orders_type_str = tep_get_order_type_info($oID);
      tep_db_query("update `".TABLE_ORDERS."` set `orders_type` = '".$orders_type_str."' where orders_id = '".tep_db_input($oID)."'");
      // 1.4. UPDATE SHIPPING, DISCOUNT & CUSTOM TAXES #####

      foreach($update_totals as $total_index => $total_details) {
        extract($total_details,EXTR_PREFIX_ALL,"ot");

        // Here is the major caveat: the product is priced in default currency, while shipping etc. are priced in target currency. We need to convert target currency
        // into default currency before calculating RunningTax (it will be converted back before display)
        if ($ot_class == "ot_shipping" || $ot_class == "ot_lev_discount" || $ot_class == "ot_customer_discount" || $ot_class == "ot_custom" || $ot_class == "ot_cod_fee") {
          $order = new order($oID);
          $RunningTax += $ot_value * $products_details['tax'] / $order->info['currency_value'] / 100 ; // corrected tax by cb
        }
      }

      // 1.5 UPDATE TOTALS #####

      $RunningTotal = 0;
      $sort_order = 0;

      // 1.5.1 Do pre-check for Tax field existence
      $ot_tax_found = 0;
      foreach ($update_totals as $total_details) {
        extract($total_details,EXTR_PREFIX_ALL,"ot");
        if ($ot_class == "ot_tax") {
          $ot_tax_found = 1;
          break;
        }
      }

      // 1.5.2. Summing up total
      foreach ($update_totals as $total_index => $total_details) {

        // 1.5.2.1 Prepare Tax Insertion      
        extract($total_details,EXTR_PREFIX_ALL,"ot");

        // inserisce la tassa se non la trova oppure ??
        if (trim(strtolower($ot_title)) == "iva" || trim(strtolower($ot_title)) == "iva:") {
          if ($ot_class != "ot_tax" && $ot_tax_found == 0) {
            // Inserting Tax
            $ot_class = "ot_tax";
            $ot_value = "x"; // This gets updated in the next step
            $ot_tax_found = 1;
          }
        }

        // 1.5.2.2 Update ot_subtotal, ot_tax, and ot_total classes
        if (trim($ot_title) || $ot_class == "ot_point") {

          $sort_order++;
          if ($ot_class == "ot_subtotal") {
            $ot_value = $RunningSubTotal;
          }           
          if ($ot_class == "ot_tax") {
            $ot_value = $RunningTax;
          }

          // Check for existence of subtotals (CWS)                      
          if ($ot_class == "ot_total") {
            // I can't find out, WHERE the $RunningTotal is calculated - but the subtraction of the tax was wrong (in our shop)
            $ot_value = $RunningTotal;

          }


          $order = new order($oID);

          if ($customer_guest['customers_guest_chk'] == 0 && $ot_class == "ot_point" && $ot_value != $before_point) { //会員ならポントの増減
            $point_difference = ($ot_value - $before_point);
            tep_db_query("update " . TABLE_CUSTOMERS . " set point = point - " . $point_difference . " where customers_id = '" . $order->customer['id'] . "'"); 
          }

          $ot_text = $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']);

          if ($ot_class == "ot_total") {
            $ot_text = "<b>" . $ot_text . "</b>";
          }

          if($ot_class = 'ot_custom'){

              if($_POST['sign_value_'.$total_index] == '0'){

                $ot_value = 0-$ot_value;
              } 
          }
          if($ot_total_id > 0 || $ot_class == "ot_point") { 
            $Query = 'UPDATE ' . TABLE_ORDERS_TOTAL . ' SET
              title = "' . $ot_title . '",
                    value = "' . tep_insert_currency_value($ot_value) . '",
                    sort_order = "' . $sort_order . '"
                      WHERE orders_total_id = "' . $ot_total_id . '"';
            tep_db_query($Query);
          } else { 
            $Query = 'INSERT INTO ' . TABLE_ORDERS_TOTAL . ' SET
              orders_id = "' . $oID . '",
                        title = "' . $ot_title . '",
                        text = "",
                        value = "' . tep_insert_currency_value($ot_value) . '",
                        class = "' . $ot_class . '",
                        sort_order = "' . $sort_order . '"';
            tep_db_query($Query);
          }

          if ($ot_class == "ot_shipping" || $ot_class == "ot_lev_discount" || $ot_class == "ot_customer_discount" || $ot_class == "ot_custom" || $ot_class == "ot_cod_fee") {
            // Again, because products are calculated in terms of default currency, we need to align shipping, custom etc. values with default currency
            $RunningTotal += $ot_value / $order->info['currency_value'];


        } else {
          $RunningTotal += $ot_value;
        }

        } elseif (($ot_total_id > 0) && ($ot_class != "ot_shipping") && ($ot_class != "ot_point")) { 
          // Delete Total Piece
          $Query = "delete from " . TABLE_ORDERS_TOTAL . " where orders_total_id = '$ot_total_id'";
          tep_db_query($Query);
        }

      }

      $order = new order($oID);
      $RunningSubTotal = 0;
      $RunningTax = 0;

      for ($i=0; $i<sizeof($order->products); $i++) {
        if (DISPLAY_PRICE_WITH_TAX == 'true') {
          $RunningSubTotal += (tep_add_tax(($order->products[$i]['qty'] * $order->products[$i]['final_price']), $order->products[$i]['tax']));
        } else {
          $RunningSubTotal += ($order->products[$i]['qty'] * $order->products[$i]['final_price']);
        }

        $RunningTax += (($order->products[$i]['tax'] / 100) * ($order->products[$i]['qty'] * $order->products[$i]['final_price']));     
      }


      $new_subtotal = $RunningSubTotal;
      $new_tax = $RunningTax;

      //subtotal
      tep_db_query("update " . TABLE_ORDERS_TOTAL . " set value = '".tep_insert_currency_value($new_subtotal)."' where class='ot_subtotal' and orders_id = '".$oID."'");

      $campaign_fee = get_campaion_fee($new_subtotal, $oID, $order->info['site_id']);
      tep_db_query("update ". TABLE_CUSTOMER_TO_CAMPAIGN." set campaign_fee = '".$campaign_fee."' where orders_id = '".$oID."' and site_id = '".$order->info['site_id']."'"); 
      //tax
      $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_ORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
      $plustax = tep_db_fetch_array($plustax_query);
      if($plustax['cnt'] > 0) {
        tep_db_query("update " . TABLE_ORDERS_TOTAL . " set value = '".tep_insert_currency_value($new_tax)."' where class='ot_tax' and orders_id = '".$oID."'");
      }

      //point修正中
      $point_query = tep_db_query("select sum(value) as total_point from " . TABLE_ORDERS_TOTAL . " where class = 'ot_point' and orders_id = '" . $oID . "'");
      $total_point = tep_db_fetch_array($point_query);

      //total
      $total_query = tep_db_query("select sum(value) as total_value from " . TABLE_ORDERS_TOTAL . " where class != 'ot_total' and class != 'ot_point' and orders_id = '" . $oID . "'");
      $total_value = tep_db_fetch_array($total_query);

      if ($plustax['cnt'] == 0) {
        $newtotal = $total_value["total_value"] + $new_tax;
      } else {
        if(DISPLAY_PRICE_WITH_TAX == 'true') {
          $newtotal = $total_value["total_value"] - $new_tax;
        } else {
          $newtotal = $total_value["total_value"];
        }
      }

      // 返回pint
      if ($newtotal > 0) {
        $newtotal -= $total_point["total_point"];
      }

      $handle_fee = $cpayment->handle_calc_fee($_POST['payment_method'], $shipping_money_total+$fee_total-$point_fee+$shipping_fee);
      $newtotal = $newtotal+$handle_fee;
      $totals = "update " . TABLE_ORDERS_TOTAL . " set value = '" .  intval(floor($newtotal+$campaign_fee+$shipping_fee)) . "' where class='ot_total' and orders_id = '" . $oID . "'";
      tep_db_query($totals);

      $update_orders_sql = "update ".TABLE_ORDERS." set code_fee = '".$handle_fee."' where orders_id = '".$oID."'";
      tep_db_query($update_orders_sql);

      // 最终处理（更新并返信）
        $check_status_query = tep_db_query("
        select orders_id, 
        customers_name, 
        customers_id,
        customers_email_address, 
        orders_status, 
        date_purchased, 
        payment_method, 
        torihiki_date,
        torihiki_date_end
        from " . TABLE_ORDERS . " 
        where orders_id = '" . tep_db_input($oID) . "'");
        $check_status = tep_db_fetch_array($check_status_query);
        
        $ot_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_total'");
        $ot_result = tep_db_fetch_array($ot_query);
        $otm = (int)$ot_result['value'] . SENDMAIL_EDIT_ORDERS_PRICE_UNIT;

        $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
        $os_result = tep_db_fetch_array($os_query);
        
        $title = str_replace(array(
              '${NAME}',
              '${MAIL}',
              '${ORDER_D}',
              '${ORDER_N}',
              '${PAY}',
              '${ORDER_M}',
              '${ORDER_S}',
              '${SITE_NAME}',
              '${SITE_URL}',
              '${SUPPORT_EMAIL}',
              '${PAY_DATE}'
              ),array(
                $check_status['customers_name'],
                $check_status['customers_email_address'],
                tep_date_long($check_status['date_purchased']),
                $oID,
                $check_status['payment_method'],
                $otm,
                $os_result['orders_status_name'],
                get_configuration_by_site_id('STORE_NAME', $order->info['site_id']),
                get_url_by_site_id($order->info['site_id']),
                get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $order->info['site_id']),
                date('Y'.SENDMAIL_TEXT_DATE_YEAR.'n'.SENDMAIL_TEXT_DATE_MONTH.'j'.SENDMAIL_TEXT_DATE_DAY,strtotime(tep_get_pay_day()))
                ),$title);

        $comments = str_replace(array(
              '${NAME}',
              '${MAIL}',
              '${ORDER_D}',
              '${ORDER_N}',
              '${PAY}',
              '${ORDER_M}',
              '${ORDER_S}',
              '${SITE_NAME}',
              '${SITE_URL}',
              '${SUPPORT_EMAIL}',
              '${PAY_DATE}'
              ),array(
                $check_status['customers_name'],
                $check_status['customers_email_address'],
                tep_date_long($check_status['date_purchased']),
                $oID,
                $check_status['payment_method'],
                $otm,
                $os_result['orders_status_name'],
                get_configuration_by_site_id('STORE_NAME', $order->info['site_id']),
                get_url_by_site_id($order->info['site_id']),
                get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $order->info['site_id']),
                date('Y'.SENDMAIL_TEXT_DATE_YEAR.'n'.SENDMAIL_TEXT_DATE_MONTH.'j'.SENDMAIL_TEXT_DATE_DAY,strtotime(tep_get_pay_day()))
                ),$comments);
        tep_order_status_change($oID,$status);
        tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
        orders_updated(tep_db_input($oID));
        $notify_comments = '';
        $notify_comments_mail = $comments;
        $customer_notified = '0';

        if ($comments) {
          $notify_comments_mail .= "\n\n";
        }

        if (isset($_POST['notify_comments']) && ($_POST['notify_comments'] == 'on')) {
          $notify_comments = $comments;
        }
        if (isset($_POST['notify']) && ($_POST['notify'] == 'on')) {
          $products_ordered_mail = '';
          $max_c_len = 0;
          $max_len_array = array();
          for ($mi=0; $mi<sizeof($order->products); $mi++) {
            for ($mj=0; $mj<sizeof($order->products[$mi]['attributes']); $mj++) {
              $max_len_array[] = mb_strlen($order->products[$mi]['attributes'][$mj]['option_info']['title'], 'utf-8'); 
            } 
          }
          if (!empty($max_len_array)) {
            $max_c_len = max($max_len_array); 
          }
          if ($max_c_len < 4) {
            $max_c_len = 4; 
          }
          $search_products_id_list = array();
          $mode_products_name_list = array();
          for ($i=0; $i<sizeof($order->products); $i++) {
            $search_products_id_list[] = $order->products[$i]['id'];
            $mode_products_name_list[] = $order->products[$i]['name'];
            $products_ordered_mail .= SENDMAIL_ORDERS_PRODUCTS.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_ORDERS_PRODUCTS, 'utf-8'))).'：' . $order->products[$i]['name'] . '（' . $order->products[$i]['model'] . '）';
            if ($order->products[$i]['price'] != '0') {
              $products_ordered_mail .= '（'.$currencies->display_price($order->products[$i]['price'], $order->products[$i]['tax']).'）'; 
            }
            $products_ordered_mail .= "\n"; 
            // Has Attributes?
            if (sizeof($order->products[$i]['attributes']) > 0) {
              for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
                $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['id'];
                $products_ordered_mail .=  tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['title'], array("'"=>"&quot;")) . str_repeat('　', intval($max_c_len - mb_strlen($order->products[$i]['attributes'][$j]['option_info']['title'], 'utf-8'))).'：';
                $products_ordered_mail .= tep_parse_input_field_data(str_replace(array("<br>", "<BR>", "\r", "\n", "\r\n"), "", $order->products[$i]['attributes'][$j]['option_info']['value']), array("'"=>"&quot;"));
                if ($order->products[$i]['attributes'][$j]['price'] != '0') {
                  $products_ordered_mail .= '（'.$currencies->format($order->products[$i]['attributes'][$j]['price']).'）'; 
                }
                $products_ordered_mail .= "\n"; 
              }
            }

            $pcount_email = '';
            if(isset($order->products[$i]['rate'])
              &&$order->products[$i]['rate']!=1
              &&$order->products[$i]['rate']!=0){
              $pcount_email = ' ('.number_format($order->products[$i]['qty']*$order->products[$i]['rate']).')';
            }
            $products_ordered_mail .= SENDMAIL_QTY_NUM.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_QTY_NUM, 'utf-8'))).'：' .  $order->products[$i]['qty'] . SENDMAIL_EDIT_ORDERS_NUM_UNIT .  $pcount_email . "\n";
            $products_ordered_mail .= SENDMAIL_TABLE_HEADING_PRODUCTS_PRICE.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_TABLE_HEADING_PRODUCTS_PRICE, 'utf-8'))).'：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax']) . "\n";
            $products_ordered_mail .= str_replace(':', '', SENDMAIL_ENTRY_SUB_TOTAL).str_repeat('　', intval($max_c_len - mb_strlen(str_replace(':', '', SENDMAIL_ENTRY_SUB_TOTAL), 'utf-8'))).'：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . "\n";
            $products_ordered_mail .= '------------------------------------------' . "\n";
          }

          $total_details_mail = '';
          $totals_query = tep_db_query("select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . tep_db_input($oID) . "' order by sort_order");
          $order->totals = array();
          while ($totals = tep_db_fetch_array($totals_query)) {
            if ($totals['class'] == "ot_point" || $totals['class'] == "ot_subtotal") {
              if ($totals['class'] == "ot_point") {
                $camp_exists_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$oID."' and site_id = '".$order->info['site_id']."'"); 
                if (tep_db_num_rows($camp_exists_query)) {
                  $total_details_mail .= SENDMAIL_TEXT_POINT_ONE . $currencies->format(abs($campaign_fee)) . "\n";
                } else {
                  if ((int)$totals['value'] >= 1 && $totals['class'] != "ot_subtotal") {
                    $total_details_mail .= SENDMAIL_TEXT_POINT_ONE .  $currencies->format($totals['value']) . "\n";
                  }
                }
              } else {
                if ((int)$totals['value'] >= 1 && $totals['class'] != "ot_subtotal") {
                  $total_details_mail .= SENDMAIL_TEXT_POINT_ONE .  $currencies->format($totals['value']) . "\n";
                }
              }
            } elseif ($totals['class'] == "ot_total") {
              if($handle_fee)
                $total_details_mail .= SENDMAIL_TEXT_HANDLE_FEE.$currencies->format($handle_fee)."\n";
              $total_details_mail .= SENDMAIL_TEXT_PAYMENT_AMOUNT . $currencies->format($totals['value']);
            } else {
              // 去掉 决算费用  消费税
              $totals['title'] = str_replace(SENDMAIL_TEXT_TRANSACTION_FEE, SENDMAIL_TEXT_HANDLE_FEE_ONE, $totals['title']);
              $total_details_mail .= $totals['title'] . str_repeat('　', intval((16 - strlen($totals['title']))/2)) . '：' . $currencies->format($totals['value']) . "\n";
            }
          }

          $email = '';
          $email .= $notify_comments_mail;
          $email_content = $products_ordered_mail;
          $email_content .= $total_details_mail;
          $email = str_replace('${CONTENT}', $email_content, $email); 
          $fetch_time_start_array = explode(' ', $check_status['torihiki_date']); 
          $fetch_time_end_array = explode(' ', $check_status['torihiki_date_end']); 
          
          $tmp_date = date('D', strtotime($check_status['torihiki_date'])); 
          switch(strtolower($tmp_date)) {
            case 'mon':
             $week_str = '（'.SENDMAIL_TEXT_DATE_MONDAY.'）'; 
             break;
            case 'tue':
             $week_str =  '（'.SENDMAIL_TEXT_DATE_TUESDAY.'）'; 
             break;
            case 'wed':
             $week_str =  '（'.SENDMAIL_TEXT_DATE_WEDNESDAY.'）'; 
             break;
           case 'thu':
             $week_str =  '（'.SENDMAIL_TEXT_DATE_THURSDAY.'）'; 
             break;
           case 'fri':
             $week_str =  '（'.SENDMAIL_TEXT_DATE_FRIDAY.'）'; 
             break;
           case 'sat':
             $week_str =  '（'.SENDMAIL_TEXT_DATE_STATURDAY.'）'; 
             break;
           case 'sun':
             $week_str =  '（'.SENDMAIL_TEXT_DATE_SUNDAY.'）'; 
             break;
           default:
             break;
          }
          
          $fetch_time_str = date('Y'.SENDMAIL_TEXT_DATE_YEAR.'m'.SENDMAIL_TEXT_DATE_MONTH.'d'.SENDMAIL_TEXT_DATE_DAY, strtotime($check_status['torihiki_date'])).$week_str.$fetch_time_start_array[1].' '.SENDMAIL_TEXT_TIME_LINK.' '.$fetch_time_end_array[1];
          
          $email = str_replace('${SHIPPING_TIME}', $fetch_time_str, $email); 
          $title = str_replace('${SHIPPING_TIME}', $fetch_time_str, $title); 
          $email = str_replace(TEXT_MONEY_SYMBOL,SENDMAIL_TEXT_MONEY_SYMBOL,$email);
          $search_products_name_list = array();
          foreach($search_products_id_list as $products_name_value){
            $search_products_name_query = tep_db_query("select products_name from ". TABLE_PRODUCTS_DESCRIPTION ." where products_id='".$products_name_value."' and language_id='".$languages_id."' and (site_id='".$order->info['site_id']."' or site_id='0') order by site_id DESC");
            $search_products_name_array = tep_db_fetch_array($search_products_name_query);
            tep_db_free_result($search_products_name_query);
            $search_products_name_list[] = $search_products_name_array['products_name'];
          }
          if ($customer_guest['is_send_mail'] != '1')
            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], $title, str_replace($mode_products_name_list,$search_products_name_list,$email), get_configuration_by_site_id('STORE_OWNER', $order->info['site_id']), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $order->info['site_id']),$order->info['site_id']);

          tep_mail(get_configuration_by_site_id('STORE_OWNER', $order->info['site_id']), get_configuration_by_site_id('SENTMAIL_ADDRESS', $order->info['site_id']), $title, $email, $check_status['customers_name'], $check_status['customers_email_address'],$order->info['site_id']);
          $customer_notified = '1';
        }
        
        if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && MODULE_ORDER_TOTAL_POINT_ADD_STATUS != '0') {
          $pcount_query = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".MODULE_ORDER_TOTAL_POINT_ADD_STATUS."' and orders_id = '".$oID."'");
          $pcount = tep_db_fetch_array($pcount_query);
          if($pcount['cnt'] == 0 && $status == MODULE_ORDER_TOTAL_POINT_ADD_STATUS) {
            $query1 = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
            $result1 = tep_db_fetch_array($query1);
            $query2 = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_point' and orders_id = '".tep_db_input($oID)."'");
            $result2 = tep_db_fetch_array($query2);
            $query3 = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_subtotal' and orders_id = '".tep_db_input($oID)."'");
            $result3 = tep_db_fetch_array($query3);
            $query4 = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '".$result1['customers_id']."'");
            $result4 = tep_db_fetch_array($query4);
            if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
              $customer_id = $result1['customers_id'];
              $ptoday = date("Y-m-d H:i:s", time());
              $pstday_array = getdate();
              $pstday = date("Y-m-d H:i:s", mktime($pstday_array[hours],$pstday_array[mimutes],$pstday_array[second],$pstday_array[mon],($pstday_array[mday] - MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN),$pstday_array[year]));
              $total_buyed_date = 0;
              $customer_level_total_query = tep_db_query("select * from orders where customers_id = '".$customer_id."' and date_purchased >= '".$pstday."'");
              if(tep_db_num_rows($customer_level_total_query)) {
                while($customer_level_total = tep_db_fetch_array($customer_level_total_query)) {
                  $cltotal_subtotal_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_subtotal'");
                  $cltotal_subtotal = tep_db_fetch_array($cltotal_subtotal_query);

                  $cltotal_point_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_point'");
                  $cltotal_point = tep_db_fetch_array($cltotal_subtotal_query);

                  $total_buyed_date += ($cltotal_subtotal['value'] - $cltotal_point['value']);
               }
             }
             $total_buyed_date = $total_buyed_date - ($result3['value'] - (int)$result2['value']);
          
             if(mb_ereg("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK)) {
               $back_rate_array = explode("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
               $back_rate = MODULE_ORDER_TOTAL_POINT_FEE;
               for($j=0; $j<sizeof($back_rate_array); $j++) {
                $back_rate_array2 = explode(",", $back_rate_array[$j]);
                 if($back_rate_array2[2] <= $total_buyed_date) {
                   $back_rate = $back_rate_array2[1];
                   $back_rate_name = $back_rate_array2[0];
                 }
               }
            } else {
              $back_rate_array = explode(",", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
              if($back_rate_array[2] <= $total_buyed_date) {
                $back_rate = $back_rate_array[1];
                $back_rate_name = $back_rate_array[0];
              }
            }
            $point_rate = $back_rate;
          } else {
            $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
          }
        
          if ($result3['value'] >= 0) {
            $get_point = ($result3['value'] - (int)$result2['value']) * $point_rate;
          } else {
            $get_point = $cpayment->admin_get_fetch_point(payment::changeRomaji($_POST['payment_method'],'code'),$result3['value']);
          }
        }else{
          $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
          $os_result = tep_db_fetch_array($os_query);
          if($os_result['orders_status_name']==TEXT_NOTICE_PAYMENT){
            $query1 = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
            $result1 = tep_db_fetch_array($query1);

            $get_point = $cpayment->admin_get_orders_point(payment::changeRomaji($_POST['payment_method'],'code'),$oID);
            $point_done_query =tep_db_query("select count(orders_status_history_id) cnt from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".$status."' and orders_id = '".tep_db_input($oID)."'");
            $point_done_row  =  tep_db_fetch_array($point_done_query);
          }
        }
      }
      //增加销售处理
      $orders_status_flag = false;
      $orders_status_history_flag = false;
      $orders_oa_flag = false;
      $end_orders_status_flag = false;
      $status_list_array = array();
      $orders_status_finish_query = tep_db_query("select orders_status_id,finished from ". TABLE_ORDERS_STATUS);
      while($orders_status_finish_array = tep_db_fetch_array($orders_status_finish_query)){

        $status_list_array[$orders_status_finish_array['orders_status_id']] = $orders_status_finish_array['finished'];
      }
      tep_db_free_result($orders_status_finish_query);
      $orders_status_flag = $status_list_array[tep_db_input($status)] == 1 ? true : $orders_status_flag;
      $orders_status_history_list_query = tep_db_query("select orders_status_id from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".tep_db_input($oID)."'");
      while($orders_status_history_list_array = tep_db_fetch_array($orders_status_history_list_query)){

        if($status_list_array[$orders_status_history_list_array['orders_status_id']] == 1){

          $orders_status_history_flag = true;
          break;
        }
      }
      tep_db_free_result($orders_status_history_list_query);

      $orders_oa_flag = tep_orders_finishqa(tep_db_input($oID)) == 1 ? true : $orders_oa_flag;

      //获取最后一次订单状态
      $orders_status_id_query = tep_db_query("select orders_status_id from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".tep_db_input($oID)."' order by date_added desc limit 0,1");
      $orders_status_id_array = tep_db_fetch_array($orders_status_id_query);
      tep_db_free_result($orders_status_id_query);
      $end_orders_status_flag = $status_list_array[$orders_status_id_array['orders_status_id']] == 1 ? true : $end_orders_status_flag;

      if($orders_oa_flag == true && $orders_status_flag == true && ($orders_status_history_flag == false || $end_orders_status_flag == false)){

        $orders_products_query = tep_db_query("select products_id,products_quantity from ". TABLE_ORDERS_PRODUCTS ." where orders_id='".tep_db_input($oID)."'");
        while($orders_products_array = tep_db_fetch_array($orders_products_query)){
          tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%d', $orders_products_array['products_quantity']) . " where products_id = '" . (int)$orders_products_array['products_id'] . "'");
        }
        tep_db_free_result($orders_products_query);
      }

      if($orders_oa_flag == true && $orders_status_history_flag == true && $orders_status_flag == false && $end_orders_status_flag == true){

        $orders_products_query = tep_db_query("select products_id,products_quantity from ". TABLE_ORDERS_PRODUCTS ." where orders_id='".tep_db_input($oID)."'");
        while($orders_products_array = tep_db_fetch_array($orders_products_query)){
          tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered - " . sprintf('%d', $orders_products_array['products_quantity']) . " where products_id = '" . (int)$orders_products_array['products_id'] . "'");
        }
        tep_db_free_result($orders_products_query);    
      }
        tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments, user_added) values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" . tep_db_input($customer_notified) . "', '" .  mysql_real_escape_string($comment_arr['comment'].$comments_text) . "', '".tep_db_input($update_user_info['name'])."')");
        $order_updated_2 = true;

      if ($order_updated && $order_updated_2) {
        $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
      } else {
        $messageStack->add_session(TEXT_ERROR_NO_SUCCESS, 'error');
      }
      unset($_SESSION['orders_update_products'][$oID]);
      unset($_SESSION['new_products_list'][$oID]);
      unset($_SESSION['orders_update_time'][$oID]);
      tep_redirect(tep_href_link("edit_orders.php", tep_get_all_get_params(array('action','clear_products')) . 'action=edit'));

      break;

      // 2. ADD A PRODUCT ###############################################################################################
    case 'add_product':
      $a_option = new HM_Option();
      if($step == 5)
      {
        // 2.1 GET ORDER INFO #####
        
        $oID = tep_db_prepare_input($_GET['oID']);
        $order = new order($oID);

        $AddedOptionsPrice = 0;
        
        $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>", "'", "\"");
        // 2.1.1 Get Product Attribute Info
        foreach($_POST as $op_key => $op_value)
        {
          $op_pos = substr($op_key, 0, 3);
          if ($op_pos == 'op_') {
            $op_tmp_value = str_replace(' ', '', $op_value);
            $op_tmp_value = str_replace('　', '', $op_value);
            $op_info_array = explode('_', $op_key);
            $op_item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_info_array[1]."' and id = '".$op_info_array[3]."'");
            $op_item_res = tep_db_fetch_array($op_item_query); 
            if ($op_tmp_value == '') {
              $_POST[$op_key] = $a_option->msg_is_null; 
            }
            if ($op_item_res) {
              if ($op_item_res['type'] == 'radio') {
                $o_option_array = @unserialize($op_item_res['option']);
                if (!empty($o_option_array['radio_image'])) {
                  foreach ($o_option_array['radio_image'] as $or_key => $or_value) {
                    if (trim(str_replace($replace_arr, '', nl2br(stripslashes($or_value['title']))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_value)))))) {
                      $AddedOptionsPrice += $or_value['money'];
                      break; 
                    }
                  }
                }
              } else if ($op_item_res['type'] == 'textarea') {
                $t_option_array = @unserialize($op_item_res['option']);
                $tmp_t_single = false; 
                if ($t_option_array['require'] == '0') {
                  if ($op_tmp_value == '') {
                    $tmp_t_single = true; 
                  }
                }
                if (!$tmp_t_single) {
                  $AddedOptionsPrice += $op_item_res['price'];
                }
              } else {
                $AddedOptionsPrice += $op_item_res['price'];
              }
            }
          }
        }
        // 2.1.2 Get Product Info
        $InfoQuery = "
          select p.products_model, 
                 p.products_price, 
                 pd.products_name, 
                 p.products_tax_class_id, 
                 p.products_small_sum,
                 p.products_price_offset
                   from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pd.products_id=p.products_id 
                   where p.products_id='$add_product_products_id' 
                   and pd.site_id = '0'
                   and pd.language_id = '" . (int)$languages_id . "'";
        $result = tep_db_query($InfoQuery);

        $row = tep_db_fetch_array($result);
        extract($row, EXTR_PREFIX_ALL, "p");

        $p_products_price = tep_get_final_price($p_products_price, $p_products_price_offset, $p_products_small_sum, (int)$add_product_quantity);

        // Following functions are defined at the bottom of this file
        $CountryID = tep_get_country_id($order->delivery["country"]);
        $ZoneID = tep_get_zone_id($CountryID, $order->delivery["state"]);

        $ProductsTax = tep_get_tax_rate($p_products_tax_class_id, $CountryID, $ZoneID);  

        $new_products_attributes_array = array();
        foreach($_POST as $op_i_key => $op_i_value)
        {
          $op_pos = substr($op_i_key, 0, 3);
          if ($op_pos == 'op_') {
            $op_i_tmp_value = str_replace(' ', '', $op_i_value);
            $op_i_tmp_value = str_replace('　', '', $op_i_value);
            if ($op_i_tmp_value == '') {
              continue; 
            }
            $i_op_array = explode('_', $op_i_key); 
            $ioption_item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$i_op_array[1]."' and id = '".$i_op_array[3]."'"); 
            $ioption_item_res = tep_db_fetch_array($ioption_item_query); 
            if ($ioption_item_res) {
            $input_option_array = array('title' => $ioption_item_res['front_title'], 'value' => str_replace("<BR>", "<br>", stripslashes($op_i_value))); 
            $op_price = 0; 
            if ($ioption_item_res['type'] == 'radio') {
              $io_option_array = @unserialize($ioption_item_res['option']);
              if (!empty($io_option_array['radio_image'])) {
                foreach ($io_option_array['radio_image'] as $ior_key => $ior_value) {
                  if (trim(str_replace($replace_arr, '', nl2br(stripslashes($ior_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_i_value))))) {
                    $op_price = $ior_value['money']; 
                    break; 
                  }
                }
              }
            } else if ($ioption_item_res['type'] == 'textarea') {
              $iot_option_array = @unserialize($ioption_item_res['option']);
              $tmp_iot_single = false; 
              if ($iot_option_array['require'] == '0') {
                if ($op_i_value == $a_option->msg_is_null) {
                  $tmp_iot_single = true; 
                }
              }
              if ($tmp_iot_single) {
                $op_price = 0; 
              } else {
                $op_price = $ioption_item_res['price']; 
              }
            } else {
              $op_price = $ioption_item_res['price']; 
            }
            $new_products_attributes_array[] = array('options_values_price'=>tep_db_input($op_price),
                                                   'option_group_id'=>$ioption_item_res['group_id'], 
                                                   'option_item_id'=>$ioption_item_res['id'],
                                                   'option_info'=>tep_db_input(serialize($input_option_array))
                                                   );  
            }
          } 
        } 

        // 2.2 UPDATE ORDER SESSION ##### 
        $products_list_array = array('products_id'=>$add_product_products_id,
                                     'products_model'=>$p_products_model, 
                                     'products_name'=>str_replace("'", "&#39;", $p_products_name),
                                     'products_price'=>$p_products_price,
                                     'final_price'=>($p_products_price + $AddedOptionsPrice),
                                     'products_tax'=>$ProductsTax,
                                     'site_id'=>tep_get_site_id_by_orders_id($oID),
                                     'products_rate'=>tep_get_products_rate($add_product_products_id),
                                     'products_quantity'=>(int)$add_product_quantity,
                                     'products_attributes'=>$new_products_attributes_array
                                   );
        $_SESSION['new_products_list_add'][$oID]['orders_products'][] = $products_list_array; 
        // 2.2.2 Calculate Tax and Sub-Totals
        $order = new order($oID);
        $RunningSubTotal = 0;
        $RunningTax = 0; 
        for ($i=0; $i<sizeof($order->products); $i++) {
          if (DISPLAY_PRICE_WITH_TAX == 'true') {
            $RunningSubTotal += (tep_add_tax(($order->products[$i]['qty'] * $order->products[$i]['final_price']), $order->products[$i]['tax']));
          } else {
            $RunningSubTotal += ($order->products[$i]['qty'] * $order->products[$i]['final_price']);
          }

          $RunningTax += (($order->products[$i]['tax'] / 100) * ($order->products[$i]['qty'] * $order->products[$i]['final_price']));     
        }


        $new_subtotal = $RunningSubTotal;
        $new_tax = $RunningTax;

        $campaign_fee = get_campaion_fee($new_subtotal, $oID, $order->info['site_id']); 
        //tax
        $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_ORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
        $plustax = tep_db_fetch_array($plustax_query);
   
        //total
        $total_query = tep_db_query("select sum(value) as total_value from " . TABLE_ORDERS_TOTAL . " where class != 'ot_total' and class != 'ot_point' and orders_id = '".$oID."'");
        $total_value = tep_db_fetch_array($total_query);
        $point_total_query = tep_db_query("select value as point_value from " . TABLE_ORDERS_TOTAL . " where class = 'ot_point' and orders_id = '".$oID."'");
        $point_total_value = tep_db_fetch_array($point_total_query);
        $total_value["total_value"] -= $point_total_value['point_value'];
        if($plustax['cnt'] == 0) {
          $newtotal = $total_value["total_value"] + $new_tax;
        } else {
          if(DISPLAY_PRICE_WITH_TAX == 'true') {
            $newtotal = $total_value["total_value"] - $new_tax;
          } else {
            $newtotal = $total_value["total_value"];
          }
        }
        $handle_fee = $cpayment->handle_calc_fee(payment::changeRomaji($order->info['payment_method'], PAYMENT_RETURN_TYPE_CODE), $newtotal);
        $newtotal   = $newtotal+$handle_fee; 
        $_SESSION['orders_products_price_subtotal'][$_GET['oID']] = tep_insert_currency_value($new_subtotal);
        $_SESSION['orders_products_price_total'][$_GET['oID']] = $newtotal+$campaign_fee+$shipping_fee;
        $_SESSION['orders_products_price_point'][$_GET['oID']] = $point_total_value['point_value'];
        if(!isset($_SESSION['clear_products_flag'])){

          $_SESSION['clear_products_flag'] = true;
        }
        tep_redirect(tep_href_link("edit_orders.php", tep_get_all_get_params(array('action')) . 'action=add_product&step=1'));
      }
      break;
  }
}

if (($action == 'edit') && isset($_GET['oID'])) {
  if(isset($_GET['once_pwd'])&&$_GET['once_pwd']){
    tep_insert_pwd_log($_GET['once_pwd'],$ocertify->auth_user);
  }
  $oID = tep_db_prepare_input($_GET['oID']);
  $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
  $order_exists = true;
  if (!tep_db_num_rows($orders_query)) {
    $order_exists = false;
    $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
  }
}

//计算配送费用
$shipping_array = $order->products;
$shipping_weight_total = 0;
$new_products_total = 0;

if($_SESSION['new_products_list'][$_GET['oID']]['orders_products']){
    foreach($_SESSION['new_products_list'][$_GET['oID']]['orders_products'] as $new_products_key=>$new_products_value){

      $new_products_total += $new_products_value['final_price']*$new_products_value['products_quantity'];
      $shipping_fee_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id=". $new_products_value['products_id']);
      $shipping_fee_array = tep_db_fetch_array($shipping_fee_query);
      $shipping_weight_total += (isset($_SESSION['orders_update_products'][$oID]['o_'.$new_products_key]['qty']) ? $_SESSION['orders_update_products'][$oID]['o_'.$new_products_key]['qty'] :$new_products_value['products_quantity']) * $shipping_fee_array['products_weight'];
      tep_db_free_result($shipping_fee_query);
    }
}
foreach($shipping_array as $shipping_value){

  $shipping_fee_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id=". $shipping_value['id']);
  $shipping_fee_array = tep_db_fetch_array($shipping_fee_query);
  $shipping_weight_total += $shipping_value['qty'] * $shipping_fee_array['products_weight'];
  tep_db_free_result($shipping_fee_query);
}

$weight = $shipping_weight_total;

$shipping_orders_array = array();
$shipping_address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."'");
while($shipping_address_orders_array = tep_db_fetch_array($shipping_address_orders_query)){

  $shipping_orders_array[$shipping_address_orders_array['name']] = $shipping_address_orders_array['value'];
}
tep_db_free_result($shipping_address_orders_query);

$country_fee_array = array();
$country_fee_id_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
while($country_fee_id_array = tep_db_fetch_array($country_fee_id_query)){

  $country_fee_array[$country_fee_id_array['fixed_option']] = $country_fee_id_array['name_flag'];
}
tep_db_free_result($country_fee_id_query);

foreach($shipping_orders_array  as $op_key=>$op_value){
  if($op_key == $country_fee_array[3]){
    $city_query = tep_db_query("select * from ". TABLE_COUNTRY_CITY ." where name='". $op_value ."' and status='0'");
    $city_num = tep_db_num_rows($city_query);
  }
 
  
  if($op_key == $country_fee_array[2]){
    $address_query = tep_db_query("select * from ". TABLE_COUNTRY_AREA ." where name='". $op_value ."' and status='0'");
    $address_num = tep_db_num_rows($address_query);
  }

   
  if($op_key == $country_fee_array[1]){
    $country_query = tep_db_query("select * from ". TABLE_COUNTRY_FEE ." where name='". $op_value ."' and status='0'");
    $address_country_num = tep_db_num_rows($country_query);
  }

if($city_num > 0 && $op_key == $country_fee_array[3]){
  $city_array = tep_db_fetch_array($city_query);
  tep_db_free_result($city_query);
  $city_free_value = $city_array['free_value'];
  $city_weight_fee_array = unserialize($city_array['weight_fee']);

  //根据重量来获取相应的配送费用
  foreach($city_weight_fee_array as $key=>$value){
    
    if(strpos($key,'-') > 0){

      $temp_array = explode('-',$key);
      $city_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
    }else{
  
      $city_weight_fee = $weight <= $key ? $value : 0;
    }

    if($city_weight_fee > 0){

      break;
    }
  }
}elseif($address_num > 0 && $op_key == $country_fee_array[2]){
  $address_array = tep_db_fetch_array($address_query);
  tep_db_free_result($address_query);
  $address_free_value = $address_array['free_value'];
  $address_weight_fee_array = unserialize($address_array['weight_fee']);

  //根据重量来获取相应的配送费用
  foreach($address_weight_fee_array as $key=>$value){
    
    if(strpos($key,'-') > 0){

      $temp_array = explode('-',$key);
      $address_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
    }else{
  
      $address_weight_fee = $weight <= $key ? $value : 0;
    }

    if($address_weight_fee > 0){

      break;
    }
  }
}else{
  if($address_country_num > 0 && $op_key == $country_fee_array[1]){
  $country_array = tep_db_fetch_array($country_query);
  tep_db_free_result($country_query);
  $country_free_value = $country_array['free_value'];
  $country_weight_fee_array = unserialize($country_array['weight_fee']);

  //根据重量来获取相应的配送费用
  foreach($country_weight_fee_array as $key=>$value){
    
    if(strpos($key,'-') > 0){

      $temp_array = explode('-',$key);
      $country_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
    }else{
  
      $country_weight_fee = $weight <= $key ? $value : 0;
    }

    if($country_weight_fee > 0){

      break;
    }
  }
  }
}

}

$shipping_money_total = $order->totals[0]['value'];
if($city_weight_fee != ''){
  $weight_fee = $city_weight_fee;
}else{
  $weight_fee = $address_weight_fee != '' ? $address_weight_fee : $country_weight_fee;
}
if($city_free_value != ''){

  $free_value = $city_free_value;
}else{
  $free_value = $address_free_value != '' ? $address_free_value : $country_free_value;
}

$orders_total_num = 0;
foreach($order->totals as $total_key=>$total_value){
        
   if($total_value['class'] == 'ot_total'){

      $orders_total_num = (int)$total_value['value'];
   }
}

$shipping_fee = $orders_total_num+$new_products_total-$order->info['code_fee']-$order->info['shipping_fee'] > $free_value ? 0 : $weight_fee;

$shipping_fee = $order->info['shipping_fee'] != $shipping_fee ? $shipping_fee : $order->info['shipping_fee'];


if(isset($_SESSION['error_edit_orders_status'])&&$_SESSION['error_edit_orders_status']){
  $messageStack->add($_SESSION['error_edit_orders_status'], 'error');
  unset($_SESSION['error_edit_orders_status']);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/styles.css">
<link rel="stylesheet" type="text/css" href="css/popup_window.css">
<script language="javascript">
var session_orders_id = '<?php echo $_GET['oID'];?>';
var session_site_id = '<?php echo $order->info['site_id'];?>';
</script>
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=all_order&type=js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=all_orders&type=js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" src="includes/3.4.1/build/yui/yui.js"></script>
<script language="javascript" src="includes/jquery.form.js"></script>
<script language="javascript" src="js2php.php?path=js&name=popup_window&type=js"></script>
<script language="javascript">
<?php //检查配送时间是否正确?>
function date_time(){
    var fetch_year = document.getElementById('fetch_year').value; 
    var fetch_month = document.getElementById('fetch_month').value;
    var fetch_day = document.getElementById('fetch_day').value;
    var date_time = parseInt('<?php echo date('Ymd');?>');
    var date_hour = parseInt('<?php echo date('Hi');?>');
    var date_time_value = parseInt(fetch_year+fetch_month+fetch_day);
    var start_hour = document.getElementById('hour').value;
    var start_min = document.getElementById('min').value;
    var end_min = document.getElementById('min_1').value;
    var start_hour_str = parseInt(start_hour+start_min+end_min);
    if(date_time_value < date_time || (date_time_value == date_time && start_hour_str < date_hour)){
      if(confirm('<?php echo TEXT_DATE_TIME_ERROR;?>')){
        return true;
      }else{
        return false; 
      }
    }
    return true;
}
<?php //检查订单商品的数量是否正确?>
function products_num_check(orders_products_list_id,products_name,products_list_id){

    var _end = $("#mail_title_status").val();
    if($("#confrim_mail_title_"+_end).val()==$("#mail_title").val()){
    }else{
      if(confirm("<?php echo TEXT_STATUS_MAIL_TITLE_CHANGED;?>")){
      }else{
        return false;
      }
    }
    var products_error = true;
    var products_array = new Array();
    products_array = orders_products_list_id.split('|||');
    var products_list_str = '';
    var products_temp;
    for(var x in products_array){
      products_temp = $("#update_products_new_qty_"+products_array[x]).val(); 
      products_list_str += products_temp+'|||';
    }
    $.ajax({
    type: "POST",
    data: 'products_list_id='+products_list_id+'&products_list_str='+products_list_str+'&products_name='+products_name+'&orders_products_list_id='+orders_products_list_id+'&products_diff=1',
    async:false,
    url: 'ajax_orders.php?action=products_num',
    success: function(msg) {
      if(msg != ''){

        if(confirm(msg+"\n\n<?php echo TEXT_PRODUCTS_NUM;?>")){

          products_error = true;
        }else{
          products_error = false;
        }
      }else{  
        products_error = true;
      }         
    }
    }); 
    return products_error;
}
<?php //检查订单商品的重量是否超重?>
function submit_check_con(){

    var options = {
      url: 'ajax_orders_weight.php?action=edit_orders&oID=<?php echo $_GET['oID'];?>',
    type:  'POST',
    success: function(data) {
      if(data != ''){
        if(confirm(data)){

          submitChk('<?php echo $ocertify->npermission;?>'); 
        }
      }else{

        submitChk('<?php echo $ocertify->npermission;?>'); 
      } 
    }
  };
  $('#edit_order_id').ajaxSubmit(options);
}
<?php //加减符号?>
function sign(num){

  var sign = '<select id="sign_'+num+'" name="sign_value_'+num+'" onchange="price_total(\'<?php echo TEXT_MONEY_SYMBOL;?>\');orders_session(\'sign_'+num+'\',this.value);">';
  sign += '<option value="1">+</option>';
  sign += '<option value="0">-</option>';
  sign += '</select>';
  return sign;
}
<?php //添加输入框?>
function add_option(ele){
    var add_num = $("#button_add_id").val();
    add_num = parseInt(add_num);
    orders_session('orders_totals',add_num+1);
    $("#button_add_id").val(add_num+1);
    add_num++; 
    var add_str = '';

    add_str += '<tr><td class="smallText" align="left">&nbsp;</td>'
      +'<td class="smallText" align="right" style="min-width:188px;"><input style="text-align:right;" value="" size="'+$("#text_len").val()+'" name="update_totals['+add_num+'][title]" onkeyup="price_total(\'<?php echo TEXT_MONEY_SYMBOL;?>\');">:'
            +'</td><td class="smallText" align="right">'+sign(add_num)+'<input style="text-align:right;" id="update_total_'+add_num+'" value="" size="6" onkeyup="clearNewLibNum(this);price_total(\'<?php echo TEXT_MONEY_SYMBOL;?>\');" name="update_totals['+add_num+'][value]"><input type="hidden" name="update_totals['+add_num+'][class]" value="ot_custom"><input type="hidden" name="update_totals['+add_num+'][total_id]" value="0"><?php echo TEXT_MONEY_SYMBOL;?></td>'
            +'<td><b><img height="17" width="1" border="0" alt="" src="images/pixel_trans.gif"></b></td></tr>';

    $("#point_id").parent().parent().before(add_str);
  }
<?php
if($weight > 0){
  $address_fixed_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
  while($address_fixed_array = tep_db_fetch_array($address_fixed_query)){

    switch($address_fixed_array['fixed_option']){
/* -----------------------------------------------------
   case '1' 国家id的html值    
   case '2' 区域id的html值   
   case '3' 城市id的html值   
------------------------------------------------------*/
    case '1':
      echo 'var country_fee_id = "ad_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_fee_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_fee_id = 'ad_'.$address_fixed_array['name_flag'];
      break;
    case '2':
      echo 'var country_area_id = "ad_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_area_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_area_id = 'ad_'.$address_fixed_array['name_flag'];
      break;
    case '3':
      echo 'var country_city_id = "ad_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_city_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_city_id = 'ad_'.$address_fixed_array['name_flag'];
      break;
    }
  }
?>
<?php //生成所配送的国家列表?>
function check(select_value){

  $("#td_"+country_fee_id_one).hide();
  $("#td_"+country_area_id_one).hide();
  $("#td_"+country_city_id_one).hide();
  var arr = new Array();
  <?php  
    $country_fee_query = tep_db_query("select id,name from ". TABLE_COUNTRY_FEE ." where status='0' order by id");
    while($country_fee_array = tep_db_fetch_array($country_fee_query)){

      echo 'arr["'.$country_fee_array['name'].'"] = "'. $country_fee_array['name'] .'";'."\n";
    }
    tep_db_free_result($country_fee_query);
   ?>
  if(document.getElementById(country_fee_id)){
    var country_fee = document.getElementById(country_fee_id);
    country_fee.options.length = 0;
    var i = 0;
    for(x in arr){

      country_fee.options[country_fee.options.length]=new Option(arr[x], x,x==select_value,x==select_value);
      i++;
    }

    if(i ==  0){

      $("#td_"+country_fee_id_one).hide();
    }else{

      $("#td_"+country_fee_id_one).show();
    } 
  }
}
<?php //生成所配送的区域列表?>
function country_check(value,select_value){
   
   var arr = new Array();
  <?php 
    $country_array = array();
    $country_area_query = tep_db_query("select id,fid,name from ". TABLE_COUNTRY_AREA ." where status='0' order by sort");
    while($country_area_array = tep_db_fetch_array($country_area_query)){
      
      $country_fee_fid_query = tep_db_query("select name from ". TABLE_COUNTRY_FEE ." where id='".$country_area_array['fid']."'"); 
      $country_fee_fid_array = tep_db_fetch_array($country_fee_fid_query);
      tep_db_free_result($country_fee_fid_query);
      $country_array[$country_fee_fid_array['name']][$country_area_array['name']] = $country_area_array['name'];
      
    }
    tep_db_free_result($country_area_query);
    foreach($country_array as $country_key=>$country_value){
      
      echo 'arr["'.$country_key.'"] = new Array();'."\n";
      foreach($country_value as $c_key=>$c_value){
      
        echo 'arr["'.$country_key.'"]["'.$c_key.'"] = "'. $c_value .'";'."\n";

      }

    }
  ?>
  if(document.getElementById(country_area_id)){ 
    var country_area = document.getElementById(country_area_id);
    country_area.options.length = 0;
    var i = 0;
    for(x in arr[value]){

      country_area.options[country_area.options.length]=new Option(arr[value][x], x,x==select_value,x==select_value);
      i++;
    }

    if(i ==  0){

      $("#td_"+country_area_id_one).hide();
    }else{

      $("#td_"+country_area_id_one).show();
    }
  }

}
<?php //生成所配送的城市列表?>
function country_area_check(value,select_value){
   
   var arr = new Array();
  <?php
    $country_array = array();
    $country_city_query = tep_db_query("select id,fid,name from ". TABLE_COUNTRY_CITY ." where status='0' order by sort");
    while($country_city_array = tep_db_fetch_array($country_city_query)){
      
      $country_area_fid_query = tep_db_query("select name from ". TABLE_COUNTRY_AREA ." where id='".$country_city_array['fid']."'"); 
      $country_area_fid_array = tep_db_fetch_array($country_area_fid_query);
      tep_db_free_result($country_area_fid_query); 
      $country_array[$country_area_fid_array['name']][$country_city_array['name']] = $country_city_array['name'];
      
    }
    tep_db_free_result($country_city_query);
    foreach($country_array as $country_key=>$country_value){
      
      echo 'arr["'.$country_key.'"] = new Array();'."\n";
      foreach($country_value as $c_key=>$c_value){
      
        echo 'arr["'.$country_key.'"]["'.$c_key.'"] = "'. $c_value .'";'."\n";

      }

    }
  ?>
  if(document.getElementById(country_city_id)){
    var country_city = document.getElementById(country_city_id);
    country_city.options.length = 0;
    var i = 0;
    for(x in arr[value]){

      country_city.options[country_city.options.length]=new Option(arr[value][x], x,x==select_value,x==select_value);
      i++;
    }

    if(i ==  0){

      $("#td_"+country_city_id_one).hide();
    }else{
      
      $("#td_"+country_city_id_one).show();
    }
  }

}
<?php
}
?>
<?php //生成配送开始时间的小时列表?>
function check_hour(value){
  var hour_1 = document.getElementById('hour_1');
  var hour_1_value = hour_1.value;
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value;
  var min = document.getElementById('min');
  var min_value = min.value;
  var min_end_1 = document.getElementById('min_end_1');
  var min_end_1_value = min_end_1.value;
  var min_end = document.getElementById('min_end');
  var min_end_value = min_end.value;


  if(parseInt(value) >= parseInt(hour_1.value)){ 
    hour_1.options.length = 0;
    value = parseInt(value);
    for(h_i = value;h_i <= 23;h_i++){
      h_i_str = h_i < 10 ? '0'+h_i : h_i;
      hour_1.options[hour_1.options.length]=new Option(h_i_str,h_i_str,h_i_str==value); 
    }

    if(parseInt(min_end_value) < parseInt(min_value)){
      min_end.options.length = 0;
      min_value = parseInt(min_value);
      for(m_i = min_value;m_i <= 5;m_i++){
        min_end.options[min_end.options.length]=new Option(m_i,m_i,m_i==min_value); 
      }
    }

    if(parseInt(min_end_1_value) < parseInt(min_1_value)){
      min_end_1.options.length = 0;
      min_1_value = parseInt(min_1_value);
      for(m_i_1 = min_1_value;m_i_1 <= 9;m_i_1++){
        min_end_1.options[min_end_1.options.length]=new Option(m_i_1,m_i_1,m_i_1==min_1_value); 
      }
    }
  }else{

    hour_1.options.length = 0;
    value = parseInt(value);
    for(h_i = value;h_i <= 23;h_i++){
      h_i_str = h_i < 10 ? '0'+h_i : h_i;
      hour_1.options[hour_1.options.length]=new Option(h_i_str,h_i_str,h_i_str==hour_1_value); 
    }
    min_end.options.length = 0;
    min_value = parseInt(min_value);
    for(m_i = 0;m_i <= 5;m_i++){
      min_end.options[min_end.options.length]=new Option(m_i,m_i,m_i==min_end_value); 
    }

    min_end_1.options.length = 0;
    min_1_value = parseInt(min_1_value);
    for(m_i_1 = 0;m_i_1 <= 9;m_i_1++){
      min_end_1.options[min_end_1.options.length]=new Option(m_i_1,m_i_1,m_i_1==min_end_1_value); 
    } 
  }
}
<?php //生成配送开始时间的分钟十位列表?>
function check_min(value){
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value;
  var min_end = document.getElementById('min_end');
  var min_end_value = min_end.value;
  var min_end_1 = document.getElementById('min_end_1');
  var min_end_1_value = min_end_1.value;
  var hour_1 = document.getElementById('hour_1');
  var hour_1_value = hour_1.value;
  var hour = document.getElementById('hour');
  var hour_value = hour.value;
   
  if(parseInt(value) >= parseInt(min_end_value) && parseInt(hour.value) >= parseInt(hour_1.value)){ 
    min_end.options.length = 0;
    value = parseInt(value);
    for(mi_i = value;mi_i <= 5;mi_i++){
      min_end.options[min_end.options.length]=new Option(mi_i,mi_i,mi_i==value); 
    }
    min_end_1.options.length = 0;
    for(mi_i_end = min_1_value;mi_i_end <= 9;mi_i_end++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i_end,mi_i_end,mi_i_end==min_end_1_value); 
    }
  }else if(parseInt(value) <  parseInt(min_end_value) && parseInt(hour.value) >= parseInt(hour_1.value)){
   min_end.options.length = 0;
    value = parseInt(value);
    for(mi_i = value;mi_i <= 5;mi_i++){
      min_end.options[min_end.options.length]=new Option(mi_i,mi_i,mi_i==min_end_value); 
    }
    min_end_1.options.length = 0;
    for(mi_i_end = 0;mi_i_end <= 9;mi_i_end++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i_end,mi_i_end,mi_i_end==min_end_1_value); 
    }
  }
}
<?php //生成配送开始时间的分钟个位列表?>
function check_min_1(value){
  var min = document.getElementById('min');
  var min_value = min.value;
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value;
  var min_end = document.getElementById('min_end');
  var min_end_value = min_end.value;
  var min_end_1 = document.getElementById('min_end_1');
  var min_end_1_value = min_end_1.value;
  var hour_1 = document.getElementById('hour_1');
  var hour_1_value = hour_1.value;
  var hour = document.getElementById('hour');
  var hour_value = hour.value;
   
  if(parseInt(value) >= parseInt(min_end_1_value) && parseInt(hour.value) >= parseInt(hour_1.value) && parseInt(min.value) >= parseInt(min_end.value)){ 
    min_end_1.options.length = 0;
    value = parseInt(value);
    for(mi_i = value;mi_i <= 9;mi_i++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==value); 
    }
  }else if(parseInt(value) < parseInt(min_end_1_value) && parseInt(hour.value) >= parseInt(hour_1.value) && parseInt(min.value) >= parseInt(min_end.value)){
   min_end_1.options.length = 0;
    value = parseInt(value);
    for(mi_i = value;mi_i <= 9;mi_i++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==min_end_1_value); 
    }

  }
}
<?php //生成配送结束时间的小时列表?>
function check_hour_1(value){
  var min = document.getElementById('min');
  var min_value = min.value;
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value;
  var min_end = document.getElementById('min_end');
  var min_end_value = min_end.value;
  var min_end_1 = document.getElementById('min_end_1');
  var min_end_1_value = min_end_1.value;
  var hour = document.getElementById('hour');
  var hour_value = hour.value;

  
  if(hour_value == value){ 
    min_end.options.length = 0;
    min_value = parseInt(min_value);
    for(mi_i = min_value;mi_i <= 5;mi_i++){
      min_end.options[min_end.options.length]=new Option(mi_i,mi_i,mi_i==min_1_value); 
    }
    if(min_end_value <= min_value ){
      min_end_1.options.length = 0;
      min_1_value = parseInt(min_1_value);
      for(mi_i = min_1_value;mi_i <= 9;mi_i++){
        min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==min_end_1_value); 
      }
    }else{
      min_end_1.options.length = 0;
      min_1_value = parseInt(min_1_value);
      for(mi_i = 0;mi_i <= 9;mi_i++){
        min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==min_end_1_value); 
      } 
    }
  }else{

    min_end.options.length = 0;
    min_value = parseInt(min_value);
    for(mi_i = 0;mi_i <= 5;mi_i++){
      min_end.options[min_end.options.length]=new Option(mi_i,mi_i,mi_i==min_1_value); 
    }
    min_end_1.options.length = 0;
    min_1_value = parseInt(min_1_value);
    for(mi_i = 0;mi_i <= 9;mi_i++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==min_end_1_value); 
    }
    
  }
}
<?php //生成配送结束时间的小时十位列表?>
function check_end_min(value){
  var min = document.getElementById('min');
  var min_value = min.value;
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value; 
  var min_end_1 = document.getElementById('min_end_1');
  var min_end_1_value = min_end_1.value;
  var hour = document.getElementById('hour');
  var hour_value = hour.value;
  var hour_1 = document.getElementById('hour_1');
  var hour_1_value = hour_1.value;
  
  if(parseInt(value) == parseInt(min_value) && parseInt(hour.value) == parseInt(hour_1.value)){ 
    min_end_1.options.length = 0;
    min_1_value = parseInt(min_1_value);
    for(mi_i = min_1_value;mi_i <= 9;mi_i++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==min_end_1_value); 
    }
  }else{
    min_end_1.options.length = 0;
    for(mi_i = 0;mi_i <= 9;mi_i++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==min_end_1_value); 
    }    
  }
}


<?php
if($weight > 0){
?>
<?php //清除地址错误?>
function address_clear_error(){
  
  var list_error = new Array();
  <?php 
    $error_i = 0; 
    $address_error_query = tep_db_query("select name_flag from ". TABLE_ADDRESS ." where status='0'");
    while($address_error_array = tep_db_fetch_array($address_error_query)){
     
      echo 'list_error['. $error_i .'] = "'. $address_error_array['name_flag'] .'";';
      $error_i++;
    }
    tep_db_free_result($address_error_query);
   ?>
   
    for(x in list_error){
      
      $("#error_"+list_error[x]).html("");
    }

}
<?php //判断该值是否在数组里?>
function in_array(value,arr){

  for(vx in arr){
    if(value == arr[vx]){

      return true;
    } 
  }
  return false;
}
// end in_array
var first_num = 0;
//地址属性显示
function address_option_show(action){
  switch(action){

  case 'new' :
    arr_new = new Array();
    arr_color = new Array();
    $("#address_list_id").hide();
    check();
    country_check($('#'+country_fee_id).val());
    country_area_check($('#'+country_area_id).val());
    
<?php 
  $address_new_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type!='text' and status='0' order by sort");
  while($address_new_array = tep_db_fetch_array($address_new_query)){
    $address_new_arr = unserialize($address_new_array['type_comment']);
    if($address_new_array['type'] == 'textarea'){
      if($address_new_arr['set_value'] != ''){
        echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_arr['set_value'] .'";';
        echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#000";';
      }else{
        echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_array['comment'] .'";';
        echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#999";';
      }
    }elseif($address_new_array['type'] == 'option' && $address_new_arr['select_value'] !=''){
      echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "'. $address_new_arr['select_value'] .'";';
      echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#000";';
    }else{

      echo 'arr_new["'. $address_new_array['name_flag'] .'"] = "";';
      echo 'arr_color["'. $address_new_array['name_flag'] .'"] = "#000";';


    }
  }
  tep_db_free_result($address_new_query);
?>
  for(x in arr_new){
    if(document.getElementById("ad_"+x)){ 
      var list_options = document.getElementById("ad_"+x);
      list_options.value = arr_new[x];
      list_options.style.color = arr_color[x];
      $("#error_"+x).html('');
      <?php
      if(!isset($_POST['address_option']) || $_POST['address_option'] == 'old'){
      ?>
        if(document.getElementById("l_"+x)){
          if($("#l_"+x).val() == 'true'){
            $("#r_"+x).html("&nbsp;<?php echo TEXT_REQUIRE;?>");
          }
        }
      <?php
      }
      ?>
    }
    }
    break;
  case 'old' :
    $("#address_list_id").show();
    var arr_old  = new Array();
    var arr_name = new Array();
<?php

  //根据后台的设置来显示相应的地址列表
  $address_list_arr = array();
  $address_i = 0;
  $address_list_query = tep_db_query("select name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_list_array = tep_db_fetch_array($address_list_query)){

    $address_list_arr[] = $address_list_array['name_flag'];
    echo 'arr_name['. $address_i .'] = "'. $address_list_array['name_flag'] .'";';
    $address_i++;
  }
  tep_db_free_result($address_list_query);
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id=". $order->customer['id'] ." group by orders_id order by orders_id desc");
  
   
  $address_num = 0;
  $json_str_array = array();
  $json_old_array = array();

  while($address_orders_group_array = tep_db_fetch_array($address_orders_group_query)){
  
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where orders_id='". $address_orders_group_array['orders_id'] ."' order by id asc");

   
  $json_str_list = '';
  unset($json_old_array);
  while($address_orders_array = tep_db_fetch_array($address_orders_query)){
    
    if(in_array($address_orders_array['name'],$address_list_arr)){

      $json_str_list .= $address_orders_array['value'];
    }
    
    $json_old_array[$address_orders_array['name']] = $address_orders_array['value'];
        
  }

  
  //这里判断，如果有重复的记录只显示一个
  if(!in_array($json_str_list,$json_str_array)){
      
      $json_str_array[$address_num] = $json_str_list; 
      echo 'arr_old['. $address_num .'] = new Array();';
      foreach($json_old_array as $key=>$value){
        
        $value = str_replace("\n","",$value); 
        $value = str_replace("\r","",$value); 
        echo 'arr_old['. $address_num .']["'. $key .'"] = "'. $value .'";';
      }
      $address_num++;
  }
 
  tep_db_free_result($address_orders_query); 
  }

  echo "\n".'var address_str = "";'."\n";
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' order by id");
  while($address_orders_array = tep_db_fetch_array($address_orders_query)){
  
    if(in_array($address_orders_array['name'],$address_list_arr)){

      echo "\n".'address_str += "'. $address_orders_array['value'] .'";'."\n";
    }
  }
  tep_db_free_result($address_orders_query);
?>
  var address_show_list = document.getElementById("address_show_list");

  if(document.getElementById("address_show_list")){
    address_show_list.options.length = 0;
  }

  len = arr_old.length;
  j_num = 0;
  for(i = 0;i < len;i++){
    arr_str = '';
    for(x in arr_old[i]){
        if(in_array(x,arr_name)){
          arr_str += arr_old[i][x];
        }
        <?php
        if(!isset($_POST['address_option']) || $_POST['address_option'] == 'new'){ 
        ?>
        if(document.getElementById("l_"+x)){
          if($("#l_"+x).val() == 'true'){
            $("#r_"+x).html("&nbsp;<?php echo TEXT_REQUIRE;?>");
          }
        }
        <?php
        }
        ?>
    }
  if(document.getElementById("address_show_list")){
    if(arr_str != ''){
      ++j_num;
      if(j_num == 1){first_num = i;}

      if('<?php echo $_POST['address_show_list'];?>' != ''){
        address_show_list.options[address_show_list.options.length]=new Option(arr_str,i,i=='<?php echo $_POST['address_show_list'];?>',i=='<?php echo $_POST['address_show_list'];?>');
      }else{
        if(arr_str == address_str){
          address_show_list.options[address_show_list.options.length]=new Option(arr_str,i,true,true);
        }else{
          address_show_list.options[address_show_list.options.length]=new Option(arr_str,i);
        }
      }
    }
  }

  }
    break;
  }
}
<?php //地址属性列表?>
function address_option_list(value){
  var arr_list = new Array();
<?php
  //根据后台的设置来显示相应的地址列表
  $address_list_arr = array();
  $address_list_query = tep_db_query("select name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_list_array = tep_db_fetch_array($address_list_query)){

    $address_list_arr[] = $address_list_array['name_flag'];
  }
  tep_db_free_result($address_list_query);
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id=". $order->customer['id'] ." group by orders_id order by orders_id desc");
  
   
  $address_num = 0;
  $json_str_list = '';
  $json_str_array = array();
  
  while($address_orders_group_array = tep_db_fetch_array($address_orders_group_query)){
  
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where orders_id='". $address_orders_group_array['orders_id'] ."' order by id");
  
  while($address_orders_array = tep_db_fetch_array($address_orders_query)){
    
    if(in_array($address_orders_array['name'],$address_list_arr)){

      $json_str_list .= $address_orders_array['value'];
    }
    
    $json_old_array[$address_orders_array['name']] = $address_orders_array['value'];
        
  }

  
  //这里判断，如果有重复的记录只显示一个
  if(!in_array($json_str_list,$json_str_array)){
      
      $json_str_array[] = $json_str_list; 
      echo 'arr_list['. $address_num .'] = new Array();';
      foreach($json_old_array as $key=>$value){
        
        $value = str_replace("\n","",$value); 
        $value = str_replace("\r","",$value); 
        echo 'arr_list['. $address_num .']["'. $key .'"] = "'. $value .'";';
      }
      $address_num++;
    }
    $json_str_list = '';
 
  tep_db_free_result($address_orders_query); 
  }
?>
  ii = 0;
  for(x in arr_list[value]){
   if(document.getElementById("ad_"+x)){
     var list_option = document.getElementById("ad_"+x);
     if('<?php echo $country_fee_id;?>' == 'ad_'+x){
      check(arr_list[value][x]);
    }else if('<?php echo $country_area_id;?>' == 'ad_'+x){
      country_check(document.getElementById(country_fee_id).value,arr_list[value][x]);
     
    }else if('<?php echo $country_city_id;?>' == 'ad_'+x){
      country_area_check(document.getElementById(country_area_id).value,arr_list[value][x]);
    }else{
      list_option.style.color = '#000';
      list_option.value = arr_list[value][x];   
    }
     
    $("#error_"+x).html('');
    $("#r_"+x).html("&nbsp;<?php echo TEXT_REQUIRE;?>");
    ii++; 
   }
  }

}

<?php 
}
$suu = 0;
$text_suu = 0;  
$__orders_status_query = tep_db_query("
    select orders_status_id 
    from " . TABLE_ORDERS_STATUS . " 
    where language_id = " . $languages_id . " 
    order by orders_status_id");
$__orders_status_ids   = array();
while($__orders_status = tep_db_fetch_array($__orders_status_query)){
  $__orders_status_ids[] = $__orders_status['orders_status_id'];
}
$select_query = tep_db_query("
    select om.orders_status_mail,
    om.orders_status_title,
    os.orders_status_id,
    os.nomail,
    om.site_id
    from ".TABLE_ORDERS_STATUS." os left join ".TABLE_ORDERS_MAIL." om on os.orders_status_id = om.orders_status_id
    where os.language_id = " . $languages_id . " 
    and os.orders_status_id IN (".join(',', $__orders_status_ids).")");

while($select_result = tep_db_fetch_array($select_query)){
  if($suu == 0){
    $select_select = $select_result['orders_status_id'];
    $suu = 1;
  }

  $osid = $select_result['orders_status_id'];

  if($text_suu == 0){
    $select_text = $select_result['orders_status_mail'];
    $select_title = $select_result['orders_status_title'];
    $text_suu = 1;
    $select_nomail = $select_result['nomail'];
  }

  $mt[$osid][$select_result['site_id']?$select_result['site_id']:0] = $select_result['orders_status_mail'];
  $mo[$osid][$select_result['site_id']?$select_result['site_id']:0] = $select_result['orders_status_title'];
  $nomail[$osid] = $select_result['nomail'];
}


        // 输出订单邮件
        // title
        foreach ($mo as $oskey => $value){
          echo 'window.status_title['.$oskey.'] = new Array();'."\n";
          foreach ($value as $sitekey => $svalue) {
            echo 'window.status_title['.$oskey.']['.$sitekey.'] = "' . str_replace(array("\r\n","\r","\n"), array('\n', '\n', '\n'),$svalue) . '";' . "\n";
          }
        }

//content
foreach ($mt as $oskey => $value){
  echo 'window.status_text['.$oskey.'] = new Array();'."\n";
  foreach ($value as $sitekey => $svalue) {
    echo 'window.status_text['.$oskey.']['.$sitekey.'] = "' . str_replace(array("\r\n","\r","\n"), array('\n', '\n', '\n'),$svalue) . '";' . "\n";
  }
}

//no mail
echo 'var nomail = new Array();'."\n";
foreach ($nomail as $oskey => $value){
  echo 'nomail['.$oskey.'] = "' . $value . '";' . "\n";
}

if($weight > 0){
?>
<?php //切换地址显示?>
function address_show(){
  
  var style = $("#address_show_id").css("display");
  if(style == 'none'){
    $("#address_show_id").show(); 
    $("#address_font").html("<?php echo TEXT_ADDRESS_INFO_HIDE;?>");
 
  }else{

    $("#address_show_id").hide();
    $("#address_font").html("<?php echo TEXT_ADDRESS_INFO_SHOW;?>");
  }
}
<?php //地址列表?>
function address_list(){

  var arr_list = new Array();
<?php
  $address_list_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' order by id");
  while($address_list_array = tep_db_fetch_array($address_list_query)){
 
    echo 'arr_list["'. $address_list_array['name'] .'"] = "'. $address_list_array['value'] .'";';

  }
  tep_db_free_result($address_list_query);
?>
  for(x in arr_list){
   if(document.getElementById("ad_"+x)){ 
     var op_list = document.getElementById("ad_"+x);
     if('<?php echo $country_fee_id;?>' == 'ad_'+x){
      check(arr_list[x]);
    }else if('<?php echo $country_area_id;?>' == 'ad_'+x){
      country_check(document.getElementById(country_fee_id).value,arr_list[x]);
     
    }else if('<?php echo $country_city_id;?>' == 'ad_'+x){
      country_area_check(document.getElementById(country_area_id).value,arr_list[x]);
    }else{
      op_list.style.color = '#000';
      $("#ad_"+x).val(arr_list[x]);
    }
    
   }
  }
}

<?php
  if($shipping_weight_total > 0){
?>
$(document).ready(function(){            
  <?php
  if(!($_POST['address_option'] == 'new')){
  ?>
  address_option_show('old');
  <?php
  }
  if(!isset($_POST['address_option'])){
  ?> 
  address_list();
  address_clear_error();
  <?php
  }
  ?>
});
<?php
  }
?>

<?php
  if(isset($_POST['address_option']) && $_POST['address_option'] == 'new'){
?>
$(document).ready(function(){            
  $("#address_list_id").hide();
});
<?php
  }
?>

<?php
  if(isset($_POST['address_option']) && $_POST['address_option'] == 'old'){
?>
$(document).ready(function(){            
  address_option_show('old');
});
<?php
  }
}
?>
//todo:修改通性用
<?php
      foreach($order->totals as $total_key=>$total_value){
        
        if($total_value['class'] == 'ot_total'){

          $orders_total_sum = (int)$total_value['value'];
        }
      }
      $cpayment = payment::getInstance($order->info['site_id']);
      $payment_array = payment::getPaymentList();
      $orders_total_sum -= $order->info['code_fee'];
      $orders_total_sum = isset($_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal']) ? $_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal']+$_SESSION['orders_update_products'][$_GET['oID']]['fee_total']-$_SESSION['orders_update_products'][$_GET['oID']]['point']+$shipping_fee : $orders_total_sum;
      foreach($payment_array[0] as $pay_key=>$pay_value){ 
        $payment_info = $cpayment->admin_get_payment_info_comment($pay_value,$order->customer['email_address'],$order->info['site_id']);
        if(is_array($payment_info)){

          switch($payment_info[0]){
          case 1: 
            $handle_fee_code = $cpayment->handle_calc_fee( payment::changeRomaji($pay_value,PAYMENT_RETURN_TYPE_CODE), $orders_total_sum);
            $pay_type_str = $pay_value;
            break;  
          }
        } 
      }
      $handle_fee_code = $handle_fee_code == '' ? 0 : $handle_fee_code;
?>
  <?php //隐藏支付方法的附加信息?> 
  function hidden_payment(num){
   if(document.edit_order){
     var idx = document.edit_order.elements["payment_method"].selectedIndex;
     var CI = 
     document.edit_order.elements["payment_method"].options[idx].value;
     $(".rowHide").hide();
     $(".rowHide").find("input").attr("disabled","true");
     $(".rowHide_"+CI).show();
     $(".rowHide_"+CI).find("input").removeAttr("disabled");
     if(CI == '<?php echo $pay_type_str;?>'){
       $("#handle_fee_id").html('<?php echo $handle_fee_code.TEXT_MONEY_SYMBOL;?>');
     }else{
       $("#handle_fee_id").html(0+'<?php echo TEXT_MONEY_SYMBOL;?>'); 
     }
     if(num == 0){
       price_total('<?php echo TEXT_MONEY_SYMBOL;?>');
     }
   }
  }
$(document).ready(function(){
  hidden_payment(1); 
  $("#fetch_year").change(function(){
    var date_value = document.getElementById("fetch_year").value;
    orders_session('fetch_year',date_value);
  });
  $("#fetch_month").change(function(){
    var date_value = document.getElementById("fetch_month").value;
    orders_session('fetch_month',date_value);
  });
  $("#fetch_day").change(function(){
    var date_value = document.getElementById("fetch_day").value;
    orders_session('fetch_day',date_value);
  });
  $("#hour").change(function(){
    var date_value = document.getElementById("hour").value;
    orders_session('hour',date_value);
    var date_value = document.getElementById("hour_1").value;
    orders_session('hour_1',date_value);
    var date_value = document.getElementById("min_end").value;
    orders_session('min_end',date_value);
    var date_value = document.getElementById("min_end_1").value;
    orders_session('min_end_1',date_value);
  });
  $("#min").change(function(){
    var date_value = document.getElementById("min").value;
    orders_session('min',date_value);
    var date_value = document.getElementById("min_end").value;
    orders_session('min_end',date_value);
    var date_value = document.getElementById("min_end_1").value;
    orders_session('min_end_1',date_value);
  });
  $("#min_1").change(function(){
    var date_value = document.getElementById("min_1").value;
    orders_session('min_1',date_value);
    var date_value = document.getElementById("min_end_1").value;
    orders_session('min_end_1',date_value);
  });
  $("#hour_1").change(function(){
    var date_value = document.getElementById("hour_1").value;
    orders_session('hour_1',date_value);
  });
  $("#min_end").change(function(){
    var date_value = document.getElementById("min_end").value;
    orders_session('min_end',date_value);
  });
  $("#min_end_1").change(function(){
    var date_value = document.getElementById("min_end_1").value;
    orders_session('min_end_1',date_value);
  }); 
  $("select[name='s_status']").change(function(){
    var s_status = document.getElementsByName("s_status")[0].value;
    orders_session('s_status',s_status);
    var title = document.getElementsByName("title")[0].value;
    orders_session('title',title);
    var comments = document.getElementsByName("comments")[0].value;
    orders_session('comments',comments);
  }); 
  $("input[name='title']").blur(function(){
    var title = document.getElementsByName("title")[0].value;
    orders_session('title',title);
  });
  $("textarea[name='comments']").blur(function(){
    var comments = document.getElementsByName("comments")[0].value;
    orders_session('comments',comments);
  });
  $("textarea[name='comments_text']").blur(function(){
    var comments_text = document.getElementsByName("comments_text")[0].value;
    orders_session('comments_text',comments_text);
  });
  $("input[name='notify']").click(function(){
    var notify = document.getElementsByName("notify")[0].checked;
    notify = notify == true ? 1 : 0;
    orders_session('notify',notify);
  });
  $("input[name='notify_comments']").click(function(){
    var notify_comments = document.getElementsByName("notify_comments")[0].checked;
    notify_comments = notify_comments == true ? 1 : 0;
    orders_session('notify_comments',notify_comments);
  });
  $("input[name='update_customer_name']").blur(function(){
    var update_customer_name = document.getElementsByName("update_customer_name")[0].value;
    orders_session('update_customer_name',update_customer_name);
  });
  $("input[name='update_customer_email_address']").blur(function(){
    var update_customer_email_address = document.getElementsByName("update_customer_email_address")[0].value;
    orders_session('update_customer_email_address',update_customer_email_address);
  }); 
});
$(document).ready(function(){
<?php
if($weight > 0){
?>
  $("#"+country_fee_id).change(function(){
    country_check($("#"+country_fee_id).val());
    country_area_check($("#"+country_area_id).val());
  }); 
  $("#"+country_area_id).change(function(){
    country_area_check($("#"+country_area_id).val());
  });
  <?php
    $address_name = array();
    $address_id_query = tep_db_query("select name,value from ". TABLE_ADDRESS_ORDERS ." where orders_id='". tep_db_input($oID) ."' and (name='". substr($country_fee_id,3) ."' or name='". substr($country_area_id,3) ."' or name='". substr($country_city_id,3) ."')");
    while($address_id_array = tep_db_fetch_array($address_id_query)){

      $address_name[$address_id_array['name']] = $address_id_array['value'];
    }
    tep_db_free_result($address_id_query);
    if(isset($_POST[$country_fee_id])){
  ?>  
    check("<?php echo isset($_POST[$country_fee_id]) ? $_POST[$country_fee_id] : '';?>");
  <?php
   }elseif(!empty($address_name)){
  ?>
    check("<?php echo $address_name[substr($country_fee_id,3)];?>");
  <?php
  }else{
  ?>
    check();
  <?php
  }
  ?>
  <?php
    if(isset($_POST[$country_area_id])){
  ?>
    country_check($("#"+country_fee_id).val(),"<?php echo $_POST[$country_area_id];?>");
  <?php
   }elseif(!empty($address_name)){
  ?>
    country_check($("#"+country_fee_id).val(),"<?php echo $address_name[substr($country_area_id,3)];?>");
  <?php
   }else{
  ?>
    country_check($("#"+country_fee_id).val());
  <?php
  }
  ?>
  <?php
    if(isset($_POST[$country_city_id])){
  ?>
     
     country_area_check($("#"+country_area_id).val(),"<?php echo $_POST[$country_city_id];?>");
  <?php
   }elseif(!empty($address_name)){
  ?>
    country_area_check($("#"+country_area_id).val(),"<?php echo $address_name[substr($country_city_id,3)];?>");
  <?php
   }else{
  ?>
    country_area_check($("#"+country_area_id).val());
  <?php
   }
}
  ?>   
  $("select[name='payment_method']").change(function(){
    hidden_payment(0);
  });

});
<?php //开启日历?>
function open_calendar()
{
  var is_open = $('#toggle_open').val(); 
  if (is_open == 0) {
    //mm-dd-yyyy || mm/dd/yyyy
    $('#toggle_open').val('1'); 
    
    var rules = {
           "all": {
                  "all": {
                           "all": {
                                      "all": "current_s_day",
                                }
                     }
            }};
    if ($("#date_orders").val() != '') {
      if ($("#date_orders").val() == '0000-00-00') {
        date_info_str = '<?php echo date('Y-m-d', time())?>';  
        date_info = date_info_str.split('-');  
      } else {
        date_info = $("#date_orders").val().split('-'); 
      }
    } else {
                      //mm-dd-yyyy || mm/dd/yyyy
      date_info_str = '<?php echo date('Y-m-d', time())?>';  
      date_info = date_info_str.split('-');  
    }
    new_date = new Date(date_info[0], date_info[1]-1, date_info[2]); 
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
            contentBox: "#mycalendar",
            width:'170px',
            date: new_date

        }).render();
      if (rules != '') {
       month_tmp = date_info[1].substr(0, 1);
       if (month_tmp == '0') {
         month_tmp = date_info[1].substr(1);
         month_tmp = month_tmp-1;
       } else {
         month_tmp = date_info[1]-1; 
       }
       day_tmp = date_info[2].substr(0, 1);
       
       if (day_tmp == '0') {
         day_tmp = date_info[2].substr(1);
       } else {
         day_tmp = date_info[2];   
       }
       data_tmp_str = date_info[0]+'-'+month_tmp+'-'+day_tmp;
       
       calendar.set("customRenderer", {
            rules: rules,
               filterFunction: function (date, node, rules) {
                 cmp_tmp_str = date.getFullYear()+'-'+date.getMonth()+'-'+date.getDate();
                 if (cmp_tmp_str == data_tmp_str) {
                   node.addClass("redtext"); 
                 }
               }
       });
     }
      var dtdate = Y.DataType.Date;
      calendar.on("selectionChange", function (ev) {
        var newDate = ev.newSelection[0];
        tmp_show_date = dtdate.format(newDate); 
        tmp_show_date_array = tmp_show_date.split('-');
        $("#fetch_year").val(tmp_show_date_array[0]); 
        $("#fetch_month").val(tmp_show_date_array[1]); 
        $("#fetch_day").val(tmp_show_date_array[2]); 
        $("#date_orders").val(tmp_show_date); 
        $('#toggle_open').val('0');
        $('#toggle_open').next().html('<div id="mycalendar"></div>');
      });
    });
  }
}
<?php //判断日期是否正确?>
function is_date(dateval)
{
  var arr = new Array();
  if(dateval.indexOf("-") != -1){
    arr = dateval.toString().split("-");
  }else if(dateval.indexOf("/") != -1){
    arr = dateval.toString().split("/");
  }else{
    return false;
  }
  if(arr[0].length==4){
    var date = new Date(arr[0],arr[1]-1,arr[2]);
    if(date.getFullYear()==arr[0] && date.getMonth()==arr[1]-1 && date.getDate()==arr[2]) {
      return true;
    }
  }
  
  if(arr[2].length==4){
    var date = new Date(arr[2],arr[1]-1,arr[0]);
    if(date.getFullYear()==arr[2] && date.getMonth()==arr[1]-1 && date.getDate()==arr[0]) {
      return true;
    }
  }
  
  if(arr[2].length==4){
    var date = new Date(arr[2],arr[0]-1,arr[1]);
    if(date.getFullYear()==arr[2] && date.getMonth()==arr[0]-1 && date.getDate()==arr[1]) {
      return true;
    }
  }
 
  return false;
}
<?php //把配送时间的日期复制到隐藏域中?>
function change_fetch_date() {
  fetch_date_str = $("#fetch_year").val()+"-"+$("#fetch_month").val()+"-"+$("#fetch_day").val(); 
  if (!is_date(fetch_date_str)) {
    alert('<?php echo ERROR_INPUT_RIGHT_DATE;?>'); 
  } else {
    $("#date_orders").val(fetch_date_str); 
  }
}
</script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/oID=[^&]+/',$belong,$belong_array);
if($belong_array[0][0] != ''){

  $belong = $href_url.'?'.$belong_array[0][0];
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
    <?php }?>
    <!-- header //-->
    <?php
    require(DIR_WS_INCLUDES . 'header.php');
    ?>
    <style type="text/css">
.yui3-skin-sam .redtext {
    color:#0066CC;
}
    .Subtitle {
      font-family: Verdana, Arial, Helvetica, sans-serif;
      font-size: 11px;
      font-weight: bold;
color: #FF6600;
    }
.yui3-skin-sam input {
  float:left;
}
a.dpicker {
	width: 16px;
	height: 18px;
	border: none;
	color: #fff;
	padding: 0;
	margin: 0;
	overflow: hidden;
        display:block;	
        cursor: pointer;
	background: url(./includes/calendar.png) no-repeat; 
	float:left;
}
#new_yui3{
	position:absolute;
	
}
</style>
<!-- header_eof //-->
<!-- body //-->
<?php 
if($action != "add_product"){
  echo tep_draw_form('edit_order', "edit_orders.php", tep_get_all_get_params(array('action','paycc')) . 'action=update_order', 'post', 'id="edit_order_id"'); 
}
?>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
<tr>
<td width="<?php echo BOX_WIDTH; ?>" valign="top">
<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
</table>
</td>
<!-- body_text //-->
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
 <div class="compatible">
 <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
if (($action == 'edit') && ($order_exists == true)) {
  //编辑订单 
  $order = new order($oID);
  $products_id_array = array();
  $products_name_array = array();
  $products_orders_id_array = array();
  $products_orders_list_query = tep_db_query("select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($oID) . "'");
  while($products_orders_list_array = tep_db_fetch_array($products_orders_list_query)){

    $products_orders_id_array[] = $products_orders_list_array['orders_products_id'];
  }
  tep_db_free_result($products_orders_list_query);
  if(is_array($_SESSION['new_products_list'][$oID]['orders_products'])||
      is_object($_SESSION['new_products_list'][$_GET['oID']]['orders_products'])){
  foreach($_SESSION['new_products_list'][$oID]['orders_products'] as $orders_products_id_key=>$orders_products_id_value){

    $products_orders_id_array[] = 'o_'.$orders_products_id_key;
  }
  }
  if(is_array($order->products)||is_object($order->products)){
  foreach($order->products as $order_key=>$order_val){

    $products_id_array[] = $order_val['id'];
    $products_name_array[] = $order_val['name']; 
  }
  }
  if(is_array($_SESSION['new_products_list'][$_GET['oID']]['orders_products'])||is_object($_SESSION['new_products_list'][$_GET['oID']]['orders_products'])){
  foreach($_SESSION['new_products_list'][$_GET['oID']]['orders_products'] as $new_value){
    $products_id_array[] = $new_value['products_id']; 
    $products_name_array[] = $new_value['products_name'];
  }
  }
  $products_name_str = implode('|||',$products_name_array);
  $products_id_str = implode('|||',$products_id_array);
  $products_orders_id_str = implode('|||',$products_orders_id_array );
  ?>
    <tr>
    <td width="100%">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
    <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
    <td class="pageHeading" align="right">
    <INPUT type="button" class="element_button" value="<?php echo TEXT_FOOTER_CHECK_SAVE;?>" onClick="if(date_time()){if(products_num_check('<?php echo $products_orders_id_str;?>','<?php echo $products_name_str;?>','<?php echo $products_id_str;?>')){submit_check_con();}}">&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('clear_products'))) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?>
    </td>
    </tr>
    </table>
    <?php echo tep_draw_separator(); ?>
    </td>
    </tr> 
    <tr>
    <td>
    <!-- Begin Update Block -->
    <table width="100%" border="0" cellpadding="2" cellspacing="1">
    <tr>
    <td class="main" bgcolor="#FAEDDE" height="25"><?php echo EDIT_ORDERS_UPDATE_NOTICE;?></td>
    <td class="main" bgcolor="#FBE2C8" width="10">&nbsp;</td>
    <td class="main" bgcolor="#FFCC99" width="10">&nbsp;</td>
    <td class="main" bgcolor="#F8B061" width="10">&nbsp;</td>
    <td class="main" bgcolor="#FF9933" width="120" align="center">&nbsp;</td>
    </tr>
    </table>
    <!-- End Update Block -->
    <br>
    <!-- Begin Addresses Block -->
    <span class="SubTitle"><?php echo MENUE_TITLE_CUSTOMER; ?></span>
    <table width="100%" border="0" class="dataTableRow" cellpadding="2" cellspacing="0">
    <tr>
    <td class="main" valign="top" width="30%"><?php echo ENTRY_SITE;?>:</td>
    <td class="main" width="70%"><?php echo tep_get_site_name_by_order_id($oID);?></td>
    </tr>
    <tr>
    <td class="main" valign="top" width="30%"><?php echo EDIT_ORDERS_ID_TEXT;?></td>
    <td class="main" width="70%"><?php echo $oID;?></td>
    </tr>
    <tr>
    <td class="main" valign="top"><?php echo EDIT_ORDERS_DATE_TEXT;?></td>
    <td class="main"><?php echo tep_date_long($order->info['date_purchased']);?></td>
    </tr>
    <tr>
    <td class="main" valign="top"><?php echo EDIT_ORDERS_CUSTOMER_NAME;?></td>
    <td class="main">
    <input name="update_customer_name" size="25" value="<?php echo tep_html_quotes(isset($_SESSION['orders_update_products'][$_GET['oID']]['update_customer_name']) ? $_SESSION['orders_update_products'][$_GET['oID']]['update_customer_name']: $order->customer['name']); ?>">
    <span class="smalltext"><?php echo EDIT_ORDERS_CUSTOMER_NAME_READ;?></span>
    </td>
    </tr>
    <tr>
    <td class="main" valign="top"><?php echo EDIT_ORDERS_EMAIL;?></td>
    <td class="main"><input name="update_customer_email_address" size="45" value="<?php echo isset($_SESSION['orders_update_products'][$_GET['oID']]['update_customer_email_address']) ? $_SESSION['orders_update_products'][$_GET['oID']]['update_customer_email_address'] : $order->customer['email_address']; ?>"></td>
    </tr>
    <!-- End Addresses Block -->
    <!-- Begin Payment Block -->
    <tr>
    <td class="main" valign="top"><?php echo EDIT_ORDERS_PAYMENT_METHOD;?></td>
    <td class="main">
<?php 
      $pay_method = isset($_SESSION['orders_update_products'][$_GET['oID']]['payment_method']) ? $_SESSION['orders_update_products'][$_GET['oID']]['payment_method'] : payment::changeRomaji($order->info['payment_method'], PAYMENT_RETURN_TYPE_CODE);
      $pay_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : $pay_method;
      $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".$oID."' order by date_added desc limit 0,1"); 
      $orders_status_history_array = tep_db_fetch_array($orders_status_history_query);
      $pay_comment = $orders_status_history_array['comments']; 
      tep_db_free_result($orders_status_history_query);
      $payment_array = payment::getPaymentList();
      $pay_info_array = array();
      $pay_orders_id_array = array();
      $pay_type_array = array();
      foreach($payment_array[0] as $pay_key=>$pay_value){ 
        $payment_info = $cpayment->admin_get_payment_info_comment($pay_value,$order->customer['email_address'],$order->info['site_id']);
        if(is_array($payment_info)){

          switch($payment_info[0]){
          case 0: 
            $pay_orders_id_array[0] = $payment_info[1];
            $pay_type_array[0] = $pay_value;
            break;
          case 1: 
            $pay_orders_id_array[1] = $payment_info[1];
            $pay_type_array[1] = $pay_value;
            break;
          case 2: 
            $pay_orders_id_array[2] = $payment_info[1];
            $pay_type_array[2] = $pay_value;
            break;   
          }
        } 
      }

          if($pay_orders_id_array[0] != '' && $pay_method != $pay_type_array[0]){ 
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id_array[0]."' order by date_added desc"); 
            while($orders_status_history_array = tep_db_fetch_array($orders_status_history_query)){
              if($orders_status_history_array['comments']!=''){
                $pay_info_array[0] = $orders_status_history_array['comments']; 
                break;
              }
            }
            tep_db_free_result($orders_status_history_query);
          }
          if($pay_orders_id_array[1] != '' && $pay_method != $pay_type_array[1]){ 
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id_array[1]."' order by date_added desc"); 
            while($orders_status_history_array = tep_db_fetch_array($orders_status_history_query)){
              if($orders_status_history_array['comments']!=''){
                $pay_info_array[1] = $orders_status_history_array['comments']; 
                break;
              }
            }
            tep_db_free_result($orders_status_history_query);
          }
          if($pay_orders_id_array[2] != '' && $pay_method != $pay_type_array[2]){ 
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id_array[2]."' order by date_added desc"); 
            while($orders_status_history_array = tep_db_fetch_array($orders_status_history_query)){
              if($orders_status_history_array['comments']!=''){
                $pay_info_array[2] = $orders_status_history_array['comments']; 
                break;
              }
            }
            tep_db_free_result($orders_status_history_query);
          }
          $pay_info_array[0] = $pay_info_array[0] == '' && $pay_method == $pay_type_array[0] ? $pay_comment : $pay_info_array[0];
          $pay_info_array[1] = $pay_info_array[1] == '' && $pay_method == $pay_type_array[1] ? $pay_comment : $pay_info_array[1];
          $pay_info_array[2] = $pay_info_array[2] == '' && $pay_method == $pay_type_array[2] ?  $pay_comment : $pay_info_array[2];  
    ?>
    <?php echo payment::makePaymentListPullDownMenu(payment::changeRomaji($pay_method, PAYMENT_RETURN_TYPE_CODE));?> 
    <?php  
          echo "\n".'<script language="javascript">'."\n"; 
          echo '$(document).ready(function(){'."\n";

          $cpayment->admin_show_payment_list($pay_method,$pay_info_array); 
          echo '});'."\n";
          echo '</script>'."\n";
      
          if(!isset($selections)){
            $selections = $cpayment->admin_selection();
          } 
          echo '<table>';
          foreach ($selections as $se){
            if(!is_array($se['fields'])&&!is_object($se['fields'])){
              continue;
            }
            foreach($se['fields'] as $field ){
              echo '<tr class="rowHide rowHide_'.$se['id'].'">';
              echo '<td class="main">';
              echo $field['title']."</td>";
              echo "<td class='main'>";
              echo "&nbsp;&nbsp;".$field['field'];
              if(isset($_POST['payment_method'])){
                $pay_arr = array();
                preg_match_all('/name="(.*?)"/',$field['field'],$pay_arr);
                if(trim($_POST[$pay_arr[1][0]]) == ''){
                  $field['message'] = ''; 
                }else{
                  $field['message'] = ''; 
                }
              }else{
                if(!$cpayment->admin_get_payment_buying_type(payment::changeRomaji($pay_method, 'code'),$field['title'])){
                  $field['message'] = '';
                }
              }
              echo "<font color='red'>&nbsp;".$field['message']."</font>";
              echo "</td>";
              echo "</tr>";
           } 
         }
         echo '</table>'; 
         $pay_array = explode("\n",trim($pay_info_array[0]));
         $bank_name = explode(':',$pay_array[0]);
         if(!isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_name'])){ 
           $_SESSION['orders_update_products'][$_GET['oID']]['bank_name'] = $bank_name[1];
         }
         $bank_shiten = explode(':',$pay_array[1]);
         if(!isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_shiten'])){
           $_SESSION['orders_update_products'][$_GET['oID']]['bank_shiten'] = $bank_shiten[1];
         }
         $bank_kamoku = explode(':',$pay_array[2]);
         if(!isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kamoku'])){
           $_SESSION['orders_update_products'][$_GET['oID']]['bank_kamoku'] = $bank_kamoku[1];
         }
         $bank_kouza_num = explode(':',$pay_array[3]);
         if(!isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_num'])){
           $_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_num'] = $bank_kouza_num[1];
         }
         $bank_kouza_name = explode(':',$pay_array[4]);
         if(!isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_name'])){
           $_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_name'] = $bank_kouza_name[1];
         }
         $pay_array = explode("\n",trim($pay_info_array[1]));
         $con_email = explode(":",trim($pay_array[0]));
         if(!isset($_SESSION['orders_update_products'][$_GET['oID']]['con_email'])){
           $_SESSION['orders_update_products'][$_GET['oID']]['con_email'] = $con_email[1];
         }
         $pay_array = explode("\n",trim($pay_info_array[2]));
         $rak_tel = explode(":",trim($pay_array[0]));
         if(!isset($_SESSION['orders_update_products'][$_GET['oID']]['rak_tel'])){
           $_SESSION['orders_update_products'][$_GET['oID']]['rak_tel'] = $rak_tel[1];
         }
    ?> 
    </td>
    </tr>
    <tr>
    <td class="main" valign="top"><?php echo EDIT_ORDERS_FETCHTIME;?></td>
    <td class="main">
    <?php
      $date_array = explode('_',$order->tori['date']);
      $date_start_array = explode(' ',$date_array[0]);
      $fetch_date_array = explode('-', $date_start_array[0]); 
    ?>
    <div style="float:left;"> 
    <select name="fetch_year" id="fetch_year" onchange="change_fetch_date();">
   <?php
      $fetch_date_array[0] = isset($_SESSION['orders_update_products'][$_GET['oID']]['fetch_year']) ? $_SESSION['orders_update_products'][$_GET['oID']]['fetch_year'] : $fetch_date_array[0];
      $default_fetch_year = (isset($_POST['fetch_year']))?$_POST['fetch_year']:$fetch_date_array[0]; 
      for ($f_num = 2006; $f_num <= 2050; $f_num++) {
        echo '<option value="'.$f_num.'"'.(($default_fetch_year == $f_num)?' selected':'').'>'.$f_num.'</option>'; 
      }
    ?>
    </select>
    <select name="fetch_month" id="fetch_month" onchange="change_fetch_date();">
    <?php
      for ($f_num = 1; $f_num <= 12; $f_num++) {
        $fetch_date_array[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['fetch_month']) ? $_SESSION['orders_update_products'][$_GET['oID']]['fetch_month'] : $fetch_date_array[1];
        $default_fetch_month = (isset($_POST['fetch_month']))?$_POST['fetch_month']:$fetch_date_array[1]; 
        $tmp_fetch_month = sprintf('%02d', $f_num); 
        echo '<option value="'.$tmp_fetch_month.'"'.(($default_fetch_month == $tmp_fetch_month)?' selected':'').'>'.$tmp_fetch_month.'</option>'; 
      }
    ?>
    </select>
    <select name="fetch_day" id="fetch_day" onchange="change_fetch_date();">
    <?php
      for ($f_num = 1; $f_num <= 31; $f_num++) {
        $fetch_date_array[2] = isset($_SESSION['orders_update_products'][$_GET['oID']]['fetch_day']) ? $_SESSION['orders_update_products'][$_GET['oID']]['fetch_day'] : $fetch_date_array[2];
        $default_fetch_day = (isset($_POST['fetch_day']))?$_POST['fetch_day']:$fetch_date_array[2]; 
        $tmp_fetch_day = sprintf('%02d', $f_num); 
        echo '<option value="'.$tmp_fetch_day.'"'.(($default_fetch_day == $tmp_fetch_day)?' selected':'').'>'.$tmp_fetch_day.'</option>'; 
      }
    ?>
    </select>
    </div>
    <div class="yui3-skin-sam yui3-g">
    <input id="date_orders" type="hidden" name='date_orders' size='15' value='<?php echo str_replace('&nbsp;','',$date_start_array[0]); ?>'>
    <a href="javascript:void(0);" onclick="open_calendar();" class="dpicker"></a> 
    <input type="hidden" id="date_order" name="update_tori_torihiki_date" value="<?php echo str_replace('&nbsp;','',$date_start_array[0]); ?>">
    <input type="hidden" name="toggle_open" value="0" id="toggle_open"> 
    <div class="yui3-u" id="new_yui3">
    <div id="mycalendar"></div> 
    </div>
    </div>
    <?php
      // 生成时间下拉框
      $date_start_array[1] = str_replace('&nbsp;','',$date_start_array[1]);
      $start_temp = explode(":",$date_start_array[1]);
      $start_temp[0] = isset($_SESSION['orders_update_products'][$_GET['oID']]['hour']) ? $_SESSION['orders_update_products'][$_GET['oID']]['hour'] : $start_temp[0];
      $hour_str = '&nbsp;<select name="start_hour" id="hour" onchange="check_hour(this.value);">';
      for($h = 0;$h <= 23;$h++){
        
        $h_str = $h < 10 ? '0'.$h : $h; 
        $selected = (int)$start_temp[0] == $h ? ' selected' : '';
        $hour_str .= '<option value="'.$h_str.'"'.$selected.'>'.$h_str.'</option>';

      }
      $hour_str .= '</select>&nbsp;'.TEXT_HOUR;
      echo $hour_str;
      $work_min_temp = substr($start_temp[1],0,1);
      $work_min_temp = isset($_SESSION['orders_update_products'][$_GET['oID']]['min']) ? $_SESSION['orders_update_products'][$_GET['oID']]['min'] : $work_min_temp;
      $min_str_1 = '&nbsp;<select name="start_min_1" id="min" onchange="check_min(this.value);">';
      for($m_1 = 0;$m_1 <= 5;$m_1++){
        
        $selected = (int)$work_min_temp == $m_1 ? ' selected' : '';
        $min_str_1 .= '<option value="'.$m_1.'"'.$selected.'>'.$m_1.'</option>';

      }
      $min_str_1 .= '</select>';
      echo $min_str_1;
      $min_str_temp = substr($start_temp[1],1,1);
      $min_str_temp = isset($_SESSION['orders_update_products'][$_GET['oID']]['min_1']) ? $_SESSION['orders_update_products'][$_GET['oID']]['min_1'] : $min_str_temp;
      $min_str_2 = '<select name="start_min_2" id="min_1" onchange="check_min_1(this.value);">';
      for($m_2 = 0;$m_2 <= 9;$m_2++){
        
        $selected = (int)$min_str_temp == $m_2 ? ' selected' : '';
        $min_str_2 .= '<option value="'.$m_2.'"'.$selected.'>'.$m_2.'</option>';

      }
      $min_str_2 .= '</select>&nbsp;'.TEXT_MIN.'&nbsp;'.TEXT_TIME_LINK;
      echo $min_str_2;
      $date_array[1] = str_replace('&nbsp;','',$date_array[1]);
      $end_temp = explode(":",$date_array[1]);
      $end_temp[0] = isset($_SESSION['orders_update_products'][$_GET['oID']]['hour_1']) ? $_SESSION['orders_update_products'][$_GET['oID']]['hour_1'] : $end_temp[0];
      $hour_str_1 = '&nbsp;<select name="end_hour" id="hour_1" onchange="check_hour_1(this.value);">';
      for($h_1 = (int)$start_temp[0];$h_1 <= 23;$h_1++){
        
        $h_str_1 = $h_1 < 10 ? '0'.$h_1 : $h_1; 
        $selected = (int)$end_temp[0] == $h_1 ? ' selected' : '';
        $hour_str_1 .= '<option value="'.$h_str_1.'"'.$selected.'>'.$h_str_1.'</option>';

      }
      $hour_str_1 .= '</select>&nbsp;'.TEXT_HOUR;
      echo $hour_str_1;
      $min_str_1_temp = substr($end_temp[1],0,1);
      $min_str_1_temp = isset($_SESSION['orders_update_products'][$_GET['oID']]['min_end']) ? $_SESSION['orders_update_products'][$_GET['oID']]['min_end'] : $min_str_1_temp;
      $min_str_1_end = '&nbsp;<select name="end_min_1" id="min_end" onchange="check_end_min(this.value);">';
      $min_start = (int)$work_min_temp; 
      $min_start = $start_temp[0] < $end_temp[0] ? 0 : $min_start;
      for($m_1_end = $min_start;$m_1_end <= 5;$m_1_end++){
        
        $selected = (int)$min_str_1_temp == $m_1_end ? ' selected' : '';
        $min_str_1_end .= '<option value="'.$m_1_end.'"'.$selected.'>'.$m_1_end.'</option>';

      }
      $min_str_1_end .= '</select>';
      echo $min_str_1_end;
      $min_str_end_temp = substr($end_temp[1],1,1);
      $min_str_end_temp = isset($_SESSION['orders_update_products'][$_GET['oID']]['min_end_1']) ? $_SESSION['orders_update_products'][$_GET['oID']]['min_end_1'] : $min_str_end_temp;
      $min_str_2_end = '<select name="end_min_2" id="min_end_1">';
      $min_end = (int)$min_str_end_temp;
      $min_end = $min_str_1_temp == $work_min_temp ? $min_str_temp : 0;
      $min_end = $start_temp[0] < $end_temp[0] ? 0 : $min_end;
      for($m_2_end = $min_end;$m_2_end <= 9;$m_2_end++){
        
        $selected = (int)$min_str_end_temp == $m_2_end ? ' selected' : '';
        $min_str_2_end .= '<option value="'.$m_2_end.'"'.$selected.'>'.$m_2_end.'</option>';

      }
      $min_str_2_end .= '</select>&nbsp;'.TEXT_MIN.'&nbsp;';
      echo $min_str_2_end;
    ?>
    <input type="hidden" name='update_tori_torihiki_start_date' size='10' value='<?php echo str_replace('&nbsp;','',$date_start_array[1]); ?>'>
    <input type="hidden" name='update_tori_torihiki_end_date' size='10' value='<?php echo str_replace('&nbsp;','',$date_array[1]); ?>'>
    </td>
    </tr>
    <?php
      $address_temp_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."'");
      $count_num = tep_db_num_rows($address_temp_query);
      if($count_num > 0){
    ?>
    <tr>
    <td class="main" valign="top"><a href="javascript:void(0);" onclick="address_show();"><font color="blue"><u><span id="address_font"><?php echo TEXT_SHIPPING_ADDRESS;?></span></u></font></a></td>
    <td class="main">
    </td>
    </tr>
    <tr>
    <?php
        $address_style = isset($address_style) && $address_style != '' ? $address_style : 'display: none;'; 
        $old_checked = !isset($_POST['address_option']) || $_POST['address_option'] == 'old' ? 'checked' : '';
        $new_checked = isset($_POST['address_option']) && $_POST['address_option'] == 'new' ? 'checked' : '';
    ?>
      <td colspan="2"><table width="100%" border="0" cellpadding="2" cellspacing="0" id="address_show_id" style="<?php echo $address_style;?>">
      <tr>
        <td class="main" width="30%">&nbsp;</td>
        <td class="main" width="70%"><table border="0" cellpadding="0" cellspacing="0">
         <tr><td>
         <input type="radio" name="address_option" value="old" style="margin: 0 4px 2px 0;" onClick="address_option_show('old');address_list();address_clear_error();" <?php echo $old_checked;?>></td><td><?php echo TABLE_OPTION_OLD; ?></td><td>
        <input type="radio" name="address_option" value="new" style="margin: 0 4px 2px 15px;" onClick="address_option_show('new');" <?php echo $new_checked;?>></td><td><?php echo TABLE_OPTION_NEW; ?></td> 
        </tr>
        </table>
        </td>
      </tr>
      <tr id="address_list_id">
<td class="main" width="30%"><?php echo TABLE_ADDRESS_SHOW; ?></td>
<td class="main" width="70%">
<select name="address_show_list" id="address_show_list" onChange="address_option_list(this.value);">
<option value="">--</option>
</select>
</td></tr>

    <?php
      $ad_option->render('');
    ?>
    </table></td></tr>
    <?php
      }
    ?>
<tr>
<td colspan="2">
    <input type="hidden" name="update_viladate" value="true">
    <input name="update_customer_company" size="25" type='hidden' value="<?php echo tep_html_quotes($order->customer['company']); ?>">
    <input name="update_delivery_company" size="25" type='hidden' value="<?php echo tep_html_quotes($order->delivery['company']); ?>">
    <input name="update_delivery_name" size="25" type='hidden' value="<?php echo tep_html_quotes($order->delivery['name']); ?>">
    <input name="update_customer_name_f" size="25" type='hidden' value="<?php echo tep_html_quotes($order->customer['name_f']); ?>">
    <input name="update_delivery_name_f" size="25" type='hidden' value="<?php echo tep_html_quotes($order->delivery['name_f']); ?>">
    <input name="update_customer_street_address" size="25" type='hidden' value="<?php echo tep_html_quotes($order->customer['street_address']); ?>">
    <input name="update_delivery_street_address" size="25" type='hidden' value="<?php echo tep_html_quotes($order->delivery['street_address']); ?>">
    <input name="update_customer_suburb" size="25" type='hidden' value="<?php echo tep_html_quotes($order->customer['suburb']); ?>">
    <input name="update_delivery_suburb" size="25" type='hidden' value="<?php echo tep_html_quotes($order->delivery['suburb']); ?>">
    <input name="update_customer_city" size="25" type='hidden' value="<?php echo tep_html_quotes($order->customer['city']); ?>">
    <input name="update_delivery_city" size="25" type='hidden' value="<?php echo tep_html_quotes($order->delivery['city']); ?>">
    <input name="update_customer_state" size="25" type='hidden' value="<?php echo tep_html_quotes($order->customer['state']); ?>">
    <input name="update_delivery_state" size="25" type='hidden' value="<?php echo tep_html_quotes($order->delivery['state']); ?>">
    <input name="update_customer_postcode" size="25" type='hidden' value="<?php echo $order->customer['postcode']; ?>">
    <input name="update_delivery_postcode" size="25" type='hidden' value="<?php echo $order->delivery['postcode']; ?>">
    <input name="update_customer_country" size="25" type='hidden' value="<?php echo tep_html_quotes($order->customer['country']); ?>">
    <input name="update_delivery_country" size="25" type='hidden' value="<?php echo tep_html_quotes($order->delivery['country']); ?>">
    <input name="update_customer_telephone" size="25" type='hidden' value="<?php echo $order->customer['telephone']; ?>">
</td>
</tr>
    </table>
    <!-- End Trade Date Block -->
    </td>
    </tr>
    <!-- Begin Products Listing Block -->
    <tr>
    <td class="SubTitle"><br><?php echo EDIT_ORDERS_PRO_LIST_TITLE;?></td>
    </tr>
    <tr>
    <td>      
    <?php
    // Override order.php Class's Field Limitations
    $index = 0;
  $order->products = array();
  $orders_products_query = tep_db_query("select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($oID) . "'");
  while ($orders_products = tep_db_fetch_array($orders_products_query)) {
    $order->products[$index] = array(
        'id' => $orders_products['products_id'],
        'qty' => $orders_products['products_quantity'],
        'name' => str_replace("'", "&#39;", $orders_products['products_name']),
        'model' => $orders_products['products_model'],
        'tax' => $orders_products['products_tax'],
        'price' => $orders_products['products_price'],
        'final_price' => $orders_products['final_price'],
        'orders_products_id' => $orders_products['orders_products_id']);

    $subindex = 0;
    $attributes_query_string = "select * from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . tep_db_input($oID) . "' and orders_products_id = '" . (int)$orders_products['orders_products_id'] . "'";
    $attributes_query = tep_db_query($attributes_query_string);

    if (tep_db_num_rows($attributes_query)) {
      while ($attributes = tep_db_fetch_array($attributes_query)) {
        $order->products[$index]['attributes'][$subindex] = array('id' => $attributes['orders_products_attributes_id'],
            'option_info' => @unserialize(stripslashes($attributes['option_info'])),
            'option_group_id' => $attributes['option_group_id'],
            'option_item_id' => $attributes['option_item_id'],
            'price' => $attributes['options_values_price']);
        $subindex++;
      }
    }
    $index++;
  }

  ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2" id="ctable">
    <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" colspan="2" width="35%"><?php echo TABLE_HEADING_NUM_PRO_NAME;?></td>
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENICY; ?></td>
    <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_BEFORE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_AFTER; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_BEFORE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_AFTER; ?></td>
    </tr>

    <?php
    $new_products_temp_list = array();
    if(is_array($_SESSION['new_products_list'][$oID]['orders_products'])||
      is_object($_SESSION['new_products_list'][$oID]['orders_products'])){
    foreach($_SESSION['new_products_list'][$oID]['orders_products'] as $list_key=>$list_value){

      $new_products_list_attributes_array = array();
      foreach($list_value['products_attributes'] as $attr_key=>$attr_value){

        $attr_list_array = array();
        $attr_list_array = unserialize(stripcslashes($attr_value['option_info']));
        $new_products_list_attributes_array[] = array('id'=>'a_'.$attr_key,
                                                      'option_info'=>$attr_list_array, 
                                                      'option_group_id'=>$attr_value['option_group_id'],
                                                      'option_item_id'=>$attr_value['option_item_id'],
                                                      'price'=>$attr_value['options_values_price']
                                                      );
      }
      $new_products_temp_list = array('id'=>$list_value['products_id'],
                                      'qty'=>$list_value['products_quantity'],
                                      'name'=>$list_value['products_name'],
                                      'model'=>$list_value['products_model'],
                                      'tax'=>sprintf("%01.4f",$list_value['products_tax']),
                                      'price'=>sprintf("%01.4f",$list_value['products_price']),
                                      'final_price'=>sprintf("%01.4f",$list_value['final_price']),
                                      'orders_products_id'=>'o_'.$list_key,
                                      'attributes'=>$new_products_list_attributes_array 
                                    );
      $order->products[] = $new_products_temp_list;
    } 
    }
    $all_p_info_array = array(); 
    $orders_products_array = array(); 
    $orders_products_list = '';
    for ($k=0; $k<sizeof($order->products); $k++) {
       $orders_products_array[] = $order->products[$k]['orders_products_id']; 
    }
    $orders_products_list = implode('|||',$orders_products_array);
    echo '<div id="popup_window" class="popup_window"></div>';
    for ($i=0; $i<sizeof($order->products); $i++) {
      $op_info_str = '';
      if ($order->products[$i]['attributes'] && sizeof($order->products[$i]['attributes']) > 0) {
        $op_info_array = array();
        for ($i_num = 0; $i_num < sizeof($order->products[$i]['attributes']); $i_num++) {
          $op_info_array[] = $order->products[$i]['attributes'][$i_num]['id']; 
        }
        $op_info_str = implode('|||', $op_info_array);
      }
      $orders_products_id = $order->products[$i]['orders_products_id'];
      $all_p_info_array[] = $orders_products_id;  
      $tmp_op_str = substr($orders_products_id, 0, 2);
      if ($tmp_op_str == 'o_') {
        $is_less_option = tep_check_less_option_product_by_products_id($order->products[$i]['id'], $order->products[$i]['attributes']); 
      } else {
        $is_less_option = tep_check_less_option_product($orders_products_id); 
      }
      $RowStyle = "dataTableContent";
      $order->products[$i]['qty'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['qty']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['qty'] : $order->products[$i]['qty']; 
      echo '    <tr class="dataTableRow" id="products_list_'.$orders_products_id.'">' . "\n" .
        '      <td class="' . $RowStyle . '" align="left" valign="top" width="8%" style="min-width:100px;">'
        . "<input type='hidden'
        name='update_products_real_quantity[$orders_products_id]'
        id='update_products_real_quantity_$orders_products_id' value='1'><input
        type='hidden' id='update_products_qty_$orders_products_id' value='" .
        $order->products[$i]['qty'] . "'>";
      if ($is_less_option) {
        echo "<input type='text' class='update_products_qty' style='background: none repeat scroll 0 0 #CCCCCC;' readonly id='update_products_new_qty_$orders_products_id' name='update_products[$orders_products_id][qty]' size='2' value='" .  (isset($_POST['update_products'][$orders_products_id]['qty'])?$_POST['update_products'][$orders_products_id]['qty']:$order->products[$i]['qty']) . "'>";
      } else {
        echo "<input type='text' class='update_products_qty' id='update_products_new_qty_$orders_products_id' name='update_products[$orders_products_id][qty]' size='2' value='" .  (isset($_POST['update_products'][$orders_products_id]['qty'])?$_POST['update_products'][$orders_products_id]['qty']:$order->products[$i]['qty']) . "' onkeyup='clearLibNum(this);recalc_order_price(\"".$oID."\", \"".$orders_products_id."\", \"2\", \"".$op_info_str."\",\"".$orders_products_list."\");price_total(\"".TEXT_MONEY_SYMBOL."\");'>";
      }
      echo "&nbsp;<input type='button' value='".IMAGE_DELETE."' onclick=\"delete_products( '".$orders_products_id."', '".TEXT_MONEY_SYMBOL."','".$customer_guest['is_calc_quantity']."');recalc_order_price('".$oID."', '".$orders_products_id."', '2', '".$op_info_str."','".$orders_products_list."');\">&nbsp;x</td>\n" . 
        '      <td class="' . $RowStyle . '">' . $order->products[$i]['name'] . "<input name='update_products[$orders_products_id][name]' size='64' id='update_products_name_$orders_products_id' type='hidden' value='" . $order->products[$i]['name'] . "'>\n" . 
        '      &nbsp;&nbsp;';
      if ($is_less_option) {
        echo '<br><font color="#ff0000" size="1">'.NOTICE_LESS_PRODUCT_OPTION_TEXT.'</font>'; 
      }
      // Has Attributes?
      $op_info_str = '';
      if ($order->products[$i]['attributes'] && sizeof($order->products[$i]['attributes']) > 0) {
        $op_info_array = array();
        for ($i_num = 0; $i_num < sizeof($order->products[$i]['attributes']); $i_num++) {
          $op_info_array[] = $order->products[$i]['attributes'][$i_num]['id']; 
        }
        $op_info_str = implode('|||', $op_info_array);
              // new option list
      $all_show_option_id = array();
      $all_show_option = array();
      $option_item_order_sql = "select it.id,it.type item_type,it.option item_option from ".TABLE_PRODUCTS."
      p,".TABLE_OPTION_ITEM." it 
      where p.products_id = '".(int)$order->products[$i]['id']."' 
      and p.belong_to_option = it.group_id 
      and it.status = 1
      order by it.sort_num,it.title";
      $option_item_order_query = tep_db_query($option_item_order_sql);
      $item_type_array = array();
      $item_option_array = array(); 
      $op_include_array = array(); 
      while($show_option_row_item = tep_db_fetch_array($option_item_order_query)){
        $all_show_option_id[] = $show_option_row_item['id'];
        $item_type_array[$show_option_row_item['id']] = $show_option_row_item['item_type'];
        $item_option_array[$show_option_row_item['id']] = $show_option_row_item['item_option']; 
      }  
      for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {

        $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['id'];
        $order->products[$i]['attributes'][$j]['price'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['price']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['price'] : $order->products[$i]['attributes'][$j]['price'];
                $order->products[$i]['attributes'][$j]['option_info']['title'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['option_info']['title']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['option_info']['title'] : $order->products[$i]['attributes'][$j]['option_info']['title'];
                $order->products[$i]['attributes'][$j]['option_info']['value'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['option_info']['value']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['option_info']['value'] : $order->products[$i]['attributes'][$j]['option_info']['value'];
        $all_show_option[$order->products[$i]['attributes'][$j]['option_item_id']] =
          $order->products[$i]['attributes'][$j];
      }
      foreach($all_show_option_id as $t_item_id){
        $op_include_array[] = $all_show_option[$t_item_id]['id'];
        $item_type = $item_type_array[$t_item_id]; 
        $item_option_string = $item_option_array[$t_item_id];
        $item_option_string_array = unserialize($item_option_string);
        $item_option_temp_array = array();
        if($item_type == 'radio'){
           foreach($item_option_string_array['radio_image'] as $item_value){
            $item_option_line_array = explode("\n",$item_value['title']);
            foreach($item_option_line_array as $item_line_key=>$item_line_value){

              $item_option_line_array[$item_line_key] = trim($item_line_value);
            }
            if(count($item_option_line_array) > 1){
              $item_option_line_str = implode("|||<<<",$item_option_line_array); 
            }else{
              $item_option_line_str = $item_value['title'];
            }
            $item_option_temp_array[] = $item_option_line_str; 
          }
          $item_list = implode('|||>>>',$item_option_temp_array);    
        }else if($item_type == 'select'){
          foreach($item_option_string_array['se_option'] as $item_value){
            $item_option_temp_array[] = $item_value; 
          }
          $item_list = implode('|||>>>',$item_option_temp_array); 
        } 
        if($item_type == 'textarea'){
          if($item_option_string_array['iline'] == 1){
            $item_type = 'text'; 
          } 
        }else if($item_type == 'text'){
          $item_type = 'textarea'; 
        }
        $orders_products_attributes_id = $all_show_option[$t_item_id]['id'];
        if(is_array($all_show_option[$t_item_id]['option_info'])){
        $default_value = tep_parse_input_field_data($all_show_option[$t_item_id]['option_info']['value'], array("'"=>"&quot;")) == '' ? TEXT_UNSET_DATA : tep_parse_input_field_data($all_show_option[$t_item_id]['option_info']['value'], array("'"=>"&quot;"));
        echo '<br><div class="order_option_width">&nbsp;<i><div class="order_option_info"><div class="order_option_title"> - ' 
          .tep_parse_input_field_data($all_show_option[$t_item_id]['option_info']['title'], array("'"=>"&quot;"))."<input type='hidden' onkeyup='recalc_order_price(\"".$oID."\", \"".$orders_products_id."\", \"2\", \"".$op_info_str."\",\"".$orders_products_list."\");price_total(\"".TEXT_MONEY_SYMBOL."\");' class='option_input_width' name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][option]' value='" .  (isset($_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['option'])?tep_parse_input_field_data($_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['option'], array("'"=>"&quot;")):tep_parse_input_field_data($all_show_option[$t_item_id]['option_info']['title'], array("'"=>"&quot;"))) . "'>" .
          '</div><div class="order_option_value">: ';
        if ($is_less_option) {
          echo $default_value; 
        } else {
          echo "<a onclick='popup_window(this,\"".$item_type."\",\"".tep_parse_input_field_data($all_show_option[$t_item_id]['option_info']['title'], array("'"=>"&quot;"))."\",\"".$item_list."\")' href='javascript:void(0);'><u>".$default_value."</u></a>";
        }
        echo "<input type='hidden' onkeyup='recalc_order_price(\"".$oID."\", \"".$orders_products_id."\", \"2\", \"".$op_info_str."\",\"".$orders_products_list."\");price_total(\"".TEXT_MONEY_SYMBOL."\");' class='option_input_width' name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][value]' value='" .  (isset($_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['value'])?tep_parse_input_field_data($_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['value'], array("'"=>"&quot;")):tep_parse_input_field_data($all_show_option[$t_item_id]['option_info']['value'], array("'"=>"&quot;")));
          echo "'></div></div>";
          echo '<div class="order_option_price">'; 
          if ($is_less_option) {
            echo "<input type='text' size='9' style='background: none repeat scroll 0 0 #CCCCCC' readonly name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][price]' value='".(int)(isset($_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price'])?$_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price']:$all_show_option[$t_item_id]['price'])."'>";   
          } else {
            echo "<input type='text' size='9' name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][price]' value='".(int)(isset($_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price'])?$_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price']:$all_show_option[$t_item_id]['price'])."' onkeyup=\"clearNewLibNum(this);recalc_order_price('".$oID."', '".$orders_products_id."', '1', '".$op_info_str."','".$orders_products_list."');price_total('".TEXT_MONEY_SYMBOL."');\">";   
          }
          echo TEXT_MONEY_SYMBOL; 
          echo '</div>'; 
          echo '</i></div>';
          }
        }
        foreach ($order->products[$i]['attributes'] as $ex_key => $ex_value) {
          if (!in_array($ex_value['id'], $op_include_array)) {
             echo '<br>';
             echo '<div class="order_option_width">&nbsp;<i><div class="order_option_info"><div class="order_option_title"> - '.tep_parse_input_field_data($ex_value['option_info']['title'], array("'"=>"&quot;"));
             echo "<input type='hidden' class='option_input_width' name='update_products[".$orders_products_id."][attributes][".$ex_value['id']."][option]' value='".(isset($_POST['update_products'][$orders_products_id]['attributes'][$ex_value['id']]['option'])?:$ex_value['option_info']['title'])."'></div><div class=\"order_option_value\">: ".$ex_value['option_info']['value']."<input type='hidden' name='update_products[".$orders_products_id."][attributes][".$ex_value['id']."][value]'class='option_input_width' value='".$ex_value['option_info']['value']."'></div></div>";
             echo '<div class="order_option_price">';
             $tmp_op_price = (int)(isset($_POST['update_products'][$orders_products_id]['attributes'][$ex_value['id']]['price'])?$_POST['update_products'][$orders_products_id]['attributes'][$ex_value['id']]['price']:$ex_value['price']);   
             if ($is_less_option) {
               echo "<input type='text' size='9' style='background: none repeat scroll 0 0 #CCCCCC' readonly name='update_products[".$orders_products_id."][attributes][".$ex_value['id']."][price]' value='".$tmp_op_price."'>";   
             } else {
               echo "<input type='text' size='9' name='update_products[".$orders_products_id."][attributes][".$ex_value['id']."][price]' value='".$tmp_op_price."' onkeyup=\"clearNewLibNum(this);recalc_order_price('".$oID."', '".$orders_products_id."', '1', '".$op_info_str."','".$orders_products_list."');price_total('".TEXT_MONEY_SYMBOL."');\">";   
             }
             echo TEXT_MONEY_SYMBOL; 
             echo '</div>'; 
             echo '</i></div>'; 
          }
        }
      }

      echo '      </td>' . "\n" .
        '      <td class="' . $RowStyle . '">' . $order->products[$i]['model'] . "<input name='update_products[$orders_products_id][model]' size='12' type='hidden' value='" . $order->products[$i]['model'] . "'>" . '</td>' . "\n" .
        '      <td class="' . $RowStyle . '" align="right">' .
        tep_display_tax_value($order->products[$i]['tax']) . "<input name='update_products[$orders_products_id][tax]' size='2' type='hidden' value='" . tep_display_tax_value($order->products[$i]['tax']) . "'>" .  '%</td>' . "\n";

      $order->products[$i]['price'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['p_price']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['p_price'] : $order->products[$i]['price']; 
      if ($is_less_option) {
        echo '<td class="'.$RowStyle.'" align="right"><input type="text" class="once_pwd" style="text-align:right;background: none repeat scroll 0 0 #CCCCCC" readonly name="update_products['.$orders_products_id.'][p_price]" size="9" value="'.tep_display_currency(number_format(abs(isset($_POST['update_products'][$orders_products_id]['p_price'])?$_POST['update_products'][$orders_products_id]['p_price']:$order->products[$i]['price']), 2)).'">'.TEXT_MONEY_SYMBOL.'</td>'; 
      } else {
        echo '<td class="'.$RowStyle.'" align="right"><input type="text" class="once_pwd" style="text-align:right;" name="update_products['.$orders_products_id.'][p_price]" size="9" value="'.tep_display_currency(number_format(abs(isset($_POST['update_products'][$orders_products_id]['p_price'])?$_POST['update_products'][$orders_products_id]['p_price']:$order->products[$i]['price']), 2)).'" onkeyup="clearLibNum(this);recalc_order_price(\''.$oID.'\', \''.$orders_products_id.'\', \'2\', \''.$op_info_str.'\',\''.$orders_products_list.'\');price_total(\''.TEXT_MONEY_SYMBOL.'\');">'.TEXT_MONEY_SYMBOL.'</td>'; 
      }

      $order->products[$i]['final_price'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['final_price']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['final_price'] : $order->products[$i]['final_price']; 
      echo  '<td class="' . $RowStyle . '" align="right">' . "<input type='hidden' style='text-align:right' class='once_pwd' name='update_products[$orders_products_id][final_price]' size='9' value='" .  tep_display_currency(number_format(abs(isset($_POST['update_products'][$orders_products_id]['final_price'])?$_POST['update_products'][$orders_products_id]['final_price']:$order->products[$i]['final_price']),2)) .  "'" .' onkeyup="clearNoNum(this);recalc_order_price(\''.$oID.'\', \''.$orders_products_id.'\', \'3\',\''.$op_info_str.'\',\''.$orders_products_list.'\');price_total(\''.TEXT_MONEY_SYMBOL.'\');" >'.  
        '<input type="hidden" name="op_id_'.$orders_products_id.'" 
        value="'.tep_get_product_by_op_id($orders_products_id).'"><div id="update_products['.$orders_products_id.'][final_price]">'; 
      if ($order->products[$i]['final_price'] < 0) {
        echo  '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
      } else {
        echo $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']);
      }
      echo '</div></td>' . "\n" . 
        '      <td class="' . $RowStyle . '" align="right">';
      echo '<div id="update_products['.$orders_products_id.'][a_price]">'; 
      if ($order->products[$i]['final_price'] < 0) {
        $a_price_str = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
        $a_price_h_str = '-'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']));
      } else {
        $a_price_str = $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']);
        $a_price_h_str = '+'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']));
      }
      if (isset($_POST['update_products'][$orders_products_id]['ah_price'])) {
        $symbol_str = substr($_POST['update_products'][$orders_products_id]['ah_price'], 0, 1); 
        if ($symbol_str == '+') {
          echo substr($_POST['update_products'][$orders_products_id]['ah_price'], 1).TEXT_MONEY_SYMBOL; 
        } else {
          echo '<font color="#ff0000">'.substr($_POST['update_products'][$orders_products_id]['ah_price'], 1).'</font>'.TEXT_MONEY_SYMBOL; 
        }
      } else {
        echo $a_price_str; 
      }
      echo '</div>'; 
      
      echo '<input type="hidden" value="'.(isset($_POST['update_products'][$orders_products_id]['ah_price'])?$_POST['update_products'][$orders_products_id]['ah_price']:$a_price_h_str).'" name="update_products['.$orders_products_id.'][ah_price]" id="update_products['.$orders_products_id.'][ah_price]">'; 
      echo '</td>' . "\n" . 
        '      <td class="' . $RowStyle . '" align="right">';
      echo '<div id="update_products['.$orders_products_id.'][b_price]">'; 
      if ($order->products[$i]['final_price'] < 0) {
        $b_price_str = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
        
        $b_price_h_str = '-'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']));
      } else {
        $b_price_str = $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']);
        $b_price_h_str = '+'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']));
      }
      
      if (isset($_POST['update_products'][$orders_products_id]['bh_price'])) {
        $symbol_str = substr($_POST['update_products'][$orders_products_id]['bh_price'], 0, 1); 
        if ($symbol_str == '+') {
          echo substr($_POST['update_products'][$orders_products_id]['bh_price'], 1).TEXT_MONEY_SYMBOL; 
        } else {
          echo '<font color="#ff0000">'.substr($_POST['update_products'][$orders_products_id]['bh_price'], 1).'</font>'.TEXT_MONEY_SYMBOL; 
        }
      } else {
        echo $b_price_str; 
      }
      
      echo '</div>'; 
      echo '<input type="hidden" value="'.(isset($_POST['update_products'][$orders_products_id]['bh_price'])?$_POST['update_products'][$orders_products_id]['bh_price']:$b_price_h_str).'" name="update_products['.$orders_products_id.'][bh_price]" id="update_products['.$orders_products_id.'][bh_price]">';      echo '</td>' . "\n" . 
        '      <td class="' . $RowStyle . '" align="right">';
      echo '<div id="update_products['.$orders_products_id.'][c_price]">'; 
      if ($order->products[$i]['final_price'] < 0) {
        $c_price_str = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value'])).'</font>' .TEXT_MONEY_SYMBOL;
        $c_price_h_str = '-'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']));
      } else {
        $c_price_str = $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']);
        
        $c_price_h_str = '+'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']));
      }
     
      if (isset($_POST['update_products'][$orders_products_id]['ch_price'])) {
        $symbol_str = substr($_POST['update_products'][$orders_products_id]['ch_price'], 0, 1); 
        if ($symbol_str == '+') {
          echo substr($_POST['update_products'][$orders_products_id]['ch_price'], 1).TEXT_MONEY_SYMBOL; 
        } else {
          echo '<font color="#ff0000">'.substr($_POST['update_products'][$orders_products_id]['ch_price'], 1).'</font>'.TEXT_MONEY_SYMBOL; 
        }
      } else {
        echo $c_price_str; 
      }
      
      
      echo '</div>'; 
      echo '<input type="hidden" value="'.(isset($_POST['update_products'][$orders_products_id]['ch_price'])?$_POST['update_products'][$orders_products_id]['ch_price']:$c_price_h_str).'" name="update_products['.$orders_products_id.'][ch_price]" id="update_products['.$orders_products_id.'][ch_price]">'; 
      
      echo '</td>' . "\n" . 
        '    </tr>' . "\n";
    } 
  ?>
    </table>

    </td>
    <tr>
    <td>
    <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
    <td valign="top">&nbsp;</td>
    <td align="right">
    <?php echo '<a href="' . $PHP_SELF . '?oID=' . $oID . '&action=add_product&step=1">' . tep_html_element_button(ADDING_TITLE) . '</a>'; ?>
    </td>
    </tr> 
    </table>
    </td>
    </tr>     
    <!-- End Products Listings Block -->
    <!-- Begin Order Total Block -->
    <tr>
    <td class="SubTitle"><?php echo EDIT_ORDERS_FEE_TITLE_TEXT;?></td>
    </tr>
    <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
    </tr>   
    <tr>
    <td>

    <table width="100%" border="0" cellspacing="0" cellpadding="2" class="dataTableRow" id="add_option">
    <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" align="left" width="60%"><?php echo TABLE_HEADING_FEE_MUST?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_MODULE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_AMOUNT; ?></td>
    <td class="dataTableHeadingContent"width="1"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
    </tr>
<?php
    // Override order.php Class's Field Limitations
    $totals_query = tep_db_query("select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . tep_db_input($oID) . "' order by sort_order");
  $order->totals = array();
  while ($totals = tep_db_fetch_array($totals_query)) { 
    $order->totals[] = array('title' => $totals['title'], 'text' => $totals['value'], 'class' => $totals['class'], 'value' => $totals['value'], 'orders_total_id' => $totals['orders_total_id']); 
  }

  // START OF MAKING ALL INPUT FIELDS THE SAME LENGTH 
  $max_length = 0;
  $TotalsLengthArray = array();
  for ($i=0; $i<sizeof($order->totals); $i++) {
    $TotalsLengthArray[] = array("Name" => $order->totals[$i]['title']);
  }
  reset($TotalsLengthArray);
  foreach($TotalsLengthArray as $TotalIndex => $TotalDetails) {
    if (strlen($TotalDetails["Name"]) > $max_length) {
      $max_length = strlen($TotalDetails["Name"]);
    }
  }
  // END OF MAKING ALL INPUT FIELDS THE SAME LENGTH
 
  $TotalsArray = array();
  for ($i=0; $i<sizeof($order->totals); $i++) {
    $TotalsArray[] = array("Name" => $order->totals[$i]['title'], "Price" => tep_display_currency(number_format($order->totals[$i]['value'], 2, '.', '')), "Class" => $order->totals[$i]['class'], "TotalID" => $order->totals[$i]['orders_total_id']);
     
    if($order->totals[$i]['class'] == 'ot_subtotal' && $order->totals[$i+1]['class'] != 'ot_custom'){

      $TotalsArray[] = array("Name" => "          ", "Price" => "", "Class" => "ot_custom", "TotalID" => "0");
    } 
  }
  $shipping_fee_subtotal = 0; //小计
  $shipping_fee_tax = 0; //税
  $shipping_fee_point = 0; //折点
  $sum_num = count($TotalsArray)-1;
  $show_num = 0;
  $totals_num = '';
  foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
    if(trim($TotalDetails['Name']) == '' && $TotalDetails['Class'] == 'ot_custom' && $TotalIndex != 1 && $TotalIndex != 3){
       unset($TotalsArray[$TotalIndex]);
    }
    if($TotalDetails['Class'] == 'ot_total'){

      $totals_num = $TotalIndex;
    }
  } 
  $sum_array = array_keys($TotalsArray);
  array_pop($sum_array);
  $show_num = end($sum_array);
  $totals_end_value = end($TotalsArray);
  array_pop($TotalsArray);
  $start_num = 0;
  $total_point_str = '';
  $total_point_num = '';
  foreach ($TotalsArray as $TotalIndex => $TotalDetails) {

    if($TotalDetails['Class'] == 'ot_custom'){

      $start_num = $TotalIndex;
    } 

    if($TotalDetails['Class'] == 'ot_point'){

      $total_point_str = $TotalsArray[$TotalIndex];
      unset($TotalsArray[$TotalIndex]);
      $total_point_num = $TotalIndex;
    }
  } 
  if(isset($_SESSION['orders_update_products'][$_GET['oID']]['orders_totals'])){
    $total_num = $_SESSION['orders_update_products'][$_GET['oID']]['orders_totals'];
    for($totals_i = $show_num+2;$totals_i <= $total_num;$totals_i++){
      $TotalsArray[$totals_i]['Name'] = '';
      $TotalsArray[$totals_i]['Price'] = '';
      $TotalsArray[$totals_i]['Class'] = 'ot_custom';
      $TotalsArray[$totals_i]['TotalID'] = 0;
    }
  }else{
   $total_num = $show_num; 
  }
  $TotalsArray[$total_point_num] = $total_point_str;
  $TotalsArray[$totals_num] = $totals_end_value; 
  foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
    $TotalStyle = "smallText";
    if ($TotalDetails["Class"] == "ot_total") {
      $TotalDetails["Price"] = isset($_SESSION['orders_update_products'][$_GET['oID']]['ot_total']) ? $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] : $TotalDetails["Price"];
      $shipping_fee_total = ($shipping_fee_subtotal+$shipping_fee+$order->info["code_fee"]+$shipping_fee_tax-$shipping_fee_point) != $TotalDetails["Price"] ? $shipping_fee : 0; 
      
      echo '  <tr id="add_option_total">' . "\n" .
        '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_OTTOTAL_READ.'</td>' . 
        '    <td align="right" class="' . $TotalStyle . '">' . $TotalDetails["Name"] . '</td>' . 
        '    <td align="right" class="' . $TotalStyle . '"><div id="ot_total_id">';
      if($TotalDetails["Price"] >= 0 ){
        echo $currencies->ot_total_format(($TotalDetails["Price"]), true, $order->info['currency'], $order->info['currency_value']);
      }else{
        echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->ot_total_format(($TotalDetails["Price"]), true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
      }
      echo '</div>' . 
        "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
        "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
        "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
        "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</td>' . 
        '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
        '  </tr>' . "\n";
    } elseif ($TotalDetails["Class"] == "ot_subtotal") {
      $TotalDetails["Price"] = isset($_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal']) ? $_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal'] : $TotalDetails["Price"];
      $shipping_fee_subtotal = $TotalDetails["Price"];
      echo '  <tr>' . "\n" .
        '    <td align="left" class="' . $TotalStyle .  '">&nbsp;</td>' . 
        '    <td align="right" class="' . $TotalStyle . '">' . $TotalDetails["Name"] . '</td>' .
        '    <td align="right" class="' . $TotalStyle . '"><div id="ot_subtotal_id">';
      if($TotalDetails["Price"]>=0){
        echo $currencies->ot_total_format($TotalDetails["Price"], true,
            $order->info['currency'], $order->info['currency_value']);
      }else{
        echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($TotalDetails["Price"], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
      } 
      echo '</div>' . 
        "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
        "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
        "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
        "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</td>' . 
        '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
        '  </tr>' . "\n";        
    } elseif ($TotalDetails["Class"] == "ot_tax") {
      $shipping_fee_tax = $TotalDetails["Price"];
      echo '  <tr>' . "\n" . 
        '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
        '    <td align="right" class="' . $TotalStyle . '">' . trim($TotalDetails["Name"]) . "<input name='update_totals[$TotalIndex][title]' type='hidden' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . '</td>' . "\n" .
        '    <td align="right" class="' . $TotalStyle . '">' . $currencies->format($TotalDetails["Price"], true, $order->info['currency'], $order->info['currency_value']) . '' . 
        "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
        "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
        "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</td>' . 
        '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
        '  </tr>' . "\n";
    } elseif ($TotalDetails["Class"] == "ot_point") {
      $shipping_fee_point = $TotalDetails["Price"];
      $point_session_id = isset($_SESSION['orders_update_products'][$_GET['oID']]['point']) ? $_SESSION['orders_update_products'][$_GET['oID']]['point'] : $TotalDetails["Price"];
      if ($customer_guest['customers_guest_chk'] == 0) { //会員
        $current_point = $customer_point['point'] + $TotalDetails["Price"];
        echo '  <tr>' . "\n" .
          '    <td align="left" class="' . $TotalStyle . '">'.TEXT_CUSTOMER_INPUT.'<font color="red">'.TEXT_REMAINING . $customer_point['point'] . TEXT_SUBTOTAL . $current_point . TEXT_RIGHT_BRACKETS.'</font>'.TEXT_INPUT_POSITIVE_NUM.'</td>' . 
          '    <td align="right" class="' . $TotalStyle . '">' .
          trim($TotalDetails["Name"]) . '</td>' . "\n";
        
          echo '    <td align="right" class="' . $TotalStyle . '" nowrap>−' ;
          $campaign_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$_GET['oID']."' and site_id = '".$order->info['site_id']."'"); 
          $campaign_res = tep_db_fetch_array($campaign_query);
          if ($campaign_res) {
            $campaign_res['campaign_fee'] = isset($_SESSION['orders_update_products'][$_GET['oID']]['point']) ? $_SESSION['orders_update_products'][$_GET['oID']]['point'] : $campaign_res['campaign_fee'];
            echo "<input type='hidden' id='point_value_temp' value='".(int)$campaign_res['campaign_fee']."'><input style='text-align:right;' name='update_totals[$TotalIndex][value]' id='point_id' onkeyup='clearNoNum(this);price_total(\"".TEXT_MONEY_SYMBOL."\");' size='6' value='" .abs((int)$campaign_res['campaign_fee']) .  "'>".TEXT_MONEY_SYMBOL.'';
          } else {
            echo "<input style='text-align:right;' name='update_totals[$TotalIndex][value]' id='point_id' onkeyup='clearNoNum(this);price_total(\"".TEXT_MONEY_SYMBOL."\");' size='6' value='" . $point_session_id . "'>".TEXT_MONEY_SYMBOL.'';
          }
          echo "<input type='hidden' name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . 
          "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
          "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
          "<input type='hidden' name='before_point' value='" . $TotalDetails["Price"] . "'>" . 
          '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
          '   </td>' . "\n" .
          '  </tr>' . "\n";
      } else { //ゲスト
        echo '  <tr>' . "\n" .
          '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_TOTAL_DETAIL_READ.'</td>' . 
          '    <td align="right" class="' . $TotalStyle . '">' . trim($TotalDetails["Name"]) . '</td>' . "\n" .
          '    <td align="right" class="' . $TotalStyle . '">' . $TotalDetails["Price"] .(mb_substr($TotalDetails["Price"],-1,1,'utf-8') == TEXT_MONEY_SYMBOL ? '' : TEXT_MONEY_SYMBOL). 
          "<input type='hidden' id='point_id' value=''><input type='hidden' name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . 
          "<input type='hidden' name='update_totals[$TotalIndex][value]' size='6' value='" . $TotalDetails["Price"] . "'>" . 
          "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
          "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
          '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
          '   </td>' . "\n" .
          '  </tr>' . "\n";
      }
      //手续费，配送费
      $shipping_fee = isset($_SESSION['orders_update_products'][$oID]['shipping_fee']) ? $_SESSION['orders_update_products'][$oID]['shipping_fee'] : $shipping_fee; 
      echo '  <tr>' . "\n" .
           '    <td align="left" class="' . $TotalStyle .  '">&nbsp;</td>' . 
           '    <td align="right" class="' . $TotalStyle . '">' . TEXT_SHIPPING_FEE . '</td>' .
           '    <td align="right" class="' . $TotalStyle . '"><div id="shipping_fee_id">'.
         $currencies->format($shipping_fee) .'</div><input type="hidden" name="shipping_fee_num" value="'. $shipping_fee .'">'.
           '  </tr>'. "\n". 
           '  <tr>' . "\n" .
           '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
           '    <td align="right" class="' . $TotalStyle . '">'.TEXT_CODE_HANDLE_FEE.'</td>' .
           '    <td align="right" class="' . $TotalStyle . '"><div id="handle_fee_id">' . $currencies->format(isset($_SESSION['orders_update_products'][$_GET['oID']]['code_fee']) ? $_SESSION['orders_update_products'][$_GET['oID']]['code_fee'] : $order->info["code_fee"]) .  '</div><input type="hidden" name="payment_code_fee" value="'.(isset($_SESSION['orders_update_products'][$_GET['oID']]['code_fee']) ? $_SESSION['orders_update_products'][$_GET['oID']]['code_fee'] : $order->info["code_fee"]).'">' . 
          '</td>' . 
          '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
          '  </tr>' . "\n"; 
    } else {
      $sign_str = '<select id="sign_'.$TotalIndex.'" name="sign_value_'.$TotalIndex.'" onchange="price_total(\''.TEXT_MONEY_SYMBOL.'\');orders_session(\'sign_'.$TotalIndex.'\',this.value);">';
      $sign_str .= '<option value="1"'.(isset($_SESSION['orders_update_products'][$_GET['oID']]['sign_'.$TotalIndex]) && $_SESSION['orders_update_products'][$_GET['oID']]['sign_'.$TotalIndex] == '1' ? ' selected="selected"': '').'>+</option>';
      $sign_str .= '<option value="0"'.(isset($_SESSION['orders_update_products'][$_GET['oID']]['sign_'.$TotalIndex]) && $_SESSION['orders_update_products'][$_GET['oID']]['sign_'.$TotalIndex] == '0' ? ' selected="selected"': $TotalDetails["Price"] < 0 ? ' selected="selected"': '').'>-</option>';
      $sign_str .= '</select>';
      $sum_num = isset($_SESSION['orders_update_products'][$_GET['oID']]['orders_totals']) ? $_SESSION['orders_update_products'][$_GET['oID']]['orders_totals'] : $sum_num;
      $show_num = isset($_SESSION['orders_update_products'][$_GET['oID']]['orders_totals']) ? $_SESSION['orders_update_products'][$_GET['oID']]['orders_totals'] : $show_num;
      $button_add = $TotalIndex == 1 ? '<INPUT type="button" id="button_add" value="'.TEXT_BUTTON_ADD.'" onclick="add_option(this);"><input type="hidden" id="button_add_id" value="'.$sum_num.'"><input type="hidden" id="text_len" value="'.$max_length.'">&nbsp;' : '';
      $TotalDetails["Price"] = isset($_SESSION['orders_update_products'][$_GET['oID']][$TotalIndex]['value']) ? $_SESSION['orders_update_products'][$_GET['oID']][$TotalIndex]['value'] : $TotalDetails["Price"];
      $TotalDetails["Name"] = isset($_SESSION['orders_update_products'][$_GET['oID']][$TotalIndex]['title']) ? $_SESSION['orders_update_products'][$_GET['oID']][$TotalIndex]['title'] : $TotalDetails["Name"];
      echo '  <tr>' . "\n" .
        '    <td align="left" class="' . $TotalStyle .  '">'.($TotalIndex == 1 ? EDIT_ORDERS_TOTALDETAIL_READ_ONE : '').'</td>' . 
        '    <td style="min-width:188px;" align="right" class="' . $TotalStyle . '">' . $button_add ."<input style='text-align:right;' name='update_totals[$TotalIndex][title]' onkeyup='price_total(\"".TEXT_MONEY_SYMBOL."\");' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>:" . '</td>' . "\n" .
        '    <td align="right" class="' . $TotalStyle . '">' . $sign_str ."<input style='text-align:right;' name='update_totals[$TotalIndex][value]' id='update_total_".$TotalIndex."' onkeyup='clearLibNum(this);price_total(\"".TEXT_MONEY_SYMBOL."\");' size='6' value='" . ($TotalDetails["Price"] != '' ? abs($TotalDetails["Price"]) : $TotalDetails["Price"]) . "'>" . TEXT_MONEY_SYMBOL .
        "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
        "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
        '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
        '   </td>' . "\n" .
        '  </tr>' . "\n";
    }
  }
  ?>
    </table>
    </td>
    </tr>
    <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <!-- End Order Total Block -->
    <!-- Begin Update Block -->
            <!-- End of Update Block -->
            <!-- Begin Status Block -->
            <tr>
            <td class="SubTitle"><?php echo EDIT_ORDERS_ITEM_FOUR_TITLE;?></td>
            </tr>
            <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
            </tr> 
            <tr>
            <td class="main">

            <table border="0" cellspacing="0" cellpadding="2" class="dataTableRow" width="100%">
            <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
            <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
            <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_CUSTOMER_NOTIFIED; ?></td>
            <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
            <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_STATUS; ?></td>
            <?php if($CommentsWithStatus) { ?>
            <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
            <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td>
            <?php } ?>
            <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
            <td class="dataTableHeadingContent" align="left"><?php echo TEXT_OPERATE_USER; ?></td>
            </tr>
                <?php
                $orders_history_query = tep_db_query("select * from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' order by date_added");
  if (tep_db_num_rows($orders_history_query)) {
    $orders_status_history_str = '';
    while ($orders_history = tep_db_fetch_array($orders_history_query)) {
      echo '  <tr>' . "\n" .
        '    <td class="smallText" align="left">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
        '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
        '    <td class="smallText" align="center">';
      if ($orders_history['customer_notified'] == '1') {
        echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";
      } else {
        echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";
      }
      echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
        '    <td class="smallText" align="left">' . $orders_status_array[$orders_history['orders_status_id']] . '</td>' . "\n";
      $orders_explode_array = array();
      $orders_explode_all_array = explode("\n",$orders_history['comments']);
      $orders_explode_array = explode(':',$orders_explode_all_array[0]);
      if(count($orders_explode_all_array) > 1){

       if(strlen(trim($orders_explode_array[1])) == 0){ 
         if(count($orders_explode_array) > 1){
           unset($orders_explode_all_array[0]);
         }
         $orders_history_comment = implode("\n",$orders_explode_all_array); 
       }else{ 
         $orders_temp_str = end($orders_explode_all_array);
         array_pop($orders_explode_all_array);
         $orders_comments_old_str = implode("\n",$orders_explode_all_array);
         if(trim($orders_comments_old_str) == trim($orders_status_history_str) && $orders_status_history_str != ''){

           $orders_history_comment = $orders_temp_str;
         }else{
           $orders_history_comment = $orders_history['comments']; 
         }
       }
      }else{
        $orders_history_comment = $orders_history['comments'];
      }
      if ($CommentsWithStatus && $orders_history['comments'] != $orders_status_history_str) {
        echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
          '    <td class="smallText" align="left">' . nl2br(tep_db_output($cpayment->admin_get_comment(payment::changeRomaji($order->info['payment_method'],PAYMENT_RETURN_TYPE_CODE),$orders_history_comment))) . '&nbsp;</td>' . "\n";
      } else {
        if ($CommentsWithStatus) {
          echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
            '    <td class="smallText" align="left">&nbsp;</td>' . "\n";
        }
      }
      echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
        '    <td class="smallText" align="left">' . $orders_history['user_added'] . '&nbsp;</td>' . "\n";
      echo '  </tr>' . "\n";
      $orders_status_history_str = $orders_history['comments'];
    }
  } else {
    echo '  <tr>' . "\n" .
      '    <td class="smallText" colspan="5">' . TEXT_NO_ORDER_HISTORY . '</td>' . "\n" .
      '  </tr>' . "\n";
  }
  ?>
    </table>

    </td>
    </tr>
    <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
    </tr>
    <tr>
    <td>  

    <table width="100%" border="0" cellspacing="0" cellpadding="2" class="dataTableRow">
    <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_STATUS; ?></td>
    <td class="main" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_EMAIL_COMMENTS; ?></td>
    </tr>
    <tr>
    <td valign="top" width="40%">
    <table border="0" cellspacing="0" cellpadding="2">
<?php    
          $order_status_num_query = tep_db_query("select * from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='". $oID ."'");
          $order_status_num = tep_db_num_rows($order_status_num_query);
          $order_status_query = tep_db_query("select * from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='". $oID ."' order by orders_status_history_id desc limit 0,1");            
          $order_status_array = tep_db_fetch_array($order_status_query);
          $select_status = $order_status_array['orders_status_id'];
          $customer_notified = $order_status_array['customer_notified'];           
          $customer_notified = isset($customer_notified) ? $customer_notified : true;
          $customer_notified = $select_status == 31 ? 0 : $customer_notified;
          $select_select = 16;
          $customer_notified = isset($_SESSION['orders_update_products'][$_GET['oID']]['notify']) ? $_SESSION['orders_update_products'][$_GET['oID']]['notify'] : $customer_notified;
          $select_select = isset($_SESSION['orders_update_products'][$_GET['oID']]['s_status']) ? $_SESSION['orders_update_products'][$_GET['oID']]['s_status'] : $select_select;
?>
    <tr>
    <td class="main" width="82" style="min-width:45px;"><?php echo ENTRY_STATUS; ?></td>
    <td class="main"><?php echo tep_draw_pull_down_menu('s_status', $orders_statuses, $select_select,'onChange="new_mail_text_orders(this, \'s_status\',\'comments\',\'title\')"; style="width:80px;" id="mail_title_status"'); ?></td> 
    </tr>
    <?php

            $ma_se = "select * from ".TABLE_ORDERS_MAIL." where ";
          if(!isset($_GET['status']) || $_GET['status'] == ""){
            $ma_se .= " orders_status_id = '".$select_select."' ";

            // 用来判断是否选中 送信&通知，如果nomail==1则不选中
            $ma_s = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$order->info['orders_status']."'"));
          }else{
            $ma_se .= " orders_status_id = '".$_GET['status']."' ";

            // 用来判断是否选中 送信&通知，如果nomail==1则不选中
            $ma_s = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$_GET['status']."'"));
          }
          $ma_se .= "and site_id='0'";
          $mail_sele = tep_db_query($ma_se);
          $mail_sql  = tep_db_fetch_array($mail_sele);
          $sta       = isset($_GET['status'])?$_GET['status']:'';
          $mail_sql['orders_status_title'] = isset($_SESSION['orders_update_products'][$_GET['oID']]['title']) ? $_SESSION['orders_update_products'][$_GET['oID']]['title'] : $mail_sql['orders_status_title'];
          $notify_comments_checked = isset($_SESSION['orders_update_products'][$_GET['oID']]['notify_comments']) ? $_SESSION['orders_update_products'][$_GET['oID']]['notify_comments'] == 1 ? true : false : false;
          ?>

            <tr>
            <td class="main"><?php echo ENTRY_EMAIL_TITLE; ?></td>
            <td class="main"><?php echo tep_draw_input_field('title', $mail_sql['orders_status_title'],'style="width:100%;" id="mail_title"'); ?></td>
            </tr>
    <tr>
    <td class="main"><?php echo EDIT_ORDERS_SEND_MAIL_TEXT;?></td>
    <td class="main"><table bgcolor="red" cellspacing="5"><tr><td><?php echo tep_draw_checkbox_field('notify', '', $customer_notified); ?></td></tr></table></td>
    </tr>
    <?php if($CommentsWithStatus) { ?>
      <tr>
        <td class="main"><?php echo EDIT_ORDERS_RECORD_TEXT;?></td>
        <td class="main"><?php echo tep_draw_checkbox_field('notify_comments', '', $notify_comments_checked); ?>&nbsp;&nbsp;<font style="color:#FF0000;"><?php echo EDIT_ORDERS_RECORD_READ;?></font></td>
        </tr>
      <tr>
        <td class="main" valign="top"><?php echo TABLE_HEADING_COMMENTS;?>:</td>
        <td class="main"><?php echo tep_draw_textarea_field('comments_text', 'hard', '74', '5', $_SESSION['orders_update_products'][$_GET['oID']]['comments_text'],'style=" font-family:monospace; font-size:12px; width:100%;"'); ?></td>
      </tr>
        <?php } ?>
        </table>
        </td>
        <td class="main" width="15%">&nbsp;</td>
        <td class="main">
        <?php echo EDIT_ORDERS_RECORD_ARTICLE;?><br>
        <?php
        $mail_sql['orders_status_mail'] = isset($_SESSION['orders_update_products'][$_GET['oID']]['comments']) ? $_SESSION['orders_update_products'][$_GET['oID']]['comments'] : $mail_sql['orders_status_mail'];
        if($CommentsWithStatus) {


          echo tep_draw_textarea_field('comments', 'hard', '74', '30', isset($order->info['comments'])?$order->info['comments']:str_replace('${ORDER_A}',orders_a($order->info['orders_id']),$mail_sql['orders_status_mail']),'style=" font-family:monospace; font-size:12px; width:400px;"');
        } else {
          echo tep_draw_textarea_field('comments', 'hard', '74', '30', isset($order->info['comments'])?$order->info['comments']:str_replace('${ORDER_A}',orders_a($order->info['orders_id']),$mail_sql['orders_status_mail']),'style=" font-family:monospace; font-size:12px; width:400px;"');
        }
  ?>
    </td>
    </tr>
    </table>
    </td>
    </tr>
    <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
    </tr>
    <!-- End of Status Block -->
    <!-- Begin Update Block -->
    <tr>
    <td class="SubTitle"><?php echo EDIT_ORDERS_ITEM_FIVE_TITLE;?></td>
    </tr>
    <tr>
    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
    </tr>   
    <tr>
    <td>
    <table width="100%" border="0" cellpadding="2" cellspacing="1">
    <tr>
    <td class="main" bgcolor="#FAEDDE"><?php echo EDIT_ORDERS_FINAL_CONFIRM_TEXT;?>&nbsp;<?php echo HINT_PRESS_UPDATE; ?></td>
    <td class="main" bgcolor="#FBE2C8" width="10">&nbsp;</td>
    <td class="main" bgcolor="#FFCC99" width="10">&nbsp;</td>
    <td class="main" bgcolor="#F8B061" width="10">&nbsp;</td>
    <td class="pageHeading" bgcolor="#FF9933" align="right">
    <?php
      foreach($orders_statuses as $o_status){
        echo '<input type="hidden" id="confrim_mail_title_'.$o_status['id'].
          '" value="'.$mo[$o_status['id']][0].'">';
      }
    ?>
    <INPUT type="button" class="element_button" value="<?php echo TEXT_FOOTER_CHECK_SAVE;?>" onClick="if(date_time()){if(products_num_check('<?php echo $orders_products_list;?>','<?php echo $products_name_str;?>','<?php echo $products_id_str;?>')){submit_check_con();}}">&nbsp;<?php echo '<a href="' . tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('clear_products'))) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td>
    </tr>
    </table>
    </td>
    </tr>
    <!-- End of Update Block -->
    <?php
}
if($action == "add_product")
{ 
    //添加商品
    $new_products_temp_list = array();
    $new_products_temp_add = array();
    $oID = $_GET['oID'];
    foreach($_SESSION['new_products_list_add'][$oID]['orders_products'] as $list_key=>$list_value){

      $new_products_list_attributes_array = array();
      foreach($list_value['products_attributes'] as $attr_key=>$attr_value){

        $attr_list_array = array();
        $attr_list_array = unserialize(stripcslashes($attr_value['option_info']));
        $new_products_list_attributes_array[] = array('id'=>'a_'.$attr_key,
                                                      'option_info'=>$attr_list_array, 
                                                      'option_group_id'=>$attr_value['option_group_id'],
                                                      'option_item_id'=>$attr_value['option_item_id'],
                                                      'price'=>$attr_value['options_values_price']
                                                      );
      }
      $new_products_temp_list = array('id'=>$list_value['products_id'],
                                      'qty'=>$list_value['products_quantity'],
                                      'name'=>$list_value['products_name'],
                                      'model'=>$list_value['products_model'],
                                      'tax'=>sprintf("%01.4f",$list_value['products_tax']),
                                      'price'=>sprintf("%01.4f",$list_value['products_price']),
                                      'final_price'=>sprintf("%01.4f",$list_value['final_price']),
                                      'orders_products_id'=>'o_'.$list_key,
                                      'attributes'=>$new_products_list_attributes_array 
                                    );
      $new_products_temp_add[] = $new_products_temp_list; 
    }
    $index_num = count($new_products_temp_add); 
//start
?>
<tr><td>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
</tr>
<tr>
<td class="pageHeading"><?php echo ADDING_TITLE; ?>:</td>
</tr>
</table>
</td></tr>
<tr><td>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
if($index_num > 0){
?>
<tr>
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
</tr>
<tr>
<td class="formAreaTitle"><?php echo ORDERS_PRODUCTS;?></td>
</tr>
<?php
}
?>
</table>
</td></tr>

<?php
if($index_num > 0){
?>
<tr>
  <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea">
  <tr>
  <td class="main"><input type="hidden" name="oID" value="<?php echo $oID;?>">
  
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr style="background-color: #e1f9fe;">
            <td class="dataTableContent" colspan="2" width="35%">&nbsp;<?php echo TABLE_HEADING_NUM_PRO_NAME;?></td>
            <td class="dataTableContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableContent"><?php echo TABLE_HEADING_CURRENICY;?></td>
            <td class="dataTableContent" align="center"><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_PRICE_BEFORE;?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_PRICE_AFTER;?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_TOTAL_BEFORE;?></td>
            <td class="dataTableContent" align="right"><?php echo TABLE_HEADING_TOTAL_AFTER;?></td>
            </tr>

          <?php
          for ($i=0; $i<sizeof($new_products_temp_add); $i++) {
            $orders_products_id = $new_products_temp_add[$i]['orders_products_id']; 
            $RowStyle = "dataTableContent";
            $porducts_qty = $new_products_temp_add[$i]['qty'];
            echo '<tr>' . "\n" .
                 '<td class="' . $RowStyle . '" align="left" valign="top" width="20">&nbsp;'
                 .$porducts_qty."&nbsp;x</td>\n" .  '<td class="' . $RowStyle . '">' . $new_products_temp_add[$i]['name'] . "\n"; 
            // Has Attributes?
            if (sizeof($new_products_temp_add[$i]['attributes']) > 0) { 
              for ($j=0; $j<sizeof($new_products_temp_add[$i]['attributes']); $j++) {
                $orders_products_attributes_id = $new_products_temp_add[$i]['attributes'][$j]['id'];
                echo '<div class="order_option_list"><small>&nbsp;<i><div
                  class="order_option_info"><div class="order_option_title"> - ' .str_replace(array("<br>", "<BR>"), '', tep_parse_input_field_data($new_products_temp_add[$i]['attributes'][$j]['option_info']['title'], array("'"=>"&quot;"))) . ': ' . 
                  '</div><div class="order_option_value">' . 
                  str_replace(array("<br>", "<BR>"), '', tep_parse_input_field_data($new_products_temp_add[$i]['attributes'][$j]['option_info']['value'], array("'"=>"&quot;"))); 
                echo '</div></div>';
                echo '<div class="order_option_price">';
                if ((int)$new_products_temp_add[$i]['attributes'][$j]['price'] < 0) {
                  echo '<font color="#ff0000">'.abs((int)$new_products_temp_add[$i]['attributes'][$j]['price']).'</font>'.TEXT_MONEY_SYMBOL;
                } else {
                  echo (int)$new_products_temp_add[$i]['attributes'][$j]['price'];
                  echo TEXT_MONEY_SYMBOL;
                }
                echo '</div>';
                echo '</i></small></div>';
              }
            }

                echo '</td>' . "\n" .
                     '<td class="' . $RowStyle . '">' . $new_products_temp_add[$i]['model'] . '</td>' . "\n" .
                     '<td class="' . $RowStyle . '" align="right">' . tep_display_tax_value($new_products_temp_add[$i]['tax']) . '%</td>' . "\n";
            if($new_products_temp_add[$i]['price'] < 0){
              $products_price_value = '<font color="#ff0000">'.$currencies->format(tep_display_currency(number_format(abs($new_products_temp_add[$i]['price']), 2))).'</font>';  
            }else{

              $products_price_value = $currencies->format(tep_display_currency(number_format(abs($new_products_temp_add[$i]['price']), 2)));
            }
            if($new_products_temp_add[$i]['final_price'] < 0){
              $products_tax_price_value = '<font color="#ff0000">'.$currencies->format(tep_display_currency(number_format(abs($new_products_temp_add[$i]['final_price']),2))).'</font>';  
            }else{

              $products_tax_price_value = $currencies->format(tep_display_currency(number_format(abs($new_products_temp_add[$i]['final_price']),2)));
            }
                echo '<td class="'.$RowStyle.'" align="right">'.str_replace(TEXT_MONEY_SYMBOL,'',$products_price_value).TEXT_MONEY_SYMBOL.'</td>'; 
                echo '<td class="' . $RowStyle . '" align="right">' .str_replace(TEXT_MONEY_SYMBOL,'',$products_tax_price_value)
                     .TEXT_MONEY_SYMBOL ."\n" . '</td>' . "\n" . 
                     '<td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][a_price]">';
            if ($new_products_temp_add[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($new_products_temp_add[$i]['final_price'], $new_products_temp_add[$i]['tax']), true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format(tep_add_tax($new_products_temp_add[$i]['final_price'], $new_products_temp_add[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']);
            }
            echo '</div></td>' . "\n" . 
              '<td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][b_price]">';
            if ($new_products_temp_add[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($new_products_temp_add[$i]['final_price'] * $new_products_temp_add[$i]['qty'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format($new_products_temp_add[$i]['final_price'] * $new_products_temp_add[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']);
            }
            echo '</div></td>' . "\n" . 
                 '<td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][c_price]">';
            if ($new_products_temp_add[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($new_products_temp_add[$i]['final_price'], $new_products_temp_add[$i]['tax']) * $new_products_temp_add[$i]['qty'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format(tep_add_tax($new_products_temp_add[$i]['final_price'], $new_products_temp_add[$i]['tax']) * $new_products_temp_add[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']);
            }
            echo '</div></td>' . "\n" . 
                 '</tr>' . "\n";
          }
          ?>
            </table>

            </td>
            </tr>     
</table>
</td>
</tr>
<?php
}
//end
?>
    <tr>
    <td class="formAreaTitle"><br><?php echo ADDING_TITLE; ?> (Nr. <?php echo $oID; ?>)</td>
    </tr>

    <?php 
    //   Get List of All Products

    $result = tep_db_query("
        SELECT products_name, 
        p.products_id, 
        cd.categories_name, 
        ptc.categories_id 
        FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id=p.products_id LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON ptc.products_id=p.products_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id=ptc.categories_id 
        where pd.language_id = '" . (int)$languages_id . "' 
        and cd.site_id = '0'
        and pd.site_id = '0'
        ORDER BY categories_name");
  while($row = tep_db_fetch_array($result))
  {
    extract($row,EXTR_PREFIX_ALL,"db");
    $ProductList[$db_categories_id][$db_products_id] = $db_products_name;
    $CategoryList[$db_categories_id] = $db_categories_name;
    $LastCategory = $db_categories_name;
  }

  // ksort($ProductList);

  $LastOptionTag = "";
  $ProductSelectOptions = "<option value='0'>Don't Add New Product" . $LastOptionTag . "\n";
  $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
  foreach($ProductList as $Category => $Products)
  {
    $ProductSelectOptions .= "<option value='0'>$Category" . $LastOptionTag . "\n";
    $ProductSelectOptions .= "<option value='0'>---------------------------" . $LastOptionTag . "\n";
    asort($Products);
    foreach($Products as $Product_ID => $Product_Name)
    {
      $ProductSelectOptions .= "<option value='$Product_ID'> &nbsp; $Product_Name" . $LastOptionTag . "\n";
    }

    if($Category != $LastCategory)
    {
      $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
      $ProductSelectOptions .= "<option value='0'>&nbsp;" . $LastOptionTag . "\n";
    }
  }


  //   Add Products Steps

  print "<tr><td><table border='0' width='100%' class='option_box_space' cellspacing='1' cellpadding='2'>\n";

  // Set Defaults
  if(!IsSet($add_product_categories_id))
    $add_product_categories_id = 0;

  if(!IsSet($add_product_products_id))
    $add_product_products_id = 0;

  // Step 1: Choose Category
  print "<tr>\n";
  print "<td class='dataTableContent' width='80'>&nbsp;" . ADDPRODUCT_TEXT_STEP . " 1:</td>\n";
  print "<td class='dataTableContent' valign='top'>";
  print "<form action='$PHP_SELF?oID=$oID&action=$action' method='POST'>";
  print "<table>";
  print '<tr>';
  print '<td width="150">';
  print ADDPRODUCT_TEXT_STEP1;
  print '</td>';
  print '<td>';
  echo ' ' . tep_draw_pull_down_menu('add_product_categories_id', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"');
  print "<input type='hidden' name='step' value='2'>";
  print '<td></tr>';
  print '</table>';
  print "</form>";
  print "</td>\n";
  print "<td class='dataTableContent'>";
  if(isset($_GET['add_error']) && $_GET['add_error'] == 1){
    print "&nbsp;&nbsp;&nbsp;<font color='#FF0000'>".ORDERS_PRODUCT_ERROR."</font>";
  }
  print "</td>\n";
  print "</tr>\n";

  // Step 2: Choose Product
  if(($step > 1) && ($add_product_categories_id > 0))
  {
    print "<tr>\n";
    print "<td class='dataTableContent'>&nbsp;" . ADDPRODUCT_TEXT_STEP . " 2: </td>\n";
    print "<td class='dataTableContent' valign='top'>";
    print "<form action='$PHP_SELF?oID=$oID&action=$action' method='POST'>";
    print "<table>";
    print "<tr><td width='150'>";
    print ADDPRODUCT_TEXT_STEP2."</td>";
    print "<td>";
    print "<select name=\"add_product_products_id\" onChange=\"this.form.submit();\">";
    $ProductOptions = "<option value='0'>" .  ADDPRODUCT_TEXT_SELECT_PRODUCT . "\n";
    asort($ProductList[$add_product_categories_id]);
    foreach($ProductList[$add_product_categories_id] as $ProductID => $ProductName)
    {
      $ProductOptions .= "<option value='$ProductID'> $ProductName\n";
    }
    $ProductOptions = str_replace("value='$add_product_products_id'","value='$add_product_products_id' selected", $ProductOptions);
    print $ProductOptions;
    print "</select>\n";
    print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
    print "<input type='hidden' name='step' value='3'>\n";
    print "<input type='hidden' name='cstep' value='1'>\n";
    print "</td>";
    print "</tr>";
    print "</table>";
    print "</form>";
    print "</td>\n";
    print "<td class='dataTableContent' align='right'>&nbsp;</td>\n";
    print "</tr>\n";
  }
  
  $hm_option = new HM_Option();
  
  if (($step == 3) && ($add_product_products_id > 0) && isset($_POST['action_process'])) {
    if (!$hm_option->check()) {
      $step = 4; 
    }
  }
  // Step 3: Choose Options
  if(($step > 2) && ($add_product_products_id > 0))
  {
    $option_product_raw = tep_db_query("select products_cflag, belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$add_product_products_id."'"); 
    $option_product = tep_db_fetch_array($option_product_raw); 
    // Skip to Step 4 if no Options
    if(!$hm_option->admin_whether_show($option_product['belong_to_option'], 0, $option_product['products_cflag']))
    {
      print "<tr>\n";
      print "<td class='dataTableContent'>&nbsp;" . ADDPRODUCT_TEXT_STEP . " 3: </td>\n";
      print "<td class='dataTableContent' valign='top' colspan='2'><i>" . ADDPRODUCT_TEXT_OPTIONS_NOTEXIST . "</i></td>\n";
      print "</tr>\n";
      $step = 4;
    }
    else
    {

      $p_cflag = tep_get_cflag_by_product_id($add_product_products_id);
      print "<tr>";
      print "<td class='option_title_space' valign='top'>&nbsp;" . ADDPRODUCT_TEXT_STEP . " 3: </td><td class='dataTableContent' valign='top'>";
      print "<div class=\"pro_option\">"; 
      print "<form name='aform' action='$PHP_SELF?oID=$oID&action=$action' method='POST'>\n";
         
      print $hm_option->render($option_product['belong_to_option'], false, 2, '', '', $p_cflag); 
      print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
      print "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
      print "<input type='hidden' name='step' value='3'>";
      print "<input type='hidden' name='action_process' value='1'>";
      
      print "</form>";
      print "</div>"; 
      print "</td>";
      print "</tr>\n";
    }

    print "<tr><td colspan='3' align='right'><input type='button' value='" . ADDPRODUCT_TEXT_OPTIONS_CONFIRM . "' onclick='document.forms.aform.submit();'></td></tr>\n";
  }

  // Step 4: Confirm
  if($step > 3)
  {
    print "<tr><form action='$PHP_SELF?oID=$oID&action=$action' method='POST'>\n";
    print "<td class='dataTableContent'>&nbsp;" . ADDPRODUCT_TEXT_STEP . " 4: </td>";
    print "<td class='dataTableContent'><table><tr><td width='150'>" .  ADDPRODUCT_TEXT_CONFIRM_QUANTITY . ":</td><td><input name='add_product_quantity' size='9' value='1' onkeyup='clearLibNum(this);' style='text-align:right;'>&nbsp;".EDIT_ORDERS_NUM_UNIT."&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table></td>";
    print "<td class='dataTableContent' align='right'><input type='submit' value='" . ADDPRODUCT_TEXT_CONFIRM_ADDNOW . "'>";

    foreach ($_POST as $op_key => $op_value)
    {
      $op_pos = substr($op_key, 0, 3); 
      if ($op_pos == 'op_') {
        print "<input type='hidden' name='".$op_key."' value='".tep_parse_input_field_data(stripslashes($op_value), array("'" => "&quot;"))."'>";
      } 
    }
    print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
    print "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
    print "<input type='hidden' name='step' value='5'>";
    print "</td>\n";
    print "</form></tr>\n";
  }

  print "</table></td></tr>\n";
  echo '<tr><td><br>';
  echo '<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr><td class="main" align="left">';
  echo '<a href="' .  tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params(array('action','step')).'action=edit&clear_products=0') . '">' . tep_html_element_button(IMAGE_BACK) . '</a></td>'; 
  echo '<td class="main" align="right">';
  $url_action_array = $index_num > 0 ? array('action','step') : array(''); 
  $url_action = $index_num > 0 ? 'action=edit&clear_products=1' : 'add_error=1';
  echo '<a href="' .  tep_href_link(FILENAME_ORDERS_EDIT, tep_get_all_get_params($url_action_array).$url_action) .  '">'.tep_html_element_button(IMAGE_CONFIRM_NEXT).'</a>';
  echo '</td></tr>';
  echo '</table>';
  echo '</td></tr>';
}  
?>
</table>
</div>
</div>
</td>
<!-- body_text_eof //-->
</tr>
</table>
<?php
if($action != "add_product"){
?>
</form>
<?php
}
?>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php
require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
