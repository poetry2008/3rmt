<body><div class="body_shadow" align="center"> 
   <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
   <!-- header_eof //--> 
   <!-- body //--> 
   <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
   <tr> 
   <!--<td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">--><!-- left_navigation //--> 
   <?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
   <!-- left_navigation_eof //--><!--</td>--> 
   <!-- body_text //--> 
   <td valign="top" id="contents"><?php echo tep_draw_form('checkout_payment', tep_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'), 'post', 'onsubmit="return check_form();"'); ?>
<h1 class="pageHeading">
   <span class="game_t">
   <?php echo HEADING_TITLE ; ?>
   </span>
   </h1> 
   <div class="comment"> 
   <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
   <tr>
   <td>
   <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
   <tr> 
   <td><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
   <tr> 
   <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
   <tr> 
   <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
   <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
   </tr> 
   </table></td> 
   <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
   <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
   <tr> 
   <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
   <td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
   <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
   </tr> 
   </table></td> 
   <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
   <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
   <tr> 
   <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
   <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
   </tr> 
   </table></td> 
   </tr> 
   <tr> 
   <td align="center" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PRODUCTS, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_PRODUCTS . '</a>'; ?></td> 
   <td align="center" width="20%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; ?></td> 
   <td align="center" width="20%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_PAYMENT; ?></td> 
   <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td> 
   <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td> 
   </tr> 
   </table>
              
   </td>
   </tr>
   <tr> 
   <td>
   <table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
   <tr> 
   <td class="main"><b><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td> 
   <td class="main" align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
   </tr> 
   </table>
   </td>
   </tr>
 <?php
   if (isset($_GET['payment_error']) && is_object(${$_GET['payment_error']}) && ($error = ${$_GET['payment_error']}->get_error())) {
     ?> 
     <tr> 
       <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
       <tr> 
       <td class="main"><b><?php echo htmlspecialchars($error['title']); ?></b></td> 
                                                                               </tr> 
                                                                               </table></td> 
                                                                               </tr> 
  
                                                                               <tr> 
                                                                               <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxNotice"> 
                                                                               <tr class="infoBoxNoticeContents"> 
                                                                               <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                                                                               <tr> 
                                                                               <td class="main" width="100%" valign="top"><?php echo htmlspecialchars($error['error']); ?></td> 
                                                                                                                                                                              </tr> 
                                                                                                                                                                              </table></td> 
                                                                                                                                                                              </tr> 
                                                                                                                                                                              </table></td> 
                                                                                                                                                                              </tr> 
                                                                                                                                                                              <?php
                                                                                                                                                                              }
?>
<tr> 
<td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
   <tr> 
   <td class="main"><b><?php echo TABLE_HEADING_PAYMENT_METHOD; ?></b></td> 
   </tr> 
   </table></td> 
   </tr> 
   <tr> 
   <td>
 <?php tep_payment_out_selection();?>
   </td> 
        </tr> 

</table>                      
</td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
  <tr> 
  <td class="main"><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td> 
  </tr> 
  </table></td> 
  </tr> 
  <tr> 
  <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
  <tr class="infoBoxContents"> 
  <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
  <tr> 
  <td><?php echo tep_draw_textarea_field('comments', 'soft', '60', '5') . tep_draw_hidden_field('comments_added', 'YES'); ?></td> 
  </tr> 
  </table></td> 
  </tr> 
  </table></td> 
  </tr> 
<?php
  if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && $cart->show_total() > 0) {//point --  
    if($guestchk == '1') {
      echo '<input type="hidden" name="point" value="0">';
    } else if(tep_only_sell_product()){
      ?> 
      <tr> 
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
        <tr> 
        <td class="main"><b><?php echo TEXT_POINT; ?></b></td> 
                                                         </tr> 
                                                         </table></td> 
                                                         </tr> 
                                                         <tr> 
                                                         <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
                                                         <tr class="infoBoxContents"> 
                                                         <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                                                         <tr> 
                                                         <td class="main"><input type="text" value="0" name="point" class="input_text_short"> 
                                                         /<?php echo $point['point'] ?></td> 
                                                         </tr> 
                                                         </table></td> 
                                                         </tr> 
                                                         </table></td> 
                                                         </tr> 
                                                         <?php
                                                         }else{
      echo '<input type="hidden" name="point" value="0">';
    }
  }//point eof// 
?> 
<tr> 
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
  <tr> 
  <td class="main"><b><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td> 
  <td class="main" align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
  </tr> 
  </table>
  </td> 
  </tr> 
  </table>
  </td>
  </tr>
  </table> 
  </div>
  </form> 
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
  <?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>