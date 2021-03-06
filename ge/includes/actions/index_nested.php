<?php
/*
  $Id$
*/
  $category = tep_get_category_by_id($current_category_id, SITE_ID, $languages_id);
?> 
<div id="content">
<?php  
  if( isset($cPath_array)) {
       echo '<div class="headerNavigation">'.$breadcrumb->trail(' &raquo; ').'</div>'; 
      if ($category['categories_status'] != '0') {
        echo '<div class="waring_category">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
      }
       echo '<h1 class="pageHeading">'.$seo_category['categories_name'].'</h1>'; 
  } elseif ($_GET['manufacturers_id']) {
       echo '<div class="headerNavigation">'.$breadcrumb->trail(' &raquo; ').'</div>';
      if ($category['categories_status'] != '0') {
        echo '<div class="waring_category">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
      }
       echo '<h1 class="pageHeading">'.$seo_manufacturers['manufacturers_name'].'</h1>';
  }
?> 
      <!-- heading title eof-->
    <p><?php echo str_replace('#STORE_NAME#', STORE_NAME, $seo_category['categories_header_text']); //seo phrase?></p>
      <table border="0" width="95%" cellspacing="3" cellpadding="3"> 
        <tr> 
<?php
    if (isset($cPath) && ereg('_', $cPath)) {
// check to see if there are deeper categories within the current category
      $category_links = array_reverse($cPath_array);
      for($i=0, $n=sizeof($category_links); $i<$n; $i++) {
        
        $categories_query = tep_db_query("
          select * 
          from (
            select c.categories_id, 
                   cd.categories_name, 
                   cd.categories_name_list, 
                   cd.categories_status, 
                   cd.categories_image, 
                   c.parent_id,
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
          order by sort_order, categories_name_list
        ");
        if (tep_db_num_rows($categories_query) < 1) {
          // do nothing, go through the loop
        } else {
          break; // we've found the deepest category the customer is in
        }
      }
    } else {
      
        $categories_query = tep_db_query("
          select * 
          from (
            select c.categories_id, 
                   cd.categories_name, 
                   cd.categories_name_list, 
                   cd.categories_image, 
                   c.parent_id,
                   cd.site_id,
                   cd.categories_status,
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
          order by sort_order, categories_name_list
        ");
    }

    $rows = 0;
    while ($categories = tep_db_fetch_array($categories_query)) {
    $rows++;
      $cPath_new = tep_get_path($categories['categories_id']);
      $width = (int)(100 / MAX_DISPLAY_CATEGORIES_PER_ROW) . '%';
      echo '<td class="smallText" style="width:'.$width.'" align="center"><h3
        class="Tlist"><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) .
        '">' . tep_image(DIR_WS_IMAGES . 'categories/' .
        $categories['categories_image'], $categories['categories_name_list'], SUBCATEGORY_IMAGE_WIDTH, SUBCATEGORY_IMAGE_HEIGHT) ;
                             if(tep_not_null($categories['categories_image'])) { echo '<br>' ; } 
                   echo $categories['categories_name_list'] . '</a></h3></td>' . "\n";
      if ((($rows / MAX_DISPLAY_CATEGORIES_PER_ROW) == floor($rows / MAX_DISPLAY_CATEGORIES_PER_ROW)) && ($rows != tep_db_num_rows($categories_query))) {
        echo '        </tr>' . "\n";
        echo '        <tr>' . "\n";
      }
  }
?> 
        </tr>
      </table>
    <?php
    if (!empty($seo_category['categories_footer_text'])) { 
    ?>
    <div id="information">
    <p><?php echo str_replace('#STORE_NAME#', STORE_NAME, $seo_category['categories_footer_text']); //seo phrase ?></p>
    </div>
    <?php
    } 
    ?>
      <?php 
  $new_products_category_id = $current_category_id;
  $exone_single = false; 
  $exone_query = tep_db_query("select * from categories where categories_id = '".$current_category_id."' and parent_id = '0'"); 
  if (tep_db_num_rows($exone_query)) {
    $exone_single = true; 
  }
  if (!$exone_single) {
    include(DIR_WS_MODULES .'new_products5.php'); 
  } else {
    include(DIR_WS_MODULES .'new_products.php'); 
  }
  if (!empty($seo_category['text_information'])) {
        $old_info_arr = array('#STORE_NAME#'); 
        $new_info_arr = array(STORE_NAME); 
        //分类描述内容
        $seo_category_array = explode('||||||',str_replace($old_info_arr, $new_info_arr, $seo_category['text_information'])); 
        foreach($seo_category_array as $seo_value){

          echo $seo_value;
        }
  } 
?>
      <?php
      if (isset($cPath) && !ereg('_', $cPath)) { 
      $all_game_news = tep_get_categories_rss($current_category_id);
      if ($all_game_news) {
      ?>
<div class="background_news01">
  <table width="95%" class="news_title_03">
  <tr>
    <td>
      <h3 style="border-bottom:none; font-size:14px; color:#fff; padding-left:10px; margin-top:2px;font-weight:bold;">ONLINE GAME NEWS for 4Gamer.net</h3>
    </td>
    <td align="left" width="70">
    </td>
  </tr>
  </table>
  <div class="game_news_index01">
    <ul> 
      <?php
        foreach ($all_game_news as $cgmkey => $cgame_news_rss) {
          if ($cgmkey == CATEGORIES_GAME_NEWS_MAX_DISPLAY)  break;
          echo '<li class="news_list">';
          echo '<a  class="latest_news_link01" href="'.$cgame_news_rss['url'].'" rel="nofollow" target="_blank">'.mb_strimwidth($cgame_news_rss['headline'],0,95,'...').'</a>'; 
          echo '</li>'; 
        }
      ?>
    </ul> 
  </div>
</div>
      <?php
      } 
?>
<?php } ?>
</div>
<div id="r_menu">
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
