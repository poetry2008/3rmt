
<?php
define('PRO_ROOT_DIR','/home/szn/project/3rmt/spider/');
ini_set('display_errors', 'On');
error_reporting(E_ALL);
//file patch

require_once(PRO_ROOT_DIR."includes/configure.php");
require_once(PRO_ROOT_DIR."class/spider.php");
require_once(PRO_ROOT_DIR."collect.php");

define('LOG_DIR',PRO_ROOT_DIR.'logs/');

define('LOG_FILE_NAME',LOG_DIR.date('Y-m-d',time()).'.log');

define('DB_SERVER', 'localhost'); //服务器名
define('DB_SERVER_USERNAME', 'root'); //用户名
define('DB_SERVER_PASSWORD', '123456'); //密码
define('DB_DATABASE', 'osc_collect'); //数据库名

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

$link = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
mysql_select_db(DB_DATABASE);


$run_sql = "select config_value from config where config_key = 'COLLECT_IS_RUN_STATUS'";
$run_query = mysql_query($run_sql);
if($run_res = mysql_fetch_array($run_query)){
  $is_run = $run_res['config_value'];
}else{
  $is_run = 1;
}
//获得是否停止状态
if($is_run==1){
  echo "the script is run";
  exit;
}

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
  $sql = '';
  exit;
}

//检索到的行数
$auto_array = array();
$auto_sql = "select config_value from config where config_key = 'COLLECT_LAST_DATE'";
$auto_query = mysql_query($auto_sql);
if($auto_res = mysql_fetch_array($auto_query)){
  $auto_array = $auto_res['config_value'];
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


while(true){
  foreach($category_name_array as $game){
    if($game == $auto_array['game_name'] && $flag == 1){
      continue;
    }
    foreach($category_type as $key=>$category){
      if($key != $auto_array['game_type'] && $flag == 1){
        continue;
      }
      foreach($site_array as $site){
        if($site['site_id'] == $auto_array['site_name'] && $flag == 1){
          $flag = 0;
          continue;
        }
        if($is_stop==1){
           $is_run = 0;
           //mysql_query("update product_auto set is_run='".$is_run."',game_name='".$game."',game_type='".$key."',site_name='".$site['site_id']."'");
           echo "update product_auto set is_run='".$is_run."',game_name='".$game."',game_type='".$key."',site_name='".$site['site_id']."'";
           exit;
        }
        $is_run = 1;
        $run_update_sql = "update config set config_value='".$is_run."' where
          config_key='COLLECT_IS_RUN_STATUS'";
        mysql_query($run_update_sql);
        //$tep = get_contents_main($game,$key,$site['site_id']); 
        explode('|||',$tep);
        if($tep[0]!='error'){
          $write_str =$game.'--'.$category.'--'.$site['site_name'];
          cron_log($write_str);
        }else{
          $write_str = 'collect fail'.$game.'-'.$site['site_name'].'<br/>';
          cron_log($write_str);
        }
        sleep(3);
      }
    }
  }
}
//发送游戏相关信息
/*foreach($category_name_array as $game){
   foreach($category_type as $key=>$category){
	   foreach($site_array as $site){
		   
          $tep = get_contents_main($game,$key,$site['site_id']); 
		  explode('|||',$tep);
		  if($tep[0]!='error'){
			  $write_str =$game.'--'.$category.'--'.$site['site_name'];
			  cron_log($write_str);
		  }else{
			  $write_str = 'collect fail'.$game.'-'.$site['site_name'].'<br/>';
			  cron_log($write_str);
		  }
		  sleep(10);
	   } 
   }
}

*/

