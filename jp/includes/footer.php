<?php
/*
  $Id$
*/
?>
<div id="footer">
  <?php echo str_replace('${YEAR}',date('Y'),FOOTER_TEXT_BODY) . "\n"; ?>
  <?php if ($banner = tep_banner_exists('dynamic', 'footer')) { echo  '<br><div align="center">' . "\n" . tep_display_banner('static', $banner) . '</div>'; }?>
</div>
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&amp;&amp;p=g&amp;&amp;md=nj" alt="" height="1" width="1" ></noscript>
<?php 
// 显示SQL执行记录
if (STORE_DB_TRANSACTIONS) {?>
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
<?php }?>
