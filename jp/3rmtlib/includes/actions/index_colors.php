<?php
  $colors_title_query = tep_db_query("select color_name from ".TABLE_COLOR." where color_id = '".(int)$_GET['colors']."'");
  $colors_title = tep_db_fetch_array($colors_title_query);
  
  // ccdd
  $listing_sql = "
  select * 
  from (
    select pd.products_name, 
           p.products_image, 
           cp.color_id, 
           cp.color_image, 
           p.products_id, 
           p.manufacturers_id, 
           p.products_price, 
           p.products_tax_class_id, 
           p.products_price_offset,
           p.products_small_sum,
           p.products_quantity, 
           pd.products_description ,
           pd.site_id
    from " .  TABLE_PRODUCTS_DESCRIPTION . " pd, ".TABLE_COLOR_TO_PRODUCTS." cp, " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
    where p.products_status != '0' 
      and p.products_id = p2c.products_id 
      and pd.products_id = p2c.products_id 
      and pd.language_id = '" . $languages_id . "' 
      and cp.products_id = p.products_id 
      and cp.color_id = '".(int)$_GET['colors']."' 
    order by pd.products_name, pd.site_id DESC
  ) p
  where site_id = '0'
     or site_id = '".SITE_ID."'
  group by products_id
  order by products_name
  ";

  //View
  echo '<td valign="top" id="contents">';
  echo '<h1 class="pageHeading_long">'.HEADING_COLOR_TITLE.$colors_title['color_name'].'</h1>';
  include(DIR_WS_MODULES . FILENAME_COLOR_LISTING);
  echo '</td>';
