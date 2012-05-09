<?php
//从other表里取出四个变量

$keys = array(
'reset_pwd_startdate',
'reset_pwd_enddate',
'reset_pwd_title',
'reset_pwd_content',
      );

foreach( $keys as $key){
    $$key = '';
    $sql = tep_db_query("select `value` from ".TABLE_OCONFIG." where  keyword = '".$key."'");
    $result = tep_db_fetch_array($sql);
    if($result['value']!=''){
    $$key = $result['value'];
    }
}




$get_all_number_sql = 'select count(*) as n from '.TABLE_CUSTOMERS.' where customers_guest_chk = "0"';
$tmp = tep_db_fetch_array(tep_db_query($get_all_number_sql));
$all_member_count = $tmp['n'];

$need_reset_member_count_sql = 'select count(*) as n from '.TABLE_CUSTOMERS.' c
where c.reset_flag = 1 and c.customers_guest_chk = 0' ;
$tmp = tep_db_fetch_array(tep_db_query($need_reset_member_count_sql));
$need_reset_member_count = $tmp['n']; 
unset($tmp);
$reset_done_count_sql = 'select count(*) as n from '.TABLE_CUSTOMERS.' c where
c.reset_flag = 1 and c.reset_success = 1';

$tmp = tep_db_fetch_array(tep_db_query($reset_done_count_sql));
$reset_done = $tmp['n'];
unset($tmp);

