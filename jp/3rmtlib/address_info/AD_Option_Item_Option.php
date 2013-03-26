<?php
require_once "AD_Option_Item_Basic.php";
class AD_Option_Item_Option extends AD_Option_Item_Basic
{
  var $hasSelect = true; 
  var $hasComment = true;

/* -------------------------------------
    功能: 输出该元素 
    参数: $option_error_array(array) 错误信息  
    参数: $is_space(boolean) 是否空行  
    返回值: 无 
------------------------------------ */
  function render($option_error_array,$is_space = false)
  {
     if (strlen($this->front_title)) {
       echo '<td class="main" width="93">';
       echo $this->front_title.':';
       echo '</td>';
     }
     if(!$is_space){
       echo '<td class="main">'; 
     }else{
         
       echo '<td class="main">'; 
     }
     echo '<input type="hidden" name="'.$this->formname.'" value="'.$this->front_title.'">';
    if($this->fixed_option == 0){
     if (!empty($this->option)) {
        
       
       $option = unserialize($this->option);
       if(!empty($option['option_list']) && count($option['option_list']) == 1){

         echo current($option['option_list']).'<input type="hidden" name="op_'.$this->formname.'" value="'.current($option['option_list']).'">';
       }elseif(empty($option['option_list']) && count($option[$_SESSION['select_value']]['option_list']) == 1){
         echo '';
       }else{
         echo '<select name="op_'.$this->formname.'" id="op_'.$this->formname.'">'; 
       
         $option_array = $option['option_list'];  
         if(empty($option_array)){
         
           $option_array = $option[$_SESSION['select_value']]['option_list'];
           $select_value = $option[$_SESSION['select_value']]['select_value'];
           unset($_SESSION['select_value']);
         }else{
           $select_value = $option['select_value'];
           $_SESSION['select_value'] = isset($_POST['op_'.$this->formname]) ? $_POST['op_'.$this->formname] : $option['select_value'];
         }
       
         foreach ($option_array as $key => $value) {
           if (isset($_POST['op_'.$this->formname])) {
             echo '<option value="'.$value.'"'.(($_POST['op_'.$this->formname] == $value)?'selected ':'').'>'.$value.'</option>'; 
           } else {
             echo '<option value="'.$value.'" '.((isset($select_value))&&($select_value==$value)?'selected':'').'>'.$value.'</option>'; 
           }
        }
         echo '</select>'; 
      }
     } 
    }else{

      echo '<select name="op_'.$this->formname.'" id="op_'.$this->formname.'"></select>';
    }
     echo '<span id="error_'.$this->formname.'" class="option_error">';
     if (isset($option_error_array[$this->formname])) {
       echo $option_error_array[$this->formname]; 
     }
     echo '</span>'; 
     echo '</td>'; 
  }

/* -------------------------------------
    功能: 指定元素的项目 
    参数: $item_id(int) 元素id  
    返回值: 元素的项目的html(string) 
------------------------------------ */
  static public function prepareForm($item_id = NULL)
  {
    return $formString;
  }
  
/* -------------------------------------
    功能: 检查信息是否正确 
    参数: $option_error_array(array) 错误信息  
    返回值: 是否正确(boolean) 
------------------------------------ */
  function check(&$option_error_array)
  {
    return false; 
  }
}

