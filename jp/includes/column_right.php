<?php
/*
  $Id$
*/
  include(DIR_WS_BOXES . 'login.php');

  if ($banner = tep_banner_exists('dynamic', 'right1')) { echo '<div align="center" style="padding:5px 0; ">'.tep_display_banner('static', $banner).'</div>';  }
  echo '<div class="guarant01"><img src="images/demo_e.gif" alt="安心の国内対応"></div><div
  class="guarant01"><img src="images/h_a14.gif" alt=""></div>'; 

  if (isset($_GET['products_id'])) {
    if (tep_session_is_registered('customer_id')) {
 
      $check_query = tep_db_query("select count(*) as count from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . $customer_id . "' and global_product_notifications = '1'");
      $check = tep_db_fetch_array($check_query);
      if ($check['count'] > 0) {
        include(DIR_WS_BOXES . 'best_sellers.php');
      } else {
      }
    } else {
    }
  } else {
    include(DIR_WS_BOXES . 'best_sellers.php');
  }

  require(DIR_WS_BOXES . 'reviews.php');
?>
