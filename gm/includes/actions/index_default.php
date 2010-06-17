<?php
/*
  $Id$
*/
?>
 <div id="content">
   <!-- <h2 class="index_h2">はじめてRMTゲームマネーをご利用いただくお客様へ</h2>  -->
<?php 
  // @TODO 改成设置
  #$contents = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '11' and site_id = '" . SITE_ID . "'");//top
  #$result = tep_db_fetch_array($contents) ;
  include(DIR_WS_MODULES . FILENAME_LATEST_NEWS);
?>
  <div class="index_h2">&nbsp;</div> 
<?php
  echo DEFAULT_PAGE_TOP_CONTENTS;
?>

<?php /*
<div class="background_news01" style="margin-top:10px;">
<table width="95%" class="news_title_03">
<tr>
  <td>
    <h3 style="border-bottom:none; font-size:14px; color:#fff; padding-left:10px; margin-top:2px;font-weight:bold;">ONLINE GAME NEWS for 4Gamer.net</h3>
  </td>
  <td align="left" width="70">
  </td>
</table>

<div class="game_news_index01">
  <ul>
   <?php
   $all_game_news = tep_get_rss(ALL_GAME_RSS); 
  #print_r($all_game_news);
  #var_dump(defined('GAME_NEWS_MAX_DISPLAY'));
   if ($all_game_news) {
     foreach ($all_game_news as $game_key => $game_news) {
       if($game_key >= GAME_NEWS_MAX_DISPLAY) break; 
     ?>
      <li class="news_list">
        <a class="latest_news_link01" href="<?php echo $game_news['url'];?>" rel="nofollow" target="_blank"><?php echo mb_strimwidth($game_news['headline'],0,120,'...');?></a> 
      </li>
     <?php
     }
   }
   ?>
  </ul>
  <div class="link_more_news02">
  <a class="news_more01" href="<?php echo tep_href_link('game_news.php');?>">過去のオンラインゲームニュースを見る</a>
  </div>
</div>
</div>
*/ ?>
</div>   
<!-- body_text_eof //--> 
<!--column_right -->
<div id="r_menu">
<?php 
  $index_default = true;
  require(DIR_WS_INCLUDES . 'column_right.php'); 
?> 
</div>
<?php
