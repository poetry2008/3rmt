<?php
global $language;
require_once "HM_Option_Item_Basic.php";
require_once DIR_WS_LANGUAGES . $language . '/option/HM_Option_Item_Textarea.php';
class HM_Option_Item_Textarea extends HM_Option_Item_Basic
{
  var $hasRequire = true;
  var $has_text_default = true; 
  var $has_text_comment = true; 
  var $has_text_line = true;
  var $has_text_check_type = true;
  var $has_text_max_num = true; 

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
     $back_pos = strpos($_SERVER['PHP_SELF'], 'admin/');
     $cp_pos = strpos($_SERVER['PHP_SELF'], 'change_preorder.php');
     
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
     
     $default_value = ''; 
       if ($cart_obj != '') {
         $pre_item_tmp_str = substr($pre_item_str, 0, -1); 
         if (isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && !isset($_POST[$pre_item_str.'op_'.$this->formname])) {
           $default_value = $cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]; 
         } else {
           $default_value = (isset($_POST[$pre_item_str.'op_'.$this->formname])?$_POST[$pre_item_str.'op_'.$this->formname]:$this->itext);
         }
       } else {
         $default_value = (isset($_POST[$pre_item_str.'op_'.$this->formname])?$_POST[$pre_item_str.'op_'.$this->formname]:$this->itext);
       }
       
       if ($sp_pos !== false) {
         if ($_SESSION['guestchk'] != '1') {
           if (!isset($cart_obj->contents[$pre_item_tmp_str]['ck_attributes'][$this->formname]) && !isset($_POST[$pre_item_str.'op_'.$this->formname])) {
             $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where op.orders_id = o.orders_id and o.customers_id = '".(int)$_SESSION['customer_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' and op.orders_products_id = opa.orders_products_id and op.products_id = '".(int)$pre_item_str."' order by opa.orders_id desc limit 1"); 
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
            $customer_info_raw = tep_db_query("select customers_id from ".TABLE_CUSTOMERS." where customers_email_address = '".$_GET['Customer_mail']."' and site_id = '".$_GET['site_id']."'");   
            $customer_info = tep_db_fetch_array($customer_info_raw); 
            if ($customer_info) {
              $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where op.orders_id = o.orders_id and o.customers_id = '".(int)$customer_info['customers_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' and op.orders_products_id = opa.orders_products_id and op.products_id = '".(int)$_POST['add_product_products_id']."' order by opa.orders_id desc limit 1"); 
              $o_attributes_res = tep_db_fetch_array($o_attributes_raw); 
              if ($o_attributes_res) {
                $old_option_info = @unserialize(stripslashes($o_attributes_res['option_info']));  
                $default_value = $old_option_info['value']; 
              }
            }
         }
       }
       
       if ($ed_pos !== false) {
         if (($_GET['action'] == 'add_product') && !isset($_POST[$pre_item_str.'op_'.$this->formname])) {
            $origin_order_raw = tep_db_query("select customers_id, site_id from ".TABLE_ORDERS." where orders_id = '".$_GET['oID']."'"); 
            $origin_order = tep_db_fetch_array($origin_order_raw);
            if ($origin_order) {
                $o_attributes_raw = tep_db_query("select opa.* from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." opa, ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where op.orders_id = o.orders_id and o.customers_id = '".(int)$origin_order['customers_id']."' and opa.option_group_id = '".$this->group_id."' and opa.option_item_id = '".$this->id."' and op.orders_products_id = opa.orders_products_id and op.products_id = '".(int)$_POST['add_product_products_id']."' order by opa.orders_id desc limit 1"); 
                $o_attributes_res = tep_db_fetch_array($o_attributes_raw); 
                if ($o_attributes_res) {
                  $old_option_info = @unserialize(stripslashes($o_attributes_res['option_info']));  
                  $default_value = $old_option_info['value']; 
                }
            }
         }
       }
    
       if ($cp_pos !== false) {
         if ($_SERVER['REQUEST_METHOD'] == 'GET') {
           if (isset($_SESSION['preorder_information'][$pre_item_str.'op_'.$this->formname])) {
             $default_value = $_SESSION['preorder_information'][$pre_item_str.'op_'.$this->formname]; 
           }
         }
       }
      $default_value = stripslashes($default_value);
      if(NEW_STYLE_WEB===true){
         $style_width = 'style="width:43%"';
      }
      if ($this->iline > 1) {
       echo '<div class="option_info_text">'; 
       if ($this->require == '1') {
         echo '<textarea class="option_input" '.$style_width.'name="'.$pre_item_str.'op_'.$this->formname.'" rows="'.$this->iline.'">'.$default_value.'</textarea>';    
       } else {
         if ($pro_pos !== false) {
           echo '<textarea class="option_input" '.$style_width.' name="'.$pre_item_str.'op_'.$this->formname.'" rows="'.$this->iline.'"  onkeyup="recalc_product_price(this);">'.$default_value.'</textarea>';    
         } else {
           echo '<textarea class="option_input" '.$style_width.' name="'.$pre_item_str.'op_'.$this->formname.'" rows="'.$this->iline.'">'.$default_value.'</textarea>';    
         }
       }
       
       if ($this->require == '1') {
         if ($back_pos !== false) {
           if (!isset($option_error_array[$pre_item_str.$this->formname])) {
             if (isset($_POST['cstep'])) {
               if ($pro_pos !== false) {
                 echo '<font color="#ff0000" style="float:left; line-height: 20px;">&nbsp;'.OPTION_ITEM_TEXT_REQUIRE.'</font>'; 
               } else {
                 echo '<font color="#ff0000" style="float:left">'.OPTION_ITEM_TEXT_REQUIRE.'</font>'; 
               }
             }
           }
         } else {
           if (!isset($option_error_array[$pre_item_str.$this->formname])) {
             if ($_SERVER['REQUEST_METHOD'] != 'POST') {
               echo '<font color="#ff0000" style="float:left; line-height: 20px;">&nbsp;'.OPTION_ITEM_TEXT_REQUIRE.'</font>'; 
             }
           } 
         }
       }
      
       echo '</div>'; 
       echo '<span id="'.$pre_item_str.'error_'.$this->formname.'" class="option_error">';
       if (isset($option_error_array[$pre_item_str.$this->formname])) {
         echo $option_error_array[$pre_item_str.$this->formname]; 
       }
       echo '</span>';
       echo '<div class="option_info_text">'; 
       if ($this->icomment) {
         echo $this->icomment; 
       }
       echo '</div>'; 
     } else {
       echo '<div class="option_info_text">'; 
       if ($this->require == '1') {
           echo '<input class="option_input" '.$style_width.'type="text" name="'.$pre_item_str.'op_'.$this->formname.'" value="'.$default_value.'">'; 
       } else {
         if ($pro_pos !== false) {
           echo '<input class="option_input" '.$style_width.'type="text" name="'.$pre_item_str.'op_'.$this->formname.'" value="'.$default_value.'" onkeyup="recalc_product_price(this);">'; 
         } else {
           echo '<input class="option_input" '.$style_width.'type="text" name="'.$pre_item_str.'op_'.$this->formname.'" value="'.$default_value.'">'; 
         }
       }
       
       if ($this->require == '1') {
         if ($back_pos !== false) {
           if (!isset($option_error_array[$pre_item_str.$this->formname])) {
             if (isset($_POST['cstep'])) { 
               if ($pro_pos !== false) {
                 echo '<font color="#ff0000" style="float:left; line-height: 20px;">&nbsp;'.OPTION_ITEM_TEXT_REQUIRE.'</font>'; 
               } else {
                 echo '<font color="#ff0000" style="float:left">'.OPTION_ITEM_TEXT_REQUIRE.'</font>'; 
               }
             }
           }
         } else {
           if (!isset($option_error_array[$pre_item_str.$this->formname])) {
             if ($_SERVER['REQUEST_METHOD'] != 'POST') {
               echo '<font color="#ff0000" style="float:left; line-height: 20px;">&nbsp;'.OPTION_ITEM_TEXT_REQUIRE.'</font>'; 
             }
           } 
         }
       }
       echo '</div>'; 
       echo '<span id="'.$pre_item_str.'error_'.$this->formname.'" class="option_error">';
       if (isset($option_error_array[$pre_item_str.$this->formname])) {
         echo $option_error_array[$pre_item_str.$this->formname]; 
       }
       echo '</span>';
       echo '<div class="option_info_text">'; 
       if ($this->icomment) {
         echo $this->icomment; 
       }
       echo '</div>';
     }
    
     if ($pro_pos !== false) {
       if ($this->require == '1') {
         echo '<input id="tp1_'.$pre_item_str.$this->formname.'" type="hidden" name="tp1_'.$pre_item_str.$this->formname.'" value="'.number_format($this->s_price).'">'; 
       } else {
         echo '<input id="tp0_'.$pre_item_str.$this->formname.'" type="hidden" name="tp0_'.$pre_item_str.$this->formname.'" value="'.number_format($this->s_price).'">'; 
       }
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
     
     if ($this->require == '1') {
       if ($input_text_str == '') {
         $option_error_array[$pre_error_str.$this->formname] = ERROR_OPTION_ITEM_TEXT_NULL;  
         return true; 
       }
       $input_text_len = mb_strlen($input_text_str, 'UTF-8');
       if ($input_text_len > $this->imax_num) {
         $option_error_array[$pre_error_str.$this->formname] = sprintf(ERROR_OPTION_ITEM_TEXT_NUM_MAX, $this->imax_num);  
         return true; 
       }
     }
    
     if ($input_text_str != '') {
       $item_type_error = false; 
       switch ($this->ictype) {
/* -----------------------------------------------------
   case '1' 是否是片假名    
   case '2' 是否是数字和字母    
   case '3' 是否是字母   
   case '4' 是否是数字   
   case '5' 是否是邮箱   
------------------------------------------------------*/
         case 1;
           $item_type_error = $this->check_character($input_text_str); 
           break;
         case 2;
           if (!preg_match('/^[0-9a-zA-Z]+$/', $input_text_str)) {
             $item_type_error = true; 
           }
           break;
         case 3;
           if (!preg_match('/^[a-zA-Z]+$/', $input_text_str)) {
             $item_type_error = true; 
           }
           break;
         case 4;
           if (!preg_match('/^[0-9]+$/', $input_text_str)) {
             $item_type_error = true; 
           }
           break;
         case 5;
           if (!preg_match('/^[a-zA-Z0-9_\-\.\+]+@([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/', $input_text_str)) {
             $item_type_error = true; 
           }
           break;
         default;
           break;
       }
       
       if ($item_type_error) {
         $option_error_array[$pre_error_str.$this->formname] = ERROR_OPTION_ITEM_TEXT_TYPE_WRONG;  
         return true; 
       }
     }
     return false; 
  }

/* -------------------------------------
    功能: 检查字符串的字符是否在指定字符范围内 
    参数: $c_str(string) 字符串   
    返回值: 是否在指定字符范围内(boolean) 
------------------------------------ */
  function check_character($c_str)
  {
    $character_array = array('ア' , 'ｱ', 'ぁ' , 'ァ', 'ｧ' ,'あ', 'イ' , 'ｲ' , 'ぃ' ,
        'ィ' , 'ｨ' , 'い', 'ウ' , 'ｳ' , 'ぅ' , 'ゥ' , 'ｩ' ,'う', 'エ' , 'ｴ' , 'ぇ'
        , 'ェ' , 'ｪ' ,'え', 'オ' , 'ｵ' , 'ぉ' , 'ォ' , 'ｫ' , 'お', 'カ', 'ｶ', 'ヵ' ,
        'か', 'キ' , 'ｷ' ,'き', 'ク' , 'ｸ' , 'く', 'ケ', 'ｹ', 'ヶ' ,'け', 'コ',
        'ｺ','こ', 'サ', 'ｻ','さ', 'シ' , 'ｼ','し', 'ス', 'ｽ','す', 'セ', 'ｾ','せ',
        'ソ', 'ｿ','そ', 'タ', 'ﾀ','た', 'チ' , 'ﾁ' , 'ち', 'ツ', 'ﾂ', 'っ' , 'ッ' ,
        'ｯ','つ', 'テ', 'ﾃ','て', 'ト' , 'ﾄ','と', 'ナ', 'ﾅ','な', 'ニ', 'ﾆ','に',
        'ヌ', 'ﾇ','ぬ', 'ネ', 'ﾈ','ね', 'ノ', 'ﾉ' , 'の', 'ハ', 'ﾊ','は', 'ヒ' ,
        'ﾋ','ひ', 'フ', 'ﾌ', 'ふ', 'ヘ' , 'ﾍ','へ', 'ホ' , 'ﾎ','ほ','マ', 'ﾏ' ,'ま',
        'ミ', 'ﾐ','み', 'ム' , 'ﾑ' ,'む', 'メ', 'ﾒ','め', 'モ' , 'ﾓ','も', 'ヤ',
        'ゃ', 'ゃ', 'ャ' , 'ｬ','や', 'ユ', 'ﾕ', 'ゅ', 'ュ', 'ｭ','ゆ', 'ヨ', 'ﾖ',
        'ょ', 'ョ' , 'ｮ','よ', 'ラ' , 'ﾗ','ら', 'リ', 'ﾘ','り', 'ル', 'ﾙ','る',
        'レ', 'ﾚ','れ', 'ロ' , 'ﾛ' , 'ろ', 'ワ' , 'ﾜ','わ', 'ゎ', 'ヮ', 'ヮ','わ',
        'ン', 'ﾝ' , 'ん', 'ガ', 'ｶﾞ','が', 'ギ', 'ｷﾞ','ぎ', 'グ' , 'ｸﾞ','ぐ', 'ゲ',
        'ｹﾞ','げ', 'ゴ', 'ｺﾞ','ご', 'ザ', 'ｻﾞ','ざ', 'ジ', 'ｼﾞ','じ', 'ズ',
        'ｽﾞ','ず', 'ゼ', 'ｾﾞ','ぜ', 'ゾ', 'ｿﾞ','ぞ', 'ダ', 'ﾀﾞ','だ', 'ヂ',
        'ﾁﾞ','ぢ', 'ヅ', 'ﾂﾞ','づ', 'デ', 'ﾃﾞ','で', 'ド', 'ﾄﾞ','ど', 'バ', 'ﾊﾞ',
        'ば', 'ビ', 'ﾋﾞ','び', 'ブ', 'ﾌﾞ','ぶ', 'ベ', 'ﾍﾞ','べ', 'ボ', 'ﾎﾞ', 'ぼ',
        'パ', 'ﾊﾟ','ぱ', 'ピ', 'ﾋﾟ','ぴ', 'プ', 'ﾌﾟ','ぷ', 'ペ', 'ﾍﾟ','ぺ', 'ポ',
        'ﾎ','ぽ'); 
    
    $c_str_len = mb_strlen($c_str, 'UTF-8');
    if ($c_str_len) {
      for($i=0; $i<$c_str_len; $i++) {
        $trac_str = mb_substr($c_str, $i, 1, 'UTF-8'); 
        if (!in_array($trac_str, $character_array)) {
          return true; 
        }
      }
    }
    
    return false;
  }
}

