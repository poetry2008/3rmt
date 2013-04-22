<?php
/*
  $Id$
*/
?>
<!-- reviews //-->
<?php
    // ccdd
  if(basename($PHP_SELF) == FILENAME_PRODUCT_INFO){
    // ccdd
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
      echo  '<div class="pageHeading_long"><h3>'.$product_info['products_name'].BOX_REVIEWS_LINK_TEXT.'</h3></div>'."\n" . '<div class="comment_long">'."\n" ;
      while ($reviews = tep_db_fetch_array($reviews_query)) {
        $reviews_des_query = tep_db_query("select reviews_text from ".TABLE_REVIEWS_DESCRIPTION." where reviews_id = '".$reviews['reviews_id']."' and languages_id = '".$languages_id."'"); 
        $reviews_des_res = tep_db_fetch_array($reviews_des_query); 
        echo '<div class="comment_long_text"><p class="main">';
        echo '<div>';
        echo '<span><b>' . sprintf(TEXT_REVIEW_BY, tep_output_string_protected($reviews['customers_name'])) .  '</b>&nbsp;&nbsp;</span>';
        echo tep_image(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] .
            '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating'])) ; 
        echo '<span>[' . sprintf(BOX_REVIEWS_TEXT_OF_5_STARS,
            $reviews['reviews_rating']) . ']</span>';
        echo '</div>';
        echo '<br>' . nl2br($reviews_des_res['reviews_text']) . "\n" . '</p></div>';
      }
      echo '</div>' . "\n";
   } 
}
?>
<!-- reviews_eof //-->

