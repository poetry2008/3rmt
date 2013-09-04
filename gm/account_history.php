<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  # For Guest
  if($guestchk == '1') {
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT_HISTORY);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
?>
<?php page_head();?>
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<!-- body_text //-->
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<?php include('includes/search_include.php');?>
<div id="main-content">
<h2><?php echo HEADING_TITLE ; ?></h2> 
         <table  border="0" width="100%" cellspacing="0" cellpadding="0" id="hm-account-history"> 
            <tr> 
              <td><?php
  $customer_info_raw = tep_db_query("select * from ".TABLE_CUSTOMERS_INFO." where customers_info_id = '".$customer_id."'"); 
  $customer_info = tep_db_fetch_array($customer_info_raw); 
  $history_query_raw = "
        select o.orders_id, 
                o.date_purchased, 
                o.delivery_name, 
                ot.text as order_total, 
                ot.value as order_total_value, 
                o.orders_status_name 
        from " . TABLE_ORDERS . " o 
          left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id) 
        where o.customers_id = '" . $customer_id . "' 
          and ot.class = 'ot_total' 
          and o.site_id = ".SITE_ID." and o.date_purchased >= '".$customer_info['customers_info_date_account_created']."' and is_gray != '1' and is_guest = '0' order by orders_id DESC
  ";
  $history_count_query_raw = "
        select count(o.orders_id) as count
        from " . TABLE_ORDERS . " o 
        where o.customers_id = '" . $customer_id . "' 
          and o.site_id = ".SITE_ID." and o.date_purchased >= '".$customer_info['customers_info_date_account_created']."' and is_gray != '1' and is_guest = '0' ";
  $history_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ORDER_HISTORY, $history_query_raw, $history_numrows, $history_count_query_raw);
  $history_query = tep_db_query($history_query_raw);

  $info_box_contents = array();

  if (tep_db_num_rows($history_query)) {
    while ($history = tep_db_fetch_array($history_query)) {
      $products_query = tep_db_query
("select count(*) as count from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $history['orders_id'] . "'");
      $products = tep_db_fetch_array($products_query);

      $order_heading = '<table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n" .
                       '  <tr>' . "\n" .
                       '    <td><b>' . TEXT_ORDER_NUMBER . '</b> ' . $history['orders_id'] . '</td>' . "\n" .
                       '    <td align="right" style="padding-right:15px;">' .
                       '<table width="150"><tr><td width="50%"><b>'.
                       TEXT_ORDER_STATUS.'</b></td><td align="left" width="50%"
                       > ' .  $history['orders_status_name']
. ' </td></tr></table>'.'</td>'."\n".
                       '  </tr>' . "\n" .
                       '</table>';

      $order = '<table  border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n" .
               '  <tr>' . "\n" .
               '    <td width="50%" valign="top"><b>' . TEXT_ORDER_DATE . '</b> ' .
               tep_date_long($history['date_purchased']) . '<br><b>' .
               TEXT_ORDER_SHIPPED_TO . '</b> ' .
               tep_get_orders_address($history['orders_id']) . '</td>' . "\n" .
               '    <td width="30%" valign="top"><b>' .
               TEXT_ORDER_PRODUCTS . '</b> ' . $products['count'] . '<br><b>' .
               TEXT_ORDER_COST . '</b> ' .  $currencies->format_total($history['order_total_value']) . '</td>' . "\n" .
               '    <div style="margin:3px 0 3px 0;"><a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'page=' . $_GET['page'] . '&order_id=' . $history['orders_id'], 'SSL') . '">' . TEXT_VIEW_ORDER . '</a></div>' . "\n" .
               '  </tr>' . "\n" .
               '</table>';

      new tableBox(array(array('text' => $order_heading)), true);
      new infoBox(array(array('text' => $order)));

      echo '<br>';
    }
  } else {
  new infoBox(array(array('text' => TEXT_NO_PURCHASES)));
  }
?> </td> 
            </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <?php
  if (tep_db_num_rows($history_query)) {
?> 
                  <tr> 
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">  
                      <tr> 
                        <td><?php echo $history_split->display_count($history_numrows, MAX_DISPLAY_ORDER_HISTORY, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td> 
                      </tr> 
                    </table> 
                    <table border="0" width="100%" cellspacing="0" cellpadding="2">  
                      <tr> 
                        <td><?php echo $history_split->display_links($history_numrows, MAX_DISPLAY_ORDER_HISTORY, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td> 
                      </tr> 
                    </table> 
                  </tr> 
                  <?php
  }
?> 
                  <tr> 
                    <td style="padding-top:40px;"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '',
                    'SSL') . '">' . tep_image_button('button_back.gif',
                    IMAGE_BUTTON_BACK,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_back.gif\'" onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_back_hover.gif\'"') . '</a>'; ?></td> 
                  </tr> 
                </table></td> 
            </tr> 
          </table> 
        </div>
        </div>
<?php include('includes/float-box.php');?>

          </div>
 
 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
