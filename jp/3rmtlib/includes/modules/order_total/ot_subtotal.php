<?php
/*
  $Id$
*/

  class ot_subtotal {
    var $site_id, $title, $output;
/*--------------------------------
 功能：构造函数
 参数：$site_id (string) SITE_ID值
 返回值：无
 -------------------------------*/
    function ot_subtotal($site_id = 0) {

      $this->site_id = $site_id;

      $this->code = 'ot_subtotal';
      $this->title = MODULE_ORDER_TOTAL_SUBTOTAL_TITLE;
      $this->description = MODULE_ORDER_TOTAL_SUBTOTAL_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_SUBTOTAL_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER;

      $this->output = array();
    }
/*--------------------------------
 功能: 小计
 参数：无
 返回值：无
 -------------------------------*/

    function process() {
      global $order, $currencies;

      $this->output[] = array('title' => $this->title . ':',
                              'text'  => $currencies->format_total(
                                isset($order->info['subtotal'])?$order->info['subtotal']:'', 
                                true, 
                                isset($order->info['currency'])?$order->info['currency']:'', 
                                isset($order->info['currency_value'])?$order->info['currency_value']:''
                              ),
                              'value' => isset($order->info['subtotal']) ? $order->info['subtotal'] : '');
    }
/*-------------------------------
 功能：小计预处理
 参数：无
 返回值：无
 ------------------------------*/ 
    function pre_process() {
      global $currencies;

      $this->output[] = array('title' => $this->title . ':',
                              'text'  => '',
                              'value' => '');
    }
/*-------------------------------
 功能：检查小计
 参数：无
 返回值：检查小计SQL(string)
 ------------------------------*/
    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " .  TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_SUBTOTAL_STATUS' and site_id = '".$this->site_id."'");
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
      return array('MODULE_ORDER_TOTAL_SUBTOTAL_STATUS', 'MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER');
    }

/*-----------------------------
 功能：添加小计
 参数：无
 返回值：无
 ----------------------------*/
    function install() {

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title,
        configuration_key, configuration_value, configuration_description,
        configuration_group_id, sort_order, set_function, date_added,user_added,
        site_id) values ('小計の表示', 'MODULE_ORDER_TOTAL_SUBTOTAL_STATUS', 'true',
          '小計の表示をしますか?', '6', '1','tep_cfg_select_option(array(\'true\',
              \'false\'), ', now(),'".$_SESSION['user_name']."', ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_ORDER_TOTAL_SUBTOTAL_SORT_ORDER', '1', '表示の整列順を設定できます. 数字が小さいほど上位に表示されます.', '6', '2', now(), ".$this->site_id.")");
    }

/*----------------------------
 功能：删除小计SQL 
 参数：无
 返回值：无
 ---------------------------*/
    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    }
  }
?>
