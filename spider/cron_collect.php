#!/usr/bin/env php
<?php
define('PRO_ROOT_DIR','/home/.sites/132/site21/web/');
ini_set('display_errors', 'On');
error_reporting(E_ALL);
//file patch

require_once(PRO_ROOT_DIR."class/spider.php");
require_once(PRO_ROOT_DIR."collect.php");

define('LOG_DIR',PRO_ROOT_DIR.'logs/');

define('DB_SERVER', 'localhost'); //服务器名
define('DB_SERVER_USERNAME', 'root'); //用户名
define('DB_SERVER_PASSWORD', 'Qz8PYrk60uVg'); //密码
define('DB_DATABASE', 'osc_collect'); //数据库名

function cron_log($collect_info){
  //文件不存在则建立
  $log_file = LOG_DIR.date('Y-m-d',time()).'.log';


  if(!file_exists($log_file)){
     echo 'file not exist ,creating';
     $handle=fopen($log_file,"w"); //创建文件
     fclose($handle);
  }
  if (!file_exists($log_file)){
    echo 'create file failed'.$log_file;
  }else {
    $str_write = '';
    $str_write .=date('H:i:s',time()).str_repeat(' ',5);
    $str_write .= $collect_info."\n";

    $handle=fopen($log_file,"a+");
        //写日志
    echo $str_write;
    if(!fwrite($handle,$str_write)){//写日志失败
      echo "failed to write log";
    fclose($handle);
    }
  }
  //将message写入文件 
}

$link = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
mysql_select_db(DB_DATABASE);

$stop_sql = "select config_value from config where config_key = 'COLLECT_IS_STOP_STATUS'";
$stop_query = mysql_query($stop_sql);
if($stop_res = mysql_fetch_array($stop_query)){
  $is_stop = $stop_res['config_value'];
}else{
  $is_stop = 1;
}

if($is_stop==1){
  //确定可以运行 is_run = 0;
  $is_run = 0;
  $run_update_sql = "update config set config_value='".$is_run."' where config_key='COLLECT_IS_RUN_STATUS'";
  mysql_query($run_update_sql);
  $log_str = "script is stop";
  cron_log($log_str);
  exit;
}

$run_sql = "select config_value from config where config_key = 'COLLECT_IS_RUN_STATUS'";
$run_query = mysql_query($run_sql);
if($run_res = mysql_fetch_array($run_query)){
  $is_run = $run_res['config_value'];
}else{
  $is_run = 1;
}
//获得是否停止状态
if($is_run==1){
  $log_str = "the script is run";
  echo $log_str ."\n";
  exit;
}


//检索到的行数
$auto_array = array();
$auto_sql = "select config_value from config where config_key = 'COLLECT_LAST_DATE'";
$auto_query = mysql_query($auto_sql);
if($auto_res = mysql_fetch_array($auto_query)){
  $auto_array = unserialize($auto_res['config_value']);
  $flag = 1;
}else{
  $flag = 0;
}

$category_name_array=array();
$category_type=array();
$site_array=array();

//游戏
$category_query=  mysql_query("select distinct(category_name) from category order by category_id asc");  
while($row = mysql_fetch_array($category_query)){
    $category_name_array[] = $row['category_name'];
}
//买卖
$category_type=array(1=>'buy',0=>'sell');

//site(网站)
$site_query = mysql_query("select site_id,site_name from site order by site_id asc");
while($site_row = mysql_fetch_array($site_query)){
   $site_array[] =$site_row; 
}
if(empty($auto_array)){
  $auto_array['game_name'] = $category_name_array[0];
  $auto_array['game_type'] = 1;
}

$collect_error_array = array();
while(true){
  foreach($category_name_array as $game){
    if($game != $auto_array['game_name'] && $flag == 1){
      continue;
    }
    foreach($category_type as $key=>$category){
      if($key == $auto_array['game_type']){
        $flag = 0;
      }
      if($flag == 1){
        continue;
      }
      foreach($site_array as $site){
        $write_str =$game.'--'.$category.'--'.$site['site_name'];
        cron_log($write_str);
        $site_arr = $site['site_id'];
      }
        $stop_sql = "select config_value from config where config_key = 'COLLECT_IS_STOP_STATUS'";
        $stop_query = mysql_query($stop_sql);
        if($stop_res = mysql_fetch_array($stop_query)){
          $is_stop = $stop_res['config_value'];
        }else{
          $is_stop = 1;
        }
        if($is_stop==1){
          $is_run = 0;
          $run_update_sql = "update config set config_value='".$is_run."' where config_key='COLLECT_IS_RUN_STATUS'";
          mysql_query($run_update_sql);
          $log_str = "script is stop";
          cron_log($log_str);
          exit;
        }
        $insert_auto_array = array();
        $insert_auto_array['game_name'] = $game;
        $insert_auto_array['game_type'] = $key;
        $insert_auto_str = serialize($insert_auto_array);
        $update_last_data_sql = "update config set config_value = '".$insert_auto_str."' where
          config_key='COLLECT_LAST_DATE'";
        mysql_query($update_last_data_sql);
        $is_run = 1;
        $run_update_sql = "update config set config_value='".$is_run."' where
          config_key='COLLECT_IS_RUN_STATUS'";
        mysql_query($run_update_sql);
        $tep = get_contents_main($game,$key,$site_arr,$collect_error_array); 
        sleep(10);
    }
  }
  if(!empty($collect_error_array)){
   //获取所有的网站
   $site_list_array = array();
   $site_query = mysql_query("select site_id,site_name from site");
   while($site_array = mysql_fetch_array($site_query)){

     $site_list_array[$site_array['site_id']] = $site_array['site_name'];
   }
   //发送错误邮件
   $mail_str = '取得失敗詳細'."\n";
   foreach($collect_error_array as $collect_error_value){

     if($collect_error_value['type'] == 'buy'){

       $category_type = 1;
     }else{
       $category_type = 0;
     }
     $category_query = mysql_query("select category_id,site_id from category where category_name='".$collect_error_value['game']."' and category_url='".$collect_error_value['url']."' and category_type='".$category_type."'");
     $category_array = mysql_fetch_array($category_query);
     mysql_query("update product set is_error=1 where category_id='".$category_array['category_id']."'");
     $mail_str .= date('H:i:s',$collect_error_value['time']).'　　';
     $mail_str .= $collect_error_value['game'].'--';
     $mail_str .= $collect_error_value['type'].'--';
     $mail_str .= $site_list_array[$category_array['site_id']].'　　';
     $mail_str .= $collect_error_value['url']."\n";
   }
   $email = '287499757@qq.com';
   $admin_email = '287499757@qq.com';
   $error_subject = '取得失敗エラー';
   $error_msg = $mail_str;
   $error_headers = "From: ".$email ."<".$email.">";
   mail($admin_email,$error_subject,$error_msg,$error_headers);
  }
  $auto_array['game_name'] = $category_name_array[0];
  $auto_array['game_type'] = 1;
}
