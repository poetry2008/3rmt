<?php
/*
  $Id: latest_news.php,v 1.2 2002/11/11 06:38:08 will Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 Will Mays

  Released under the GNU General Public License
*/
?>
<!-- latest_news //-->
<p class="pageHeading">RMTジャックポットからのお知らせ</p>
<div id="news">
    <ul class="news_ul">

<?php
    $latest_news_query = tep_db_query('SELECT * from ' . TABLE_LATEST_NEWS . ' WHERE status = 1 ORDER BY isfirst DESC, date_added DESC LIMIT 5');
    if (!tep_db_num_rows($latest_news_query)) { // there is no news
      echo '<!-- ' . TEXT_NO_LATEST_NEWS . ' -->';
    } else {
      $info_box_contents = array();
      $info_box_contents[] = array('align' => 'left',
                                                'text'  => TABLE_HEADING_LATEST_NEWS);
      // new contentBoxHeading($info_box_contents);

    $info_box_contents = array();
    $row = 0;
    while ($latest_news = tep_db_fetch_array($latest_news_query)) {
      if($latest_news['news_image'] != '') { 
      $latest_news_image = '&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'infobox/photo.gif', $latest_news['headline'], '15', '15');
    } else {
      $latest_news_image = '';
    }
                if(time()-strtotime($latest_news['date_added'])<(defined('DS_LATEST_NEWS_NEW_LIMIT')?DS_LATEST_NEWS_NEW_LIMIT:7)*86400){
                    $latest_news_new = tep_image(DIR_WS_IMAGES . 'design/latest_news_new.gif', $latest_news['headline'], '28', '14');
                } else {
                    $latest_news_new = '';
                }
    /*
                $info_box_contents[$row] = array('align' => 'left',
                                                            'params' => 'class="smallText" valign="top"',
                                                            'text' =>
                                                            tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . FILENAME_LATEST_NEWS . '?news_id=' . $latest_news['news_id'] . '">' . $latest_news['headline'] . '&nbsp;&nbsp;' . $latest_news_image . '</a><br>');
    */
                echo '        <li class="news_list">
                ' . tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'news_id=' . $latest_news['news_id']) . '">' . $latest_news['headline'] . $latest_news_new .'</a>
            </li>'."\n";          
                $row++;
            }
            // new contentBox($info_box_contents);
        }
    ?>
        </ul>
    </div>
    <div align="right" style="padding: 5px 10px 0px 0px;">
        <a href='<?php echo tep_href_link('latest_news.php');?>'>more</a>
        <?php //<img src="includes/languages/japanese/images/buttons/button_more.gif" width="56" height="25" alt="more" title="more" >?>
    </div>
<!-- latest_news_eof //-->
