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
              <td onclick="toggle_lan('col4');" onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style="">&nbsp;<span><?php echo tep_image(DIR_WS_MENU_ICON . 'icon_customer.gif'); ?></span><span><a class="menuBoxHeading_Link" href="javascript:void(0);"><?php echo BOX_HEADING_CUSTOMERS;?></a></span> 
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
    if (!check_whether_is_limited(FILENAME_CUSTOMERS)) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_CUSTOMERS){
        echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_customer.gif').'</span><span>' . BOX_CUSTOMERS_CUSTOMERS . '</span></div>';
      }else{
        echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_customer.gif').'</span><span>' . BOX_CUSTOMERS_CUSTOMERS . '</span></div>';
      }
    }
    if (!check_whether_is_limited(FILENAME_ORDERS)) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_ORDERS){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_ORDERS, '', 'NONSSL').'\';"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_orders.gif').'</span><span>' . BOX_CUSTOMERS_ORDERS . '</span></div>';
      }else{
        echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_ORDERS, '', 'NONSSL').'\';"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_orders.gif').'</span><span>' . BOX_CUSTOMERS_ORDERS . '</span></div>';
      }
    }
    if (!check_whether_is_limited('telecom_unknow.php')) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'telecom_unknow.php'){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('telecom_unknow.php').'\';"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_payment_settings.gif').'</span><span>'.FILENAME_TELECOM_UNKNOW_TEXT.'</span></div>';
      }else{
        echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link('telecom_unknow.php').'\';"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_payment_settings.gif').'</span><span>'.FILENAME_TELECOM_UNKNOW_TEXT.'</span></div>';
      }
    }
    if (!check_whether_is_limited(FILENAME_RESET_PWD)) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_RESET_PWD){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_RESET_PWD).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_password_reset.gif').'</span><span>'.FILENAME_FILENAME_RESET_PWD_TEXT.'</span></div>';
      }else{
        echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_RESET_PWD).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_password_reset.gif').'</span><span>'.FILENAME_FILENAME_RESET_PWD_TEXT.'</span></div>';
      }
    }
    if (!check_whether_is_limited('bill_templates.php')) {
      if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'bill_templates.php'){
        echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('bill_templates.php').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_template.gif').'</span><span>'.FILENAME_BILL_TEMPLATES_TEXT.'</span></div>'; 
      }else{ 
        echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link('bill_templates.php').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_template.gif').  '</span><span>'.FILENAME_BILL_TEMPLATES_TEXT.'</span></div>'; 
      }
    }
    ?> 
      </td> 
    </tr>
            </table>
            </div>
           </td>
          </tr>
<!-- customers_eof -->
