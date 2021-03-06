<?php
/*
  $Id$
*/
  $categories_path   = explode('_', $_GET['cPath']);
   
  $_categories_query = tep_db_query("
      select categories_name 
      from ".TABLE_CATEGORIES_DESCRIPTION." 
      where categories_id = '".$categories_path[0]."' 
        and language_id = '".$languages_id."' 
        and (site_id = '".SITE_ID."' or site_id = '0')
      order by site_id DESC
      ");
  $_categories       = tep_db_fetch_array($_categories_query);
  $new_c_name        = $_categories['categories_name'];

  if ( (!isset($new_products_category_id)) || ($new_products_category_id == '0') ) {
    
    $new_products_query = tep_db_query("
        select * from (select p.products_id, 
               p.products_real_quantity + p.products_virtual_quantity as products_quantity,
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
    
    $new_products_query = tep_db_query("
        select * from (select distinct p.products_id, 
                        p.products_real_quantity + p.products_virtual_quantity as products_quantity,
                        p.products_tax_class_id, 
                        p.products_price, 
                        p.products_price_offset, 
                        p.products_date_added,
                        pd.site_id, 
                        pd.products_status,
                        p.products_small_sum
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .
        TABLE_CATEGORIES . " c, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id
          and p.products_id = pd.products_id
          and c.parent_id = '" . $new_products_category_id . "' 
          ".(BOX_NEW_PRODUCTS_DAY_LIMIT ? ( " and p.products_date_added > '" . date('Y-m-d H:i:s', time()-(BOX_NEW_PRODUCTS_DAY_LIMIT*86400)) . "'" ) : '')." 
        order by pd.site_id DESC) c where site_id = '".SITE_ID."' or site_id = '0' group by products_id having c.products_status != '0' and c.products_status != '3' order by products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS
    );
  }

  $num_products = tep_db_num_rows($new_products_query);
  if (0 === $num_products) {
    $subcategories = array();
    
    $subcategory_query = tep_db_query("
        select * 
        from " . TABLE_CATEGORIES . " 
        where parent_id=" . $new_products_category_id
    );
    while($subcategory = tep_db_fetch_array($subcategory_query)){
      $subcategories[] = $subcategory['categories_id'];
    }
    if ($subcategories) {
      
      $new_products_query = tep_db_query("
          select * from (select distinct p.products_id, 
                          p.products_real_quantity + p.products_virtual_quantity as products_quantity,
                          p.products_tax_class_id, 
                          p.products_price, 
                          p.products_date_added,
                          pd.site_id, 
                          pd.products_status,
                          p.products_price_offset, 
                          p.products_small_sum
          from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, "
          . TABLE_CATEGORIES . " c, ".TABLE_PRODUCTS_DESCRIPTION." pd 
          where p.products_id = p2c.products_id 
            and p2c.categories_id = c.categories_id
            and p.products_id = pd.products_id
            and c.parent_id in (" . join(',', $subcategories) . ") 
            ".(BOX_NEW_PRODUCTS_DAY_LIMIT ? ( " and p.products_date_added > '" . date('Y-m-d H:i:s', time()-(BOX_NEW_PRODUCTS_DAY_LIMIT*86400)) . "'" ) : '')." 
          order by pd.site_id DESC) c where site_id = '".SITE_ID."' or site_id = '0' group by products_id having c.products_status != '0' and c.products_status != '3'  order by products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS);
      $num_products       = tep_db_num_rows($new_products_query);
    }
  }
  if (0 < $num_products || BOX_NEW_PRODUCTS_DAY_LIMIT) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')));
 //   new contentBoxHeading($info_box_contents);
    $row = 0;
    $col = 0;
?>
<!-- new_products -->
<?php if (0 < $num_products) { ?>
<h3 class="pageHeading"><?php echo $new_c_name; ?>の新着商品</h3>
<div class="comment">
<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="product_list_info">
<?php
    while ($new_products = tep_db_fetch_array($new_products_query)) {
      $product_details = tep_get_product_by_id($new_products['products_id'], SITE_ID, $languages_id);
      
      $new_products['products_name'] = $product_details['products_name'];
      $description_view =
        strip_tags(mb_substr(replace_store_name($product_details['products_description']),0,200));
  
      $row ++;
?>
  <tr>
    <td>
      <table width="480" border="0" cellspacing="0" cellpadding="0" style="margin: 10px;" class="product_listing_content02">
        <tr>
          <td width="60" rowspan="2" " align="left">
          <?php
          //获取商品图片 
          $img_array =
          tep_products_images($new_products['products_id'],$new_products['site_id']);
          ?>
            <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO,
            'products_id=' . $new_products['products_id']) . '">' .
              tep_image(DIR_WS_IMAGES . 'products/' . $img_array[0], $new_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; ?>
          </td>
          <td valign="top">
            <p class="main">
              <img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="5" border="0" alt="">
              <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">'.$new_products['products_name'].'</a>'; ?><br>
            </p>
          </td>
          <td align="right">
            <a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) ; ?>" class="button_description"></a>
          </td>
        </tr>
        <tr>
          <td class="main" colspan="3"><p><span class="smallText"><?php echo $description_view; ?>...</span></p>
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
</div>
<p class="pageBottom"></p>
<?php 
/* if($num_products && 0){
?>
<div align="right" style="padding: 5px 10px 0px 0px;">
      <a href="/pl-<?php echo $categories_path[count($categories_path)-1];?>.html">more</a>
</div>
<?php 
}*/

    } else if (BOX_NEW_PRODUCTS_DAY_LIMIT) {
      //echo "<p style='padding-left:10px;'>".BOX_NEW_PRODUCTS_DAY_LIMIT."日以内に登録された商品はありません。</p>";
    }
?>
<?php
  }
?>
<!-- new_products2_eof -->
