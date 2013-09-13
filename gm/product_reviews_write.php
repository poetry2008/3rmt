<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_ACTIONS.'product_reviews_write.php');
  $review_query = tep_db_query("select * from `configuration` where configuration_key ='REVIEWS_BAN_CHARACTER' and site_id='0'");
  $review_rows = tep_db_fetch_array($review_query);
?>
<?php page_head();?>
<script type="text/javascript"><!--
function checkForm() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";
  var review = document.product_reviews_write.review.value;
  var reviews_name = document.product_reviews_write.reviews_name.value;
  str="<?php echo $review_rows['configuration_value']; ?>"; // this is string
  var strs= new Array(); //define array
  strs=str.split(","); //split string    
  if(str != ''){
  for(var i=0;i<strs.length;i++){
  var patt = new RegExp(strs[i]);
  if(patt.test(review) == true || patt.test(reviews_name) == true){
     result = true;
     break;
    }else{
     result = false;
    }
  }
  if(result == true){
     error_message = error_message + "<?php echo JS_REVIEW_BAN_CHARACTER;?>";
     error = 1;
  }else{
     error_message = error_message;
  }
  }
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
<!-- body_text //-->
<div class="yui3-u" id="layout">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<div id="main-content">
<h2><?php echo HEADING_TITLE; ?></h2>
  
      <?php
  if ($valid_product == false) {
?> 
      <p><b><?php echo ERROR_INVALID_PRODUCT; ?></b></p> 
      <?php
  } else {
    $product_info = tep_db_fetch_array($product_query);
?> 
      <div id="detail-div"><?php echo tep_draw_form('product_reviews_write', tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'action=process&products_id=' . $_GET['products_id']), 'post', 'onSubmit="return checkForm();"'); ?> 
        <table class="box_des" width="100%" cellpadding="0" cellspacing="0" border="0"> 
          <tr> 
            <td colspan="2">
  <?php if ($form_error === true) {?>
  <font color='red' style='font-size:14px'><?php echo str_replace('\n','<br>',$error_message);?></font>
  <?php }?>
  <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr>
              <td colspan="2" valign="top" align="right"> 
                <?php echo tep_image3(DIR_WS_IMAGES . 'products/' . $product_info['products_image'], $product_info['products_name'], PRODUCT_INFO_IMAGE_WIDTH, PRODUCT_INFO_IMAGE_HEIGHT, 'hspace="5" vspace="5"'); ?></td>
              </tr>
              <tr> 
                <td width="20%"><b><?php echo SUB_TITLE_PRODUCT; ?></b></td>
                <td><?php echo $product_info['products_name']; ?></td>  
              </tr> 
              <tr> 
              	<td><b><?php echo SUB_TITLE_FROM; ?></b></td>
                <td colspan="3"> <?php echo tep_draw_input_field('reviews_name',
                    '','id="input_text"'); ?></td> 
              </tr> 
              <tr> 
                <td valign="top"><b><?php echo SUB_TITLE_REVIEW; ?></b></td> 
                <td colspan="3"><?php echo tep_draw_textarea_field('review', 'soft', 50, 15, $_POST['review'], 'style="width:80%;"');?></td>
              </tr> 
              <tr>
              	<td></td> 
                <td colspan="3" class="smallText"><?php echo TEXT_NO_HTML; ?></td> 
              </tr> 
            </table></td> 
          </tr> 
          <tr> 
            <td width="20%"> <b><?php echo SUB_TITLE_RATING; ?></b> </td>
            <td><?php echo TEXT_BAD . ' ' . tep_draw_radio_field('rating', '1', $_POST['rating'] == '1') . ' ' . tep_draw_radio_field('rating', '2', $_POST['rating'] == '2') . ' ' . tep_draw_radio_field('rating', '3', $_POST['rating'] == '3') . ' ' . tep_draw_radio_field('rating', '4', $_POST['rating'] == '4') . ' ' . tep_draw_radio_field('rating', '5', $_POST['rating'] == '5') . ' ' . TEXT_GOOD; ?></td> 
          </tr> 
          
        </table> 
        <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr>
                <td><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS,
  $get_params_back) . '">' . tep_image_button('button_back.gif',
  IMAGE_BUTTON_BACK,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_back.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_back_hover.gif\'"') . '</a>'; ?></td> 
                <td align="right"><?php echo tep_image_submit('button_continue.gif',
                    IMAGE_BUTTON_CONTINUE,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_hover.gif\'"'); ?>
                
                 <?php echo tep_draw_hidden_field('get_params', $get_params); ?> </td> 

                </td> 
              </tr> 
            </table> 
        </form> 
      </div> 
      <?php
    }
?></div>
      <!-- body_text_eof //--> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <!-- footer_eof //--> 
</div>
<?php include('includes/float-box.php');?>
</div>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
