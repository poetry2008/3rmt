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
?>
  <div class="reviews_box">
  <div class="menu_top">
  <a href="<?php echo tep_href_link(FILENAME_REVIEWS); ?>"><span>レビュー</span>
  <?php //echo tep_image(DIR_WS_IMAGES.'design/box/reviews.gif',BOX_HEADING_REVIEWS,171,44); ?></a>
  </div>
    <?php
// display random review box
    // ccdd
        $site_list_arr = array();

        $site_list_query = tep_db_query("select * from sites where id != '".SITE_ID."'"); 
        while ($site_list_res = tep_db_fetch_array($site_list_query)) {
          $site_list_arr[] = $site_list_res['id']; 
        }
        
        $site_ra_arr = array(); 
        $site_total = count($site_list_arr);
        for ($ra_num = 0; $ra_num < 3; $ra_num++) {
          $site_ra_num = tep_rand(0, $site_total-1); 
          $site_ra_arr[] = $site_list_arr[$site_ra_num]; 
        } 
        
        $ran_category_arr = array();
        for ($ran_num = 0; $ran_num < 3; $ran_num++) {
          while (true) {
          $random_break = false; 
          $random_category_query = tep_db_query("
              select *, RAND() as b 
              from (
                select c.categories_id, 
                       cd.categories_name, 
                       cd.categories_status, 
                       c.parent_id,
                       cd.romaji, 
                       cd.site_id,
                       cd.categories_image2,
                       c.sort_order
                from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                where c.parent_id = '0' 
                  and c.categories_id = cd.categories_id 
                  and cd.language_id='" . $languages_id ."' 
                order by site_id DESC
              ) c 
              where site_id = ".$site_ra_arr[$ran_num]."
                 or site_id = 0
              group by categories_id
              having c.categories_status != '1' and c.categories_status != '3'  
              order by b limit 1 
          ");
          $random_category_res = tep_db_fetch_array($random_category_query); 
          if ($random_category_res) {
            if (empty($ran_category_arr)) {
                $ran_category_arr[$ran_num][] = $random_category_res['categories_id']; 
                $ran_category_arr[$ran_num][] = $site_ra_arr[$ran_num]; 
                $ran_category_arr[$ran_num][] = $random_category_res['categories_name']; 
                $ran_category_arr[$ran_num][] = $random_category_res['romaji']; 
                $random_break = true; 
            } else {
            foreach($ran_category_arr as $rkey => $rvalue) {
              if (!(($random_category_res['categories_id'] == $rvalue[0]) && ($site_ra_arr[$ran_num] == $rvalue[1]))) {
                $ran_category_arr[$ran_num][] = $random_category_res['categories_id']; 
                $ran_category_arr[$ran_num][] = $site_ra_arr[$ran_num]; 
                $ran_category_arr[$ran_num][] = $random_category_res['categories_name']; 
                $ran_category_arr[$ran_num][] = $random_category_res['romaji']; 
                $random_break = true; 
                break; 
              }
            }
            }
          }
            if ($random_break) {
              break;            
            }
          }
        }
        echo '<div class="bestseller_text">';
	echo '<ul>';
    
             foreach ($ran_category_arr as $ran_key => $ran_value) {
	       echo '<li class="text_a">';
               echo '<div class="bestseller_text_01">'.$ran_value[2].'</div>';
               $url_str = ''; 
               switch ($ran_value[1]) {
                 case '5': 
                   $site_info_query = tep_db_query("select * from sites where id = '5'");
                   $site_info_res = tep_db_fetch_array($site_info_query);
                   $url_str = 'http://'.$ran_value[3].'.'.RANDOM_SUB_SITE; 
                   break; 
                 case '1': 
                 case '2': 
                 case '3': 
                   $site_info_query = tep_db_query("select * from sites where id = '".$ran_value[1]."'");
                   $site_info_res = tep_db_fetch_array($site_info_query);
                   $url_str = $site_info_res['url'].'/rmt/c-'.$ran_value[0].'.html'; 
                   break; 
                 default:
                   $site_info_query = tep_db_query("select * from sites where id = '".$ran_value[1]."'");
                   $site_info_res = tep_db_fetch_array($site_info_query); $url_str = $site_info_res['url'].'/'.$ran_value[3].'/'; 
                   break;
               }
               echo '<a href="'.$url_str.'">'.$url_str.'</a>'; 
               echo '</li>'; 
             }
	echo '</ul>';
	echo '</div>';
?>
    </div>
<!-- reviews_eof //-->
<?php
  }
}
?>

