<?php
/*
  $Id$
*/

  //ccdd
  if ($random_product = tep_random_select("select distinct p.products_id, p.products_image, p.products_tax_class_id, p.products_price, p.products_price_offset, p.products_small_sum from " . TABLE_PRODUCTS . "p , ".TABLE_PRODUCTS_DESCRIPTION." pd  where pd.products_status != '0' and p.products_id = pd.products_id order by p.products_date_added desc limit " . MAX_RANDOM_SELECT_NEW)) {
?>
<!-- whats_new //-->
          <tr>
            <td>
<?php
    $random_product['products_name'] = tep_get_products_name($random_product['products_id']);
    $random_product['specials_new_products_price'] = tep_get_products_special_price($random_product['products_id']);

    $info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_WHATS_NEW);

    new infoBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_PRODUCTS_NEW));

    /*if (tep_not_null($random_product['specials_new_products_price'])) {
      $whats_new_price = '<s>' . $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</s><br>';
      $whats_new_price .= '<span class="productSpecialPrice">' . $currencies->display_price($random_product['specials_new_products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</span>';
    } else {
      $whats_new_price = $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id']));
    }*/
    if (tep_get_special_price($random_product['products_price'], $random_product['products_price_offset'], $random_product['products_small_sum'])) {
      $whats_new_price = '<s>' . $currencies->display_price(tep_get_price($random_product['products_price'], $random_product['products_price_offset'], $random_product['products_small_sum'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($random_product['products_price'], $random_product['products_price_offset'], $random_product['products_small_sum'])) . '</span>&nbsp;';
    } else {
      $whats_new_price = $currencies->display_price(tep_get_price($random_product['products_price'], $random_product['products_price_offset'], $random_product['products_small_sum']));
    }

    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'center',
                                 'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'products/' . $random_product['products_image'], $random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id']) . '">' . $random_product['products_name'] . '</a><br>' . $whats_new_price);

    new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- whats_new_eof //-->
<?php
  }
?>
