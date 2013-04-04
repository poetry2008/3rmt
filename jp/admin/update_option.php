<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<?php
$do = 0;
$undo = 0;
ini_set("display_errors", "Off");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
set_time_limit(0);
include("includes/configure.php");
$con = mysql_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD) or die('can not connect server!');
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");
echo "start update お客様のキャラクター名";
echo "<br>";
$sql = "SELECT `id` , `option` , `group_id` 
FROM `option_item`
WHERE `front_title` = 'お客様のキャラクター名' 
and type = 'textarea'";
$query = mysql_query($sql);
$new_text = 
'<p class=red>ご入力されましたキャラクター名にお間違えはございませんか？</p>
<span>よくある間違い</span>
<p>
■ スペル間違い。記号や数字の有無。<br>
■ -　（ハイフン）と　_　（アンダーバー）の入力間違い。<br>
■ ・　（中点）と　.　（ドット）の入力間違い。
</p>
<p>
<span class=red>※</span>&nbsp;キャラクター名の入力不要な商品が一部ございます。「入力フォーム」が表示されない場合は「次へ進む」をクリックしてください。
</p>';
function get_item_group_title($gid){
  $g_sql = "SELECT name
    FROM `option_group`
    WHERE id = '".$gid."' limit 1";
  $g_query = mysql_query($g_sql);
  $g_row = mysql_fetch_array($g_query);
  if(isset($g_row)){
    return $g_row['name'];
  }else{
    return '';
  }
}
while($row = mysql_fetch_array($query)){
  $id = $row['id'];
  $info_arr = unserialize($row['option']);
  $new_info_arr = array();
  $flag = false;
  $show_flag = false;
  foreach($info_arr as $key=>$value){
    if($key == 'icomment' && $value == ''){
      $new_info_arr[$key] = $new_text;
      $flag = true;
    }else if($key == 'icomment'){
      $new_info_arr[$key] = $value;
      if($value!=$new_text){
      $show_flag = true;
      }
    }else{
      $new_info_arr[$key] = $value;
    }
  }
  $g_name = get_item_group_title($row['group_id']);
  if($flag){
    $new_option = serialize($new_info_arr);
    $update_sql = "UPDATE `option_item` SET `option` = '".$new_option."' 
      where `id` = '".$row['id']."'";
    mysql_query($update_sql);
    echo $g_name .' > '.'お客様のキャラクター名 注釈  修正完了';
    $do++;
    echo "<br>";
  }
  if($show_flag){
    echo $g_name .' > '.'お客様のキャラクター名 注釈  未処理';
    $undo++;
    echo "<br>";
  }
}
echo '处理件数合计: '.$do;
echo "<br>";
echo "未处理合计: ".$undo;
echo "<br>";
?>
update is finish
</body>
</html>
