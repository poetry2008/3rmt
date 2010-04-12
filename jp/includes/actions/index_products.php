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
          $select_column_list .= 'p.products_quantity';
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
                 p.products_tax_class_id, 
                 pd.site_id,
                 IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, 
                 IF(s.status, s.specials_new_products_price, p.products_price) as final_price 
          from (" . TABLE_PRODUCTS . " p, " .  TABLE_PRODUCTS_DESCRIPTION . "
              pd, " . TABLE_MANUFACTURERS . " m, " .
              TABLE_PRODUCTS_TO_CATEGORIES . " p2c) left join " .
              TABLE_SPECIALS . " s on p.products_id = s.products_id 
          where p.products_status = '1' 
            and p.manufacturers_id = m.manufacturers_id 
            and m.manufacturers_id = '" .  $_GET['manufacturers_id'] . "' 
            and p.products_id = p2c.products_id 
            and pd.products_id = p2c.products_id 
            and pd.language_id = '" . $languages_id . "' 
            and p2c.categories_id = '" . $_GET['filter_id'] . "' 
          order by pd.site_id DESC"
            ;
      } else {
// We show them all
        $listing_sql.= "
        select " . $select_column_list . " 
              p.products_id, 
              p.manufacturers_id, 
              p.products_price, 
              p.products_tax_class_id, 
              pd.site_id,
              IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, 
              IF(s.status, s.specials_new_products_price, p.products_price) as final_price 
        from (" . TABLE_PRODUCTS . " p, " .  TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m ) left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id 
        where p.products_status = '1' 
          and pd.products_id = p.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.manufacturers_id = m.manufacturers_id 
          and m.manufacturers_id = '" . $_GET['manufacturers_id'] . "' 
        order by pd.site_id DESC
          ";
        /*
        $listing_sql = "
        select " . $select_column_list . " 
              p.products_id, 
              p.manufacturers_id, 
              p.products_price, 
              p.products_tax_class_id, 
              IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, 
              IF(s.status, s.specials_new_products_price, p.products_price) as final_price 
        from (" . TABLE_PRODUCTS . " p, " .  TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m ) left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id 
        where p.products_status = '1' 
          and pd.products_id = p.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.manufacturers_id = m.manufacturers_id 
          and m.manufacturers_id = '" . $_GET['manufacturers_id'] . "' 
          and pd.site_id = ".SITE_ID;
          */
      }
// We build the categories-dropdown
      $filterlist_sql = "
        select *
        from(
          select distinct c.categories_id as id, 
                          cd.categories_name as name,
                          cd.site_id
          from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
          where p.products_status = '1' 
            and p.products_id = p2c.products_id 
            and p2c.categories_id = c.categories_id 
            and p2c.categories_id = cd.categories_id 
            and cd.language_id = '" . $languages_id . "' 
            and p.manufacturers_id = '" .  $_GET['manufacturers_id'] . "' 
          order by cd.categories_name
        ) c
        where site_id = 0
           or site_id = " . SITE_ID . "
        group by id
        order by name
        ";
      /*
      $filterlist_sql = "
        select distinct c.categories_id as id, 
                        cd.categories_name as name 
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where p.products_status = '1' 
          and p.products_id = p2c.products_id 
          and p2c.categories_id = c.categories_id 
          and p2c.categories_id = cd.categories_id 
          and cd.language_id = '" . $languages_id . "' 
          and p.manufacturers_id = '" .  $_GET['manufacturers_id'] . "' 
          and cd.site_id = ".SITE_ID." 
        order by cd.categories_name";
        */
    } else {
// show the products in a given categorie
      if (isset($_GET['filter_id'])) {
// We are asked to show only specific catgeory
        $listing_sql .= "
          select " . $select_column_list . " 
                 p.products_id, 
                 p.manufacturers_id, 
                 p.products_price, 
                 p.products_tax_class_id, 
                 pd.site_id,
                 IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, 
                 IF(s.status, s.specials_new_products_price, p.products_price) as final_price 
          from ( " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c  ) left join " .  TABLE_SPECIALS . " s on p.products_id = s.products_id 
          where p.products_status = '1' 
            and p.manufacturers_id = m.manufacturers_id 
            and m.manufacturers_id = '" .  $_GET['filter_id'] . "' 
            and p.products_id = p2c.products_id 
            and pd.products_id = p2c.products_id 
            and pd.language_id = '" . $languages_id . "' 
            and p2c.categories_id = '" . $current_category_id . "' 
          order by pd.site_id DESC
            ";
        /*
        $listing_sql = "
          select " . $select_column_list . " 
                 p.products_id, 
                 p.manufacturers_id, 
                 p.products_price, 
                 p.products_tax_class_id, 
                 IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, 
                 IF(s.status, s.specials_new_products_price, p.products_price) as final_price 
          from ( " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c  ) left join " .  TABLE_SPECIALS . " s on p.products_id = s.products_id 
          where p.products_status = '1' 
            and p.manufacturers_id = m.manufacturers_id 
            and m.manufacturers_id = '" .  $_GET['filter_id'] . "' 
            and p.products_id = p2c.products_id 
            and pd.products_id = p2c.products_id 
            and pd.language_id = '" . $languages_id . "' 
            and p2c.categories_id = '" . $current_category_id . "' 
            and pd.site_id = ".SITE_ID;
            */
      } else {
// We show them all
        $listing_sql .= "
          select " . $select_column_list . " 
                 p.products_id, 
                 p.manufacturers_id, 
                 p.products_price, 
                 p.products_bflag, 
                 p.products_cflag, 
                 p.products_tax_class_id, 
                 pd.site_id,
                 IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, 
                 IF(s.status, s.specials_new_products_price, p.products_price) as final_price 
          from ((" . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p )left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ) left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id 
          where p.products_status = '1' 
            and p.products_id = p2c.products_id 
            and pd.products_id = p2c.products_id 
            and pd.language_id = '" . $languages_id . "' 
            and p2c.categories_id = '" . $current_category_id . "' 
          order by pd.site_id DESC
            ";
        /*
        $listing_sql = "
          select " . $select_column_list . " 
                 p.products_id, 
                 p.manufacturers_id, 
                 p.products_price, 
                 p.products_bflag, 
                 p.products_cflag, 
                 p.products_tax_class_id, 
                 IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, 
                 IF(s.status, s.specials_new_products_price, p.products_price) as final_price 
          from ((" . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p )left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ) left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id 
          where p.products_status = '1' 
            and p.products_id = p2c.products_id 
            and pd.products_id = p2c.products_id 
            and pd.language_id = '" . $languages_id . "' 
            and p2c.categories_id = '" . $current_category_id . "' 
            and pd.site_id = ".SITE_ID;
            */
      }
// We build the manufacturers Dropdown
      $filterlist_sql= "
        select distinct m.manufacturers_id as id, 
                        m.manufacturers_name as name
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_MANUFACTURERS . " m 
        where p.products_status = '1' 
          and p.manufacturers_id = m.manufacturers_id 
          and p.products_id = p2c.products_id 
          and p2c.categories_id = '" . $current_category_id . "' 
        order by m.manufacturers_name";
    } 
    $listing_sql .= "
    ) p
    where site_id = '0'
       or site_id = '".SITE_ID."'
    group by products_id
    ";

    if (!isset($_GET['sort'])) $_GET['sort']=NULL;
    if ( (!$_GET['sort']) || (!ereg('[1-9][ad]', $_GET['sort'])) || (substr($_GET['sort'],0,1) > sizeof($column_list)) ) {
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
?> 
	<td valign="top" id="contents">
		<h1 class="pageHeading_long"><?php
	if (isset($cPath_array)) {
		echo $seo_category['categories_name'];
	} elseif ($_GET['manufacturers_id']) {
		echo $seo_manufacturers['manufacturers_name'];
	} else {
		echo HEADING_TITLE;
	}
?></h1>
		<p class="comment"><?php echo $seo_category['categories_header_text']; //seoフレーズ ?></p>
		<h2 class="line"><?php
	if(isset($_GET['cPath']) && $_GET['cPath']) {
		$categories_path = explode('_', $_GET['cPath']);
		//大カテゴリの画像を返す
    // ccdd
    /*
		$_categories_query = tep_db_query("
        select categories_name 
        from ".TABLE_CATEGORIES_DESCRIPTION." 
        where categories_id = '".$categories_path[0]."' 
          and language_id = '".$languages_id."' 
          and site_id='".SITE_ID."'
    ");
		$_categories = tep_db_fetch_array($_categories_query);
    */
    $_categories = tep_get_category_by_id($categories_path[0], SITE_ID, $languages_id);
		echo $_categories['categories_name'];
	} else {
		echo 'RMT：ゲーム通貨・アイテム・アカウント';
	}
?></h2>
      <?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?> </td> 
      <?php
  // Add Color =============================================================================
