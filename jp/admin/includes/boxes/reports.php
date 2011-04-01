<?php
/*
  $Id$
*/
?>
<!-- reports //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_REPORTS,
                     'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=reports'));

  if ($selected_box == 'reports') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_STATS_SALES_REPORT2, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_SALES_REPORT2 . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_STATS_PRODUCTS_VIEWED, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_PRODUCTS_VIEWED . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_PRODUCTS_PURCHASED . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_STATS_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_ORDERS_TOTAL . '</a><br>'.
                                   '<a href="' . tep_href_link('referer.php') . '" class="menuBoxContent_Link">アクセスランキング</a><br>' . 
                                   '<a href="' . tep_href_link('keywords.php') . '" class="menuBoxContent_Link">キーワードランキング</a><br>' . 
                                   '<a href="' . tep_href_link(FILENAME_NEW_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_NEW_CUSTOMERS . '</a>');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- reports_eof //-->
