<?php
/*
  $Id$
*/
?>
<!-- modules -->
          <tr>
            <td>
            <table cellspacing="0" cellpadding="2" border="0" width="150"> 
              <tr>
              <td onclick="toggle_lan('col3');" onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style=""> &nbsp;
              <span><?php echo tep_image(DIR_WS_MENU_ICON . 'icon_module.gif');?></span><span><a class="menuBoxHeading_Link" href="javascript:void(0);"><?php echo BOX_HEADING_MODULES;?></a></span>&nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if (in_array('col3', $l_select_box_arr)) {
            ?>
            <div id="col3" style="display:block"> 
            <?php
            } else {
            ?>
            <div id="col3" style="display:none"> 
            <?php
            }
            ?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
<?php 
  if(str_replace('/admin/','',$_SERVER['PHP_SELF']).'?set='.$_GET['set'] == FILENAME_MODULES.'?set=payment'){
  echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_MODULES, 'set=payment', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_payment.gif').'</span><span>' . BOX_MODULES_PAYMENT . '</span></div>';
  }else{
  echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_MODULES, 'set=payment', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_payment.gif').'</span><span>' . BOX_MODULES_PAYMENT . '</span></div>';
  }
  if(str_replace('/admin/','',$_SERVER['PHP_SELF']).'?set='.$_GET['set'] == FILENAME_MODULES. '?set=order_total'){
  echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_MODULES, 'set=order_total', 'NONSSL').'\';"><span>' . tep_image(DIR_WS_MENU_ICON .  'icon_calculation.gif').'</span><span>' . BOX_MODULES_ORDER_TOTAL . '</span></div>';
  }else{
  echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_MODULES, 'set=order_total', 'NONSSL').'\';"><span>' . tep_image(DIR_WS_MENU_ICON .  'icon_calculation.gif').'</span><span>' . BOX_MODULES_ORDER_TOTAL . '</span></div>';
  }
  if(str_replace('/admin/','',$_SERVER['PHP_SELF']).'?set='.$_GET['set'] == FILENAME_MODULES.'?set=metaseo'){
  echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_MODULES, 'set=metaseo', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON . 'icon_seo.gif').  '</span><span>' .  BOX_MODULES_METASEO .  '</span></div>';
  }else{
  echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_MODULES, 'set=metaseo', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON . 'icon_seo.gif').  '</span><span>' .  BOX_MODULES_METASEO .  '</span></div>'; 
  } 
  ?>
                </td>
              </tr>
            </table>
            </div>
            </td>
          </tr>
<!-- modules_eof -->
