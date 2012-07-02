<?php include(DIR_WS_BOXES . 'best_sellers.php');?>
<div id="footer">
		<div id="footer-nav">
                <a href="sitemap.php"><?php echo TEXT_FOOTER_ONE;?></a>&nbsp;| 
                <a href="<?php echo tep_href_link(FILENAME_REORDER,'','NONSSL');?>"><?php echo TEXT_FOOTER_TWO;?></a>&nbsp;| 
                <a href="/info/profile.html"><?php echo TEXT_FOOTER_THREE;?></a>&nbsp;| 
                <a href="/info/salespolicies.html"><?php echo TEXT_FOOTER_FOUR;?></a>&nbsp;| 
                <a href="/link/"><?php echo TEXT_FOOTER_FIVE;?></a>&nbsp;
		</div>
                  <p><?php echo TEXT_FOOTER_SIX;?></p>
  <?php if ($banner = tep_banner_exists('dynamic', 'footer')) { 
  echo  '<div class="footer_links" align="center">' .tep_display_banner('static', $banner) . '<br><a href="'.HTTP_SERVER.'">RMTゲームマネー</a></div>'; 
}?>
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&amp;&amp;p=g&amp;&amp;md=nj" alt="img" height="1" width="1" border="0"></noscript>
<?php
/*
<noscript>Some functions Supported by <a href="http://www.ds-style.com">DigitalStudio</a> INC.  Powered by <a href="http://osc.ds-style.com">osCommerce</a></noscript>
*/
?>
<?php 
if (STORE_DB_TRANSACTIONS && false) {?>
<div id="debug_info" style="text-align:left;">
  <pre>
<?php if(isset($log_queries)){
    foreach ($log_queries as $qk => $qv) {
      echo '[' . $log_times[$qk] . ']' . $qk . "\t=>\t" . $qv."\n";
    }
  }
   //&& print_r($logger->queries);
  ?>
  <?php //print_r($logger->times);?>
  </pre>
</div>
<?php }?>            </div>
