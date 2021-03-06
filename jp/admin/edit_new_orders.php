<?php
/*
   $Id$

   创建订单
 */

require('includes/application_top.php');
require('includes/step-by-step/new_application_top.php');
ini_set("display_errors","Off");
include(DIR_FS_ADMIN . DIR_WS_LANGUAGES .  '/default.php');
include(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/' . FILENAME_EDIT_ORDERS);
require(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_EDIT_ORDERS);
require(DIR_WS_LANGUAGES . $language . '/javascript/'. FILENAME_ALL_ORDERS);
if(!isset($_SESSION['sites_id_flag']) || !isset($_SESSION['customer_id']) || !isset($_SESSION['email_address']) || !isset($_SESSION['firstname']) || !isset($_SESSION['lastname'])){
  tep_redirect(tep_redirect(tep_href_link(FILENAME_CREATE_ORDER, null, 'SSL')));
}

require(DIR_WS_CLASSES . 'currencies.php');
$payment_info_array = array();
$currencies = new currencies(2);
$oID = tep_db_input($_GET['oID']);
$update_op_flag = false;
$oID = tep_is_has_order($oID);
if($oID != tep_db_input($_GET['oID'])){
  $update_op_flag = true;
}
$_GET['oID'] = $oID;
$orders_oid_query = tep_db_query("select orders_id from ". TABLE_ORDERS ." where orders_id='".$oID."'");
$ordres_oid_num_rows = tep_db_num_rows($orders_oid_query);
tep_db_free_result($orders_oid_query);
$orders_exit_flag = false;
if($ordres_oid_num_rows > 0){

  $orders_exit_flag = true;
}
include(DIR_WS_CLASSES . 'order.php');
require_once('includes/address/AD_Option.php');
require_once('includes/address/AD_Option_Group.php');
$ad_option = new AD_Option();
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
    where language_id = '" . (int)$languages_id . "'");
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
if($orders_exit_flag == true){
  $order = new order($oID);
}
// 获取返点
// 获取客户信息
$customer_id_flag = $orders_exit_flag == true ? $order->customer['id'] : $_SESSION['customer_id'];
$customer_point_query = tep_db_query("
    select point,is_quited 
    from " . TABLE_CUSTOMERS . " 
    where customers_id = '" . $customer_id_flag . "'");
$customer_point = tep_db_fetch_array($customer_point_query);
// 游客检查
// 获取客户 是否为注册用户
$customer_guest_query = tep_db_query("
    select customers_guest_chk, is_send_mail, is_calc_quantity  
    from " . TABLE_CUSTOMERS . " 
    where customers_id = '" . $customer_id_flag . "'");
$customer_guest = tep_db_fetch_array($customer_guest_query);
$site_id_flag = $orders_exit_flag == true ? $order->info['site_id'] : $_SESSION['sites_id_flag'];
if (tep_not_null($action)) {

  $payment_modules = payment::getInstance($site_id_flag);
  switch ($action) {
/* -----------------------------------------------------
   case 'update_order' 创建订单  
------------------------------------------------------*/
    // 1. UPDATE ORDER ###############################################################################################
  case 'update_order':
    //订单状态更新
    if(!isset($_POST['payment_method']) ||$_POST['payment_method']=='' ||!$_POST['payment_method']){
      $_SESSION['payment_empty_error'] = TEXT_NO_PAYMENT_ENABLED;
      tep_redirect(tep_href_link("edit_new_orders.php", tep_get_all_get_params(array('action')) . 'action=edit'));
    }else{
      $payment_method = tep_db_prepare_input($_POST['payment_method']); 
      $payment_continue = tep_get_payment_flag( $payment_method,$_SESSION['customer_id'],$_SESSION['sites_id_flag']);
      if(!$payment_continue){
        $_SESSION['payment_empty_error'] = TEXT_SELECT_PAYMENT_ERROR;
        tep_redirect(tep_href_link("edit_new_orders.php", tep_get_all_get_params(array('action')) . 'action=edit'));
      }
    }
    $update_user_info = tep_get_user_info($ocertify->auth_user);
    $oID      = tep_db_prepare_input($_GET['oID']);
    $status   = tep_db_prepare_input($_POST['s_status']);
    $title    = tep_db_prepare_input($_POST['title']);
    $comments = stripslashes(tep_db_input($_POST['comments']));
    $comments_text = tep_db_input($_POST['comments_text']);
    $comment_arr = $payment_modules->dealComment($payment_method,$comments_text);    
     
    $error = false;

    $options_comment = array();
    $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type='textarea' and status='0' order by sort");
    while($address_required = tep_db_fetch_array($address_query)){
    
      $options_comment[$address_required['name_flag']] = $address_required['comment'];
    }
    tep_db_free_result($address_query); 
      
    //过滤提示字符串
    foreach ($_POST as $p_key => $p_value) {
      $op_single_str = substr($p_key, 0, 3);
      if ($op_single_str == 'ad_') {
        $_POST[$p_key] = tep_db_prepare_input($p_value);
        if($options_comment[substr($p_key,3)] == $p_value){

          $_POST[$p_key] = '';
        }
      } 
    }
    $options_info_array = array(); 
      if (!$ad_option->check()) {
        foreach ($_POST as $p_key => $p_value) {
          $op_single_str = substr($p_key, 0, 3);
          if ($op_single_str == 'ad_') {
            $options_info_array[$p_key] = $p_value; 
          } 
        }
      }else{
        $address_style = 'display: table;';
        $error = true;
      }
 
      $payment_method_romaji = payment::changeRomaji($payment_method,PAYMENT_RETURN_TYPE_CODE);
      $validateModule = $payment_modules->admin_confirmation_check($payment_method);

      if ($validateModule['validated']===false || $error == true){

        $selections = $payment_modules->admin_selection();
        $selections[strtoupper($payment_method)] = $validateModule;
        $action = 'edit';
        break;
      }
     
      $viladate = tep_db_input($_POST['update_viladate']);//viladate pwd 
      if($viladate!='_false'&&$viladate!=''){
        tep_insert_pwd_log($viladate,$ocertify->auth_user);
        $viladate = true;
      }else if($viladate=='_false'){
        $viladate = false;
        $messageStack->add_session(TEXT_CANCEL_UPDATE, 'error');
        tep_redirect(tep_href_link("edit_new_orders.php", tep_get_all_get_params(array('action')) . 'action=edit'));
        break;
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
      if((int)$date_time < (int)$date_now || (int)$date_end_hour < (int)$date_start_hour){

        $messageStack->add(TEXT_DATE_NUM_ERROR, 'error');
        $action = 'edit';
        break; 
      }
    //创建订单
    $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
    if (!tep_db_num_rows($orders_query)) {
      $currency_text  = DEFAULT_CURRENCY . ",1";


     //开始生成订单
     $currency_array = explode(",", $currency_text);
     $currency = $currency_array[0];
     $currency_value = $currency_array[1];
     $insert_id = $oID;
     $sql_data_array = array('orders_id'              => $insert_id,
			'customers_id'                => $_SESSION['customer_id'],	
			'date_purchased'              => 'now()', 
			'orders_status'               => DEFAULT_ORDERS_STATUS_ID,
			'currency'                    => $currency,
			'currency_value'              => $currency_value,
			'orders_wait_flag'            => '1',
                        'user_added'                  => $_SESSION['user_name']
			); 
     //创建订单
     tep_db_perform(TABLE_ORDERS, $sql_data_array);

    //开始更新订单

    $sql_data_array = array('customers_id'            => $_SESSION['customer_id'],
			'customers_name'              => tep_get_fullname($_SESSION['firstname'],$_SESSION['lastname']),
			'customers_company'           => $_SESSION['company'],
			'customers_street_address'    => $_SESSION['street_address'],
			'customers_suburb'            => $_SESSION['suburb'],
			'customers_city'              => $_SESSION['city'],
			'customers_postcode'          => $_SESSION['postcode'],
			'customers_state'             => $_SESSION['state'],
			'customers_country'           => $_SESSION['country'],
			'customers_telephone'         => $_SESSION['telephone'],
			'customers_email_address'     => $_SESSION['email_address'],
			'customers_address_format_id' => $_SESSION['format_id'],
			'delivery_company'            => $_SESSION['company'],
			'delivery_street_address'     => $_SESSION['street_address'],
			'delivery_suburb'             => $_SESSION['suburb'],
			'delivery_city'               => $_SESSION['city'],
			'delivery_postcode'           => $_SESSION['postcode'],
			'delivery_state'              => $_SESSION['state'],
			'delivery_country'            => $_SESSION['country'],
			'delivery_address_format_id'  => $_SESSION['format_id'],
			'billing_name'                => tep_get_fullname($_SESSION['firstname'],$_SESSION['lastname']),
			'billing_company'             => $_SESSION['company'],
			'billing_street_address'      => $_SESSION['street_address'],
			'billing_suburb'              => $_SESSION['suburb'],
			'billing_city'                => $_SESSION['city'],
			'billing_postcode'            => $_SESSION['postcode'],
			'billing_state'               => $_SESSION['state'],
			'billing_country'             => $_SESSION['country'],
			'billing_address_format_id'   => $_SESSION['format_id'],
			'orders_status'               => DEFAULT_ORDERS_STATUS_ID,
			'site_id'                     => $_SESSION['sites_id_flag'],
			'orders_wait_flag'            => '1'
			); 
       //更新订单
       tep_db_perform(TABLE_ORDERS, $sql_data_array,'update','orders_id=\''.$oID.'\'');
       last_customer_action();
       orders_updated($insert_id);
       $orders_type_str = tep_get_order_type_info($oID);
       tep_db_query("update `".TABLE_ORDERS."` set `orders_type` = '".$orders_type_str."' where orders_id = '".tep_db_input($oID)."'");
 
    }
    tep_db_free_result($orders_query);
  
    $site_id  = tep_get_site_id_by_orders_id($oID);

if($orders_exit_flag == true){
    $orders_email_query = tep_db_query("select payment_method from ". TABLE_ORDERS ." where orders_id='".$oID."'");
    $orders_email_array = tep_db_fetch_array($orders_email_query);
    tep_db_free_result($orders_email_query);    

    $order_updated = false;
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
}else{
    $check_status['orders_id'] = $oID;
    $check_status['customers_name'] = tep_get_fullname($_SESSION['firstname'],$_SESSION['lastname']);
    $check_status['customers_id'] = $customer_id_flag;
    $check_status['customers_email_address'] = $_SESSION['email_address'];
    $check_status['orders_status'] = 1;
    $check_status['date_purchased'] = '';
    $check_status['payment_method'] = payment::changeRomaji(tep_db_input($_POST['payment_method']),'title' );
    $check_status['torihiki_date'] = '';
    $check_status['torihiki_date_end'] = '';
}
    //oa start 如果状态发生改变，找到当前的订单的
    tep_order_status_change($oID,$status);

    //计算配送料    
      $products_weight_total = 0; //商品总重量
      $products_money_total = 0; //商品总价
      $cart_shipping_time = array(); //商品配送时间
      foreach($update_products as $update_key=>$update_value){
        
        if($update_op_flag){
          tep_db_query("update ".TABLE_ORDERS_PRODUCTS." SET orders_id ='".$oID."' where
              where orders_products_id='". $update_key ."'");
        }
        $update_weight_query = tep_db_query("select products_id,final_price from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='". $update_key ."'");
        $update_weight_array = tep_db_fetch_array($update_weight_query);
        tep_db_free_result($update_weight_query);
        $products_weight_query = tep_db_query("select * from ". TABLE_PRODUCTS ." where products_id='". $update_weight_array['products_id'] ."'");
        $products_weight_array = tep_db_fetch_array($products_weight_query);
        $cart_shipping_time[] = $products_weight_array['products_shipping_time'];
        $products_weight_total += $products_weight_array['products_weight']*$update_value['qty'];
        $update_value['final_price'] = $update_weight_array['final_price'] < 0 ? -$update_value['final_price'] : $update_value['final_price'];
        $products_money_total += $update_value['final_price']*$update_value['qty'];
        tep_db_free_result($products_weight_query);
      }
      // start
      //计算配送费用 
    $country_fee_array = array();
    $country_fee_id_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
    while($country_fee_id_array = tep_db_fetch_array($country_fee_id_query)){

      $country_fee_array[$country_fee_id_array['fixed_option']] = $country_fee_id_array['name_flag'];
    }
    tep_db_free_result($country_fee_id_query);
    $weight = $products_weight_total;
    
    foreach($options_info_array  as $op_key=>$op_value){
     if($op_key == 'ad_'.$country_fee_array[3]){
       $city_query = tep_db_query("select * from ". TABLE_COUNTRY_CITY ." where name='". $op_value ."' and status='0'");
       $city_num = tep_db_num_rows($city_query); 
     }

     if($op_key == 'ad_'.$country_fee_array[2]){ 
       $address_query = tep_db_query("select * from ". TABLE_COUNTRY_AREA ." where name='". $op_value ."' and status='0'");
       $address_num = tep_db_num_rows($address_query);
     }

     if($op_key == 'ad_'.$country_fee_array[1]){ 
       $country_query = tep_db_query("select * from ". TABLE_COUNTRY_FEE ." where name='". $op_value ."' and status='0'");
       $address_country_num = tep_db_num_rows($country_query);
     }

    if($city_num > 0 && $op_key == 'ad_'.$country_fee_array[3]){
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
    }elseif($address_num > 0 && $op_key == 'ad_'.$country_fee_array[2]){
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
    if($address_country_num > 0 && $op_key == 'ad_'.$country_fee_array[1]){
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

  $shipping_money_total = $products_money_total;
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

    $shipping_fee = $shipping_money_total > $free_value ? 0 : $weight_fee;
    $shipping_fee = $products_weight_total == 0 ? 0 : $shipping_fee;  
    // end 
      
      //更新订单

      $oID = tep_db_prepare_input($_GET['oID']);
      $order = new order($oID);
      $goods_check = $order_query;
            
      // 1.1 UPDATE ORDER INFO #####
      $UpdateOrders = "update " . TABLE_ORDERS . " set 
        customers_name = '" . tep_db_input($update_customer_name) . "',
                       customers_name_f = '" . tep_db_input(stripslashes($update_customer_name_f)) . "',
                       customers_company = '" . tep_db_input(stripslashes($update_customer_company)) . "',
                       customers_street_address = '" . tep_db_input(stripslashes($update_customer_street_address)) . "',
                       customers_suburb = '" . tep_db_input(stripslashes($update_customer_suburb)) . "',
                       customers_city = '" . tep_db_input(stripslashes($update_customer_city)) . "',
                       customers_state = '" . tep_db_input(stripslashes($update_customer_state)) . "',
                       customers_postcode = '" . tep_db_input($update_customer_postcode) . "',
                       customers_country = '" . tep_db_input(stripslashes($update_customer_country)) . "',
                       customers_telephone = '" . tep_db_input($update_customer_telephone) . "',
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
        payment_method = '" . payment::changeRomaji(tep_db_input($_POST['payment_method']),'title' ). "',
        torihiki_houhou = '" . tep_db_input($update_tori_torihiki_houhou) . "',
        torihiki_date = '" . tep_db_input($_POST['date_orders'].' '.$_POST['start_hour'].':'.$_POST['start_min'].$_POST['start_min_1'].':00') . "',
        torihiki_date_end = '" . tep_db_input($_POST['date_orders'].' '.$_POST['end_hour'].':'.$_POST['end_min'].$_POST['end_min_1'].':00') . "',
        shipping_fee = '" . tep_db_input($shipping_fee) . "',
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

      //作所信息入库开始
      $address_num_query = tep_db_query("select count(*) as count_num from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and billing_address='0'"); 
      $address_num_array = tep_db_fetch_array($address_num_query);

      tep_db_query("delete from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and customers_id='".$check_status['customers_id']."' and billing_address='0'");
      
      foreach($options_info_array as $op_key=>$op_value){
  
        $address_options_query = tep_db_query("select * from ". TABLE_ADDRESS ." where name_flag='". substr($op_key,3) ."'");
        $address_options_array = tep_db_fetch_array($address_options_query);
        tep_db_free_result($address_options_query);
        $op_value = $op_value == $address_options_array['comment'] ? '' : $op_value;
        $address_query = tep_db_query("insert into ". TABLE_ADDRESS_ORDERS ." values(NULL,'$oID',{$check_status['customers_id']},{$address_options_array['id']},'{$address_options_array['name_flag']}','".addslashes($op_value)."','0')");
        tep_db_free_result($address_query);
      }

  $address_show_array = array(); 
  $address_show_list_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_show_list_array = tep_db_fetch_array($address_show_list_query)){

    $address_show_array[$address_show_list_array['id']] = $address_show_list_array['name_flag'];
  }
  tep_db_free_result($address_show_list_query);
  $address_temp_str = '';
  foreach($options_info_array as $address_his_key=>$address_his_value){
    
      if(in_array(substr($address_his_key,3),$address_show_array)){

         $address_temp_str .= $address_his_value;
      }
  }
  
  $address_error = false;
  $orders_id_temp = '';
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
      $orders_id_temp = $address_sh_his_array['orders_id'];
      break;
    }
    tep_db_free_result($address_sh_query);
  }
  tep_db_free_result($address_sh_his_query);
  //update address info
  if($address_error == true && $orders_id_temp != ''){

    tep_db_query("update ". TABLE_ADDRESS_HISTORY ." set orders_id='".$oID."' where orders_id='".$orders_id_temp."'"); 
  }
if($address_error == false && $customer_guest['customers_guest_chk'] == '0'){
  $address_history_search_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where orders_id='".$oID."'");
  $address_history_num_rows = tep_db_num_rows($address_history_search_query);
  tep_db_free_result($address_history_search_query);

  if($address_history_num_rows > 0){
    $orders_id_flag = date("Ymd") . '-' . date("His") . tep_get_order_end_num();
  }else{
    $orders_id_flag = $oID;
  }
  foreach($options_info_array as $address_history_key=>$address_history_value){
      $address_history_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where name_flag='". substr($address_history_key,3) ."'");
      $address_history_array = tep_db_fetch_array($address_history_query);
      tep_db_free_result($address_history_query);
      $address_history_id = $address_history_array['id'];
      $address_history_add_query = tep_db_query("insert into ". TABLE_ADDRESS_HISTORY ." values(NULL,'$orders_id_flag',{$check_status['customers_id']},$address_history_id,'{$address_history_array['name_flag']}','".addslashes($address_history_value)."','0')");
      tep_db_free_result($address_history_add_query);
  }
}
  //获取是否开启了帐单邮寄地址功能
  $billing_address_show = get_configuration_by_site_id('BILLING_ADDRESS_SETTING',$_SESSION['sites_id_flag']);
  $billing_address_show = $billing_address_show == '' ? get_configuration_by_site_id('BILLING_ADDRESS_SETTING',0) : $billing_address_show; 
  if($billing_address_show == 'true'){
    //把帐单邮寄地址的数据存入数据库
    $billing_address_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where customers_id='".$check_status['customers_id']."' and billing_address='1' order by id");
    if(tep_db_num_rows($billing_address_query) > 1){
      while($billing_address_array = tep_db_fetch_array($billing_address_query)){
        $address_query = tep_db_query("insert into ". TABLE_ADDRESS_ORDERS ." values(NULL,'$oID',{$check_status['customers_id']},{$billing_address_array['address_id']},'".$billing_address_array['name']."','".addslashes($billing_address_array['value'])."','1')");
        tep_db_free_result($address_query); 
      }
      tep_db_free_result($billing_address_query);
    }
  }

     //作所信息入库结束

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
      $products_delete = false;
      $is_history = false;
      $exists_history_raw = tep_db_query("select * from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id = '".tep_db_input($oID)."'");
      if (tep_db_num_rows($is_history)) {
        $is_history = true; 
      }
      foreach ($update_products as $orders_products_id => $products_details) {
        // 1.3.1.1 Update Inventory Quantity
        $op_query = tep_db_query("
            select products_id, 
            products_quantity
            from " . TABLE_ORDERS_PRODUCTS . " 
            where orders_id = '" . tep_db_input($oID) . "'
            and orders_products_id='".$orders_products_id."'
            ");
        $order = tep_db_fetch_array($op_query);
        if (!$is_history) {
          $tmp_quantity = $products_details["qty"]; 
          $p = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$order['products_id']."'"));
          $radices = tep_get_radices($order['products_id']);
          $pr_quantity = $p['products_real_quantity'];
          $pv_quantity = $p['products_virtual_quantity'];
            
          if ($pr_quantity - $tmp_quantity*$radices < 0) {
            $pr_quantity = 0;
            $pv_quantity += ($pr_quantity - $tmp_quantity);
          } else {
            $pr_quantity -= $tmp_quantity*$radices;
          } 
            if($customer_guest['is_calc_quantity'] != '1') {
              tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = ".$pr_quantity.", products_virtual_quantity = ".$pv_quantity." where products_id = '" . (int)$order['products_id'] . "'");
            }
            tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = 0 where products_real_quantity < 0 and products_id = '" . (int)$order['products_id'] . "'");
            tep_db_query("update " . TABLE_PRODUCTS . " set products_virtual_quantity = 0 where products_virtual_quantity < 0 and products_id = '" . (int)$order['products_id'] . "'");
        } else {
          if ($products_details["qty"] != $order['products_quantity'] ) {
            $quantity_difference = ($products_details["qty"] - $order['products_quantity']);
            $p = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$order['products_id']."'"));
            $pr_quantity = $p['products_real_quantity'];
            $pv_quantity = $p['products_virtual_quantity'];
            // 增加库存
            if($quantity_difference < 0){
              if ($_POST['update_products_real_quantity'][$orders_products_id]) {
                // 增加实数
                $pr_quantity = $pr_quantity - $quantity_difference;
              } else {
                // 增加虚拟库存
                $pv_quantity = $pv_quantity - $quantity_difference;
              }
              // 减少库存
            } else {
              // 实数卖空
              if ($pr_quantity - $quantity_difference < 0) {
                $pr_quantity = 0;
                $pv_quantity += ($pr_quantity - $quantity_difference);
              } else {
                $pr_quantity -= $quantity_difference;
              }
            }
            // 如果是业者，不更新
              if($customer_guest['is_calc_quantity'] != '1') {
                tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = ".$pr_quantity.", products_virtual_quantity = ".$pv_quantity." where products_id = '" . (int)$order['products_id'] . "'");
              } 
              tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = 0 where products_real_quantity < 0 and products_id = '" . (int)$order['products_id'] . "'");
              tep_db_query("update " . TABLE_PRODUCTS . " set products_virtual_quantity = 0 where products_virtual_quantity < 0 and products_id = '" . (int)$order['products_id'] . "'");
          }
        }
        

        if($products_details["qty"] > 0) { 
          // a.) quantity found --> add to list & sum    
          $Query = "update " . TABLE_ORDERS_PRODUCTS . " set
            products_model = '" . $products_details["model"] . "',
            products_name = '" . str_replace("'", "&#39;", $products_details["name"]) . "',
            products_price = '" .  (tep_check_product_type($orders_products_id) ? 0 - $products_details["p_price"] : $products_details["p_price"]) . "',
                           final_price = '" . (tep_get_bflag_by_product_id((int)$order['products_id']) ? 0 - $products_details["final_price"] : $products_details["final_price"]) . "',
                           products_tax = '" . $products_details["tax"] . "',
                           products_quantity = '" . $products_details["qty"] . "'
                             where orders_products_id = '$orders_products_id';";
          tep_db_query($Query);

          $RunningSubTotal += $products_details["qty"] * $products_details["final_price"]; // version WITHOUT tax
          $RunningTax += (($products_details["tax"]/100) * ($products_details["qty"] * $products_details["final_price"]));

          // Update Any Attributes
          if (IsSet($products_details[attributes])) {
            foreach ($products_details["attributes"] as $orders_products_attributes_id => $attributes_details) {
              $input_option = array('title' => $attributes_details['option'], 'value'=> $attributes_details['value']); 
              $Query = "update " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set option_info = '" .tep_db_input(serialize($input_option)) . "',options_values_price = '".$attributes_details['price']."' where orders_products_attributes_id = '$orders_products_attributes_id';";
              tep_db_query($Query);
            }
          }
        }else{ 
          // b.) null quantity found --> delete
          $Query = "delete from " . TABLE_ORDERS_PRODUCTS . " where orders_products_id
            = '$orders_products_id';";
          tep_db_query($Query);
          $Query = "delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where
            orders_products_id = '$orders_products_id';";
          tep_db_query($Query);
          $products_delete = true;
        }
      }
 
        $orders_type_str = tep_get_order_type_info($oID);
        tep_db_query("update `".TABLE_ORDERS."` set `orders_type` = '".$orders_type_str."' where orders_id = '".tep_db_input($oID)."'"); 

      // 1.4. UPDATE SHIPPING, DISCOUNT & CUSTOM TAXES #####

      foreach($update_totals as $total_index => $total_details) {
        extract($total_details,EXTR_PREFIX_ALL,"ot");

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

          // Set $ot_text (display-formatted value)


          $order = new order($oID);
          if ($customer_guest['customers_guest_chk'] == 0 && $ot_class == "ot_point" && $ot_value != $before_point) { 
            //如果是会员的话进行返点的计算
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
            // Already in database --> Update
            //delete from query 
            $Query = 'UPDATE ' . TABLE_ORDERS_TOTAL . ' SET
              title = "' . $ot_title . '",
                    value = "' . tep_insert_currency_value($ot_value) . '",
                    sort_order = "' . $sort_order . '"
                      WHERE orders_total_id = "' . $ot_total_id . '"';
            tep_db_query($Query);
          } else { 
            // New Insert
            //change text to "" 
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

      //tax
      $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_ORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
      $plustax = tep_db_fetch_array($plustax_query);
      if($plustax['cnt'] > 0) {
        tep_db_query("update " . TABLE_ORDERS_TOTAL . " set value = '".tep_insert_currency_value($new_tax)."' where class='ot_tax' and orders_id = '".$oID."'");
      }

      //point修正中
      //返点 
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
      if ($newtotal > 0) {
        $newtotal -= $total_point["total_point"];
      }

      $handle_fee = $payment_modules->handle_calc_fee(
          payment::changeRomaji($_POST['payment_method'],PAYMENT_RETURN_TYPE_CODE), $newtotal);

      $newtotal = $newtotal+$handle_fee+$shipping_fee;

      $totals = "update " . TABLE_ORDERS_TOTAL . " set value = '" . intval(floor($newtotal)) . "' where class='ot_total' and orders_id = '" . $oID . "'";
      tep_db_query($totals);

      $update_orders_sql = "update ".TABLE_ORDERS." set code_fee = '".$handle_fee."' where orders_id = '".$oID."'";
      tep_db_query($update_orders_sql);

      // 最终处理（更新并发信）
      if ($products_delete == false) {
        tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "',user_update='".$_SESSION['user_name']."', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
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
            $search_products_id_list[] =$order->products[$i]['id'];
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
                $products_ordered_mail .= $order->products[$i]['attributes'][$j]['option_info']['title'] .str_repeat('　', intval($max_c_len - mb_strlen($order->products[$i]['attributes'][$j]['option_info']['title'], 'utf-8'))) .'：';
                $products_ordered_mail .= str_replace(array("<br>", "<BR>", "\r", "\n", "\r\n"), "", $order->products[$i]['attributes'][$j]['option_info']['value']);
                
                if ($order->products[$i]['attributes'][$j]['price'] != '0') {
                  $products_ordered_mail .= '（'.$currencies->format($order->products[$i]['attributes'][$j]['price']).'）'; 
                }
                $products_ordered_mail .= "\n"; 
              }
            }
            $_product_info_query = tep_db_query("
                select p.products_id, 
                pd.products_name, 
                p.products_attention_5,
                pd.products_description, 
                p.products_model, 
                p.products_real_quantity + p.products_virtual_quantity as products_quantity,
                pd.products_url, 
                p.products_price, 
                p.products_tax_class_id, 
                p.products_date_added, 
                p.products_date_available, 
                p.manufacturers_id, 
                p.products_bflag, 
                p.products_cflag, 
                p.products_small_sum 
                  from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
                  where 
                  -- p.products_status != '0' and 
                  p.products_id = '" . $order->products[$i]['id'] . "' 
                  and pd.products_id = p.products_id 
                  and pd.site_id = '0'
                  and pd.language_id = '" . $languages_id . "'");
            $product_info = tep_db_fetch_array($_product_info_query);

            $pcount_email = '';
            if(isset($order->products[$i]['rate'])
              &&$order->products[$i]['rate']!=1
              &&$order->products[$i]['rate']!=0){
              $pcount_email = ' ('.number_format($order->products[$i]['qty']*$order->products[$i]['rate']).')';
            }
            $products_ordered_mail .= SENDMAIL_QTY_NUM.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_QTY_NUM, 'utf-8'))).'：' .  $order->products[$i]['qty'] . SENDMAIL_EDIT_ORDERS_NUM_UNIT .  $pcount_email . "\n";
            $products_ordered_mail .= SENDMAIL_TABLE_HEADING_PRODUCTS_PRICE.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_TABLE_HEADING_PRODUCTS_PRICE, 'utf-8'))).'：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax']) . "\n";
            $products_ordered_mail .= SENDMAIL_ENTRY_SUB_TOTAL.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_ENTRY_SUB_TOTAL, 'utf-8'))).'：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . "\n";
            $products_ordered_mail .= "------------------------------------------\n";
            if (tep_get_cflag_by_product_id($order->products[$i]['id'])) {
              if (tep_get_bflag_by_product_id($order->products[$i]['id'])) {
                $products_ordered_mail .= SENDMAIL_TEXT_CHARACTER_NAME_SEND_MAIL."\n\n";
              } else {
                $products_ordered_mail .= SENDMAIL_TEXT_CHARACTER_NAME_CONFIRM_SEND_MAIL."\n\n";
              }
            }
          }

          $total_details_mail = '';
          $totals_query = tep_db_query("select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . tep_db_input($oID) . "' order by sort_order");
          $order->totals = array();

          while ($totals = tep_db_fetch_array($totals_query)) {

            if ($totals['class'] == "ot_point" || $totals['class'] == "ot_subtotal") {
              if ((int)$totals['value'] >= 1 && $totals['class'] != "ot_subtotal") {
                $total_details_mail .= SENDMAIL_TEXT_POINT_ONE . $currencies->format($totals['value']) . "\n";
                $mailpoint = str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format($totals['value']));
              }
            } elseif ($totals['class'] == "ot_total") {
              if($handle_fee) {
                $total_details_mail .= SENDMAIL_TEXT_HANDLE_FEE_ONE.$currencies->format($handle_fee)."\n";
              }
              $total_details_mail .= SENDMAIL_TEXT_PAYMENT_AMOUNT_ONE . $currencies->format($totals['value']) . "\n";
              $mailtotal = $totals['value'];
              $total_price_mail = round($totals['value']);
            } else {
              $total_details_mail .= '▼' . $totals['title'] . str_repeat('　', intval((16 -
                strlen($totals['title']))/2)) . '：' . $currencies->format($totals['value']) . "\n";
            }
          }
          $totals_email_i = 0;
          foreach($update_totals as $update_total_key=>$update_total_value){

              if($update_total_value['class'] == 'ot_custom' && trim($update_total_value['title']) != '' && trim($update_total_value['value']) != ''){
                $totals_email_i = $update_total_key;
              }
          }
          $totals_email_str = '';
          foreach($update_totals as $update_totals_key=>$update_totals_value){

              if($update_totals_value['class'] == 'ot_custom' && trim($update_totals_value['title']) != '' && trim($update_totals_value['value']) != ''){
                if($totals_email_i != $update_totals_key){
                  $totals_email_str .= '▼'.$update_totals_value['title'].str_repeat('　', intval((16 -
                      strlen($update_totals_value['title']))/2)).'：'.$currencies->format($update_totals_value['value'])."\n";
                }else{
                  $totals_email_str .= '▼'.$update_totals_value['title'].str_repeat('　', intval((16 -
                      strlen($update_totals_value['title']))/2)).'：'.$currencies->format($update_totals_value['value']); 
                }
              }
            }
		  $tep_num = count($update_totals);
          if ($customer_guest['is_send_mail'] != '1')
          {
          $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
          $newarr = array(SENDMAIL_TEXT_DATE_MONDAY, SENDMAIL_TEXT_DATE_TUESDAY, SENDMAIL_TEXT_DATE_WEDNESDAY, SENDMAIL_TEXT_DATE_THURSDAY, SENDMAIL_TEXT_DATE_FRIDAY, SENDMAIL_TEXT_DATE_STATURDAY, SENDMAIL_TEXT_DATE_SUNDAY);
            $mailoption['ORDER_NUMBER']         = $oID;                         
            $mailoption['ORDER_DATE']       = tep_date_long(time())  ;       
            $mailoption['USER_NAME']        =  $order->customer['name'] ;
            $mailoption['USER_MAIL'] = $order->customer['email_address']; 
            //自定义费用
            if($totals_email_str != ''){
              $mailoption['CUSTOMIZED_FEE']      = $totals_email_str;
            }
            //配送费
            $mailoption['SHIPPING_FEE']      = str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format($shipping_fee)); 
            $mailoption['ORDER_TOTAL']      = str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format($mailtotal));
            @$payment_class = $$payment; 

            $mailoption['TORIHIKIHOUHOU']   =  $order->tori['houhou'];      
            $mailoption['PAYMENT']    = $order->info['payment_method'] ;  
            $trade_time = str_replace($oarr, $newarr,date('Y'.SENDMAIL_TEXT_DATE_YEAR.'m'.SENDMAIL_TEXT_DATE_MONTH.'d'.SENDMAIL_TEXT_DATE_DAY.'（l）H'.SENDMAIL_TEXT_HOUR.'i'.SENDMAIL_TEXT_MIN, strtotime($_POST['date_orders'].' '.$_POST['start_hour'].':'.$_POST['start_min'].$_POST['start_min_1'].':00'))); 
            $trade_time_1 = date('H'.SENDMAIL_TEXT_HOUR.'i'.SENDMAIL_TEXT_MIN,strtotime($_POST['date_orders'].' '.$_POST['end_hour'].':'.$_POST['end_min'].$_POST['end_min_1'].':00'));
            $mailoption['SHIPPING_TIME']      = $trade_time . SENDMAIL_TEXT_TIME_LINK . $trade_time_1 .SENDMAIL_TEXT_TWENTY_FOUR_HOUR;
            $mailoption['ORDER_COMMENT']    = $_POST['comments_text'];// = $comments;
            $mailoption['ORDER_PRODUCTS']   = $products_ordered_mail;
            $mailoption['SHIPPING_METHOD']    = $insert_torihiki_date;
            $mailoption['SITE_NAME']        = get_configuration_by_site_id('STORE_NAME',$order->info['site_id']);
            $mailoption['SITE_MAIL']        = get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS',$order->info['site_id']);
            $mailoption['SITE_URL']         = get_url_by_site_id($order->info['site_id']);

            $payment_show = payment::getInstance($order->info['site_id']);
            if(is_array($comment_arr) && !empty($comment_arr)){
              $payment_show->admin_get_payment_buying(payment::changeRomaji($order->info['payment_method'],PAYMENT_RETURN_TYPE_CODE),$mailoption,$comment_arr); 
            }

            $payment_modules->admin_deal_mailoption($mailoption, $oID, payment::changeRomaji($order->info['payment_method'], PAYMENT_RETURN_TYPE_CODE)); 
            $mailoption['ADD_INFO'] = isset($_SESSION['payment_bank_info'][$oID]['add_info'])?$_SESSION['payment_bank_info'][$oID]['add_info']:(isset($comment_arr['payment_bank_info']['add_info']) ? $comment_arr['payment_bank_info']['add_info']: '');
            unset($_SESSION['orderinfo_mail_use']);
            $print_totals_email_str = '';
            $totals_email_i = 0;
            foreach($update_totals as $update_total_key=>$update_total_value){

              if($update_total_value['class'] == 'ot_custom' && trim($update_total_value['title']) != '' && trim($update_total_value['value']) != ''){
                $totals_email_i = $update_total_key;
              }
            }
            
            foreach($update_totals as $update_totals_key=>$update_totals_value){

              if($update_totals_value['class'] == 'ot_custom' && trim($update_totals_value['title']) != '' && trim($update_totals_value['value']) != ''){
                if($totals_email_i != $update_totals_key){
                  $print_totals_email_str .= $update_totals_value['title'].'：'.$currencies->format($update_totals_value['value'])."\n";
                }else{
                  $print_totals_email_str .= $update_totals_value['title'].'：'.str_replace(TEXT_MONEY_SYMBOL,'',$currencies->format($update_totals_value['value'])); 
                }
              }
            }
            $point = $mailpoint;
            if ($point){
              $mailoption['POINT']            = $point;
            }else {
              $mailoption['POINT']            = '0';
            }
            $total_mail_fee = $handle_fee;	    
            if(!isset($total_mail_fee)){
              $total_mail_fee =0;
            }
            $mailoption['MAILFEE']          = $total_mail_fee.'';

            $selected_module = payment::changeRomaji($order->info['payment_method'],
                PAYMENT_RETURN_TYPE_CODE);
            //订单邮件
            $orders_mail_templates = tep_get_mail_templates("MODULE_PAYMENT_".strtoupper($selected_module)."_MAILSTRING",$order->info['site_id']);
            $email = $orders_mail_templates['contents'];
            
            foreach ($mailoption as $key=>$value){
              $email = str_replace('${'.strtoupper($key).'}',$value,$email);
              }
            
            //address
            if(isset($options_info_array) && !empty($options_info_array)){
              $address_len_array = array();
              foreach($options_info_array as $address_value){

                $address_len_array[] = strlen($address_value);
              }
              $maxlen = max($address_len_array);
              $email_address_str = "";
              $email_address_str .= '------------------------------------------'."\n";
              $maxlen = 9;
              foreach($options_info_array as $ad_key=>$ad_value){
                $ad_name_query = tep_db_query("select name from ". TABLE_ADDRESS ." where name_flag='". substr($ad_key,3) ."'");
                $ad_name_array = tep_db_fetch_array($ad_name_query);
                tep_db_free_result($ad_name_query);
                $ad_len = mb_strlen($ad_name_array['name'],'utf8');
                $temp_str = str_repeat('　',$maxlen-$ad_len);
                $email_address_str .= $ad_name_array['name'].$temp_str.'：'.$ad_value."\n";
              }
              $email_address_str .= '------------------------------------------'."\n";
              $email = str_replace('${USER_ADDRESS}',$email_address_str,$email);
            }else{
              $email = str_replace("\n".'${USER_ADDRESS}','',$email); 
              $email = str_replace('${USER_ADDRESS}','',$email);
              $email = str_replace("\n".SENDMAIL_TEXT_ADDRESS_INFO_LEFT,'',$email);
            }
            if($totals_email_str == ''){

              $email = str_replace("\r\n".'${CUSTOMIZED_FEE}','',$email);
              $email = str_replace('${CUSTOMIZED_FEE}','',$email);
            }
  // new send mail 
            $email = str_replace(TEXT_MONEY_SYMBOL,SENDMAIL_TEXT_MONEY_SYMBOL,$email); 
            $search_products_name_list = array();
            foreach($search_products_id_list as $products_name_value){
              $search_products_name_query = tep_db_query("select products_name from ". TABLE_PRODUCTS_DESCRIPTION ." where products_id='".$products_name_value."' and language_id='".$languages_id."' and (site_id='".$order->info['site_id']."' or site_id='0') order by site_id DESC");
              $search_products_name_array = tep_db_fetch_array($search_products_name_query);
              tep_db_free_result($search_products_name_query);
              $search_products_name_list[] = $search_products_name_array['products_name'];
            }
            $email = tep_replace_mail_templates($email,$check_status['customers_email_address'],$check_status['customers_name'],$order->info['site_id']);
            $email = html_entity_decode(htmlspecialchars(stripslashes($email)));
            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], str_replace('${SITE_NAME}',get_configuration_by_site_id('STORE_NAME',$order->info['site_id']),$orders_mail_templates['title']), str_replace($mode_products_name_list,$search_products_name_list,$email), get_configuration_by_site_id('STORE_OWNER',$order->info['site_id']), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS',$order->info['site_id']),$order->info['site_id']);
            tep_mail(get_configuration_by_site_id('STORE_OWNER',$order->info['site_id']), get_configuration_by_site_id('SENTMAIL_ADDRESS',$order->info['site_id']), str_replace('${SITE_NAME}',get_configuration_by_site_id('STORE_NAME',$order->info['site_id']),$orders_mail_templates['title']), $email, $check_status['customers_name'], $check_status['customers_email_address'],$order->info['site_id']);
          }
          $customer_notified = '1';
          
          // 支付方法是信用卡的话，发送决算URL
          $email_credit_info =  $payment_modules->admin_process_pay_email( payment::changeRomaji($payment_method,PAYMENT_RETURN_TYPE_CODE), $order,$total_price_mail,$order->info['site_id']);
          $email_credit = str_replace(TEXT_MONEY_SYMBOL,SENDMAIL_TEXT_MONEY_SYMBOL,$email_credit_info[0]);
          $email_credit = tep_replace_mail_templates($email_credit,$check_status['customers_email_address'],$check_status['customers_name'],$order->info['site_id']);
          $email_credit = html_entity_decode(htmlspecialchars(stripslashes($email_credit)));
          if($email_credit){
            if ($customer_guest['is_send_mail'] != '1'){
                tep_mail($check_status['customers_name'], $check_status['customers_email_address'], str_replace('${SITE_NAME}', get_configuration_by_site_id('STORE_NAME',$order->info['site_id']), $email_credit_info[1]) , $email_credit, get_configuration_by_site_id('STORE_OWNER',$order->info['site_id']), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS',$order->info['site_id']), $order->info['site_id']);
            }
              tep_mail(get_configuration_by_site_id('STORE_OWNER',$order->info['site_id']), get_configuration_by_site_id('SENTMAIL_ADDRESS',$order->info['site_id']), str_replace('${SITE_NAME}', get_configuration_by_site_id('STORE_NAME',$order->info['site_id']), $email_credit_info[1]), $email_credit, $check_status['customers_name'], $check_status['customers_email_address'], $order->info['site_id']);
          }
          }
          $order_updated_2 = true;
        }

    //Add Point System
    if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && MODULE_ORDER_TOTAL_POINT_ADD_STATUS != '0') {
      $pcount_query = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".MODULE_ORDER_TOTAL_POINT_ADD_STATUS."' and orders_id = '".$oID."'");
      $pcount = tep_db_fetch_array($pcount_query);
      if($pcount['cnt'] == 0 && $status == MODULE_ORDER_TOTAL_POINT_ADD_STATUS) {
        if($orders_exit_flag == true){
          $query1 = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
          $result1 = tep_db_fetch_array($query1);
        }else{
          $result1['customers_id'] = $customer_id_flag; 
        }
        $query2 = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_point' and orders_id = '".tep_db_input($oID)."'");
        $result2 = tep_db_fetch_array($query2);
        $query3 = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_subtotal' and orders_id = '".tep_db_input($oID)."'");
        $result3 = tep_db_fetch_array($query3);
        $query4 = tep_db_query("select point from " . TABLE_CUSTOMERS . " where customers_id = '".$result1['customers_id']."'");
        $result4 = tep_db_fetch_array($query4);



        // 计算各个不同顾客的返点率从这开始============================================================
        if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
          $customer_id = $result1['customers_id'];
          //规定期间内，计算订单合计金额------------
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
          //这次的订单金额除外
          $total_buyed_date = $total_buyed_date - ($result3['value'] - (int)$result2['value']);

          //计算返点率----------------------------------
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
        // 计算各个不同顾客的返点率到此结束============================================================
        if ($result3['value'] >= 0) {
          $get_point = ($result3['value'] - (int)$result2['value']) * $point_rate;
        } else {
          $get_point = $payment_modules->admin_get_fetch_point(payment::changeRomaji($payment_method,'code'),$result3['value']);
        }
        
      }else{
        $os_query = tep_db_query("select orders_status_name,nomail from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
        $os_result = tep_db_fetch_array($os_query);
        if($os_result['orders_status_name']==TEXT_NOTICE_PAYMENT){
          if($orders_exit_flag == true){
            $query1 = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
            $result1 = tep_db_fetch_array($query1);
          }else{

            $result1['customers_id'] = $customer_id_flag;
          }
          $get_point = $payment_modules->admin_get_orders_point(payment::changeRomaji($payment_method,'code'),$oID); 
          $point_done_query =tep_db_query("select count(orders_status_history_id) cnt from
              ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".$status."' and 
              orders_id = '".tep_db_input($oID)."'");
          $point_done_row  =  tep_db_fetch_array($point_done_query);
        }
      }
    }
      if ($check_status['orders_status'] != $status || $comments != '' || $orders_exit_flag == false) {
          $comment_str = is_array($comment_arr) ? $comment_arr['comment'] : $comments_text;
          tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments, user_added) values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" . $customer_notified . "', '".$comment_str."', '".tep_db_input($update_user_info['name'])."')");
      }
        if ($order_updated && !$products_delete && $order_updated_2) {
          if($message_success == false){
            $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
          }
        } elseif ($order_updated && $products_delete) {
          $messageStack->add_session(TEXT_PRODUCTS_DELETE, 'success');
        } else {
          $messageStack->add_session(TEXT_ERROR_NO_SUCCESS, 'error');
        }
  //订单状态邮件
  if ($check_status['orders_status'] != $status || $comments != '' || $orders_exit_flag == false) {
        tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', user_update='".$_SESSION['user_name']."',last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
        orders_updated(tep_db_input($oID));
        orders_wait_flag(tep_db_input($oID));
        $customer_notified = '0';
        $os_query = tep_db_query("select orders_status_name,nomail from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
        $os_result = tep_db_fetch_array($os_query); 
      if ($_POST['notify'] == 'on' && $os_result['nomail'] == 0) {

        $ot_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_total'");
        $ot_result = tep_db_fetch_array($ot_query);
        $otm = (int)$ot_result['value'] . EDIT_ORDERS_PRICE_UNIT;

        //自定义费用
        if($totals_email_str != ''){
		  if($tep_num >=1){
          $comments = str_replace('${CUSTOMIZED_FEE}',str_replace('▼','',$totals_email_str), $comments);
		  }else{
          $comments = str_replace('${CUSTOMIZED_FEE}'."\r\n".str_replace('▼','',$totals_email_str), $comments);
		  }
        }else{
          $comments = str_replace("\r\n".'${CUSTOMIZED_FEE}','', $comments); 
          $comments = str_replace('${CUSTOMIZED_FEE}','', $comments);
        }
        //住所
        if($email_address_str != ''){
          $comments = str_replace('${USER_ADDRESS}',str_replace('▼','',$email_address_str), $comments);
        }else{
          $comments = str_replace("\n".'${USER_ADDRESS}','', $comments); 
          $comments = str_replace('${USER_ADDRESS}','', $comments);
        }

        $title = str_replace(array(
              '${USER_NAME}',
              '${USER_MAIL}',
              '${ORDER_DATE}',
              '${ORDER_NUMBER}',
              '${PAYMENT}',
              '${ORDER_TOTAL}',
              '${TRADING}',
              '${ORDER_STATUS}',
              '${SITE_NAME}',
              '${SITE_URL}',
              '${SUPPORT_MAIL}',
              '${PAY_DATE}'
              ),array(
                $check_status['customers_name'],
                $check_status['customers_email_address'],
                tep_date_long($check_status['date_purchased']),
                $oID,
                $check_status['payment_method'],
                $otm,
                tep_torihiki($check_status['torihiki_date']).TEXT_TIME_LINK.date('H'.TEXT_HOUR.'i'.TEXT_MIN,strtotime($check_status['torihiki_date_end'])).TEXT_TWENTY_FOUR_HOUR,
                $os_result['orders_status_name'],
                get_configuration_by_site_id('STORE_NAME', $site_id),
                get_url_by_site_id($site_id),
                get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
                date('Y'.TEXT_DATE_YEAR.'n'.TEXT_DATE_MONTH.'j'.TEXT_DATE_DAY,strtotime(tep_get_pay_day()))
                ),$title);
        $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $newarr = array(SENDMAIL_TEXT_DATE_MONDAY, SENDMAIL_TEXT_DATE_TUESDAY, SENDMAIL_TEXT_DATE_WEDNESDAY, SENDMAIL_TEXT_DATE_THURSDAY, SENDMAIL_TEXT_DATE_FRIDAY, SENDMAIL_TEXT_DATE_STATURDAY, SENDMAIL_TEXT_DATE_SUNDAY);
        $trade_time_start = str_replace($oarr, $newarr,date('Y'.SENDMAIL_TEXT_DATE_YEAR.'m'.SENDMAIL_TEXT_DATE_MONTH.'d'.SENDMAIL_TEXT_DATE_DAY.'（l）H'.SENDMAIL_TEXT_HOUR.'i'.SENDMAIL_TEXT_MIN, strtotime($_POST['date_orders'].' '.$_POST['start_hour'].':'.$_POST['start_min'].$_POST['start_min_1'].':00'))); 
        $trade_time_end = date('H'.SENDMAIL_TEXT_HOUR.'i'.SENDMAIL_TEXT_MIN,strtotime($_POST['date_orders'].' '.$_POST['end_hour'].':'.$_POST['end_min'].$_POST['end_min_1'].':00')); 
        $comments = str_replace(array(
              '${USER_NAME}',
              '${USER_MAIL}',
              '${ORDER_DATE}',
              '${ORDER_NUMBER}',
              '${PAYMENT}',
              '${ORDER_TOTAL}',
              '${TRADING}',
              '${ORDER_STATUS}',
              '${SITE_NAME}',
              '${SITE_URL}',
              '${SUPPORT_MAIL}',
              '${PAY_DATE}',
              '${SHIPPING_TIME}',
              '${MAIL_COMMENT}',
              '${COMMISSION}',
              '${ORDER_PRODUCTS}',
              '${SHIPPING_FEE}',
              '${ORDER_COMMENT}',
              '${SHIPPING_METHOD}',
              '${POINT}',
              '${TOTAL}',
              ),array(
                $check_status['customers_name'],
                $check_status['customers_email_address'],
                tep_date_long(date('Y-m-d H:i:s')),
                $oID,
                $check_status['payment_method'],
                $currencies->format($_SESSION['orders_update_products'][$oID]['ot_total']+$shipping_fee),
                tep_torihiki($check_status['torihiki_date']).TEXT_TIME_LINK.date('H'.TEXT_HOUR.'i'.TEXT_MIN,strtotime($check_status['torihiki_date_end'])).TEXT_TWENTY_FOUR_HOUR,
                $os_result['orders_status_name'],
                get_configuration_by_site_id('STORE_NAME', $site_id),
                get_url_by_site_id($site_id),
                get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
                date('Y'.TEXT_DATE_YEAR.'n'.TEXT_DATE_MONTH.'j'.TEXT_DATE_DAY,strtotime(tep_get_pay_day())),
                $trade_time_start . SENDMAIL_TEXT_TIME_LINK . $trade_time_end .SENDMAIL_TEXT_TWENTY_FOUR_HOUR,
                orders_a($order->info['orders_id']),
                $handle_fee,
                $products_ordered_mail,      
                str_replace(SENDMAIL_TEXT_MONEY_SYMBOL,"",$currencies->format($shipping_fee)),
                $_POST['comments_text'],
                $insert_torihiki_date,
                empty($point) ? 0 : $point,
                str_replace(SENDMAIL_TEXT_MONEY_SYMBOL,"",$currencies->format($mailtotal)),
              ),$comments);
        $comments = str_replace(TEXT_MONEY_SYMBOL,SENDMAIL_TEXT_MONEY_SYMBOL, $comments);
        $comments = tep_replace_mail_templates($comments,$check_status['customers_email_address'],$check_status['customers_name'],$site_id);
        $comments = html_entity_decode(htmlspecialchars(stripslashes($comments)));
        if ($customer_guest['is_send_mail'] != '1') {

          tep_mail($check_status['customers_name'], $check_status['customers_email_address'], $title, $comments, get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $site_id), $site_id);
        }
        tep_mail(get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('SENTMAIL_ADDRESS', $site_id), SENDMAIL_TEXT_SENDED.$title, $comments, $check_status['customers_name'], $check_status['customers_email_address'], $site_id);
        $customer_notified = '1';
      }


        $customer_notified = '1';
      
      // 同步问答
      $order_updated = true;
    }
    
    $message_success = false;
    if ($order_updated) {
      $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
      $message_success = true;  
    } else {
      if($orders_exit_flag == true){
        $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
      }
    }
    //订单状态更新结束 
  //打印邮件
  $orders_print_mail_templates = tep_get_mail_templates('MODULE_PAYMENT_'.strtoupper($_POST['payment_method']).'_PRINT_MAILSTRING',$site_id_flag);  
  $payment_name_string = $orders_print_mail_templates['contents']; 
  $payment_name_string_title = $orders_print_mail_templates['title'];

  $payment_mode = array(
                        '${USER_NAME}',
                        '${YEAR}',
                        '${SITE_NAME}',
                        '${ORDER_NUMBER}',
                        '${ORDER_DATE}',
                        '${USER_MAIL}',
                        '${BANK_FOR_TRANSFER}',
                        '${POINT}',
                        '${SHIPPING_FEE}',
                        '${TOTAL}',
                        '${COMMISSION}',
                        '${ORDER_TOTAL}',
                        '${ORDER_PRODUCTS}',
                        '${SHIPPING_TIME}',
                        '${ORDER_COMMENT}',
                        '${ADD_INFO}',
                        '${USER_INFO}',
                        '${CREDIT_RESEARCH}',
                        '${ORDER_HISTORY}',
                        '${SHIPPING_METHOD}',
                      );
  //storm name
  $orders_site_name_query = tep_db_query("select name,url from ". TABLE_SITES ." where id='". $site_id_flag ."'");
  $orders_site_name_array = tep_db_fetch_array($orders_site_name_query);
  tep_db_free_result($orders_site_name_query);
  //site mail
  $site_name_query = tep_db_query("select configuration_value from ". TABLE_CONFIGURATION ." where configuration_key='SUPPORT_EMAIL_ADDRESS' and site_id='".$site_id_flag."'");
  $site_name_array = tep_db_fetch_array($site_name_query);
  tep_db_free_result($site_name_query);
  //orders products
      $order2 = new order($oID);
      $products_ordered_mail = '';
      $max_c_len = 0;
      $max_len_array = array();
      for ($mi=0; $mi<sizeof($order2->products); $mi++) {
        for ($mj=0; $mj<sizeof($order2->products[$mi]['attributes']); $mj++) {
          $max_len_array[] = mb_strlen($order2->products[$mi]['attributes'][$mj]['option_info']['title'], 'utf-8'); 
        } 
      }
      if (!empty($max_len_array)) {
        $max_c_len = max($max_len_array); 
      }
      if ($max_c_len < 4) {
        $max_c_len = 4; 
      }
      for ($i=0; $i<sizeof($order2->products); $i++) {
        $products_ordered_mail .= SENDMAIL_ORDERS_PRODUCTS.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_ORDERS_PRODUCTS, 'utf-8'))).'：' . $order2->products[$i]['name'] . '（' . $order2->products[$i]['model'] . '）' . "\n";
        // Has Attributes?
        if (sizeof($order2->products[$i]['attributes']) > 0) {
          for ($j=0; $j<sizeof($order2->products[$i]['attributes']); $j++) {
            $products_ordered_mail .= $order2->products[$i]['attributes'][$j]['option_info']['title'] .str_repeat('　', intval($max_c_len - mb_strlen($order2->products[$i]['attributes'][$j]['option_info']['title'], 'utf-8'))).'：';
            $products_ordered_mail .= $order2->products[$i]['attributes'][$j]['option_info']['value'] . "\n";
          }
        }
          
        $products_ordered_mail .= SENDMAIL_QTY_NUM.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_QTY_NUM, 'utf-8'))).'：' . $order2->products[$i]['qty'] . SENDMAIL_EDIT_ORDERS_NUM_UNIT.'(' . tep_get_full_count_in_order2($order2->products[$i]['qty'], $order2->products[$i]['id']) . ")\n";
        $products_ordered_mail .= SENDMAIL_TABLE_HEADING_PRODUCTS_PRICE.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_TABLE_HEADING_PRODUCTS_PRICE, 'utf-8'))).'：' . $currencies->display_price($order2->products[$i]['final_price'], $order2->products[$i]['tax']) . "\n";
        $products_ordered_mail .= SENDMAIL_SUB_TOTAL.str_repeat('　', intval($max_c_len - mb_strlen(SENDMAIL_SUB_TOTAL, 'utf-8'))).'：' . $currencies->display_price($order2->products[$i]['final_price'], $order2->products[$i]['tax'], $order2->products[$i]['qty']) . "\n";
        $products_ordered_mail .= "------------------------------------------\n";
        if (tep_get_cflag_by_product_id($order2->products[$i]['id'])) {
            if (tep_get_bflag_by_product_id($order2->products[$i]['id'])) {
              $products_ordered_mail .= SENDMAIL_TEXT_CHARACTER_NAME_SEND_MAIL."\n\n";
            } else {
              $products_ordered_mail .= SENDMAIL_TEXT_CHARACTER_NAME_CONFIRM_SEND_MAIL."\n\n";
            }
        }
      } 
      //customer info
      $customer_printing_order .= SENDMAIL_TEXT_IP_ADDRESS . $_SERVER["REMOTE_ADDR"] . "\n";
      $customer_printing_order .= SENDMAIL_TEXT_HOST . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
      $customer_printing_order .= SENDMAIL_TEXT_USER_AGENT . $_SERVER["HTTP_USER_AGENT"] . "\n";
  //credit research
  $credit_inquiry_query = tep_db_query("select customers_fax, customers_guest_chk from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id_flag . "'");
  $credit_inquiry       = tep_db_fetch_array($credit_inquiry_query);
  
  $credit_printing_order .= $credit_inquiry['customers_fax'];
  //orders history
  $email_orders_history = '';
  
  if ($credit_inquiry['customers_guest_chk'] == '1') { 
    $email_orders_history .= SENDMAIL_TEXT_GUEST; 
  } else { 
    $email_orders_history .= SENDMAIL_TEXT_MEMBER; 
  }
  
  $email_orders_history .= "\n";
  
$order_history_query_raw = "select o.orders_id, o.customers_name, o.customers_id,
  o.date_purchased, s.orders_status_name, ot.value as order_total_value from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . tep_db_input($customer_id_flag) . "' and o.orders_status = s.orders_status_id and s.language_id = '" . $_SESSION['languages_id'] . "' and ot.class = 'ot_total' order by o.date_purchased DESC limit 0,5";  
$order_history_query = tep_db_query($order_history_query_raw);
$orders_i = 0;
while ($order_history = tep_db_fetch_array($order_history_query)) {
  $orders_i++;
  $br = $orders_i == 5 ? "" : "\n";
  $email_orders_history .= $order_history['date_purchased'] . '　　' .
    tep_output_string_protected($order_history['customers_name']) . '　　' .
    $currencies->format(abs(intval($order_history['order_total_value']))) .'　　' . $order_history['orders_status_name'] . $br;
}
  //orders comment
      $cpayment = payment::getInstance();
      $payment_array = payment::getPaymentList();
      foreach($payment_array[0] as $pay_key=>$pay_value){ 
        $payment_info = $cpayment->admin_get_payment_info_comment($pay_value,$update_customer_email_address,$site_id_flag,1,(($customer_point['is_quited'] == '1')?2:0));
        $payment_info_array[$pay_key] = $payment_info;
        if(is_array($payment_info)){

          switch($payment_info[0]){
          case 0: 
            $pay_type_array[0] = $pay_value;
            break; 
          case 2: 
            $pay_type_array[2] = $pay_value;
            break; 
          }
        } 
      }
  $orders_comments = $pay_type_array[0] == $payment_method || $pay_type_array[2] == $payment_method ? $comment_arr['comment'] : $comments_text;  
  $point = !isset($point) ? 0 : $point;
  $payment_replace = array(
                          tep_db_input(stripslashes($update_customer_name)),
                          date('Y'),
                          $orders_site_name_array['name'],
                          $oID,
                          tep_date_long(time()),
                          tep_db_input($update_customer_email_address), 
                          $comment_arr['payment_info'],
                          $point,  
                          str_replace(SENDMAIL_TEXT_MONEY_SYMBOL,"",$currencies->format($shipping_fee)),
                          str_replace(SENDMAIL_TEXT_MONEY_SYMBOL,"",$currencies->format($mailtotal)),
                          $handle_fee, 
                          str_replace(SENDMAIL_TEXT_MONEY_SYMBOL,"",$currencies->format(abs($newtotal))),
                          $products_ordered_mail,
                          tep_date_long($_POST['date_orders']) . $_POST['start_hour'] . SENDMAIL_TEXT_HOUR . $_POST['start_min'].$_POST['start_min_1'] . SENDMAIL_TEXT_MIN.SENDMAIL_TEXT_TIME_LINK. $_POST     ['end_hour'] .SENDMAIL_TEXT_HOUR. $_POST['end_min'].$_POST['end_min_1'] .SENDMAIL_TEXT_MIN.SENDMAIL_TEXT_TWENTY_FOUR_HOUR, 
                          strpos($payment_name_string,'${BANK_FOR_TRANSFER}') == true ? $comments_text : $comment_str,
                          '',
                          $customer_printing_order,
                          $credit_printing_order,
                          $email_orders_history,
                          ''
  );
  $payment_name_string = str_replace($payment_mode,$payment_replace,$payment_name_string);  
  $email_printing_order = $payment_name_string; 
  $email_printing_order_title = str_replace('${SITE_NAME}',$orders_site_name_array['name'],$payment_name_string_title);
  //自定义费用
  if($totals_email_str != ''){
	 if($tep_num >=1){
       $email_printing_order = str_replace('${CUSTOMIZED_FEE}',str_replace('▼','',$totals_email_str), $email_printing_order);
	 }else{
       $email_printing_order = str_replace('${CUSTOMIZED_FEE}'."\r\n",str_replace('▼','',$totals_email_str), $email_printing_order);
	 }
  }else{
    $email_printing_order = str_replace("\r\n".'${CUSTOMIZED_FEE}','', $email_printing_order); 
    $email_printing_order = str_replace('${CUSTOMIZED_FEE}','', $email_printing_order);
  }
  //住所
  if($email_address_str != ''){
    $email_printing_order = str_replace('${USER_ADDRESS}',str_replace('▼','',$email_address_str), $email_printing_order);
  }else{
    $email_printing_order = str_replace("\n".'${USER_ADDRESS}','', $email_printing_order); 
    $email_printing_order = str_replace('${USER_ADDRESS}','', $email_printing_order);
    $email_printing_order = str_replace("\n".str_replace('▼','',SENDMAIL_TEXT_ADDRESS_INFO_LEFT),'', $email_printing_order);
  } 
  $email_printing_order = str_replace(TEXT_MONEY_SYMBOL,SENDMAIL_TEXT_MONEY_SYMBOL, $email_printing_order); 
  # ------------------------------------------
  $email_printing_order = tep_replace_mail_templates($email_printing_order,$check_status['customers_email_address'],$check_status['customers_name'],$site_id_flag);  
  $email_printing_order = html_entity_decode(htmlspecialchars(stripslashes($email_printing_order)));
  tep_mail('',
      get_configuration_by_site_id('PRINT_EMAIL_ADDRESS',$site_id_flag),
      $email_printing_order_title,
      $email_printing_order,tep_db_input(stripslashes($update_customer_name))
      ,tep_db_input($update_customer_email_address) , ''); 
       
       $exists_customer_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$_SESSION['customer_id']."'"); 
       $exists_customer = tep_db_fetch_array($exists_customer_raw); 
       if ($exists_customer) {
         if ($exists_customer['is_quited'] == '1') {
           tep_db_query("update ".TABLE_ORDERS." set is_gray = '2' where orders_id = '".$oID."'"); 
         }
         if ($exists_customer['customers_guest_chk'] == '1') {
           tep_db_query("update ".TABLE_ORDERS." set is_guest = '1' where orders_id = '".$oID."'"); 
         }
       } else {
         tep_db_query("update ".TABLE_ORDERS." set is_guest = '1' where orders_id = '".$oID."'"); 
       }
       //session unset
       unset($_SESSION['oID']);
       unset($_SESSION['customer_id']);
       unset($_SESSION['firstname']);
       unset($_SESSION['lastname']);
       unset($_SESSION['email_address']);
       unset($_SESSION['telephone']);
       unset($_SESSION['fax']);
       unset($_SESSION['street_address']);
       unset($_SESSION['company']);
       unset($_SESSION['suburb']);
       unset($_SESSION['postcode']);
       unset($_SESSION['city']);
       unset($_SESSION['zone_id']);
       unset($_SESSION['state']);
       unset($_SESSION['country']);
       unset($_SESSION['sites_id_flag']);
       unset($_SESSION['format_id']);
       unset($_SESSION['size']);
       unset($_SESSION['new_value']);
       unset($_SESSION['temp_amount']);
       unset($_SESSION['currency']); 
       unset($_SESSION['currency_value']);
       unset($_SESSION['orders_update_products'][$oID]);
       tep_redirect(tep_href_link("orders.php", 'keywords='.$oID.'&search_type=orders_id'));
        
        break;

        // 2. ADD A PRODUCT ###############################################################################################
        case 'add_product':

        if($step == 5)
        {
          // 2.1 GET ORDER INFO #####

          $oID = tep_db_prepare_input($_GET['oID']);
          $order = new order($oID);

          $AddedOptionsPrice = 0;
          
          foreach ($_POST as $op_key => $op_value) {
            $op_pos = substr($op_key, 0, 3);
            if ($op_pos == 'op_') {
              $op_info_array = explode('_', $op_key);
              $op_item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_info_array[1]."' and id = '".$op_info_array[3]."'");
              $op_item_res = tep_db_fetch_array($op_item_query);
              if ($op_item_res) {
                if ($op_item_res['type'] == 'radio') {
                  $o_option_array = @unserialize($op_item_res['option']);
                  if (!empty($o_option_array['radio_image'])) {
                    foreach ($o_option_array['radio_image'] as $or_key => $or_value) {
                      if (trim($or_value['title']) == trim($op_value)) {
                        $AddedOptionsPrice += $or_value['money'];
                        break;
                      }
                    }
                  }
                } else {
                  $AddedOptionsPrice += $op_item_res['price'];
                }
              }
            }
          }
          // 2.1.1 Get Product Attribute Info
          // 2.1.2 Get Product Info
          $InfoQuery = "
            select p.products_model, 
                   p.products_price, 
                   pd.products_name, 
                   p.products_tax_class_id, 
                   p.products_small_sum,
                   p.products_price_offset,
                   p.price_type
                     from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pd.products_id=p.products_id 
                     where p.products_id='$add_product_products_id' 
                     and pd.site_id = '0'
                     and pd.language_id = '" . (int)$languages_id . "'";
          $result = tep_db_query($InfoQuery);

          $row = tep_db_fetch_array($result);
          extract($row, EXTR_PREFIX_ALL, "p");

          // 适用于特价
          $p_products_price = tep_get_final_price($p_products_price,
              $p_products_price_offset, $p_products_small_sum,
              (int)$add_product_quantity,$p_price_type);
          // Following functions are defined at the bottom of this file
          $CountryID = tep_get_country_id($order->delivery["country"]);
          $ZoneID = tep_get_zone_id($CountryID, $order->delivery["state"]);

          $ProductsTax = tep_get_tax_rate($p_products_tax_class_id, $CountryID, $ZoneID);

          // 2.2 UPDATE ORDER #####
          $Query = "insert into " . TABLE_ORDERS_PRODUCTS . " set
            orders_id = '$oID',
                      products_id = $add_product_products_id,
                      products_model = '$p_products_model',
                      products_name = '" . str_replace("'", "&#39;", $p_products_name) . "',
                      products_price = '$p_products_price',
                      final_price = '" . ($p_products_price + $AddedOptionsPrice) . "',
                      products_tax = '$ProductsTax',
                      site_id = '".tep_get_site_id_by_orders_id($oID)."',
                      products_rate = '".tep_get_products_rate($add_product_products_id)."',
                      products_quantity = '" . (int)$add_product_quantity . "';";
          tep_db_query($Query);
          $new_product_id = tep_db_insert_id();


          orders_updated($oID);


          // 2.2.1 Update inventory Quantity
          $p = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$add_product_products_id."'"));
          if ((int)$add_product_quantity > $p['products_real_quantity']) {
            // 买取商品大于实数
            tep_db_perform('products',array(
                  'products_real_quantity' => 0,
                  'products_virtual_quantity' => $p['products_virtual_quantity'] - (int)$add_product_quantity + $p['products_real_quantity']
                  ),
                'update',
                "products_id = '" . $add_product_products_id . "'");
          } else {
            tep_db_perform('products',array(
                  'products_real_quantity' => $p['products_real_quantity'] - (int)$add_product_quantity
                  ),
                'update',
                "products_id = '" . $add_product_products_id . "'");
          }
          // 处理负数问题
          tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = 0 where products_real_quantity < 0 and products_id = '" . $add_product_products_id . "'");
          tep_db_query("update " . TABLE_PRODUCTS . " set products_virtual_quantity = 0 where products_virtual_quantity < 0 and products_id = '" . $add_product_products_id . "'");

            foreach($_POST as $op_i_key => $op_i_value) {
              $op_pos = substr($op_i_key, 0, 3);
              if ($op_pos == 'op_') {
                $i_op_array = explode('_', $op_i_key);
                $ioption_item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$i_op_array[1]."' and id = '".$i_op_array[3]."'"); 
                $ioption_item_res = tep_db_fetch_array($ioption_item_query);
                if ($ioption_item_res) {
                  $input_option_array = array('title' => $ioption_item_res['front_title'], 'value' => $op_i_value); 
                  $op_price = 0; 
                  if ($ioption_item_res['type'] == 'radio') {
                    $io_option_array = @unserialize($ioption_item_res['option']);
                    if (!empty($io_option_array['radio_image'])) {
                      foreach ($io_option_array['radio_image'] as $ior_key => $ior_value) {
                        if (trim($ior_value['title']) == trim($op_i_value)) {
                          $op_price = $ior_value['money']; 
                          break; 
                        }
                      }
                    }
                  } else {
                    $op_price = $ioption_item_res['price']; 
                  }
                  $Query = "insert into " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set
                    orders_id = '$oID',
                              orders_products_id = $new_product_id,
                              options_values_price = '" .  tep_db_input($op_price) . "',
                              option_group_id = '" .  $ioption_item_res['group_id'] . "',
                              option_item_id = '" .  $ioption_item_res['id'] . "',
                              option_info = '".tep_db_input(serialize($input_option_array))."';";
                  tep_db_query($Query);
                }
              }
            }

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

          //subtotal
          tep_db_query("update " . TABLE_ORDERS_TOTAL . " set value = '".$new_subtotal."' where class='ot_subtotal' and orders_id = '".$oID."'");

          //tax
          $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_ORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
          $plustax = tep_db_fetch_array($plustax_query);
          if($plustax['cnt'] > 0) {
            tep_db_query("update " . TABLE_ORDERS_TOTAL . " set value = '".tep_insert_currency_value($new_tax)."' where class='ot_tax' and orders_id = '".$oID."'");
          }

          //total
          $total_query = tep_db_query("select sum(value) as total_value from " . TABLE_ORDERS_TOTAL . " where class != 'ot_total' and orders_id = '".$oID."'");
          $total_value = tep_db_fetch_array($total_query);

          if($plustax['cnt'] == 0) {
            $newtotal = $total_value["total_value"] + $new_tax;
          } else {
            if(DISPLAY_PRICE_WITH_TAX == 'true') {
              $newtotal = $total_value["total_value"] - $new_tax;
            } else {
              $newtotal = $total_value["total_value"];
            }
          }
          $handle_fee = $payment_modules->handle_calc_fee(
          payment::changeRomaji($order->info['payment_method'],PAYMENT_RETURN_TYPE_CODE), $newtotal);

          $newtotal = $newtotal+$handle_fee+$shipping_fee;    
          $totals = "update " . TABLE_ORDERS_TOTAL . " set value = '".intval(floor($newtotal))."' where class='ot_total' and orders_id = '".$oID."'";
          tep_db_query($totals);
          // shipping total
          $update_orders_sql = "update ".TABLE_ORDERS." set code_fee = '".$handle_fee."' where orders_id = '".$oID."'";
          tep_db_query($update_orders_sql);
          tep_redirect(tep_href_link("edit_new_orders.php", tep_get_all_get_params(array('action')) . 'action=edit'));
        }

        break;

      }
  }

  if (isset($_GET['oID'])) {
    $oID = tep_db_prepare_input($_GET['oID']);

    if($orders_exit_flag == true){
      $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
      $order_exists = true;
      if (!tep_db_num_rows($orders_query)) {
        $order_exists = false;
        $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
      }
    }
    $p_weight_total = 0; //商品总重量
    $p_address_query = tep_db_query("select * from ". TABLE_ORDERS_PRODUCTS ." where orders_id='". tep_db_input($oID) ."'");
    while($p_address_array = tep_db_fetch_array($p_address_query)){

      $p_weight_query = tep_db_query("select * from ". TABLE_PRODUCTS ." where products_id='". $p_address_array['products_id'] ."'");
      $p_weight_array = tep_db_fetch_array($p_weight_query);
      $p_weight_total += $p_weight_array['products_weight']*$p_address_array['products_quantity'];
      tep_db_free_result($p_weight_query);
    }
    tep_db_free_result($p_address_query);
  }
  //这里判断是否 存在产品 如果存在产品 使用产品的配送信息

  ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html <?php echo HTML_PARAMS; ?>>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css?v=<?php echo $back_rand_info?>">
    <link rel="stylesheet" type="text/css" href="includes/styles.css?v=<?php echo $back_rand_info?>">
    <link rel="stylesheet" type="text/css" href="css/popup_window.css?v=<?php echo $back_rand_info?>">
    <script type="text/javascript">
    var session_orders_id = '<?php echo $_GET['oID'];?>';
    var session_site_id = '<?php echo $_SESSION['sites_id_flag'];?>';
    var text_unset_data = '<?php echo TEXT_UNSET_DATA;?>';
    var image_icon_info = '<?php echo IMAGE_ICON_INFO;?>';
    var text_popup_window_show = '<?php echo TEXT_POPUP_WINDOW_SHOW;?>';
    var text_popup_window_edit = '<?php echo TEXT_POPUP_WINDOW_EDIT;?>';
    var image_save = '<?php echo IMAGE_SAVE;?>';
    var tmp_other_str = '<?php echo $_SERVER['PHP_SELF'];?>'; 
    var notice_relogin_str = '<?php echo TEXT_TIMEOUT_RELOGIN;?>'; 
    var js_text_all_orders_not_choose = '<?php echo JS_TEXT_ALL_ORDERS_NOT_CHOOSE;?>';
    var js_text_all_orders_no_option_order = '<?php echo JS_TEXT_ALL_ORDERS_NO_OPTION_ORDER;?>';
    </script>
    <script language="javascript" src="js2php.php?path=includes&name=general&type=js&v=<?php echo $back_rand_info?>"></script>
    <script language="javascript" src="includes/javascript/jquery.js?v=<?php echo $back_rand_info?>"></script>
    <script language="javascript" src="includes/javascript/jquery_include.js?v=<?php echo $back_rand_info?>"></script>
    <script language="javascript" src="includes/javascript/one_time_pwd.js?v=<?php echo $back_rand_info?>"></script>
    <script language="javascript" src="includes/javascript/all_order.js?v=<?php echo $back_rand_info?>"></script>
    <script language="javascript" src="includes/3.4.1/build/yui/yui.js?v=<?php echo $back_rand_info?>"></script>
    <script language="javascript" src="includes/jquery.form.js?v=<?php echo $back_rand_info?>"></script>
    <script language="javascript" src="js/popup_window.js?v=<?php echo $back_rand_info?>"></script>
    <script type="text/javascript">
	var js_ne_orders_text_ok = '<?php echo DIV_TEXT_OK;?>'; 
	var js_ne_orders_text_clear = '<?php echo DIV_TEXT_CLEAR;?>';
	var js_ne_orders_date_time = '<?php echo date('Ymd');?>';
	var js_ne_orders_date_hour = '<?php echo date('Hi');?>';
	var js_ne_orders_bank_error_name = '<?php echo TS_TEXT_BANK_ERROR_NAME;?>';
	var js_ne_orders_bank_error_entity = '<?php echo TS_TEXT_BANK_ERROR_ENTITY;?>';
	var js_ne_orders_kouza_num = '<?php echo TS_TEXT_BANK_ERROR_ACCOUNT_NUM;?>';
	var js_ne_orders_kouza_num2 = '<?php echo TS_TEXT_BANK_ERROR_ACCOUNT_NUM2;?>';
	var js_ne_orders_kouza_name = '<?php echo TS_TEXT_BANK_ERROR_ACCOUNT_NAME;?>';
	var js_ne_orders_bank_error_message = '<?php echo TS_MODULE_PAYMENT_RAKUTEN_BANK_TEXT_ERROR_MESSAGE;?>';
	var js_ne_orders_date_num_error = '<?php echo TEXT_DATE_NUM_ERROR;?>';
	var js_ne_orders_oid = '<?php echo $_GET['oID'];?>';
	var js_ne_orders_c_name_info = '<?php echo $orders_exit_flag == true ? tep_html_quotes($order->customer['name']) : tep_html_quotes($_SESSION['lastname'].' '.addslashes($_SESSION['firstname'])); ?>';
	var js_ne_orders_c_mail_info = '<?php echo $orders_exit_flag == true ?  $order->customer['email_address'] : $_SESSION['email_address'];?>';
	var js_ne_orders_c_id_info = '<?php echo (isset($_SESSION['sites_id_flag'])?$_SESSION['sites_id_flag']:'');?>';
	var js_ne_orders_products_num = '<?php echo TEXT_PRODUCTS_NUM;?>';
	var js_ne_orders_languages_id = '<?php echo $languages_id;?>';
	var js_ne_orders_site_id = '<?php echo $_SESSION['sites_id_flag'];?>';
	var js_ne_orders_npermission = '<?php echo $ocertify->npermission;?>';
	var js_ne_orders_money_symbol = '<?php echo TEXT_MONEY_SYMBOL;?>';

<?php
if($p_weight_total > 0){
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
      break;
    case '3':
      echo 'var country_city_id = "ad_'. $address_fixed_array['name_flag'] .'";'."\n";
      echo 'var country_city_id_one = "'. $address_fixed_array['name_flag'] .'";'."\n";
      $country_city_id = 'ad_'.$address_fixed_array['name_flag'];
      break;
      break;
    }
  }
  //获取是否开启了帐单邮寄地址功能
  $billing_address_show = get_configuration_by_site_id('BILLING_ADDRESS_SETTING',$_SESSION['sites_id_flag']);
  $billing_address_show = $billing_address_show == '' ? get_configuration_by_site_id('BILLING_ADDRESS_SETTING',0) : $billing_address_show;
?>
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

// end in_array
	var first_num = 0;
	var address_first_num = 0;
	var arr_new = new Array();
	var arr_color = new Array();
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
	var js_ne_orders_text_require = '<?php echo TEXT_REQUIRE;?>';
<?php
      if(!isset($_POST['address_option']) || $_POST['address_option'] == 'old'){
?>
	var weight_option_status_old = true;
<?php
	}else{
?>
	var weight_option_status_old = false;
<?php
	}
?>
<?php
      if(!isset($_POST['address_option']) || $_POST['address_option'] == 'new'){
?>
        var weight_option_status_new = true;
<?php
        }else{
?>
        var weight_option_status_new = false;
<?php
        }
?>
	var arr_old  = new Array();
	var arr_name = new Array();
	var billing_address_num = '';
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
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id=". $customer_id_flag ." group by orders_id order by orders_id desc");
  
   
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
    if($billing_address_show == 'true' && $address_orders_array['billing_address'] == '1'){

      echo 'billing_address_num = '.$address_num.';';
    }
        
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
  $address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and billing_address='0' order by id");
  while($address_orders_array = tep_db_fetch_array($address_orders_query)){
  
    if(in_array($address_orders_array['name'],$address_list_arr)){

      echo "\n".'address_str += "'. $address_orders_array['value'] .'";'."\n";
    }
  }
  tep_db_free_result($address_orders_query);
?>
	var js_ne_orders_weight_billing_add = '<?php echo TEXT_BILLING_ADDRESS;?>';
	var js_ne_orders_weight_add_show_list = '<?php echo $_POST['address_show_list'];?>';
	var arr_list = new Array();
<?php
  //根据后台的设置来显示相应的地址列表
  $address_list_arr = array();
  $address_list_query = tep_db_query("select name_flag from ". TABLE_ADDRESS ." where status='0' and show_title='1'");
  while($address_list_array = tep_db_fetch_array($address_list_query)){

    $address_list_arr[] = $address_list_array['name_flag'];
  }
  tep_db_free_result($address_list_query);
  $address_orders_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_HISTORY ." where customers_id=".$customer_id_flag ." group by orders_id order by orders_id desc");
  
   
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
	var js_ne_country_fee_id = '<?php echo $country_fee_id;?>';
	var js_ne_country_area_id = '<?php echo $country_area_id;?>';
	var js_ne_country_city_id = '<?php echo $country_city_id;?>';
	var js_arr_country = new Array();
<?php
    $country_fee_query = tep_db_query("select id,name from ". TABLE_COUNTRY_FEE ." where status='0' order by id");
    while($country_fee_array = tep_db_fetch_array($country_fee_query)){

      echo 'js_arr_country["'.$country_fee_array['name'].'"] = "'. $country_fee_array['name'] .'";'."\n";
    }
    tep_db_free_result($country_fee_query);
?>
	var js_arr_area = new Array();
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

      echo 'js_arr_area["'.$country_key.'"] = new Array();'."\n";
      foreach($country_value as $c_key=>$c_value){

        echo 'js_arr_area["'.$country_key.'"]["'.$c_key.'"] = "'. $c_value .'";'."\n";

      }

    }
?>
	var js_arr_city = new Array();
<?php
    $weight_count = $p_weight_total;
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

      echo 'js_arr_city["'.$country_key.'"] = new Array();'."\n";
      foreach($country_value as $c_key=>$c_value){

        echo 'js_arr_city["'.$country_key.'"]["'.$c_key.'"] = "'. $c_value .'";'."\n";

      }

    }
  ?>


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
    select 
    orders_status_id,
    nomail
    from ".TABLE_ORDERS_STATUS."
    where language_id = " . $languages_id . " 
    and orders_status_id IN (".join(',', $__orders_status_ids).")");

while($select_result = tep_db_fetch_array($select_query)){
  if($suu == 0){
    $select_select = $select_result['orders_status_id'];
    $suu = 1;
  }

  $osid = $select_result['orders_status_id'];
  //获取对应的邮件模板
  $mail_templates_query = tep_db_query("select site_id,title,contents from ". TABLE_MAIL_TEMPLATES ." where flag='ORDERS_STATUS_MAIL_TEMPLATES_".$osid."' and site_id='0'");
  $mail_templates_array = tep_db_fetch_array($mail_templates_query);
  tep_db_free_result($mail_templates_query);

  if($text_suu == 0){
    $select_text = $mail_templates_array['contents'];
    $select_title = $mail_templates_array['title'];
    $text_suu = 1;
    $select_nomail = $select_result['nomail'];
  }

  $mt[$osid][$mail_templates_array['site_id']?$mail_templates_array['site_id']:0] = $mail_templates_array['contents'];
  $mo[$osid][$mail_templates_array['site_id']?$mail_templates_array['site_id']:0] = $mail_templates_array['title'];
  $nomail[$osid] = $select_result['nomail'];
}


        // 输出订单邮件
        // title

//no mail
echo 'var nomail = new Array();'."\n";
foreach ($nomail as $oskey => $value){
  echo 'nomail['.$oskey.'] = "' . $value . '";' . "\n";
}
if($p_weight_total > 0){
?>
	var address_select = '';
	var address_list = new Array();
<?php 
    $products_weight_sum = 0;
    $products_weight_query = tep_db_query("select * from ". TABLE_ORDERS_PRODUCTS ." where orders_id='". tep_db_input($oID) ."'");
    while($products_weight_array = tep_db_fetch_array($products_weight_query)){
      $product_weight_query = tep_db_query("select * from ". TABLE_PRODUCTS ." where products_id='". $products_weight_array['products_id'] ."'");
      $product_weight_array = tep_db_fetch_array($product_weight_query);
      tep_db_free_result($product_weight_query);
      $products_weight_sum += $product_weight_array['products_weight']*$products_weight_array['products_quantity']; 
    }
    tep_db_free_result($products_weight_query);
    $add_array = array();
    $add_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_ORDERS ." where customers_id={$customer_id_flag} and billing_address='0' group by orders_id order by orders_id desc limit 0,1");
    $add_group_array = tep_db_fetch_array($add_group_query);
    tep_db_free_result($add_group_query);
    $add_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and billing_address='0'");
    $add_num = tep_db_num_rows($add_query);
    tep_db_free_result($add_query);
    
    if($add_num == 0){

      $oID_id = $add_group_array['orders_id'];
    }else{

      $oID_id = $oID;
    }
    $address_show_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID_id ."' and billing_address='0' order by id");
    $add_count = tep_db_num_rows($address_show_query);
    while($address_show_array = tep_db_fetch_array($address_show_query)){
      
      echo 'address_list["'. $address_show_array['name'] .'"] = "'. $address_show_array['value'] .'";';
      if(in_array($address_show_array['name'],$address_list_arr)){
        echo 'address_select += "'.$address_show_array['value'].'";'."\n";
      }
      $add_array[] = $address_show_array['value'];

    } 
    tep_db_free_result($address_show_query);
?>
	var js_show_list_country = '<?php echo $country_fee_id;?>';
	var js_show_list_area = '<?php echo $country_area_id;?>';
	var js_show_list_city = '<?php echo $country_city_id;?>';
  
<?php
}
?>

<?php
if($p_weight_total > 0){
?>
$(function() {
<?php
    if($add_count > 0 && $products_weight_sum > 0){
      if(!(isset($_GET['action']) && $_GET['action'] == 'update_order')){
?>
    address_show_list();
<?php
      }
    }
?>
  });
	var js_ne_orders_info_hide = "<?php echo TEXT_ADDRESS_INFO_HIDE;?>";
	var js_ne_orders_info_show = "<?php echo TEXT_ADDRESS_INFO_SHOW;?>";
<?php
  }
?>
  //todo:修改通性用
<?php
      $cpayment = payment::getInstance();
      $payment_array = payment::getPaymentList();
      foreach($payment_array[0] as $pay_key=>$pay_value){ 
        if(isset($payment_info_array)&&is_array($payment_info_array[$pay_key])){
          $payment_info = $payment_info_array[$pay_key];
        }else{
          $payment_info = $cpayment->admin_get_payment_info_comment($pay_value,$_SESSION['email_address'],$site_id_flag,1,(($customer_point['is_quited'] == '1')?2:0));
          $payment_info_array[$pay_key] = $payment_info;
        }
        if(is_array($payment_info)){

          switch($payment_info[0]){
          case 1: 
            $handle_fee_code = $cpayment->handle_calc_fee( payment::changeRomaji($pay_value,PAYMENT_RETURN_TYPE_CODE), 0);
            $pay_type_str = $pay_value;
            break;  
          }
        } 
      }
?>
	var js_ne_payment_type_str = '<?php echo $pay_type_str;?>';
	var js_ne_payment_fee_code_content = '<?php echo $handle_fee_code.TEXT_MONEY_SYMBOL;?>';
	var js_ne_payment_fee_money_symbol = '<?php echo TEXT_MONEY_SYMBOL;?>'

<?php
if($p_weight_total > 0){
?>
<?php
      if(!($_POST['address_option'] == 'new')){
?>
	var js_ready_option = true;
<?php 
	}else{
?>
	var js_ready_option = false;
<?php
	}
?>
<?php
      if(!isset($_GET['action'])){
?>
        var js_ready_option_action = true;
<?php
        }else{
?>
        var js_ready_option_action = false;
<?php
        }
?>
<?php
      if(isset($_POST['address_option']) && $_POST['address_option'] == 'new'){
?>
        var js_ready_option_new = true;
<?php
        }else{
?>
        var js_ready_option_new = false;
<?php
        }
      if(isset($_POST['address_option']) && $_POST['address_option'] == 'old'){
?>
        var js_ready_option_old = true;
<?php
        }else{
?>
        var js_ready_option_old = false;
<?php
        }
?>

<?php
}
?>
	var js_ne_calendar_date = '<?php echo date('Y-m-d', time())?>';

$(document).ready(function(){
<?php
if($p_weight_total > 0){
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
    $address_id_query = tep_db_query("select name,value from ". TABLE_ADDRESS_ORDERS ." where orders_id='". tep_db_input($oID) ."' and billing_address='0' and (name='". substr($country_fee_id,3) ."' or name='". substr($country_area_id,3) ."' or name='". substr($country_city_id,3) ."')");
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
  }
}
  ?> 
  $("select[name='payment_method']").change(function(){
    hidden_payment(1);
  });
});
	var js_ne_feth_date = '<?php echo ERROR_INPUT_RIGHT_DATE;?>';

</script>
<script language="javascript" src="includes/javascript/admin_edit_new_orders.js?v=<?php echo $back_rand_info?>"></script>
<?php
        if($p_weight_total > 0){
?>
<script language="javascript" src="includes/javascript/admin_edit_new_orders_weight.js?v=<?php echo $back_rand_info?>"></script>
<?php
        }
?>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
    </head>
    <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
    <?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
      <script language='javascript'>
        one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>', '<?php echo JS_TEXT_INPUT_ONETIME_PWD?>', '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>');
      </script>
        <?php }?>
        <!-- header -->
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
        <!-- header_eof -->
        <!-- body -->
        <?php echo tep_draw_form('edit_order', "edit_new_orders.php", tep_get_all_get_params(array('action','paycc')) . 'action=update_order', 'post', 'id="edit_order_id"'); ?> 
        <table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
        <tr>
        <td width="<?php echo BOX_WIDTH; ?>" valign="top">
        <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
        <!-- left_navigation -->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof -->
        </table>
        </td>
        <!-- body_text -->
        <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
        <div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <?php
        if ($action == 'edit') {
          if($orders_exit_flag == true){
            $order = new order($oID);
          }
          ?>
            <tr>
            <td width="100%">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
            <td class="pageHeading"><?php echo EDIT_NEW_ORDERS_CREATE_TITLE;?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            <td class="pageHeading" align="right">&nbsp;</td>
            </tr>
            <tr>
            <td colspan="3"><font color="red"><?php echo EDIT_NEW_ORDERS_CREATE_READ;?></font></td>
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
            <td class="main" bgcolor="#FFDDFF" height="25"><?php echo EDIT_ORDERS_UPDATE_NOTICE;?></td>
            <td class="main" bgcolor="#FFBBFF" width="10">&nbsp;</td>
            <td class="main" bgcolor="#FF99FF" width="10">&nbsp;</td>
            <td class="main" bgcolor="#FF77FF" width="10">&nbsp;</td>
            <td class="main" bgcolor="#FF55FF" width="120" align="center">&nbsp;</td>
            </tr>
            </table>
            <!-- End Update Block -->
            <br>
            <!-- Begin Addresses Block -->
            <span class="SubTitle"><?php echo MENUE_TITLE_CUSTOMER; ?></span>
            <table width="100%" border="0" class="dataTableRow" cellpadding="2" cellspacing="0">
            <tr>
            <td class="main" valign="top" width="30%"><?php echo ENTRY_SITE;?>:</td>
            <?php
              if(isset($_SESSION['sites_id_flag'])){
                $orders_site_name_query = tep_db_query("select name from ". TABLE_SITES ." where id='". $_SESSION['sites_id_flag'] ."'");
                $orders_site_name_array = tep_db_fetch_array($orders_site_name_query);
                $orders_site_name = $orders_site_name_array['name'];
                tep_db_free_result($orders_site_name_query);
              }
            ?>
            <td class="main" width="70%"><font color='#FF0000'><?php echo $orders_exit_flag == true ? tep_get_site_name_by_order_id($oID) : $orders_site_name;?></font></td>
            </tr>
            <tr>
            <td class="main" valign="top" width="30%"><?php echo EDIT_ORDERS_ID_TEXT;?></td>
            <td class="main" width="70%"><?php echo $oID;?></td>
            </tr>
            <tr>
            <td class="main" valign="top"><?php echo EDIT_ORDERS_DATE_TEXT;?></td>
            <td class="main"><?php echo $orders_exit_flag == true ? tep_date_long($order->info['date_purchased']) : tep_date_long(date('Y-m-d H:i:s'));?></td>
            </tr>
            <tr>
            <td class="main" valign="top"><?php echo EDIT_ORDERS_CUSTOMER_NAME;?></td>
            <td class="main"><?php echo $orders_exit_flag == true ?  tep_html_quotes(htmlspecialchars($order->customer['name'])) : tep_html_quotes(htmlspecialchars($_SESSION['lastname']).' '.htmlspecialchars($_SESSION['firstname'])); ?></td>
            </tr>
            <tr>
            <td class="main" valign="top"><?php echo EDIT_ORDERS_EMAIL;?></td>
            <td class="main"><font color="red"><?php echo $orders_exit_flag == true ? $order->customer['email_address'] : $_SESSION['email_address'];?></font></td>
            </tr>
            <!-- End Addresses Block -->
            <!-- Begin Payment Block -->
            <tr>
            <td class="main" valign="top"><?php echo EDIT_ORDERS_PAYMENT_METHOD;?></td>
            <td class="main">
            <?php
          //获取用户最近一次使用的支付方式
          //这里判断 订单商品是否有配送 如果有用自己的配送 如果没有用session的
              $products_weight_total = 0; //商品总重量
              $products_money_total = 0; //商品总价
              $cart_shipping_time = array(); //商品交易时间
              $products_address_query = tep_db_query("select * from ". TABLE_ORDERS_PRODUCTS ." where orders_id='". tep_db_input($oID) ."'");
              while($products_address_array = tep_db_fetch_array($products_address_query)){

                $products_weight_query = tep_db_query("select * from ". TABLE_PRODUCTS ." where products_id='". $products_address_array['products_id'] ."'");
                $products_weight_array = tep_db_fetch_array($products_weight_query);
                
                $cart_shipping_time[] = $products_weight_array['products_shipping_time'];
                $products_weight_total += $products_weight_array['products_weight']*$products_address_array['products_quantity'];
                $products_money_total += $products_address_array['final_price']*$products_address_array['products_quantity'];
                tep_db_free_result($products_weight_query);
              }
              tep_db_free_result($products_address_query); 
      $email_address_flag = $orders_exit_flag == true ? $order->customer['email_address'] : $_SESSION['email_address'];
      $cpayment = payment::getInstance();
      $payment_array = payment::getPaymentList();
      $pay_info_array = array();
      $pay_orders_id_array = array();
      $pay_type_array = array();
      $payment_negative_array = array();
      $payment_positive_array = array();
      foreach($payment_array[0] as $pay_key=>$pay_value){ 
        if(isset($payment_info_array)&&is_array($payment_info_array[$pay_key])){
          $payment_info = $payment_info_array[$pay_key];
        }else{
          $payment_info = $cpayment->admin_get_payment_info_comment($pay_value,$email_address_flag,$site_id_flag,1,(($customer_point['is_quited'] == '1')?2:0));
          $payment_info_array[$pay_key] = $payment_info;
        }
        if(is_array($payment_info)){

          switch($payment_info[0]){
          case 0: 
            $pay_orders_id_array[0] = $payment_info[1];
            $pay_type_array[0] = $pay_value;
            $payment_negative_array[] = $payment_array[1][$pay_key];
            break;
          case 1: 
            $pay_orders_id_array[1] = $payment_info[1];
            $pay_type_array[1] = $pay_value;
            $payment_positive_array[] = $payment_array[1][$pay_key];
            break;
          case 2: 
            $pay_orders_id_array[2] = $payment_info[1];
            $pay_type_array[2] = $pay_value;
            $payment_positive_array[] = $payment_array[1][$pay_key];
            break;
          case 3:
            $payment_negative_array[] = $payment_array[1][$pay_key];
            break;
          case 4:
            $payment_negative_array[] = $payment_array[1][$pay_key];
            break;
          case 5:
            $payment_zero = $payment_array[0][$pay_key];
            break;
          case 6:
            $payment_default = $payment_array[0][$pay_key];
            $payment_positive_array[] = $payment_array[1][$pay_key];
            break; 
          }
        }else{

           $payment_positive_array[] = $payment_array[1][$pay_key];
        }
      }     
      
      $payment_negative_array = array_unique($payment_negative_array);       
      $payment_positive_array = array_unique($payment_positive_array); 
      if($products_money_total != 0){
          $orders_payment_query = tep_db_query("select payment_method,orders_id from ". TABLE_ORDERS ." where customers_email_address='".  $email_address_flag ."' and site_id='".$site_id_flag."' and is_gray != '1' and is_guest = '0' order by orders_id desc"); 
          while($orders_payment_array = tep_db_fetch_array($orders_payment_query)){

            if($orders_payment_array['payment_method'] != ''){
              if($products_money_total > 0 && in_array($orders_payment_array['payment_method'],$payment_positive_array)){
                $payment_num = array_search($orders_payment_array['payment_method'],$payment_array[1]);
                $pay_method = $orders_payment_array['payment_method'];
                break;
              }
              if($products_money_total < 0 && in_array($orders_payment_array['payment_method'],$payment_negative_array)){
                $payment_num = array_search($orders_payment_array['payment_method'],$payment_array[1]);
                $pay_method = $orders_payment_array['payment_method'];
                break;
              }
            }
          }
          tep_db_free_result($orders_payment_query);
      }
       
          if($pay_orders_id_array[0] != ''){ 
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id_array[0]."' order by date_added desc"); 
            while($orders_status_history_array = tep_db_fetch_array($orders_status_history_query)){
              if($orders_status_history_array['comments']!=''){
                $orders_status_list_array = array();
                $orders_status_list_array = explode("\n",$orders_status_history_array['comments']);
                $status_flag = false;
                foreach($orders_status_list_array as $orders_value){

                  $orders_list_array = array();
                  $orders_list_array = explode(':',$orders_value);
                  if(trim($orders_list_array[1]) == '' && count($orders_list_array) > 1){

                    $status_flag = true; 
                    break;
                  }
                }
                if($status_flag == false){
                  if (tep_get_payment_customer_chk('',$_SESSION['customer_id']) == '2') {
                    $pay_info_array[0] = ''; 
                  } else {
                    $pay_info_array[0] = $orders_status_history_array['comments']; 
                  }
                  break;
                }
              }
            }
            tep_db_free_result($orders_status_history_query);
          }
          if($pay_orders_id_array[1] != ''){ 
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id_array[1]."' order by date_added desc"); 
            while($orders_status_history_array = tep_db_fetch_array($orders_status_history_query)){
              if($orders_status_history_array['comments']!=''){
                $orders_status_list_array = array();
                $orders_status_list_array = explode("\n",$orders_status_history_array['comments']);
                $status_flag = false;
                foreach($orders_status_list_array as $orders_value){

                  $orders_list_array = array();
                  $orders_list_array = explode(':',$orders_value);
                  if(trim($orders_list_array[1]) == '' && count($orders_list_array) > 1){

                    $status_flag = true; 
                    break;
                  }
                }
                if($status_flag == false){
                  $pay_info_array[1] = $orders_status_history_array['comments']; 
                  break;
                }
              }
            }
            tep_db_free_result($orders_status_history_query);
          }
          if($pay_orders_id_array[2] != ''){ 
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id_array[2]."' order by date_added desc"); 
            while($orders_status_history_array = tep_db_fetch_array($orders_status_history_query)){
              if($orders_status_history_array['comments']!=''){
                $orders_status_list_array = array();
                $orders_status_list_array = explode("\n",$orders_status_history_array['comments']);
                $status_flag = false;
                foreach($orders_status_list_array as $orders_value){

                  $orders_list_array = array();
                  $orders_list_array = explode(':',$orders_value);
                  if(trim($orders_list_array[1]) == '' && count($orders_list_array) > 1){

                    $status_flag = true; 
                    break;
                  }
                }
                if($status_flag == false){
                  $pay_info_array[2] = $orders_status_history_array['comments']; 
                  break;
                }
              }
            }
            tep_db_free_result($orders_status_history_query);
          }
          $code_payment_method = $payment_array[0][$payment_num];
          if($order->info['payment_method'] != ''){
            $code_payment_method =
            payment::changeRomaji($order->info['payment_method'],'code');
            $pay_method = payment::changeRomaji($order->info['payment_method'],'code');
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".$order->info['orders_id']."' order by date_added desc limit 0,1"); 
            while($orders_status_history_array = tep_db_fetch_array($orders_status_history_query)){
              if($orders_status_history_array['comments']!=''){
                $pay_comment = $orders_status_history_array['comments']; 
                break;
              }
            }
            tep_db_free_result($orders_status_history_query); 
          }
          $pay_method = isset($_SESSION['orders_update_products'][$_GET['oID']]['payment_method']) ? $_SESSION['orders_update_products'][$_GET['oID']]['payment_method'] : $pay_method; 
          $pay_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : $pay_method;
          if($pay_method == ''){
            if($products_money_total > 0){

              $pay_method = $payment_default;
            }
            if($products_money_total < 0){

              $pay_method = $pay_type_array[0];
            }
            if($products_money_total == 0){

              $pay_method = $payment_zero;
            }
          }else{

            $pay_method = $pay_method;
          } 

          $c_chk = tep_get_payment_customer_chk('',$_SESSION['customer_id']);
          $payment_code = payment::changeRomaji($pay_method,'code');
          $payment_select_str =  payment::makePaymentListPullDownMenu($payment_code,$_SESSION['sites_id_flag'],$c_chk);
          $paymentlist = true;
          if($payment_select_str!=''){
            echo $payment_select_str;
            if(isset($_SESSION['payment_empty_error'])
                &&$_SESSION['payment_empty_error']!=''){
              echo $_SESSION['payment_empty_error'];
              unset($_SESSION['payment_empty_error']);
            }
          }else{
            if(isset($_SESSION['payment_empty_error'])
                &&$_SESSION['payment_empty_error']!=''){
              echo $_SESSION['payment_empty_error'];
              unset($_SESSION['payment_empty_error']);
            }else{
              echo TEXT_NO_PAYMENT_ENABLED;
            }
            $paymentlist = false;
          }
          
          
          if($paymentlist){
          echo "\n".'<script language="javascript">'."\n"; 
          echo '$(document).ready(function(){'."\n";
          
          if($payment_code!=''){
          $cpayment->admin_show_payment_list($payment_code,$pay_info_array,$_SESSION['sites_id_flag'],$c_chk,'order',$_SESSION['email_address'],(($c_chk == '2')?false:true));
          }
          
          echo '});'."\n";
          echo '</script>'."\n";
      
          
          if(!isset($selections)){
            $selections = $cpayment->admin_selection();
          } 
          }
          echo '<tr><td class="main"></td><td class="main"><table>';
          if($paymentlist){
          foreach ($selections as $se){
            $pay_k = 0;
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
                  $field['message'] = $field['message'] != '' ? ADDRESS_ERROR_OPTION_ITEM_TEXT_NULL : ''; 
                }else{
                  $field['message'] = $field['message'] != '' ? ADDRESS_ERROR_OPTION_ITEM_TEXT_TYPE_WRONG : ''; 
                }
              }else{
                if(!$cpayment->admin_get_payment_buying_type(payment::changeRomaji($pay_method, 'code'),$field['title']) && $pay_k != 2){
                  $field['message'] = TEXT_REQUIRE;
                }
              }
              echo "<font color='red'>&nbsp;".$field['message']."</font>";
              echo "</td>";
              echo "</tr>";
              $pay_k++;
           } 
         }
          }
          echo '</table></td></tr>';
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
            <!-- End Payment Block -->
            <!-- Begin Trade Date Block -->
            <?php  
      // start
    //计算配送费用 
    $country_fee_array = array();
    $country_fee_id_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
    while($country_fee_id_array = tep_db_fetch_array($country_fee_id_query)){

      $country_fee_array[$country_fee_id_array['fixed_option']] = $country_fee_id_array['name_flag'];
    }
    tep_db_free_result($country_fee_id_query);
    $weight = $products_weight_total;

    $shipping_orders_array = array();
    $shipping_address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and billing_address='0'");
    while($shipping_address_orders_array = tep_db_fetch_array($shipping_address_orders_query)){

      $shipping_orders_array[$shipping_address_orders_array['name']] = $shipping_address_orders_array['value'];
    }
    tep_db_free_result($shipping_address_orders_query);
    if(empty($shipping_orders_array)){
      $shipping_orders_array = $add_array;
    }

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
  }elseif($address_country_num > 0 && $op_key == $country_fee_array[1]){
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
  $shipping_money_total = $products_money_total;
  
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
    $shipping_fee = $shipping_money_total > $free_value ? 0 : $weight_fee;
    if($weight == 0){
      $shipping_fee = 0;
    }
              // end
  //根据订单商品表中的商品来生成取引时间 
   
  $cart_shipping_time = array_unique($cart_shipping_time); 
  
  $products_num = count($cart_shipping_time); 
  $shipping_time_array = array();
  foreach($cart_shipping_time as $cart_shipping_value){

    $shipping_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where id=".$cart_shipping_value);
    $shipping_array = tep_db_fetch_array($shipping_query);
    $shipping_time_array['work'][] = unserialize($shipping_array['work']);
    $shipping_time_array['db_set_day'][] = $shipping_array['db_set_day'];
    $shipping_time_array['shipping_time'][] = $shipping_array['shipping_time'];

  }

  //work
  $shipping_time_start = array();
  $shipping_time_end = array();
  foreach($shipping_time_array['work'] as $shipping_time_key=>$shipping_time_value){

    foreach($shipping_time_value as $k=>$val){

      $shipping_time_start[$shipping_time_key][] = $val[0]; 
      $shipping_time_end[$shipping_time_key][] = $val[1];
    } 
  }
   
  $ship_array = array();
  $ship_time_array = array();
  foreach($shipping_time_start as $shipping_key=>$shipping_value){
    foreach($shipping_value as $sh_key=>$sh_value){
      
      $sh_start_array = explode(':',$sh_value);
      $sh_end_array = explode(':', $shipping_time_end[$shipping_key][$sh_key]);
      for($i = (int)$sh_start_array[0];$i <= (int)$sh_end_array[0];$i++){
        if(isset($ship_time_array[$shipping_key][$i]) && $ship_time_array[$shipping_key][$i] != ''){
          $ship_time_array[$shipping_key][$i] .= '|'.$sh_value.','.$shipping_time_end[$shipping_key][$sh_key];
        }else{
          $ship_time_array[$shipping_key][$i] = $sh_value.','.$shipping_time_end[$shipping_key][$sh_key]; 
        }
      } 
    }  
  }

  $ship_count_array = array();
  foreach($ship_time_array as $ship_key=>$ship_value){

    foreach($ship_value as $ship_k=>$ship_v){
      $ship_temp_array = array();
      $ship_temp_array = explode('|',$ship_v);
      $ship_time_array[$ship_key][$ship_k] = $ship_temp_array;
    }
    $ship_count_array[$ship_key] = count($ship_value);
  } 

  $ship_min_value = array_search(min($ship_count_array),$ship_count_array);
  $shipp_time_array = array();
  foreach($ship_time_array[$ship_min_value] as $ship_hour_key=>$ship_hour_value){

    foreach($ship_hour_value as $ship_hour_k=>$ship_hour_v){

      $ship_hour_array = explode(',',$ship_hour_v);
      foreach($ship_time_array as $ship_t_k=>$ship_t_v){

          if($ship_t_k == $ship_min_value){continue;}
            if(isset($ship_t_v[$ship_hour_key])){
   
            foreach($ship_t_v[$ship_hour_key] as $ship_tt_k=>$ship_tt_v){

               $ship_hour_temp_array = array();
               $ship_hour_temp_array = explode(',',$ship_tt_v); 
               if($ship_hour_array[0] <= $ship_hour_temp_array[1]){

                 $ship_start_time = max($ship_hour_array[0],$ship_hour_temp_array[0]); 
                 $ship_end_time = min($ship_hour_array[1],$ship_hour_temp_array[1]);
                 $ship_start_time_value = str_replace(':','',$ship_start_time);
                 $ship_end_time_value = str_replace(':','',$ship_end_time);
                 if(!in_array($ship_start_time.','.$ship_end_time,$shipp_time_array[$ship_hour_key]) && (int)$ship_start_time_value < (int)$ship_end_time_value){
                   $shipp_time_array[$ship_hour_key][] = $ship_start_time.','.$ship_end_time;
                   
                 }
               }
            }
          }
      }
    }
  }

  $shipp_flag_array = $ship_time_array[$ship_min_value];

  foreach($shipp_time_array as $shipp_flag_k=>$shipp_flag_v){

    $shipp_temp_start_array = array();
    $shipp_temp_end_array = array(); 
   if(isset($shipp_flag_array[$shipp_flag_k])){
    foreach($shipp_flag_array[$shipp_flag_k] as $shipp_flag_key=>$shipp_flag_value){
 
      $shipp_temp_all_array = array();
      $shipp_temp_all_array = explode(',',$shipp_flag_value);
      $shipp_temp_start_array[] = $shipp_temp_all_array[0];
      $shipp_temp_end_array[] = $shipp_temp_all_array[1];
    }
    $shipp_temp_start_num = str_replace(':','',min($shipp_temp_start_array));
    $shipp_temp_end_num = str_replace(':','',max($shipp_temp_end_array));
    foreach($shipp_flag_v as $shipp_f_k=>$shipp_f_v){

      $shipp_t_all_array = array();
      $shipp_t_all_array = explode(',',$shipp_f_v);
      $shipp_t_start_num = str_replace(':','',$shipp_t_all_array[0]);
      $shipp_t_end_num = str_replace(':','',$shipp_t_all_array[1]);

      if(!($shipp_t_start_num >= $shipp_temp_start_num && $shipp_t_end_num <= $shipp_temp_end_num)){

        unset($shipp_time_array[$shipp_flag_k][$shipp_f_k]);
      }
    }
   }else{

     unset($shipp_time_array[$shipp_flag_k]);
   }

  }
   
  $ship_new_array = array(); 
  $shipp_array = array();
  foreach($shipp_time_array as $shipp_time_k=>$shpp_time_v){

      $ship_new_str = implode('|',$shpp_time_v);
      $ship_new_array[] = $ship_new_str;
      $shipp_array[] = $shipp_time_k;

  }

  foreach($ship_new_array as $_s_key=>$_s_value){
      $s_temp_array = explode('|',$_s_value);    
      sort($s_temp_array);
      $ship_new_array[$_s_key] = implode('|',$s_temp_array); 
  } 

  foreach($ship_new_array as $s_key=>$s_val){
    $ss_array = array();
    $ss_array = explode(',',$s_val);
    $ss_start = str_replace(':','',$ss_array[0]);
    $ss_end = str_replace(':','',$ss_array[1]);
    if($ss_start > $ss_end){

      unset($ship_new_array[$s_key]);
      unset($shipp_array[$s_key]);
    }
  }

  $max_time_str_old = implode('||',$shipp_array);
  $min_time_str_old = implode('||',$ship_new_array);
  //当日起几日后可以收货
  $db_set_day = max($shipping_time_array['db_set_day']);
  //可选收货期限
  $shipping_time = max($shipping_time_array['shipping_time']);

  $now_time_date = date('Y-m-d',strtotime("+".$shipping_time." minutes"));
  $now_time_hour = date('Hi',strtotime("+".$shipping_time." minutes"));
  $now_time = date('H:i',strtotime("+".$db_set_day." minutes"));
  $now_time = str_replace(':','',$now_time); 
  $now_flag = false;
  if(date('Ymd') == date('Ymd',strtotime("+".$shipping_time." minutes"))){
    $now_time_end = date('H:i',strtotime("+".$shipping_time." minutes"));
    $now_time_end = str_replace(':','',$now_time_end);
    $now_flag = true;
  }

  $ship_new_end_array = array();
  $ship_new_end_array = $ship_new_array;
  $shipp_end_array = array();
  $shipp_end_array = $shipp_array;

  foreach($ship_new_array as $s_k=>$s_v){
    $ss_array = array();
    $ss_array = explode(',',$s_v);
    $ss_start = str_replace(':','',$ss_array[0]);
    $ss_end = str_replace(':','',$ss_array[1]);

    if($ss_end > $now_time_hour){

      unset($ship_new_end_array[$s_k]);
      unset($shipp_end_array[$s_k]);
    }
    if($ss_start > $ss_end || $ss_start < $now_time || ($now_flag == true && $ss_end > $now_time_end)){

      unset($ship_new_array[$s_k]);
      unset($shipp_array[$s_k]);
    }
  }


  $shipping_time_nows = array_search(min($shipp_array),$shipp_array);
  $shipping_time_nows_str = $ship_new_array[$shipping_time_nows];
  $shipping_time_nows_array = explode('|',$shipping_time_nows_str);
  $shipping_time_nows_min = min($shipping_time_nows_array);
  //----------
  if(count($shipping_time_array['work']) == 1){
    $shi_time_array = array();
    foreach($shipping_time_start[0] as $shi_key=>$shi_value){

      $shi_start_array = explode(':',$shi_value);
      $shi_end_array = explode(':',$shipping_time_end[0][$shi_key]);

      for($shi_i = (int)$shi_start_array[0];$shi_i <= (int)$shi_end_array[0];$shi_i++){

        if(isset($shi_time_array[$shi_i]) && $shi_time_array[$shi_i] != ''){

          
          $shi_time_array[$shi_i] .= '|'.$shi_value.','.$shipping_time_end[0][$shi_key]; 
        }else{

          $shi_time_array[$shi_i] = $shi_value.','.$shipping_time_end[0][$shi_key]; 
        }
      }
    }

    foreach($shi_time_array as $_s_key=>$_s_value){
      $s_temp_array = explode('|',$_s_value);    
      sort($s_temp_array);
      $shi_time_array[$_s_key] = implode('|',$s_temp_array); 
    }
   $max_time_str_old = implode('||',array_keys($shi_time_array));
    $min_time_str_old = implode('||',$shi_time_array);


    $now_time_date = date('Y-m-d',strtotime("+".$shipping_time." minutes"));
    $now_time_hour = date('Hi',strtotime("+".$shipping_time." minutes"));
    $now_time = date('H:i',strtotime("+".$db_set_day." minutes"));
    $now_time = str_replace(':','',$now_time);
    $now_flag = false;
    if(date('Ymd') == date('Ymd',strtotime("+".$shipping_time." minutes"))){
      $now_time_end = date('H:i',strtotime("+".$shipping_time." minutes"));
      $now_time_end = str_replace(':','',$now_time_end);
      $now_flag = true;
    }

    $shi_time_end_array = array();
    $shi_time_end_array = $shi_time_array;

    foreach($shi_time_array as $s_k=>$s_v){
      $ss_array = array();
      $ss_end_array = array();
      $ss_str = '';
      $ss_array = explode('|',$s_v);
      $ss_end_array = explode('|',$s_v);

      foreach($ss_array as $ss_k=>$ss_v){

        $now_array = array();
        $now_array = explode(',',$ss_v);
        $ss_start = str_replace(':','',$now_array[0]);
        $ss_end = str_replace(':','',$now_array[1]); 

        if($ss_end > $now_time_hour){
           
            unset($ss_end_array[$ss_k]);
        }

        if($ss_start < $now_time || ($now_flag == true && $ss_end > $now_time_end)){
 
          unset($ss_array[$ss_k]);
        }else{
          $now_hour = date('H');
          if($s_k <  $now_hour){

            unset($ss_array[$ss_k]);
          }

        }
      }
      $ss_str = implode('|',$ss_array);
      $ss_end_str = implode('|',$ss_end_array);
      $shi_time_array[$s_k] = $ss_str; 
      $shi_time_end_array[$s_k] = $ss_end_str;
    }    

    foreach($shi_time_array as $shi_k=>$shi_v){

      if($shi_v == ''){

        unset($shi_time_array[$shi_k]);
      }

    }

    foreach($shi_time_end_array as $shi_end_k=>$shi_end_v){
     
       if($shi_end_v == ''){
     
           unset($shi_time_end_array[$shi_end_k]);
       }
     
    } 
  }
  

  //可配送时间区域
  $work_start = $max_time_str;
  $work_end = $min_time_str;
  $work_start_old = $max_time_str_old;
  $work_end_old = $min_time_str_old;
  $work_start_exit = $max_time_end_str;
  $work_end_exit = $min_time_end_str;
 
  //可配送时间区域
  //获取更新后订单的取引时间
if($orders_exit_flag == true){
  $orders_time_query = tep_db_query("select torihiki_date,torihiki_date_end from ". TABLE_ORDERS ." where orders_id='". $oID ."'");
  $orders_time_array = tep_db_fetch_array($orders_time_query);
  tep_db_free_result($orders_time_query);
}
  if($orders_time_array['torihiki_date'] != '0000-00-00 00:00:00' && $orders_time_array['torihiki_date_end'] != '0000-00-00 00:00:00' && $orders_exit_flag == true){
    $orders_temp_time_start = explode(' ',$orders_time_array['torihiki_date']);
    $work_start = substr($orders_temp_time_start[1],0,5);
    $orders_temp_time_end = explode(' ',$orders_time_array['torihiki_date_end']);
    $work_end = substr($orders_temp_time_end[1],0,5);
    $date_orders = date('Y-m-d',strtotime($orders_time_array['torihiki_date']));
  }else{

  if(count($shipping_time_array['work']) == 1){
    $shipping_time_array_key = array_keys($shi_time_array);
    $shipping_time_array_min = min($shipping_time_array_key);
    $shipping_time_now = $shi_time_array[$shipping_time_array_min];  
    $shipping_time_now_array = explode('|',$shipping_time_now);
    $shipping_time_hour = min($shipping_time_now_array);
    $shipping_time_hour_array = explode(',',$shipping_time_hour);
    $work_start = $shipping_time_hour_array[0];
    $work_end = $shipping_time_hour_array[1]; 
  }else{
    $shipping_time_hour_array = explode(',',$shipping_time_nows_min);
    $work_start = $shipping_time_hour_array[0];
    $work_end = $shipping_time_hour_array[1];  
  }
     $date_time_temp = date('Y-m-d H:i',strtotime("+ ".$db_set_day."minute"));
     $work_start_temp = date('Y-m-d').' '.$work_start;
    if(($work_start == '' && $work_end == '') || $work_start_temp < $date_time_temp){

      $shipping_time_default_array = $shipping_time_array['work'][0]; 
      $default_num = 0;
      $default_temp = $shipping_time_default_array[0][0];
      foreach($shipping_time_default_array as $default_key=>$default_value){
        if($default_value[0] < $default_temp){
          $default_num = $default_key; 
        } 
      }
      $default_time = $shipping_time_default_array[$default_num];
      $work_start = $default_time[0];
      $work_end = $default_time[1];
      $db_set_day = max($shipping_time_array['db_set_day']);
      $date_orders = date('Y-m-d',strtotime("+ ".$db_set_day."minute"));
      $date_orders = date('Y-m-d',strtotime("+1 days")); 
    }else{
      //当日起几日后可以收货
      $db_set_day = max($shipping_time_array['db_set_day']);
      $date_orders = date('Y-m-d',strtotime("+ ".$db_set_day."minute"));
    }

  }
  $work_start_array = explode(':',$work_start);
  $work_end_array = explode(':',$work_end);
  $work_start_hour = $work_start_array[0]; //开始时
  $work_start_hour = isset($_SESSION['orders_update_products'][$_GET['oID']]['hour']) ? $_SESSION['orders_update_products'][$_GET['oID']]['hour'] : $work_start_hour;
  $work_start_min = $work_start_array[1]; //开始分
  $work_end_hour = $work_end_array[0]; //结束时
  $work_end_hour = isset($_SESSION['orders_update_products'][$_GET['oID']]['hour_1']) ? $_SESSION['orders_update_products'][$_GET['oID']]['hour_1'] : $work_end_hour;
  $work_end_min = $work_end_array[1]; //结束分

  //可选收货期限
  $shipping_time = max($shipping_time_array['shipping_time']); 
  //生成时间下拉框
  $hour_str = '<select name="start_hour" id="hour" onchange="check_hour(this.value);">';
  for($h_i = 0;$h_i <= 23;$h_i++){

    $h_str = $h_i < 10 ? '0'.$h_i : $h_i;
    if($h_str == $work_start_hour){

      $selected = ' selected';
    }else{

      $selected = '';
    }
    $hour_str .= '<option value="'.$h_str.'"'.$selected.'>'.$h_str.'</option>';
  } 
  $hour_str .= '</select>';
  $work_min_temp = substr($work_start_min,0,1);
  $work_min_temp = isset($_SESSION['orders_update_products'][$_GET['oID']]['min']) ? $_SESSION['orders_update_products'][$_GET['oID']]['min'] : $work_min_temp;
  $min_str = '<select name="start_min" id="min" onchange="check_min(this.value);">';
  for($m_i = 0;$m_i <= 5;$m_i++){

    if($m_i == $work_min_temp){

      $selected = ' selected';
    }else{

      $selected = '';
    }
    $min_str .= '<option value="'.$m_i.'"'.$selected.'>'.$m_i.'</option>';
  } 
  $min_str .= '</select>';
  $min_str_temp = substr($work_start_min,1,1);;
  $min_str_temp = isset($_SESSION['orders_update_products'][$_GET['oID']]['min_1']) ? $_SESSION['orders_update_products'][$_GET['oID']]['min_1'] : $min_str_temp;
  $min_str_start = '<select name="start_min_1" id="min_1" onchange="check_min_1(this.value);">';
  for($m_i_1 = 0;$m_i_1 <= 9;$m_i_1++){

    if($m_i_1 == $min_str_temp){

      $selected = ' selected';
    }else{

      $selected = '';
    }
    $min_str_start .= '<option value="'.$m_i_1.'"'.$selected.'>'.$m_i_1.'</option>';
  } 
  $min_str_start .= '</select>';
  
  $hour_str_1 = '<select name="end_hour" id="hour_1" onchange="check_hour_1(this.value);">';
  if(strlen($work_start_hour) == 2 && substr($work_start_hour,0,1) == 0){
   
    $work_min_start = substr($work_start_hour,1,1);
  }else{
   
    $work_min_start = $work_start_hour;
  }
  for($h1_i = $work_min_start;$h1_i <= 23;$h1_i++){

    $h1_str = $h1_i < 10 ? '0'.$h1_i : $h1_i;
    if($h1_str == $work_end_hour){

      $selected = ' selected';
    }else{

      $selected = '';
    }
    $hour_str_1 .= '<option value="'.$h1_str.'"'.$selected.'>'.$h1_str.'</option>';
  } 
  $hour_str_1 .= '</select>';

  $min_str_1_temp = substr($work_end_min,0,1); 
  $min_str_1_temp = isset($_SESSION['orders_update_products'][$_GET['oID']]['min_end']) ? $_SESSION['orders_update_products'][$_GET['oID']]['min_end'] : $min_str_1_temp;
  $min_str_1 = '<select name="end_min" id="min_end" onchange="check_end_min(this.value);">';
  $work_min_temp = $work_start_hour < $work_end_hour ? 0 : $work_min_temp;
  for($m1_i = $work_min_temp;$m1_i <= 5;$m1_i++){

    if($m1_i == $min_str_1_temp){

      $selected = ' selected';
    }else{

      $selected = '';
    }
    $min_str_1 .= '<option value="'.$m1_i.'"'.$selected.'>'.$m1_i.'</option>';
  } 
  $min_str_1 .= '</select>';
  $min_str_end_temp = substr($work_end_min,1,1);
  $min_str_end_temp = isset($_SESSION['orders_update_products'][$_GET['oID']]['min_end_1']) ? $_SESSION['orders_update_products'][$_GET['oID']]['min_end_1'] : $min_str_end_temp;
  $min_str_end = '<select name="end_min_1" id="min_end_1">';
  $work_start_min_temp = $min_str_1_temp == $work_min_temp ? $min_str_temp : 0;
  $work_start_min_temp = $work_start_hour < $work_end_hour ? 0 : $work_start_min_temp;
  for($m1_i_1 = $work_start_min_temp;$m1_i_1 <= 9;$m1_i_1++){

    if($m1_i_1 == $min_str_end_temp){

      $selected = ' selected';
    }else{

      $selected = '';
    }
    $min_str_end .= '<option value="'.$m1_i_1.'"'.$selected.'>'.$m1_i_1.'</option>';
  } 
  $min_str_end .= '</select>';
  //获取手料费
  $payment_modules = payment::getInstance($order->info['site_id']); 
  if($code_payment_method==''||$code_payment_method!=$payment_code){
    $code_payment_method = $payment_code;
  }
  $code_payment_method = isset($code_payment_method) && $code_payment_method != '' ? $code_payment_method : 'buying';
  if($paymentlist){
  $handle_fee_code = $payment_modules->handle_calc_fee( payment::changeRomaji($code_payment_method,PAYMENT_RETURN_TYPE_CODE), $shipping_money_total);
  }
  $fetch_date_array = explode('-', $date_orders); 
            ?>
            <tr> 
            <td class="main" valign="top"><?php echo EDIT_ORDERS_FETCHTIME;?></td>
            <td class="main"> 
            <div style="float:left;"> 
              <select name="fetch_year" id="fetch_year" onchange="change_fetch_date();">
             <?php
                $fetch_date_array[0] = isset($_SESSION['orders_update_products'][$_GET['oID']]['fetch_year']) ? $_SESSION['orders_update_products'][$_GET['oID']]['fetch_year'] : $fetch_date_array[0];
                $default_fetch_year = (isset($_POST['fetch_year']))?$_POST['fetch_year']:$fetch_date_array[0]; 
                for ($f_num = 2006; $f_num <= 2050; $f_num++) {
                 if($default_fetch_year == $f_num || $f_num >= date('Y',time())){
			 echo '<option value="'.$f_num.'"'.(($default_fetch_year == $f_num)?' selected':'').'>'.$f_num.'</option>'; 
                 }
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
              <input type="hidden" id="date_orders" size="15" value="<?php echo $date_orders;?>"> 
              <a href="javascript:void(0);" onclick="open_calendar();" class="dpicker"></a> 
              <input type="hidden" id="date_order" name="date_orders" value="<?php echo $date_orders;?>">
              <input type="hidden" name="toggle_open" value="0" id="toggle_open"> 
              <div class="yui3-u" id="new_yui3">
              <div id="mycalendar"></div> 
              </div>
            </div>
            <?php echo '&nbsp;'.$hour_str.'&nbsp;'.TEXT_HOUR.'&nbsp;'.$min_str.$min_str_start.'&nbsp;'.TEXT_MIN.'&nbsp;'.TEXT_TIME_LINK.'&nbsp;'.$hour_str_1.'&nbsp;'.TEXT_HOUR.'&nbsp;'.$min_str_1.$min_str_end.'&nbsp;'.TEXT_MIN.'&nbsp;';
            ?>
            </td>
            </tr>
            <?php 
              // 住所信息
              if($products_weight_total > 0){
                $address_style = isset($address_style) && $address_style != '' ? $address_style : 'display: none;';
                $old_checked = !isset($_POST['address_option']) || $_POST['address_option'] == 'old' ? 'checked' : '';
                $new_checked = isset($_POST['address_option']) && $_POST['address_option'] == 'new' ? 'checked' : '';
                $address_historys_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where customers_id='".$customer_id_flag ."'");
                $address_historys_num = tep_db_num_rows($address_historys_query);
                tep_db_free_result($address_historys_query);
                if($address_historys_num == 0 && !isset($_POST['address_option'])){
                    $old_checked = '';
                    $new_checked = 'checked';
                
            ?>
              <script type="text/javascript">
              $(document).ready(function(){
                address_option_show('new'); 
              }); 
              </script>

            <?php
               }
            ?>
            <tr>
            <td class="main" valign="top"><a href="javascript:void(0);" onclick="address_show();"><font color="blue"><b><u><span id="address_font"><?php echo TEXT_SHIPPING_ADDRESS;?></span></u></b></font></a></td>
            <td class="main">
            </td>
            </tr> 
            <tr><td colspan="2"><table width="100%" border="0" cellpadding="2" cellspacing="0" id="address_show_id" style="<?php echo $address_style;?>">
        <tr>
        <td class="main" width="30%">&nbsp;</td>
        <td class="main" width="70%"><table border="0" cellpadding="0" cellspacing="0">
        <tr><td>
        <input type="radio" name="address_option" value="old" style="margin: 0 4px 2px 0;" onClick="address_option_show('old');address_option_list(address_first_num);address_clear_error();" <?php echo $old_checked;?>></td><td><?php echo TABLE_OPTION_OLD; ?></td><td>
        <input type="radio" name="address_option" value="new" style="margin: 0 4px 2px 15px;" onClick="address_option_show('new');" <?php echo $new_checked;?>></td><td><?php echo TABLE_OPTION_NEW; ?></td>  
        </tr>
        </table>
        </td>
      </tr>
      <tr id="address_list_id">
<td class="main" width="30%"><?php echo TABLE_ADDRESS_SHOW; ?></td>
<td class="main" width="70%">
<div style="display:none" id='edit_order_send_mail'></div>
<select name="address_show_list" id="address_show_list" onChange="address_option_list(this.value);">
<option value="">--</option>
</select>
</td></tr>
            <?php 
                $ad_option->render('');
            ?>
            </table>
            </td>
            </tr>
            <?php
              }
            ?> 
            <tr>
            <td class="main">
            <?php echo $orders_exit_flag == true ? $order->tori['houhou'] : '';?>             
            <input type="hidden" name="update_viladate" value="true">
            <input type="hidden" name="update_customer_name" size="25" value="<?php echo htmlspecialchars($orders_exit_flag == true ?  tep_html_quotes($order->customer['name']) : tep_html_quotes($_SESSION['lastname'].' '.$_SESSION['firstname'])); ?>">
            <input type="hidden" name="update_customer_email_address" size="45" value="<?php echo $orders_exit_flag == true ? $order->customer['email_address'] : $_SESSION['email_address']; ?>">
            <input type="hidden" name='update_info_payment_method' size='25' value='<?php echo $orders_exit_flag == true ? $order->info['payment_method'] : payment::changeRomaji($pay_method,'code'); ?>'>
            <input type="hidden" name='update_tori_torihiki_date' size='25' value='<?php echo $orders_exit_flag == true ? $order->tori['date'] : $date_orders.' '.$work_start_hour.':'.$work_start_min.':00&nbsp;_&nbsp;'.$work_end_hour.':'.$work_end_min.':00'; ?>'>
            <input type="hidden" name='update_tori_torihiki_houhou' size='45' value='<?php echo $orders_exit_flag == true ? $order->tori['houhou'] : ''; ?>'>

            <input name="update_customer_company" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->customer['company']) : tep_html_quotes($_SESSION['company']); ?>">
            <input name="update_delivery_company" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->delivery['company']) : tep_html_quotes($_SESSION['company']); ?>">
            <input name="update_delivery_name" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->delivery['name']) : ''; ?>">
            <input name="update_customer_name_f" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->customer['name_f']) : ''; ?>">
            <input name="update_delivery_name_f" size="25" type='hidden' value="<?php echo tep_html_quotes($order->delivery['name_f']); ?>">
            <input name="update_customer_street_address" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->customer['street_address']) : tep_html_quotes($_SESSION['street_address']); ?>">
            <input name="update_delivery_street_address" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->delivery['street_address']) : tep_html_quotes($_SESSION['street_address']); ?>">
            <input name="update_customer_suburb" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->customer['suburb']) : tep_html_quotes($_SESSION['suburb']); ?>">
            <input name="update_delivery_suburb" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->delivery['suburb']) : tep_html_quotes($_SESSION['suburb']); ?>">
            <input name="update_customer_city" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->customer['city']) : tep_html_quotes($_SESSION['city']); ?>">
            <input name="update_delivery_city" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->delivery['city']) : tep_html_quotes($_SESSION['city']); ?>">
            <input name="update_customer_state" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->customer['state']) : tep_html_quotes($_SESSION['state']); ?>">
            <input name="update_delivery_state" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->delivery['state']) : tep_html_quotes($_SESSION['state']); ?>">
            <input name="update_customer_postcode" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? $order->customer['postcode'] : $_SESSION['postcode']; ?>">
            <input name="update_delivery_postcode" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? $order->delivery['postcode'] : $_SESSION['postcode']; ?>">
            <input name="update_customer_country" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->customer['country']) : tep_html_quotes($_SESSION['country']); ?>">
            <input name="update_delivery_country" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? tep_html_quotes($order->delivery['country']) : tep_html_quotes($_SESSION['country']); ?>">
            <input name="update_customer_telephone" size="25" type='hidden' value="<?php echo $orders_exit_flag == true ? $order->customer['telephone'] : $_SESSION['telephone']; ?>">
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
            $order->products[$index] = array('qty' => $orders_products['products_quantity'],
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
                    'option_info' => @unserialize($attributes['option_info']),                    
                    'option_group_id' => $attributes['option_group_id'],                    
                    'option_item_id' => $attributes['option_item_id'],                    
                    'price' => $attributes['options_values_price']);
                $subindex++;
              }
            }
            $index++;
          }

          ?>
            <?php // Version without editable names & prices ?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2" id="show_product_list">
            <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent" colspan="2" width="35%"><?php echo TABLE_HEADING_NUM_PRO_NAME;?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENICY;?></td>
            <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_BEFORE;?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_AFTER;?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_BEFORE;?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_AFTER;?></td>
            </tr>

<?php
          echo '<div id="popup_window" class="popup_window"></div>';
          $only_buy= true;
          $products_name_array = array();
          $products_id_array = array();
          $orders_products_array = array();
          $orders_products_list = '';
          for ($k=0; $k<sizeof($order->products); $k++) {
           $orders_products_array[] = $order->products[$k]['orders_products_id']; 
          }
          $orders_products_list = implode('|||',$orders_products_array);
          for ($i=0; $i<sizeof($order->products); $i++) {
            $orders_products_id_query = tep_db_query("select products_id from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='".$order->products[$i]['orders_products_id']."'");
            $orders_products_id_array = tep_db_fetch_array($orders_products_id_query);
            tep_db_free_result($orders_products_id_query);
            $option_item_order_sql = "select it.id,it.type item_type,it.option item_option from ".TABLE_PRODUCTS."
      p,".TABLE_OPTION_ITEM." it 
      where p.products_id = '".(int)$orders_products_id_array['products_id']."' 
      and p.belong_to_option = it.group_id 
      and it.status = 1
      order by it.sort_num,it.title";
            $option_item_order_query = tep_db_query($option_item_order_sql);
            $item_type_array = array();
            $item_option_array = array(); 
            while($show_option_row_item = tep_db_fetch_array($option_item_order_query)){
              $item_type_array[$show_option_row_item['id']] = $show_option_row_item['item_type'];
              $item_option_array[$show_option_row_item['id']] = $show_option_row_item['item_option']; 
            }
            $orders_products_id = $order->products[$i]['orders_products_id'];
            if(!tep_get_bflag_by_product_id($orders_products_id)){
              $only_buy= false;
            }
            $orders_weight_query = tep_db_query("select products_id from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='". $order->products[$i]['orders_products_id'] ."'");
            $orders_weight_array = tep_db_fetch_array($orders_weight_query);
            tep_db_free_result($orders_weight_query);
            if(isset($products_qty_array) && !empty($products_qty_array)){

              $products_qty_num = $products_qty_array[$orders_weight_array['products_id']];
            }else{

              $products_qty_num = $order->products[$i]['qty'];
            }
            if(isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['qty'])){

              $products_qty_num = $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['qty'];
            }
            $order->products[$i]['qty'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['qty']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['qty'] : $order->products[$i]['qty'];
            $op_info_str = '';
            if (sizeof($order->products[$i]['attributes']) > 0) {
              $op_info_array = array();
              for ($i_num = 0; $i_num < sizeof($order->products[$i]['attributes']); $i_num++) {
                $op_info_array[] = $order->products[$i]['attributes'][$i_num]['id'];
              }
              $op_info_str = implode('|||', $op_info_array);
            }
            $RowStyle = "dataTableContent";
            $products_name_array[] = $order->products[$i]['name'];
            $products_id_array[] = $orders_weight_array['products_id'];
            $less_op_single = tep_check_less_option_product($orders_products_id);
            echo '    <tr class="dataTableRow" id="products_list_'.$orders_products_id.'">' . "\n" .
              '      <td class="' . $RowStyle . '" align="left" valign="top" width="8%" style="min-width:100px;">'
              . "<input type='hidden' id='update_products_qty_$orders_products_id' value='" . $products_qty_num . "'>";
            if ($less_op_single) {
              echo "<input type='text' class='update_products_qty' style='background: none repeat scroll 0 0 #CCCCCC' readonly id='update_products_new_qty_$orders_products_id' name='update_products[$orders_products_id][qty]' size='2' value='" .  $products_qty_num . "'>";
            } else {
              echo "<input type='text' class='update_products_qty' id='update_products_new_qty_$orders_products_id' name='update_products[$orders_products_id][qty]' size='2' value='" .  $products_qty_num . "' onkeyup=\"clearLibNum(this);recalc_order_price('".$oID."', '".$orders_products_id."', '2', '".$op_info_str."','".$orders_products_list."');price_total('".TEXT_MONEY_SYMBOL."');\" onchange=\"clearLibNum(this);recalc_order_price('".$oID."', '".$orders_products_id."', '2', '".$op_info_str."','".$orders_products_list."');price_total('".TEXT_MONEY_SYMBOL."');\">";
            }
            echo "&nbsp;<input type='button' value='".IMAGE_DELETE."' onclick=\"delete_products( '".$orders_products_id."', '".TEXT_MONEY_SYMBOL."','1');recalc_order_price('".$oID."', '".$orders_products_id."', '2', '".$op_info_str."','".$orders_products_list."');\">&nbsp;x</td>\n" . 
              '      <td class="' . $RowStyle . '">' . $order->products[$i]['name'] . "<input id='update_products_name_$orders_products_id' name='update_products[$orders_products_id][name]' size='64' type='hidden' value='" . $order->products[$i]['name'] . "'>\n" . 
              '      &nbsp;&nbsp;';
            if ($less_op_single) {
              echo '<br><font color="#ff0000" size="1">'.NOTICE_LESS_PRODUCT_OPTION_TEXT.'</font>'; 
            }
            // Has Attributes?
            $op_info_str = '';
            if (sizeof($order->products[$i]['attributes']) > 0) {
              $op_info_array = array();
              for ($i_num = 0; $i_num < sizeof($order->products[$i]['attributes']); $i_num++) {
                $op_info_array[] = $order->products[$i]['attributes'][$i_num]['id'];
              }
              $op_info_str = implode('|||', $op_info_array);
              for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
                $t_item_id = $order->products[$i]['attributes'][$j]['option_item_id'];
                $item_type = $item_type_array[$t_item_id]; 
                $item_option_string = $item_option_array[$t_item_id];
                $item_option_string_array = unserialize($item_option_string);
                $item_option_temp_array = array();
                $item_list = '';
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
                $default_value = strtr($order->products[$i]['attributes'][$j]['option_info']['value'], array("'"=>"&#39;",'"'=>"&#34;")) == '' ? TEXT_UNSET_DATA : strtr($order->products[$i]['attributes'][$j]['option_info']['value'], array("'"=>"&#39;",'"'=>"&#34;"));
                $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['id'];
                $order->products[$i]['attributes'][$j]['price'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['price']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['price'] : $order->products[$i]['attributes'][$j]['price'];
                $order->products[$i]['attributes'][$j]['option_info']['title'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['option_info']['title']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['option_info']['title'] : $order->products[$i]['attributes'][$j]['option_info']['title'];
                $order->products[$i]['attributes'][$j]['option_info']['value'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['option_info']['value']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['attributes'][$orders_products_attributes_id]['option_info']['value'] : $order->products[$i]['attributes'][$j]['option_info']['value'];
                echo '<br><div class="order_option_width">&nbsp;<i><div class="order_option_info"><div class="order_option_title"> - ' . tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['title'], array("'"=>"&#39;",'"'=>"&#34;")) .'<input type="hidden" onkeyup="recalc_order_price(\''.$oID.'\', \''.$orders_products_id.'\', \'1\', \''.$op_info_str.'\',\''.$orders_products_list.'\');price_total(\''.TEXT_MONEY_SYMBOL.'\');" onchange="recalc_order_price(\''.$oID.'\', \''.$orders_products_id.'\', \'1\', \''.$op_info_str.'\',\''.$orders_products_list.'\');price_total(\''.TEXT_MONEY_SYMBOL.'\');" class="option_input_width" name="update_products[' . $orders_products_id .  '][attributes][' . $orders_products_attributes_id . '][option]" value="' .  tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['title'], array("'"=>"&#39;",'"'=>"&#34;")) . '">: ' . 
                  '</div><div class="order_option_value">';
                if ($less_op_single) {
                  echo htmlspecialchars($default_value); 
                } else {
                  echo '<a onclick="popup_window(this,\''.$item_type.'\',\''.tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['title'], array("'"=>"&#39;",'"'=>"&#34;")).'\',\''.$item_list.'\');" href="javascript:void(0);"><u>' .  htmlspecialchars(html_entity_decode($default_value)) .'</u></a>';
                }
                echo '<input type="hidden" onkeyup="recalc_order_price(\''.$oID.'\', \''.$orders_products_id.'\', \'1\', \''.$op_info_str.'\',\''.$orders_products_list.'\');price_total(\''.TEXT_MONEY_SYMBOL.'\');" onchange="recalc_order_price(\''.$oID.'\', \''.$orders_products_id.'\', \'1\', \''.$op_info_str.'\',\''.$orders_products_list.'\');price_total(\''.TEXT_MONEY_SYMBOL.'\');" class="option_input_width" name="update_products[' . $orders_products_id .  '][attributes][' . $orders_products_attributes_id . '][value]" value="' .  strtr($order->products[$i]['attributes'][$j]['option_info']['value'], array("'"=>"&#39;",'"'=>"&#34;"));
                echo '"></div></div>';
                echo '<div class="order_option_price">';
                if ($less_op_single) {
                  echo "<input size='9' type='text' style='background: none repeat scroll 0 0 #CCCCCC' readonly name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][price]' value='".(int)(isset($_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price'])?$_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price']:$order->products[$i]['attributes'][$j]['price'])."'>";
                } else {
                  echo "<input size='9' type='text' name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][price]' value='".(int)(isset($_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price'])?$_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price']:$order->products[$i]['attributes'][$j]['price'])."' onkeyup=\"clearNewLibNum(this);recalc_order_price('".$oID."', '".$orders_products_id."', '1', '".$op_info_str."','".$orders_products_list."');price_total('".TEXT_MONEY_SYMBOL."');\" onchange=\"clearNewLibNum(this);recalc_order_price('".$oID."', '".$orders_products_id."', '1', '".$op_info_str."','".$orders_products_list."');price_total('".TEXT_MONEY_SYMBOL."');\">";
                }
                echo TEXT_MONEY_SYMBOL;
                echo '</div>';
                echo '</i></div>';
              }
            }

            echo '      </td>' . "\n" .
              '      <td class="' . $RowStyle . '">' . $order->products[$i]['model'] . "<input name='update_products[$orders_products_id][model]' size='12' type='hidden' value='" . $order->products[$i]['model'] . "'>" . '</td>' . "\n" .
              '      <td class="' . $RowStyle . '" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . "<input name='update_products[$orders_products_id][tax]' size='2' type='hidden' value='" . tep_display_tax_value($order->products[$i]['tax']) . "'>" . '%</td>' . "\n";
              $order->products[$i]['price'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['p_price']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['p_price'] : $order->products[$i]['price']; 
              if ($less_op_single) {
                echo '<td class="'.$RowStyle.'" align="right"><input type="text" style="text-align:right;background: none repeat scroll 0 0 #CCCCCC" readonly class="once_pwd" name="update_products['.$orders_products_id.'][p_price]" size="9" value="'.tep_display_currency(number_format(abs(isset($_POST['update_products'][$orders_products_id]['p_price'])?$_POST['update_products'][$orders_products_id]['p_price']:$order->products[$i]['price']), 2)).'">'.TEXT_MONEY_SYMBOL;
              } else {
                echo '<td class="'.$RowStyle.'" align="right"><input type="text" style="text-align:right;" class="once_pwd" name="update_products['.$orders_products_id.'][p_price]" size="9" value="'.tep_display_currency(number_format(abs(isset($_POST['update_products'][$orders_products_id]['p_price'])?$_POST['update_products'][$orders_products_id]['p_price']:$order->products[$i]['price']), 2)).'" onkeyup="clearNoNum(this);recalc_order_price(\''.$oID.'\', \''.$orders_products_id.'\', \'2\',\''.$op_info_str.'\',\''.$orders_products_list.'\');price_total(\''.TEXT_MONEY_SYMBOL.'\');" onchange="clearNoNum(this);recalc_order_price(\''.$oID.'\', \''.$orders_products_id.'\', \'2\',\''.$op_info_str.'\',\''.$orders_products_list.'\');price_total(\''.TEXT_MONEY_SYMBOL.'\');">'.TEXT_MONEY_SYMBOL;
              }
              echo '<input type="hidden" name="hidden_pro_id[]" value="'.$orders_products_id.'">'; 
              echo '</td>';
              $order->products[$i]['final_price'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['final_price']) ? $_SESSION['orders_update_products'][$_GET['oID']][$orders_products_id]['final_price'] : $order->products[$i]['final_price'];
              echo '      <td class="' . $RowStyle . '" align="right">' . '<input type="hidden"
              class="once_pwd" style="text-align:right;" name="update_products['.$orders_products_id.'][final_price]" size="9" value="' . tep_display_currency(number_format(abs($order->products[$i]['final_price']),2)) 
              . '" onkeyup="clearNoNum(this);recalc_order_price(\''.$oID.'\', \''.$orders_products_id.'\', \'3\',\''.$op_info_str.'\',\''.$orders_products_list.'\');price_total(\''.TEXT_MONEY_SYMBOL.'\');" onchange="clearNoNum(this);recalc_order_price(\''.$oID.'\', \''.$orders_products_id.'\', \'3\',\''.$op_info_str.'\',\''.$orders_products_list.'\');price_total(\''.TEXT_MONEY_SYMBOL.'\');">' .
              '<input type="hidden" name="op_id_'.$orders_products_id.'" 
              value="'.tep_get_product_by_op_id($orders_products_id).'"><div id="update_products['.$orders_products_id.'][final_price]">'; 
              $order->products[$i]['final_price'] = isset($_SESSION['orders_update_products'][$_GET['oID']][$_POST['opd']]['final_price']) ? $_SESSION['orders_update_products'][$_GET['oID']][$_POST['opd']]['final_price'] : $order->products[$i]['final_price'];
              if ($order->products[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->products[$i]['final_price'], true, $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format($order->products[$i]['final_price'], true, $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value']);
            }
              echo "</div>\n" . '</td>' . "\n" . 
              '      <td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][a_price]">';
            if ($order->products[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value']);
            }
            echo '</div></td>' . "\n" . 
              '      <td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][b_price]">';
            if ($order->products[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value']);
            }
            echo '</div></td>' . "\n" . 
              '      <td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][c_price]">';
            if ($order->products[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value']);
            }
            echo '</div></td>' . "\n" . 
              '    </tr>' . "\n";
          }
          $products_name_str = implode('|||',$products_name_array);
          $products_id_str = implode('|||',$products_id_array);
          ?>
            </table>

            </td>
            <tr>
            <td>
            <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
            <td valign="top">&nbsp;</td> <td align="right"><?php echo '<a href="create_order.php?oID=' . $oID . '&Customer_mail='.$email_address_flag.'&site_id='.$site_id_flag.'">' . tep_html_element_button(ADDING_TITLE) . '</a>'; ?></td>
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
          
          $handle_fee_code = isset($order->info["code_fee"]) && $order->info["code_fee"] != 0 ? $order->info["code_fee"] : $handle_fee_code;
          $TotalsArray = array();
          for ($i=0; $i<sizeof($order->totals); $i++) {
            $TotalsArray[] = array("Name" => $order->totals[$i]['title'], "Price" => tep_display_currency(number_format($order->totals[$i]['value'], 2, '.', '')), "Class" => $order->totals[$i]['class'], "TotalID" => $order->totals[$i]['orders_total_id']);
            $TotalsArray[] = array("Name" => "          ", "Price" => "", "Class" => "ot_custom", "TotalID" => "0");
          }

          array_pop($TotalsArray);
          $totals_end_value = end($TotalsArray);
          array_pop($TotalsArray);
          foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
            if($TotalDetails['Class'] == 'ot_point'){

              $total_point_num = $TotalIndex;
              $total_point_value = $TotalsArray[$TotalIndex];
              unset($TotalsArray[$TotalIndex]);
            }
          }
          $total_num = $_SESSION['orders_update_products'][$_GET['oID']]['orders_totals'];
          for($totals_i = 5;$totals_i <= $total_num;$totals_i++){

            $TotalsArray[$totals_i]['Name'] = '';
            $TotalsArray[$totals_i]['Price'] = '';
            $TotalsArray[$totals_i]['Class'] = 'ot_custom';
            $TotalsArray[$totals_i]['TotalID'] = 0;
          }
          $TotalsArray[$total_point_num] = $total_point_value;
          $TotalsArray[4] = $totals_end_value;
          foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
             
                if($TotalIndex == 3 && $TotalDetails['Class'] == 'ot_custom' && $TotalDetails['Price'] == ''){
            
                  unset($TotalsArray[$TotalIndex]);
                }
          }
          $_SESSION['orders_update_products'][$_GET['oID']]['new_shipping_fee'] = $shipping_fee;
          foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
            $TotalStyle = "smallText";
            if ($TotalDetails["Class"] == "ot_total") {
              echo '  <tr>' . "\n" .
                   '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
                   '    <td align="right" class="' . $TotalStyle . '">'.TEXT_CODE_SHIPPING_FEE.'</td>' .
                   '    <td align="right" class="' . $TotalStyle . '"><div id="shipping_fee_id">' . $currencies->format($shipping_fee) . '</div>' . 
                   '</td>' . 
                   '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
                   '  </tr>' . "\n".
                   '  <tr>' . "\n" .
                   '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
                   '    <td align="right" class="' . $TotalStyle . '">'.TEXT_CODE_HANDLE_FEE.'</td>' .
                   '    <td align="right" class="' . $TotalStyle . '"><div id="handle_fee_id">' . $currencies->format($handle_fee_code) . '</div><input type="hidden" name="payment_code_fee" value="'.$order->info["code_fee"].'">' . 
                  '</td>' . 
                  '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
                  '  </tr>' . "\n";
              $shipping_fee_total = ($shipping_ot_subtotal+$shipping_fee+$shipping_ot_tax+$order->info["code_fee"]-$shipping_ot_point) != $TotalDetails["Price"] ? $shipping_fee : 0;
              $shipping_fee_total += ($shipping_ot_subtotal+$shipping_fee+$shipping_ot_tax+$handle_fee_code-$shipping_ot_point) != $TotalDetails["Price"] ? $handle_fee_code : 0;
              echo '  <tr id="add_option_total">' . "\n" .
                '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_OTTOTAL_READ.'</td>' . 
                '    <td align="right" class="' . $TotalStyle . '">' . $TotalDetails["Name"] . '</td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><div id="ot_total_id">' ;
              if (($TotalDetails["Price"]+$shipping_fee_total) >= 0){
                echo $currencies->ot_total_format(isset($_SESSION['orders_update_products'][$_GET['oID']]['ot_total']) ? $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] : $TotalDetails["Price"]+$shipping_fee_total, true,
                    $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value']);
              }else{
                echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->ot_total_format(isset($_SESSION['orders_update_products'][$_GET['oID']]['ot_total']) ? $_SESSION['orders_update_products'][$_GET['oID']]['ot_total'] : $TotalDetails["Price"]+$shipping_fee_total, true, $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
              }
              echo '</div>' . 
                "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . ($TotalDetails["Price"]+$shipping_fee_total) . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
                '  </tr>' . "\n";
            } elseif ($TotalDetails["Class"] == "ot_subtotal") {
              $TotalDetails["Price"] = isset($_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal']) ? $_SESSION['orders_update_products'][$_GET['oID']]['ot_subtotal'] : $TotalDetails["Price"];
              $shipping_ot_subtotal = $TotalDetails["Price"];
              echo '  <tr>' . "\n" .
                '    <td align="left" class="' . $TotalStyle .  '">&nbsp;</td>' . 
                '    <td align="right" class="' . $TotalStyle . '">' . $TotalDetails["Name"] . '</td>' .
                '    <td align="right" class="' . $TotalStyle . '"><div id="ot_subtotal_id">';
              if($TotalDetails["Price"] >= 0){
                echo $currencies->format($TotalDetails["Price"], true,
                    $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value']);
              }else{
                echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($TotalDetails["Price"], true, $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
              }
              echo '</div>' . 
                "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
                '  </tr>' . "\n"; 
            } elseif ($TotalDetails["Class"] == "ot_tax") {
              $shipping_ot_tax = $TotalDetails["Price"];
              echo '  <tr>' . "\n" . 
                '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
                '    <td align="right" class="' . $TotalStyle . '">' . trim($TotalDetails["Name"]) . "<input name='update_totals[$TotalIndex][title]' type='hidden' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . '</td>' . "\n" .
                '    <td align="right" class="' . $TotalStyle . '">' . $currencies->format($TotalDetails["Price"], true, $orders_exit_flag == true ? $order->info['currency'] : $_SESSION['currency'], $orders_exit_flag == true ? $order->info['currency_value'] : $_SESSION['currency_value']) . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
                '  </tr>' . "\n";
            } elseif ($TotalDetails["Class"] == "ot_point") {
              $shipping_ot_point = $TotalDetails["Price"];
              $TotalDetails["Price"] = isset($_SESSION['orders_update_products'][$_GET['oID']]['point']) ? $_SESSION['orders_update_products'][$_GET['oID']]['point'] : $TotalDetails["Price"];
              if ($customer_guest['customers_guest_chk'] == 0) { //会员
                $current_point = $customer_point['point'] + $TotalDetails["Price"];
                echo '  <tr>' . "\n" .
                  '    <td align="left" class="' . $TotalStyle . '">'.TEXT_CUSTOMER_INPUT.'<font color="red">'.TEXT_REMAINING . $customer_point['point'] . TEXT_SUBTOTAL . $current_point . TEXT_RIGHT_BRACKETS.'</font>' . TEXT_INPUT_POSITIVE_NUM . 
                  '    <td align="right" class="' . $TotalStyle . '">' . trim($TotalDetails["Name"]) . '</td>' . "\n" .
                  '    <td align="right" class="' . $TotalStyle . '" nowrap>−' .  "<input style='text-align:right;' name='update_totals[$TotalIndex][value]' id='point_id' onkeyup='clearNoNum(this);price_total(\"".TEXT_MONEY_SYMBOL."\");' onchange='clearNoNum(this);price_total(\"".TEXT_MONEY_SYMBOL."\");' size='6' value='" . $TotalDetails["Price"] . "'>
                  " .TEXT_MONEY_SYMBOL.  "<input type='hidden' name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . 
                  "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
                  "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
                  "<input type='hidden' name='before_point' value='0'>" . 
                  '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
                  '   </td>' . "\n" .
                  '  </tr>' . "\n";
              } else { //ゲスト
                echo '  <tr>' . "\n" .
                  '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_TOTALDETAIL_READ.'</td>' . 
                  '    <td align="right" class="' . $TotalStyle . '">' . trim($TotalDetails["Name"]) . '</td>' . "\n" .
                  '    <td align="right" class="' . $TotalStyle . '">' .  $TotalDetails["Price"] .TEXT_MONEY_SYMBOL. 
                  "<input type='hidden' id='point_id' value=''><input type='hidden' name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . 
                  "<input type='hidden' name='update_totals[$TotalIndex][value]' size='6' value='" . $TotalDetails["Price"] . "'>" . 
                  "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
                  "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
                  '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
                  '   </td>' . "\n" .
                  '  </tr>' . "\n";
              }
            } else {
              $sign_str = '<select id="sign_'.$TotalIndex.'" name="sign_value_'.$TotalIndex.'" onchange="price_total(\''.TEXT_MONEY_SYMBOL.'\');orders_session(\'sign_'.$TotalIndex.'\',this.value);">';
              $sign_str .= '<option value="1"'.(isset($_SESSION['orders_update_products'][$_GET['oID']]['sign_'.$TotalIndex]) && $_SESSION['orders_update_products'][$_GET['oID']]['sign_'.$TotalIndex] == '1' ? ' selected="selected"': '').'>+</option>';
              $sign_str .= '<option value="0"'.(isset($_SESSION['orders_update_products'][$_GET['oID']]['sign_'.$TotalIndex]) && $_SESSION['orders_update_products'][$_GET['oID']]['sign_'.$TotalIndex] == '0' ? ' selected="selected"': $TotalDetails["Price"] < 0 ? ' selected="selected"': '').'>-</option>';
              $sign_str .= '</select>';
              $totals_sum = isset($_SESSION['orders_update_products'][$_GET['oID']]['orders_totals']) ? $_SESSION['orders_update_products'][$_GET['oID']]['orders_totals'] : 4;
              $totals_num = isset($_SESSION['orders_update_products'][$_GET['oID']]['orders_totals']) ? $_SESSION['orders_update_products'][$_GET['oID']]['orders_totals'] : 3;
              $button_add = $TotalIndex == 1 ? '<INPUT type="button" id="button_add" value="'.TEXT_BUTTON_ADD.'" onclick="add_option();"><input type="hidden" id="button_add_id" value="'. $totals_sum.'">&nbsp;' : '';
              $TotalDetails["Price"] = isset($_SESSION['orders_update_products'][$_GET['oID']][$TotalIndex]['value']) ? $_SESSION['orders_update_products'][$_GET['oID']][$TotalIndex]['value'] : $TotalDetails["Price"];
              $TotalDetails["Name"] = isset($_SESSION['orders_update_products'][$_GET['oID']][$TotalIndex]['title']) ? $_SESSION['orders_update_products'][$_GET['oID']][$TotalIndex]['title'] : $TotalDetails["Name"];
              echo '  <tr>' . "\n" .
                '    <td align="left" class="' . $TotalStyle .  '">'.($TotalIndex == 1 ? EDIT_ORDERS_TOTALDETAIL_READ_ONE : '').'</td>' . 
                '    <td style="min-width:188px;" align="right" class="' .  $TotalStyle . '">' . $button_add ."<input style='text-align:right;' name='update_totals[$TotalIndex][title]' onkeyup='price_total(\"".TEXT_MONEY_SYMBOL."\");' onchange='price_total(\"".TEXT_MONEY_SYMBOL."\");' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>:" . '</td>' . "\n" .
                '    <td align="right" class="' . $TotalStyle . '" style="min-width:
                60px;">' . $sign_str ."<input style='text-align:right;'
                name='update_totals[$TotalIndex][value]'
                id='update_total_".$TotalIndex."'
                onkeyup='clearNoNum(this);price_total(\"".TEXT_MONEY_SYMBOL."\");' onchange='clearNoNum(this);price_total(\"".TEXT_MONEY_SYMBOL."\");'
                size='6' value='" . ($TotalDetails["Price"] != '' &&
                $TotalDetails["Price"] != 0 ? 
                abs($TotalDetails["Price"]) : '') . "'>" .TEXT_MONEY_SYMBOL.  "
                <input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
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
            <tr>
<?php
          $order_status_query = tep_db_query("select * from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='". $oID ."' order by orders_status_history_id desc limit 0,1");            
          $order_status_num = tep_db_num_rows($order_status_query);
          $order_status_array = tep_db_fetch_array($order_status_query);
          $select_status = get_configuration_by_site_id_or_default('MODULE_PAYMENT_'.strtoupper(payment::changeRomaji($pay_method,'code')).'_ORDER_STATUS_ID', $_SESSION['sites_id_flag']);
          $select_status = $select_status != 0 ? $select_status: get_configuration_by_site_id('DEFAULT_ORDERS_STATUS_ID');
          $customer_notified = $order_status_array['customer_notified'];           
          $customer_notified = isset($customer_notified) ? $customer_notified : true;
          $customer_notified = $select_status == 31 ? 0 : $customer_notified;
          $customer_notified = isset($_SESSION['orders_update_products'][$_GET['oID']]['notify']) ? $_SESSION['orders_update_products'][$_GET['oID']]['notify'] : $customer_notified;
          $select_status = isset($_SESSION['orders_update_products'][$_GET['oID']]['s_status']) ? $_SESSION['orders_update_products'][$_GET['oID']]['s_status'] : $select_status;
          
?>
            <td class="main" width="82" style="min-width:45px;"><?php echo ENTRY_STATUS; ?></td>
            <td class="main"><?php echo tep_draw_pull_down_menu('s_status', $orders_statuses, $select_status, 'onChange="new_mail_text_orders(this, \'s_status\',\'comments\',\'title\');" style="width:80px;" id="s_status"');?>&nbsp;&nbsp;<?php echo EDIT_ORDERS_ORIGIN_VALUE_TEXT;?></td>
            </tr>
            <?php

          $ma_se = "select * from ".TABLE_MAIL_TEMPLATES." where ";
          $orders_status_num_query =  tep_db_query("select min(orders_status_id) as orders_status_id_min from ". TABLE_ORDERS_STATUS);
          $orders_status_num_array = tep_db_fetch_array($orders_status_num_query);
          tep_db_free_result($orders_status_num_query);
          $orders_status_num = $orders_exit_flag == true ? $order->info['orders_status'] : $orders_status_num_array['orders_status_id_min'];
          if(!isset($_GET['status']) || $_GET['status'] == ""){
            $ma_se .= " flag = 'ORDERS_STATUS_MAIL_TEMPLATES_".$orders_status_num."' ";

            // 用来判断是否选中 送信&通知，如果nomail==1则不选中
            $ma_s = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$orders_status_num."'"));
          }else{
            $ma_se .= " flag = 'ORDERS_STATUS_MAIL_TEMPLATES_".$_GET['status']."' ";

            // 用来判断是否选中 送信&通知，如果nomail==1则不选中
            $ma_s = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$_GET['status']."'"));
          }
          $ma_se .= "and site_id='0'";
          $mail_sele = tep_db_query($ma_se);
          $mail_sql  = tep_db_fetch_array($mail_sele);
          $sta       = isset($_GET['status'])?$_GET['status']:'';
          $mail_sql['title'] = isset($_SESSION['orders_update_products'][$_GET['oID']]['title']) ? $_SESSION['orders_update_products'][$_GET['oID']]['title'] : $mail_sql['title'];
          $notify_comments_checked = isset($_SESSION['orders_update_products'][$_GET['oID']]['notify_comments']) ? $_SESSION['orders_update_products'][$_GET['oID']]['notify_comments'] == 1 ? true : false : false;
          ?>

            <tr>
            <td class="main"><?php echo ENTRY_EMAIL_TITLE; ?></td>
            <td class="main"><?php echo tep_draw_input_field('title', $mail_sql['title'],'style="width:100%;" id="mail_title"'); ?></td>
            </tr>
            <tr>
            <td class="main"><?php echo EDIT_ORDERS_SEND_MAIL_TEXT;?></td>
            <td class="main"><table bgcolor="red" cellspacing="5"><tr><td><?php echo tep_draw_checkbox_field('notify', '', $customer_notified,'id="notify"'); ?></td></tr></table></td>
            </tr>
            <?php if($CommentsWithStatus) { ?>
              <tr>
                <td class="main"><?php echo EDIT_ORDERS_RECORD_TEXT;?></td>
                <td class="main"><?php echo tep_draw_checkbox_field('notify_comments', '', $notify_comments_checked); ?>&nbsp;&nbsp;<font color="#FF0000;"><?php echo EDIT_ORDERS_RECORD_READ;?></font></td>
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
                $mail_sql['contents'] = isset($_SESSION['orders_update_products'][$_GET['oID']]['comments']) ? $_SESSION['orders_update_products'][$_GET['oID']]['comments'] : $mail_sql['contents'];
                if($CommentsWithStatus) {


                  echo tep_draw_textarea_field('comments', 'hard', '74', '30', isset($order->info['comments'])?$order->info['comments']:str_replace('${MAIL_COMMENT}',orders_a($order->info['orders_id']),$mail_sql['contents']),'style=" font-family:monospace; font-size:12px; width:400px;" id="c_comments"');
                } else {
                  echo tep_draw_textarea_field('comments', 'hard', '74', '30', isset($order->info['comments'])?$order->info['comments']:str_replace('${MAIL_COMMENT}',orders_a($order->info['orders_id']),$mail_sql['contents']),'style=" font-family:monospace; font-size:12px; width:400px;" id="c_comments"');
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
            <td class="main" bgcolor="#FFDDFF"><?php echo EDIT_ORDERS_FINAL_CONFIRM_TEXT;?>&nbsp;<?php echo HINT_PRESS_UPDATE; ?></td>
            <td class="main" bgcolor="#FFBBFF" width="10">&nbsp;</td>
            <td class="main" bgcolor="#FF99FF" width="10">&nbsp;</td>
            <td class="main" bgcolor="#FF77FF" width="10">&nbsp;</td>
            <td class="main" bgcolor="#FF55FF" width="120" align="center"><INPUT type="button" value="<?php echo TEXT_FOOTER_CHECK_SAVE;?>" onClick="if(date_time()){if(products_num_check('<?php echo $orders_products_list;?>','<?php echo $products_name_str;?>','<?php echo $products_id_str;?>')){submit_check_con();}}"></td>
            </tr>
            </table>
            </td>
            </tr>
            <!-- End of Update Block -->
            <?php
        }
      ?>
        </table>
        </div>
        </div>
        </td>
        <!-- body_text_eof -->
        </tr>
        </table>
        </form>
        <!-- body_eof -->

        <!-- footer -->
        <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
        <!-- footer_eof -->
        <br>
        </body>
        </html>
        <?php
        require(DIR_WS_INCLUDES . 'application_bottom.php');
      ?>
