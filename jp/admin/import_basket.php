<?php
set_time_limit(0);
include("includes/configure.php");
$connect = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD) or die('can not connect database');
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");

echo "Start!<br>";

$sql = "select distinct(customers_id) from customers_basket";
$query = mysql_query($sql);
$show_num = 0;
while($array = mysql_fetch_assoc($query)){
  $sql_0 = "select  customers_id,products_id,customers_basket_id from customers_basket where customers_id='".$array['customers_id']."'";
  $query_0 = mysql_query($sql_0);
  $basket_info_array = array();
  $new_basket_array = array();
  while($array_0 = mysql_fetch_assoc($query_0)){
    $basket_info_array[] = array($array_0['products_id'],$array_0['customers_basket_id']);
  }

  $i = 0;
  foreach($basket_info_array as $key => $val)
  {
    if(preg_match('/^\d+_\d+$/',$val[0])){
      continue;	
    }
    
    if($key==0){
      if(strpos($val[0],"{") === false){
        $new_basket_array[$i][] = $val[0]."_1";
      }else{
        $pos1 = strpos($val[0],"{");
        $new_basket_array[$i][] = substr($val[0],0,$pos1)."_1";
      }
    }else{
      if(preg_match('/^\d+$/',$val[0])){
        $tmp_pro_id = $val[0];
      }else{
        $pos_str = strpos($val[0],'{');
        $tmp_pro_id = substr($val[0],0,$pos_str)	;
      }

      $tmp_num = 0;
      foreach($new_basket_array as $key1 => $val1){
        if(preg_match('/^'.$tmp_pro_id.'_\d+$/',$val1[0])){
          $tmp_pro_array = explode('_',$val1[0])	;
          $tmp_num = $tmp_pro_array[1];
        }	
      }

      if($tmp_num == 0){
        $new_basket_array[$i][] = $tmp_pro_id.'_1';
      }else{
        $tmp_num = $tmp_num+1;
        $new_basket_array[$i][] = $tmp_pro_id.'_'.$tmp_num;
      }

    }
    $new_basket_array[$i][] = $val[0];
    $new_basket_array[$i][] = $val[1];
    $i++;
  }
  
  foreach($new_basket_array as $u_key => $u_val){
    $customers_b_sql = "update `customers_basket` set `products_id` = '".$u_val[0]."' where `customers_basket_id` = '".$u_val[2]."'";
    mysql_query($customers_b_sql);
    
    $option_v_id_query = mysql_query("select products_options_value_id from customers_basket_attributes where customers_id='".$array['customers_id']."' and products_id='".$u_val[1]."'");
    $option_v_id_array = mysql_fetch_assoc($option_v_id_query);
    if(empty($option_v_id_array)){
      continue;
    }
    $option_v_id = $option_v_id_array['products_options_value_id'];
    $option_v_query = mysql_query("select products_options_values_name from products_options_values where products_options_values_id='".$option_v_id."'");
    $option_v_array = mysql_fetch_assoc($option_v_query);
    if(empty($option_v_array)){
      continue;
    }
    $option_v = $option_v_array['products_options_values_name'];
    $p_id = explode('_',$u_val[0]);
    $belong_to_op_query = mysql_query("select belong_to_option from products where products_id='".$p_id[0]."'");
    $belong_to_op_array = mysql_fetch_assoc($belong_to_op_query);
    if(empty($belong_to_op_array)){
      continue;
    }
    $belong_to_op = $belong_to_op_array['belong_to_option'];
    $op_item_info_query = mysql_query("select * from option_item where group_id='".$belong_to_op."' and type='select' and `option` like '%".$option_v."%'");
    $op_item_info_array = mysql_fetch_assoc($op_item_info_query);
    if(empty($op_item_info_array)){
      continue;
    }
    $op_item_id = $op_item_info_array['id'];
    $op_item_group_id = $op_item_info_array['group_id'];
    $op_item_name = $op_item_info_array['name'];
    $option_info = array("op_".$op_item_name."_".$op_item_group_id."_".$op_item_id => $option_v);
    $option_info = serialize($option_info);
    $customers_b_op_sql = "insert into `customers_basket_options` (`option_id`,`customers_id`,`products_id`,`option_info`) values (NULL,'".$array['customers_id']."','".$u_val[0]."','".addslashes($option_info)."')";

    mysql_query($customers_b_op_sql);
  }

echo $show_num;
$show_num++;

}
echo "Complete!";
?>
