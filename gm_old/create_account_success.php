<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CREATE_ACCOUNT_SUCCESS);

  $breadcrumb->add(NAVBAR_TITLE_1);
  $breadcrumb->add(NAVBAR_TITLE_2);

  if (sizeof($navigation->snapshot) > 0) {
    $origin_href = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(tep_session_name())), $navigation->snapshot['mode']);
    $navigation->clear_snapshot();
  } else {
    if ($cart->count_contents() > 0) {
      $origin_href = tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL'); 
    } else {
      $origin_href = tep_href_link(FILENAME_DEFAULT);
    } 
  }
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
<h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1> 
<div> 
          <table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0"> 
        <tr>
          <td align="right"><?php echo '<a href="' . $origin_href . '">' . tep_image_button('button_continue_02.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
        </tr>
            <tr> 
              <td><table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td><?php echo tep_image(DIR_WS_IMAGES . 'table_background_man_on_board.gif', HEADING_TITLE); ?></td>
              <td valign="top" class="main"><br><?php echo TEXT_ACCOUNT_CREATED; ?></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td align="right"><br><?php echo '<a href="' . $origin_href . '">' . tep_image_button('button_continue_02.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
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
