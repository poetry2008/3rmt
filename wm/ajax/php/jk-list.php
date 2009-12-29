<?php
$navigation->remove_current_page();
?>
<table width="500" height="35" border="0" cellpadding="3" cellspacing="0">
  <tr>
    <td height="35">&nbsp;</td>
    <td height="35" width="35"><a href="#" onclick="cart_non('dis_clist'); return false;"><?php echo tep_draw_separator('pixel_trans.gif', '35', '35'); ?></a></td>
  </tr>
</table>
<table width="500" height="200" border="0" cellpadding="3" cellspacing="0">
  <tr>
    <td valign="top">
	<div style="width: 445px;height:110px;background-repeat: repeat-y;padding: 0px 15px 10px 15px; scrollbar : yes;overflow:auto;">
	<table width="400" border="0" cellspacing="0" cellpadding="2">
<?php
  $products = $cart->get_products();
  for($i=0; $i<sizeof($products); $i++) {
	
	if(mb_strlen($products[$i]['name']) > 25){
	  $pname = mb_substr($products[$i]['name'],0,25) . '...';
	}else{
	  $pname = $products[$i]['name'];
	}
	
	echo '      <tr>' . "\n";
	echo '        <td width="25" valign="top" class="main">' . mb_convert_encoding($products[$i]['quantity'] . '¸Ä','UTF-8','EUC-JP') . '</td>' . "\n";
	echo '        <td valign="top" class="main"><a href="'.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$products[$i]['id']).'">' . mb_convert_encoding($pname,'UTF-8','EUC-JP') . '</a></td>' . "\n";
	echo '        <td align="right" valign="top" class="main">'.mb_convert_encoding($currencies->format($products[$i]['price']),'UTF-8','EUC-JP').'</td>' . "\n";
	echo '      </tr>' . "\n";
	
  }
?>      
    </table>
	</div>
	</td>
  </tr>
  <tr>
    <td height="40" style="padding: 0px 15px 10px 15px;">
	</td>
  </tr>
  <tr>
    <td height="30" style="padding: 0px 15px 10px 15px;" class="main">
	<?php echo mb_convert_encoding('¸Ä¿ô¡§<strong>' . number_format(sizeof($products)).'¸Ä','UTF-8','EUC-JP') . '</strong>'; ?>&nbsp;
	<?php echo mb_convert_encoding('¹ç·×¡§<strong style="font-size:16px; color:red;">' . $currencies->format($cart->show_total()),'UTF-8','EUC-JP') . '</strong>'; ?>&nbsp;
	<?php echo '<a href="'.tep_href_link('step-0.php', '', 'SSL').'">' . tep_image(DIR_WS_IMAGES . 'design/btn_shopping_cart.gif') . '</a>'; ?>&nbsp;
	<?php echo '<a href="'.tep_href_link('step-1.php', '', 'SSL').'">' . tep_image(DIR_WS_IMAGES . 'design/btn_checkout.gif') . '</a>'; ?>&nbsp;
	</td>
  </tr>
</table>