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
     echo  '<div class="sep">&nbsp;</div><div class="pageHeading_long">'.$product_info['products_name'] .'のレビュー</div>'."\n" . '<div id="contents">'."\n" ;
         while ($reviews = tep_db_fetch_array($reviews_query)) {
          $reviews_des_query = tep_db_query("select reviews_text from ".TABLE_REVIEWS_DESCRIPTION." where reviews_id = '".$reviews['reviews_id']."' and languages_id = '".$languages_id."'"); 
          $reviews_des_res = tep_db_fetch_array($reviews_des_query); 
           echo '<div class="main"><br><b>' .sprintf(TEXT_REVIEW_BY, tep_output_string_protected($reviews['customers_name'])) .  '</b>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating'])).'['.sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating']).']
                 <br>' . nl2br($reviews_des_res['reviews_text']). '</div>';
                 //<div align="right"><i>'. sprintf(TEXT_REVIEW_DATE_ADDED, tep_date_long($reviews['date_added'])) . '</i></div>
                 //</div>';
      } 
    echo '</div>' ;
   } 
}else{
    if (isset($_GET['cPath']) && $cPath_array) {
      $subcid = tep_get_categories_id_by_parent_id($cPath_array[count($cPath_array) - 1]);
    }
?>
<div class="box_des"><a href="<?php echo tep_href_link(FILENAME_REVIEWS);?>">REVIEW</a></div>
<?php
  $random_select = "
  select *
  from (
    select r.reviews_id, 
           r.reviews_rating, 
           p.products_id, 
           p.products_image, 
           pd.products_name,
           r.site_id as rsid,
           pd.products_status, 
           pd.site_id as psid
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
    order by reviews_id, psid DESC
  ) p
  where psid = '0'
     or psid = '".SITE_ID."'
  having p.products_status != '0' and p.products_status != '3' 
  group by reviews_id
  ";
  $random_select .= " order by reviews_id desc";
  $random_product = tep_random_select($random_select);

  $info_box_contents = array();

  if ($random_product) {
// display random review box
    // ccdd
    $review_query = tep_db_query("
        select substring(reviews_text, 1, 60) as reviews_text 
        from " . TABLE_REVIEWS_DESCRIPTION . " 
        where reviews_id = '" . $random_product['reviews_id'] . "' 
          and languages_id = '" . $languages_id . "'
    ");
    $review = tep_db_fetch_array($review_query);

    $review = htmlspecialchars($review['reviews_text']);
    $review = tep_break_string($review, 15, '-<br>');

    echo '<div align="center" style="width:90%; padding-left:5px;" class="smallText"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $random_product['products_id'] . '&reviews_id=' . $random_product['reviews_id']) . '">' . tep_image(DIR_WS_IMAGES . 'products/' . $random_product['products_image'], $random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $random_product['products_id'] . '&reviews_id=' . $random_product['reviews_id']) . '">' . $review . ' ..</a><br>' . tep_image(DIR_WS_IMAGES . 'stars_' . $random_product['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $random_product['reviews_rating'])) . '</div>';
  } elseif (isset($_GET['products_id'])) {
// display 'write a review' box
    echo '<table border="0" cellspacing="2" cellpadding="2" width="95%" align="center"><tr><td><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $_GET['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'box_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a></td><td class="boxText"><a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $_GET['products_id']) . '">' . BOX_REVIEWS_WRITE_REVIEW .'</a></td></tr></table>';
  } else {
// display 'no reviews' box
    echo BOX_REVIEWS_NO_REVIEWS;
  }
    
?>
<div class="sep">&nbsp;</div>
<!-- reviews_eof //-->
<?php
  }
}
?>
