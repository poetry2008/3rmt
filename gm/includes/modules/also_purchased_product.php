

<?php
/*
  $Id$
*/
  global $product_info;
  if (isset($_GET['products_id'])) {
    
    $orders_sql = "
        select * from (select o.date_purchased, pd.site_id, pd.products_status, p.products_id  from " .  TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " .  TABLE_ORDERS . " o, " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_DESCRIPTION." pd where opa.products_id = '" .  (int)$_GET['products_id'] . "' 
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
<h3 ><span><?php echo $product_info['products_name'];?><?php  echo TEXT_ALSO_PURCHASED_PRODUCTS ; ?></span> </h3>
  
<?php
      $row = 0;
      $col = 0;
      
      echo '<div class="yui3-g main-columns">'."\n" ;
      }
      while ($orders = tep_db_fetch_array($orders_query)) {
        if ($orders['products_status'] != 0) {
        $orders['products_name'] = tep_get_products_name($orders['products_id']);
        
        $products_description = tep_get_products_description($orders['products_id'], $languages_id) ;
        if($products_description){
          $products_description = strip_tags(substr(replace_store_name($products_description['products_description']),0,96));
        } else {
          $products_description = '';
        }
    echo '<div class="yui3-u-1-8 hm-hot"> ';
if ($orders['products_status'] == 0) {
  $img_array = tep_products_images($orders['products_id'],$orders['site_id']);
  echo tep_image(DIR_WS_IMAGES . $img_array[0], $orders['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="image_border"').'<br>'.$orders['products_name'];
} else {
  echo '<div id="hm-hot-category"> <a href="' . tep_href_link(FILENAME_PRODUCT_INFO,
       'products_id=' . $orders['products_id']) . '">'.tep_image(DIR_WS_IMAGES .
         $img_array[0], $orders['products_name'], SMALL_IMAGE_WIDTH,
       SMALL_IMAGE_HEIGHT,'class="image_border"').'</a></div> <br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' .  $orders['products_id']) . '">'.$orders['products_name'].'</a>';
}
echo '</div>';

        $col ++;
        if ($col > 3) {
             $col = 0;
          $row ++;
        }
        }
      }
if($h_show_flag){
      echo '</div>' ;
?> 
<!-- also_purchased_products_eof //--> 
<?php
}
    }
  }
?> 
