<?php
/*
  $Id$
*/
?>

<div id="footer">
<div class="footer_link">
<?php 
if ($banner = tep_banner_exists('dynamic', 'footer1')) { echo  tep_display_banner('static', $banner) ; }?>

</div>
          <address class="footer_contacts">
        <?php echo FOOTER_TEXT_BODY . "\n"; ?><br>
              Copyright&nbsp;&copy;&nbsp;<?php echo date("Y");?>&nbsp;&nbsp;
<?php
echo '<a class="bold" href="'. HTTP_SERVER.'">'.STORE_NAME.'</a>';
?>

            </address>
  <?php if ($banner = tep_banner_exists('dynamic', 'footer')) { echo  '<div>' . "\n" . tep_display_banner('static', $banner) . '</div>'; }?>

</div>
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&amp;&amp;p=g&amp;&amp;md=nj" alt="" height="1" width="1" ></noscript>
