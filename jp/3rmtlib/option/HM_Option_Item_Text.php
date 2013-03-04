<?php
global $language;
require_once "HM_Option_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/option/HM_Option_Item_Text.php';
class HM_Option_Item_Text extends HM_Option_Item_Basic
{
  //var $hasRequire = true;
  var $has_textarea_default = true; 

/* -------------------------------------
    功能: 输出元素的html 
    参数: $option_error_array(array) 错误信息   
    参数: $pre_item_str(string) 变量名前缀   
    参数: $$cart_obj(obj) 购物车对象   
    参数: $ptype(boolean) 是否是预约转正式   
    返回值: 无 
------------------------------------ */
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
      echo '<input type="hidden" name="'.$pre_item_str.'op_'.$this->formname.'" value="'.stripslashes($this->itextarea).'">'; 
    }
    
    $sp_pos = strpos($_SERVER['PHP_SELF'], 'checkout_option.php');
    /* 
    if ($sp_pos !== false) {
      if ($this->s_price != '0') {
        echo '<span class="option_money">'.number_format($this->s_price).OPTION_ITEM_TEXT_MONEY_UNIT.'</span>'; 
      }
    } 
    */ 
    $pro_pos = strpos($_SERVER['PHP_SELF'], 'product_info.php');
    if ($pro_pos !== false) {
       echo '<input id="tp1_'.$pre_item_str.$this->formname.'" type="hidden" name="tp1_'.$pre_item_str.$this->formname.'" value="'.number_format($this->s_price).'">'; 
    }
    echo '</td>'; 
  }
  
/* -------------------------------------
    功能: 输出相应的项 
    参数: $item_id(int) 元素id   
    返回值: 相应的项的html(string) 
------------------------------------ */
  static public function prepareForm($item_id = NULL)
  {
    return $formString;
  }

/* -------------------------------------
    功能: 检查信息是否正确 
    参数: $option_error_array(array) 错误信息   
    参数: $check_type(int) 类型   
    参数: $pre_error_str(string) 名字前缀   
    返回值: 是否正确(boolean) 
------------------------------------ */
  function check(&$option_error_array, $check_type = 0, $pre_error_str = '')
  {
    return false; 
  }
}

