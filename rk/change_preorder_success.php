<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/change_preorder_success.php');
   
  $breadcrumb->add(CPREORDER_SUCCESS_NAVBAR_TITLE_FETCH, '');
  $breadcrumb->add(CPREORDER_SUCCESS_NAVBAR_TITLE_CONFIRM, '');
  $breadcrumb->add(CPREORDER_SUCCESS_NAVBAR_TITLE, '');
?>
<?php page_head();?>
</head>
<body><div align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
        <h1 class="pageHeading"><?php echo CPREORDER_SUCCESS_HEADING_TITLE ; ?></h1> 
        <div class="comment">
          <table border="0" cellspacing="0" cellpadding="0" border="0" width="90%" align="center">
            <tr>
              <td width="20%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="30%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5');?></td> 
                    <td width="70%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?></td> 
                  </tr>
                </table> 
              </td>
              <td width="60%">
              <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?>
              </td>
              <td width="20%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="70%">
                    <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?>
                    </td>
                    <td width="30%">
                    <?php echo tep_image(DIR_WS_IMAGES.'checkout_bullet.gif');?>
                    </td>
                  </tr>
                </table>  
              </td>
            </tr>
            <tr>
              <td align="left" width="20%" class="preorderBarFrom"><?php echo PREORDER_TRADER_LINE_TITLE;?></td> 
              <td align="center" width="60%" class="preorderBarFrom"><?php echo PREORDER_CONFIRM_LINE_TITLE;?></td> 
              <td align="right" width="20%" class="preorderBarCurrent"><?php echo PREORDER_FINISH_LINE_TITLE;?></td> 
            </tr>
          </table>
          <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;">
          <tr>
            <td>
            <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10');?> 
            </td>
          </tr>
          <tr>
            <td align="right">
            <?php echo '<a href="' .tep_href_link(FILENAME_DEFAULT). '">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?>
            </td>
          </tr>
          <tr>
            <td>
            <?php echo CPREORDER_SUCCESS_TEXT;?> 
            </td>
          </tr>
          <tr>
            <td>
                  <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                    <tr> 
                      <td class="main" align="right"><?php echo '<a href="' .tep_href_link(FILENAME_DEFAULT). '">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td> 
                      <td align="right" class="main">
                      </td> 
                    </tr> 
                  </table></td> 
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
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
