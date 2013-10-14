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
    
    if (defined('NEW_TYPE_SYMBOL')) {
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
    $category_query = tep_db_query($sql);
    $category = tep_db_fetch_array($category_query);
    if ($category && $category['parent_id'] == 0) {
      setcookie('quick_categories_id', $category['categories_id'], time()+(86400*30), '/');
    }

    $categories_products_sql = "select count(*) as total from 
      (
       SELECT count( pd.products_id ) AS pcount, pd.products_id as p_id 
        FROM ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_PRODUCTS_DESCRIPTION." pd
        WHERE p2c.categories_id = '".$current_category_id."'
        AND p2c.products_id = pd.products_id
        AND (
          pd.site_id = '0'
          OR pd.site_id = '".SITE_ID."'
          )
        GROUP BY pd.products_id
        ORDER BY pd.site_id DESC
      ) s ,".TABLE_PRODUCTS_DESCRIPTION." pd2 where pd2.products_id = s.p_id  
      and if(pcount > 1 ,pd2.site_id = '".SITE_ID."',pd2.site_id = '0' )
      and pd2.products_status !=0 
      and pd2.products_status !=3";
    $categories_products_query = tep_db_query($categories_products_sql);
    $cateqories_products = tep_db_fetch_array($categories_products_query);
    if ($cateqories_products['total'] > 0) {
      //判断该分类下是否有商品 
      $category_depth = 'products'; // display products
      
      if ($_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS  > $cateqories_products['total'] + MAX_DISPLAY_SEARCH_RESULTS) {
        forward404();
      }
    } else {
      $category_parent_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . $current_category_id . "'");
      $category_parent = tep_db_fetch_array($category_parent_query);
      if ($category_parent['total'] > 0) {
        $category_depth = 'nested'; // navigate through the categories
      } else {
        $category_depth = 'products'; // category has no products, but display the 'no products' message
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
    $m_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = pd.products_id and p.manufacturers_id = '" . $_GET['manufacturers_id'] . "' and pd.products_status != 0 and pd.products_status != 3 and site_id = ".SITE_ID);
    $m = tep_db_fetch_array($m_query);
    check_uri('/sort=(\d+)/');
    if ($_GET['page'] * MAX_DISPLAY_SEARCH_RESULTS > $m['total'] + MAX_DISPLAY_SEARCH_RESULTS) {
      forward404();
    }
    $seo_manufacturers_query = tep_db_query("
      select manufacturers_id, manufacturers_name 
      from " . TABLE_MANUFACTURERS . " 
      where manufacturers_id = '".(int)$_GET['manufacturers_id']."'");
    $seo_manufacturers = tep_db_fetch_array($seo_manufacturers_query);
  }

  if (isset($_GET['tags_id']))
  {
    $seo_tags_query = tep_db_query("
      select * 
      from " . TABLE_TAGS. " 
      where tags_id = '".$_GET['tags_id']."'
    ");
    $seo_tags = tep_db_fetch_array($seo_tags_query);
    check_uri('/tags_id=/');
  }
   
   //------ SEO TUNING  -----//

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_DEFAULT);
 
  if (strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
    $_SESSION['history_url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
  }
