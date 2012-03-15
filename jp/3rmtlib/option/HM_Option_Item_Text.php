<?php
global $language;
require_once "HM_Option_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/option/HM_Option_Item_Text.php';
class HM_Option_Item_Text extends HM_Option_Item_Basic
{
  //var $hasRequire = true;
  var $has_textarea_default = true; 

  function render($option_error_array)
  {
    if (strlen($this->front_title)) {
      echo '<td>'; 
      echo $this->front_title.':'; 
      echo '</td>'; 
    }
    echo '<td>'; 
    if (strlen($this->itextarea)) {
      echo $this->itextarea; 
      echo '<input type="hidden" name="op_'.$this->formname.'" value="'.$this->itextarea.'">'; 
    }
    echo '</td>'; 
  }
  
  
  static public function prepareForm($item_id = NULL)
  {
    return $formString;
  }

  function check(&$option_error_array)
  {
    return false; 
  }
}

