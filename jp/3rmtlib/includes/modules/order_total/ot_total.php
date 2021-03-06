<?php
/*
  $Id$
*/

  class ot_total {
    var $site_id, $title, $output;
/*--------------------------------
 功能：构造函数
 参数：$site_id (string) SITE_ID值
 返回值：无
 -------------------------------*/
    function ot_total($site_id = 0) {
      $this->site_id = $site_id;
      $this->code = 'ot_total';
      $this->title = MODULE_ORDER_TOTAL_TOTAL_TITLE;
      $this->description = MODULE_ORDER_TOTAL_TOTAL_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_TOTAL_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER;
      $this->output = array();
    }
/*--------------------------------
 功能: 合计
 参数：无
 返回值：无
 -------------------------------*/

    function process() {
      global $order, $currencies, $payment, $point, $_POST, $cart;
      $total = @$order->info['total'];
      if ((MODULE_ORDER_TOTAL_CODT_STATUS == 'true')
          && isset($_SESSION['h_code_fee'])
          && (0 < intval($_SESSION['h_code_fee']))) {
        $total += intval($_SESSION['h_code_fee']);
      }
    
    //Add point
      if ((MODULE_ORDER_TOTAL_POINT_STATUS == 'true')
          && (0 < intval($point))) {
        $total -= intval($point);
      }   
    
      if (isset($cart)) {
      $bflag_single = $this->ds_count_bflags();
      if ($bflag_single == 'View') {
        $buy_table_fee = split("[:,]", MODULE_PAYMENT_BUYING_COST);
        $buying_fee = 0;
        for ($i = 0; $i < count($buy_table_fee); $i+=2) {
          if ($total <= $buy_table_fee[$i]) {
            $buy_add_fee = $total.$buy_table_fee[$i+1]; 
            @eval("\$buy_add_fee = $buy_add_fee;");
            if (is_numeric($buy_add_fee)) {
              $buying_fee = $buy_add_fee; 
            }
            break; 
          }
        }
        $total += $buying_fee; 
      }
    }
      if($_SESSION['c_point']){
         $campaign_query = tep_db_query("select * from " .  TABLE_CAMPAIGN . " where keyword = '".$_SESSION['c_point']."'");
         $campaign_row   = tep_db_fetch_array($campaign_query);
         if($campaign_row['range_type'] == 2){
           if($total <= $campaign_row['limit_value']){
             if(isset($_SESSION['campaign_fee'])){
               if((!strstr($campaign_row['point_value'],'-')) && $campaign_row['type'] == 2){
                  $total += abs($_SESSION['campaign_fee']);
               }else{
                  $total += $_SESSION['campaign_fee'];
               }
             }
           }
         }else if($campaign_row['range_type'] == 1){
           if($total >= $campaign_row['limit_value']){
             if(isset($_SESSION['campaign_fee'])){
               if((!strstr($campaign_row['point_value'],'-')) && $campaign_row['type'] == 2){
                  $total += abs($_SESSION['campaign_fee']);
               }else{
                  $total += $_SESSION['campaign_fee'];
               }
             }
           }
         } 
      }else if (isset($_SESSION['campaign_fee'])) {
         $campaign_query = tep_db_query("select * from " .  TABLE_CAMPAIGN . " where keyword = '".$_SESSION['hc_camp_point']."'");
         $campaign_row   = tep_db_fetch_array($campaign_query);
            if(strstr($campaign_row['point_value'],'-')){
              $total += $_SESSION['campaign_fee']; 
            }else{
              $total -= $_SESSION['campaign_fee']; 
            }
      }
      if(isset($_SESSION['h_shipping_fee'])){
        $total += $_SESSION['h_shipping_fee'];  
      }
      $this->output[] = array('title' => $this->title . ':',
                              'text' => '',
                              'value' => $total);
    }
/*------------------------
 功能：合计预处理 
 参数：无
 返回值：无
 ------------------------*/    
    function pre_process() {
      $this->output[] = array('title' => $this->title . ':',
                              'text' => '',
                              'value' => '');
    }
 /*-------------------------------
 功能：检查合计
 参数：无
 返回值：检查合计SQL(string)
 ------------------------------*/
    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " .  TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_TOTAL_STATUS' and site_id = '".$this->site_id."'");
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
      return array('MODULE_ORDER_TOTAL_TOTAL_STATUS', 'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER');
    }

/*-----------------------------
 功能：添加合计
 参数：无
 返回值：无
 ----------------------------*/
    function install() {

      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title,
        configuration_key, configuration_value, configuration_description,
        configuration_group_id, sort_order, set_function, date_added,user_added,
        site_id) values ('合計額の表示', 'MODULE_ORDER_TOTAL_TOTAL_STATUS', 'true',
          '合計額の表示をしますか?', '6', '1','tep_cfg_select_option(array(\'true\',
              \'false\'), ', now(),'".$_SESSION['user_name']."', ".$this->site_id.")");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER', '6', '表示の整列順を設定できます. 数字が小さいほど上位に表示されます.', '6', '2', now(), ".$this->site_id.")");
    }

/*----------------------------
 功能：删除合计SQL 
 参数：无
 返回值：无
 ---------------------------*/
    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
    }
/*----------------------------
 功能：订单总额标志
 参数：无
 返回值：判断是否返回View(string)
 ----------------------------*/
   function ds_count_bflags() {
      global $cart;
      $products = $cart->get_products();
      for ($i=0, $n=sizeof($products); $i<$n; $i++) {
        if ($products[$i]['bflag'] == '1') {
          return 'View'; 
        }
      }
      return false; 
    }
  }
?>
