<?php
/*
  $Id$
*/
?>
<div id="f_menu">
<a href="/info/starting_rmt.html">はじめてのRMT</a>
&nbsp;|&nbsp;
<a href="/info/salespolicies.html">特定商取引に基づく表記</a>
&nbsp;|&nbsp;
<a href="<?php echo tep_href_link('rss.php'); ?>">RSS</a>
<?php /*
&nbsp;|&nbsp;
<a href="<?php echo tep_href_link('domain.php');?>">相互リンク</a> */ ?>
</div>
<div id="footer">
  <address class="copyright">
  <?php echo FOOTER_TEXT_BODY ; ?>
  </address>
</div>
<?php if ($banner = tep_banner_exists('dynamic', 'footer')) { 
  echo  '<div class="footer_links" align="center">' .tep_display_banner('static', $banner) . '<br><a target="_blank" href="'.HTTP_SERVER.'">RMTゲームマネー</a></div>'; 
}?>
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&amp;&amp;p=g&amp;&amp;md=nj" alt="img" height="1" width="1" border="0"></noscript>
<?php
/*
<noscript>Some functions Supported by <a href="http://www.ds-style.com">DigitalStudio</a> INC.  Powered by <a href="http://osc.ds-style.com">osCommerce</a></noscript>
*/
?>
<?php 
// 显示SQL执行记录
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
