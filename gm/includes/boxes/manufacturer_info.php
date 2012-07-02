<?php
/*
  $Id$
*/

  if (isset($_GET['products_id'])) {
    // ccdd
    $manufacturer_query = tep_db_query("
        select m.manufacturers_id, 
               m.manufacturers_name, 
               m.manufacturers_image, 
               mi.manufacturers_url 
        from " . TABLE_MANUFACTURERS . " m 
          left join " . TABLE_MANUFACTURERS_INFO . " mi on (m.manufacturers_id = mi.manufacturers_id and mi.languages_id = '" . $languages_id . "'), " . TABLE_PRODUCTS . " p  
        where p.products_id = '" . (int)$_GET['products_id'] . "' 
          and p.manufacturers_id = m.manufacturers_id
    ");
    if (tep_db_num_rows($manufacturer_query)) {
      $manufacturer = tep_db_fetch_array($manufacturer_query);
?>
<!-- manufacturer_info //-->
<?php

      echo '<a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $manufacturer['manufacturers_id']) . '">' . $manufacturer['manufacturers_name'] . '</a>';

?>
<!-- manufacturer_info_eof //-->
<?php
    } else {
    echo '---';
  }
  }
?>
