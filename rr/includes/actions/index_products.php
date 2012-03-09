<?php
/*
  $Id$
*/
// create column list
    $research_caid = tep_rr_get_categories_id_by_parent_id(FF_CID); 
    $rp_cid_arr = explode(',', FF_CID); 
    if (empty($research_caid)) {
      $research_caid[] = $rp_cid_arr[0]; 
      $research_caid[] = $rp_cid_arr[1]; 
    } else {
      array_push($research_caid, $rp_cid_arr[0], $rp_cid_arr[1]); 
    }
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

    $select_column_list = '';

    for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
      if ( ($column_list[$col] == 'PRODUCT_LIST_BUY_NOW') || ($column_list[$col] == 'PRODUCT_LIST_PRICE') ) {
        continue;
      }

      if (tep_not_null($select_column_list)) {
        $select_column_list .= ', ';
      }

      switch ($column_list[$col]) {
        case 'PRODUCT_LIST_MODEL':
          $select_column_list .= 'p.products_model';
          break;
        case 'PRODUCT_LIST_NAME':
          $select_column_list .= 'pd.products_name,pd.products_description';
          break;
        case 'PRODUCT_LIST_MANUFACTURER':
          $select_column_list .= 'm.manufacturers_name';
          break;
        case 'PRODUCT_LIST_QUANTITY':
          $select_column_list .= 'p.products_real_quantity + p.products_virtual_quantity as products_quantity';
          break;
        case 'PRODUCT_LIST_IMAGE':
          $select_column_list .= 'p.products_image';
          break;
        case 'PRODUCT_LIST_WEIGHT':
          $select_column_list .= 'p.products_weight';
          break;
        case 'PRODUCT_LIST_ORDERED':
          $select_column_list .= 'p.products_ordered';
          break;
      }
    }

    if (tep_not_null($select_column_list)) {
      $select_column_list .= ', ';
    }
    $listing_sql = "
      select * 
      from (
    ";

// show the products of a specified manufacturer
    if (isset($_GET['manufacturers_id'])) {
      if (isset($_GET['filter_id'])) {
// We are asked to show only a specific category
        $listing_sql .= "
          select " . $select_column_list . " 
                 p.products_id, 
                 p.manufacturers_id, 
                 p.products_price, 
                 p.products_price_offset, 
                 p.products_small_sum,
                 p.products_tax_class_id, 
                 p.sort_order,
                 pd.products_status, 
                 pd.site_id
          from " . TABLE_PRODUCTS . " p, " .  TABLE_PRODUCTS_DESCRIPTION . "
              pd, " . TABLE_MANUFACTURERS . " m, " .
              TABLE_PRODUCTS_TO_CATEGORIES . " p2c
          where p.manufacturers_id = m.manufacturers_id 
            and m.manufacturers_id = '" .  $_GET['manufacturers_id'] . "' 
            and p.products_id = p2c.products_id 
            and pd.products_id = p2c.products_id 
            and pd.language_id = '" . $languages_id . "' 
            and p2c.categories_id = '" . $_GET['filter_id'] . "' order by pd.site_id DESC";
      } else {
// We show them all
        $listing_sql.= "
        select " . $select_column_list . " 
              p.products_id, 
              p.products_bflag, 
              p.manufacturers_id, 
              p.products_price, 
              p.products_price_offset, 
              p.products_small_sum,
              p.products_tax_class_id, 
              p.sort_order,
              pd.products_status, 
              pd.site_id
        from " . TABLE_PRODUCTS . " p, " .  TABLE_PRODUCTS_DESCRIPTION . " pd, " .
        TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
        where pd.products_id = p.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.manufacturers_id = m.manufacturers_id 
          and p.products_id = p2c.products_id
          and p2c.categories_id in (".implode(',', $research_caid).")
          and m.manufacturers_id = '" . $_GET['manufacturers_id'] . "' order by pd.site_id DESC 
        ";
      }
// We build the categories-dropdown
      $filterlist_sql = "
        select *
        from(
          select distinct c.categories_id as id, 
                          cd.categories_name as name,
                          pd.products_status,
                          cd.site_id
          from " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_DESCRIPTION." pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
          where p.products_id = pd.products_id 
            and p.products_id = p2c.products_id 
            and p2c.categories_id = c.categories_id 
            and p2c.categories_id = cd.categories_id 
            and cd.language_id = '" . $languages_id . "' 
            and p.manufacturers_id = '" .  $_GET['manufacturers_id'] . "' 
            and p2c.categories_id in (".implode(',', $research_caid).")
          order by cd.categories_name
        ) c
        where site_id = 0
           or site_id = " . SITE_ID . "
        group by id
        having c.products_status != '0' and c.products_status != '3' 
        order by name
        ";
    } else {
// show the products in a given categorie
      if (isset($_GET['filter_id'])) {
// We are asked to show only specific catgeory
        $listing_sql .= "
          select " . $select_column_list . " 
                 p.products_id, 
                 p.manufacturers_id, 
                 p.products_price, 
                 p.products_price_offset,
                 p.products_small_sum, 
                 p.sort_order,
                 p.products_tax_class_id, 
                 pd.products_status,
                 pd.site_id
          from ( " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c  )
          where p.manufacturers_id = m.manufacturers_id 
            and m.manufacturers_id = '" .  $_GET['filter_id'] . "' 
            and p.products_id = p2c.products_id 
            and pd.products_id = p2c.products_id 
            and pd.language_id = '" . $languages_id . "' 
            and p2c.categories_id = '" . $current_category_id . "' order by pd.site_id DESC
          ";
      } else {
// We show them all
        $listing_sql .= "
          select " . $select_column_list . " 
                 p.products_id, 
                 p.manufacturers_id, 
                 p.products_price, 
                 p.sort_order,
                 p.products_price_offset,
                 p.products_small_sum, 
                 p.products_bflag, 
                 p.products_cflag, 
                 p.products_tax_class_id, 
                 pd.products_status, 
                 pd.site_id
          from ((" . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p )left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c )
          where p.products_id = p2c.products_id 
            and pd.products_id = p2c.products_id 
            and pd.language_id = '" . $languages_id . "' 
            and p2c.categories_id = '" . $current_category_id . "' order by pd.site_id DESC
          ";
      }
// We build the manufacturers Dropdown
      $filterlist_sql= "
        select * from (select distinct p.products_id, pd.site_id, pd.products_status, m.manufacturers_id as id, m.manufacturers_name as name
        from " . TABLE_PRODUCTS . " p, " .TABLE_PRODUCTS_DESCRIPTION . " pd, ". TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_MANUFACTURERS . " m 
        where p.products_id = pd.products_id 
          and p.manufacturers_id = m.manufacturers_id 
          and p.products_id = p2c.products_id 
          and p2c.categories_id = '" . $current_category_id . "' 
        order by pd.site_id DESC) c where site_id = ".SITE_ID." or site_id = 0
        group by products_id having c.products_status != '0' and c.products_status != '3' order by name";
    } 
    $listing_sql .= "
    ) p
    where site_id = '0'
       or site_id = '".SITE_ID."'
    group by products_id
    having p.products_status != '0' and p.products_status != '3'  
    ";
