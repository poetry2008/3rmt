<?php
/*
  $Id$
  订制订单完成页
*/

 require('includes/application_top.php');


// 以下是页面
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PREORDER_UNSUCCESS);

  $breadcrumb->add(NAVBAR_TITLE_1);
//ccdd
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
        <h1 class="pageHeading"><?php echo TEXT_UNSUCCESS; ?></h1> 
        <div class="comment"> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr> 
              <td>
              <font size="2"><?php echo TEXT_PAY_UNSUCCESS;?></font></td> 
            </tr> 
            <tr> 
              <td align="right" class="main">
              <a href="<?php echo tep_href_link(FILENAME_DEFAULT);?>"><?php echo tep_image_button('button_continue.gif', '');?></a> 
              </td> 
            </tr> 
          </table> 
          </div>
          <p class="pageBottom"></p>
        </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
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
