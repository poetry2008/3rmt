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
<div class="background_news01">
<table class="news_title_01">
  <tr>
    <td><div style="border-bottom:none; font-size:14px; color:#fff; padding-left:10px; margin-top:2px;
    font-weight:bold;">RMTゲームマネーからのお知らせ</div></td> 
    <td align="left" width="70"></td>
  </tr>
</table>
<div id="news" class="news_title_02"> 
  <ul> 
    <?php
    $latest_news_query = tep_db_query("
      SELECT * 
      from " . TABLE_LATEST_NEWS . " 
      WHERE status = 1 
        AND (site_id = '".SITE_ID."' or site_id='0')
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
      $latest_news_image = tep_image(DIR_WS_IMAGES . 'infobox/photo.gif', replace_store_name($latest_news['headline']), '15', '15');
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
tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . FILENAME_LATEST_NEWS . '?news_id=' . $latest_news['news_id'] . '">' . replace_store_name($latest_news['headline']) . '&nbsp;&nbsp;' . $latest_news_image . '</a><br>');
echo'   
          <li class="news_list" onmouseover="rowNewsEffect(this)" onmouseout="outNewsEffect(this)"> 
            <span class="news_date01">'.tep_date_short($latest_news['date_added']).'</span> 
            <a class="latest_news_link" href="' . tep_href_link(FILENAME_LATEST_NEWS, 'news_id=' . $latest_news['news_id']) . '">' . replace_store_name($latest_news['headline']) . '&nbsp;&nbsp;' . $latest_news_image . $latest_news_new . '</a> 
          </li>
';      
}
$row++;
}
//new contentBox($info_box_contents);
?> 
  </ul> 
<div class="link_more_news01">
<a class="news_more01" href="<?php echo tep_href_link(FILENAME_LATEST_NEWS);?>">過去のRMTニュースを見る</a>
</div>
</div> 
</div>
<!-- latest_news_eof //--> 