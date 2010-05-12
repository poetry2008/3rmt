<?php
/*
  $Id$
*/
?>
<img src="images/design/oc/l_menu_end.jpg" alt="FF11 謾ｻ逡･" width="163" height="27">
<!--left_banner -->
  <?php if ($banner = tep_banner_exists('dynamic', 'left1')) { echo  tep_display_banner('static', $banner) ; }?>
  <?php if ($banner = tep_banner_exists('dynamic', 'left2')) { echo  '<br><br>'.tep_display_banner('static', $banner) ; }?>
  <?php if ($banner = tep_banner_exists('dynamic', 'left3')) { echo  '<br><br>'.tep_display_banner('static', $banner) ; }?>
<!--left_banner_eof -->