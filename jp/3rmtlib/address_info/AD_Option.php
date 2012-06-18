<?php
require_once 'AD_Option_DbRecord.php';

class AD_Option extends AD_Option_DbRecord
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
    $sql = 'select * from '.TABLE_ADDRESS.' where status="0" order by sort'; 
    $groups = $this->getResultObjects($sql, 'AD_Option_Group'); 
    return $groups; 
  }
  
  function render($belong_option_str, $ptype = false, $is_space = false)
  {
    $this->groups = $this->getGroups($belong_option_str, $ptype); 
    foreach ($this->groups as $group) {
      $group->render($this->option_error_array,$is_space); 
    }
  }

  function check() 
  {
    global $_POST; 
    
    $error_single = false;
    foreach ($_POST as $key => $value) {
      $op_pos = substr($key, 0, 3); 
      if ($op_pos == 'op_') {
        $option_info_array = explode('_', $key);
        $item_sql = "select * from ".TABLE_ADDRESS." where name_flag = '".$option_info_array[1]."'"; 
        $item = $this->getResultObject($item_sql, 'AD_Option_Item'); 
        if ($item) {
          if ($item->check($this->option_error_array)) {
            $error_single = true;
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
    $exists_group_raw = tep_db_query("select id from ".TABLE_ADDRESS." where id in ('".$belong_option_str."')");  
     if (tep_db_num_rows($exists_group_raw)) {
       return true; 
     }
     return false; 
  }

  function admin_whether_show($belong_option_str)
  {
    if (empty($belong_option_str)) {
      return false; 
    }
    $exists_group_raw = tep_db_query("select id from ".TABLE_ADDRESS." where id in ('".$belong_option_str."')");  
     if (tep_db_num_rows($exists_group_raw)) {
       while ($exists_group = tep_db_fetch_array($exists_group_raw)) {
         $item_exists_raw = tep_db_query("select id from ".TABLE_ADDRESS." where id = '".$exists_group['id']."' and status = '0'"); 
         if (tep_db_num_rows($item_exists_raw)) {
           return true; 
         }
       }
     }
     return false; 
  }
}
