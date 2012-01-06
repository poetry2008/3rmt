<?php
/*
  $Id$
*/
?>
<div id="footer">
          <address class="footer_contacts">
        <?php echo FOOTER_TEXT_BODY . "\n"; ?><br>
              Copyright&nbsp;&copy;&nbsp;<?php echo date("Y");?>&nbsp;&nbsp;
<?php
echo '<a class = "bold" href="'.tep_href_link('/').'" ><strong>'.STORE_NAME.'</strong></a>';
?>

            </address>
            <?php if ($banner = tep_banner_exists('dynamic', 'footer')) { echo  '<div align="center">' . "\n" . tep_display_banner('static', $banner) . '</div>'; }?>

</div>
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&amp;&amp;p=g&amp;&amp;md=nj" alt="" height="1" width="1" ></noscript>
