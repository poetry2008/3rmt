<?php
global $language;
require_once "HM_Option_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/option/HM_Option_Item_Select.php';
class HM_Option_Item_Select extends HM_Option_Item_Basic
{
  var $hasSelect = true; 
  var $has_select_default = true; 
  var $hasComment = true;

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
     $sp_pos = strpos($_SERVER['PHP_SELF'], 'checkout_option.php');
     $ac_pos = strpos($_SERVER['PHP_SELF'], 'admin/create_order.php');
     $ed_pos = strpos($_SERVER['PHP_SELF'], 'admin/edit_orders.php');
     $pro_pos = strpos($_SERVER['PHP_SELF'], 'product_info.php');
     $cp_pos = strpos($_SERVER['PHP_SELF'], 'change_preorder.php');
     $back_pos = strpos($_SERVER['PHP_SELF'], 'admin/');
     $pre_ac_pos = strpos($_SERVER['PHP_SELF'], 'admin/create_preorder.php');
     
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
         if ($_SESSION['guestchk'] != '1') {
           if (!isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && !isset($_POST[$pre_item_str.'op_'.$this->formname])) {
             $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where op.orders_id = o.orders_id and o.customers_id = '".(int)$_SESSION['customer_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' and op.orders_products_id = opa.orders_products_id and op.products_id = '".(int)$pre_item_str."' and o.is_gray != '1' and o.is_guest = '0' order by opa.orders_id desc limit 1"); 
             $o_attributes_res = tep_db_fetch_array($o_attributes_raw); 
             if ($o_attributes_res) {
               $old_option_info = @unserialize(stripslashes($o_attributes_res['option_info']));  
               $default_value = $old_option_info['value']; 
             }
           }
         }
       
     }
     
     if ($ac_pos !== false) {
         if (($_GET['action'] == 'add_product') && isset($_GET['Customer_mail']) && isset($_GET['site_id']) && !isset($_POST[$pre_item_str.'op_'.$this->formname])) {
            $customer_info_raw = tep_db_query("select customers_id, customers_guest_chk from ".TABLE_CUSTOMERS." where customers_email_address = '".$_GET['Customer_mail']."' and site_id = '".$_GET['site_id']."'");   
            $customer_info = tep_db_fetch_array($customer_info_raw); 
            if ($customer_info) {
              if ($customer_info['customers_guest_chk'] == '0') {
                $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where op.orders_id = o.orders_id and o.customers_id = '".(int)$customer_info['customers_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' and op.orders_products_id = opa.orders_products_id and op.products_id = '".(int)$_POST['add_product_products_id']."' and o.is_gray != '1' and o.is_guest = '0' order by opa.orders_id desc limit 1"); 
                $o_attributes_res = tep_db_fetch_array($o_attributes_raw); 
                if ($o_attributes_res) {
                  $old_option_info = @unserialize(stripslashes($o_attributes_res['option_info']));  
                  $default_value = $old_option_info['value']; 
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
              $customer_info_raw = tep_db_query("select customers_guest_chk from ".TABLE_CUSTOMERS." where customers_id = '".$origin_order['customers_id']."'");   
              $customer_info = tep_db_fetch_array($customer_info_raw); 
              if ($customer_info) {
                if ($customer_info['customers_guest_chk'] == '0') {
                  $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where op.orders_id = o.orders_id and o.customers_id = '".(int)$origin_order['customers_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' and op.orders_products_id = opa.orders_products_id and op.products_id = '".(int)$_POST['add_product_products_id']."' and o.is_gray != '1' and o.is_guest = '0' order by opa.orders_id desc limit 1"); 
                  $o_attributes_res = tep_db_fetch_array($o_attributes_raw); 
                  if ($o_attributes_res) {
                    $old_option_info = @unserialize(stripslashes($o_attributes_res['option_info']));  
                    $default_value = $old_option_info['value']; 
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
         } else {
           $preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where check_preorder_str = '".$_GET['pid']."' and site_id = '".SITE_ID."' and is_active = '1'"); 
           $preorder_res = tep_db_fetch_array($preorder_raw); 
           if ($preorder_res) {
             $customers_info_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".$preorder_res['customers_id']."'"); 
             $customers_info = tep_db_fetch_array($customers_info_raw); 
             if ($customers_info) {
               if ($customers_info['customers_guest_chk'] == '0') {
                 $preorder_product_raw = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$preorder_res['orders_id']."'"); 
                 $preorder_product_res = tep_db_fetch_array($preorder_product_raw); 
                 if ($preorder_product_res) {
                   $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where op.orders_id = o.orders_id and o.customers_id = '".(int)$preorder_res['customers_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' and op.orders_products_id = opa.orders_products_id and op.products_id = '".(int)$preorder_product_res['products_id']."' and o.is_gray != '1' and o.is_guest = '0' order by opa.orders_id desc limit 1"); 
                   $o_attributes_res = tep_db_fetch_array($o_attributes_raw); 
                   if ($o_attributes_res) {
                     $old_option_info = @unserialize(stripslashes($o_attributes_res['option_info']));  
                     $default_value = $old_option_info['value']; 
                   }
                 }
               }
             }
           }
         }
       }
     }
     
     if ($pre_ac_pos !== false) {
       if (($_GET['action'] == 'add_product') && isset($_GET['Customer_mail']) && isset($_GET['site_id']) && !isset($_POST[$pre_item_str.'op_'.$this->formname])) {
          $customer_info_raw = tep_db_query("select customers_id, customers_guest_chk from ".TABLE_CUSTOMERS." where customers_email_address = '".$_GET['Customer_mail']."' and site_id = '".$_GET['site_id']."'");   
          $customer_info = tep_db_fetch_array($customer_info_raw); 
          if ($customer_info) {
            if ($customer_info['customers_guest_chk'] == '0') {
              $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_PREORDERS." o, ".TABLE_PREORDERS_PRODUCTS." op where op.orders_id = o.orders_id and o.customers_id = '".(int)$customer_info['customers_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' and op.orders_products_id = opa.orders_products_id and op.products_id = '".(int)$_POST['add_product_products_id']."' and o.is_gray != '1' and o.is_guest = '0' order by opa.orders_id desc limit 1"); 
              $o_attributes_res = tep_db_fetch_array($o_attributes_raw); 
              if ($o_attributes_res) {
                $old_option_info = @unserialize(stripslashes($o_attributes_res['option_info']));  
                $default_value = $old_option_info['value']; 
              }
            }
          }
       }
     }
     
     $default_value = stripslashes($default_value); 
     echo '<td>'; 
     echo '<div class="option_info_text">'; 
     if (!empty($this->se_option)) {
       $i = 1; 
       echo '<select name="'.$pre_item_str.'op_'.$this->formname.'">'; 
       if (strlen($this->sedefault)) {
         echo '<option value="">'.$this->sedefault.'</option>'; 
       }
       foreach ($this->se_option as $key => $value) {
         echo '<option value="'.$value.'"'.(($default_value == stripslashes($value))?'selected ':'').'>'.stripslashes($value).'</option>'; 
         $i++; 
       }
       echo '</select>';
       
       if (strlen($this->sedefault)) {
         if ($back_pos !== false) {
           if (!isset($option_error_array[$pre_item_str.$this->formname])) {
             if (isset($_POST['cstep'])) {
               if ($pro_pos !== false) {
                 echo '<font color="#ff0000" style="float:left; line-height:20px;">&nbsp;'.OPTION_ITEM_TEXT_REQUIRE.'</font>'; 
               } else {
                 echo '<font color="#ff0000">&nbsp;'.OPTION_ITEM_TEXT_REQUIRE.'</font>'; 
               }
             }
           } 
         } else {
           if (!isset($option_error_array[$pre_item_str.$this->formname])) {
             if ($_SERVER['REQUEST_METHOD'] != 'POST') {
               echo '<font color="#ff0000" style="float:left; line-height:20px;">&nbsp;'.OPTION_ITEM_TEXT_REQUIRE.'</font>'; 
             }
           }
         }
       }
     }
     
     echo '</div>'; 
     if ($this->secomment) {
       echo $this->secomment; 
     }
     echo '<span id="'.$pre_item_str.'error_'.$this->formname.'" class="option_error">';
     if (isset($option_error_array[$pre_item_str.$this->formname])) {
       echo $option_error_array[$pre_item_str.$this->formname]; 
     }
     echo '</span>'; 
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

