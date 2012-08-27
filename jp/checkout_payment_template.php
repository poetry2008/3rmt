<body>
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
  <tr> 
  <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
  <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
  <!-- left_navigation_eof //--> </td> 
  <!-- body_text //--> 
  <td valign="top" id="contents"><?php echo tep_draw_form('checkout_payment', tep_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'), 'post', 'onsubmit="return check_form();"'); ?><h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1> 
    <div> 
      <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
        <tr> 
          <td>
            <?php require(DIR_WS_INCLUDES . 'payment_nav_box.php'); ?> 
          </td> 
        </tr> 
        <tr>
          <td>
            <table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
              <tr> 
                <td class="main"><b><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b> <br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td> 
                <td class="main" align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
              </tr> 
           </table>
         </td> 
       </tr> 
       <tr> 
         <td>
           <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
             <tr> 
               <td class="main"><b><?php echo TABLE_HEADING_PAYMENT_METHOD; ?></b></td> 
             </tr> 
           </table>
         </td> 
      </tr>
      <tr> 
       <td>
         <?php tep_payment_out_selection(); ?>
       </td> 
      </tr> 
      <tr>
  	<td>
    	  <table>
        	<tr><td><img height="10" width="100%" alt="" src="images/pixel_trans.gif"></td></tr>
        	<tr>
                <td class="main">
                <b><?php echo TABLE_HEADING_COMMENTS;?></b> 
                </td> 
                </tr>
        	<tr><td><img height="10" width="100%" alt="" src="images/pixel_trans.gif"></td></tr>
          </table>
      </td>
    </tr> 
    <tr> 
      <td>
          <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
              <tr class="infoBoxContents"> 
                  <td>
                      <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                          <tr> 
                              <td>
                              <?php echo tep_draw_textarea_field('comments', 'soft', '60', '5') . tep_draw_hidden_field('comments_added', 'YES'); ?>
                              </td> 
                          </tr> 
                      </table>
                  </td> 
              </tr> 
          </table>
      </td> 
</tr> 
<tr> 
<td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
    <tr> 
      <td class="main"><b><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b> <br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td> 
      <td class="main" align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
    </tr> 
  </table>
</td> 
</tr> 
</table> 
</form> 
</div>
</td> 
<!-- body_text_eof //--> 
<td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
<!-- right_navigation_eof //--> 
</td> 
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
