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
               pd.products_attention_1,
               pd.products_attention_2,
               pd.products_attention_3,
               pd.products_attention_4,
               pd.products_attention_5,
               pd.products_url,
               pd.products_viewed,
               s.specials_new_products_price,
               s.specials_date_added,
               s.specials_last_modified,
               s.expires_date,
               s.date_status_change,
               s.status,
               IF(s.status, s.specials_new_products_price, p.products_price) as final_price 
      from " . TABLE_PRODUCTS_TO_TAGS . " as p2t join ". TABLE_PRODUCTS . " as p on p2t.products_id = p.products_id left join " . TABLE_PRODUCTS_DESCRIPTION . " as pd on p.products_id = pd.products_id left join " . TABLE_SPECIALS . " as s on p.products_id = s.products_id 
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
               pd.products_attention_1,
               pd.products_attention_2,
               pd.products_attention_3,
               pd.products_attention_4,
               pd.products_attention_5,
               pd.products_url,
               pd.products_viewed,
               s.specials_new_products_price,
               s.specials_date_added,
               s.specials_last_modified,
               s.expires_date,
               s.date_status_change,
               s.status,
               IF(s.status, s.specials_new_products_price, p.products_price) as final_price 
      from " . TABLE_PRODUCTS_TO_TAGS . " as p2t join ". TABLE_PRODUCTS . " as p on p2t.products_id = p.products_id left join " . TABLE_PRODUCTS_DESCRIPTION . " as pd on p.products_id = pd.products_id left join " . TABLE_SPECIALS . " as s on p.products_id = s.products_id 
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

  if ( (!$_GET['sort']) || (!preg_match('/[1-9][ad]/', $_GET['sort'])) || (substr($_GET['sort'],0,1) > sizeof($column_list)) ) {
    for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
      if ($column_list[$col] == 'PRODUCT_LIST_NAME') {
        $_GET['sort'] = $col+1 . 'a';
        $listing_sql .= " order by products_name";
        break;
      }
    }
  } else {
    $sort_col = substr($_GET['sort'], 0 , 1);
    $sort_order = substr($_GET['sort'], 1);
    $listing_sql .= ' order by ';
    switch ($column_list[$sort_col-1]) {
      case 'PRODUCT_LIST_MODEL':
        $listing_sql .= "products_model " . ($sort_order == 'd' ? 'desc' : '') . ", products_name";
        break;
      case 'PRODUCT_LIST_NAME':
       $listing_sql .= "products_name " . ($sort_order == 'd' ? 'desc' : '');
        break;
      case 'PRODUCT_LIST_MANUFACTURER':
        $listing_sql .= "manufacturers_name " . ($sort_order == 'd' ? 'desc' : '') . ", products_name";
        break;
      case 'PRODUCT_LIST_QUANTITY':
        $listing_sql .= "products_quantity " . ($sort_order == 'd' ? 'desc' : '') . ", products_name";
        break;
      case 'PRODUCT_LIST_IMAGE':
        $listing_sql .= "products_name";
        break;
      case 'PRODUCT_LIST_WEIGHT':
        $listing_sql .= "products_weight " . ($sort_order == 'd' ? 'desc' : '') . ", products_name";
        break;
      case 'PRODUCT_LIST_PRICE':
        $listing_sql .= "final_price " . ($sort_order == 'd' ? 'desc' : '') . ", products_name";
        break;
      case 'PRODUCT_LIST_ORDERED':
        $listing_sql .= "products_ordered " . ($sort_order == 'd' ? 'desc' : '') . ", products_name";
        break;
    }
  }

