<?php
if ( isset($_GET['pID']) && ($_GET['pID']) && (!$_POST) ) {
      $site_id = isset($_GET['site_id']) ?$_GET['site_id']:0;
      $product_query = tep_db_query("
          select pd.products_name, 
                 pd.products_description, 
                 pd.products_url, 
                 p.products_id,
                 p.option_type, 
                 p.products_quantity, 
                 p.products_model, 
                 p.products_image,
                 p.products_image2,
                 p.products_image3, 
                 p.products_price, 
                 p.products_price_offset,
                 p.products_weight, 
                 p.products_date_added, 
                 p.products_last_modified, 
                 date_format(p.products_date_available, '%Y-%m-%d') as products_date_available, 
                 p.products_status, 
                 p.products_tax_class_id, 
                 p.manufacturers_id, 
                 p.products_bflag, 
                 p.products_cflag, 
                 p.products_small_sum 
          from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
          where p.products_id = '" . $_GET['pID'] . "' 
            and p.products_id = pd.products_id 
            and pd.language_id = '" . $languages_id . "' 
            and pd.site_id = '".(tep_products_description_exist($_GET['pID'], $site_id, $languages_id)?$site_id:0)."'");
      $product = tep_db_fetch_array($product_query);
      $pInfo = new objectInfo($product);
    } elseif ($_POST) {

      $pInfo = new objectInfo($_POST);
      $products_name = $_POST['products_name'];
      $products_description = $_POST['products_description'];

      $products_url = $_POST['products_url'];
      $site_id = isset($_POST['site_id']) ?$_POST['site_id']:0;
    } else {
      $pInfo = new objectInfo(array());
      $site_id = isset($_GET['site_id']) ?$_GET['site_id']:0;
    }
