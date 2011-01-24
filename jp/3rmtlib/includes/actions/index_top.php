<?php
/*
  $Id$
 */
// the following cPath references come from application_top.php
  $category_depth = 'top';
  if (isset($cPath) && tep_not_null($cPath)) {
    //rmt/c-168_198_page0.html => 404
    //rmt/c-168_198_page0*.html => 404
    check_uri('/page0/');
    //rmt/c-168_198_page1.html => 404
    check_uri('/page1\.html/');
    
    if (SITE_ID <= 3) {
      if (!empty($cPath_array)) {
        foreach ($cPath_array as $cpkey => $cpvalue) {
          $ex_ca_query = tep_db_query("select * from ".TABLE_CATEGORIES." where categories.categories_id = '".$cpvalue."'"); 
          if (!tep_db_num_rows($ex_ca_query)) {
            forward404();
            break; 
          }
        }
      }
    }
    
    $sql = "select * from " . TABLE_CATEGORIES . " where categories.categories_id = '" . $cPath . "'";
    // ccdd
    $category_query = tep_db_query($sql);
    $category = tep_db_fetch_array($category_query);
    if ($category && $category['parent_id'] == 0) {
      setcookie('quick_categories_id', $category['categories_id'], time()+(86400*30), '/');
    }

    $categories_products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . $current_category_id . "'");
    // ccdd
    $cateqories_products = tep_db_fetch_array($categories_products_query);
    if ($cateqories_products['total'] > 0) {
      $category_depth = 'products'; // display products
      
      if (!defined('URL_SUB_SITE_ENABLED')) {
        //check_uri('/page=(\d+)/');
      }
      
      if ($_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS > $cateqories_products['total'] + MAX_DISPLAY_SEARCH_RESULTS) {
        forward404();
      }
    } else {
      $category_parent_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . $current_category_id . "'");
      // ccdd
      $category_parent = tep_db_fetch_array($category_parent_query);
      if ($category_parent['total'] > 0) {
        $category_depth = 'nested'; // navigate through the categories
      } else {
        $category_depth = 'products'; // category has no products, but display the 'no products' message
        //echo $_SERVER['REQUEST_URI'];
        check_uri('/page=(\d+)/');
        if ($_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS > $cateqories_products['total'] + MAX_DISPLAY_SEARCH_RESULTS) {
          forward404();
        }
      }
    }
  }
     //------ SEO TUNING  -----//
  if ($current_category_id) {
    $seo_category = tep_get_category_by_id($current_category_id, SITE_ID, $languages_id);
  }
  if (isset($_GET['manufacturers_id'])) {
    $m_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = pd.products_id and p.manufacturers_id = '" . $_GET['manufacturers_id'] . "' and pd.products_status != 0 and site_id = ".SITE_ID);
    // ccdd
    $m = tep_db_fetch_array($m_query);
    check_uri('/sort=(\d+)/');
    if ($_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS > $m['total'] + MAX_DISPLAY_SEARCH_RESULTS) {
      forward404();
    }
    // ccdd
    $seo_manufacturers_query = tep_db_query("
      select manufacturers_id, manufacturers_name 
      from " . TABLE_MANUFACTURERS . " 
      where manufacturers_id = '".(int)$_GET['manufacturers_id']."'");
    $seo_manufacturers = tep_db_fetch_array($seo_manufacturers_query);
  }

  if (isset($_GET['tags_id']))
  {
    // ccdd
    $seo_tags_query = tep_db_query("
      select * 
      from " . TABLE_TAGS. " 
      where tags_id = '".$_GET['tags_id']."'
    ");
    $seo_tags = tep_db_fetch_array($seo_tags_query);
  }
   
   //------ SEO TUNING  -----//

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_DEFAULT);
