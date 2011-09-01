<?php
/*
  $Id$
*/
if (
  basename($PHP_SELF) != FILENAME_CHECKOUT_PRODUCTS
  && basename($PHP_SELF) != FILENAME_CHECKOUT_SHIPPING
  && basename($PHP_SELF) != FILENAME_CHECKOUT_PAYMENT
  && basename($PHP_SELF) != FILENAME_CHECKOUT_CONFIRMATION
  && basename($PHP_SELF) != FILENAME_CHECKOUT_SUCCESS
  && basename($PHP_SELF) != FILENAME_SHOPPING_CART
  && basename($PHP_SELF) != FILENAME_LOGIN
) {
?>
<!-- reviews //-->
<?php
  if(basename($PHP_SELF) == FILENAME_PRODUCT_INFO){
    // ccdd
    $reviews_query = tep_db_query("
        select rd.reviews_text, 
               r.reviews_rating, 
               r.reviews_id, 
               r.products_id, 
               r.customers_name, 
               r.date_added, 
               r.last_modified, 
               r.reviews_read 
        from " .  TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd 
        where r.reviews_id = rd.reviews_id 
          and r.products_id = '" .  (int)$_GET['products_id'] . "' 
          and r.reviews_status = '1' 
          and  r.products_id not in".tep_not_in_disabled_products()." 
          and r.site_id = ".SITE_ID
        );
    if(tep_db_num_rows($reviews_query)) {
      echo  '<div class="pageHeading_long">'.$product_info['products_name'] .'のレビュー</div>'."\n" . '<div class="comment_long">'."\n" ;
      while ($reviews = tep_db_fetch_array($reviews_query)) {
        echo '<div class="reviews_area"><p class="main">
<b>' . sprintf(TEXT_REVIEW_BY, tep_output_string_protected($reviews['customers_name'])) . '</b>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating'])) . '[' . sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating']) . '] <br>' . str_replace('<br />', '<br>',nl2br($reviews['reviews_text'])) . "\n" . '</p></div>';
//<div align="right"><i>' . sprintf(TEXT_REVIEW_DATE_ADDED, tep_date_long($reviews['date_added'])) . '</i></div></div>' . "\n";
      }
      //if(MAX_RANDOM_SELECT_REVIEWS > tep_db_num_rows($reviews_query)){
      //  echo '<div align="right"><a href="'tep_href_link(FILENAME_PRODUCT_REVIEWS,'products_id='.(int)$_GET['products_id']).'">レビュー一覧へ</a></div>' ;
      //}  
      echo '</div><div class="pageBottom_long"></div>' . "\n";
   } 
} else {
    if (isset($_GET['cPath']) && $cPath_array) {
      $subcid = tep_get_categories_id_by_parent_id($cPath_array[count($cPath_array) - 1]);
    }
?>
  <div class="reviews_box">
  <div class="menu_top_reviews">
  <a href="<?php echo tep_href_link(FILENAME_REVIEWS); ?>"><img src="images/menu_ico.gif" alt="" align="top">&nbsp;レビュー
  <?php //echo tep_image(DIR_WS_IMAGES.'design/box/reviews.gif',BOX_HEADING_REVIEWS,171,44); ?></a>
  </div>
    <div class="boxText" align="center">
    <?php
  $random_reviews_array = array(); 
  if (isset($_GET['products_id'])) {
    $random_reviews_raw = tep_db_query("select * from ".TABLE_REVIEWS." where site_id = '".SITE_ID."' and reviews_status = '1' and products_id = '".(int)$_GET['products_id']."'"); 
  } else {
    $random_reviews_raw = tep_db_query("select * from ".TABLE_REVIEWS." where site_id = '".SITE_ID."' and reviews_status = '1'"); 
  }
  $random_reviews_num = tep_db_num_rows($random_reviews_raw);
  $re_calc_num = 1;
  $re_max_num = 1;
  $re_show_num = ($random_reviews_num > 1)?1:$random_reviews_num; 
  if ($random_reviews_num > 0) {
    while (true) {
      $random_num = tep_rand(0, ($random_reviews_num-1)); 
      tep_db_data_seek($random_reviews_raw, $random_num); 
      $random_reviews = tep_db_fetch_array($random_reviews_raw); 
      $re_max_num++;
      if (isset($random_reviews_array[$random_reviews['reviews_id']])) {
        continue; 
      }
      $exists_reviews_raw = "select p.products_id, p.products_image, pd.products_name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd ";
      
      if (isset($subcid) && $subcid) {
          $exists_reviews_raw .= (", " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c");
      }
      
      $exists_reviews_raw .= " where p.products_id = pd.products_id and pd.language_id = '".$languages_id."'";
      if (isset($subcid) && $subcid) {
        $exists_reviews_raw .= "and p.products_id = p2c.products_id and p2c.categories_id in (".implode(',',$subcid).") ";
      }
      if (!isset($_GET['products_id'])) {
        $exists_reviews_raw .= " and p.products_id = '" .  $random_reviews['products_id'] . "'";
      }
      
      $exists_reviews_raw .= " and pd.products_status != 0 and pd.products_status != 3 order by pd.site_id DESC";
      $exists_reviews_query = tep_db_query($exists_reviews_raw); 
      if (tep_db_num_rows($exists_reviews_query)) {
        $exists_reviews_res = tep_db_fetch_array($exists_reviews_query); 
        $random_reviews_array[$random_reviews['reviews_id']]['products_id'] = $exists_reviews_res['products_id'];  
        $random_reviews_array[$random_reviews['reviews_id']]['products_image'] = $exists_reviews_res['products_image'];  
        $random_reviews_array[$random_reviews['reviews_id']]['products_name'] = $exists_reviews_res['products_name'];  
        $random_reviews_array[$random_reviews['reviews_id']]['reviews_rating'] = $random_reviews['reviews_rating'];  
        $re_calc_num++; 
      }
      if ($re_calc_num > $re_show_num) {
        break; 
      }
      if ($re_max_num > 3000) {
        break; 
      }
    }
    foreach ($random_reviews_array as $ran_key => $ran_value) {
// display random review box
    // ccdd
    $review_query = tep_db_query("
        select substring(reviews_text, 1, 60) as reviews_text 
        from " . TABLE_REVIEWS_DESCRIPTION . " 
        where reviews_id = '" . $ran_key . "' 
          and languages_id = '" . $languages_id . "'
    ");
    $review = tep_db_fetch_array($review_query);

    $review = htmlspecialchars($review['reviews_text']);
    $review = tep_break_string($review, 15, '-<br>');

    echo '<p class="reviews_top"><a href="' .  tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' .  $ran_value['products_id'] . '&reviews_id=' . $ran_key) . '" class="reviews_img">' . tep_image(DIR_WS_IMAGES . 'products/' .  $ran_value['products_image'], $ran_value['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br>'.  tep_image(DIR_WS_IMAGES . 'stars_' . $ran_value['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $ran_value['reviews_rating']), 88, 16) . "\n".'</p> <p class="reviews_bottom"><a href="' .  tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' .  $ran_value['products_id'] . '&reviews_id=' .  $ran_key) . '">' . tep_show_review_des($review) . ' ...</a></p>'; 
    }
    if (empty($random_reviews_array)) {
      echo BOX_REVIEWS_NO_REVIEWS;
    }
  } elseif (isset($_GET['products_id'])) {
// display 'write a review' box
    echo '<table border="0" cellspacing="2" cellpadding="2" width="100%">
      <tr><td class="boxText">
        <a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $_GET['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'box_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a>
      </td><td class="boxText">
        <a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $_GET['products_id']) . '">' . BOX_REVIEWS_WRITE_REVIEW .'</a>
      </td></tr>
    </table>' . "\n";
  } else {
// display 'no reviews' box
    echo BOX_REVIEWS_NO_REVIEWS;
  }
?>
          <div class="reviews_tom"><img height="14" width="170" alt="" src="images/design/box/box_bottom_bg_01.gif"></div>
</div>
    </div>
<!-- reviews_eof //-->
<?php
  }
}
?>

