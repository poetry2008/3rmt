<?php include(DIR_WS_BOXES . 'best_sellers.php');?>
<?php 
if (!isset($is_show_review) || ($is_show_review == true)) {
  if ((basename($PHP_SELF) != FILENAME_PRODUCT_INFO) && (basename($PHP_SELF) != FILENAME_ADVANCED_SEARCH_RESULT)) {
    include(DIR_WS_BOXES . 'reviews.php');
  }
}
?>
<div id="footer">
		<div id="footer-nav">
<?php if ($banner = tep_banner_exists('dynamic', 'footer1')) {  echo tep_display_banner    ('static',  $banner); }?>
		</div>
  <?php echo str_replace('${YEAR}',date('Y'),FOOTER_TEXT_BODY) . "\n"; ?>
  <?php if ($banner = tep_banner_exists('dynamic', 'footer')) { 
  echo  '<div align="center" class="footer_links" >' .tep_display_banner('static',
      $banner) . '</div>'; 
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
  ?>
  </pre>
</div>
<?php }?>            </div>
