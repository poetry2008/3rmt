<?php
class HM_Option_Item extends Option_DbRecord
{
  var $type;
  var $id;
  var $name;
  var $group_id;
  function __construct()
  {
    parent::__construct();
  }

  function init()
  {
    $this->getInstance();
    return $this;
  }
  function render($option_error_array, $pre_item_str = '', $cart_obj = '', $ptype = false)
  {
    $this->instance->render($option_error_array, $pre_item_str, $cart_obj, $ptype);
  }
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
  function parseOption($option)
  {
    if($optionArray  = unserialize($option)){
      foreach($optionArray as $key=>$value){
        $this->$key = $value;
      }
    }
  }
  
  function check(&$option_error_array, $check_type = 0, $pre_error_str = '')
  {
    $this->getInstance();
    return $this->instance->check($option_error_array, $check_type, $pre_error_str);
  }
}
