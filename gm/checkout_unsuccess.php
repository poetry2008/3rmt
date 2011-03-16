<?php
/*
  $Id$
  订制订单完成页
*/

 require('includes/application_top.php');

// 以下是动作
// if the customer is not logged on, redirect them to the shopping cart page
  if (!tep_session_is_registered('customer_id')) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  }

  if (isset($_GET['action']) && ($_GET['action'] == 'checkout_payment')) {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, ''));
  }
  if (!isset($_GET['msg'])||$_GET['msg']!='paypal_error'){
    //forward404();
  }



// 以下是页面
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_UNSUCCESS);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2);
//ccdd
?>
<?php page_head();?>
</head>
<body> 
<div class="body_shadow"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <div id="main">
  <!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<div id="content">
<?php echo tep_draw_form('order', tep_href_link(FILENAME_CHECKOUT_UNSUCCESS, 'action=checkout_payment', 'SSL')); ?> 
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php echo TEXT_UNSUCCESS; ?></h1>
 <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr> 
              <td><p>
              <?php if(isset($_GET['msg'])&&$_GET['msg']=='paypal_error'){ ?>
              <font color='red'>
              <?php echo TEXT_PAYPAL_ERROR;?>
              </font>
              <?php } ?>
              <br />
              <?php echo TEXT_PAY_UNSUCCESS;?></p>
              <p><?php echo tep_image_submit('button_back_payment.jpg', IMAGE_BUTTON_CONTINUE); ?></p>
              </td> 
            </tr> 
          </table> 
       </form> 
  </div>
  <div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
  </div>
</div> 
</body>
</html>
<?php 
# For Guest - LogOff
if($guestchk == '1') {
  tep_session_unregister('customer_id');
  tep_session_unregister('customer_default_address_id');
  tep_session_unregister('customer_first_name');
  tep_session_unregister('customer_last_name'); //Add Japanese osCommerce
  tep_session_unregister('customer_country_id');
  tep_session_unregister('customer_zone_id');
  tep_session_unregister('comments');
  tep_session_unregister('guestchk');

  $cart->reset();  
}

require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>
