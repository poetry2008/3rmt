<?php
global $language;
require_once "HM_Option_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/option/HM_Option_Item_Select.php';
class HM_Option_Item_Select extends HM_Option_Item_Basic
{
  //var $hasRequire = true;
  var $hasSelect = true; 
  var $hasComment = true;

  function render($option_error_array, $pre_item_str = '', $cart_obj = '', $ptype = false)
  {
     if (strlen($this->front_title)) {
       if ($ptype) {
         echo '<td class="preorder_option_name">';
       } else {
         echo '<td class="option_name">';
       }
       echo $this->front_title.':';
       echo '</td>';
     }
     $default_value = '';
     $is_obj_single = 0; 
     if ($cart_obj != '') {
       $pre_item_tmp_str = substr($pre_item_str, 0, -1); 
       if (isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && (!isset($_POST[$pre_item_str.'op_'.$this->formname]))) {
         $default_value = $cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]; 
         $is_obj_single = 1; 
       } else {
         $default_value = $_POST[$pre_item_str.'op_'.$this->formname]; 
       }
     } else {
       $default_value = $_POST[$pre_item_str.'op_'.$this->formname]; 
     }
     echo '<td>'; 
     if (!empty($this->se_option)) {
       $i = 1; 
       if (isset($this->se_num)) {
         $se_num = $this->se_num; 
       }
       echo '<select name="'.$pre_item_str.'op_'.$this->formname.'">'; 
       foreach ($this->se_option as $key => $value) {
         if (isset($_POST[$pre_item_str.'op_'.$this->formname]) || ($is_obj_single == 1)) {
           echo '<option value="'.$value.'"'.(($default_value == $value)?'selected ':'').'>'.$value.'</option>'; 
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
     echo '<span id="'.$pre_item_str.'error_'.$this->formname.'" class="option_error">';
     if (isset($option_error_array[$pre_item_str.$this->formname])) {
       echo $option_error_array[$pre_item_str.$this->formname]; 
     }
     echo '</span>'; 
     echo '</td>'; 
  }
  static public function prepareForm($item_id = NULL)
  {
    return $formString;
  }
  
  function check(&$option_error_array, $check_type = 0, $pre_error_str = '')
  {
    return false; 
  }
}

