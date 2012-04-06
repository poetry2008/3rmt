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

  function init()
  {
    $this->getInstance();
    return $this;
  }
  function render($option_error_array)
  {
    $this->instance->render($option_error_array);
  }
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
  
  function check(&$option_error_array)
  {
    $this->getInstance();
    return $this->instance->check($option_error_array);
  }
}
