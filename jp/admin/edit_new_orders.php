<?php
/*
   $Id$

   创建订单
 */

require('includes/application_top.php');
require('includes/step-by-step/new_application_top.php');
ini_set("display_errors","Off");
include(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/' . FILENAME_EDIT_ORDERS);
require(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_EDIT_ORDERS);

require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies(2);
$oID = tep_db_input($_GET['oID']);
include(DIR_WS_CLASSES . 'order.php');
require_once('includes/address/AD_Option.php');
require_once('includes/address/AD_Option_Group.php');
$ad_option = new AD_Option();
//error_reporting(E_ALL);
//ini_set("display_errors","On");
// Optional Tax Rates, e.g. shipping tax of 17.5% is "17.5"
// $AddCustomTax = "20.0"; // class "ot_custom", used for all unknown total modules
$AddCustomTax = "19.6";  // new
// $AddShippingTax = "20.0"; // class "ot_shippping"
$AddShippingTax = "19.6";  // new
// $AddLevelDiscountTax = "7.6"; // class "ot_lev_discount"
$AddLevelDiscountTax = "19.6";  // new
// $AddCustomerDiscountTax = "7.6"; // class "ot_customer_discount"
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

// 最新の注文情報取得
// 获取最新 订单情报
$order = new order($oID);
// ポイントを取得する
// 获得客户信息
$customer_point_query = tep_db_query("
    select point 
    from " . TABLE_CUSTOMERS . " 
    where customers_id = '" . $order->customer['id'] . "'");
$customer_point = tep_db_fetch_array($customer_point_query);
// ゲストチェック
// 获取客户 是否为注册用户
$customer_guest_query = tep_db_query("
    select customers_guest_chk 
    from " . TABLE_CUSTOMERS . " 
    where customers_id = '" . $order->customer['id'] . "'");
$customer_guest = tep_db_fetch_array($customer_guest_query);

if (tep_not_null($action)) {

  $payment_modules = payment::getInstance($order->info['site_id']);
  switch ($action) {
    // 1. UPDATE ORDER ###############################################################################################
      case 'update_order':

      //订单状态更新
    $oID      = tep_db_prepare_input($_GET['oID']);
    $status   = tep_db_prepare_input($_POST['s_status']);
    $title    = tep_db_prepare_input($_POST['title']);
    $comments = tep_db_prepare_input($_POST['comments']);
    $comments_text = tep_db_prepare_input($_POST['comments_text']);
    $payment_method = tep_db_prepare_input($_POST['payment_method']); 
    $site_id  = tep_get_site_id_by_orders_id($oID);
    
    $error = false;
    $options_info_array = array(); 
      if (!$ad_option->check()) {
        foreach ($_POST as $p_key => $p_value) {
          $op_single_str = substr($p_key, 0, 3);
          if ($op_single_str == 'ad_') {
            $options_info_array[$p_key] = $p_value; 
          } 
        }
      }else{
        $address_style = 'display: block;';
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
    
    $comment_arr = $payment_modules->dealComment($payment_method,$comment);

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
    //oa start 如果状态发生改变，找到当前的订单的
    //if ($check_status['orders_status']!=$status){
    tep_order_status_change($oID,$status);
    //}
    //OA_END
    /*
       if ($status == '9') {
       tep_db_query("update `".TABLE_ORDERS."` set `confirm_payment_time` = '".date('Y-m-d H:i:s', time())."' where `orders_id` = '".$oID."'");
       }
        var 
     */ 
    //Add Point System
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



        // ここからカスタマーレベルに応じたポイント還元率算出============================================================
        if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
          $customer_id = $result1['customers_id'];
          //設定した期間内の注文合計金額を算出------------
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
          //----------------------------------------------
          //今回の注文額は除外
          $total_buyed_date = $total_buyed_date - ($result3['value'] - (int)$result2['value']);

          //還元率を計算----------------------------------
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
          //----------------------------------------------
          $point_rate = $back_rate;
        } else {
          $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
        }
        // ここまでカスタマーレベルに応じたポイント還元率算出============================================================
        if ($result3['value'] >= 0) {
          $get_point = ($result3['value'] - (int)$result2['value']) * $point_rate;
        } else {
          if ($result3['value'] > -200) {
            if ($check_status['payment_method'] == '来店支払い') {
              $get_point = 0;
            } else {
              $get_point = abs($result3['value']);
            }
          } else {
            $get_point = 0;
          }
        }
        //$plus = $result4['point'] + $get_point;

        if($check_status['payment_method'] != 'ポイント(買い取り)'){
          tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . $get_point . 
              " where customers_id = '" . $result1['customers_id']."' 
              and customers_guest_chk = '0' ");
        }
      }else{
        $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
        $os_result = tep_db_fetch_array($os_query);
        if($os_result['orders_status_name']=='支払通知*'){
          $query1 = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '".$oID."'");
          $result1 = tep_db_fetch_array($query1);
          if ($check_status['payment_method'] == 'ポイント(買い取り)') {
            $query_t = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_total' and orders_id = '".tep_db_input($oID)."'");
            $result_t = tep_db_fetch_array($query_t);
            $get_point = abs(intval($result_t['value']));
          } else {
            $get_point = 0;
          }
          $point_done_query =tep_db_query("select count(orders_status_history_id) cnt from
              ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id = '".$status."' and 
              orders_id = '".tep_db_input($oID)."'");
          $point_done_row  =  tep_db_fetch_array($point_done_query);
          if($point_done_row['cnt'] <1 ){
            tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " .
                $get_point . " where customers_id = '" . $result1['customers_id']."' 
                and customers_guest_chk = '0'");
          }
        }
      }
    }

    if ($check_status['orders_status'] != $status || $comments != '') {
      tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
      orders_updated(tep_db_input($oID));
      orders_wait_flag(tep_db_input($oID));
      $customer_notified = '0';

      if ($_POST['notify'] == 'on') {

        $ot_query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_total'");
        $ot_result = tep_db_fetch_array($ot_query);
        $otm = (int)$ot_result['value'] . '円';

        $os_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '".$status."'");
        $os_result = tep_db_fetch_array($os_query);

        $title = str_replace(array(
              '${NAME}',
              '${MAIL}',
              '${ORDER_D}',
              '${ORDER_N}',
              '${PAY}',
              '${ORDER_M}',
              '${TRADING}',
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
                tep_torihiki($check_status['torihiki_date']).'～'.date('H時i分',strtotime($check_status['torihiki_date_end'])).'　（24時間表記）',
                $os_result['orders_status_name'],
                get_configuration_by_site_id('STORE_NAME', $site_id),
                get_url_by_site_id($site_id),
                get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
                date('Y年n月j日',strtotime(tep_get_pay_day()))
                ),$title);

        $comments = str_replace(array(
              '${NAME}',
              '${MAIL}',
              '${ORDER_D}',
              '${ORDER_N}',
              '${PAY}',
              '${ORDER_M}',
              '${TRADING}',
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
                tep_torihiki($check_status['torihiki_date']).'～'.date('H時i分',strtotime($check_status['torihiki_date_end'])).'　（24時間表記）',
                $os_result['orders_status_name'],
                get_configuration_by_site_id('STORE_NAME', $site_id),
                get_url_by_site_id($site_id),
                get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $site_id),
                date('Y年n月j日',strtotime(tep_get_pay_day()))
                ),$comments);
        if (!tep_is_oroshi($check_status['customers_id'])) {
          tep_mail($check_status['customers_name'], $check_status['customers_email_address'], $title, $comments, get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $site_id), $site_id);
        }
        tep_mail(get_configuration_by_site_id('STORE_OWNER', $site_id), get_configuration_by_site_id('SENTMAIL_ADDRESS', $site_id), '送信済：'.$title, $comments, $check_status['customers_name'], $check_status['customers_email_address'], $site_id);
        $customer_notified = '1';
      }


      //if($_POST['notify'] == 'on') {
        $customer_notified = '1';
      //} else {
        //$customer_notified = '0';
      //}
      tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" . $customer_notified . "', '".$comment_arr['comment']."\n".$comments_text."')");
      // 同步问答
      //    orders_status_updated_for_question($oID,tep_db_input($status),$_POST['notify_comments'] == 'on', $_POST['qu_type']);
      $order_updated = true;
    }

    if ($order_updated) {
      $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
    } else {
      $messageStack->add_session(WARNING_ORDER_NOT_UPDATED, 'warning');
    }
      //订单状态更新结束  
      $products_weight_total = 0; //商品总重量
      $products_money_total = 0; //商品总价
      $cart_shipping_time = array(); //商品取引时间
      //$products_address_query = tep_db_query("select * from ". TABLE_ORDERS_PRODUCTS ." where orders_id='". tep_db_input($oID) ."'");
      //while($products_address_array = tep_db_fetch_array($products_address_query)){
      foreach($update_products as $update_key=>$update_value){
        
        $update_weight_query = tep_db_query("select products_id from ". TABLE_ORDERS_PRODUCTS ." where orders_products_id='". $update_key ."'");
        $update_weight_array = tep_db_fetch_array($update_weight_query);
        tep_db_free_result($update_weight_query);
        $products_weight_query = tep_db_query("select * from ". TABLE_PRODUCTS ." where products_id='". $update_weight_array['products_id'] ."'");
        $products_weight_array = tep_db_fetch_array($products_weight_query);
        $cart_shipping_time[] = $products_weight_array['products_shipping_time'];
        $products_weight_total += $products_weight_array['products_weight']*$update_value['qty'];
        $products_money_total += $update_value['final_price']*$update_value['qty'];
        tep_db_free_result($products_weight_query);
      }
      //tep_db_free_result($products_address_query);
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
      // end

      //更新订单

      $oID = tep_db_prepare_input($_GET['oID']);
      $order = new order($oID);
      //$status = '1'; // 初期値
      $goods_check = $order_query;
      /*
         if (tep_db_num_rows($goods_check) == 0) {
         $messageStack->add('商品が追加されていません。', 'error');
         $action = 'edit';
         break;
         }
       */
      $viladate = tep_db_input($_POST['update_viladate']);//viladate pwd 
      if($viladate!='_false'&&$viladate!=''){
        tep_insert_pwd_log($viladate,$ocertify->auth_user);
        $viladate = true;
      }else if($viladate=='_false'){
        $viladate = false;
        $messageStack->add_session('更新をキャンセルしました。', 'error');
        tep_redirect(tep_href_link("edit_new_orders.php", tep_get_all_get_params(array('action')) . 'action=edit'));
        break;
      }

      //  错误信息处理
      //暂时关闭 日期错误处理
         /*
         if (isset($update_tori_torihiki_date)) { //日時が有効かチェック
         if (!preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)$/', $update_tori_torihiki_date, $m)) { // check the date format
         $messageStack->add('日時フォーマットが間違っています。 "2008-01-01 10:30:00"', 'error');
         $action = 'edit';
         break;
         } elseif (!checkdate($m[2], $m[3], $m[1]) || $m[4] >= 24 || $m[5] >= 60 || $m[6] >= 60) { // make sure the date provided is a validate date
         $messageStack->add('無効な日付または右記の数字を超えています。 "23:59:59"', 'error');
         $action = 'edit';
         break;
         }
         } else {
         $messageStack->add('日時が入力されていません。', 'error');
         $action = 'edit';
         break;
         }
         */

      foreach ($update_totals as $total_index => $total_details) {    
        extract($total_details,EXTR_PREFIX_ALL,"ot");
        if ($ot_class == "ot_point" && (int)$ot_value > 0) {
          $current_point = $customer_point['point'] + $before_point;
          if ((int)$ot_value > $current_point) {
            $messageStack->add('ポイントが足りません。入力可能なポイントは <b>' . $current_point . '</b> です。', 'error');
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

      
      if(isset($comment_arr['comment']) && !empty($comment_arr['comment'])){
         $UpdateOrders .= "orders_comment = '{$comment_arr['comment']}',";
      }

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
      $address_num_query = tep_db_query("select count(*) as count_num from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."'"); 
      $address_num_array = tep_db_fetch_array($address_num_query);

      tep_db_query("delete from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."' and customers_id='".$check_status['customers_id']."'");
      
      foreach($options_info_array as $op_key=>$op_value){
  
        $address_options_query = tep_db_query("select * from ". TABLE_ADDRESS ." where name_flag='". substr($op_key,3) ."'");
        $address_options_array = tep_db_fetch_array($address_options_query);
        tep_db_free_result($address_options_query);
        $op_value = $op_value == $address_options_array['comment'] ? '' : $op_value;
        $address_query = tep_db_query("insert into ". TABLE_ADDRESS_ORDERS ." values(NULL,'$oID',{$check_status['customers_id']},{$address_options_array['id']},'{$address_options_array['name_flag']}','$op_value')");
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
  foreach($options_info_array as $address_history_key=>$address_history_value){
      $address_history_query = tep_db_query("select id,name_flag from ". TABLE_ADDRESS ." where name_flag='". substr($address_history_key,3) ."'");
      $address_history_array = tep_db_fetch_array($address_history_query);
      tep_db_free_result($address_history_query);
      $address_history_id = $address_history_array['id'];
      $address_history_add_query = tep_db_query("insert into ". TABLE_ADDRESS_HISTORY ." values(NULL,'$oID',{$check_status['customers_id']},$address_history_id,'{$address_history_array['name_flag']}','$address_history_value')");
      tep_db_free_result($address_history_add_query);
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
              // 增加架空
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
          if(!tep_is_oroshi($check_status['customers_id']))
            tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = ".$pr_quantity.", products_virtual_quantity = ".$pv_quantity.", products_ordered = products_ordered + " . $quantity_difference . " where products_id = '" . (int)$order['products_id'] . "'");
          tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = 0 where products_real_quantity < 0 and products_id = '" . (int)$order['products_id'] . "'");
          tep_db_query("update " . TABLE_PRODUCTS . " set products_virtual_quantity = 0 where products_virtual_quantity < 0 and products_id = '" . (int)$order['products_id'] . "'");
        }

        if($products_details["qty"] > 0) { // a.) quantity found --> add to list & sum    
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
              //$Query = "update " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set option_info = '" .tep_db_input(serialize($input_option)) . "',options_values_price = '".$attributes_details['price']."' where orders_products_attributes_id = '$orders_products_attributes_id';";
              $Query = "update " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " set options_values_price = '".$attributes_details['price']."' where orders_products_attributes_id = '$orders_products_attributes_id';";
              tep_db_query($Query);
            }
          }
        }else{ // b.) null quantity found --> delete
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

          //} elseif ($ot_class == "ot_point") { // ポイント割引
          //$order = new order($oID);
          //$RunningTax -= $ot_value * $products_details['tax'] / $order->info['currency_value'] / 100 ;

      }
      }

      // exit;
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
            // print "ot_value = $ot_value<br>\n";
          }

          // Check for existence of subtotals (CWS)                      
          if ($ot_class == "ot_total") {
            // Correction tax calculation (Michel Haase, 2005-02-18)
            // I can't find out, WHERE the $RunningTotal is calculated - but the subtraction of the tax was wrong (in our shop)
            //        $ot_value = $RunningTotal-$RunningTax;
            $ot_value = $RunningTotal;

            if ( !$ot_subtotal_found ) { // There was no subtotal on this order, lets add the running subtotal in.
              //        $ot_value +=  $RunningSubTotal;
            }
            //      print $ot_value;
            //      exit;
          }

          // Set $ot_text (display-formatted value)

          // Correction of number_format - German format (Michel Haase, 2005-02-18)
          //      $ot_text = "\$" . number_format($ot_value, 2, ',', '');

          $order = new order($oID);

          if ($customer_guest['customers_guest_chk'] == 0 && $ot_class == "ot_point" && $ot_value != $before_point) { //会員ならポントの増減
            $point_difference = ($ot_value - $before_point);
            tep_db_query("update " . TABLE_CUSTOMERS . " set point = point - " . $point_difference . " where customers_id = '" . $order->customer['id'] . "'"); 
          }

          $ot_text = $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']);

          if ($ot_class == "ot_total") {
            $ot_text = "<b>" . $ot_text . "</b>";
          }

          if($ot_total_id > 0 || $ot_class == "ot_point") { // Already in database --> Update
            //delete from query 
            //    text = "' . tep_insert_currency_text($ot_text) . '",
            $Query = 'UPDATE ' . TABLE_ORDERS_TOTAL . ' SET
              title = "' . $ot_title . '",
                    value = "' . tep_insert_currency_value($ot_value) . '",
                    sort_order = "' . $sort_order . '"
                      WHERE orders_total_id = "' . $ot_total_id . '"';
            tep_db_query($Query);
          } else { // New Insert
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

            //} elseif ($ot_class == "ot_point") {
            //$RunningTotal -= $ot_value; // ポイント割引

        } else {
          $RunningTotal += $ot_value;
        }

        //  print $ot_value."<br>";
        } elseif (($ot_total_id > 0) && ($ot_class != "ot_shipping") && ($ot_class != "ot_point")) { // Delete Total Piece
          $Query = "delete from " . TABLE_ORDERS_TOTAL . " where orders_total_id = '$ot_total_id'";
          tep_db_query($Query);
        }

      }
      //  print "Totale ".$RunningTotal;
      //  exit;   

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
      /*
         , text = '".tep_insert_currency_text($currencies->format($new_subtotal, true, $order->info['currency']))."'
       */
      tep_db_query("update " . TABLE_ORDERS_TOTAL . " set value = '".tep_insert_currency_value($new_subtotal)."' where class='ot_subtotal' and orders_id = '".$oID."'");

      //tax
      $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_ORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
      $plustax = tep_db_fetch_array($plustax_query);
      if($plustax['cnt'] > 0) {
        /*
           , text = '".tep_insert_currency_text($currencies->format($new_tax, true, $order->info['currency']))."'   */
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
          payment::changeRomaji($order->info['payment_method'],PAYMENT_RETURN_TYPE_CODE), $newtotal);

      $newtotal = $newtotal+$handle_fee+$shipping_fee;

      /*
         , text = '<b>" . $currencies->ot_total_format(intval(floor($newtotal)), true, $order->info['currency']) . "</b>'
       */
      $totals = "update " . TABLE_ORDERS_TOTAL . " set value = '" . intval(floor($newtotal)) . "' where class='ot_total' and orders_id = '" . $oID . "'";
      tep_db_query($totals);

      $update_orders_sql = "update ".TABLE_ORDERS." set code_fee = '".$handle_fee."' where orders_id = '".$oID."'";
      tep_db_query($update_orders_sql);

      // 最終処理（更新およびメール送信）
      if ($products_delete == false) {
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
          for ($i=0; $i<sizeof($order->products); $i++) {
            //$orders_products_id = $order->products[$i]['orders_products_id'];
            $products_ordered_mail .= '注文商品'.str_repeat('　', intval($max_c_len - mb_strlen('注文商品', 'utf-8'))).'：' . $order->products[$i]['name'] . '（' . $order->products[$i]['model'] . '）';
            if ($order->products[$i]['price'] != '0') {
              $products_ordered_mail .= '（'.$currencies->display_price($order->products[$i]['price'], $order->products[$i]['tax']).'）'; 
            }
            $products_ordered_mail .= "\n"; 
            // Has Attributes?
            if (sizeof($order->products[$i]['attributes']) > 0) {
              for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
                $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['id'];
                $products_ordered_mail .= tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['title'], array("'"=>"&quot;")) .str_repeat('　', intval($max_c_len - mb_strlen($order->products[$i]['attributes'][$j]['option_info']['title'], 'utf-8'))) .'：';
                $products_ordered_mail .= tep_parse_input_field_data(str_replace(array("<br>", "<BR>", "\r", "\n", "\r\n"), "", $order->products[$i]['attributes'][$j]['option_info']['value']), array("'"=>"&quot;"));
                
                if ($order->products[$i]['attributes'][$j]['price'] != '0') {
                  //$products_ordered_mail .= '（'.$currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty']).'）'; 
                  $products_ordered_mail .= '（'.$currencies->format($order->products[$i]['attributes'][$j]['price']).'）'; 
                }
                $products_ordered_mail .= "\n"; 
              }
            }
            $_product_info_query = tep_db_query("
                select p.products_id, 
                pd.products_name, 
                p.products_attention_1,
                p.products_attention_2,
                p.products_attention_3,
                p.products_attention_4,
                p.products_attention_5,
                pd.products_description, 
                p.products_model, 
                p.products_real_quantity + p.products_virtual_quantity as products_quantity,
                p.products_image,
                p.products_image2,
                p.products_image3, 
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
            $data1 = explode("//", $product_info['products_attention_1']);

            $products_ordered_mail .= '個数'.str_repeat('　', intval($max_c_len - mb_strlen('個数', 'utf-8'))).'：' . $order->products[$i]['qty'] . '個' . tep_get_full_count2($order->products[$i]['qty'], $order->products[$i]['id']) . "\n";
            $products_ordered_mail .= '単価'.str_repeat('　', intval($max_c_len - mb_strlen('単価', 'utf-8'))).'：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax']) . "\n";
            $products_ordered_mail .= '小計'.str_repeat('　', intval($max_c_len - mb_strlen('小計', 'utf-8'))).'：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . "\n";
            //$products_ordered_mail .= 'キャラクター名　　：' . (EMAIL_USE_HTML === 'true' ? htmlspecialchars($order->products[$i]['character']) : $order->products[$i]['character']) . "\n";
            $products_ordered_mail .= "------------------------------------------\n";
            if (tep_get_cflag_by_product_id($order->products[$i]['id'])) {
              if (tep_get_bflag_by_product_id($order->products[$i]['id'])) {
                $products_ordered_mail .= "※ 当社キャラクター名は、お取引10分前までに電子メールにてお知らせいたします。\n\n";
              } else {
                $products_ordered_mail .= "※ 当社キャラクター名は、お支払い確認後に電子メールにてお知らせいたします。\n\n";
              }
            }
          }

          $total_details_mail = '';
          $totals_query = tep_db_query("select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . tep_db_input($oID) . "' order by sort_order");
          $order->totals = array();

          while ($totals = tep_db_fetch_array($totals_query)) {

            if ($totals['class'] == "ot_point" || $totals['class'] == "ot_subtotal") {
              if ((int)$totals['value'] >= 1 && $totals['class'] != "ot_subtotal") {
                $total_details_mail .= '▼割引　　　　　　：-' . $currencies->format($totals['value']) . "\n";
                $mailpoint = str_replace('円','',$currencies->format($totals['value']));
              }
            } elseif ($totals['class'] == "ot_total") {
              if($handle_fee) {
                $total_details_mail .= '▼手数料　　　　　：'.$currencies->format($handle_fee)."\n";
              }
              $total_details_mail .= '▼お支払金額　　　：' . $currencies->format($totals['value']) . "\n";
              $mailtotal = $totals['value'];
              $total_price_mail = round($totals['value']);
            } else {
              $total_details_mail .= '▼' . $totals['title'] . str_repeat('　', intval((16 -
                      strlen($totals['title']))/2)) . '：' . $currencies->format($totals['value']) . "\n";
            }
          }



          if ($customer_guest['customers_guest_chk'] != 9)
          {
            //bobhero start{{{
            $mailoption['ORDER_ID']         = $oID;                         //d
            $mailoption['ORDER_DATE']       = tep_date_long(time())  ;      //d 
            $mailoption['USER_NAME']        =  $order->customer['name'] ;
            $mailoption['USER_MAILACCOUNT'] = $order->customer['email_address']; //d
            $mailoption['ORDER_TOTAL']      = $currencies->format($mailtotal);
            @$payment_class = $$payment; 

            $mailoption['TORIHIKIHOUHOU']   =  $order->tori['houhou'];      //?
            $mailoption['ORDER_PAYMENT']    = $order->info['payment_method'] ;  //d
            $trade_time = date('Y年m月d日H時i分', strtotime($_POST['date_orders'].' '.$_POST['start_hour'].':'.$_POST['start_min'].':00')); 
            $trade_time_1 = date('H時i分',strtotime($_POST['date_orders'].' '.$_POST['end_hour'].':'.$_POST['end_min'].':00'));
            $mailoption['ORDER_TTIME']      = $trade_time . '～' . $trade_time_1 .'　（24時間表記）';//d
            //$mailoption['ORDER_COMMENT']    = $notify_comments_mail;// = $comments;
            $mailoption['ORDER_COMMENT']    = $_POST['comments_text'];// = $comments;
            $mailoption['ORDER_PRODUCTS']   = $products_ordered_mail;//?
            $mailoption['ORDER_TMETHOD']    = $insert_torihiki_date;
            $mailoption['SITE_NAME']        = get_configuration_by_site_id('STORE_NAME',$order->info['site_id']);//d
            $mailoption['SITE_MAIL']        = get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS',$order->info['site_id']);//d
            $mailoption['SITE_URL']         = get_url_by_site_id($order->info['site_id']);

            if(isset($comment_arr) && !empty($comment_arr)){
               $mailoption['BANK_NAME']        = $comment_arr['payment_bank_info']['bank_name'];      //?
               $mailoption['BANK_SHITEN']      = $comment_arr['payment_bank_info']['bank_shiten'] ;   //?
               $mailoption['BANK_KAMOKU']      = $comment_arr['payment_bank_info']['bank_kamoku'];    //?
               $mailoption['BANK_KOUZA_NUM']   = $comment_arr['payment_bank_info']['bank_kouza_num'] ;//?
               $mailoption['BANK_KOUZA_NAME']  = $comment_arr['payment_bank_info']['bank_kouza_name'];//?
               $mailoption['ADD_INFO']  = $comment_arr['add_info'];//?
            }
            
            $payment_modules->admin_deal_mailoption($mailoption, $oID, payment::changeRomaji($order->info['payment_method'], PAYMENT_RETURN_TYPE_CODE)); 
            $mailoption['ADD_INFO'] = isset($_SESSION['payment_bank_info'][$oID]['add_info'])?$_SESSION['payment_bank_info'][$oID]['add_info']:'';
            unset($_SESSION['orderinfo_mail_use']);
            $point = $mailpoint;
            if ($point){
              $mailoption['POINT']            = $point;
            }else {
              $mailoption['POINT']            = 0;
            }
            $total_mail_fee = $handle_fee;	    
            if(!isset($total_mail_fee)){
              $total_mail_fee =0;
            }
            $mailoption['MAILFEE']          = $total_mail_fee.'';

            $selected_module = payment::changeRomaji($order->info['payment_method'],
                PAYMENT_RETURN_TYPE_CODE);

            $email =get_configuration_by_site_id("MODULE_PAYMENT_".strtoupper($selected_module)."_MAILSTRING",$order->info['site_id']);
            if($email === false){
              $email =get_configuration_by_site_id("MODULE_PAYMENT_".strtoupper($selected_module)."_MAILSTRING",0);
            }
            foreach ($mailoption as $key=>$value){
              $email = str_replace('${'.strtoupper($key).'}',$value,$email);
              }

              //$email_order = $payment_class->getOrderMailString($mailoption);  
              //bobhero end}}}
            tep_mail($check_status['customers_name'], $check_status['customers_email_address'], 'ご注文ありがとうございます【' . get_configuration_by_site_id('STORE_NAME',$order->info['site_id']) . '】', $email, get_configuration_by_site_id('STORE_OWNER',$order->info['site_id']), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS',$order->info['site_id']),$order->info['site_id']);
          }
          tep_mail(get_configuration_by_site_id('STORE_OWNER',$order->info['site_id']), get_configuration_by_site_id('SENTMAIL_ADDRESS',$order->info['site_id']), 'ご注文ありがとうございます【' . get_configuration_by_site_id('STORE_NAME',$order->info['site_id']) . '】', $email, $check_status['customers_name'], $check_status['customers_email_address'],$order->info['site_id']);
          $customer_notified = '1';

          // 支払方法がクレジットなら決済URLを送る
          $email_credit =  $payment_modules->admin_process_pay_email(
                  payment::changeRomaji($payment_method,PAYMENT_RETURN_TYPE_CODE),
                $order,$total_price_mail);
          if($email_credit){
            if ($customer_guest['customers_guest_chk'] != 9){
              tep_mail($check_status['customers_name'], $check_status['customers_email_address'], 'クレジットカード決済について【' . get_configuration_by_site_id('STORE_NAME',$order->info['site_id']) . '】', $email_credit, get_configuration_by_site_id('STORE_OWNER',$order->info['site_id']), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS',$order->info['site_id']), $order->info['site_id']);
            }
            tep_mail(get_configuration_by_site_id('STORE_OWNER',$order->info['site_id']), get_configuration_by_site_id('SENTMAIL_ADDRESS',$order->info['site_id']), '送信済：クレジットカード決済について【' . get_configuration_by_site_id('STORE_NAME',$order->info['site_id']) . '】', $email_credit, $check_status['customers_name'], $check_status['customers_email_address'], $order->info['site_id']);
          }
          }
          $order_updated_2 = true;
        }

        if ($order_updated && !$products_delete && $order_updated_2) {
          $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
        } elseif ($order_updated && $products_delete) {
          $messageStack->add_session('商品を削除しました。<font color="red">メールは送信されていません。</font>', 'success');
        } else {
          $messageStack->add_session('エラーが発生しました。正常に処理が行われていない可能性があります。', 'error');
        }

        tep_redirect(tep_href_link("edit_new_orders.php", tep_get_all_get_params(array('action')) . 'action=edit'));

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
                   p.products_price_offset
                     from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on pd.products_id=p.products_id 
                     where p.products_id='$add_product_products_id' 
                     and pd.site_id = '0'
                     and pd.language_id = '" . (int)$languages_id . "'";
          $result = tep_db_query($InfoQuery);

          $row = tep_db_fetch_array($result);
          extract($row, EXTR_PREFIX_ALL, "p");

          // 特価を適用
          $p_products_price = tep_get_final_price($p_products_price, $p_products_price_offset, $p_products_small_sum, (int)$add_product_quantity);
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
                  //'products_quantity' => $p['products_quantity'] - (int)$add_product_quantity,
                  'products_real_quantity' => 0,
                  // 'products_virtual_quantity' => $p['products_virtual_quantity'] - ((int)$add_product_quantity - $p['products_real_quantity'])
                  'products_virtual_quantity' => $p['products_virtual_quantity'] - (int)$add_product_quantity + $p['products_real_quantity']
                  ),
                'update',
                "products_id = '" . $add_product_products_id . "'");
          } else {
            tep_db_perform('products',array(
                  //'products_quantity' => $p['products_quantity'] - (int)$add_product_quantity,
                  'products_real_quantity' => $p['products_real_quantity'] - (int)$add_product_quantity
                  // 'products_real_quantity' => $p['products_virtual_quantity'] - (int)$add_product_quantity
                  ),
                'update',
                "products_id = '" . $add_product_products_id . "'");
          }
          // 增加销售量
          tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . (int)$add_product_quantity . " where products_id = '" . $add_product_products_id . "'");
          // 处理负数问题
          //tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = 0 where products_quantity < 0 and products_id = '" . $add_product_products_id . "'");
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
          /*
             , text = '".$currencies->format($new_subtotal, true, $order->info['currency'])."'
           */
          tep_db_query("update " . TABLE_ORDERS_TOTAL . " set value = '".$new_subtotal."' where class='ot_subtotal' and orders_id = '".$oID."'");

          //tax
          $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_ORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
          $plustax = tep_db_fetch_array($plustax_query);
          if($plustax['cnt'] > 0) {
            /*
               , text = '".tep_insert_currency_text($currencies->format($new_tax, true, $order->info['currency']))."'
             */
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
          //	$handle_fee = new_calc_handle_fee($order->info['payment_method'], $newtotal, $oID);
          $handle_fee = $payment_modules->handle_calc_fee(
          payment::changeRomaji($order->info['payment_method'],PAYMENT_RETURN_TYPE_CODE), $newtotal);

          $newtotal = $newtotal+$handle_fee+$shipping_fee;    
          /*
             , text = '<b>".$currencies->ot_total_format(intval(floor($newtotal)), true, $order->info['currency'])."</b>'
           */
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

    $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
    $order_exists = true;
    if (!tep_db_num_rows($orders_query)) {
      $order_exists = false;
      $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
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
    <!--京-->
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="includes/styles.css">
    <script language="javascript" src="includes/general.js"></script>
    <script language="javascript" src="includes/javascript/jquery.js"></script>
    <script language="javascript" src="includes/javascript/jquery_include.js"></script>
    <script language="javascript" src="includes/javascript/all_orders.js"></script>
    <script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
  <script language="javascript" src="includes/3.4.1/build/yui/yui.js"></script>
  <script language="javascript" src="includes/jquery.form.js"></script>
  <script type="text/javascript"> 
  function submit_check(){

    var options = {
    url: 'ajax_orders_weight.php?action=create_new_orders',
    type:  'POST',
    success: function(data) {
      if(data != ''){
        if(confirm(data)){

          update_price2();
        }
      }else{

          update_price2();
      } 
    }
  };
  $('#edit_order_id').ajaxSubmit(options);
  }

  function submit_check_con(){

    var options = {
    url: 'ajax_orders_weight.php?action=create_new_orders',
    type:  'POST',
    success: function(data) {
      if(data != ''){
        if(confirm(data)){

          submitChk();
        }
      }else{

        submitChk();
      } 
    }
  };
  $('#edit_order_id').ajaxSubmit(options);
  }
  <?php
  $address_fixed_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
  while($address_fixed_array = tep_db_fetch_array($address_fixed_query)){

    switch($address_fixed_array['fixed_option']){

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
?> 
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
var address_first_num = 0;
function address_option_show(action){
  switch(action){

  case 'new' :
    arr_new = new Array();
    arr_color = new Array();
    $("#address_list_id").hide();
    check();
    country_check($("#"+country_fee_id).val());
    country_area_check($("#"+country_area_id).val());
    
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
            $("#r_"+x).html("&nbsp;*必須");
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

  address_show_list.options.length = 0;

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
            $("#r_"+x).html("&nbsp;*必須");
          }
        }
        <?php
         }
        ?>
        //$("#error_"+x).html('');
    }
    if(arr_str != ''){
      if(arr_str==address_select){
              address_first_num = i;
      }
      ++j_num;
      if(j_num == 1){first_num = i;}
        if('<?php echo $_POST['address_show_list'];?>' != ''){
          address_show_list.options[address_show_list.options.length]=new Option(arr_str,i,i=='<?php echo $_POST['address_show_list'];?>',i=='<?php echo $_POST['address_show_list'];?>');
        }else{
          if(arr_str == address_str){
            address_show_list.options[address_show_list.options.length]=new Option(arr_str,i,true,true);
          }else{
            address_show_list.options[address_show_list.options.length]=new Option(arr_str,i,arr_str==address_select,arr_str==address_select); 
          }
       }
    }

  }
    //address_option_list(first_num);
    break;
  }
}

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
    ii++; 
   }
  }

}


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

    if(i == 0){

      $("#td_"+country_fee_id_one).hide();
    }else{

      $("#td_"+country_fee_id_one).show();
    }
  }
}
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

    if(i == 0){

      $("#td_"+country_area_id_one).hide();
    }else{

      $("#td_"+country_area_id_one).show();
    }
  }

}

function country_area_check(value,select_value){
   
   var arr = new Array();
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

    if(i == 0){

      $("#td_"+country_city_id_one).hide();
    }else{

      $("#td_"+country_city_id_one).show();
    }
  }

}

<?php 
//------------------------------------------------
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

//------------------------------------------------

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
?>
var address_select = '';
  function address_show_list(){
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
    $add_group_query = tep_db_query("select orders_id from ". TABLE_ADDRESS_ORDERS ." where customers_id={$order->customer['id']} group by orders_id order by orders_id desc limit 0,1");
    $add_group_array = tep_db_fetch_array($add_group_query);
    tep_db_free_result($add_group_query);
    $add_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."'");
    $add_num = tep_db_num_rows($add_query);
    tep_db_free_result($add_query);
    
    if($add_num == 0){

      $oID_id = $add_group_array['orders_id'];
    }else{

      $oID_id = $oID;
    }
    $address_show_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID_id ."'");
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
   
    for(x in address_list){
     if(document.getElementById("ad_"+x)){ 
       var address_id = document.getElementById("ad_"+x);
    if('<?php echo $country_fee_id;?>' == 'ad_'+x){
      check(address_list[x]);
    }else if('<?php echo $country_area_id;?>' == 'ad_'+x){
      country_check(document.getElementById(country_fee_id).value,address_list[x]);
     
    }else if('<?php echo $country_city_id;?>' == 'ad_'+x){
      country_area_check(document.getElementById(country_area_id).value,address_list[x]);
    }else{
      $("#ad_"+x).val(address_list[x]);
      address_id.style.color = '#000';
    }
      
     }
    }

  
  }
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
    min_end.options.length = 0;
    min_value = parseInt(min_value);
    for(m_i = min_value;m_i <= 5;m_i++){
      min_end.options[min_end.options.length]=new Option(m_i,m_i,m_i==min_value); 
    }

    min_end_1.options.length = 0;
    min_1_value = parseInt(min_1_value);
    for(m_i_1 = min_1_value;m_i_1 <= 9;m_i_1++){
      min_end_1.options[min_end_1.options.length]=new Option(m_i_1,m_i_1,m_i_1==min_1_value); 
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
  $(function() {
    //$.datePicker.setDateFormat('ymd', '-');
    //$('#date_orders').datePicker();
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
  function address_show(){
    var style = $("#address_show_id").attr("style");
  if(style == 'display: none;' || style == "display: none"){
    
    $("#address_show_id").show(); 
    $("#address_font").html("住所情報▲");
  }else{
    
    $("#address_show_id").hide();
    $("#address_font").html("住所情報▼");
  }
 }
  //todo:修改通性用
  function hidden_payment(){
  var idx = document.edit_order.elements["payment_method"].selectedIndex;
  var CI = document.edit_order.elements["payment_method"].options[idx].value;
  $(".rowHide").hide();
  $(".rowHide").find("input").attr("disabled","true");
  $(".rowHide_"+CI).show();
  $(".rowHide_"+CI).find("input").removeAttr("disabled");
 }
   $(document).ready(function(){hidden_payment()});

$(document).ready(function(){            
     
   var address_show_list = document.getElementById("address_show_list");
   if(address_show_list){
     <?php
      if(!($_POST['address_option'] == 'new')){
     ?>
     address_option_show('old');
     <?php 
     }
     if(!isset($_GET['action'])){
     ?>
       //address_option_list(first_num);
       address_clear_error();
    <?php
     }
    ?>
   }
});

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
?>
function open_calendar()
{
  var is_open = $('#toggle_open').val(); 
  if (is_open == 0) {
    browser_str = navigator.userAgent.toLowerCase(); 
    if (browser_str.indexOf("msie 9.0") > 0) {
      $('#new_yui3').css('margin-left', '-90px'); 
    }
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
        $("#date_orders").val(dtdate.format(newDate)); 
        $('#toggle_open').val('0');
        $('#toggle_open').next().html('<div id="mycalendar"></div>');
      });
    });
  }
}

$(document).ready(function(){
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
    //check();
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
    //country_check($("#"+country_fee_id).val());
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
    //country_area_check($("#"+country_area_id).val());
  <?php
  }
  ?> 
  $("select[name='payment_method']").change(function(){
    hidden_payment();
  });
});
</script>
    </head>
    <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
    <?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
      <script language='javascript'>
        one_time_pwd('<?php echo $page_name;?>');
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
	height: 16px;
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
	left:580px\9;
}
      </style>
        <!-- header_eof //-->
        <!-- body //-->
        <table border="0" width="100%" cellspacing="2" cellpadding="2">
        <tr>
        <td width="<?php echo BOX_WIDTH; ?>" valign="top">
        <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
        </table>
        </td>
        <!-- body_text //-->
        <td width="100%" valign="top">
        <table border="0" width="96%" cellspacing="0" cellpadding="2">
        <?php
        if (($action == 'edit') && ($order_exists == true)) {
          $order = new order($oID);
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
            <tr><?php echo tep_draw_form('edit_order', "edit_new_orders.php", tep_get_all_get_params(array('action','paycc')) . 'action=update_order', 'post', 'id="edit_order_id"'); ?>

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
            <td class="main" valign="top" width="30%"><b><?php echo ENTRY_SITE;?>:</b></td>
            <td class="main" width="70%"><font color='#FF0000'><b><?php echo tep_get_site_name_by_order_id($oID)?></b></font></td>
            </tr>
            <tr>
            <td class="main" valign="top" width="30%"><b><?php echo EDIT_ORDERS_ID_TEXT;?></b></td>
            <td class="main" width="70%"><?php echo $oID;?></td>
            </tr>
            <tr>
            <td class="main" valign="top"><b><?php echo EDIT_ORDERS_DATE_TEXT;?></b></td>
            <td class="main"><?php echo tep_date_long($order->info['date_purchased']);?></td>
            </tr>
            <tr>
            <td class="main" valign="top"><b><?php echo EDIT_ORDERS_CUSTOMER_NAME;?></b></td>
            <td class="main"><?php echo tep_html_quotes($order->customer['name']); ?></td>
            </tr>
            <tr>
            <td class="main" valign="top"><b><?php echo EDIT_ORDERS_EMAIL;?></b></td>
            <td class="main"><font color="red"><b><?php echo $order->customer['email_address']; ?></b></font></td>
            </tr>
            <!-- End Addresses Block -->
            <!-- Begin Payment Block -->
            <tr>
            <td class="main" valign="top"><b><?php echo EDIT_ORDERS_PAYMENT_METHOD;?></b></td>
            <td class="main">
            <?php
            //    echo tep_payment_method_menu($order->info['payment_method']);
/*
$payment_modules = payment::getInstance($_POST['site_id']);
$payment_method_romaji = payment::changeRomaji($order->info['payment_method'],PAYMENT_RETURN_TYPE_CODE);
$validateModule = $payment_modules->admin_confirmation_check($payment_method_romaji);
$selections = $payment_modules->admin_selection();
$selections[strtoupper($payment_method_romaji)] = $validateModule;

               $payment_array = payment::getPaymentList(); 

               for($i=0; $i<sizeof($payment_array[0]); $i++) {
               $payment_list[] = array('id' => $payment_array[0][$i],
               'text' => $payment_array[1][$i]);
               }
               echo tep_draw_pull_down_menu('payment_method', $payment_list,
                   $order->info['payment_method'],'onchange="hidden_payment()"');
 */        //获取用户最近一次使用的支付方式
          $payment_array = payment::getPaymentList(); //支付方式列表
          $orders_payment_query = tep_db_query("select payment_method,orders_id from ". TABLE_ORDERS ." where customers_email_address='". $order->customer['email_address'] ."' and site_id='".$order->info['site_id']."' order by orders_id desc limit 0,2"); 
          $payment_i = 0;
          while($orders_payment_array = tep_db_fetch_array($orders_payment_query)){

            if($payment_i == 1){

              $payment_num = array_search($orders_payment_array['payment_method'],$payment_array[1]);
              $pay_orders_id = $orders_payment_array['orders_id'];
              $pay_method = $orders_payment_array['payment_method'];
            }
            $payment_i++;
          }
          tep_db_free_result($orders_payment_query);
          
          $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id."' order by date_added desc limit 0,1"); 
    $orders_status_history_array = tep_db_fetch_array($orders_status_history_query);
    $pay_comment = $orders_status_history_array['comments']; 
    tep_db_free_result($orders_status_history_query);
          $code_payment_method = $payment_array[0][$payment_num];
          if($order->info['payment_method'] != ''){
          $code_payment_method =
            payment::changeRomaji($order->info['payment_method'],'code');
            $pay_method = $order->info['payment_method'];
            $pay_comment = $order->info['orders_comment']; 
          }

          if(isset($_POST['payment_method'])){
            $code_payment_method = payment::changeRomaji($_POST['payment_method'],'code');
            $pay_method = $_POST['payment_method'];
          }

          echo payment::makePaymentListPullDownMenu($code_payment_method);
          
          
          echo "\n".'<script language="javascript">'."\n"; 
          echo '$(document).ready(function(){'."\n";
          switch($pay_method){

          case '銀行振込(買い取り)':
            $pay_array = explode("\n",trim($pay_comment));
            $bank_name = explode(':',$pay_array[0]);
            $bank_name[1] = isset($_POST['bank_name']) ? $_POST['bank_name'] : $bank_name[1]; 
            echo 'document.getElementsByName("bank_name")[0].value = "'. $bank_name[1] .'";'."\n"; 
            $bank_shiten = explode(':',$pay_array[1]); 
            $bank_shiten[1] = isset($_POST['bank_shiten']) ? $_POST['bank_shiten'] : $bank_shiten[1];
            echo 'document.getElementsByName("bank_shiten")[0].value = "'. $bank_shiten[1] .'";'."\n"; 
            $bank_kamoku = explode(':',$pay_array[2]);
            $bank_kamoku[1] = isset($_POST['bank_kamoku']) ? $_POST['bank_kamoku'] : $bank_kamoku[1];
            if($bank_kamoku[1] == '普通'){
               echo 'document.getElementsByName("bank_kamoku")[0].checked = true;'."\n"; 
            }else{
               echo 'document.getElementsByName("bank_kamoku")[1].checked = true;'."\n"; 
            }
            $bank_kouza_num = explode(':',$pay_array[3]);
            $bank_kouza_num[1] = isset($_POST['bank_kouza_num']) ? $_POST['bank_kouza_num'] : $bank_kouza_num[1];
            echo 'document.getElementsByName("bank_kouza_num")[0].value = "'.$bank_kouza_num[1].'";'."\n";
            $bank_kouza_name = explode(':',$pay_array[4]);
            $bank_kouza_name[1] = isset($_POST['"bank_kouza_name']) ? $_POST['"bank_kouza_name'] : $bank_kouza_name[1];
            echo 'document.getElementsByName("bank_kouza_name")[0].value = "'.$bank_kouza_name[1].'";'."\n";
            break;
          case 'コンビニ決済':
            $con_email = explode(":",trim($pay_comment));
            $con_email[1] = isset($_POST['con_email']) ? $_POST['con_email'] : $con_email[1];
            echo 'document.getElementsByName("con_email")[0].value = "'.$con_email[1].'";'."\n";
            break;
          case '楽天銀行':
            $rak_tel = explode(":",trim($pay_comment));
            $rak_tel[1] = isset($_POST['rak_tel']) ? $_POST['rak_tel'] : $rak_tel[1];
            echo 'document.getElementsByName("rak_tel")[0].value = "'.$rak_tel[1].'";'."\n";
            break;
          }
          echo '});'."\n";
          echo '</script>'."\n";
      
          $cpayment = payment::getInstance((int)SITE_ID);
          if(!isset($selections)){
            $selections = $cpayment->admin_selection();
          } 
          echo '<tr><td class="main"></td><td class="main"><table>';
          foreach ($selections as $se){
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
                  $field['message'] = $field['message'] != '' ? '必須項目' : ''; 
                }else{
                  $field['message'] = $field['message'] != '' ? '正しく入力してください' : ''; 
                }
              }else{
                if($field['title'] != '口座種別:'){
                  $field['message'] = '*必須';
                }
              }
              echo "<font color='red'>&nbsp;".$field['message']."</font>";
              echo "</td>";
              echo "</tr>";
           } 
         }
         echo '</table></td></tr>';

?>
            </td>
            </tr>
            <!-- End Payment Block -->
            <!-- Begin Trade Date Block -->
            <?php 

            //这里判断 订单商品是否有配送 如果有用自己的配送 如果没有用session的
              $products_weight_total = 0; //商品总重量
              $products_money_total = 0; //商品总价
              $cart_shipping_time = array(); //商品取引时间
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
    $shipping_address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."'");
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
  $j = 0;
  foreach($shipping_time_start as $shipping_key=>$shipping_value){
    foreach($shipping_value as $sh_key=>$sh_value){
      
      $sh_start_array = explode(':',$sh_value);
      $sh_end_array = explode(':', $shipping_time_end[$shipping_key][$sh_key]);
      for($i = (int)$sh_start_array[0];$i <= (int)$sh_end_array[0];$i++){
        if(isset($ship_time_array[$i]) && $ship_time_array[$i] != ''){
          if($ship_temp_array[$i] != $j){$ship_array[$i]++;}
          $ship_time_array[$i] .= '|'.$sh_value.','.$shipping_time_end[$shipping_key][$sh_key];
        }else{
          $ship_time_array[$i] = $sh_value.','.$shipping_time_end[$shipping_key][$sh_key]; 
          $ship_temp_array[$i] = $j;
        }
      } 
    }
    
    $j++;  
  }

  $s_array = array();
  foreach($ship_array as $ship_k=>$ship_v){
    if($ship_v >= $products_num-1){
      $s_array[$ship_k] = $ship_v;
    } 
  } 
  $ship_array = $s_array;
  $shipp_array = array_keys($ship_array);
  sort($shipp_array);
  $ship_new_array = array();
  foreach($shipp_array as $shipp_key=>$shipp_value){
  
    $ship_1_array = explode('|',$ship_time_array[$shipp_value]);
    foreach($ship_1_array as $ship_1_value){

      $ship_2_array = explode(',',$ship_1_value);
      $ship_3_array[$shipp_key][] = $ship_2_array[0];
      $ship_4_array[$shipp_key][] = $ship_2_array[1];
    } 
  }

  foreach($ship_3_array as $ship_3_key=>$ship_3_value){

    natsort($ship_3_array[$ship_3_key]); 
    natsort($ship_4_array[$ship_3_key]);
    $ship_new_array[] = end($ship_3_array[$ship_3_key]).','.current($ship_4_array[$ship_3_key]);
  }
  
  $time_array = array(); 
  $time_array = $ship_new_array;
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
    $time_array = $shi_time_array;
  }
  

  //可配送时间区域
  //获取更新后订单的取引时间
  $orders_time_query = tep_db_query("select torihiki_date,torihiki_date_end from ". TABLE_ORDERS ." where orders_id='". $oID ."'");
  $orders_time_array = tep_db_fetch_array($orders_time_query);
  tep_db_free_result($orders_time_query);
  if($orders_time_array['torihiki_date'] != '0000-00-00 00:00:00' && $orders_time_array['torihiki_date_end'] != '0000-00-00 00:00:00'){
    $orders_temp_time_start = explode(' ',$orders_time_array['torihiki_date']);
    $work_start = substr($orders_temp_time_start[1],0,5);
    $orders_temp_time_end = explode(' ',$orders_time_array['torihiki_date_end']);
    $work_end = substr($orders_temp_time_end[1],0,5);
    $date_orders = date('Y-m-d',strtotime($orders_time_array['torihiki_date']));
  }else{
    $min_value_array = array_keys($time_array);
    $min_value = min($min_value_array);
    $work_temp_str = $time_array[$min_value];
    $work_temp_array = explode(',',$work_temp_str);
    $work_start = $work_temp_array[0];
    $work_end = $work_temp_array[1];
    //当日起几日后可以收货
    $db_set_day = max($shipping_time_array['db_set_day']);
    $date_orders = date('Y-m-d',strtotime("+ ".$db_set_day."day"));

  }
  
  $work_start_array = explode(':',$work_start);
  $work_end_array = explode(':',$work_end);
  $work_start_hour = $work_start_array[0]; //开始时
  $work_start_min = $work_start_array[1]; //开始分
  $work_end_hour = $work_end_array[0]; //结束时
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

  $min_str = '<select name="start_min" id="min" onchange="check_min(this.value);">';
  for($m_i = 0;$m_i <= 5;$m_i++){

    if($m_i == substr($work_start_min,0,1)){

      $selected = ' selected';
    }else{

      $selected = '';
    }
    $min_str .= '<option value="'.$m_i.'"'.$selected.'>'.$m_i.'</option>';
  } 
  $min_str .= '</select>';
  $min_str_start = '<select name="start_min_1" id="min_1" onchange="check_min_1(this.value);">';
  for($m_i_1 = 0;$m_i_1 <= 9;$m_i_1++){

    if($m_i_1 == substr($work_start_min,1,1)){

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

  $min_str_1 = '<select name="end_min" id="min_end" onchange="check_end_min(this.value);">';
  for($m1_i = substr($work_start_min,0,1);$m1_i <= 5;$m1_i++){

    if($m1_i == substr($work_end_min,0,1)){

      $selected = ' selected';
    }else{

      $selected = '';
    }
    $min_str_1 .= '<option value="'.$m1_i.'"'.$selected.'>'.$m1_i.'</option>';
  } 
  $min_str_1 .= '</select>';
  $min_str_end = '<select name="end_min_1" id="min_end_1">';
  for($m1_i_1 = substr($work_start_min,1,1);$m1_i_1 <= 9;$m1_i_1++){

    if($m1_i_1 == substr($work_end_min,1,1)){

      $selected = ' selected';
    }else{

      $selected = '';
    }
    $min_str_end .= '<option value="'.$m1_i_1.'"'.$selected.'>'.$m1_i_1.'</option>';
  } 
  $min_str_end .= '</select>';
  //获取手料费
  $payment_modules = payment::getInstance($order->info['site_id']); 
  $code_payment_method = isset($code_payment_method) && $code_payment_method != '' ? $code_payment_method : 'buying';
  $handle_fee_code = $payment_modules->handle_calc_fee(
    payment::changeRomaji($code_payment_method,PAYMENT_RETURN_TYPE_CODE), $shipping_money_total);
            ?>
            <tr> 
            <td class="main" valign="top"><b><?php echo EDIT_ORDERS_FETCHTIME;?></b></td>
            <td class="main"> 
            <div class="yui3-skin-sam yui3-g"> 
              <input type="text" id="date_orders" size="15" value="<?php echo $date_orders;?>"> 
              <a href="javascript:void(0);" onclick="open_calendar();" class="dpicker"></a> 
              <input type="hidden" id="date_order" name="date_orders" value="<?php echo $date_orders;?>">
              <input type="hidden" name="toggle_open" value="0" id="toggle_open"> 
              <div class="yui3-u" id="new_yui3">
              <div id="mycalendar"></div> 
              </div>
            </div>
            <?php
              echo '&nbsp;'.$hour_str.'&nbsp;時&nbsp;'.$min_str.$min_str_start.'&nbsp;分&nbsp;～&nbsp;'.$hour_str_1.'&nbsp;時&nbsp;'.$min_str_1.$min_str_end.'&nbsp;分&nbsp;';
            ?>
            </td>
            </tr>
            <?php 
              // 住所信息
              if($products_weight_total > 0){
                $address_style = isset($address_style) && $address_style != '' ? $address_style : 'display: none;';
                $old_checked = !isset($_POST['address_option']) || $_POST['address_option'] == 'old' ? 'checked' : '';
                $new_checked = isset($_POST['address_option']) && $_POST['address_option'] == 'new' ? 'checked' : '';
                $address_historys_query = tep_db_query("select * from ". TABLE_ADDRESS_HISTORY ." where customers_id='". $order->customer['id'] ."'");
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
            <tr><td colspan="2"><table width="100%" border="0" cellpadding="2" cellspacing="0" id="address_show_id" style="<?php echo $address_style;?>"><br>
        <tr>
        <td class="main" width="30%">
        <input type="radio" name="address_option" value="old" onClick="address_option_show('old');address_option_list(address_first_num);address_clear_error();" <?php echo $old_checked;?>><?php echo TABLE_OPTION_OLD; ?>
        <input type="radio" name="address_option" value="new" onClick="address_option_show('new');" <?php echo $new_checked;?>><?php echo TABLE_OPTION_NEW; ?> 
        </td>
        <td class="main" width="70%"></td>
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
            </table>
            </td>
            </tr>
            <?php
              }
            ?> 
            <tr>
            <td class="main">
            <?php echo $order->tori['houhou'];?>             
            <input type="hidden" name="update_viladate" value="true">
            <input type="hidden" name="update_customer_name" size="25" value="<?php echo tep_html_quotes($order->customer['name']); ?>">
            <input type="hidden" name="update_customer_email_address" size="45" value="<?php echo $order->customer['email_address']; ?>">
            <input type="hidden" name='update_info_payment_method' size='25' value='<?php echo $order->info['payment_method']; ?>'>
            <input type="hidden" name='update_tori_torihiki_date' size='25' value='<?php echo $order->tori['date']; ?>'>
            <input type="hidden" name='update_tori_torihiki_houhou' size='45' value='<?php echo $order->tori['houhou']; ?>'>

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
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
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
            $only_buy= true;
          for ($i=0; $i<sizeof($order->products); $i++) {
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
            $RowStyle = "dataTableContent";
            echo '    <tr class="dataTableRow">' . "\n" .
              '      <td class="' . $RowStyle . '" align="left" valign="top" width="20">'
              . "<input type='hidden' id='update_products_qty_$orders_products_id' value='" . $products_qty_num . "'><input class='update_products_qty' id='update_products_new_qty_$orders_products_id' name='update_products[$orders_products_id][qty]' size='2' value='" . $products_qty_num . "' onkeyup=\"clearLibNum(this);\">&nbsp;x</td>\n" . 
              '      <td class="' . $RowStyle . '">' . $order->products[$i]['name'] . "<input id='update_products_name_$orders_products_id' name='update_products[$orders_products_id][name]' size='64' type='hidden' value='" . $order->products[$i]['name'] . "'>\n" . 
              '      &nbsp;&nbsp:<input type="hidden" name="dummy" value="あいうえお眉幅">';
            // Has Attributes?
            if (sizeof($order->products[$i]['attributes']) > 0) {
              $op_info_array = array();
              for ($i_num = 0; $i_num < sizeof($order->products[$i]['attributes']); $i_num++) {
                $op_info_array[] = $order->products[$i]['attributes'][$i_num]['id'];
              }
              $op_info_str = implode('|||', $op_info_array);
              for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
                $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['id'];
                echo '<br><div><small>&nbsp;<i><div class="order_option_info"><div class="order_option_title"> - ' . str_replace(array("<br>", "<BR>"), '', tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['title'], array("'"=>"&quot;"))). ': <input type="hidden" name="update_products[' . $orders_products_id .  '][attributes][' . $orders_products_attributes_id . '][option]" size="10" value="' .  tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['title'], array("'"=>"&quot;")) . '">' . 
                  '</div><div class="order_option_value">' . 
                  str_replace(array("<br>", "<BR>"), '', tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['value'], array("'"=>"&quot;"))).'<input type="hidden" name="update_products[' . $orders_products_id .  '][attributes][' . $orders_products_attributes_id . '][value]" size="35" value="' .  tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['value'], array("'"=>"&quot;"));
                //if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
                echo '"></div></div>';
                echo '<div class="order_option_price">';
                echo "<input size='9' type='text' name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][price]' value='".(int)(isset($_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price'])?$_POST['update_products'][$orders_products_id]['attributes'][$orders_products_attributes_id]['price']:$order->products[$i]['attributes'][$j]['price'])."' onkeyup=\"recalc_order_price('".$oID."', '".$orders_products_id."', '1', '".$op_info_str."');\">";
                //if ($order->products[$i]['attributes'][$j]['price'] != '0') {
                  //echo ' ('.$currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty']).')'; 
                //}
                echo TEXT_MONEY_SYMBOL;
                echo '</div>';
                echo '</i></small></div>';
              }
            }

            echo '      </td>' . "\n" .
              '      <td class="' . $RowStyle . '">' . $order->products[$i]['model'] . "<input name='update_products[$orders_products_id][model]' size='12' type='hidden' value='" . $order->products[$i]['model'] . "'>" . '</td>' . "\n" .
              '      <td class="' . $RowStyle . '" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . "<input name='update_products[$orders_products_id][tax]' size='2' type='hidden' value='" . tep_display_tax_value($order->products[$i]['tax']) . "'>" . '%</td>' . "\n";
              echo '<td class="'.$RowStyle.'" align="right"><input type="text" style="text-align:right;" class="once_pwd" name="update_products['.$orders_products_id.'][p_price]" size="9" value="'.tep_display_currency(number_format(abs(isset($_POST['update_products'][$orders_products_id]['p_price'])?$_POST['update_products'][$orders_products_id]['p_price']:$order->products[$i]['price']), 2)).'" onkeyup="recalc_order_price(\''.$oID.'\', \''.$orders_products_id.'\', \'2\',\''.$op_info_str.'\')">'.TEXT_MONEY_SYMBOL.'</td>';
              echo '      <td class="' . $RowStyle . '" align="right">' . "<input
              class='once_pwd' style='text-align:right;' name='update_products[$orders_products_id][final_price]' size='9' value='" . tep_display_currency(number_format(abs($order->products[$i]['final_price']),2)) 
              . "' onkeyup='clearNoNum(this)' >" .
              '<input type="hidden" name="op_id_'.$orders_products_id.'" 
              value="'.tep_get_product_by_op_id($orders_products_id).'">' .TEXT_MONEY_SYMBOL. "\n" . '</td>' . "\n" . 
              '      <td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][a_price]">';
            if ($order->products[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']);
            }
            echo '</div></td>' . "\n" . 
              '      <td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][b_price]">';
            if ($order->products[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']);
            }
            echo '</div></td>' . "\n" . 
              '      <td class="' . $RowStyle . '" align="right"><div id="update_products['.$orders_products_id.'][c_price]"><b>';
            if ($order->products[$i]['final_price'] < 0) {
              echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
            } else {
              echo $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']);
            }
            echo '</b></div></td>' . "\n" . 
              '    </tr>' . "\n";
          }
          ?>
            </table>

            </td>
            <tr>
            <td>
            <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
            <td valign="top"><?php echo "<span class='smalltext'>" .  HINT_DELETE_POSITION . "</span>"; ?></td> <td align="right"><?php echo '<a href="create_order.php?oID=' . $oID . '&Customer_mail='.$order->customer['email_address'].'&site_id=1">' . tep_html_element_button(ADDING_TITLE) . '</a>'; ?></td>
            <!--
            <td valign="top"><?php echo "<span class='smalltext'>" .  HINT_DELETE_POSITION . EDIT_ORDERS_ADD_PRO_READ . "</span>"; ?></td> <td align="right"><?php echo '<a href="' . $PHP_SELF . '?oID=' . $oID . '&action=add_product&step=1">' . tep_html_element_button(ADDING_TITLE) . '</a>'; ?></td>
            -->
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

            <table width="100%" border="0" cellspacing="0" cellpadding="2" class="dataTableRow">
            <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent" align="left" width="75%"><?php echo TABLE_HEADING_FEE_MUST?></td>
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
          
          foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
            $TotalStyle = "smallText";
            if ($TotalDetails["Class"] == "ot_total") {
              $shipping_fee_total = ($shipping_ot_subtotal+$shipping_fee+$shipping_ot_tax+$order->info["code_fee"]-$shipping_ot_point) != $TotalDetails["Price"] ? $shipping_fee : 0;
              $shipping_fee_total += ($shipping_ot_subtotal+$shipping_fee+$shipping_ot_tax+$handle_fee_code-$shipping_ot_point) != $TotalDetails["Price"] ? $handle_fee_code : 0;
              echo '  <tr>' . "\n" .
                '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_OTTOTAL_READ.'</td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' . $TotalDetails["Name"] . '</b></td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' ;
              if (($TotalDetails["Price"]+$shipping_fee_total) >= 0){
                echo $currencies->ot_total_format($TotalDetails["Price"]+$shipping_fee_total, true,
                    $order->info['currency'], $order->info['currency_value']);
              }else{
                echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->ot_total_format($TotalDetails["Price"]+$shipping_fee_total, true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
              }
              echo '</b>' . 
                "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . ($TotalDetails["Price"]+$shipping_fee_total) . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</b></td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
                '  </tr>' . "\n";
            } elseif ($TotalDetails["Class"] == "ot_subtotal") {
              $shipping_ot_subtotal = $TotalDetails["Price"];
              echo '  <tr>' . "\n" .
                '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_OTSUBTOTAL_READ.'</td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' . $TotalDetails["Name"] . '</b></td>' .
                '    <td align="right" class="' . $TotalStyle . '"><b>';
              if($TotalDetails["Price"] >= 0){
                echo $currencies->format($TotalDetails["Price"], true,
                    $order->info['currency'], $order->info['currency_value']);
              }else{
                echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($TotalDetails["Price"], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
              }
              echo '</b>' . 
                "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</b></td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
                '  </tr>' . "\n".
                '  <tr>' . "\n" .
                '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>'.TEXT_CODE_HANDLE_FEE.'</b></td>' .
                '    <td align="right" class="' . $TotalStyle . '"><b>' . $currencies->format($handle_fee_code) . '</b><input type="hidden" name="payment_code_fee" value="'.$order->info["code_fee"].'">' . 
                '</td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
                '  </tr>' . "\n".
                '  <tr>' . "\n" .
                '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>'.TEXT_CODE_SHIPPING_FEE.'</b></td>' .
                '    <td align="right" class="' . $TotalStyle . '"><b>' . $currencies->format($shipping_fee) . '</b>' . 
                '</td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
                '  </tr>' . "\n";
            } elseif ($TotalDetails["Class"] == "ot_tax") {
              $shipping_ot_tax = $TotalDetails["Price"];
              echo '  <tr>' . "\n" . 
                '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' . trim($TotalDetails["Name"]) . "</b><input name='update_totals[$TotalIndex][title]' type='hidden' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . '</td>' . "\n" .
                '    <td align="right" class="' . $TotalStyle . '"><b>' . $currencies->format($TotalDetails["Price"], true, $order->info['currency'], $order->info['currency_value']) . '</b>' . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</b></td>' . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
                '  </tr>' . "\n";
            } elseif ($TotalDetails["Class"] == "ot_point") {
              $shipping_ot_point = $TotalDetails["Price"];
              if ($customer_guest['customers_guest_chk'] == 0) { //会員
                $current_point = $customer_point['point'] + $TotalDetails["Price"];
                echo '  <tr>' . "\n" .
                  '    <td align="left" class="' . $TotalStyle . '">このお客様は会員です。入力可能ポイントは <font color="red"><b>残り' . $customer_point['point'] . '（合計' . $current_point . '）</b></font> です。−（マイナス）符号の入力は必要ありません。必ず正数を入力するように！</td>' . 
                  '    <td align="right" class="' . $TotalStyle . '">' . trim($TotalDetails["Name"]) . '</td>' . "\n" .
                  '    <td align="right" class="' . $TotalStyle . '" nowrap>−' . "<input name='update_totals[$TotalIndex][value]' size='6' value='" . $TotalDetails["Price"] . "'>" . 
                  "<input type='hidden' name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . 
                  "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
                  "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
                  "<input type='hidden' name='before_point' value='" . $TotalDetails["Price"] . "'>" . 
                  '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
                  '   </td>' . "\n" .
                  '  </tr>' . "\n";
              } else { //ゲスト
                echo '  <tr>' . "\n" .
                  '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_TOTALDETAIL_READ.'</td>' . 
                  '    <td align="right" class="' . $TotalStyle . '">' . trim($TotalDetails["Name"]) . '</td>' . "\n" .
                  '    <td align="right" class="' . $TotalStyle . '">' . $TotalDetails["Price"] . 
                  "<input type='hidden' name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . 
                  "<input type='hidden' name='update_totals[$TotalIndex][value]' size='6' value='" . $TotalDetails["Price"] . "'>" . 
                  "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
                  "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
                  '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
                  '   </td>' . "\n" .
                  '  </tr>' . "\n";
              }
            } else {
              echo '  <tr>' . "\n" .
                '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_TOTALDETAIL_READ_ONE.'</td>' . 
                '    <td align="right" class="' . $TotalStyle . '">' . "<input name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . '</td>' . "\n" .
                '    <td align="right" class="' . $TotalStyle . '">' . "<input name='update_totals[$TotalIndex][value]' size='6' value='" . $TotalDetails["Price"] . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
                '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
                '   </td>' . "\n" .
                '  </tr>' . "\n";
            }
          }
          ?>
            </table>
            <span class='smalltext'><?php echo EDIT_ORDERS_PRICE_CONSTRUCT_READ;?></span>
            </td>
            </tr>
            <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
            </tr>
            <!-- End Order Total Block -->
            <!-- Begin Update Block -->
            <!-- Improvement: more "Update" buttons (Michel Haase, 2005-02-18) -->
            <tr>
            <td>
            <table width="100%" border="0" cellpadding="2" cellspacing="1">
            <tr>
            <td class="main" bgcolor="#FFDDFF" height="25"><?php echo EDIT_ORDERS_CONFIRMATION_READ;?></td>
            <td class="main" bgcolor="#FFBBFF" width="10">&nbsp;</td>
            <td class="main" bgcolor="#FF99FF" width="10">&nbsp;</td>
            <td class="main" bgcolor="#FF77FF" width="10">&nbsp;</td>
            <td class="main" bgcolor="#FF55FF" width="120" align="center">
            <?php if (tep_is_oroshi($order->customer['id'])) { ?>
              <INPUT type="button" value="<?php echo EDIT_ORDERS_CONFIRM_BUTTON;?>" onClick="update_price()">
                <?php } else { ?>
                  <INPUT type="button" value="<?php echo EDIT_ORDERS_CONFIRM_BUTTON;?>" onClick="submit_check();">
                    <?php } ?>
                    </td>
                    </tr>
                    </table>
                    </td>
                    </tr>
                    <tr>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
                    </tr>   
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

                    <table border="0" cellspacing="0" cellpadding="2" class="dataTableRow">
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
                        </tr>
                        <?php
                        $orders_history_query = tep_db_query("select * from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' order by date_added");
          if (tep_db_num_rows($orders_history_query)) {
            while ($orders_history = tep_db_fetch_array($orders_history_query)) {
              echo '  <tr>' . "\n" .
                '    <td class="smallText" align="center">' . tep_datetime_short($orders_history['date_added']) . '</td>' . "\n" .
                '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
                '    <td class="smallText" align="center">';
              if ($orders_history['customer_notified'] == '1') {
                echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK) . "</td>\n";
              } else {
                echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS) . "</td>\n";
              }
              echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
                '    <td class="smallText" align="left">' . $orders_status_array[$orders_history['orders_status_id']] . '</td>' . "\n";
              if ($CommentsWithStatus) {
                echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
                  '    <td class="smallText" align="left">' . nl2br(tep_db_output($orders_history['comments'])) . '&nbsp;</td>' . "\n";
              }
              echo '  </tr>' . "\n";
            }
          } else {
            //echo '  <tr>' . "\n" .
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
            <td valign="top">
            <table border="0" cellspacing="0" cellpadding="2">
            <tr>
<?php
          $order_status_query = tep_db_query("select * from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='". $oID ."' order by orders_status_history_id desc limit 0,1");            
          $order_status_num = tep_db_num_rows($order_status_query);
          $order_status_array = tep_db_fetch_array($order_status_query);
          $select_status = $order_status_array['orders_status_id'];
          $customer_notified = $order_status_array['customer_notified'];           
          $customer_notified = isset($customer_notified) ? $customer_notified : true;
          $customer_notified = $select_status == 31 ? 0 : $customer_notified;
          
?>
            <td class="main"><b><?php echo ENTRY_STATUS; ?></b></td>
            <td class="main"><?php echo tep_draw_pull_down_menu('s_status', $orders_statuses, $select_status, 'onChange="new_mail_text_orders(this, \'s_status\',\'comments\',\'title\')"');?>&nbsp;&nbsp;<?php echo EDIT_ORDERS_ORIGIN_VALUE_TEXT;?></td>
            </tr>
            <?php

            $ma_se = "select * from ".TABLE_ORDERS_MAIL." where ";
          if(!isset($_GET['status']) || $_GET['status'] == ""){
            $ma_se .= " orders_status_id = '".$order->info['orders_status']."' ";
            //echo '<input type="hidden" name="status" value="' .$order->info['orders_status'].'">';

            // 用来判断是否选中 送信&通知，如果nomail==1则不选中
            $ma_s = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$order->info['orders_status']."'"));
          }else{
            $ma_se .= " orders_status_id = '".$_GET['status']."' ";
            //echo '<input type="hidden" name="status" value="' .$_GET['status'].'">';

            // 用来判断是否选中 送信&通知，如果nomail==1则不选中
            $ma_s = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$_GET['status']."'"));
          }
          $ma_se .= "and site_id='0'";
          $mail_sele = tep_db_query($ma_se);
          $mail_sql  = tep_db_fetch_array($mail_sele);
          $sta       = isset($_GET['status'])?$_GET['status']:'';
          ?>

            <tr>
            <td class="main"><b><?php echo ENTRY_EMAIL_TITLE; ?></b></td>
            <td class="main"><?php echo tep_draw_input_field('title', $mail_sql['orders_status_title'],'style="width:315px;"'); ?></td>
            </tr>
            <tr>
            <td class="main"><?php echo EDIT_ORDERS_SEND_MAIL_TEXT;?></b></td>
            <td class="main"><table bgcolor="red" cellspacing="5"><tr><td><?php echo tep_draw_checkbox_field('notify', '', $customer_notified,'id="notify"'); ?></td></tr></table></td>
            </tr>
            <?php if($CommentsWithStatus) { ?>
              <tr>
                <td class="main"><b><?php echo EDIT_ORDERS_RECORD_TEXT;?></b></td>
                <td class="main"><?php echo tep_draw_checkbox_field('notify_comments', '', false); ?>&nbsp;&nbsp;<b style="color:#FF0000;"><?php echo EDIT_ORDERS_RECORD_READ;?></b></td>
                </tr>
              <tr>
                <td class="main" valign="top"><b><?php echo TABLE_HEADING_COMMENTS;?>:</b></td>
                <td class="main"><?php echo tep_draw_textarea_field('comments_text', 'hard', '74', '5', '','style=" font-family:monospace; font-size:12px; width:400px;"'); ?></td>
                </tr>
                <?php } ?>
                </table>
                </td>
                <td class="main" width="10">&nbsp;</td>
                <td class="main">
                <?php echo EDIT_ORDERS_RECORD_ARTICLE;?><br>
                <?
                if($CommentsWithStatus) {

                  //<textarea style="font-family:monospace;font-size:x-small" name="comments" wrap="hard" rows="30" cols="74"></textarea>

                  echo tep_draw_textarea_field('comments', 'hard', '74', '30', isset($order->info['comments'])?$order->info['comments']:str_replace('     ${ORDER_A}',orders_a($order->info['orders_id']),$mail_sql['orders_status_mail']),'style=" font-family:monospace; font-size:12px; width:400px;"');
                  //    echo tep_draw_textarea_field('comments', 'soft', '40', '5');
                } else {
                  echo tep_draw_textarea_field('comments', 'hard', '74', '30', isset($order->info['comments'])?$order->info['comments']:str_replace('     ${ORDER_A}',orders_a($order->info['orders_id']),$mail_sql['orders_status_mail']),'style=" font-family:monospace; font-size:12px; width:400px;"');
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
            <td class="main" bgcolor="#FFDDFF"><b><?php echo EDIT_ORDERS_FINAL_CONFIRM_TEXT;?></b>&nbsp;<?php echo HINT_PRESS_UPDATE; ?></td>
            <td class="main" bgcolor="#FFBBFF" width="10">&nbsp;</td>
            <td class="main" bgcolor="#FF99FF" width="10">&nbsp;</td>
            <td class="main" bgcolor="#FF77FF" width="10">&nbsp;</td>
            <td class="main" bgcolor="#FF55FF" width="120" align="center"><INPUT type="button" value="<?php echo IMAGE_UPDATE;?>" onClick="submit_check_con();"></td>
            </tr>
            </table>
            </td>
            </tr>
            <tr>
            <td>
            <?php echo EDIT_ORDERS_UPDATE_COMMENT;?> 
            </td>
            </tr>
            <!-- End of Update Block -->
            </form>
            <?php
        }
      if ($action == "add_product") {
        ?>
          <tr>
          <td width="100%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
          <td class="pageHeading"><?php echo ADDING_TITLE; ?> (Nr. <?php echo $oID; ?>)</td>
          <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
          <td class="pageHeading" align="right"><?php echo '<a href="' .  tep_href_link(FILENAME_EDIT_NEW_ORDERS, tep_get_all_get_params(array('action'))) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td>
          </tr>
          </table>
          </td>
          </tr>

          <?php
          // ############################################################################
          //   Get List of All Products
          // ############################################################################

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


        // ############################################################################
        //   Add Products Steps
        // ############################################################################

        print "<tr><td><table border='0'>\n";

        // Set Defaults
        if(!isset($add_product_categories_id))
          $add_product_categories_id = 0;

        if(!isset($add_product_products_id))
          $add_product_products_id = 0;

        // Step 1: Choose Category
        print "<tr class=\"dataTableRow\"><form action='$PHP_SELF?oID=$oID&action=$action' method='POST'>\n";
        print "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 1:</b></td>\n";
        print "<td class='dataTableContent' valign='top'>";
        echo ' ' . tep_draw_pull_down_menu('add_product_categories_id', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"');
        print "<input type='hidden' name='step' value='2'>";
        print "</td>\n";
        print "<td class='dataTableContent'>" . ADDPRODUCT_TEXT_STEP1 . "</td>\n";
        print "</form></tr>\n";
        print "<tr><td colspan='3'>&nbsp;</td></tr>\n";

        // Step 2: Choose Product
        if(($step > 1) && ($add_product_categories_id > 0))
        {
          print "<tr class=\"dataTableRow\"><form action='$PHP_SELF?oID=$oID&action=$action' method='POST'>\n";
          print "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 2: </b></td>\n";
          print "<td class='dataTableContent' valign='top'><select name=\"add_product_products_id\" onChange=\"this.form.submit();\">";
          $ProductOptions = "<option value='0'>" .  ADDPRODUCT_TEXT_SELECT_PRODUCT . "\n";
          asort($ProductList[$add_product_categories_id]);
          foreach($ProductList[$add_product_categories_id] as $ProductID => $ProductName)
          {
            $ProductOptions .= "<option value='$ProductID'> $ProductName\n";
          }
          $ProductOptions = str_replace("value='$add_product_products_id'","value='$add_product_products_id' selected", $ProductOptions);
          print $ProductOptions;
          print "</select></td>\n";
          print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
          print "<input type='hidden' name='step' value='3'>\n";
          print "<input type='hidden' name='cstep' value='1'>\n";
          print "<td class='dataTableContent'>" . ADDPRODUCT_TEXT_STEP2 . "</td>\n";
          print "</form></tr>\n";
          print "<tr><td colspan='3'>&nbsp;</td></tr>\n";
        }
        require('option/HM_Option.php');
        require('option/HM_Option_Group.php');
        $hm_option = new HM_Option();
        if(($step == 3) && ($add_product_products_id > 0) && isset($_POST['action_process'])) {
          if (!$hm_option->check()) {
            $step = 4; 
          }
        }
        // Step 3: Choose Options
        if(($step > 2) && ($add_product_products_id > 0))
        {
          $option_product_raw = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$add_product_products_id."'"); 
          $option_product = tep_db_fetch_array($option_product_raw); 
          if(!$hm_option->admin_whether_show($option_product['belong_to_option']))
          {
            print "<tr class=\"dataTableRow\">\n";
            print "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 3: </b></td>\n";
            print "<td class='dataTableContent' valign='top' colspan='2'><i>" . ADDPRODUCT_TEXT_OPTIONS_NOTEXIST . "</i></td>\n";
            print "</tr>\n";
            $step = 4;
          }
          else
          {

            print "<tr class=\"dataTableRow\">";
            print "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 3: </b></td><td class='dataTableContent' valign='top'>";
            print "<form name='aform' action='$PHP_SELF?oID=$oID&action=$action' method='POST'>\n";
            print $hm_option->render($option_product['belong_to_option'], false, 2); 
            print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
            print "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
            print "<input type='hidden' name='step' value='3'>";
            print "<input type='hidden' name='action_process' value='1'>";
            print "</form>"; 
            print "</td>";
            print "<td class='dataTableContent' align='center'><input type='button' value='" . ADDPRODUCT_TEXT_OPTIONS_CONFIRM . "' onclick='document.forms.aform.submit();'>";
            print "</td>\n";
            //print "</form></tr>\n";
            print "</tr>\n";
          }

          echo "<tr><td colspan='3'>&nbsp;</td></tr>\n";
        }

        // Step 4: Confirm
        if($step > 3)
        {
          echo "<tr class=\"dataTableRow\"><form action='$PHP_SELF?oID=$oID&action=$action' method='POST'>\n";
          echo "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 4: </b></td>";
          echo '<td class="dataTableContent" valign="top">' .  ADDPRODUCT_TEXT_CONFIRM_QUANTITY . '<input name="add_product_quantity" size="2" value="1" onkeyup="clearLibNum(this);">&nbsp;'.EDIT_ORDERS_NUM_UNIT.'&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="dummy" value="あいうえお眉幅"></td>';
          echo "<td class='dataTableContent' align='center'><input type='submit' value='" . ADDPRODUCT_TEXT_CONFIRM_ADDNOW . "'>";

          foreach ($_POST as $op_key => $op_value) {
            $op_pos = substr($op_key, 0, 3);
            if ($op_pos == 'op_') {
              echo "<input type='hidden' name='".$op_key."' value='".$op_value."'>"; 
            }
          }
          echo "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
          echo "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
          echo "<input type='hidden' name='step' value='5'>";
          echo "</td>\n";
          echo "</form></tr>\n";
        }

        echo "</table></td></tr>\n";
      }  
      ?>
        </table></td>
        <!-- body_text_eof //-->
        </tr>
        </table>
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
