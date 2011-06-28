<?php

class HM_Item extends DbRecord
{
  var $type;
  var $id;
  var $name;
  var $group_id;
  var $form_id;
  function __construct()
  {
    parent::__construct();
  }

  function init()
  {
    $this->getInstance();
  }
  function render()
  {
    $this->instance->render();
    if (method_exists($this->instance,'renderScript')){
      $this->instance->renderScript();
    }
  }
  function getInstance()
  {
    $instanceClass = "HM_Item_".ucfirst($this->type);
    require_once $instanceClass.".php";
    $this->instance = new $instanceClass();
    $this->instance->name = $this->name;
    $this->instance->id= $this->id;
    $this->instance->group_id= $this->group_id;
    $this->instance->form_id= $this->form_id;
    $this->instance->init($this->option);
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
  function loadDefaultValue($order_id,$form_id,$group_id)
  {
    //echo $order_id,'form',$form_id,'group',$group_id;
    define("TABLE_OA_FORMVALUE",'oa_formvalue');
    $sql = 'select value from '.TABLE_OA_FORMVALUE.' where ';
    $sql .= ' orders_id="' .$order_id.'"';
    $sql .= 'and  group_id="' .$group_id.'"';
    $sql .= 'and  form_id="' .$form_id.'"';
    $sql .= 'and  item_id="' .$this->id.'"';
    $result = (tep_db_fetch_array(tep_db_query($sql)));
    if ($result){
    $this->instance->order_id = $order_id;
    $this->instance->loadedValue = $result['value'];
    $this->instance->loaded = true;
    }else {
      if (method_exists($this->instance,'initDefaultValue')){
        $this->instance->initDefaultValue($order_id,$form_id,$group_id);
      }
    }
  }
}
