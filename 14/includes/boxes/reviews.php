<?php
/*
  $Id$
*/
if (
  basename($PHP_SELF) != FILENAME_CHECKOUT_OPTION
  && basename($PHP_SELF) != FILENAME_CHECKOUT_SHIPPING
  && basename($PHP_SELF) != FILENAME_CHECKOUT_PAYMENT
  && basename($PHP_SELF) != FILENAME_CHECKOUT_CONFIRMATION
  && basename($PHP_SELF) != FILENAME_CHECKOUT_SUCCESS
  && basename($PHP_SELF) != FILENAME_SHOPPING_CART
  && basename($PHP_SELF) != FILENAME_LOGIN
  && basename($PHP_SELF) != FILENAME_PREORDER 
  && basename($PHP_SELF) != FILENAME_PREORDER_PAYMENT 
  && basename($PHP_SELF) != FILENAME_PREORDER_CONFIRMATION
  && basename($PHP_SELF) != FILENAME_PREORDER_SUCCESS 
  && basename($PHP_SELF) != 'change_preorder.php' 
  && basename($PHP_SELF) != 'change_preorder_confirm.php' 
  && basename($PHP_SELF) != 'change_preorder_success.php' 
) {
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
      echo  '<div class="pageHeading_long"><span>'.$product_info['products_name'] .BOX_REVIEWS_LINK_TEXT.'</span></div>'."\n" . '<div class="comment_long">'."\n" ;
      while ($reviews = tep_db_fetch_array($reviews_query)) {
        $reviews_des_query = tep_db_query("select reviews_text from ".TABLE_REVIEWS_DESCRIPTION." where reviews_id = '".$reviews['reviews_id']."' and languages_id = '".$languages_id."'"); 
        $reviews_des_res = tep_db_fetch_array($reviews_des_query); 
        echo '<table width="100%" cellspacing="0" cellpadding="2" border="0" class="reviews_area">';
        echo '<tr><td width="60" align="center" style="padding-right:8px; padding-top:5px" rowspan="2" valign="top">';
        //获取商品图片
        $img_array =
          tep_products_images($product_info['products_id'],$product_info['site_id']);
        echo tep_image(DIR_WS_IMAGES . 'products/' .  $img_array[0], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"');
        echo '</td><td class="main" style="padding-left:5px;">';
        echo '<div class="text_main"> <span><b>' . sprintf(TEXT_REVIEW_BY, tep_output_string_protected($reviews['customers_name'])) .  '</b></span>&nbsp;&nbsp;<span>' . tep_image(DIR_WS_IMAGES . 'stars_' .  $reviews['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_FIVE_INFO_STARS, $reviews['reviews_rating'])) .  '</span>&nbsp;&nbsp;<span>[' .  sprintf(BOX_REVIEWS_TEXT_OF_FIVE_INFO_STARS, $reviews['reviews_rating']) .  ']</span></div>' . nl2br($reviews_des_res['reviews_text']) . "\n" . '</td></tr>';
        echo '</table>';
      }
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
  <a href="<?php echo tep_href_link(FILENAME_REVIEWS); ?>"><?php echo BOX_HEADING_REVIEWS;?>
  </a> 
  </div>
    <?php
  $random_select = "
  select *
  from (
    select r.reviews_id, 
           r.reviews_rating, 
           p.products_id, 
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
      echo '<p class="reviews_top"><a href="' .
        tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' .
            $random_reviews['products_id'] . '&reviews_id=' .
            $random_reviews['reviews_id']) . '" class="reviews_img">' .
        tep_image(DIR_WS_IMAGES . 'products/' . $img_array[0], $random_reviews['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) .  '</a><br>'. tep_image(DIR_WS_IMAGES . 'stars_' .  $random_reviews['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_FIVE_INFO_STARS, $random_reviews['reviews_rating']), 88, 16) . "\n".'</p> <p class="reviews_bottom"><a href="' .  tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' .  $random_reviews['products_id'] . '&reviews_id=' .  $random_reviews['reviews_id']) . '">' . tep_show_review_des($review) . ' ...</a></p>'; 
      echo '</div>';
    } 
    //获取商品图片
      $img_array =
        tep_products_images($random_product['products_id'],$random_product['site_id']);
      echo tep_image(DIR_WS_IMAGES .'products/'. $img_array[0], $random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>
      <a style="display:block;width:169px;word-wrap:break-word;overflow:hidden;" href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $random_product['products_id'] . '&reviews_id=' . $random_product['reviews_id']) . '">' . $review . ' ...</a><br>
      ' . tep_image(DIR_WS_IMAGES . 'stars_' . $random_product['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_FIVE_INFO_STARS, $random_product['reviews_rating'])) . "\n";
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

