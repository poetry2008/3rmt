<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html dir="ltr" lang="ja">
   <head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <title>NEW OA Init</title>
   </head>
   </body>
   <h1>NEW OA Init</h1>
   <?php
error_reporting(E_ALL^E_NOTICE^E_WARNING);
ini_set("display_errors",'On');
ini_set('include_path',ini_get('include_path').':'.'/home/.sites/28/site1/web/admin/i');
ini_set('include_path',ini_get('include_path').':'.'/home/.sites/28/site1/web/3rmtlib/oa');
$start = microtime(true);
   //无用数据 
   //delete from oa_item where group_id not in (select id from oa_group ) //删除非现有组的oa_item
   $language = 'japanese';
require_once 'includes/configure.php';
require_once (DIR_WS_FUNCTIONS . 'database.php');
require_once (DIR_WS_FUNCTIONS . 'general.php');
define('TABLE_ORDERS','orders');
define('TABLE_OA_GROUP', 'oa_group'); 
define('TABLE_OA_FORM', 'oa_form'); 
define('TABLE_OA_FORM_GROUP', 'oa_form_group'); 
define('TABLE_OA_ITEM', 'oa_item'); 
define('TABLE_OA_FORMVALUE', 'oa_formvalue'); 

require_once("oa/HM_Form.php");
require_once("oa/HM_Group.php");
require_once("oa/HM_Item_Checkbox.php");
require_once("oa/HM_Item_Autocalculate.php");
require_once("oa/HM_Item_Text.php");
require_once("oa/HM_Item_Specialbank.php");
require_once("oa/HM_Item_Date.php");
require_once("oa/HM_Item_Myname.php");

tep_db_connect() or die('Unable to connect to database server!');
$debug = true;
$payment = array(
                 "ゆうちょ銀行（郵便局）",
                 "銀行振込(買い取り)",
                 "ペイパル決済",
                 "銀行振込",
                 "コンビニ決済",
                 "クレジットカード決済",
                 "ポイント(買い取り)",
                 "来店支払い",
                 "支払いなし",);
$sql[] = "delete from oa_form where payment_romaji not in('".join("','",$payment)."')";//删除 romaji非这九个支付方法的form
$sql[] = "delete from oa_form where formtype not in (1,2,3)";
$sql[] = "delete from oa_form_group  where form_id not in (select id from oa_form )";//删除 非现有form的 oa_form_group 关联  
$sql[] = "delete from oa_form_group  where group_id not in (select id from oa_group )";//删除 非现有group的 oa_form_group 关联 
$sql[] = "delete from oa_item where group_id not in (select id from oa_group ) ";
$sql[] = "delete from oa_formvalue where 1";

if ($debug){
  foreach ($sql as $sigleSql){
    tep_db_query($sigleSql);
  }
}
//q_1_1  checkbox 備考の有無：     如果是 1 则_0 如果是null 或 0 不删 空值
//q_2_1  checkbox 在庫確認        如果是 1 则_0 如果是null 或 0 不删 空值
//q_3_2  date  入金確認:       
//q_4_2  checkbox 入金確認メール送信:        如果是 1 则_0 如果是null 或 0 不删 空值  
//q_5_2  date 発送  
//q_6_1  checkbox 残量入力(买)：   根据买卖有不同  如果是买的话  //q_6_1  checkbox 残量入力(买)：   根据买卖有不同  如果是买的话   如果是 1 则_0 如果是null 或 0 不删 空值
//q_7_2  checkbox 発送完了メール送信      如果是 1 则_0 如果是null 或 0 不删 空值  
//q_8_1  特殊处理 取引完了 如果有值，则标记结束 并且 将值记在orders里
//q_9_2  date 決算確認：
//q_10_1 checkbox 在庫確認        如果是 1 则_0 如果是null 或 0 不删 空值
//q_11_15 checkbox 新加
//q_11_3 ()  q_11_4 q_11_5 q_11_6 q_11_7  q_11_3() q_11_8 q_11_11 q_11_12  q_11_14  => 信用調査:  radio 
//q_17_1 myname  信用判定：
//q_12_1 キャラクターの有無 checkbox
//q_13_2 date 受领注意
//q_14_1 checkbox 受領メール送信
//q_15_2 checkbox 支払：
//q_15_3 q_15_4 q_15_5 
//q_15_7 text 支払: 受付番号
//q_16_1 无用
//q_16_2 checkbox 支払完了メール送信：  
$item_to_q = array(
                   "入金確認" ,
                   "備考の有無",
                   "振込先選択",
                   "支払",
                   "支払完了メール送信",
                   "発送",
                   "発送完了メール送信",
                   "入金確認メール送信",
                   "最終確認",
                   "受領 ※注意※",
                   "受領メール送信",
                   "受付番号",
                   "キャラクターの有無",
                   "決済確認",
                   "担当者（許可者）",
                   "初回or2回以上",
                   "残量入力（買取）",
                   "残量入力（販売）",
                   "信用判定",
                   "サイト入力",
                   "在庫確認",
                   "金額確認",
);
foreach($item_to_q as $key=>$item){
  $sql = 'select * from oa_item where trim(title) ="'.trim($item).'"';
  $res = tep_db_query($sql);
  $item_row = tep_db_fetch_array($res);
  if(!$res or !count($item_row)){
    echo "</br>";
    die('item [' .$item. '] ありません');
  }
  $new_data[$item_row['title']] = array('id'=>$item_row['id'],'method'=>$key);
}
echo "</br>";
echo "でタ　は　就绪　です";
echo "</br>";
//删除 现有的oa_formvalue 数据  delete from oa_formvalue where 1;

/*
  備考の有無：   →　　備考の有無   d
  キャラクターの有無：   →　　キャラクターの有無 d
  在庫確認：   →　　在庫確認 d
  決算確認：   →　　決済確認 d
  入金確認：   →　　入金確認 d
  入金確認メール送信   →　　入金確認メール送信  d
  受領 ※注意※：   →　　受領 ※注意※  d
  受領メール送信：   →　　受領メール送信 d
  信用判定：   →　　信用判定 d
  信用調査：   →　　信用調査 d
  支払：   →　　支払 d
  支払完了メール送信：   →　　支払完了メール送信  d
  発送:   →　　発送 d
  発送完了メール送信   →　　発送完了メール送信 d
  サイト入力　　→　　サイト入力 d
  残量入力   →　　誤差有無：（買取データ）   →　　残量入力（買取） d
  残量入力   →　　誤差有無：（販売データ）   →　　残量入力（販売） d
  最終確認:   →　　取引完了 d
  注意点
  スクリプトを作る際本サイトとテストサイトではDBのIDが違う
  つまり、IDを考慮に入れてはいけない
*/
//q_1_2  无用
//q_2_2  无用
//q_3_1  无用
//q_4_1  暂时未定
//q_4_3  无用
//q_5_1  无用
//q_7_1  无用
//q_9_1  无用 
//q_17_2 无用
//q_12_2 无用
//q_13_1 无用
//q_14_2 无用


//处理 自动计算  orders_questions_products 把这个值 放到对应的位置 去

//选择所有现有数据 从 orders_questions 表时 一条一条循环
$sql  = 'select oq.* ,o.* from orders_questions oq ,orders o  where o.orders_id = oq.orders_id ';
//$sql.=' and o.orders_id = "20110726-08162806"';


$res =tep_db_query($sql);
$i = 0;
while($orderq = tep_db_fetch_array($res)){
//while($orderq = mysql_fetch_array($res)){

  $i++;
  //取得当前订单的类型
  $order_id = $orderq['orders_id'];
  $formtype = tep_check_order_type($order_id);
  $payment_romaji = tep_get_payment_code_by_order_id($order_id); 
  $oa_form_sql = "select * from ".TABLE_OA_FORM." where formtype = '".$formtype."' and payment_romaji = '".$payment_romaji."'";
  $form = tep_db_fetch_object(tep_db_query($oa_form_sql), "HM_Form");
  foreach ($form->groups as $group){
    foreach ($group->items as $item){
      call_user_func('method_'.$new_data[$item->title]['method'],$orderq,$form->id,$group->id,$item->id);
      method_8($orderq,$form->id,$group->id,$item->id);//处理是否完成订单
    }
  }
  //取得订单的form
  //取得订单的group
  //每个group再取得对应的 item 根据每个item 的名 找到对应的 字段 然后进行给值


}
echo "</br>";
echo "問題完了(".$i.")</br>";
function oavalue($value,$form_id,$group_id,$item_id,$oid)
{
  if($value == NULL)
    return 0;
  return tep_db_query("
INSERT INTO `oa_formvalue` 
(`id`, `orders_id`, `form_id`, `item_id`, `group_id`, `value`) 
VALUES (NULL,'".$oid."',".$form_id.",".$item_id.",".$group_id.",'".$value."')");
}
function method_0($order,$form_id,$group_id,$item_id){
  //  echo 'q_3_2  date  入金確認:       ';
  
  if ($order['q_3_1'] != 1) {
    return ''; 
  }
  
  $order_status_query = tep_db_query("select * from orders_status_history where orders_id = '".$order['orders_id']."' and orders_status_id = '9' order by date_added desc limit 1"); 
  $order_status_res =  tep_db_fetch_array($order_status_query);
  if ($order_status_res) {
    $value = date('Y-m-d h:i', strtotime($order_status_res['date_added']));   
  } else {
    $value = oa_date($order['q_3_2']);
  }

  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//q_1_1  checkbox 備考の有無：     如果是 1 则_0 如果是null 或 0 不删 空值
function method_1($order,$form_id,$group_id,$item_id){
  $value =$order['q_1_1'];
  if($value!=NULL){
  $value = '_0';
  }
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//q_15_3 q_15_4 q_15_5                    "振込先選択",
function method_2($order,$form_id,$group_id,$item_id){

  $value ='';
  $value .= oa_checkbox($order['q_15_3'],0);
  $value .= oa_checkbox($order['q_15_4'],1);  
  $value .= oa_checkbox($order['q_15_5'],2);
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//q_15_2 checkbox 支払：
function method_3($order,$form_id,$group_id,$item_id){
  if ($order['q_15_1'] != 1) {
    return ''; 
  }
  
  $order_status_query = tep_db_query("select * from orders_status_history where orders_id = '".$order['orders_id']."' and orders_status_id = '5' order by date_added desc limit 1"); 
  $order_status_res =  tep_db_fetch_array($order_status_query);
  if ($order_status_res) {
    $value = date('Y-m-d h:i', strtotime($order_status_res['date_added']));   
  } else {
    $value = oa_date($order['q_15_2']);
  } 
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
////q_16_2 checkbox 支払完了メール送信：  
function method_4($order,$form_id,$group_id,$item_id){
  $value = oa_checkbox($order['q_16_2']);
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//q_5_2  date 発送  
function method_5($order,$form_id,$group_id,$item_id){
  if ($order['q_5_1'] != 1) {
    return ''; 
  }
  $order_status_query = tep_db_query("select * from orders_status_history where orders_id = '".$order['orders_id']."' and orders_status_id = '2' order by date_added desc limit 1"); 
  $order_status_res =  tep_db_fetch_array($order_status_query);
  if ($order_status_res) {
    $value = date('Y-m-d h:i', strtotime($order_status_res['date_added']));   
  } else {
    $value = oa_date($order['q_5_2']);
  } 
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//q_7_2  checkbox 発送完了メール送信      如果是 1 则_0 如果是null 或 0 不删 空值  
function method_6($order,$form_id,$group_id,$item_id){
  $value = oa_checkbox($order['q_7_2']);
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//q_4_2  checkbox 入金確認メール送信:        如果是 1 则_0 如果是null 或 0 不删 空值  
function method_7($order,$form_id,$group_id,$item_id){

  $value = oa_checkbox($order['q_4_2']);
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);

}
//q_8_1                   "最終確認",
function method_8($order,$form_id,$group_id,$item_id){
  if(trim($order['q_8_1'])!=''){
    $sql = 'update orders set flag_qaf=1 ,end_user="'.$order['q_8_1'].'" where orders_id = "'.$order['orders_id'].'"';    
    tep_db_query($sql);
  }
    
  if ($order['payment_method'] != '銀行振込(買い取り)') {
    
    $order_status_query = tep_db_query("select * from orders_status_history where orders_id = '".$order['orders_id']."' and orders_status_id = '9' order by date_added desc limit 1"); 
    $order_status_res =  tep_db_fetch_array($order_status_query);
    
    $whether_update_payment = false;
    if ($order_status_res) {
      tep_db_query("update `orders` set `confirm_payment_time` = '".$order_status_res['date_added']."' where orders_id = '".$order['orders_id']."'"); 
    } else {
      $whether_update_payment = true;
    }
    if ($whether_update_payment) {
      if ($order['orders_questions_type'] == '2') {
        $pay_time = $order['q_4_3'] && $order['q_4_3'] != '0000-00-00' && $order['q_4_2'] ? $order['q_4_3'] : false;
      } else {
        $pay_time = $order['q_3_2'] && $order['q_3_1'] && $order['q_3_4'] ? $order['q_3_2'] : false;
      }
      if ($pay_time) {
        $confirm_payment_time = date('Y-m-d H:i:s', strtotime($pay_time)); 
        tep_db_query("update `orders` set `confirm_payment_time` = '".$pay_time."' where orders_id = '".$order['orders_id']."'"); 
      }
    }
  }
}
//q_13_2 date 受领注意
function method_9($order,$form_id,$group_id,$item_id){
  if ($order['q_13_1'] != 1) {
    return ''; 
  }
  $order_status_query = tep_db_query("select * from orders_status_history where orders_id = '".$order['orders_id']."' and orders_status_id = '13' order by date_added desc limit 1"); 
  $order_status_res =  tep_db_fetch_array($order_status_query);
  if ($order_status_res) {
    $value = date('Y-m-d h:i', strtotime($order_status_res['date_added']));   
  } else {
    $value = oa_date($order['q_13_2']);
  } 
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//q_14_1 checkbox 受領メール送信
function method_10($order,$form_id,$group_id,$item_id){
  $value = oa_checkbox($order['q_14_1']);
  if ($value != NULL){
    $value = '_0';
  }

  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//q_15_7 text 支払: 受付番号
function method_11($order,$form_id,$group_id,$item_id){
  $value = oa_text($order['q_15_7']);
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//q_12_1 キャラクターの有無 checkbox
function method_12($order,$form_id,$group_id,$item_id){
  $value = $order['q_12_1'];
  if($value !=NULL){
    $value = '_0';
      }
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//q_9_2  date 決算確認：
function method_13($order,$form_id,$group_id,$item_id){
  /* 
  $order_status_query = tep_db_query("select * from orders_status_history where orders_id = '".$order['orders_id']."' and orders_status_id = '9' order by date_added desc limit 1"); 
  $order_status_res =  tep_db_fetch_array($order_status_query);
  if ($order_status_res) {
    $value = date('Y-m-d h:i', strtotime($order_status_res['date_added']));   
  } else {
    $value = oa_date($order['q_9_2']);
  } 
  */ 
  if ($order['q_9_1'] != 1) {
    return ''; 
  }
  $value = oa_date($order['q_9_2']);
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//q_17_1 myname  信用判定：
function method_14($order,$form_id,$group_id,$item_id){
  $value = oa_text($order['q_17_1']);
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}

//q_11_3 ()  q_11_4 q_11_5 q_11_6 q_11_7  q_11_3() q_11_8 q_11_11 q_11_12  q_11_14  => 信用調査:  radio 
function method_15($order,$form_id,$group_id,$item_id){
  $value = $order['q_11_3'].'|';
  $value .= oa_radio($order['q_11_4'],'0_0');
  $value .= oa_radio($order['q_11_5'],'0_1');
  $value .= oa_radio($order['q_11_6'],'0_2');
  $value .= oa_radio($order['q_11_7'],'0_3');
  $value .= oa_radio($order['q_11_8'],'1_0');
  $value .= oa_radio($order['q_11_11'],'1_1');
  $value .= oa_radio($order['q_11_12'],'1_2');
  $value .= oa_radio($order['q_11_14'],'1_3');
  //  echo $order['orders_id'];
  //  echo "\n";
  //  echo $value;
  //  echo "\n";

  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);

}
//q_6_1  checkbox 残量入力(买)：   根据买卖有不同  如果是买的话  

function method_16($order,$form_id,$group_id,$item_id){
  //  $value = oa_checkbox($order['q_6_1'],'0','0');
  $value = $order['q_6_1'];
  if ($value != NULL){
    $value = '_0';
  }
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//q_6_1  checkbox 残量入力(买)：   根据买卖有不同  如果是买的话   如果是 1 则_0 如果是null 或 0 不删 空值
function method_17($order,$form_id,$group_id,$item_id){
  //  $value = oa_checkbox($order['q_6_1'],'0','0');
  $value = $order['q_6_1'];
  if ($value != NULL){
    $value = '_0';
  }
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}

//q_17_2                   "信用判定"
function method_18($order,$form_id,$group_id,$item_id){
  $value = oa_checkbox($order['q_17_2']);
  //  $value = '_0';
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}
//                   "サイト入力",
function method_19($order,$form_id,$group_id,$item_id){

  //如果 orders_questions_products 表里没有 则不用操作
  $sql = 'select * from orders_questions_products where orders_id = "'.$order['orders_id'].'" and checked = 1';
  $res = tep_db_query($sql);

  if (!$res){
    return 0;
  }else {
    while( $row = tep_db_fetch_array($res)){
      $value ='';
      $sql = 'select products_id from products where relate_products_id ='.$row['products_id'].' limit 1';
      $relate = tep_db_fetch_array(tep_db_query($sql));
      $sql2 = 'select products_quantity qt from orders_products where products_id = "'.$relate['products_id'].'" and orders_id = "'.$order['orders_id'].'"';
      $quality = tep_db_fetch_array(tep_db_query($sql2));
      $qt = (int)$quality['qt'];
      $offset = (int)$row['offset'];
      $real = $qt-$offset;
      
      $value .= $relate['products_id'].'|'.(string)$real.'|'.$row['products_id'].'_';
      //      echo $value."|||",$form_id."|||",$group_id."|||",$item_id."|||",$order['orders_id'];
      //      echo "\n";
      oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);

    }
  }


}
//                   "在庫確認");//q_10_1 checkbox 在庫確認        如果是 1 则_0 如果是null 或 0 不删 空值
function method_20($order,$form_id,$group_id,$item_id){

  $value = $order['q_2_1'];
      if ($value != NULL){
        $value = '_0';
      }
      oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
      /*
  if($order['payment_method']=="銀行振込(買い取り)" or $order['payment_method']=="銀行振込")
    {
      $value = oa_checkbox($order['q_2_1'],'0','0');
      oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
    }else{
    $value = oa_checkbox($order['q_10_1'],'0','0');
      oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
  }
      */

}
//q_11_15  金額確認
function method_21($order,$form_id,$group_id,$item_id){
  $value = oa_checkbox($order['q_11_15']);
  oavalue($value,$form_id,$group_id,$item_id,$order['orders_id']);
}




function oa_date($value)
{
  if (strtotime($value)){
    //插入0
    return  date('Y/m/d h:i',strtotime($value));
  }
  return '';

}
function oa_text($value)
{
  return trim($value);
}
function oa_checkbox($value,$default='0',$flag = '1')
{
  if($value == NULL){
    return $value;
  }
  if( $value == $flag ){
    return '_'.$default;
  }
  return '';

}
function oa_radio($value,$default=0){
  if( $value ==1 ){
    return $default.'|';
  }
  return '';
  
}

$end = microtime(true);
echo ($end-$start).'<br />';//显示执行switch所用时间
echo 'pre record' .($end-$start)/$i;
?>
</body>
</html>
