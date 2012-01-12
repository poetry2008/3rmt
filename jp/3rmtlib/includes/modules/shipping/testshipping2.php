<?php
/*
   测试用 配送
  test shipping  
*/
require_once (DIR_WS_CLASSES . 'baseShipping.php');
class testshipping2 extends baseShipping implements shippingInterface {
  var $site_id,$products_id,$title,$description,$enabled,$s_error,$n_fee;
  function loadSpecialSettings($site_id = 0){
    $this->code = 'testshipping2';
  }
  function calc_fee($products_id,$qty,$site_id){
    $this->n_fee = 100;
    return $this->n_fee;
  }
  //获得 配送可用时间列表
  function get_torihiki_date(){
  }
  function get_torihiki_time(){
  }
  function fields(){

  }
  function keys(){
    return array(
'MODULE_SHIPPING_TESTSHIPPING2_STATUS',
'MODULE_SHIPPING_TESTSHIPPING2_LIMIT_SHOW',
'MODULE_SHIPPING_TESTSHIPPING2_PREORDER_SHOW',
'MODULE_SHIPPING_TESTSHIPPING2_ZONE',
'MODULE_SHIPPING_TESTSHIPPING2__TAX_CLASS',
'MODULE_SHIPPING_TESTSHIPPING2_COST',
'MODULE_SHIPPING_TESTSHIPPING2_WORK_TIME',
'MODULE_SHIPPING_TESTSHIPPING2_SLEEP_TIME',
'MODULE_SHIPPING_TESTSHIPPING2_DB_SET_DAY',
'MODULE_SHIPPING_TESTSHIPPING2_SORT_ORDER',
        );
  }
  function get_error(){
    global $_POST, $_GET;
    if(isset($_GET['shipping_error']) && (strlen($_GET['shipping_error']) > 0)) {
      $error_message = '';
      //去翻译文件 定义 TEXT_SHIPPING_ERROR_JA エラー! 
      return array('title' => $this->title.' エラー!', 'error' => $error_message);
    }else{
      return false;
    }
  }

  function check(){
    return true;
  }

  //支付 确认也没的回显
  function specialOutput(){

  }

}

?>
