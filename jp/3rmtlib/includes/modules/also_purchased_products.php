<?php
/*
  $Id$
*/
  global $product_info;
  if (isset($_GET['products_id'])) {
    // ccdd
    $orders_query = tep_db_query("
        select * from (select o.date_purchased, pd.site_id, pd.products_status, p.products_id, p.products_image from " .  TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " .  TABLE_ORDERS . " o, " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_DESCRIPTION." pd where opa.products_id = '" .  (int)$_GET['products_id'] . "' 
          and o.orders_id = opa.orders_id
          and opa.orders_id = opb.orders_id 
          and opb.products_id != '" . (int)$_GET['products_id'] . "' 
          and opb.products_id = p.products_id 
          and p.products_id = pd.products_id 
          and opa.site_id = '".SITE_ID."'
          order by pd.site_id DESC
        ) c where site_id = ".SITE_ID." or site_id = 0
        group by products_id
        having c.products_status != '3'
        order by date_purchased desc 
        limit " . MAX_DISPLAY_ALSO_PURCHASED
    );
    $num_products_ordered = tep_db_num_rows($orders_query);
    if ($num_products_ordered >= MIN_DISPLAY_ALSO_PURCHASED) {
?>
<!-- also_purchased_products //-->
<h3 class="pageHeading_long"><?php echo $product_info['products_name'];?><?php  echo TEXT_ALSO_PURCHASED_PRODUCTS ; ?> </h3>
  
<?php
      $row = 0;
      $col = 0;
      
      echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">'."\n" ;
      echo   '<tr>'."\n";
      while ($orders = tep_db_fetch_array($orders_query)) {
        $orders['products_name'] = tep_get_products_name($orders['products_id']);
        // ccdd
        $products_description = tep_get_products_description($orders['products_id'], $languages_id) ;
        if($products_description){
          $products_description = strip_tags(substr(replace_store_name($products_description['products_description']),0,96));
        } else {
          $products_description = '';
        }
    echo '

<td width="25%" align="center" class="smallText">   
';
if ($orders['products_status'] == 0) {
  echo tep_image(DIR_WS_IMAGES . $orders['products_image'], $orders['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"').'<br>'.$orders['products_name'];
} else {
  echo ' <a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $orders['products_id']) . '">'.tep_image(DIR_WS_IMAGES . $orders['products_image'], $orders['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"').'</a> <br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .  $orders['products_id']) . '">'.$orders['products_name'].'</a>';
}
echo '</td>';

        $col ++;
        if ($col > 3) {
    echo '</tr><tr>';
          $col = 0;
          $row ++;
        }
      }
      echo '</tr>';
  echo '</table>' ;
?> 
<!-- also_purchased_products_eof //--> 
<?php
    }
  }
?> 
