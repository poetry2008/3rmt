<?php

/*
  此文件用于保存前后台通的函数  ，只限于函数 ，不可以包括语句，类
 */

function get_configuration_by_site_id($key, $site_id = '0',$table_name='') {
  if($table_name==''){
    $config = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key='".$key."' and site_id='".$site_id."'"));
  }else{
    $config = tep_db_fetch_array(tep_db_query("select * from ".$table_name." where configuration_key='".$key."' and site_id='".$site_id."'"));
  }
  if ($config) {
    return $config['configuration_value'];
  } else {
    return false;
  }
}


/*
  取得唯一值
*/
function get_configuration_by_site_id_or_default($key,$site_id){
  return get_configuration_by_site_id($key,$site_id)===false?get_configuration_by_site_id($key,0):get_configuration_by_site_id($key,$site_id);
}


/*
判断是否是POST
 */

function isPost(){
  return requestMethod()==="POST";
}
function isGet(){
  return requestMethod()==="GET";
}

function requestMethod(){
  return $_SERVER['REQUEST_METHOD'];
}

//取引时间显示
function tep_get_torihiki_format($date='',$time=''){
  if($date!=''&&$time!=''){
    $time_arr = explode('-',$time);
    $start = $date." ".$time_arr[0];
    $end = $date." ".$time_arr[1];
    return $start.TEXT_TORIHIKI_REPLACE_STR.$end; 
  }else if($date==''&&time!=''){
    $time_arr = explode('-',$time);
    $start = str_replace(':',TEXT_TORIHIKI_HOUR_STR,$time_arr[0]).TEXT_TORIHIKI_MIN_STR;
    $end = str_replace(':',TEXT_TORIHIKI_HOUR_STR,$time_arr[1]).TEXT_TORIHIKI_MIN_STR;
    return $start.TEXT_TORIHIKI_REPLACE_STR.$end; 
  }else if($date!=''&&time==''){
    return $date;
  }else {
    return null;
  }
}

//判断订单的产品有几种配送方法

// set default language to languages id 
function tep_get_default_language_id(){
    $language_id_query = tep_db_query("select languages_id, directory from " .
        TABLE_LANGUAGES . " where code = '" . DEFAULT_LANGUAGE . "'");
    if(tep_db_num_rows($language_id_query)){
      $lan_id_row = tep_db_fetch_array($language_id_query);
      $languages_id = $lan_id_row['languages_id'];
    }
    return $languages_id;
}

// set default language to language
function tep_get_default_language(){
    $language_id_query = tep_db_query("select languages_id, directory from " .
        TABLE_LANGUAGES . " where code = '" . DEFAULT_LANGUAGE . "'");
    if(tep_db_num_rows($language_id_query)){
      $lan_id_row = tep_db_fetch_array($language_id_query);
      $languages_dir = $lan_id_row['directory'];
    }
    return $languages_dir;
}
