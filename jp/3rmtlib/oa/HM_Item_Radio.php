<?php
require_once "HM_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/oa/HM_Item_Radio.php';
class HM_Item_Radio extends HM_Item_Basic  
{
  function render()
  {
    if(strlen($this->thename)){
      echo "<td>";
    echo $this->thename.':';
      echo "</td>";
    }
    echo "<td>";
   if ($this->loaded){
    $this->defaultValue = $this->loadedValue;
   }  
    foreach($this->radios as $key=>$value){
      if ($this->defaultValue == $value){
	$check = 'checked';
      }else{
	$check = '';
      }
      echo "<input value = '".$value."' type='radio' ".$check." name='".$this->formname."' >".$value;
	  }
      echo "<br>";
      echo $this->afterInput;
      echo "</td>";
  }

  static public function prepareForm($item_id = NULL)
  {
    $item_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where id = '".(int)$item_id."'"); 
    $item_res = tep_db_fetch_object($item_raw); 
    if ($item_res) {
      $item_value = unserialize($item_res->option); 
    }
    $radios = $item_value['radios'];
    $result = '';
    $result .= TEXT_RADIO_MUSTBE."<input type='checkbox' name='require' ".$checked."></br>\n";
    $result .= TEXT_RADIO_P_NAME."<input type='text' name='thename' value='".(isset($item_value['thename'])?$item_value['thename']:'')."'>";
    $result .= TEXT_RADIO_FRONT."<input type='text' name='beforeInput' value='".(isset($item_value['beforeInput'])?$item_value['beforeInput']:'')."'></br>\n";
    $result .= TEXT_RADIO_AFTER."<input type='text' name='afterInput' value='".(isset($item_value['afterInput'])?$item_value['afterInput']:'')."'></br>\n";
    $result .= '</br>';
    if(count($radios)){
      foreach($radios as $key=>$radio){
        $result.= TEXT_RADIO_ELEMENT."<input type='text' name='radios[]' value=$radio />\r\n</br>";
      }
    }else{
        $result.= TEXT_RADIO_ELEMENT."<input type='text' name='radios[]' />\r\n</br>";
    }
    $result .="<script type='text/javascript' >";
    $result .="function insertAitem(e)";
    $result .="{";
    $result .="$('<p>".TEXT_RADIO_ELEMENT."</p><input type=\"text\" name=\"radios[]\" ></br>').insertBefore($(e));";
    $result .="}";
    $result .="</script>";
    $result .="<input value='".TEXT_RADIO_CHECKBOX_ADD."' type='button' onClick='insertAitem(this)' >";
    ?>
<?php 
    return $result;
  }
}
