<?php
/*
  $Id$
*/
  check_uri('/^\/reviews\.php/');
  check_uri('/page0/');
  check_uri('/page1\.html/');
  
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_REVIEWS);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_REVIEWS));
  
  $reviews_array = array();  
  $reviews_query_raw = "
  select * 
  from (
    select r.reviews_id, 
           rd.reviews_text, 
           r.reviews_rating, 
           r.date_added, 
           p.products_id, 
           pd.products_name, 
           pd.products_status, 
           p.products_image, 
           r.customers_name,
           pd.site_id as psid
    from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
    where (pd.site_id        = '" . SITE_ID . "' or pd.site_id = 0)
      and p.products_id     = r.products_id 
      and r.reviews_id      = rd.reviews_id 
      and p.products_id     = pd.products_id 
      and pd.language_id    = '" . $languages_id . "' 
      and rd.languages_id   = '" . $languages_id . "' 
      and r.reviews_status  = '1' 
      and r.site_id         = ".SITE_ID." 
    ORDER by pd.site_id DESC
    ) p
    where psid = '0'
       or psid = '".SITE_ID."'
    group by reviews_id
    having p.products_status != '0' and p.products_status != '3'
    order by date_added DESC
  ";
  $reviews_split = new splitPageResults($_GET['page'], MAX_DISPLAY_NEW_REVIEWS, $reviews_query_raw, $reviews_numrows);
//ccdd
  $reviews_query = tep_db_query($reviews_query_raw);
  while ($reviews = tep_db_fetch_array($reviews_query)) {
    $reviews_array[] = array('id' => $reviews['reviews_id'],
                             'products_id'    => $reviews['products_id'],
                             'reviews_id'     => $reviews['reviews_id'],
                             'products_name'  => $reviews['products_name'],
                             'products_image' => $reviews['products_image'],
                             'authors_name'   => tep_output_string_protected($reviews['customers_name']),
                             'review'         => tep_output_string_protected(mb_substr($reviews['reviews_text'], 0, 250)) . '..',
                             'rating'         => $reviews['reviews_rating'],
                             'word_count'     => tep_word_count($reviews['reviews_text'], ' '),
                             'date_added'     => tep_date_long($reviews['date_added']));
  }
  forward404Unless($reviews_array);
  page_head();
