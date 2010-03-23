<?php
/*
  $Id$

*/

  if (isset($HTTP_GET_VARS['products_id'])) {
    // ccdd
    $orders_query = tep_db_query("
        select p.products_id, 
               p.products_image 
        from " .  TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " .  TABLE_ORDERS . " o, " . TABLE_PRODUCTS . " p 
        where opa.products_id = '" .  (int)$HTTP_GET_VARS['products_id'] . "' 
          and opa.orders_id = opb.orders_id 
          and opb.products_id != '" . (int)$HTTP_GET_VARS['products_id'] . "' 
          and opb.products_id = p.products_id 
          and opb.orders_id = o.orders_id 
          and p.products_status = '1' 
          and o.site_id = '".SITE_ID."' 
        group by p.products_id 
        order by o.date_purchased desc 
        limit " . MAX_DISPLAY_ALSO_PURCHASED
    );
    $num_products_ordered = tep_db_num_rows($orders_query);
    if ($num_products_ordered >= MIN_DISPLAY_ALSO_PURCHASED) {
?>
<!-- also_purchased_products //-->
<h1 class="pageHeading_long"><?php  echo TEXT_ALSO_PURCHASED_PRODUCTS ; ?> </h1>
<?php
      $row = 0;
      $col = 0;
    echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">'."\n" ;
    echo   '<tr>'."\n";
      while ($orders = tep_db_fetch_array($orders_query)) {
        $orders['products_name'] = tep_get_products_name($orders['products_id']);
        // ccdd
        /*
        $products_description = tep_db_query("
            select products_description 
            from " . TABLE_PRODUCTS_DESCRIPTION . " 
            where products_id = '".$orders['products_id']."' 
              and site_id = '".SITE_ID."'"
        );
        $products_description = tep_db_fetch_array($products_description);
        */
        $products_description = tep_get_products_description($orders['products_id'], $languages_id) ;
        $products_description = strip_tags(substr ($products_description['products_description'],0,96));
	  echo '

<td width="25%" align="center" class="smallText">		
<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $orders['products_id']) . '">'.tep_image(DIR_WS_IMAGES . $orders['products_image'], $orders['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"').'</a>
   <br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $orders['products_id']) . '">'.$orders['products_name'].'</a>
</td>';

		/*
		$info_box_contents[$row][$col] = array('align' => 'center',
                                               'params' => 'class="smallText" width="33%" valign="top"',
                                               'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $orders['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $orders['products_image'], $orders['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $orders['products_id']) . '">' . $orders['products_name'] . '</a>');
        */
        $col ++;
        if ($col > 3) {
	  echo '</tr><tr>';
          $col = 0;
          $row ++;
        }
      }
		  echo '</tr>';
	echo '</table>' ;
//      new contentBox($info_box_contents);
?> 
<!-- also_purchased_products_eof //--> 
<?php
    }
  }
?> 
