<?php
/*
  $Id$
*/
?>
<div id="f_menu">
<?php if ($banner = tep_banner_exists('dynamic', 'footer1')) { echo  '<div align="center">' . "\n" . tep_display_banner('static', $banner) . '</div>'; }?>
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
