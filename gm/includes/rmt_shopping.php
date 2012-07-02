<div class="yui3-g main-columns">
<div id="main-product-img"><img src="images/shop.png" alt="detail"></div>
<div class="hm-product-content no-title-class">
<?php 
if (isset($cPath_array)) {
  if ($seo_category['seo_description']) {
    echo '<div class="seo01"><div class="seo_title_04">'.str_replace('#STORE_NAME#', STORE_NAME, $seo_category['seo_name']).ABOUT_TEXT_LINK.'</div>'; echo '<p>'.str_replace('#STORE_NAME#', STORE_NAME, $seo_category['seo_description']).'</p>'; 
    echo '<div class="seo_news_index02"></div>';
    echo '</div>';
  }
  if (!empty($seo_category['text_information'])) {
    echo str_replace('#STORE_NAME#', STORE_NAME, $seo_category['text_information']); 
  }
}
?>
</div>
<div class="yui3-g main-columns">
<?php
if (isset($cPath) && !ereg('_', $cPath)) { 
  $all_game_news = tep_get_categories_rss($current_category_id);
  if ($all_game_news) {
    ?>
      <div style="margin-top: 10px;" class="background_news01 background_news02"> 
      <h3 ><span>ONLINE GAME NEWS for 4Gamer.net </span> </h3>
      <div class="yui3-u main-columns">
      <ul> 
      <?php
      foreach ($all_game_news as $cgmkey => $cgame_news_rss) {
        if ($cgmkey == CATEGORIES_GAME_NEWS_MAX_DISPLAY)  break;
        echo '<li class="news_list">';
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
