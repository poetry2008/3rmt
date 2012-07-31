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
  </td> 
  </tr> 
  <?php
  //点数处理
  if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && $cart->show_total() > 0) {//point --  
    if($guestchk == '1') {
      echo '<input type="hidden" name="point" value="0">';
    } else {
      ?> 
      <tr> 
      <td>
	  <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
      <tr> 
        <td class="main">
        <b><?php echo TEXT_POINT_OR_CAMPAION; ?></b>
        &nbsp;&nbsp;
        <?php
        if ($campaign_error) {
          echo '<font color="#ff0000">'.CAMPAIGN_ERROR_TEXT.'</font>'; 
        }
        ?>
        </td> 
      </tr> 
      </table></td> 
      </tr> 
      <tr> 
      <td>
	  <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
            <tr class="infoBoxContents"> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr> 
                    <td class="main">
                    <?php
                      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $default_point_value = $campaign_error?$campaign_error_str:$_POST['point']; 
                      } else {
                        $default_point_value = (isset($_SESSION['hc_point']))?$_SESSION['hc_point']:((isset($_SESSION['h_point']))?$_SESSION['h_point']:($campaign_error?$campaign_error_str:0)); 
                      }
                    ?>
                    <input type="text" value="<?php echo $default_point_value;?>" name="point" size="24" style="text-align:right"> 
                    </td> 
                    <td class="main" align="right">
                    <?php echo isset($current_point['point'])?$current_point['point']:$point['point']; ?><?php echo TEXT_POINT_READ;?>
                    </td> 
                    </tr> 
                  </table></td> 
              </tr> 
            </table></td> 
        </tr> 
      <tr> 
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
      </tr> 
      <?php 
    }
  } else if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && $cart->show_total() < 0) { 
    if($guestchk != '1') {
  ?>
  <tr> 
      <td>
	  <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
      <tr> 
        <td class="main">
        <b><?php echo TEXT_POINT_OR_CAMPAION; ?></b>
        &nbsp;&nbsp;
        <?php
        if ($campaign_error) {
          echo '<font color="#ff0000">'.CAMPAIGN_ERROR_TEXT.'</font>'; 
        }
        ?>
        </td> 
      </tr> 
      </table></td> 
      </tr> 
      <tr> 
      <td>
	  <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
            <tr class="infoBoxContents"> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                <tr> 
                <td class="main">
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                  $default_point_value = $campaign_error?$campaign_error_str:0; 
                } else {
                  $default_point_value = (isset($_SESSION['hc_camp_point']))?$_SESSION['hc_camp_point']:($campaign_error?$campaign_error_str:0); 
                }
                ?>
                <input type="text" value="<?php echo $default_point_value;?>" name="camp_point" size="24" style="text-align:right"> 
                </td> 
                </tr> 
        </table></td> 
      </tr> 
      </table>
      </td> 
      </tr> 
      <tr> 
      <td>
	  <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
      </td> 
      </tr>
  <?php
    }
  }
?> 
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
