<?php
/*
  $Id$
*/
?>
<!--right_banner -->
<?php
  if ($banner = tep_banner_exists('dynamic', 'right1')) { echo '<div class="link_banner">'.tep_display_banner('static', $banner).'</div>'."\n"; } 
  //if ($banner = tep_banner_exists('dynamic', 'right2')) { echo '<div class="link_banner">'.tep_display_banner('static', $banner, 170, 79).'</div>'."\n"; }
  //if ($banner = tep_banner_exists('dynamic', 'right3')) { echo '<div class="link_banner">'.tep_display_banner('static', $banner, 170, 79).'</div>'."\n"; }
  if ($banner = tep_banner_exists('dynamic', 'right4')) { echo '<div class="link_banner">'.tep_display_banner('static', $banner).'</div>'."\n"; }
  if ($banner = tep_banner_exists('dynamic', 'right5')) { echo '<div class="link_banner">'.tep_display_banner('static', $banner).'</div>'."\n"; }
?>
<!--right_banner_eof -->
