<?php
/*
  $Id$
*/
?>

<div id="footer">
<div class="footer_link"><?php
    $footer_info_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where status = 1 and romaji != 'company' and romaji != 'payment'  and
 site_id = ".SITE_ID." order by sort_id"); 
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
      echo '|'; 
    }
  ?>
<?php echo '<a href="'.tep_href_link(FILENAME_CONTACT_US).'">'.BOX_INFORMATION_CONTACT.'</a>|<a
href="'.HTTP_SERVER.'/link/">'.FOOTER_LINK_TEXT.'</a>';?>
<?php echo '<br>';?> 
</div>
          <address class="footer_contacts">
        <?php echo FOOTER_TEXT_BODY . "\n"; ?><br>
              Copyright&nbsp;&copy;&nbsp;2011&nbsp;&nbsp;
<?php
echo '<a class="bold" href="'. HTTP_SERVER.'">'.STORE_NAME.'</a>';
?>

            </address>
  <?php if ($banner = tep_banner_exists('dynamic', 'footer')) { echo  '<div align="center">' . "\n" . tep_display_banner('static', $banner) . '</div>'; }?>

</div>
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&amp;&amp;p=g&amp;&amp;md=nj" alt="" height="1" width="1" ></noscript>
