<?php
  class products_new {
    var $site_id, $code, $title, $description;

// class constructor
    function products_new($site_id = 0) {
      global $order;

      $this->site_id = $site_id;
      $this->code = 'products_new';
      $this->title = MODULE_METASEO_PRODUCTS_NEW_TEXT_TITLE;
      $this->description = MODULE_METASEO_PRODUCTS_NEW_TEXT_DESCRIPTION;
    $this->explain = MODULE_METASEO_PRODUCTS_NEW_TEXT_EXPLAIN;
      $this->sort_order = MODULE_METASEO_PRODUCTS_NEW_SORT_ORDER;
      $this->link = 'products_new.php';
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
      return array('title' => MODULE_METASEO_PRODUCTS_NEW_TEXT_DESCRIPTION);
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
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_METASEO_PRODUCTS_NEW_TITLE' and site_id='".$this->site_id."'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added,site_id) values ('表示の整列順', 'MODULE_METASEO_PRODUCTS_NEW_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), '".$this->site_id."')");

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('タイトル',   'MODULE_METASEO_PRODUCTS_NEW_TITLE',       '', '新着商品のタイトル<br>#SEO_PAGE#',   '6', '0', now(), NULL, '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('キーワード', 'MODULE_METASEO_PRODUCTS_NEW_KEYWORDS',    '', '新着商品のキーワード<br>#SEO_PAGE#', '6', '0', now(), \"tep_draw_textarea_field('configuration[MODULE_METASEO_PRODUCTS_NEW_KEYWORDS]', false, 35, 5,\", '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('説明',       'MODULE_METASEO_PRODUCTS_NEW_DESCRIPTION', '', '新着商品の説明<br>#SEO_PAGE#',       '6', '0', now(), \"tep_draw_textarea_field('configuration[MODULE_METASEO_PRODUCTS_NEW_DESCRIPTION]', false, 35, 5,\", '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('ロボット',   'MODULE_METASEO_PRODUCTS_NEW_ROBOTS',      '', '新着商品のロボット',               '6', '0', now(), \"tep_cfg_select_option(array('index,follow', 'noindex'),\", '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('著作者',     'MODULE_METASEO_PRODUCTS_NEW_COPYRIGHT',   '', '新着商品の著作者',     '6', '0', now(), NULL, '".$this->site_id."')");
    }

    function remove() {
      //tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "', '".$this->site_id."')");
    }

    function keys() {
      return array('MODULE_METASEO_PRODUCTS_NEW_TITLE', 'MODULE_METASEO_PRODUCTS_NEW_KEYWORDS', 'MODULE_METASEO_PRODUCTS_NEW_DESCRIPTION', 'MODULE_METASEO_PRODUCTS_NEW_ROBOTS', 'MODULE_METASEO_PRODUCTS_NEW_COPYRIGHT', 'MODULE_METASEO_PRODUCTS_NEW_SORT_ORDER');
    }
  }
?>
