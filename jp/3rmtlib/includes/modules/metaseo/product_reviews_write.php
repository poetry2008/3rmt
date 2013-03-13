<?php
  class product_reviews_write {
    var $site_id, $code, $title, $description;

// class constructor
/*-------------------------
 功能：构造函数
 参数：$site_id (string) SITE_ID值
 返回值：无
 ------------------------*/
    function product_reviews_write($site_id = 0) {
      global $order;

      $this->site_id = $site_id;
      $this->code = 'product_reviews_write';
      $this->title = MODULE_METASEO_PRODUCT_REVIEWS_WRITE_TEXT_TITLE;
      $this->description = MODULE_METASEO_PRODUCT_REVIEWS_WRITE_TEXT_DESCRIPTION;
    $this->explain = MODULE_METASEO_PRODUCT_REVIEWS_WRITE_TEXT_EXPLAIN;
      $this->sort_order = MODULE_METASEO_PRODUCT_REVIEWS_WRITE_SORT_ORDER;
      $this->link = 'product_reviews_write.php?products_id=31693';
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
 功能：检查前台编辑商品评论
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
      return array('title' => MODULE_METASEO_PRODUCT_REVIEWS_WRITE_TEXT_DESCRIPTION);
    }
/*-----------------------
 功能：商品评论编辑过程按钮 
 参数：无
 返回值：是否编辑成功(boolean)
 ----------------------*/
    function process_button() {
      return false;
    }
/*-----------------------
 功能：商品评论编辑过程
 参数：无
 返回值：是否编辑成功(boolean)
 ----------------------*/
    function before_process() {
      return false;
    }
/*----------------------
 功能：商品评论编辑之后的过程
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
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_METASEO_PRODUCT_REVIEWS_WRITE_TITLE' and site_id='".$this->site_id."'");
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
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added,site_id) values ('表示の整列順', 'MODULE_METASEO_PRODUCT_REVIEWS_WRITE_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0', now(), '".$this->site_id."')");

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title,
        configuration_key, configuration_value, configuration_description,
        configuration_group_id, sort_order, date_added,user_added, set_function,
        site_id) values ('タイトル',   'MODULE_METASEO_PRODUCT_REVIEWS_WRITE_TITLE',
          '',
          '商品のレビューを書くのタイトル<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#',
          '6', '0', now(),'".$_SESSION['user_name']."', NULL, '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('キーワード', 'MODULE_METASEO_PRODUCT_REVIEWS_WRITE_KEYWORDS',    '', '商品のレビューを書くのキーワード<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#', '6', '0', now(), \"tep_draw_textarea_field('configuration[MODULE_METASEO_PRODUCT_REVIEWS_WRITE_KEYWORDS]', false, 35, 5,\", '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('説明',       'MODULE_METASEO_PRODUCT_REVIEWS_WRITE_DESCRIPTION', '', '商品のレビューを書くの説明<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#',       '6', '0', now(), \"tep_draw_textarea_field('configuration[MODULE_METASEO_PRODUCT_REVIEWS_WRITE_DESCRIPTION]', false, 35, 5,\", '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('ロボット',   'MODULE_METASEO_PRODUCT_REVIEWS_WRITE_ROBOTS',      '', '商品のレビューを書くのロボット<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#',               '6', '0', now(), \"tep_cfg_select_option(array('index,follow', 'noindex'),\", '".$this->site_id."')");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, set_function, site_id) values ('著作者',     'MODULE_METASEO_PRODUCT_REVIEWS_WRITE_COPYRIGHT',   '', '商品のレビューを書くの著作者<br>#STORE_NAME#<br>#BREADCRUMB#<br>#PAGE_TITLE#<br>#BREADCRUMB_KEYWORD#<br>#BREADCRUMB_FIRST#',     '6', '0', now(), NULL, '".$this->site_id."')");
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
 功能：商品评论编辑
 参数：无
 返回值：编辑信息(string)
 -------------------*/
    function keys() {
      return array('MODULE_METASEO_PRODUCT_REVIEWS_WRITE_TITLE', 'MODULE_METASEO_PRODUCT_REVIEWS_WRITE_KEYWORDS', 'MODULE_METASEO_PRODUCT_REVIEWS_WRITE_DESCRIPTION', 'MODULE_METASEO_PRODUCT_REVIEWS_WRITE_ROBOTS', 'MODULE_METASEO_PRODUCT_REVIEWS_WRITE_COPYRIGHT', 'MODULE_METASEO_PRODUCT_REVIEWS_WRITE_SORT_ORDER');
    }
  }
?>
