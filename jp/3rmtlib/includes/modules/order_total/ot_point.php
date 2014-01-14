<?php
/*
  $Id$
*/


  class ot_point {
    var $site_id, $title, $output;
/*-------------------------------
 功能：构造函数
 参数：$site_id(string) SITE_ID值
 返回值：无
 ------------------------------*/  
    function ot_point($site_id = 0) {
      global $point;

      $this->site_id = $site_id;

	  
	  $this->code = 'ot_point';
      if(strstr($_SESSION['campaign_fee'],'-')){
        $this->title = MODULE_ORDER_TOTAL_POINT_TITLE;
      }else{
        $this->title = MODULE_ORDER_TOTAL_COST_TITLE;
      }
      $this->description = MODULE_ORDER_TOTAL_POINT_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_POINT_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_POINT_SORT_ORDER;
      $this->point = $point;

      $this->output = array();
    }
/*--------------------------------
 功能: 折扣
 参数：无
 返回值：无
 -------------------------------*/
    function process() {
      global $order, $currencies, $point;

      if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {

          $this->output[] = array('title' => $this->title . ':',
                                  'text' => $currencies->format(
                                    $point, 
                                    true, 
                                    isset($order->info['currency'])?$order->info['currency']:'', 
                                    isset($order->info['currency_value'])?$order->info['currency_value']:''
                                  ),
                                  'value' => $point);
      }
    }
/*---------------------------
 功能：判断订单总额点状态 
 参数：无
 返回值：无
 --------------------------*/    
    function pre_process() {
      global $currencies;

      if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {

          $this->output[] = array('title' => $this->title . ':',
                                  'text' => '',
                                  'value' => '');
      }
    }
/*-------------------------------
 功能：检查折扣
 参数：无
 返回值：检查折扣SQL(string)
 ------------------------------*/
    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " .  TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_POINT_STATUS' and site_id = '".$this->site_id."'");
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
      return array('MODULE_ORDER_TOTAL_POINT_STATUS', 'MODULE_ORDER_TOTAL_POINT_SORT_ORDER', 'MODULE_ORDER_TOTAL_POINT_FEE', 'MODULE_ORDER_TOTAL_POINT_LIMIT', 'MODULE_ORDER_TOTAL_POINT_ADD_STATUS', 'MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL', 'MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK', 'MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN');
	}
	
/*-----------------------------
 功能：添加折扣
 参数：无
 返回值：无
 ----------------------------*/
    function install() {

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title,
        configuration_key, configuration_value, configuration_description,
        configuration_group_id, sort_order, set_function, date_added,user_added,
        site_id) values ('ポイントシステムの使用',
          'MODULE_ORDER_TOTAL_POINT_STATUS', 'true',
          'ポイントシステムを使用しますか?', '6',
          '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now(),
            '".$_SESSION['user_name']."',".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_ORDER_TOTAL_POINT_SORT_ORDER', '4', '表示の整列順を設定できます. 数字が小さいほど上位に表示されます.', '6', '2', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added, site_id) values ('ポイント還元率', 'MODULE_ORDER_TOTAL_POINT_FEE', '0.05', '還元率の設定をします。<br>還元率は5%の場合「0.05」10%の場合「0.1」と入力してください。', '6', '3', '', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added, site_id) values ('ポイントの有効期限の設定', 'MODULE_ORDER_TOTAL_POINT_LIMIT', '0', 'ポイントの有効期限（日数）の設定をします。<br>設定しない場合は「0」を入力してください。',
               '6', '4', '', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('ポイントの加算設定', 'MODULE_ORDER_TOTAL_POINT_ADD_STATUS', '0', 'ポイントを加算するステータスの設定を行います<br>会計時に加算する場合は「デフォルト」を選択。ステータス更新時に加算する場合は、加算するステータスを選択してください', '6', '5', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
    
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('カスタマーレベルの使用', 'MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL', 'false', 'ポイントの計算方法にカスタマーレベルの適用を行いますか?', '6', '6','tep_cfg_select_option(array(\'true\', \'false\'), ', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added, site_id) values ('カスタマーレベルによる還元率の設定', 'MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK', 'ブロンズ,0.05,20000||ゴールド,0.1,50000||プラチナ,0.15,100000', 'カスタマーレベル別のポイント還元率の設定をします。<br>カンマ区切りでいくつでも登録できます。<br>例）ランク名：ブロンズ、ポイント付与率5％、売上合計20000円の場合→「ブロンズ,0.05,20000」<br>※複数登録する場合は「||」で区切ってください', '6', '7', '', now(), ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added, site_id) values ('売上の集計期間の設定', 'MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN', '365', 'カスタマーレベルを決定する売上金額の集計期間を設定します。<br>単位は「日」になり、集計期間が365日の場合「365」と入力してください。', '6', '8', '', now(), ".$this->site_id.")");
	}
/*----------------------------
 功能：删除折扣SQL 
 参数：无
 返回值：无
 ---------------------------*/
    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key
          in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    }
  }
?>
