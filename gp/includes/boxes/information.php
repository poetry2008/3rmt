<?php
/*
  $Id$
*/
?>
<!-- information //-->
<div class="buttom_warp01">
  <div class="buttom_menu">
インフォメーション
  </div>
  <ul>
<?php
// ccdd
  $contents_page = tep_db_query("
      select * 
      from ".TABLE_INFORMATION_PAGE." 
      where status = 1 
        and site_id = ".SITE_ID." 
      order by sort_id 
  ");
  while ($result = tep_db_fetch_array($contents_page)){
    echo '    <li>' . "\n";
    echo '      <a href="' .  info_tep_href_link($result['romaji']) . '">' . $result['heading_title'] . '</a>' . "\n";
    echo '    </li>' . "\n";
  } 
// Extra Pages ADDED END
?>
    <li><a href="<?php echo HTTP_SERVER.'/link/';?>">相互リンク</a></li>
  </ul>
</div>
<!-- information_eof //-->
