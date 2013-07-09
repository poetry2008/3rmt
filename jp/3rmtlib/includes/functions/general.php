<?php
/*
  $Id$

  global functions
  
  required by application_top.php
*/

/* -------------------------------------
    功能: session关闭 
    参数: 无   
    返回值: 无 
------------------------------------ */
  function tep_exit() {
   tep_session_close();
   exit();
  }

/* -------------------------------------
    功能: 页面跳转 
    参数: $url(string) url地址  
    参数: $suc(string) 成功标识 
    返回值: 无 
------------------------------------ */
  function tep_redirect($url,$suc='') {

    header('Location: ' . $url);
  
  if($suc == 'T'){
    echo 'SuccessOK';
    }
  
    tep_exit();
  }

/* -------------------------------------
    功能: 页面404 
    参数: 无   
    返回值: 无 
------------------------------------ */
function forward404()
{ 
  header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
  require(DIR_WS_MODULES  . '404.html');
  exit;
}

/* -------------------------------------
    功能: 页面404 
    参数: $condition(string) 条件
    返回值: 无 
------------------------------------ */
function forward404If($condition)
{
  if ($condition)
  {
    forward404();
  }
}

/* -------------------------------------
    功能: 页面404 
    参数: $condition(string) 条件
    返回值: 无 
------------------------------------ */
function forward404Unless($condition)
{
  if (!$condition)
  {
    forward404();
  }
}

/* -------------------------------------
    功能: 替换文字 
    参数: $data(string) 数据 
    参数: $parse(array) 替换的内容 例：array('替换前的内容' => ‘替换后的内容’) 
    返回值: 替换后的文字(string) 
------------------------------------ */
  function tep_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
  }

/* -------------------------------------
    功能: 输出文字 
    参数: $string(string) 文字 
    参数: $translate(boolean) 是否转义 
    参数: $protected(boolean) 是否转义输出  
    返回值: 替换后的文字(string) 
------------------------------------ */
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

/* -------------------------------------
    功能: 输出文字 
    参数: $string(string) 文字 
    返回值: 处理后的文字(string) 
------------------------------------ */
  function tep_output_string_protected($string) {
    return tep_output_string($string, false, true);
  }

/* -------------------------------------
    功能: 把文字里的 +换成空格 
    参数: $string(string) 文字 
    返回值: 替换后的字符串(string) 
------------------------------------ */
  function tep_sanitize_string($string) {
    $string = ereg_replace(' +', ' ', trim($string));
    return $string;
  }

/* -------------------------------------
    功能: 输出错误信息 
    参数: $error_message(string) 错误文字 
    参数: $close_application(boolean) 是否关闭应用 
    参数: $close_application_error(string) 关闭应用的文字 
    返回值: 无 
------------------------------------ */
  function tep_error_message($error_message, $close_application = false, $close_application_error = '') {
    echo $error_message;

    if ($close_application == true) {
      die($close_application_error);
    }
  }

/* -------------------------------------
    功能: 按照指定的sql随机取一条数据 
    参数: $query(string) sql语句
    返回值: 随机数据的信息(array) 
------------------------------------ */
  function tep_random_select($query) {
    $random_product = '';
    $random_query = tep_db_query($query);
    $num_rows = tep_db_num_rows($random_query);
    if ($num_rows > 0) {
      $random_row = tep_rand(0, ($num_rows - 1));
      tep_db_data_seek($random_query, $random_row);
      $random_product = tep_db_fetch_array($random_query);
    }

    return $random_product;
  }

/* -------------------------------------
    功能: 获得商品的名字 
    参数: $product_id(int) 商品id 
    参数: $language(string) 语言id
    返回值: 商品的名字(string) 
------------------------------------ */
  function tep_get_products_name($product_id, $language = '') {
    global $languages_id;

    if (empty($language)) $language = $languages_id;
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

/* -------------------------------------
    功能: 获得商品的描述 
    参数: $product_id(int) 商品id 
    参数: $language(string) 语言id
    返回值: 商品的描述(string) 
------------------------------------ */
  function tep_get_products_description($product_id, $language = '') {
    global $languages_id;

    if (empty($language)) $language = $languages_id;
    $product_query = tep_db_query("
        select products_description 
        from " .  TABLE_PRODUCTS_DESCRIPTION . " 
        where products_id = '" . (int)$product_id .  "' 
          and language_id = '" . (int)$language . "' 
          and (site_id = '".SITE_ID."' or site_id = '0')
        order by site_id DESC"
    );
    $product = tep_db_fetch_array($product_query);

    return replace_store_name($product['products_description']);
  }

/* -------------------------------------
    功能: 获得商品的描述(去掉html并且替换STORE_NAME) 
    参数: $products_id(int) 商品id 
    返回值: 商品的描述(string) 
------------------------------------ */
  function ds_tep_get_description($products_id) {
    global $languages_id;
    $description_query = tep_db_query("
        select products_description 
        from ".TABLE_PRODUCTS_DESCRIPTION." 
        where products_id = '".$products_id."' 
          and language_id = '".$languages_id."' 
          and (site_id = '".SITE_ID."' or site_id = '0')
        order by site_id DESC"
        );
    $description = tep_db_fetch_array($description_query);
    return strip_tags(replace_store_name($description['products_description'])) ;
  }

/* -------------------------------------
    功能: 获得商品的库存数 
    参数: $products_id(int) 商品id 
    返回值: 商品的库存数(int) 
------------------------------------ */
  function tep_get_products_stock($products_id) {
    $stock_query = tep_db_query("
    select * from (
      select p.products_real_quantity + p.products_virtual_quantity as products_quantity, pd.products_status, p.products_id, pd.site_id 
      from " . TABLE_PRODUCTS . " p , ".TABLE_PRODUCTS_DESCRIPTION." pd 
      where p.products_id = pd.products_id and p.products_id = '" . (int)$products_id . "' 
      order by pd.site_id DESC
    ) c where site_id = ".SITE_ID." or site_id = 0 group by products_id
    ");
    $stock_values = tep_db_fetch_array($stock_query);

    return ($stock_values['products_status'] == '1') ? $stock_values['products_quantity'] : 0;
  }

/* -------------------------------------
    功能: 检测商品的库存是否充足 
    参数: $products_id(int) 商品id 
    参数: $products_quantity(int) 商品数量 
    参数: $link_single(boolean) 是否显示链接  
    返回值: 检测商品库存不足的信息(string) 
------------------------------------ */
  function tep_check_stock($products_id, $products_quantity, $link_single = false) {
    $stock_left = tep_get_quantity($products_id,true) - $products_quantity;
    $out_of_stock = '';
    $product = tep_get_product_by_id($products_id, SITE_ID, 4);

    if ($stock_left < 0) {
      $product = tep_get_product_by_id($products_id, SITE_ID, 4,true,'product_info');
      if ($link_single) {
        $out_of_stock = '<span class="markProductOutOfStock" style="color:#cc0033">'.STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
      } else {
        $out_of_stock = '<span class="markProductOutOfStock"><a style="color:#CC0033" href="'.tep_href_link('open.php', 'products='.urlencode($product['products_name'])).'">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</a></span>';
      }
    }

    return $out_of_stock;
  }


/* -------------------------------------
    功能: 输出字符串 
    参数: $string(string) 字符串 
    参数: $len(int) 长度 
    参数: $break_char(string) 换行字符 
    返回值: 处理后的字符串(string) 
------------------------------------ */
  function tep_break_string($string, $len, $break_char = '-') {
    return $string;
  }

/* -------------------------------------
    功能: 获得get所得数据 
    参数: $exclude_array(array) 不包括参数的数组 
    返回值: 获得的参数以及值(string) 
------------------------------------ */
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

/* -------------------------------------
    功能: 获得国家信息 
    参数: $countries_id(int) 国家id 
    参数: $with_iso_codes(boolean) 是否显示iso_code 
    返回值: 获得相关信息(array) 
------------------------------------ */
  function tep_get_countries($countries_id = '', $with_iso_codes = false) {
    $countries_array = array();
    if (tep_not_null($countries_id)) {
      if ($with_iso_codes == true) {
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
        $countries = tep_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "'");
        $countries_values = tep_db_fetch_array($countries);
        $countries_array = array('countries_name' => $countries_values['countries_name']);
      }
    } else {
      $countries = tep_db_query("select countries_id, countries_name from " . TABLE_COUNTRIES . " order by countries_name");
      while ($countries_values = tep_db_fetch_array($countries)) {
        $countries_array[] = array('countries_id' => $countries_values['countries_id'],
                                   'countries_name' => $countries_values['countries_name']);
      }
    }

    return $countries_array;
  }

/* -------------------------------------
    功能: 获得国家信息 
    参数: $countries_id(int) 国家id 
    返回值: 获得带iso的相关信息(array) 
------------------------------------ */
  function tep_get_countries_with_iso_codes($countries_id) {
    return tep_get_countries($countries_id, true);
  }

/* -------------------------------------
    功能: 根据当前分类id寻找其父结点的路径 
    参数: $current_category_id(int) 分类id 
    返回值: 获得分类路径(string) 
------------------------------------ */
  function tep_get_path($current_category_id = '') {
    global $cPath_array;

    if (tep_not_null($current_category_id)) {
      $cp_size = sizeof($cPath_array);
      if ($cp_size == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
        $last_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$cPath_array[($cp_size-1)] . "'");
        $last_category = tep_db_fetch_array($last_category_query);

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

/* -------------------------------------
    功能: 检测浏览器类型 
    参数: $component(string) 浏览器类型 
    返回值: 浏览器信息(string) 
------------------------------------ */
  function tep_browser_detect($component) {
    global $HTTP_USER_AGENT;

    return stristr($HTTP_USER_AGENT, $component);
  }

/* -------------------------------------
    功能: 获得国家的名字 
    参数: $country_id(int) 国家id 
    返回值: 国家的名字(string) 
------------------------------------ */
  function tep_get_country_name($country_id) {
    $country_array = tep_get_countries($country_id);

    if (!isset($country_array['countries_name'])) $country_array['countries_name'] = NULL;
    return $country_array['countries_name'];
  }

/* -------------------------------------
    功能: 获得区域的名字 
    参数: $country_id(int) 国家id 
    参数: $zone_id(int) 区域id
    参数: $default_zone(string) 默认区域
    返回值: 区域的名字(string) 
------------------------------------ */
  function tep_get_zone_name($country_id, $zone_id, $default_zone) {
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

/* -------------------------------------
    功能: 获得区域的编码 
    参数: $country_id(int) 国家id 
    参数: $zone_id(int) 区域id
    参数: $default_zone(string) 默认区域
    返回值: 区域的编码(string) 
------------------------------------ */
  function tep_get_zone_code($country_id, $zone_id, $default_zone) {
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

/* -------------------------------------
    功能: 对浮点数进行四舍五入 
    参数: $value(float) 数值 
    参数: $precision(int) 小数点后的位数
    返回值: 处理后的数值(float) 
------------------------------------ */
  function tep_round($value, $precision) {
    if (PHP_VERSION < 4) {
      $exp = pow(10, $precision);
      return round($value * $exp) / $exp;
    } else {
      return round($value, $precision);
    }
  }

/* -------------------------------------
    功能: 获得稅率 
    参数: $class_id(int) 稅率类id 
    参数: $country_id(int) 国家id
    参数: $zone_id(int) 区域id
    返回值: 税率(int) 
------------------------------------ */
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

/* -------------------------------------
    功能: 获得稅率的描述 
    参数: $class_id(int) 稅率类id 
    参数: $country_id(int) 国家id
    参数: $zone_id(int) 区域id
    返回值: 税率的描述(string) 
------------------------------------ */
  function tep_get_tax_description($class_id, $country_id, $zone_id) {
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

/* -------------------------------------
    功能: 获得商品税率后的价格 
    参数: $price(float) 商品的价格 
    参数: $tax(float) 税率
    返回值: 税后价格(float) 
------------------------------------ */
  function tep_add_tax($price, $tax) {
    global $currencies;

    if ( (DISPLAY_PRICE_WITH_TAX == 'true') && ($tax > 0) ) {
      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + tep_calculate_tax($price, $tax);
    } else {
      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
    }
  }

/* -------------------------------------
    功能: 获得商品税率值 
    参数: $price(float) 商品的价格 
    参数: $tax(float) 税率
    返回值: 税率值(float) 
------------------------------------ */
  function tep_calculate_tax($price, $tax) {
    global $currencies;
    return $currencies->round_off($price * $tax / 100);
  }

/* -------------------------------------
    功能: 获得该分类下商品的个数 
    参数: $category_id(int) 分类id 
    参数: $include_inactive(boolean) 是否包含不显示的商品 
    返回值: 商品个数(int) 
------------------------------------ */
  function tep_count_products_in_category($category_id, $include_inactive = false) {
    $products_count = 0;
    if ($include_inactive == true) {
      $products_query = tep_db_query("
          select count(*) as total 
          from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
          where p.products_id = p2c.products_id 
            and p2c.categories_id = '" . (int)$category_id . "'
      ");
    } else {
      $products_query = tep_db_query("select count(*) as total from " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, ".TABLE_PRODUCTS_DESCRIPTION." pd where p.products_id = pd.products_id and p.products_id = p2c.products_id and pd.products_status != '0' and pd.products_status != '3' and p2c.categories_id = '" . (int)$category_id . "'");
    }
    $products = tep_db_fetch_array($products_query);
    $products_count += $products['total'];
    $child_categories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$category_id . "'");
    if (tep_db_num_rows($child_categories_query)) {
      while ($child_categories = tep_db_fetch_array($child_categories_query)) {
        $products_count += tep_count_products_in_category($child_categories['categories_id'], $include_inactive);
      }
    }

    return $products_count;
  }

/* -------------------------------------
    功能: 判断该分类下是否有子分类 
    参数: $category_id(int) 分类id 
    返回值: 是否有子分类(boolean) 
------------------------------------ */
  function tep_has_category_subcategories($category_id) {
    $child_category_query = tep_db_query("select count(*) as count from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$category_id . "'");
    $child_category = tep_db_fetch_array($child_category_query);

    if ($child_category['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }

/* -------------------------------------
    功能: 获得地址规格编号 
    参数: $country_id(int) 国家id 
    返回值: 地址规格编号(string) 
------------------------------------ */
  function tep_get_address_format_id($country_id) {
    $address_format_query = tep_db_query("select address_format_id as format_id from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");
    if (tep_db_num_rows($address_format_query)) {
      $address_format = tep_db_fetch_array($address_format_query);
      return $address_format['format_id'];
    } else {
      return '6';
    }
  }

/* -------------------------------------
    功能: 获得住址规格 
    参数: $address_format_id(int) 住址规格id 
    参数: $address(array) 住址的信息 
    参数: $html(boolean) 是否是html显示 
    参数: $boln(string) 开始符号 
    参数: $eoln(string) 结束符号 
    返回值: 住址规格(string) 
------------------------------------ */
  function tep_address_format($address_format_id, $address, $html, $boln, $eoln) {
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
    $statename = tep_get_zone_name($country_id,$zone_id,$state);
    $telephone = tep_output_string_protected($address['telephone']);

    if ($html) {
    // HTML Mode
      $HR = '<hr>';
      $hr = '<hr>';
      if ( ($boln == '') && ($eoln == "\n") ) { 
      // Values not specified, use rational defaults
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

/* -------------------------------------
    功能: 获得该顾客的住址规格
    参数: $customers_id(int) 顾客id 
    参数: $address_id(int) 住址id 
    参数: $html(boolean) 是否是html显示 
    参数: $boln(string) 开始符号 
    参数: $eoln(string) 结束符号 
    返回值: 住址规格(string) 
------------------------------------ */
  function tep_address_label($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
    $address_query = tep_db_query("select entry_firstname as firstname, entry_lastname as lastname, entry_firstname_f as firstname_f, entry_lastname_f as lastname_f, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id, entry_telephone as telephone from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customers_id . "' and address_book_id = '" . (int)$address_id . "'");
    $address = tep_db_fetch_array($address_query);

    $format_id = tep_get_address_format_id($address['country_id']);

    return tep_address_format($format_id, $address, $html, $boln, $eoln);
  }

/* -------------------------------------
    功能: 获得该顾客的住址简要
    参数: $customers_id(int) 顾客id 
    参数: $address_id(int) 住址id 
    返回值: 住址简要(string) 
------------------------------------ */
  function tep_address_summary($customers_id, $address_id) {
    $customers_id = tep_db_prepare_input($customers_id);
    $address_id = tep_db_prepare_input($address_id);
    $address_query = tep_db_query("select ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_state, ab.entry_country_id, ab.entry_zone_id, c.countries_name, c.address_format_id from " . TABLE_ADDRESS_BOOK . " ab, " . TABLE_COUNTRIES . " c where ab.address_book_id = '" . tep_db_input($address_id) . "' and ab.customers_id = '" . tep_db_input($customers_id) . "' and ab.entry_country_id = c.countries_id");
    $address = tep_db_fetch_array($address_query);

    $street_address = tep_output_string_protected($address['entry_street_address']);
    $suburb = tep_output_string_protected($address['entry_suburb']);
    $postcode = tep_output_string_protected($address['entry_postcode']);
    $city = tep_output_string_protected($address['entry_city']);
    $state = tep_get_zone_code($address['entry_country_id'], $address['entry_zone_id'], $address['entry_state']);
    $country = tep_output_string_protected($address['countries_name']);

    $address_format_query = tep_db_query("select address_summary from " . TABLE_ADDRESS_FORMAT . " where address_format_id = '" . (int)$address['address_format_id'] . "'");
    $address_format = tep_db_fetch_array($address_format_query);
    $statename = tep_get_zone_name($address['entry_country_id'], $address['entry_zone_id'],'');

    $address_summary = $address_format['address_summary'];
    eval("\$address = \"$address_summary\";");

    return $address;
  }

/* -------------------------------------
    功能: 格式化输出数字
    参数: $number(int) 数字 
    返回值: 格式化数字(string) 
------------------------------------ */
  function tep_row_number_format($number) {
    if ( ($number < 10) && (substr($number, 0, 1) != '0') ) $number = '0' . $number;

    return $number;
  }

/* -------------------------------------
    功能: 获得该分类相关的分类信息
    参数: $categories_array(array) 分类信息的数组 
    参数: $parent_id(int) 父结点
    参数: $indent(string) 缩进的标识
    返回值: 分类信息(array) 
------------------------------------ */
  function tep_get_categories($categories_array = '', $parent_id = '0', $indent = '') {
    global $languages_id;

    $parent_id = tep_db_prepare_input($parent_id);

    if (!is_array($categories_array)) $categories_array = array();
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

/* -------------------------------------
    功能: 获得生产商的相关信息
    参数: $manufacturers_array(array) 生产商信息的数组 
    返回值: 生产商的相关信息(array) 
------------------------------------ */
  function tep_get_manufacturers($manufacturers_array = '') {
    if (!is_array($manufacturers_array)) $manufacturers_array = array();
    $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
      $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);
    }

    return $manufacturers_array;
  }

/* -------------------------------------
    功能: 获得该分类的子分类
    参数: $subcategories_array(array) 子分类信息的数组 
    参数: $parent_id(int) 父节点 
    返回值: 子分类(array) 
------------------------------------ */
  function tep_get_subcategories(&$subcategories_array, $parent_id = 0) {
    $subcategories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$parent_id . "'");
    while ($subcategories = tep_db_fetch_array($subcategories_query)) {
      $subcategories_array[sizeof($subcategories_array)] = $subcategories['categories_id'];
      if ($subcategories['categories_id'] != $parent_id) {
        tep_get_subcategories($subcategories_array, $subcategories['categories_id']);
      }
    }
  }

/* -------------------------------------
    功能: 格式化输出时间
    参数: $raw_date(string) 时间 
    返回值: 格式化后的时间(string) 
------------------------------------ */
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
    $newarr = array('（月曜日）', '（火曜日）', '（水曜日）', '（木曜日）', '（金曜日）', '（土曜日）', '（日曜日）');
    return str_replace($oarr, $newarr, $returntime);
  }

/* -------------------------------------
    功能: 简要的格式化输出时间
    参数: $raw_date(string) 时间 
    返回值: 格式化后的时间(string) 
------------------------------------ */
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

/* -------------------------------------
    功能: 分割字符串
    参数: $search_str(string) 字符串 
    参数: $objects(object) 分割后的对象 
    返回值: 分割是否成功(boolean) 
------------------------------------ */
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

/* -------------------------------------
    功能: 判断日期是否按照指定格式输出
    参数: $date_to_check(string) 要检查的日期 
    参数: $format_string(string) 检查的格式 
    参数: $date_array(array) 日期的数组 
    返回值: 是否按照格式输出(boolean) 
------------------------------------ */
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

/* -------------------------------------
    功能: 判断是否是闰年
    参数: $year(int) 年数 
    返回值: 是否是闰年(boolean) 
------------------------------------ */
  function tep_is_leap_year($year) {
    if ($year % 100 == 0) {
      if ($year % 400 == 0) return true;
    } else {
      if (($year % 4) == 0) return true;
    }

    return false;
  }

/* -------------------------------------
    功能: 排序的标题头
    参数: $sortby(int) 排序的代号 
    参数: $colnum(string) 列 
    参数: $heading(string) 头部信息 
    返回值: 标题头(string) 
------------------------------------ */
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

/* -------------------------------------
    功能: 获得其分类的父分类
    参数: $categories(array) 分类的信息 
    参数: $categories_id(int) 分类id 
    返回值: 父分类(array) 
------------------------------------ */
  function tep_get_parent_categories(&$categories, $categories_id) {
    $parent_categories_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$categories_id . "'");
    while ($parent_categories = tep_db_fetch_array($parent_categories_query)) {
      if ($parent_categories['parent_id'] == 0) return true;
      $categories[sizeof($categories)] = $parent_categories['parent_id'];
      if ($parent_categories['parent_id'] != $categories_id) {
        tep_get_parent_categories($categories, $parent_categories['parent_id']);
      }
    }
  }

/* -------------------------------------
    功能: 获得该商品所在的关联分类
    参数: $products_id(int) 商品id 
    返回值: 关联分类(string) 
------------------------------------ */
  function tep_get_product_path($products_id) {
    $cPath = '';
    $cat_count_sql = tep_db_query("select count(*) as count from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$products_id . "'");
    $cat_count_data = tep_db_fetch_array($cat_count_sql);

    if ($cat_count_data['count'] == 1) {
      $categories = array();
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

/* -------------------------------------
    功能: 获得商品的prid
    参数: $prid(int) 商品id 
    参数: $params(array) 相关参数 
    返回值: 重新生成的prid(string) 
------------------------------------ */
  function tep_get_uprid($prid, $params) {
    $uprid = $prid;
    if ( (is_array($params)) && (!strstr($prid, '{')) ) {
      while (list($option, $value) = each($params)) {
        $uprid = $uprid . '{' . $option . '}' . $value;
      }
    }

    return $uprid;
  }

/* -------------------------------------
    功能: 获得商品id
    参数: $uprid(int) 商品id的信息 
    返回值: 过滤后的商品id(string) 
------------------------------------ */
  function tep_get_prid($uprid) {
    $pieces = split('[{]', $uprid, 2);

    return $pieces[0];
  }

/* -------------------------------------
    功能: 输出顾客的欢迎语
    参数: 无 
    返回值: 欢迎语(string) 
------------------------------------ */
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

/* -------------------------------------
    功能: 发送邮件
    参数: $to_name(string) 收信人的名字
    参数: $to_email_address(string) 收信人的邮箱 
    参数: $email_subject(string) 邮件标题 
    参数: $email_text(string) 邮件内容 
    参数: $from_email_name(string) 寄信人的名字 
    参数: $from_email_address(string) 寄信人的邮箱 
    返回值: 无 
------------------------------------ */

  function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {
    if (SEND_EMAILS != 'true') return false;
    // Instantiate a new mail object
    $message = new email(array('X-Mailer: iimy Mailer'));

    // Build the text version
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

/* -------------------------------------
    功能: 判断该商品是否有属性
    参数: $products_id(int) 商品id 
    返回值: 商品是否有属性(boolean) 
------------------------------------ */
  function tep_has_product_attributes($products_id) {
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

/* -------------------------------------
    功能: 获得字符个数
    参数: $string(string) 字符串 
    参数: $needle(string) 分割的标识 
    返回值: 字符个数(int) 
------------------------------------ */
  function tep_word_count($string, $needle) {
    global $language;
    if ($language == 'japanese') {
        return mb_strlen($string);
    }

    $temp_array = split($needle, $string);
    return sizeof($temp_array);
  }

/* -------------------------------------
    功能: 获得模块个数
    参数: $modules(string) 模块的信息 
    返回值: 模块个数(int) 
------------------------------------ */
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
/* -------------------------------------
    功能: 获得配送模块个数
    参数: 无 
    返回值: 配送模块个数(int) 
------------------------------------ */
  function tep_count_shipping_modules() {
    return tep_count_modules(MODULE_SHIPPING_INSTALLED);
  }

/* -------------------------------------
    功能: 获得随机数
    参数: $length(int) 随机数的长度 
    参数: $type(string) 类型 
    返回值: 随机数(int) 
------------------------------------ */
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
        //字母和数字
        if (eregi('^[a-z0-9]$', $char)) $rand_value .= $char;
      } elseif ($type == 'chars') {
        //字母
        if (eregi('^[a-z]$', $char)) $rand_value .= $char;
      } elseif ($type == 'digits') {
        //数字
        if (ereg('^[0-9]$', $char)) $rand_value .= $char;
      }
    }

    return $rand_value;
  }

/* -------------------------------------
    功能: 格式化警告信息
    参数: $warning(string) 警告语 
    返回值: 警告信息(string) 
------------------------------------ */
  function tep_output_warning($warning) {
    new errorBox(array(array('text' => tep_image(DIR_WS_ICONS . 'warning.gif', ICON_WARNING) . ' ' . $warning)));
  }

/* -------------------------------------
    功能: 把数组格式化成字符串
    参数: $array(array) 格式化数组 
    参数: $exclude(string) 排除的字符串
    参数: $equals(string) 等于的符号
    参数: $separator(string) 分割符 
    返回值: 格式化后的字符串(string) 
------------------------------------ */
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

/* -------------------------------------
    功能: 判断输入是否为空
    参数: $value(string) 字符串 
    返回值: 值是否为空(boolean) 
------------------------------------ */
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

/* -------------------------------------
    功能: 输出税后的值
    参数: $value(string) 字符串
    参数: $padding(string) 分割符 
    返回值: 税后的值(string) 
------------------------------------ */
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

/* -------------------------------------
    功能: 判断该货币是否存在
    参数: $code(int) 货币代号
    返回值: 货币是否存在(boolean) 
------------------------------------ */
  function tep_currency_exists($code) {
    $currency_code = tep_db_query("select currencies_id from " . TABLE_CURRENCIES . " where code = '" . tep_db_input($code) . "'");
    if (tep_db_num_rows($currency_code)) {
      return $code;
    } else {
      return false;
    }
  }

/* -------------------------------------
    功能: 把字符串转换成整数 
    参数: $string(string) 字符串 
    返回值: 转换后的整数(int) 
------------------------------------ */
  function tep_string_to_int($string) {
    return (int)$string;
  }

/* -------------------------------------
    功能: 分割分类的字符串
    参数: $cPath(string) 分类信息
    返回值: 分割后的数组(array) 
------------------------------------ */
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

/* -------------------------------------
    功能: 数字生成的随机数
    参数: $min(int) 最小值
    参数: $max(int) 最大值 
    返回值: 随机数(int) 
------------------------------------ */
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

/* -------------------------------------
    功能: 把字符串全角转为半角
    参数: $string(string) 字符串
    返回值: 转换后的字符串(string) 
------------------------------------ */
  function tep_an_zen_to_han($string) {
    return mb_convert_kana($string, "a");
  }

/* -------------------------------------
    功能: 获得名字的全名
    参数: $firstname(string) 名
    参数: $lastname(string) 姓 
    返回值: 全名(string) 
------------------------------------ */
  function tep_get_fullname($firstname, $lastname) {
    global $language;
    $separator = ' ';
    if ($language == 'japanese') {
        return $lastname.$separator.$firstname;
    } else {
        return $firstname.$separator.$lastname;
    }
  }

/* -------------------------------------
    功能: 获得该生产商所拥有的商品个数
    参数: $manufacturers_id(int) 生产商id 
    返回值: 所拥有的商品个数(int) 
------------------------------------ */
  function ds_tep_get_count_manufactures($manufacturers_id) {
    $manufactures_query = tep_db_query("select count(*) as total from ".TABLE_PRODUCTS." where manufacturers_id = '".$manufacturers_id."'");
    $manufactures = tep_db_fetch_array($manufactures_query);
    
  return $manufactures['total'];
  }
   
/* -------------------------------------
    功能: 获得生产商的相关信息
    参数: $manufacturers_id(int) 生产商id 
    参数: $return(int) 返回类型 
    返回值: 生产商的相关信息(string) 
------------------------------------ */
  function ds_tep_get_manufactures($manufacturers_id, $return) {
  
  if($return == 1) {
    //返回厂商名
    $manufactures_query = tep_db_query("select manufacturers_name from ".TABLE_MANUFACTURERS." where manufacturers_id = '".$manufacturers_id."'");
    $manufactures = tep_db_fetch_array($manufactures_query);
    
    $mreturn = $manufactures['manufacturers_name'];
  } elseif($return == 2) {
    //厂商图像
    $manufactures_query = tep_db_query("select manufacturers_image from ".TABLE_MANUFACTURERS." where manufacturers_id = '".$manufacturers_id."'");
    $manufactures = tep_db_fetch_array($manufactures_query);
    
    $mreturn = $manufactures['manufacturers_image'];
  }
  
  return $mreturn;
  }
  
/* -------------------------------------
    功能: 转换字符串编码(UTF-8->EUC-JP)
    参数: $string(string) 字符串 
    返回值: 转换后的字符串(string) 
------------------------------------ */
  function ds_convert_Ajax($string) {
    return mb_convert_encoding($string,'UTF-8','EUC-JP');
  }

/* -------------------------------------
    功能: 获得商品数量的乘积
    参数: $cnt(int) 乘积值 
    参数: $pid(int) 商品id 
    返回值: 商品数量的乘积(string) 
------------------------------------ */
  function tep_get_full_count2($cnt, $pid){
    $radices = tep_get_radices($pid);
    if($radices!=1&&$radices!=0){
    return 
      '('
    . number_format($radices * $cnt) 
    . ')';
    }else{
      return '';
    }
  }
/* -------------------------------------
    功能: 获得商品数量的乘积
    参数: $cnt(int) 乘积值 
    参数: $pid(int) 商品id 
    参数: $flag(boolean) 是否显示乘积值 
    返回值: 商品数量的乘积(string) 
------------------------------------ */
  function tep_get_full_count_in_order2($cnt, $pid,$flag=false){
    $p = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." where products_id='".$pid."'"));
    if($flag){
      return number_format($p['products_attention_1_3']);
    }else{
    return 
    number_format($p['products_attention_1_3'] * $cnt);
    }
  }
  
/* -------------------------------------
    功能: 生成商品的选择时间的下拉列表
    参数: $product_ids(array) 乘积值 
    参数: $select_name(string) 下拉列表的名字 
    返回值: 选择时间的下拉列表(string) 
------------------------------------ */
  function tep_get_torihiki_select_by_products($product_ids = null,$select_name='')
  {
    $torihiki_list = array();
    $torihiki_array = tep_get_torihiki_by_products($product_ids);
    foreach($torihiki_array as $torihiki){
      $torihiki_list[] = array('id' => $torihiki,
        'text' => $torihiki
      );
    }
    if (!isset($torihikihouhou)) $torihikihouhou=NULL;
    if($select_name){
    return tep_draw_pull_down_menu($select_name, $torihiki_list, $torihikihouhou);
    }else{
    return tep_draw_pull_down_menu('torihikihouhou', $torihiki_list, $torihikihouhou);
    }
  }
  
/* -------------------------------------
    功能: 获得商品的选择时间
    参数: $product_ids(array) 乘积值 
    返回值: 选择时间(array/null) 
------------------------------------ */
  function tep_get_torihiki_by_products($product_ids = null)
  {
    $option_types = array();
    if ($product_ids) {
      $sql = "select * from `" . TABLE_PRODUCTS . "` where products_id IN (" . implode(',', $product_ids) . ")";
    
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
  
/* -------------------------------------
    功能: 获得选择时间的相关信息
    参数: 无 
    返回值: 选择时间的相关信息(array) 
------------------------------------ */
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
  
/* -------------------------------------
    功能: 获得选择时间的详细信息
    参数: 无 
    返回值: 选择时间的详细信息(array) 
------------------------------------ */
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
  
/* -------------------------------------
    功能: 获得无用的分类的信息数组
    参数: 无 
    返回值: 分类的数组(array) 
------------------------------------ */
  function tep_get_disabled_categories()
  {
    $categories_ids = array();
    $categories = array();
  $categories_query = tep_db_query("select * from (select c.parent_id, cd.site_id, cd.categories_status, c.categories_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd  where c.categories_id = cd.categories_id order by site_id DESC) c  where site_id = ".SITE_ID." or site_id = 0 group by categories_id");
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
  
/* -------------------------------------
    功能: 获得无用的分类
    参数: 无 
    返回值: 分类的信息(string) 
------------------------------------ */
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
  
/* -------------------------------------
    功能: 获得无用的商品的信息数组
    参数: 无 
    返回值: 无用的商品的数组(array) 
------------------------------------ */
  function tep_get_disabled_products(){
    $products_ids = array();
    $products_query = tep_db_query("select p.products_id from `" . TABLE_CATEGORIES . "` c," . TABLE_PRODUCTS . " p, "  . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and c.categories_id in".tep_not_in_disabled_categories());
  while($product = tep_db_fetch_array($products_query)){
    $products_ids[] = $product['products_id'];
  }
  return $products_ids;
  }

/* -------------------------------------
    功能: 获得无用的商品
    参数: 无 
    返回值: 商品的信息(string) 
------------------------------------ */
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
  
/* -------------------------------------
    功能: 判断该商品是否为买取
    参数: $products_id(int) 商品id 
    返回值: 商品是否为买取(string) 
------------------------------------ */
  function tep_get_bflag_by_product_id($product_id) {
    // 0 => sell   1 => buy
    $product_query = tep_db_query("select products_bflag from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_bflag'];
  }

/* -------------------------------------
    功能: 判断该商品的cflag标识
    参数: $products_id(int) 商品id 
    返回值: cflag标识(string) 
------------------------------------ */
  function tep_get_cflag_by_product_id($product_id) {
    // 0 => no   1=> yes
    $product_query = tep_db_query("select products_cflag from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_cflag'];
  }

/* -------------------------------------
    功能: 获得文件头名字
    参数: $filename(string) 文件名 
    返回值: 文件名(string) 
------------------------------------ */
  function tep_get_filename($filename){
    $arr = explode('.', $filename);
    return $arr[0];
  }
  
/* -------------------------------------
    功能: 获得常量的值
    参数: $const_name(string) 常量的名字 
    返回值: 常量的值(string) 
------------------------------------ */
  function tep_get_value_by_const_name($const_name) {
    eval('$value = ' . $const_name . ';');
    return $value;
  }

/* -------------------------------------
    功能: html的头部
    参数: 无 
    返回值: html的头部(string) 
------------------------------------ */
  function page_head(){
    global $HTTP_GET_VARS, $request_type, $breadcrumb;
    $title       = C_TITLE;
    $keywords    = C_KEYWORDS;
    $description = C_DESCRIPTION;
    $robots      = C_ROBOTS;
    $copyright   = C_AUTHER;
    
    $search = $replace = array();
    
    
    if (SITE_ID > 3) {
      // 这段代码为了兼容id和kt的ssl链接
      //id add script name
      $ssl_pos = strrpos($_SERVER['SCRIPT_NAME'], '/');
      $script_name = substr($_SERVER['SCRIPT_NAME'], $ssl_pos);
      $_SERVER['SCRIPT_NAME'] = $script_name;
    }
if(defined(strtoupper(str_replace('.php', '', str_replace('/', '', $_SERVER['SCRIPT_NAME'])))._CACHETIME)){
$num = constant(strtoupper(str_replace('.php', '', str_replace('/', '', $_SERVER['SCRIPT_NAME'])))._CACHETIME);

header("Cache-Control:");
header("Pragma:");
header("Last-Modified:".date("D, d M Y H:i:s",time())." GMT"); 
header("Expires:".date("D, d M Y H:i:s",time()+60*$num)." GMT");
    }else{
header("Cache-Control:");
header("Pragma:");
header("Expires:".date("D, d M Y H:i:s",0)." GMT");
    }
    switch (str_replace('/', '', $_SERVER['SCRIPT_NAME'])) {
      case FILENAME_FAQ:
        global $current_faq_category_id;
           if($faq_category_info = tep_get_faq_category_info($current_faq_category_id)){
             $sub_len = intval(MAX_META_FAQ_TITLE) - mb_strlen(' | '.TEXT_FAQ.' - '.STORE_NAME,'UTF-8');
             if($sub_len>0){
               $title = mb_substr(strip_tags($faq_category_info['title']),0,$sub_len,'UTF-8')
               .' | '.TEXT_FAQ.' - '.STORE_NAME;
             }else{
               $title = ' | '.TEXT_FAQ.' - '.STORE_NAME;
             }
             $keywords = strip_tags($faq_category_info['keywords']);
             $description = mb_substr(strip_tags($faq_category_info['description']),0,
                 MAX_META_FAQ_DESCRIPTION,'UTF-8');
             $use_mate_seo = true;
           }
        break;
      case FILENAME_FAQ_INFO:
         global $faq_question_id;
           if($faq_question_info = tep_get_faq_question_info($faq_question_id)){
             $sub_len = intval(MAX_META_FAQ_TITLE) -  mb_strlen(' - '.STORE_NAME,'UTF-8');
             if($sub_len>0){
               $title = mb_substr(strip_tags($faq_question_info['ask']),0,$sub_len,'UTF-8').
               ' - '.STORE_NAME;
             }else{
               $title = ' - '.STORE_NAME;
             }
             $keywords = strip_tags($faq_question_info['keywords']);
             $description = mb_substr(strip_tags($faq_question_info['answer']),0,
                 MAX_META_FAQ_DESCRIPTION,'UTF-8');
             $use_mate_seo = true;
           }
        break;
      case FILENAME_DEFAULT:
         global $cPath_array, $cPath, $seo_tags, $seo_category, $seo_manufacturers;
         if (isset($cPath_array)) {
            if (isset($cPath) && tep_not_null($cPath)) {
              switch(SITE_ID) {
                case '3':
                  $title       = $seo_category['categories_name'] . 'と言えば'.STORE_NAME.'｜' . (tep_not_null($seo_category['categories_meta_text']) ? $seo_category['categories_meta_text'] : C_TITLE); 
                  break;
                case '2':
                  $title       = $seo_category['categories_name'] . (tep_not_null($seo_category['categories_meta_text']) ? '-' .  $seo_category['categories_meta_text'] . '｜激安の'.STORE_NAME : C_TITLE); 
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
      case FILENAME_NEWS:
        global $breadcrumb, $latest_news;
        $title = (!isset($_GET['news_id']) or !(int)$_GET['news_id']) ? $breadcrumb->trail_title(' &raquo; ') : $latest_news['headline'];
        break;
      case FILENAME_MANUFACTURERS:
        global $breadcrumb;
        switch(SITE_ID){
          case "3":
            $title        = $breadcrumb->trail_title(' &raquo; ');
            $keywords     =
              "RMT,スクウェア・エニックス,NCJ,ガンホー,NEXON,ゲームオン,コーエー,セガ,販売,買取,アイテム,アカウント";
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
            $keywords     =
              "RMT,スクウェア・エニックス,NCJ,ガンホー,NEXON,ゲームオン,コーエー,セガ,販売,買取,アイテム,アカウント";
            $description  = "ゲームメーカーの一覧です。スクウェア・エニックス、NCJ、ガンホーなど。RMTのことなら".TITLE."へ";
            break;
        }
        break;
      case FILENAME_REORDER:
      case FILENAME_REORDER2:
        global $breadcrumb;
        $title       = "RMT &raquo; 再配達フォーム｜" . TITLE;
        $keywords    = "RMT,再配達," . TITLE;
        $description = "再配達依頼。お届け日時やお届け先を変更するページです。";
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
      case FILENAME_FAQ:
        $title = TITLE;
        break;
    }
    if(!$use_mate_seo){
    $script_name = tep_get_filename(str_replace('/', '', $_SERVER['SCRIPT_NAME']));
    

    if ($script_name == 'news' && $_GET['news_id']) {
      $script_name = $script_name.'_info';
    }
    if ($script_name == 'non-member_auth') {
      $script_name = str_replace('-','_',$script_name);
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
          if ($page == 1) { 
            $replace = array_merge($replace, array('', $metas['keywords'], $metas['description'],));
          } else {
            $replace = array_merge($replace, array($page . 'ページ目', $metas['keywords'], $metas['description'],));
          }
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
      case FILENAME_NEWS:
        if ((int)$_GET['news_id']) {
          if (defined('MODULE_METASEO_NEWS_INFO_TITLE') && strlen(tep_get_value_by_const_name('MODULE_METASEO_NEWS_INFO_TITLE'))) {
            $title       = tep_get_value_by_const_name('MODULE_METASEO_NEWS_INFO_TITLE');
          }
          if (defined('MODULE_METASEO_NEWS_INFO_KEYWORDS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_NEWS_INFO_KEYWORDS'))) {
            $keywords    = tep_get_value_by_const_name('MODULE_METASEO_NEWS_INFO_KEYWORDS');
          }
          if (defined('MODULE_METASEO_NEWS_INFO_DESCRIPTION') && strlen(tep_get_value_by_const_name('MODULE_METASEO_NEWS_INFO_DESCRIPTION'))) {
            $description = tep_get_value_by_const_name('MODULE_METASEO_NEWS_INFO_DESCRIPTION');
          }
          if (defined('MODULE_METASEO_NEWS_INFO_ROBOTS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_NEWS_INFO_ROBOTS'))) {
            $robots      = tep_get_value_by_const_name('MODULE_METASEO_NEWS_INFO_ROBOTS');
          }
          if (defined('MODULE_METASEO_NEWS_INFO_COPYRIGHT') && strlen(tep_get_value_by_const_name('MODULE_METASEO_NEWS_INFO_COPYRIGHT'))) {
            $copyright   = tep_get_value_by_const_name('MODULE_METASEO_NEWS_INFO_COPYRIGHT');
          }
          $search  = array_merge($search, array('#TITLE#'));
          $replace = array_merge($replace, array($latest_news['headline']));
        } else {
          if (defined('MODULE_METASEO_NEWS_TITLE') && strlen(tep_get_value_by_const_name('MODULE_METASEO_NEWS_TITLE'))) {
            $title       = tep_get_value_by_const_name('MODULE_METASEO_NEWS_TITLE');
          }
          if (defined('MODULE_METASEO_NEWS_KEYWORDS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_NEWS_KEYWORDS'))) {
            $keywords    = tep_get_value_by_const_name('MODULE_METASEO_NEWS_KEYWORDS');
          }
          if (defined('MODULE_METASEO_NEWS_DESCRIPTION') && strlen(tep_get_value_by_const_name('MODULE_METASEO_NEWS_DESCRIPTION'))) {
            $description = tep_get_value_by_const_name('MODULE_METASEO_NEWS_DESCRIPTION');
          }
          if (defined('MODULE_METASEO_NEWS_ROBOTS') && strlen(tep_get_value_by_const_name('MODULE_METASEO_NEWS_ROBOTS'))) {
            $robots      = tep_get_value_by_const_name('MODULE_METASEO_NEWS_ROBOTS');
          }
          if (defined('MODULE_METASEO_NEWS_COPYRIGHT') && strlen(tep_get_value_by_const_name('MODULE_METASEO_NEWS_COPYRIGHT'))) {
            $copyright   = tep_get_value_by_const_name('MODULE_METASEO_NEWS_COPYRIGHT');
          }
          $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
          if ($page != 1) {
            $search  = array_merge($search,  array('#SEO_PAGE#'));
            $replace = array_merge($replace, array($page . 'ページ目'));
          }
        }
        break;
      case FILENAME_TAGS:
          $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
          if ($page != 1) {
            $search  = array_merge($search,  array('#SEO_PAGE#'));
            $replace = array_merge($replace, array($page . 'ページ目'));
          }
        
        break;
      case FILENAME_MANUFACTURERS:
        // MAX_DISPLAY_SEARCH_RESULTS
        $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
        if ($page != 1) {
          $search  = array_merge($search,  array('#SEO_PAGE#'));
          $replace = array_merge($replace, array($page . 'ページ目'));
        }
        break;
      case FILENAME_PRESENT:
        // MAX_DISPLAY_SEARCH_RESULTS
        $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
        if ($page != 1) {
          $search  = array_merge($search,  array('#SEO_PAGE#'));
          $replace = array_merge($replace, array($page . 'ページ目'));
        } 
        break;
      case FILENAME_PRODUCT_NEW:
        // MAX_DISPLAY_PRODUCTS_NEW
        $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
        if ($page != 1) {
          $search  = array_merge($search,  array('#SEO_PAGE#'));
          $replace = array_merge($replace, array($page . 'ページ目'));
        } 
        break;
      case FILENAME_SPECIALS:
        // MAX_DISPLAY_SPECIAL_PRODUCTS
        $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
        if ($page != 1) {
          $search  = array_merge($search,  array('#SEO_PAGE#'));
          $replace = array_merge($replace, array($page . 'ページ目'));
        } 
        break;
      case FILENAME_ADVANCED_SEARCH_RESULT:
        // MAX_DISPLAY_SEARCH_RESULTS
        $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
        if ($page != 1) {
          $search  = array_merge($search,  array('#SEO_PAGE#'));
          $replace = array_merge($replace, array($page . 'ページ目'));
        } 
        break;
      case FILENAME_REVIEWS:
        // MAX_DISPLAY_NEW_REVIEWS
        $page    = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1 ;
        if ($page != 1) {
          $search  = array_merge($search,  array('#SEO_PAGE#'));
          $replace = array_merge($replace, array($page . 'ページ目'));
        } 
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
      if ($c_page != 1) {
        $search = array_merge(array('#SEO_PAGE#'), $search); 
        $replace = array_merge(array($c_page.'ページ目'), $replace); 
      }
    }
    $title       = str_replace($search, $replace, $title);
    $keywords    = str_replace($search, $replace, $keywords);
    $description = str_replace($search, $replace, $description);
    $copyright   = str_replace($search, $replace, $copyright);
    // replace again
    $title       = str_replace($search, $replace, $title);
    $keywords    = str_replace($search, $replace, $keywords);
    $description = str_replace($search, $replace, $description);
    $copyright   = str_replace($search, $replace, $copyright);
    
    $title = str_replace('#SEO_PAGE#', '', $title); 
    $keywords = str_replace('#SEO_PAGE#', '', $keywords); 
    $description = str_replace('#SEO_PAGE#', '', $description); 
    $copyright = str_replace('#SEO_PAGE#', '', $copyright); 
     
    $title = str_replace(' &raquo; ', ' ', $title); 
    $keywords = str_replace(' &raquo; ', ' ', $keywords); 
    $description = str_replace(' &raquo; ', ' ', $description); 
    $copyright = str_replace(' &raquo; ', ' ', $copyright); 
    }
  ?>
<?php if(NEW_STYLE_WEB!==true){?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php } else { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 Strict//EN">
<?php } ?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo strip_tags($title ? $title : TITLE); ?></title>
<meta name="keywords" content="<?php echo $keywords ? $keywords : C_KEYWORDS;?>">
<meta name="description" content="<?php echo $description ? $description : C_DESCRIPTION;?>">
<?php if ($request_type == 'SSL') {?>
<meta name="robots" content="NOINDEX">
<?php } else { ?>
<meta name="robots" content="<?php echo strtoupper($robots ? $robots : C_ROBOTS);?>">
<?php } ?>
<meta name="copyright" content="<?php echo $copyright ? $copyright : C_AUTHER;?>">
<meta http-equiv="Content-Style-Type" content="text/css">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<?php
$site_romaji = tep_get_site_romaji_by_id(SITE_ID);
$oconfig_raw = tep_db_query("select value from ".TABLE_OTHER_CONFIG." where keyword = 'css_random_string' and site_id = '".SITE_ID."'");
$oconfig_res = tep_db_fetch_array($oconfig_raw);
if ($oconfig_res) {
  $css_random_str = substr($oconfig_res['value'], 0, 4);
} else {
  $css_random_str = date('YmdHi', time());
}
?>
<?php if(NEW_STYLE_WEB===true){?>
<link rel="stylesheet" type="text/css" href="<?php echo 
  'css/cssbase-min.css?v='.$css_random_str;?>"> 
<link rel="stylesheet" type="text/css" href="<?php echo 
'css/cssfonts-min.css?v='.$css_random_str;?>"> 
<link rel="stylesheet" type="text/css" href="<?php echo 
'css/cssreset.css?v='.$css_random_str;?>">
<link rel="stylesheet" type="text/css" href="<?php echo 
'css/grids-min.css?v='.$css_random_str;?>">
<link href="<?php echo
'banner/css/webwidget_slideshow_dot.css?v='.$css_random_str;?>" rel="stylesheet" type="text/css">
<script type="text/javascript" src="js/search_include.js"></script>
<?php } ?>
<link rel="stylesheet" type="text/css" href="<?php echo 'css/'.$site_romaji.'.css?v='.$css_random_str;?>"> 
<?php
if($_SERVER['HTTPS'] == 'on'){
?>
<link rel="shortcut icon" type="image/ico" href="<?php echo HTTPS_SERVER;?>/favicon.ico">
<?php }else { ?>
<link rel="shortcut icon" type="image/ico" href="<?php echo HTTP_SERVER;?>/favicon.ico">
<?
}
    switch (str_replace('/', '', $_SERVER['SCRIPT_NAME'])) {
      case FILENAME_CATEGORY:
        if (isset($cPath) && $cPath || isset($_GET['tags_id']) || isset($_GET['manufacturers_id'])) {
?>
<script type="text/javascript" src="js/sort.js"></script>
<?php
        }
        break;
    }
?>
<script type="text/javascript" src="js/split_page.js"></script> 
<?php
  }

/* -------------------------------------
    功能: 获得生产商的meta的信息 
    参数: $manufacturers_id(int) 生产商id 
    返回值: meta的信息(array) 
------------------------------------ */
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
        $metaQuery .= "AND products_description.products_status != '0' ";
        $metaQuery .= "AND products_description.products_status != '3' ";
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

/* -------------------------------------
    功能: 获得用-符号分割的日期 
    参数: $string(string) 日期字符串 
    返回值: 处理后的年月日(string) 
------------------------------------ */
  function str_string($string='') {
    if(ereg("-", $string)) {
    $string_array = explode("-", $string);
    return $string_array[0] . '年' . $string_array[1] . '月' . $string_array[2] . '日';
  }
  }

/* -------------------------------------
    功能: 获得指定长度的数字随机数 
    参数: $len(int) 随机数长度 
    返回值: 随机数(string) 
------------------------------------ */
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
/* -------------------------------------
    功能: 获得随机长度的随机字母组成的字符串 
    参数: 无 
    返回值: 随机字符串(string) 
------------------------------------ */
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
/* -------------------------------------
    功能: 删除指定目录下的文件以及该目录 
    参数: $dir(string) 目录路径 
    返回值: 无 
------------------------------------ */
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

/* -------------------------------------
    功能: 获得cPath路径 
    参数: $id(int) 数组的键值
    参数: $categories(array) 分类的信息
    返回值: 分类路径(string) 
------------------------------------ */
  function get_cPath($id, $categories)
  {
      if($categories[$id]['parent_id'] == '0'){
        return $categories[$id]['categories_id'];
      } else {
        return ($categories[$categories[$id]['parent_id']]['parent_id'] == 0 ? $categories[$categories[$id]['parent_id']]['categories_id'] : $categories[$categories[$id]['parent_id']]['parent_id'].'_'.$categories[$categories[$id]['parent_id']]['categories_id']) . '_' . $categories[$id]['categories_id'];
      }
  }

/* -------------------------------------
    功能: rss的url信息 
    参数: $loc(string) url值
    参数: $lastmod(string) 上次访问时间
    参数: $changefreq(string) 改变频率
    参数: $priority(string) 优先级
    返回值: rss部分信息(string) 
------------------------------------ */
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

/* -------------------------------------
    功能: 获得区域列表 
    参数: $name(string) 下拉列表的名字
    参数: $selected(string) 默认值 
    参数: $country_code(string) 国家代码
    返回值: 区域列表(string) 
------------------------------------ */
      function tep_get_zone_list2($name, $selected = '', $country_code = '107') {
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

/* -------------------------------------
    功能: 获得该商品所在的分类的名字 
    参数: $products_id(int) 商品id
    参数: $return(boolean) 返回类型
    返回值: 分类的名字(string) 
------------------------------------ */
  function ds_tep_get_categories($products_id, $return) {
    global $languages_id;
  
  $categories_path = tep_get_product_path($products_id);
  $categories_path_array = explode("_", $categories_path);
  
  if($return == 1) {
    //返回一级分类的图像
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
    //返回二级分类名
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

/* -------------------------------------
    功能: 获得该商品的点数 
    参数: $products_id(int) 商品id
    返回值: 返回的点数(int) 
------------------------------------ */
  function ds_tep_get_point_value($products_id) {
  if ($new_price = tep_get_products_special_price($products_id)) {
    $price = $new_price;
  } else {
    $query = tep_db_query("select products_price from ".TABLE_PRODUCTS." where products_id = '".$products_id."'");
    $result = tep_db_fetch_array($query);
    $price = $result['products_price'];
  }
  
  //计算返点
  $point_value = (int)($price * MODULE_ORDER_TOTAL_POINT_FEE);
  
  return $point_value;
  }

/* -------------------------------------
    功能: 检查是否超库存 
    参数: $options_stock(int) 库存数
    参数: $orders_quantity(int) 所要数量 
    返回值: 超出的错误信息(string) 
------------------------------------ */
  function tep_check_opstock($options_stock, $orders_quantity) {
    $stock_left = $options_stock - $orders_quantity;
    $out_of_stock = '';

    if ($stock_left < 0) {
      $out_of_stock = '<span class="markProductOutOfStock" style="color:#CC0033">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
    }

    return $out_of_stock;
  }

/* -------------------------------------
    功能: 是否存在买取商品 
    参数: 无 
    返回值: 是否存在买取商品(string/boolean) 
------------------------------------ */
  function ds_count_bflag() {
    global $cart;
    return $cart->show_total() < 0 ? 'View' : false;
  }
  
/* -------------------------------------
    功能: 通过商品的数量以及是否买取返回相应的商品信息 
    参数: $pID(int) 商品id
    参数: $qty(int) 数量
    参数: $string(string) 信息 
    返回值: 相应的商品信息(string) 
------------------------------------ */
  function ds_replace_plist($pID, $qty, $string) {
    $query = tep_db_query("select * from ".TABLE_PRODUCTS." where products_id = '".(int)tep_get_prid($pID)."'");
    $result = mysql_fetch_array($query);
  
    if($qty < 1) {
      if($result['products_bflag'] == '1') {
        # 买取商品
      return '<span class="markProductOutOfStock">一時停止</span>';
      } else {
        # 通常商品
        return '<span class="markProductOutOfStock">在庫切れ</span>';
      }
    } else {
      return $string;
    }
  }

/* -------------------------------------
    功能: 通过分类id返回其相关信息 
    参数: $cid(int) 分类id
    参数: $site_id(int) 网站id 
    参数: $lid(int) 语言id 
    参数: $default(boolean) 是否返回默认数据 
    返回值: 分类的相关信息(array) 
------------------------------------ */
  function tep_get_category_by_id($cid, $site_id, $lid, $default = true){
    $sql = "
        select c.categories_id,
               cd.categories_status,
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

/* -------------------------------------
    功能: 通过商品id返回其相关信息 
    参数: $pid(int) 商品id
    参数: $site_id(int) 网站id 
    参数: $lid(int) 语言id 
    参数: $default(boolean) 是否返回默认数据 
    参数: $page(string) 指定页面 
    参数: $show(boolean) 是否显示关闭数据 
    返回值: 商品的相关信息(array) 
------------------------------------ */
  function tep_get_product_by_id($pid,$site_id, $lid, $default = true,$page='', $show=false){ 
    if ($default) {
    $sql = "
        SELECT * FROM (SELECT p.products_id, 
               p.products_real_quantity + p.products_virtual_quantity as products_quantity,
               p.products_real_quantity, 
               p.products_virtual_quantity, 
               p.products_model, 
               p.products_image, 
               p.products_image2, 
               p.products_image3, 
               p.products_price, 
               p.products_price_offset,
               p.products_date_added, 
               p.products_date_available, 
               p.products_weight,
               pd.products_status,
               p.products_tax_class_id, 
               p.manufacturers_id,
               p.products_ordered,
               p.products_bflag,
               p.products_cflag,
               p.products_small_sum,
               p.option_type,
         p.products_attention_1,
               p.products_attention_1_1,
                p.products_attention_1_2,
                p.products_attention_1_3,
                p.products_attention_1_4, 
               p.products_attention_2, 
               p.products_attention_3, 
               p.products_attention_4, 
               p.products_attention_5, 
               p.products_cart_image,
               p.products_cartorder,
               p.products_cartflag,
               p.belong_to_option,
               pd.language_id,
               pd.products_name, 
               pd.products_description,
               pd.option_image_type, 
               pd.site_id,
               pd.romaji, 
               pd.products_url,
               pd.products_viewed,
               pd.preorder_status
        FROM " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
        WHERE p.products_id = '" . $pid . "' 
          AND pd.products_id = '" .  $pid . "'" . " 
          AND pd.language_id ='" . $lid . "' 
        ORDER BY pd.site_id DESC
        ) c WHERE  site_id = '0' OR site_id = '".$site_id."'
        GROUP BY products_id 
       "; 
       if($page=='product_info'){
       }else if($page=='shopping_cart'){
         if ($show) {
           $sql .= " HAVING c.products_status != '3'";
         } else {
           $sql .= " HAVING c.products_status != '0' and c.products_status != '3'";
         }
       }else{
         $sql .= " HAVING c.products_status != '0'";
       }
    } else {
    $sql = "
        SELECT p.products_id, 
               p.products_real_quantity + p.products_virtual_quantity as products_quantity,
               p.products_real_quantity, 
               p.products_virtual_quantity, 
               p.products_model, 
               p.products_image, 
               p.products_image2, 
               p.products_image3, 
               p.products_price, 
               p.products_price_offset,
               p.products_date_added, 
               p.products_date_available, 
               p.products_weight,
               pd.products_status,
               p.products_tax_class_id, 
               p.manufacturers_id,
               p.products_ordered,
               p.products_bflag,
               p.products_cflag,
               p.products_small_sum,
               p.option_type,
               p.products_attention_1_1,
               p.products_attention_1_2,
               p.products_attention_1_3,
               p.products_attention_1_4,
         p.products_attention_1,
               p.products_attention_2, 
               p.products_attention_3, 
               p.products_attention_4, 
               p.products_attention_5, 
               pd.language_id,
               pd.products_name, 
               pd.products_description,
               pd.site_id,
               pd.romaji, 
               pd.option_image_type, 
               pd.products_url,
               pd.products_viewed,
               pd.preorder_status
        FROM " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
        WHERE p.products_id = '" . $pid . "' 
          AND pd.products_status != '0' 
          AND pd.products_status != '3' 
          AND pd.products_id = '" .  $pid . "'" . " 
          AND pd.language_id ='" . $lid . "' 
          "; 
      $sql .= "
          AND pd.site_id='" . $site_id . "' 
      ";
    }
    $product_query = tep_db_query($sql);
    $product = tep_db_fetch_array($product_query);
    return $product;
  }
/* -------------------------------------
    功能: 商品描述在指定网站是否存在 
    参数: $pid(int) 商品id
    参数: $sid(int) 网站id 
    参数: $lid(int) 语言id 
    返回值: 描述是否存在(boolean) 
------------------------------------ */
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

/* -------------------------------------
    功能: 获得faq的分类的信息 
    参数: $c_id(int) id
    返回值: faq的分类的信息(array) 
------------------------------------ */
  function tep_get_faq_categories($c_id){
    $query = tep_db_query("
        select * 
        from faq_categories 
        where c_id = '".(int)$c_id."'
        ");
    return tep_db_fetch_array($query);
  }

/* -------------------------------------
    功能: 获得faq的问题的信息 
    参数: $q_id(int) id
    返回值: faq的问题的信息(array) 
------------------------------------ */
  function tep_get_faq_questions($q_id){
    $query = tep_db_query("
        select * 
        from faq_questions 
        where q_id = '".(int)$q_id."'
        ");
    return tep_db_fetch_array($query);
  }

/* -------------------------------------
    功能: 获得faq的分类的信息数组 
    参数: $g_id(int) id
    返回值: faq的分类的信息(array) 
------------------------------------ */
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

/* -------------------------------------
    功能: 获得faq的问题的信息 
    参数: $c_id(int) 问题id
    返回值: faq的问题的信息(array) 
------------------------------------ */
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
/* -------------------------------------
    功能: 判断订单是否完成 
    参数: $osid(int) 订单状态id
    返回值: 订单是否完成(boolean) 
------------------------------------ */
function tep_orders_status_finished($osid){
    $query = tep_db_query("
        select * 
        from  ".TABLE_ORDERS_STATUS."
        where orders_status_id = '".(int)$osid."'
        ");
    $os = tep_db_fetch_array($query);
    return isset($os['finished']) && $os['finished'];
}

/* -------------------------------------
    功能: 根据url获得rss内容 
    参数: $url(string) 网站地址
    返回值: rss内容(string) 
------------------------------------ */
  function tep_get_rss($url){

    $input_arr = array();
    $i = 0; 
    $rss_str = @file_get_contents($url);
    preg_match_all("/\<item rdf:about=\"([^\"]*)\"\>(.*?)\<\/item\>/s", $rss_str, $gamearr);

    if (!empty($gamearr[2])) {
      foreach ($gamearr[2] as $gkey => $game)
      {
        preg_match_all("/\<title\>(.*?)\<\/title\>/", $game, $title);
        preg_match_all("/\<link\>(.*?)\<\/link\>/", $game, $link);
        preg_match_all("/\<dc:date\>(.*?)\<\/dc:date\>/", $game, $date_added);

        if (isset($link[1][0])) {

          $input_arr[$i]['url'] =  $link[1][0]; 

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
  
/* -------------------------------------
    功能: 获得该分类的rss数据 
    参数: $cid(int) 分类id
    返回值: 分类的rss数据(string) 
------------------------------------ */
  function tep_get_categories_rss($cid){
    $rss = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CATEGORIES_RSS." where categories_id='".$cid."'"));
    if($rss && isset($rss['categories_rss']) && $rss['categories_rss']){
      return tep_get_rss($rss['categories_rss']);
    } else {
      return null;
    }
  }

/* -------------------------------------
    功能: 获得该分类下的子节点的信息 
    参数: $parent_id(int) 分类id
    参数: $languages_id(int) 语言id 
    返回值: 子节点的信息(array) 
------------------------------------ */
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

/* -------------------------------------
    功能: 是否显示警告 
    参数: $categories_id(int) 分类id
    参数: $languages_id(int) 语言id 
    返回值: 显示警告(string/boolean) 
------------------------------------ */
function tep_show_warning($categories_id, $languages_id = 4) {
  $categories_query = tep_db_query("select * from (select c.categories_id, c.parent_id, cd.categories_status, cd.site_id, cd.categories_name, c.sort_order from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and cd.language_id = '".$languages_id."' and c.categories_id = '".$categories_id."' order by site_id DESC) c where site_id = '".SITE_ID."' or site_id = 0 group by categories_id order by sort_order, categories_name");
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

/* -------------------------------------
    功能: 获得跟该商品所关联的分类id 
    参数: $products_id(int) 商品id
    返回值: 关联的分类id(string) 
------------------------------------ */
function tep_get_products_categories_id($products_id) {
  $query = tep_db_query("select * from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $products_id . "'");
  $c = tep_db_fetch_array($query);
  return $c['categories_id'];
}

/* -------------------------------------
    功能: 获得该商品的特价 
    参数: $products_id(int) 商品id
    返回值: 商品的特价(float) 
------------------------------------ */
function tep_get_products_special_price($product_id) {
  $product_query = tep_db_query("select * from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
  $product = tep_db_fetch_array($product_query);

  return tep_get_special_price($product['products_price'], $product['products_price_offset'], $product['products_small_sum']);
}

/* -------------------------------------
    功能: 获得该商品的特价 
    参数: $price(float) 价格
    参数: $offset(string) 间隔值 
    参数: $sum(string) 折扣 
    返回值: 商品的特价(float/boolean) 
------------------------------------ */
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

/* -------------------------------------
    功能: 获得该商品的价格 
    参数: $price(float) 价格
    参数: $offset(string) 间隔值 
    参数: $sum(string) 折扣 
    参数: $bflag(int) 是否为买取 
    返回值: 商品的价格(float) 
------------------------------------ */
function tep_get_price ($price, $offset, $sum = '', $bflag = 0) {
  if ($price && $sum) {
    $hprice = $price;
    foreach (tep_get_wari_array_by_sum($sum) as $p) {
      if ($p + $price > $hprice) {
        $hprice = $p + $price;
      }
    }
    return $hprice;
  } else if ($price && $offset && $offset != 0) {
    return calculate_special_price($price, $offset, $bflag);
  } else {
    return $price;
  }
}

/* -------------------------------------
    功能: 获得该商品的最终价格 
    参数: $price(float) 价格
    参数: $offset(string) 间隔值 
    参数: $sum(string) 折扣 
    参数: $quantity(int) 数量 
    返回值: 商品的最终价格(float) 
------------------------------------ */
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
  } else {
    return $price;
  }
}

/* -------------------------------------
    功能: 获得该商品的价格信息 
    参数: $products_id(int) 商品id 
    返回值: 商品的价格信息(array) 
------------------------------------ */
function tep_get_products_price ($products_id) {
  $product_query = tep_db_query("select * from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
  $product = tep_db_fetch_array($product_query);
  if ($product['products_bflag'] == 1) {
    return array(
      'price' => tep_get_price($product['products_price'], $product['products_price_offset'], $product['products_small_sum'], $product['products_bflag']),
      'sprice' => tep_get_special_price($product['products_price'], $product['products_price_offset'], $product['products_small_sum'])
    );
  } else {
    return array(
      'price' => tep_get_price($product['products_price'], $product['products_price_offset'], $product['products_small_sum']),
      'sprice' => tep_get_special_price($product['products_price'], $product['products_price_offset'], $product['products_small_sum'])
    );
  }
}

/* -------------------------------------
    功能: 获得折扣信息 
    参数: $small_num(string) 折扣字符串 
    返回值: 折扣信息(array) 
------------------------------------ */
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
/* -------------------------------------
    功能: 计算特价 
    参数: $price(float) 价格 
    参数: $offset(string) 折扣值 
    参数: $bflag(int) 是否为买取 
    返回值: 特价(float) 
------------------------------------ */
function calculate_special_price($price, $offset, $bflag = 0) {
  $price = (float) $price;
  $offset = trim($offset);
  
  $special = $price;
  
  if (substr($offset, -1) == '%') {
    $special = $price +(($offset / 100) * $price);
  } else {
    $offset = (float) $offset;
    if ($bflag == 1) {
      if ($offset > 0) {
        $special = $price - $offset;
      } else {
        $special = $price + abs($offset);
      }
    } else {
      $special = $price + $offset;
    }
  }
  return $special;
}

/* -------------------------------------
    功能: 更新订单状态 
    参数: $orders_status_id(int) 订单状态id 
    返回值: 无 
------------------------------------ */
function orders_status_updated($orders_status_id) {
  $orders_status = tep_db_fetch_array(tep_db_query("select * from orders_status where orders_status_id='".$orders_status_id."'"));
  tep_db_query("
    update ".TABLE_ORDERS." set language_id='".$orders_status['language_id']."',orders_status_name='".$orders_status['orders_status_name']."',orders_status_image='".$orders_status['orders_status_image']."',finished='".$orders_status['finished']."'
  ");
}

/* -------------------------------------
    功能: 更新订单相关信息 
    参数: $orders_id(int) 订单id 
    返回值: 无 
------------------------------------ */
function orders_updated($orders_id) {
  tep_db_query("update ".TABLE_ORDERS." set language_id = ( select language_id from ".TABLE_ORDERS_STATUS." where orders_status.orders_status_id=orders.orders_status ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_ORDERS." set finished = ( select finished from ".TABLE_ORDERS_STATUS." where orders_status.orders_status_id=orders.orders_status ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_ORDERS." set orders_status_name = ( select orders_status_name from ".TABLE_ORDERS_STATUS." where orders_status.orders_status_id=orders.orders_status ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_ORDERS." set orders_status_image = ( select orders_status_image from ".TABLE_ORDERS_STATUS." where orders_status.orders_status_id=orders.orders_status ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_ORDERS_PRODUCTS." set torihiki_date = ( select torihiki_date from ".TABLE_ORDERS." where orders.orders_id=orders_products.orders_id ) where orders_id='".$orders_id."'");
}

/* -------------------------------------
    功能: 替换网站名字 
    参数: $str(string) 字符串 
    返回值: 替换后的文字(string) 
------------------------------------ */
function replace_store_name($str) {
  return str_replace('#STORE_NAME#', STORE_NAME, $str);
}

/* -------------------------------------
    功能: 获得该分类下的子分类 
    参数: $categories_id(int) 分类id 
    参数: $languages_id(int) 语言id 
    返回值: 子分类(array) 
------------------------------------ */
function tep_get_categories_id_by_parent_id($categories_id, $languages_id = 4) {
  $arr = array();
  $categories = tep_get_categories_by_parent_id($categories_id, $languages_id);
  foreach ($categories as $c){
    $arr[] = $c['categories_id'];
  }
  return $arr;
}

/* -------------------------------------
    功能: 获得该订单的总价 
    参数: $orders_id(int) 订单id 
    返回值: 总价(string) 
------------------------------------ */
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

/* -------------------------------------
    功能: 显示正值的数量 
    参数: $quantity(int) 数量 
    返回值: 正值的数量(int) 
------------------------------------ */
function tep_show_quantity($quantity) {
  return $quantity > 0 ? $quantity : 0;
}

/* -------------------------------------
    功能: 添加rmt标识 
    参数: $name(string) 名字 
    返回值: 处理后的字符串(string) 
------------------------------------ */
function tep_add_rmt($name) {
  if (!strpos($name, 'RMT')){
    return $name . ' RMT';
  }
  return $name;
}

/* -------------------------------------
    功能: 根据指定格式判断$_SERVER['REQUEST_URI']是否标准 
    参数: $p(string) 规则 
    返回值: 无 
------------------------------------ */
function check_uri($p) {
  if (preg_match($p, $_SERVER['REQUEST_URI'])) {
    forward404();
  }
}

/* -------------------------------------
    功能: 获得url的网站名 
    参数: $url(string) 网站地址 
    返回值: 网站名(string/boolean) 
------------------------------------ */
function tep_get_domain($url)
{
  if (preg_match('/https?:\/\/([^\/?]*)/', $url, $out)) {
    return $out[1];
  } else {
    return false;
  }
}

/* -------------------------------------
    功能: 更新顾客最新下订单时间 
    参数: 无 
    返回值: 无 
------------------------------------ */
function last_customer_action() {
  tep_db_query("update ".TABLE_CONFIGURATION." set configuration_value=now() where configuration_key='LAST_CUSTOMER_ACTION'");
}

/* -------------------------------------
    功能: 把全角转为半角 
    参数: $str(string) 字符串 
    返回值: 处理后的字符串(string) 
------------------------------------ */
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

/* -------------------------------------
    功能: 根据分类id获得该分类的罗马字 
    参数: $cpath(string) 分类路径 
    返回值: 分类的罗马字(string) 
------------------------------------ */
function tep_get_romaji_cpath($cpath)
{
    global $languages_id;

    if (empty($language)) $language = $languages_id;
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

/* -------------------------------------
    功能: 根据商品的罗马字查询商品id 
    参数: $romaji(string) 罗马字 
    参数: $categories_id(int) 分类id 
    参数: $single(boolean) 是否取默认数据 
    返回值: 商品id(int) 
------------------------------------ */
function tep_get_pid_by_romaji($romaji, $categories_id = 0, $single = false) {
  global $languages_id;
  if (empty($language)){
    $language = $languages_id;
  }
  if ($single) {
    $queryString = "
        select pd.`products_id` 
        from " . TABLE_PRODUCTS . " p, 
             " . TABLE_PRODUCTS_DESCRIPTION . " pd,
             " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
        where p.products_id = pd.products_id
          and p.products_id = p2c.products_id
          and p2c.categories_id = '" . $categories_id. "'
          and pd.romaji = '" . $romaji . "' 
          and pd.language_id = '" . (int)$language . "' 
          and pd.site_id = '" . SITE_ID . "'" ;
    $product_query = tep_db_query($queryString);
    if (tep_db_num_rows($product_query)) {
      $product = tep_db_fetch_array($product_query);
      return $product['products_id'];
    } else {
      $queryString = "
          select pd.`products_id` 
          from " . TABLE_PRODUCTS . " p, 
               " . TABLE_PRODUCTS_DESCRIPTION . " pd,
               " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
          where p.products_id = pd.products_id
            and p.products_id = p2c.products_id
            and p2c.categories_id = '" . $categories_id. "'
            and pd.romaji = '" . $romaji . "' 
            and pd.language_id = '" . (int)$language . "' 
            and pd.site_id = '0'" ;
      $product_query = tep_db_query($queryString);
      $product = tep_db_fetch_array($product_query);
      if ($product) {
        $queryString = "
            select pd.`romaji` 
            from " . TABLE_PRODUCTS . " p, 
                 " . TABLE_PRODUCTS_DESCRIPTION . " pd,
                 " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
            where p.products_id = pd.products_id
              and p.products_id = p2c.products_id
              and p2c.categories_id = '" . $categories_id. "'
              and pd.products_id = '" . $product['products_id'] . "' 
              and pd.language_id = '" . (int)$language . "' 
              and pd.site_id = '" . SITE_ID . "'" ;
        $or_product_query = tep_db_query($queryString); 
        $or_product_res = tep_db_fetch_array($or_product_query); 
        if ($or_product_res) {
          if ($or_product_res['romaji'] != $romaji) {
            return 0; 
          }
        }
      }
      return $product['products_id'];
    }
  } else {
  $queryString = "
      select pd.`products_id` 
      from " . TABLE_PRODUCTS . " p, 
           " . TABLE_PRODUCTS_DESCRIPTION . " pd,
           " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c
      where p.products_id = pd.products_id
        and p.products_id = p2c.products_id
        and p2c.categories_id = '" . $categories_id. "'
        and pd.romaji = '" . $romaji . "' 
        and pd.language_id = '" . (int)$language . "' 
        and (pd.site_id = '" . SITE_ID . "' or pd.site_id = '0')
      order by pd.site_id DESC" ;
  $product_query = tep_db_query($queryString);
  $product       = tep_db_fetch_array($product_query);
  return $product['products_id'];
  }
}

/* -------------------------------------
    功能: 根据分类的罗马字获取分类id 
    参数: $cname(string) 罗马字 
    参数: $parent_id(int) 父分类id 
    返回值: 分类id(string) 
------------------------------------ */
function tep_get_cpath_by_cname($cname, $parent_id = 0)
{
  global $languages_id;
  if (empty($language)){
    $language = $languages_id;
  }
  $queryString = "
      select cd.`categories_id` 
      from " .  TABLE_CATEGORIES . " c, " .  TABLE_CATEGORIES_DESCRIPTION . " cd
      where c.categories_id = cd.categories_id
        and c.parent_id = '".$parent_id."'
        and cd.romaji = '" . $cname .  "' 
        and cd.language_id = '" . (int)$language . "' 
        and (cd.site_id = '".SITE_ID."' or cd.site_id = '0')
      order by cd.site_id DESC" ;
  $category_query = tep_db_query($queryString);
  $category = tep_db_fetch_array($category_query);
  return $category['categories_id'];
}

/* -------------------------------------
    功能: 根据商品id获取所在的分类信息 
    参数: $pid(int) 商品id 
    参数: $romaji(boolean) 是否返回罗马字 
    返回值: 分类信息(array) 
------------------------------------ */
function tep_get_categories_by_pid($pid,$romaji=true)
{
  static $romaji_arr = array();
  $arr = array();
  
  $p_parent = tep_get_categories_by_products_id($pid);
  if (!isset($p_parent[0])) {
    forward404();
    exit('no categories');
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

/* -------------------------------------
    功能: 根据商品id获取其直接关联的分类id
    参数: $pid(int) 商品id 
    返回值: 分类信息(array) 
------------------------------------ */
function tep_get_categories_by_products_id($pid){
  $carr = array();
  $query = tep_db_query("select * from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
  while($p2c = tep_db_fetch_array($query)){
    $carr[] = $p2c['categories_id'];
  }
  
  if (!isset($carr[0])) {
    $carr[] = 0; 
  }
  return $carr;
}
/* -------------------------------------
    功能: 根据商品id获取其罗马字
    参数: $id(int) 商品id 
    返回值: 罗马字(string/int) 
------------------------------------ */
function tep_get_romaji_by_pid($id)
{
  $p = tep_db_fetch_array(tep_db_query("
        select * 
        from ".TABLE_PRODUCTS_DESCRIPTION." 
        where products_id='".$id."'
          and (site_id='".SITE_ID."' or site_id='0')
        order by site_id desc
  "));
  if ($p['romaji']) {
    return $p['romaji'];
  } else {
    return $id;
  }
}

/* -------------------------------------
    功能: 获得商品的乘积率
    参数: $pid(int) 商品id 
    返回值: 商品的乘积率(string) 
------------------------------------ */
function tep_get_products_rate($pid) {
  $n = str_replace(',','',tep_get_full_count_in_order2(1, $pid));
  preg_match_all('/(\d+)/',$n,$out);
  return $out[1][0];
}
  
/* -------------------------------------
    功能: 判断url里是否有指定字符 
    参数: $url(string) url 
    返回值: 是否符合规则(string/boolean) 
------------------------------------ */
function tep_get_google_adsense_adurl($url) {
  if ( preg_match('/from=adwords/',$url)) {
    return '1';
  } else {
    
    return false;
  }
}

/* -------------------------------------
    功能: 子域名解析专用 
    参数: 无 
    返回值: 无 
------------------------------------ */
function tep_parseURI()
{
  if (defined('URL_SUB_SITE_ENABLED') && URL_SUB_SITE_ENABLED) {
    //处理分页,先把分页参数从参数中除去
    if (preg_match("/page-(\d+)/",$_SERVER["REQUEST_URI"],$pagenum)){
      $_SERVER["REQUEST_URI"] = substr($_SERVER["REQUEST_URI"],0,strpos($_SERVER["REQUEST_URI"],"page-"));
      $_GET['page'] = $pagenum[1];
    }
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
                         "firstFolder"  => "/^\/[^\.\/]+\/?$/",        //   /abc(/)
                         "secondFolder" => '/^\/[^\.\/]+\/[^\.\/]+\/?$/',              //   /asb/xcv(/)
                         "thirdFolder"  => '/^\/[^\.\/]+\/[^\.\/]+\/[^\.\/]+\/?$/',              //   /asb/xcv(/)
                         "product"      => '/\.html$/'                    //   /asd/xcv/xcv.html  /zxv.html /xcv/xcv/xc.html
                         );
    foreach ($rewriteRule as $ruler=>$value){
      if (preg_match($value, $subSiteUri)) {
        $router = $ruler;
      }
    }
    if ((defined('SID_SYMBOL')) && SID_SYMBOL) {
      $i_pos = strpos($_SERVER['REQUEST_URI'], '/?sid=');
    } else {
      $i_pos = strpos($_SERVER['REQUEST_URI'], '/?cmd=');
    }
    if ($i_pos !== false) {
      $router = 'x'; 
    }
    if(isset($_GET['cName'])){
      if(preg_match("/\//",urldecode($_GET['cName']))){
        $temp_cname = str_replace('/','',urldecode($_GET['cName']));
      }else{
        $temp_cname = urldecode($_GET['cName']);
      }
      $firstId = tep_get_cpath_by_cname($temp_cname);
      unset($temp_cname);
      $_GET['cPath'] = $firstId;
    }
    /* -----------------------------------------------------
    case 'firstFolder' 一级分类
    case 'secondFolder' 二级分类
    case 'product' 产品页
    case 'x' 其他
    -----------------------------------------------------*/
    switch($router){
    case 'firstFolder':
      $firstFolder = substr($subSiteUri,1);
      if(substr($firstFolder,-1)=='/'){
        $firstFolder = substr($firstFolder,0,-1);
      }
      $secondId = tep_get_cpath_by_cname(urldecode($firstFolder), $firstId);
      if ($secondId == 0) {
        forward404();
      }
      $_GET['cPath'] = join('_',array($firstId,$secondId));
      break;
    case 'secondFolder':
      $secondFolder = substr($subSiteUri,1);
      $folder_arr = explode('/', $secondFolder); 
      $secondId = tep_get_cpath_by_cname(urldecode($folder_arr[0]), $firstId);
      $thirdId  = tep_get_cpath_by_cname(urldecode($folder_arr[1]), $secondId); 
      if ($thirdId == 0) {
        forward404();
      }
      $_GET['cPath'] = join('_',array($firstId,$secondId,$thirdId));
      break;
    case 'product':
      $tmpArray = explode('/',$subSiteUri);
      $tmpArray2 = array();
      $tmpArray3 = explode('.',$_SERVER['HTTP_HOST']);
      $firstId = tep_get_cpath_by_cname(urldecode($tmpArray3[0]));

      foreach ($tmpArray as $k => $v) {
        if ($v) {
          if ($k == count($tmpArray)-1) {
            $pid = tep_get_pid_by_romaji( urldecode(substr($v,0,-5)), $tmpArray2[count($tmpArray2)-1]?$tmpArray2[count($tmpArray2)-1]:$firstId);
          } else {
            $cid = tep_get_cpath_by_cname(urldecode($v),
                $tmpArray2[count($tmpArray2)-1]?$tmpArray2[count($tmpArray2)-1]:$firstId
                );
            if ($cid) {
              $tmpArray2[] = $cid;
            } else {
              forward404();
            }
          }
        }
      }
      $_GET['products_id'] = $pid;
      break;
      case 'x':
        if(basename($_SERVER['REQUEST_URI']) != FILENAME_SHOPPING_CART && $_SERVER['REQUEST_URI'] != '/'){
          forward404();
        }
        break;
    }
  } else {
    if (preg_match("/page-(\d+)/",$_SERVER["REQUEST_URI"],$pagenum)){
      $_SERVER["REQUEST_URI"] = substr($_SERVER["REQUEST_URI"],0,strpos($_SERVER["REQUEST_URI"],"page-"));
      $_GET['page'] = $pagenum[1];
    }
    $subSiteUri = $_SERVER['REQUEST_URI'];
    $g_pos = strpos($_SERVER['REQUEST_URI'], '?'); 
    if ($g_pos !== false) {
      $subSiteUri = substr($_SERVER['REQUEST_URI'], 0, $g_pos);
    }
    $router = 'x';
    $rewriteRule = array(
                         "firstFolder"  => "/^\/[^\.\/]+\/?$/",
                         "secondFolder" => '/^\/[^\.\/]+\/[^\/]+\/?$/',
                         "thirdFolder"  => '/^\/[^\.\/]+\/[^\/]+\/[^\/]+\/?$/',
                         "product"      => '/\.html$/'
                         );
    foreach ($rewriteRule as $ruler=>$value){
      if (preg_match($value, $subSiteUri)) {
        $router = $ruler;
      }
    }
    $tmp_router = $router; 
    if ((defined('SID_SYMBOL')) && SID_SYMBOL) {
      $i_pos = strpos($_SERVER['REQUEST_URI'], '/?sid=');
    } else {
      $i_pos = strpos($_SERVER['REQUEST_URI'], '/?cmd=');
    }
    if ($i_pos !== false) {
      $router = 'x'; 
    }
    if ((defined('ROUTER_DIRECTION')) && ROUTER_DIRECTION) {
      if (($_SERVER['PHP_SELF'] == '/index.php') && $tmp_router != 'x') {
        $router = $tmp_router; 
      }
    }
    /* -----------------------------------------------------
    case 'firstFolder' 一级分类
    case 'secondFolder' 二级分类
    case 'thirdFolder' 三级分类
    case 'product' 产品页
    case 'x' 其他
    -----------------------------------------------------*/
    switch($router){
    case 'firstFolder':
    case 'secondFolder':
    case 'thirdFolder':
      $tmpArray = explode('/',$subSiteUri);
      $tmpArray2 = array();

      foreach ($tmpArray as $v) {
        if ($v) {
          $cid = tep_get_cpath_by_cname(urldecode($v),$tmpArray2[count($tmpArray2)-1]);
          if ($cid) {
            $tmpArray2[] = $cid;
          } else {
            forward404();
          }
        }
      }

      $_GET['cPath'] = implode('_', $tmpArray2);
      break;
    case 'product':
      $tmpArray = explode('/',$subSiteUri);
      $tmpArray2 = array();

      foreach ($tmpArray as $k => $v) {
        if ($v) {
          if ($k == count($tmpArray)-1) {
            if ((defined('WHETHER_START')) && WHETHER_START) {
              $pid = tep_get_pid_by_romaji( urldecode(substr($v,0,-5)), $tmpArray2[count($tmpArray2)-1]?$tmpArray2[count($tmpArray2)-1]:0, true);
            } else {
              $pid = tep_get_pid_by_romaji( urldecode(substr($v,0,-5)), $tmpArray2[count($tmpArray2)-1]?$tmpArray2[count($tmpArray2)-1]:0);
            }
          } else {
            $cid = tep_get_cpath_by_cname(urldecode($v),$tmpArray2[count($tmpArray2)-1]);
            if ($cid) {
              $tmpArray2[] = $cid;
            } else {
              forward404();
            }
          }
        }
      }
      $_GET['products_id'] = $pid;
      break;
    case 'x':
      break;
    }
  }
}

/* -------------------------------------
    功能: 根据分类数组获得一级分类的信息 
    参数: $cPath_array(array) 分类信息 
    返回值: 一级分类信息(array) 
------------------------------------ */
function tep_get_top_category_by_cpath($cPath_array)
{
  if (!empty($cPath_array)) {
    $top_category_query = tep_db_query("select * from ".TABLE_CATEGORIES_DESCRIPTION." where site_id = '0' or site_id = '".SITE_ID."' and categories_id = '".$cPath_array[0]."' order by site_id desc limit 1"); 
    return tep_db_fetch_array($top_category_query); 
  } 
  return '';
}

/* -------------------------------------
    功能: 判断该分类状态是否为黑色 
    参数: $category_id(int) 分类id 
    返回值: 是否黑色(boolean) 
------------------------------------ */
function tep_check_black_category($category_id)
{
  $category_query = tep_db_query("select * from ".TABLE_CATEGORIES_DESCRIPTION." where (site_id = '0' or site_id = '".SITE_ID."') and categories_id = '".(int)$category_id."' order by site_id desc limit 1");
  $category_res = tep_db_fetch_array($category_query);
  
  if ($category_res) {
    if ($category_res['categories_status'] == 3) {
      return true; 
    }
  }
  return false;
}

/* -------------------------------------
    功能: 判断该商品是否为黑色 
    参数: $products_id(int) 商品id 
    返回值: 是否黑色(boolean) 
------------------------------------ */
function tep_check_black_product($products_id)
{
  $product_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where (site_id = '0' or site_id = '".SITE_ID."') and products_id = '".(int)$products_id."' order by site_id desc limit 1");
  $product_res = tep_db_fetch_array($product_query);
  if ($product_res) {
    if ($product_res['products_status'] == 3) {
      return true; 
    }
  }
  return false;
}

/* -------------------------------------
    功能: 判断该商品是否为显示 
    参数: $products_id(int) 商品id 
    返回值: 是否显示(boolean) 
------------------------------------ */
function tep_whether_show_products($products_id)
{
  $product_query = tep_db_query("select products_status from ".TABLE_PRODUCTS_DESCRIPTION." where (site_id ='0' or site_id = '".SITE_ID."') and products_id = '".(int)$products_id."' order by site_id desc limit 1");
  $product_res = tep_db_fetch_array($product_query);
  if ($product_res) {
    if ($product_res['products_status']  == 3) {
      return true;
    } else {
      $pro_category_query = tep_db_query("select c.parent_id, cd.categories_id, cd.categories_status from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd , ".TABLE_PRODUCTS_TO_CATEGORIES." p2c where c.categories_id = cd.categories_id and cd.categories_id = p2c.categories_id and p2c.products_id = '".(int)$products_id."' and (cd.site_id ='0' or cd.site_id = '".SITE_ID."') order by cd.site_id desc limit 1");
      $pro_category_res = tep_db_fetch_array($pro_category_query);
      if ($pro_category_res) {
        if ($pro_category_res['categories_status'] == 3) {
    return true;
        } else {
          $parent_category_query = tep_db_query("select c.parent_id, cd.categories_status from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and c.categories_id = '".$pro_category_res['parent_id']."' and (cd.site_id = '0' or cd.site_id = '".SITE_ID."') order by cd.site_id desc limit 1"); 
          $parent_category_res = tep_db_fetch_array($parent_category_query); 
          if ($parent_category_res) {
            if ($parent_category_res['categories_status'] == 3) {
              return true; 
            } else {
              $parent_parent_category_query = tep_db_query("select c.parent_id, cd.categories_status from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and c.categories_id = '".$parent_category_res['parent_id']."' and (cd.site_id = '0' or cd.site_id = '".SITE_ID."') order by cd.site_id desc limit 1"); 
              $parent_parent_category_res = tep_db_fetch_array($parent_parent_category_query); 
              if ($parent_parent_category_res['categories_status'] == 3) {
                return true; 
              }
            }
          }
        }
      }
    }
  }
  return false;
}
  
/* -------------------------------------
    功能: 获得网站的罗马字 
    参数: $id(int) 网站id 
    返回值: 罗马字(string) 
------------------------------------ */
function tep_get_site_romaji_by_id($id){
    if ($id == 0){
      return '';
    }
    $site_query = tep_db_query("
        select * 
        from sites 
        where id = '".intval($id)."'
    ");
    $site = tep_db_fetch_array($site_query);
    if (isset($site['romaji'])) {
      return $site['romaji'];
    } else {
      return '';
    }
}

/* -------------------------------------
    功能: 获得提醒商品 
    参数: $pid(array) 商品id 
    返回值: 提醒商品的id(array) 
------------------------------------ */
function tep_get_cart_products($pid){
  if (empty($pid)) {
    $pid = array(0); 
  }
  $raw = "
    select distinct(p2c.products_id)
    from products_to_tags p2t,products_to_carttag p2c, products p, products p2
    where p2t.products_id in (".join(',',$pid).")
      and p2c.tags_id = p2t.tags_id
      and p.products_bflag = p2c.buyflag
      and p.products_id = p2t.products_id
      and p2.products_id = p2c.products_id
      and p2.products_cartflag = '1'
      and p2c.products_id not in (".join(',',$pid).")
      and p2.products_real_quantity + p2.products_virtual_quantity > p2.products_cart_min
    order by p2.products_cartorder
    limit ".CART_TAG_PRODUCTS_MAX."
  ";
  $query = tep_db_query($raw);
  $arr = array();
  while($p = tep_db_fetch_array($query)){
    $arr[] = $p['products_id'];
  }
  return $arr;
}

/* -------------------------------------
    功能: 根据shopping_cart中商品集取到商品id数组 
    参数: $products(array) 商品信息 
    返回值: 商品的id(array) 
------------------------------------ */
function tep_get_products_by_shopiing_cart($products){
  $arr = array();
  foreach ($products as $p) {
    $arr[] = (int)$p['id'];
  }
  return $arr;
}

/* -------------------------------------
    功能: 根据shopping_cart中其他商品 
    参数: $pid(array) 商品id
    参数: $cid_arr(array) 分类id
    返回值: 商品的id(array) 
------------------------------------ */
function tep_get_cart_other_products($pid, $cid_arr){
  $pid_str = join(',', $pid);
  if (empty($pid_str)) {
    $pid_str = '0'; 
  }
  $raw = "
    select distinct(p2c.products_id)
    from products_to_tags p2t,products_to_carttag p2c, products p, products p2
    where p2t.products_id in (".$pid_str.")
      and p2c.tags_id = p2t.tags_id
      and p.products_bflag = p2c.buyflag
      and p.products_id = p2t.products_id
      and p2.products_id = p2c.products_id
      and p2.products_cartflag = '1'
      and p2c.products_id not in (".$pid_str.")
      and p2.products_real_quantity + p2.products_virtual_quantity > p2.products_cart_min
    order by p2.products_cartorder
    limit ".CART_TAG_PRODUCTS_MAX."
  ";
  $query = tep_db_query($raw);
  $arr = array();
  while($p = tep_db_fetch_array($query)){
    $exists_pro_query = tep_db_query("select * from ".TABLE_PRODUCTS_TO_CATEGORIES." where categories_id in (".implode(',', $cid_arr).") and products_id = '".$p['products_id']."'"); 
    if (tep_db_num_rows($exists_pro_query)) {
      $arr[] = $p['products_id'];
    }
  }
  return $arr;
}
/* -------------------------------------
    功能: 格式化输出价格信息 
    参数: $str(string) 数值信息
    返回值: 处理后的数值信息(string) 
------------------------------------ */
  function tep_display_attention_1_32($str) {
    $str2 = $str;
    if (strlen($str) > 8) {
      $str = preg_replace('/00000000$/', '億', $str);
    } else {
      $str = preg_replace('/0000$/', '万', $str);
    }
    if (preg_match('/^(\d*)([^\d]*)$/', $str, $out)) {
      return number_format($out[1]).'（'.number_format($str2).'）'.$out[2];
    } else {
      return number_format($str);
    }
  }

/* -------------------------------------
    功能: 格式化输出价格信息 
    参数: $str(string) 数值信息
    返回值: 处理后的数值信息(string) 
------------------------------------ */
  function tep_display_attention_1_3($str) {
    $str2 = $str;
    if (strlen($str) > 8) {
      $ret .= floor(substr($str,0,strlen($str)-8)) . '億';
    }
    if (intval(substr($str,-8)) >= 10000000) {
      $tmp = substr();
      $ret .= intval(substr($str,-8)/10000000) . '千';
      $a = true;
    }
    if (intval(substr($str,-7)) >= 10000) {
      $ret .= intval(substr($str,-7)/10000) . '万';
    } else if ($str > 10000 && $a) {
      $ret .= '万';
    }
    if (intval(substr($str,-4)) >= 1000) {
      $ret .= intval(substr($str,-4)/1000) . '千';
    }
    if (intval(substr($str,-3))) {
      $ret .= intval(substr($str,-3));
    }
    if(intval($str) >= 1000){
      return $ret.'（'.number_format($str2).'）';
    }else{
      return $ret;
    }
  }
  
/* -------------------------------------
    功能: 分割评论的描述(当全是数字时，每24个字符一换行) 
    参数: $desc(string) 评论描述
    返回值: 处理后的描述(string) 
------------------------------------ */
function  tep_show_review_des($desc) {
  if (preg_match('/^[0-9]+$/', $desc)) {
    $i = 1; 
    $return_str = ''; 
    for ($u=0; $u<mb_strlen($desc, 'UTF-8'); $u++) {
      $return_str .= $desc[$u]; 
      if ($i % 24 == 0) {
        $return_str .= '<br>'; 
      }
      $i++; 
    }
    return $return_str; 
  } else {
    return $desc; 
  }
}

/* -------------------------------------
    功能: 获得该分类所关联的所有分类 
    参数: $categories_id(int) 分类id
    参数: $languages_id(int) 语言id
    返回值: 关联分类数组(array) 
------------------------------------ */
function tep_other_get_categories_id_by_parent_id($categories_id, $languages_id = 4) {
  $arr = array();
  $categories = tep_get_categories_by_parent_id($categories_id, $languages_id);
  foreach ($categories as $c){
    $arr[] = $c['categories_id'];
    $subcategories = tep_get_categories_by_parent_id($c['categories_id'], $languages_id);
    foreach ($subcategories as $sc) {
      $arr[] = $sc['categories_id']; 
    }
  }
  return $arr;
}

/* -------------------------------------
    功能: 判断购物车里是否只有买取商品 
    参数: 无
    返回值: 是否只有买取商品(boolean) 
------------------------------------ */
function tep_only_buy_product(){
  global $cart;
  foreach($cart->get_products() as $p){
    if (tep_get_bflag_by_product_id($p['id']) == 0) {
      return false;
    }
  }
  return true;
}

/* -------------------------------------
    功能: 判断购物车里是否只有贩卖商品 
    参数: 无
    返回值: 是否只有贩卖商品(boolean) 
------------------------------------ */
function tep_only_sell_product(){
  global $cart;
  foreach($cart->get_products() as $value){
    if ($value['bflag'] == '1') {
      return false;
    }
  }
  return true;

}
  
/* -------------------------------------
    功能: 获得该分类下的所有子节点 
    参数: $parent_id(int) 父节点id 
    返回值: 子节点(array) 
------------------------------------ */
function tep_get_all_subcategories($parent_id) 
{
    $subcategories_array = array();
    $subcategories_array[] = $parent_id;
    $subcategories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$parent_id . "'");
    while ($subcategories = tep_db_fetch_array($subcategories_query)) {
      $subcategories_array[] = $subcategories['categories_id'];
      $sub_subcategories_query = tep_db_query("select categories_id from " .  TABLE_CATEGORIES . " where parent_id = '" .  (int)$subcategories['categories_id'] . "'");
      while ($sub_subcategories = tep_db_fetch_array($sub_subcategories_query)) {
        $subcategories_array[] = $sub_subcategories['categories_id'];
      }
    }
    return $subcategories_array;
}

/* -------------------------------------
    功能: 判断分类的罗马字和给的罗马字是否一样 
    参数: $cPath(string) 分类路径 
    参数: $cName(string) 分类罗马字 
    返回值: 是否一样 (boolean) 
------------------------------------ */
function tep_check_exists_category($cPath, $cName)
{
  $cpath_arr = explode('_', $cPath);
  $cname_arr = explode('/', $cName);
  foreach ($cpath_arr as $key => $value) {
    $category_raw = tep_db_query("select * from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id = '".$value."' and site_id = '".SITE_ID."'"); 
    $category_res = tep_db_fetch_array($category_raw); 
    if ($category_res) {
      if (($category_res['romaji'] != $cname_arr[$key]) && isset($cname_arr[$key])) {
        return true; 
      }
    }
  }
  return false;
}

/* -------------------------------------
    功能: 获得指定长度的大小写字母的随机字符串 
    参数: $length(int) 字符长度 
    返回值: 随机字符串(string) 
------------------------------------ */
function tep_get_random_ac_code($length = 10)
{
  $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
  $return_str = '';

  for ($i = 1; $i <= $length; $i++) {
   $q = floor(tep_rand(1,62));
   $return_str .= $letters[$q];
  }
  return $return_str;
}

/* -------------------------------------
    功能: 判断除了指定客户以外的该邮箱是否存在 
    参数: $email_address(string) 邮箱地址 
    参数: $customer_id(int) 客户id 
    参数: $ctype(int) 类型 
    返回值: 是否存在(boolean) 
------------------------------------ */
function tep_check_exists_cu_email($email_address, $customer_id, $ctype)
{
   $customers_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id != '".$customer_id."' and site_id = '".SITE_ID."' and customers_email_address = '".$email_address."'");
   if (tep_db_num_rows($customers_raw)) {
     return true; 
   }
   return false;
}

/* -------------------------------------
    功能: 根据faq的罗马字获得其分类id 
    参数: $cname(string) 罗马字 
    参数: $parent_id(int) 父结点 
    返回值: 分类id(int) 
------------------------------------ */
function tep_get_faq_cpath_by_cname($cname, $parent_id = 0)
{
  $queryString = "
      select cd.`faq_category_id` 
      from " .  TABLE_FAQ_CATEGORIES . " c, " .  TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
      where c.id = cd.faq_category_id
        and c.parent_id = '".$parent_id."'
        and cd.romaji = '" . $cname .  "' 
        and cd.site_id = '".SITE_ID."'" ;
  $category_query = tep_db_query($queryString);
  if(tep_db_num_rows($category_query)){
    $category = tep_db_fetch_array($category_query);
    return $category['faq_category_id'];
  }else{
    $queryString = "
      select cd.`faq_category_id` 
      from " .  TABLE_FAQ_CATEGORIES . " c, " .  TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
      where c.id = cd.faq_category_id
        and c.parent_id = '".$parent_id."'
        and cd.romaji = '" . $cname .  "' 
        and cd.site_id = '0'" ;
    $category_query = tep_db_query($queryString);
    $category = tep_db_fetch_array($category_query);
    if($category){
      $queryString = "
        select cd.`faq_category_id` 
          from " .  TABLE_FAQ_CATEGORIES . " c, " .  TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
          where c.id = cd.faq_category_id
          and c.parent_id = '".$parent_id."'
          and c.id = '".$category['faq_category_id']."' 
          and cd.site_id = '".SITE_ID."'" ;
      $query = tep_db_query($queryString);
      $res = tep_db_fetch_array($query);
      if($res){
        if($res['romaji']!=$cname){
          return false;
        }
      }
    }
    return $category['faq_category_id'];

  }
}

/* -------------------------------------
    功能: 获得faq的分类信息 
    参数: $c_id(int) 分类id 
    返回值: 分类信息(array) 
------------------------------------ */
function tep_get_faq_category_info($c_id){
  $sql = "select * from ".TABLE_FAQ_CATEGORIES." fc ,"
         .TABLE_FAQ_CATEGORIES_DESCRIPTION." fcd 
         where fc.id = fcd.faq_category_id 
         and fc.id = '".$c_id."' 
         and (fcd.site_id = '".SITE_ID."' or fcd.site_id = '0')
         order by site_id DESC";
  $query = tep_db_query($sql);
  return tep_db_fetch_array($query);
}

/* -------------------------------------
    功能: 获得faq的问题信息 
    参数: $q_id(int) 问题id 
    返回值: 问题信息(array) 
------------------------------------ */
function tep_get_faq_question_info($q_id){
  $sql = "select * from ".TABLE_FAQ_QUESTION." fq ,"
         .TABLE_FAQ_QUESTION_DESCRIPTION." fqd 
         where fq.id = fqd.faq_question_id 
         and fq.id = '".$q_id."' 
         and (fqd.site_id = '".SITE_ID."' or fqd.site_id = '0')
         order by site_id DESC";
  $query = tep_db_query($sql);
  return tep_db_fetch_array($query);
}

/* -------------------------------------
    功能: 根据罗马字获得faq问题的id 
    参数: $qname(string) 问题的罗马字 
    参数: $qpath(int) 分类id 
    返回值: 问题id(int) 
------------------------------------ */
function tep_get_faq_qid_by_qname($qname,$qpath){
   $sql = "select * from ".TABLE_FAQ_QUESTION." fq,
        ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd,
        ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
        WHERE fq.id = fqd.faq_question_id  
        and fq2c.faq_question_id = fqd.faq_question_id 
        and fq2c.faq_category_id = '".$qpath."'
        and fqd.romaji = '".$qname."' 
        and fqd.site_id = '".SITE_ID."'" ;
 $query = tep_db_query($sql);
 if(tep_db_num_rows($query)){
   $question = tep_db_fetch_array($query);
   return $question['faq_question_id'];
 }else{
   $sql = "select * from ".TABLE_FAQ_QUESTION." fq,
        ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd,
        ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
        WHERE fq.id = fqd.faq_question_id  
        and fq2c.faq_question_id = fqd.faq_question_id 
        and fq2c.faq_category_id = '".$qpath."'
        and fqd.romaji = '".$qname."' 
        and fqd.site_id = '0'" ;
   $query = tep_db_query($sql);
   $question = tep_db_fetch_array($query);
   if($question){
     $sql = "select * from ".TABLE_FAQ_QUESTION." fq,
        ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd,
        ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
        WHERE fq.id = fqd.faq_question_id  
        and fq2c.faq_question_id = fqd.faq_question_id 
        and fq2c.faq_category_id = '".$qpath."'
        and fqd.faq_question_id = '".$question['faq_question_id']."' 
        and fqd.site_id = '".SITE_ID."'" ;
     $query = tep_db_query($sql);
     $res = tep_db_fetch_array($query);
     if($res){
       if($res['romaji'] != $qname){
         return false;
       }
     }
   }
   return $question['faq_question_id'];
 }
}

/* -------------------------------------
    功能: 获取faq问题于分类关联表的信息 
    参数: $qid(int) 问题id 
    参数: $cath(int) 分类id 
    返回值: 信息(mixed) 
------------------------------------ */
function tep_question_in_category_by_id($qid,$cid){
  $pro_to_ca_query = tep_db_query("select * from ".TABLE_FAQ_QUESTION_TO_CATEGORIES." 
      where faq_category_id = '".$cid."'
      and faq_question_id='".$qid."'");
  return tep_db_fetch_array($pro_to_ca_query);
}

/* -------------------------------------
    功能: 随机获取指定条数的评论信息 
    参数: $query(string) 评论的sql 
    参数: $limit_num(int) 条数 
    返回值: 评论信息(array) 
------------------------------------ */
function tep_reviews_random_select($query, $limit_num) {
  $random_product = array();
  $random_query = tep_db_query($query);
  $num_rows = tep_db_num_rows($random_query);
  if ($num_rows > 0) {
    $random_row = tep_rand(0, ($num_rows - 1));
    if ($num_rows > $limit_num) {
      if (($num_rows - $random_row) < $limit_num) {
        $random_row = $num_rows - $limit_num; 
      }
    }
    for($i=0; $i<$limit_num; $i++) {
      tep_db_data_seek($random_query, $random_row);
      $random_product[] = tep_db_fetch_array($random_query);
      $random_row++; 
    }
  }

  return $random_product;
}

/* -------------------------------------
    功能: 订单的确认支付时间或者订单等待标识更新 
    参数: $oID(int) 订单id 
    参数: $status(int) 状态id 
    返回值: 无 
------------------------------------ */
function tep_order_status_change($oID,$status){
  require_once("oa/HM_Form.php");
  require_once("oa/HM_Group.php");
  require_once("oa/HM_Item_Checkbox.php");
  require_once("oa/HM_Item_Autocalculate.php");
  require_once("oa/HM_Item_Text.php");
  require_once("oa/HM_Item_Specialbank.php");
  require_once("oa/HM_Item_Date.php");
  require_once("oa/HM_Item_Myname.php");
  $order_id = $oID;
  $formtype = tep_check_order_type($order_id);
  $payment_romaji = tep_get_payment_code_by_order_id($order_id); 
  $oa_form_sql = "select * from ".TABLE_OA_FORM." where formtype = '".$formtype."' and payment_romaji = '".$payment_romaji."'";
  
  if ($status == '9') {
    tep_db_query("update `".TABLE_ORDERS."` set `confirm_payment_time` = '".date('Y-m-d H:i:s', time())."' where `orders_id` = '".$oID."'");
  }
  if($status == '17'){
    tep_db_query("update `".TABLE_ORDERS."` set `orders_wait_flag` = '1' where `orders_id` = '".$oID."'");
  }

  $form = tep_db_fetch_object(tep_db_query($oa_form_sql), "HM_Form");
  //如果存在，把每个元素找出来，看是否有自动更新
  if($form){
    $form->loadOrderValue($order_id);
    foreach ($form->groups as $group){
      foreach ($group->items as $item){
        if ($item->instance->status == $status){
          $item->instance->statusChange($order_id,$form->id,$group->id,$item->id);
          continue;
        }
      }
    }}
  }

/* -------------------------------------
    功能: 判断订单的类型 
    参数: $oID(int) 订单id 
    返回值: 订单的类型(int 注：1为贩卖 2为买取 3为混合) 
------------------------------------ */
  function tep_check_order_type($oID)
  {
    $sql = "  SELECT avg( products_bflag ) bflag FROM orders_products op, products p  WHERE 1 AND p.products_id = op.products_id AND op.orders_id = '".$oID."'";

    $avg  = tep_db_fetch_array(tep_db_query($sql));
    $avg = $avg['bflag'];

    if($avg == 0){
      return 1;
    }
    if($avg == 1){
      return 2;
    }
    return 3;

  }

/* -------------------------------------
    功能: 获得该订单的支付方法 
    参数: $oID(int) 订单id 
    返回值: 支付方法(string) 
------------------------------------ */
  function tep_get_payment_code_by_order_id($oID)
  {
    $orders_raw = tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$oID."'");
    $orders_res = tep_db_fetch_array($orders_raw);
    return $orders_res['payment_method'];
  }

/* -------------------------------------
    功能: 获取mysql的fetch对象 
    参数: $result(object) 结果集 
    参数: $classname(string) 类名字 
    返回值: 对象信息(obj) 
------------------------------------ */
  function tep_db_fetch_object($result, $classname = '')
  {
    if (empty($classname)) {
      return mysql_fetch_object($result); 
    }
    return mysql_fetch_object($result, $classname); 
  }

/* -------------------------------------
    功能: 生成订单id最后两位 
    参数: 无 
    返回值: 最后两位信息(string) 
------------------------------------ */
function tep_get_order_end_num() 
{
  $last_orders_raw = tep_db_query("select * from ".TABLE_ORDERS." order by orders_id desc limit 1"); 
  $last_orders = tep_db_fetch_array($last_orders_raw);
  
  if ($last_orders) {
    $last_orders_num = substr($last_orders['orders_id'], -2); 
    
    if (((int)$last_orders_num < 99) && ((int)$last_orders_num > 0)) {
      $next_orders_num = (int)$last_orders_num + 1; 
    } else {
      $next_orders_num = 1; 
    }
    return sprintf('%02d', $next_orders_num); 
  }
  
  return '01';
}

/* -------------------------------------
    功能: 生成预约商品的确保时间下拉列表 
    参数: $product_id(int) 商品id 
    返回值: 列表的html(string) 
------------------------------------ */
  function tep_get_torihiki_select_by_pre_products($product_id)
  {
    $torihiki_list = array();
    $torihiki_array = tep_get_torihiki_by_pre_products($product_id);
    foreach($torihiki_array as $torihiki){
      $torihiki_list[] = array('id' => $torihiki,
        'text' => $torihiki
      );
    }
    if (!isset($torihikihouhou)) $torihikihouhou=NULL;
    return tep_draw_pull_down_menu('ensure_deadline', $torihiki_list, $torihikihouhou);
  }
  
/* -------------------------------------
    功能: 生成预约商品的时间选择信息 
    参数: $product_id(int) 商品id 
    返回值: 时间选择信息(array/null) 
------------------------------------ */
  function tep_get_torihiki_by_pre_products($product_id)
  {
    $option_types = array();
    if ($product_id) {
      $sql = "select * from `" . TABLE_PRODUCTS . "` where products_id = '".  $product_id . "'";
    
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
 
/* -------------------------------------
    功能: 创建游客 
    参数: $email(string) 邮箱地址 
    参数: $last_anme(string) 姓  
    参数: $first_name(string) 名 
    返回值: 游客id(int) 
------------------------------------ */
function tep_create_tmp_guest($email, $last_name, $first_name)
{ 
  
  $NewPass = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
  $sql_data_array = array('customers_firstname' => $first_name,
                            'customers_lastname' => $last_name,
                            'customers_firstname_f' => '',
                            'customers_lastname_f' => '',
                            'customers_email_address' => $email,
                            'customers_telephone' => '',
                            'customers_newsletter' => '0',
                            'customers_password' => tep_encrypt_password($NewPass),
                            'customers_default_address_id' => 1,
                            'customers_guest_chk' => '1',
                            'send_mail_time' => time(),
                            'site_id' => SITE_ID,
                            'point' => '0');


    tep_db_perform(TABLE_CUSTOMERS, $sql_data_array);

    $customer_id = tep_db_insert_id();

    $sql_data_array = array('customers_id' => $customer_id,
                            'address_book_id' => 1,
                            'entry_firstname' => $first_name,
                            'entry_lastname' => $last_name,
                            'entry_firstname_f' => '',
                            'entry_lastname_f' => '',
                            'entry_street_address' => '',
                            'entry_postcode' => '',
                            'entry_city' => '',
                            'entry_country_id' => '107',
                            'entry_telephone' => '');

    tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);
    
    tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . tep_db_input($customer_id) . "', '0', now())");
    return $customer_id; 
}

/* -------------------------------------
    功能: 更新预约订单相关信息 
    参数: $orders_id(int) 订单id 
    返回值: 无 
------------------------------------ */
function preorders_updated($orders_id) {
  tep_db_query("update ".TABLE_PREORDERS." set language_id = ( select language_id from ".TABLE_PREORDERS_STATUS." where preorders_status.orders_status_id=preorders.orders_status ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_PREORDERS." set finished = ( select finished from ".TABLE_PREORDERS_STATUS." where preorders_status.orders_status_id=preorders.orders_status ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_PREORDERS." set orders_status_name = ( select orders_status_name from ".TABLE_PREORDERS_STATUS." where preorders_status.orders_status_id=preorders.orders_status ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_PREORDERS." set orders_status_image = ( select orders_status_image from ".TABLE_PREORDERS_STATUS." where preorders_status.orders_status_id=preorders.orders_status ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_PREORDERS_PRODUCTS." set torihiki_date = ( select torihiki_date from ".TABLE_PREORDERS." where preorders.orders_id=preorders_products.orders_id ) where orders_id='".$orders_id."'");
}
  
/* -------------------------------------
    功能: 创建预约订单 
    参数: $pInfo(array) 预约信息 
    参数: $preorder_id(int) 预约id 
    参数: $cid(int) 顾客id 
    参数: $tmp_cid(int) 游客id 
    参数: $exists_single(boolean) 是否有已有的名字 
    返回值: 无 
------------------------------------ */
function tep_create_preorder_info($pInfo, $preorder_id, $cid, $tmp_cid = null, $exists_single = false) 
{
   global $currency, $currencies, $payment_modules, $languages_id; 
   $is_active = 1; 
   if ($tmp_cid) {
     $is_active = 0; 
   }
   if ($tmp_cid) {
     $customers_id = $tmp_cid; 
   } else {
     $customers_id = $cid; 
   }
   $cpayment = $payment_modules->getModule($pInfo['pre_payment']); 
   $payment_method = $cpayment->title;
   //获取相应支付方式的默认预约订单状态
   $orders_status_id = get_configuration_by_site_id('MODULE_PAYMENT_'.strtoupper($pInfo['pre_payment']).'_PREORDER_STATUS_ID',SITE_ID);
   $orders_status = $orders_status_id != 0 ? $orders_status_id : DEFAULT_PREORDERS_STATUS_ID;
   $orders_status_raw = tep_db_query("select * from ".TABLE_PREORDERS_STATUS." where orders_status_id = '".$orders_status."'"); 
   $orders_status_res = tep_db_fetch_array($orders_status_raw);
   
   $orders_status_name = $orders_status_res['orders_status_name'];
   
   $customers_raw = tep_db_query("
      select c.customers_firstname, 
              c.customers_lastname, 
              c.customers_firstname_f, 
              c.customers_lastname_f, 
              c.customers_telephone, 
              c.customers_email_address, 
              ab.entry_company, 
              ab.entry_street_address, 
              ab.entry_suburb, 
              ab.entry_postcode, 
              ab.entry_city, 
              ab.entry_zone_id, 
              z.zone_name, 
              co.countries_id, 
              co.countries_name, 
              co.countries_iso_code_2, 
              co.countries_iso_code_3, 
              co.address_format_id, 
              ab.entry_state 
      from " . TABLE_CUSTOMERS . " c, " .  TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " co on (ab.entry_country_id = co.countries_id) 
      where c.customers_id = '" .  $customers_id . "' 
      and ab.customers_id = '" . $customers_id . "' 
      and c.customers_default_address_id = ab.address_book_id 
      and c.site_id = ".SITE_ID);
   $customers_res = tep_db_fetch_array($customers_raw);
  
   $shipping_address_query = tep_db_query("
      select ab.entry_firstname, ab.entry_lastname, ab.entry_firstname_f, ab.entry_lastname_f, ab.entry_telephone, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id, c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id, ab.entry_state 
      from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " c on (ab.entry_country_id = c.countries_id) 
      where ab.customers_id = '" . $customers_id . "' 
        and ab.address_book_id = '1'
  ");
  $shipping_address = tep_db_fetch_array($shipping_address_query);
  
  $billing_address_query = tep_db_query("
      select ab.entry_firstname, ab.entry_lastname, ab.entry_firstname_f, ab.entry_lastname_f, ab.entry_telephone, ab.entry_company, ab.entry_street_address, ab.entry_suburb, ab.entry_postcode, ab.entry_city, ab.entry_zone_id, z.zone_name, ab.entry_country_id, c.countries_id, c.countries_name, c.countries_iso_code_2, c.countries_iso_code_3, c.address_format_id, ab.entry_state 
      from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) left join " . TABLE_COUNTRIES . " c on (ab.entry_country_id = c.countries_id) 
      where ab.customers_id = '" . $customers_id . "' 
      and ab.address_book_id = '1'");
  $billing_address = tep_db_fetch_array($billing_address_query);
   
   $order_id = $preorder_id;
   
   $sql_data_array = array('orders_id' => $order_id,
                           'customers_id' => $customers_id, 
                           'customers_name' => ($exists_single)?tep_get_fullname($pInfo['firstname'],$pInfo['lastname']):tep_get_fullname($customers_res['customers_firstname'],
                             $customers_res['customers_lastname']), 
                           'customers_email_address' => $customers_res['customers_email_address'], 
                           'customers_street_address' => $customers_res['entry_street_address'], 
                           'customers_suburb' => $customers_res['entry_suburb'], 
                           'customers_city' => $customers_res['entry_city'],
                           'customers_postcode' => $customers_res['entry_postcode'], 
                           'customers_state' => ((tep_not_null($customers_res['entry_state']))?$customers_res['entry_state']:$customers_res['zone_name']), 
                           'customers_country' => $customers_res['countries_name'], 
                           'customers_telephone' => $customers_res['customers_telephone'],
                           'customers_address_format_id' => $customers_res['address_format_id'],
                           'payment_method' => $payment_method, 
                           'date_purchased'    => 'now()', 
                           'orders_status' => $orders_status,
                           'orders_status_name' => $orders_status_name, 
                           'currency' => $currency, 
                           'currency_value' =>
                           $currencies->currencies[$currency]['value'],
                           'site_id' => SITE_ID, 
                           'orders_ip' => $_SERVER['REMOTE_ADDR'], 
                           'orders_host_name'  => trim(strtolower(@gethostbyaddr($_SERVER['REMOTE_ADDR']))), 
                           'orders_user_agent' => $_SERVER['HTTP_USER_AGENT'], 
                           'orders_wait_flag'  => 1, 
                           'orders_http_accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'],
                           'code_fee' => 0, 
                           'is_active' => $is_active,
                           'delivery_name'  => tep_get_fullname($shipping_address['entry_firstname'],$shipping_address['entry_lastname']), 
                           'delivery_company' => $shipping_address['entry_company'],
                           'delivery_street_address' => $shipping_address['entry_street_address'],
                           'delivery_suburb'    => $shipping_address['entry_suburb'],
                           'delivery_city'      => $shipping_address['entry_city'], 
                           'delivery_postcode'  => $shipping_address['entry_postcode'], 
                           'delivery_state'     => ((tep_not_null($shipping_address['entry_state']))?$shipping_address['state']:$shipping_address['zone_name']),  
                           'delivery_country'   => $shipping_address['countries_name'],  
                           'delivery_telephone' => $shipping_address['entry_telephone'],   
                           'delivery_address_format_id' => $shipping_address['address_format_id'], 
                           'billing_name' => tep_get_fullname($billing_address['entry_firstname'],$billing_address['entry_lastname']), 
                           'billing_company' => $billing_address['entry_company'], 
                           'billing_street_address' => $billing_address['entry_street_address'],  
                           'billing_suburb'   => $billing_address['entry_suburb'],  
                           'billing_city'     => $billing_address['entry_city'], 
                           'billing_postcode' => $billing_address['entry_postcode'], 
                           'billing_state' => ((tep_not_null($billing_address['entry_state']))?$billing_address['state']:$billing_address['zone_name']), 
                           'billing_country' => $billing_address['countries_name'],
                           'billing_telephone' => $billing_address['entry_telephone'], 
                           'billing_address_format_id' => $billing_address['address_format_id'],  
                           'comment_msg' => $pInfo['yourmessage'], 
                           );
   
   $payment_modules->deal_preorder_info($pInfo, $sql_data_array); 
   $sh_comments = ''; 
   $sh_comments = $payment_modules->deal_preorder_additional($pInfo, $sql_data_array); 
   
   tep_db_perform(TABLE_PREORDERS, $sql_data_array);

   require(DIR_WS_CLASSES.'order_total.php');
   $order_total_modules = new order_total;
   $order_totals = $order_total_modules->pre_process(); 
   
   for ($i=0, $n=sizeof($order_totals); $i<$n; $i++) { 
     $sql_data_array = array('orders_id' => $order_id,
                             'title' => $order_totals[$i]['title'], 
                             'text' => $order_totals[$i]['text'], 
                             'value' => $order_totals[$i]['value'], 
                             'class' => $order_totals[$i]['code'], 
                             'sort_order' => $order_totals[$i]['sort_order'], 
     );
     tep_db_perform(TABLE_PREORDERS_TOTAL, $sql_data_array);
   }
  
   $customer_notification = (SEND_EMAILS == 'true') ? '1' : '0';
   
   $sql_data_array = array('orders_id' => $order_id, 
                           'orders_status_id' => $orders_status, 
                           'date_added' => 'now()', 
                           'customer_notified' => $customer_notification,
                           'comments' => $sh_comments,
                           'user_added' => ($exists_single)?tep_get_fullname($pInfo['firstname'],$pInfo['lastname']):tep_get_fullname($customers_res['customers_firstname'], $customers_res['customers_lastname'])
                           );
   tep_db_perform(TABLE_PREORDERS_STATUS_HISTORY, $sql_data_array);
  
   $products_raw = tep_db_query("select * from ".TABLE_PRODUCTS." where products_id = '".$pInfo['products_id']."'");
   $products_res = tep_db_fetch_array($products_raw);
   
  $tax_address_query = tep_db_query("select ab.entry_country_id, ab.entry_zone_id from " . TABLE_ADDRESS_BOOK . " ab left join " . TABLE_ZONES . " z on (ab.entry_zone_id = z.zone_id) where ab.customers_id = '" . $customers_id . "' and ab.address_book_id = '1'");
  $tax_address = tep_db_fetch_array($tax_address_query);
   $search_products = tep_get_product_by_id($pInfo['products_id'], 0, $languages_id,true,'product_info'); 
   $sql_data_array = array('orders_id' => $order_id, 
                          'products_id' => $pInfo['products_id'], 
                          'products_model' => $products_res['products_model'], 
                          'products_name' => $search_products['products_name'],
                          'products_tax' => tep_get_tax_rate($products_res['products_tax_class_id'], $tax_address['entry_country_id'], $tax_address['entry_zone_id']), 
                          'products_quantity' => $pInfo['quantity'], 
                          'products_rate' => tep_get_products_rate($pInfo['products_id']), 
                          'site_id' => SITE_ID
                          );
   tep_db_perform(TABLE_PREORDERS_PRODUCTS, $sql_data_array);
   $preorder_products_id = tep_db_insert_id();

   $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>");
   foreach ($pInfo as $op_key => $op_value) {
     $op_single_str = substr($op_key, 0, 3);
     if ($op_single_str == 'op_') {
       $op_tmp_value = str_replace(' ', '', $op_value);
       $op_tmp_value = str_replace('　', '', $op_value);
       $op_info_array = explode('_', $op_key);
       $item_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_info_array[1]."' and id = '".$op_info_array[3]."'");
       $item_res = tep_db_fetch_array($item_raw); 
       if ($item_res) {
         $item_price = 0; 
         if ($op_tmp_value == '') {
           $input_option_array = array('title' => $item_res['front_title'], 'value' => MSG_TEXT_NULL); 
         } else {
           $input_option_array = array('title' => $item_res['front_title'], 'value' => str_replace("<BR>", "<br>", stripslashes($op_value))); 
         }
         if ($item_res['type'] == 'radio') {
           $ro_array = @unserialize($item_res['option']);
           if (!empty($ro_array)) {
             foreach ($ro_array['radio_image'] as $ro_key => $ro_value) {
               if (trim(str_replace($replace_arr, '', nl2br(stripslashes($ro_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_value))))) {
                 $item_price = $ro_value['money'];
                 break; 
               }
             }
           }
         } else {
           $item_price = $item_res['price']; 
         }
         $sql_data_array = array(
           'orders_id' => $order_id,
           'orders_products_id' => $preorder_products_id, 
           'options_values_price' => 0, 
           'option_info' => tep_db_input(serialize($input_option_array)), 
           'option_group_id' => $item_res['group_id'], 
           'option_item_id' => $item_res['id'], 
         );
         tep_db_perform(TABLE_PREORDERS_PRODUCTS_ATTRIBUTES, $sql_data_array);
       }
     }
     
   }
   preorders_updated($order_id);  
   
   if ($is_active == 1) {
     preorder_last_customer_action();
   }
}

/* -------------------------------------
    功能: 生成预约订单号的后两位数字 
    参数: 无 
    返回值: 后两位数字(string) 
------------------------------------ */
function tep_get_preorder_end_num() 
{
  $last_orders_raw = tep_db_query("select * from ".TABLE_PREORDERS." order by orders_id desc limit 1"); 
  $last_orders = tep_db_fetch_array($last_orders_raw);
  
  if ($last_orders) {
    $last_orders_num = substr($last_orders['orders_id'], -2); 
    
    if (((int)$last_orders_num < 99) && ((int)$last_orders_num > 0)) {
      $next_orders_num = (int)$last_orders_num + 1; 
    } else {
      $next_orders_num = 1; 
    }
    return sprintf('%02d', $next_orders_num); 
  }
  
  return '01';
}
/* -------------------------------------
    功能: 更新顾客最新下预约订单时间 
    参数: 无 
    返回值: 无 
------------------------------------ */
function preorder_last_customer_action() {
  tep_db_query("update ".TABLE_CONFIGURATION." set configuration_value=now() where configuration_key='PREORDER_LAST_CUSTOMER_ACTION'");
}

/* -------------------------------------
    功能: 是否显示预约的支付方法 
    参数: $limit_setting(string) 在会员显示还是游客显示的设定 
    返回值: 是否显示(boolean) 
------------------------------------ */
function tep_whether_show_preorder_payment($limit_setting) {
  $payment_arr = unserialize($limit_setting);  
  
  if (empty($payment_arr)) {
    return false; 
  }
  $tmp_arr = array(); 
  
  foreach ($payment_arr as $pkey => $pvalue) {
    if (!empty($pvalue)) {
      $tmp_arr[] = $pvalue; 
    }
  }
  
  if (empty($tmp_arr)) {
    return false; 
  }
  
  $payment_arr = $tmp_arr;

  $num = count($payment_arr); 
  if ($num == 1) {
    if (empty($payment_arr[0])) {
      return false; 
    }
  }
  
  if ($num == 2) {
    $i = 0; 
    foreach ($payment_arr as $key => $value) {
      if (!empty($value)) {
        $i++; 
      }
    }
    if ($i == $num) {
      return true; 
    } else {
      foreach ($payment_arr as $skey => $svalue) {
        if (!empty($svalue)) {
          if ($svalue == 1) {
            if (!isset($_SESSION['customer_id'])) {
              return false; 
            } else  if (isset($_SESSION['customer_id']) && ($_SESSION['guestchk'] == '1')) {
              return false; 
            }
          } else if ($svalue == 2) {
            if (isset($_SESSION['customer_id']) && ($_SESSION['guestchk'] == '0')) {
              return false; 
            }
          }
          break; 
        }
      }  
    }
  } else {
    if ($payment_arr[0] == 1) {
      if (!isset($_SESSION['customer_id'])) {
        return false; 
      } else  if (isset($_SESSION['customer_id']) && ($_SESSION['guestchk'] == '1')) {
        return false; 
      }
    } else if ($payment_arr[0] == 2) {
      if (isset($_SESSION['customer_id']) && ($_SESSION['guestchk'] == '0')) {
        return false; 
      }
    }
  }

  return true;
}

/* -------------------------------------
    功能: 通过GET获得的信息取得商品id 
    参数: 无 
    返回值: 商品id(int/boolean) 
------------------------------------ */
function tep_preorder_get_products_id_by_param()
{
   global $languages_id;
   
   $category_array = array();
   
   if (!isset($_GET['fromaji'])) {
     return false; 
   }
   
   if (!isset($_GET['promaji'])) {
     return false; 
   }
   
   $category_query = tep_db_query("select cd.categories_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and c.parent_id = '0' and cd.romaji = '".urldecode($_GET['fromaji'])."' and cd.language_id = '".$languages_id."' and (cd.site_id = '0' or cd.site_id = '".SITE_ID."') order by cd.site_id desc limit 1");
   $category = tep_db_fetch_array($category_query);
   if ($category) {
     $category_array[] = $category['categories_id']; 
     if (isset($_GET['sromaji'])) {
       $child_category_query = tep_db_query("select cd.categories_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and c.parent_id = '".$category['categories_id']."' and cd.romaji = '".urldecode($_GET['sromaji'])."' and cd.language_id = '".$languages_id."' and (cd.site_id = '0' or cd.site_id = '".SITE_ID."') order by cd.site_id desc limit 1");
       $child_category = tep_db_fetch_array($child_category_query);
       
       if ($child_category) {
         $category_array[] = $child_category['categories_id']; 
         if (isset($_GET['tromaji'])) {
           $child_child_category_query = tep_db_query("select cd.categories_id from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and c.parent_id = '".$child_category['categories_id']."' and cd.romaji = '".urldecode($_GET['tromaji'])."' and cd.language_id = '".$languages_id."' and (cd.site_id = '0' or cd.site_id = '".SITE_ID."') order by cd.site_id desc limit 1");
           $child_child_category = tep_db_fetch_array($child_child_category_query);
          
           if ($child_child_category) {
             $category_array[] = $child_child_category['categories_id']; 
           }
         }
       }
     }
   }
  
   if (!empty($category_array)) {
     $count_num = count($category_array); 
     $product_info_query = tep_db_query("select pd.products_id from ".TABLE_PRODUCTS_DESCRIPTION." pd where pd.language_id = '".$languages_id."' and pd.romaji = '".urldecode($_GET['promaji'])."' and (pd.site_id = '0' or pd.site_id = '".SITE_ID."') group by products_id order by pd.site_id desc");
     if (tep_db_num_rows($product_info_query) > 0) {
       while ($product_info = tep_db_fetch_array($product_info_query)) {
         $product_to_category_query = tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id = '".$product_info['products_id']."'"); 
         while ($product_to_category = tep_db_fetch_array($product_to_category_query)) {
           if ($product_to_category['categories_id'] == $category_array[$count_num-1]) {
             return $product_info['products_id']; 
           }
         }
       }
     }
   }
   
   return false;
}

/* -------------------------------------
    功能: 生成时间信息的html元素  
    参数: $start_time(string) 开始时间 
    参数: $radio_name(string) 元素名字 
    返回值: html元素(string) 
------------------------------------ */
function tep_get_torihiki_date_radio($start_time,$radio_name="torihiki_time"){
  $arr = array();
  $time_str = date('H:i',$start_time);
  $time_arr = explode(':',$time_str);
  $hour = $time_arr[0];
  $mim_start = $time_arr[1];
  $show_row = 0;
  for($hour;$hour<24;$hour++){
    for($mim_start;$mim_start<60;){
      if($show_row ==0 ){
        if($mim_start < 15){
          $mim_start = 15;
          $arr[]=null;
        }else if($mim_start < 30){
          $mim_start = 30;
        }else if($mim_start < 30){
        }else if($mim_start < 30){
        }else if($mim_start < 45){
          $mim_start = 45;
          $arr[]=null;
        }else if($mim_start < 30){
        }else if($mim_start < 30){
        }else if($mim_start < 30){
        }else if($mim_start >= 45){
          $mim_start = 0;
          break;
        }
      }
      $s_start = $mim_start;
      $mim_start+=14;
      $e_start = $mim_start;
      $return_str = "<input type='radio' name='".$radio_name."' value='".
           sprintf('%02d',$hour).":".sprintf('%02d',$s_start)."-".
           sprintf('%02d',$hour).":".sprintf('%02d',$e_start)."'>&nbsp;&nbsp;";
      $return_str .= sprintf('%02d',$hour)."時".sprintf('%02d',$s_start)."分";
      $return_str .= " ～ ";
      $return_str .= sprintf('%02d',$hour)."時".sprintf('%02d',$e_start)."分";
      $show_row ++;
      $mim_start++;
      $arr[]=$return_str;
    }
    $mim_start = 0;
  }
  return $arr;
}






/* -------------------------------------
    功能: 获得该订单的商品id 
    参数: $oid(string) 订单id 
    返回值: 商品id(array) 
------------------------------------ */
  function tep_get_products_list_by_order_id($oid){
  $sql = "select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id
    = '" . $oid. "'";
  $query = tep_db_query($sql);
  $products_list = array();
  while($row = tep_db_fetch_array($query)){
    $products_list[] = $row;
  }
  return $products_list;
}
/* -------------------------------------
    功能: 是否显示支付方法 
    参数: 无 
    返回值: 是否显示(boolean) 
------------------------------------ */
function tep_whether_show_payment(){
  return true;
}
/* -------------------------------------
    功能: 是否金额超值 
    参数: 无 
    返回值: 是否超值(boolean) 
------------------------------------ */
function check_money_limit() {
  return false;
}


/* -------------------------------------
    功能: 输出支付方法 
    参数: $is_show(boolean) 是否增加html元素 
    返回值: 支付方法html(string) 
------------------------------------ */
function tep_payment_out_selection($is_show = false){
global $selection;
global $payment_modules;
global $order;
global $h_point;
$total_info = $order->info['total'];
if ((MODULE_ORDER_TOTAL_POINT_STATUS == 'true') && (intval($h_point) > 0)) {
   $total_info -= intval($h_point);
}
if (isset($_SESSION['campaign_fee'])) {
   $total_info += $_SESSION['campaign_fee'];
}
?>
<!-- selection start -->
<div class="checkout_payment_info">
  <?php
   //如果大于1个支付方法需要用户选择 ，如果小于则不需要选择了
    if (sizeof($selection) > 0) {
      if (!$is_show) {
        echo "<div>";
      }
      echo '<div class="float_left">'.TEXT_SELECT_PAYMENT_METHOD."</div>";
      echo '<div class="txt_right"><b>'.TITLE_PLEASE_SELECT.'</b><br>'.tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif').'</div> ';
      if (!$is_show) {
        echo "</div> ";
      } 
    }else {
      echo "<div>";
      echo '<div class="float_left">';
      echo TEXT_ENTER_PAYMENT_INFORMATION;
      echo '</div><div>&nbsp;</div>';
      echo "</div>";
    }
  ?>
  <!-- loop start  -->
<?php  
     if(isset($_SESSION['payment_error'])){
	 ?>
     <br>
     <div class="box_waring">
     <?php
     echo TEXT_PAYMENT_ERROR_TOP;
     if(is_array($_SESSION['payment_error'])){
           foreach($_SESSION['payment_error'] as $key=>$value){
               if (is_array($value)) {
                 echo $value[0]; 
               } else {
                 echo $selection[strtoupper($key)]['module'];
                 echo TEXT_ERROR_PAYMENT_SUPPLY;
                 echo $value;
               }
           }
         }else{
           echo $_SESSION['payment_error'];
         }
         $_SESSION['new_payment_error'] = $_SESSION['payment_error'];
         unset($_SESSION['payment_error']);
     ?>
     </div><br>
     <?php
	 }
    foreach ($selection as $key=>$singleSelection){
      //判断支付范围 
      if($payment_modules->moneyInRange($singleSelection['id'], $total_info)){
	continue;
      }
      if(!$payment_modules->showToUser($singleSelection['id'],$_SESSION['guestchk'])){
        continue;
      }
?>
	<div>
       
		<div class="box_content_title <?php if($_SESSION['payment']==$singleSelection['id']) { echo 'box_content_title_selected';}?> "  >
			<div class="frame_w70"><b><?php echo $singleSelection['module'];?></b></div>
			<div class="float_right">
            	<?php echo tep_draw_radio_field('payment',$singleSelection['id'] ,$_SESSION['payment']==$singleSelection['id']); ?>
			</div>
		</div>
		<div>
                <p class="cp_description"> <?php  echo $singleSelection['description'];?></p>
				<div class="cp_content">
                	<div style="display: none;"  class="rowHide rowHide_<?php echo $singleSelection['id'];?>">
                    <?php echo $singleSelection['fields_description']; 
                    foreach ($singleSelection['fields'] as $key2=>$field){
					?>
                                                                                  
                        <div class="txt_input_box">
                        <?php if($field['title']){ ?>
                            <div class="frame_title"><?php echo $field['title'];?></div>
                            <?php }?>
                            <div class="float_left"><?php echo $field['field'];?><small><font color="#AE0E30"><?php echo $field['message'];?></font></small></div>
                        </div>
					<?php 
                                      }
                                         echo $singleSelection['footer'];
					?>
					</div>
					<div><?php echo $singleSelection['codefee'];?></div>
				</div>
		</div>
	</div>
<?
    }
?>
<!-- loop end  -->
</div>

<!-- selection end -->
<?php
  }

/* -------------------------------------
    功能: 判断该顾客是否是会员 
    参数: $customer_id(int) 顾客id 
    返回值: 是否是会员(boolean) 
------------------------------------ */
function tep_is_member_customer($customer_id){
  $sql = "select customers_guest_chk from ".TABLE_CUSTOMERS." 
    where customers_id='".$customer_id."' 
    and site_id ='".SITE_ID."' 
    and customers_guest_chk = '0' 
    limit 1";
  $query = tep_db_query($sql);
  if($row = tep_db_fetch_array($query)){
    return true;
  }else{
    return false;
  }
}

/* -------------------------------------
    功能: 过滤优惠券信息 
    参数: $c_str(string) 优惠券信息 
    返回值: 过滤后的信息(string) 
------------------------------------ */
function get_strip_campaign_info($c_str)
{
  $c_str = str_replace('　', '', $c_str);
  $c_str = str_replace(' ', '', $c_str);
  $arr = array(
      'Ａ','Ｂ','Ｃ','Ｄ','Ｅ','Ｆ','Ｇ','Ｈ','Ｉ','Ｊ','Ｋ','Ｌ','Ｍ','Ｎ','Ｏ','Ｐ','Ｑ','Ｒ','Ｓ','Ｔ','Ｕ','Ｖ','Ｗ','Ｘ','Ｙ','Ｚ',
      'ａ','ｂ','ｃ','ｄ','ｅ','ｆ','ｇ','ｈ','ｉ','ｊ','ｋ','ｌ','ｍ','ｎ','ｏ','ｐ','ｑ','ｒ','ｓ','ｔ','ｕ','ｖ','ｗ','ｘ','ｙ','ｚ',
      '１','２','３','４','５','６','７','８','９','０',
      '　'
    );
  $arr2 = array(
      'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
      'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
      '1','2','3','4','5','6','7','8','9','0',
      ' '
  );
  
  $c_str = str_replace($arr, $arr2, $c_str);
  $c_str = preg_replace('/[^0-9a-zA-Z]/','',$c_str);
  
  return $c_str;
}

/* -------------------------------------
    功能: 在预约转正式的最终确认页获得小计,总价等相关信息 
    参数: $payment(string) 支付方法 
    参数: $pid(string) 预约订单id 
    参数: $option_info_array(array) 预约订单的属性 
    返回值: 相关信息(array) 
------------------------------------ */
function get_preorder_total_info($payment, $pid, $option_info_array) 
{
  global $payment_modules;

  $preorder_total_info = array();    
  
  $preorder_product_info_raw = tep_db_query("select final_price, products_quantity, products_tax from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$pid."'");
  $preorder_product_info = tep_db_fetch_array($preorder_product_info_raw);  
  
  $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>");
  if (!empty($option_info_array)) {
    $attr_total = 0; 
    foreach ($option_info_array as $tp_key => $tp_value) {
      $tp_key_array = explode('_', $tp_key); 
      $option_item_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$tp_key_array[3]."' and name = '".$tp_key_array[1]."'"); 
      $option_item_res = tep_db_fetch_array($option_item_raw); 
      if ($option_item_res) {
        if ($option_item_res['type'] == 'radio') {
          $o_option_array = @unserialize($option_item_res['option']);
          if (!empty($o_option_array['radio_image'])) {
            foreach ($o_option_array['radio_image'] as $or_key => $or_value) {
              if (trim(str_replace($replace_arr, '', nl2br(stripslashes($or_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($tp_value))))) {
                $attr_total += $or_value['money']; 
                break; 
              }
            }
          }
        } else if ($option_item_res['type'] == 'textarea') {
          $t_option_array = @unserialize($option_item_res['option']);
          $tmp_t_single = false;
          if ($t_option_array['require'] == '0') {
            if ($tp_value == MSG_TEXT_NULL) {
              $tmp_t_single = true;
            }
          }
          if ($tmp_t_single) {
            $attr_total += 0; 
          } else {
            $attr_total += $option_item_res['price']; 
          }
        } else {
          $attr_total += $option_item_res['price']; 
        }
      }
    }
    
    if ($attr_total == 0) {
      return $preorder_total_info; 
    }
    
    if (DISPLAY_PRICE_WITH_TAX == 'true') {
      $p_show_price = tep_add_tax(($preorder_product_info['final_price']+$attr_total)*$preorder_product_info['products_quantity'], $preorder_product_info['products_tax']); 
    } else {
      $p_show_price = ($preorder_product_info['final_price']+$attr_total)*$preorder_product_info['products_quantity']; 
    }
    
    $preorder_total_info['final_price'] = $preorder_product_info['final_price']+$attr_total;
    $preorder_subtotal = $p_show_price;
    $preorder_total_info['subtotal'] = $preorder_subtotal;
    
    $new_tax = 0;

    $plustax_query = tep_db_query("select count(*) as cnt from ".TABLE_PREORDERS_TOTAL." where class = 'ot_tax' and orders_id = '".$pid."'");
    $plustax = tep_db_fetch_array($plustax_query);
    
    if ($plustax['cnt'] > 0) {
      $new_tax = (($preorder_product_info['products_tax']/100)*($preorder_products_info['products_quantity']*($preorder_products_info['final_price']+$attr_total)));
      $preorder_total_info['tax'] = $new_tax; 
    }
    
    $total_query = tep_db_query("select sum(value) as total_value from ".TABLE_PREORDERS_TOTAL." where class != 'ot_total' and class != 'ot_point' and class != 'ot_tax' and class != 'ot_subtotal' and orders_id = '".$pid."'");
    $total_value = tep_db_fetch_array($total_query);
    if ($plustax['cnt'] == 0) {
      $preorder_newtotal = $total_value['total_value']+$new_tax+$preorder_subtotal; 
    } else {
      if (DISPLAY_PRICE_WITH_TAX == 'true') {
        $preorder_newtotal = $total_value['total_value']-$new_tax+$preorder_subtotal; 
      } else {
        $preorder_newtotal = $total_value['total_value']+$preorder_subtotal; 
      }
    }
   
    $preorder_calc_fee = $payment_modules->handle_calc_fee($payment, $preorder_newtotal);
    $preorder_total_info['total'] = $preorder_newtotal+$preorder_calc_fee;
    $preorder_total_info['fee'] = $preorder_calc_fee;
  }
  
  return $preorder_total_info;
}

/* -------------------------------------
    功能: 获得属性的价格 
    参数: $item_id(int) 属性元素id 
    参数: $group_id(int) 属性组id 
    参数: $att_value(string) 属性值 
    返回值: 属性价格(float) 
------------------------------------ */
function tep_get_show_attributes_price($item_id, $group_id, $att_value) 
{
  $item_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$item_id."' and group_id = '".$group_id."'"); 
  $item_res = tep_db_fetch_array($item_raw);
  
  $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>");
  if ($item_res) {
    if ($item_res['type'] == 'radio') {
      $option_array = @unserialize($item_res['option']);
      if (!empty($option_array)) {
        foreach ($option_array['radio_image'] as $key => $value) {
          if (trim(str_replace($replace_arr, '', nl2br(stripslashes($value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($att_value))))) {
            return $value['money']; 
          }
        }
      }
    } else if ($item_res['type'] == 'textarea') {
      $t_option_array = @unserialize($item_res['option']);
      $t_o_single = false; 
      if ($t_option_array['require'] == '0') {
        if ($att_value == MSG_TEXT_NULL) {
          $t_o_single = true; 
        }
      } 
      if ($t_o_single) {
        return 0; 
      } else {
        return $item_res['price']; 
      }
    } else {
      return $item_res['price']; 
    }
  }
  
  return 0;
}

/* -------------------------------------
    功能: 判断购物车里的商品是否有登录属性 
    参数: 无 
    返回值: 是否有登录属性(boolean) 
------------------------------------ */
function tep_check_also_products_attr()
{
   global $cart, $hm_option;
   $return_single = true; 

   $list_products = $cart->get_products();
   $c_array = array();

   for ($j=0, $k=sizeof($list_products); $j<$k; $j++) {
     $belong_option_raw = tep_db_query("select products_cflag, belong_to_option from ".TABLE_PRODUCTS." where products_id = '".(int)$list_products[$j]['id']."'"); 
     $belong_option = tep_db_fetch_array($belong_option_raw); 
     
     if ($belong_option) {
       if ($hm_option->check_old_symbol_show($belong_option['belong_to_option'], $belong_option['products_cflag'])) {
         $c_array[] = (int)$list_products[$j]['id']; 
       }
     }
   }
   
   if (!empty($c_array)) {
     $return_single = false; 
   }
   return $return_single;
}

/* -------------------------------------
    功能: 判断该顾客重置标识是否为真 
    参数: $id(int) 顾客id 
    返回值: 是否为真(boolean) 
------------------------------------ */
function tep_customer_in_reset_range($id){
  $sql = "select reset_flag from ".TABLE_CUSTOMERS_INFO." 
    where customers_Info_Id ='".$id."'";
  $query = tep_db_query($sql);
  $query = tep_db_fetch_array($query);
  return $query['reset_flag']==1;	 
}
/* -------------------------------------
    功能: 替换顾客邮件指定参数 
    参数: $msg(string) 邮件信息 
    返回值: 处理后的信息(string) 
------------------------------------ */
function tep_get_replaced_reset_msg($msg){
  $customer_id = $_SESSION['reset_customers_id'];
  $c_sql = "select * from ".TABLE_CUSTOMERS." where customers_id='".
    $customer_id."' limit 1 ";
  $c_query = tep_db_query($c_sql);
  $c_info = tep_db_fetch_array($c_query);
  $msg = str_replace(
  array(
    "\n",
    "\n\r",
    '${NAME}',
    '${MAIL}',
    '${SITE_NAME}',
    '${SUPPORT_EMAIL}',
    '${SITE_URL}',
    '${SITE_MAIL}'
    ),
  array(
  '<br>',
  '<br>',
  tep_get_fullname($c_info['customers_firstname'],$c_info['customers_lastname']),
  $c_info['customers_email_address'],
  STORE_NAME,
  SUPPORT_EMAIL_ADDRESS,
  HTTP_SERVER,
  SUPPORT_EMAIL_ADDRESS),$msg
  );
	 return $msg;
	 
}

/* -------------------------------------
    功能: 获得弹出层的url 
    参数: 无 
    返回值: url地址(string) 
------------------------------------ */
function tep_get_popup_url(){
  $customer_id = $_SESSION['reset_customers_id'];
  $c_sql = "select * from ".TABLE_CUSTOMERS." where customers_id='".
    $customer_id."' limit 1 ";
  $c_query = tep_db_query($c_sql);
  $c_info = tep_db_fetch_array($c_query);
  if($c_info){
    return tep_href_link(FILENAME_SEND_SUCCESS,'send_mail='.$c_info['customers_email_address'].'&show=1');
  }else{
    return '';
  }
}

/* -------------------------------------
    功能: 替换商品描述的指定元素 
    参数: $string(string) 商品描述 
    返回值: 处理后的信息(string) 
------------------------------------ */
function tep_replace_product_des($string){
  if(preg_match('|<td>(([^<]*)(<b>)*[^<]*(</b>)*[^<]*)</td><td>([^<]*)</td><td>([^<]*(<b>)*[^<]*(</b>)*[^<]*)</td>|',
  $string)){
  $string = preg_replace('|<td>(([^<]*)(<b>)*[^<]*(</b>)*[^<]*)</td><td>([^<]*)</td><td>([^<]*(<b>)*[^<]*(</b>)*[^<]*)</td>|',
"<td width='10%'>\$1</td><td width='3%'>\$5</td><td width='15%'>\$6</td>"
,$string);
  }
  return str_replace('<td','<td valign="top" ',$string);
}

/* -------------------------------------
    功能: 把全角的数字和字母替换成半角 
    参数: $c_str(string) 字符串 
    返回值: 替换后的字符串(string) 
------------------------------------ */
function tep_replace_all_full_character($c_str)
{
  $arr = array(
      'Ａ','Ｂ','Ｃ','Ｄ','Ｅ','Ｆ','Ｇ','Ｈ','Ｉ','Ｊ','Ｋ','Ｌ','Ｍ','Ｎ','Ｏ','Ｐ','Ｑ','Ｒ','Ｓ','Ｔ','Ｕ','Ｖ','Ｗ','Ｘ','Ｙ','Ｚ',
      'ａ','ｂ','ｃ','ｄ','ｅ','ｆ','ｇ','ｈ','ｉ','ｊ','ｋ','ｌ','ｍ','ｎ','ｏ','ｐ','ｑ','ｒ','ｓ','ｔ','ｕ','ｖ','ｗ','ｘ','ｙ','ｚ',
      '１','２','３','４','５','６','７','８','９','０',
      '　'
    );
  $arr2 = array(
      'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
      'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
      '1','2','3','4','5','6','7','8','9','0',
      ' '
  );
  
  $c_str = str_replace($arr, $arr2, $c_str);
  return $c_str;
}
/* -------------------------------------
    功能: 判断该图片是否存在 
    参数: $src(string) 图片路径 
    返回值: 是否存在(boolean) 
------------------------------------ */
function file_exists3($src) {
  if(substr(DIR_FS_CATALOG,-1)=='/'){
    $fs_catalog = DIR_FS_CATALOG;
  }else{
    $fs_catalog = DIR_FS_CATALOG.'/';
  }
  if(!file_exists($fs_catalog .'images/' . $src)
       && file_exists($fs_catalog .  str_replace('images/', 'default_images/', $src))
       ){
     $src = str_replace('images/', 'default_images/', $src);
   }
  return file_exists($src);
  }

/* -------------------------------------
    功能: 转换回车字符 
    参数: $string(string) 字符串 
    返回值: 处理后的字符串(string) 
------------------------------------ */
function new_nl2br($string) {
  $string = str_replace(array("\r\n", "\r", "\n"), "<br>", $string);
  return $string;
} 

/* -------------------------------------
    功能: 缓存已购买页的信息 
    参数: $auto_expire(boolean) 是否自动过期 
    参数: $refresh(boolean) 是否刷新 
    返回值: 缓存页的信息(string) 
------------------------------------ */
  function tep_cache_also_purchaseds($auto_expire = false, $refresh = false) {
    global $_GET, $language, $languages_id;

    if (($refresh == true) || !read_cache($cache_output, 'also_purchased-' . $language . '.cache' . $_GET['products_id'], $auto_expire)) {
      ob_start();
      include(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCT);
      $cache_output = ob_get_contents();
      ob_end_clean();
      write_cache($cache_output, 'also_purchased-' . $language . '.cache' . $_GET['products_id']);
    }

    return $cache_output;
  }

/* -------------------------------------
    功能: 显示支付方法的html 
    参数: 无 
    返回值: 支付方法的html(string) 
------------------------------------ */
function tep_payment_out_selections(){
global $selection;
global $payment_modules;
global $order;
global $h_point;
$total_info = $order->info['total'];
if ((MODULE_ORDER_TOTAL_POINT_STATUS == 'true') && (intval($h_point) > 0)) {
   $total_info -= intval($h_point);
}
if (isset($_SESSION['campaign_fee'])) {
   $total_info += $_SESSION['campaign_fee'];
}
?>
<!-- selection start -->
<div class="checkout_payment_info">
  <?php
   //如果大于1个支付方法需要用户选择 ，如果小于则不需要选择了
    if (sizeof($selection) > 1) {
      echo "<div id='hm-payment'>";
      echo '<div class="hm-payment-top-left">'.TEXT_SELECT_PAYMENT_METHOD."</div>";
      echo '<div class="hm-payment-right"><b>'.TITLE_PLEASE_SELECT.'</b></div> ';
      echo "</div> ";
    }else {
      echo "<div id='hm-payment'>";
      echo '<div class="hm-payment-left">';
      echo TEXT_ENTER_PAYMENT_INFORMATION;
      echo '</div><div></div>';
      echo "</div>";
    }
  ?>
  <!-- loop start  -->
<?php  
     if(isset($_SESSION['payment_error'])){
	 ?>
    <?php if (NEW_STYLE_WEB === true) {?>
    <div class="box_new_waring">
    <?php } else {?>
    <div class="box_waring">
    <?php }?>
     <?php
     echo TEXT_PAYMENT_ERROR_TOP;
     if(is_array($_SESSION['payment_error'])){
           foreach($_SESSION['payment_error'] as $key=>$value){
               if (is_array($value)) {
                 echo $value[0]; 
               } else {
                 echo $selection[strtoupper($key)]['module'];
                 echo TEXT_ERROR_PAYMENT_SUPPLY;
                 echo $value;
               }
           }
         }else{
           echo $_SESSION['payment_error'];
         }
         unset($_SESSION['payment_error']);
     ?>
     </div>
     <?php if (NEW_STYLE_WEB !== true) {?>
     <br>
     <?php }?>
     <?php
	 }
    foreach ($selection as $key=>$singleSelection){
      //判断支付范围 
      if($payment_modules->moneyInRange($singleSelection['id'],$total_info)){
	continue;
      }
      if(!$payment_modules->showToUser($singleSelection['id'],$_SESSION['guestchk'])){
        continue;
      }
?>
	<div>
       
		<div class="box_content_title <?php if($_SESSION['payment']==$singleSelection['id']) { echo 'box_content_title_selected';}?> "  >
			<div class="hm-payment-left"><b><?php echo
                        $singleSelection['module'];?><br></b></div>
			<div class="hm-payment-right">
            	<?php echo tep_draw_radio_field('payment',$singleSelection['id'] ,$_SESSION['payment']==$singleSelection['id']); ?>
			</div>
		</div>
		<div class="box_content_text">
                <p class="cp_description"> <?php  echo $singleSelection['description'];?></p>
				<div class="cp_content">
                	<div style="display: none;"  class="rowHide rowHide_<?php echo $singleSelection['id'];?>">
                    <?php echo $singleSelection['fields_description']; 
                    foreach ($singleSelection['fields'] as $key2=>$field){
					?>
                                                                                  
                        <div class="txt_input_box">
                        <?php 
                            if(NEW_STYLE_WEB===true){
                              $style_nowrap = 'style="white-space:nowrap;width:180px;"'; 
                            }
                            if($field['title']){ ?>
                            <div class="frame_title" <?php echo $style_nowrap; ?> ><?php echo $field['title'];?></div>
                            <?php }?>
                            <div class="input_title"><?php echo $field['field'];?><small><font color="#AE0E30"><?php echo $field['message'];?></font></small></div>
                        </div>
					<?php 
                                      }
                                         echo $singleSelection['footer'];
					?>
					</div>
					<div><?php echo $singleSelection['codefee'];?></div>
				</div>
		</div>
	</div>
<?
    }
?>
<!-- loop end  -->
</div>

<!-- selection end -->
<?php

  }
?>
<?php
/* -------------------------------------
    功能: 显示最终确认金额的信息 
    参数: 无 
    返回值: 确认金额的信息的html(string) 
------------------------------------ */
function outputs() {
      global $order;
      global $cart;
      global $payment, $currencies;
      //先使用global 等支付方法修改 完毕 修改成使用POST 
      global $_POST;

      $show_handle_fee = 0;

      if(isset($_POST['code_fee'])){
      $show_handle_fee = intval($_POST['code_fee']); 
      }
      $buying_fee = 0; 
      if (isset($cart)) { 
        $bflag_single = $this->ds_count_bflags();
        if ($bflag_single == 'View') {
          $buy_table_fee = split("[:,]", MODULE_PAYMENT_BUYING_COST);
          for ($i = 0; $i < count($buy_table_fee); $i+=2) {
            if ($order->info['total'] <= $buy_table_fee[$i]) {
              $buy_add_fee = $order->info['total'].$buy_table_fee[$i+1];
              @eval("\$buy_add_fee = $buy_add_fee;");
              if (is_numeric($buy_add_fee)) {
                $buying_fee = $buy_add_fee; 
              }
              break; 
            }
          }
        }
      }
      $total_handle_fee = $show_handle_fee + $buying_fee;
      
      $output_string = '';
      if (is_array($this->modules)) {
        reset($this->modules);
        while (list(, $value) = each($this->modules)) {
          $class = substr($value, 0, strrpos($value, '.'));
          if ($GLOBALS[$class]->enabled) {
            $size = sizeof($GLOBALS[$class]->output);
            for ($i=0; $i<$size; $i++) {
              if ($class == 'ot_point') {
                if (isset($_SESSION['campaign_fee'])) {
                  if ($_SESSION['campaign_fee'] == 0) {
                    continue; 
                  }
                } else {
                  if ($GLOBALS[$class]->output[$i]['value'] == 0) {
                    continue; 
                  }
                }
              }
              $colspan = SITE_ID == 2 ? ' colspan="2"' : '';
              $output_string .= '              <tr>' . "\n" .
                                '                <td align="right" class="main">' . $GLOBALS[$class]->output[$i]['title'] . '</td>' . "\n" .
                                '                <td align="right" class="main"'.
                                $colspan .'>';
              if ($class == 'ot_point') {
                if (isset($_SESSION['campaign_fee'])) {
                 
                  $output_string .= '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format_total(abs($_SESSION['campaign_fee']))) .  '</font>'.JPMONEY_UNIT_TEXT.'</td>' . "\n" .  '              </tr>';
                } else {
                  $output_string .= '<font color="#ff0000">'.str_replace(JPMONEY_UNIT_TEXT, '', $currencies->format_total($GLOBALS[$class]->output[$i]['value'])) . '</font>'.JPMONEY_UNIT_TEXT.'</td>' . "\n" .  '              </tr>';
                }
              } else {
                $output_string .= $currencies->format_total($GLOBALS[$class]->output[$i]['value']) . '</td>' . "\n" .  '              </tr>';
              }
            }
            $_SESSION['mailfee'] = $currencies->format($total_handle_fee);   
            if ($class == 'ot_subtotal') {
              if (!empty($total_handle_fee)) {
                $output_string .= '              <tr>' . "\n" .
                                  '                <td align="right" class="main">'
                                  . TEXT_HANDLE_FEE_CONFIRMATION . '</td>' . "\n" .
                                  '                <td align="right" class="main">'
                                  . $currencies->format($total_handle_fee) . '</td>' . "\n" .
                                  '              </tr>';
              }
            }
          }
        }
      }

      return $output_string;
    }

/* -------------------------------------
    功能: 把GET获得的信息整理成url的参数  
    参数: $exclude_array(string/array) 不包括的参数名 
    返回值: url的参数(string) 
------------------------------------ */
function tep_get_all_get_param($exclude_array = '') {
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

/* -------------------------------------
    功能: 分页html  
    参数: $query_numrows(int) 总行数 
    参数: $max_rows_per_page(int) 每页显示行数 
    参数: $max_page_links(int) 显示的页数 
    参数: $current_page_number(int) 当前页数 
    参数: $parameters(string) 其它参数 
    返回值: 分页的html(string) 
------------------------------------ */
 function display_linkst($query_numrows, $max_rows_per_page, $max_page_links, $current_page_number, $parameters = '') {
      global $PHP_SELF;
      $class = 'class="pageResults"';
      if ( tep_not_null($parameters) && (substr($parameters, -1) != '&') ) $parameters .= '&';
      
      $total_pg = ceil($query_numrows / $max_rows_per_page);
      $calc_num = 0;
        
      $jump_page_name = $_SERVER['SCRIPT_NAME']; 
      $cu_self = basename($PHP_SELF);
      $tag_page_single = 1;
      if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
        $tag_page_single = 0;
      }
      $jump_query_str = urlencode($_SERVER['QUERY_STRING']);

      if ($current_page_number > 1) {
        if ($current_page_number == 2) {
                if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
                  echo '<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters.'page=1')) . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>' . PREVNEXT_BUTTON_PREV . '</u></a>&nbsp;&nbsp;';
                } else {
                  echo '<a href="' . tep_href_link(basename($PHP_SELF), $parameters.'page=1') . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>' . PREVNEXT_BUTTON_PREV . '</u></a>&nbsp;&nbsp;';
                }
          } else {
                if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
                  echo '<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters . 'page=' .  ($current_page_number - 1))) . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>' . PREVNEXT_BUTTON_PREV . '</u></a>&nbsp;&nbsp;';
                } else {
                  echo '<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . ($current_page_number - 1)) . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>' . PREVNEXT_BUTTON_PREV . '</u></a>&nbsp;&nbsp;';
                }
        }
      }
      if ($total_pg <= 11) {
        for ($i = 1; $i <= $total_pg; $i++) {
          if ($i == $current_page_number) {
            if ($total_pg > 1) {
              echo '&nbsp;<b>'.$i.'</b>&nbsp;'; 
            }
          } else {
            if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
              echo '&nbsp;<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters . 'page=' .  $i)) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $i) . ' "><u>' .  $i . '</u></a>&nbsp;';
            } else {
              echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . $i) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $i) . ' "><u>' . $i . '</u></a>&nbsp;';
            }
          }
        }
      } else if (($current_page_number + 5) >= $total_pg) {
        $diff_num = $total_pg - $current_page_number;
        if (($current_page_number - (10 - $diff_num)) > 1) {
                  if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
                    echo '<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters.'page=1')) .  '" ' . $class . ' title=" ' .  PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>1...</u></a>&nbsp;&nbsp;';
                  } else {
                    echo '<a href="' . tep_href_link(basename($PHP_SELF), $parameters.'page=1') . '" ' . $class . ' title=" ' .  PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>1...</u></a>&nbsp;&nbsp;';
                  }
        }
        for ($i = 10-$diff_num; $i > 0; $i--) {
          $front_start = $current_page_number - $i; 
              if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
                echo '&nbsp;<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters . 'page=' .  $front_start)) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $front_start) . ' "><u>' .  $front_start . '</u></a>&nbsp;';
              } else {
                echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . $front_start) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $front_start) . ' "><u>' . $front_start . '</u></a>&nbsp;';
              }
        }
        echo '&nbsp;<b>'.$current_page_number.'</b>&nbsp;'; 
        for ($j = 1; $j <= $diff_num; $j++) {
          $end_start = $current_page_number + $j; 
                if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
                  echo '&nbsp;<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters . 'page=' .  $end_start)) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $end_start) . ' "><u>' .  $end_start . '</u></a>&nbsp;';
                } else {
                  echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . $end_start) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $end_start) .  ' "><u>' . $end_start . '</u></a>&nbsp;';
                }
        }
      } else if (($current_page_number - 5) <= 1) {
        $diff_num = $current_page_number - 1;
        for ($i = ($current_page_number-1); $i > 0; $i--) {
          $front_start = $current_page_number - $i; 
                if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
                  echo '&nbsp;<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters . 'page=' .  $front_start)) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $front_start) . ' "><u>' .  $front_start . '</u></a>&nbsp;';
                } else {
                  echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . $front_start) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $front_start) . ' "><u>' . $front_start . '</u></a>&nbsp;';
                }
        }
        echo '&nbsp;<b>'.$current_page_number.'</b>&nbsp;'; 
        for ($j = 1; $j <= (10-$diff_num); $j++) {
          $end_start = $current_page_number + $j; 
                if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
                  echo '&nbsp;<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters . 'page=' .  $end_start)) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $end_start) . ' "><u>' .  $end_start . '</u></a>&nbsp;';
                } else {
                  echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . $end_start) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $end_start) .  ' "><u>' . $end_start . '</u></a>&nbsp;';
                }
        }
        if ($end_start < $total_pg) {
              if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
                echo '&nbsp;<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters . 'page=' .  $total_pg)) . '" ' . $class . ' title=" ' .  PREVNEXT_TITLE_NEXT_PAGE . ' "><u>...' . $total_pg . '</u></a>&nbsp;';
              } else {
                echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . $total_pg) . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' "><u>...' . $total_pg . '</u></a>&nbsp;';
              }
        }
      } else {
        $front_start = 1;
        if ($current_page_number > 5) {
          $front_start = $current_page_number - 5; 
        }
        
        if ($front_start > 1) {
                    if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
                      echo '<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters.'page=1')) .  '" ' . $class . ' title=" ' .  PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>1...</u></a>&nbsp;&nbsp;';
                    } else {
                      echo '<a href="' . tep_href_link(basename($PHP_SELF), $parameters.'page=1') . '" ' . $class . ' title=" ' .  PREVNEXT_TITLE_PREVIOUS_PAGE . ' "><u>1...</u></a>&nbsp;&nbsp;';
                    }
        }
        for ($i=$front_start; $i<$current_page_number; $i++) {
            if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
              echo '&nbsp;<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters . 'page=' .  $i)) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $i) . ' "><u>' .  $i . '</u></a>&nbsp;';
            } else {
              echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . $i) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $i) . ' "><u>' . $i . '</u></a>&nbsp;';
            }
        }
        
        echo '&nbsp;<b>'.$current_page_number.'</b>&nbsp;';
        
        $end_start = 5;
        if ($total_pg > $end_start && ($current_page_number+$end_start) < $total_pg) {
          $end_start = $current_page_number + $end_start; 
        } else {
          $end_start = $total_pg; 
        }
        for ($j=$current_page_number+1; $j<=$end_start; $j++) {
            if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
              echo '&nbsp;<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters . 'page=' .  $j)) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $j) . ' "><u>' .  $j . '</u></a>&nbsp;';
            } else {
              echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . $j) . '" ' . $class . ' title=" ' .  sprintf(PREVNEXT_TITLE_PAGE_NO, $j) . ' "><u>' . $j . '</u></a>&nbsp;';
            }
        }
        
        if ($end_start < $total_pg) {
                if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
                  echo '&nbsp;<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters . 'page=' .  $total_pg)) . '" ' . $class . ' title=" ' .  PREVNEXT_TITLE_NEXT_PAGE . ' "><u>...' . $total_pg . '</u></a>&nbsp;';
                } else {
                  echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . $total_pg) . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' "><u>...' . $total_pg . '</u></a>&nbsp;';
                }
        }
      }
     
      if ($current_page_number < $total_pg) {
              if (preg_match('/^tags_id=([0-9]*)(.*)/', $_SERVER['QUERY_STRING'])) {
                echo '&nbsp;<a href="' . str_replace('index/', '', tep_href_link(basename($PHP_SELF), $parameters . 'page=' .  ($current_page_number + 1))) . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' "><u>' . PREVNEXT_BUTTON_NEXT . '</u></a>&nbsp;';
              } else {
                echo '&nbsp;<a href="' . tep_href_link(basename($PHP_SELF), $parameters . 'page=' . ($current_page_number + 1)) . '" ' . $class . ' title=" ' . PREVNEXT_TITLE_NEXT_PAGE . ' "><u>' . PREVNEXT_BUTTON_NEXT . '</u></a>&nbsp;';
              }
      }
      echo '&nbsp;<input type="text" name="spage" id="spage" size="2">&nbsp;'.JUMP_PAGE_TEXT.'&nbsp;';
      echo '<input type="button" onclick="jump_page(\''.$jump_page_name.'\', \''.$cu_self.'\', \''.urlencode($parameters).'\', \''.$jump_query_str.'\', '.$tag_page_single.', '.$total_pg.');" value="'.JUMP_PAGE_BUTTON_TEXT.'">';
    }

/* -------------------------------------
    功能: 获得订单地址 
    参数: $oid(string) 订单id 
    返回值: 订单地址(string) 
------------------------------------ */
function tep_get_orders_address($oid){
  $sql = "SELECT ao.value FROM ".TABLE_ADDRESS_ORDERS." ao 
    right join ".TABLE_ADDRESS." a
    on ao.address_id=a.id  
    WHERE ao.orders_id='".$oid."' 
    and a.fixed_option != '0'
    order by a.fixed_option";
  $query = tep_db_query($sql);
  $address_str = '';
  $address_arr = array();
  while($row = tep_db_fetch_array($query)){
    $address_arr[] = $row['value'];
  }
  if(!empty($address_arr)){
    $address_str = implode(' ',$address_arr);
  }
  return mb_substr($address_str,0,17,'UTF-8');
}

/* -------------------------------------
    功能: 检测商品是否缺失属性 
    参数: $check_type(boolean) 是否检测登录后的商品属性 
    返回值: 是否缺失(boolean) 
------------------------------------ */
function tep_check_less_product_option($check_type = false)
{
  global $cart;
  $return_array = array();
  $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>"); 
  $products = $cart->get_products();

  for ($i = 0, $n = sizeof($products); $i < $n; $i++) {
    $products_id_info = explode('_', $products[$i]['id']); 
    $products_exists_query = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$products_id_info[0]."'"); 
    $products_exists = tep_db_fetch_array($products_exists_query); 
    
    if ($products_exists) {
      $option_front_item_array = array(); 
      $option_front_item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where group_id = '".$products_exists['belong_to_option']."' and status = '1' and place_type = '0'");  
      while ($option_front_item = tep_db_fetch_array($option_front_item_query)) {
        $option_front_item_array[] = $option_front_item; 
      }
      
      $option_back_item_array = array(); 
      $option_back_item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where group_id = '".$products_exists['belong_to_option']."' and status = '1' and place_type = '1'");  
      while ($option_back_item = tep_db_fetch_array($option_back_item_query)) {
        $option_back_item_array[] = $option_back_item; 
      }
      if (empty($products[$i]['op_attributes'])) {
        if (!empty($option_front_item_array)) {
          $return_array[] = $products[$i]['id'];
          continue;
        } 
      } else {
        if (empty($option_front_item_array)) {
          $return_array[] = $products[$i]['id']; 
          continue;
        } else {
          $count_num = count($products[$i]['op_attributes']);
          $count_tmp_num = count($option_front_item_array);
          if ($count_num != $count_tmp_num) {
            $return_array[] = $products[$i]['id']; 
            continue;
          } 
          foreach ($products[$i]['op_attributes'] as $op_key => $op_value) {
            $op_key_array = explode('_', $op_key); 
            $option_item_exists_query  = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$op_key_array[1]."' and id = '".$op_key_array[3]."' and group_id = '".$products_exists['belong_to_option']."' and status = '1' and place_type = '0'"); 
            $option_item_exists = tep_db_fetch_array($option_item_exists_query); 
            if ($option_item_exists) {
              $c_option = @unserialize(stripslashes($option_item_exists['option']));
              if ($option_item_exists['type'] == 'radio') {
                if (!empty($c_option)) {
                  $op_single = false; 
                  foreach ($c_option['radio_image'] as $cr_key => $cr_value) {
                    if (trim(str_replace($replace_arr, '', nl2br(stripslashes($cr_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($op_value))))) {
                      $op_single = true;
                      break;
                    }
                  }
                  if (!$op_single) {
                    $return_array[] = $products[$i]['id']; 
                    continue;
                  }
                } else {
                  if (!empty($op_value)) {
                    $return_array[] = $products[$i]['id']; 
                    continue;
                  }
                } 
              } else if ($option_item_exists['type'] == 'text') {
                if (trim(str_replace($replace_arr, '', nl2br(stripslashes($c_option['itextarea'])))) != trim(str_replace($replace_arr, '', nl2br(stripslashes($op_value))))) {
                  $return_array[] = $products[$i]['id']; 
                  continue;
                }
              } else if ($option_item_exists['type'] == 'select') {
                if (!empty($c_option['se_option'])) {
                  $op_se_single = false; 
                  foreach ($c_option['se_option'] as $se_key => $se_value) {
                    if ($se_value == $op_value) {
                      $op_se_single = true;
                      break;
                    }
                  }
                  if (!$op_se_single) {
                    $return_array[] = $products[$i]['id']; 
                    continue;
                  }
                } else {
                  $return_array[] = $products[$i]['id']; 
                  continue;
                }
              }
            } else {
              $return_array[] = $products[$i]['id']; 
              continue;
            }
          }
        }
      }
      $ck_single = false; 
      if ($check_type) {
        $ck_single = true; 
      } else {
        if (!empty($products[$i]['ck_attributes'])) {
          $ck_single = true; 
        }
      }
      if ($ck_single) {
        if (empty($products[$i]['ck_attributes'])) {
          $count_op_num = 0;
        } else {
          $count_op_num = count($products[$i]['ck_attributes']);
        }
        if (empty($option_back_item_array)) {
          $count_tmp_op_num = 0;
        } else {
          $count_tmp_op_num = count($option_back_item_array);
        }
        if ($count_op_num != $count_tmp_op_num) {
          $return_array[] = $products[$i]['id']; 
          continue;
        }
        foreach ($products[$i]['ck_attributes'] as $cop_key => $cop_value) {
          $cop_key_array = explode('_', $cop_key); 
          $coption_item_exists_query  = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$cop_key_array[0]."' and id = '".$cop_key_array[2]."' and group_id = '".$products_exists['belong_to_option']."' and status = '1' and place_type = '1'"); 
          $coption_item_exists = tep_db_fetch_array($coption_item_exists_query); 
          if ($coption_item_exists) {
            $cop_option = @unserialize(stripslashes($coption_item_exists['option']));
            if ($coption_item_exists['type'] == 'radio') {
              if (!empty($cop_option)) {
                $cop_single = false; 
                foreach ($cop_option['radio_image'] as $cor_key => $cor_value) {
                  if (trim(str_replace($replace_arr, '', nl2br(stripslashes($cor_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($cop_value))))) {
                    $cop_single = true;
                    break;
                  }
                }
                if (!$cop_single) {
                  $return_array[] = $products[$i]['id']; 
                  continue;
                }
              } else {
                if (!empty($cop_value)) {
                  $return_array[] = $products[$i]['id']; 
                  continue;
                }
              }
            } else if ($coption_item_exists['type'] == 'text') {
              if (trim(str_replace($replace_arr, '', nl2br(stripslashes($cop_option['itextarea'])))) != trim(str_replace($replace_arr, '', nl2br(stripslashes($cop_value))))) {
                $return_array[] = $products[$i]['id']; 
                continue;
              }
            } else if ($coption_item_exists['type'] == 'select') {
              if (!empty($cop_option['se_option'])) {
                $cop_se_single = false; 
                foreach ($cop_option['se_option'] as $cse_key => $cse_value) {
                  if ($cse_value == $cop_value) {
                    $cop_se_single = true;
                    break;
                  }
                }
                if (!$cop_se_single) {
                  $return_array[] = $products[$i]['id']; 
                  continue;
                }
              } else {
                $return_array[] = $products[$i]['id']; 
                continue;
              }
            }
          } else {
            $return_array[] = $products[$i]['id']; 
            continue;
          }
        }
      }
    } else {
      $return_array[] = $products[$i]['id']; 
    }
  }
  return $return_array;
}

/* -------------------------------------
    功能: 检测商品预约是否缺失属性 
    参数: 无 
    返回值: 是否缺失(boolean) 
------------------------------------ */
function tep_pre_check_less_product_option($products_id)
{
  $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>"); 
  if (!empty($_SESSION['preorder_option_info'])) {
    $exists_products_query = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$products_id."'"); 
    $exists_products = tep_db_fetch_array($exists_products_query);
    if (!$exists_products) {
      return true; 
    }
    $group_info_query = tep_db_query("select id, is_preorder from ".TABLE_OPTION_GROUP." where id = '".$exists_products['belong_to_option']."'"); 
    $group_info = tep_db_fetch_array($group_info_query);
    if (!$group_info) {
      return true; 
    } 
    if ($group_info['is_preorder'] == '0') {
      return true; 
    }
    $tmp_op_array = array(); 
    $item_list_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where group_id = '".$exists_products['belong_to_option']."' and status = '1' and place_type = '1'"); 
    while ($item_list = tep_db_fetch_array($item_list_query)) {
      $tmp_op_array[] = $item_list['id']; 
    }
    if (empty($tmp_op_array)) {
      return true; 
    } 
    if (empty($_SESSION['preorder_option_info'])) {
      $op_num = 0;
    } else {
      $op_num = count($_SESSION['preorder_option_info']);
    }
    if (empty($tmp_op_array)) {
      $tmp_op_num = 0;
    } else {
      $tmp_op_num = count($tmp_op_array);
    }
    if ($op_num != $tmp_op_num) {
      return true; 
    }
    foreach ($_SESSION['preorder_option_info'] as $key => $value) {
      $tmp_op_info = explode('_', $key); 
      $coption_item_exists_query  = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$tmp_op_info[1]."' and id = '".$tmp_op_info[3]."' and group_id = '".$exists_products['belong_to_option']."' and status = '1' and place_type = '1'"); 
      $coption_item_exists = tep_db_fetch_array($coption_item_exists_query); 
      if ($coption_item_exists) {
        $cop_option = @unserialize(stripslashes($coption_item_exists['option']));
        if ($coption_item_exists['type'] == 'radio') {
          if (!empty($cop_option)) {
            $cop_single = false; 
            foreach ($cop_option['radio_image'] as $cor_key => $cor_value) {
              if (trim(str_replace($replace_arr, '', nl2br(stripslashes($cor_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($value))))) {
                $cop_single = true;
                break;
              }
            }
            if (!$cop_single) {
              return true; 
            }
          } else {
            if (!empty($value)) {
              return true; 
            }
          }
        } else if ($coption_item_exists['type'] == 'text') {
          if (trim(str_replace($replace_arr, '', nl2br(stripslashes($cop_option['itextarea'])))) != trim(str_replace($replace_arr, '', nl2br(stripslashes($value))))) {
            return true; 
          }
        } else if ($coption_item_exists['type'] == 'select') {
          if (!empty($cop_option['se_option'])) {
            $cop_se_single = false; 
            foreach ($cop_option['se_option'] as $cse_key => $cse_value) {
              if ($cse_value == $value) {
                $cop_se_single = true;
                break;
              }
            }
            if (!$cop_se_single) {
              return true; 
            }
          } else {
            return true; 
          }
        }
      } else {
        return true; 
      }
    }
  } else {
    $exists_products_query = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$products_id."'"); 
    $exists_products = tep_db_fetch_array($exists_products_query);
    if ($exists_products) {
      $group_info_query = tep_db_query("select id, is_preorder from ".TABLE_OPTION_GROUP." where id = '".$exists_products['belong_to_option']."' and is_preorder = '1'"); 
      $group_info = tep_db_fetch_array($group_info_query);
      if ($group_info) {
        $item_exists_query  = tep_db_query("select * from ".TABLE_OPTION_ITEM." where group_id = '".$group_info['id']."' and status = '1' and place_type = '1' limit 1"); 
        $item_exists = tep_db_fetch_array($item_exists_query); 
        if ($item_exists) {
          return true; 
        }
      }
    }
  }
  return false;
}

/* -------------------------------------
    功能: 检查前台预约的商品属性是否缺失 
    参数: $op_info_array(array) 属性信息 
    参数: $products_id(int) 商品id 
    返回值: 是否缺失(boolean) 
------------------------------------ */
function tep_pre_check_less_product_option_by_products_info($op_info_array, $products_id) 
{
  $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>"); 
  if (!empty($op_info_array)) {
    $exists_products_query = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$products_id."'"); 
    $exists_products = tep_db_fetch_array($exists_products_query);
    if (!$exists_products) {
      return true; 
    }
    $group_info_query = tep_db_query("select id from ".TABLE_OPTION_GROUP." where id = '".$exists_products['belong_to_option']."'"); 
    $group_info = tep_db_fetch_array($group_info_query);
    if (!$group_info) {
      return true; 
    }
  
    $tmp_op_array = array(); 
    $item_list_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where group_id = '".$exists_products['belong_to_option']."' and status = '1' and place_type = '0'"); 
    while ($item_list = tep_db_fetch_array($item_list_query)) {
      $tmp_op_array[] = $item_list['id']; 
    }
    
    if (empty($tmp_op_array)) {
      return true; 
    } 
    $op_num = count($op_info_array);
    $tmp_op_num = count($tmp_op_array);
    
    if ($op_num != $tmp_op_num) {
      return true; 
    }
    
    foreach ($op_info_array as $o_key => $o_value) {
      $tmp_op_key = explode('_', $o_key); 
      
      $option_item_exists_query  = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$tmp_op_key[1]."' and id = '".$tmp_op_key[3]."' and group_id = '".$exists_products['belong_to_option']."' and status = '1' and place_type = '0'"); 
      $option_item_exists = tep_db_fetch_array($option_item_exists_query); 
      if ($option_item_exists) {
        $cop_option = @unserialize(stripslashes($option_item_exists['option']));
        if ($option_item_exists['type'] == 'radio') {
          if (!empty($cop_option)) {
            $cop_single = false; 
            foreach ($cop_option['radio_image'] as $cor_key => $cor_value) {
              if (trim(str_replace($replace_arr, '', nl2br(stripslashes($cor_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($o_value))))) {
                $cop_single = true;
                break;
              }
            }
            if (!$cop_single) {
              return true; 
            }
          } else {
            if (!empty($o_value)) {
              return true; 
            }
          }
        } else if ($option_item_exists['type'] == 'text') {
          if (trim(str_replace($replace_arr, '', nl2br(stripslashes($cop_option['itextarea'])))) != trim(str_replace($replace_arr, '', nl2br(stripslashes($o_value))))) {
            return true; 
          }
        } else if ($option_item_exists['type'] == 'select') {
          if (!empty($cop_option['se_option'])) {
            $cop_se_single = false; 
            foreach ($cop_option['se_option'] as $cse_key => $cse_value) {
              if ($cse_value == $o_value) {
                $cop_se_single = true;
                break;
              }
            }
            if (!$cop_se_single) {
              return true; 
            }
          } else {
            return true; 
          }
        }
      } else {
        return true; 
      }
    }
  } else {
    $exists_products_query = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$products_id."'"); 
    $exists_products = tep_db_fetch_array($exists_products_query);
    if ($exists_products) {
      $group_info_query = tep_db_query("select id from ".TABLE_OPTION_GROUP." where id = '".$exists_products['belong_to_option']."'"); 
      $group_info = tep_db_fetch_array($group_info_query);
      if ($group_info) {
        $item_exists_query  = tep_db_query("select * from ".TABLE_OPTION_ITEM." where group_id = '".$group_info['id']."' and status = '1' and place_type = '0' limit 1"); 
        $item_exists = tep_db_fetch_array($item_exists_query); 
        if ($item_exists) {
          return true; 
        }
      }
    }
  }
  return false;
}

/* -------------------------------------
    功能: 检查该状态是否取消 
    参数: $status_id(int) 状态id 
    返回值: 是否取消(boolean) 
 ------------------------------------ */
function tep_order_transaction_status($status_id)
{
  $order_status_raw = tep_db_query("select is_cancle from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$status_id."'");
  $order_status = tep_db_fetch_array($order_status_raw);
  if ($order_status['is_cancle'] == '1') {
    return true;
  }
  return false;
}

/* -------------------------------------
    功能: 获取订单完成标识 
    参数: $orders_id(int) 订单id 
    返回值: 完成标识(string) 
 ------------------------------------ */
function tep_orders_transaction_finished($orders_id) 
{
  $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS." where orders_id='".$orders_id."'"));
  return $order['flag_qaf'];
}

/* -------------------------------------
    功能: 特殊处理日历重复的设置 
    参数: $type(int) 类型 
    参数: $cl_date(string) 日期 
    返回值: 特殊处理后的数据(string) 
 ------------------------------------ */
function tep_get_repeat_date($type,$cl_date){
            
    switch($type){

      case 1:
        $value = getdate(mktime(0,0,0,substr($cl_date,4,2),substr($cl_date,6,2),substr($cl_date,0,4))); 
        $value = $value['wday'];
        break;
      case 2:
        $value = substr($cl_date,6,2);
        break;
      case 3:
        $temp_value = substr($cl_date,6,2);
        $value = ceil($temp_value/7);
        $week = getdate(mktime(0,0,0,substr($cl_date,4,2),substr($cl_date,6,2),substr($cl_date,0,4)));
        $value = array($value,$week['wday']);
        break;
      case 4:
        $value = substr($cl_date,4,2).substr($cl_date,6,2);
        break;
    } 
    return $value;
}
/*----------------------------------
  功能: 通过产品ID获得产品的库存
  参数: $pid (int)类型  产品ID
  参数: $v_quantity (boolean)类型 虚拟库存 默认false不参加基数 true参加计算
  返回：根据基数和 产品（游戏币） 计算出商品个数 取整（小数省略）
----------------------------------*/
function tep_get_quantity($pid,$v_quantity=false){
  if($v_quantity){
    $sql = "SELECT products_attention_1_3,
      (`products_real_quantity`/`products_attention_1_3`) 
      + `products_virtual_quantity`  as quantity FROM 
      " .TABLE_PRODUCTS." WHERE products_id = '".$pid."' limit 1";
  }else{
    $sql = "SELECT products_attention_1_3,
      (`products_real_quantity`/`products_attention_1_3`) as quantity
      FROM 
      " .TABLE_PRODUCTS." WHERE products_id = '".$pid."' limit 1";
  }
  $query = tep_db_query($sql);
  if($row = tep_db_fetch_array($query)){
    if($row['products_attention_1_3']!=''&&$row['products_attention_1_3']!=0){
      return (int)($row['quantity']);
    }else{
      $sql = "SELECT products_attention_1_3,
      `products_real_quantity` as quantity FROM 
      " .TABLE_PRODUCTS." WHERE products_id = '".$pid."' limit 1";
      $query = tep_db_query($sql);
      if($row = tep_db_fetch_array($query)){
        return (int)($row['quantity']);
      }else{
        return 0;
      }
    }
  }else{
    $sql = "SELECT products_attention_1_3,
      `products_real_quantity` as quantity FROM 
      " .TABLE_PRODUCTS." WHERE products_id = '".$pid."' limit 1";
    $query = tep_db_query($sql);
    if($row = tep_db_fetch_array($query)){
      return (int)($row['quantity']);
    }else{
      return 0;
    }
  }
}
/*----------------------------------
  功能: 通过产品ID获得产品汇率(基数)
  参数: $pid (int)类型  产品ID
  返回：基数
----------------------------------*/
function tep_get_radices($pid){
    $sql = "SELECT products_attention_1_3 as radices FROM 
      " .TABLE_PRODUCTS." WHERE products_id = '".$pid."' limit 1";
    $query = tep_db_query($sql);
    if($row = tep_db_fetch_array($query)){
      return (int)$row['radices'];
    }else{
      return 1;
    }
}
/*------------------------------
  功能: 获取相应的邮件模板 
  参数: $mail_flag(string) 邮件模板标识
  参数: $site_id(int) 所属网站
  返回: 邮件模板的标题、内容 
  -----------------------------*/
function tep_get_mail_templates($mail_flag,$site_id){

  $mail_query = tep_db_query("select title,contents from ". TABLE_MAIL_TEMPLATES ." where flag='".$mail_flag."' and site_id='".$site_id."'");
  $mail_array = tep_db_fetch_array($mail_query);
  tep_db_free_result($mail_query);

  return array('title'=>$mail_array['title'],'contents'=>$mail_array['contents']);
}
/*----------------------------------
  功能: 通过CID判断客户是否存在
  参数: $cid (int)类型  客户ID
  返回：如果存在返回 详细信息 如果不存在 返回FALSE
----------------------------------*/
function tep_is_customer_by_id($cid){
  $customer_sql = "SELECT * FROM `".TABLE_CUSTOMERS."` 
    WHERE `customers_id` = '".$cid."' limit 1";
  $customer_query = tep_db_query($customer_sql);
  if($customer_row = tep_db_fetch_array($customer_query)){
    return $customer_row;
  }else{
    return false;
  }
}
