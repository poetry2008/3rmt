<?php

function tep_get_preorders_status_name($orders_status_id, $language_id = '') {
  global $languages_id;

  if (!$language_id) $language_id = $languages_id;
  $orders_status_query = tep_db_query("select orders_status_name from " . TABLE_PREORDERS_STATUS . " where orders_status_id = '" . $orders_status_id . "' and language_id = '" . $language_id . "'");
  $orders_status = tep_db_fetch_array($orders_status_query);

  return $orders_status['orders_status_name'];
}

function tep_get_computers_names_by_preorders_id($orders_id)
{
  $names = array();
  $o2c_query = tep_db_query("select * from ".TABLE_PREORDERS_TO_COMPUTERS." o2c, ".TABLE_COMPUTERS." c where c.computers_id=o2c.computers_id and o2c.orders_id = '".$orders_id."' order by sort_order asc");
  while($o = tep_db_fetch_array($o2c_query)) {
    $names[] = $o['computers_name'];
  }
  return $names;
}

function tep_get_computers_by_preorders_id($oid)
{
  $c = array();
  $o2c_query = tep_db_query("select * from ".TABLE_PREORDERS_TO_COMPUTERS." where orders_id = '".$oid."'");
  while ($o2c = tep_db_fetch_array($o2c_query)) {
    $c[] = $o2c['computers_id'];
  }
  return $c;
}

function tep_show_preorders_products_info($orders_id) {
  $str = '';

  $orders_info_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$orders_id."'"); 
  $orders = tep_db_fetch_array($orders_info_raw);
  
  if (!$orders) {
    return $str; 
  }

  $str .= '<table border="0" cellpadding="0" cellspacing="0">';

  $str .= '<tr><td class="mian" align="left" colspan="2">';
  if ($orders['orders_inputed_flag']) {
    $str .= '<font color="red"><b>入力済み</b></font>';
  }
  
  $str .= '</td></tr><tr><td class="mian" align="left"colspan="2">';
  if ($orders['orders_care_flag']) {
    $str .= '<font color="red"><b>取扱注意</b></font>';
  }
  $str .= '</td></tr><tr><td class="mian" align="left"colspan="2">';
  if ($orders['orders_comment']) {
    $str .= '<font color="blue"><b>メモ有り</b></font>';
  }

  $str .= '</td></tr>';
  $str .= '<tr><td colspan="2">&nbsp;</td></tr>';
  $str .= '<tr><td class="main" width="60"><b>支払方法：</b></td><td class="main" style="color:darkred;"><b>'.$orders['payment_method'].'</b></td></tr>';
  if ($orders['payment_method'] != '銀行振込(買い取り)') {
    if ($orders['confirm_payment_time'] != '0000-00-00 00:00:00') {
      $time_str = date('Y年n月j日', strtotime($orders['confirm_payment_time'])); 
    } else {
      $time_str = '入金まだ'; 
    }
    $str .= '<tr><td class="main"><b>入金日：</b></td><td class="main" style="color:red;"><b>'.$time_str.'</b></td></tr>';
  }
  $str .= '<tr><td colspan="2">&nbsp;</td></tr>';
  $str .= '<tr><td class="main"><b>オプション：</b></td><td class="main" style="color:blue;"><b>'.$orders['torihiki_houhou'].'</b></td></tr>';

  $orders_products_query = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." op,".TABLE_PRODUCTS." p where p.products_id = op.products_id and op.orders_id = '".$orders['orders_id']."'");
  $autocalculate_arr = array();
  $autocalculate_sql = "select oaf.value as arr_str from ".TABLE_PREORDERS_OA_FORMVALUE." oaf,".
    TABLE_OA_ITEM." oai 
    where oaf.item_id = oai.id 
    and oai.type = 'autocalculate' 
    and oaf.orders_id = '".$orders['orders_id']."' 
    order by oaf.id asc limit 1 ";
  $autocalculate_query = tep_db_query($autocalculate_sql);
  $autocalculate_row = tep_db_fetch_array($autocalculate_query);
  $arr_checked = explode('_',$autocalculate_row['arr_str']);
  $autocalculate_arr = array();
  foreach($arr_checked as $key=>$value){
    $temp_arr = explode('|',$value);
    if($temp_arr[0] != 0 && $temp_arr[3] != 0){
      $autocalculate_arr[] = array($temp_arr[0],$temp_arr[3]);
    }
  }
  $tmpArr = array();
  while ($p = tep_db_fetch_array($orders_products_query)) {
    if(in_array($p,$tmpArr)){
      continue;
    }
    $tmpArr[] = $p ;
    $products_attributes_query = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id='".$p['orders_products_id']."'");
    if(in_array(array($p['products_id'],$p['orders_products_id']),$autocalculate_arr)&&
        !empty($autocalculate_arr)){
      $str .= '<tr><td class="main"><b>商品：</b><font color="red">「入」</font></td><td class="main">'.$p['products_name'].'</td></tr>';
    }else{
      $str .= '<tr><td class="main"><b>商品：</b><font color="red">「未」</font></td><td class="main">'.$p['products_name'].'</td></tr>';
    }
    $str .= '<tr><td class="main"><b>個数：</b></td><td class="main">'.$p['products_quantity'].'個'.tep_get_full_count2($p['products_quantity'], $p['products_id'], $p['products_rate']).'</td></tr>';
    while($pa = tep_db_fetch_array($products_attributes_query)){
      $str .= '<tr><td class="main"><b>'.$pa['products_options'].'：</b></td><td class="main">'.$pa['products_options_values'].'</td></tr>';
    }
    $str .= '<tr><td class="main"><b>キャラ名：</b></td><td style="font-size:20px;color:#407416;"><b>'.$p['products_character'].'</b></td></tr>';
    $names = tep_get_computers_names_by_orders_id($orders['orders_id']);
    if ($names) {
      $str .= '<tr><td class="main"><b>PC：</b></td><td class="main">'.implode('&nbsp;,&nbsp;', $names).'</td></tr>';
    }
    $str .= '<tr><td class="main"></td><td class="main"></td></tr>';
    $i++;
  }
  $str .= '</table>';
  $str=str_replace("\n","",$str);
  $str=str_replace("\r","",$str);
  return $str; 
}

function tep_preorder_remove_attributes($order_id, $restock = false) {
  if ($restock == 'on') {
    $orderproduct_query = tep_db_query("select orders_products_id, products_quantity from " . TABLE_PREORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($order_id) . "'");
    while ($o_product = tep_db_fetch_array($orderproduct_query)) {
      $opID = $o_product['orders_products_id'];
      $zaiko = $o_product['products_quantity'];

      $order_attributes_query = tep_db_query("select attributes_id from " .  TABLE_PREORDERS_PRODUCTS_ATTRIBUTES . " where orders_products_id = '" . $opID . "'");
      $order_attributes_result = tep_db_fetch_array($order_attributes_query);
      $att_id = $order_attributes_result['attributes_id'];
    }
  }
}

function tep_preorder_remove_order($order_id, $restock = false) {
  if ($restock == 'on') {
    $order_query = tep_db_query("select products_id, products_quantity from " .  TABLE_PREORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($order_id) . "'");
    while ($order = tep_db_fetch_array($order_query)) {
      //tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = products_real_quantity + " . $order['products_quantity'] . ", products_ordered = products_ordered - " . $order['products_quantity'] . " where products_id = '" . $order['products_id'] . "'");
    }
  }

  tep_db_query("delete from " . TABLE_PREORDERS . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_PREORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_PREORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_PREORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_PREORDERS_TOTAL . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_PREORDERS_TO_COMPUTERS . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from preorders_products_download where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from ".TABLE_PREORDERS_OA_FORMVALUE." where orders_id = '".tep_db_input($order_id)."'");
}

function preorders_updated($orders_id) {
  tep_db_query("update ".TABLE_PREORDERS." set language_id = ( select language_id from ".TABLE_PREORDERS_STATUS." where preorders_status.orders_status_id=preorders.orders_status ) where orders_id='".$orders_id."'");
  
  tep_db_query("update ".TABLE_PREORDERS." set finished = ( select finished from ".TABLE_PREORDERS_STATUS." where preorders_status.orders_status_id=preorders.orders_status ) where orders_id='".$orders_id."'");
  
  tep_db_query("update ".TABLE_PREORDERS." set orders_status_name = ( select orders_status_name from ".TABLE_PREORDERS_STATUS." where preorders_status.orders_status_id=preorders.orders_status ) where orders_id='".$orders_id."'");
  
  tep_db_query("update ".TABLE_PREORDERS." set orders_status_image = ( select orders_status_image from ".TABLE_PREORDERS_STATUS." where preorders_status.orders_status_id=preorders.orders_status ) where orders_id='".$orders_id."'");
  
  tep_db_query("update ".TABLE_PREORDERS_PRODUCTS." set torihiki_date = ( select torihiki_date from ".TABLE_PREORDERS." where preorders.orders_id=preorders_products.orders_id ) where orders_id='".$orders_id."'");
}

function preorders_wait_flag($orders_id) {
  $orders_query = tep_db_query("select * from " . TABLE_PREORDERS . " where orders_id = '".$orders_id."'");
  $orders       = tep_db_fetch_array($orders_query);
  if ($orders['orders_wait_flag']) {
    $orders_status_query = tep_db_query("select * from " . TABLE_PREORDERS_STATUS . " where orders_status_id='".$orders['orders_status']."'");
    $orders_status       = tep_db_fetch_array($orders_status_query);
    if ($orders_status['finished']) {
      tep_db_query("update ".TABLE_PREORDERS." set orders_wait_flag = '0' where orders_id='".$orders_id."'");
    }
  }
}

function tep_get_pre_ot_total_by_orders_id($orders_id, $single = false) {
  if ($single) {
    global $currencies; 
  }
  $query = tep_db_query("select value from " . TABLE_PREORDERS_TOTAL . " where class='ot_total' and orders_id='".$orders_id."'");
  $result = tep_db_fetch_array($query);
  if($result['value'] > 0){
    if ($single) {
      return "<b>".$currencies->format(abs($result['value']))."</b>";
    } else {
      return "<b>".abs($result['value'])."円</b>";
    }
  }else{
    if ($single) {
      return "<b><font color='ff0000'>".$currencies->format(abs($result['value']))."</font></b>";
    } else {
      return "<b><font color='ff0000'>".abs($result['value'])."円</font></b>";
    }
  }
}

function tep_get_pre_site_name_by_order_id($id){
  $order_query = tep_db_query("
      select s.name
      from " . TABLE_PREORDERS . " o, ".TABLE_SITES." s
      where o.orders_id = '".$id."'
      and s.id = o.site_id
      ");
  $order = tep_db_fetch_array($order_query);
  return isset($order['name'])?$order['name']:'';
}

function tep_get_pre_site_id_by_orders_id($orders_id) {
  $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$orders_id."'"));
  if ($order) {
    return $order['site_id'];
  } else {
    return false;
  }
}

function preorders_a($orders_id, $allorders = null, $site_id = 0)
{
  static $products;
  $str = "";
  if ($allorders && $products === null) {
    foreach($allorders as $o) {
      $allorders_ids[] = $o['orders_id'];
    }
    $sql = "select pd.products_name,p.products_attention_5,p.products_id from ".TABLE_PREORDERS_PRODUCTS." op, ".TABLE_PRODUCTS_DESCRIPTION." pd,".TABLE_PRODUCTS." p WHERE op.products_id=pd.products_id and p.products_id=pd.products_id and `orders_id` IN ('".join("','", $allorders_ids)."') and pd.site_id = '".$site_id."'";
    $orders_products_query = tep_db_query($sql);
    while ($product = tep_db_fetch_array($orders_products_query)) {
      $products[$product['orders_id']][] = $product;
    }
  }
  if (isset($products[$orders_id]) && $products[$orders_id]) {
    foreach($products[$orders_id] as $p){
      $str .= $p['products_name'] . " 当社のキャラクター名：\n";
      $str .= $p['products_attention_5'] . "\n";
    }
  } else {
    $sql = "select * from `".TABLE_PREORDERS_PRODUCTS."` WHERE `orders_id`='".$orders_id."'";
    $orders_products_query = tep_db_query($sql);
    while ($orders_products = tep_db_fetch_array($orders_products_query)){
      $sql = "select pd.products_name,p.products_attention_5,p.products_id from `".TABLE_PRODUCTS_DESCRIPTION."` pd,".TABLE_PRODUCTS." p WHERE p.products_id=pd.products_id and p.`products_id`='".$orders_products['products_id']."' and pd.site_id = '".$site_id."'";
      $products_description = tep_db_fetch_array(tep_db_query($sql));
      if ($products_description['products_attention_5']) {
        $str .= $orders_products['products_name']." 当社のキャラクター名：\n";
        $str .= $products_description['products_attention_5'] . "\n";
      }
    }
  }
  return $str;
}

function prenew_calc_handle_fee($payment_name, $products_total, $oID)
{
  $oid_query = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$oID."'"); 
  $oid_res = tep_db_fetch_array($oid_query);
  if ($oid_res) {
    $site_id = $oid_res['site_id']; 
  } else {
    $site_id = 0; 
  } 

  if ($products_total == 0) {
    return 0; 
  }
  $handle_fee = 0; 
  if ($payment_name == '銀行振込(買い取り)') {
    $pay_cost_query = tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_BUYING_COST' and (site_id = 0 or site_id = ".$site_id.") order by site_id DESC limit 1"); 
    $pay_cost_res = tep_db_fetch_array($pay_cost_query); 

    $handle_fee = calc_fee_final($pay_cost_res['configuration_value'], $products_total); 
  } else if ($payment_name == 'コンビニ決済') {
    $pay_cost_query = tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_CONVENIENCE_STORE_COST' and (site_id = 0 or site_id = ".$site_id.") order by site_id DESC limit 1"); 
    $pay_cost_res = tep_db_fetch_array($pay_cost_query); 

    $handle_fee = calc_fee_final($pay_cost_res['configuration_value'], $products_total); 
  } else if ($payment_name == '銀行振込') {
    $pay_cost_query = tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_MONEYORDER_COST' and (site_id = 0 or site_id = ".$site_id.") order by site_id DESC limit 1"); 
    $pay_cost_res = tep_db_fetch_array($pay_cost_query); 

    $handle_fee = calc_fee_final($pay_cost_res['configuration_value'], $products_total); 
  } else if ($payment_name == 'ゆうちょ銀行（郵便局）') {
    $pay_cost_query = tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_POSTALMONEYORDER_COST' and (site_id = 0 or site_id = ".$site_id.") order by site_id DESC limit 1"); 
    $pay_cost_res = tep_db_fetch_array($pay_cost_query); 

    $handle_fee = calc_fee_final($pay_cost_res['configuration_value'], $products_total); 
  } else if ($payment_name == 'クレジットカード決済') {
    $pay_cost_query = tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key = 'MODULE_PAYMENT_TELECOM_COST' and (site_id = 0 or site_id = ".$site_id.") order by site_id DESC limit 1"); 
    $pay_cost_res = tep_db_fetch_array($pay_cost_query); 
    $handle_fee = calc_fee_final($pay_cost_res['configuration_value'], $products_total); 
  } else {
    return 0; 
  }
  return $handle_fee;
}

function tep_get_pre_product_by_op_id($orders_products_id,$type=''){
  if($type=='pid'){
    $sql = "select p.products_price as price from ".
      TABLE_PREORDERS_PRODUCTS." op,"
      .TABLE_PRODUCTS." p  
      where p.products_id ='".$orders_products_id."' 
      limit 1";
  }else{
    $sql = "select p.products_price as price from ".
      TABLE_PREORDERS_PRODUCTS." op,"
      .TABLE_PRODUCTS." p  
      where op.orders_products_id ='".$orders_products_id."' 
      and op.products_id = p.products_id limit 1";
  }
  $res = tep_db_query($sql);
  if($row = tep_db_fetch_array($res)){
    return $row['price'];
  }else{
    return false;
  }
}

function tep_check_pre_order_type($oID)
{
  $sql = "  SELECT avg( products_bflag ) bflag FROM preorders_products op, products p  WHERE 1 AND p.products_id = op.products_id AND op.orders_id = '".$oID."'";

  $avg  = tep_db_fetch_array(tep_db_query($sql));
  $avg = $avg['bflag'];

  if($avg == 0){
    return 1;
  }
  if($avg == 1){
    return 2;
  }
  return 3;

}

function tep_get_pre_payment_code_by_order_id($oID)
{
  $orders_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$oID."'");
  $orders_res = tep_db_fetch_array($orders_raw);
  return $orders_res['payment_method'];
}

function   tep_pre_order_status_change($oID,$status){
  require_once("pre_oa/HM_Form.php");
  require_once("pre_oa/HM_Group.php");
  require_once("pre_oa/HM_Item_Checkbox.php");
  require_once("pre_oa/HM_Item_Autocalculate.php");
  require_once("pre_oa/HM_Item_Text.php");
  require_once("pre_oa/HM_Item_Specialbank.php");
  require_once("pre_oa/HM_Item_Date.php");
  require_once("pre_oa/HM_Item_Myname.php");
  $order_id = $oID;
  //$formtype = tep_check_pre_order_type($order_id);
  $formtype = 4;
  $payment_romaji = tep_get_pre_payment_code_by_order_id($order_id); 
  $oa_form_sql = "select * from ".TABLE_OA_FORM." where formtype = '".$formtype."' and payment_romaji = '".$payment_romaji."'";
  
  if ($status == '9') {
    tep_db_query("update `".TABLE_PREORDERS."` set `confirm_payment_time` = '".date('Y-m-d H:i:s', time())."' where `orders_id` = '".$oID."'");
  }
  
  $form = tep_db_fetch_object(tep_db_query($oa_form_sql), "HM_Form");
  //如果存在，把每个元素找出来，看是否有自动更新
  if($form){
    $form->loadOrderValue($order_id);
    foreach ($form->groups as $group){
      foreach ($group->items as $item){
        if ($item->instance->status == $status){
          $item->instance->statusChange($order_id,$form->id,$group->id,$item->id);
          continue;
        }
      }
    }
  }
}

function tep_get_pre_orders_products_string($orders, $single = false) {
  $str = '';


  $str .= '<table border="0" cellpadding="0" cellspacing="0">';

  if (ORDER_INFO_TRANS_NOTICE == 'true') {
    if ($orders['orders_care_flag']) {
      $str .= '<tr>'; 
      $str .= '<td class="main" colspan="2"><font color="red">';
      $str .= RIGHT_ORDER_INFO_TRANS_NOTICE; 
      $str .= '</font></td>'; 
      $str .= '</tr>'; 
    }
  }
  
  if (ORDER_INFO_TRANS_WAIT == 'true') {
    if ($orders['orders_wait_flag']) {
      $str .= '<tr>'; 
      $str .= '<td class="main" colspan="2"><font color="red">';
      $str .= RIGHT_ORDER_INFO_TRANS_WAIT; 
      $str .= '</font></td>'; 
      $str .= '</tr>'; 
    } 
  }
  
  if (ORDER_INFO_INPUT_FINISH == 'true') {
    if ($orders['orders_inputed_flag']) {
      $str .= '<tr>'; 
      $str .= '<td class="main" colspan="2"><font color="red">';
      $str .= RIGHT_ORDER_INFO_INPUT_FINISH; 
      $str .= '</font></td>'; 
      $str .= '</tr>'; 
    } 
  }
  //$str .= '<tr><td colspan="2">&nbsp;</td></tr>';
  $str .= '<tr><td class="main" width="150"><b>支払方法：</b></td><td class="main" style="color:darkred;"><b>'.$orders['payment_method'].'</b></td></tr>';
  if ($orders['payment_method'] != '銀行振込(買い取り)') {
    //$str .= '<tr><td class="main"><b>入金日：</b></td><td class="main" style="color:red;"><b>'.($pay_time?date('m月d日',strtotime($pay_time)):'入金まだ').'</b></td></tr>';
    if ($orders['confirm_payment_time'] != '0000-00-00 00:00:00') {
      $time_str = date('Y年n月j日', strtotime($orders['confirm_payment_time'])); 
    } else {
      $time_str = '入金まだ'; 
    }
    $str .= '<tr><td class="main"><b>入金日：</b></td><td class="main" style="color:red;"><b>'.$time_str.'</b></td></tr>';
  }
  $str .= '<tr><td colspan="2">&nbsp;</td></tr>';
  $str .= '<tr><td class="main"><b>オプション：</b></td><td class="main" style="color:blue;"><b>'.$orders['torihiki_houhou'].'</b></td></tr>';

  $orders_products_query = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." op,".TABLE_PRODUCTS." p where p.products_id = op.products_id and op.orders_id = '".$orders['orders_id']."'");
  $autocalculate_arr = array();
  $autocalculate_sql = "select oaf.value as arr_str from ".TABLE_PREORDERS_OA_FORMVALUE." oaf,".
    TABLE_OA_ITEM." oai 
    where oaf.item_id = oai.id 
    and oai.type = 'autocalculate' 
    and oaf.orders_id = '".$orders['orders_id']."' 
    order by oaf.id asc limit 1 ";
  $autocalculate_query = tep_db_query($autocalculate_sql);
  $autocalculate_row = tep_db_fetch_array($autocalculate_query);
  $arr_checked = explode('_',$autocalculate_row['arr_str']);
  $autocalculate_arr = array();
  foreach($arr_checked as $key=>$value){
    $temp_arr = explode('|',$value);
    if($temp_arr[0] != 0 && $temp_arr[3] != 0){
      $autocalculate_arr[] = array($temp_arr[0],$temp_arr[3]);
    }
  }
  $tmpArr = array();
  if (ORDER_INFO_PRODUCT_LIST == 'true') { 
  while ($p = tep_db_fetch_array($orders_products_query)) {
    if(in_array($p,$tmpArr)){
      continue;
    }
    $tmpArr[] = $p ;
    $products_attributes_query = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id='".$p['orders_products_id']."'");
    if(in_array(array($p['products_id'],$p['orders_products_id']),$autocalculate_arr)&&
        !empty($autocalculate_arr)){
      $str .= '<tr><td class="main"><b>商品：</b><font color="red">「入」</font></td><td class="main">'.$p['products_name'].'</td></tr>';
    }else{
      $str .= '<tr><td class="main"><b>商品：</b><font color="red">「未」</font></td><td class="main">'.$p['products_name'].'</td></tr>';
    }
    $str .= '<tr><td class="main"><b>個数：</b></td><td class="main">'.$p['products_quantity'].'個'.tep_get_full_count2($p['products_quantity'], $p['products_id'], $p['products_rate']).'</td></tr>';
    while($pa = tep_db_fetch_array($products_attributes_query)){
      $str .= '<tr><td class="main"><b>'.$pa['products_options'].'：</b></td><td class="main">'.$pa['products_options_values'].'</td></tr>';
    }
    $str .= '<tr><td class="main"><b>キャラ名：</b></td><td style="font-size:20px;color:#407416;"><b>'.$p['products_character'].'</b></td></tr>';
    $names = tep_get_computers_names_by_preorders_id($orders['orders_id']);
    if ($names) {
      $str .= '<tr><td class="main"><b>PC：</b></td><td class="main">'.implode('&nbsp;,&nbsp;', $names).'</td></tr>';
    }
    $str .= '<tr><td class="main"></td><td class="main"></td></tr>';
    $i++;
  }
  }
  
  
  if (ORDER_INFO_ORDER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_FROM.'</b></td>'; 
    $str .= '<td class="main">';
    $str .= tep_get_site_name_by_order_id($orders['orders_id']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_FETCH_TIME.'</b></td>';
    $str .= '<td class="main">';
    $str .= $orders['torihiki_date']; 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_OPTION.'</b></td>';
    $str .= '<td class="main">';
    $str .= $orders['torihiki_houhou'];    
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_ID.'</b></td>';
    $str .= '<td class="main">';
    $str .= $orders['orders_id']; 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_DATE.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_date_long($orders['date_purchased']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_CUSTOMER_TYPE.'</b></td>';
    $str .= '<td class="main">';
    $str .= get_guest_chk($orders['customers_id']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_CUSTOMER_NAME.'</b></td>';
    $str .= '<td class="main">';
    $str .= '<a href="">'.$orders['customers_name'].'</a>'; 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $ostGetPara = array( "name"=>urlencode($orders['customers_name']),
                         "topicid"=>urlencode(constant("SITE_TOPIC_".$orders['site_id'])),
                         "source"=>urlencode('Email'), 
                         "email"=>urlencode($orders['customers_email_address']));
    $parmStr = '';
    foreach($ostGetPara as $key=>$value){
      $parmStr.= '&'.$key.'='.$value; 
    }
    $remoteurl = (defined('OST_SERVER')?OST_SERVER:'scp')."/tickets.php?a=open2".$parmStr."";
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_EMAIL.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_output_string_protected($orders['customers_email_address']).'&nbsp;&nbsp;<a title="'.RIGHT_TICKIT_ID_TITLE.'" href="'.$remoteurl.'" target="_blank">'.RIGHT_TICKIT_EMAIL.'</a>&nbsp;&nbsp;<a href="telecom_unknow.php?keywords='.tep_output_string_protected($orders['customers_email_address']).'">'.RIGHT_TICKIT_CARD.'</a>'; 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    if ( (($orders['cc_type']) || ($orders['cc_owner']) || ($orders['cc_number'])) ) {  
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_TYPE.'</b></td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_type']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
      
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_OWNER.'</b></td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_owner']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
      
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_ID.'</b></td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_number']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
      
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_EXPIRE_TIME.'</b></td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_expires']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
  } 
  }
  if (ORDER_INFO_CUSTOMER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_IP.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_ip'] ?  $orders['orders_ip'] : 'UNKNOW',IP_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_HOST.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_host_name']?'<font'.($orders['orders_host_name'] == $orders['orders_ip'] ? ' color="red"':'').'>'.$orders['orders_host_name'].'</font>':'UNKNOW',HOST_NAME_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_USER_AGEMT.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_user_agent'] ?  $orders['orders_user_agent'] : 'UNKNOW',USER_AGENT_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  if ($orders['orders_user_agent']) { 
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_OS.'</td>';
      $str .= '<td class="main">';
      $str .= tep_high_light_by_keywords(getOS($orders['orders_user_agent']),OS_LIGHT_KEYWORDS); 
      $str .= '</td>'; 
      $str .= '</tr>'; 
      
      $browser_info = getBrowserInfo($orders['orders_user_agent']); 
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_BROWSE_TYPE.'</b></td>';
      $str .= '<td class="main">';
      $str .= tep_high_light_by_keywords($browser_info['longName'] . ' ' .  $browser_info['version'],BROWSER_LIGHT_KEYWORDS); 
      $str .= '</td>'; 
      $str .= '</tr>'; 
  } 
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_BROWSE_LAN.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_http_accept_language'] ?  $orders['orders_http_accept_language'] : 'UNKNOW',HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_COMPUTER_LAN.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_system_language'] ?  $orders['orders_system_language'] : 'UNKNOW',SYSTEM_LANGUAGE_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_USER_LAN.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_user_language'] ?  $orders['orders_user_language'] : 'UNKNOW',USER_LANGUAGE_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_PIXEL.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_screen_resolution'] ?  $orders['orders_screen_resolution'] : 'UNKNOW',SCREEN_RESOLUTION_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_COLOR.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_color_depth'] ?  $orders['orders_color_depth'] : 'UNKNOW',COLOR_DEPTH_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_FLASH.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_flash_enable'] === '1' ?  'YES' : ($orders['orders_flash_enable'] === '0' ? 'NO' : 'UNKNOW'),FLASH_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
  if ($orders['orders_flash_enable']) {
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_FLASH_VERSION.'</b></td>';
      $str .= '<td class="main">';
      $str .= tep_high_light_by_keywords($orders['orders_flash_version'],FLASH_VERSION_LIGHT_KEYWORDS); 
      $str .= '</td>'; 
      $str .= '</tr>'; 
  }
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_DIRECTOR.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_director_enable'] === '1' ? 'YES' : ($orders['orders_director_enable'] === '0' ? 'NO' : 'UNKNOW'),DIRECTOR_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_QUICK_TIME.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_quicktime_enable'] === '1' ? 'YES' : ($orders['orders_quicktime_enable'] === '0' ? 'NO' : 'UNKNOW'),QUICK_TIME_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_REAL_PLAYER.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_realplayer_enable'] === '1' ?  'YES' : ($orders['orders_realplayer_enable'] === '0' ? 'NO' : 'UNKNOW'),REAL_PLAYER_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_WINDOWS_MEDIA.'</b></td>';
    $str .= '<td class="main">'; $str .= tep_high_light_by_keywords($orders['orders_windows_media_enable'] === '1' ? 'YES' : ($orders['orders_windows_media_enable'] === '0' ?  'NO' : 'UNKNOW'),WINDOWS_MEDIA_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_PDF.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_pdf_enable'] === '1' ?  'YES' : ($orders['orders_pdf_enable'] === '0' ? 'NO' : 'UNKNOW'),PDF_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>'.RIGHT_CUSTOMER_INFO_ORDER_JAVA.'</b></td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_java_enable'] === '1' ?  'YES' : ($orders['orders_java_enable'] === '0' ? 'NO' : 'UNKNOW'),JAVA_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  }
 
  if (ORDER_INFO_REFERER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main"><b>Referer Info:</b></td>';
    $str .= '<td class="main">';
    $str .= urldecode($orders['orders_ref']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    if ($orders['orders_ref_keywords']) {
      $str .= '<tr>'; 
      $str .= '<td class="main"><b>KEYWORDS:</b></td>';
      $str .= '<td class="main">';
      $str .= $orders['orders_ref_keywords']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
    }
  }
  
  if (ORDER_INFO_ORDER_HISTORY == 'true') {
    $order_history_list_raw = tep_db_query("select * from ".TABLE_PREORDERS." where customers_email_address = '".$orders['customers_email_address']."' order by date_purchased desc limit 5"); 
    if (tep_db_num_rows($order_history_list_raw)) {
      $str .= '<tr>';      
      $str .= '<td class="main" colspan="2">';      
      $str .= '<table width="100%" border="0" cellspacing="0" cellpadding="2">'; 
      $str .= '<tr>'; 
      $str .= '<td colspan="4"><b>Order History:</b></td>'; 
      $str .= '</tr>'; 
      while ($order_history_list = tep_db_fetch_array($order_history_list_raw)) {
        $str .= '<tr>'; 
        $str .= '<td>'; 
        $store_name_raw = tep_db_query("select * from ".TABLE_SITES." where id = '".$order_history_list['site_id']."'"); 
        $store_name_res = tep_db_fetch_array($store_name_raw); 
        $str .= $store_name_res['romaji']; 
        $str .= '</td>'; 
        $str .= '<td>'; 
        $str .= $order_history_list['date_purchased']; 
        $str .= '</td>'; 
        $str .= '<td>'; 
        $str .= strip_tags(tep_get_pre_ot_total_by_orders_id($order_history_list['orders_id'], true)); 
        $str .= '</td>'; 
        $str .= '<td>'; 
        $str .= $order_history_list['orders_status_name']; 
        $str .= '</td>'; 
        $str .= '</tr>'; 
      }
      $str .= '</table>'; 
      $str .= '</td>';      
      $str .= '</tr>';      
    }
  }
  
  if (ORDER_INFO_REPUTAION_SEARCH == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">';
    $str .= '<b>'.RIGHT_ORDER_INFO_REPUTAION_SEARCH.'</b>'; 
    $str .= '</td>';
    $str .= '<td class="main">';
    $str .= tep_get_customers_fax_by_id($orders['customers_id']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  }
  
  if (ORDER_INFO_ORDER_COMMENT == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">';
    $str .= '<b>'.RIGHT_ORDER_COMMENT_TITLE.'</b>'; 
    $str .= '</td>';
    $str .= '<td class="main">';
    $str .= nl2br($orders['orders_comment']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  }
  
  
  $str .= '</table>';
  $str=str_replace("\n","",$str);
  $str=str_replace("\r","",$str);
  if ($single) {
    echo $str; 
  } else {
    return htmlspecialchars($str);
  }
}


function tep_preorders_finishqa($orders_id) {
  $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PREORDERS." where orders_id='".$orders_id."'"));
  return $order['flag_qaf'];
}

function tep_get_preorders_status_id($orders_id, $language_id = '') {
  global $languages_id;

  if (!$language_id) $language_id = $languages_id;
  $orders_query = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id='".$orders_id."'");
  $orders = tep_db_fetch_array($orders_query);
  return $orders['orders_status'];
}

function tep_get_preorder_canbe_finish($orders_id){
  //  如果是取消的可以结束 
  
  if (tep_preorders_finishqa($orders_id)) {
    return false;
  }
  $status =  tep_get_preorders_status_id($orders_id);
  if($status == 6 or $status == 8){
    return true;
  }
  //$formtype = tep_check_pre_order_type($orders_id);
  $formtype = 4;
  $payment_romaji = tep_get_pre_payment_code_by_order_id($orders_id); 
  $oa_form_sql = "select * from ".TABLE_OA_FORM."   where formtype = '".$formtype."' and payment_romaji = '".$payment_romaji."'";
  $res = tep_db_fetch_array(tep_db_query($oa_form_sql));;
  $form_id = $res['id'] ;
  $sql = 'select i.* from oa_form_group fg ,oa_item i where  i.group_id = fg.group_id and i.option like "%require%" and fg.form_id = "'.$form_id .'"';
  $res3  = tep_db_query($sql);
  while($item = tep_db_fetch_array($res3)){
    $sql2 =  'select value from preorders_oa_formvalue where item_id = '.$item['id'] .' and orders_id ="'.$orders_id.'" and form_id = "'.$form_id.'"';
    $res2 = tep_db_fetch_array(tep_db_query($sql2));
    if (!$res2){
      return false;
    }else {
      if ($res2['value']==''){
      return false;
      }
    }
    $res2 = '';
  }
  
return true;
}

function tep_get_preorders_products_names($orders_id) {
  $str = '';
  $orders_products_query = tep_db_query("select * from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$orders_id."'");
  while ($p = tep_db_fetch_array($orders_products_query)) {
    $str .= $p['products_name'].' ';
  }
  return $str;
}

function tep_pre_payment_method_menu($payment_method = "") {
  $payment_text = tep_get_list_pre_payment(); 
  $payment_array = explode("\n", $payment_text);
  for($i=0; $i<sizeof($payment_array); $i++) {
    $payment_list[] = array('id' => $payment_array[$i],
        'text' => $payment_array[$i]);
  }
  return tep_draw_pull_down_menu('payment_method', $payment_list, $payment_method);
}

function tep_get_list_pre_payment() {
  global $language;

  $payment_directory = DIR_FS_CATALOG_MODULES .'payment/';
  $payment_array = array();
  $payment_list_str = '';

  if ($dh = @dir($payment_directory)) {
    while ($payment_file = $dh->read()) {
      if (!is_dir($payment_directory.$payment_file)) {
        if (substr($payment_file, strrpos($payment_file, '.')) == '.php') {
          $payment_array[] = $payment_file; 
        }
      }
    }
    sort($payment_array);
    $dh->close();
  }

  for ($i = 0, $n = sizeof($payment_array); $i < $n; $i++) {
    $payment_filename = $payment_array[$i]; 
    include(DIR_WS_LANGUAGES . $language . '/modules/payment/' . $payment_filename); 
    include($payment_directory . $payment_filename); 
    $payment_class = substr($payment_filename, 0, strrpos($payment_filename, '.'));
    if (tep_class_exists($payment_class)) {
      $payment_module = new $payment_class; 
      if ($payment_module->code == 'buying' || $payment_module->code == 'buyingpoint' || $payment_module->code == 'fetch_good' || $payment_module->code == 'free_payment') {
        continue; 
      }
      $payment_list_str .= $payment_module->title."\n"; 
    }
  }

  return mb_substr($payment_list_str, 0, -1, 'UTF-8');
}
