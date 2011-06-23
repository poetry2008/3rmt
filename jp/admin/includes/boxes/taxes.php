<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- taxes //-->
          <tr>
            <td>
<?php
  /* 
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_LOCATION_AND_TAXES,
                     'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=taxes'));

  if ($selected_box == 'taxes') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_COUNTRIES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_TAXES_COUNTRIES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_ZONES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_TAXES_ZONES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_GEO_ZONES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_TAXES_GEO_ZONES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_TAX_CLASSES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_TAXES_TAX_CLASSES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_TAX_RATES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_TAXES_TAX_RATES . '</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
*/
?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
              <td onmouseover="this.style.cursor='hand'" class="menuBoxHeading">
              &nbsp; 
              <a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col9');"><?php echo BOX_HEADING_LOCATION_AND_TAXES;?></a> 
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if (in_array('col9', $l_select_box_arr)) {
            ?>
            <div id="col9" style="display:block"> 
            <?php
            } else {
            ?>
            <div id="col9" style="display:none"> 
            <?php
            }
            ?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
      <?php echo '<a href="' . tep_href_link(FILENAME_COUNTRIES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_TAXES_COUNTRIES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_ZONES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_TAXES_ZONES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_GEO_ZONES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_TAXES_GEO_ZONES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_TAX_CLASSES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_TAXES_TAX_CLASSES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_TAX_RATES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_TAXES_TAX_RATES . '</a>';
                ?> 
                </td>
              </tr>
            </table> 
            </div> 
            </td>
          </tr>
<!-- taxes_eof //-->
