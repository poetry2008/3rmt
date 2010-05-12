<?php
/*
  $Id$
*/
?>
<div id="f_menu">
<?php
  // add info romaji 
  $fo_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '3'"); 
  $fo_res = tep_db_fetch_array($fo_query); 
  if ($fo_res) { 
?>
<a href="<?php echo info_tep_href_link($fo_res['romaji']); ?>">はじめてのRMT</a>
<?php
  }
?>
&nbsp;|&nbsp;
<?php
  // add info romaji 
  $so_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '1'"); 
  $so_res = tep_db_fetch_array($so_query); 
  if ($so_res) { 
?>
<a href="<?php echo info_tep_href_link($so_res['romaji']); ?>">特定商取引に基づく表記</a>
<?php
  }
?>
&nbsp;|&nbsp;
<a href="<?php echo tep_href_link('rss.php'); ?>">RSS</a>
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
//for sql_log
$logNumber = 0;
tep_db_query('select * from cache');
$testArray = array();
//end for sql_log
?>
