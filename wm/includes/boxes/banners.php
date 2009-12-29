<?php
/*
  $Id: manufacturers.php,v 1.2 2003/02/20 21:25:02 junnichi Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<!--left_banner -->
<?php
  if ($banner = tep_banner_exists('dynamic', 'left1')) { echo '<div align="center" style="padding-bottom:5px; ">'.tep_display_banner('static', $banner, 171, 281).'</div>'."\n"; } 
  if ($banner = tep_banner_exists('dynamic', 'left2')) { echo '<div align="center" style="padding-bottom:5px; ">'.tep_display_banner('static', $banner, 171, 113).'</div>'."\n"; }
  if ($banner = tep_banner_exists('dynamic', 'left3')) { echo '<div align="center" style="padding-bottom:5px; ">'.tep_display_banner('static', $banner, 171, 113).'</div>'."\n"; }
  if ($banner = tep_banner_exists('dynamic', 'left4')) { echo '<div align="center" style="padding-bottom:5px; ">'.tep_display_banner('static', $banner).'</div>'."\n"; }
  if ($banner = tep_banner_exists('dynamic', 'left5')) { echo '<div align="center" style="padding-bottom:5px; ">'.tep_display_banner('static', $banner).'</div>'."\n"; }
?>
<!--left_banner_eof -->
