<?php
$_link  = mysql_connect('localhost', 'root', '123456') or die('3');

mysql_query('set names utf8');

echo "jp\n";
//mysql_select_db('test_iimycatalog_utf8');
//$query = mysql_query("select * from iimy_customers order by customers_id asc");
//mysql_select_db('test_iimycatalog_final');
//while($c = mysql_fetch_array($query)) {
  ////echo $c['customers_id'];
  //if (!mysql_num_rows(mysql_query("select * from customers where customers_firstname = '".$c['customers_firstname']."'"))) {
    //echo $c['customers_id'] . "\n";
  //}
//}

mysql_select_db('test_iimycatalog_utf8');
$query = mysql_query("select * from iimy_orders order by orders_id asc");
mysql_select_db('test_iimycatalog_final');
while($c = mysql_fetch_array($query)) {
  //echo $c['customers_id'];
  if (!mysql_num_rows(mysql_query("select * from orders where orders_id = '".$c['orders_id']."'"))) {
    echo $c['orders_id'] . "\n";
  }
}

/*
echo "gm\n";
mysql_select_db('test_iimycatalog_utf8');
$query = mysql_query("select * from gm_customers order by customers_id asc");
mysql_select_db('test_iimycatalog_final');
while($c = mysql_fetch_array($query)) {
  //echo $c['customers_id'];
  if (!mysql_num_rows(mysql_query("select * from customers where customers_firstname = '".$c['customers_firstname']."'"))) {
    echo $c['customers_id'] . "\n";
  }
}

echo "wm\n";
mysql_select_db('test_iimycatalog_utf8');
$query = mysql_query("select * from wm_customers order by customers_id asc");
mysql_select_db('test_iimycatalog_final');
while($c = mysql_fetch_array($query)) {
  //echo $c['customers_id'];
  if (!mysql_num_rows(mysql_query("select * from customers where customers_firstname = '".$c['customers_firstname']."'"))) {
    echo $c['customers_id'] . "\n";
  }
}
*/
