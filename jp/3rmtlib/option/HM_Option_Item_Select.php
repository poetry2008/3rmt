<?php
global $language;
require_once "HM_Option_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/option/HM_Option_Item_Select.php';
class HM_Option_Item_Select extends HM_Option_Item_Basic
{
  //var $hasRequire = true;
  var $hasSelect = true; 
  var $hasComment = true;

  function render($option_error_array)
  {
     if (strlen($this->front_title)) {
       echo '<td>';
       echo $this->front_title.':';
       echo '</td>';
     }
     echo '<td>'; 
     if (!empty($this->se_option)) {
       $i = 1; 
       if (isset($this->se_num)) {
         $se_num = $this->se_num; 
       }
       echo '<select name="op_'.$this->formname.'">'; 
       foreach ($this->se_option as $key => $value) {
         if (isset($_POST['op_'.$this->formname])) {
           echo '<option value="'.$value.'"'.(($_POST['op_'.$this->formname] == $value)?'selected ':'').'>'.$value.'</option>'; 
         } else {
           echo '<option value="'.$value.'"'.((isset($se_num))&&($se_num==($i-1))?'selected ':'').'>'.$value.'</option>'; 
         }
         $i++; 
       }
       echo '</select>'; 
     }
     if ($this->secomment) {
       echo '<br>'.$this->secomment; 
     }
     echo '<span id="error_'.$this->formname.'" class="option_error">';
     if (isset($option_error_array[$this->formname])) {
       echo $option_error_array[$this->formname]; 
     }
     echo '</span>'; 
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

