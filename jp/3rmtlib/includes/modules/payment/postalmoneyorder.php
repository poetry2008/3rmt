<?php
/*
  $Id$
*/

  class postalmoneyorder {
    var $site_id, $code, $title, $description, $enabled, $n_fee, $email_footer, $s_error;

// class constructor
    function postalmoneyorder($site_id = 0) {
      global $order;
      
      $this->site_id = $site_id;

      $this->code        = 'postalmoneyorder';
      $this->title       = MODULE_PAYMENT_POSTALMONEYORDER_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_POSTALMONEYORDER_TEXT_DESCRIPTION;
      $this->explain     = MODULE_PAYMENT_POSTALMONEYORDER_TEXT_EXPLAIN;
      $this->sort_order  = MODULE_PAYMENT_POSTALMONEYORDER_SORT_ORDER;
      $this->enabled     = ((MODULE_PAYMENT_POSTALMONEYORDER_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_POSTALMONEYORDER_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_POSTALMONEYORDER_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();
    
      $this->email_footer = MODULE_PAYMENT_POSTALMONEYORDER_TEXT_EMAIL_FOOTER;
    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_POSTALMONEYORDER_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_POSTALMONEYORDER_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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

    function calc_fee($total_cost) {
      $table_fee = split("[:,]" , MODULE_PAYMENT_POSTALMONEYORDER_COST);
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
        $this->s_error = MODULE_PAYMENT_POSTALMONEYORDER_TEXT_OVERFLOW_ERROR;
      }
      return $f_find; 
    }
    function javascript_validation() {
      return false;
    }

    function selection() {
      global $currencies;
      global $order;
      
      $total_cost = $order->info['total'];
      $f_result = $this->calc_fee($total_cost); 
      
      $added_hidden = $f_result ? tep_draw_hidden_field('postal_money_order_fee', $this->n_fee):tep_draw_hidden_field('postal_money_order_fee_error', $this->s_error);
     
      if (!empty($this->n_fee)) {
        $s_message = $f_result ? (MODULE_PAYMENT_POSTALMONEY_ORDER_TEXT_FEE . '&nbsp;' .  $currencies->format($this->n_fee)):('<font color="#FF0000">'.$this->s_error.'</font>'); 
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
      global $currencies;
      global $_POST;
      
      $s_result = !$_POST['postal_money_order_fee_error'];
     
      if (!empty($_POST['postal_money_order_fee'])) {
        //$s_message = $s_result ? (MODULE_PAYMENT_POSTALMONEY_ORDER_TEXT_FEE . '&nbsp;' .  $currencies->format($_POST['postal_money_order_fee'])):('<font color="#FF0000">'.$_POST['postal_money_order_fee_error'].'</font>'); 
        $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['postal_money_order_fee_error'].'</font>'); 
      } else {
        $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['postal_money_order_fee_error'].'</font>'); 
      }
      
      if (!empty($_POST['postal_money_order_fee'])) {
        return array(
            'title' => MODULE_PAYMENT_POSTALMONEYORDER_TEXT_DESCRIPTION,
            'fields' => array(array('title' => MODULE_PAYMENT_POSTALMONEY_ORDER_TEXT_PROCESS,
                                    'field' => ''),
                              array('title' => $s_message, 'field' => '')  
                       )           
            );
      } else {
        return array(
            'title' => MODULE_PAYMENT_POSTALMONEYORDER_TEXT_DESCRIPTION,
            'fields' => array(array('title' => $s_message, 'field' => '')  
                       )           
            );
      }
      //return array('title' => MODULE_PAYMENT_POSTALMONEYORDER_TEXT_DESCRIPTION);
    }

    function process_button() {
      global $currencies;
      global $_POST; 
      global $order;

      $total = $order->info['total'];
      if ($payment == 'postalmoneyorder') {
        $total += intval($_POST['postal_money_order_fee']); 
      }
      
      $s_message = $_POST['postal_money_order_fee_error']?$_POST['postal_money_order_fee_error']:sprintf(MODULE_PAYMENT_POSTALMONEY_ORDER_TEXT_MAILFOOTER, $currencies->format($total), $currencies->format($_POST['postal_money_order_fee']));
      
      return tep_draw_hidden_field('postal_money_order_message', htmlspecialchars($s_message)). tep_draw_hidden_field('postal_money_order_fee', $_POST['postal_money_order_fee']);
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
        
        return array('title' => 'コンビニ決済 エラー!', 'error' => $error_message);
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
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    }

    function keys() {
      return array('MODULE_PAYMENT_POSTALMONEYORDER_STATUS', 'MODULE_PAYMENT_POSTALMONEYORDER_LIMIT_SHOW', 'MODULE_PAYMENT_POSTALMONEYORDER_ZONE', 'MODULE_PAYMENT_POSTALMONEYORDER_ORDER_STATUS_ID', 'MODULE_PAYMENT_POSTALMONEYORDER_SORT_ORDER', 'MODULE_PAYMENT_POSTALMONEYORDER_PAYTO', 'MODULE_PAYMENT_POSTALMONEYORDER_COST', 'MODULE_PAYMENT_POSTALMONEYORDER_MONEY_LIMIT');
    }
  }
?>
