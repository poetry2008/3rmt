<?php
global $language;
require_once "HM_Option_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/option/HM_Option_Item_Textarea.php';
class HM_Option_Item_Textarea extends HM_Option_Item_Basic
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
    echo '<textarea
      name="op_'.$this->formname.'">'.(isset($_POST['op_'.$this->formname])?$_POST['op_'.$this->formname]:'').'</textarea>'; 
     echo '<span id="error_'.$this->formname.'" class="option_error">';
     if (isset($option_error_array[$this->formname])) {
       echo $option_error_array[$this->formname]; 
     }
     echo '</span>'; 
    if (strlen($this->itextarea)) {
      echo '<br>'.$this->itextarea; 
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

