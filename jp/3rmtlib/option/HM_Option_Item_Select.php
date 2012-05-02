<?php
global $language;
require_once "HM_Option_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/option/HM_Option_Item_Select.php';
class HM_Option_Item_Select extends HM_Option_Item_Basic
{
  var $hasSelect = true; 
  var $has_select_default = true; 
  var $hasComment = true;

  function render($option_error_array, $pre_item_str = '', $cart_obj = '', $ptype = false)
  {
     $sp_pos = strpos($_SERVER['PHP_SELF'], 'checkout_option.php');
     
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
     if ($cart_obj != '') {
       $pre_item_tmp_str = substr($pre_item_str, 0, -1); 
       if (isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && (!isset($_POST[$pre_item_str.'op_'.$this->formname]))) {
         $default_value = $cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]; 
       } else {
         if (isset($_POST[$pre_item_str.'op_'.$this->formname])) {
           $default_value = $_POST[$pre_item_str.'op_'.$this->formname]; 
         } else {
           $default_value = ''; 
         }
       }
     } else {
       if (isset($_POST[$pre_item_str.'op_'.$this->formname])) {
         $default_value = $_POST[$pre_item_str.'op_'.$this->formname]; 
       } else {
         $default_value = ''; 
       }
     }
     if ($sp_pos !== false) {
         if (!isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && !isset($_POST[$pre_item_str.'op_'.$this->formname])) {
           $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_ORDERS." o where opa.orders_id = o.orders_id and o.customers_id = '".(int)$_SESSION['customer_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' order by opa.orders_id desc limit 1"); 
           $o_attributes_res = tep_db_fetch_array($o_attributes_raw); 
           if ($o_attributes_res) {
             $old_option_info = @unserialize(stripslashes($o_attributes_res['option_info']));  
             $default_value = $old_option_info['value']; 
           }
         }
       
     }
     echo '<td>'; 
     echo '<div class="option_info_text">'; 
     if (!empty($this->se_option)) {
       $i = 1; 
       echo '<select name="'.$pre_item_str.'op_'.$this->formname.'">'; 
       if (strlen($this->sedefault)) {
         echo '<option value="">'.$this->sedefault.'</option>'; 
       }
       foreach ($this->se_option as $key => $value) {
         echo '<option value="'.$value.'"'.(($default_value == $value)?'selected ':'').'>'.$value.'</option>'; 
         $i++; 
       }
       echo '</select>'; 
     }
    
     if ($sp_pos !== false) {
       if ($this->s_price != '0') {
         echo '<span class="option_money">'.number_format($this->s_price).OPTION_ITEM_SELECT_MONEY_UNIT.'</span>'; 
       }
     }
     echo '</div>'; 
     if ($this->secomment) {
       echo $this->secomment; 
     }
     echo '<span id="'.$pre_item_str.'error_'.$this->formname.'" class="option_error">';
     if (isset($option_error_array[$pre_item_str.$this->formname])) {
       echo '<br>'.$option_error_array[$pre_item_str.$this->formname]; 
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
    
    global $_POST;
    $input_text_str = $_POST[$pre_error_str.'op_'.$this->formname];
    $input_text_str = str_replace(' ', '', $input_text_str); 
    $input_text_str = str_replace('　', '', $input_text_str); 
    if ($input_text_str == '') {
      $option_error_array[$pre_error_str.$this->formname] = ERROR_OPTION_ITEM_SELECT_TEXT_NULL; 
      return true; 
    }
    return false; 
  }
}

