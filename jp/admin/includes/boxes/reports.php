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
              <a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col6');"><?php echo BOX_HEADING_REPORTS;?></a> 
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if (in_array('col6', $l_select_box_arr)) {
            ?>
            <div id="col6" style="display:block"> 
            <?php
            } else {
            ?>
            <div id="col6" style="display:none"> 
            <?php
            }
            ?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
    <?php 
    echo '<a href="' . tep_href_link('cal_info.php', '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CAL_SITES_INFO_TEXT . '</a><br>' .
   '<a href="' . tep_href_link(FILENAME_STATS_SALES_REPORT2, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_SALES_REPORT2 . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_STATS_PRODUCTS_VIEWED, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_PRODUCTS_VIEWED . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_PRODUCTS_PURCHASED . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_STATS_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_ORDERS_TOTAL . '</a><br>'.
                                   '<a href="' . tep_href_link('referer.php') . '"
                                   class="menuBoxContent_Link">'.FILENAME_REFERER_TEXT.'</a><br>' . 
                                   '<a href="' . tep_href_link('keywords.php') . '"
                                   class="menuBoxContent_Link">'.FILENAME_KEYWORDS_TEXT.'</a><br>' . 
                                   '<a href="' .  tep_href_link(FILENAME_NEW_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_NEW_CUSTOMERS . '</a>';
    ?>
                </td>
              </tr>
            </table> 
            </div> 
            </td>
          </tr>
<!-- reports_eof //-->
