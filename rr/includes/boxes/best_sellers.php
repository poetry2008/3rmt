<?php
/*
  $Id$
*/
  if (isset($current_category_id) && ($current_category_id > 0)) {
    $best_c_category_id = $current_category_id; 
  } else {
    $best_s_single = true; 
    $best_c_category_id = FF_CID; 
  }
    $best_sellers_raw = "
      select *
      from (
        select distinct p.products_id,
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
          and p2c.categories_id = c.categories_id";
          if (isset($best_s_single)) {
            $best_ca_str = ''; 
            $best_ca_arr = explode(',', $best_c_category_id); 
            foreach ($best_ca_arr as $bs_key => $bs_value) {
              $best_ca_str .= "('".$bs_value."' in (c.categories_id, c.parent_id)) or "; 
            }
            $best_ca_str = '('.substr($best_ca_str, 0, -4).')'; 
            $best_sellers_raw .= " and ".$best_ca_str;
          } else {
            $best_sellers_raw .= " and '" .  $best_c_category_id . "' in (c.categories_id, c.parent_id) ";
          }
         $best_sellers_raw .= "
          order by pd.site_id DESC
        limit 100
        ) p
      where site_id = '0'
         or site_id = '".SITE_ID."' 
      group by products_id
      having p.products_status != '0' and p.products_status != '3' 
      order by products_ordered desc, products_name 
      limit " . MAX_DISPLAY_BESTSELLERS;
  
  $best_sellers_query = tep_db_query($best_sellers_raw);
  if (tep_db_num_rows($best_sellers_query) >= MIN_DISPLAY_BESTSELLERS) {
?>
<!-- best_sellers -->
      <div class="ranking_warpper">
      <div class="menu_top"><?php echo BOX_HEADING_BESTSELLERS;?></div>
      <div class="bestseller_sort">
<?php
  $info_box_contents = array();
  $info_box_contents[] = array('text' => BOX_HEADING_BESTSELLERS);


  $rows = 0;
  while ($best_sellers = tep_db_fetch_array($best_sellers_query)) {
    $rows++;
?>    
<div class="bestseller_sort_info">
<div class="bestseller_text">
<!--sellers num -->
<span><?php echo $rows;?></span>
<a href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_sellers['products_id']); ?>" title="<?php echo $best_sellers['products_name']; ?>">
<?php echo $best_sellers['products_name']; ?></a>


</div> 
<?php
    //获取商品图片 
    $img_array =
    tep_products_images($best_sellers['products_id'],$best_sellers['site_id']);
?>
            <div class="ranking_area_lint">
            <table width="100%" class="ranking_area" align="center" border="0" cellpadding="0" cellspacing="0" summary="ranking">
              <tr>
                   <td width="50" align="center" valign="top"><a href="<?php echo
                   tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .
                       $best_sellers['products_id']); ?>"
                   class="ranking_money_ico"><?php echo
                   tep_image(DIR_WS_IMAGES.'products/'.$img_array[0],$best_sellers['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT); ?></a></td>
          <td valign="top" class="bestseller_description">
            <p><?php echo mb_substr(strip_tags(replace_store_name($best_sellers['products_description'])),0,36); ?>...</p>
                    </td>
                </tr>
            </table>
            </div>
</div>
<?php
    }
?>  
</div>
</div>
<!-- best_sellers_eof -->
<?php
  }
?>
