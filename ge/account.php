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

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
?>
<?php page_head();?>
</head>
<body>
<!-- header --> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof --> 
  <!-- body --> 
  <div id="main">
  <!-- left_navigation -->
    <div id="l_menu">
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
    </div>
      <!-- left_navigation_eof -->
      <!-- body_text -->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>
      <table class="box_des" width="95%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td><?php
  $is_read_only = true;

 
  $account_query = tep_db_query("
      SELECT c.customers_gender, c.customers_firstname, c.customers_lastname, c.customers_firstname_f, c.customers_lastname_f, c.customers_dob, c.customers_email_address, a.entry_company, a.entry_street_address, a.entry_suburb, a.entry_postcode, a.entry_city, a.entry_zone_id, a.entry_state, a.entry_country_id, c.customers_telephone, c.customers_fax, c.customers_newsletter
      FROM " . TABLE_CUSTOMERS . " c, " .  TABLE_ADDRESS_BOOK . " a 
      WHERE c.customers_id = '" . $customer_id . "' AND a.customers_id = c.customers_id AND a.address_book_id = '" .  $customer_default_address_id . "' AND  c.site_id = ".SITE_ID);
  $account = tep_db_fetch_array($account_query);

  require(DIR_WS_MODULES . 'account_details.php');
?>
          </td>
        </tr>
        <?php 
    //Point
    if (MODULE_ORDER_TOTAL_POINT_STATUS == 'true') { 
    $point_query = tep_db_query("
        select point 
        from " . TABLE_CUSTOMERS . " 
        where customers_id = '" . $customer_id . "'
    ");
    $point = tep_db_fetch_array($point_query);
    ?>
        
       <tr>
          <td>
		  <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
              <td class="formAreaTitle">
              <div class="box_link_clear"><?php echo TEXT_POINT_NOW; ?></div></td>
            </tr>
			<tr>
           <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="0" class="formArea">
                <tr>
                  <td class="main">
				  <table border="0" cellspacing="0" cellpadding="2">
                    <tr>
                      <td class="main" width="120" >&nbsp;<?php echo TEXT_POINT_ADD; ?></td>
                      <td class="main">&nbsp;<?php echo $point['point'] . " P"; ?></td>
                    </tr>
                  </table></td>
                </tr>
              </table></td>
			</tr>
          </table></td>
        </tr>
        <?php } ?>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
<!--
              <td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . tep_image_button('button_address_book.gif', IMAGE_BUTTON_ADDRESS_BOOK) . '</a>'; ?></td>
-->       
              <td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . tep_image_button('button_history.gif', IMAGE_BUTTON_HISTORY) . '</a>'; ?></td>
              <td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EXIT, 'check_from='.time(), 'SSL') . '">' . tep_image_button('button_out.gif', IMAGE_BUTTON_CUSTOMERS_EXIT) . '</a>'; ?></td>
              <td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL') . '">' . tep_image_button('button_notifications.gif', IMAGE_BUTTON_NOTIFICATIONS) . '</a>'; ?></td>
              <td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . tep_image_button('button_edit_account.gif', IMAGE_BUTTON_EDIT_ACCOUNT) . '</a>'; ?></td>
            </tr>
          </table></td>
        </tr>
      </table></div>
      <!-- body_text_eof --> 
<!-- right_navigation --> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<!-- right_navigation_eof -->
  <!-- body_eof -->
  <!-- footer -->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof -->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
