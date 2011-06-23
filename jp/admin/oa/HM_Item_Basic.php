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

  function updateValue($order_id,$form_id,$group_id,$item_id,$result)
    {
    $sql = 'delete from '. TABLE_OA_FORMVALUE. " where form_id = '" . tep_db_input($form_id) . "' and item_id='". tep_db_input($item_id) . "' and group_id='". tep_db_input($group_id) . "'";
    tep_db_query($sql);
    $sql = "INSERT INTO ".TABLE_OA_FORMVALUE ." (`id`, `orders_id`, `form_id`, `item_id`, `group_id`, `name`, `value`) VALUES (NULL,'".$order_id."',".$form_id.",".$item_id.",".$group_id.",'".$this->formname."','".$result."')";
    return tep_db_query($sql);
    
    }
  public function prepareFormWithParent($item_id){
    $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $formString  = '';
    $formString ='';

    if ($this->hasRequire){
      $checked = isset($item_value['require'])?'checked="true"':'';
      $formString .= "必須<input type='checkbox' name='require' ".$checked."/></br>\n";      
    }
    //关联状态
    if ($this->hasSelect){
      $languages_id = 4;
      $orders_statuses     = $all_orders_statuses = $orders_status_array = array();
      $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");
      $formString .="ステータス<select name='status'>";
      while ($orders_status = tep_db_fetch_array($orders_status_query)) {
        if($item_value['status'] == $orders_status['orders_status_id']){
          $selcted = 'selected';
        }else {
          $selcted ='';
        }
         $formString .= "<option ".$selcted. " value=".$orders_status['orders_status_id'].">".$orders_status['orders_status_name']."</option>";
      }
      $formString .="</select></br>\n";
      //    $formString .= "<input type='text' name='status' value='".(isset($item_value['status'])?$item_value['status']:'')."'/></br>\n";
    }
    if ($this->hasTheName){
      $formString .= "项目名<input type='text' name='thename' value='".(isset($item_value['thename'])?$item_value['thename']:'')."'/></br>\n";
    }

    if ($this->hasFrontText){
      $formString .= "前方文字<input type='text' name='beforeInput' value='".(isset($item_value['beforeInput'])?$item_value['beforeInput']:'')."'/></br>\n";
    }
    if ($this->hasSubmit){
      $formString .= "SubmitName<input type='text' name='submitName'      value='".(isset($item_value['submitName'])?$item_value['submitName']:'')."'/></br>\n";
    }
    if ($this->hasBackText){
      $formString .= "後方文字<input type='text' name='afterInput' value='".(isset($item_value['afterInput'])?$item_value['afterInput']:'')."'/></br>\n";
    }
    if ($this->hasDefaultValue){
    $formString .= "defaultValue<input type='text' name='defaultValue'   value='".(isset($item_value['defaultValue'])?$item_value['defaultValue']:'')."'/></br>\n";
    }
    if($this->hasSize){
    $formString .= "Size<input type='text' name='size' value='".(isset($item_value['size'])?$item_value['size']:'')."'/></br>\n";
    }


    return $formString;
  }



}
