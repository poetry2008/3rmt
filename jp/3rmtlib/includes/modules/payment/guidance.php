<?php
/*
   $Id$
 */

// 货到付款（手续费与购买价格连动）
require_once (DIR_WS_CLASSES . 'basePayment.php');
class guidance extends basePayment  implements paymentInterface  {
  var $site_id, $code, $title, $description, $enabled, $n_fee, $s_error, $email_footer,$c_prefix, $show_payment_info;
 /*------------------------------
 功能：构造函数
 参数：$site_id (string) SITE_ID值
 返回值：无
 -----------------------------*/
  function loadSpecialSettings($site_id = 0)
  {
    $this->site_id = $site_id;
    $this->code        = 'guidance';
    $this->show_payment_info = 0;
  }


  // class methods
/*----------------------------
 功能：检查标志
 参数：无
 返回值：判断是否检查成功(string) 
 ---------------------------*/
  function update_status() {
    global $order;
    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_GUIDANCE_ZONE > 0) ) {
      $check_flag = false;
      $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_GUIDANCE_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
      while ($check = tep_db_fetch_array($check_query)) {
        if ($check['zone_id'] < 1) {
          $check_flag = true;
          break;
        } elseif ($check['zone_id'] == $order->billing['zone_id']) {
          $check_flag = true;
          break;
        }
      }

      if ($check_flag == false) {
        $this->enabled = false;
      }
    }
  }



  // class methods
/*-----------------------------
 功能：JS验证
 参数：无
 返回值：JS验证是否成功(boolean)
 ----------------------------*/
  function javascript_validation() {
    return false;
  }
/*----------------------------
 功能：编辑webmoney及game之间的移动
 参数：$theData(boolean) 数据
 参数：$back(boolean) true/false
 返回值：无
 ---------------------------*/
 
  function fields($theData=false, $back=false){
  }
/*--------------------------
 功能：确认检查前台支付方法 
 参数：无
 返回值：是否检查成功(boolean)
 -------------------------*/
  function pre_confirmation_check() {
    return false;
  }
/*--------------------------
 功能：确认检查预约 
 参数：无
 返回值：1(string) 
 --------------------------*/  
  function preorder_confirmation_check() {
    return 1; 
  }
/*----------------------------
 功能：确认webmoney及game之间的移动
 参数：无
 返回值：无
 ----------------------------*/  
  function confirmation() {
    return ''; 
  }
 /*----------------------
   功能：webmoney及game之间的移动过程按钮
   参数：无
   返回值：无
   ---------------------*/
  function process_button() {
    return ''; 
  }
/*-----------------------
 功能：webmoney及game之间的移动前
 参数：无
 返回值：无
 ----------------------*/
  function before_process() {
  }
/*-------------------------
 功能：webmoney及game之间的移动后 
 参数：无
 返回值：无
 -------------------------*/
  function after_process() {
    return false;
  }
/*-------------------------
 功能：获取错误
 参数：无
 返回值：错误信息(array)
 ------------------------*/
  function get_error() {
    return array('title' => $this->title.'エラー!',
        'error' => TS_MODULE_PAYMENT_GUIDANCE_ERROR); 
  }
/*------------------------
 功能：获取预约错误信息 
 参数：$error_type(string) 错误类型
 返回值：预约错误信息(string) 
 -----------------------*/  
  function get_preorder_error($error_type) {
    return TS_MODULE_PAYMENT_GUIDANCE_ERROR; 
  }
/*---------------------------
 功能：检查SQL
 参数：无
 返回值：SQL(string)
 --------------------------*/  
  function check() {
    if (!isset($this->_check)) {
      $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_GUIDANCE_STATUS' and site_id = '".$this->site_id."'");
      $this->_check = tep_db_num_rows($check_query);
    }
    return $this->_check;
  }
/*--------------------------------
 功能：添加支付方法的SQL
 参数：无
 返回值：无
 -------------------------------*/
  function install() {
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title,
      configuration_key, configuration_value, configuration_description,
      configuration_group_id, sort_order, set_function, date_added,user_added,
      site_id) values ('ウェブマネー及びゲーム間移動を有効にする',
        'MODULE_PAYMENT_GUIDANCE_STATUS', 'True',
        '楽天銀行による支払いを受け付けますか?', '6', '1',
        'tep_cfg_select_option(array(\'True\', \'False\'),
          ',now(),'".$_SESSION['user_name']."', ".$this->site_id.");");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済手数料', 'MODULE_PAYMENT_GUIDANCE_COST', '99999999999:*0', '決済手数料 例: 代金300円以下、30円手数料をとる場合　300:*0+30, 代金301～1000円以内、代金の2％の手数料をとる場合　999:*0.02, 代金1000円以上の場合、手数料を無料する場合　99999999:*0, 無限大の符号を使えないため、このサイトで存在可能性がない数値で使ってください。 300:*0+30では*0がなければ、手数料は300+30になってしまいますので、ご注意ください。', '6', '3', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('表示の整列順', 'MODULE_PAYMENT_GUIDANCE_SORT_ORDER', '0', '表示の整列順を設定できます。数字が小さいほど上位に表示されます.', '6', '0' , now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, site_id) values ('適用地域', 'MODULE_PAYMENT_GUIDANCE_ZONE', '0', '適用地域を選択すると、選択した地域のみで利用可能となります.', '6', '4', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added, site_id) values ('初期注文ステータス', 'MODULE_PAYMENT_GUIDANCE_ORDER_STATUS_ID', '0', '設定したステータスが受注時に適用されます.', '6', '5', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now(), ".$this->site_id.")");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, site_id) values ('決済可能金額', 'MODULE_PAYMENT_GUIDANCE_MONEY_LIMIT', '0,99999999999', '決済可能金額の最大と最小値の設置
      例：0,3000
      0,3000円に入れると、0円から3000円までの金額が決済可能。設定範囲外の決済は不可。', '6', '0', now(), ".$this->site_id.")");

    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('表示設定', 'MODULE_PAYMENT_GUIDANCE_LIMIT_SHOW', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '表示設定', '6', '1', 'tep_cfg_payment_checkbox_option(array(\'1\', \'2\'), ',now(), ".$this->site_id.");");

    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, site_id) values ('予約注文', 'MODULE_PAYMENT_GUIDANCE_PREORDER_SHOW', 'True', '予約注文で楽天銀行を表示します', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ',now(), ".$this->site_id.");");
  }
/*--------------------------------
 功能：删除SQL 
 参数：无
 返回值：无
 -------------------------------*/
  function remove() {
    tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "') and site_id = '".$this->site_id."'");
  }
/*-----------------------------
 功能：编辑webmoney及game之间的移动方法
 参数：无
 返回值：webmoney及game之间的移动方法数据
 ----------------------------*/
  function keys() {
    return array( 
        'MODULE_PAYMENT_GUIDANCE_STATUS', 
        'MODULE_PAYMENT_GUIDANCE_LIMIT_SHOW', 
        'MODULE_PAYMENT_GUIDANCE_PREORDER_SHOW', 
        'MODULE_PAYMENT_GUIDANCE_ZONE', 
        'MODULE_PAYMENT_GUIDANCE_ORDER_STATUS_ID' , 
        'MODULE_PAYMENT_GUIDANCE_PREORDER_STATUS_ID' ,
        'MODULE_PAYMENT_GUIDANCE_SORT_ORDER', 
        'MODULE_PAYMENT_GUIDANCE_COST', 
        'MODULE_PAYMENT_GUIDANCE_MONEY_LIMIT',
        'MODULE_PAYMENT_GUIDANCE_MAILSTRING',
);
  }

}
?>
