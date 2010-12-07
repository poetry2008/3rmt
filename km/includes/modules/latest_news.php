<?php
/*
  $Id$
*/
?>
<!-- latest_news //-->
<div class="latest_news_box">
<div class="pageHeading">
<div class="pageHeading_left">
<img src="images/menu_ico.gif" alt="" align="top">&nbsp;RMTワールドマネーからのお知らせ
</div>
    <div class="pageHeading_right">
        <a href='<?php echo tep_href_link('latest_news.php');?>'>>>MORE</a>
        <?php //<img src="includes/languages/japanese/images/buttons/button_more.gif" width="56" height="25" alt="more" title="more" >?>
    </div>
</div>
<div class="comment">
    <div id="news">
        <ul class="news_ul">

<?php
//ccdd
    $latest_news_query = tep_db_query('
      SELECT * 
      from ' . TABLE_LATEST_NEWS . ' 
      WHERE status = 1 
        and (site_id = '.SITE_ID.' or site_id=0)
      ORDER BY isfirst DESC, date_added DESC LIMIT 5
    ');
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
      $latest_news_image = '&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'infobox/photo.gif', replace_store_name($latest_news['headline']), '28', '14');
    } else {
      $latest_news_image = '';
    }
                if(time()-strtotime($latest_news['date_added'])<(defined('DS_LATEST_NEWS_NEW_LIMIT')?DS_LATEST_NEWS_NEW_LIMIT:7)*86400){
                    $latest_news_new = tep_image(DIR_WS_IMAGES . 'design/latest_news_new.gif', strip_tags(replace_store_name($latest_news['headline'])), '28', '14');
                } else {
                    $latest_news_new = '';
                }
echo '        <li class="news_list">
' . tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'news_id=' . $latest_news['news_id']) . '">' . replace_store_name($latest_news['headline']) . $latest_news_new .'</a>
</li>'."\n";          
$row++;
}
}
    ?>
        </ul>
    </div>
</div>
<div class="pageBottom"></div>
</div>
<!-- latest_news_eof //-->
