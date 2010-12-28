<?php
/*
  $Id$

  global functions
  
  required by application_top.php
*/

////
// Stop from parsing any further PHP code
  function tep_exit() {
   tep_session_close();
   exit();
  }

////
// Redirect to another page or site
  function tep_redirect($url,$suc='') {
    if ( (ENABLE_SSL == true) && (getenv('HTTPS') == 'on') && SITE_ID != 4) { // We are loading an SSL page // 要求id登陆跳回非SSL页面 2010.12.28 18:00 - maker
      if (substr($url, 0, strlen(HTTP_SERVER)) == HTTP_SERVER) { // NONSSL url
        $url = HTTPS_SERVER . substr($url, strlen(HTTP_SERVER)); // Change it to SSL
      }
    }

    header('Location: ' . $url);
  
  if($suc == 'T'){
    echo 'SuccessOK';
    }
  
  tep_exit();
  }


function forward404()
{
  header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  require(DIR_WS_MODULES  . '404.html');
  exit;
  //throw new Exception();
}

//在条件成立的时候，404
function forward404If($condition)
{
  if ($condition)
  {
    forward404();
  }
}

//在条件不成立时，404
function forward404Unless($condition)
{
  if (!$condition)
  {
    forward404();
  }
}

////
// Parse the data used in the html tags to ensure the tags will not break
  function tep_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
  }

  function tep_output_string($string, $translate = false, $protected = false) {
    if ($protected == true) {
      return htmlspecialchars($string);
    } else {
      if ($translate == false) {
        return tep_parse_input_field_data($string, array('"' => '&quot;'));
      } else {
        return tep_parse_input_field_data($string, $translate);
      }
    }
  }

  function tep_output_string_protected($string) {
    return tep_output_string($string, false, true);
  }

  function tep_sanitize_string($string) {
    $string = ereg_replace(' +', ' ', trim($string));

    //return str_replace(array('<', '>'), array('＜', '＞'), $string);
    //return preg_replace("/[<>]/", '_', $string);
    return $string;
  }

////
// Error message wrapper
// When optional parameters are provided, it closes the application
// (ie, halts the current application execution task)
  function tep_error_message($error_message, $close_application = false, $close_application_error = '') {
    echo $error_message;

    if ($close_application == true) {
      die($close_application_error);
    }
  }

////
// Return a random row from a database query
  function tep_random_select($query) {
    $random_product = '';
    // ccdd
    $random_query = tep_db_query($query);
    $num_rows = tep_db_num_rows($random_query);
    if ($num_rows > 0) {
      $random_row = tep_rand(0, ($num_rows - 1));
      tep_db_data_seek($random_query, $random_row);
      $random_product = tep_db_fetch_array($random_query);
    }

    return $random_product;
  }

////
// Return a product's name
// TABLES: products
  function tep_get_products_name($product_id, $language = '') {
    global $languages_id;

    if (empty($language)) $language = $languages_id;
    //ccdd
    $product_query = tep_db_query("
        select products_name 
        from " .  TABLE_PRODUCTS_DESCRIPTION . " 
        where products_id = '" . (int)$product_id .  "' 
          and language_id = '" . (int)$language . "' 
          and (site_id = '".SITE_ID."' or site_id = '0')
        order by site_id DESC"
    );
    $product = tep_db_fetch_array($product_query);

    return $product['products_name'];
  }


// Return a product's description
// TABLES: products
  function tep_get_products_description($product_id, $language = '') {
    global $languages_id;

    if (empty($language)) $language = $languages_id;
    //ccdd
    $product_query = tep_db_query("
        select products_description 
        from " .  TABLE_PRODUCTS_DESCRIPTION . " 
        where products_id = '" . (int)$product_id .  "' 
          and language_id = '" . (int)$language . "' 
          and (site_id = '".SITE_ID."' or site_id = '0')
        order by site_id DESC"
    );
    $product = tep_db_fetch_array($product_query);

    return $product['products_description'];
  }

////
// 商品IDから説明文を呼び出す
  function ds_tep_get_description($products_id) {
    global $languages_id;
//ccdd
    $description_query = tep_db_query("
        select products_description 
        from ".TABLE_PRODUCTS_DESCRIPTION." 
        where products_id = '".$products_id."' 
          and language_id = '".$languages_id."' 
          and (site_id = '".SITE_ID."' or site_id = '0')
        order by site_id DESC"
        );
    $description = tep_db_fetch_array($description_query);
    return strip_tags($description['products_description']) ;
  }

////
// Return a product's special price (returns nothing if there is no offer)
// TABLES: products
/*
  function tep_get_products_special_price($product_id) {
    //ccdd
    $product_query = tep_db_query("
        select specials_new_products_price 
        from " . TABLE_SPECIALS . " 
        where products_id = '" . (int)$product_id . "' 
          and status
    ");
    $product = tep_db_fetch_array($product_query);

    return $product['specials_new_products_price'];
  }*/

////
// Return a product's stock
// TABLES: products
  function tep_get_products_stock($products_id) {
    $products_id = tep_get_prid($products_id);
    //ccdd
    $stock_query = tep_db_query("
        select products_quantity, 
               products_status 
        from " . TABLE_PRODUCTS . " 
        where products_id = '" . (int)$products_id . "'
    ");
    $stock_values = tep_db_fetch_array($stock_query);

    return ($stock_values['products_status'] == '1') ? $stock_values['products_quantity'] : 0;
  }

////
// Check if the required stock is available
// If insufficent stock is available return an out of stock message
  function tep_check_stock($products_id, $products_quantity) {
    $stock_left = tep_get_products_stock($products_id) - $products_quantity;
    $out_of_stock = '';

    if ($stock_left < 0) {
      $out_of_stock = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
    }

    return $out_of_stock;
  }

////
// Break a word in a string if it is longer than a specified length ($len)
  function tep_break_string($string, $len, $break_char = '-') {
    /*
    $l = 0;
    $output = '';
    for ($i=0, $n=strlen($string); $i<$n; $i++) {
      $char = substr($string, $i, 1);
      if ($char != ' ') {
        $l++;
      } else {
        $l = 0;
      }
      if ($l > $len) {
        $l = 1;
        $output .= $break_char;
      }
      $output .= $char;
    }

    return $output;
    */
    return $string;
  }

////
// Return all HTTP GET variables, except those passed as a parameter
  function tep_get_all_get_params($exclude_array = '') {
    global $_GET;

    if (!is_array($exclude_array)) $exclude_array = array();

    $get_url = '';
    if (is_array($_GET) && (sizeof($_GET) > 0)) {
      reset($_GET);
      while (list($key, $value) = each($_GET)) {
        if ( (strlen($value) > 0) && ($key != tep_session_name()) && ($key != 'error') && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y') ) {
          $get_url .= $key . '=' . rawurlencode(stripslashes($value)) . '&';
        }
      }
    }

    return $get_url;
  }

////
// Returns an array with countries
// TABLES: countries
  function tep_get_countries($countries_id = '', $with_iso_codes = false) {
    $countries_array = array();
    if (tep_not_null($countries_id)) {
      if ($with_iso_codes == true) {
//ccdd
        $countries = tep_db_query("
            select countries_name, 
                   countries_iso_code_2, 
                   countries_iso_code_3 
            from " . TABLE_COUNTRIES . " 
            where countries_id = '" . (int)$countries_id . "' 
            order by countries_name
        ");
        $countries_values = tep_db_fetch_array($countries);
        $countries_array = array('countries_name' => $countries_values['countries_name'],
                                 'countries_iso_code_2' => $countries_values['countries_iso_code_2'],
                                 'countries_iso_code_3' => $countries_values['countries_iso_code_3']);
      } else {
//ccdd
        $countries = tep_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "'");
        $countries_values = tep_db_fetch_array($countries);
        $countries_array = array('countries_name' => $countries_values['countries_name']);
      }
    } else {
//ccdd
      $countries = tep_db_query("select countries_id, countries_name from " . TABLE_COUNTRIES . " order by countries_name");
      while ($countries_values = tep_db_fetch_array($countries)) {
        $countries_array[] = array('countries_id' => $countries_values['countries_id'],
                                   'countries_name' => $countries_values['countries_name']);
      }
    }

    return $countries_array;
  }

////
// Alias function to tep_get_countries, which also returns the countries iso codes
  function tep_get_countries_with_iso_codes($countries_id) {
    return tep_get_countries($countries_id, true);
  }

////
// Generate a path to categories
  function tep_get_path($current_category_id = '') {
    global $cPath_array;

    if (tep_not_null($current_category_id)) {
      $cp_size = sizeof($cPath_array);
      if ($cp_size == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
//ccdd
        $last_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$cPath_array[($cp_size-1)] . "'");
        $last_category = tep_db_fetch_array($last_category_query);

//ccdd
        $current_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");
        $current_category = tep_db_fetch_array($current_category_query);

        if ($last_category['parent_id'] == $current_category['parent_id']) {
          for ($i=0; $i<($cp_size-1); $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        } else {
          for ($i=0; $i<$cp_size; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        }
        $cPath_new .= '_' . $current_category_id;

        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    } else {
      $cPath_new = implode('_', $cPath_array);
    }

    return 'cPath=' . $cPath_new;
  }

////
// Returns the clients browser
  function tep_browser_detect($component) {
    global $HTTP_USER_AGENT;

    return stristr($HTTP_USER_AGENT, $component);
  }

////
// Alias function to tep_get_countries()
  function tep_get_country_name($country_id) {
    $country_array = tep_get_countries($country_id);

    if (!isset($country_array['countries_name'])) $country_array['countries_name'] = NULL;
    return $country_array['countries_name'];
  }

////
// Returns the zone (State/Province) name
// TABLES: zones
  function tep_get_zone_name($country_id, $zone_id, $default_zone) {
//ccdd
    $zone_query = tep_db_query("
        select zone_name 
        from " . TABLE_ZONES . " 
        where zone_country_id = '" . (int)$country_id . "' 
          and zone_id = '" . (int)$zone_id . "'
    ");
    if (tep_db_num_rows($zone_query)) {
      $zone = tep_db_fetch_array($zone_query);
      return $zone['zone_name'];
    } else {
      return $default_zone;
    }
  }

////
// Returns the zone (State/Province) code
// TABLES: zones
  function tep_get_zone_code($country_id, $zone_id, $default_zone) {
//ccdd
    $zone_query = tep_db_query("
        select zone_code 
        from " . TABLE_ZONES . " 
        where zone_country_id = '" . (int)$country_id . "' 
          and zone_id = '" . (int)$zone_id . "'
    ");
    if (tep_db_num_rows($zone_query)) {
      $zone = tep_db_fetch_array($zone_query);
      return $zone['zone_code'];
    } else {
      return $default_zone;
    }
  }

////
// Wrapper function for round() for php3 compatibility
  function tep_round($value, $precision) {
    if (PHP_VERSION < 4) {
      $exp = pow(10, $precision);
      return round($value * $exp) / $exp;
    } else {
      return round($value, $precision);
    }
  }

////
// Returns the tax rate for a zone / class
// TABLES: tax_rates, zones_to_geo_zones
  function tep_get_tax_rate($class_id, $country_id = -1, $zone_id = -1) {
    global $customer_zone_id, $customer_country_id;

    if ( ($country_id == -1) && ($zone_id == -1) ) {
      if (!tep_session_is_registered('customer_id')) {
        $country_id = STORE_COUNTRY;
        $zone_id = STORE_ZONE;
      } else {
        $country_id = $customer_country_id;
        $zone_id = $customer_zone_id;
      }
    }
//ccdd
    $tax_query = tep_db_query("
        select sum(tax_rate) as tax_rate 
        from " . TABLE_TAX_RATES . " tr 
          left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) 
          left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) 
        where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') 
          and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') 
          and tr.tax_class_id = '" . (int)$class_id . "' group by tr.tax_priority
    ");
    if (tep_db_num_rows($tax_query)) {
      $tax_multiplier = 0;
      while ($tax = tep_db_fetch_array($tax_query)) {
        $tax_multiplier += $tax['tax_rate'];
      }
      return $tax_multiplier;
    } else {
      return 0;
    }
  }

////
// Return the tax description for a zone / class
// TABLES: tax_rates;
  function tep_get_tax_description($class_id, $country_id, $zone_id) {
//ccdd
    $tax_query = tep_db_query("
        select tax_description 
        from " . TABLE_TAX_RATES . " tr 
          left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) 
        where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') 
          and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') 
          and tr.tax_class_id = '" . (int)$class_id . "' 
        order by tr.tax_priority
      ");
    if (tep_db_num_rows($tax_query)) {
      $tax_description = '';
      while ($tax = tep_db_fetch_array($tax_query)) {
        $tax_description .= $tax['tax_description'] . ' + ';
      }
      $tax_description = substr($tax_description, 0, -3);

      return $tax_description;
    } else {
      return TEXT_UNKNOWN_TAX_RATE;
    }
  }

////
// Add tax to a products price
  function tep_add_tax($price, $tax) {
    global $currencies;

    if ( (DISPLAY_PRICE_WITH_TAX == 'true') && ($tax > 0) ) {
      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + tep_calculate_tax($price, $tax);
    } else {
      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
    }
  }

// Calculates Tax rounding the result
  function tep_calculate_tax($price, $tax) {
    global $currencies;

  // return tep_round($price * $tax / 100, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
    return $currencies->round_off($price * $tax / 100);
  }

////
// Return the number of products in a category
// TABLES: products, products_to_categories, categories
  function tep_count_products_in_category($category_id, $include_inactive = false) {
    $products_count = 0;
    if ($include_inactive == true) {
//ccdd
      $products_query = tep_db_query("
          select count(*) as total 
          from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
          where p.products_id = p2c.products_id 
            and p2c.categories_id = '" . (int)$category_id . "'
      ");
    } else {
//ccdd
      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status != '0' and p2c.categories_id = '" . (int)$category_id . "'");
    }
    $products = tep_db_fetch_array($products_query);
    $products_count += $products['total'];
//ccdd
    $child_categories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$category_id . "'");
    if (tep_db_num_rows($child_categories_query)) {
      while ($child_categories = tep_db_fetch_array($child_categories_query)) {
        $products_count += tep_count_products_in_category($child_categories['categories_id'], $include_inactive);
      }
    }

    return $products_count;
  }

////
// Return true if the category has subcategories
// TABLES: categories
  function tep_has_category_subcategories($category_id) {
//ccdd
    $child_category_query = tep_db_query("select count(*) as count from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$category_id . "'");
    $child_category = tep_db_fetch_array($child_category_query);

    if ($child_category['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }

////
// Returns the address_format_id for the given country
// TABLES: countries;
  function tep_get_address_format_id($country_id) {
//ccdd
    $address_format_query = tep_db_query("select address_format_id as format_id from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");
    if (tep_db_num_rows($address_format_query)) {
      $address_format = tep_db_fetch_array($address_format_query);
      return $address_format['format_id'];
    } else {
      return '1';
    }
  }

////
// Return a formatted address
// TABLES: address_format
  function tep_address_format($address_format_id, $address, $html, $boln, $eoln) {
//ccdd
    $address_format_query = tep_db_query("select address_format as format from " . TABLE_ADDRESS_FORMAT . " where address_format_id = '" . (int)$address_format_id . "'");
    $address_format = tep_db_fetch_array($address_format_query);

    $company = tep_output_string_protected($address['company']);
    if (!isset($address['firstname'])) $address['firstname'] = NULL;
    $firstname = tep_output_string_protected($address['firstname']);
    if (!isset($address['lastname'])) $address['lastname'] = NULL;
    $lastname = tep_output_string_protected($address['lastname']);
  
    if (!isset($address['lastname_f'])) $address['lastname_f'] = NULL;
    if (!isset($address['firstname_f'])) $address['firstname_f'] = NULL;
    $name_f = tep_output_string_protected($address['lastname_f']) . tep_output_string_protected($address['firstname_f']);
  
    $street = tep_output_string_protected($address['street_address']);
    $suburb = tep_output_string_protected($address['suburb']);
    $city   = tep_output_string_protected($address['city']);
    $state  = tep_output_string_protected($address['state']);
    if (!isset($address['country_id'])) $address['country_id'] = NULL;
    $country_id = $address['country_id'];
    if (!isset($address['zone_id'])) $address['zone_id'] = NULL;
    $zone_id   = $address['zone_id'];
    $postcode  = tep_output_string_protected($address['postcode']);
    $zip       = $postcode;
    $country   = tep_get_country_name($country_id);
    $state     = tep_get_zone_code($country_id, $zone_id, $state);
    $statename = tep_get_zone_name($country_id,$zone_id,$state); // for Japanese Localize
    $telephone = tep_output_string_protected($address['telephone']);

    if ($html) {
// HTML Mode
      $HR = '<hr>';
      $hr = '<hr>';
      if ( ($boln == '') && ($eoln == "\n") ) { // Values not specified, use rational defaults
        $CR = '<br>';
        $cr = '<br>';
        $eoln = $cr;
      } else { // Use values supplied
        $CR = $eoln . $boln;
        $cr = $CR;
      }
    } else {
// Text Mode
      $CR = $eoln;
      $cr = $CR;
      $HR = '----------------------------------------';
      $hr = '----------------------------------------';
    }

    $statecomma = '';
    $streets = $street;
    if ($suburb != '') $streets = $street . $cr . $suburb;
    if ($firstname == '') $firstname = tep_output_string_protected($address['name']);
  if ($name_f == '') $name_f = tep_output_string_protected($address['name_f']);
    if ($country == '') $country = tep_output_string_protected($address['country']);
    if ($state != '') $statecomma = $state . ', ';
  
    $fmt = $address_format['format'];
    $fmt = str_replace(",", "", $fmt); 
    eval("\$address = \"$fmt\";");
    $address = stripslashes($address);

    if ( (ACCOUNT_COMPANY == 'true') && (tep_not_null($company)) ) {
      $address = $company . $cr . $address;
    }

    return $boln . $address . $eoln;
  }

////
// Return a formatted address
// TABLES: customers, address_book
  function tep_address_label($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
//ccdd
    $address_query = tep_db_query("select entry_firstname as firstname, entry_lastname as lastname, entry_firstname_f as firstname_f, entry_lastname_f as lastname_f, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id, entry_telephone as telephone from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customers_id . "' and address_book_id = '" . (int)$address_id . "'");
    $address = tep_db_fetch_array($address_query);

    $format_id = tep_get_address_format_id($address['country_id']);

    return tep_address_format($format_id, $address, $html, $boln, $eoln);
  }

////
// Return a formatted address
// TABLES: address_book, address_format
  function tep_address_summary($customers_id, $address_id) {
    $customers_id = tep_db_prepare_input($customers_id);
    $address_id = tep_db_prepare_input($address_id);
//ccdd
    $address_query = tep_db_query("select ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_state, ab.entry_country_id, ab.entry_zone_id, c.countries_name, c.address_format_id from " . TABLE_ADDRESS_BOOK . " ab, " . TABLE_COUNTRIES . " c where ab.address_book_id = '" . tep_db_input($address_id) . "' and ab.customers_id = '" . tep_db_input($customers_id) . "' and ab.entry_country_id = c.countries_id");
    $address = tep_db_fetch_array($address_query);

    $street_address = tep_output_string_protected($address['entry_street_address']);
    $suburb = tep_output_string_protected($address['entry_suburb']);
    $postcode = tep_output_string_protected($address['entry_postcode']);
    $city = tep_output_string_protected($address['entry_city']);
    $state = tep_get_zone_code($address['entry_country_id'], $address['entry_zone_id'], $address['entry_state']);
    $country = tep_output_string_protected($address['countries_name']);

//ccdd
    $address_format_query = tep_db_query("select address_summary from " . TABLE_ADDRESS_FORMAT . " where address_format_id = '" . (int)$address['address_format_id'] . "'");
    $address_format = tep_db_fetch_array($address_format_query);
    $statename = tep_get_zone_name($address['entry_country_id'], $address['entry_zone_id'],''); // for Japanese Localize

//    eval("\$address = \"{$address_format['address_summary']}\";");
    $address_summary = $address_format['address_summary'];
    eval("\$address = \"$address_summary\";");

    return $address;
  }

  function tep_row_number_format($number) {
    if ( ($number < 10) && (substr($number, 0, 1) != '0') ) $number = '0' . $number;

    return $number;
  }

  function tep_get_categories($categories_array = '', $parent_id = '0', $indent = '') {
    global $languages_id;

    $parent_id = tep_db_prepare_input($parent_id);

    if (!is_array($categories_array)) $categories_array = array();
    //ccdd
    $categories_query = tep_db_query("
      select *
      from (
        select c.categories_id, cd.categories_name ,c.sort_order, cd.site_id
        from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where parent_id = '" . tep_db_input($parent_id) . "' 
          and c.categories_id = cd.categories_id 
          and cd.language_id = '" . (int)$languages_id . "' 
        order by cd.site_id DESC
      ) c
      where site_id = '0'
         or site_id = ".SITE_ID." 
      group by categories_id
      order by sort_order, categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      $categories_array[] = array('id' => $categories['categories_id'],
                                  'text' => $indent . $categories['categories_name']);

      if ($categories['categories_id'] != $parent_id) {
        $categories_array = tep_get_categories($categories_array, $categories['categories_id'], $indent . '&nbsp;&nbsp;');
      }
    }

    return $categories_array;
  }

  function tep_get_manufacturers($manufacturers_array = '') {
    if (!is_array($manufacturers_array)) $manufacturers_array = array();
    //ccdd
    $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
      $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);
    }

    return $manufacturers_array;
  }

////
// Return all subcategory IDs
// TABLES: categories
  function tep_get_subcategories(&$subcategories_array, $parent_id = 0) {
    //ccdd
    $subcategories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$parent_id . "'");
    while ($subcategories = tep_db_fetch_array($subcategories_query)) {
      $subcategories_array[sizeof($subcategories_array)] = $subcategories['categories_id'];
      if ($subcategories['categories_id'] != $parent_id) {
        tep_get_subcategories($subcategories_array, $subcategories['categories_id']);
      }
    }
  }

// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
  function tep_date_long($raw_date) {
    if (is_numeric($raw_date))$raw_date = date('Y-m-d H:i:s', $raw_date);

    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = (int)substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    $returntime = strftime(DATE_FORMAT_LONG, mktime($hour,$minute,$second,$month,$day,$year));
    $oarr = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $newarr = array('月曜日', '火曜日', '水曜日', '木曜日', '金曜日', '土曜日', '日曜日');
    return str_replace($oarr, $newarr, $returntime);
  }

////
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
  function tep_date_short($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
      return date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
    } else {
      $base_year = tep_is_leap_year($year) ? 2036 : 2037;
      return ereg_replace((string)$base_year, $year, date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $base_year)));
    }
  }

////
// Parse search string into indivual objects
  function tep_parse_search_string($search_str = '', &$objects) {
    $search_str = trim(strtolower($search_str));

// Break up $search_str on whitespace; quoted string will be reconstructed later
    $pieces = split('[[:space:]]+', $search_str);
    $objects = array();
    $tmpstring = '';
    $flag = '';

    for ($k=0; $k<count($pieces); $k++) {
      while (substr($pieces[$k], 0, 1) == '(') {
        $objects[] = '(';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 1);
        } else {
          $pieces[$k] = '';
        }
      }

      $post_objects = array();

      while (substr($pieces[$k], -1) == ')')  {
        $post_objects[] = ')';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 0, -1);
        } else {
          $pieces[$k] = '';
        }
      }

// Check individual words

      if ( (substr($pieces[$k], -1) != '"') && (substr($pieces[$k], 0, 1) != '"') ) {
        $objects[] = trim($pieces[$k]);

        for ($j=0; $j<count($post_objects); $j++) {
          $objects[] = $post_objects[$j];
        }
      } else {
/* This means that the $piece is either the beginning or the end of a string.
   So, we'll slurp up the $pieces and stick them together until we get to the
   end of the string or run out of pieces.
*/

// Add this word to the $tmpstring, starting the $tmpstring
        $tmpstring = trim(ereg_replace('"', ' ', $pieces[$k]));

// Check for one possible exception to the rule. That there is a single quoted word.
        if (substr($pieces[$k], -1 ) == '"') {
// Turn the flag off for future iterations
          $flag = 'off';

          $objects[] = trim($pieces[$k]);

          for ($j=0; $j<count($post_objects); $j++) {
            $objects[] = $post_objects[$j];
          }

          unset($tmpstring);

// Stop looking for the end of the string and move onto the next word.
          continue;
        }

// Otherwise, turn on the flag to indicate no quotes have been found attached to this word in the string.
        $flag = 'on';

// Move on to the next word
        $k++;

// Keep reading until the end of the string as long as the $flag is on

        while ( ($flag == 'on') && ($k < count($pieces)) ) {
          while (substr($pieces[$k], -1) == ')') {
            $post_objects[] = ')';
            if (strlen($pieces[$k]) > 1) {
              $pieces[$k] = substr($pieces[$k], 0, -1);
            } else {
              $pieces[$k] = '';
            }
          }

// If the word doesn't end in double quotes, append it to the $tmpstring.
          if (substr($pieces[$k], -1) != '"') {
// Tack this word onto the current string entity
            $tmpstring .= ' ' . $pieces[$k];

// Move on to the next word
            $k++;
            continue;
          } else {
/* If the $piece ends in double quotes, strip the double quotes, tack the
   $piece onto the tail of the string, push the $tmpstring onto the $haves,
   kill the $tmpstring, turn the $flag "off", and return.
*/
            $tmpstring .= ' ' . trim(ereg_replace('"', ' ', $pieces[$k]));

// Push the $tmpstring onto the array of stuff to search for
            $objects[] = trim($tmpstring);

            for ($j=0; $j<count($post_objects); $j++) {
              $objects[] = $post_objects[$j];
            }

            unset($tmpstring);

// Turn off the flag to exit the loop
            $flag = 'off';
          }
        }
      }
    }

// add default logical operators if needed
    $temp = array();
    for($i=0; $i<(count($objects)-1); $i++) {
      $temp[sizeof($temp)] = $objects[$i];

      if ( ($objects[$i] != 'and') &&
           ($objects[$i] != 'or') &&
           ($objects[$i] != '(') &&
           ($objects[$i] != ')') &&
           ($objects[$i+1] != 'and') &&
           ($objects[$i+1] != 'or') &&
           ($objects[$i+1] != '(') &&
           ($objects[$i+1] != ')') ) {
        $temp[sizeof($temp)] = ADVANCED_SEARCH_DEFAULT_OPERATOR;
      }
    }
    $temp[sizeof($temp)] = $objects[$i];
    $objects = $temp;

    $keyword_count = 0;
    $operator_count = 0;
    $balance = 0;
    for($i=0; $i<count($objects); $i++) {
      if ($objects[$i] == '(') $balance --;
      if ($objects[$i] == ')') $balance ++;
      if ( ($objects[$i] == 'and') || ($objects[$i] == 'or') ) {
        $operator_count ++;
      } elseif ( ($objects[$i]) && ($objects[$i] != '(') && ($objects[$i] != ')') ) {
        $keyword_count ++;
      }
    }

    if ( ($operator_count < $keyword_count) && ($balance == 0) ) {
      return true;
    } else {
      return false;
    }
  }

////
// Check date
  function tep_checkdate($date_to_check, $format_string, &$date_array) {
    $separator_idx = -1;

    $separators = array('-', ' ', '/', '.');
    $month_abbr = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
    $no_of_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    $format_string = strtolower($format_string);

    if (strlen($date_to_check) != strlen($format_string)) {
      return false;
    }

    $size = sizeof($separators);
    for ($i=0; $i<$size; $i++) {
      $pos_separator = strpos($date_to_check, $separators[$i]);
      if ($pos_separator != false) {
        $date_separator_idx = $i;
        break;
      }
    }

    for ($i=0; $i<$size; $i++) {
      $pos_separator = strpos($format_string, $separators[$i]);
      if ($pos_separator != false) {
        $format_separator_idx = $i;
        break;
      }
    }

    if ($date_separator_idx != $format_separator_idx) {
      return false;
    }

    if ($date_separator_idx != -1) {
      $format_string_array = explode( $separators[$date_separator_idx], $format_string );
      if (sizeof($format_string_array) != 3) {
        return false;
      }

      $date_to_check_array = explode( $separators[$date_separator_idx], $date_to_check );
      if (sizeof($date_to_check_array) != 3) {
        return false;
      }

      $size = sizeof($format_string_array);
      for ($i=0; $i<$size; $i++) {
        if ($format_string_array[$i] == 'mm' || $format_string_array[$i] == 'mmm') $month = $date_to_check_array[$i];
        if ($format_string_array[$i] == 'dd') $day = $date_to_check_array[$i];
        if ( ($format_string_array[$i] == 'yyyy') || ($format_string_array[$i] == 'aaaa') ) $year = $date_to_check_array[$i];
      }
    } else {
      if (strlen($format_string) == 8 || strlen($format_string) == 9) {
        $pos_month = strpos($format_string, 'mmm');
        if ($pos_month != false) {
          $month = substr( $date_to_check, $pos_month, 3 );
          $size = sizeof($month_abbr);
          for ($i=0; $i<$size; $i++) {
            if ($month == $month_abbr[$i]) {
              $month = $i;
              break;
            }
          }
        } else {
          $month = substr($date_to_check, strpos($format_string, 'mm'), 2);
        }
      } else {
        return false;
      }

      $day = substr($date_to_check, strpos($format_string, 'dd'), 2);
      $year = substr($date_to_check, strpos($format_string, 'yyyy'), 4);
    }

    if (strlen($year) != 4) {
      return false;
    }

    if (!settype($year, 'integer') || !settype($month, 'integer') || !settype($day, 'integer')) {
      return false;
    }

    if ($month > 12 || $month < 1) {
      return false;
    }

    if ($day < 1) {
      return false;
    }

    if (tep_is_leap_year($year)) {
      $no_of_days[1] = 29;
    }

    if ($day > $no_of_days[$month - 1]) {
      return false;
    }

    $date_array = array($year, $month, $day);

    return true;
  }

////
// Check if year is a leap year
  function tep_is_leap_year($year) {
    if ($year % 100 == 0) {
      if ($year % 400 == 0) return true;
    } else {
      if (($year % 4) == 0) return true;
    }

    return false;
  }

////
// Return table heading with sorting capabilities
  function tep_create_sort_heading($sortby, $colnum, $heading) {
    global $PHP_SELF;

    $sort_prefix = '';
    $sort_suffix = '';

    if ($sortby) {
      $sort_prefix = '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=' . $colnum . ($sortby == $colnum . 'a' ? 'd' : 'a')) . '" title="' . TEXT_SORT_PRODUCTS . ($sortby == $colnum . 'd' || substr($sortby, 0, 1) != $colnum ? TEXT_ASCENDINGLY : TEXT_DESCENDINGLY) . TEXT_BY . $heading . '">' ;
      $sort_suffix = (substr($sortby, 0, 1) == $colnum ? (substr($sortby, 1, 1) == 'a' ? '+' : '-') : '') . '</a>';
    }

    return $sort_prefix . $heading . $sort_suffix;
  }

////
// Recursively go through the categories and retreive all parent categories IDs
// TABLES: categories
  function tep_get_parent_categories(&$categories, $categories_id) {
    //ccdd
    $parent_categories_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$categories_id . "'");
    while ($parent_categories = tep_db_fetch_array($parent_categories_query)) {
      if ($parent_categories['parent_id'] == 0) return true;
      $categories[sizeof($categories)] = $parent_categories['parent_id'];
      if ($parent_categories['parent_id'] != $categories_id) {
        tep_get_parent_categories($categories, $parent_categories['parent_id']);
      }
    }
  }

////
// Construct a category path to the product
// TABLES: products_to_categories
  function tep_get_product_path($products_id) {
    $cPath = '';
    //ccdd
    $cat_count_sql = tep_db_query("select count(*) as count from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$products_id . "'");
    $cat_count_data = tep_db_fetch_array($cat_count_sql);

    if ($cat_count_data['count'] == 1) {
      $categories = array();
      //ccdd
      $cat_id_sql = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$products_id . "'");
      $cat_id_data = tep_db_fetch_array($cat_id_sql);
      tep_get_parent_categories($categories, $cat_id_data['categories_id']);

      $size = sizeof($categories)-1;
      for ($i = $size; $i >= 0; $i--) {
        if ($cPath != '') $cPath .= '_';
        $cPath .= $categories[$i];
      }
      if ($cPath != '') $cPath .= '_';
      $cPath .= $cat_id_data['categories_id'];
    }

    return $cPath;
  }

////
// Return a product ID with attributes
  function tep_get_uprid($prid, $params) {
    $uprid = $prid;
    if ( (is_array($params)) && (!strstr($prid, '{')) ) {
      while (list($option, $value) = each($params)) {
        $uprid = $uprid . '{' . $option . '}' . $value;
      }
    }

    return $uprid;
  }

////
// Return a product ID from a product ID with attributes
  function tep_get_prid($uprid) {
    $pieces = split('[{]', $uprid, 2);

    return $pieces[0];
  }

////
// Return a customer greeting
  function tep_customer_greeting() {
    global $customer_id, $customer_first_name;
    global $customer_last_name, $language; 

    if ( $customer_last_name || $customer_first_name ) {
      $s_name = ($language == 'japanese')
                ? ($customer_last_name . ' ' . $customer_first_name)
                : ($customer_first_name . ' ' . $customer_last_name);
      $s_name = tep_output_string_protected($s_name);
    }

    if (tep_session_is_registered('customer_first_name') && tep_session_is_registered('customer_id')) {
      $greeting_string = sprintf(TEXT_GREETING_PERSONAL, $s_name, tep_href_link(FILENAME_PRODUCTS_NEW));
    } else {
      $greeting_string = sprintf(TEXT_GREETING_GUEST, tep_href_link(FILENAME_LOGIN, '', 'SSL'), tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));
    }

    return $greeting_string;
  }

////
//! Send email (text/html) using MIME
// This is the central mail function. The SMTP Server should be configured
// correct in php.ini
// Parameters:
// $to_name           The name of the recipient, e.g. "Jan Wildeboer"
// $to_email_address  The eMail address of the recipient, 
//                    e.g. jan.wildeboer@gmx.de 
// $email_subject     The subject of the eMail
// $email_text        The text of the eMail, may contain HTML entities
// $from_email_name   The name of the sender, e.g. Shop Administration
// $from_email_adress The eMail address of the sender, 
//                    e.g. info@mytepshop.com

  function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {
    if (SEND_EMAILS != 'true') return false;
    // Instantiate a new mail object
    $message = new email(array('X-Mailer: iimy Mailer'));

    // Build the text version
    //$text = strip_tags($email_text);
    $text = $email_text;
    if (EMAIL_USE_HTML == 'true') {
      $message->add_html(nl2br($email_text), $text);
    } else {
      $message->add_text($text);
    }

    // Send message
    $message->build_message();
    $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
  }

////
// Check if product has attributes
  function tep_has_product_attributes($products_id) {
    //ccdd
    $attributes_query = tep_db_query("
        select count(*) as count 
        from " . TABLE_PRODUCTS_ATTRIBUTES . " 
        where products_id = '" . (int)$products_id . "'
    ");
    $attributes = tep_db_fetch_array($attributes_query);

    if ($attributes['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }

////
// Get the number of times a word/character is present in a string
// return string length in Japanese
  function tep_word_count($string, $needle) {
    global $language;
    if ($language == 'japanese') {
        return mb_strlen($string);
    }

    $temp_array = split($needle, $string);
    return sizeof($temp_array);
  }

  function tep_count_modules($modules = '') {
    $count = 0;

    if (empty($modules)) return $count;

    $modules_array = split(';', $modules);

    for ($i=0, $n=sizeof($modules_array); $i<$n; $i++) {
      $class = substr($modules_array[$i], 0, strrpos($modules_array[$i], '.'));

      if (!isset($GLOBALS[$class])) $GLOBALS[$class]=NULL;
      if (is_object($GLOBALS[$class])) {
        if ($GLOBALS[$class]->enabled) {
          $count++;
        }
      }
    }

    return $count;
  }

  function tep_count_payment_modules() {
    return tep_count_modules(MODULE_PAYMENT_INSTALLED);
  }

  function tep_count_shipping_modules() {
    return tep_count_modules(MODULE_SHIPPING_INSTALLED);
  }

  function tep_create_random_value($length, $type = 'mixed') {
    if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) return false;

    $rand_value = '';
    while (strlen($rand_value) < $length) {
      if ($type == 'digits') {
        $char = tep_rand(0,9);
      } else {
        $char = chr(tep_rand(0,255));
      }
      if ($type == 'mixed') {
        if (eregi('^[a-z0-9]$', $char)) $rand_value .= $char;
      } elseif ($type == 'chars') {
        if (eregi('^[a-z]$', $char)) $rand_value .= $char;
      } elseif ($type == 'digits') {
        if (ereg('^[0-9]$', $char)) $rand_value .= $char;
      }
    }

    return $rand_value;
  }

  function tep_output_warning($warning) {
    new errorBox(array(array('text' => tep_image(DIR_WS_ICONS . 'warning.gif', ICON_WARNING) . ' ' . $warning)));
  }

  function tep_array_to_string($array, $exclude = '', $equals = '=', $separator = '&') {
    if (!is_array($exclude)) $exclude = array();

    $get_string = '';
    if (sizeof($array) > 0) {
      while (list($key, $value) = each($array)) {
        if ( (!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y') ) {
          $get_string .= $key . $equals . $value . $separator;
        }
      }
      $remove_chars = strlen($separator);
      $get_string = substr($get_string, 0, -$remove_chars);
    }

    return $get_string;
  }

  function tep_not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if (($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }

////
// Output the tax percentage with optional padded decimals
  function tep_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) {
    if (strpos($value, '.')) {
      $loop = true;
      while ($loop) {
        if (substr($value, -1) == '0') {
          $value = substr($value, 0, -1);
        } else {
          $loop = false;
          if (substr($value, -1) == '.') {
            $value = substr($value, 0, -1);
          }
        }
      }
    }

    if ($padding > 0) {
      if ($decimal_pos = strpos($value, '.')) {
        $decimals = strlen(substr($value, ($decimal_pos+1)));
        for ($i=$decimals; $i<$padding; $i++) {
          $value .= '0';
        }
      } else {
        $value .= '.';
        for ($i=0; $i<$padding; $i++) {
          $value .= '0';
        }
      }
    }

    return $value;
  }

////
// Checks to see if the currency code exists as a currency
// TABLES: currencies
  function tep_currency_exists($code) {
    //ccdd
    $currency_code = tep_db_query("select currencies_id from " . TABLE_CURRENCIES . " where code = '" . tep_db_input($code) . "'");
    if (tep_db_num_rows($currency_code)) {
      return $code;
    } else {
      return false;
    }
  }

  function tep_string_to_int($string) {
    return (int)$string;
  }

////
// Parse and secure the cPath parameter values
  function tep_parse_category_path($cPath) {
// make sure the category IDs are integers
    $cPath_array = array_map('tep_string_to_int', explode('_', $cPath));

// make sure no duplicate category IDs exist which could lock the server in a loop
    $tmp_array = array();
    $n = sizeof($cPath_array);
    for ($i=0; $i<$n; $i++) {
      if (!in_array($cPath_array[$i], $tmp_array)) {
        $tmp_array[] = $cPath_array[$i];
      }
    }

    return $tmp_array;
  }

////
// Return a random value
  function tep_rand($min = null, $max = null) {
    static $seeded;

    if (!isset($seeded)) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }

    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }

  // Convert "zen-kaku" alphabets and numbers to "han-kaku"
  // tamura 2002/12/30
  function tep_an_zen_to_han($string) {
    return mb_convert_kana($string, "a");
  }

////
// Return fullname
// for Japanese Localize
  function tep_get_fullname($firstname, $lastname) {
    global $language;
    $separator = ' ';
    if ($language == 'japanese') {
        return $lastname.$separator.$firstname;
    } else {
        return $firstname.$separator.$lastname;
    }
  }

////
// 商品IDからメーカー名を呼び出す
  function ds_tep_get_count_manufactures($manufacturers_id) {
//ccdd
    $manufactures_query = tep_db_query("select count(*) as total from ".TABLE_PRODUCTS." where manufacturers_id = '".$manufacturers_id."'");
    $manufactures = tep_db_fetch_array($manufactures_query);
    
  return $manufactures['total'];
  }
   

////
// 商品IDからメーカー名を呼び出す
  function ds_tep_get_manufactures($manufacturers_id, $return) {
  
  if($return == 1) {
    //メーカー名を返す
//ccdd
    $manufactures_query = tep_db_query("select manufacturers_name from ".TABLE_MANUFACTURERS." where manufacturers_id = '".$manufacturers_id."'");
    $manufactures = tep_db_fetch_array($manufactures_query);
    
    $mreturn = $manufactures['manufacturers_name'];
  } elseif($return == 2) {
    //メーカー画像
//ccdd
    $manufactures_query = tep_db_query("select manufacturers_image from ".TABLE_MANUFACTURERS." where manufacturers_id = '".$manufacturers_id."'");
    $manufactures = tep_db_fetch_array($manufactures_query);
    
    $mreturn = $manufactures['manufacturers_image'];
  }
  
  return $mreturn;
  }
  
////
////
// Ajax用文字コード変換
  function ds_convert_Ajax($string) {
    return mb_convert_encoding($string,'UTF-8','EUC-JP');
  }
  
  function tep_get_full_count($cnt, $rate){
    if (strlen($rate) > 50 or strlen(trim($rate)) < 2) {
      return '';
    }
    if (trim($rate) == '天空の羽毛5個・インクリスクロール5個のセット'){
      return '(天空の羽毛'.number_format(strval(5*$cnt)).'個・インクリスクロール'.number_format(strval(5*$cnt)).'個のセット)';
    }
    if (trim($rate) == 'ネットカフェ1DAYチケット5枚セット'){
      return '(ネットカフェ1DAYチケット'.number_format(strval(5*$cnt)).'枚セット)';
    }
    $rate = str_replace(array(','), array(''), $rate);
    if (preg_match('/^(.*)億(.*)万(.*)$/', $rate, $out)) {
      $rate = (($out[1] * 100000000) + ($out[2] * 10000)) . $out[3];
    }
    $rate = str_replace(array('万','億'), array('0000','00000000'), $rate);
    if (preg_match('/^(\d+)(.*)（\d+.*）$/', $rate, $out)) {
      return '(' . number_format($out[1] * $cnt) . $out[2] . ')';
    }
    if (preg_match('/^(\d+)(.*)\(\d+.*\)$/', $rate, $out)) {
      return '(' . number_format($out[1] * $cnt) . $out[2] . ')';
    }
    if (preg_match('/^(\d+)(.*)$/', $rate, $out)) {
      return '(' . number_format($out[1] * $cnt) . $out[2] . ')';
    }
    if (preg_match('/^([^\d]*)(\d+)([^\d]*)$/', $rate, $out)) {
      return '(' . $out[1] . number_format($out[2] * $cnt) . $out[3] . ')';
    }
    return '';
  }

  function tep_get_full_count_in_order($cnt, $rate){
    if (strlen($rate) > 50 or strlen(trim($rate)) < 2) {
      return '';
    }
    if (trim($rate) == '天空の羽毛5個・インクリスクロール5個のセット'){
      return '天空の羽毛'.number_format(strval(5*$cnt)).'個・インクリスクロール'.number_format(strval(5*$cnt)).'個のセット';
    }
    if (trim($rate) == 'ネットカフェ1DAYチケット5枚セット'){
      return 'ネットカフェ1DAYチケット'.number_format(strval(5*$cnt)).'枚セット';
    }
    
    $rate = str_replace(array(','), array(''), $rate);
    if (preg_match('/^(.*)億(.*)万(.*)$/', $rate, $out)) {
      $rate = (($out[1] * 100000000) + ($out[2] * 10000)) . $out[3];
    }
    $rate = str_replace(array('万','億'), array('0000','00000000'), $rate);
    if (preg_match('/^(\d+)(.*)（\d+.*）$/', $rate, $out)) {
      return number_format($out[1] * $cnt) . $out[2];
    }
    if (preg_match('/^(\d+)(.*)\(\d+.*\)$/', $rate, $out)) {
      return number_format($out[1] * $cnt) . $out[2];
    }
    if (preg_match('/^(\d+)(.*)$/', $rate, $out)) {
      return number_format($out[1] * $cnt) . $out[2];
    }
    if (preg_match('/^([^\d]*)(\d+)([^\d]*)$/', $rate, $out)) {
      return $out[1] . number_format($out[2] * $cnt) . $out[3];
    }
    return '';
  }
  
  function tep_get_torihiki_select_by_products($product_ids = null)
  {
    $torihiki_list = array();
    $torihiki_array = tep_get_torihiki_by_products($product_ids);
    foreach($torihiki_array as $torihiki){
      $torihiki_list[] = array('id' => $torihiki,
        'text' => $torihiki
      );
    }
    if (!isset($torihikihouhou)) $torihikihouhou=NULL;
    return tep_draw_pull_down_menu('torihikihouhou', $torihiki_list, $torihikihouhou);
  }
  
  function tep_get_torihiki_by_products($product_ids = null)
  {
    $option_types = array();
    if ($product_ids) {
      $sql = "select * from `" . TABLE_PRODUCTS . "` where products_id IN (" . implode(',', $product_ids) . ")";
    
      // ccdd
    $product_query = tep_db_query($sql);
    while($product = tep_db_fetch_array($product_query)){
      $option_types[] = $product['option_type'];
    }
    }
    $torihikis = tep_get_torihiki_houhou();
    if($option_types){
      if ($torihikis) {
        foreach ($torihikis as $tkey => $torihiki) {
          if(in_array($tkey, $option_types)){
            return $torihiki;
          }
        }
      }
      if ($torihikis) {
        return array_shift($torihikis);
      } else {
        return null;
      }
    } else if ($torihikis) {
      return array_shift($torihikis);
    } else {
      return null;
    }
  }
  
  // return all types with options
  function tep_get_torihiki_houhou()
  {
    $types = $return = array();
    $types = explode("\n", DS_TORIHIKI_HOUHOU);
    if ($types) {
      foreach($types as $type){
        $atype = explode('//', $type);
        if (isset($atype[0]) && strlen($atype[0]) && isset($atype[1]) && strlen($atype[1])) {
          $return[$atype[0]] = explode('||', $atype[1]);
        }
      }
    }
    return $return;
  }
  
  function tep_get_option_array()
  {
      $return = array();
      $arr = array_keys(tep_get_torihiki_houhou());
      foreach($arr as $key => $value){
        $return [] = array(
          'id' => $value,
          'text' => $value,
        );
      }
      return $return;
  }
  
  function tep_get_disabled_categories()
  {
    $categories_ids = array();
    $categories = array();
    // ccdd
  $categories_query = tep_db_query("select * from `" . TABLE_CATEGORIES . "`");
  while($category = tep_db_fetch_array($categories_query)){
    if($category['categories_status']){
      $categories_ids[] = $category['categories_id'];
    } else {
      $categories[] = $category;
    }
  }

  if($categories){
    while(1) { 
            foreach($categories as $key => $category){
                  $j = 0;
              if(in_array($category['parent_id'], $categories_ids)){
                $categories_ids[] = $category['categories_id'];
                unset($categories[$key]);
                $j ++;
              }
            }
      if($j == 0)break;
    }
  }
  return $categories_ids;
  }
  
  function tep_not_in_disabled_categories()
  {
    static $disabled_categories_ids = null;
    if ($disabled_categories_ids === null) {
      $disabled_categories_ids = tep_get_disabled_categories();
    }
    if ($disabled_categories_ids) {
      return ' ('.implode(',',$disabled_categories_ids).') ';
    } else {
      return ' ("0") ';
    }
  }
  
  function tep_get_disabled_products(){
    $products_ids = array();
    // ccdd
  $products_query = tep_db_query("select p.products_id from `" . TABLE_CATEGORIES . "` c," . TABLE_PRODUCTS . " p, "  . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and c.categories_id in".tep_not_in_disabled_categories());
  while($product = tep_db_fetch_array($products_query)){
    $products_ids[] = $product['products_id'];
  }
  return $products_ids;
  }

  function tep_not_in_disabled_products()
  {
    static $disabled_products_ids = null;
    if ($disabled_products_ids === null) {
      $disabled_products_ids = tep_get_disabled_products();
    }
    if ($disabled_products_ids) {
      return ' ('.implode(',',$disabled_products_ids).') ';
    } else {
      return ' ("0")';
    }
  }
  
  function tep_get_bflag_by_product_id($product_id) {
    // 0 => sell   1 => buy
    // ccdd
    $product_query = tep_db_query("select products_bflag from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_bflag'];
  }

  function tep_get_cflag_by_product_id($product_id) {
    // 0 => no   1=> yes
    // ccdd
    $product_query = tep_db_query("select products_cflag from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_cflag'];
  }

  function tep_get_filename($filename){
    $arr = explode('.', $filename);
    return $arr[0];
  }
  
  function tep_get_value_by_const_name($const_name) {
    eval('$value = ' . $const_name . ';');
    return $value;
  }

  function page_head(){
    global $HTTP_GET_VARS, $request_type, $breadcrumb;
    $title       = C_TITLE;
    $keywords    = C_KEYWORDS;
    $description = C_DESCRIPTION;
    $robots      = C_ROBOTS;
    $copyright   = C_AUTHER;
    
    $search = $replace = array();
   
    //id add script name
    $ssl_pos = strrpos($_SERVER['SCRIPT_NAME'], '/');
    $script_name = substr($_SERVER['SCRIPT_NAME'], $ssl_pos);
    $_SERVER['SCRIPT_NAME'] = $script_name;

    switch (str_replace('/', '', $_SERVER['SCRIPT_NAME'])) {
      case FILENAME_DEFAULT:
         global $cPath_array, $cPath, $seo_tags, $seo_category, $seo_manufacturers;
         if (isset($cPath_array)) {
            if (isset($cPath) && tep_not_null($cPath)) {
              switch(SITE_ID) {
                case '3':
                  $title       = $seo_category['categories_name'] . 'と言えばRMTワールドマネー｜' . (tep_not_null($seo_category['categories_meta_text']) ? $seo_category['categories_meta_text'] : C_TITLE); 
                  break;
                case '2':
                  $title       = $seo_category['categories_name'] . (tep_not_null($seo_category['categories_meta_text']) ? '-' . $seo_category['categories_meta_text'] . '｜激安のRMTゲームマネー' : C_TITLE); 
                  break;
                case '1':
                default:
                  $title       = $seo_category['categories_name'] . (tep_not_null($seo_category['categories_meta_text']) ? '-' . $seo_category['categories_meta_text'] . '専門の' . TITLE : C_TITLE);
                  break;
              }
              $keywords    = $seo_category['meta_keywords'];
              $description = $seo_category['meta_description'];
            }
         } elseif (isset($_GET['manufacturers_id']) && $_GET['manufacturers_id']) {
            $title = $seo_manufacturers['manufacturers_name'] . '-' . C_TITLE;
            // meta_tags
            $metas       = tep_get_metas_by_manufacturers_id(intval($_GET['manufacturers_id']));
            $keywords    = "RMT, " . $metas['keywords'];
            $description = "RMT総合サイト " . TITLE . "へようこそ。" . $metas['description'];
         } else if (isset($_GET['tags_id']) && $_GET['tags_id']) {
           global $breadcrumb;
           $breadcrumb->add($seo_tags['tags_name'], tep_href_link(FILENAME_TAGS, 'tags_id=' . $seo_tags['tags_id']));
           $title = $seo_tags['tags_name'] . '-' . C_TITLE;
           if (!isset($seo_tags['tags_name'])) $seo_tags['tags_name'] = NULL;
           $keywords    = $seo_tags['tags_name'];
           $description = $seo_tags['tags_name'];
         }
         else {
           // no change
           
         }
        break;
      case FILENAME_PRODUCT_INFO:
        global $the_product_name, $the_manufacturers, $the_product_model, $the_product_description,$the_product_category;
        if (isset($the_product_name) && tep_not_null($the_product_name)) {
          $title       = $the_product_name .':'. TITLE;
          $keywords    = TITLE . ', ' . $the_product_name . ', ' . $the_product_model . ', ' . $the_manufacturers['manufacturers_name'];
          $description = $the_product_description . "," . $the_product_name;
        }
        break;
      case FILENAME_PRESENT:
        global $breadcrumb, $present;

        $title = (!$_GET['goods_id']) ? $breadcrumb->trail_title(' &raquo; ') : strip_tags($present['title']);
        if ($present['title']) 
          $keywords = strip_tags($present['title']);
        if ($present['text'])
          $description = mb_substr(strip_tags($present['text']),0,65);
        break;
      case FILENAME_SPECIALS:
        $title       = HEADING_TITLE . ' ' . TITLE;
        switch(SITE_ID){
          case "3":
            $keywords    = "RMT,激安,安い,特価,販売,買取,MMORPG,アイテム,アカウント,ゲーム通貨";
            $description = "今日のお買い得ゲーム一覧。RMTのことなら".TITLE."へ";
            break;
          case "2":
            $keywords = "RMT,お買い得,安い,激安," . TITLE;
            $description = "今がお買い得！" . TITLE . "のお買い得商品一覧。";
            break;
          case "1":
          default:
            $keywords    = "RMT,激安,安い,特価,販売,買取,MMORPG,アイテム,アカウント,ゲーム通貨";
            $description = "今日のお買い得ゲーム一覧。RMTのことなら".TITLE."へ";
            break;
        }
        break;
      case FILENAME_PREORDER:
        global $po_game_c, $product_info;
        switch(SITE_ID){
          case "2":
            $title       = ds_tep_get_categories((int)$_GET['products_id'],1) . '/' . $product_info['products_name'] . '/' . TITLE;
            $keywords    = "rmt,激安,販売," . ds_tep_get_categories((int)$_GET['products_id'],1) . ',' . $product_info['products_name'] . ',' . TITLE;
            $description = ds_tep_get_categories((int)$_GET['products_id'],1) . '-' . $product_info['products_name'] . 'を予約するページです。' . TITLE;
            break;
          case "3":
          case "1":
          default:
            $title       = $po_game_c . '専門の' . TITLE . ' - ' . $product_info['products_name'] . 'を予約する';
            $keywords    = $po_game_c . ',' . $product_info['products_name'] . ", RMT,予約,特価,販売";
            $description = $po_game_c . '専門の' . TITLE . '。' . $product_info['products_name'] . 'を予約するページです。';
            break;
        }
        break;
      case FILENAME_LATEST_NEWS:
        global $breadcrumb, $latest_news;
        $title = (!isset($_GET['news_id']) or !(int)$_GET['news_id']) ? $breadcrumb->trail_title(' &raquo; ') : $latest_news['headline'];
        break;
      case FILENAME_MANUFACTURERS:
        global $breadcrumb;
        switch(SITE_ID){
          case "3":
            $title        = $breadcrumb->trail_title(' &raquo; ');
            $keywords     = "RMT,スクウェア・エニックス,NCJ,ガンホー,NEXON,ゲームオン,コーエー,セガ,販売,買取,アイテム,アカウント";
            $description  = "ゲームメーカーの一覧です。スクウェア・エニックス、NCJ、ガンホーなど。RMTのことなら".TITLE."へ";
            break;
          case "2":
            $title        = $breadcrumb->trail_title(' &raquo; ');
            $keywords     = "RMT,メーカー,一覧";
            $description  = "オンラインゲームのメーカー一覧ページです。" . TITLE;
            break;
          case "1":
          default:
            $title        = $breadcrumb->trail_title(' &raquo; ');
            $keywords     = "RMT,スクウェア・エニックス,NCJ,ガンホー,NEXON,ゲームオン,コーエー,セガ,販売,買取,アイテム,アカウント";
            $description  = "ゲームメーカーの一覧です。スクウェア・エニックス、NCJ、ガンホーなど。RMTのことなら".TITLE."へ";
            break;
        }
        break;
      case FILENAME_REORDER:
      case FILENAME_REORDER2:
        global $breadcrumb;
        $title       = "RMT &raquo; 再配達フォーム｜" . TITLE;
        $keywords    = "RMT,再配達," . TITLE;
        $description = "再配達依頼。取引日時やお届け先を変更するページです。";
        break;
      case FILENAME_SITEMAP:
      case FILENAME_PRESENT_SUCCESS:
      case FILENAME_SHOPPING_CART:
      case FILENAME_INFO_SHOPPING_CART:
      case FILENAME_CHECKOUT_CONFIRMATION:
      case FILENAME_MAGAZINE:
      case FILENAME_PRESENT_CONFIRMATION:
      case FILENAME_LOGIN:
      case FILENAME_PAGE:
      case FILENAME_CHECKOUT_PAYMENT:
      case FILENAME_PRESENT_ORDER:
      case FILENAME_POPUP_SEARCH_HELP:
      case FILENAME_ACCOUNT_EDIT:
      case FILENAME_ACCOUNT:
      case FILENAME_PRODUCT_REVIEWS_WRITE:
      case FILENAME_BROWSER_IE6X:
      case FILENAME_CONTACT_US:
        global $breadcrumb;
        $title = $breadcrumb->trail_title(' &raquo; ');
        break;
      case FILENAME_ADVANCED_SEARCH:
      case FILENAME_CHECKOUT_PRODUCTS:
      case FILENAME_CHECKOUT_SUCCESS:
      case FILENAME_CHECKOUT_PAYMENT_ADDRESS:
      case FILENAME_TELL_A_FRIEND:
      case FILENAME_PRODUCT_REVIEWS_INFO:
      case FILENAME_ACCOUNT_HISTORY:
      case FILENAME_REVIEWS:
      case FILENAME_ADDRESS_BOOK:
      case FILENAME_LOGOFF:
      case FILENAME_ADDRESS_BOOK_PROCESS:
      case FILENAME_PRODUCT_NOTIFICATIONS:
      case FILENAME_CREATE_ACCOUNT_PROCESS:
      case FILENAME_PRODUCT_REVIEWS:
      case FILENAME_CHECKOUT_SHIPPING_ADDRESS:
      case FILENAME_PRODUCTS_NEW:
      case FILENAME_CHECKOUT_SHIPPING:
      case FILENAME_CREATE_ACCOUNT:
      case FILENAME_ACCOUNT_HISTORY_INFO:
      case FILENAME_PASSWORD_FORGOTTEN:
      case FILENAME_ADVANCED_SEARCH_RESULT:
      case FILENAME_CREATE_ACCOUNT_SUCCESS:
        $title = TITLE;
        break;
    }
    //exit($title);
    $script_name = tep_get_filename(str_replace('/', '', $_SERVER['SCRIPT_NAME']));
    

    if ($script_name == 'latest_news' && $_GET['news_id']) {
      $script_name = 'a_'.$script_name;
    }

    $title_const_name       = strtoupper('module_metaseo_' . $script_name . '_title');
    $keywords_const_name    = strtoupper('module_metaseo_' . $script_name . '_keywords');
    $description_const_name = strtoupper('module_metaseo_' . $script_name . '_description');
    $robots_const_name      = strtoupper('module_metaseo_' . $script_name . '_robots');
    $copyright_const_name   = strtoupper('module_metaseo_' . $script_name . '_copyright');

    if (defined($title_const_name) && strlen(tep_get_value_by_const_name($title_const_name))) {
      $title = tep_get_value_by_const_name($title_const_name);
    }
    if (defined($keywords_const_name) && strlen(tep_get_value_by_const_name($keywords_const_name))) {
      $keywords = tep_get_value_by_const_name($keywords_const_name);
    }
    if (defined($description_const_name) && strlen(tep_get_value_by_const_name($description_const_name))) {
      $description = tep_get_value_by_const_name($description_const_name);
    }
    if (defined($robots_const_name) && strlen(tep_get_value_by_const_name($robots_const_name))) {
      $robots = tep_get_value_by_const_name($robots_const_name);
    }
    if (defined($copyright_const_name) && strlen(tep_get_value_by_const_name($copyright_const_name))) {
      $copyright = tep_get_value_by_const_name($copyright_const_name);
    }
    //echo $_SERVER['SCRIPT_NAME'];
    switch (str_replace('/', '', $_SERVER['SCRIPT_NAME'])) {
      case FILENAME_CATEGORY:
      case FILENAME_MANUFACTURER:
        if (isset($cPath_array)) {
           if (isset($cPath) && tep_not_null($cPath)) {
             if (defined('MODULE_METASEO_CATEGORY_TITLE') && strlen(tep_get_value_by_const_name('MODULE_METASEO_CATEGORY_TITLE'))) {
               $title       = tep_get_value_by_const_name('MODULE_METASEO_CATEGORY_TITLE');
             }
             if (defined('MODULE_METASEO_CATEGORY_KEYWORDS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_CATEGORY_KEYWORDS'))) {
               $keywords    = tep_get_value_by_const_name('MODULE_METASEO_CATEGORY_KEYWORDS');
             }
             if (defined('MODULE_METASEO_CATEGORY_DESCRIPTION') && strlen(tep_get_value_by_const_name('MODULE_METASEO_CATEGORY_DESCRIPTION'))) {
               $description = tep_get_value_by_const_name('MODULE_METASEO_CATEGORY_DESCRIPTION');
             }
             if (defined('MODULE_METASEO_CATEGORY_ROBOTS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_CATEGORY_ROBOTS'))) {
               $robots      = tep_get_value_by_const_name('MODULE_METASEO_CATEGORY_ROBOTS');
             }
             if (defined('MODULE_METASEO_CATEGORY_COPYRIGHT') && strlen(tep_get_value_by_const_name('MODULE_METASEO_CATEGORY_COPYRIGHT'))) {
               $copyright   = tep_get_value_by_const_name('MODULE_METASEO_CATEGORY_COPYRIGHT');
             }
             // MAX_DISPLAY_SEARCH_RESULTS
             //$page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
             //$search  = array_merge($search,  array('#SEO_PAGE#'));
             //$replace = array_merge($replace, array($page . 'ページ目'));
             $search  = array_merge($search, array('#CATEGORIES_NAME#','#SEO_NAME#','#SEO_DESCRIPTION#','#CATEGORIES_META_TEXT#','#CATEGORIES_HEADER_TEXT#','#CATEGORIES_FOOTER_TEXT#','#TEXT_INFORMATION#','#META_KEYWORDS#','#META_DESCRIPTION#','#CATEGORIES_ID#',));
             $replace = array_merge($replace, array($seo_category['categories_name'],$seo_category['seo_name'],$seo_category['seo_description_' . ABBR_SITENAME],$seo_category['categories_meta_text'],$seo_category['categories_header_text_' . ABBR_SITENAME],$seo_category['categories_footer_text_' . ABBR_SITENAME],$seo_category['text_information_' . ABBR_SITENAME],$seo_category['meta_keywords_' . ABBR_SITENAME],$seo_category['meta_description_' . ABBR_SITENAME],$seo_category['categories_id'],));
           }
        } elseif ($_GET['manufacturers_id']) {
          if (defined('MODULE_METASEO_MANUFACTURER_TITLE') && strlen(MODULE_METASEO_MANUFACTURER_TITLE)) {
            $title       = tep_get_value_by_const_name('MODULE_METASEO_MANUFACTURER_TITLE');
          }
          if (defined('MODULE_METASEO_MANUFACTURER_KEYWORDS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_MANUFACTURER_KEYWORDS'))) {
            $keywords    = tep_get_value_by_const_name('MODULE_METASEO_MANUFACTURER_KEYWORDS');
          }
          if (defined('MODULE_METASEO_MANUFACTURER_DESCRIPTION') && strlen(tep_get_value_by_const_name('MODULE_METASEO_MANUFACTURER_DESCRIPTION'))) {
            $description = tep_get_value_by_const_name('MODULE_METASEO_MANUFACTURER_DESCRIPTION');
          }
          if (defined('MODULE_METASEO_MANUFACTURER_ROBOTS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_MANUFACTURER_ROBOTS'))) {
            $robots      = tep_get_value_by_const_name('MODULE_METASEO_MANUFACTURER_ROBOTS');
          }
          if (defined('MODULE_METASEO_MANUFACTURER_COPYRIGHT') && strlen(tep_get_value_by_const_name('MODULE_METASEO_MANUFACTURER_COPYRIGHT'))) {
            $copyright   = tep_get_value_by_const_name('MODULE_METASEO_MANUFACTURER_COPYRIGHT');
          }
          $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
          
          $search  = array_merge($search, array('#SEO_PAGE#', '#KEYWORDS#', '#DESCRIPTION#',));
          $replace = array_merge($replace, array($page . 'ページ目', $metas['keywords'], $metas['description'],));
        } else if ((int)$_GET['tags_id']) {
          if (defined('MODULE_METASEO_A_TAG_TITLE') && strlen(tep_get_value_by_const_name('MODULE_METASEO_A_TAG_TITLE'))) {
            $title       = tep_get_value_by_const_name('MODULE_METASEO_A_TAG_TITLE');
          }
          if (defined('MODULE_METASEO_A_TAG_KEYWORDS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_A_TAG_KEYWORDS'))) {
            $keywords    = tep_get_value_by_const_name('MODULE_METASEO_A_TAG_KEYWORDS');
          }
          if (defined('MODULE_METASEO_A_TAG_DESCRIPTION') && strlen(tep_get_value_by_const_name('MODULE_METASEO_A_TAG_DESCRIPTION'))) {
            $description = tep_get_value_by_const_name('MODULE_METASEO_A_TAG_DESCRIPTION');
          }
          if (defined('MODULE_METASEO_A_TAG_ROBOTS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_A_TAG_ROBOTS'))) {
            $robots      = tep_get_value_by_const_name('MODULE_METASEO_A_TAG_ROBOTS');
          }
          if (defined('MODULE_METASEO_A_TAG_COPYRIGHT') && strlen(tep_get_value_by_const_name('MODULE_METASEO_A_TAG_COPYRIGHT'))) {
            $copyright   = tep_get_value_by_const_name('MODULE_METASEO_A_TAG_COPYRIGHT');
          }
          $search  = array_merge($search, array('#TITLE#'));
          $replace = array_merge($replace, array($latest_news['headline']));
        } else {
          if (defined('MODULE_METASEO_DEFAULT_PAGE_TITLE') && strlen(tep_get_value_by_const_name('MODULE_METASEO_DEFAULT_PAGE_TITLE'))) {
            $title       = tep_get_value_by_const_name('MODULE_METASEO_DEFAULT_PAGE_TITLE');
          }
          if (defined('MODULE_METASEO_DEFAULT_PAGE_KEYWORDS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_DEFAULT_PAGE_KEYWORDS'))) {
            $keywords    = tep_get_value_by_const_name('MODULE_METASEO_DEFAULT_PAGE_KEYWORDS');
          }
          if (defined('MODULE_METASEO_DEFAULT_PAGE_DESCRIPTION') && strlen(tep_get_value_by_const_name('MODULE_METASEO_DEFAULT_PAGE_DESCRIPTION'))) {
            $description = tep_get_value_by_const_name('MODULE_METASEO_DEFAULT_PAGE_DESCRIPTION');
          }
          if (defined('MODULE_METASEO_DEFAULT_PAGE_ROBOTS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_DEFAULT_PAGE_ROBOTS'))) {
            $robots      = tep_get_value_by_const_name('MODULE_METASEO_DEFAULT_PAGE_ROBOTS');
          }
          if (defined('MODULE_METASEO_DEFAULT_PAGE_COPYRIGHT') && strlen(tep_get_value_by_const_name('MODULE_METASEO_DEFAULT_PAGE_COPYRIGHT'))) {
            $copyright   = tep_get_value_by_const_name('MODULE_METASEO_DEFAULT_PAGE_COPYRIGHT');
          }
        }
        break;
      case FILENAME_PRODUCT_INFO:
        $search  = array_merge($search, array('#PRODUCT_NAME#', '#PRODUCT_MODEL#', '#PRODUCT_DESCRITION#', '#MANUFACTURERS_NAME#','#PRODUCT_CATEGORY#'));
        $replace = array_merge($replace, array($the_product_name, $the_product_model, $the_product_description, $the_manufacturers['manufacturers_name'],$the_product_category));
        break;
      case FILENAME_PREORDER:
        $search  = array_merge($search, array('#CATEGORIES_NAME#','#PRODUCTS_NAME#'));
        $replace = array_merge($replace, array($po_game_c, $product_info['products_name']));
        break;
      case FILENAME_LATEST_NEWS:
      case FILENAME_A_LATEST_NEWS:
        if ((int)$_GET['news_id']) {
          if (defined('MODULE_METASEO_A_LATEST_NEWS_TITLE') && strlen(tep_get_value_by_const_name('MODULE_METASEO_A_LATEST_NEWS_TITLE'))) {
            $title       = tep_get_value_by_const_name('MODULE_METASEO_A_LATEST_NEWS_TITLE');
          }
          if (defined('MODULE_METASEO_A_LATEST_NEWS_KEYWORDS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_A_LATEST_NEWS_KEYWORDS'))) {
            $keywords    = tep_get_value_by_const_name('MODULE_METASEO_A_LATEST_NEWS_KEYWORDS');
          }
          if (defined('MODULE_METASEO_A_LATEST_NEWS_DESCRIPTION') && strlen(tep_get_value_by_const_name('MODULE_METASEO_A_LATEST_NEWS_DESCRIPTION'))) {
            $description = tep_get_value_by_const_name('MODULE_METASEO_A_LATEST_NEWS_DESCRIPTION');
          }
          if (defined('MODULE_METASEO_A_LATEST_NEWS_ROBOTS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_A_LATEST_NEWS_ROBOTS'))) {
            $robots      = tep_get_value_by_const_name('MODULE_METASEO_A_LATEST_NEWS_ROBOTS');
          }
          if (defined('MODULE_METASEO_A_LATEST_NEWS_COPYRIGHT') && strlen(tep_get_value_by_const_name('MODULE_METASEO_A_LATEST_NEWS_COPYRIGHT'))) {
            $copyright   = tep_get_value_by_const_name('MODULE_METASEO_A_LATEST_NEWS_COPYRIGHT');
          }
          $search  = array_merge($search, array('#TITLE#'));
          $replace = array_merge($replace, array($latest_news['headline']));
        } else {
          if (defined('MODULE_METASEO_LATEST_NEWS_TITLE') && strlen(tep_get_value_by_const_name('MODULE_METASEO_LATEST_NEWS_TITLE'))) {
            $title       = tep_get_value_by_const_name('MODULE_METASEO_LATEST_NEWS_TITLE');
          }
          if (defined('MODULE_METASEO_LATEST_NEWS_KEYWORDS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_LATEST_NEWS_KEYWORDS'))) {
            $keywords    = tep_get_value_by_const_name('MODULE_METASEO_LATEST_NEWS_KEYWORDS');
          }
          if (defined('MODULE_METASEO_LATEST_NEWS_DESCRIPTION') && strlen(tep_get_value_by_const_name('MODULE_METASEO_LATEST_NEWS_DESCRIPTION'))) {
            $description = tep_get_value_by_const_name('MODULE_METASEO_LATEST_NEWS_DESCRIPTION');
          }
          if (defined('MODULE_METASEO_LATEST_NEWS_ROBOTS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_LATEST_NEWS_ROBOTS'))) {
            $robots      = tep_get_value_by_const_name('MODULE_METASEO_LATEST_NEWS_ROBOTS');
          }
          if (defined('MODULE_METASEO_LATEST_NEWS_COPYRIGHT') && strlen(tep_get_value_by_const_name('MODULE_METASEO_LATEST_NEWS_COPYRIGHT'))) {
            $copyright   = tep_get_value_by_const_name('MODULE_METASEO_LATEST_NEWS_COPYRIGHT');
          }
          $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
          $search  = array_merge($search,  array('#SEO_PAGE#'));
          $replace = array_merge($replace, array($page . 'ページ目'));
        }
        break;
      case FILENAME_TAGS:
          /*
          if (defined('MODULE_METASEO_TAGS_TITLE') && strlen(tep_get_value_by_const_name('MODULE_METASEO_TAGS_TITLE'))) {
            $title       = tep_get_value_by_const_name('MODULE_METASEO_TAGS_TITLE');
          }
          if (defined('MODULE_METASEO_TAGS_KEYWORDS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_TAGS_KEYWORDS'))) {
            $keywords    = tep_get_value_by_const_name('MODULE_METASEO_TAGS_KEYWORDS');
          }
          if (defined('MODULE_METASEO_TAGS_DESCRIPTION') && strlen(tep_get_value_by_const_name('MODULE_METASEO_TAGS_DESCRIPTION'))) {
            $description = tep_get_value_by_const_name('MODULE_METASEO_TAGS_DESCRIPTION');
          }
          if (defined('MODULE_METASEO_TAGS_ROBOTS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_TAGS_ROBOTS'))) {
            $robots      = tep_get_value_by_const_name('MODULE_METASEO_TAGS_ROBOTS');
          }
          if (defined('MODULE_METASEO_TAGS_COPYRIGHT') && strlen(tep_get_value_by_const_name('MODULE_METASEO_TAGS_COPYRIGHT'))) {
            $copyright   = tep_get_value_by_const_name('MODULE_METASEO_TAGS_COPYRIGHT');
          }*/
          $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
          $search  = array_merge($search,  array('#SEO_PAGE#'));
          $replace = array_merge($replace, array($page . 'ページ目'));
        
        break;
      case FILENAME_MANUFACTURERS:
        // MAX_DISPLAY_SEARCH_RESULTS
        $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
        $search  = array_merge($search,  array('#SEO_PAGE#'));
        $replace = array_merge($replace, array($page . 'ページ目'));
        break;
      case FILENAME_PRESENT:
        // MAX_DISPLAY_SEARCH_RESULTS
        $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
        $search  = array_merge($search,  array('#SEO_PAGE#'));
        $replace = array_merge($replace, array($page . 'ページ目'));
        break;
      case FILENAME_PRODUCT_NEW:
        // MAX_DISPLAY_PRODUCTS_NEW
        $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
        $search  = array_merge($search,  array('#SEO_PAGE#'));
        $replace = array_merge($replace, array($page . 'ページ目'));
        break;
      case FILENAME_SPECIALS:
        // MAX_DISPLAY_SPECIAL_PRODUCTS
        $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
        $search  = array_merge($search,  array('#SEO_PAGE#'));
        $replace = array_merge($replace, array($page . 'ページ目'));
        break;
      case FILENAME_ADVANCED_SEARCH_RESULT:
        // MAX_DISPLAY_SEARCH_RESULTS
        $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
        $search  = array_merge($search,  array('#SEO_PAGE#'));
        $replace = array_merge($replace, array($page . 'ページ目'));
        break;
      case FILENAME_REVIEWS:
        // MAX_DISPLAY_NEW_REVIEWS
        $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
        $search  = array_merge($search,  array('#SEO_PAGE#'));
        $replace = array_merge($replace, array($page . 'ページ目'));
        break;
      case FILENAME_PRODUCT_REVIEWS_INFO:
        global $reviews;
        if (preg_match_all('/#(\d*)REVIEWS#/', MODULE_METASEO_PRODUCT_REVIEWS_INFO_TITLE, $out)) {
          foreach($out[0] as $key => $value){
            $search  = array_merge($search,  array($out[0][$key]));
            $replace = array_merge($replace, array(mb_substr(strip_tags($reviews['reviews_text']),0,$out[1][$key],'UTF-8')));
          }
        }
        if (preg_match_all('/#(\d*)REVIEWS#/', MODULE_METASEO_PRODUCT_REVIEWS_INFO_KEYWORDS, $out)) {
          foreach($out[0] as $key => $value){
            $search  = array_merge($search,  array($out[0][$key]));
            $replace = array_merge($replace, array(mb_substr(strip_tags($reviews['reviews_text']),0,$out[1][$key],'UTF-8')));
          }
        }
        if (preg_match_all('/#(\d*)REVIEWS#/', MODULE_METASEO_PRODUCT_REVIEWS_INFO_DESCRIPTION, $out)) {
          foreach($out[0] as $key => $value){
            $search  = array_merge($search,  array($out[0][$key]));
            $replace = array_merge($replace, array(mb_substr(strip_tags($reviews['reviews_text']),0,$out[1][$key],'UTF-8')));
          }
        }
        break;
    }
    $breadcrumb_str = $breadcrumb->trail_title(' &raquo; ');
    $breadcrumb_lat = '';
    $breadcrumb_arr = explode('&raquo;', $breadcrumb_str);
    if (is_array($breadcrumb_arr)) {
      $bread_num = count($breadcrumb_arr); 
      $breadcrumb_lat = trim($breadcrumb_arr[$bread_num-1]);  
    }
    $search  = array_merge(array('#STORE_NAME#','#BREADCRUMB#', '#PAGE_TITLE#', '#BREADCRUMB_KEYWORD#', '#BREADCRUMB_FIRST#'), $search);
    $replace = array_merge(array(STORE_NAME, $breadcrumb_str, $breadcrumb_lat, str_replace(' &raquo; ', ',', $breadcrumb_str), trim($breadcrumb_arr[1])), $replace);
    if (!in_array('#SEO_PAGE#', $search)) {
      $c_page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
      $search = array_merge(array('#SEO_PAGE#'), $search); 
      $replace = array_merge(array($c_page.'ページ目'), $replace); 
    }
    $title       = str_replace($search, $replace, $title);
    $keywords    = str_replace($search, $replace, $keywords);
    $description = str_replace($search, $replace, $description);
    // replace again
    $title       = str_replace($search, $replace, $title);
    $keywords    = str_replace($search, $replace, $keywords);
    $description = str_replace($search, $replace, $description);
    //
    $title = str_replace(' &raquo; ', ' ', $title); 
    $keywords = str_replace(' &raquo; ', ' ', $keywords); 
    $description = str_replace(' &raquo; ', ' ', $description); 
  ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo strip_tags($title ? $title : TITLE); ?></title>
<meta name="keywords" content="<?php echo $keywords ? $keywords : C_KEYWORDS;?>">
<meta name="description" content="<?php echo $description ? $description : C_DESCRIPTION;?>">
<meta name="robots" content="<?php echo strtoupper($robots ? $robots : C_ROBOTS);?>">
<meta name="copyright" content="<?php echo $copyright ? $copyright : C_AUTHER;?>">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css"> 
<?php
    switch (str_replace('/', '', $_SERVER['SCRIPT_NAME'])) {
      case FILENAME_CATEGORY:
        if (isset($cPath) && $cPath || isset($_GET['tags_id']) || isset($_GET['manufacturers_id'])) {
?>
<script type="text/javascript" src="js/sort.js"></script>
<?php
        }
        break;
    }
  }

  function tep_get_metas_by_manufacturers_id($manufacturers_id)
  {
        //Step 1. Construct the general Query!

        $metaQuery  = "SELECT `products_description`.`products_name`, `categories_description`.`categories_name`, `manufacturers`.`manufacturers_name` ";
        $metaQuery .= "FROM products, products_description, products_to_categories, categories, categories_description, ".TABLE_LANGUAGES.", manufacturers, ".TABLE_CONFIGURATION." ";
        $metaQuery .= "WHERE products.products_id = products_description.products_id ";
        $metaQuery .= "AND products_description.language_id = ".TABLE_LANGUAGES.".languages_id ";
        $metaQuery .= "AND products_description.products_id = products_to_categories.products_id ";
        $metaQuery .= "AND products_to_categories.categories_id = categories.categories_id ";
        $metaQuery .= "AND categories.categories_id = categories_description.categories_id ";
        $metaQuery .= "AND categories_description.language_id = ".TABLE_LANGUAGES.".languages_id ";
        $metaQuery .= "AND products.manufacturers_id = manufacturers.manufacturers_id ";
        $metaQuery .= "AND products.products_status != '0' ";
        $metaQuery .= "AND ".TABLE_CONFIGURATION.".configuration_key = 'DEFAULT_LANGUAGE' ";
        $metaQuery .= "AND ".TABLE_LANGUAGES.".code = ".TABLE_CONFIGURATION.".configuration_value ";
        $metaQuery .= "AND ".TABLE_CONFIGURATION.".site_id= '".SITE_ID."' ";
        $metaQuery .= "AND products_description.site_id= '".SITE_ID."' ";
        $metaQuery .= "AND categories_description.site_id= '".SITE_ID."' ";

        //Step 2. Narrow the search!
        
        //Are we looking within a manufacturer?
        if (isset($manufacturers_id) && tep_not_null($manufacturers_id))
        {
        
          $metaManufacturersId = $manufacturers_id;
        
          $metaQuery .= "AND manufacturers.manufacturers_id = '" . $metaManufacturersId . "' ";
        }
        
        //Step 3. Extract the info from the DB
        // ccdd
        $metaQueryResult = tep_db_query ( $metaQuery );
        
        $metaProductsNames = array();
        $metaCategoriesNames = array();
        $metaManufacturersNames = array();
        
        //Step 4. Remove duplicates by using the name as the key in an array
        while($metaQueryData = tep_db_fetch_array ($metaQueryResult))
        {
          $metaProductsNames[$metaQueryData['products_name']] = $metaQueryData['products_name'];
          $metaCategoriesNames[$metaQueryData['categories_name']] = $metaQueryData['categories_name'];
          $metaManufacturersNames[$metaQueryData['manufacturers_name']] = $metaQueryData['manufacturers_name'];
        }
        
        //Step 5. Construct the keywords
        $metaKeywords = "";
        foreach($metaProductsNames as $metaProductsName)
        {
          if($metaKeywords == "")
          {
            //First Row
            $metaKeywords = $metaProductsName;
          }
          else
          {
            //Other Rows
            $metaKeywords .= ", " . $metaProductsName;
          }
        }
        
        foreach($metaCategoriesNames as $metaCategoriesName)
        {
          if($metaKeywords == "")
          {
            //No previous entries
            $metaKeywords = $metaCategoriesName;
          }
          else
          {
            //Other Rows
            $metaKeywords .= ", " . $metaCategoriesName;
          }
        }
          
        //Limit the keywords to 1000 characters
        $metaKeywords = mb_substr($metaKeywords, 0, 90);
        
        //Step 6. Construct the description
        $metaDescription = "";
        $i = 0;
        foreach($metaManufacturersNames as $metaManufacturersName)
        {
          //Limit the decription to 150 words
          if($i >= 149)
          {
            break;
          }
        
          if($i == 0)
          {
            //First Row
            $metaDescription .= $metaManufacturersName;
          }
          else
          {
            //Other Rows
            $metaDescription .= ", " . $metaManufacturersName;
          }
        
          $i++;
        }

        return array(
          'keywords'    => $metaKeywords,
          'description' => $metaDescription
        );
  }
  // checkout_confirmation.php
  function str_string($string='') {
    if(ereg("-", $string)) {
    $string_array = explode("-", $string);
    return $string_array[0] . '年' . $string_array[1] . '月' . $string_array[2] . '日';
  }
  }
  // checkout_process.php
  // reorder.php
/*
  function str_string($string='') {
    if(ereg("-", $string)) {
    $string_array = explode("-", $string);
    return $string_array[0] . '年' . $string_array[1] . '月' . $string_array[2] . '日';
  }
  }*/
  // checkout_process.php
  # Random
  function ds_makeRandStr( $len=2 ) {

    $strElem = "0123456789";

    $strElemArray = preg_split("//", $strElem, 0, PREG_SPLIT_NO_EMPTY);

    $retStr = "";

    srand( (double)microtime() * 100000);

    for( $i=0; $i<$len; $i++ ) {

        $retStr .= $strElemArray[array_rand($strElemArray, 1)];

    }

    return $retStr;

  }
// download.php
// Returns a random name, 16 to 20 characters long
// There are more than 10^28 combinations
// The directory is "hidden", i.e. starts with '.'
function tep_random_name()
{
  $letters = 'abcdefghijklmnopqrstuvwxyz';
  $dirname = '.';
  $length = floor(tep_rand(16,20));
  for ($i = 1; $i <= $length; $i++) {
   $q = floor(tep_rand(1,26));
   $dirname .= $letters[$q];
  }
  return $dirname;
}
// download.php
// Unlinks all subdirectories and files in $dir
// Works only on one subdir level, will not recurse
function tep_unlink_temp_dir($dir)
{
  $h1 = opendir($dir);
  while ($subdir = readdir($h1)) {
// Ignore non directories
    if (!is_dir($dir . $subdir)) continue;
// Ignore . and .. and CVS
    if ($subdir == '.' || $subdir == '..' || $subdir == 'CVS') continue;
// Loop and unlink files in subdirectory
    $h2 = opendir($dir . $subdir);
    while ($file = readdir($h2)) {
      if ($file == '.' || $file == '..') continue;
      @unlink($dir . $subdir . '/' . $file);
    }
    closedir($h2); 
    @rmdir($dir . $subdir);
  }
  closedir($h1);
}
// ggsitemap.php
  function get_cPath($id, $categories)
  {
      if($categories[$id]['parent_id'] == '0'){
        return $categories[$id]['categories_id'];
      } else {
        return ($categories[$categories[$id]['parent_id']]['parent_id'] == 0 ? $categories[$categories[$id]['parent_id']]['categories_id'] : $categories[$categories[$id]['parent_id']]['parent_id'].'_'.$categories[$categories[$id]['parent_id']]['categories_id']) . '_' . $categories[$id]['categories_id'];
      }
  }
// ggsitemap.php
  function gg_url($loc, $lastmod = null, $changefreq = 'daily', $priority = 0.3)
  {
?>
  <url>
    <loc><?php echo $loc;?></loc>
    <lastmod><?php echo $lastmod?$lastmod:date('c');?></lastmod>
    <changefreq><?php echo $changefreq?$changefreq:'daily';?></changefreq>
    <priority><?php echo $priority;?></priority>
  </url>
<?php
  }
// present_confirmation.php
      function tep_get_zone_list2($name, $selected = '', $country_code = '107') {
        // ccdd
        $zones_query = tep_db_query("select zone_name, zone_id from ".TABLE_ZONES." where zone_country_id = '107' order by zone_code");
        $string = '<select name="'.$name.'">';
        while ($zones_values = tep_db_fetch_array($zones_query)) {
          $string .= '<option value="'.$zones_values['zone_id'].'"';
          if($zones_values['zone_id'] == $selected) $string .= ' selected';
          $string .= '>'.$zones_values['zone_name'].'</option>';
        }
        $string .= '</select>';
        return $string;
      }  

// Get Categories_image & subcategories_name
  function ds_tep_get_categories($products_id, $return) {
    global $languages_id;
  
  $categories_path = tep_get_product_path($products_id);
  $categories_path_array = explode("_", $categories_path);
  
  if($return == 1) {
    //大カテゴリの画像を返す
    // ccdd
    $categories_query = tep_db_query("
        select categories_name
        from ".TABLE_CATEGORIES_DESCRIPTION." 
        where categories_id = '".$categories_path_array[0]."' 
          and (site_id = ". SITE_ID ." or site_id = '0')
        order by site_id DESC"
        );
    $categories = tep_db_fetch_array($categories_query);
    
    $creturn = $categories['categories_name'];
  } elseif($return == 2) {
    //中カテゴリ名を返す
    // ccdd
    $categories_query = tep_db_query("
      select categories_name 
      from ".TABLE_CATEGORIES_DESCRIPTION." 
      where categories_id = '".(isset($categories_path_array[1])?$categories_path_array[1]:'')."' 
        and language_id = '".$languages_id."' 
        and (site_id = ".SITE_ID . " or site_id = '0')
      order by site_id DESC"
      );
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
    // ccdd
    $query = tep_db_query("select products_price from ".TABLE_PRODUCTS." where products_id = '".$products_id."'");
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
    // ccdd
    $query = tep_db_query("select * from ".TABLE_PRODUCTS." where products_id = '".(int)tep_get_prid($pID)."'");
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

  function tep_get_category_by_id($cid, $site_id, $lid, $default = true){
    $sql = "
        select c.categories_id,
               c.categories_status,
               c.categories_image,
               c.parent_id,
               c.sort_order,
               c.date_added,
               c.last_modified,
               cd.site_id,
               cd.language_id,
               cd.categories_name,
               cd.seo_name,
               cd.categories_image2,
               cd.categories_image3,
               cd.categories_meta_text,
               cd.seo_description,
               cd.categories_header_text,
               cd.categories_footer_text,
               cd.text_information,
               cd.meta_keywords,
               cd.meta_description
        from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where c.categories_id = '" . $cid. "' 
          and cd.categories_id = '" . $cid. "' 
          and cd.language_id = '" . $lid.  "'"
    ;
    if ($default) {
          $sql .= " and (cd.site_id = '" . $site_id . "' or cd.site_id = '0') order by site_id DESC";
    } else {
          $sql .= " and cd.site_id = '" . $site_id . "'";
    }
    $category_query = tep_db_query($sql);
    $category = tep_db_fetch_array($category_query);
    return $category;
  }

  function tep_get_product_by_id($pid,$site_id, $lid, $default = true){
    $sql = "
        SELECT p.products_id, 
               p.products_quantity, 
               p.products_model, 
               p.products_image, 
               p.products_image2, 
               p.products_image3, 
               p.products_price, 
               p.products_price_offset,
               p.products_date_added, 
               p.products_date_available, 
               p.products_weight,
               p.products_status,
               p.products_tax_class_id, 
               p.manufacturers_id,
               p.products_ordered,
               p.products_bflag,
               p.products_cflag,
               p.products_small_sum,
               p.option_type,
               p.products_attention_1, 
               p.products_attention_2, 
               p.products_attention_3, 
               p.products_attention_4, 
               p.products_attention_5, 
               pd.language_id,
               pd.products_name, 
               pd.products_description,
               pd.site_id,
               pd.products_url,
               pd.products_viewed
        FROM " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
        WHERE p.products_id = '" . $pid . "' 
          AND p.products_status != '0' 
          AND pd.products_id = '" .  $pid . "'" . " 
          AND pd.language_id ='" . $lid . "' 
          "; 
    if ($default) {
      $sql .= "
        AND (pd.site_id = '0'
         OR pd.site_id = '".$site_id."')
        ORDER BY pd.site_id DESC
        ";
    } else {
      $sql .= "
          AND pd.site_id='" . $site_id . "' 
      ";
    }
    //echo $sql;
    $product_query = tep_db_query($sql);
    $product = tep_db_fetch_array($product_query);
    return $product;
  }
/*
    function tep_categories_description_exist($cid, $sid, $lid){
      $query = tep_db_query("select * from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cid."' and site_id = '".$sid."' and language_id='".$lid."'");
      if(tep_db_num_rows($query)) {
        return true;
      } else {
        return false;
      }
    }
*/
    function tep_products_description_exist($pid, $sid, $lid){
      $query = tep_db_query("
          select * 
          from ".TABLE_PRODUCTS_DESCRIPTION." 
          where products_id='".$pid."' 
            and site_id = '".$sid."' 
            and language_id='".$lid."'");
      if(tep_db_num_rows($query)) {
        return true;
      } else {
        return false;
      }
    }

  //function tep_module_installed(){
  //}

  function tep_get_faq_categories($c_id){
    $query = tep_db_query("
        select * 
        from faq_categories 
        where c_id = '".(int)$c_id."'
        ");
    return tep_db_fetch_array($query);
  }

  function tep_get_faq_questions($q_id){
    $query = tep_db_query("
        select * 
        from faq_questions 
        where q_id = '".(int)$q_id."'
        ");
    return tep_db_fetch_array($query);
  }

  function tep_get_faq_categories_by_g_id($g_id){
    $categories = array();
    $query = tep_db_query("
        select * 
        from faq_categories 
        where g_id = '".(int)$g_id."'
        ");
    while($c = tep_db_fetch_array($query)){
      $categories[] = $c;
    }
    return $categories;
  }

  function  tep_get_questions_by_c_id($c_id){
    $questions = array();
    $query = tep_db_query("
        select * 
        from faq_questions 
        where c_id = '".(int)$c_id."'
        ");
    while($q = tep_db_fetch_array($query)){
      $questions[] = $q;
    }
    return $questions;
  }
  
  function calc_buy_handle($total)
  {
    $buy_table_fee = split("[:,]", MODULE_PAYMENT_BUYING_COST);
    $buying_fee = 0;
    for ($i = 0; $i < count($buy_table_fee); $i+=2) {
      if ($total <= $buy_table_fee[$i]) {
        $buy_add_fee = $total.$buy_table_fee[$i+1]; 
        @eval("\$buy_add_fee = $buy_add_fee;");
        if (is_numeric($buy_add_fee)) {
          $buying_fee = $buy_add_fee; 
        }
        break; 
      }
    }
    return $buying_fee; 
  }

function tep_orders_status_finished($osid){
    $query = tep_db_query("
        select * 
        from  ".TABLE_ORDERS_STATUS."
        where orders_status_id = '".(int)$osid."'
        ");
    $os = tep_db_fetch_array($query);
    return isset($os['finished']) && $os['finished'];
}


  function tep_get_rss($url){
    //寡鹿議方怏
    $input_arr = array();
    $i = 0; 
    //貫rss嶄寡鹿仟療議url
    /*
    $cache_file = '/tmp/' . base64_encode($url);
    if(file_exists($cache_file) && time() - filemtime($cache_file) > ){
      file_get_contents($cache_file);
    } else {
      $rss_str = file_get_contents($url);
      file_put_contents($cache_file, $rss_str);
    }
    */
    $rss_str = @file_get_contents($url);
    preg_match_all("/\<item rdf:about=\"([^\"]*)\"\>(.*?)\<\/item\>/s", $rss_str, $gamearr);
    //rss嶄頁倦嗤仟療方象
    if (!empty($gamearr[2])) {
      foreach ($gamearr[2] as $gkey => $game)
      {
        preg_match_all("/\<title\>(.*?)\<\/title\>/", $game, $title);
        preg_match_all("/\<link\>(.*?)\<\/link\>/", $game, $link);
        preg_match_all("/\<dc:date\>(.*?)\<\/dc:date\>/", $game, $date_added);
        //仟療議銭俊頁倦贋壓 
        if (isset($link[1][0])) {
          //仟療議url 
          $input_arr[$i]['url'] =  $link[1][0]; 
          //仟療議炎籾 
          if (isset($title[1][0])) {
            $input_arr[$i]['headline'] =  $title[1][0]; 
          } else {
            $input_arr[$i]['headline'] =  ''; 
          }
          $input_arr[$i]['date_added'] = date('Y-m-d H:i:s', strtotime($date_added[1][0]));
        }
        $i++;
      }
    }
    return $input_arr;
  }
  
  function tep_get_categories_rss($cid){
    $rss = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CATEGORIES_RSS." where categories_id='".$cid."'"));
    if($rss && isset($rss['categories_rss']) && $rss['categories_rss']){
      return tep_get_rss($rss['categories_rss']);
    } else {
      return null;
    }
  }

  function tep_get_categories_by_parent_id($parent_id, $languages_id = 4) {
    $categories = array();

    $query = tep_db_query("
      select *
      from (
        select c.categories_id, cd.categories_name ,c.sort_order, cd.site_id
        from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where parent_id = '" . tep_db_input($parent_id) . "' 
          and c.categories_id = cd.categories_id 
          and cd.language_id = '" . (int)$languages_id . "' 
        order by cd.site_id DESC
      ) c
      where site_id = '0'
         or site_id = ".SITE_ID." 
      group by categories_id
      order by sort_order, categories_name");
    
    while($c = tep_db_fetch_array($query)){
      $categories[] = $c;
    }
    return $categories;
  }
function tep_show_warning($categories_id) {
  $categories_query = tep_db_query("select * from " . TABLE_CATEGORIES . " where categories_id='" . $categories_id . "' LIMIT 1");
  $categories = tep_db_fetch_array($categories_query);
  if ($categories) {
    if ($categories['categories_status'] != '0') {
      return $categories['categories_status'];
    } else if ($categories['parent_id'] != '0') {
      return tep_show_warning($categories['parent_id']);
    } else {
      return false;
    }
  } else {
    return false;
  }
}

function tep_get_products_categories_id($products_id) {
  $query = tep_db_query("select * from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $products_id . "'");
  $c = tep_db_fetch_array($query);
  return $c['categories_id'];
}


function tep_get_products_special_price($product_id) {
  $product_query = tep_db_query("select * from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
  $product = tep_db_fetch_array($product_query);

  return tep_get_special_price($product['products_price'], $product['products_price_offset'], $product['products_small_sum']);
}

function tep_get_special_price($price, $offset, $sum = '') {
  if ($price && $sum) {
    $lprice = $price;
    foreach (tep_get_wari_array_by_sum($sum) as $p) {
      if ($p + $price < $lprice) {
        $lprice = $p + $price;
      }
    }
    return $lprice;
  } else if ($price && $offset && $offset != 0) {
    return $price;
  } else {
    return false;
  }
}

function tep_get_price ($price, $offset, $sum = '') {
  if ($price && $sum) {
    $hprice = $price;
    foreach (tep_get_wari_array_by_sum($sum) as $p) {
      if ($p + $price > $hprice) {
        $hprice = $p + $price;
      }
    }
    return $hprice;
  } else if ($price && $offset && $offset != 0) {
    return calculate_special_price($price, $offset);
  } else {
    return $price;
  }
}

function tep_get_final_price($price, $offset, $sum, $quantity) {
  if ($price && $sum) {
    $lprice = $price;
    $lq = null;
    $wari_array = tep_get_wari_array_by_sum($sum);
    ksort($wari_array);
    
    foreach ($wari_array as $q => $p) {
      if ($lq === null or ($q > $lq && $q <= $quantity)) {
        $lq = $q;
        $lprice = $p;
      }
    }
    return $price + $lprice;
  } else if ($price && $offset && $offset != 0) {
    //return calculate_special_price($price, $offset);
    return $price;
  } else {
    return $price;
  }
}

function tep_get_products_price ($products_id) {
  $product_query = tep_db_query("select * from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
  $product = tep_db_fetch_array($product_query);
  return array(
    'price' => tep_get_price($product['products_price'], $product['products_price_offset'], $product['products_small_sum']),
    'sprice' => tep_get_special_price($product['products_price'], $product['products_price_offset'], $product['products_small_sum'])
  );
}


function tep_get_wari_array_by_sum($small_sum) {
  $wari_array = array();
  if(tep_not_null($small_sum)) {
    $parray = explode(",", $small_sum);
    for($i=0; $i<sizeof($parray); $i++) {
      $tt = explode(':', $parray[$i]);
      $wari_array[$tt[0]] = $tt[1];
    }
  }
  @krsort($wari_array);
  return $wari_array;
}
/*
function calculate_special_price($price, $offset) {
  $price = (float) $price;
  $offset = trim($offset);
  
  $special = $price;
  
  if (substr($offset, -1) == '%') {
    $special = $price +(($offset / 100) * $price);
  } else {
    $offset = (float) $offset;
    $special = $price + $offset;
  }
  return $special;
}*/

function calculate_special_price($price, $offset) {
  $price = (float) $price;
  $offset = trim($offset);
  
  $special = $price;
  
  if (substr($offset, -1) == '%') {
    $special = $price +(($offset / 100) * $price);
  } else {
    $offset = (float) $offset;
    $special = $price + $offset;
  }
  return $special;
}

// 代替触发器
function orders_status_updated($orders_status_id) {
  $orders_status = tep_db_fetch_array(tep_db_query("select * from orders_status where orders_status_id='".$orders_status_id."'"));
  tep_db_query("
    update ".TABLE_ORDERS." set language_id='".$orders_status['language_id']."',orders_status_name='".$orders_status['orders_status_name']."',orders_status_image='".$orders_status['orders_status_image']."',finished='".$orders_status['finished']."'
  ");
}

// 代替存储过程
function orders_updated($orders_id) {
  tep_db_query("update ".TABLE_ORDERS." set language_id = ( select language_id from ".TABLE_ORDERS_STATUS." where orders_status.orders_status_id=orders.orders_status ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_ORDERS." set finished = ( select finished from ".TABLE_ORDERS_STATUS." where orders_status.orders_status_id=orders.orders_status ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_ORDERS." set orders_status_name = ( select orders_status_name from ".TABLE_ORDERS_STATUS." where orders_status.orders_status_id=orders.orders_status ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_ORDERS." set orders_status_image = ( select orders_status_image from ".TABLE_ORDERS_STATUS." where orders_status.orders_status_id=orders.orders_status ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_ORDERS_PRODUCTS." set torihiki_date = ( select torihiki_date from ".TABLE_ORDERS." where orders.orders_id=orders_products.orders_id ) where orders_id='".$orders_id."'");
}

function replace_store_name($str) {
  return str_replace('#STORE_NAME#', STORE_NAME, $str);
}
/*
function replace_category_seo($input_str, $category_name) 
{
    global $_GET, $request_type, $breadcrumb;
    $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
    $search  = array('#STORE_NAME#','#BREADCRUMB#', '#SEO_PAGE#', '#CATEGORY_NAME#');
    $replace = array(STORE_NAME,$breadcrumb->trail_title(' &raquo; '), $page .  'ページ目', $category_name);
    return str_replace($search, $replace, $input_str);
}*/

function tep_get_categories_id_by_parent_id($categories_id, $languages_id = 4) {
  $arr = array();
  $categories = tep_get_categories_by_parent_id($categories_id, $languages_id);
  foreach ($categories as $c){
    $arr[] = $c['categories_id'];
  }
  return $arr;
}

function tep_get_ot_total($orders_id)
{
  $ot = tep_db_fetch_array(tep_db_query("
    select * 
    from " . TABLE_ORDERS_TOTAL . " 
    where orders_id='".intval($orders_id)."' 
      and class = 'ot_total'
  "));
  return $ot['text'];
}

function tep_show_quantity($quantity) {
  return $quantity > 0 ? $quantity : 0;
}

function tep_add_rmt($name) {
  if (!strpos($name, 'RMT')){
    return $name . ' RMT';
  }
  return $name;
}

function check_uri($p) {
  //print_r($_SERVER['REQUEST_URI']);
  if (preg_match($p, $_SERVER['REQUEST_URI'])) {
    forward404();
  }
}

function tep_get_domain($url)
{
  if (preg_match('/https?:\/\/([^\/?]*)/', $url, $out)) {
    return $out[1];
  } else {
    return false;
  }
}

//LAST_CUSTOMER_ACTION
function last_customer_action() {
  tep_db_query("update ".TABLE_CONFIGURATION." set configuration_value=now() where configuration_key='LAST_CUSTOMER_ACTION'");
}

function SBC2DBC($str) {
  $arr = array(
    'Ａ','Ｂ','Ｃ','Ｄ','Ｅ','Ｆ','Ｇ','Ｈ','Ｉ','Ｊ','Ｋ','Ｌ','Ｍ','Ｎ','Ｏ','Ｐ','Ｑ','Ｒ','Ｓ','Ｔ','Ｕ','Ｖ','Ｗ','Ｘ','Ｙ','Ｚ',
    'ａ','ｂ','ｃ','ｄ','ｅ','ｆ','ｇ','ｈ','ｉ','ｊ','ｋ','ｌ','ｍ','ｎ','ｏ','ｐ','ｑ','ｒ','ｓ','ｔ','ｕ','ｖ','ｗ','ｘ','ｙ','ｚ',
    '｀','～','！','＠','＃','＄','％','＾','＆','＊','（','）','＿','＋','＝','－','［','］','｛','｝','＼','＇','；','：','＂','｜','？','＞','＜','，','．','／',
    '１','２','３','４','５','６','７','８','９','０',
    '　'
  );
  $arr2 = array(
    'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
    'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
    '`','~','!','@','#','$','%','^','&','*','(',')','_','+','=','-','[',']','{','}','\\',"'",';',':','"','|','?','>','<',',','.','/',
    '1','2','3','4','5','6','7','8','9','0',
    ' '
  );
  return str_replace($arr, $arr2, $str);
}

function tep_parseURI()
{
  //如果是https的链接不解析{
  $tmpArr = parse_url(HTTPS_SERVER);
  $tmpHttphost = $tmpArr['host'];
  unset($tmpArr);
  if($tmpHttphost == $_SERVER['HTTP_HOST']){
    return true;
  }
  //}
  if(substr($_SERVER['HTTP_HOST'],0,3)=='www'){
    return true;
  }
  $subSiteUri = $_SERVER['REQUEST_URI'];
  $g_pos = strpos($_SERVER['REQUEST_URI'], '?'); 
  if ($g_pos !== false) {
    $subSiteUri = substr($_SERVER['REQUEST_URI'], 0, $g_pos);
  }
  $router = 'x';
  $rewriteRule = array(
                       "firstFolder"  => "/^\/[a-zA-Z0-9\-]+\/?$/",        //   /abc(/)
                       "secondFolder" => '/^\/[a-zA-Z0-9\-]+\/[a-zA-Z0-9\-]+\/?$/',              //   /asb/xcv(/)
                       "thirdFolder"  => '/^\/[a-zA-Z0-9\-]+\/[a-zA-Z0-9\-]+\/[a-zA-Z0-9\-]+\/?$/',              //   /asb/xcv(/)
                       "product"      => '/\.html$/'                    //   /asd/xcv/xcv.html  /zxv.html /xcv/xcv/xc.html
                       );
  foreach ($rewriteRule as $ruler=>$value){
    //if (preg_match($value, $rewriteRule)) {
    if (preg_match($value, $subSiteUri)) {
      $router = $ruler;
    }
  }
  $i_pos = strpos($_SERVER['REQUEST_URI'], '/?cmd=');
  if ($i_pos !== false) {
    $router = 'x'; 
  }
  if(isset($_GET['cName'])){
  $firstId = tep_get_cpath_by_cname($_GET['cName']);
  $_GET['cPath'] = $firstId;
  }
  switch($router){
  case 'firstFolder':
    $firstFolder = substr($subSiteUri,1);
    if(substr($firstFolder,-1)=='/'){
      $firstFolder = substr($firstFolder,0,-1);
    }
    $secondId = tep_get_cpath_by_cname($firstFolder);
    if ($secondId == 0) {
      forward404();
    }
    $_GET['cPath'] = join('_',array($firstId,$secondId));
    break;
  case 'secondFolder':
    $secondFolder = substr($subSiteUri,1);
    $folder_arr = explode('/', $secondFolder); 
    $secondId = tep_get_cpath_by_cname($folder_arr[0]);
    $thirdId  = tep_get_cpath_by_cname($folder_arr[1]); 
    if ($thirdId == 0) {
      forward404();
    }
    $_GET['cPath'] = join('_',array($firstId,$secondId,$thirdId));
    $break;
  case 'product':
    $tmpArray = explode('/',$subSiteUri);
    $pid = $tmpArray[count($tmpArray)-1];
    $pid = substr($pid,0,-5);
    $_GET['products_id'] = $pid;

  }

    /*
if (isset($_GET['cName'])){

  $_GET['cPath'] = tep_get_cpath_by_cname($_GET['cName']);
  if($_GET['cPath']==false){//如果没有 则404
    
  }
}
    */


}

function tep_get_romaji_cpath($cpath)
{
    global $languages_id;

    if (empty($language)) $language = $languages_id;
    //ccdd
    $queryString = "
        select `romaji` 
        from " .  TABLE_CATEGORIES_DESCRIPTION . "
        where categories_id = '" . (int)$cpath .  "' 
          and language_id = '" . (int)$language . "' 
          and (site_id = '".SITE_ID."' or site_id = '0')
        order by site_id DESC"
    ;
    $category_query = tep_db_query($queryString);
    $category = tep_db_fetch_array($category_query);
    return $category['romaji'];

}
function tep_get_cpath_by_cname($cname)
{
    global $languages_id;
    if (empty($language)){
      $language = $languages_id;
    }
    //ccdd
    $queryString = "
        select `categories_id` 
        from " .  TABLE_CATEGORIES_DESCRIPTION . "
        where romaji = '" . $cname .  "' 
          and language_id = '" . (int)$language . "' 
          and (site_id = '".SITE_ID."' or site_id = '0')
        order by site_id DESC" ;
    $category_query = tep_db_query($queryString);
    $category = tep_db_fetch_array($category_query);
    return $category['categories_id'];

}
function tep_get_categories_by_pid($pid,$romaji=true)
{
  static $romaji_arr = array();
  $arr = array();
  $p_parent = tep_get_categories_by_products_id($pid);
  if (!$p_parent) {
    //exit('no categories');
  }
  //如果同一商品属于多个分类默认返回第一个 
  $categories[] = $p_parent[0];
  tep_get_parent_categories($categories, $p_parent[0]);
  if ($romaji) {
    $query = false;
    foreach($categories as $k => $v){
      if (!array_key_exists($v,$romaji_arr)) {
        $query = true || $query;
      }
    }
    if ($query) {
      $rquery = tep_db_query("
          select * from (
            select categories_id,romaji,site_id
            from ".TABLE_CATEGORIES_DESCRIPTION." 
            where categories_id in ('".implode("','", $categories)."') 
            order by site_id desc
          ) c
          where site_id='".SITE_ID."' or site_id='0'
          group by categories_id
      ");
      while($c = tep_db_fetch_array($rquery)){
        $romaji_arr[$c['categories_id']] = $c['romaji'];
      }
    }
    foreach($categories as $k => $v){
      $categories[$k] = $romaji_arr[$v];
    }
  }
  return array_reverse($categories);
}
// 根据产品id返回分类id
function tep_get_categories_by_products_id($pid){
  $carr = array();
  $query = tep_db_query("select * from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
  while($p2c = tep_db_fetch_array($query)){
    $carr[] = $p2c['categories_id'];
  }
  return $carr;
}
//返回该产品的romaji
function tep_get_romaji_by_pid($id)
{
  return $id;
}

function tep_get_products_rate($pid) {
  $p =  tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." where products_id='".$pid."'"));
  $t = explode('//',$p['products_attention_1']);
  $n = str_replace(',','',tep_get_full_count_in_order(1, $t[1]));
  preg_match_all('/(\d+)/',$n,$out);
  //print_r($out);
  return $out[1][0];
}
function tep_get_google_adsense_adurl($url) {
  $arr = parse_url($url);
  $q_arr = array();
  if ($arr['query']) {
    $queries = explode("&",$arr['query']);
    foreach($queries as $q) {
      $tmp = explode('=',$q);
      $q_arr[$tmp[0]] = $tmp[1];
    }
    if ($q_arr['sa'] && $q_arr['ai'] && $q_arr['adurl']) {
      return $q_arr['adurl'];
    }
    return false;
  } else {
    return false;
  } 
}
