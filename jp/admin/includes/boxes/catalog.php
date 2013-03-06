<?php
/*
  $Id$
*/
?>
<!-- catalog -->
          <tr>
            <td>
            <table cellspacing="0" cellpadding="2" border="0" width="150"> 
              <tr>
              <td onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style="">&nbsp;<span><?php echo tep_image(DIR_WS_MENU_ICON . 'icon_catalog.gif'); ?></span><span><a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col2');"><?php echo BOX_HEADING_CATALOG;?></a></span>&nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if (in_array('col2', $l_select_box_arr)) {
            ?>
            <div id="col2" style="display:block"> 
            <?php
            } else {
            ?>
            <div id="col2" style="display:none"> 
            <?php
            }
            ?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent"><?php 
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_CATEGORIES){
       echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_category.gif').'</span><span><a href="' .  tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_CATEGORIES_PRODUCTS .  '</a></span></div>';
       }else{
       echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style=""><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_category.gif').'</span><span><a href="' .  tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_CATEGORIES_PRODUCTS .  '</a></span></div>';
       }
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_OPTION){
       echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_option_register.gif') .'</span><span><a href="' . tep_href_link(FILENAME_OPTION, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES .  '</a></span></div>'; 
       }else{
       echo '<div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style=""><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_option_register.gif') .'</span><span><a href="' . tep_href_link(FILENAME_OPTION, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES .  '</a></span></div>'; 
       }
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_TAGS){
       echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_tags.gif') . '</span><span><a href="' .  tep_href_link(FILENAME_TAGS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_PRODUCTS_TAGS . '</a></span></div>';
       }else{
       echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style=""><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_tags.gif') . '</span><span><a href="' .  tep_href_link(FILENAME_TAGS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_PRODUCTS_TAGS . '</a></span></div>';
       }
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'products_tags.php'){
       echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_products_tags.gif').'</span><span><a href="' . tep_href_link('products_tags.php', '', 'NONSSL') . '" class="menuBoxContent_Link">'.FILENAME_PRODUCTS_TAGS_TEXT.'</a></span></div>';
       }else{
       echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style=""><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_products_tags.gif').'</span><span><a href="' . tep_href_link('products_tags.php', '', 'NONSSL') . '" class="menuBoxContent_Link">'.FILENAME_PRODUCTS_TAGS_TEXT.'</a></span></div>';
       }
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_MANUFACTURERS){
       echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_maker.gif').'</span><span><a href="' .  tep_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_MANUFACTURERS .  '</a></span></div>';
       }else{
       echo '<div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style=""><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_maker.gif').'</span><span><a href="' .  tep_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_MANUFACTURERS .  '</a></span></div>';
       }
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_REVIEWS){
       echo ' <div class="sidebarselected"><span>' .  (isset($_color_l)?$_color_l:'') . tep_image(DIR_WS_MENU_ICON .  'icon_review_set.gif').'</span><span><a href="' .  tep_href_link(FILENAME_REVIEWS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_REVIEWS . '</a></span></div>';
       }else{
       echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style=""><span>' .  (isset($_color_l)?$_color_l:'') . tep_image(DIR_WS_MENU_ICON .  'icon_review_set.gif').'</span><span><a href="' .  tep_href_link(FILENAME_REVIEWS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_REVIEWS . '</a></span></div>';
       }
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'cleate_oroshi.php'){
       echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_wholesaler.gif').'</span><span><a href="'.tep_href_link('cleate_oroshi.php', '', 'NONSSL').'" class="menuBoxContent_Link">'.FILENAME_CLEATE_OROSHI_TEXT.'</a></span></div>';
       }else{
       echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style=""><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_wholesaler.gif').'</span><span><a href="'.tep_href_link('cleate_oroshi.php', '', 'NONSSL').'" class="menuBoxContent_Link">'.FILENAME_CLEATE_OROSHI_TEXT.'</a></span></div>';
       }
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'cleate_dougyousya.php'){
       echo ' <div style="background-color:#FFD700"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_peer.gif').'</span><span><a href="'.tep_href_link('cleate_dougyousya.php', '', 'NONSSL').  '"class="menuBoxContent_Link">'.FILENAME_CLEATE_DOUGYOUSYA_TEXT.'</a></span></div>';
       }else{
       echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style=""><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_peer.gif').'</span><span><a href="'.tep_href_link('cleate_dougyousya.php', '', 'NONSSL').  '"class="menuBoxContent_Link">'.FILENAME_CLEATE_DOUGYOUSYA_TEXT.'</a></span></div>';
       }
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'address.php'){
       echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_address.gif').'</span><span><a href="'.tep_href_link('address.php','','NONSSL').'" class="menuBoxContent_Link">'.BOX_CREATE_ADDRESS.'</a></span></div>';
       }else{
       echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style=""><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_address.gif').'</span><span><a href="'.tep_href_link('address.php','','NONSSL').'" class="menuBoxContent_Link">'.BOX_CREATE_ADDRESS.'</a></span></div>';
       }
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'country_fee.php'){
       echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_charge.gif').'</span><span><a href="'.tep_href_link('country_fee.php','','NONSSL').'" class="menuBoxContent_Link">'.BOX_COUNTRY_FEE.'</a></span></div>';
       }else{
       echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style=""><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_charge.gif').'</span><span><a href="'.tep_href_link('country_fee.php','','NONSSL').'" class="menuBoxContent_Link">'.BOX_COUNTRY_FEE.'</a></span></div>';
       }
       if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'products_shipping_time.php'){
       echo ' <div class="sidebarselected"><span>' . tep_image(DIR_WS_MENU_ICON . 'icon_delivery_time.gif').'</span><span><a href="'.tep_href_link('products_shipping_time.php','','NONSSL').'" class="menuBoxContent_Link">'.BOX_SHIPPING_TIME.'</a></span></div>';
       }else{
       echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style=""><span>' . tep_image(DIR_WS_MENU_ICON . 'icon_delivery_time.gif').'</span><span><a href="'.tep_href_link('products_shipping_time.php','','NONSSL').'" class="menuBoxContent_Link">'.BOX_SHIPPING_TIME.'</a></span></div>';
       }
       ?>
              </td>
             </tr>
            </table> 
           </div> 
          </td>
         </tr>
<!-- catalog_eof -->
