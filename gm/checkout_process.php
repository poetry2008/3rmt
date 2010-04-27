<?php
/*
  JP、GM共通ファイル
*/

  include('includes/application_top.php');

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_CHECKOUT_PAYMENT));
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }
  
//  if (!tep_session_is_registered('sendto')) {
//    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
//  }

  if ( (tep_not_null(MODULE_PAYMENT_INSTALLED)) && (!tep_session_is_registered('payment')) ) {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
 }

// avoid hack attempts during the checkout procedure by checking the internal cartID
  if (isset($cart->cartID) && tep_session_is_registered('cartID')) {
    if ($cart->cartID != $cartID) {
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
  }

// Stock Check
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      if (tep_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
        tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
        break;
      }
    }
  }

  include(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PROCESS);

// load selected payment module
  require(DIR_WS_CLASSES . 'payment.php');
  $payment_modules = new payment($payment);

// load the selected shipping module
/*
  require(DIR_WS_CLASSES . 'shipping.php');
  $shipping_modules = new shipping($shipping);
  // add for Japanese update
  if (isset($shipping['timespec'])) {
    $comments = '['.TEXT_TIME_SPECIFY.$shipping['timespec'].']'
       ."\n".$comments;
  }
*/
  
  # OrderNo
  $insert_id = date("Ymd") . '-' . date("His") . ds_makeRandStr(2);

  # Check
  $NewOidQuery = tep_db_query("select count(*) as cnt from ".TABLE_ORDERS." where orders_id = '".$insert_id."' and site_id = '".SITE_ID."'");
  $NewOid = tep_db_fetch_array($NewOidQuery);
  if($NewOid['cnt'] == 0) {
    # OrderNo
    $insert_id = date("Ymd") . '-' . date("His") . ds_makeRandStr(2);
  }
  
  # load the selected shipping module(convenience_store)
  if ($_SESSION['payment'] == 'convenience_store') {
    $convenience_sid = str_replace('-', "", $insert_id);
  
    $pay_comments = '取引コード' . $convenience_sid ."\n";
  $pay_comments .= '郵便番号:' . $_POST['convenience_store_zip_code'] ."\n";
  $pay_comments .= '住所1:' . $_POST['convenience_store_address1'] ."\n";
  $pay_comments .= '住所2:' . $_POST['convenience_store_address2'] ."\n";
  $pay_comments .= '氏:' . $_POST['convenience_store_l_name'] ."\n";
  $pay_comments .= '名:' . $_POST['convenience_store_f_name'] ."\n";
  $pay_comments .= '電話番号:' . $_POST['convenience_store_tel'] ."\n";
  $pay_comments .= '接続URL:' . tep_href_link('convenience_store_chk.php', 'sid=' . $convenience_sid, 'SSL');
  
  $comments = $pay_comments ."\n".$comments;
  }
  
  require(DIR_WS_CLASSES . 'order.php');
  $order = new order;

// load the before_process function from the payment modules
  $payment_modules->before_process();

  require(DIR_WS_CLASSES . 'order_total.php');
  $order_total_modules = new order_total;

  $order_totals = $order_total_modules->process();
  
  
  # Select
  $cnt = strlen($NewOid);

// 2003-06-06 add_telephone
  $sql_data_array = array('orders_id' => $insert_id,
                          'customers_id' => $customer_id,
              'customers_name' => tep_get_fullname($order->customer['firstname'],$order->customer['lastname']),
              'customers_name_f' => tep_get_fullname($order->customer['firstname_f'],$order->customer['lastname_f']),
                          'customers_company' => $order->customer['company'],
                          'customers_street_address' => $order->customer['street_address'],
                          'customers_suburb' => $order->customer['suburb'],
                          'customers_city' => $order->customer['city'],
                          'customers_postcode' => $order->customer['postcode'], 
                          'customers_state' => $order->customer['state'], 
                          'customers_country' => $order->customer['country']['title'], 
                          'customers_telephone' => $order->customer['telephone'],
              //'customers_fax' => $order->customer['fax'],
                          'customers_email_address' => $order->customer['email_address'],
                          'customers_address_format_id' => $order->customer['format_id'], 
                          'delivery_name' => tep_get_fullname($order->delivery['firstname'],$order->delivery['lastname']),
              'delivery_name_f' => tep_get_fullname($order->delivery['firstname_f'],$order->delivery['lastname_f']),
                          'delivery_company' => $order->delivery['company'],
                          'delivery_street_address' => $order->delivery['street_address'], 
                          'delivery_suburb' => $order->delivery['suburb'], 
                          'delivery_city' => $order->delivery['city'], 
                          'delivery_postcode' => $order->delivery['postcode'], 
                          'delivery_state' => $order->delivery['state'], 
                          'delivery_country' => $order->delivery['country']['title'], 
                          'delivery_telephone' => $order->delivery['telephone'], 
                          'delivery_address_format_id' => $order->delivery['format_id'], 
                          'billing_name' => tep_get_fullname($order->billing['firstname'],$order->billing['lastname']),
              'billing_name_f' => tep_get_fullname($order->billing['firstname_f'],$order->billing['lastname_f']),
                          'billing_company' => $order->billing['company'],
                          'billing_street_address' => $order->billing['street_address'], 
                          'billing_suburb' => $order->billing['suburb'], 
                          'billing_city' => $order->billing['city'], 
                          'billing_postcode' => $order->billing['postcode'], 
                          'billing_state' => $order->billing['state'], 
                          'billing_country' => $order->billing['country']['title'], 
                          'billing_telephone' => $order->billing['telephone'], 
                          'billing_address_format_id' => $order->billing['format_id'], 
                          'payment_method' => $order->info['payment_method'], 
                          'cc_type' => $order->info['cc_type'], 
                          'cc_owner' => $order->info['cc_owner'], 
                          'cc_number' => $order->info['cc_number'], 
                          'cc_expires' => $order->info['cc_expires'], 
                          'date_purchased' => 'now()', 
                          'orders_status' => $order->info['order_status'], 
                          'currency' => $order->info['currency'], 
                          'currency_value' => $order->info['currency_value'],
              'torihiki_houhou' => $torihikihouhou,
              'site_id' => SITE_ID,
              'torihiki_date' => $insert_torihiki_date
              );
  tep_db_perform(TABLE_ORDERS, $sql_data_array);
  //$insert_id = tep_db_insert_id();
  for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) {
    $sql_data_array = array('orders_id' => $insert_id,
                            'title' => $order_totals[$i]['title'],
                            'text' => $order_totals[$i]['text'],
                            'value' => $order_totals[$i]['value'], 
                            'class' => $order_totals[$i]['code'], 
                            'sort_order' => $order_totals[$i]['sort_order']);
    tep_db_perform(TABLE_ORDERS_TOTAL, $sql_data_array);
  }

  $customer_notification = (SEND_EMAILS == 'true') ? '1' : '0';
  $sql_data_array = array('orders_id' => $insert_id, 
                          'orders_status_id' => $order->info['order_status'], 
                          'date_added' => 'now()', 
                          'customer_notified' => $customer_notification,
                          'comments' => $order->info['comments']);
  tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
  
  # 追加分（買取情報）
  if(tep_session_is_registered('bank_name')) {
    $bbbank = TEXT_BANK_NAME . '：' . $bank_name . "\n";
    $bbbank .= TEXT_BANK_SHITEN . '：' . $bank_shiten . "\n";
    $bbbank .= TEXT_BANK_KAMOKU . '：' . $bank_kamoku . "\n";
    $bbbank .= TEXT_BANK_KOUZA_NUM . '：' . $bank_kouza_num . "\n";
    $bbbank .= TEXT_BANK_KOUZA_NAME . '：' . $bank_kouza_name;

  $sql_data_array = array('orders_id' => $insert_id, 
                            'orders_status_id' => $order->info['order_status'], 
                            'date_added' => 'now()', 
                            'customer_notified' => $customer_notification,
                            'comments' => $bbbank);
    tep_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);
  }

// initialized for the email confirmation
  $products_ordered = '';
  $subtotal = 0;
  $total_tax = 0;

  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
// Stock Update - Joao Correia
    if (STOCK_LIMITED == 'true') {
      if (DOWNLOAD_ENABLED == 'true') {
        $stock_query_raw = "SELECT products_quantity, pad.products_attributes_filename 
                            FROM " . TABLE_PRODUCTS . " p
                            LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                             ON p.products_id=pa.products_id
                            LEFT JOIN " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                             ON pa.products_attributes_id=pad.products_attributes_id
                            WHERE p.products_id = '" . tep_get_prid($order->products[$i]['id']) . "'";
// Will work with only one option for downloadable products
// otherwise, we have to build the query dynamically with a loop
        $products_attributes = $order->products[$i]['attributes'];
        if (is_array($products_attributes)) {
          $stock_query_raw .= " AND pa.options_id = '" . $products_attributes[0]['option_id'] . "' AND pa.options_values_id = '" . $products_attributes[0]['value_id'] . "'";
        }
        $stock_query = tep_db_query($stock_query_raw);
      } else {
        $stock_query = tep_db_query("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
      }
      if (tep_db_num_rows($stock_query) > 0) {
        $stock_values = tep_db_fetch_array($stock_query);
// do not decrement quantities if products_attributes_filename exists
        if ((DOWNLOAD_ENABLED != 'true') || (!$stock_values['products_attributes_filename'])) {
          $stock_left = $stock_values['products_quantity'] - $order->products[$i]['qty'];
        } else {
          $stock_left = $stock_values['products_quantity'];
        }
        tep_db_query("update " . TABLE_PRODUCTS . " set products_quantity = '" . $stock_left . "' where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
        if ($stock_left < 1) {
      // 在庫切れでも商品は表示
      //tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '0' where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
        ########## 在庫切れのメール通知　##############
        if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
                $zaiko_alart = '商品名　　-　　　型番'."\n";
        $zaiko_alart .= tep_get_products_name(tep_get_prid($order->products[$i]['id'])).'('.$order->products[$i]['model'].')'."\n";
        $zaiko_alart .= HTTPS_SERVER.'/admin/categories.php?search='.urlencode(tep_get_products_name(tep_get_prid($order->products[$i]['id'])))."\n\n";
                tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, ZAIKO_ALART_TITLE, ZAIKO_ARART_BODY.$zaiko_alart, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');
      }
    }
      }
    }

// Update products_ordered (for bestsellers list)
    tep_db_query("update " . TABLE_PRODUCTS . " set products_ordered = products_ordered + " . sprintf('%d', $order->products[$i]['qty']) . " where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");

    //character
/*
  //$character = tep_get_prid($order->products[$i]['id']);
  $character = $order->products[$i]['id'];
  if(array_key_exists($character,$_POST['character'])){
    $chara = $_POST['character'][$character];
  }else{
    $chara = "";
  }
*/
  $chara = '';
  $character_id = $order->products[$i]['id'];
  foreach($_SESSION['character'] as $st => $en) {
    if($_SESSION['character'][$character_id] == $_SESSION['character'][$st]) {
      $chara = $_SESSION['character'][$character_id];
    }
  }
  
  $sql_data_array = array('orders_id' => $insert_id, 
                            'products_id' => tep_get_prid($order->products[$i]['id']), 
                            'products_model' => $order->products[$i]['model'], 
                            'products_name' => $order->products[$i]['name'], 
                            'products_price' => $order->products[$i]['price'], 
                            'final_price' => $order->products[$i]['final_price'], 
                            'products_tax' => $order->products[$i]['tax'], 
                            'products_quantity' => $order->products[$i]['qty'],
              'products_character' =>  stripslashes($chara));
    tep_db_perform(TABLE_ORDERS_PRODUCTS, $sql_data_array);
    $order_products_id = tep_db_insert_id();

//------insert customer choosen option to order--------
    $attributes_exist = '0';
    $products_ordered_attributes = '';
    if (isset($order->products[$i]['attributes'])) {
      $attributes_exist = '1';
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        if (DOWNLOAD_ENABLED == 'true') {
          $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_attributes_id, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename 
                               from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa 
                               left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                on pa.products_attributes_id=pad.products_attributes_id
                               where pa.products_id = '" . $order->products[$i]['id'] . "' 
                                and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "' 
                                and pa.options_id = popt.products_options_id 
                                and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "' 
                                and pa.options_values_id = poval.products_options_values_id 
                                and popt.language_id = '" . $languages_id . "' 
                                and poval.language_id = '" . $languages_id . "'";
          $attributes = tep_db_query($attributes_query);
        } else {
          $attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_attributes_id from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . $order->products[$i]['id'] . "' and pa.options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . $languages_id . "' and poval.language_id = '" . $languages_id . "'");
        }
        $attributes_values = tep_db_fetch_array($attributes);
    
    //---------------------------------------
    // オプションの在庫数減処理 - 2005.09.20
    //---------------------------------------
    if (STOCK_LIMITED == 'true') {
      $zaiko = $attributes_values['products_at_quantity']-$order->products[$i]['qty'];
      tep_db_query("update ".TABLE_PRODUCTS_ATTRIBUTES." set products_at_quantity = '". $zaiko ."' where products_id = '" . tep_get_prid($order->products[$i]['id']) . "' and options_id = '" . $order->products[$i]['attributes'][$j]['option_id'] . "' and options_values_id = '" . $order->products[$i]['attributes'][$j]['value_id'] . "'");

          //全てのオプション値が「0」担った時点で商品のステータスを（falseに）更新
      $attributes_stock_check_query = tep_db_query("select * from ".TABLE_PRODUCTS_ATTRIBUTES." where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
      $stock_cnt = 0;
      while($attributes_stock_check = tep_db_fetch_array($attributes_stock_check_query)) {
        $stock_cnt += $attributes_stock_check['products_at_quantity'];
      }
    
      if($stock_cnt > 0) {
        //Not process
      } else {
            //Update products_status(TABLE: PRODUCTS)
      tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '0' where products_id = '" . tep_get_prid($order->products[$i]['id']) . "'");
      
      
      }
    }
    //--------------------------------------END

        $sql_data_array = array('orders_id' => $insert_id, 
                                'orders_products_id' => $order_products_id, 
                                'products_options' => $attributes_values['products_options_name'],
                                'products_options_values' => $attributes_values['products_options_values_name'], 
                                'options_values_price' => $attributes_values['options_values_price'], 
                                'price_prefix' => $attributes_values['price_prefix'],
                'attributes_id'  => $attributes_values['products_attributes_id']);
        tep_db_perform(TABLE_ORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);

        if ((DOWNLOAD_ENABLED == 'true') && isset($attributes_values['products_attributes_filename']) && tep_not_null($attributes_values['products_attributes_filename'])) {
          $sql_data_array = array('orders_id' => $insert_id, 
                                  'orders_products_id' => $order_products_id, 
                                  'orders_products_filename' => $attributes_values['products_attributes_filename'], 
                                  'download_maxdays' => $attributes_values['products_attributes_maxdays'], 
                                  'download_count' => $attributes_values['products_attributes_maxcount']);
          tep_db_perform(TABLE_ORDERS_PRODUCTS_DOWNLOAD, $sql_data_array);
        }
        $products_ordered_attributes .= "\n" 
        . $attributes_values['products_options_name'] 
        . str_repeat('　',intval((18-strlen($attributes_values['products_options_name']))/2))
        . '：' . $attributes_values['products_options_values_name'];
      }
    }
//------insert customer choosen option eof ----
    $total_weight += ($order->products[$i]['qty'] * $order->products[$i]['weight']);
    $total_tax += tep_calculate_tax($total_products_price, $products_tax) * $order->products[$i]['qty'];
    $total_cost += $total_products_price;
  
    $products_ordered .= '注文商品　　　　　：' . $order->products[$i]['name'];
    
  if(tep_not_null($order->products[$i]['model'])) {
    $products_ordered .= ' (' . $order->products[$i]['model'] . ')';
  }
  
    $_product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_attention_1,pd.products_attention_2,pd.products_attention_3,pd.products_attention_4,pd.products_attention_5,pd.products_description, p.products_model, p.products_quantity, p.products_image,p.products_image2,p.products_image3, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id, p.products_bflag, p.products_cflag, p.products_small_sum from " . TABLE_PRODUCTS . " p, " .  TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . $order->products[$i]['id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . $languages_id . "' and pd.site_id = '".SITE_ID."'");
    tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = products_viewed+1 where products_id = '" . (int)$_GET['products_id'] . "' and language_id = '" . $languages_id . "' and site_id = '".SITE_ID."'");
    $product_info = tep_db_fetch_array($_product_info_query);
    $data1 = explode("//", $product_info['products_attention_1']);
  
  $products_ordered .= $products_ordered_attributes . "\n";
  //$products_ordered .= '個数          :' . $order->products[$i]['qty'] . (!empty($data1[0]) ? ' x '. $data1[1] : '') . "\n";
  $products_ordered .= '個数　　　　　　　：' . $order->products[$i]['qty'] . '個' . tep_get_full_count($order->products[$i]['qty'], $data1[1]) . "\n";
  $products_ordered .= '単価　　　　　　　：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax']) . "\n";
  $products_ordered .= '小計　　　　　　　：' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . "\n";
  if(tep_not_null($chara)) {
    $products_ordered .= 'キャラクター名　　：' . (EMAIL_USE_HTML === 'true' ? htmlspecialchars(stripslashes($chara)) : stripslashes($chara)) . "\n";
  }
  $products_ordered .= "------------------------------------------\n";
  if (tep_get_cflag_by_product_id($order->products[$i]['id'])) {
      if (tep_get_bflag_by_product_id($order->products[$i]['id'])) {
        $products_ordered .= "※ 当社キャラクター名は、お取引10分前までに電子メールにてお知らせいたします。\n\n";
      } else {
        $products_ordered .= "※ 当社キャラクター名は、お支払い確認後に電子メールにてお知らせいたします。\n\n";
      }
    }
 }

  # メール本文整形 --------------------------------------
  $email_order = '';
  
  $otq = tep_db_query("select * from ".TABLE_ORDERS_TOTAL." where class = 'ot_total' and orders_id = '".$insert_id."'");
  $ot = tep_db_fetch_array($otq);
  
  $email_order .= tep_get_fullname($order->customer['firstname'],$order->customer['lastname']) . '様' . "\n\n";
  $email_order .= 'この度は、' . STORE_NAME . 'をご利用いただき、誠にあり' . "\n";
  $email_order .= 'がとうございます。' . "\n";
  $email_order .= '下記の内容にてご注文を承りましたので、ご確認ください。' . "\n";
  $email_order .= 'ご不明な点がございましたら、注文番号をご確認の上、' . "\n";
  $email_order .= '「' . STORE_NAME . '」までお問い合わせください。' . "\n\n";
  $email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
  //$email_order .= '▼お支払金額　　　：' . strip_tags($ot['text']) . "\n\n";
  $email_order .= '▼注文番号　　　　：' . $insert_id . "\n";
  $email_order .= '▼注文日　　　　　：' . strftime(DATE_FORMAT_LONG) . "\n";
  $email_order .= '▼お名前　　　　　：' . tep_get_fullname($order->customer['firstname'],$order->customer['lastname']) . "\n";
  $email_order .= '▼メールアドレス　：' . $order->customer['email_address'] . "\n";
  $email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
  if ($point > 0) {
  $email_order .= '▼ポイント割引　　：' . $point . '円' . "\n";
  }
  $email_order .= '▼お支払金額　　　：' . strip_tags($ot['text']) . "\n";
  if (is_object($$payment)) {
    $payment_class = $$payment;
  $email_order .= '▼お支払方法　　　：' . $payment_class->title . "\n";
  }
  
  if ($payment_class->email_footer) { 
    $email_order .= $payment_class->email_footer . "\n";
  }
  
  if(tep_not_null($bbbank)) {
    $email_order .= '▼お支払先金融機関' . "\n";
  $email_order .= $bbbank . "\n";
  $email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n\n";
  //$email_order .= '・本メールに記載された当社キャラクター宛に商品をトレードしてください。' . "\n";
  $email_order .= '・当社にて商品の受領確認がとれましたら代金お支払い手続きに入ります。' . "\n";
  $email_order .= '・本メール送信後7日以内に取引が完了できない場合、' . "\n";
  $email_order .= '　当社は、お客様がご注文を取り消されたものとして取り扱います。' . "\n\n";
  }
  
  //$email_order .= '------------------------------------------' . "\n";
  $email_order .= '▼注文商品' . "\n";
  $email_order .= '------------------------------------------' . "\n";
  $email_order .= $products_ordered . "\n";

  //$email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_order .= '▼取引日時　　　　：' . str_string($date) . $hour . '時' . $min . '分　（24時間表記）' . "\n";
  $email_order .= '　　　　　　　　　：' . $torihikihouhou . "\n";
  //$email_order .= '※ 取引の混雑により5分程度お待ちいただく場合がございます。予めご了承ください。' . "\n\n";
  
  $email_order .= '▼備考　　　　　　：' . "\n";
  if ($order->info['comments']) {
    $email_order .= tep_db_output($order->info['comments']) . "\n";
  }
  //$email_order .= '━━━━━━━━━━━━━━━━━━━━━' . "\n";
  
  
  if ($_SESSION['payment'] == 'convenience_store') {
    $email_order .= '■コンビニ決済情報' . "\n";
  $email_order .= '郵便番号:' . $_POST['convenience_store_zip_code'] ."\n";
  $email_order .= '住所    :' . $_POST['convenience_store_address1'] . " " . $_POST['convenience_store_address2'] ."\n";
  $email_order .= 'お名前  :' . $_POST['convenience_store_l_name'] . " " . $_POST['convenience_store_f_name'] ."\n";
  $email_order .= '電話番号:' . $_POST['convenience_store_tel'] . "\n\n";
  }
  
  $email_order .= "\n\n\n";
  $email_order .= '[ご連絡・お問い合わせ先]━━━━━━━━━━━━' . "\n";
  $email_order .= '株式会社 iimy' . "\n";
  $email_order .= SUPPORT_EMAIL_ADDRESS . "\n";
  $email_order .= HTTP_SERVER . "\n";
  $email_order .= '━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  
  # メール本文整形 --------------------------------------
  
  // 2003.03.08 Edit Japanese osCommerce
  tep_mail(tep_get_fullname($order->customer['firstname'],$order->customer['lastname']), $order->customer['email_address'], EMAIL_TEXT_SUBJECT, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');
  
  if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
    tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, EMAIL_TEXT_SUBJECT2, $email_order, tep_get_fullname($order->customer['firstname'],$order->customer['lastname']), $order->customer['email_address'], '');
  }
  
  # 印刷用メール本文 ----------------------------
  $email_printing_order = '';
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= 'サイト名　　　　：' . STORE_NAME . "\n";
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= '取引日時　　　　：' . str_string($date) . $hour . '時' . $min . '分　（24時間表記）' . "\n";
  $email_printing_order .= 'オプション　　　：' . $torihikihouhou . "\n";
  $email_printing_order .= '------------------------------------------------------------------------' . "\n";
  $email_printing_order .= '日時変更　　　　：200 年  月  日  時  分' . "\n";
  $email_printing_order .= '日時変更　　　　：200 年  月  日  時  分' . "\n";
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= '注文者名　　　　：' . tep_get_fullname($order->customer['firstname'],$order->customer['lastname']) . '様' . "\n";
  $email_printing_order .= '注文番号　　　　：' . $insert_id . "\n";
  $email_printing_order .= '注文日　　　　　：' . strftime(DATE_FORMAT_LONG) . "\n";
  $email_printing_order .= 'メールアドレス　：' . $order->customer['email_address'] . "\n";
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  if ($point > 0) {
  $email_printing_order .= '□ポイント割引　　：' . $point . '円' . "\n";
  }
  $email_printing_order .= 'お支払金額　　　：' . strip_tags($ot['text']) . "\n";
  if (is_object($$payment)) {
    $payment_class = $$payment;
  $email_printing_order .= 'お支払方法　　　：' . $payment_class->title . "\n";
  }
  
  if(tep_not_null($bbbank)) {
    $email_printing_order .= 'お支払先金融機関' . "\n";
  $email_printing_order .= $bbbank . "\n";
  }
  
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= $products_ordered;

  $email_printing_order .= '備考　　　　　　：' . "\n";
  if ($order->info['comments']) {
    $email_printing_order .= tep_db_output($order->info['comments']) . "\n";
  }

  if ($_SESSION['payment'] == 'convenience_store') {
    $email_printing_order .= '■コンビニ決済情報' . "\n";
  $email_printing_order .= '郵便番号:' . $_POST['convenience_store_zip_code'] ."\n";
  $email_printing_order .= '住所    :' . $_POST['convenience_store_address1'] . " " . $_POST['convenience_store_address2'] ."\n";
  $email_printing_order .= 'お名前  :' . $_POST['convenience_store_l_name'] . " " . $_POST['convenience_store_f_name'] ."\n";
  $email_printing_order .= '電話番号:' . $_POST['convenience_store_tel'] . "\n";
  }

  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= 'IPアドレス　　　　　　：' . $_SERVER["REMOTE_ADDR"] . "\n";
  $email_printing_order .= 'ホスト名　　　　　　　：' . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
  $email_printing_order .= 'ユーザーエージェント　：' . $_SERVER["HTTP_USER_AGENT"] . "\n";
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= '信用調査' . "\n";
  
  $credit_inquiry_query = tep_db_query("select customers_fax, customers_guest_chk from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "' and site_id = '".SITE_ID."'");
    $credit_inquiry = tep_db_fetch_array($credit_inquiry_query);
  
  $email_printing_order .= $credit_inquiry['customers_fax'] . "\n";
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  $email_printing_order .= '注文履歴　　　　　　　：';
  
  if ($credit_inquiry['customers_guest_chk'] == '1') { $email_printing_order .= 'ゲスト'; } else { $email_printing_order .= '会員'; }
  
  $email_printing_order .= "\n";
  
  $order_history_query_raw = "select o.orders_id, o.customers_name, o.customers_id, o.date_purchased, s.orders_status_name, ot.text as order_total from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id), " . TABLE_ORDERS_STATUS . " s where o.customers_id = '" . tep_db_input($customer_id) . "' and o.orders_status = s.orders_status_id and s.language_id = '" . $languages_id . "' and ot.class = 'ot_total' order by o.date_purchased DESC limit 0,5";  
  $order_history_query = tep_db_query($order_history_query_raw);
  while ($order_history = tep_db_fetch_array($order_history_query)) {
  $email_printing_order .= $order_history['date_purchased'] . '　　' . tep_output_string_protected($order_history['customers_name']) . '　　' . strip_tags($order_history['order_total']) . '　　' . $order_history['orders_status_name'] . "\n";
  }
  
  $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n\n\n";
  
  

  if ($payment_class->title === '銀行振込(買い取り)') {
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
// send emails to other people

  if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
    tep_mail('', 'printing_order@iimy.co.jp', STORE_NAME, $email_printing_order, tep_get_fullname($order->customer['firstname'],$order->customer['lastname']), $order->customer['email_address'], '');
  }

  // Include OSC-AFFILIATE 
  //require(DIR_WS_INCLUDES . 'affiliate_checkout_process.php');

  //$ac_total = tep_add_tax($affiliate_total,0);
  
  
  //tep_session_register('ac_total');
  //var_dump($ac_total);

// load the after_process function from the payment modules
  $payment_modules->after_process();

  $cart->reset(true);

//Add point
  if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
    if(MODULE_ORDER_TOTAL_POINT_ADD_STATUS == '0') {
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " .  intval($get_point - $point) . " where customers_id = " . $customer_id . " and site_id = ".SITE_ID);
    } else {
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point - " .  intval($point) . " where customers_id = " . $customer_id . " and site_id = " . SITE_ID);
  }
  }
  
  
// ゲスト購入の場合はポイントリセット
  if($guestchk == '1') {
    tep_db_query("update ".TABLE_CUSTOMERS." set point = '0' where customers_id = '".$customer_id."' and site_id = '".SITE_ID."'");
  }  
  
  

// unregister session variables used during checkout
  tep_session_unregister('sendto');
  tep_session_unregister('billto');
  tep_session_unregister('shipping');
  tep_session_unregister('payment');
  tep_session_unregister('comments');
  if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  tep_session_unregister('point');
  tep_session_unregister('get_point');
  }
  
  tep_session_unregister('torihikihouhou');
  tep_session_unregister('date');
  tep_session_unregister('hour');
  tep_session_unregister('min');
  tep_session_unregister('insert_torihiki_date');
  
  tep_session_unregister('bank_name');
  tep_session_unregister('bank_shiten');
  tep_session_unregister('bank_kamoku');
  tep_session_unregister('bank_kouza_num');
  tep_session_unregister('bank_kouza_name');
  
  #convenience_store
  unset($_SESSION['character']);
  
  $pr = '?SID=' . $convenience_sid;

  tep_redirect(FILENAME_CHECKOUT_SUCCESS . $pr,'T');
    
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
