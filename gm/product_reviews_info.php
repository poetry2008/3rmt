<?php
/*
  $Id$
*/

  require('includes/application_top.php');

// lets retrieve all $_GET keys and values..
  $get_params = tep_get_all_get_params(array('reviews_id'));
  $get_params = substr($get_params, 0, -1); //remove trailing &

  // ccdd
  $reviews_query = tep_db_query("
    select *
    from (
      SELECT rd.reviews_text, 
             r.reviews_rating, 
             r.reviews_id, 
             r.products_id, 
             r.customers_name, 
             r.date_added, 
             r.last_modified, 
             r.reviews_read, 
             pd.products_name, 
             p.products_image,
             r.site_id as rsid,
             pd.site_id as psid
      FROM (( " .  TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd ) 
              LEFT JOIN " .  TABLE_PRODUCTS . " p 
              ON (r.products_id = p.products_id) )
        LEFT JOIN " .  TABLE_PRODUCTS_DESCRIPTION . " pd 
        ON (p.products_id = pd.products_id AND pd.language_id = '". $languages_id . "') 
      WHERE r.reviews_id = '" .  (int)$_GET['reviews_id'] . "' 
        AND r.reviews_id = rd.reviews_id 
        AND p.products_status != '0' 
        AND r.reviews_status = '1' 
        AND r.site_id  = ".SITE_ID." 
      order by pd.site_id DESC
    ) p
    where psid = '0'
       or psid = '".SITE_ID."'
    group by reviews_id
   ");
  if (!tep_db_num_rows($reviews_query)) tep_redirect(tep_href_link(FILENAME_REVIEWS));
  $reviews = tep_db_fetch_array($reviews_query);

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_REVIEWS_INFO);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_PRODUCT_REVIEWS, $get_params));
?>
<?php page_head();?>
<script type="text/javascript" src="js/prototype.js"></script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="js/lightbox.js"></script>
<link rel="stylesheet" href="css/lightbox.css" type="text/css" media="screen">
<script type="text/javascript"><!--
function popupImageWindow(url) {
  window.open(url,'popupImageWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
function showimage($1) {
  document.images.lrgproduct.src = $1;
}
//--></script>
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
<div id="content"><?php
      // ccdd
  tep_db_query("
      UPDATE " . TABLE_REVIEWS . " 
      SET reviews_read = reviews_read+1 
      WHERE reviews_id = '" . $reviews['reviews_id'] . "'
  ");
  $reviews_text = tep_break_string(tep_output_string_protected($reviews['reviews_text']), 60, '-<br>');
?> 
        <div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
    <h1 class="pageHeading"><?php echo sprintf(HEADING_TITLE, $reviews['products_name']); ?></h1> 
        
        <div> 
          <table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td><table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr> 
                    <td class="main"><b><?php echo SUB_TITLE_PRODUCT; ?></b> <?php echo $reviews['products_name']; ?></td> 
                    <td class="smallText" rowspan="3" align="center">
          <a href="<?php echo DIR_WS_IMAGES . $reviews['products_image']; ?>" rel="lightbox[products]"><?php echo tep_image(DIR_WS_IMAGES . 'products/' . $reviews['products_image'], $reviews['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'align="center" hspace="5" vspace="5"'); ?><br> </a></td> 
                  </tr> 
                  <tr> 
                    <td class="main"><b><?php echo SUB_TITLE_FROM; ?></b> <?php echo tep_output_string_protected($reviews['customers_name']); ?></td> 
                  </tr> 
                  <tr> 
                    <td class="main"><b><?php echo SUB_TITLE_DATE; ?></b> <?php echo tep_date_long($reviews['date_added']); ?></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <tr> 
              <td class="main"><b><?php echo SUB_TITLE_REVIEW; ?></b></td> 
            </tr> 
            <tr> 
              <td class="main"><br> 
                <?php echo str_replace('<br />', '<br>', nl2br($reviews_text)); ?></td>
            </tr> 
            <tr> 
              <td class="main"><br> 
                <b><?php echo SUB_TITLE_RATING; ?></b> <?php echo tep_image(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])); ?> <small>[<?php echo sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating']); ?>]</small></td> 
            </tr> 
            <tr> 
              <td><br> 
                <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr> 
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS, $get_params) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
                    <td align="right" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, 'action=buy_now&products_id=' . $reviews['products_id']) . '">' . tep_image_button('button_in_cart.jpg', IMAGE_BUTTON_IN_CART); ?></a></td> 
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
