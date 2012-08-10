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
    $ed_pos = strpos($_SERVER['PHP_SELF'], 'admin/edit_orders.php');
    $pro_pos = strpos($_SERVER['PHP_SELF'], 'product_info.php');
    $ped_pos = strpos($_SERVER['PHP_SELF'], 'admin/edit_new_preorders.php');
    $cp_pos = strpos($_SERVER['PHP_SELF'], 'change_preorder.php');
    
    $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>");

    if (strlen($this->front_title)) {
       if ($ptype) {
         echo '<td class="preorder_option_name">';      
       } else {
         echo '<td class="option_name">'; 
       }
       echo $this->front_title.':';
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
     echo '<td valign="top">';
    
     if ($sp_pos !== false) {
         if (!isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && !isset($_POST[$pre_item_str.'op_'.$this->formname])) {
           $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_ORDERS." o where opa.orders_id = o.orders_id and o.customers_id = '".(int)$_SESSION['customer_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' order by opa.orders_id desc limit 1"); 
           $o_attributes_res = tep_db_fetch_array($o_attributes_raw); 
           if ($o_attributes_res) {
             $old_option_info = @unserialize(stripslashes($o_attributes_res['option_info']));  
             if (!empty($this->radio_image)) {
               foreach ($this->radio_image as $cr_key => $cr_value) {
                 if (trim(str_replace($replace_arr, '', nl2br($cr_value['title']))) == trim(str_replace($replace_arr, '', nl2br($old_option_info['value'])))) {
                   $old_sel_single = true; 
                   $default_value = new_nl2br($cr_value['title']);
                   break;
                 }
               }
             }
           }
         }
       
     }
     
     if ($ac_pos !== false) {
         if (($_GET['action'] == 'add_product') && isset($_GET['Customer_mail']) && isset($_GET['site_id']) && !isset($_POST[$pre_item_str.'op_'.$this->formname])) {
            $customer_info_raw = tep_db_query("select customers_id from ".TABLE_CUSTOMERS." where customers_email_address = '".$_GET['Customer_mail']."' and site_id = '".$_GET['site_id']."'");   
            $customer_info = tep_db_fetch_array($customer_info_raw); 
            if ($customer_info) {
              $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_ORDERS." o where opa.orders_id = o.orders_id and o.customers_id = '".(int)$customer_info['customers_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' order by opa.orders_id desc limit 1"); 
              $o_attributes_res = tep_db_fetch_array($o_attributes_raw); 
              if ($o_attributes_res) {
                $old_option_info = @unserialize(stripslashes($o_attributes_res['option_info']));  
                if (!empty($this->radio_image)) {
                  foreach ($this->radio_image as $car_key => $car_value) {
                     if (trim(str_replace($replace_arr, '', nl2br($car_value['title']))) == trim(str_replace($replace_arr, '', nl2br($old_option_info['value'])))) {
                       $a_old_single = true;
                       $default_value = new_nl2br($car_value['title']);
                       break;
                     }
                  }
                }
              }
            }
         }
     }
     
     if ($ed_pos !== false) {
         if (($_GET['action'] == 'add_product') && !isset($_POST[$pre_item_str.'op_'.$this->formname])) {
            $origin_order_raw = tep_db_query("select customers_id, site_id from ".TABLE_ORDERS." where orders_id = '".$_GET['oID']."'"); 
            $origin_order = tep_db_fetch_array($origin_order_raw);
            if ($origin_order) {
                $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_ORDERS." o where opa.orders_id = o.orders_id and o.customers_id = '".(int)$origin_order['customers_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' order by opa.orders_id desc limit 1"); 
                $o_attributes_res = tep_db_fetch_array($o_attributes_raw); 
                if ($o_attributes_res) {
                  $old_option_info = @unserialize(stripslashes($o_attributes_res['option_info']));  
                  if (!empty($this->radio_image)) {
                    foreach ($this->radio_image as $cer_key => $cer_value) {
                       if (trim(str_replace($replace_arr, '', nl2br($cer_value['title']))) == trim(str_replace($replace_arr, '', nl2br($old_option_info['value'])))) {
                         $a_old_single = true;
                         $default_value = new_nl2br($cer_value['title']);
                         break;
                       }
                    }
                  }
                }
            }
         }
     }
     if ($cp_pos !== false) {
         if ($_SERVER['REQUEST_METHOD'] == 'GET') {
         if (isset($_SESSION['preorder_information'][$pre_item_str.'op_'.$this->formname])) {
           $default_value = $_SESSION['preorder_information'][$pre_item_str.'op_'.$this->formname]; 
           $a_old_single = true;
         }
         }
     }
     $default_value = stripslashes($default_value);
     
     echo '<div class="option_product_radio_list">';
     if ($is_default == 1) {
       echo '<div class="option_product_default_radio">';
       if (!empty($_POST[$pre_item_str.'op_'.$this->formname])) {
         echo '<div class="option_hide_border">'; 
         if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
           echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
         } else {
           echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
         }
         echo '<div class="option_conent">'; 
         echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'1\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \'0\');"><span>'.stripslashes($this->default_radio).'</span></a>';
         echo '</div>'; 
         echo '</div>'; 
       } else {
         if ($cart_obj != '') {
           $pre_item_tmp_str = substr($pre_item_str, 0, -1);
           if (isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && (!isset($_POST[$pre_item_str.'op_'.$this->formname]))) {
             echo '<div class="option_hide_border">'; 
             if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
               echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
             } else {
               echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
             }
             echo '<div class="option_conent">'; 
             echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'1\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \'0\');"><span>'.stripslashes($this->default_radio).'</span></a>';
             echo '</div>'; 
             echo '</div>'; 
           } else {
             if (isset($a_old_single) || isset($old_sel_single)) {
               if (trim($default_value) == '') {
                 echo '<div class="option_show_border">'; 
               } else {
                 echo '<div class="option_hide_border">'; 
               }
             } else {
               echo '<div class="option_show_border">'; 
             }
             
             if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
               echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
             } else {
               echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
             }
             echo '<div class="option_conent">'; 
             echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'1\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \'0\');"><span>'.stripslashes($this->default_radio).'</span></a>';
             echo '</div>'; 
             echo '</div>'; 
           }
         } else {
           if (isset($a_old_single) || isset($old_sel_single)) {
             //if (trim(nl2br($default_value)) == trim(nl2br(stripslashes($this->default_radio)))) {
             if (trim($default_value) == '') {
               echo '<div class="option_show_border">'; 
             } else {
               echo '<div class="option_hide_border">'; 
             }
           } else {
             echo '<div class="option_show_border">'; 
           }
           if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
             echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
           } else {
             echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
           }
           echo '<div class="option_conent">'; 
           echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'1\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \'0\');"><span>'.stripslashes($this->default_radio).'</span></a>';
           echo '</div>'; 
           echo '</div>'; 
         }
         
       }
       echo '</div>';
     }
     $i = 0; 
     if (!empty($this->radio_image)) {
       echo '<div class="option_product_radio_img_list">';  
       foreach ($this->radio_image as $key => $value) {
         echo '<div class="option_product_single_radio">';  
         if ($is_default == 1) {
           if ($is_post == 1) {
             if (trim(str_replace($replace_arr, '', nl2br(stripslashes($value['title'])))) == trim(str_replace($replace_arr, '', nl2br($default_value)))) {
               echo '<div class="option_show_border">'; 
               if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                 echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
               } else {
                 echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
               }
               echo '<div class="option_conent">'; 
               echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
               echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
             } else {
               echo '<div class="option_hide_border">'; 
               if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                 echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
               } else {
                 echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
               }
               echo '<div class="option_conent">'; 
               echo '<a href="javascript:void(0);" onclick="select_item_radio(this,\'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">'; 
               echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
             }
           } else {
             if ($cart_obj != '') {
               $pre_item_tmp_str = substr($pre_item_str, 0, -1);
               if (isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && (!isset($_POST[$pre_item_str.'op_'.$this->formname]))) {
                 if (trim(str_replace($replace_arr, '', nl2br($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]))) == trim(str_replace($replace_arr, '',nl2br(stripslashes($value['title']))))) {
                   echo '<div class="option_show_border">'; 
                   if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                     echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                   } else {
                     echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                   }
                   echo '<div class="option_conent">'; 
                   echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                   echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
                 } else {
                   echo '<div class="option_hide_border">'; 
                   if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                     echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                   } else {
                     echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                   }
                   echo '<div class="option_conent">'; 
                   echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                   echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
                 }
               } else {
                 //echo '<div class="option_hide_border">'; 
                 if (trim(str_replace($replace_arr, '',nl2br($default_value))) == trim(str_replace($replace_arr, '',nl2br(stripslashes($value['title']))))) {
                   echo '<div class="option_show_border">'; 
                 } else {
                   echo '<div class="option_hide_border">'; 
                 }
                 if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                   echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                 } else {
                   echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                 }
                 echo '<div class="option_conent">'; 
                 echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                 echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
               }
             } else {
               if (isset($a_old_single)) {
                 if (trim(str_replace($replace_arr, '', nl2br($default_value))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($value['title']))))) {
                   echo '<div class="option_show_border">'; 
                 } else {
                   echo '<div class="option_hide_border">'; 
                 }
               } else {
                 echo '<div class="option_hide_border">'; 
               }
               if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                 echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
               } else {
                 echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
               }
               echo '<div class="option_conent">'; 
               echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
               echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
             }
             
           }
         } else {
           if ($is_post == 1) {
             if (trim(str_replace($replace_arr, '', nl2br(stripslashes($value['title'])))) == trim(str_replace($replace_arr, '', nl2br($default_value)))) {
               echo '<div class="option_show_border">'; 
               if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                 echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
               } else {
                 echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
               }
               echo '<div class="option_conent">'; 
               echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
               echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
             } else {
               echo '<div class="option_hide_border">'; 
               if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                 echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
               } else {
                 echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
               }
               echo '<div class="option_conent">'; 
               echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
               echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
             }
           } else {
             if ($cart_obj != '') {
               $pre_item_tmp_str = substr($pre_item_str, 0, -1);
               if (isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && (!isset($_POST[$pre_item_str.'op_'.$this->formname]))) {
                 if (trim(str_replace($replace_arr, '', nl2br($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($value['title']))))) {
                   echo '<div class="option_show_border">'; 
                   if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                     echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                   } else {
                     echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                   }
                   echo '<div class="option_conent">'; 
                   echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                   echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
                 } else {
                   echo '<div class="option_hide_border">'; 
                   if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                     echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                   } else {
                     echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                   }
                   echo '<div class="option_conent">'; 
                   echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                   echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
                 }
               } else {
                 if (isset($_POST[$pre_item_str.'op_'.$this->formname])) {
                   if (trim(str_replace($replace_arr, '', nl2br($_POST[$pre_item_str.'op_'.$this->formname]))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($value['title']))))) {
                     echo '<div class="option_show_border">'; 
                     if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                       echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                     } else {
                       echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                     }
                     echo '<div class="option_conent">'; 
                     echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                     echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
                   } else {
                     echo '<div class="option_hide_border">'; 
                     if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                       echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                     } else {
                       echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                     }
                     echo '<div class="option_conent">'; 
                     echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                     echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
                   }
                 } else {
                   if (isset($old_sel_single)) {
                     if (trim(str_replace($replace_arr, '', nl2br(stripslashes($value['title'])))) == trim(str_replace($replace_arr, '', nl2br($default_value)))) {
                       echo '<div class="option_show_border">'; 
                       if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                         echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                       } else {
                         echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                       }
                       echo '<div class="option_conent">'; 
                       echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                       echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
                     } else {
                       echo '<div class="option_hide_border">'; 
                       if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                         echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                       } else {
                         echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                       }
                       echo '<div class="option_conent">'; 
                       echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                       echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
                     }
                   } else {
                     if ($i == 0) {
                       $default_i_value = new_nl2br($value['title']); 
                       echo '<div class="option_show_border">'; 
                       if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                         echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                       } else {
                         echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                       }
                       echo '<div class="option_conent">'; 
                       echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                       echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
                     } else {
                       echo '<div class="option_hide_border">'; 
                       if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                         echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                       } else {
                         echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                       }
                       echo '<div class="option_conent">'; 
                       echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                       echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
                     }
                   }
                 }
               }
             } else {
               if ($i == 0) {
                 if (isset($a_old_single)) {
                   if (trim(str_replace($replace_arr, '', nl2br($default_value))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($value['title']))))) {
                     echo '<div class="option_show_border">'; 
                   } else {
                     echo '<div class="option_hide_border">'; 
                   }
                 } else {
                   echo '<div class="option_show_border">'; 
                   $default_i_value = new_nl2br($value['title']); 
                 }
                 if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                   echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                 } else {
                   echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                 }
                 echo '<div class="option_conent">'; 
                 echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                 echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
               } else {
                 if (isset($a_old_single)) {
                   if (trim(str_replace($replace_arr, '', nl2br($default_value))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($value['title']))))) {
                     echo '<div class="option_show_border">'; 
                   } else {
                     echo '<div class="option_hide_border">'; 
                   }
                 } else {
                   echo '<div class="option_hide_border">'; 
                 }
                 if (file_exists(DIR_FS_CATALOG.'default_images/design/checkmark.png')) {
                   echo '<div class="option_checkmark"><img src="default_images/design/checkmark.png" alt=""></div>'; 
                 } else {
                   echo '<div class="option_checkmark"><img src="upload_images/0/design/checkmark.png" alt=""></div>'; 
                 }
                 echo '<div class="option_conent">'; 
                 echo '<a href="javascript:void(0);" onclick="select_item_radio(this, \'\', \''.$pre_item_str.'h_'.$this->formname.'\', \''.$pre_item_str.'op_'.$this->formname.'\', \''.number_format($value['money']).'\');">';
                 echo '<span>'.new_nl2br(stripslashes($value['title'])).'</span>';
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
         
         /*
         if ($sp_pos !== false) {
           if ($value['money'] != '0') {
             echo '<span class="option_money">'.$value['money'].OPTION_ITEM_MONEY_UNIT.'</span>'; 
           }
         }
         */ 
         echo '</div>';
         echo '</a>';
         echo '</div>';
         echo '</div>';
         $i++; 
       }
         echo '</div>';
     }
     echo '</div>';
     if ($this->racomment) {
       echo $this->racomment; 
     }
     
     echo '<span>'; 
     echo '<input id="'.$pre_item_str.'h_'.$this->formname.'" type="hidden" name="'.$pre_item_str.'op_'.$this->formname.'" value="'.(isset($default_i_value)?stripslashes($default_i_value):$default_value).'">'; 
     echo '</span>'; 
     echo '<span id="'.$pre_item_str.'error_'.$this->formname.'" class="option_error">'; 
     if (isset($option_error_array[$pre_item_str.$this->formname])) {
       if ($this->racomment) {
         echo '<br>'; 
       }
       echo $option_error_array[$pre_item_str.$this->formname]; 
     }
     echo '</span>'; 
     if ($pro_pos !== false) {
       $default_price = 0; 
       $d_tmp_value = (isset($default_i_value)?$default_i_value:$default_value);
       if (!$d_tmp_value == '') {
         if (!empty($this->radio_image)) {
           foreach ($this->radio_image as $dp_key => $dp_value) {
             if (trim(str_replace($replace_arr, '', nl2br($dp_value['title']))) == str_replace($replace_arr, '', nl2br($d_tmp_value))) {
               $default_price = $dp_value['money'];
               break;
             }
           } 
         }
       } 
       echo '<input id="tp1_'.$pre_item_str.$this->formname.'" type="hidden" name="tp1_'.$pre_item_str.$this->formname.'" value="'.number_format($default_price).'">'; 
     }
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

