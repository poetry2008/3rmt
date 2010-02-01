<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  //forward 404
if (isset($HTTP_GET_VARS['products_id'])) {
  $_404_query = tep_db_query("select * from " . TABLE_PRODUCTS . " where products_id
      = '" . intval($HTTP_GET_VARS['products_id']) . "'");
  $_404 = tep_db_fetch_array($_404_query);

  forward404Unless($_404);
}
	
// lets retrieve all $HTTP_GET_VARS keys and values..
  $get_params = tep_get_all_get_params();
  $get_params_back = tep_get_all_get_params(array('reviews_id')); // for back button
  $get_params = substr($get_params, 0, -1); //remove trailing &
  if (tep_not_null($get_params_back)) {
    $get_params_back = substr($get_params_back, 0, -1); //remove trailing &
  } else {
    $get_params_back = $get_params;
  }

  $product_info_query = tep_db_query("select pd.products_name from " . TABLE_PRODUCTS_DESCRIPTION . " pd left join " . TABLE_PRODUCTS . " p on pd.products_id = p.products_id where pd.language_id = '" . $languages_id . "' and p.products_status = '1' and pd.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "'");
  if (!tep_db_num_rows($product_info_query)) tep_redirect(tep_href_link(FILENAME_REVIEWS));
  $product_info = tep_db_fetch_array($product_info_query);

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_REVIEWS);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_PRODUCT_REVIEWS, $get_params));
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
<h1 class="pageHeading"><?php echo sprintf(HEADING_TITLE, $product_info['products_name']); ?></h1> 
        
        <div> 
          <table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td>

<table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="tableHeading"><?php echo TABLE_HEADING_NUMBER; ?></td>
            <td class="tableHeading"><?php echo TABLE_HEADING_AUTHOR; ?></td>
            <td align="center" class="tableHeading"><?php echo TABLE_HEADING_RATING; ?></td>
            <td align="center" class="tableHeading"><?php echo TABLE_HEADING_READ; ?></td>
            <td align="right" class="tableHeading"><?php echo TABLE_HEADING_DATE_ADDED; ?></td>
          </tr>
          <tr>
            <td colspan="5"><?php echo tep_draw_separator(); ?></td>
          </tr>
<?php
  $reviews_query = tep_db_query("select reviews_rating, reviews_id, customers_name, date_added, last_modified, reviews_read from " . TABLE_REVIEWS . " where products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and reviews_status = '1' order by reviews_id DESC");
  if (tep_db_num_rows($reviews_query)) {
    $row = 0;
    while ($reviews = tep_db_fetch_array($reviews_query)) {
      $row++;

      if (($row / 2) == floor($row / 2)) {
        echo '          <tr class="productReviews-even">' . "\n";
      } else {
        echo '          <tr class="productReviews-odd">' . "\n";
      }

      echo '            <td class="smallText">' . tep_row_number_format($row) . '.</td>' . "\n" .
           '            <td class="smallText"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, $get_params . '&reviews_id=' . $reviews['reviews_id']) . '">' . tep_output_string_protected($reviews['customers_name']) . '</a></td>' . "\n" .
           '            <td align="center" class="smallText">' . tep_image(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])) . '</td>' . "\n" .
           '            <td align="center" class="smallText">' . $reviews['reviews_read'] . '</td>' . "\n" .
           '            <td align="right" class="smallText">' . tep_date_short($reviews['date_added']) . '</td>' . "\n" .
           '          </tr>' . "\n";
    }
  } else {
?>
          <tr class="productReviews-odd">
            <td colspan="5" class="smallText"><?php echo TEXT_NO_REVIEWS; ?></td>
          </tr>
<?php
  }
?>
          <tr>
            <td colspan="5"><?php echo tep_draw_separator(); ?></td>
          </tr>
          <tr>
            <td class="main" colspan="5"><br><table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, $get_params_back) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
                <td align="right" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, $get_params) . '">' . tep_image_button('button_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a>'; ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></div></div>
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
