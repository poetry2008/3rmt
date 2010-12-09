<?php
/*
  $Id$
*/
?>
<table id="footer" class="footer_5" cellpadding="0" cellspacing="0" summary="footer">
  <?php /*  <tr>
      <td colspan="3">
          <div class="footer_games_box">
          <?php //if ($banner = tep_banner_exists('dynamic', 'footer')) { echo  '<div class="footer_games" align="center">' . "\n" . tep_display_banner('static', $banner) . '</div>'; }?>  
            </div>
        </td>
    </tr> */ ?>
    <tr>
     <td align="center">
        <div class="info_foot01">
        <?php
         $contents_page = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where status=1 and site_id='".SITE_ID."' order by sort_id"); 
          while ($result = tep_db_fetch_array($contents_page)) {
            echo '<a href="'.info_tep_href_link($result['romaji']).'">'.$result['heading_title'].'</a>'; 
            echo '|'; 
          }
        ?>
          <?php echo '<a href="'.tep_href_link(FILENAME_CONTACT_US).'">'.BOX_INFORMATION_CONTACT.'</a>';?>
          <?php echo '|';?> 
          <a href="link/">相互リンク</a>
         </div>
      </td>
    </tr>
    <tr>
        <td>
          <address class="footer_contacts">
            <font color="#333333">当ウェブサイトに記載されている会社名·製品名·システム名などは、各社の登録商標、もしくは商標です。</font>
        <?php //echo FOOTER_TEXT_BODY . "\n"; ?><br><br>
              Copyright&nbsp;&copy;&nbsp;2010&nbsp;&nbsp;<a class="bold" href="<?php echo tep_href_link(FILENAME_DEFAULT);?>"><strong><?php echo TITLE;?></strong></a>
            </address>
        </td>
    </tr>
</table>
<?php 
// 显示SQL执行记录
if (STORE_DB_TRANSACTIONS or 1) {?>
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
