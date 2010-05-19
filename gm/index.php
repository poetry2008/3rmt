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
  $current_category    = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id='".$current_category_id."'"));
  if ($current_category['categories_status'] == '2') {
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
<?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?>
      <?php
      if (isset($cPath) && !ereg('_', $cPath)) { 
      $all_game_news = tep_get_categories_rss($current_category_id);
      if ($all_game_news) {
      ?>
<table width="95%" style="border-top:#444 dotted 3px;">
<tr>
  <td>
    <h1 style="font-size:20px; color:#fff; border-left:#ccc solid 7px; padding-left:10px; margin-top:8px;"><?php echo $_categories['categories_name'];?> NEWS for 4Gamer.net 
    </td>
</tr>
</table>
      <div class="game_news_index01 list_rss01"> 
      <ul> 
      <?php
        foreach ($all_game_news as $cgmkey => $cgame_news_rss) {
          if ($cgmkey == CATEGORIES_GAME_NEWS_MAX_DISPLAY)  break;
          echo '<li class="news_list03">';
          //echo '<span>'.tep_date_short($cgame_news_rss['date_added']).'</span>'; 
          echo '<a href="'.$cgame_news_rss['url'].'" rel="nofollow" target="_blank">'.mb_strimwidth($cgame_news_rss['headline'],0,95,'...').'</a>'; 
          echo '</li>'; 
        }
      ?>
      </ul> 
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
