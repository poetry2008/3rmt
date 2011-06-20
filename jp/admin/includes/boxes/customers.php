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
              <a class="menuBoxHeading_Link" href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=customers');?>"><?php echo BOX_HEADING_CUSTOMERS;?></a> 
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if ($selected_box == 'customers') {
            ?>
            <div id="col4"> 
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
    <?php
    echo '<a '.((basename($PHP_SELF) == FILENAME_CUSTOMERS)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' .  tep_href_link(FILENAME_CUSTOMERS, '', 'NONSSL') . '">' . BOX_CUSTOMERS_CUSTOMERS . '</a><br>' .  '<a '.((basename($PHP_SELF) == FILENAME_ORDERS)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link(FILENAME_ORDERS, '', 'NONSSL') . '">' .  BOX_CUSTOMERS_ORDERS . '</a><br>'.  '<a '.((basename($PHP_SELF) == 'telecom_unknow.php')?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link('telecom_unknow.php') . '">決算管理</a><br>' .  '<a '.((basename($PHP_SELF) == 'bill_templates.php')?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link('bill_templates.php') . '">請求書のテンプレート</a><br>';?> 
                </td>
              </tr>
            </table>
            </div>
            <?php }?> 
            </td>
          </tr>
<!-- customers_eof //-->
