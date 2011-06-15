<?php
require('includes/application_top.php');
if(isset($_GET['action'])){
  switch ($_GET['action']){
  case 'updateoaorder':
    $id = $_GET['id'];
    $order = substr($_GET['order'],1,1);
    $result = tep_db_query("update `".TABLE_OA_FORM_GROUP."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    //    var_dump($order);
  case 'updateitemorder':     
    $id = $_GET['id'];
    $order = substr($_GET['order'],1,1);
    $result = tep_db_query("update `".TABLE_OA_ITEM."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    var_dump("update `".TABLE_OA_ITEM."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
  }
}
