<?php
  class change_preorder_success {
    var $site_id, $code, $title, $description;

// class constructor
    function change_preorder_success($site_id = 0) {
      global $order;

      $this->site_id = $site_id;
      $this->code = 'change_preorder_success';
      $this->title = MODULE_METASEO_CHANGE_PREORDER_SUCCESS_TEXT_TITLE;
      $this->description = MODULE_METASEO_CHANGE_PREORDER_SUCCESS_TEXT_DESCRIPTION;
      $this->explain = MODULE_METASEO_CHANGE_PREORDER_SUCCESS_TEXT_EXPLAIN;
      $this->sort_order = MODULE_METASEO_CHANGE_PREORDER_SUCCESS_SORT_ORDER;
      $this->link = 'change_preorder_success.php';
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
      return array('title' => MODULE_METASEO_CHANGE_PREORDER_SUCCESS_TEXT_DESCRIPTION);
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
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_METASEO_CHANGE_PREORDER_SUCCESS_TITLE' and site_id='".$this->site_id."'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added,site_id) values ('表示の整列順', 'MODULE_METASEO_CHANGE_PREORDER_SUCCESS_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), '".$this->site_id."')");

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('タイトル',   'MODULE_METASEO_CHANGE_PREORDER_SUCCESS_TITLE',       '', '予約注文の手続きが完了しましたのタイトル<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#',   '6', '0', now(), NULL, '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('キーワード', 'MODULE_METASEO_CHANGE_PREORDER_SUCCESS_KEYWORDS',    '', '予約注文の手続きが完了しましたのキーワード<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#', '6', '0', now(), \"tep_draw_textarea_field('configuration[MODULE_METASEO_CHANGE_PREORDER_SUCCESS_KEYWORDS]', false, 35, 5,\", '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('説明',       'MODULE_METASEO_CHANGE_PREORDER_SUCCESS_DESCRIPTION', '', '予約注文の手続きが完了しましたの説明<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#',       '6', '0', now(), \"tep_draw_textarea_field('configuration[MODULE_METASEO_CHANGE_PREORDER_SUCCESS_DESCRIPTION]', false, 35, 5,\", '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('ロボット',   'MODULE_METASEO_CHANGE_PREORDER_SUCCESS_ROBOTS',      '', '予約注文の手続きが完了しましたのロボット',               '6', '0', now(), \"tep_cfg_select_option(array('index,follow', 'noindex'),\", '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('著作者',     'MODULE_METASEO_CHANGE_PREORDER_SUCCESS_COPYRIGHT',   '', '予約注文の手続きが完了しましたの著作者',     '6', '0', now(), NULL, '".$this->site_id."')");
    }

    function remove() {
      //tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "', '".$this->site_id."')");
    }

    function keys() {
      return array('MODULE_METASEO_CHANGE_PREORDER_SUCCESS_TITLE', 'MODULE_METASEO_CHANGE_PREORDER_SUCCESS_KEYWORDS', 'MODULE_METASEO_CHANGE_PREORDER_SUCCESS_DESCRIPTION', 'MODULE_METASEO_CHANGE_PREORDER_SUCCESS_ROBOTS', 'MODULE_METASEO_CHANGE_PREORDER_SUCCESS_COPYRIGHT', 'MODULE_METASEO_CHANGE_PREORDER_SUCCESS_SORT_ORDER');
    }
  }
?>