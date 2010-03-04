<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  // ccdd
  $expected_query = tep_db_query(
      "select p.products_id, 
              pd.products_name, 
              products_date_available as date_expected 
       from " . TABLE_PRODUCTS . " p, " .  TABLE_PRODUCTS_DESCRIPTION . " pd 
       where to_days(products_date_available) >= to_days(now()) 
         and p.products_id = pd.products_id 
         and pd.language_id = '" .  $languages_id . "' 
         and pd.site_id = '".SITE_ID."' 
       order by " . EXPECTED_PRODUCTS_FIELD . " " . EXPECTED_PRODUCTS_SORT . " 
       limit " . MAX_DISPLAY_UPCOMING_PRODUCTS
   );
  if (tep_db_num_rows($expected_query) > 0) {
?>
<!-- upcoming_products //-->
        <div class="pageHeading"><?php echo tep_image(DIR_WS_IMAGES.'design/text/upcomming_products.gif',TABLE_HEADING_UPCOMING_PRODUCTS);?></div> 
        <div class="pageHeading_line"></div> 
<div id="contents">
<table width="500" border="0" cellpadding="2" cellspacing="0">
              <tr>
                <td class="tableHeading">&nbsp;<?php echo TABLE_HEADING_UPCOMING_PRODUCTS; ?>&nbsp;</td>
                <td align="right" class="tableHeading">&nbsp;<?php echo TABLE_HEADING_DATE_EXPECTED; ?>&nbsp;</td>
              </tr>
              <tr>
<?php
    $row = 0;
    while ($expected = tep_db_fetch_array($expected_query)) {
      $row++;
      if (($row / 2) == floor($row / 2)) {
        echo '              <tr class="upcomingProducts-even">' . "\n";
      } else {
        echo '              <tr class="upcomingProducts-odd">' . "\n";
      }

      echo '                <td class="smallText">&nbsp;<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $expected['products_id']) . '">' . $expected['products_name'] . '</a>&nbsp;</td>' . "\n" .
           '                <td align="right" class="smallText">&nbsp;' . tep_date_short($expected['date_expected']) . '&nbsp;</td>' . "\n" .
           '              </tr>' . "\n";
    }
?>
</table>
</div>
<!-- upcoming_products_eof //-->
<?php
  }
?>
