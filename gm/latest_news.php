<?php
/*
  $Id$
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
*/

	require('includes/application_top.php');
	
  //forward 404
if (isset($_GET['news_id'])) {
  $_404_query = tep_db_query("select * from " . TABLE_LATEST_NEWS. " where
      news_id = '" . intval($_GET['news_id']) . "' and site_id = '".SITE_ID."'");
  $_404 = tep_db_fetch_array($_404_query);

  forward404Unless($_404);
}
	
	require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LATEST_NEWS);
	
	$breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_LATEST_NEWS));
	
  if (!isset($_GET['news_id'])) $_GET['news_id']=NULL;
	$latest_news_query = tep_db_query('SELECT * from ' . TABLE_LATEST_NEWS . ' WHERE news_id = ' . (int)$_GET['news_id'] . ' and site_id=' . SITE_ID);
	$latest_news = tep_db_fetch_array($latest_news_query);
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
<h1 class="pageHeading"><?php if ($_GET['news_id']) { echo $latest_news['headline']; } else { echo HEADING_TITLE; } ?></h1>
<table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0">
	<tr>
		<td>
			<div id="contents">
<?php
	if ($_GET['news_id']) {  
		if($latest_news['news_image']) {
?>
				<table width="100%" border="0" cellpadding="4" cellspacing="1">
					<tr>
						<td class="infoBoxContents">
							<script type="text/javascript">
								<!--
									document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . tep_href_link(FILENAME_POPUP_IMAGE_NEWS, 'nID=' . $latest_news['news_id']) . '\\\')">' . tep_image(DIR_WS_IMAGES . $latest_news['news_image'], addslashes($latest_news['headline']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '</a>'; ?>');
								//-->
							</script>
							<noscript>
								<?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . $latest_news['news_image']) . '">' . tep_image(DIR_WS_IMAGES . $latest_news['news_image'], $latest_news['headline'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '</a>'; ?>
							</noscript>
							<br>
							<?php echo $latest_news['news_image_description']; ?>
						</td>
					</tr>
				</table>
<?php
		}
?>
				<p class="main"><?php echo nl2br($latest_news['content']); ?></p>
<?php
	} else {
		$latest_news_query_raw = 'SELECT * from ' . TABLE_LATEST_NEWS . ' WHERE status = 1 and site_id = ' . SITE_ID . ' ORDER BY isfirst DESC, date_added DESC';
		$latest_news_split = new splitPageResults($_GET['page'], MAX_DISPLAY_LATEST_NEWS, $latest_news_query_raw, $latest_news_numrows);
		$latest_news_query = tep_db_query($latest_news_query_raw);
	
		if (($latest_news_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
				<table border="0" width="100%" cellspacing="0" cellpadding="2">
					<tr>
						<td class="smallText"><?php echo $latest_news_split->display_count($latest_news_numrows, MAX_DISPLAY_LATEST_NEWS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS); ?></td>
                     </tr>   
                     <tr>
						<td class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $latest_news_split->display_links($latest_news_numrows, MAX_DISPLAY_LATEST_NEWS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
					</tr>
				</table>
<?php
		}

		echo '<ul>'."\n";
		while ($latest_news = tep_db_fetch_array($latest_news_query)) {
			if($latest_news['news_image'] != '') { 
				$latest_news_image = tep_image(DIR_WS_IMAGES . 'infobox/photo.gif', strip_tags($latest_news['headline']), '15', '15');
			} else {
				$latest_news_image = '';
			}
			
			if(time()-strtotime($latest_news['date_added'])<(defined('DS_LATEST_NEWS_NEW_LIMIT')?DS_LATEST_NEWS_NEW_LIMIT:7)*86400){
				$latest_news_new = tep_image(DIR_WS_IMAGES . 'design/latest_news_new.gif', strip_tags($latest_news['headline']));
			} else {
				$latest_news_new = '';
			}
		
		echo '<li class="news_list">'.tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' .tep_href_link(FILENAME_LATEST_NEWS ,'news_id=' . $latest_news['news_id']).'">' . $latest_news['headline'] . '&nbsp;&nbsp;' . $latest_news_image . $latest_news_new .'</a></li>'."\n";
		
		}
		echo '</ul>';
	
		if (($latest_news_numrows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
				<table border="0" width="100%" cellspacing="0" cellpadding="2">
					<tr>
						<td class="smallText"><?php echo $latest_news_split->display_count($latest_news_numrows, MAX_DISPLAY_LATEST_NEWS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS); ?></td>
                    </tr>
                    <tr>
						<td class="smallText"><?php echo TEXT_RESULT_PAGE; ?> <?php echo $latest_news_split->display_links($latest_news_numrows, MAX_DISPLAY_LATEST_NEWS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
					</tr>
				</table>
<?php
		}
	}
	if ($_GET['news_id']) { 
?>
                <p align="right" class="smallText">
					[ <?php echo tep_date_long($latest_news['date_added']); ?> ]
				</p>
                <div style="text-align:right; margin:5px 0;">
					<?php echo '<a href="' . tep_href_link(FILENAME_LATEST_NEWS) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
				</div>
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
