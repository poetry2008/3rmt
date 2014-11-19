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
    foreach($result as $res ){
      if($res->categories_id == $value ){
        $sort = $res->sort_order;
        break;
      }
    }
    $temp_info_array['keyword'] = $key;
    $temp_info_array['name'] = $game_str_array[$key];
    $temp_info_array['sort'] = $sort;
    $insert_category_sort_array[] = $temp_info_array;
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
  $game_name = !isset($_GET['game']) ? 'DQ10' : $_GET['game'];

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
echo '<form name="form1" method="post" action="show.php?action=save'.(isset($_GET['flag']) ? '&flag='.$_GET['flag'] : '').(isset($_GET['game']) ? '&game='.$_GET['game'] : '').'">';
echo '<br><span class="pageHeading">'.$game_str_array[$game].' RMT '.$product_type.'価格相場</span><br><br>';
echo '<select onchange="show_game_info(this.value)">';
foreach($game_str_array as $key => $value){
  echo '<option value="'.$key.'" '.($_GET['game']==$key ? 'selected="selected"' : '').'>'.$value.'</option>';
}
echo '</select>';
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
$config_query = mysql_query("select * from config where config_key='TEXT_IS_QUANTITY_SHOW' or config_key='TEXT_IS_INVENTORY_SHOW'");
while($config_array = mysql_fetch_array($config_query)){
  if($config_array['config_value'] != ''){
    if($config_array['config_key'] == 'TEXT_IS_QUANTITY_SHOW'){
      $inventory_show_array = unserialize($config_array['config_value']);
    }else{
      $inventory_flag_array = unserialize($config_array['config_value']);
    }
  }
}
echo '<td width="8%"><input type="checkbox" name="inventory_show" value="1"'.($_POST['inventory_show'] == 1 ? ' checked="checked"' : $inventory_show_array[$game] !== 0 ? ' checked="checked"' : '').' id="inventory_show_flag"><label for="inventory_show_flag">数量表示</label></td>';
echo '<td><input type="checkbox" name="inventory_flag" value="1"'.($_POST['inventory_flag'] == 1 ? ' checked="checked"' : $inventory_flag_array[$game] !== 0 ? ' checked="checked"' : '').' id="inventory_flag_id"><label for="inventory_flag_id">在庫ゼロ非表示</label></td></tr>';
if($update_status==0){
$value_status = '自動更新中止';
}else{
$value_status = '自動更新開始';
}
$date_query = mysql_query("select max(collect_date) as collect_date from category where category_name='".$game."'");
$date_array = mysql_fetch_array($date_query);
echo '<tr><td colspan="3"><input type="submit" name="submit1" value="設定を保存">&nbsp;&nbsp;<input type="button" name="button_update" value="更新"  onclick="update_data();this.disabled=true;"'.(time() - strtotime($date_array['collect_date']) < 10*0 ? ' disabled' : '').'>&nbsp;&nbsp;<input type="button" id="update_status" name="button_update" value="'.$value_status.'" onclick="update_data_status('.$update_status.');">';
echo '&nbsp;&nbsp;<input type="button" onclick="get_category_sort()" value="ゲームタイトル並び順を更新">';
echo '</td>';
echo '</tr></table>';
echo '</form>';
?>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">
function get_category_sort(){
  $.ajax({
    type: "POST",
    data:"",
    beforeSend: function(){$('body').css('cursor','wait');$("#wait").show();},
    async:false,
    url: 'show.php?action=get_parent_category',
    success: function(msg) {
      setTimeout('read_time()',8000);
      location.href = location.href
    }
  });
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
  $.ajax({
    type: "POST",
    data: 'game=<?php echo isset($_GET['game']) ? $_GET['game'] : 'FF11';?>&flag=<?php echo 'has';?>',
    beforeSend: function(){$('body').css('cursor','wait');$("#wait").show();},
    async:false,
    url: 'collect.php',
    success: function(msg) {
alert(msg);
      var error_str = msg.split("|||");
      if(error_str[0] == 'error'){ 
        alert('URL：'+error_str[1]+'\n更新が失敗しましたので、しばらくもう一度お試しください。');
        $('body').css('cursor','');
        setTimeout('read_time()',8000);
        location.href="show.php<?php echo (isset($_GET['flag']) ? '?flag='.$_GET['flag'].'&num='.time() : '').(isset($_GET['game']) ? (isset($_GET['flag']) ? '&game='.$_GET['game'] : '?game='.$_GET['game']) : '').'&';?>error_url="+error_str[1];
      }else{
        $('body').css('cursor','');
        setTimeout('read_time()',8000); 
        location.href="show.php<?php echo (isset($_GET['flag']) ? '?flag='.$_GET['flag'].'&num='.time() : '').(isset($_GET['game']) ? (isset($_GET['flag']) ? '&game='.$_GET['game'] : '?game='.$_GET['game']) : '');?>";
      }
    }
  }); 
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
  $.ajax({
    type: "POST",
    data: 'category_name='+category_name+'&products_name='+products_name+'&products_type='+products_type+'&products_id='+products_id,
    beforeSend: function(){$('body').css('cursor','wait');$("#wait").show();},
    async:false,
    url: 'ajax.php?action=update_products_price',
    success: function(msg) {
      setTimeout('read_time()',1000); 
      $('body').css('cursor','');
    }
  }); 
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
                 'ARAD'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
               'nobunaga'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
                                2=>'http://www.matubusi.com/system/pc/cart/nobunaga-rmt-hanbai/hanbai/items',
                                3=>'http://ftb-rmt.jp/nobunaga/buy/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/nobunaga/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/',
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
            'PSO2'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
            'L1'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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

            'TERA'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
            'AION'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
            'CABAL'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
            'latale'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
            'blade'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
            'EWD'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
            'LH'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
            'AA'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
            'ECO'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
                                2=>'http://www.matubusi.com/', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/',
                                6=>'http://rmt.kakaran.jp/eco/',
                                8=>'http://pastel-rmt.jp/',
                                9=>'http://rmt.diamond-gil.jp/game.php/gameid/eco/view/sv/',
                                10=>'http://www.asahi-rmt-service.com/',
                                ),
                                'sell'=>array(1=>'http://www.mugenrmt.com/',
                                 2=>'http://www.matubusi.com/', 
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
                                2=>'http://www.matubusi.com/', 
                                3=>'http://ftb-rmt.jp/', 
                                4=>'http://www.rmt-wm.com/',
                                5=>'http://rmtrank.com/fno+index.htm',
                                6=>'http://rmt.kakaran.jp/fno/',
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
                             'SUN'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'talesweave'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'MU'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'C9'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'cronous'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'rose'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'hzr'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'dekaron'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'fez'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'lakatonia'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'moe'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'mabinogi'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'genshin'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                             'lineage'=>array('buy'=>array(1=>'http://www.mugenrmt.com/',
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
                                 3=>'http://ftb-rmt.jp/', 
                                 4=>'http://www.rmt-wm.com/', 
                                 5=>'http://rmtrank.com/pico4+index.htm',
                                 6=>'http://rmt.kakaran.jp/lineage/',
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
echo '<table width="100%"><tr><td'.(!isset($_GET['flag']) || $_GET['flag'] == 'buy' ? ' style="background-color:#666666;"' : '').'><a href="show.php?flag=buy'.(isset($_GET['game']) ? '&game='.$_GET['game'] : '').'&num='.time().'">販売</a></td><td'.($_GET['flag'] == 'sell' ? ' style="background-color:#666666;"' : '').'><a href="show.php?flag=sell'.(isset($_GET['game']) ? '&game='.$_GET['game'] : '').'&num='.time().'">買取</a></td>';
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
echo '<table style="min-width:750px;" class="dataTableContent_right" width="100%" cellspacing="0" cellpadding="2" border="0">';
echo '<tr class="dataTableHeadingRow"><td class="dataTableHeadingContent_order" style=" text-align:left; padding-left:20px;"  nowrap="nowrap">'.(isset($_GET['game']) ? $game_str_array[$_GET['game']] : 'FF11').'</td>';

//查询当前游戏不是主站商品的信息
$category_list_array = array();
$category_query = mysql_query("select * from category where category_name='".$game."' and game_server='jp'");

while($category_array = mysql_fetch_array($category_query)){

  if($category_array['category_url'] != ''){
    if($category_array['category_type'] == 1){
      $category_list_array[$category_array['site_id']]['buy'] = $category_array['category_id'];
      $category_site_array['buy'][] = $category_array['site_id'];
    }else{
      $category_list_array[$category_array['site_id']]['sell'] = $category_array['category_id'];
      $category_site_array['sell'][] = $category_array['site_id'];
    }
  }
}

$flag = $_GET['flag'] == 'sell' ? 'sell' : 'buy';
$site_str = implode(',',$category_site_array[$flag]);
$site_list_array = array();
//将获取对应网站的信息取出。而不是所有 echo 输出的信息来源的网站名字
$site_query = mysql_query("select * from site where site_id in($site_str) order by sort_order");  

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

  $site_list_temp = unserialize($site_array['is_show']);
  if($site_list_temp[$game] !== 0 ){
    $site_list_array[] = $site_array['site_id'];

$url_arr = parse_url($_GET['error_url']);
if(($url_array[$game][$flag][$site_array['site_id']]==$_GET['error_url'] && isset($_GET['error_url'])) || strpos($url_array[$game][$flag][$site_array['site_id']],$url_arr['host'])){
    echo '<td class="dataTableHeadingContent_order"><a href="'.$url_array[$game][$flag][$site_array['site_id']].'" target="_black">'.$site_array['site_name'].'</a><span id="enable_img" ><img src="images/icon_alarm_log.gif"></span></td>';
}else{
        echo '<td class="dataTableHeadingContent_order"><a href="'.$url_array[$game][$flag][$site_array['site_id']].'" target="_black">'.$site_array['site_name'].'</a><span id="enable_img" style="display:none;"><img src="images/icon_alarm_log.gif"></span></td>';
}
  }
}
if($flag == 'buy'){
echo '<td width="5%" style="min-width:80px;">最安</td><td width="5%" style="min-width:80px;">次点</td></tr>';
}else{

echo '<td width="5%" style="min-width:80px;">最高</td><td width="5%" style="min-width:80px;">次点</td></tr>';
}
$product_list_aray = array();
$product_name_array = array();
$product_real_array = array();
$product_sort_array = array();
$game_type = $_GET['flag'] == 'sell' ? 0 : 1;
$product_query = mysql_query("select * from product p,category c where p.category_id=c.category_id and category_name='".$game."' and category_type='".$game_type."' and c.game_server='jp' order by p.sort_order desc");
if($game=='AION'){
//这里面有两个种类的游戏(特别处理。为了区分开)
$product_query = mysql_query("select * from product p,category c where p.category_id=c.category_id and category_name='".$game."' and category_type='".$game_type."' and c.game_server='jp' order by p.category_id desc, p.sort_order desc");
}
/*
$sql ="select * from product p,category c where p.category_id=c.category_id and category_name='".$game."' and category_type='".$game_type."' and c.game_server='jp' order by p.category_id desc, p.sort_order desc";
echo $sql;
*/

/*处理排序*/
if($game != 'AION'){
//主站价格不是0
  $product_query_before = mysql_query("select * from product p,category c where p.product_price!=0 and c.site_id=7 and p.category_id=c.category_id and category_name='".$game."' and category_type='".$game_type."' and c.game_server='jp' order by p.product_name asc");
  while($p_sort_array = mysql_fetch_array($product_query_before)){
     $product_before[]=$p_sort_array['product_name'];
  }
  if(empty($product_before)){
      $product_query_behind = mysql_query("select * from product p,category c where p.product_price=0 and p.category_id=c.category_id and category_name='".$game."' and category_type='".$game_type."' and c.game_server='jp' order by p.product_name asc");
  }else{
     $product_query_behind = mysql_query("select * from product p,category c where c.site_id=7 and p.category_id=c.category_id and category_name='".$game."' and category_type='".$game_type."' and c.game_server='jp' order by p.product_name asc");
  }
  while($p_sort_array = mysql_fetch_array($product_query_behind)){
     $product_behind[]=$p_sort_array['product_name'];
  }
  $product_behind = array_unique($product_behind);
  $product_sort = array_unique(array_merge($product_before,$product_behind));
  foreach($product_sort as $product_name){
     $temp_name = strtolower(trim(preg_replace('/\s+/is','',$product_name)));
     $product_sort_array[] = $temp_name;
  }
}
/*end 处理排序*/


while($product_array = mysql_fetch_array($product_query)){
	//AION是两个种商品.排序单独处理了
  if($game == 'AION'){
    $temp_name = strtolower(trim(preg_replace('/\s+/is','',$product_array['product_name'])));
    if(!in_array($temp_name,$product_sort_array)){
      $product_sort_array[] = $temp_name;     
    }
  }
  if($product_array['site_id'] != 7){
    if($game == 'PSO2'){
      $product_array['product_name'] = preg_replace('/(．|：)/is','',$product_array['product_name']);
      $product_list_aray[$product_array['category_id']][] = array('name'=>$product_array['product_name'],'price'=>$product_array['product_price'],'inventory'=>$product_array['product_inventory'],'product_id'=>$product_array['product_id'],'is_error'=>$product_array['is_error']);
    }else{
      $product_list_aray[$product_array['category_id']][] = array('name'=>$product_array['product_name'],'price'=>$product_array['product_price'],'inventory'=>$product_array['product_inventory'],'product_id'=>$product_array['product_id'],'is_error'=>$product_array['is_error']);
    }
    if($game == 'PSO2'){
      $product_name_array[] = strtolower(trim(preg_replace('/(．|：)/is','',$product_array['product_name'])));
    }else{
      $product_name_array[] = strtolower(trim(preg_replace('/\s+/is','',$product_array['product_name'])));
    }
    $product_real_array[] = $product_array['product_name'];
}else{
     $product_real_array[] = $product_array['product_name'];
     $product_name_array[] = strtolower(trim(preg_replace('/\s+/is','',$product_array['product_name'])));
     $product_list_aray[$product_array['category_id']][] = array('name'=>$product_array['product_name'],'price'=>$product_array['product_price'],'inventory'=>$product_array['product_inventory'],'product_id'=>$product_array['product_id'],'is_error'=>$product_array['is_error']);
  }
}
//sort($product_sort_array);

$product_name_array = array_unique($product_name_array);
if($game == 'DQ10'){

  $replace_name_array = array('ドラクエ10'=>'dq10');
  $product_name_array = $product_sort_array;
  $product_real_array = $product_sort_array;
  foreach($product_list_aray as $list_key=>$list_value){

    foreach($list_value as $key=>$value){

      $product_list_aray[$list_key][$key]['name'] = $product_sort_array[$key]; 
    }
  }
  $diff_name_array = array_keys($replace_name_array);
  $product_sort_array = array_diff($product_sort_array,$diff_name_array);
}else if($game == 'ARAD'){

  $replace_name_array = array('diregee'=>'ディレジエ',
                              'kain'=>'カイン' 
                            );
  foreach($product_list_aray as $list_key=>$list_value){

    foreach($list_value as $key=>$value){

      if(array_key_exists(strtolower($value['name']),$replace_name_array)){
        $product_list_aray[$list_key][$key]['name'] = $replace_name_array[strtolower($value['name'])]; 
      }
    }
  }
  $diff_name_array = array_keys($replace_name_array);
  $product_sort_array = array_diff($product_sort_array,$diff_name_array);
}

$product_name_sort_array = array();
foreach($product_sort_array as $sort_key=>$sort_value){

  if(in_array($sort_value,$product_name_array)){

    $product_name_sort_array[array_search($sort_value,$product_name_array)] = $sort_value; 
  }  
}

$products_type = $_GET['flag'] == 'sell' ? 0 : 1;
//获取选中价格的商品ID
$products_price_array = array();
$products_price_query = mysql_query("select * from products_price where category_name='".$game."' and product_type='".$products_type."'");
while($products_price_rows = mysql_fetch_array($products_price_query)){

  $products_price_array[$products_price_rows['product_name']] = $products_price_rows['product_id'];
}
mysql_free_result($products_price_query);


//print_r($product_sort_array);
//print_r($product_name_array);
//商品列表
$type = $_GET['flag'] == 'sell' ? 'sell' : 'buy';
$i=0;
foreach($product_name_sort_array as $p_key=>$product_value){
  $even = 'dataTableSecondRow';
  $odd = 'dataTableRow';
  if (isset($nowColor) && $nowColor == $odd) {
    $nowColor = $even;
  } else {
    $nowColor = $odd;
  }
//if($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price'] != ''){
echo '<tr class="'. $nowColor .'"  onmouseover="this.className=\'dataTableRowOver\'; this.style.cursor=\'hand\'"; onmouseout="this.className=\''. $nowColor .'\'" >';
    echo '<td align="left" nowrap="nowrap">'.$product_real_array[$p_key].(isset($_GET['game']) ? ($_GET['game'] == 'DQ10' ? ' JP' : '') : ' JP').'</td>';
 
    $price_array = array();
    foreach($site_list_array as $site_value){
      $product_key = '';
      foreach($product_list_aray[$category_list_array[$site_value][$type]] as $product_name_key=>$product_name_value){

        if($product_value == strtolower(trim(preg_replace('/\s+/is','',$product_name_value['name'])))){

          $product_key = $product_name_key;
        }
      }
      if(number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']) != 0){
        if($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'] == 0){
          if($inventory_flag_array[$game] === 0){
          //  echo '<td class="dataTableContent_gray"><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="dataTableContent_right"><tr><td width="50%" align="right" nowrap="nowrap">'.number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']).'円'.($inventory_show_array[$game] !== 0 ? '</td><td align="right" style="min-width:50px">'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'].'個' : '').'</td><td width="40%">&nbsp;</td></tr></table></td>';
            if($site_value == 7){

            echo '<td class="dataTableContent_gray"><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="dataTableContent_right"><tr><td width="50%" align="right" nowrap="nowrap"><b><label for ='.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'>'.number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']).'</label>円</b>'.($inventory_show_array[$game] !== 0 ? '</td><td align="right" style="min-width:45px">'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'].'個' : '').'</td><td width="40%">&nbsp;</td></tr></table></td>'; 
            }else{ 
            echo '<td class="dataTableContent_gray"><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="dataTableContent_right"><tr><td width="50%" align="right" nowrap="nowrap"><b>'.($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['is_error'] == 1 ? '<span id="enable_img" ><img src="images/icon_alarm_log.gif"></span>' : '').'<input id='.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].' type="radio" '.($products_price_array[$product_real_array[$p_key]] == $product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'] ? 'checked ' : '').'onclick="update_products_price(\''.$game.'\',\''.$product_real_array[$p_key].'\',\''.$flag.'\',\''.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'\')" name="select_products['.$product_real_array[$p_key].']" value="'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'"><label for ='.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'>'.number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']).'</label>円</b>'.($inventory_show_array[$game] !== 0 ? '</td><td align="right" style="min-width:45px">'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'].'個' : '').'</td><td width="40%">&nbsp;</td></tr></table></td>'; 
         }
        }else{
           echo '<td>&nbsp;</td>'; 
          }
        }else{ 
      //   if(strpos($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price'],'-')){
        //     $prices_array = explode('-',$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']);
          //   $inventorys_array =  explode('-',$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory']);      
            // $prices_str = '';
            // $prices_str .= '<td width="50%" align="right" nowrap="nowrap"><input type="radio" '.($products_price_array[$product_real_array[$p_key]] == $product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'] ? 'checked ' : '').'onclick="update_products_price(\''.$game.'\',\''.$product_real_array[$p_key].'\',\''.$flag.'\',\''.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'\')" name="select_products['.$product_real_array[$p_key].']" value="'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'">'.number_format($prices_array[0]).'円</td><td align="right" style="min-width:45px">'.$inventorys_array[0].'個</td>';
           //  $prices_str .= '<td width="50%" align="right" nowrap="nowrap"><input type="radio" '.($products_price_array[$product_real_array[$p_key]] == $product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'] ? 'checked ' : '').'onclick="update_products_price(\''.$game.'\',\''.$product_real_array[$p_key].'\',\''.$flag.'\',\''.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'\')" name="select_products['.$product_real_array[$p_key].']" value="'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'">'.number_format($prices_array[1]).'円</td><td align="right" style="min-width:45px">'.$inventorys_array[1].'個</td>';
            // $prices_str .= '<td width="50%" align="right" nowrap="nowrap"><input type="radio" '.($products_price_array[$product_real_array[$p_key]] == $product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'] ? 'checked ' : '').'onclick="update_products_price(\''.$game.'\',\''.$product_real_array[$p_key].'\',\''.$flag.'\',\''.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'\')" name="select_products['.$product_real_array[$p_key].']" value="'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'">'.number_format($prices_array[2]).'円</td><td align="right" style="min-width:45px">'.$inventorys_array[2].'個</td>';
             
            // echo '<td><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="dataTableContent_right"><tr>'.$prices_str.'<td width="40%">&nbsp;</td></tr></table></td>'; 
          // }else{ 
         //echo '<td><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="dataTableContent_right"><tr><td width="50%" align="right" nowrap="nowrap">'.number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']).'円'.($inventory_show_array[$game] !== 0 ? '</td><td align="right" style="min-width:50px">'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'].'個' : '').'</td><td width="40%">&nbsp;</td></tr></table></td>'; 
        if($site_value == 7){ 
          echo '<td><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="dataTableContent_right"><tr><td width="50%" align="right" nowrap="nowrap"><b>'.number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']).'<span style="margin-left: 2px;">円</span></b>'.($inventory_show_array[$game] !== 0 ? '</td><td align="right" style="min-width:45px">'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'].'個' : '').'</td><td width="40%">&nbsp;</td></tr></table></td>'; 
        }else{

          echo '<td><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="dataTableContent_right"><tr><td width="50%" align="right" nowrap="nowrap"><b>'.($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['is_error'] ? '<span id="enable_img" ><img src="images/icon_alarm_log.gif"></span>' : '').'<input id='.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].' type="radio" '.($products_price_array[$product_real_array[$p_key]] == $product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'] ? 'checked ' : '').'onclick="update_products_price(\''.$game.'\',\''.$product_real_array[$p_key].'\',\''.$flag.'\',\''.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'\')" name="select_products['.$product_real_array[$p_key].']" value="'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'"><label for='.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'>'.number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']).'<span  style="margin-left: 2px;">円</span></b>'.($inventory_show_array[$game] !== 0 ? '</td><td align="right" style="min-width:45px">'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'].'個' : '').'</td><td width="40%">&nbsp;</td></tr></table></td>'; 

        }  // }
        }
      }else{

        echo '<td>&nbsp;</td>';
      }
      if(number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']) != 0 && !($inventory_flag_array[$game] !== 0 && $product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'] == 0)){
        $price_array[] = $product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price'];
      }
    }
    $price_array = array_filter($price_array);
if($type=='buy'){
    sort($price_array);
}else{

   rsort($price_array);
}  
  $price = array();
    foreach($price_array as $val){
       if(!in_array($val,$price)){
         $price[] = $val;
       }
    }
$i++;
if(count($price)>1){
    echo '<td><input id= "zui_'.$i.'" type="radio" '.($products_price_array[$product_real_array[$p_key]] == -1 ? 'checked ' : '').'onclick="update_products_price(\''.$game.'\',\''.$product_real_array[$p_key].'\',\''.$flag.'\',\'-1\')" name="select_products['.$product_real_array[$p_key].']" value=""><label for="zui_'.$i.'">'.(number_format($price[0]) != 0 ? number_format($price[0]).'円' : '&nbsp;').'<label></td><td><input id="ci_'.$i.'" type="radio" '.(isset($products_price_array[$product_real_array[$p_key]]) && $products_price_array[$product_real_array[$p_key]] == 0 ? 'checked ' : '').'onclick="update_products_price(\''.$game.'\',\''.$product_real_array[$p_key].'\',\''.$flag.'\',\'0\')" name="select_products['.$product_real_array[$p_key].']" value=""><label for="ci_'.$i.'">'.(number_format($price[1]) != 0 ? number_format($price[1]).'円' : '&nbsp;').'<label></td>';
}else{

    echo '<td><label for="zui_'.$i.'">'.(number_format($price[0]) != 0 ? number_format($price[0]).'円' : '&nbsp;').'<label></td><td><label for="ci_'.$i.'">'.(number_format($price[1]) != 0 ? number_format($price[1]).'円' : '&nbsp;').'<label></td>';
}
    echo '</tr>';
}

//na ff14 游戏商品列表

$start_buy_id = '';
$start_sell_id = '';
$category_list_array = array();
$category_query = mysql_query("select * from category where category_name='".$game."' and game_server='na'");
while($category_array = mysql_fetch_array($category_query)){

  if($category_array['category_url'] != ''){
    if($category_array['category_type'] == 1){
      $products_max_query = mysql_query("select max(product_id) as max_num from product where category_id='".$category_array['category_id']."'");
      $products_max_array = mysql_fetch_array($products_max_query);
      $max_products_name[$category_array['category_id']] = $products_max_array['max_num'];
      $category_list_array[$category_array['site_id']]['buy'] = $category_array['category_id'];
    }else{
      $products_max_query = mysql_query("select max(product_id) as max_num from product where category_id='".$category_array['category_id']."'");
      $products_max_array = mysql_fetch_array($products_max_query);
      $max_products_name[$category_array['category_id']] = $products_max_array['max_num'];
      $category_list_array[$category_array['site_id']]['sell'] = $category_array['category_id'];
    }
  }
}
asort($max_products_name);
$start_buy_id = array_search(end($max_products_name),$max_products_name);
$start_sell_id = array_search(end($max_products_name),$max_products_name);
$product_list_aray = array();
$product_query = mysql_query("select * from product order by product_name asc");
while($product_array = mysql_fetch_array($product_query)){

  $product_list_aray[$product_array['category_id']][] = array('name'=>$product_array['product_name'],'price'=>$product_array['product_price'],'inventory'=>$product_array['product_inventory']);
}

//print_r($product_list_aray);
//商品列表
$key = $_GET['flag'] == 'sell' ? $start_sell_id : $start_buy_id;
$type = $_GET['flag'] == 'sell' ? 'sell' : 'buy';
foreach($product_list_aray[$key] as $product_key=>$product_value){
  $even = 'dataTableSecondRow';
  $odd = 'dataTableRow';
  if (isset($nowColor) && $nowColor == $odd) {
    $nowColor = $even;
  } else {
    $nowColor = $odd;
  }

    echo '<tr class="'. $nowColor .'"  onmouseover="this.className=\'dataTableRowOver\'; this.style.cursor=\'hand\'"; onmouseout="this.className=\''. $nowColor .'\'">';
    echo '<td align="left">'.$product_value['name'].' NA</td>';
    $price_array = array();
    foreach($site_list_array as $site_value){

      if(number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']) != 0){
        if($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'] == 0){
          if($inventory_flag_array[$game] === 0){
           
            echo '<td class="dataTableContent_gray"><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="dataTableContent_right"><tr><td width="50%" align="right" nowrap="nowrap">'.($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['is_error'] ? '<span id="enable_img" ><img src="images/icon_alarm_log.gif"></span>' : '').'<input id="" type="radio" '.($products_price_array[$product_real_array[$p_key]] == $product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'] ? 'checked ' : '').'onclick="update_products_price(\''.$game.'\',\''.$product_real_array[$p_key].'\',\''.$flag.'\',\''.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'\')" name="select_products['.$product_real_array[$p_key].']" value="'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'"><label for ='.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'>'.number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']).'</label>円'.($inventory_show_array[$game] !== 0 ? '</td><td align="right" style="min-width:45px">'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'].'個' : '').'</td><td width="40%">&nbsp;</td></tr></table></td>'; 
           //echo '<td class="dataTableContent_gray"><table  width="100%" border="0" cellspacing="0" cellpadding="0"  class="dataTableContent_right"><tr><td width="50%" align="right" nowrap="nowrap">'.number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']).'円'.($inventory_show_array[$game] !== 0 ? '</td><td align="right" style="min-width:50px">'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'].'個' : '').'</td><td width="40%">&nbsp;</td></tr></table></td>'; 
          }else{
           echo '<td>&nbsp;</td>'; 
          }
        }else{
          
          echo '<td><table width="100%" border="0" cellspacing="0" cellpadding="0"  class="dataTableContent_right"><tr><td width="50%" align="right" nowrap="nowrap">'.($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['is_error'] ? '<span id="enable_img" ><img src="images/icon_alarm_log.gif"></span>' : '').'<input id="" type="radio" '.($products_price_array[$product_real_array[$p_key]] == $product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'] ? 'checked ' : '').'onclick="update_products_price(\''.$game.'\',\''.$product_real_array[$p_key].'\',\''.$flag.'\',\''.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'\')" name="select_products['.$product_real_array[$p_key].']" value="'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'"><label for='.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['product_id'].'>'.number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']).'<span style="margin-left: 2px;">円</span>'.($inventory_show_array[$game] !== 0 ? '</td><td align="right" style="min-width:45px">'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'].'個' : '').'</td><td width="40%">&nbsp;</td></tr></table></td>'; 
         // echo '<td><table  width="100%" border="0" cellspacing="0" cellpadding="0"  class="dataTableContent_right"><tr><td width="50%" align="right" nowrap="nowrap">'.number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']).'円'.($inventory_show_array[$game] !== 0 ? '</td><td align="right" style="min-width:50px">'.$product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'].'個' : '').'</td><td width="40%">&nbsp;</td></tr></table></td>'; 
          
        }
      }else{

        echo '<td>&nbsp;</td>';
      }
      if(number_format($product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price']) != 0 && !($inventory_flag_array[$game] !== 0 && $product_list_aray[$category_list_array[$site_value][$type]][$product_key]['inventory'] == 0)){
        $price_array[] = $product_list_aray[$category_list_array[$site_value][$type]][$product_key]['price'];
      }
    }
    $price_array = array_filter($price_array);
    sort($price_array);
 
    echo '<td><input id="" type="radio" '.($products_price_array[$product_real_array[$p_key]] == -1 ? 'checked ' : '').'onclick="update_products_price(\''.$game.'\',\''.$product_real_array[$p_key].'\',\''.$flag.'\',\'-1\')" name="select_products['.$product_real_array[$p_key].']" value="">'.(number_format($price[0]) != 0 ? number_format($price[0]).'円' : '&nbsp;').'</td><td><input id="" type="radio" '.(isset($products_price_array[$product_real_array[$p_key]]) && $products_price_array[$product_real_array[$p_key]] == 0 ? 'checked ' : '').'onclick="update_products_price(\''.$game.'\',\''.$product_real_array[$p_key].'\',\''.$flag.'\',\'0\')" name="select_products['.$product_real_array[$p_key].']" value="">'.(number_format($price[1]) != 0 ? number_format($price[1]).'円' : '&nbsp;').'</td>';
 //  echo '<td>'.(number_format($price_array[0]) != 0 ? number_format($price_array[0]).'円' : '&nbsp;').'</td><td>'.(number_format($price_array[1]) != 0 ? number_format($price_array[1]).'円' : '&nbsp;').'</td>';
    echo '</tr>';
}
echo '</table>';
?>
<div id="wait" style="position:fixed; left:45%; top:45%; display:none;"><img src="images/load.gif" alt="img"></div>
</body>
</html>
