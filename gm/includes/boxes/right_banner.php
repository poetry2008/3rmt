<?php
/*
  $Id$
*/
?>
<!--right banner -->
<div class="right_pic_banner01">
<?php
    if ($banner = tep_banner_exists('dynamic', 'right1')) { echo tep_display_banner('static', $banner).'';  }
    if ($banner = tep_banner_exists('dynamic', 'right2')) { echo tep_display_banner('static', $banner).'';  }
    if ($banner = tep_banner_exists('dynamic', 'right3')) { echo tep_display_banner('static', $banner).'';  }
    if ($banner = tep_banner_exists('dynamic', 'right4')) { echo tep_display_banner('static', $banner).'';  }

  if (!isset($index_default)) {
    if ($banner = tep_banner_exists('dynamic', 'right5')) { echo tep_display_banner('static', $banner).'';  }
    echo "\n" . '<p>';
    include ('includes/modules/random_ad/ad_hotgame.php');
    echo '</p>' . "\n" . '<p>';
    include ('includes/modules/random_ad/ad_mmonavi.php');
    echo '</p>' . "\n";
    if ($banner = tep_banner_exists('dynamic', 'right6')) { echo tep_display_banner('static', $banner).'';  }
    if ($banner = tep_banner_exists('dynamic', 'right7')) { echo tep_display_banner('static', $banner).'';  }
    if ($banner = tep_banner_exists('dynamic', 'right8')) { echo tep_display_banner('static', $banner).'';  }
    if ($banner = tep_banner_exists('dynamic', 'right9')) { echo tep_display_banner('static', $banner).'';  }
?>
<div class="best_link_bottom01"></div>
<?php
  }
?>

</div>
<!--right_banner_eof//-->
