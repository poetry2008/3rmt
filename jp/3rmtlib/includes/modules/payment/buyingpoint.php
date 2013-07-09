<?php
/*
   $Id$
 */
require_once (DIR_WS_CLASSES . 'basePayment.php');
class buyingpoint extends basePayment  implements paymentInterface  {  
  var $site_id, $code, $title, $description, $enabled, $s_error, $n_fee, $email_footer, $show_payment_info, $additional_title, $show_point;
 /*------------------------------
 功能：加载购买点方法设置
 参数：$site_id (string) SITE_ID值
 返回值：无
 -----------------------------*/
  function loadSpecialSettings($site_id=0){
    $this->site_id = $site_id;    
    $this->code        = 'buyingpoint';
    $this->show_payment_info = 0;
    $this->additional_title = TS_MODULE_PAYMENT_BUYINGPOINT_ADDITIONAL_TEXT_TITLE; 
    $this->show_point = 1; 
  }
 /*----------------------------
 功能：编辑点数
 参数：$theData(boolean) 数据
 参数：$back(boolean) true/false
 返回值：返回点数类型数据(array)
 ---------------------------*/
  
  function fields($theData=false, $back=false){
  }

  
/*-----------------------------
 功能：JS验证
 参数：无
 返回值：JS验证是否成功(boolean)
 ----------------------------*/
  function javascript_validation() {
    return false;
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
 功能：确认点数支付方法
 参数：无
 返回值：点数支付方法数据(array)
 ----------------------------*/  
  function confirmation() {
    global $currencies;
    global $_POST;
    global $order;

    $s_result = !$_POST['point_order_fee_error'];
    $this->calc_fee($order->info['total']);
    $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['point_order_fee_error'].'</font>'); 
    return array(
        'title' => nl2br(constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_CONFIRMATION")),
        'fields' => array(
          array('title' => constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_SHOW"), 'field' => ''),  
          array('title' => $s_message, 'field' => '')  
          )           
        );

  }
/*----------------------
 功能：检查购买的商品
 参数：无
 返回值：显示购买总计(string)
 ----------------------*/
  function check_buy_goods() {
    global $cart;
    return $cart->show_total() > 0;
  }
/*----------------------
 功能：判断支付方法返回的信息
 参数：无
 返回值：隐藏INPUT购买订单的信息(string)
 ---------------------*/
  function process_button() {
    global $currencies;
    global $_POST; 
    global $order;

    $total = $order->info['total'];
    if ($payment == 'point') {
      $total += intval($_POST['point_order_fee']); 
    }

    $s_message = $_POST['point_order_fee_error']?$_POST['point_order_fee_error']:sprintf(TS_MODULE_PAYMENT_BUYINGPOINT_TEXT_MAILFOOTER, $currencies->format($total), $currencies->format($_POST['point_order_fee']));

    return tep_draw_hidden_field('point_order_message', htmlspecialchars($s_message)). tep_draw_hidden_field('point_order_fee', $_POST['point_order_fee']);
  }
/*-----------------------
 功能：购买前
 参数：无
 返回值：无
 ----------------------*/
  function before_process() {
    global $_POST;

    $this->email_footer = str_replace("\r\n", "\n", $_POST['point_order_message']);
  }
/*-----------------------
 功能：购买后
 参数：无
 返回值：false(boolean)
 ----------------------*/
  function after_process() {
    return false;
  }



/*---------------------------
 功能：检查SQL
 参数：无
 返回值：SQL(string)
 --------------------------*/  
  function check() {
    if (!isset($this->_check)) {
      $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_BUYINGPOINT_STATUS' and site_id = '".$this->site_id."'");
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
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added,user_added, site_id) values ('買い取りを有効にする', 'MODULE_PAYMENT_BUYINGPOINT_STATUS', 'True', '銀行振込による支払いを受け付けますか?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(),'".$_SESSION['user_name']."', ".$this->site_id.");");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_BUYINGPOINT_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_BUYINGPOINT_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_BUYINGPOINT_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置 例：0,3000 0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");

    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_BUYINGPOINT_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '1', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ', now(), ".$this->site_id.");");
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_BUYINGPOINT_PREORDER_SHOW', 'True', '予約注文でポイント(買い取り)を表示します', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('ポイント', 'MODULE_PAYMENT_BUYINGPOINT_IS_GET_POINT', '1', 'ポイント', '6', '1', 'tep_cfg_payment_new_checkbox(', now(), ".$this->site_id.");");
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('ポイント還元率', 'MODULE_PAYMENT_BUYINGPOINT_POINT_RATE', '1', 'ポイント還元率', '6', '0', now(), ".$this->site_id.")");
  }

/*-----------------------------
 功能：编辑点数方法
 参数：无
 返回值：点数方法数据
 ----------------------------*/
  function keys() {
    return array(
        'MODULE_PAYMENT_BUYINGPOINT_STATUS', 
        'MODULE_PAYMENT_BUYINGPOINT_LIMIT_SHOW', 
        'MODULE_PAYMENT_BUYINGPOINT_PREORDER_SHOW',
        'MODULE_PAYMENT_BUYINGPOINT_IS_GET_POINT',
        'MODULE_PAYMENT_BUYINGPOINT_POINT_RATE',
        'MODULE_PAYMENT_BUYINGPOINT_ORDER_STATUS_ID', 
        'MODULE_PAYMENT_BUYINGPOINT_PREORDER_STATUS_ID',
        'MODULE_PAYMENT_BUYINGPOINT_SORT_ORDER', 
        'MODULE_PAYMENT_BUYINGPOINT_MONEY_LIMIT',
        );
  }
/*----------------------------
 功能：获取邮件的字符串
 参数：$option(string) 选项
 返回值：邮件字符串(string)
 ---------------------------*/
 function getMailString($option=''){
    $email_printing_order ='';
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
    $email_printing_order .=
    '------------------------------------------------------------------------' . "\n";
    $email_printing_order .= '最終確認　　　　　　：確認者名＿＿＿＿' . "\n";
    $email_printing_order .= '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━' . "\n";
      
    return $email_printing_order;
  }
/*------------------------------
 功能：获取后台点数
 参数：$point_value(string) 点值
 返回值：点数(string) 
 -----------------------------*/
  function admin_get_point($point_value){
    
    return abs($point_value['value']);
         
  }
/*------------------------------
 功能：获取后台订单点数
 参数：$orders_id(string) 订单ID
 返回值：点数(string) 
 -----------------------------*/
  function admin_get_orders_point($orders_id){

    $query_t = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_total' and orders_id = '".tep_db_input($orders_id)."'");
    $result_t = tep_db_fetch_array($query_t);
    $get_point = abs(intval($result_t['value']));
    return $get_point;
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
   if($bank_kamoku[1] == TS_MODULE_PAYMENT_BUYINGPOINT_NORMAL || $bank_kamoku[1] == ''){
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
 功能：获取后台支付方法信息评论 
 参数：$customers_email(string) 顾客的邮件
 参数：$site_id(string) SITE_ID值
 参数：$orders_type(string) 订单类型
 返回值：支付方法的订单ID(string) 
 ---------------------------*/
  function admin_get_payment_info_comment($customers_email,$site_id){
 
    return array(3);
  }
 /*-----------------------------
 功能：后台获得取点 
 参数：无
 返回值：点值(string)
 ----------------------------*/  
  function admin_get_fetch_point(){
    return 0;
  }

/*---------------------------
 功能：得到点值
 参数：无
 返回值：得到点值(boolean)
 --------------------------*/  
  function is_get_point()
  {
    return true; 
  }

 /*--------------------------
 功能：后台获取点值
 参数：$site_id(string) SITE_ID值
 返回值：点值(string)
 -------------------------*/  
  function admin_is_get_point($site_id)
  {
    return get_configuration_by_site_id_or_default('MODULE_PAYMENT_BUYINGPOINT_IS_GET_POINT', (int)$site_id); 
  }
/*------------------------
 功能：后台获取点率
 参数：$site_id(string) SITE_ID值
 返回值：点率(string) 
 -----------------------*/  
  function admin_get_point_rate($site_id)
  {
    return get_configuration_by_site_id_or_default('MODULE_PAYMENT_BUYINGPOINT_POINT_RATE', (int)$site_id); 
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
    return MODULE_PAYMENT_BUYINGPOINT_POINT_RATE; 
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
