<?php
/*
  $Id$
*/
  require(DIR_WS_BOXES . 'login.php');
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 5px;" summary="reviews">
  <tr>
  <td align="center">
  <img width="171" height="27" alt="<?php echo CHANGE_DELIVERY;?>" src="images/design/reorder_right.gif" >
  <?php echo '<a href="'.tep_href_link('reorder.php').'"><img width="171"  alt="'.REORDER_FORM.'" src="images/design/reorder_right02.gif" ></a>';?>
  </td>
  </tr>
  </table>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 5px;">
  <tr>
  <td align="center">
  <a href="<?php echo tep_href_link(FILENAME_CONTACT_US,'','SSL') ;?>"><?php echo  tep_image(DIR_WS_IMAGES.'contact_us_img.gif',BOX_INFORMATION_CONTACT) ?></a>
  </td>
  </tr>
  </table>
<?php
  
  if ($_SERVER['REQUEST_URI'] != "/" && $_SERVER['REQUEST_URI'] != "/index.php") {
    if ($banner = tep_banner_exists('dynamic', 'left1')) { echo '<div align="center" style="padding-bottom:5px; ">'.tep_display_banner('static', $banner, 171, 323).'</div>'."\n"; } 
  }
  
  if (isset($HTTP_GET_VARS['products_id'])) {
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

  if ($_SERVER['REQUEST_URI'] != "/" && $_SERVER['REQUEST_URI'] != "/index.php") {
    require(DIR_WS_BOXES . 'reviews.php');
  }

  if ($banner = tep_banner_exists('dynamic', 'left2')) { echo '<div align="center" style="padding-bottom:5px; ">'.tep_display_banner('static', $banner, 171, 113).'</div>'."\n"; }
  //if ($banner = tep_banner_exists('dynamic', 'left3')) { echo '<div align="center" style="padding-bottom:5px; ">'.tep_display_banner('static', $banner, 171, 113).'</div>'."\n"; }

