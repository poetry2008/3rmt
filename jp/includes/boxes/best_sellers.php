<?php
/*
  $Id$
*/
  if (isset($current_category_id) && ($current_category_id > 0)) {
    $best_sellers_query = tep_db_query("
      select *
      from (
        select distinct p.products_id,
                        p.products_image,
                        p.products_ordered,
                        pd.products_viewed,
                        pd.products_name,
                        pd.products_status, 
                        pd.products_description,
                        pd.site_id
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .  " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .  TABLE_CATEGORIES . " c 
        where (pd.site_id = '0'
          or pd.site_id = '".SITE_ID."' )
          and p.products_ordered > 0 
          and p.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.products_id = p2c.products_id 
          and p2c.categories_id = c.categories_id 
          and '" .  $current_category_id . "' in (c.categories_id, c.parent_id) 
        order by pd.site_id DESC
        limit 100
        ) p
      where site_id = '0'
         or site_id = '".SITE_ID."' 
      group by products_id
      having p.products_status != '0' and p.products_status != '3' 
      order by products_ordered desc, products_name 
      limit " . MAX_DISPLAY_BESTSELLERS);
  } else {
    $best_sellers_query = tep_db_query("
      select *
      from (
        select distinct p.products_id,
                        p.products_image,
                        p.products_ordered,
                        pd.products_viewed,
                        pd.products_status, 
                        pd.products_name,
                        pd.products_description,
                        pd.site_id
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .  " pd 
        where (pd.site_id = '0'
          or pd.site_id = '".SITE_ID."' )
          and p.products_ordered > 0 
          and p.products_id = pd.products_id 
          and pd.language_id = '" .  $languages_id . "' 
          and p.products_id not in".tep_not_in_disabled_products()." 
        order by pd.site_id DESC
        limit 100
      ) p
      where site_id = '0'
         or site_id = '".SITE_ID."' 
      group by products_id
      having p.products_status != '0' and p.products_status != '3' 
      order by products_ordered desc, products_name 
      limit " . MAX_DISPLAY_BESTSELLERS
        );
  }

  if (tep_db_num_rows($best_sellers_query) >= MIN_DISPLAY_BESTSELLERS) {
?>
<!-- best_sellers //-->
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr><td colspan="2" height="25"><?php echo tep_image(DIR_WS_IMAGES.'design/box/ranking.gif',BOX_HEADING_BESTSELLERS,'171','25'); ?></td></tr>
<?php
  $info_box_contents = array();
  $info_box_contents[] = array('text' => BOX_HEADING_BESTSELLERS);
  
  $rows = 0;
  while ($best_sellers = tep_db_fetch_array($best_sellers_query)) {
    $rows++;
?>
  <tr><td colspan="2">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="24" class="bestseller_img">
          <img src="images/design/box/ranking_<?php echo $rows; ?>.gif" width="24" height="24" alt="<?php echo BOX_HEADING_BESTSELLERS . $rows . TEXT_PRODUCTS_SORT_FIRST_FEW; ?>">
        </td>
        <td class="bestseller_text">
          <a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_sellers['products_id']); ?>"><?php echo $best_sellers['products_name']; ?></a>
        </td>
      </tr>
    </table>
  </td></tr>
  <tr>
    <td width="50" class="bestseller_img" align="center" valign="middle">
    <a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_sellers['products_id']); ?>">
    <?php echo tep_image(DIR_WS_IMAGES.'products/'.$best_sellers['products_image'],$best_sellers['products_name'],SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT); ?>
    </a>
    </td>
    <td valign="top" class="bestseller_description"><?php echo mb_substr(strip_tags(replace_store_name($best_sellers['products_description'])),0,36); ?>...</td>
  </tr> 
<?php
    }
?>
</table>
<!-- best_sellers_eof //-->
<?php
  }
?>
