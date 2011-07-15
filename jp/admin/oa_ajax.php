<?php
require('includes/application_top.php');
if(isset($_GET['action'])){
  switch ($_GET['action']){
  case 'updateoaorder':
    $id = $_GET['id'];
    $order = substr($_GET['order'],1);
    $result = tep_db_query("update `".TABLE_OA_FORM_GROUP."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    break;
    //    var_dump($order);
  case 'updategrouporder':     
    $id = $_GET['id'];
    $order = substr($_GET['order'],1);
    $result = tep_db_query("update `".TABLE_OA_GROUP."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    //    var_dump("update `".TABLE_OA_ITEM."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    break;

  case 'updateitemorder':     
    $id = $_GET['id'];
    $order = substr($_GET['order'],1);
    $result = tep_db_query("update `".TABLE_OA_ITEM."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    //    var_dump("update `".TABLE_OA_ITEM."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    break;
  case 'getTime':
    echo date('Y/m/d H-i' ,time());
    break;
  case 'finish':
    $id = $_GET['oID'];
    $user_info = tep_get_user_info($ocertify->auth_user);
    $value =$user_info['name'];
    $result = tep_db_query("update `".TABLE_ORDERS."` set `end_user` = '".$value."', `flag_qaf` = ".'1'." where orders_id = '".$id."'");  
    break;
  }
}
