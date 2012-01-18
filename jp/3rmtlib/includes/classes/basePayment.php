<?php
interface paymentInterface
{
  public function loadSpecialSettings($site_id);
  public function calc_fee($money);
  public function fields($theData);
  //  public function selection($theData);
  public function pre_confirmation_check();
  public function confirmation() ;
}
/*
  支付方法基本类
 */
class BasePayment
{

  const RULE_NOT_NULL = 'validation_not_null';
  const RULE_NOT_NULL_MSG= '入力内容を確認し、再度入力してください。';
  const RULE_SAME_TO = 'validation_same_to';
  const RULE_EMAIL = 'validation_email';
  const RULE_IS_NUMBER = 'validation_is_number';
  const RULE_IS_NUMBER_MSG = '半角で入力してください';
  const RULE_EMAIL_MSG = '入力内容を確認し、再度入力してください。';
  const RULE_SAME_TO_MSG = '入力内容を確認し、再度入力してください。';
  const REQUIRE_MSG = '<span class="fieldRequired">Error</span>';
  const RULE_CHECK_TEL = 'validation_check_tel';
  const RULE_CHECK_TEL_MSG = '入力内容を確認し、再度入力してください。';
  function __construct($site_id = 0){
    global $order;
    $this->site_id = $site_id;
    $this->loadSpecialSettings($site_id);
    $this->loadSettings($site_id);
    unset($_SESSION[$this->session_error_name]);
  }

  function loadSettings($site_id = 0){

    $this->title        = constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_TITLE");
    $this->description  = constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_DESCRIPTION");
    $this->explain      = constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_EXPLAIN");     
    $this->email_footer = constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_TEXT_EMAIL_FOOTER");
    $this->sort_order  = get_configuration_by_site_id_or_default("MODULE_PAYMENT_".strtoupper($this->code)."_SORT_ORDER",$site_id);
    $this->enabled     = get_configuration_by_site_id_or_default("MODULE_PAYMENT_".strtoupper($this->code)."_STATUS",$site_id) === 'True' ? true : false;
    $this->cost  = get_configuration_by_site_id_or_default("MODULE_PAYMENT_".strtoupper($this->code)."_COST",$site_id);
    $this->fields_description = constant("TS_MODULE_PAYMENT_".strtoupper($this->code)."_FIELDS_DESCRIPTION");

  }
  function selectionError($key,$message){
    $_SESSION[$session_error_name][$key] = $message;
    return true;
  }
  /*
    验证选项是否为真
   */
  function validate_selection(&$selection,$value, $show_type = false)
  {
    $pass = true;
    if(count($value)){
      foreach ($selection['fields'] as $key=>$singleValue){
        if($singleValue['rule'] != null){
          if(is_array($singleValue['rule'])){
            foreach ($singleValue['rule'] as $rule){
              $validateResult = $this->$rule($value[$singleValue['code']],$value[$singleValue['params_code']]);
              if ($validateResult !== true){
                $pass = false;
                //  $selection['error'][]=$validateResult;
                if ($show_type) {
                  $selection['fields'][$key]['message'] = str_replace(
                      array(self::RULE_NOT_NULL_MSG,
                        self::RULE_IS_NUMBER_MSG,self::RULE_CHECK_TEL_MSG), self::REQUIRE_MSG, $validateResult);
                } else {
                  $_SESSION['payment_error'][$this->code]=$validateResult;
                }
                //                $selection['fields'][$key]['message'] = $validateResult;
              }
            }
          } else {
            $validateResult = $this->$singleValue['rule']($value[$singleValue['code']],$value[$singleValue['params_code']]);
            if($validateResult !== true){
              $pass = false;
              if ($show_type) {
                $selection['fields'][$key]['message'] = str_replace(
                      array(self::RULE_NOT_NULL_MSG,
                        self::RULE_IS_NUMBER_MSG,self::RULE_CHECK_TEL_MSG), self::REQUIRE_MSG, $validateResult);
              } else {
                $_SESSION['payment_error'][$this->code]=$validateResult;
              }
                //              $selection['error'][]=$validateResult;
            }
          }
        }
      }
    }
    $selection['validated'] = $pass;
    return $selection;
  }
  
  function validation_not_null($value)
  {
    $value = trim($value);
    if(!empty($value)){
      return true;
    } else{
      return self::RULE_NOT_NULL_MSG;
    }
  }
  function validation_is_number($value)
  {
    $value = trim($value);
    if(preg_match('/^[0-9]+$/',$value)){
      return true;
    }else{
      return self::RULE_IS_NUMBER_MSG;
    }
  }
  function validation_email($value){
    $e = "/^[-+\\.0-9=a-z_]+@([-0-9a-z]+\\.)+([0-9a-z]){2,4}$/i";
    if(!preg_match($e, $value)) return self::RULE_EMAIL_MSG;
    return true;
  }
  function validation_same_to($value1,$value2){

    if( $value2 == $value1 ){
      return true;
    }
    return self::RULE_SAME_TO_MSG;
  }

  function validation_check_tel($value) {
    if (!preg_match("/^(\+\d{2}){0,1}((\d{2}(-){0,1}\d{4})|(\d{3}(-){0,1}\d{3})|(\d{3}(-){0,1}\d{4}))(-){0,1}\d{4}$/", $value)) {
      return self::RULE_CHECK_TEL_MSG; 
    }
    return true; 
  }
  
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
        //$this->n_fee = $table_fee[$i+1]; 
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

    //return $f_find;
    return $this->n_fee;

  }
    

}
