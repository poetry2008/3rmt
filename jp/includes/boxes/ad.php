<!-- bestlinks //-->
<script src="js/blank.js" type="text/javascript"></script>
<div class="boxText">
<?php
	echo tep_image(DIR_WS_IMAGES.'design/box/bestlinks.gif','リンク集',171,25) . "\n";
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
?>
</div>
<!-- bestlinks_eof //-->
