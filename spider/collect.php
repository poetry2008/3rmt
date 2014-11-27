<?php
//采集脚本
ini_set("display_errors", "On");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
set_time_limit(0);

//file patch
require('includes/configure.php');
//require_once('class/spider.php');

//link db
$link = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
mysql_query('set names utf8');
mysql_select_db(DB_DATABASE);

//kakaran网站采集
$kakaran_array = array('http://rmt.kakaran.jp/ragnarok/','http://rmt.kakaran.jp/redstone/','http://rmt.kakaran.jp/lineage2/');

//获取游戏分类
$game_type = $_POST['game'];
$flag_check = $_POST['flag'];
if($flag_check!= ''){
  //在show里面点击更新
   $game_type = $game_type == '' ? 'FF11' : $game_type;
   $category = array('1'=>'buy','0'=>'sell');
  /*
   * jp 游戏各网站采集
   */
   include('collect_match.php');
   get_collect_res($game_type,$category,$other_array_match,$search_array_match);
}
function get_collect_res($game_type,$category,$other_array_match,$search_array_match,$show_log=true){
  $site_str = array();
  $url_str_array = array();
  $category_id_str_array = array();
  $url_kaka_array = array();
  $site_info = array();
  $log_str = '';
  /*以下是区分是手动更新的还是后台自动执行更新的判断
   * 买卖是数组是手动更新的,相反就是后台自动更新的
   * */
  //site
  $site_query = mysql_query("select site_id,site_name from site order by site_id asc");
  $i = 0;
  $j = 0;
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
  $rate_diff_site = array('');
  $category_type_all = $category;
  /*以下是正式采集*/
  $game_type=$game_type;
  foreach($category_type_all as $category_value){

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
        if(in_array($site_value,$rate_diff_site)){
          $tmp_url_arr = parse_url($url_array[$site_key]);
          $patch_arr = explode('&',$tmp_url_arr['query']);
          $file_name = '';
          foreach($patch_arr as $p_val){
            $tmp_patch_arr = explode('=',$p_val);
            if($tmp_patch_arr[0] == 'gamefilename'){
              $file_name = $tmp_patch_arr[1];
            }
          }
          if($file_name != ''){
            $search_url[$site_value.'_rate'] = $tmp_url_arr['scheme'].'://'.$tmp_url_arr['host'].'/rmt/'.$file_name.'html';
          }
        }
        $log_str .= date('H:i:s',time()).str_repeat(' ',5).$game_type.'--'.$category_value.'--'.$site_n[$site_key]."\n";
      }
    }
    //采集所有网站的数据
    $all_result = get_all_result($search_url);
    //通过正则获得所有网站的数据
    $all_site_info_array = get_info_array($all_result,$search_array,$rate_diff_site);
    //处理数据并存储到数据库
    $collect_res_url = array();
    $collect_res_name = array();
    foreach($all_site_info_array as $site_info_key => $site_info_arr){
      $temp_product_name = array();
      $site_value = array_search($site_info_key,$site);
      $category_id = $category_id_array[$site_value];

      foreach($site_info_arr['products_name'] as $p_name){
      	//处理产品名
        $p_name = str_replace('<br />',' ',$p_name);
        $p_name = str_replace('<br>',' ',$p_name);
        $temp_product_name[] = match_data_iimy($game_type,$category_value,$url_array[$site_value],$p_name);
      }
      $site_info_arr['products_name'] = $temp_product_name;
      if(in_array($site_info_key,$collect_site)){
      	$collect_res_url[$site_info_key]['url'] = $site_info_arr['url'];
      	$collect_res_url[$site_info_key]['products_name'] =  $site_info_arr['products_name'];
      	$collect_res_url[$site_info_key]['rate'] =  $site_info_arr['rate'];
        continue;
      }
      $site_value = array_search($site_info_key,$site);
      $category_id = $category_id_array[$site_value];
	  //如果是rmt1需要特殊处理
	  if($site_info_key=='rmt1.jp'){ 
              $site_info_arr['price'][$site_info_arr['section_1']['0']]= $site_info_arr['price_1'];
              unset($site_info_arr['section_1']);
              unset($site_info_arr['price_1']);
            if($category_value == 'buy'){
                $site_info_arr['price'][$site_info_arr['section_2']['0']]= $site_info_arr['price_2'];
                unset($site_info_arr['section_2']);
                unset($site_info_arr['price_2']);
                $site_info_arr['price'][$site_info_arr['section_3']['0']]= $site_info_arr['price_3'];
                unset($site_info_arr['section_3']);
                unset($site_info_arr['price_3']);
            }
           save2db($category_id,$site_value,$site_info_arr,$category_value,$game_type,$site_info_key);
          }else{
             save2db($category_id,$site_value,$site_info_arr,$category_value,$game_type);
      }
    }
    //采集网站的特殊处理
    //处理网站名
    
    
    //获得rmt 需要采集的产品数量 
    $product_sql =  "select * from product where 
       category_id in (select category_id from category where 
         site_id=(select site_id from site where 
           site_url like 'http://www.iimy.co.jp%' ) 
       and category_name='".$game_type."' 
       and game_server='jp' 
       and category_type = '".($category_value=='buy'?1:0)."')
       order by sort_order, product_name";
    $product_query = mysql_query($product_sql);
    $product_name_arr = array();
    while($product_row = mysql_fetch_array($product_query)){
      $product_name_arr[] = strtolower(trim($product_row['product_name']));
    }

    $search_url_list = array();
    $search_name_list = array();
    foreach($collect_res_url as $site_key => $site_product_url){
      foreach($site_product_url['url'] as $product_index => $url){
      	if(!in_array(strtolower(trim($collect_res_url[$site_key]['products_name'][$product_index])),$product_name_arr)){
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
        $search_rate_list[$product_index][$site_key] = $collect_res_url[$site_key]['rate'][$product_index];
      }
    }
    $i = 0;
    foreach($search_url_list as $sk => $sv){
      $tmp_url = array();
      foreach($sv as $s_k => $s_v){
        $tmp_url[] = $s_v;
      }
      $i++;
      if($i%2==0){
        sleep(1);
      }
      $all_result = get_all_result($tmp_url);
      //通过正则获得所有网站的数据
      $all_site_info_array = get_info_array($all_result,$other_array,$rate_diff_site);
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
          if(in_array(strtolower(trim(strip_tags($site_info['site_names'][$con_key]))),$rmt_name)){
            continue;
          }
          if($site_info['inventory'][$con_key] == 0){
            continue;
          }
          $price[] = str_replace(',','',$site_info['price'][$con_key]);
          $inventory[] = $site_info['inventory'][$con_key];
        }
        if($category_value =='sell'){
          $pos = array_search(max($price), $price);
          $t_price[] = $price[$pos];
          $t_inventory[] = $inventory[$pos];
          $key_temp = value_key($price[$pos],$price);
          $price = delete_keys($key_temp,$price);
          $inventory = delete_keys($key_temp,$inventory);
          //unset($price[$pos]);
          //unset($inventory[$pos]);
          $pos = array_search(max($price), $price);
          $t_price[] = $price[$pos];
          $t_inventory[] = $inventory[$pos];
          $key_temp = value_key($price[$pos],$price);
          $price = delete_keys($key_temp,$price);
          $inventory = delete_keys($key_temp,$inventory);
          //unset($price[$pos]);
          //unset($inventory[$pos]);
          $pos = array_search(max($price), $price);
          $t_price[] = $price[$pos];
          $t_inventory[] = $inventory[$pos];
          $key_temp = value_key($price[$pos],$price);
          $price = delete_keys($key_temp,$price);
          $inventory = delete_keys($key_temp,$inventory);
          //unset($price[$pos]);
          //unset($inventory[$pos]);
        }else{
          $pos = array_search(min($price), $price);
          $t_price[] = $price[$pos];
          $t_inventory[] = $inventory[$pos];
          $key_temp = value_key($price[$pos],$price);
          $price = delete_keys($key_temp,$price);
          $inventory = delete_keys($key_temp,$inventory);
          //unset($price[$pos]);
          //unset($inventory[$pos]);
          $pos = array_search(min($price), $price);
          $t_price[] = $price[$pos];
          $t_inventory[] = $inventory[$pos];
          $key_temp = value_key($price[$pos],$price);
          $price = delete_keys($key_temp,$price);
          $inventory = delete_keys($key_temp,$inventory);
          //unset($price[$pos]);
          //unset($inventory[$pos]);
          $pos = array_search(min($price), $price);
          $t_price[] = $price[$pos];
          $t_inventory[] = $inventory[$pos];
          $key_temp = value_key($price[$pos],$price);
          $price = delete_keys($key_temp,$price);
          $inventory = delete_keys($key_temp,$inventory);
          //unset($price[$pos]);
          //unset($inventory[$pos]);
        }
        foreach($t_price as $t_price_key=>$t_price_value){

          if($t_price_value == ''){

            $t_price[$t_price_key] = -1;
          }
        }
        foreach($t_inventory as $t_inventory_key=>$t_inventory_value){

          if($t_inventory_value == ''){

            $t_inventory[$t_inventory_key] = -1;
          }
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
        $log_str .= date('H:i:s',time()).str_repeat(' ',5).$game_type.'--'.$category_value.'--'.str_replace('1','',$log_name[0]).'-'.$i."\n";
      }
    }

  }

/*
 * na FF14 游戏采集
 */
  if($show_log){
    if($game_type == 'FF14'){
      tep_get_toher_collect($game_type);
    }
  }
  return $log_str;
}
//通过采集结果获得相关信息 返回数组 key = url_host value=array（价格等）
function get_info_array($curl_results,$search_array,$rate_diff_site){
  $url_info_array = array();
  $searched_url = array();
  foreach($curl_results as $result){
    $url_info = parse_url($result['info']['url']);
    $search_key = $url_info['host'];
    if(in_array($search_key,$rate_diff_site)&&!in_array($search_key,$searched_url)){
      $searched_url[] = $search_key;
      $res = $result['results'];
      continue;
    }else if(in_array($search_key,$searched_url)){
      $res .= $result['results'];
    }else{
      $res = $result['results'];
    }
    $encode_array = array('UTF-8','EUC-JP','Shift_JIS','ISO-2022-JP');
    $encode = mb_detect_encoding($res,$encode_array);
    if(strtolower($encode) != 'UTF-8'){
      $res = mb_convert_encoding($res,'UTF-8',$encode_array);
    }
    $search_info_array = $search_array[$search_key];
    $res_search_array = array();
    foreach($search_info_array as $key => $value){
      preg_match_all('/'.$value.'/is',$res,$temp_array);
      if($key == 'rate'){
        $res_search_array[$key] = strip_tags($temp_array[0][count($temp_array[0])-1]);
      }else{
        foreach($temp_array[1] as $k => $v){ 
          if($v==''||trim($v)==''||strip_tags($temp_array[1][$k])==''){
            $temp_array[1][$k] = strip_tags($temp_array[2][$k]);
          }
        }
        $res_search_array[$key] = $temp_array[1];
      }
    }
    $url_info_array[$search_key] = $res_search_array;
  }
  return $url_info_array;
}


function get_fetch_by_url($url,$search_match){
  $result = '';
  $result_array = array();
    $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url); //设置访问的url地址 
  //curl_setopt($ch,CURLOPT_HEADER,1); //是否显示头部信息
  curl_setopt($ch, CURLOPT_TIMEOUT, 20); //设置超时  
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //设置连接等待时间  
  curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_); //用户访问代理 User-Agent
  curl_setopt($ch, CURLOPT_REFERER,$url); //设置 referer 
  curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1); //跟踪301
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回结果
  $result = curl_exec($ch);
  curl_close($ch);
  if(!$result){
    return false;
  }
  $encode_array = array('UTF-8','EUC-JP','Shift_JIS','ISO-2022-JP');
  $encode = mb_detect_encoding($result,$encode_array);
  if(strtolower($encode) != 'UTF-8'){
      $result = mb_convert_encoding($result,'UTF-8',$encode_array);
  }
  $search_array = array();
  foreach($search_match as $key => $value){
    preg_match_all('/'.$value.'/is',$result,$temp_array);
    if($key == 'rate'){
      $search_array[$key] = strip_tags($temp_array[0][0]);
    }else{
      foreach($temp_array[1] as $k => $v){ 
        if($v==''||trim($v)==''){
          $temp_array[1][$k] = strip_tags($temp_array[2][$k]);
        }
      }
      $search_array[$key] = $temp_array[1];
    }
  }
  $result_array[] = $search_array;
  return $result_array;

}
//并行采集所有URL 
function get_all_result($urls) {
  $queue = curl_multi_init();
  $map = array();
  foreach ($urls as $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); //设置访问的url地址 
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); //设置超时  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); //设置连接等待时间  
    curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_); //用户访问代理 User-Agent
    curl_setopt($ch, CURLOPT_REFERER,$url); //设置 referer 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回结果
    curl_multi_add_handle($queue, $ch);
    $map[(string) $ch] = $url;
  }
  $responses = array();
  do{
    while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ;
      if ($code != CURLM_OK) { break; }
      // a request was just completed -- find out which one
        while ($done = curl_multi_info_read($queue)) {
          // get the info and content returned on the request
          $info = curl_getinfo($done['handle']);
          $error = curl_error($done['handle']);
          $results = curl_multi_getcontent($done['handle']);
          $responses[$map[(string) $done['handle']]] = compact('info', 'error', 'results'); 
          // remove the curl handle that just completed
          curl_multi_remove_handle($queue, $done['handle']);
          curl_close($done['handle']);
        }
        // Block for data in / output; error handling is done by curl_multi_exec
        if ($active > 0) {
            curl_multi_select($queue, 0.5);
        }
  
  } while ($active);
  curl_multi_close($queue);
  return $responses;
}



//根据 category id 和 获得的结果 把数据存储到数据库
function save2db($category_id,$site_value,$result_str,$category_value,$game_type,$site_name=''){
  $category_update_query = mysql_query("update category set collect_date=now() where category_id='".$category_id."'");
  $rate_arr = tep_get_rate(SBC2DBC($result_array['rate'][0]));
  $result_array[0] = $result_str;
  $result_array[0]['products_name'] = array_unique($result_array[0]['products_name']);
  //当获取的数据商品名称为空(或这个页面没有数据)
  if(empty($result_array[0]['products_name'])){
    mysql_query("update product set is_error=1 where category_id='".$category_id."'");
  }
  foreach($result_array[0]['products_name'] as $product_key=>$value){
    $t_site_value = $site_value;
    if($site_name == 'rmtrank.com'){
      $t_site_value = 4;
    }
    if($site_name == 'rmt.kakaran.jp'){
      $t_site_value = 5;
    }
$value=match_data_iimy($game_type,$category_value,$url_array[$site_value],$value);
//rmt1
if($value!='' && $site_name=='rmt1.jp'){
   $price_info = get_price_info_new($result_array,$category_value,$game_type,$site_name,$product_key,$value);
}else if($value!='' && $site_name!='rmt1.jp'){
   $price_info = tep_get_price_info($result_array,$category_value,$game_type,$t_site_value,$product_key,$value);
}
//    $price_info = tep_get_price_info($result_array,$category_value,$game_type,$t_site_value,$product_key,$value);
    $value = $price_info['value'];
    $result_str = $price_info['result_str'];
    $result_inventory = $price_info['result_inventory'];
    $sort_order = 0;
  //判断数据库是否存在相同名称相同category_id 的商品
    $search_query = mysql_query("select product_id from product where category_id='".$category_id."' and product_name='".trim($value)."'");
  
  //当前游戏主站所有商品名称
    $c_type = $category_value=='buy'?'1':'0';
    $product_all_sql= mysql_query("select * from product p,category c where p.category_id=c.category_id and category_name='".$game_type."' and category_type='".$c_type."' and c.game_server='jp' and c.site_id=7");
    while($product_row = mysql_fetch_array($product_all_sql)){
      $product_name_list_array[]=strtolower(trim($product_row['product_name']));
    }
    $allow_insert_mark = 0;
    if(in_array(strtolower(trim($value)),$product_name_list_array) && !empty($product_name_list_array)){
      $allow_insert_mark = 1;
    }
    //最新采集的商品名称
    $product_new[] = trim($value);
    //有,则更新 没有,则添加
    if(mysql_num_rows($search_query) == 1 && $allow_insert_mark == 1){
      $products_query = mysql_query("update product set is_error=0, product_price='".$result_str."',product_inventory='".$result_inventory."',sort_order='".$sort_order."' where category_id='".$category_id."' and product_name='".trim($value)."'");
    }else{
      if($value!='' && $allow_insert_mark = 1){
        $products_query = mysql_query("insert into product values(NULL,'".$category_id."','".trim($value)."','".$result_str."','".$result_inventory."','".$sort_order."',0)");
      }
    }
  }
  if($site_name==''){
  //数据库原有的商品名称
  $search_query = mysql_query("select product_name from product where category_id='".$category_id."'");
  $product_old_list[] = array();
  while($row_tep = mysql_fetch_array($search_query)){
     $product_old_list[] = $row_tep['product_name'];
  }
  //新获取的数据已经不包含数据库的数据,删除
  foreach($product_old_list as $product_old_name){
    if(!in_array($product_old_name,$product_new) && !empty($product_new)){
      $products_query = mysql_query("delete from product where category_id='".$category_id."' and product_name='".$product_old_name."'");
    }
  }
  }
}



//过滤逗号
function my_filter($value){

  return str_replace(',','',$value); 
}

//通过值取键名
function value_key($value,$array){

  $key = array();
  foreach($array as $k=>$v){

    if($value == $v){

      $key[] = $k;
    }
  }
  sort($key);
  return $key;
}
//通过键名的数组删除相应的值
function delete_keys($keys,$array){

  foreach($keys as $value){

    unset($array[$value]);
  }
  return $array;
}
function save_site_res($game_type,$category_value,$category_id_array,$site_value,$url_array,$search_array,$site_key,$sleep_flag=false,$other_array){
  if($url_array[$site_value] == ''){
    $collect_error_array[] = array('time'=>time(),'game'=>$game_type,'type'=>$category_value,'site'=>$site_value,'url'=>$url_array[$site_value]);
    return false;
  }
   if(strpos($url_array[$site_value],'pastel-rmt.jp')||strpos($url_array[$site_value],'www.rmt-king.com') || strpos($url_array[$site_value],'rmt1')){
      $curl_flag=0;
   }else{
      $curl_flag=1;
   }
    if($url_array[$site_value]=='//http://rmtrank.com/777town+index.htm'){
      $url_array[$site_value] = str_replace('//http://rmtrank.com/777town+index.htm','http://rmtrank.com/777town+index.htm',$url_array[$site_value]);
    }
    if(class_exists('Spider')){
      $result = new Spider($url_array[$site_value],'',$search_array[$site_key],$curl_flag);
      $result_array = $result->fetch();
      if(!$result->collect_flag){

        $collect_error_array[] = array('time'=>time(),'game'=>$game_type,'type'=>$category_value,'site'=>$site_value,'url'=>$url_array[$site_value]);
      }else{
        $collect_res = date('H:i:s',time()).str_repeat(' ',5).$game_type.'--'.$category_value;
      }
      /*
      //处理NA区kakaran
      if($game_type == 'FF14' && strpos($url_array[$site_value],'rmt.kakaran.jp')){

        $result_na = new Spider('http://rmt.kakaran.jp/ff14_naeu/','',$search_array[$site_key],$curl_flag);
        $result_array_na = $result_na->fetch();
        if(!$result_na->collect_flag){

          $collect_error_array[] = array('time'=>time(),'game'=>$game_type,'type'=>$category_value,'site'=>$site_value,'url'=>'http://rmt.kakaran.jp/ff14_naeu/');
        }else{
          $collect_res = date('H:i:s',time()).str_repeat(' ',5).$game_type.'--'.$category_value;
        }
        foreach($result_array_na[0]['products_name'] as $result_na_key=>$result_na_value){


          $result_array[0]['products_name'][] = $result_na_value;
          $result_array[0]['url'][] = $result_array_na[0]['url'][$result_na_key];
        }
      }
      */
    }else{
      $result_array = get_fetch_by_url($url_array[$site_value],$search_array[$site_key]);
      if($result_array){
        $collect_res = date('H:i:s',time()).str_repeat(' ',5).$game_type.'--'.$category_value;
      }
      /*
      //处理NA区kakaran
      if($game_type == 'FF14' && strpos($url_array[$site_value],'rmt.kakaran.jp')){

        $result_array_na = get_fetch_by_url('http://rmt.kakaran.jp/ff14_naeu/',$search_array[$site_key]);
        if($result_array_na){
          $collect_res = date('H:i:s',time()).str_repeat(' ',5).$game_type.'--'.$category_value;
        }
        foreach($result_array_na[0]['products_name'] as $result_na_key=>$result_na_value){


          $result_array[0]['products_name'][] = $result_na_value;
          $result_array[0]['url'][] = $result_array_na[0]['url'][$result_na_key];
        }
      }
      */
    }
    if($result_array[0]['url']){
      $collect_res = array();
      $url_kaka_array[] = 'rmt.kakaran.jp'.$site_value;
      //取出单价i
      $kaka_array = array();
      foreach($result_array[0]['url'] as $key=>$url){
        if($url==''){
          continue;
        }
        if($sleep_flag){
          sleep(2);
        }
        if(strpos($url_array[$site_value],'rmt.kakaran.jp')){
          if($sleep_flag){
            sleep(1);
          }
          if($category_value=='sell'){
            $url = str_replace('buy','sell',$url);
          }
          $url = $url.'?s=bank_transfer';
          $search_url = "http://rmt.kakaran.jp".$url;
        }else if(strpos($url_array[$site_value],'rmtrank.com')){
          sleep(1);

          $search_url = preg_replace('/\.htm$/','+sort+price.htm',$url);
          if($category_value=='sell'){
            $search_url = str_replace('content_id+1','content_id+2',$search_url);
          }
        }
        if(class_exists('Spider')){
          $result_kaka = new Spider($search_url,'',$other_array[$site_key],$curl_flag);
           $result_array_kaka = $result_kaka->fetch();
           if(!$result_kaka->collect_flag){
             $collect_error_array[] = array('time'=>time(),'game'=>$game_type,'type'=>$category_value,'site'=>$site_value,'url'=>$search_url);
           }else{
             $collect_res[] = date('H:i:s',time()).str_repeat(' ',5).$game_type.'--'.$category_value;
           }
         }else{
           $result_array_kaka = get_fetch_by_url($search_url,$other_array[$site_key]);
           if($result_array_kaka){
              $collect_res[] = date('H:i:s',time()).str_repeat(' ',5).$game_type.'--'.$category_value;
           }
         }
         //过滤RMT网站数据
         $kaka_name = array(); 
         foreach($result_array_kaka[0]['site_names'] as $vname){
               preg_match_all("#(?:<img .*?>){0,1}<a .*?>(.*?)<\/a>#",$vname,$temp_array);
               if(!empty($temp_array[1])){
                   $kaka_name[] = $temp_array[1][0];
               }else{
                   $kaka_name[] = $vname;
               }
         }
          $rmt_array = array();
          $rmt_name = array('ジャックポット','ゲームマネー','カメズ','学園','FF14-RMT','RedStone-RMT','GM-Exchange','ワールドマネー','Itemdepot','GM-Exchange');
          foreach($kaka_name as $kaka_key=>$kaka_value){

            foreach($rmt_name as $rmt_key=>$rmt_value){

              if(strpos($kaka_value,$rmt_value)!==false){

                $rmt_array[] = $kaka_key;
              }
            }
          }
         //根据游戏分类来获取网站名称
         $category_site_query = mysql_query("select * from category where category_id='".$category_id_array[$site_value]."'");
         $category_site_array = mysql_fetch_array($category_site_query);
         $site_name_query = mysql_query("select * from site where site_id='".$category_site_array['site_id']."'");
         $site_name_array = mysql_fetch_array($site_name_query);
         $result_inventory = $result_array_kaka[0]['inventory'];
         foreach($result_inventory as $result_inventory_key=>$result_inventory_value){

           if(trim($result_inventory_value) == '' || $result_inventory_value == 0){
             $rmt_array[] = $result_inventory_key;
           }
         }
         $result_price = $result_array_kaka[0]['price']; 
         foreach($rmt_array as $rmt_value){

           unset($result_price[$rmt_value]);
         }
         $result_price = array_map("my_filter",$result_price);

         //根据商品价格正排序，来获取前3个商品价格及对应的商品库存
         if($category_value=='buy'){
           asort($result_price);
         }else if($category_value=='sell'){

           arsort($result_price);
         }

         $frist_price_value = '';
         $frist_inventory_value = '';
         $two_price_value = '';
         $two_inventory_value = '';
         $three_price_value = '';
         $three_inventory_value = '';
         $i = 0;
         $keys = array();
         foreach($result_price as $key=>$value){

           if($i == 0){
              $keys = value_key($value,$result_price);
              $frist_price_value = $result_price[$keys[0]];
              unset($result_price[$keys[0]]);
              $frist_inventory_value = $result_inventory[$keys[0]];
           }
           if($i == 1){
              $keys = value_key($value,$result_price);
              $two_price_value = $result_price[$keys[0]];
              unset($result_price[$keys[0]]);
              $two_inventory_value = $result_inventory[$keys[0]];
           }
           if($i == 2){
              $keys = value_key($value,$result_price);
              $three_price_value = $result_price[$keys[0]];
              unset($result_price[$keys[0]]);
              $three_inventory_value = $result_inventory[$keys[0]];
           }
           $i++;
           if($i == 3){

            break;
           }
         }
         //根据不同的网站，来获取相对应的商品价格及库存
         if($site_name_array['site_name'] == 'カカラン1'||$site_name_array['site_name'] == 'ランキング1'){
           $result_array[0]['price'][] =  $frist_price_value;
           $result_array[0]['inventory'][] = $frist_inventory_value;
         }
         if($site_name_array['site_name'] == 'カカラン2'||$site_name_array['site_name'] == 'ランキング2'){
           $result_array[0]['price'][] =  $two_price_value;
           $result_array[0]['inventory'][] = $two_inventory_value;
         }
         if($site_name_array['site_name'] == 'カカラン3'||$site_name_array['site_name'] == 'ランキング3'){
           $result_array[0]['price'][] =  $three_price_value;
           $result_array[0]['inventory'][] = $three_inventory_value;
         }
     }
   }
  return $collect_res;
}

function tep_get_toher_collect($game_type){
  require_once('class/spider.php');
  $na_url_array = array();
  $na_category_id_array = array();
  $na_category_type_array = array();
  $jp_category_array = array();
  $site_category_array = array();

  $na_category_query = mysql_query("select * from category where category_name='FF14' and game_server='na'");
  while($na_category_array = mysql_fetch_array($na_category_query)){

    $na_url_array[] = $na_category_array['category_url'];
    $na_category_id_array[] = $na_category_array['category_id'];
    $na_category_type_array[] = $na_category_array['category_type'] == 1 ? 'buy' : 'sell';
    $site_category_array[] = $na_category_array['site_id'];
  }
  //FF14 kakaran jp
  $jp_categorys_query = mysql_query("select * from category where category_url='http://rmt.kakaran.jp/ff14/'");
  while($jp_categorys_array = mysql_fetch_array($jp_categorys_query)){

    $jp_categorys_array['category_type'] = $jp_categorys_array['category_type'] == 1 ? 'buy' : 'sell';
    $jp_category_array[$jp_categorys_array['site_id']][$jp_categorys_array['category_type']] = $jp_categorys_array['category_id'];
  }
  $wm_category_array = array();
  //FF14 夢幻 WM
  $na_categorys_query = mysql_query("select * from category where `category_name`='FF14' and `game_server`='jp' and `site_id` in (1,4)");
  while($na_categorys_array = mysql_fetch_array($na_categorys_query)){

    $na_categorys_array['category_type'] = $na_categorys_array['category_type'] == 1 ? 'buy' : 'sell';
    $wm_category_array[$na_categorys_array['site_id']][$na_categorys_array['category_type']] = $na_categorys_array['category_id'];
  }

  $na_search_array  = array(array('products_name'=>'<td height=\'24\' class=\'border03 border04\'>([a-zA-Z]+)\(.*?\)\-rmt<\/td>',
                        '1-80'=>'<td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        '81-500'=>'<td class=\'border03 border04\'>[0-9,.]*?円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td><td class=\'border03 border04\'>([0-9,.]*?)円<span style=\'margin-right:5px\'><\/span>[0-9,.]*?WM<\/td>', 
                        'inventory'=>'<td class=\'border03 border04\' style=\'color:Red;font-weight:bold;\'>.*?<\/td><td class=\'border03 border04\'>(.*?)<\/td>'
                      ),
                      array('products_name'=>'<td rowspan="3"><span>([a-zA-Z]+)\(?L?E?G?A?C?Y?.*?\)?<\/span><\/td>',
                      '1-9'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>',
                      '10-29'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td><td>[0-9,.]+?円<\/td>',
                      '30-'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>([0-9,.]+?)円<\/td>',
                      'inventory'=>'<td rowspan="3" class="ipayment">銀行振込<br\/>クレジット決済<br\/>WebMoney<\/td>.*?<td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td><td>[0-9,.]+?円<\/td>.*?<td rowspan="3">(.*?)<\/td>' 
                    ),
                    array('products_name'=>'<td class="th"><a href=".*?">([a-zA-Z]+)\(?L?E?G?A?C?Y?.*?\)?<\/a><\/td>',
                      'price'=>'<td>([0-9.,]+)円<\/td>.*?<td>[0-9.,]+Pt<\/td>.*?<td>.*?<\/td>',
                      'inventory'=>'<td>[0-9.,]+円<\/td>.*?<td>[0-9.,]+Pt<\/td>.*?<td>(.*?)<\/td>' 
                    ),
                  );
  $kakaran_array = array('buy'=>array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+)\(?.*?\)?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                      ),
                      'sell'=>array( 'products_name'=>'<td><a href=".*?">([a-zA-Z]+)\(?.*?\)?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',
                        'url'=>'<td><a href="(.*?)">.*?<\/a><\/td>.*?<td>[0-9,.]*?円<\/td>',  
                        ) 
                      );
  $other_array = array('buy'=>array( 
                        'site_names'=>'<td class="position-relative">(.*?)<\/td><td class="compare"><span>.*?<\/span><\/td><td class="price sort">([0-9,.]+)円<\/td><td class="price">.*?<\/td>', 
                        'price'=>'<td class="price sort">([0-9,.]+)円<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                        'inventory'=>'<td class="price sort">[0-9,.]+円<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="price">.*?<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                      ),
                      'sell'=>array( 
                        'site_names'=>'<td class="position-relative">(.*?)<\/td><td class="compare"><span>.*?<\/span>', 
                        'price'=>'<td class="price sort">([0-9,.]+)円<\/td><td class="stock"><span class="number">[0-9,.]+<\/span>口<\/td>',
                        'inventory'=>'<td class="price sort">[0-9,.]+円<\/td><td class="stock"><span class="number">([0-9,.]+)<\/span>口<\/td>', 
                        )  
                    );

  //开始采集数据
  foreach($na_url_array as $key=>$value){

    if($value == ''){continue;}
    if(strpos($value,'rmt.kakaran.jp')){
       
      $result = new Spider($value,'',$kakaran_array[$na_category_type_array[$key]]);
    }else{
      $result = new Spider($value,'',$na_search_array[$key]);
    }
    $result_array = $result->fetch();
    //start
    if($result_array[0]['url']){
      $collect_res = array();
      foreach($result_array[0]['url'] as $k=>$url){
        if($url==''){
          continue;
        }
        if(strpos($value,'rmt.kakaran.jp')){
          if($na_category_type_array[$key]=='sell'){
            $url = str_replace('buy','sell',$url);
          }
          $url = $url.'?s=bank_transfer';
          $search_url = "http://rmt.kakaran.jp".$url;
        }
        if(class_exists('Spider')){
           $result_kaka = new Spider($search_url,'',$other_array[$na_category_type_array[$key]]);
           $result_array_kaka = $result_kaka->fetch();
           if(!$result_kaka->collect_flag){
             $collect_error_array[] = array('time'=>time(),'game'=>$game_type,'type'=>$category_value,'site'=>$site_value,'url'=>$search_url);
           }else{
             $collect_res[] = date('H:i:s',time()).str_repeat(' ',5).$game_type.'--'.$category_value;
           }
         }else{
           $result_array_kaka = get_fetch_by_url($search_url,$na_category_type_array[$key]);
           if($result_array_kaka){
              $collect_res[] = date('H:i:s',time()).str_repeat(' ',5).$game_type.'--'.$category_value;
           }
         }
         //过滤RMT网站数据
         $kaka_name = array(); 
         foreach($result_array_kaka[0]['site_names'] as $vname){
               preg_match_all("#(?:<img .*?>){0,1}<a .*?>(.*?)<\/a>#",$vname,$temp_array);
               if(!empty($temp_array[1])){
                   $kaka_name[] = $temp_array[1][0];
               }else{
                   $kaka_name[] = $vname;
               }
         }
          $rmt_array = array();
          $rmt_name = array('ジャックポット','ゲームマネー','カメズ','学園','FF14-RMT','RedStone-RMT','GM-Exchange','ワールドマネー','Itemdepot','GM-Exchange');
          foreach($kaka_name as $kaka_key=>$kaka_value){

            foreach($rmt_name as $rmt_key=>$rmt_value){

              if(strpos($kaka_value,$rmt_value)!==false){

                $rmt_array[] = $kaka_key;
              }
            }
          }

         //根据游戏分类来获取网站名称
         $category_site_query = mysql_query("select * from category where category_id='".$jp_category_array[$site_category_array[$key]][$na_category_type_array[$key]]."'");
         $category_site_array = mysql_fetch_array($category_site_query);
         $site_name_query = mysql_query("select * from site where site_id='".$category_site_array['site_id']."'");
         $site_name_array = mysql_fetch_array($site_name_query);
         $result_inventory = $result_array_kaka[0]['inventory'];
         foreach($result_inventory as $result_inventory_key=>$result_inventory_value){

           if(trim($result_inventory_value) == '' || $result_inventory_value == 0){
             $rmt_array[] = $result_inventory_key;
           }
         }
         $result_price = $result_array_kaka[0]['price']; 
         foreach($rmt_array as $rmt_value){

           unset($result_price[$rmt_value]);
         }
         $result_price = array_map("my_filter",$result_price);

         //根据商品价格正排序，来获取前3个商品价格及对应的商品库存
         if($na_category_type_array[$key]=='buy'){
           asort($result_price);
         }else if($na_category_type_array[$key]=='sell'){

           arsort($result_price);
         }

         $frist_price_value = '';
         $frist_inventory_value = '';
         $two_price_value = '';
         $two_inventory_value = '';
         $three_price_value = '';
         $three_inventory_value = '';
         $i = 0;
         $keys = array();
         foreach($result_price as $kk=>$val){

           if($i == 0){
              $keys = value_key($val,$result_price);
              $frist_price_value = $result_price[$keys[0]];
              //$result_price = delete_keys($keys,$result_price);
              unset($result_price[$keys[0]]);
              $frist_inventory_value = $result_inventory[$keys[0]];
           }
           if($i == 1){
              $keys = value_key($val,$result_price);
              $two_price_value = $result_price[$keys[0]];
              //$result_price = delete_keys($keys,$result_price);
              unset($result_price[$keys[0]]);
              $two_inventory_value = $result_inventory[$keys[0]];
           }
           if($i == 2){
              $keys = value_key($val,$result_price);
              $three_price_value = $result_price[$keys[0]];
              //$result_price = delete_keys($keys,$result_price);
              unset($result_price[$keys[0]]);
              $three_inventory_value = $result_inventory[$keys[0]];
           }
           $i++;
           if($i == 3){

            break;
           }
         }
         //根据不同的网站，来获取相对应的商品价格及库存
         if($site_name_array['site_name'] == 'カカラン1'){
           $result_array[0]['price'][] =  $frist_price_value;
           $result_array[0]['inventory'][] = $frist_inventory_value;
         }
         if($site_name_array['site_name'] == 'カカラン2'){
           $result_array[0]['price'][] =  $two_price_value;
           $result_array[0]['inventory'][] = $two_inventory_value;
         }
         if($site_name_array['site_name'] == 'カカラン3'){
           $result_array[0]['price'][] =  $three_price_value;
           $result_array[0]['inventory'][] = $three_inventory_value;
         }
     }
     $na_category_id_array[$key] = $jp_category_array[$site_category_array[$key]][$na_category_type_array[$key]];
    }else{ 

    $na_category_id_array[$key] = $wm_category_array[$site_category_array[$key]][$na_category_type_array[$key]];
    }
    //end
    //$na_category_id_array[$key] = $wm_category_array[$site_category_array[$key]][$na_category_type_array[$key]];
    $category_update_query = mysql_query("update category set collect_date=now() where category_id='".$na_category_id_array[$key]."'");

    foreach($result_array[0]['products_name'] as $products_key=>$products_value){
      if($key <=2){
        preg_match('/([0-9,]+).*?口/is',$result_array[0]['inventory'][$products_key],$inventory_array);
      }
      if($key == 0){

        if($inventory_array[0] != ''){

          if($inventory_array[0] >= 1 && $inventory_array[0] <= 80){

            $price = $result_array[0]['1-80'][$products_key];
          }else if($inventory_array[0] >= 81 && $inventory_array[0] <= 500){

            $price = $result_array[0]['81-500'][$products_key];
          }
          $result_inventory = $inventory_array[0];
        }else{
          $price = $result_array[0]['1-80'][$products_key]; 
          $result_inventory = 0;
        }
      }else if($key == 1){

        if($inventory_array[0] != ''){

          if($inventory_array[0] >= 1 && $inventory_array[0] <= 9){

            $price = $result_array[0]['1-9'][$products_key];
          }else if($inventory_array[0] >= 10 && $inventory_array[0] <= 29){

            $price = $result_array[0]['10-29'][$products_key];
          }else{
             $price = $result_array[0]['30-'][$products_key]; 
          }
          $result_inventory = $inventory_array[0];
        }else{
          $price = $result_array[0]['1-9'][$products_key]; 
          $result_inventory = 0;
        }
      }else if($key == 2){

          $price = $result_array[0]['price'][$products_key]; 
          if($inventory_array[0] != ''){
       
            $result_inventory = $inventory_array[0];
          }else{
        
            $result_inventory = 0;
          }
      }else{
        $price = $result_array[0]['price'][$products_key]; 
          if($result_array[0]['inventory'][$products_key] != ''){
       
            $result_inventory = str_replace(',','',$result_array[0]['inventory'][$products_key]);
          }else{
        
            $result_inventory = 0;
          } 
      }

      //数据入库
      $search_query = mysql_query("select product_id from product where category_id='".$na_category_id_array[$key]."' and product_name='".trim($products_value)."'");

      if(mysql_num_rows($search_query) == 1){

        $products_query = mysql_query("update product set is_error='0', product_price='".$price."',product_inventory='".$result_inventory."'where category_id='".$na_category_id_array[$key]."' and product_name='".trim($products_value)."'");
      }else{

        $products_query = mysql_query("insert into product values(NULL,'".$na_category_id_array[$key]."','".trim($products_value)."','".$price."','".$result_inventory."',0,0)");
      } 
    }
  }
}

function tep_get_price_info($result_array,$category_value,$game_type,$site_value,$product_key,$value){
        if($site_value == 0){//夢幻
          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
          switch($game_type){
            case 'FF11':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=99){
                    $price = $result_array[0]['1-99'][$product_key]*10; 
                  }else{
                    $price = $result_array[0]['100-10000'][$product_key]*10; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-99'][$product_key]*10; 
                  $result_inventory = 0;
                }
              }
              $result_str = $price;
            break;
            case 'DQ10':
                 if($inventory_array[0] != ''){
                    if($inventory_array[0] >= 101){
                        $price = $result_array[0]['101-9999'][$product_key]; 
                    }else{
                        $price = $result_array[0]['51-100'][$product_key]; 
                    } 
                    $result_inventory = $inventory_array[0];
                 }else{
                    $price = $result_array[0]['51-100'][$product_key]; 
                    $result_inventory = 0;
                 }
                 $result_str = $price;

              break;
            case 'RS':
               if(strpos($result_array[0]['inventory'][$product_key],'a')){
                  $inventory_array[0]=0;
               }

              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 30 && $inventory_array[0] <=10000){

                  $price = $result_array[0]['30-10000'][$product_key]; 
                }else{
                  $price = $result_array[0]['1-29'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['1-29'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
             case 'L2':
              if(strpos($result_array[0]['inventory'][$product_key],'a')){
  		  $inventory_array[0]=0;
              }
              if($inventory_array[0] != ''){
                if($inventory_array[0] >=5 && $inventory_array[0] <=30){
                  $price = $result_array[0]['5-30'][$product_key];
                }else{
                  $price = $result_array[0]['31-500'][$product_key];
                }
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['5-30'][$product_key];
                $result_inventory = 0; 
              }
              $result_str = $price;
              break;
            case 'FF14':
if(strpos($result_array[0]['inventory'][$product_key],'a')){
    $inventory_array[0]=0;
 }
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 21 && $inventory_array[0] <=500){

                  $price = $result_array[0]['21-500'][$product_key]; 
                }else{
                  $price = $result_array[0]['6-20'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['6-20'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
              break;
            case 'RO':
              if(strpos($result_array[0]['inventory'][$product_key],'a')){
                  $inventory_array[0]=0;
              }

              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 100 && $inventory_array[0] <=9999){

                  $price = $result_array[0]['100-9999'][$product_key]; 
                }else{
                  $price = $result_array[0]['10-99'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0]/100;
              }else{
                $price = $result_array[0]['10-99'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price*100;
              break;
             case 'ARAD':
             if($category_value =='buy'){
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 101){

                  $price = $result_array[0]['101-1000'][$product_key]; 
                }else if($inventory_array[0] >= 21&&$inventory_array[0] <100){
                  $price = $result_array[0]['21-100'][$product_key]; 
                }else{
                  $price = $result_array[0]['5-20'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['5-20'][$product_key]; 
                $result_inventory = 0;
               }
              $result_str = $price;
          }
         break;
             case 'nobunaga':
              $price = $result_array[0]['price'][$product_key];
              if($category_value == 'sell'){
                $result_str = $price;
                if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0]/10;
                }else{
                    $result_inventory = 0; 
                } 
              }else{
                $result_str = $price*10;
                if($inventory_array[0] != ''){
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_inventory = 0; 
                } 
              }
              break;
             case 'PSO2':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/100;
              }else{
                $result_inventory = 0; 
              } 
              break;
            case 'L1':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
              } 
              break;
            case 'TERA':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10000;
              }else{
                $result_inventory = 0; 
              } 
              break;

           case 'AION':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/100;
              }else{
                $result_inventory = 0; 
              } 
              break;

            case 'CABAL':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/100;
              }else{
                $result_inventory = 0; 
              } 
              break;
             case 'WZ':
             if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                 if($inventory_array[0] >= 1 && $inventory_array[0] <=50){
                   $price = $result_array[0]['1-50'][$product_key]; 
                 }else{
                   $price = $result_array[0]['51-5000'][$product_key]; 
                 } 
                 $result_inventory = $inventory_array[0]/10;

               }else{
                 $price = $result_array[0]['1-50'][$product_key]; 
                 $result_inventory = 0;
               }
                 $result_str = $price*10;

             }else{
               $price = $result_array[0]['price'][$product_key];
               $result_str = $price;
               $result_inventory = $result_array[0]['inventory'][$product_key]/10;
             }
             break;
             case 'latale':
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price*10;
                  if($result_array[0]['inventory'][$product_key] != ''){
                    $result_inventory = $result_array[0]['inventory'][$product_key]/1000;
                  }else{
                    $result_inventory = 0; 
                  }
              break;
             case 'blade':
             if($category_value == 'buy'){

               if($inventory_array[0] != ''){
                if($inventory_array[0] >= 41){
                  $price = $result_array[0]['41-9999'][$product_key]; 
                }else{
                  $price = $result_array[0]['41-9999'][$product_key]; 
                } 
                 $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-40'][$product_key]; 
                  $result_inventory = 0;
                }
                $result_str = $price*10;
              }
                break;
             case 'megaten':
             if($category_value == 'buy'){

               if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=100){
                  $price = $result_array[0]['1-100'][$product_key]; 
                }else{
                  $price = $result_array[0]['101-9999'][$product_key]; 
                } 
                 $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-100'][$product_key]; 
                  $result_inventory = 0;
                }
                $result_str = $price*10;

             }else{
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              $result_inventory = $result_array[0]['inventory'][$product_key]/10;
             }
             break;
             case 'EWD':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price*10;
              if($result_array[0]['inventory'][$product_key]!= ''){
                $result_inventory = $result_array[0]['inventory'][$product_key]/100;
              }else{
                $result_inventory = 0; 
              } 
              break;
             case 'LH':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($result_array[0]['inventory'][$product_key]!= ''){
                $result_inventory = $result_array[0]['inventory'][$product_key]/100;
              }else{
                $result_inventory = 0; 
              } 
              break;
             case 'HR':
             if($category_value == 'buy'){

               if($inventory_array[0] != ''){
                 if($inventory_array[0] >= 1 && $inventory_array[0] <=10){
                   $price = $result_array[0]['1-10'][$product_key]; 
                 }else{
                   $price = $result_array[0]['11-2000'][$product_key]; 
                 } 
                   $result_inventory = $inventory_array[0];
               }else{
                    $price = $result_array[0]['1-10'][$product_key]; 
                    $result_inventory = 0;
               }
                   $result_str = $price;

             }else{
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              $result_inventory = $result_array[0]['inventory'][$product_key]/10;
             }
             break;
             case 'AA':
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0]/100;
                  }else{
                    $result_inventory = 0; 
                  }
              break;
             case 'ThreeSeven':
                if($category_value == 'buy'){
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0];
                  }else{
                    $result_inventory = 0; 
                  }
                 }else{
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price/10;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0]*10;
                  }else{
                    $result_inventory = 0; 
                  }

                }
              break;
             case 'ECO':
                  preg_match('/[0-9,]+(口|M)?/is',trim($result_array[0]['inventory'][$product_key]),$inventory_array);
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0];
                  }else{
                    $result_inventory = 0; 
                  }
              break;
             case 'FNO':
                 if($inventory_array[0] >= 1 && $inventory_array[0] <=10){
                   $price = $result_array[0]['1-10'][$product_key]; 
                 }else{
                   $price = $result_array[0]['11-100'][$product_key]; 
                 } 
                   $result_inventory = $inventory_array[0]*10;
                  $result_str = $price/10;
              break;
             case 'SUN':
                   $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0]/100;
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price;
              break;
             case 'talesweave':
                   $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0];
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price;
              break;
             case 'MU':
                   $price = $result_array[0]['price'][$product_key]; 
          preg_match('/[0-9,]+(口|M)?/is',trim($result_array[0]['inventory'][$product_key]),$inventory_array);
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0];
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price;
              break;
             case 'C9':
                 $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0]/10;
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price*10;
              break;
             case 'MS':
               if($category_value == 'buy'){
                 if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 5 && $inventory_array[0] <=10){
                        $price = $result_array[0]['5-10'][$product_key]; 
                     }else{
                        $price = $result_array[0]['11-500'][$product_key]; 
                     } 
                        $result_inventory = $inventory_array[0]/10;
                 }else{
                     $price = $result_array[0]['5-10'][$product_key]; 
                     $result_inventory = 0;
                 }
                     $result_str = $price*10;
               }else{
                 $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0]/10;
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price*10;

               }
              break;
             case 'cronous':
                if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0]/10000;
                }else{
                     $result_inventory = 0;
                } 
                   $result_str = $result_array[0]['price'][$product_key]*10;
              break;
             case 'tenjouhi':
    
	       if(strpos($result_array[0]['inventory'][$product_key],'img')){
   	  	  $inventory_array[0]=0;
		 }
               if($category_value == 'buy'){
                 if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 1 && $inventory_array[0] <=49){
                        $price = $result_array[0]['1-49'][$product_key]; 
                     }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                        $price = $result_array[0]['50-99'][$product_key]; 
                     }else{ 
                        $price = $result_array[0]['100-'][$product_key]; 
                     }
                     $result_inventory = $inventory_array[0]/10;
                 }else{
                     $price = $result_array[0]['1-49'][$product_key]; 
                     $result_inventory = 0;
                 }
                     $result_str = $price*10;
               }else{
                 $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0]/10;
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price*10;

               }
              break;
             case 'rose':
                   $price = $result_array[0]['price'][$product_key]; 
                   if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0]/100;
                   }else{
                     $result_inventory = 0;
                   } 
                    $result_str = $price;
              break;
             case 'hzr':
               if($category_value == 'buy'){
                   $price = $result_array[0]['price'][$product_key]; 
                   if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0]/10;
                   }else{
                     $result_inventory = 0;
                   } 
                    $result_str = $price*10;
               }
              break;
             case 'dekaron':
                   $price = $result_array[0]['price'][$product_key]; 
                   if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0]/100;
                   }else{
                     $result_inventory = 0;
                   } 
                    $result_str = $price*10;
              break;
             case 'fez':
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
               if($category_value == 'buy'){
                 if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
                        $price = $result_array[0]['1-19'][$product_key]; 
                     }else if($inventory_array[0] >= 20 && $inventory_array[0] <=29){
                        $price = $result_array[0]['20-29'][$product_key]; 
                     }else{ 
                        $price = $result_array[0]['30-'][$product_key]; 
                     }
                     $result_inventory = $inventory_array[0];
                 }else{
                     $price = $result_array[0]['1-19'][$product_key]; 
                     $result_inventory = 0;
                 }
                     $result_str = $price;
               }else{
                 $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0];
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price;

               }
              break;
             case 'lakatonia':
		if(strpos($result_array[0]['inventory'][$product_key],'img')){
   		   $inventory_array[0]=0;
		 }
               if($category_value == 'buy'){
                 if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 1 && $inventory_array[0] <=2){
                        $price = $result_array[0]['1-2'][$product_key]; 
                     }else if($inventory_array[0] >= 3 && $inventory_array[0] <=4){
                        $price = $result_array[0]['3-4'][$product_key]; 
                     }else{ 
                        $price = $result_array[0]['5-'][$product_key]; 
                     }
                     $result_inventory = $inventory_array[0]/10;
                 }else{
                     $price = $result_array[0]['1-2'][$product_key]; 
                     $result_inventory = 0;
                 }
                     $result_str = $price*10;
               }else{
                 $price = $result_array[0]['price'][$product_key]; 
                 if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0]/10;
                 }else{
                   $result_inventory = 0;
                 } 
                  $result_str = $price*10;

               }
              break;
             case 'mabinogi':
                   $price = $result_array[0]['price'][$product_key]; 
                   if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0]/100;
                   }else{
                     $result_inventory = 0;
                   } 
                    $result_str = $price;
              break;
             case 'rohan':
                   $price = $result_array[0]['price'][$product_key]; 
                   if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0];
                   }else{
                     $result_inventory = 0;
                   } 
                    $result_str = $price;
              break;
             case 'genshin':
                   $price = $result_array[0]['price'][$product_key]; 
                   if($inventory_array[0] != ''){
                     $result_inventory = $inventory_array[0]/100;
                   }else{
                     $result_inventory = 0;
                   } 
                    $result_str = $price;
              break;

         case 'moe':
           if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 5 && $inventory_array[0] <=9){
		        $price = $result_array[0]['5-9'][$product_key];
		  }else if($inventory_array[0] >= 10 && $inventory_array[0] <=49){
 		      $price = $result_array[0]['10-49'][$product_key];
                  }else{
		      $price = $result_array[0]['50-'][$product_key];
                  }
                 $result_inventory = $inventory_array[0]/10;
 
              }else{
 		$price = $result_array[0]['5-9'][$product_key];
                $result_inventory = 0;
              }
             $result_str = $price*10;
            }
         break;
     
         case 'rohan':
         break;
          }
        }else if($site_value == 1){//マツブシ
          $result_str = $result_array[0]['price'][$product_key];
          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',trim($result_array[0]['inventory'][$product_key]),$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
         if($result_array[0]['inventory'][$product_key] != ''){
           $result_inventory = $result_array[0]['inventory'][$product_key];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
          switch($game_type){ 
            case 'FF11': 
               if($category_value == 'buy'){
                  $result_inventory =$inventory_array[0]/10;
                  $price = $result_array[0]['price'][$product_key]*10;
               }else{
                  $result_inventory =$inventory_array[0]/100;
                  $price = $result_array[0]['price'][$product_key];
               }
               $result_str = $price;
            break;
           case 'DQ10':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10;
              }else{
                $result_inventory = 0; 
              }
          break;
          case 'RS':
             $result_inventory = $inventory_array[0]/100;
          break;
          case 'L2':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/100;
              }else{
                $result_inventory = 0; 
              }
          break;
            case 'FF14':
		if(strpos($result_array[0]['inventory'][$product_key],'img')){
   		   $inventory_array[0]=0;
		 }
              $result_inventory = $inventory_array[0]/10;
            break;
          case 'ARAD':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10;
              }else{
                $result_inventory = 0; 
              }
              break;
           case 'nobunaga':
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=19){

                    $price = $result_array[0]['1-19'][$product_key]; 
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                    $price = $result_array[0]['20-49'][$product_key]; 
                  }else{
             
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-19'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0]/10;
              }
              $result_str = $price*10;
          break;
          case 'PSO2':
              $result_str = $result_array[0]['price'][$product_key];
              $result_inventory = $result_array[0]['inventory'][$product_key];
          break;
          case 'RO':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*100;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/100;
              }else{
                $result_inventory = 0; 
              }
         break;
         case 'TERA':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10;
              }else{
                $result_inventory = 0; 
			
              }
         break;
         case 'AION':
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=9){

                    $result_str = $result_array[0]['1-9'][$product_key]; 
                  }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                    $result_str = $result_array[0]['10-29'][$product_key]; 
                  }else{
             
                    $result_str = $result_array[0]['30-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_str = $result_array[0]['1-9'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $result_str = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }

         break;
	 case 'CABAL':
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=49){

                    $result_str = $result_array[0]['1-49'][$product_key]; 
                  }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                    $result_str = $result_array[0]['50-99'][$product_key]; 
                  }else{
             
                    $result_str = $result_array[0]['100-'][$product_key]*10; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_str = $result_array[0]['1-49'][$product_key]*10; 
                  $result_inventory = 0;
                }
              }else{
                $result_str = $result_array[0]['price'][$product_key]*10; 
                $result_inventory = $inventory_array[0]/10;
              }
         break; 
         case 'TERA':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10;
              }else{
                $result_inventory = 0; 
			
	      }
         break; 
	 case 'WZ':
              if($category_value == 'buy'){
                 $result_inventory = str_replace(',','',$inventory_array[0]); 
                 $price = $result_array[0]['price'][$product_key]; 
                 $result_str = $price;
                 if($inventory_array[0] != ''){
                   $result_inventory = $result_inventory/10;
                 }else{
                    $result_inventory = 0; 
	         }
               }else{
                  if($inventory_array[0] != ''){
                    $price = $result_array[0]['price'][$product_key]; 
                    $result_inventory = $inventory_array[0];
                  }else{
                    $price = $result_array[0]['price'][$product_key]; 
                     $result_inventory = 0;
                  }
                   $result_str = $price;

               }
         break;
         case 'latale':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory/10;
              }else{
                $result_inventory = 0; 
	      }
         break; 
	 case 'blade':
         if($category_value == 'buy'){
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price*10;
                  if($result_array[0]['inventory'][$product_key] != ''){
                    $result_inventory = $result_array[0]['inventory'][$product_key]/10;
                  }else{
                    $result_inventory = 0; 
                  }
/*
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
            if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
                $result_inventory = $result_inventory;
              }else{
                $result_inventory = 0; 
	      }
*/
         }else{
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
            if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
                $result_inventory = $result_inventory;
              }else{
                $result_inventory = 0; 
	      }
          }
         break; 
	 case 'megaten':
            if($category_value == 'buy'){
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory/10;
              }else{
                $result_inventory = 0; 
	      }
            }else{
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $result_inventory = 0; 
	      }
            }
        break; 
	case 'EWD':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory/10;
              }else{
                $result_inventory = 0; 
	      }
        break; 
	case 'LH':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){

                  if($inventory_array[0] >= 1 && $inventory_array[0] <=49){

                    $result_str = $result_array[0]['1-49'][$product_key]; 
                  }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                    $result_str = $result_array[0]['50-99'][$product_key]; 
                  }else{
             
                    $result_str = $result_array[0]['100-'][$product_key]*10; 
                  } 
                   $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_str = $result_array[0]['1-49'][$product_key]*10; 
                  $result_inventory = 0;
                }
            //如果没有库存就会是一张图片
                if(strpos($result_array[0]['inventory'][$product_key],'img')){
                    $result_str = $result_array[0]['1-49'][$product_key]*10;
                    $result_inventory = 0;
                 }
              }else{
                $result_str = $result_array[0]['price'][$product_key]*10; 
                $result_inventory = $inventory_array[0]/10;
              }
         break; 
	 case 'HR':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory/10;
              }else{
                $result_inventory = 0; 
	      }
        break; 
        case 'AA':
            $result_inventory = str_replace(',','',$inventory_array[0]); 
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $result_inventory/10;
              }else{
                $result_inventory = 0; 
			
		}
        break;
        case 'ThreeSeven':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $result_inventory = 0; 
	      }
        break; 
	case 'FNO':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($result_array[0]['inventory'][$product_key] != ''){
                $result_inventory = $result_array[0]['inventory'][$product_key]/10;
              }else{
                $result_inventory = 0; 
	      }
        break; 
/*	case 'SUN':
              preg_match_all("|.*?([0-9,]+).*?口.*?|",$result_array[0]['inventory'][$product_key],$temp_array);
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $temp_array[1][0]/10;
              }else{
                $result_inventory = 0; 
	      }
         break;*/
	 case 'MU':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
        break; 
	  case 'MS':
           if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
                    $price = $result_array[0]['1-19'][$product_key]; 
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                    $price = $result_array[0]['20-49'][$product_key]; 
                  }else{
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 

                  $price = str_replace(',','',$price); 
                  $result_str = $price*10;
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_str = $result_array[0]['1-19'][$product_key]*10; 
                  $result_inventory = 0;
                }
           }else{
             $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
            if($category_value == 'buy'){
             $price = $result_array[0]['price'][$product_key];
            }else{

             $price = $result_array[0]['price'][$product_key];
             }
              $result_str = $price*10;
             $result_inventory = $inventory_array[0]/10;
            }
 	  break;
	 case 'cronous':
              $price = $result_array[0]['price'][$product_key]; 
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/100;
              }else{
                $result_inventory = 0; 
	      }
              $result_str = $price*100;
        break; 
	case 'rose':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
        break; 
	case 'dekaron':
           if($category_value == 'buy'){
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10;
              }else{
                $result_inventory = 0; 
	      }
           }
        break; 
	case 'fez':
           if($category_value == 'buy'){
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
           }
        break; 
        case 'moe':
           
	if(strpos($result_array[0]['inventory'][$product_key],'img')){
   	   $inventory_array[0]=0;
         }
           $inventory_array[0]=$result_array[0]['inventory'][$product_key];
           if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 5 && $inventory_array[0] <=9){
                    $price = $result_array[0]['5-9'][$product_key]; 
                  }else if($inventory_array[0] >= 10 && $inventory_array[0] <=49){
                    $price = $result_array[0]['10-49'][$product_key]; 
                  }else{
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 

                  $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
                  $result_str = $price*10;
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_str = $result_array[0]['5-9'][$product_key]*10; 
                  $result_inventory = 0;
                }
           }else{
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0]/10;
              }else{
                $result_inventory = 0; 
	      }
           }
        break; 
	case 'WF':
           $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
           if($inventory_array[0] != ''){
              if($inventory_array[0] >= 1 && $inventory_array[0] <=24){
                  $price = $result_array[0]['1-24'][$product_key]; 
              }else if($inventory_array[0] >= 25 && $inventory_array[0] <=49){
                  $price = $result_array[0]['25-49'][$product_key]; 
              }else{
                  $price = $result_array[0]['50-'][$product_key]; 
              } 
                  $result_str = $price*10;
                  $result_inventory = $inventory_array[0]/10;
           }else{
              $result_str = $result_array[0]['1-24'][$product_key]*10; 
              $result_inventory = 0;
           }
        break; 
	case 'genshin':
           $inventory_array[0] = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
             if($category_value == 'buy'){
                $price = $result_array[0]['price'][$product_key]; 
             }else{
                $price = $result_array[0]['price'][$product_key]; 
             }
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
        break; 


      } 
      }else if($site_value == 2){//FTB
         if($game_type != 'L2'){
           preg_match('/([0-9,]+)(口|M|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         }else{
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         }
         $price = $result_array[0]['price'][$product_key]; 
         if($result_array[0]['inventory'][$product_key] != ''){
           $result_inventory = $result_array[0]['inventory'][$product_key];
         }else{
           $result_inventory = 0; 
         } 
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
         $result_str = $price; 
         switch($game_type){
            case 'FF11':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                    $price = $result_array[0]['1-9'][$product_key]; 
                  }else{
                    $price = $result_array[0]['10-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-9'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }
              $result_str = $price;
           break;

            case 'DQ10':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=49){

                    $price = $result_array[0]['1-49'][$product_key]; 
                  }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                    $price = $result_array[0]['50-99'][$product_key]; 
                  }else{
             
                    $price = $result_array[0]['100-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-49'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }
              $result_str = $price;

            break;
          case 'RS':

             if(strpos($result_array[0]['inventory'][$product_key],'img')){
               $inventory_array[0]=0;
             }
	 if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=9){

                  $price = $result_array[0]['1-9'][$product_key]; 
                }else if($inventory_array[0] >= 10 && $inventory_array[0] <=19){
                  $price = $result_array[0]['20-29'][$product_key]; 
                }else{
             
                  $price = $result_array[0]['20-'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['1-9'][$product_key]; 
                $result_inventory = 0;
              }
			 }else{
		        $result_inventory = $inventory_array[0];
                $price = $result_array[0]['price'][$product_key];
			 }
                $result_str = $price;
            break;  
            case 'L2':
             if(strpos($result_array[0]['inventory'][$product_key],'img')){
               $inventory_array[0]=0;
             }
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=9){

                    $price = $result_array[0]['1-9'][$product_key]; 
                  }else if($inventory_array[0] >= 10 && $inventory_array[0] <=19){
                    $price = $result_array[0]['10-19'][$product_key]; 
                  }else{
             
                    $price = $result_array[0]['20-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-9'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }
              $result_str = $price;
            break;
          case 'FF14':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=29){
                     $price = $result_array[0]['1-29'][$product_key]; 
                  }else if($inventory_array[0] >= 30 && $inventory_array[0] <=59){
                     $price = $result_array[0]['30-59'][$product_key]; 
                  }else{
                     $price = $result_array[0]['60-'][$product_key]; 
                  } 
                     $result_inventory = $inventory_array[0]/10;
                 }else{
                    $price = $result_array[0]['1-29'][$product_key]; 
                    $result_inventory = 0;
                 }
                  $result_str = $price*10;
            }else{
                $price = $result_array[0]['price'][$product_key]; 
               if($inventory_array[0] != ''){
                  $result_inventory = $inventory_array[0]/10;
                  $result_str = $price*10;
               }else{
                  $result_inventory = $inventory_array[0]; 
                  $result_str = $price*10;
               }
            }
          break;
          case 'RO':
             if($category_value == 'buy'){
               if(strpos($result_array[0]['inventory'][$product_key],'img')){
                  $inventory_array[0]=0;
               }
               if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
                     $price = $result_array[0]['1-19'][$product_key]; 
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=29){
                     $price = $result_array[0]['20-29'][$product_key]; 
                  }else{
                     $price = $result_array[0]['30-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/100;
               }else{
                  $price = $result_array[0]['1-19'][$product_key]; 
                  $result_inventory = 0;
               }
               $result_str = $price*100;
            }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_str = $price*100; 
                $result_inventory = $result_inventory[0]/100;
            }
          break;
            case 'ARAD':
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=24){

                    $price = $result_array[0]['1-24'][$product_key]; 
                  }else if($inventory_array[0] >= 25 && $inventory_array[0] <=49){
                    $price = $result_array[0]['25-49'][$product_key]; 
                  }else{
             
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-24'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }
              $result_str = $price;
           break;
            case 'nobunaga':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
            case 'PSO2':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
            case 'L1':
              if($category_value =='buy'){
      
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              }else{
           
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
             }
              $result_str = $price;
            break;
            case 'WZ':
            if($category_value == 'buy'){

              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
            }else{
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
            }
              $result_str = $price;
            break;

            case 'latale':
             if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]*10; 
                $result_inventory = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
                $result_inventory = $result_inventory/10;
              }else{
                $price = $result_array[0]['price'][$product_key]*10; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
            case 'megaten':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
            case 'EWD':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory/10;
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price*10;
            break;
            case 'LH':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
            case  'ECO':
            if($category_value == 'buy'){
               //商品库存
                preg_match_all("|<b .*?>([0-9,]+).*?<\/b>|",$result_array[0]['inventory'][$product_key],$temp_array);
                if($temp_array[1][0]==''){
                    $inventory_array[0]=0;
                }else{
                   $inventory_array[0]=$temp_array[1][0];
                }
                $inventory_array[0] = str_replace(',','',$inventory_array[0]); 

                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 10 && $inventory_array[0] <=49){

                    $result_str = $result_array[0]['10-49'][$product_key]; 
                  }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                    $result_str = $result_array[0]['50-99'][$product_key]*10; 
                  }else if($inventory_array[0] >= 100 && $inventory_array[0] <=299){
                    $result_str = $result_array[0]['100-299'][$product_key]*10; 
                  }else{
                    $result_str = $result_array[0]['300-'][$product_key]*10; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_str = $result_array[0]['1-49'][$product_key]*10; 
                  $result_inventory = 0;
                }
             }else{
                preg_match_all("|<b .*?>([0-9,]+).*?<\/b>|",$result_array[0]['inventory'][$product_key],$temp_array);
                if($temp_array[1][0]==''){
                    $inventory_array[0]=0;
                }else{
                   $inventory_array[0]=$temp_array[1][0];
                }
                $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
                $result_str = $result_array[0]['price'][$product_key]*10; 
                $result_inventory = $inventory_array[0]/10;
             }
             break;
             case 'lineage':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory  =$inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
             break;

           case 'AION':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
              } 
              break;
        /* case 'AION':
         if(strpos($result_array[0]['inventory'][$product_key],'img')){
             $inventory_array[0]=0;
          }
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=9){

                    $result_str = $result_array[0]['1-9'][$product_key]; 
                  }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                    $result_str = $result_array[0]['10-29'][$product_key]; 
                  }else{
             
                    $result_str = $result_array[0]['30-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_str = $result_array[0]['1-9'][$product_key]; 
                  $result_inventory = 0;
                }
              }else{
                $result_str = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }

         break;*/
         case 'HR':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
              } 
         break;
	  case 'MS':
             $inventory_array[0] = str_replace(',','',$inventory_array[0]); 

             $price = $result_array[0]['price'][$product_key];
              $result_str = $price*10;
             $result_inventory = $inventory_array[0]/10;
 	  break;
	 case 'MU':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
        break; 
             case 'ThreeSeven':
            
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0];
                  }else{
                    $result_inventory = 0; 
                  }
            break;
            }

        }else if($site_value == 3){//WM
          preg_match('/([0-9,]+)\s*?(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
           $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
          $price = $result_array[0]['price'][$product_key]; 
          if($result_array[0]['inventory'][$product_key] != ''){
            $result_inventory = $result_array[0]['inventory'][$product_key];
          }else{
            $result_inventory = 0; 
          } 
          $result_str = $price; 
            switch($game_type){    
            case 'FF11':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=49){
                    $price = $result_array[0]['1-49'][$product_key]*10; 
                  }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                    $price = $result_array[0]['50-99'][$product_key]*10;
                  }else{
                    $price = $result_array[0]['100-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-49'][$product_key]*10; 
                  $result_inventory = 0;
                }
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0]/10;
              }
              $result_str = $price*10;
           break;
              case 'DQ10':
		      if($category_value == 'buy'){
                          if($inventory_array[0] != ''){
                             if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                                $price = $result_array[0]['1-9'][$product_key]; 
                             }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                                 $price = $result_array[0]['10-29'][$product_key]; 
                             }else{
                                 $price = $result_array[0]['30-'][$product_key]; 
                             } 
                             $result_inventory = $inventory_array[0];
                          }else{
                              $price = $result_array[0]['1-9'][$product_key]; 
                              $result_inventory = 0;
                          }
		      }else{
                         if($inventory_array[0] != ''){
                            $result_inventory = $inventory_array[0];
                         }else{
                            $result_inventory = 0; 
                         } 
                         $price = $result_array[0]['price'][$product_key];
		      } 
                        $result_str = $price;

              break;
            case 'RS':
             if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 5 && $inventory_array[0] <=99){
                  $price = $result_array[0]['5-99'][$product_key]; 
                }else if($inventory_array[0] >= 100 && $inventory_array[0] <=199){
                  $price = $result_array[0]['100-199'][$product_key]; 
                }else{
             
                  $price = $result_array[0]['200-'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['5-99'][$product_key]; 
                $result_inventory = 0;
              }
            }else{
              if($inventory_array[0] != ''){
                 $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0;
              }                   
              $price = $result_array[0]['price'][$product_key];
            }
              $result_str = $price;
              break; 
              case 'L2':
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
                    $price = $result_array[0]['1-19'][$product_key];
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                   $price = $result_array[0]['20-49'][$product_key];
                  }else{
                   $price = $result_array[0]['50-'][$product_key];
                  }
                 $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-19'][$product_key]; 
                  $result_inventory = 0;
                }
                  $result_str = $price;
                }else{
                if($inventory_array[0] != ''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0; 
                } 
                $price = $result_array[0]['price'][$product_key];
                $result_str = $price;
                }
              break;
            case 'FF14':
             if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=9){

                  $price = $result_array[0]['1-9'][$product_key]; 
                }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                  $price = $result_array[0]['10-29'][$product_key]; 
                }else{
             
                  $price = $result_array[0]['30-'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['1-9'][$product_key]; 
                $result_inventory = 0;
              }
             }else{
              if($inventory_array[0] != ''){
                 $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0;  
              }
              $price = $result_array[0]['price'][$product_key];

            }
              $result_str = $price;
              break;

            case 'RO':
              if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=49){

                  $price = $result_array[0]['1-49'][$product_key]; 
                }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                  $price = $result_array[0]['50-99'][$product_key]; 
                }else{
                  $price = $result_array[0]['100-'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-49'][$product_key]; 
                  $result_inventory = 0;
                }
                $price= str_replace(',','',$price); 
                $result_str = $price;
			  }else{

                 $price = $result_array[0]['price'][$product_key];
                 $result_str = $price*100;
                 $result_inventory = $inventory_array[0]/100;
			  }
              break;
              case 'ARAD':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
              break;
              case 'blade':
	    if($category_value == 'buy'){
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
            if($inventory_array[0] != ''){
                $result_inventory = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
                $result_inventory = $result_inventory;
              }else{
                $result_inventory = 0; 
	      }
/*
              $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=4){
                    $price = $result_array[0]['1-4'][$product_key]; 
                  }else if($inventory_array[0] >= 5 && $inventory_array[0] <=10){
                    $price = $result_array[0]['5-10'][$product_key]; 
                  }else{
                    $price = $result_array[0]['10-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-4'][$product_key]; 
                  $result_inventory = 0;
                }

              $result_str = $price*10;
*/
			 }else{
              if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
			$price = $result_array[0]['1-9'][$product_key]; 
                }else{
                  $price = $result_array[0]['10-'][$product_key]; 
                } 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['1-9'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price*10;
			 }
              break;
              case 'genshin':
           $inventory_array[0] = str_replace(',','',$result_array[0]['inventory'][$product_key]); 
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
                    $price = $result_array[0]['1-19'][$product_key]; 
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                    $price = $result_array[0]['20-49'][$product_key]; 
                  }else{
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-19'][$product_key]; 
                  $result_inventory = 0;
                }
            }else{
                $result_inventory = $inventory_array[0];
                $price = $result_array[0]['price'][$product_key]; 
             }
              $result_str = $price;
              break;
			  case 'latale':
              if($category_value == 'sell'){
               if($inventory_array[0] != ''){
                  $result_inventory = $inventory_array[0]/10;
               }else{
                 $result_inventory = 0; 
               } 
               $price = $result_array[0]['price'][$product_key];
               $result_str = $price*10;
			  }	  
             			  break;
        case 'MS':

            if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=19){

                  $price = $result_array[0]['1-19'][$product_key]; 
                }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                  $price = $result_array[0]['20-49'][$product_key]; 
                }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                  $price = $result_array[0]['50-99'][$product_key]; 
                }else {
                  $price = $result_array[0]['100-'][$product_key];
                } 
                $result_inventory = $inventory_array[0]/10;
              }else{
                $price = $result_array[0]['1-19'][$product_key]; 
                $result_inventory = 0;
              }
           $result_str = $price*10;
          break;

        case 'AA':
            $result_inventory = str_replace(',','',$inventory_array[0]); 
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $result_inventory/10;
              }else{
                $result_inventory = 0; 
			
		}
        break;
          }
        }else if($site_value == 4){//ランキング
          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
          $price = $result_array[0]['price'][$product_key]; 
          if($result_array[0]['inventory'][$product_key] != ''){
            $result_inventory = $result_array[0]['inventory'][$product_key];
          }else{
            $result_inventory = 0; 
          } 
          $result_str = $price; 

        switch($game_type){
          case 'FF11':
            if($category_value == 'buy'){
             if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key];
                $result_inventory = $inventory_array[0]/10;
              }else{
               $price = $result_array[0]['price'][$product_key];
               $result_inventory = 0;
             }
            }
            $result_str = $price*10;
            break;
         case 'DQ10':
            if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0;
              }
               $price = $result_array[0]['price'][$product_key]; 
           
              $result_str = $price;

        break;

       case 'RS':
            // $inventory_array[0] = str_replace(',','',$inventory_array[0]);
             $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
             $result_inventory = $result_array[0]['inventory'][$product_key];
       break;
       case 'L2':
             if(strpos($result_array[0]['inventory'][$product_key],'span')){
               $inventory_array[0]=0;
             }
           if($category_value == 'buy'){
            if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0;
              }
               $price = $result_array[0]['price'][$product_key]; 
           }
              $result_str = $price;
        break; 
      case 'RO':
             $price = $result_array[0]['price'][$product_key];
             $result_inventory = $result_array[0]['inventory'][$product_key]/100;
             $result_str = $price*100;
        break; 
		case 'latale':
             $price = $result_array[0]['price'][$product_key];
             $result_inventory = $result_array[0]['inventory'][$product_key]/10;
             $result_str = $price*10;
        break; 
		case 'L1':
           if(strpos($result_array[0]['inventory'][$product_key],'span')){
             $inventory_array[0]=0;
            }
            if($inventory_array[0] != ''){
                /*
                if($inventory_array[0] >= 1 && $inventory_array[0] <=9){

                  $price = $result_array[0]['1-9'][$product_key]; 
                }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                  $price = $result_array[0]['10-29'][$product_key]; 
                }else {
                  $price = $result_array[0]['30-'][$product_key];
                } 
                */
                $price = $result_array[0]['price'][$product_key];
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
             $result_str = $price;
         break;
         case 'ARAD':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
          case 'MS':
           if($category_value == 'buy'){
            if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=49){

                  $price = $result_array[0]['20-49'][$product_key]; 
                }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                  $price = $result_array[0]['50-99'][$product_key]; 
                }else {
                  $price = $result_array[0]['100-'][$product_key];
                } 
                $result_inventory = $inventory_array[0]/10;
              }else{
                $price = $result_array[0]['20-49'][$product_key]; 
                $result_inventory = 0;
              }
           }else{
            if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=19){

                  $price = $result_array[0]['1-19'][$product_key]; 
                }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                  $price = $result_array[0]['20-49'][$product_key]; 
                }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                  $price = $result_array[0]['50-99'][$product_key]; 
                }else {
                  $price = $result_array[0]['100-'][$product_key];
                } 
                $result_inventory = $inventory_array[0]/10;
              }else{
                $price = $result_array[0]['1-19'][$product_key]; 
                $result_inventory = 0;
              }
           }
          $result_str = $price*10;
          break;

          case 'genshin':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'WZ':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'blade':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
            case 'megaten':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
         case 'HR':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
              } 
         break;
        case 'AA':
            $result_inventory = str_replace(',','',$inventory_array[0]); 
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price*10;
              if($inventory_array[0] != ''){
                $result_inventory = $result_inventory/10;
              }else{
                $result_inventory = 0; 
			
		}
        break;
	 case 'MU':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
        break; 
	case 'FF14':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
        break; 
            case 'EWD':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory/10;
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price*10;
            break;
             case 'ThreeSeven':
            
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0];
                  }else{
                    $result_inventory = 0; 
                  }
            break;
           }

        }else if($site_value == 5) {//カカラン
          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
          $price = $result_array[0]['price'][$product_key]; 
          if($result_array[0]['inventory'][$product_key] != ''){
            $result_inventory = $result_array[0]['inventory'][$product_key];
          }else{
            $result_inventory = 0; 
          } 
         $result_str = $price; 
          switch($game_type){
          case 'FF11': 
              if(strpos($result_array[0]['inventory'][$product_key],'-')){
                  $inventory_array[0]=$result_array[0]['inventory'][$product_key];
               }
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0;
              }
              if($category_value == 'buy'){
                $price = $result_array[0]['price'][$product_key];
              }else{
              
                $price = $result_array[0]['price'][$product_key];
              }
              $result_str = $price;
           break;
	   case 'DQ10':
             if(strpos($result_array[0]['inventory'][$product_key],'-')){
                  $inventory_array[0]=$result_array[0]['inventory'][$product_key];
              }
               if($category_value=='buy'){
                 if($inventory_array[0] != ''){
                     $price = $result_array[0]['price'][$product_key]; 
                     $result_inventory = $inventory_array[0];
                  }else{
                     $price = $result_array[0]['price'][$product_key]; 
                     $result_inventory = 0;
                  }
               }else{
           
                 if($inventory_array[0] != ''){
                     $price = $result_array[0]['price'][$product_key]; 
                     $result_inventory = $inventory_array[0];
                  }else{
                     $price = $result_array[0]['price'][$product_key]; 
                     $result_inventory = 0;
                  }
               }
                $result_str = $price;

	 break;
          case 'RS':
          // if(strpos($result_array[0]['inventory'][$product_key],'-')){
            //  $inventory_array[0]=$result_array[0]['inventory1'][$product_key];
          // }
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
              if($category_value == 'buy'){
               $price = $result_array[0]['price'][$product_key]; 
             }else{ 
               $price = $result_array[0]['price'][$product_key]; 
             } 
              $result_str = $price;
          break;
	  case 'L2':
              if(strpos($result_array[0]['inventory'][$product_key],'-')){
                  $inventory_array[0]=$result_array[0]['inventory'][$product_key];
               }
               if(strpos($result_array[0]['inventory'][$product_key],'span')){
                 $inventory_array[0]=0;
               }
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
                if($category_value == 'buy'){
               $price = $result_array[0]['price'][$product_key]; 
                }else{
		$price = $result_array[0]['price'][$product_key];  
                }
               $result_str = $price;
         break;
          case 'RO':
             $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
            if($category_value == 'buy'){
             $price = $result_array[0]['price'][$product_key];
            }else{

             $price = $result_array[0]['price'][$product_key];
             }
              $result_str = $price*100;
             $result_inventory = $inventory_array[0]/100;
 	  break;

	  case 'MS':
             $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
            if($category_value == 'buy'){
             $price = $result_array[0]['price'][$product_key];
            }else{

             $price = $result_array[0]['price'][$product_key];
             }
              $result_str = $price*10;
             $result_inventory = $inventory_array[0]/10;
 	  break;

          case 'genshin':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;

          case 'latale':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price*10;
          break;
          
          case 'L1':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'WZ':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'blade':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
            case 'megaten':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
            break;
           case 'AION':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
              } 
              break;
              case 'ARAD':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = $inventory_array[0];
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price;
              break;
         case 'HR':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
              } 
         break;
	 case 'MU':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
        break; 
            case 'EWD':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory/10;
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price*10;
            break;
             case 'ThreeSeven':
            
                  $price = $result_array[0]['price'][$product_key];
                  $result_str = $price;
                  if($inventory_array[0] != ''){
                    $result_inventory = $inventory_array[0];
                  }else{
                    $result_inventory = 0; 
                  }
            break;
          } 
      }else if($site_value == 6){
		  //这个是主站
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
         if($inventory_array[0] != ''){
           $result_inventory = $inventory_array[0];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
         switch($game_type){
        
          case 'latale':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price*10;
          break;
          case 'L1':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
            case 'megaten':
              if($inventory_array[0] != ''){
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = str_replace(',','',$inventory_array[0]); 
                $result_inventory = $result_inventory;
              }else{
                $price = $result_array[0]['price'][$product_key]; 
                $result_inventory = 0;
              }
              $result_str = $price*10;
            break;
           case 'AION':
              $price = $result_array[0]['price'][$product_key];
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
              } 
              break;
	case 'FF14':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
        break; 
         }
      }else if($site_value == 7){//ぱすてる
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
         if($result_array[0]['inventory'][$product_key] != ''){
           $result_inventory = $inventory_array[0];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
         switch($game_type){
	 case 'DQ10':
              if(strpos($result_array[0]['inventory'][$product_key],'span')){
                   $inventory_array[0]=0;
              }
              $inventory_array[0] = str_replace(',','',$inventory_array[0]);
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 0 && $inventory_array[0] <=19){
                    $price = $result_array[0]['1-19'][$product_key]; 
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                    $price = $result_array[0]['20-49'][$product_key]; 
                  }else{
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-19'][$product_key]; 
                  $result_inventory = 0;
                }
                $result_str = $price*10;
             }else{
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
  
                    $price = $result_array[0]['1-19'][$product_key]; 
                  }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                    $price = $result_array[0]['20-49'][$product_key]; 
                  }else{
                    $price = $result_array[0]['50-'][$product_key]; 
                  } 
                 }else{
                    $price = $result_array[0]['1-19'][$product_key]; 
                 }
                 $result_str = $price*10;
                 $result_inventory = $inventory_array[0]/10;
            }
       break;
         case 'L2':
          if($category_value == 'buy'){
           if($inventory_array[0] != ''){
              if($inventory_array[0] >= 0 && $inventory_array[0] <=9){
                $price = $result_array[0]['1-9'][$product_key];
              }else if($inventory_array[0] >= 10 && $inventory_array[0] <=19){
                $price = $result_array[0]['10-19'][$product_key];
              }else{
                $price = $result_array[0]['20-'][$product_key];
              }
              $result_inventory = $inventory_array[0]/10;
           }else {
             $price = $result_array[0]['1-9'][$product_key];
             $result_inventory = 0;
           }
           $result_str = $price*10;
          }
          break;
	
          case 'RO':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0]/100;
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price*100;
          break;
	case 'FF14':
              $price = $result_array[0]['price'][$product_key]; 
              $result_str = $price;
              if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
              }else{
                $result_inventory = 0; 
	      }
        break; 
       }
      }else if($site_value == 8){//ダイアモンドギル
        if($game_type == 'L2'){

          $result_array[0]['inventory'][$product_key] = strip_tags($result_array[0]['inventory'][$product_key]);
        }
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
           $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
         if($inventory_array[0] != ''){
           $result_inventory = $inventory_array[0];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
         switch($game_type){
         case 'FF11':
           if($category_value == 'buy'){ 
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 10 && $inventory_array[0] <=99){
                    $price = $result_array[0]['10-99'][$product_key]; 
                  }else if($inventory_array[0] >= 100 && $inventory_array[0] <=199){
                    $price = $result_array[0]['100-199'][$product_key];
                  }else if($inventory_array[0] >= 200 && $inventory_array[0] <=299){
                    $price = $result_array[0]['200-299'][$product_key];
                  }else{
                    $price = $result_array[0]['300-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['10-99'][$product_key]; 
                  $result_inventory = 0;
                }
           }else {
               if($inventory_array[0] != ''){
                 if($inventory_array[0] >= 1 && $inventory_array[0] <=4){
                   $price = $result_array[0]['1-4'][$product_key];
                 }else {
                   $price = $result_array[0]['5-'][$product_key];
                 }
                 $result_inventory = $inventory_array[0]/10;
              }else {
                 $price = $result_array[0]['1-4'][$product_key];
                 $result_inventory = 0;
              }
           }
           $result_str = $price*10;
        break;
	 case 'DQ10':
               if($category_value == 'buy'){
               //商品库存
                preg_match_all("|<b .*?>([0-9,]+).*?<\/b>|",$result_array[0]['inventory'][$product_key],$temp_array);
                if($temp_array[1][0]==''){
                    $inventory_array[0]=0;
                }else{
                   $inventory_array[0]=$temp_array[1][0];
                }
                $inventory_array[0] = str_replace(',','',$inventory_array[0]); 
                     if($inventory_array[0] != ''){
                        if($inventory_array[0] >= 1 && $inventory_array[0] <=49){
                            $price = $result_array[0]['1-49'][$product_key]; 
                        }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                            $price = $result_array[0]['50-99'][$product_key]; 
                        }else{
                            $price = $result_array[0]['100-'][$product_key]; 
                        } 
                      }else{
                        $price = $result_array[0]['1-49'][$product_key]; 
                      }
		 }else{
                     if($inventory_array[0] != ''){
                        if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                            $price = $result_array[0]['1-9'][$product_key]; 
                        }else if($inventory_array[0] >= 10 && $inventory_array[0] <=49){
                            $price = $result_array[0]['10-49'][$product_key]; 
                        }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                            $price = $result_array[0]['50-99'][$product_key]; 
                        }else{
                            $price = $result_array[0]['100-'][$product_key]; 
                        } 
                      }else{
                        $price = $result_array[0]['1-9'][$product_key]; 
                      }
		 }
                     $result_str = $price;
                     $result_inventory = $inventory_array[0];

           break;
         case 'RS':
           if($category_value == 'buy'){
              if($inventory_array[0] != ''){
               if($inventory_array[0] >= 10 && $inventory_array[0] <=49){
                  $price = $result_array[0]['10-49'][$product_key];
                }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                   $price = $result_array[0]['50-99'][$product_key];
                }else if($inventory_array[0] >= 100 && $inventory_array[0] <=199){
                      $price = $result_array[0]['100-199'][$product_key];
                }else{
                   $price = $result_array[0]['200-'][$product_key];
                }
               $result_inventory = $inventory_array[0];
              }else{
                 $price = $result_array[0]['10-49'][$product_key];
                 $result_inventory = 0;
              
              }
               $result_str = $price;
           }else{
            if($inventory_array[0] != ''){
              $result_inventory = $inventory_array[0];
            }else{
              $result_inventory = 0;
            }
             $price = $result_array[0]['5-'][$product_key];
             $result_str = $price;
           }
           break;
         case 'L2':
            if(strpos($result_array[0]['inventory'][$product_key],'-')){
               $inventory_array[0]=$result_array[0]['inventory'][$product_key];
            }
           if($category_value == 'buy'){
             if($inventory_array[0] != ''){
                if($inventory_array[0] >= 1 && $inventory_array[0] <=19){
                   $price = $result_array[0]['1-19'][$product_key];
                }else if($inventory_array[0] >= 20 && $inventory_array[0] <=49){
                   $price = $result_array[0]['20-49'][$product_key];
                }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                   $price = $result_array[0]['50-99'][$product_key];
                }else{
                   $price = $result_array[0]['100-'][$product_key];
                }
                $result_inventory = $inventory_array[0];
             }else{
               $price = $result_array[0]['1-19'][$product_key];
               $result_inventory = 0;
             }
           }else{
             if($inventory_array[0] != ''){
                $result_inventory = $inventory_array[0];
             }else{
                $result_inventory = 0;
             }
             $price = $result_array[0]['price'][$product_key];
           }
           $result_str = $price;
         break;
	
          case 'RO':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0]/100;
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price*100;
          break;
         case 'moe':
           if($category_value == 'buy'){
              if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 5 && $inventory_array[0] <=9){
		        $price = $result_array[0]['5-9'][$product_key];
		  }else if($inventory_array[0] >= 10 && $inventory_array[0] <=49){
 		      $price = $result_array[0]['10-49'][$product_key];
                  }else{
		      $price = $result_array[0]['50-'][$product_key];
                  }
                 $result_inventory = $inventory_array[0]/10;
 
              }else{
 		$price = $result_array[0]['5-9'][$product_key];
                $result_inventory = 0;
              }
             $result_str = $price*10;
            }
         break;
        case 'nobunaga':
echo $value;
              if($category_value == 'buy'){
                if($inventory_array[0] != ''){
                  if($inventory_array[0] >= 0 && $inventory_array[0] <=49){

                    $price = $result_array[0]['1-49'][$product_key]; 
                  }else if($inventory_array[0] >= 50 && $inventory_array[0] <=99){
                    $price = $result_array[0]['50-99'][$product_key]; 
                  }else{
             
                    $price = $result_array[0]['100-'][$product_key]; 
                  } 
                  $result_inventory = $inventory_array[0]/10;
                }else{
                  $price = $result_array[0]['1-49'][$product_key]; 
                  $result_inventory = 0;
                }
              }
       break;
	 }
	}
      else if($site_value == 9){//アサヒ
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
         if($inventory_array[0] != ''){
           $result_inventory = $inventory_array[0];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
         switch($game_type){
			 //DQ10不需要处理
         case 'FF11': 
           if($category_value == 'buy'){ 
                if($inventory_array[0] != ''){
                   $result_inventory = $inventory_array[0]/10;
                }else{
                  $result_inventory = 0;
                }
                $price = $result_array[0]['price'][$product_key]; 
           }else {
               if($inventory_array[0] != ''){
                 $result_inventory = $inventory_array[0]/10;
              }else {
                 $result_inventory = 0;
              }
              $price = $result_array[0]['price'][$product_key];
           }
           $result_str = $price*10;
        break;
        case'DQ10':
        break;
         case 'RS':
         if($category_value == 'buy'){
            if($inventory_array[0] != ''){
              $price = $result_array[0]['price'][$product_key];
              $result_inventory = $inventory_array[0];
            }else{
              $result_inventory = 0;            
            }
            $result_str = $price;
        }else{
            if($inventory_array[0] != ''){
              $price = $result_array[0]['price'][$product_key];
               $result_inventory = $inventory_array[0];
            }else{
               $result_inventory = 0;
            }
             $result_str = $price;
        }
         break;
         case 'L2':
           if($inventory_array[0] != ''){
             $result_inventory = $inventory_array[0];
           }else{
              $result_inventory = 0;
           }
            $price = $result_array[0]['price'][$product_key];
            $result_str = $price;
         break;
		 }
	  }
      else if($site_value == 10){//KING
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
         if($inventory_array[0] != ''){
           $result_inventory = $inventory_array[0];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
         switch($game_type){
         case 'FF11':
           if($inventory_array[0] != ''){
              if($inventory_array[0] >=1 && $inventory_array[0]<=2){
                $price = $result_array[0]['1-2'][$product_key];
              }else if($inventory_array[0] >=3 && $inventory_array[0]<=4){
                $price = $result_array[0]['3-4'][$product_key];
              }else{
                $price = $result_array[0]['5-'][$product_key];
              }
             $result_inventory = $inventory_array[0]*10;
            }else{
              $price = $result_array[0]['1-2'][$product_key];
              $result_inventory = 0;
            }    
          $price = str_replace(',','',$price); 
          $result_str = $price/10;
        break;
	 case 'DQ10':
	          if($category_value == 'buy'){
                      $inventory_array[0] = str_replace(',','',$inventory_array[0]);
                     if($inventory_array[0] != ''){
                        if($inventory_array[0] >= 1 && $inventory_array[0] <=49){
                            $price = $result_array[0]['1-49'][$product_key]; 
                        }else if($inventory_array[0] >= 50 && $inventory_array[0] <=149){
                            $price = $result_array[0]['50-149'][$product_key]; 
                        }else if($inventory_array[0] >= 150 && $inventory_array[0] <=299){
                            $price = $result_array[0]['150-299'][$product_key]; 
                        }else{
                            $price = $result_array[0]['300-'][$product_key]; 
                        } 
                      }else{
                        $price = $result_array[0]['300-'][$product_key]; 
                      }
                     $result_str = $price;
                     $result_inventory = $inventory_array[0];
		 }else{
                     if($inventory_array[0] != ''){
                        if($inventory_array[0] >= 1 && $inventory_array[0] <=49){
                            $price = $result_array[0]['1-49'][$product_key]; 
                        }else if($inventory_array[0] >= 50 && $inventory_array[0] <=149){
                            $price = $result_array[0]['50-149'][$product_key]; 
                        }else{
                            $price = $result_array[0]['150-'][$product_key]; 
                        } 
                      }else{
                        $price = $result_array[0]['1-49'][$product_key]; 
                      }
	        }
                     $result_str = $price;
                     $result_inventory = $inventory_array[0];
		break;
                case 'RS':
                if(strpos($result_array[0]['inventory'][$product_key],'span')){
                      $inventory_array[0]=0;
                }
                if($inventory_array[0] != ''){
                 if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                    $price = $result_array[0]['1-9'][$product_key];
                 }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                    $price = $result_array[0]['10-29'][$product_key];
                 }else{
                    if($category_value != 'buy'){
                         $price = $result_array[0]['10-29'][$product_key];
                    }else{
                         $price = $result_array[0]['30-'][$product_key];
                    }
                 }
                 $result_inventory = $inventory_array[0];
                }else{
                  $price = $result_array[0]['1-9'][$product_key];
                   $result_inventory = 0;
                }
                 $result_str = $price;
                break;
                case 'L2':
                
                if(strpos($result_array[0]['inventory'][$product_key],'span')){
                      $inventory_array[0]=0;
                }
                  if($category_value == 'buy'){
                    if($inventory_array[0] != ''){
                      if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                        $price = $result_array[0]['1-9'][$product_key];
                      }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                        $price = $result_array[0]['10-29'][$product_key];
                      }else if($inventory_array[0] >= 30 && $inventory_array[0] <=99){
                        $price = $result_array[0]['30-99'][$product_key];
                      }else{
                        $price = $result_array[0]['100-'][$product_key];
                      }
                      $result_inventory = $inventory_array[0];
                    }else{
                       $price = $result_array[0]['1-9'][$product_key];
                       $result_inventory = 0;
                    }
                  }else{
                     if($inventory_array[0] != ''){
                         $result_inventory = $inventory_array[0];
                     }else {
                        $result_inventory = 0;
                    }
                     $price = $result_array[0]['1-9'][$product_key];
                  }
                  $result_str = $price;
                break;
                case 'L1':
                if(strpos($result_array[0]['inventory'][$product_key],'span')){
                    $inventory_array[0]=0;
                 }
                if($category_value == 'buy'){               
                    if($inventory_array[0] != ''){
                      if($inventory_array[0] >= 1 && $inventory_array[0] <=9){
                        $price = $result_array[0]['1-9'][$product_key];
                      }else if($inventory_array[0] >= 10 && $inventory_array[0] <=29){
                        $price = $result_array[0]['10-29'][$product_key];
                      }else{
                        $price = $result_array[0]['30-'][$product_key];
                      }
                      $result_inventory = $inventory_array[0];
                    }else{
                       $price = $result_array[0]['1-9'][$product_key];
                       $result_inventory = 0;
                    }
                 }else{
                    if($inventory_array[0] != ''){
                        $price = $result_array[0]['1-9'][$product_key];
                        $result_inventory = $inventory_array[0];  
                    }else{
                        $result_inventory = 0;
                    } 
                 }
                break;
		 }
	  }
      else if($site_value == 11){//SONIC
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
         $price = $result_array[0]['price'][$product_key]; 
         if($inventory_array[0] != ''){
           $result_inventory = $inventory_array[0];
         }else{
           $result_inventory = 0; 
         } 
         $result_str = $price; 
         switch($game_type){
         case 'FF11':
           if($category_value == 'buy'){
             if($inventory_array[0] != ''){
               $result_inventory = $inventory_array[0]/10;
            }else {
               $result_inventory = 0;
           }
           }
           $price = $result_array[0]['price'][$product_key];
           $result_str = $price*10;
        break;
	 case 'DQ10':
                 if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 1 && $inventory_array[0] <=99){
                        $price = $result_array[0]['1-99'][$product_key]; 
                     }else{
                        $price = $result_array[0]['100-'][$product_key]; 
                     } 
                     $result_inventory = $inventory_array[0];
                  }else{
                     $price = $result_array[0]['1-99'][$product_key]; 
                     $result_inventory = 0;
                  }
                $result_str = $price;

		 break;
                 case 'RS':
                  if(strpos($result_array[0]['inventory'][$product_key],'img')){
                      $inventory_array[0]=0;
                  }
                 if($category_value == 'buy'){
                   if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 1 && $inventory_array[0] <=99){
                        $price = $result_array[0]['1-99'][$product_key];
                     }else{
                     $price = $result_array[0]['100'][$product_key];
                     }
                     $result_inventory = $inventory_array[0];
                   }else{
                     $price = $result_array[0]['1-99'][$product_key];
                     $result_inventory = 0;
                   }
                   $result_str = $price;
                 }
                 break;
                 case 'L2':
                 if($category_value == 'buy'){
                    if($inventory_array[0] != ''){
                      if($inventory_array[0] >= 1 && $inventory_array[0] <=99){
                        $price = $result_array[0]['1-5'][$product_key];
                      }else{
                        $price = $result_array[0]['6'][$product_key];
                      }
                    }else{
                       $price = $result_array[0]['1-5'][$product_key];
                     $result_inventory = 0;
                    }
                 }
                  $result_str = $price;
                 break;
           }
       
       }else if($site_value == 12){

          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
          $price = $result_array[0]['price'][$product_key]; 
          if($result_array[0]['inventory'][$product_key] != ''){
            $result_inventory = $result_array[0]['inventory'][$product_key];
          }else{
            $result_inventory = 0; 
          } 
         $result_str = $price; 
          switch($game_type){
          case 'FF11':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'DQ10':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'RS':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'L2':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
         case 'RO':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          }



       }else if($site_value == 13){

          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
          $price = $result_array[0]['price'][$product_key]; 
          if($result_array[0]['inventory'][$product_key] != ''){
            $result_inventory = $result_array[0]['inventory'][$product_key];
          }else{
            $result_inventory = 0; 
          } 
         $result_str = $price; 
          switch($game_type){
          case 'FF11':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'DQ10':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'RS':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          case 'L2':
             if($inventory_array[0] !=''){
                  $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
               $price = $result_array[0]['price'][$product_key]; 
               $result_str = $price;
          break;
          }
        }else if($site_value == 16){
          preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
          switch($game_type){
	 case 'DQ10':
                if($category_value == 'buy'){
                 if($inventory_array[0] != ''){
                     if($inventory_array[0] >= 10){
                        $price = $result_array[0]['10-'][$product_key]; 
                     }else if($inventory_array[0] >= 5 && $inventory_array[0] <=9){
                        $price = $result_array[0]['5-9'][$product_key]; 
                     }else{
                        $price = $result_array[0]['1-4'][$product_key]; 
                     }
                     $result_inventory = $inventory_array[0];
                  }else{
                     $price = $result_array[0]['1-4'][$product_key]; 
                     $result_inventory = 0;
                  }
                $result_str = $price;
                }else{
                  if($inventory_array[0] !=''){
                     $result_inventory = $inventory_array[0];
                }else{
                  $result_inventory = 0;
                }
                  $price = $result_array[0]['1-4'][$product_key]; 
                  $result_str = $price;
                }
		 break;
          }    

        }else{
          $price = $result_array[0]['price'][$product_key]; 
          if($result_array[0]['inventory'][$product_key] != ''){
            $result_inventory = $result_array[0]['inventory'][$product_key];
          }else{
            $result_inventory = 0; 
          } 
          $result_str = $price; 
        } 
      

      $value = str_replace('<br />','',$value);
      $result_str = str_replace(',','',$result_str);
      $result_inventory = str_replace(',','',$result_inventory);
      $value = preg_replace('/<.*?>/','',$value);
     //echo "insert into product values(NULL,'".$category_id_array[$site_value]."','".trim($value)."','".$result_str."','".$result_inventory."',0)<br>";
      //数据入库
      $res = array('value'=>$value,'result_str'=>$result_str,'result_inventory'=>$result_inventory);
      return $res;

}
//处理数据
/*@date 20141125
 * param1=> $game_type(FF11)游戏分类
 *param2=> $c_type 买卖
 *param3=> $fix_url 商品所在网站的url
 *param4=> $product_name要处理的商品名
 *result=> 商品名与主站相同
 */
function match_data_iimy($game_type,$c_type,$fix_url,$product_name){
   //主站当前分类的商品名
   if($c_type=='buy'){
      $c_type = 1;
   }else{
     $c_type = 0;
   }
   //主站商品名
    $product_iimy_sql= mysql_query("select * from product p,category c where p.category_id=c.category_id and category_name='".$game_type."' and category_type='".$c_type."' and c.game_server='jp' and c.site_id=7");
    while($product_row = mysql_fetch_array($product_iimy_sql)){
		if($product_name == $product_row['product_name']){
	        $product_real_name = $product_row['product_name'];	
		}
		//1.单词一致最多只差大小写和空格(RS)
	       $iimy_tep_name = strtolower(trim(preg_replace('/\s+/is','',$product_row['product_name'])));
	       $get_tep_name = strtolower(trim(preg_replace('/\s+/is','',$product_name)));
	       if($get_tep_name==$iimy_tep_name){
	          return $product_row['product_name'];	
		   }
         preg_match('/'.$iimy_tep_name.'/is',$get_tep_name,$seach_product);
         if(!empty($seach_product)){
             $product_real_name = $product_row['product_name'];
         }

         $product_name = trim(preg_replace('/\s+/is','',$product_name));

	   //RS拼写错误
          if($game_type=='RS'){
            if(strpos($fix_url,'mugenrmt')){
               if($product_name=='Ecplise'){
                  $product_real_name = 'Eclipse';	
               }
            }
            if(strpos($fix_url,'rmt-king') || strpos($fix_url,'kakaran')){
               if(trim($product_name)=='RedEmrald')
                 $product_real_name = 'Red Emerald';	
            }
           //ダイアモンドギル
           if(strpos($fix_url,'rmtsonic') || strpos($fix_url,'diamond-gil')){
              if($product_name=='Twlight'){
                 $product_real_name = 'Twilight';
              }	
           }
         if(strpos($fix_url,'rmtrank')){
            $product_name = strtolower(trim(preg_replace('/\s+/is','',$product_name)));
            $product_tep_name = strtolower(trim(preg_replace('/\s+/is','',$product_name)));
            $iimy_tep_name = strtolower(trim(preg_replace('/\s+/is','',$product_row['product_name'])));
            if($product_tep_name==$iimy_tep_name){
                $product_real_name =  $product_row['product_name'];	
            }
         }
		}
		//2.PSO2
         if($game_type=='PSO2'){
            if(strpos($fix_url,'ftb-rmt') || strpos($fix_url,'kakaran')){
               $product_real_name = str_replace('：',' ',$product_name);
            }else{
               $product_real_name = str_replace('．',' ',$product_name);
            }
          }
		//3.DQ10
          if($game_type=='DQ10'){
             $array_dq = array('共通サーバー','普通取引','ドラゴンクエスト10・ゴールド','PC','全サーバー共通','ゴールド','Windows版');
            if(strpos($fix_url,'diamond')){
                 if($product_name=='全サーバー共通'){
                     $product_real_name = 'DQ10';
                 }else{
                     $product_real_name = '';
                  }
            }else if(in_array($product_name,$array_dq)){
                $product_real_name = 'DQ10';
            }else{
               $product_real_name = '';
            }
          }
		//L2
        if($game_type=='L2'){
            if($product_name=='キャスディエン'){
               $product_real_name = 'キャスティエン';
            }
            if(strpos($fix_url,'diamond-gil')){
              $product_real_name = strip_tags($product_name);
            }
        }
      //AION
       if($game_type=='AION'){
           if(strpos($fix_url,'matubusi')||strpos($fix_url,'kakaran')){
                $product_name = str_replace('）','',$product_name);
                $tep_arr=explode('（',$product_name);
                $product_tep_name=$tep_arr[1].$tep_arr[0];
           }else if(strpos($fix_url,'ftb-rmt')){
                $tep_arr=explode('_',$product_name);
                $product_tep_name=$tep_arr[1].$tep_arr[0];
           }

           $iimy_tep_name = trim(preg_replace('/\s+/is','',$product_row['product_name']));
           $get_tep_name = trim(preg_replace('/\s+/is','',$product_tep_name));
           if($iimy_tep_name == $get_tep_name){
               $product_real_name =  $product_row['product_name']; 
		  }
		}
        if($game_type=='WZ'){
           if(strpos($fix_url,'matubusi')){
               if($product_name=='†Liberal†'){
                  $product_real_name = 'リベラル';
               }
           }
        }
        if($game_type=='latale'){
          if(strpos($fix_url,'rmtrank')||strpos($fix_url,'kakaran')){
             if($product_name=='ダイアモンド'){
                  $product_real_name= str_replace('ダイアモンド','ダイヤモンド',$product_name);
     	     }else if($product_name=='サファイヤ'){
                $product_real_name = str_replace('サファイヤ','サファイア',$product_name);
 	        }
         }
       }
       if($game_type=='HR'){
           $product_real_name = str_replace('共通サーバー','マビノギ英雄伝',$product_name);
       }
       if($game_type=='rose'){
          if(strpos($fix_url,'rmtrank')){
             if($product_name=='デネブ'){
                 $product_real_name = str_replace('デネブ','Deneb',$product_name);
            }else{
                 $product_real_name = str_replace('ベガ','Vega',$product_name);
            }
          }
        }
        if($game_type=='fez'){
          if(strpos($fix_url,'rmtrank')){
             if($product_name=='ケテル'){	
                 $product_real_name = str_replace('ケテル','Kether',$product_name);
              }else if($product_name=='イシュルド'){
                 $product_real_name = str_replace('イシュルド','Ishuld',$product_name);
              }else if($product_name=='エレミア'){
                 $product_real_name = str_replace('エレミア','Jeremiah',$product_name);
			  }
			}
		}
        if($game_type=='blade'){
           if(strpos($fix_url,'diamond')){
                $product_real_name = str_replace('こはく','琥珀',$product_name);
           }
       }
       if($game_type=='FF14'){
          if(strpos($fix_url,'mugenrmt')){
             $product_real_name = str_replace('(LEGASY)','',$product_name);
           }
       }
       if($game_type=='MU'){
          if(strpos($fix_url,'matubusi')){
             preg_match_all("|<a.*?><font.*?><u.*?><font .*?>(.*?)<\/font><\/u><\/font><\/a>|",$product_name,$temp_array);
                if(!empty($temp_array[1][0])){
                    $product_real_name = $temp_array[1][0].'の'.$temp_array[1][1];
                }else{
                    $product_real_name = str_replace('祝福','の祝福',$product_name);
                } 
             }
            if(strpos($fix_url,'kakaran') && $product_name!=''){
                $product_real_name = $product_name.'の祝福';           
            }
         }
       if($game_type=='ECO'){
	      if(strpos($fix_url,'diamond')){
              preg_match_all("|<a .*?>([a-zA-Z]+).*?<\/a>|",$product_name,$temp_array);	
             if($temp_array[1][0]==''){
             }else{
                $product_real_name=$temp_array[1][0];
             }
          }
        }
       if($game_type=='cronous'){
           preg_match_all("|<a.*?><font.*?><u.*?><font .*?>(.*?)<\/font><\/u><\/font><\/a>|",$product_name,$temp_array);
           if($temp_array[0][0]!=''){
              $product_real_name=  'アルテミス';
           }
       }
       if($game_type=='AA'){
         $name_mode_array = array('タヤン','ジン','キープローザ','ルシウス','エアンナ','アーランゼブ');
         $name_replace_array = array('Tahyang','Gene','Kyprosa','Lucius','Eanna','Aranzeb');
         if(strpos($fix_url,'rmtrank')){

           $product_real_name = str_replace($name_mode_array,$name_replace_array,$product_name);
         }
       }

   }
   $product_real_name = str_replace('<br />','',$product_real_name);
   $product_real_name = preg_replace('/<.*?>/','',$product_real_name);
   return $product_real_name;
}
function SBC2DBC($str) {
  $arr = array(
      '１','２','３','４','５','６','７','８','９','０','＋','－','％','＝'
      );
  $arr2 = array(
      '1','2','3','4','5','6','7','8','9','0','+','-','%','='
      );
  return str_replace($arr, $arr2, $str);
}
function tep_get_rate($str){
  $str = str_replace('あたり','=',$str);
  $str = str_replace(',','',$str);
  $str = str_replace('万','0000',$str);
  $str = str_replace('億','00000000',$str);
  if(preg_match('/1口=(\d+)M[^M\d]+(\d+)M=(\d+)/',$str,$arr)){
    return $arr;
  }else if(preg_match('/1口=(\d+)/',$str,$arr)){
    return $arr;
  }
}

/*@20141126
 *param1 $result_array 采集的数据
 *param2 $category_value 
 *param3 $game_type 游戏
 * param4 $site_name 网站
 * 商品index
 */ 

function get_price_info_new($result_array,$category_value,$game_type,$site_name,$product_key,$value){ 
   if($site_name == 'rmt1.jp'){
        $min=0;
        foreach($result_array[0]['price'] as $key=>$sec){
            if($key < $min){
              $min = $key;
            }
        }
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
              if(strpos($result_array[0]['inventory'][$product_key],'span')){
                  $inventory_array[0]=0;
              }
              if($category_value == 'buy'){
                  if($inventory_array[0]!=''){
                       foreach($result_array[0]['price'] as $section=> $value_array){
                          if($inventory_array[0]>$section){
                              $price = $result_array[0]['price'][$section][$product_key];
                              $result_inventory = $inventory_array[0];
                          }
                        }
                    }else{
                       $price = $result_array[0]['price'][$min][$product_key];    
                       $result_inventory=0;
                    }         
                }else{
                    if($inventory_array[0]!=''){
                         foreach($result_array[0]['price'] as $section=> $value_array){
                             $price = $result_array[0]['price'][$section][$product_key];
                             $result_inventory = $inventory_array[0];
                         }
                     }else{
                         $price = $result_array[0]['price'][$min][$product_key];    
                         $result_inventory=0;
                     } 

                 } 
          if($game_type=='RO'){
              $price = $price*100;
              $result_inventory = $result_inventory/100;
          }
          $result_str=$price;
    }
      

      $value = str_replace('<br />','',$value);
      $result_str = str_replace(',','',$result_str);
      $result_inventory = str_replace(',','',$result_inventory);
      $value = preg_replace('/<.*?>/','',$value);
      //数据入库
      $res = array('value'=>$value,'result_str'=>$result_str,'result_inventory'=>$result_inventory);
      return $res;



}


?>
