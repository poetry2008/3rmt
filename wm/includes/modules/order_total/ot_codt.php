<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class ot_codt {
    var $title, $output;

    function ot_codt() {
      $this->code = 'ot_codt';
      $this->title       = MODULE_ORDER_TOTAL_CODT_TITLE;
      $this->description = MODULE_ORDER_TOTAL_CODT_DESCRIPTION;
      $this->enabled     = ((MODULE_ORDER_TOTAL_CODT_STATUS == 'true') ? true : false);
      $this->sort_order  = MODULE_ORDER_TOTAL_CODT_SORT_ORDER;

      $this->output = array();
    }

    function process() {
      global $order, $currencies;
      global $payment;
      global $HTTP_POST_VARS;

      if (($payment == 'cod_table')
          && isset($HTTP_POST_VARS['codt_fee'])
          && (0 < intval($HTTP_POST_VARS['codt_fee']))) {
        $fee = intval($HTTP_POST_VARS['codt_fee']);
        $this->output[] = array('title' => $this->title . ':',
                                'text' => $currencies->format($fee, true, $order->info['currency'], $order->info['currency_value']),
                                'value' => $fee);
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " .  TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_CODT_STATUS' and site_id = '".SITE_ID."'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_ORDER_TOTAL_CODT_STATUS',
        'MODULE_ORDER_TOTAL_CODT_SORT_ORDER');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('代金引換払い手数料の表示', 'MODULE_ORDER_TOTAL_CODT_STATUS', 'true', '代金引換払い手数料の表示をしますか?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('表示の整列順', 'MODULE_ORDER_TOTAL_CODT_SORT_ORDER', '5', '表示の整列順を設定できます. 数字が小さいほど上位に表示されます.', '6', '2', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".SITE_ID."'");
    }
  }
?>
