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
      echo  '<div class="pageHeading_long"><span>'.$product_info['products_name'] .'のレビュー</span></div>'."\n" . '<div class="comment_long">'."\n" ;
      while ($reviews = tep_db_fetch_array($reviews_query)) {
        echo '<div class="reviews_area"><p class="main">
<b>' . sprintf(TEXT_REVIEW_BY, tep_output_string_protected($reviews['customers_name'])) . '</b>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating'])) . '[' . sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating']) . ']
<br>' . str_replace('<br />', '<br>', nl2br($reviews['reviews_text'])) . "\n" . '</p></div>';
//<div align="right"><i>' . sprintf(TEXT_REVIEW_DATE_ADDED, tep_date_long($reviews['date_added'])) . '</i></div></div>' . "\n";
      }
      //if(MAX_RANDOM_SELECT_REVIEWS > tep_db_num_rows($reviews_query)){
      //  echo '<div align="right"><a href="'tep_href_link(FILENAME_PRODUCT_REVIEWS,'products_id='.(int)$_GET['products_id']).'">レビュー一覧へ</a></div>' ;
      //}  
      echo '</div>' . "\n";
   } 
} else {
    if (isset($_GET['cPath']) && $cPath_array) {
      $subcid = tep_get_categories_id_by_parent_id($cPath_array[count($cPath_array) - 1]);
    } else {
      $subcid = tep_other_get_categories_id_by_parent_id(FF_CID);
    }
?>
  <div class="reviews_box">
  <div class="menu_top">
  <a href="<?php echo tep_href_link(FILENAME_REVIEWS); ?>">レビュー
  </a> 
  </div>
    <?php
  $random_select = "
  select *
  from (
    select r.reviews_id, 
           r.reviews_rating, 
           p.products_id, 
           p.products_image, 
           pd.products_name,
           pd.products_status, 
           r.site_id as rsid,
           rd.reviews_text, 
           pd.site_id as psid,
           RAND() as c
    from " . TABLE_REVIEWS . " r, " .  TABLE_REVIEWS_DESCRIPTION . " rd, " . TABLE_PRODUCTS . " p, " .  TABLE_PRODUCTS_DESCRIPTION . " pd
    ";
    if (isset($subcid) && $subcid) {
        $random_select .= (", " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c");
    }
    $random_select .= "
    where p.products_id = r.products_id 
      and r.reviews_id = rd.reviews_id 
      and rd.languages_id = '" . $languages_id . "' 
      and p.products_id = pd.products_id 
      and pd.language_id = '" . $languages_id . "' 
      and r.reviews_status = '1' 
      and r.site_id = '".SITE_ID."'";
  if (isset($subcid) && $subcid) {
    $random_select .= "and p.products_id = p2c.products_id and p2c.categories_id in (".implode(',',$subcid).") ";
  }
  if (isset($_GET['products_id'])) {
    $random_select .= " and p.products_id = '" . (int)$_GET['products_id'] . "'";
  }
  $random_select .= "
  ) p
  where psid = '0'
     or psid = '".SITE_ID."'
  group by reviews_id
  having p.products_status != '0' and p.products_status != '3'
  ";
  $random_select .= " order by c desc limit 3";
  $info_box_contents = array();
  $random_reviews_query = tep_db_query($random_select);
  if (tep_db_num_rows($random_reviews_query)) { 
    while ($random_reviews = tep_db_fetch_array($random_reviews_query)) {
  // display random review box
      $review = htmlspecialchars(mb_substr($random_reviews['reviews_text'], 0, 60 , 'UTF-8'));
      $review = tep_break_string($review, 15, '-<br>');
      echo '<div class="reviews_warp" align="center">';
      echo '<p class="reviews_top"><a href="' .  tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' .  $random_reviews['products_id'] . '&reviews_id=' .  $random_reviews['reviews_id']) . '" class="reviews_img">' .  tep_image(DIR_WS_IMAGES . 'products/' . $random_reviews['products_image'], $random_reviews['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) .  '</a><br>'. tep_image(DIR_WS_IMAGES . 'stars_' .  $random_reviews['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $random_reviews['reviews_rating']), 88, 16) . "\n".'</p> <p class="reviews_bottom"><a href="' .  tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' .  $random_reviews['products_id'] . '&reviews_id=' .  $random_reviews['reviews_id']) . '">' . tep_show_review_des($review) . ' ...</a></p>'; 
      echo '</div>';
    } 
  } elseif (isset($_GET['products_id'])) {
// display 'write a review' box
    echo '<div class="reviews_warp" align="center">';
    echo '<table border="0" cellspacing="2" cellpadding="2" width="100%">
      <tr><td class="boxText">
        <a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $_GET['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'box_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a>
      </td><td class="boxText">
        <a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $_GET['products_id']) . '">' . BOX_REVIEWS_WRITE_REVIEW .'</a>
      </td></tr>
    </table>' . "\n";
    echo '</div>'; 
  } else {
// display 'no reviews' box
    echo '<div class="reviews_warp" align="center">';
    echo BOX_REVIEWS_NO_REVIEWS;
    echo '</div>'; 
  }
?>
    </div>
<!-- reviews_eof //-->
<?php
  }
}
?>

