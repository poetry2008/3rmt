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


//判断产品名背景颜色

//判断 其他网站 价格 库存北京颜色
/**
打开页面自动通过api自动获取主站数据
  */
if(function_exists('curl_init')){
get_iimy_data();
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
             );
        }else{
             $mode_array =array('products_name'=>'<name>(.*?)の.*?<\/name>',
                   'price'=>'<price>([0-9,.]+)円<\/price>',
                   'inventory'=>'<quantity>(.*?)<\/quantity>',
                   'rate'=>'<rate>([0-9,.]+)<\/rate>',
                   'rate_other'=>'<rate_other>([^<]*)<\/rate_other>',
             );
        }
//匹配数据
       $search_array = array();
       foreach($mode_array as $key=>$value){
          preg_match_all('/'.$value.'/is',$contents,$temp_array);
          $search_array[$key] = $temp_array[1];
       }

//插入数据库
      foreach($search_array['products_name'] as $key=>$value){
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
//设置保存处理
if($_GET['action'] == 'save'){

  $inventory_show = $_POST['inventory_show'];
  $inventory_flag = $_POST['inventory_flag'];
  $site = $_POST['site'];
  $game_name = !isset($_GET['game']) ? 'FF11' : $_GET['game'];

  $site_id_array = array();
  $site_all_query = mysql_query("select site_id from site order by sort_order");
  while($site_all_array = mysql_fetch_array($site_all_query)){

    $site_id_array[] = $site_all_array['site_id'];
  }
  mysql_free_result($site_all_query);
  foreach($site_id_array as $site_value){

    $site_str_query = mysql_query("select is_show from site where site_id='".$site_value."'");
    $site_str_array = mysql_fetch_array($site_str_query);
    $site_setting_array = array();
    if($site_str_array['is_show'] != ''){
      $site_setting_array = unserialize($site_str_array['is_show']);
    }

    if(in_array($site_value,$site)){
      $site_setting_array[$game_name] = 1;
    }else{
      $site_setting_array[$game_name] = 0;
    }
    $site_setting_str = serialize($site_setting_array);
    mysql_free_result($site_str_query);
    mysql_query("update site set is_show='".$site_setting_str."' where site_id='".$site_value."'");
  }

  $quantity_array = array();
  $inventory_array = array();
  $config_value_query = mysql_query("select config_key,config_value from config where config_key='TEXT_IS_QUANTITY_SHOW' or config_key='TEXT_IS_INVENTORY_SHOW'");
  while($config_value_array = mysql_fetch_array($config_value_query)){

    if($config_value_array['config_key'] == 'TEXT_IS_QUANTITY_SHOW' && $config_value_array['config_value'] !='' ){

      $quantity_array = unserialize($config_value_array['config_value']);
    }
   
    if($config_value_array['config_key'] == 'TEXT_IS_INVENTORY_SHOW' && $config_value_array['config_value'] !='' ){

      $inventory_array = unserialize($config_value_array['config_value']);
    }
  }
  mysql_free_result($config_value_query);
  if($inventory_show == 1){
    $quantity_array[$game_name] = 1;
  }else{
    $quantity_array[$game_name] = 0;
  }
  $quantity_str = serialize($quantity_array);
  mysql_query("update config set config_value='".$quantity_str."' where config_key='TEXT_IS_QUANTITY_SHOW'");
  if($inventory_flag == 1){
    $inventory_array[$game_name] = 1;
  }else{
    $inventory_array[$game_name] = 0;
  }
  $inventory_str = serialize($inventory_array);
  mysql_query("update config set config_value='".$inventory_str."' where config_key='TEXT_IS_INVENTORY_SHOW'");
  
  header('Location: show.php'.(isset($_GET['flag']) ? '?flag='.$_GET['flag'].'&num='.time() : '').(isset($_GET['game']) ? (isset($_GET['flag']) ? '&game='.$_GET['game'] : '?game='.$_GET['game']) : ''));  
}
$tep_flag= (isset($_GET['flag']) ? '&flag='.$_GET['flag'].'' : '');
$product_type = $_GET['flag'] == 'sell' ? '買取' : '購入';
$game = !isset($_GET['game']) ? 'FF11' : $_GET['game'];
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
  <title>'.$game_str_array[$_GET['game']].'</title>
  </head>
  <body>';
echo '<br>';
$page_title = '<span onclick="get_category_info()" class="pageHeading">'.$game_str_array[$game].' RMT '.$product_type.'価格相場</span><br><br>';
echo $page_title;
echo '<select onchange="show_game_info(this.value)">';
foreach($game_str_array as $key => $value){
  echo '<option value="'.$key.'" '.($_GET['game']==$key ? 'selected="selected"' : '').'>'.$value.'</option>';
}
echo '</select>';
?>
<div id="category_info_box" style="min-width:550px;display:none; position:absolute; background:#FFFF00; width:70%;z-index:2; /*bottom:0;margin-top:40px;right:0; width:200px;*/">
<?php
echo '<form name="form1" method="post" action="show.php?action=save'.(isset($_GET['flag']) ? '&flag='.$_GET['flag'] : '').(isset($_GET['game']) ? '&game='.$_GET['game'] : '').'">';
?>
<?php
echo '</form>';
?>
</div>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    var info_left_widht = $('#info_left').width();
    var info_width = document.body.clientWidth-info_left_widht;
    $("#site_info").width(info_width);

    });
$(window).resize(function() {
    var info_left_widht = $('#info_left').width();
    var info_width = document.body.clientWidth-info_left_widht;
    $("#site_info").width(info_width);
    });
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
          $(this).css({'border-top':'2px solid #fc9700'});
          z++;
        }
      $(this).css({'border-left':'2px solid #fc9700','border-right':'2px solid #fc9700'});
      z_temp = this;
        });
      $(z_temp).css({'border-bottom':'2px solid #fc9700'});
  }else{
    var class_first = c_flag+'_price';
    var class_second = c_flag+'_inventory';
    var zf=0;
    var zs=0;
    var zf_temp;
    var zs_temp;
    $('.'+class_first).each(function(i){
        if(zf==0){
          $(this).css({'border-top':'2px solid #fc9700'});
          zf++;
        }
      $(this).css({'border-left':'2px solid #fc9700'});
      zf_temp = this;
        });
    $(zf_temp).css({'border-bottom':'2px solid #fc9700'});
    $('.'+class_second).each(function(i){
        if(zs==0){
          $(this).css({'border-top':'2px solid #fc9700'});
          zs++;
        }
      $(this).css({'border-right':'2px solid #fc9700'});
      zs_temp = this;
        });
    $(zs_temp).css({'border-bottom':'2px solid #fc9700'});

  }
  var temp;
  $("#tr_div_"+index).find("td").each(function(i){
    $(this).css({'border-bottom':'2px solid #fc9700','border-top':'2px solid #fc9700'});
    temp = this;
  });
  $(temp).css({'border-right':'2px solid #fc9700'});
  var x=0;
  $("#tr_start_"+index).find("td").each(function(i){
    if(x==0){
      $(this).css({'border-left':'2px solid #fc9700','border-bottom':'2px solid #fc9700','border-top':'2px solid #fc9700'});
      x++;
    }else{
      $(this).css({'border-bottom':'2px solid #fc9700','border-top':'2px solid #fc9700'});
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
          $(this).css({'border-top':'0px solid #fc9700'});
          z++;
        }
      $(this).css({'border-left':'0px solid #fc9700','border-right':'0px solid #fc9700'});
      z_temp = this;
        });
      $(z_temp).css({'border-bottom':'0px solid #fc9700'});
  }else{
    var class_first = c_flag+'_price';
    var class_second = c_flag+'_inventory';
    var zf=0;
    var zs=0;
    var zf_temp;
    var zs_temp;
    $('.'+class_first).each(function(i){
        if(zf==0){
          $(this).css({'border-top':'0px solid #fc9700'});
          zf++;
        }
      $(this).css({'border-left':'0px solid #fc9700'});
      zf_temp = this;
        });
    $(zf_temp).css({'border-bottom':'0px solid #fc9700'});
    $('.'+class_second).each(function(i){
        if(zs==0){
          $(this).css({'border-top':'0px solid #fc9700'});
          zs++;
        }
      $(this).css({'border-right':'0px solid #fc9700'});
      zs_temp = this;
        });
    $(zs_temp).css({'border-bottom':'0px solid #fc9700'});

  }
  var temp;
  $("#tr_div_"+index).find("td").each(function(i){
    $(this).css({'border-bottom':'0px solid #fc9700','border-top':'0px solid #fc9700'});
    temp = this;
  });
  $(temp).css({'border-right':'0px solid #fc9700'});
  var x = 0;
  $("#tr_start_"+index).find("td").each(function(i){
    if(x==0){
      $(this).css({'border-left':'0px solid #fc9700','border-bottom':'0px solid #fc9700','border-top':'0px solid #fc9700'});
      x++;
    }else{
      $(this).css({'border-bottom':'0px solid #fc9700','border-top':'0px solid #fc9700'});
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
/*
 * FF14 游戏各网站数据显示
 */

$url_array = array('FF14'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/ff14.html',
                                2=>'http://www.matubusi.com/system/pc/cart/ff14-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/ff14/buy/', 
                                4=>'http://www.rmt-wm.com/buy/ff14.html',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/ff14/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/ff14-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/ff14/sell/', 
                                 4=>'http://www.rmt-wm.com/sale/ff14.html', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                ),
                             ),
                  'RO'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/ro.html',
                                2=>'http://www.matubusi.com/system/pc/cart/ro-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/ro/buy/', 
                                4=>'http://www.rmt-wm.com/buy/0004.html',
                                5=>'http://rmtrank.com/pico2+index.htm',
                                6=>'http://rmt.kakaran.jp/ragnarok/',
                                8=>'http://pastel-rmt.jp/cart/cart.php?ACTION=Shop%3A%3AShopForm&ID=28&Mode=Sale&',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/ro/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/ragnarok/sale.html',
                                11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=48&Mode=Sale&',
                                12=>'http://www.rmtsonic.jp/games/ro.html',
                                13=>'http://rmt.kakaran.jp/ragnarok/',
                                14=>'http://rmt.kakaran.jp/ragnarok/',
                                15=>'http://rmt.kakaran.jp/ragnarok/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/ro-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/ro/sell/', 
                                 4=>'http://www.rmt-wm.com/sale/0004.html', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/ragnarok/',
                                 8=>'http://pastel-rmt.jp/cart/cart.php?ACTION=Shop%3A%3AShopForm&ID=28&Mode=Buy&',
                                 9=>'http://rmt.diamond-gil.jp/game2.php/gameid/ro/view/sv/',
                                 10=>'http://www.asahi-rmt-service.com/items/index2.cgi?gname=ragnarok_purchase&call=new',
                                 11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=48&Mode=Buy&',
                                 12=>'http://www.rmtsonic.jp/',
                                ),
                              ), 
                  'RS'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/RedStone.html',
                                2=>'http://www.matubusi.com/system/pc/cart/redstone-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/redstone/buy/', 
                                4=>'http://www.rmt-wm.com/buy/redstone.html',
                                5=>'http://rmtrank.com/pico7+index.htm',
                                6=>'http://rmt.kakaran.jp/redstone/',
                                8=>'http://pastel-rmt.jp/cart/cart.php?ACTION=Shop%3A%3AShopForm&ID=13&Mode=Buy&',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/redstone/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/redstone/sale.html',
                                11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=9&Mode=Sale&',
                                12=>'http://www.rmtsonic.jp/games/redstone.html',
                                13=>'http://rmt.kakaran.jp/redstone/',
                                14=>'http://rmt.kakaran.jp/redstone/'
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/redstone-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/redstone/sell/', 
                                 4=>'http://www.rmt-wm.com/sale/redstone.html', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/redstone/',
                                 8=>'http://pastel-rmt.jp/cart/cart.php?ACTION=Shop%3A%3AShopForm&ID=13&Mode=Sale',
                                 9=>'http://rmt.diamond-gil.jp/game2.php/gameid/redstone/view/sv/',
                                 10=>'http://www.asahi-rmt-service.com/redstone/purchase.html',
                                 11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=9&Mode=Buy&',
                                 12=>'http://www.rmtsonic.jp/'
                                ),
                              ),
                   'FF11'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/ff11.html',
                                2=>'http://www.matubusi.com/system/pc/cart/ff11-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/ff11/buy/', 
                                4=>'http://www.rmt-wm.com/buy/0005.html',
                                5=>'http://rmtrank.com/pico3+index.htm',
                                6=>'http://rmt.kakaran.jp/ff11',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/ff11/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/ff/sale_yoyaku.html',
                                11=>'http://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=8&Mode=Sale&',
                                12=>'http://www.rmtsonic.jp/games/ff11.html',
                                13=>'http://rmt.kakaran.jp/ff11',
                                14=>'http://rmt.kakaran.jp/ff11',
                                15=>'http://rmt.kakaran.jp/ff11'
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/ff11-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/ff11/sell/', 
                                 4=>'http://www.rmt-wm.com/sale/0005.html', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/ff11',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/game2.php/gameid/ff11/view/sv/',
                                 10=>'http://www.asahi-rmt-service.com/ff/purchase.html',
                                 11=>'http://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=8&Mode=Buy&',
                                 12=>'http://www.rmtsonic.jp/',
                                ),
                              ),
                   'DQ10'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/dqx.html',
                                2=>'http://www.matubusi.com/system/pc/cart/dq10-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/dq10/buy/', 
                                4=>'http://www.rmt-wm.com/buy/dragonquest.html',
                                5=>'http://rmtrank.com/dq10+index.htm',
                                6=>'http://rmt.kakaran.jp/dqx',
                                8=>'http://pastel-rmt.jp/cart/cart.php?ACTION=Shop%3A%3AShopForm&ID=38&Mode=Sale&',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/doragonkuesuto/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/dqx/sale.html',
                                11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=161&Mode=Sale&',
                                12=>'http://www.rmtsonic.jp/games/wii.html',
                                13=>'http://rmt.kakaran.jp/dqx',
                                14=>'http://rmt.kakaran.jp/dqx',
                                15=>'http://rmt.kakaran.jp/dqx',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/dq10-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/dq10/sell/', 
                                 4=>'http://www.rmt-wm.com/sale/dragonquest.html', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/dqx',
                                 8=>'http://pastel-rmt.jp/cart/cart.php?ACTION=Shop%3A%3AShopForm&ID=38&Mode=Buy&',
                                 9=>'http://rmt.diamond-gil.jp/game2.php/gameid/doragonkuesuto/view/sv/',
                                 10=>'http://www.asahi-rmt-service.com/dqx/purchase.html',
                                 11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=161&Mode=Buy&',
                                 12=>'http://www.rmtsonic.jp/',
                                ),
                              ),
                  'L2'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/Lineage2.html',
                                2=>'http://www.matubusi.com/system/pc/cart/lineage2-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/lineage2/buy/', 
                                4=>'http://www.rmt-wm.com/buy/0003.html',
                                5=>'http://rmtrank.com/pico5+index.htm',
                                6=>'http://rmt.kakaran.jp/lineage2/',
                                8=>'http://pastel-rmt.jp/cart/cart.php?ACTION=Shop%3A%3AShopForm&ID=10&Mode=Sale&',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/lineage2/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/lineage/sale.html',
                                11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=4&Mode=Sale&',
                                12=>'http://www.rmtsonic.jp/games/lineage2.html',
                                13=>'http://rmt.kakaran.jp/lineage2/',
                                14=>'http://rmt.kakaran.jp/lineage2/',
                                15=>'http://rmt.kakaran.jp/lineage2/',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/lineage2-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/lineage2/sell/', 
                                 4=>'http://www.rmt-wm.com/sale/0003.html', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/lineage2',
                                 8=>'http://pastel-rmt.jp/cart/cart.php?ACTION=Shop%3A%3AShopForm&ID=10&Mode=Buy&',
                                 9=>'http://rmt.diamond-gil.jp/game2.php/gameid/lineage2/view/sv/',
                                 10=>'http://www.asahi-rmt-service.com/lineage/purchase.html',
                                 11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=4&Mode=Buy&',
                                 12=>'http://www.rmtsonic.jp/',
                                ),
                              ),
                 'ARAD'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/arad.html',
                                2=>'http://www.matubusi.com/system/pc/cart/arad-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/arad/buy/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/pico+index.htm',
                                6=>'http://rmt.kakaran.jp/arad/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/arad-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/arad/sell/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                ),
                              ),
               'nobunaga'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/nobunaga.html',
                                2=>'http://www.matubusi.com/system/pc/cart/nobunaga-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/nobunaga/buy/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/nobunaga/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/nobunaga/view/sv/',
                                13=>'http://rmt.kakaran.jp/nobunaga/',
                                14=>'http://rmt.kakaran.jp/nobunaga/',
                                15=>'http://rmt.kakaran.jp/nobunaga/',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/nobunaga-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/nobunaga/sell/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                ),
                              ),
            'PSO2'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/PSO2.html',
                                2=>'http://www.matubusi.com/system/pc/cart/pso2-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/pso2+index.htm',
                                6=>'http://rmt.kakaran.jp/pso2/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/pso2-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/nobunaga/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                ),
                              ),
            'L1'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/Lineage1.html',
                                2=>'http://www.matubusi.com/system/pc/cart/lineage-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/pico4+index.htm',
                                6=>'http://rmt.kakaran.jp/lineage/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=3&Mode=Sale&',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/lineage-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/nobunaga/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/lineage/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=3&Mode=Buy&',
                                ),
                             ),

            'TERA'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/tera.html',
                                2=>'http://www.matubusi.com/system/pc/cart/tera-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/tera/buy/', 
                                4=>'http://www.rmt-wm.com/buy/tera.html',
                                5=>'http://rmtrank.com/tera+index.htm',
                                6=>'http://rmt.kakaran.jp/tera/',
                                8=>'http://pastel-rmt.jp/cart/cart.php?ACTION=Shop%3A%3AShopForm&ID=36&Mode=Sale&',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/tera/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/tera/sale.html',
                                11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=95&Mode=Sale&',
                                12=>'http://www.rmtsonic.jp/games/TERA.html',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/tera-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/tera/sell/', 
                                 4=>'http://www.rmt-wm.com/sale/tera.html', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/tera/',
                                 8=>'http://pastel-rmt.jp/cart/cart.php?ACTION=Shop%3A%3AShopForm&ID=36&Mode=Buy&',
                                 9=>'http://rmt.diamond-gil.jp/game2.php/gameid/tera/view/sv/',
                                 10=>'http://www.asahi-rmt-service.com/tera/purchase.html',
                                 11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=95&Mode=Buy&',
                                 12=>'http://www.rmtsonic.jp/',
                                ),
                             ),
            'AION'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/aion.html',
                                2=>'http://www.matubusi.com/system/pc/cart/aion-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/aion/buy/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/aion+index.htm',
                                6=>'http://rmt.kakaran.jp/aion/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/aion-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/aion/sell/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                ),
                             ),
            'CABAL'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/cabal.html',
                                2=>'http://www.matubusi.com/system/pc/cart/cabal-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/cabal/buy/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/aion+index.htm',
                                6=>'http://rmt.kakaran.jp/cabal/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/cabal-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/cabal/sell/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                ),
                             ),
            'WZ'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/Wizardry.html',
                                2=>'http://www.matubusi.com/system/pc/cart/wizardry-rmt-hanbai/hanbai/items', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/aion+index.htm',
                                6=>'http://rmt.kakaran.jp/wizardry/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/wizardry-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/cabal/sell/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/wizardry/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                ),
                             ),
            'latale'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/latale.html',
                                2=>'http://www.matubusi.com/system/pc/cart/latale-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/pico28+index.htm',
                                6=>'http://rmt.kakaran.jp/latale/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/latale/sale.html',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/latale-rmt-kaitori/kaitori/items',
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/latale/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/latale/purchase.html',
                                ),
                             ),
            'blade'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/BNS.html',
                                2=>'http://www.matubusi.com/system/pc/cart/bns-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/blade/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/BladeSoul/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/bns-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/blade/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/game2.php/gameid/BladeSoul/view/sv/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
            'megaten'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/imagine.html',
                                2=>'http://www.matubusi.com/system/pc/cart/megaten-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/megaten/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                 'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/megaten-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/megaten/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
            'EWD'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/elsword.html',
                                2=>'http://www.matubusi.com/system/pc/cart/elsword-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/elsword+index.htm',
                                6=>'http://rmt.kakaran.jp/elsword/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/elsword-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/elsword/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
            'LH'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/lucentheart.html',
                                2=>'http://www.matubusi.com/system/pc/cart/lucentheart-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/lh/buy/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/lucentheart/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/lucentheart-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/lh/sell/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/lucentheart/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
            'HR'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/mabinogiheroes.html',
                                2=>'http://www.matubusi.com/system/pc/cart/mabinogiheroes-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/heroes/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/mabinogiheroes-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
            'AA'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/archeage.html',
                                2=>'http://www.matubusi.com/system/pc/cart/archeage-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/archeage/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/archeage-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/archeage/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
            'ThreeSeven'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
                                2=>'http://www.matubusi.com/system/pc/cart/777-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/777town+index.htm',
                                6=>'http://rmt.kakaran.jp/777town/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                13=>'http://rmt.kakaran.jp/777town/',
                                14=>'http://rmt.kakaran.jp/777town/',
                                15=>'http://rmt.kakaran.jp/777town/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/777-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/archeage/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
            'ECO'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/eco.html',
                                2=>'http://www.matubusi.com/system/pc/cart/eco-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/eco/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/eco/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/eco-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/eco/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/game2.php/gameid/eco/view/sv/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'FNO'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/fno.html',
                                2=>'http://www.matubusi.com/system/pc/cart/fno-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/fno+index.htm',
                                6=>'http://rmt.kakaran.jp/fno/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/fno-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'SUN'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/sun.html',
                                2=>'http://www.matubusi.com/system/pc/cart/sunonline-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/pico10+index.htm',
                                6=>'http://rmt.kakaran.jp/sun/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/sunonline-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'talesweave'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/talesweaver.html',
                                2=>'http://www.matubusi.com/', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/pico12+index.htm',
                                6=>'http://rmt.kakaran.jp/talesweaver/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'MU'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/mu.html',
                                2=>'http://www.matubusi.com/system/pc/cart/mu-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/pico16+index.htm',
                                6=>'http://rmt.kakaran.jp/mu/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                13=>'http://rmt.kakaran.jp/mu/',
                                14=>'http://rmt.kakaran.jp/mu/',
                                15=>'http://rmt.kakaran.jp/mu/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/mu-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'C9'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/c9.html',
                                2=>'http://www.matubusi.com/system/pc/cart/c9-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/c9/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                13=>'http://rmt.kakaran.jp/c9/',
                                14=>'http://rmt.kakaran.jp/c9/',
                                15=>'http://rmt.kakaran.jp/c9/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/c9-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'MS'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/MapleStory.html',
                                2=>'http://www.matubusi.com/', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/buy/54862123356.html',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/maplestory/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/maplestory/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/sale/54862123356.html', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/maplestory/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/game2.php/gameid/maplestory/view/sv/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'cronous'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/cronous.html',
                                2=>'http://www.matubusi.com/system/pc/cart/cronous-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/pico17+index.htm',
                                6=>'http://rmt.kakaran.jp/cronous/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                11=>'https://www.rmt-king.com/',
                                12=>'http://www.rmtsonic.jp/',
                                13=>'http://rmt.kakaran.jp/cronous/',
                                14=>'http://rmt.kakaran.jp/cronous/',
                                15=>'http://rmt.kakaran.jp/cronous/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/cronous-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/cronous/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'tenjouhi'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
                                2=>'http://www.matubusi.com/', 
                                3=>'http://ftb-rmt.jp/tenjouhi/buy/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/tenjouhi/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/', 
                                 3=>'http://ftb-rmt.jp/tenjouhi/sell/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'rose'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/rose.html',
                                2=>'http://www.matubusi.com/system/pc/cart/rose-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/pico27+index.htm',
                                6=>'http://rmt.kakaran.jp/rose/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/rose-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'hzr'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/haresora.html',
                                2=>'http://www.matubusi.com/', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/harezora+index.htm',
                                6=>'http://rmt.kakaran.jp/milkyrush/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'dekaron'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/dekaron.html',
                                2=>'http://www.matubusi.com/system/pc/cart/dekaron-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/dekaron+index.htm',
                                6=>'http://rmt.kakaran.jp/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/dekaron-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'fez'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/fez.html',
                                2=>'http://www.matubusi.com/', 
                                3=>'http://ftb-rmt.jp/fez/buy/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/fezero+index.htm',
                                6=>'http://rmt.kakaran.jp/fez/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                13=>'http://rmt.kakaran.jp/fez/',
                                14=>'http://rmt.kakaran.jp/fez/',
                                15=>'http://rmt.kakaran.jp/fez/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/', 
                                 3=>'http://ftb-rmt.jp/fez/sell/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'lakatonia'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/lakatoniarmt.html',
                                2=>'http://www.matubusi.com/', 
                                3=>'http://ftb-rmt.jp/lakatonia/buy/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/lakatonia/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                13=>'http://rmt.kakaran.jp/lakatonia/',
                                14=>'http://rmt.kakaran.jp/lakatonia/',
                                15=>'http://rmt.kakaran.jp/lakatonia/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/', 
                                 3=>'http://ftb-rmt.jp/lakatonia/sell/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'moe'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/moe.html',
                                2=>'http://www.matubusi.com/', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/moe/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/moe/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/',
                                13=>'http://rmt.kakaran.jp/moe/',
                                14=>'http://rmt.kakaran.jp/moe/',
                                15=>'http://rmt.kakaran.jp/moe/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/game2.php/gameid/moe/view/sv/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'mabinogi'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/mabinogi.html',
                                2=>'http://www.matubusi.com/system/pc/cart/mabinogi-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/mabinogi/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                13=>'http://rmt.kakaran.jp/mabinogi/',
                                14=>'http://rmt.kakaran.jp/mabinogi/',
                                15=>'http://rmt.kakaran.jp/mabinogi/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/mabinogi-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'WF'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
                                2=>'http://www.matubusi.com/', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/elter/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=47&Mode=Sale&',
                                13=>'http://rmt.kakaran.jp/elter/',
                                14=>'http://rmt.kakaran.jp/elter/',
                                15=>'http://rmt.kakaran.jp/elter/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/elter/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                 11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=47&Mode=Buy&',
                                ),
                             ),
                             'rohan'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
                                2=>'http://www.matubusi.com/', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/rohan+index.htm',
                                6=>'http://rmt.kakaran.jp/rohan/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                13=>'http://rmt.kakaran.jp/rohan/',
                                14=>'http://rmt.kakaran.jp/rohan/',
                                15=>'http://rmt.kakaran.jp/rohan/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                ),
                             ),
                             'genshin'=>array('buy'=>array(1=>'http://www.mugenrmt.com/rmt/fantasyfrontier.html',
                                2=>'http://www.matubusi.com/system/pc/cart/genshin-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/genshin/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
                                10=>'http://www.asahi-rmt-service.com/',
                                11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=198&Mode=Sale&',
                                12=>'http://rmt.kakaran.jp/genshin/',
                                13=>'http://rmt.kakaran.jp/genshin/',
                                14=>'http://rmt.kakaran.jp/genshin/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/genshin-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/genshin/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                 11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=198&Mode=Buy&',
                                ),
                             ),
                             'tartaros'=>array('buy'=>array(
                                1=>'http://www.mugenrmt.com/rmt/tartaros.html',
                                2=>'http://www.matubusi.com/system/pc/cart/tartarosrebirth-rmt-hanbai/hanbai/items', 
                                3=>'http://ftb-rmt.jp/tartaros/buy/', 
                                4=>'http://www.rmt-wm.com/buy/xk.html',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/',
                                8=>'http://pastel-rmt.jp/cart/cart.php?ACTION=Shop%3A%3AShopForm&ID=23&Mode=Sale&',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/Tartraos/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/',
                                11=>'https://www.rmt-king.com/',
                                12=>'http://www.rmtsonic.jp/games/Tartaros.html',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/tartarosrebirth-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                 11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=3&Mode=Buy&',
                                ),
                             ),
                             'atlantica'=>array('buy'=>array(
                                1=>'http://www.mugenrmt.com/rmt/atlantica.html',
                                2=>'http://www.matubusi.com/', 
                                3=>'http://ftb-rmt.jp/atlantica/buy/', 
                                4=>'http://www.rmt-wm.com/buy/atlantica.html',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/',
                                8=>'http://pastel-rmt.jp/cart/cart.php?ACTION=Shop%3A%3AShopForm&ID=22&Mode=Sale&',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/atlantica/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/atlantica/sale.html',
                                11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=31&Mode=Sale&',
                                12=>'http://www.rmtsonic.jp/games/atlantica.html',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/system/pc/cart/tartarosrebirth-rmt-kaitori/kaitori/items', 
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/',
                                 6=>'http://rmt.kakaran.jp/',
                                 8=>'http://pastel-rmt.jp/',
                                 9=>'http://rmt.diamond-gil.jp/',
                                 10=>'http://www.asahi-rmt-service.com/',
                                 11=>'https://www.rmt-king.com/rmtcart/cart.php?ACTION=Shop%3A%3AShopForm&ID=3&Mode=Buy&',
                                ),
                             ),


);
//获取主站的URL
$main_site_query = mysql_query("select site_id from site where site_name='ジャックポット'");
$main_site_array = mysql_fetch_array($main_site_query);
$main_category_query = mysql_query("select * from category where site_id='".$main_site_array['site_id']."'");
while($main_category_array = mysql_fetch_array($main_category_query)){

  $category_type = $main_category_array['category_type'] == 1 ? 'buy' : 'sell';
  if($main_category_array['category_name'] == 'AION'){
    $url_array[$main_category_array['category_name']][$category_type][$main_site_array['site_id']] = 'http://www.iimy.co.jp/rmt/c-396.html';
  }else{
    $url_array[$main_category_array['category_name']][$category_type][$main_site_array['site_id']] = $main_category_array['category_url'];
  }
}

//kakaran网站url
$kk_site_query = mysql_query("select site_id from site where site_name like '%カカ%'");
while($kk_row = mysql_fetch_array($kk_site_query)){
  $kk_res[]=$kk_row['site_id'];
}
foreach($kk_res as $kk_site){
   $kk_category_query = mysql_query("select * from category where site_id='".$kk_site."'");
    while($kk_category_array = mysql_fetch_array($kk_category_query)){
       $category_type = $kk_category_array['category_type'] == 1 ? 'buy' : 'sell';
       $url_array[$kk_category_array['category_name']][$category_type][$kk_site] = $kk_category_array['category_url'];
    }
}
//rank网站url
$rank_site_query = mysql_query("select site_id from site where site_name like '%ランキング%' or site_name like '%エルモ%' or site_name like '%WM%'");
while($rank_row = mysql_fetch_array($rank_site_query)){
  $rank_res[]=$rank_row['site_id'];
}
foreach($rank_res as $rank_site){
   $rank_category_query = mysql_query("select * from category where site_id='".$rank_site."'");
    while($rank_category_array = mysql_fetch_array($rank_category_query)){
       $category_type = $rank_category_array['category_type'] == 1 ? 'buy' : 'sell';
       $url_array[$rank_category_array['category_name']][$category_type][$rank_site] = $rank_category_array['category_url'];
    }
}


echo '<table width="100%"><tr height="30px"><td'.(!isset($_GET['flag']) || $_GET['flag'] == 'buy' ? ' style="background-color:#666666;"' : '').'><a href="show.php?flag=buy'.(isset($_GET['game']) ? '&game='.$_GET['game'] : '').'&num='.time().'">販売</a></td><td'.($_GET['flag'] == 'sell' ? ' style="background-color:#666666;"' : '').'><a href="show.php?flag=sell'.(isset($_GET['game']) ? '&game='.$_GET['game'] : '').'&num='.time().'">買取</a></td>';
$game = isset($_GET['game']) ? $_GET['game'] : 'FF11';
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
		   'MU'=>'1個あたり　祝福の宝石  10個',
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
		   'lineage'=>'1個あたり  100万（1,000,000）アデナ(Adena)'
);
echo '<td align="right">最終更新&nbsp;&nbsp;'.date('Y/m/d H:i',strtotime($date_array['collect_date'])).'&nbsp;&nbsp;&nbsp;'.$game_info[$game].'</td></tr></table>';
//获得所有网站的 标题 
//数据输出的三个数组
$left_info = array();
$right_info = array();
$left_title = array();
$right_title = array();
//获得释放显示库存 
$show_inventory = 1;
//获得是否显示库存0的数据
$zero_inventory = 0;
$flag = $_GET['flag'] == 'sell' ? 'sell' : 'buy';
$flag_type = $_GET['flag'] == 'sell'?0:1;
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
  if(preg_match('/www\.iimy\.co\.jp/',$all_site_row['site_url'])){
    $host_site_id = $all_site_row['site_id'];
    $left_title[] = '<td '.$colspan.' style="min-width:75px;" width="8%" class="dataTableHeadingContent_order"><a href="'.
      $url_array[$game][$flag][$site_array['site_id']].'" target="_blank">'.$all_site_row['site_name'].
      '</a></td>';
  }else{
    $site_other_arr[$all_site_row['site_id']] = $all_site_row['site_id'];
    $right_title[$all_site_row['site_id']] = '<td '.$colspan.' style="min-width:75px;" width="8%" class="dataTableHeadingContent_order"><a href="'.
      $url_array[$game][$flag][$site_array['site_id']].'" target="_blank">'.$all_site_row['site_name'].
      '</a></td>';
  }
}
$data_info_sql = "select * from product p,category c 
  where c.category_id = p.category_id
  and c.category_type='".$flag_type."'
  and c.category_name='".$game."' 
  and c.site_id = '".$host_site_id."'
  order by p.product_id asc";
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
foreach($name_arr as $index => $name){
  $check_pid_row = $check_pid[array_search($name,$check_name)];
  echo '<tr height="30px" id="tr_start_'.$index.'">';
  echo '<td class="td_name" align="left" nowrap="nowrap" onmouseover="onmouseover_style(this,\''.$index.'\',false)"; onmouseout="onmouseout_style(this,\''.$index.'\',false)" >';
  echo $name;
  echo '</td>';
  //最高最低值
  $price_arr = array_unique($price_info_arr[$index]);
  if($flag = 'buy'){
    sort($price_arr);
  }else{
    rsort($price_arr);
  }
  echo '<td class="td_first" align="center" onmouseover="onmouseover_style(this,\''.$index.'\',false)"; onmouseout="onmouseout_style(this,\''.$index.'\',false)" >';
  if(count($price_arr)>0){
    echo "<input type='radio' ".(($check_pid_row == -1 )?'checked':'')." name='radio_".$index."' id='first_value' onclick='update_products_price(\"".$game."\",\"".$name."\",\"".$flag."\",-1)'>";
    echo '<label for="first_value">'.price_number_format($price_arr[0]).'円</label>';
  }else{
    echo '<label for="first_value">-円</label>';
  }
  echo '</td>';
  //最高最低第二个值
  echo '<td class="td_second" align="center" onmouseover="onmouseover_style(this,\''.$index.'\',false)"; onmouseout="onmouseout_style(this,\''.$index.'\',false)" >';
  if(count($price_arr)>1){
    echo "<input type='radio' ".(($check_pid_row == 0 )?'checked':'')." name='radio_".$index."' id='second_value' onclick='update_products_price(\"".$game."\",\"".$name."\",\"".$flag."\",0)'>";
    echo '<label for="second_value">'.price_number_format($price_arr[1]).'円</label>';
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
echo '<td width="77%" valign="top" style="border-left:2px solid #808080;">';
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
  echo '<tr height="30px" id="tr_div_'.$index.'">';
  //其他网站信息
  foreach($show_site_arr as $site_id){
    $index_other = array_search($name,$all_site_name_arr[$site_id]);
    $error_str = '';
    $radio_str = '';
    if($index_other === false){
      $temp_price_str = '-円';
      $temp_inventory_str = '-個';
      $temp_pid = '';
    }else{
      $temp_price = price_number_format($all_site_info_arr[$site_id][$index_other]['product_price']); 
      $temp_inventory = show_effective_number($all_site_info_arr[$site_id][$index_other]['product_inventory']);;
      $temp_pid = $all_site_info_arr[$site_id][$index_other]['product_id'];
      if($all_site_info_arr[$site_id][$index_other]['is_error']==1){
        $error_str = '<span id="enable_img" ><img width="10" height="10" src="images/icon_alarm_log.gif"></span>';
      }
      $temp_inventory_str = '';
      $temp_price_str = "<font style='font-weight: bold;'><input ".(($check_pid_row == $temp_pid )?'checked':'')." name='radio_".$index."' type='radio' id='first_value' onclick='update_products_price(\"".$game."\",\"".$name."\",\"".$flag."\",\"".$temp_pid."\")'>";
      $temp_price_str .= "<label for='".$temp_pid."'>".$temp_price."円</label></font>";
      $temp_inventory_str .= $temp_inventory.'個';
    }
    if($show_inventory == 1){
      if($zero_inventory == 1&&$temp_inventory==0){
        echo '<td class="td_'.$site_id.'_price" onmouseover="onmouseover_style(this,\''.$index.'\',\'td_'.$site_id.'\')"; onmouseout="onmouseout_style(this,\''.$index.'\',\'td_'.$site_id.'\')" >&nbsp;</td><td class="td_'.$site_id.'_inventory" onmouseover="onmouseover_style(this,\''.$index.'\',\'td_'.$site_id.'\')"; onmouseout="onmouseout_style(this,\''.$index.'\',\'td_'.$site_id.'\')" >&nbsp;</td>';
      }else{
        echo '<td class="td_'.$site_id.'_price" align="right" style="min-width:60px" onmouseover="onmouseover_style(this,\''.$index.'\',\'td_'.$site_id.'\')"; onmouseout="onmouseout_style(this,\''.$index.'\',\'td_'.$site_id.'\')" >';
        echo $error_str;
        echo $temp_price_str;
        echo '</td>';
        echo '<td class="td_'.$site_id.'_inventory" align="right" style="min-width:60px" onmouseover="onmouseover_style(this,\''.$index.'\',\'td_'.$site_id.'\')"; onmouseout="onmouseout_style(this,\''.$index.'\',\'td_'.$site_id.'\')" >';
        echo $temp_inventory_str;
        echo '</td>';
      }
    }else{
      echo '<td class="td_'.$site_id.'_price" align="right" onmouseover="onmouseover_style(this,\''.$index.'\')"; onmouseout="onmouseout_style(this,\''.$index.'\')" >';
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
echo '<form name="form1" method="post" action="show.php?action=save'.(isset($_GET['flag']) ? '&flag='.$_GET['flag'] : '').(isset($_GET['game']) ? '&game='.$_GET['game'] : '').'">';
echo '<table style="min-width:750px" width="100%" cellspacing="0" cellpadding="0" border="0">';
echo '<tr><td width="12%">表示業者設定</td>';
$site_query = mysql_query("select * from site order by sort_order");
$all_site_array = array();
$index = 0;
while($site_array_row = mysql_fetch_array($site_query)){
  $index++;
  if($site_array_row['site_name']=='ジャックポット'){
    $all_site_array[0] = $site_array_row;
  }else{
    $all_site_array[$index] = $site_array_row;
  }

}
ksort($all_site_array);
foreach($all_site_array as $site_array){
  $site_temp = unserialize($site_array['is_show']);
  echo '<td><input type="checkbox" name="site[]" value="'.$site_array['site_id'].'"'.(in_array($site_array['site_id'],$_POST['site']) ? ' checked="checked"' : $site_temp[$game] !== 0 ? ' checked="checked"' : '').' id="site_'.$site_array['site_id'].'"><label for="site_'.$site_array['site_id'].'">'.$site_array['site_name'].'</label></td>';
}
echo '<td><input type="button" name="button1" value="全てチェック・解除" onclick="check_all();">&nbsp;&nbsp;<input type="hidden" name="num1" id="num" value="1"></td></tr></table>';
echo '<table style="min-width:750px;" width="100%" cellspacing="0" cellpadding="0" border="0">';
echo '<tr><td width="12%">オプション</td>';
echo '<td width="8%"><input type="checkbox" name="inventory_show" value="1"'.($_POST['inventory_show'] == 1 ? ' checked="checked"' : $inventory_show_array[$game] !== 0 ? ' checked="checked"' : '').' id="inventory_show_flag"><label for="inventory_show_flag">数量表示</label></td>';
echo '<td><input type="checkbox" name="inventory_flag" value="1"'.($_POST['inventory_flag'] == 1 ? ' checked="checked"' : $inventory_flag_array[$game] !== 0 ? ' checked="checked"' : '').' id="inventory_flag_id"><label for="inventory_flag_id">在庫ゼロ非表示</label></td></tr>';
if($update_status==0){
$value_status = '自動更新中止';
}else{
$value_status = '自動更新開始';
}
echo '<tr><td colspan="3"><input type="submit" name="submit1" value="設定を保存">&nbsp;&nbsp;<input type="button" name="button_update" value="更新"  onclick="update_data();this.disabled=true;"'.(time() - strtotime($date_array['collect_date']) < 10*60 ? ' disabled' : '').'>&nbsp;&nbsp;<input type="button" id="update_status" name="button_update" value="'.$value_status.'" onclick="update_data_status('.$update_status.');">';
echo '&nbsp;&nbsp;<input type="button" onclick="get_category_sort()" value="ゲームタイトル並び順を更新">';
echo '</td>';
echo '</tr></table>';
echo '</form>';
/*end*/
?>
<div id="wait" style="position:fixed; left:45%; top:45%; display:none;"><img src="images/load.gif" alt="img"></div>
</body>
</html>
