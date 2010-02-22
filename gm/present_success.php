<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRESENT_SUCCESS);

  $breadcrumb->add(NAVBAR_TITLE_1);
  $breadcrumb->add(NAVBAR_TITLE_2);

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
<h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1>
 
        
        <table border="0" width="95%" cellspacing="0" cellpadding="0"> 
          <tr> 
            <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td width="33%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                        </tr>
                      </table></td>
                    <td width="33%" align="center"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                          <td width="1"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
                          <td><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                        </tr>
                      </table></td>
                    <td width="33%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                          <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                          <td width="50%"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
                        </tr>
                      </table></td>
                  </tr>
                  <tr class="box_des">
                    <td align="center" width="33%" class="checkoutBarFrom">応募者情報</td>
                    <td align="center" width="33%" class="checkoutBarFrom">確認画面</td>
                    <td align="center" width="33%" class="checkoutBarCurrent">応募完了</td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td class="main"><table class="box_des" width="95%" border="0" align="center" cellpadding="2" cellspacing="4">
                  <tr>
                    <td valign="top"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_man_on_board.gif', HEADING_TITLE); ?></td>
                    <td valign="top" class="main"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?>
                      
                      <br>
                      <?php echo TEXT_SUCCESS; ?><br>
                      <br>
                      <?php
    echo '<br><br>' . TEXT_CONTACT_STORE_OWNER;
?>
                      <h3><?php echo TEXT_THANKS_FOR_SHOPPING; ?></h3></td>
                  </tr>
                </table></td>
            </tr>
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
            </tr>
            <tr>
              <td align="right" class="main"><br>
                <?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
            </tr>
          </table>
        </div>
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
<?php 
//セッション開放
tep_session_unregister('pc_id');
tep_session_unregister('firstname');
tep_session_unregister('lastname');
tep_session_unregister('email_address');
tep_session_unregister('telephone');
tep_session_unregister('street_address');
tep_session_unregister('suburb');
tep_session_unregister('postcode');
tep_session_unregister('city');
tep_session_unregister('zone_id');


require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
