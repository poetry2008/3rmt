<?php
require_once 'Option_DbRecord.php';

class HM_Option extends Option_DbRecord
{
  var $option_error_array = array();
  var $msg_is_null = '無し'; 

/* -------------------------------------
    功能: 构造函数 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function __construct()
  {
  }

/* -------------------------------------
    功能: 获得组的相关信息 
    参数: $belong_option_str(string) 组id   
    参数: $ptype(boolean) 是否是预约   
    返回值: 组的相关信息(array) 
------------------------------------ */
  function getGroups($belong_option_str, $ptype = false)
  {
    if (empty($belong_option_str)) {
      $belong_option_str = 0; 
    }
    $sql = 'select * from '.TABLE_OPTION_GROUP.' where id in ( '.$belong_option_str.') '.(($ptype)?' and is_preorder = 1':'').' order by sort_num'; 
    $groups = $this->getResultObjects($sql, 'HM_Option_Group'); 
    return $groups; 
  }
  
/* -------------------------------------
    功能: 输出组所关联的元素的html 
    参数: $belong_option_str(string) 组id   
    参数: $ptype(boolean) 是否是预约   
    参数: $is_product_info(int) 是否是商品信息页   
    参数: $pre_item_str(string) 名字前缀   
    参数: $cart_obj(obj) 购物车对象   
    参数: $cflag(boolean) 标识   
    返回值: 无 
------------------------------------ */
  function render($belong_option_str, $ptype = false, $is_product_info = 0, $pre_item_str = '', $cart_obj = '', $cflag)
  {
    $this->groups = $this->getGroups($belong_option_str, $ptype); 
    foreach ($this->groups as $group) {
      $group->render($this->option_error_array, $is_product_info, $pre_item_str, $cart_obj, $ptype, $cflag); 
    }
  }

/* -------------------------------------
    功能: 检查信息是否正确 
    参数: $check_type(int) 类型   
    返回值: 是否正确(boolean) 
------------------------------------ */
  function check($check_type = 0) 
  {
    global $_POST; 
    
    $error_single = false;
    foreach ($_POST as $key => $value) {
      if ($check_type == 1) {
        $f_op_pos = strpos($key, 'op_');
        if ($f_op_pos !== false) {
          $op_str = substr($key, $f_op_pos);
          $pre_error_str = substr($key, 0, $f_op_pos);
          $option_info_array = explode('_', $op_str);
          $item_sql = "select * from ".TABLE_OPTION_ITEM." where id = '".$option_info_array[3]."'"; 
          $item = $this->getResultObject($item_sql, 'HM_Option_Item'); 
          if ($item) {
            if ($item->check($this->option_error_array, 1, $pre_error_str)) {
              $error_single = true;
            }
          }
        }
      } else {
        $op_pos = substr($key, 0, 3); 
        if ($op_pos == 'op_') {
          $option_info_array = explode('_', $key);
          $item_sql = "select * from ".TABLE_OPTION_ITEM." where id = '".$option_info_array[3]."'"; 
          $item = $this->getResultObject($item_sql, 'HM_Option_Item'); 
          if ($item) {
            if ($item->check($this->option_error_array)) {
              $error_single = true;
            }
          }
        }
      }
    }
    return $error_single; 
  }
  
/* -------------------------------------
    功能: 组是否显示 
    参数: $belong_option_str(str) 组信息   
    返回值: 是否显示(boolean) 
------------------------------------ */
  function whether_show($belong_option_str)
  {
    if (empty($belong_option_str)) {
      return false; 
    }
    $exists_group_raw = tep_db_query("select id from ".TABLE_OPTION_GROUP." where id in ('".$belong_option_str."') and is_preorder = '1'");  
     if (tep_db_num_rows($exists_group_raw)) {
       return true; 
     }
     return false; 
  }

/* -------------------------------------
    功能: 组是否显示(后台) 
    参数: $belong_option_str(str) 组信息   
    参数: $atype(int) 是否是登录后   
    参数: $ad_c_flag(int) 标识   
    返回值: 是否显示(boolean) 
------------------------------------ */
  function admin_whether_show($belong_option_str, $atype = 0, $ad_c_flag = 1)
  {
    if (empty($belong_option_str)) {
      return false; 
    }
    $exists_group_raw = tep_db_query("select id from ".TABLE_OPTION_GROUP." where id in ('".$belong_option_str."')");  
     if (tep_db_num_rows($exists_group_raw)) {
       while ($exists_group = tep_db_fetch_array($exists_group_raw)) {
         $item_exists_raw = tep_db_query("select id from ".TABLE_OPTION_ITEM." where group_id = '".$exists_group['id']."' and status = '1'".(($atype == 1)?" and place_type = '0'":"")); 
         if (tep_db_num_rows($item_exists_raw)) {
           return true; 
         }
       }
     }
     return false; 
  }
  
/* -------------------------------------
    功能: 该组下是否有登录后的元素 
    参数: $belong_option(int) 组id   
    参数: $p_cflag(int) 标识   
    返回值: 是否有元素(boolean) 
------------------------------------ */
  function check_old_symbol_show($belong_option, $p_cflag)
  {
    if (empty($belong_option)) {
      return false; 
    }
    $exists_group_raw = tep_db_query("select id from ".TABLE_OPTION_GROUP." where id ='".$belong_option."'");  
    $exists_group_res = tep_db_fetch_array($exists_group_raw);
    if ($exists_group_res) {
      $item_raw = tep_db_query("select id from ".TABLE_OPTION_ITEM." where group_id = '".$exists_group_res['id']."' and place_type = '1'");   
      if (!tep_db_num_rows($item_raw)) {
        return false; 
      }
    }
    return true;
  }

/* -------------------------------------
    功能: 该组是否显示(用于预约) 
    参数: $belong_option(int) 组id信息   
    参数: $pre_cflag(int) 标识   
    返回值: 是否显示(boolean) 
------------------------------------ */
  function preorder_whether_show($belong_option_str, $pre_cflag = 0)
  {
    if (empty($belong_option_str)) {
      return false; 
    }
    $exists_group_raw = tep_db_query("select id from ".TABLE_OPTION_GROUP." where id in ('".$belong_option_str."') and is_preorder = '1'");  
     if (tep_db_num_rows($exists_group_raw)) {
       $exists_item_raw = tep_db_query("select id from ".TABLE_OPTION_ITEM." where group_id in ('".$belong_option_str."') and status = 1 and place_type = 1"); 
       if (tep_db_num_rows($exists_item_raw)) {
         return true; 
       }
     }
     return false; 
  }
}
