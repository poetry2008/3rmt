<?php
/*
  $Id$
*/
require_once (DIR_WS_CLASSES . 'basePayment.php');
  class paypal extends basePayment  implements paymentInterface  { 
    var $site_id, $code, $title, $description, $enabled, $s_error, $email_footer, $show_payment_info;

// class constructor
/*------------------------------
 功能：加载paypal结算方法设置
 参数：$site_id (string) SITE_ID值
 返回值：无
 -----------------------------*/
    function loadSpecialSettings($site_id=0){
      $this->site_id = $site_id;
      $this->code        = 'paypal';
      $this->form_action_url = MODULE_PAYMENT_PAYPAL_CONNECTION_URL ;
      $this->show_payment_info = 2;
    }
/*----------------------------
 功能：编辑paypal结算
 参数：$theData(boolean) 数据
 参数：$back(boolean) true/false
 返回值：返回paypal结算类型数据(array)
 ---------------------------*/
  function fields($theData=false, $back=false){
    if (!$back) { 
    global $order;
    $total_cost = $order->info['total'];
    $code_fee = $this->calc_fee($total_cost); 
    $added_hidden = tep_draw_hidden_field('code_fee', $code_fee);
    return array(
		 array(
		       "code"=>'',
		       "title"=>'',
		       "field"=>$added_hidden,
		       "rule"=>'',
		       "message"=>"",
		       ));      
    }
  }


// class methods
/*-----------------------------
 功能：检查日志
 参数：无
 返回值：判断是否检查成功(boolean)
 ----------------------------*/
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
/*-----------------------------
 功能：JS验证
 参数：无
 返回值：JS验证是否成功(boolean)
 ----------------------------*/
    function javascript_validation() {
      return false;
    }

/*----------------------------
 功能：选择支付方法
 参数：$theData(string) 数据
 返回值：支付方法数组(array)
 ---------------------------*/
    function selection($theData) {
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
    }
/*--------------------------
 功能：确认检查前台支付方法 
 参数：无
 返回值：是否检查成功(boolean)
 -------------------------*/
    function pre_confirmation_check() {
      return true;
    }
/*----------------------------
 功能：确认支付方法
 参数：无
 返回值：支付方法数据(array)
 ----------------------------*/  
    function confirmation() {

      global $currencies;
      global $_POST;
      global $order;
      
      $s_result = !$_POST['paypal_order_fee_error'];
     
      if (!empty($_POST['paypal_order_fee'])) {
        $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['paypal_order_fee_error'].'</font>'); 
      } else {
        $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['paypal_order_fee_error'].'</font>'); 
      }
    return array(
		 'title' => nl2br(constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_CONFIRMATION")),
		 'fields' => array(
				   array('title' => constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_SHOW"), 'field' => ''),  
				   array('title' => $s_message, 'field' => '')  
				   )           
		 );

    }

 /*----------------------
 功能：判断支付方法返回的信息
 参数：无
 返回值：隐藏INPUT购买订单的信息(string)
 ---------------------*/
  function process_button() { 
      global $order, $currencies, $currency;   
      global $point,$cart,$languages_id;

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
    
    if (isset($_SESSION['campaign_fee'])) {
      $total += $_SESSION['campaign_fee']; 
    }

    if(isset($_SESSION['h_shipping_fee'])){
      $total += $_SESSION['h_shipping_fee']; 
    }
    #mail送信
    $mail_body = '仮クレジットカード注文です。'."\n\n";
    
    # 用户信息----------------------------
    $mail_body .= '━━━━━━━━━━━━━━━━━━━━━'."\n";
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
      $char_id = $products[$i]['id'];
    $mail_body .= '・' . $products[$i]['name'] . '×' . $products[$i]['quantity'] . "\n";
      $attributes_exist = ((isset($products[$i]['op_attributes'])) ? 1 : 0);
       
       if ($attributes_exist == 1) {
          foreach ($products[$i]['op_attributes'] as $op_key => $op_value) {
            $op_key_array = explode('_', $op_key);
            $option_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_key_array[1]."' and id = '".$op_key_array[3]."'"); 
            $option_res = tep_db_fetch_array($option_query);
            if ($option_res) {
              $mail_body .= '└' . $option_res['front_title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $op_value) . "\n";
            }
          }
        }
    
       if (isset($products[$i]['ck_attributes'])) {
         foreach ($products[$i]['ck_attributes'] as $c_op_key => $c_op_value) {
           $c_op_array = explode('_', $c_op_key);
           $c_option_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$c_op_array[0]."' and id = '".$c_op_array[2]."'"); 
           $c_option_res = tep_db_fetch_array($c_option_query);
           if ($c_option_res) {
             $mail_body .= '└' . $c_option_res['front_title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $c_op_value) . "\n";
           }
         }
       }
    }

    
    $mail_body .= "\t" . '------------------------------------------'."\n";
    
    # 交易时间----------------------------
    $mail_body .= '▼お届け日時　　　　：' . $_SESSION["insert_torihiki_date"] . "\n";
    $mail_body .= '　　　　　　　　　：' . $_SESSION["torihikihouhou"] . "\n";
    
    # 用户代理等----------------------------
    $mail_body .= "\n\n";
    $mail_body .= '■IPアドレス　　　　　　：' . $_SERVER["REMOTE_ADDR"] . "\n";
    $mail_body .= '■ホスト名　　　　　　　：' . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
    $mail_body .= '■ユーザーエージェント　：' . $_SERVER["HTTP_USER_AGENT"] . "\n";
    
    tep_mail('管理者', SENTMAIL_ADDRESS, '仮クレカ注文', $mail_body, '', '');
    
    $today = date("YmdHis");


    $process_button_string =

      tep_draw_hidden_field('amount',$total) .
      tep_draw_hidden_field('RETURNURL', trim(tep_href_link(MODULE_PAYMENT_OK_URL, '', 'SSL'))) .//return
      tep_draw_hidden_field('CANCELURL', trim(tep_href_link(MODULE_PAYMENT_NO_URL, '', 'SSL')));//return                 
      $process_button_string .= tep_draw_hidden_field('paypal_order_message', htmlspecialchars($s_message)). tep_draw_hidden_field('paypal_order_fee', $_POST['paypal_order_fee']);
      return $process_button_string;
    }
  
/*-----------------------
 功能：paypal结算前
 参数：无
 返回值：FALESE(boolean)
 ----------------------*/
    function before_process() {
      global $_POST;
      $this->email_footer = str_replace("\r\n", "\n", $_POST['paypal_order_message']);
      
      return false;
    }
/*-----------------------
 功能：paypal结算后
 参数：无
 返回值：false(boolean)
 ----------------------*/
    function after_process() {
      return false;
    }
/*----------------------
 功能：输出错误
 参数：无
 返回值：输出错误(boolean)
 ---------------------*/
    function output_error() {
      return false;
    }
/*---------------------------
 功能：检查SQL
 参数：无
 返回值：SQL(string)
 --------------------------*/  
    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_PAYPAL_STATUS' and site_id = '".$this->site_id."'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }
/*--------------------------------
 功能：添加支付方法的SQL
 参数：无
 返回值：无
 -------------------------------*/
    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added,user_added, site_id) values ('PAYPAL 支払いを有効にする', 'MODULE_PAYMENT_PAYPAL_STATUS', 'True', 'PAYPAL での支払いを受け付けますか?', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(),'".$_SESSION['user_name']."', ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_PAYPAL_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('接続先URL', 'MODULE_PAYMENT_PAYPAL_CONNECTION_URL', '', 'テレコムクレジット申込受付画面URLの設定をします。', '6', '0', now(), ".$this->site_id.")");
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
      
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('ポイント', 'MODULE_PAYMENT_PAYPAL_IS_GET_POINT', '1', 'ポイント', '6', '3', 'tep_cfg_payment_new_checkbox(', now(), ".$this->site_id.")");
      
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('ポイント還元率', 'MODULE_PAYMENT_PAYPAL_POINT_RATE', '0.01', 'ポイント還元率', '6', '0', now(), ".$this->site_id.")");
  }
/*------------------------------
 功能：删除SQL
 参数：无
 返回值：无
 -----------------------------*/
    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    }
/*-----------------------------
 功能：编辑paypal结算方法
 参数：无
 返回值：paypal结算方法数据(array)
 ----------------------------*/
    function keys() {
    return array( 
                 'MODULE_PAYMENT_PAYPAL_STATUS',
                 'MODULE_PAYMENT_PAYPAL_LIMIT_SHOW',
                 'MODULE_PAYMENT_PAYPAL_PREORDER_SHOW',
                 'MODULE_PAYMENT_PAYPAL_IS_GET_POINT',
                 'MODULE_PAYMENT_PAYPAL_POINT_RATE',
                 'MODULE_PAYMENT_PAYPAL_ORDER_STATUS_ID',
                 'MODULE_PAYMENT_PAYPAL_SORT_ORDER',
                 'MODULE_PAYMENT_PAYPAL_CONNECTION_URL',
                 'MODULE_PAYMENT_OK_URL',
                 'MODULE_PAYMENT_NO_URL',
                 'MODULE_PAYMENT_PAYPAL_COST',
                 'MODULE_PAYMENT_PAYPAL_MONEY_LIMIT',
                 'MODULE_PAYMENT_PAYPAL_MAILSTRING',
                 'MODULE_PAYMENT_PAYPAL_PRINT_MAILSTRING_TITLE',
                 'MODULE_PAYMENT_PAYPAL_PRINT_MAILSTRING',
                  );
    }
  
  //错误
/*----------------------
 功能：获取错误
 参数：无
 返回值：错误信息(boolean/array)
 ----------------------*/
  function get_error() {
      global $_GET;
    
      $error_message = MODULE_PAYMENT_PAYPAL_TEXT_ERROR_MESSAGE; 

      return array('title' => MODULE_PAYMENT_PAYPAL_TEXT_ERROR,
                   'error' => $error_message);
    }



/*----------------------
 功能：快速获取到paypal结算
 参数：$order_totals(string) 订单总额
 参数：$num(string)   数量
 返回值：获取成功(boolean)
 ---------------------*/
  function getExpress($order_totals,$num){
  if($order_totals[$num]['code'] =='ot_total' &&  array_key_exists('token', $_REQUEST)){
  $token = urlencode(htmlspecialchars($_REQUEST['token']));
  $amt = $order_totals[$num]['value'];
  $paypalData = array();
  $testcode = 1;
  global $insert_id;
  // Add request-specific fields to the request string.
  $nvpStr = "&TOKEN=$token";
  // Execute the API operation; see the PPHttpPost function above.
  $httpParsedResponseAr = PPHttpPost('GetExpressCheckoutDetails', $nvpStr);

  $receive_tmp_email = @urldecode($httpParsedResponseAr['EMAIL']); 
  if (empty($receive_tmp_email)) {
    tep_db_query("delete from ".TABLE_ORDERS." where orders_id='".$insert_id."'");
    tep_redirect(tep_href_link('checkout_unsuccess.php'));
    exit;
  }
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
    foreach($httpParsedResponseAr as $key=>$value){
      $paypalData[$key] = urldecode($value);
    }

    if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
      
      if($paypalData['PAYMENTSTATUS'] == "Completed"){
                  tep_db_perform('telecom_unknow', array(
        'payment_method' => 'paypal',
        '`option`'      => ' ',
        'username'      => $paypalData['FIRSTNAME'] . '' . $paypalData['LASTNAME'] .(isset($paypalData['BUSINESS']) && $paypalData['BUSINESS'] != '' ? ' / '.$paypalData['BUSINESS'] : ''),
        'email'         => $paypalData['EMAIL'],
        'telno'         => $paypalData['PHONENUM'],
        'money'         => $paypalData['AMT'],
        'rel'           => 'yes',
        'type'          => 'success',
        'date_added'    => 'now()',
        'last_modified' => 'now()'
      ));
      }else{
      //不明处理
                  tep_db_perform('telecom_unknow', array(
        'payment_method' => 'paypal',
        '`option`'      => ' ',
        'username'      => $paypalData['FIRSTNAME'] . '' . $paypalData['LASTNAME'].(isset($paypalData['BUSINESS']) && $paypalData['BUSINESS'] != '' ? ' / '.$paypalData['BUSINESS'] : ''),
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
    
  }
  tep_db_perform(TABLE_ORDERS, array(
                                     'paypal_paymenttype'   => $paypalData['PAYMENTTYPE'],
                                     'paypal_payerstatus'   => $paypalData['PAYERSTATUS'],
                                     'paypal_paymentstatus' => $paypalData['PAYMENTSTATUS'],
                                     'paypal_countrycode'   => $paypalData['COUNTRYCODE'],
				     'paypal_business'      => $paypalData['BUSINESS'],
                                     'telecom_email'        => $paypalData['EMAIL'],
                                     'telecom_money'        => $paypalData['AMT'],
                                     'telecom_name'         => $paypalData['FIRSTNAME'] . ''. $paypalData['LASTNAME'],
                                     'telecom_tel'          => $paypalData['PHONENUM'],
                                     'orders_status'        => '30',
                                     'paypal_playerid'      => $payerID,
                                     'paypal_token'         => $token,
                                     ), 'update', "orders_id='".$insert_id."'");
    return true;
  }else{
    return false;
  }
}
/*-------------------------------
 功能：快速获取前台paypal结算
 参数：$pre_value(string) 支付金额
 参数：$pre_pid(string) 订单ID
 返回值：判断是否获取成功(string) 
 ------------------------------*/
function getpreexpress($pre_value, $pre_pid){
  if(array_key_exists('token', $_REQUEST)){
  $token = urlencode(htmlspecialchars($_REQUEST['token']));
  $amt = $pre_value;
  $paypalData = array();
  $testcode = 1;
  // Add request-specific fields to the request string.
  $nvpStr = "&TOKEN=$token";
  // Execute the API operation; see the PPHttpPost function above.
  $httpParsedResponseAr = PPHttpPost('GetExpressCheckoutDetails', $nvpStr);

  $receive_tmp_email = @urldecode($httpParsedResponseAr['EMAIL']); 
  if (empty($receive_tmp_email)) {
    tep_db_query("delete from ".TABLE_ORDERS." where orders_id='".$pre_pid."'");
    tep_db_query("delete from ".TABLE_ORDERS_TOTAL." where orders_id='".$pre_pid."'");
    tep_redirect(tep_href_link('checkout_unsuccess.php'));
    exit;
  }
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
    foreach($httpParsedResponseAr as $key=>$value){
      $paypalData[$key] = urldecode($value);
    }

    if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
      
      if($paypalData['PAYMENTSTATUS'] == "Completed"){
                  tep_db_perform('telecom_unknow', array(
        'payment_method' => 'paypal',
        '`option`'      => ' ',
        'username'      => $paypalData['FIRSTNAME'] . '' . $paypalData['LASTNAME'].(isset($paypalData['BUSINESS']) && $paypalData['BUSINESS'] != '' ? ' / '.$paypalData['BUSINESS'] : ''),
        'email'         => $paypalData['EMAIL'],
        'telno'         => $paypalData['PHONENUM'],
        'money'         => $paypalData['AMT'],
        'rel'           => 'yes',
        'type'          => 'success',
        'date_added'    => 'now()',
        'last_modified' => 'now()'
      ));
      }else{
      //不明处理
                  tep_db_perform('telecom_unknow', array(
        'payment_method' => 'paypal',
        '`option`'      => ' ',
        'username'      => $paypalData['FIRSTNAME'] . '' . $paypalData['LASTNAME'].(isset($paypalData['BUSINESS']) && $paypalData['BUSINESS'] != '' ? ' / '.$paypalData['BUSINESS'] : ''),
        'email'         => $paypalData['EMAIL'],
        'telno'         => $paypalData['PHONENUM'],
        'money'         => $paypalData['AMT'],
        'rel'           => 'no',
        'date_added'    => 'now()',
        'last_modified' => 'now()'
      ));
              tep_db_query("delete from ".TABLE_ORDERS." where
            orders_id='".$pre_pid."'");
            tep_redirect(tep_href_link('checkout_unsuccess.php'));
            exit;
      }

    }else{
        tep_db_query("delete from ".TABLE_ORDERS." where
            orders_id='".$pre_pid."'");
            tep_redirect(tep_href_link('checkout_unsuccess.php'));
            exit;
    }
  }else{
        tep_db_query("delete from ".TABLE_ORDERS." where
            orders_id='".$pre_pid."'");
            tep_redirect(tep_href_link('checkout_unsuccess.php'));
            exit;

  }
  tep_db_perform(TABLE_ORDERS, array(
                                     'paypal_paymenttype'   => $paypalData['PAYMENTTYPE'],
                                     'paypal_payerstatus'   => $paypalData['PAYERSTATUS'],
                                     'paypal_paymentstatus' => $paypalData['PAYMENTSTATUS'],
                                     'paypal_countrycode'   => $paypalData['COUNTRYCODE'],
				     'paypal_business'      => $paypalData['BUSINESS'],
                                     'telecom_email'        => $paypalData['EMAIL'],
                                     'telecom_money'        => $paypalData['AMT'],
                                     'telecom_name'         => $paypalData['FIRSTNAME'] . ''. $paypalData['LASTNAME'],
                                     'telecom_tel'          => $paypalData['PHONENUM'],
                                     'orders_status'        => '30',
                                     'paypal_playerid'      => $payerID,
                                     'paypal_token'         => $token,
                                     ), 'update', "orders_id='".$pre_pid."'");
    return true;
  }else{
    return false;
  }
}
/*----------------------------------
 功能：paypal结算预约按钮 
 参数：$pid(string) 预约ID
 参数：$preorder_total(string) 预约总额
 返回值：无
 ---------------------------------*/  
  function preorder_process_button($pid, $preorder_total) { 
    global $currencies;   
    global $languages_id;
   
    $preorder_info_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$pid."'");
    $preorder_info = tep_db_fetch_array($preorder_info_raw);
    
    
    $mail_body = '仮クレジットカード注文です。'."\n\n";
    $mail_body .= '━━━━━━━━━━━━━━━━━━━━━'."\n";

    $mail_body .= '▼注文日　　　　　：' . tep_date_long(time())."\n";
    $mail_body .= '▼お名前　　　　　：' . $preorder_info['customers_name']."\n";
    $mail_body .= '▼メールアドレス　：' . $preorder_info['customers_email_address']."\n";
    $mail_body .= '━━━━━━━━━━━━━━━━━━━━━'."\n";
    $mail_body .= '▼お支払金額　　　：' . $currencies->format($preorder_total) . "\n";
    $mail_body .= '▼お支払方法　　　：ペイパル決済'."\n";
    $mail_body .= '▼注文商品'."\n";
    $mail_body .= "\t" . '------------------------------------------'."\n";
   
    $preorder_products_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$pid."'");
    $preorder_products_res = tep_db_fetch_array($preorder_products_raw); 
    if ($preorder_products_res) {
      $mail_body .= '・' . $preorder_products_res['products_name'] . '×' .  $preorder_products_res['products_quantity'] . "\n";
   
      $old_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$pid."'");
      while ($old_attr_res = tep_db_fetch_array($old_attr_raw)) {
        $old_attr_info = @unserialize(stripslashes($old_attr_res['option_info']));
        $mail_body .= '└' . $old_attr_info['title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $old_attr_info['value']) . "\n";
      }
      
      if (isset($_SESSION['preorder_option_info'])) {
        foreach ($_SESSION['preorder_option_info'] as $key => $value) {
          $i_option = explode('_', $key);
          $option_item_raw = tep_db_query("select front_title from ".TABLE_OPTION_ITEM." where name = '".$i_option[1]."' and id = '".$i_option[3]."'");
          $option_item = tep_db_fetch_array($option_item_raw); 
          if ($option_item) {
            $mail_body .= '└' . $option_item['front_title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $value) . "\n";
          }
        }
      }
      
    }

    $mail_body .= "\t" . '------------------------------------------'."\n";
    
    $mail_body .= '▼お届け日時　　　　：' .  $_SESSION["preorder_info_date"].$_SESSION["preorder_info_hour"].':'.$_SESSION["preroder_info_min"] .":00". "\n";
    $mail_body .= '　　　　　　　　　：' . $_SESSION["preorder_info_tori"] . "\n";
    
    $mail_body .= "\n\n";
    $mail_body .= '■IPアドレス　　　　　　：' . $_SERVER["REMOTE_ADDR"] . "\n";
    $mail_body .= '■ホスト名　　　　　　　：' . @gethostbyaddr($_SERVER["REMOTE_ADDR"]) . "\n";
    $mail_body .= '■ユーザーエージェント　：' . $_SERVER["HTTP_USER_AGENT"] . "\n";
    tep_mail('管理者', SENTMAIL_ADDRESS, '仮クレカ注文', $mail_body, '', '');
    
    $hidden_param_str = ''; 
    $hidden_param_str .= tep_draw_hidden_field('cpre_type', '1');
    $hidden_param_str .= tep_draw_hidden_field('amount', $preorder_total);
    $hidden_param_str .= tep_draw_hidden_field('RETURNURL', HTTP_SERVER.'/change_preorder_process.php');
    $hidden_param_str .= tep_draw_hidden_field('CANCELURL', HTTP_SERVER.'/change_preorder.php?pid='.$preorder_info['check_preorder_str']);
    echo $hidden_param_str; 
  }
/*----------------------------
 功能： 获取邮件的字符串
 参数：$option(string) 选项
 返回值：邮件字符串(string) 
 ---------------------------*/
 function getMailString($option='')
  {
    $email_printing_order ='';
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
    $email_printing_order .=
    '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '最終確認　　　　　　：確認者名＿＿＿＿' . "\n";
    $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
  return $email_printing_order;
  }

/*-------------------------------
 功能：获取支付方法标志
 参数：无
 返回值：标志值(string) 
 ------------------------------*/
  function admin_get_payment_symbol(){

    return 2;
  }
/*---------------------
 功能：显示支付方法目录
 参数：$payment(string) 支付方法
 参数：$pay_info_array(string) 支付信息的数组
 返回值：支付方法的目录(string)
 --------------------*/
  function admin_show_payment_list($pay_info_array){

   global $_POST;
   global $_GET;
   $pay_array = explode("\n",trim($pay_info_array[0]));
   $bank_name = explode(':',$pay_array[0]);
   $bank_name[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_name']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_name'] : $bank_name[1];
   $bank_name[1] = isset($_POST['bank_name']) ? $_POST['bank_name'] : $bank_name[1]; 
   echo 'document.getElementsByName("bank_name")[0].value = "'. $bank_name[1] .'";'."\n"; 
   $bank_shiten = explode(':',$pay_array[1]); 
   $bank_shiten[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_shiten']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_shiten'] : $bank_shiten[1];
   $bank_shiten[1] = isset($_POST['bank_shiten']) ? $_POST['bank_shiten'] : $bank_shiten[1];
   echo 'document.getElementsByName("bank_shiten")[0].value = "'. $bank_shiten[1] .'";'."\n"; 
   $bank_kamoku = explode(':',$pay_array[2]);
   $bank_kamoku[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kamoku']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_kamoku'] : $bank_kamoku[1];
   $bank_kamoku[1] = isset($_POST['bank_kamoku']) ? $_POST['bank_kamoku'] : $bank_kamoku[1];
   if($bank_kamoku[1] == TS_MODULE_PAYMENT_PAYPAL_NORMAL || $bank_kamoku[1] == ''){
     echo 'document.getElementsByName("bank_kamoku")[0].checked = true;'."\n"; 
   }else{
     echo 'document.getElementsByName("bank_kamoku")[1].checked = true;'."\n"; 
   }
   $bank_kouza_num = explode(':',$pay_array[3]);
   $bank_kouza_num[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_num']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_num'] : $bank_kouza_num[1];
   $bank_kouza_num[1] = isset($_POST['bank_kouza_num']) ? $_POST['bank_kouza_num'] : $bank_kouza_num[1];
   echo 'document.getElementsByName("bank_kouza_num")[0].value = "'.$bank_kouza_num[1].'";'."\n";
   $bank_kouza_name = explode(':',$pay_array[4]);
   $bank_kouza_name[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_name']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_name'] : $bank_kouza_name[1];
   $bank_kouza_name[1] = isset($_POST['bank_kouza_name']) ? $_POST['bank_kouza_name'] : $bank_kouza_name[1];
   echo 'document.getElementsByName("bank_kouza_name")[0].value = "'.$bank_kouza_name[1].'";'."\n";
   $pay_array = explode("\n",trim($pay_info_array[1]));
   $con_email = explode(":",trim($pay_array[0]));
   $con_email[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['con_email']) ? $_SESSION['orders_update_products'][$_GET['oID']]['con_email'] : $con_email[1];
   $con_email[1] = isset($_POST['con_email']) ? $_POST['con_email'] : $con_email[1];
   echo 'document.getElementsByName("con_email")[0].value = "'.$con_email[1].'";'."\n";
   $pay_array = explode("\n",trim($pay_info_array[2]));
   $rak_tel = explode(":",trim($pay_array[0]));
   $rak_tel[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['rak_tel']) ? $_SESSION['orders_update_products'][$_GET['oID']]['rak_tel'] : $rak_tel[1];
   $rak_tel[1] = isset($_POST['rak_tel']) ? $_POST['rak_tel'] : $rak_tel[1];
   echo 'document.getElementsByName("rak_tel")[0].value = "'.$rak_tel[1].'";'."\n";
   echo <<<EOT
   $("input[name='con_email']").blur(function(){
     var con_email = document.getElementsByName("con_email")[0].value;
     orders_session('con_email',con_email);
   });
   $("input[name='bank_name']").blur(function(){
     var payment_value = document.getElementsByName("bank_name")[0].value;
     orders_session('bank_name',payment_value);
   });
   $("input[name='bank_shiten']").blur(function(){
     var payment_value = document.getElementsByName("bank_shiten")[0].value;
     orders_session('bank_shiten',payment_value);
   });
   $("input[name='bank_kamoku']").click(function(){
     if(document.getElementsByName("bank_kamoku")[0].checked == true){
       var payment_value = document.getElementsByName("bank_kamoku")[0].value;
     }else{
      var payment_value = document.getElementsByName("bank_kamoku")[1].value; 
     }
     orders_session('bank_kamoku',payment_value);
   });
   $("input[name='bank_kouza_num']").blur(function(){
     var payment_value = document.getElementsByName("bank_kouza_num")[0].value;
     orders_session('bank_kouza_num',payment_value);
   });
   $("input[name='bank_kouza_name']").blur(function(){
     var payment_value = document.getElementsByName("bank_kouza_name")[0].value;
     orders_session('bank_kouza_name',payment_value);
   });
   $("input[name='rak_tel']").blur(function(){
     var payment_value = document.getElementsByName("rak_tel")[0].value;
     orders_session('rak_tel',payment_value);
   });
EOT;
   echo "\n";
  }
/*------------------------------
 功能：获取后台顾客点值 
 参数：$point_value(string) 点值
 参数：$customer_id(string) 顾客ID
 返回值：无
 -----------------------------*/
  function admin_get_customer_point($point_value,$customer_id){
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . $point_value .  " where customers_id = '" .$customer_id."' and customers_guest_chk = '0' ");
  } 
  
/*--------------------------
 功能：后台获取点值
 参数：$site_id(string) SITE_ID值
 返回值：点值(string)
 -------------------------*/  
  function admin_is_get_point($site_id)
  {
    return get_configuration_by_site_id_or_default('MODULE_PAYMENT_PAYPAL_IS_GET_POINT', (int)$site_id); 
  }
/*------------------------
 功能：后台获取点率
 参数：$site_id(string) SITE_ID值
 返回值：点率(string) 
 -----------------------*/  
  function admin_get_point_rate($site_id)
  {
    return get_configuration_by_site_id_or_default('MODULE_PAYMENT_PAYPAL_POINT_RATE', (int)$site_id); 
  }
/*----------------------
 功能：后台计算得到的点数
 参数：$orders_id(string) 订单ID
 参数：$point_rate(string) 点率
 参数：$site_id(string) SITE_ID值
 返回值：点数(string) 
 ---------------------*/
  function admin_calc_get_point($orders_id, $point_rate, $site_id)
  {
    $order_point_raw = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_point' and orders_id = '".$orders_id."'"); 
    $order_point = tep_db_fetch_array($order_point_raw); 
    
    $order_subtotal_raw = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_subtotal' and orders_id = '".$orders_id."'"); 
    $order_subtotal = tep_db_fetch_array($order_subtotal_raw); 
  
    $order_campaign_raw = tep_db_query("select campaign_fee from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$orders_id."' and site_id = '".$site_id."'");
    $order_campaign = tep_db_fetch_array($order_campaign_raw);
    
    if ($order_subtotal['value'] > 0) {
      if ($order_campaign) {
        return ($order_subtotal['value'] + $order_campaign['campaign_fee']) * $point_rate; 
      } else {
        return ($order_subtotal['value'] - (int)$order_point['value']) * $point_rate; 
      }
    } else {
      if ($order_campaign) {
        return (abs($order_subtotal['value']) + abs($order_campaign['campaign_fee'])) * $point_rate; 
      } else {
        return abs($order_subtotal['value']) * $point_rate; 
      }
    }
  }
/*--------------------------
 功能：获取点率
 参数：无
 返回值：点率(string)
 -------------------------*/  
  function get_point_rate()
  {
    return MODULE_PAYMENT_PAYPAL_POINT_RATE; 
  }
/*--------------------------
 功能：后台获得评论
 参数：$comment(string) 评论
 返回值：评论信息(string)
 -------------------------*/
  function admin_get_comment($comment){

    $payment_comment_array = array(TS_TEXT_BANK_NAME,
                                   TS_TEXT_BANK_SHITEN,
                                   TS_TEXT_BANK_KAMOKU,
                                   TS_TEXT_BANK_KOUZA_NUM,
                                   TS_TEXT_BANK_KOUZA_NAME,
                                   TS_MODULE_PAYMENT_CONVENIENCE_EMAIL_TEXT,
                                   TS_MODULE_PAYMENT_CONVENIENCE_STORE_TEXT_TEL
                                 );
    $comment_array = explode("\n",$comment);
    $comment_str_array = array();
    $comment_str = '';
    foreach($comment_array as $value){

      $value_array = explode(':',$value); 
      if(!(in_array($value_array[0].':',$payment_comment_array) && trim($value_array[1]) == '')){

        $comment_str_array[] = $value;
      }
    }
    $comment_str = implode("\n",$comment_str_array);
    return $comment_str;
  }
}
/*---------------------------
 功能：通过关键字分割字符串
 参数：$str(string)  字符串
 参数：$keywords(string) 关键字
 返回值：分割之后的字符串(string) 
 --------------------------*/
function tep_high_light_by_keywords_flag($str, $keywords){ 
      $k = $rk= explode('|',$keywords);
      foreach($k as $key => $value){
           $rk[$key] = '<font style="background:red;">'.$value.'</font>';
      }
      return str_replace($k, $rk, $str);
  }
/*--------------------------
 功能：从payapl获取相应信息
 参数：$methodName_(string) 方法名称
 参数：$nvpStr(string) 字符串
 返回值：获取相应信息(string) 
 -------------------------*/
function PPHttpPost($methodName_, $nvpStr_) {
  //  global $environment;

  // Set up your API credentials, PayPal end point, and API version.
  $API_UserName = urlencode(my_api_username);
  $API_Password = urlencode(my_api_password);
  $API_Signature = urlencode(my_api_signature);
  $API_Endpoint = "https://api-3t.paypal.com/nvp";


  $version = urlencode('51.0');
  // Set the curl parameters.
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
  curl_setopt($ch, CURLOPT_VERBOSE, 1);
  // Turn off the server and peer verification (TrustManager Concept).
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);

  // Set the API operation, version, and API signature in the request.
  $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature".$nvpStr_;

  // Set the request as a POST FIELD for curl.
  curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

  // Get response from the server.
  $httpResponse = curl_exec($ch);

  if(!$httpResponse) {
    exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
  }

  // Extract the response details.
  $httpResponseAr = explode("&", $httpResponse);

  $httpParsedResponseAr = array();
  foreach ($httpResponseAr as $i => $value) {
    $tmpAr = explode("=", $value);
    if(sizeof($tmpAr) > 1) {
      $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
    }
  }

  if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
    exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
  }

  require_once(DIR_WS_FUNCTIONS . 'visites.php'); 
  $orders_mail_title = ORDERS_EMPTY_EMAIL_TITLE.'　'.date('Y-m-d H:i:s');
  $orders_mail_text = ORDERS_EMPTY_EMAIL_TEXT;
  $orders_mail_text = str_replace('${ERROR_NUMBER}','002',$orders_mail_text);
  $orders_mail_text = str_replace('${ERROR_TIME}',date('Y-m-d H:i:s'),$orders_mail_text); 

  $orders_error_contents = "\n\n";
  $orders_error_contents .= ORDERS_SITE." ".STORE_NAME."\n";
  $orders_shipping_time = isset($_SESSION['insert_torihiki_date']) ? $_SESSION['insert_torihiki_date'] : $_SESSION['preorder_info_date'].' '.$_SESSION['preorder_info_hour'].':'.$_SESSION['preorder_info_min'];
  $orders_error_contents .= ORDERS_TIME." ".$orders_shipping_time."\n";
  $orders_torihikihouhou = isset($_SESSION['torihikihouhou']) ? $_SESSION['torihikihouhou'] : $_SESSION['preorder_info_tori'];
  $orders_error_contents .= ORDERS_OPTION." ".$orders_torihikihouhou."\n";
  $orders_error_contents .= CREATE_ORDERS_DATE." ".date('Y-m-d H:i:s')."\n";
  $customer_query = tep_db_query("select customers_guest_chk from " . TABLE_CUSTOMERS . " where customers_id = '" . $_SESSION['customer_id'] . "'");
  $customer_array = tep_db_fetch_array($customer_query);
  tep_db_free_result($customer_query);
  $customer_type = $customer_array['customers_guest_chk'] == 1 ? TABLE_HEADING_MEMBER_TYPE_GUEST : TEXT_MEMBER;
  $orders_error_contents .= CUSTOMER_TYPE." ".$customer_type."\n";
  $customer_name = tep_get_fullname($_SESSION['customer_first_name'],$_SESSION['customer_last_name']);
  $orders_error_contents .= CUSTOMER_NAME." ".$customer_name."\n";
  $orders_error_contents .= ORDERS_EMAIL." ".$_SESSION['customer_emailaddress']."\n";
  $orders_payment = isset($_SESSION['payment']) ? $_SESSION['payment'] : $_SESSION['payment_value']['payment'];
  $orders_error_contents .= ORDERS_PAYMENT." ".$orders_payment."\n";

  foreach($httpParsedResponseAr as $p_key=>$p_value){
    $orders_error_contents .= $p_key .':'.urldecode($p_value)."\n";
  }

  $orders_error_contents .= CUSTOMER_IP." ".$_SERVER['REMOTE_ADDR']."\n";
  $orders_error_contents .= HOST_NAME." ".trim(strtolower(@gethostbyaddr($_SERVER['REMOTE_ADDR'])))."\n";
  $orders_error_contents .= USER_AGENT." ".$_SERVER["HTTP_USER_AGENT"]."\n";
  $orders_error_contents .= CUSTOMER_OS." ".tep_high_light_by_keywords_flag(getOS($_SERVER["HTTP_USER_AGENT"]),OS_LIGHT_KEYWORDS)."\n";
  $browser_info = getBrowserInfo($_SERVER["HTTP_USER_AGENT"]);
  $browser_type = tep_high_light_by_keywords_flag($browser_info['longName'] . ' ' . $browser_info['version'],BROWSER_LIGHT_KEYWORDS);
  $orders_error_contents .= BROWSER_TYPE." ".$browser_type."\n";
  $browser_language = tep_high_light_by_keywords_flag($_SERVER['HTTP_ACCEPT_LANGUAGE'] ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'UNKNOW',HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS);
  $orders_error_contents .= BROWSER_LANGUAGE." ".$browser_language."\n";
  $browser_pc = tep_high_light_by_keywords_flag($_SESSION['systemLanguage'] ? $_SESSION['systemLanguage'] : 'UNKNOW',SYSTEM_LANGUAGE_LIGHT_KEYWORDS);
  $orders_error_contents .= BROWSER_PC_LANGUAGE." ".$browser_pc."\n";
  $browser_user = tep_high_light_by_keywords_flag($_SESSION['userLanguage'] ? $_SESSION['userLanguage'] : 'UNKNOW',USER_LANGUAGE_LIGHT_KEYWORDS);
  $orders_error_contents .= BROWSER_USER_LANGUAGE." ".$browser_user."\n";

  $orders_mail_text = str_replace('${ERROR_CONTENTS}',$orders_error_contents,$orders_mail_text);
 
  $message = new email(array('X-Mailer: iimy Mailer'));
  $text = $orders_mail_text;
  $message->add_html(nl2br($orders_mail_text), $text);
  $message->build_message();
  $message->send(STORE_OWNER,IP_SEAL_EMAIL_ADDRESS,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS,$orders_mail_title);
  return $httpParsedResponseAr;
}
?>
