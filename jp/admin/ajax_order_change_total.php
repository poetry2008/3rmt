<?php
require('includes/application_top.php');

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
# 永远是改动过的
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
# HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
# HTTP/1.0
header("Pragma: no-cache");
if(isset($_GET['action'])&&$_GET['action']=='save_change'){
  $oid = $_POST['o_id'];
  $isreg = 0;
  $sub = $_POST['sub'];
  $suborder = $_POST['suborder'];
  $total = $_POST['total'];
  $point = $_POST['point'];
  if($sub == $total){
    $isreg = 1;
  }
  $suborder_query = tep_db_query("select count(*) cnt from orders_total where
      orders_id = '".$oid."'");
  $suborder_row = tep_db_fetch_array($suborder_query);
  $suborder=$suborder_row['cnt'];
  tep_db_query("update orders_temp 
      set sub = '".$sub."',
      suborder = '".$suborder."',
      total = '".$total."',
      point = '".$point."',
      isreg = '".$isreg."' where id = '".$oid."'");
  $ot_total_query = tep_db_query("select * from orders_total where 
      orders_id = '".$oid."' and class = 'ot_total'");
  if($ot_total_row = tep_db_fetch_array($ot_total_query)){
    tep_db_query('update orders_total set value = "'.$total.'" 
        where orders_total_id = "'.$ot_total_row['orders_total_id'].'"');
  }else{
    tep_db_query("INSERT INTO `orders_total` (`orders_total_id`,
      `orders_id`, `title`, `text`, `value`, `class`, `sort_order`) VALUES (NULL,
        '".$oid."', '合計:', '', '".$total."', 'ot_total', '0')");
  }

  $ot_subtotal_query = tep_db_query("select * from orders_total where 
      orders_id = '".$oid."' and class = 'ot_subtotal'");
  if($ot_subtotal_row = tep_db_fetch_array($ot_subtotal_query)){
    tep_db_query('update orders_total set value = "'.$sub.'" 
        where orders_total_id = "'.$ot_subtotal_row['orders_total_id'].'"');
  }else{
    tep_db_query("INSERT INTO `orders_total` (`orders_total_id`,
      `orders_id`, `title`, `text`, `value`, `class`, `sort_order`) VALUES (NULL,
        '".$oid."', '小計:', '', '".$sub."', 'ot_subtotal', '0')");
  }

  $ot_point_query = tep_db_query("select * from orders_total where 
      orders_id = '".$oid."' and class = 'ot_point'");
  if($ot_point_row = tep_db_fetch_array($ot_point_query)){
    tep_db_query('update orders_total set value = "'.$point.'" 
        where orders_total_id = "'.$ot_point_row['orders_total_id'].'"');
  }else{
    tep_db_query("INSERT INTO `orders_total` (`orders_total_id`,
      `orders_id`, `title`, `text`, `value`, `class`, `sort_order`) VALUES (NULL,
        '".$oid."', 'ポイント割引:', '', '".$point."', 'ot_point', '0')");
  }
  $info_query = tep_db_query("select sub,total,point,isreg,suborder,type from
      orders_temp where id = '".$oid."'");
  if($row = tep_db_fetch_array($info_query)){
    echo
      $row['total'].",".
      $row['sub'].",".
      $row['point'].",".
      $row['suborder'].",".
      $row['isreg'].",".
      $row['type'];
  }
}else if(isset($_GET['action'])&&$_GET['action']=='reset_db'){

$checkSql[] = "TRUNCATE TABLE `orders_temp`";
$checkSql[] = "insert into orders_temp(id)
select distinct(orders.orders_id) from orders";
$checkSql[] = "update orders_temp  o ,
orders_total ot1, 
orders_total ot2,
orders_total ot3
set o.`total` = ot1.value
, o.`sub`   = ot2.value
, o.`point` = ot3.value 
where 1
and o.id = ot1.orders_id 
and o.id = ot2.orders_id
and o.id = ot3.orders_id
and ot1.class = 'ot_total'
and ot2.class = 'ot_subtotal'
and ot3.class = 'ot_point'";

$checkSql[] = "update orders_temp
   o 
   set 
   o.avgb = -1,
  type='ERROR'  ,
   o.isreg = if (abs(`total`)=abs(`sub`),1,0)";
$checkSql[] = "
UPDATE orders_temp o,
(
SELECT op.orders_products_id, op.orders_id, avg( p.products_bflag ) avgf
FROM orders_products op, products p
WHERE op.products_id = p.products_id
GROUP BY op.orders_id
)tt
SET o.avgb = avgf WHERE tt.orders_id = o.id";
$checkSql[] = "UPDATE orders_temp SET TYPE ='BUY' WHERE   avgb  = 0";
$checkSql[] = "UPDATE orders_temp SET TYPE ='SELL' WHERE  avgb = 1";
$checkSql[] = "UPDATE orders_temp SET TYPE ='MIX' WHERE   avgb >0 and avgb <1 ";
$return_str = "all sql run ok ";
foreach($checkSql as $sql){
  if(!tep_db_query($sql)){
    $return_str = "sql has error";
  }
}
echo $return_str;
}
