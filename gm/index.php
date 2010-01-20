<?php
/*
  $Id: default.php,v 1.1.1.1 2003/02/20 01:03:53 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// the following cPath references come from application_top.php
  $category_depth = 'top';
  if (isset($cPath) && tep_not_null($cPath)) {
    $sql = "select * from " . TABLE_CATEGORIES . " where categories.categories_id = '" . $cPath . "'";
    $category_query = tep_db_query($sql);
    $category = tep_db_fetch_array($category_query);
    if ($category && $category['parent_id'] == 0) {
      setcookie('quick_categories_id', $category['categories_id'], time()+(86400*30), '/');
    }

    $categories_products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . $current_category_id . "'");
    $cateqories_products = tep_db_fetch_array($categories_products_query);
    if ($cateqories_products['total'] > 0) {
      $category_depth = 'products'; // display products
    } else {
      $category_parent_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . $current_category_id . "'");
      $category_parent = tep_db_fetch_array($category_parent_query);
      if ($category_parent['total'] > 0) {
        $category_depth = 'nested'; // navigate through the categories
      } else {
        $category_depth = 'products'; // category has no products, but display the 'no products' message
      }
    }
  }
     //------ SEO TUNING  -----//
	 
    $seo_category_query = tep_db_query("select categories_name,seo_name,seo_description,categories_image3,categories_meta_text,categories_header_text,categories_footer_text,text_information, meta_keywords,meta_description, categories_id from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '".$current_category_id."' and language_id='" . $languages_id . "'");
    $seo_category = tep_db_fetch_array($seo_category_query);
    
    $seo_manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " where manufacturers_id = '".$HTTP_GET_VARS['manufacturers_id']."'");
    $seo_manufacturers = tep_db_fetch_array($seo_manufacturers_query);
   
   //------ SEO TUNING  -----//

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_DEFAULT);
?>
<?php page_head();?>
</head>
<body<?=$body_option?>>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!--body -->
<div id="main">
<!-- left_navigation //-->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
</div>
<!-- left_navigation_eof //-->

      <!-- body_text //--> 
      <?php
if (!isset($HTTP_GET_VARS['tags_id'])) $HTTP_GET_VARS['tags_id']= NULL;
if (!isset($HTTP_GET_VARS['action'])) $HTTP_GET_VARS['action']= NULL;
  if ($category_depth == 'nested') {
    $category_query = tep_db_query("select cd.categories_name, c.categories_image from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . $current_category_id . "' and site_id = '" . SITE_ID . "' and cd.categories_id = '" . $current_category_id . "' and cd.language_id = '" . $languages_id . "'");
    $category = tep_db_fetch_array($category_query);
?> 
<div id="content">
      <?php  
	if (isset($cPath_array)) {
       echo '<div class="headerNavigation">'.$breadcrumb->trail(' &raquo; ').'</div><h1 class="pageHeading">'.$seo_category['categories_name'].'</h1>'; 
	  } elseif ($HTTP_GET_VARS['manufacturers_id']) {
       echo '<div class="headerNavigation">'.$breadcrumb->trail(' &raquo; ').'</div><h1 class="pageHeading">'.$seo_manufacturers['manufacturers_name'].'</h1>';
      }
?> 
      <!-- heading title eof//-->
	  <p><?php echo $seo_category['categories_header_text']; //seoフレーズ ?></p>
      <table border="0" width="95%" cellspacing="3" cellpadding="3"> 
        <tr> 
          <?php
    if (isset($cPath) && ereg('_', $cPath)) {
// check to see if there are deeper categories within the current category
      $category_links = array_reverse($cPath_array);
      for($i=0, $n=sizeof($category_links); $i<$n; $i++) {
        $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id 
          from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
          where c.parent_id = '" . $category_links[$i] . "' and site_id = '" . SITE_ID . "' and c.categories_id = cd.categories_id and cd.language_id = '" . $languages_id . "' 
          order by sort_order, cd.categories_name");
        if (tep_db_num_rows($categories_query) < 1) {
          // do nothing, go through the loop
        } else {
          break; // we've found the deepest category the customer is in
        }
      }
    } else {
      $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id 
        from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where c.parent_id = '" . $current_category_id . "' and site_id = '" . SITE_ID . "' and c.categories_id = cd.categories_id and cd.language_id = '" . $languages_id . "' 
        order by sort_order, cd.categories_name");
    }

    $rows = 0;
    while ($categories = tep_db_fetch_array($categories_query)) {
	  $rows++;
      $cPath_new = tep_get_path($categories['categories_id']);
      $width = (int)(100 / MAX_DISPLAY_CATEGORIES_PER_ROW) . '%';
      echo '                <td class="smallText" style="width:'.$width.'" align="center"><h3 class="Tlist"><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">' . tep_image(DIR_WS_IMAGES . $categories['categories_image'], $categories['categories_name'], SUBCATEGORY_IMAGE_WIDTH, SUBCATEGORY_IMAGE_HEIGHT) ;
	                           if(tep_not_null($categories['categories_image'])) { echo '<br>' ; } 
							     echo $categories['categories_name'] . '</a></h3></td>' . "\n";
      if ((($rows / MAX_DISPLAY_CATEGORIES_PER_ROW) == floor($rows / MAX_DISPLAY_CATEGORIES_PER_ROW)) && ($rows != tep_db_num_rows($categories_query))) {
        echo '              </tr>' . "\n";
        echo '              <tr>' . "\n";
      }
	}
?> 
        </tr> 
      </table>
	  <br>
	  <div id="information">
	  <p><?php echo $seo_category['categories_footer_text']; //seoフレーズ ?></p>
	  </div>
	  <br>
      <?php $new_products_category_id = $current_category_id; include(DIR_WS_MODULES .'new_products.php'); ?>
	  <br>
	  <p><?php echo $seo_category['seo_description']; //seoフレーズ ?></p>
	  <br>
</div> 
<div id="r_menu">
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
  <?php
  } elseif ($HTTP_GET_VARS['tags_id']) { 
// create column list
    $define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                         'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                         'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER, 
                         'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE, 
                         'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY, 
                         'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT, 
                         'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE, 
                         'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW,
                         'PRODUCT_LIST_ORDERED' => PRODUCT_LIST_ORDERED,
    );
    asort($define_list);

    $column_list = array();
    reset($define_list);
    while (list($column, $value) = each($define_list)) {
      if ($value) $column_list[] = $column;
    }
  if (tep_session_is_registered('customer_id'))
  {
    $products_query = "select *, p.products_id , IF(s.status,
      s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS_TO_TAGS . " as
        p2t join ". TABLE_PRODUCTS . " as p on p2t.products_id = p.products_id left
        join " . TABLE_PRODUCTS_DESCRIPTION . " as pd on p.products_id =
        pd.products_id left join " . TABLE_SPECIALS . " as s
        on p.products_id = s.products_id 
        where p2t.tags_id = " . intval($HTTP_GET_VARS['tags_id']);
  } else {
    $products_query = "select *, p.products_id, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS_TO_TAGS . " as
        p2t join ". TABLE_PRODUCTS . " as p on p2t.products_id = p.products_id left
        join " . TABLE_PRODUCTS_DESCRIPTION . " as pd on p.products_id =
        pd.products_id left join " . TABLE_SPECIALS . " as s
         on p.products_id = s.products_id where p2t.tags_id = " . $HTTP_GET_VARS['tags_id'];
  } 

     $listing_sql = $products_query;
if (!isset($HTTP_GET_VARS['sort'])) $HTTP_GET_VARS['sort']= NULL;
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
<div id="cgi">
<div id="body_text">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
  <?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?>
</div>
</div>
  <?php
  } elseif ($category_depth == 'products' || $HTTP_GET_VARS['manufacturers_id']) {
// create column list
    $define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                         'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                         'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER, 
                         'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE, 
                         'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY, 
                         'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT, 
                         'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE, 
                         'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW,
                         'PRODUCT_LIST_ORDERED' => PRODUCT_LIST_ORDERED,
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

// show the products of a specified manufacturer
    if (isset($HTTP_GET_VARS['manufacturers_id'])) {
      if (isset($HTTP_GET_VARS['filter_id'])) {
// We are asked to show only a specific category
        $listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from (" . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c) left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . $HTTP_GET_VARS['manufacturers_id'] . "' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . $languages_id . "' and p2c.categories_id = '" . $HTTP_GET_VARS['filter_id'] . "'";
      } else {
// We show them all
        $listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from (" . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m ) left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and pd.products_id = p.products_id and pd.language_id = '" . $languages_id . "' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . $HTTP_GET_VARS['manufacturers_id'] . "'";
      }
// We build the categories-dropdown
      $filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where p.products_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = cd.categories_id and cd.language_id = '" . $languages_id . "' and p.manufacturers_id = '" . $HTTP_GET_VARS['manufacturers_id'] . "' order by cd.categories_name";
    } else {
// show the products in a given categorie
      if (isset($HTTP_GET_VARS['filter_id'])) {
// We are asked to show only specific catgeory
        $listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from ( " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c  ) left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . $HTTP_GET_VARS['filter_id'] . "' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . $languages_id . "' and p2c.categories_id = '" . $current_category_id . "'";
      } else {
// We show them all
        $listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_bflag, p.products_cflag, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from ((" . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p )left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ) left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . $languages_id . "' and p2c.categories_id = '" . $current_category_id . "'";
      }
// We build the manufacturers Dropdown
      $filterlist_sql= "select distinct m.manufacturers_id as id, m.manufacturers_name as name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_MANUFACTURERS . " m where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and p.products_id = p2c.products_id and p2c.categories_id = '" . $current_category_id . "' order by m.manufacturers_name";
    }

   if (!isset($HTTP_GET_VARS['sort'])) $HTTP_GET_VARS['sort']= NULL; 
    if ( (!$HTTP_GET_VARS['sort']) || (!ereg('[1-9][ad]', $HTTP_GET_VARS['sort'])) || (substr($HTTP_GET_VARS['sort'],0,1) > sizeof($column_list)) ) {
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
<div id="cgi">
<div id="body_text">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading">
<?php
	if($HTTP_GET_VARS['cPath']) {
		$categories_path = explode('_', $HTTP_GET_VARS['cPath']);
		//大カテゴリの画像を返す
		$_categories_query = tep_db_query("select categories_name from categories_description where categories_id = '".$categories_path[0]."' and language_id = '".$languages_id."'");
		$_categories = tep_db_fetch_array($_categories_query);
		echo $_categories['categories_name'];
	} else {
		echo 'RMT：ゲームマネー・アイテム・アカウント';
	}
?>		  
</h1>
<h2 align="right">
<?php
  if (isset($cPath_array)) {
       echo $seo_category['categories_name']; 
  
   } elseif ($HTTP_GET_VARS['manufacturers_id']) {
       echo $seo_manufacturers['manufacturers_name'];

   } else {
       echo HEADING_TITLE ;
 }
?></h2>
<?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?>
</div>
</div>	  
	  
      <?php
  // Add Color =============================================================================
  } elseif($HTTP_GET_VARS['colors'] && !empty($HTTP_GET_VARS['colors'])) {
	
	$colors_title_query = tep_db_query("select color_name from ".TABLE_COLOR." where color_id = '".(int)$HTTP_GET_VARS['colors']."'");
	$colors_title = tep_db_fetch_array($colors_title_query);
	
	$listing_sql = "select pd.products_name, p.products_image, cp.color_id, cp.color_image, p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_quantity, pd.products_description from " . TABLE_PRODUCTS_DESCRIPTION . " pd, ".TABLE_COLOR_TO_PRODUCTS." cp, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where p.products_status = '1' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . $languages_id . "' and cp.products_id = p.products_id and cp.color_id = '".(int)$HTTP_GET_VARS['colors']."'";
	$listing_sql .= " order by pd.products_name";
	
	//View
	echo '<td valign="top" id="contents">';
	echo '<h1 class="pageHeading_long">'.HEADING_COLOR_TITLE.$colors_title['color_name'].'</h1>';
	include(DIR_WS_MODULES . FILENAME_COLOR_LISTING);
	echo '</td>';
	//======================================================================================
  // maker
  } elseif($HTTP_GET_VARS['action'] && $HTTP_GET_VARS['action'] == 'select' && 0) { // select my game 
?> 
	  <td valign="top" id="contents">
      <?php include(DIR_WS_MODULES . 'select_categories.php');?>
    </td>
	  <!-- body_text_eof //--> 
      <td width="<?php echo BOX_WIDTH; ?>" valign="top" class="right_colum_border">
      <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--></td> 
<?php } else { // default page ?>
 <div id="content">
   <h2 class="index_h2">はじめてRMTゲームマネーをご利用いただくお客様へ</h2> 
		  <?php 
		        $contents = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '11' and site_id = '" . SITE_ID . "'");//top
		        $result = tep_db_fetch_array($contents) ;
		        
				echo $result['text_information'];
				//include("ajax/php/tab.php") ;
				//include(DIR_WS_MODULES . FILENAME_LATEST_NEWS);
				//include(DIR_WS_MODULES . 'new_products.php');
				//include(DIR_WS_MODULES . 'upcoming_products.php');
				//include(DIR_WS_MODULES . 'categories_banner_text.php');

		  ?>
</div>		  
<!-- body_text_eof //--> 



<!--column_right -->
<div id="r_menu">
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
      <?php
  }
?> 

<!-- footer //--> 
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
<!-- footer_eof //--> 

</div>
<!--body_EOF// -->

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
