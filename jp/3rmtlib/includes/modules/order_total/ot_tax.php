<?php
/*
  $Id$
*/

  class ot_tax {
    var $site_id, $title, $output;
/*--------------------------------
 功能：构造函数
 参数：$site_id (string) SITE_ID值
 返回值：无
 -------------------------------*/
    function ot_tax($site_id = 0) {

      $this->site_id = $site_id;

      $this->code = 'ot_tax';
      $this->title = MODULE_ORDER_TOTAL_TAX_TITLE;
      $this->description = MODULE_ORDER_TOTAL_TAX_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_TAX_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_TAX_SORT_ORDER;

      $this->output = array();
    }
/*--------------------------------
 功能: 消费税
 参数：无
 返回值：无
 -------------------------------*/

    function process() {
      global $order, $currencies;

      if(isset($order->info['tax_groups'])){
        reset($order->info['tax_groups']);
        while (list($key, $value) = each($order->info['tax_groups'])) {
          if ($value > 0) {
            $this->output[] = array('title' => $key . ':',
                                    'text' => $currencies->format($value, true, $order->info['currency'], $order->info['currency_value']),
                                    'value' => $value);
          }
        }
      }
    }
/*-------------------------------
 功能：检查消费税
 参数：无
 返回值：检查消费税SQL(string)
 ------------------------------*/
    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " .  TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_TAX_STATUS' and site_id = '".$this->site_id."'");
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
      return array('MODULE_ORDER_TOTAL_TAX_STATUS', 'MODULE_ORDER_TOTAL_TAX_SORT_ORDER');
    }

/*-----------------------------
 功能：添加消费税
 参数：无
 返回值：无
 ----------------------------*/
    function install() {

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title,
        configuration_key, configuration_value, configuration_description,
        configuration_group_id, sort_order, set_function, date_added,user_added,
        site_id) values ('消費税の表示', 'MODULE_ORDER_TOTAL_TAX_STATUS', 'true',
          '消費税の表示をしますか?', '6', '1','tep_cfg_select_option(array(\'true\',
              \'false\'), ', now(),'".$_SESSION['user_name']."', ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_ORDER_TOTAL_TAX_SORT_ORDER', '3', '表示の整列順を設定できます. 数字が小さいほど上位に表示されます.', '6', '2', now(), ".$this->site_id.")");
    }

/*----------------------------
 功能：删除消费税SQL 
 参数：无
 返回值：无
 ---------------------------*/
    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    }
  }
?>
