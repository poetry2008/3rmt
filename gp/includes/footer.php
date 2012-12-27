<?php
/*
   $Id$
 */
?>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-29027754-1']);
_gaq.push(['_trackPageview']);

(function() {
 var ga = document.createElement('script'); ga.type = 'text/javascript';
 ga.async = true;
 ga.src = ('https:' == document.location.protocol ? 'https://ssl' :
   'http://www') + '.google-analytics.com/ga.js';
 var s = document.getElementsByTagName('script')[0];
 s.parentNode.insertBefore(ga, s);
 })();
</script>
<table class="footer_top" width="900"> 
<tr>
<td colspan="3">
<?php include(DIR_WS_BOXES.'information.php');?> 
<div class="buttom_warp02">
<?php if ($banner = tep_banner_exists('dynamic', 'footer')) { echo tep_display_banner('static', $banner); }?>
</div>

<div class="buttom_warp03">
<?php echo DEFAULT_PAGE_BOTTOM_CONTENTS;?> 
</div>

</td>
</tr>
</table> 
<div id="footer">
<?php echo str_replace('${YEAR}',date('Y'),FOOTER_TEXT_BODY) . "\n"; ?>
</div>
