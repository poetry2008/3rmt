<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_ACTIONS.'product_reviews_write.php');
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
  
      <?php
  if ($valid_product == false) {
?> 
      <p class="main"><b><?php echo ERROR_INVALID_PRODUCT; ?></b></p> 
      <?php
  } else {
    $product_info = tep_db_fetch_array($product_query);
?> 
      <div><?php echo tep_draw_form('product_reviews_write', tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'action=process&products_id=' . $_GET['products_id']), 'post', 'onSubmit="return checkForm();"'); ?> 
        <table class="box_des" width="95%" cellpadding="0" cellspacing="0" border="0"> 
          <tr> 
            <td>
  <?php if ($form_error === true) {?>
  <font color='red' style='font-size:12px'><?php echo str_replace('\n','<br>',$error_message);?></font>
  <?php }?>
  <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td class="main"><b><?php echo SUB_TITLE_PRODUCT; ?></b> <?php echo $product_info['products_name']; ?></td> 
                <td rowspan="2" valign="top" align="right"><br> 
                <?php echo tep_image(DIR_WS_IMAGES . 'products/' . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?></td> 
              </tr> 
              <tr> 
                <td colspan="2" class="main"><b><?php echo SUB_TITLE_FROM; ?></b> <?php echo tep_draw_input_field('reviews_name', ''); ?></td> 
              </tr> 
              <tr> 
                <td colspan="2" class="main"><br> 
                <b><?php echo SUB_TITLE_REVIEW; ?></b></td> 
              </tr> 
              <tr> 
                <td colspan="2"><?php echo tep_draw_textarea_field('review', 'soft', 50, 15, $_POST['review']);?></td> 
              </tr> 
              <tr> 
                <td colspan="2" class="smallText"><?php echo TEXT_NO_HTML; ?></td> 
              </tr> 
            </table></td> 
          </tr> 
          <tr> 
            <td colspan='2' class="main"><br> 
            <b><?php echo SUB_TITLE_RATING; ?></b> <?php echo TEXT_BAD . ' ' . tep_draw_radio_field('rating', '1', $_POST['rating'] == '1') . ' ' . tep_draw_radio_field('rating', '2', $_POST['rating'] == '2') . ' ' . tep_draw_radio_field('rating', '3', $_POST['rating'] == '3') . ' ' . tep_draw_radio_field('rating', '4', $_POST['rating'] == '4') . ' ' . tep_draw_radio_field('rating', '5', $_POST['rating'] == '5') . ' ' . TEXT_GOOD; ?></td> 
          </tr> 
          <tr> 
            <td class="main"><br> 
            <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2"> 
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
?></div>
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
