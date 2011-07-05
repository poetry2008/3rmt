<?php
/*
  $Id$
*/
 // require(DIR_WS_BOXES . 'shopping_cart.php');

//  if (isset($_GET['products_id'])) include(DIR_WS_BOXES . 'manufacturer_info.php');

 // if (tep_session_is_registered('customer_id')) include(DIR_WS_BOXES . 'order_history.php');
require(DIR_WS_BOXES . 'banners.php');
  //include(DIR_WS_BOXES . 'login.php');
//define ("RIGHT_ORDER_TEXT","ÔÙÅäß_ÒÀîm");
  //echo '<div class="reorder"><a class="reorder_link" href="'.tep_href_link('reorder.php').'"><img src="images/design/reorder.gif" alt="'.RIGHT_ORDER_TEXT.'"></a></div>';
  //require(DIR_WS_BOXES . 'reviews.php');
  
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
  
  //require(DIR_WS_BOXES . 'information.php');
  //require(DIR_WS_BOXES . 'best_goods.php');
  //include(DIR_WS_BOXES . 'right_banner.php') ;
  //require(DIR_WS_BOXES . 'left_link.php');
  
