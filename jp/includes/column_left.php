<?php
/*
  $Id$
*/

  //require(DIR_WS_BOXES . 'quick_link.php');

  include(DIR_WS_BOXES . 'categories.php');
/*
  //Color
  if(COLOR_SEARCH_BOX_TF == 'true') {
    include(DIR_WS_BOXES . 'color.php');
  }
*/
  require(DIR_WS_BOXES . 'information.php');
  require(DIR_WS_BOXES . 'banners.php');
  //require(DIR_WS_BOXES . 'cl.php');

/*s
  require(DIR_WS_BOXES . 'whats_new.php');*/
    
  /*if (substr(basename($PHP_SELF), 0, 5) == 'index' && (int)$current_category_id == 0) {
   require(DIR_WS_BOXES . 'banners.php');
  }*/
?>
