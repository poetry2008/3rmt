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
</head>
<body>
<div align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" summary="table">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //--> </td>
      <!-- body_text //-->
      <td id="contents" valign="top">
        <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
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
        echo '<div class="comment">'.TEXT_DELETE_ORDER_SUCCESS.' <div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" width="74" height="25" alt="'.TEXT_BACK_TO_HISTORY.'" title="'.TEXT_BACK_TO_HISTORY.'"></a></div></div>';
      //} else if ($date && $hour && $minute && ($time < (time() - MINUTES * 60) or $time > (time() + (7*86400)))) {
        // time error
        //echo '<div class="comment">'.TEXT_INFO_FOR_TRADE.' <div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back_home.gif" width="74" height="25" alt="'.TEXT_BACK_TO_TOP.'" title="'.TEXT_BACK_TO_TOP.'"></a></div></div>';
      } else {
        // update time
        

        // update character
        if (isset($_POST['character']) && is_array($_POST['character'])){
          foreach($_POST['character'] as $pid=>$character){
            // ccdd
            tep_db_query("
                update `".TABLE_ORDERS_PRODUCTS."` 
                set `products_character`='".mysql_real_escape_string($character)."' 
                where `orders_id`='".$oID."' 
                  and `products_id`='".$pid."'
            ");
          }
        }
        // update attributes
        if($o->products){
          foreach($o->products as $p){
            if(isset($p['attributes']) && $p['attributes']){
              foreach($p['attributes'] as $a) {
                if(isset($_POST['id'][$p['id']])) {
                  // old attribute
                  // ccdd
                  $attributes = tep_db_fetch_array(tep_db_query("
                        select * 
                        from `".TABLE_PRODUCTS_ATTRIBUTES."` 
                        where `products_attributes_id`='".$a['attributes_id']."'
                  "));
                  if(isset($_POST['id'][(int)$p['id']][(int)$attributes['options_id']]) && $_POST['id'][(int)$p['id']][(int)$attributes['options_id']]){
                    // new option
                    // ccdd
                    $option = tep_db_fetch_array(tep_db_query("
                          select * 
                          from `".TABLE_PRODUCTS_OPTIONS."` 
                          where `products_options_id`='".$attributes['options_id']."'
                    "));
                    // new attribute
                    // ccdd
                    $nattribute = tep_db_fetch_array(tep_db_query("
                          select * 
                          from `".TABLE_PRODUCTS_ATTRIBUTES."` 
                          where `products_id`='".$p['id']."' 
                            and `options_id`='".$attributes['options_id']."' 
                            and `options_values_id`='".$_POST['id'][(int)$p['id']][(int)$attributes['options_id']]."'
                    "));
                    // new option value
                    // ccdd
                    $value = tep_db_fetch_array(tep_db_query("
                          select * 
                          from `".TABLE_PRODUCTS_OPTIONS_VALUES."` 
                          where `products_options_values_id`='".$_POST['id'][(int)$p['id']][(int)$attributes['options_id']]."'
                    "));
                    // execute update`
                    // ccdd
                    $update_query = tep_db_query("
                        update `".TABLE_ORDERS_PRODUCTS_ATTRIBUTES."` 
                        set `products_options_values`='".$value['products_options_values_name']."',
                            `attributes_id`='".$nattribute['products_attributes_id']."' 
                        where `orders_id`='".$oID."' 
                          and `products_options`='".$option['products_options_name']."' 
                          and `attributes_id`='".$a['attributes_id']."'
                    ");
                  }
                }
              }
            }
          }
        }
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
        echo '<div class="comment">'.TEXT_CHANGE_ORDER_CONFIRM_EMAIL.' <div align="right"><a href="/"><img src="includes/languages/japanese/images/buttons/button_back_home.gif" width="74" height="25" alt="'.TEXT_BACK_TO_TOP.'" title="'.TEXT_BACK_TO_TOP.'"></a></div></div>';
        // sent mail to customer
        // ccdd
        $mail    = tep_db_fetch_array(tep_db_query("
              select * 
              from ".TABLE_ORDERS_MAIL." 
              where orders_status_id=17 
                and (site_id='0' or site_id = '" . SITE_ID . "')
              order by site_id DESC
        "));
        // $mail_title = "注文内容の変更を承りました";
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
  /*if ($_SESSION['payment'] == 'convenience_store') {
    $convenience_sid = str_replace('-', "", $insert_id);
  
    $pay_comments = '取引コード' . $convenience_sid ."\n";
  $pay_comments .= '郵便番号:' . $_POST['convenience_store_zip_code'] ."\n";
  $pay_comments .= '住所1:' . $_POST['convenience_store_address1'] ."\n";
  $pay_comments .= '住所2:' . $_POST['convenience_store_address2'] ."\n";
  $pay_comments .= '氏:' . $_POST['convenience_store_l_name'] ."\n";
  $pay_comments .= '名:' . $_POST['convenience_store_f_name'] ."\n";
  $pay_comments .= '電話番号:' . $_POST['convenience_store_tel'] ."\n";
  $pay_comments .= '接続URL:' . tep_href_link('convenience_store_chk.php', 'sid=' . $convenience_sid, 'SSL');
  
  $comments = $pay_comments ."\n".$comments;
  }
  */

// load the before_process function from the payment modules
  $payment_modules->before_process($payment_code);

  require(DIR_WS_CLASSES . 'order_total.php');
  $order_total_modules = new order_total;

  $order_totals = $order_total_modules->process();
  
  # Random

  
  # Select
  //$cnt = strlen($NewOid);

  // initialized for the email confirmation
  $products_ordered = '';
  $subtotal = 0;
  $total_tax = 0;

  for ($i=0, $n=sizeof($o->products); $i<$n; $i++) {
  //------insert customer choosen option to order--------
    $attributes_exist = '0';
    $products_ordered_attributes = '';
    if (isset($o->products[$i]['attributes'])) {
      for ($j=0, $n2=sizeof($o->products[$i]['attributes']); $j<$n2; $j++) {
        if (DOWNLOAD_ENABLED == 'true') {
          $attributes_query = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_attributes_id, pad.products_attributes_maxdays, pad.products_attributes_maxcount , pad.products_attributes_filename 
                               from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa 
                               left join " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad
                                on pa.products_attributes_id=pad.products_attributes_id
                               where pa.products_id = '" . $o->products[$i]['id'] . "' 
                                and pa.options_id = '" . $o->products[$i]['attributes'][$j]['option_id'] . "' 
                                and pa.options_id = popt.products_options_id 
                                and pa.options_values_id = '" . $o->products[$i]['attributes'][$j]['value_id'] . "' 
                                and pa.options_values_id = poval.products_options_values_id 
                                and popt.language_id = '" . $languages_id . "' 
                                and poval.language_id = '" . $languages_id . "'";
          //ccdd
          $attributes = tep_db_query($attributes_query);
        } else {
          $sql = "select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix, pa.products_at_quantity, pa.products_attributes_id from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa 
          where pa.products_id = '" . $o->products[$i]['id'] . "' 
          and pa.options_id = '" . $o->products[$i]['attributes'][$j]['option_id'] . "' 
          and pa.options_id = popt.products_options_id 
          and pa.options_values_id = '" . $o->products[$i]['attributes'][$j]['value_id'] . "' 
          and pa.options_values_id = poval.products_options_values_id 
          and popt.language_id = '" . $languages_id . "' 
          and poval.language_id = '" . $languages_id . "'";

          $attributes = tep_db_query($sql);
        }
        $attributes_values = tep_db_fetch_array($attributes);
        $products_ordered_attributes .= "\n" . $attributes_values['products_options_name'] 
        . str_repeat('　',intval((18-strlen($attributes_values['products_options_name']))/2))
        . '：' . $attributes_values['products_options_values_name'];
      }
    }
 
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
    
    //------insert customer choosen option eof ----
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
    
    // ccdd
    $product_info = tep_get_product_by_id($o->products[$i]['id'], SITE_ID ,$languages_id);
    
    $products_ordered .= $products_ordered_attributes . "\n";
    $products_ordered .= TEXT_REORDER_QTY_SUM.str_repeat('　',intval(($attribute_max_len - mb_strlen(TEXT_REORDER_QTY_SUM, 'utf-8')))).'：' . $o->products[$i]['qty'] . TEXT_REORDER_QTY . tep_get_full_count2($o->products[$i]['qty'], $o->products[$i]['id']) . "\n";
    if(tep_not_null($o->products[$i]['character'])) {
      $products_ordered .= TEXT_REORDER_CHARACTER . (EMAIL_USE_HTML === 'true' ? htmlspecialchars($o->products[$i]['character']) : $o->products[$i]['character']) . "\n";
    }

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
  $email_order .= TEXT_REORDER_PRODUCT_EMAIL . "\n";
  $email_order .= '------------------------------------------' . "\n";
  $email_order .= $products_ordered . "\n";
  $email_order .= TEXT_REORDER_TRADE_DATE . str_string($_date) . $_hour . TIME_HOUR_TEXT . $_minute . TIME_MIN_TEXT . "～" . $end_hour.TIME_HOUR_TEXT.$end_min.TEXT_REORDER_TWENTY_FOUR_HOUR . "\n";

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
        echo '<div class="comment">'.TEXT_REORDER_COMMERN_STATUS.'<div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" width="74" height="25" alt="'.TEXT_BACK_TO_HISTORY.'" title="'.TEXT_BACK_TO_HISTORY.'"></a></div></div>';
    } else {
        // edit order
?>
<div class="comment">
<div id='form'>
<form action="reorder.php" method="post" name="order">
<input type="hidden" name="dummy" value="<?php echo TEXT_REORDER_DUMMY_WIDTH;?>">
<input type='hidden' name='order_id' value='<?php echo $order['orders_id']?>' >
<input type='hidden' name='email' value='<?php echo $order['customers_email_address']?>' >
<div id="form_error" style="display:none"></div>
<table class="information_table" summary="table">
 <tr>
 <td width="30%" bgcolor="#eeeeee"><?php echo TEXT_REORDER_OID_TITLE;?></td>
  <td><?php echo $order['orders_id']?></td>
 </tr>
 <tr>
 <td bgcolor="#eeeeee"><?php echo TEXT_REORDER_OID_NAME;?></td>
  <td><?php echo $order['customers_name']?></td>
 </tr>
 <tr>
 <td bgcolor="#eeeeee"><?php echo TEXT_REORDER_EMAIL_TITLE;?></td>
  <td><?php echo $order['customers_email_address']?></td>
 </tr>
 <tr>
 <td bgcolor="#eeeeee"><?php echo TEXT_REORDER_TRADE_NO_CHANGE;?></td>
  <td id='old_time'><?php echo tep_date_long(strtotime($order['torihiki_date']))?> <?php echo date('H:i', strtotime($order['torihiki_date']));?>～<?php echo date('H:i', strtotime($order['torihiki_date_end']));?></td>
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
  $j = 0;
  foreach($shipping_time_start as $shipping_key=>$shipping_value){
    foreach($shipping_value as $sh_key=>$sh_value){
      
      $sh_start_array = explode(':',$sh_value);
      $sh_end_array = explode(':', $shipping_time_end[$shipping_key][$sh_key]);
      for($i = (int)$sh_start_array[0];$i <= (int)$sh_end_array[0];$i++){
        if(isset($ship_time_array[$i]) && $ship_time_array[$i] != ''){
          if($ship_temp_array[$i] != $j){$ship_array[$i]++;}
          $ship_time_array[$i] .= '|'.$sh_value.','.$shipping_time_end[$shipping_key][$sh_key];
        }else{
          $ship_time_array[$i] = $sh_value.','.$shipping_time_end[$shipping_key][$sh_key]; 
          $ship_temp_array[$i] = $j;
        }
      } 
    }
    
    $j++;  
  }

  $s_array = array();
  foreach($ship_array as $ship_k=>$ship_v){
    if($ship_v >= $products_num-1){
      $s_array[$ship_k] = $ship_v;
    } 
  } 
  $ship_array = $s_array;
  $shipp_array = array_keys($ship_array);
  sort($shipp_array);
  $ship_new_array = array();
  foreach($shipp_array as $shipp_key=>$shipp_value){
  
    $ship_1_array = explode('|',$ship_time_array[$shipp_value]);
    foreach($ship_1_array as $ship_1_value){

      $ship_2_array = explode(',',$ship_1_value);
      $ship_3_array[$shipp_key][] = $ship_2_array[0];
      $ship_4_array[$shipp_key][] = $ship_2_array[1];
    } 
  }

  foreach($ship_3_array as $ship_3_key=>$ship_3_value){

    natsort($ship_3_array[$ship_3_key]); 
    natsort($ship_4_array[$ship_3_key]);
    $ship_new_array[] = end($ship_3_array[$ship_3_key]).','.current($ship_4_array[$ship_3_key]);
  }

  foreach($ship_new_array as $_s_key=>$_s_value){
      $s_temp_array = explode('|',$_s_value);    
      sort($s_temp_array);
      $ship_new_array[$_s_key] = implode('|',$s_temp_array); 
  } 
  $max_time_str = implode('||',$shipp_array);
  $min_time_str = implode('||',$ship_new_array);
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

    $max_time_str = implode('||',array_keys($shi_time_array));
    $min_time_str = implode('||',$shi_time_array);
  }
  

  //可配送时间区域
  $work_start = $max_time_str;
  $work_end = $min_time_str;

  //当日起几日后可以收货
  $db_set_day = max($shipping_time_array['db_set_day']);
  //可选收货期限
  $shipping_time = max($shipping_time_array['shipping_time']);

  $today = getdate();
  $m_num = $today['mon'];
  $d_num = $today['mday']+$db_set_day;
  $year = $today['year'];
    
  $hours = date('H');
  $mimutes = date('i');
?>
 <tr>
 <td bgcolor="#eeeeee"><?php echo TEXT_REORDER_TRADE_CHANGE;?></td>
  <td>
  <select name="date" id="new_date" onChange="selectDate('<?php echo $work_start; ?>', '<?php echo $work_end; ?>',this.value);$('#date_error').html('');$('#hour_error').html('');">
  <option value=""><?php echo EXPECT_DATE_SELECT;?></option>
    <?php
          $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
          $newarr = array(TEXT_DATE_MONDAY, TEXT_DATE_TUESDAY, TEXT_DATE_WEDNESDAY, TEXT_DATE_THURSDAY, TEXT_DATE_FRIDAY, TEXT_DATE_STATURDAY, TEXT_DATE_SUNDAY);
    for($j = 0;$j < $shipping_time;$j++){
      
      echo '<option value="'.date("Y-m-d", mktime(0,0,0,$m_num,$d_num+$j,$year)).'">'.str_replace($oarr, $newarr, date("Y".DATE_YEAR_TEXT."m".DATE_MONTH_TEXT."d".DATE_DAY_TEXT."（l）", mktime(0,0,0,$m_num,$d_num+$j,$year))).'</option>' . "\n";

    }
    ?> 
   </select><br>
   <span id="date_error"></span>
</td></tr>
<tr>
<td colspan="2" id="table_td_p0">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="shipping_box">
  <tr id="shipping_list" style="display:none;">
  <td width="30%" class="main" bgcolor="#eeeeee"><?php echo TEXT_EXPECT_TRADE_TIME; ?></td>
  <td id="shipping_list_show"></td>
  </tr>
</table>
<table border="0" cellpadding="0" cellspacing="0" style=" position:absolute; width:504px;">
<tr id="shipping_list_min" style="display:none;">
 <td width="29%">&nbsp;<input type="hidden" id="ele_id" name="ele" value=""></td>
 <td id="shipping_list_show_min">
 </td>
 </tr>
</table>
</td></tr>
<tr>
<td></td>
<td><span id="hour_error"></span></td>
</tr>
<tr><td colspan="2">
  <div><?php echo TEXT_REORDER_TRADE_TEXT;?></div>
  </td>
 </tr>
</table>
<?php foreach($o->products as $key => $value){
  // for multi products
  ?>
<br>
<table class="information_table" id='product_<?php echo $value['id'];?>' summary="table">
 <tr>
 <td width="30%" bgcolor="#eeeeee"><?php echo TEXT_REORDER_P_PRODUCT_NAME;?></td>
  <td name='products_names'><?php echo $value['name'];?></td>
 </tr>
<?php if($value['character']) {?>
 <tr>
 <td bgcolor="#eeeeee"><?php echo TEXT_REORDER_P_PRODUCT_CHARACTER;?></td>
  <td><input type='text' id='character_<?php echo $value['id'];?>' name='character[<?php echo $value['id'];?>]' value="<?php echo htmlspecialchars($value['character'])?>" class="input_text" ></td>
 </tr>
<?php }?>
<?php if(isset($value['attributes']) && $value['attributes'])foreach ($value['attributes'] as $att) {?>
 <tr>
  <td bgcolor="#eeeeee"><?php echo $att['option'].TEXT_REORDER_NO_CHANGE;?></td>
  <td><?php echo $att['value'];?></td>
 </tr>
<?php }?>
 <?php
  // ccdd
        /*
        $products_attributes_query = tep_db_query("
            select count(*) as total 
            from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib 
            where patrib.products_id='" . $value['id'] . "' 
              and patrib.options_id = popt.products_options_id 
              and popt.language_id = '" . $languages_id . "'
        ");
        $products_attributes = tep_db_fetch_array($products_attributes_query);
         */
        if (false) {
          //ccdd
          $products_options_name_query = tep_db_query("
              select distinct popt.products_options_id, 
                              popt.products_options_name 
              from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib 
              where patrib.products_id='" . $value['id'] . "' 
                and patrib.options_id = popt.products_options_id 
                and popt.language_id = '" . $languages_id . "'
              ");
          while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {
            $selected = 0;
            $products_options_array = array();
            echo '<tr><td bgcolor="#eeeeee">' . $products_options_name['products_options_name'] . TEXT_REORDER_CHANGE .'</td><td>' . "\n";
            // ccdd
            $products_options_query = tep_db_query("
                select pov.products_options_values_id, 
                       pov.products_options_values_name, 
                       pa.options_values_price, 
                       pa.price_prefix, 
                       pa.products_at_quantity, 
                       pa.products_at_quantity 
                from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov 
                where pa.products_id = '" . $value['id'] . "' 
                  and pa.options_id = '" . $products_options_name['products_options_id'] . "' 
                  and pa.options_values_id = pov.products_options_values_id 
                  and pov.language_id = '" . $languages_id . "' 
                order by pa.products_attributes_id
            ");
            while ($products_options = tep_db_fetch_array($products_options_query)) {
              if($products_options['products_at_quantity'] > 0) {
                $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
                if ($products_options['options_values_price'] != '0') {
                  $products_options_array[sizeof($products_options_array)-1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) .') ';
                }
              }
            }
            $products_options_array = array_merge(array(array('id' => '', 'text' => '--')), $products_options_array);
            echo tep_draw_pull_down_menu(
                'id['.$value['id'].'][' . $products_options_name['products_options_id'] . ']', 
                $products_options_array, 
                isset($cart->contents[$value['id']]['attributes'][$products_options_name['products_options_id']])?  $cart->contents[$value['id']]['attributes'][$products_options_name['products_options_id']]:'');
            echo '</td></tr>';
          }
          //echo '</table>';
        }
    ?>
</table>
<?php }?>
<br>
<table class="information_table" summary="table">
<tr>
<td width="30%" bgcolor="#eeeeee"><?php echo TEXT_REORDER_COMMENT_TITLE;?></td>
<td><textarea name='comment' id='comment' rows="5"></textarea></td>
</tr>
</table>
<br>
<p align="center">
<input type='image' src="includes/languages/japanese/images/buttons/button_submit.gif" alt="<?php echo TEXT_REORDER_CONFRIM;?>" title="<?php echo TEXT_REORDER_CONFRIM;?>" onClick="return orderConfirmPage();" >
<input type='image' src="includes/languages/japanese/images/buttons/button_reset.gif" alt="<?php echo TEXT_REORDER_CLEAR;?>" title="<?php echo TEXT_REORDER_CLEAR;?>" onClick="javascript:document.order.reset();selectDate('','','');return false;" >
</p>
</form>
</div>
<div id='confirm' style='display:none; text-align: center;'>
  <div id='confirm_content'></div>
  <input type='image' src="includes/languages/japanese/images/buttons/button_submit2.gif" alt="<?php echo TEXT_REORDER_CONFRIM_INFO;?>" title="<?php echo TEXT_REORDER_CONFRIM_INFO;?>" onClick="document.order.submit()" >
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
  oldCharacter = new Array();
  oldAttribute = new Array();
  text         = "";
  orderChanged = false;
  now          = new Date();
  nowMinutes   = now.getHours() * 60 + now.getMinutes();

  oldTime = '<?php echo tep_date_long(strtotime($order['torihiki_date']));?> <?php echo date('H:i', strtotime($order['torihiki_date']));?>～<?php echo date('H:i', strtotime($order['torihiki_date_end']));?>';
  oldTime_value = '<?php echo strtotime($order['torihiki_date']);?>';
  today   = '<?php echo tep_date_long(time());?>';
  today_value = '<?php echo time();?>';
  
<?php foreach($o->products as $p){?>
  productName[<?php echo $p['id'];?>] = '<?php echo $p['name'];?>';
  oldCharacter[<?php echo $p['id'];?>] = "<?php echo htmlspecialchars(addslashes($p['character']));?>";
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
  text += "<tr><td bgcolor='#eeeeee' width='130'>\n";
  text += "<?php echo TEXT_REORDER_TRADE_NO_CHANGE;?>";
  text += "</td><td>\n";
  text += oldTime + "\n";
  text += "</td></tr><tr><td bgcolor='#eeeeee'>\n";
  
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
    var shipping_time_flag = shippint_time_num > 0 ;
  }else{
    var shipping_time_flag = !document.getElementById('m0');
  } 
  if(shipping_time_flag && document.getElementById('new_date').selectedIndex != 0){
      document.getElementById('hour_error').innerHTML = "<font color='red'><?php echo TEXT_REORDER_CHANGE_TRADE_SELECT;?></font>";
      return false;
  }

  if(dateChanged){
    newTime = document.getElementById('new_date').options[document.getElementById('new_date').selectedIndex].innerHTML + " " +document.getElementById('start_hour').value + ":" + document.getElementById('start_min').value + "～" +document.getElementById('end_hour').value + ":" + document.getElementById('end_min').value;
    text += newTime + "</td></tr></table><br >\n";
  } else {
    text += oldTime + "</td></tr></table><br >\n";
  }
  
  for(i in productName){
    text += "<table class='information_table' summary='table'>\n";
    text += "<tr><td width='130' bgcolor='#eeeeee'><?php echo TEXT_REORDER_P_PRODUCT_NAME;?></td><td>\n";
    text += productName[i] + "\n";
    text += "</td></tr>";

    if(oldCharacter[i] != ''){
      text += "<tr><td bgcolor='#eeeeee' width='130'>\n";
      text += "<?php echo TEXT_REORDER_P_PRODUCT_CHARACTER.TEXT_REORDER_NO_CHANGE;?>";
      text += "</td><td>\n";
      text += oldCharacter[i] + "\n";
      text += "</td></tr>";
      text += "<tr><td bgcolor='#eeeeee'>\n";
      text += "<?php echo TEXT_REORDER_P_PRODUCT_CHARACTER.TEXT_REORDER_CHANGE;?>";
      text += "</td><td>\n";
      if(document.getElementById('character_'+i)){
      text += document.getElementById('character_'+i).value.replace(/\</ig,"&lt;").replace(/\>/ig,"&gt;") + "\n";
      text += "</td></tr>";
      orderChanged = orderChanged || (oldCharacter[i] != document.getElementById('character_'+i).value);
      }
    }

    
    

    for(j in oldAttribute[i]){
      text += "<tr><td bgcolor='#eeeeee'>\n";
      text += oldAttribute[i][j][0] + "<?php echo TEXT_REORDER_NO_CHANGE;?>\n"
      text += "</td><td>\n";
      text += oldAttribute[i][j][1] + "\n";
      text += "</td></tr><tr><td bgcolor='#eeeeee'>\n";
      text += oldAttribute[i][j][0];
      text += "<?php echo TEXT_REORDER_CHANGE;?></td><td>\n";
      if(document.getElementById('id[' + i + '][' + j + ']')){
      if (document.getElementById('id[' + i + '][' + j + ']').selectedIndex != 0) {
        text += document.getElementById('id[' + i + '][' + j + ']').options[document.getElementById('id[' + i + '][' + j + ']').selectedIndex].innerHTML + "\n";
      } else {
        text += oldAttribute[i][j][1] + "\n";
      }
      text += "</td></tr>\n";
      orderChanged = orderChanged || (document.getElementById('id[' + i + '][' + j + ']').selectedIndex != 0);
      }
    }
    text += "</table><br >\n";
  }

  text += "<table class='information_table' summary='table'>\n"
  text += "<tr><td bgcolor='#eeeeee' width='130'>";
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
    echo '<div class="comment">'.TEXT_REORDER_NO_ORDER_ERROR.'<div align="right"><a href="javascript:void(0);" onclick="history.go(-1)"><img src="includes/languages/japanese/images/buttons/button_back.gif" width="74" height="25" alt="'.TEXT_BACK_TO_HISTORY.'" title="'.TEXT_BACK_TO_HISTORY.'"></a></div></div>';
  }
?>
<?php } else {
  // enter basic order info
  ?>
<div class="comment">
<form action="reorder.php" method="post" name='order'>
<input type="hidden" name="dummy" value="<?php echo TEXT_REORDER_DUMMY_WIDTH;?>">   
<table class="information_table" summary="table">
 <tr>
 <td align="left" bgcolor='#eeeeee'><?php echo TEXT_REORDER_OID_TITLE;?></td>
  <td><input type='text' name='order_id_1' class="input_text" maxlength='8' style='width:80px' >-<input type='text' name='order_id_2' class="input_text" maxlength='8' style='width:80px' >
  <a href="/reorder2.php"><?php echo TEXT_REORDER_OID_FORGET;?></a><br >
  <font color='red' style='font-size:12px'><?php echo TEXT_REORDER_OID_TEXT_INFO;?></font>
  </td>
 </tr>
 <tr>
 <td align="left" bgcolor='#eeeeee'><?php echo TEXT_REORDER_EMAIL_TITLE;?></td>
  <td><input type='text' name='email' class="input_text" ></td>
 </tr>
 <tr>
  <td colspan='2' align="center">
   
  <input type='image' src="includes/languages/japanese/images/buttons/button_continue.gif" alt="<?php echo TEXT_REORDER_NEXT;?>" title="<?php echo TEXT_REORDER_NEXT;?>" >
  <input type='image' src="includes/languages/japanese/images/buttons/button_reset.gif" alt="<?php echo TEXT_REORDER_CLEAR;?>" title="<?php echo TEXT_REORDER_CLEAR;?>" onClick="javascript:document.order.reset();return false;" >
  </td>
 </tr>
</table>
</form>
<?php }?>
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
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
