<?php
/*
  $Id$

*/
?>
<!-- localization -->
          <tr>
            <td>
            <table cellspacing="0" cellpadding="2" border="0" width="150"> 
              <tr>
              <td onclick="toggle_lan('col5');" onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style="">
              &nbsp;
               <span><?php echo tep_image(DIR_WS_MENU_ICON . 'icon_localize.gif');?></span><span><a class="menuBoxHeading_Link" href="javascript:void(0);"><?php echo BOX_HEADING_LOCALIZATION;?></a></span>
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if (in_array('col5', $l_select_box_arr)) {
            ?>
            <div id="col5" style="display:block"> 
            <?php
            } else {
            ?>
            <div id="col5" style="display:none"> 
            <?php
            }
            ?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
                 <?php 
                 if (!check_whether_is_limited(FILENAME_ORDERS_STATUS)) {
                   if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_ORDERS_STATUS){
                     echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_ORDERS_STATUS, '', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_order_status.gif').'</span><span>' .  BOX_LOCALIZATION_ORDERS_STATUS .  '</span></div>';
                   }else{ 
                     echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_ORDERS_STATUS, '', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_order_status.gif').'</span><span>' .  BOX_LOCALIZATION_ORDERS_STATUS .  '</span></div>';
                   }
                 }
                 if (!check_whether_is_limited(FILENAME_PREORDERS_STATUS)) {
                   if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_PREORDERS_STATUS){
                     echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_PREORDERS_STATUS, '', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_preorder_status.gif').'</span><span>' .  BOX_LOCALIZATION_PREORDERS_STATUS . '</span></div>';
                   }else{ 
                     echo'<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_PREORDERS_STATUS, '', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_preorder_status.gif').'</span><span>' .  BOX_LOCALIZATION_PREORDERS_STATUS . '</span></div>';
                   }
                 }
                 ?>
                </td>
              </tr>
             </table>
            </div>
           </td>
          </tr>
<!-- localization_eof -->
