<?php
/*
  $Id$
*/
?>
<!-- customers //-->
          <tr>
            <td>
<?php
  /*  
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_CUSTOMERS,
                     'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=customers'));

  if ($selected_box == 'customers') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CUSTOMERS_CUSTOMERS . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_ORDERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CUSTOMERS_ORDERS . '</a><br>'.
                                   '<a href="' . tep_href_link('telecom_unknow.php') . '" class="menuBoxContent_Link">決算管理</a><br>' . 
                                   '<a href="' . tep_href_link('bill_templates.php') . '" class="menuBoxContent_Link">請求書のテンプレート</a><br>' . 
                   '<!--<a href="customers_dl.php" class="menuBoxContent_Link">顧客データダウンロード</a>-->');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
  */
?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
              <td onmouseover="this.style.cursor='hand'" class="menuBoxHeading">
              &nbsp; 
              <a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col4');"><?php echo BOX_HEADING_CUSTOMERS;?></a> 
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
    echo '<a href="' . tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CUSTOMERS_CUSTOMERS . '</a><br>' .  '<a href="' . tep_href_link(FILENAME_ORDERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CUSTOMERS_ORDERS . '</a><br>'.  '<a href="' . tep_href_link('telecom_unknow.php') . '" class="menuBoxContent_Link">決算管理</a><br>' .  '<a href="' . tep_href_link('bill_templates.php') . '" class="menuBoxContent_Link">請求書のテンプレート</a><br>';?> 
                </td>
              </tr>
            </table>
            </div>
            </td>
          </tr>
<!-- customers_eof //-->
