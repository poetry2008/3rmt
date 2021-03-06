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
  $category_type_all = $category;
  /*以下是正式采集*/
  $game_type=$game_type;
  foreach($category_type_all as $category_value){

    $url_array = $url_str_array[$category_value];
    $category_id_array = $category_id_str_array[$category_value];
    $site = $site_str[$category_value];
    $site_n = $site_info[$category_value];

     //正则
    $search_array = $search_array_match['old'][$category_value][$game_type];
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
        $log_str .= date('H:i:s',time()).str_repeat(' ',5).$game_type.'--'.$category_value.'--'.$site_n[$site_key]."\n";
      }
    }

    if($game_type == 'FF14'){
      $search_url['rmt.kakaran.jp_ff14_naeu']= 'http://rmt.kakaran.jp/ff14_naeu/';
      if($category_value == 'buy'){
        $search_url['www.mugenrmt.com_ff14_naeu']= 'http://www.mugenrmt.com/getDataDispWeb.ashx?parmType=gamemoneydisp&gameid=381&gamefilename=FF14NAEUrmt';
        $search_url['www.rmt-wm.com_ff14_naeu']= 'http://www.rmt-wm.com/buy/finalfantasyxiv|eu.html';
      }else{
        $search_url['www.rmt-wm.com_ff14_naeu']= 'http://www.rmt-wm.com/sale/finalfantasyxiv%7Ceu.html';
      }	
    }
    //采集所有网站的数据
    $all_result = get_all_result($search_url);
   foreach($all_result as $result){
        $url_info_tep = parse_url($result['info']['url']);
        $url_host[] = $url_info_tep['host'];
    }
   foreach($url_host as $key=>$host){
       if(isset($search_array_match['new'][$category_value][$host])&&!empty($search_array_match['new'][$category_value][$host])){
           $search_array[$host]=$search_array_match['new'][$category_value][$host];
        }else{
           $search_array[$host]=$search_array[$host];
        }
   }
    //处理特殊网站的汇率
    sleep(1);
    $other_rate_site = array('www.rmtsonic.jp','www.mugenrmt.com');
    $rate_all_site_info_array = get_other_rate($other_rate_site,$game_type);
    //通过正则获得所有网站的数据
    $all_site_info_array = get_info_array($all_result,$search_array);
//处理PSO2的梦幻
if($game_type=='PSO2'){
    foreach($all_site_info_array['www.mugenrmt.com']['inventory'] as $p_key=>$tep_invent){
          preg_match('/入荷通知/is',$tep_invent,$inventory_array_tep);
          preg_match('/即時取引/is',$all_site_info_array['www.mugenrmt.com']['products_name'][$p_key],$name_array_tep);
          if($inventory_array_tep[0]!='' && $name_array_tep[0]!=''){
             $name_tep = $all_site_info_array['www.mugenrmt.com']['products_name'][$p_key];
             $all_site_info_array['www.mugenrmt.com']['products_name'][$p_key]=str_replace('即時取引','即時取引_tep',$all_site_info_array['www.mugenrmt.com']['products_name'][$p_key]); 
                
          }
          $product_name_tp = preg_replace('/(.*?)\(即時取引\)/i','$1$2 $3',$name_tep);
          $product_name_tt = preg_replace('/(.*?)\(予約制\)/i','$1$2 $3',$all_site_info_array['www.mugenrmt.com']['products_name'][$p_key]);
          if($name_tep!=''&& $product_name_tt!='' && $product_name_tt==$product_name_tp){
             $all_site_info_array['www.mugenrmt.com']['products_name'][$p_key]=str_replace('予約制','即時取引',$all_site_info_array['www.mugenrmt.com']['products_name'][$p_key]); 
             preg_match('/入荷通知/is',$all_site_info_array['inventory']['products_name'][$p_key],$match_inventory);
             if($match_inventory[0]!=''){
                 $all_site_info_array['inventory']['products_name'][$p_key]=999;
             }

          }
    }
}else{
    foreach($all_site_info_array['www.mugenrmt.com']['inventory'] as $p_key=>$tep_invent){
          preg_match('/入荷通知/is',$tep_invent,$inventory_array_tep);
          preg_match('/予約制/is',$all_site_info_array['www.mugenrmt.com']['products_name'][$p_key],$name_array_tep);
          if($inventory_array_tep[0]!='' && $name_array_tep[0]!=''){
              $all_site_info_array['www.mugenrmt.com']['inventory'][$p_key]='入荷通知';
          }else if($inventory_array_tep[0]!=''){
              $all_site_info_array['www.mugenrmt.com']['inventory'][$p_key]=0;
          }
       
    }
   foreach($all_site_info_array['ftb-rmt.jp']['inventory'] as $t_key=>$tep_invent){
         preg_match('/zaikogire/is',$tep_invent,$inventory_array_tep);
         preg_match('/予約制/is',$all_site_info_array['ftb-rmt.jp']['products_name'][$p_key],$name_array_tep);
         if($inventory_array_tep[0]!='' && $name_array_tep[0]!=''){
             $all_site_info_array['ftb-rmt.jp']['inventory'][$p_key]='入荷通知';
         }else if($inventory_array_tep[0]!=''){
             $all_site_info_array['ftb-rmt.jp']['inventory'][$p_key]=0;
         }
   }
}

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
      if($site_info_arr['rate']==null||$site_info_arr['rate']==''){
        $site_info_arr['rate'] = $rate_all_site_info_array[$site_info_key]['rate'];
      }
      $site_value = array_search($site_info_key,$site);
      $category_id = $category_id_array[$site_value];
	  //如果是rmt1需要特殊处理
	  if($site_info_key=='rmt1.jp' || $site_info_key=='www.rmt-wm.com'){ 
              $site_info_arr['0-'.$site_info_arr['section_2']['0']]= $site_info_arr['price_1'];
              unset($site_info_arr['section_1']);
              unset($site_info_arr['price_1']);
            if($category_value == 'buy'){
                $site_info_arr[$site_info_arr['section_2']['0'].'-'.$site_info_arr['section_3']['0']]= $site_info_arr['price_2'];
                unset($site_info_arr['section_2']);
                unset($site_info_arr['price_2']);
                $site_info_arr[$site_info_arr['section_3']['0'].'-']= $site_info_arr['price_3'];
                unset($site_info_arr['section_3']);
                unset($site_info_arr['price_3']);
            }
            save2db($category_id,$site_value,$site_info_arr,$category_value,$game_type,$site_info_key);
          }else{
            save2db($category_id,$site_value,$site_info_arr,$category_value,$game_type,$site_info_key);
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
        $search_rate_list[$product_index][$site_key] = $collect_res_url[$site_key]['rate'];
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
          if(in_array(strtolower(trim(strip_tags($site_info['site_names'][$con_key]))),$rmt_name)){
            continue;
          }
          if($site_info['inventory'][$con_key] == 0 || $site_info['inventory'][$con_key] == '--'){
            continue;
          }
          if($site_info['price'][$con_key] == '--'){
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
          $site_info_arr = array(
                  'rate'=> $search_rate_list[$sk][$site_key],
                  'products_name'=> array($search_name_list[$sk][$site_key]),
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
  return $log_str;
}
//通过采集结果获得相关信息 返回数组 key = url_host value=array（价格等）
function get_info_array($curl_results,$search_array,$rate_only=false){
  $url_info_array = array();
  $searched_url = array();
  $search_key_arr = array();
  foreach($curl_results as $result){
    $url_info = parse_url($result['info']['url']);
    $search_key = $url_info['host'];
    $res = $result['results'];
    $encode_array = array('UTF-8','EUC-JP','Shift_JIS','ISO-2022-JP');
    $encode = mb_detect_encoding($res,$encode_array);
    if(strtolower($encode) != 'UTF-8'){
      $res = mb_convert_encoding($res,'UTF-8',$encode_array);
    }
    if($rate_only){
      $search_info_array = $search_array;
    }else{
      $search_info_array = $search_array[$search_key];
    }
    $res_search_array = array();
    //根据正则数组获得数据
    if($rate_only==false&&$search_key=='www.mugenrmt.com'){
      //调用获得 梦幻网站数据的方法
      $res_search_array = tep_get_mugenrmt_info($res,$search_info_array);
    }else{
    foreach($search_info_array as $key => $value){
      if($search_key == 'rmtrank.com'&&($key=='price'||$key=='site_names'||$key=='inventory')){
        preg_match_all('/'.$value.'/i',$res,$temp_array);
      }else{
        preg_match_all('/'.$value.'/is',$res,$temp_array);
      }
      if($key == 'rate'){
        if(preg_match('/=|＝/',strip_tags($temp_array[0][count($temp_array[0])-1]))){
          if($search_key == 'rmt.kakaran.jp'){
            $res_search_array[$key] = strip_tags($temp_array[0][0]);
          }else{
            $res_search_array[$key] = strip_tags($temp_array[0][count($temp_array[0])-1]);
          }
        }else{
          $res_search_array[$key] = strip_tags($temp_array[0][0]);
        }
      }else{
        foreach($temp_array[1] as $k => $v){ 
          if($v==''||trim($v)==''||strip_tags($temp_array[1][$k])==''){
            $temp_array[1][$k] = strip_tags($temp_array[2][$k]);
          }else if(strip_tags($temp_array[1][$k])!=''){
            $temp_array[1][$k] = strip_tags($temp_array[1][$k]);
          }
        }
        $res_search_array[$key] = $temp_array[1];
      }
    }
    }
    //针对FF14 多个URL 的特殊处理
    if(!in_array($search_key,$search_key_arr)){
      $search_key_arr[] = $search_key;
      $url_info_array[$search_key] = $res_search_array;
    }else{
      $t_arr = array();
      foreach($url_info_array[$search_key] as $arr_key => $arr_v){
        $t_arr[$arr_key] = array();
      }
      foreach($url_info_array[$search_key]['products_name'] as $p_key => $p_name){
        foreach($t_arr as $t_key => $t_value){
          $t_arr[$t_key][] = $url_info_array[$search_key][$t_key][$p_key];
        }
      }
      foreach($res_search_array['products_name'] as $p_key => $p_name){
        foreach($t_arr as $t_key => $t_value){
          $t_arr[$t_key][] = $res_search_array[$t_key][$p_key];
        }
      }
      /*
      $temp_search_arr = array();
      $temp_search_url = array();
      $temp_search_name = array();
      foreach($url_info_array[$search_key]['products_name'] as $p_key => $p_name){
        $temp_search_name[] = $p_name;
        $temp_search_url[] = $url_info_array[$search_key]['url'][$p_key];
      }
      foreach($res_search_array['products_name'] as $p_key => $p_name){
        $temp_search_name[] = $p_name;
        $temp_search_url[] = $res_search_array['url'][$p_key];
      }
      $temp_search_arr = array('products_name'=>$temp_search_name,'rate'=>$url_info_array[$search_key]['rate'],'url'=>$temp_search_url);
      $url_info_array[$search_key] = $temp_search_arr;
      */
      $url_info_array[$search_key] = $t_arr;
    }
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
function save2db($category_id,$site_value,$result_str_search,$category_value,$game_type,$site_name=''){
  $c_type = $category_value=='buy'?'1':'0';
  $category_update_query = mysql_query("update category set collect_date=now() where category_id='".$category_id."'");
  $rate_arr = SBC2DBC($result_str_search['rate']);
  $result_array[0] = $result_str_search;
  //当获取的数据商品名称为空(或这个页面没有数据)
  if(empty($result_array[0]['products_name'])){
    mysql_query("update product set is_error=1 where category_id='".$category_id."'");
  }
  $name_arr =array();
  $db_rate = tep_get_config_rate($game_type,$c_type,$site_name);

  foreach($result_array[0]['products_name'] as $product_key=>$value){
    if(in_array($value,$name_arr)){
      $name_arr[] = $value;
    }

$value=match_data_iimy($game_type,$category_value,$url_array[$site_value],$value);
//rmt1
if($value==''){
  continue;
}
   $rate_host_sql = "select * from product p,category c 
      where p.category_id=c.category_id and p.product_name='".trim($value)."'
      and c.category_name='".$game_type."' and c.category_type='".$c_type."' and c.site_id=(select site_id from site where site_url like 'http://www.iimy.co.jp%')";
   $rate_host_query = mysql_query($rate_host_sql);
   if($rate_host_row = mysql_fetch_array($rate_host_query)){
      $host_rate = $rate_host_row['rate'];
   }else{
      $host_rate = 1;
   }

   $price_info = format_price_inventory($result_str_search,$value,$product_key,$host_rate,$rate_arr,$site_name,$db_rate);
   /*
if($value!='' && $site_name=='rmt1.jp'){
   $price_info = get_price_info_new($result_array,$category_value,$game_type,$site_name,$product_key,$value);
}else if($value!='' && $site_name!='rmt1.jp'){
   $price_info = tep_get_price_info($result_array,$category_value,$game_type,$t_site_value,$product_key,$value);
}
*/
//    $price_info = tep_get_price_info($result_array,$category_value,$game_type,$t_site_value,$product_key,$value);
    $value = $price_info['value'];
    $result_str = $price_info['result_str'];
    $result_inventory = $price_info['result_inventory'];
    $rate = $price_info['rate'];
    $sort_order = 0;
  //判断数据库是否存在相同名称相同category_id 的商品
    $search_query = mysql_query("select product_id from product where category_id='".$category_id."' and product_name='".trim($value)."'");
  
  //当前游戏主站所有商品名称
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
      $products_query = mysql_query("update product set is_error=0, rate='".$rate."',product_price='".$result_str."',product_inventory='".$result_inventory."',sort_order='".$sort_order."' where category_id='".$category_id."' and product_name='".trim($value)."'");
    }else{
      if($value!='' && $allow_insert_mark = 1){
        $products_query = mysql_query("insert into product values(NULL,'".$category_id."','".trim($value)."','".$result_str."','".$result_inventory."','".$sort_order."',0,'".$rate."')");
      }
    }
  }
  if($site_name!='rmt.kakaran.jp'&&$site_name!='rmtrank.com'){
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
         preg_match('/rmt-wm/',$fix_url,$seach_url_wm);
         preg_match('/rmtrank/',$fix_url,$seach_url_rr);
         preg_match('/king/',$fix_url,$seach_url_king);

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
            if(strpos($fix_url,'ftb-rmt') || strpos($fix_url,'kakaran') || strpos($fix_url,'matubusi')){
              $product_real_name = str_replace('：',' ',$product_name);
			}
			if(strpos($fix_url,'mugenrmt')){
               $product_real_name = preg_replace('/(.*?)([0-9]+):(.*?)\(即時取引\)/i','$1$2 $3',$product_name);
               if(strpos($product_real_name,'10') == false){
                $product_real_name = preg_replace('/([1-9]+)/i','0$1',$product_real_name);
               }
			}
			if(!empty($seach_url_rr)){
               $product_real_name = str_replace('．',' ',$product_name);
            }
            if(!empty($seach_url_wm)){
             //01-フェオ   Ship01 フェオ
           $tep_data = explode('-',$product_name);
	       $iimy_tep_name = trim(preg_replace('/\s+/is','',$product_row['product_name']));
	       $get_tep_name = trim(preg_replace('/\s+/is','',$tep_data[1]));
               preg_match('/'.$get_tep_name.'/is',$iimy_tep_name,$seach_product_wm);
              if(!empty($seach_product_wm)){
                  $product_real_name = $product_row['product_name'];
              }
                
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
            if(strpos($product_name,'キャスディエン')!==false){
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
           }else if(strpos($fix_url,'mugenrmt')){
                $product_name = str_replace(array('1','2','-rmt'),'',$product_name);
                $tep_arr=explode('_',$product_name);
                $product_tep_name=$tep_arr[1].$tep_arr[0];
           }

           $iimy_tep_name = trim(preg_replace('/\s+/is','',$product_row['product_name']));
           $get_tep_name = trim(preg_replace('/\s+/is','',$product_tep_name));
           if($iimy_tep_name == $get_tep_name){
               $product_real_name =  $product_row['product_name']; 
		  }
          preg_match('/rmtrank/',$fix_url,$seach_url_rr);
          preg_match('/rmt-wm/',$fix_url,$seach_url_wm);

          if(!empty($seach_url_rr) || !empty($seach_url_wm)){
              $product_name= str_replace('-','',$product_name);
              $pname_len = mb_strlen($product_name,'UTF8');
              $str_len = $pname_len-2;
              $pro1 = mb_substr($product_name,0,$str_len,'utf-8');
              $pro2 = mb_substr($product_name,-2,2,'utf-8');
              $product_name_tep = $pro2.$pro1;
              if($product_name_tep==$iimy_tep_name){
                   $product_real_name =  $product_row['product_name'];
              }
          }
       }
//ARAD
          if($game_type=='ARAD'){
              preg_match('/mugenrmt/',$fix_url,$seach_url_mug);
              if(!empty($seach_url_mug)){
                   preg_match('/ディレジェ/is',$product_name,$tep_array);
                   if(!empty($tep_array)){
                       $product_real_name='ディレジエ';
                   }
               }
             preg_match('/rmtrank/',$fix_url,$seach_url_rank);
             if(!empty($seach_url_rank)){
                 if($product_name=='Diregee'){
                      $product_real_name = 'ディレジエ';
                  }
                  if($product_name=='Kain'){
                      $product_real_name = 'カイン';
                  }
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
          if(strpos($fix_url,'rmtrank')||strpos($fix_url,'kakaran')||strpos($fix_url,'mugenrmt')||!empty($seach_url_wm)){
             if(strpos($fix_url,'mugenrmt')){

               $product_name = str_replace('3','',$product_name);
               $product_name = trim($product_name);
             }
             if($product_name=='ダイアモンド'){
                  $product_real_name= str_replace('ダイアモンド','ダイヤモンド',$product_name);
     	     }else if($product_name=='サファイヤ'){
                $product_real_name = str_replace('サファイヤ','サファイア',$product_name);
 	     }
          }
       }
      if($game_type=='talesweave'){
         preg_match('/ミストフル/',$product_name,$seach_name_wm);
         if(!empty($seach_url_wm) && !empty($seach_name_wm)){
              $product_real_name = 'ミストラル';
         }

      }
       if($game_type=='HR'){
              $product_real_name = str_replace('共通サーバー','マビノギ英雄伝',$product_name);
              preg_match('/rmtrank/',$fix_url,$seach_url);
              if(!empty($seach_url)){
                 $product_real_name = str_replace('共通','マビノギ英雄伝',$product_name);
              }
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
          if(strpos($fix_url,'mugenrmt')||strpos($fix_url,'matubusi')){
             $product_real_name = str_replace('(LEGASY)','',$product_name);
             if(strpos($product_name,'Valefor')!==false){
               $product_real_name = 'Valefora';
             }
           }
          preg_match('/kakaran/',$fix_url,$seach_url_kk);
          preg_match('/rmt-wm/',$fix_url,$seach_url_wm);
          preg_match('/pastel-rmt/',$fix_url,$seach_url_psl);
          if(!empty($seach_url_kk)||!empty($seach_url_wm) || !empty($seach_url_psl) || !empty($seach_url_rr) || !empty($seach_url_king)){
             preg_match('/Valefor/',$product_name,$seach_name_tep);
             if(!empty($seach_name_tep)){
                  $product_real_name = str_replace('Valefor','Valefora',$product_name);
             }
          }
          if(!empty($seach_url_wm)){
             preg_match('/Zelera/',$product_name,$seach_name_tep);
             if(!empty($seach_name_tep)){
                 $product_real_name = str_replace('Zelera','Zalera',$product_name);
             }
          }
       }
       if($game_type=='tenjouhi'){
          if(!empty($seach_url_wm)){
              $product_real_name = str_replace('まほろぼ','まほろば',$product_name);
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

             preg_match('/rmtrank/',$fix_url,$seach_url_rank);
             preg_match('/kakaran/',$fix_url,$seach_url_kaka);
            if((!empty($seach_url_rank) ||!empty($seach_url_kaka)) &&$product_name!=''){
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
       if($game_type=='L1'){
         $name_mode_array = array('Altair','Arcturus','Canopus','Sirius','Vega','Unity','Rigel');
         $name_replace_array = array('アルタイル','アークトゥルス','カノープス','シリウス','ベガ','ユニティ','リゲル');
         if(strpos($fix_url,'mugenrmt')){

           $product_real_name = str_replace($name_mode_array,$name_replace_array,$product_name);
         }
       }
       if($game_type=='lakatonia'){
         preg_match('/(mugenrmt|rmt-wm)/',$fix_url,$seach_url_mg);
         if(!empty($seach_url_mg)){
             preg_match('/1/',$product_name,$seach_name_mg_1);
             preg_match('/2/',$product_name,$seach_name_mg_2);
             if(!empty($seach_name_mg_1)){
                  $product_real_name=  '第一サーバー';
             }
             if(!empty($seach_name_mg_2)){
                  $product_real_name=  '第二サーバー';
             }
		 }
     }
     if($game_type=='LH'){
         preg_match('/mugenrmt/',$fix_url,$seach_url_mg);
         if(!empty($seach_url_mg)){
            preg_match('/カベラ/',$product_name,$seach_name_mg_1);
            if(!empty($seach_name_mg_1)){
                $product_real_name=  'カペラ';
            }
        }
     }
     if($game_type=='EWD'){
       if(strpos($fix_url,'mugenrmt')){

         if(preg_match('/予約制/i',$product_name)){
           $product_real_name = ''; 
         }
       }
     }


   }
   $product_real_name = str_replace('<br />','',$product_real_name);
   $product_real_name = preg_replace('/<.*?>/','',$product_real_name);
   return $product_real_name;
}
function SBC2DBC($str) {
  $arr = array(
      '１','２','３','４','５','６','７','８','９','０','＋','－','％','＝','Ｍ'
      );
  $arr2 = array(
      '1','2','3','4','5','6','7','8','9','0','+','-','%','=','M'
      );
  return str_replace($arr, $arr2, $str);
}
function tep_get_rate($str,$flag=true){
  $str = str_replace('あたり','=',$str);
if($flag){
  $str = str_replace(',','',$str);
  $str = str_replace('万','0000',$str);
  $str = str_replace('億','00000000',$str);
}
  if(preg_match('/1口=(\d+)M[^M\d]+(\d+)M=(\d+)/',$str,$arr)){
    return $arr;
  }else if(preg_match('/1口=(\d+M)/',$str,$arr)){
    return $arr;
  }else if(preg_match('/1ロ=(\d+M)/',$str,$arr)){
    return $arr;
  }else if(preg_match('/1口=(\d+)m[^m\d]+(\d+)m=(\d+)/',$str,$arr)){
    return $arr;
  }else if(preg_match('/1口=(\d+m)/',$str,$arr)){
    return $arr;
  }else if(preg_match('/1ロ=(\d+m)/',$str,$arr)){
    return $arr;
  }else if(preg_match('/1口=(\d+)/',$str,$arr)){
    return $arr;
  }
}

function format_price_inventory($result_arr,$value,$index,$host_rate,$this_rate,$site_key,$db_rate=0){
  $this_price = 0;
  $old_inventory = $result_arr['inventory'][$index];
  if($site_key != 'www.rmt-wm.com'){
    $result_arr['inventory'][$index] = strip_tags($result_arr['inventory'][$index]);
  }else{
    if(strip_tags($result_arr['inventory'][$index]) != ''){
      $result_arr['inventory'][$index] = strip_tags($result_arr['inventory'][$index]);
    }else{
      $result_arr['inventory'][$index] = strip_tags($result_arr['inventory_preorder'][$index]);
    } 
  }
  $inventory_str = str_replace(',','',$result_arr['inventory'][$index]);
  if(preg_match('/[0-9]+/',$inventory_str,$inv_arr)){
    $inventory = $inv_arr[0];
  }else{
    $inventory = 0;
  }
  $rate_add = 1;
  if(preg_match('/(\d+)万/',$this_rate,$add_arr)){
    $rate_add = $add_arr[1];
  }
  if(preg_match('/(\d+)億/',$this_rate,$add_arr)){
    $rate_add = $add_arr[1];
  }
  $sub_rate = 1;
  if(preg_match('/(\d+)銀/',$this_rate,$add_arr)){
    $sub_rate = $add_arr[1];
  }
  $old_rate = $this_rate;
  $this_rate = tep_get_rate($this_rate);
  $temp_price = 0;
  $inv_price_arr = array();
  $temp_inv = 0;
  $old_preorder_inventory = $inventory;
  if($site_key == 'www.rmt-wm.com'){
    if(preg_match('/[0-9]+/',strip_tags($result_arr['inventory_preorder'][$index]),$preorder_array)){
      $preorder_inventory = $preorder_array[0];
    }else{
      $preorder_inventory = 0;
    } 
    $inventory += $preorder_inventory;
    if($old_preorder_inventory==0){
       $old_preorder_inventory = $preorder_inventory; 
    }
  }
  foreach($result_arr as $key => $val){
    if($key == 'inventory'||$key=='products_name'||$key=='rate'){
      continue;
    }
    $temp_price = strip_tags($val[$index]);
    $temp_price = str_replace(',','',$temp_price);
    if($key == 'price'){
      $this_price = $temp_price;
      break;
    }
    if(preg_match('/[0-9.]+/',$temp_price,$price_arr)){
      $temp_price = $price_arr[0];
    }else{
      $temp_price = 0;
    }
    if(preg_match('/(\d{0,})-(\d{0,})/',$key,$match_arr)){
      $inv_price_arr[] = $temp_price;
      if($match_arr[2]!=''&&$match_arr[1]!=''){
        if($inventory >= $match_arr[1] && $inventory <= $match_arr[2]){
          $this_price = $temp_price;
          break;
        }else{
          if($inventory >= $match_arr[2]){
            if($match_arr[2] < $temp_inv){
              break;
            }
            $this_price = $temp_price;
            $temp_inv = $match_arr[2];
          }
        }
      }else if($match_arr[2]==''){
        if($inventory >= $match_arr[1]){
          $this_price = $temp_price;
        }
      }
    }
  }
  $inventory = $old_preorder_inventory;
  if($inventory == 0&&!empty($inv_price_arr)){
    $this_price = min($inv_price_arr);
  }
  $this_inventory = $inventory;
  $res_rate = 1;
  // M 個 枚 特殊处理
  if($host_rate!=''&&$host_rate!=0&&!empty($this_rate)){
    if(preg_match('/M/',$this_rate[count($this_rate)-1]) || preg_match('/m/',$this_rate[count($this_rate)-1])){
      $this_rate[count($this_rate)-1] = str_replace(array('M','m'),'000000',$this_rate[count($this_rate)-1]);
      $add_sub = $host_rate/$this_rate[count($this_rate)-1];
     
    }else{
      $add_sub = $host_rate/$this_rate[count($this_rate)-1];
    }
    if($add_sub >= 1000000){
      if(preg_match('/(個|枚|M|m)/',$old_rate)){
        $add_sub = $add_sub/1000000;
      }
    }
    if($site_key == 'www.matubusi.com'){
      $old_rate = tep_get_rate($old_rate,false);
      $this_inventory = $this_inventory/$old_rate[1];
      $config_inventory = $config_inventory/$old_rate[1];
    }
    $this_price = $this_price*$add_sub;
    $this_inventory = $this_inventory/$add_sub;
    if(preg_match('/万/',$inventory_str)||preg_match('/億/',$inventory_str)){
      if($site_key != 'www.matubusi.com'){
        $this_inventory = $this_inventory/$rate_add;
        $config_inventory = $config_inventory/$rate_add;
      }
    }
    $res_rate = $this_rate[count($this_rate)-1];
  }
  if($sub_rate!=1){
    $this_price = $this_price*$sub_rate;
    $this_inventory = $this_inventory/$sub_rate;
  }
  //使用设置汇率
  if($db_rate!=0){
    $this_price = $this_price*$db_rate;
    $this_inventory = $this_inventory/$db_rate;
  }
  //预约库存处理

  if($this_inventory==0){
    //マツブシ http://www.matubusi.com 库存处理
    if(preg_match('/入荷通知/',$old_inventory)){
      $this_inventory = 999;
    }
  }

  $res = array('value'=>$value,'result_str'=>$this_price,'result_inventory'=>$this_inventory,'rate'=>$res_rate);
  return $res;
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
          $min = $key;
          break;
        }
         preg_match('/[0-9,]+(口|M|万|枚| 口|ゴールド|金|&nbsp;口)?/is',$result_array[0]['inventory'][$product_key],$inventory_array);
        $inventory_str = str_replace(',','',$result_array[0]['inventory'][$product_key]);
        preg_match('/\d{0,}/',$inventory_str,$inventory_array);
              if($category_value == 'buy'){
                  $result_inventory = $inventory_array[0];
                  if($inventory_array[0]!=''){
                       $i=0;
                       foreach($result_array[0]['price'] as $section=> $value_array){
                          if($inventory_array[0]>$section||$i==0){
                              $price = $result_array[0]['price'][$section][$product_key];
                              $i++;
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
                         }
                     }else{
                         $price = $result_array[0]['price'][$min][$product_key];    
                         $result_inventory=0;
                     } 

                 } 
          if($game_type=='RO'){
              $price = $price*100;
              if($result_inventory!=0){
                $result_inventory = $result_inventory/100;
              }
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
function get_other_rate($site_key_arr,$game_type){
  $site_rate_url = array(
    'FF11' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/ff11.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/ff11.html'),
    'RS' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/redstone.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/RedStone.html'),
    'DQ10' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/wii.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/dqx.html'),
    'TERA' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/TERA.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/tera.html'),
    'RO' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/ro.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/ro.html'),
    'ARAD' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/aradosenki.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/arad.html'),
    'nobunaga' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/nobunaga.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/nobunaga.html'),
    'PSO2' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/PSO2.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/PSO2.html'),
    'AION' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/aion.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/aion.html'),
    'FF14' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/FF14RMT.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/FF14NAEUrmt.html'),
    'genshin' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/InnocentWorld.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/fantasyfrontier.html'),
    'latale' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/latale.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/latale.html'),
    'L1' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/lineage.html'),
    'WZ' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/wizardry.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/Wizardry.html'),
    'blade' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/BladeSoul.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/BNS.html'),
    'CABAL' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/cabal.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/cabal.html'),
    'megaten' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/imagine.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/imagine.html'),
    'EWD' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/Elsword.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/elsword.html'),
    'LH' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/lucentheart.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/lucentheart.html'),
    'HR' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/mabinogi:heroes.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/mabinogiheroes.html'),
    'AA' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/ArcheAge.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/archeage.html'),
    'ECO' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/eco.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/eco.html'),
    'FNO' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/Finding%20Neverland%20Online.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/fno.html'),
    'SUN' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/sun.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/sun.html'),
    'talesweave' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/talesweaver.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/talesweaver.html'),
    'MU' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/mu.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/mu.html'),
    'MS' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/maplestory.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/MapleStory.html'),
    'cronous' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/cronous.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/cronous.html'),
    'tenjouhi' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/tenjohi.html'),
    'rose' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/rose.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/rose.html'),
    'hzr' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/harezora.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/haresora.html'),
    'dekaron' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/dekaron.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/dekaron.html'),
    'fez' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/fez.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/fez.html'),
    'moe' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/senmado.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/moe.html'),
    'mabinogi' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/Mabinogi.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/mabinogi.html'),
    'rohan' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/rohan.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/rohan.html'),
    'tartaros' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/Tartaros.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/tartaros.html'),
	'atlantica' => array('www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/atlantica.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/atlantica.html'),
  );
  $rate_match = array('rate'=>'((<span[^>]*>|※){0,}[1-9０１２３４５６７８９]{1,}(<\/span><span[^>]*>){0,}(口|ロ)(=|＝|あたり){1}[^<]*(<\/span>){0,}[1-9０１２３４５６７８９,]{1,}[^<]*)($|<){1}');
  $search_url = array();
  foreach($site_key_arr as $site_key){
    if(isset($site_rate_url[$game_type][$site_key])){
      $search_url[] = $site_rate_url[$game_type][$site_key];
    }
  }
  $all_result = get_all_result($search_url);
  //通过正则获得所有网站的数据
  $all_site_info_array = get_info_array($all_result,$rate_match,true);
  return $all_site_info_array;
}
function tep_get_config_rate($game,$category_type,$site_key){
  $sql = "select * from category_info where category_key='".$game."' limit 1";
  $query = mysql_query($sql);
  if($row = mysql_fetch_array($query)){
    $site_rate = $row['site_rate'];
    $category_rate = unserialize($site_rate);
    if(!empty($category_rate)){
      $type_rate = $category_rate[$category_type];
      $site_sql = "select site_id from site where site_url='http://".$site_key."/' order by site_id desc limit 1";
      $site_query = mysql_query($site_sql);
      if($row_site = mysql_fetch_array($site_query)){
        if(isset($type_rate[$row_site['site_id']])){
          return $type_rate[$row_site['site_id']];
        }else{
          return 0;
        }
      }else{
        return 0;
      }
    }else{
      return 0;
    }
  }else{
    return 0;
  }
}
/*
 * 处理梦幻网站的采集信息
 */
function tep_get_mugenrmt_info($result,$preg_arr){
$result = str_replace('<\/tr><tr>',"<\/tr>\n<tr>",$result);
$result = str_replace('<tr><td',"<tr>\n<td",$result);
$result = str_replace('<\/td><',"<\/td>\n<",$result);
$res_info = array();
if(preg_match_all("/".$preg_arr['title']."/i",$result,$arr)){
  $price_arr = array();
  $count_arr = array();
  foreach($arr[1] as $temp){
    if(preg_match_all("/".$preg_arr['price_title']."/i",$temp,$t_arr)){
      if(!in_array($t_arr[0],$count_arr)){
        $count_arr[] = $t_arr[0];
      }
    }
  }
  foreach($count_arr as $count_temp){
    $str_start = '';
    $str = '';
    foreach($count_temp as $temp_start){
      $temp_start = str_replace('/','\/',$temp_start);
      $str_start .= '[^>]*'.$temp_start;
      $str .= $preg_arr['match_price'];
    }
    $start_preg = str_replace('title_sum',$str_start,$preg_arr['match_title']);
    $t_preg = str_replace('price_sum',$str,$preg_arr['match_info']);
    $t_preg = $start_preg.$t_preg;
    if(preg_match_all("/".$t_preg."/i",$result,$s_arr)){
      foreach($s_arr[0] as $key => $del_value){
        $res = array();
        $index = 1;
        $res['products_name'] = $s_arr[$index][$key];
        $index++;
        foreach($count_temp as $value){
          $res[strip_tags($value)] = $s_arr[$index][$key];
          $index++;
        }
        $res['inventory'] = $s_arr[$index][$key];
        $res_info[] = $res;
      }
    }
  }
}
$last_res = array();
$last_res['products_name'] = array();
$last_res['price'] = array();
$last_res['inventory'] = array();
foreach($res_info as $temp_info){
  $last_res['products_name'][] = $temp_info['products_name'];
  $last_res['inventory'][] = $temp_info['inventory'];
  preg_match_all('/([0-9]+)口/i',$temp_info['inventory'],$temp_array);
  if($temp_array[1][0] != ''){
    $current_inventory = $temp_array[1][0];
  }else{
    $current_inventory = 999;
  }
  unset($temp_info['products_name']);
  unset($temp_info['inventory']);
  $t_price = 0;
  $i = 0;
  foreach($temp_info as $t_key => $t_value){
    preg_match_all('/[0-9]+/i',$t_key,$t_array);
    if($current_inventory >= $t_array[0][0] && $current_inventory <= $t_array[0][1]){

      $t_price = $t_value;
      break;
    }else if($current_inventory < $t_array[0][0]){

      $t_price = $t_value;
      break;
    }else if($current_inventory >  $t_array[0][1] && count($temp_info)-1 == $i){

      $t_price = $t_value;
    }
    $i++;
  }
  $last_res['price'][] = $t_price;
}
return $last_res;
}
?>
