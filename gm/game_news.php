<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_NEWS);
  
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('game_news.php'));
  
?>
<?php page_head();?>
<script type="text/javascript"><!--
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
</head>
<body>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- body_text //-->
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<div id="main-content">
<table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <div id="contents">
<?php
                $all_game_news = tep_get_rss(ALL_GAME_RSS);
    if ((count($all_game_news)> 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
<?php
    }

    echo '<ul class="show_latest_news">'."\n";
    foreach ($all_game_news as $latest_news) {
      
      if(time()-strtotime($latest_news['date_added'])<(defined('DS_LATEST_NEWS_NEW_LIMIT')?DS_LATEST_NEWS_NEW_LIMIT:7)*86400){
        $latest_news_new = tep_image(DIR_WS_IMAGES . 'design/latest_news_new.gif', strip_tags($latest_news['headline']));
      } else {
        $latest_news_new = '';
      }
    
    echo '<li class="news_list game_news"><a href="' .$latest_news['url'].'" rel="nofollow" target="_blank">' .  mb_strimwidth($latest_news['headline'],0,130,'...') . '' . $latest_news_image .'</a></li>'."\n";
    
    }
    echo '</ul>';
  
    if ((count($all_game_news) > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
<?php
    }
?>
      </div>
    </td>
  </tr>
</table>
</div>
</div>
<?php include('includes/float-box.php');?>
<!-- footer_eof //-->
</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
