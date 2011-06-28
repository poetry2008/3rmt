<?php
/*
  $Id$
*/
?>
<!-- modules //-->
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
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
              <td onmouseover="this.style.cursor='hand'" class="menuBoxHeading">
              &nbsp; 
              <a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col3');"><?php echo BOX_HEADING_MODULES;?></a> 
              &nbsp; 
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
    echo '<a href="' . tep_href_link(FILENAME_MODULES, 'set=payment', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_MODULES_PAYMENT . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_MODULES, 'set=ordertotal', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_MODULES_ORDER_TOTAL . '</a><br>'.  '<a href="' . tep_href_link(FILENAME_MODULES, 'set=metaseo', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_MODULES_METASEO . '</a><br>'; ?>
                </td>
              </tr>
            </table>
            </div>
            </td>
          </tr>
<!-- modules_eof //-->
