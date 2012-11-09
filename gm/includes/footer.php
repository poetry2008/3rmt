<?php include(DIR_WS_BOXES . 'best_sellers.php');?>
<div id="footer">
		<div id="footer-nav">
<?php if ($banner = tep_banner_exists('dynamic', 'footer1')) {  echo tep_display_banner    ('static',  $banner); }?>
		</div>
                  <p><?php echo TEXT_FOOTER_SIX;?></p>
  <?php if ($banner = tep_banner_exists('dynamic', 'footer')) { 
  echo  '<div class="footer_links" align="center">' .tep_display_banner('static',
      $banner) . '<br><a href="'.HTTP_SERVER.'">'.STORE_NAME.'</a></div>'; 
}?>
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&amp;&amp;p=g&amp;&amp;md=nj" alt="img" height="1" width="1" border="0"></noscript>
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
