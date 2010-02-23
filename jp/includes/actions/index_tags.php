<?php
    // create column list
    $define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                       'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                       'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER, 
                       'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE, 
                       'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY, 
                       'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT, 
                       'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE, 
                       'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW,
                       'PRODUCT_LIST_ORDERED' => PRODUCT_LIST_ORDERED);
    asort($define_list);

    //print_r($define_list);

    $column_list = array();
    reset($define_list);
    while (list($column, $value) = each($define_list)) {
      if ($value) $column_list[] = $column;
    }
  if (tep_session_is_registered('customer_id'))
  {
    // ccdd
    $products_query = "select *, p.products_id , IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS_TO_TAGS . " as p2t join ". TABLE_PRODUCTS . " as p on p2t.products_id = p.products_id left join " . TABLE_PRODUCTS_DESCRIPTION . " as pd on p.products_id = pd.products_id left join " . TABLE_SPECIALS . " as s on p.products_id = s.products_id where p2t.tags_id = " .  (int)$HTTP_GET_VARS['tags_id']." and pd.site_id = ".SITE_ID;
  }
  else
  {
    // ccdd
    $products_query = "select *, p.products_id, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS_TO_TAGS . " as p2t join ". TABLE_PRODUCTS . " as p on p2t.products_id = p.products_id left join " . TABLE_PRODUCTS_DESCRIPTION . " as pd on p.products_id = pd.products_id left join " . TABLE_SPECIALS . " as s on p.products_id = s.products_id where p2t.tags_id = " .  (int)$HTTP_GET_VARS['tags_id']." and pd.site_id = ".SITE_ID;
  } 

     $listing_sql = $products_query;

     if (!isset($HTTP_GET_VARS['sort'])) $HTTP_GET_VARS['sort'] = NULL;
  if ( (!$HTTP_GET_VARS['sort']) || (!preg_match('/[1-9][ad]/', $HTTP_GET_VARS['sort'])) || (substr($HTTP_GET_VARS['sort'],0,1) > sizeof($column_list)) ) {
    for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
      if ($column_list[$col] == 'PRODUCT_LIST_NAME') {
        $HTTP_GET_VARS['sort'] = $col+1 . 'a';
        $listing_sql .= " order by pd.products_name";
        break;
      }
    }
  } else {
    $sort_col = substr($HTTP_GET_VARS['sort'], 0 , 1);
    $sort_order = substr($HTTP_GET_VARS['sort'], 1);
    $listing_sql .= ' order by ';
    switch ($column_list[$sort_col-1]) {
      case 'PRODUCT_LIST_MODEL':
        $listing_sql .= "p.products_model " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_NAME':
       $listing_sql .= "pd.products_name " . ($sort_order == 'd' ? 'desc' : '');
        break;
      case 'PRODUCT_LIST_MANUFACTURER':
        $listing_sql .= "m.manufacturers_name " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_QUANTITY':
        $listing_sql .= "p.products_quantity " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_IMAGE':
        $listing_sql .= "pd.products_name";
        break;
      case 'PRODUCT_LIST_WEIGHT':
        $listing_sql .= "p.products_weight " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_PRICE':
        $listing_sql .= "final_price " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
        break;
      case 'PRODUCT_LIST_ORDERED':
        $listing_sql .= "p.products_ordered " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
        break;
    }
  }
?>
      <td valign="top" id="contents">
       <h1 class="pageHeading_long"><?php echo $seo_tags['tags_name'];?></h1>
       <h2 class="line">RMT：ゲーム通貨・アイテム・アカウント </h2>
<?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?>
</td>
<td>
</td>
<?php
