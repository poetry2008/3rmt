<?php
/*
  $Id$
*/
?>
<!-- latest_news -->
<div class="latest_news_box">
<h3 class="pageHeading"> <?php echo STORE_NAME.TEXT_MODULE_NEWS_TITLE;?> </h3>
<div id="news">
    <ul class="news_ul">

<?php
 
    if(preg_match('/^[0-9][0-9][0-9][0-9]\/[0-9][0-9]\/[0-9][0-9]$/',trim(SITE_OPEN_TIME))){
      $start_open_time = str_replace('/','-',trim(SITE_OPEN_TIME));
    }else{
      $start_open_time = '';
    }
    $latest_news_query = tep_db_query("
      SELECT * 
      from " . TABLE_NEWS . " 
      WHERE status = 1 
      AND (site_id = '".SITE_ID."' or site_id='0')
      AND date_added >= '".$start_open_time."'
      ORDER BY isfirst DESC, date_added DESC LIMIT 5");
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
      $latest_news_image = '&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'infobox/photo.gif', strip_tags(replace_store_name($latest_news['headline'])), '28', '14');
    } else {
      $latest_news_image = '';
    }
                if(time()-strtotime($latest_news['date_added'])<(defined('DS_LATEST_NEWS_NEW_LIMIT')?DS_LATEST_NEWS_NEW_LIMIT:7)*86400){
                    $latest_news_new = tep_image(DIR_WS_IMAGES . 'design/latest_news_new.gif', strip_tags(replace_store_name($latest_news['headline'])), '28', '14');
                } else {
                    $latest_news_new = '';
                }
                echo '        <li class="news_list">
                <img src="images/design/li_list.gif" alt="img">' . tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;<a href="' .  tep_href_link(FILENAME_NEWS, 'news_id=' . $latest_news['news_id']) . '">' .  replace_store_name($latest_news['headline']) . $latest_news_new .'</a>
            </li>'."\n";          
                $row++;
            }
        }
    ?>
        </ul>
        <div class="pageHeading_right">
        <a href='<?php echo tep_href_link('news.php');?>'><?php echo TEXT_MODULE_NEWS_MORE ;?></a>
    </div>
    </div>
</div>
<!-- latest_news_eof -->
