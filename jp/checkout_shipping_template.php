<?php page_head();?>
<script type="text/javascript" src="./js/jquery-1.3.2.min.js">
</script>
<script type="text/javascript" src="./js/shipping.js">
</script>
<script type="text/javascript" >
$(document).ready(function (){
    clear_all_radio_checked()
});
</script>
</head>
<body> 
<div class="body_shadow" align="center"> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
<tr> 
<td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> 
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
</td> 
<td valign="top" id="contents"><?php echo tep_draw_form('order', tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL')) . tep_draw_hidden_field('action', 'process'); ?> 
<h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>
<table border="0" width="100%" cellspacing="0" cellpadding="0"> 

<tr> 
<td>

<table border="0" width="97%" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
<td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
</tr> 
</table></td> 
<td width="25%">
<table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
</tr> 
</table></td> 
<td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
<td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
<tr> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td> 
<td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td align="center" width="25%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_DELIVERY; ?></td> 
<td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_PAYMENT; ?></td> 
<td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td> 
<td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td> 
</tr> 
</table>

</td> 
</tr> 
<tr> 
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
<tr> 
<td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td>  
<td align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
</tr> 
</table>
</td> 
</tr> 
<?php if($error){?>
<tr><td>
  <div class="shipping_info_error"><?php echo TEXT_SHIPPING_INFO_ERROR;?></div>
</td></tr>
<?php }?>
<tr>
<td>
<?php  //这里写正式的内容?>
<?php require_once DIR_WS_ACTIONS.'checkout_shipping_list.php';?>
</td> 
</tr> 
<tr> 
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
<tr> 
<td class="main"><?php echo '<b>' . TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td> 
<td class="main" align="right"><?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
</tr> 
</table>
</td> 
</tr> 
<tr> 
<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
</tr> 
</table>      </form> 
</td>
<td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</table> 
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
