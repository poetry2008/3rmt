<?php
/*
  $Id$
*/

  if (isset($current_category_id) && ($current_category_id > 0)) {
    // ccdd
    // del distinct 10.8.2
    $best_sellers_query = tep_db_query("
      select *
      from (
        select p.products_id,
                        p.products_image,
                        p.products_ordered,
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
      having p.products_status != '0' 
      order by products_ordered desc, products_name 
      limit " . MAX_DISPLAY_BESTSELLERS);
    
  } else {
    // ccdd
    // del distinct 10.8.2
    $best_sellers_query = tep_db_query("
      select *
      from (
        select p.products_id,
                        p.products_image,
                        p.products_ordered,
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
      having p.products_status != '0' 
      order by products_ordered desc, products_name 
      limit " . MAX_DISPLAY_BESTSELLERS
        );
  }

  if (tep_db_num_rows($best_sellers_query) >= MIN_DISPLAY_BESTSELLERS) {
?>
<!-- best_sellers //-->
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="background: url(images/design/box/ranking_content_bg.gif) repeat-y; margin-bottom: 10px;" summary="best sellers box">
  <tr>
      <td height="39"><img width="171" height="39" alt="RMTランキング" src="images/design/box/ranking.gif"><?php //echo tep_image(DIR_WS_IMAGES.'design/box/ranking.gif',BOX_HEADING_BESTSELLERS,'171','39'); ?></td>
    </tr>
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
  <tr>
      <td>
          <div class="ranking_area">       
            <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" summary="ranking">
                <tr>
                    <td width="21" class="bestseller_img">
                        <img src="images/design/box/ranking_<?php echo $rows; ?>.gif" width="21" height="16" alt="<?php echo 'ランキング' . $rows . '位'; ?>">
                    </td>
                    <td class="bestseller_text">
                        <nobr><a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_sellers['products_id']); ?>" title="<?php echo $best_sellers['products_name']; ?>"><?php echo $best_sellers['products_name']; ?></a></nobr>
                    </td>
                </tr>
            </table>
            <table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" summary="ranking">
              <tr>
                   <td width="50" align="center" valign="middle"><a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_sellers['products_id']); ?>" class="ranking_money_ico"><?php echo tep_image2(DIR_WS_IMAGES.'products/'.$best_sellers['products_image'],$best_sellers['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT); ?></a></td>
          <td valign="top" class="bestseller_description">
            <p>
              <?php echo mb_substr(strip_tags($best_sellers['products_description']),0,30); ?>...
                      </p>
                    </td>
                </tr>
            </table>
            </div>
    </td>
    </tr>  
<?php
    }
?>  <tr>
    <td colspan="2"><img src="images/design/box/ranking_bottom_bg.gif" width="171" height="11" alt="" ></td>
  </tr>
</table>
<!-- best_sellers_eof //-->
<?php
  }
?>
