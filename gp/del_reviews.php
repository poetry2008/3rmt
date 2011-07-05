<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  $reviews_query = tep_db_query("select * from reviews r where site_id = 9");
  while ($reviews_res = tep_db_fetch_array($reviews_query)) {
    tep_db_query("delete from reviews_description where reviews_id = '".$reviews_res['reviews_id']."'"); 
  }
