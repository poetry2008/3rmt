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
$sql_orders = "SELECT op.orders_products_id, op.orders_id, avg( p.products_bflag ) avgf
FROM orders_products op, products p,orders o
WHERE op.products_id = p.products_id 
and o.date_purchased > '".$year."-1-1 00:00:00'  
and o.date_purchased < '".$year1."-1-1 00:00:00' 
and o.orders_id = op.orders_id
group by op.orders_id";
$query = tep_db_query($sql_orders);
while($o = tep_db_fetch_array($query)) {

  $op_query = tep_db_query("select * from orders_products where orders_id='".$o['orders_id']."'");
  $ott = tep_db_fetch_array(tep_db_query("select * from orders_total where orders_id='".$o['orders_id']."' and class='ot_total'"));
  $ots = tep_db_fetch_array(tep_db_query("select * from orders_total where orders_id='".$o['orders_id']."' and class='ot_subtotal'"));
  $ot_total = $ott['value'];
  $ot_subtotal = $ots['value'];
  if ($o['avgf']>0 and $o['avgf']<1){
    $mixed.= $o['orders_id'] ." 小計".$ot_subtotal." 合計".$ot_total."</br>\n";
    continue;
  }
  while($op = tep_db_fetch_array($op_query)){

    if (intval($op['final_price']) > 0) {
      $p = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$op['products_id']."'"));
      if ($p) {
        if ($p['products_bflag'] == '1' && $op['final_price'] > 0) {

          $ot_total = 0-$ot_total;
          $ot_subtotal = 0-$ot_subtotal;
          if(abs($ot_total) != abs($ot_subtotal)){//小计和合计进行比较
            $red2 .= $o['orders_id']. " 小計".$ot_subtotal." 合計".$ot_total."</br>\n";
            ob_flush();
            flush();
          } else {
            //if ($ot_total < 0 and $ot_total != $ott['value']) {
              //echo "合計　".$o['orders_id']. " " . $ott['value'] . " => " . $ot_total ;
              //echo "<br>\n";
              //ob_flush();
              //flush();
	    //            }
            //if($ot_subtotal < 0 and $ot_subtotal != $ots['value']){
              //echo "小計　".$o['orders_id']. " " . $ots['value'] . " => " . $ot_subtotal ;
              //echo "<br>\n";
              //ob_flush();
              //flush();
	    //            }
          }
        }
      } else {
        $del .= $o['orders_id']." product deleted!<br>\n";
      }
    }
  }
}
echo '<hr>';
echo '小計と合計が違う値<br>';
echo '<hr>';
echo "\n";
echo $red2."<br>";
echo '注文した商品が存在してません</br>';
echo '<hr>';
echo $del."<br>";
echo "\n";
echo 'buy and sell</br>';
echo '<hr>';
echo $mixed."<br>";
?>
</body>
</html>
