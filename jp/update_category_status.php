<?php
  require('includes/application_top.php');

  $category_query = tep_db_query("select * from ".TABLE_CATEGORIES);
  
  while ($category_res = tep_db_fetch_array($category_query)) {
    $update_sql = "update `".TABLE_CATEGORIES_DESCRIPTION."` set `categories_status` = '".$category_res['categories_status']."' where `categories_id`= '".$category_res['categories_id']."'"; 
    tep_db_query($update_sql); 
  }
  echo 'finish';
?>
