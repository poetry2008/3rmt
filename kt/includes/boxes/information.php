<?php
/*
  $Id$
*/
?>
<!-- information //-->
<div class="reviews_box">
  <div class="menu_top_reviews">
  <img src="images/menu_ico.gif" alt="" align="top">&nbsp;INFORMATION 
  </div>
  <ul class="l_m_category_ul_infomation">
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
    echo '    <li class="l_m_category_li_infomation">' . "\n";
    echo '      <a href="' .  info_tep_href_link($result['romaji']) . '">' . $result['heading_title'] . '</a>' . "\n";
    echo '    </li>' . "\n";
  } 
// Extra Pages ADDED END
?>
    <li class="l_m_category_li_infomation"><a href="<?php echo tep_href_link(FILENAME_CONTACT_US);?>"><?php echo BOX_INFORMATION_CONTACT;?></a></li> 
  </ul>
  <div class="reviews_tom"><img height="14" width="170" alt="" src="images/design/box/box_bottom_bg_01.gif"></div>
</div>
<!-- information_eof //-->
