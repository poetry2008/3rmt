<?php
/*
  $Id: buying.php,v 1.4 2003/03/07 05:27:32 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class buying {
    var $code, $title, $description, $enabled;

// class constructor
    function buying() {
      global $order;

      $this->code = 'buying';
      $this->title = MODULE_PAYMENT_BUYING_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_BUYING_TEXT_DESCRIPTION;
	  $this->explain = MODULE_PAYMENT_BUYING_TEXT_EXPLAIN;
      $this->sort_order = MODULE_PAYMENT_BUYING_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_BUYING_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_BUYING_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_BUYING_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();
    
      $this->email_footer = MODULE_PAYMENT_BUYING_TEXT_EMAIL_FOOTER;
    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_BUYING_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_BUYING_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
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
                   'module' => '',
				   'fields' => '');
    }

    function pre_confirmation_check() {
      return false;
    }

    function confirmation() {
      return false;
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
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_BUYING_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('�㤤����ͭ���ˤ���', 'MODULE_PAYMENT_BUYING_STATUS', 'True', '��Կ����ˤ���ʧ��������դ��ޤ���?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now());");
      //tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('��������:', 'MODULE_PAYMENT_BUYING_PAYTO', '', '��������̾�������ꤷ�Ƥ�������.', '6', '1', now());");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('ɽ���������', 'MODULE_PAYMENT_BUYING_SORT_ORDER', '0', 'ɽ��������������Ǥ��ޤ����������������ۤɾ�̤�ɽ������ޤ�.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Ŭ���ϰ�', 'MODULE_PAYMENT_BUYING_ZONE', '0', 'Ŭ���ϰ�����򤹤�ȡ����򤷤��ϰ�Τߤ����Ѳ�ǽ�Ȥʤ�ޤ�.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('�����ʸ���ơ�����', 'MODULE_PAYMENT_BUYING_ORDER_STATUS_ID', '0', '���ꤷ�����ơ��������������Ŭ�Ѥ���ޤ�.', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_BUYING_STATUS', 'MODULE_PAYMENT_BUYING_ZONE', 'MODULE_PAYMENT_BUYING_ORDER_STATUS_ID', 'MODULE_PAYMENT_BUYING_SORT_ORDER');
    }
  }
?>
