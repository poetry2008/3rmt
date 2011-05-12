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


$_GET['y']=2010;
set_time_limit(0);
if($_GET['y'] == '2007') {
  $query = tep_db_query("select * from orders where date_purchased>'2007-1-1 00:00:00' and date_purchased<'2008-1-1 00:00:00'");
} else if ($_GET['y'] == '2008'){
  $query = tep_db_query("select * from orders where date_purchased>'2008-1-1 00:00:00' and date_purchased<'2009-1-1 00:00:00'");
} else if ($_GET['y'] == '2009'){
  $query = tep_db_query("select * from orders where date_purchased>'2009-1-1 00:00:00' and date_purchased<'2010-1-1 00:00:00'");
} else if ($_GET['y'] == '2010'){
  $query = tep_db_query("select * from orders where date_purchased>'2010-1-1 00:00:00' and date_purchased<'2011-1-1 00:00:00'");
} else if ($_GET['y'] == '2011'){
  $query = tep_db_query("select * from orders where date_purchased>'2011-1-1 00:00:00' and date_purchased<'2012-1-1 00:00:00'");
} else {
  exit('no parameter');
}

while($o = tep_db_fetch_array($query)) {
  $op_query = tep_db_query("select * from orders_products where orders_id='".$o['orders_id']."'");
  $ott = tep_db_fetch_array(tep_db_query("select * from orders_total where orders_id='".$o['orders_id']."' and class='ot_total'"));
  $ots = tep_db_fetch_array(tep_db_query("select * from orders_total where orders_id='".$o['orders_id']."' and class='ot_subtotal'"));
  $ot_total = $ott['value'];
  $ot_subtotal = $ots['value'];
  while($op = tep_db_fetch_array($op_query)){
    if (intval($op['final_price']) > 0) {
      $p = tep_db_fetch_array(tep_db_query("select * from products where products_id='".$op['products_id']."'"));
      if ($p) {
        if ($p['products_bflag'] == '1' && $op['final_price'] > 0) {
          // 荵ｰ取
          $ot_total = 0-$ot_total;
          $ot_subtotal = 0-$ot_subtotal;
        }
      } else {
        $del .= $o['orders_id']." product deleted!<br>\n";
      }
    }
  }
  if(abs($ot_total) != abs($ot_subtotal)){//小計合計比較
   // $red2 .= $o['orders_id']. " 小計".$ot_subtotal." 合計".$ot_total."</br>\n";
      echo  $o['orders_id']. " 小計".$ot_subtotal." 合計".$ot_total."</br>\n";
      ob_flush();
      flush();
      mysql_query('update orders_total set value= 0-abs(`value`)'.' where orders_id = "'.$o['orders_id'].'"');
  }else 
  {
    if ($ot_total < 0 and $ot_total != $ott['value']) {
      //  echo "合計　".$o['orders_id']. " " . $ott['value'] . " => " . $ot_total ;
      //   echo "<br>\n";
      ob_flush();
      flush();
      mysql_query('update orders_total set value='.$ot_total.' where orders_id = "'.$o['orders_id'].'" and class = "ot_total"');
    }
    if($ot_subtotal < 0 and $ot_subtotal != $ots['value']){
      //echo "小計　".$o['orders_id']. " " . $ots['value'] . " => " . $ot_subtotal ;
      //echo "<br>\n";
      ob_flush();
      flush();
      mysql_query('update orders_total set value='.$ot_subtotal.' where orders_id = "'.$o['orders_id'].'" and class = "ot_subtotal"');
    }
    // debug not open
    //tep_db_perform('orders_total', array('text' => '<b>'.$ot_total.'</b>', value => $ot_total), 'update', "orders_id='".$o['orders_id']."' and class='ot_total'");

    // debug not open
    //tep_db_perform('orders_total', array('text' => $ot_subtotal, value => $ot_subtotal), 'update', "orders_id='".$o['orders_id']."' and class='ot_subtotal'");

    //}      
    //}

}
}
echo '<hr>';
echo '小計と合計が違う値<br>';
echo '<hr>';
//echo $red2."<br>";
echo '注文した商品が存在してません</br>';
echo '<hr>';
echo $del."<br>";
?>
</body>
</html>
