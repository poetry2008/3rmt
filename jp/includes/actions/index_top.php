<?php
// the following cPath references come from application_top.php
  $category_depth = 'top';
  if (isset($cPath) && tep_not_null($cPath)) {

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
    } else {
      $category_parent_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . $current_category_id . "'");
      // ccdd
      $category_parent = tep_db_fetch_array($category_parent_query);
      if ($category_parent['total'] > 0) {
        $category_depth = 'nested'; // navigate through the categories
      } else {
        $category_depth = 'products'; // category has no products, but display the 'no products' message
      }
    }
  }
     //------ SEO TUNING  -----//
  if ($current_category_id) {
    // ccdd
    /*
    $seo_category_query = tep_db_query("
        select categories_name,
               seo_name,
               seo_description,
               categories_image3,
               categories_meta_text,
               categories_header_text,
               categories_footer_text,
               text_information,
               meta_keywords,
               meta_description, 
               categories_id 
        from " . TABLE_CATEGORIES_DESCRIPTION . " 
        where categories_id = '".$current_category_id."' 
          and language_id='" . $languages_id . "' 
          and site_id='" . SITE_ID . "'");
    $seo_category = tep_db_fetch_array($seo_category_query);
      */
    $seo_category = tep_get_category_by_id($current_category_id, SITE_ID, $languages_id);
  }
    
  if (isset($_GET['manufacturers_id'])) {
    // ccdd
    $seo_manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " where manufacturers_id = '".(int)$_GET['manufacturers_id']."'");
    $seo_manufacturers = tep_db_fetch_array($seo_manufacturers_query);
  }

  if (isset($_GET['tags_id']))
  {
    // ccdd
    $seo_tags_query = tep_db_query("select * from " . TABLE_TAGS. " where tags_id = '".$_GET['tags_id']."'");
    $seo_tags = tep_db_fetch_array($seo_tags_query);
}
   
   //------ SEO TUNING  -----//

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_DEFAULT);
