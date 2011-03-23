<?php
/*
  $Id$
*/
?>
<!-- customers //-->
          <tr>
            <td>
<?php
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
?>
            </td>
          </tr>
<!-- customers_eof //-->
