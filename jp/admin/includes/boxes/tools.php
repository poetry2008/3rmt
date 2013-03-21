<?php
/*
  $Id$
*/
?>
<!-- tools -->
          <tr>
            <td>
            <table cellspacing="0" cellpadding="2" border="0" width="150"> 
              <tr>
              <td onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style="">&nbsp;
              <span><?php echo tep_image(DIR_WS_MENU_ICON . 'icon_tool.gif');?></span><span><a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col7');"><?php echo BOX_HEADING_TOOLS;?></a></span>&nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if (in_array('col7', $l_select_box_arr)) {
            ?>
            <div id="col7" style="display:block"> 
            <?php
            } else {
            ?>
            <div id="col7" style="display:none"> 
            <?php
            }
            ?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent"><?php 
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_BANNER_MANAGER){
     echo ' <div class="sidebarselected"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_banner.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_BANNER_MANAGER) .  '">' . BOX_TOOLS_BANNER_MANAGER .  '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_banner.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_BANNER_MANAGER) .  '">' . BOX_TOOLS_BANNER_MANAGER .  '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'calendar.php'){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_business_day.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link('calendar.php') .  '">'.FILENAME_BANK_CL_TEXT.'</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_business_day.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link('calendar.php') .  '">'.FILENAME_BANK_CL_TEXT.'</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_CACHE){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_cache_control.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link(FILENAME_CACHE) . '">' .  BOX_TOOLS_CACHE .  '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_cache_control.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link(FILENAME_CACHE) . '">' .  BOX_TOOLS_CACHE .  '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_PW_MANAGER){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_identity.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_PW_MANAGER) . '">' .FILENAME_PW_MANAGER_TEXT.  '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_identity.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_PW_MANAGER) . '">' .FILENAME_PW_MANAGER_TEXT.  '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_DEFINE_LANGUAGE){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_language_file.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE) .  '">' .  BOX_TOOLS_DEFINE_LANGUAGE . '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_language_file.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link(FILENAME_DEFINE_LANGUAGE) .  '">' .  BOX_TOOLS_DEFINE_LANGUAGE . '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_MAIL){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_email.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_MAIL) . '">' .  BOX_TOOLS_MAIL . '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_email.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_MAIL) . '">' .  BOX_TOOLS_MAIL . '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_NEWSLETTERS){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_magazine.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_NEWSLETTERS) .  '">' . BOX_TOOLS_NEWSLETTER_MANAGER .  '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_magazine.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_NEWSLETTERS) .  '">' . BOX_TOOLS_NEWSLETTER_MANAGER .  '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_POINT_EMAIL){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_points.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_POINT_EMAIL) .  '">' . BOX_TOOLS_POINT_EMAIL_MANAGER .  '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_points.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_POINT_EMAIL) .  '">' . BOX_TOOLS_POINT_EMAIL_MANAGER .  '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_WHOS_ONLINE){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_online_user.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link(FILENAME_WHOS_ONLINE) .  '">' . BOX_TOOLS_WHOS_ONLINE . '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span class="menuBoxContent_image">' .  tep_image(DIR_WS_MENU_ICON . 'icon_online_user.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link(FILENAME_WHOS_ONLINE) .  '">' . BOX_TOOLS_WHOS_ONLINE . '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_COMPUTERS){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_button.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_COMPUTERS) .  '">'.FILENAME_COMPUTERS_TEXT.'</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_button.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_COMPUTERS) .  '">'.FILENAME_COMPUTERS_TEXT.'</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_MARKS){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_mark.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_MARKS) . '">' .  BOX_TOOLS_MARKS_MANAGER .  '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_mark.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_MARKS) . '">' .  BOX_TOOLS_MARKS_MANAGER .  '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_LATEST_NEWS){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_new_info.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_LATEST_NEWS) .  '">' . BOX_TOOLS_LATEST_NEWS .  '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_new_info.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_LATEST_NEWS) .  '">' . BOX_TOOLS_LATEST_NEWS .  '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_CONTENTS){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_contents.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_CONTENTS) . '">' .  BOX_TOOLS_CONTENTS .  '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_contents.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_CONTENTS) . '">' .  BOX_TOOLS_CONTENTS .  '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_PRESENT){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_present.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_PRESENT) . '" class="menuBoxContent_Link">' .  BOX_TOOLS_PRESENT . '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_present.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_PRESENT) . '" class="menuBoxContent_Link">' .  BOX_TOOLS_PRESENT . '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_CAMPAIGN){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_campaign_code.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_CAMPAIGN) . '" class="menuBoxContent_Link">' . BOX_TOOLS_CAMPAIGN . '</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_campaign_code.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_CAMPAIGN) . '" class="menuBoxContent_Link">' . BOX_TOOLS_CAMPAIGN . '</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_FAQ){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_faq.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_FAQ) . '">' .  BOX_TOOLS_FAQ . '</a></span></div>'.'<hr size="1">';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_faq.gif').'</span><span><a class="menuBoxContent_Link" href="' .  tep_href_link(FILENAME_FAQ) . '">' .  BOX_TOOLS_FAQ . '</a></span></div>'.'<hr size="1">';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'mag_up.php'){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_magazine_register.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link('mag_up.php', '', 'NONSSL') .  '">'.FILENAME_MAG_UP_TEXT.'</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_magazine_register.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link('mag_up.php', '', 'NONSSL') .  '">'.FILENAME_MAG_UP_TEXT.'</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'mag_dl.php'){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_magazine_data.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link('mag_dl.php', '', 'NONSSL') .  '">'.FILENAME_MAG_DL_TEXT.'</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_magazine_data.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link('mag_dl.php', '', 'NONSSL') .  '">'.FILENAME_MAG_DL_TEXT.'</a></span></div>';
     }
     if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_ORDERS_DOWNLOAD){
     echo ' <div class="sidebarselected"><span>'.  tep_image(DIR_WS_MENU_ICON . 'icon_date_export.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link(FILENAME_ORDERS_DOWNLOAD, '', 'NONSSL') .'">'.FILENAME_ORDER_DOWNLOAD.'</a></span></div>';
     }else{
     echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>'.  tep_image(DIR_WS_MENU_ICON . 'icon_date_export.gif').'</span><span><a class="menuBoxContent_Link" href="' . tep_href_link(FILENAME_ORDERS_DOWNLOAD, '', 'NONSSL') .'">'.FILENAME_ORDER_DOWNLOAD.'</a></span></div>';
     }
              ?> 
                </td>
              </tr>
             </table> 
            </div> 
           </td>
          </tr>
<!-- tools_eof -->
