<?php
/*
  $Id$
*/
  check_uri('/^\/latest_news\.php/');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LATEST_NEWS);
  
  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_LATEST_NEWS));

  if (isset($_GET['news_id'])) {
    //ccdd
    $latest_news_query = tep_db_query('
        SELECT * 
        from ' . TABLE_LATEST_NEWS . ' 
        WHERE news_id = ' . (int)$_GET['news_id'] . ' 
          and (site_id=' . SITE_ID . ' or site_id=0)');
    $latest_news = tep_db_fetch_array($latest_news_query);
    $breadcrumb->add(replace_store_name(strip_tags($latest_news['headline'])), tep_href_link(FILENAME_LATEST_NEWS, 'news_id='.$latest_news['news_id']));
    forward404Unless($latest_news);
  } else {
    $latest_news_query_raw = '
      SELECT * 
      FROM ' . TABLE_LATEST_NEWS . ' 
      WHERE status = 1 
        AND (site_id = ' . SITE_ID . ' or site_id =0 )
      ORDER BY isfirst DESC, date_added DESC
    ';
    $latest_news_split = new splitPageResults($_GET['page'], MAX_DISPLAY_LATEST_NEWS, $latest_news_query_raw, $latest_news_numrows);
    $latest_news_query = tep_db_query($latest_news_query_raw);
    forward404Unless(tep_db_num_rows($latest_news_query));
  }