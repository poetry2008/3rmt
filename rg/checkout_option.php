<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_ACTIONS.'checkout_option.php'); 
    
?>
<?php page_head();?>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/option.js"></script>
</head>
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
      <td valign="top" id="contents"> <div class="pageHeading"><img align="top" src="images/menu_ico.gif" alt=""><h1><?php echo HEADING_TITLE ; ?></h1></div>
        <div class="comment"> 
        <form action="<?php echo tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL'); ?>" method="post" >
          <input type="hidden" name="dummy" value="あいうえお眉幅">
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
          <tr> 
            <td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                <tr> 
                  <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td width="50%" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                      </tr> 
                    </table></td> 
                  <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                  <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                  <td width="20%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                  <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
                      </tr> 
                    </table></td> 
                </tr> 
                <tr> 
                  <td align="center" width="20%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_OPTION; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_DELIVERY; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_PAYMENT; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td> 
                  <td align="center" width="20%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td> 
                </tr> 
  </table>
            </td> 
          </tr>
          <tr> 
            <td><img width="100%" height="10" alt="" src="images/pixel_trans.gif"></td> 
          </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="rg_pay_info"> 
                <tr> 
                  <td class="main"><?php echo CHECKOUT_OPTION_BUTTON_TEXT;?></td> 
                  <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                </tr> 
              </table></td> 
            </tr> 
        <tr>
        <td>
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td>
          <?php
          $list_products = $cart->get_products();
          for ($j=0, $k=sizeof($list_products); $j<$k; $j++) {
            $belong_option_raw = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".(int)$list_products[$j]['id']."'");  
            $belong_option = tep_db_fetch_array($belong_option_raw);
            
            echo '<div class="option_product_title"><a href="'.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.(int)$list_products[$j]['id']).'">'.$list_products[$j]['name'].'</a></div>';
            
            if ($belong_option) {
              $is_checkout_item_raw = tep_db_query("select id from ".TABLE_OPTION_ITEM." where place_type = '1' and group_id = '".$belong_option['belong_to_option']."' and status = '1'"); 
              if (tep_db_num_rows($is_checkout_item_raw)) {
                $p_cflag = tep_get_cflag_by_product_id((int)$list_products[$j]['id']); 
                echo '<div class="option_render">'; 
                $hm_option->render($belong_option['belong_to_option'], false, 1, $list_products[$j]['id'].'_', $cart, (int)$p_cflag); 
                echo '</div>'; 
              }
            }
          }
          ?>
          </td>
        </tr>
        </table>
        <table border="0" width="100%" cellspacing="1" cellpadding="2"> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="rg_pay_info"> 
                <tr> 
                  <td class="main"><?php echo CHECKOUT_OPTION_BUTTON_TEXT;?></td> 
                  <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
                </tr> 
              </table></td> 
            </tr> 
          </table>
        </td>
      </tr>
      </table>
    <input type="hidden" name="action" value="process"> 
    </form>
    </div>
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

