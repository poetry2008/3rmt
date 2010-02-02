<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_REVIEWS);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_REVIEWS));
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
<h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
 
        
        <div> 
          <table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0"> 

<?php
  $reviews_array = array(); 
  $reviews_query_raw = "select r.reviews_id, rd.reviews_text, r.reviews_rating, r.date_added, p.products_id, pd.products_name, p.products_image, r.customers_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = r.products_id and r.reviews_id = rd.reviews_id and p.products_id = pd.products_id and pd.language_id = '" .  $languages_id . "' and rd.languages_id = '" . $languages_id . "' and r.reviews_status = '1' and r.site_id = '".SITE_ID."' and pd.site_id = '".SITE_ID."' and p.products_id not in".tep_not_in_disabled_products()." order by r.reviews_id DESC";
  $reviews_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_NEW_REVIEWS, $reviews_query_raw, $reviews_numrows);
  $reviews_query = tep_db_query($reviews_query_raw);
  while ($reviews = tep_db_fetch_array($reviews_query)) {
    $reviews_array[] = array('id' => $reviews['reviews_id'],
                             'products_id' => $reviews['products_id'],
                             'reviews_id' => $reviews['reviews_id'],
                             'products_name' => $reviews['products_name'],
                             'products_image' => $reviews['products_image'],
                             'authors_name' => tep_output_string_protected($reviews['customers_name']),
                             'review' => tep_output_string_protected(mb_substr($reviews['reviews_text'], 0, 250)) . '..',
                             'rating' => $reviews['reviews_rating'],
                             'word_count' => tep_word_count($reviews['reviews_text'], ' '),
                             'date_added' => tep_date_long($reviews['date_added']));
  }

  if (($reviews_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
      <tr>
        <td><br><table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText"><?php echo $reviews_split->display_count($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
            <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $reviews_split->display_links($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
          </tr>
        </table></td>
      </tr>

<?php
  }
?>
      <tr>
        <td>
<?php
  require(DIR_WS_MODULES  . 'reviews.php');
?>
        </td>
      </tr>
<?php
  if (($reviews_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
      <tr>
        <td><br><table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText"><?php echo $reviews_split->display_count($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
            <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $reviews_split->display_links($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
          </tr>
        </table></td>
      </tr>
<?php
  }
?>
    </table></div></div>
      <!-- body_text_eof //--> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
