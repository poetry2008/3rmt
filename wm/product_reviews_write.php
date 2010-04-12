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
if (isset($_GET['products_id'])) {
  $_404_query = tep_db_query("select * from " . TABLE_PRODUCTS . " where products_id
      = '" . intval($_GET['products_id']) . "'");
  $_404 = tep_db_fetch_array($_404_query);

  forward404Unless($_404);
}

/*
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }
*/
  $product_query = tep_db_query("select pd.products_name, p.products_image from " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . $languages_id . "' and pd.site_id = '".SITE_ID."'");
  $valid_product = (tep_db_num_rows($product_query) > 0);

  if (isset($_GET['action']) && $_GET['action'] == 'process') {
    if ($valid_product == true) { // We got to the process but it is an illegal product, don't write
      $customer = tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "' and site_id = '".SITE_ID."'");
      $customer_values = tep_db_fetch_array($customer);
      $date_now = date('Ymd');
	  if($_POST['reviews_name'] && tep_not_null($_POST['reviews_name'])) {
	    $reviews_name = $_POST['reviews_name'];
	  } else {
  		require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_REVIEWS_WRITE);
		$reviews_name = REVIEWS_NO_NAMES;
	  }
      tep_db_query("insert into " . TABLE_REVIEWS . " (products_id, customers_id, customers_name, reviews_rating, date_added, reviews_status, site_id) values ('" . $_GET['products_id'] . "', '" . $customer_id . "', '" .  addslashes($reviews_name) . "', '" . $_POST['rating'] . "', now(), '0', '".SITE_ID."')");
      $insert_id = tep_db_insert_id();
      tep_db_query("insert into " . TABLE_REVIEWS_DESCRIPTION . " (reviews_id, languages_id, reviews_text) values ('" . $insert_id . "', '" . $languages_id . "', '" . $_POST['review'] . "')");
    }

    tep_redirect(tep_href_link(FILENAME_PRODUCT_INFO, $_POST['get_params']));
  }

// lets retrieve all $_GET keys and values..
  $get_params = tep_get_all_get_params();
  $get_params_back = tep_get_all_get_params(array('reviews_id')); // for back button
  $get_params = substr($get_params, 0, -1); //remove trailing &
  if (tep_not_null($get_params_back)) {
    $get_params_back = substr($get_params_back, 0, -1); //remove trailing &
  } else {
    $get_params_back = $get_params;
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_REVIEWS_WRITE);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_PRODUCT_REVIEWS, $get_params));

  $customer_info_query = tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" .  $customer_id . "' and site_id = '".SITE_ID."'");
  $customer_info = tep_db_fetch_array($customer_info_query);
?>
<?php page_head();?>
<script type="text/javascript"><!--
function checkForm() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  var review = document.product_reviews_write.review.value;

  if (review.length < <?php echo REVIEW_TEXT_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_REVIEW_TEXT; ?>";
    error = 1;
  }

  if ((document.product_reviews_write.rating[0].checked) || (document.product_reviews_write.rating[1].checked) || (document.product_reviews_write.rating[2].checked) || (document.product_reviews_write.rating[3].checked) || (document.product_reviews_write.rating[4].checked)) {
  } else {
    error_message = error_message + "<?php echo JS_REVIEW_RATING; ?>";
    error = 1;
  }

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}
//--></script>
</head>
<body><div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"><h1 class="pageHeading"><?php echo HEADING_TITLE ; ?> </h1>  
      <?php
  if ($valid_product == false) {
?> 
      <p class="main comment"><b><?php echo ERROR_INVALID_PRODUCT; ?></b></p> 
      <?php
  } else {
    $product_info = tep_db_fetch_array($product_query);
?> 
      <div class="comment"><?php echo tep_draw_form('product_reviews_write', tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'action=process&products_id=' . $_GET['products_id']), 'post', 'onSubmit="return checkForm();"'); ?> 
        <table width="100%" cellpadding="0" cellspacing="0" border="0"> 
          <tr> 
            <td><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td class="main"><b><?php echo SUB_TITLE_PRODUCT; ?></b> <?php echo $product_info['products_name']; ?></td> 
                <td rowspan="4" valign="top" align="right"><br> 
                <?php echo tep_image(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?></td> 
              </tr> 
              <tr> 
                <td class="main"><b><?php echo SUB_TITLE_FROM; ?></b> <?php echo tep_draw_input_field('reviews_name', '',"class='input_text'"); ?></td> 
              </tr> 
              <tr> 
                <td class="main"><br> 
                <b><?php echo SUB_TITLE_REVIEW; ?></b></td> 
              </tr> 
              <tr> 
                <td><?php echo tep_draw_textarea_field('review', 'soft', 60, 15);?></td> 
              </tr> 
              <tr> 
                <td class="smallText"><?php echo TEXT_NO_HTML; ?></td> 
              </tr> 
            </table></td> 
          </tr> 
          <tr> 
            <td class="main"><br> 
            <b><?php echo SUB_TITLE_RATING; ?></b> <?php echo TEXT_BAD . ' ' . tep_draw_radio_field('rating', '1') . ' ' . tep_draw_radio_field('rating', '2') . ' ' . tep_draw_radio_field('rating', '3') . ' ' . tep_draw_radio_field('rating', '4') . ' ' . tep_draw_radio_field('rating', '5') . ' ' . TEXT_GOOD; ?></td> 
          </tr> 
          <tr> 
            <td class="main"><br> 
            <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
              <tr> 
                <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS, $get_params_back) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
                <td align="right" class="main"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
              </tr> 
            </table> 
            <?php echo tep_draw_hidden_field('get_params', $get_params); ?> </td> 
          </tr> 
        </table> 
        </form> 
      </div> 
      <?php
		}
?>
		<p class="pageBottom"></p> 
		</td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--> </td> 
    </tr> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
