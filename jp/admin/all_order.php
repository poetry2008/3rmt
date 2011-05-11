<?php
  require('includes/application_top.php');
//  $_GET['y']=2010;
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
            // 买取
            $ot_total = $ot_total - ($op['final_price'] * $op['products_quantity'] * 2);
            $ot_subtotal = $ot_subtotal - ($op['final_price'] * $op['products_quantity'] * 2);
            // debug not open
            //tep_db_perform('orders_products', array('final_price' => $op['final_price'] - ($op['final_price'] * $op['products_quantity'] * 2)), 'update', "orders_products_id='".$op['orders_products_id']."'");
          }
        } else {
          $del .= $o['orders_id']." product deleted!<br>\n";
        }
      }
    }
    if ($ot_total != $ott['value']) {
      if (abs($ot_total) != abs($ott['value'])) {
        $red .= $o['orders_id']. " " . $ott['value'] . " => " . $ot_total ."<br>\n";
      } else {
        echo $o['orders_id']. " " . $ott['value'] . " => " . $ot_total ;
        echo "<br>\n";
      }
      // debug not open
      //tep_db_perform('orders_total', array('text' => '<b>'.$ot_total.'</b>', value => $ot_total), 'update', "orders_id='".$o['orders_id']."' and class='ot_total'");
    }
    if ($ot_subtotal != $ots['value']) {
      // debug not open
      //tep_db_perform('orders_total', array('text' => $ot_subtotal, value => $ot_subtotal), 'update', "orders_id='".$o['orders_id']."' and class='ot_subtotal'");
    }
  }
echo '正しくない注文書</br>';
echo '<hr>';
echo $red."<br>";
echo '注文した商品が存在してません</br>';
echo '<hr>';
echo $del."<br>";
