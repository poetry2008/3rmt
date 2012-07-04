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
<!-- body_text //-->
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<div id="main-content"> 
<h2><?php echo HEADING_TITLE ; ?></h2> 
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"> 
        <tr>
          <td align="right"><?php echo '<a href="' . $origin_href . '">' .
          tep_image_button('button_continue_02.gif',
              IMAGE_BUTTON_CONTINUE,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue_02.gif\'" onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_02_hover.gif\'"') . '</a>'; ?></td>
        </tr>
            <tr> 
              <td><table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td><?php echo tep_image(DIR_WS_IMAGES . 'table_background_man_on_board.gif', HEADING_TITLE); ?></td>
              <td valign="top"><br><?php echo TEXT_ACCOUNT_CREATED; ?></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td align="right"><br><?php echo '<a href="' . $origin_href . '">' .
          tep_image_button('button_continue_02.gif', IMAGE_BUTTON_CONTINUE,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue_02.gif\'" onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_02_hover.gif\'"') . '</a>'; ?></td>
        </tr>
      </table></div></div>
      <?php  include('includes/float-box.php');?>
      </div>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
