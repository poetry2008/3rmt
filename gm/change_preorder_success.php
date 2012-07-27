<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/change_preorder_success.php');
   
  $breadcrumb->add(CPREORDER_SUCCESS_NAVBAR_TITLE_FETCH, '');
  $breadcrumb->add(CPREORDER_SUCCESS_NAVBAR_TITLE_CONFIRM, '');
  $breadcrumb->add(CPREORDER_SUCCESS_NAVBAR_TITLE, '');
?>
<?php page_head();?>
</head>
<body>
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <div id="main"> 
        <?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
      <!-- body_text //--> 
      <div id="layout" class="yui3-u"> 
        <div id="current"><?php echo $breadcrumb->trail(' <img  src="images/point.gif"> '); ?></div>
        
        <div id="main-content">
        <h2><?php echo CPREORDER_SUCCESS_HEADING_TITLE ; ?></h2> 
          <table border="0" cellspacing="0" cellpadding="0" border="0" width="100%" align="center" class="preorder_title">
            <tr>
              <td width="33%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5');?></td> 
                    <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?></td> 
                  </tr>
                </table> 
              </td>
              <td width="33%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="50%">
              <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?>
                    </td>
                  </tr>
                </table>  
 
              
              </td>
              <td width="33%">
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr>
                    <td width="50%">
                    <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1');?>
                    </td>
                    <td width="50%">
                    <?php echo tep_image(DIR_WS_IMAGES.'checkout_bullet.gif');?>
                    </td>
                  </tr>
                </table>  
              </td>
            </tr>
            <tr>
              <td align="center" width="33%" class="preorderBarFrom"><?php echo PREORDER_TRADER_LINE_TITLE;?></td> 
              <td align="center" width="33%" class="preorderBarFrom"><?php echo PREORDER_CONFIRM_LINE_TITLE;?></td> 
              <td align="center" width="33%" class="preorderBarCurrent"><?php echo PREORDER_FINISH_LINE_TITLE;?></td> 
            </tr>
          </table>
          <div id="hm-checkout-warp" align="right">
            <?php echo '<a href="' .tep_href_link(FILENAME_DEFAULT). '">' .  tep_image_button('button_continue_02.gif', IMAGE_BUTTON_CONTINUE, 'onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_02_hover.gif\'" onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue_02.gif\'"') . '</a>'; ?>
          </div>
          <div class="checkout-conent">
          <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:14px;">
          <tr>
            <td>
            <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10');?> 
            </td>
          </tr>
         
          <tr>
            <td>
            <?php echo CPREORDER_SUCCESS_TEXT;?> 
            </td>
          </tr>
         
        </table>
        </div>
        <div id="hm-checkout-warp" align="right">
            <?php echo '<a href="' .tep_href_link(FILENAME_DEFAULT). '">' .  tep_image_button('button_continue_02.gif', IMAGE_BUTTON_CONTINUE, 'onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_02_hover.gif\'" onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue_02.gif\'"') . '</a>'; ?>
        </div>
        </div>
       <p class="pageBottom"></p>
      </div> 
      <!-- body_text_eof //--> 
        <?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
  <!-- body_eof //--> 
  <!-- footer //--> 
<?php include("includes/float-box.php");?>
  <!-- footer_eof //--> 
</div>
</div>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
