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
  const RULE_NOT_NULL_MSG = '此项必须填写';
  const RULE_SAME_TO = 'validation_same_to';
  const RULE_SAME_TO_MSG = '必须一样';
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
  function validate_selection(&$selection,$value)
  {
    $pass = true;
    if(count($value)){
      foreach ($selection['fields'] as $key=>$singleValue){
        if($singleValue['rule'] != null){
          if(is_array($singleValue['rule'])){
            foreach ($singleValue['rule'] as $rule){
              $validateResult = $this->$singleValue['rule']($value[$singleValue['code']],$value[$singleValue['params_code']]);
              if ($validateResult !== true){
                $pass = false;
                $selection['fields'][$key]['message'] = $validateResult;
              }
            }
          } else {
            $validateResult = $this->$singleValue['rule']($value[$singleValue['code']],$value[$singleValue['params_code']]);
            if($validateResult !== true){
              $pass = false;
              $selection['fields'][$key]['message'] = $validateResult;
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
  function validation_same_to($value1,$value2){

    if( $value2 == $value1 ){
      return true;
    }
    return self::RULE_SAME_TO_MSG;
  }

  function calc_fee($money){
    $table_fee = split("[:,]" , $this->cost);
    $f_find = false;
    $this->n_fee = 0;
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
