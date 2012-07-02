<?php
/*
  $Id$
*/
?>
<!--left_banner -->
  <?php if ($banner = tep_banner_exists('dynamic', 'left1')) { echo tep_display_banner('static', $banner); }?>
  <div class="work_time_banner01"> 
  <?php if ($banner = tep_banner_exists('dynamic', 'left2')) { echo  tep_display_banner('static', $banner) ; }?>
  </div> 
  <?php if ($banner = tep_banner_exists('dynamic', 'left3')) { echo  tep_display_banner('static', $banner) ; }?>
<div class="right_banner_bottom01"></div>
<!--left_banner_eof -->