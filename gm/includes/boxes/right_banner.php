<!--right banner -->
<div>
<?php
	  if ($banner = tep_banner_exists('dynamic', 'right1')) { echo tep_display_banner('static', $banner).'';  }
	  if ($banner = tep_banner_exists('dynamic', 'right2')) { echo tep_display_banner('static', $banner).'';  }
	  if ($banner = tep_banner_exists('dynamic', 'right3')) { echo tep_display_banner('static', $banner).'';  }
	  if ($banner = tep_banner_exists('dynamic', 'right4')) { echo tep_display_banner('static', $banner).'';  }
	  if ($banner = tep_banner_exists('dynamic', 'right5')) { echo tep_display_banner('static', $banner).'';  }
	  echo "\n" . '<p>';
	  include ('includes/modules/random_ad/ad_hotgame.php');
	  echo '</p>' . "\n" . '<p>';
	  include ('includes/modules/random_ad/ad_mmonavi.php');
	  echo '</p>' . "\n";
	//echo "\n" . '<p>';
	/*
	if (isset($current_category_id) && ($current_category_id > 0)) {
		$g_id = $current_category_id;
		//if ($g_id == "168" || $g_id == "169" || $g_id == "170" || $g_id == "171" || $g_id == "177" || $g_id == "178" || $g_id == "179" || $g_id == "190" || $g_id == "195" || $g_id == "200" || $g_id == "203" || $g_id == "206" || $g_id == "209" || $g_id == "212") {
		if (file_exists('includes/modules/ad/' . (int)$g_id . '.php')) {
			include('includes/modules/ad/' . (int)$g_id . '.php');
		} else {
			include('includes/modules/ad/main.php');
		}
	} else {
		include('includes/modules/ad/main.php');
	}
	*/
	//echo '</p>' . "\n";
	  if ($banner = tep_banner_exists('dynamic', 'right6')) { echo tep_display_banner('static', $banner).'';  }
	  if ($banner = tep_banner_exists('dynamic', 'right7')) { echo tep_display_banner('static', $banner).'';  }
	  if ($banner = tep_banner_exists('dynamic', 'right8')) { echo tep_display_banner('static', $banner).'';  }
	  if ($banner = tep_banner_exists('dynamic', 'right9')) { echo tep_display_banner('static', $banner).'';  }
?></div>
<!--right_banner_eof//-->
