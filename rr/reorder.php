<?php
/*
 $Id$
*/
require('includes/application_top.php');
require_once(DIR_WS_LANGUAGES.$language.'/'.FILENAME_REORDER);

$breadcrumb->add(TEXT_BREADCRUMB_TITLE, tep_href_link('reorder.php'));
?>
<?php page_head();?>
<script type="text/javascript" src="./js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="./js/date_time_reorder.js"></script>
<script type="text/javascript">
$(document).ready(function(){
document.onclick=function(e){  
  var shipping_hour = $("input[name='hour']").val();
  var e=e?e:window.event;  
  var tar = e.srcElement||e.target;  
  if(tar.id!="hour"+shipping_hour){  
    if($(tar).attr("id")!="shipping_time_id"){  
      if($(tar).attr("class")!="time_radio"){
        if($(tar).attr("class")!="time_label"){
          if($(tar).attr("name")!="min"){
            if($(tar).attr("href")!="javascript:void(0);"){
              check_out(shipping_hour); 
            }
          }
        }
      }
    }  
  }  
}
});
</script>
</head>
<body>
<div align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" summary="table">
    <tr>
      <td valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td id="contents" valign="top">
        <h1 class="pageHeading"><span><?php echo HEADING_TITLE; ?></span></h1>
<?php if ($_POST) {
  include(DIR_WS_CLASSES . 'admin_order.php');

  if(isset($_POST['order_id'])){
    $oID    = tep_db_prepare_input($_POST['order_id']);
  } else {
    $oID    = tep_db_prepare_input($_POST['order_id_1']).'-'.tep_db_prepare_input($_POST['order_id_2']);
  }
  
  $cEmail = tep_db_prepare_input($_POST['email']);
  $cEmail = str_replace("\xe2\x80\x8b", '', $cEmail);
  
  $o      = new order($oID);
  // ccdd
  $order  = tep_db_fetch_array(tep_db_query("
        select * 
        from `".TABLE_ORDERS."` 
        where site_id = '" . SITE_ID . "' 
          and `orders_id` = '".$oID."' 
          and `customers_email_address` = '".$cEmail."'
        "));

  if ($order) {
    if (isset($_POST['hour'])){
      $date   = tep_db_prepare_input($_POST['date']);
      $hour   = tep_db_prepare_input($_POST['hour']);
      $minute = tep_db_prepare_input($_POST['min']);
      $start_hour = tep_db_prepare_input($_POST['start_hour']);
      $start_min = tep_db_prepare_input($_POST['start_min']);
      $end_hour = tep_db_prepare_input($_POST['end_hour']);
      $end_min = tep_db_prepare_input($_POST['end_min']);
      
      $comment = tep_db_prepare_input($_POST['comment']);

      $datetime = $date.' '.$start_hour.':'.$start_min;
      $datetime_end = $date.' '.$end_hour.':'.$end_min;
      $time     = strtotime($datetime);

      //if (in_array($order['orders_status'], array(2,5,6,7,8))) {
      if (tep_orders_status_finished($order['orders_status'])) {
        // status can not change
        echo '<div class="comment">'.TEXT_DELETE_ORDER_SUCCESS.' <div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" width="47" height="17" alt="'.TEXT_BACK_TO_HISTORY.'" title="'.TEXT_BACK_TO_HISTORY.'"></a></div></div>';
        // time error
      } else {
        // update time
        //change order status and insert order status history
        if ($date && $hour && $start_min) {
          tep_db_query("
              update `".TABLE_ORDERS."` 
              set `orders_status`='17' ,
                  `torihiki_date` = '".$datetime."' ,
                  `torihiki_date_end` = '".$datetime_end."' ,
                  `last_modified` = now()
              WHERE `orders_id`='".$order_id."' 
                and site_id = '".SITE_ID."'
          ");
          orders_updated($order_id);
          last_customer_action();
        }else{
          tep_db_query("
              update `".TABLE_ORDERS."` 
              set `orders_status`='17' ,
                  `last_modified` = now()
              WHERE `orders_id`='".$order_id."' 
                and site_id = '".SITE_ID."'
          ");
          orders_updated($order_id);
          last_customer_action();
        }
        tep_order_status_change($order_id,17);
          // insert a history
          $sql = "
            INSERT INTO `".TABLE_ORDERS_STATUS_HISTORY."` (
                `orders_status_history_id`,
                `orders_id` ,
                `orders_status_id` ,
                `date_added` ,
                `customer_notified` ,
                `comments`
              ) VALUES (
                NULL ,
                '".$order_id."', 
                '17', 
                '".date("Y-m-d H:i:s")."', 
                '1', 
                '".mysql_real_escape_string($comment)."'
              )
          ";
          // ccdd
          tep_db_query($sql);
        echo '<div class="comment"><div class="product_info_box">'.TEXT_CHANGE_ORDER_CONFIRM_EMAIL.' <div align="right"><a href="/"><img src="includes/languages/japanese/images/buttons/button_back_home.gif" width="63" height="18" alt="'.TEXT_BACK_TO_TOP.'" title="'.TEXT_BACK_TO_TOP.'"></a></div></div></div>';
        // sent mail to customer
        // ccdd
        $mail    = tep_db_fetch_array(tep_db_query("
              select * 
              from ".TABLE_ORDERS_MAIL." 
              where orders_status_id=17 
                and (site_id='0' or site_id = '" . SITE_ID . "')
              order by site_id DESC
        "));
        $mail_title   = $mail['orders_status_title'];
        $mail_content = $mail['orders_status_mail'];

  // load selected payment module
  require(DIR_WS_CLASSES . 'payment.php');
  $payment_modules = new payment(isset($payment) ? $payment : '');

  # OrderNo
  $insert_id = $oID;
  
  $o = new order($oID);
  $payment_code = payment::changeRomaji($o->info['payment_method'], PAYMENT_RETURN_TYPE_CODE); 

  # Check
  // ccdd
  $NewOidQuery = tep_db_query("
      select count(*) as cnt 
      from ".TABLE_ORDERS." 
      where orders_id = '".$insert_id."' 
        and site_id = '".SITE_ID."'
  ");
  $NewOid = tep_db_fetch_array($NewOidQuery);
  
  # load the selected shipping module(convenience_store)
// load the before_process function from the payment modules
  $payment_modules->before_process($payment_code);

  require(DIR_WS_CLASSES . 'order_total.php');
  $order_total_modules = new order_total;

  $order_totals = $order_total_modules->process();
  
  # Random

  
  # Select

  // initialized for the email confirmation
  $products_ordered = '';
  $subtotal = 0;
  $total_tax = 0;

  for ($i=0, $n=sizeof($o->products); $i<$n; $i++) {
  //------insert customer choosen option to order--------
    $attributes_exist = '0';
    $products_ordered_attributes = '';
//------insert customer choosen option eof ----
    $attribute_len_array = array();
    $attribute_max_len = 0;

    if (isset($o->products[$i]['op_attributes'])) {
      foreach ($o->products[$i]['op_attributes'] as $opa_l_order) {
        $attribute_len_array[] = mb_strlen($opa_l_order['option_info']['title'], 'utf-8'); 
      }
    }
    
    if (!empty($attribute_len_array)) {
      $attribute_max_len = max($attribute_len_array); 
    }
   
    if ($attribute_max_len < 4) {
      $attribute_max_len = 4; 
    }
    if (isset($o->products[$i]['op_attributes'])) {
      foreach ($o->products[$i]['op_attributes'] as $opa_order) {
        $products_ordered_attributes .= "\n" .$opa_order['option_info']['title'] .  str_repeat('　',intval(($attribute_max_len - mb_strlen($opa_order['option_info']['title'], 'utf-8')))) . '：' .  str_replace("<br>", "\n", $opa_order['option_info']['value']);
      }
    }
    if(isset($o->products[$i]['weight']) && isset($o->products[$i]['qty'])){
      $total_weight += ($o->products[$i]['qty'] * $o->products[$i]['weight']);
    }
    if(isset($o->products[$i]['qty'])) {
      $total_tax += tep_calculate_tax(
        isset($total_products_price)?$total_products_price:0, 
        (isset($products_tax)?$products_tax:0)
        ) * $o->products[$i]['qty'];
    }
    if(isset($total_cost)){
      $total_cost += isset($total_products_price)?$total_products_price:0;
    } else {
      $total_cost = 0;
    }
    
    $products_ordered .= TEXT_REORDER_ORDER_PRODUCT.str_repeat('　',intval(($attribute_max_len - mb_strlen(TEXT_REORDER_ORDER_PRODUCT, 'utf-8')))).'：' . $o->products[$i]['name'];
    if(tep_not_null($o->products[$i]['model'])) {
      $products_ordered .= ' (' . $o->products[$i]['model'] . ')';
    }
  
    //ccdd
    $product_info = tep_get_product_by_id($o->products[$i]['id'], SITE_ID ,$languages_id);
  
    $products_ordered .= $products_ordered_attributes . "\n";
    $products_ordered .= TEXT_REORDER_QTY_SUM.str_repeat('　',intval(($attribute_max_len - mb_strlen(TEXT_REORDER_QTY_SUM, 'utf-8')))).'：' . $o->products[$i]['qty'] . TEXT_REORDER_QTY . tep_get_full_count2($o->products[$i]['qty'], $o->products[$i]['id']) . "\n";

    $products_ordered .= '------------------------------------------' . "\n";
  }
  
  # メール本文整形 --------------------------------------
  $email_order = '';

  // ccdd
  $otq = tep_db_query("
      select * 
      from ".TABLE_ORDERS_TOTAL." 
      where class = 'ot_total' 
        and orders_id = '".$insert_id."'
  ");
  $ot = tep_db_fetch_array($otq);
  $_datetime = $o->tori['date'];
  $_datetime = explode(' ',$_datetime);
  $_date = $_datetime[0];
  $_time = explode(':',$_datetime[1]);
  $_hour = $_time[0]; 
  $_minute = $_time[1];

  $email_order .= TEXT_REORDER_LINE . "\n";
  $email_order .= TEXT_REORDER_OID_EMAIL . $insert_id . "\n";
  $email_order .= TEXT_REORDER_TDATE_EMAIL . tep_date_long(time()) . "\n";
  $email_order .= TEXT_REORDER_NAME_EMAIL . $o->customer['name'] . "\n";
  $email_order .= TEXT_REORDER_EMAIL_EMAIL . $o->customer['email_address'] . "\n";
  $email_order .= TEXT_REORDER_LINE . "\n\n";
  $email_order .= TEXT_REORDER_TRADE_DATE . str_string($_date);
  $date_arr = explode('-', $_date);  
  $tmp_date = date('D', mktime(0, 0, 0, $date_arr[1], $date_arr[2], $date_arr[0]));  
  switch(strtolower($tmp_date)) {
     case 'mon':
       $email_order .= '（'.TEXT_DATE_MONDAY.'）'; 
       break;
     case 'tue':
       $email_order .= '（'.TEXT_DATE_TUESDAY.'）'; 
       break;
     case 'wed':
       $email_order .= '（'.TEXT_DATE_WEDNESDAY.'）'; 
       break;
     case 'thu':
       $email_order .= '（'.TEXT_DATE_THURSDAY.'）'; 
       break;
     case 'fri':
       $email_order .= '（'.TEXT_DATE_FRIDAY.'）'; 
       break;
     case 'sat':
       $email_order .= '（'.TEXT_DATE_STATURDAY.'）'; 
       break;
     case 'sun':
       $email_order .= '（'.TEXT_DATE_SUNDAY.'）'; 
       break;
     default:
       break;
  }
  $email_order .= $_hour .  TIME_HOUR_TEXT . $_minute . TIME_MIN_TEXT . TEXT_TIME_LINK . $end_hour.TIME_HOUR_TEXT.$end_min.TIME_MIN_TEXT.TEXT_REORDER_TWENTY_FOUR_HOUR."\n";

  if ($comment) {
    $email_order .= TEXT_REORDER_COMMERN_EMAIL . "\n";
    $email_order .= $comment . "\n";
  }
  
  $mail_title = "[" . $order['orders_id'] . "]".TEXT_REORDER_TITLE_EMAIL;
  $email_order = str_replace(array('${NAME}', '${TIME}', '${CONTENT}', '${SITE_NAME}', '${SITE_URL}', '${SUPPORT_EMAIL}'), array($o->customer['name'], date('Y-m-d H:i:s'), $email_order, STORE_NAME, HTTP_SERVER, SUPPORT_EMAIL_ADDRESS), $mail_content);

  # メール本文整形 --------------------------------------
  tep_mail($o->customer['name'], $o->customer['email_address'], $mail_title, $email_order, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, '');
  if (SEND_EXTRA_ORDER_EMAILS_TO != '') {
    tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, $mail_title, $email_order, $o->customer['name'], $o->customer['email_address'], '');
  }
      }
    } else if (tep_orders_status_finished($order['orders_status'])) {
        // status can not change
        echo '<div class="comment">'.TEXT_REORDER_COMMERN_STATUS.'<div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" width="47" height="17" alt="'.TEXT_BACK_TO_HISTORY.'" title="'.TEXT_BACK_TO_HISTORY.'"></a></div></div>';
    } else {
        // edit order
?>
<div class="comment">
<div id='form' class="product_info_box">
<?php
echo tep_draw_form('order', tep_href_link('reorder.php'));
?>
<input type='hidden' name='order_id' value='<?php echo $order['orders_id']?>' >
<input type='hidden' name='email' value='<?php echo $order['customers_email_address']?>' >
<div id="form_error" style="display:none"></div>
<table class="information_table" summary="table">
 <tr>
 <td width="30%" bgcolor="#333333"><?php echo TEXT_REORDER_OID_TITLE;?></td>
  <td bgcolor="#333333"><?php echo $order['orders_id']?></td>
 </tr>
 <tr>
 <td bgcolor="#333333"><?php echo TEXT_REORDER_OID_NAME;?></td>
  <td bgcolor="#333333"><?php echo $order['customers_name']?></td>
 </tr>
 <tr>
 <td bgcolor="#333333"><?php echo TEXT_REORDER_EMAIL_TITLE;?></td>
  <td bgcolor="#333333"><?php echo $order['customers_email_address']?></td>
 </tr>
 <tr>
 <td bgcolor="#333333"><?php echo TEXT_REORDER_TRADE_NO_CHANGE;?></td>
  <td bgcolor="#333333" id='old_time'><?php echo tep_date_long(strtotime($order['torihiki_date']))?> <?php echo date('H:i', strtotime($order['torihiki_date']));?><?php echo TEXT_TIME_LINK;?><?php echo date('H:i', strtotime($order['torihiki_date_end']));?></td>
 </tr>
<?php
//根据订单中的商品来生成取引时间
  $cart_products_id = array();
  
  $orders_products_shipping_query = tep_db_query("select products_id from ". TABLE_ORDERS_PRODUCTS ." where orders_id='". $order['orders_id'] ."'");
  while($orders_products_shipping_array = tep_db_fetch_array($orders_products_shipping_query)){

    $cart_products_id[] = $orders_products_shipping_array["products_id"];
  }
  tep_db_free_result($orders_products_shipping_query);

  //根据$cart_products_id数组中的商品ID来获取每个商品的取引时间
  $cart_shipping_time = array();
  foreach($cart_products_id as $cart_products_value){
    
    $shipping_time_query = tep_db_query("select * from ". TABLE_PRODUCTS ." where products_id=".(int)$cart_products_value);
    $shipping_time_array = tep_db_fetch_array($shipping_time_query);
    tep_db_free_result($shipping_time_query);
    $cart_shipping_time[] = $shipping_time_array['products_shipping_time'];
  }
   
  $cart_shipping_time = array_unique($cart_shipping_time); 
  
  $products_num = count($cart_shipping_time); 
  $shipping_time_array = array();
  foreach($cart_shipping_time as $cart_shipping_value){

    $shipping_query = tep_db_query("select * from ". TABLE_PRODUCTS_SHIPPING_TIME ." where id=".$cart_shipping_value);
    $shipping_array = tep_db_fetch_array($shipping_query);
    $shipping_time_array['work'][] = unserialize($shipping_array['work']);
    $shipping_time_array['db_set_day'][] = $shipping_array['db_set_day'];
    $shipping_time_array['shipping_time'][] = $shipping_array['shipping_time'];

  }
  
  //work
  $shipping_time_start = array();
  $shipping_time_end = array();
  foreach($shipping_time_array['work'] as $shipping_time_key=>$shipping_time_value){

    foreach($shipping_time_value as $k=>$val){

      $shipping_time_start[$shipping_time_key][] = $val[0]; 
      $shipping_time_end[$shipping_time_key][] = $val[1];
    } 
  }
   
  
  $ship_array = array();
  $ship_time_array = array();
  foreach($shipping_time_start as $shipping_key=>$shipping_value){
    foreach($shipping_value as $sh_key=>$sh_value){
      
      $sh_start_array = explode(':',$sh_value);
      $sh_end_array = explode(':', $shipping_time_end[$shipping_key][$sh_key]);
      for($i = (int)$sh_start_array[0];$i <= (int)$sh_end_array[0];$i++){
        if(isset($ship_time_array[$shipping_key][$i]) && $ship_time_array[$shipping_key][$i] != ''){
          $ship_time_array[$shipping_key][$i] .= '|'.$sh_value.','.$shipping_time_end[$shipping_key][$sh_key];
        }else{
          $ship_time_array[$shipping_key][$i] = $sh_value.','.$shipping_time_end[$shipping_key][$sh_key]; 
        }
      } 
    }  
  }


  $ship_count_array = array();
  foreach($ship_time_array as $ship_key=>$ship_value){

    foreach($ship_value as $ship_k=>$ship_v){
      $ship_temp_array = array();
      $ship_temp_array = explode('|',$ship_v);
      $ship_time_array[$ship_key][$ship_k] = $ship_temp_array;
    }
    $ship_count_array[$ship_key] = count($ship_value);
  } 

  $ship_min_value = array_search(min($ship_count_array),$ship_count_array);
  $shipp_time_array = array();
  foreach($ship_time_array[$ship_min_value] as $ship_hour_key=>$ship_hour_value){

    foreach($ship_hour_value as $ship_hour_k=>$ship_hour_v){

      $ship_hour_array = explode(',',$ship_hour_v);
      foreach($ship_time_array as $ship_t_k=>$ship_t_v){

          if($ship_t_k == $ship_min_value){continue;}
            if(isset($ship_t_v[$ship_hour_key])){
   
            foreach($ship_t_v[$ship_hour_key] as $ship_tt_k=>$ship_tt_v){

               $ship_hour_temp_array = array();
               $ship_hour_temp_array = explode(',',$ship_tt_v); 
               if($ship_hour_array[0] <= $ship_hour_temp_array[1]){

                 $ship_start_time = max($ship_hour_array[0],$ship_hour_temp_array[0]); 
                 $ship_end_time = min($ship_hour_array[1],$ship_hour_temp_array[1]);
                 $ship_start_time_value = str_replace(':','',$ship_start_time);
                 $ship_end_time_value = str_replace(':','',$ship_end_time);
                 if(!in_array($ship_start_time.','.$ship_end_time,$shipp_time_array[$ship_hour_key]) && (int)$ship_start_time_value < (int)$ship_end_time_value){
                   $shipp_time_array[$ship_hour_key][] = $ship_start_time.','.$ship_end_time;
                 }
               }
            }
          }
      }
    }
  }

  
  $shipp_flag_array = $ship_time_array[$ship_min_value];

  foreach($shipp_time_array as $shipp_flag_k=>$shipp_flag_v){

    $shipp_temp_start_array = array();
    $shipp_temp_end_array = array(); 
   if(isset($shipp_flag_array[$shipp_flag_k])){
    foreach($shipp_flag_array[$shipp_flag_k] as $shipp_flag_key=>$shipp_flag_value){
 
      $shipp_temp_all_array = array();
      $shipp_temp_all_array = explode(',',$shipp_flag_value);
      $shipp_temp_start_array[] = $shipp_temp_all_array[0];
      $shipp_temp_end_array[] = $shipp_temp_all_array[1];
    }
    $shipp_temp_start_num = str_replace(':','',min($shipp_temp_start_array));
    $shipp_temp_end_num = str_replace(':','',max($shipp_temp_end_array));
    foreach($shipp_flag_v as $shipp_f_k=>$shipp_f_v){

      $shipp_t_all_array = array();
      $shipp_t_all_array = explode(',',$shipp_f_v);
      $shipp_t_start_num = str_replace(':','',$shipp_t_all_array[0]);
      $shipp_t_end_num = str_replace(':','',$shipp_t_all_array[1]);

      if(!($shipp_t_start_num >= $shipp_temp_start_num && $shipp_t_end_num <= $shipp_temp_end_num)){

        unset($shipp_time_array[$shipp_flag_k][$shipp_f_k]);
      }
    }
   }else{

     unset($shipp_time_array[$shipp_flag_k]);
   }

  }
 




  
  $ship_new_array = array(); 
  $shipp_array = array();
  foreach($shipp_time_array as $shipp_time_k=>$shpp_time_v){

      $ship_new_str = implode('|',$shpp_time_v);
      $ship_new_array[] = $ship_new_str;
      $shipp_array[] = $shipp_time_k;

  } 

  foreach($ship_new_array as $_s_key=>$_s_value){
      $s_temp_array = explode('|',$_s_value);    
      sort($s_temp_array);
      $ship_new_array[$_s_key] = implode('|',$s_temp_array); 
  } 

  foreach($ship_new_array as $s_key=>$s_val){
    $ss_array = array();
    $ss_array = explode(',',$s_val);
    $ss_start = str_replace(':','',$ss_array[0]);
    $ss_end = str_replace(':','',$ss_array[1]);
    if($ss_start > $ss_end){

      unset($ship_new_array[$s_key]);
      unset($shipp_array[$s_key]);
    }
  }

  $max_time_str_old = implode('||',$shipp_array);
  $min_time_str_old = implode('||',$ship_new_array);
  //当日起几日后可以收货
  $db_set_day = max($shipping_time_array['db_set_day']);
  //可选收货期限
  $shipping_time = max($shipping_time_array['shipping_time']);

  $now_time_date = date('Y-m-d',strtotime("+".$shipping_time." minutes"));
  $now_time_hour = date('Hi',strtotime("+".$shipping_time." minutes"));
  $now_time = date('H:i',strtotime("+".$db_set_day." minutes"));
  $now_time = str_replace(':','',$now_time); 
  $now_flag = false;
  if(date('Ymd') == date('Ymd',strtotime("+".$shipping_time." minutes"))){
    $now_time_end = date('H:i',strtotime("+".$shipping_time." minutes"));
    $now_time_end = str_replace(':','',$now_time_end);
    $now_flag = true;
  }

  $ship_new_end_array = array();
  $ship_new_end_array = $ship_new_array;
  $shipp_end_array = array();
  $shipp_end_array = $shipp_array;

  foreach($ship_new_array as $s_k=>$s_v){
    $ss_array = array();
    $ss_array = explode(',',$s_v);
    $ss_start = str_replace(':','',$ss_array[0]);
    $ss_end = str_replace(':','',$ss_array[1]);

    if($ss_end > $now_time_hour){

      unset($ship_new_end_array[$s_k]);
      unset($shipp_end_array[$s_k]);
    }
    if($ss_start > $ss_end || $ss_start < $now_time || ($now_flag == true && $ss_end > $now_time_end)){

      unset($ship_new_array[$s_k]);
      unset($shipp_array[$s_k]);
    }
  } 




  $max_time_str = implode('||',$shipp_array);
  $min_time_str = implode('||',$ship_new_array);
  $max_time_end_str = implode('||',$shipp_end_array);
  $min_time_end_str = implode('||',$ship_new_end_array);
  //----------
  if(count($shipping_time_array['work']) == 1){
    
    $shi_time_array = array();
    foreach($shipping_time_start[0] as $shi_key=>$shi_value){

      $shi_start_array = explode(':',$shi_value);
      $shi_end_array = explode(':',$shipping_time_end[0][$shi_key]);

      for($shi_i = (int)$shi_start_array[0];$shi_i <= (int)$shi_end_array[0];$shi_i++){

        if(isset($shi_time_array[$shi_i]) && $shi_time_array[$shi_i] != ''){

          
          $shi_time_array[$shi_i] .= '|'.$shi_value.','.$shipping_time_end[0][$shi_key]; 
        }else{

          $shi_time_array[$shi_i] = $shi_value.','.$shipping_time_end[0][$shi_key]; 
        }
      }
    }

    foreach($shi_time_array as $_s_key=>$_s_value){
      $s_temp_array = explode('|',$_s_value);    
      sort($s_temp_array);
      $shi_time_array[$_s_key] = implode('|',$s_temp_array); 
    }
   $max_time_str_old = implode('||',array_keys($shi_time_array));
    $min_time_str_old = implode('||',$shi_time_array);


    $now_time_date = date('Y-m-d',strtotime("+".$shipping_time." minutes"));
    $now_time_hour = date('Hi',strtotime("+".$shipping_time." minutes"));
    $now_time = date('H:i',strtotime("+".$db_set_day." minutes"));
    $now_time = str_replace(':','',$now_time);
    $now_flag = false;
    if(date('Ymd') == date('Ymd',strtotime("+".$shipping_time." minutes"))){
      $now_time_end = date('H:i',strtotime("+".$shipping_time." minutes"));
      $now_time_end = str_replace(':','',$now_time_end);
      $now_flag = true;
    }

    $shi_time_end_array = array();
    $shi_time_end_array = $shi_time_array;

    foreach($shi_time_array as $s_k=>$s_v){
      $ss_array = array();
      $ss_end_array = array();
      $ss_str = '';
      $ss_array = explode('|',$s_v);
      $ss_end_array = explode('|',$s_v);

      foreach($ss_array as $ss_k=>$ss_v){

        $now_array = array();
        $now_array = explode(',',$ss_v);
        $ss_start = str_replace(':','',$now_array[0]);
        $ss_end = str_replace(':','',$now_array[1]); 

        if($ss_end > $now_time_hour){
           
            unset($ss_end_array[$ss_k]);
        }

        if($ss_start < $now_time || ($now_flag == true && $ss_end > $now_time_end)){
 
          unset($ss_array[$ss_k]);
        }else{
          $now_hour = date('H');
          if($s_k <  $now_hour){

            unset($ss_array[$ss_k]);
          }

        }
      }
      $ss_str = implode('|',$ss_array);
      $ss_end_str = implode('|',$ss_end_array);
      $shi_time_array[$s_k] = $ss_str; 
      $shi_time_end_array[$s_k] = $ss_end_str;
    }    

    foreach($shi_time_array as $shi_k=>$shi_v){

      if($shi_v == ''){

        unset($shi_time_array[$shi_k]);
      }

    }

    foreach($shi_time_end_array as $shi_end_k=>$shi_end_v){
     
       if($shi_end_v == ''){
     
           unset($shi_time_end_array[$shi_end_k]);
       }
     
    }

    $max_time_str = implode('||',array_keys($shi_time_array));
    $min_time_str = implode('||',$shi_time_array);
    $max_time_end_str = implode('||',array_keys($shi_time_end_array));
    $min_time_end_str = implode('||',$shi_time_end_array);
  }
  

  //可配送时间区域
  $work_start = $max_time_str;
  $work_end = $min_time_str;
  $work_start_old = $max_time_str_old;
  $work_end_old = $min_time_str_old;
  $work_start_exit = $max_time_end_str;
  $work_end_exit = $min_time_end_str;

  

  $today = getdate();
  $m_num = $today['mon'];
  $d_num = date('d',strtotime("+".$db_set_day." minutes"));
  $shipping_time = strtotime("+".$shipping_time." minutes");
  $year = $today['year'];
    
  $hours = date('H');
  $mimutes = date('i');
?>
 <tr>
 <td bgcolor="#333333"><?php echo TEXT_REORDER_TRADE_CHANGE;?></td>
  <td bgcolor="#333333">
  <select name="date" id="new_date" onChange="selectDate('<?php echo $work_start; ?>', '<?php echo $work_end; ?>',this.value,'<?php echo $work_start_old; ?>','<?php echo $work_end_old; ?>','<?php echo date('Y-m-d');?>','<?php echo $work_start_exit; ?>','<?php echo $work_end_exit; ?>','<?php echo $now_time_date;?>');$('#date_error').html('');$('#hour_error').html('');">
  <option value=""><?php echo EXPECT_DATE_SELECT;?></option>
    <?php
          $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
          $newarr = array(TEXT_DATE_MONDAY, TEXT_DATE_TUESDAY, TEXT_DATE_WEDNESDAY, TEXT_DATE_THURSDAY, TEXT_DATE_FRIDAY, TEXT_DATE_STATURDAY, TEXT_DATE_SUNDAY);
          $j = 0;
          $j_shipping = time();
  while($j_shipping <= $shipping_time){
    if(!($work_start == '' && $work_end == '' && date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)) == date("Y-m-d"))){
     
     if(!(date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year))== $now_time_date && date('Hi') >= $now_time_hour)){
       if(date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)) == $now_time_date && $min_time_end_str == ''){
          break;
        } 
       echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)).'">'.str_replace($oarr, $newarr, date("Y".DATE_YEAR_TEXT."m".DATE_MONTH_TEXT."d".DATE_DAY_TEXT." （l） ", mktime(0,0,0,$m_num,$d_num+$j,$year))).'</option>' . "\n";
      }
    }
    $j_shipping += 86400;
    $j++;
    if(date('Y-m-d',$j_shipping) == $now_time_date && $min_time_end_str != ''){

      echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)).'" '. $selected_str .'>'.str_replace($oarr, $newarr, date("Y".DATE_YEAR_TEXT."m".DATE_MONTH_TEXT."d".DATE_DAY_TEXT." （l） ", mktime(0,0,0,$m_num,$d_num+$j,$year))).'</option>' . "\n";
      break;
     }
    }
    ?> 
   </select><br>
   <span id="date_error"></span>
</td></tr>
<tr id="date_show_id" style="display:none;">
<td colspan="2" id="shipping_box">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr id="shipping_list" style="display:none;">
  <td class="main" width="30%" bgcolor="#333333"><?php echo TEXT_EXPECT_TRADE_TIME; ?></td>
  <td class="main" id="shipping_list_show" bgcolor="#333333"></td>
  </tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" style=" position:absolute; width:679px; *width:675px;">
<tr id="shipping_list_min" style="display:none;">
 <td class="main" width="189">&nbsp;<input type="hidden" id="ele_id" name="ele" value=""></td>
 <td class="main" id="shipping_list_show_min">
 </td>
 </tr>
</table>
</td></tr>
<tr id="hour_show_error" style="display:none;">
<td></td>
<td><span id="hour_error"></span></td>
</tr>
<tr><td colspan="2" bgcolor="#333333">
  <div><?php echo TEXT_REORDER_TRADE_TEXT;?></div>
  </td>
 </tr>
</table>
<br>
<table class="information_table" summary="table">
<tr>
<td width="30%" bgcolor="#333333"><?php echo TEXT_REORDER_COMMENT_TITLE;?></td>
<td bgcolor="#333333"><textarea name='comment' id='comment' rows="5"></textarea></td>
</tr>
</table>
<br>
<p align="center">
<input type='image' src="includes/languages/japanese/images/buttons/button_submit.gif" alt="<?php echo TEXT_REORDER_CONFIRM;?>" title="<?php echo TEXT_REORDER_CONFIRM;?>" onClick="return orderConfirmPage();" >
<input type='image' src="includes/languages/japanese/images/buttons/button_reset.gif" alt="<?php echo TEXT_REORDER_CLEAR;?>" title="<?php echo TEXT_REORDER_CLEAR;?>" onClick="javascript:document.order.reset();$('#shipping_list').hide();$('#shipping_list_show').html('');$('#shipping_list_show_min').html('');$('#shipping_list_min').hide();return false;" >
</p>
</form>
</div>
<div id='confirm' style='display:none; text-align: center;' class="product_info_box">
  <div id='confirm_content'></div>
  <input type='image' src="includes/languages/japanese/images/buttons/button_submit.gif" alt="<?php echo TEXT_REORDER_CONFIRM;?>" title="<?php echo TEXT_REORDER_CONFIRM;?>" onClick="document.order.submit()" >
  <input type='image' src="includes/languages/japanese/images/buttons/button_back.gif" alt="<?php echo TEXT_BACK_TO_HISTORY;?>" title="<?php echo TEXT_BACK_TO_HISTORY;?>" onClick="document.getElementById('confirm').style.display='none';document.getElementById('form').style.display='block'" >
</div>
<script type="text/javascript">
<!---
function orderConfirmPage(){
  document.getElementById('form_error').innerHTML = "";
  document.getElementById('form_error').style.display = 'none';
  document.getElementById('date_error').innerHTML = "";
  document.getElementById('hour_error').innerHTML = "";
  // init
  productName  = new Array();
  oldAttribute = new Array();
  text         = "";
  orderChanged = false;
  now          = new Date();
  nowMinutes   = now.getHours() * 60 + now.getMinutes();

  oldTime = '<?php echo tep_date_long(strtotime($order['torihiki_date']));?> <?php echo date('H:i', strtotime($order['torihiki_date']));?><?php echo TEXT_TIME_LINK?><?php echo date('H:i', strtotime($order['torihiki_date_end']));?>';
  oldTime_value = '<?php echo strtotime($order['torihiki_date']);?>';
  today   = '<?php echo tep_date_long(time());?>';
  today_value = '<?php echo time();?>';
  
<?php foreach($o->products as $p){?>
  productName[<?php echo $p['id'];?>] = '<?php echo $p['name'];?>';
  oldAttribute[<?php echo $p['id'];?>] = new Array();
<?php   if($p['attributes'])foreach($p['attributes'] as $a){
          if($a['option_id'] != ''){
?>
  oldAttribute[<?php echo $p['id'];?>][<?php echo $a['option_id'];?>] = new Array('<?php echo $a['option'];?>', '<?php echo $a['value'];?>');
<?php   
          } 
        }

?>
<?php }?>
  text += "<table class='information_table' summary='table'>\n";
  text += "<tr><td bgcolor='#333333' width='130'>\n";
  text += "<?php echo TEXT_REORDER_TRADE_NO_CHANGE;?>";
  text += "</td><td>\n";
  text += oldTime + "\n";
  text += "</td></tr><tr><td bgcolor='#333333'>\n";
  
  dateChanged = (document.getElementById('new_date').selectedIndex != 0);
  
  orderChanged = orderChanged || dateChanged;

  text += "<?php echo TEXT_REORDER_TRADE_CHANGE;?></td><td>";

  if(document.getElementById('new_date').selectedIndex == 0 && document.getElementById('comment').value == ''){
      document.getElementById('form_error').innerHTML = "<font color='red'><?php echo TEXT_REORDER_UNCHANGE_QTY;?></font>";
      document.getElementById('form_error').style.display = 'block';
      return false;
  }

  if(document.getElementById('new_date').selectedIndex == 0 && oldTime_value <= today_value){
      document.getElementById('date_error').innerHTML = "<font color='red'><?php echo TEXT_REORDER_CHANGE_TRADE_SELECT;?></font>";
      return false;
  }

  if(document.getElementById('shipping_time_id')){
    var shipping_time_str = $("#shipping_time_id").attr("style");
    var shippint_time_num = shipping_time_str.indexOf("none");
    shippint_time_num = parseInt(shippint_time_num);
  }else{
    var shipping_time_flag = !document.getElementById('m0');
  } 
  if(shipping_time_flag && document.getElementById('new_date').selectedIndex != 0){
      document.getElementById('hour_error').innerHTML = "<font color='red'><?php echo TEXT_REORDER_CHANGE_TRADE_SELECT;?></font>";
      $("#hour_show_error").show();
      return false;
  }

  if(dateChanged){
    newTime = document.getElementById('new_date').options[document.getElementById('new_date').selectedIndex].innerHTML + " " +document.getElementById('start_hour').value + ":" + document.getElementById('start_min').value + "<?php echo TEXT_TIME_LINK;?>" +document.getElementById('end_hour').value + ":" + document.getElementById('end_min').value;
    text += newTime + "</td></tr></table><br >\n";
  } else {
    text += oldTime + "</td></tr></table><br >\n";
  }
  
  text += "<table class='information_table' summary='table'>\n"
  text += "<tr><td bgcolor='#333333' width='130'>";
  text += "<?php echo TEXT_REORDER_COMMENT_TITLE;?>";
  text += "</td><td>\n";
  text += document.getElementById('comment').value.replace(/\</ig,"&lt;").replace(/\>/ig,"&gt;");
  text += "</td></tr>\n";
  text += "</table><br >\n"
  
  
  document.getElementById('form').style.display = 'none';
  document.getElementById('confirm').style.display = 'block';
  document.getElementById('confirm_content').innerHTML = text;
  return false;
}
-->
</script>
<?php
    }
  } else {
    // has no order or info error
    echo '<div class="comment"><div class="product_info_box">'.TEXT_REORDER_NO_ORDER_ERROR.'<div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" width="47" height="17" alt="'.TEXT_BACK_TO_HISTORY.'" title="'.TEXT_BACK_TO_HISTORY.'"></a></div></div></div>';
  }
?>
<?php } else {
  // enter basic order info
  ?>
<div class="comment">
<div class="product_info_box"><?php
echo tep_draw_form('order', tep_href_link('reorder.php'));
?>
<table class="information_table03" summary="table" border="0" cellpadding="0" cellspacing="1">
 <tr>
 <th align="left"><?php echo TEXT_REORDER_OID_TITLE;?></th>
  <td><input type='text' name='order_id_1' class="input_text" maxlength='8' style='width:80px' >-<input type='text' name='order_id_2' class="input_text" maxlength='8' style='width:80px' >
  <a href="/reorder2.php"><?php echo TEXT_REORDER_OID_FORGET;?></a><br >
  <font color='red' style='font-size:12px'><?php echo TEXT_REORDER_OID_TEXT_INFO;?></font>
  </td>
 </tr>
 <tr>
 <th align="left"><?php echo TEXT_REORDER_EMAIL_TITLE;?></th>
  <td><input type='text' name='email' class="input_text" ></td>
 </tr>
 </table>
 <table width="100%">
 <tr>
  <td colspan='2' align="right">
   
  <input type='image' src="includes/languages/japanese/images/buttons/button_continue.gif" alt="<?php echo TEXT_REORDER_NEXT;?>" title="<?php echo TEXT_REORDER_NEXT;?>" >
  <input type='image' src="includes/languages/japanese/images/buttons/button_reset.gif" alt="<?php echo TEXT_REORDER_CLEAR;?>" title="<?php echo TEXT_REORDER_CLEAR;?>" onClick="javascript:document.order.reset();return false;" >
  </td>
 </tr>
</table>
</form>
<?php }?></div>
    </div>
        <p class="pageBottom"></p>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
        <!-- right_navigation_eof //-->
      </td>           
    </tr>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
