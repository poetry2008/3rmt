<?php
/*
  $Id$
*/
  global $product_info;
  if (isset($_GET['products_id'])) {
    
    $orders_sql = "
        select * from (select p.products_id, o.date_purchased, pd.site_id, pd.products_status, p.products_image from " .  TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " .  TABLE_ORDERS . " o, " .  TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_DESCRIPTION." pd 
        where opa.products_id = '" .  (int)$_GET['products_id'] . "' 
          and opa.orders_id = opb.orders_id 
          and opb.products_id != '" . (int)$_GET['products_id'] . "' 
          and opb.products_id = p.products_id 
          and opb.orders_id = o.orders_id 
          and p.products_id = pd.products_id 
          and o.site_id = '".SITE_ID."' 
        order by pd.site_id DESC) p where site_id = '".SITE_ID."' or site_id = '0' 
        group by products_id 
        having p.products_status != '3'
        order by date_purchased desc 
        limit " . MAX_DISPLAY_ALSO_PURCHASED
    ;
    $_orders_query = tep_db_query($orders_sql); 
    $orders_query = tep_db_query($orders_sql);
    $num_products_ordered = tep_db_num_rows($orders_query);
    if ($num_products_ordered >= MIN_DISPLAY_ALSO_PURCHASED) {
      $h_show_flag = false;
      while ($_orders = tep_db_fetch_array($_orders_query)) {
        if ($_orders['products_status'] != 0) {
          $h_show_flag = true;
        }
      }
      if($h_show_flag){
?>
<!-- also_purchased_products //-->
<h1 class="pageHeading_long"><?php echo $product_info['products_name'];?><?php  echo TEXT_ALSO_PURCHASED_PRODUCTS ; ?> </h1>
<div class="comment_long">
<div class="product_info_box"><?php
      $row = 0;
      $col = 0;
      
      echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">'."\n" ;
      echo   '<tr>'."\n";
      }
      while ($orders = tep_db_fetch_array($orders_query)) {
        if($orders['products_status'] != 0 ){
        $orders['products_name'] = tep_get_products_name($orders['products_id']);
        
        $products_description = tep_get_products_description($orders['products_id'], $languages_id) ;
        if($products_description){
          $products_description = strip_tags(substr($products_description['products_description'],0,96));
        } else {
          $products_description = '';
        }
    echo '

<td width="25%" align="center" class="smallText">';
if ($orders['products_status'] == 0) {
  echo tep_image(DIR_WS_IMAGES . 'products/' . $orders['products_image'], $orders['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"').'<br>'.$orders['products_name'];
} else {
  echo ' <a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $orders['products_id']) . '">'.tep_image(DIR_WS_IMAGES . 'products/' . $orders['products_image'], $orders['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"').'</a> <br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .  $orders['products_id']) . '">'.$orders['products_name'].'</a>';
}
echo '</td>';

        $col ++;
        if ($col > 3) {
    echo '</tr><tr>';
          $col = 0;
          $row ++;
        }
        }
      }
if($h_show_flag){
      echo '</tr>';
  echo '</table>' ;
?> </div>
</div>
<!-- also_purchased_products_eof //--> 
<?php
}
    }
  }
?> 
