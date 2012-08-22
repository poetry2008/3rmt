<?php
/*
  $Id$
*/
require_once (DIR_WS_CLASSES . 'basePayment.php');
class postalmoneyorder extends basePayment  implements paymentInterface  { 
  var $site_id, $code, $title, $description, $enabled, $n_fee, $email_footer, $s_error,$c_prefix, $show_payment_info;

  // class constructor
  function loadSpecialSettings($site_id=0){
    $this->code        = 'postalmoneyorder';
    $this->site_id      = $site_id;
    $this->show_payment_info = 0;
  }
  
  function javascript_validation() {
    return false;
  }

  function selection($theData) {

    global $currencies;

    $returnArray = array(
			 "id"=>$this->code,
			 'module' =>$this->title,
			 'description'=>$this->explain,
			 'field_description'=>'',
			 'footer'=>"",
			 'fields'=>$this->fields($theData),
			 );
    $this->selection = $returnArray;
    return $returnArray;

  }
     


  function fields($theData=false, $back=false){
    if (!$back) {
    global $order;
    $total_cost = $order->info['total'];
    $code_fee = $this->calc_fee($total_cost); 
    $added_hidden = tep_draw_hidden_field('code_fee', $code_fee);
    return array(
		 array(
		       "code"=>'bank_name',
		       "title"=>'',
		       "field"=>$added_hidden,
		       "rule"=>'',
		       "message"=>"",
		       ));
    }
  }
  function pre_confirmation_check() {
    return true;
  }

  function confirmation() {
    global $currencies;
    global $_POST;
     

    return array(
                 'title' => nl2br(constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_CONFIRMATION")),
                 'fields' => array(
                                   array('title' => constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_SHOW"), 'field' => ''),  
                                   array('title' => $s_message, 'field' => '')  
                                   )           
                 );

  }

  function process_button() {
    global $currencies;
    global $_POST; 
    global $order;

    $total = $order->info['total'];
    if ($payment == 'postalmoneyorder') {
      $total += intval($_SESSION['h_code_fee']); 
    }
      
    $s_message = $_POST['postal_money_order_fee_error']?$_POST['postal_money_order_fee_error']:sprintf(MODULE_PAYMENT_POSTALMONEY_ORDER_TEXT_MAILFOOTER, $currencies->format($total), $currencies->format($_POST['postal_money_order_fee']));
      
    return tep_draw_hidden_field('postal_money_order_message', htmlspecialchars($s_message)). tep_draw_hidden_field('code_fee', $_SESSION['h_code_fee']);
    //return false;
  }

  function before_process() {
    global $_POST;

    $this->email_footer = str_replace("\r\n", "\n", $_POST['postal_money_order_message']);
    return false;
  }

  function after_process() {
    return false;
  }

  function get_error() {
    global $_POST, $_GET;

    if (isset($_GET['payment_error']) && (strlen($_GET['payment_error']) > 0)) {
      $error_message = MODULE_PAYMENT_POSTALMONEY_ORDER_TEXT_ERROR_MESSAGE;
        
      return array('title' => $this->title.' エラー!', 'error' => $error_message);
    } else {
      return false;
    }
    return false;
  }

  function check() {
    if (!isset($this->_check)) {
      $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_POSTALMONEYORDER_STATUS' and site_id = '".$this->site_id."'");
      $this->_check = tep_db_num_rows($check_query);
    }
    return $this->_check;
  }

  function install() {
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('ゆうちょ銀行（郵便局）を有効にする', 'MODULE_PAYMENT_POSTALMONEYORDER_STATUS', 'True', 'ゆうちょ銀行（郵便局）による支払いを受け付けますか?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('お振込先:', 'MODULE_PAYMENT_POSTALMONEYORDER_PAYTO', '', 'お振込先名義を設定してください.', '6', '1', now(), ".$this->site_id.");");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_POSTALMONEYORDER_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, site_id) values ('適用地域', 'MODULE_PAYMENT_POSTALMONEYORDER_ZONE', '0', '適用地域を選択すると、選択した地域のみで利用可能となります.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_POSTALMONEYORDER_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済手数料', 'MODULE_PAYMENT_POSTALMONEYORDER_COST', '99999999999:*0', '決済手数料 例: 代金300円以下、30円手数料をとる場合　300:*0+30, 代金301～1000円以内、代金の2％の手数料をとる場合　999:*0.02, 代金1000円以上の場合、手数料を無料する場合　99999999:*0, 無限大の符号を使えないため、このサイトで存在可能性がない数値で使ってください。 300:*0+30では*0がなければ、手数料は300+30になってしまいますので、ご注意ください。', '6', '3', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_POSTALMONEYORDER_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置
例：0,3000
0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");
      
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_POSTALMONEYORDER_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '1', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ', now(), ".$this->site_id.");");
      
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_POSTALMONEYORDER_PREORDER_SHOW', 'True', '予約注文でゆうちょ銀行（郵便局）を表示します', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
  }

  function remove() {
    tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
  }


    function keys() {
      return array('MODULE_PAYMENT_POSTALMONEYORDER_STATUS',
                   'MODULE_PAYMENT_POSTALMONEYORDER_LIMIT_SHOW',
                   'MODULE_PAYMENT_POSTALMONEYORDER_PREORDER_SHOW',
                   'MODULE_PAYMENT_POSTALMONEYORDER_ZONE',
                   'MODULE_PAYMENT_POSTALMONEYORDER_ORDER_STATUS_ID',
                   'MODULE_PAYMENT_POSTALMONEYORDER_SORT_ORDER',
                   'MODULE_PAYMENT_POSTALMONEYORDER_PAYTO',
                   'MODULE_PAYMENT_POSTALMONEYORDER_COST',
                   'MODULE_PAYMENT_POSTALMONEYORDER_MONEY_LIMIT',
                   'MODULE_PAYMENT_POSTALMONEYORDER_MAILSTRING',
                   'MODULE_PAYMENT_POSTALMONEYORDER_PRINT_MAILSTRING_TITLE',
                   'MODULE_PAYMENT_POSTALMONEYORDER_PRINT_MAILSTRING',
);
    }
    

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

  function get_email_configuration($site_id,$oid=0){
    return get_configuration_by_site_id('C_POSTAL',$site_id);
  }
}
?>
