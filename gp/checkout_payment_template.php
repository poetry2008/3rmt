<body><div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"><?php echo tep_draw_form('checkout_payment', tep_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'), 'post', 'onsubmit="return check_form();"'); ?><div class="pageHeading"><h1><?php echo HEADING_TITLE ; ?></h1></div> 
        
        <div class="comment"> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0" class="product_info_box"> 
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
                </table></td> 
            </tr>
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="rg_pay_info"> 
                  <tr> 
                  	<td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td> 
                    <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                  </tr> 
                </table></td> 
            </tr> 
<?php
  //販売開始
  //if($cart->show_total() >= 0) {
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
                          <td></td> 
                          <td class="main" width="100%" valign="top"><?php echo htmlspecialchars($error['error']); ?></td> 
                          <td></td> 
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
?>
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr> 
                    <td class="main"><b><?php echo TABLE_HEADING_PAYMENT_METHOD; ?></b></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <!--new-->
            <tr>
                <td>
                <?php tep_payment_out_selection(); 
                ?>
                </td>
            </tr>
            <!--new_eof-->
            <tr> 
              <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
                  <tr class="infoBoxContents"> 
                    <td>
                    <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                        <?php
  $selection = $payment_modules->selection();

  if (sizeof($selection) > 1) {
?> 
                        <tr> 
                          <td></td> 
                          <td class="main" width="50%" valign="top"><?php echo TEXT_SELECT_PAYMENT_METHOD; ?></td> 
                          <td class="main" width="50%" valign="top" align="right"><b><?php echo TITLE_PLEASE_SELECT; ?></b><br> 
                            <?php echo tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif'); ?></td> 
                          <td></td> 
                        </tr> 
                        <?php
  } else {
?> 
                        <tr> 
                          <td></td> 
                          <td class="main" width="100%" colspan="2"><?php echo TEXT_ENTER_PAYMENT_INFORMATION; ?></td> 
                          <td></td> 
                        </tr> 
                        <?php
  }

  $radio_buttons = 0;
  for ($i=0, $n=sizeof($selection); $i<$n; $i++) {

    if(!tep_whether_show_payment(constant("MODULE_PAYMENT_".strtoupper($selection[$i]['id'])."_LIMIT_SHOW"), $_SESSION['guestchk'])) {

      continue;
    }
    if (check_money_limit(constant("MODULE_PAYMENT_".strtoupper($selection[$i]['id'])."_MONEY_LIMIT"), $order->info['total'])) {
      //      echo constant("MODULE_PAYMENT_".strtoupper($selection[$i]['id'])."_MONEY_LIMIT");
      continue; 
    }

?> 
                        <tr> 
                          <td></td> 
                          <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                              <?php
    if ( ($selection[$i]['id'] == $payment) || ($n == 1) ) {
      echo '                  <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
    } else {
      echo '                  <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
    }
?> 
                              <td width="10"></td> 
                                <td class="main" colspan="3"><b><?php echo $selection[$i]['module']; ?></b></td> 
                                <td class="main" align="right"><?php
    if (sizeof($selection) > 1) {
      echo tep_draw_radio_field('payment', $selection[$i]['id']);
    } else {
      echo tep_draw_hidden_field('payment', $selection[$i]['id']);
    }
?> </td> 
                                <td width="10"></td> 
                              </tr> <?php
    if (isset($selection[$i]['error'])) {
?> 
                              <tr> 
                                <td width="10"></td> 
                                <td class="main" colspan="4"><?php echo $selection[$i]['error']; ?></td> 
                                <td width="10"></td> 
                              </tr> 
                              <?php
    } elseif (isset($selection[$i]['fields']) && is_array($selection[$i]['fields'])) {
?> 
                              <tr> 
                                <td width="10"></td> 
                                <td colspan="4"><table border="0" cellspacing="0" cellpadding="2"> 
                                    <?php
      for ($j=0, $n2=sizeof($selection[$i]['fields']); $j<$n2; $j++) {
?> 
                                    <tr> 
                                      <td width="10"></td> 
                                      <td class="main"><?php echo $selection[$i]['fields'][$j]['title']; ?></td> 
                                      <td></td> 
                                      <td class="main"><?php echo $selection[$i]['fields'][$j]['field']; ?></td> 
                                      <td width="10"></td> 
                                    </tr> 
                                    <?php
      }
?> 
                                  </table></td> 
                                <td width="10"></td> 
                              </tr> 
                              <?php
    }
?> 
                            </table></td> 
                          <td></td> 
                        </tr> 
                        <?php
    $radio_buttons++;
  //}//buying not view
  }
?> 
                      </table></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <tr> 
              <td><?php //echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
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
                          <td>
                          <textarea name="comments"  cols="60" rows="5"></textarea> 
						  <?php echo /*tep_draw_textarea_field('comments', 'soft', '60', '5') . */tep_draw_hidden_field('comments_added', 'YES'); ?></td> 
                        </tr> 
                      </table></td> 
                  </tr> 
                </table></td> 
            </tr> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <?php
      if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true'  && $cart->show_total() > 0) {//point --  
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
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <?php
        }else{
          echo '<input type="hidden" name="point" value="0">';
        }
      }//point eof// 
      ?> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="rg_pay_info">
                        <tr> 
                          <td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td> 
                          <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                        </tr> 
                      </table></td> 
            </tr> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr>  
          </table> 
        </div>
        </form> 
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