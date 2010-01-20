<?php
  class product_notifications {
    var $code, $title, $description;

// class constructor
    function product_notifications() {
      global $order;

      $this->code = 'product_notifications';
      $this->title = MODULE_METASEO_PRODUCT_NOTIFICATIONS_TEXT_TITLE;
      $this->description = MODULE_METASEO_PRODUCT_NOTIFICATIONS_TEXT_DESCRIPTION;
	  $this->explain = MODULE_METASEO_PRODUCT_NOTIFICATIONS_TEXT_EXPLAIN;
      $this->sort_order = MODULE_METASEO_PRODUCT_NOTIFICATIONS_SORT_ORDER;
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
      return array('title' => MODULE_METASEO_PRODUCT_NOTIFICATIONS_TEXT_DESCRIPTION);
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
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_METASEO_PRODUCT_NOTIFICATIONS_TITLE'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('表示の整列順', 'MODULE_METASEO_PRODUCT_NOTIFICATIONS_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now())");

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) values ('タイトル',   'MODULE_METASEO_PRODUCT_NOTIFICATIONS_TITLE',       '', '商品のお知らせのタイトル<br>#STORE_NAME#<br>#BREADCRUMB#',   '6', '0', now(), NULL)");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) values ('キーワード', 'MODULE_METASEO_PRODUCT_NOTIFICATIONS_KEYWORDS',    '', '商品のお知らせのキーワード<br>#STORE_NAME#<br>#BREADCRUMB#', '6', '0', now(), \"tep_draw_textarea_field('configuration[MODULE_METASEO_PRODUCT_NOTIFICATIONS_KEYWORDS]', false, 35, 5,\")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) values ('説明',       'MODULE_METASEO_PRODUCT_NOTIFICATIONS_DESCRIPTION', '', '商品のお知らせの説明<br>#STORE_NAME#<br>#BREADCRUMB#',       '6', '0', now(), \"tep_draw_textarea_field('configuration[MODULE_METASEO_PRODUCT_NOTIFICATIONS_DESCRIPTION]', false, 35, 5,\")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) values ('ロボット',   'MODULE_METASEO_PRODUCT_NOTIFICATIONS_ROBOTS',      '', '商品のお知らせのロボット',               '6', '0', now(), \"tep_cfg_select_option(array('index,follow', 'noindex'),\")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function) values ('著作者',     'MODULE_METASEO_PRODUCT_NOTIFICATIONS_COPYRIGHT',   '', '商品のお知らせの著作者',     '6', '0', now(), NULL)");
    }

    function remove() {
      //tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_METASEO_PRODUCT_NOTIFICATIONS_TITLE', 'MODULE_METASEO_PRODUCT_NOTIFICATIONS_KEYWORDS', 'MODULE_METASEO_PRODUCT_NOTIFICATIONS_DESCRIPTION', 'MODULE_METASEO_PRODUCT_NOTIFICATIONS_ROBOTS', 'MODULE_METASEO_PRODUCT_NOTIFICATIONS_COPYRIGHT', 'MODULE_METASEO_PRODUCT_NOTIFICATIONS_SORT_ORDER');
    }
  }
?>
