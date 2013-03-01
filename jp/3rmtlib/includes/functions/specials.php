<?php
/*
  $Id$

*/

/* -------------------------------------
    功能: 设置特价的状态 
    参数: $specials_id(int) 特价id   
    参数: $status(string) 状态   
    返回值: 是否成功(resource/boolean) 
------------------------------------ */
  function tep_set_specials_status($specials_id, $status) {
    return tep_db_query("
        update " . TABLE_SPECIALS . " 
        set status = '" . $status . "', 
            date_status_change = now() 
        where specials_id = '" . $specials_id . "'
    ");
  }

/* -------------------------------------
    功能: 设置过期的特价的状态 
    参数: 无  
    返回值: 无 
------------------------------------ */
  function tep_expire_specials() {
    $specials_query = tep_db_query("
        select specials_id 
        from " . TABLE_SPECIALS . " 
        where status = '1' 
          and now() >= expires_date 
          and expires_date > 0
    ");
    if (tep_db_num_rows($specials_query)) {
      while ($specials = tep_db_fetch_array($specials_query)) {
        tep_set_specials_status($specials['specials_id'], '0');
      }
    }
  }
?>
