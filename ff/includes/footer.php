<?php
/*
  $Id$
*/
?>
<div class="footer_link"><?php
    $footer_info_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where status = 1 and romaji != 'company' and romaji != 'payment'  and
 site_id = 7 order by sort_id"); 
    $footer_info_total_num = tep_db_num_rows($footer_info_query);
    if ($footer_info_total_num > 0) {
      $footer_num = 0; 
      while ($footer_info_res = tep_db_fetch_array($footer_info_query)) {
        echo '<a href="'.info_tep_href_link($footer_info_res['romaji']).'">'.$footer_info_res['heading_title'].'</a>'; 
        $footer_num++;  
        if ($footer_num < $footer_info_total_num) {
          echo '|'; 
        }
      }
      echo '<br>'; 
    }
  ?>
</div>
<div id="footer">

          <address class="footer_contacts">
        <?php echo FOOTER_TEXT_BODY . "\n"; ?><br>
              Copyright&nbsp;&copy;&nbsp;2011&nbsp;&nbsp;
<?php
echo '<a class="bold" href="'. HTTP_SERVER.'">'.$_SERVER['HTTP_HOST'].'</a>';
?>

            </address>

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
//      echo '[' . $log_times[$qk] . ']' . $qk . "\t=>\t" . $qv."\n";
    }
  }
  print_r($_COOKIE);
  print_r($_SESSION);
  ?>
  </pre>
</div>
<?php }?>
