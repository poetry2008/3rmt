<?php
/*
  $Id$
*/


  if ( ((!$_COOKIE['sort']) || (!ereg('1?[0-9][ad]', $_COOKIE['sort'])) || (substr($_COOKIE['sort'],0,1) > sizeof($column_list)))) {
    $listing_sql .= "order by sort_order " . ($sort_order == 'd' ? 'desc' : '') . ", products_name, products_id";
    
  } else {

    $sort_col = substr($_COOKIE['sort'], 0 , -1);
    $sort_order = substr($_COOKIE['sort'], -1);
    if(isset($_GET['page']) && $_GET['page'] !=""){
      $_SESSION['have_page_flag'] = true;
    }else{
      if($_SESSION['have_page_flag'] == true) {
        $_SESSION['have_page_flag'] =  false;
      }else{
         if (!empty($_COOKIE['sort_single'])) {
          setcookie('sort_single', ''); 
         } else {
           setcookie('sort', ''); 
           $sort_col = 'a';
           $sort_order = '100';
           tep_redirect($_SERVER["REQUEST_URI"]);
         }
      }
    }
    $listing_sql .= ' order by ';
    switch ($column_list[$sort_col-1]) {
      case 'PRODUCT_LIST_MODEL':
        $listing_sql .= "products_model " . ($sort_order == 'd' ? 'desc' : '') . ", products_name, products_id";
        break;
      case 'PRODUCT_LIST_NAME':
        $listing_sql .= "products_name " . ($sort_order == 'd' ? 'desc' : '') . ", products_id";
        break;
      case 'PRODUCT_LIST_MANUFACTURER':
        $listing_sql .= "manufacturers_name " . ($sort_order == 'd' ? 'desc' : '') . ", products_name, products_id";
        break;
      case 'PRODUCT_LIST_QUANTITY':
        $listing_sql .= "products_real_quantity + products_virtual_quantity as products_quantity, " . ($sort_order == 'd' ? 'desc' : '') . ", products_name, products_id";
        break;
      case 'PRODUCT_LIST_IMAGE':
        $listing_sql .= "products_name, products_id";
        break;
      case 'PRODUCT_LIST_WEIGHT':
        $listing_sql .= "products_weight " . ($sort_order == 'd' ? 'desc' : '') . ", products_name, products_id";
        break;
      case 'PRODUCT_LIST_PRICE':
        $listing_sql .= "products_price " . ($sort_order == 'd' ? 'desc' : '') . ", products_name, products_id";
        break;
      case 'PRODUCT_LIST_ORDERED':
        $listing_sql .= "products_ordered " . ($sort_order == 'd' ? 'desc' : '') . ", products_name, products_id";
        break;
      default:
        $listing_sql .= "sort_order " . ($sort_order == 'd' ? 'desc' : '') . ", products_name, products_id";
        break;
    }
  }
if($sort_order == 'a'){
$sort_type = 'd';
}else{
$sort_type = 'a';
}
  define('LISTING_DISPLAY_OPTION','表示形式:');
  define('LISTING_SORT_BY','並び替え:');
  define('LISTING_PRICE_LOW','価格が安い');
  define('LISTING_PRICE_HIGHT','価格が高い');
  define('LISTING_TITLE_A_TO_Z','タイトル A - Z');
  define('LISTING_TITLE_Z_TO_A','タイトル Z - A');
  define('LISTING_DEFAULT_UP','お勧め順(昇順)') ;
  define('LISTING_DEFAULT_DOWN','お勧め順(降順)') ;
  define('LISTING_PEOPLE_DOWN','人気順に並べる(昇順)') ;
  define('LISTING_PEOPLE_UP','人気順に並べる(降順)') ;
  
  $listing_numrows_sql = $listing_sql;
  $listing_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $listing_sql, $listing_numrows);
  // fix counted products
  $listing_numrows = tep_db_query($listing_numrows_sql);
  $listing_numrows = tep_db_num_rows($listing_numrows);
