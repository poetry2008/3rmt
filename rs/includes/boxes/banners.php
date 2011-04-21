<?php
/*
  $Id$
*/
?>
<!--left_banner -->
<?php
  if ($banner = tep_banner_exists('dynamic', 'left1')) { echo '<div class="link_banner">'.tep_display_banner('static', $banner, 170, 365).'</div>'."\n"; } 
  if ($banner = tep_banner_exists('dynamic', 'left2')) { echo '<div class="link_banner">'.tep_display_banner('static', $banner, 170, 100).'</div>'."\n"; }
  //if ($banner = tep_banner_exists('dynamic', 'left3')) { echo '<div class="link_banner">'.tep_display_banner('static', $banner, 170, 79).'</div>'."\n"; }
  //if ($banner = tep_banner_exists('dynamic', 'left4')) { echo '<div class="link_banner">'.tep_display_banner('static', $banner).'</div>'."\n"; }
  //if ($banner = tep_banner_exists('dynamic', 'left5')) { echo '<div class="link_banner">'.tep_display_banner('static', $banner).'</div>'."\n"; }
?>
<!--left_banner_eof -->
