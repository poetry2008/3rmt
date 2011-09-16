<?php 
set_time_limit(0);
ob_implicit_flush(true);
ob_end_clean();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html dir="ltr" lang="ja">
   <head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <title>ORDER TYPE INIT</title>
   </head>
   </body>
   <h1>ORDER TYPE INIT</h1>
   <?php
error_reporting(E_ALL^E_NOTICE^E_WARNING);
ini_set("display_errors",'On');
$start = microtime(true);
   $language = 'japanese';
   $oa_admin = '';//realpath('./');
//   $oa_admin = realpath('./');
require_once ($oa_admin.'includes/configure.php');
require_once (DIR_WS_FUNCTIONS . 'database.php');
define('TABLE_ORDERS','orders');
function tep_check_order_type($oID)
  {
    $sql_count_op = "  SELECT products_id FROM orders_products op WHERE 1  AND op.orders_id = '".$oID."'";
    $query_count_op = tep_db_query($sql_count_op);
    while($sql_count_op_row = tep_db_fetch_array($query_count_op)){
      $sql_product = "select products_id from products where products_id = '".
        $sql_count_op_row['products_id']."'";
      if(!tep_db_num_rows(tep_db_query($sql_product))){
        return 4;
      }
    }
    $sql = "  SELECT avg( products_bflag ) bflag FROM orders_products op, products p  WHERE 1 AND p.products_id = op.products_id AND op.orders_id = '".$oID."'";

    $avg  = tep_db_fetch_array(tep_db_query($sql));
    $avg = $avg['bflag'];
    /*
    $sql_count_bflag = "  SELECT count( products_bflag ) count FROM orders_products op, products p  WHERE 1 AND p.products_id = op.products_id AND op.orders_id = '".$oID."'";
    $sql_count_op = "  SELECT count( products_id ) count FROM orders_products op WHERE 1  AND op.orders_id = '".$oID."'";
    $count_bflag =  tep_db_fetch_array(tep_db_query($sql_count_bflag));    
    $count_op =  tep_db_fetch_array(tep_db_query($sql_count_op));    
    if($count_bflag['count'] != $count_op['count']){
      if($count_bflag['count'] == 0 ){
        return 4;
      }
      return 3;
    }
    */
    

    if($avg == 0){
      return 1;
    }
    if($avg == 1){
      return 2;
    }
    return 3;

}
//define('OA_3RMTLIB','/home/.sites/28/site1/web/3rmtlib/');
tep_db_connect() or die('Unable to connect to database server!');
$all_order_sql = "select orders_id from orders";
$all_order_query = tep_db_query($all_order_sql);
while($order_row = tep_db_fetch_array($all_order_query)){
  $type = tep_check_order_type($order_row['orders_id']);
  $update_sql = "update orders set orders_type = '".$type."' where orders_id
    ='".$order_row['orders_id']."'";
  if(tep_db_query($update_sql)){
  echo $order_row['orders_id']."<br>";
  }
  ob_flush();
  flush();
?>
<?php
}
?>
</body>
</html>
