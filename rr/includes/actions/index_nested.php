<?php
/*
  $Id$
*/
  $category = tep_get_category_by_id($current_category_id, SITE_ID, $languages_id);
?>
      <td valign="top" id="contents">
      <!-- heading title --> 
<?php  
  if (isset($cPath_array)) {
      if ($category['categories_status'] != '0') {
        echo '<div class="waring_category">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
      }
      echo '<h1 class="pageHeading"><span>'.$seo_category['categories_name'].'</span></h1>';
    } elseif ($_GET['manufacturers_id']) {
      if ($category['categories_status'] != '0') {
        echo '<div class="waring_category">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
      }
       echo '<h1 class="pageHeading">'.$seo_manufacturers['manufacturers_name'].'</h1>';
      }
?> 
            <div class="comment">
               <font color="#FFFFFF"><?php echo $seo_category['categories_header_text']; //seoフレーズ ?></font>
        <table border="0" width="100%" cellspacing="3" cellpadding="3" summary="" class="product_list_page"> 
          <tr align="center">
<?php
    if (isset($cPath) && ereg('_', $cPath)) {
    // check to see if there are deeper categories within the current category
      $category_links = array_reverse($cPath_array);
      for($i=0, $n=sizeof($category_links); $i<$n; $i++) {
        //ccdd
        $categories_query = tep_db_query("
          select * 
          from (
            select c.categories_id, 
                   cd.categories_name, 
                   c.categories_image, 
                   c.parent_id,
                   cd.categories_status, 
                   cd.site_id,
                   c.sort_order
            from " .  TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
            where c.parent_id = '" . $category_links[$i] . "' 
              and c.categories_id = cd.categories_id 
              and cd.language_id = '" . $languages_id . "'  
            order by cd.site_id DESC
          ) c
          where site_id = 0 
             or site_id = ".SITE_ID."
          group by categories_id
          having c.categories_status != '1' and c.categories_status != '3'  
          order by sort_order, categories_name
        ");
        if (tep_db_num_rows($categories_query) < 1) {
          // do nothing, go through the loop
        } else {
          break; // we've found the deepest category the customer is in
        }
      }
    } else {
      //ccdd
        $categories_query = tep_db_query("
          select * 
          from (
            select c.categories_id, 
                   cd.categories_name, 
                   c.categories_image, 
                   c.parent_id,
                   cd.categories_status, 
                   cd.site_id,
                   c.sort_order
            from " .  TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
            where c.parent_id = '" . $current_category_id . "' 
              and c.categories_id = cd.categories_id 
              and cd.language_id = '" . $languages_id . "'  
            order by cd.site_id DESC
          ) c
          where site_id = 0 
             or site_id = ".SITE_ID."
          group by categories_id
          having c.categories_status != '1' and c.categories_status != '3'  
          order by sort_order, categories_name
        ");
    }

    $rows = 0;
    while ($categories = tep_db_fetch_array($categories_query)) {
    $rows++;
      $cPath_new = tep_get_path($categories['categories_id']);
      $width = (int)(100 / MAX_DISPLAY_CATEGORIES_PER_ROW) . '%';
      echo '<td class="smallText"><h2 class="Tlist"><a href="' .  tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">' . tep_image(DIR_WS_IMAGES . 'categories/' . $categories['categories_image'], $categories['categories_name'], 299, 48) ;
                             if(tep_not_null($categories['categories_image'])) { echo '<br>' ; } 
                   echo $categories['categories_name'] . '</a></h2></td>' . "\n";
      if ((($rows / 2) == floor($rows / 2)) && ($rows != tep_db_num_rows($categories_query))) {
        echo '        </tr>' . "\n";
        echo '        <tr align="center">' . "\n";
      }
  }
?> 
          </tr>
        </table>
      <font color="#FFFFFF"><?php echo $seo_category['categories_footer_text']; //seoフレーズ ?></font>
            </div>
<!--            <p class="pageBottom"></p>
-->      <?php 
      $new_products_category_id = $current_category_id; 
      $exone_single = false; 
      $exone_query = tep_db_query("select * from categories where categories_id = '".$current_category_id."' and parent_id = '0'"); 
      if (tep_db_num_rows($exone_query)) {
        $exone_single = true; 
      }
      if (!$exone_single) {
        include(DIR_WS_MODULES .'new_products5.php'); 
      } else {
        include(DIR_WS_MODULES .'new_products2.php'); 
      }
      ?>
<?php  
  if (isset($cPath_array)) {
    if ($seo_category['seo_description']) {
      echo '<h3 class="pageHeading"><span> ' . $seo_category['seo_name'] . 'について</span></h3>' . "\n";
      echo '<div class="comment"><div class="reviews_area"><p>' . $seo_category['seo_description'] . '</p></div></div>' . "\n"; //seoフレーズ
?>
        <p class="pageBottom"></p>
<?php
    }
?>
<?php  if (!empty($seo_category['text_information'])) {
    echo $seo_category['text_information'];
?>
        <!--<p class="pageBottom"></p>-->
<?php 
        }
?>
<?php
  }
?>
    </td> 
    <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="right_colum_border">
      <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //-->
    </td> 
