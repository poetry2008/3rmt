<?php
/*
  $Id$
  ファイルコードを確認
*/
  $category = tep_get_category_by_id($current_category_id, SITE_ID, $languages_id);
?> 
<div class="yui3-u" id="layout">
<?php  
  if (isset($cPath_array)) {
       echo '<div id="current">'.$breadcrumb->trail(' <img src="images/point.gif"> ').'</div>';
       include('includes/search_include.php');
       //include('includes/search_include.php');
       echo "<div id='main-content'>";
      if ($category['categories_status'] != '0') {
        echo '<div class="waring_category">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
      }
       echo '<h1 class="pageHeading">'.$seo_category['categories_name'].'</h1>'; 
    } elseif ($_GET['manufacturers_id']) {
       echo '<div id="current">'.$breadcrumb->trail(' <img src="images/point.gif"> ').'</div>';
       include('includes/search_include.php');
      if ($category['categories_status'] != '0') {
        echo '<div class="waring_category">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
      }
      echo '<h1 class="pageHeading">'.$seo_manufacturers['manufacturers_name'].'</h1>';
    }
?> 
      <!-- heading title eof-->
    <div class="list_spacing"><?php echo str_replace('#STORE_NAME#', STORE_NAME,
        $seo_category['categories_header_text']);?></div>
      <div id="product_list"> 
        <ul> 
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
                   cd.site_id,
                   cd.categories_status, 
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
          order by sort_order, categories_name
        ");
    }

    $rows = 0;
    while ($categories = tep_db_fetch_array($categories_query)) {
      if($rows%MAX_DISPLAY_CATEGORIES_PER_ROW
          ==0&&$rows!=0&&MAX_DISPLAY_CATEGORIES_PER_ROW!=0){
        echo '</ul><ul>';
      }
      $rows++;
      $cPath_new = tep_get_path($categories['categories_id']);
      $width = (int)(100 / MAX_DISPLAY_CATEGORIES_PER_ROW) . '%';
      if(MAX_DISPLAY_CATEGORIES_PER_ROW!=0){
        $c_li_list_style = 'style="width:'.$width.'"';
      }else{
        $c_li_list_style = 'style="padding-left:11px;padding-right:11px"';
      }
      echo '<li '.$c_li_list_style.' ><h3 class="Tlist"><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">' . tep_image(DIR_WS_IMAGES . 'categories/' . $categories['categories_image'], $categories['categories_name'], SUBCATEGORY_IMAGE_WIDTH, SUBCATEGORY_IMAGE_HEIGHT) ;
                           if(tep_not_null($categories['categories_image'])) { echo '<br>' ; } 
                 echo $categories['categories_name'] . '</a></h3></li>' . "\n";
      if ((($rows / MAX_DISPLAY_CATEGORIES_PER_ROW) == floor($rows / MAX_DISPLAY_CATEGORIES_PER_ROW)) && ($rows != tep_db_num_rows($categories_query))) {
      }
  }
?> 
        </ul> 
      </div>
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
      ?>
      <?php
      if (isset($cPath) && !ereg('_', $cPath)) { 
      $all_game_news = tep_get_categories_rss($current_category_id);
      if ($all_game_news) {
      ?>
<div class="background_news01" style="margin-top:10px;">
  <table width="100%" style="border-top:#444 dashed 1px;" class="news_title_03">
    <tr>
  <td align="left" width="70">
       <?php echo str_replace('#STORE_NAME#', STORE_NAME, $seo_category['categories_footer_text']);?>

    
    </td>
  </tr>
  </table>
 </div>
      <?php
      #echo '</div>'; 
      } 
?>


  </div>
<!--</div>-->
<?php } ?>
</div>
<?php include('includes/float-box.php');?>
</div>
<?php include('includes/rmt_shopping.php');?>
