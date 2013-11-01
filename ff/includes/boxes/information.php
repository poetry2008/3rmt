<?php
/*
  $Id$
*/
?>
<!-- information //-->
<div class="reviews_box">
  <div class="menu_top">
  <img src="images/menu_ico09.gif" alt="" align="top"><span>INFORMATION</span> 
  </div>
  <ul class="l_m_category_ul_infomation">
<?php

  $contents_page = tep_db_query("
      select * 
      from ".TABLE_INFORMATION_PAGE." 
      where status = 1 
        and site_id = ".SITE_ID." 
      order by sort_id 
  ");
  while ($result = tep_db_fetch_array($contents_page)){
    if($result['show_status'] != '1'){
    echo '    <li class="l_m_category_li_infomation">' . "\n";
    echo '      <a href="' .  info_tep_href_link($result['romaji']) . '">' . $result['heading_title'] . '</a>' . "\n";
    echo '    </li>' . "\n";
    }
  } 
// Extra Pages ADDED END
?>
    <li class="l_m_category_li_infomation"><a href="<?php echo tep_href_link(FILENAME_CONTACT_US,'','SSL');?>"><?php echo BOX_INFORMATION_CONTACT;?></a></li>
  </ul>
</div>
<!-- information_eof //-->
