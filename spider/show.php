<?php
//显示采集的结果
ini_set("display_errors", "On");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
set_time_limit(0);
header("Content-type: text/html; charset=utf-8");

//缓存设置
header('Expires:'.date('D, d M Y H:i:s',0).' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

//file patch
require('includes/configure.php');
require_once('class/spider.php');

//link db
$link = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
mysql_query('set names utf8');
mysql_select_db(DB_DATABASE);



//处理 category info 信息 
if($_GET['action'] == 'save_category_info'){
  $inventory_show = $_POST['inventory_show']?$_POST['inventory_show']:'0';
  $inventory_flag = $_POST['inventory_flag']?$_POST['inventory_flag']:'0';
  $category_key = $_GET['game'];
  $category_type = $_GET['flag_type'];
  if(empty($_POST['white_site_select'])){
    $white_site_select = null;
  }else{
    $white_site_select = serialize($_POST['white_site_select']);
  }
  if(empty($_POST['rate_site'])){
    $site_select = null;
  }else{
    $site_select = serialize($_POST['rate_site']);
  }
  if(empty($_POST['rate_text'])){
    $site_rate = null;
  }else{
    if($category_type==1){
      $site_rate = serialize(array('1'=>$_POST['rate_text'],'0'=>array()));
    }else{
      $site_rate = serialize(array('0'=>$_POST['rate_text'],'1'=>array()));
    }
  }

  $status_arr = array();
  $manual_arr = array();
  $auto_arr = array();
  foreach($_POST['p_key'] as $key => $value){
    $t_name = $_POST['p_name'][$key];
    $manual_arr[$t_name] = $_POST['manual'][$value];
  }
  $inventory_status = serialize($status_arr);
  $manual =  serialize($manual_arr);
  $auto = serialize($auto_arr);
  if(isset($_POST['info_id'])&&$_POST['info_id']!=''){
    //修改数据
    $sql = "select * from category_info where id='".$_POST['info_id']."'";
    $query = mysql_query($sql);
    if($row = mysql_fetch_array($query)){
      $rate_text_arr = unserialize($row['site_rate']);
      $rate_text_arr[$category_type] = $_POST['rate_text'];
      $site_rate = serialize($rate_text_arr);
    }
    $update_sql = "update category_info set 
      `white_site_select` ='".$white_site_select."',
      `site_select` ='".$site_select."', 
      `site_rate` ='".$site_rate."', 
      `inventory_show` ='".$inventory_show."', 
      `inventory_flag` ='".$inventory_flag."', 
      `manual` ='".$manual."'
    where id='".$_POST['info_id']."'";
    mysql_query($update_sql);
  }else{
   //新建数据
   $insert_sql = "INSERT INTO `category_info` VALUES 
   (NULL, 
   '".$category_key."', 
   '".$white_site_select."', 
   '".$site_select."', 
   '".$site_rate."', 
   '".$inventory_show."', 
   '".$inventory_flag."', 
   '".$manual."')";
   mysql_query($insert_sql);
  }
  $location_href = $_SERVER['HTTP_REFERER'];
  header('Location: ' . $location_href);
  exit;
}

//判断产品名背景颜色

//判断 其他网站 价格 库存北京颜色
/**
打开页面自动通过api自动获取主站数据
  */
if(function_exists('curl_init')){
  $api_arr = get_iimy_data();
  $api_name = $api_arr['name'];
  $api_info = $api_arr['info'];
}
//测试数据
function get_max_or_min($site_info_list,$type='min'){
  $site_id_arr = array();
  $site_price_arr = array();
  foreach($site_info_list as $site_info){
    $site_id_arr[] = $site_info['site_id'];
    $site_price_arr[] = $site_info['product_price'];
  }
  if($type=='min'){
    $index = array_search(min($site_price_arr),$site_price_arr);
  }else{
    $index = array_search(max($site_price_arr),$site_price_arr);
  }
  return $site_id_arr[$index];
}

//获得每一行的 黑名单白名单
/*
$p_name 产品名
$p_type 供应情况
*/
function get_style($api_name,$api_info,$host_site_id,$site_select,$p_name,$p_type,$flag_type,$game,$check_site_id){
  $api_index = array_search($p_name,$api_name);
  $info = $api_info[$api_index];
  //最大库存最小库存 库存
  $max_quantity = $info['max'];
  $min_quantity = $info['min'];
  $quantity = $info['quantity'];
  //实际库存的平均采购价格 或者 关联商品的贩卖单价 根据产品贩卖买取 
  $price_avg = $info['avg'];
  //计算设置＞倍率设置
  $product_cacl = $info['cacl'];
  $host_price = $info['price'];
  $yellow_site = 0;
  $white_site = array();
  //所有网站存入白名单
  $all_site_sql = "select * from product p,category c 
    where c.category_id = p.category_id
    and c.category_type='".$flag_type."'
    and c.category_name='".$game."' 
    and c.site_id != '".$host_site_id."'
    and p.product_name='".$p_name."' 
    order by p.sort_order desc";
  $all_site_query = mysql_query($all_site_sql);
  $all_site_info_pid = array();
  $site_select_count = 0;
  $site_select_sum = 0;
  while($all_site_row = mysql_fetch_array($all_site_query)){
  	if(in_array($all_site_row['site_id'],$site_select)){
  	  $site_select_count++;
  	  $site_select_sum += $all_site_row['product_price'];
  	}
  	$all_site_info_pid[$all_site_row['site_id']] = $all_site_row['product_id'];
    $white_site[$all_site_row['site_id']] = $all_site_row;
  }
  $site_select_avg = $site_select_sum/$site_select_count;
  $checked_site_id = array_search($check_site_id,$all_site_info_pid);
  $black_site = array();
  //if 「计算设置→汇率设置」的设置有的话 {
  if($product_cacl!=0){
    /*
    if 白名单里，如果有单价是赤字的网站，就将这些网站{
      放入黑名单
    }
    */
    foreach($white_site as $site_id => $site_info){
        if($flag_type == '1'){
          if($site_info['product_price'] <= $price_avg*$product_cacl){
            //放入黑名单
            $black_site[$site_id] = $site_info;
            //从白名单移出
            unset($white_site[$site_id]);
          }
        }else{
          if($site_info['product_price'] >= $price_avg/$product_cacl){
            //放入黑名单
            $black_site[$site_id] = $site_info;
            //从白名单移出
            unset($white_site[$site_id]);
          }
        }
    }
  //  if 供应正常的情况下{
    if($p_type == 'normal'){
  //    if 该商品的【最大库存】被设置了。{
      if($max_quantity!=0){
  //      if 「库存」 >= 「最大库存」{
        if($quantity>=$max_quantity){
  /*
          if 从白名单里，选出订单金额上限 <= 2000円的{
            放入黑名单
          }
  */
          foreach($white_site as $site_id => $site_info){
            if($site_info['product_price']*$site_info['product_inventory'] <= 2000 ){
              $black_site[$site_id] = $site_info;
              unset($white_site[$site_id]);
            }
          }
  /*
          if 白名单里如果有数据的话 {
            从名单里选出最便宜的单价，将其背景色变成黄色
          }else{
            $target_error = true;
          }
  */
          if(!empty($white_site)){
            $yellow_site = get_max_or_min($white_site);
          }else{
            $target_error = true;
          }
  //    }elseif 该商品的【最小库存】被设置了。&& 「库存」 >= (（「最大库存」 - 「最小库存」）/ 2 ） + 「最小库存」 {
        }else if($min_quantity!=0 && $quantity >= (($max_quantity-$min_quantity)/2) +$min_quantity){
  /*
          if 从白名单里，选出订单金额上限 <= 4000円的{
            放入黑名单
          }
          if 从白名单中，价格领先者（最便宜的）没有设置 && 【ターゲット設定】（设置目标）没有设置 && 比价格领先者（最便宜的）的平均单价少10%的网站{
            放入黑名单
          }
  */
          foreach($white_site as $site_id => $site_info){
            if($site_info['product_price']*$site_info['product_inventory'] <= 4000 ){
              $black_site[$site_id] = $site_info;
              unset($white_site[$site_id]);
              continue;
            }
            if(!in_array($site_id,$site_select)&&!in_array($site_id,$check_site_id)&&$site_info['product_price']<$site_select_avg*0.9){
              $black_site[$site_id] = $site_info;
              unset($white_site[$site_id]);
            }
          }
  /*
          if 白名单里如果有数据的话 {
            从名单里选出最便宜的单价，将其背景色变成黄色
          }else{
            $target_error = true;
          }
  */
          if(!empty($white_site)){
            $yellow_site = get_max_or_min($white_site);
          }else{
            $target_error = true;
          }
        }
  //    }elseif 该商品的「最小库存」的值设置了&& 「库存」 <= 「最小库存」{
      }else if ($min_quantity!=0&&$quantity <= $min_quantity){
  /*
        if 白名单里，订单金额上限 <= 10,000円{
          放入黑名单
        }
        if 白名单里，比现在的jp的单价便宜10%以上的网站{
          放入黑名单
        }
        if 白名单里，【ターゲット設定】（设置目标）没有选中的网站{
          放入黑名单
        }
        if 白名单里，比价格领先者（最便宜的）的平均单价少10%的网站{
          放入黑名单
        }
  */
        foreach($white_site as $site_id => $site_info){
          if($site_info['product_price']*$site_info['product_inventory'] <= 10000 ){
            $black_site[$site_id] = $site_info;
            unset($white_site[$site_id]);
            continue;
          }
          if($site_info['product_price']<$host_price*0.9){
            $black_site[$site_id] = $site_info;
            unset($white_site[$site_id]);
            continue;
          }
          if(!in_array($site_id,$check_site_id)){
            $black_site[$site_id] = $site_info;
            unset($white_site[$site_id]);
            continue;
          }
          if($site_info['product_price']<$site_select_avg*0.9){
            $black_site[$site_id] = $site_info;
            unset($white_site[$site_id]);
          }
        }
        /*
        if 白名单里有数据的话 {
          白名单里最便宜的值的背景色变成黄色。
        }else{
          $target_error = true;
        }
        */
        if(!empty($white_site)){
          $yellow_site = get_max_or_min($white_site);
        }else{
          $target_error = true;
        }
      }else{
        /*
        if 白名单里，订单金额上限 <= 4000円{
          放入黑名单
        }
        if 白名单里，【ターゲット設定】（设置目标）没有选中的网站{
          放入黑名单
        }
        */
        foreach($white_site as $site_id => $site_info){
          if($site_info['product_price']*$site_info['product_inventory'] <= 4000 ){
            $black_site[$site_id] = $site_info;
            unset($white_site[$site_id]);
            continue;
          }
          if(!in_array($site_id,$check_site_id)){
            $black_site[$site_id] = $site_info;
            unset($white_site[$site_id]);
          }
        }
        /*
        if 白名单里有数据的话{
          白名单里最便宜的值的背景色变成黄色。
        }else{
          $target_error = true;
        }
        */
        if(!empty($white_site)){
          $yellow_site = get_max_or_min($white_site);
        }else{
          $target_error = true;
        }
      }
  //  }elseif 供应不足的话 {
    }else if ($p_type == 'less') {
      /*
      if 白名单里，订单金额上限 <= 15,000円{
        放入黑名单
      }
      if 白名单里，比现在的jp的单价便宜10%以上的网站{
        放入黑名单
      }
      if 白名单里，【ターゲット設定】（设置目标）没有选中的网站{
        放入黑名单
      }
      if 白名单里，比价格领先者（最便宜的）的平均单价少10%的网站{
        放入黑名单
      }
      */
        foreach($white_site as $site_id => $site_info){
          if($site_info['product_price']*$site_info['product_inventory'] <= 15000 ){
            $black_site[$site_id] = $site_info;
            unset($white_site[$site_id]);
            continue;
          }
          if($site_info['product_price']<$host_price*0.9){
            $black_site[$site_id] = $site_info;
            unset($white_site[$site_id]);
            continue;
          }
          if(!in_array($site_id,$check_site_id)){
            $black_site[$site_id] = $site_info;
            unset($white_site[$site_id]);
            continue;
          }
          if($site_info['product_price']<$site_select_avg*0.9){
            $black_site[$site_id] = $site_info;
            unset($white_site[$site_id]);
          }
        }
      /*
      if 白名单里有数据的话{
        白名单里最便宜的值的背景色变成黄色。
      }else{
        $target_error = true;
      }
      */
      if(!empty($white_site)){
        $yellow_site = get_max_or_min($white_site);
      }else{
        $target_error = true;
      }
    }else{
      /*
      if 白名单里，价格领先者（最便宜的）和【ターゲット設定】（设置目标）没有选中的网站{
        放入黑名单
      }
      if 白名单里，比现在的jp的单价贵50%以上的网站{
        放入黑名单
      }
      */
      foreach($white_site as $site_id => $site_info){
        if(!in_array($site_id,$site_select)||!in_array($site_id,$check_site_id)){
          $black_site[$site_id] = $site_info;
          unset($white_site[$site_id]);
          continue;
        }
        if($site_info['product_price']>$host_price*1.5){
          $black_site[$site_id] = $site_info;
          unset($white_site[$site_id]);
        }
      }
      /*
      if 白名单里有数据的话{
        从名单里选出最高的单价，将其背景色变成黄色。注意是最高的单价
      }else{
        $target_error = true;
      }
      */
      if(!empty($white_site)){
        $yellow_site = get_max_or_min($white_site);
      }else{
        $target_error = true;
      }
    }
  }else{
      $target_error = true;
  }
  return array('yellow_site'=>$yellow_site,'is_error'=>$target_error,'white_site'=>$white_site,'black_site'=>$black_site);
}
function tep_display_attention_1_3($str) {
  $str2 = $str;
  if (strlen($str) > 8) {
    $ret .= floor(substr($str,0,strlen($str)-8)) . '億';
  }
  if (intval(substr($str,-8)) >= 10000000) {
    $tmp = substr();
    $ret .= intval(substr($str,-8)/10000000) . '千';
    $a = true;
  }
  if (intval(substr($str,-7)) >= 10000) {
    $ret .= intval(substr($str,-7)/10000) . '万';
  } else if ($str > 10000 && $a) {
    $ret .= '万';
  }
  if (intval(substr($str,-4)) >= 1000) {
    $ret .= intval(substr($str,-4)/1000) . '千';
  }
  if (intval(substr($str,-3))) {
    $ret .= intval(substr($str,-3));
  }
  if(intval($str) >= 1000){
    return '&nbsp;&nbsp;'.$ret.'（'.number_format($str2).'）';
  }else{
    return '&nbsp;&nbsp;'.$ret;
  }
}
function tep_get_rate_str($api_info,$game){
  $rate = $api_info[0]['rate'];
  $rate_other = $api_info[0]['rate_other'];
  $res_str = tep_display_attention_1_3($rate).$rate_other;
  if($game == 'ARAD'){
    $res_str = '1個あたり金貨&nbsp;&nbsp;'.$res_str;
  }else if($game == 'MU'){
  	$res_str = '1個あたり&nbsp;&nbsp;祝福の宝石&nbsp;&nbsp;'.$res_str;
  }else if($game == 'rose'){
    $res_str = '1個あたり貯金箱&nbsp;&nbsp;'.$res_str;
  }else if($game == 'RO'){
  	$res_str = '1個あたりインゴット&nbsp;&nbsp;'.$res_str;
  }else {
  	 $res_str = '1個あたり&nbsp;&nbsp;'.$res_str;
  }
  if(empty($api_info)){
    return '';
  }
  return $res_str;
}


function get_site_title_url($site_id,$game,$flag_type,$site_title_url){
  $sql = "select * from category 
     where site_id ='".$site_id."'
     and category_name ='".$game."'
     and category_type ='".$flag_type."' order by category_id desc limit 1";
  $query = mysql_query($sql);
  if($row = mysql_fetch_array($query)){
    $url = $row['category_url'];
    $url_info = parse_url($row['category_url']);
    $host_url = $url_info['host'];
    if($host_url=='rmt.kakaran.jp'){
      if($flag_type == 0){
        $url = str_replace('buy','sell',$url);
      }
      $return_url = $url.'?s=bank_transfer';
    }else if($host_url=='rmtrank.com'){
      if($flag_type == 0){
        $return_url = str_replace('content_id+1','content_id+2',$url);
      }else{
        $return_url = $url;
      }
    }else  if($host_url=='www.rmtsonic.jp'){
      $return_url = $site_title_url[$game][$host_url];
      if($flag_type==0){
        $return_url = str_replace('/games/','/sell/',$return_url);
      }
    }else if($host_url=='www.mugenrmt.com'){
      $return_url = $site_title_url[$game][$host_url];
    }else if($host_url=='www.asahi-rmt-service.com'){
      $return_url = $site_title_url[$game][$host_url];
      if($flag_type==0){
        if($game=='FF11'){
          $return_url = str_replace('sale_yoyaku.html','purchase.html',$return_url);
        }else{
          $return_url = str_replace('sale.html','purchase.html',$return_url);
        }
      }
    }else{
      $return_url = $url;
    }
    return $return_url;
  }else{
    return '';
  }
}
function get_iimy_data(){
    $game_name = !isset($_GET['game']) ? 'FF11' : $_GET['game'];
    $category_type = $_GET['flag'] == 'sell' ? '0' : '1';

    $category_query = mysql_query("select * from category where category_name='".$game_name."' and category_type='".$category_type."' and site_id='7'");
    while($category_row = mysql_fetch_array($category_query)){
       $iimy_url_array= parse_url($category_row['category_url']);
       preg_match_all("|[0-9]+_([0-9]+)|",$iimy_url_array['path'],$temp_category_id);
       $url= 'http://192.168.160.200/api.php?key=testkey1_98ufgo48d&action=clt&cpath='.$temp_category_id[1][0];
       //$url= 'http://www.iimy.co.jp/api.php?key=testkey1_98ufgo48d&action=clt&cpath='.$temp_category_id[1][0];
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url); //设置访问的url地址
       curl_setopt($ch, CURLOPT_TIMEOUT, 10); //设置超时
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); //设置连接等待时间
       curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_); //用户访问代理 User-Agent
       curl_setopt($ch, CURLOPT_REFERER,$url); //设置 referer
       curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1); //跟踪301
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回结果
       $contents = curl_exec($ch);
       curl_close($ch);
//正则
       if($game_name == 'MU'){
            $mode_array =array('products_name'=>'<name>(.*?)の宝石.*?<\/name>',
                   'price'=>'<price>([0-9,.]+)円<\/price>',
                   'inventory'=>'<quantity>(.*?)<\/quantity>',
                   'rate'=>'<rate>([0-9,.]+)<\/rate>',
                   'rate_other'=>'<rate_other>([^<]*)<\/rate_other>',
                   'cacl'=>'<cacl>([^<]*)<\/cacl>',
                   'max'=>'<max>([^<]*)<\/max>',
                   'min'=>'<min>([^<]*)<\/min>',
                   'avg'=>'<avg>([^<]*)<\/avg>',
             );
        }else{
             $mode_array =array('products_name'=>'<name>(.*?)の.*?<\/name>',
                   'price'=>'<price>([0-9,.]+)円<\/price>',
                   'inventory'=>'<quantity>(.*?)<\/quantity>',
                   'rate'=>'<rate>([0-9,.]+)<\/rate>',
                   'rate_other'=>'<rate_other>([^<]*)<\/rate_other>',
                   'cacl'=>'<cacl>([^<]*)<\/cacl>',
                   'max'=>'<max>([^<]*)<\/max>',
                   'min'=>'<min>([^<]*)<\/min>',
                   'avg'=>'<avg>([^<]*)<\/avg>',
             );
        }
//匹配数据
       $search_array = array();
       foreach($mode_array as $key=>$value){
          preg_match_all('/'.$value.'/is',$contents,$temp_array);
          $search_array[$key] = $temp_array[1];
       }

//插入数据库
      $tools_arr = array();
      $tools_index = array();
      foreach($search_array['products_name'] as $key=>$value){
         $tools_arr[$key] = array(
            'max' => $search_array['max'][$key],
             'min' => $search_array['min'][$key],
             'cacl' => $search_array['cacl'][$key],
             'avg' => $search_array['avg'][$key],
             'quantity' => $search_array['inventory'][$key],
             'price' => $search_array['price'][$key],
             'rate' => $search_array['rate'][$key],
             'rate_other' => $search_array['rate_other'][$key],
           );
         $tools_index[$key] = $value;
          $search_array['price'][$key] = str_replace(',','',$search_array['price'][$key]);
          $search_array['inventory'][$key] = str_replace(',','',$search_array['inventory'][$key]);
          $sort_order= 10000-$key;
          $search_query = mysql_query("select product_id from product where category_id='".$category_row['category_id']."' and product_name='".trim($value)."'");
          $rate_other = str_replace(',','',$search_array['rate_other'][$key]);
          $rate_other_value = 1;
          $rate_add = 1;
          if(preg_match('/千/',$rate_other)){
            $rate_add = 1000;
          }
          if(preg_match('/万/',$rate_other)){
            $rate_add = 10000;
          }
          if(preg_match('/億/',$rate_other)){
            $rate_add = 100000000;
          }
          if(preg_match('/\d+/',$rate_other,$arr)){
            $rate_other_value = $arr[0];
          }
          if($search_array['rate'][$key]<($rate_other_value*$rate_add)){
            $search_array['rate'][$key] = ($rate_other_value*$rate_add);
          }
          if(mysql_num_rows($search_query) == 1){
              $products_query = mysql_query("update product set is_error=0, product_price='".$search_array['price'][$key]."',product_inventory='".$search_array['inventory'][$key]."',sort_order='".$sort_order."',rate='".$search_array['rate'][$key]."' where category_id='".$category_row['category_id']."' and product_name='".trim($value)."'");
          }else{
             if($value!=''){
               $products_query = mysql_query("insert into product values(NULL,'".$category_row['category_id']."','".$value."','".$search_array['price'][$key]."','".$search_array['inventory'][$key]."','".$sort_order."',0,'".$search_array['rate'][$key]."')");
             }
          }
       }

      //数据库原有的商品名称
       $search_query = mysql_query("select product_name from product where category_id='".$category_row['category_id']."'");
       $product_old_list[] = array();
       while($row_tep = mysql_fetch_array($search_query)){
         $product_old_list[] = $row_tep['product_name'];
      }
     //新获取的数据已经不包含数据库的数据,删除
      foreach($product_old_list as $product_old_name){
        if(!in_array($product_old_name,$search_array['products_name']) && !empty($search_array['products_name'])){
          $products_query = mysql_query("delete from product where category_id='".$category_row['category_id']."' and product_name='".$product_old_name."'");
         }
      }
   }
   return array(
     'info' => $tools_arr,
     'name' => $tools_index);
}


function show_effective_number($str,$count=2){ 
  if("$str" == 0){
    return 0;
  }
  if($str<1){
    $str = $str+1;
    $arr = str_split($str);
    $add_flag = false;
    $i=0;
    foreach($arr as $value){
      if($add_flag){
        if($value!=0){
          break;
        }
        $i++;
      }
      if($value=='.'){
        $add_flag = true;
      }
    }
    $i = $i+$count;
    for($j=$count;$j>0;$j--){
      if(substr($str,$i+1,1)==0){
        $i--;
      }else{
        break;
      }
    }
    return '0.'.substr($str,2,$i);
  }
  $arr = explode('.',$str);
  if(count($arr)==1){
    return $str.$str_end;
  }else{
    $arr_end = str_split($arr[1]);
    $index=0;
    foreach($arr_end as $end){
      if($end!=0){
        $index+=2;
        break;
      }
      $index++;
    }
    if($index>$count+1){
      return $arr[0].$str_end;
    }
    for($j=$index;$j>0;$j--){
      if(substr($arr[1],$index-1,1)==0){
        $index--;
      }else{
        break;
      }
    }
    if(substr($arr[1],0,$index)==''){
      return $arr[0];
    }else{
      return $arr[0].'.'.substr($arr[1],0,$index);
    }
  }
}

//格式化数据
function price_number_format($price){

  $price = number_format($price);
  $price = str_replace(',','<span style="font-size:14px">,</span>',$price);
  return $price;
}


$config_value_query = mysql_query("select config_key,config_value from config where config_key='COLLECT_IS_STOP_STATUS'");
$config_value_array = mysql_fetch_array($config_value_query);
$update_status = $config_value_array['config_value'];
//跟新列表排序

$game_str_sql = "select * from category_sort order by sort ,  category_name  ";
$game_str_query = mysql_query($game_str_sql);
$game_str_array = array();
$has_row = false;
while($game_str_row = mysql_fetch_array($game_str_query)){
  $has_row = true;
  $game_str_array[$game_str_row['category_keyword']] = $game_str_row['category_name'];
}
  $old_game_str_array = array('FF14'=>'FF14',
                        'RO'=>'ラグナロク',
                        'RS'=>'レッドストーン',
                        'FF11'=>'FF11',
                        'DQ10'=>'DQ10',
                        'L2'=>'リネージュ2',
                        'ARAD'=>'アラド戦記',
                        'nobunaga'=>'信長の野望',
                        'PSO2'=>'PSO2',
                        'L1'=>'リネージュ',
   'TERA'=> 'TERA',
   'AION'=> 'AION',
   'CABAL'=> 'CABAL',
   'WZ'=> 'ウィザードリィ',
   'latale'=> 'ラテール',
   'blade'=> 'ブレイドアンドソウル',
   'megaten'=> '女神転生IMAGINE',
   'EWD'=> 'エルソード',
   'LH'=> 'ルーセントハート',
   'HR'=> 'マビノギ英雄伝',
   'AA'=> 'ArcheAge',
   'ThreeSeven'=> '777タウン',
   'ECO'=> 'エミルクロニクル',
   'FNO'=> 'FNO',
   'SUN'=> 'SUN',
   'talesweave'=> 'テイルズウィーバー',
   'MU'=> 'MU',
   'C9'=> 'C9',
   'MS'=> 'メイプルストーリー',
   'cronous'=> 'クロノス',
   'tenjouhi'=> '天上碑',
   'rose'=> 'ローズオンライン',
   'hzr'=> '晴空物語',
   'dekaron'=> 'デカロン',
   'fez'=> 'ファンタジーアースゼロ',
   'lakatonia'=> 'ラカトニア',
   'moe'=> 'マスターオブエピック',
   'mabinogi'=> 'マビノギ',
   'WF'=> '戦場のエルタ',
   'rohan'=> 'ROHAN',
            'tartaros'=> 'タルタロス',
            'atlantica'=> 'アトランティカ',
   'genshin'=> '幻想神域',
                      );
if(!$has_row){
  $game_str_array = $old_game_str_array;
}
if($_GET['action'] == 'get_parent_category'){
  $game_id_array = array('FF14'=>'456',
      'RO'=>'171',
      'RS'=>'195',
      'FF11'=>'168',
      'DQ10'=>'597',
      'L2'=>'169',
      'ARAD'=>'218',
      'nobunaga'=>'178',
      'PSO2'=>'591',
      'L1'=>'170',
      'TERA'=> '547',
      'AION'=> '396',
      'CABAL'=> '200',
      'WZ'=> '556',
      'latale'=> '325',
      'blade'=> '628',
      'megaten'=> '319',
      'EWD'=> '462',
      'LH'=> '250',
      'HR'=> '571',
      'AA'=> '623',
      'ThreeSeven'=> '538',
      'ECO'=> '299',
      'FNO'=> '544',
      'SUN'=> '231',
      'talesweave'=> '190',
      'MU'=> '422',
      'C9'=> '550',
      'MS'=> '209',
      'cronous'=> '287',
      'tenjouhi'=> '337',
      'rose'=> '331',
      'hzr'=> '559',
      'dekaron'=> '302',
      'fez'=> '436',
      'lakatonia'=> '565',
      'moe'=> '322',
      'mabinogi'=> '179',
      'WF'=> '431',
      'rohan'=> '382',
      'tartaros'=> '450',
      'atlantica'=> '278',
      'genshin'=> '620');
  $game_str_array = $old_game_str_array;
  $url = 'http://192.168.160.200/api.php?key=testkey1_98ufgo48d&action='.$_GET['action'];
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url); //设置访问的url地址
  curl_setopt($ch, CURLOPT_TIMEOUT, 10); //设置超时
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); //设置连接等待时间
  curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_); //用户访问代理 User-Agent
  curl_setopt($ch, CURLOPT_REFERER,_REFERER_); //设置 referer
  curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1); //跟踪301
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回结果
  $result = curl_exec($ch);
  curl_close($ch);
  $result = json_decode($result);
  $insert_category_sort_array = array();
  foreach($game_id_array as $key => $value){
    $temp_info_array = array();
    $sort = 9999;
    $temp_flag = false;
    foreach($result as $res ){
      if($res->categories_id == $value ){
        $temp_flag = true;
        $sort = $res->sort_order;
        break;
      }
    }

    if($temp_flag == true){
      $temp_info_array['keyword'] = $key;
      $temp_info_array['name'] = $game_str_array[$key];
      $temp_info_array['sort'] = $sort;
      $insert_category_sort_array[] = $temp_info_array;
    }
  }
  //插入排序
  $sql_clear = "TRUNCATE TABLE category_sort";
  mysql_query($sql_clear);
  foreach($insert_category_sort_array as $info_arr){
    $sql_insert = "INSERT INTO `category_sort`  values (null, '".$info_arr['keyword']."','".$info_arr['name']."','".$info_arr['sort']."')";
    mysql_query($sql_insert);
  }
  exit;
}
//更改更新状态
if($_GET['action'] == 'update_status'){
  $update_status = $_POST['update_status'];
  $result = mysql_query("update config set config_value='".$update_status."' where config_key='COLLECT_IS_STOP_STATUS'");
}

$tep_flag= (isset($_GET['flag']) ? '&flag='.$_GET['flag'].'' : '');
$product_type = $_GET['flag'] == 'sell' ? '買取' : '購入';
$game = !isset($_GET['game']) ? 'FF11' : $_GET['game'];
$flag = $_GET['flag'] == 'sell' ? 'sell' : 'buy';
$flag_type = $_GET['flag'] == 'sell'?0:1;
$other_site_url_arr = array('http://rmtrank.com/','http://rmt.kakaran.jp/');
//获得 category info 信息
$c_info_sql = "select * from category_info where category_key='".$game."' order by id desc limit 1";
$c_info_query = mysql_query($c_info_sql);
if($c_info_row = mysql_fetch_array($c_info_query)){
  $c_info_arr = $c_info_row;
  $show_cid = $c_info_row['id'];
  $show_white_site_select = unserialize($c_info_row['white_site_select']);
  $show_site_select = unserialize($c_info_row['site_select']);
  $temp_site_rate = unserialize($c_info_row['site_rate']); 
  $show_site_rate = $temp_site_rate[$flag_type];
  $show_inventory_show = $c_info_row['inventory_show']; 
  $show_inventory_flag = $c_info_row['inventory_flag']; 
  $show_manual =  unserialize($c_info_row['manual']); 
}else{
  $c_info_arr = array();
}

//获得 rmt 主站信息
$host_site_sql = "select * from site where site_url = 'http://www.iimy.co.jp/' limit 1";
$host_site_query = mysql_query($host_site_sql);
$host_site_info = mysql_fetch_array($host_site_query);
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
  <title>'.$game_str_array[$_GET['game']].'</title>
  </head>
  <body>';
echo '<br>';
$page_title = $game_str_array[$game].' RMT '.$product_type.'価格相場';
echo '<a href="javascript:void(0)" onclick="get_category_info()"><span class="pageHeading" >';
echo $page_title;
echo '</span></a><br><br>';
echo '<select onchange="show_game_info(this.value)">';
foreach($game_str_array as $key => $value){
  echo '<option value="'.$key.'" '.($_GET['game']==$key ? 'selected="selected"' : '').'>'.$value.'</option>';
}
echo '</select>';
?>
<div id="category_info_box" style="min-width:550px;display:none; position:absolute; background:#FFFF00; width:70%;z-index:2; /*bottom:0;margin-top:40px;right:0; width:200px;*/">
<?php
echo '<form name="form_category_info" method="post" action="show.php?action=save_category_info&flag_type='.$flag_type.'&game='.$game.'">';
//获得所有网站

//获得表示状态信息

//获得库存显示

?>
<div class="info_title">
  <div class="price_title_left">
  <?php echo $page_title;?>
  <input type="hidden" name="info_id" value="<?php echo $show_cid;?>">
  </div>
  <div class="price_title_right">
  <a href="javascript:void(0)" onclick="close_category_info()">X</a>
  </div>
</div>
<div class="info_site">
  <div class="left_info">
  プライスリーダー
  <br>
  *最大5つ
  </div>
  <div class="right_info_site">
  <?php 
  $other_site_sql = "select * from site where site_id !='".$host_site_info['site_id']."' order by sort_order asc";
  $other_site_query = mysql_query($other_site_sql);
  $site_only_arr = array();
  $site_some_arr = array();
  $site_info_arr = array();
  $site_some_info_arr = array();
  $site_all_info = array();
  while($other_site_row = mysql_fetch_array($other_site_query)){
    $site_all_info[] = $other_site_row;
    if(!in_array($other_site_row['site_url'],$other_site_url_arr)){
      $site_only_arr[] = $other_site_row['site_url'];
      $site_info_arr[] = $other_site_row;
    }else{
      $site_some_arr[] = $other_site_row;
      $site_some_info_arr[] = $other_site_row['site_name'];
    }
  }
  $count = count($site_only_arr);
  if($count%2!=0){
    $count = $count/2+1;
  }else{
    $count = $count/2;
  }
  echo '<ul>';
  $i=1;
  $index=0;
  while($i<=$count){
    echo '<li>';
    $checked_temp = '';
    if(in_array($site_info_arr[$index]['site_id'],$show_site_select)){
      $checked_temp = ' checked ';
    }
    echo '<input type="checkbox" '.$checked_temp.' name="rate_site[]" value="'.$site_info_arr[$index]['site_id'].'">';
    echo '&nbsp;';
    echo $site_info_arr[$index]['site_name'];
    echo '</li>';
    echo '<li>';
    echo '<input  style="text-align:right" type="text"name="rate_text['.$site_info_arr[$index]['site_id'].']" size="10"  value="'.($show_site_rate[$site_info_arr[$index]['site_id']]!=0?$show_site_rate[$site_info_arr[$index]['site_id']]:0).'">';
    echo '</li>';
    $index++;
    $checked_temp = '';
    if(in_array($site_info_arr[$index]['site_id'],$show_site_select)){
      $checked_temp = ' checked ';
    }
    echo '<li>';
    echo '<input type="checkbox" '.$checked_temp.' name="rate_site[]" value="'.$site_info_arr[$index]['site_id'].'">';
    echo '&nbsp;';
    echo $site_info_arr[$index]['site_name'];
    echo '</li>';
    echo '<li>';
    echo '<input style="text-align:right" type="text" name="rate_text['.$site_info_arr[$index]['site_id'].']" size="10" value="'.($show_site_rate[$site_info_arr[$index]['site_id']]!=0?$show_site_rate[$site_info_arr[$index]['site_id']]:0).'">';
    echo '</li>';
    $index++;
    if($i==1){
      echo '<li>';
      echo '*通貨レート調整';
      echo '</li>';
    }else{
      echo '<li>';
      echo '</li>';
    }
    $i++;
  }
  $row=1;
  asort($site_some_info_arr);
  foreach($site_some_info_arr as $some_key => $some_row){
    $checked_temp = '';
    if(in_array($site_some_arr[$some_key]['site_id'],$show_site_select)){
      $checked_temp = ' checked ';
    }
    echo '<li>';
    echo '<input type="checkbox" '.$checked_temp.' name="rate_site[]" value="'.$site_some_arr[$some_key]['site_id'].'">';
    echo '&nbsp;';
    echo $some_row;
    echo '</li>';
    if($row %3 == 0){
      $some_input_id = $site_some_arr[$some_key]['site_id'];
      echo '<li>';
      echo '<input style="text-align:right" type="text" name="rate_text['.$some_input_id.']" size="10" value="'.($show_site_rate[$site_some_arr[$some_key]['site_id']]!=0?$show_site_rate[$site_some_arr[$some_key]['site_id']]:0).'">';
      echo '</li>';
      echo '<li>';
      echo '</li>';
    }
    $row++;
  }
  echo '</ul>';
  ?>
  </div>
</div>
<div style="clear:both; width:10px;">&nbsp;</div>
<div class="info_site">
  <div class="left_info">
  ターゲット設定
  </div>
  <div class="right_info_site">
  <ul>
  <?php foreach($site_all_info as $site_info){
    $temp_checked = '';
    if(in_array($site_info['site_id'],$show_white_site_select)){
      $temp_checked = ' checked ';
    }
    echo '<li>';
    echo '<input type="checkbox" name="white_site_select[]" value="'.$site_info['site_id'].'" '.$temp_checked.'>';
    echo '&nbsp;';
    echo $site_info['site_name'];
    echo '</li>';
  }
  ?>
  </ul>
  </div>
</div>
<div style="clear:both; width:10px;">&nbsp;</div>
<div class="info_inventory">
  <div class="left_info">
  表示設定
  </div>
  <div class="right_info">
  <?php
   $show_inventory_show_check = ((isset($show_inventory_show)&&$show_inventory_show==1)||!isset($show_inventory_show))?' checked ':'';
   $show_inventory_flag_check = (isset($show_inventory_flag)&&$show_inventory_flag==1)?' checked ':'';
  ?>
  <div class="left" style="width:17%">
  <input type="checkbox" id="inventory_show" value="1" <?php echo $show_inventory_show_check;?> name="inventory_show">
  <label for="inventory_show">数量表示</label>
  </div>
  <div class="left">
  <input type="checkbox" id="inventory_flag" value="1" <?php echo $show_inventory_flag_check;?> name="inventory_flag">
  <label for="inventory_flag">在庫ゼロ非表示</label>
  </div>
  </div>
</div>
<div style="clear:both; width:10px;">&nbsp;</div>
<div class="info_show_status">
  <div class="left_info">
  供給状況
  </div>
  <div class="right_info">
   <div class="left" style="width:17%">
   <div class="left">
     <?php
       //输出当前分类下的所有JP 产品
       $p_list_sql =  "select * from product p,category c 
          where c.category_id = p.category_id
          and c.category_type='".$flag_type."'
          and c.category_name='".$game."' 
          and c.site_id = '".$host_site_info['site_id']."'
          order by p.sort_order desc";
        $p_list_query = mysql_query($p_list_sql);
        $p_name_info = array();
       $p_name_arr = array();
       while($p_list_row = mysql_fetch_array($p_list_query)){
         $p_name_info[] = $p_list_row;
         $p_name_arr[] = $p_list_row['product_name'];
       }
     ?>
     <ul>
     <?php 
         foreach($p_name_arr as $p_key => $p_name){
         echo '<li>';
         echo $p_name;
         echo '<input type="hidden" name="p_key[]" value="'.$p_key.'">';
         echo '<input type="hidden" name="p_name[]" value="'.$p_name.'">';
         echo '</li>';
       }
     ?>
     </ul>
   </div>
   </div>
    <div class="left" style="width:60%">
      <div class="left">
      <ul>
      <?php 
       //手动的所有设置
       foreach($p_name_arr as $p_key => $p_name){
         echo '<li>';
         echo '<input type="radio" name="manual['.$p_key.']" value="normal" '.(($show_manual[$p_name]=='normal'||$show_manual[$p_name]==null)?' checked ':'').'>';
         echo '普通';
         echo '&nbsp';
         echo '<input type="radio" name="manual['.$p_key.']" value="less" '.($show_manual[$p_name]=='less'?' checked ':'').'>';
         echo '小 ';
         echo '&nbsp';
         echo '<input type="radio" name="manual['.$p_key.']" value="zero" '.($show_manual[$p_name]=='zero'?' checked ':'').'>';
         echo  '無し';
         echo '</li>';
       }
      ?>
      </ul>
      </div>
    </div>
  </div>
</div>
<div style="clear:both; width:10px;">&nbsp;</div>
<div >
  <div class="center">
  <input type="submit" name="submit" value="Save">
  <input type="button" value="clear" onclick="close_category_info()">
  </div>
</div>
</div>
<?php
echo '</form>';
?>
</div>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
 $("input[name='rate_site[]']").click( function() {
      if ( $("input[name='rate_site[]']:checked").length > 5 ) {
        $(this).attr("checked",false);
        alert("check more than 5");
      }
    });
    var info_left_widht = $('#info_left').width();
    var info_width = document.body.clientWidth-info_left_widht;
    $("#site_info").width(info_width);
    $(".dataTableContent_right").each(function(){
       $(this).find("td").each(function(i){
          $(this).css({'border-left':'1px','border-right':'1px'});
          $(this).css({'border-top':'1px solid #808080','border-bottom':'1px solid #808080'});
           
         });
       });
    });
$(window).resize(function() {
    var info_left_widht = $('#info_left').width();
    var info_width = document.body.clientWidth-info_left_widht;
    $("#site_info").width(info_width);
    });
function close_category_info(){
  $('#category_info_box').hide();
}
function get_category_info(){
  $('#category_info_box').show();
}
function onmouseover_style(_this,index,c_flag){
  if(c_flag == false){
    var class_temp = $(_this).attr('class');
    var z=0;
    var z_temp;
    $('.'+class_temp).each(function(i){
        if(z==0){
          $(this).css({'border-top':'3px solid #fc9700'});
          z++;
        }
      $(this).css({'border-left':'3px solid #fc9700','border-right':'3px solid #fc9700'});
      z_temp = this;
        });
      $(z_temp).css({'border-bottom':'3px solid #fc9700'});
  }else{
    var class_first = c_flag+'_price';
    var class_second = c_flag+'_inventory';
    var zf=0;
    var zs=0;
    var zf_temp;
    var zs_temp;
    $('.'+class_first).each(function(i){
        if(zf==0){
          $(this).css({'border-top':'3px solid #fc9700'});
          zf++;
        }
      $(this).css({'border-left':'3px solid #fc9700'});
      zf_temp = this;
        });
    $(zf_temp).css({'border-bottom':'3px solid #fc9700'});
    $('.'+class_second).each(function(i){
        if(zs==0){
          $(this).css({'border-top':'3px solid #fc9700'});
          zs++;
        }
      $(this).css({'border-right':'3px solid #fc9700'});
      zs_temp = this;
        });
    $(zs_temp).css({'border-bottom':'3px solid #fc9700'});

  }
  var temp;
  $("#tr_div_"+index).find("td").each(function(i){
    $(this).css({'border-bottom':'3px solid #fc9700','border-top':'3px solid #fc9700'});
    temp = this;
  });
  $(temp).css({'border-right':'3px solid #fc9700'});
  var x=0;
  $("#tr_start_"+index).find("td").each(function(i){
    if(x==0){
      $(this).css({'border-left':'3px solid #fc9700','border-bottom':'3px solid #fc9700','border-top':'3px solid #fc9700'});
      x++;
    }else{
      $(this).css({'border-bottom':'3px solid #fc9700','border-top':'3px solid #fc9700'});
    }
  });

}
function onmouseout_style(_this,index,c_flag){
  if(c_flag == false){
    var class_temp = $(_this).attr('class');
    var z=0;
    var z_temp;
    $('.'+class_temp).each(function(i){
        if(z==0){
          $(this).css({'border-top':'1px solid #808080'});
          z++;
        }
      $(this).css({'border-left':'1px ','border-right':'1px'});
      z_temp = this;
        });
      $(z_temp).css({'border-bottom':'1px solid #808080'});
  }else{
    var class_first = c_flag+'_price';
    var class_second = c_flag+'_inventory';
    var zf=0;
    var zs=0;
    var zf_temp;
    var zs_temp;
    $('.'+class_first).each(function(i){
        if(zf==0){
          $(this).css({'border-top':'1px solid #808080'});
          zf++;
        }
      $(this).css({'border-left':'1px'});
      zf_temp = this;
        });
    $(zf_temp).css({'border-bottom':'1px solid #808080'});
    $('.'+class_second).each(function(i){
        if(zs==0){
          $(this).css({'border-top':'1px solid #808080'});
          zs++;
        }
      $(this).css({'border-right':'1px'});
      zs_temp = this;
        });
    $(zs_temp).css({'border-bottom':'1px solid #808080'});

  }
  var temp;
  $("#tr_div_"+index).find("td").each(function(i){
    $(this).css({'border-bottom':'1px solid #808080','border-top':'1px solid #808080'});
    temp = this;
  });
  $(temp).css({'border-right':'1px'});
  var x = 0;
  $("#tr_start_"+index).find("td").each(function(i){
    if(x==0){
      $(this).css({'border-left':'1px solid','border-bottom':'1px solid #808080','border-top':'1px solid #808080'});
      x++;
    }else{
      $(this).css({'border-bottom':'1px solid #808080','border-top':'1px solid #808080'});
    }
    i++;
  });
}
function get_category_sort(){
  $('body').css('cursor','wait');$("#wait").show();
  setTimeout(function(){
  $.ajax({
    type: "POST",
    data:"",
    async:false,
    url: 'show.php?action=get_parent_category',
    success: function(msg) {
      setTimeout('read_time()',1000);
      location.href = location.href
    }
  });
  },500);
}
var flag_mark='<?php echo $tep_flag;?>';
function show_game_info(ele){
window.location.href='show.php?game='+ele+flag_mark;
}


function check_submit(str){

    document.form1.action = 'show.php?action=save&flag='+str; 
    document.form1.submit(); 
}
<?php //多选框全选动作?>
function check_all(){
  var checkbox_name = 'site[]';
  check_flag = $("#num").val();
  for (i = 0; i < document.form1.elements[checkbox_name].length; i++) {
    if (check_flag == 1) {
      document.form1.elements[checkbox_name][i].checked = true;
      $("#num").val(0);
    } else {
      document.form1.elements[checkbox_name][i].checked = false;
      $("#num").val(1);
    }
  }
}

function cancel_all(){

  site = document.getElementsByName('site[]');
  for(x in site){

    site[x].checked = false;
  }
}

//wait hide
function read_time(){
  $("#wait").hide();
}

function update_data(){
  $('body').css('cursor','wait');$("#wait").show();
  setTimeout(function(){
  $.ajax({
    type: "POST",
    data: 'game=<?php echo isset($_GET['game']) ? $_GET['game'] : 'FF11';?>&flag=<?php echo 'has';?>',
    async:false,
    url: 'collect.php',
    success: function(msg) {
      var error_str = msg.split("|||");
      if(error_str[0] == 'error'){ 
        alert('URL：'+error_str[1]+'\n更新が失敗しましたので、しばらくもう一度お試しください。');
        $('body').css('cursor','');
        setTimeout('read_time()',1000);
       location.href="show.php<?php echo (isset($_GET['flag']) ? '?flag='.$_GET['flag'].'&num='.time() : '').(isset($_GET['game']) ? (isset($_GET['flag']) ? '&game='.$_GET['game'] : '?game='.$_GET['game']) : '').'&';?>error_url="+error_str[1];
      }else{
        $('body').css('cursor','');
        setTimeout('read_time()',1000); 
        location.href="show.php<?php echo (isset($_GET['flag']) ? '?flag='.$_GET['flag'].'&num='.time() : '').(isset($_GET['game']) ? (isset($_GET['flag']) ? '&game='.$_GET['game'] : '?game='.$_GET['game']) : '');?>";
      }
    }
  }); 
  },500);
}
function update_data_status(update_status){
  if(update_status==0){
     var flag = window.confirm("确定要停止更新吗?如果停止将不会继续更新数据,直到点击继续更新时才会继续更新数据,");
  }else{
  
     var flag =  window.confirm("确定要继续更新吗?如果继续更新将继续更新数据,直到点击停止更新时才会停止更新数据,");
  }
  if(flag){ 
      var update_status = (update_status==0)?1:0;
      $.ajax({
         type: "POST",
         data:"update_status="+update_status,
         false:true,
         url: 'show.php?action=update_status',
         success: function(msg) {
         location.href = "show.php<?php echo (isset($_GET['flag']) ? '?flag='.$_GET['flag'].'&num='.time() : '').(isset($_GET['game']) ? (isset($_GET['flag']) ? '&game='.$_GET['game'] : '?game='.$_GET['game']) : '');?>"
         }
       });
  }

}
//update products price
function update_products_price(category_name,products_name,products_type,products_id){
  $('body').css('cursor','wait');$("#wait").show();
  setTimeout(function(){
  $.ajax({
    type: "POST",
    data: 'category_name='+category_name+'&products_name='+products_name+'&products_type='+products_type+'&products_id='+products_id,
    async:false,
    url: 'ajax.php?action=update_products_price',
    success: function(msg) {
      setTimeout('read_time()',1000); 
      $('body').css('cursor','');
    }
  }); 
  },500);
}


</script>
<?php
  $site_title_url = array(
    'FF11' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/ff/sale_yoyaku.html' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/ff11.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/ff11.html'),
    'RS' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/redstone/sale.html' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/redstone.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/RedStone.html'),
    'DQ10' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/dqx/sale.html' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/wii.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/dqx.html'),
    'TERA' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/tera/sale.html' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/TERA.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/tera.html'),
    'RO' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/ragnarok/sale.html' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/ro.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/ro.html'),
    'ARAD' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/aradosenki.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/arad.html'),
    'nobunaga' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/nobunaga.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/nobunaga.html'),
    'PSO2' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/PSO2.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/PSO2.html'),
    'AION' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/aion.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/aion.html'),
    'FF14' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/FF14RMT.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/FF14NAEUrmt.html'),
    'genshin' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/InnocentWorld.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/fantasyfrontier.html'),
    'latale' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/latale/sale.html' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/latale.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/latale.html'),
    'L1' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/lineage.html'),
    'WZ' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/wizardry.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/Wizardry.html'),
    'blade' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/BladeSoul.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/BNS.html'),
    'CABAL' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/cabal.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/cabal.html'),
    'megaten' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/imagine.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/imagine.html'),
    'EWD' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/Elsword.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/elsword.html'),
    'LH' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/lucentheart.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/lucentheart.html'),
    'HR' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/mabinogi:heroes.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/mabinogiheroes.html'),
    'AA' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/ArcheAge.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/archeage.html'),
    'ECO' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/eco.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/eco.html'),
    'FNO' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/Finding%20Neverland%20Online.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/fno.html'),
    'SUN' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/sun.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/sun.html'),
    'talesweave' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/talesweaver.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/talesweaver.html'),
    'MU' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/mu.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/mu.html'),
    'MS' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/maplestory.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/MapleStory.html'),
    'cronous' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/cronous.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/cronous.html'),
    'tenjouhi' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/tenjohi.html'),
    'rose' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/rose.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/rose.html'),
    'hzr' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/harezora.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/haresora.html'),
    'dekaron' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/dekaron.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/dekaron.html'),
    'fez' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/fez.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/fez.html'),
    'moe' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/senmado.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/moe.html'),
    'mabinogi' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/Mabinogi.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/mabinogi.html'),
    'rohan' => array('www.asahi-rmt-service.com' => 'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/rohan.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/rohan.html'),
    'tartaros' => array('www.asahi-rmt-service.com'=>'http://www.asahi-rmt-service.com/' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/Tartaros.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/tartaros.html'),
 'atlantica' => array('www.asahi-rmt-service.com'=>'http://www.asahi-rmt-service.com/atlantica/sale.html' , 'www.rmtsonic.jp' => 'http://www.rmtsonic.jp/games/atlantica.html' , 'www.mugenrmt.com' => 'http://www.mugenrmt.com/rmt/atlantica.html'),
  );
//获取主站的URL

echo '<table width="100%"><tr height="30px"><td'.(!isset($_GET['flag']) || $_GET['flag'] == 'buy' ? ' style="background-color:#666666;"' : '').'><a href="show.php?flag=buy'.(isset($_GET['game']) ? '&game='.$_GET['game'] : '').'&num='.time().'">販売</a></td><td'.($_GET['flag'] == 'sell' ? ' style="background-color:#666666;"' : '').'><a href="show.php?flag=sell'.(isset($_GET['game']) ? '&game='.$_GET['game'] : '').'&num='.time().'">買取</a></td>';
$game = isset($_GET['game']) ? $_GET['game'] : 'FF11';
/*
$game_info = array('FF14'=>'1個あたり  10万（100,000）ギル(Gil)',
                   'RO'=>'1個あたり  1億（100,000,000）ゼニー(Zeny)',
                   'RS'=>'1個あたりインゴット  1本(1億ゴールド)',
                   'FF11'=>'1個あたり  100万（1,000,000）ギル(Gil)',
                   'DQ10'=>'1個あたり  10万（100,000）ゴールド(Gold)',
                   'L2'=>'1個あたり  1億（100,000,000）アデナ(Adena)',
                   'ARAD'=>'1個あたり金貨  10枚(1,000万ゴールド)',
                   'nobunaga'=>'1個あたり  10万（100,000）貫',
                   'PSO2'=>'1個あたり  100万（1,000,000）メセ',
                   'L1'=>'1個あたり  100万（1,000,000）アデナ(Adena)',
     'TERA'=>'1個あたり  1万（10,000）金(金貨)',
     'AION'=>'1個あたり  1億（100,000,000）ギーナ',
     'CABAL'=>'1個あたり  1億（100,000,000）アゼル',
     'WZ'=>'1個あたり  1千万（10,000,000）G',
     'latale'=>'1個あたり  10億（1,000,000,000）エリー(ELY)',
     'blade'=>'1個あたり  10金',
     'megaten'=>'1個あたり  1千万（10,000,000）マッカ',
     'EWD'=>'1個あたり  1億（100,000,000）ED',
     'LH'=>'1個あたり  1億（100,000,000）スター',
     'HR'=>'1個あたり  1千万（10,000,000）ゴールド(Gold)',
     'AA'=>'1個あたり  100金',
     'ThreeSeven'=>'1個あたり  10枚',
     'ECO'=>'1個あたり  1千万（10,000,000）ゴールド(Gold)',
     'FNO'=>'1個あたり  1千（1,000）G',
     'SUN'=>'1個あたり  1億（100,000,000）ハイム',
     'talesweave'=>'1個あたり  1千万（10,000,000）シード(Seed)',
     'MU'=>'1個あたり 祝福の宝石  10個',
     'C9'=>'1個あたり  1千万（10,000,000）ゴールド(Gold)',
     'MS'=>'1個あたり  10億（1,000,000,000）メル',
     'cronous'=>'1個あたり  100億（10,000,000,000）クロ',
     'tenjouhi'=>'1個あたり  1億（100,000,000）銀銭',
     'rose'=>'1個あたり貯金箱  1個(1億ジュリー)',
     'hzr'=>'1個あたり  100G(金)',
     'dekaron'=>'1個あたり  1億（100,000,000）ディル(DIL)',
     'fez'=>'1個あたり  100万（1,000,000）ゴールド(Gold)',
     'lakatonia'=>'1個あたり  100G',
     'moe'=>'1個あたり  100万（1,000,000）ゴールド(Gold)',
     'mabinogi'=>'1個あたり  100万（1,000,000）ゴールド(Gold)',
     'WF'=>'1個あたり  1千万（10,000,000）ゴールド(Gold)',
     'rohan'=>'1個あたり  1千万（10,000,000）クロン',
     'genshin'=>'1個あたり  100金',
     'lineage'=>'1個あたり  100万（1,000,000）アデナ(Adena)',
     'atlantica'=>'1個あたり  10億（1,000,000,000）G',
     'tartaros'=>'1個あたり  100万（1,000,000）リル',
);
*/
$date_query = mysql_query("select max(collect_date) as collect_date from category where category_name='".$game."' and site_id!=7");
$date_array = mysql_fetch_array($date_query);
echo '<td align="right">';
echo '最終更新&nbsp;&nbsp;'.date('Y/m/d H:i',strtotime($date_array['collect_date'])).'&nbsp;&nbsp;&nbsp;';
echo tep_get_rate_str($api_info,$game);
echo '</td></tr></table>';
//获得所有网站的 标题 
//数据输出的三个数组
$left_info = array();
$right_info = array();
$left_title = array();
$right_title = array();
//获得释放显示库存 


$show_inventory = isset($show_inventory_show)?$show_inventory_show:1;
//获得是否显示库存0的数据
$zero_inventory = $show_inventory_flag;
$left_title[] = '<td class="dataTableHeadingContent_order" width="5%" style="min-width:70px; style=" text-align:left; padding-left:20px;"  nowrap="nowrap">'.(isset($_GET['game']) ? $game_str_array[$_GET['game']] : 'FF11').'</td>';
if($flag == 'buy'){
  $left_title[] = '<td width="5%" style="min-width:70px; text-align:center;">最安</td><td width="5%" style="min-width:70px; text-align:center;">次点</td>';
}else{
  $left_title[] = '<td width="5%" style="min-width:70px; text-align:center;">最高</td><td width="5%" style="min-width:70px; text-align:center;">次点</td>';
}
$all_site_sql = 'select * from site order by sort_order';
$all_site_query = mysql_query($all_site_sql);
$host_site_id = 0;
$site_other_arr = array();
$colspan = '';
if($show_inventory == 1){
  $colspan = ' colspan="2" ';
}
while($all_site_row = mysql_fetch_array($all_site_query)){
  $link_url = get_site_title_url($all_site_row['site_id'],$game,$flag_type,$site_title_url);
  if($all_site_row['site_id']==$host_site_info['site_id']){
    $host_site_id = $all_site_row['site_id'];
    $left_title[] = '<td '.$colspan.' style="min-width:75px;" width="8%" class="dataTableHeadingContent_order"><a href="'.
      $link_url.'" target="_blank">'.$all_site_row['site_name'].
      '</a></td>';
  }else{
    $site_other_arr[$all_site_row['site_id']] = $all_site_row['site_id'];
    $right_title[$all_site_row['site_id']] = '<td '.$colspan.' style="min-width:100px;" width="8%" class="dataTableHeadingContent_order"><a href="'.
      $link_url.'" target="_blank">'.$all_site_row['site_name'].
      '</a></td>';
  }
  $link_url = '';
}
$data_info_sql = "select * from product p,category c 
  where c.category_id = p.category_id
  and c.category_type='".$flag_type."'
  and c.category_name='".$game."' 
  and c.site_id = '".$host_site_id."'
  order by p.sort_order desc";
$data_info_query = mysql_query($data_info_sql);
$host_info_arr = array();
$name_arr = array();
$price_info_arr = array();
while($data_info_row = mysql_fetch_array($data_info_query)){
  $name_arr[] = $data_info_row['product_name'];
  $price_info_arr[] = array();
  $host_info[] = $data_info_row;
}
$all_site_info_arr = array();
$all_site_name_arr = array();
$unshow_site = array();
foreach($site_other_arr as $site_id){
  $data_info_sql = "select * from product p,category c 
    where c.category_id = p.category_id
    and c.category_type='".$flag_type."'
    and c.category_name='".$game."' 
    and c.site_id = '".$site_id."'
    order by p.product_id desc";
  $data_info_query = mysql_query($data_info_sql);
  $site_info_arr = array();
  $other_name_arr = array();
  while($data_info_row = mysql_fetch_array($data_info_query)){
    if(in_array($data_info_row['product_name'],$name_arr)&&$data_info_row['product_price']>0){
      $price_info_arr[array_search($data_info_row['product_name'],$name_arr)][] = $data_info_row['product_price'];
    }
    $site_info_arr[] = $data_info_row;
    $other_name_arr[] = $data_info_row['product_name'];
  }
  if(!empty($site_info_arr)){
    $all_site_info_arr[$site_id] = $site_info_arr;
    $all_site_name_arr[$site_id] = $other_name_arr;
  }else{
    $unshow_site[] = $site_id;
  }
}
$price_checked_query = mysql_query("select * from products_price where category_name='".$game."' and product_type='".$flag_type."'");
$check_name = array();
$check_pid = array();
while($price_checked_row = mysql_fetch_array($price_checked_query)){
  $check_name[] = $price_checked_row['product_name'];
  $check_pid[] = $price_checked_row['product_id'];
}


//信息显示
echo '<table style="min-width:750px;" width="100%" cellspacing="0" cellpadding="0" border="0">';
echo '<tr>';
echo '<td widht="23%" valign="top" id="info_left">';
echo '<table class="dataTableContent_right" width="100%" cellspacing="0" cellpadding="2" border="0">';
//输出标题栏
echo '<tr height="30px" class="dataTableHeadingRow">';
foreach($left_title as $title){
  echo $title;
}
echo '</tr>';

//输出详细信息
$style_row_all = array();
foreach($name_arr as $index => $name){
  $check_pid_row = $check_pid[array_search($name,$check_name)];
  $p_status_value = $show_manual[$name];
  $bg_color_type = $p_status_value;
  if($bg_color_type == 'zero'){
    $p_bg_color = '#0000FF';
  }else if($bg_color_type == 'less'){
    $p_bg_color = '#00EEEE';
  }else{
    $p_bg_color = '#FFFFFF';
  }
  $p_type = $p_status_value;
  $style_row_arr = get_style($api_name,$api_info,$host_site_id,$site_select,$name,$p_type,$flag_type,$game,$show_white_site_select);
  $style_row_all[$index] = $style_row_arr;
  
  echo '<tr class="tr_line" height="30px" id="tr_start_'.$index.'">';
  echo '<td  bgcolor="'.$p_bg_color.'" class="td_name" align="left" nowrap="nowrap" onmouseover="onmouseover_style(this,\''.$index.'\',false)"; onmouseout="onmouseout_style(this,\''.$index.'\',false)" >';
  echo $name;
  echo '</td>';
  //最高最低值
  $price_arr = array_unique($price_info_arr[$index]);
  if($flag == 'buy'){
    sort($price_arr);
  }else{
    rsort($price_arr);
  }
  echo '<td class="td_first" align="center" onmouseover="onmouseover_style(this,\''.$index.'\',false)"; onmouseout="onmouseout_style(this,\''.$index.'\',false)" >';
  if(count($price_arr)>0){
    echo "<input type='radio' ".(($check_pid_row == -1 )?'checked':'')." name='radio_".$index."' id='first_value_".$index."' onclick='update_products_price(\"".$game."\",\"".$name."\",\"".$flag."\",-1)'>";
    echo '<label for="first_value_'.$index.'">'.price_number_format($price_arr[0]).'円</label>';
  }else{
    echo '<label for="first_value">-円</label>';
  }
  echo '</td>';
  //最高最低第二个值
  echo '<td class="td_second" align="center" onmouseover="onmouseover_style(this,\''.$index.'\',false)"; onmouseout="onmouseout_style(this,\''.$index.'\',false)" >';
  if(count($price_arr)>1){
    echo "<input type='radio' ".(($check_pid_row == 0 )?'checked':'')." name='radio_".$index."' id='second_value_".$index."' onclick='update_products_price(\"".$game."\",\"".$name."\",\"".$flag."\",0)'>";
    echo '<label for="second_value_'.$index.'">'.price_number_format($price_arr[1]).'円</label>';
  }else{
    echo '<label for="second_value">-円</label>';
  }
  echo '</td>';
  //主站信息
  if($show_inventory == 1){
    if($zero_inventory == 1&&$host_info[$index]['product_inventory']==0){
      echo '<td class="td_host_price" onmouseover="onmouseover_style(this,\''.$index.'\',\'td_host\')"; onmouseout="onmouseout_style(this,\''.$index.'\',\'td_host\')" >&nbsp;</td><td class="td_host_inventory" onmouseover="onmouseover_style(this,\''.$index.'\',\'td_host\')"; onmouseout="onmouseout_style(this,\''.$index.'\',\'td_host\')" >&nbsp;</td>';
    }else{
      echo '<td class="td_host_price" align="right" onmouseover="onmouseover_style(this,\''.$index.'\',\'td_host\')"; onmouseout="onmouseout_style(this,\''.$index.'\',\'td_host\')"  style="min-width:50px"><font style="font-weight: bold;">';
      echo price_number_format($host_info[$index]['product_price']).'円';
      echo '</font></td>';
      echo '<td class="td_host_inventory" align="right" onmouseover="onmouseover_style(this,\''.$index.'\',\'td_host\')"; onmouseout="onmouseout_style(this,\''.$index.'\',\'td_host\')"  style="min-width:50px">';
      echo show_effective_number($host_info[$index]['product_inventory']).'個';
      echo '</td>';
    }
  }else{
    echo '<td class="td_host_price" align="right" onmouseover="onmouseover_style(this,\''.$index.'\',false)"; onmouseout="onmouseout_style(this,\''.$index.'\',false)"  style="min-width:45px"><font style="font-weight: bold;">';
    if($zero_inventory == 1&&$host_info[$index]['product_inventory']==0){
      echo '&nbsp;';
    }else{
      echo price_number_format($host_info[$index]['product_price']).'円';
    }
    echo '</font></td>';
  }
  echo '</tr>';
}
echo '</table>';
echo '</td>';
echo '<td width="77%" valign="top">';
echo '<div id="site_info" style="min-width:465px;height:100%; overflow-x:scroll;">';
echo '<table style="min-width:465px;" class="dataTableContent_right" width="100%" cellspacing="0" cellpadding="2" border="0">';
$show_site_arr = array();
echo '<tr height="30px" class="dataTableHeadingRow">';
foreach($right_title as $r_key => $title){
  $r_site_id = $site_other_arr[$r_key];
  if(!in_array($r_site_id,$unshow_site)){
    $show_site_arr[] = $r_site_id;
    echo $title;
  }
}
echo '</tr>';
foreach($name_arr as $index => $name){
  //开始处理 处理 rmt 主站之外每一个网站的数据信息显示
  $style_row_arr = $style_row_all[$index];
  if(isset($style_row_arr['black_site'])&&!empty($style_row_arr['black_site'])){
    $black_site_arr = array();
    foreach($style_row_arr['black_site'] as $s => $value){
      $black_site_arr[] = $s;
    }
  }
  echo '<tr class="tr_line" height="30px" id="tr_div_'.$index.'">';
  //其他网站信息
  foreach($show_site_arr as $site_id){
    $index_other = array_search($name,$all_site_name_arr[$site_id]);
    $error_str = '';
    $radio_str = '';
    if($index_other === false||$temp_price = price_number_format($all_site_info_arr[$site_id][$index_other]['product_price'])<0){
      $temp_price_str = '-円';
      $temp_inventory_str = '-個';
      $temp_pid = '';
    }else{
      $temp_price = price_number_format($all_site_info_arr[$site_id][$index_other]['product_price']); 
      $temp_inventory = show_effective_number($all_site_info_arr[$site_id][$index_other]['product_inventory']);
      $temp_pid = $all_site_info_arr[$site_id][$index_other]['product_id'];
      if($all_site_info_arr[$site_id][$index_other]['is_error']==1){
        $error_str = '<span id="enable_img" ><img width="10" height="10" src="images/icon_alarm_log.gif"></span>';
      }
      $temp_inventory_str = '';
      $temp_price_str = "<font style='font-weight: bold;'><input ".(($check_pid_row == $temp_pid )?'checked':'')." name='radio_".$index."' type='radio'
        id='radio_".$temp_pid."_".$index."' onclick='update_products_price(\"".$game."\",\"".$name."\",\"".$flag."\",\"".$temp_pid."\")'>";
      $temp_price_str .= "<label for='radio_".$temp_pid."_".$index."'>".$temp_price."円</label></font>";
      $temp_inventory_str .= $temp_inventory.'個';
    }
    if(in_array($site_id,$black_site_arr)){
      $td_bg_color = ' bgcolor="#808080" ';
    }else if($site_id == $style_row_arr['yellow_site']){
      $td_bg_color = ' bgcolor="#FFFF00" ';
    }else {
      $td_bg_color = ' bgcolor="#ffffff" ';
    }
    if($show_inventory == 1){
      if($zero_inventory == 1&&$temp_inventory==0){
        echo '<td '.$td_bg_color.' class="td_'.$site_id.'_price" onmouseover="onmouseover_style(this,\''.$index.'\',\'td_'.$site_id.'\')"; onmouseout="onmouseout_style(this,\''.$index.'\',\'td_'.$site_id.'\')" >&nbsp;</td><td class="td_'.$site_id.'_inventory" onmouseover="onmouseover_style(this,\''.$index.'\',\'td_'.$site_id.'\')"; onmouseout="onmouseout_style(this,\''.$index.'\',\'td_'.$site_id.'\')" >&nbsp;</td>';
      }else{
        if($error_str == ''){
          $style_str = ' style="min-width:70px" ';
        }else{
          $style_str = ' style="min-width:80px" ';
        }
        echo '<td '.$td_bg_color.' class="td_'.$site_id.'_price" align="right" '.$style_str.' onmouseover="onmouseover_style(this,\''.$index.'\',\'td_'.$site_id.'\')"; onmouseout="onmouseout_style(this,\''.$index.'\',\'td_'.$site_id.'\')" >';
        echo $error_str;
        echo $temp_price_str;
        echo '</td>';
        echo '<td '.$td_bg_color.' class="td_'.$site_id.'_inventory" align="right" style="min-width:60px" onmouseover="onmouseover_style(this,\''.$index.'\',\'td_'.$site_id.'\')"; onmouseout="onmouseout_style(this,\''.$index.'\',\'td_'.$site_id.'\')" >';
        echo $temp_inventory_str;
        echo '</td>';
      }
    }else{
      echo '<td '.$td_bg_color.' class="td_'.$site_id.'_price" align="right" onmouseover="onmouseover_style(this,\''.$index.'\',false)"; onmouseout="onmouseout_style(this,\''.$index.'\',false)" >';
      if($zero_inventory == 1&&$temp_inventory==0){
        echo '&nbsp;';
      }else{
        echo $error_str;
        echo $temp_price_str;
      }
      echo '</td>';
    }
  }


  echo "</tr>";
}
echo '</table>';
echo '</div></td></tr>';
echo '</table>';

echo '<br/>';
echo '<br/>';
echo '<table style="min-width:750px;" width="100%" cellspacing="0" cellpadding="0" border="0">';
if($update_status==0){
$value_status = '自動更新中止';
}else{
$value_status = '自動更新開始';
}
echo '<tr><td><input type="button" name="button_update" value="更新"  onclick="update_data();this.disabled=true;"'.(time() - strtotime($date_array['collect_date']) < 10*60 ? ' disabled' : '').'>&nbsp;&nbsp;<input type="button" id="update_status" name="button_update" value="'.$value_status.'" onclick="update_data_status('.$update_status.');">';
echo '&nbsp;&nbsp;<input type="button" onclick="get_category_sort()" value="ゲームタイトル並び順を更新">';
echo '</td>';
echo '</tr></table>';
/*end*/
?>
<div id="wait" style="position:fixed; left:45%; top:45%; display:none;"><img src="images/load.gif" alt="img"></div>
</body>
</html>
