<?php
set_time_limit(0);
include("includes/configure.php");
$connect = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD) or die('can not connect database');
mysql_select_db(DB_DATABASE);
mysql_query("set names utf8");

echo "start!<br>";

$orders_products_attributes_sql = "select * from orders_products_attributes";
$orders_products_attributes_query = mysql_query($orders_products_attributes_sql);
$show_num = 0;
while($orders_products_attributes_array = mysql_fetch_array($orders_products_attributes_query)){

  if(empty($orders_products_attributes_array['products_options'])){
    continue;
  }

  $option_info = array();
  $option_info["title"] = $orders_products_attributes_array['products_options'];
  $option_info["value"] = $orders_products_attributes_array['products_options_values'];
  $option_info = serialize($option_info);
           
  $order_o_pro_att_sql = "UPDATE `orders_products_attributes` SET `option_info` = '".addslashes($option_info)."' WHERE `orders_products_attributes_id` = '".$orders_products_attributes_array['orders_products_attributes_id']."'";
  mysql_query($order_o_pro_att_sql);

  $option_val_id_query = mysql_query("select options_values_id from products_attributes where products_attributes_id='".(int)$orders_products_attributes_array['attributes_id']."'");
  $option_val_id_array = mysql_fetch_array($option_val_id_query);
  if($option_val_id_array){
    $option_val_query = mysql_query("select products_options_values_name from products_options_values where products_options_values_id='".$option_val_id_array['options_values_id']."'");
    $option_val_array = mysql_fetch_array($option_val_query);
    if($option_val_array){
      $products_id_query = mysql_query("select products_id from orders_products where orders_products_id='".  $orders_products_attributes_array['orders_products_id']."'");
      $products_id_array = mysql_fetch_array($products_id_query);

      if($products_id_array){
        $belong_to_op_query = mysql_query("select belong_to_option from products where products_id='".$products_id_array['products_id']."'");
        $belong_to_op_array = mysql_fetch_array($belong_to_op_query);

        if($belong_to_op_array){
          $op_id_query = mysql_query("select id,group_id from option_item where group_id='".$belong_to_op_array['belong_to_option']."' and `option` like '%".$option_val_array['products_options_values_name']."%' and front_title='".$orders_products_attributes_array['products_options']."' and type='select'");
          $op_id_array = mysql_fetch_array($op_id_query);
          if($op_id_array){
            $op_group_id = $op_id_array['group_id'];
            $op_item_id = $op_id_array['id'];

            $order_pro_att_sql = "UPDATE `orders_products_attributes` SET `option_group_id` = '".$op_group_id."', `option_item_id` = '".$op_item_id."' WHERE `orders_products_attributes_id` = '".$orders_products_attributes_array['orders_products_attributes_id']."'";

            mysql_query($order_pro_att_sql);
          } 
        }
      }
    }
  }
  echo $show_num; 
  $show_num++;
}
echo "finsh!";
?>
