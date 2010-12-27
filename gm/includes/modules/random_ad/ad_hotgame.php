<script src="js/blank.js" type="text/javascript"></script>
<?php
	srand((double)microtime()*1000000);
	$ad_list1 = file("includes/modules/random_ad/ad_hotgame.txt");
	$ad_key1 = array_rand($ad_list1, 1);
	echo $ad_list1[$ad_key1];
?>
