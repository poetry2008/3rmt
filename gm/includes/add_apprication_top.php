<?php
/*
  add_apprication_top.php v1.0.0 ds-style
  
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
     //------ SEO TUNING  -----//
	 
    $seo_category_query = tep_db_query("select categories_name,categories_image3,categories_meta_text, categories_id from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '".$current_category_id."' and language_id='" . $languages_id . "'");
	$seo_category = tep_db_fetch_array($seo_category_query);
    
if (!isset($HTTP_GET_VARS['manufacturers_id'])) $HTTP_GET_VARS['manufacturers_id']= NULL;
	$seo_manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " where manufacturers_id = '".$HTTP_GET_VARS['manufacturers_id']."'");
    $seo_manufacturers = tep_db_fetch_array($seo_manufacturers_query);
   
   
 if (isset($cPath_array)) {
       $header_title =  $seo_category['categories_name'] ;
	   $header_title.= tep_not_null($seo_category['categories_meta_text']) ? '-' . $seo_category['categories_meta_text'] : C_TITLE ; 
	   $header_text = $seo_category['categories_name'] . '&nbsp;&nbsp;&nbsp;' .strip_tags($seo_category['categories_meta_text']);

   } elseif ($HTTP_GET_VARS['manufacturers_id']) {
       $header_title = $seo_manufacturers['manufacturers_name'].'-' .C_TITLE;
       $header_text = $seo_manufacturers['manufacturers_name'] ;
   } else {
       $header_title =  C_TITLE ;
       $header_text = C_DESCRIPTION ;
 }
   //------ SEO TUNING  -----//
   
////
// Get Categories_image & subcategories_name
  function ds_tep_get_categories($products_id, $return) {
    global $languages_id;
	
	$categories_path = tep_get_product_path($products_id);
	$categories_path_array = explode("_", $categories_path);
	
	if($return == 1) {
	  //大カテゴリの画像を返す
	  $categories_query = tep_db_query("select categories_name from categories_description where categories_id = '".$categories_path_array[0]."'");
	  $categories = tep_db_fetch_array($categories_query);
	  
	  $creturn = $categories['categories_name'];
	} elseif($return == 2) {
	  //中カテゴリ名を返す
if (!isset($categories_path_array[1])) $categories_path_array[1]= NULL;//del notice
	  $categories_query = tep_db_query("select categories_name from categories_description where categories_id = '".$categories_path_array[1]."' and language_id = '".$languages_id."'");
	  $categories = tep_db_fetch_array($categories_query);
	  
	  $creturn = $categories['categories_name'];
	}
	
	return $creturn;
  }

////
// Get Point
  function ds_tep_get_point_value($products_id) {
	if ($new_price = tep_get_products_special_price($products_id)) {
	  $price = $new_price;
	} else {
      $query = tep_db_query("select products_price from products where products_id = '".$products_id."'");
	  $result = tep_db_fetch_array($query);
	  
	  $price = $result['products_price'];
	}
	
	//ポイント計算
	$point_value = (int)($price * MODULE_ORDER_TOTAL_POINT_FEE);
	
	return $point_value;
  }

////
// Options stock check
  function tep_check_opstock($options_stock, $orders_quantity) {
    $stock_left = $options_stock - $orders_quantity;
    $out_of_stock = '';

    if ($stock_left < 0) {
      $out_of_stock = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
    }

    return $out_of_stock;
  }

////
// 買い取り商品が存在するか？
  function ds_count_bflag() {
    global $cart;
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
	  if($products[$i]['bflag'] == '1') {
	    return 'View';
	  }
	}
	
	return false;
  }
  
////
// 在庫調査  
  function ds_replace_plist($pID, $qty, $string) {
    $query = tep_db_query("select * from products where products_id = '".(int)tep_get_prid($pID)."'");
	$result = mysql_fetch_array($query);
	
	if($qty < 1) {
	  if($result['products_bflag'] == '1') {
	    # 買い取り商品
		return '<span class="markProductOutOfStock">一時停止</span>';
	  } else {
	    # 通常商品
	    return '<span class="markProductOutOfStock">在庫切れ</span>';
	  }
	} else {
	  return $string;
	}
  }
  
// SESSION REGISTER
if (!isset($HTTP_GET_VARS['ajax'])) $HTTP_GET_VARS['ajax']= NULL;
switch($HTTP_GET_VARS['ajax']){
  case 'on' :
    $ajax = 'on' ;
    break;
  case 'off' :
    $ajax = 'off' ;
    break;
}

tep_session_register('ajax');

# 注文上限金額設定
  if(substr(basename($PHP_SELF),0,9) == 'checkout_') {
    if(DS_LIMIT_PRICE < $cart->show_total()) {
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART, 'limit_error=true'));
    }
  }
?>
