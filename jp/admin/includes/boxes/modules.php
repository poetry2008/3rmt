<?php
/*
  $Id$
*/
?>
<!-- modules -->
          <tr>
            <td>
<?php
  /* 
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_MODULES,
                     'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=modules'));

  if ($selected_box == 'modules') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_MODULES, 'set=payment', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_MODULES_PAYMENT . '</a><br>' .
                                   //'<a href="' . tep_href_link(FILENAME_MODULES, 'set=shipping', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_MODULES_SHIPPING . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_MODULES, 'set=ordertotal', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_MODULES_ORDER_TOTAL . '</a><br>'.
                                   '<a href="' . tep_href_link(FILENAME_MODULES, 'set=metaseo', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_MODULES_METASEO . '</a><br>'
                                   );
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
  */
?>
            <table cellspacing="0" cellpadding="2" border="0" width="150"> 
              <tr>
              <td onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style=""> &nbsp; <?php 
              echo tep_image(DIR_WS_MENU_ICON . 'icon_module.gif');?> <a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col3');"><?php echo BOX_HEADING_MODULES;?></a>&nbsp; 
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
  echo '<div class="sidebarselected"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_payment.gif').'</span><span><a href="' . tep_href_link(FILENAME_MODULES, 'set=payment', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_MODULES_PAYMENT . '</a></span></div>';
  }else{
  echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_payment.gif').'</span><span><a href="' . tep_href_link(FILENAME_MODULES, 'set=payment', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_MODULES_PAYMENT . '</a></span></div>';
  }
  if(str_replace('/admin/','',$_SERVER['PHP_SELF']).'?set='.$_GET['set'] == FILENAME_MODULES. '?set=order_total'){
  echo '<div class="sidebarselected"><span>' . tep_image(DIR_WS_MENU_ICON .  'icon_calculation.gif').'</span><span><a href="' .  tep_href_link(FILENAME_MODULES, 'set=order_total', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_MODULES_ORDER_TOTAL . '</a></span></div>';
  }else{
  echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' . tep_image(DIR_WS_MENU_ICON .  'icon_calculation.gif').'</span><span><a href="' .  tep_href_link(FILENAME_MODULES, 'set=order_total', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_MODULES_ORDER_TOTAL . '</a></span></div>';
  }
  if(str_replace('/admin/','',$_SERVER['PHP_SELF']).'?set='.$_GET['set'] == FILENAME_MODULES.'?set=metaseo'){
  echo '<div class="sidebarselected"><span>'.tep_image(DIR_WS_MENU_ICON . 'icon_seo.gif').  '</span><span><a href="' . tep_href_link(FILENAME_MODULES, 'set=metaseo', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_MODULES_METASEO .  '</a></span></div>';
  }else{
  echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>'.tep_image(DIR_WS_MENU_ICON . 'icon_seo.gif').  '</span><span><a href="' . tep_href_link(FILENAME_MODULES, 'set=metaseo', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_MODULES_METASEO .  '</a></span></div>'; 
  } 
  ?>
                </td>
              </tr>
            </table>
            </div>
            </td>
          </tr>
<!-- modules_eof -->
