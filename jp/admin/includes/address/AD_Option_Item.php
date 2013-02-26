<?php
class AD_Option_Item extends AD_Option_DbRecord
{
  var $type;
  var $id;
  var $name;
  var $group_id;
  function __construct()
  {
    parent::__construct();
  }
/*----------------------
 功能：初始化
 参数：无
 返回值：当前值
 ---------------------*/
  function init()
  {
    $this->getInstance();
    return $this;
  }
/*---------------------
 功能：提供选项错误数组
 参数：$option_error_array(array) 选项错误数组
 返回值：无
 --------------------*/
  function render($option_error_array)
  {
    $this->instance->render($option_error_array);
  }
/*--------------------
 功能：获取实例
 参数：无
 返回值：返回当前例
 -------------------*/
  function getInstance()
  {
    if(!$this->instance){
    $instanceClass = "AD_Option_Item_".ucfirst($this->type);
    require_once $instanceClass.".php";
    $this->instance = new $instanceClass();
    $this->instance->formname = $this->name_flag;
    $this->instance->id= $this->id;
    $this->instance->option = $this->type_comment;
    $this->instance->front_title = $this->name; 
    $this->instance->comment = $this->comment; 
    $this->instance->num_limit = $this->num_limit;
    $this->instance->num_limit_min = $this->num_limit_min;
    $this->instance->required = $this->required;
    $this->instance->fixed_option = $this->fixed_option;
    }
    return $this->instance;
  }
/*----------------------------
 功能：解析选项
 参数：$option(string) 选项
 返回值：无
 ---------------------------*/
  function parseOption($option)
  {
    if($optionArray  = unserialize($option)){
      foreach($optionArray as $key=>$value){
        $this->$key = $value;
      }
    }
  }
/*---------------------------
 功能：检查选项错误数组
 参数：$option_error_array(array) 选项错误数组
 返回值：错误数组
 --------------------------*/ 
  function check(&$option_error_array)
  {
    $this->getInstance();
    return $this->instance->check($option_error_array);
  }
}
