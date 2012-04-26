<?php
global $language;
require_once "HM_Option_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/option/HM_Option_Item_Text.php';
class HM_Option_Item_Text extends HM_Option_Item_Basic
{
  //var $hasRequire = true;
  var $has_textarea_default = true; 

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
    echo '<td>'; 
    if (strlen($this->itextarea)) {
      echo $this->itextarea; 
      echo '<input type="hidden" name="'.$pre_item_str.'op_'.$this->formname.'" value="'.$this->itextarea.'">'; 
    }
    
    $sp_pos = strpos($_SERVER['PHP_SELF'], 'checkout_option.php');
    if ($sp_pos !== false) {
      if ($this->s_price != '0') {
        echo '('.number_format($this->s_price).')'.OPTION_ITEM_TEXT_MONEY_UNIT; 
      }
    } 
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

