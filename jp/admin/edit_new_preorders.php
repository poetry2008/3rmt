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

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies(2);
  
  include(DIR_WS_CLASSES . 'preorder.php');
  unset($cpayment); 
  $cpayment = payment::getInstance((int)$_SESSION['create_preorder']['orders']['site_id']);
// START CONFIGURATION ################################


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
      case 'check_session':
        /*
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        # 永远是改动过的
        header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
        # HTTP/1.1
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        # HTTP/1.0
        header("Pragma: no-cache");
        */
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
  $update_user_info = tep_get_user_info($ocertify->auth_user);
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



    if($products_details["qty"] > 0) { // a.) quantity found --> add to list & sum    
      
      $_SESSION['create_preorder']['orders_products'][$orders_products_id]['products_model'] = $products_details["model"];
      $_SESSION['create_preorder']['orders_products'][$orders_products_id]['products_name'] = str_replace("'", "&#39;", $products_details["name"]);
      $_SESSION['create_preorder']['orders_products'][$orders_products_id]['final_price'] = (tep_get_bflag_by_product_id((int)$orders_products_id) ? 0 - $products_details["final_price"] : $products_details["final_price"]);
      $_SESSION['create_preorder']['orders_products'][$orders_products_id]['products_tax'] = $products_details["tax"];
      $_SESSION['create_preorder']['orders_products'][$orders_products_id]['products_quantity'] = $products_details["qty"];
      
  
// Update Tax and Subtotals: please choose sum WITH or WITHOUT tax, but activate only ONE version ;-)
  
  
      $RunningSubTotal += $products_details["qty"] * $products_details["final_price"]; // version WITHOUT tax
      $RunningTax += (($products_details["tax"]/100) * ($products_details["qty"] * $products_details["final_price"]));
  
      // Update Any Attributes
      if (IsSet($products_details["attributes"])) {
        foreach ($products_details["attributes"] as $attributes_id => $attributes_details) {
          $_SESSION['create_preorder']['orders_products_attributes'][$orders_products_id][$attributes_id]['option_info'] = array('title' => $attributes_details['option'], 'value' => $attributes_details['value']);
        }
      }
    } else { // b.) null quantity found --> delete
      unset($_SESSION['create_preorder']['orders_products'][$orders_products_id]);
      unset($_SESSION['create_preorder']['orders_products_attributes'][$orders_products_id]);
      $products_delete = true;
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
        // print "ot_value = $ot_value<br>\n";
      }
  
      // Check for existence of subtotals (CWS)                      
      if ($ot_class == "ot_total") {
        // I can't find out, WHERE the $RunningTotal is calculated - but the subtraction of the tax was wrong (in our shop)
        $ot_value = $RunningTotal;
        
        if ( !$ot_subtotal_found ) { // There was no subtotal on this order, lets add the running subtotal in.
        }
      }
  
      $order = $_SESSION['create_preorder']['orders'];

/*
需要加到订单生成中去
*/

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
      //echo $ot_class;

      if ($ot_class == "ot_shipping" || $ot_class == "ot_lev_discount" || $ot_class == "ot_customer_discount" || $ot_class == "ot_custom" || $ot_class == "ot_cod_fee") {
        // Again, because products are calculated in terms of default currency, we need to align shipping, custom etc. values with default currency
        $RunningTotal += $ot_value / $order['currency_value'];
      } else {
        $RunningTotal += $ot_value;
      }

    } elseif (
      ($ot_class != "ot_shipping") && ($ot_class != "ot_point")) { // Delete Total Piece
      unset($_SESSION['create_preorder']['orders_total'][$ot_class]);
    }
  
  }

  //exit;
  
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
  foreach ($_SESSION['create_preorder']['orders_total'] as $code => $ott) {
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
  
  $payment_code = payment::changeRomaji($order['payment_method'], PAYMENT_RETURN_TYPE_CODE); 
  $handle_fee = $cpayment->handle_calc_fee($payment_code, $newtotal); 
  
  $newtotal = $newtotal+$handle_fee;
  if(!$total_value_more_zero){
    $newtotal = $newtotal*-1;
  }

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
      tep_db_perform(TABLE_PREORDERS, $_SESSION['create_preorder']['orders']);
      preorder_last_customer_action();
      preorders_updated($_SESSION['create_preorder']['orders']['orders_id']);

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
        $ot['text'] = '';
        if(isset($new_orders2_id)&&$new_orders2_id){
        $ot['orders_id'] = $new_orders2_id;
        }
        tep_db_perform(TABLE_PREORDERS_TOTAL, $ot);
      }
      
      // orders_status_history
      $comment_str = $cpayment->admin_deal_comment($_POST['payment_method']);

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
      $num_product_raw = tep_db_query("select products_name, products_quantity from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$order->info['orders_id']."'");
      $num_product_res = tep_db_fetch_array($num_product_raw);
      if ($num_product_res) {
        $num_product = $num_product_res['products_quantity']; 
      }
      
      $email = str_replace(array(
              '${NAME}',
              '${MAIL}',
              '${PREORDER_D}',
              '${PREORDER_N}',
              '${PAY}',
              '${ORDER_M}',
              '${ORDER_S}',
              '${SITE_NAME}',
              '${SITE_URL}',
              '${SUPPORT_EMAIL}',
              '${PAY_DATE}',
              '${ENSURE_TIME}',
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
              $num_product.SENDMAIL_EDIT_ORDERS_NUM_UNIT,
              $num_product_res['products_name'] 
            ),$email);

      if ($customer_guest['is_send_mail'] != '1') {
        $site_url_raw = tep_db_query("select * from sites where id = '".$order->info['site_id']."'"); 
        $site_url_res = tep_db_fetch_array($site_url_raw); 
        if ($_POST['status'] == 32) {
          $change_preorder_url_param = md5(time().$order->info['orders_id']); 
          $change_preorder_url = $site_url_res['url'].'/change_preorder.php?pid='.$change_preorder_url_param; 
          $email = str_replace('${REAL_ORDER_URL}', $change_preorder_url, $email); 
          tep_db_query("update ".TABLE_PREORDERS." set check_preorder_str = '".$change_preorder_url_param."' where orders_id = '".$order->info['orders_id']."'"); 
        }
        if ($_POST['status'] == 33) {
          $change_preorder_url = $site_url_res['url'].'/extend_time.php?pid='.$order->info['orders_id']; 
          $email = str_replace('${ORDER_UP_DATE}', $change_preorder_url, $email); 
        }
        $email_title = $_POST['etitle']; 
        $email_title = str_replace(array(
              '${NAME}',
              '${MAIL}',
              '${PREORDER_D}',
              '${PREORDER_N}',
              '${PAY}',
              '${ORDER_M}',
              '${ORDER_S}',
              '${SITE_NAME}',
              '${SITE_URL}',
              '${SUPPORT_EMAIL}',
              '${PAY_DATE}',
              '${ENSURE_TIME}',
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
              $num_product.SENDMAIL_EDIT_ORDERS_NUM_UNIT,
              $num_product_res['products_name'] 
            ),$email_title);
        
        $s_status_raw = tep_db_query("select nomail from ".TABLE_PREORDERS_STATUS." where orders_status_id = '".$_POST['status']."'");  
        $s_status_res = tep_db_fetch_array($s_status_raw);
        $email = str_replace(TEXT_MONEY_SYMBOL,SENDMAIL_TEXT_MONEY_SYMBOL,$email);
        if ($s_status_res['nomail'] != 1) {
          tep_mail($order->customer['name'], $order->customer['email_address'], $email_title, $email, get_configuration_by_site_id('STORE_OWNER',$order->info['site_id']), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS',$order->info['site_id']),$order->info['site_id']);
          
          tep_mail(get_configuration_by_site_id('STORE_OWNER', $order->info['site_id']), get_configuration_by_site_id('SENTMAIL_ADDRESS',$order->info['site_id']), $email_title, $email, $order->customer['name'], $order->customer['email_address'], $order->info['site_id']);
        }
        
        $preorder_email_subject = str_replace('${SITE_NAME}', get_configuration_by_site_id('STORE_NAME', $order->info['site_id']), get_configuration_by_site_id_or_default('PREORDER_MAIL_SUBJECT', $order->info['site_id'])); 
        $preorder_email_text = get_configuration_by_site_id_or_default('PREORDER_MAIL_CONTENT', $order->info['site_id']); 
        $replace_info_arr = array('${PRODUCTS_NAME}', '${PRODUCTS_QUANTITY}', '${PAY}', '${NAME}', '${SITE_NAME}', '${SITE_URL}', '${PREORDER_N}', '${ORDER_COMMENT}', '${PRODUCTS_ATTRIBUTES}');
        
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
        tep_mail($order->customer['name'], $order->customer['email_address'], $preorder_email_subject, $preorder_email_text, get_configuration_by_site_id('STORE_OWNER', $order->info['site_id']), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $order->info['site_id']), $order->info['site_id']);
        
        tep_mail('', get_configuration_by_site_id('SENTMAIL_ADDRESS', $order->info['site_id']), $preorder_email_subject, $preorder_email_text, $order->customer['name'], $order->customer['email_address'], $order->info['site_id']); 
      }
      $customer_notified = '1';
    }

//end print
      tep_pre_order_status_change($_SESSION['create_preorder']['orders']['orders_id'],$_SESSION['create_preorder']['orders']['orders_status']);      
      unset($_SESSION['create_preorder']);

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
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" src="includes/javascript/jquery.form.js"></script>
<script language="javascript" src="includes/3.4.1/build/yui/yui.js"></script>
<script language="javascript" src="includes/jquery.form.js"></script>
<script language="javascript" src="js2php.php?path=js&name=popup_window&type=js"></script>
<script>
function add_option(){
    var add_num = $("#button_add_id").val();
    add_num = parseInt(add_num);
    $("#button_add_id").val(add_num+1);
    var add_option_total_str = $("#add_option_total").html();
    $("#add_option_total").remove();
    $("#button_add").remove();
    add_num++;
    var add_str = '';

    add_str += '<tr><td class="smallText" align="left"><?php echo EDIT_ORDERS_TOTALDETAIL_READ_ONE;?></td>'
            +'<td class="smallText" align="right"><INPUT type="button" id="button_add" value="<?php echo TEXT_BUTTON_ADD;?>" onClick="add_option();">&nbsp;<input value="" size="7" name="update_totals['+add_num+'][title]">'
            +'</td><td class="smallText" align="right"><input id="update_totals_'+add_num+'" value="" size="6" onkeyup="clearNoNum(this);price_total(\'<?php echo TEXT_MONEY_SYMBOL;?>\');" name="update_totals['+add_num+'][value]"><input type="hidden" name="update_totals['+add_num+'][class]" value="ot_custom"><input type="hidden" name="update_totals['+add_num+'][total_id]" value="0"></td>'
            +'<td><b><img height="17" width="1" border="0" alt="" src="images/pixel_trans.gif"></b></td></tr>'
            +'<tr id="add_option_total">'+add_option_total_str+'</tr>';

    $("#add_option").append(add_str);
}

function submit_order_check(products_id,op_id){
  var qty = document.getElementById('p_'+op_id).value;

  $.ajax({
    dataType: 'text',
    url: 'ajax_orders_weight.php?action=edit_preorder',
    data: 'qty='+qty+'&products_id='+products_id, 
    type:'POST',
    async: false,
    success: function(data) {
      if(data != ''){

        if(confirm(data)){

          createPreorderChk();
          document.edit_order.submit();
        }
      }else{

        createPreorderChk();
        document.edit_order.submit();
      }
    }
  });
    
}

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

function recalc_preorder_price(oid, opd, o_str, oid_price, op_price)
{
  
  pro_num = document.getElementById('p_'+opd).value;
  p_price = oid_price; 
  p_final_price = document.getElementsByName('update_products['+opd+'][final_price]')[0].value;  
  p_op_info = op_price;  

  $.ajax({
    type: "POST",
    data:'oid='+oid+'&opd='+opd+'&o_str='+o_str+'&op_price='+p_op_info+'&p_num='+pro_num+'&p_price='+p_price+'&p_final_price='+p_final_price,
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
        document.getElementById('update_products['+opd+'][c_price]').innerHTML = '<b>'+msg_info[3]+'</b>';
      }else{
        document.getElementById('update_products['+opd+'][c_price]').innerHTML = '<b>'+msg_info[6]+'</b>'; 
      }
      document.getElementById('ot_subtotal_id').innerHTML = document.getElementById('update_products['+opd+'][c_price]').innerHTML;
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

      var update_total_temp;
      var update_total_num = 0;
      var add_num = $("#button_add_id").val();
      for(var i = 1;i <= add_num;i++){
     
        if(document.getElementById('update_totals_'+i)){
          update_total_temp = document.getElementById('update_totals_'+i).value; 
          if(update_total_temp == ''){update_total_temp = 0;}
          update_total_temp = parseInt(update_total_temp);
          update_total_num += update_total_temp;
        }
      }
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
          if(update_total_temp == ''){update_total_temp = 0;}
          update_total_temp = parseInt(update_total_temp);
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

function check_add(){
  price = document.getElementById('add_product_price').value;
  if(price != '' && price != 0  && price > 0){
    return true;
  } else {
    alert("<?php echo ERROR_INPUT_PRICE_NOTICE;?>");
    return false;
  }
}
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
      document.edit_order.etitle.value = t_msg;
    }
  });
}  

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
    one_time_pwd('<?php echo $page_name;?>');
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
                <td class="main" valign="top" width="30%"><b><?php echo ENTRY_SITE;?>:</b></td>
                <td class="main" width="70%"><font color='#FF0000'><b><?php echo tep_get_site_name_by_id($order['site_id'])?></b></font></td>
              </tr>
              <tr>
                <td class="main" valign="top" width="30%"><b><?php echo EDIT_ORDERS_ID_TEXT;?></b></td>
                <td class="main" width="70%"><?php echo $order['orders_id'];?></td>
              </tr>
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_DATE_TEXT;?></b></td>
                <td class="main"><?php echo tep_date_long(date('Y-m-d H:i:s'));?></td>
              </tr>
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_CUSTOMER_NAME;?></b></td>
                <td class="main"><?php echo tep_html_quotes($order['customers_name']); ?></td>
              </tr>
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_EMAIL;?></b></td>
                <td class="main"><font color="red"><b><?php echo $order['customers_email_address']; ?></b></font></td>
              </tr>
              <!-- End Addresses Block -->
              <!-- Begin Payment Block -->
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_PAYMENT_METHOD;?></b></td>
                <td class="main">
                <?php 
                $payment_code = payment::changeRomaji($order['payment_method'], PAYMENT_RETURN_TYPE_CODE); 
                echo payment::makePaymentListPullDownMenu($payment_code); 
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
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_NUM_PRO_NAME;?></td>
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENICY; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_BEFORE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_AFTER; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_BEFORE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_AFTER; ?></td>
    <input type="hidden" name="update_viladate" value="true">
  </tr>
  
<?php 
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
    $orders_products_num = isset($_POST['update_products'][$pid]['qty']) ? $_POST['update_products'][$pid]['qty'] : $order_products[$pid]['qty'];
    echo '    <tr class="dataTableRow">' . "\n" .
         '      <td class="' . $RowStyle . '" align="left" valign="top" width="6%">'
         . "<input name='update_products[$pid][qty]' id='p_".$pid."' size='2' value='" . $orders_products_num . "' onkeyup='clearLibNum(this);recalc_preorder_price(\"".$oID."\", \"".$pid."\", \"0\", \"".$orders_price."\", \"".$op_price."\");' class='update_products_qty'>&nbsp;x</td>\n" . 
         '      <td class="' . $RowStyle . '" width="35%">' . $order_products[$pid]['name'] . "<input name='update_products[$pid][name]' size='64' type='hidden' value='" . $order_products[$pid]['name'] . "'>\n" . 
       '      &nbsp;&nbsp;';
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
        $default_value = tep_parse_input_field_data(stripslashes($order_products_attributes[$pid][$j]['option_info']['value']), array("'"=>"&quot;")) == '' ? TEXT_UNSET_DATA : tep_parse_input_field_data(stripslashes($order_products_attributes[$pid][$j]['option_info']['value']), array("'"=>"&quot;"));
        echo '<br><div class="order_option_width">&nbsp;<i><div class="order_option_info"><div class="order_option_title"> - ' .tep_parse_input_field_data(stripslashes($order_products_attributes[$pid][$j]['option_info']['title']), array("'"=>"&quot;")).'<input type="hidden" class="option_input_width" name="update_products[' .  $pid . '][attributes]['.$j.'][option]" value=\'' .  tep_parse_input_field_data(stripslashes($order_products_attributes[$pid][$j]['option_info']['title']), array("'"=>"&quot;")) . '\'>: ' . 
           '</div><div class="order_option_value"><a onclick="popup_window(this,\''.$item_type.'\',\''.tep_parse_input_field_data(stripslashes($order_products_attributes[$pid][$j]['option_info']['title']), array("'"=>"&quot;")).'\',\''.$item_list.'\');" href="javascript:void(0);"><u>' . 
           $default_value.'</u></a><input type="hidden" class="option_input_width" name="update_products[' . $pid .  '][attributes]['.$j.'][value]" value=\'' .  tep_parse_input_field_data(stripslashes($order_products_attributes[$pid][$j]['option_info']['value']), array("'"=>"&quot;")).'\'></div>';
        //if ($order_products_attributes[$pid][$j]['price'] != '0') {
          //echo ' ('.$currencies->format($order_products_attributes[$pid][$j]['price'] * $order_products[$pid]['qty']).')'; 
        //}
        echo '</div></i></div>';
      }
    }
    
    echo '      </td>' . "\n" .
         '      <td class="' . $RowStyle . '">' . $order_products[$pid]['model'] . "<input name='update_products[$pid][model]' size='12' type='hidden' value='" . $order_products[$pid]['model'] . "'>" . '</td>' . "\n" .
         '      <td class="' . $RowStyle . '" align="right">' . tep_display_tax_value($order_products[$pid]['tax']) . "<input name='update_products[$pid][tax]' size='2' type='hidden' value='" . tep_display_tax_value($order_products[$pid]['tax']) . "'>" . '%</td>' . "\n" .
         '      <td class="' . $RowStyle . '" align="right">' . "<input type='hidden' name='update_products[$pid][final_price]' onkeyup='clearLibNum(this);recalc_preorder_price(\"".$oID."\", \"".$pid."\", \"1\", \"".$orders_price."\", \"".$op_price."\");' size='9' style='text-align:right;' value='" . tep_display_currency(number_format(abs($order_products[$pid]['final_price']),2)) 
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
    if ($order_products[$pid]['final_price'] < 0) {
      echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order_products[$pid]['final_price'] * $order_products[$pid]['qty'], true, $order['currency'], $order['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    } else {
      echo $currencies->format($order_products[$pid]['final_price'] * $order_products[$pid]['qty'], true, $order['currency'], $order['currency_value']);
    }
    echo '</div></td>' . "\n" . 
         '      <td class="' . $RowStyle . '" align="right"><div id="update_products['.$pid.'][c_price]"><b>';
    if ($order_products[$pid]['final_price'] < 0) {
      echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order_products[$pid]['final_price'], $order_products[$pid]['tax']) * $order_products[$pid]['qty'], true, $order['currency'], $order['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    } else {
      echo $currencies->format(tep_add_tax($order_products[$pid]['final_price'], $order_products[$pid]['tax']) * $order_products[$pid]['qty'], true, $order['currency'], $order['currency_value']);
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
              <td valign="top"><?php //echo "<span class='smalltext'>" .  HINT_DELETE_POSITION . EDIT_ORDERS_ADD_PRO_READ . "</span>"; ?></td>
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
    <td class="dataTableHeadingContent" align="left" width="75%"><?php echo TABLE_HEADING_FEE_MUST;?></td>
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
  foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
    $TotalStyle = "smallText";
    if ($TotalDetails["Class"] == "ot_total") {
      echo '  <tr id="add_option_total">' . "\n" .
           '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_OTTOTAL_READ.'</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . $TotalDetails["Name"] . '</b></td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b><div id="ot_total_id">';
                if($TotalDetails["Price"]>=0){
                  echo  $currencies->ot_total_format($TotalDetails["Price"], true,
                      $order['currency'], $order['currency_value']);
                }else{
                  echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->ot_total_format($TotalDetails["Price"], true, $order['currency'], $order['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
                }
                  echo '</div></b>' . 
                "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</b></td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
           '  </tr>' . "\n";
    } elseif ($TotalDetails["Class"] == "ot_subtotal") {
      echo '  <tr>' . "\n" .
           '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_OTSUBTOTAL_READ.'</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . $TotalDetails["Name"] . '</b></td>' .
           '    <td align="right" class="' . $TotalStyle . '"><b><div id="ot_subtotal_id">';
           if($TotalDetails["Price"]>=0){
                  echo  $currencies->ot_total_format($TotalDetails["Price"], true,
                      $order['currency'], $order['currency_value']);
           }else{
             echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($TotalDetails["Price"], true, $order['currency'], $order['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
           }
            echo '</div></b>' . 
                "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</b></td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
           '  </tr>' . "\n".
           '  <tr>' . "\n" .
           '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>'.TEXT_CODE_HANDLE_FEE.'</b></td>' .
           '    <td align="right" class="' . $TotalStyle . '"><b><div id="handle_fee_id">' . $currencies->format($order["code_fee"]) . '</div></b><input type="hidden" name="payment_code_fee" value="'.$order["code_fee"].'">' . 
                '</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
           '  </tr>' . "\n";
    } elseif ($TotalDetails["Class"] == "ot_tax") {
      echo '  <tr>' . "\n" . 
           '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . trim($TotalDetails["Name"]) . "</b><input name='update_totals[$TotalIndex][title]' type='hidden' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . '</td>' . "\n" .
           '    <td align="right" class="' . $TotalStyle . '"><b>' . $currencies->format($TotalDetails["Price"], true, $order['currency'], $order['currency_value']) . '</b>' . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</b></td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
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
      $button_add = $TotalIndex == count($TotalsArray)-2 ? '<INPUT type="button" id="button_add" value="'.TEXT_BUTTON_ADD.'" onClick="add_option();"><input type="hidden" id="button_add_id" value="'.(count($TotalsArray)-1).'">&nbsp;' : '';
      echo '  <tr>' . "\n" .
           '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_TOTALDETAIL_READ_ONE.'</td>' . 
           '    <td style="min-width:180px;" align="right" class="' . $TotalStyle . '">' . $button_add ."<input name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . '</td>' . "\n" .
           '    <td align="right" class="' . $TotalStyle . '">' . "<input name='update_totals[$TotalIndex][value]' id='update_totals_$TotalIndex' onkeyup='clearNoNum(this);price_total();' size='6' value='" . $TotalDetails["Price"] . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
           '   </td>' . "\n" .
           '  </tr>' . "\n";
    }
  }
?>
</table>
<span class='smalltext'><?php echo EDIT_ORDERS_PRICE_CONSTRUCT_READ; ?></span>
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
          <td class="main" width="82" style="min-width:45px;"><b><?php echo ENTRY_STATUS; ?></b></td>
          <td class="main">
          <?php
          $is_select_query = tep_db_query(" select orders_status_id, orders_status_name from " . TABLE_PREORDERS_STATUS . " where language_id = '" . (int)$languages_id . "' limit 1");
          $is_select_res = tep_db_fetch_array($is_select_query); 
          $sel_status_id = DEFAULT_PREORDERS_STATUS_ID; 
          echo tep_draw_pull_down_menu('status', $orders_statuses, $sel_status_id, 'id="status" onchange="check_prestatus();" style="width:80px;"'); 
          ?>
          </td>
        </tr>
        <tr>
          <td class="main"><b><?php echo EDIT_ORDERS_SEND_MAIL_TEXT; ?></b></td>
          <td class="main"><table bgcolor="red" cellspacing="5"><tr><td><?php echo tep_draw_checkbox_field('notify', '', isset($_GET['dtype'])?false:true); ?></td></tr></table></td>
        </tr>
        <?php if($CommentsWithStatus) { ?>
        <tr>
          <td class="main"><b><?php echo EDIT_ORDERS_RECORD_TEXT; ?></b></td>
          <td class="main"><?php echo tep_draw_checkbox_field('notify_comments', '', false); ?>&nbsp;&nbsp;<b style="color:#FF0000;"><?php echo EDIT_ORDERS_RECORD_READ; ?></b></td>
        </tr>
        <?php } ?>
      </table>
    </td>
    <td class="main" width="5%">&nbsp;</td>
    <td class="main">
    <?php
    $ma_se = "select * from ".TABLE_PREORDERS_MAIL." where orders_status_id = '".$sel_status_id."'"; 
    $mail_sele = tep_db_query($ma_se);
    $mail_sql = tep_db_fetch_array($mail_sele);
    ?>
    <?php   
    echo '<b>'.TEXT_EMAIL_TITLE.'</b>'.tep_draw_input_field('etitle', $mail_sql['orders_status_title'],'style="width:230px;"'); 
    ?> 
    <br>
    <br>
    <?php
    $order_a_str = '';
    foreach ($order_products as $okey => $ovalue) {
      $order_a_str .= $ovalue['name'].NEW_PREORDERS_CHARACTER_TEXT."\n"; 
      $order_a_str .= $ovalue['character']."\n"; 
    }
    ?>
    <textarea style="font-family:monospace;font-size:12px; width:400px;" name="comments" wrap="hard" rows="30" cols="74"><?php echo str_replace('${ORDER_A}', $order_a_str, $mail_sql['orders_status_mail']);?></textarea>
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
              <td class="main" bgcolor="#FFDDFF"><b><?php echo EDIT_ORDERS_FINAL_CONFIRM_TEXT; ?></b>&nbsp;<?php echo HINT_PRESS_UPDATE; ?></td>
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
    <tr>
      <td>
      <?php echo EDIT_ORDERS_UPDATE_COMMENT;?> 
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
