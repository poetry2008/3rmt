<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ADVANCED_SEARCH_RESULT);

  $error = 0; // reset error flag to false
  $errorno = 0;

  if ( (isset($_GET['keywords']) && empty($_GET['keywords'])) &&
       (isset($_GET['dfrom']) && (empty($_GET['dfrom']) || ($_GET['dfrom'] == DOB_FORMAT_STRING))) &&
       (isset($_GET['dto']) && (empty($_GET['dto']) || ($_GET['dto'] == DOB_FORMAT_STRING))) &&
       (isset($_GET['pfrom']) && empty($_GET['pfrom'])) &&
       (isset($_GET['pto']) && empty($_GET['pto'])) ) {
    $errorno += 1;
    $error = 1;
  }
  if (!isset($_GET['dfrom'])) $_GET['dfrom'] = NULL;
  $dfrom_to_check = (($_GET['dfrom'] == DOB_FORMAT_STRING) ? '' : $_GET['dfrom']);
  if (!isset($_GET['dto'])) $_GET['dto'] = NULL;
  $dto_to_check = (($_GET['dto'] == DOB_FORMAT_STRING) ? '' : $_GET['dto']);

  if (strlen($dfrom_to_check) > 0) {
    if (!tep_checkdate($dfrom_to_check, DOB_FORMAT_STRING, $dfrom_array)) {
      $errorno += 10;
      $error = 1;
    }
  }  

  if (strlen($dto_to_check) > 0) {
    if (!tep_checkdate($dto_to_check, DOB_FORMAT_STRING, $dto_array)) {
      $errorno += 100;
      $error = 1;
    }
  }  

  if (strlen($dfrom_to_check) > 0 && !(($errorno & 10) == 10) && strlen($dto_to_check) > 0 && !(($errorno & 100) == 100)) {
    if (mktime(0, 0, 0, $dfrom_array[1], $dfrom_array[2], $dfrom_array[0]) > mktime(0, 0, 0, $dto_array[1], $dto_array[2], $dto_array[0])) {
      $errorno += 1000;
      $error = 1;
    }
  }

  if (!isset($_GET['pfrom'])) $_GET['pfrom'] = NULL;
  if (strlen($_GET['pfrom']) > 0) {
    $pfrom_to_check = $_GET['pfrom'];
    if (!settype($pfrom_to_check, "double")) {
      $errorno += 10000;
      $error = 1;
    }
  }

  if (!isset($_GET['pto'])) $_GET['pto'] = NULL;
  if (strlen($_GET['pto']) > 0) {
    $pto_to_check = $_GET['pto'];
    if (!settype($pto_to_check, "double")) {
      $errorno += 100000;
      $error = 1;
    }
  }

  if (strlen($_GET['pfrom']) > 0 && !(($errorno & 10000) == 10000) && strlen($_GET['pto']) > 0 && !(($errorno & 100000) == 100000)) {
    if ($pfrom_to_check > $pto_to_check) {
      $errorno += 1000000;
      $error = 1;
    }
  }

  if (strlen($_GET['keywords']) > 0) {
    if (!tep_parse_search_string(stripslashes($_GET['keywords']), $search_keywords)) {
      $errorno += 10000000;
      $error = 1;
    }
  }
  
  if ($error == 1) {
    tep_redirect(tep_href_link(FILENAME_ADVANCED_SEARCH, 'errorno=' . $errorno . '&' . tep_get_all_get_params(array('x', 'y'))));
  } else {
    $breadcrumb->add(NAVBAR_TITLE1, tep_href_link(FILENAME_ADVANCED_SEARCH));
    $breadcrumb->add(NAVBAR_TITLE2, tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'keywords=' . $_GET['keywords'] . '&search_in_description=' . $_GET['search_in_description'] . '&categories_id=' . $_GET['categories_id'] . '&inc_subcat=' . $_GET['inc_subcat'] . '&manufacturers_id=' . $_GET['manufacturers_id'] . '&pfrom=' . $_GET['pfrom'] . '&pto=' . $_GET['pto'] . '&dfrom=' . $_GET['dfrom'] . '&dto=' . $_GET['dto']));
?>
<?php page_head();?>
<?php
  if($ajax == 'on') {
    echo '<script language="javascript" src="./ajax/js/jk-ajax.js"></script>'."\n";
    echo '<script language="javascript" src="./ajax/js/in-cart.js"></script>'."\n";
  }
?>
<script type="text/javascript" src="js/sort.js"></script>
</head>
<body><div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof --> 
  <!-- body --> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td valign="top" class="left_colum_border"> <!-- left_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof --> </td> 
      <!-- body_text --> 
      <td valign="top" id="contents">
      <h1 class="pageHeading_long">
      <?php echo HEADING_TITLE ; ?>
      </h1>
       
        <div class="comment_long"> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td>

<?php
  // create column list
  $define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                       'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                       'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER, 
                       'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE, 
                       'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY, 
                       'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT, 
                       'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE, 
                       'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW);
  asort($define_list);

  $column_list = array();
  reset($define_list);
  while (list($column, $value) = each($define_list)) {
    if ($value) $column_list[] = $column;
  }

  $select_column_list = '';

  for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
    if ( ($column_list[$col] == 'PRODUCT_LIST_BUY_NOW') || ($column_list[$col] == 'PRODUCT_LIST_NAME') || ($column_list[$col] == 'PRODUCT_LIST_PRICE') ) {
      continue;
    }

    if (tep_not_null($select_column_list)) {
      $select_column_list .= ', ';
    }

    switch ($column_list[$col]) {
      case 'PRODUCT_LIST_MODEL':
        $select_column_list .= 'p.products_model,pd.products_description';
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
    }
  }

  if (tep_not_null($select_column_list)) {
    $select_column_list .= ', ';
  }

  $select_str = "
    select * 
    from (
    select distinct " . $select_column_list . " 
                    m.manufacturers_id, 
                    p.products_bflag, 
                    p.products_id, 
                    p.sort_order,
                    pd.products_name, 
                    p.products_price, 
                    p.products_tax_class_id, 
                    pd.site_id,
                    pd.products_status, 
                    pd.romaji,
                    pd.preorder_status, 
                    p.products_price_offset, p.products_small_sum"; 
  /*
  if(isset($_GET['colors']) && !empty($_GET['colors'])) {
    $select_str .= ", cp.color_image ";
  }
  */

  if ( (DISPLAY_PRICE_WITH_TAX == 'true') && ( (isset($_GET['pfrom']) && tep_not_null($_GET['pfrom'])) || (isset($_GET['pto']) && tep_not_null($_GET['pto']))) ) {
    $select_str .= ", SUM(tr.tax_rate) as tax_rate ";
  }
  
  #$from_str = "(( " . TABLE_PRODUCTS . " p ) left join " . TABLE_MANUFACTURERS . " m using(manufacturers_id), " . TABLE_PRODUCTS_DESCRIPTION . " pd )left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id, " . TABLE_CATEGORIES . " c, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, ".TABLE_COLOR_TO_PRODUCTS." cp";
  $from_str = "( " . TABLE_PRODUCTS . " p ) left join " . TABLE_MANUFACTURERS . " m using(manufacturers_id), " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_CATEGORIES . " c, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ";

  if ( (DISPLAY_PRICE_WITH_TAX == 'true') && ( (isset($_GET['pfrom']) && tep_not_null($_GET['pfrom'])) || (isset($_GET['pto']) && tep_not_null($_GET['pto']))) ) {
    if (!tep_session_is_registered('customer_country_id')) {
      $customer_country_id = STORE_COUNTRY;
      $customer_zone_id = STORE_ZONE;
    }
    $from_str = '(('.$from_str.") left join " . TABLE_TAX_RATES . " tr on p.products_tax_class_id = tr.tax_class_id) left join " . TABLE_ZONES_TO_GEO_ZONES . " gz on tr.tax_zone_id = gz.geo_zone_id and (gz.zone_country_id is null or gz.zone_country_id = '0' or gz.zone_country_id = '" . $customer_country_id . "') and (gz.zone_id is null or gz.zone_id = '0' or gz.zone_id = '" . $customer_zone_id . "')";
  }

  $where_str = " where p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id ";

  if (isset($_GET['categories_id']) && tep_not_null($_GET['categories_id'])) {
    if ($_GET['inc_subcat'] == '1') {
      $subcategories_array = array();
      tep_get_subcategories($subcategories_array, $_GET['categories_id']);
      $where_str .= " and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and (p2c.categories_id = '" . (int)$_GET['categories_id'] . "'";
      for ($i=0, $n=sizeof($subcategories_array); $i<$n; $i++ ) {
        $where_str .= " or p2c.categories_id = '" . $subcategories_array[$i] . "'";
      }
      $where_str .= ")";
    } else {
      $where_str .= " and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p2c.categories_id = '" . $_GET['categories_id'] . "'";
    }
  } else {
    $search_caid = tep_other_get_categories_id_by_parent_id(FF_CID); 
    $where_str .= " and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and (p2c.categories_id = '".FF_CID."'";
    for ($i=0, $n=sizeof($search_caid); $i<$n; $i++ ) {
      $where_str .= " or p2c.categories_id = '" . $search_caid[$i] . "'";
    }
    $where_str .= ')'; 
  }

  if (isset($_GET['manufacturers_id']) && tep_not_null($_GET['manufacturers_id'])) {
    $where_str .= " and m.manufacturers_id = '" . $_GET['manufacturers_id'] . "'";
  }

  if (isset($_GET['keywords']) && tep_not_null($_GET['keywords'])) {
    if (tep_parse_search_string(stripslashes($_GET['keywords']), $search_keywords)) {
      $tags_id_array = array();
      $where_str .= " and (";
      $where_tags_str = $where_str;
      for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
        switch ($search_keywords[$i]) {
          case '(':
          case ')':
          case 'and':
          case 'or':
            $where_str .= " " . $search_keywords[$i] . " ";
            break;
          default:
            $tags_query = tep_db_query("select tags_id from ". TABLE_TAGS ." where tags_name like '%".addslashes($search_keywords[$i])."%'");
            while($tags_array = tep_db_fetch_array($tags_query)){

              $products_tags_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_TAGS ." where tags_id='".$tags_array['tags_id']."'");

              while($products_tags_array = tep_db_fetch_array($products_tags_query)){
                $tags_id_array[] = $products_tags_array['products_id'];
              }
              tep_db_free_result($products_tags_query);
            }
            tep_db_free_result($tags_query);
            $where_str .= "(pd.products_name like '%" . addslashes($search_keywords[$i]) . "%' or p.products_model like '%" . addslashes($search_keywords[$i]) . "%' or m.manufacturers_name like '%" . addslashes($search_keywords[$i]) . "%'";
            if (isset($_GET['search_in_description']) && ($_GET['search_in_description'] == '1')) $where_str .= " or pd.products_description like '%" . addslashes($search_keywords[$i]) . "%'";
              $where_str .= ')';
            break;
        }
      }
      $where_tags_str = $where_tags_str."p.products_id in (".implode(',',$tags_id_array).")";
      $where_tags_str .= " )";
      $where_str .= " )";
    }
  }

  if (isset($_GET['dfrom']) && tep_not_null($_GET['dfrom']) && ($_GET['dfrom'] != DOB_FORMAT_STRING)) {
    $where_str_temp .= " and p.products_date_added >= '" . tep_date_raw($dfrom_to_check) . "'";
  }

  if (isset($_GET['dto']) && tep_not_null($_GET['dto']) && ($_GET['dto'] != DOB_FORMAT_STRING)) {
    $where_str_temp .= " and p.products_date_added <= '" . tep_date_raw($dto_to_check) . "'";
  }

  $rate = $currencies->get_value($currency);
  if ($rate) {
    $pfrom = $_GET['pfrom'] / $rate;
    $pto = $_GET['pto'] / $rate;
  }

  if (DISPLAY_PRICE_WITH_TAX == 'true') {
    if ($pfrom) $where_str_temp .= " and (IF(p.products_price_offset, p.products_price + p.products_price_offset, p.products_price) * if(gz.geo_zone_id is null, 1, 1 + (tr.tax_rate / 100) ) >= " . $pfrom . ")";
    if ($pto)   $where_str_temp .= " and (IF(p.products_price_offset, p.products_price + p.products_price_offset, p.products_price) * if(gz.geo_zone_id is null, 1, 1 + (tr.tax_rate / 100) ) <= " . $pto . ")";
  } else {
    if ($pfrom) $where_str_temp .= " and (IF(p.products_price_offset, p.products_price + p.products_price_offset, p.products_price) >= " . $pfrom . ")";
    if ($pto)   $where_str_temp .= " and (IF(p.products_price_offset, p.products_price + p.products_price_offset, p.products_price) <= " . $pto . ")";
  }

  //$where_str .= " and pd.site_id = '".SITE_ID."'";
  
  if ( (DISPLAY_PRICE_WITH_TAX == 'true') && ((isset($_GET['pfrom']) && tep_not_null($_GET['pfrom'])) || (isset($_GET['pto']) && tep_not_null($_GET['pto']))) ) {
    $where_str_temp .= " group by p.products_id, tr.tax_priority";
  }
  $where_str .= $where_str_temp."
    order by pd.site_id DESC ) p 
    where site_id = 0
       or site_id = ".SITE_ID."
    group by products_id
    having p.products_status != '0' and p.products_status != '3' 
    ";
  $where_tags_str .= $where_str_temp."
    order by pd.site_id DESC ) p 
    where site_id = 0
       or site_id = ".SITE_ID."
    group by products_id
    having p.products_status != '0' and p.products_status != '3' 
    ";
  $listing_sql = $select_str . ' from ' . $from_str . $where_str;
  $listing_tags_sql = $select_str . ' from ' . $from_str . $where_tags_str;
  if(count($tags_id_array) > 0){
    $listing_sql = "select * from ((".$listing_sql.") union (".$listing_tags_sql.")) t_p ";
  }
  require(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING);
?>
<br>
<br>

<?php echo '<a href="' . tep_href_link(FILENAME_ADVANCED_SEARCH, tep_get_all_get_params(array('sort', 'page', 'x', 'y')), 'NONSSL', true, false) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
</td> 
            </tr> 
          </table> 
        </div>
        </td> 
      <!-- body_text_eof --> 

  </table> 
  <!-- body_eof --> 
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof --> 
</div> 
</div> 
</body>
</html>
<?php
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
