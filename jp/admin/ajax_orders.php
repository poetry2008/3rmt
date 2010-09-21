<?php
require('includes/application_top.php');

if ($_POST['orders_id'] && $_POST['orders_comment']) {
  tep_db_perform('orders', array('orders_comment' => $_POST['orders_comment']), 'update', "orders_id='".$_POST['orders_id']."'");
  echo $_POST['orders_comment'];
} else if ($_GET['orders_id'] && isset($_GET['orders_important_flag'])) {
  tep_db_perform('orders', array('orders_important_flag' => $_GET['orders_important_flag']), 'update', "orders_id='".$_GET['orders_id']."'");

} else if ($_GET['orders_id'] && isset($_GET['orders_care_flag'])) {
  tep_db_perform('orders', array('orders_care_flag' => $_GET['orders_care_flag']), 'update', "orders_id='".$_GET['orders_id']."'");

} else if ($_GET['orders_id'] && isset($_GET['orders_wait_flag'])) {
  tep_db_perform('orders', array('orders_wait_flag' => $_GET['orders_wait_flag']), 'update', "orders_id='".$_GET['orders_id']."'");
}