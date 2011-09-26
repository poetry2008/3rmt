<?php
/*
  $Id$
*/

  class free_payment {
    var $site_id, $code, $title, $description, $enabled, $s_error, $n_fee, $email_footer;

// class constructor
    function free_payment ($site_id = 0) {
      global $order;
      
      $this->site_id = $site_id;

      $this->code        = 'free_payment';
      $this->title       = MODULE_PAYMENT_FREE_PAYMENT_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_FREE_PAYMENT_TEXT_DESCRIPTION;
      $this->explain       = MODULE_PAYMENT_FREE_PAYMENT_TEXT_EXPLAIN;
      $this->sort_order  = MODULE_PAYMENT_FREE_PAYMENT_SORT_ORDER;
      $this->enabled     = ((MODULE_PAYMENT_FREE_PAYMENT_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_FREE_PAYMENT_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_FREE_PAYMENT_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();
    
      $this->email_footer = MODULE_PAYMENT_FREE_PAYMENT_TEXT_EMAIL_FOOTER;
    }

// class methods
    function update_status() {
      global $order;
      return true;
    }
    
    function calc_fee($total_cost) {
      return 0;
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      global $currencies;
      global $order;
      
      $total_cost = $order->info['total'];
      $f_result = $this->calc_fee($total_cost); 
      
      $added_hidden = $f_result ? tep_draw_hidden_field('free_payment_order_fee', $this->n_fee):tep_draw_hidden_field('free_payment_order_fee_error', $this->s_error);
      
      if (!empty($this->n_fee)) {
        $s_message = $f_result ? (MODULE_PAYMENT_FREE_PAYMENT_TEXT_FEE . '&nbsp;' .  $currencies->format($this->n_fee)):('<font color="#FF0000">'.$this->s_error.'</font>'); 
      } else {
        $s_message = $f_result ? '':('<font color="#FF0000">'.$this->s_error.'</font>'); 
      }
      return array('id' => $this->code,
                   'module' => $this->title,
                   'fields' => array(
                     array('title' => MODULE_PAYMENT_FREE_PAYMENT_TEXT_DESCRIPTION, 'field' => ''),
                     array('title' => $s_message, 'field' => '') 
                     )             
      );
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      global $currencies;
      global $_POST;
      global $order;
      
      $s_result = !$_POST['free_payment_order_fee_error'];
      $this->calc_fee($order->info['total']);
      if (!empty($this->n_fee)) {
        $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['free_payment_order_fee_error'].'</font>'); 
      } else {
        $s_message = $s_result ? '':('<font color="#FF0000">'.$_POST['free_payment_order_fee_error'].'</font>'); 
      }
      
      if (!empty($this->n_fee)) {
        return array(
            'title' => MODULE_PAYMENT_FREE_PAYMENT_TEXT_DESCRIPTION,
            'fields' => array(array('title' => MODULE_PAYMENT_FREE_PAYMENT_TEXT_PROCESS,
                                    'field' => ''),
                              array('title' => $s_message, 'field' => '')  
                       )           
            );
      } else {
          return array(
              'title' => MODULE_PAYMENT_FREE_PAYMENT_TEXT_DESCRIPTION,
              'fields' => array(array('title' => $s_message, 'field' => '')  
                         )           
              );
      }
    }
    
    function process_button() {
      global $currencies;
      global $_POST; 
      global $order;

      $total = $order->info['total'];
      if ($payment == 'freepayment') {
        $total += intval($_POST['free_payment_order_fee']); 
      }
      
      $s_message =
        $_POST['free_payment_order_fee_error']?$_POST['free_payment_order_fee_error']:sprintf(MODULE_PAYMENT_FREE_PAYMENT_TEXT_MAILFOOTER, $currencies->format($total), $currencies->format($_POST['free_payment_order_fee']));
      
      return tep_draw_hidden_field('free_payment_order_message', htmlspecialchars($s_message)).  tep_draw_hidden_field('free_payment_order_fee', $_POST['free_payment_order_fee']);
      //return false;
    }

    function before_process() {
      global $_POST;

      $this->email_footer = str_replace("\r\n", "\n", $_POST['free_payment_order_message']);
      //return false;
    }

    function after_process() {
      return false;
    }

    function get_error() {
      global $_POST, $_GET;

      if (isset($_GET['payment_error']) && (strlen($_GET['payment_error']) > 0)) {
        $error_message = MODULE_PAYMENT_FREE_PAYMENT_TEXT_ERROR_MESSATE;
        
        return array('title' => 'コンビニ決済 エラー!', 'error' => $error_message);
      } else {
        return false;
      }
      //return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " .  TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_FREE_PAYMENT_STATUS' and site_id = '".$this->site_id."'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('買い取りを有効にする', 'MODULE_PAYMENT_FREE_PAYMENT_STATUS', 'True', '銀行振込による支払いを受け付けますか?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_FREE_PAYMENT_SORT_ORDER', '9', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_FREE_PAYMENT_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_FREE_PAYMENT_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置 例：0,3000 0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");
      
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_FREE_PAYMENT_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '1', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ', now(), ".$this->site_id.");");
    }

    //function remove() {
    //  tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    //}

    function keys() {
      return array('MODULE_PAYMENT_FREE_PAYMENT_STATUS', 'MODULE_PAYMENT_FREE_PAYMENT_LIMIT_SHOW', 'MODULE_PAYMENT_FREE_PAYMENT_ORDER_STATUS_ID', 'MODULE_PAYMENT_FREE_PAYMENT_SORT_ORDER', 'MODULE_PAYMENT_FREE_PAYMENT_MONEY_LIMIT');
    }
  }
?>
