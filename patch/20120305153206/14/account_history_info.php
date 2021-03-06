<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  //forward 404
  if (isset($_GET['order_id']))
{
//ccdd
  $_404_query = tep_db_query("select * from " .TABLE_ORDERS . " where site_id = '".SITE_ID."' and orders_id = '"
      . $_GET['order_id'] . "'");
  $_404 = tep_db_fetch_array($_404_query);

  forward404Unless($_404);
}
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  # For Guest
  if($guestchk == '1') {
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  if (!isset($_GET['order_id'])) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  }
  
//ccdd
  $customer_number_query = tep_db_query("select customers_id from " . TABLE_ORDERS .  " where orders_id = '".  tep_db_input(tep_db_prepare_input($_GET['order_id'])) . "' and site_id = ".SITE_ID);
  $customer_number = tep_db_fetch_array($customer_number_query);
  if ($customer_number['customers_id'] != $customer_id) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT_HISTORY_INFO);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_3, tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $_GET['order_id'], 'SSL'));

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order($_GET['order_id']);
?>
<?php page_head();?>
</head>
<body><div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof --> 
  <!-- body --> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td valign="top" class="left_colum_border"> <!-- left_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof --> </td> 
      <!-- body_text --> 
      <td valign="top" id="contents"> <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1> 
        
        <div class="comment"> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0" class="product_info_box"> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr> 
                    <td class="main" colspan="2"><b><?php echo sprintf(HEADING_ORDER_NUMBER, $_GET['order_id']) . ' <small>(' . $order->info['orders_status'] . ')</small>'; ?></b></td> 
                  </tr> 
                  <tr> 
                    <td class="smallText"><?php echo HEADING_ORDER_DATE . ' ' . tep_date_long($order->info['date_purchased']); ?></td> 
                    <td class="smallText" align="right">
                    <?php 
                    if ($order->info['total'] < 0) {
                      echo HEADING_ORDER_TOTAL . ' ' .'<font color="#ff0000">' .  abs($order->info['total']).'</font>'.MONEY_UNIT_ATEXT; 
                    } else {
                      echo HEADING_ORDER_TOTAL . ' ' .  abs($order->info['total']).MONEY_UNIT_ATEXT; 
                    }
                    ?>
                    </td> 
                  </tr> 
                </table></td> 
            </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2" > 
                  <tr> 
                    <?php
  if ($order->delivery != false) {
?> 
                    <td width="30%" valign="top">
                    <table border="0" width="100%" cellspacing="0" cellpadding="2" > 
                        <tr> 
                          <td class="main"><b><?php echo HEADING_DELIVERY_ADDRESS; ?></b></td> 
                        </tr> 
                        <tr>
                          <td class="main"><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br>'); ?></td> 
                        </tr> 
                        <?php
    if ($order->info['shipping_method']) {
?> 
                        <tr> 
                          <td class="main"><b><?php echo HEADING_SHIPPING_METHOD; ?></b></td> 
                        </tr> 
                        <tr> 
                          <td class="main"><?php echo $order->info['shipping_method']; ?></td> 
                        </tr> 
                        <?php
    }
?> 
                      </table>
                      </td> 
                    <?php
  }
?> 
                    <td width="<?php echo (($order->delivery != false) ? '70%' : '100%'); ?>" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2" > 
                        <tr> 
                          <td>
                            <table class="infoBoxContents"> 
                              <?php
  if (sizeof($order->info['tax_groups']) > 1) {
?> 
                              <tr> 
                                <td class="main" colspan="2"><b><?php echo HEADING_PRODUCTS; ?></b></td> 
                                <td class="smallText" align="right"><b><?php echo HEADING_TAX; ?></b></td> 
                                <td class="smallText" align="right"><b><?php echo HEADING_TOTAL; ?></b></td> 
                              </tr> 
                  <tr>
                  <td colspan="3">
                    <table width="100%"> 
                              <?php
  } else {
?> 
                              <tr> 
                                <td class="main" colspan="3"><b><?php echo HEADING_PRODUCTS; ?></b></td> 
                              </tr>
                  <tr>
                  <td colspan="3">
                    <table width="100%"> 
                              <?php
  }

  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    echo '          <tr>' . "\n" .
         '            <td class="main" align="right" valign="top" width="30">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
         '            <td class="main" valign="top">' . $order->products[$i]['name'];

    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        echo '<br><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small>';
      }
    }

    echo '</td>' . "\n";

    if (sizeof($order->info['tax_groups']) > 1) echo '            <td class="main" valign="top" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";

    echo '            <td class="main" align="right" valign="top">';
    if ($order->products[$i]['final_price'] < 0) {
      echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value'])).'</font>'.JPMONEY_UNIT_TEXT;
    } else {
      echo $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']);
    }
    echo '</td>' . "\n" .
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
              <td class="main"><b><?php echo HEADING_BILLING_INFORMATION; ?></b></td> 
            </tr> 
            <tr> 
              <td><table class="infoBoxContents"> 
                  <tr> 
            <td><table width="100%"><tr><td>
                    <td width="30%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                        <tr> 
                          <td class="main"><b><?php echo HEADING_BILLING_ADDRESS; ?></b></td> 
                        </tr> 
                        <tr> 
                          <td class="main"><?php echo tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br>'); ?></td> 
                        </tr> 
                        <tr> 
                          <td class="main"><b><?php echo HEADING_PAYMENT_METHOD; ?></b></td> 
                        </tr> 
                        <tr> 
                          <td class="main"><?php echo $order->info['payment_method']; ?></td> 
                        </tr> 
                      </table></td> 
                    <td width="70%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                        <?php
  for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
    if ($order->totals[$i]['class'] == 'ot_point') {
      $campaign_info_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$_GET['order_id']."' and site_id = '".SITE_ID."'"); 
      $campaign_info = tep_db_fetch_array($campaign_info_query);
      if ($campaign_info) {
        if ($campaign_info['campaign_fee'] == 0) {
          continue; 
        }
      } else {
        if ($order->totals[$i]['value'] == 0) {
          continue; 
        }
      }
    }
    echo '              <tr>' . "\n" .
         '                <td class="main" align="right" width="100%">' . $order->totals[$i]['title'] . '</td>' . "\n" .
         '                <td class="main" align="right" nowrap>';
         if ($order->totals[$i]['class'] == 'ot_point') {
           $campaign_info_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$_GET['order_id']."' and site_id = '".SITE_ID."'"); 
           $campaign_info = tep_db_fetch_array($campaign_info_query);
           if ($campaign_info) {
             echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format_total(abs($campaign_info['campaign_fee']))).'</font>'.JPMONEY_UNIT_TEXT; 
           } else {
             echo '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format_total($order->totals[$i]['value'])).'</font>'.JPMONEY_UNIT_TEXT; 
           }
         } else {
           echo $currencies->format_total($order->totals[$i]['value']); 
         }
         echo '</td>' . "\n" .
         '              </tr>' . "\n";
    if ($i == 0) {
      echo '              <tr>' . "\n" .
           '                <td class="main" align="right" width="100%">' . TEXT_FEE_HANDLE . '</td>' . "\n" .
           '                <td class="main" align="right" nowrap>' .$currencies->format($order->info['code_fee'])  . '</td>' . "\n" .
           '              </tr>' . "\n";
    
    }
  }
?> 
                      </table></td></tr></table></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <tr> 
              <td class="main"><b><?php echo HEADING_ORDER_HISTORY; ?></b></td> 
            </tr> 
            <tr> 
              <td><table class="infoBoxContents"> 
                  <tr> 
                    <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                        <?php
//ccdd
//todo: need filter
  $statuses_query = tep_db_query("select os.orders_status_name, osh.date_added, osh.comments from " . TABLE_ORDERS_STATUS . " os, " . TABLE_ORDERS_STATUS_HISTORY . " osh where osh.orders_id = '" . $_GET['order_id'] . "' and osh.orders_status_id = os.orders_status_id and os.language_id = '" . $languages_id . "' and osh.customer_notified = '1' order by osh.date_added");
  while ($statuses = tep_db_fetch_array($statuses_query)) {
    echo '              <tr>' . "\n" .
         '                <td class="main" valign="top" width="75">' . tep_date_short($statuses['date_added']) . '</td>' . "\n" .
         '                <td class="main" valign="top" width="70">' . $statuses['orders_status_name'] . '</td>' . "\n" .
         '                <td class="main" valign="top">' .
         (empty($statuses['comments']) ? '&nbsp;' : str_replace('<br />', '<br>', nl2br(htmlspecialchars($statuses['comments'])))) . '</td>' . "\n" .  '              </tr>' . "\n";
  }
?> 
                      </table></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <?php
  if (DOWNLOAD_ENABLED == 'true') include(DIR_WS_MODULES . 'downloads.php');
?> 
            <tr> 
              <td align="right" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, tep_get_all_get_params(array('order_id')), 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
            </tr> 
          </table> 
        </div>
        <p class="pageBottom"></p>
        </td> 
      <!-- body_text_eof --> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof --> </td> 
  </table> 
  <!-- body_eof --> 
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof --> 
</div> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
