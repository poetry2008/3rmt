<?php
ob_start();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
  <body>
  <?php
  require('includes/application_top.php');
set_time_limit(0);
if (isset($_GET['y'])){
  $year = $_GET['y'];
}else {
  $year = 2010;
}
$year = intval($year);
$year1 = $year+1;
echo '<hr>';
echo '小計と合計が違う値<br>';
echo '<hr>';
$sql_orders = "SELECT op.orders_products_id, op.orders_id, avg( p.products_bflag ) avgf
FROM orders_products op, products p,orders o
WHERE op.products_id = p.products_id 
and o.date_purchased > '".$year."-1-1 00:00:00'  
and o.date_purchased < '".$year1."-1-1 00:00:00' 
and o.orders_id = op.orders_id
group by op.orders_id";
$query = tep_db_query($sql_orders);
$shouldUpdateOrdres = array();
$shouldUpdateOrderFinalPrice = array();

while($o = tep_db_fetch_array($query)) {
  $ot_total = '';
  $ot_subtotal = '';
  if ($o['avgf']>0 and $o['avgf']<1){
    $mixedOrder = tep_db_fetch_array(tep_db_query("
SELECT ot1.value AS ot_subtotal, ot2.value AS ot_total
FROM orders_total ot1, orders_total ot2
WHERE ot2.class = 'ot_total'
AND ot1.class = 'ot_subtotal'
AND ot1.orders_id = ot2.orders_id
AND ot1.orders_id = '".$o['orders_id']."'"
));
    $mixed.= $o['orders_id'] ." 小計".$mixedOrder['ot_subtotal']." 合計".$mixedOrder['ot_total']."</br>\n";
    continue;
  }
  //如果是荵ｰ
  if ($o['avgf'] ==1 ){

    ob_flush();
    flush();
    $op_query = tep_db_query("select * from orders_products where orders_id='".$o['orders_id']."'");
    $ott = tep_db_fetch_array(tep_db_query("select * from orders_total where orders_id='".$o['orders_id']."' and class='ot_total'"));
    $ots = tep_db_fetch_array(tep_db_query("select * from orders_total where orders_id='".$o['orders_id']."' and class='ot_subtotal'"));
    $ot_total = $ott['value'];
    $ot_subtotal = $ots['value'];

    if(!in_array($o['orders_id'],$shouldUpdateOrderFinalPrice)){
      $shouldUpdateOrderFinalPrice[] = $o['orders_id'];
      tep_db_query("update orders_products set final_price=0-`final_price` where orders_id = '".$o['orders_id']."'");          
    }

    while($op = tep_db_fetch_array($op_query)){
      //      if (intval($op['final_price']) > 0) {
      $p = tep_db_fetch_array(
                              tep_db_query(
                                           "select * from products where products_id='".$op['products_id']."'"
                                           )
                              );
      if ($p) {

        // 荵ｰ取
        $ot_total = 0-$ot_total;
        $ot_subtotal = 0-$ot_subtotal;
          
        //小計合計比較
        if($ot_total < 0 and $ot_subtotal < 0 and abs($ot_total) != abs($ot_subtotal)){
          echo  $o['orders_id']. " 小計".$ot_subtotal." 合計".$ot_total."</br>\n";
          //        $red2.=$o['orders_id']. " 小計".$ot_subtotal." 合計".$ot_total."</br>\n";
          ob_flush();
          flush();
          if(!in_array($o['orders_id'],$shouldUpdateOrdres)){
            $shouldUpdateOrdres[] = $o['orders_id'];
             mysql_query('update orders_total set value= 0-`value`'.' where orders_id = "'.$o['orders_id'].'"');
          }
        } else {
          if ($ot_total < 0 and $ot_total != $ott['value']) {
            mysql_query('update orders_total set value='.$ot_total.' where orders_id = "'.$o['orders_id'].'" and class = "ot_total"');
          }
          if($ot_subtotal < 0 and $ot_subtotal != $ots['value']){
            mysql_query('update orders_total set value='.$ot_subtotal.' where orders_id = "'.$o['orders_id'].'" and class = "ot_subtotal"');
          }
        }
      } else {
        $del .= $o['orders_id']." product deleted!<br>\n";
      }
    }
  }
}
//echo $red2."<br>";
echo '注文した商品が存在してません</br>';
echo '<hr>';
echo "\n";
echo $del."<br>";
echo "\n";
echo 'buy and sell</br>';
echo "\n";
echo '<hr>';
echo "\n";
echo $mixed."<br>";
?>
</body>
</html>
