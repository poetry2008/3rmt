#!/usr/bin/env php
<?php
define('PRO_ROOT_DIR','/home/.sites/28/site1/web/spider/');
ini_set('display_errors', 'On');
error_reporting(E_ALL);
//file patch

require_once(PRO_ROOT_DIR."class/spider.php");
require_once(PRO_ROOT_DIR."collect.php");
require_once(PRO_ROOT_DIR."collect_match.php");
require_once(PRO_ROOT_DIR."includes/configure.php");

define('LOG_DIR',PRO_ROOT_DIR.'logs/');

/*
define('DB_SERVER', 'localhost'); //服务器名
define('DB_SERVER_USERNAME', 'root'); //用户名
define('DB_SERVER_PASSWORD', 'Qz8PYrk60uVg'); //密码
define('DB_DATABASE', 'osc_collect'); //数据库名
*/

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
/*
$category_query=  mysql_query("select distinct(category_name) from category order by category_id asc");  
while($row = mysql_fetch_array($category_query)){
    $category_name_array[] = $row['category_name'];
}
*/


//买卖
$category_type_all=array(1=>'buy',0=>'sell');

//site(网站)
$site_query = mysql_query("select site_id,site_name from site order by site_id asc");
while($site_row = mysql_fetch_array($site_query)){
   $site_array[] =$site_row; 
}

$collect_error_array = array();
while(true){
$category_query=  mysql_query("select * from category_sort order by sort ,  category_name  ");  
while($row = mysql_fetch_array($category_query)){
    $category_name_array[] = $row['category_keyword'];
}
if(empty($auto_array)){
  $auto_array['game_name'] = $category_name_array[0];
  $auto_array['game_type'] = 1;
}
        $collect_error_array = array();
  foreach($category_name_array as $game){
    if($game != $auto_array['game_name'] && $flag == 1){
      continue;
    }
    foreach($category_type_all as $key=>$category){
      if($key == $auto_array['game_type']){
        $flag = 0;
      }
      if($flag == 1){
        continue;
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
        $site_arr = array();
        foreach($site_array as $site){
          $site_arr[] = $site;     
        }
//预处理网站
  $site_str = array();
  $url_str_array = array();
  $category_id_str_array = array();
  $url_kaka_array = array();
  $site_info = array();
  /*以下是区分是手动更新的还是后台自动执行更新的判断
   * 买卖是数组是手动更新的,相反就是后台自动更新的
   * */
  //site
  $site_query = mysql_query("select site_id,site_name from site order by site_id asc");
  $i = 0;
  $j = 0;
  $game_type=$game;
  while($site_array = mysql_fetch_array($site_query)){

    $category_query = mysql_query("select * from category where site_id='".$site_array['site_id']."' and category_name='".$game_type."' and game_server='jp'");
    while($category_array = mysql_fetch_array($category_query)){

      $url_info = parse_url($category_array['category_url']);
      if($category_array['category_type'] == 1){
        $url_str_array['buy'][$i] = $category_array['category_url'];
        $category_id_str_array['buy'][$i] = $category_array['category_id'];
        $site_str['buy'][$i] = $url_info['host'];
        $site_info['buy'][$i] = $site_array['site_name'];
        $i++;
      }else{
        $url_str_array['sell'][$j] = $category_array['category_url'];
        $category_id_str_array['sell'][$j] = $category_array['category_id'];
        $site_str['sell'][$j] = $url_info['host']; 
        $site_info['sell'][$j] = $site_array['site_name'];
        $j++;
      }
    } 
  }
  $collect_site = array('rmt.kakaran.jp','rmtrank.com');
  $category_type = array($category);
//预处理网站结束

//开始处理数据

  foreach($category_type as $category_value){

    $url_array = $url_str_array[$category_value];
    $category_id_array = $category_id_str_array[$category_value];
    $site = $site_str[$category_value];
    $site_n = $site_info[$category_value];

     //正则
    $search_array = $search_array_match[$category_value][$game_type];
    $other_array = $other_array_match[$category_value];
    //开始采集数据
    $curl_flag = 0;
    $site_key = '';
    $search_url = array();
    $search_host = array();
    $collect_site_value = array();
    $log_str = '';
    foreach($site as $site_key => $site_value){
      if($site_value == null || $site_value ==''){
        continue;
      }
      if($site_value == 'www.iimy.co.jp'||$site_value == '192.168.160.200'){
        continue;
      }
      foreach($collect_site as $c_site){
        if($site_value == $c_site){
          $collect_site_value[$c_site][] = $site_key;
        }
      }
      if(!in_array($site_value,$search_host)&&$url_array[$site_key]!=''){
        $search_host[] = $site_value;
        $search_url[$site_value] = $url_array[$site_key];
        $log_str .= date('H:i:s',time()).str_repeat(' ',5).$game.'--'.$category.'--'.$site_n[$site_key]."\n";
      }
    }
    //采集所有网站的数据
    $all_result = get_all_result($search_url);
    //通过正则获得所有网站的数据
    $all_site_info_array = get_info_array($all_result,$search_array);
    //处理数据并存储到数据库
    $collect_res_url = array();
    $collect_res_name = array();
    foreach($all_site_info_array as $site_info_key => $site_info_arr){
      $temp_product_name = array();
      foreach($site_info_arr['products_name'] as $p_name){
      	//处理产品名
        $temp_product_name[] = match_data_iimy($game_type,$category_value,$url_array[$site_value],$p_name);
      }
      $site_info_arr['products_name'] = $temp_product_name;
      if(in_array($site_info_key,$collect_site)){
      	$collect_res_url[$site_info_key]['url'] = $site_info_arr['url'];
      	$collect_res_url[$site_info_key]['products_name'] =  $site_info_arr['products_name'];
        continue;
      }
      $site_value = array_search($site_info_key,$site);
      $category_id = $category_id_array[$site_value];
      save2db($category_id,$site_value,$site_info_arr,$category_value,$game_type);
    }
    //采集网站的特殊处理
    //处理网站名
    
    
    //获得rmt 需要采集的产品数量 
    $product_sql =  "select * from product where 
       category_id = (select category_id from category where 
         site_id=(select site_id from site where 
           site_url like 'http://www.iimy.co.jp%' ) 
       and category_name='".$game_type."' 
       and game_server='jp' 
       and category_type = '".($category_value=='buy'?1:0)."')
       order by sort_order, product_name";
    $product_query = mysql_query($product_sql);
    $product_name_arr = array();
    while($product_row = mysql_fetch_array($product_query)){
      $product_name_arr[] = $product_row['product_name'];
    }

    $search_url_list = array();
    $search_name_list = array();
    foreach($collect_res_url as $site_key => $site_product_url){
      foreach($site_product_url['url'] as $product_index => $url){
      	if(!in_array($collect_res_url[$site_key]['products_name'][$product_index],$product_name_arr)){
      	  continue;
      	}
        if($site_key=='rmt.kakaran.jp'){
          if($category_value=='sell'){
            $url = str_replace('buy','sell',$url);
          }
          $url = $url.'?s=bank_transfer';
          $search_url = "http://rmt.kakaran.jp".$url;
        }
        if($site_key=='rmtrank.com'){
          $search_url = preg_replace('/\.htm$/','+sort+price.htm',$url);
          if($category_value=='sell'){
            $search_url = str_replace('content_id+1','content_id+2',$search_url);
          }
        }
        $search_url_list[$product_index][$site_key] = $search_url;
        $search_name_list[$product_index][$site_key] = $collect_res_url[$site_key]['products_name'][$product_index];
      }
    }
    $i = 0;
    foreach($search_url_list as $sk => $sv){
      $tmp_url = array();
      foreach($sv as $s_k => $s_v){
        $tmp_url[] = $s_v;
      }
      $i++;
      sleep(3);
      $all_result = get_all_result($tmp_url);
      //通过正则获得所有网站的数据
      $all_site_info_array = get_info_array($all_result,$other_array);
      foreach($all_site_info_array as $site_key => $site_info){
        $con = count($site_info['price']);
        $con_arr = $site_info['price'];
        if($con > count($site_info['site_names'])){
          $con = count($site_info['site_names']);
          $con_arr = $site_info['site_names'];
        }
        if($con > count($site_info['inventory'])){
          $con = count($site_info['inventory']);
          $con_arr = $site_info['inventory'];
        }
        $t_price = array();
        $t_inventory = array();
        $price = array();
        $inventory = array();
        $rmt_name = array('ジャックポット','ゲームマネー','カメズ','学園','FF14-RMT','RedStone-RMT','GM-Exchange','ワールドマネー','Itemdepot','GM-Exchange');
        foreach($con_arr as $con_key => $con_value){
          if(in_array($site_info['site_names'][$con_key],$rmt_name)){
            continue;
          }
          $price[] = $site_info['price'][$con_key];
          $inventory[] = $site_info['inventory'][$con_key];
        }
        if($category_value =='sell'){
          $pos = array_search(max($price), $price);
          $t_price[] = $price[$pos];
          $t_inventory[] = $inventory[$pos];
          unset($price[$pos]);
          unset($inventory[$pos]);
          $pos = array_search(max($price), $price);
          $t_price[] = $price[$pos];
          $t_inventory[] = $inventory[$pos];
          unset($price[$pos]);
          unset($inventory[$pos]);
          $pos = array_search(max($price), $price);
          $t_price[] = $price[$pos];
          $t_inventory[] = $inventory[$pos];
          unset($price[$pos]);
          unset($inventory[$pos]);
        }else{
          $pos = array_search(min($price), $price);
          $t_price[] = $price[$pos];
          $t_inventory[] = $inventory[$pos];
          unset($price[$pos]);
          unset($inventory[$pos]);
          $pos = array_search(min($price), $price);
          $t_price[] = $price[$pos];
          $t_inventory[] = $inventory[$pos];
          unset($price[$pos]);
          unset($inventory[$pos]);
          $pos = array_search(min($price), $price);
          $t_price[] = $price[$pos];
          $t_inventory[] = $inventory[$pos];
          unset($price[$pos]);
          unset($inventory[$pos]);
        }
        $log_name = array();
        foreach($collect_site_value[$site_key] as $t_key => $s_site_value){
          $site_info_arr = array();
          $site_info_arr = array('products_name'=> array($search_name_list[$sk][$site_key]),
          	  'price' => array($t_price[$t_key]),
          	  'inventory' => array($t_inventory[$t_key]));
          $log_name[] = $site_n[$s_site_value];
          $category_id = $category_id_array[$s_site_value];
          save2db($category_id,$s_site_value,$site_info_arr,$category_value,$game_type,$site_key);
        }
        $log_str .= date('H:i:s',time()).str_repeat(' ',5).$game.'--'.$category.'--'.str_replace('1','',$log_name[0]).'-'.$i."\n";
      }
    }

  }

/*
 * na FF14 游戏采集
 */
  cron_log($log_str);
  if($game_type == 'FF14'){
    tep_get_toher_collect($game_type);
    $write_str = date('H:i:s',time()).str_repeat(' ',5).$game_type.'--NA';
    cron_log($write_str);
  }
        sleep(20);
    }
  }
  if(!empty($collect_error_array)){
   //获取所有的网站
   $site_list_array = array();
   $site_url_array = array();
   $site_query = mysql_query("select site_id,site_name from site");
   while($site_id_array = mysql_fetch_array($site_query)){

     $site_list_array[$site_id_array['site_id']] = $site_id_array['site_name'];
     $site_url_array[$site_id_array['site_id']] = $site_id_array['site_url'];
   }
   //发送错误邮件
   $mail_str = '自動更新失敗詳細'."\n";
   foreach($collect_error_array as $collect_error_value){

     if($collect_error_value['type'] == 'buy'){

       $category_type = 1;
     }else{
       $category_type = 0;
     }
     $category_query = mysql_query("select category_id,site_id from category where category_name='".$collect_error_value['game']."' and category_url='".$collect_error_value['url']."' and category_type='".$category_type."'");
     if(mysql_num_rows($category_query) > 0){
       $category_array = mysql_fetch_array($category_query);
     }else{

       $url_array = parse_url($collect_error_value['url']);
       $url_str = $url_array['scheme'].'://'.$url_array['host'].'/';
       $category_array['site_id'] = array_search($url_str,$site_url_array);
     }
     //mysql_query("update product set is_error=1 where category_id='".$category_array['category_id']."'");
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
   //mail($admin_email,$error_subject,$error_msg,$error_headers);
  }
  $auto_array['game_name'] = $category_name_array[0];
  $auto_array['game_type'] = 1;
}
