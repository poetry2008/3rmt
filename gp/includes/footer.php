<?php
/*
  $Id$
*/
?>
 <table class="footer_top"> 
  <tr>
    <td colspan="3">
    <?php include(DIR_WS_BOXES.'information.php');?> 
    <div class="buttom_warp02">
    <?php if ($banner = tep_banner_exists('dynamic', 'footer')) { echo tep_display_banner('static', $banner); }?>
    </div>
    
    <div class="buttom_warp03">
    <?php echo DEFAULT_PAGE_BOTTOM_CONTENTS;?> 
    </div>
    
    </td>
  </tr>
 </table> 
<div id="footer">
          <address class="footer_contacts">
        <?php echo FOOTER_TEXT_BODY . "\n"; ?><br>
              Copyright&nbsp;&copy;&nbsp;2011&nbsp;&nbsp;
<?php
echo '<a class = "bold" href="'.tep_href_link('/').'" ><strong>'.STORE_NAME.'</strong></a>';
?>

            </address>
</div>
<script type="text/javascript" src="js/access.js"></script>
<noscript><img src="axs/dsw.cgi?pg=RMT&amp;&amp;p=g&amp;&amp;md=nj" alt="" height="1" width="1" ></noscript>
