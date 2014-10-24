#!/usr/bin/env php
<?php
define('PRO_ROOT_DIR','/home/.sites/132/site21/web');
ini_set('display_errors', 'On');
error_reporting(E_ALL);
//file patch
require_once(PRO_ROOT_DIR."/includes/configure.php");
require_once(PRO_ROOT_DIR."/class/spider.php");
require_once(PRO_ROOT_DIR."/collect.php");

define('LOG_DIR',PRO_ROOT_DIR.'/log/');
define('LOG_FILE_NAME',LOG_DIR.date('Y-m-d',time()).'.log');
define('LOG_FILE_NAME_LAST',LOG_DIR.'last.log');

$link = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
mysql_select_db(DB_DATABASE);

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

//发送游戏相关信息
foreach($category_name_array as $game){
   foreach($category_type as $key=>$category){
	   foreach($site_array as $site){
		   
          $tep = get_contents_main($game,$key,$site['site_id']); 
		  explode('|||',$tep);
		  if($tep[0]!='error'){
			  $write_str ='GAME_NAME:'.$game;
			  $write_str .='TYPE:'.$category;
			  $write_str .='SITE_NAME:'.$site['site_name'];
			  cron_log($write_str);
		  }else{
			  $write_str = 'collect fail'.$game.'-'.$site['site_name'];
			  cron_log($write_str);
		  }
		  sleep(10);
	   } 
   }
}


function cron_log($collect_info){
  //文件不存在则建立

  if(!file_exists(LOG_FILE_NAME)){
     echo 'file not exist ,creating';
     $handle=fopen(LOG_FILE_NAME,"w"); //创建文件
     fclose($handle);
  }
  if (!file_exists(LOG_FILE_NAME)){
    echo 'create file failed'.LOG_FILE_NAME;
  }else {
    $str_write = '';
    $str_write .=date('H:i:s',time()).str_repeat(' ',5);
    $str_write .= $collect_info."\n";

    $handle=fopen(LOG_FILE_NAME,"a+");
        //写日志
    echo $str_write;
    if(!fwrite($handle,$str_write)){//写日志失败
      echo "failed to write log";
    fclose($handle);
    }
  }
  //将message写入文件 
}
