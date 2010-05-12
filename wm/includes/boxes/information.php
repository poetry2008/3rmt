<?php
/*
  $Id$
*/
?>
<!-- information //-->
<div id="information">
  <?php //echo tep_image(DIR_WS_IMAGES.'design/box/information.gif',BOX_HEADING_INFORMATION,172,39) . "\n"; ?>
  <img width="172" height="39" alt="RMT情報" src="images/design/box/information.gif">
  <ul class="l_m_category_ul">
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
    echo '    <li class="l_m_category_li">' . "\n";
    echo '      <a href="' .  info_tep_href_link($result['romaji']) . '">' . $result['heading_title'] . '</a>' . "\n";
    echo '    </li>' . "\n";
  } 
// Extra Pages ADDED END

?>
    <li class="l_m_category_li">
      <?php echo '<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">' . BOX_INFORMATION_CONTACT . '</a>' . "\n"; ?>
    </li>
    <li class="l_m_category_li">
      <?php echo '<a href="' . tep_href_link('reorder.php') . '">' . '再配達フォーム' . '</a>' . "\n"; ?>
    </li>
  </ul>
  <img src="images/design/box/box_bottom_bg_01.gif" width="172" height="14" alt="" >
</div>
<!-- information_eof //-->
