<?php
set_time_limit(0);
include("includes/configure.php");
$connect = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD)or die ("can not connect server!");
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");

echo "start!<br>";

$sql = "select products_id from products_attributes group by products_id";
$query = mysql_query($sql);
$all_options = array();
$i_num = 1;
$now_time = time()-3600*24*15;
$past_time = time()-3600*24*7;
while($array = mysql_fetch_array($query)){
  $products_attributes_query = mysql_query("select * from products_attributes where products_id='".$array['products_id']."'");
  $options_array_one = array();
  while($products_attributes_array=mysql_fetch_array($products_attributes_query)){
    $get_options_sql = "select o.products_options_name from products_options as o where o.products_options_id='".$products_attributes_array['options_id']."'";
    
    $get_options_values_sql = "select ov.products_options_values_name from products_options_values as ov where ov.products_options_values_id='".$products_attributes_array['options_values_id']."'";
    
    $get_options_query = mysql_query($get_options_sql);
    $get_options_values_query = mysql_query($get_options_values_sql);
    
    $get_options_array = mysql_fetch_array($get_options_query);
    $get_options_values_array = mysql_fetch_array($get_options_values_query);
    
    $options_array_one['value']['se_option'][] = $get_options_values_array['products_options_values_name'];
    $options_array_one['value']['secomment'] = "";
    $options_array_one['value']['sedefault'] = "";
    $options_array_one['name'] = $get_options_array['products_options_name'];
  }
 
  if (empty($options_array_one['name'])) {
    continue; 
  }
  
  $options_array_one['value']['se_option'] = array_reverse($options_array_one['value']['se_option']);
  $new = 0; 
  
  if(empty($all_options)){
    $new = 0;
  }else{
    foreach($all_options as $key=>$val){
      $tmp_options_array = $val;
      unset($tmp_options_array['value']['eid']);

      if(($options_array_one['name'] == $val['name']) && (serialize($options_array_one['value']) == serialize($tmp_options_array['value']))){
        $new = $val['value']['eid'];
        break;
      }
    }
  }
  
  if($new == 0){
    $sql_group = "INSERT INTO `option_group` (`id`, `name`, `title`, `option`, `comment`, `is_preorder`, `sort_num`, `created_at`) VALUES (NULL, '".$options_array_one['name']."_".(count($all_options)+1)."', '".$options_array_one['name']."', '', '', '1', '1000', '".date('Y-m-d H:i:s', $now_time+$i_num)."')";
    mysql_query($sql_group);
    $last_id = mysql_insert_id();
    
    $item_name = rand_name();
    $options_array_one['value']['eid'] = $last_id;
    $all_options[] = $options_array_one;

    $options_array_one['value'] = serialize($options_array_one['value']);
    
    $sql_item = "INSERT INTO `option_item` (`id` , `group_id` , `title` , `front_title` , `name` , `comment` , `option` , `type` , `price` , `status` , `sort_num` , `place_type`, `created_at`) VALUES (NULL, '".$last_id."', '".$options_array_one['name']."', '".$options_array_one['name']."', '".$item_name."', '', '".addslashes($options_array_one['value'])."', 'select', '0.0000', '1', '1000', '0', '".date('Y-m-d H:i:s', $now_time+$i_num)."')";
    mysql_query($sql_item);

    $string_array = 'a:7:{s:5:"itext";s:0:"";s:7:"require";s:1:"1";s:8:"icomment";s:0:"";s:5:"iline";s:1:"1";s:6:"ictype";s:1:"0";s:8:"imax_num";s:3:"100";s:3:"eid";i:0;}';
    $string_array_show = @unserialize($string_array);
    $string_array_show['eid'] = $last_id;

    $string = serialize($string_array_show);

    $item_t_name = rand_name();

    $sql_item_add = "INSERT INTO `option_item` (`id`, `group_id`, `title`, `front_title`, `name`, `comment`, `option`, `type`, `price`, `status`, `sort_num`, `place_type`, `created_at`) VALUES (NULL, '".$last_id."', 'お客様のキャラクター名', 'お客様のキャラクター名', '".$item_t_name."', '', '".addslashes($string)."', 'textarea', '0.0000', '1', '1000', '1', '".date('Y-m-d H:i:s',$past_time+$i_num+1)."')";
    mysql_query($sql_item_add);
    
    $sql_products = "UPDATE `products` SET `belong_to_option` = '".$last_id."' WHERE `products_id` =".$array['products_id']."";
    mysql_query($sql_products);

  }else{
    $sql_products = "UPDATE `products` SET `belong_to_option` = '".$new."' WHERE `products_id` =".$array['products_id']."";
    mysql_query($sql_products);
  }
  $i_num++;
}
echo "Complete!";









function rand_name(){
  $pattern = "abcdefghijklmnopqrstuvwxyz";
  $res = "";
  for($i = 0;$i < 16;$i++){
    $res .= $pattern[mt_rand(0,25)];
  }
  $check_res_sql = "select name from option_item where name='".$res."'";
  $check_res_query = mysql_query($check_res_sql);
  $check_res_rows = mysql_num_rows($check_res_query);
  if($check_res_rows > 0){
    rand_name();
  }
  return $res;
}

?>
