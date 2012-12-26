<?php
/*
  $Id$
*/
?>
<div class="footer_link">
<?php
if ($banner = tep_banner_exists('dynamic', 'footer1')) { echo  tep_display_banner('static', $banner) ; }
?>

</div>
<div id="footer">
  <?php echo str_replace('${YEAR}',date('Y'),FOOTER_TEXT_BODY) . "\n"; ?>
            <?php if ($banner = tep_banner_exists('dynamic', 'footer')) { echo  '<div  class="footer_link02">' . "\n" . tep_display_banner('static', $banner) . '</div>'; }?>

</div>
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&amp;&amp;p=g&amp;&amp;md=nj" alt="" height="1" width="1" ></noscript>
