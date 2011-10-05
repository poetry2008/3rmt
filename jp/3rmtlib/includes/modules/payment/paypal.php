<?php
/*
  $Id$
*/
//ペイパル実験
  class paypal{
    var $site_id, $code, $title, $description, $enabled, $n_fee, $s_error, $email_footer;

// class constructor
    function paypal($site_id = 0) {
      global $order, $_GET;
      
      $this->site_id = $site_id;

      $this->code        = 'paypal';
      $this->title       = MODULE_PAYMENT_PAYPAL_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_PAYPAL_TEXT_DESCRIPTION;
      $this->explain     = MODULE_PAYMENT_PAYPAL_TEXT_EXPLAIN;
      $this->sort_order  = MODULE_PAYMENT_PAYPAL_SORT_ORDER;
      $this->enabled     = ((MODULE_PAYMENT_PAYPAL_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      //      $this->form_action_url = MODULE_PAYMENT_PAYPAL_CONNECTION_URL;
      $this->form_action_url = MODULE_PAYMENT_PAYPAL_CONNECTION_URL ;

      //      $this->form_action_url =      'https://api-3t.sandbox.paypal.com/nvp ';
    
    if(isset($_GET['submit_x']) || isset($_GET['submit_y'])){
      $_GET['payment_error'] = 'paypal';
    }
    
    $this->email_footer = MODULE_PAYMENT_PAYPAL_TEXT_EMAIL_FOOTER;
    }

// class methods
    function update_status() {
      global $order;
      if (!defined('MODULE_PAYMENT_PAYPAL_ZONE')) define('MODULE_PAYMENT_PAYPAL_ZONE', NULL);
      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_PAYPAL_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_PAYPAL_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function calc_fee($total_cost) {
      $table_fee = split("[:,]" , MODULE_PAYMENT_PAYPAL_COST);
      $f_find = false;
      $this->n_fee = 0;
      for ($i = 0; $i < count($table_fee); $i+=2) {
        if ($total_cost <= $table_fee[$i]) { 
          $additional_fee = $total_cost.$table_fee[$i+1]; 
          @eval("\$additional_fee = $additional_fee;"); 
          //$this->n_fee = $table_fee[$i+1]; 
          if (is_numeric($additional_fee)) {
            $this->n_fee = intval($additional_fee); 
          } else {
            $this->n_fee = 0; 
          }
          $f_find = true;
          break;
        }
      }
      if ( !$f_find ) {
        $this->s_error = MODULE_PAYMENT_PAYPAL_TEXT_OVERFLOW_ERROR;
      }

      return $f_find;
    }
    function selection() {
      global $currencies;
      global $order;
      
      $total_cost = $order->info['total'];
      $f_result = $this->calc_fee($total_cost); 
      $added_hidden = $f_result ? tep_draw_hidden_field('paypal_order_fee', $this->n_fee):tep_draw_hidden_field('paypal_order_fee_error', $this->s_error);
      
      if (!empty($this->n_fee)) {
        $s_message = $f_result ? (MODULE_PAYMENT_PAYPAL_TEXT_FEE . '&nbsp;' .  $currencies->format($this->n_fee)):('<font color="#FF0000">'.$this->s_error.'</font>'); 
      } else {
        $s_message = $f_result ? '':('<font color="#FF0000">'.$this->s_error.'</font>'); 
      }
      return array('id' => $this->code,
                   'module' => $this->title,
           'fields' => array(array('title' => $this->explain,'field' => ''),
                                     array('title' => $s_message, 'field' => $added_hidden) 
                                     ));
      //return array('id' => $this->code, 'module' => $this->title, 'fields' => array(array('title' => $this->explain,'field' => '')));
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      //$SESSION 处理
      global $currencies;
      global $_POST;
      global $order;
      //      var_dump($order);
      
      $s_result = !$_POST['paypal_order_fee_error'];
     
      if (!empty($_POST['paypal_order_fee'])) {
        //$s_message = $s_result ? (MODULE_PAYMENT_PAYPAL_TEXT_FEE . '&nbsp;' .  $currencies->format($_POST['paypal_order_fee'])):('<font color="#FF0000">'.$_POST['paypal_order_fee_error'].'</font>'); 
        $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['paypal_order_fee_error'].'</font>'); 
      } else {
        $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['paypal_order_fee_error'].'</font>'); 
      }
      
      if (!empty($_POST['paypal_order_fee'])) {
        return array(
            'title' => MODULE_PAYMENT_PAYPAL_TEXT_DESCRIPTION,
            'fields' => array(array('title' => MODULE_PAYMENT_PAYPAL_TEXT_PROCESS,
                                    'field' => ''),
                              array('title' => $s_message, 'field' => '')  
                       )           
            );
      } else {
        return array(
            'title' => MODULE_PAYMENT_PAYPAL_TEXT_DESCRIPTION,
            'fields' => array(array('title' => $s_message, 'field' => '')  
                       )           
            );
      }
      //return false;
    }

    
  function process_button() { 
      global $order, $currencies, $currency;   
      global $point,$cart,$languages_id;

      // 追加 - 2007.01.05 ----------------------------------------------
      $total = $order->info['total'];
      $f_result = $this->calc_fee($total); 
      if ((MODULE_ORDER_TOTAL_CODT_STATUS == 'true')
          && ($payment == 'cod_table')
          && isset($_POST['codt_fee'])
          && (0 < intval($_POST['codt_fee']))) {
        $total += intval($_POST['codt_fee']);
      }
    
    //Add point
      if ((MODULE_ORDER_TOTAL_POINT_STATUS == 'true')
          && (0 < intval($point))) {
        $total -= intval($point);
      }   
    
    if(MODULE_ORDER_TOTAL_CONV_STATUS == 'true' && ($payment == 'convenience_store')) {
        $total += intval($_POST['codt_fee']);
    }
          $total += intval($this->n_fee); 
          // 追加 - 2007.01.05 ----------------------------------------------
    
    #mail送信
    $mail_body = '仮クレジットカード注文です。'."\n\n";
    
    # ユーザー情報----------------------------
    $mail_body .= '━━━━━━━━━━━━━━━━━━━━━'."\n";
    $mail_body .= '▼注文番号　　　　：2007****-********'."\n";
    $mail_body .= '▼注文日　　　　　：' . tep_date_long(time())."\n";
    $mail_body .= '▼お名前　　　　　：' . $order->customer["lastname"] . ' ' . $order->customer["firstname"]."\n";
    $mail_body .= '▼メールアドレス　：' . $order->customer["email_address"]."\n";
    $mail_body .= '━━━━━━━━━━━━━━━━━━━━━'."\n";
    $mail_body .= '▼お支払金額　　　：' . $currencies->format($total) . "\n";
    $mail_body .= '▼お支払方法　　　：ペイパル決済'."\n";
    
    # 商品内容----------------------------
    $mail_body .= '▼注文商品'."\n";
    $mail_body .= "\t" . '------------------------------------------'."\n";

      $products = $cart->get_products();
      for ($i=0, $n=sizeof($products); $i<$n; $i++) {
        if (isset($products[$i]['attributes'])) {
          while (list($option, $value) = each($products[$i]['attributes'])) {
            echo tep_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
            $attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity
                                        from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa
                                        where pa.products_id = '" . $products[$i]['id'] . "'
                                         and pa.options_id = '" . $option . "'
                                         and pa.options_id = popt.products_options_id
                                         and pa.options_values_id = '" . $value . "'
                                         and pa.options_values_id = poval.products_options_values_id
                                         and popt.language_id = '" . $languages_id . "'
                                         and poval.language_id = '" . $languages_id . "'");
            $attributes_values = tep_db_fetch_array($attributes);

            $products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
            $products[$i][$option]['options_values_id'] = $value;
            $products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
            $products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
            $products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
        $products[$i][$option]['products_at_quantity'] = $attributes_values['products_at_quantity'];
          }
        }
      }
    
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      $char_id = $products[$i]['id'];
    $mail_body .= '・' . $products[$i]['name'] . '×' . $products[$i]['quantity'] . '(キャラクター名:' . $_SESSION["character"][$char_id] . ')' . "\n";
      $attributes_exist = ((isset($products[$i]['attributes'])) ? 1 : 0);

        if ($attributes_exist == 1) {
          reset($products[$i]['attributes']);
          while (list($option, $value) = each($products[$i]['attributes'])) {
            $mail_body .= '└' . $products[$i][$option]['products_options_name'] . ' ' . $products[$i][$option]['products_options_values_name'] . "\n";
          }
        }
    }

/*    
    foreach($order->products as $key => $val){
      $char_id = $val["id"];
    $mail_body .= "\t" . $val["name"] . '×' . $val["qty"] . '個（キャラクター名：' . $_SESSION["character"][$char_id] . '）' . "\n";
    $mail_body .= "\t" . 'オプション：不明・・・' . "\n";
    }
*/    
    $mail_body .= "\t" . '------------------------------------------'."\n";
    
    # 取引日時----------------------------
    $mail_body .= '▼取引日時　　　　：' . $_SESSION["insert_torihiki_date"] . "\n";
    $mail_body .= '　　　　　　　　　：' . $_SESSION["torihikihouhou"] . "\n";
    
    # ユーザーエージェントなど----------------------------
    $mail_body .= "\n\n";
    $mail_body .= '■IPアドレス　　　　　　：' . $_SERVER["REMOTE_ADDR"] . "\n";
    $mail_body .= '■ホスト名　　　　　　　：' . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
    $mail_body .= '■ユーザーエージェント　：' . $_SERVER["HTTP_USER_AGENT"] . "\n";
    
    tep_mail('管理者', SENTMAIL_ADDRESS, '仮クレカ注文', $mail_body, '', '');
    
    $today = date("YmdHis");


    //paypal需要的字段在以下购成
    $process_button_string =
           //tep_draw_hidden_field('cmd',"_xclick") .
      //                 tep_draw_hidden_field('method', 'SetExpressCheckout').
      //                 tep_draw_hidden_field('business', 'bobher_1299564524_biz@gmail.com').
      //                 tep_draw_hidden_field('paymentaction', 'authorization').
      //                 tep_draw_hidden_field('PWD', '1299564532').
      //                 tep_draw_hidden_field('USER', 'bobher_1299564524_biz_api1.gmail.com').
      //                 tep_draw_hidden_field('SIGNATURE', 'AHbu1UVi7OHLerk7cyw7SE57-EvSANiOenfnho-SXzWVX0EQFAHvySxI').
                       tep_draw_hidden_field('amount',$total) .//旧money
      //                 tep_draw_hidden_field('version','51').
      //                 tep_draw_hidden_field('currency_code', "JPY") 
                 //tep_draw_hidden_field('return','http://jp.gamelife.jp/GetExpressCheckoutDetails.php' ) .//return
      tep_draw_hidden_field('RETURNURL', trim(tep_href_link(MODULE_PAYMENT_OK_URL, '', 'SSL'))) .//return
      tep_draw_hidden_field('CANCELURL', trim(tep_href_link(MODULE_PAYMENT_NO_URL, '', 'SSL')));//return
                 //tep_draw_hidden_field('redirect_url', HTTPS_SERVER . tep_href_link(MODULE_PAYMENT_OK_URL, '', 'SSL')) .
                 //tep_draw_hidden_field('redirect_back_url', HTTPS_SERVER . tep_href_link(MODULE_PAYMENT_NO_URL, '', 'SSL'));
      $process_button_string .= tep_draw_hidden_field('paypal_order_message', htmlspecialchars($s_message)). tep_draw_hidden_field('paypal_order_fee', $_POST['paypal_order_fee']);
      //$process_button_string .= '<img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;">';
      return $process_button_string;
    }
  

    function before_process() {
      global $_POST;
      $this->email_footer = str_replace("\r\n", "\n", $_POST['paypal_order_message']);
      
      return false;
    }

    function after_process() {
      return false;
    }

    function output_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_PAYPAL_STATUS' and site_id = '".$this->site_id."'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('PAYPAL 支払いを有効にする', 'MODULE_PAYMENT_PAYPAL_STATUS', 'True', 'PAYPAL での支払いを受け付けますか?', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_PAYPAL_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('接続先URL', 'MODULE_PAYMENT_PAYPAL_CONNECTION_URL', '', 'テレコムクレジット申込受付画面URLの設定をします。', '6', '0', now(), ".$this->site_id.")");
      /*
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('番組コード', 'MODULE_PAYMENT_PAYPAL_KID', '', '番組コードの設定をします。', '6', '0', now(), ".$this->site_id.")");
      */
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('戻り先URL(正常時)', 'MODULE_PAYMENT_OK_URL', 'checkout_process.php', '戻り先URL(正常時)の設定をします。', '6', '0', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('戻り先URL(キャンセル時)', 'MODULE_PAYMENT_NO_URL', 'checkout_payment.php', '戻り先URL(キャンセル時)の設定をします。', '6', '0', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済手数料', 'MODULE_PAYMENT_PAYPAL_COST', '99999999999:*0', '決済手数料
例:
代金300円以下、30円手数料をとる場合　300:*0+30,
代金301～1000円以内、代金の2％の手数料をとる場合　999:*0.02,
代金1000円以上の場合、手数料を無料する場合　99999999:*0,
無限大の符号を使えないため、このサイトで存在可能性がない数値で使ってください。
300:*0+30では*0がなければ、手数料は300+30になってしまいますので、ご注意ください。', '6', '3', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_PAYPAL_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置
例：0,3000
0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");
      
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_PAYPAL_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '3', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ', now(), ".$this->site_id.")");
      
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_PAYPAL_PREORDER_SHOW', 'True', '予約注文でペイパル決済を表示します', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.")");
  }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    }

    function keys() {
    return array('MODULE_PAYMENT_PAYPAL_STATUS', 'MODULE_PAYMENT_PAYPAL_LIMIT_SHOW', 'MODULE_PAYMENT_PAYPAL_PREORDER_SHOW', 'MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID', 'MODULE_PAYMENT_PAYPAL_SORT_ORDER', 'MODULE_PAYMENT_PAYPAL_CONNECTION_URL','MODULE_PAYMENT_OK_URL', 'MODULE_PAYMENT_NO_URL', 'MODULE_PAYMENT_PAYPAL_COST', 'MODULE_PAYMENT_PAYPAL_MONEY_LIMIT');
    /*  原来 返回值
    return array('MODULE_PAYMENT_PAYPAL_STATUS', 'MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID', 'MODULE_PAYMENT_PAYPAL_SORT_ORDER', 'MODULE_PAYMENT_PAYPAL_CONNECTION_URL', 'MODULE_PAYMENT_PAYPAL_KID', 'MODULE_PAYMENT_OK_URL', 'MODULE_PAYMENT_NO_URL', 'MODULE_PAYMENT_PAYPAL_COST', 'MODULE_PAYMENT_PAYPAL_MONEY_LIMIT');
    */
    }
  
  //エラー
  function get_error() {
      global $_GET;
    
      $error_message = MODULE_PAYMENT_PAYPAL_TEXT_ERROR_MESSAGE; 

      return array('title' => MODULE_PAYMENT_PAYPAL_TEXT_ERROR,
                   'error' => $error_message);
    }




function getexpress($amt,$token){
  $paypalData = array();
  $testcode = 1;
  global $insert_id;
  // Add request-specific fields to the request string.
  $nvpStr = "&TOKEN=$token";
  // Execute the API operation; see the PPHttpPost function above.
  $httpParsedResponseAr = PPHttpPost('GetExpressCheckoutDetails', $nvpStr);

  if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
    foreach($httpParsedResponseAr as $key=>$value){
      $paypalData[$key] = urldecode($value);
    }
    // Extract the response details.
    $payerID = urlencode($httpParsedResponseAr['PAYERID']);
    $paymentType = urlencode("Sale");     // or 'Sale' or 'Order'
    $paymentAmount = urlencode($amt);
    $currencyID = urlencode("JPY");   
    //$token = urlencode($httpParsedResponseAr['TOKEN']);
    $nvpStr = "&TOKEN=$token&PAYERID=$payerID&AMT=$paymentAmount&PAYMENTACTION=$paymentType&CURRENCYCODE=$currencyID";

    // Execute the API operation; see the PPHttpPost function above.
    $httpParsedResponseAr = PPHttpPost('DoExpressCheckoutPayment', $nvpStr);
    /*
      ★PAYMENTTYPE      支払いが即時に行われるか遅れて行われるかを示します。 譏ｾ示及譌ｶ支付霑・･諡冶ｿ沁x付
      ★PAYERSTATUS      支払人のステータス 支付人身莉ｽ
      ★PAYMENTSTATUS      支払いのステータス。 支付状諤閼      Completed: 支払いが完了し、会員残高に正常に入金されました。 支付完豈普C蟶先姐余鬚攝ｳ常霑寢ｼ
      ★COUNTRYCODE      支払人の居住国 支付人居住国家
      ○EMAIL      支払人のメールアドレス。 支付人的驍ｮ箱  found
      ○AMT      最終請求金額。 最后申隸ｷ金鬚魘   found
      ○FIRSTNAME      支払人の名 支付人名字
      ○LASTNAME      支払人の姓。 支付人姓
      ○PHONENUM      支払人の電話番号 支付人逕ｵ隸搓・黴閼   found 
    */
    //var_dump($httpParsedResponseAr['ACK']);
    foreach($httpParsedResponseAr as $key=>$value){
      $paypalData[$key] = urldecode($value);
    }

    if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
      //成功コード発行予定
      //$sql_data_array['money'] =$httpParsedResponseAr["AMT"];
      //$sql_data_array['type']="success";
      //$sql_data_array['rel']="yes";
      //$sql_data_array['date_added']= 'now()';
      //$sql_data_array['last_modified']= 'now()';
      //      tep_db_perform("telecom_unknow", $sql_data_array);
      //エラーコード発行予定
      //                  exit('DoExpressCheckoutPayment failed: ' . urldecode(print_r($httpParsedResponseAr, true)));
      if($paypalData['PAYMENTSTATUS'] == "Completed"){
                  tep_db_perform('telecom_unknow', array(
        'payment_method' => 'paypal',
        '`option`'      => ' ',
        'username'      => $paypalData['FIRSTNAME'] . '' . $paypalData['LASTNAME'],
        'email'         => $paypalData['EMAIL'],
        'telno'         => $paypalData['PHONENUM'],
        'money'         => $paypalData['AMT'],
        'rel'           => 'yes',
        'type'          => 'success',
        'date_added'    => 'now()',
        'last_modified' => 'now()'
      ));
      }else{
      //不明扱い
                  tep_db_perform('telecom_unknow', array(
        'payment_method' => 'paypal',
        '`option`'      => ' ',
        'username'      => $paypalData['FIRSTNAME'] . '' . $paypalData['LASTNAME'],
        'email'         => $paypalData['EMAIL'],
        'telno'         => $paypalData['PHONENUM'],
        'money'         => $paypalData['AMT'],
        'rel'           => 'no',
        'date_added'    => 'now()',
        'last_modified' => 'now()'
      ));
              tep_db_query("delete from ".TABLE_ORDERS." where
            orders_id='".$insert_id."'");
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_UNSUCCESS,
                  'msg=paypal_error'));
            exit;
      }

    }else{
        tep_db_query("delete from ".TABLE_ORDERS." where
            orders_id='".$insert_id."'");
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_UNSUCCESS,
                  'msg=paypal_error'));
            exit;
    }
  }else{
        tep_db_query("delete from ".TABLE_ORDERS." where
            orders_id='".$insert_id."'");
            tep_redirect(tep_href_link(FILENAME_CHECKOUT_UNSUCCESS,
                  'msg=paypal_error'));
            exit;
    // 不正
    //エラーコード発行予定
   // exit('GetExpressCheckoutDetails failed: ' . urldecode(print_r($httpParsedResponseAr, true)));
  }
  tep_db_perform(TABLE_ORDERS, array(
                                     'paypal_paymenttype'   => $paypalData['PAYMENTTYPE'],
                                     'paypal_payerstatus'   => $paypalData['PAYERSTATUS'],
                                     'paypal_paymentstatus' => $paypalData['PAYMENTSTATUS'],
                                     'paypal_countrycode'   => $paypalData['COUNTRYCODE'],
                                     'telecom_email'        => $paypalData['EMAIL'],
                                     'telecom_money'        => $paypalData['AMT'],
                                     'telecom_name'         => $paypalData['FIRSTNAME'] . ''. $paypalData['LASTNAME'],
                                     'telecom_tel'          => $paypalData['PHONENUM'],
                                     'orders_status'        => '30',
                                     'paypal_playerid'      => $payerID,
                                     'paypal_token'         => $token,
                                     ), 'update', "orders_id='".$insert_id."'");
}


















  
  }
?>
