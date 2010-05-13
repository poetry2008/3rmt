<?php
/*
  $Id$
*/
  $categories_path   = explode('_', $_GET['cPath']);
  //ccdd
  $_categories_query = tep_db_query("
      select categories_name 
      from ".TABLE_CATEGORIES_DESCRIPTION." 
      where categories_id = '".$categories_path[0]."' 
        and language_id = '".$languages_id."' 
        and (site_id = '".SITE_ID."' or site_id = '0')
      ");
  $_categories       = tep_db_fetch_array($_categories_query);
  $new_c_name        = $_categories['categories_name'];

  if ( (!isset($new_products_category_id)) || ($new_products_category_id == '0') ) {
    // ccdd
    $new_products_query = tep_db_query("
        select p.products_id, 
               p.products_quantity, 
               p.products_image, 
               p.products_tax_class_id, 
               if(s.status, s.specials_new_products_price, p.products_price) as products_price 
        from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id 
        where products_status = '1' 
        order by p.products_date_added desc 
        limit " . MAX_DISPLAY_NEW_PRODUCTS
    );
  } else {
    // ccdd
    $new_products_query = tep_db_query("
        select distinct p.products_id, 
                        p.products_quantity, 
                        p.products_image, 
                        p.products_tax_class_id, 
                        if(s.status, s.specials_new_products_price, p.products_price) as products_price 
        from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c 
        where p.products_id = p2c.products_id 
          and p2c.categories_id = c.categories_id 
          and c.parent_id = '" . $new_products_category_id . "' 
          and p.products_status = '1' 
        order by p.products_date_added desc 
        limit " . MAX_DISPLAY_NEW_PRODUCTS
    );
  }

  $num_products = tep_db_num_rows($new_products_query);
  if (0 === $num_products) {
    $subcategories = array();
    // ccdd
    $subcategory_query = tep_db_query("
        select * 
        from " . TABLE_CATEGORIES . " 
        where parent_id=" . $new_products_category_id
    );
    while($subcategory = tep_db_fetch_array($subcategory_query)){
      $subcategories[] = $subcategory['categories_id'];
    }
    if ($subcategories) {
      // ccdd
      $new_products_query = tep_db_query("
          select distinct p.products_id, 
                          p.products_quantity, 
                          p.products_image, 
                          p.products_tax_class_id, 
                          if(s.status, s.specials_new_products_price, p.products_price) as products_price 
          from " . TABLE_PRODUCTS . " p 
            left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c 
          where p.products_id = p2c.products_id 
            and p2c.categories_id = c.categories_id 
            and c.parent_id in (" . join(',', $subcategories) . ") 
            and p.products_status = '1' 
          order by p.products_date_added desc 
          limit " . MAX_DISPLAY_NEW_PRODUCTS);
      $num_products       = tep_db_num_rows($new_products_query);
    }
  }
  if (0 < $num_products) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')));
 //   new contentBoxHeading($info_box_contents);
    $row = 0;
    $col = 0;
?>
<!-- new_products //-->
<h3 class="pageHeading"><?php echo $new_c_name; ?>の新着商品</h3>
<div class="comment">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<?php
    while ($new_products = tep_db_fetch_array($new_products_query)) {
      $product_details = tep_get_product_by_id($new_products['products_id'], SITE_ID, $languages_id);
      
      $new_products['products_name'] = $product_details['products_name'];
      $description_view = strip_tags(mb_substr($product_details['products_description'],0,110));
  
      $row ++;
?>
  <tr>
    <td>
      <table width="480" border="0" cellspacing="0" cellpadding="0" style="margin: 10px;">
        <tr>
          <td width="<?php echo MALL_IMAGE_WIDTH;?>" rowspan="2" style="padding-right:8px; " align="center">
            <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $new_products['products_image'], $new_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; ?>
          </td>
          <td height="40" colspan="2" valign="top" style="padding-left:5px; ">
            <p class="main">
              <img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" alt="">
              <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">'.$new_products['products_name'].'</a>'; ?><br>
              <span class="smallText"><?php echo $description_view; ?>...</span>
            </p>
          </td>
        </tr>
        <tr>
          <td class="main" style="padding-left:5px; ">
            <p class="main">
              <?php echo '残り&nbsp;' . number_format($new_products['products_quantity']) . '個'; ?>
              &nbsp;&nbsp;&nbsp;
              <span class="red"><?php echo $currencies->display_price($new_products['products_price'], tep_get_tax_rate($new_products['products_tax_class_id'])) ; ?></span>
            </p>
          </td>
          <td align="right">
            <a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) ; ?>" class="button_description"></a>
          </td>
        </tr>
      </table>
      <div class="dot">&nbsp;</div>
    </td>
  </tr>
<?php
  }
?>
</table>
<?php if($num_products && 0){?>
<div align="right" style="padding: 5px 10px 0px 0px;">
      <a href="/pl-<?php echo $categories_path[count($categories_path)-1];?>.html">more</a>
</div>
<?php }?>
</div>
<p class="pageBottom"></p>
<?php
  }
?>
<!-- new_products_eof //-->
