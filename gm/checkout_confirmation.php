<?php
/*
  $Id$
*/
require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_CONFIRMATION);
require(DIR_WS_ACTIONS.'checkout_confirmation.php');
?>
<body>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- body_text //-->
<div id="layout" class="yui3-u">
<?php echo tep_draw_form('checkout_confirmation', $form_action_url, 'post', 'onSubmit="return check_confirm_payment(\''.$payment.'\')"');?>
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>


<div id="main-content">
<?php echo tep_draw_form('checkout_confirmation', $form_action_url, 'post', 'onSubmit="return check_confirm_payment(\''.$payment.'\')"');?>
<h2><?php echo HEADING_TITLE; ?></h2>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="checkout_s_link">
      <tr>
        <td width="20%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            </tr>
          </table>
        </td>
        <td width="20%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
           <td> 
        <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            </tr>
          </table>
        </td>
        <td width="20%">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
           <td> 
        <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            </tr>
          </table>
        </td>
        <td width="20%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
              <td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            </tr>
          </table>
        </td>
        <td width="20%">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
              <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr class="box_des">
        <td align="center" nowrap="nowrap" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_PRODUCTS . '</a>'; ?></td>
        <td align="center" nowrap="nowrap" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; ?></td>
        <td align="center" nowrap="nowrap" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_PAYMENT . '</a>'; ?></td>
        <td align="center" nowrap="nowrap" width="20%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td>
        <td align="center" nowrap="nowrap" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td>
      </tr>
    </table>
	
    <div id="hm-checkout-warp"><div class="checkout-title"><b><?php  echo TEXT_ORDERS_SUBMIT_TWO;?></b></div>
    <div class="checkout-bottom"> 
    <a href="javascript:void(0);" onclick="confirm_session_error();">
<?php echo
  tep_image_button('button_confirm_order.gif',
      IMAGE_BUTTON_CONFIRM_ORDER,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_confirm_order.gif\'" onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_confirm_order_hover.gif\'"');?></a></div>  
  </div>
  <div class="checkout-conent">
<table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<?php
  if ($sendto != false) {
?>
                <tr>
              <td><?php echo '<b>' . HEADING_DELIVERY_ADDRESS . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
            </tr>
            <tr>
              <td><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br>'); ?></td>
            </tr>
            <?php
    if ($order->info['shipping_method']) {
?>
            <tr>
              <td><?php echo '<b>' . HEADING_SHIPPING_METHOD . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
            </tr>
            <tr>
              <td><?php echo $order->info['shipping_method']; ?></td>
            </tr>
            <?php
    }
?>
                <?php
  }
?>
                                    <?php
  if (sizeof($order->info['tax_groups']) > 1) {
?>
                        <tr>
                          <td colspan="2"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
                          <td align="right"><b><?php echo HEADING_TAX; ?></b></td>
                          <td align="right"><b><?php echo HEADING_TOTAL; ?></b></td>
                        </tr>
                        <?php
  } else {
?>
                        <tr>
                          <td colspan="4"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
                        </tr>
                        <?php
  }

  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    //ccdd
    $product_info = tep_get_product_by_id($order->products[$i]['id'], SITE_ID, $languages_id);
    
    echo '          <tr>' . "\n" .
         '          <td width="10"></td>  
		 			<td align="left" valign="top" width="30%">' .
         $order->products[$i]['qty'] . '&nbsp;' . TEXT_ORDERS_OPTION_QTY .
         (!empty($product_info['products_attention_1_3']) &&
          tep_get_full_count_in_order2($order->products[$i]['qty'],
            $order->products[$i]['id']) ? '<br/><span style="font-size:14px">'. tep_get_full_count_in_order2($order->products[$i]['qty'], $order->products[$i]['id']) .'</span>': '') . '</td>' . "\n" .
         '            <td valign="top" width="50%">' . $order->products[$i]['name'];

    if (STOCK_CHECK == 'true') {
      echo tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty']);
    }

    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        echo '<br><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small>';
      }
    }

    echo '</td>' . "\n";

    if (sizeof($order->info['tax_groups']) > 1) echo '            <td
      valign="top" align="right" >' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";

    echo '            <td align="right" valign="top" width="20%">';
    if ($order->products[$i]['final_price'] < 0) {
      echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty'])).'</font>'.JPMONEY_UNIT_TEXT;
    } else {
      echo $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']);
    }
    echo '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
           
                                    <tr>
                    <td colspan="4"><br><b><?php echo TEXT_TRADE_DATE; ?></b><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
                  </tr>
                  <tr>
                  	 <td width="10"></td>
                     <td align="left"><?php echo TEXT_OPTION; ?></td>
                    <td colspan="2"><?php echo $torihikihouhou; ?></td>
                  </tr>
                  <tr>
                  	 <td width="10"></td>
                     <td align="left"><?php echo TEXT_EXPECT_TRADE_DATE; ?></td>
                    <td colspan="2"><?php echo str_string($date); ?>
                       <?php
                         $_SESSION['date1']=str_string($date);
                       ?> 
                     </td>
                  </tr>
                  <tr>
                  	 <td width="10"></td>
                     <td align="left"><?php echo TEXT_EXPECT_TRADE_TIME; ?></td>
                     <td colspan="2"><?php echo $hour; ?> &nbsp;<?php echo TEXT_ORDERS_OPTION_HOUR;?>&nbsp; <?php echo $min; ?> &nbsp;<?php echo TEXT_ORDERS_OPTION_MIN;?>&nbsp;
                     <?php
                      $_SESSION['hour'] = $hour;
                      $_SESSION['min'] = $min;
                     ?>
                    </td>
                  </tr>


                  <?php
	      $payment_modules->specialOutput($payment);
?>
      <tr>
        <td colspan="4"><br><b><?php echo HEADING_BILLING_INFORMATION; ?></b></td>
      </tr>
    
            <tr>
                    <td colspan="4"><?php echo '<b>' . HEADING_PAYMENT_METHOD . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
                  </tr>
                  <tr>
                  	 <td width="10"></td>
                    <td colspan="3"><?php echo payment::changeRomaji($order->info['payment_method']); ?></td>
                  </tr>
                                  <?php
  if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
    if(@$_SESSION['h_point'] < $order->info['subtotal']) {
      $point = isset($_SESSION['h_point'])?$_SESSION['h_point']:0;
    } else {
      $point = $order->info['subtotal'];
    }
    $real_point = $point;
    tep_session_register('real_point');
    tep_session_register('point');
  }
  
  if (MODULE_ORDER_TOTAL_INSTALLED) {
    $order_total_modules->process();
    echo $order_total_modules->output();
  }
  if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
  // 2005.11.17 K.Kaneko
  if(MODULE_ORDER_TOTAL_POINT_CUSTOMER_LEVEL == 'true') {
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
    if (isset($_SESSION['campaign_fee'])) {
      $get_point = ($order->info['subtotal'] + $_SESSION['campaign_fee']) * $point_rate;
    } else {
      $get_point = ($order->info['subtotal'] - (int)$point) * $point_rate;
    }
  } else {
    if ($payment == 'buyingpoint') {
      if (isset($_SESSION['campaign_fee'])) {
        $get_point = abs($order->info['subtotal'])+abs($_SESSION['campaign_fee']);
      } else {
        $get_point = abs($order->info['subtotal']);
      }
    } else {
      $get_point = 0;
    }
  }
  if ($guestchk == '1') {
    $get_point = 0;
  }
  tep_session_register('get_point');
  if(isset($customer_id)&&tep_is_member_customer($customer_id)){
  echo '<tr>' . "\n";
  if (!tep_only_buy_product()) {
    echo '<td align="right" colspan="3"><br>'.TEXT_POINT_NOW.'</td>' . "\n";
  } else {
    if ($get_point == 0) {
      echo '<td align="right" colspan="3"><br>'.TS_TEXT_POINT_NOW_TWO.'</td>' . "\n";
    } else {
      echo '<td align="right" colspan="3"><br>'.TEXT_POINT_NOW.'</td>' . "\n";
    }
  }
  echo '<td align="right"><br>'.(int)$get_point.'P</td>' . "\n";
  echo '</tr>' . "\n";
  }
  }
?>
              <?php
  if (is_array($payment_modules->modules)) {
    if ($confirmation = $payment_modules->confirmation($payment)) {
?>
      <tr>
        <td colspan="4"><br><b><?php echo HEADING_PAYMENT_INFORMATION; ?></b></td>
      </tr>


              <tr>
              		<td width="10"></td>
                    <td colspan="3"><?php echo str_replace(' />','>',$confirmation['title']); ?></td>
                  </tr>
                  <?php
      for ($i=0, $n=sizeof($confirmation['fields']); $i<$n; $i++) {
?>
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td><?php echo $confirmation['fields'][$i]['title']; ?></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td><?php echo $confirmation['fields'][$i]['field']; ?></td>
                  </tr>
                  <?php
      }
?>
                <?php
                  if ($bflag_cnt == 'View' && false) {
                    $con_show_fee = calc_buy_handle($order->info['total']); 
                    if ($con_show_fee != 0) { 
                ?>
                    <tr> 
                      <td colspan="4"><?php echo CONFIRMATION_BUYING_TEXT_TITLE; ?></td> </tr> 
                    <tr> 
                      <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                      <td><?php echo CONFIRMATION_BUYING_TEXT_FEE.$currencies->format($con_show_fee); ?></td> 
                      <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                      <td>&nbsp;</td> 
                    </tr> 
                <?php
                    }
                  }
                ?>
             <?php
    }
  }
?>
      <?php
  if (tep_not_null($order->info['comments'])) {
?>
      <tr>
        <td colspan="4"><br><b><?php echo '<b>' . HEADING_ORDER_COMMENTS . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
      </tr>
                <tr>
                	<td width="10"></td>
                   <td colspan="3"><div class="payment_comment"><?php echo
                   nl2br(htmlspecialchars($order->info['comments'])) .
                   tep_draw_hidden_field('comments', $order->info['comments']); ?>
                 </div></td>
                  </tr>
            <?php
  }
?>
    </table>

	</div>
	<div id="hm-checkout-warp">
        <div class="checkout-title"><b><?php echo TEXT_ORDERS_SUBMIT_TWO;?></b></div>
  <div class="checkout-bottom"><?php
  if (is_array($payment_modules->modules)) {
    echo $payment_modules->process_button($payment);
  }
  //character  
  if(isset($_SESSION['character'])){
    foreach($_SESSION['character'] as $ck => $cv){
      echo tep_draw_hidden_field("character[$ck]", $cv);
    }
  }
  echo '<a href="javascript:void(0);" onclick="confirm_session_error();">';
  echo tep_image_button('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_confirm_order.gif\'" onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_confirm_order_hover.gif\'"') . "</a>\n";
?></div></div></div>
</form>
</div>
<?php include('includes/float-box.php');?>
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

<!-- visites --> 
<object><noscript><img src="visites.php" alt="Statistics" style="border:0" /></noscript></object>
<!-- /visites -->
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
