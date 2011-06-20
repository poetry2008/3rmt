<?php
/*
  $Id$
*/
?>
<!-- reports //-->
          <tr>
            <td>
<?php
  /* 
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
  */
?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
              <td onmouseover="this.style.cursor='hand'" class="menuBoxHeading">
              &nbsp; 
              <a class="menuBoxHeading_Link" href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=reports');?>"><?php echo BOX_HEADING_REPORTS;?></a> 
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if ($selected_box == 'reports') {
            ?>
            <div id="col6"> 
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
    <?php 
    echo  '<a '.((basename($PHP_SELF) == FILENAME_STATS_SALES_REPORT2)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link(FILENAME_STATS_SALES_REPORT2, '', 'NONSSL') . '">' . BOX_REPORTS_SALES_REPORT2 . '</a><br>' .
                                   '<a '.((basename($PHP_SELF) == FILENAME_STATS_PRODUCTS_VIEWED)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link(FILENAME_STATS_PRODUCTS_VIEWED, '', 'NONSSL') . '">' . BOX_REPORTS_PRODUCTS_VIEWED . '</a><br>' .
                                   '<a '.((basename($PHP_SELF) == FILENAME_STATS_PRODUCTS_PURCHASED)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, '', 'NONSSL') . '">' . BOX_REPORTS_PRODUCTS_PURCHASED . '</a><br>' .
                                   '<a '.((basename($PHP_SELF) == FILENAME_STATS_CUSTOMERS)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link(FILENAME_STATS_CUSTOMERS, '', 'NONSSL') . '">' . BOX_REPORTS_ORDERS_TOTAL . '</a><br>'.
                                   '<a '.((basename($PHP_SELF) == 'referer.php')?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link('referer.php') . '">アクセスランキング</a><br>' . 
                                   '<a '.((basename($PHP_SELF) == 'keywords.php')?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link('keywords.php') . '">キーワードランキング</a><br>' . 
                                   '<a '.((basename($PHP_SELF) == FILENAME_NEW_CUSTOMERS)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' .  tep_href_link(FILENAME_NEW_CUSTOMERS, '', 'NONSSL') . '">' . BOX_REPORTS_NEW_CUSTOMERS . '</a>';
    ?>
                </td>
              </tr>
            </table> 
            </div> 
            <?php }?> 
            </td>
          </tr>
<!-- reports_eof //-->
