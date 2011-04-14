<?php
/*
   $Id$
   
   创建订单
*/

  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');

  include(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/' . FILENAME_EDIT_ORDERS);
  require(DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_EDIT_ORDERS);

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies(2);

  include(DIR_WS_CLASSES . 'order.php');

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
  $order = $_SESSION['create_order2']['orders'];
  // ポイントを取得する
  $customer_point_query = tep_db_query("
      select point 
      from " . TABLE_CUSTOMERS . " 
      where customers_id = '" . $order['customer_id'] . "'");
  $customer_point = tep_db_fetch_array($customer_point_query);
  // ゲストチェック
  $customer_guest_query = tep_db_query("
      select customers_guest_chk 
      from " . TABLE_CUSTOMERS . " 
      where customers_id = '" . $order['customer_id'] . "'");
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
             !$_SESSION['create_order2']['orders'] 
          || !$_SESSION['create_order2']['orders']['orders_id'] 
          || !$_SESSION['create_order2']['orders']['customers_id']
        ) {
          echo 'error';
        }
        exit;
        break;
  // 1. UPDATE ORDER ###############################################################################################
  case 'update_order':
/*
    echo "<pre>";
    print_r($_POST);
    exit;
*/
    $oID = tep_db_prepare_input($_GET['oID']);
    $order = $_SESSION['create_order2']['orders'];
    //$status = '31'; // 初期値
    //$goods_check = $order_query;
    
    /*
    if (tep_db_num_rows($goods_check) == 0) {
      $messageStack->add('商品が追加されていません。', 'error');
      $action = 'edit';
      break;
    }
    */
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

    if ($update_totals) 
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
    $order_updated = true;

    $check_status_query = tep_db_query("select customers_name, customers_email_address, orders_status, date_purchased from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
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
      
      $_SESSION['create_order2']['orders_products'][$orders_products_id]['products_model'] = $products_details["model"];
      $_SESSION['create_order2']['orders_products'][$orders_products_id]['products_name'] = str_replace("'", "&#39;", $products_details["name"]);
      $_SESSION['create_order2']['orders_products'][$orders_products_id]['products_character'] = mysql_real_escape_string($products_details["character"]);
      $_SESSION['create_order2']['orders_products'][$orders_products_id]['final_price'] = (tep_get_bflag_by_product_id((int)$order['products_id']) ? 0 - $products_details["final_price"] : $products_details["final_price"]);
      $_SESSION['create_order2']['orders_products'][$orders_products_id]['products_tax'] = $products_details["tax"];
      $_SESSION['create_order2']['orders_products'][$orders_products_id]['products_quantity'] = $products_details["qty"];
      
  
// Update Tax and Subtotals: please choose sum WITH or WITHOUT tax, but activate only ONE version ;-)
  
// Correction tax calculation (Michel Haase, 2005-02-18)
// -> correct calculation, but why there is a division by 20 and afterwards a mutiplication with 20 ???
//    -> no changes made
//      $RunningSubTotal += (tep_add_tax(($products_details["qty"] * $products_details["final_price"]), $products_details["tax"])*20)/20; // version WITH tax
  
      $RunningSubTotal += $products_details["qty"] * $products_details["final_price"]; // version WITHOUT tax
      $RunningTax += (($products_details["tax"]/100) * ($products_details["qty"] * $products_details["final_price"]));
  
      // Update Any Attributes
      if (IsSet($products_details["attributes"])) {
        foreach ($products_details["attributes"] as $attributes_id => $attributes_details) {
          $_SESSION['create_order2']['orders_products_attributes'][$orders_products_id][$attributes_id]['products_options'] = $attributes_details["option"];
          $_SESSION['create_order2']['orders_products_attributes'][$orders_products_id][$attributes_id]['products_options_values'] = $attributes_details["value"];
        }
      }
    } else { // b.) null quantity found --> delete
      unset($_SESSION['create_order2']['orders_products'][$orders_products_id]);
      unset($_SESSION['create_order2']['orders_products_attributes'][$orders_products_id]);
      $products_delete = true;
    }
  }
  
  // 1.4. UPDATE SHIPPING, DISCOUNT & CUSTOM TAXES #####

  foreach($update_totals as $total_index => $total_details) {
    extract($total_details,EXTR_PREFIX_ALL,"ot");
  
// Correction tax calculation (Michel Haase, 2005-02-18)
// Correction tax calculation (Shimon Pozin, 2005-09-03) 
// Here is the major caveat: the product is priced in default currency, while shipping etc. are priced in target currency. We need to convert target currency
// into default currency before calculating RunningTax (it will be converted back before display)
    if ($ot_class == "ot_shipping" || $ot_class == "ot_lev_discount" || $ot_class == "ot_customer_discount" || $ot_class == "ot_custom" || $ot_class == "ot_cod_fee") {
      $order = $_SESSION['create_order2']['orders'];
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
    if (trim($ot_title) && trim($ot_value) || $ot_class == "ot_point") {
  
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
        }
      }
  
      $order = $_SESSION['create_order2']['orders'];

/*
需要加到订单生成中去
      if ($customer_guest['customers_guest_chk'] == 0 && $ot_class == "ot_point" && $ot_value != $before_point) { //会員ならポントの増減
        $point_difference = ($ot_value - $before_point);
        tep_db_query("update " . TABLE_CUSTOMERS . " set point = point - " . $point_difference . " where customers_id = '" . $order->customer['id'] . "'"); 
      }
*/

      $ot_text = $currencies->format($ot_value, true, $order['currency'], $order['currency_value']);
  
      if ($ot_class == "ot_total") {
        $ot_text = "<b>" . $ot_text . "</b>";
      }
      // 处理手续费失效的问题
      if ($ot_class == 'ot_custom') {
        $_SESSION['create_order2']['orders_total'][] = array(
          'orders_id' => $oID ,
          'title' => $ot_title ,
          'text' =>  tep_insert_currency_text($ot_text) ,
          'value' => tep_insert_currency_value($ot_value) ,
          'class' => $ot_class ,
          'sort_order' => $sort_order 
        );
      } else {
        $_SESSION['create_order2']['orders_total'][$ot_class] = array(
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
      //($ot_total_id > 0) && 
      ($ot_class != "ot_shipping") && ($ot_class != "ot_point")) { // Delete Total Piece
      unset($_SESSION['create_order2']['orders_total'][$ot_class]);
    }
  
  }
  //exit;
  
  $order = $_SESSION['create_order2']['orders'];
  $RunningSubTotal = 0;
  $RunningTax = 0;
  
  foreach ($_SESSION['create_order2']['orders_products'] as $pid => $order_products) {
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
  /*
  tep_db_query("update " . TABLE_ORDERS_TOTAL . " set 
  value = '".$new_subtotal."', 
  text = '".$currencies->format($new_subtotal, true, $order->info['currency'])."' 
  where class='ot_subtotal' and orders_id = '".$oID."'");
  */
  $_SESSION['create_order2']['orders_total']['ot_subtotal']['value'] = tep_insert_currency_value($new_subtotal);
  $_SESSION['create_order2']['orders_total']['ot_subtotal']['text']  = tep_insert_currency_text($currencies->format($new_subtotal, true, $order['currency']));
  
  
  //tax
  $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_ORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
  $plustax = tep_db_fetch_array($plustax_query);
  if($plustax['cnt'] > 0) {
    $_SESSION['create_order2']['orders_total']['ot_tax']['value'] = tep_insert_currency_value($new_tax);
    $_SESSION['create_order2']['orders_total']['ot_tax']['text']  = tep_insert_currency_text($currencies->format($new_tax, true, $order['currency']));
    //tep_db_query("update " . TABLE_ORDERS_TOTAL . " set value = '".$new_tax."', text = '".$currencies->format($new_tax, true, $order->info['currency'])."' where class='ot_tax' and orders_id = '".$oID."'");
  }

  //point修正中
  //$point_query = tep_db_query("select sum(value) as total_point from " . TABLE_ORDERS_TOTAL . " where class = 'ot_point' and orders_id = '" . $oID . "'");
  //$total_point = tep_db_fetch_array($point_query);
  $total_point = $_SESSION['create_orders2']['orders_total']['ot_point']['value'];

  //total
  //$total_query = tep_db_query("select sum(value) as total_value from " . TABLE_ORDERS_TOTAL . " where class != 'ot_total' and class != 'ot_point' and orders_id = '" . $oID . "'");
  //$total_value = tep_db_fetch_array($total_query);
  //$total_value = $_SESSION['create_orders2']['orders_total']['ot_point']['value'];
  $total_value = 0;
  foreach ($_SESSION['create_order2']['orders_total'] as $code => $orders_total) {
    if ($code != 'ot_total') {
      $total_value += $orders_total['value'];
    }
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
  
  //if (($newtotal - $total_point["total_point"]) >= 1) {
  //if ($newtotal > 0) {
  //  $newtotal -= $total_point["total_point"];
  //}
  //} else {
  //  $newtotal = '0';
  //}
  
  $handle_fee = calc_handle_fee($order['payment_method'], $newtotal);
  
  $newtotal = $newtotal+$handle_fee;

  $_SESSION['create_order2']['orders_total']['ot_total']['value'] = intval(floor($newtotal));
  $_SESSION['create_order2']['orders_total']['ot_total']['text']  = "<b>" . $currencies->ot_total_format(intval(floor($newtotal)), true, $order['currency']) . "</b>";
  
  /*
  $totals = "update " . TABLE_ORDERS_TOTAL . " set value = '" . $newtotal . "', text = '<b>" . $currencies->format($newtotal, true, $order->info['currency']) . "</b>' where class='ot_total' and orders_id = '" . $oID . "'";
  tep_db_query($totals);
  */
  
  /*
  $update_orders_sql = "update ".TABLE_ORDERS." set code_fee = '".$handle_fee."' where orders_id = '".$oID."'";
  tep_db_query($update_orders_sql);
  */
  $_SESSION['create_order2']['orders']['code_fee'] = $handle_fee;
    
  // 最終処理（更新およびメール送信）
  if ($products_delete == false) {
    /*
    tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
    orders_updated(tep_db_input($oID));
    */
    //$_SESSION['create_order2']['orders']['orders_status'] = $status;
    //$_SESSION['create_order2']['orders']['last_modified'] = 'now()';
    
    $notify_comments = '';
    $notify_comments_mail = $comments;
    $customer_notified = '0';
    
    if ($comments) {
      $notify_comments_mail .= "\n\n";
    }
    if (isset($_POST['notify_comments']) && ($_POST['notify_comments'] == 'on')) {
      $notify_comments = $comments;
    }

    if (isset($_POST['x']) && isset($_POST['y'])) {
      if (
           !$_SESSION['create_order2']['orders'] 
        || !$_SESSION['create_order2']['orders']['orders_id'] 
        || !$_SESSION['create_order2']['orders']['customers_id']
      ) {
        $messageStack->add_session(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
        tep_redirect(tep_href_link(FILENAME_CREATE_ORDER2));
      }
      // orders 
      tep_db_perform(TABLE_ORDERS, $_SESSION['create_order2']['orders']);
      orders_updated($_SESSION['create_order2']['orders']['orders_id']);
      foreach($_SESSION['create_order2']['orders_products'] as $pid => $orders_product) {
        $orders_product['site_id'] = $_SESSION['create_order2']['orders']['site_id'];
        tep_db_perform(TABLE_ORDERS_PRODUCTS, $orders_product);
        $orders_products_id = tep_db_insert_id();
        foreach($_SESSION['create_order2']['orders_products_attributes'][$pid] as $aid => $orders_product_attributes) {
          $orders_product_attributes['orders_products_id'] = $orders_products_id;
          tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $orders_product_attributes);
        }
        /* 士入不需要操作库存 */
      }
      orders_updated($_SESSION['create_order2']['orders']['orders_id']);
      foreach($_SESSION['create_order2']['orders_total'] as $c => $ot){
        tep_db_perform(TABLE_ORDERS_TOTAL, $ot);
      }
      
      // orders_status_history
      $sql_data_array = array(
                  'orders_id' => $_SESSION['create_order2']['orders']['orders_id'], 
                  'orders_status_id' => $_SESSION['create_order2']['orders']['orders_status'], 
                  'date_added' => 'now()', 
                  'customer_notified' => '1',
                  'comments' => $_SESSION['create_order']['comments']);
      tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
      $sql_customers_array = array( 'customers_fax' =>
          $_SESSION['create_order2']['customer_fax']);
      tep_db_perform(TABLE_CUSTOMERS,$sql_customers_array,'update','customers_id='.$_SESSION['create_order2']['orders']['customers_id']);
      
      
      
//start print 
  # 印刷用メール本文 ----------------------------
  if(preg_match('/ /',$order['torihiki_date'])){
    $date_arr = explode(" ",$order['torihiki_date']);
    $date_time_arr = explode(':',$date_arr[1]);
  }
  $email_printing_order = '';
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= 'サイト名　　　　：' . STORE_NAME . "\n";
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= '取引日時　　　　：' . str_string($date_arr[0]) .
  $date_time_arr[0] . '時' . $date_time_arr[1] . '分　（24時間表記）' . "\n";
  $email_printing_order .= 'オプション　　　：' . $order['torihiki_houhou'] . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '日時変更　　　　：' . date('Y') . ' 年  月  日  時  分' . "\n";
  $email_printing_order .= '日時変更　　　　：' . date('Y') . ' 年  月  日  時  分' . "\n";
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= '注文者名　　　　：' . $order['customers_name'] . '様' . "\n";
  $email_printing_order .= '注文番号　　　　：' . $order['orders_id'] . "\n";
  $email_printing_order .= '注文日　　　　　：' . tep_date_long(time()) . "\n";
  $email_printing_order .= 'メールアドレス　：' . $order['customers_email_address'] . "\n";
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  if ($ot_point['value'] > 0) {
    $email_printing_order .= '□ポイント割引　　：' . (int)$ot_point['value'] . '円' . "\n";
  }
  /*
  if (!empty($total_mail_fee)) {
    $email_printing_order .= '手数料　　　　　：'.$total_mail_fee.'円'."\n"; 
  }
  */
  if($handle_fee) {
    $email_printing_order .= '手数料　　　　　：'.$currencies->format($handle_fee)."\n";
  }
  $email_printing_order .= 'お支払金額　　　：' .
    strip_tags($orders_total['text']) . "\n";
  if (isset($order['payment_method'])&&$order['payment_method']!='') {
    $payment_class = $$payment;
    $email_printing_order .= 'お支払方法　　　：' . $order['payment_method']. "\n";
  }
  
  /*
  if(tep_not_null($bbbank)) {
    $email_printing_order .= 'お支払先金融機関' . "\n";
    $email_printing_order .= $bbbank . "\n";
  }
  */
  
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  
  
      $order2 = new order($_SESSION['create_order2']['orders']['orders_id']);
      $products_ordered_mail = '';
      for ($i=0; $i<sizeof($order2->products); $i++) {
        //$orders_products_id = $order->products[$i]['orders_products_id'];
        $products_ordered_mail .= '注文商品　　　　　：' . $order2->products[$i]['name'] . '（' . $order2->products[$i]['model'] . '）' . "\n";
        // Has Attributes?
        if (sizeof($order2->products[$i]['attributes']) > 0) {
          for ($j=0; $j<sizeof($order2->products[$i]['attributes']); $j++) {
            $orders_products_attributes_id = $order2->products[$i]['attributes'][$j]['orders_products_attributes_id'];
            $products_ordered_mail .= tep_parse_input_field_data($order2->products[$i]['attributes'][$j]['option'], array("'"=>"&quot;")) . '　　　　　：';
            $products_ordered_mail .= tep_parse_input_field_data($order2->products[$i]['attributes'][$j]['value'], array("'"=>"&quot;")) . "\n";
          }
        }
          
        $products_ordered_mail .= '個数　　　　　　　：' . $order2->products[$i]['qty'] . '個' . tep_get_full_count2($order2->products[$i]['qty'], $order2->products[$i]['id']) . "\n";
        $products_ordered_mail .= '単価　　　　　　　：' . $currencies->display_price($order2->products[$i]['final_price'], $order2->products[$i]['tax']) . "\n";
        $products_ordered_mail .= '小計　　　　　　　：' . $currencies->display_price($order2->products[$i]['final_price'], $order2->products[$i]['tax'], $order2->products[$i]['qty']) . "\n";
        $products_ordered_mail .= 'キャラクター名　　：' . (EMAIL_USE_HTML === 'true' ? htmlspecialchars($order2->products[$i]['character']) : $order2->products[$i]['character']) . "\n";
        $products_ordered_mail .= "------------------------------------------\n";
        if (tep_get_cflag_by_product_id($order2->products[$i]['id'])) {
            if (tep_get_bflag_by_product_id($order2->products[$i]['id'])) {
              $products_ordered_mail .= "※ 当社キャラクター名は、お取引10分前までに電子メールにてお知らせいたします。\n\n";
            } else {
              $products_ordered_mail .= "※ 当社キャラクター名は、お支払い確認後に電子メールにてお知らせいたします。\n\n";
            }
        }
      }
  
  
  
  
  
  
  
  
  
  $email_printing_order .= $products_ordered_mail;

  $email_printing_order .= '備考　　　　　　：' . "\n";

  if ($order['orders_comment']) {
    $email_printing_order .= $order['orders_comment'] . "\n";
  }

  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= 'IPアドレス　　　　　　：' . $_SERVER["REMOTE_ADDR"] . "\n";
  $email_printing_order .= 'ホスト名　　　　　　　：' . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
  $email_printing_order .= 'ユーザーエージェント　：' . $_SERVER["HTTP_USER_AGENT"] . "\n";
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= '信用調査' . "\n";
//ccdd
  $credit_inquiry_query = tep_db_query("select customers_fax, customers_guest_chk
      from " . TABLE_CUSTOMERS . " where customers_id = '" . $order['customers_id'] . "'");
  $credit_inquiry       = tep_db_fetch_array($credit_inquiry_query);
  
  $email_printing_order .= $credit_inquiry['customers_fax'] . "\n";
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= '注文履歴　　　　　　　：';
  
  if ($credit_inquiry['customers_guest_chk'] == '1') { $email_printing_order .= 'ゲスト'; } else { $email_printing_order .= '会員'; }
  
  $email_printing_order .= "\n";
  
  $order_history_query_raw = "select o.orders_id, o.customers_name, o.customers_id,
    o.date_purchased, s.orders_status_name, ot.text as order_total from " .
      TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id =
      ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" .
      tep_db_input($order['customers_id']) . "' and o.orders_status =
      s.orders_status_id and s.language_id = '" . $_SESSION['languages_id'] . "' and ot.class = 'ot_total' order by o.date_purchased DESC limit 0,5";  
//ccdd
  $order_history_query = tep_db_query($order_history_query_raw);
  while ($order_history = tep_db_fetch_array($order_history_query)) {
  $email_printing_order .= $order_history['date_purchased'] . '　　' . tep_output_string_protected($order_history['customers_name']) . '　　' . strip_tags($order_history['order_total']) . '　　' . $order_history['orders_status_name'] . "\n";
  }
  
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n\n\n";
  
  

  if ($order['payment_method'] === '銀行振込(買い取り)') {
    $email_printing_order .= '★★★★★★★★★★★★この注文は【買取】です。★★★★★★★★★★★★' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '備考の有無　　　　　：□ 無　　｜　　□ 有　→　□ 返答済' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= 'キャラクターの有無　：□ 有　　｜　　□ 無　→　新規作成してお客様へ連絡' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '受領　※注意※　　●：＿＿月＿＿日' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '残量入力→誤差有無　：□ 無　　｜　　□ 有　→　□ 報告' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '受領メール送信　　　：□ 済' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '支払　　　　　　　　：＿＿月＿＿日　※総額5,000円未満は168円引く※' . "\n";
    $email_printing_order .= '　　　　　　　　　　　□ JNB　　□ eBank　　□ ゆうちょ' . "\n";
    $email_printing_order .= '　　　　　　　　　　　入金予定日＿＿月＿＿日　受付番号＿＿＿＿＿＿＿＿＿' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '支払完了メール送信　：□ 済　　　※追加文章がないか確認しましたか？※' . "\n";
  } elseif ($payment_class->title === 'クレジットカード決済') {
    $email_printing_order .= 'この注文は【販売】です。' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '備考の有無　　　　　：□ 無　　｜　　□ 有　→　□ 返答済' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '決済確認　　　　　●：＿＿月＿＿日' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '在庫確認　　　　　　：□ 有　　｜　　□ 無　→　仕入困難ならお客様へ電話' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '信用調査　　　　　　：□ 2回目以降　→　□ 常連（以下のチェック必要無）' . "\n";
    $email_printing_order .= '　　　　　　　　　　　　　　　　　　　　□ 1. 過去に本人確認をしている' . "\n";
    $email_printing_order .= '　　　　　　　　　　　　　　　　　　　　□ 2. 決済内容に変更がない' . "\n";
    $email_printing_order .= '　　　　　　　　　　　　　　　　　　　　□ 3. 短期間に高額決済がない' . "\n";
    $email_printing_order .= '　　　　　　　　　　----------------------------------------------------' . "\n";
    $email_printing_order .= '　　　　　　　　　　　□ 初回　→　□ IP・ホストのチェック' . "\n";
    $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 電話確認をする' . "\n";
    $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 カード名義（カタカナ）＿＿＿＿＿＿' . "\n";
    $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 電話番号＿＿＿＿＿＿＿＿＿＿＿＿＿' . "\n";
    $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 ＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿' . "\n";
    $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 ＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿' . "\n";
    $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 ＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿＿' . "\n";
    $email_printing_order .= '　　　　　　　　　　　　　　　　　 □ カード名義・商品名・キャラ名一致' . "\n";
    $email_printing_order .= '　　　　　　　　　　　　　　　　　 　 本人確認日：＿＿月＿＿日' . "\n";
    $email_printing_order .= '　　　　　　　　　　　　　　　　　 □ 信用調査入力' . "\n";
    $email_printing_order .= '　　　　　　　　　　----------------------------------------------------' . "\n";
    $email_printing_order .= '※ 疑わしい点があれば担当者へ報告をする　→　担当者＿＿＿＿の承諾を得た' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '発送　　　　　　　　：＿＿月＿＿日' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '残量入力→誤差有無　：□ 無　　｜　　□ 有　→　報告　□' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '発送完了メール送信　：□ 済' . "\n";
  } else {
    $email_printing_order .= 'この注文は【販売】です。' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '備考の有無　　　　　：□ 無　　｜　　□ 有　→　□ 返答済' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '在庫確認　　　　　　：□ 有　　｜　　□ 無　→　入金確認後仕入' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '入金確認　　　　　●：＿＿月＿＿日　→　金額は' . strip_tags($ot['text']) . 'ですか？　□ はい' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '入金確認メール送信　：□ 済' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '発送　　　　　　　　：＿＿月＿＿日' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '残量入力→誤差有無　：□ 無　　｜　　□ 有　→　報告　□' . "\n";
    $email_printing_order .= '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '発送完了メール送信　：□ 済' . "\n";    
  }
  
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '最終確認　　　　　　：確認者名＿＿＿＿' . "\n";
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  # ------------------------------------------

  tep_mail('',
      get_configuration_by_site_id('PRINT_EMAIL_ADDRESS',$order->info['site_id']),
      get_configuration_by_site_id('STORE_NAME',$order->info['site_id']),
      $email_printing_order,$order['customers_name']
      ,$order['customers_email_address'] , '');


// echo print 
/*
  print_r($_SESSION);
  var_dump("<br><br>");
  var_dump(str_replace("\n",'<br>',$email_printing_order));
  exit;
*/

    if (isset($_POST['notify']) && ($_POST['notify'] == 'on')) {
      $order = new order($_SESSION['create_order2']['orders']['orders_id']);
      $products_ordered_mail = '';
      for ($i=0; $i<sizeof($order->products); $i++) {
        //$orders_products_id = $order->products[$i]['orders_products_id'];
        $products_ordered_mail .= '注文商品　　　　　：' . $order->products[$i]['name'] . '（' . $order->products[$i]['model'] . '）' . "\n";
        // Has Attributes?
        if (sizeof($order->products[$i]['attributes']) > 0) {
          for ($j=0; $j<sizeof($order->products[$i]['attributes']); $j++) {
            $orders_products_attributes_id = $order->products[$i]['attributes'][$j]['orders_products_attributes_id'];
            $products_ordered_mail .= tep_parse_input_field_data($order->products[$i]['attributes'][$j]['option'], array("'"=>"&quot;")) . '　　　　　：';
            $products_ordered_mail .= tep_parse_input_field_data($order->products[$i]['attributes'][$j]['value'], array("'"=>"&quot;")) . "\n";
          }
        }
          
        $products_ordered_mail .= '個数　　　　　　　：' . $order->products[$i]['qty'] . '個' . tep_get_full_count2($order->products[$i]['qty'], $order->products[$i]['id']) . "\n";
        $products_ordered_mail .= '単価　　　　　　　：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax']) . "\n";
        $products_ordered_mail .= '小計　　　　　　　：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . "\n";
        $products_ordered_mail .= 'キャラクター名　　：' . (EMAIL_USE_HTML === 'true' ? htmlspecialchars($order->products[$i]['character']) : $order->products[$i]['character']) . "\n";
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
            $total_details_mail .= '▼ポイント割引　　：-' . strip_tags($totals['text']) . "\n";
          }
        } elseif ($totals['class'] == "ot_total") {
          if($handle_fee) {
            $total_details_mail .= '▼手数料　　　　　：'.$currencies->format($handle_fee)."\n";
          }
          $total_details_mail .= '▼お支払金額　　　：' . strip_tags($totals['text']) . "\n";
          $total_price_mail = round($totals['value']);
        } else {
          $total_details_mail .= '▼' . $totals['title'] . str_repeat('　', intval((16 - strlen($totals['title']))/2)) . '：' . strip_tags($totals['text']) . "\n";
        }
      }



      $email = '';
      $email .= $order->customer['name'] . '様' . "\n\n";
      $email .= 'この度は、' . get_configuration_by_site_id('STORE_NAME',$order->info['site_id']) . 'をご利用いただき、誠にありが' . "\n";
      $email .= 'とうございます。' . "\n";
      $email .= '下記の内容にてご注文を承りましたので、ご確認ください。' . "\n";
      $email .= 'ご不明な点がございましたら、ご注文番号をご確認の上、' . "\n";
      $email .= '「' . get_configuration_by_site_id('STORE_NAME',$order->info['site_id']) . '」までお問い合わせください。' . "\n\n";
      $email .= $notify_comments_mail;
      $email .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
      $email .= '▼注文番号　　　　：' . $oID . "\n";
      $email .= '▼注文日　　　　　：' . tep_date_long(time()) . "\n";
      $email .= '▼お名前　　　　　：' . $order->customer['name'] . '様' . "\n";
      $email .= '▼メールアドレス　：' . $order->customer['email_address'] . "\n";
      $email .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
      $email .= $total_details_mail;
      $email .= '▼お支払方法　　　：' . $order->info['payment_method'] . "\n";
      if ($order->info['payment_method'] == 'ゆうちょ銀行（郵便局）') {
         $email .= get_configuration_by_site_id('C_POSTAL',$order->info['site_id']); 
      }
      if ($order->info['payment_method'] === '銀行振込') {
            $email .= get_configuration_by_site_id('C_BANK',$order->info['site_id']);
      } elseif ($order->info['payment_method'] === 'クレジットカード決済') {
            $email .= get_configuration_by_site_id('C_CC',$order->info['site_id']);
      } elseif ($order->info['payment_method'] === '銀行振込(買い取り)') {
        $orders_bank_account_query = tep_db_query("select comments from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($oID) . "' and orders_status_id = '1' and customer_notified = '1' order by date_added");
        if (tep_db_num_rows($orders_bank_account_query)) {
          while ($orders_bank_account = tep_db_fetch_array($orders_bank_account_query)) {
            if (strncmp($orders_bank_account['comments'], '金融機関名　　　　：', 20) == 0) {
              $bbbank = $orders_bank_account['comments'];
            }
          }
        } else {
          $bbbank = 'エラーが発生しました。' . "\n" . get_configuration_by_site_id('STORE_NAME',$order->info['site_id']) . 'へお問い合わせくだい。' . "\n";
        }
        $email .= '▼お支払先金融機関' . "\n";
      $email .= $bbbank . "\n";
      $email .= '━━━━━━━━━━━━━━━━━━━━━' . "\n\n";
      //$email .= '・本メールに記載された当社キャラクター宛に商品をトレードしてください。' . "\n";
      $email .= '・当社にて商品の受領確認がとれましたら代金お支払い手続きに入ります。' . "\n";
      $email .= '・本メール送信後7日以内に取引が完了できない場合、' . "\n";
      $email .= '　当社は、お客様がご注文を取り消されたものとして取り扱います。';
} elseif ($order->info['payment_method'] === 'コンビニ決済') {
      $email .= get_configuration_by_site_id('C_CONVENIENCE_STORE',$order->info['site_id']);
} else {
      $email .= '別途取り決めた方法に準じて行います。';
}
      $email .= "\n\n\n";
      $email .= '▼注文商品' . "\n";
      $email .= '------------------------------------------' . "\n";
      $email .= $products_ordered_mail;

      $array1 = explode(" ", $order->tori['date']);
      $array_ymd = explode("-",$array1[0]);
      $array_hms = explode(":",$array1[1]);
      $time1 = mktime($array_hms[0],$array_hms[1],$array_hms[2],$array_ymd[1],$array_ymd[2],$array_ymd[0]);
      $trade_time = date("Y年m月d日H時i分", $time1);

      $email .= '▼取引日時　　　　：' . $trade_time . '　（24時間表記）' . "\n";
      $email .= '　　　　　　　　　：' . strip_tags($order->tori['houhou']) . "\n";
      $email .= '▼備考　　　　　　：';
      if ($order->info['payment_method'] === 'コンビニ決済') {
        $orders_con_query = tep_db_query("select comments from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id = '".tep_db_input($oID)."' and orders_status_id = '1' and customer_notified = '1' order by date_added");
        if (tep_db_num_rows($orders_con_query)) {
          while ($orders_con_res = tep_db_fetch_array($orders_con_query)) {
            $email .= $orders_con_res['comments'];  
          }
        }
      }
      $email .= "\n\n\n";
      $email .= '[ご連絡・お問い合わせ先]━━━━━━━━━━━━' . "\n";
      $email .= '株式会社 iimy' . "\n";
      $email .= get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS',$order->info['site_id']) . "\n";
      $email .= get_url_by_site_id($order->info['site_id']) . "\n";
      $email .= '━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
      if ($customer_guest['customers_guest_chk'] != 9) {
        tep_mail($check_status['customers_name'], $check_status['customers_email_address'], 'ご注文ありがとうございます【' . get_configuration_by_site_id('STORE_NAME',$order->info['site_id']) . '】', $email, get_configuration_by_site_id('STORE_OWNER',$order->info['site_id']), get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS',$order->info['site_id']),$order->info['site_id']);
      }
      tep_mail(get_configuration_by_site_id('STORE_OWNER',$order->info['site_id']), get_configuration_by_site_id('SENTMAIL_ADDRESS',$order->info['site_id']), 'ご注文ありがとうございます【' . get_configuration_by_site_id('STORE_NAME',$order->info['site_id']) . '】', $email, $check_status['customers_name'], $check_status['customers_email_address'],$order->info['site_id']);
      
      $customer_notified = '1';
    }



//end print

      
      
      unset($_SESSION['create_order2']);
      //exit;
      tep_redirect(tep_href_link(FILENAME_ORDERS, 'oID='.$sql_data_array['orders_id']));
    }
    //tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" . tep_db_input($customer_notified) . "', '" . $notify_comments . "')");
    $order_updated_2 = true;
  }

    if ($order_updated && !$products_delete && $order_updated_2) {
      $messageStack->add_session(SUCCESS_ORDER_UPDATED, 'success');
    } elseif ($order_updated && $products_delete) {
      $messageStack->add_session('商品を削除しました。<font color="red">メールは送信されていません。</font>', 'success');
    } else {
      $messageStack->add_session('エラーが発生しました。正常に処理が行われていない可能性があります。', 'error');
    }

    tep_redirect(tep_href_link("edit_new_orders2.php", tep_get_all_get_params(array('action')) . 'action=edit'));
    
  break;

  // 2. ADD A PRODUCT ###############################################################################################
  case 'add_product':
  
    if($step == 5)
    {
      // 2.1 GET ORDER INFO #####
      
      $oID = tep_db_prepare_input($_SESSION['create_order2']['orders']['orders_id']);
      $order = $_SESSION['create_order2']['orders'];

      if (isset($_POST['add_product_options'])) {
        $add_product_options = $_POST['add_product_options'];
      }
      $AddedOptionsPrice = 0;

      // 2.1.1 Get Product Attribute Info
      if(IsSet($add_product_options))
      {
        foreach($add_product_options as $option_id => $option_value_id)
        {
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
      
      // 特価を適用
      // $p_products_price = tep_get_final_price($p_products_price, $p_products_price_offset, $p_products_small_sum, (int)$add_product_quantity);
      $p_products_price = $_POST['add_product_price'];

      // Following functions are defined at the bottom of this file
      $CountryID = tep_get_country_id($order["delivery_country"]);
      $ZoneID = tep_get_zone_id($CountryID, $order["delivery_state"]);
      
      $ProductsTax = tep_get_tax_rate($p_products_tax_class_id, $CountryID, $ZoneID);
      
      // 2.2 UPDATE ORDER #####
      $_SESSION['create_order2']['orders_products'][$add_product_products_id] = array(
        'orders_id' => $oID,
        'products_id' => $add_product_products_id,
        'products_model' => $p_products_model,
        'products_name' => str_replace("'", "&#39;", $p_products_name),
        'products_character' => mysql_real_escape_string($add_product_character),
        'products_price' => $p_products_price,
        'final_price' => $p_products_price + $AddedOptionsPrice,
        'products_tax' => $ProductsTax,
        'site_id' => tep_get_site_id_by_orders_id($oID),
        'products_rate' => tep_get_products_rate($add_product_products_id),
        'products_quantity' => $add_product_quantity
      );
      //tep_db_query($Query);
      //$new_product_id = tep_db_insert_id();

      
      //orders_updated($oID);
      
      
      // 2.2.1 Update inventory Quantity
      //tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = products_quantity - " . (int)$add_product_quantity . ", products_ordered = products_ordered + " . (int)$add_product_quantity . " where products_id = '" . $add_product_products_id . "'");
      //tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = 0 where products_quantity < 0 and products_id = '" . $add_product_products_id . "'");

      if (IsSet($add_product_options)) {
        foreach($add_product_options as $option_id => $option_value_id) {
          $_SESSION['create_order2']['orders_products_attributes'][$add_product_products_id][$option_attributes_id[$option_value_id]] = array(
            'orders_id' => $oID,
            'orders_products_id'      => $new_product_id,
            'products_options'        => $option_names[$option_id],
            'products_options_values' => tep_db_input($option_values_names[$option_value_id]),
            'options_values_price'    => $option_value_details[$option_id][$option_value_id]["options_values_price"],
            'attributes_id'           => $option_attributes_id[$option_value_id],
            'price_prefix'            => '+'
          );
          //tep_db_query($Query);
        }
      }
      
      // 2.2.2 Calculate Tax and Sub-Totals
      $order = $_SESSION['create_order2']['orders'];
      $RunningSubTotal = 0;
      $RunningTax = 0;

      foreach ($_SESSION['create_order2']['orders_products'] as $pid => $order_products) {
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
      /*
      tep_db_query("update " . TABLE_ORDERS_TOTAL . " set 
      value = '".$new_subtotal."', 
      text = '".$currencies->format($new_subtotal, true, $order['currency'])."' 
      where class='ot_subtotal' and orders_id = '".$oID."'");
      */
      
      $_SESSION['create_order2']['orders_total']['ot_subtotal']['value'] = tep_insert_currency_value($new_subtotal);
      $_SESSION['create_order2']['orders_total']['ot_subtotal']['text']  = tep_insert_currency_text($currencies->format($new_subtotal, true, $order['currency']));
      
      //tax
      $plustax_query = tep_db_query("select count(*) as cnt from " . TABLE_ORDERS_TOTAL . " where class = 'ot_tax' and orders_id = '".$oID."'");
      $plustax = tep_db_fetch_array($plustax_query);
      if($plustax['cnt'] > 0) {
        $_SESSION['create_order2']['orders_total']['ot_tax']['value'] = tep_insert_currency_value($new_tax);
        $_SESSION['create_order2']['orders_total']['ot_tax']['text']  = tep_insert_currency_text($currencies->format($new_tax, true, $order['currency']));
        //tep_db_query("update " . TABLE_ORDERS_TOTAL . " set value = '".$new_tax."', text = '".$currencies->format($new_tax, true, $order['currency'])."' where class='ot_tax' and orders_id = '".$oID."'");
      }
      
      //total
      //$total_query = tep_db_query("select sum(value) as total_value from " . TABLE_ORDERS_TOTAL . " where class != 'ot_total' and orders_id = '".$oID."'");
      //$total_value = tep_db_fetch_array($total_query);
      $total_value = 0;
      foreach ($_SESSION['create_order2']['orders_total'] as $code => $orders_total) {
        if ($code != 'ot_total') {
          $total_value += $orders_total['value'];
        }
      }

      if($plustax['cnt'] == 0) {
        $newtotal = $total_value + $new_tax;
      } else {
        if(DISPLAY_PRICE_WITH_TAX == 'true') {
          $newtotal = $total_value - $new_tax;
        } else {
          $newtotal = $total_value;
        }
      }
      
      $handle_fee = calc_handle_fee($order['payment_method'], $newtotal);
      $newtotal = $newtotal+$handle_fee;    
      //$totals = "update " . TABLE_ORDERS_TOTAL . " set value = '".$newtotal."', text = '<b>".$currencies->format($newtotal, true, $order['currency'])."</b>' where class='ot_total' and orders_id = '".$oID."'";
      //tep_db_query($totals);
      $_SESSION['create_order2']['orders_total']['ot_total']['value'] = intval(floor($newtotal));
      $_SESSION['create_order2']['orders_total']['ot_total']['text']  = $currencies->ot_total_format(intval(floor($newtotal)), true, $order['currency']);
      
      $_SESSION['create_order2']['orders']['code_fee'] = $handle_fee;
      //$update_orders_sql = "update ".TABLE_ORDERS." set code_fee = '".$handle_fee."' where orders_id = '".$oID."'";
      //tep_db_query($update_orders_sql);
      tep_redirect(tep_href_link("edit_new_orders2.php", tep_get_all_get_params(array('action')) . 'action=edit'));
    }
  
    break;
    
  }
}

  if (($action == 'edit') && isset($_GET['oID'])) {
    //$oID = tep_db_prepare_input($_GET['oID']);
    //$orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
    $order_exists = true;
    if (!isset($_SESSION['create_order2']['orders']['orders_id'])
    || $_SESSION['create_order2']['orders']['orders_id'] != $_GET['oID']
    ) {
      $order_exists = false;
      $messageStack->add(sprintf(ERROR_ORDER_DOES_NOT_EXIST, $oID), 'error');
      tep_redirect(tep_href_link(FILENAME_CREATE_ORDER2));
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<!--京-->
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script>
function check_add(){
  price = document.getElementById('add_product_price').value;
  if(price != '' && price != 0  && price > 0){
    return true;
  } else {
    alert("単価を書いてください");
    return false;
  }
}
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
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
    $order = $_SESSION['create_order2']['orders'];
?>
        <tr>
          <td width="100%">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading">注文書の作成</td>
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
                <td class="pageHeading" align="right">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="3"><font color="red">【重要】注文編集ではありません。新規注文作成システムです。</font></td>
              </tr>
            </table>
            <?php echo tep_draw_separator(); ?>
          </td>
        </tr> 
        <tr><?php echo tep_draw_form('edit_order', "edit_new_orders2.php", tep_get_all_get_params(array('action','paycc')) . 'action=update_order', 'post', 'onSubmit="return submitChk2();"'); ?>
          <td>
            <!-- Begin Update Block -->
            <table width="100%" border="0" cellpadding="2" cellspacing="1">
              <tr>
                <td class="main" bgcolor="#FFDDFF" height="25">変更したい内容を慎重に入力してください。<b>空白などの余分な文字が入力されていないかチェックするように！</b></td>
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
                <td class="main" valign="top" width="30%"><b>注文番号:</b></td>
                <td class="main" width="70%"><?php echo $order['orders_id'];?></td>
              </tr>
              <tr>
                <td class="main" valign="top"><b>注文日:</b></td>
                <td class="main"><?php echo tep_date_long(date('Y-m-d H:i:s'));?></td>
              </tr>
              <tr>
                <td class="main" valign="top"><b>顧客名:</b></td>
                <td class="main"><?php echo tep_html_quotes($order['customers_name']); ?></td>
              </tr>
              <tr>
                <td class="main" valign="top"><b>メールアドレス:</b></td>
                <td class="main"><font color="red"><b><?php echo $order['customers_email_address']; ?></b></font></td>
              </tr>
              <!-- End Addresses Block -->
              <!-- Begin Payment Block -->
              <tr>
                <td class="main" valign="top"><b>支払方法:</b></td>
                <td class="main"><?php echo $order['payment_method']; ?></td>
              </tr>
              <!-- End Payment Block -->
              <!-- Begin Trade Date Block -->
              <tr>
                <td class="main" valign="top"><b>取引日時:</b></td>
                <td class="main"><?php echo $order['torihiki_date']; ?></td>
              </tr>
              <tr>
                <td class="main" valign="top"><b>オプション:</b></td>
                <td class="main"><?php echo $order['torihiki_houhou']; ?></td>
              </tr>
            </table>
            <!-- End Trade Date Block -->
          </td>
        </tr>
        <!-- Begin Products Listing Block -->
        <tr>
          <td class="SubTitle"><br>2. 注文商品</td>
        </tr>
        <tr>
          <td>      
  <?php
    // Override order.php Class's Field Limitations
    $order_products = array();
    $order_products_attributes = array();
    //$orders_products_query = tep_db_query("select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($oID) . "'");
    if ($_SESSION['create_order2']['orders_products']) 
      foreach ($_SESSION['create_order2']['orders_products'] as $pid => $orders_products) {
      $order_products[$pid] = array('qty' => $orders_products['products_quantity'],
                                     'name' => str_replace("'", "&#39;", $orders_products['products_name']),
                                     'model' => $orders_products['products_model'],
                                     'character' => $orders_products['products_character'],
                                     'tax' => $orders_products['products_tax'],
                                     'price' => $orders_products['products_price'],
                                     'final_price' => $orders_products['final_price'],
                                     'orders_products_id' => $orders_products['orders_products_id']);

    //$attributes_query_string = "select * from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . tep_db_input($oID) . "' and orders_products_id = '" . (int)$orders_products['orders_products_id'] . "'";
    //$attributes_query = tep_db_query($attributes_query_string);

    if ($_SESSION['create_order2']['orders_products_attributes'][$pid]) {
      foreach ($_SESSION['create_order2']['orders_products_attributes'][$pid] as $attributes) {
        $order_products_attributes[$pid][] = array('option' => $attributes['products_options'],
                                                         'value' => $attributes['products_options_values'],
                                                         'prefix' => $attributes['price_prefix'],
                                                         'price' => $attributes['options_values_price'],
                                                         'attributes_id' => $attributes['attributes_id'],
                                                         'orders_products_attributes_id' => $attributes['orders_products_attributes_id']);
      }
    }
  }
  
?>
<?php // Version without editable names & prices ?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" colspan="2">数量 / 商品名</td>
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
    <td class="dataTableHeadingContent">税率</td>
    <td class="dataTableHeadingContent" align="right">価格(税別)</td>
    <td class="dataTableHeadingContent" align="right">価格(税込)</td>
    <td class="dataTableHeadingContent" align="right">合計(税別)</td>
    <td class="dataTableHeadingContent" align="right">合計(税込)</td>
  </tr>
  
<?php
  foreach ($order_products as $pid => $orders_products) {
    //$orders_products_id = $order_products[$pid]['orders_products_id'];
    $RowStyle = "dataTableContent";
    echo '    <tr class="dataTableRow">' . "\n" .
         '      <td class="' . $RowStyle . '" align="left" valign="top" width="20">' . "<input name='update_products[$pid][qty]' size='2' value='" . $order_products[$pid]['qty'] . "'>&nbsp;x</td>\n" . 
         '      <td class="' . $RowStyle . '">' . $order_products[$pid]['name'] . "<input name='update_products[$pid][name]' size='64' type='hidden' value='" . $order_products[$pid]['name'] . "'>\n" . 
       '      &nbsp;&nbsp;キャラ名：<input type="hidden" name="dummy" value="あいうえお眉幅"><input name="update_products[' . $pid . '][character]" size="20" value="' . htmlspecialchars($order_products[$pid]['character']) . '">';
    // Has Attributes?
    if (sizeof($order_products_attributes[$pid]) > 0) {
      for ($j=0; $j<sizeof($order_products_attributes[$pid]); $j++) {
        //$orders_products_attributes_id = $order_products_attributes[$pid][$j]['orders_products_attributes_id'];
        echo '<br><nobr><small>&nbsp;<i> - ' . 
           '<input name="update_products[' . $pid . '][attributes][' . $order_products_attributes[$pid][$j]['attributes_id'] . '][option]" size="10" value="' . tep_parse_input_field_data($order_products_attributes[$pid][$j]['option'], array("'"=>"&quot;")) . '">' . 
           ': ' . 
           '<input name="update_products[' . $pid . '][attributes][' . $order_products_attributes[$pid][$j]['attributes_id'] . '][value]" size="35" value="' . tep_parse_input_field_data($order_products_attributes[$pid][$j]['value'], array("'"=>"&quot;"));
        echo '">';
        echo '</i></small></nobr>';
      }
    }
    
    echo '      </td>' . "\n" .
         '      <td class="' . $RowStyle . '">' . $order_products[$pid]['model'] . "<input name='update_products[$pid][model]' size='12' type='hidden' value='" . $order_products[$pid]['model'] . "'>" . '</td>' . "\n" .
         '      <td class="' . $RowStyle . '" align="right">' . tep_display_tax_value($order_products[$pid]['tax']) . "<input name='update_products[$pid][tax]' size='2' type='hidden' value='" . tep_display_tax_value($order_products[$pid]['tax']) . "'>" . '%</td>' . "\n" .
         '      <td class="' . $RowStyle . '" align="right">' . "<input name='update_products[$pid][final_price]' size='9' value='" . tep_display_currency(number_format(abs($order_products[$pid]['final_price']),2)) . "'>" . '</td>' . "\n" . 
         '      <td class="' . $RowStyle . '" align="right">' . $currencies->format(tep_add_tax($order_products[$pid]['final_price'], $order_products[$pid]['tax']), true, $order['currency'], $order['currency_value']) . '</td>' . "\n" . 
         '      <td class="' . $RowStyle . '" align="right">' . $currencies->format($order_products[$pid]['final_price'] * $order_products[$pid]['qty'], true, $order['currency'], $order['currency_value']) . '</td>' . "\n" . 
         '      <td class="' . $RowStyle . '" align="right"><b>' . $currencies->format(tep_add_tax($order_products[$pid]['final_price'], $order_products[$pid]['tax']) * $order_products[$pid]['qty'], true, $order['currency'], $order['currency_value']) . '</b></td>' . "\n" . 
         '    </tr>' . "\n";
  }
  ?>
</table>

        </td>
      <tr>
        <td>
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td valign="top"><?php echo "<span class='smalltext'>" . HINT_DELETE_POSITION . "商品追加と他の項目は同時に変更できません。<b>「 商品の追加 」は単体で行ってください。</b></span>"; ?></td>
              <td align="right"><?php echo '<a href="' . $PHP_SELF . '?oID=' . $order['orders_id'] . '&action=add_product&step=1">' . tep_image_button('button_add_article.gif', ADDING_TITLE) . '</a>'; ?></td>
            </tr>
          </table>
        </td>
      </tr>     
  <!-- End Products Listings Block -->
  <!-- Begin Order Total Block -->
      <tr>
        <td class="SubTitle">3. ポイント割引、手数料、値引き</td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
      </tr>   
      <tr>
        <td>

<table width="100%" border="0" cellspacing="0" cellpadding="2" class="dataTableRow">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent" align="left" width="75%">注意事項</td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_MODULE; ?></td>
    <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_AMOUNT; ?></td>
    <td class="dataTableHeadingContent"width="1"><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
  </tr>
<?php
  // Override order.php Class's Field Limitations
  //$totals_query = tep_db_query("select * from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . tep_db_input($oID) . "' order by sort_order");
  $order_totals = array();
  if ($_SESSION['create_order2']['orders_total']) 
  foreach ($_SESSION['create_order2']['orders_total'] as $totals) { 
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
  foreach ($order_totals as $k => $ot) {
    $TotalsArray[] = array("Name" => $ot['title'], "Price" => tep_display_currency(number_format((float)$ot['value'], 2, '.', '')), "Class" => $ot['class'], "TotalID" => $ot['orders_total_id']);
    $TotalsArray[] = array("Name" => "          ", "Price" => "", "Class" => "ot_custom", "TotalID" => "0");
  }
  
  array_pop($TotalsArray);
  //print_r($TotalsArray);
  
  foreach ($TotalsArray as $TotalIndex => $TotalDetails) {
    $TotalStyle = "smallText";
    if ($TotalDetails["Class"] == "ot_total") {
      echo '  <tr>' . "\n" .
           '    <td align="left" class="' . $TotalStyle . '">合計金額が合っているか必ず確認してください。</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . $TotalDetails["Name"] . '</b></td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . $currencies->ot_total_format($TotalDetails["Price"], true, $order['currency'], $order['currency_value']) . '</b>' . 
                "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</b></td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
           '  </tr>' . "\n";
    } elseif ($TotalDetails["Class"] == "ot_subtotal") {
      echo '  <tr>' . "\n" .
           '    <td align="left" class="' . $TotalStyle . '"><table><tr class="smalltext"><td><font color="red">※</font>&nbsp;コピペ用:</td><td>調整額</td><td>事務手数料</td><td>値引き</td></tr></table></td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . $TotalDetails["Name"] . '</b></td>' .
           '    <td align="right" class="' . $TotalStyle . '"><b>' . $currencies->format($TotalDetails["Price"], true, $order['currency'], $order['currency_value']) . '</b>' . 
                "<input name='update_totals[$TotalIndex][title]' type='hidden' value='" . trim($TotalDetails["Name"]) . "' size='" . strlen($TotalDetails["Name"]) . "' >" . 
                "<input name='update_totals[$TotalIndex][value]' type='hidden' value='" . $TotalDetails["Price"] . "' size='6' >" . 
                "<input name='update_totals[$TotalIndex][class]' type='hidden' value='" . $TotalDetails["Class"] . "'>\n" . 
                "<input type='hidden' name='update_totals[$TotalIndex][total_id]' value='" . $TotalDetails["TotalID"] . "'>" . '</b></td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>' . tep_draw_separator('pixel_trans.gif', '1', '17') . '</b>' . 
           '  </tr>' . "\n".
           '  <tr>' . "\n" .
           '    <td align="left" class="' . $TotalStyle . '">&nbsp;</td>' . 
           '    <td align="right" class="' . $TotalStyle . '"><b>'.TEXT_CODE_HANDLE_FEE.'</b></td>' .
           '    <td align="right" class="' . $TotalStyle . '"><b>' . $currencies->format($order["code_fee"]) . '</b><input type="hidden" name="payment_code_fee" value="'.$order["code_fee"].'">' . 
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
             '    <td align="left" class="' . $TotalStyle . '">このお客様はゲストです。ポイント割引の入力はできません。</td>' . 
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
           '    <td align="left" class="' . $TotalStyle . '">値引きする場合は、−（マイナス）符号を入力してください。</td>' . 
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
<span class='smalltext'><font color="red">ヒント:</font>&nbsp;価格構成要素を削除する場合は金額に「0」と入力して更新してください。</span>
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
              <td class="main" bgcolor="#FFDDFF" height="25"><font color="red">重要:</font>&nbsp;<b>価格構成要素を変更した場合は「<font color="red">注文内容確認</font>」ボタンをクリックして合計金額が一致するか確認してください。&nbsp;⇒</b></td>
              <td class="main" bgcolor="#FFBBFF" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF99FF" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF77FF" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF55FF" width="120" align="center"><INPUT type="button" value=" 注文内容確認 " onClick="update_price()"></td>
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
        <td class="SubTitle">4. 注文ステータス、コメント通知</td>
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
          <td class="main">--&nbsp;&nbsp;（初期値）</td>
        </tr>
        <tr>
          <td class="main"><b>メール送信:</b></td>
          <td class="main"><table bgcolor="red" cellspacing="5"><tr><td><?php echo tep_draw_checkbox_field('notify', '', false); ?></td></tr></table></td>
        </tr>
        <?php if($CommentsWithStatus) { ?>
        <tr>
          <td class="main"><b>コメント記録:</b></td>
          <td class="main"><?php echo tep_draw_checkbox_field('notify_comments', '', false); ?>&nbsp;&nbsp;<b style="color:#FF0000;">←ここはチェックしないように</b></td>
        </tr>
        <?php } ?>
      </table>
    </td>
    <td class="main" width="10">&nbsp;</td>
    <td class="main">
    こちらに入力した文章はメール本文に挿入されます。<br>
    <?
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
        <td class="SubTitle">5. データを更新</td>
    </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '1'); ?></td>
      </tr>   
      <tr>
      <td>
          <table width="100%" border="0" cellpadding="2" cellspacing="1">
            <tr>
              <td class="main" bgcolor="#FFDDFF"><b>最終確認はしましたか？</b>&nbsp;<?php echo HINT_PRESS_UPDATE; ?></td>
              <td class="main" bgcolor="#FFBBFF" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF99FF" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF77FF" width="10">&nbsp;</td>
              <td class="main" bgcolor="#FF55FF" width="120" align="center"><?php echo tep_image_submit('button_update.gif', IMAGE_UPDATE); ?></td>
          </tr>
          </table>
    </td>
      </tr>
    <tr>
      <td>
<table width="100%" cellspacing="0" cellpadding="2">
  <tr class="smalltext"><td valign="top" colspan="2"><font color="red">※</font>&nbsp;コピペ用フレーズです。トリプルクリックをすると全選択できます。</td></tr>
  <tr class="smalltext" bgcolor="#999999"><td>DBに登録されているキャラクター以外の場合</td><td>予備</td></tr>
  <tr class="smalltext" bgcolor="#CCCCCC">
  <td valign="top">【重要】弊社キャラクター【】がお取り引きに伺います。</td>
  <td valign="top">
    予備
  </td>
  </tr>
</table>
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
              <td class="pageHeading" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_EDIT_NEW_ORDERS2, tep_get_all_get_params(array('action'))) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>'; ?></td>
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

      echo "<tr><td colspan='3'>&nbsp;</td></tr>\n";
    }

    // Step 4: Confirm
    if($step > 3)
    {
      echo "<tr class=\"dataTableRow\"><form action='$PHP_SELF?oID=$oID&action=$action' method='POST' onsubmit='return check_add()' >\n";
      echo "<td class='dataTableContent' align='right'><b>" . ADDPRODUCT_TEXT_STEP . " 4: </b></td>";
      echo '<td class="dataTableContent" valign="top">' . ADDPRODUCT_TEXT_CONFIRM_QUANTITY . '<input name="add_product_quantity" size="2" value="1">&nbsp;個&nbsp;&nbsp;単価<input name="add_product_price" id="add_product_price" size="4" value="0">&nbsp;円&nbsp;&nbsp;キャラクター名:&nbsp;<input type="hidden" name="dummy" value="あいうえお眉幅"><input name="add_product_character" size="20" value=""></td>';
      echo "<td class='dataTableContent' align='center'><input type='submit' value='" . ADDPRODUCT_TEXT_CONFIRM_ADDNOW . "'>";

      if(IsSet($add_product_options))
      {
        foreach($add_product_options as $option_id => $option_value_id)
        {
          print "<input type='hidden' name='add_product_options[$option_id]' value='$option_value_id'>";
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
