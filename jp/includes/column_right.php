<?php
/*
  $Id$
*/
  include(DIR_WS_BOXES . 'login.php');

  if ($banner = tep_banner_exists('dynamic', 'right1')) { echo '<div align="center" style="padding:5px 0; border-bottom:1px dashed #ccc; ">'.tep_display_banner('static', $banner).'</div>';  }
  echo '<div class="guarant01"><img src="images/h_a14.gif" alt=""></div>'; 

  if (isset($_GET['products_id'])) {
    if (tep_session_is_registered('customer_id')) {
//ccdd
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
  if (isset($_GET['products_id'])) {
    if (basename($PHP_SELF) != FILENAME_TELL_A_FRIEND) include(DIR_WS_BOXES . 'tell_a_friend.php');
  } else {
    include(DIR_WS_BOXES . 'specials.php');
  }
*/

  require(DIR_WS_BOXES . 'reviews.php');
/*
  if (substr(basename($PHP_SELF), 0, 8) != 'checkout') {
    include(DIR_WS_BOXES . 'languages.php');
    include(DIR_WS_BOXES . 'currencies.php');
  }
*/
//include(DIR_WS_BOXES . 'pickup.php');
  //include(DIR_WS_BOXES . 'right_banner.php') ;
  //include(DIR_WS_BOXES . 'ad.php') ;
?>
