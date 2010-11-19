<?php
/*
  $Id$
*/


  if ( (!$_COOKIE['sort']) || (!ereg('1?[0-9][ad]', $_COOKIE['sort'])) || (substr($_COOKIE['sort'],0,1) > sizeof($column_list)) ) {
    /*for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
      if ($column_list[$col] == 'PRODUCT_LIST_NAME') {
        $_COOKIE['sort'] = $col+1 . 'a';
        $listing_sql .= " order by products_name";
        break;
      }
    }*/
    $listing_sql .= "order by sort_order " . ($sort_order == 'd' ? 'desc' : '') . ", products_name";
  } else {
    $sort_col = substr($_COOKIE['sort'], 0 , -1);
    $sort_order = substr($_COOKIE['sort'], -1);
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
        $listing_sql .= "products_price " . ($sort_order == 'd' ? 'desc' : '') . ", products_name";
        break;
      case 'PRODUCT_LIST_ORDERED':
        $listing_sql .= "products_ordered " . ($sort_order == 'd' ? 'desc' : '') . ", products_name";
        break;
      default:
        $listing_sql .= "sort_order " . ($sort_order == 'd' ? 'desc' : '') . ", products_name";
        break;
    }
  }
  //define('PRODUCT_SORT_BY_CHARACTER', 'アルファベット順');
  //define('PRODUCT_SORT_BY_PRICE', '価格順');
  //define('PRODUCT_SORT_BY_POPULAR', '人気順');
  
  define('LISTING_DISPLAY_OPTION','表示形式:');
  define('LISTING_SORT_BY','並び替え:');
  define('LISTING_PRICE_LOW','価格が安い');
  define('LISTING_PRICE_HIGHT','価格が高い');
  define('LISTING_TITLE_A_TO_Z','タイトル A - Z');
  define('LISTING_TITLE_Z_TO_A','タイトル Z - A');
  
  ?>
<script>

</script>
<?php
  $listing_numrows_sql = $listing_sql;
  $listing_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $listing_sql, $listing_numrows);
  // fix counted products
  $listing_numrows = tep_db_query($listing_numrows_sql);
  $listing_numrows = tep_db_num_rows($listing_numrows);