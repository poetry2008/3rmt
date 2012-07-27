<?php
require_once "AD_Option_Item_Basic.php";
class AD_Option_Item_Option extends AD_Option_Item_Basic
{
  var $hasSelect = true; 
  var $hasComment = true;

  function render($option_error_array, $is_space = false)
  {
     if (!$is_space) {
       if (NEW_STYLE_WEB !== true) {
         echo '<td width="10">'. tep_draw_separator('pixel_trans.gif', '10', '1') .'</td>';
       }
     }
     if (strlen($this->front_title)) {
       if (NEW_STYLE_WEB === true) {
         echo '<td class="main" width="20%">'; 
       } else {
         echo '<td class="main" width="30%">'; 
       }
       echo $this->front_title.':';
       echo '</td>';
     }
     if (NEW_STYLE_WEB === true) {
       echo '<td class="main">'; 
     } else {
       echo '<td class="main" width="70%">'; 
     }
     echo '<input type="hidden" name="'.$this->formname.'" value="'.$this->front_title.'">';
    if($this->fixed_option == '0'){

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
      echo '<span id="prompt_'.$this->formname.'"><font color="#FF0000"><b>';
      if(isset($option_error_array['prompt_'.$this->formname])){

        echo '&nbsp;'.$option_error_array['prompt_'.$this->formname];

      }
      echo '</b></font></span>';
    }
     echo '<br>';
     echo '<span id="error_'.$this->formname.'" class="shipping_error"><font color="red">';
     if (isset($option_error_array[$this->formname])) {
       echo $option_error_array[$this->formname]; 
     }
     echo '</font></span>'; 
     echo '</td>'; 
  }
  static public function prepareForm($item_id = NULL)
  {
    return $formString;
  }
  
  function check(&$option_error_array)
  {
    global $_POST;
    global $weight_count;
    global $weight_limit;
    $prompt_str = '<a style="color:#CC0033" href="'.tep_href_link('open.php','products='.urlencode($product_info['products_name']), 'SSL').'">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</a>'; 
    $weight_prompt_str = '<br>'.PRODUCTS_WEIGHT_ERROR_THREE . $weight_limit . PRODUCTS_WEIGHT_ERROR_FOUR;
  //products shipping fee  
  $fixed_option_list_array = array();
  $fixed_option_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where status='0' and fixed_option!='0'");
  while($fixed_option_array = tep_db_fetch_array($fixed_option_query)){

    $fixed_option_list_array[$fixed_option_array['fixed_option']] = $fixed_option_array['name_flag'];
  }
  tep_db_free_result($fixed_option_query);

  if(isset($_POST['op_'.$fixed_option_list_array[3]])){

    $country_city_search_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_CITY ." where name='".$_POST['op_'.$fixed_option_list_array[3]]."' and status='0'");
    $country_city_search_array = tep_db_fetch_array($country_city_search_query);
    tep_db_free_result($country_city_search_query);
    $weight_limit = $country_city_search_array['weight_limit'];

    if($weight_count > $weight_limit){

      $option_error_array['prompt_'.$fixed_option_list_array[3]] = $prompt_str;
      $option_error_array[$fixed_option_list_array[3]] = PRODUCTS_WEIGHT_ERROR_ONE . $_POST['op_'.$fixed_option_list_array[3]] . PRODUCTS_WEIGHT_ERROR_TWO.$weight_prompt_str;
      return  true;
    }
  }elseif(isset($_POST['op_'.$fixed_option_list_array[2]])){
    $country_area_search_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_AREA ." where name='".$_POST['op_'.$fixed_option_list_array[2]]."' and status='0'");
    $country_area_search_array = tep_db_fetch_array($country_area_search_query);
    tep_db_free_result($country_area_search_query);
    $weight_limit = $country_area_search_array['weight_limit'];

    if($weight_count > $weight_limit){

      $option_error_array['prompt_'.$fixed_option_list_array[2]] = $prompt_str;
      $option_error_array[$fixed_option_list_array[2]] = PRODUCTS_WEIGHT_ERROR_ONE . $_POST['op_'.$fixed_option_list_array[2]] . PRODUCTS_WEIGHT_ERROR_TWO.$weight_prompt_str;
      return true;
    }
  }elseif(isset($_POST['op_'.$fixed_option_list_array[1]])){

    $country_fee_search_query = tep_db_query("select weight_limit from ". TABLE_COUNTRY_FEE ." where name='".$_POST['op_'.$fixed_option_list_array[1]]."' and status='0'");
    $country_fee_search_array = tep_db_fetch_array($country_fee_search_query);
    tep_db_free_result($country_fee_search_query);
    $weight_limit = $country_fee_search_array['weight_limit'];

    if($weight_count > $weight_limit){

      $option_error_array['prompt_'.$fixed_option_list_array[1]] = $prompt_str;
      $option_error_array[$fixed_option_list_array[1]] = PRODUCTS_WEIGHT_ERROR_ONE . $_POST['op_'.$fixed_option_list_array[1]] . PRODUCTS_WEIGHT_ERROR_TWO.$weight_prompt_str;
      return true;
    }
  }
        
    return false; 
  }
}

