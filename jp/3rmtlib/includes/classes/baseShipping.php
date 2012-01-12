<?php
interface shippingInterface
{
  public function loadSpecialSettings($site_id);
  public function calc_fee($products_id,$qty,$site_id);
}

class BaseShipping
{
  const SHIPPING_RULE_NOT_NULL = 'validation_not_null';
  const SHIPPING_RULE_NOT_NULL_MSG = '該当項目は空白なら行けないです。';
  function __construct($site_id = 0){
    global $order;
    $this->site_id = $site_id;
    $this->loadSpecialSettings($site_id);
    $this->loadSettings($site_id);
    unset($_SESSION[$this->session_error_name]);
  }
  function loadSettings($site_id =0){
    $this->title       = constant("TS_MODULE_SHIPPING_".strtoupper($this->code)."_TEXT_TITLE");
    $this->description = constant("TS_MODULE_SHIPPING_".strtoupper($this->code)."_TEXT_DESCRIPTION");
    $this->explain     = constant("TS_MODULE_SHIPPING_".strtoupper($this->code)."_TEXT_EXPLAIN");    
    $this->sort_order  = get_configuration_by_site_id_or_default("MODULE_SHIPPING_".strtoupper($this->code)."_SORT_ORDER",$site_id);
    $this->enabled     = get_configuration_by_site_id_or_default("MODULE_SHIPPING_".strtoupper($this->code)."_STATUS",$site_id) === 'True' ? true : false;
    $this->sleep_time  = get_configuration_by_site_id_or_default("MODULE_SHIPPING_".strtoupper($this->code)."_SLEEP_TIME",$site_id);
    $this->db_set_day  = get_configuration_by_site_id_or_default("MODULE_SHIPPING_".strtoupper($this->code)."_DB_SET_DAY",$site_id);
    $this->work_time  = get_configuration_by_site_id_or_default("MODULE_SHIPPING_".strtoupper($this->code)."_WORK_TIME",$site_id);
   }
  function selectionError($key,$message){
    $_SESSION[$session_error_name][$key] = $message;
    return true;
  }
  /* 验证表单  */
  function validate_selection(){
  }

  function validate_not_null($value){
    $value = trim($value);
    if(!empty($value)){
    }else{
      return self::SHIPPING_RULE_NOT_NULL_MSG;
    }
  }
  //获得 可选日期的下拉列表
  function get_torihiki_date_select($select=''){
    $today = getdate();
    $m_num = $today['mon'];
    $d_num = $today['mday'];
    $year = $today['year'];
    $res_str = '';
    $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $newarr = array('月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日', '日曜日');
    // 这里需要添加代码 获取 后台配送的时间设置 db_set_day 是后台设置 类似 0-1  3-7
    // start_day 开始时间要加的天数
    // end_day 开始时间加取得的设置的第二个 参数 是结束时间
    //后台设置的 配送 延迟时间 $send_sleep_time
    $send_sleep_time = $this->sleep_time;
    $db_set_day = $this->db_set_day;
    $day_temp_arr = explode('-',$db_set_day);
    if(count($day_temp_arr)>1){
      $start_day = $day_temp_arr[0];
      $end_day = $day_temp_arr[1];
    }else{
      $start_day = 0;
      $end_day = $day_temp_arr[0];
    }
    $show_next_day = false;
    $start_time = time()+$send_sleep_time*60;
    if($start_day == 0){
      if($d_num != intval(date('d',$start_time))){
        $start_day = 1;
        $end_day--;
        $show_next_day = true;
      }
    }
    $check = false;
    for($i=0; $i<=$end_day; $i++) {
      //echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$i,$year)).'">'.strftime("%Y年%m月%d日（%a）", mktime(0,0,0,$m_num,$d_num+$i,$year)).'</option>' . "\n";
      if($select == date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$start_day,$year))){
        $check = true;
      }
      $res_str .= '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$start_day,$year)).
        '" '.($check?'selected="true"':'').'>'.
        str_replace($oarr, $newarr, date("Y年m月d日（l）",
              mktime(0,0,0,$m_num,$d_num+$start_day,$year))).
        '</option>' . "\n";
      $check = false;
      $start_day++;
    }
    $this->start_time = $start_time;
    return $res_str;

  }

}
