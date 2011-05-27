<?php

class HM_Item_Basic 
{
  var $name;
  var $class;
  var $formname;
  function init($option)
  {
    //    var_dump($option);
    $this->parseOption($option);
    $this->formname= $this->name.'_'.$this->form_id.'_'.$this->group_id.'_'.$this->id;
  }
  function parseOption($option)
  {
    if($optionArray  = unserialize($option)){
      foreach($optionArray as $key=>$value){
        $this->$key = $value;
      }
    }
  }

}
