<?php
/*
  $Id$
*/
?>
<table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (sizeof($products_new_array) < 1) {
?>
  <tr>
    <td class="main"><?php echo TEXT_NO_NEW_PRODUCTS; ?></td>
  </tr>
<?php
  } else {
    for($i=0, $n=sizeof($products_new_array); $i<$n; $i++) {
      if (tep_get_special_price($products_new_array[$i]['price'], $products_new_array[$i]['price_offset'], $products_new_array[$i]['small_sum'])) {
        $products_price = '<s>' .
          $currencies->display_price(tep_get_price($products_new_array[$i]['price'],
                $products_new_array[$i]['price_offset'],
                $products_new_array[$i]['small_sum'],
                $products_new_array[$i]['products_bflag'],
                $products_new_array[$i]['price_type']), tep_get_tax_rate($products_new_array[$i]['tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($products_new_array[$i]['price'], $products_new_array[$i]['price_offset'], $products_new_array[$i]['small_sum']), tep_get_tax_rate($products_new_array[$i]['tax_class_id'])) . '</span>&nbsp;';
      } else {
        $products_price =
          $currencies->display_price(tep_get_price($products_new_array[$i]['price'],
                $products_new_array[$i]['price_offset'],
                $products_new_array[$i]['small_sum'],
                $products_new_array[$i]['products_bflag'],
                $products_new_array[$i]['price_type']), tep_get_tax_rate($products_new_array[$i]['tax_class_id']));
      }
?>
  <tr>
    <td width="<?php echo SMALL_IMAGE_WIDTH + 10; ?>" valign="top" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_new_array[$i]['id']) . '">' . tep_image(DIR_WS_IMAGES . $products_new_array[$i]['image'], $products_new_array[$i]['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; ?></td>
    <td valign="top" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_new_array[$i]['id']) . '"><b><u>' . $products_new_array[$i]['name'] . '</u></b></a><br>' . TEXT_DATE_ADDED . ' ' . $products_new_array[$i]['date_added'] . '<br>' . TEXT_MANUFACTURER . ' ' . $products_new_array[$i]['manufacturer'] . '<br><br>' . TEXT_PRICE . ' ' . $products_price; ?></td>
    <td align="right" valign="middle" class="main"></td>
  </tr>
<?php
      if (($i+1) != $n) {
?>
  <tr>
    <td colspan="3" class="main">&nbsp;</td>
  </tr>
<?php
      }
    }
  }
?>
</table>
