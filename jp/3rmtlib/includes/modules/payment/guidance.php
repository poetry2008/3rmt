<?php
/*
   $Id$
 */

// 代金引換払い(手数料が購入金額に連動)
require_once (DIR_WS_CLASSES . 'basePayment.php');
class guidance extends basePayment  implements paymentInterface  {
  var $site_id, $code, $title, $description, $enabled, $n_fee, $s_error, $email_footer,$c_prefix, $show_payment_info;

  function loadSpecialSettings($site_id = 0)
  {
    $this->site_id = $site_id;
    $this->code        = 'guidance';
    $this->show_payment_info = 0;
  }


  // class methods
  function update_status() {
    global $order;
    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_GUIDANCE_ZONE > 0) ) {
      $check_flag = false;
      $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_GUIDANCE_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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



  // class methods
  function javascript_validation() {
    return false;
  }
  function fields($theData, $back=false){
  }

  function pre_confirmation_check() {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error='.$this->code, 'SSL', true, false)); 
  }
  
  function preorder_confirmation_check() {
    return 1; 
  }

  function confirmation() {
    return ''; 
  }

  function process_button() {
    return ''; 
  }

  function before_process() {
  }

  function after_process() {
    return false;
  }

  function get_error() {
    return array('title' => $this->title.'エラー!',
        'error' => 'エラー：案内をよく読み、手順に沿ってお手続きください。'); 
  }
  
  function get_preorder_error($error_type) {
    return 'エラー：案内をよく読み、手順に沿ってお手続きください。'; 
  }

  function check() {
    if (!isset($this->_check)) {
      $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_GUIDANCE_STATUS' and site_id = '".$this->site_id."'");
      $this->_check = tep_db_num_rows($check_query);
    }
    return $this->_check;
  }

  function install() {
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('ウェブマネー及びゲーム間移動を有効にする', 'MODULE_PAYMENT_GUIDANCE_STATUS', 'True', '楽天銀行による支払いを受け付けますか?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ',now(), ".$this->site_id.");");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済手数料', 'MODULE_PAYMENT_GUIDANCE_COST', '99999999999:*0', '決済手数料 例: 代金300円以下、30円手数料をとる場合　300:*0+30, 代金301～1000円以内、代金の2％の手数料をとる場合　999:*0.02, 代金1000円以上の場合、手数料を無料する場合　99999999:*0, 無限大の符号を使えないため、このサイトで存在可能性がない数値で使ってください。 300:*0+30では*0がなければ、手数料は300+30になってしまいますので、ご注意ください。', '6', '3', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_GUIDANCE_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0' , now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, site_id) values ('適用地域', 'MODULE_PAYMENT_GUIDANCE_ZONE', '0', '適用地域を選択すると、選択した地域のみで利用可能となります.', '6', '4', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_GUIDANCE_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '5', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_GUIDANCE_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置
      例：0,3000
      0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");

    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_GUIDANCE_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '1', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ',now(), ".$this->site_id.");");

    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_GUIDANCE_PREORDER_SHOW', 'True', '予約注文で楽天銀行を表示します', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ',now(), ".$this->site_id.");");
  }

  function remove() {
    tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
  }

  function keys() {
    return array( 
        'MODULE_PAYMENT_GUIDANCE_STATUS', 
        'MODULE_PAYMENT_GUIDANCE_LIMIT_SHOW', 
        'MODULE_PAYMENT_GUIDANCE_PREORDER_SHOW', 
        'MODULE_PAYMENT_GUIDANCE_ZONE', 
        'MODULE_PAYMENT_GUIDANCE_ORDER_STATUS_ID' , 
        'MODULE_PAYMENT_GUIDANCE_SORT_ORDER', 
        'MODULE_PAYMENT_GUIDANCE_COST', 
        'MODULE_PAYMENT_GUIDANCE_MONEY_LIMIT',
        'MODULE_PAYMENT_GUIDANCE_MAILSTRING',
);
  }

}
?>