<?php
/*
  $Id$

*/
?>
<!-- localization -->
          <tr>
            <td>
<?php
  /* 
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_LOCALIZATION,
                     'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=localization'));

  if ($selected_box == 'localization') {
    $contents[] = array('text'  => 
                                   //'<a href="' . tep_href_link(FILENAME_CURRENCIES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_LOCALIZATION_CURRENCIES . '</a><br>' .
                                   //'<a href="' . tep_href_link(FILENAME_LANGUAGES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_LOCALIZATION_LANGUAGES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_ORDERS_STATUS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_LOCALIZATION_ORDERS_STATUS . '</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
  */
?>
            <table cellspacing="0" cellpadding="2" border="0" width="150"> 
              <tr>
              <td onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style="">
              &nbsp;
               <span><?php echo tep_image(DIR_WS_MENU_ICON . 'icon_localize.gif');?></span><span><a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col5');"><?php echo BOX_HEADING_LOCALIZATION;?></a></span>
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
                 if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_ORDERS_STATUS){
                 echo '<div class="sidebarselected"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_order_status.gif').'</span><span><a href="' .  tep_href_link(FILENAME_ORDERS_STATUS, '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_LOCALIZATION_ORDERS_STATUS .  '</a></span></div>';
                 }else{ 
                 echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_order_status.gif').'</span><span><a href="' .  tep_href_link(FILENAME_ORDERS_STATUS, '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_LOCALIZATION_ORDERS_STATUS .  '</a></span></div>';
                 }?>
                 <?php 
                 if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_PREORDERS_STATUS){
                 echo '<div class="sidebarselected"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_preorder_status.gif').'</span><span><a href="' .  tep_href_link(FILENAME_PREORDERS_STATUS, '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_LOCALIZATION_PREORDERS_STATUS . '</a></span></div>';
                 }else{ 
                 echo'<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_preorder_status.gif').'</span><span><a href="' .  tep_href_link(FILENAME_PREORDERS_STATUS, '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_LOCALIZATION_PREORDERS_STATUS . '</a></span></div>';
                 }?>
                </td>
              </tr>
             </table>
            </div>
           </td>
          </tr>
<!-- localization_eof -->
