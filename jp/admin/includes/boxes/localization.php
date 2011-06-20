<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- localization //-->
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
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
              <td onmouseover="this.style.cursor='hand'" class="menuBoxHeading">
              &nbsp; 
              <a class="menuBoxHeading_Link" href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=localization');?>"><?php echo BOX_HEADING_LOCALIZATION;?></a> 
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if ($selected_box == 'localization') {
            ?>
            <div id="col5"> 
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
                 <?php echo '<a '.((basename($PHP_SELF) == FILENAME_ORDERS_STATUS)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link(FILENAME_ORDERS_STATUS, '', 'NONSSL') . '">' .  BOX_LOCALIZATION_ORDERS_STATUS . '</a>';?>
                </td>
              </tr>
            </table>
            </div>
            <?php }?> 
            </td>
          </tr>
<!-- localization_eof //-->
