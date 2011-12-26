<?php
 class ot_shipping {
    var $title, $output;

    function ot_shipping() {
      $this->code = 'ot_shipping';
      $this->title = MODULE_ORDER_TOTAL_SHIPPING_TITLE;
      $this->description = MODULE_ORDER_TOTAL_SHIPPING_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_SHIPPING_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $currencies,
      $shipping_method_info_arr;
      $shipping_customer_flag = false;
      if(count($shipping_method_info_arr) > 1 ){
        $shipping_customer_flag = true;
      }
      foreach($shipping_method_info_arr as $c_key => $shipping_method_info){
        $temp_arr  = array('title' => $this->title,
                           'text'  => $shipping_method_info['shipping_method'],
                           'value' => $shipping_method_info['shipping_cost']
                          );
        if($shipping_customer_flag){
          $temp_arr['shipping_pid'] = $c_key;
        }
        $this->output[] = $temp_arr;
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_SHIPPING_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER', 'MODULE_ORDER_TOTAL_SHIPPING_DESTINATION');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('送料の表示', 'MODULE_ORDER_TOTAL_SHIPPING_STATUS', 'true', '送料の表示をしますか?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('表示の整列順', 'MODULE_ORDER_TOTAL_SHIPPING_SORT_ORDER', '2', '表示の整列順を設定できます. 数字が小さいほど上位に表示されます.', '6', '2', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('送料無料設定', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING', 'false', '送料無料設定を有効にしますか?', '6', '3', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('送料を無料にする購入金額設定', 'MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER', '5000', '設定金額以上をご購入の場合は送料を無料にします.', '6', '4', 'currencies->format', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('送料無料を適用する地域の設定', 'MODULE_ORDER_TOTAL_SHIPPING_DESTINATION', 'national', '設定した地域に対して送料無料を適用します.', '6', '5', 'tep_cfg_select_option(array(\'national\', \'international\', \'both\'), ', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>
