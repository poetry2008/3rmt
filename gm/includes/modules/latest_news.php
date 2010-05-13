<?php
/*
  $Id$
*/
?>
<!-- latest_news //-->
 <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0"> 
  <tr> 
     <td width="79"><img src="images/design/contents/title_news_left.jpg" width="79"
     height="23" title="<?php echo TABLE_HEADING_LATEST_NEWS;?>"></td> 
     <td background="images/design/contents/title_bg.jpg">&nbsp;</td> 
     <td width="47"><img src="images/design/contents/title_news_right.jpg"
     width="47" height="23" title="<?php echo TABLE_HEADING_LATEST_NEWS;?>"></td> 
   </tr> 
</table> 
<div id="news"> 
  <table width=""  border="0" cellpadding="2" cellspacing="0"> 
    <?php
//ccdd
    $latest_news_query = tep_db_query('
      SELECT * 
      from ' . TABLE_LATEST_NEWS . ' 
      WHERE status = 1 
        and site_id = '.SITE_ID.' 
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
      $latest_news_image = tep_image(DIR_WS_IMAGES . 'infobox/photo.gif', $latest_news['headline'], '15', '15');
    } else {
      $latest_news_image = '';
    }
                if(time()-strtotime($latest_news['date_added'])<(defined('DS_LATEST_NEWS_NEW_LIMIT')?DS_LATEST_NEWS_NEW_LIMIT:7)*86400){
                    $latest_news_new = tep_image(DIR_WS_IMAGES . 'design/latest_news_new.gif', strip_tags($latest_news['headline']), '28', '14');
                } else {
                    $latest_news_new = '';
                }

$info_box_contents[$row] = array('align' => 'left',
'params' => 'class="smallText" valign="top"',
'text' =>
tep_date_short($latest_news['date_added']) . '&nbsp;&nbsp;&nbsp;&nbsp;<a href="' . FILENAME_LATEST_NEWS . '?news_id=' . $latest_news['news_id'] . '">' . $latest_news['headline'] . '&nbsp;&nbsp;' . $latest_news_image . '</a><br>');

echo'   
          <tr> 
            <td class="news_list"><span class="smallText">'.tep_date_short($latest_news['date_added']).'</span><br> 
            <img src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" align="absmiddle"><a href="' . FILENAME_LATEST_NEWS . '?news_id=' . $latest_news['news_id'] . '">' . $latest_news['headline'] . '&nbsp;&nbsp;' . $latest_news_image . $latest_news_new . '</a></td> 
          </tr>
';      
$row++;
}
// new contentBox($info_box_contents);
}
?> 
  </table> 
</div> 
<!-- latest_news_eof //--> 
