<?php
/*
  $Id$
*/
?>
<div id="f_menu">
<a href="<?php echo HTTP_SERVER;?>/info/starting_rmt.html"><?php echo FOOTER_FIRST;?></a>
&nbsp;|&nbsp;
<a href="<?php echo HTTP_SERVER;?>/info/salespolicies.html"><?php echo FOOTER_RECORD; ?></a>
&nbsp;|&nbsp;
<a href="<?php echo tep_href_link('rss.php'); ?>"><?php echo FOOTER_RSS;?></a>
&nbsp;|&nbsp;
<a href="<?php echo HTTP_SERVER;?>/link/"><?php echo FOOTER_LINKS; ?></a>
</div>
<div id="footer">
  <address class="copyright">
  <?php echo FOOTER_TEXT_BODY ; ?>
  </address>
</div>
<?php if ($banner = tep_banner_exists('dynamic', 'footer')) { 
  echo  '<div class="footer_links" align="center">' .tep_display_banner('static',
      $banner) . '<br><a href="'.HTTP_SERVER.'">'.STORE_NAME.'</a></div>'; 
}?>
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
<?php }?>
