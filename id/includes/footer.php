<?php
/*
  $Id$
*/
?>
<div id="footer" class="footer_5">
  <?php /* 
          <div class="footer_games_box">
          <?php //if ($banner = tep_banner_exists('dynamic', 'footer')) { echo  '<div class="footer_games" align="center">' . "\n" . tep_display_banner('static', $banner) . '</div>'; }?>  
            </div>
    */ ?>
        <div class="info_foot01">

<?php 
if ($banner = tep_banner_exists('dynamic', 'footer1')) { 
  echo   tep_display_banner('static',  $banner) ; }?>
        
       </div>
          <?php echo str_replace('${YEAR}',date('Y'),FOOTER_TEXT_BODY) . "\n"; ?>
            <?php if ($banner = tep_banner_exists('dynamic', 'footer')) { echo  '<div class="info_foot02">' . "\n" . tep_display_banner('static', $banner) . '</div>'; }?>

<?php 
// 显示SQL执行记录
if (STORE_DB_TRANSACTIONS and 0) {?>
<div id="debug_info" style="text-align:left;">
  <pre>
<?php if(isset($log_queries)){
    foreach ($log_queries as $qk => $qv) {
      echo '[' . $log_times[$qk] . ']' . $qk . "\t=>\t" . $qv."\n";
    }
  }
  print_r($_COOKIE);
  print_r($_SESSION);
  ?>
  </pre>
</div>
<?php }?>
