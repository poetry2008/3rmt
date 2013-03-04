<?php
class HM_Option_Item extends Option_DbRecord
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
    返回值: 对象(obj) 
------------------------------------ */
  function init()
  {
    $this->getInstance();
    return $this;
  }
  
/* -------------------------------------
    功能: 输出元素的html 
    参数: $option_error_array(array) 错误信息   
    参数: $pre_item_str(string) 变量名前缀   
    参数: $$cart_obj(obj) 购物车对象   
    参数: $ptype(boolean) 是否是预约转正式   
    返回值: 无 
------------------------------------ */
  function render($option_error_array, $pre_item_str = '', $cart_obj = '', $ptype = false)
  {
    $this->instance->render($option_error_array, $pre_item_str, $cart_obj, $ptype);
  }

/* -------------------------------------
    功能: 获得对象实例 
    参数: 无   
    返回值: 对象实例(obj) 
------------------------------------ */
  function getInstance()
  {
    if(!$this->instance){
    $instanceClass = "HM_Option_Item_".ucfirst($this->type);
    require_once $instanceClass.".php";
    $this->instance = new $instanceClass();
    $this->instance->name = $this->name;
    $this->instance->id= $this->id;
    $this->instance->group_id= $this->group_id;
    $this->instance->init($this->option);
    $this->instance->front_title = $this->front_title; 
    $this->instance->s_price = $this->price; 
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
    参数: $check_type(int) 类型   
    参数: $pre_error_str(string) 错误变量名前缀   
    返回值: 信息是否正确(boolean) 
------------------------------------ */
  function check(&$option_error_array, $check_type = 0, $pre_error_str = '')
  {
    $this->getInstance();
    return $this->instance->check($option_error_array, $check_type, $pre_error_str);
  }
}
