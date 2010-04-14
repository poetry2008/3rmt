<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<?php

  // maker
  //require(DIR_WS_BOXES . 'quick_link.php');

if(substr(basename($PHP_SELF), 0, 9) != 'affiliate'){

  if ( (USE_CACHE == 'true') && !SID ) {
    echo tep_cache_categories_box();
  } else {
    include(DIR_WS_BOXES . 'categories.php');
  }
/*
  //Color
  if(COLOR_SEARCH_BOX_TF == 'true') {
    include(DIR_WS_BOXES . 'color.php');
  }
*/
  require(DIR_WS_BOXES . 'information.php');
  require(DIR_WS_BOXES . 'banners.php');
  //require(DIR_WS_BOXES . 'cl.php');

 

 // Include OSC-AFFILIATE 
  
}else{
 
  require(DIR_WS_BOXES . 'affiliate.php');
}

/*s
  require(DIR_WS_BOXES . 'whats_new.php');*/
		
  /*if (substr(basename($PHP_SELF), 0, 5) == 'index' && (int)$current_category_id == 0) {
   require(DIR_WS_BOXES . 'banners.php');
  }*/
?>
