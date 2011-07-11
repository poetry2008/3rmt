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
      echo  '<div class="pageHeading_long"><h3>'.$product_info['products_name'] .'のレビュー</h3></div>'."\n" . '<div class="comment_long"><div class="comment_long_text">'."\n" ;
      while ($reviews = tep_db_fetch_array($reviews_query)) {
        echo '<div class="reviews_area"><p class="main">
<b>' . sprintf(TEXT_REVIEW_BY, tep_output_string_protected($reviews['customers_name'])) . '</b>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif' , sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating'])) . '[' . sprintf(BOX_REVIEWS_TEXT_OF_5_STARS, $reviews['reviews_rating']) . ']
<br>' . str_replace('<br />', '<br>', nl2br($reviews['reviews_text'])) . "\n" . '</p></div>';
//<div align="right"><i>' . sprintf(TEXT_REVIEW_DATE_ADDED, tep_date_long($reviews['date_added'])) . '</i></div></div>' . "\n";
      }
      //if(MAX_RANDOM_SELECT_REVIEWS > tep_db_num_rows($reviews_query)){
      //  echo '<div align="right"><a href="'tep_href_link(FILENAME_PRODUCT_REVIEWS,'products_id='.(int)$_GET['products_id']).'">レビュー一覧へ</a></div>' ;
      //}  
      echo '</div></div>' . "\n";
   } 
} else {
    if (isset($_GET['cPath']) && $cPath_array) {
      $subcid = tep_get_categories_id_by_parent_id($cPath_array[count($cPath_array) - 1]);
    }
?>
  <div class="reviews_box">
  <div class="menu_top">
  <a href="<?php echo tep_href_link(FILENAME_REVIEWS); ?>"><span>レビュー</span>
  <?php //echo tep_image(DIR_WS_IMAGES.'design/box/reviews.gif',BOX_HEADING_REVIEWS,171,44); ?></a>
  </div>
    <?php
  $random_select = "";  
  $random_select .= "select distinct(r.reviews_id) as rid , r.products_id, r.site_id as rsid from ".TABLE_REVIEWS." r, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd 
    ";
  if (isset($subcid) && $subcid) {
      $random_select .= ", " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c";
  }
  
  $random_select .= " where r.reviews_status = '1' 
    and p.products_id = r.products_id 
    and pd.language_id = '".$languages_id."'
    and p.products_id = pd.products_id 
    and pd.products_status != '0' and pd.products_status != '3' ";
  
  if (isset($subcid) && $subcid) {
    $random_select .= "and p.products_id = p2c.products_id and p2c.categories_id in (".implode(',',$subcid).") ";
  }
  $info_box_contents = array();
  $random_products = tep_reviews_random_select($random_select, 3);
  
  if ($random_products) {
// display random review box
    // ccdd
	echo '<div class="bestseller_text">';
	echo '<ul>';
    for ($ri=0; $ri<sizeof($random_products); $ri++) { 
    
    $link_path_str = tep_get_product_path($random_products[$ri]['products_id']);
    $link_path_arr = explode('_', $link_path_str);
    $link_top_ca_query = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where (site_id = 0 or site_id = ".SITE_ID.") and categories_id = '".$link_path_arr[0]."' order by site_id desc limit 1");
    $link_top_ca_res = tep_db_fetch_array($link_top_ca_query);
    
    if ($link_top_ca_res) {
      $site_raw = tep_db_query("select * from sites where id = '".$random_products[$ri]['rsid']."'"); 
      $site_res = tep_db_fetch_array($site_raw); 
      
      if ($site_res) {
	    echo '<li class="text_a">';
        if ($site_res['id'] == SITE_ID) {
          echo '<div class="bestseller_text_01">'.$link_top_ca_res['categories_name'].'</div>';
		  echo '<a href="'.tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' .  $random_products[$ri]['products_id'] . '&reviews_id=' .  $random_products[$ri]['rid']).'">'.tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $random_products[$ri]['products_id'] . '&reviews_id=' .  $random_products[$ri]['rid']).'</a>'; 
        } else {
          echo '<div class="bestseller_text_01">'.$link_top_ca_res['categories_name'].'</div>';
		  echo '<a href="'.$site_res['url'].'/reviews/pr-'.$random_products[$ri]['products_id'].'/'.$random_products[$ri]['rid'].'.html">'.$site_res['url'].'/reviews/pr-'.$random_products[$ri]['products_id'].'/'.$random_products[$ri]['rid'].'.html</a>'; 
        }
		echo '</li>';
      }
    }
    } 
	echo '</ul>';
	echo '</div>';
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
     echo '</div></div>'; 
  } else {
// display 'no reviews' box
    echo '<div class="reviews_warp" align="center"> <div class="reviews_box_info"> ';
    echo BOX_REVIEWS_NO_REVIEWS;
     echo '</div></div>'; 
  }
?>
    </div>
<!-- reviews_eof //-->
<?php
  }
}
?>

