<?php
class AD_Option_Item extends AD_Option_DbRecord
{
  var $type;
  var $id;
  var $name;
  var $group_id;

/* -------------------------------------
    功能: 构造函数 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function __construct()
  {
    parent::__construct();
  }

/* -------------------------------------
    功能: 初始化 
    参数: 无   
    返回值: 元素对象(obj) 
------------------------------------ */
  function init()
  {
    $this->getInstance();
    return $this;
  }

/* -------------------------------------
    功能: 输出该对象信息 
    参数: $option_error_array(array) 错误信息   
    参数: $is_space(array) 是否空行   
    返回值: 无 
------------------------------------ */
  function render($option_error_array, $is_space = false)
  {
    $this->instance->render($option_error_array, $is_space);
  }

/* -------------------------------------
    功能: 获得该对象 
    参数: 无   
    返回值: 对象(obj) 
------------------------------------ */
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

/* -------------------------------------
    功能: 格式化信息 
    参数: $option(string) 信息   
    返回值: 无 
------------------------------------ */
  function parseOption($option)
  {
    if($optionArray  = unserialize($option)){
      foreach($optionArray as $key=>$value){
        $this->$key = $value;
      }
    }
  }
  
/* -------------------------------------
    功能: 检查信息是否正确 
    参数: $option_error_array(array) 错误信息   
    返回值: 是否正确(boolean) 
------------------------------------ */
  function check(&$option_error_array)
  {
    $this->getInstance();
    return $this->instance->check($option_error_array);
  }
}
