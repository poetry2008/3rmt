<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/non-member_auth.php');
  
  
  $breadcrumb->add(NAVBAR_TITLE);

?>
<?php page_head();?>
</head>
<body><div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
        <h1 class="pageHeading">
        <span class="game_t">
          <?php echo HEADING_TITLE_FINISH; ?>
        </span>
        </h1> 
        <div class="comment"> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;"> 
            <tr>
              <td>
              <?php echo CHECK_FINISH_TEXT;?> 
              </td>
            </tr>
            <tr> 
              <td><br> 
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr> 
                    <td class="main"><?php //echo '<a href="' .  tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
                    <td align="right" class="main"><?php echo '<a href="' .  tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
                    </td> 
                  </tr> 
                </table></td> 
            </tr> 
          </table> 
        </div>
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
