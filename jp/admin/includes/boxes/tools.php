<?php
/*
  $Id$
*/
?>
<!-- tools -->
          <tr>
            <td>
            <table cellspacing="0" cellpadding="2" border="0" class="leftTitle"> 
              <tr>
              <td onclick="toggle_lan('col7');" onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style="">&nbsp;
              <span><?php echo tep_image(DIR_WS_MENU_ICON . 'icon_tool.gif');?></span><span><a class="menuBoxHeading_Link" href="javascript:void(0);"><?php echo BOX_HEADING_TOOLS;?></a></span>&nbsp; 
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
                <td class="menuBoxContent">
     <?php 
     if (!check_whether_is_limited(FILENAME_SEARCH)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_SEARCH){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_SEARCH).'\';"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_customer.gif').'</span><span>' . BOX_TOOLS_SEARCH .  '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_SEARCH).'\';"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_customer.gif').'</span><span>' . BOX_TOOLS_SEARCH .  '</span></div>';
       }
     }
     if (!check_whether_is_limited('messages.php')) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'messages.php'){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('messages.php').'\';"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_messages.png').'</span><span>' . MESSAGES_PAGE_LINK_NAME .  '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link('messages.php').'\';"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_messages.png').'</span><span>' . MESSAGES_PAGE_LINK_NAME .  '</span></div>';
       }
     }
     if (!check_whether_is_limited('bulletin_board.php')) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'bulletin_board.php'){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('bulletin_board.php').'\';"><span>'.  tep_image('images/icons/left_bbs.png').'</span><span>' .HEADER_TEXT_BULLETIN .  '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link('bulletin_board.php').'\';"><span>'.  tep_image('images/icons/left_bbs.png').'</span><span>' . HEADER_TEXT_BULLETIN .  '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_BANNER_MANAGER)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_BANNER_MANAGER){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_BANNER_MANAGER).'\';"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_banner.gif').'</span><span>' . BOX_TOOLS_BANNER_MANAGER .  '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_BANNER_MANAGER).'\';"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_banner.gif').'</span><span>' . BOX_TOOLS_BANNER_MANAGER .  '</span></div>';
       }
     }
     if (!check_whether_is_limited('calendar.php')) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'calendar.php'){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('calendar.php').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_business_day.gif').'</span><span>'.FILENAME_BANK_CL_TEXT.'</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link('calendar.php').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_business_day.gif').'</span><span>'.FILENAME_BANK_CL_TEXT.'</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_CACHE)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_CACHE){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_CACHE).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_cache_control.gif').'</span><span>' .  BOX_TOOLS_CACHE .  '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_CACHE).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_cache_control.gif').'</span><span>' .  BOX_TOOLS_CACHE .  '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_PW_MANAGER)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_PW_MANAGER){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_PW_MANAGER).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_identity.gif').'</span><span>' .FILENAME_PW_MANAGER_TEXT.  '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_PW_MANAGER).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_identity.gif').'</span><span>' .FILENAME_PW_MANAGER_TEXT.  '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_CONFIGURATION_META)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_CONFIGURATION_META){ 
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_CONFIGURATION_META, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_seo.gif').'</span><span>'.BOX_MODULES_METASEO.'</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_CONFIGURATION_META, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_seo.gif').'</span><span>'.BOX_MODULES_METASEO.'</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_DEFINE_LANGUAGE)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_DEFINE_LANGUAGE){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_DEFINE_LANGUAGE).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_language_file.gif').'</span><span>' .  BOX_TOOLS_DEFINE_LANGUAGE . '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_DEFINE_LANGUAGE).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_language_file.gif').'</span><span>' .  BOX_TOOLS_DEFINE_LANGUAGE . '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_MAIL)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_MAIL){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_MAIL).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_email.gif').'</span><span>' .  BOX_TOOLS_MAIL . '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_MAIL).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_email.gif').'</span><span>' .  BOX_TOOLS_MAIL . '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_NEWSLETTERS)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_NEWSLETTERS){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_NEWSLETTERS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_magazine.gif').'</span><span>' . BOX_TOOLS_NEWSLETTER_MANAGER .  '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_NEWSLETTERS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_magazine.gif').'</span><span>' . BOX_TOOLS_NEWSLETTER_MANAGER .  '</span></div>';
       }
     }
     //邮件模板管理
     if (!check_whether_is_limited(FILENAME_MAIL_TEMPLATES)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_MAIL_TEMPLATES){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_MAIL_TEMPLATES).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_mail_templates.gif').'</span><span>' . BOX_TOOLS_MAIL_TEMPLATES .  '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_MAIL_TEMPLATES).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_mail_templates.gif').'</span><span>' . BOX_TOOLS_MAIL_TEMPLATES .  '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_POINT_EMAIL)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_POINT_EMAIL){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_POINT_EMAIL).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_points.gif').'</span><span>' . BOX_TOOLS_POINT_EMAIL_MANAGER .  '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_POINT_EMAIL).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_points.gif').'</span><span>' . BOX_TOOLS_POINT_EMAIL_MANAGER .  '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_WHOS_ONLINE)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_WHOS_ONLINE){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_WHOS_ONLINE).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_online_user.gif').'</span><span>' . BOX_TOOLS_WHOS_ONLINE . '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_WHOS_ONLINE).'\';"><span class="menuBoxContent_image">' .  tep_image(DIR_WS_MENU_ICON . 'icon_online_user.gif').'</span><span>' . BOX_TOOLS_WHOS_ONLINE . '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_BUTTONS)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_BUTTONS){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_BUTTONS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_button.gif').'</span><span>'.FILENAME_BUTTONS_TEXT.'</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_BUTTONS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_button.gif').'</span><span>'.FILENAME_BUTTONS_TEXT.'</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_MARKS)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_MARKS){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_MARKS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_mark.gif').'</span><span>' .  BOX_TOOLS_MARKS_MANAGER .  '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_MARKS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_mark.gif').'</span><span>' .  BOX_TOOLS_MARKS_MANAGER .  '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_NEWS)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_NEWS){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_NEWS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_new_info.gif').'</span><span>' . BOX_TOOLS_LATEST_NEWS .  '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_NEWS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_new_info.gif').'</span><span>' . BOX_TOOLS_LATEST_NEWS .  '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_CONTENTS)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_CONTENTS){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_CONTENTS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_contents.gif').'</span><span>' .  BOX_TOOLS_CONTENTS .  '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_CONTENTS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_contents.gif').'</span><span>' .  BOX_TOOLS_CONTENTS .  '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_PRESENT)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_PRESENT){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_PRESENT).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_present.gif').'</span><span>' .  BOX_TOOLS_PRESENT . '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_PRESENT).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_present.gif').'</span><span>' .  BOX_TOOLS_PRESENT . '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_CAMPAIGN)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_CAMPAIGN){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_CAMPAIGN).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_campaign_code.gif').'</span><span>' . BOX_TOOLS_CAMPAIGN . '</span></div>';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_CAMPAIGN).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_campaign_code.gif').'</span><span>' . BOX_TOOLS_CAMPAIGN . '</span></div>';
       }
     }
     if (!check_whether_is_limited(FILENAME_FAQ)) {
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_FAQ){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_FAQ).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_faq.gif').'</span><span>' .  BOX_TOOLS_FAQ . '</span></div>'.'<hr size="1">';
       }else{
         echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_FAQ).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_faq.gif').'</span><span>' .  BOX_TOOLS_FAQ . '</span></div>';
       }
     }
    ?> 
                </td>
              </tr>
             </table> 
            </div> 
           </td>
          </tr>
<!-- tools_eof -->
