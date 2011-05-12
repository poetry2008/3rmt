<?php
/*
  $Id$
*/

  class ot_total {
    var $site_id, $title, $output;

    function ot_total($site_id = 0) {

      $this->site_id = $site_id;

      $this->code = 'ot_total';
      $this->title = MODULE_ORDER_TOTAL_TOTAL_TITLE;
      $this->description = MODULE_ORDER_TOTAL_TOTAL_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_TOTAL_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $currencies, $payment, $point, $_POST, $cart;

      $total = @$order->info['total'];
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
        $total += isset($_POST['codt_fee']) ? intval($_POST['codt_fee']) : 0;
    }
      if ($payment == 'moneyorder') {
        $total += intval($_POST['money_order_fee']);
      }
      if ($payment == 'postalmoneyorder') {
        $total += intval($_POST['postal_money_order_fee']);
      }
      if ($payment == 'telecom') {
        $total += intval($_POST['telecom_order_fee']);
      }
      if (isset($cart)) {
      $bflag_single = $this->ds_count_bflags();
      if ($bflag_single == 'View') {
        $buy_table_fee = split("[:,]", MODULE_PAYMENT_BUYING_COST);
        $buying_fee = 0;
        for ($i = 0; $i < count($buy_table_fee); $i+=2) {
          if ($total <= $buy_table_fee[$i]) {
            $buy_add_fee = $total.$buy_table_fee[$i+1]; 
            @eval("\$buy_add_fee = $buy_add_fee;");
            if (is_numeric($buy_add_fee)) {
              $buying_fee = $buy_add_fee; 
            }
            break; 
          }
        }
        $total += $buying_fee; 
      }
    }
                              /*
                              'text' => '<b>' . $currencies->format_total(
                                $total, 
                                true, 
                                isset($order->info['currency'])?$order->info['currency']:'', 
                                isset($order->info['currency_value'])?$order->info['currency_value']:''
                                ) . '</b>',
                                */
      $this->output[] = array('title' => $this->title . ':',
                              'text' => '',
                              'value' => $total);
    }

    function check() {
      if (!isset($this->_check)) {
        // ccdd
        $check_query = tep_db_query("select configuration_value from " .  TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_TOTAL_STATUS' and site_id = '".$this->site_id."'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_TOTAL_STATUS', 'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER');
    }

    function install() {
      // ccdd
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('合計額の表示', 'MODULE_ORDER_TOTAL_TOTAL_STATUS', 'true', '合計額の表示をしますか?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now(), ".$this->site_id.")");
      // ccdd
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER', '6', '表示の整列順を設定できます. 数字が小さいほど上位に表示されます.', '6', '2', now(), ".$this->site_id.")");
    }

    function remove() {
      // ccdd
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    }
    
    function ds_count_bflags() {
      global $cart;
      $products = $cart->get_products();
      for ($i=0, $n=sizeof($products); $i<$n; $i++) {
        if ($products[$i]['bflag'] == '1') {
          return 'View'; 
        }
      }
      return false; 
    }
  }
?>
