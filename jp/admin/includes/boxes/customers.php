<?php
/*
  $Id$
*/
?>
<!-- customers -->
          <tr>
            <td>
            <table cellspacing="0" cellpadding="2" border="0" width="150"> 
              <tr>
              <td onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style="">&nbsp;<span><?php echo tep_image(DIR_WS_MENU_ICON . 'icon_customer.gif'); ?></span><span><a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col4');"><?php echo BOX_HEADING_CUSTOMERS;?></a></span> 
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if (in_array('col4', $l_select_box_arr)) {
            ?>
            <div id="col4" style="display:block"> 
            <?php
            } else {
            ?>
            <div id="col4" style="display:none"> 
            <?php
            }
            ?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
    <?php
    if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_CUSTOMERS){
    echo '<div class="sidebarselected"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_customer.gif').'</span><span><a href="' .  tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CUSTOMERS_CUSTOMERS . '</a></span></div>';
    }else{
    echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_customer.gif').'</span><span><a href="' .  tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CUSTOMERS_CUSTOMERS . '</a></span></div>';
    }
    if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_ORDERS){
    echo ' <div class="sidebarselected" ><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_orders.gif').'</span><span><a href="' .  tep_href_link(FILENAME_ORDERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CUSTOMERS_ORDERS . '</a></span></div>';
    }else{
    echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_orders.gif').'</span><span><a href="' .  tep_href_link(FILENAME_ORDERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CUSTOMERS_ORDERS . '</a></span></div>';
    }
    if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'telecom_unknow.php'){
    echo ' <div class="sidebarselected"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_payment_settings.gif').'</span><span><a href="' .  tep_href_link('telecom_unknow.php') . '" class="menuBoxContent_Link">'.FILENAME_TELECOM_UNKNOW_TEXT.'</a></span></div>';
    }else{
    echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_payment_settings.gif').'</span><span><a href="' .  tep_href_link('telecom_unknow.php') . '" class="menuBoxContent_Link">'.FILENAME_TELECOM_UNKNOW_TEXT.'</a></span></div>';
    }
    if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_RESET_PWD){
    echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_password_reset.gif').'</span><span><a href="' .  tep_href_link(FILENAME_RESET_PWD) . '" class="menuBoxContent_Link">'.FILENAME_FILENAME_RESET_PWD_TEXT.'</a></span></div>';
    }else{
    echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_password_reset.gif').'</span><span><a href="' .  tep_href_link(FILENAME_RESET_PWD) . '" class="menuBoxContent_Link">'.FILENAME_FILENAME_RESET_PWD_TEXT.'</a></span></div>';
    }
    if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'bill_templates.php'){
    echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_template.gif').	'</span><span><a href="' . tep_href_link('bill_templates.php') . '" class="menuBoxContent_Link">'.FILENAME_BILL_TEMPLATES_TEXT.'</a></span></div>'; }else{ echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_template.gif').  '</span><span><a href="' . tep_href_link('bill_templates.php') . '" class="menuBoxContent_Link">'.FILENAME_BILL_TEMPLATES_TEXT.'</a></span></div>'; }?> 
      </td> 
    </tr>
            </table>
            </div>
           </td>
          </tr>
<!-- customers_eof -->
