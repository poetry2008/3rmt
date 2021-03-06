<?php
/*
  $Id$
*/

  class ot_codt {
    var $site_id, $title, $output;

    function ot_codt($site_id = 0) {

      $this->site_id = $site_id;

      $this->code = 'ot_codt';
      $this->title       = MODULE_ORDER_TOTAL_CODT_TITLE;
      $this->description = MODULE_ORDER_TOTAL_CODT_DESCRIPTION;
      $this->enabled     = ((MODULE_ORDER_TOTAL_CODT_STATUS == 'true') ? true : false);
      $this->sort_order  = MODULE_ORDER_TOTAL_CODT_SORT_ORDER;

      $this->output = array();
    }
/*--------------------------------
 功能：代收货价邮件手续费
 参数：无
 返回值：无
 -------------------------------*/
    function process() {
      global $order, $currencies;
      global $payment;
      global $_POST;

      if (($payment == 'cod_table')
          && isset($_POST['codt_fee'])
          && (0 < intval($_POST['codt_fee']))) {
        $fee = intval($_POST['codt_fee']);
        $this->output[] = array('title' => $this->title . ':',
                                'text' => $currencies->format($fee, true, $order->info['currency'], $order->info['currency_value']),
                                'value' => $fee);
      }
    }
/*-------------------------------
 功能：检查代收货价邮件手续费 
 参数：无
 返回值：检查代收货价邮件手续费SQL(string)
 ------------------------------*/
    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " .  TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_CODT_STATUS' and site_id = '".$this->site_id."'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }
/*-------------------------------
 功能：配置关键字
 参数：无
 返回值：配置关键字值(string)
 ------------------------------*/
    function keys() {
      return array('MODULE_ORDER_TOTAL_CODT_STATUS',
        'MODULE_ORDER_TOTAL_CODT_SORT_ORDER');
    }
/*-----------------------------
 功能：添加代收货价手续费
 参数：无
 返回值：无
 ----------------------------*/
    function install() {

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title,
        configuration_key, configuration_value, configuration_description,
        configuration_group_id, sort_order, set_function, date_added,user_added,
        site_id) values ('代金引換払い手数料の表示',
          'MODULE_ORDER_TOTAL_CODT_STATUS', 'true',
          '代金引換払い手数料の表示をしますか?', '6',
          '1','tep_cfg_select_option(array(\'true\', \'false\'), ',
            now(),'".$_SESSION['user_name']."', ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_ORDER_TOTAL_CODT_SORT_ORDER', '5', '表示の整列順を設定できます. 数字が小さいほど上位に表示されます.', '6', '2', now(), ".$this->site_id.")");
    }
/*----------------------------
 功能：删除代收货价手续费SQL 
 参数：无
 返回值：无
 ---------------------------*/
    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    }
  }
?>
