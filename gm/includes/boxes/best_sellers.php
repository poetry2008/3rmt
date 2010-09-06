<?php
/*
  $Id$
*/
  if (isset($current_category_id) && ($current_category_id > 0)) {
    // ccdd
    $best_sellers_query = tep_db_query("
      select *
      from (
        select distinct p.products_id,
                        p.products_image,
                        p.products_ordered,
                        pd.products_viewed,
                        pd.products_name,
                        pd.products_description,
                        pd.site_id
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .  " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " .  TABLE_CATEGORIES . " c 
        where p.products_status != '0' 
          and p.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.products_id = p2c.products_id 
          and p2c.categories_id = c.categories_id 
          and '" .  $current_category_id . "' in (c.categories_id, c.parent_id) 
        order by pd.site_id DESC
        ) p
      where site_id = '0'
         or site_id = '".SITE_ID."' 
      group by products_id
      order by products_viewed desc, products_name 
      limit " . MAX_DISPLAY_BESTSELLERS);
    if (tep_db_num_rows($best_sellers_query) == 0) {
      $best_sellers_query = tep_db_query("
        select *
        from (
          select distinct p.products_price, 
                          p.products_id, 
                          p.products_image, 
                          pd.products_name, 
                          pd.products_description, 
                          pd.products_viewed,
                          pd.site_id
          from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd 
        where p.products_status != '0' 
          and p.products_ordered > 0 
          and p.products_id = pd.products_id 
          and pd.language_id = '" .  $languages_id . "' 
          and p.products_id not in".tep_not_in_disabled_products()." 
          order by pd.site_id DESC
        ) p
        where site_id = '0'
           or site_id = '".SITE_ID."' 
        group by products_id
        order by products_viewed desc 
        limit ".MAX_DISPLAY_BESTSELLERS
      ); 
    }
  } else {
    // ccdd
    $best_sellers_query = tep_db_query("
      select *
      from (
        select distinct p.products_id,
                        p.products_image,
                        p.products_ordered,
                        pd.products_viewed,
                        pd.products_name,
                        pd.products_description,
                        pd.site_id
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION .  " pd 
        where p.products_status != '0' 
          and p.products_ordered > 0 
          and p.products_id = pd.products_id 
          and pd.language_id = '" .  $languages_id . "' 
          and p.products_id not in".tep_not_in_disabled_products()." 
        order by pd.site_id DESC
      ) p
      where site_id = '0'
         or site_id = '".SITE_ID."' 
      group by products_id
      order by products_viewed desc, products_name 
      limit " . MAX_DISPLAY_BESTSELLERS
        );
  }
  if (
    tep_db_num_rows($best_sellers_query) >= MIN_DISPLAY_BESTSELLERS 
    && ((isset($current_category_id) && ($current_category_id > 0)) ? tep_show_warning($current_category_id) != 1 : true)
  ) {
?>
<!-- best_sellers //-->
<div class="best_sellers_main">
<div class="top"></div>
<div class="best_sellers_title">アクセスランキング</div>

<?php
  $info_box_contents = array();
  $info_box_contents[] = array('text' => BOX_HEADING_BESTSELLERS);

  // new infoBoxHeading($info_box_contents, false, false);
  $rows = 0;
  //$bestsellers_list = '<table border="0" width="100%" cellspacing="0" cellpadding="1">';
  while ($best_sellers = tep_db_fetch_array($best_sellers_query)) {
    $rows++;
    // $bestsellers_list .= '<tr><td class="infoBoxContents" valign="top">' . tep_row_number_format($rows) . '.</td><td class="infoBoxContents"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_sellers['products_id']) . '">' . $best_sellers['products_name'] . '</a></td></tr>';
?>

<table class="best_table">
  <tr>
    <td rowspan="3">

<a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_sellers['products_id']) ; ?>">
<?php
if (!empty($best_sellers['products_image'])) {
  if (file_exists(DIR_FS_CATALOG.DIR_WS_IMAGES.$best_sellers['products_image'])) {
    echo tep_image2(DIR_WS_IMAGES.$best_sellers['products_image'], $best_sellers['products_name'], 71, 71);
  } else {
    echo tep_image2(DIR_WS_IMAGES.'new_products_blank_small.gif', $best_sellers['products_name'], 71, 71);
  }
} else {
  echo tep_image2(DIR_WS_IMAGES.'new_products_blank_small.gif', $best_sellers['products_name'], 71, 71);
}
?>
</a>
    </td>
    <td class="best_table_number"><?php echo $rows.'位';?></td>
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
<!-- best_sellers_eof //-->
<?php
  }
?>
