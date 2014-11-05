<?php
/*
 *ajax submit
 */
require('includes/configure.php');
//link db
$link = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
mysql_query('set names utf8');
mysql_select_db(DB_DATABASE);

/*
  *更新选中的商品价格
 */
if($_GET['action'] == 'update_products_price'){

  $category_name = $_POST['category_name'];
  $products_name = $_POST['products_name'];
  $products_type = $_POST['products_type'];
  $products_type = $products_type == 'buy' ? 1 : 0;
  $products_id = $_POST['products_id'];

  $products_query = mysql_query("select * from products_price where category_name='".$category_name."' and product_name='".$products_name."' and product_type='".$products_type."'");
  $products_num = mysql_num_rows($products_query);

  if($products_num == 1){

    mysql_query("update products_price set product_id='".$products_id."' where category_name='".$category_name."' and product_name='".$products_name."' and product_type='".$products_type."'");
  }else{
    mysql_query("insert into products_price(id,category_name,product_name,product_type,product_id) values(NULL,'".$category_name."','".$products_name."','".$products_type."','".$products_id."')"); 
  }
}
/**
 *点击开始/停止采集
 * */
else if($_GET['action'] == 'collect_data_start'){
   $config_value = $_POST['config_value'];
    mysql_query("update config set config_value='".$config_value."' where config_key='COLLECT_DATA_STATUS'");
   if(isset($config_value)&&$config_value==1){
	   exec('/etc/php cron_collect_tep.php',$array,$status);
	   var_dump($array);
	   var_dump($status);
   }


}
?>
