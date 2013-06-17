<?php
/*
  $Id$
*/
?>
<!-- reports -->
          <tr>
            <td>
            <table cellspacing="0" cellpadding="2" border="0" width="150"> 
             <tr>
              <td onclick="toggle_lan('col6');" onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style="">&nbsp;<span><?php echo tep_image(DIR_WS_MENU_ICON . 'icon_report.gif');?></span><span><a class="menuBoxHeading_Link" href="javascript:void(0);"><?php echo BOX_HEADING_REPORTS;?></a></span>&nbsp; 
              </td>
             </tr>
            </table> 
            <?php
            if (in_array('col6', $l_select_box_arr)) {
            ?>
            <div id="col6" style="display:block"> 
            <?php
            } else {
            ?>
            <div id="col6" style="display:none"> 
            <?php
            }
            ?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
    <?php 
    if (!check_whether_is_limited('cal_info.php')) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'cal_info.php'){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('cal_info.php', '', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_statistics.gif').'</span><span>' .  BOX_CAL_SITES_INFO_TEXT . '</span></div>';
      }else{
        echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link('cal_info.php', '', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_statistics.gif').'</span><span>' .  BOX_CAL_SITES_INFO_TEXT . '</span></div>';
      }
    }
    if (!check_whether_is_limited(FILENAME_STATS_SALES_REPORT)) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_STATS_SALES_REPORT){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_STATS_SALES_REPORT, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_sales.gif').'</span><span>' . BOX_REPORTS_SALES_REPORT . '</span></div>';
      }else{
        echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_STATS_SALES_REPORT, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_sales.gif').'</span><span>' . BOX_REPORTS_SALES_REPORT . '</span></div>';
      }
    }
    if (!check_whether_is_limited(FILENAME_ASSETS)) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_ASSETS){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_ASSETS, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_property.gif').'</span><span>' .  BOX_REPORTS_ASSETS . '</span></div>';
      }else{ 
        echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_ASSETS, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_property.gif').'</span><span>' .  BOX_REPORTS_ASSETS . '</span></div>';
      }
    }
    if (!check_whether_is_limited(FILENAME_STATS_PRODUCTS_VIEWED)) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_STATS_PRODUCTS_VIEWED){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_STATS_PRODUCTS_VIEWED, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_access.gif' ).  '</span><span>' .  BOX_REPORTS_PRODUCTS_VIEWED .  '</span></div>';
      }else{
        echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_STATS_PRODUCTS_VIEWED, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_access.gif' ).  '</span><span>' .  BOX_REPORTS_PRODUCTS_VIEWED .  '</span></div>';
      }
    }
    if (!check_whether_is_limited(FILENAME_STATS_PRODUCTS_PURCHASED)) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_STATS_PRODUCTS_PURCHASED){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_sale_count.gif').'</span><span>' .  BOX_REPORTS_PRODUCTS_PURCHASED .  '</span></div>';
      }else{
        echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_sale_count.gif').'</span><span>' .  BOX_REPORTS_PRODUCTS_PURCHASED .  '</span></div>';
      }
    }
    if (!check_whether_is_limited(FILENAME_STATS_CUSTOMERS)) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_STATS_CUSTOMERS){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_STATS_CUSTOMERS, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_sale_ranking.gif').'</span><span>' . BOX_REPORTS_ORDERS_TOTAL .  '</span></div>';
      }else{
        echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_STATS_CUSTOMERS, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_sale_ranking.gif').'</span><span>' . BOX_REPORTS_ORDERS_TOTAL .  '</span></div>';
      }
    }
    if (!check_whether_is_limited('referer.php')) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'referer.php'){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('referer.php').'\';"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_access_ranking.gif').'</span><span>'.FILENAME_REFERER_TEXT.'</span></div>';
      }else{
        echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link('referer.php').'\';"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_access_ranking.gif').'</span><span>'.FILENAME_REFERER_TEXT.'</span></div>';
      }
    }
    if (!check_whether_is_limited('keywords.php')) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'keywords.php'){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('keywords.php').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_keyword_ranking.gif').'</span><span>'.FILENAME_KEYWORDS_TEXT.'</span></div>';
      }else{
        echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link('keywords.php').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_keyword_ranking.gif').'</span><span>'.FILENAME_KEYWORDS_TEXT.'</span></div>';
      }
    }
    if (!check_whether_is_limited(FILENAME_NEW_CUSTOMERS)) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_NEW_CUSTOMERS){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_NEW_CUSTOMERS, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_new_customers.gif').'</span><span>' . BOX_REPORTS_NEW_CUSTOMERS . '</span></div>';
      }else{
        echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_NEW_CUSTOMERS, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_new_customers.gif').'</span><span>' . BOX_REPORTS_NEW_CUSTOMERS . '</span></div>';
      }
    }
    ?>
                </td>
              </tr>
            </table> 
            </div> 
            </td>
          </tr>
<!-- reports_eof -->
