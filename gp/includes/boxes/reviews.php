<?php
/*
  $Id$
*/
?>
<!-- reviews //-->
<?php
  if(basename($PHP_SELF) == FILENAME_PRODUCT_INFO){
    $reviews_query = tep_db_query("
        select r.reviews_rating, 
               r.reviews_id, 
               r.customers_name 
        from " .  TABLE_REVIEWS . " r 
        where r.products_id = '" .  (int)$_GET['products_id'] . "' 
          and r.reviews_status = '1' 
          and r.site_id = ".SITE_ID
        );
    if(tep_db_num_rows($reviews_query)) {
      echo  '<div class="pageHeading_long"><h3>'.$product_info['products_name'].BOX_REVIEWS_LINK_TEXT.'</h3></div>'."\n" . '<div class="comment_long"><div class="comment_long_text">'."\n" ;
      while ($reviews = tep_db_fetch_array($reviews_query)) {
        $reviews_des_query = tep_db_query("select reviews_text from ".TABLE_REVIEWS_DESCRIPTION." where reviews_id = '".$reviews['reviews_id']."' and languages_id = '".$languages_id."'"); 
        $reviews_des_res = tep_db_fetch_array($reviews_des_query); 
        echo '<table width="100%" cellspacing="0" cellpadding="2" border="0" class="table_wrap">';
        echo '<tr><td width="60" align="center" style="padding-right:8px; padding-top:5px" rowspan="2" valign="top">';
        echo tep_image(DIR_WS_IMAGES . 'products/' .  $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"');
        echo '</td><td class="main" style="padding-left:5px;">';
        echo '<p class="main"><div class="text_main"> <span><b>' . sprintf(TEXT_REVIEW_BY, tep_output_string_protected($reviews['customers_name'])) .  '</b></span>&nbsp;&nbsp;<span>' . tep_image(DIR_WS_IMAGES . 'stars_' .  $reviews['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating'])) .  '</span>&nbsp;&nbsp;<span>[' .  sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating']) .  ']</span></div>' . nl2br($reviews_des_res['reviews_text']) . "\n" . '</p></td></tr>';
        echo '</table>';
      }
      echo '</div></div>' . "\n";
   } 
}
?>
<!-- reviews_eof //-->

