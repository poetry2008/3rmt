<?php
/*
  $Id$
*/
?>
<div id="f_menu">
<?php if ($banner = tep_banner_exists('dynamic', 'footer1')) { echo  tep_display_banner('static', $banner); }?>
</div>
<div id="footer">
<?php echo str_replace('${YEAR}',date('Y'),FOOTER_TEXT_BODY) . "\n"; ?>
</div>
<?php if ($banner = tep_banner_exists('dynamic', 'footer')) { 
  echo  '<div class="footer_links_bgcolor"><div class="footer_links" align="center">' .tep_display_banner('static', $banner); 
  echo "</div></div>";
}?>
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
