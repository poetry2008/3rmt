<?php
/*
   $Id$
*/

  class postalmoneyorder {
    var $site_id, $code, $title, $description, $enabled;

// class constructor
    function postalmoneyorder($site_id = 0) {
      global $order;

      $this->site_id = $site_id;

      $this->code = 'postalmoneyorder';
      $this->title = MODULE_PAYMENT_POSTALMONEYORDER_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_POSTALMONEYORDER_TEXT_DESCRIPTION;
	  $this->explain = MODULE_PAYMENT_POSTALMONEYORDER_TEXT_EXPLAIN;
      $this->sort_order = MODULE_PAYMENT_POSTALMONEYORDER_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_POSTALMONEYORDER_STATUS == 'True') ? true : false);

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
        // ccdd
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

    function javascript_validation() {
      return false;
    }

    function selection() {
      return array('id' => $this->code,
                   'module' => $this->title,
				   'fields' => array(array('title' => $this->explain,'field' => '')));
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      return array('title' => MODULE_PAYMENT_POSTALMONEYORDER_TEXT_DESCRIPTION);
    }

    function process_button() {
      return false;
    }

    function before_process() {
      return false;
    }

    function after_process() {
      return false;
    }

    function get_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        // ccdd
        $check_query = tep_db_query("select configuration_value from " .  TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_POSTALMONEYORDER_STATUS' and site_id = '".$this->site_id."'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      // ccdd
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('ゆうちょ銀行（郵便局）を有効にする', 'MODULE_PAYMENT_POSTALMONEYORDER_STATUS', 'True', 'ゆうちょ銀行（郵便局）による支払いを受け付けますか?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), ".$this->site_id.");");
      // ccdd
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('お振込先:', 'MODULE_PAYMENT_POSTALMONEYORDER_PAYTO', '', 'お振込先名義を設定してください.', '6', '1', now(), ".$this->site_id.");");
      // ccdd
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_POSTALMONEYORDER_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), ".$this->site_id.")");
      // ccdd
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, site_id) values ('適用地域', 'MODULE_PAYMENT_POSTALMONEYORDER_ZONE', '0', '適用地域を選択すると、選択した地域のみで利用可能となります.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), ".$this->site_id.")");
      // ccdd
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_POSTALMONEYORDER_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
    }

    function remove() {
      // ccdd
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    }

    function keys() {
      return array('MODULE_PAYMENT_POSTALMONEYORDER_STATUS', 'MODULE_PAYMENT_POSTALMONEYORDER_ZONE', 'MODULE_PAYMENT_POSTALMONEYORDER_ORDER_STATUS_ID', 'MODULE_PAYMENT_POSTALMONEYORDER_SORT_ORDER', 'MODULE_PAYMENT_POSTALMONEYORDER_PAYTO');
    }
  }
?>
