<?php

/*
  此文件用于保存前后台通的函数  ，只限于函数 ，不可以包括语句，类
 */
/* -------------------------------------
    功能: 获取指定网站的设置数值 
    参数: $key(int) 设置名   
    参数: $site_id(int) 网站id   
    参数: $table_name(string) 表名   
    返回值: 设置数值(string/boolean) 
------------------------------------ */

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

/* -------------------------------------
    功能: 获取指定网站的设置数值(如果取不到就取默认值) 
    参数: $key(int) 设置名   
    参数: $site_id(int) 网站id   
    返回值: 设置数值(string/boolean) 
------------------------------------ */
function get_configuration_by_site_id_or_default($key,$site_id){
  $res = get_configuration_by_site_id($key,$site_id);
  if($res===false){
    return get_configuration_by_site_id($key,0);
  }else{
    return $res;
  }
}

/* -------------------------------------
    功能: 是否是post提交 
    参数: 无   
    返回值: 是否是post提交(boolean) 
------------------------------------ */
function isPost(){
  return requestMethod()==="POST";
}

/* -------------------------------------
    功能: 是否是get提交 
    参数: 无   
    返回值: 是否是get提交(boolean) 
------------------------------------ */
function isGet(){
  return requestMethod()==="GET";
}

/* -------------------------------------
    功能: 请求的类型 
    参数: 无   
    返回值: 请求的类型(string) 
------------------------------------ */
function requestMethod(){
  return $_SERVER['REQUEST_METHOD'];
}

/* -------------------------------------
    功能: 配送时间显示 
    参数: $date(string) 日期   
    参数: $time(string) 时间   
    返回值: 时间显示(string) 
------------------------------------ */
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

/* -------------------------------------
    功能: 获取默认语言id 
    参数: 无   
    返回值: 默认语言id(int) 
------------------------------------ */
function tep_get_default_language_id(){
    $language_id_query = tep_db_query("select languages_id, directory from " .
        TABLE_LANGUAGES . " where code = '" . DEFAULT_LANGUAGE . "'");
    if(tep_db_num_rows($language_id_query)){
      $lan_id_row = tep_db_fetch_array($language_id_query);
      $languages_id = $lan_id_row['languages_id'];
    }
    return $languages_id;
}

/* -------------------------------------
    功能: 获得默认语言目录名 
    参数: 无   
    返回值: 默认语言目录名(string) 
------------------------------------ */
function tep_get_default_language(){
    $language_id_query = tep_db_query("select languages_id, directory from " .
        TABLE_LANGUAGES . " where code = '" . DEFAULT_LANGUAGE . "'");
    if(tep_db_num_rows($language_id_query)){
      $lan_id_row = tep_db_fetch_array($language_id_query);
      $languages_dir = $lan_id_row['directory'];
    }
    return $languages_dir;
}
/*----------------------------------
  功能: 获得数据库字段长度 
  参数: $table (string)类型  表名
  参数: $cname (string  array)  字段名
  返回:  (string  array)  字段长度
----------------------------------*/
function tep_get_column_len($table,$cname){
  $sql = "desc ".$table;
  $query = tep_db_query($sql);
  if(is_array($cname)){
    $res_arr=array();
    while($res = tep_db_fetch_array($query)){
      $type='';
      $arr=array();
      if(in_array($res['Field'],$cname)){
        $type = $res['Type'];
        if(preg_match("/(\d+)/",$type,$arr)){
           $res_arr[$res['Field']] = $arr[1];
        }else{
           $res_arr[$res['Field']] = '';
        }
      }
    }
    return $res_arr;
  }else{
    while($res = tep_db_fetch_array($query)){
      if($res['Field'] == $cname){
        $type = $res['Type'];
        if(preg_match("/(\d+)/",$type,$arr)){
          return $arr[1];
        }else{
          return 0;
        }
      }
    }
  }
}
