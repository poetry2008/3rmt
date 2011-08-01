<?php
global $language;
require_once DIR_WS_LANGUAGES . $language . '/oa/HM_Item_Basic.php';

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
    $sql = 'delete from '. TABLE_OA_FORMVALUE. " where form_id = '" . tep_db_input($form_id) . "' and orders_id ='".tep_db_input($order_id)."' and item_id='". tep_db_input($item_id) . "' and group_id='". tep_db_input($group_id) . "'";
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
    $formString = '<table width="100%" boder="0">';

    if ($this->hasRequire){
      $checked = isset($item_value['require'])?'checked="true"':'';
      $formString .= "<tr><td width='5%' nowrap >".TEXT_MUSTBE."</td><td width='15%'><input type='checkbox' name='require' ".$checked."/></td><td><font size='2' color='#ff0000'>".$this->must_comment."</font></td></tr>";      
    }
    //关联状态
    if ($this->hasSelect){
      $languages_id = 4;
      $orders_statuses     = $all_orders_statuses = $orders_status_array = array();
      $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");
      $formString .="<tr><td width='5%' nowrap >".TEXT_OA_BASEIC_STATUS."</td><td width='15%'><select name='status'>";

      if($item_value['status'] == 'null'){
        $selcted = 'selected';
      }else {
        $selcted ='';
      }

      $formString .= "<option ".$selcted. " value='null'".">".TEXT_OA_BASEIC_SELECT."</option>";      

      while ($orders_status = tep_db_fetch_array($orders_status_query)) {
        if($item_value['status'] == $orders_status['orders_status_id']){
          $selcted = 'selected';
        }else {
          $selcted ='';
        }
         $formString .= "<option ".$selcted. " value=".$orders_status['orders_status_id'].">".$orders_status['orders_status_name']."</option>";
      }
      $formString .="</select></td><td><font size='2' color='#ff0000'>".$this->status_comment."</font></td></tr>";
      //    $formString .= "<input type='text' name='status' value='".(isset($item_value['status'])?$item_value['status']:'')."'/></br>\n";
    }
    if ($this->hasTheName){
      $formString .= "<tr><td width='5%' nowrap >".TEXT_OA_BASEIC_P_NAME."</td><td width='15%'><input type='text' size='40' name='thename' value='".(isset($item_value['thename'])?$item_value['thename']:'')."'/></td><td><font size='2' color='#ff0000'>".$this->project_name_comment."</font></td></tr>";
    }

    if ($this->hasFrontText){
      $formString .= "<tr><td width='5%' nowrap >".TEXT_OA_BASEIC_F_TEXT."</td><td width='15%'><input type='text' size='40' name='beforeInput' size='40' value='".(isset($item_value['beforeInput'])?$item_value['beforeInput']:'')."'/></td><td><font size='2' color='#ff0000'>".$this->front_comment."</font></td></tr>";
    }
    if ($this->hasSubmit){
      $formString .= "<tr><td width='5%' nowrap >SubmitName</td><td width='15%'><input type='text' size='40' name='submitName' value='".(isset($item_value['submitName'])?$item_value['submitName']:'')."'/></td><td><font size='2' color='#ff0000'>".$this->submit_name_comment."</font></td></tr>";
    }
    if ($this->hasBackText){
      $formString .= "<tr><td width='5%' nowrap >".TEXT_OA_BASEIC_E_TEXT."</td><td width='15%'><input type='text' size='40' name='afterInput' value='".(isset($item_value['afterInput'])?$item_value['afterInput']:'')."'/></td><td><font size='2' color='#ff0000'>".$this->after_comment."</font></td></tr>";
    }
    if ($this->hasDefaultValue){
    $formString .= "<tr><td width='5%' nowrap >defaultValue</td><td width='15%'><input type='text' size='40' name='defaultValue' value='".(isset($item_value['defaultValue'])?$item_value['defaultValue']:'')."'/></td><td><font size='2' color='#ff0000'>".$this->default_value_comment."</font></td></tr>";
    }
    if($this->hasSize){
    $formString .= "<tr><td width='5%' nowrap >Size</td><td width='15%'><input type='text' size='40' name='size' value='".(isset($item_value['size'])?$item_value['size']:'')."'/></td><td><font size='2' color='#ff0000'>".$this->size_comment."</font></td></tr>";
    }

    if (!isset($this->html_form_end)) {
	$formString .= "</table>";
    }
    return $formString;
  }



}
