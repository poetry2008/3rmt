<?php
/*
   $Id$
   
   编辑订单
*/

  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_EDIT_ORDERS);

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies(2);

  include(DIR_WS_CLASSES . 'preorder.php');

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
  // ポイントを取得する
  $customer_point_query = tep_db_query("
      select point 
      from " . TABLE_CUSTOMERS . " 
      where customers_id = '" . $order->customer['id'] . "'");
  $customer_point = tep_db_fetch_array($customer_point_query);
  // ゲストチェック
  $customer_guest_query = tep_db_query("
      select customers_guest_chk 
      from " . TABLE_CUSTOMERS . " 
      where customers_id = '" . $order->customer['id'] . "'");
  $customer_guest = tep_db_fetch_array($customer_guest_query);

  if (tep_not_null($action)) {
    switch ($action) {
      
  // 1. UPDATE ORDER ###############################################################################################
  case 'update_order':
    $oID = tep_db_prepare_input($_GET['oID']);
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
    if (isset($_POST['update_predate'])) { //日時が有効かチェック
      if (!preg_match('/^(\d\d\d\d)-(\d\d)-(\d\d) (\d\d):(\d\d):(\d\d)$/', $_POST['update_predate'], $m)) { // check the date format
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
      payment_method = '" . tep_db_input($_POST['payment_method']) . "',
      torihiki_date = '" . tep_db_input($update_tori_torihiki_date) . "',
      torihiki_houhou = '" . tep_db_input($update_tori_torihiki_houhou) . "',
      predate = '" . tep_db_input($_POST['update_predate']) . "',
      ensure_deadline = '" . tep_db_input($_POST['update_ensure_deadeline']) . "',
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
          products_character = '" . mysql_real_escape_string($products_details["character"]) . "',
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
          $Query = "update " . TABLE_PREORDERS_PRODUCTS_ATTRIBUTES . " set
              products_options = '" . $attributes_details["option"] . "',
              products_options_values = '" . $attributes_details["value"] . "'
              where orders_products_attributes_id = '$orders_products_attributes_id';";
          tep_db_query($Query);
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
        tep_db_query("update " . TABLE_CUSTOMERS . " set point = point - " . $point_difference . " where customers_id = '" . $order->customer['id'] . "'"); 
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
  
  $handle_fee = prenew_calc_handle_fee($_POST['payment_method'], $newtotal, $oID);
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
    tep_order_status_change($oID,$status);
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
            $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['orders_products_attributes_id'];
            $products_ordered_mail .=  "\t" . tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option'], array("'"=>"&quot;")) . '　　　　　：';
            $products_ordered_mail .= tep_parse_input_field_data($order->products[$i]['attributes'][$j]['value'], array("'"=>"&quot;")) . "\n";
          }
        }

        $products_ordered_mail .= "\t" . FORDERS_MAIL_PRODUCTS_NUM .  $order->products[$i]['qty'] . EDIT_ORDERS_NUM_UNIT . tep_get_full_count2($order->products[$i]['qty'], $order->products[$i]['id']) . "\n";
        $products_ordered_mail .= "\t" . FORDERS_MAIL_PRODUCTS_PRICE . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax']) . "\n"; 
        $products_ordered_mail .= "\t" . FORDERS_MAIL_PRODUCTS_TOTAL_MONEY . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . "\n";
        $products_ordered_mail .= "\t" . FORDERS_MAIL_PRODUCTS_CHARACTER . (EMAIL_USE_HTML === 'true' ? htmlspecialchars($order->products[$i]['character']) : $order->products[$i]['character']) . "\n";
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
      
      if ($customer_guest['customers_guest_chk'] != 9) {
        if ($status == 32) {
          $site_url_raw = tep_db_query("select * from sites where id = '".$order->info['site_id']."'"); 
          $site_url_res = tep_db_fetch_array($site_url_raw); 
          $change_preorder_url = $site_url_res['url'].'/change_preorder.php?pid='.$oID; 
          $email .= "\n\n".$change_preorder_url; 
        }
      tep_mail($check_status['customers_name'], $check_status['customers_email_address'], FORDERS_MAIL_UPDATE_CONTENT_FINISH.'【' . get_configuration_by_site_id('STORE_NAME', $order->info['site_id']) . '】', $email, get_configuration_by_site_id('STORE_OWNER', $order->info['site_id']), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS', $order->info['site_id']),$order->info['site_id']);
      } 
      tep_mail(get_configuration_by_site_id('STORE_OWNER', $order->info['site_id']), get_configuration_by_site_id('SENTMAIL_ADDRESS', $order->info['site_id']), FORDERS_MAIL_UPDATE_CONTENT_MAIL.'【' . get_configuration_by_site_id('STORE_NAME', $order->info['site_id']) . '】', $email, $check_status['customers_name'], $check_status['customers_email_address'],$order->info['site_id']);
      $customer_notified = '1';
    }
    tep_db_query("insert into " . TABLE_PREORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" . tep_db_input($customer_notified) . "', '" . mysql_real_escape_string($notify_comments) . "')");
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
      /**
      echo "<pre>";
      print_r($_GET);
      print_r($_POST);
      echo "</pre>";
      exit;
      /**/
      
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
        products_character = '" . mysql_real_escape_string($add_product_character) . "',
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
      $handle_fee = prenew_calc_handle_fee($order->info['payment_method'], $newtotal, $oID);
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
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
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
    $order = new preorder($oID);
?>
        <tr>
          <td width="100%">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
                <td class="pageHeading" align="right">
    <?php echo '<a href="' . tep_href_link(FILENAME_PREORDERS, tep_get_all_get_params()) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?>
                </td>
              </tr>
            </table>
            <?php echo tep_draw_separator(); ?>
          </td>
        </tr> 
        <tr><?php echo tep_draw_form('edit_order', FILENAME_FINAL_PREORDERS, tep_get_all_get_params(array('action','paycc')) . 'action=update_order', 'post', 'onSubmit="return presubmitChk()"'); ?>
          
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
                  <input name="update_customer_name" size="25" value="<?php echo tep_html_quotes($order->customer['name']); ?>">
                  <span class="smalltext"><?php echo EDIT_ORDERS_CUSTOMER_NAME_READ;?></span>
                </td>
              </tr>
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_EMAIL;?></b></td>
                <td class="main"><input name="update_customer_email_address" size="45" value="<?php echo $order->customer['email_address']; ?>"></td>
              </tr>
              <!-- End Addresses Block -->
              <!-- Begin Payment Block -->
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_PAYMENT_METHOD;?></b></td>
                <td class="main">
                  <?php echo tep_payment_method_menu($order->info['payment_method']);?>
                  <?php echo EDIT_ORDERS_PAYMENT_METHOD_READ;?> 
                </td>
              </tr>
              <!-- End Payment Block -->
              <!-- Begin Trade Date Block -->
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
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_ENSUREDATE;?></b></td>
                <td class="main">
                  <input name='update_predate' size='25' value='<?php echo $order->info['predate']; ?>'>
                  <span class="smalltext"><?php echo EDIT_ORDERS_FETCHTIME_READ;?></span>
                </td>
              </tr>
              <tr>
                <td class="main" valign="top"><b><?php echo EDIT_ORDERS_ENSUREDATE_TEXT;?></b></td>
                <td class="main">
                  <input name='update_ensure_deadeline' size='45' value='<?php echo $order->info['ensure_deadline']; ?>'>
                  <?php echo EDIT_ORDERS_TORI_READ;?> 
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
                                     'character' => $orders_products['products_character'],
                                     'tax' => $orders_products['products_tax'],
                                     'price' => $orders_products['products_price'],
                                     'final_price' => $orders_products['final_price'],
                                     'orders_products_id' => $orders_products['orders_products_id']);

    $subindex = 0;
    $attributes_query_string = "select * from " . TABLE_PREORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . tep_db_input($oID) . "' and orders_products_id = '" . (int)$orders_products['orders_products_id'] . "'";
    $attributes_query = tep_db_query($attributes_query_string);

    if (tep_db_num_rows($attributes_query)) {
    while ($attributes = tep_db_fetch_array($attributes_query)) {
      $order->products[$index]['attributes'][$subindex] = array('option' => $attributes['products_options'],
                                                                'value' => $attributes['products_options_values'],
                                                                'prefix' => $attributes['price_prefix'],
                                                                'price' => $attributes['options_values_price'],
                                                                'orders_products_attributes_id' => $attributes['orders_products_attributes_id']);
      $subindex++;
      }
    }
    $index++;
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
  </tr>
  
<?php
  for ($i=0; $i<sizeof($order->products); $i++) {
    $orders_products_id = $order->products[$i]['orders_products_id'];
    $RowStyle = "dataTableContent";
    echo '    <tr class="dataTableRow">' . "\n" .
         '      <td class="' . $RowStyle . '" align="left" valign="top" width="20">' . "<input type='hidden' name='update_products_real_quantity[$orders_products_id]' id='update_products_real_quantity_$orders_products_id' value='1'><input type='hidden' id='update_products_qty_$orders_products_id' value='" . $order->products[$i]['qty'] . "'><input class='update_products_qty' id='update_products_new_qty_$orders_products_id' name='update_products[$orders_products_id][qty]' size='2' value='" . $order->products[$i]['qty'] . "'>&nbsp;x</td>\n" . 
         '      <td class="' . $RowStyle . '">' . $order->products[$i]['name'] . "<input name='update_products[$orders_products_id][name]' size='64' id='update_products_name_$orders_products_id' type='hidden' value='" . $order->products[$i]['name'] . "'>\n" . 
       '      &nbsp;&nbsp;'.EDIT_ORDERS_DUMMY_TITLE. "<input type='hidden' name='dummy' value='あいうえお眉幅'><input name='update_products[$orders_products_id][character]' size='20' value=\"" . htmlspecialchars($order->products[$i]['character']) . "\">";
    // Has Attributes?
    if ($order->products[$i]['attributes'] && sizeof($order->products[$i]['attributes']) > 0) {
      for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
        $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['orders_products_attributes_id'];
        echo '<br><nobr><small>&nbsp;<i> - ' . 
           "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][option]' size='10' value='" . tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option'], array("'"=>"&quot;")) . "'>" . 
           ': ' . 
           "<input name='update_products[$orders_products_id][attributes][$orders_products_attributes_id][value]' size='35' value='" . tep_parse_input_field_data($order->products[$i]['attributes'][$j]['value'], array("'"=>"&quot;"));
        //if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
        echo "'>";
        echo '</i></small></nobr>';
      }
    }
    
    echo '      </td>' . "\n" .
         '      <td class="' . $RowStyle . '">' . $order->products[$i]['model'] . "<input name='update_products[$orders_products_id][model]' size='12' type='hidden' value='" . $order->products[$i]['model'] . "'>" . '</td>' . "\n" .
         '      <td class="' . $RowStyle . '" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . "<input name='update_products[$orders_products_id][tax]' size='2' type='hidden' value='" . tep_display_tax_value($order->products[$i]['tax']) . "'>" . '%</td>' . "\n" .
         '      <td class="' . $RowStyle . '" align="right">';
    //if ($ocertify->npermission == 7) {
      echo "<input class='once_pwd' name='update_products[$orders_products_id][final_price]' size='9' value='" .  tep_display_currency(number_format(abs($order->products[$i]['final_price']),2)) .  "'" .' onkeyup="clearNoNum(this)" >';
    //} else {
      //echo "<input type='hidden' name='update_products[$orders_products_id][final_price]' value='" .  tep_display_currency(number_format(abs($order->products[$i]['final_price']),2)) .  "'" .'>'.tep_display_currency(number_format(abs($order->products[$i]['final_price']),2));
    //}
    echo '<input type="hidden" name="op_id_'.$orders_products_id.'" 
         value="'.tep_get_pre_product_by_op_id($orders_products_id).'">' . '</td>' . "\n" . 
         '      <td class="' . $RowStyle . '" align="right">' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']), true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" . 
         '      <td class="' . $RowStyle . '" align="right">' . $currencies->format($order->products[$i]['final_price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" . 
         '      <td class="' . $RowStyle . '" align="right"><b>' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</b></td>' . "\n" . 
         '    </tr>' . "\n";
  }
  ?>
</table>

        </td>
      <tr>
        <td>
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td valign="top"><?php echo "<span class='smalltext'>" .  HINT_DELETE_POSITION . EDIT_ORDERS_ADD_PRO_READ."</span>"; ?></td>
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

<table width="100%" border="0" cellspacing="0" cellpadding="2" class="dataTableRow">
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
  foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
    $TotalStyle = "smallText";
    if ($TotalDetails["Class"] == "ot_total") {
      echo '  <tr>' . "\n" .
           '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_OTTOTAL_READ.'</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . $TotalDetails["Name"] . '</b></td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>';
                if($TotalDetails["Price"] >= 0 ){
                  echo $currencies->ot_total_format($TotalDetails["Price"], true,
                    $order->info['currency'], $order->info['currency_value']);
                }else{
                  echo "<font color='red'>";
                  echo $currencies->ot_total_format($TotalDetails["Price"], true,
                    $order->info['currency'], $order->info['currency_value']);
                  echo "</font>";
                }
                echo '</b>' . 
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
           '    <td align="right" class="' . $TotalStyle . '"><b>';
                if($TotalDetails["Price"]>=0){
                  echo $currencies->ot_total_format($TotalDetails["Price"], true,
                    $order->info['currency'], $order->info['currency_value']);
                }else{
                  echo "<font color='red'>";
                  echo $currencies->format($TotalDetails["Price"], true,
                    $order->info['currency'], $order->info['currency_value']);
                  echo "</font>";
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
           '    <td align="right" class="' . $TotalStyle . '"><b>' . $currencies->format($order->info["code_fee"]) . '</b><input type="hidden" name="payment_code_fee" value="'.$order->info["code_fee"].'">' . 
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
             '    <td align="left" class="' . $TotalStyle .  '">'.EDIT_ORDERS_TOTAL_DETAIL_READ.'</td>' . 
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
              <td class="main" bgcolor="#FAEDDE" height="25"><?php echo EDIT_ORDERS_CONFIRMATION_READ;?></td>
              <td class="main" bgcolor="#FBE2C8" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FFCC99" width="10">&nbsp;</td>
              <td class="main" bgcolor="#F8B061" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF9933" width="120" align="center">
<?php if (tep_is_oroshi($order->customer['id'])) { ?>
                <INPUT type="button" value="<?php echo EDIT_ORDERS_CONFIRM_BUTTON;?>" onClick="pre_update_price()">
<?php } else { ?>
                <INPUT type="button" value="<?php echo EDIT_ORDERS_CONFIRM_BUTTON;?>" onClick="pre_update_price2()">
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
    <td valign="top">
      <table border="0" cellspacing="0" cellpadding="2">
        <tr>
          <td class="main"><b><?php echo ENTRY_STATUS; ?></b></td>
          <td class="main"><?php echo tep_draw_pull_down_menu('status', $orders_statuses, '16'); ?></td>
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
        <?php } ?>
      </table>
    </td>
    <td class="main" width="10">&nbsp;</td>
    <td class="main">
    <?php echo EDIT_ORDERS_RECORD_ARTICLE;?><br>
    <?php
    if($CommentsWithStatus) {
  
  //<textarea style="font-family:monospace;font-size:x-small" name="comments" wrap="hard" rows="30" cols="74"></textarea>
  
      echo tep_draw_textarea_field('comments', 'hard', '74', '5', isset($order->info['comments'])?$order->info['comments']:'');
  //    echo tep_draw_textarea_field('comments', 'soft', '40', '5');
    } else {
      echo tep_draw_textarea_field('comments', 'hard', '74', '5', isset($order->info['comments'])?$order->info['comments']:'');
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
              <td class="main" bgcolor="#FAEDDE"><b><?php echo EDIT_ORDERS_FINAL_CONFIRM_TEXT;?></b>&nbsp;<?php echo HINT_PRESS_UPDATE; ?></td>
              <td class="main" bgcolor="#FBE2C8" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FFCC99" width="10">&nbsp;</td>
              <td class="main" bgcolor="#F8B061" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF9933" width="120" align="center"><?php echo tep_html_element_submit(IMAGE_UPDATE); ?></td>
          </tr>
          </table>
    </td>
      </tr>
    <tr>
      <td>
      <?php echo EDIT_ORDERS_FINAL_CONFIRM_TEMPLATE;?> 
      </td>
    </tr>
  <!-- End of Update Block -->
      </form>
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
      print "<td class='dataTableContent' valign='top'>" .  ADDPRODUCT_TEXT_CONFIRM_QUANTITY . "<input name='add_product_quantity' size='2' value='1'>&nbsp;個&nbsp;&nbsp;&nbsp;".EDIT_ORDERS_PRO_DUMMY_NAME."&nbsp;<input type='hidden' name='dummy' value='あいうえお眉幅'><input name='add_product_character' size='20' value=''></td>";
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
