<?php
  class logoff {
    var $site_id, $code, $title, $description;

// class constructor
/*-------------------------
 功能：构造函数
 参数：$site_id (string) site_id值
 返回值：无
 ------------------------*/
    function logoff($site_id = 0) {
      global $order;

      $this->site_id = $site_id;
      $this->code = 'logoff';
      $this->title = MODULE_METASEO_LOGOFF_TEXT_TITLE;
      $this->description = MODULE_METASEO_LOGOFF_TEXT_DESCRIPTION;
      $this->explain = MODULE_METASEO_LOGOFF_TEXT_EXPLAIN;
      $this->sort_order = MODULE_METASEO_LOGOFF_SORT_ORDER;
      $this->link = 'logoff.php';
    }

// class methods
/*-------------------------
 功能：javascript验证 
 参数：无
 返回值：false(boolean)
 ------------------------*/
    function javascript_validation() {
      return false;
    }
/*------------------------
 功能：选项 
 参数：无
 返回值：无
 -----------------------*/
    function selection() {
      return array('id' => $this->code,
                   'module' => $this->title,
           'fields' => array(array('title' => $this->explain,'field' => '')));
    }
/*------------------------
 功能：检查前台是否退出登陆
 参数：无
 返回值：无
 -----------------------*/
    function pre_confirmation_check() {
      return false;
    }
/*------------------------
 功能：文本描述 
 参数：无
 返回值：标题描述(string)
 -----------------------*/
    function confirmation() {
      return array('title' => MODULE_METASEO_LOGOFF_TEXT_DESCRIPTION);
    }
/*-----------------------
 功能：退出登录编辑过程按钮 
 参数：无
 返回值：是否编辑成功(boolean)
 ----------------------*/
    function process_button() {
      return false;
    }
/*-----------------------
 功能：退出登录编辑过程
 参数：无
 返回值：是否编辑成功(boolean)
 ----------------------*/
    function before_process() {
      return false;
    }
/*----------------------
 功能：退出登录编辑之后的过程
 参数：无
 返回值：是否编辑成功(boolean)
 ---------------------*/
    function after_process() {
      return false;
    }
/*---------------------
 功能：获取错误
 参数：无
 返回值：是否获取错误(boolean)
 --------------------*/
    function get_error() {
      return false;
    }
/*--------------------
 功能：检查SQL数据
 参数：无
 返回值：SQL数据(string) 
 -------------------*/
    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_METASEO_LOGOFF_TITLE' and site_id='".$this->site_id."'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }
/*--------------------
 功能：添加SQL数据模块
 参数：无
 返回值：无
 -------------------*/

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added,site_id) values ('表示の整列順', 'MODULE_METASEO_LOGOFF_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), '".$this->site_id."')");

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title,
        configuration_key, configuration_value, configuration_description,
        configuration_group_id, sort_order, date_added,user_added, set_function,
        site_id) values ('タイトル',   'MODULE_METASEO_LOGOFF_TITLE',       '',
          'ログアウトのタイトル<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#',
          '6', '0', now(),'".$_SESSION['user_name']."', NULL, '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('キーワード', 'MODULE_METASEO_LOGOFF_KEYWORDS',    '', 'ログアウトのキーワード<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#', '6', '0', now(), \"tep_draw_textarea_field('configuration[MODULE_METASEO_LOGOFF_KEYWORDS]', false, 35, 5,\", '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('説明',       'MODULE_METASEO_LOGOFF_DESCRIPTION', '', 'ログアウトの説明<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#',       '6', '0', now(), \"tep_draw_textarea_field('configuration[MODULE_METASEO_LOGOFF_DESCRIPTION]', false, 35, 5,\", '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('ロボット',   'MODULE_METASEO_LOGOFF_ROBOTS',      '', 'ログアウトのロボット',               '6', '0', now(), \"tep_cfg_select_option(array('index,follow', 'noindex'),\", '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('著作者',     'MODULE_METASEO_LOGOFF_COPYRIGHT',   '', 'ログアウトの著作者',     '6', '0', now(), NULL, '".$this->site_id."')");
    }

/*---------------------
 功能：删除SQL数据 
 参数：无
 返回值：无
 --------------------*/
    function remove() {
      //tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "', '".$this->site_id."')");
    }
/*--------------------
 功能：退出登录编辑
 参数：无
 返回值：编辑信息(string)
 -------------------*/
    function keys() {
      return array('MODULE_METASEO_LOGOFF_TITLE', 'MODULE_METASEO_LOGOFF_KEYWORDS', 'MODULE_METASEO_LOGOFF_DESCRIPTION', 'MODULE_METASEO_LOGOFF_ROBOTS', 'MODULE_METASEO_LOGOFF_COPYRIGHT', 'MODULE_METASEO_LOGOFF_SORT_ORDER');
    }
  }
?>
