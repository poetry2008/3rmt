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
        select r.reviews_rating, 
               r.reviews_id, 
               r.customers_name 
        from " .  TABLE_REVIEWS . " r 
        where r.products_id = '" .  (int)$_GET['products_id'] . "' 
          and r.reviews_status = '1' 
          and r.site_id = ".SITE_ID
        );
    if(tep_db_num_rows($reviews_query)) {
      echo  '<div class="pageHeading_long"><img align="top" src="images/menu_ico.gif" alt=""><h3>'.$product_info['products_name'] .'のレビュー</h3></div>'."\n" . '<div class="comment_long">'."\n" ;
      while ($reviews = tep_db_fetch_array($reviews_query)) {
        $reviews_des_query = tep_db_query("select reviews_text from ".TABLE_REVIEWS_DESCRIPTION." where reviews_id = '".$reviews['reviews_id']."' and languages_id = '".$languages_id."'"); 
        $reviews_des_res = tep_db_fetch_array($reviews_des_query); 
        echo '<div class="reviews_area"><p class="main">
<b>' . sprintf(TEXT_REVIEW_BY, tep_output_string_protected($reviews['customers_name'])) . '</b>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating'])) . '[' . sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating']) . ']
<br>' . str_replace('<br />', '<br>', nl2br($reviews_des_res['reviews_text'])) . "\n" . '</p></div>';
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
    }
?>
  <div class="reviews_box">
  <div class="menu_top">
  <a href="<?php echo tep_href_link(FILENAME_REVIEWS); ?>"><img src="images/menu_ico10.gif" alt="" align="top"><span>レビュー</span>
  <?php //echo tep_image(DIR_WS_IMAGES.'design/box/reviews.gif',BOX_HEADING_REVIEWS,171,44); ?></a>
  </div>
    <?php
  $random_reviews_array = array(); 

  $random_from_str = '';
  $random_where_str = '';
  
  if (isset($_GET['products_id'])) {
    $random_from_str = "select r.reviews_id, r.reviews_rating, r.products_id from ".TABLE_REVIEWS." r";
    $random_where_str = "r.site_id = '".SITE_ID."' and r.reviews_status = '1' and r.products_id = '".(int)$_GET['products_id']."' and r.products_status != '0' and r.products_status != '3'"; 
  } else {
    $random_from_str = "select r.reviews_id, r.reviews_rating, r.products_id from ".TABLE_REVIEWS." r";
    $random_where_str = "site_id = '".SITE_ID."' and reviews_status = '1' and products_status != '0' and products_status != '3'"; 
  }
  
  if (isset($subcid) && $subcid) {
    $random_from_str .= ", " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c";
    $random_where_str .= " and r.products_id = p2c.products_id and p2c.categories_id in (".implode(',', $subcid).")";   
  }
  
  $random_reviews_str = $random_from_str.' where '.$random_where_str;
  
  $random_reviews = tep_reviews_random_select($random_reviews_str, 3); 
  
  if (!empty($random_reviews)) {
    foreach ($random_reviews as $rr_key => $rr_value) {
      $link_pro_raw = tep_db_query("select p.products_image, pd.products_name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = pd.products_id and p.products_id = '".$rr_value['products_id']."' and pd.language_id = '".$languages_id."' and (pd.site_id = '0' or pd.site_id = '".SITE_ID."') order by pd.site_id DESC limit 1"); 
      $link_pro = tep_db_fetch_array($link_pro_raw); 
      
      if ($link_pro) {
        $random_reviews_array[$rr_value['reviews_id']]['products_id'] = $rr_value['products_id'];  
        $random_reviews_array[$rr_value['reviews_id']]['products_image'] = $link_pro['products_image'];  
        $random_reviews_array[$rr_value['reviews_id']]['products_name'] = $link_pro['products_name'];  
        $random_reviews_array[$rr_value['reviews_id']]['reviews_rating'] = $rr_value['reviews_rating'];  
      }
    }
// display random review box
    // ccdd
    foreach ($random_reviews_array as $ran_key => $ran_value) { 
    $review_query = tep_db_query("
        select substring(reviews_text, 1, 60) as reviews_text 
        from " . TABLE_REVIEWS_DESCRIPTION . " 
        where reviews_id = '" . $ran_key . "' 
          and languages_id = '" . $languages_id . "'
    ");
    $review = tep_db_fetch_array($review_query);

    $review = htmlspecialchars($review['reviews_text']);
    $review = tep_break_string($review, 15, '-<br>');

    echo '<div class="reviews_warp" align="center">';
      
    echo '<p class="reviews_top"><a href="' .  tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' .  $ran_value['products_id'] . '&reviews_id=' . $ran_key) . '" class="reviews_img">' . tep_image(DIR_WS_IMAGES . 'products/' .  $ran_value['products_image'], $ran_value['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br>'.  tep_image(DIR_WS_IMAGES . 'stars_' . $ran_value['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $ran_value['reviews_rating']), 88, 16) . "\n".'</p> <table border="0" cellspacing="0" cellpadding="0" class="reviews_bottom"><tr><td><a href="' .  tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' .  $ran_value['products_id'] . '&reviews_id=' .  $ran_key) . '">' . tep_show_review_des($review) . ' ...</a></td></tr></table>'; 
     echo '</div>'; 
    } 
    if (empty($random_reviews_array)) {
      echo '<div class="reviews_warp" align="center">';
      echo BOX_REVIEWS_NO_REVIEWS;
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

