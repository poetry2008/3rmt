<?php
/*
   $Id$
   
   创建订单
*/

  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');
  include(DIR_FS_ADMIN . DIR_WS_LANGUAGES .  '/default.php');
  include(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/edit_preorders.php');
  require(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/step-by-step/edit_preorders.php');
  if(!isset($_SESSION['create_preorder'])){
    tep_redirect(tep_redirect(tep_href_link('create_preorder.php', null, 'SSL')));
  }
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies(2);
  
  include(DIR_WS_CLASSES . 'preorder.php');
  unset($cpayment); 
  $cpayment = payment::getInstance((int)$_SESSION['create_preorder']['orders']['site_id']);
// START CONFIGURATION ################################
// Optional Tax Rates, e.g. shipping tax of 17.5% is "17.5"
  $AddCustomTax = "19.6";  // new
  $AddShippingTax = "19.6";  // new
  $AddLevelDiscountTax = "19.6";  // new
  $AddCustomerDiscountTax = "19.6";  // new
  
// END OF CONFIGURATION ################################


  // New "Status History" table has different format.
  $OldNewStatusValues = (tep_field_exists(TABLE_PREORDERS_STATUS_HISTORY, "old_value") && tep_field_exists(TABLE_PREORDERS_STATUS_HISTORY, "new_value"));
  $CommentsWithStatus = tep_field_exists(TABLE_PREORDERS_STATUS_HISTORY, "comments");
  $SeparateBillingFields = tep_field_exists(TABLE_PREORDERS, "billing_name");
  
  $orders_statuses = array();
  $orders_status_array = array();
  $orders_status_query = tep_db_query("
      select orders_status_id, 
             orders_status_name 
      from " . TABLE_PREORDERS_STATUS . " 
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
      from " . TABLE_PREORDERS_PRODUCTS . " 
      where orders_id = '" . tep_db_input($oID) . "'");
  
  // 获取最新预约信息
  $order = $_SESSION['create_preorder']['orders'];
  // 获取返点
  $customer_point_query = tep_db_query("
      select point 
      from " . TABLE_CUSTOMERS . " 
      where customers_id = '" . $order['customers_id'] . "'");
  $customer_point = tep_db_fetch_array($customer_point_query);

  // 游客检查
  $customer_guest_query = tep_db_query("
      select customers_guest_chk, is_send_mail, is_calc_quantity 
      from " . TABLE_CUSTOMERS . " 
      where customers_id = '" . $order['customers_id'] . "'");
  $customer_guest = tep_db_fetch_array($customer_guest_query);
  
  if (tep_not_null($action)) {
    switch ($action) {
/* -----------------------------------------------------
   case 'check_session' 检查session    
   case 'update_order' 创建预约订单      
------------------------------------------------------*/
      case 'check_session':
        if (
             !$_SESSION['create_preorder']['orders'] 
          || !$_SESSION['create_preorder']['orders']['orders_id'] 
          || !$_SESSION['create_preorder']['orders']['customers_id']
        ) {
          echo 'error';
        }
        exit;
        break;
  // 1. UPDATE ORDER ###############################################################################################
  case 'update_order':
   if(!isset($_POST['payment_method']) ||$_POST['payment_method']=='' ||!$_POST['payment_method']){
      $_SESSION['pre_payment_empty_error'] = TEXT_NO_PAYMENT_ENABLED;
      tep_redirect(tep_href_link("edit_new_preorders.php", tep_get_all_get_params(array('action')) . 'action=edit'));
    }else{
      $payment_method = tep_db_prepare_input($_POST['payment_method']); 
      $payment_continue = tep_get_payment_flag( $payment_method,$_SESSION['create_preorder']['orders']['customers_id'],$_SESSION['create_preorder']['orders']['site_id'],'',true,'preorder');
      if(!$payment_continue){
        $_SESSION['pre_payment_empty_error'] = TEXT_SELECT_PAYMENT_ERROR;
        tep_redirect(tep_href_link("edit_new_preorders.php", tep_get_all_get_params(array('action')) . 'action=edit'));
      }
    }
  $update_user_info = tep_get_user_info($ocertify->auth_user);
  $payment_method_romaji = payment::changeRomaji($payment_method,PAYMENT_RETURN_TYPE_CODE);
  $payment_modules = payment::getInstance($_SESSION['create_preorder']['orders']['site_id']);
  $validateModule = $payment_modules->admin_confirmation_check($payment_method);
  $comments_text = stripslashes(tep_db_input($_POST['comments_text']));
  $comment_arr = $payment_modules->dealComment($payment_method,$comments_text);

  if ($validateModule['validated']===false){

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
    $messageStack->add_session(ERROR_VILADATE_NEW_PREORDERS, 'error');
    tep_redirect(tep_href_link("edit_new_preorders.php", tep_get_all_get_params(array('action')) . 'action=edit'));
    break;
  }
  
    $oID = tep_db_prepare_input($_GET['oID']);
    
    $_SESSION['create_preorder']['orders']['payment_method'] = payment::changeRomaji($_POST['payment_method'], PAYMENT_RETURN_TYPE_TITLE); 
     
    $preorder_status_raw = tep_db_query("select orders_status_name from ".TABLE_PREORDERS_STATUS." where orders_status_id = '".$_POST['status']."'"); 
    $preorder_status = tep_db_fetch_array($preorder_status_raw);
     
    $_SESSION['create_preorder']['orders']['orders_status'] = $_POST['status']; 
    $_SESSION['create_preorder']['orders']['orders_status_name'] = $preorder_status['orders_status_name']; 
   

    $order = $_SESSION['create_preorder']['orders']; 
     
    if ($update_totals) 
    foreach ($update_totals as $total_index => $total_details) {    
      extract($total_details,EXTR_PREFIX_ALL,"ot");
      if ($ot_class == "ot_point" && (int)$ot_value > 0) {
        $current_point = $customer_point['point'] + $before_point;
        if ((int)$ot_value > $current_point) {
          $messageStack->add(sprintf(ERROR_NEW_PREORDERS_POINT, $current_point), 'error');
          $action = 'edit';
          break 2;
        }
      }
    }

    // 1.1 UPDATE ORDER INFO #####
    $order_updated = true;

    $check_status_query = tep_db_query("select customers_name, customers_email_address, orders_status, date_purchased from " . TABLE_PREORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
    $check_status = tep_db_fetch_array($check_status_query);

  // fin mise ・jour
  // 1.3 UPDATE PRODUCTS #####
  
  $RunningSubTotal = 0;
  $RunningTax = 0;
  
  // Do pre-check for subtotal field existence (CWS)
  $ot_subtotal_found = false;
  
  if ($update_totals) 
  foreach ($update_totals as $total_details) {
    extract($total_details,EXTR_PREFIX_ALL,"ot");
    if($ot_class == "ot_subtotal") {
      $ot_subtotal_found = true;
      break;
    }
  }
  
  // 1.3.1 Update orders_products Table
  $products_delete = false;


  if ($update_products)
  foreach ($update_products as $orders_products_id => $products_details) {
    // 1.3.1.1 Update Inventory Quantity



    if($products_details["qty"] > 0) { 
      // a.) quantity found --> add to list & sum    
      
      $_SESSION['create_preorder']['orders_products'][$orders_products_id]['products_model'] = $products_details["model"];
      $_SESSION['create_preorder']['orders_products'][$orders_products_id]['products_name'] = str_replace("'", "&#39;", $products_details["name"]);
      $_SESSION['create_preorder']['orders_products'][$orders_products_id]['final_price'] = (tep_get_bflag_by_product_id((int)$orders_products_id) ? 0 - $products_details["final_price"] : $products_details["final_price"]);
      $_SESSION['create_preorder']['orders_products'][$orders_products_id]['products_price'] = (tep_get_bflag_by_product_id((int)$orders_products_id) ? 0 - $products_details["p_price"] : $products_details["p_price"]);;
      $_SESSION['create_preorder']['orders_products'][$orders_products_id]['products_tax'] = $products_details["tax"];
      $_SESSION['create_preorder']['orders_products'][$orders_products_id]['products_quantity'] = $products_details["qty"];
      
  
// Update Tax and Subtotals: please choose sum WITH or WITHOUT tax, but activate only ONE version ;-)
  
  
      $RunningSubTotal += $products_details["qty"] * $products_details["final_price"]; // version WITHOUT tax
      $RunningTax += (($products_details["tax"]/100) * ($products_details["qty"] * $products_details["final_price"]));
  
      // Update Any Attributes
      if (IsSet($products_details["attributes"])) {
        foreach ($products_details["attributes"] as $attributes_id => $attributes_details) {
          $_SESSION['create_preorder']['orders_products_attributes'][$orders_products_id][$attributes_id]['option_info'] = array('title' => $attributes_details['option'], 'value' => $attributes_details['value']);
          $_SESSION['create_preorder']['orders_products_attributes'][$orders_products_id][$attributes_id]['options_values_price'] = tep_db_input($attributes_details['price']);
        }
      }
    } else { // b.) null quantity found --> delete
      unset($_SESSION['create_preorder']['orders_products'][$orders_products_id]);
      unset($_SESSION['create_preorder']['orders_products_attributes'][$orders_products_id]);
      $products_delete = true;
    }
  }
  
  // update total
  $total_key_temp = ''; 
  foreach ($update_totals as $total_key=>$total_value){

    if($total_value['class'] == 'ot_custom' && trim($total_value['title']) == '' && $total_key_temp == ''){
      $total_key_temp = $total_key;
    }
    if($total_value['class'] == 'ot_custom' && trim($total_value['title']) != '' && $total_key_temp != ''){

      $update_totals_temp = $update_totals[$total_key_temp];
      $update_totals[$total_key_temp] = $total_value;
      $update_totals[$total_key] = $update_totals_temp;
      break;
    }
  }  
  // 1.4. UPDATE SHIPPING, DISCOUNT & CUSTOM TAXES #####

  foreach($update_totals as $total_index => $total_details) {
    extract($total_details,EXTR_PREFIX_ALL,"ot");
  
// Here is the major caveat: the product is priced in default currency, while shipping etc. are priced in target currency. We need to convert target currency
// into default currency before calculating RunningTax (it will be converted back before display)
    if ($ot_class == "ot_shipping" || $ot_class == "ot_lev_discount" || $ot_class == "ot_customer_discount" || $ot_class == "ot_custom" || $ot_class == "ot_cod_fee") {
      $order = $_SESSION['create_preorder']['orders'];
      $RunningTax += $ot_value * $products_details['tax'] / $order['currency_value'] / 100 ; // corrected tax by cb
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
  unset($_SESSION['create_preorder']['orders_total']);
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
  
      $order = $_SESSION['create_preorder']['orders'];

/* 需要加到订单生成中去 */

      $ot_text = $currencies->format($ot_value, true, $order['currency'], $order['currency_value']);
  
      if ($ot_class == "ot_total") {
        $ot_text = "<b>" . $ot_text . "</b>";
      }
      // 处理手续费失效的问题
      if ($ot_class == 'ot_custom') {
        $_SESSION['create_preorder']['orders_total'][] = array(
          'orders_id' => $oID ,
          'title' => $ot_title ,
          'text' =>  tep_insert_currency_text($ot_text) ,
          'value' => tep_insert_currency_value($ot_value) ,
          'class' => $ot_class ,
          'sort_order' => $sort_order 
        );
      } else {
        $_SESSION['create_preorder']['orders_total'][$ot_class] = array(
          'orders_id' => $oID ,
          'title' => $ot_title ,
          'text' =>  tep_insert_currency_text($ot_text) ,
          'value' => tep_insert_currency_value($ot_value) ,
          'class' => $ot_class ,
          'sort_order' => $sort_order 
        );
      }

      if ($ot_class == "ot_shipping" || $ot_class == "ot_lev_discount" || $ot_class == "ot_customer_discount" || $ot_class == "ot_custom" || $ot_class == "ot_cod_fee") {
        // Again, because products are calculated in terms of default currency, we need to align shipping, custom etc. values with default currency
        $RunningTotal += $ot_value / $order['currency_value'];
      } else {
        $RunningTotal += $ot_value;
      }

    } elseif (
      ($ot_class != "ot_shipping") && ($ot_class != "ot_point")) { 
      // Delete Total Piece
      unset($_SESSION['create_preorder']['orders_total'][$ot_class]);
    }
  
  }

  
  $order = $_SESSION['create_preorder']['orders'];
  $RunningSubTotal = 0;
  $RunningTax = 0;
  
  foreach ($_SESSION['create_preorder']['orders_products'] as $pid => $order_products) {
    if (DISPLAY_PRICE_WITH_TAX == 'true') {
      $RunningSubTotal += (tep_add_tax(($order_products['products_quantity'] * $order_products['final_price']), $order_products['products_tax']));
    } else {
      $RunningSubTotal += ($order_products['products_quantity'] * $order_products['final_price']);
    }
  
    $RunningTax += (($order_products['products_tax'] / 100) * ($order_products['products_quantity'] * $order_products['final_price']));     
  }
  
  
  $new_subtotal = $RunningSubTotal;
  $new_tax = $RunningTax;
  
  //subtotal
  $_SESSION['create_preorder']['orders_total']['ot_subtotal']['value'] = tep_insert_currency_value($new_subtotal);
  $_SESSION['create_preorder']['orders_total']['ot_subtotal']['text']  = tep_insert_currency_text($currencies->format($new_subtotal, true, $order['currency']));
  
  
  //tax
  $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_PREORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
  $plustax = tep_db_fetch_array($plustax_query);
  if($plustax['cnt'] > 0) {
    $_SESSION['create_preorder']['orders_total']['ot_tax']['value'] = tep_insert_currency_value($new_tax);
    $_SESSION['create_preorder']['orders_total']['ot_tax']['text']  = tep_insert_currency_text($currencies->format($new_tax, true, $order['currency']));
  }

  //point修正中
  $total_point = $_SESSION['create_preorder']['orders_total']['ot_point']['value'];

  //total
  $total_value = 0;
  $sign_value_array = $_POST['sign_value'];
  foreach ($_SESSION['create_preorder']['orders_total'] as $code => $ott) {
    if($ott['class'] == 'ot_custom'){

      if($sign_value_array[$code] == '0'){

        $ott['value'] = 0-$ott['value'];
      } 
    }
    if ($code !== 'ot_total' && $code !== 'ot_point') {
      $total_value += $ott['value'];
    }
  }

  $total_value_more_zero = true;
  if($total_value < 0){
    $total_value_more_zero = false;
    $total_value = abs($total_value);
  }


  if ($plustax['cnt'] == 0) {
    $newtotal = $total_value + $new_tax;
  } else {
    if(DISPLAY_PRICE_WITH_TAX == 'true') {
      $newtotal = $total_value - $new_tax;
    } else {
      $newtotal = $total_value;
    }
  }

  if (($newtotal - $total_point) >= 1) {
    if ($newtotal > 0) {
      $newtotal -= $total_point;
    }
  } else {
    $newtotal = '0';
  }
  
  $payment_code = payment::changeRomaji($payment_method, PAYMENT_RETURN_TYPE_CODE); 
  $handle_fee = 0; 
  
  $newtotal = $newtotal+$handle_fee;
  if(!$total_value_more_zero){
    $newtotal = $newtotal*-1;
  }

  $update_totals_temp = 0;
  $update_totals_temp_num = 0;
  $temp_i = 0;
  foreach($update_totals as $key=>$value){

    if(trim($value['title']) == '' && $value['class'] == 'ot_custom'){

      if($sign_value_array[$temp_i] == '0'){

        $update_totals_temp_num += $value['value'];
      }else{
        $update_totals_temp += $value['value'];
      }
      $temp_i++;
    } 
  }
  
  
  $newtotal = isset($_SESSION['preorder_products'][$_GET['oID']]['ot_total']) ? $_SESSION['preorder_products'][$_GET['oID']]['ot_total'] : $newtotal;

  $newtotal -= $update_totals_temp;
  $newtotal += $update_totals_temp_num;
  $_SESSION['create_preorder']['orders_total']['ot_total']['value'] = intval(floor($newtotal));
  $_SESSION['create_preorder']['orders_total']['ot_total']['text']  = "<b>" . $currencies->ot_total_format(intval(floor($newtotal)), true, $order['currency']) . "</b>";
  
  $_SESSION['create_preorder']['orders']['code_fee'] = $handle_fee;
    
  // 最终处理（更新并返信）
  if ($products_delete == false) {
    
    $notify_comments = '';
    $notify_comments_mail = $comments;
    $customer_notified = '0';
    
    if ($comments) {
      $notify_comments_mail .= "\n\n";
    }
    if (isset($_POST['notify_comments']) && ($_POST['notify_comments'] == 'on')) {
      $notify_comments = $comments;
    }

    if (isset($_POST['x']) && $_POST['x'] && isset($_POST['y']) && $_POST['y']) {
      if (
           !$_SESSION['create_preorder']['orders'] 
        || !$_SESSION['create_preorder']['orders']['orders_id'] 
        || !$_SESSION['create_preorder']['orders']['customers_id']
      ) {
        $messageStack->add_session(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
        tep_redirect(tep_href_link('create_preorder.php'));
      }
      // orders 
      $new_orders2_id = '';
      $_SESSION['create_preorder']['orders']['orders_id']= date("Ymd") . '-' .  date("His") . tep_get_preorder_end_num();
      $new_orders2_id = $_SESSION['create_preorder']['orders']['orders_id'];
      $sql_data_temp_array = $_SESSION['create_preorder']['orders'];
      $cpayment->admin_add_additional_info($sql_data_temp_array, $_POST['payment_method']);
      $_SESSION['create_preorder']['orders'] = $sql_data_temp_array;
      tep_db_perform(TABLE_PREORDERS, $_SESSION['create_preorder']['orders']);
      preorder_last_customer_action();
      preorders_updated($_SESSION['create_preorder']['orders']['orders_id']);

      $exists_customer_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$_SESSION['create_preorder']['orders']['customers_id']."'"); 
      $exists_customer = tep_db_fetch_array($exists_customer_raw); 
      if ($exists_customer) {
        if ($exists_customer['is_quited'] == '1') {
          tep_db_query("update ".TABLE_PREORDERS." set is_gray = '2' where orders_id = '".$new_orders2_id."'"); 
        }
        if ($exists_customer['customers_guest_chk'] == '1') {
          tep_db_query("update ".TABLE_PREORDERS." set is_guest = '1' where orders_id = '".$new_orders2_id."'"); 
        }
      } else {
        tep_db_query("update ".TABLE_PREORDERS." set is_guest = '1' where orders_id = '".$new_orders2_id."'"); 
      }
      foreach($_SESSION['create_preorder']['orders_products'] as $pid => $orders_product) {
        $orders_product['site_id'] = $_SESSION['create_preorder']['orders']['site_id'];
        if(isset($new_orders2_id)&&$new_orders2_id){
          $orders_product['orders_id'] = $new_orders2_id;
        }
        tep_db_perform(TABLE_PREORDERS_PRODUCTS, $orders_product);
        $orders_products_id = tep_db_insert_id();
        foreach($_SESSION['create_preorder']['orders_products_attributes'][$pid] as $aid => $orders_product_attributes) {
        if(isset($new_orders2_id)&&$new_orders2_id){
          $orders_product_attributes['orders_id'] = $new_orders2_id;
        }
          $orders_product_attributes['orders_products_id'] = $orders_products_id;
          $orders_product_attributes['option_info'] = tep_db_input(serialize($orders_product_attributes['option_info']));
          tep_db_perform(TABLE_PREORDERS_PRODUCTS_ATTRIBUTES, $orders_product_attributes);
        }
      }
      
      
      preorders_updated($_SESSION['create_preorder']['orders']['orders_id']); 
      foreach($_SESSION['create_preorder']['orders_total'] as $c => $ot){
        if($ot['class'] == 'ot_custom'){

          if($sign_value_array[$c] == '0'){

            $ot['value'] = 0-$ot['value'];
          } 
        }
        $ot['text'] = '';
        if(isset($new_orders2_id)&&$new_orders2_id){
        $ot['orders_id'] = $new_orders2_id;
        }
        tep_db_perform(TABLE_PREORDERS_TOTAL, $ot);
      }
      
      // orders_status_history
      $comment_str = is_array($comment_arr) ? $comment_arr['comment'] : $comments_text;

      if (DEFAULT_PREORDERS_STATUS_ID == $_POST['status']) {
        $sql_data_array = array(
                    'orders_id' => $_SESSION['create_preorder']['orders']['orders_id'], 
                    'orders_status_id' => $_POST['status'], 
                    'date_added' => 'now()', 
                    'customer_notified' => '1',
                    'comments' => $comment_str,
                    'user_added' => $update_user_info['name']);
        tep_db_perform(TABLE_PREORDERS_STATUS_HISTORY, $sql_data_array);
      } else {
        $sql_data_array = array(
                    'orders_id' => $_SESSION['create_preorder']['orders']['orders_id'], 
                    'orders_status_id' => DEFAULT_PREORDERS_STATUS_ID, 
                    'date_added' => 'now()', 
                    'customer_notified' => '1',
                    'comments' => $comment_str,
                    'user_added' => $update_user_info['name']);
        tep_db_perform(TABLE_PREORDERS_STATUS_HISTORY, $sql_data_array);
        
        $sql_data_array = array(
                    'orders_id' => $_SESSION['create_preorder']['orders']['orders_id'], 
                    'orders_status_id' => $_POST['status'], 
                    'date_added' => 'now()', 
                    'customer_notified' => '1',
                    'comments' => '',
                    'user_added' => $update_user_info['name']);
        tep_db_perform(TABLE_PREORDERS_STATUS_HISTORY, $sql_data_array);
      
      } 

      $sql_customers_array = array( 'customers_fax' =>
          $_SESSION['create_preorder']['customer_fax']);
      tep_db_perform(TABLE_CUSTOMERS,$sql_customers_array,'update','customers_id='.$_SESSION['create_preorder']['orders']['customers_id']);
      
      

    if (isset($_POST['notify']) && ($_POST['notify'] == 'on')) {
      $order = new preorder($_SESSION['create_preorder']['orders']['orders_id']);
      
      $email = $_POST['comments'];
      $select_status_raw = tep_db_query("select * from ".TABLE_PREORDERS_STATUS." where orders_status_id = '".$order->info['orders_status']."'");
      $select_status_res = tep_db_fetch_array($select_status_raw); 
      $preorder_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$order->info['orders_id']."' and class='ot_total'"); 
      $preorder_total_res = tep_db_fetch_array($preorder_total_raw);
      if ($preorder_total_res) {
        $pre_otm = number_format($preorder_total_res['value'], 0, '.', '').SENDMAIL_EDIT_ORDERS_PRICE_UNIT; 
      }
      $num_product = 0;
      $num_product_raw = tep_db_query("select products_name, products_id, products_quantity,products_rate from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$order->info['orders_id']."'");
      $num_product_res = tep_db_fetch_array($num_product_raw);
      if ($num_product_res) {
        $num_product = $num_product_res['products_quantity']; 
        if(isset($num_product_res['products_rate']) &&$num_product_res['products_rate']!=0 &&$num_product_res['products_rate']!=1){ $num_product_end = ' ('.number_format($num_product_res['products_rate']*$num_product).')'; 
        }else{
          $num_product_end = '';
        }
      }
      
      $email = str_replace(array(
              '${USER_NAME}',
              '${USER_MAIL}',
              '${PREORDER_DATE}',
              '${PREORDER_NUMBER}',
              '${PAYMENT}',
              '${ORDER_TOTAL}',
              '${ORDER_STATUS}',
              '${SITE_NAME}',
              '${SITE_URL}',
              '${SUPPORT_MAIL}',
              '${PAY_DATE}',
              '${RESERVE_DATE}',
              '${PRODUCTS_QUANTITY}',
              '${PRODUCTS_NAME}',
              '${ORDER_COMMENT}' 
            ),array(
              $order->customer['name'],
              $order->customer['email_address'],
              tep_date_long($order->info['date_purchased']),
              $order->info['orders_id'],
              $order->info['payment_method'],
              $pre_otm,
              $select_status_res['orders_status_name'],
              get_configuration_by_site_id('STORE_NAME', $order->info['site_id']),
              get_url_by_site_id($order->info['site_id']),
              get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $order->info['site_id']),
              date('Y'.SENDMAIL_TEXT_DATE_YEAR.'n'.SENDMAIL_TEXT_DATE_MONTH.'j'.SENDMAIL_TEXT_DATE_DAY,strtotime(tep_get_pay_day())),
              $_POST['update_ensure_deadline'],
              $num_product.SENDMAIL_EDIT_ORDERS_NUM_UNIT.$num_product_end,
              $num_product_res['products_name'],
              preorders_a($order->info['orders_id'])
            ),$email);

      if ($customer_guest['is_send_mail'] != '1') {
        $site_url_raw = tep_db_query("select * from sites where id = '".$order->info['site_id']."'"); 
        $site_url_res = tep_db_fetch_array($site_url_raw); 
        $change_preorder_url_param = md5(time().$order->info['orders_id']); 
        $change_preorder_url = $site_url_res['url'].'/change_preorder.php?pid='.$change_preorder_url_param; 
        $email = str_replace('${REAL_ORDER_URL}', $change_preorder_url, $email); 
        tep_db_query("update ".TABLE_PREORDERS." set check_preorder_str = '".$change_preorder_url_param."' where orders_id = '".$order->info['orders_id']."'"); 
        $email_title = $_POST['title']; 
        $email_title = str_replace(array(
              '${USER_NAME}',
              '${USER_MAIL}',
              '${PREORDER_DATE}',
              '${PREORDER_NUMBER}',
              '${PAYMENT}',
              '${ORDER_TOTAL}',
              '${ORDER_STATUS}',
              '${SITE_NAME}',
              '${SITE_URL}',
              '${SUPPORT_MAIL}',
              '${PAY_DATE}',
              '${RESERVE_DATE}',
              '${PRODUCTS_QUANTITY}',
              '${PRODUCTS_NAME}' 
            ),array(
              $order->customer['name'],
              $order->customer['email_address'],
              tep_date_long($order->info['date_purchased']),
              $order->info['orders_id'],
              $order->info['payment_method'],
              $pre_otm,
              $select_status_res['orders_status_name'],
              get_configuration_by_site_id('STORE_NAME', $order->info['site_id']),
              get_url_by_site_id($order->info['site_id']),
              get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $order->info['site_id']),
              date('Y'.SENDMAIL_TEXT_DATE_YEAR.'n'.SENDMAIL_TEXT_DATE_MONTH.'j'.SENDMAIL_TEXT_DATE_DAY,strtotime(tep_get_pay_day())),
              $_POST['update_ensure_deadline'],
              $num_product.SENDMAIL_EDIT_ORDERS_NUM_UNIT.$num_product_end,
              $num_product_res['products_name'] 
            ),$email_title);

        //自定义费用列表 
        $totals_email_str = '';
        foreach($update_totals as $value){

          if($value['title'] != '' && $value['value'] != '' && $value['class']== 'ot_custom'){


            $totals_email_str .= $value['title'].str_repeat('　', intval((16 -strlen($value['title']))/2)).'：'.$currencies->format($value['value'])."\n";
          }
        }
        $email = str_replace('${CUSTOMIZED_FEE}',$totals_email_str,$email); 

        $s_status_raw = tep_db_query("select nomail from ".TABLE_PREORDERS_STATUS." where orders_status_id = '".$_POST['status']."'");  
        $s_status_res = tep_db_fetch_array($s_status_raw);
        $email = str_replace(TEXT_MONEY_SYMBOL,SENDMAIL_TEXT_MONEY_SYMBOL,$email);
        $search_products_name_query = tep_db_query("select products_name from ". TABLE_PRODUCTS_DESCRIPTION ." where products_id='".$num_product_res['products_id']."' and language_id='".$languages_id."' and (site_id='".$order->info['site_id']."' or site_id='0') order by site_id DESC");
        $search_products_name_array = tep_db_fetch_array($search_products_name_query);
        tep_db_free_result($search_products_name_query);
        $email = tep_replace_mail_templates($email,$order->customer['email_address'],$order->customer['name'],$order->info['site_id']);
        $email = html_entity_decode(htmlspecialchars($email));
        if ($s_status_res['nomail'] != 1) {
          tep_mail($order->customer['name'], $order->customer['email_address'], $email_title, str_replace($num_product_res['products_name'],$search_products_name_array['products_name'],$email), get_configuration_by_site_id('STORE_OWNER',$order->info['site_id']), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS',$order->info['site_id']),$order->info['site_id']);
          
          tep_mail(get_configuration_by_site_id('STORE_OWNER', $order->info['site_id']), get_configuration_by_site_id('SENTMAIL_ADDRESS',$order->info['site_id']), $email_title, $email, $order->customer['name'], $order->customer['email_address'], $order->info['site_id']);
        }
        
        //预约完成邮件认证
        $preorders_mail_array = tep_get_mail_templates('PREORDER_MAIL_CONTENT',$order->info['site_id']);
        $preorder_email_subject = str_replace('${SITE_NAME}', get_configuration_by_site_id('STORE_NAME', $order->info['site_id']), $preorders_mail_array['title']); 
        $preorder_email_text = $preorders_mail_array['contents']; 
        $replace_info_arr = array('${PRODUCTS_NAME}', '${PRODUCTS_QUANTITY}', '${PAYMENT}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}', '${PREORDER_NUMBER}', '${ORDER_COMMENT}', '${PRODUCTS_ATTRIBUTES}');
        
        $max_op_len = 0;
        $max_op_array = array();
        $mail_option_str = '';
        if (!empty($order->products[0]['attributes'])) {
          foreach($order->products[0]['attributes'] as $o_key => $o_value) {
            $max_op_array[] = mb_strlen($o_value['option_info']['title'], 'utf-8'); 
          }
        }
        if (!empty($max_op_array)) {
          $max_op_len = max($max_op_array); 
        }
        if (!empty($order->products[0]['attributes'])) {
          foreach($order->products[0]['attributes'] as $o_at_key => $o_at_value) {
            $mail_option_str .= $o_at_value['option_info']['title'].str_repeat('　', intval($max_op_len - mb_strlen($o_at_value['option_info']['title'], 'utf-8'))).':'.str_replace(array("<br>", "<BR>", "\r", "\n", "\r\n"), "", $o_at_value['option_info']['value'])."\n"; 
          }
        }
        $pre_replace_info_arr = array($num_product_res['products_name'], $num_product, $order->info['payment_method'], $order->customer['name'], get_configuration_by_site_id('STORE_NAME', $order->info['site_id']), $site_url_res['url'], $order->info['orders_id'], '', $mail_option_str);
        
        $preorder_email_text = str_replace($replace_info_arr, $pre_replace_info_arr, $preorder_email_text);
        
        $preorder_email_text = str_replace(TEXT_MONEY_SYMBOL,SENDMAIL_TEXT_MONEY_SYMBOL,$preorder_email_text);
        $email = tep_replace_mail_templates($email,$order->customer['email_address'],$order->customer['name'],$order->info['site_id']);
        $email = html_entity_decode(htmlspecialchars($email));
        tep_mail($order->customer['name'], $order->customer['email_address'], $preorder_email_subject, str_replace($num_product_res['products_name'],$search_products_name_array['products_name'],$email), get_configuration_by_site_id('STORE_OWNER', $order->info['site_id']), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $order->info['site_id']), $order->info['site_id']);

        $preorder_email_text = tep_replace_mail_templates($preorder_email_text,$order->customer['email_address'],$order->customer['name'],$order->info['site_id']); 
        tep_mail('', get_configuration_by_site_id('SENTMAIL_ADDRESS', $order->info['site_id']), $preorder_email_subject, $preorder_email_text, $order->customer['name'], $order->customer['email_address'], $order->info['site_id']); 
      }
      $customer_notified = '1';
    }

      //end print
      tep_pre_order_status_change($_SESSION['create_preorder']['orders']['orders_id'],$_SESSION['create_preorder']['orders']['orders_status']);      
      unset($_SESSION['create_preorder']);
      unset($_SESSION['preorder_products'][$_GET['oID']]);

      tep_redirect(tep_href_link(FILENAME_PREORDERS, 'oID='.$sql_data_array['orders_id']));
    }
    $order_updated_2 = true;
  }
    if ($order_updated && !$products_delete && $order_updated_2) {
      $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
    } elseif ($order_updated && $products_delete) {
      $messageStack->add_session(NOTICE_NEW_PREORDERS_PRODUCTS_DEL, 'success');
    } else {
      $messageStack->add_session(ERROR_NEW_PREORDERS_UPDATE, 'error');
    }
    if($products_delete == false){
      unset($_SESSION['create_preorder']);
      unset($_SESSION['orders_update_products'][$_GET['oID']]);
    }
    unset($_SESSION['preorder_products'][$_GET['oID']]);

    tep_redirect(tep_href_link("edit_new_preorders.php", tep_get_all_get_params(array('action')) . 'action=edit'));
    
  break;
  
  }
}

  if (($action == 'edit') && isset($_GET['oID'])) {
    $order_exists = true;
    if (!isset($_SESSION['create_preorder']['orders']['orders_id'])
    || $_SESSION['create_preorder']['orders']['orders_id'] != $_GET['oID']
    ) {
      $order_exists = false;
      $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
      tep_redirect(tep_href_link('create_preorder.php'));
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/styles.css">
<link rel="stylesheet" type="text/css" href="css/popup_window.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/jquery.form.js"></script>
<script language="javascript" src="includes/3.4.1/build/yui/yui.js"></script>
<script language="javascript" src="includes/jquery.form.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="javascript" src="js/popup_window.js"></script>
<script>
var text_unset_data = '<?php echo TEXT_UNSET_DATA;?>';
var image_icon_info = '<?php echo IMAGE_ICON_INFO;?>';
var text_popup_window_show = '<?php echo TEXT_POPUP_WINDOW_SHOW;?>';
var text_popup_window_edit = '<?php echo TEXT_POPUP_WINDOW_EDIT;?>';
var image_save = '<?php echo IMAGE_SAVE;?>';
$(document).ready(function() {
  <?php //监听按键?> 
  $(document).keydown(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if (typeof($('#alert_div_submit').val()) != 'undefined'){
          clear_confirm_div();
      }
    }
    if (event.which == 13) {
      <?php //回车?> 
      if (typeof($('#alert_div_submit').val()) != 'undefined'){
        $('#alert_div_submit').trigger('click');
      }
    }
  });
});
function avg_div_checkbox(){
  document.getElementById('alert_div_id').checked=!document.getElementById('alert_div_id').checked
}
function confirm_div(str){
  var ClassName = "thumbviewbox";
  var allheight = document.body.scrollHeight;
  //ground div 
  var element_ground = document.createElement('div');
  element_ground.setAttribute('class',ClassName);
  element_ground.setAttribute('id','element_ground_close');
  element_ground.style.cssText = 'position: absolute; top: 0px; left: 0; z-index: 150;background-color: rgb(0, 0, 0); opacity: 0.01; width:100%; height: '+allheight+'px;';
  element_ground.style.filter="alpha(opacity=1)";
  // text str 
  var element_boder = document.createElement('div');
  element_boder.setAttribute('class',ClassName);
  element_boder.setAttribute('id','element_boder_close');
  element_boder.style.cssText = 'margin: 0 auto; line-height: 1.4em;width:500px;;background-color: rgb(255,255,255)';
  ok_input_html =  '<input type="button" id="alert_div_submit" onclick=\'save_div_action()\' value="'+'<?php echo DIV_TEXT_OK;?>'+'">';
  clear_input_html = '<input type="button" onclick="clear_confirm_div()" value="'+'<?php echo DIV_TEXT_CLEAR;?>'+'">';
  alert_div_html = '<div style="padding:10px;text-align:left">'+str+'</div>';
  alert_div_html = alert_div_html+'<div style="text-align:center">'+ok_input_html+'&nbsp;&nbsp;'+clear_input_html+'</div>'
  element_boder.innerHTML = '<div style="padding:10px;text-align:left">'+alert_div_html+'</div>';

  //center div 
  var element = document.createElement('div');
  element.appendChild(element_boder);
  element.setAttribute('class',ClassName);
  element.setAttribute('id','element_close');
  element.style.cssText = 'width:100%;position:fixed;z-index:151;text-align:center;line-height:0;top:25%';


// add div 
  document.body.appendChild(element_ground);
  document.body.appendChild(element);
  var Apdiv=document.getElementById("alert_div_id");
  Apdiv.focus();
}
function save_div_action(){
  if(document.getElementById("alert_div_id").checked){
    clear_confirm_div();
    edit_preorder_weight();
  }else{
    clear_confirm_div();
  }
}

function clear_confirm_div(){
  var em_close=document.getElementById("element_ground_close");
  em_close.parentNode.removeChild(em_close);
  var em_close=document.getElementById("element_close");
  em_close.parentNode.removeChild(em_close);
}
function confirm_div_init(hidden_list_str,price_list_str,num_list_str){
  $.ajax({
    url: 'ajax_preorders.php?action=check_preorder_products_avg',
    type: 'POST',
    dataType: 'text',
    data: 'language_id=<?php echo $languages_id;?>'+'&site_id=<?php echo $order->Info['site_id'];?>'+'&products_list_str='+hidden_list_str+'&price_list_str='+price_list_str+'&num_list_str='+num_list_str+'&check_type=1&preorder_type=new',
    async: false,
    success: function (msg_info) {
      if (msg_info != '') {
        confirm_div(msg_info);
      } else {
        edit_preorder_weight();
      } 
    }
  }); 
}
function edit_preorder_weight(){
  createPreorderChk('<?php echo $ocertify->npermission;?>');
  document.edit_order.submit();
}
var session_orders_id = '<?php echo $_GET['oID'];?>';
<?php //把值放入session里?>
function orders_session(type,value){
  
  $.ajax({
    type: "POST",
    data: 'orders_session_type='+type+'&orders_session_value='+value+'&orders_id='+session_orders_id,
    async:false,
    url: 'ajax_preorders.php?action=orders_session',
    success: function(msg) {
      
    }
  });
}
  //todo:修改通性用
<?php
  $payment_array = payment::getPaymentList(); 
  $op_info_str = '';
  $op_info_array = array();
  foreach($_SESSION['create_preorder']['orders_products'] as $orders_key=>$orders_value){
   $products_id_str = $orders_key; 
  }
  foreach($_SESSION['create_preorder']['orders_products_attributes'] as $attr_key=>$attr_value){ 
    foreach($attr_value as $attr_str_key=>$attr_str_value){
      $op_info_array[] = $attr_str_key; 
    }   
  } 
  $op_info_str = implode('|||',$op_info_array);
?>
  <?php //隐藏支付方法的附加信息?> 
  function hidden_payment(h_type){
  if(document.edit_order.elements["payment_method"]){
  var idx = document.edit_order.elements["payment_method"].selectedIndex;
  var CI = document.edit_order.elements["payment_method"].options[idx].value;
  $(".rowHide").hide();
  $(".rowHide").find("input").attr("disabled","true");
  $(".rowHide_"+CI).show();
  $(".rowHide_"+CI).find("input").removeAttr("disabled"); 
  price_total();
  recalc_preorder_price("<?php echo $_GET['oID'];?>", "<?php echo $products_id_str;?>", "0", "<?php echo $op_info_str;?>");
  if (h_type == 1) {
    $.ajax({
      dataType: 'text',
      url: 'ajax_orders.php?action=get_select_payment_status',
      type: 'POST',
      data: 'select_payment='+CI+'&s_site_id=<?php echo $order['site_id']?>&h_type=1',
      async: false, 
      success: function(msg) {
        $('#status').val(msg); 
        check_prestatus();
      }
    });
  }
  }
  }
$(document).ready(function(){
  hidden_payment(0);
  $("select[name='status']").change(function(){
    var s_status = document.getElementsByName("status")[0].value;
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
  $("select[name='payment_method']").change(function(){
    hidden_payment(1);
  });
  $("textarea[name='comments_text']").blur(function(){
    var comments_text = document.getElementsByName("comments_text")[0].value;
    orders_session('comments_text',comments_text);
  });
  $("input[name='bank_kouza_num']").blur(function(){
    var ele = document.getElementsByName("bank_kouza_num")[0];
    ele_value = ele.value;
    ele_value = ele_value.replace(/\s/g,'');
    ele_value = ele_value.replace(/　/g,'');
    ele_value = ele_value.replace(/－/g,'-');
    ele_value = ele_value.replace(/ー/g,'-');
    ele_value = ele_value.replace(/１/g,'1');
    ele_value = ele_value.replace(/２/g,'2');
    ele_value = ele_value.replace(/３/g,'3');
    ele_value = ele_value.replace(/４/g,'4');
    ele_value = ele_value.replace(/５/g,'5');
    ele_value = ele_value.replace(/６/g,'6');
    ele_value = ele_value.replace(/７/g,'7');
    ele_value = ele_value.replace(/８/g,'8');
    ele_value = ele_value.replace(/９/g,'9');
    ele_value = ele_value.replace(/０/g,'0');
    ele.value = ele_value;
  });
});

<?php //加减符号?>
function sign(num){

  var sign = '<select id="sign_'+num+'" name="sign_value[]" onchange="price_total(\'<?php echo TEXT_MONEY_SYMBOL;?>\');recalc_preorder_price(\'<?php echo $_GET['oID'];?>\', \'<?php echo $products_id_str;?>\', \'0\', \'<?php echo $op_info_str;?>\');orders_session(\'sign_'+num+'\',this.value);">';
  sign += '<option value="1">+</option>';
  sign += '<option value="0">-</option>';
  sign += '</select>';
  return sign;
}
<?php //添加输入框?>
function add_option(){
    var add_num = $("#button_add_id").val();
    add_num = parseInt(add_num);
    orders_session('customers_add_num',add_num);
    $("#button_add_id").val(add_num+1);
    add_num++;
    var add_str = '';

    add_str += '<tr><td class="smallText" align="left">&nbsp;</td>'
            +'<td class="smallText" align="right"><input style="text-align:right;" value="" size="7" name="update_totals['+add_num+'][title]" onblur="orders_session(\'customers_total_'+add_num+'\',this.value);">:'
            +'</td><td class="smallText" align="right">'+sign(add_num)+'<input style="text-align:right;" id="update_totals_'+add_num+'" value="" size="6" onkeyup="clearNewLibNum(this);price_total(\'<?php echo TEXT_MONEY_SYMBOL;?>\');recalc_preorder_price(\'<?php echo $_GET['oID'];?>\', \'<?php echo $products_id_str;?>\', \'0\', \'<?php echo $op_info_str;?>\');" name="update_totals['+add_num+'][value]"><?php echo TEXT_MONEY_SYMBOL;?><input type="hidden" name="update_totals['+add_num+'][class]" value="ot_custom"><input type="hidden" name="update_totals['+add_num+'][total_id]" value="0"></td>'
            +'<td><b><img height="17" width="1" border="0" alt="" src="images/pixel_trans.gif"></b></td></tr>';

    $("#handle_fee_id").parent().parent().before(add_str);
}
<?php //检查表单?>
function submit_order_check(products_id,op_id){
  var qty = document.getElementById('p_'+op_id).value;
  var payment_str = '';
  if (document.getElementsByName('payment_method')[0]) {
    payment_str = document.getElementsByName('payment_method')[0].value; 
  }
  var is_cu_single = 1;
  var start_num = $('#button_add_id').val(); 
  var is_cu_str = ''; 
  for (var s_num = start_num; s_num > 0; s_num--) {
    if (document.getElementsByName('update_totals['+s_num+'][class]')[0]) {
      if (document.getElementsByName('update_totals['+s_num+'][class]')[0].value == 'ot_custom') {
        is_cu_str += document.getElementsByName('update_totals['+s_num+'][title]')[0].value + document.getElementsByName('update_totals['+s_num+'][value]')[0].value; 
      }
    }
  }
  is_cu_str = is_cu_str.replace(/^\s+|\s+$/g,"");  
  if (is_cu_str == '') {
    is_cu_single = 0;
  }
  $.ajax({
    dataType: 'text',
    type: 'POST',
    data:'c_comments='+$('#c_comments').val()+'&c_title='+$('#mail_title').val()+'&c_status_id='+$('#status').val()+'&c_payment='+payment_str+'&is_customized_fee='+is_cu_single,
    async:false,
    url:'ajax_preorders.php?action=check_new_preorder_variable_data',
    success: function(msg_info) {
      if (msg_info != '') {
        alert(msg_info); 
      } else {
        $.ajax({
          dataType: 'text',
          url: 'ajax_orders_weight.php?action=edit_preorder',
          data: 'qty='+qty+'&products_id='+products_id, 
          type:'POST',
          async: false,
          success: function(data) {
            var reg_info = new RegExp("update_products\\[[0-9]+\\]\\[p_price\\]"); 
            var find_input_name = '';
            var price_list_str = '';
            var hidden_list_str = '';
            var num_list_str = '';
            $('#preorder_list').find('input').each(function() {
              if ($(this).attr('type') == 'text') {
                find_input_name = $(this).attr('name');
                if (reg_info.test(find_input_name)) {
                  price_list_str += $(this).val()+'|||';
                  hidden_list_str += $(this).next().val()+'|||';
                  num_list_str += $(this).parent().prev().prev().prev().prev().find('input[type=text]').val()+'|||';
                }
              }
            });
            if(data != ''){
              if(confirm(data)){
                if (price_list_str != '') {
                  price_list_str = price_list_str.substr(0, price_list_str.length-3); 
                  hidden_list_str = hidden_list_str.substr(0, hidden_list_str.length-3); 
                  num_list_str = num_list_str.substr(0, num_list_str.length-3); 
                  $.ajax({
                    url: 'ajax_preorders.php?action=check_preorder_products_profit',
                    type: 'POST',
                    dateType: 'text',
                    data: 'products_list_str='+hidden_list_str+'&price_list_str='+price_list_str+'&num_list_str='+num_list_str+'&check_type=1&preorder_type=new',
                    async: false,
                    success: function (new_msg_info) {
                      if (new_msg_info != '') {
                        if (confirm(new_msg_info)) {
                          confirm_div_init(hidden_list_str,price_list_str,num_list_str);
                        } 
                      } else {
                        confirm_div_init(hidden_list_str,price_list_str,num_list_str);
                      }
                    }
                  });  
                } else {
                  edit_preorder_weight();
                }
              }
            }else{
              if (price_list_str != '') {
                price_list_str = price_list_str.substr(0, price_list_str.length-3); 
                hidden_list_str = hidden_list_str.substr(0, hidden_list_str.length-3); 
                num_list_str = num_list_str.substr(0, num_list_str.length-3); 
                $.ajax({
                  url: 'ajax_preorders.php?action=check_preorder_products_profit',
                  type: 'POST',
                  dateType: 'text',
                  data: 'products_list_str='+hidden_list_str+'&price_list_str='+price_list_str+'&num_list_str='+num_list_str+'&check_type=1&preorder_type=new',
                  async: false,
                  success: function (new_msg_info) {
                    if (new_msg_info != '') {
                      if (confirm(new_msg_info)) {
                        confirm_div_init(hidden_list_str,price_list_str,num_list_str);
                      } 
                    } else {
                      confirm_div_init(hidden_list_str,price_list_str,num_list_str);
                    }
                  }
                });
              } else {
                edit_preorder_weight();
              }
            }
          }
        });
      }
    }
  });
}
<?php //格式化输出价格?>
function fmoney(s)
{
   s = parseFloat((s + "").replace(/[^\d\.-]/g, "")).toFixed(0) + "";
    var l = s.split(".")[0].split("").reverse();
     var t = '';
      for(i = 0; i < l.length; i ++ ){
            t += l[i] + ((i + 1) % 3 == 0 && (i + 1) != l.length ? "," : "");
              }
       return t.split("").reverse().join("");
}
<?php //计算价格?>
function recalc_preorder_price(oid, opd, o_str, op_str)
{
  
  var op_array = op_str.split("|||"); 
  var op_price = 0;
  var op_price_total = 0;
  var oid_price = 0;
  var op_price_str = ''; 
  if(document.getElementsByName('update_products['+opd+'][p_price]')[0]){
    oid_price = document.getElementsByName('update_products['+opd+'][p_price]')[0].value;
  }
  if(op_str != ''){
    for(x in op_array){

      op_price = document.getElementsByName('update_products['+opd+'][attributes]['+op_array[x]+'][price]')[0].value;  
      op_price = parseInt(op_price);
      op_price_total += op_price;
      op_price_str += op_price+'|||';
    }
  }
  var pro_num = <?php echo $_SESSION['create_preorder']['orders_products'][$products_id_str]['products_quantity'];?>;
  if(document.getElementById('p_'+opd)){
    pro_num = document.getElementById('p_'+opd).value;
  }
  p_price = oid_price; 
  var p_final_price = 0;
  if(document.getElementsByName('update_products['+opd+'][final_price]')[0]){
    p_final_price = document.getElementsByName('update_products['+opd+'][final_price]')[0].value;  
  }
  p_op_info = op_price_total;  

  var update_total_temp;
  var update_total_num = 0;
  var add_num = $("#button_add_id").val();
  var total_str = '';
  var total_price_str = '';
  for(var i = 1;i <= add_num;i++){
     
        if(document.getElementById('update_totals_'+i)){
          update_total_temp = document.getElementById('update_totals_'+i).value; 
          update_total_temp_value = update_total_temp;
          if(update_total_temp == '' || update_total_temp == '-'){update_total_temp = 0;}
          if($("#sign_"+i).val() == '0'){

            update_total_temp = 0-update_total_temp;  
          }
          update_total_temp = parseInt(update_total_temp);
          update_total_num += update_total_temp;
          total_str += i+'|||';
          total_price_str += update_total_temp_value+'|||';
        }
  }
  if(document.getElementsByName('payment_method')[0]){
  var payment_method = document.getElementsByName('payment_method')[0].value;
  $.ajax({
    type: "POST",
    data:'oid='+oid+'&opd='+opd+'&o_str='+o_str+'&op_price='+p_op_info+'&p_num='+pro_num+'&p_price='+p_price+'&p_final_price='+p_final_price+'&update_total_num='+update_total_num+'&op_str='+op_str+'&op_price_str='+op_price_str+'&total_str='+total_str+'&total_price_str='+total_price_str+'&payment_method='+payment_method,
    async:false,
    url: 'ajax_preorders.php?action=recalc_price',
    success: function(msg) {
      msg_info = msg.split('|||');
      if(o_str != 1){
        document.getElementsByName('update_products['+opd+'][final_price]')[0].value = msg_info[0];
        document.getElementById('update_products['+opd+'][final_price]').innerHTML = msg_info[7];
      }
      if(o_str != 1){
        document.getElementById('update_products['+opd+'][a_price]').innerHTML = msg_info[1];
      }else{
        document.getElementById('update_products['+opd+'][a_price]').innerHTML = msg_info[4]; 
      }
      if(o_str != 1){
        document.getElementById('update_products['+opd+'][b_price]').innerHTML = msg_info[2];
      }else{
        document.getElementById('update_products['+opd+'][b_price]').innerHTML = msg_info[5]; 
      }
      if(o_str != 1){
        document.getElementById('update_products['+opd+'][c_price]').innerHTML = msg_info[3];
      }else{
        document.getElementById('update_products['+opd+'][c_price]').innerHTML = msg_info[6]; 
      }
      document.getElementById('ot_subtotal_id').innerHTML = document.getElementById('update_products['+opd+'][c_price]').innerHTML;
      document.getElementById('handle_fee_id').innerHTML = msg_info[8]+'<?php echo TEXT_MONEY_SYMBOL;?>';
      var opd_str_value = document.getElementById('ot_subtotal_id').innerHTML;
      var opd_str_temp = opd_str_value;
      opd_str_value = opd_str_value.replace(/<.*?>/g,'');
      opd_str_value = opd_str_value.replace(/,/g,'');
      opd_str_value = opd_str_value.replace('<?php echo TEXT_MONEY_SYMBOL;?>','');
      opd_str_value = parseFloat(opd_str_value);
      var ot_total = 0;
      var handle_fee_id = document.getElementById('handle_fee_id').innerHTML; 
      handle_fee_id = handle_fee_id.replace(/<.*?>/g,'');
      handle_fee_id = handle_fee_id.replace(/,/g,'');
      handle_fee_id = handle_fee_id.replace('<?php echo TEXT_MONEY_SYMBOL;?>','');
      handle_fee_id = parseInt(handle_fee_id);  
 
      if(opd_str_temp.indexOf('color') > 0){
         
         ot_total = handle_fee_id+update_total_num-opd_str_value;
      }else{
         
         ot_total = opd_str_value+handle_fee_id+update_total_num;
      } 
       
      if(ot_total < 0){
        ot_total = Math.abs(ot_total);
        document.getElementById('ot_total_id').innerHTML = '<font color="#FF0000">'+fmoney(ot_total)+'</font><?php echo TEXT_MONEY_SYMBOL;?>';
      }else{
        document.getElementById('ot_total_id').innerHTML = fmoney(ot_total)+'<?php echo TEXT_MONEY_SYMBOL;?>'; 
      } 
    }
  });
}
}
<?php //显示订单价格信息?>
function price_total()
{
      var ot_total = '';
      var ot_total_flag = false;
      var ot_subtotal_id = document.getElementById('ot_subtotal_id').innerHTML; 
      if(ot_subtotal_id.indexOf('color') > 0){
        ot_total_flag = true; 
      }
      ot_subtotal_id = ot_subtotal_id.replace(/<.*?>/g,'');
      ot_subtotal_id = ot_subtotal_id.replace(/,/g,'');
      ot_subtotal_id = ot_subtotal_id.replace('<?php echo TEXT_MONEY_SYMBOL;?>','');
      ot_subtotal_id= parseInt(ot_subtotal_id);
      var handle_fee_id = document.getElementById('handle_fee_id').innerHTML; 
      handle_fee_id = handle_fee_id.replace(/<.*?>/g,'');
      handle_fee_id = handle_fee_id.replace(/,/g,'');
      handle_fee_id = handle_fee_id.replace('<?php echo TEXT_MONEY_SYMBOL;?>','');
      handle_fee_id = parseInt(handle_fee_id);  
      var update_total_temp;
      var update_total_num = 0;
      var add_num = $("#button_add_id").val();
      for(var i = 1;i <= add_num;i++){
     
        if(document.getElementById('update_totals_'+i)){
          update_total_temp = document.getElementById('update_totals_'+i).value; 
          if(update_total_temp == '' || update_total_temp == '-'){update_total_temp = 0;}
          update_total_temp = parseInt(update_total_temp);
          if($("#sign_"+i).val() == '0'){

            update_total_temp = 0-update_total_temp;  
          }
          update_total_num += update_total_temp;
        }
      }
 
      if(ot_total_flag == false){
        ot_total = ot_subtotal_id+handle_fee_id+update_total_num;
      }else{
        ot_total = handle_fee_id+update_total_num-ot_subtotal_id; 
      }
      if(ot_total < 0){
        ot_total = Math.abs(ot_total);
        document.getElementById('ot_total_id').innerHTML = '<font color="#FF0000">'+fmoney(ot_total)+'</font><?php echo TEXT_MONEY_SYMBOL;?>';
      }else{
        document.getElementById('ot_total_id').innerHTML = fmoney(ot_total)+'<?php echo TEXT_MONEY_SYMBOL;?>'; 
      } 
}
<?php //检查添加商品的价格?>
function check_add(){
  price = document.getElementById('add_product_price').value;
  if(price != '' && price != 0  && price > 0){
    return true;
  } else {
    alert("<?php echo ERROR_INPUT_PRICE_NOTICE;?>");
    return false;
  }
}
<?php //切换预约订单的状态?>
function check_prestatus() {
  var s_value = document.getElementById('status').value;

  $.ajax({
    dataType: 'text',
    url: 'ajax_preorders.php?action=get_mail',
    data: 'sid='+s_value, 
    type:'POST',
    async: false,
    success: function(msg) {
      document.edit_order.comments.value = msg;
    }
  });
  
  $.ajax({
    dataType: 'text',
    url: 'ajax_preorders.php?action=get_mail',
    data: 'sid='+s_value+'&type=1', 
    type:'POST',
    async: false,
    success: function(t_msg) {
      document.edit_order.title.value = t_msg;
    }
  });
}  
<?php //弹出日历?>
function open_calendar()
{
  var is_open = $('#toggle_open').val(); 
  if (is_open == 0) {
    $('#toggle_open').val('1'); 
    var rules = {
           "all": {
                  "all": {
                           "all": {
                                      "all": "current_s_day",
                                }
                     }
            }};


    if ($("#predate").val() != '') {
      date_info = $("#predate").val().split('-'); 
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
        tmp_show_date = dtdate.format(newDate); 
        tmp_show_date_array = tmp_show_date.split('-');
        $("#predate_year").val(tmp_show_date_array[0]); 
        $("#predate_month").val(tmp_show_date_array[1]); 
        $("#predate_day").val(tmp_show_date_array[2]); 
        $("#predate").val(tmp_show_date); 
        $('#toggle_open').val('0');
        $('#toggle_open').next().html('<div id="mycalendar"></div>');
      });
    });
  }
}
<?php //检查日期是否正确?>
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
<?php //检查切换日期?>
function change_predate_date() {
  predate_str = $("#predate_year").val()+"-"+$("#predate_month").val()+"-"+$("#predate_day").val(); 
  if (!is_date(predate_str)) {
    alert('<?php echo ERROR_INPUT_RIGHT_DATE;?>'); 
  } else {
    $("#predate").val(predate_str); 
  }
}
</script>
<style type="text/css">
.yui3-skin-sam .redtext {
    color:#0066CC;
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
.popup-calendar {
top:20px;
}
.number{
font-size:24px;
font-weight:bold;
width:20px;
text-align:center;
}
form{
margin:0;
padding:0;
}
.alarm_input{
width:80px;
}
.log{
  border:#999 solid 1px;
  background:#eee;
  clear: both;
}
.log .content{
  padding:3px;
  font-size:12px;
}
.log .alarm{
  display:none;
  font-size:10px;
  background:url(images/icons/alarm.gif) no-repeat left center;
}
.log .level{
  font-size:10px;
  font-weight:bold;
  display:none;
  width:100px;
  *width:120px;
}
.log .level input{
margin:0;
padding:0;
}
.log .info{
  font-size:10px;
  background:#fff;
  text-align:right;
}
.info02{
width:50px;
}
.log .action{
text-align:center;
  font-size:10px;
}
.edit_action{
  display:none;
  font-size:10px;
line-height:24px;
padding-right:5px;
}
.action a{
padding:0 3px;
}
textarea,input{
  font-size:12px;
}
.alarm_on{
  border:2px solid #ff8e90;
  background:#ffe6e6;
}
.clr{
clear:both;
width:100%;
height:5px;
overflow:hidden;
}
.popup-calendar-wrapper{
float:left;
}
</style>
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
.Subtitle {
  font-family: Verdana, Arial, Helvetica, sans-serif;
  font-size: 11px;
  font-weight: bold;
  color: #FF6600;
}
</style>
<!-- header_eof -->
<!-- body -->
<?php 
if($_GET['action'] != 'add_product'){
  echo tep_draw_form('edit_order', "edit_new_preorders.php", tep_get_all_get_params(array('action','paycc')) . 'action=update_order', 'post', 'id="edit_order_id_one"'); 
}
?>
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
      <div class="compatible">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
if (($action == 'edit') && ($order_exists == true)) {
    $order = $_SESSION['create_preorder']['orders'];
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
                <td class="main" width="70%"><font color='#FF0000'><?php echo tep_get_site_name_by_id($order['site_id'])?></font></td>
              </tr>
              <tr>
                <td class="main" valign="top" width="30%"><?php echo EDIT_ORDERS_ID_TEXT;?></td>
                <td class="main" width="70%"><?php echo $order['orders_id'];?></td>
              </tr>
              <tr>
                <td class="main" valign="top"><?php echo EDIT_ORDERS_DATE_TEXT;?></td>
                <td class="main"><?php echo tep_date_long(date('Y-m-d H:i:s'));?></td>
              </tr>
              <tr>
                <td class="main" valign="top"><?php echo EDIT_ORDERS_CUSTOMER_NAME;?></td>
                <td class="main"><?php echo tep_html_quotes(htmlspecialchars($order['customers_name'])); ?></td>
              </tr>
              <tr>
                <td class="main" valign="top"><?php echo EDIT_ORDERS_EMAIL;?></td>
                <td class="main"><font color="red"><?php echo $order['customers_email_address']; ?></font></td>
              </tr>
              <!-- End Addresses Block -->
              <!-- Begin Payment Block -->
              <tr>
                <td class="main" valign="top"><?php echo EDIT_ORDERS_PAYMENT_METHOD;?></td>
                <td class="main">
      <?php 
      //payment method
      $pay_info_array = array();
      $pay_orders_id_array = array();
      $pay_type_array = array();
      $payment_negative_array = array();
      $payment_positive_array = array();
      $customers_info_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$_SESSION['create_preorder']['orders']['customers_id']."'"); 
      $customers_info = tep_db_fetch_array($customers_info_raw); 
      foreach($payment_array[0] as $pay_key=>$pay_value){ 
        $payment_info = $cpayment->admin_get_payment_info_comment($pay_value,$_SESSION['create_preorder']['orders']['customers_email_address'],$_SESSION['create_preorder']['orders']['site_id'],0,(($customers_info['is_quited'] == '1')?2:0));
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
      $products_money_total = $_SESSION['create_preorder']['orders_total']['ot_total']['value'];
      if($products_money_total != 0){
          $orders_payment_query = tep_db_query("select payment_method,orders_id from ". TABLE_PREORDERS ." where customers_email_address='".  $_SESSION['create_preorder']['orders']['customers_email_address'] ."' and site_id='". $_SESSION['create_preorder']['orders']['site_id'] ."' and is_gray = '1' and is_guest = '0' order by orders_id desc"); 
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
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_PREORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id_array[0]."' order by date_added desc"); 
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
                  $pay_info_array[0] = $orders_status_history_array['comments']; 
                  break;
                }
              }
            }
            tep_db_free_result($orders_status_history_query);
          }
          if($pay_orders_id_array[1] != ''){ 
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_PREORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id_array[1]."' order by date_added desc"); 
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
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_PREORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id_array[2]."' order by date_added desc"); 
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
          //end
          $pay_method = isset($_SESSION['create_preorder']['orders']['payment_method']) ? $_SESSION['create_preorder']['orders']['payment_method'] : $pay_method;
          $pay_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : $pay_method;
          $payment_code = payment::changeRomaji($pay_method, PAYMENT_RETURN_TYPE_CODE); 
          $_SESSION['create_preorder']['orders']['payment_method'] = $payment_code;
          $c_chk = tep_get_payment_customer_chk('',$_SESSION['create_preorder']['orders']['customers_id']);
          $payment_select_str = payment::makePaymentListPullDownMenu($payment_code,$_SESSION['create_preorder']['orders']['site_id'],$c_chk,'preorder'); 
          $paymentlist = true;
          if($payment_select_str!=''){
            echo $payment_select_str;
            if(isset($_SESSION['pre_payment_empty_error'])
                &&$_SESSION['pre_payment_empty_error']!=''){
              echo $_SESSION['pre_payment_empty_error'];
              unset($_SESSION['pre_payment_empty_error']);
            }
          }else{
            if(isset($_SESSION['pre_payment_empty_error'])
                &&$_SESSION['pre_payment_empty_error']!=''){
              echo $_SESSION['pre_payment_empty_error'];
              unset($_SESSION['pre_payment_empty_error']);
            }else{
              echo TEXT_NO_PAYMENT_ENABLED;
            }
            $paymentlist = false;
          }
          if($paymentlist){
          echo "\n".'<script language="javascript">'."\n"; 
          echo '$(document).ready(function(){'."\n";

          $cpayment->admin_show_payment_list($payment_code,$pay_info_array,$_SESSION['create_preorder']['orders']['site_id'],$c_chk,'preorder',$order['customers_email_address'],(($c_chk == '2')?false:true));
          
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
                if(!$cpayment->admin_get_payment_buying_type($payment_code,$field['title']) && $pay_k != 2){
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
        ?>
                </td>
              </tr>
              <!-- End Payment Block -->
            </table>
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
    $order_products = array();
    $order_products_attributes = array();
    if ($_SESSION['create_preorder']['orders_products']) 
      foreach ($_SESSION['create_preorder']['orders_products'] as $pid => $orders_products) {
      $order_products[$pid] = array('qty' => $orders_products['products_quantity'],
                                     'name' => str_replace("'", "&#39;", $orders_products['products_name']),
                                     'model' => $orders_products['products_model'],
                                     'tax' => $orders_products['products_tax'],
                                     'price' => $orders_products['products_price'],
                                     'final_price' => $orders_products['final_price'],
                                     'orders_products_id' => $orders_products['orders_products_id']);


    if ($_SESSION['create_preorder']['orders_products_attributes'][$pid]) {
      foreach ($_SESSION['create_preorder']['orders_products_attributes'][$pid] as $attributes) {
        $order_products_attributes[$pid][] = array('price' => $attributes['options_values_price'],
                                                         'option_info' => $attributes['option_info'],
                                                         'option_item_id' => $attributes['option_item_id'],
                                                         'option_group_id' => $attributes['option_group_id']);
      }
    }
  }
    ?>
<?php // Version without editable names & prices ?>
<table border="0" width="100%" cellspacing="0" cellpadding="2" id="preorder_list">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_NUM_PRO_NAME;?></td>
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENICY; ?></td>
    <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_BEFORE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_AFTER; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_BEFORE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_AFTER; ?></td>
    <input type="hidden" name="update_viladate" value="true">
  </tr>
  
<?php 
  $op_info_str = '';
  $op_info_array = array();
  foreach($order_products_attributes[$pid] as $attributes_key=>$attributes_value){
    $op_info_array[] = $attributes_key;   
  } 
  $op_info_str = implode('|||',$op_info_array);
  foreach ($order_products as $pid => $orders_products) { 
    $products_id = '';
    $orders_price = $orders_products['price'];
    $op_price = 0;
    $option_item_order_sql = "select it.id,it.type item_type,it.option item_option from ".TABLE_PRODUCTS."
      p,".TABLE_OPTION_ITEM." it 
      where p.products_id = '".(int)$pid."' 
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
    foreach($create_preorder['orders_products_attributes'][$pid] as $orders_att_key=>$orders_att_value){

      $op_price += $orders_att_value['options_values_price'];
    }
    $RowStyle = "dataTableContent";
    $less_op_single = tep_pre_check_less_option_product($pid, $order_products_attributes[$pid]); 
    $order_products[$pid]['qty'] = isset($_SESSION['preorder_products'][$_GET['oID']]['qty']) ? $_SESSION['preorder_products'][$_GET['oID']]['qty'] : $order_products[$pid]['qty']; 
    $orders_products_num = isset($_POST['update_products'][$pid]['qty']) ? $_POST['update_products'][$pid]['qty'] : $order_products[$pid]['qty'];
    echo '    <tr class="dataTableRow">' . "\n" .
         '      <td class="' . $RowStyle . '" align="left" valign="top" width="6%">';
    if ($less_op_single) {
      echo "<input type='text' name='update_products[$pid][qty]' id='p_".$pid."' size='2' class='update_products_qty' style='background: none repeat scroll 0 0 #CCCCCC' readonly value='" . $orders_products_num . "'>";
    } else {
      echo "<input type='text' name='update_products[$pid][qty]' id='p_".$pid."' size='2' value='" . $orders_products_num . "' onkeyup='clearLibNum(this);recalc_preorder_price(\"".$oID."\", \"".$pid."\", \"0\", \"".$op_info_str."\");' class='update_products_qty'>";
    }
    echo "&nbsp;x</td>\n" . 
         '      <td class="' . $RowStyle . '" width="35%">' . $order_products[$pid]['name'] . "<input name='update_products[$pid][name]' size='64' type='hidden' value='" . $order_products[$pid]['name'] . "'>\n" . 
       '      &nbsp;&nbsp;';
    if ($less_op_single) {
      echo '<br><font color="#ff0000" size="1">'.NOTICE_LESS_PRODUCT_PRE_OPTION_TEXT.'</font>'; 
    }
    // Has Attributes? 
    if (sizeof($order_products_attributes[$pid]) > 0) {
      echo '<div id="popup_window" class="popup_window"></div>';
      for ($j=0; $j<sizeof($order_products_attributes[$pid]); $j++) {
        $t_item_id = $order_products_attributes[$pid][$j]['option_item_id']; 
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
        $default_value = strtr($order_products_attributes[$pid][$j]['option_info']['value'], array("'"=>"&#39;",'"'=>"&#34;")) == '' ? TEXT_UNSET_DATA : strtr($order_products_attributes[$pid][$j]['option_info']['value'], array("'"=>"&#39;",'"'=>"&#34;"));
        echo '<br><div class="order_option_width">&nbsp;<i><div class="order_option_info"><div class="order_option_title"> - ' .tep_parse_input_field_data(stripslashes($order_products_attributes[$pid][$j]['option_info']['title']), array("'"=>"&#39;",'"'=>"&#34;")).'<input type="hidden" class="option_input_width" name="update_products[' .  $pid . '][attributes]['.$j.'][option]" value=\'' .  tep_parse_input_field_data(stripslashes($order_products_attributes[$pid][$j]['option_info']['title']), array("'"=>"&#39;",'"'=>"&#34;")) . '\'>: ' . 
           '</div><div class="order_option_value">';
        if ($less_op_single) {
          echo htmlspecialchars($default_value); 
        } else {
          echo '<a onclick="popup_window(this,\''.$item_type.'\',\''.tep_parse_input_field_data(stripslashes($order_products_attributes[$pid][$j]['option_info']['title']), array("'"=>"&#39;",'"'=>"&#34;")).'\',\''.$item_list.'\');" href="javascript:void(0);"><u>' .  htmlspecialchars(html_entity_decode($default_value)).'</u></a>';
        }
        echo '<input type="hidden" class="option_input_width" name="update_products[' . $pid .  '][attributes]['.$j.'][value]" value=\'' .  strtr($order_products_attributes[$pid][$j]['option_info']['value'], array("'"=>"&#39;",'"'=>"&#34;")).'\'></div></div>';
        echo '<div class="order_option_price">';
        $order_products_attributes[$pid][$j]['price'] = isset($_SESSION['preorder_products'][$_GET['oID']]['attr'][$j]) ? $_SESSION['preorder_products'][$_GET['oID']]['attr'][$j] : $order_products_attributes[$pid][$j]['price'];
        if ($less_op_single) {
          echo "<input size='9' type='text' style='background: none repeat scroll 0 0 #CCCCCC' readonly name='update_products[$pid][attributes][$j][price]' value='".(int)(isset($_POST['update_products'][$pid]['attributes'][$j]['price'])?$_POST['update_products'][$pid]['attributes'][$j]['price']:$order_products_attributes[$pid][$j]['price'])."'>";
        } else {
          echo "<input size='9' type='text' name='update_products[$pid][attributes][$j][price]' value='".(int)(isset($_POST['update_products'][$pid]['attributes'][$j]['price'])?$_POST['update_products'][$pid]['attributes'][$j]['price']:$order_products_attributes[$pid][$j]['price'])."' onkeyup=\"clearNewLibNum(this);recalc_preorder_price('".$oID."', '".$pid."', '0', '".$op_info_str."');\">";
        }
        echo TEXT_MONEY_SYMBOL;
        echo '</div>'; 
        echo '</i></div>';
      }
    }
    
    echo '      </td>' . "\n" .
         '      <td class="' . $RowStyle . '">' . $order_products[$pid]['model'] . "<input name='update_products[$pid][model]' size='12' type='hidden' value='" . $order_products[$pid]['model'] . "'>" . '</td>' . "\n" .
         '      <td class="' . $RowStyle . '" align="right">' . tep_display_tax_value($order_products[$pid]['tax']) . "<input name='update_products[$pid][tax]' size='2' type='hidden' value='" . tep_display_tax_value($order_products[$pid]['tax']) . "'>" . '%</td>' . "\n";
         $order_products[$pid]['price'] = isset($_SESSION['preorder_products'][$_GET['oID']]['price']) ? $_SESSION['preorder_products'][$_GET['oID']]['price'] : $order_products[$pid]['price'];
         $orders_products_type = '';
         if(tep_get_bflag_by_product_id((int)$pid)){

           $orders_products_type = '−';
         }
         if ($less_op_single) {
           echo '<td class="'.$RowStyle.'" align="right">'.$orders_products_type.'<input type="text" style="text-align:right;background: none repeat scroll 0 0 #CCCCCC" readonly class="once_pwd" name="update_products['.$pid.'][p_price]" size="9" value="'.tep_display_currency(number_format(abs(isset($_POST['update_products'][$pid]['p_price'])?$_POST['update_products'][$pid]['p_price']:$order_products[$pid]['price']), 2)).'">'.TEXT_MONEY_SYMBOL;
         } else {
           echo '<td class="'.$RowStyle.'" align="right">'.$orders_products_type.'<input type="text" style="text-align:right;" class="once_pwd" name="update_products['.$pid.'][p_price]" size="9" value="'.tep_display_currency(number_format(abs(isset($_POST['update_products'][$pid]['p_price'])?$_POST['update_products'][$pid]['p_price']:$order_products[$pid]['price']), 2)).'" onkeyup="clearNoNum(this);recalc_preorder_price(\''.$oID.'\', \''.$pid.'\', \'0\',\''.$op_info_str.'\');">'.TEXT_MONEY_SYMBOL;
         }
         echo '<input type="hidden" name="hidden_pro_id[]" value="'.$pid.'">';         
         echo '</td>'; 
         $order_products[$pid]['final_price'] = isset($_SESSION['preorder_products'][$_GET['oID']]['final_price']) ? $_SESSION['preorder_products'][$_GET['oID']]['final_price'] : $order_products[$pid]['final_price'];
         echo '      <td class="' . $RowStyle . '" align="right">' . "<input type='hidden' name='update_products[$pid][final_price]' onkeyup='clearLibNum(this);recalc_preorder_price(\"".$oID."\", \"".$pid."\", \"1\", \"".$orders_price."\", \"".$op_price."\");' size='9' style='text-align:right;' value='" . tep_display_currency(number_format(abs($order_products[$pid]['final_price']),2)) 
         . "'  onkeyup='clearNoNum(this)' class='once_pwd' >" . 
         '<input type="hidden" name="op_id_'.$pid.'" 
         value="'.tep_get_pre_product_by_op_id($pid,'pid').'"><div id="update_products['.$pid.'][final_price]">';  
     if ($order_products[$pid]['final_price'] < 0) {
      echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order_products[$pid]['final_price'], true, $order['currency'], $order['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    } else {
      echo $currencies->format($order_products[$pid]['final_price'], true, $order['currency'], $order['currency_value']);
    }
         echo "</div>\n" .'</td>' . "\n" .
         '      <td class="' . $RowStyle . '" align="right"><div id="update_products['.$pid.'][a_price]">';
    if ($order_products[$pid]['final_price'] < 0) {
      echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order_products[$pid]['final_price'], $order_products[$pid]['tax']), true, $order['currency'], $order['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    } else {
      echo $currencies->format(tep_add_tax($order_products[$pid]['final_price'], $order_products[$pid]['tax']), true, $order['currency'], $order['currency_value']);
    }
    echo '</div></td>' . "\n" . 
      '      <td class="' . $RowStyle . '" align="right"><div id="update_products['.$pid.'][b_price]">';
    $order_products[$pid]['qty'] = isset($_POST['update_products'][$pid]['qty']) ? $_POST['update_products'][$pid]['qty'] : $order_products[$pid]['qty'];
    if ($order_products[$pid]['final_price'] < 0) {
      echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order_products[$pid]['final_price'] * $order_products[$pid]['qty'], true, $order['currency'], $order['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    } else {
      echo $currencies->format($order_products[$pid]['final_price'] * $order_products[$pid]['qty'], true, $order['currency'], $order['currency_value']);
    }
    echo '</div></td>' . "\n" . 
         '      <td class="' . $RowStyle . '" align="right"><div id="update_products['.$pid.'][c_price]">';
    if ($order_products[$pid]['final_price'] < 0) {
      echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order_products[$pid]['final_price'], $order_products[$pid]['tax']) * $order_products[$pid]['qty'], true, $order['currency'], $order['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    } else {
      echo $currencies->format(tep_add_tax($order_products[$pid]['final_price'], $order_products[$pid]['tax']) * $order_products[$pid]['qty'], true, $order['currency'], $order['currency_value']);
    }
    echo '</div></td>' . "\n" . 
         '    </tr>' . "\n";
  }
    
  ?>
</table>

        </td>
      <tr>
        <td>
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td valign="top"></td>
              <?php 
              if (!(count($order_products) > 0)) {
              ?> 
              <td align="right"><?php echo '<a href="create_preorder.php?oID=' .  $order['orders_id'] . '&Customer_mail='.$order['customers_email_address'].'&site_id='.$order['site_id'].'">' .  tep_html_element_button(ADDING_TITLE) . '</a>'; ?></td>
              <?php }?> 
            </tr>
          </table>
        </td>
      </tr>     
  <!-- End Products Listings Block -->
  <!-- Begin Order Total Block -->
      <tr>
        <td class="SubTitle"><?php echo EDIT_ORDERS_FEE_TITLE_TEXT; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
      </tr>   
      <tr>
        <td>

<table width="100%" border="0" cellspacing="0" cellpadding="2" class="dataTableRow" id="add_option">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" align="left" width="60%"><?php echo TABLE_HEADING_FEE_MUST;?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_MODULE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_AMOUNT; ?></td>
    <td class="dataTableHeadingContent"width="1"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
  </tr>
<?php
  // Override order.php Class's Field Limitations
  if ($_SESSION['create_preorder']['orders_total']) 
  foreach ($_SESSION['create_preorder']['orders_total'] as $totals) { 
    $order_totals[] = array('title' => $totals['title'], 'text' => $totals['text'], 'class' => $totals['class'], 'value' => $totals['value'], 'orders_total_id' => $totals['orders_total_id']); 
  }


// START OF MAKING ALL INPUT FIELDS THE SAME LENGTH 
  $max_length = 0;
  $TotalsLengthArray = array();
  foreach ($order_totals as $ot) {
    $TotalsLengthArray[] = array("Name" => $ot['title']);
  }
  
  reset($TotalsLengthArray);
  foreach($TotalsLengthArray as $TotalIndex => $TotalDetails) {
    if (strlen($TotalDetails["Name"]) > $max_length) {
      $max_length = strlen($TotalDetails["Name"]);
    }
  }
// END OF MAKING ALL INPUT FIELDS THE SAME LENGTH



  $TotalsArray = array();
  $ot_custom_flag = false;
  $orders_totals_num = 0;
  foreach($order_totals as $key => $value){

    if($value['class'] == 'ot_custom'){

      $ot_custom_flag = true;
      $orders_totals_num++;
    }
  }
  foreach ($order_totals as $k => $ot) {
    $TotalsArray[] = array("Name" => $ot['title'], "Price" => tep_display_currency(number_format((float)$ot['value'], 2, '.', '')), "Class" => $ot['class'], "TotalID" => $ot['orders_total_id']);
    if($ot_custom_flag == false){
      $TotalsArray[] = array("Name" => "          ", "Price" => "", "Class" => "ot_custom", "TotalID" => "0");
    }else{
      if($ot['class'] == 'ot_point' && $orders_totals_num < 2){

        $TotalsArray[] = array("Name" => "          ", "Price" => "", "Class" => "ot_custom", "TotalID" => "0");
      } 
    }
  }

  if($ot_custom_flag == false){ 
    array_pop($TotalsArray);
  }
  if(isset($_SESSION['orders_update_products'][$_GET['oID']]['customers_add_num'])){
    $customers_add_num = $_SESSION['orders_update_products'][$_GET['oID']]['customers_add_num'];
    $totals_total = array_pop($TotalsArray);
    for($total_i = 5;$total_i <= $customers_add_num+1;$total_i++){
      $TotalsArray[$total_i] = array("Name" => "          ", "Price" => "", "Class" => "ot_custom", "TotalID" => "0"); 
    } 
    $TotalsArray[] = $totals_total;
  }
  foreach ($TotalsArray as $TotalIndex => $TotalDetails) {

    if($TotalIndex == 3 && $TotalDetails['Class'] == 'ot_custom' && $TotalDetails['Price'] == ''){

      unset($TotalsArray[$TotalIndex]);
    }
  }
  foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
    $TotalStyle = "smallText";
    if ($TotalDetails["Class"] == "ot_total") {
      echo '  <tr>' . "\n" .
           '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
           '    <td align="right" class="' . $TotalStyle . '">'.TEXT_CODE_HANDLE_FEE.'</td>' .
           '    <td align="right" class="' . $TotalStyle . '"><div id="handle_fee_id">' . $currencies->format($order["code_fee"]) . '</div><input type="hidden" name="payment_code_fee" value="'.$order["code_fee"].'">' . 
                '</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
           '  </tr>' . "\n";
      echo '  <tr id="add_option_total">' . "\n" .
           '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_OTTOTAL_READ.'</td>' . 
           '    <td align="right" class="' . $TotalStyle . '">' . $TotalDetails["Name"] . '</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><div id="ot_total_id">';
                $TotalDetails["Price"] = isset($_SESSION['preorder_products'][$_GET['oID']]['ot_total']) ? $_SESSION['preorder_products'][$_GET['oID']]['ot_total'] : $TotalDetails["Price"];
                if($TotalDetails["Price"]>=0){
                  echo  $currencies->ot_total_format($TotalDetails["Price"], true,
                      $order['currency'], $order['currency_value']);
                }else{
                  echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->ot_total_format($TotalDetails["Price"], true, $order['currency'], $order['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
                }
                  echo '</div>' . 
                "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
           '  </tr>' . "\n";
    } elseif ($TotalDetails["Class"] == "ot_subtotal") {
      echo '  <tr>' . "\n" .
           '    <td align="left" class="' . $TotalStyle .  '">&nbsp;</td>' . 
           '    <td align="right" class="' . $TotalStyle . '">' . $TotalDetails["Name"] . '</td>' .
           '    <td align="right" class="' . $TotalStyle . '"><div id="ot_subtotal_id">';
           $TotalDetails["Price"] = isset($_SESSION['preorder_products'][$_GET['oID']]['ot_subtotal']) ? $_SESSION['preorder_products'][$_GET['oID']]['ot_subtotal'] : $TotalDetails["Price"];
           if($TotalDetails["Price"]>=0){
                  echo  $currencies->ot_total_format($TotalDetails["Price"], true,
                      $order['currency'], $order['currency_value']);
           }else{
             echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($TotalDetails["Price"], true, $order['currency'], $order['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
           }
            echo '</div>' . 
                "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
           '  </tr>' . "\n";
           
    } elseif ($TotalDetails["Class"] == "ot_tax") {
      echo '  <tr>' . "\n" . 
           '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
           '    <td align="right" class="' . $TotalStyle . '">' . trim($TotalDetails["Name"]) . "<input name='update_totals[$TotalIndex][title]' type='hidden' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . '</td>' . "\n" .
           '    <td align="right" class="' . $TotalStyle . '">' . $currencies->format($TotalDetails["Price"], true, $order['currency'], $order['currency_value']) . '</b>' . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' .  tep_draw_separator('pixel_trans.gif', '1', '17') . '</b></td>' . 
           '  </tr>' . "\n";
    } elseif ($TotalDetails["Class"] == "ot_point") {
      if ($customer_guest['customers_guest_chk'] == 0) { //会员
        $current_point = $customer_point['point'] + $TotalDetails["Price"];
        echo '  <tr>' . "\n" .
             '    <td colspan="4">' . "<input type='hidden' name='update_totals[$TotalIndex][value]' size='6' value='" . $TotalDetails["Price"] . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
                "<input type='hidden' name='before_point' value='" . $TotalDetails["Price"] . "'>" . 
             '   </td>' . "\n" .
             '  </tr>' . "\n";
      } else { //游客
        echo '  <tr>' . "\n" .
             '    <td colspan="4">'. 
                "<input type='hidden' name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][value]' size='6' value='" . $TotalDetails["Price"] . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
             '   </td>' . "\n" .
             '  </tr>' . "\n";
      }
    } else {
      $sign_str = '<select id="sign_'.$TotalIndex.'" name="sign_value[]" onchange="price_total(\''.TEXT_MONEY_SYMBOL.'\');recalc_preorder_price(\"".$oID."\", \"".$pid."\", \"0\", \"".$op_info_str."\");orders_session(\'sign_'.$TotalIndex.'\',this.value);">';
      $sign_str .= '<option value="1"'.(isset($_SESSION['orders_update_products'][$_GET['oID']]['sign_'.$TotalIndex]) && $_SESSION['orders_update_products'][$_GET['oID']]['sign_'.$TotalIndex] == '1' ? ' selected="selected"': '').'>+</option>';
      $sign_str .= '<option value="0"'.(isset($_SESSION['orders_update_products'][$_GET['oID']]['sign_'.$TotalIndex]) && $_SESSION['orders_update_products'][$_GET['oID']]['sign_'.$TotalIndex] == '0' ? ' selected="selected"': $TotalDetails["Price"] < 0 ? ' selected="selected"': '').'>-</option>';
      $sign_str .= '</select>';
      $button_add = $TotalIndex == 1 ? '<INPUT type="button" id="button_add" value="'.TEXT_BUTTON_ADD.'" onClick="add_option();"><input type="hidden" id="button_add_id" value="'.(count($TotalsArray)).'">&nbsp;' : '';
      $TotalDetails["Name"] = isset($_POST['update_totals'][$TotalIndex]['title']) ? $_POST['update_totals'][$TotalIndex]['title'] : $TotalDetails["Name"];
      $TotalDetails["Price"] = isset($_SESSION['preorder_products'][$_GET['oID']]['customer'][$TotalIndex]) ? $_SESSION['preorder_products'][$_GET['oID']]['customer'][$TotalIndex] : $TotalDetails["Price"];
      $TotalDetails["Price"] = isset($_POST['update_totals'][$TotalIndex]['value']) ? $_POST['update_totals'][$TotalIndex]['value'] : $TotalDetails["Price"];
      $TotalDetails["Name"] = isset($_SESSION['orders_update_products'][$_GET['oID']]['customers_total_'.$TotalIndex]) ? $_SESSION['orders_update_products'][$_GET['oID']]['customers_total_'.$TotalIndex] : $TotalDetails["Name"];
      echo '  <tr>' . "\n" .
           '    <td align="left" class="' . $TotalStyle .  '">'.($TotalIndex == 1 ? EDIT_ORDERS_TOTALDETAIL_READ_ONE : '').'</td>' . 
           '    <td style="min-width:180px;" align="right" class="' . $TotalStyle .  '">' . $button_add ."<input style='text-align:right;' name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "' onblur='orders_session(\"customers_total_".$TotalIndex."\",this.value);'>:" . '</td>' . "\n" .
           '    <td align="right" class="' . $TotalStyle . '" style="min-width: 60px;">' . $sign_str ."<input style='text-align:right;' name='update_totals[$TotalIndex][value]' id='update_totals_$TotalIndex' onkeyup='clearNoNum(this);price_total();recalc_preorder_price(\"".$oID."\", \"".$pid."\", \"0\", \"".$op_info_str."\");' size='6' value='" . $TotalDetails["Price"] . "'>" .TEXT_MONEY_SYMBOL . 
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
        <td class="SubTitle"><?php echo EDIT_ORDERS_ITEM_FOUR_TITLE; ?></td>
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
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td>
  </tr>
  <tr>
    <td valign="top" width="40%">
      <table border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td class="main" width="82" style="min-width:45px;"><?php echo ENTRY_STATUS; ?></td>
          <td class="main">
          <?php
          $is_select_query = tep_db_query(" select orders_status_id, orders_status_name from " . TABLE_PREORDERS_STATUS . " where language_id = '" . (int)$languages_id . "' limit 1");
          $is_select_res = tep_db_fetch_array($is_select_query); 
          $sel_status_id = get_configuration_by_site_id_or_default('MODULE_PAYMENT_'.strtoupper($_SESSION['create_preorder']['orders']['payment_method']).'_PREORDER_STATUS_ID', $order['site_id']);
          $sel_status_id = $sel_status_id != 0 ? $sel_status_id: get_configuration_by_site_id('DEFAULT_PREORDERS_STATUS_ID');
          $customer_notified = isset($_SESSION['orders_update_products'][$_GET['oID']]['notify']) ? $_SESSION['orders_update_products'][$_GET['oID']]['notify'] : $customer_notified;
          $sel_status_id = isset($_SESSION['orders_update_products'][$_GET['oID']]['s_status']) ? $_SESSION['orders_update_products'][$_GET['oID']]['s_status'] : $sel_status_id;
          echo tep_draw_pull_down_menu('status', $orders_statuses, $sel_status_id, 'id="status" onchange="check_prestatus();" style="width:80px;"'); 
          ?>
          </td>
        </tr>
        <tr>
          <td class="main"><?php echo EDIT_ORDERS_SEND_MAIL_TEXT; ?></td>
          <td class="main"><table bgcolor="red" cellspacing="5"><tr><td><?php echo tep_draw_checkbox_field('notify', '', isset($_GET['dtype'])?false:$_POST['notify'] == 1 ? true : isset($_SESSION['orders_update_products'][$_GET['oID']]['notify']) && $_SESSION['orders_update_products'][$_GET['oID']]['notify']== 1 ? true :false); ?></td></tr></table></td>
        </tr>
        <?php if($CommentsWithStatus) { ?>
        <tr>
          <td class="main"><?php echo EDIT_ORDERS_RECORD_TEXT; ?></td>
          <td class="main"><?php echo tep_draw_checkbox_field('notify_comments', '', isset($_SESSION['orders_update_products'][$_GET['oID']]['notify_comments']) && $_SESSION['orders_update_products'][$_GET['oID']]['notify_comments'] == 1 ? true: false); ?>&nbsp;&nbsp;<font color="#FF0000;"><?php echo EDIT_ORDERS_RECORD_READ; ?></font></td>
        </tr>
        <tr>
          <td class="main" valign="top"><?php echo TABLE_HEADING_COMMENTS;?>:</td>
          <td class="main"><?php echo tep_draw_textarea_field('comments_text', 'hard', '74', '5', $_SESSION['orders_update_products'][$_GET['oID']]['comments_text'],'style=" font-family:monospace; font-size:12px; width:100%;"'); ?></td>
        </tr>
        <?php } ?>
      </table>
    </td>
    <td class="main" width="5%">&nbsp;</td>
    <td class="main">
    <?php
    $ma_se = "select * from ".TABLE_MAIL_TEMPLATES." where flag = 'PREORDERS_STATUS_MAIL_TEMPLATES_".$sel_status_id."'"; 
    $mail_sele = tep_db_query($ma_se);
    $mail_sql = tep_db_fetch_array($mail_sele);
    $mail_sql['title'] = isset($_SESSION['orders_update_products'][$_GET['oID']]['title']) ? $_SESSION['orders_update_products'][$_GET['oID']]['title'] : $mail_sql['title'];  
    ?>
    <?php   
    echo TEXT_EMAIL_TITLE.tep_draw_input_field('title', $mail_sql['title'],'style="width:230px;" id="mail_title"'); 
    ?> 
    <br>
    <br>
    <?php
    $order_a_str = '';
    foreach ($order_products as $okey => $ovalue) {
      $order_a_str .= $ovalue['name'].NEW_PREORDERS_NOTE_TEXT."\n"; 
      $order_a_str .= $ovalue['character']."\n"; 
    }
    ?>
    <textarea id="c_comments" style="font-family:monospace;font-size:12px; width:400px;" name="comments" wrap="hard" rows="30" cols="74"><?php echo str_replace('${ORDER_COMMENT}', $order_a_str, isset($_POST['comments']) ? $_POST['comments'] : isset($_SESSION['orders_update_products'][$_GET['oID']]['comments']) ? $_SESSION['orders_update_products'][$_GET['oID']]['comments'] : $mail_sql['contents']);?></textarea>
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
        <td class="SubTitle"><?php echo EDIT_ORDERS_ITEM_FIVE_TITLE; ?></td>
    </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
      </tr>   
      <tr>
      <td>
          <table width="100%" border="0" cellpadding="2" cellspacing="1">
            <tr>
              <td class="main" bgcolor="#FFDDFF"><?php echo EDIT_ORDERS_FINAL_CONFIRM_TEXT; ?>&nbsp;<?php echo HINT_PRESS_UPDATE; ?></td>
              <td class="main" bgcolor="#FFBBFF" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF99FF" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF77FF" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF55FF" width="120" align="center">
              <input type="hidden" name="x" value="43"> 
              <input type="hidden" name="y" value="12"> 
              <INPUT type="button" value="<?php echo TEXT_FOOTER_CHECK_SAVE; ?>" onClick="submit_order_check(<?php echo $pid;?>,<?php echo $pid;?>);">
              </td>
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
<?php
if($action != 'add_product'){
?>
</form>
<?php
}
?>
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
