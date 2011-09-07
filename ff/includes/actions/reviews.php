<?php
/*
  $Id$
*/
  check_uri('/^\/reviews\.php/');
  check_uri('/page0/');
  check_uri('/page1\.html/');
  
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_REVIEWS);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_REVIEWS));
  $sub_caid = tep_other_get_categories_id_by_parent_id(FF_CID); 
  if (empty($sub_caid)) {
    $sub_caid = array(FF_CID); 
  } else {
    array_push($sub_caid, FF_CID); 
  }
  $reviews_array = array();  
  $reviews_query_raw = "select r.reviews_id, r.reviews_rating, r.date_added, r.products_id, r.customers_name from ".TABLE_REVIEWS." r, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c where r.products_id = p2c.products_id and p2c.categories_id in (".implode(',', $sub_caid).") and r.reviews_status = '1' and r.site_id = '".SITE_ID."' and r.products_status != '0' and r.products_status != '3' order by r.date_added DESC"; 
  $reviews_split = new splitPageResults($_GET['page'], MAX_DISPLAY_NEW_REVIEWS, $reviews_query_raw, $reviews_numrows);
//ccdd
  $reviews_query = tep_db_query($reviews_query_raw);
  while ($reviews = tep_db_fetch_array($reviews_query)) {
    $product_info_raw = tep_db_query("select p.products_image, pd.products_name from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = pd.products_id and p.products_id = '".$reviews['products_id']."' and (pd.site_id = '0' or pd.site_id = '".SITE_ID."') order by pd.site_id DESC limit 1"); 
    $product_info = tep_db_fetch_array($product_info_raw); 
    
    $reviews_des_raw = tep_db_query("select reviews_text from ".TABLE_REVIEWS_DESCRIPTION." where reviews_id = '".$reviews['reviews_id']."' and languages_id = '".$languages_id."'"); 
    $reviews_des = tep_db_fetch_array($reviews_des_raw); 
    $reviews_array[] = array('id' => $reviews['reviews_id'],
                             'products_id'    => $reviews['products_id'],
                             'reviews_id'     => $reviews['reviews_id'],
                             'products_name'  => $product_info['products_name'],
                             'products_image' => $product_info['products_image'],
                             'authors_name'   => tep_output_string_protected($reviews['customers_name']),
                             'review'         => tep_output_string_protected(mb_substr($reviews_des['reviews_text'], 0, 250)) . '..',
                             'rating'         => $reviews['reviews_rating'],
                             'word_count'     => tep_word_count($reviews_des['reviews_text'], ' '),
                             'date_added'     => tep_date_long($reviews['date_added']));
  }
  forward404Unless($reviews_array);
  page_head();
