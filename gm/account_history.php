<?php
/*
  $Id: account_history.php,v 1.2 2004/05/26 05:07:55 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
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
<!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof //-->
<!-- body_text //-->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1> 
        
        <div> 
          <table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td><?php
  $history_query_raw = "select o.orders_id, o.date_purchased, o.delivery_name, ot.text as order_total, s.orders_status_name from " . TABLE_ORDERS . " o left join " . TABLE_ORDERS_TOTAL . " ot on (o.orders_id = ot.orders_id) left join " .  TABLE_ORDERS_STATUS . " s on (o.orders_status = s.orders_status_id and s.language_id = '" . $languages_id . "') where o.customers_id = '" . $customer_id . "' and ot.class = 'ot_total' and o.site_id = '".SITE_ID."' order by orders_id DESC";
  $history_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_ORDER_HISTORY, $history_query_raw, $history_numrows);
  $history_query = tep_db_query($history_query_raw);

  $info_box_contents = array();

  if (tep_db_num_rows($history_query)) {
    while ($history = tep_db_fetch_array($history_query)) {
      $products_query = tep_db_query("select count(*) as count from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . $history['orders_id'] . "'");
      $products = tep_db_fetch_array($products_query);

      $order_heading = '<table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n" .
                       '  <tr>' . "\n" .
                       '    <td class="main"><b>' . TEXT_ORDER_NUMBER . '</b> ' . $history['orders_id'] . '</td>' . "\n" .
                       '    <td class="main" align="right" style="padding-right:15px;"><b>' . TEXT_ORDER_STATUS . '</b> ' . $history['orders_status_name'] . '</td>' . "\n" .
                       '  </tr>' . "\n" .
                       '</table>';

      $order = '<table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n" .
               '  <tr>' . "\n" .
               '    <td class="main" width="50%" valign="top"><b>' . TEXT_ORDER_DATE . '</b> ' . tep_date_long($history['date_purchased']) . '<br><b>' . TEXT_ORDER_SHIPPED_TO . '</b> ' . tep_output_string_protected($history['delivery_name']) . '</td>' . "\n" .
               '    <td class="main" width="30%" valign="top"><b>' . TEXT_ORDER_PRODUCTS . '</b> ' . $products['count'] . '<br><b>' . TEXT_ORDER_COST . '</b> ' . strip_tags($history['order_total']) . '</td>' . "\n" .
               '    <td class="main" width="20%"><a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'page=' . $HTTP_GET_VARS['page'] . '&order_id=' . $history['orders_id'], 'SSL') . '">' . TEXT_VIEW_ORDER . '</a></td>' . "\n" .
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
              <td><table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <?php
  if (tep_db_num_rows($history_query)) {
?> 
                  <tr> 
                    <td class="smallText" valign="top"><?php echo $history_split->display_count($history_numrows, MAX_DISPLAY_ORDER_HISTORY, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td> 
                    <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE; echo $history_split->display_links($history_numrows, MAX_DISPLAY_ORDER_HISTORY, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td> 
                  </tr> 
                  <?php
  }
?> 
                  <tr> 
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                  </tr> 
                  <tr> 
                    <td class="smallText"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
                  </tr> 
                </table></td> 
            </tr> 
          </table> 
        </div></div>
      <!-- body_text_eof //--> 
<!-- right_navigation //--> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<!-- right_navigation_eof //--> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
