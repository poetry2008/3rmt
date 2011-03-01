<?php
/*
  $Id$
*/


//无权限修改 提示401
function forward401()
{ 
  header($_SERVER["SERVER_PROTOCOL"] . " 401Not Found");
  //  require("/home/hansir/project/OSC_3RMT/jp/".DIR_WS_MODULES  . '401.html');
  require( DIR_WS_MODULES. '401.html');
  exit;
  //throw new Exception();
}
//在条件成立的时候，401
function forward401If($condition)
{
  if ($condition)
  {
    forward403();
  }
}
//在条件不成立时，401
function forward401Unless($condition)
{
  if (!$condition)
  {
    forward401();
  }
}

//取得minitor 的信息
function tep_minitor_info(){
  $show_div = false;
  $errorString = array();
  $monitors  = tep_db_query("select id ,name,url from monitor m where m.enable='on'");
  while($monitor= tep_db_fetch_array($monitors)){
    $fiftheenbefore = date('Y-m-d H:i:s',time()-60*15);
    $logIn15 = tep_db_query("select * from monitor_log where ng = 1 and m_id =".$monitor['id'].' and created_at > "'.$fiftheenbefore.'"');
    $tmpRow = tep_db_fetch_array($logIn15);
    if(mysql_num_rows($logIn15)){ //十五分钟内多于两件

      $tmpString  = '回線障害発生： '.$tmpRow['name'].' <font
        class="error_monitor">'.date('m月d日H時i分s秒',strtotime($tmpRow['created_at'])).'</font><br/><a ';
      if($show_div){
      $tmpString .='
        onMouseOver="show_monitor_error(\'minitor_'.$monitor['name'].'\',1,this)" 
        onMouseOut="show_monitor_error(\'minitor_'.$monitor['name'].'\',0,this)"';
      }
      $tmpString .=  'id="moni_'.$tmpRow['name'].'" class="monitor"
        href="'.$monitor['url'].'"
        target="_blank">こちら</a>をクリックして状況を確認してください。</div>';
      $tmpString2 = "<div class='monitor_error' style='display:none;' id='minitor_".$monitor['name']."'>";
      $tmpString2.= '<table width="100%"><tr><td>'.$tmpRow['created_at']."</td><td
      width='50%'>".nl2br($tmpRow['obj'])."</td></tr>";
    while($tmpRow2 = tep_db_fetch_array($logIn15)){
    $tmpString2.= '<tr><td colspan="2"><hr></td></tr>';
    $tmpString2.= '<tr><td>'.$tmpRow2['created_at']."</td><td>".nl2br($tmpRow2['obj'])."</td></tr>";
    }
    $tmpString2.= "</table>";
    $errorString[] = $tmpString.$tmpString2;
  }
    else {
    $log = "select name,obj, created_at from monitor_log where ng =1 and m_id = ".$monitor['id']. " order by id  desc limit 1";
    $logsResult = tep_db_fetch_array(tep_db_query($log));
    if ($logsResult){
      $aString = '回線障害の最終日： ' . $logsResult['name'] . ' <a ';
      if($show_div){
      $aString.=  'onMouseOver="show_monitor_error(\'minitor_'.$logsResult['name'].'\',1,this)"
        onMouseOut="show_monitor_error(\'minitor_'.$logsResult['name'].'\',0,this)"';
      }
      $aString.=  'class="monitor_right" id="moni_'.$logsResult['name'].'" href="'.$monitor['url'].'"  target="_blank">'.date('m月d日H時i分s秒',strtotime($logsResult['created_at'])).'</a>';
        $aString.= '<div class="monitor_error" style="display:none;" id ="minitor_'.$logsResult['name'].'">';
        $aString.= '<table
          width="100%"><tr><td>'.$logsResult['created_at']."</td><td
          width='50%'>".nl2br($logsResult['obj'])."</td></tr>";
        $aString.= '</table></div>';
        $errorString[] = $aString;
      }
    }
  }
  if(count($errorString)<1){
        $no_error_string = '<tr><td></td><td align="right"><font
          color="green">システムの動作状況： 正常</font></td></tr>';
  }
  $returnString = '';
  foreach ($errorString as $error){
    $returnString .= '<tr><td></td><td align="right">'.$error.'</td></tr>';
  }
  if($no_error_string!=""){
    return $no_error_string;
  }
  return $returnString;

}

////
// Redirect to another page or site
  function tep_redirect($url) {
    global $logger;

    header('Location: ' . $url);

    if (STORE_PAGE_PARSE_TIME == 'true') {
      if (!is_object($logger)) $logger = new logger;
      $logger->timer_stop();
    }

    exit;
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
    $string = ereg_replace(' +', ' ', $string);

    return preg_replace("/[<>]/", '_', $string);
  }

  function tep_customers_name($customers_id) {
    $customers = tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $customers_id . "'");
    $customers_values = tep_db_fetch_array($customers);

    return tep_get_fullname($customers_values['customers_firstname'], $customers_values['customers_lastname']);
  }

  function tep_get_path($current_category_id = '') {
    global $cPath_array;

    if ($current_category_id == '') {
      $cPath_new = implode('_', $cPath_array);
    } else {
      if (sizeof($cPath_array) == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
        $last_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . $cPath_array[(sizeof($cPath_array)-1)] . "'");
        $last_category = tep_db_fetch_array($last_category_query);
        $current_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . $current_category_id . "'");
        $current_category = tep_db_fetch_array($current_category_query);
        if ($last_category['parent_id'] == $current_category['parent_id']) {
          for ($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        } else {
          for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        }
        $cPath_new .= '_' . $current_category_id;
        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    }

    return 'cPath=' . $cPath_new;
  }

  function tep_get_all_get_params($exclude_array = '') {
    global $_GET;

    if ($exclude_array == '') $exclude_array = array();

    $get_url = '';

    reset($_GET);
    while (list($key, $value) = each($_GET)) {
      if (($key != tep_session_name()) && ($key != 'error') && (!tep_in_array($key, $exclude_array))) $get_url .= $key . '=' . rawurlencode($value) . '&';
    }

    return $get_url;
  }

  function tep_date_long($raw_date) {
    if (is_numeric($raw_date)) $raw_date = date('Y-m-d H:i:s', $raw_date);
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = (int)substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    $returntime = strftime(DATE_FORMAT_LONG, mktime($hour,$minute,$second,$month,$day,$year));
    $oarr = array('January','February','March','April','May','June','July','August','September','October','November','December');
    $newarr = array('1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月');
    $returntime = str_replace($oarr, $newarr, $returntime);
    
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

  function tep_datetime_short($raw_datetime) {
    if ( ($raw_datetime == '0000-00-00 00:00:00') || ($raw_datetime == '') ) return false;

    $year = (int)substr($raw_datetime, 0, 4);
    $month = (int)substr($raw_datetime, 5, 2);
    $day = (int)substr($raw_datetime, 8, 2);
    $hour = (int)substr($raw_datetime, 11, 2);
    $minute = (int)substr($raw_datetime, 14, 2);
    $second = (int)substr($raw_datetime, 17, 2);

    return strftime(DATE_TIME_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
  }

  function tep_array_merge($array1, $array2, $array3 = '') {
    if ($array3 == '') $array3 = array();
    if (!is_array($array2)) $array2 = array();
    if (!is_array($array1)) $array1 = array();
    if (function_exists('array_merge')) {
      $array_merged = array_merge($array1, $array2, $array3);
    } else {
      while (list($key, $val) = each($array1)) $array_merged[$key] = $val;
      while (list($key, $val) = each($array2)) $array_merged[$key] = $val;
      if (sizeof($array3) > 0) while (list($key, $val) = each($array3)) $array_merged[$key] = $val;
    }

    return (array) $array_merged;
  }

  function tep_in_array($lookup_value, $lookup_array) {
    if (function_exists('in_array')) {
      if (in_array($lookup_value, $lookup_array)) return true;
    } else {
      reset($lookup_array);
      while (list($key, $value) = each($lookup_array)) {
        if ($value == $lookup_value) return true;
      }
    }

    return false;
  }

  function tep_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
    global $languages_id;

    if (!is_array($category_tree_array)) $category_tree_array = array();
    if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

    if ($include_itself) {
      $category_query = tep_db_query("select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . $languages_id . "' and cd.categories_id = '" . $parent_id . "' and cd.site_id='0'");
      $category = tep_db_fetch_array($category_query);
      $category_tree_array[] = array('id' => $parent_id, 'text' => $category['categories_name']);
    }

    $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . $languages_id . "' and c.parent_id = '" . $parent_id . "' and site_id ='0' order by c.sort_order, cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      if ($exclude != $categories['categories_id']) $category_tree_array[] = array('id' => $categories['categories_id'], 'text' => $spacing . $categories['categories_name']);
      $category_tree_array = tep_get_category_tree($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
    }

    return $category_tree_array;
  }

  function tep_get_products_tree($cid){
      $category_tree_array = array();
      $products_query = tep_db_query("select * from products_description pd,products_to_categories p2c where pd.products_id=p2c.products_id and pd.site_id=0 and p2c.categories_id='".$cid."' order by pd.products_name asc");
      while($p = tep_db_fetch_array($products_query)){
        $category_tree_array[] = array('id' => $p['products_id'], 'text' => $spacing . $spacing . $p['products_name']);
      }
      return $category_tree_array;
  }
/*
  function tep_get_products_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '') {
    global $languages_id;

    if (!is_array($category_tree_array)) $category_tree_array = array();
    if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

    $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . $languages_id . "' and c.parent_id = '" . $parent_id . "' and site_id ='0' order by c.sort_order, cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      //if ($exclude != $categories['categories_id']) $category_tree_array[] = array('id' => $categories['categories_id'], 'text' => $spacing . $categories['categories_name']);
      if ($exclude != $categories['categories_id']) $category_tree_array[] = array('id' => '', 'text' => $spacing . $categories['categories_name']);
      $category_tree_array = tep_get_products_tree($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
      
      $products_query = tep_db_query("select * from products_description pd,products_to_categories p2c where pd.products_id=p2c.products_id and pd.site_id=0 and p2c.categories_id='".$categories['categories_id']."'");
      while($p = tep_db_fetch_array($products_query)){
        //exit(1);
        $category_tree_array[] = array('id' => $p['products_id'], 'text' => $spacing . $spacing . $p['products_name']);
      }
    }

    return $category_tree_array;
  }
*/
  function tep_draw_products_pull_down($name, $parameters = '', $exclude = '') {
    global $currencies, $languages_id;

    if ($exclude == '') {
      $exclude = array();
    }
    $select_string = '<select name="' . $name . '"';
    if ($parameters) {
      $select_string .= ' ' . $parameters;
    }
    $select_string .= '>';
    $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' order by products_name and site_id = '0'");
    while ($products = tep_db_fetch_array($products_query)) {
      if (!tep_in_array($products['products_id'], $exclude)) {
        $select_string .= '<option value="' . $products['products_id'] . '">' . $products['products_name'] . ' (' . $currencies->format($products['products_price']) . ')</option>';
      }
    }
    $select_string .= '</select>';

    return $select_string;
  }

  function tep_options_name($options_id) {
    global $languages_id;

    $options = tep_db_query("select products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $options_id . "' and language_id = '" . $languages_id . "'");
    $options_values = tep_db_fetch_array($options);

    return $options_values['products_options_name'];
  }

  function tep_values_name($values_id) {
    global $languages_id;

    $values = tep_db_query("select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $values_id . "' and language_id = '" . $languages_id . "'");
    $values_values = tep_db_fetch_array($values);

    return $values_values['products_options_values_name'];
  }

  function tep_info_image($image, $alt, $width = '', $height = '', $site_id = '0') {
    //if ( ($image) && (file_exists(DIR_FS_CATALOG_IMAGES . $image)) ) {
      //$image = tep_image(DIR_WS_CATALOG_IMAGES . $image, $alt, $width, $height);
    //echo tep_get_upload_dir($site_id) . $image;
    if ( ($image) && (file_exists(tep_get_upload_dir($site_id). $image)) ) {
      $image = tep_image(tep_get_web_upload_dir($site_id). $image, $alt, $width, $height);
    } else {
      // TEXT_IMAGE_NONEXISTENT 数据表和程序中都未发现
      $image = TEXT_IMAGE_NONEXISTENT;
    }

    return $image;
  }

  function tep_break_string($string, $len, $break_char = '-') {
    /*
    $l = 0;
    $output = '';
    for ($i = 0; $i < strlen($string); $i++) {
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

  function tep_get_country_name($country_id) {
    $country_query = tep_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . $country_id . "'");

    if (!tep_db_num_rows($country_query)) {
      return $country_id;
    } else {
      $country = tep_db_fetch_array($country_query);
      return $country['countries_name'];
    }
  }

  function tep_get_zone_name($zone_id) {
    $zone_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_id = '" . $zone_id . "'");

    if (!tep_db_num_rows($zone_query)) {
      return $zone_id;
    } else {
      $zone = tep_db_fetch_array($zone_query);
      return $zone['zone_name'];
    }
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

  function tep_browser_detect($component) {
    global $HTTP_USER_AGENT;

    return stristr($HTTP_USER_AGENT, $component);
  }

  function tep_tax_classes_pull_down($parameters, $selected = '') {
    $select_string = '<select ' . $parameters . '>';
    $classes_query = tep_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
    while ($classes = tep_db_fetch_array($classes_query)) {
      $select_string .= '<option value="' . $classes['tax_class_id'] . '"';
      if ($selected == $classes['tax_class_id']) $select_string .= ' SELECTED';
      $select_string .= '>' . $classes['tax_class_title'] . '</option>';
    }
    $select_string .= '</select>';

    return $select_string;
  }

  function tep_geo_zones_pull_down($parameters, $selected = '') {
    $select_string = '<select ' . $parameters . '>';
    $zones_query = tep_db_query("select geo_zone_id, geo_zone_name from " . TABLE_GEO_ZONES . " order by geo_zone_name");
    while ($zones = tep_db_fetch_array($zones_query)) {
      $select_string .= '<option value="' . $zones['geo_zone_id'] . '"';
      if ($selected == $zones['geo_zone_id']) $select_string .= ' SELECTED';
      $select_string .= '>' . $zones['geo_zone_name'] . '</option>';
    }
    $select_string .= '</select>';

    return $select_string;
  }
  
  function tep_check_footer_string() {
    if(!file_exists(DIR_FS_CATALOG . 'includes/footer.php')) {
      return true;
    }
  
    $footerFile = DIR_FS_CATALOG . 'includes/footer.php';
    $handle = fopen($footerFile, "r");
    $contents = fread($handle, filesize($footerFile));
    fclose($handle);  
    if(!ereg("FOOTER_TEXT_BODY", $contents)) {
      return true;
    }
    if(!ereg('DigitalStudio', $contents)) {
      return true;
    }
  
    return false;
  }

  function tep_get_geo_zone_name($geo_zone_id) {
    $zones_query = tep_db_query("select geo_zone_name from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . $geo_zone_id . "'");

    if (!tep_db_num_rows($zones_query)) {
      $geo_zone_name = $geo_zone_id;
    } else {
      $zones = tep_db_fetch_array($zones_query);
      $geo_zone_name = $zones['geo_zone_name'];
    }

    return $geo_zone_name;
  }


//////////////////////////////////////////////////////////////////////////////////////////
//
// Function : tep_format_address
//
// Arguments    : customers_id, address_id, html
//
// Return   : properly formatted address
//
// Description  : This function will lookup the Addres format from the countries database
//        and properly format the address label.
//
//////////////////////////////////////////////////////////////////////////////////////////

// 2003-06-06 add_telephone
//  function tep_address_format($address_format_id, $address, $html, $boln, $eoln) {
  function tep_address_format($address_format_id, $address, $html, $boln, $eoln, $telephone=TRUE) {
    $address_format_query = tep_db_query("select address_format as format from " . TABLE_ADDRESS_FORMAT . " where address_format_id = '" . $address_format_id . "'");
    $address_format = tep_db_fetch_array($address_format_query);

    $company = tep_output_string_protected($address['company']);
    $firstname = tep_output_string_protected($address['firstname']);
    $lastname = tep_output_string_protected($address['lastname']);
  
  $name_f = tep_output_string_protected($address['name_f']);
  
    $street = tep_output_string_protected($address['street_address']);
    $suburb = tep_output_string_protected($address['suburb']);
    $city = tep_output_string_protected($address['city']);
    $state = tep_output_string_protected($address['state']);
    $country_id = $address['country_id'];
    $zone_id = $address['zone_id'];
    $postcode = tep_output_string_protected($address['postcode']);
    $zip = $postcode;
    $country = tep_get_country_name($country_id);
    $state = tep_get_zone_code($country_id, $zone_id, $state);
    $statename = $state; // for Japanese Localize
// 2003-06-06 add_telephone
    if ($telephone) { $telephone = tep_output_string_protected($address['telephone']); }

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
    if ($country == '') $country = tep_output_string_protected($address['country']);
    if ($state != '') $statecomma = $state . ', ';

    $fmt = $address_format['format'];
    eval("\$address = \"$fmt\";");
    $address = stripslashes($address);

    if ( (ACCOUNT_COMPANY == 'true') && (tep_not_null($company)) ) {
      $address = $company . $cr . $address;
    }

    return $boln . $address . $eoln;
  }

  ////////////////////////////////////////////////////////////////////////////////////////////////
  //
  // Function    : tep_get_zone_code
  //
  // Arguments   : country           country code string
  //               zone              state/province zone_id
  //               def_state         default string if zone==0
  //
  // Return      : state_prov_code   state/province code
  //
  // Description : Function to retrieve the state/province code (as in FL for Florida etc)
  //
  ////////////////////////////////////////////////////////////////////////////////////////////////
  function tep_get_zone_code($country, $zone, $def_state) {

    $state_prov_query = tep_db_query("select zone_code from " . TABLE_ZONES . " where zone_country_id = '" . $country . "' and zone_id = '" . $zone . "'");

    if (!tep_db_num_rows($state_prov_query)) {
      $state_prov_code = $def_state;
    }
    else {
      $state_prov_values = tep_db_fetch_array($state_prov_query);
      $state_prov_code = $state_prov_values['zone_code'];
    }
    
    return $state_prov_code;
  }

  function tep_get_uprid($prid, $params) {
    $uprid = $prid;
    if ( (is_array($params)) && (!strstr($prid, '{')) ) {
      while (list($option, $value) = each($params)) {
        $uprid = $uprid . '{' . $option . '}' . $value;
      }
    }

    return $uprid;
  }

  function tep_get_prid($uprid) {
    $pieces = explode ('{', $uprid);

    return $pieces[0];
  }

  function tep_get_languages() {
    $languages_query = tep_db_query("select languages_id, name, code, image, directory from " . TABLE_LANGUAGES . " order by sort_order");
    while ($languages = tep_db_fetch_array($languages_query)) {
      $languages_array[] = array('id' => $languages['languages_id'],
                                 'name' => $languages['name'],
                                 'code' => $languages['code'],
                                 'image' => $languages['image'],
                                 'directory' => $languages['directory']
                                );
    }

    return $languages_array;
  }

  function tep_get_category_name($category_id, $language_id, $site_id = 0, $default = false) {
    if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
      $site_id = 0;
    }
    $category_query = tep_db_query("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id='".$site_id."'");
    $category = tep_db_fetch_array($category_query);

    return $category['categories_name'];
  }
  
  // categories.php
  function tep_get_category_romaji($category_id, $language_id, $site_id = 0, $default = false) {
    if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
      $site_id = 0;
    }
    $category_query = tep_db_query("select romaji from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id='".$site_id."'");
    $category = tep_db_fetch_array($category_query);

    return $category['romaji'];
  }

  function tep_get_category_image2($category_id, $language_id, $site_id = 0, $default = false) {
    if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
      $site_id = 0;
    }
    $category_query = tep_db_query("select categories_image2 from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
    $category = tep_db_fetch_array($category_query);

    return $category['categories_image2'];
  }

  function tep_get_category_meta_text($category_id, $language_id, $site_id = 0, $default = false) {
    if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
      $site_id = 0;
    }
    $category_query = tep_db_query("select categories_meta_text from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
    $category = tep_db_fetch_array($category_query);

    return $category['categories_meta_text'];
  }
  
  function tep_get_seo_name($category_id, $language_id, $site_id = 0, $default = false) { 
    if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
      $site_id = 0;
    }
    $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
    $category = tep_db_fetch_array($category_query);

    return $category['seo_name'];
  }
  
  function tep_get_seo_description($category_id, $language_id, $site_id = 0, $default = false) { 
    if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
      $site_id = 0;
    }
    $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
    $category = tep_db_fetch_array($category_query);

    return $category['seo_description'];
  }
  
  function tep_get_categories_header_text($category_id, $language_id, $site_id = 0, $default = false) {
    if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
      $site_id = 0;
    }
    $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
    $category = tep_db_fetch_array($category_query);

    return $category['categories_header_text'];
  }
  
  function tep_get_categories_footer_text($category_id, $language_id, $site_id = 0, $default = false) {
    if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
      $site_id = 0;
    }
    $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
    $category = tep_db_fetch_array($category_query);

    return $category['categories_footer_text'];
  }

  function tep_get_text_information($category_id, $language_id, $site_id = 0, $default = false) {
    if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
      $site_id = 0;
    }
    $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
    $category = tep_db_fetch_array($category_query);
    return $category['text_information'];
  }
  
  function tep_get_meta_keywords($category_id, $language_id, $site_id = 0 , $default = false) {
    if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
      $site_id = 0;
    }
    $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
    $category = tep_db_fetch_array($category_query);

    return $category['meta_keywords'];
  }

  function tep_get_meta_description($category_id, $language_id, $site_id = 0, $default = false) { 
    if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
      $site_id = 0;
    }
    $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id ."'");
    $category = tep_db_fetch_array($category_query);

    return $category['meta_description'];
  }

  function tep_get_orders_status_name($orders_status_id, $language_id = '') {
    global $languages_id;

    if (!$language_id) $language_id = $languages_id;
    $orders_status_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . $orders_status_id . "' and language_id = '" . $language_id . "'");
    $orders_status = tep_db_fetch_array($orders_status_query);

    return $orders_status['orders_status_name'];
  }
  
  function tep_get_orders_status_id($orders_id, $language_id = '') {
    global $languages_id;

    if (!$language_id) $language_id = $languages_id;
    $orders_query = tep_db_query("select * from ".TABLE_ORDERS." where orders_id='".$orders_id."'");
    $orders = tep_db_fetch_array($orders_query);
    return $orders['orders_status'];
  }

  // get all orders status
  function tep_get_orders_status() {
    global $languages_id;

    $orders_status_array = array();
    $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "' order by orders_status_id");
    while ($orders_status = tep_db_fetch_array($orders_status_query)) {
      $orders_status_array[] = array('id' => $orders_status['orders_status_id'],
                                     'text' => $orders_status['orders_status_name']
                                    );
    }

    return $orders_status_array;
  }

  function tep_get_products_name($product_id, $language_id = 0, $site_id = 0, $default = false) {
    //echo $product_id,$language_id,$site_id;
    global $languages_id;

    if ($language_id == 0) $language_id = $languages_id;
    if ($default){
      $product_query = tep_db_query("
      select products_name 
      from " . TABLE_PRODUCTS_DESCRIPTION . " 
      where products_id = '" . $product_id . "' 
        and language_id = '" . $language_id . "'
        and (site_id ='".$site_id."' or site_id = '0')
      order by site_id desc
      ");
    } else {
      $product_query = tep_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $product_id . "' and language_id = '" . $language_id . "' and site_id ='".$site_id."'");
    }
    $product = tep_db_fetch_array($product_query);

    return $product['products_name'];
  }


   function tep_get_products_description($product_id, $language_id, $site_id = 0, $default = false) {
    if ($default) {
      $product_query = tep_db_query("select products_description,site_id from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $product_id . "' and language_id = '" . $language_id . "' and (site_id ='".$site_id."' or site_id='0') order by site_id desc");
    } else {
      $product_query = tep_db_query("select products_description,site_id from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $product_id . "' and language_id = '" . $language_id . "' and site_id ='".$site_id."'");
    }
    $product = tep_db_fetch_array($product_query);
  
    if ($product['site_id']==0){
      return replace_store_name($product['products_description'],null,$product['site_id']);
    }
      return $product['products_description'];
  }
  
  function tep_get_products_description_mobile($product_id, $language_id, $site_id = 0) {
    $product_query = tep_db_query("select products_description_mobile from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $product_id . "' and language_id = '" . $language_id . "' and site_id ='".$site_id."'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_description_mobile'];
  }
  
  function tep_get_products_url($product_id, $language_id, $site_id = 0) {
    $product_query = tep_db_query("
        select products_url 
        from " . TABLE_PRODUCTS_DESCRIPTION . " 
        where products_id = '" . $product_id . "' 
          and language_id = '" . $language_id . "' 
          and site_id='".$site_id."'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_url'];
  }

////
// Return the manufacturers URL in the needed language
// TABLES: manufacturers_info
  function tep_get_manufacturer_url($manufacturer_id, $language_id) {
    $manufacturer_query = tep_db_query("select manufacturers_url from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . $manufacturer_id . "' and languages_id = '" . $language_id . "'");
    $manufacturer = tep_db_fetch_array($manufacturer_query);

    return $manufacturer['manufacturers_url'];
  }

////
// Wrapper for class_exists() function
// This function is not available in all PHP versions so we test it before using it.
  function tep_class_exists($class_name) {
    if (function_exists('class_exists')) {
      return class_exists($class_name);
    } else {
      return true;
    }
  }

////
// Count how many products exist in a category
// TABLES: products, products_to_categories, categories
  function tep_products_in_category_count($categories_id, $include_deactivated = false) {
    $products_count = 0;

    if ($include_deactivated) {
      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . $categories_id . "'");
    } else {
      //$products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status != '0' and p2c.categories_id = '" . $categories_id . "'");
      $products_query = tep_db_query("select count(*) as total from " .  TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . $categories_id . "'");
    }

    $products = tep_db_fetch_array($products_query);

    $products_count += $products['total'];

    $childs_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . $categories_id . "'");
    if (tep_db_num_rows($childs_query)) {
      while ($childs = tep_db_fetch_array($childs_query)) {
        $products_count += tep_products_in_category_count($childs['categories_id'], $include_deactivated);
      }
    }

    return $products_count;
  }

////
// Count how many subcategories exist in a category
// TABLES: categories
  function tep_childs_in_category_count($categories_id) {
    $categories_count = 0;

    $categories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . $categories_id . "'");
    while ($categories = tep_db_fetch_array($categories_query)) {
      $categories_count++;
      $categories_count += tep_childs_in_category_count($categories['categories_id']);
    }

    return $categories_count;
  }

////
// Returns an array with countries
// TABLES: countries
  function tep_get_countries($default = '') {
    $countries_array = array();
    if ($default) {
      $countries_array[] = array('id' => '',
                                 'text' => $default);
    }
    $countries_query = tep_db_query("select countries_id, countries_name from " . TABLE_COUNTRIES . " order by countries_name");
    while ($countries = tep_db_fetch_array($countries_query)) {
      $countries_array[] = array('id' => $countries['countries_id'],
                                 'text' => $countries['countries_name']);
    }

    return $countries_array;
  }

////
// return an array with country zones
  function tep_get_country_zones($country_id) {
    $zones_array = array();
    $zones_query = tep_db_query("select zone_id, zone_name from " . TABLE_ZONES . " where zone_country_id = '" . $country_id . "' order by " . ($country_id == 107 ? "zone_code" : "zone_name"));
    while ($zones = tep_db_fetch_array($zones_query)) {
      $zones_array[] = array('id' => $zones['zone_id'],
                             'text' => $zones['zone_name']);
    }

    return $zones_array;
  }

  function tep_prepare_country_zones_pull_down($country_id = '') {
// preset the width of the drop-down for Netscape
    $pre = '';
    if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) {
      for ($i=0; $i<45; $i++) $pre .= '&nbsp;';
    }

    $zones = tep_get_country_zones($country_id);

    if (sizeof($zones) > 0) {
      $zones_select = array(array('id' => '', 'text' => PLEASE_SELECT));
      $zones = tep_array_merge($zones_select, $zones);
    } else {
      $zones = array(array('id' => '', 'text' => TYPE_BELOW));
// create dummy options for Netscape to preset the height of the drop-down
      if ( (!tep_browser_detect('MSIE')) && (tep_browser_detect('Mozilla/4')) ) {
        for ($i=0; $i<9; $i++) {
          $zones[] = array('id' => '', 'text' => $pre);
        }
      }
    }

    return $zones;
  }

////
// Alias function for Store configuration values in the Administration Tool
  function tep_cfg_pull_down_country_list($country_id) {
    return tep_draw_pull_down_menu('configuration_value', tep_get_countries(), $country_id);
  }

  function tep_cfg_pull_down_zone_list($zone_id) {
    return tep_draw_pull_down_menu('configuration_value', tep_get_country_zones(STORE_COUNTRY), $zone_id);
  }

  function tep_cfg_pull_down_tax_classes($tax_class_id, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $tax_class_query = tep_db_query("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
    while ($tax_class = tep_db_fetch_array($tax_class_query)) {
      $tax_class_array[] = array('id' => $tax_class['tax_class_id'],
                                 'text' => $tax_class['tax_class_title']);
    }

    return tep_draw_pull_down_menu($name, $tax_class_array, $tax_class_id);
  }

////
// Function to read in text area in admin
 function tep_cfg_textarea($text) {
    return tep_draw_textarea_field('configuration_value', false, 35, 5, $text);
  }


////
// Sets the status of a banner
  function tep_set_banner_status($banners_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_BANNERS . " set status = '1', expires_impressions = NULL, expires_date = NULL, date_status_change = NULL where banners_id = '" . $banners_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_BANNERS . " set status = '0', date_status_change = now() where banners_id = '" . $banners_id . "'");
    } else {
      return -1;
    }
  }

////
// Sets the status of a product
  function tep_set_product_status($products_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '1', products_last_modified = now() where products_id = '" . $products_id . "'");
    } elseif ($status == '2') {
      return tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '2', products_last_modified = now() where products_id = '" . $products_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '0', products_last_modified = now() where products_id = '" . $products_id . "'");
    } elseif ($status == '3') {
      return tep_db_query("update " . TABLE_PRODUCTS . " set products_status = '3', products_last_modified = now() where products_id = '" . $products_id . "'");
    } else {
      return -1;
    }
  }

////
// Sets the status of a product on special
/*
  function tep_set_specials_status($specials_id, $status) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_SPECIALS . " set status = '1', expires_date = NULL, date_status_change = NULL where specials_id = '" . $specials_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_SPECIALS . " set status = '0', date_status_change = now() where specials_id = '" . $specials_id . "'");
    } else {
      return -1;
    }
  }
*/
////
// Sets timeout for the current script.
// Cant be used in safe mode.
  function tep_set_time_limit($limit) {
    if (!get_cfg_var('safe_mode')) {
      set_time_limit($limit);
    }
  }

////
// Alias function for Store configuration values in the Administration Tool
  function tep_cfg_select_option($select_array, $key_value, $key = '') {
    $string = '';
    for ($i = 0, $n = sizeof($select_array); $i < $n; $i++) {
      $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
      $string .= '<br><input type="radio" name="' . $name . '" value="' . $select_array[$i] . '"';
      if ($key_value == $select_array[$i]) $string .= ' CHECKED';
      $string .= '> ' . $select_array[$i];
    }

    return $string;
  }

////
// Alias function for module configuration keys
  function tep_mod_select_option($select_array, $key_name, $key_value) {
    reset($select_array);
    while (list($key, $value) = each($select_array)) {
      if (is_int($key)) $key = $value;
      $string .= '<br><input type="radio" name="configuration[' . $key_name . ']" value="' . $key . '"';
      if ($key_value == $key) $string .= ' CHECKED';
      $string .= '> ' . $value;
    }

    return $string;
  }

////
// Retreive server information
  function tep_get_system_information() {
    global $_SERVER;

    $db_query = tep_db_query("select now() as datetime");
    $db = tep_db_fetch_array($db_query);

    list($system, $host, $kernel) = preg_split('/[\s,]+/', @exec('uname -a'), 5);

    return array('date' => tep_datetime_short(date('Y-m-d H:i:s')),
                 'system' => $system,
                 'kernel' => $kernel,
                 'host' => $host,
                 'ip' => gethostbyname($host),
                 'uptime' => @exec('uptime'),
                 'http_server' => $_SERVER['SERVER_SOFTWARE'],
                 'php' => PHP_VERSION,
                 'zend' => (function_exists('zend_version') ? zend_version() : ''),
                 'db_server' => DB_SERVER,
                 'db_ip' => gethostbyname(DB_SERVER),
                 'db_version' => 'MySQL ' . (function_exists('mysql_get_server_info') ? mysql_get_server_info() : ''),
                 'db_date' => tep_datetime_short($db['datetime']));
  }

  function tep_get_uploaded_file($filename) {
    if (isset($_FILES[$filename])) {
      $uploaded_file = array('name' => $_FILES[$filename]['name'],
                             'type' => $_FILES[$filename]['type'],
                             'size' => $_FILES[$filename]['size'],
                             'tmp_name' => $_FILES[$filename]['tmp_name']);
    } elseif (isset($GLOBALS['HTTP_POST_FILES'][$filename])) {
      global $HTTP_POST_FILES;

      $uploaded_file = array('name' => $HTTP_POST_FILES[$filename]['name'],
                             'type' => $HTTP_POST_FILES[$filename]['type'],
                             'size' => $HTTP_POST_FILES[$filename]['size'],
                             'tmp_name' => $HTTP_POST_FILES[$filename]['tmp_name']);
    } else {
      $uploaded_file = array('name' => isset($GLOBALS[$filename . '_name'])?$GLOBALS[$filename . '_name']:'',
                             'type' => isset($GLOBALS[$filename . '_type'])?$GLOBALS[$filename . '_type']:'',
                             'size' => isset($GLOBALS[$filename . '_size'])?$GLOBALS[$filename . '_size']:'',
                             'tmp_name' => isset($GLOBALS[$filename])?$GLOBALS[$filename]:'');
    }

    return $uploaded_file;
  }

// the $filename parameter is an array with the following elements:
// name, type, size, tmp_name
  function tep_copy_uploaded_file($filename, $target) {
    if (substr($target, -1) != '/') $target .= '/';

    $target .= $filename['name'];
    //if (!file_exists($target)) {
      //@mkdir($target);
      //@chmod($target, 0777);
    //}
    move_uploaded_file($filename['tmp_name'], $target);
    chmod($target, 0666);
  }

// return a local directory path (without trailing slash)
  function tep_get_local_path($path) {
    if (substr($path, -1) == '/') $path = substr($path, 0, -1);

    return $path;
  }

  function tep_array_shift(&$array) {
    if (function_exists('array_shift')) {
      return array_shift($array);
    } else {
      $i = 0;
      $shifted_array = array();
      reset($array);
      while (list($key, $value) = each($array)) {
        if ($i > 0) {
          $shifted_array[$key] = $value;
        } else {
          $return = $array[$key];
        }
        $i++;
      }
      $array = $shifted_array;

      return $return;
    }
  }

  function tep_array_reverse($array) {
    if (function_exists('array_reverse')) {
      return array_reverse($array);
    } else {
      $reversed_array = array();
      for ($i=sizeof($array)-1; $i>=0; $i--) {
        $reversed_array[] = $array[$i];
      }
      return $reversed_array;
    }
  }

  function tep_generate_category_path($id, $from = 'category', $categories_array = '', $index = 0) {
    global $languages_id;

    if (!is_array($categories_array)) $categories_array = array();

    if ($from == 'product') {
      $categories_query = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . $id . "'");
      while ($categories = tep_db_fetch_array($categories_query)) {
        if ($categories['categories_id'] == '0') {
          $categories_array[$index][] = array('id' => '0', 'text' => TEXT_TOP);
        } else {
          $category_query = tep_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . $categories['categories_id'] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . $languages_id . "' and cd.site_id='0'");
          $category = tep_db_fetch_array($category_query);
          $categories_array[$index][] = array('id' => $categories['categories_id'], 'text' => $category['categories_name']);
          if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0') ) $categories_array = tep_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
          $categories_array[$index] = tep_array_reverse($categories_array[$index]);
        }
        $index++;
      }
    } elseif ($from == 'category') {
      $category_query = tep_db_query("select cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = '" . $id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . $languages_id . "' and cd.site_id='0'");
      $category = tep_db_fetch_array($category_query);
      $categories_array[$index][] = array('id' => $id, 'text' => $category['categories_name']);
      if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0') ) $categories_array = tep_generate_category_path($category['parent_id'], 'category', $categories_array, $index);
    }

    return $categories_array;
  }

  function tep_output_generated_category_path($id, $from = 'category') {
    $calculated_category_path_string = '';
    $calculated_category_path = tep_generate_category_path($id, $from);
    for ($i = 0, $n = sizeof($calculated_category_path); $i < $n; $i++) {
      for ($j = 0, $k = sizeof($calculated_category_path[$i]); $j < $k; $j++) {
        $calculated_category_path_string .= $calculated_category_path[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
      }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -16) . '<br>';
    }
    $calculated_category_path_string = substr($calculated_category_path_string, 0, -4);

    if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = TEXT_TOP;

    return $calculated_category_path_string;
  }

  function tep_remove_category($category_id) {
    $category_image_query = tep_db_query("select categories_image from " . TABLE_CATEGORIES . " where categories_id = '" . tep_db_input($category_id) . "'");
    $category_image = tep_db_fetch_array($category_image_query);

    $duplicate_image_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where categories_image = '" . tep_db_input($category_image['categories_image']) . "'");
    $duplicate_image = tep_db_fetch_array($duplicate_image_query);

    //if ($duplicate_image['total'] < 2) {
      //if (file_exists(DIR_FS_CATALOG_IMAGES . $category_image['categories_image'])) {
        //@unlink(DIR_FS_CATALOG_IMAGES . $category_image['categories_image']);
      //}
    //}

    tep_db_query("delete from " . TABLE_CATEGORIES . " where categories_id = '" . tep_db_input($category_id) . "'");
    tep_db_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . tep_db_input($category_id) . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . tep_db_input($category_id) . "'");

    if (USE_CACHE == 'true') {
      tep_reset_cache_block('categories');
      tep_reset_cache_block('also_purchased');
    }
  }

  function tep_remove_product($product_id) {
    $product_image_query = tep_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . tep_db_input($product_id) . "'");
    $product_image = tep_db_fetch_array($product_image_query);

    $duplicate_image_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " where products_image = '" . tep_db_input($product_image['products_image']) . "'");
    $duplicate_image = tep_db_fetch_array($duplicate_image_query);

    //if ($duplicate_image['total'] < 2) {
      //if (file_exists(DIR_FS_CATALOG_IMAGES . $product_image['products_image'])) {
        //@unlink(DIR_FS_CATALOG_IMAGES . $product_image['products_image']);
      //}
    //}

    //tep_db_query("delete from " . TABLE_SPECIALS . " where products_id = '" . tep_db_input($product_id) . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS . " where products_id = '" . tep_db_input($product_id) . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . tep_db_input($product_id) . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . tep_db_input($product_id) . "'");
    tep_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . tep_db_input($product_id) . "'");
    tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where products_id = '" . tep_db_input($product_id) . "'");
    tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where products_id = '" . tep_db_input($product_id) . "'");

    $product_reviews_query = tep_db_query("select reviews_id from " . TABLE_REVIEWS . " where products_id = '" . tep_db_input($product_id) . "'");
    while ($product_reviews = tep_db_fetch_array($product_reviews_query)) {
      tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $product_reviews['reviews_id'] . "'");
    }
    tep_db_query("delete from " . TABLE_REVIEWS . " where products_id = '" . tep_db_input($product_id) . "'");

    if (USE_CACHE == 'true') {
      tep_reset_cache_block('categories');
      tep_reset_cache_block('also_purchased');
    }
  }

  function tep_remove_order($order_id, $restock = false) {
    if ($restock == 'on') {
      $order_query = tep_db_query("select products_id, products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($order_id) . "'");
      while ($order = tep_db_fetch_array($order_query)) {
        tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = products_quantity + " . $order['products_quantity'] . ", products_ordered = products_ordered - " . $order['products_quantity'] . " where products_id = '" . $order['products_id'] . "'");
      }
    }

    tep_db_query("delete from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($order_id) . "'");
    tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($order_id) . "'");
    tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . tep_db_input($order_id) . "'");
    tep_db_query("delete from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($order_id) . "'");
    tep_db_query("delete from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . tep_db_input($order_id) . "'");
    tep_db_query("delete from " . TABLE_ORDERS_TO_COMPUTERS . " where orders_id = '" . tep_db_input($order_id) . "'");
  }

  function tep_reset_cache_block($cache_block, $site_id='') {
    global $cache_blocks;

  foreach (tep_get_sites() as $k=>$s){
  $dir_fs_cache = get_configuration_by_site_id('DIR_FS_CACHE', $s['id']);
    for ($i = 0, $n = sizeof($cache_blocks); $i < $n; $i++) {
      if ($cache_blocks[$i]['code'] == $cache_block) {
        if ($cache_blocks[$i]['multiple']) {
          if ($dir = @opendir($dir_fs_cache)) {
            while ($cache_file = readdir($dir)) {
              $cached_file = $cache_blocks[$i]['file'];
              $languages = tep_get_languages();
              for ($j = 0, $k = sizeof($languages); $j < $k; $j++) {
                $cached_file_unlink = ereg_replace('-language', '-' . $languages[$j]['directory'], $cached_file);
                if (ereg('^' . $cached_file_unlink, $cache_file)) {
                  @unlink($dir_fs_cache . $cache_file);
                }
              }
            }
            closedir($dir);
          }
        } else {
          $cached_file = $cache_blocks[$i]['file'];
          $languages = tep_get_languages();
          for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $cached_file = ereg_replace('-language', '-' . $languages[$i]['directory'], $cached_file);
            @unlink($dir_fs_cache . $cached_file);
          }
        }
        break;
      }
    }
    }
  }

  function tep_get_file_permissions($mode) {
// determine type
    if ( ($mode & 0xC000) == 0xC000) { // unix domain socket
      $type = 's';
    } elseif ( ($mode & 0x4000) == 0x4000) { // directory
      $type = 'd';
    } elseif ( ($mode & 0xA000) == 0xA000) { // symbolic link
      $type = 'l';
    } elseif ( ($mode & 0x8000) == 0x8000) { // regular file
      $type = '-';
    } elseif ( ($mode & 0x6000) == 0x6000) { //bBlock special file
      $type = 'b';
    } elseif ( ($mode & 0x2000) == 0x2000) { // character special file
      $type = 'c';
    } elseif ( ($mode & 0x1000) == 0x1000) { // named pipe
      $type = 'p';
    } else { // unknown
      $type = '?';
    }

// determine permissions
    $owner['read']    = ($mode & 00400) ? 'r' : '-';
    $owner['write']   = ($mode & 00200) ? 'w' : '-';
    $owner['execute'] = ($mode & 00100) ? 'x' : '-';
    $group['read']    = ($mode & 00040) ? 'r' : '-';
    $group['write']   = ($mode & 00020) ? 'w' : '-';
    $group['execute'] = ($mode & 00010) ? 'x' : '-';
    $world['read']    = ($mode & 00004) ? 'r' : '-';
    $world['write']   = ($mode & 00002) ? 'w' : '-';
    $world['execute'] = ($mode & 00001) ? 'x' : '-';

// adjust for SUID, SGID and sticky bit
    if ($mode & 0x800 ) $owner['execute'] = ($owner['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x400 ) $group['execute'] = ($group['execute'] == 'x') ? 's' : 'S';
    if ($mode & 0x200 ) $world['execute'] = ($world['execute'] == 'x') ? 't' : 'T';

    return $type .
           $owner['read'] . $owner['write'] . $owner['execute'] .
           $group['read'] . $group['write'] . $group['execute'] .
           $world['read'] . $world['write'] . $world['execute'];
  }

  function tep_array_slice($array, $offset, $length = '0') {
    if (function_exists('array_slice')) {
      return array_slice($array, $offset, $length);
    } else {
      $length = abs($length);
      if ($length == 0) {
        $high = sizeof($array);
      } else {
        $high = $offset+$length;
      }

      for ($i=$offset; $i<$high; $i++) {
        $new_array[$i-$offset] = $array[$i];
      }

      return $new_array;
    }
  }

  function tep_remove($source) {
    global $messageStack, $tep_remove_error;

    if (isset($tep_remove_error)) $tep_remove_error = false;

    if (is_dir($source)) {
      $dir = dir($source);
      while ($file = $dir->read()) {
        if ( ($file != '.') && ($file != '..') ) {
          if (is_writeable($source . '/' . $file)) {
            tep_remove($source . '/' . $file);
          } else {
            $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source . '/' . $file), 'error');
            $tep_remove_error = true;
          }
        }
      }
      $dir->close();

      if (is_writeable($source)) {
        rmdir($source);
      } else {
        $messageStack->add(sprintf(ERROR_DIRECTORY_NOT_REMOVEABLE, $source), 'error');
        $tep_remove_error = true;
      }
    } else {
      if (is_writeable($source)) {
        unlink($source);
      } else {
        $messageStack->add(sprintf(ERROR_FILE_NOT_REMOVEABLE, $source), 'error');
        $tep_remove_error = true;
      }
    }
  }

////
// Wrapper for constant() function
// Needed because its only available in PHP 4.0.4 and higher.
  function tep_constant($constant) {
    if (function_exists('constant')) {
      $temp = constant($constant);
    } else {
      eval("\$temp=$constant;");
    }
    return $temp;
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

  function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address, $site_id = 0) {
    if (SEND_EMAILS != 'true') return false;
    // Instantiate a new mail object
    $message = new email(array('X-Mailer: iimy Mailer'), $site_id);

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

  function tep_get_tax_class_title($tax_class_id) {
    if ($tax_class_id == '0') {
      return TEXT_NONE;
    } else {
      $classes_query = tep_db_query("select tax_class_title from " . TABLE_TAX_CLASS . " where tax_class_id = '" . $tax_class_id . "'");
      $classes = tep_db_fetch_array($classes_query);

      return $classes['tax_class_title'];
    }
  }

  function tep_banner_image_extension() {
    if (function_exists('imagetypes')) {
      if (imagetypes() & IMG_PNG) {
        return 'png';
      } elseif (imagetypes() & IMG_JPG) {
        return 'jpg';
      } elseif (imagetypes() & IMG_GIF) {
        return 'gif';
      }
    } elseif (function_exists('imagecreatefrompng') && function_exists('imagepng')) {
      return 'png';
    } elseif (function_exists('imagecreatefromjpeg') && function_exists('imagejpeg')) {
      return 'jpg';
    } elseif (function_exists('imagecreatefromgif') && function_exists('imagegif')) {
      return 'gif';
    }

    return false;
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
// Add tax to a products price
  function tep_add_tax($price, $tax) {
    global $currencies;

    if (DISPLAY_PRICE_WITH_TAX == 'true') {
      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + tep_calculate_tax($price, $tax);
    } else {
      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
    }
  }

// Calculates Tax rounding the result
  function tep_calculate_tax($price, $tax) {
    global $currencies;

    return tep_round($price * $tax / 100, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
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

    $tax_query = tep_db_query("select SUM(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za ON tr.tax_zone_id = za.geo_zone_id left join " . TABLE_GEO_ZONES . " tz ON tz.geo_zone_id = tr.tax_zone_id WHERE (za.zone_country_id IS NULL OR za.zone_country_id = '0' OR za.zone_country_id = '" . $country_id . "') AND (za.zone_id IS NULL OR za.zone_id = '0' OR za.zone_id = '" . $zone_id . "') AND tr.tax_class_id = '" . $class_id . "' GROUP BY tr.tax_priority");
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

  function tep_call_function($function, $parameter, $object = '') {
    if ($object == '') {
      return call_user_func($function, $parameter);
    } elseif (PHP_VERSION < 4) {
      return call_user_method($function, $object, $parameter);
    } else {
      return call_user_func(array($object, $function), $parameter);
    }
  }

  function tep_get_zone_class_title($zone_class_id) {
    if ($zone_class_id == '0') {
      return TEXT_NONE;
    } else {
      $classes_query = tep_db_query("select geo_zone_name from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . $zone_class_id . "'");
      $classes = tep_db_fetch_array($classes_query);

      return $classes['geo_zone_name'];
    }
  }

  function tep_cfg_pull_down_zone_classes($zone_class_id, $key = '') {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $zone_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $zone_class_query = tep_db_query("select geo_zone_id, geo_zone_name from " . TABLE_GEO_ZONES . " order by geo_zone_name");
    while ($zone_class = tep_db_fetch_array($zone_class_query)) {
      $zone_class_array[] = array('id' => $zone_class['geo_zone_id'],
                                  'text' => $zone_class['geo_zone_name']);
    }

    return tep_draw_pull_down_menu($name, $zone_class_array, $zone_class_id);
  }

  function tep_cfg_pull_down_order_statuses($order_status_id, $key = '') {
    global $languages_id;

    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

    $statuses_array = array(array('id' => '0', 'text' => TEXT_DEFAULT));
    $statuses_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "' order by orders_status_name");
    while ($statuses = tep_db_fetch_array($statuses_query)) {
      $statuses_array[] = array('id' => $statuses['orders_status_id'],
                                'text' => $statuses['orders_status_name']);
    }

    return tep_draw_pull_down_menu($name, $statuses_array, $order_status_id);
  }

  function tep_get_order_status_name($order_status_id, $language_id = '') {
    global $languages_id;

    if ($order_status_id < 1) return TEXT_DEFAULT;

    if (!is_numeric($language_id)) $language_id = $languages_id;

    $status_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . $order_status_id . "' and language_id = '" . $language_id . "'");
    $status = tep_db_fetch_array($status_query);

    return $status['orders_status_name'];
  }

////
// Return a random value
  function tep_rand($min = null, $max = null) {
    static $seeded;

    if (!$seeded) {
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
// Returns the address_format_id for the given country
// TABLES: countries;
// for Japanese Localize (imported from catalog)
  function tep_get_address_format_id($country_id) {
    $address_format_query = tep_db_query("select address_format_id as format_id from " . TABLE_COUNTRIES . " where countries_id = '" . $country_id . "'");
    if (tep_db_num_rows($address_format_query)) {
      $address_format = tep_db_fetch_array($address_format_query);
      return $address_format['format_id'];
    } else {
      return '6';
    }
  }

////
// Return fullname
// for Japanese Localize
  function tep_get_fullname($firstname, $lastname) {
    global $language;
    $separator = ' ';
    return ($language == 'japanese')
        ? ($lastname.$separator.$firstname)
        : ($firstname.$separator.$lastname);
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
// Get manufacturers_name from manufacturers_id
  function tep_get_manufacturers_name($id) {
    $mquery = tep_db_query("select manufacturers_name from manufacturers where manufacturers_id = '".$id."'");
  $mresult = tep_db_fetch_array($mquery);
  
  return $mresult['manufacturers_name'];
  }

////
// Get comment from orders_status 
  function tep_get_orders_status_comment($orders_status_id, $language_id = '') {
    global $languages_id;

    if (!$language_id) $language_id = $languages_id;
    $orders_status_query = tep_db_query("select comment from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . $orders_status_id . "' and language_id = '" . $language_id . "'");
    $orders_status = tep_db_fetch_array($orders_status_query);

    return $orders_status['comment'];
  }

//update orders_products_attributes
  function tep_remove_attributes($order_id, $restock = false) {
    if ($restock == 'on') {
      $orderproduct_query = tep_db_query("select orders_products_id, products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($order_id) . "'");
    while ($o_product = tep_db_fetch_array($orderproduct_query)) {
    $opID = $o_product['orders_products_id'];
    $zaiko = $o_product['products_quantity'];
    
    $order_attributes_query = tep_db_query("select attributes_id from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_products_id = '" . $opID . "'");
    $order_attributes_result = tep_db_fetch_array($order_attributes_query);
    $att_id = $order_attributes_result['attributes_id'];
    
    tep_db_query("update " . TABLE_PRODUCTS_ATTRIBUTES . " set products_at_quantity = products_at_quantity + " . $zaiko . " where products_attributes_id = '" . $att_id . "'");
      }
    }
  }

// Function to reset SEO URLs database cache entries 
// Ultimate SEO URLs v2.1
function tep_reset_cache_data_seo_urls($action){  
  switch ($action){
    case 'reset':
      tep_db_query("DELETE FROM cache WHERE cache_name LIKE '%seo_urls%'");
      tep_db_query("UPDATE configuration SET configuration_value='false' WHERE configuration_key='SEO_URLS_CACHE_RESET'");
      break;
    default:
      break;
  }
  # The return value is used to set the value upon viewing
  # It's NOT returining a false to indicate failure!!
  return 'false';
}

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

      while (substr($pieces[$k], -1) == ')' && strpos($search_str, ' ('))  {
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
      $temp[] = $objects[$i];
      if ( ($objects[$i] != 'and') &&
           ($objects[$i] != 'or') &&
           ($objects[$i] != '(') &&
           ($objects[$i+1] != 'and') &&
           ($objects[$i+1] != 'or') &&
           ($objects[$i+1] != ')') ) {
        $temp[] = ADVANCED_SEARCH_DEFAULT_OPERATOR;
      }
    }
    $temp[] = $objects[$i];
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

// 取引日時
  function tep_torihiki($raw_datetime) {
    if ( ($raw_datetime == '0000-00-00 00:00:00') || ($raw_datetime == '') ) return false;

    $year = (int)substr($raw_datetime, 0, 4);
    $month = (int)substr($raw_datetime, 5, 2);
    $day = (int)substr($raw_datetime, 8, 2);
    $hour = (int)substr($raw_datetime, 11, 2);
    $minute = (int)substr($raw_datetime, 14, 2);
    $second = (int)substr($raw_datetime, 17, 2);

    return date('Y年m月d日 H時i分', mktime($hour, $minute, $second, $month, $day, $year));
  }
  
  // return all types with options
  function tep_get_torihiki_houhou()
  {
    $types = $return = array();
    //DS_TORIHIKI_HOUHOU
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
      $return = array('0' => array());
      $arr = array_keys(tep_get_torihiki_houhou());
      foreach($arr as $key => $value){
        $return [] = array(
          'id' => $value,
          'text' => $value,
        );
      }
      return $return;
  }
  
  function tep_get_all_torihiki()
  {
      $torihikis = array();
      $return = array(0 => array());
      $all = tep_get_torihiki_houhou();
      foreach($all as $key=>$value){
        foreach($value as $item){
          if(!in_array($item, $torihikis)){
              $torihikis[] = $item;
          }
        }
      }
      foreach($torihikis as $tkey => $torihiki){
          $return[] = array(
            'id' => $torihiki,
            'text' => $torihiki,
          );
      }
      return $return;
  }
  
  function tep_get_bflag_by_product_id($product_id) {
    // 0 => sell   1 => buy
    $product_query = tep_db_query("select products_bflag from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_bflag'];
  }
  
  function tep_get_full_count($cnt, $rate, $prate = ''){
  if ($prate) {
    if (trim($rate) == '天空の羽毛5個・インクリスクロール5個のセット'){
      return '(天空の羽毛'.number_format(strval(5*$cnt)).'個・インクリスクロール'.number_format(strval(5*$cnt)).'個のセット)';
    }
    if (trim($rate) == 'ネットカフェ1DAYチケット5枚セット'){
      return '(ネットカフェ1DAYチケット'.number_format(strval(5*$cnt)).'枚セット)';
    }
    $rate = str_replace(array(','), array(''), $rate);
    /*
    if (preg_match('/^(.*)億(.*)万(.*)$/', $rate, $out)) {
      $rate = (($prate * 100000000) + ($out[2] * 10000)) . $out[3];
    }
    */
    $rate = str_replace(array('万','億'), array('0000','00000000'), $rate);
    if (preg_match('/^(\d+)(.*)（\d+.*）$/', $rate, $out)) {
      return '(' . number_format($prate * $cnt) . $out[2] . ')';
    }
    if (preg_match('/^(\d+)(.*)\(\d+.*\)$/', $rate, $out)) {
      return '(' . number_format($prate * $cnt) . $out[2] . ')';
    }
    if (preg_match('/^(\d+)(.*)$/', $rate, $out)) {
      return '(' . number_format($prate * $cnt) . $out[2] . ')';
    }
    if (preg_match('/^([^\d]*)(\d+)([^\d]*)$/', $rate, $out)) {
      return '(' . $out[1] . number_format($prate * $cnt) . $out[3] . ')';
      //return '(' . $prate . number_format($out[2] * $cnt) . $out[3] . ')';
    }
    return '';
  } else {
    /*
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
    */
  }
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
    } else {
      $rate = str_replace(array('万','億'), array('0000','00000000'), $rate);
    }
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

// Start Documents Manager
////
// Build an array of downloadable document types
//   Table: document_types
  function document_types () {
    $type_array = array ();
    $documents_query_raw = "
      select 
        document_types_id,
        type_description
      from 
        " . TABLE_DOCUMENT_TYPES . "
      where
        type_visible = 'True'
      order by 
        sort_order
    ";
    
    $documents_query = tep_db_query ($documents_query_raw);
    while ($documents = tep_db_fetch_array ($documents_query) ) {
      $type_array[] = array ('id' => $documents['document_types_id'],
                             'text' => $documents['type_description']
                            );
    } // while ($documents
    
    return $type_array;
  } // function document_types

////
// Get a list of documents in a directory
//   Table: none
  function get_directory_list ($directory, $file=true) {
    $d = dir ($directory);
    $list = array();
    while ($entry = $d->read() ) {
      if ($file == true) { // We want a list of files, not directories
        $parts_array = explode ('.', $entry);
        $extension = $parts_array[1];
        // Don't add files or directories that we don't want
        if ($entry != '.' && $entry != '..' && $entry != '.htaccess' && $extension != 'php') {
          if (!is_dir ($directory . "/" . $entry) ) {
            $list[] = $entry;
          }
        }
      } else { // We want the directories and not the files
        if (is_dir ($directory . "/" . $entry) && $entry != '.' && $entry != '..') {
          $list[] = array ('id' => $entry,
                           'text' => $entry
                          );
        }
      }
    }
    $d->close();
    return $list;
  }

function tep_get_document_url($document_id)
{
  $document_query = tep_db_query("select * from " . TABLE_DOCUMENTS . " as d, " .
      TABLE_DOCUMENT_TYPES . " as dt where d.document_types_id =
      dt.document_types_id and d.documents_id = '" . $document_id . "'");

  $document = tep_db_fetch_array($document_query);
  if ($document)
  {
    return DIR_WS_DOCUMENTS . $document['type_name'] . '/' . $document['documents_name'];
  }
  else
  {
    return null;
  }
}
function tep_get_document_image($document_id)
{
  $document_query = tep_db_query("select * from " . TABLE_DOCUMENTS . " as d, " .
      TABLE_DOCUMENT_TYPES . " as dt where d.document_types_id =
      dt.document_types_id and d.documents_id = '" . $document_id . "'");

  $document = tep_db_fetch_array($document_query);
  if ($document)
  {
    return '<img src=' . DIR_WS_DOCUMENTS . $document['type_name'] . '/' .  $document['documents_name'] . ' >';
  }
  else
  {
    return null;
  }
}
// End Documents Manager

  function image_document_types () {
    $type_array = array ();
    $documents_query_raw = "
      select 
        document_types_id,
        type_description
      from 
        " . TABLE_IMAGE_DOCUMENT_TYPES . "
      where
        type_visible = 'True'
      order by 
        sort_order
    ";
    
    $documents_query = tep_db_query ($documents_query_raw);
    while ($documents = tep_db_fetch_array ($documents_query) ) {
      $type_array[] = array ('id' => $documents['document_types_id'],
                             'text' => $documents['type_description']
                            );
    } // while ($documents
    
    return $type_array;
  } // function document_types

////
// Get a list of documents in a directory
//   Table: none
  function get_image_directory_list ($directory, $file=true) {
    $d = dir ($directory);
    $list = array();
    while ($entry = $d->read() ) {
      if ($file == true) { // We want a list of files, not directories
        $parts_array = explode ('.', $entry);
        $extension = $parts_array[1];
        // Don't add files or directories that we don't want
        if ($entry != '.' && $entry != '..' && $entry != '.htaccess' && $extension != 'php') {
          if (!is_dir ($directory . "/" . $entry) ) {
            $list[] = $entry;
          }
        }
      } else { // We want the directories and not the files
        if (is_dir ($directory . "/" . $entry) && $entry != '.' && $entry != '..') {
          $list[] = array ('id' => $entry,
                           'text' => $entry
                          );
        }
      }
    }
    $d->close();
    return $list;
  }
function tep_get_image_document_path($document_id)
{
  $document_query = tep_db_query("select * from " . TABLE_IMAGE_DOCUMENTS . " as id, " .
      TABLE_IMAGE_DOCUMENT_TYPES . " as idt where id.document_types_id =
      idt.document_types_id and id.documents_id = '" . $document_id . "'");

  $document = tep_db_fetch_array($document_query);
  if ($document)
  {
    return tep_get_web_upload_dir() . DIR_WS_IMAGE_DOCUMENTS . $document['type_name'] . '/' . $document['documents_name'];
  }
  else
  {
    return null;
  }
}

function tep_get_new_file_path($documents_id, $type_id)
{
   $type_query = tep_db_query("select * from " . TABLE_IMAGE_DOCUMENT_TYPES . "
            where document_types_id = " . $type_id);
   $type = tep_db_fetch_array($type_query);

   $document_query = tep_db_query("select * from " . TABLE_IMAGE_DOCUMENTS . " where
       documents_id=" . $documents_id);
   $document = tep_db_fetch_array($document_query);

   return tep_get_web_upload_dir() . DIR_WS_IMAGE_DOCUMENTS . $type['type_name'] . '/' . $document['documents_name'];
}

function tep_get_image_document_url($document_id)
{
  $document_query = tep_db_query("select * from " . TABLE_IMAGE_DOCUMENTS . " as d, " .
      TABLE_IMAGE_DOCUMENT_TYPES . " as dt where d.document_types_id =
      dt.document_types_id and d.documents_id = '" . $document_id . "'");

  $document = tep_db_fetch_array($document_query);
  if ($document)
  {
    return  tep_get_web_upload_dir() . DIR_WS_IMAGE_DOCUMENTS . $document['type_name'] . '/' . $document['documents_name'];
  }
  else
  {
    return null;
  }
}
function tep_get_image_document_image($document_id)
{
  $document_query = tep_db_query("select * from " . TABLE_IMAGE_DOCUMENTS . " as d, " .
      TABLE_IMAGE_DOCUMENT_TYPES . " as dt where d.document_types_id =
      dt.document_types_id and d.documents_id = '" . $document_id . "'");

  $document = tep_db_fetch_array($document_query);
  if ($document)
  {
    return '&lt;img src=&quot;' . HTTP_SERVER . tep_get_web_upload_dir() . DIR_WS_IMAGE_DOCUMENTS .
      $document['type_name'] . '/' .  $document['documents_name'] . '&quot;&gt;';
  }
  else
  {
    return null;
  }
}
  // orders.php
  // replace actor name in mail
  function orders_a($orders_id, $allorders = null, $site_id = 0)
  {
      static $products;
      $str = "";
      if ($allorders && $products === null) {
        foreach($allorders as $o) {
          $allorders_ids[] = $o['orders_id'];
        }
        $sql = "select pd.products_name,p.products_attention_5,p.products_id from ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_PRODUCTS_DESCRIPTION." pd,".TABLE_PRODUCTS." p WHERE op.products_id=pd.products_id and p.products_id=pd.products_id and `orders_id` IN ('".join("','", $allorders_ids)."') and pd.site_id = '".$site_id."'";
        $orders_products_query = tep_db_query($sql);
        while ($product = tep_db_fetch_array($orders_products_query)) {
          $products[$product['orders_id']][] = $product;
        }
      }
      if (isset($products[$orders_id]) && $products[$orders_id]) {
        foreach($products[$orders_id] as $p){
            $str .= $p['products_name'] . " 当社のキャラクター名：\n";
            $str .= $p['products_attention_5'] . "\n";
        }
      } else {
          $sql = "select * from `".TABLE_ORDERS_PRODUCTS."` WHERE `orders_id`='".$orders_id."'";
          $orders_products_query = tep_db_query($sql);
          while ($orders_products = tep_db_fetch_array($orders_products_query)){
              $sql = "select pd.products_name,p.products_attention_5,p.products_id from `".TABLE_PRODUCTS_DESCRIPTION."` pd,".TABLE_PRODUCTS." p WHERE p.products_id=pd.products_id and p.`products_id`='".$orders_products['products_id']."' and pd.site_id = '".$site_id."'";
              $products_description = tep_db_fetch_array(tep_db_query($sql));
              if ($products_description['products_attention_5']) {
                $str .= $orders_products['products_name']." 当社のキャラクター名：\n";
                $str .= $products_description['products_attention_5'] . "\n";
              }
          }
      }
      return $str;
  }

  function tep_get_sites() {
    $sites = array();
    $sites_query = tep_db_query("
        select * 
        from " . TABLE_SITES . "
        order by order_num ASC
    ");
    while ($site = tep_db_fetch_array($sites_query)) {
      $sites[] = $site;
    }
    return $sites;
  }

  function tep_get_sites_id() {
    $sites_id = array();
    $sites_query = tep_db_query("
        select * 
        from " . TABLE_SITES . "
        order by order_num ASC
    ");
    while ($site = tep_db_fetch_array($sites_query)) {
      $sites_id[] = $site['id'];
    }
    return $sites_id;
  }

  function tep_site_filter($filename, $ca_single = false){
    global $_GET, $_POST;
    ?>
      <div id="tep_site_filter">
      <?php
          if (!isset($_GET['site_id']) || !$_GET['site_id']) {?>
            <span class="site_filter_selected"><a href="<?php echo tep_href_link($filename);
            ?>">all</a></span>
          <?php } else { ?>
            <span><a href="<?php 
              if ($ca_single) {
                echo tep_href_link($filename, tep_get_all_get_params(array('site_id')));
              } else {
                echo tep_href_link($filename, tep_get_all_get_params(array('site_id', 'page', 'oID', 'rID', 'cID')));
              }
            ?>">all</a></span> 
            <?php } ?>
          <?php foreach (tep_get_sites() as $site) {?>
            <?php if (isset($_GET['site_id']) && $_GET['site_id'] == $site['id']) {?>
<span class="site_filter_selected"><?php echo $site['romaji'];?></span>
            <?php } else {?>
<span><a href="<?php 
  if ($ca_single) {
    echo tep_href_link($filename, tep_get_all_get_params(array('site_id')) . 'site_id=' . $site['id']);
  } else {
    echo tep_href_link($filename, tep_get_all_get_params(array('site_id', 'page', 'oID', 'rID', 'cID', 'pID')) . 'site_id=' . $site['id']);
  }
?>"><?php echo $site['romaji'];?></a></span>
            <?php }
           }
    ?>
      </div>
      <?php
  }

function tep_siteurl_pull_down_menu($default = '',$require = false){
    $sites_array = array(array('id' => '', 'text' => 'サイトへ移動'));
    $sites = tep_get_sites();
    foreach($sites as $site){
      $sites_array[] = array('id' => $site['url'], 'text' => $site['name']);
    }
    return tep_draw_pull_down_menu('site_url_id', $sites_array, $default, $params = 'onChange="window.open(this.value);this.selectedIndex=0;"', $require);

}
  // 生成选择SITE_ID的下拉框
  function tep_site_pull_down_menu($default = '',$require = true,$all = false){
    $sites_array = array();
    $sites = tep_get_sites();
    if ($all) {
      $sites_array[] = array('id' => '0', 'text' => '全部サイト');
    }
    foreach($sites as $site){
      $sites_array[] = array('id' => $site['id'], 'text' => $site['name']);
    }
    return tep_draw_pull_down_menu('site_id', $sites_array, $default, $params = '', $require);
  }

  function tep_site_pull_down_menu_with_all($default = '',$require = true,$text = 'all'){
    $sites_array = array();
    $sites = tep_get_sites();
    $sites_array[] = array('id' => '', 'text' => $text );
    foreach($sites as $site){
      $sites_array[] = array('id' => $site['id'], 'text' => $site['name']);
    }
    return tep_draw_pull_down_menu('site_id', $sites_array, $default, $params = '', $require);
  }

  function tep_site_pull_down_menu_with_none($default = '',$require = true){
  }

  function tep_get_site_romaji_by_id($id){
    static $arr;
    if ($id == 0){
      return 'all';
    }
    if (isset($arr[$id])) {
      return $arr[$id];
    }
    $site_query = tep_db_query("
        select * 
        from " . TABLE_SITES . "
        where id = '".intval($id)."'
    ");
    $site = tep_db_fetch_array($site_query);
    if (isset($site['romaji'])) {
      $arr[$id] = $site['romaji'];
      return $site['romaji'];
    } else {
      return '';
    }
    #return isset($site['romaji'])?$site['romaji']:'';
  }
  
  function tep_get_site_name_by_id($id){
    if ($id == '0') {
      return '全部サイト';
    }
    $site_query = tep_db_query("
        select * 
        from " . TABLE_SITES . "
        where id = '".intval($id)."'
    ");
    $site = tep_db_fetch_array($site_query);
    if (isset($site['name']) && $site['name']) {
      return $site['name'];
    } else if (isset($site['romaji']) && $site['romaji']) {
      return $site['romaji'];
    } else {
      return '';
    }
  }
  
  function tep_get_site_romaji_by_order_id($id){
    $order_query = tep_db_query("
        select s.romaji
        from " . TABLE_ORDERS . " o, ".TABLE_SITES." s
        where o.orders_id = '".$id."'
          and s.id = o.site_id
    ");
    $order = tep_db_fetch_array($order_query);
    return isset($order['romaji'])?$order['romaji']:'';
  }
  
  function tep_get_site_name_by_order_id($id){
    $order_query = tep_db_query("
        select s.name
        from " . TABLE_ORDERS . " o, ".TABLE_SITES." s
        where o.orders_id = '".$id."'
          and s.id = o.site_id
    ");
    $order = tep_db_fetch_array($order_query);
    return isset($order['name'])?$order['name']:'';
  }

////
// categories.php
// Return a product's special price (returns nothing if there is no offer)
// TABLES: products
/*
  function tep_get_products_special_price($product_id) {
    $product_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$product_id . "' and status");
    $product = tep_db_fetch_array($product_query);

    return $product['specials_new_products_price'];
  }*/
  
    //オプション名取得
    function tep_get_add_options_name($id, $languages='4') {
        $query = tep_db_query("select products_options_name from products_options where products_options_id = '".$id."' and language_id = '".$languages."'");
      if(tep_db_num_rows($query)) {
        $result = tep_db_fetch_array($query);
        return $result['products_options_name'];
      } else {
        return '';
      }
    }
    //オプション値登録
    function tep_get_add_options_value($id, $languages='4') {
      $query = tep_db_query("select products_options_values_name from products_options_values where products_options_values_id = '".$id."' and language_id = '".$languages."'");
      if(tep_db_num_rows($query)) {
        $result = tep_db_fetch_array($query);
        return $result['products_options_values_name'];
      } else {
        return '';
      }
    }

    function tep_categories_description_exist($cid, $lid, $sid){
      $query = tep_db_query("select * from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cid."' and site_id = '".$sid."' and language_id='".$lid."'");
      if(tep_db_num_rows($query)) {
        return true;
      } else {
        return false;
      }
    }

    function tep_products_description_exist($pid, $sid, $lid){
      $query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$pid."' and site_id = '".$sid."' and language_id='".$lid."'");
      if(tep_db_num_rows($query)) {
        return true;
      } else {
        return false;
      }
    }

  function tep_module_installed($class, $site_id = 0){
    $module = new $class($site_id);
    return $module->check();
  }

  function tep_get_upload_dir($site_id = '0'){
    if (!trim($site_id)) $site_id = '0';
    return DIR_FS_CATALOG . 'upload_images/' . $site_id . '/';
  }

  function tep_get_web_upload_dir($site_id = '0'){
    if (!trim($site_id)) $site_id = '0';
    //return DIR_WS_CATALOG . 'upload_images/' . $site_id . '/';
    return 'upload_images/' . $site_id . '/';
  }

  function tep_get_upload_root(){
    //echo DIR_FS_CATALOG;
    return DIR_FS_CATALOG . 'upload_images/';
  }

  function tep_get_banner($bid){
    $banner_query = tep_db_query("select * from " .TABLE_BANNERS. " where banners_id = '".$bid."'");
    return tep_db_fetch_array($banner_query);
  }

  function tep_get_latest_news_by_id($latest_news_id){
    return tep_db_fetch_array(tep_db_query("select * from " . TABLE_LATEST_NEWS . " where news_id = '".$latest_news_id."'"));
  }

  function tep_get_present_by_id($id){
    return tep_db_fetch_array(tep_db_query("select * from " . TABLE_PRESENT_GOODS . " where goods_id = '".$id."'"));
  }
  
  function tep_get_cflag_by_product_id($product_id) {
    // 0 => no   1=> yes
    // ccdd
    $product_query = tep_db_query("select products_cflag from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_cflag'];
  }

  function tep_get_default_configuration_id_by_id($cid) {
    $configuration_query = tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_id = '".$cid."'");
    $configuration = tep_db_fetch_array($configuration_query);
    $default_query = tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key='".$configuration['configuration_key']."' and site_id = '0'");
    $default = tep_db_fetch_array($default_query);
    if($default)
      return $default['configuration_id'];
    else 
      return $cid;
  }
  function tep_get_product_by_id($pid,$site_id, $lid, $default = true){
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
          AND pd.products_id = '" .  $pid . "'" . " 
          AND pd.language_id ='" . $lid . "' 
        ORDER BY pd.site_id DESC) c
        WHERE site_id = '0' OR site_id = '".$site_id."'
        GROUP BY products_id HAVING c.products_status != '0' and  c.products_status != '3'
        ";
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

  function tep_get_faq_game_id_string(){
    $g_ids = array();
    $query = tep_db_query("select g_id from " . TABLE_FAQ_CATEGORIES . " group by g_id");
    while ($c = tep_db_fetch_array($query)) {
      $g_ids[] = $c['g_id'];
    }
    return implode(',', $g_ids);
  }

  function tep_get_faq_question($q_id){
    return tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_QUESTIONS." where q_id = '".$q_id."'"));
  }

  function tep_get_faq_category($c_id){
    return tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_CATEGORIES." where c_id = '".$c_id."'"));
  }

function calc_handle_fee($payment_name, $products_total)
{
  if ($products_total == 0) {
    return 0; 
  }
  $handle_fee = 0; 
  if ($payment_name == '銀行振込(買い取り)') {
    $handle_fee = calc_fee_final(MODULE_PAYMENT_BUYING_COST, $products_total); 
  } else if ($payment_name == 'コンビニ決済') {
    $handle_fee = calc_fee_final(MODULE_PAYMENT_CONVENIENCE_STORE_COST, $products_total); 
  } else if ($payment_name == '銀行振込') {
    $handle_fee = calc_fee_final(MODULE_PAYMENT_MONEYORDER_COST, $products_total); 
  } else if ($payment_name == 'ゆうちょ銀行（郵便局）') {
    $handle_fee = calc_fee_final(MODULE_PAYMENT_POSTALMONEYORDER_COST, $products_total); 
  } else if ($payment_name == 'クレジットカード決済') {
    $handle_fee = calc_fee_final(MODULE_PAYMENT_TELECOM_COST, $products_total); 
  } else {
    return 0; 
  }
  return $handle_fee;
}
function calc_fee_final($fee_set, $total_cost)
{
  $return_fee = 0; 
  $table_fee = split("[:,]", $fee_set);
  for ($i = 0; $i < count($table_fee); $i+=2) {
    if ($total_cost <= $table_fee[$i]) {
      $additional_fee = $total_cost.$table_fee[$i+1];
      @eval("\$additional_fee = $additional_fee;");
      if (is_numeric($additional_fee)) {
        $return_fee = $additional_fee; 
      }
      break; 
    }
  }
  return $return_fee;
}

function tep_set_categories_status($categories_id, $status)
{
  /*
  // c 0 => g 1 => r 2 => b
  // p 0 => r 1 => g 2 => b
  if ($status == 1) {
    $products_status = 0;
  } else if ($status == 0) {
    $products_status = 1;
  } else {
    $products_status = 2;
  }
  
  $categories_query = tep_db_query("SELECT * FROM ".TABLE_CATEGORIES." WHERE parent_id = '".$categories_id."'");
  while ( $categories = tep_db_fetch_array($categories_query) ) {
    tep_set_categories_status($categories['categories_id'], $status);
  }
  */
  tep_db_query("UPDATE `".TABLE_CATEGORIES."` SET `categories_status` = '".intval($status)."' WHERE `categories_id` =".$categories_id." LIMIT 1 ;");
  //tep_db_query("UPDATE ".TABLE_PRODUCTS." SET products_status = '".$products_status."' WHERE products_id IN (select products_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where categories_id = '".$categories_id."')");
  return true;
}

function tep_get_categories_status($categories_id)
{
  $categories_query = tep_db_query("select * from " . TABLE_CATEGORIES . " where categories_id='" . $categories_id . "' LIMIT 1");
  $categories = tep_db_fetch_array($categories_query);
  if ($categories) {
    return $categories['categories_status'];
  } else {
    return null;
  }
}

function tep_get_ot_total_by_orders_id($orders_id) {
  $query = tep_db_query("select text from " . TABLE_ORDERS_TOTAL . " where class='ot_total' and orders_id='".$orders_id."'");
  $result = tep_db_fetch_array($query);
  return $result['text'];
}

// order.php
function tep_get_ot_total_num_by_text($text) {
  return str_replace(array("," , "<b>" , "</b>" , "円") , array("" , "" , "" , "") , $text);
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

function SBC2DBC($str) {
  $arr = array(
    '１','２','３','４','５','６','７','８','９','０','＋','－','％'
  );
  $arr2 = array(
    '1','2','3','4','5','6','7','8','9','0','+','-','%'
  );
  return str_replace($arr, $arr2, $str);
}

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

function tep_get_site_id_by_orders_id($orders_id) {
  $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$orders_id."'"));
  if ($order) {
    return $order['site_id'];
  } else {
    return false;
  }
}

function get_configuration_by_site_id($key, $site_id = '0') {
  $config = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key='".$key."' and site_id='".$site_id."'"));
  if ($config) {
    return $config['configuration_value'];
  } else {
    return false;
  }
}

function get_url_by_site_id($site_id) {
  $site = tep_db_fetch_array(tep_db_query("select * from ".TABLE_SITES." where id='".$site_id."'"));
  if ($site) {
    return $site['url'];
  } else {
    return false;
  }
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
  tep_db_query("update ".TABLE_ORDERS." o set q_8_1 = ( select q_8_1 from orders_questions oq where oq.orders_id=o.orders_id ) where orders_id='".$orders_id."'");
  tep_db_query("update ".TABLE_ORDERS_PRODUCTS." set torihiki_date = ( select torihiki_date from ".TABLE_ORDERS." where orders.orders_id=orders_products.orders_id ) where orders_id='".$orders_id."'");
}

// 如果订单的状态发生改动则同步问答
function orders_status_updated_for_question($orders_id, $orders_status_id, $notify = true) {
  switch($orders_status_id){
    case 13:
      // 14_1 13_1 13_2(y-m-d)
      $arr = array(
        'q_13_1' => '1',
        'q_13_2' => date('Y-m-d')
      );
      if ($notify) {
        $arr['q_14_1'] = '1';
      }
      break;
    case 5:
      // 15_1 15_2(y-m-d) 16_1
      $arr = array(
        'q_15_1' => '1',
        'q_15_2' => date('Y-m-d')
      );
      if ($notify) {
        $arr['q_16_1'] = '1';
      }
      break;
    case 9:
      // 4_1  
      $arr = array(
        'q_4_1' => '1'
      );
      break;
    case 2:
      // 5_1 5_2(y-m-d) 7_1
      $arr = array(
        'q_5_1' => '1',
        'q_5_2' => date('Y-m-d')
      );
      if ($notify) {
        $arr['q_7_1'] = '1';
      }
      break;
  }
  if ($arr ) {
    if (tep_db_num_rows(tep_db_query("select * from orders_questions where orders_id='".$orders_id."'"))) {
      tep_db_perform('orders_questions',$arr,'update',"orders_id='".$orders_id."'");
    } else {
      $arr['orders_id'] = $orders_id;
      tep_db_perform('orders_questions',$arr);
    }
  }
}


// 如果订单finished则取消orders_wait_flag
function orders_wait_flag($orders_id) {
  $orders_query = tep_db_query("select * from " . TABLE_ORDERS . " where orders_id = '".$orders_id."'");
  $orders       = tep_db_fetch_array($orders_query);
  if ($orders['orders_wait_flag']) {
    $orders_status_query = tep_db_query("select * from " . TABLE_ORDERS_STATUS . " where orders_status_id='".$orders['orders_status']."'");
    $orders_status       = tep_db_fetch_array($orders_status_query);
    if ($orders_status['finished']) {
      tep_db_query("update ".TABLE_ORDERS." set orders_wait_flag = '0' where orders_id='".$orders_id."'");
    }
  }
  //exit;
}

//为创建下拉列表
  function countSubcategories($cid)
  { 
     $res = tep_db_query("select count(c.categories_id) cnt from categories_description cd,categories c where cd.site_id =0 and  c.categories_id = cd.categories_id and c.parent_id =".$cid);
     $col = tep_db_fetch_array($res);
     return $col['cnt']>0;
     
  }
  
  function getMainGames()
  {
  $cid = 0;
    $res = tep_db_query("select c.categories_id cid,cd.categories_name cname from categories_description cd,categories c where c.categories_id = cd.categories_id and cd.site_id = 0 and c.parent_id =".$cid );
  while ($col = @tep_db_fetch_array($res))
  {
    $result[] = $col;
  }
  return $result;
  }
  
  function getSubcatergories($cid)
  { 
    $res = tep_db_query("select c.categories_id cid,cd.categories_name cname from categories_description cd,categories c where c.categories_id = cd.categories_id and cd.site_id = 0 and c.parent_id =".$cid." order by c.sort_order,cd.categories_name" );
    while ( $col = @tep_db_fetch_array($res))
      {

        if (countSubcategories($col['cid'])) {
           $col['sub'] =  getSubcatergories($col['cid']);
          }
        $result[] =$col;
      }
    return $result;
  }
  
function makeCheckbox($arrCategories,$selectValue = Fales,$startName='')
{
  //echo $selectValue;
  $result = '<ul class="change_one_list">';

  foreach ($arrCategories as $cate1 ) {
    //如果有子，则本条记录为 grop
    $flag=true;
    if (count($cate1['sub'])) {
      if($selectValue != 'Fales'){
      foreach($selectValue as $select) {
        if($select == $cate1['cid']) {
        $result .= '<li class="change_one_list_main"><input type = "checkbox"
          checked="checked" name="ocid[]" value =
          "'.$cate1['cid'].'"><b>'.$cate1['cname']. '</b></li>';
        $flag=false;
        }
      }
      if($flag) {
        $result .= '<li class="change_one_list_main"><input type = "checkbox" name="ocid[]" value =
          "'.$cate1['cid'].'"><b>'.$cate1['cname']. '</b></li>';
      }
      }else{
        $result .= '<li class="change_one_list_main"><input type = "checkbox" name="ocid[]" value =
          "'.$cate1['cid'].'"><b>'.$cate1['cname']. '</b></li>';
        
      }
      }
  }
        $result .= '</ul>';
  return $result;
}

//分离cpath
function cpathPart($cpath,$which=1) {
  $a = $cpath;
  if (strpos($a ,'_')){
    if($which ==1){
      $b = substr($a,0,strpos($a,'_'));
    }else {
      //$b = substr($a,strpos($a,'_')+1);
      $arr = explode('_',$a);
      return $arr[count($arr)-1];
    }
    return $b;
  }
  return $a;
}

function makeSelectOption($arrCategories,$selectValue = Fales,$startName='')
{
  //echo $selectValue;
  $result = '';

  foreach ($arrCategories as $cate1 ) {
    //如果有子，则本条记录为 grop
    if (count($cate1['sub'])) {
        $result .= '<optgroup label = "'.$cate1['cname'].'">';
        $result .= makeSelectOption($cate1['sub'],$selectValue,$cate1['cname'].'_');
        $result .= '</optgroup>';
      }else{
      $result .= '<option ';
      if ($cate1['cid']==$selectValue) 
        {
          $result .='selected = "true"';
        }
      $result .= 'value ="'.$cate1['cid'].'">'.$startName.$cate1['cname'].'</option>';
      }
  }
}

if (!function_exists('json_encode'))
  {
  function json_encode($a=false)
  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
      {
        if (is_float($a))
          {
            // Always use "." for floats.
            return floatval(str_replace(",", ".", strval($a)));
          }

        if (is_string($a))
          {
            static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
            return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
          }
      else        return $a;
      }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
      {
        if (key($a) !== $i)
          {
        $isList = false;
        break;
          }
      }
    $result = array();
    if ($isList)
      {
        foreach ($a as $v) $result[] = json_encode($v);
        return '[' . join(',', $result) . ']';
      }
    else    {
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
  }
      function update_products_dougyousya($product_id, $dougyousya_id) {
        if (tep_db_num_rows(tep_db_query("select * from set_products_dougyousya where product_id = '".$product_id."'"))) {
          return tep_db_perform("set_products_dougyousya", array('dougyousya_id' => tep_db_prepare_input($dougyousya_id)), 'update', 'product_id = \'' . tep_db_prepare_input($product_id) . '\'');
        } else {
          return tep_db_perform("set_products_dougyousya", array('product_id' => $product_id, 'dougyousya_id' => tep_db_prepare_input($dougyousya_id)));
        }
      }
      
      function get_products_dougyousya($products_id) {
        $data = tep_db_fetch_array(tep_db_query("select * from set_products_dougyousya where product_id='".$products_id."'"));
        if($data) {
          return $data['dougyousya_id'];
        } else {
          return 0;
        }
      }
      
      function get_all_products_dougyousya($categories_id,$products_id) {
        $arr = array();
        $query = tep_db_query("select dougyousya_id from set_dougyousya_categories where categories_id = '".$categories_id."'");
        while($data = tep_db_fetch_array($query))
        {
          $arr[] = $data;
        }
        return $arr;
      }
      
      function get_dougyousya_history($products_id, $dougyousya_id) {
        $data = tep_db_fetch_array(tep_db_query("select * from set_dougyousya_history where products_id='".$products_id."' and dougyousya_id='".$dougyousya_id."' order by last_date desc"));
         if($data) {
          return $data['dougyosya_kakaku'];
        } else {
          return 0;
        }
      }
      
  function tep_get_products_by_categories_id($categories_id,$status=null) {
    $arr = array();
    $query = tep_db_query("select distinct p.*,pd.* from products p, products_description pd, products_to_categories p2c where p.products_id=pd.products_id and p2c.products_id=p.products_id and categories_id='".$categories_id."' and pd.site_id='0' ".($status===null?'':" and pd.products_status='1'")." order by pd.products_name");
    while ($product = tep_db_fetch_array($query)) {
      $arr[] = $product;
    }
    return $arr;
  }
  
  function tep_get_kakuukosuu_by_products_id($categories_id, $products_id) {
    $data = tep_db_fetch_array(tep_db_query("select * from set_menu_list where categories_id='".$categories_id."' and products_id='".$products_id."'"));
    if ($data) {
      return (int)$data['kakuukosuu'];
    } else {
      return 0;
    }
  }
  
  function tep_get_kakaku_by_products_id($categories_id, $products_id){
    $data = tep_db_fetch_array(tep_db_query("select * from set_menu_list where categories_id='".$categories_id."' and products_id='".$products_id."'"));
    if ($data) {
      return (int)$data['kakaku'];
    } else {
      return 0;
    }
  }

  // for categories_admin jumper
  function tep_get_category_tree_cpath($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
    global $languages_id;

    if (!is_array($category_tree_array)) $category_tree_array = array();
    if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

    if ($include_itself) {
      $category_query = tep_db_query("select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . $languages_id . "' and cd.categories_id = '" . $parent_id . "' and cd.site_id='0'");
      $category = tep_db_fetch_array($category_query);
      $category_tree_array[] = array('id' => $parent_id, 'text' => $category['categories_name']);
    }

    $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . $languages_id . "' and c.parent_id = '" . $parent_id . "' and site_id ='0' order by c.sort_order, cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      if ($exclude != $categories['categories_id']) $category_tree_array[] = array('id' => ($parent_id != 0 ? (tep_get_parent_cpath($parent_id) . '_') : '') . $categories['categories_id'], 'text' => $spacing . $categories['categories_name']);
      $category_tree_array = tep_get_category_tree_cpath($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
    }

    return $category_tree_array;
  }
  function tep_get_parent_cpath($cid){
    $p = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id='".$cid."'"));
    if ($p) {
      if ($p['parent_id'] != '0') {
        return $p['parent_id'] . '_'. $cid;
      } else {
        return $cid;
      }
    } else {
      return $cid;
    }
  }
  
  
  //function tep_update_kakuukosuu
  

function spliteOroData($orodata){
    $new_lines = array();
    $cr = array("\r\n", "\r");   // 改行コード置換用配
    $data = trim($orodata);
    $data = str_replace($cr, "\n",$data);  // 改行コードを統一
    $lines = explode("\n", $data);
    foreach($lines as $key => $line) {
      $lines[$key] = trim($line);
      if(strlen(trim($line)) == 0)unset($lines[$key]);
    }
    foreach($lines as $l){
      $new_lines[] = $l;
    }
    return $new_lines;
}

function tep_get_customers_fax_by_id($cid)
{
  $query = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id='".$cid."'");
  $customers = tep_db_fetch_array($query);
  return $customers['customers_fax'];
}
  // orders.php
  function tep_get_orders_products_names($orders_id) {
    $str = '';
    $orders_products_query = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".$orders_id."'");
    while ($p = tep_db_fetch_array($orders_products_query)) {
      $str .= $p['products_name'].' ';
    }
    return $str;
  }
  // orders.php
  function tep_get_orders_products_string($orders) {
    $str = '';


    $str .= '<table border="0">';
    
    /*
    $str .= '<tr><td class="mian" align="center" colspan="2"><table width="100%"><tr><td class="main" width="50%" align="left">';
    if ($orders['orders_inputed_flag']) {
      $str .= '<font color="red"><b>入力済み</b></font>';
    }
    $str .= '</td><td class="mian" align="right" width="50%">';
    if ($orders['orders_comment']) {
      $str .= '<font color="blue"><b>メモ有り</b></font>';
    }
    */
    $str .= '<tr><td class="mian" align="left" colspan="2">';
    if ($orders['orders_inputed_flag']) {
      $str .= '<font color="red"><b>入力済み</b></font>';
    }
    /*
    $str .= '</td></tr><tr><td class="mian" align="left"colspan="2">';
    if ($orders['orders_important_flag']) {
      $str .= '<font color="red"><b>重要</b></font>';
    }
    */
    $str .= '</td></tr><tr><td class="mian" align="left"colspan="2">';
    if ($orders['orders_care_flag']) {
      $str .= '<font color="red"><b>取扱注意</b></font>';
    }
    $str .= '</td></tr><tr><td class="mian" align="left"colspan="2">';
    if ($orders['orders_comment']) {
      $str .= '<font color="blue"><b>メモ有り</b></font>';
    }
    
    $pay_time = tep_get_orders_status_history_time($orders['orders_id'], 9);
    $str .= '</td></tr>';
    $str .= '<tr><td colspan="2">&nbsp;</td></tr>';
    $str .= '<tr><td class="main" width="60"><b>支払方法：</b></td><td class="main" style="color:darkred;"><b>'.$orders['payment_method'].'</b></td></tr>';
    if ($orders['payment_method'] != '銀行振込(買い取り)') {
      $str .= '<tr><td class="main"><b>入金日：</b></td><td class="main" style="color:red;"><b>'.($pay_time?date('m月d日',strtotime($pay_time)):'入金まだ').'</b></td></tr>';
    }
    $str .= '<tr><td colspan="2">&nbsp;</td></tr>';
    $str .= '<tr><td class="main"><b>オプション：</b></td><td class="main" style="color:blue;"><b>'.$orders['torihiki_houhou'].'</b></td></tr>';
    
    $orders_products_query = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".$orders['orders_id']."'");
    while ($p = tep_db_fetch_array($orders_products_query)) {
      $products_attributes_query = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id='".$p['orders_products_id']."'");
      $products_rate_query = tep_db_query("select products_attention_1 from ".TABLE_PRODUCTS." where products_id = '".$p['products_id']."'");
      $products = tep_db_fetch_array($products_rate_query);
      $tmp = explode('//', $products['products_attention_1']);
      $p_rate = $tmp[1];

      $str .= '<tr><td class="main"><b>商品：</b></td><td class="main">'.$p['products_name'].'</td></tr>';
      $str .= '<tr><td class="main"><b>個数：</b></td><td class="main">'.$p['products_quantity'].'個'.tep_get_full_count($p['products_quantity'], $p_rate, $p['products_rate']).'</td></tr>';
      while($pa = tep_db_fetch_array($products_attributes_query)){
        $str .= '<tr><td class="main"><b>'.$pa['products_options'].'：</b></td><td class="main">'.$pa['products_options_values'].'</td></tr>';
      }
      $str .= '<tr><td class="main"><b>キャラ名：</b></td><td style="font-size:20px;color:#407416;"><b>'.$p['products_character'].'</b></td></tr>';
      $names = tep_get_computers_names_by_orders_id($orders['orders_id']);
      if ($names) {
        $str .= '<tr><td class="main"><b>PC：</b></td><td class="main">'.implode('&nbsp;,&nbsp;', $names).'</td></tr>';
      }
      $str .= '<tr><td class="main"></td><td class="main"></td></tr>';
    }
    $str .= '</table>';
    $str=str_replace("\n","",$str);
    $str=str_replace("\r","",$str);
    return htmlspecialchars($str);
  }
  
  // orders.php
  function tep_get_computers_names_by_orders_id($orders_id)
  {
    $names = array();
    $o2c_query = tep_db_query("select * from ".TABLE_ORDERS_TO_COMPUTERS." o2c, ".TABLE_COMPUTERS." c where c.computers_id=o2c.computers_id and o2c.orders_id = '".$orders_id."' order by sort_order asc");
    while($o = tep_db_fetch_array($o2c_query)) {
      $names[] = $o['computers_name'];
    }
    return $names;
  }
  
  // orders.php
  function tep_get_computers()
  {
    $computers = array();
    $computers_query = tep_db_query("select * from ".TABLE_COMPUTERS." order by sort_order asc");
    while ($c = tep_db_fetch_array($computers_query)) {
      $computers[] = $c;
    }
    return $computers;
  }
  
  // orders.php
  function tep_get_computers_by_orders_id($oid)
  {
    $c = array();
    $o2c_query = tep_db_query("select * from ".TABLE_ORDERS_TO_COMPUTERS." where orders_id = '".$oid."'");
    while ($o2c = tep_db_fetch_array($o2c_query)) {
      $c[] = $o2c['computers_id'];
    }
    return $c;
  }
// orders.php
  function tep_get_orders_changed($orders_id, $language_id = '') {
    global $languages_id;

    if (!$language_id) $language_id = $languages_id;
    $orders_query = tep_db_query("select * from ".TABLE_ORDERS." where orders_id='".$orders_id."'");
    $orders = tep_db_fetch_array($orders_query);
    return $orders['orders_status'] . $orders['last_modified'];
  }
  // orders.php
  function tep_get_orders_status_history_time($orders_id, $orders_status_id){
    $history = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id='".$orders_id."' and orders_status_id='".$orders_status_id."' order by date_added desc"));
    return $history['date_added'];
  }
  // orders.php
  function tep_get_orders_status_history_notified($orders_id, $orders_status_id){
    $history = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id='".$orders_id."' and orders_status_id='".$orders_status_id."' order by date_added desc"));
    return $history['customer_notified'];
  }
// orders.php
  function tep_orders_finished($orders_id, $language_id = '') {
    global $languages_id;

    if (!$language_id) $language_id = $languages_id;
    
    $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS." where orders_id='".$orders_id."'"));
    $order_status = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$order['orders_status']."'"));
    return $order_status['finished'];
  }
function tep_get_siteurl_name($siteurl)
{
  $sql = "select sitename from ".TABLE_SITENAME." 
         where siteurl='".$siteurl."'";
  $query = tep_db_query($sql);
  if(tep_db_num_rows($query)>0){
    $res = tep_db_fetch_array($query);
    return $res['sitename'];
  }else{
    return $siteurl;
  }
}

// orders.php
function get_guest_chk($customers_id)
{
  $customers = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id='".$customers_id."'"));
  return $customers['customers_guest_chk'];
}

// orders.php
function tep_high_light_by_keywords($str, $keywords)
{
  $k = $rk= explode('|',$keywords);
  foreach($k as $key => $value){
    $rk[$key] = '<font style="background:red;">'.$value.'</font>';
  }
  return str_replace($k, $rk, $str);
}

// telecom_unknow.php
function tep_match_by_keywords($str, $keywords)
{
  $k = explode('|',$keywords);
  foreach($k as $key => $value){
    if (preg_match('/'.$value.'/', $str)) {
      //exit($value.'+'.$keywords);
      return true;
    }
  }
}

// telecom_unknow.php
function tep_get_first_products_name_by_orders_id($orders_id)
{
  $p = tep_db_fetch_array(tep_db_query("select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id='".$orders_id."'"));
  return $p['products_name'];
}

// 取得支付时间，当天或者下一个工作日。
// orders.php
function tep_get_pay_day($time = null){
  //echo strtotime(date('Y-m-d H:00:00', strtotime($time)));
  //echo strtotime(date('Y-m-d H:00:00'));
  if ($time === null) {
    $time = date('Y-m-d H:i:s');
  }
  if (strtotime(date('Y-m-d 00:00:00', strtotime($time))) == strtotime(date('Y-m-d 00:00:00'))) {
    $c = tep_db_fetch_array(tep_db_query("select * from " . TABLE_BANK_CALENDAR . " where cl_ym = '".date('Ym',strtotime($time))."'"));
    for($i=date('d')-1;$i<strlen($c['cl_value']);$i++){
      // 如果是当天
      if (date('d')-1 == $i) {
        // 如果当天营业
        if ($c['cl_value'][$i] == '0') {
          if (date('H',strtotime($time)) < 15) {
            return date('Y-m',strtotime($time)).'-'.date('d');
          }
        }
      } else {
        // 如果下一天营业
        if ($c['cl_value'][$i] == '0') {
          return date('Y-m',strtotime($time)).'-'.($i+1);
        }
      }
    }
    //exit(date('Y-m-d H:i:s', $time));
    return tep_get_pay_day(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s', strtotime($time)).' + 1 day')));
  } else {
    $c = tep_db_fetch_array(tep_db_query("select * from " . TABLE_BANK_CALENDAR . " where cl_ym = '".date('Ym',strtotime($time))."'"));
    for($i=0;$i<strlen($c['cl_value']);$i++){
        if ($c['cl_value'][$i] == '0') {
          return date('Y-m',strtotime($time)).'-'.($i+1);
        }
    }
    
    return tep_get_pay_day(date('Y-m-d H:i:s', strtotime($time.' + 1 day')));
  }
  //echo $c['cl_value'];
}
// orders.php
// 0 卖 2 信用卡 1 买 
function tep_get_order_type($orders_id){
  $oq = tep_db_fetch_array(tep_db_query("select * from orders_questions where orders_id='".$orders_id."'"));
  if ($oq['orders_questions_type']) {
    return $oq['orders_questions_type'];
  } else {
    return 0;
  }
}

/*
function tep_get_order_type($orders_id){
  $type = 0;
  $query = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS." op,".TABLE_PRODUCTS." p where p.products_id=op.products_id and op.orders_id='".$orders_id."'");
  while($op = tep_db_fetch_array($query)){
    if ($op['products_bflag'] == 0) {
      if ($type == 1 || $type == 0) {
        $type = 1;
      } else {
        $type = 3;
      }
    } else {
      if ($type == 2 || $type == 0) {
        $type = 2;
      } else {
        $type = 3;
      }
    }
  }
  return $type;
}
*/

function tep_questions_can_show($orders_id){
  /*
  ($('#q_1_1_0').attr('checked') || $('#q_1_1_1').attr('checked')) 
  && ($('#q_12_1_0').attr('checked') || $('#q_12_1_1').attr('checked'))
  && $('#q_13_1').attr('checked')
  && ($('#q_6_1_0').attr('checked') || $('#q_6_1_1').attr('checked'))
  && $('#q_14_1').attr('checked')
  //&& $('#q_15_1').attr('checked')
  && $('#q_16_2').attr('checked')
  */
  // 15: 3,4,5,7,8
  // 有过受领通知（13）状态的
  $oq = tep_db_fetch_array(tep_db_query("select * from orders_questions where orders_id = '".$orders_id."'"));
  if (
    $oq 
    && strlen($oq['q_1_1'] !== 0)
    && strlen($oq['q_12_1'] !== 0)
    && $oq['q_13_1'] 
    && strlen($oq['q_6_1'] !== 0)
    && $oq['q_14_1'] 
    
    && !$oq['q_15_1'] 
    && !$oq['q_16_2'] 
    && !$oq['q_15_3']
    && !$oq['q_15_4']
    && !$oq['q_15_5']
    && !$oq['q_15_7']
    && !$oq['q_15_8']
  ) {
    //return true;
    
    if (tep_db_num_rows(tep_db_query("select * from ".TABLE_ORDERS_STATUS_HISTORY." where orders_status_id='13' and orders_id='".$orders_id."'"))) {
      return true;
    } else {
      return false;
    }
  } else {
/*
    print_r($oq);
    var_dump(strlen($oq['q_1_1'] !== 0));
    var_dump(strlen($oq['q_12_1'] !== 0));
    var_dump( $oq['q_13_1']);
    var_dump(strlen($oq['q_6_1'] !== 0));
    var_dump($oq['q_14_1'] );
    var_dump(!$oq['q_15_1'] );
    var_dump(!$oq['q_16_2']);
    var_dump(!$oq['q_15_3']);
    var_dump(!$oq['q_15_4']);
    var_dump(!$oq['q_15_5']);
    var_dump(!$oq['q_15_7']);
    var_dump(!$oq['q_15_8']);
*/
    
    return false;
  }
  
}

function tep_display_google_results(){
  // 谷歌关键字结果显示停止条件
  $stop_site_url = array(
      //"iimy.co.jp",
      //"www.iimy.co.jp",
  );
  if(isset($_GET['cPath'])&&$_GET['cPath']!=''){
  $categories_id = array_pop(explode('_',$_GET['cPath']));
  /*
  $record_sql = "select tr.siteurl as url 
                from ".TABLE_RECORD." tr
                where tr.session_id =(select max(r.session_id) from ".TABLE_RECORD." r left
                    join ".TABLE_CATEGORIES_TO_MISSION." c2m on c2m.mission_id =
                    r.mission_id where c2m.categories_id ='".$categories_id."')
                order by tr.order_total_number";
  */
  $record_sql = "select tr.siteurl as url
              from ".TABLE_RECORD." tr left join "
              .TABLE_CATEGORIES_TO_MISSION." c2m
              on tr.mission_id = c2m.mission_id 
              where c2m.categories_id = '".$categories_id."'
              order by tr.order_total_number";
  $record_query = tep_db_query($record_sql);
  $siturl = '';
  $seach_categoties_sql = "SELECT cd.categories_name as categories_name,
                                  m.keyword
                           FROM ".TABLE_CATEGORIES_TO_MISSION." c2m,
                                ".TABLE_CATEGORIES_DESCRIPTION." cd ,
                                ".TABLE_MISSION." m
                           WHERE cd.categories_id = c2m.categories_id 
                             AND m.id = c2m.mission_id
                             AND c2m.categories_id = '".$categories_id."'
                             AND cd.site_id = '0'";
  $seach_categoties_query = tep_db_query($seach_categoties_sql);
  echo "<tr><td colspan='4'>";
  echo '<table class="search_class" width="100%" cellspacing="0" cellpadding="2" border="0">';
  if(tep_db_num_rows($seach_categoties_query)>0){
  $seach_categoties_res = tep_db_fetch_array($seach_categoties_query);
  echo "<tr class='dataTableHeadingRow'><td class='dataTableHeadingContent' colspan='3'>";
  echo $seach_categoties_res['categories_name'];
  echo sprintf(TEXT_GOOGLE_SEARCH, $seach_categoties_res['keyword']);
  echo "</td></tr>";
  if(tep_db_num_rows($record_query)>0){
  $i=1;
  $url_arr = array();
  while($record_res = tep_db_fetch_array($record_query)){
    if(in_array($record_res['url'],$url_arr)){
      continue;
    }else{
      $url_arr[] = $record_res['url'];
    }
    if($i >= 10){
      $i=1;
      break;
    }
    $i++;
  }
//  while($record_res = tep_db_fetch_array($record_query)){
 $icount = 1; //序号
  foreach($url_arr as $distinct_url){
    if($icount%2==0){
      echo "<tr class='dataTableSecondRow'>";
    }else{
      echo "<tr class='dataTableRow'>";
    }
    if(in_array($distinct_url,$stop_site_url)){
      $search_message = sprintf(TEXT_FIND_DATA_STOP, $distinct_url);
    echo "<td class='dataTableContent search_class_td' style='width:20px'>&nbsp;".$icount++.":"."</td>";
    echo "<td class='dataTableContent' ><b>".tep_get_siteurl_name($distinct_url)."</b></td>";
    echo "<td class='dataTableContent' >";
    /*
    echo "<a href='".tep_href_link(FILENAME_RECORD,
        'action=unshow&cID='.$_GET['cID'].'&cPath='.$_GET['cPath'].'&url='.$prama_url).
        "'>".TEXT_UNSHOW."</a>";
    */
    echo "<a href='".tep_href_link(FILENAME_RECORD,
        'action=rename&act='.$_GET['action'].'&cID='.$_GET['cID'].'&cPath='.$_GET['cPath'].'&url='.$prama_url).
        "'>".TEXT_RENAME."</a>";
    echo "</td></tr>";
      break;
    }
    $prama_url = str_replace('.','_',$distinct_url); 
    echo "<td class='dataTableContent search_class_td' style='width:20px'>&nbsp;".$icount++.":"."</td>";
    echo "<td class='dataTableContent' >".tep_get_siteurl_name($distinct_url)."</td>";
    echo "<td class='dataTableContent' >";
    /*
    echo "<a href='".tep_href_link(FILENAME_RECORD,
        'action=unshow&cID='.$_GET['cID'].'&cPath='.$_GET['cPath'].'&url='.$prama_url).
        "'>".TEXT_UNSHOW."</a>";
    */
    echo "<a href='".tep_href_link(FILENAME_RECORD,
        'action=rename&act='.$_GET['action'].'&cID='.$_GET['cID'].'&cPath='.$_GET['cPath'].'&url='.$prama_url).
        "'>".TEXT_RENAME."</a>";
    echo "</td></tr>";
    $i++;
  }
   if(!isset($search_message)){
    if($i<11){
    $search_message = sprintf(TEXT_NOT_ENOUGH_DATA, $i-1);
    }else{
    $search_message = sprintf(TEXT_LAST_SEARCH_DATA, $i-1);
    }
   }
  }else{
    $search_message = TEXT_NO_DATA;
  }
  }else{
    $search_message = TEXT_NO_SET_KEYWORD;
  }
  echo "<tr><td class='smalltext' colspan='3'>";
  echo $search_message;
  echo "</td></tr>";
  echo "</table>";
  echo "</td></tr>";
}
}
//取得分类的父id
function tep_get_category_parent_id($cid){
  if ($cid) {
    $c = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id='".$cid."'"));
    return $c['parent_id'];
  } else {
    return 0;
  }
}

// 取得商品的分类
function tep_get_products_parent_id($pid){
  $carr = array();
  $query = tep_db_query("select * from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
  while($p2c = tep_db_fetch_array($query)){
    $carr[] = $p2c['categories_id'];
  }
  return $carr[0];
}

// 取得关联商品名
function tep_get_relate_products_name($pid) {
  $p = tep_db_fetch_array(tep_db_query("select relate_products_id from ".TABLE_PRODUCTS." where products_id='".$pid."'"));
  $r = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$p['relate_products_id']."'"));
  return $r['products_name'];
}

function tep_get_products_rate($pid) {
  $p =  tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." where products_id='".$pid."'"));
  $t = explode('//',$p['products_attention_1']);
  $n = str_replace(',','',tep_get_full_count_in_order(1, $t[1]));
  preg_match_all('/(\d+)/',$n,$out);
  return $out[1][0];
}

function tep_check_symbol($str){
  $keywords = array(
    '~','!','@','#','$','%','^','&','*','(',')','_','+','`','=','[',']',';','\'','\\',',','.','/','<','>','?',':','"','{','}',' ','　'
  );
  foreach($keywords as $k){
    if (strpos($str,$k) !== false) {
      return false;
    }
  }
  return true;
}

function tep_check_romaji($romaji){
  /*
  if (!preg_match('/^[a-zA-Z0-9\-]+$/', $romaji)) {
    return false;
  }*/
  
  $keywords = array(
    'page',
    'reviews',
    'info',
    'latest_news',
    '=','?','&'
  );
  foreach($keywords as $k){
    if (strpos($romaji,$k) !== false) {
      return false;
    }
  }
  return true;
}
function tep_get_product_inventory($pid) {
  $inventory_sql = "select max_inventory as `max`,min_inventory as `min` 
    from ".TABLE_PRODUCTS." WHERE products_id='".$pid."'";
  $inventory_res = tep_db_query($inventory_sql);
  return tep_db_fetch_array($inventory_res);
}
function tep_upload_products_to_inventory($pid,$status){
  $sql = "select products_id from ".TABLE_PRODUCTS_TO_INVENTORY
    ." where products_id ='".$pid."'";
  $res = tep_db_query($sql);
  if(tep_db_fetch_array($res)){
     $method = 'update';    
  }else{
     $method = 'insert';
  }
  $invArr = tep_get_inventory($pid);
  $cpath = '';
  if(count($invArr['cpath'])==1) {
    $cpath =$invArr['cpath'][0];
  }else {
    $cpath =join('_',array_reverse($invArr['cpath']));
  }
  $inventory_data_arr = array(
      'products_id' => $pid,
      'inventory_status' => $status,
      'last_date' => 'now()',
      'cpath'=>$cpath,
      );

  tep_db_perform(TABLE_PRODUCTS_TO_INVENTORY,$inventory_data_arr,$method,
      "products_id='".$pid."'");

}
function tep_get_inventory($pid){
    $categories_id = tep_get_products_parent_id($pid);
    $parent_id = $categories_id;
    $cpath = array();
    while($parent_id != 0){
      $categories_id = $parent_id;
      $cpath[] = $categories_id;
      $parent_id = tep_get_category_parent_id($categories_id);
    }
    $inventory_arr = tep_get_product_inventory($pid);
    $inventory_arr['cpath'] = $cpath;
    return $inventory_arr;
}

function tep_check_categories_exists($cid, $site_id)
{
  $exists_ca_query = tep_db_query("select * from ".TABLE_CATEGORIES_DESCRIPTION." where site_id = '".(int)$site_id."' and categories_id = '".$cid."'");
  
  return tep_db_num_rows($exists_ca_query);
}

function tep_create_site_categories($cid, $site_id)
{
  $zero_ca_query = tep_db_query("select * from ".TABLE_CATEGORIES_DESCRIPTION." where site_id = 0 and categories_id = '".$cid."'");
  $zero_ca_res = tep_db_fetch_array($zero_ca_query); 
  if ($zero_ca_res) { 
  $sql_data_array = array(
      'categories_name' => tep_db_prepare_input($zero_ca_res['categories_name']), 
      'romaji' => tep_db_prepare_input($zero_ca_res['romaji']), 
      'categories_meta_text' => tep_db_prepare_input($zero_ca_res['categories_meta_text']), 
      'seo_name' => tep_db_prepare_input($zero_ca_res['seo_name']), 
      'seo_description' => tep_db_prepare_input($zero_ca_res['seo_description']), 
      'categories_header_text' => tep_db_prepare_input($zero_ca_res['categories_header_text']), 
      'categories_footer_text' => tep_db_prepare_input($zero_ca_res['categories_footer_text']), 
      'text_information' => tep_db_prepare_input($zero_ca_res['text_information']), 
      'meta_keywords' => tep_db_prepare_input($zero_ca_res['meta_keywords']), 
      'meta_description' => tep_db_prepare_input($zero_ca_res['meta_description']), 
      'categories_id' => tep_db_prepare_input($zero_ca_res['categories_id']), 
      'language_id' => tep_db_prepare_input($zero_ca_res['language_id']), 
      'site_id' => $site_id, 
      );
  tep_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
  }
}

function tep_set_categories_status_by_site_id($categories_id, $status, $site_id)
{
  tep_db_query("UPDATE `".TABLE_CATEGORIES_DESCRIPTION."` SET `categories_status` = '".intval($status)."' WHERE `categories_id` =".$categories_id." and `site_id` = '".$site_id."' LIMIT 1 ;");
  return true;
}

function tep_check_products_exists($pid, $site_id)
{
  $exist_pro_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$pid."' and site_id = '".(int)$site_id."'");
  return tep_db_num_rows($exist_pro_query);
}

function tep_create_products_by_site_id($pid, $site_id)
{
  $zero_pro_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$pid."' and site_id = '0'");
  $zero_pro_res = tep_db_fetch_array($zero_pro_query);
  if ($zero_pro_res) {
    $sql_data_array = array(
        'products_id' => tep_db_prepare_input($zero_pro_res['products_id']), 
        'language_id' => tep_db_prepare_input($zero_pro_res['language_id']), 
        'products_name' => tep_db_prepare_input($zero_pro_res['products_name']), 
        'products_description' => tep_db_prepare_input($zero_pro_res['products_description']), 
        'site_id' => $site_id, 
        'products_url' => tep_db_prepare_input($zero_pro_res['products_url']), 
        'products_viewed' => tep_db_prepare_input($zero_pro_res['products_viewed']), 
        'romaji' => tep_db_prepare_input($zero_pro_res['romaji']), 
        'products_status' => tep_db_prepare_input($zero_pro_res['products_status']), 
        ); 
    tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
  }
}

function tep_set_product_status_by_site_id($products_id, $status, $site_id) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_status = '1' where products_id = '" . $products_id . "' and site_id = '".$site_id."'");
    } elseif ($status == '2') {
      return tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_status = '2' where products_id = '" . $products_id . "' and site_id = '".$site_id."'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_status = '0' where products_id = '" . $products_id . "' and site_id = '".$site_id."'");
    } elseif ($status == '3') {
      return tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_status = '3' where products_id = '" . $products_id . "' and site_id = '".$site_id."'");
    } else {
      return -1;
    }
}

function tep_set_all_category_status($cID, $cstatus)
{
  $site_arr = array(0); 
  $site_query = tep_db_query("select * from ".TABLE_SITES); 
  while ($site_res = tep_db_fetch_array($site_query)) {
    $site_arr[] = $site_res['id']; 
  }
  
  foreach ($site_arr as $key => $value) {
    if (!tep_check_categories_exists($cID, $value)) {
      tep_create_site_categories($cID, $value);   
    }
    tep_db_query("UPDATE `".TABLE_CATEGORIES_DESCRIPTION."` SET `categories_status` = '".$cstatus."' where `categories_id` = '".$cID."' and `site_id` = '".$value."'"); 
  }
}

function tep_set_all_product_status($pID, $pstatus)
{
  $site_arr = array(0); 
  $site_query = tep_db_query("select * from ".TABLE_SITES); 
  while ($site_res = tep_db_fetch_array($site_query)) {
    $site_arr[] = $site_res['id']; 
  }
  
  foreach ($site_arr as $key => $value) {
    if (!tep_check_products_exists($pID, $value)) {
      tep_create_products_by_site_id($pID, $value); 
    }
     
    tep_db_query("UPDATE `".TABLE_PRODUCTS_DESCRIPTION."` SET `products_status` = '".$pstatus."' where `products_id` = '".$pID."' and `site_id` = '".$value."'");  
  }
}

function tep_set_category_link_product_status($cID, $cstatus, $site_id)
{
  $site_arr = array(); 
  $product_total_arr = array();
  $category_total_arr = array($cID); 
  
  switch ($cstatus) {
    case '2':
      $pstatus = 2;
      break;
    case '1':
      $pstatus = 0;
      break;
    case '3':
      $pstatus = 3;
      break;
    default:
      $pstatus = 1;
      break;
  }
  
  if ($site_id == 0) {
    $site_arr[] = '0'; 
    $site_query = tep_db_query("select * from ".TABLE_SITES);
    while ($site_res = tep_db_fetch_array($site_query)) {
      $site_arr[] = $site_res['id']; 
    }
  } else {
    $site_arr = array($site_id); 
  }
  
  $product_arr = tep_get_link_product_id_by_category_id($cID);
  if (!empty($product_arr)) {
    $product_total_arr = array_merge($product_total_arr, $product_arr); 
  }

  $child_category_query = tep_db_query("select * from ".TABLE_CATEGORIES." where parent_id = '".$cID."'");
  while ($child_category_res = tep_db_fetch_array($child_category_query)) {
    $category_total_arr[] = $child_category_res['categories_id']; 
    $product_arr = tep_get_link_product_id_by_category_id($child_category_res['categories_id']);
    if (!empty($product_arr)) {
      $product_total_arr = array_merge($product_total_arr, $product_arr); 
    }
    $child_child_category_query = tep_db_query("select * from ".TABLE_CATEGORIES." where parent_id = '".$child_category_res['categories_id']."'");
    
    while ($child_child_category_res = tep_db_fetch_array($child_child_category_query)) {
      $category_total_arr[] = $child_child_category_res['categories_id']; 
      $product_arr = tep_get_link_product_id_by_category_id($child_child_category_res['categories_id']);
      if (!empty($product_arr)) {
        $product_total_arr = array_merge($product_total_arr, $product_arr); 
      }
    }
  }
  
  foreach ($site_arr as $skey => $svalue) {
    foreach ($category_total_arr as $ckey => $cvalue) {
      if (!tep_check_categories_exists($cvalue, $svalue)) {
        tep_create_site_categories($cvalue, $svalue);
      }
      tep_set_categories_status_by_site_id($cvalue, $cstatus, $svalue);
    }

    foreach ($product_total_arr as $pkey => $pvalue) {
      if (!tep_check_products_exists($pvalue, $svalue)) {
        tep_create_products_by_site_id($pvalue, $svalue);
      }
      tep_set_product_status_by_site_id($pvalue, $pstatus, $svalue); 
    }
  }
}

function tep_get_link_product_id_by_category_id($category_id)
{
  $product_arr = array(); 
  $pro_to_ca_query = tep_db_query("select * from ".TABLE_PRODUCTS_TO_CATEGORIES." where categories_id = '".$category_id."'");
  while ($pro_to_ca_res = tep_db_fetch_array($pro_to_ca_query)) {
    $product_arr[] = $pro_to_ca_res['products_id']; 
  }
  return $product_arr;
}
function replace_store_name($str,$product_id,$site_id) {
  $name =  tep_get_site_name_by_id($site_id);
  if($site_id!=0){
  return str_replace('#STORE_NAME#', $name, $str);
  }
  return $str;
}

// 根据pid数据取得提醒商品
function tep_get_cart_products($pid,$tid,$buyflag){
  $raw = "
    select distinct(p.products_id) 
    from products_to_tags p2t,products p ,products_to_carttag p2c
    where 
    p2c.tags_id in (".join(',',$tid).")
    and p2c.tags_id = p2t.tags_id
    and p.products_bflag = ".$buyflag."
    and p.products_id = p2t.products_id
    and p.products_id != ".$pid."
  "; 
  //echo $raw;
  $query = tep_db_query($raw);
  $arr = array();
  while($p = tep_db_fetch_array($query)){
    $arr[] = $p['products_id'];
  }
  return $arr;
}

function tep_is_oroshi($cid){
  $query = tep_db_query("select customers_guest_chk from ".TABLE_CUSTOMERS." where customers_id='".$cid."'");
  $c = tep_db_fetch_array($query);
  return $c['customers_guest_chk'] == 9;
}

function getReachTime()
{
   $date_arr = array();  
   $now_time = time();
   
   $now_minute = (int)date('i', $now_time);
   $now_hour = (int)date('H', $now_time); 
   
   $tmp_num = round($now_minute/10);
   
   if ($tmp_num == 6) {
     if ($now_hour == 23) {
       $date_arr[] = date('Y-m-d', strtotime('+1 day', $now_time)); 
       $date_arr[] = '01';
       $date_arr[] = '00';
     } else {
       $date_arr[] = date('Y-m-d', $now_time);
       $date_arr[] = date('H', strtotime('+1 hour', $now_time)); 
       $date_arr[] = '00'; 
     }
   } else {
     $date_arr[] = date('Y-m-d', $now_time); 
     $date_arr[] = date('H', $now_time); 
     $date_arr[] = sprintf('%2d0', $tmp_num); 
   }
   return $date_arr;
}

function tep_calc_products_price($real_qty = 0, $virtual_qty = 0){
  if ($real_qty > 0) {
    return $real_qty + $virtual_qty;
  } else {
    return 0;
  }
  //return 100;
}
//获取用户对网站的权限 用于判断用户能否对last_news页面的网站新闻进行管理
 function  editPermission($site_permission,$site_id){

  $edit_p=FALSE;
  $site_arr=array();
  $site_arr=explode(",",$site_permission);//返回权限数组
  if($site_id == ''){
    $site_id = 0;
  }
  if(in_array($site_id,$site_arr)){//判断iste_id是否存在于权限数组中
    
    $edit_p=true;//true 说明有管理权限 可以在点击新闻时进行修改 
  }else if(($site_id =='' || $site_id ==0)&&$_SESSION['user_permission'] == 15){
    //判断 管理员 可以修改全部(all)
    $edit_p=true;
  }
  return $edit_p;
 }

function tep_get_conf_sid_by_id($id){
    return tep_db_fetch_array(tep_db_query("select  site_id  from " . TABLE_CONFIGURATION. " where configuration_id = '".$id."'"));
  }
  
function tep_get_rev_sid_by_id($id){
    return tep_db_fetch_array(tep_db_query("select  site_id  from " . TABLE_REVIEWS. " where reviews_id = '".$id."'"));
  }
