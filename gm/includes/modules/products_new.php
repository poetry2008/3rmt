<?php
/*
  $Id$
*/
?>

<?php
  if (sizeof($products_new_array) < 1) {
?>
<?php echo TEXT_NO_NEW_PRODUCTS; ?>
<?php
  } else {
 ?>
 <?php
    for($i=0, $n=sizeof($products_new_array); $i<$n; $i++) {
      if (tep_get_special_price($products_new_array[$i]['price'], $products_new_array[$i]['price_offset'], $products_new_array[$i]['small_sum'])) {
        $products_price = '<s>' . $currencies->display_price(tep_get_price($products_new_array[$i]['price'], $products_new_array[$i]['price_offset'], $products_new_array[$i]['small_sum']), tep_get_tax_rate($products_new_array[$i]['tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($products_new_array[$i]['price'], $products_new_array[$i]['price_offset'], $products_new_array[$i]['small_sum']), tep_get_tax_rate($products_new_array[$i]['tax_class_id'])) . '</span>&nbsp;';
      } else {
        $products_price = $currencies->display_price(tep_get_price($products_new_array[$i]['price'], $products_new_array[$i]['price_offset'], $products_new_array[$i]['small_sum']), tep_get_tax_rate($products_new_array[$i]['tax_class_id']));
      }
      /*if (isset($products_new_array[$i]['specials_price'])) {
        $products_price = '<s>' .  $currencies->display_price($products_new_array[$i]['price'], tep_get_tax_rate($products_new_array[$i]['tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price($products_new_array[$i]['specials_price'], tep_get_tax_rate($products_new_array[$i]['tax_class_id'])) . '</span>';
      } else {
        $products_price = $currencies->display_price($products_new_array[$i]['price'], tep_get_tax_rate($products_new_array[$i]['tax_class_id']));
      }*/
?>
<table class="product_listing_content02">
  <tr>
    <td width="<?php echo SMALL_IMAGE_WIDTH + 10; ?>" valign="top" class="main" rowspan="2"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_new_array[$i]['id']) . '">' . tep_image(DIR_WS_IMAGES . 'products/'. $products_new_array[$i]['image'], $products_new_array[$i]['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; ?></td>
    <td valign="top" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_new_array[$i]['id']) . '"><b><u>' . $products_new_array[$i]['name'] . '</u></b></a>';?>
	</td>
	<td class="main" align="right"><?php echo TEXT_DATE_ADDED . ' ' . $products_new_array[$i]['date_added'];?></td>
    <!--<td align="right" valign="middle" class="main"><?php //echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_NEW, tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $products_new_array[$i]['id']) . '">' . tep_image_button('button_in_cart.gif', IMAGE_BUTTON_IN_CART) . '</a>'; ?></td>-->
  </tr>
  <tr>
  <td class="main"><?php echo TEXT_MANUFACTURER . ' ' . $products_new_array[$i]['manufacturer'];?></td>
  <td class="main" align="right"><?php echo TEXT_PRICE . ' ' . $products_price; ?></td>
  </tr>
 </table>
<?php
      if (($i+1) != $n) {
?>
<!--
  <tr>
    <td colspan="3" class="main">&nbsp;</td>
  </tr>
-->
<?php
      }
    }
?>
<?php
  }
?>

