<?php
/*
  $Id$
*/

  //require(DIR_WS_BOXES . 'quick_link.php');

  if ( (USE_CACHE == 'true') && !SID ) {
    include(DIR_WS_BOXES . 'categories.php');
   // echo tep_cache_categories_box();
  } else {
    include(DIR_WS_BOXES . 'categories.php');
  }
?> <div class="reorder_link">
	<div class="menu_top"><?php echo LEFT_REORDER_TITLE;?></div>
    <div class="reorder_link_info">
  <a href="<?php echo tep_href_link('reorder.php');?>"><?php echo LEFT_REORDER_TEXT;?></a> 
  </div>
  </div>
<?php 
include(DIR_WS_BOXES . 'login.php');
  if (isset($_GET['products_id'])) {
    if (tep_session_is_registered('customer_id')) {
      $check_query = tep_db_query("select count(*) as count from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . $customer_id . "' and global_product_notifications = '1'");
      $check = tep_db_fetch_array($check_query);
      if ($check['count'] > 0) {
        include(DIR_WS_BOXES . 'best_sellers.php');
      } else {
     //   include(DIR_WS_BOXES . 'product_notifications.php');
      }
    } else {
    //  include(DIR_WS_BOXES . 'product_notifications.php');
    }
  } else {
    include(DIR_WS_BOXES . 'best_sellers.php');
  }
/*
  //Color
  if(COLOR_SEARCH_BOX_TF == 'true') {
    include(DIR_WS_BOXES . 'color.php');
  }
*/

  //require(DIR_WS_BOXES . 'information.php');
  //require(DIR_WS_BOXES . 'banners.php');
  //require(DIR_WS_BOXES . 'cl.php');

/*s
  require(DIR_WS_BOXES . 'whats_new.php');*/
    
  /*if (substr(basename($PHP_SELF), 0, 5) == 'index' && (int)$current_category_id == 0) {
   require(DIR_WS_BOXES . 'banners.php');
  }*/
?>
