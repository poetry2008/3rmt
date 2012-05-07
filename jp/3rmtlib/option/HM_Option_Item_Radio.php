<?php
global $language;
require_once "HM_Option_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/option/HM_Option_Item_Radio.php';
class HM_Option_Item_Radio extends HM_Option_Item_Basic
{
  var $has_radio_comment = true; 
  var $has_default = true;
  var $has_radio = true;

  function render($option_error_array, $pre_item_str = '', $cart_obj = '', $ptype = false)
  {
    $sp_pos = strpos($_SERVER['PHP_SELF'], 'checkout_option.php');
    $ac_pos = strpos($_SERVER['PHP_SELF'], 'admin/create_order.php');
    
    if (strlen($this->front_title)) {
       if ($ptype) {
         echo '<td class="preorder_option_name">';      
       } else {
         echo '<td class="option_name">'; 
       }
       echo $this->front_title;
       echo '</td>';
     }
     $is_default = 0;
     $is_post = 0;
     
     if (strlen($this->default_radio)) {
       $is_default = 1;
     }
     
     

     if ($cart_obj != '') {
       $pre_item_tmp_str = substr($pre_item_str, 0, -1);
       if (isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && (!isset($_POST[$pre_item_str.'op_'.$this->formname]))) {
         $default_value = $cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]; 
       } else {
         if (isset($_POST[$pre_item_str.'op_'.$this->formname])) {
           $default_value = $_POST[$pre_item_str.'op_'.$this->formname]; 
           $is_post = 1;
         } else {
           $default_value = ''; 
         }
       }
     } else {
       if (isset($_POST[$pre_item_str.'op_'.$this->formname])) {
         $default_value = $_POST[$pre_item_str.'op_'.$this->formname]; 
         $is_post = 1;
       } else {
         $default_value = ''; 
       }
     }
     echo '<td>';
    
     if ($sp_pos !== false) {
         if (!isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && !isset($_POST[$pre_item_str.'op_'.$this->formname])) {
           $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_ORDERS." o where opa.orders_id = o.orders_id and o.customers_id = '".(int)$_SESSION['customer_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' order by opa.orders_id desc limit 1"); 
           $o_attributes_res = tep_db_fetch_array($o_attributes_raw); 
           if ($o_attributes_res) {
             $old_option_info = @unserialize(stripslashes($o_attributes_res['option_info']));  
             if (!empty($this->radio_image)) {
               foreach ($this->radio_image as $cr_key => $cr_value) {
                 if (trim($cr_value['title']) == trim($old_option_info['value'])) {
                   $old_sel_single = true; 
                   $default_value = $old_option_info['value'];
                   break;
                 }
               }
             }
           }
         }
       
     }
     echo '<div class="option_radio_list">';
     if ($is_default == 1) {
       echo '<div class="option_radio_list">';
       if (!empty($_POST[$pre_item_str.'op_'.$this->formname])) {
         echo '<div class="option_hide_border">'; 
         echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$this->default_radio.'</a>';
         echo '</div>'; 
       } else {
         if ($cart_obj != '') {
           $pre_item_tmp_str = substr($pre_item_str, 0, -1);
           if (isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && (!isset($_POST[$pre_item_str.'op_'.$this->formname]))) {
             echo '<div class="option_hide_border">'; 
             echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$this->default_radio.'</a>';
             echo '</div>'; 
           } else {
             echo '<div class="option_show_border">'; 
             echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$this->default_radio.'</a>';
             echo '</div>'; 
           }
         } else {
           echo '<div class="option_show_border">'; 
           echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$this->default_radio.'</a>';
           echo '</div>'; 
         }
         
       }
       echo '</div>';
     }
     $i = 0; 
     if (!empty($this->radio_image)) {
       foreach ($this->radio_image as $key => $value) {
         echo '<div class="option_radio_list">';  
         if ($is_default == 1) {
           if ($is_post == 1) {
             if (trim($value['title']) == trim($default_value)) {
               echo '<div class="option_show_border">'; 
               echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
               echo '</div>'; 
             } else {
               echo '<div class="option_hide_border">'; 
               echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
               echo '</div>'; 
             }
           } else {
             if ($cart_obj != '') {
               $pre_item_tmp_str = substr($pre_item_str, 0, -1);
               if (isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && (!isset($_POST[$pre_item_str.'op_'.$this->formname]))) {
                 if (trim($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) == trim($value['title'])) {
                   echo '<div class="option_show_border">'; 
                   echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                   echo '</div>'; 
                 } else {
                   echo '<div class="option_hide_border">'; 
                   echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                   echo '</div>'; 
                 }
               } else {
                 echo '<div class="option_hide_border">'; 
                 echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                 echo '</div>'; 
               }
             } else {
               echo '<div class="option_hide_border">'; 
               echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
               echo '</div>'; 
             }
             
           }
         } else {
           if ($is_post == 1) {
             if (trim($value['title']) == trim($default_value)) {
               echo '<div class="option_show_border">'; 
               echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
               echo '</div>'; 
             } else {
               echo '<div class="option_hide_border">'; 
               echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
               echo '</div>'; 
             }
           } else {
             if ($cart_obj != '') {
               $pre_item_tmp_str = substr($pre_item_str, 0, -1);
               if (isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && (!isset($_POST[$pre_item_str.'op_'.$this->formname]))) {
                 if (trim($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) == trim($value['title'])) {
                   echo '<div class="option_show_border">'; 
                   echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                   echo '</div>'; 
                 } else {
                   echo '<div class="option_hide_border">'; 
                   echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                   echo '</div>'; 
                 }
               } else {
                 if (isset($_POST[$pre_item_str.'op_'.$this->formname])) {
                   if (trim($_POST[$pre_item_str.'op_'.$this->formname]) == trim($value['title'])) {
                     echo '<div class="option_show_border">'; 
                     echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                     echo '</div>'; 
                   } else {
                     echo '<div class="option_hide_border">'; 
                     echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                     echo '</div>'; 
                   }
                 } else {
                   if (isset($old_sel_single)) {
                     if (trim($value['title']) == trim($default_value)) {
                       echo '<div class="option_show_border">'; 
                       echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                       echo '</div>'; 
                     } else {
                       echo '<div class="option_hide_border">'; 
                       echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                       echo '</div>'; 
                     }
                   } else {
                     if ($i == 0) {
                       $default_i_value = $value['title']; 
                       echo '<div class="option_show_border">'; 
                       echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                       echo '</div>'; 
                     } else {
                       echo '<div class="option_hide_border">'; 
                       echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                       echo '</div>'; 
                     }
                   }
                 }
               }
             } else {
               if ($i == 0) {
                 $default_i_value = $value['title']; 
                 echo '<div class="option_show_border">'; 
                 echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                 echo '</div>'; 
               } else {
                 echo '<div class="option_hide_border">'; 
                 echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \''.$value['title'].'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\');">'.$value['title'].'</a>';
                 echo '</div>'; 
               }
             }
           }
         }
         
         echo '<span class="option_radio_img">';
         if (!empty($value['images'])) {
           foreach ($value['images'] as $p_key => $p_value) {
             if (file_exists(DIR_FS_CATALOG.'default_images/option_image/'.$p_value) && $p_value != '') {
               echo '<img src="default_images/option_image/'.$p_value.'" alt="pic">'; 
             } else if (file_exists(DIR_FS_CATALOG.'upload_images/0/option_image/'.$p_value) && $p_value != '') {
               echo '<img src="upload_images/0/option_image/'.$p_value.'" alt="pic">'; 
             }
           }
         }
          
         echo '</span>';
         
         if (($sp_pos !== false) || ($ac_pos !== false)) {
           if ($value['money'] != '0') {
             echo '<span class="option_money">'.$value['money'].OPTION_ITEM_MONEY_UNIT.'</span>'; 
           }
         }
         
         echo '</div>';
         $i++; 
       }
     }
     echo '</div>';
     if ($this->racomment) {
       echo $this->racomment; 
     }
     
     echo '<span>'; 
     echo '<input id="'.$pre_item_str.'h_'.$this->formname.'" type="hidden" name="'.$pre_item_str.'op_'.$this->formname.'" value="'.(isset($default_i_value)?$default_i_value:$default_value).'">'; 
     echo '</span>'; 
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
    global $_POST;
    $input_text_str = $_POST[$pre_error_str.'op_'.$this->formname];
    $input_text_str = str_replace(' ', '', $input_text_str); 
    $input_text_str = str_replace('　', '', $input_text_str); 
    if ($input_text_str == '') {
      $option_error_array[$pre_error_str.$this->formname] = ERROR_OPTION_ITEM_RADIO_TEXT_NULL; 
      return true; 
    }
    return false; 
  }
}

