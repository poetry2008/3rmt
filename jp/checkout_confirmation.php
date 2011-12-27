<?php
/*
   $Id$
 */
require('includes/application_top.php');

require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_CONFIRMATION);
require(DIR_WS_ACTIONS.'checkout_confirmation.php');
page_head();
?>
<script type="text/javascript">
<!--
var a_vars = Array();
var pagename='';
var visitesSite = 1;
var visitesURL = "<?php echo ($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER; ?>/visites.php";
<?php
require(DIR_WS_ACTIONS.'visites.js');
?>
//-->
</script>
</head>
<body> 
<div class="body_shadow" align="center"> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<?php echo tep_draw_form('checkout_confirmation', $form_action_url, 'post');?>
<table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
<tr> 
<td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
<!-- left_navigation_eof //--> </td> 
<!-- body_text //--> 
<td valign="top" id="contents"> <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>      
<table class="table_ie" border="0" width="100%" cellspacing="0" cellpadding="0"> 

<tr> 
<td><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
</tr> 
</table></td> 
<td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
<td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
<td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
<td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
</tr> 
</table></td> 
<td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td align="center" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_PRODUCTS . '</a>'; ?></td> 
<td align="center" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; ?></td> 
<td align="center" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_PAYMENT . '</a>'; ?></td> 
<td align="center" width="20%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td> 
<td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
<tr> 
<td class="main"><b>ご注文内容をご確認の上「注文する」をクリックしてください。</b></td> 
<td class="main" align="right"><?php echo tep_image_submit('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER);?></td> 
</tr> 
</table>
<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
<tr class="infoBoxContents">
<td>
<?php //这里 回显 配送的 信息?>
<?php
$shipping_method_count = count($_SESSION['shipping_method_info_arr']);
$shipping_method_info_arr = $_SESSION['shipping_method_info_arr'];
$show_some_shipping = false;
if($shipping_method_count > 1){
  $show_some_shipping = true;
}
if(!$show_some_shipping){
  echo "<div>";
}
for($i =0,$n=sizeof($order->products);$i<$n;$i++){
  $product_info = tep_get_product_by_id($order->products[$i]['id'], SITE_ID,
      $languages_id);
    if($show_some_shipping){
      echo "<div>";
    }
  echo "<div>";
    
    echo "<div>";
    echo $order->products[$i]['qty'] . '&nbsp;個' .
    (!empty($product_info['products_attention_1_3']) &&
     tep_get_full_count_in_order2($order->products[$i]['qty'],
       $order->products[$i]['id']) ? '<br><span style="font-size:10px">'.
     tep_get_full_count_in_order2($order->products[$i]['qty'],
       $order->products[$i]['id']) .'</span>': ''); 
    echo "</div>";
    
    echo "<div>";
    echo $order->products[$i]['name'];
    if (STOCK_CHECK == 'true') {
      echo tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty']);
    }
  
    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        echo '<br><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small>';
      }
    }
  echo "</div>";
    
    if (sizeof($order->info['tax_groups']) > 1) {
      echo '<div>'.tep_display_tax_value($order->products[$i]['tax']) . '%</div>';
    }
  echo '<div>' . $currencies->display_price($order->products[$i]['final_price'], 
      $order->products[$i]['tax'], $order->products[$i]['qty']) . '</div>'; 
    
    echo "</div>";
    if($show_some_shipping){
      echo "</div>";
        //这里输出这个产品的配送
        echo "<div>";
        //配送的DIV
        echo "</div>";
        echo "<div>";
        $address_id_arr = explode('|||',
            $shipping_method_info_arr[$product_info['products_id']]['shipping_address']);
        $cid = $address_id_arr[0];
        $aid = $address_id_arr[1];
        $address_info = tep_get_address_by_cid_aid($cid,$aid,true);
        foreach($address_info as $address_row){
          echo "<div>";
            echo $address_row['text'];
            echo "</div>";
        }
      echo $shipping_method_info_arr[$product_info['products_id']]['torihikihouhou'];
        echo $shipping_method_info_arr[$product_info['products_id']]['insert_torihiki_date'];
        echo "</div>";
    }
}
if(!$show_some_shipping){
  echo "</div>";
    
    //单个 配送的输出信息
    echo "<div>";
    echo "</div>";
}

?>
</td> 
</tr> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 

<?php
$payment_modules->specialOutput($payment);

?>
<tr> 
<td class="main"><b><?php echo HEADING_BILLING_INFORMATION; ?></b></td> 
</tr> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 
<tr> 
<td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
<tr class="infoBoxContents"> 
<td width="30%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
<tr> 
<td class="main"><?php echo '<b>' . HEADING_PAYMENT_METHOD . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
</tr> 
<tr> 
<td class="main"><?php echo payment::changeRomaji($order->info['payment_method']); ?></td> 
</tr> 
</table></td> 
<td width="70%" valign="top" align="right"><table width="100%" border="0" cellspacing="0" cellpadding="2"> 
<?php
if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  if(@$_POST['point'] < $order->info['subtotal']) {
    $point = isset($_POST['point'])?$_POST['point']:0;
  } else {
    $point = $order->info['subtotal'];
  }
  // add new sesssion for point named real_point
  $real_point = $point;
  tep_session_register('real_point');
  tep_session_register('point');
}
if (MODULE_ORDER_TOTAL_INSTALLED) {
  $order_total_modules->process();
  echo $order_total_modules->output();
}
if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  // ここからカスタマーレベルに応じたポイント還元率算出============================================================
  // 2005.11.17 K.Kaneko
  if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
    //設定した期間内の注文合計金額を算出------------
    $ptoday = date("Y-m-d H:i:s", time());
    $pstday_array = getdate();
    $pstday = date("Y-m-d H:i:s", mktime($pstday_array[hours],$pstday_array[mimutes],$pstday_array[second],$pstday_array[mon],($pstday_array[mday] - MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL_KIKAN),$pstday_array[year]));

    $total_buyed_date = 0;
    // ccdd
    $customer_level_total_query = tep_db_query("select * from orders where customers_id = '".$customer_id."' and date_purchased >= '".$pstday."' and site_id = ".SITE_ID);
    if(tep_db_num_rows($customer_level_total_query)) {
      while($customer_level_total = tep_db_fetch_array($customer_level_total_query)) {
        $cltotal_subtotal_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_subtotal'");
        $cltotal_subtotal = tep_db_fetch_array($cltotal_subtotal_query);

        $cltotal_point_query = tep_db_query("select value from orders_total where orders_id = '".$customer_level_total['orders_id']."' and class = 'ot_point'");
        $cltotal_point = tep_db_fetch_array($cltotal_subtotal_query);

        $total_buyed_date += ($cltotal_subtotal['value'] - $cltotal_point['value']);
      }
    }
    //----------------------------------------------
    //還元率を計算----------------------------------
    if(mb_ereg("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK)) {
      $back_rate_array = explode("||", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
      $back_rate = MODULE_ORDER_TOTAL_POINT_FEE;
      for($j=0; $j<sizeof($back_rate_array); $j++) {
        $back_rate_array2 = explode(",", $back_rate_array[$j]);
        if($back_rate_array2[2] <= $total_buyed_date) {
          $back_rate = $back_rate_array2[1];
          $back_rate_name = $back_rate_array2[0];
        }
      }
    } else {
      $back_rate_array = explode(",", MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVER_BACK);
      if($back_rate_array[2] <= $total_buyed_date) {
        $back_rate = $back_rate_array[1];
        $back_rate_name = $back_rate_array[0];
      }
    }
    //----------------------------------------------
    $point_rate = $back_rate;
  } else {
    $point_rate = MODULE_ORDER_TOTAL_POINT_FEE;
  }
  if ($order->info['subtotal'] > 0) {
    $get_point = ($order->info['subtotal'] - (int)$point) * $point_rate;
  } else {
    if ($payment == 'buyingpoint') {
      $get_point = abs($order->info['subtotal']);
    } else {
      $get_point = 0;
    }
  }
  tep_session_register('get_point');
  echo '<tr>' . "\n";
  if (!tep_only_buy_product()) {
    echo '<td align="right" class="main"><br>'.TS_TEXT_POINT_NOW.'</td>' . "\n";
  } else {
    echo '<td align="right" class="main"><br>'.TS_TEXT_POINT_NOW_TWO.'</td>' . "\n";
  } 

  echo '<td align="right" class="main"><br>'.(int)$get_point.'&nbsp;P</td>' . "\n";
  echo '</tr>' . "\n";
}
?> 
</table></td> 
</tr> 
</table></td> 
</tr> 
<?php
if (is_array($payment_modules->modules)) {

  if ($confirmation = $payment_modules->confirmation($payment)) {
    ?> 
      <tr> 
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
      </tr> 
      <tr> 
      <td class="main"><b><?php echo HEADING_PAYMENT_INFORMATION; ?></b></td> 
      </tr> 
      <tr> 
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
      </tr> 
      <tr> 
      <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
      <tr class="infoBoxContents"> 
      <td><table border="0" cellspacing="0" cellpadding="2"> 
      <tr> 
      <td class="main" colspan="4"><?php
      echo $confirmation['title']; ?></td> 
      </tr> 
      <?php
      for ($i=0, $n=sizeof($confirmation['fields']); $i<$n; $i++) {
        ?> 
          <tr> 
          <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
          <td class="main"><?php echo $confirmation['fields'][$i]['title']; ?></td> 
          <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
          <td class="main"><?php echo $confirmation['fields'][$i]['field']; ?></td> 
          </tr> 
          <?php
      }
    ?> 
      <?php

      ?>
      </table></td> 
      </tr> 
      </table></td> 
      </tr> 
      <?php
  }
}
?> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 
<?php

if (tep_not_null($order->info['comments'])) {
  ?> 
    <tr> 
    <td class="main"><?php echo '<b>' . HEADING_ORDER_COMMENTS . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
    </tr> 
    <tr> 
    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
    </tr> 
    <tr> 
    <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
    <tr class="infoBoxContents"> 
    <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
    <tr> 
    <td class="main"><div class="payment_comment"><?php echo nl2br(htmlspecialchars($order->info['comments'])) . tep_draw_hidden_field('comments', $order->info['comments']); ?></div></td> 
    </tr> 
    </table></td> 
    </tr> 
    </table></td> 
    </tr> 
    <tr> 
    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
    </tr> 
    <?php
}
?> 
<tr> 
<td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
<tr> 
<td class="main"><b>ご注文内容をご確認の上「注文する」をクリックしてください。</b></td>
<td align="right" class="main"> <?php
if (is_array($payment_modules->modules)) {
  echo $payment_modules->process_button($payment);
}
//character  

if(isset($_SESSION['character'])){
  foreach($_SESSION['character'] as $ck => $cv){
    echo tep_draw_hidden_field("character[$ck]", $cv);
  }
}


echo tep_image_submit('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER) . '</form>' . "\n";
?> </td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 
</table></td> 
<!-- body_text_eof //--> 
<td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
<!-- right_navigation_eof //--> </td> 
</table> 
<!-- body_eof //--> 
<!-- footer //--> 
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
<!-- footer_eof //--> 
</div> 
<!-- /visites --> 
<object>
<noscript>
<img src="visites.php" alt="Statistics" style="border:0" />
</noscript>
</object>
<!-- /visites -->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
