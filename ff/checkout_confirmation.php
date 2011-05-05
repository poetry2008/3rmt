<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_CONFIRMATION);

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_CHECKOUT_PAYMENT));
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  }

// avoid hack attempts during the checkout procedure by checking the internal cartID
  if (isset($cart->cartID) && tep_session_is_registered('cartID')) {
    if ($cart->cartID != $cartID) {
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
  }
  
  $sendto = false;

// if no shipping method has been selected, redirect the customer to the shipping method selection page
//  if (!tep_session_is_registered('shipping')) {
//    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
//  }

  if (!tep_session_is_registered('payment')) tep_session_register('payment');
  if (isset($_POST['payment'])) $payment = $_POST['payment'];

  if (!tep_session_is_registered('comments')) tep_session_register('comments');
  if ($_POST['comments_added'] != '') {
    $comments = tep_db_prepare_input($_POST['comments']);
  }
  
////
// check if bank info
  if($payment == 'buying') {
  $bank_name = tep_db_prepare_input($_POST['bank_name']);
  $bank_shiten = tep_db_prepare_input($_POST['bank_shiten']);
  $bank_kamoku = tep_db_prepare_input($_POST['bank_kamoku']);
  $bank_kouza_num = tep_db_prepare_input($_POST['bank_kouza_num']);
  $bank_kouza_name = tep_db_prepare_input($_POST['bank_kouza_name']);
  
  tep_session_register('bank_name');
  tep_session_register('bank_shiten');
  tep_session_register('bank_kamoku');
  tep_session_register('bank_kouza_num');
  tep_session_register('bank_kouza_name');
  
  if($bank_name == '') {
    tep_session_unregister('bank_name');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'bank_error=' . urlencode(TEXT_BANK_ERROR_NAME), 'SSL'));
  }
  if($bank_shiten == '') {
    tep_session_unregister('bank_shiten');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'bank_error=' . urlencode(TEXT_BANK_ERROR_SHITEN), 'SSL'));
  }
  if($bank_kouza_num == '') {
    tep_session_unregister('bank_kouza_num');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'bank_error=' . urlencode(TEXT_BANK_ERROR_KOUZA_NUM), 'SSL'));
  }
  if (!preg_match("/^[0-9]+$/", $bank_kouza_num)) {
    tep_session_unregister('bank_kouza_num');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'bank_error=' . urlencode(TEXT_BANK_ERROR_KOUZA_NUM2), 'SSL'));
  } 
  if($bank_kouza_name == '') {
    tep_session_unregister('bank_kouza_name');
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'bank_error=' . urlencode(TEXT_BANK_ERROR_KOUZA_NAME), 'SSL'));
  }
  }  

// load the selected payment module
  require(DIR_WS_CLASSES . 'payment.php');
  $payment_modules = new payment($payment);

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order;

  $payment_modules->update_status();

  if ( ( is_array($payment_modules->modules) && (sizeof($payment_modules->modules) > 1) && !is_object($$payment) ) || (is_object($$payment) && ($$payment->enabled == false)) ) {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
  }

  if (is_array($payment_modules->modules)) {
    $payment_modules->pre_confirmation_check();
  }

// load the selected shipping module
//  require(DIR_WS_CLASSES . 'shipping.php');
//  $shipping_modules = new shipping($shipping);

  require(DIR_WS_CLASSES . 'order_total.php');
  $order_total_modules = new order_total;

// Stock Check
  $any_out_of_stock = false;
  if (STOCK_CHECK == 'true') {
    for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
      if (tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty'])) {
        $any_out_of_stock = true;
      }
    }
    // Out of Stock
    if ( (STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true) ) {
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
    }
  }

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2);
  
  if (isset($$payment->form_action_url)) {
    $form_action_url = $$payment->form_action_url;
  } else {
    $form_action_url = tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
  }
?>
<?php page_head();?>
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
      <td valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
      <?php echo tep_draw_form('checkout_confirmation', $form_action_url, 'post');?>
      <h1 class="pageHeading"><span><?php echo HEADING_TITLE ; ?></span></h1>      
      <div class="comment">
        <table border="0" width="100%" cellspacing="0" cellpadding="0" class="product_info_box"> 
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
        <tr><td>
        <table border="0" width="100%" cellspacing="0" cellpadding="2" class="cg_pay_info"> 
        <tr> 
            <td class="main"><b>ご注文内容をご確認の上「注文する」をクリックしてください。</b></td> 
            <td class="main" align="right"><?php echo tep_image_submit('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER);?></td> 
            </tr></table>
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
    if ($payment == 'buying') {
?>
          <tr> 
            <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
                <tr> 
                  <td>
<table width="100%" border="0" cellspacing="0" cellpadding="2" >
  <tr>
  <td class="main" colspan="3"><b><?php echo TABLE_HEADING_BANK; ?></b><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
  </tr>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main" width="30%"><?php echo TEXT_BANK_NAME; ?></td>
    <td class="main" width="70%"><?php echo $bank_name; ?></td>
  </tr>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_BANK_SHITEN; ?></td>
    <td class="main"><?php echo $bank_shiten; ?></td>
  </tr>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_BANK_KAMOKU; ?></td>
    <td class="main"><?php echo $bank_kamoku; ?></td>
  </tr>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_BANK_KOUZA_NUM; ?></td>
    <td class="main"><?php echo $bank_kouza_num; ?></td>
  </tr>
  <tr>
    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
  <td class="main"><?php echo TEXT_BANK_KOUZA_NAME; ?></td>
    <td class="main"><?php echo $bank_kouza_name; ?></td>
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
  if ($order->info['total'] > 0) {
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
          <td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="cg_pay_info"> 
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
