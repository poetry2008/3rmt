<?php
/*
  $Id$
*/
?>
<div id="f_menu">
<a href="<?php echo HTTP_SERVER;?>/info/starting_rmt.html">はじめてのRMT</a>
&nbsp;|&nbsp;
<a href="<?php echo HTTP_SERVER;?>/info/salespolicies.html">特定商取引に基づく表記</a>
&nbsp;|&nbsp;
<a href="<?php echo tep_href_link('rss.php'); ?>">RSS</a>
&nbsp;|&nbsp;
<a href="<?php echo HTTP_SERVER;?>/link/">相互リンク</a>
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
// 譏ｾ示SQL謇ｧ行隶ｰ蠖销
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
