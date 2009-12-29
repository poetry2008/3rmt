<?php
/*
  $Id: latest_news.php,v 1.2 2002/11/11 06:38:08 will Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 Will Mays

  Released under the GNU General Public License
  
  
*/

//require('/includes/application_top.php');
?>
<!-- latest_news //-->
<div id="n_border"> 
<div id="news">
<?php
  
  $latest_news_query = tep_db_query('SELECT * from ' . TABLE_LATEST_NEWS . ' WHERE status = 1 ORDER BY date_added DESC LIMIT ' . MAX_DISPLAY_LATEST_NEWS);

  if (!tep_db_num_rows($latest_news_query)) { // there is no news
    echo '<!-- ' . TEXT_NO_LATEST_NEWS . ' -->';
  } else {
    //$info_box_contents = array();
    //$info_box_contents[] = array('align' => 'left',
	echo '<h2 id="">'.ds_convert_Ajax(STORE_NAME).'新着情報</h2>';
	echo '<ul>'."\n";
//    new contentBoxHeading($info_box_contents);

    $info_box_contents = array();
    $row = 0;
    while ($latest_news = tep_db_fetch_array($latest_news_query)) {
      if($latest_news['news_image'] != '') { 
	  $latest_news_image = tep_image(DIR_WS_IMAGES . 'infobox/photo.gif', ds_convert_Ajax($latest_news['headline']), '15', '15');
	  } else {
	  $latest_news_image = '';
	  }
/*	  
	  $info_box_contents[$row] = array('align' => 'left',
                                       'params' => 'class="smallText" valign="top"',
									   'text' =>
                                       tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . FILENAME_LATEST_NEWS . '?news_id=' . $latest_news['news_id'] . '">' . $latest_news['headline'] . '&nbsp;&nbsp;' . $latest_news_image . '</a><br>');
*/      
	  echo '<li class="smallText">'.tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' .tep_href_link(FILENAME_LATEST_NEWS , 'news_id=' . $latest_news['news_id']) . '">' . ds_convert_Ajax($latest_news['headline']) . '&nbsp;&nbsp;' . $latest_news_image . '</a></li>'."\n";
	  $row++;
    }
	echo '</ul>'."\n";
	echo '<div align="right"><small><a href="'.tep_href_link(FILENAME_LATEST_NEWS, '',NONSSL).'">新着情報一覧<img src="images/design/right.gif" width="13" height="13" hspace="3" align="absmiddle" border="0"></a></small></div>';
    //new contentBox($info_box_contents);
  }
?>
 </div></div>

<!-- latest_news_eof //-->
