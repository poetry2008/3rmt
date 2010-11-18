<?php
/*
  $Id$
*/
?>
    <td valign="top" id="contents">
<?php 
  echo DEFAULT_PAGE_TOP_CONTENTS;
  
  echo '<div class="news_reviews05">';
  include(DIR_WS_MODULES . FILENAME_LATEST_NEWS);
  include(DIR_WS_BOXES . 'index_reviews.php'); 
  echo '</div>';
  echo '<div class="info_index05">';
  echo DEFAULT_PAGE_BOTTOM_CONTENTS;
  echo '</div>';
?>
<!--game news-->
<h3 class="pageHeading">
<span class="game_im"><img width="26" height="26" alt="4gamer" src="images/design/title_img09.gif"></span>
<span class="game_t">ONLINE GAME NEWS for</span>
<span class="game_im02"><img width="113" height="21" alt="" src="images/design/box_middle_listimg.gif"></span>
</h3>
<div class="comment">
<div id="game_news">
  <ul>
  <?php 
    $all_game_news = tep_get_rss(ALL_GAME_RSS);
    if ($all_game_news) {
      foreach($all_game_news as $key => $game_news){
        if($key == GAME_NEWS_MAX_DISPLAY)break;
    ?>
      <li>
        <a href="<?php echo $game_news['url'];?>" rel="nofollow" target="_blank"><?php echo mb_strimwidth($game_news['headline'],0,95,'...');?></a>  
        <span><?php echo tep_date_short($game_news['date_added']);?></span>
      </li> 
    <?php
      }
    }
  ?>
</ul>
</div>
  <div class="games_info_more">
    <a href="<?php echo tep_href_link('game_news.php');?>">more</a> 
  </div>
  </div>
       
      </td>
    <!-- body_text_eof //--> 
      <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="right_colum_border">
      <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--></td> 
