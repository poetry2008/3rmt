<?php
/*
  $Id: manufacturers.php,v 1.2 2003/02/20 21:25:02 junnichi Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<img src="images/design/oc/l_menu_end.jpg" alt="FF11 攻略" width="163" height="27">
<!--left_banner -->
  <?php if ($banner = tep_banner_exists('dynamic', 'left1')) { echo  tep_display_banner('static', $banner) ; }?>
  <?php if ($banner = tep_banner_exists('dynamic', 'left2')) { echo  '<br><br>'.tep_display_banner('static', $banner) ; }?>
  <?php if ($banner = tep_banner_exists('dynamic', 'left3')) { echo  '<br><br>'.tep_display_banner('static', $banner) ; }?>
<!--left_banner_eof -->