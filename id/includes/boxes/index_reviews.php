<?php
/*
  $Id$
*/
?>
<!-- reviews //-->
<?php
  if(basename($PHP_SELF) == FILENAME_PRODUCT_INFO){
    $reviews_query = tep_db_query("select rd.reviews_text, r.reviews_rating, r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where  r.reviews_id = rd.reviews_id and r.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and r.reviews_status = '1' and  r.products_id not in".tep_not_in_disabled_products()." limit 100");
    if(tep_db_num_rows($reviews_query)) {
      echo  '<div class="pageHeading_long02"><span class="game_im">
            <img width="26" height="26" src="images/design/title_img08.gif" alt=""/> 
          </span>
          <span class="game_t">この商品のレビュー</span></div>'."\n" . '<div class="comment_long">'."\n" ;
      while ($reviews = tep_db_fetch_array($reviews_query)) {
        echo '<div class="reviews_area"><p class="main">
<b>' . sprintf(TEXT_REVIEW_BY, tep_output_string_protected($reviews['customers_name'])) . '</b>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating'])) . '[' . sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating']) . ']
<br>' . nl2br($reviews['reviews_text']) . "\n" . '</p>
<div align="right"><i>' . sprintf(TEXT_REVIEW_DATE_ADDED, tep_date_long($reviews['date_added'])) . '</i></div></div>' . "\n";
      }
      //if(MAX_RANDOM_SELECT_REVIEWS > tep_db_num_rows($reviews_query)){
      //  echo '<div align="right"><a href="'tep_href_link(FILENAME_PRODUCT_REVIEWS,'products_id='.(int)$HTTP_GET_VARS['products_id']).'">レビュー一覧へ</a></div>' ;
      //}  
      echo '</div>' . "\n";
   } 
} else {
?>
<div class="index_reviews05">
  <a href="<?php echo tep_href_link(FILENAME_REVIEWS); ?>"><img width="171" alt="RMT 評判" src="images/design/title_img12.gif" ><?php //echo tep_image(DIR_WS_IMAGES.'design/box/reviews.gif',BOX_HEADING_REVIEWS,171,44); ?></a>
  <div class="boxText_reviews_05">
    <?php
  $random_select = "select r.reviews_id, r.reviews_rating, p.products_id, p.products_image, pd.products_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status != '0' and p.products_id = r.products_id and r.reviews_id = rd.reviews_id and rd.languages_id = '" . $languages_id . "' and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and r.reviews_status = '1' and r.site_id = '".SITE_ID."' group by r.reviews_id";
if (isset($HTTP_GET_VARS['products_id'])) {
    $random_select .= " and p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "'";
  }
  $random_select .= " order by r.reviews_id desc limit 100";
  $random_product = tep_random_select($random_select);

  $info_box_contents = array();

  if ($random_product) {
// display random review box
    $review_query = tep_db_query("select substring(reviews_text, 1, 170) as reviews_text from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $random_product['reviews_id'] . "' and languages_id = '" . $languages_id . "'");
    $review = tep_db_fetch_array($review_query);

    $review = htmlspecialchars($review['reviews_text']);
    //$review = tep_break_string($review, 15, '-<br>');

    echo '<p class="reviews_top"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $random_product['products_id'] . '&reviews_id=' . $random_product['reviews_id']) . '" class="reviews_img">' . tep_image(DIR_WS_IMAGES . $random_product['products_image'], $random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br>'. tep_image(DIR_WS_IMAGES . 'stars_' . $random_product['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $random_product['reviews_rating']), 88, 16) . "\n".'</p>
    <p class="reviews_bottom"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $random_product['products_id'] . '&reviews_id=' . $random_product['reviews_id']) . '">' . $review . ' ...</a></p>'; 
  } elseif (isset($HTTP_GET_VARS['products_id'])) {
// display 'write a review' box
    echo '<table border="0" cellspacing="2" cellpadding="2" width="100%"> <tr>';
    //echo' <td class="boxText"> <a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $HTTP_GET_VARS['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'box_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a> </td> '; 
      echo '<td class="boxText02">
        <a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $HTTP_GET_VARS['products_id']) . '">' . BOX_REVIEWS_WRITE_REVIEW .'</a>
      </td></tr>
    </table>' . "\n";
  } else {
// display 'no reviews' box
    echo BOX_REVIEWS_NO_REVIEWS;
  }
?>
    </div>
</div>
<?php
  }
?>
<!-- reviews_eof //-->
