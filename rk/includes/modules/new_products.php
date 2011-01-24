<?php
/*
  $Id$
*/
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
               p.products_image, 
               p.products_tax_class_id, 
               p.products_price, 
               p.products_price_offset, 
               pd.site_id,
               pd.products_status,
               p.products_date_added,
               p.products_small_sum
        from " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_DESCRIPTION." pd 
        where p.products_id = pd.products_id order by pd.site_id DESC) c where site_id = '".SITE_ID."' or site_id = '0' group by products_id having c.products_status != '0' order by products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS
    );
  } else {
    // ccdd
    $new_products_query = tep_db_query("
        select * from (select distinct p.products_id, 
                        p.products_image, 
                        p.products_tax_class_id, 
                        p.products_price, 
                        p.products_price_offset, 
                        pd.site_id,
                        pd.products_status,
                        p.products_date_added,
                        p.products_small_sum
      from " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_DESCRIPTION." pd 
      where p.products_id = p2c.products_id 
        and p2c.categories_id = c.categories_id 
        and c.parent_id = '" . $new_products_category_id . "' 
        and p.products_id = pd.products_id 
      order by pd.site_id DESC) c where site_id = '".SITE_ID."' or site_id = '0' group by products_id having c.products_status != '0' order by products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS
    );
  }

  $num_products = tep_db_num_rows($new_products_query);
  if (0 < $num_products) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')));
     echo '
   <table width="100%"  border="0" cellspacing="0" cellpadding="0" align="center"> 
          <tr>';
 //   new contentBoxHeading($info_box_contents);

    $row = 0;
    $col = 0;
  $info_box_contents = array();
    while ($new_products = tep_db_fetch_array($new_products_query)) {
      $row ++;
      // ccdd
      /*
      $product_query = tep_db_query("
          select products_name, 
                 products_description 
          from " . TABLE_PRODUCTS_DESCRIPTION . " 
          where products_id = '" .  $new_products['products_id'] . "' 
            and language_id = '" . $languages_id . "' 
            and site_id = '".SITE_ID."'
          ");
      $product_details = tep_db_fetch_array($product_query);
      */
  
    $product_details = tep_get_product_by_id($new_products['products_id'], SITE_ID, $languages_id);
  
    $new_products['products_name'] = $product_details['products_name'];
  
    if(mb_strlen($new_products['products_name']) > 17) {
       $products_name = mb_substr($new_products['products_name'],0,17);
      $ten = '..';
      }else{
          $products_name = $new_products['products_name'];
      $ten = '';
    }
  $description_array = explode("|-#-|", $product_details['products_description']);
  $description_view = strip_tags(mb_substr($description_array[0],0,63));
//  $description = strip_tags(mb_substr ($description_array[0],0,50));
?>
            <td width="250"><!-- products_id <?php echo $new_products['products_id'];?>--><table width="250"  border="0" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td width="<?php echo SMALL_IMAGE_WIDTH;?>" style="padding-right:8px; " align="center"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'products/' . $new_products['products_image'], $new_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>' ; ?></td> 
                <td valign="top" style="padding-left:5px; "><p class="main"><img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" align="absmiddle"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">'.$products_name.$ten.'</a>';?><br> 
                  <span class="red"><?php echo $currencies->display_price($new_products['products_price'], tep_get_tax_rate($new_products['products_tax_class_id'])) ; ?></span><br> 
                  <span class="smallText"><?php echo $description_view; ?>...</span></p></td> 
              </tr> 
            </table> 
            <br> 
            <div class="dot">&nbsp;</div></td> 
<?php      
     if (($row/2) == floor($row/2)) {
           echo '</tr>'."\n".'<tr>' ;
         } else {
       echo '<td>'.tep_draw_separator('pixel_trans.gif', '10', '1').'</td>'."\n";
     }  
    }

    //new contentBox($info_box_contents);
  echo '</tr></table>' ;
  
  }
?>
<!-- new_products_eof //-->
