<?php
/*
  $Id$
*/

  if (isset($current_category_id) && ($current_category_id > 0)) {
    // ccdd
    // del distinct 10.8.2
    $best_sellers_query = tep_db_query("
        select p.products_id,
                        p.products_image,
                        p.products_ordered,
                        pd.products_name,
                        pd.products_status, 
                        pd.products_description,
                        pd.site_id
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .  " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .  TABLE_CATEGORIES . " c 
        where pd.site_id = '".SITE_ID."'
          and p.products_ordered > 0 
          and p.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.products_id = p2c.products_id 
          and p2c.categories_id = c.categories_id 
          and '" .  $current_category_id . "' in (c.categories_id, c.parent_id) 
          and pd.products_status != '0' and pd.products_status != '3' 
          order by p.products_ordered desc, pd.products_name 
      limit " . MAX_DISPLAY_BESTSELLERS);
    
  } else {
    // ccdd
    // del distinct 10.8.2
    $best_sellers_query = tep_db_query("
        select p.products_id,
                        p.products_image,
                        p.products_ordered,
                        pd.products_name,
                        pd.products_status, 
                        pd.products_description,
                        pd.site_id
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .  " pd 
        where pd.site_id = '".SITE_ID."'
          and p.products_ordered > 0 
          and p.products_id = pd.products_id 
          and pd.language_id = '" .  $languages_id . "' 
          and p.products_id not in".tep_not_in_disabled_products()." 
          and pd.products_status != '0' and pd.products_status != '3' 
        order by p.products_ordered desc, pd.products_name 
      limit " . MAX_DISPLAY_BESTSELLERS
        );
  }
  
  if (tep_db_num_rows($best_sellers_query) >= MIN_DISPLAY_BESTSELLERS) {
?>
<!-- best_sellers //-->
      <div class="ranking_warpper">
      <div class="menu_top"><span>ランキング</span></div>
<?php
  $info_box_contents = array();
  $info_box_contents[] = array('text' => BOX_HEADING_BESTSELLERS);


  $rows = 0;
  while ($best_sellers = tep_db_fetch_array($best_sellers_query)) {
    $rows++;
?>    


<div class="bestseller_text"><div class="bestseller_number"><?php echo $rows;?></div><a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_sellers['products_id']); ?>" title="<?php echo $best_sellers['products_name']; ?>"><?php echo $best_sellers['products_name']; ?></a>
<div class="bestseller_des">
<a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .  $best_sellers['products_id']); ?>" title="<?php echo $best_sellers['products_name']; ?>"><?php echo tep_image2(DIR_WS_IMAGES.'products/'.$best_sellers['products_image'], $best_sellers['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);?></a>
<span>
<?php echo mb_substr(strip_tags(replace_store_name($best_sellers['products_description'])),0, 30);?>...
</span>
</div> 
</div> 

<?php
    }
?>  
</div>
<!-- best_sellers_eof //-->
<?php
  }
?>
