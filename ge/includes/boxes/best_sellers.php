<?php
/*
  $Id$
*/
  if (isset($current_category_id) && ($current_category_id > 0)) {
    $best_sellers_query = tep_db_query("
      select *
      from (
        select distinct p.products_id,
                        pd.products_image,
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
                        pd.products_image,
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
<!-- best_sellers -->
<div class="best_sellers_main">
<div class="top"></div>
<div class="best_sellers_title"><?php echo TEXT_PRODUCTS_SORT;?></div>

<?php
  $info_box_contents = array();
  $info_box_contents[] = array('text' => BOX_HEADING_BESTSELLERS);

  $rows = 0;
  while ($best_sellers = tep_db_fetch_array($best_sellers_query)) {
    $rows++;
?>

<table class="best_table">
  <tr>
    <td rowspan="3">

<a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_sellers['products_id']) ; ?>">
<?php
if (!empty($best_sellers['products_image'])) {
  if (file_exists(DIR_FS_CATALOG.DIR_WS_IMAGES.'products/'.$best_sellers['products_image'])) {
    echo tep_image(DIR_WS_IMAGES.'products/'.$best_sellers['products_image'], $best_sellers['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
  } else {
    echo tep_image(DIR_WS_IMAGES.'new_products_blank_small.gif', $best_sellers['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
  }
} else {
  echo tep_image(DIR_WS_IMAGES.'new_products_blank_small.gif', $best_sellers['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
}
?>
</a>
    </td>
    <td class="best_table_number"><?php echo $rows.TEXT_PRODUCTS_SORT_FIRST_FEW;?></td>
  </tr>
  <tr>
    <td>
<a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_sellers['products_id']) ; ?>"><?php echo $best_sellers['products_name'] ; ?></a>
    </td>
  </tr>
  <tr>
    <td>
<?php
$special_query = tep_db_query("select * from ".TABLE_SPECIALS." where products_id = '".$best_sellers['products_id']."'");
$special_res = tep_db_fetch_array($special_query);
?>
    </td>
  </tr>
</table>
 
  <?php
  }
?>
<div class="bottom"></div>
</div>
<div class="sep" style="display:none;">&nbsp;</div>
<!-- best_sellers_eof -->
<?php
  }
?>
