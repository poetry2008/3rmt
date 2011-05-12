<?php
/*
  $Id$
*/

  if ($random_product = tep_random_select("
      select *
      from (
        select p.products_id, 
               pd.products_name,
               pd.site_id,
               pd.products_status, 
               p.products_price, 
               p.products_price_offset,
               p.products_small_sum,
               p.products_tax_class_id, 
               p.products_bflag, 
               p.products_image
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd
        where (p.products_price_offset != '' or p.products_small_sum != '')
          and pd.language_id = '" . $languages_id . "' 
        order by pd.site_id DESC
        ) p
        where site_id = '0'
           or site_id = '".SITE_ID."'
        group by products_id
        having p.products_status != '0' and p.products_status != '3'
        order by p.products_date_added desc 
        limit " . MAX_RANDOM_SELECT_SPECIALS
        )) {
?>
<!-- specials //-->
          <tr>
            <td>
<?php
    $info_box_contents = array();
    $info_box_contents[] = array('text' => BOX_HEADING_SPECIALS);

    new infoBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_SPECIALS));

    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'center',
                                 'text' => '<a href="' .
                                 tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='
                                   . $random_product["products_id"]) . '">' .
                                 tep_image(DIR_WS_IMAGES . 'products/' .
                                   $random_product['products_image'],
                                   $random_product['products_name'],
                                   SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) .
                                 '</a><br><a href="' .
                                 tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='
                                   . $random_product['products_id']) . '">' .
                                 $random_product['products_name'] . '</a><br><s>' .
                                 $currencies->display_price(tep_get_price($random_product['products_price'], $random_product['products_price_offset'], $random_product['products_small_sum'], $random_product['products_bflag']), tep_get_tax_rate($random_product['products_tax_class_id'])) . '</s><br><span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($random_product['products_price'], $random_product['products_price_offset'], $random_product['products_small_sum']), tep_get_tax_rate($random_product['products_tax_class_id'])) . '</span>');

    new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- specials_eof //-->
<?php
  }
?>
