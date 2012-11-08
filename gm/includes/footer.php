<?php include(DIR_WS_BOXES . 'best_sellers.php');?>
<div id="footer">
		<div id="footer-nav">
                <a href="<?php echo HTTP_SERVER;?>/sitemap.php"><?php echo TEXT_FOOTER_ONE;?></a>&nbsp;| 

                <a href="<?php echo tep_href_link(FILENAME_REORDER,'','NONSSL');?>"><?php echo TEXT_FOOTER_TWO;?></a>&nbsp;| 
<?php 
    $footer_info_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where status = 1 and romaji != 'company' and romaji != 'payment'  and
 site_id = ".SITE_ID." order by sort_id"); 
    $footer_info_total_num = tep_db_num_rows($footer_info_query);
    if ($footer_info_total_num > 0) {
      $footer_num = 0; 
      while ($footer_info_res = tep_db_fetch_array($footer_info_query)) {
        echo '<a href="'.info_tep_href_link($footer_info_res['romaji']).'">'.$footer_info_res['heading_title'].'</a>'; 
        $footer_num++;  
        if ($footer_num < $footer_info_total_num) {
          echo ' | '; 
        }
      }
      echo ' | '; 
    }
?>

                <a href="<?php echo HTTP_SERVER;?>/link/"><?php echo TEXT_FOOTER_FIVE;?></a>&nbsp;|
                <a href="<?php echo tep_href_link(FILENAME_RSS, '', 'NONSSL');?>">RSS</a>&nbsp;
		</div>
                  <p><?php echo TEXT_FOOTER_SIX;?></p>
  <?php if ($banner = tep_banner_exists('dynamic', 'footer')) { 
  echo  '<div class="footer_links" align="center">' .tep_display_banner('static',
      $banner) . '<br><a href="'.HTTP_SERVER.'">'.STORE_NAME.'</a></div>'; 
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
