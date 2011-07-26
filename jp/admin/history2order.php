<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html dir="ltr" lang="ja">
   <head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <title>order status history to orders</title>
   </head>
   </body>
   <h1>order status history to orders</h1>
   <?php
    set_time_limit(0);
   error_reporting(E_ALL^E_NOTICE^E_WARNING);
ini_set("display_errors",'On');
$start = microtime(true);
   //无用数据 
   //delete from oa_item where group_id not in (select id from oa_group ) //删除非现有组的oa_item
   $language = 'japanese';
require_once 'includes/configure.php';
require_once (DIR_WS_FUNCTIONS . 'database.php');
tep_db_connect() or die('Unable to connect to database server!');
$select_sql = "SELECT *
FROM `orders_status_history`
WHERE orders_status_id = '9'
GROUP BY orders_id
ORDER BY `date_added` DESC";
$select_query = tep_db_query($select_sql);
while($select_row = tep_db_fetch_array($select_query)){
  $sql_update = "update orders set confirm_payment_time = '".$select_row['date_added']."'
     where orders_id = '".$select_row['orders_id']."'";
  if(tep_db_query($sql_update)){
    echo $select_row['orders_id']." updated <br>";
  }
}
echo 'all date changed ok '

?>
</body>
</html>
