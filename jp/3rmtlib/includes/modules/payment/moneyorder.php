<?php
/*
  $Id$
*/
//银行转账
require_once (DIR_WS_CLASSES . 'basePayment.php');
class moneyorder extends basePayment implements paymentInterface {
  var $site_id, $code, $title, $description, $enabled, $s_error, $n_fee, $email_footer, $show_payment_info;
  // class constructor
/*------------------------------
 功能：加载银行转账支付方法设置
 参数：$site_id (string) SITE_ID值
 返回值：无
 -----------------------------*/
  public function loadSpecialSettings($site_id=0)
  {
    $this->site_id = $site_id;
    $this->code        = 'moneyorder';
    $this->show_payment_info = 0;

  }
  // class methods
/*----------------------------
 功能：检查标志
 参数：无
 返回值：判断是否检查成功(string) 
 ---------------------------*/
  function update_status() {
    global $order;

    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_MONEYORDER_ZONE > 0) ) {
      $check_flag = false;
      $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_MONEYORDER_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
 功能：编辑银行转账
 参数：$theData(boolean) 数据
 参数：$back(boolean) true/false
 返回值：无
 ---------------------------*/
  public function fields($theData=false, $back=false){
    if (!$back) { 
    global $order;
      
    $total_cost = $order->info['total'];
    $f_result = $this->calc_fee($total_cost); 

    $added_hidden = tep_draw_hidden_field('code_fee', $f_result);

    return array(
		 array(
		       "code"=>'',
		       "title"=>'',
		       "field"=>$added_hidden,
		       "rule"=>'',
		       "message"=>"",
		       ),);
    }
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
 功能：确认银行转账
 参数：无
 返回值：无
 ----------------------------*/  
  function confirmation() {
    global $currencies;
    $s_result = $_SESSION['h_code_fee'];
    $s_message = $s_result ? '':('<font color="#FF0000">'.$_SESSION['h_code_fee'].'</font>'); 
    return array(
                 'title' => nl2br(constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_CONFIRMATION")),
                 'fields' => array(
                                   array('title' => constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_SHOW"), 'field' => ''),  
                                   array('title' => $s_message, 'field' => '')  
                                   )           
                 );

  }
 /*----------------------
   功能：银行转账的过程按钮
   参数：无
   返回值：无
   ---------------------*/
  function process_button() {
    global $currencies;
    global $_POST; 
    global $order;

    $total = $order->info['total'];
    if ($payment == 'moneyorder') {
      $total += intval($_SESSION['h_code_fee']); 
    }
      
    $s_message = $_POST['code_fee_error']?$_POST['money_order_fee_error']:sprintf(TS_MODULE_PAYMENT_MONEY_ORDER_TEXT_MAILFOOTER, $currencies->format($total), $currencies->format($_SESSION['h_code_fee']));
      
    return tep_draw_hidden_field('money_order_message', htmlspecialchars($s_message)). tep_draw_hidden_field('code_fee', $_SESSION['h_code_fee']);
  }
/*-----------------------
 功能：银行转账前
 参数：无
 返回值：无
 ----------------------*/
  function before_process() {
    global $_POST;

    $this->email_footer = str_replace("\r\n", "\n", $_POST['money_order_message']);
      
  }
/*-------------------------
 功能：银行转账后 
 参数：无
 返回值：无
 -------------------------*/
  function after_process() {
    return false;
  }
/*-------------------------
 功能：获取错误
 参数：无
 返回值：错误信息(array)
 ------------------------*/
  function get_error() {
    global $_POST, $_GET;

    if (isset($_GET['payment_error']) && (strlen($_GET['payment_error']) > 0)) {
      $error_message = TS_MODULE_PAYMENT_MONEY_ORDER_TEXT_ERROR_MESSAGE;
        
      return array('title' => $this->title.' エラー!', 'error' => $error_message);
    } else {
      return false;
    }
  }
/*---------------------------
 功能：检查SQL
 参数：无
 返回值：SQL(string)
 --------------------------*/  
  function check() {
    if (!isset($this->_check)) {
      $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_MONEYORDER_STATUS' and site_id = '".$this->site_id."'");
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
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added,user_added, site_id) values ('銀行振込を有効にする', 'MODULE_PAYMENT_MONEYORDER_STATUS', 'True', '銀行振込による支払いを受け付けますか?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(),'".$_SESSION['user_name']."', ".$this->site_id.");");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('お振込先:', 'MODULE_PAYMENT_MONEYORDER_PAYTO', '', 'お振込先名義を設定してください.', '6', '1', now(), ".$this->site_id.");");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_MONEYORDER_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, site_id) values ('適用地域', 'MODULE_PAYMENT_MONEYORDER_ZONE', '0', '適用地域を選択すると、選択した地域のみで利用可能となります.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済手数料', 'MODULE_PAYMENT_MONEYORDER_COST', '99999999999:*0', '決済手数料 例: 代金300円以下、30円手数料をとる場合　300:*0+30, 代金301～1000円以内、代金の2％の手数料をとる場合　999:*0.02, 代金1000円以上の場合、手数料を無料する場合　99999999:*0, 無限大の符号を使えないため、このサイトで存在可能性がない数値で使ってください。 300:*0+30では*0がなければ、手数料は300+30になってしまいますので、ご注意ください。', '6', '3', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_MONEYORDER_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置
例：0,3000
0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");
      
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_MONEYORDER_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '1', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ', now(), ".$this->site_id.");");
     
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_MONEYORDER_PREORDER_SHOW', 'True', '予約注文で銀行振込を表示します', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('ポイント', 'MODULE_PAYMENT_MONEYORDER_IS_GET_POINT', 'True', 'ポイント', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('ポイント還元率', 'MODULE_PAYMENT_MONEYORDER_POINT_RATE', '0.01', 'ポイント還元率', '6', '0', now(), ".$this->site_id.")");
  }
/*--------------------------------
 功能：删除SQL 
 参数：无
 返回值：无
 -------------------------------*/
  function remove() {
    tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
  }
/*-----------------------------
 功能：编辑银行转账方法
 参数：无
 返回值：银行转账方法数据
 ----------------------------*/
    function keys() {
      return array(
                   'MODULE_PAYMENT_MONEYORDER_STATUS',
                   'MODULE_PAYMENT_MONEYORDER_LIMIT_SHOW',
                   'MODULE_PAYMENT_MONEYORDER_PREORDER_SHOW',
                   'MODULE_PAYMENT_MONEYORDER_IS_GET_POINT',
                   'MODULE_PAYMENT_MONEYORDER_POINT_RATE',
                   'MODULE_PAYMENT_MONEYORDER_ZONE',
                   'MODULE_PAYMENT_MONEYORDER_ORDER_STATUS_ID',
                   'MODULE_PAYMENT_MONEYORDER_PREORDER_STATUS_ID',
                   'MODULE_PAYMENT_MONEYORDER_SORT_ORDER',
                   'MODULE_PAYMENT_MONEYORDER_PAYTO',
                   'MODULE_PAYMENT_MONEYORDER_COST',
                   'MODULE_PAYMENT_MONEYORDER_MONEY_LIMIT',
                   );
    }
/*----------------------------
 功能：获取邮件的字符串
 参数：$option(string) 选项
 返回值：邮件字符串(string)
 ---------------------------*/
  function getMailString($option=''){
    $email_printing_order .= 'この注文は【販売】です。' . "\n";
    $email_printing_order .=
      '------------------------------------------------------------------------'
      . "\n";
    $email_printing_order .= '備考の有無　　　　　：□ 無　　｜　　□ 有　→　□
      返答済' . "\n";
    $email_printing_order .=
      '------------------------------------------------------------------------'
      . "\n";
    $email_printing_order .= '在庫確認　　　　　　：□ 有　　｜　　□
      無　→　入金確認後仕入' . "\n";
    $email_printing_order .=
      '------------------------------------------------------------------------'
      . "\n";
    $email_printing_order .=
      '入金確認　　　　　●：＿＿月＿＿日　→　金額は' .
      abs($option) . '円ですか？　□ はい' . "\n";
    $email_printing_order .=
      '------------------------------------------------------------------------'
      . "\n";
    $email_printing_order .= '入金確認メール送信　：□ 済' . "\n";
    $email_printing_order .=
      '------------------------------------------------------------------------'
      . "\n";
    $email_printing_order .=
      '発送　　　　　　　　：＿＿月＿＿日' . "\n";
    $email_printing_order .=
      '------------------------------------------------------------------------'
      . "\n";
    $email_printing_order .= '残量入力→誤差有無　：□
      無　　｜　　□ 有　→　報告　□' . "\n";
    $email_printing_order .=
      '------------------------------------------------------------------------'
      . "\n";
    $email_printing_order .= '発送完了メール送信　：□
      済' . "\n";    
    $email_printing_order .=
      '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '最終確認　　　　　　：確認者名＿＿＿＿' . "\n";
    $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
    return $email_printing_order;
  }
/*-------------------------------
 功能：获取电子邮件的配置
 参数：$site_id(string) SITE_ID值
 参数：$oid(string) ID值
 ------------------------------*/
  function get_email_configuration($site_id,$oid=0){
    return get_configuration_by_site_id('C_BANK',$site_id);
  }
/*-----------------------------
 功能：显示后台支付方法列表 
 参数：$pay_info_array(string) 支付信息数组
 返回值：无
 ----------------------------*/
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
   if($bank_kamoku[1] == TS_MODULE_PAYMENT_MONEYORDER_NORMAL || $bank_kamoku[1] == ''){
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
 /*----------------------------
 功能：获取后台顾客点值 
 参数：$point_value(string) 点值
 参数：$customer_id(string) 顾客ID
 返回值：无
 ---------------------------*/    
  function admin_get_customer_point($point_value,$customer_id){
    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . $point_value .  " where customers_id = '" .$customer_id."' and customers_guest_chk = '0' ");
  } 
/*----------------------------
 功能：获取后台支付方法信息评论 
 参数：$customers_email(string) 顾客的邮件
 参数：$site_id(string) SITE_ID值
 参数：$orders_type(string) 订单类型
 参数：$gray_single(int) 信息
 返回值：支付方法的订单ID(string) 
 ---------------------------*/
  function admin_get_payment_info_comment($customers_email,$site_id,$orders_type,$gray_single){

     return array(6);
  }
  
/*--------------------------
 功能：后台获取点值
 参数：$site_id(string) SITE_ID值
 返回值：点值(string)
 -------------------------*/  
  function admin_is_get_point($site_id)
  {
    return get_configuration_by_site_id_or_default('MODULE_PAYMENT_MONEYORDER_IS_GET_POINT', (int)$site_id); 
  }
/*------------------------
 功能：后台获取点率
 参数：$site_id(string) SITE_ID值
 返回值：点率(string) 
 -----------------------*/  
  function admin_get_point_rate($site_id)
  {
    return get_configuration_by_site_id_or_default('MODULE_PAYMENT_MONEYORDER_POINT_RATE', (int)$site_id); 
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
    return MODULE_PAYMENT_MONEYORDER_POINT_RATE; 
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
?>
