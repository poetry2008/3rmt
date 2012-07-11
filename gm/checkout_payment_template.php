<body>
<!-- header //--> 
 <?php
require_once DIR_WS_INCLUDES . 'header.php';
//$breadcrumb->add('dsada', tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'));
//echo   tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'); 
?>
 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
   <?php //require_once DIR_WS_INCLUDES . 'column_left.php'; ?>
<!-- body_text //-->
<div id="layout" class="yui3-u">
   <?php
   echo tep_draw_form('checkout_payment', tep_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'), 'post', 'onsubmit="return check_form();"'); 
?>

<div id="current">
   <?php
   echo $breadcrumb->trail(' <img src="images/point.gif"> '); 
?>
</div>
<div id="main-content">
<h2>
   <?php
   echo HEADING_TITLE ; 
?>
</h2>

<table border="0" width="100%" cellspacing="0" cellpadding="0" class="checkout_s_link"> 
           <tr> 
               <td width="20%">
                   <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                         <tr> 
                            <td width="50%" align="right">
                                    <?php
                             echo tep_draw_separator('pixel_silver.gif', '1', '5'); 
                                  ?>  
                              </td> 
                                <td width="50%">
                                 <?php
                                   echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
                                            ?>
                                </td> 
                          </tr> 
                    </table>
                  </td> 
                  <td width="20%">
                   <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                         <tr> 
                                <td width="50%">
                                <?php
                            echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
                                ?>
                                </td> 
                          </tr> 
                    </table>
                  </td> 
                  <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
                 <td width="50%">
                      <?php
                     echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
                      ?>
                  </td> 
                  <td>
                     <?php
                      echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); 
                      ?>
                    </td> 
                  <td width="50%">
                    <?php
                         echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
                      ?>
                  </td> 
            </tr> 
        </table>
    </td> 
    <td width="20%">
           <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
                  <td width="50%">
     <?php
        echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
        ?>
               
                  </td> 
              </tr> 
          </table>

    
      </td> 
    <td width="20%">
        <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
                <td width="50%">
                  <?php
                    echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
                      ?>
                </td> 
                  <td width="50%">
                        <?php
                      echo tep_draw_separator('pixel_silver.gif', '1', '5'); 
                      ?>
                  </td> 
              </tr> 
          </table>
    </td> 
</tr> 
<tr class="box_des"> 
   <td align="center" nowrap="nowrap" width="20%" class="checkoutBarFrom">
   <?php
   echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_OPTION . '</a>'; 
?>
</td>
<td align="center" nowrap="nowrap" width="20%" class="checkoutBarFrom">
   <?php
   echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; 
?>
</td> 
<td align="center" nowrap="nowrap" width="20%" class="checkoutBarCurrent">
   <?php
   echo CHECKOUT_BAR_PAYMENT; 
?>
</td> 
<td align="center" nowrap="nowrap" width="20%" class="checkoutBarTo">
   <?php
   echo CHECKOUT_BAR_CONFIRMATION; 
?>
</td> 
<td align="center" nowrap="nowrap" width="20%" class="checkoutBarTo">
   <?php
   echo CHECKOUT_BAR_FINISHED; 
?>
</td> 
</tr> 
</table>
<div id="hm-checkout-warp">
      <div class="checkout-title"><p><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</p><p>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE;  ?></p></div>
      <div class="checkout-bottom"><?php echo
      tep_image_submit('button_continue_02.gif',
          IMAGE_BUTTON_CONTINUE,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue_02.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_02_hover.gif\'"'); ?></div>
</div>
	  <div class="checkout-conent">
        <h3><b><?php echo TABLE_HEADING_PAYMENT_METHOD;?></b></h3>  
        <div><?php tep_payment_out_selections();?></div>
		</div>
		<div class="checkout-conent">
<h3><b><?php echo  TABLE_HEADING_COMMENTS; ?></b></h3>
  

<?php
//if($cart->show_total() >= 0) { 
if(true) { 
  ?>
    <div>

      <?php
    }else{
     echo "<div>"; 
  ?>

     <?php
    } 
?>

  <?php
  echo tep_draw_textarea_field('comments', 'soft', '50', '5', '','style="width:100%"') . tep_draw_hidden_field('comments_added', 'YES'); 
?>
</div>


<?php

if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && $cart->show_total() > 0) {//point --  
  if($guestchk == '1') {
    echo '<input type="hidden" name="point" value="0">';
  } else {
         
    ?> 
      <h3><b><?php echo TEXT_POINT_OR_CAMPAION; ?></b></h3>
  <div class="payment-content">
      <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
          $default_point_value = $campaign_error?$campaign_error_str:$_POST['point']; 
        } else {
          $default_point_value = (isset($_SESSION['hc_point']))?$_SESSION['hc_point']:((isset($_SESSION['h_point']))?$_SESSION['h_point']:($campaign_error?$campaign_error_str:0)); 
        }
      ?>
      <input type="text" value="<?php echo $default_point_value;?>" name="point" id="input_text_short" style="text-align:right"> 
                                        <div> <?php echo isset($current_point['point'])?$current_point['point']:$point['point']; ?><?php echo TEXT_POINT_READ;?> 
                    <?php
                    if ($campaign_error) {
                      echo '&nbsp;<font color="#ff0000">'.CAMPAIGN_ERROR_TEXT.'</font>'; 
                    }
                    ?>
                      </div>
                                        </div>
      <?php
 
        }
} else if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && $cart->show_total() < 0) { 
  if($guestchk != '1') {
?>
   <h3><b><?php echo TEXT_POINT_OR_CAMPAION; ?></b></h3>
   <div>
     <?php
      if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $default_point_value = $campaign_error?$campaign_error_str:0; 
      } else {
        $default_point_value = (isset($_SESSION['hc_camp_point']))?$_SESSION['hc_camp_point']:($campaign_error?$campaign_error_str:0); 
      }
      ?>
      <input type="text" value="<?php echo $default_point_value;?>" name="camp_point" id="input_text_short" style="text-align:right"> 
                    <?php
                    if ($campaign_error) {
                      echo '<font color="#ff0000">'.CAMPAIGN_ERROR_TEXT.'</font>'; 
                    }
                    ?>  
      </div>           
<?php
  }
}
?>
<?php

//if($cart->show_total() >= 0) {
if(true) {
   echo "</div><div style='clear:both;'>"; 
  ?>

     <?php

    }else{
  echo "<div style='clear:both;'>";
  ?>

     <?php
 
    }

?>
<div id="hm-checkout-warp">
<div class="checkout-title"><p><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</p><p>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE;  ?></p></div>
<div class="checkout-bottom"><?php echo tep_image_submit('button_continue_02.gif',
    IMAGE_BUTTON_CONTINUE,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue_02.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_02_hover.gif\'"'); ?></div>
</div>
</div>
</div>
</form>
</div>
 <?php include('includes/float-box.php');?>
</div>
<?php
require(DIR_WS_INCLUDES . 'footer.php'); 
?>

</body>
</html>
<?php
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>

