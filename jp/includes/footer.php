<?php
/*
  $Id$
*/
?>
<div id="footer">
  <?php echo FOOTER_TEXT_BODY . "\n"; ?>
  <address>
    Copyright&nbsp;&copy;&nbsp;2004-2009&nbsp;Jackpot&nbsp;<a class="bold" href="http://www.iimy.co.jp/"><strong>RMT</strong>ジャックポット</a>
  </address>
  <?php if ($banner = tep_banner_exists('dynamic', 'footer')) { echo  '<br><div align="center">' . "\n" . tep_display_banner('static', $banner) . '</div>'; }?>
</div>
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&amp;&amp;p=g&amp;&amp;md=nj" alt="" height="1" width="1" ></noscript>
