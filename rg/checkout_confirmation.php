<?php
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
<body><div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
      <?php echo tep_draw_form('checkout_confirmation', $form_action_url, 'post');?>
      <div class="pageHeading"><img align="top" alt="" src="images/menu_ico.gif"><h1><?php echo HEADING_TITLE ; ?></h1></div>      
      <div class="comment">
        <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
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
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr> 
        <tr><td>
        <table border="0" width="100%" cellspacing="0" cellpadding="0" class="rg_pay_info">
        <tr>
        <td class="main"><b>ご注文内容をご確認の上「注文する」をクリックしてください。</b></td>
        <td class="main" align="right"><?php echo tep_image_submit('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER);?></td>
        </tr>
        </table>
</td></tr>
          <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr> 
        <tr> 
          <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
              <tr> 
                <?php
  if ($sendto != false) {
?> 
                <td width="30%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                    <tr> 
                      <td class="main"><?php echo '<b>' . HEADING_DELIVERY_ADDRESS . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
                    </tr> 
                    <tr> 
                      <td class="main"><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br>'); ?></td> 
                    </tr> 
                    <?php
    if ($order->info['shipping_method']) {
?> 
                    <tr> 
                      <td class="main"><?php echo '<b>' . HEADING_SHIPPING_METHOD . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
                    </tr> 
                    <tr> 
                      <td class="main"><?php echo $order->info['shipping_method']; ?></td> 
                    </tr> 
                    <?php
    }
?> 
                  </table></td> 
                <?php
  }
?> 
                <td width="<?php echo (($sendto != false) ? '70%' : '100%'); ?>" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                    <tr> 
                      <td>
                      <table class="infoBoxContents"> 
                          <?php
  if (sizeof($order->info['tax_groups']) > 1) {
?> 
                          <tr> 
                            <td class="main" colspan="2"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
                            <td class="smallText" align="right"><b><?php echo HEADING_TAX; ?></b></td> 
                            <td class="smallText" align="right"><b><?php echo HEADING_TOTAL; ?></b></td> 
                          </tr> 
                          <?php
  } else {
?> 
                          <tr> 
                            <td class="main"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
                          </tr>
                <tr><td><table width="100%"> 
                          <?php
  }
  /**************/
  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    //ccdd
    $product_info = tep_get_product_by_id($order->products[$i]['id'], SITE_ID, $languages_id);
    
    echo '          <tr>' . "\n" .
         '            <td class="main" align="center" valign="top" width="150">' . $order->products[$i]['qty'] . '&nbsp;個' . (!empty($product_info['products_attention_1_3']) && tep_get_full_count_in_order2($order->products[$i]['qty'], $order->products[$i]['id']) ? '<br><span style="font-size:10px">'. tep_get_full_count_in_order2($order->products[$i]['qty'], $order->products[$i]['id']) .'</span>': '') . '</td>' . "\n" .
         '            <td class="main" valign="top">' . $order->products[$i]['name'];

    if (STOCK_CHECK == 'true') {
      echo tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty']);
    }

    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        echo '<br><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small>';
      }
    }

    echo '</td>' . "\n";

    if (sizeof($order->info['tax_groups']) > 1) echo '            <td class="main" valign="top" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";

    echo '            <td class="main" align="right" valign="top">' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?> 
                        </table></td></tr></table></td> 
                    </tr> 
                  </table></td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
        </tr> 
    
    
          <tr> 
            <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
                <tr> 
                  <td>
<table class="infoBoxContents">
  <tr>
  <td class="main"><b><?php echo TEXT_TORIHIKI_TITLE; ?></b><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
  </tr>
  <tr>
    <td>
      <table width="100%">
      <tr>
        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
      <td class="main"><?php echo TEXT_TORIHIKIHOUHOU; ?></td>
        <td class="main"><?php echo $torihikihouhou; ?></td>
      </tr>
      <tr>
        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
      <td class="main" width="30%"><?php echo TEXT_TORIHIKIKIBOUBI; ?></td>
        <td class="main" width="70%"><?php echo str_string($date); ?></td>
      </tr>
      <tr>
        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
      <td class="main"><?php echo TEXT_TORIHIKIKIBOUJIKAN; ?></td>
        <td class="main">
      <?php echo $hour; ?>
      &nbsp;時&nbsp;
      <?php echo $min; ?>
      &nbsp;分&nbsp;
      </td>
      </tr>
      </table>
    </td>
  </tr>
</table>
          
          </td> 
                </tr> 
              </table></td> 
          </tr> 
          <tr> 
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
          </tr> 
<?php
            if(method_exists(
                             $payment_modules,
                             'specialOutput'
                             )){
              call_user_method ('specialOutput',$payment_modules);

              //              call_user$payment_modules
              
            }
?>
    
        <tr> 
          <td  style="color: #000; font-size: 12px; padding: 10px; background: url(images/design/box/dot.gif) bottom repeat-x;">
            <b><?php echo HEADING_BILLING_INFORMATION; ?></b>
          </td> 
        </tr> 
        <tr> 
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
        </tr> 
        <tr> 
          <td>
          <table class="infoBoxContents"> 
              <tr> 
                <td width="30%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                    <tr> 
                      <td class="main"><?php echo '<b>' . HEADING_PAYMENT_METHOD . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td> 
                    </tr> 
                    <tr> 
                      <td class="main"><?php echo $order->info['payment_method']; ?></td> 
                    </tr> 
                  </table></td> 
                <td width="70%" valign="top" align="right"><table border="0" cellspacing="0" cellpadding="2"> 
                    <?php
  if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') {
    if($_POST['point'] < $order->info['subtotal']) {
    $point = $_POST['point'];
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
  // ここまでカスタマーレベルに応じたポイント還元率算出============================================================
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
    echo '<td align="right" class="main"><br>'.TEXT_POINT_NOW.'</td>' . "\n";
  } else {
    echo '<td align="right" class="main"><br>'.TEXT_POINT_NOW_TWO.'</td>' . "\n";
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
    if ($confirmation = $payment_modules->confirmation()) {
?> 
        <tr> 
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
        </tr> 
        <tr> 
          <td style="color: #000; font-size: 12px; padding: 10px; background: url(images/design/box/dot.gif) bottom repeat-x;"><b><?php echo HEADING_PAYMENT_INFORMATION; ?></b></td> 
        </tr> 
        <tr> 
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
        </tr> 
        <tr> 
          <td>
            <table class="infoBoxContents"> 
              <tr> 
                <td>
                  <table border="0" cellspacing="0" cellpadding="2"> 
                    <tr> 
                      <td class="main" colspan="4"><?php echo str_replace('<br />', '<br>', $confirmation['title']); ?></td> 
                    </tr> 
                    <?php
                    if (!isset($confirmation['fields'])) $confirmation['fields'] = NULL;
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
                  if ($bflag_cnt == 'View' && false) {
                  $con_show_fee = calc_buy_handle($order->info['total']); 
                ?>
                    <tr> 
                      <td class="main" colspan="4"><?php echo CONFIRMATION_BUYING_TEXT_TITLE; ?></td> </tr> 
                    <tr> 
                      <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                      <td class="main"><?php echo CONFIRMATION_BUYING_TEXT_FEE.$currencies->format($con_show_fee); ?></td> 
                      <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                      <td class="main">&nbsp;</td> 
                    </tr> 
                <?php
                  }
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
          <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
              <tr> 
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                    <tr> 
                      <td class="main"><div class="payment_comment"><?php echo str_replace('<br />', '<br>', nl2br(htmlspecialchars($order->info['comments']))) . tep_draw_hidden_field('comments', $order->info['comments']); ?></div></td> 
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
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="rg_pay_info"> 
              <tr> 
              <td class="main"><b>ご注文内容をご確認の上「注文する」をクリックしてください。</b></td>
                <td align="right" class="main"> <?php


  

  if (is_array($payment_modules->modules)) {
    echo $payment_modules->process_button();
  }

  //character  
  if(isset($_SESSION['character'])){
    foreach($_SESSION['character'] as $ck => $cv){
    echo tep_draw_hidden_field("character[$ck]", $cv);
  }
  }
  
  //echo tep_image_submit('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER) . '</form>' . "\n";
  echo tep_image_submit('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER) . "\n";
?> </td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
        </tr>  
        </table>
        </div>
        <p class="pageBottom"></p>
        </form> 
        </td> 
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
</div> 
<!-- /visites --> 
<object>
<noscript>
<img src="visites.php" alt="Statistics" style="border:0">
</noscript>
</object>
<!-- /visites -->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
