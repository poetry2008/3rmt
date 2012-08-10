<?php
/*
  $Id$
*/

 require('includes/application_top.php');

// if the customer is not logged on, redirect them to the shopping cart page
  if (!tep_session_is_registered('customer_id')) {
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/checkout_losing_information.php');

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
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<div id="main-content">
<h2><?php echo TEXT_UNSUCCESS; ?></h2>
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
              <div class="botton-continue">
              <a href="<?php echo tep_href_link(FILENAME_DEFAULT);?>"><?php echo tep_image_button('button_continue.gif', '');?></a> 
              </div>
              </td> 
            </tr> 
          </table>
          </div>
       </form> 
  </div>
  <?php   include("includes/float-box.php");?>
  <!-- body_eof //--> 
  <!-- footer //--> 
    <!-- footer_eof //--> 
  </div>
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php 
# For Guest - LogOff
if($guestchk == '1') {
  tep_session_unregister('customer_id');
  tep_session_unregister('customer_default_address_id');
  tep_session_unregister('customer_first_name');
  tep_session_unregister('customer_last_name');
  tep_session_unregister('customer_country_id');
  tep_session_unregister('customer_zone_id');
  tep_session_unregister('comments');
  tep_session_unregister('guestchk');

  $cart->reset();  
}

require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>
