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

      $products = $cart->get_products();
    
    $order_product_list = '';    
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      $char_id = $products[$i]['id'];
      $order_product_list .= '・' . $products[$i]['name'] . '×' . $products[$i]['quantity'] . "\n";
      $attributes_exist = ((isset($products[$i]['op_attributes'])) ? 1 : 0);
       
       if ($attributes_exist == 1) {
          foreach ($products[$i]['op_attributes'] as $op_key => $op_value) {
            $op_key_array = explode('_', $op_key);
            $option_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_key_array[1]."' and id = '".$op_key_array[3]."'"); 
            $option_res = tep_db_fetch_array($option_query);
            if ($option_res) {
            $order_product_list .= '└' . $option_res['front_title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $op_value) . "\n";
            }
          }
        }
    
       if (isset($products[$i]['ck_attributes'])) {
         foreach ($products[$i]['ck_attributes'] as $c_op_key => $c_op_value) {
           $c_op_array = explode('_', $c_op_key);
           $c_option_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$c_op_array[0]."' and id = '".$c_op_array[2]."'"); 
           $c_option_res = tep_db_fetch_array($c_option_query);
           if ($c_option_res) {
             $order_product_list .= '└' . $c_option_res['front_title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $c_op_value) . "\n";
           }
         }
       }
    }

    $mail_mode = array(
        '${USER_NAME}',
        '${USER_MAIL}',
        '${ORDER_DATE}',
        '${ORDER_TOTAL}',
        '${ORDER_PRODUCTS}',
        '${SHIPPING_TIME}',
        '${IP}',
        '${USER_AGENT}',
        '${HOST_NAME}',
        '${PAYMENT}'
        );
    $mail_value = array(
        $order->customer["lastname"] . ' '. $order->customer["firstname"],
        $order->customer["email_address"],
        tep_date_long(time()),
        $currencies->format($total),
        $order_product_list,
        str_string(date('Y-m-d',strtotime($_SESSION["insert_torihiki_date"]))) . date('H',strtotime($_SESSION["insert_torihiki_date"])) . TIME_HOUR_TEXT . date('i',strtotime($_SESSION["insert_torihiki_date"])) . TEXT_ORDERS_PRODUCTS_LINK. date('H',strtotime($_SESSION["insert_torihiki_date_end"])) .TIME_HOUR_TEXT. date('i',strtotime($_SESSION["insert_torihiki_date_end"])) .TEXT_ORDERS_PRODUCTS_TWENTY_HOUR,
        $_SERVER["REMOTE_ADDR"],
        @gethostbyaddr($_SERVER["REMOTE_ADDR"]),
        $_SERVER["HTTP_USER_AGENT"],
        payment::changeRomaji($this->code,PAYMENT_RETURN_TYPE_TITLE)
        );
    
    $process_button_template = tep_get_mail_templates('MODULE_PAYMENT_CARD_CONFRIMTION_EMAIL_CONTENT',SITE_ID);
    $mail_body = str_replace($mail_mode,$mail_value,$process_button_template['contents']);
    $mail_body = tep_replace_mail_templates($mail_body,$order->customer["email_address"],$order->customer["lastname"] . ' '. $order->customer["firstname"]);
    $subject = $process_button_template['title'];
    $title_mode_array = array(
                             '${SITE_NAME}' 
                           );
    $title_replace_array = array(
                             STORE_NAME 
                           );
    $subject = str_replace($title_mode_array,$title_replace_array,$subject); 
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
      tep_mail('', SENTMAIL_ADDRESS, $subject, $mail_body, '', '');
    }
    
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
      
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_PAYPAL_PREORDER_SHOW', 'True', '予約注文でクレジットカード決済（PayPal）を表示します', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.")");
      
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('ポイント', 'MODULE_PAYMENT_PAYPAL_IS_GET_POINT', 'True', 'ポイント', '6', '3', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.")");
      
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
                 'MODULE_PAYMENT_PAYPAL_PREORDER_STATUS_ID',
                 'MODULE_PAYMENT_PAYPAL_SORT_ORDER',
                 'MODULE_PAYMENT_PAYPAL_CONNECTION_URL',
                 'MODULE_PAYMENT_OK_URL',
                 'MODULE_PAYMENT_NO_URL',
                 'MODULE_PAYMENT_PAYPAL_COST',
                 'MODULE_PAYMENT_PAYPAL_MONEY_LIMIT',
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
 功能：是否插入历史信息
 参数：$option_info(string) 信息
 参数：$orders_id(string) 订单id
 返回值：信息(array)
 ----------------------*/
  function check_insert_status_history($option_info, $orders_id) {
    if ($orders_id) {
      $telecom_unknow = tep_db_fetch_array(tep_db_query("select * from `telecom_unknow` where `option` = '".$orders_id."' and `rel` = 'yes' and `payment_method` = '".$this->code."' limit 1")); 
      if ($telecom_unknow) {
        $order_info_raw = tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$orders_id."'"); 
        $order_info_res = tep_db_fetch_array($order_info_raw); 
        return array($telecom_unknow['date_added'], constant('TS_MODULE_PAYMENT_'.strtoupper($this->code).'_TEXT_CAL'), $order_info_res['telecom_name']); 
      }
    }
    return array(); 
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

      //检测订单ID是否重复 
      $success_flag = true;
      $orders_id_array = array();
      $telecom_unknow_query = tep_db_query("select id from telecom_unknow where `option`='".$insert_id."'");
      if(tep_db_num_rows($telecom_unknow_query) > 0){

        $orders_id_array[] = $insert_id; 
        $success_flag = false;
        //如果订单ID存在的话，最多循环10次生成新订单ID，最后如果还存在的话，跳转到注文失败页面，并发电子邮件
        for($orders_num = 0;$orders_num < 10;$orders_num++){
          $nid = date('Ymd-His').ds_makeRandStr(2);
          $telecom_unknow_query = tep_db_query("select id from telecom_unknow where `option`='".$nid."'");
          if(tep_db_num_rows($telecom_unknow_query) == 0){
            $insert_id = $nid;
            $_SESSION['insert_id'] = $insert_id;
            $success_flag = true;
            break;
          }else{
            $orders_id_array[] = $nid; 
          }
        }
      }
      $orders_query = tep_db_query("select orders_id from ".TABLE_ORDERS." where orders_id='".$insert_id."'");  
      if(tep_db_num_rows($orders_query) > 0){

        $orders_id_array[] = $insert_id;
        $success_flag = false;
        //如果订单ID存在的话，最多循环10次生成新订单ID，最后如果还存在的话，跳转到注文失败页面，并发电子邮件
        for($orders_num = 0;$orders_num < 10;$orders_num++){
          $nid = date('Ymd-His').ds_makeRandStr(2);
          $orders_query = tep_db_query("select orders_id from ".TABLE_ORDERS." where orders_id='".$nid."'");  
          if(tep_db_num_rows($orders_query) == 0){
            $insert_id = $nid;
            $_SESSION['insert_id'] = $insert_id;
            $success_flag = true;
            break;
          }else{
            $orders_id_array[] = $nid; 
          }
        }
      }
      $orders_id_array = array_unique($orders_id_array);
      if($success_flag == false){

        //发送电子邮件
        $message = new email(array('X-Mailer: iimy Mailer'));
        $orders_mail_title = 'paypal error';
        $orders_mail_text = 'ID: '.implode(';',$orders_id_array)."\n";
        $orders_mail_text .= 'TIME: '.date('Y-m-d H:i:s')."\n";
        $text = $orders_mail_text;  
        $message->add_html(nl2br($orders_mail_text), $text);
        $message->build_message();
        //Administrator
        $message->send(STORE_OWNER,IP_SEAL_EMAIL_ADDRESS,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS,$orders_mail_title);
      }
      if($paypalData['PAYMENTSTATUS'] == "Completed" && $success_flag == true){
                  tep_db_perform('telecom_unknow', array(
        'payment_method' => 'paypal',
        '`option`'      => $insert_id,
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
  $orders_status_id = get_configuration_by_site_id('MODULE_PAYMENT_'.strtoupper($this->code).'_ORDER_STATUS_ID', SITE_ID); 
  $orders_status_id = $orders_status_id != 0 ? $orders_status_id : DEFAULT_ORDERS_STATUS_ID;
  $_SESSION['paypal_order_info'] = array(
                                     'paypal_paymenttype'   => $paypalData['PAYMENTTYPE'],
                                     'paypal_payerstatus'   => $paypalData['PAYERSTATUS'],
                                     'paypal_paymentstatus' => $paypalData['PAYMENTSTATUS'],
                                     'paypal_countrycode'   => $paypalData['COUNTRYCODE'],
				     'paypal_business'      => $paypalData['BUSINESS'],
                                     'telecom_email'        => $paypalData['EMAIL'],
                                     'telecom_money'        => $paypalData['AMT'],
                                     'telecom_name'         => $paypalData['FIRSTNAME'] . ''. $paypalData['LASTNAME'],
                                     'telecom_tel'          => $paypalData['PHONENUM'],
                                     'orders_status'        => $orders_status_id,
                                     'paypal_playerid'      => $payerID,
                                     'paypal_token'         => $token,);
  /*
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
                                     'orders_status'        => $orders_status_id,
                                     'paypal_playerid'      => $payerID,
                                     'paypal_token'         => $token,
                                     ), 'update', "orders_id='".$insert_id."'");
    */
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

      //检测预约订单ID是否重复 
      $success_flag = true;
      $orders_id_array = array();
      $telecom_unknow_query = tep_db_query("select id from telecom_unknow where `option`='".$pre_pid."'");
      if(tep_db_num_rows($telecom_unknow_query) > 0){

        $orders_id_array[] = $pre_pid;  
        $success_flag = false;
        //如果订单ID存在的话，最多循环10次生成新订单ID，最后如果还存在的话，跳转到注文失败页面，并发电子邮件
        for($orders_num = 0;$orders_num < 10;$orders_num++){
          $nid = date('Ymd-His').ds_makeRandStr(2);
          $telecom_unknow_query = tep_db_query("select id from telecom_unknow where `option`='".$nid."'");
          if(tep_db_num_rows($telecom_unknow_query) == 0){
            $pre_pid = $nid;
            $success_flag = true;
            break;
          }else{
            $orders_id_array[] = $nid; 
          }
        }
      }
      $orders_query = tep_db_query("select orders_id from ".TABLE_ORDERS." where orders_id='".$pre_pid."'");  
      if(tep_db_num_rows($orders_query) > 0){

        $orders_id_array[] = $pre_pid;
        $success_flag = false;
        //如果订单ID存在的话，最多循环10次生成新订单ID，最后如果还存在的话，跳转到注文失败页面，并发电子邮件
        for($orders_num = 0;$orders_num < 10;$orders_num++){
          $nid = date('Ymd-His').ds_makeRandStr(2);
          $orders_query = tep_db_query("select orders_id from ".TABLE_ORDERS." where orders_id='".$nid."'");  
          if(tep_db_num_rows($orders_query) == 0){
            $pre_pid = $nid;
            $success_flag = true;
            break;
          }else{
            $orders_id_array[] = $nid; 
          }
        }
      }
      $orders_id_array = array_unique($orders_id_array);
      if($success_flag == false){

        //发送电子邮件
        $message = new email(array('X-Mailer: iimy Mailer'));
        $orders_mail_title = 'paypal error';
        $orders_mail_text = 'ID: '.implode(';',$orders_id_array)."\n";
        $orders_mail_text .= 'TIME: '.date('Y-m-d H:i:s')."\n";
        $text = $orders_mail_text;  
        $message->add_html(nl2br($orders_mail_text), $text);
        $message->build_message();
        //Administrator
        $message->send(STORE_OWNER,IP_SEAL_EMAIL_ADDRESS,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS,$orders_mail_title);
      } 
        
      if($paypalData['PAYMENTSTATUS'] == "Completed" && $success_flag == true){
                  tep_db_perform('telecom_unknow', array(
        'payment_method' => 'paypal',
        '`option`'      => $pre_pid,
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
  $orders_status_id = get_configuration_by_site_id('MODULE_PAYMENT_'.strtoupper($this->code).'_ORDER_STATUS_ID', SITE_ID); 
  $orders_status_id = $orders_status_id != 0 ? $orders_status_id : DEFAULT_ORDERS_STATUS_ID;
  $_SESSION['paypal_order_info'] = array(
                                     'paypal_paymenttype'   => $paypalData['PAYMENTTYPE'],
                                     'paypal_payerstatus'   => $paypalData['PAYERSTATUS'],
                                     'paypal_paymentstatus' => $paypalData['PAYMENTSTATUS'],
                                     'paypal_countrycode'   => $paypalData['COUNTRYCODE'],
				     'paypal_business'      => $paypalData['BUSINESS'],
                                     'telecom_email'        => $paypalData['EMAIL'],
                                     'telecom_money'        => $paypalData['AMT'],
                                     'telecom_name'         => $paypalData['FIRSTNAME'] . ''. $paypalData['LASTNAME'],
                                     'telecom_tel'          => $paypalData['PHONENUM'],
                                     'orders_status'        => $orders_status_id,
                                     'paypal_playerid'      => $payerID,
                                     'paypal_token'         => $token,);
  /*
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
                                     'orders_status'        => $orders_status_id,
                                     'paypal_playerid'      => $payerID,
                                     'paypal_token'         => $token,
                                     ), 'update', "orders_id='".$pre_pid."'");
    */
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
    
    $preorder_product_list = '';    
    $preorder_products_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$pid."'");
    $preorder_products_res = tep_db_fetch_array($preorder_products_raw); 
    if ($preorder_products_res) {
      $preorder_product_list .= '・' . $preorder_products_res['products_name'] . '×' .  $preorder_products_res['products_quantity'] . "\n";

      $old_attr_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_id = '".$pid."'");
      while ($old_attr_res = tep_db_fetch_array($old_attr_raw)) {
        $old_attr_info = @unserialize(stripslashes($old_attr_res['option_info']));
        $preorder_product_list .= '└' . $old_attr_info['title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $old_attr_info['value']) . "\n";
      }
      
      if (isset($_SESSION['preorder_option_info'])) {
        foreach ($_SESSION['preorder_option_info'] as $key => $value) {
          $i_option = explode('_', $key);
          $option_item_raw = tep_db_query("select front_title from ".TABLE_OPTION_ITEM." where name = '".$i_option[1]."' and id = '".$i_option[3]."'");
          $option_item = tep_db_fetch_array($option_item_raw); 
          if ($option_item) {
            $preorder_product_list .= '└' . $option_item['front_title'] . ' ' .  str_replace(array("<BR>", "<br>"), "\n", $value) . "\n";
          }
        }
      }
    }
    
    $mail_mode = array(
        '${USER_NAME}',
        '${USER_MAIL}',
        '${ORDER_DATE}',
        '${ORDER_TOTAL}',
        '${ORDER_PRODUCTS}',
        '${SHIPPING_TIME}',
        '${IP}',
        '${USER_AGENT}',
        '${HOST_NAME}',
        '${PAYMENT}'
        );
    $mail_value = array(
        $preorder_info['customers_name'],
        $preorder_info['customers_email_address'],
        tep_date_long(time()),
        $currencies->format($preorder_total),
        $preorder_product_list,
        str_string($_SESSION['preorder_info_date']) .  $_SESSION['preorder_info_start_hour'] . TIME_HOUR_TEXT . $_SESSION['preorder_info_start_min'] .  TEXT_ORDERS_PRODUCTS_LINK. $_SESSION['preorder_info_end_hour'].TIME_HOUR_TEXT. $_SESSION['preorder_info_end_min'].TEXT_ORDERS_PRODUCTS_TWENTY_HOUR,
        $_SERVER["REMOTE_ADDR"],
        @gethostbyaddr($_SERVER["REMOTE_ADDR"]),
        $_SERVER["HTTP_USER_AGENT"],
        payment::changeRomaji($this->code,PAYMENT_RETURN_TYPE_TITLE)
        );
   

    $process_button_template = tep_get_mail_templates('MODULE_PAYMENT_CARD_CONFRIMTION_EMAIL_CONTENT',SITE_ID);
    $mail_body = str_replace($mail_mode,$mail_value,$process_button_template['contents']);
    $mail_body = tep_replace_mail_templates($mail_body,$preorder_info['customers_email_address'],$preorder_info['customers_name']);
    $subject = $process_button_template['title'];
    $title_mode_array = array(
                             '${SITE_NAME}' 
                           );
    $title_replace_array = array(
                             STORE_NAME 
                           );
    $subject = str_replace($title_mode_array,$title_replace_array,$subject);
    if(isset($_SESSION['preorders_send_mail_flag']) && $_SESSION['preorders_send_mail_flag'] == 1){
      tep_mail('', SENTMAIL_ADDRESS,$subject, $mail_body, '', '');
      unset($_SESSION['preorders_send_mail_flag']);
    }
    
    $hidden_param_str = ''; 
    $hidden_param_str .= tep_draw_hidden_field('cpre_type', '1');
    $hidden_param_str .= tep_draw_hidden_field('amount', $preorder_total);
    $hidden_param_str .= tep_draw_hidden_field('RETURNURL', HTTP_SERVER.'/change_preorder_process.php');
    $hidden_param_str .= tep_draw_hidden_field('CANCELURL', HTTP_SERVER.'/change_preorder.php?pid='.$preorder_info['check_preorder_str']);
    echo $hidden_param_str; 
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
 参数：$pay_info_array(array) 支付信息的数组
 参数：$default_email_info(string) 默认邮件地址
 参数：$is_show(boolean) 是否显示默认值 
 返回值：无
 --------------------*/
  function admin_show_payment_list($pay_info_array, $default_email_info, $is_show){

   global $_POST;
   global $_GET;
   $pay_array = explode("\n",trim($pay_info_array[0]));
   $bank_name = explode(':',$pay_array[0]);
   if (!$is_show) {
     $bank_name[1] = ''; 
   }
   $bank_name[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_name']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_name'] : $bank_name[1];
   $bank_name[1] = isset($_POST['bank_name']) ? $_POST['bank_name'] : $bank_name[1]; 
   echo <<<EOT
   if (document.getElementsByName("bank_name")[0]) {
     document.getElementsByName("bank_name")[0].value = "$bank_name[1]"; 
   }
EOT;
   echo "\n";
   $bank_shiten = explode(':',$pay_array[1]); 
   if (!$is_show) {
     $bank_shiten[1] = ''; 
   }
   $bank_shiten[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_shiten']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_shiten'] : $bank_shiten[1];
   $bank_shiten[1] = isset($_POST['bank_shiten']) ? $_POST['bank_shiten'] : $bank_shiten[1];
   echo <<<EOT
   if (document.getElementsByName("bank_shiten")[0]) {
     document.getElementsByName("bank_shiten")[0].value = "$bank_shiten[1]"; 
   }
EOT;
   echo "\n";
   $bank_kamoku = explode(':',$pay_array[2]);
   if (!$is_show) {
     $bank_kamoku[1] = ''; 
   }
   $bank_kamoku[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kamoku']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_kamoku'] : $bank_kamoku[1];
   $bank_kamoku[1] = isset($_POST['bank_kamoku']) ? $_POST['bank_kamoku'] : $bank_kamoku[1];
   if($bank_kamoku[1] == TS_MODULE_PAYMENT_PAYPAL_NORMAL || $bank_kamoku[1] == ''){
   echo <<<EOT
   if (document.getElementsByName("bank_kamoku")[0]) {
     document.getElementsByName("bank_kamoku")[0].checked = true; 
   }
EOT;
   echo "\n";
   }else{
   echo <<<EOT
   if (document.getElementsByName("bank_kamoku")[1]) {
     document.getElementsByName("bank_kamoku")[1].checked = true; 
   }
EOT;
   echo "\n";
   }
   $bank_kouza_num = explode(':',$pay_array[3]);
   if (!$is_show) {
     $bank_kouza_num[1] = ''; 
   }
   $bank_kouza_num[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_num']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_num'] : $bank_kouza_num[1];
   $bank_kouza_num[1] = isset($_POST['bank_kouza_num']) ? $_POST['bank_kouza_num'] : $bank_kouza_num[1];
   echo <<<EOT
   if (document.getElementsByName("bank_kouza_num")[0]) {
     document.getElementsByName("bank_kouza_num")[0].value = "$bank_kouza_num[1]";
   }
EOT;
   echo "\n";
   $bank_kouza_name = explode(':',$pay_array[4]);
   if (!$is_show) {
     $bank_kouza_name[1] = ''; 
   }
   $bank_kouza_name[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_name']) ? $_SESSION['orders_update_products'][$_GET['oID']]['bank_kouza_name'] : $bank_kouza_name[1];
   $bank_kouza_name[1] = isset($_POST['bank_kouza_name']) ? $_POST['bank_kouza_name'] : $bank_kouza_name[1];
   echo <<<EOT
   if (document.getElementsByName("bank_kouza_name")[0]) {
     document.getElementsByName("bank_kouza_name")[0].value = "$bank_kouza_name[1]";
   }
EOT;
   echo "\n";
   $pay_array = explode("\n",trim($pay_info_array[1]));
   $con_email = explode(":",trim($pay_array[0]));
   $con_email[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['con_email']) ? $_SESSION['orders_update_products'][$_GET['oID']]['con_email'] : $con_email[1];
   $con_email[1] = isset($_POST['con_email']) ? $_POST['con_email'] : $con_email[1];
   $tmp_email = (!empty($con_email[1])?$con_email[1]:$default_email_info); 
   echo <<<EOT
   if (document.getElementsByName("con_email")[0]) {
     document.getElementsByName("con_email")[0].value = "$tmp_email";
   }
EOT;
   echo "\n";
   $pay_array = explode("\n",trim($pay_info_array[2]));
   $rak_tel = explode(":",trim($pay_array[0]));
   $rak_tel[1] = isset($_SESSION['orders_update_products'][$_GET['oID']]['rak_tel']) ? $_SESSION['orders_update_products'][$_GET['oID']]['rak_tel'] : $rak_tel[1];
   $rak_tel[1] = isset($_POST['rak_tel']) ? $_POST['rak_tel'] : $rak_tel[1];
   echo <<<EOT
   if (document.getElementsByName("rak_tel")[0]) {
     document.getElementsByName("rak_tel")[0].value = "$rak_tel[1]";
   }
EOT;
   echo "\n";
   
   echo <<<EOT
   if (document.getElementsByName("con_email")[0]) {
     $("input[name='con_email']").blur(function(){
       var con_email = document.getElementsByName("con_email")[0].value;
       orders_session('con_email',con_email);
     });
   }
   if (document.getElementsByName("bank_name")[0]) {
     $("input[name='bank_name']").blur(function(){
       var payment_value = document.getElementsByName("bank_name")[0].value;
       orders_session('bank_name',payment_value);
     });
   }
   if (document.getElementsByName("bank_shiten")[0]) {
     $("input[name='bank_shiten']").blur(function(){
       var payment_value = document.getElementsByName("bank_shiten")[0].value;
       orders_session('bank_shiten',payment_value);
     });
   }
   if (document.getElementsByName("bank_kamoku")[0]) {
     $("input[name='bank_kamoku']").click(function(){
       if(document.getElementsByName("bank_kamoku")[0].checked == true){
         var payment_value = document.getElementsByName("bank_kamoku")[0].value;
       }else{
        var payment_value = document.getElementsByName("bank_kamoku")[1].value; 
       }
       orders_session('bank_kamoku',payment_value);
     });
   }
   if (document.getElementsByName("bank_kouza_num")[0]) {
     $("input[name='bank_kouza_num']").blur(function(){
       var payment_value = document.getElementsByName("bank_kouza_num")[0].value;
       orders_session('bank_kouza_num',payment_value);
     });
   }
   if (document.getElementsByName("bank_kouza_name")[0]) {
     $("input[name='bank_kouza_name']").blur(function(){
       var payment_value = document.getElementsByName("bank_kouza_name")[0].value;
       orders_session('bank_kouza_name',payment_value);
     });
   }
   if (document.getElementsByName("rak_tel")[0]) {
     $("input[name='rak_tel']").blur(function(){
       var payment_value = document.getElementsByName("rak_tel")[0].value;
       orders_session('rak_tel',payment_value);
     });
   }
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
                                   TS_TEXT_BANK_BRANCH,
                                   TS_TEXT_BANK_TYPE,
                                   TS_TEXT_BANK_ACCOUNT_NUM,
                                   TS_TEXT_BANK_ACCOUNT_NAME,
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
//  $API_Endpoint = "https://api-3t.paypal.com/nvp";
  $API_Endpoint = "https://api-3t.sandbox.paypal.com/nvp";
    


  $version = urlencode('51.0');
  // Set the curl parameters.
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
  curl_setopt($ch, CURLOPT_VERBOSE, 0);
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
  $error = curl_error($ch);
  if (curl_errno($ch) != 0) {
    $error_mail_array = tep_get_mail_templates('ORDERS_EMPTY_EMAIL_TEXT',SITE_ID);
    $orders_mail_title = $error_mail_array['title'].'　'.date('Y-m-d H:i:s');
    $message = new email(array('X-Mailer: iimy Mailer'));
    $orders_mail_text = "$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')';
    $text = $orders_mail_text;
    $message->add_html(nl2br($orders_mail_text), $text);
    $message->build_message();
    $message->send(STORE_OWNER,IP_SEAL_EMAIL_ADDRESS,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS,$orders_mail_title);
    exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
  }

  if(!$httpResponse) {
    $error_mail_array = tep_get_mail_templates('ORDERS_EMPTY_EMAIL_TEXT',SITE_ID);
    $orders_mail_title = $error_mail_array['title'].'　'.date('Y-m-d H:i:s');
    $message = new email(array('X-Mailer: iimy Mailer'));
    $orders_mail_text = "$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')';
    $text = $orders_mail_text;
    $message->add_html(nl2br($orders_mail_text), $text);
    $message->build_message();
    $message->send(STORE_OWNER,IP_SEAL_EMAIL_ADDRESS,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS,$orders_mail_title);
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
    $error_mail_array = tep_get_mail_templates('ORDERS_EMPTY_EMAIL_TEXT',SITE_ID);
    $orders_mail_title = $error_mail_array['title'].'　'.date('Y-m-d H:i:s');
    $message = new email(array('X-Mailer: iimy Mailer'));
    $orders_mail_text = "Invalid HTTP Response for POST request(".$nvpreq.") to ".$API_Endpoint;
    $text = $orders_mail_text;
    $message->add_html(nl2br($orders_mail_text), $text);
    $message->build_message();
    exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
  }
  return $httpParsedResponseAr;
}
?>
