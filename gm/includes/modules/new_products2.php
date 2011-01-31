<?php
/*
  $Id$
*/
  $categories_path = explode('_', $_GET['cPath']);
  //ccdd
  $_categories_query = tep_db_query("
      select categories_name 
      from ".TABLE_CATEGORIES_DESCRIPTION." 
      where categories_id = '".$categories_path[0]."' 
        and language_id = '".$languages_id."' 
        and (site_id = '".SITE_ID."' or site_id = '0')
      ");
  $_categories = tep_db_fetch_array($_categories_query);
  $new_c_name = $_categories['categories_name'];
?>
<!-- new_products //-->
        <h2> 
          <table width="100%" border="0" align="center" cellpadding="0"
          cellspacing="0" summary="<?php echo sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B'));?>"> 
            <tr> 
              <td width="63"><img
              src="images/design/contents/title_newproducts_left.jpg" width="63"
              height="23" title="<?php echo sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B'));?>"></td> 
              <td background="images/design/contents/title_bg.jpg">&nbsp;</td> 
              <td width="47"><img
              src="images/design/contents/title_newproducts_right.jpg" width="47"
              height="23" title="<?php echo sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B'));?>"></td> 
            </tr> 
          </table> 
        </h2>
<?php
  if ( (!isset($new_products_category_id)) || ($new_products_category_id == '0') ) {
    // ccdd
    $new_products_query = tep_db_query("
        select * from (select p.products_id, 
               p.products_quantity, 
               p.products_image, 
               p.products_tax_class_id, 
               p.products_price, 
               p.products_price_offset, 
               p.products_date_added,
               pd.site_id,
               pd.products_status,
               p.products_small_sum
        from " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_DESCRIPTION." pd 
        where p.products_id = pd.products_id 
        order by pd.site_id DESC) c where site_id = '".SITE_ID."' or site_id = '0' group by products_id having c.products_status != '0' and c.products_status != '3' order by products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS
    );
  } else {
    // ccdd
    $new_products_query = tep_db_query("
        select * from (select distinct p.products_id, 
                        p.products_quantity, 
                        p.products_image, 
                        p.products_tax_class_id, 
                        p.products_price, 
                        p.products_price_offset, 
                        p.products_date_added,
                        pd.site_id,
                        pd.products_status,
                        p.products_small_sum
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .  TABLE_CATEGORIES . " c, ".TABLE_PRODUCTS_DESCRIPTION." pd 
        where p.products_id = p2c.products_id 
          and p2c.categories_id = c.categories_id 
          and c.parent_id = '" . $new_products_category_id . "' 
          and p.products_id = pd.products_id 
        order by pd.site_id DESC) c where site_id = '".SITE_ID."' or site_id = '0' group by products_id having c.products_status != '0' and c.products_status != '3'  order by products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS
    );
  }

  $num_products = tep_db_num_rows($new_products_query);
  if (0 < $num_products) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')));

 //   new contentBoxHeading($info_box_contents);

    $row = 0;
    $col = 0;
    echo '<table width="100%"  border="0" cellspacing="0" cellpadding="0">';
    while ($new_products = tep_db_fetch_array($new_products_query)) {
      $product_details = tep_get_product_by_id($new_products['products_id'], SITE_ID, $languages_id);
  
      $new_products['products_name'] = $product_details['products_name'];
      $description_view = strip_tags(mb_substr(replace_store_name($product_details['products_description']),0,110));

      $row ++;
?>
  <tr>
    <td>
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="<?php echo SMALL_IMAGE_WIDTH;?>" rowspan="2" style="padding-right:8px; " align="center"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'products/' . $new_products['products_image'], $new_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>' ; ?></td>
            <td height="40" colspan="2" valign="top" style="padding-left:5px; "><p class="main"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" align="absmiddle"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">'.$new_products['products_name'].'</a>';?><br>
                    <span class="smallText"><?php echo $description_view; ?>..</span></p></td>
              </tr>
              <tr>
                <td class="main" style="padding-left:5px; ">
<?php
      if (tep_get_special_price($new_products['products_price'], $new_products['products_price_offset'], $new_products['products_small_sum'])) {
        echo '<s>' . $currencies->display_price(tep_get_price($new_products['products_price'], $new_products['products_price_offset'], $new_products['products_small_sum']), tep_get_tax_rate($new_products['products_tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($new_products['products_price'], $new_products['products_price_offset'], $new_products['products_small_sum']), tep_get_tax_rate($new_products['products_tax_class_id'])) . '</span>&nbsp;';
      } else {
        echo $currencies->display_price(tep_get_price($new_products['products_price'], $new_products['products_price_offset'], $new_products['products_small_sum']));
      }
?>                </td>
                <td align="right">
        <?php echo '<span id="' . $new_products['products_id'] . '"><a href="'.tep_href_link(FILENAME_PRODUCT_INFO,'products_id='.$new_products['products_id'].'&action=buy_now').'" onClick="sendData(\'' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $new_products['products_id']) . '\',\'' . displaychange . '\',\'' . $new_products['products_id'] . '\'); return false;"><img src="images/design/button/button_in_cart.jpg" border="0"></a></span>'; ?>        
        &nbsp;&nbsp;&nbsp;<a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) ; ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/button/button_description.jpg',IMAGE_BUTTON_DEC);?></a></td>
              </tr>
            </table>
            <br>
            <div class="dot">&nbsp;</div></td>
          </tr>      
<?php      
  }
  echo '</table>' ;
  }
?>
<!-- new_products_eof //-->
