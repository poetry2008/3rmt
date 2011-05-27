<?php
require_once "HM_Item_Basic.php";
class HM_Item_Text extends HM_Item_Basic
{
  function getDefaultValue()
  {
    if ($this->loaded){
      return $this->loadedValue;
    }else{
      return $this->defaultValue;
    }

  }

  function render()
  {
    echo $this->beforeInput."<input type='text' name='".$this->formname."'
      value='".$this->getDefaultValue()."' />".$this->afterInput;
    echo "</br>";
  }

  static public function prepareForm($item_id = NULL)
  {
    $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $formString  = '';
    $formString .= "元素前文字<input type='text' name='beforeInput' value='".(isset($item_value['beforeInput'])?$item_value['beforeInput']:'')."'/></br>\n";
    $formString .= "defaultValue<input type='text' name='defaultValue'
      value='".(isset($item_value['defaultValue'])?$item_value['defaultValue']:'')."'/></br>\n";
    $formString .= "元素后文字<input type='text' name='afterInput' value='".(isset($item_value['afterInput'])?$item_value['afterInput']:'')."'/></br>\n";
    return $formString;
  }

  
}

