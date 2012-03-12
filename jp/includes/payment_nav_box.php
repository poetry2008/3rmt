<table border="0" width="100%" cellspacing="0" cellpadding="0" > 
  <tr> 
    <td width="20%">
      <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
	<tr> 
	  <td width="50%" align="right">
	    <?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?>
	  </td> 
	  <td width="50%">
	    <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?>
	  </td> 
	</tr> 
      </table>
    </td> 
    <td width="20%">
      <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
	<tr> 
	  <td width="50%">
	    <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?>
	  </td> 
	  <td>
	    <?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?>
	  </td> 
	  <td width="50%">
	    <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?>
	  </td> 
	</tr> 
      </table>
    </td> 
    <td width="20%">
      <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?>
    </td> 
    <td width="20%">
      <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
	<tr> 
	  <td width="50%">
	    <?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?>
	  </td> 
	  <td width="50%">
	    <?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?>
	  </td> 
	</tr> 
      </table>
    </td> 
  </tr> 
  <tr> 
    <td align="center" width="20%" class="checkoutBarFrom">
      <?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; ?>
    </td> 
    <td align="center" width="20%" class="checkoutBarCurrent">
      <?php echo CHECKOUT_BAR_PAYMENT; ?>
    </td> 
    <td align="center" width="20%" class="checkoutBarTo">
      <?php echo CHECKOUT_BAR_CONFIRMATION; ?>
    </td> 
    <td align="center" width="20%" class="checkoutBarTo">
      <?php echo CHECKOUT_BAR_FINISHED; ?>
    </td> 
  </tr> 
</table>
