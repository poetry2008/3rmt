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
              <td onclick="toggle_lan('col2');" onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style="">
              &nbsp;<span><?php echo tep_image(DIR_WS_MENU_ICON . 'icon_catalog.gif'); ?></span><span><a class="menuBoxHeading_Link" href="javascript:void(0);"><?php echo BOX_HEADING_CATALOG;?></a></span>
              &nbsp; 
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
                <td class="menuBoxContent">
       <?php 
       if (!check_whether_is_limited(FILENAME_CATEGORIES)) {
         if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_CATEGORIES){
           echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_category.gif').'</span><span>' . BOX_CATALOG_CATEGORIES_PRODUCTS .  '</span></div>';
         }else{
           echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style="" onclick="window.location.href=\''.tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_category.gif').'</span><span>' . BOX_CATALOG_CATEGORIES_PRODUCTS .  '</span></div>';
         }
       }
       if (!check_whether_is_limited(FILENAME_OPTION)) {
         if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_OPTION){
           echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_OPTION, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_option_register.gif') .'</span><span>' . BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES .  '</span></div>'; 
         }else{
           echo '<div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style="" onclick="window.location.href=\''.tep_href_link(FILENAME_OPTION, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_option_register.gif') .'</span><span>' . BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES .  '</span></div>'; 
         }
       }
       if (!check_whether_is_limited(FILENAME_TAGS)) {
         if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_TAGS){
           echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_TAGS, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_tags.gif') . '</span><span>' . BOX_CATALOG_PRODUCTS_TAGS . '</span></div>';
         }else{
           echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style="" onclick="window.location.href=\''.tep_href_link(FILENAME_TAGS, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_tags.gif') . '</span><span>' . BOX_CATALOG_PRODUCTS_TAGS . '</span></div>';
         } 
       } 
       if (!check_whether_is_limited(FILENAME_MANUFACTURERS)) {
         if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_MANUFACTURERS){
           echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_maker.gif').'</span><span>' . BOX_CATALOG_MANUFACTURERS .  '</span></div>';
         }else{
           echo '<div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style="" onclick="window.location.href=\''.tep_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_maker.gif').'</span><span>' . BOX_CATALOG_MANUFACTURERS .  '</span></div>';
         }
       }
       if (!check_whether_is_limited(FILENAME_REVIEWS)) {
         if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_REVIEWS){
           echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_REVIEWS, '', 'NONSSL').'\';"><span>' .  (isset($_color_l)?$_color_l:'') . tep_image(DIR_WS_MENU_ICON .  'icon_review_set.gif').'</span><span>' . BOX_CATALOG_REVIEWS . '</span></div>';
         }else{
           echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style="" onclick="window.location.href=\''.tep_href_link(FILENAME_REVIEWS, '', 'NONSSL').'\';"><span>' .  (isset($_color_l)?$_color_l:'') . tep_image(DIR_WS_MENU_ICON .  'icon_review_set.gif').'</span><span>' . BOX_CATALOG_REVIEWS . '</span></div>';
         }
       }
       if (!check_whether_is_limited('cleate_oroshi.php')) {
         if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'cleate_oroshi.php'){
           echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('cleate_oroshi.php', '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_wholesaler.gif').'</span><span>'.FILENAME_CLEATE_WHOLESALE_TEXT.'</span></div>';
         }else{
           echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style="" onclick="window.location.href=\''.tep_href_link('cleate_oroshi.php', '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_wholesaler.gif').'</span><span>'.FILENAME_CLEATE_WHOLESALE_TEXT.'</span></div>';
         }
       }
       if (!check_whether_is_limited('cleate_dougyousya.php')) {
         if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'cleate_dougyousya.php'){
           echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('cleate_dougyousya.php', '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_peer.gif').'</span><span>'.FILENAME_CLEATE_DOUGYOUSYA_TEXT.'</span></div>';
         }else{
           echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style="" onclick="window.location.href=\''.tep_href_link('cleate_dougyousya.php', '', 'NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_peer.gif').'</span><span>'.FILENAME_CLEATE_DOUGYOUSYA_TEXT.'</span></div>';
         }
       }
       if (!check_whether_is_limited('address.php')) {
         if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'address.php'){
           echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('address.php','','NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_address.gif').'</span><span>'.BOX_CREATE_ADDRESS.'</span></div>';
         }else{
           echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style="" onclick="window.location.href=\''.tep_href_link('address.php','','NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_address.gif').'</span><span>'.BOX_CREATE_ADDRESS.'</span></div>';
         }
       }
       if (!check_whether_is_limited('country_fee.php')) {
         if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'country_fee.php'){
           echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('country_fee.php','','NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_charge.gif').'</span><span>'.BOX_COUNTRY_FEE.'</span></div>';
         }else{
           echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style="" onclick="window.location.href=\''.tep_href_link('country_fee.php','','NONSSL').'\';"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_charge.gif').'</span><span>'.BOX_COUNTRY_FEE.'</span></div>';
         }
       }
       if (!check_whether_is_limited('products_shipping_time.php')) {
         if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'products_shipping_time.php'){
         echo ' <div class="sidebarselected" onclick="window.location.href=\''.tep_href_link('products_shipping_time.php','','NONSSL').'\';"><span>' . tep_image(DIR_WS_MENU_ICON . 'icon_delivery_time.gif').'</span><span>'.BOX_SHIPPING_TIME.'</span></div>';
         }else{
         echo ' <div class="sidebar" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" onmouseout="this.className=\'sidebar\'" style="" onclick="window.location.href=\''.tep_href_link('products_shipping_time.php','','NONSSL').'\';"><span>' . tep_image(DIR_WS_MENU_ICON . 'icon_delivery_time.gif').'</span><span>'.BOX_SHIPPING_TIME.'</span></div>';
         }
       }
       
       $other_configuration_groups_img = array();
       $other_configuration_groups_img = array("998"=>"icon_price_setting.gif");
       if (!check_whether_is_limited(FILENAME_CONFIGURATION)) {
         $other_configuration_groups_query = tep_db_query("select configuration_group_id as cgID, configuration_group_title as cgTitle from " .  TABLE_CONFIGURATION_GROUP . " where visible = '1' and type_info = '1' order by sort_order");
         while ($other_configuration_groups = tep_db_fetch_array($other_configuration_groups_query)) {
           if(str_replace('/admin/','',$_SERVER['PHP_SELF']).'?gID='.$_GET['gID'] == FILENAME_CONFIGURATION.'?gID='.$other_configuration_groups['cgID']){
             echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_CONFIGURATION, 'gID=' .  $other_configuration_groups['cgID'], 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON.$other_configuration_groups_img[$other_configuration_groups['cgID']]).'</span><span>';
             if(constant($other_configuration_groups['cgTitle'])){
               echo constant($other_configuration_groups['cgTitle']);
             }else{
               echo $other_configuration_groups['cgTitle']; 
             }
               echo '</span></div>';
             }else{
               echo '<div onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_CONFIGURATION, 'gID=' .  $other_configuration_groups['cgID'], 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON.$other_configuration_groups_img[$other_configuration_groups['cgID']]).'</span><span>';
               if(constant($other_configuration_groups['cgTitle'])){
                 echo constant($other_configuration_groups['cgTitle']);
               }else{
                 echo $other_configuration_groups['cgTitle']; 
               }
               echo '</span></div>';
             } 
         }
       }
       ?>
              </td>
             </tr>
            </table> 
           </div> 
          </td>
         </tr>
<!-- catalog_eof -->
