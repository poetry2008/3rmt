<?php
/*
  $Id$
*/
?>
<!--left_banner -->
<?php
  //if ($banner = tep_banner_exists('dynamic', 'left1')) { echo '<div align="center" style="padding-bottom:5px; ">'.tep_display_banner('static', $banner).'</div>'."\n"; } 
  if ($banner = tep_banner_exists('dynamic', 'left2')) { echo '<div align="center" style="padding:10px 0 5px 0; ">'.tep_display_banner('static', $banner).'</div>'."\n"; }
  if ($banner = tep_banner_exists('dynamic', 'left3')) { echo '<div align="center" style="padding-bottom:5px; ">'.tep_display_banner('static', $banner).'</div>'."\n"; }
  if ($banner = tep_banner_exists('dynamic', 'left4')) { echo '<div align="center" style="padding-bottom:5px; ">'.tep_display_banner('static', $banner).'</div>'."\n"; }
  if ($banner = tep_banner_exists('dynamic', 'left5')) { echo '<div align="center" style="padding-bottom:5px; ">'.tep_display_banner('static', $banner).'</div>'."\n"; }
?>
<!--left_banner_eof -->
