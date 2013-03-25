<?php
require_once(DIR_WS_LANGUAGES.$language.'/basepayment.php');
interface paymentInterface
{
  public function loadSpecialSettings($site_id=0);
  public function calc_fee($money);
  public function fields($theData=false, $back=false);
  public function pre_confirmation_check();
  public function confirmation() ;
}
/*
  支付方法基本类
 */
class BasePayment
{

  const RULE_NOT_NULL = 'validation_not_null';
  const RULE_NOT_NULL_MSG= BASEPAYMENT_ERROR_MSG;
  const RULE_SAME_TO = 'validation_same_to';
  const RULE_EMAIL = 'validation_email';
  const RULE_IS_NUMBER = 'validation_is_number';
  const RULE_IS_NUMBER_MSG =BASEPAYMENT_ERROR_NUMBER_MSG;
  const RULE_EMAIL_MSG = BASEPAYMENT_ERROR_MSG;
  const RULE_SAME_TO_MSG = BASEPAYMENT_ERROR_MSG;
  const REQUIRE_MSG = '<span class="fieldRequired">Error</span>';
  const RULE_CHECK_TEL = 'validation_check_tel';
  const RULE_CHECK_TEL_MSG = BASEPAYMENT_ERROR_MSG;
  var $p_error_msg = ''; 
/*-----------------------
 功能：初始化 
 参数: $site_id(string) SITE ID 值
 返回值: 无
 -----------------------*/
  function __construct($site_id = 0){
    global $order;
    $this->site_id = $site_id;
    $this->loadSpecialSettings($site_id);
    $this->loadSettings($site_id);
    unset($_SESSION[$this->session_error_name]);
  }
/*----------------------
 功能：加载设置
 参数：$site_id(string) SITE ID 值
 返回值：无
 ---------------------*/
  function loadSettings($site_id = 0){
    if(defined("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_TITLE")){
      $this->title        = constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_TITLE");
    }
    if(defined("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_DESCRIPTION")){
      $this->description  = constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_DESCRIPTION");
    }
    if(defined("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_EXPLAIN")){
      $this->explain      = constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_EXPLAIN");     
    }
    if(defined("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_EMAIL_FOOTER")){
      $this->email_footer = constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_EMAIL_FOOTER");
    }
    $this->sort_order  = get_configuration_by_site_id_or_default("MODULE_PAYMENT_".strtoupper($this->code)."_SORT_ORDER",$site_id);
    $this->enabled     = get_configuration_by_site_id_or_default("MODULE_PAYMENT_".strtoupper($this->code)."_STATUS",$site_id) === 'True' ? true : false;
    $this->cost  = get_configuration_by_site_id_or_default("MODULE_PAYMENT_".strtoupper($this->code)."_COST",$site_id);
    if(defined("TS_MODULE_PAYMENT_".strtoupper($this->code)."_FIELDS_DESCRIPTION")){
      $this->fields_description = constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_FIELDS_DESCRIPTION");
    }

  }
/*--------------------------
 功能：验证错误 
 参数：$key(string) 关键字
 参数：$message(string) 信息
 返回值：验证成功(boolean)
 -------------------------*/
  function selectionError($key,$message){
    $_SESSION[$session_error_name][$key] = $message;
    return true;
  }
/*--------------------------
  功能：验证选项是否为真
  参数：$selection(string) 选项
  参数：$value(string) 数值
  参数：$show_type(string) 是否显示类型
  返回值：判断选项是否为真(string)
 -------------------------*/
  function validate_selection(&$selection,$value, $show_type = false)
  {
    $pass = true;
    if(count($value)){
      foreach ($selection['fields'] as $key=>$singleValue){
        if($singleValue['rule'] != null){
          if(is_array($singleValue['rule'])){
            $i_num = 0; 
            foreach ($singleValue['rule'] as $rule){
              if (isset($singleValue['error_msg'][$i_num])) {
                $this->p_error_msg = $singleValue['error_msg'][$i_num]; 
              }
              $validateResult = $this->$rule($value[$singleValue['code']],$value[$singleValue['params_code']],$p_error_msg);
              if ($validateResult !== true){
                $pass = false;
                if ($show_type) {
                  $selection['fields'][$key]['message'] = str_replace(
                      array(self::RULE_NOT_NULL_MSG,
                        self::RULE_IS_NUMBER_MSG,self::RULE_CHECK_TEL_MSG), self::REQUIRE_MSG, $validateResult);
                } else {
                  if (!empty($this->p_error_msg)) {
                    $_SESSION['payment_error'][$this->code][]=$validateResult;
                  } else {
                    $_SESSION['payment_error'][$this->code]=$validateResult;
                  }
                }
              }
              $i_num++; 
            }
          } else {
            if (isset($singleValue['error_msg'])) {
              $this->p_error_msg = $singleValue['error_msg']; 
            }
            $validateResult = $this->$singleValue['rule']($value[$singleValue['code']],$value[$singleValue['params_code']], $p_error_msg);
            if($validateResult !== true){
              $pass = false;
              if ($show_type) {
                $selection['fields'][$key]['message'] = str_replace(
                      array(self::RULE_NOT_NULL_MSG,
                        self::RULE_IS_NUMBER_MSG,self::RULE_CHECK_TEL_MSG), self::REQUIRE_MSG, $validateResult);
              } else {
                if (!empty($this->p_error_msg)) {
                  $_SESSION['payment_error'][$this->code][]=$validateResult;
                } else {
                  $_SESSION['payment_error'][$this->code]=$validateResult;
                }
              }
            }
          }
        }
      }
    }
    $selection['validated'] = $pass;
    return $selection;
  }
/*------------------------------
 功能：验证是否为空 
 参数：$value(string) 数值
 返回值：判断是否为空(boolean/string)
 -----------------------------*/  
  function validation_not_null($value)
  {
    $value = trim($value);
    if(!empty($value)){
      return true;
    } else{
      if (!empty($this->p_error_msg)) {
        return $this->p_error_msg; 
      } else {
        return self::RULE_NOT_NULL_MSG;
      }
    }
  }
/*-----------------------------
 功能：验证数字
 参数：$value(string) 数值
 返回值：判断是否验证数字成功(boolean/string)
 ----------------------------*/
  function validation_is_number($value)
  {
    $value = trim($value);
    if(preg_match('/^[0-9]+$/',$value)){
      return true;
    }else{
      if (!empty($this->p_error_msg)) {
        return $this->p_error_msg; 
      } else {
        return self::RULE_IS_NUMBER_MSG;
      } 
    }
  }
/*---------------------------
 功能：验证邮箱
 参数：$value(string) 参数值
 返回值:判断是否验证邮箱成功(string)
 --------------------------*/
  function validation_email($value){
    $value  = str_replace("\xe2\x80\x8b", '', $value);
    if(!tep_validate_email($value)){
      if (!empty($this->p_error_msg)) {
        return $this->p_error_msg; 
      } else {
        return self::RULE_EMAIL_MSG;
      } 
    }
    return true;
  }
/*--------------------------
 功能：验证是否相同 
 参数：$value1(string) 参数1
 参数：$value2(string) 参数2
 返回值：判断是否相同(boolean/string)
 -------------------------*/
  function validation_same_to($value1,$value2){
    $cmp_int = strcmp($value1, $value2); 
    if($cmp_int == 0){
      return true;
    }
    if (!empty($this->p_error_msg)) {
      return $this->p_error_msg; 
    } else {
      return self::RULE_SAME_TO_MSG;
    }
  }
/*-------------------------------
 功能：验证检查电话 
 参数：$value(string) 参数值
 返回值：判断验证电话是否成功(boolean/string)
 ------------------------------*/
  function validation_check_tel($value) {
    if (!preg_match("/^(\+\d{2}){0,1}((\d{2}(-){0,1}\d{4})|(\d{3}(-){0,1}\d{3})|(\d{3}(-){0,1}\d{4}))(-){0,1}\d{4}$/", $value)) {
      if (!empty($this->p_error_msg)) {
        return $this->p_error_msg; 
      } else {
        return self::RULE_CHECK_TEL_MSG; 
      }
    }
    return true; 
  }
/*-----------------------------
 功能：手续费
 参数：$money(string) 费用
 返回值：手续费(string)
 ----------------------------*/
  function calc_fee($money){
    if(!$this->cost){
      return 0;
    }
    $table_fee = split("[:,]" , $this->cost);
    $f_find = false;
    for ($i = 0; $i < count($table_fee); $i+=2) {
      if ($money <= $table_fee[$i]) { 
        $additional_fee = $money.$table_fee[$i+1]; 
        @eval("\$additional_fee = $additional_fee;"); 
        if (is_numeric($additional_fee)) {
          $this->n_fee = intval($additional_fee); 
        } else {
          $this->n_fee = 0; 
        }
        $f_find = true;
        break;
      }
    }
    if ( !$f_find ) {
      $this->s_error = 'over flow';
    }

    return $this->n_fee;

  }
    

}
