<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  $reviews_query = tep_db_query("select * from reviews where site_id = 9 limit 1");
  $reviews_res = tep_db_fetch_array($reviews_query);
 
  for ($i=0; $i<300000; $i++) {
  $reviews_sql = "insert reviews values(NULL, '".$reviews_res['products_id']."', '".$reviews_res['customers_id']."', '".$reviews_res['customers_name']."', '".$reviews_res['reviews_rating']."', '".$reviews_res['date_added']."', NULL, '".$reviews_res['reviews_read']."', '".$reviews_res['site_id']."', '".$reviews_res['reviews_status']."', '".$reviews_res['reviews_ip']."')"; 
  tep_db_query($reviews_sql);
  $reviews_id = tep_db_insert_id(); 
  $reviews_desc_sql = "insert reviews_description values('".$reviews_id."', '4', 'hello')";
  tep_db_query($reviews_desc_sql);
  }
