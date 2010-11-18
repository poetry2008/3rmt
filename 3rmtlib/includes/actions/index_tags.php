<?php
/*
  $Id$
*/
    // create column list
    $define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                       'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                       'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER, 
                       'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE, 
                       'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY, 
                       'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT, 
                       'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE, 
                       'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW,
                       'PRODUCT_LIST_ORDERED' => PRODUCT_LIST_ORDERED
    );
    asort($define_list);

    $column_list = array();
    reset($define_list);
    while (list($column, $value) = each($define_list)) {
      if ($value) $column_list[] = $column;
    }
  if (tep_session_is_registered('customer_id')) {
    // ccdd
    $products_query = "
        select p.products_id,
               p.products_quantity,
               p.products_model,
               p.products_image,
               p.products_image2,
               p.products_image3,
               p.products_price,
               p.products_price_offset,
               p.products_date_added,
               p.products_last_modified,
               p.products_date_available,
               p.products_weight,
               p.products_status,
               p.products_tax_class_id,
               p.manufacturers_id,
               p.products_ordered,
               p.products_bflag,
               p.products_cflag,
               p.products_small_sum,
               p.option_type,
               p2t.tags_id,
               pd.language_id,
               pd.products_name,
               pd.products_description,
               pd.site_id,
               p.products_attention_1,
               p.products_attention_2,
               p.products_attention_3,
               p.products_attention_4,
               p.products_attention_5,
               pd.products_url,
               p.order_pickup,
               pd.products_viewed
      from " . TABLE_PRODUCTS_TO_TAGS . " as p2t join ". TABLE_PRODUCTS . " as p on p2t.products_id = p.products_id left join " . TABLE_PRODUCTS_DESCRIPTION . " as pd on p.products_id = pd.products_id
      where p2t.tags_id = " .  (int)$_GET['tags_id']."
      order by pd.site_id DESC
      ";
  } else {
    // ccdd
    $products_query = "
        select p.products_id,
               p.products_quantity,
               p.products_model,
               p.products_image,
               p.products_image2,
               p.products_image3,
               p.products_price,
               p.products_price_offset,
               p.products_date_added,
               p.products_last_modified,
               p.products_date_available,
               p.products_weight,
               p.products_status,
               p.products_tax_class_id,
               p.manufacturers_id,
               p.products_ordered,
               p.products_bflag,
               p.products_cflag,
               p.products_small_sum,
               p.option_type,
               p2t.tags_id,
               pd.language_id,
               pd.products_name,
               pd.products_description,
               pd.site_id,
               p.products_attention_1,
               p.products_attention_2,
               p.products_attention_3,
               p.products_attention_4,
               p.products_attention_5,
               pd.products_url,
               p.order_pickup,
               pd.products_viewed
      from " . TABLE_PRODUCTS_TO_TAGS . " as p2t join ". TABLE_PRODUCTS . " as p on p2t.products_id = p.products_id left join " . TABLE_PRODUCTS_DESCRIPTION . " as pd on p.products_id = pd.products_id 
      where p2t.tags_id = " .  (int)$_GET['tags_id']." 
      order by pd.site_id DESC";
  } 

   $listing_sql = "
     select *
     from (
     ".$products_query."
     ) p
     where site_id = '0'
        or site_id = '".SITE_ID."'
     group by products_id
   ";
