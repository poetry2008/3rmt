<?php
/*
  $Id$

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

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
?>
<?php page_head();?>
</head>
<body>
<div class="body_shadow" align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" summary="box">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"><!-- left_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" id="contents">
      <h1 class="pageHeading"><img align="top" alt="" src="images/menu_ico.gif">&nbsp<?php echo HEADING_TITLE ; ?></h1>
      <div class="comment">
      <table width="100%" border="0" cellpadding="0" cellspacing="0" summary="table">
        <tr>
          <td><?php
  $is_read_only = true;

//ccdd
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
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
        </tr>
        <tr>
          <td><table border="0" cellpadding="2" cellspacing="0" width="100%" summary="table">
            <tr>
              <td class="formAreaTitle"><br>
              <?php echo TEXT_POINT_NOW; ?></td>
            </tr>
            <tr>
              <td class="main"><table border="0" width="100%" cellspacing="0" cellpadding="2" class="formArea" summary="table">
                <tr>
                  <td class="main"><table border="0" cellspacing="0" cellpadding="2" summary="table">
                    <tr>
                      <td class="main">&nbsp;<?php echo TEXT_POINT_ADD; ?></td>
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
          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
        </tr>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="2" summary="table">
            <tr>
<!--
              <td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . tep_image_button('button_address_book.gif', IMAGE_BUTTON_ADDRESS_BOOK) . '</a>'; ?></td>
-->       
              <td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . tep_image_button('button_history.gif', IMAGE_BUTTON_HISTORY) . '</a>'; ?></td>
              <td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL') . '">' . tep_image_button('button_notifications.gif', IMAGE_BUTTON_NOTIFICATIONS) . '</a>'; ?></td>
              <td align="center" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . tep_image_button('button_edit_account.gif', IMAGE_BUTTON_EDIT_ACCOUNT) . '</a>'; ?></td>
            </tr>
          </table></td>
        </tr>
      </table>
      </div>
      <p class="pageBottom"></p>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"><!-- right_navigation //-->
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
      <!-- right_navigation_eof //-->
      </td>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
