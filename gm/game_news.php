<?php
/*
  $Id$
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
*/

	require('includes/application_top.php');
	
	require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LATEST_NEWS);
	
	$breadcrumb->add(NAVBAR_TITLE, tep_href_link('game_news.php'));
	
?>
<?php page_head();?>
<script type="text/javascript" src="js/emailProtector.js"></script>
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
<!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof //-->
<!-- body_text //-->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h2 class="pageHeading"><?php if (isset($HTTP_GET_VARS['news_id'])) { echo HEADING_TITLE; } else { echo HEADING_TITLE; } ?></h2>
<table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0">
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
		
		//echo '<li class="news_list">'.tep_date_short($latest_news['date_added']) .  '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' .$latest_news['url'].'" rel="nofollow" target="_blank">' .  mb_strimwidth(iconv("UTF-8", "EUC-JP",$latest_news['headline']),0,95,'...') . '' . $latest_news_image .'</a></li>'."\n";
		echo '<li class="news_list game_news"><a href="' .$latest_news['url'].'" rel="nofollow" target="_blank">' .  mb_strimwidth(iconv("UTF-8", "EUC-JP",$latest_news['headline']),0,130,'...') . '' . $latest_news_image .'</a></li>'."\n";
		
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
<!-- body_text_eof //-->
<!-- right_navigation //-->
<div id="r_menu">
	<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
</div>
<!-- right_navigation_eof //-->
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
