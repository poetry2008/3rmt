<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_ACTIONS.'index_top.php');

?>
<?php page_head();?>
</head>
<?php
if (isset($body_option)) {
?>
<body <?php echo $body_option;?>>
<?php
} else {
?>
<body>
<?php
}
?>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!--body -->
<div id="main">
<!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
</div>
<!-- left_navigation_eof //-->

      <!-- body_text //--> 
<?php
if ($category_depth == 'nested') {
  require(DIR_WS_ACTIONS.'index_nested.php');
} elseif ($_GET['tags_id']) { 
  require(DIR_WS_ACTIONS.'index_tags.php');
  ?>
<div id="cgi">
<div id="body_text">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
  <?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?>
</div>
</div>
<?php
} elseif ($category_depth == 'products' || $_GET['manufacturers_id']) {
  require(DIR_WS_ACTIONS.'index_products.php');
?> 
<div id="cgi">
<div id="body_text">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<?php
  #$current_category    = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id='".$current_category_id."'"));
  if (tep_show_warning($current_category_id)) {
    echo '<div class="waring_category">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
  }
?>
<h1 class="pageHeading">
<?php
  if($_GET['cPath']) {
    $categories_path = explode('_', $_GET['cPath']);
    $_categories = tep_get_category_by_id($categories_path[0], SITE_ID, $languages_id);
    echo $_categories['categories_name'];
  } else {
    echo 'RMT：ゲームマネー・アイテム・アカウント';
  }
?>      
</h1>
<p><?php echo $seo_category['categories_header_text'];?></p>
      <table border="0" width="95%" cellspacing="3" cellpadding="3"> 
        <tr> 
          <?php
        $categories_query = tep_db_query("
          select * 
          from (
            select c.categories_id, 
                   cd.categories_name, 
                   c.categories_image, 
                   c.parent_id,
                   cd.site_id,
                   c.sort_order
            from " .  TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
            where  c.categories_status != '1' 
              and c.parent_id = '" . $current_category_id . "' 
              and c.categories_id = cd.categories_id 
              and cd.language_id = '" . $languages_id . "'  
            order by cd.site_id DESC
          ) c
          where site_id = 0 
             or site_id = ".SITE_ID."
          group by categories_id
          order by sort_order, categories_name
        ");
    $rows = 0;
    while ($categories = tep_db_fetch_array($categories_query)) {
      $rows++;
      $cPath_new = tep_get_path($categories['categories_id']);
      $width = (int)(100 / MAX_DISPLAY_CATEGORIES_PER_ROW) . '%';
      echo '                <td class="smallText" style="width:'.$width.'" align="center"><h3 class="Tlist"><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">' . tep_image(DIR_WS_IMAGES . 'categories/' . $categories['categories_image'], $categories['categories_name'], SUBCATEGORY_IMAGE_WIDTH, SUBCATEGORY_IMAGE_HEIGHT) ;
                           if(tep_not_null($categories['categories_image'])) { echo '<br>' ; } 
                 echo $categories['categories_name'] . '</a></h3></td>' . "\n";
      if ((($rows / MAX_DISPLAY_CATEGORIES_PER_ROW) == floor($rows / MAX_DISPLAY_CATEGORIES_PER_ROW)) && ($rows != tep_db_num_rows($categories_query))) {
        echo '              </tr>' . "\n";
        echo '              <tr>' . "\n";
      }
  }
?> 
        </tr> 
      </table>
<h2 align="right">
<?php
  if (isset($cPath_array)) {
    echo $seo_category['categories_name']; 
  } elseif ($_GET['manufacturers_id']) {
    echo $seo_manufacturers['manufacturers_name'];
  } else {
    echo HEADING_TITLE ;
  }
?></h2>
<?php
   if (isset($_GET['cPath'])) {
     $listing_tmp_sql = $listing_sql;
     $list_tmp_query = tep_db_query($listing_tmp_sql);
     if (tep_db_num_rows($list_tmp_query)) {
       include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); 
     }
   } else {
     include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); 
   }
?>
<p><?php echo $seo_category['categories_footer_text'];?></p>
<?php
  if (isset($_GET['cPath'])) {
    $new_products_category_id = $current_category_id;
    include(DIR_WS_MODULES.'new_products3.php');
  }
?>
<?php 
if (isset($cPath_array)) {
  if ($seo_category['seo_description']) {
    echo '<p>'.$seo_category['seo_name'].'について</p>'; 
    echo '<p>'.$seo_category['seo_description'].'</p>'; 
  }
  if (!empty($seo_category['text_information'])) {
    echo $seo_category['text_information']; 
  }
}
?>
      <?php
      if (isset($cPath) && !ereg('_', $cPath)) { 
      $all_game_news = tep_get_categories_rss($current_category_id);
      if ($all_game_news) {
      ?>
<div style="margin-top: 10px;" class="background_news01 background_news02"> 
<table width="95%" class="news_title_03 news_title_04" style="border-top: 3px dotted rgb(68, 68, 68);">
<tr>
  <td>
    <h3 h3 style="border-bottom: medium none; font-size: 14px; color: rgb(255, 255, 255); padding-left: 10px; margin-top: 2px; font-weight: bold;"><?php echo $_categories['categories_name'];?> NEWS for 4Gamer.net </h3>
    </td>
</tr>
</table>
      <div class="game_news_index01 game_news_index02">
      <ul> 
      <?php
        foreach ($all_game_news as $cgmkey => $cgame_news_rss) {
          if ($cgmkey == CATEGORIES_GAME_NEWS_MAX_DISPLAY)  break;
          echo '<li class="news_list">';
          //echo '<span>'.tep_date_short($cgame_news_rss['date_added']).'</span>'; 
          echo '<a href="'.$cgame_news_rss['url'].'" class="latest_news_link01" rel="nofollow" target="_blank">'.mb_strimwidth($cgame_news_rss['headline'],0,95,'...').'</a>'; 
          echo '</li>'; 
        }
      ?>
      </ul> 
      </div>
</div>
      <?php
      }
      }
      ?>
</div>
</div>
<?php
} elseif($_GET['colors'] && !empty($_GET['colors'])) {
  require(DIR_WS_ACTIONS.'index_colors.php');
#} elseif($_GET['action'] && $_GET['action'] == 'select' && 0) { 
#   require(DIR_WS_ACTIONS.'index_select.php');
} else {
  require(DIR_WS_ACTIONS.'index_default.php');
}
?> 
<!-- footer //--> 
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
<!-- footer_eof //--> 
</div>
<!--body_EOF// -->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
