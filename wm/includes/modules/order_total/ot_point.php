<?php
/*
  $Id: ot_point.php,v 1.4 2003/05/10 12:00:28 hawk Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/


  class ot_point {
    var $title, $output;
  
    function ot_point() {
      global $point;
	  
	  $this->code = 'ot_point';
      $this->title = MODULE_ORDER_TOTAL_POINT_TITLE;
      $this->description = MODULE_ORDER_TOTAL_POINT_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_POINT_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_POINT_SORT_ORDER;
	  $this->point = $point;

      $this->output = array();
    }

    function process() {
      global $order, $currencies, $point;

      if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {

          $this->output[] = array('title' => $this->title . ':',
                                  'text' => $currencies->format($point, true, $order->info['currency'], $order->info['currency_value']),
                                  'value' => $this->point);
      }
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_POINT_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      //return array('MODULE_ORDER_TOTAL_POINT_STATUS', 'MODULE_ORDER_TOTAL_POINT_SORT_ORDER', 'MODULE_ORDER_TOTAL_POINT_FEE', 'MODULE_ORDER_TOTAL_POINT_LIMIT', 'MODULE_ORDER_TOTAL_POINT_ADD_STATUS');
      return array('MODULE_ORDER_TOTAL_POINT_STATUS', 'MODULE_ORDER_TOTAL_POINT_SORT_ORDER', 'MODULE_ORDER_TOTAL_POINT_FEE', 'MODULE_ORDER_TOTAL_POINT_LIMIT', 'MODULE_ORDER_TOTAL_POINT_ADD_STATUS', 'MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL', 'MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK', 'MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN');
	}
	
    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('�ݥ���ȥ����ƥ�λ���', 'MODULE_ORDER_TOTAL_POINT_STATUS', 'true', '�ݥ���ȥ����ƥ����Ѥ��ޤ���?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('ɽ���������', 'MODULE_ORDER_TOTAL_POINT_SORT_ORDER', '4', 'ɽ��������������Ǥ��ޤ�. �������������ۤɾ�̤�ɽ������ޤ�.', '6', '2', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('�ݥ���ȴԸ�Ψ', 'MODULE_ORDER_TOTAL_POINT_FEE', '0.05', '�Ը�Ψ������򤷤ޤ���<br>�Ը�Ψ��5%�ξ���0.05��10%�ξ���0.1�פ����Ϥ��Ƥ���������', '6', '3', '', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('�ݥ���Ȥ�ͭ�����¤�����', 'MODULE_ORDER_TOTAL_POINT_LIMIT', '0', '�ݥ���Ȥ�ͭ�����¡������ˤ�����򤷤ޤ���<br>���ꤷ�ʤ����ϡ�0�פ����Ϥ��Ƥ���������', '6', '4', '', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('�ݥ���Ȥβû�����', 'MODULE_ORDER_TOTAL_POINT_ADD_STATUS', '0', '�ݥ���Ȥ�û����륹�ơ������������Ԥ��ޤ�<br>��׻��˲û�������ϡ֥ǥե���ȡפ����򡣥��ơ������������˲û�������ϡ��û����륹�ơ����������򤷤Ƥ�������', '6', '5', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    
      //�������ޡ���٥��Ѥ��ɲ� - 2005.11.17 - K.Kaneko
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('�������ޡ���٥�λ���', 'MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL', 'false', '�ݥ���Ȥη׻���ˡ�˥������ޡ���٥��Ŭ�Ѥ�Ԥ��ޤ���?', '6', '6','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('�������ޡ���٥�ˤ��Ը�Ψ������', 'MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK', '�֥��,0.05,20000||�������,0.1,50000||�ץ����,0.15,100000', '�������ޡ���٥��̤Υݥ���ȴԸ�Ψ������򤷤ޤ���<br>����޶��ڤ�Ǥ����ĤǤ���Ͽ�Ǥ��ޤ���<br>��˥��̾���֥�󥺡��ݥ������ͿΨ5�������20000�ߤξ�碪�֥֥��,0.05,20000��<br>��ʣ����Ͽ������ϡ�||�פǶ��ڤäƤ�������', '6', '7', '', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('���ν��״��֤�����', 'MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN', '365', '�������ޡ���٥����ꤹ������ۤν��״��֤����ꤷ�ޤ���<br>ñ�̤ϡ����פˤʤꡢ���״��֤�365���ξ���365�פ����Ϥ��Ƥ���������', '6', '8', '', now())");
	}

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>
