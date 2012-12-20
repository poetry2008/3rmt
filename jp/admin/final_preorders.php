<?php
/*
   $Id$
   
   编辑订单
*/

  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');
  include(DIR_FS_ADMIN . DIR_WS_LANGUAGES .  '/default.php');
  require(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_EDIT_ORDERS);
 
  $active_order_raw = tep_db_query("select is_active from ".TABLE_PREORDERS." where orders_id = '".$_GET['oID']."'");
  $active_order_res = tep_db_fetch_array($active_order_raw);
  if (!$active_order_res['is_active']) {
    tep_redirect(FILENAME_PREORDERS); 
  }
  unset($cpayment); 
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies(2);

  include(DIR_WS_CLASSES . 'preorder.php');


  $__orders_status_query = tep_db_query("
      select orders_status_id 
      from " . TABLE_PREORDERS_STATUS . " 
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
      from ".TABLE_PREORDERS_STATUS." os left join ".TABLE_PREORDERS_MAIL." om on os.orders_status_id = om.orders_status_id
      where os.language_id = " . $languages_id . " 
        and os.orders_status_id IN (".join(',', $__orders_status_ids).")");

  while($select_result = tep_db_fetch_array($select_query)){
    $osid = $select_result['orders_status_id'];
    $mt[$osid][$select_result['site_id']?$select_result['site_id']:0] = $select_result['orders_status_mail'];
    $mo[$osid][$select_result['site_id']?$select_result['site_id']:0] = $select_result['orders_status_title'];
  }

// START CONFIGURATION ################################

// Correction tax pre-values (Michel Haase, 2005-02-18)
// -> What was this ? Why 20.0, 20.0, 7.6 and 7.6 ???
//    It's used later in a 'hidden way' an produces unlogical results ...

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
      from " . TABLE_PREORDERS_PRODUCTS . " 
      where orders_id = '" . tep_db_input($oID) . "'");
  
  // 最新の注文情報取得
  $order = new preorder($oID);
  $cpayment = payment::getInstance($order->info['site_id']); 
  // ポイントを取得する
  $customer_point_query = tep_db_query("
      select point 
      from " . TABLE_CUSTOMERS . " 
      where customers_id = '" . $order->customer['id'] . "'");
  $customer_point = tep_db_fetch_array($customer_point_query);
  // ゲストチェック
  $customer_guest_query = tep_db_query("
      select customers_guest_chk, is_send_mail, is_calc_quantity 
      from " . TABLE_CUSTOMERS . " 
      where customers_id = '" . $order->customer['id'] . "'");
  $customer_guest = tep_db_fetch_array($customer_guest_query);

  if (tep_not_null($action)) {
    $payment_modules = payment::getInstance($order->info['site_id']);
    switch ($action) {
      
  // 1. UPDATE ORDER ###############################################################################################
  case 'update_order':
    $update_user_info = tep_get_user_info($ocertify->auth_user);
    $oID = tep_db_prepare_input($_GET['oID']);
    $comments_text = tep_db_prepare_input($_POST['comments_text']);
    $order = new preorder($oID);
    $status = tep_db_prepare_input($_POST['status']);
    $goods_check = $order_query;
    /*
    if (tep_db_num_rows($goods_check) == 0) {
      $messageStack->add('商品が追加されていません。', 'error');
      $action = 'edit';
      break;
    }
    */
    //viladate
  $viladate = tep_db_input($_POST['update_viladate']);//viladate pwd 
  if($viladate!='_false'&&$viladate!=''){
      tep_insert_pwd_log($viladate,$ocertify->auth_user);
    $viladate = true;
  }else if($viladate=='_false'){
    $viladate = false;
    $messageStack->add_session(EDIT_ORDERS_NOTICE_UPDATE_FAIL_TEXT, 'error');
    tep_redirect(tep_href_link(FILENAME_FINAL_PREORDERS, tep_get_all_get_params(array('action')) . 'action=edit'));
    break;
  }
    /*
    if (isset($update_tori_torihiki_date)) { //日時が有効かチェック
      if (!preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)$/', $update_tori_torihiki_date, $m)) { // check the date format
        $messageStack->add(EDIT_ORDERS_NOTICE_DATE_WRONG_TEXT, 'error');
        $action = 'edit';
        break;
      } elseif (!checkdate($m[2], $m[3], $m[1]) || $m[4] >= 24 || $m[5] >= 60 || $m[6] >= 60) { // make sure the date provided is a validate date
        $messageStack->add(EDIT_ORDERS_NOTICE_NOUSE_DATE_TEXT, 'error');
        $action = 'edit';
        break;
      }
    } else {
      $messageStack->add(EDIT_ORDERS_NOTICE_MUST_INPUT_DATE_TEXT, 'error');
      $action = 'edit';
      break;
    }
    */
    //valadate email 
    if (isset($_POST['h_deadline'])) { //日時が有効かチェック
      if (!preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d)$/', $_POST['h_deadline'], $m1)) { // check the date format
        $messageStack->add(EDIT_ORDERS_NOTICE_DATE_WRONG_TEXT, 'error');
        $action = 'edit';
        break;
      } elseif (!checkdate($m1[2], $m1[3], $m1[1])) { // make sure the date provided is a validate date
        if ($_POST['h_deadline'] != '0000-00-00') {
          $messageStack->add(EDIT_ORDERS_NOTICE_NOUSE_DATE_TEXT, 'error');
          $action = 'edit';
          break;
        }
      }
    } else {
      $messageStack->add(EDIT_ORDERS_NOTICE_MUST_INPUT_DATE_TEXT, 'error');
      $action = 'edit';
      break;
    }
    
    foreach ($update_totals as $total_index => $total_details) {    
      extract($total_details,EXTR_PREFIX_ALL,"ot");
      if ($ot_class == "ot_point" && (int)$ot_value > 0) {
        $current_point = $customer_point['point'] + $before_point;
        if ((int)$ot_value > $current_point) {
          $messageStack->add(EDIT_ORDERS_NOTICE_POINT_ERROR.' <b>' . $current_point . '</b> '.EDIT_ORDERS_NOTICE_POINT_ERROR_LINK, 'error');
          $action = 'edit';
          break 2;
        }
      }
    } 

    $comment_arr = $payment_modules->dealComment($payment_method,'');
      
    // 1.1 UPDATE ORDER INFO #####
    $UpdateOrders = "update " . TABLE_PREORDERS . " set 
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
      customers_email_address = '" . tep_db_input($update_customer_email_address) . "',
      user_update ='".$_SESSION['user_name']."',
      ";
    
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
    $payment_method_info = payment::changeRomaji($_POST['payment_method'], PAYMENT_RETURN_TYPE_TITLE); 
    $UpdateOrders .= "delivery_name = '" . tep_db_input(stripslashes($update_delivery_name)) . "',
      delivery_name_f = '" . tep_db_input(stripslashes($update_delivery_name_f)) . "',
      delivery_company = '" . tep_db_input(stripslashes($update_delivery_company)) . "',
      delivery_street_address = '" . tep_db_input(stripslashes($update_delivery_street_address)) . "',
      delivery_suburb = '" . tep_db_input(stripslashes($update_delivery_suburb)) . "',
      delivery_city = '" . tep_db_input(stripslashes($update_delivery_city)) . "',
      delivery_state = '" . tep_db_input(stripslashes($update_delivery_state)) . "',
      delivery_postcode = '" . tep_db_input($update_delivery_postcode) . "',
      delivery_country = '" . tep_db_input(stripslashes($update_delivery_country)) . "',
      payment_method = '" . tep_db_input($payment_method_info) . "',
      torihiki_date = '" . tep_db_input($update_tori_torihiki_date) . "',
      torihiki_houhou = '" . tep_db_input($update_tori_torihiki_houhou) . "',
      ensure_deadline = '" . tep_db_input($_POST['h_deadline']) . " 00:00:00',
      cc_type = '" . tep_db_input($update_info_cc_type) . "',
      cc_owner = '" . tep_db_input($update_info_cc_owner) . "',";

    $UpdateOrders .= $payment_modules->admin_get_payment_info($payment_method,$comment_arr['comment']);
    
    //if(isset($comment_arr['comment']) && !empty($comment_arr['comment'])){
        //$UpdateOrders .= "orders_comment = '{$comment_arr['comment']}',";
    //}

    if(substr($update_info_cc_number,0,8) != "(Last 4)") {
      $UpdateOrders .= "cc_number = '$update_info_cc_number',";
    }   
    $UpdateOrders .= "cc_expires = '$update_info_cc_expires',
      orders_status = '" . tep_db_input($status) . "'";
    
    if(!$CommentsWithStatus) {
      //$UpdateOrders .= ", comments = '" . tep_db_input($comments) . "'";
    }
    $UpdateOrders .= " where orders_id = '" . tep_db_input($oID) . "';";

    tep_db_query($UpdateOrders);
    preorders_updated($oID);
    $order_updated = true;

    $check_status_query = tep_db_query("select customers_id, customers_name, customers_email_address, orders_status, date_purchased from " . TABLE_PREORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
    $check_status = tep_db_fetch_array($check_status_query);

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
    from " . TABLE_PREORDERS_PRODUCTS . " 
    where orders_id = '" . tep_db_input($oID) . "' 
      and orders_products_id = '".$orders_products_id."'");

    // 1.3.1.1 Update Inventory Quantity
    $order = tep_db_fetch_array($op_query);
    if ($products_details["qty"] != $order['products_quantity']) {
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
      //if(!tep_is_oroshi($check_status['customers_id']))
      //tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = ".$pr_quantity.", products_virtual_quantity = ".$pv_quantity.", products_ordered = products_ordered + " . $quantity_difference . " where products_id = '" . (int)$order['products_id'] . "'");
      
      //tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = 0 where products_real_quantity < 0 and products_id = '" . (int)$order['products_id'] . "'");
      //tep_db_query("update " . TABLE_PRODUCTS . " set products_virtual_quantity = 0 where products_virtual_quantity < 0 and products_id = '" . (int)$order['products_id'] . "'");
    }

    if($products_details["qty"] > 0) { // a.) quantity found --> add to list & sum
      $Query = "update " . TABLE_PREORDERS_PRODUCTS . " set
          products_model = '" . $products_details["model"] . "',
          products_name = '" . str_replace("'", "&#39;", $products_details["name"]) . "',
          products_price = '" .  (tep_check_pre_product_type($orders_products_id) ? 0 - $products_details["p_price"] : $products_details["p_price"]) . "',
          final_price = '" . (tep_get_bflag_by_product_id((int)$order['products_id']) ? 0 - $products_details["final_price"] : $products_details["final_price"]) . "',
          products_tax = '" . $products_details["tax"] . "',
          products_quantity = '" . $products_details["qty"] . "'
          where orders_products_id = '$orders_products_id';";
      tep_db_query($Query);
  
// Update Tax and Subtotals: please choose sum WITH or WITHOUT tax, but activate only ONE version ;-)
  
// Correction tax calculation (Michel Haase, 2005-02-18)
// -> correct calculation, but why there is a division by 20 and afterwards a mutiplication with 20 ???
//    -> no changes made
//      $RunningSubTotal += (tep_add_tax(($products_details["qty"] * $products_details["final_price"]), $products_details["tax"])*20)/20; // version WITH tax
  
      $RunningSubTotal += $products_details["qty"] * $products_details["final_price"]; // version WITHOUT tax
      $RunningTax += (($products_details["tax"]/100) * ($products_details["qty"] * $products_details["final_price"]));
  
      // Update Any Attributes
      if (IsSet($products_details[attributes])) {
        foreach ($products_details["attributes"] as $orders_products_attributes_id => $attributes_details) {
          $input_option = array('title' => $attributes_details['option'], 'value' => $attributes_details['value']); 
          $Query = "update " . TABLE_PREORDERS_PRODUCTS_ATTRIBUTES . " set option_info = '" . tep_db_input(serialize($input_option)) . "' , options_values_price = '".$attributes_details['price']."' where orders_products_attributes_id = '$orders_products_attributes_id';";
          tep_db_query($Query);
        }
      }
      if (IsSet($_POST['new_update_products_op_title'])) {
        if (!empty($_POST['new_update_products_op_title'])) { 
            foreach($_POST['new_update_products_op_title'] as $option_key=>$option_value){
              $tmp_new_op_array = array('title' => $option_value, 'value' => $_POST['new_update_products_op_value'][$option_key]);  
              $new_op_data_array = array(
                 'orders_id' => $oID,
                 'orders_products_id' => $orders_products_id,
                 'options_values_price' => $_POST['new_update_products_op_price'][$option_key],
                 'option_info' => tep_db_input(serialize($tmp_new_op_array)),
                 'option_group_id' => $_POST['belong_to_option'],
                 'option_item_id' => $option_key
                 ); 
             tep_db_perform(TABLE_PREORDERS_PRODUCTS_ATTRIBUTES, $new_op_data_array); 
          }
        }
      }
    } else { // b.) null quantity found --> delete
      $Query = "delete from " . TABLE_PREORDERS_PRODUCTS . " where orders_products_id = '$orders_products_id';";
      tep_db_query($Query);
      $Query = "delete from " . TABLE_PREORDERS_PRODUCTS_ATTRIBUTES . " where orders_products_id = '$orders_products_id';";
      tep_db_query($Query);
      $products_delete = true;
    }
  }
  //exit;
  // 1.4. UPDATE SHIPPING, DISCOUNT & CUSTOM TAXES #####

  foreach($update_totals as $total_index => $total_details) {
    extract($total_details,EXTR_PREFIX_ALL,"ot");
  
// Correction tax calculation (Michel Haase, 2005-02-18)
// Correction tax calculation (Shimon Pozin, 2005-09-03) 
// Here is the major caveat: the product is priced in default currency, while shipping etc. are priced in target currency. We need to convert target currency
// into default currency before calculating RunningTax (it will be converted back before display)
    if ($ot_class == "ot_shipping" || $ot_class == "ot_lev_discount" || $ot_class == "ot_customer_discount" || $ot_class == "ot_custom" || $ot_class == "ot_cod_fee") {
      $order = new preorder($oID);
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
      }
  
// Set $ot_text (display-formatted value)
  
      // Correction of number_format - German format (Michel Haase, 2005-02-18)
//      $ot_text = "\$" . number_format($ot_value, 2, ',', '');
  
      $order = new preorder($oID);

      if ($customer_guest['customers_guest_chk'] == 0 && $ot_class == "ot_point" && $ot_value != $before_point) { //会員ならポントの増減
        $point_difference = ($ot_value - $before_point);
        //tep_db_query("update " . TABLE_CUSTOMERS . " set point = point - " . $point_difference . " where customers_id = '" . $order->customer['id'] . "'"); 
      }

      $ot_text = $currencies->format($ot_value, true, $order->info['currency'], $order->info['currency_value']);
  
      if ($ot_class == "ot_total") {
        $ot_text = "<b>" . $ot_text . "</b>";
      }
  
      if($ot_total_id > 0 || $ot_class == "ot_point") { // Already in database --> Update
        /*
           delete form query
            text = "' . tep_insert_currency_text($ot_text) . '",
           */
        $Query = 'UPDATE ' . TABLE_PREORDERS_TOTAL . ' SET
            title = "' . $ot_title . '",
            value = "' . tep_insert_currency_value($ot_value) . '",
            sort_order = "' . $sort_order . '"
            WHERE orders_total_id = "' . $ot_total_id . '"';
            tep_db_query($Query);
      } else { // New Insert
        /*
           change form query
            text = "' . tep_insert_currency_text($ot_text) . '",
            to
            text = ""
           */
            $Query = 'INSERT INTO ' . TABLE_PREORDERS_TOTAL . ' SET
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
      $Query = "delete from " . TABLE_PREORDERS_TOTAL . " where orders_total_id = '$ot_total_id'";
      tep_db_query($Query);
    }
  
  }
//  print "Totale ".$RunningTotal;
//  exit;   
  
  $order = new preorder($oID);
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
  /*delete text = '".tep_insert_currency_text($currencies->format($new_subtotal,
    true, $order->info['currency']))."'
    for all update TABLE_ORDERS_TOTAL
     */
  tep_db_query("update " . TABLE_PREORDERS_TOTAL . " set value = '".tep_insert_currency_value($new_subtotal)."' where class='ot_subtotal' and orders_id = '".$oID."'");
  
  //tax
  $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_PREORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
  $plustax = tep_db_fetch_array($plustax_query);
  if($plustax['cnt'] > 0) {
    tep_db_query("update " . TABLE_PREORDERS_TOTAL . " set value = '".tep_insert_currency_value($new_tax)."' where class='ot_tax' and orders_id = '".$oID."'");
  }

  //point修正中
  $point_query = tep_db_query("select sum(value) as total_point from " . TABLE_PREORDERS_TOTAL . " where class = 'ot_point' and orders_id = '" . $oID . "'");
  $total_point = tep_db_fetch_array($point_query);

  //total
  $total_query = tep_db_query("select sum(value) as total_value from " . TABLE_PREORDERS_TOTAL . " where class != 'ot_total' and class != 'ot_point' and orders_id = '" . $oID . "'");
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
  //if (($newtotal - $total_point["total_point"]) >= 1) {
  if ($newtotal > 0) {
    $newtotal -= $total_point["total_point"];
  }
  //} else {
  //  $newtotal = '0';
  //}
  
  $handle_fee = $cpayment->handle_calc_fee($_POST['payment_method'], $newtotal);
  //$newtotal = $newtotal + $_POST['payment_code_fee']; 
  $newtotal = $newtotal+$handle_fee;
  /*
  delete form  $totals = update .....
, text = '<b>" . $currencies->ot_total_format(intval(floor($newtotal)), true, $order->info['currency']) . "</b>'
     */
  $totals = "update " . TABLE_PREORDERS_TOTAL . " set value = '" . intval(floor($newtotal)) . "' where class='ot_total' and orders_id = '" . $oID . "'";
  tep_db_query($totals);
  
  $update_orders_sql = "update ".TABLE_PREORDERS." set code_fee = '".$handle_fee."' where orders_id = '".$oID."'";
  tep_db_query($update_orders_sql);
    
  // 最終処理（更新およびメール送信）
  if ($products_delete == false) {
    tep_pre_order_status_change($oID,$status);
    tep_db_query("update " . TABLE_PREORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
    preorders_updated(tep_db_input($oID));
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
      for ($i=0; $i<sizeof($order->products); $i++) {
        //$orders_products_id = $order->products[$i]['orders_products_id'];
        $products_ordered_mail .= "\t" . FORDERS_MAIL_PRODUCTS_NAME . $order->products[$i]['name'] . '（' . $order->products[$i]['model'] . '）' . "\n";
        // Has Attributes?
        if (sizeof($order->products[$i]['attributes']) > 0) {
          for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
            $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['id'];
            $products_ordered_mail .=  "\t" .  tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option_info']['title'], array("'"=>"&quot;")) . '　　　　　：';
            $products_ordered_mail .= tep_parse_input_field_data(str_replace(array("<br>", "<BR>", "\r", "\n", "\r\n"), "", $order->products[$i]['attributes'][$j]['option_info']['value']), array("'"=>"&quot;")) . "\n";
          }
        }

        $products_ordered_mail .= "\t" . FORDERS_MAIL_PRODUCTS_NUM .  $order->products[$i]['qty'] . EDIT_ORDERS_NUM_UNIT . tep_get_full_count2($order->products[$i]['qty'], $order->products[$i]['id']) . "\n";
        $products_ordered_mail .= "\t" . FORDERS_MAIL_PRODUCTS_PRICE . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax']) . "\n"; 
        $products_ordered_mail .= "\t" . FORDERS_MAIL_PRODUCTS_TOTAL_MONEY . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . "\n";
        $products_ordered_mail .= "\t" . '------------------------------------------' . "\n";
        if (tep_get_cflag_by_product_id($order->products[$i]['id'])) {
            if (tep_get_bflag_by_product_id($order->products[$i]['id'])) {
              $products_ordered_mail .= FORDERS_MAIL_PRODUCTS_BFLAG_TEXT."\n\n";
            } else {
              $products_ordered_mail .= FORDERS_MAIL_PRODUCTS_NOBFLAG_TEXT."\n\n";
            }
                }
      }
$total_details_mail = '';
$totals_query = tep_db_query("select * from " . TABLE_PREORDERS_TOTAL . " where orders_id = '" . tep_db_input($oID) . "' order by sort_order");
$order->totals = array();
while ($totals = tep_db_fetch_array($totals_query)) {
  if ($totals['class'] == "ot_point" || $totals['class'] == "ot_subtotal") {
    if ((int)$totals['value'] >= 1 && $totals['class'] != "ot_subtotal") {
      $total_details_mail .= "\t" . FORDERS_MAIL_PRODUCTS_SALE . $currencies->format($totals['value']) . "\n";
    }
  } elseif ($totals['class'] == "ot_total") {
    if($handle_fee)
      $total_details_mail .= "\t" . FORDERS_MAIL_HANDLE_FEE . $currencies->format($handle_fee)."\n";
    $total_details_mail .= "\t" . FORDERS_MAIL_TOTAL_MONEY . $currencies->format($totals['value']) . "\n";
  } else {
    // 去掉 決済手数料 消費税
    $totals['title'] = str_replace(FORDERS_MAIL_REPLACE_TRAN_HANDLE_FEE, FORDERS_MAIL_REPLACE_HANDLE_FEE, $totals['title']);
    $total_details_mail .= "\t" . $totals['title'] . str_repeat('　', intval((16 -
            strlen($totals['title']))/2)) . '：' . $currencies->format($totals['value']) . "\n";
  }
}

      $email = '';
      $email .= $order->customer['name'] . FORDERS_MAIL_YANG_TEXT . "\n\n";
      $email .= FORDERS_MAIL_SITE_BEFORE_TEXT .  get_configuration_by_site_id('STORE_NAME', $order->info['site_id']) .  FORDERS_MAIL_SITE_AFTER_TEXT . "\n";
      $email .= FORDERS_MAIL_CONFIRM_CONTENT_TEXT . "\n\n";
      $email .= $notify_comments_mail;
      $email .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
      $email .= FORDERS_MAIL_CONFIRM_ORDERS_ID . $oID . "\n";
      $email .= FORDERS_MAIL_CONFIRM_CUSTOMERS_NAME. $order->customer['name'] .  FORDERS_MAIL_YANG_TEXT . "\n";
      $email .= FORDERS_MAIL_CONFIRM_EMAIL . $order->customer['email_address'] . "\n";
      $email .= FORDERS_MAIL_CONFIRM_PAYMENT_METHOD. $order->info['payment_method'] . "\n";
      $email .= FORDERS_MAIL_CONFIRM_FETCH_TIME . $order->tori['date'] .  FORDERS_MAIL_CONFIRM_ALL_DAY . "\n";
      $email .= FORDERS_MAIL_CONFIRM_HOUHOU . $order->tori['houhou'] . "\n";
      $email .= '━━━━━━━━━━━━━━━━━━━━━' . "\n\n";
      $email .= FORDERS_MAIL_CONFIRM_PRODUCTS. "\n";
      $email .= "\t" . '------------------------------------------' . "\n";
      $email .= $products_ordered_mail;
      $email .= $total_details_mail;
      $email .= "\n\n\n\n";
//      $email .= '会員のお客様は' . EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL') . "\n\n\n\n";
      $email .= FORDERS_MAIL_CONFIRM_OID_TEXT. "\n";
      $email .= '「' . get_configuration_by_site_id('STORE_NAME', $order->info['site_id']) . '」'.FORDERS_MAIL_CONTACT_SITE_TEXT . "\n\n";
      $email .= '['.FORDERS_MAIL_CONTACT_NEXT_TEXT.']━━━━━━━━━━━━' . "\n";
      $email .=  FORDERS_MAIL_CONTACT_NAME. "\n";
      $email .= get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $order->info['site_id']) . "\n";
      $email .= get_url_by_site_id($order->info['site_id']) . "\n";
      $email .= '━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
      
      $email = ''; 
      $select_status_query = tep_db_query("select orders_status_mail from ".TABLE_PREORDERS_MAIL." where orders_status_id = '".$_POST['status']."'"); 
      $select_status_res = tep_db_fetch_array($select_status_query);
      if ($select_status_res) {
        $email = $select_status_res['orders_status_mail']; 
      }
      
      $select_products_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$oID."'");
      $select_products_res = tep_db_fetch_array($select_products_raw);

      $select_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$oID."' and class = 'ot_total'");
      $select_total_res = tep_db_fetch_array($select_total_raw);
      
      $pre_otm = (int)$select_total_res['value'].TEXT_MONEY_SYMBOL;
      
      $ot_sub_query = tep_db_query("select value from " . TABLE_PREORDERS_TOTAL . " where orders_id = '".$oID."' and class = 'ot_subtotal'");
      $ot_sub_result = tep_db_fetch_array($ot_sub_query);
      $ot_sub_total = abs((int)$ot_sub_result['value']).TEXT_MONEY_SYMBOL;
      
      $num_product = 0; 
      $num_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$oID."'"); 
      $num_product_res = tep_db_fetch_array($num_product_raw); 
      if ($num_product_res) {
        $num_product = $num_product_res['products_quantity']; 
      }
      
      $ensure_date_arr = explode(' ', $select_products_res['ensure_deadline']);
      $email = $_POST['comments']; 
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
        '${PRODUCTS_NAME}',
        '${PRODUCTS_PRICE}',
        '${SUB_TOTAL}'
      ),array(
        $select_products_res['customers_name'],
        $select_products_res['customers_email_address'],
        tep_date_long($select_products_res['date_purchased']),
        $oID,
        $select_products_res['payment_method'],
        $pre_otm,
        $select_products_res['orders_status_name'],
        get_configuration_by_site_id('STORE_NAME', $select_products_res['site_id']),
        get_url_by_site_id($select_products_res['site_id']),
        get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $select_products_res['site_id']),
        date('Y'.YEAR_TEXT.'n'.MONTH_TEXT.'j'.DAY_TEXT,strtotime(tep_get_pay_day())),
        $ensure_date_arr[0],
        $num_product.PREORDER_PRODUCT_UNIT_TEXT,
        $num_product_res['products_name'],
        $currencies->display_price($num_product_res['final_price'], $num_product_res['products_tax']),
        $ot_sub_total
      ),$email);
      
      if ($customer_guest['is_send_mail'] != '1') {
        if ($status == 32) {
          $site_url_raw = tep_db_query("select * from sites where id = '".$order->info['site_id']."'"); 
          $site_url_res = tep_db_fetch_array($site_url_raw); 
          $change_preorder_url_param = md5(time().$oID);
          $change_preorder_url = $site_url_res['url'].'/change_preorder.php?pid='.$change_preorder_url_param; 
          $email = str_replace('${REAL_ORDER_URL}', $change_preorder_url, $email); 
          
          tep_db_query("update ".TABLE_PREORDERS." set check_preorder_str = '".$change_preorder_url_param."' where orders_id = '".$oID."'"); 
        }
        $preorder_email_title = $_POST['etitle']; 
        $select_status_raw = tep_db_query("select * from ".TABLE_PREORDERS_MAIL." where orders_status_id = '".$status."'"); 
        $select_status_res = tep_db_fetch_array($select_status_raw);
        if ($select_status_res) {
          $select_t_products_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$oID."'");
          $select_t_products_res = tep_db_fetch_array($select_t_products_raw);

          $select_t_total_raw = tep_db_query("select * from ".TABLE_PREORDERS_TOTAL." where orders_id = '".$oID."' and class = 'ot_total'");
          $select_t_total_res = tep_db_fetch_array($select_t_total_raw);
          
          $pre_t_otm = (int)$select_t_total_res['value'].TEXT_MONEY_SYMBOL;
          
          $preorder_email_title = str_replace(array(
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
            '${PRODUCTS_NAME}', 
            '${PRODUCTS_PRICE}',
            '${SUB_TOTAL}'
          ),array(
            $select_t_products_res['customers_name'],
            $select_t_products_res['customers_email_address'],
            tep_date_long($select_t_products_res['date_purchased']),
            $oID,
            $select_t_products_res['payment_method'],
            $pre_t_otm,
            $select_t_products_res['orders_status_name'],
            get_configuration_by_site_id('STORE_NAME', $select_t_products_res['site_id']),
            get_url_by_site_id($select_t_products_res['site_id']),
            get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS', $select_t_products_res['site_id']),
            date('Y'.YEAR_TEXT.'n'.MONTH_TEXT.'j'.DAY_TEXT,strtotime(tep_get_pay_day())),
            $ensure_date_arr[0],
            $num_product.PREORDER_PRODUCT_UNIT_TEXT,
            $num_product_res['products_name'],
            $currencies->display_price($num_product_res['final_price'], $num_product_res['products_tax']),
            $ot_sub_total
          ),$preorder_email_title);
        }
        $s_status_raw = tep_db_query("select nomail from ".TABLE_PREORDERS_STATUS." where orders_status_id = '".$status."'");  
        $s_status_res = tep_db_fetch_array($s_status_raw);
        $email = str_replace(TEXT_MONEY_SYMBOL,SENDMAIL_TEXT_MONEY_SYMBOL,$email);
        if ($s_status_res['nomail'] != 1) {
          tep_mail($check_status['customers_name'], $check_status['customers_email_address'], $preorder_email_title, $email, get_configuration_by_site_id('STORE_OWNER', $order->info['site_id']), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $order->info['site_id']),$order->info['site_id']);
          
          tep_mail(get_configuration_by_site_id('STORE_OWNER', $order->info['site_id']), get_configuration_by_site_id('SENTMAIL_ADDRESS', $order->info['site_id']), $preorder_email_title, $email, $check_status['customers_name'], $check_status['customers_email_address'], $order->info['site_id']);
        }
      } 
      //tep_mail(get_configuration_by_site_id('STORE_OWNER', $order->info['site_id']), get_configuration_by_site_id('SENTMAIL_ADDRESS', $order->info['site_id']), FORDERS_MAIL_UPDATE_CONTENT_MAIL.'【' . get_configuration_by_site_id('STORE_NAME', $order->info['site_id']) . '】', $email, $check_status['customers_name'], $check_status['customers_email_address'],$order->info['site_id']);
      $customer_notified = '1';
    }
    tep_db_query("insert into " . TABLE_PREORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments, user_added) values ('" .  tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" .  tep_db_input($customer_notified) . "', '" .  mysql_real_escape_string($comment_arr['comment'].$comments_text) . "', '".tep_db_input($update_user_info['name'])."')");
    $order_updated_2 = true;
  }

    if ($order_updated && !$products_delete && $order_updated_2) {
      $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
    } elseif ($order_updated && $products_delete) {
      $messageStack->add_session(EDIT_ORDERS_NOTICE_PRODUCT_DEL, 'success');
    } else {
      $messageStack->add_session(EDIT_ORDERS_NOTICE_ERROR_OCCUR, 'error');
    }

    tep_redirect(tep_href_link(FILENAME_FINAL_PREORDERS, tep_get_all_get_params(array('action')) . 'action=edit'));
    
  break;

  // 2. ADD A PRODUCT ###############################################################################################
  case 'add_product':
  
    if($step == 5)
    {
      // 2.1 GET ORDER INFO #####
       
      $oID = tep_db_prepare_input($_GET['oID']);
      $order = new preorder($oID);
      
      if (isset($_POST['add_product_options'])) {
        $add_product_options = $_POST['add_product_options'];
      }
      $AddedOptionsPrice = 0;

      // 2.1.1 Get Product Attribute Info
      if(IsSet($add_product_options))
      {
        foreach($add_product_options as $option_id => $option_value_id)
        {
          //echo 'a';
          $result = tep_db_query("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON po.products_options_id=pa.options_id LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON pov.products_options_values_id=pa.options_values_id WHERE products_id='$add_product_products_id' and options_id=$option_id and options_values_id=$option_value_id and po.language_id = '" . (int)$languages_id . "' and pov.language_id = '" . (int)$languages_id . "'");
          $row = tep_db_fetch_array($result);
          extract($row, EXTR_PREFIX_ALL, "opt");
          $AddedOptionsPrice += $opt_options_values_price;
          $option_value_details[$option_id][$option_value_id] = array ("options_values_price" => $opt_options_values_price);
          $option_names[$option_id] = $opt_products_options_name;
          $option_values_names[$option_value_id] = $opt_products_options_values_name;
          $option_attributes_id[$option_value_id] = $opt_products_attributes_id;
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
      
      // 2.2 UPDATE ORDER #####
      $Query = "insert into " . TABLE_PREORDERS_PRODUCTS . " set
        orders_id = '$oID',
        products_id = $add_product_products_id,
        products_model = '$p_products_model',
        products_name = '" . str_replace("'", "&#39;", $p_products_name) . "',
        products_price = '$p_products_price',
        final_price = '" . ($p_products_price + $AddedOptionsPrice) . "',
        products_tax = '$ProductsTax',
        site_id = '".tep_get_pre_site_id_by_orders_id($oID)."',
        products_rate = '".tep_get_products_rate($add_product_products_id)."',
        products_quantity = '" . (int)$add_product_quantity . "';";
      tep_db_query($Query);
      $new_product_id = tep_db_insert_id();
      
      preorders_updated($oID);
      
      // 2.2.1 Update inventory Quantity
      $p = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$add_product_products_id."'"));
      if ((int)$add_product_quantity > $p['products_real_quantity']) {
        // 买取商品大于实数
        /* 
        tep_db_perform('products',array(
          'products_real_quantity' => 0,
          'products_virtual_quantity' => $p['products_virtual_quantity'] - (int)$add_product_quantity + $p['products_real_quantity']
        ),
        'update',
        "products_id = '" . $add_product_products_id . "'");
        */ 
      } else {
        /* 
        tep_db_perform('products',array(
          'products_real_quantity' =>$p['products_real_quantity']  - (int)$add_product_quantity
        ),
        'update',
        "products_id = '" . $add_product_products_id . "'");
        */ 
      }
      // 增加销售量
      //tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . (int)$add_product_quantity . " where products_id = '" . $add_product_products_id . "'");
      // 处理负数问题
      //tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = 0 where products_real_quantity < 0 and products_id = '" . $add_product_products_id . "'");
      //tep_db_query("update " . TABLE_PRODUCTS . " set products_virtual_quantity = 0 where products_virtual_quantity < 0 and products_id = '" . $add_product_products_id . "'");

      if (IsSet($add_product_options)) {
      
        foreach($add_product_options as $option_id => $option_value_id) {
          $Query = "insert into " . TABLE_PREORDERS_PRODUCTS_ATTRIBUTES . " set
            orders_id = '$oID',
            orders_products_id = $new_product_id,
            products_options = '" . $option_names[$option_id] . "',
            products_options_values = '" . tep_db_input($option_values_names[$option_value_id]) . "',
            options_values_price = '" . $option_value_details[$option_id][$option_value_id]["options_values_price"] . "',
            attributes_id = '" . $option_attributes_id[$option_value_id] . "',
            price_prefix = '+';";
          tep_db_query($Query);
        }
      }
      
      // 2.2.2 Calculate Tax and Sub-Totals
      $order = new preorder($oID);
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
         delete
, text = '".tep_insert_currency_text($currencies->format($new_subtotal, true, $order->info['currency']))."'
         */
      tep_db_query("update " . TABLE_PREORDERS_TOTAL . " set value = '".tep_insert_currency_value($new_subtotal)."' where class='ot_subtotal' and orders_id = '".$oID."'");
      
      //tax
      $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_PREORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
      $plustax = tep_db_fetch_array($plustax_query);
      /*
         delete from update 
         text = '".tep_insert_currency_text
         ($currencies->format($new_tax, true, $order->info['currency']))."'
      */
      if($plustax['cnt'] > 0) {
        tep_db_query("update " . TABLE_PREORDERS_TOTAL . " set value = '".tep_insert_currency_value($new_tax)."' where class='ot_tax' and orders_id = '".$oID."'");
      }
      
      //total
      $total_query = tep_db_query("select sum(value) as total_value from " . TABLE_PREORDERS_TOTAL . " where class != 'ot_total' and orders_id = '".$oID."'");
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
      $payment_code = payment::changeRomaji($order->info['payment_method'], PAYMENT_RETURN_TYPE_CODE); 
      $handle_fee = $cpayment->handle_calc_fee($payment_code, $newtotal);
      $newtotal   = $newtotal+$handle_fee;
      
      /* delete text for update 
         text = '<b>".$currencies->ot_total_format
         (intval(floor($newtotal)), true, $order->info['currency'])."</b>'
      */
      $totals = "update " . TABLE_PREORDERS_TOTAL . " set value = '".intval(floor($newtotal))."' where class='ot_total' and orders_id = '".$oID."'";
      tep_db_query($totals);
      
      $update_orders_sql = "update ".TABLE_PREORDERS." set code_fee = '".$handle_fee."' where orders_id = '".$oID."'";
      tep_db_query($update_orders_sql);
      //exit;
      tep_redirect(tep_href_link(FILENAME_FINAL_PREORDERS, tep_get_all_get_params(array('action')) . 'action=edit'));
    }
    break;
  }
}

  if (($action == 'edit') && isset($_GET['oID'])) {
    if(isset($_GET['once_pwd'])&&$_GET['once_pwd']){
      tep_insert_pwd_log($_GET['once_pwd'],$ocertify->auth_user);
    }
    $oID = tep_db_prepare_input($_GET['oID']);
    $orders_query = tep_db_query("select orders_id from " . TABLE_PREORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
    $order_exists = true;
    if (!tep_db_num_rows($orders_query)) {
      $order_exists = false;
      $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
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
<script language="javascript" src="js2php.php?path=js&name=popup_window&type=js"></script>
<script language="javascript">
function submit_order_check(products_id,op_id){
  var _end = $("#status").val();
  if($("#confrim_mail_title_"+_end).val()==$("#mail_title").val()){
  }else{
    if(confirm("<?php echo TEXT_STATUS_MAIL_TITLE_CHANGED;?>")){
    }else{
      return false;
    }
  }
  var qty = document.getElementById('update_products_new_qty_'+op_id).value;

  $.ajax({
    dataType: 'text',
    url: 'ajax_orders_weight.php?action=edit_new_preorder',
    data: 'qty='+qty+'&products_id='+products_id, 
    type:'POST',
    async: false,
    success: function(data) {
      if(data != ''){

        if(confirm(data)){

          check_mail_product_status('<?php echo $_GET['oID'];?>');
          
        }
      }else{
  
         check_mail_product_status('<?php echo $_GET['oID'];?>');
         
      }
    }
  });
    
}

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
//todo:修改通性用
<?php
      $cpayment = payment::getInstance();
      $payment_array = payment::getPaymentList();
      foreach($payment_array[0] as $pay_key=>$pay_value){ 
        $payment_info = $cpayment->admin_get_payment_info_comment($pay_value,$order->customer['email_address'],$order->info['site_id']);
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
  function hidden_payment(){
     var idx = document.edit_order.elements["payment_method"].selectedIndex;
     var CI =  document.edit_order.elements["payment_method"].options[idx].value;
     $(".rowHide").hide();
     $(".rowHide").find("input").attr("disabled","true");
     $(".rowHide_"+CI).show();
     $(".rowHide_"+CI).find("input").removeAttr("disabled");
     if(CI == '<?php echo $pay_type_str;?>'){
       $("#handle_fee_id").html('<?php echo $handle_fee_code.TEXT_MONEY_SYMBOL;?>');
     }else{
       $("#handle_fee_id").html(0+'<?php echo TEXT_MONEY_SYMBOL;?>'); 
     }
     price_total('<?php echo TEXT_MONEY_SYMBOL;?>');
  }
$(document).ready(function(){
  hidden_payment();
  $("select[name='payment_method']").change(function(){
    hidden_payment();
  });
});
$(document).ready(function() {
   var se_status = document.getElementById('status').value;  
  $.ajax({
    url:'ajax_preorders.php?action=get_nyuuka',
    data: 'sid='+se_status, 
    type:'POST',
    dataType: 'text', 
    async: false,
    success: function(data) {
      document.getElementById('isruhe').value = data; 
    }
  });
});
function check_mail_product_status(pid)
{
   var direct_single = false; 
   var select_status = document.getElementById('status').value;  
   var isruhe_value = document.getElementById('isruhe').value;  
   var ensure_date = document.getElementById('date_ensure_deadline').value; 
   ensure_date = ensure_date.replace(/(^\s*)|(\s*$)/g, ""); 
   document.getElementById("h_deadline").value = document.getElementById("date_ensure_deadline").value; 
   if (select_status == 32) {
     if (ensure_date == '' || ensure_date == '0000-00-00') {
         direct_single = true; 
     } 
   }
   if ((isruhe_value == 1) && (ensure_date == '0000-00-00')) {
         direct_single = true; 
   }
   
   if (direct_single) {
     alert('<?php echo NOTICE_INPUT_ENSURE_DEADLINE;?>'); 
   }
   
   if (!direct_single) { 
   $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
var pwd_arr = data.split(",");;
var flag_tmp = true;
$(".once_pwd").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var percent = 0;
  $.ajax({
    url: 'ajax_preorders.php?action=getpercent',
    data: 'cid='+op_id,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(_data) {
      percent = _data/100;  
    }
    });
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*(1+percent))||input_val < Math.abs(final_val*(1-percent))){
    if(percent!=0){
      flag_tmp=false;
    }
  }
  });
  if(!flag_tmp){
  var pwd =  window.prompt("<?php echo FORDERS_NOTICE_INPUT_ONCE_PWD;?>\r\n","");
  if(in_array(pwd,pwd_arr)){
  $("input[name=update_viladate]").val(pwd);
    _flag = true; 
  }else{
  alert("<?php echo FORDERS_NOTICE_ONCE_PWD_WRONG;?>");
  $("input[name=update_viladate]").val('_false');
  $("input[name=x]").val('43');
  $("input[name=y]").val('12');
  //document.edit_order.submit();
  return false;
  }
  }else{
    $("input[name=update_viladate]").val('');
    $("input[name=x]").val('43');
    $("input[name=y]").val('12');
    _flag = true;
  }
}
});

   if (!direct_single&&_flag) {
     document.edit_order.submit(); 
   }
  }
}
function check_prestatus() {
  var s_value = document.getElementById('status').value;
  $.ajax({
    url:'ajax_preorders.php?action=get_nyuuka',
    data: 'sid='+s_value, 
    type:'POST',
    dataType: 'text', 
    async: false,
    success: function(data) {
      document.getElementById('isruhe').value = data; 
    }
  });

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

function recalc_preorder_price(oid, opd, o_str, op_str)
{
  var op_array = op_str.split('|||');
  var p_op_info = 0; 
  for (var i=0; i<op_array.length; i++) {
    if (op_array[i] != '') {
      if(o_str == 'true' || document.getElementById('belong_to_option')){
        p_op_info += parseInt(document.getElementsByName('new_update_products_op_price['+op_array[i]+']')[0].value); 
      }else{
        p_op_info += parseInt(document.getElementsByName('update_products['+opd+'][attributes]['+op_array[i]+'][price]')[0].value); 
      }
    }
  }
  pro_num = document.getElementById('update_products_new_qty_'+opd).value;
  p_price = document.getElementsByName('update_products['+opd+'][p_price]')[0].value;
  p_final_price = document.getElementsByName('update_products['+opd+'][final_price]')[0].value;
  $.ajax({
    type: "POST",
    data:'oid='+oid+'&opd='+opd+'&o_str='+o_str+'&op_price='+p_op_info+'&p_num='+pro_num+'&p_price='+p_price+'&p_final_price='+p_final_price,
    async:false,
    url: 'ajax_preorders.php?action=recalc_price',
    success: function(msg) {
      msg_info = msg.split('|||');
      if(o_str != 3){
        document.getElementsByName('update_products['+opd+'][final_price]')[0].value = msg_info[0];
        document.getElementById('update_products['+opd+'][final_price]').innerHTML = msg_info[7];
      }
      if(o_str != 3){
        document.getElementById('update_products['+opd+'][a_price]').innerHTML = msg_info[1];
      }else{
        document.getElementById('update_products['+opd+'][a_price]').innerHTML = msg_info[4]; 
      }
      if(o_str != 3){
        document.getElementById('update_products['+opd+'][b_price]').innerHTML = msg_info[2];
      }else{
        document.getElementById('update_products['+opd+'][b_price]').innerHTML = msg_info[5]; 
      }
      if(o_str != 3){
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
    if ($("#date_predate").val() != '') {
      if ($("#date_predate").val() == '0000-00-00') {
        date_info_str = '<?php echo date('Y-m-d', time())?>';  
        date_info = date_info_str.split('-');  
      } else {
        date_info = $("#date_predate").val().split('-'); 
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
        tmp_show_date = dtdate.format(newDate); 
        tmp_show_date_array = tmp_show_date.split('-');
        $("#update_predate_year").val(tmp_show_date_array[0]); 
        $("#update_predate_month").val(tmp_show_date_array[1]); 
        $("#update_predate_day").val(tmp_show_date_array[2]); 
        $("#date_predate").val(tmp_show_date); 
        $('#toggle_open').val('0');
        $('#toggle_open').next().html('<div id="mycalendar"></div>');
      });
    });
  }
}

function open_ensure_calendar()
{
  var is_open = $('#toggle_ensure').val(); 
  if (is_open == 0) {
    $('#toggle_ensure').val('1'); 
    var rules = {
           "all": {
                  "all": {
                           "all": {
                                      "all": "current_s_day",
                                }
                     }
            }};
    if ($("#date_ensure_deadline").val() != '') {
      if ($("#date_ensure_deadline").val() == '0000-00-00') {
        date_info_str = '<?php echo date('Y-m-d', time())?>';  
        date_info = date_info_str.split('-');  
      } else {
        date_info = $("#date_ensure_deadline").val().split('-'); 
      }

    } else {
      date_info_str = '<?php echo date('Y-m-d', time())?>';  
      date_info = date_info_str.split('-');  
    }
    new_date = new Date(date_info[0], date_info[1]-1, date_info[2]); 
    
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
            contentBox: "#ecalendar",
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
        $("#update_ensure_year").val(tmp_show_date_array[0]); 
        $("#update_ensure_month").val(tmp_show_date_array[1]); 
        $("#update_ensure_day").val(tmp_show_date_array[2]); 
        $("#date_ensure_deadline").val(tmp_show_date); 
        $('#toggle_ensure').val('0');
        $('#toggle_ensure').next().html('<div id="ecalendar"></div>');
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
  update_predate_str = $("#update_predate_year").val()+"-"+$("#update_predate_month").val()+"-"+$("#update_predate_day").val(); 
  if (!is_date(update_predate_str)) {
    alert('<?php echo ERROR_INPUT_RIGHT_DATE;?>'); 
  } else {
    $("#date_predate").val(update_predate_str); 
  }
}

function change_ensure_date() {
  update_ensure_str = $("#update_ensure_year").val()+"-"+$("#update_ensure_month").val()+"-"+$("#update_ensure_day").val(); 
  if (!is_date(update_ensure_str)) {
    alert('<?php echo ERROR_INPUT_RIGHT_DATE;?>'); 
  } else {
    $("#date_ensure_deadline").val(update_ensure_str); 
  }
}
</script>
<script language="javascript">
//$(function() {
 //$.datePicker.setDateFormat('ymd', '-');
 //$('#date_predate').datePicker();
 //$('#date_ensure_deadline').datePicker();
//});
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
textarea{
/*  width:100%;
*/}
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
#new_yui3, #ensure_yui3{
	position:absolute;
}
</style>
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
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header //-->
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
<!-- header_eof //-->
<!-- body //-->
<?php echo tep_draw_form('edit_order', FILENAME_FINAL_PREORDERS, tep_get_all_get_params(array('action','paycc')) . 'action=update_order', 'post','onSubmit="return presubmitChk();"'); ?>
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
    $order = new preorder($oID);
    $preorders_products_query = tep_db_query("select orders_products_id from ". TABLE_PREORDERS_PRODUCTS . " where orders_id='".$oID."'");
    $preorders_products_array = tep_db_fetch_array($preorders_products_query);
    $order_products_id = $preorders_products_array['orders_products_id'];
    tep_db_free_result($preorders_products_query);
?>
        <tr>
          <td width="100%">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
                <td class="pageHeading" align="right">
    <?php echo '<a href="' . tep_href_link('handle_new_preorder.php', 'oID='.$_GET['oID']) . '">' . tep_html_element_button(BUTTON_WRITE_PREORDER) . '</a>'; ?>
    &nbsp; 
    <?php echo tep_html_element_button(TEXT_FOOTER_CHECK_SAVE, 'onclick="submit_order_check('.$order->products[0]['id'].','.$order_products_id.');"');?>
    &nbsp;
    <?php echo '<a href="' . tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params()) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?>
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
                <td class="main" valign="top" width="30%"><b><?php echo ENTRY_SITE;?>:</b></td>
                <td class="main" width="70%"><?php echo tep_get_pre_site_name_by_order_id($oID);?></td>
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
                <td class="main">
                  <input class="edit_input" name="update_customer_name" size="25" value="<?php echo tep_html_quotes($order->customer['name']); ?>">
                  <br><span class="smalltext"><?php echo EDIT_ORDERS_CUSTOMER_NAME_READ;?></span>
                </td>
              </tr>
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_EMAIL;?></b></td>
                <td class="main"><input class="edit_new_input" name="update_customer_email_address" size="45" value="<?php echo $order->customer['email_address']; ?>"></td>
              </tr>
              <!-- End Addresses Block -->
              <!-- Begin Payment Block -->
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_PAYMENT_METHOD;?></b></td>
                <td class="main">
                  <?php 
                  $payment_code = payment::changeRomaji($order->info['payment_method'], PAYMENT_RETURN_TYPE_CODE); 
                  $payment_code = isset($_POST['payment_method']) ? $_POST['payment_method'] : $payment_code;
                  echo payment::makePaymentListPullDownMenu($payment_code); 
                  $orders_status_history_query = tep_db_query("select comments from ". TABLE_PREORDERS_STATUS_HISTORY ." where orders_id='".$oID."' order by date_added desc limit 0,1"); 
                  $orders_status_history_array = tep_db_fetch_array($orders_status_history_query);
                  $pay_comment = $orders_status_history_array['comments']; 
                  tep_db_free_result($orders_status_history_query);
      //orders status
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

          if($pay_orders_id_array[0] != '' && $payment_code != $pay_type_array[0]){ 
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id_array[0]."' order by date_added desc"); 
            while($orders_status_history_array = tep_db_fetch_array($orders_status_history_query)){
              if($orders_status_history_array['comments']!=''){
                $pay_info_array[0] = $orders_status_history_array['comments']; 
                break;
              }
            }
            tep_db_free_result($orders_status_history_query);
          }
          if($pay_orders_id_array[1] != '' &&  $payment_code != $pay_type_array[1]){ 
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id_array[1]."' order by date_added desc"); 
            while($orders_status_history_array = tep_db_fetch_array($orders_status_history_query)){
              if($orders_status_history_array['comments']!=''){
                $pay_info_array[1] = $orders_status_history_array['comments']; 
                break;
              }
            }
            tep_db_free_result($orders_status_history_query);
          }
          if($pay_orders_id_array[2] != '' &&  $payment_code != $pay_type_array[2]){ 
            $orders_status_history_query = tep_db_query("select comments from ". TABLE_ORDERS_STATUS_HISTORY ." where orders_id='".$pay_orders_id_array[2]."' order by date_added desc"); 
            while($orders_status_history_array = tep_db_fetch_array($orders_status_history_query)){
              if($orders_status_history_array['comments']!=''){
                $pay_info_array[2] = $orders_status_history_array['comments']; 
                break;
              }
            }
            tep_db_free_result($orders_status_history_query);
          }
          $pay_info_array[0] = $pay_info_array[0] == '' && $payment_code == $pay_type_array[0] ? $pay_comment : $pay_info_array[0];
          $pay_info_array[1] = $pay_info_array[1] == '' && $payment_code == $pay_type_array[1] ? $pay_comment : $pay_info_array[1];
          $pay_info_array[2] = $pay_info_array[2] == '' && $payment_code == $pay_type_array[2] ?  $pay_comment : $pay_info_array[2];
                  echo "\n".'<script language="javascript">'."\n"; 
                  echo '$(document).ready(function(){'."\n";

                  $cpayment->admin_show_payment_list($payment_code,$pay_info_array); 
                  echo '});'."\n";
                  echo '</script>'."\n";
      
                  if(!isset($selections)){
                    $selections = $cpayment->admin_selection();
                  } 
                  echo '<table>';
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
                          $field['message'] = ''; 
                        }else{
                          $field['message'] = ''; 
                        }
                     }else{
                        if(!$cpayment->admin_get_payment_buying_type(payment::changeRomaji($payment_code, 'code'),$field['title'])){
                          $field['message'] = '';
                        }
                     }
                     echo "<font color='red'>&nbsp;".$field['message']."</font>";
                     echo "</td>";
                     echo "</tr>";
                 } 
               }
               echo '</table>'; 
                  ?>
                  <?php echo EDIT_ORDERS_PAYMENT_METHOD_READ;?> 
                </td>
              </tr>
              <!-- End Payment Block -->
              <!-- Begin Trade Date Block -->
              <?php if (false) {?> 
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_FETCHTIME;?></b></td>
                <td class="main">
                  <input name='update_tori_torihiki_date' size='25' value='<?php echo $order->tori['date']; ?>'>
                  <span class="smalltext"><?php echo EDIT_ORDERS_FETCHTIME_READ;?></span>
                </td>
              </tr>
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_TORI_TEXT;?></b></td>
                <td class="main">
                  <input name='update_tori_torihiki_houhou' size='45' value='<?php echo $order->tori['houhou']; ?>'>
                  <?php echo EDIT_ORDERS_TORI_READ;?> 
                </td>
              </tr>
              <?php }?> 
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_ENSUREDATE;?></b></td>
                <td class="main">
                  <?php
                  $ensure_arr = explode(' ', $order->info['ensure_deadline']);  
                  if ($ensure_arr[0] != '0000-00-00') {
                    $update_ensure_array = explode('-', $ensure_arr[0]); 
                  } else {
                    $update_ensure_array = explode('-', date('Y-m-d', time())); 
                  }
                  ?>
                  <div style="float:left;"> 
                    <select name="update_ensure_year" id="update_ensure_year" onchange="change_ensure_date();">
                    <?php
                      $default_update_ensure_year = (isset($_POST['update_ensure_year']))?$_POST['update_ensure_year']:$update_ensure_array[0]; 
                      for ($f_num = 2006; $f_num <= 2050; $f_num++) {
                        echo '<option value="'.$f_num.'"'.(($default_update_ensure_year == $f_num)?' selected':'').'>'.$f_num.'</option>'; 
                      }
                    ?>
                    </select>
                    <select name="update_ensure_month" id="update_ensure_month" onchange="change_ensure_date();">
                    <?php
                      for ($f_num = 1; $f_num <= 12; $f_num++) {
                        $default_update_ensure_month = (isset($_POST['update_ensure_month']))?$_POST['update_ensure_month']:$update_ensure_array[1]; 
                        $tmp_update_ensure_month = sprintf('%02d', $f_num); 
                        echo '<option value="'.$tmp_update_ensure_month.'"'.(($default_update_ensure_month == $tmp_update_ensure_month)?' selected':'').'>'.$tmp_update_ensure_month.'</option>'; 
                      }
                    ?>
                    </select>
                    <select name="update_ensure_day" id="update_ensure_day" onchange="change_ensure_date();">
                    <?php
                      for ($f_num = 1; $f_num <= 31; $f_num++) {
                        $default_update_ensure_day = (isset($_POST['update_ensure_day']))?$_POST['update_ensure_day']:$update_ensure_array[2]; 
                        $tmp_update_ensure_day = sprintf('%02d', $f_num); 
                        echo '<option value="'.$tmp_update_ensure_day.'"'.(($default_update_ensure_day == $tmp_update_ensure_day)?' selected':'').'>'.$tmp_update_ensure_day.'</option>'; 
                      }
                    ?>
                    </select>
                  </div>
                  <div class="yui3-skin-sam yui3-g" style="overflow:hidden;">
                    
                    <input id='date_ensure_deadline' name='update_ensure_deadline' type='hidden' value='<?php echo ($ensure_arr[0] != '0000-00-00')?$ensure_arr[0]:date('Y-m-d', time()); ?>'>
                    <a href="javascript:void(0)" onclick="open_ensure_calendar();" class="dpicker"></a> 
                    <input type="hidden" name="toggle_ensure" value="0" id="toggle_ensure"> 
                    <div class="yui3-u" id="ensure_yui3">
                    <div id="ecalendar"></div>
                    </div>
                  </div>
                  <span class="smalltext"><?php //echo EDIT_ORDERS_FETCHTIME_READ;?></span>
                  <input type="hidden" name='update_tori_torihiki_date' size='25' value='<?php echo $order->tori['date']; ?>'>
                  <input type="hidden" name='update_tori_torihiki_houhou' size='45' value='<?php echo $order->tori['houhou']; ?>'>
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
                  <input type='hidden' id="h_deadline" name="h_deadline">
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
    $orders_products_query = tep_db_query("select * from " . TABLE_PREORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($oID) . "'");
    while ($orders_products = tep_db_fetch_array($orders_products_query)) {
    $order->products[$index] = array('qty' => $orders_products['products_quantity'],
                                     'name' => str_replace("'", "&#39;", $orders_products['products_name']),
                                     'model' => $orders_products['products_model'],
                                     'tax' => $orders_products['products_tax'],
                                     'price' => $orders_products['products_price'],
                                     'final_price' => $orders_products['final_price'],
                                     'products_id' => $orders_products['products_id'],
                                     'orders_products_id' => $orders_products['orders_products_id']);

    $subindex = 0;
    $attributes_query_string = "select * from " . TABLE_PREORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . tep_db_input($oID) . "' and orders_products_id = '" . (int)$orders_products['orders_products_id'] . "'";
    $attributes_query = tep_db_query($attributes_query_string);

    if (tep_db_num_rows($attributes_query)) {
    while ($attributes = tep_db_fetch_array($attributes_query)) {
      $order->products[$index]['attributes'][$subindex] = array(
          'id'              => $attributes['orders_products_attributes_id'],
          'option_info'     => @unserialize(stripslashes($attributes['option_info'])),
          'price'           => $attributes['options_values_price'],
          'option_item_id'  => $attributes['option_item_id'],
          'option_group_id' => $attributes['option_group_id']);
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
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CURRENICY; ?></td>
    <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_BEFORE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_AFTER; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_BEFORE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_AFTER; ?></td>
  </tr>
  
<?php
  for ($i=0; $i<sizeof($order->products); $i++) {
    $orders_products_id = $order->products[$i]['orders_products_id'];
    $op_info_str = '';
     if ($order->products[$i]['attributes'] && sizeof($order->products[$i]['attributes']) > 0) {
      $op_info_array = array();
      for ($i_num = 0; $i_num < sizeof($order->products[$i]['attributes']); $i_num++) {
        $op_info_array[] = $order->products[$i]['attributes'][$i_num]['id']; 
      }
      $op_info_str = implode('|||', $op_info_array);
     }else{
      $option_item_orders_sql = "select it.id,it.type item_type,it.option item_option,it.place_type as place_type from ".TABLE_PRODUCTS."
      p,".TABLE_OPTION_ITEM." it 
      where p.products_id = '".(int)$order->products[$i]['products_id']."' 
      and p.belong_to_option = it.group_id 
      and it.status = 1
      order by it.sort_num,it.title";
      $all_show_option_id_array = array();
      $option_item_orders_query = tep_db_query($option_item_orders_sql);
      while($show_option_rows_item = tep_db_fetch_array($option_item_orders_query)){
        if($show_option_rows_item['place_type'] == 0){
          $all_show_option_id_array[] = $show_option_rows_item['id'];
        }
      }
      $op_info_str = implode('|||',$all_show_option_id_array); 
     }
    $RowStyle = "dataTableContent";
    echo '    <tr class="dataTableRow">' . "\n" .
         '      <td class="' . $RowStyle . '" align="left" valign="top" width="6%">'
         . "<input type='hidden' name='update_products_real_quantity[$orders_products_id]' id='update_products_real_quantity_$orders_products_id' value='1'><input type='hidden' id='update_products_qty_$orders_products_id' value='" .  $order->products[$i]['qty'] . "'><input type='text' class='update_products_qty' id='update_products_new_qty_$orders_products_id' name='update_products[$orders_products_id][qty]' onkeyup='clearLibNum(this);recalc_preorder_price(\"".$oID."\", \"".$orders_products_id."\", \"1\", \"".$op_info_str."\");' size='2' value='" . $order->products[$i]['qty'] . "'>&nbsp;x</td>\n" . 
         '      <td class="' . $RowStyle . '">' . $order->products[$i]['name'] . "<input name='update_products[$orders_products_id][name]' size='64' id='update_products_name_$orders_products_id' type='hidden' value='" . $order->products[$i]['name'] . "'>\n" . 
       '      &nbsp;&nbsp;';
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
      where p.products_id = '".(int)$order->products[$i]['products_id']."' 
      and p.belong_to_option = it.group_id 
      and it.status = 1
      order by it.sort_num,it.title";
      $option_item_order_query = tep_db_query($option_item_order_sql);
      $item_type_array = array();
      $item_option_array = array();
      while($show_option_row_item = tep_db_fetch_array($option_item_order_query)){
        $all_show_option_id[] = $show_option_row_item['id'];
        $item_type_array[$show_option_row_item['id']] = $show_option_row_item['item_type'];
        $item_option_array[$show_option_row_item['id']] = $show_option_row_item['item_option'];
      }
      for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
        $all_show_option[$order->products[$i]['attributes'][$j]['option_item_id']] =
          $order->products[$i]['attributes'][$j];
      }
      echo '<div id="popup_window" class="popup_window"></div>';
      foreach($all_show_option_id as $t_item_id){
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
        $orders_products_attributes_id = $all_show_option[$t_item_id]['id'];
        if(is_array($all_show_option[$t_item_id]['option_info'])){
        $item_default_value = tep_parse_input_field_data($all_show_option[$t_item_id]['option_info']['value'], array("'"=>"&quot;")) == '' ? TEXT_UNSET_DATA : tep_parse_input_field_data($all_show_option[$t_item_id]['option_info']['value'], array("'"=>"&quot;"));
        echo '<br><div class="order_option_width">&nbsp;<i><div class="order_option_info"><div class="order_option_title"> - ' . tep_parse_input_field_data($all_show_option[$t_item_id]['option_info']['title'], array("'"=>"&quot;"))."<input type='hidden' class='option_input_width' name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][option]' value='" .  tep_parse_input_field_data($all_show_option[$t_item_id]['option_info']['title'], array("'"=>"&quot;")) . "'>: " . 
           '</div><div class="order_option_value">' . 
           "<a onclick='popup_window(this,\"".$item_type."\",\"".tep_parse_input_field_data($all_show_option[$t_item_id]['option_info']['title'], array("'"=>"&quot;"))."\",\"".$item_list."\");' href='javascript:void(0);'><u>".$item_default_value."</u></a><input type='hidden' class='option_input_width' name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][value]' value='" .  tep_parse_input_field_data($all_show_option[$t_item_id]['option_info']['value'], array("'"=>"&quot;"));
        //if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
        echo "'></div></div>";
        echo '<div class="order_option_price">';
        echo "<input type='text' size='9' name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][price]' value='".(int)$all_show_option[$t_item_id]['price']."' onkeyup=\"clearLibNum(this);recalc_preorder_price('".$oID."', '".$orders_products_id."', '1', '".$op_info_str."');\">"; 
        echo TEXT_MONEY_SYMBOL; 
        //if ($order->products[$i]['attributes'][$j]['price'] != '0') {
          //echo ' ('.$currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty']).')'; 
        //}
        echo '</div>'; 
        echo '</i></div>';
        }

      }
      
    } else {
      $all_show_option_id = array();
      $all_show_option = array();
      $option_item_order_sql = "select it.id,it.type item_type,it.option item_option,it.place_type as place_type from ".TABLE_PRODUCTS."
      p,".TABLE_OPTION_ITEM." it 
      where p.products_id = '".(int)$order->products[$i]['products_id']."' 
      and p.belong_to_option = it.group_id 
      and it.status = 1
      order by it.sort_num,it.title";
      $option_item_order_query = tep_db_query($option_item_order_sql);
      $item_type_array = array();
      $item_option_array = array();
      while($show_option_row_item = tep_db_fetch_array($option_item_order_query)){
        if($show_option_row_item['place_type'] == 0){
          $all_show_option_id[] = $show_option_row_item['id'];
          $item_type_array[$show_option_row_item['id']] = $show_option_row_item['item_type'];
          $item_option_array[$show_option_row_item['id']] = $show_option_row_item['item_option'];
        }
        $belong_to_option = $show_option_row_item['belong_to_option'];
      }
      $op_info_str = implode('|||',$all_show_option_id);
      echo '<input type="hidden" id="belong_to_option" name="belong_to_option" value="'.$belong_to_option.'">';
      echo '<div id="popup_window" class="popup_window"></div>';
      foreach($all_show_option_id as $t_item_id){
          $option_item_query = tep_db_query("select front_title,`option` as option_text,type,price from ". TABLE_OPTION_ITEM ." where id='".$t_item_id."'");
          $option_item_array = tep_db_fetch_array($option_item_query);
          tep_db_free_result($option_item_query);
        $option_default_value = TEXT_UNSET_DATA; 
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
        $orders_products_attributes_id = $all_show_option[$t_item_id]['id'];
        echo '<br><div class="order_option_width">&nbsp;<i><div class="order_option_info"><div class="order_option_title"> - ' . tep_parse_input_field_data($option_item_array['front_title'], array("'"=>"&quot;"))."<input type='hidden' class='option_input_width' name='new_update_products_op_title[$t_item_id]' value='" .  tep_parse_input_field_data($option_item_array['front_title'], array("'"=>"&quot;")) . "'>: " . 
           '</div><div class="order_option_value">' . 
           "<a onclick='popup_window(this,\"".$item_type."\",\"".tep_parse_input_field_data($option_item_array['front_title'], array("'"=>"&quot;"))."\",\"".$item_list."\");' href='javascript:void(0);'><u>".tep_parse_input_field_data($option_default_value, array("'"=>"&quot;"))."</u></a><input type='hidden' class='option_input_width' name='new_update_products_op_value[$t_item_id]' value='"; 
        echo "'></div></div>";
        echo '<div class="order_option_price">';
        echo "<input type='text' size='9' name='new_update_products_op_price[$t_item_id]' value='".(int)$option_item_array['price']."' onkeyup=\"clearLibNum(this);recalc_preorder_price('".$oID."', '".$orders_products_id."', 'true', '".$op_info_str."');\">"; 
        echo TEXT_MONEY_SYMBOL; 
        echo '</div>'; 
        echo '</i></div>';
 
      } 
    }
    echo '      </td>' . "\n" .
         '      <td class="' . $RowStyle . '">' . $order->products[$i]['model'] . "<input name='update_products[$orders_products_id][model]' size='12' type='hidden' value='" . $order->products[$i]['model'] . "'>" . '</td>' . "\n" .
         '      <td class="' . $RowStyle . '" align="right">' .  tep_display_tax_value($order->products[$i]['tax']) . "<input name='update_products[$orders_products_id][tax]' size='2' type='hidden' value='" . tep_display_tax_value($order->products[$i]['tax']) . "'>" .  '%</td>' . "\n";
    
    echo '<td class="'.$RowStyle.'" align="right"><input type="text" style="text-align:right;" class="once_pwd" name="update_products['.$orders_products_id.'][p_price]" size="9" value="'.tep_display_currency(number_format(abs($order->products[$i]['price']), 2)).'" onkeyup="clearLibNum(this);recalc_preorder_price(\''.$oID.'\', \''.$orders_products_id.'\', \'2\', \''.$op_info_str.'\');" >'.TEXT_MONEY_SYMBOL.'</td>'; 
    
    echo '<td class="' . $RowStyle . '" align="right">';
    //if ($ocertify->npermission == 7) {
      echo "<input type='hidden' style='text-align:right;' class='once_pwd' name='update_products[$orders_products_id][final_price]' size='9' value='" .  tep_display_currency(number_format(abs($order->products[$i]['final_price']),2)) .  "'" .' onkeyup="clearNoNum(this);recalc_preorder_price(\''.$oID.'\', \''.$orders_products_id.'\', \'3\', \''.$op_info_str.'\');" >';
    //} else {
      //echo "<input type='hidden' name='update_products[$orders_products_id][final_price]' value='" .  tep_display_currency(number_format(abs($order->products[$i]['final_price']),2)) .  "'" .'>'.tep_display_currency(number_format(abs($order->products[$i]['final_price']),2));
    //}
    echo '<input type="hidden" name="op_id_'.$orders_products_id.'" 
      value="'.tep_get_pre_product_by_op_id($orders_products_id).'"><div id="update_products['.$orders_products_id.'][final_price]">'; 
    if ($order->products[$i]['final_price'] < 0) {
      echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    } else {
      echo $currencies->format($order->products[$i]['final_price'], true, $order->info['currency'], $order->info['currency_value']);
    }
    echo '</div></td>' . "\n" . 
         '      <td class="' . $RowStyle . '" align="right">';
    echo '<div id="update_products['.$orders_products_id.'][a_price]">'; 
    if ($order->products[$i]['final_price'] < 0) {
      $a_price_str = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    } else {
      $a_price_str = $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']);
    }
    echo $a_price_str; 
    echo '</div>'; 
    echo '</td>' . "\n" . 
         '      <td class="' . $RowStyle . '" align="right">';
    echo '<div id="update_products['.$orders_products_id.'][b_price]">'; 
    if ($order->products[$i]['final_price'] < 0) {
      $b_price_str = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    } else {
      $b_price_str = $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']);
    }
    echo $b_price_str; 
    echo '</div>'; 
    echo '</td>' . "\n" . 
         '      <td class="' . $RowStyle . '" align="right">';
    echo '<div id="update_products['.$orders_products_id.'][c_price]">'; 
    if ($order->products[$i]['final_price'] < 0) {
      $c_price_str = '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
    } else {
      $c_price_str = $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']);
    }
    echo '<b>'.$c_price_str.'</b>'; 
    echo '</div>'; 
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
              <td valign="top"><?php //echo "<span class='smalltext'>" .  HINT_DELETE_POSITION . EDIT_ORDERS_ADD_PRO_READ."</span>"; ?></td>
              <td align="right"><?php //echo '<a href="' . $PHP_SELF . '?oID=' . $oID . '&action=add_product&step=1">' . tep_html_element_button(ADDING_TITLE) . '</a>'; ?></td>
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
    <td class="dataTableHeadingContent" align="left" width="75%"><?php echo TABLE_HEADING_FEE_MUST?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_MODULE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_AMOUNT; ?></td>
    <td class="dataTableHeadingContent"width="1"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
  </tr>
<?php
  // Override order.php Class's Field Limitations
  $totals_query = tep_db_query("select * from " . TABLE_PREORDERS_TOTAL . " where orders_id = '" . tep_db_input($oID) . "' order by sort_order");
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
    $TotalsArray[] = array("Name" => "          ", "Price" => "", "Class" => "ot_custom", "TotalID" => "0");
  }
  
  array_pop($TotalsArray);
  $sum_num = count($TotalsArray)-1;
  $show_num = 0;
  $ot_custom_num = 0;
  foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
    if(trim($TotalDetails['Name']) != '' && $TotalDetails['Class'] == 'ot_custom'){
      $ot_custom_num++;
    }
  }
  foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
    if(trim($TotalDetails['Name']) == '' && $TotalDetails['Class'] == 'ot_custom' && $ot_custom_num >= 2){
       unset($TotalsArray[$TotalIndex]);
    }
  }
  $sum_array = array_keys($TotalsArray);
  array_pop($sum_array);
  $show_num = end($sum_array);
  foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
    $TotalStyle = "smallText";
    if ($TotalDetails["Class"] == "ot_total") {
      echo '  <tr id="add_option_total">' . "\n" .
           '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_OTTOTAL_READ.'</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . $TotalDetails["Name"] . '</b></td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b><div id="ot_total_id">';
                if($TotalDetails["Price"] >= 0 ){
                  echo $currencies->ot_total_format($TotalDetails["Price"], true,
                    $order->info['currency'], $order->info['currency_value']);
                }else{
                  echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL ,'', $currencies->ot_total_format($TotalDetails["Price"], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
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
                  echo $currencies->ot_total_format($TotalDetails["Price"], true,
                    $order->info['currency'], $order->info['currency_value']);
                }else{
                  echo '<font color="#ff0000">'.str_replace(TEXT_MONEY_SYMBOL ,'', $currencies->format($TotalDetails["Price"], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.TEXT_MONEY_SYMBOL;
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
           '    <td align="right" class="' . $TotalStyle . '"><b><div id="handle_fee_id">' . $currencies->format($order->info["code_fee"]) . '</div></b><input type="hidden" name="payment_code_fee" value="'.$order->info["code_fee"].'">' . 
                '</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
           '  </tr>' . "\n";
    } elseif ($TotalDetails["Class"] == "ot_tax") {
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
      if ($customer_guest['customers_guest_chk'] == 0) { //会員
        $current_point = $customer_point['point'] + $TotalDetails["Price"];
        echo '  <tr>' . "\n" .
             '    <td colspan="4">' . "<input type='hidden' name='update_totals[$TotalIndex][value]' size='6' value='" . $TotalDetails["Price"] . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][title]' size='" . $max_length . "' value='" . trim($TotalDetails["Name"]) . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][class]' value='" . $TotalDetails["Class"] . "'>" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . 
                "<input type='hidden' name='before_point' value='" . $TotalDetails["Price"] . "'>" . 
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
      $button_add = $TotalIndex == $show_num ? '<INPUT type="button" id="button_add" value="'.TEXT_BUTTON_ADD.'" onClick="add_option();"><input type="hidden" id="button_add_id" value="'.$sum_num.'">&nbsp;' : '';
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
<span class='smalltext'><?php echo EDIT_ORDERS_PRICE_CONSTRUCT_READ;?></span>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
  <!-- End Order Total Block -->
  <!-- Begin Update Block -->
<!-- Improvement: more "Update" buttons (Michel Haase, 2005-02-18) -->   
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
    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>
    <td class="dataTableHeadingContent" align="left"><?php echo TEXT_OPERATE_USER; ?></td>
  </tr>
<?php
$orders_history_query = tep_db_query("select * from " . TABLE_PREORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' order by date_added");
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
    echo '    <td class="dataTableHeadingContent" align="left" width="10">&nbsp;</td>' . "\n" .
         '    <td class="smallText" align="left">' . $orders_history['user_added'] . '</td>' . "\n";
    echo '  </tr>' . "\n";
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
    <td class="dataTableHeadingContent" align="left"><?php echo TABLE_HEADING_COMMENTS; ?></td>
  </tr>
  <tr>
    <td valign="top" width="40%">
      <table border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td class="main" width="82" style="min-width:45px;"><b><?php echo ENTRY_STATUS; ?></b></td>
          <td class="main"><?php 
          $is_nyuuka_raw = tep_db_query("select * from ".TABLE_PREORDERS_STATUS." where is_nyuuka = '1' order by orders_status_id asc limit 1"); 
          $is_nyuuka_res = tep_db_fetch_array($is_nyuuka_raw);
          if ($is_nyuuka_res) {
            $sel_nyuuka_id = $is_nyuuka_res['orders_status_id']; 
          } else {
            $sel_nyuuka_id = 1; 
          }
          echo tep_draw_pull_down_menu('status', $orders_statuses, $sel_nyuuka_id, 'id="status" onchange="check_prestatus();" style="width:80px;"'); ?>
          <input type="hidden" name="isruhe" id="isruhe" value=""> 
          </td>
        </tr>
        <tr>
          <td class="main"><b><?php echo EDIT_ORDERS_SEND_MAIL_TEXT;?></b></td>
          <td class="main"><table bgcolor="red" cellspacing="5"><tr><td><?php echo tep_draw_checkbox_field('notify', '', true); ?></td></tr></table></td>
        </tr>
        <?php if($CommentsWithStatus) { ?>
        <tr>
          <td class="main"><b><?php echo EDIT_ORDERS_RECORD_TEXT;?></b></td>
          <td class="main"><?php echo tep_draw_checkbox_field('notify_comments', '', false); ?>&nbsp;&nbsp;<b style="color:#FF0000;"><?php echo EDIT_ORDERS_RECORD_READ;?></b></td>
        </tr>
        <tr>
          <td class="main" valign="top"><b><?php echo TABLE_HEADING_COMMENTS;?>:</b></td>
          <td class="main"><?php echo tep_draw_textarea_field('comments_text', 'hard', '74', '5', '', 'style="font-family:monospace; font-size:12px; width:100%;"');?></td> 
        </tr>
        <?php } ?>
      </table>
    </td>
    <td class="main" width="15%">&nbsp;</td>
    <td class="main">
    <?php
      $ma_se = "select * from ".TABLE_PREORDERS_MAIL." where orders_status_id = '".$sel_nyuuka_id."'"; 
      $mail_sele = tep_db_query($ma_se); 
      $mail_sql = tep_db_fetch_array($mail_sele); 
    ?>
    <?php echo '<b>'.ENTRY_EMAIL_TITLE.'</b>'.tep_draw_input_field('etitle', $mail_sql['orders_status_title'],' style="width:230px;" id="mail_title"');?> 
    <br> 
    <br> 
    <textarea style="font-family:monospace; font-size:12px; width:400px;" name="comments" wrap="hard" rows="30" cols="74"><?php echo str_replace('${ORDER_A}', preorders_a($order->info['orders_id']), $mail_sql['orders_status_mail']);?></textarea> 
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
              <td class="main" bgcolor="#FAEDDE"><b><?php echo EDIT_ORDERS_FINAL_CONFIRM_TEXT;?></b>&nbsp;<?php echo HINT_PRESS_UPDATE; ?></td>
              <td class="main" bgcolor="#FBE2C8" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FFCC99" width="10">&nbsp;</td>
              <td class="main" bgcolor="#F8B061" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF9933" width="120" align="center">
              <?php
              foreach($orders_statuses as $o_status){
                echo '<input type="hidden" id="confrim_mail_title_'.$o_status['id'].
                  '" value="'.$mo[$o_status['id']][0].'">';
              }
              ?>
              <?php echo tep_html_element_button(TEXT_FOOTER_CHECK_SAVE, 'onclick="submit_order_check('.$order->products[0]['products_id'].','.$order->products[0]['orders_products_id'].');"');?> 
              </td>
          </tr>
          </table>
    </td>
      </tr>
    <tr>
      <td>
      <?php echo EDIT_PREORDERS_FINAL_CONFIRM_TEMPLATE;?> 
      </td>
    </tr>
  <!-- End of Update Block -->
<?php
}
if($action == "add_product")
{
?>
      <tr>
        <td width="100%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo ADDING_TITLE; ?> (Nr. <?php echo $oID; ?>)</td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
              <td class="pageHeading" align="right"><?php echo '<a href="' .  tep_href_link(FILENAME_FINAL_PREORDERS, tep_get_all_get_params(array('action'))) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td>
            </tr>
          </table>
        </td>
      </tr>

<?php 
  // ############################################################################
  //   Get List of All Products
  // ############################################################################

    //$result = tep_db_query("SELECT products_name, p.products_id, x.categories_name, ptc.categories_id FROM " . TABLE_PRODUCTS . " p LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id=p.products_id LEFT JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " ptc ON ptc.products_id=p.products_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " cd ON cd.categories_id=ptc.categories_id LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION . " x ON x.categories_id=ptc.categories_id ORDER BY categories_id");
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
      if(!IsSet($add_product_categories_id))
      $add_product_categories_id = 0;

      if(!IsSet($add_product_products_id))
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
      print "<td class='dataTableContent'>" . ADDPRODUCT_TEXT_STEP2 . "</td>\n";
      print "</form></tr>\n";
      print "<tr><td colspan='3'>&nbsp;</td></tr>\n";
    }

    // Step 3: Choose Options
    if(($step > 2) && ($add_product_products_id > 0))
    {
      // Get Options for Products
      $result = tep_db_query("SELECT * FROM " . TABLE_PRODUCTS_ATTRIBUTES . " pa LEFT JOIN " . TABLE_PRODUCTS_OPTIONS . " po ON po.products_options_id=pa.options_id LEFT JOIN " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov ON pov.products_options_values_id=pa.options_values_id WHERE products_id='$add_product_products_id' and po.language_id = '" . (int)$languages_id . "'");
      
      // Skip to Step 4 if no Options
      if(tep_db_num_rows($result) == 0)
      {
        print "<tr class=\"dataTableRow\">\n";
        print "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 3: </b></td>\n";
        print "<td class='dataTableContent' valign='top' colspan='2'><i>" . ADDPRODUCT_TEXT_OPTIONS_NOTEXIST . "</i></td>\n";
        print "</tr>\n";
        $step = 4;
      }
      else
      {
        while($row = tep_db_fetch_array($result))
        {
          extract($row,EXTR_PREFIX_ALL,"db");
          $Options[$db_products_options_id] = $db_products_options_name;
          $ProductOptionValues[$db_products_options_id][$db_products_options_values_id] = $db_products_options_values_name;
        }
      
        print "<tr class=\"dataTableRow\"><form action='$PHP_SELF?oID=$oID&action=$action' method='POST'>\n";
        print "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 3: </b></td><td class='dataTableContent' valign='top'>";
        foreach($ProductOptionValues as $OptionID => $OptionValues)
        {
          $OptionOption = "<b>" . $Options[$OptionID] . "</b> - <select name='add_product_options[$OptionID]'>";
          foreach($OptionValues as $OptionValueID => $OptionValueName)
          {
          $OptionOption .= "<option value='$OptionValueID'> $OptionValueName\n";
          }
          $OptionOption .= "</select><br>\n";
          
          if(IsSet($add_product_options))
          $OptionOption = str_replace("value='" . $add_product_options[$OptionID] . "'","value='" . $add_product_options[$OptionID] . "' selected",$OptionOption);
          
          print $OptionOption;
        }   
        print "</td>";
        print "<td class='dataTableContent' align='center'><input type='submit' value='" . ADDPRODUCT_TEXT_OPTIONS_CONFIRM . "'>";
        print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
        print "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
        print "<input type='hidden' name='step' value='4'>";
        print "</td>\n";
        print "</form></tr>\n";
      }

      print "<tr><td colspan='3'>&nbsp;</td></tr>\n";
    }

    // Step 4: Confirm
    if($step > 3)
    {
      print "<tr class=\"dataTableRow\"><form action='$PHP_SELF?oID=$oID&action=$action' method='POST'>\n";
      print "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 4: </b></td>";
      print "<td class='dataTableContent' valign='top'>" .  ADDPRODUCT_TEXT_CONFIRM_QUANTITY . "<input name='add_product_quantity' size='2' value='1'>&nbsp;".EDIT_ORDERS_NUM_UNIT."&nbsp;&nbsp;&nbsp;".EDIT_ORDERS_PRO_DUMMY_NAME."&nbsp;<input name='add_product_character' size='20' value=''></td>";
      print "<td class='dataTableContent' align='center'><input type='submit' value='" . ADDPRODUCT_TEXT_CONFIRM_ADDNOW . "'>";

      if(IsSet($add_product_options))
      {
        foreach($add_product_options as $option_id => $option_value_id)
        {
          print "<input type='hidden' name='add_product_options[$option_id]' value='$option_value_id'>";
        }
      }
      print "<input type='hidden' name='add_product_categories_id' value='$add_product_categories_id'>";
      print "<input type='hidden' name='add_product_products_id' value='$add_product_products_id'>";
      print "<input type='hidden' name='step' value='5'>";
      print "</td>\n";
      print "</form></tr>\n";
    }
    
    print "</table></td></tr>\n";
}  
?>
    </table></div></div></td>
<!-- body_text_eof //-->
  </tr>
</table>
</form>
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
