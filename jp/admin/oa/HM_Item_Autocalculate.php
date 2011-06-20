<?php
require_once "HM_Item_Basic.php";
class HM_Item_Autocalculate extends HM_Item_Basic
{
  /*
必須：○　必須

項目名_____ _____　
   */
  var $hasRequire = true;
  var $hasTheName = true;
  
  function render()
  {
   if ($this->loaded){
    $this->defaultValue = $this->loadedValue;
}  
    echo $this->beforeInput;
    echo $this->thename;
    echo "<input id = '".$this->formname.'ymd'."' type='hidden' name='".$this->formname."' value='".$this->defaultValue."' />";
    echo "<input id = '".$this->formname.'y'."' size=4 type='text' value='$this->y'  />"."年";
    echo "<input id = '".$this->formname.'m'."' size=4 type='text' value='$this->m'  />"."月";
    echo "<input id = '".$this->formname.'d'."' size=4 type='text' value='$this->d' />"."日";
    echo $this->afterInput;
    echo "<script type='text/javascript'>";
    echo "function mkymd(){";
    echo
    "$('#".$this->formname.'ymd'."').val($('#".$this->formname.'y'."').val()+'-'+$('#".$this->formname.'m'."').val()+'-'+$('#".$this->formname.'d'."').val());";
    echo "}";
    echo "$(document).ready(function()";
    echo "{";
    echo "var dateymd = $('#".$this->formname.'ymd'."').val().split('-');";
    echo "$('#".$this->formname.'y'."').val(dateymd[0]);";
    echo "$('#".$this->formname.'m'."').val(dateymd[1]);";
    echo "$('#".$this->formname.'d'."').val(dateymd[2]);";
    echo "$('#".$this->formname.'y'."').bind('change',mkymd);";
    echo "$('#".$this->formname.'m'."').bind('change',mkymd);";
    echo "$('#".$this->formname.'d'."').bind('change',mkymd);";
    echo "});";
    echo "</script>";
    echo "</br>\n";
  }

  function initDefaultValue($order_id,$form_id,$group_id)
  {
    //    $sql = 'select '.$this->datetype.' dp from orders where orders_id = "'.$order_id.'"';
    //    $result = tep_db_fetch_array(tep_db_query($sql));
    //    $theDate = $result['dp'];
    $theDate = time();
    $this->defaultValue = date('Y-m-d',$theDate);
    $this->m= date('m',strtotime($theDate));
    $this->d= date('d',strtotime($theDate));
    $this->y= date('Y',strtotime($theDate));
  }


  static public function prepareForm($item_id = NULL)
  {
    $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $formString  = '';
    //    $formString .= "必須<input type='checkbox' name='require' ".$checked."/></br>\n";
    //    $formString .= "项目名<input type='text' name='thename' value='".(isset($item_value['thename'])?$item_value['thename']:'')."'/>";
    $formString .= "</br>\n";
    return $formString;
  }


}
