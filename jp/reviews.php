<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_REVIEWS);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_REVIEWS));
?>
<?php page_head();?>
</head>
<body>
<div class="body_shadow" align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" id="contents">
        <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>
        <div>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <?php
  $reviews_array = array();  
  $reviews_query_raw = "
  select * 
  from (
    select r.reviews_id, 
           rd.reviews_text, 
           r.reviews_rating, 
           r.date_added, 
           p.products_id, 
           pd.products_name, 
           p.products_image, 
           r.customers_name,
           pd.site_id as psid
    from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
    where pd.site_id        = '" . SITE_ID . "' 
      and p.products_status != '0' 
      and p.products_id     = r.products_id 
      and r.reviews_id      = rd.reviews_id 
      and p.products_id     = pd.products_id 
      and pd.language_id    = '" . $languages_id . "' 
      and rd.languages_id   = '" . $languages_id . "' 
      and r.reviews_status  = '1' 
      and r.site_id         = ".SITE_ID." 
    ORDER by pd.site_id DESC
    ) p
    where psid = '0'
       or psid = '".SITE_ID."'
    group by reviews_id
    order by date_added DESC
  ";
  /*
  $reviews_query_raw = "
    select r.reviews_id, 
           rd.reviews_text, 
           r.reviews_rating, 
           r.date_added, 
           p.products_id, 
           pd.products_name, 
           p.products_image, 
           r.customers_name 
    from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
    where pd.site_id        = '" . SITE_ID . "' 
      and p.products_status != '0' 
      and p.products_id     = r.products_id 
      and r.reviews_id      = rd.reviews_id 
      and p.products_id     = pd.products_id 
      and pd.language_id    = '" . $languages_id . "' 
      and rd.languages_id   = '" . $languages_id . "' 
      and r.reviews_status  = '1' 
      and r.site_id         = ".SITE_ID." 
      and pd.site_id        = '".SITE_ID."' 
    order by r.reviews_id DESC
  ";
  */
  $reviews_split = new splitPageResults($_GET['page'], MAX_DISPLAY_NEW_REVIEWS, $reviews_query_raw, $reviews_numrows);
//ccdd
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
              <td><br>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $reviews_split->display_count($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
                    <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE; ?>
                      <?php echo $reviews_split->display_links($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                  </tr>
                </table>
              </td>
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
              <td><br>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $reviews_split->display_count($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
                    <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE; ?>
                      <?php echo $reviews_split->display_links($reviews_numrows, MAX_DISPLAY_NEW_REVIEWS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                  </tr>
                </table>
              </td>
            </tr>
            <?php
  }
?>
          </table>
        </div>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation //-->
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
