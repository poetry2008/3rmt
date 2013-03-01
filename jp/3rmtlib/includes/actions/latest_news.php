<?php
/*
  $Id$
*/
  check_uri('/^\/latest_news\.php/');
  check_uri('/page0/');
  check_uri('/page1\.html/');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LATEST_NEWS);
  
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_LATEST_NEWS));

if(preg_match('/^[0-9][0-9][0-9][0-9]\/[0-9][0-9]\/[0-9][0-9]$/',trim(SITE_OPEN_TIME))){
  $start_open_time = str_replace('/','-',trim(SITE_OPEN_TIME));
}else{
  $start_open_time = '';
}
if (isset($_GET['news_id'])) {
$check_array = array();
$check_news_id_query = tep_db_query("SELECT * FROM " . TABLE_LATEST_NEWS . 
    " WHERE status = 1 
    AND (site_id = '" . SITE_ID . "' or site_id =0 ) 
    AND date_added >= '".$start_open_time."'
    ORDER BY isfirst DESC, date_added DESC");
while($check_news_id_array = tep_db_fetch_array($check_news_id_query)){
$check_array[] = $check_news_id_array['news_id'];
}
//print_r($check_array);
if(!in_array($_GET['news_id'],$check_array)){
//判断该新闻是否显示
forward404Unless($latest_news);

}
    $latest_news_query = tep_db_query('
        SELECT * 
        from ' . TABLE_LATEST_NEWS . ' 
        WHERE news_id = ' . (int)$_GET['news_id'] . ' 
          and (site_id=' . SITE_ID . ' or site_id=0)');
    $latest_news = tep_db_fetch_array($latest_news_query);
    $breadcrumb->add(replace_store_name(strip_tags($latest_news['headline'])), tep_href_link(FILENAME_LATEST_NEWS, 'news_id='.$latest_news['news_id']));
    forward404Unless($latest_news);
  } else {
          $latest_news_query_raw = "
        SELECT * 
        FROM " . TABLE_LATEST_NEWS . " 
        WHERE status = 1 
          AND (site_id = '" . SITE_ID . "' or site_id =0 )
        AND date_added >= '".$start_open_time."'
        ORDER BY isfirst DESC, date_added DESC
      ";
    $latest_news_split = new splitPageResults($_GET['page'], MAX_DISPLAY_LATEST_NEWS, $latest_news_query_raw, $latest_news_numrows);
    $latest_news_query = tep_db_query($latest_news_query_raw);
    //是否有新闻 
    forward404Unless(tep_db_num_rows($latest_news_query));
  }
