<?php
/*
  $Id$
*/
?>
<!-- information //-->
<div id="information">
  <?php echo tep_image(DIR_WS_IMAGES.'design/box/information.gif',BOX_HEADING_INFORMATION,171,25) . "\n"; ?>
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
    echo '      <img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt="">' . "\n";
    echo '      <a href="' .
                  info_tep_href_link($result['romaji']) . '">' . $result['heading_title'] . '</a>' . "\n";
    echo '    </li>' . "\n";
  } 
// Extra Pages ADDED END

?>
    <li class="l_m_category_li">
      <img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt="">
      <?php echo '<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">' . BOX_INFORMATION_CONTACT . '</a>' . "\n"; ?>
    </li>
    <li class="l_m_category_li">
      <img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt="">
      <?php echo '<a href="' . tep_href_link('reorder.php') . '">' . '再配達フォーム' . '</a>' . "\n"; ?>
    </li>
    <?php /*<li class="l_m_category_li">
      <img width="5" hspace="3" height="5" alt="" src="images/design/box/arrow_2.gif" class="middle" >
      <a href="<?php echo tep_href_link('domain.php');?>">相互リンク</a>
    </li>*/ ?>
  </ul>
</div>
<!-- information_eof //-->
