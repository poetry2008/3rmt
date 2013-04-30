<?php
/*
  $Id$
*/
?>
<!-- latest_news //-->
<script type="text/javascript">
function rowNewsEffect(object) {
 if (object.className == 'news_list') object.className = "outnews01";
}
function outNewsEffect(object) {
 if (object.className == 'outnews01') object.className = "news_list";
}
</script>
<div class="yui3-g main-columns">
<h3><span><?php echo STORE_NAME.TEXT_MODULE_NEWS_TITLE;?></span></h3>

<div class="yui3-u-1-3">
  <ul>
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
    $n = 1;
      while ($latest_news = tep_db_fetch_array($latest_news_query)) {
      if($latest_news['news_image'] != '') { 
      $latest_news_image = tep_image(DIR_WS_IMAGES . 'infobox/photo.gif', strip_tags(replace_store_name($latest_news['headline'])), '15', '15');
    } else {
      $latest_news_image = '';
    }
                if(time()-strtotime($latest_news['date_added'])<(defined('DS_LATEST_NEWS_NEW_LIMIT')?DS_LATEST_NEWS_NEW_LIMIT:7)*86400){
                    $latest_news_new = tep_image(DIR_WS_IMAGES . 'design/latest_news_new.gif', strip_tags(replace_store_name($latest_news['headline'])), '28', '14');
                } else {
                    $latest_news_new = '';
                }

$info_box_contents[$row] = array('align' => 'left',
'params' => 'class="smallText" valign="top"',
'text' =>
tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' .  FILENAME_NEWS . '?news_id=' . $latest_news['news_id'] . '">' .  replace_store_name($latest_news['headline']) . '&nbsp;&nbsp;' . $latest_news_image . '</a><br>');

   echo'  <li onmouseover="rowNewsEffect(this)" onmouseout="outNewsEffect(this)"> 
            <div class="news_date">'.tep_date_short($latest_news['date_added']).'</div>
            <div class="latest_news_link"><a  href="' .  tep_href_link(FILENAME_NEWS, 'news_id=' .  $latest_news['news_id']) . '">' .  replace_store_name($latest_news['headline']) . '' . $latest_news_image . $latest_news_new . '</a></div></li>
';

if($n % 2 ==0){
   echo '</ul></div><div class="yui3-u-1-3">
  <ul> 
';
}
$n++;
}
$row++;
}
//new contentBox($info_box_contents);
?> 
  </ul>  </div> <div id="hm-more">
  <img src="images/more_img.gif" alt="img"><a href="<?php echo tep_href_link(FILENAME_NEWS);?>"><?php echo TEXT_MODULE_NEWS_MORE;?></a>
</div>
 
</div>
<!-- latest_news_eof //--> 
