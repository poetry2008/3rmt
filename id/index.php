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
<div align="center">
  <!-- header //-->
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" summary="container" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <!-- body_text //-->
<?php
if ($category_depth == 'nested') {
  require(DIR_WS_ACTIONS.'index_nested.php');
} elseif ($_GET['tags_id']) {
  require(DIR_WS_ACTIONS.'index_tags.php');
?>
  <td valign="top" id="contents_long">
        <h1 class="pageHeading_long">
        <?php
        $sel_tags_query = tep_db_query("select * from ".TABLE_TAGS." where tags_id =
            '".(int)$_GET['tags_id']."'"); 
        $sel_tags_res = tep_db_fetch_array($sel_tags_query); 
        if ($sel_tags_res) {
          echo $sel_tags_res['tags_name']; 
        }
        ?>
        </h1> 
        <div class="comment_long">
        <?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?></div>
        </td>
<?php
} elseif ($category_depth == 'products' || $_GET['manufacturers_id']) {

  require(DIR_WS_ACTIONS.'index_products.php');
?> 
  <td valign="top" id="contents_long">
<?php
  #$current_category    = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id='".$current_category_id."'"));
  if (tep_show_warning($current_category_id)) {
    echo '<div class="waring_category">'.WARN_PRODUCT_STATUS_TEXT.'</div>'; 
  }
?>
    <h1 class="pageHeading_long"><?php
  if (isset($cPath_array)) {
    echo $seo_category['categories_name'];
  } elseif ($_GET['manufacturers_id']) {
    echo $seo_manufacturers['manufacturers_name'];
  } else {
    echo HEADING_TITLE;
  }
?></h1>
    <div class="comment_long"><?php echo $seo_category['categories_header_text']; //seoフレーズ ?>
    <h2 class="line"><?php
  if($_GET['cPath']) {
    $categories_path = explode('_', $_GET['cPath']);
    $_categories = tep_get_category_by_id($categories_path[0], SITE_ID, $languages_id);
    echo $_categories['categories_name'];
  } else {
    echo 'RMT：ゲーム通貨・アイテム・アカウント';
  }
 
?></h2>
      <?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?></div>
      <?php
        if (isset($cPath) && !ereg('_', $cPath)) {
          //$cgame_news_query = tep_db_query("select ns.headline, ns.date_added, ns.url from ". TABLE_CATEGORIES_ALIAS." a, ".TABLE_CATEGORIES_NEWS." ns where a.categories_id = '".$current_category_id."' and a.alias = ns.categories_name"); 
          //if (tep_db_num_rows($cgame_news_query)) { 
    $all_game_news = tep_get_categories_rss($current_category_id);
    if ($all_game_news) {
        
            ?>
          <h1 class="pageHeading">
          <span class="game_im"><img width="26" height="26" src="images/design/title_img08.gif"></span> 
          <span class="game_t"><?php echo $_categories['categories_name'];?> NEWS for</span> 
          <span class="game_im02"><img width="113" height="21" alt="" src="images/design/box_middle_listimg.gif"></span>
          </h1>
          <div class="comment"> 
            <div id="game_news">
              <ul>
            <?php 
            //while ($cgame_news_res = tep_db_fetch_array($cgame_news_query)) {
            foreach($all_game_news as $key => $cgame_news_res){
              if($key == CATEGORIES_GAME_NEWS_MAX_DISPLAY)break;
              echo '<li><a href="'.$cgame_news_res['url'].'" rel="nofollow" target="_blank">'.mb_strimwidth($cgame_news_res['headline'],0,95,'...').'</a>'; 
              echo '<span>'.tep_date_short($cgame_news_res['date_added'])."</span></li>";
            } 
          ?>
              </ul> 
            </div> 
          </div> 
          <?php
          }
        } 
      ?>
      </td> 
      <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="right_colum_border">
      <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--></td> 
<?php
} elseif($_GET['colors'] && !empty($_GET['colors'])) {
  require(DIR_WS_ACTIONS.'index_colors.php');
} elseif($_GET['action'] && $_GET['action'] == 'select') { 
  require(DIR_WS_ACTIONS.'index_select.php');
} else { 
  require(DIR_WS_ACTIONS.'index_default.php');
}
?> 
    </tr> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
