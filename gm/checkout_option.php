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
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
<div id="main">
  <div class="yui3-u" id="layout">
  <div id="current" ><?php echo $breadcrumb->trail(' <img  src="images/point.gif"> '); ?></div>
 	<div id="main-content"> 
 		<h2><?php echo HEADING_TITLE ; ?></h2>
        <div> 
        <form name="option_form" action="<?php echo tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL'); ?>" method="post" >
          <input type="hidden" name="dummy" value="<?php echo TEXT_DUMMY;?>">
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
          <tr> 
            <td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0" class="checkout_s_link"> 
                <tr> 
                  <td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                      <tr> 
                        <td width="50%" align="right"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
                        <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
                      </tr> 
                    </table></td> 
                  <td width="20%">
                  	<table border="0" width="100%" cellspacing="0" cellpadding="0">
				  		<tr><td><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td></tr>
                    </table>                    		
                     </td> 
                  <td width="20%">
				  <table border="0" width="100%" cellspacing="0" cellpadding="0">
				  		<tr><td><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td></tr>
                    </table> 
                  </td> 
                  <td width="20%">
                  <table border="0" width="100%" cellspacing="0" cellpadding="0">
				  		<tr><td><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td></tr>
                    </table> 
                  </td> 
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
              <td>
              <div id="hm-checkout-warp"> 
                  <div class="checkout-title"><?php echo CHECKOUT_OPTION_BUTTON_TEXT;?></div> 
                  <div class="checkout-bottom" align="right">
                  <a href="javascript:void(0);" onclick="document.option_form.submit();">
                  <?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE, 'onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_02_hover.gif\'" onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue_02.gif\'"'); ?>
                  </a> 
                  </div> 
              </div>
              </td> 
            </tr> 
        <tr>
        <td>
       <div class="checkout-conent">
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
          </div>
        </td>
      </tr>
      <tr>
         <td>
              <div id="hm-checkout-warp"> 
                  <div class="checkout-title"><?php echo CHECKOUT_OPTION_BUTTON_TEXT;?></div> 
                  <div class="checkout-bottom" align="right">
                  <a href="javascript:void(0);" onclick="document.option_form.submit();">
                  <?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE, 'onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_02_hover.gif\'" onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue_02.gif\'"'); ?>
                  </a> 
                  </div> 
              </div>
              </td> 
      </tr>
      </table>
      
    <input type="hidden" name="action" value="process"> 
    </form>
    </div>
    </div>
    </div>    
  <?php include('includes/float-box.php');?>
  </div>
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

