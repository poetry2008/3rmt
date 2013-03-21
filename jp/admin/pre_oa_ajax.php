<?php
require('includes/application_top.php');
if(isset($_GET['action'])){
  switch ($_GET['action']){
/* -----------------------------------------------------
   case 'updateoaorder' 更新oa组在表单里的序号  
   case 'updategrouporder' 更新oa组的序号   
   case 'updateitemorder' 更新oa元素的序号   
   case 'getTime' 获得当前时间   
   case 'finish' 更新预约订单完成标识 
------------------------------------------------------*/
  case 'updateoaorder':
    $id = $_GET['id'];
    $order = substr($_GET['order'],1);
    $result = tep_db_query("update `".TABLE_OA_FORM_GROUP."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    break;
  case 'updategrouporder':     
    $id = $_GET['id'];
    $order = substr($_GET['order'],1);
    $result = tep_db_query("update `".TABLE_OA_GROUP."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    break;

  case 'updateitemorder':     
    $id = $_GET['id'];
    $order = substr($_GET['order'],1);
    $result = tep_db_query("update `".TABLE_OA_ITEM."` set `ordernumber` = '".tep_db_prepare_input($order)."' where id = '".$id."'");  
    break;
  case 'getTime':
    echo date('Y/m/d H-i' ,time());
    break;
  case 'finish':

    $id = $_GET['oID'];
    if(!$id){
      $ids = $_POST['oID'];
    }
    if (strpos($ids,'_')){
      $m = true;
      $oids = explode('_',$ids);
    }
    $user_info = tep_get_user_info($ocertify->auth_user);
    $value =$user_info['name'];
    if($m){
      foreach ($oids as $id){
        if(trim($id)!=''){
        $result = tep_db_query("update `".TABLE_PREORDERS."` set `end_user` = '".$value."', `flag_qaf` = ".'1'." where orders_id = '".$id."'");  
        if($_POST['finish']){
          $messageStack->add_session('注文ID'.$id . 'の成功：取り引きが完了致しました', 'success');
        }

        }
      }
    }else {
      $result = tep_db_query("update `".TABLE_PREORDERS."` set `end_user` = '".$value."', `flag_qaf` = ".'1'." where orders_id = '".$id."'");  
    }

    break;
  }
}
