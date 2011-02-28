<?php
/*
  $Id$
  Released under the GNU General Public License
*/
  $categories_path = explode('_', $_GET['cPath']);
  $_categories_query = tep_db_query("select categories_name from categories_description where categories_id = '".$categories_path[0]."' and language_id = '".$languages_id."' and site_id = '".SITE_ID."' order by site_id DESC");
  $_categories = tep_db_fetch_array($_categories_query);
  $new_c_name = $_categories['categories_name'];

  if ( (!isset($new_products_category_id)) || ($new_products_category_id == '0') ) {
    // ccdd
    $new_products_query = tep_db_query("
        select * from (select p.products_id, 
               p.products_image, 
               p.products_tax_class_id, 
               p.products_price, 
               p.products_real_quantity + p.products_virtual_quantity as products_quantity,
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
    $has_child_category_raw = tep_db_query("select * from (select cd.site_id, cd.categories_id, cd.categories_status from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and c.parent_id = '".$new_products_category_id."' order by cd.site_id desc) c where site_id = '0' or site_id = '".SITE_ID."' group by categories_id having c.categories_status != '1' and c.categories_status != '3'"); 
    $has_c_arr = array();
    while ($has_child_category_res = tep_db_fetch_array($has_child_category_raw)) {
      $has_c_arr[] = $has_child_category_res['categories_id']; 
    }
    if (!empty($has_c_arr)) {
      $new_products_query = tep_db_query("
          select * from (select distinct p.products_id, 
                          p.products_image, 
                          p.products_tax_class_id, 
                          p.products_price, 
                          p.products_real_quantity + p.products_virtual_quantity as products_quantity,
                          p.products_price_offset, 
                          p.products_date_added,
                          pd.site_id,
                          pd.products_status,
                          p.products_small_sum
        from " . TABLE_PRODUCTS . " p ," . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c , ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = p2c.products_id 
          and p2c.categories_id = c.categories_id 
          and c.categories_id in (" . implode(',', $has_c_arr)  . ") 
          and p.products_id = pd.products_id 
          ".(BOX_NEW_PRODUCTS_DAY_LIMIT ? ( " and p.products_date_added > '" . date('Y-m-d H:i:s', time()-(BOX_NEW_PRODUCTS_DAY_LIMIT*86400)) . "'" ) : '')." 
          order by pd.site_id DESC) c where site_id = '".SITE_ID."' or site_id = '0' group by products_id having c.products_status != '0' and c.products_status != '3' order by products_date_added desc 
        limit " . MAX_DISPLAY_NEW_PRODUCTS
      );
    } else {
      $new_products_query = tep_db_query("
          select * from (select distinct p.products_id, 
                          p.products_image, 
                          p.products_tax_class_id, 
                          p.products_price, 
                          p.products_real_quantity + p.products_virtual_quantity as products_quantity,
                          p.products_price_offset, 
                          p.products_date_added,
                          pd.site_id,
                          pd.products_status,
                          p.products_small_sum
        from " . TABLE_PRODUCTS . " p ," . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c , ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = p2c.products_id 
          and p2c.categories_id = c.categories_id 
          and c.parent_id = '" . $new_products_category_id . "' 
          and p.products_id = pd.products_id 
          ".(BOX_NEW_PRODUCTS_DAY_LIMIT ? ( " and p.products_date_added > '" . date('Y-m-d H:i:s', time()-(BOX_NEW_PRODUCTS_DAY_LIMIT*86400)) . "'" ) : '')." 
          order by pd.site_id DESC) c where site_id = '".SITE_ID."' or site_id = '0' group by products_id having c.products_status != '0' and c.products_status != '3' order by products_date_added desc 
        limit " . MAX_DISPLAY_NEW_PRODUCTS
      );
    }
  }

  $num_products = tep_db_num_rows($new_products_query);
  if (0 === $num_products) {
    $subcategories = array();
    //$subcategory_query = tep_db_query("select * from " . TABLE_CATEGORIES . " where parent_id=" . $new_products_category_id);
    $subcategory_query = tep_db_query("select * from (select cd.site_id, cd.categories_status, cd.categories_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and parent_id = '".$new_products_category_id."' order by cd.site_id desc) c where site_id = '0' or site_id = '".SITE_ID."' group by categories_id having c.categories_status != '1' and c.categories_status != '3'"); 
    
    while($subcategory = tep_db_fetch_array($subcategory_query)){
      $subcategories[] = $subcategory['categories_id'];
    }
    if ($subcategories) {
      $new_products_query = tep_db_query("
        select * from (select distinct p.products_id, 
                        p.products_real_quantity + p.products_virtual_quantity as products_quantity,
                        p.products_image, 
                        p.products_tax_class_id, 
                        p.products_date_added,
                        pd.site_id,
                        pd.products_status,
                        if(s.status, s.specials_new_products_price, p.products_price) as products_price 
        from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .  TABLE_CATEGORIES . " c, ".TABLE_PRODUCTS_DESCRIPTION." pd 
        where p.products_id = p2c.products_id 
          and p2c.categories_id = c.categories_id 
          and c.parent_id in (" . join(',', $subcategories) . ") 
          and p.products_id = pd.products_id 
          ".(BOX_NEW_PRODUCTS_DAY_LIMIT ? ( " and p.products_date_added > '" . date('Y-m-d H:i:s', time()-(BOX_NEW_PRODUCTS_DAY_LIMIT*86400)) . "'" ) : '')." 
          order by pd.site_id DESC) c where site_id = '".SITE_ID."' or site_id = '0' group by products_id having c.products_status != '0' and c.products_status != '3' order by products_date_added desc limit " . MAX_DISPLAY_NEW_PRODUCTS
      );
      $num_products       = tep_db_num_rows($new_products_query);
    }
  }
  if (0 < $num_products || BOX_NEW_PRODUCTS_DAY_LIMIT) {
    $info_box_contents = array();
    $info_box_contents[] = array('text' => sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')));
 //   new contentBoxHeading($info_box_contents);

    $row = 0;
    $col = 0;
  $info_box_contents = array();
?>
<!-- new_products //-->
<?php 
if (0 < $num_products) {
?>
<h2 class="pageHeading"><?php echo sprintf(TABLE_HEADING_NEW_PRODUCTS, strftime('%B')) ; ?></h2>
<?php
    while ($new_products = tep_db_fetch_array($new_products_query)) {
      $new_products['products_name'] = tep_get_products_name($new_products['products_id']);
      if (tep_get_special_price($new_products['products_price'], $new_products['products_price_offset'], $new_products['products_small_sum'])) {
        $p =  '<s>' . $currencies->display_price(tep_get_price($new_products['products_price'], $new_products['products_price_offset'], $new_products['products_small_sum']), tep_get_tax_rate($new_products['products_tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($new_products['products_price'], $new_products['products_price_offset'], $new_products['products_small_sum']), tep_get_tax_rate($new_products['products_tax_class_id'])) . '</span>&nbsp;';
      } else {
        $p =  $currencies->display_price(tep_get_price($new_products['products_price'], $new_products['products_price_offset'], $new_products['products_small_sum']), tep_get_tax_rate($new_products['products_tax_class_id']));
      }
      $info_box_contents[$row][$col] = array('align' => 'center', 'params' => 'class="smallText" width="33%" valign="top"', 'text' => '<a href="' .  tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .  $new_products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'products/' . $new_products['products_image'], $new_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' .  tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .  $new_products['products_id']) . '">' . $new_products['products_name'] .  '</a>');

      $col ++;
      if ($col > 2) {
        $col = 0;
        $row ++;
      }
    }
    new contentBox($info_box_contents);
    /*
    if ($num_products && 0) {?>
<div align="right" style="padding: 5px 20px 0px 0px;">
      <a href="/pl-<?php echo $categories_path[count($categories_path)-1];?>.html">more</a>
</div>
<?php 
    }*/
  } else if (BOX_NEW_PRODUCTS_DAY_LIMIT) {
    //echo "<p style='padding-left:10px;'>".BOX_NEW_PRODUCTS_DAY_LIMIT."日以内に登録された商品はありません。</p>";
  }
  }
?>
<!-- new_products_eof //-->
