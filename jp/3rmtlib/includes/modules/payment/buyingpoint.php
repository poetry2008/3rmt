<?php
/*
   $Id$
 */
require_once (DIR_WS_CLASSES . 'basePayment.php');
class buyingpoint extends basePayment  implements paymentInterface  {  
  var $site_id, $code, $title, $description, $enabled, $s_error, $n_fee, $email_footer, $show_payment_info, $additional_title, $show_point;
  
  function loadSpecialSettings($site_id=0){
    $this->site_id = $site_id;    
    $this->code        = 'buyingpoint';
    $this->show_payment_info = 0;
    $this->additional_title = TS_MODULE_PAYMENT_BUYINGPOINT_ADDITIONAL_TEXT_TITLE; 
    $this->show_point = 1; 
  }
  
  function fields($theData=false, $back=false){
  }

  

  function javascript_validation() {
    return false;
  }

  function pre_confirmation_check() {
    return true;
  }

  function confirmation() {
    global $currencies;
    global $_POST;
    global $order;

    $s_result = !$_POST['point_order_fee_error'];
    $this->calc_fee($order->info['total']);
    if (!empty($this->n_fee)) {
      $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['point_order_fee_error'].'</font>'); 
    } else {
      $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['point_order_fee_error'].'</font>'); 
    }
    return array(
        'title' => nl2br(constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_CONFIRMATION")),
        'fields' => array(
          array('title' => constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_SHOW"), 'field' => ''),  
          array('title' => $s_message, 'field' => '')  
          )           
        );

    //return false;
  }

  function check_buy_goods() {
    global $cart;
    return $cart->show_total() > 0;
  }

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
    //return false;
  }

  function before_process() {
    global $_POST;

    $this->email_footer = str_replace("\r\n", "\n", $_POST['point_order_message']);
    //return false;
  }

  function after_process() {
    return false;
  }



  function check() {
    if (!isset($this->_check)) {
      $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_BUYINGPOINT_STATUS' and site_id = '".$this->site_id."'");
      $this->_check = tep_db_num_rows($check_query);
    }
    return $this->_check;
  }

  function install() {
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('買い取りを有効にする', 'MODULE_PAYMENT_BUYINGPOINT_STATUS', 'True', '銀行振込による支払いを受け付けますか?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_BUYINGPOINT_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_BUYINGPOINT_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_BUYINGPOINT_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置 例：0,3000 0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");

    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_BUYINGPOINT_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '1', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ', now(), ".$this->site_id.");");
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_BUYINGPOINT_PREORDER_SHOW', 'True', '予約注文でポイント(買い取り)を表示します', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
  }


  function keys() {
    return array(
        'MODULE_PAYMENT_BUYINGPOINT_STATUS', 
        'MODULE_PAYMENT_BUYINGPOINT_LIMIT_SHOW', 
        'MODULE_PAYMENT_BUYINGPOINT_PREORDER_SHOW',
        'MODULE_PAYMENT_BUYINGPOINT_ORDER_STATUS_ID', 
        'MODULE_PAYMENT_BUYINGPOINT_SORT_ORDER', 
        'MODULE_PAYMENT_BUYINGPOINT_MONEY_LIMIT',
        'MODULE_PAYMENT_BUYINGPOINT_MAILSTRING',
        'MODULE_PAYMENT_BUYINGPOINT_PRINT_MAILSTRING_TITLE',
        'MODULE_PAYMENT_BUYINGPOINT_PRINT_MAILSTRING'
        );
  }
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

  function admin_get_point($point_value){
    
    return abs($point_value['value']);
         
  }

  function admin_get_customer_point($point_value,$customer_id){

    tep_db_query( "update " . TABLE_CUSTOMERS . " set point = point + " . $point_value . 
              " where customers_id = '" .$customer_id."' 
              and customers_guest_chk = '0' ");
  }

  function admin_get_orders_point($orders_id){

    $query_t = tep_db_query("select value from ".TABLE_ORDERS_TOTAL." where class = 'ot_total' and orders_id = '".tep_db_input($orders_id)."'");
    $result_t = tep_db_fetch_array($query_t);
    $get_point = abs(intval($result_t['value']));
    return $get_point;
  }
  function admin_show_payment_list($pay_buying_comment,$pay_convenience_store_comment,$pay_rakuten_bank_comment){

   global $_POST;
   $pay_array = explode("\n",trim($pay_buying_comment));
   $bank_name = explode(':',$pay_array[0]);
   $bank_name[1] = isset($_POST['bank_name']) ? $_POST['bank_name'] : $bank_name[1]; 
   echo 'document.getElementsByName("bank_name")[0].value = "'. $bank_name[1] .'";'."\n"; 
   $bank_shiten = explode(':',$pay_array[1]); 
   $bank_shiten[1] = isset($_POST['bank_shiten']) ? $_POST['bank_shiten'] : $bank_shiten[1];
   echo 'document.getElementsByName("bank_shiten")[0].value = "'. $bank_shiten[1] .'";'."\n"; 
   $bank_kamoku = explode(':',$pay_array[2]);
   $bank_kamoku[1] = isset($_POST['bank_kamoku']) ? $_POST['bank_kamoku'] : $bank_kamoku[1];
   if($bank_kamoku[1] == TS_TEXT_BANK_SELECT_KAMOKU_F){
     echo 'document.getElementsByName("bank_kamoku")[0].checked = true;'."\n"; 
   }else{
     echo 'document.getElementsByName("bank_kamoku")[1].checked = true;'."\n"; 
   }
   $bank_kouza_num = explode(':',$pay_array[3]);
   $bank_kouza_num[1] = isset($_POST['bank_kouza_num']) ? $_POST['bank_kouza_num'] : $bank_kouza_num[1];
   echo 'document.getElementsByName("bank_kouza_num")[0].value = "'.$bank_kouza_num[1].'";'."\n";
   $bank_kouza_name = explode(':',$pay_array[4]);
   $bank_kouza_name[1] = isset($_POST['"bank_kouza_name']) ? $_POST['"bank_kouza_name'] : $bank_kouza_name[1];
   echo 'document.getElementsByName("bank_kouza_name")[0].value = "'.$bank_kouza_name[1].'";'."\n";
   $pay_array = explode("\n",trim($pay_convenience_store_comment));
   $con_email = explode(":",trim($pay_array[0]));
   $con_email[1] = isset($_POST['con_email']) ? $_POST['con_email'] : $con_email[1];
   echo 'document.getElementsByName("con_email")[0].value = "'.$con_email[1].'";'."\n";
   $pay_array = explode("\n",trim($pay_rakuten_bank_comment));
   $rak_tel = explode(":",trim($pay_array[0]));
   $rak_tel[1] = isset($_POST['rak_tel']) ? $_POST['rak_tel'] : $rak_tel[1];
   echo 'document.getElementsByName("rak_tel")[0].value = "'.$rak_tel[1].'";'."\n";
  }
}
?>
