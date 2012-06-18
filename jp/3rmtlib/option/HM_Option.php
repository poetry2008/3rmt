<?php
require_once 'Option_DbRecord.php';

class HM_Option extends Option_DbRecord
{
  var $option_error_array = array();
  
  function __construct()
  {
  }

  function getGroups($belong_option_str, $ptype = false)
  {
    if (empty($belong_option_str)) {
      $belong_option_str = 0; 
    }
    $sql = 'select * from '.TABLE_OPTION_GROUP.' where id in ( '.$belong_option_str.') '.(($ptype)?' and is_preorder = 1':'').' order by sort_num'; 
    $groups = $this->getResultObjects($sql, 'HM_Option_Group'); 
    return $groups; 
  }
  
  function render($belong_option_str, $ptype = false, $is_product_info = 0, $pre_item_str = '', $cart_obj = '', $cflag)
  {
    $this->groups = $this->getGroups($belong_option_str, $ptype); 
    foreach ($this->groups as $group) {
      $group->render($this->option_error_array, $is_product_info, $pre_item_str, $cart_obj, $ptype, $cflag); 
    }
  }

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

  function admin_whether_show($belong_option_str, $atype = 0)
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
  
  function check_old_symbol_show($belong_option)
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

  function preorder_whether_show($belong_option_str)
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
