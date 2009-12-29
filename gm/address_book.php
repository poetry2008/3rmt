<?php
/*
  $Id: address_book.php,v 1.3 2004/05/26 05:07:55 ptosh Exp $

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

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ADDRESS_BOOK);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL'));
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
          <table border="0" width="95%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr> 
                    <td class="tableHeading" align="center"><?php echo TABLE_HEADING_NUMBER; ?></td> 
                    <td class="tableHeading"><?php echo TABLE_HEADING_NAME; ?></td> 
                    <td class="tableHeading" align="right"><?php echo TABLE_HEADING_LOCATION; ?></td> 
                  </tr> 
                  <tr> 
                    <td colspan="3"><?php echo tep_draw_separator(); ?></td> 
                  </tr> 
                  <?php
  $address_book_query = tep_db_query("select address_book_id, entry_firstname, entry_lastname from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $customer_id . "' and address_book_id > 1 order by address_book_id");
  if (!tep_db_num_rows($address_book_query)) {
?> 
                  <tr class="addressBook-odd"> 
                    <td colspan="3" class="smallText"><?php echo TEXT_NO_ENTRIES_IN_ADDRESS_BOOK; ?></td> 
                  </tr> 
                  <?php
  } else {
    $row = 0;
    while ($address_book = tep_db_fetch_array($address_book_query)) {
      $row++;
       if (($row / 2) == floor($row / 2)) {
        echo '          <tr class="addressBook-even">' . "\n";
      } else {
        echo '          <tr class="addressBook-odd">' . "\n";
      }
      echo '            <td class="smallText" align="center">' . tep_row_number_format($row) . '.</td>' . "\n" .
           '            <td class="smallText"><a href="' . tep_href_link(FILENAME_ADDRESS_BOOK_PROCESS, 'action=modify&entry_id=' . $address_book['address_book_id'], 'SSL') . '">' . tep_output_string_protected(tep_get_fullname($address_book['entry_firstname'],$address_book['entry_lastname'])) . '</a></td>' . "\n" .
           '            <td class="smallText" align="right">' . tep_address_summary($customer_id, $address_book['address_book_id']) . '</td>' . "\n" .
           '          </tr>' . "\n";
    }
  }
?> 
                  <tr> 
                    <td colspan="3"><?php echo tep_draw_separator(); ?></td> 
                  </tr> 
                  <tr> 
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
                  </tr> 
                  <?php
  if ($row < MAX_ADDRESS_BOOK_ENTRIES) {
?> 
                  <tr> 
                    <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                        <tr> 
                          <td class="smallText" valign="top"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a><br><br>' . sprintf(TEXT_MAXIMUM_ENTRIES, MAX_ADDRESS_BOOK_ENTRIES); ?></td> 
                          <td class="smallText" align="right" valign="top"><?php echo '<a href="' . tep_href_link(FILENAME_ADDRESS_BOOK_PROCESS,  'entry_id=' . ($row + 2), 'SSL') . '">' . tep_image_button('button_add_address.gif', IMAGE_BUTTON_ADD_ADDRESS) . '</a>'; ?></td> 
                        </tr> 
                      </table></td> 
                  </tr> 
                  <?php
  } else {
?> 
                  <tr> 
                    <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                        <tr> 
                          <td class="smallText"><?php echo sprintf(TEXT_MAXIMUM_ENTRIES_REACHED, MAX_ADDRESS_BOOK_ENTRIES); ?></td> 
                          <td class="smallText" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
                        </tr> 
                      </table></td> 
                  </tr> 
                  <?php
  }
?> 
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
