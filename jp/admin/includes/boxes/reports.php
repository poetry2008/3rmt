<?php
/*
  $Id$
*/
?>
<!-- reports -->
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
            <table cellspacing="0" cellpadding="2" border="0" width="150"> 
             <tr>
              <td onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style=""> &nbsp; <?php echo tep_image(DIR_WS_MENU_ICON . 'icon_report.gif');?>&nbsp;<a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col6');"><?php echo BOX_HEADING_REPORTS;?></a> &nbsp; 
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
    if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'cal_info.php'){
    echo ' <div class="sidebarselected"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_statistics.gif').'</span><span><a href="' .  tep_href_link('cal_info.php', '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_CAL_SITES_INFO_TEXT . '</a></span></div>';
    }else{
    echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_statistics.gif').'</span><span><a href="' .  tep_href_link('cal_info.php', '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_CAL_SITES_INFO_TEXT . '</a></span></div>';
    }
    if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_STATS_SALES_REPORT2){
    echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_sales.gif').'</span><span><a href="' .  tep_href_link(FILENAME_STATS_SALES_REPORT2, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_SALES_REPORT2 . '</a></span></div>';
    }else{
    echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_sales.gif').'</span><span><a href="' .  tep_href_link(FILENAME_STATS_SALES_REPORT2, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_SALES_REPORT2 . '</a></span></div>';
    }
    if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_ASSETS){
    echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_property.gif').'</span><span><a href="' .  tep_href_link(FILENAME_ASSETS, '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_REPORTS_ASSETS . '</a></span></div>';
    }else{ 
    echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_property.gif').'</span><span><a href="' .  tep_href_link(FILENAME_ASSETS, '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_REPORTS_ASSETS . '</a></span></div>';
    }
   if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_STATS_PRODUCTS_VIEWED){
    echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_access.gif' ).  '</span><span><a href="' .  tep_href_link(FILENAME_STATS_PRODUCTS_VIEWED, '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_REPORTS_PRODUCTS_VIEWED .  '</a></span></div>';
   }else{
    echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON . 'icon_access.gif' ).  '</span><span><a href="' .  tep_href_link(FILENAME_STATS_PRODUCTS_VIEWED, '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_REPORTS_PRODUCTS_VIEWED .  '</a></span></div>';
   }
   if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_STATS_PRODUCTS_PURCHASED){
   echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_sale_count.gif').'</span><span><a href="' .  tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_REPORTS_PRODUCTS_PURCHASED .  '</a></span></div>';
   }else{
   echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_sale_count.gif').'</span><span><a href="' .  tep_href_link(FILENAME_STATS_PRODUCTS_PURCHASED, '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_REPORTS_PRODUCTS_PURCHASED .  '</a></span></div>';
   }
   if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_STATS_CUSTOMERS){
   echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_sale_ranking.gif').'</span><span><a href="' .  tep_href_link(FILENAME_STATS_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_ORDERS_TOTAL .  '</a></span></div>';
   }else{
   echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_sale_ranking.gif').'</span><span><a href="' .  tep_href_link(FILENAME_STATS_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_ORDERS_TOTAL .  '</a></span></div>';
   }
   if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'referer.php'){
     echo ' <div class="sidebarselected"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_access_ranking.gif').'</span><span><a href="' .  tep_href_link('referer.php') . '" class="menuBoxContent_Link">'.FILENAME_REFERER_TEXT.'</a></span></div>';
   }else{
   echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>'.  tep_image(DIR_WS_MENU_ICON .  'icon_access_ranking.gif').'</span><span><a href="' .  tep_href_link('referer.php') . '" class="menuBoxContent_Link">'.FILENAME_REFERER_TEXT.'</a></span></div>';
   }
   if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == 'keywords.php'){
     echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_keyword_ranking.gif').'</span><span><a href="' .  tep_href_link('keywords.php') . '" class="menuBoxContent_Link">'.FILENAME_KEYWORDS_TEXT.'</a></span></div>';
   }else{
   echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_keyword_ranking.gif').'</span><span><a href="' .  tep_href_link('keywords.php') . '" class="menuBoxContent_Link">'.FILENAME_KEYWORDS_TEXT.'</a></span></div>';
   }
   if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_NEW_CUSTOMERS){
   echo ' <div class="sidebarselected"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_new_customers.gif').'</span><span><a href="' .  tep_href_link(FILENAME_NEW_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_NEW_CUSTOMERS . '</a></span></div>';
   }else{
   echo ' <div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_new_customers.gif').'</span><span><a href="' .  tep_href_link(FILENAME_NEW_CUSTOMERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_REPORTS_NEW_CUSTOMERS . '</a></span></div>';
   }
    ?>
                </td>
              </tr>
            </table> 
            </div> 
            </td>
          </tr>
<!-- reports_eof -->
