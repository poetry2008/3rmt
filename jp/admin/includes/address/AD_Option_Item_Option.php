<?php
require_once "AD_Option_Item_Basic.php";
class AD_Option_Item_Option extends AD_Option_Item_Basic
{
  var $hasSelect = true; 
  var $hasComment = true;

  function render($option_error_array)
  {
     if (strlen($this->front_title)) {
       echo '<td class="main" width="30%">'; 
       echo $this->front_title.':';
       echo '</td>';
     }
     echo '<td class="main" width="70%">'; 
     echo '<input type="hidden" name="'.$this->formname.'" value="'.$this->front_title.'">';
     if (!empty($this->option)) {
        
       
       $option = unserialize($this->option);
       if(!empty($option['option_list']) && count($option['option_list']) == 1){

         echo current($option['option_list']).'<input type="hidden" name="ad_'.$this->formname.'" value="'.current($option['option_list']).'">';
       }elseif(empty($option['option_list']) && count($option[$_SESSION['select_value']]['option_list']) == 1){
         echo '';
       }else{
         echo '<select name="ad_'.$this->formname.'" id="ad_'.$this->formname.'">'; 
       
         $option_array = $option['option_list'];  
         if(empty($option_array)){
         
           $option_array = $option[$_SESSION['select_value']]['option_list'];
           $select_value = $option[$_SESSION['select_value']]['select_value'];
           unset($_SESSION['select_value']);
         }else{
           $select_value = $option['select_value'];
           $_SESSION['select_value'] = isset($_POST['ad_'.$this->formname]) ? $_POST['ad_'.$this->formname] : $option['select_value'];
         }
       
         foreach ($option_array as $key => $value) {
           if (isset($_POST['ad_'.$this->formname])) {
             echo '<option value="'.$value.'"'.(($_POST['ad_'.$this->formname] == $value)?'selected ':'').'>'.$value.'</option>'; 
           } else {
             echo '<option value="'.$value.'" '.((isset($select_value))&&($select_value==$value)?'selected':'').'>'.$value.'</option>'; 
           }
        }
         echo '</select>'; 
      }
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

