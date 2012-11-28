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
              <td onmouseover="this.style.cursor='hand'" class="menuBoxHeading">
              &nbsp;<?php echo tep_image(DIR_WS_MENU_ICON . 'icon_catalog.gif'); ?> <a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col2');"><?php echo BOX_HEADING_CATALOG;?></a> 
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
                <td class="menuBoxContent"><?php 
       echo '<span class="menuBoxContent_image">'.
       tep_image(DIR_WS_MENU_ICON . 'icon_category.gif').'</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_CATEGORIES_PRODUCTS . '</a></span><br><span class="menuBoxContent_image">' .
       tep_image(DIR_WS_MENU_ICON . 'icon_delete_option.gif').'</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_SHOW_USELESS_OPTION, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_SHOW_USELESS_OPTION . '</a></span><br><span class="menuBoxContent_image">' .
       tep_image(DIR_WS_MENU_ICON . 'icon_option_register.gif') .'</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_OPTION_GROUP, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES . '</a></span><br><span class="menuBoxContent_image">' .
       tep_image(DIR_WS_MENU_ICON . 'icon_tags.gif') . '</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_TAGS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_PRODUCTS_TAGS . '</a></span><br><span class="menuBoxContent_image">' .
       tep_image(DIR_WS_MENU_ICON . 'icon_products_tags.gif').'</span><span class="menuBoxContent_span"><a href="' . tep_href_link('products_tags.php', '', 'NONSSL') . '" class="menuBoxContent_Link">'.FILENAME_PRODUCTS_TAGS_TEXT.'</a></span><br><span class="menuBoxContent_image">' .
       tep_image(DIR_WS_MENU_ICON . 'icon_maker.gif').'</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_MANUFACTURERS . '</a></span><br><span class="menuBoxContent_image">'.(isset($_color_l)?$_color_l:'') .
       tep_image(DIR_WS_MENU_ICON . 'icon_review_set.gif').'</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_REVIEWS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_REVIEWS . '</a></span><br><span class="menuBoxContent_image">' .
       tep_image(DIR_WS_MENU_ICON . 'icon_wholesaler.gif').'</span><span class="menuBoxContent_span"><a href="'.tep_href_link('cleate_oroshi.php', '', 'NONSSL').'" class="menuBoxContent_Link">'.FILENAME_CLEATE_OROSHI_TEXT.'</a></span><br><span class="menuBoxContent_image">'.
       tep_image(DIR_WS_MENU_ICON . 'icon_peer.gif').'</span><span class="menuBoxContent_span"><a href="'.tep_href_link('cleate_dougyousya.php', '', 'NONSSL').  '"class="menuBoxContent_Link">'.FILENAME_CLEATE_DOUGYOUSYA_TEXT.'</a></span><br><span class="menuBoxContent_image">'.
       tep_image(DIR_WS_MENU_ICON . 'icon_address.gif').'</span><span class="menuBoxContent_span"><a href="'.tep_href_link('address.php','','NONSSL').'" class="menuBoxContent_Link">'.BOX_CREATE_ADDRESS.'</a></span><br><span class="menuBoxContent_image">'.
       tep_image(DIR_WS_MENU_ICON . 'icon_charge.gif').'</span><span class="menuBoxContent_span"><a href="'.tep_href_link('country_fee.php','','NONSSL').'" class="menuBoxContent_Link">'.BOX_COUNTRY_FEE.'</a></span><br><span class="menuBoxContent_image">'.
       tep_image(DIR_WS_MENU_ICON . 'icon_delivery_time.gif').'</span><span class="menuBoxContent_span"><a href="'.tep_href_link('products_shipping_time.php','','NONSSL').'" class="menuBoxContent_Link">'.BOX_SHIPPING_TIME.'</a></span>';

?>
              </td>
             </tr>
            </table> 
           </div> 
          </td>
         </tr>
<!-- catalog_eof -->
