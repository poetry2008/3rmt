<?php
  class popup_search_help {
    var $code, $title, $description;

// class constructor
    function popup_search_help() {
      global $order;

      $this->code = 'popup_search_help';
      $this->title = MODULE_METASEO_POPUP_SEARCH_HELP_TEXT_TITLE;
      $this->description = MODULE_METASEO_POPUP_SEARCH_HELP_TEXT_DESCRIPTION;
	  $this->explain = MODULE_METASEO_POPUP_SEARCH_HELP_TEXT_EXPLAIN;
      $this->sort_order = MODULE_METASEO_POPUP_SEARCH_HELP_SORT_ORDER;
    }

// class methods
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
      return array('title' => MODULE_METASEO_POPUP_SEARCH_HELP_TEXT_DESCRIPTION);
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
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_METASEO_POPUP_SEARCH_HELP_TITLE'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('ɽ���������', 'MODULE_METASEO_POPUP_SEARCH_HELP_SORT_ORDER', '0', 'ɽ��������������Ǥ��ޤ����������������ۤɾ�̤�ɽ������ޤ�.', '6', '0', now())");

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) values ('�����ȥ�',   'MODULE_METASEO_POPUP_SEARCH_HELP_TITLE',       '', '�ܺٸ����λȤ����Υ����ȥ�<br>#STORE_NAME#<br>#BREADCRUMB#',   '6', '0', now(), NULL)");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) values ('�������', 'MODULE_METASEO_POPUP_SEARCH_HELP_KEYWORDS',    '', '�ܺٸ����λȤ����Υ������<br>#STORE_NAME#<br>#BREADCRUMB#', '6', '0', now(), \"tep_draw_textarea_field('configuration[MODULE_METASEO_POPUP_SEARCH_HELP_KEYWORDS]', false, 35, 5,\")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) values ('����',       'MODULE_METASEO_POPUP_SEARCH_HELP_DESCRIPTION', '', '�ܺٸ����λȤ���������<br>#STORE_NAME#<br>#BREADCRUMB#',       '6', '0', now(), \"tep_draw_textarea_field('configuration[MODULE_METASEO_POPUP_SEARCH_HELP_DESCRIPTION]', false, 35, 5,\")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) values ('��ܥå�',   'MODULE_METASEO_POPUP_SEARCH_HELP_ROBOTS',      '', '�ܺٸ����λȤ����Υ�ܥå�',               '6', '0', now(), \"tep_cfg_select_option(array('index,follow', 'noindex'),\")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) values ('�����',     'MODULE_METASEO_POPUP_SEARCH_HELP_COPYRIGHT',   '', '�ܺٸ����λȤ����������',     '6', '0', now(), NULL)");
    }

    function remove() {
      //tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_METASEO_POPUP_SEARCH_HELP_TITLE', 'MODULE_METASEO_POPUP_SEARCH_HELP_KEYWORDS', 'MODULE_METASEO_POPUP_SEARCH_HELP_DESCRIPTION', 'MODULE_METASEO_POPUP_SEARCH_HELP_ROBOTS', 'MODULE_METASEO_POPUP_SEARCH_HELP_COPYRIGHT', 'MODULE_METASEO_POPUP_SEARCH_HELP_SORT_ORDER');
    }
  }
?>
