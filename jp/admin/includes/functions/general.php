<?php
/*
   $Id$
 */

/* -------------------------------------
    功能: 无权限修改 提示401
    参数: 无 
    返回值: 无
 ------------------------------------ */
function forward401()
{ 
  header($_SERVER["SERVER_PROTOCOL"] . " 401Not Found");
  require( DIR_WS_MODULES. '401.html');
  exit;
}

/* -------------------------------------
    功能: 没有找到页面 提示404
    参数: 无 
    返回值: 无
 ------------------------------------ */
function forward404()
{ 
  header($_SERVER["SERVER_PROTOCOL"] . " 404Not Found");
  require( DIR_WS_MODULES. '404.html');
  exit;
}

/* -------------------------------------
    功能: 不可以直接访问的页面 提示401
    参数: $page_name(string) URL  
    参数: $back_url(string) 返回URL  
    参数: $one_time_array(string) 信息 
    返回值: 无
 ------------------------------------ */
function one_time_pwd_forward401($page_name, $back_url = '', $one_time_array = array())
{ 
  $file_name = substr($page_name,7,strlen($page_name));
  $inpagelist = true;
  $pagelist = array(
'handle_payment_time.php', 
'pre_handle_payment_time.php', 
'ajax_preorders.php', 
'ajax_orders.php', 
'handle_new_preorder.php', 
'orders_csv_exe.php', 
'preorders_csv_exe.php', 
'pre_oa_answer_process.php', 
'oa_answer_process.php', 
'popup_image.php',
'posts.php',
'update_position.php',
'item_process.php',
'oa_ajax.php',
'preorder_item_process.php',
'set_ajax_dougyousya.php',
'upload.php',
'js2php.php',
'help.php',
'ajax.php'
      );
  foreach($pagelist as $page){
    if($file_name == $page){
      $inpagelist = false;
      break;
    }
  }
  
  if($inpagelist){
    header($_SERVER["SERVER_PROTOCOL"] . " 401Not Found");
    if (!empty($back_url)) {
      require( DIR_WS_MODULES. '401-unauthorized.php');
    } else {
      require( DIR_WS_MODULES. '401.html');
    }
    exit;
  }
}

/* -------------------------------------
    功能: 当条件成立的时候 401
    参数: $condition(boolean) 条件 
    返回值: 无
 ------------------------------------ */
function forward401If($condition)
{
  if ($condition)
  {
    forward403();
  }
}

/* -------------------------------------
    功能: 当条件不成立时 401
    参数: $condition(boolean) 条件 
    返回值: 无
 ------------------------------------ */
function forward401Unless($condition)
{
  if (!$condition)
  {
    forward401();
  }
}

/* -------------------------------------
    功能: 当条件成立时 404
    参数: $condition (boolean) 条件 
    返回值: 没有
 ------------------------------------ */
function forward404Unless($condition)
{
  if (!$condition)
  {
    forward404();
  }
}

/* -------------------------------------
    功能: 取得minitor的信息
    参数: 无 
    返回值: minitor的信息的HTML(string) 
 ------------------------------------ */
function tep_minitor_info(){
  $show_div = false;
  $errorString = array();
  $monitors  = tep_db_query("select id ,name,url from monitor m where m.enable='on'");
  while($monitor= tep_db_fetch_array($monitors)){
    $fiftheenbefore = date('Y-m-d H:i:s',time()-60*15);
    $logIn15 = tep_db_query("select * from monitor_log where ng > 0 and m_id =".$monitor['id'].' and created_at > "'.$fiftheenbefore.'"');
    $tmpRow = tep_db_fetch_array($logIn15);
    if(mysql_num_rows($logIn15)){ 
      //十五分钟内多于两件
      $tmpString  = TEXT_HEADER_NOTICE_ACCIDENT_HAPPEN.$tmpRow['name'].' <font class="error_monitor">'.date('m'.MONTH_TEXT.'d'.DAY_TEXT.'H'.HOUR_TEXT.'i'.MINUTE_TEXT.'s'.SECOND_TEXT,strtotime($tmpRow['created_at'])).'</font><br/><a ';
      if($show_div){
        $tmpString .='
          onMouseOver="show_monitor_error(\'minitor_'.$monitor['name'].'\',1,this)" 
          onMouseOut="show_monitor_error(\'minitor_'.$monitor['name'].'\',0,this)"';
      }
      $tmpString .=  'id="moni_'.$tmpRow['name'].'" class="monitor"
        href="'.$monitor['url'].'"
        target="_blank">'.TEXT_HEADER_NOTICE_CLICK_CONFIRM_LINK.'</a>'.TEXT_HEADER_NOTICE_CLICK_CONFIRM.'</div>';
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
      $log = "select name,obj, created_at from monitor_log where ng >0 and m_id = ".$monitor['id']. " order by id  desc limit 1";
      $logsResult = tep_db_fetch_array(tep_db_query($log));
      if ($logsResult){
        $aString = TEXT_HEADER_NOTICE_ACCIDENT_HAPPEN_FINAL_DAY . $logsResult['name'] . ' <a ';
        if($show_div){
          $aString.=  'onMouseOver="show_monitor_error(\'minitor_'.$logsResult['name'].'\',1,this)"
            onMouseOut="show_monitor_error(\'minitor_'.$logsResult['name'].'\',0,this)"';
        }
        $aString.=  'class="monitor_right" id="moni_'.$logsResult['name'].'" href="'.$monitor['url'].'" target="_blank">'.date('m'.MONTH_TEXT.'d'.DAY_TEXT.'H'.HOUR_TEXT.'i'.MINUTE_TEXT.'s'.SECOND_TEXT,strtotime($logsResult['created_at'])).'</a>';
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
      color="green">'.TEXT_HEADER_NOTICE_SYSTEM_CONDITION.'</font></td></tr>';
  }
  $returnString = '';
  foreach ($errorString as $error){
    $returnString .= '<tr><td></td><td align="right"><div class="text_box">'.$error.'</div></td></tr>';
  }
  if($no_error_string!=""){
    return $no_error_string;
  }
  return $returnString;

}

/* -------------------------------------
    功能: 跳转到指定URL 
    参数: $url(string) URL 
    返回值: 无 
 ------------------------------------ */
function tep_redirect($url) {
  global $logger;

  header('Location: ' . $url);

  if (STORE_PAGE_PARSE_TIME == 'true') {
    if (!is_object($logger)) $logger = new logger;
    $logger->timer_stop();
  }

  exit;
}

/* -------------------------------------
    功能: 根据指定字符转换字符串 
    参数: $data(string) 需要转换的字符串
    参数: $parse(string) 需要转换的特定字符(接受数组的方式)
    返回值: 转换后的字符串(string) 
 ------------------------------------ */
function tep_parse_input_field_data($data, $parse) {
  return strtr(trim($data), $parse);
}

/* -------------------------------------
    功能: 根据不同的条件,进行不同的转换 
    参数: $string(string) 需要转换的字符串
    参数: $translate(boolean) 条件 
    参数: $protected(boolean) 条件
    返回值: 转换后的字符串(string) 
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
    功能: 转换字符串 
    参数: $string(string) 需要转换的字符串
    返回值: 转换后的字符串(string)
 ------------------------------------ */
function tep_output_string_protected($string) {
  return tep_output_string($string, false, true);
}

/* -------------------------------------
    功能: 正则替换字符串 
    参数: $string(string) 需要替换的字符串
    返回值: 替换后的字符串(string)
 ------------------------------------ */
function tep_sanitize_string($string) {
  $string = ereg_replace(' +', ' ', $string);

  return preg_replace("/[<>]/", '_', $string);
}

/* -------------------------------------
    功能: 根据顾客的id,来获取顾客的名字 
    参数: $customers_id(int) 顾客id 
    返回值: 顾客的名字(string)
 ------------------------------------ */
function tep_customers_name($customers_id) {
  $customers = tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $customers_id . "'");
  $customers_values = tep_db_fetch_array($customers);

  return tep_get_fullname($customers_values['customers_firstname'], $customers_values['customers_lastname']);
}

/* -------------------------------------
    功能: 根据商品分类的id来生成相应的分类路径 
    参数: $current_category_id(int) 商品id 
    返回值: 分类路径(string)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 根据GET的信息生成url参数 
    参数: $exclude_array(array) 不包括的参数信息 
    返回值: url参数(string)
 ------------------------------------ */
function tep_get_all_get_params($exclude_array = '') {
  global $_GET;

  if ($exclude_array == '') $exclude_array = array();

  $get_url = '';

  reset($_GET);
  while (list($key, $value) = each($_GET)) {
    if (($key !='eof') && ($key != tep_session_name()) && ($key != 'error') && (!tep_in_array($key, $exclude_array))) $get_url .= $key . '=' . rawurlencode($value) . '&';
  }

  return $get_url;
}

/* -------------------------------------
    功能: 把相应的英文月份和星期替换为相应的日文月份和星期 
    参数: $raw_date(string) 日期 
    返回值: 格式化日期(string/boolean)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 格式化日期    
    参数: $raw_date(string) 日期 
    返回值: 日期(string)
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
    功能: 根据区域设置格式化本地时间/日期     
    参数: $raw_datetime(string) 日期 
    返回值: 本地时间/日期(string)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 合并数组     
    参数: $array1(array) 数组1
    参数: $array2(array) 数组2
    参数: $array3(array) 数组3
    返回值: 合并后的数组(array)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 在数组中搜索给定的值     
    参数: $lookup_value(string) 给定的值 
    参数: $lookup_array(array) 被搜索的数组
    返回值: 是否在数组里(boolean)
 ------------------------------------ */
function tep_in_array($lookup_value, $lookup_array) {
  if (function_exists('in_array')) {
    if (is_array($lookup_array)){
      if (in_array($lookup_value, $lookup_array)) return true;
    }
  } else {
    reset($lookup_array);
    while (list($key, $value) = each($lookup_array)) {
      if ($value == $lookup_value) return true;
    }
  }

  return false;
}

/* -------------------------------------
    功能: 根据条件生成商品分类目录     
    参数: $parent_id(string) 商品父分类  
    参数: $spacing(string) 间隔字符
    参数: $exclude(int) 排除掉的商品分类id
    参数: $category_tree_array(array) 给定的商品目录数组
    参数: $include_itself(boolean) 是否包含 $parent_id 自身的id,名字 
    参数: $index_flag(boolean) 是否显示分类首页
    返回值: 商品分类数组(array)
 ------------------------------------ */
function tep_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false,$index_flag = true) {
  global $languages_id;

  if (!is_array($category_tree_array)) $category_tree_array = array();
  if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') && $index_flag == true) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

  if ($include_itself) {
    $category_query = tep_db_query("select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . $languages_id . "' and cd.categories_id = '" . $parent_id . "' and cd.site_id='0'");
    $category = tep_db_fetch_array($category_query);
    $category_tree_array[] = array('id' => $parent_id, 'text' => $category['categories_name']);
  }

  $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . $languages_id . "' and c.parent_id = '" . $parent_id . "' and site_id ='0' order by c.sort_order, cd.categories_name");
  while ($categories = tep_db_fetch_array($categories_query)) {
    if ($exclude != $categories['categories_id']) {
      $category_tree_array[] = array('id' => $categories['categories_id'], 'text' => $spacing . $categories['categories_name']);
      $category_tree_array = tep_get_category_tree($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
    }
  }

  return $category_tree_array;
}

/* -------------------------------------
    功能: 根据商品分类id,获取其所关联的商品信息     
    参数: $cid(int) 商品分类id  
    返回值: 商品信息(array)
 ------------------------------------ */
function tep_get_products_tree($cid){
  $category_tree_array = array();
  $products_query = tep_db_query("select * from products_description pd,products_to_categories p2c where pd.products_id=p2c.products_id and pd.site_id=0 and p2c.categories_id='".$cid."' order by pd.products_name asc");
  while($p = tep_db_fetch_array($products_query)){
    $category_tree_array[] = array('id' => $p['products_id'], 'text' => $spacing . $spacing . $p['products_name']);
  }
  return $category_tree_array;
}
/* -------------------------------------
    功能: 根据条件生成商品的下拉框     
    参数: $name(string) 下拉框的名字  
    参数: $parameters(string) 下拉框的参数
    参数: $exclude(array) 排除掉的商品
    返回值: 商品的下拉框(string)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取商品属性的名字     
    参数: $options_id(int) 商品属性的id  
    返回值: 商品属性的名字(string)
 ------------------------------------ */
function tep_options_name($options_id) {
  global $languages_id;

  $options = tep_db_query("select products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id = '" . $options_id . "' and language_id = '" . $languages_id . "'");
  $options_values = tep_db_fetch_array($options);

  return $options_values['products_options_name'];
}

/* -------------------------------------
    功能: 获取商品属性值的名字     
    参数: $values_id(int) 商品属性值的id  
    返回值: 商品属性值的名字(string)
 ------------------------------------ */
function tep_values_name($values_id) {
  global $languages_id;

  $values = tep_db_query("select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id = '" . $values_id . "' and language_id = '" . $languages_id . "'");
  $values_values = tep_db_fetch_array($values);

  return $values_values['products_options_values_name'];
}

/* -------------------------------------
    功能: 根据条件生成图片的html
    参数: $image(string) 图片路径  
    参数: $alt(string) 图片说明
    参数: $width(string) 宽度
    参数: $height(string) 高度
    参数: $site_id(string) 所属网站id
    返回值: 图片的html(string)
 ------------------------------------ */
function tep_info_image($image, $alt, $width = '', $height = '', $site_id = '0') {
  if ( ($image) && (file_exists(tep_get_upload_dir($site_id). $image)) ) {
    $image = tep_image(tep_get_web_upload_dir($site_id). $image, $alt, $width, $height);
  } else {
    // TEXT_IMAGE_NONEXISTENT 数据表和程序中都未发现
    $image = TEXT_IMAGE_NONEXISTENT;
  }

  return $image;
}

/* -------------------------------------
    功能: 转换字符串 
    参数: $string(string) 需要转换的字符串  
    参数: $len(string) 字符串长度
    参数: $break_char(string) 间隔字符
    返回值: 转换后的字符串(string)
 ------------------------------------ */
function tep_break_string($string, $len, $break_char = '-') {
  //原有的 截取处理删除 暂时不做处理
  return $string;
}

/* -------------------------------------
    功能: 获取国家名字 
    参数: $country_id(int) 国家id 
    返回值: 国家名字(string)
 ------------------------------------ */
function tep_get_country_name($country_id) {
  $country_query = tep_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . $country_id . "'");

  if (!tep_db_num_rows($country_query)) {
    return $country_id;
  } else {
    $country = tep_db_fetch_array($country_query);
    return $country['countries_name'];
  }
}

/* -------------------------------------
    功能: 获取区域名字 
    参数: $zone_id(int) 区域id  
    返回值: 区域名字(string)
 ------------------------------------ */
function tep_get_zone_name($zone_id) {
  $zone_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_id = '" . $zone_id . "'");

  if (!tep_db_num_rows($zone_query)) {
    return $zone_id;
  } else {
    $zone = tep_db_fetch_array($zone_query);
    return $zone['zone_name'];
  }
}

/* -------------------------------------
    功  能 : 判断字符串或者数组是否为空 
    参  数 : $value(string/array) 字符串或数组  
    返回值 : 是否为空(boolean)
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
    功能: 获取浏览器信息 
    参数: $component(string) 给定的字符串 
    返回值: 浏览器信息(string)
 ------------------------------------ */
function tep_browser_detect($component) {
  global $HTTP_USER_AGENT;

  return stristr($HTTP_USER_AGENT, $component);
}

/* -------------------------------------
    功能: 生成税率类型的下拉框  
    参数: $parameters(string) 下拉框的参数 
    参数: $selected(int) 默认选中的税率类型id
    返回值: 税率类型的下拉框(string)
 ------------------------------------ */
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

/* -------------------------------------
    功  能 : 生成区域的下拉框  
    参  数 : $parameters(string) 下拉框的参数 
    参  数 : $selected(int) 默认选中的区域id
    返回值 : 区域的下拉框(string)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 判断页面底部的文件，是否存在给定的字符串  
    参数: 无 
    返回值: 是否存在指定字符串(boolean)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取相应的geo区域名字 
    参数: $geo_zone_id(int) 区域id
    返回值: 区域名字(string/int)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获得住址规格  
    参数: $address_format_id(int) 住址规格id
    参数: $address(array) 住址信息
    参数: $html(bool) 是否html显示
    参数: $boln(string) 开始符号
    参数: $eoln(string) 结束符号
    参数: $telephone(bool) 是否输出电话号码
    返回值: 住址规格(string)
 ------------------------------------ */
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
  if ($telephone) { $telephone = tep_output_string_protected($address['telephone']); }

  if ($html) {
    // HTML Mode
    $HR = '<hr>';
    $hr = '<hr>';
    if ( ($boln == '') && ($eoln == "\n") ) { 
      // Values not specified, use rational defaults
      $CR = '<br>';
      $cr = '<br>';
      $eoln = $cr;
    } else { 
      // Use values supplied
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

/* -------------------------------------
    功能: 获取区域编码  
    参数: $country(int) 国家id
    参数: $zone(int) 区域id 
    参数: $def_state(string) 默认的区域编码 
    返回值: 区域编码(string)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取商品的prid  
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
    功能: 获取商品的id  
    参数: $uprid(string) 商品你id的信息 
    返回值: 过滤后的商品id(string)
 ------------------------------------ */
function tep_get_prid($uprid) {
  $pieces = explode ('{', $uprid);

  return $pieces[0];
}

/* -------------------------------------
    功能: 获取当前语言的相关信息  
    参数: 无 
    返回值: 当前语言的相关信息(array)
 ------------------------------------ */
function tep_get_languages() {
  global $languages_id;
  if(isset($languages_id)&&$languages_id){
    $languages_query = tep_db_query("select languages_id, name, code, image,
        directory from " . TABLE_LANGUAGES . " where languages_id ='".$languages_id
        ."' order by sort_order");
  }else{
    $languages_query = tep_db_query("select languages_id, name, code, image, directory from " . TABLE_LANGUAGES . " order by sort_order");
  }
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

/* -------------------------------------
    功能: 获取分类的名字  
    参数: $category_id(int) 分类id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(boolean) 是否默认网站
    返回值: 分类的名字(string)
 ------------------------------------ */
function tep_get_category_name($category_id, $language_id, $site_id = 0, $default = false) {
  if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
    $site_id = 0;
  }
  $category_query = tep_db_query("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id='".$site_id."'");
  $category = tep_db_fetch_array($category_query);

  return $category['categories_name'];
}

/* -------------------------------------
    功能: 获取分类的罗马字  
    参数: $category_id(int) 分类id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(boolean) 是否默认网站
    返回值: 分类的罗马字(string)
 ------------------------------------ */
function tep_get_category_romaji($category_id, $language_id, $site_id = 0, $default = false) {
  if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
    $site_id = 0;
  }
  $category_query = tep_db_query("select romaji from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id='".$site_id."'");
  $category = tep_db_fetch_array($category_query);

  return $category['romaji'];
}

/* -------------------------------------
    功能: 获取分类的图片路径  
    参数: $category_id(int) 分类id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(boolean) 是否默认网站
    返回值: 分类的图片路径(string)
 ------------------------------------ */
function tep_get_category_image2($category_id, $language_id, $site_id = 0, $default = false) {
  if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
    $site_id = 0;
  }
  $category_query = tep_db_query("select categories_image2 from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
  $category = tep_db_fetch_array($category_query);

  return $category['categories_image2'];
}

/* -------------------------------------
    功能: 获取分类的meta信息  
    参数: $category_id(int) 分类id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(boolean) 是否默认网站
    返回值: 分类的meta信息(string)
 ------------------------------------ */
function tep_get_category_meta_text($category_id, $language_id, $site_id = 0, $default = false) {
  if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
    $site_id = 0;
  }
  $category_query = tep_db_query("select categories_meta_text from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
  $category = tep_db_fetch_array($category_query);

  return $category['categories_meta_text'];
}

/* -------------------------------------
    功能: 获取分类的seo名字  
    参数: $category_id(int) 分类id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(boolean) 是否默认网站
    返回值: 分类的seo名字(string)
 ------------------------------------ */
function tep_get_seo_name($category_id, $language_id, $site_id = 0, $default = false) { 
  if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
    $site_id = 0;
  }
  $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
  $category = tep_db_fetch_array($category_query);

  return $category['seo_name'];
}

/* -------------------------------------
    功能: 获取分类的seo描述 
    参数: $category_id(int) 分类id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(boolean) 是否默认网站
    返回值: 分类的seo描述(string)
 ------------------------------------ */
function tep_get_seo_description($category_id, $language_id, $site_id = 0, $default = false) { 
  if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
    $site_id = 0;
  }
  $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
  $category = tep_db_fetch_array($category_query);

  return $category['seo_description'];
}

/* -------------------------------------
    功能: 获取分类的头部信息  
    参数: $category_id(int) 分类id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(boolean) 是否默认网站
    返回值: 分类的头部信息(string)
 ------------------------------------ */
function tep_get_categories_header_text($category_id, $language_id, $site_id = 0, $default = false) {
  if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
    $site_id = 0;
  }
  $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
  $category = tep_db_fetch_array($category_query);

  return $category['categories_header_text'];
}

/* -------------------------------------
    功能: 获取分类的底部信息  
    参数: $category_id(int) 分类id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(boolean) 是否默认网站
    返回值: 分类的底部信息(string)
 ------------------------------------ */
function tep_get_categories_footer_text($category_id, $language_id, $site_id = 0, $default = false) {
  if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
    $site_id = 0;
  }
  $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
  $category = tep_db_fetch_array($category_query);

  return $category['categories_footer_text'];
}

/* -------------------------------------
    功能: 获取分类的文本信息  
    参数: $category_id(int) 分类id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(boolean) 是否默认网站
    返回值: 分类的文本信息(string)
 ------------------------------------ */
function tep_get_text_information($category_id, $language_id, $site_id = 0, $default = false) {
  if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
    $site_id = 0;
  }
  $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
  $category = tep_db_fetch_array($category_query);
  return $category['text_information'];
}

/* -------------------------------------
    功能: 获取分类meta的关键字  
    参数: $category_id(int) 分类id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(boolean) 是否默认网站
    返回值: 分类meta的关键字(string)
 ------------------------------------ */
function tep_get_meta_keywords($category_id, $language_id, $site_id = 0 , $default = false) {
  if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
    $site_id = 0;
  }
  $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id."'");
  $category = tep_db_fetch_array($category_query);

  return $category['meta_keywords'];
}

/* -------------------------------------
    功能: 获取分类meta的描述 
    参数: $category_id(int) 分类id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(boolean) 是否默认网站
    返回值: 分类meta的描述(string)
 ------------------------------------ */
function tep_get_meta_description($category_id, $language_id, $site_id = 0, $default = false) { 
  if ($default && $site_id != 0 && !tep_categories_description_exist($category_id, $language_id, $site_id)) {
    $site_id = 0;
  }
  $category_query = tep_db_query("select * from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $category_id . "' and language_id = '" . $language_id . "' and site_id = '".$site_id ."'");
  $category = tep_db_fetch_array($category_query);

  return $category['meta_description'];
}

/* -------------------------------------
    功能: 获取订单状态的名字  
    参数: $orders_status_id(int) 订单状态id 
    参数: $language_id(int) 语言id
    返回值: 订单状态的名字(string)
 ------------------------------------ */
function tep_get_orders_status_name($orders_status_id, $language_id = '') {
  global $languages_id;

  if (!$language_id) $language_id = $languages_id;
  $orders_status_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . $orders_status_id . "' and language_id = '" . $language_id . "'");
  $orders_status = tep_db_fetch_array($orders_status_query);

  return $orders_status['orders_status_name'];
}

/* -------------------------------------
    功能: 获取订单状态id 
    参数: $orders_id(string) 订单id 
    参数: $language_id(int) 语言id
    返回值: 订单状态id(int)
 ------------------------------------ */
function tep_get_orders_status_id($orders_id, $language_id = '') {
  global $languages_id;

  if (!$language_id) $language_id = $languages_id;
  $orders_query = tep_db_query("select * from ".TABLE_ORDERS." where orders_id='".$orders_id."'");
  $orders = tep_db_fetch_array($orders_query);
  return $orders['orders_status'];
}

/* -------------------------------------
    功能: 获取所有的订单状态信息  
    参数: 无 
    返回值: 订单状态信息(array)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取订单的第一个商品名字  
    参数: $orders_id(string) 订单id 
    返回值: 订单的第一个商品名字(string)
 ------------------------------------ */
function tep_get_orders_edit_title_from_oID($orders_id){
$oid = $orders_id;
$products_id_query = tep_db_query("select products_id from ".TABLE_ORDERS_PRODUCTS." where orders_id='".$oid."'");
$products_id_array = tep_db_fetch_array($products_id_query);
$products_name_query = tep_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$products_id_array['products_id']."'");
$products_name_array = tep_db_fetch_array($products_name_query);
return $products_name_array['products_name'];
}

/* -------------------------------------
    功能: 获取商品的手册标题 
    参数: $products_id(int) 商品id 
    返回值: 商品的手册标题(string)
 ------------------------------------ */
function tep_get_orders_manual_title_from_pID($products_id){
$pID=$products_id;
$categories_info_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pID."'");
$categories_info_array=tep_db_fetch_array($categories_info_query);
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_info_array['categories_id']."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$cp_manual_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid_array['parent_id']."' and site_id='0'");
$cp_manual_array=tep_db_fetch_array($cp_manual_query);

$c_manual_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_info_array['categories_id']."' and site_id='0'");
$c_manual_array=tep_db_fetch_array($c_manual_query);

$pro_manual_query=tep_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$pID."' and site_id='0'");
$pro_manual_array=tep_db_fetch_array($pro_manual_query);
$manual_title=$cp_manual_array['categories_name'].'/'.$c_manual_array['categories_name'].'/'.$pro_manual_array['products_name'].'のマニュアル';
return $manual_title;
}

/* -------------------------------------
    功能: 获取分类的手册标题 
    参数: $categories_id(int) 分类id 
    返回值: 分类的手册标题(string)
 ------------------------------------ */
function tep_get_orders_manual_title_from_cID($categories_id){
$cID=$categories_id;
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$cID."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$cp_manual_query=tep_db_query("select categories_id,categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid_array['parent_id']."' and site_id='0'");
$cp_manual_array=tep_db_fetch_array($cp_manual_query);
$check_categories_query = tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$cp_manual_array['categories_id']."'");
$check_categories_array = tep_db_fetch_array($check_categories_query);
if($check_categories_array['parent_id'] !=0 ){
$get_categories = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$check_categories_array['parent_id']."' and site_id='0'");
$get_categories_array = tep_db_fetch_array($get_categories);
$title_cp = $get_categories_array['categories_name'].'/';
}
$c_manual_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cID."' and site_id='0'");
$c_manual_array=tep_db_fetch_array($c_manual_query);

$manual_title=$title_cp.$cp_manual_array['categories_name'].'/'.$c_manual_array['categories_name'].'のマニュアル';
return $manual_title;
}

/* -------------------------------------
    功能: 分类的手册标题  
    参数: $cPath(int) 分类id 
    返回值: 分类的手册标题(string)
 ------------------------------------ */
function tep_get_orders_manual_title_from_cPath($cPath){
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$cPath."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$cp_manual_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid_array['parent_id']."' and site_id='0'");
$cp_manual_array=tep_db_fetch_array($cp_manual_query);

$c_manual_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cPath."' and site_id='0'");
$c_manual_array=tep_db_fetch_array($c_manual_query);
if($cp_manual_array['categories_name']!=""){
$title_part1 = $cp_manual_array['categories_name'].'/';
}
$title_part2 = $c_manual_array['categories_name'];
$manual_title=$title_part1.$title_part2.'のマニュアル';
return $manual_title;
}

/* -------------------------------------
    功能: 获取商品的名字  
    参数: $product_id(int) 商品id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(boolean) 是否默认网站
    返回值: 商品的名字(string)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取商品的描述 
    参数: $product_id(int) 商品id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    参数: $default(bool) 是否默认网站
    返回值: 商品的描述(string)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取商品的描述 
    参数: $product_id(int) 商品id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    返回值: 商品的描述(string)
 ------------------------------------ */
function tep_get_products_description_mobile($product_id, $language_id, $site_id = 0) {
  $product_query = tep_db_query("select products_description_mobile from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . $product_id . "' and language_id = '" . $language_id . "' and site_id ='".$site_id."'");
  $product = tep_db_fetch_array($product_query);

  return $product['products_description_mobile'];
}

/* -------------------------------------
    功能: 获取商品的url 
    参数: $product_id(int) 商品id 
    参数: $language_id(int) 语言id
    参数: $site_id(int) 网站id
    返回值: 商品的url(string)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取制造商的url 
    参数: $manufacturer_id(int) 制造商id 
    参数: $language_id(int) 语言id
    返回值: 制造商的url(string)
 ------------------------------------ */
function tep_get_manufacturer_url($manufacturer_id, $language_id) {
  $manufacturer_query = tep_db_query("select manufacturers_url from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . $manufacturer_id . "' and languages_id = '" . $language_id . "'");
  $manufacturer = tep_db_fetch_array($manufacturer_query);

  return $manufacturer['manufacturers_url'];
}

/* -------------------------------------
    功能: 判断类是否存在 
    参数: $class_name(string) 类名 
    返回值: 是否存在(boolean)
 ------------------------------------ */
function tep_class_exists($class_name) {
  if (function_exists('class_exists')) {
    return class_exists($class_name);
  } else {
    return true;
  }
}

/* -------------------------------------
    功能: 获取指定分类下的商品总和  
    参数: $categories_id(int) 分类id 
    参数: $include_deactivated(boolean) 是否包含不显示的商品
    返回值: 商品总和(int)
 ------------------------------------ */
function tep_products_in_category_count($categories_id, $include_deactivated = false) {
  $products_count = 0;

  if ($include_deactivated) {
    $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . $categories_id . "'");
  } else {
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

/* -------------------------------------
    功能: 获取指定分类下的子分类总和  
    参数: $categories_id(int) 分类id 
    返回值: 子分类总和(int)
 ------------------------------------ */
function tep_childs_in_category_count($categories_id) {
  $categories_count = 0;

  $categories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . $categories_id . "'");
  while ($categories = tep_db_fetch_array($categories_query)) {
    $categories_count++;
    $categories_count += tep_childs_in_category_count($categories['categories_id']);
  }

  return $categories_count;
}

/* -------------------------------------
    功能: 获取所有国家的相关信息  
    参数: $default(string) 默认国家的名称 
    返回值: 国家的相关信息(array)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取相应的区域信息  
    参数: $country_id(int) 国家id 
    返回值:区域信息(array)
 ------------------------------------ */
function tep_get_country_zones($country_id) {
  $zones_array = array();
  $zones_query = tep_db_query("select zone_id, zone_name from " . TABLE_ZONES . " where zone_country_id = '" . $country_id . "' order by " . ($country_id == 107 ? "zone_code" : "zone_name"));
  while ($zones = tep_db_fetch_array($zones_query)) {
    $zones_array[] = array('id' => $zones['zone_id'],
        'text' => $zones['zone_name']);
  }

  return $zones_array;
}

/* -------------------------------------
    功能: 生成指定国家的区域列表  
    参数: $country_id(int) 国家id 
    返回值: 区域列表(array)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 生成相应的国家列表  
    参数: $country_id(int) 国家id 
    返回值: 国家列表(string)
 ------------------------------------ */
function tep_cfg_pull_down_country_list($country_id,$empty_params ='',$params = '') {
  if($params != ''){
    return tep_draw_pull_down_menu('configuration_value', tep_get_countries(), $country_id,$params);
  }else{
    return tep_draw_pull_down_menu('configuration_value', tep_get_countries(), $country_id);
  }
}

/* -------------------------------------
    功能: 生成相应的区域列表  
    参数: $zone_id(int) 区域id 
    返回值: 区域列表(string)
 ------------------------------------ */
function tep_cfg_pull_down_zone_list($zone_id,$empty_params = '',$params = '') {
  if($params != ''){
    return tep_draw_pull_down_menu('configuration_value', tep_get_country_zones(STORE_COUNTRY), $zone_id,$params);
  }else{
    return tep_draw_pull_down_menu('configuration_value', tep_get_country_zones(STORE_COUNTRY), $zone_id);
  }
}

/* -------------------------------------
    功能: 生成税率的下拉列表  
    参数: $tax_class_id(int) 税率id 
    参数: $key(string) 下拉列表的名字
    返回值: 税率的下拉列表(string)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 生成指定的文本域  
    参数: $text(string) 默认的内容 
    返回值: 指定的文本域(string)
 ------------------------------------ */
function tep_cfg_textarea($text,$empty_params = '',$params = '') {
  if($params != ''){
    return tep_draw_textarea_field('configuration_value', false, 35, 5, $text, $params);
  }else{
    return tep_draw_textarea_field('configuration_value', false, 35, 5, $text);
  }
}

/* -------------------------------------
    功能: 设置banner的状态  
    参数: $banners_id(int) banner的id 
    参数: $status(int) banner状态
    参数: $site_id(int) 网站id 
    返回值: 设置的状态(resource/boolean/int)
 ------------------------------------ */
function tep_set_banner_status($banners_id, $status, $site_id) {
  if ($status == '1') {
    return tep_db_query("update " . TABLE_BANNERS . " set status = '1',
        expires_impressions = NULL, expires_date = NULL, date_status_change = NULL
        where banners_id = '" . $banners_id . "' and site_id ='".$site_id."'");
  } elseif ($status == '0') {
    return tep_db_query("update " . TABLE_BANNERS . " set status = '0', date_status_change = now() where banners_id = '" . $banners_id . "' and site_id ='".$site_id."'");
  } else {
    return -1;
  }
}

/* -------------------------------------
    功能: 设置最大可执行时间  
    参数: $limit(int) 执行时间(单位秒) 
    返回值: 无
 ------------------------------------ */
function tep_set_time_limit($limit) {
  if (!get_cfg_var('safe_mode')) {
    set_time_limit($limit);
  }
}

/* -------------------------------------
    功能: 根据给定的数据生成单选框  
    参数: $select_array(array) 单选框的数据数组 
    参数: $key_value(string) 默认选中的值
    参数: $key(string) 单选框的名字
    返回值: 生成的单选框(string)
 ------------------------------------ */
function tep_cfg_select_option($select_array, $key_value, $key = '',$parameter = '') {
  $string = '';
  for ($i = 0, $n = sizeof($select_array); $i < $n; $i++) {
    $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
   if($parameter != ''){
    $string .= '<br><input type="radio" '.$parameter.'name="' . $name . '" value="' . $select_array[$i] . '"';
   }else{
    $string .= '<br><input type="radio" name="' . $name . '" value="' . $select_array[$i] . '"';
   }
    if ($key_value == $select_array[$i]) $string .= ' CHECKED';
    $string .= '> ' . $select_array[$i];
  }

  return $string;
}

/* -------------------------------------
    功能: 根据给定的数据生成单选框  
    参数: $select_array(array) 单选框的数据数组 
    参数: $key_name(string) 单选框的名字
    参数: $key_value(string) 默认选中的值
    返回值: 生成的单选框(string)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取服务器等系统的参数  
    参数: 无 
    返回值: 服务器等系统的参数(array)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取上传文件的相应信息  
    参数: $filename(string) 上传文件的名字 
    返回值: 上传文件的相应信息(array)
 ------------------------------------ */
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

/* -------------------------------------
    功能: 把上传的临时文件移动到指定目录下  
    参数: $filename(array) 上传文件的资源 
    参数: $target(string) 指定目录的路径 
    返回值: 无
 ------------------------------------ */
function tep_copy_uploaded_file($filename, $target) {
  if (substr($target, -1) != '/') $target .= '/';

  $target .= $filename['name'];
  move_uploaded_file($filename['tmp_name'], $target);
  chmod($target, 0666);
}

/* -------------------------------------
    功能: 如果路径的最后一个字符是/,则过滤掉  
    参数: $path(string) 路径 
    返回值: 过滤后的路径(string) 
 ------------------------------------ */
function tep_get_local_path($path) {
  if (substr($path, -1) == '/') $path = substr($path, 0, -1);

  return $path;
}

/* -------------------------------------
    功能: 删除数组中的第一个元素  
    参数: $array(array) 指定的数组 
    返回值: 被删除元素的值(string) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 将原数组中的元素顺序翻转 
    参数: $array(array) 指定的数组 
    返回值: 顺序翻转后的数组(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取分类的相关信息 
    参数: $id(int) 商品id/分类id 
    参数: $from(string) 类型(分类或者商品)
    参数: $categories_array(array) 分类信息
    参数: $index(int) 索引 
    返回值: 分类信息(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取分类信息并按指定格式输出  
    参数: $id(int) 分类id 
    参数: $from(string) 类型
    返回值: 指定格式输出的信息(string) 
 ------------------------------------ */
function tep_output_generated_category_path($id, $from = 'category') {
  $calculated_category_path_string = '';
  $calculated_category_path = tep_generate_category_path($id, $from);
  for ($i = 0, $n = sizeof($calculated_category_path); $i < $n; $i++) {
    for ($j = 0, $k = sizeof($calculated_category_path[$i]); $j < $k; $j++) {
      $calculated_category_path_string .= $calculated_category_path[$i][$j]['text'];
      $calculated_category_path_string .= '&nbsp;&gt;&nbsp;';
    }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -16) . '<br>';
  }
  $calculated_category_path_string = substr($calculated_category_path_string, 0, -4);

  if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = TEXT_TOP;

  return $calculated_category_path_string;
}

/* -------------------------------------
    功能: 删除指定的分类及关联数据  
    参数: $category_id(int) 分类id 
    返回值: 无 
 ------------------------------------ */
function tep_remove_category($category_id) {
  $category_image_query = tep_db_query("select categories_image from " . TABLE_CATEGORIES . " where categories_id = '" . tep_db_input($category_id) . "'");
  $category_image = tep_db_fetch_array($category_image_query);

  $duplicate_image_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where categories_image = '" . tep_db_input($category_image['categories_image']) . "'");
  $duplicate_image = tep_db_fetch_array($duplicate_image_query);


  tep_db_query("delete from " . TABLE_CATEGORIES . " where categories_id = '" . tep_db_input($category_id) . "'");
  tep_db_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . tep_db_input($category_id) . "'");
  tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . tep_db_input($category_id) . "'");

  if (USE_CACHE == 'true') {
    tep_reset_cache_block('categories');
    tep_reset_cache_block('also_purchased');
  }
}

/* -------------------------------------
    功能: 删除指定的商品及关联数据  
    参数: $product_id(int) 商品id 
    返回值: 无
 ------------------------------------ */
function tep_remove_product($product_id) {
  $product_image_query = tep_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . tep_db_input($product_id) . "'");
  $product_image = tep_db_fetch_array($product_image_query);

  $duplicate_image_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " where products_image = '" . tep_db_input($product_image['products_image']) . "'");
  $duplicate_image = tep_db_fetch_array($duplicate_image_query);

  tep_db_query("delete from " . TABLE_PRODUCTS . " where products_id = '" . tep_db_input($product_id) . "'");
  tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . tep_db_input($product_id) . "'");
  tep_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . tep_db_input($product_id) . "'");
  tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where products_id like ('%" . tep_db_input($product_id) . "%')");
  tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_OPTIONS . " where products_id like ('%" . $product_id . "%')");

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

/* -------------------------------------
    功能: 删除指定的订单及关联数据  
    参数: $order_id(string) 订单id 
    参数: $restock(boolean) 是否恢复库存
    返回值: 无
 ------------------------------------ */
function tep_remove_order($order_id, $restock = false) {
  if ($restock == 'on') {
    $order_query = tep_db_query("select products_id, products_quantity from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($order_id) . "'");
    while ($order = tep_db_fetch_array($order_query)) {
      $radices = tep_get_radices($order['products_id']);
      tep_db_query("update " . TABLE_PRODUCTS . " set products_real_quantity = products_real_quantity + " . (int)($order['products_quantity']*$radices) .  (tep_orders_finished($order_id) == '1' && tep_orders_finishqa($order_id) == '1' ? ", products_ordered = products_ordered - " .  (int)($order['products_quantity']) : ''). " where products_id = '" . $order['products_id'] . "'");
    }
  }

  tep_db_query("delete from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS_ATTRIBUTES . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_ORDERS_TOTAL . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from " . TABLE_ORDERS_TO_BUTTONS . " where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from orders_products_download where orders_id = '" . tep_db_input($order_id) . "'");
  tep_db_query("delete from ".TABLE_OA_FORMVALUE." where orders_id = '".tep_db_input($order_id)."'");
  tep_db_query("delete from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".tep_db_input($order_id)."'");
}

/* -------------------------------------
    功能: 重新设置缓存模块  
    参数: $cache_block(array) 缓存数据数组 
    参数: $site_id(int) 网站id 
    返回值: 无 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取文件权限  
    参数: $mode(string) 二进制数 
    返回值: 文件权限的相关信息(string) 
 ------------------------------------ */
function tep_get_file_permissions($mode) {
  // determine type
  if ( ($mode & 0xC000) == 0xC000) { 
    // unix domain socket
    $type = 's';
  } elseif ( ($mode & 0x4000) == 0x4000) { 
    // directory
    $type = 'd';
  } elseif ( ($mode & 0xA000) == 0xA000) { 
    // symbolic link
    $type = 'l';
  } elseif ( ($mode & 0x8000) == 0x8000) { 
    // regular file
    $type = '-';
  } elseif ( ($mode & 0x6000) == 0x6000) { 
    //bBlock special file
    $type = 'b';
  } elseif ( ($mode & 0x2000) == 0x2000) { 
    // character special file
    $type = 'c';
  } elseif ( ($mode & 0x1000) == 0x1000) { 
    // named pipe
    $type = 'p';
  } else { 
    // unknown
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

/* -------------------------------------
    功能: 在数组中根据条件取出一段值 
    参数: $array(array) 指定的数组 
    参数: $offset(int) 偏移的位置 
    参数: $length(int) 截取的长度 
    返回值: 截取出的一段值(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 删除目录以及其下的文件 
    参数: $source(string) 目录路径 
    返回值: 无 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取常量的值 
    参数: $constant(string) 常量的名字 
    返回值: 常量的值 
 ------------------------------------ */
function tep_constant($constant) {
  if (function_exists('constant')) {
    $temp = constant($constant);
  } else {
    eval("\$temp=$constant;");
  }
  return $temp;
}

/* -------------------------------------
    功能: 税率的百分比 
    参数: $padding(string) 占位数  
    返回值: 税率的百分比(string) 
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
    功能: 发送邮件 
    参数: $to_name(string) 收件人名字 
    参数: $to_email_address(string) 收件人邮箱 
    参数: $email_subject(string) 邮件标题 
    参数: $email_text(string) 邮件内容 
    参数: $from_email_name(string) 发件人名字 
    参数: $from_email_address(string) 发件人邮箱 
    参数: $site_id(int) 网站id 
    返回值: 无 
 ------------------------------------ */
function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address, $site_id = 0) {
  if (SEND_EMAILS != 'true') return false;
  // Instantiate a new mail object
  $message = new email(array('X-Mailer: iimy Mailer'), $site_id);

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
    功能: 获取税率类标题 
    参数: $tax_class_id(int) 税率类id 
    返回值: 税率类标题(string) 
 ------------------------------------ */
function tep_get_tax_class_title($tax_class_id) {
  if ($tax_class_id == '0') {
    return TEXT_NONE;
  } else {
    $classes_query = tep_db_query("select tax_class_title from " . TABLE_TAX_CLASS . " where tax_class_id = '" . $tax_class_id . "'");
    $classes = tep_db_fetch_array($classes_query);

    return $classes['tax_class_title'];
  }
}

/* -------------------------------------
    功能: 获取图片类型 
    参数: 无 
    返回值: 图片类型(string/boolean) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取四舍五入的数值 
    参数: $value(int) 数值 
    参数: $precision(int) 小数点后的位数 
    返回值: 四舍五入的数值(float) 
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
    功能: 获得商品税率后的价格 
    参数: $price(float) 商品的价格 
    参数: $tax(float) 税率 
    返回值: 税后价格(float) 
 ------------------------------------ */
function tep_add_tax($price, $tax) {
  global $currencies;

  if (DISPLAY_PRICE_WITH_TAX == 'true') {
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

  return tep_round($price * $tax / 100, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
}

/* -------------------------------------
    功能: 获得税率 
    参数: $class_id(int) 税类id 
    参数: $country_id(int) 国家id 
    参数: $zone_id(int) 区域id 
    返回值: 税率值(float) 
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

/* -------------------------------------
    功能: 调用函数 
    参数: $function(string) 函数名 
    参数: $parameter(string) 函数的参数 
    参数: $object(string) 对象名 
    返回值: 被调用函数的值(mixed) 
 ------------------------------------ */
function tep_call_function($function, $parameter, $object = '') {
  if ($object == '') {
    return call_user_func($function, $parameter);
  } elseif (PHP_VERSION < 4) {
    return call_user_method($function, $object, $parameter);
  } else {
    return call_user_func(array($object, $function), $parameter);
  }
}

/* -------------------------------------
    功能: 获取geo区域类标题 
    参数: $zone_class_id(int) 区域类id 
    返回值: 区域类标题(string) 
 ------------------------------------ */
function tep_get_zone_class_title($zone_class_id) {
  if ($zone_class_id == '0') {
    return TEXT_NONE;
  } else {
    $classes_query = tep_db_query("select geo_zone_name from " . TABLE_GEO_ZONES . " where geo_zone_id = '" . $zone_class_id . "'");
    $classes = tep_db_fetch_array($classes_query);

    return $classes['geo_zone_name'];
  }
}

/* -------------------------------------
    功能: 获取geo区域类下拉列表 
    参数: $zone_class_id(int) 区域类id 
    参数: $key(string) 列表名 
    返回值: 下拉列表(string) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取订单状态的下拉列表 
    参数: $order_status_id(int) 订单状态id 
    参数: $key(string) 列表名 
    返回值: 订单状态的下拉列表(string) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取指定的订单状态的名字 
    参数: $order_status_id(int) 订单状态id 
    参数: $language_id(int) 语言id 
    返回值: 订单状态的名字(string) 
 ------------------------------------ */
function tep_get_order_status_name($order_status_id, $language_id = '') {
  global $languages_id;

  if ($order_status_id < 1) return TEXT_DEFAULT;

  if (!is_numeric($language_id)) $language_id = $languages_id;

  $status_query = tep_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . $order_status_id . "' and language_id = '" . $language_id . "'");
  $status = tep_db_fetch_array($status_query);

  return $status['orders_status_name'];
}

/* -------------------------------------
    功能: 获取在指定范围的随机数 
    参数: $min(int) 最小值 
    参数: $max(int) 最大值 
    返回值: 生成的随机数(string) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 把字符串全角字符变成半角字符 
    参数: $string(string) 字符串 
    返回值: 转换后的字符串(string) 
 ------------------------------------ */
function tep_an_zen_to_han($string) {
  return mb_convert_kana($string, "a");
}

/* -------------------------------------
    功能: 获得地址规格编号 
    参数: $country_id(int) 国家id 
    返回值: 地址规格编号(string) 
 ------------------------------------ */
function tep_get_address_format_id($country_id) {
  $address_format_query = tep_db_query("select address_format_id as format_id from " . TABLE_COUNTRIES . " where countries_id = '" . $country_id . "'");
  if (tep_db_num_rows($address_format_query)) {
    $address_format = tep_db_fetch_array($address_format_query);
    return $address_format['format_id'];
  } else {
    return '6';
  }
}

/* -------------------------------------
    功能: 获得全名 
    参数: $firstname(string) 名 
    参数: $lastname(string) 姓 
    返回值: 全名(string) 
 ------------------------------------ */
function tep_get_fullname($firstname, $lastname) {
  global $language;
  $separator = ' ';
  return ($language == 'japanese')
    ? ($lastname.$separator.$firstname)
    : ($firstname.$separator.$lastname);
}

/* -------------------------------------
    功能: 判断是否为闰年 
    参数: $year(string) 年份 
    返回值: 是否为闰年(boolean) 
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
    功能: 获取生产商的名字 
    参数: $id(int) 生产商的id  
    返回值: 生产商的名字(string) 
 ------------------------------------ */
function tep_get_manufacturers_name($id) {
  $mquery = tep_db_query("select manufacturers_name from manufacturers where manufacturers_id = '".$id."'");
  $mresult = tep_db_fetch_array($mquery);

  return $mresult['manufacturers_name'];
}

/* -------------------------------------
    功能: 获取订单状态的说明 
    参数: $orders_status_id(int) 订单状态id  
    参数: $language_id(int) 语言id  
    返回值: 订单状态的说明(string) 
 ------------------------------------ */
function tep_get_orders_status_comment($orders_status_id, $language_id = '') {
  global $languages_id;

  if (!$language_id) $language_id = $languages_id;
  $orders_status_query = tep_db_query("select comment from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . $orders_status_id . "' and language_id = '" . $language_id . "'");
  $orders_status = tep_db_fetch_array($orders_status_query);

  return $orders_status['comment'];
}

/* -------------------------------------
    功能: 删除属性 
    参数: $order_id(int) 订单id  
    参数: $restock(boolean) 是否恢复库存 
    返回值: 无 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 重置缓存seo的url 
    参数: $action(string) 动作 
    返回值: 是否成功(boolean) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 格式化输出配送时间 
    参数: $raw_datetime(string) 时间 
    返回值: 格式化后的时间(string) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获得选择时间过滤后的详细信息 
    参数: 无 
    返回值: 选择时间过滤后的详细信息(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取商品买取标识 
    参数: $product_id(int) 商品id 
    返回值: 商品买取标识(string) 
 ------------------------------------ */
function tep_get_bflag_by_product_id($product_id) {
  // 0 => sell   1 => buy
  $product_query = tep_db_query("select products_bflag from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
  $product = tep_db_fetch_array($product_query);

  return $product['products_bflag'];
}

/* -------------------------------------
    功能: 获取商品数量的乘积 
    参数: $cnt(int) 乘积值 
    参数: $pid(int) 商品id 
    参数: $prate(int) 乘积数 
    返回值: 商品数量的乘积(string) 
 ------------------------------------ */
function tep_get_full_count2($cnt, $pid, $prate = ''){
  if ($prate&&$prate!=''&&$prate!=0) {
    return '(' . number_format($prate * $cnt) . ')';
  }else{
    $radices = tep_get_radices($pid);
    if($radices!=1&&$radices!=0){
    return '(' . number_format($radices * $cnt) . ')';
    }else{
      return '';
    }
  }
}

/* -------------------------------------
    功能: 获取商品数量的乘积 
    参数: $cnt(int) 乘积值 
    参数: $pid(int) 商品id 
    返回值: 商品数量的乘积(string) 
 ------------------------------------ */
function tep_get_full_count_in_order2($cnt, $pid){
  $p = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." where products_id='".$pid."'"));
  return 
    number_format($p['products_attention_1_3'] * $cnt);
}

/* -------------------------------------
    功能: 获取文档类型的信息 
    参数: 无 
    返回值: 文档类型的信息(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取该目录下的目录以及文件列表 
    参数: $directory(string) 目录路径
    参数: $file(boolean) 是否列出文件 
    返回值: 该目录下的目录以及文件列表(array) 
 ------------------------------------ */
function get_directory_list ($directory, $file=true) {
  $d = dir ($directory);
  $list = array();
  while ($entry = $d->read() ) {
    if ($file == true) { 
      // We want a list of files, not directories
      $parts_array = explode ('.', $entry);
      $extension = $parts_array[1];
      // Don't add files or directories that we don't want
      if ($entry != '.' && $entry != '..' && $entry != '.htaccess' && $extension != 'php') {
        if (!is_dir ($directory . "/" . $entry) ) {
          $list[] = $entry;
        }
      }
    } else { 
      // We want the directories and not the files
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

/* -------------------------------------
    功能: 获取文档的url 
    参数: $document_id(int) 文档id 
    返回值: 文档的url(string/null) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取文档的图片 
    参数: $document_id(int) 文档id 
    返回值: 文档的图片(string/null) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取文档的类型信息 
    参数: 无 
    返回值: 文档的类型信息(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取该目录下的目录以及文件列表 
    参数: $directory(string) 目录路径
    参数: $file(boolean) 是否列出文件 
    返回值: 该目录下的目录以及文件列表(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取指定文档路径 
    参数: $document_id(int) 文档id 
    返回值: 指定文档路径(string/null) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取新文件路径 
    参数: $document_id(int) 文档id 
    参数: $type_id(int) 类型id 
    返回值: 新文件路径(string) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取图片文档的url 
    参数: $document_id(int) 文档id 
    返回值: 图片文档的url(string/null) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取图片文档的图片 
    参数: $document_id(int) 文档id 
    返回值: 图片文档的图片(string/null) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取该订单的商品的人物名描述 
    参数: $orders_id(string) 订单id 
    参数: $allorders(array) 订单数组 
    参数: $site_id(int) 网站id 
    返回值: 该订单的商品的人物名描述(string) 
 ------------------------------------ */
function orders_a($orders_id, $allorders = null, $site_id = 0)
{
  static $products;
  $str = "";
  if ($allorders && $products === null) {
    foreach($allorders as $o) {
      $allorders_ids[] = $o['orders_id'];
    }
    $sql = "select op.orders_id,pd.products_name,p.products_attention_5,p.products_id from ".TABLE_ORDERS_PRODUCTS." op, ".TABLE_PRODUCTS_DESCRIPTION." pd,".TABLE_PRODUCTS." p WHERE op.products_id=pd.products_id and p.products_id=pd.products_id and `orders_id` IN ('".join("','", $allorders_ids)."') and pd.site_id = '".$site_id."'";
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

/* -------------------------------------
    功能: 获取网站的详细信息 
    参数: 无 
    返回值: 网站的详细信息(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取网站的id值 
    参数: 无 
    返回值: 网站的id值(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取网站的列表 
    参数: $filename(string) 当前坐在的页面 
    参数: $ca_single(boolean) 是否过滤指定的url参数 
    返回值: 网站的列表(string) 
 ------------------------------------ */
function tep_site_filter($filename, $ca_single = false){
  global $_GET, $_POST, $ocertify;
  $orders_site_array = array();
  $orders_site_query = tep_db_query("select id from ". TABLE_SITES);
  while($orders_site_rows = tep_db_fetch_array($orders_site_query)){
    $orders_site_array[] = $orders_site_rows['id'];
  }
  tep_db_free_result($orders_site_query);
  $user_info = tep_get_user_info($ocertify->auth_user);
  if($filename == FILENAME_ORDERS){  
    if(PERSONAL_SETTING_ORDERS_SITE != ''){
      $site_setting_array = unserialize(PERSONAL_SETTING_ORDERS_SITE);
      if(array_key_exists($user_info['name'],$site_setting_array)){

        $site_setting_str = $site_setting_array[$user_info['name']];
      }else{
        $site_setting_str = implode('|',$orders_site_array); 
      }
    }else{
      $site_setting_str = implode('|',$orders_site_array); 
    }
    $site_array = array();
    $site_array = explode('|',$site_setting_str);
  }
  if($filename == FILENAME_PREORDERS){ 
    if(PERSONAL_SETTING_PREORDERS_SITE != ''){
      $site_setting_array = unserialize(PERSONAL_SETTING_PREORDERS_SITE);
      if(array_key_exists($user_info['name'],$site_setting_array)){

        $site_setting_str = $site_setting_array[$user_info['name']];
      }else{
        $site_setting_str = implode('|',$orders_site_array); 
      }
    }else{
      $site_setting_str = implode('|',$orders_site_array); 
    }
    $site_array = array();
    $site_array = explode('|',$site_setting_str);
  }
  ?>
    <div id="tep_site_filter">
<?php
  if($filename != FILENAME_ORDERS && $filename != FILENAME_PREORDERS){
    if (!isset($_GET['site_id']) || !$_GET['site_id']) {?>
      <span class="site_filter_selected"><a href="<?php echo tep_href_link($filename);
      ?>">all</a></span>
        <?php } else { ?>
          <span><a href="<?php 
            if ($ca_single) {
              echo tep_href_link($filename, tep_get_all_get_params(array('site_id')));
            } else {
              echo tep_href_link($filename, tep_get_all_get_params(array('site_id', 'page', 'oID', 'rID', 'cID', 'latest_news_id', 'bID', 'campaign_id')));
            }
          ?>">all</a></span> 
            <?php } }?>
            <?php 
            if($filename != FILENAME_ORDERS && $filename != FILENAME_PREORDERS){
              foreach (tep_get_sites() as $site) {?>
              <?php if (isset($_GET['site_id']) && $_GET['site_id'] == $site['id']) {?>
                <span class="site_filter_selected"><?php echo $site['romaji'];?></span>
                  <?php } else {?>
                    <span><a href="<?php 
                      if ($ca_single) {
                        echo tep_href_link($filename, tep_get_all_get_params(array('site_id')) . 'site_id=' . $site['id']);
                      } else {
                        echo tep_href_link($filename, tep_get_all_get_params(array('site_id', 'page', 'oID', 'rID', 'cID', 'pID', 'latest_news_id', 'bID', 'campaign_id')) . 'site_id=' . $site['id']);
                      }
                    ?>"><?php echo $site['romaji'];?></a></span>
                      <?php }
              }
            }else{
              foreach (tep_get_sites() as $site) {
               if(!isset($_GET['site_id'])){
                if(in_array($site['id'],$site_array)){
           ?>  
                <span id="site_<?php echo $site['id'];?>" class="site_filter_selected"><a href="javascript:void(0);" onclick="change_site(<?php echo $site['id'];?>,0,'<?php echo $_GET['site_id'];?>','<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'site_id')));?>');"><?php echo $site['romaji'];?></a></span>
          <?php
               }else{
          ?>
              <span id="site_<?php echo $site['id'];?>"><a href="javascript:void(0);" onclick="change_site(<?php echo $site['id'];?>,1,'<?php echo $_GET['site_id'];?>','<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'site_id')));?>');"><?php echo $site['romaji'];?></a></span>  
          <?php
               }
               }else{
                 $site_id_array = explode('-',$_GET['site_id']); 
                 if(in_array($site['id'],$site_id_array)){
          ?>
              <span id="site_<?php echo $site['id'];?>" class="site_filter_selected"><a href="javascript:void(0);" onclick="change_site(<?php echo $site['id'];?>,0,'<?php echo $_GET['site_id'];?>','<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'site_id')));?>');"><?php echo $site['romaji'];?></a></span>
          <?php
               }else{
          ?>
              <span id="site_<?php echo $site['id'];?>"><a href="javascript:void(0);" onclick="change_site(<?php echo $site['id'];?>,1,'<?php echo $_GET['site_id'];?>','<?php echo urlencode(tep_get_all_get_params(array('page', 'oID', 'action', 'site_id')));?>');"><?php echo $site['romaji'];?></a></span>
<?php
               }
               }
              }
            }
          ?>
            </div>
            <?php
}

/* -------------------------------------
    功能: 获取网站的下拉列表 
    参数: $default(string) 默认值 
    参数: $require(boolean) 是否添加必须注释 
    返回值: 网站的下拉列表(string) 
 ------------------------------------ */
function tep_siteurl_pull_down_menu($default = '',$require = false){
  $sites_array = array(array('id' => '', 'text' => 'サイトへ移動'));
  $sites = tep_get_sites();
  foreach($sites as $site){
    $sites_array[] = array('id' => $site['url'], 'text' => $site['name']);
  }
  return tep_draw_pull_down_menu('site_url_id', $sites_array, $default, $params = 'onChange="window.open(this.value);this.selectedIndex=0;"', $require);

}

/* -------------------------------------
    功能: 获取网站的下拉列表 
    参数: $default(string) 默认值 
    参数: $require(boolean) 是否添加必须注释 
    参数: $all(boolean) 是否有默认选项 
    返回值: 网站的下拉列表(string) 
 ------------------------------------ */
function tep_site_pull_down_menu($default = '',$require = true,$all = false,$params = ''){
  $sites_array = array();
  $sites = tep_get_sites();
  if ($all) {
    $sites_array[] = array('id' => '0', 'text' => '全部サイト');
  }
  foreach($sites as $site){
    $sites_array[] = array('id' => $site['id'], 'text' => $site['name']);
  }
  if($params == ''){
  return tep_draw_pull_down_menu('site_id', $sites_array, $default, $params = '', $require);
  }else{
  return tep_draw_pull_down_menu('site_id', $sites_array, $default, $params, $require);
  }
}

/* -------------------------------------
    功能: 获取网站的下拉列表 
    参数: $default(string) 默认值 
    参数: $require(boolean) 是否添加必须注释 
    参数: $text(string) 默认选项的值 
    返回值: 网站的下拉列表(string) 
 ------------------------------------ */
function tep_site_pull_down_menu_with_all($default = '',$require = true,$text = 'all',$params=''){
  $sites_array = array();
  $sites = tep_get_sites();
  $sites_array[] = array('id' => '', 'text' => $text );
  foreach($sites as $site){
    $sites_array[] = array('id' => $site['id'], 'text' => $site['name']);
  }
  return tep_draw_pull_down_menu('site_id', $sites_array, $default, $params, $require);
}

function tep_site_pull_down_menu_with_none($default = '',$require = true){
}

/* -------------------------------------
    功能: 获取网站的罗马字 
    参数: $id(int) 网站id 
    返回值: 网站的罗马字(string) 
 ------------------------------------ */
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
}

/* -------------------------------------
    功能: 获取网站的名字 
    参数: $id(int) 网站id 
    返回值: 网站的名字(string) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取该订单所属网站的罗马字 
    参数: $id(int) 订单id 
    返回值: 该订单所属网站的罗马字(string) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取该订单所属网站的名字 
    参数: $id(int) 订单id 
    返回值: 该订单所属网站的名字(string) 
 ------------------------------------ */
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


/* -------------------------------------
    功能: 获取属性名字 
    参数: $id(int) 属性id 
    参数: $languages(int) 语言id 
    返回值: 属性名字(string) 
 ------------------------------------ */
function tep_get_add_options_name($id, $languages='4') {
  $query = tep_db_query("select products_options_name from products_options where products_options_id = '".$id."' and language_id = '".$languages."'");
  if(tep_db_num_rows($query)) {
    $result = tep_db_fetch_array($query);
    return $result['products_options_name'];
  } else {
    return '';
  }
}

/* -------------------------------------
    功能: 获取属性值 
    参数: $id(int) 属性id 
    参数: $languages(int) 语言id 
    返回值: 属性值(string) 
 ------------------------------------ */
function tep_get_add_options_value($id, $languages='4') {
  $query = tep_db_query("select products_options_values_name from products_options_values where products_options_values_id = '".$id."' and language_id = '".$languages."'");
  if(tep_db_num_rows($query)) {
    $result = tep_db_fetch_array($query);
    return $result['products_options_values_name'];
  } else {
    return '';
  }
}

/* -------------------------------------
    功能: 判断分类是否存在描述 
    参数: $cid(int) 分类id 
    参数: $lid(int) 语言id 
    参数: $sid(int) 网站id 
    返回值: 是否存在描述(boolean) 
 ------------------------------------ */
function tep_categories_description_exist($cid, $lid, $sid){
  $query = tep_db_query("select * from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cid."' and site_id = '".$sid."' and language_id='".$lid."'");
  if(tep_db_num_rows($query)) {
    return true;
  } else {
    return false;
  }
}

/* -------------------------------------
    功能: 判断该商品是否存在描述 
    参数: $pid(int) 商品id 
    参数: $sid(int) 网站id 
    参数: $lid(int) 语言id 
    返回值: 是否存在描述(boolean) 
 ------------------------------------ */
function tep_products_description_exist($pid, $sid, $lid){
  $query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$pid."' and site_id = '".$sid."' and language_id='".$lid."'");
  if(tep_db_num_rows($query)) {
    return true;
  } else {
    return false;
  }
}

/* -------------------------------------
    功能: 判断该模块是否安装 
    参数: $class(string) 类的名字 
    参数: $site_id(int) 网站id 
    返回值: 是否安装(boolean) 
 ------------------------------------ */
function tep_module_installed($class, $site_id = 0){
  $module = new $class($site_id);
  return $module->check();
}

/* -------------------------------------
    功能: 获取指定网站的图片目录 
    参数: $site_id(int) 网站id 
    返回值: 图片目录(string) 
 ------------------------------------ */
function tep_get_upload_dir($site_id = '0'){
  if (!trim($site_id)) $site_id = '0';
  return DIR_FS_CATALOG . 'upload_images/' . $site_id . '/';
}

/* -------------------------------------
    功能: 获取指定网站的图片目录的相对路径 
    参数: $site_id(int) 网站id 
    返回值: 图片目录路径(string) 
 ------------------------------------ */
function tep_get_web_upload_dir($site_id = '0'){
  if (!trim($site_id)) $site_id = '0';
  return 'upload_images/' . $site_id . '/';
}

/* -------------------------------------
    功能: 获取图片目录路径 
    参数: 无 
    返回值: 图片目录路径(string) 
 ------------------------------------ */
function tep_get_upload_root(){
  return DIR_FS_CATALOG . 'upload_images/';
}

/* -------------------------------------
    功能: 获取指定banner数据的结果集 
    参数: $bid(int) banner id 
    返回值: 结果集(array) 
 ------------------------------------ */
function tep_get_banner($bid){
  $banner_query = tep_db_query("select * from " .TABLE_BANNERS. " where banners_id = '".$bid."'");
  return tep_db_fetch_array($banner_query);
}

/* -------------------------------------
    功能: 获取指定新闻数据的结果集 
    参数: $latest_news_id(int) 新闻id 
    返回值: 结果集(array) 
 ------------------------------------ */
function tep_get_latest_news_by_id($latest_news_id){
  return tep_db_fetch_array(tep_db_query("select * from " . TABLE_NEWS . " where news_id = '".$latest_news_id."'"));
}

/* -------------------------------------
    功能: 获取指定礼物数据的结果集 
    参数: $id(int) 礼物id 
    返回值: 结果集(array) 
 ------------------------------------ */
function tep_get_present_by_id($id){
  return tep_db_fetch_array(tep_db_query("select * from " . TABLE_PRESENT_GOODS . " where goods_id = '".$id."'"));
}

/* -------------------------------------
    功能: 获取商品cflag标识 
    参数: $product_id(int) 商品id 
    返回值: cflag标识(string) 
 ------------------------------------ */
function tep_get_cflag_by_product_id($product_id) {
  // 0 => no   1=> yes
  $product_query = tep_db_query("select products_cflag from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
  $product = tep_db_fetch_array($product_query);

  return $product['products_cflag'];
}

/* -------------------------------------
    功能: 获取默认的设置id 
    参数: $cid(int) 设置id 
    返回值: 默认的设置id(int) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取指定商品的相关信息 
    参数: $pid(int) 商品id 
    参数: $site_id(int) 网站id 
    参数: $lid(int) 语言id 
    参数: $default(boolean) 是否获取默认值 
    返回值: 指定商品的相关信息(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取faq分类的组id信息 
    参数: 无 
    返回值: faq分类的组id信息(string) 
 ------------------------------------ */
function tep_get_faq_game_id_string(){
  $g_ids = array();
  $query = tep_db_query("select g_id from " . TABLE_FAQ_CATEGORIES . " group by g_id");
  while ($c = tep_db_fetch_array($query)) {
    $g_ids[] = $c['g_id'];
  }
  return implode(',', $g_ids);
}

/* -------------------------------------
    功能: 获取指定faq问题的信息 
    参数: $q_id(int) faq问题id  
    返回值: 指定faq问题的信息(array) 
 ------------------------------------ */
function tep_get_faq_question($q_id){
  return tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_QUESTIONS." where q_id = '".$q_id."'"));
}

/* -------------------------------------
    功能: 获取指定faq分类的信息 
    参数: $c_id(int) faq分类id  
    返回值: 指定faq分类的信息(array) 
 ------------------------------------ */
function tep_get_faq_category($c_id){
  return tep_db_fetch_array(tep_db_query("select * from ".TABLE_FAQ_CATEGORIES." where c_id = '".$c_id."'"));
}
/* -------------------------------------
    功能: 更新分类状态 
    参数: $categories_id(int) 分类id  
    参数: $status(int) 状态id  
    返回值: 更新成功(boolean) 
 ------------------------------------ */
function tep_set_categories_status($categories_id, $status)
{
  tep_db_query("UPDATE `".TABLE_CATEGORIES."` SET `categories_status` = '".intval($status)."' WHERE `categories_id` =".$categories_id." LIMIT 1 ;");
  return true;
}

/* -------------------------------------
    功能: 获取分类状态 
    参数: $categories_id(int) 分类id  
    返回值: 分类状态(int/null) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 订单的总价的输出 
    参数: $orders_id(int) 订单id  
    参数: $single(boolean) 是否格式化输出 
    返回值: 订单的总价(string) 
 ------------------------------------ */
function tep_get_ot_total_by_orders_id($orders_id, $single = false) {
  if ($single) {
    global $currencies; 
  }
  $query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where class='ot_total' and orders_id='".$orders_id."'");
  $result = tep_db_fetch_array($query);
  if($result['value'] > 0){
    if ($single) {
      return "<b>".$currencies->format(abs($result['value']))."</b>";
    } else {
      return "<b>".abs($result['value'])."".TEXT_MONEY_SYMBOL."</b>";
    }
  }else{
    if ($single) {
      return "<b><font color='ff0000'>".$currencies->format(abs($result['value']))."</font></b>";
    } else {
      return "<b><font color='ff0000'>".abs($result['value'])."".TEXT_MONEY_SYMBOL."</font></b>";
    }
  }
}

/* -------------------------------------
    功能: 过滤订单的总价 
    参数: $text(string) 总价的文字 
    返回值: 过滤后的订单的总价(string) 
 ------------------------------------ */
function tep_get_ot_total_num_by_text($text) {
  return str_replace(array("," , "<b>" , "</b>" , "".TEXT_MONEY_SYMBOL."") , array("" , "" , "" , "") , $text);
}

/* -------------------------------------
    功能: 获取折扣信息 
    参数: $small_sum(string) 折扣字符串 
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
    功能: 获取该商品的特价 
    参数: $product_id(int) 商品id 
    返回值: 商品的特价(float/boolean) 
 ------------------------------------ */
function tep_get_products_special_price($product_id) {
  $product_query = tep_db_query("select * from " . TABLE_PRODUCTS . " where products_id = '" . (int)$product_id . "'");
  $product = tep_db_fetch_array($product_query);

  return tep_get_special_price($product['products_price'], $product['products_price_offset'], $product['products_small_sum']);
}

/* -------------------------------------
    功能: 计算该商品的特价 
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
    功能: 计算该商品的价格 
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
    功能: 获取该商品的最终价格 
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
  } else if ($price && $offset && $offset != 0) {
    return $price;
  } else {
    return $price;
  }
}

/* -------------------------------------
    功能: 获取该商品的价格信息 
    参数: $products_id(int) 商品id 
    参数: $product_info(array) 商品信息 
    返回值: 商品的价格信息(array) 
 ------------------------------------ */
function tep_get_products_price ($products_id, $product_info = '') {
  if (!empty($product_info)) {
    $product = $product_info;
  } else {
    $product_query = tep_db_query("select * from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
    $product = tep_db_fetch_array($product_query);
  }
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
    功能: 把全角数字变成半角 
    参数: $str(string) 字符串 
    返回值: 处理后的字符串(string) 
 ------------------------------------ */
function SBC2DBC($str) {
  $arr = array(
      '１','２','３','４','５','６','７','８','９','０','＋','－','％'
      );
  $arr2 = array(
      '1','2','3','4','5','6','7','8','9','0','+','-','%'
      );
  return str_replace($arr, $arr2, $str);
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
    功能: 获取该订单的所属网站id 
    参数: $orders_id(int) 订单id 
    返回值: 网站id(int/boolean) 
 ------------------------------------ */
function tep_get_site_id_by_orders_id($orders_id) {
  $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$orders_id."'"));
  if ($order) {
    return $order['site_id'];
  } else {
    return false;
  }
}

/* -------------------------------------
    功能: 获取指定网站url 
    参数: $site_id(int) 网站id 
    返回值: 网站url(string/boolean) 
 ------------------------------------ */
function get_url_by_site_id($site_id) {
  $site = tep_db_fetch_array(tep_db_query("select * from ".TABLE_SITES." where id='".$site_id."'"));
  if ($site) {
    return $site['url'];
  } else {
    return false;
  }
}

/* -------------------------------------
    功能: 更新订单状态的一些信息 
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
    功能: 更新订单的一些信息 
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
    功能: 如果订单finished则取消orders_wait_flag 
    参数: $orders_id(int) 订单id 
    返回值: 无 
 ------------------------------------ */
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
}

/* -------------------------------------
    功能: 判断该分类下是否有子分类 
    参数: $cid(int) 分类id 
    返回值: 是否有子分类(boolean) 
 ------------------------------------ */
function countSubcategories($cid)
{ 
  $res = tep_db_query("select count(c.categories_id) cnt from categories_description cd,categories c where cd.site_id =0 and  c.categories_id = cd.categories_id and c.parent_id =".$cid);
  $col = tep_db_fetch_array($res);
  return $col['cnt']>0;

}

/* -------------------------------------
    功能: 获取一级分类的信息 
    参数: 无 
    返回值: 一级分类的信息(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取指定分类下的子分类的信息 
    参数: $cid(int) 分类id 
    返回值: 子分类的信息(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 生成多选框的html 
    参数: $arrCategories(array) 分类数组 
    参数: $selectValue(string/boolean) 选择值 
    参数: $startName(string) 名 
    返回值: 生成多选框的html(string) 
 ------------------------------------ */
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
              "'.$cate1['cid'].'">'.$cate1['cname']. '</li>';
            $flag=false;
          }
        }
        if($flag) {
          $result .= '<li class="change_one_list_main"><input type = "checkbox" name="ocid[]" value =
            "'.$cate1['cid'].'">'.$cate1['cname']. '</li>';
        }
      }else{
        $result .= '<li class="change_one_list_main"><input type = "checkbox" name="ocid[]" value =
          "'.$cate1['cid'].'">'.$cate1['cname']. '</li>';

      }
    }
  }
  $result .= '</ul>';
  return $result;
}

/* -------------------------------------
    功能: 根据分类路径获取部分分类 
    参数: $cpath(array) 分类路径 
    参数: $which(int) 取第一个分类或者最后一个分类 
    返回值: 部分分类(string) 
 ------------------------------------ */
function cpathPart($cpath,$which=1) {
  $a = $cpath;
  if (strpos($a ,'_')){
    if($which ==1){
      $b = substr($a,0,strpos($a,'_'));
    }else {
      $arr = explode('_',$a);
      return $arr[count($arr)-1];
    }
    return $b;
  }
  return $a;
}

/* -------------------------------------
    功能: 生成选项的html 
    参数: $arrCategories(array) 分类数组 
    参数: $selectValue(string/boolean) 选择值 
    参数: $startName(string) 名 
    返回值: 生成选项的html(string) 
 ------------------------------------ */
function makeSelectOption($arrCategories,$selectValue = Fales,$startName='')
{
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

/* -------------------------------------
    功能: 进行json编码 
    参数: $a(mixed) 输入的信息 
    返回值: 转换后的信息(string) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 把指定商品关联到指定同业者 
    参数: $product_id(int) 商品id 
    参数: $dougyousya_id(int) 同业者id 
    返回值: 关联是否成功(boolean) 
 ------------------------------------ */
function update_products_dougyousya($product_id, $dougyousya_id) {
  if (tep_db_num_rows(tep_db_query("select * from set_products_dougyousya where product_id = '".$product_id."'"))) {
    return tep_db_perform("set_products_dougyousya", array('dougyousya_id' => tep_db_prepare_input($dougyousya_id)), 'update', 'product_id = \'' . tep_db_prepare_input($product_id) . '\'');
  } else {
    return tep_db_perform("set_products_dougyousya", array('product_id' => $product_id, 'dougyousya_id' => tep_db_prepare_input($dougyousya_id)));
  }
}

/* -------------------------------------
    功能: 获取同业者id 
    参数: $products_id(int) 商品id 
    返回值: 同业者id(int) 
 ------------------------------------ */
function get_products_dougyousya($products_id) {
  $data = tep_db_fetch_array(tep_db_query("select * from set_products_dougyousya where product_id='".$products_id."'"));
  if($data) {
    return $data['dougyousya_id'];
  } else {
    return 0;
  }
}

/* -------------------------------------
    功能: 获取该分类的同业者信息 
    参数: $categories_id(int) 分类id 
    参数: $products_id(int) 商品id 
    返回值: 同业者信息(array) 
 ------------------------------------ */
function get_all_products_dougyousya($categories_id,$products_id) {
  $arr = array();
  $query = tep_db_query("select sdc.dougyousya_id 
      from set_dougyousya_categories sdc,set_dougyousya_names sdn 
      where categories_id = '".$categories_id."'
      and sdn.dougyousya_id=sdc.dougyousya_id 
      order by sdn.sort_order,sdn.dougyousya_id asc");
  while($data = tep_db_fetch_array($query))
  {
    $arr[] = $data;
  }
  return $arr;
}

/* -------------------------------------
    功能: 获取同业者历史信息 
    参数: $products_id(int) 商品id 
    参数: $dougyousya_id(int) 同业者id 
    返回值: 同业者历史信息(array) 
 ------------------------------------ */
function get_dougyousya_history($products_id, $dougyousya_id) {
  $data = tep_db_fetch_array(tep_db_query("select * from set_dougyousya_history where products_id='".$products_id."' and dougyousya_id='".$dougyousya_id."' order by last_date desc"));
  if($data) {
    return $data['dougyosya_kakaku'];
  } else {
    return 0;
  }
}

/* -------------------------------------
    功能: 获取该分类关联的商品的信息 
    参数: $categories_id(int) 分类id 
    参数: $status(string) 状态条件 
    返回值: 商品的信息(array) 
 ------------------------------------ */
function tep_get_products_by_categories_id($categories_id,$status=null) {
  $arr = array();
  $query = tep_db_query("select distinct p.*,pd.* from products p, products_description pd, products_to_categories p2c where p.products_id=pd.products_id and p2c.products_id=p.products_id and categories_id='".$categories_id."' and pd.site_id='0' ".($status===null?'':" and pd.products_status='1'")." order by pd.products_name");
  while ($product = tep_db_fetch_array($query)) {
    $arr[] = $product;
  }
  return $arr;
}

/* -------------------------------------
    功能: 获取该商品的虚拟库存 
    参数: $products_id(int) 商品id 
    返回值: 虚拟库存(int) 
 ------------------------------------ */
function tep_get_kakuukosuu_by_products_id($products_id) {
  static $data;
  if (!isset($data[$products_id])){
    $data[$products_id] = tep_db_fetch_array(tep_db_query("select * from products where products_id = '".$products_id."'"));
  }
  if ($data) {
    return (int)$data[$products_id]['products_virtual_quantity'];
  } else {
    return 0;
  }
}

/* -------------------------------------
    功能: 根据分类id和商品id获取价格设定信息 
    参数: $categories_id(int) 分类id 
    参数: $products_id(int) 商品id 
    返回值: 价格设定信息(int) 
 ------------------------------------ */
function tep_get_kakaku_by_products_id($categories_id, $products_id){
  $data = tep_db_fetch_array(tep_db_query("select * from set_menu_list where categories_id='".$categories_id."' and products_id='".$products_id."'"));
  if ($data) {
    return (float)$data['kakaku'];
  } else {
    return 0;
  }
}

/* -------------------------------------
    功能: 获取分类树 
    参数: $parent_id(int) 父结点 
    参数: $spacing(string) 间隔符 
    参数: $exclude(string) 不包括的分类 
    参数: $category_tree_array(array) 分类数组 
    参数: $include_itself(boolean) 是否包含自己 
    返回值: 分类树(aray) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取该分类的父分类,并用_连接自己 
    参数: $cid(int) 分类id 
    返回值: 分类路径(string) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 分割换行字符串 
    参数: $orodata(string) 字符串 
    返回值: 处理后的字符信息(array) 
 ------------------------------------ */
function spliteOroData($orodata){
  $new_lines = array();
  $cr = array("\r\n", "\r");   // 用于换行代码替换
  $data = trim($orodata);
  $data = str_replace($cr, "\n",$data);  //统一换行代码
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

/* -------------------------------------
    功能: 获取顾客的传真 
    参数: $cid(int) 顾客id 
    返回值: 顾客的传真(string) 
 ------------------------------------ */
function tep_get_customers_fax_by_id($cid)
{
  $query = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id='".$cid."'");
  $customers = tep_db_fetch_array($query);
  return $customers['customers_fax'];
}

/* -------------------------------------
    功能: 获取该订单的商品名 
    参数: $orders_id(int) 订单id 
    返回值: 该订单的商品名(string) 
 ------------------------------------ */
function tep_get_orders_products_names($orders_id) {
  $str = '';
  $orders_products_query = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".$orders_id."'");
  while ($p = tep_db_fetch_array($orders_products_query)) {
    $str .= $p['products_name'].' ';
  }
  return $str;
}

/* -------------------------------------
    功能: 获取该订单的手册标题 
    参数: $orders_id(int) 订单id 
    参数: $products_id(int) 商品id 
    返回值: 订单的手册标题(string) 
 ------------------------------------ */
function tep_get_orders_manual_title($orders_id,$products_id){
$oID=$orders_id;
$pID=$products_id;
$products_info_query=tep_db_query("select products_id,site_id from ".TABLE_ORDERS_PRODUCTS." where orders_id='".$oID."' and products_id='".$pID."'");
$products_info_array=tep_db_fetch_array($products_info_query);

$categories_info_query=tep_db_query("select categories_id from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$products_info_array['products_id']."'");
$categories_info_array=tep_db_fetch_array($categories_info_query);
$categories_pid_query=tep_db_query("select parent_id from ".TABLE_CATEGORIES." where categories_id='".$categories_info_array['categories_id']."'");
$categories_pid_array=tep_db_fetch_array($categories_pid_query);
$cp_manual_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_pid_array['parent_id']."' and site_id='".$products_info_array['site_id']."'");
$cp_manual_array=tep_db_fetch_array($cp_manual_query);

$c_manual_query=tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$categories_info_array['categories_id']."' and site_id='".$products_info_array['site_id']."'");
$c_manual_array=tep_db_fetch_array($c_manual_query);

$pro_manual_query=tep_db_query("select products_name from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$products_info_array['products_id']."' and site_id='".$products_info_array."'");
$pro_manual_array=tep_db_fetch_array($pro_manual_query);
$manual_title=$cp_manual_array['categories_name'].'/'.$c_manual_array['categories_name'].'/'.$pro_manual_array['products_name'].'のマニュアル';
return $manual_title;
}

/* -------------------------------------
    功能: 生成订单相关信息 
    参数: $orders(array) 订单信息 
    参数: $single(boolean) 是否html输出 
    参数: $popup(boolean) 是否弹出 
    参数: $param_str(string) 其他参数 
    返回值: 订单相关信息(string) 
 ------------------------------------ */
function tep_get_orders_products_string($orders, $single = false, $popup = false, $param_str = '') {
  global $ocertify; 
  require_once(DIR_WS_CLASSES . 'payment.php');
  $str = '';

  $str .= '<table border="0" cellpadding="2" cellspacing="0" class="popup_order_title" width="100%">';
  $str .= '<tr>';
  $str .= '<td width="22">'.tep_image(DIR_WS_IMAGES.'icon_info.gif', IMAGE_ICON_INFO,16,16).'&nbsp;</td>'; 
  $str .= '<td align="left">['.$orders['orders_id'].']&nbsp;&nbsp;'.tep_datetime_short_torihiki($orders['date_purchased']).'</td>'; 
  $str .= '<td align="right"><a href="javascript:void(0);" onclick="hideOrdersInfo(1);">X</a></td>';
  $str .= '</tr>';
  $str .= '</table>';
  
  $str .= tep_draw_form('orders', FILENAME_ORDERS, urldecode($param_str).'&oID='.$orders['orders_id'].'&action=deleteconfirm');
  $str .= '<table border="0" cellpadding="0" cellspacing="0" class="popup_order_info" width="100%">';
  if (ORDER_INFO_TRANS_NOTICE == 'true') {
    if ($orders['orders_care_flag']) {
      $str .= '<tr>'; 
      $str .= '<td class="main" colspan="2"><font color="red">';
      $str .= RIGHT_ORDER_INFO_TRANS_NOTICE; 
      $str .= '</font></td>'; 
      $str .= '</tr>'; 
    }
  }
  
  if (ORDER_INFO_TRANS_WAIT == 'true') {
    if ($orders['orders_wait_flag']) {
      $str .= '<tr>'; 
      $str .= '<td class="main" colspan="2"><font color="red">';
      $str .= RIGHT_ORDER_INFO_TRANS_WAIT; 
      $str .= '</font></td>'; 
      $str .= '</tr>'; 
    } 
  }
  
  if (ORDER_INFO_INPUT_FINISH == 'true') {
    if ($orders['orders_inputed_flag']) {
      $str .= '<tr>'; 
      $str .= '<td class="main" colspan="2"><font color="red">';
      $str .= RIGHT_ORDER_INFO_INPUT_FINISH; 
      $str .= '</font></td>'; 
      $str .= '</tr>'; 
    } 
  }
  if(ORDER_INFO_BASIC_TEXT == 'true'){
    $str .= '<tr>';
    $str .= '<td class="main" width="120">';
    $str .= TEXT_FUNCTION_HEADING_CUSTOMERS;
    $str .= '</td>';
    $str .= '<td class="main">';
    $str .= tep_output_string_protected($orders['customers_name']); 
    $str .= '</td>';
    $str .= '</tr>';

  }
  
    $str .= '<tr><td class="main" width="120">支払方法：</td><td class="main" style="color:darkred;">'.payment::changeRomaji($orders['payment_method'],'title').'</td></tr>';
    
    if ($orders['confirm_payment_time'] != '0000-00-00 00:00:00') {
      $time_str = date('Y年n月j日', strtotime($orders['confirm_payment_time'])); 
    }else if(tep_check_order_type($orders['orders_id'])!=2){
      $time_str = '入金まだ'; 
    }
    if($time_str){
    $str .= '<tr><td class="main">入金日：</td><td class="main" style="color:red;">'.$time_str.'</td></tr>';
    }
    if(trim($orders['torihiki_houhou']) != ''){
      $str .= '<tr><td class="main">オプション：</td><td class="main" style="color:blue;">'.$orders['torihiki_houhou'].'</td></tr>';
    }

  $orders_products_query = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS." op,".TABLE_PRODUCTS." p where p.products_id = op.products_id and op.orders_id = '".$orders['orders_id']."'");
  $autocalculate_arr = array();
  $autocalculate_sql = "select oaf.value as arr_str from ".TABLE_OA_FORMVALUE." oaf,".
    TABLE_OA_ITEM." oai 
    where oaf.item_id = oai.id 
    and oai.type = 'autocalculate' 
    and oaf.orders_id = '".$orders['orders_id']."' 
    order by oaf.id asc limit 1 ";
  $autocalculate_query = tep_db_query($autocalculate_sql);
  $autocalculate_row = tep_db_fetch_array($autocalculate_query);
  $arr_checked = explode('_',$autocalculate_row['arr_str']);
  $autocalculate_arr = array();
  foreach($arr_checked as $key=>$value){
    $temp_arr = explode('|',$value);
    if($temp_arr[0] != 0 && $temp_arr[3] != 0){
      $autocalculate_arr[] = array($temp_arr[0],$temp_arr[3]);
    }
  }
  $tmpArr = array();
  if (ORDER_INFO_PRODUCT_LIST == 'true') { 
  while ($p = tep_db_fetch_array($orders_products_query)) {
    if(in_array($p,$tmpArr)){
      continue;
    }
    $tmpArr[] = $p ;
    $products_attributes_query = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id='".$p['orders_products_id']."'");
    if(in_array(array($p['products_id'],$p['orders_products_id']),$autocalculate_arr)&&
        !empty($autocalculate_arr)){
      $str .= '<tr><td class="main">商品：<font color="red">「入」</font></td><td class="main">'.$p['products_name'].'&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:window.open(\'orders.php?'.urldecode($param_str).'&oID='.$orders['orders_id'].'&pID='.$p['products_id'].'&action=show_manual_info\');">'.tep_html_element_button('マニュアル').'</a></td></tr>';
    }else{
      $str .= '<tr><td class="main">商品：<font color="red">「未」</font></td><td class="main">'.$p['products_name'].'&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="javascript:window.open(\'orders.php?'.urldecode($param_str).'&oID='.$orders['orders_id'].'&pID='.$p['products_id'].'&action=show_manual_info\');">'.tep_html_element_button('マニュアル').'</a></td></tr>';
    }
    $str .= '<tr><td class="main">個数：</td><td class="main">'.$p['products_quantity'].'個'.tep_get_full_count2($p['products_quantity'], $p['products_id'], $p['products_rate']).'</td></tr>';
    while($pa = tep_db_fetch_array($products_attributes_query)){
      $input_option = @unserialize(stripslashes($pa['option_info']));
      if ($input_option) {
        if (isset($input_option['title'])) {
          $str .= '<tr><td class="main">'.$input_option['title'].'：</td><td class="main">'.$input_option['value'].'</td></tr>';
        }
      }
    }
    $names = tep_get_buttons_names_by_orders_id($orders['orders_id']);
    if ($names) {
      $str .= '<tr><td class="main">PC：</td><td class="main">'.implode('&nbsp;,&nbsp;', $names).'</td></tr>';
    }
    $str .= '<tr><td class="main"></td><td class="main"></td></tr>';
    $i++;
  }
  }
  
  
  if (ORDER_INFO_ORDER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_FROM.'</td>'; 
    $str .= '<td class="main">';
    $str .= tep_get_site_name_by_order_id($orders['orders_id']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_FETCH_TIME.'</td>';
    $str .= '<td class="main">';
    $tmp_date_start = explode(' ', $orders['torihiki_date']);
    $tmp_date_end = explode(' ', $orders['torihiki_date_end']); 
    $tmp_week = date('D', strtotime($orders['torihiki_date'])); 
    switch(strtolower($tmp_week)) {
      case 'mon':
       $week_str = TEXT_DATE_MONDAY; 
       break;
      case 'tue':
       $week_str = TEXT_DATE_TUESDAY; 
       break;
      case 'wed':
       $week_str = TEXT_DATE_WEDNESDAY; 
       break;
     case 'thu':
       $week_str = TEXT_DATE_THURSDAY; 
       break;
     case 'fri':
       $week_str = TEXT_DATE_FRIDAY; 
       break;
     case 'sat':
       $week_str = TEXT_DATE_STATURDAY; 
       break;
     case 'sun':
       $week_str = TEXT_DATE_SUNDAY; 
       break;
     default:
       break;
    }
    $str .= date('Y'.YEAR_TEXT.'m'.MONTH_TEXT.'d'.DAY_TEXT, strtotime($orders['torihiki_date'])).' '.$week_str.'&nbsp;'.$tmp_date_start[1].'&nbsp;'.TEXT_TIME_LINK.'&nbsp;'.$tmp_date_end[1]; 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    if(trim($orders['torihiki_houhou']) != ''){ 
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_OPTION.'</td>';
      $str .= '<td class="main">';
      $str .= $orders['torihiki_houhou'];    
      $str .= '</td>'; 
      $str .= '</tr>'; 
    }
  
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_ID.'</td>';
    $str .= '<td class="main">';
    $str .= $orders['orders_id']; 
    $str .= '</td>'; 
    $str .= '</tr>'; 

    $create_week = date('D', strtotime($orders['date_purchased'])); 
    switch(strtolower($create_week)) {
      case 'mon':
       $week_string = TEXT_DATE_MONDAY; 
       break;
      case 'tue':
       $week_string = TEXT_DATE_TUESDAY; 
       break;
      case 'wed':
       $week_string = TEXT_DATE_WEDNESDAY; 
       break;
     case 'thu':
       $week_string = TEXT_DATE_THURSDAY; 
       break;
     case 'fri':
       $week_string = TEXT_DATE_FRIDAY; 
       break;
     case 'sat':
       $week_string = TEXT_DATE_STATURDAY; 
       break;
     case 'sun':
       $week_string = TEXT_DATE_SUNDAY; 
       break;
     default:
       break;
    } 
    $str .= '<tr>'; 
    $str .= '<td class="main">'.TEXT_FUNCTION_HEADING_DATE_PURCHASED.'</td>';
    $str .= '<td class="main">';
    $str .= date('Y'.YEAR_TEXT.'m'.MONTH_TEXT.'d'.DAY_TEXT, strtotime($orders['date_purchased'])).' '.$week_string; 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_CUSTOMER_TYPE.'</td>';
    $str .= '<td class="main">';
    if(get_guest_chk($orders['customers_id'])==0){
      $str .= TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_MEMBER;
    }else{
      $str .= TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_CUSTOMER;
    }
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_CUSTOMER_NAME.'</td>';
    $str .= '<td class="main">';
    $str .= '<a href="">'.$orders['customers_name'].'</a>'; 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $ostGetPara = array( "name"=>urlencode($orders['customers_name']),
                         "topicid"=>urlencode(constant("SITE_TOPIC_".$orders['site_id'])),
                         "source"=>urlencode('Email'), 
                         "email"=>urlencode($orders['customers_email_address']));
    $parmStr = '';
    foreach($ostGetPara as $key=>$value){
      $parmStr.= '&'.$key.'='.$value; 
    }
    $remoteurl = (defined('OST_SERVER')?OST_SERVER:'scp')."/tickets.php?a=open2".$parmStr."";
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_EMAIL.'</td>';
    $str .= '<td class="main">';
    $str .= tep_output_string_protected($orders['customers_email_address']).'&nbsp;&nbsp;<a title="'.RIGHT_TICKIT_ID_TITLE.'" href="'.$remoteurl.'" target="_blank">'.RIGHT_TICKIT_EMAIL.'</a>&nbsp;&nbsp;<a href="telecom_unknow.php?keywords='.tep_output_string_protected($orders['customers_email_address']).'">'.RIGHT_TICKIT_CARD.'</a>'; 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    if ( (($orders['cc_type']) || ($orders['cc_owner']) || ($orders['cc_number'])) ) {  
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_TYPE.'</td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_type']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
      
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_OWNER.'</td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_owner']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
      
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_ID.'</td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_number']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
      
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_ORDER_INFO_ORDER_CREDITCARD_EXPIRE_TIME.'</td>';
      $str .= '<td class="main">';
      $str .= $orders['cc_expires']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
  } 
  }
  if (ORDER_INFO_CUSTOMER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_IP.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_ip'] ?  $orders['orders_ip'] : 'UNKNOW',IP_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_HOST.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_host_name']?'<font'.($orders['orders_host_name'] == $orders['orders_ip'] ? ' color="red"':'').'>'.$orders['orders_host_name'].'</font>':'UNKNOW',HOST_NAME_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_USER_AGEMT.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_user_agent'] ?  $orders['orders_user_agent'] : 'UNKNOW',USER_AGENT_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  if ($orders['orders_user_agent']) { 
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_OS.'</td>';
      $str .= '<td class="main">';
      $str .= tep_high_light_by_keywords(getOS($orders['orders_user_agent']),OS_LIGHT_KEYWORDS); 
      $str .= '</td>'; 
      $str .= '</tr>'; 
      
      $browser_info = getBrowserInfo($orders['orders_user_agent']); 
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_BROWSE_TYPE.'</td>';
      $str .= '<td class="main">';
      $str .= tep_high_light_by_keywords($browser_info['longName'] . ' ' .  $browser_info['version'],BROWSER_LIGHT_KEYWORDS); 
      $str .= '</td>'; 
      $str .= '</tr>'; 
  } 
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_BROWSE_LAN.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_http_accept_language'] ?  $orders['orders_http_accept_language'] : 'UNKNOW',HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_COMPUTER_LAN.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_system_language'] ?  $orders['orders_system_language'] : 'UNKNOW',SYSTEM_LANGUAGE_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_USER_LAN.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_user_language'] ?  $orders['orders_user_language'] : 'UNKNOW',USER_LANGUAGE_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_PIXEL.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_screen_resolution'] ?  $orders['orders_screen_resolution'] : 'UNKNOW',SCREEN_RESOLUTION_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_COLOR.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_color_depth'] ?  $orders['orders_color_depth'] : 'UNKNOW',COLOR_DEPTH_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_FLASH.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_flash_enable'] === '1' ?  'YES' : ($orders['orders_flash_enable'] === '0' ? 'NO' : 'UNKNOW'),FLASH_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
  if ($orders['orders_flash_enable']) {
      $str .= '<tr>'; 
      $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_FLASH_VERSION.'</td>';
      $str .= '<td class="main">';
      $str .= tep_high_light_by_keywords($orders['orders_flash_version'],FLASH_VERSION_LIGHT_KEYWORDS); 
      $str .= '</td>'; 
      $str .= '</tr>'; 
  }
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_DIRECTOR.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_director_enable'] === '1' ? 'YES' : ($orders['orders_director_enable'] === '0' ? 'NO' : 'UNKNOW'),DIRECTOR_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_QUICK_TIME.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_quicktime_enable'] === '1' ? 'YES' : ($orders['orders_quicktime_enable'] === '0' ? 'NO' : 'UNKNOW'),QUICK_TIME_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_REAL_PLAYER.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_realplayer_enable'] === '1' ?  'YES' : ($orders['orders_realplayer_enable'] === '0' ? 'NO' : 'UNKNOW'),REAL_PLAYER_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_WINDOWS_MEDIA.'</td>';
    $str .= '<td class="main">'; $str .= tep_high_light_by_keywords($orders['orders_windows_media_enable'] === '1' ? 'YES' : ($orders['orders_windows_media_enable'] === '0' ?  'NO' : 'UNKNOW'),WINDOWS_MEDIA_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_PDF.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_pdf_enable'] === '1' ?  'YES' : ($orders['orders_pdf_enable'] === '0' ? 'NO' : 'UNKNOW'),PDF_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    
    $str .= '<tr>'; 
    $str .= '<td class="main">'.RIGHT_CUSTOMER_INFO_ORDER_JAVA.'</td>';
    $str .= '<td class="main">';
    $str .= tep_high_light_by_keywords($orders['orders_java_enable'] === '1' ?  'YES' : ($orders['orders_java_enable'] === '0' ? 'NO' : 'UNKNOW'),JAVA_LIGHT_KEYWORDS); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  }
 
  if (ORDER_INFO_REFERER_INFO == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">Referer Info：</td>';
    $str .= '<td class="main">';
    $str .= urldecode($orders['orders_ref']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
    if ($orders['orders_ref_keywords']) {
      $str .= '<tr>'; 
      $str .= '<td class="main">KEYWORDS：</td>';
      $str .= '<td class="main">';
      $str .= $orders['orders_ref_keywords']; 
      $str .= '</td>'; 
      $str .= '</tr>'; 
    }
  }
  
  if (ORDER_INFO_ORDER_HISTORY == 'true') {
    $order_history_list_raw = tep_db_query("select * from ".TABLE_ORDERS." where customers_email_address = '".$orders['customers_email_address']."' order by date_purchased desc limit 5"); 
    if (tep_db_num_rows($order_history_list_raw)) {
      $str .= '<tr>';      
      $str .= '<td class="main" colspan="2">';      
      $str .= '<table width="100%" border="0" cellspacing="0" cellpadding="2">'; 
      $str .= '<tr>'; 
      $str .= '<td colspan="4">Order History：</td>'; 
      $str .= '</tr>'; 
      while ($order_history_list = tep_db_fetch_array($order_history_list_raw)) {
        $str .= '<tr>'; 
        $str .= '<td>'; 
        $store_name_raw = tep_db_query("select * from ".TABLE_SITES." where id = '".$order_history_list['site_id']."'"); 
        $store_name_res = tep_db_fetch_array($store_name_raw); 
        $str .= $store_name_res['romaji']; 
        $str .= '</td>'; 
        $str .= '<td>'; 
        $str .= $order_history_list['date_purchased']; 
        $str .= '</td>'; 
        $str .= '<td>'; 
        $str .= strip_tags(tep_get_ot_total_by_orders_id($order_history_list['orders_id'], true)); 
        $str .= '</td>'; 
        $str .= '<td>'; 
        $str .= $order_history_list['orders_status_name']; 
        $str .= '</td>'; 
        $str .= '</tr>'; 
      }
      $str .= '</table>'; 
      $str .= '</td>';      
      $str .= '</tr>';      
    }
  }
  
  if (ORDER_INFO_REPUTAION_SEARCH == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">';
    $str .= RIGHT_ORDER_INFO_REPUTAION_SEARCH; 
    $str .= '</td>';
    $str .= '<td class="main">';
    $str .= tep_get_customers_fax_by_id($orders['customers_id']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  }
  
  if (ORDER_INFO_ORDER_COMMENT == 'true') {
    $str .= '<tr>'; 
    $str .= '<td class="main">';
    $str .= RIGHT_ORDER_COMMENT_TITLE; 
    $str .= '</td>';
    $str .= '<td class="main">';
    $str .= nl2br($orders['orders_comment']); 
    $str .= '</td>'; 
    $str .= '</tr>'; 
  }
if(tep_not_null($orders['user_added']) || tep_not_null($orders['customers_name'])){
	$str .= '<tr>';
	$str .= '<td class="main">';  
	$str .= TEXT_USER_ADDED;
	$str .= '</td>';
	$str .= '<td class="main">';
	if(isset($orders['user_added']) && $orders['user_added'] != ""){
   $str .= $orders['user_added'];	
	}else{
   $str .= $orders['customers_name'];	
	}	
	$str .= '</td>';
	$str .= '</tr>';
}else{
	$str .= '<tr>';
	$str .= '<td class="main">';  
	$str .= TEXT_USER_ADDED;
	$str .= '</td>';
	$str .= '<td class="main">';
        $str .= TEXT_UNSET_DATA;	
	$str .= '</td>';
	$str .= '</tr>';

}if(tep_not_null($orders['date_purchased'])){
        $str .= '<tr>';	
	$str .= '<td class="main">';  
	$str .= TEXT_DATE_ADDED;
	$str .= '</td>';
	$str .= '<td class="main">';
	$str .= $orders['date_purchased'];
	$str .= '</td>';
	$str .= '</tr>';
}else{
        $str .= '<tr>';	
	$str .= '<td class="main">';  
	$str .= TEXT_DATE_ADDED;
	$str .= '</td>';
	$str .= '<td class="main">';
	$str .= TEXT_UNSET_DATA;
	$str .= '</td>';
	$str .= '</tr>';

}if(tep_not_null($orders['user_update']) || tep_not_null($orders['customers_name'])){
        $str .= '<tr>';	
	$str .= '<td class="main">';  
	$str .= TEXT_USER_UPDATE;
	$str .= '</td>';
	$str .= '<td class="main">';
        if(isset($orders['user_update']) && $orders['user_update'] != ""){
        $str .= $orders['user_update'];	
        }else{
        $str .= $orders['customers_name'];
        }
	$str .= '</td>';
	$str .= '</tr>';
}else{
        $str .= '<tr>';	
	$str .= '<td class="main">';  
	$str .= TEXT_USER_UPDATE;
	$str .= '</td>';
	$str .= '<td class="main">';
        $str .= TEXT_UNSET_DATA;	
	$str .= '</td>';
	$str .= '</tr>';
}if(tep_not_null($orders['last_modified']) || tep_not_null($orders['date_purchased'])){ 
        $str .= '<tr>';	
	$str .= '<td class="main">';  
	$str .= TEXT_DATE_UPDATE;
	$str .= '</td>';
	$str .= '<td class="main">';
        if(isset($orders['last_modified']) && $orders['last_modified'] != ""){
	$str .= $orders['last_modified'];
        }else{
        $str .= $orders['date_purchased']; 
        }
	$str .= '</td>';
	$str .= '</tr>';
}else{
        $str .= '<tr>';	
	$str .= '<td class="main">';  
	$str .= TEXT_DATE_UPDATE;
	$str .= '</td>';
	$str .= '<td class="main">';
	$str .= TEXT_UNSET_DATA;
	$str .= '</td>';
	$str .= '</tr>';
}



  $str .= '</table>';
  $str .= '<table class="popup_order_info" border="0" cellpadding="2" cellspacing="0" width="100%">';
  $str .= '<tr><td class="main" colspan="2" align="center">';
  $str .= '<div id="order_del">'; 
  $str .= '<a href="'.tep_href_link(FILENAME_ALARM, urldecode($param_str.'&oID='.$orders['orders_id'])).'">'.tep_html_element_button(TEXT_ORDER_ALARM_LINK).'</a>'; 
  $str .= '<a href="'.tep_href_link(FILENAME_ORDERS, urldecode($param_str).'&oID='.$orders['orders_id'].'&action=edit').'">'.tep_html_element_button(IMAGE_DETAILS).'</a>'; 
  if ($ocertify->npermission >= 15) {
    $str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="delete_order_info(\''.$orders['orders_id'].'\', \''.urlencode($param_str).'\')"').'</a>'; 
  }
  $str .= '</div>'; 
  $str .= '</td></tr>';
  $str .= '</table>';
  $str .= '</form>'; 
  $str=str_replace("\n","",$str);
  $str=str_replace("\r","",$str);
  if ($single) {
    echo $str; 
  } else {
    return htmlspecialchars($str);
  }
}

/* -------------------------------------
    功能: 获取该订单的按钮名 
    参数: $orders_id(int) 订单id 
    返回值: 按钮名(string) 
 ------------------------------------ */
function tep_get_buttons_names_by_orders_id($orders_id)
{
  $names = array();
  $o2c_query = tep_db_query("select * from ".TABLE_ORDERS_TO_BUTTONS." o2b, ".TABLE_BUTTONS." b where b.buttons_id=o2b.buttons_id and o2b.orders_id = '".$orders_id."' order by sort_order asc");
  while($o = tep_db_fetch_array($o2c_query)) {
    $names[] = $o['buttons_name'];
  }
  return $names;
}

/* -------------------------------------
    功能: 获取所有按钮信息 
    参数: 无 
    返回值: 按钮信息(array) 
 ------------------------------------ */
function tep_get_buttons()
{
  $buttons = array();
  $buttons_query = tep_db_query("select * from ".TABLE_BUTTONS." order by sort_order asc");
  while ($c = tep_db_fetch_array($buttons_query)) {
    $buttons[] = $c;
  }
  return $buttons;
}

/* -------------------------------------
    功能: 获取该订单的按钮id 
    参数: $oid(int) 订单id 
    返回值: 该订单的按钮id(array) 
 ------------------------------------ */
function tep_get_buttons_by_orders_id($oid)
{
  $c = array();
  $o2c_query = tep_db_query("select * from ".TABLE_ORDERS_TO_BUTTONS." where orders_id = '".$oid."'");
  while ($o2c = tep_db_fetch_array($o2c_query)) {
    $c[] = $o2c['buttons_id'];
  }
  return $c;
}

/* -------------------------------------
    功能: 获取该订单的状态以及修改时间 
    参数: $orders_id(int) 订单id 
    参数: $language_id(int) 语言id 
    返回值: 该订单的状态以及修改时间(string) 
 ------------------------------------ */
function tep_get_orders_changed($orders_id, $language_id = '') {
  global $languages_id;

  if (!$language_id) $language_id = $languages_id;
  $orders_query = tep_db_query("select * from ".TABLE_ORDERS." where orders_id='".$orders_id."'");
  $orders = tep_db_fetch_array($orders_query);
  return $orders['orders_status'] . $orders['last_modified'];
}

/* -------------------------------------
    功能: 获取该订单的指定状态的最近一次记录时间 
    参数: $orders_id(int) 订单id 
    参数: $orders_status_id(int) 状态id 
    返回值: 最近一次记录时间(string) 
 ------------------------------------ */
function tep_get_orders_status_history_time($orders_id, $orders_status_id){
  $history = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id='".$orders_id."' and orders_status_id='".$orders_status_id."' order by date_added desc"));
  return $history['date_added'];
}

/* -------------------------------------
    功能: 获取该订单的指定状态的最近一次通知时间 
    参数: $orders_id(int) 订单id 
    参数: $orders_status_id(int) 状态id 
    返回值: 最近一次通知时间(string) 
 ------------------------------------ */
function tep_get_orders_status_history_notified($orders_id, $orders_status_id){
  $history = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id='".$orders_id."' and orders_status_id='".$orders_status_id."' order by date_added desc"));
  return $history['customer_notified'];
}

/* -------------------------------------
    功能: 获取该订单完成的标识 
    参数: $orders_id(int) 订单id 
    参数: $language_id(int) 语言id 
    返回值: 完成的标识(string) 
 ------------------------------------ */
function tep_orders_finished($orders_id, $language_id = '') {
  global $languages_id;

  if (!$language_id) $language_id = $languages_id;

  $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS." where orders_id='".$orders_id."'"));
  $order_status = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$order['orders_status']."'"));
  return $order_status['finished'];
}

/* -------------------------------------
    功能: 获取网站的名字 
    参数: $siteurl(string) 网站地址 
    返回值: 网站的名字(string) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获取顾客类型 
    参数: $customers_id(int) 顾客id 
    返回值: 顾客类型(string) 
 ------------------------------------ */
function get_guest_chk($customers_id)
{
  $customers = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id='".$customers_id."'"));
  return $customers['customers_guest_chk'];
}

/* -------------------------------------
    功能: 高亮显示字符串里的关键字 
    参数: $str(string) 字符串 
    参数: $keywords(string) 关键字 
    返回值: 处理后的字符串(string) 
 ------------------------------------ */
function tep_high_light_by_keywords($str, $keywords)
{
  $k = $rk= explode('|',$keywords);
  foreach($k as $key => $value){
    $rk[$key] = '<font style="background:red;">'.$value.'</font>';
  }
  return str_replace($k, $rk, $str);
}

/* -------------------------------------
    功能: 判断字符串里是否有关键字 
    参数: $str(string) 字符串 
    参数: $keywords(string) 关键字 
    返回值: 是否有关键字(boolean) 
 ------------------------------------ */
function tep_match_by_keywords($str, $keywords)
{
  $k = explode('|',$keywords);
  foreach($k as $key => $value){
    if (preg_match('/'.$value.'/', $str)) {
      return true;
    }
  }
}

/* -------------------------------------
    功能: 获取该订单的第一个商品的名字 
    参数: $orders_id(string) 订单id 
    返回值: 商品的名字(string) 
 ------------------------------------ */
function tep_get_first_products_name_by_orders_id($orders_id)
{
  $p = tep_db_fetch_array(tep_db_query("select * from " . TABLE_ORDERS_PRODUCTS . " where orders_id='".$orders_id."'"));
  return $p['products_name'];
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

/* -------------------------------------
    功能: 获取支付可用的日期 
    参数: $time(int) 时间 
    参数: $cl_status_array(array) 日历状态的信息 
    参数: $repeat_array(array) 日历的重复设置信息 
    参数: $cl_repeat_array(array) 分类处理特殊重复设置的信息 
    参数: $default_is_handle(string) 默认受理状态 
    参数: $default_cl_id(int) 默认受理状态的ID 
    参数: $cl_week_array(array) 重复周的信息 
    参数: $cl_month_day_array(array) 每月重复日的信息 
    参数: $cl_month_week_array(array) 每月重复固定周的信息 
    参数: $cl_year_month_array(array) 每年重复月日的信息 
    返回值: 支付可用的日期(string) 
 ------------------------------------ */
function tep_get_pay_time($time,$cl_status_array,$repeat_array,$cl_repeat_array,$default_is_handle,$default_cl_id,$cl_week_array,$cl_month_day_array,$cl_month_week_array,$cl_year_month_array){

  //读取相应日期的数据
  $cl_date_str = date('Ymd',strtotime($time));
  $calendar_date_query = tep_db_query("select * from ". TABLE_CALENDAR_DATE ." where cl_date='".$cl_date_str."'");
  $calendar_date_array = tep_db_fetch_array($calendar_date_query);
  $calendar_date_num = tep_db_num_rows($calendar_date_query);
  tep_db_free_result($calendar_date_query); 
  $repeat_sort_query = tep_db_query("select sort from ". TABLE_CALENDAR_STATUS ." where id='".$calendar_date_array['type']."'");
  $repeat_sort_array = tep_db_fetch_array($repeat_sort_query);
  tep_db_free_result($repeat_sort_query);

  $calendar_date_id = $calendar_date_array['id'];
  $calendar_repeat_flag = false;
  $day = tep_get_repeat_date(2,$cl_date_str);
  
  $wday = tep_get_repeat_date(1,$cl_date_str);

  $temp_num_week = ceil($day/7);

  $temp_year_month_day = substr($cl_date_str,4,4); 

  //状态重复设置，冲突时，以状态排序最小的一个为准 
  $date_time_array = array();
  $date_time_array = array('month'=>$repeat_array[$cl_month_day_array[$day]]['sort'],
                           'week'=>$repeat_array[$cl_week_array[$wday]]['sort'],                     
                           'month_week'=>$repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort'],
                           'year'=>$repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort']
                           );
  arsort($date_time_array);
  $date_time_array = array_filter($date_time_array);
  $first_value_array = array_slice($date_time_array,0,1);
  $first_value_type = array_keys($first_value_array);
  if($first_value_array[$first_value_type[0]] != ''){
    switch($first_value_type[0]){

      case 'month':
        $cl_date_temp = isset($calendar_date_array['type']) && $calendar_date_array['type'] == 0 && $repeat_array[$cl_month_day_array[$day]]['sort'] == -1 ? true : false;
        if(($repeat_sort_array['sort'] != '' && $repeat_array[$cl_month_day_array[$day]]['sort'] != '' && $repeat_sort_array['sort'] < $repeat_array[$cl_month_day_array[$day]]['sort']) || ($repeat_sort_array['sort'] == '' && $repeat_array[$cl_month_day_array[$day]]['sort'] != '') || ($repeat_array[$cl_month_day_array[$day]]['sort'] == -1)){
          $calendar_date_id  = array_key_exists($day,$cl_month_day_array) && $cl_date_temp == false ?  $cl_month_day_array[$day] : $calendar_date_id;
          $calendar_repeat_flag = true;
        } 
        if($repeat_array[$cl_month_day_array[$day]]['sort'] == -1){ return date("Y-m-d");}
        break;
      case 'week':
        $cl_date_temp = isset($calendar_date_array['type']) && $calendar_date_array['type'] == 0 && $repeat_array[$cl_week_array[$wday]]['sort'] == -1 ? true : false;
        if(($repeat_sort_array['sort'] != '' && $repeat_array[$cl_week_array[$wday]]['sort'] != '' && $repeat_sort_array['sort'] < $repeat_array[$cl_week_array[$wday]]['sort']) || ($repeat_sort_array['sort'] == '' && $repeat_array[$cl_week_array[$wday]]['sort'] != '') || ($repeat_array[$cl_week_array[$wday]]['sort'] == -1)){
          $calendar_date_id = array_key_exists($wday,$cl_week_array) && $cl_date_temp == false ? $cl_week_array[$wday] : $calendar_date_id; 
          $calendar_repeat_flag = true;
        }          
        if($repeat_array[$cl_week_array[$wday]]['sort'] == -1){ return date("Y-m-d");}
        break;
      case 'month_week':
        $cl_date_temp = isset($calendar_date_array['type']) && $calendar_date_array['type'] == 0 && $repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort'] == -1 ? true : false;  
        if(($repeat_sort_array['sort'] != '' && $repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort'] != '' && $repeat_sort_array['sort'] < $repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort']) || ($repeat_sort_array['sort'] == '' && $repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort'] != '') || ($repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort'] == -1)){ 
          $temp_week_array = array_slice($cl_month_week_array,0,1);
          $calendar_date_id = is_array($cl_month_week_array[$temp_num_week]) && $cl_date_temp == false ? $cl_month_week_array[$temp_num_week][$wday] : $calendar_date_id;
          $calendar_repeat_flag = true;
        }          
        if($repeat_array[$cl_month_week_array[$temp_num_week][$wday]]['sort'] == -1){ return date("Y-m-d");}
        break;
      case 'year':
        $cl_date_temp = isset($calendar_date_array['type']) && $calendar_date_array['type'] == 0 && $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] == -1 ? true : false; 
        if(($repeat_sort_array['sort'] != '' && $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] != '' && $repeat_sort_array['sort'] < $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort']) ||($repeat_sort_array['sort'] == '' && $repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] != '') || ($repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] == -1)){ 
          $calendar_date_id = array_key_exists($temp_year_month_day,$cl_year_month_array) && $cl_date_temp == false ? $cl_year_month_array[$temp_year_month_day] : $calendar_date_id;
          $calendar_repeat_flag = true; 
        }          
        if($repeat_array[$cl_year_month_array[$temp_year_month_day]]['sort'] == -1){ return date("Y-m-d");}
        break;
    }
  }

  //读取相应日期的数据
  $calendar_date_query = tep_db_query("select type from ". TABLE_CALENDAR_DATE ." where id='".$calendar_date_id."'");
  $calendar_date_array = tep_db_fetch_array($calendar_date_query);
  $calendar_type_num = tep_db_num_rows($calendar_date_query); 
  tep_db_free_result($calendar_date_query);
  $calendar_date_array['type'] = $calendar_type_num > 0 ? $calendar_date_array['type']: $default_cl_id;

  if($calendar_repeat_flag == true){

    if($cl_status_array[$calendar_date_array['type']]['is_handle'] == 1){
      if(date('Y-m-d') == date('Y-m-d',strtotime($time))){
        if(date('H:i',strtotime($time)) < $cl_status_array[$calendar_date_array['type']]['end_time'].':'.($cl_status_array[$calendar_date_array['type']]['end_min'] < 10 ? '0'.$cl_status_array[$calendar_date_array['type']]['end_min'] : $cl_status_array[$calendar_date_array['type']]['end_min'])){
          return date('Y-m-d',strtotime($time));
        }
      }else{

        return date('Y-m-d',strtotime($time));
      }
    }
  }else{
    if($calendar_date_num == 0){

      if($default_is_handle == 1){

        if(date('Y-m-d') == date('Y-m-d',strtotime($time))){
          if(date('H:i',strtotime($time)) < $cl_status_array[$calendar_date_array['type']]['end_time'].':'.($cl_status_array[$calendar_date_array['type']]['end_min'] < 10 ? '0'.$cl_status_array[$calendar_date_array['type']]['end_min'] : $cl_status_array[$calendar_date_array['type']]['end_min'])){
            return date('Y-m-d',strtotime($time));
          }
        }else{

          return date('Y-m-d',strtotime($time));
        }    
      }
    }else{
      if($cl_status_array[$calendar_date_array['type']]['is_handle'] == 1){

        if(date('Y-m-d') == date('Y-m-d',strtotime($time))){
          if(date('H:i',strtotime($time)) < $cl_status_array[$calendar_date_array['type']]['end_time'].':'.($cl_status_array[$calendar_date_array['type']]['end_min'] < 10 ? '0'.$cl_status_array[$calendar_date_array['type']]['end_min'] : $cl_status_array[$calendar_date_array['type']]['end_min'])){
            return date('Y-m-d',strtotime($time));
          }
        }else{

          return date('Y-m-d',strtotime($time));
        }    
      }   
    }
  }

  if($calendar_date_num == 0 && $calendar_repeat_flag == false){

    return date('Y-m-d');
  }

  if($cl_date_temp == true){
    return date('Y-m-d'); 
  }
  return tep_get_pay_time(date('Y-m-d H:i:s', strtotime($time.' + 1 day')),$cl_status_array,$repeat_array,$cl_repeat_array,$default_is_handle,$default_cl_id,$cl_week_array,$cl_month_day_array,$cl_month_week_array,$cl_year_month_array);
}

/* -------------------------------------
    功能: 取得支付时间,当天或者下一个工作日 
    参数: $time(int/null) 时间 
    返回值: 当天或者下一个工作日(string) 
 ------------------------------------ */
function tep_get_pay_day($time = null){
  if($time === null){
    $time = date('Y-m-d H:i:s');
  }

  //获取日历状态的信息
  $cl_status_array = array();
  $calendar_status_query = tep_db_query("select id,is_handle,end_time,end_min from ". TABLE_CALENDAR_STATUS ." order by sort asc,id asc");
  $default_is_handle = '';
  $default_cl_id = '';
  while($calendar_status_array = tep_db_fetch_array($calendar_status_query)){

    if($default_is_handle == ''){

      $default_is_handle = $calendar_status_array['is_handle'];
      $default_cl_id = $calendar_status_array['id'];
    }
    $cl_status_array[$calendar_status_array['id']] = array('is_handle'=>$calendar_status_array['is_handle'],'end_time'=>$calendar_status_array['end_time'],'end_min'=>$calendar_status_array['end_min']);
  }
  tep_db_free_result($calendar_status_query);

  //获取日历的重复设置信息 
  $repeat_array = array(); 
  $repeat_date_query = tep_db_query("select id,cl_date,type,repeat_type,date_update from ". TABLE_CALENDAR_DATE ." where repeat_type!=0 order by date_update asc");    
  while($repeat_date_array = tep_db_fetch_array($repeat_date_query)){

    $repeat_sort_query = tep_db_query("select sort from ". TABLE_CALENDAR_STATUS ." where id='".$repeat_date_array['type']."'");
    $repeat_sort_array = tep_db_fetch_array($repeat_sort_query);
    tep_db_free_result($repeat_sort_query);
    $repeat_array[$repeat_date_array['id']] = array('cl_date'=>$repeat_date_array['cl_date'],'repeat'=>$repeat_date_array['repeat_type'],'type'=>$repeat_date_array['type'],'date_update'=>$repeat_date_array['date_update'],'sort'=>($repeat_date_array['type'] == 0 ? -1 :$repeat_sort_array['sort']));
  }
  tep_db_free_result($repeat_date_query);
  //分类处理特殊重复设置

  foreach($repeat_array as $cl_key=>$cl_value){

    if($cl_value['repeat'] == 1){

       $cl_repeat_array[1][$cl_key] = tep_get_repeat_date(1,$cl_value['cl_date']);
    }

    if($cl_value['repeat'] == 2){

       $cl_repeat_array[2][$cl_key] = tep_get_repeat_date(2,$cl_value['cl_date']);
    }

    if($cl_value['repeat'] == 3){

       $cl_repeat_array[3][$cl_key] = tep_get_repeat_date(3,$cl_value['cl_date']);
    }

    if($cl_value['repeat'] == 4){

       $cl_repeat_array[4][$cl_key] = tep_get_repeat_date(4,$cl_value['cl_date']);
    }
  }

  //重复周
  $cl_week_array = array();
  foreach($cl_repeat_array[1] as $cl_week_key=>$cl_week_value){

      $cl_week_array[$cl_week_value] = $cl_week_key;
  }

  //每月重复的日
  $cl_month_day_array = array();
  foreach($cl_repeat_array[2] as $cl_month_key=>$cl_month_value){

      $cl_month_day_array[$cl_month_value] = $cl_month_key;
  }

  //每月重复固定周
  $cl_month_week_array = array();
  foreach($cl_repeat_array[3] as $cl_month_week_key=>$cl_month_week_value){

      $cl_month_week_array[$cl_month_week_value[0]][$cl_month_week_value[1]] = $cl_month_week_key;
  }

  //每年重复的月日
  $cl_year_month_array = array();
  foreach($cl_repeat_array[4] as $cl_year_month_key=>$cl_year_month_value){

      $cl_year_month_array[$cl_year_month_value] = $cl_year_month_key; 
  }
  //获取可支付的时间 
  return tep_get_pay_time($time,$cl_status_array,$repeat_array,$cl_repeat_array,$default_is_handle,$default_cl_id,$cl_week_array,$cl_month_day_array,$cl_month_week_array,$cl_year_month_array);  
}



/* -------------------------------------
    功能: 显示google搜索结果 
    参数: $from_url(string) 来源url 
    参数: $c_type(boolean) 是否有网站id参数 
    返回值: google搜索结果(string) 
 ------------------------------------ */
function tep_display_google_results($from_url='', $c_type=false){
  // 谷歌关键字结果显示停止条件
  $stop_site_url = array(
      );
  $tmp_param_str = ''; 
  if ($c_type == true) {
    $tmp_param_str = isset($_GET['site_id'])?'&csite_id='.$_GET['site_id']:'';  
  }
  if(isset($_GET['cPath'])&&$_GET['cPath']!=''){
    $categories_id = array_pop(explode('_',$_GET['cPath']));
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
        $icount = 1; //序号
        foreach($url_arr as $distinct_url){
          if($icount%2==0){
            echo "<tr class='dataTableSecondRow'>";
          }else{
            echo "<tr class='dataTableRow'>";
          }
          if(in_array($distinct_url,$stop_site_url)){
            $search_message = sprintf(TEXT_FIND_DATA_STOP, $distinct_url);
            echo "<td class='dataTableContent search_class_td' style='width:22px' nowrap='nowrap'>&nbsp;".$icount++.":"."</td>";
            echo "<td class='dataTableContent' ><b>".tep_get_siteurl_name($distinct_url)."</b></td>";
            echo "<td class='dataTableContent' >";
            if(isset($from_url)&&$from_url){
              echo "<a href='".tep_href_link(FILENAME_RECORD,
                  'from='.$from_url.'&action=rename&act='.$_GET['action'].'&cID='.$_GET['cID'].'&cPath='.$_GET['cPath'].'&url='.$prama_url.$tmp_param_str).
                "'>".TEXT_RENAME."</a>";
            }else{
              echo "<a href='".tep_href_link(FILENAME_RECORD,
                  'action=rename&act='.$_GET['action'].'&cID='.$_GET['cID'].'&cPath='.$_GET['cPath'].'&url='.$prama_url.$tmp_param_str).
                "'>".TEXT_RENAME."</a>";
            }
            echo "</td></tr>";
            break;
          }
          $prama_url = str_replace('.','_',$distinct_url); 
          echo "<td class='dataTableContent search_class_td' style='width:22px' nowrap='nowrap'>&nbsp;".$icount++.":"."</td>";
          echo "<td class='dataTableContent' >".tep_get_siteurl_name($distinct_url)."</td>";
          echo "<td class='dataTableContent' >";
          if(isset($from_url)&&$from_url){
            echo "<a href='".tep_href_link(FILENAME_RECORD,
                'from='.$from_url.'&action=rename&act='.$_GET['action'].'&cID='.$_GET['cID'].'&cPath='.$_GET['cPath'].'&url='.$prama_url.$tmp_param_str).
              "'>".TEXT_RENAME."</a>";
          }else{
            echo "<a href='".tep_href_link(FILENAME_RECORD,
                'action=rename&act='.$_GET['action'].'&cID='.$_GET['cID'].'&cPath='.$_GET['cPath'].'&url='.$prama_url.$tmp_param_str).
              "'>".TEXT_RENAME."</a>";
          }
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

/* -------------------------------------
    功能: 获取该分类的父分类id 
    参数: $cid(int) 分类id 
    返回值: 父分类id(int) 
 ------------------------------------ */
  function tep_get_category_parent_id($cid){
    if ($cid) {
      $c = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id='".$cid."'"));
      return $c['parent_id'];
    } else {
      return 0;
    }
  }

/* -------------------------------------
    功能: 获取该商品的关联分类id 
    参数: $pid(int) 商品id 
    返回值: 分类id(int) 
 ------------------------------------ */
  function tep_get_products_parent_id($pid){
    $carr = array();
    $query = tep_db_query("select * from ".TABLE_PRODUCTS_TO_CATEGORIES." where products_id='".$pid."'");
    while($p2c = tep_db_fetch_array($query)){
      $carr[] = $p2c['categories_id'];
    }
    return $carr[0];
  }

/* -------------------------------------
    功能: 获取该商品的关联商品的名字 
    参数: $pid(int) 商品id 
    返回值: 关联商品的名字(string) 
 ------------------------------------ */
  function tep_get_relate_products_name($pid) {
    $p = tep_db_fetch_array(tep_db_query("select relate_products_id from ".TABLE_PRODUCTS." where products_id='".$pid."'"));
    $r = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id='".$p['relate_products_id']."'"));
    return $r['products_name'];
  }

/* -------------------------------------
    功能: 获取该商品的乘积率 
    参数: $pid(int) 商品id 
    返回值: 信息(string) 
 ------------------------------------ */
  function tep_get_products_rate($pid) {
    $n = str_replace(',','',tep_get_full_count_in_order2(1, $pid));
    preg_match_all('/(\d+)/',$n,$out);
    return $out[1][0];
  }

/* -------------------------------------
    功能: 判断该字符串是否存在指定字符 
    参数: $str(string) 字符串 
    返回值: 是否存在(boolean) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 判断该罗马字是否存在指定字符 
    参数: $romaji(string) 罗马字 
    返回值: 是否存在(boolean) 
 ------------------------------------ */
  function tep_check_romaji($romaji){
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

/* -------------------------------------
    功能: 获取商品最大/最小库存 
    参数: $pid(int) 商品id 
    返回值: 库存信息(array) 
 ------------------------------------ */
  function tep_get_product_inventory($pid) {
    $inventory_sql = "select max_inventory as `max`,min_inventory as `min` 
      from ".TABLE_PRODUCTS." WHERE products_id='".$pid."'";
    $inventory_res = tep_db_query($inventory_sql);
    return tep_db_fetch_array($inventory_res);
  }

/* -------------------------------------
    功能: 更新商品在库信息 
    参数: $pid(int) 商品id 
    参数: $status(int) 状态 
    返回值: 无 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 获得商品在库信息 
    参数: $pid(int) 商品id 
    返回值: 在库信息(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 判断该分类描述在指定网站是否存在 
    参数: $cid(int) 分类id 
    参数: $site_id(int) 网站id 
    返回值: 是否存在(boolean) 
 ------------------------------------ */
  function tep_check_categories_exists($cid, $site_id)
  {
    $exists_ca_query = tep_db_query("select * from ".TABLE_CATEGORIES_DESCRIPTION." where site_id = '".(int)$site_id."' and categories_id = '".$cid."'");

    return tep_db_num_rows($exists_ca_query);
  }

/* -------------------------------------
    功能: 创建指定网站的分类信息
    参数: $cid(int) 分类id 
    参数: $site_id(int) 网站id 
    返回值: 无 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 更新分类在指定网站的状态
    参数: $categories_id(int) 分类id 
    参数: $status(int) 状态id 
    参数: $site_id(int) 网站id 
    返回值: 更新成功(boolean) 
 ------------------------------------ */
  function tep_set_categories_status_by_site_id($categories_id, $status, $site_id)
  {
    tep_db_query("UPDATE `".TABLE_CATEGORIES_DESCRIPTION."` SET `categories_status` = '".intval($status)."' WHERE `categories_id` =".$categories_id." and `site_id` = '".$site_id."' LIMIT 1 ;");
    return true;
  }

/* -------------------------------------
    功能: 判断该商品描述在指定网站是否存在
    参数: $pid(int) 商品id 
    参数: $site_id(int) 网站id 
    返回值: 是否存在(boolean) 
 ------------------------------------ */
  function tep_check_products_exists($pid, $site_id)
  {
    $exist_pro_query = tep_db_query("select * from ".TABLE_PRODUCTS_DESCRIPTION." where products_id = '".$pid."' and site_id = '".(int)$site_id."'");
    return tep_db_num_rows($exist_pro_query);
  }

/* -------------------------------------
    功能: 创建指定网站的商品信息
    参数: $pid(int) 商品id 
    参数: $site_id(int) 网站id 
    返回值: 无 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 更新指定商品在指定网站的状态
    参数: $products_id(int) 商品id 
    参数: $status(int) 状态id 
    参数: $site_id(int) 网站id 
    参数: $up_rs(boolean) 是否更新评论 
    返回值: 是否更新成功(boolean/int) 
 ------------------------------------ */
  function tep_set_product_status_by_site_id($products_id, $status, $site_id, $up_rs = false) {
    if ($status == '1') {
      return tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_status = '1' where products_id = '" . $products_id . "' and site_id = '".$site_id."'");
    } elseif ($status == '2') {
      return tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_status = '2' where products_id = '" . $products_id . "' and site_id = '".$site_id."'");
    } elseif ($status == '0') {
      return tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_status = '0' where products_id = '" . $products_id . "' and site_id = '".$site_id."'");
    } elseif ($status == '3') {
      if ($up_rs) {
        tep_db_query("UPDATE `".TABLE_REVIEWS."` SET `reviews_status` = '0' where `products_id` = '".$products_id."' and `site_id` = '".$site_id."'"); 
      }
      return tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_status = '3' where products_id = '" . $products_id . "' and site_id = '".$site_id."'");
    } else {
      return -1;
    }
  }

/* -------------------------------------
    功能: 更新指定分类的状态
    参数: $cID(int) 分类id 
    参数: $cstatus(int) 状态id 
    返回值: 无 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 更新指定商品的状态
    参数: $pID(int) 商品id 
    参数: $pstatus(int) 状态id 
    参数: $up_rs(boolean) 是否更新评论 
    返回值: 无 
 ------------------------------------ */
  function tep_set_all_product_status($pID, $pstatus, $up_rs = false)
  {
    $site_arr = array(0); 
    $site_query = tep_db_query("select * from ".TABLE_SITES); 
    while ($site_res = tep_db_fetch_array($site_query)) {
      $site_arr[] = $site_res['id']; 
    }

    foreach ($site_arr as $key => $value) {
      if (!tep_check_products_exists($pID, $value)) {
      }
   
      tep_db_query("UPDATE `".TABLE_PRODUCTS_DESCRIPTION."` SET `products_status` = '".$pstatus."' where `products_id` = '".$pID."' and `site_id` = '".$value."'");  
      if ($up_rs) {
        tep_db_query("UPDATE `".TABLE_REVIEWS."` SET `reviews_status` = '0' where `products_id` = '".$pID."' and `site_id` = '".$value."'"); 
      }
    }
  }

/* -------------------------------------
    功能: 更新指定分类以及其关联商品和分类的状态
    参数: $cID(int) 分类id 
    参数: $cstatus(int) 状态id 
    参数: $site_id(int) 网站id 
    参数: $up_rs(boolean) 是否更新评论 
    返回值: 无 
 ------------------------------------ */
  function tep_set_category_link_product_status($cID, $cstatus, $site_id, $up_rs = false)
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
        tep_set_categories_status_by_site_id($cvalue, $cstatus, $svalue);
      }

      foreach ($product_total_arr as $pkey => $pvalue) {
        tep_set_product_status_by_site_id($pvalue, $pstatus, $svalue, $up_rs); 
      }
    }
  }

/* -------------------------------------
    功能: 获取该分类关联的商品id
    参数: $category_id(int) 分类id 
    返回值: 关联的商品(array) 
 ------------------------------------ */
  function tep_get_link_product_id_by_category_id($category_id)
  {
    $product_arr = array(); 
    $pro_to_ca_query = tep_db_query("select * from ".TABLE_PRODUCTS_TO_CATEGORIES." where categories_id = '".$category_id."'");
    while ($pro_to_ca_res = tep_db_fetch_array($pro_to_ca_query)) {
      $product_arr[] = $pro_to_ca_res['products_id']; 
    }
    return $product_arr;
  }

/* -------------------------------------
    功能: 替换字符串的网站名
    参数: $str(string) 字符串 
    参数: $product_id(int) 商品id 
    参数: $site_id(int) 网站id 
    返回值: 替换后的字符串(string) 
 ------------------------------------ */
  function replace_store_name($str,$product_id,$site_id) {
    $name =  tep_get_site_name_by_id($site_id);
    if($site_id!=0){
      return str_replace('#STORE_NAME#', $name, $str);
    }
    return $str;
  }

/* -------------------------------------
    功能: 根据指定商品取得提醒商品
    参数: $pid(int) 商品id 
    参数: $tid(array) 标签id 
    参数: $buyflag(int) 买取标识 
    返回值: 提醒商品(array) 
 ------------------------------------ */
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
      $query = tep_db_query($raw);
    $arr = array();
    while($p = tep_db_fetch_array($query)){
      $arr[] = $p['products_id'];
    }
    return $arr;
  }

/* -------------------------------------
    功能: 判断顾客是否是仕入
    参数: $cid(int) 顾客id 
    返回值: 是否是仕入(boolean) 
 ------------------------------------ */
  function tep_is_oroshi($cid){
    $query = tep_db_query("select customers_guest_chk from ".TABLE_CUSTOMERS." where customers_id='".$cid."'");
    $c = tep_db_fetch_array($query);
    return $c['customers_guest_chk'] == 9;
  }

/* -------------------------------------
    功能: 获取特殊处理的时间信息
    参数: 无 
    返回值: 时间信息(array) 
 ------------------------------------ */
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

/* -------------------------------------
    功能: 计算商品数量
    参数: $real_qty(int) 真实数量 
    参数: $virtual_qty(int) 虚拟数量 
    返回值: 商品数量(int) 
 ------------------------------------ */
  function tep_calc_products_price($real_qty = 0, $virtual_qty = 0){
    if ($real_qty > 0) {
      return $real_qty + $virtual_qty;
    } else {
      return 0;
    }
  }

/* -------------------------------------
    功能: 获取用户对网站的权限量
    参数: $site_permission(string) 权限信息 
    参数: $site_id(int) 网站id 
    返回值: 是否有权限(boolean) 
 ------------------------------------ */
  function  editPermission($site_permission,$site_id,$all_change=false){
    global $ocertify; 
    $edit_p=FALSE;
    $site_arr=array();
    $site_arr=explode(",",$site_permission);//返回权限数组
    if($site_id == ''){
      $site_id = 0;
    }
    if ($ocertify->npermission == 31) {
      return true; 
    }
    if(in_array($site_id,$site_arr)){
      //判断iste_id是否存在于权限数组中
      $edit_p=true;//true 说明有管理权限 可以在点击新闻时进行修改 
    }else if($ocertify->npermission == 15){
      //判断 管理员 可以修改全部(all)
      if($all_change){
        $edit_p=false;
      }else{
        $edit_p=true;
      }
    }
    return $edit_p;
  }

/* -------------------------------------
    功能: 获取设置信息的网站结果集
    参数: $id(int) 设置id 
    返回值: 设置信息的网站结果集(array) 
 ------------------------------------ */
  function tep_get_conf_sid_by_id($id){
    return tep_db_fetch_array(tep_db_query("select  site_id  from " . TABLE_CONFIGURATION. " where configuration_id = '".$id."'"));
  }

/* -------------------------------------
    功能: 获取评论信息的网站结果集
    参数: $id(int) 评论id 
    返回值: 评论信息的网站结果集(array) 
 ------------------------------------ */
  function tep_get_rev_sid_by_id($id){
    return tep_db_fetch_array(tep_db_query("select  site_id  from " . TABLE_REVIEWS. " where reviews_id = '".$id."'"));
  }

/* -------------------------------------
    功能: 根据规则生成密码
    参数: $rule(string) 规则 
    返回值: 生成密码(string) 
 ------------------------------------ */
  function make_rand_pwd($rule){
    //分割 规则字符串
    $rule = str_replace('M','m',$rule);
    $rule = str_replace('D','d',$rule);
    $arr = explode(':',$rule);
    $str ='';
    //定义一个数字 用来存储时间
    $date_arr = array();
    $date_arr['Y'] = date('Y');
    $date_arr['y'] = date('y');
    $date_arr['m'] = date('m');
    $date_arr['n'] = date('n');
    $date_arr['d'] = date('d');
    $date_arr['j'] = date('j');
    $arr_match = array('Y','y','m','n','d','j');
    if(is_array($arr)&&count($arr)>1){
      //获得 密码长度
      $pwd_len = $arr[0];

      if(!preg_match('|[\+\-\*\/mndjYy]+|',$arr[1])){
        // no +-*/ return string
        $str = $arr[1];
        $str_len = strlen($str); 
        if($pwd_len-$str_len>0){
          for($i=$pwd_len-$str_len;$i>0;$i--){
            $str = '0'.$str;
          }
        }else{
          $str = substr($str,$pwd_len*-1);
        }
        if(!$str){
          return false;
        }
        return $str;
      }

      //获得计算字符串
      $str = $arr[1];
      foreach($arr_match as $value){
        if(preg_match('|'.$value.'|',$str)){
          $str = preg_replace('|'.$value.'|',$date_arr[$value],$str);
        }
      }
      if(preg_match('|[\+\-\=\/]|',$str)){
        //如果存在符号 计算
        $s = '$sr = $str';
        eval('$sr = '.$str.';');
        $str = $sr;
        $str = intval($str);
      }
      //判断长度 不足的时候前补0
      $str_len = strlen($str); 
      if($pwd_len-$str_len>0){
        for($i=$pwd_len-$str_len;$i>0;$i--){
          $str = '0'.$str;
        }
      }else{
        $str = substr($str,$pwd_len*-1);
      }
    }
    if(!$str){
      $str = false;
    }

    return $str;
  }

/* -------------------------------------
    功能: 获取letter信息
    参数: $userid(int) 用户id 
    返回值: letter信息(string/boolean) 
 ------------------------------------ */
  function tep_rand_pw_start($userid){
    $sql = "select letter from ".TABLE_LETTERS.
      " where userid='".$userid."' limit 1";
    $res = tep_db_query($sql);
    if($row = tep_db_fetch_array($res)){
      return $row['letter'];
    }else{
      return false; 
    }

  }

/* -------------------------------------
    功能: 获取letter信息的下拉列表
    参数: $userid(int) 用户id 
    参数: $is_letter(boolean) 是否默认选择 
    参数: $param_str(string) 参数 
    返回值: letter信息的下拉列表(string) 
 ------------------------------------ */
  function tep_show_pw_start($userid='',$is_letter=false,$param_str=''){
    $res_str = "<select name='letter' id='letter'".$param_str.">";
    if($userid!=''){
      if($is_letter){
        $selected = $is_letter;
      }else{
        $selected = tep_rand_pw_start($userid);
      }
    }else{
      $selected = 'first';
    }
    $sql = "select * from ".TABLE_LETTERS." WHERE userid IS NULL
      OR userid = '' OR userid = '".$userid."'";
    $res = tep_db_query($sql);
    while($row = tep_db_fetch_array($res)){
      $res_str .= "<option value ='".$row['letter']."' ";
      if($selected == 'first'){
        $res_str .= "SELECTED";
        $selected = false;
      }else if($selected == $row['letter']){
        $res_str .= "SELECTED";
      }
      $res_str .= " >".$row['letter']."</option>";
    }
    $res_str .= "</select>";
    return $res_str;
  }

/* -------------------------------------
    功能: 获取国家id
    参数: $country_name(string) 国家名字 
    返回值: 国家id(int) 
 ------------------------------------ */
  function tep_get_country_id($country_name) {
    $country_id_query = tep_db_query("select * from " . TABLE_COUNTRIES . " where countries_name = '" . $country_name . "'");
    if (!tep_db_num_rows($country_id_query)) {
      return 0;
    }
    else {
      $country_id_row = tep_db_fetch_array($country_id_query);
      return $country_id_row['countries_id'];
    }
  }

/* -------------------------------------
    功能: 获取国家iso_code
    参数: $country_id(int) 国家id 
    返回值: 国家iso_code(int) 
 ------------------------------------ */
  function tep_get_country_iso_code_2($country_id) {
    $country_iso_query = tep_db_query("select * from " . TABLE_COUNTRIES . " where countries_id = '" . $country_id . "'");
    if (!tep_db_num_rows($country_iso_query)) {
      return 0;
    }
    else {
      $country_iso_row = tep_db_fetch_array($country_iso_query);
      return $country_iso_row['countries_iso_code_2'];
    }
  }

/* -------------------------------------
    功能: 获取区域id
    参数: $country_id(int) 国家id 
    参数: $zone_name(string) 区域名字 
    返回值: 区域id(int) 
 ------------------------------------ */
  function tep_get_zone_id($country_id, $zone_name) {
    $zone_id_query = tep_db_query("select * from " . TABLE_ZONES . " where zone_country_id = '" . $country_id . "' and zone_name = '" . $zone_name . "'");
    if (!tep_db_num_rows($zone_id_query)) {
      return 0;
    }
    else {
      $zone_id_row = tep_db_fetch_array($zone_id_query);
      return $zone_id_row['zone_id'];
    }
  }

/* -------------------------------------
    功能: 字段是否存在
    参数: $table(string) 表名 
    参数: $filed(string) 字段名 
    返回值: 是否存在(boolean) 
 ------------------------------------ */
  function tep_field_exists($table,$field) {
    $describe_query = tep_db_query("describe $table");
    while($d_row = tep_db_fetch_array($describe_query))
    {
      if ($d_row["Field"] == "$field")
        return true;
    }
    return false;
  }

/* -------------------------------------
    功能: 替换单引号
    参数: $string(string) 字符串 
    返回值: 替换后的字符串(string) 
 ------------------------------------ */
  function tep_html_quotes($string) {
    return str_replace("'", "&#39;", $string);
  }

/* -------------------------------------
    功能: 替换指定字符为单引号
    参数: $string(string) 字符串 
    返回值: 替换后的字符串(string) 
 ------------------------------------ */
  function tep_html_unquote($string) {
    return str_replace("&#39;", "'", $string);
  }

/* -------------------------------------
    功能: 格式化输入日期
    参数: $string(string) 日期 
    返回值: 处理后的日期(string) 
 ------------------------------------ */
  function str_string($string='') {
    if(ereg("-", $string)) {
      $string_array = explode("-", $string);
      return $string_array[0] . '年' . $string_array[1] . '月' . $string_array[2] . '日';
    }
  }

/* -------------------------------------
    功能: 获取规则
    参数: 无 
    返回值: 规则(string) 
 ------------------------------------ */
  function get_rule()
  {
    if(isset($_POST['config_rules'])&&$_POST['config_rules']!=''){
      return $_POST['config_rules'];
    }else{
      $sql = "select configuration_value from configuration
        where configuration_key = 'CONFIG_RULES_KEY'";
      $query = tep_db_query($sql);
      if($row = tep_db_fetch_array($query)){
        return $row['configuration_value'];
      }else{
        return '';
      }
    }
  }

/* -------------------------------------
    功能: 舍去.00
    参数: $num(string) 数 
    返回值: 处理后的数(float) 
 ------------------------------------ */
  function tep_display_currency($num){
    $num = str_replace(',','',$num);
    $arr = $arr2 = array();
    for($i=0;$i<10;$i++) {
      $arr[] = '.'.(string)$i.'0';
      if ($i == 0) 
        $arr2[] = '';
      else 
        $arr2[] = '.'.(string)$i;
    }
    return (float)str_replace($arr,$arr2,(string)$num);
  }

/* -------------------------------------
    功能: 处理货币文本
    参数: $txt(string) 信息 
    返回值: 处理后的结果(string) 
 ------------------------------------ */
  function tep_insert_currency_text($txt){
    $arr = $arr2 = array();
    for($i=0;$i<10;$i++) {
      $arr[] = '.'.(string)$i.'0';
      if ($i == 0) 
        $arr2[] = '';
      else 
        $arr2[] = '.'.(string)$i;
    }
    return str_replace($arr,$arr2,$txt);
  }

/* -------------------------------------
    功能: 处理货币值
    参数: $num(int) 值 
    返回值: 处理后的结果(float) 
 ------------------------------------ */
  function tep_insert_currency_value($num){

    $arr = $arr2 = array();
    for($i=0;$i<10;$i++) {
      $arr[] = '.'.(string)$i.'0';
      if ($i == 0) 
        $arr2[] = '';
      else 
        $arr2[] = '.'.(string)$i;
    }
    return (float)str_replace($arr,$arr2,(string)$num);
  }

/* -------------------------------------
    功能: 获取该商品关联的订单的取引終了的状态的商品个数的总和
    参数: $pid(int) 商品id 
    参数: $site_id(int) 网站id 
    参数: $order_status_info(array) 订单状态信息
    返回值: 注文数(int) 
 ------------------------------------ */
  function tep_get_order_cnt_by_pid($pid, $site_id = '',$orders_query_str,$orders_query_num,$order_status_info=array()){
    $query_str = ''; 
    
    if(!empty($site_id) && $site_id != 0){ 
      if($orders_query_num != ''){

        $query_str = " and date_format(orders.date_purchased,'%Y-%m-%d %H:%i:%s') >= '".date('Y-m-d H:i:s',strtotime('-'.$orders_query_num.' minutes'))."'";
      }
    }else{

      $query_str = ' and (';
      $query_str .= $orders_query_str; 
      $query_str = substr($query_str,0,-4);
      $query_str .= ')';
    }
    $query = (tep_db_query("select
          orders_products.products_quantity as pq 
          ,finished,flag_qaf,orders_status 
          from orders_products left join
          orders on orders.orders_id=orders_products.orders_id 
          where products_id='".$pid."'".$query_str.(!empty($site_id)?" and orders.site_id = '".$site_id."'":"").""));
    $cnt = 0;
    while($row = tep_db_fetch_array($query)){
      if($row['finished']=='0'&&$row['flag_qaf']=='0'){
        if(!empty($order_status_info)&&!$order_status_info[$row['orders_status']]){
          $cnt += $row['pq'];
        } else if(!check_order_transaction_button($row['orders_status'])){
          $cnt += $row['pq'];
        }
      }
    }
    return $cnt;
  }

/* -------------------------------------
    功能: 获取指定用户信息
    参数: $s_user_ID(int) 用户id 
    返回值: 用户信息(array) 
 ------------------------------------ */
  function tep_get_user_info($s_user_ID = "") {

    $s_select = "select * from " . TABLE_USERS;
    $s_select .= ($s_user_ID == "" ? "" : " where userid = '$s_user_ID'");
    $s_select .= " order by userid;";     // ユーザＩＤの順番にデータを取得する
    $query = tep_db_query($s_select);
    $res = tep_db_fetch_array($query);
    return $res;

  }

/* -------------------------------------
    功能: 生成网站id的下拉列表
    参数: $parameters(string) 参数 
    参数: $selected(int) 默认选择 
    返回值: 下拉列表(string) 
 ------------------------------------ */
  function tep_site_pull_down($parameters, $selected = '') {
    $select_string = '<select ' . $parameters . '>';
    $sites_query = tep_db_query("select id, romaji from " . TABLE_SITES . " order by
        id");
    if($selected == ''||$selected == '0'){
      $select_string .= '<option value="0"';
      $select_string .= ' SELECTED';
    }else{
      $select_string .= '<option value="0"';
    }
    $select_string .= '>All</option>';
    while ($sites = tep_db_fetch_array($sites_query)) {
      $select_string .= '<option value="' . $sites['id'] . '"';
      if ($selected!=''&&$selected == $sites['id']){
        $select_string .= ' SELECTED';
      }
      $select_string .= '>' . $sites['romaji'] . '</option>';
    }
    $select_string .= '</select>';

    return $select_string;
  }

/* -------------------------------------
    功能: 根据规则生成随机字符
    参数: $pattern(string) 规则 
    参数: $length(int) 长度 
    返回值: 随机字符(string) 
 ------------------------------------ */
  function tep_get_new_random($pattern,$length) 
  {
    global $lower_alpha_arr; 
    global $upper_alpha_arr; 
    global $number_arr; 

    $random_str = ''; 
    $length_arr = explode(',',$length);
    if(count($length_arr)>1){
      $random_len_min = $length_arr[0];
      $random_len_max = $length_arr[1];
    }else{
      $random_len_min = $length_arr[0];
      $random_len_max = $length_arr[0];
    }

    if ($pattern) {
      $mixed_arr = explode(',', $pattern);
      $pattern_arr = array(); 
      foreach ($mixed_arr as $mix_key => $mix_value) {
        switch ($mix_value) {
          case 'english':
            $pattern_arr = array_merge($pattern_arr, $lower_alpha_arr); 
            break;
          case 'ENGLISH':
            $pattern_arr = array_merge($pattern_arr, $upper_alpha_arr); 
            break;
          case 'NUMBER':
            $pattern_arr = array_merge($pattern_arr, $number_arr); 
            break;
        }
      }
      $random_str = tep_get_range_random($pattern_arr, $random_len_min, $random_len_max); 
    }
    return $random_str; 
  }

/* -------------------------------------
    功能: 根据规则生成范围内的随机字符
    参数: $pattern(string) 规则 
    参数: $min_num(int) 最小值 
    参数: $max_num(int) 最大值 
    返回值: 随机字符(string) 
 ------------------------------------ */
  function tep_get_range_random($pattern, $min_num, $max_num) 
  {
    $return_str = ''; 
    shuffle($pattern);
    if ($min_num == $max_num) {
      if ($min_num != '' && $max_num != '') {
        $random_arr = array_splice($pattern, 0, $min_num); 
      }
    } else {
      $random_range_arr = array(); 
      for($i=$min_num; $i<=$max_num; $i++) {
        $random_range_arr[] = $i; 
      }
      shuffle($random_range_arr); 
      $random_arr = array_splice($pattern, 0, $random_range_arr[0]); 
    }
    if (!empty($random_arr)) {
      foreach ($random_arr as $key => $value) {
        $return_str .= $value; 
      }
    }
    return $return_str;
  }

/* -------------------------------------
    功能: 获取密码规则
    参数: 无 
    返回值: 规则(string) 
 ------------------------------------ */
  function tep_get_pwd_pattern(){
    $sql = "select configuration_value as pattern from ".TABLE_CONFIGURATION." WHERE
      configuration_key = 'IDPW_PASSWORD_ITEM'";
    $query = tep_db_query($sql);
    if($row = tep_db_fetch_array($query)){
      return $row['pattern'];
    }else{
      return 'english';
    }
  }

/* -------------------------------------
    功能: 获取idpw密码长度
    参数: 无 
    返回值: 长度(string) 
 ------------------------------------ */
  function tep_get_pwd_len(){
    $sql = "select configuration_value as len from ".TABLE_CONFIGURATION." WHERE
      configuration_key = 'IDPW_PASSWORD_LENGTH'";
    $query = tep_db_query($sql);
    if($row = tep_db_fetch_array($query)){
      return $row['len'];
    }else{
      return '6';
    }
  }

/* -------------------------------------
    功能: pw_manager是否能编辑
    参数: $pwid(int) idpw的值 
    参数: $self(string) 信息 
    参数: $permission(int) 权限值 
    返回值: 是否编辑(boolean) 
 ------------------------------------ */
  function tep_can_edit_pw_manager($pwid,$self,$permission){
    global $ocertify; 
    if($ocertify->npermission=='7'){
      $sql = "select * from ".TABLE_IDPW." where 
        (
         (privilege<='".$permission."' and id = '".$pwid."' and self='') or 
         (id = '".$pwid."' and self='".$self."') 
        ) and onoff ='1' 
        order by id desc limit 1";
    }else if($ocertify->npermission=='10'){
      $sql = "select * from ".TABLE_IDPW." where 
        (
         (privilege<='".$permission."' and id = '".$pwid."' and self='') or 
         (id = '".$pwid."' and self='".$self."') 
        ) and onoff ='1' 
        order by id desc limit 1";
    }else {
      return true;
    }
    $query = tep_db_query($sql);
    if($row = tep_db_fetch_array($query)){
      return true;
    }else{
      return false;
    }
  }

/* -------------------------------------
    功能: 生成idpw的url
    参数: $url(string) url网址 
    参数: $page_url(string) 页面 
    返回值: url地址(string) 
 ------------------------------------ */
  function make_blank_url($url,$page_url){
    $sql = "select configuration_value as url from ".TABLE_CONFIGURATION." WHERE
      configuration_key = 'IDPW_START_URL'";
    $query = tep_db_query($sql);
    if($row = tep_db_fetch_array($query)){
      $d_url = urlencode($url);
      $url = $row['url'].''.$page_url.'?url='.$d_url;
      return $url;
    }else{
      return null;
    }
  }

/* -------------------------------------
    功能: 指定idpw的信息
    参数: $idpw(int) id值 
    参数: $from(string) 字段值 
    返回值: idpw的信息(array/boolean) 
 ------------------------------------ */
  function tep_get_pwm_info($idpw,$from=''){
    $sql = "select * from ".TABLE_IDPW." where id = '".$idpw."'";
    $query = tep_db_query($sql);
    if($row = tep_db_fetch_array($query)){
      if($from){
        return $row[$from];
      }else{
        return $row;
      }
    }else{
      return false;
    }

  }

/* -------------------------------------
    功能: 生成用户的下拉列表
    参数: $selected(int) 默认值 
    参数: $select_name(string) 名 
    返回值: 用户的下拉列表(string) 
 ------------------------------------ */
  function tep_get_user_select($selected='',$select_name=''){
    $sql = "select * from ".TABLE_PERMISSIONS." where permission != '31'";
    $query = tep_db_query($sql);
    $select_str = '';
    if($select_name==''){
      $select_str .= '<select name ="user_self" >'."\r\n";
    }else{
      $select_str .= '<select name ="'.$select_name.'" >'."\r\n";
    }
    while($row = tep_db_fetch_array($query)){
      $user_info = tep_get_user_info($row['userid']);
      $select_str .= '<option value='.$row['userid'];
      if($row['userid'] == $selected){
        $select_str .= ' SELECTED ';
      }
      $select_str .= '>'.$user_info['name'].'</option>'."\r\n";
    }
    $select_str .= "</select>\r\n";
    return $select_str;
  }

/* -------------------------------------
    功能: 商品信息的平均值
    参数: $pid(int) 商品id 
    返回值: 平均值(float) 
 ------------------------------------ */
  function tep_get_avg_by_pid($pid){
    /*
EX:

2011-03-25 18:29:07     1个      -18000元     --
2011-03-25 19:16:33     10个    -100元     --
2011-03-25 18:30:36    20个     -200元     --

实在库 n = 12
算法2:
avg =  ((-18000  * 1 ) + (-100 * 10 ) ) / ( 10 +1)  = 1727.27

f(n) = (-18000 * 1 + -100 * 10 + -200 * (12-1-10)) / 12 =-1600

f(n) = (11 * avg  +  (12-1-10)*-200) /12  = -1600

-1600 * 12 = -19 200
     */
    $product = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." where products_id='".$pid."'"));
    $product_quantity = tep_get_quantity($pid);
    $p_radices = tep_get_radices($pid);
    $order_history_query = tep_db_query("
        select * 
        from ".TABLE_ORDERS_PRODUCTS." op left join ".TABLE_ORDERS." o on op.orders_id=o.orders_id left join ".TABLE_ORDERS_STATUS." os on o.orders_status=os.orders_status_id 
        where 
        op.products_id='".$product['relate_products_id']."'
        and os.calc_price = '1'
        order by o.torihiki_date desc
        ");
    $sum = 0;
    $cnt = 0;
    if(isset($p_radices)&&$p_radices!=''&&$p_radices!=0){
      $product_quantity = $product_quantity*$p_radices;
    }
    while($h = tep_db_fetch_array($order_history_query)){
      if(isset($h['products_rate'])&&$h['products_rate']!=''&&$h['products_rate']!=0){
        $h_pq = $h['products_quantity']*$h['products_rate'];
        $h_fp = $h['final_price']/$h['products_rate'];
      }else{
        if(isset($p_radices)&&$p_radices!=''&&$p_radices!=0){
          $h_pq = $h['products_quantity']*$p_radices;
          $h_fp = $h['final_price']/$p_radices;
        }else{
          $h_pq = $h['products_quantity'];
          $h_fp = $h['final_price'];
        }
      }
      if ($cnt + $h_pq > $product_quantity) {
        $sum += ($product_quantity - $cnt) * abs($h_fp);
        $cnt = $product_quantity;
        break;
      } else {
        $sum += $h_pq * abs($h_fp);
        $cnt += $h_pq;
      }
    }
    if(isset($p_radices)&&$p_radices!=''&&$p_radices!=0){
      return $sum/$cnt*$p_radices;
    }else{
      return $sum/$cnt;
    }
  }

/* -------------------------------------
    功能: 格式化输出价格
    参数: $number(float) 数值 
    返回值: 处理后的价格(string) 
 ------------------------------------ */
  function display_price($number){
    $format_string = number_format($number,2);
    $arr = $arr2 = array();
    for($i=0;$i<10;$i++) {
      $arr[] = '.'.(string)$i.'0';
      if ($i == 0) 
        $arr2[] = '';
      else 
        $arr2[] = '.'.(string)$i;
    }
    return str_replace($arr,$arr2,$format_string);
  }

/* -------------------------------------
    功能: 生成商品页的头部的前页/后页的链接
    参数: $cPath(string) 分类路径 
    参数: $pID(int) 商品id 
    参数: $language_id(int) 语言id 
    参数: $site_id(int) 网站id 
    参数: $td_flag(boolean) 是否特殊输出 
    返回值: 商品页的链接(string) 
 ------------------------------------ */
  function display_product_link($cPath, $pID, $language_id = '4',
      $site_id,$td_flag=false)
  {
    $return_str = ''; 
    $cpath_arr = explode('_', $cPath);
    $category_id = $cpath_arr[count($cpath_arr)-1];
    $product_arr = array();

    $products_query = tep_db_query("select * from (select p.products_id, p.sort_order,
      pd.site_id , pd.products_name from ".TABLE_PRODUCTS." p,
      ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c where
        p.products_id = pd.products_id and pd.language_id = '".$language_id."' and
        p.products_id = p2c.products_id and p2c.categories_id = '".$category_id."'
        order by site_id DESC) c where site_id = ".$site_id." or site_id = 0 group by products_id order by sort_order, products_name, products_id");

          if($td_flag){
          $return_str .= "</td><td class='smallText' align='right' width='240'>";
          }
    while ($products_res = tep_db_fetch_array($products_query)) {
      $product_arr[] = $products_res['products_id']; 
    }
    if (!empty($product_arr)) {
      $cur_key = array_search($pID, $product_arr);
      if ($cur_key !== false) {
      	  if($td_flag){
              $return_str .= '<div style="float:left;width:120px">&nbsp;';
          }
        if (isset($product_arr[$cur_key-1])) {
          $return_str .= '<input type="button" value="'.TEXT_CATEGORY_HEAD_IMAGE_BACK.'" onclick="window.location.href=\''.tep_href_link(FILENAME_CATEGORIES, tep_get_all_get_params(array('page', 'x', 'y', 'pID')).'pID='.$product_arr[$cur_key-1]).'\'">&nbsp;'; 
        }
        if($td_flag){
              $return_str .= '</div>';
              $return_str .= '<div style="float:left;width:120px">&nbsp;';
        }
        if (isset($product_arr[$cur_key+1])) {
          $return_str .= '&nbsp;<input type="button" value="'.TEXT_CATEGORY_HEAD_IMAGE_NEXT.'" onclick="window.location.href=\''.tep_href_link(FILENAME_CATEGORIES, tep_get_all_get_params(array('page', 'x', 'y', 'pID')).'pID='.$product_arr[$cur_key+1]).'\'">&nbsp;'; 
        }
        if($td_flag){
              $return_str .= '</div>';
        }
      }
    }
    return $return_str;
  }

/* -------------------------------------
    功能: 生成分类页的头部的前页/后页的链接
    参数: $cPath(string) 分类路径 
    参数: $current_category_id(int) 分类id 
    参数: $language_id(int) 语言id 
    参数: $site_id(int) 网站id 
    参数: $page(string) 页面名 
    参数: $td_flag(boolean) 是否特殊输出 
    返回值: 分类页的链接(string) 
 ------------------------------------ */
  function display_category_link($cPath, $current_category_id, $language_id = 4,
      $site_id, $page = FILENAME_CATEGORIES,$td_flag=false)
  {
    $return_str = ''; 
    $level_category_arr = array();
    $cpath_arr = explode('_', $cPath);
    $tmp_ca_id = $current_category_id;

    $children_ca_query = tep_db_query("select * from ".TABLE_CATEGORIES." where parent_id = '".$current_category_id."' limit 1"); 
    $children_ca_res = tep_db_fetch_array($children_ca_query);
    if ($children_ca_res) {
      $current_category_id = $children_ca_res['categories_id']; 
    } else {
      $current_category_id = 0; 
    }
    $current_category_query = tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id = '".$current_category_id."'"); 
    $current_category_res = tep_db_fetch_array($current_category_query); 

    if ($current_category_res) {
      $parent_category_query = tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id = '".$current_category_res['parent_id']."'"); 
      $parent_category_res = tep_db_fetch_array($parent_category_query); 
      if ($parent_category_res) {
        if ($parent_category_res['parent_id'] == 0) {
          $level_category_id = 0; 
        } else {
          $level_category_id = $parent_category_res['parent_id']; 
        }
        $level_category_query = tep_db_query("select * from (select c.categories_id, cd.site_id, cd.categories_name, c.sort_order from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.parent_id = ".$level_category_id." and c.categories_id = cd.categories_id and cd.language_id = '".$language_id."' order by site_id DESC) c where site_id = ".(int)$site_id." or site_id = 0 group by categories_id order by sort_order, categories_name");   

        while ($level_category_res = tep_db_fetch_array($level_category_query)) {
          $level_category_arr[] = $level_category_res['categories_id'];  
        }
        if (!empty($level_category_arr)) {
          $cur_key = array_search($parent_category_res['categories_id'], $level_category_arr); 
          $show_ca_query = tep_db_query("select * from (select c.categories_id ,cd.site_id, cd.categories_name from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id = cd.categories_id and c.categories_id = '".$tmp_ca_id."' and cd.language_id = '".$language_id."' order by site_id DESC) c where site_id = '0' or site_id = '".$site_id."'group by categories_id limit 1"); 
          $show_ca_res = tep_db_fetch_array($show_ca_query);

          if($td_flag){
          $return_str .= "<td class='smallText' align='right'>";
          }
          if ($cur_key !== false) {
            if (isset($level_category_arr[$cur_key-1])) {
              $prev_id =  $level_category_arr[$cur_key-1];
              $link_cpath = get_link_parent_category($prev_id); 
              if (isset($level_category_arr[$cur_key+1])) {
              $return_str .= '<input type="button" style="width:102px;float:left;margin-left:70px;" value="'.TEXT_CATEGORY_HEAD_IMAGE_BACK.'" onclick="window.location.href=\''.tep_href_link($page, tep_get_all_get_params(array('page', 'x', 'y', 'cPath', 'cID')).'cPath='.$link_cpath).'\'">&nbsp;'; 
	      }else{
                $return_str .= '<input type="button" style="width:102px;float:left;margin-left:70px;" value="'.TEXT_CATEGORY_HEAD_IMAGE_BACK.'" onclick="window.location.href=\''.tep_href_link($page, tep_get_all_get_params(array('page', 'x', 'y', 'cPath', 'cID')).'cPath='.$link_cpath).'\'">&nbsp;'; 
	      }
            }

            if (isset($level_category_arr[$cur_key+1])) {
              $next_id =  $level_category_arr[$cur_key+1];
              $link_cpath = get_link_parent_category($next_id); 
              if (isset($level_category_arr[$cur_key-1])) {
              $return_str .= '&nbsp;<input type="button" style="width:102px;float:left;margin-left:10px;" value="'.TEXT_CATEGORY_HEAD_IMAGE_NEXT.'" onclick="window.location.href=\''.tep_href_link($page, tep_get_all_get_params(array('page', 'x', 'y', 'cPath', 'cID')).'cPath='.$link_cpath).'\'">&nbsp;'; 
	      }else{
                $return_str .= '&nbsp;<input style="width:102px;float:left;margin-left:182px;" type="button" value="'.TEXT_CATEGORY_HEAD_IMAGE_NEXT.'" onclick="window.location.href=\''.tep_href_link($page, tep_get_all_get_params(array('page', 'x', 'y', 'cPath', 'cID')).'cPath='.$link_cpath).'\'">&nbsp;'; 
	      }
            }
          }

        }
      }

    }

    return $return_str;
  }

/* -------------------------------------
    功能: 根据分类id生成起所关联的分类(用_间隔) 
    参数: $cid(int) 分类id 
    返回值: 关联分类(string) 
 ------------------------------------ */
  function get_link_parent_category($cid)
  {
    $ca_arr = array(); 
    $current_category_query = tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id = '".$cid."'");
    $current_category_res = tep_db_fetch_array($current_category_query); 

    if ($current_category_res) {
      $parent_category_query = tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id = '".$current_category_res['parent_id']."'"); 
      $parent_category_res = tep_db_fetch_array($parent_category_query); 
      if ($parent_category_res) {
        $ca_arr[] = $parent_category_res['categories_id']; 
        $parent_parent_category_query = tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id = '".$parent_category_res['parent_id']."'"); 
        $parent_parent_category_res = tep_db_fetch_array($parent_parent_category_query); 
        if ($parent_parent_category_res) {
          $ca_arr[] = $parent_parent_category_res['categories_id']; 
        } 
      }
    }
    if (!empty($ca_arr)) {
      krsort($ca_arr); 
      return implode('_', $ca_arr).'_'.$cid; 
    }
    return $cid;
  }

/* -------------------------------------
    功能: 生成同级分类的前页/后页链接 
    参数: $cPath(string) 分类路径 
    参数: $current_category_id(int) 分类id 
    参数: $language_id(int) 语言id 
    参数: $site_id(int) 网站id 
    参数: $page(string) 页面名 
    参数: $td_flag(boolean) 是否特殊输出 
    返回值: 前页/后页链接(string) 
 ------------------------------------ */
  function get_same_level_category($cPath, $current_category_id, $language_id,
      $site_id, $page = FILENAME_CATEGORIES,$td_flag=false)
  {
    $return_str = ''; 
    $cpath_arr = explode('_', $cPath);
    $category_arr = array();

    if (count($cpath_arr) > 1) {
      $current_ca_query = tep_db_query("select * from ".TABLE_CATEGORIES." where categories_id = '".$current_category_id."'"); 
      $current_ca_res = tep_db_fetch_array($current_ca_query); 
      if ($current_ca_res) {
        $level_category_query = tep_db_query("select * from (select c.categories_id, cd.site_id, cd.categories_name, c.sort_order from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.parent_id = ".$current_ca_res['parent_id']." and c.categories_id = cd.categories_id and cd.language_id = '".$language_id."' order by site_id DESC) c where site_id = ".(int)$site_id." or site_id = 0 group by categories_id order by sort_order, categories_name");   
        while ($level_category_res = tep_db_fetch_array($level_category_query)) {
          $category_arr[] = $level_category_res['categories_id']; 
        }

        if (!empty($category_arr)) {
          $cur_pos = array_search($current_category_id, $category_arr); 
          $show_ca_query = tep_db_query("select * from (select c.categories_id ,cd.site_id, cd.categories_name from ".TABLE_CATEGORIES." c, ".TABLE_CATEGORIES_DESCRIPTION." cd where c.categories_id =
            cd.categories_id and c.categories_id = '".$current_category_id."' and cd.language_id = '".$language_id."' order by site_id DESC) c where site_id = '0' or site_id = '".$site_id."'group by categories_id limit 1"); 
            $show_ca_res = tep_db_fetch_array($show_ca_query);

          if($td_flag){
          $return_str .= "<td class='smallText' align='right' >";
          }
          if ($cur_pos !== false) {
              $link_path = get_link_parent_category($category_arr[$cur_pos-1]);
            if (isset($category_arr[$cur_pos-1])) {
              if (isset($category_arr[$cur_pos+1])) {
                $return_str .= '<input type="button" style="width:102px;float:left;margin-left:70px;" value="'.TEXT_CATEGORY_HEAD_IMAGE_BACK.'" onclick="window.location.href=\''.tep_href_link($page, 'cPath='.$link_path.'&site_id='.(int)$site_id).'\'">&nbsp;'; 
	      }else{
                $return_str .= '<input type="button" style="width:102px;float:left;margin-left:70px;" value="'.TEXT_CATEGORY_HEAD_IMAGE_BACK.'" onclick="window.location.href=\''.tep_href_link($page, 'cPath='.$link_path.'&site_id='.(int)$site_id).'\'">&nbsp;'; 
	      }
            }
            if (isset($category_arr[$cur_pos+1])) {
              $link_path = get_link_parent_category($category_arr[$cur_pos+1]); 
              if (isset($category_arr[$cur_pos-1])) {
                $return_str .= '&nbsp;<input type="button" style="width:102px;float:left;margin-left:10px;" value="'.TEXT_CATEGORY_HEAD_IMAGE_NEXT.'" onclick="window.location.href=\''.tep_href_link($page, 'cPath='.$link_path.'&site_id='.(int)$site_id).'\'">'; 
	      }else{
                $return_str .= '&nbsp;<input type="button" style="width:102px;float:left;margin-left:182px;" value="'.TEXT_CATEGORY_HEAD_IMAGE_NEXT.'" onclick="window.location.href=\''.tep_href_link($page, 'cPath='.$link_path.'&site_id='.(int)$site_id).'\'">'; 
	      }
            }
          }
        }
      }
    }

    return $return_str;
  }

/* -------------------------------------
    功能: 获取指定网站的结果集 
    参数: $site_id(int) 网站id 
    返回值: 网站的结果集(array) 
 ------------------------------------ */
  function tep_get_site_info($site_id=0){
    if($site_id){
      $sql = "select * from ".TABLE_SITES." where id = '".$site_id."' limit 1";
      $query = tep_db_query($sql);
      $row = tep_db_fetch_array($query);
      return $row;
    }else{
      $arr = array();
      $arr['romaji'] = 'All';
      return $arr;
    }
  }

/* -------------------------------------
    功能: 获取指定idpw_log的结果集 
    参数: $pwid(int) id值 
    返回值: idpw_log的结果集(array/boolean) 
 ------------------------------------ */
  function tep_has_pw_manager_log($pwid){
    if($pwid){
      $sql = "select * from ".TABLE_IDPW_LOG." where idpw_id ='".$pwid."'";
      if($row = tep_db_fetch_array(tep_db_query($sql))){
        return $row;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }

/* -------------------------------------
    功能: 获取订单商品的价格 
    参数: $orders_products_id(int) 订单商品id 
    参数: $type(string) 类型 
    返回值: 订单商品的价格(string/boolean) 
 ------------------------------------ */
  function tep_get_product_by_op_id($orders_products_id,$type=''){
    if($type=='pid'){
      $sql = "select p.products_price as price from ".
        TABLE_ORDERS_PRODUCTS." op,"
        .TABLE_PRODUCTS." p  
        where p.products_id ='".$orders_products_id."' 
        limit 1";
    }else{
      $sql = "select p.products_price as price from ".
        TABLE_ORDERS_PRODUCTS." op,"
        .TABLE_PRODUCTS." p  
        where op.orders_products_id ='".$orders_products_id."' 
        and op.products_id = p.products_id limit 1";
    }
    $res = tep_db_query($sql);
    if($row = tep_db_fetch_array($res)){
      return $row['price'];
    }else{
      return false;
    }
  }

/* -------------------------------------
    功能: 创建一条一次性密码数据 
    参数: $pwd(string) 页面 
    参数: $userid(int) 用户id 
    参数: $save_session(boolean) 是否保存session 
    参数: $page_name(string) 页面 
    参数: $redirect_url_str(string) url 
    返回值: 创建数据(boolean) 
 ------------------------------------ */
  function tep_insert_pwd_log($pwd,$userid,$save_session=false,$page_name='',$redirect_url_str=''){
    if($save_session){
      $_SESSION[$page_name] = $pwd;
    }
    $user_info = tep_get_user_info($userid);
    $letter = substr($pwd,0,1);
    $sql_letter = "select * from ".TABLE_LETTERS." 
      where letter = '".$letter."'";
    $res_letter = tep_db_query($sql_letter);
    if($row_letter = tep_db_fetch_array($res_letter)){
      $letter_info = tep_get_user_info($row_letter['userid']);
      $sql = "insert into ".TABLE_ONCE_PWD_LOG." VALUES 
        (NULL , '".$user_info['name']."',
         '".$letter_info['name']."', '".(!empty($redirect_url_str)?$redirect_url_str:$_SERVER['HTTP_REFERER'])."',
         CURRENT_TIMESTAMP
        )";
      return tep_db_query($sql);
    }else{
      return false;
    }

  }

/* -------------------------------------
    功能: 判断商品在指定时间内是否卖出 
    参数: $products_id(int) 商品id 
    参数: $limit_time_info(string) 限制时间 
    参数: $limit_orders_num(int) 订单数 
    返回值: 是否卖出(boolean) 
 ------------------------------------ */
  function tep_check_best_sellers_isbuy($products_id, $order_arr , $limit_orders_num)
  {
      $order_product_arr = array();
      $order_product_query = tep_db_query("select orders_id from ".TABLE_ORDERS_PRODUCTS." where products_id = '".$products_id."'");
      while($order_product_row = tep_db_fetch_array($order_product_query)){
        $order_product_arr[] = $order_product_row['orders_id'];
      }
      
      $intersect_order = array_intersect($order_product_arr,$order_arr);
      if(!empty($intersect_order) && count($intersect_order) >= $limit_orders_num){
        return true;
      }
    return false;
  }

/* -------------------------------------
    功能: 获取faq的分类路径 
    参数: $current_category_id(int) 分类id 
    返回值: 分类路径(string) 
 ------------------------------------ */
  function tep_get_faq_path($current_category_id = '') {
    global $cPath_array;

    if ($current_category_id == '') {
      $cPath_new = implode('_', $cPath_array);
    } else {
      if (sizeof($cPath_array) == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
        $last_category_query = tep_db_query("select parent_id from " . TABLE_FAQ_CATEGORIES . " where id = '" . $cPath_array[(sizeof($cPath_array)-1)] . "'");
        $last_category = tep_db_fetch_array($last_category_query);
        $current_category_query = tep_db_query("select parent_id from " . TABLE_FAQ_CATEGORIES . " where id = '" . $current_category_id . "'");
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

/* -------------------------------------
    功能: 计算该商品最近一次被购买的时间 
    参数: $products_id(int) 商品id 
    参数: $single(boolean) 是否设置时间 
    参数: $limit_time_info(string) 限制信息 
    参数: $speed(boolean) 是否排序 
    返回值: 时间(int) 
 ------------------------------------ */
  function tep_calc_limit_time_by_order_id($products_id, $single = false,
      $limit_time_info = '',$speed=false)
  {
    $now_time = time(); 
    
    if ($limit_time_info !== '') {
      if ($limit_time_info) {
        $limit_time = $limit_time_info; 
      } else {
        return ''; 
      }
    }
     
    if ($limit_time == 0) {
      return ''; 
    }

    if ($limit_time == 1) {
      $before_time = strtotime("-".$limit_time." day", $now_time); 
    } else {
      $before_time = strtotime("-".$limit_time." days", $now_time); 
    }

    if ($single) {
      if($speed){
      $order_query = tep_db_query("select o.orders_id, o.date_purchased from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where o.orders_id = op.orders_id and op.products_id = '".$products_id."' limit 1");
      }else{
      $order_query = tep_db_query("select o.orders_id, o.date_purchased from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where o.orders_id = op.orders_id and op.products_id = '".$products_id."' order by orders_id desc limit 1");
      }
    } else {
      if($speed){
      $order_query = tep_db_query("select o.orders_id, o.date_purchased from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where o.orders_id = op.orders_id and op.products_id = '".$products_id."' and o.date_purchased >= '".date('Y-m-d H:i:s', $before_time)."' limit 1");
      }else{
      $order_query = tep_db_query("select o.orders_id, o.date_purchased from ".TABLE_ORDERS." o, ".TABLE_ORDERS_PRODUCTS." op where o.orders_id = op.orders_id and op.products_id = '".$products_id."' and o.date_purchased <= '".date('Y-m-d H:i:s', $now_time)."' and o.date_purchased >= '".date('Y-m-d H:i:s', $before_time)."' order by orders_id desc limit 1");
      }
    }
    $order_res = tep_db_fetch_array($order_query); 
    if($speed&&$order_res){
      return true;
    }

    $diff_time_str = '';
    if ($order_res) {
      $oday_arr = explode(' ', $order_res['date_purchased']); 
      $date_arr = explode('-', $oday_arr[0]); 
      $time_arr = explode(':', $oday_arr[1]); 
      $oday_time = mktime(0, 0, 0, $date_arr[1], $date_arr[2], $date_arr[0]); 
      $now_time_tmp = mktime(0, 0, 0, date('n', $now_time), date('j', $now_time), date('Y', $now_time)); 
      $diff_time_str = ($now_time_tmp - $oday_time)/(60*60*24); 
    }
    return $diff_time_str;
  }

/* -------------------------------------
    功能: 生成固定格式的多选框 
    参数: $check_array(array) 值 
    参数: $key_value(string) 选中的值 
    参数: $key(string) 键值 
    返回值: 生成的多选框(string) 
 ------------------------------------ */
  function tep_cfg_payment_checkbox_option($check_array, $key_value, $key = '') {
    $string = '';
    for ($i = 0, $n = sizeof($check_array); $i < $n; $i++) {
      $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
      $string .= '<br><input type="checkbox" name="' . $name . '[]" value="' .  $check_array[$i] . '"';
      if (in_array($check_array[$i], unserialize($key_value))) $string .= ' CHECKED';
      $string .= '> '; 
      if (($i+1) == 1) {
        $string .= '会員'; 
      } else {
        $string .= 'ゲスト'; 
      }
    }
    return $string;
  }

/* -------------------------------------
    功能: 根据用户名搜索相应的用户信息 
    参数: $name(string) 名字 
    返回值: 用户信息(array) 
 ------------------------------------ */
  function tep_get_user_list_by_username($name){
    $sql = "select userid from ".TABLE_USERS."
      where name like '%".$name."%' ";
    $query = tep_db_query($sql);
    $list = array();
    while($row = tep_db_fetch_array($query)){
      $list[] = $row['userid'];
    }
    return $list;
  }

/* -------------------------------------
    功能: 生成网站列表 
    参数: $filename(string) 网站名 
    参数: $ca_single(boolean) 是否过滤指定参数 
    返回值: 网站列表(array) 
 ------------------------------------ */
  function tep_pw_site_filter($filename, $ca_single = false){
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
                echo tep_href_link($filename,
                    tep_get_all_get_params(array('site_id', 'page', 'pw_id', 'action')));
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
                          echo tep_href_link($filename, tep_get_all_get_params(array('site_id',
                                  'pw_id','action')) . 'site_id=' . $site['id']);
                        }
                      ?>"><?php echo $site['romaji'];?></a></span>
                        <?php }
              }
            ?>
              </div>
              <?php
  }

/* -------------------------------------
    功能: 判断订单类型 
    参数: $oID(string) 订单id 
    返回值: 订单类型(int) 
 ------------------------------------ */
  function tep_check_order_type($oID)
  {
    $sql = "  SELECT avg( products_bflag ) bflag FROM orders_products op, products p  WHERE 1 AND p.products_id = op.products_id AND op.orders_id = '".$oID."'";

    $avg  = tep_db_fetch_array(tep_db_query($sql));
    $avg = $avg['bflag'];

    if($avg == 0){
      //贩卖 
      return 1;
    }
    if($avg == 1){
      //买取 
      return 2;
    }
    //混合 
    return 3;

  }

/* -------------------------------------
    功能: 获取指定订单的支付方法 
    参数: $oID(string) 订单id 
    返回值: 支付方法(string) 
 ------------------------------------ */
  function tep_get_payment_code_by_order_id($oID)
  {
    $orders_raw = tep_db_query("select * from ".TABLE_ORDERS." where orders_id = '".$oID."'");
    $orders_res = tep_db_fetch_array($orders_raw);
    return $orders_res['payment_method'];
  }

/* -------------------------------------
    功能: 随机生成指定字符长度的字符串(用于商品属性元素名) 
    参数: $length(int) 长度 
    返回值: 随机字符串(string) 
 ------------------------------------ */
  function tep_get_random_item_name($length = 16)
  {
    $pattern = 'abcdefghijklmnopqrstuvwxyz';
    while (true) {
      $key = ''; 
      for($i = 0; $i < $length; $i++) {
        $key .= $pattern[mt_rand(0,25)]; 
      }
      $exists_item_name_raw = tep_db_query("select * from ".TABLE_OA_ITEM." where name = '".$key."'"); 
      if (!tep_db_num_rows($exists_item_name_raw)) {
        return $key; 
      }
    }
  }

/* -------------------------------------
    功能: 获取指定分类在各个网站的状态 
    参数: $category_id(int) 分类id 
    返回值: 状态集合(array) 
 ------------------------------------ */
  function get_all_site_category_status($category_id)
  {
    $site_arr = array();
    $site_romaji = array(); 
    $status_arr = array();
    $status_arr['green'] = array();
    $status_arr['blue'] = array();
    $status_arr['red'] = array();
    $status_arr['black'] = array();
    $category_status_site_id = array();
    $category_status = array();

    $site_arr[] = 0; 
    $site_romaji[] = 'all';

    $site_raw = tep_db_query("select * from ".TABLE_SITES." order by id asc");
    while ($site_res = tep_db_fetch_array($site_raw)) {
      $site_arr[] = $site_res['id']; 
      $site_romaji[] = $site_res['romaji']; 
    }
    $category_des_raw = tep_db_query("select site_id,categories_status from ".TABLE_CATEGORIES_DESCRIPTION." where  categories_id = '".$category_id."' order by site_id desc"); 
    while($category_des_res = tep_db_fetch_array($category_des_raw)){
      $category_status[] = $category_des_res['categories_status']; 
      $category_status_site_id[] = $category_des_res['site_id'];
    }
    foreach($category_status_site_id as $c_key => $c_value){
      if($c_value == '0'){
        $default_status = $category_status[$c_key];
        break;
      }
    }
    foreach ($site_arr as $key => $value) {
      if(isset($default_status)&&$default_status!=0){
        $temp_status = $default_status;
      }else{
        $temp_status = 0;
      }
      foreach($category_status_site_id as $c_k => $c_v){
        if($value == $c_v){
          $temp_status = $category_status[$c_k];
        }
      }
      switch ($temp_status) {
        case '2':
          $status_arr['blue'][] = $site_romaji[$key]; 
          break;
        case '1':
          $status_arr['red'][] = $site_romaji[$key]; 
          break;
        case '3':
          $status_arr['black'][] = $site_romaji[$key]; 
          break;
        default:
          $status_arr['green'][] = $site_romaji[$key]; 
          break;
      }
    }
    return $status_arr;
  }

/* -------------------------------------
    功能: 获取指定商品在各个网站的状态 
    参数: $product_id(int) 商品id 
    返回值: 状态集合(array) 
 ------------------------------------ */
  function get_all_site_product_status($product_id)
  {
    global $languages_id;

    $site_arr = array();
    $site_romaji = array(); 
    $status_arr = array();
    $status_arr['green'] = array();
    $status_arr['blue'] = array();
    $status_arr['red'] = array();
    $status_arr['black'] = array();
    $products_status_site_id = array();
    $products_status = array();

    $site_arr[] = 0; 
    $site_romaji[] = 'all';

    $site_raw = tep_db_query("select * from ".TABLE_SITES." order by id asc");
    while ($site_res = tep_db_fetch_array($site_raw)) {
      $site_arr[] = $site_res['id']; 
      $site_romaji[] = $site_res['romaji']; 
    }
    $product_des_raw = tep_db_query("select products_status,site_id from ".TABLE_PRODUCTS_DESCRIPTION." where  products_id = '".$product_id."' and language_id = '".$languages_id."' order by site_id desc "); 
    while($product_des_res = tep_db_fetch_array($product_des_raw)){
      $products_status[] = $product_des_res['products_status'];
      $products_status_site_id[]= $product_des_res['site_id'];
    }
    foreach($products_status_site_id as $p_key => $p_value){
      if($p_value == '0'){
        $default_status = $products_status[$p_key];
        break;
      }
    }

    foreach ($site_arr as $key => $value) {
      if(isset($default_status)&&$default_status!=1){
        $temp_status = $default_status;
      }else{
        $temp_status = 1;
      }
      foreach($products_status_site_id as $p_k => $p_v){
        if($value == $p_v){
          $temp_status = $products_status[$p_k];
        }
      }
      switch ($temp_status) {
        case '2':
          $status_arr['blue'][] = $site_romaji[$key]; 
          break;
        case '0':
          $status_arr['red'][] = $site_romaji[$key]; 
          break;
        case '3':
          $status_arr['black'][] = $site_romaji[$key]; 
          break;
        default:
          $status_arr['green'][] = $site_romaji[$key]; 
          break;
      }
    }

    return $status_arr;
  }

/* -------------------------------------
    功能: 判断faq分类描述在指定网站是否存在 
    参数: $cid(int) 分类id 
    参数: $sid(int) 网站id 
    返回值: 是否存在(boolean) 
 ------------------------------------ */
  function tep_faq_categories_description_exist($cid, $sid){
    $query = tep_db_query("select * from ".TABLE_FAQ_CATEGORIES_DESCRIPTION."
        where faq_category_id='".$cid."' and site_id = '".$sid."'");
    if(tep_db_num_rows($query)) {
      return true;
    } else {
      return false;
    }
  }

/* -------------------------------------
    功能: 判断faq问题描述在指定网站是否存在 
    参数: $qid(int) 问题id 
    参数: $sid(int) 网站id 
    返回值: 是否存在(boolean) 
 ------------------------------------ */
  function tep_faq_question_description_exist($qid, $sid){
    $query = tep_db_query("select * from ".TABLE_FAQ_QUESTION_DESCRIPTION." where
        faq_question_id='".$qid."' and site_id = '".$sid."' ");
    if(tep_db_num_rows($query)) {
      return true;
    } else {
      return false;
    }
  }

/* -------------------------------------
    功能: 获取指定faq分类下的子分类的个数 
    参数: $faq_category_id(int) 分类id 
    返回值: 分类个数(int) 
 ------------------------------------ */
  function tep_childs_in_faq_category_count($faq_category_id) {
    $categories_count = 0;

    $categories_query = tep_db_query("select id from " . TABLE_FAQ_CATEGORIES .
        " where parent_id = '" . $faq_category_id . "'");
    while ($category = tep_db_fetch_array($categories_query)) {
      $categories_count++;
      $categories_count += tep_childs_in_faq_category_count($category['id']);
    }

    return $categories_count;
  }

/* -------------------------------------
    功能: 获取指定faq分类下的问题的个数 
    参数: $category_id(int) 分类id 
    参数: $include_deactivated(boolean) 是否包含非激活 
    返回值: 问题个数(int) 
 ------------------------------------ */
  function tep_question_in_faq_category_count($category_id, $include_deactivated = false) {
    $question_count = 0;

    if ($include_deactivated) {
      $question_query = tep_db_query("select count(*) as total from " .
          TABLE_FAQ_QUESTION . " fq, " . TABLE_FAQ_QUESTION_TO_CATEGORIES . " fq2c
          where fq.id = fq2c.faq_question_id and fq2c.faq_category_id = '" . $category_id . "'");
    } else {
      $question_query = tep_db_query("select count(*) as total from " .  
          TABLE_FAQ_QUESTION . " fq, " . TABLE_FAQ_QUESTION_TO_CATEGORIES . " fq2c
          where fq.id = fq2c.faq_question_id and fq2c.faq_category_id = '" . $category_id . "'");
    }

    $question = tep_db_fetch_array($question_query);

    $question_count += $question['total'];

    $childs_query = tep_db_query("select id from " . TABLE_FAQ_CATEGORIES .
        " where parent_id = '" . $category_id . "'");
    if (tep_db_num_rows($childs_query)) {
      while ($childs = tep_db_fetch_array($childs_query)) {
        $question_count += tep_question_in_faq_category_count($childs['id'], $include_deactivated);
      }
    }

    return $question_count;
  }

/* -------------------------------------
    功能: 获取指定faq分类树 
    参数: $parent_id(int) 父分类 
    参数: $spacing(string) 间隔 
    参数: $exclude(int) 不包含分类 
    参数: $category_tree_array(array) 分类数组 
    参数: $include_itself(boolean) 是否包含自己 
    返回值: 分类树(array) 
 ------------------------------------ */
  function tep_get_faq_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
    global $languages_id;

    if (!is_array($category_tree_array)) $category_tree_array = array();
    if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

    if ($include_itself) {
      $category_query = tep_db_query("select cd.title from " .
          TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd where cd.faq_category_id = '" . $parent_id . "' and cd.site_id='0'");
      $category = tep_db_fetch_array($category_query);
      $category_tree_array[] = array('id' => $parent_id, 'text' => $category['title']);
    }

    $categories_query = tep_db_query("select c.id, cd.title, c.parent_id from " .
        TABLE_FAQ_CATEGORIES . " c, " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd 
        where c.id = cd.faq_category_id and  c.parent_id = '" . $parent_id . "'
        and site_id ='0' order by c.sort_order, cd.title");
    while ($categories = tep_db_fetch_array($categories_query)) {
      if ($exclude != $categories['id']) $category_tree_array[] = array('id' =>
          $categories['id'], 'text' => $spacing . $categories['title']);
      $category_tree_array = tep_get_faq_category_tree($categories['id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
    }

    return $category_tree_array;
  }


/* -------------------------------------
    功能: 删除指定faq分类及其相关信息 
    参数: $category_id(int) 分类id 
    返回值: 无 
 ------------------------------------ */
  function tep_remove_faq_category($category_id) {
    tep_db_query("delete from " . TABLE_FAQ_CATEGORIES . " where id = '" . tep_db_input($category_id) . "'");
    tep_db_query("delete from " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " where faq_category_id = '" . tep_db_input($category_id) . "'");
    tep_db_query("delete from " . TABLE_FAQ_QUESTION_TO_CATEGORIES . " where faq_category_id = '" . tep_db_input($category_id) . "'");
    tep_db_query("delete from `faq_sort` where info_id = '" .  tep_db_input($category_id) . "' and info_type = 'c'");
  }

/* -------------------------------------
    功能: 删除指定faq问题及其相关信息 
    参数: $product_id(int) 问题id 
    返回值: 无 
 ------------------------------------ */
  function tep_remove_faq_question($product_id) {
    tep_db_query("delete from " . TABLE_FAQ_QUESTION . " where id = '" . tep_db_input($product_id) . "'");
    tep_db_query("delete from " . TABLE_FAQ_QUESTION_TO_CATEGORIES . " where faq_question_id = '" . tep_db_input($product_id) . "'");
    tep_db_query("delete from " . TABLE_FAQ_QUESTION_DESCRIPTION . " where faq_question_id = '" . tep_db_input($product_id) . "'");
    tep_db_query("delete from `faq_sort` where info_id = '" .  tep_db_input($product_id) . "' and info_type = 'q'");
  }

/* -------------------------------------
    功能: 指定订单状态更新 
    参数: $oID(string) 订单id 
    参数: $status(int) 状态id 
    返回值: 无 
 ------------------------------------ */
function   tep_order_status_change($oID,$status){
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
    功能: 更新指定faq分类在指定网站的状态 
    参数: $faq_category_id(int) 分类id 
    参数: $status(int) 状态id 
    参数: $site_id(int) 网站id 
    返回值: 更新成功(boolean) 
 ------------------------------------ */
  function tep_set_faq_category_status_by_site_id($faq_category_id, $status, $site_id)
  {
    tep_db_query("UPDATE `".TABLE_FAQ_CATEGORIES_DESCRIPTION."` SET `is_show` =
        '".intval($status)."' WHERE `faq_category_id` =".$faq_category_id.
        " and `site_id` = '".$site_id."' LIMIT 1 ;");
    tep_db_query("UPDATE `".'faq_sort'."` SET `is_show` =
        '".intval($status)."' WHERE `info_id` =".$faq_category_id.
        " and `site_id` = '".$site_id."' LIMIT 1 ;");
    return true;
  }

/* -------------------------------------
    功能: 更新faq指定分类的关联问题的状态 
    参数: $cID(int) 分类id 
    参数: $cstatus(int) 状态id 
    参数: $site_id(int) 网站id 
    返回值: 无 
 ------------------------------------ */
  function tep_set_faq_category_link_question_status($cID, $cstatus, $site_id)
  {
    $site_arr = array(); 
    $product_total_arr = array();
    $category_total_arr = array($cID); 

    $pstatus = $cstatus;

    if ($site_id == 0) {
      $site_arr = array($site_id); 
    } else {
      $site_arr = array($site_id); 
    }

    $product_arr = tep_get_link_question_id_by_category_id($cID);
    if (!empty($product_arr)) {
      $product_total_arr = array_merge($product_total_arr, $product_arr); 
    }

    $child_category_query = tep_db_query("select * from ".TABLE_FAQ_CATEGORIES." where parent_id = '".$cID."'");
    while ($child_category_res = tep_db_fetch_array($child_category_query)) {
      $category_total_arr[] = $child_category_res['id']; 
      $product_arr = tep_get_link_question_id_by_category_id($child_category_res['id']);
      if (!empty($product_arr)) {
        $product_total_arr = array_merge($product_total_arr, $product_arr); 
      }
      $child_child_category_query = tep_db_query("select * from ".TABLE_FAQ_CATEGORIES." where parent_id = '".$child_category_res['id']."'");

      while ($child_child_category_res = tep_db_fetch_array($child_child_category_query)) {
        $category_total_arr[] = $child_child_category_res['id']; 
        $product_arr = tep_get_link_question_id_by_category_id($child_child_category_res['id']);
        if (!empty($product_arr)) {
          $product_total_arr = array_merge($product_total_arr, $product_arr); 
        }
      }
    }

    foreach ($site_arr as $skey => $svalue) {
      foreach ($category_total_arr as $ckey => $cvalue) {
        tep_set_faq_category_status_by_site_id($cvalue, $cstatus, $svalue);
      }

      foreach ($product_total_arr as $pkey => $pvalue) {
        tep_set_faq_question_status_by_site_id($pvalue, $pstatus, $svalue); 
      }
    }
  }

/* -------------------------------------
    功能: 更新faq指定问题的状态 
    参数: $question_id(int) 问题id 
    参数: $status(int) 状态id 
    参数: $site_id(int) 网站id 
    返回值: 是否更新(boolean/int) 
 ------------------------------------ */
  function tep_set_faq_question_status_by_site_id($question_id, $status, $site_id) {
    if ($status == '1') {
      tep_db_query("update " . 'faq_sort' . " set is_show = '1' where
          info_id = '" . $question_id . "' and site_id = '".$site_id."'");
      return tep_db_query("update " . TABLE_FAQ_QUESTION_DESCRIPTION . " set is_show = '1' where
          faq_question_id = '" . $question_id . "' and site_id = '".$site_id."'");
    } elseif ($status == '0') {
      tep_db_query("update " . 'faq_sort'. " set is_show = '0' where 
          info_id = '" . $question_id . "' and site_id = '".$site_id."'");
      return tep_db_query("update " . TABLE_FAQ_QUESTION_DESCRIPTION . " set is_show = '0' where 
          faq_question_id = '" . $question_id . "' and site_id = '".$site_id."'");
    } else {
      return -1;
    }
  }

/* -------------------------------------
    功能: 获取faq指定分类的关联问题 
    参数: $category_id(int) 分类id 
    返回值: 问题集合(array) 
 ------------------------------------ */
  function tep_get_link_question_id_by_category_id($category_id)
  {
    $product_arr = array(); 
    $pro_to_ca_query = tep_db_query("select * from ".TABLE_FAQ_QUESTION_TO_CATEGORIES." 
        where faq_category_id = '".$category_id."'");
    while ($pro_to_ca_res = tep_db_fetch_array($pro_to_ca_query)) {
      $product_arr[] = $pro_to_ca_res['faq_question_id']; 
    }
    return $product_arr;
  }

/* -------------------------------------
    功能: 更新faq指定问题的状态 
    参数: $qID(int) 问题id 
    参数: $pstatus(int) 状态id 
    返回值: 无 
 ------------------------------------ */
  function tep_set_all_question_status($qID, $pstatus)
  {
    $site_arr = array(0); 
    $site_query = tep_db_query("select * from ".TABLE_SITES); 
    while ($site_res = tep_db_fetch_array($site_query)) {
      $site_arr[] = $site_res['id']; 
    }

    foreach ($site_arr as $key => $value) {
      if (!tep_check_question_exists($qID, $value)) {
      }
      tep_db_query("UPDATE `".TABLE_FAQ_QUESTION_DESCRIPTION."` SET 
          `is_show` = '".$pstatus."' where `faq_question_id` = '".$qID."' 
          and `site_id` = '".$value."'");  
    }
  }

/* -------------------------------------
    功能: 判断faq的指定问题在指定网站是否存在 
    参数: $qid(int) 问题id 
    参数: $site_id(int) 网站id 
    返回值: 是否存在(boolean) 
 ------------------------------------ */
  function tep_check_question_exists($qid, $site_id)
  {
    $exist_pro_query = tep_db_query("select * from ".TABLE_FAQ_QUESTION_DESCRIPTION." where 
        faq_question_id = '".$qid."' and site_id = '".(int)$site_id."'");
    return tep_db_num_rows($exist_pro_query);
  }

/* -------------------------------------
    功能: 自动输出faq指定分类的路径 
    参数: $id(int) 分类id 
    参数: $from(int) 类型 
    返回值: 分类路径(string) 
 ------------------------------------ */
  function tep_output_generated_faq_category_path($id, $from = 'category') {
    $calculated_category_path_string = '';
    $calculated_category_path = tep_generate_faq_category_path($id, $from);
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

/* -------------------------------------
    功能: 获取订单完成标识 
    参数: $orders_id(int) 订单id 
    返回值: 完成标识(string) 
 ------------------------------------ */
  function     tep_orders_finishqa($orders_id) {
    $order = tep_db_fetch_array(tep_db_query("select * from ".TABLE_ORDERS." where orders_id='".$orders_id."'"));
    return $order['flag_qaf'];
  }

/* -------------------------------------
    功能: 根据faq的分类id自动生成分类路径 
    参数: $id(int) 分类id 
    参数: $from(string) 类型 
    参数: $categories_array(array) 分类数组 
    参数: $index(int) 值 
    返回值: 分类路径(array) 
 ------------------------------------ */
  function tep_generate_faq_category_path($id, $from = 'category', $categories_array = '', $index = 0) {
    global $languages_id;

    if (!is_array($categories_array)) $categories_array = array();

    if ($from == 'question') {
      $categories_query = tep_db_query("select faq_category_id from " . 
          TABLE_FAQ_QUESTION_TO_CATEGORIES . " where faq_question_id = '" . $id . "'");
      while ($categories = tep_db_fetch_array($categories_query)) {
        if ($categories['faq_category_id'] == '0') {
          $categories_array[$index][] = array('id' => '0', 'text' => TEXT_TOP);
        } else {
          $category_query = tep_db_query("select 
              cd.title, c.parent_id from " . 
              TABLE_FAQ_CATEGORIES . " c, " . 
              TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd 
              where c.id = '" . $categories['faq_category_id'] . "' 
              and c.id = cd.faq_category_id and 
              cd.site_id='0'");
          $category = tep_db_fetch_array($category_query);
          $categories_array[$index][] = array('id' =>
              $categories['faq_category_id'], 'text' => $category['title']);
          if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] !=
                '0') ) $categories_array = tep_generate_faq_category_path($category['parent_id'], 'category', $categories_array, $index);
          $categories_array[$index] = tep_array_reverse($categories_array[$index]);
        }
        $index++;
      }
    } elseif ($from == 'category') {
      $category_query = tep_db_query("select cd.title, c.parent_id from " .
          TABLE_FAQ_CATEGORIES . " c, " .
          TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd 
          where c.id = '" . $id . "' 
          and c.id = cd.faq_category_id 
          and cd.site_id='0'");
      $category = tep_db_fetch_array($category_query);
      $categories_array[$index][] = array('id' => $id, 'text' => $category['title']);
      if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0')
         ) $categories_array = tep_generate_faq_category_path($category['parent_id'], 'category', $categories_array, $index);
    }

    return $categories_array;
  }

/* -------------------------------------
    功能: 获得faq的面包屑 
    参数: $cPath(string) 分类路径 
    参数: $site_id(int) 网站id 
    返回值: 面包屑(string) 
 ------------------------------------ */
  function tep_get_faq_breadcreumb_by_cpath($cPath,$site_id=0){
    if(isset($cPath)&&$cPath){
      $cPath_arr = explode('_',$cPath);
      $cPath_breadcreumb = array();
      foreach($cPath_arr as $cid){
        $cPath_breadcreumb_info = tep_get_faq_category_info($cid,$site_id); 
        $cPath_breadcreumb[] = $cPath_breadcreumb_info['title'];
      }
      return implode('  ',$cPath_breadcreumb);
    }else{
      return '';
    }
  }

/* -------------------------------------
    功能: 获得faq的指定分类的在指定网站的信息 
    参数: $cid(int) 分类id 
    参数: $site_id(int) 网站id 
    返回值: 分类信息(array) 
 ------------------------------------ */
  function tep_get_faq_category_info($cid,$site_id=0){
    if($site_id==0){
    return tep_db_fetch_array(tep_db_query("select * from
          ".TABLE_FAQ_CATEGORIES_DESCRIPTION." where faq_category_id = '".$cid."' 
          order by site_id DESC "));
    }else{
    return tep_db_fetch_array(tep_db_query("select * from
          ".TABLE_FAQ_CATEGORIES_DESCRIPTION." where faq_category_id = '".$cid."' 
          and (site_id = '0' or site_id='".$site_id."') 
          order by site_id DESC "));
    }
  }

/* -------------------------------------
    功能: 获得faq指定分类在所有网站中的状态 
    参数: $faq_category_id(int) 分类id 
    返回值: 状态集合(array) 
 ------------------------------------ */
  function get_all_site_faq_category_status($faq_category_id)
  {
    $site_arr = array();
    $site_romaji = array(); 
    $status_arr = array();
    $status_arr['green'] = array();
    $status_arr['red'] = array();

    $site_arr[] = 0; 
    $site_romaji[] = 'all';

    $site_raw = tep_db_query("select * from ".TABLE_SITES." order by id asc");
    while ($site_res = tep_db_fetch_array($site_raw)) {
      $site_arr[] = $site_res['id']; 
      $site_romaji[] = $site_res['romaji']; 
    }

    foreach ($site_arr as $key => $value) {
      $faq_category_des_raw = tep_db_query("select * from ".TABLE_FAQ_CATEGORIES_DESCRIPTION." where (site_id = '".$value."' or site_id = '0') and faq_category_id = '".$faq_category_id."' order by site_id desc limit 1"); 
      $faq_category_des_res = tep_db_fetch_array($faq_category_des_raw); 

      switch ($faq_category_des_res['is_show']) {
        case '0':
          $status_arr['red'][] = $site_romaji[$key]; 
          break;
        default:
          $status_arr['green'][] = $site_romaji[$key]; 
          break;
      }
    }

    return $status_arr;
  }

/* -------------------------------------
    功能: 获得faq指定问题在所有网站中的状态 
    参数: $question_id(int) 问题id 
    返回值: 状态集合(array) 
 ------------------------------------ */
  function get_all_site_faq_question_status($question_id)
  {
    $site_arr = array();
    $site_romaji = array(); 
    $status_arr = array();
    $status_arr['green'] = array();
    $status_arr['red'] = array();

    $site_arr[] = 0; 
    $site_romaji[] = 'all';

    $site_raw = tep_db_query("select * from ".TABLE_SITES." order by id asc");
    while ($site_res = tep_db_fetch_array($site_raw)) {
      $site_arr[] = $site_res['id']; 
      $site_romaji[] = $site_res['romaji']; 
    }

    foreach ($site_arr as $key => $value) {
      $question_des_raw = tep_db_query("select * from ".TABLE_FAQ_QUESTION_DESCRIPTION." where (site_id = '".$value."' or site_id = '0') and faq_question_id = '".$question_id."' order by site_id desc limit 1"); 
      $question_des_res = tep_db_fetch_array($question_des_raw); 

      switch ($question_des_res['is_show']) {
        case '1':
          $status_arr['green'][] = $site_romaji[$key]; 
          break;
        default:
          $status_arr['red'][] = $site_romaji[$key]; 
          break;
      }
    }

    return $status_arr;
  }

/* -------------------------------------
    功能: 获得faq指定分类的信息 
    参数: $cid(int) 分类id 
    参数: $site_id(int) 网站id 
    返回值: 分类信息(array) 
 ------------------------------------ */
  function tep_is_set_faq_category($cid,$site_id){
    return tep_db_fetch_array(tep_db_query("select * from
          ".TABLE_FAQ_CATEGORIES_DESCRIPTION." where faq_category_id = '".$cid."' 
          and  site_id='".$site_id."'
          order by site_id DESC "));
  }

/* -------------------------------------
    功能: 判断faq指定问题是否在指定分类关联的问题中 
    参数: $current_category_id(int) 分类id 
    参数: $qid(int) 问题id 
    参数: $site_id(int) 网站id 
    参数: $search(string) 搜索条件 
    参数: $page(int) 页数 
    返回值: 是否在(boolean) 
 ------------------------------------ */
  function tep_is_set_faq_question($current_category_id,$qid,$site_id,$search='',$page=1){
    if(isset($search) && $search) {
      $query_raw = "select 
        fqd.is_show,
        fq2c.faq_category_id,
        fqd.faq_question_id,
        fqd.romaji,
        fqd.ask,
        fqd.keywords,
        fqd.answer,
        fq.sort_order,
        fq.created_at,
        fq.updated_at,
        fqd.site_id 
          from ".TABLE_FAQ_QUESTION." fq, 
        ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd ,
        ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
          where fq.id = fqd.faq_question_id 
          and fq.id = fq2c.faq_question_id 
          and fqd.ask like '%".$search."%' 
          and fqd.site_id ='0' 
          order by fq.sort_order,fqd.ask,fq.id  
          ";
    }else if(isset($site_id)&&$site_id){
      $query_raw = "select * from (
        select 
        fqd.is_show,
        fq2c.faq_category_id,
        fqd.faq_question_id,
        fqd.romaji,
        fqd.ask,
        fqd.keywords,
        fqd.answer,
        fq.sort_order,
        fq.created_at,
        fq.updated_at,
        fqd.site_id 
          from ".TABLE_FAQ_QUESTION." fq, 
        ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd ,
        ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
          where fq.id = fqd.faq_question_id 
          and fq.id = fq2c.faq_question_id 
          and fq2c.faq_category_id = '". $current_category_id . "' 
          order by fqd.site_id DESC
          ) c  
          where site_id = ".((isset($site_id) &&
          $site_id)?$site_id:0)." 
          or site_id = 0 
          group by c.faq_question_id 
          order by c.sort_order,c.ask,c.faq_question_id 
          ";
    }else{
      $query_raw = "select * from (
        select 
        fqd.is_show,
        fq2c.faq_category_id,
        fqd.faq_question_id,
        fqd.romaji,
        fqd.ask,
        fqd.keywords,
        fqd.answer,
        fq.sort_order,
        fq.created_at,
        fq.updated_at,
        fqd.site_id 
          from ".TABLE_FAQ_QUESTION." fq, 
        ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd ,
        ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
          where fq.id = fqd.faq_question_id 
          and fq.id = fq2c.faq_question_id 
          and fq2c.faq_category_id = '". $current_category_id . "' 
          order by fqd.site_id DESC
          ) c  
          group by c.faq_question_id 
          order by c.sort_order,c.ask,c.faq_question_id 
          ";
    }
    $split = new splitPageResults($page,MAX_DISPLAY_FAQ_ADMIN,
        $query_raw,$query_number);
    $query = tep_db_query($query_raw);
    while($res = tep_db_fetch_array($query)){
      if($res['faq_question_id']==$qid){
        return true;
      }
    }
    return false;
  }

/* -------------------------------------
    功能: faq网站列表 
    参数: $filename(string) 文件名 
    参数: $ca_single(boolean) 是否过滤指定参数 
    返回值: 网站列表(string) 
 ------------------------------------ */
function tep_faq_site_filter($filename, $ca_single = false){
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
              echo tep_href_link($filename,
                  tep_get_all_get_params(array('site_id','page')));
            } else {
              echo tep_href_link($filename, tep_get_all_get_params(array('site_id', 'page', 'cID', 'qID')));
            }
          ?>">all</a></span> 
            <?php } ?>
            <?php foreach (tep_get_sites() as $site) {?>
              <?php if (isset($_GET['site_id']) && $_GET['site_id'] == $site['id']) {?>
                <span class="site_filter_selected"><?php echo $site['romaji'];?></span>
                  <?php } else {?>
                    <span><a href="<?php 
                      if ($ca_single) {
                        echo tep_href_link($filename,
                            tep_get_all_get_params(array('site_id','page')) . 'site_id=' . $site['id']);
                      } else {
                        echo tep_href_link($filename, tep_get_all_get_params(array('site_id', 'page',  'cID', 'qID')) . 'site_id=' . $site['id']);
                      }
                    ?>"><?php echo $site['romaji'];?></a></span>
                      <?php }
            }
          ?>
            </div>
            <?php
}

/* -------------------------------------
    功能: 获得faq问题的数量 
    参数: $current_category_id(int) 分类id 
    参数: $qid(int) 问题id 
    参数: $site_id(int) 网站id 
    参数: $search(string) 搜索条件 
    返回值: faq问题的数量(int) 
 ------------------------------------ */
function tep_get_rownum_faq_question($current_category_id,$qid,$site_id,$search=''){
    if(isset($search) && $search) {
      $query_raw = "select 
        fqd.is_show,
        fq2c.faq_category_id,
        fqd.faq_question_id,
        fqd.romaji,
        fqd.ask,
        fqd.keywords,
        fqd.answer,
        fq.sort_order,
        fq.created_at,
        fq.updated_at,
        fqd.site_id 
          from ".TABLE_FAQ_QUESTION." fq, 
        ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd ,
        ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
          where fq.id = fqd.faq_question_id 
          and fq.id = fq2c.faq_question_id 
          and fqd.ask like '%".$search."%' 
          and fqd.site_id ='0' 
          order by fq.sort_order,fqd.ask,fq.id  
          ";
    }else if(isset($site_id)&&$site_id){
      $query_raw = "select * from (
        select 
        fqd.is_show,
        fq2c.faq_category_id,
        fqd.faq_question_id,
        fqd.romaji,
        fqd.ask,
        fqd.keywords,
        fqd.answer,
        fq.sort_order,
        fq.created_at,
        fq.updated_at,
        fqd.site_id 
          from ".TABLE_FAQ_QUESTION." fq, 
        ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd ,
        ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
          where fq.id = fqd.faq_question_id 
          and fq.id = fq2c.faq_question_id 
          and fq2c.faq_category_id = '". $current_category_id . "' 
          order by fqd.site_id DESC
          ) c  
          where site_id = ".((isset($site_id) &&
          $site_id)?$site_id:0)." 
          or site_id = 0 
          group by c.faq_question_id 
          order by c.sort_order,c.ask,c.faq_question_id 
          ";
    }else{
      $query_raw = "select * from (
        select 
        fqd.is_show,
        fq2c.faq_category_id,
        fqd.faq_question_id,
        fqd.romaji,
        fqd.ask,
        fqd.keywords,
        fqd.answer,
        fq.sort_order,
        fq.created_at,
        fq.updated_at,
        fqd.site_id 
          from ".TABLE_FAQ_QUESTION." fq, 
        ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd ,
        ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
          where fq.id = fqd.faq_question_id 
          and fq.id = fq2c.faq_question_id 
          and fq2c.faq_category_id = '". $current_category_id . "' 
          order by fqd.site_id DESC
          ) c  
          group by c.faq_question_id 
          order by c.sort_order,c.ask,c.faq_question_id 
          ";
    }
    $query = tep_db_query($query_raw);
    $i=1;
    while($res = tep_db_fetch_array($query)){
      if($res['faq_question_id'] == $qid){
        return $i;
      }
      $i++;
    }
    return $i;
}

/* -------------------------------------
    功能: 获得指定网站的罗马字 
    参数: $site_id(int) 网站id 
    返回值: 网站的罗马字(string/false) 
 ------------------------------------ */
function get_romaji_by_site_id($site_id) {
  $site = tep_db_fetch_array(tep_db_query("select romaji from ".TABLE_SITES." where id='".$site_id."'"));
  if ($site) {
    return $site['romaji'];
  } else {
    return false;
  }
}

/* -------------------------------------
    功能: 网站头部列表 
    参数: $filename(string) 文件名 
    返回值: 网站头部列表(string) 
 ------------------------------------ */
function tep_site_head_list($filename){
  global $_GET, $_POST;
  ?>
    <div id="tep_site_filter">
            <?php foreach (tep_get_sites() as $site) {?>
              <?php if ((isset($_GET['site_id']) && $_GET['site_id'] == $site['id']) || (!isset($_GET['site_id']) && $site['id'] == 1)) {?>
                <span class="site_filter_selected"><?php echo $site['romaji'];?></span>
                  <?php } else {?>
                    <span><a href="<?php 
                        echo tep_href_link($filename, tep_get_all_get_params(array('site_id', 'page', 'oID', 'rID', 'cID', 'pID')) . 'site_id=' . $site['id']);
                    ?>"><?php echo $site['romaji'];?></a></span>
                      <?php }
            }
          ?>
            </div>
            <?php
}

/* -------------------------------------
    功能: 获取订单id的后两位数字 
    参数: 无 
    返回值: 后两位数字(string) 
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
    功能: 判断是否可以终了该订单 
    参数: $orders_id(string) 订单id 
    返回值: 是否终了(boolean) 
 ------------------------------------ */
function tep_get_order_canbe_finish($orders_id){
  //  如果是取消的可以结束 
  
  if (tep_orders_finishqa($orders_id)) {
    return false;
  }
  $status =  tep_get_orders_status_id($orders_id);
  if($status == 6 or $status == 8){
    return true;
  }
  $formtype = tep_check_order_type($orders_id);
  $payment_romaji = tep_get_payment_code_by_order_id($orders_id); 
  $oa_form_sql = "select * from ".TABLE_OA_FORM."   where formtype = '".$formtype."' and payment_romaji = '".$payment_romaji."'";
  $res = tep_db_fetch_array(tep_db_query($oa_form_sql));;
  $form_id = $res['id'] ;
  $sql = 'select i.* from oa_form_group fg ,oa_item i where  i.group_id = fg.group_id and i.option like "%require%" and fg.form_id = "'.$form_id .'"';
  $res3  = tep_db_query($sql);
  while($item = tep_db_fetch_array($res3)){
    $sql2 =  'select value from oa_formvalue where item_id = '.$item['id'] .' and orders_id ="'.$orders_id.'" and form_id = "'.$form_id.'"';
    $res2 = tep_db_fetch_array(tep_db_query($sql2));
    if (!$res2){
      return false;
    }else {
      if ($res2['value']==''){
      return false;
      }
    }
    $res2 = '';
  }
  
return true;
}

/* -------------------------------------
    功能: 更新顾客最新下订单的时间 
    参数: 无 
    返回值: 更新时间(boolean) 
 ------------------------------------ */
function last_customer_action() {
  tep_db_query("update ".TABLE_CONFIGURATION." set configuration_value=now() where configuration_key='LAST_CUSTOMER_ACTION'");
}

/* -------------------------------------
    功能: 该订单的总价的真实输出 
    参数: $orders_id(string) 订单id 
    参数: $single(boolean) 是否格式化 
    返回值: 总价输出(string) 
 ------------------------------------ */
function tep_get_ot_total_by_orders_id_no_abs($orders_id, $single = false) {
  if ($single) {
    global $currencies; 
  }
  $query = tep_db_query("select value from " . TABLE_ORDERS_TOTAL . " where class='ot_total' and orders_id='".$orders_id."'");
  $result = tep_db_fetch_array($query);
  if($result['value'] > 0){
    if ($single) {
      return
        "<b>".$currencies->format($result['value'],true,DEFAULT_CURRENCY,'',false)."</b>";
    } else {
      return "<b>".$result['value']."</b>";
    }
  }else{
    if ($single) {
      if ($result['value'] < 0) {
        return "<b><font color='#ff0000'>".str_replace(TEXT_MONEY_SYMBOL, '', $currencies->format($result['value'],true,DEFAULT_CURRENCY,'',false))."</font>".TEXT_MONEY_SYMBOL."</b>";
      } else {
        return "<b>".$currencies->format($result['value'],true,DEFAULT_CURRENCY,'',false)."</b>";
      }
    } else {
      return "<b><font color='#ff0000'>".$result['value']."".TEXT_MONEY_SYMBOL."</font></b>";
    }
  }
}

/* -------------------------------------
    功能: 判断该订单是否在指定订单列表里 
    参数: $orders_query_raw(string) 订单查询sql 
    参数: $oID(string) 订单id 
    返回值: 是否在(boolean) 
 ------------------------------------ */
function tep_is_in_order_page($orders_query_raw,$oID){
  $show_orders_id_arr = array();
  $tmp_query = tep_db_query($orders_query_raw);
  while($tmp_row = tep_db_fetch_array($tmp_query)){
    $show_orders_id_arr[] = $tmp_row['orders_id'];
  }
  if(in_array($oID,$show_orders_id_arr)){
    return true;
  }else{
    return false;
  }
}

/* -------------------------------------
    功能: 获取该订单类型 
    参数: $oID(string) 订单id 
    返回值: 订单类型(int) 1:买取 2:贩卖 3:混合 
 ------------------------------------ */
function tep_get_order_type_info($oID)
{
  $orders_products_raw = tep_db_query("select products_id from ".TABLE_ORDERS_PRODUCTS." where orders_id = '".$oID."'");
  if (!tep_db_num_rows($orders_products_raw)) {
    return 3; 
  }
  while ($orders_products = tep_db_fetch_array($orders_products_raw)) {
    $exists_products_raw = tep_db_query("select products_id from ".TABLE_PRODUCTS." where products_id = '".$orders_products['products_id']."'"); 
    if (!tep_db_num_rows($exists_products_raw)) {
      return 3; 
    }
  }
  
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
    功能: 获取该商品在订单商品表里指定网站的平均最终价格 
    参数: $products_id(int) 商品id 
    参数: $site_id(int) 网站id 
    返回值: 平均最终价格(float) 
 ------------------------------------ */
function tep_get_avg_price_by_order($products_id,$site_id=0){
  $sql = "SELECT AVG( `final_price` )as final_unit_price 
    FROM  `orders_products` 
    WHERE products_id='".$products_id."' ";
  if($site_id != 0 ){
    $sql .= " site_id = '".$site_id."'";
  }
  $query = tep_db_query($sql);
  $res = tep_db_fetch_array($query);
  return $res['final_unit_price'];
}

/* -------------------------------------
    功能: 获取该分类下的子分类 
    参数: $cid(int) 分类id 
    返回值: 子分类 的集合(array) 
 ------------------------------------ */
function tep_get_child_category_by_cid($cid)
{
   $return_arr = array();
   $return_arr[] = $cid;
   $child_category_raw = tep_db_query("select categories_id from ".TABLE_CATEGORIES." where parent_id = '".$cid."'"); 
   while ($child_category = tep_db_fetch_array($child_category_raw)) {
     $return_arr[] = $child_category['categories_id']; 
     $child_child_category_raw = tep_db_query("select categories_id from ".TABLE_CATEGORIES." where parent_id = '".$child_category['categories_id']."'"); 
     while ($child_child_category = tep_db_fetch_array($child_child_category_raw)) {
       $return_arr[] = $child_child_category['categories_id']; 
     }
   }
   return $return_arr;
}

/* -------------------------------------
    功能: 获取该分类下指定类型的商品信息 
    参数: $cid(int) 分类id 
    参数: $bflag(int) 是否买取 
    参数: $site_id(int) 网站id 
    参数: $start(string) 开始时间 
    参数: $end(string) 结束时间 
    参数: $sort(string) 排序 
    返回值: 商品信息(array) 
 ------------------------------------ */
function tep_get_all_asset_category_by_cid($cid,$bflag,$site_id=0,
    $start='',$end='',$sort=''){
   $return_arr = array();
   $return_arr[] = $cid;
   $child_category_raw = tep_db_query("select categories_id from ".TABLE_CATEGORIES." where parent_id = '".$cid."'"); 
   while ($child_category = tep_db_fetch_array($child_category_raw)) {
     $return_arr[] = $child_category['categories_id']; 
     $child_child_category_raw = tep_db_query("select categories_id from ".TABLE_CATEGORIES." where parent_id = '".$child_category['categories_id']."'"); 
     while ($child_child_category = tep_db_fetch_array($child_child_category_raw)) {
       $return_arr[] = $child_child_category['categories_id']; 
     }
   }
   $products_arr = array();
   if(count($return_arr) >1 ){
     $cid_str = " and p2c.categories_id in (".implode(',',$return_arr).") ";
   }else if(count($return_arr)==1){
     $cid_str = " and p2c.categories_id = '".$return_arr[0]."' ";
   }
   $tmp_sql = "select p.products_id,p.products_real_quantity from ".TABLE_PRODUCTS." 
     p,".TABLE_PRODUCTS_TO_CATEGORIES." p2c where p2c.products_id = p.products_id 
     ".$cid_str." and p.products_bflag='".$bflag."'";
   $tmp_query=tep_db_query($tmp_sql);
   $quantity_all_product = 0;
   $asset_all_product = 0;
   $result = array();
   $result['error'] = false;
   $all_tmp_price = 0;
   $all_tmp_row = 0;
   while($tmp_row = tep_db_fetch_array($tmp_query)){
     $tmp_price['products_real_quantity'] = tep_get_quantity($tmp_row['products_id']);
     $tmp_price =
       @tep_get_asset_avg_by_pid($tmp_row['products_id'],$site_id,$start,$end,
           $sort);
     if($tmp_row['products_real_quantity'] > tep_get_relate_products_sum($tmp_row['products_id']
           ,$site_id,$start,$end)&&$tmp_row['products_real_quantity']!=0){
       $result['error'] = true;
     }else{
     }
     $asset_all_product += ($tmp_row['products_real_quantity']*$tmp_price);
     if($tmp_row['products_real_quantity'] != 0){
       $all_tmp_row++;
     }
     $quantity_all_product += $tmp_row['products_real_quantity'];
     $all_tmp_price += $tmp_price;
   }
   $result['quantity_all_product'] = $quantity_all_product;
   $result['asset_all_product'] = $asset_all_product;
   $result['avg_price'] = @($all_tmp_price/$all_tmp_row);
   return $result;
}

/* -------------------------------------
    功能: 获取指定商品信息 
    参数: $pid(int) 商品id 
    参数: $bflag(int) 是否买取 
    参数: $site_id(int) 网站id 
    参数: $start(int) 开始时间 
    参数: $end(int) 结束时间 
    参数: $sort(string) 排序 
    返回值: 商品信息(array) 
 ------------------------------------ */
function tep_get_all_asset_product_by_pid($pid,$bflag,$site_id=0,
    $start='',$end='',$sort=''){
  $sql = "select products_real_quantity from ".TABLE_PRODUCTS." where products_id
    ='".$pid."' and products_bflag='".$bflag."'";
  $query = tep_db_query($sql);
  $row = tep_db_fetch_array($query);
  $tmp_price = @tep_get_asset_avg_by_pid($pid,$site_id,$start,$end,$sort);
  $row['products_real_quantity'] = tep_get_quantity($pid);
  $result = array();
  $result['error'] = false;
  if($row['products_real_quantity'] > tep_get_relate_products_sum($pid,$site_id,
        $start,$end)&&$row['products_real_quantity']!=0){
    $result['error'] = true;
  }else{
    $result['error'] = false;
  }
  $result['quantity_all_product'] = $row['products_real_quantity'];
  $result['asset_all_product'] =$tmp_price*$row['products_real_quantity'];
  if($tmp_price){
    $result['price'] = $tmp_price;
  }else{
    $result['price'] = '0';
  }
  return $result;
}

/* -------------------------------------
    功能: 获取指定商品的平均值 
    参数: $pid(int) 商品id 
    参数: $site_id(int) 网站id 
    参数: $start(int) 开始时间 
    参数: $end(int) 结束时间 
    参数: $sort(string) 排序 
    返回值: 商品的平均值(float) 
 ------------------------------------ */
function tep_get_asset_avg_by_pid($pid,$site_id=0,$start='',$end='',$sort=''){
    $product = tep_db_fetch_array(tep_db_query("select * from ".TABLE_PRODUCTS." where products_id='".$pid."'"));
       $sql ="
        select op.products_quantity ,op.final_price 
        from ".TABLE_ORDERS_PRODUCTS." op left join ".TABLE_ORDERS." o on op.orders_id=o.orders_id left join ".TABLE_ORDERS_STATUS." os on o.orders_status=os.orders_status_id 
        where 
        op.products_id='".$product['relate_products_id']."'
        and os.calc_price = '1'";
       if($site_id!=0){
         $sql .= " and o.site_id = '".$site_id."' ";
       }
       if($start!=''&&$end!=''){
         $sql .= " and date_purchased between '".$start."' and '".$end."' ";
       }
    if($sort=='price_desc'){
      $sql .= " order by final_price desc ";
    }else if($sort=='price_asc'){
      $sql .= " order by final_price asc ";
    }else{
      $sql .= " order by o.torihiki_date desc";
    }
    $order_history_query = tep_db_query($sql);
    $sum = 0;
    $cnt = 0;
    while($h = tep_db_fetch_array($order_history_query)){
      $product['products_real_quantity'] = tep_get_quantity($pid);
      if ($cnt + $h['products_quantity'] > $product['products_real_quantity']) {
        $sum += ($product['products_real_quantity'] - $cnt) * abs($h['final_price']);
        $cnt = $product['products_real_quantity'];
        break;
      } else {
        $sum += $h['products_quantity'] * abs($h['final_price']);
        $cnt += $h['products_quantity'];
      }
    }
    return $sum/$cnt;
  }

/* -------------------------------------
    功能: 获取该分类下关联的指定类型的商品信息 
    参数: $categories_id(int) 分类id 
    参数: $bflag(int) 是否买取 
    参数: $site_id(int) 网站id 
    返回值: 指定类型的商品信息(array) 
 ------------------------------------ */
function tep_get_product_by_category_id($categories_id,$bflag,$site_id=0){
  $arr = array();
  $sql = "select distinct p.*,pd.*,
      IF(  `relate_products_id` =0 OR  `relate_products_id` IS NULL , '1', '0' ) as relate_id from 
      products p, products_description pd, products_to_categories p2c 
      where p.products_id=pd.products_id 
      and p2c.products_id=p.products_id
      and p.products_bflag = '".$bflag."' 
      and categories_id='".$categories_id."' and pd.site_id='0' 
       order by relate_id,pd.products_name";
  $query = tep_db_query($sql);
  while ($product = tep_db_fetch_array($query)) {
    $arr[] = $product;
  }
  return $arr;
}

/* -------------------------------------
    功能: 获取该商品在指定时间内,指定网站的最大配送开始时间 
    参数: $pid(int) 商品id 
    参数: $site_id(int) 网站id 
    参数: $start(string) 开始时间 
    参数: $end(string) 结束时间 
    返回值: 最大配送开始时间(string) 
 ------------------------------------ */
function tep_get_relate_date($pid,$site_id=0,$start='',$end='')
{
  $sql = "select max(o.torihiki_date) as max_date from ".TABLE_ORDERS." o ,
    ".TABLE_ORDERS_PRODUCTS." op ,".TABLE_ORDERS_STATUS." os ,".TABLE_PRODUCTS." p 
      where  o.orders_id = op.orders_id and p.products_id = '".$pid."'
      and p.relate_products_id= op.products_id 
      and o.orders_status = os.orders_status_id and os.calc_price = '1' ";
  if($site_id!=0){
    $sql .= " and o.site_id='".$site_id."' ";
  }
  if($start!=''&&$end!=''){
    $sql .= " and o.date_purchased between '".$start."' and '".$end."' ";
  }
  $query = tep_db_query($sql);
  $res = tep_db_fetch_array($query);
  return $res['max_date'];
}

/* -------------------------------------
    功能: 获取指定商品在指定时间和排序内的和其关联的订单的信息 
    参数: $pid(int) 商品id 
    参数: $start(string) 开始时间 
    参数: $end(string) 结束时间 
    参数: $sort(string) 排序值 
    返回值: 订单相关信息(array) 
 ------------------------------------ */
function tep_get_order_history_sql_by_pid($pid,$start='',$end='',$sort=''){
  $sql = "select p.products_id,  
    op.products_name,op.final_price,op.products_quantity,o.torihiki_date
    from ".TABLE_ORDERS." o,".TABLE_ORDERS_PRODUCTS." op,".
    TABLE_ORDERS_STATUS." os ,".TABLE_PRODUCTS." p WHERE 
    o.orders_id = op.orders_id and os.orders_status_id=o.orders_status 
    and os.calc_price = 1 and op.products_id = p.relate_products_id 
    and p.products_id ='".$pid."' ";
  if($start!=''&&$end!=''){
    $sql .= " and o.date_purchased between '".$start."' and '".$end."' ";
  }
  if($sort){
    if($sort=='price_desc'){
      $sql .= " order by final_price desc ";
    }else if($sort=='price_asc'){
      $sql .= " order by final_price asc ";
    }else{
      $sql .= " order by o.torihiki_date desc ";
    }
  }else{
    $sql .= " order by o.torihiki_date desc ";
  }
  return $sql;
}

/* -------------------------------------
    功能: 获取指定商品在指定时间,在其关联的订单里的指定网站的购买数量的总和 
    参数: $pid(int) 商品id 
    参数: $site_id(int) 网站id 
    参数: $start(string) 开始时间 
    参数: $end(string) 结束时间 
    返回值: 数量的总和(int) 
 ------------------------------------ */
function tep_get_relate_products_sum($pid,$site_id=0,$start='',$end='')
{
  $sql = "select op.products_quantity as sum_relate from ".TABLE_ORDERS." o ,
    ".TABLE_ORDERS_PRODUCTS." op ,".TABLE_ORDERS_STATUS." os ,".TABLE_PRODUCTS." p 
      where  o.orders_id = op.orders_id and p.products_id = '".$pid."'
      and p.relate_products_id= op.products_id 
      and o.orders_status = os.orders_status_id and os.calc_price = '1' ";
  if($site_id!=0){
    $sql .= " and o.site_id='".$site_id."' ";
  }
  if($start!=''&&$end!=''){
    $sql .= " and o.date_purchased between '".$start."' and '".$end."' ";
  }
  $query = tep_db_query($sql);
  $return_sum = 0;
  while($res = tep_db_fetch_array($query)){
    $return_sum += $res['sum_relate'];
  }
  return $return_sum;

}

/* -------------------------------------
    功能: 输出该分类下的子分类的信息 
    参数: $id(int) 分类id 
    参数: $from(string) 类型 
    返回值: 子分类的信息(string) 
 ------------------------------------ */
function tep_output_generated_category_path_asset($id, $from = 'category') {
  $calculated_category_path_string = '';
  $calculated_category_path = tep_generate_category_path($id, $from);
  if($from=='category'){
    krsort($calculated_category_path);
  }
  foreach ($calculated_category_path as $i=>$i_value) {
    if($from=='category'){
      krsort($calculated_category_path[$i]);
    }
    foreach ($calculated_category_path[$i] as $j=>$j_value) {
      $calculated_category_path_string .= $calculated_category_path[$i][$j]['text'];
      $calculated_category_path_string .= '&nbsp;&gt;&gt;&nbsp;';
    }
  }
  $calculated_category_path_string = substr($calculated_category_path_string, 0, -20);

  if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = TEXT_TOP;

  return $calculated_category_path_string;

}

/* -------------------------------------
    功能: 已安装的支付模块的数量 
    参数: 无 
    返回值: 支付模块的数量(int) 
 ------------------------------------ */
  function tep_count_payment_modules() {
    return tep_count_modules(MODULE_PAYMENT_INSTALLED);
  }

/* -------------------------------------
    功能: 指定模块的数量 
    参数: $modules(string) 模块的信息 
    返回值: 模块的数量(int) 
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
    功能: 根据邮箱和指定网站获取顾客id 
    参数: $email(string) 邮箱 
    参数: $site_id(int) 网站id 
    返回值: 顾客id(int) 
 ------------------------------------ */
function tep_get_customer_id_by_email($email,$site_id=0){
    $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_email_address = '" . $email . "' and site_id = '".$site_id."'");
    $account = tep_db_fetch_array($account_query);
    if(!$account){
      return false;
    }
    $customer = $account['customers_id'];
    return $customer;
}

/* -------------------------------------
    功能: 获取指定顾客信息 
    参数: $id(int) 顾客id 
    返回值: 顾客信息(array/boolean) 
 ------------------------------------ */
function tep_get_customer_by_id($id){
    $account_query = tep_db_query("select * from " . TABLE_CUSTOMERS . " where customers_id = ".$id);
    $account = tep_db_fetch_array($account_query);
    if(!$account){
      return false;
    }
    return $account;
}

/* -------------------------------------
    功能: 获取指定顾客地址信息 
    参数: $id(int) 顾客id 
    返回值: 顾客地址信息(array) 
 ------------------------------------ */
function tep_get_address_by_cid($id){
  $address_query = tep_db_query("select * from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $id . "'");
  $address = tep_db_fetch_array($address_query);
  return $address;
}

/* -------------------------------------
    功能: 生成指定规格的多选 
    参数: $check_array(array) 值 
    参数: $key_value(string) 默认值 
    参数: $key(string) 名 
    返回值: 多选(string) 
 ------------------------------------ */
function tep_cfg_shipping_checkbox_option($check_array, $key_value, $key = '') {
    $string = '';
    for ($i = 0, $n = sizeof($check_array); $i < $n; $i++) {
      $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');
      $string .= '<br><input type="checkbox" name="' . $name . '[]" value="' .  $check_array[$i] . '"';
      if (in_array($check_array[$i], unserialize($key_value))) $string .= ' CHECKED';
      $string .= '> '; 
      if (($i+1) == 1) {
        $string .= '会員'; 
      } else {
        $string .= 'ゲスト'; 
      }
    }
    return $string;
  }

/* -------------------------------------
    功能: 配送时间的简要输出 
    参数: $raw_datetime(string) 时期 
    返回值: 时间的简要输出(string) 
 ------------------------------------ */
function tep_datetime_short_torihiki($raw_datetime) {
  if ( ($raw_datetime == '0000-00-00 00:00:00') || ($raw_datetime == '') ) return false;

  $year = (int)substr($raw_datetime, 0, 4);
  $month = (int)substr($raw_datetime, 5, 2);
  $day = (int)substr($raw_datetime, 8, 2);
  $hour = (int)substr($raw_datetime, 11, 2);
  $minute = (int)substr($raw_datetime, 14, 2);
  $second = (int)substr($raw_datetime, 17, 2);

  return strftime(DATE_TIME_FORMAT_TORIHIKI, mktime($hour, $minute, $second, $month, $day, $year));
}

/* -------------------------------------
    功能: 配送时间的单选按钮 
    参数: $start_time(int) 开始时间 
    参数: $radio_name(string) 名 
    参数: $default_check(boolean) 是否默认 
    返回值: 单选按钮输出(string) 
 ------------------------------------ */
function tep_get_torihiki_date_radio($start_time,$radio_name="torihiki_time",$default_check=false){
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
          $arr[]=null;
          $arr[]=null;
        }else if($mim_start < 45){
          $mim_start = 45;
          $arr[]=null;
          $arr[]=null;
          $arr[]=null;
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
           sprintf('%02d',$hour).":".sprintf('%02d',$e_start)."'";
      if($default_check){
        $return_str .= " checked >&nbsp;&nbsp;";
        $default_check = false;
      }else{
        $return_str .= " >&nbsp;&nbsp;";
      }
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
    功能: 获取指定顾客的地址信息 
    参数: $customers_id(int) 顾客id 
    参数: $address_id(int) 地址id 
    参数: $html(boolean) 是否html输出 
    参数: $boln(string) 开始标识 
    参数: $eoln(string) 结束标识 
    返回值: 地址信息(array) 
 ------------------------------------ */
  function tep_get_address_by_customers_id($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
    $address_sql = "select address_book_id,entry_firstname as firstname,
        entry_lastname as lastname, entry_firstname_f as firstname_f,
        entry_lastname_f as lastname_f, entry_company as company,
        entry_street_address as street_address, entry_suburb as suburb, entry_city
        as city, entry_postcode as postcode, entry_state as state, entry_zone_id as
        zone_id, entry_country_id as country_id, entry_telephone as telephone from "
        . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customers_id . "'";
    $res_arr = array();
    $address_query = tep_db_query($address_sql);
    $temp_arr = array();
    $temp_arr['text'] = TEXT_SELECTED_ADDRESS_BOOK;
    $temp_arr['value'] = 0; 
    $res_arr[] = $temp_arr;
    while($address = tep_db_fetch_array($address_query)){
      $temp_arr = array();
      $format_id = tep_get_address_format_id($address['country_id']);
      $temp_arr['text'] = tep_address_format($format_id, $address, $html, $boln, $eoln);
      $temp_arr['value'] = $address['address_book_id']; 
      $res_arr[] = $temp_arr;
    
    }
    return $res_arr;
  }

/* -------------------------------------
    功能: 获取指定顾客和指定地址的地址信息 
    参数: $customers_id(int) 顾客id 
    参数: $address_id(int) 地址id 
    参数: $html(boolean) 是否html输出 
    参数: $boln(string) 开始标识 
    参数: $eoln(string) 结束标识 
    返回值: 地址信息(array) 
 ------------------------------------ */
function tep_get_address_by_cid_aid($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
    $address_sql = "select address_book_id,entry_firstname as firstname,
        entry_lastname as lastname, entry_firstname_f as firstname_f,
        entry_lastname_f as lastname_f, entry_company as company,
        entry_street_address as street_address, entry_suburb as suburb, entry_city
        as city, entry_postcode as postcode, entry_state as state, entry_zone_id as
        zone_id, entry_country_id as country_id, entry_telephone as telephone from "
        . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customers_id . "' 
        and address_book_id ='".$address_id."'";
    $res_arr = array();
    $address_query = tep_db_query($address_sql);
    while($address = tep_db_fetch_array($address_query)){
      $temp_arr = array();
      $format_id = tep_get_address_format_id($address['country_id']);
      $temp_arr['text'] = tep_address_format($format_id, $address, $html, $boln, $eoln);
      $temp_arr['value'] = $address['address_book_id']; 
      $res_arr[] = $temp_arr;
    
    }
    if(count($res_arr)>1){
      return $res_arr;
    }else{
      return $res_arr[0];
    }
  }

/* -------------------------------------
    功能: 获取该订单的商品信息 
    参数: $oid(int) 订单id 
    返回值: 商品信息(array) 
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
    功能: 获取优惠券费用 
    参数: $total(int) 总计 
    参数: $orders_id(int) 订单id 
    参数: $site_id(int) 网站id 
    返回值: 优惠券费用(array) 
 ------------------------------------ */
function get_campaion_fee($total, $orders_id, $site_id)
{
  $return_fee = 0; 
  if ($total == 0) {
    return $return_fee; 
  }
  if ($total > 0) {
    $campaion_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$orders_id."' and site_id = '".$site_id."' and campaign_type = '1'"); 
  } else {
    $campaion_query = tep_db_query("select * from ".TABLE_CUSTOMER_TO_CAMPAIGN." where orders_id = '".$orders_id."' and site_id = '".$site_id."' and campaign_type = '2'"); 
  }
 
  $campaion_res = tep_db_fetch_array($campaion_query);
  if ($campaion_res) {
    if (abs($campaion_res['campaign_limit_value']) >= abs($total)) {
      return $return_fee; 
    }
    $percent_pos = strpos($campaion_res['campaign_point_value'], '%');
    if ($percent_pos !== false) {
      $return_fee = $total*substr($campaion_res['campaign_point_value'], 0, -1)/100; 
      if ($return_fee > 0) {
       $return_fee = 0 - $return_fee; 
      }
    } else {
      $return_fee = $campaion_res['campaign_point_value'];
    }
    @eval("\$return_fee = (int)$return_fee;"); 
  }
  return $return_fee;
}

/* -------------------------------------
    功能: 获取优惠券的前页/后页链接 
    参数: $cid(int) 优惠券id 
    参数: $site_id(int) 网站id 
    参数: $st_id(int) 网站id 
    返回值: 优惠券前页/后页链接(array) 
 ------------------------------------ */
function get_campaign_link_page($cid, $site_id, $st_id)
{
  $return_str = '';  
  $campaign_query = tep_db_query("select created_at from ".TABLE_CAMPAIGN." where id = '".$cid."'");
  $campaign_res = tep_db_fetch_array($campaign_query); 
  
  if ($campaign_res) {
    if (empty($st_id)) {
      $pre_campaign_query = tep_db_query("select id from ".TABLE_CAMPAIGN." where id != '".$cid."' and created_at >= '".$campaign_res['created_at']."' order by created_at asc limit 1"); 
    } else {
      $pre_campaign_query = tep_db_query("select id from ".TABLE_CAMPAIGN." where id != '".$cid."' and created_at >= '".$campaign_res['created_at']."' and site_id = '".$site_id."' order by created_at asc limit 1"); 
    }
    $pre_campaign_res = tep_db_fetch_array($pre_campaign_query); 
    if ($pre_campaign_res) {
      $return_str .= '<a href="javascript:void(0)" onclick="show_link_campaign_info(\''.$pre_campaign_res['id'].'\', \''.$st_id.'\');">'.TEXT_CAMPAIGN_PREV.'</a>'; 
    }
    
    if (empty($st_id)) {
      $next_campaign_query = tep_db_query("select id from ".TABLE_CAMPAIGN." where id != '".$cid."' and created_at <= '".$campaign_res['created_at']."' order by created_at desc limit 1"); 
    } else {
      $next_campaign_query = tep_db_query("select id from ".TABLE_CAMPAIGN." where id != '".$cid."' and created_at <= '".$campaign_res['created_at']."' and site_id = '".$site_id."' order by created_at desc limit 1"); 
    }
    $next_campaign_res = tep_db_fetch_array($next_campaign_query); 
    if ($next_campaign_res) {
      $return_str .= '&nbsp;&nbsp;<a href="javascript:void(0)" onclick="show_link_campaign_info(\''.$next_campaign_res['id'].'\', \''.$st_id.'\');">'.TEXT_CAMPAIGN_NEXT.'</a>'; 
    }
  }
  
  return $return_str;
}

/* -------------------------------------
    功能: 获取关联商品在指定时间内的指定网站的购买数量的总和 
    参数: $relate_products_id(int) 商品id 
    参数: $date_sub(int) 时间 
    参数: $site_id(int) 网站id 
    返回值: 购买数量的总和(int) 
 ------------------------------------ */
function tep_get_relate_product_history_sum($relate_products_id,$date_sub,$site_id=0){
  $sql ="select sum(op.products_quantity) as history_sum 
        from ".TABLE_ORDERS_PRODUCTS." op left join ".TABLE_ORDERS.
        " o on op.orders_id=o.orders_id left join ".TABLE_ORDERS_STATUS.
        " os on o.orders_status=os.orders_status_id 
        where 
        op.products_id='".$relate_products_id."'
        and os.calc_price = '1' 
        and op.torihiki_date between ".
        "DATE_SUB('".date('Y-m-d H:i:s')."',INTERVAL '"
        .$date_sub."' DAY) and '".
        date('Y-m-d H:i:s')."' ";
  if($site_id!=0){
    $sql .= " and o.site_id = '".$site_id."' ";
  }
  $query = tep_db_query($sql);
  if($row = tep_db_fetch_array($query)){
    return $row['history_sum'];
  }else{
    return 0;
  }
}

/* -------------------------------------
    功能: 获取属性组的前页/后页链接 
    参数: $group_id(int) 组id 
    参数: $keyword(string) 关键字 
    返回值: 前页/后页链接(string) 
 ------------------------------------ */
function get_option_group_link($group_id, $keyword = '')
{
  $link_str = '';
  $group_query = tep_db_query("select * from ".TABLE_OPTION_GROUP." where id = '".$group_id."'");
  $group = tep_db_fetch_array($group_query);
  
  if ($group) {
    if (trim($keyword) != '') {
      $group_prev_query = tep_db_query("select * from ".TABLE_OPTION_GROUP." where id != '".$group_id."' and created_at >= '".$group['created_at']."' and name like '%".$keyword."%' order by created_at asc limit 1"); 
    } else {
      $group_prev_query = tep_db_query("select * from ".TABLE_OPTION_GROUP." where id != '".$group_id."' and created_at >= '".$group['created_at']."' order by created_at asc limit 1"); 
    }
    $group_prev = tep_db_fetch_array($group_prev_query); 
    if ($group_prev) {
      $link_str .= '<a href="javascript:void(0)" onclick="show_link_group_info(\''.$group_prev['id'].'\', \''.$keyword.'\');">'.TEXT_GROUP_PREV.'</a>'; 
    }
    if (trim($keyword) != '') {
      $group_next_query = tep_db_query("select * from ".TABLE_OPTION_GROUP." where id != '".$group_id."' and created_at <= '".$group['created_at']."'  and name like '%".$keyword."%' order by created_at desc limit 1"); 
    } else {
      $group_next_query = tep_db_query("select * from ".TABLE_OPTION_GROUP." where id != '".$group_id."' and created_at <= '".$group['created_at']."' order by created_at desc limit 1"); 
    }
    $group_next = tep_db_fetch_array($group_next_query); 
    if ($group_next) {
      $link_str .= '&nbsp;&nbsp;<a href="javascript:void(0)" onclick="show_link_group_info(\''.$group_next['id'].'\', \''.$keyword.'\');">'.TEXT_GROUP_NEXT.'</a>'; 
    }
  }
  
  return $link_str;
}

/* -------------------------------------
    功能: 生成组元素的随机字符 
    参数: $length(int) 长度 
    返回值: 随机字符(string) 
 ------------------------------------ */
function tep_get_random_option_item_name($length = 16)
  {
    $pattern = 'abcdefghijklmnopqrstuvwxyz';
    while (true) {
      $key = ''; 
      for($i = 0; $i < $length; $i++) {
        $key .= $pattern[mt_rand(0,25)]; 
      }
      $exists_item_name_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where name = '".$key."'"); 
      if (!tep_db_num_rows($exists_item_name_raw)) {
        return $key; 
      }
    }
  }

/* -------------------------------------
    功能: 获取属性组元素的前页/后页链接 
    参数: $group_id(int) 组id 
    参数: $item_id(int) 元素id 
    返回值: 前页/后页链接(string) 
 ------------------------------------ */
function get_option_item_link($group_id, $item_id)
{
  $link_str = '';
  $item_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$item_id."'");
  $item = tep_db_fetch_array($item_query);
  if ($item) {
    $item_prev_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where group_id = '".$group_id."' and id != '".$item_id."' and created_at >= '".$item['created_at']."' order by created_at asc limit 1"); 
    $item_prev = tep_db_fetch_array($item_prev_query); 
    if ($item_prev) {
      $link_str .= '<a href="javascript:void(0)" onclick="show_link_item_info(\''.$item_prev['id'].'\', \''.$group_id.'\');">'.TEXT_ITEM_PREV.'</a>'; 
      
    }
    
    $item_next_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where group_id = '".$group_id."' and id != '".$item_id."' and created_at <= '".$item['created_at']."' order by created_at desc limit 1"); 
    $item_next = tep_db_fetch_array($item_next_query); 
    if ($item_next) {
      $link_str .= '&nbsp;&nbsp;<a href="javascript:void(0)" onclick="show_link_item_info(\''.$item_next['id'].'\', \''.$group_id.'\');">'.TEXT_ITEM_NEXT.'</a>'; 
    }
  }
  
  return $link_str;
}

/* -------------------------------------
    功能: 把全角的字符,数字和片假名替换成半角 
    参数: $c_str(string) 字符串 
    返回值: 替换后的字符串(string) 
 ------------------------------------ */
function tep_replace_full_character($c_str)
{
  $arr = array(
      'Ａ','Ｂ','Ｃ','Ｄ','Ｅ','Ｆ','Ｇ','Ｈ','Ｉ','Ｊ','Ｋ','Ｌ','Ｍ','Ｎ','Ｏ','Ｐ','Ｑ','Ｒ','Ｓ','Ｔ','Ｕ','Ｖ','Ｗ','Ｘ','Ｙ','Ｚ',
      'ａ','ｂ','ｃ','ｄ','ｅ','ｆ','ｇ','ｈ','ｉ','ｊ','ｋ','ｌ','ｍ','ｎ','ｏ','ｐ','ｑ','ｒ','ｓ','ｔ','ｕ','ｖ','ｗ','ｘ','ｙ','ｚ',
      '１','２','３','４','５','６','７','８','９','０',
      'ｱ','ｲ','ｳ','ｴ','ｵ','ｶ','ｷ','ｸ','ｹ','ｺ','ｻ','ｼ','ｽ','ｾ','ｿ','ﾀ','ﾁ','ﾂ','ﾃ','ﾄ','ﾅ','ﾆ','ﾇ','ﾈ','ﾉ','ﾊ','ﾋ','ﾌ','ﾍ','ﾎ','ﾏ','ﾐ','ﾑ','ﾒ','ﾓ','ﾔ','ﾕ','ﾖ','ﾗ','ﾘ','ﾙ','ﾚ','ﾛ','ﾜ','ﾝ','ｦ',
      ' '
    );
  $arr2 = array(
      'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
      'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
      '1','2','3','4','5','6','7','8','9','0',
      'ア','イ','ウ','エ','オ','カ','キ','ク','ケ','コ','サ','シ','ス','セ','ソ','タ','チ','ツ','テ','ト','ナ','ニ','ヌ','ネ','ノ','ハ','ヒ','フ','ヘ','ホ','マ','ミ','ム','メ','モ','ヤ','ユ','ヨ','ラ','リ','ル','レ','ロ','ワ','ン','ヲ',
      ' '
  );
  
  $c_str = str_replace($arr, $arr2, $c_str);
  return $c_str;
}

/* -------------------------------------
    功能: 判断指定订单商品是否是买取 
    参数: $orders_products_id(int) 订单商品id 
    返回值: 是否是买取(int) 
 ------------------------------------ */
function tep_check_product_type($orders_products_id)
{
  $orders_products_raw = tep_db_query("select * from ".TABLE_ORDERS_PRODUCTS." where orders_products_id = '".$orders_products_id."'");
  $orders_products = tep_db_fetch_array($orders_products_raw);
  
  $product_query = tep_db_query("select products_bflag from " . TABLE_PRODUCTS . " where products_id = '" . $orders_products['products_id'] . "'");
  $product = tep_db_fetch_array($product_query);
  
  if ($product) {
    return $product['products_bflag'];
  } else {
    if ($orders_products['products_price'] < 0) {
      return 1; 
    }
  }
  
  return 0;
}

/* -------------------------------------
    功能: 生成头部提示信息 
    参数: $return_type(int) 类型 
    返回值: 头部提示信息(string) 
 ------------------------------------ */
function tep_get_notice_info($return_type = 0)
{
  global $ocertify;

  //计算剩余的记录
  $notice_order_sql = "select n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at,n.is_show,n.deleted from ".TABLE_NOTICE." n,".TABLE_ALARM." a where n.from_notice=a.alarm_id and n.type = '0' and n.is_show='1' and a.alarm_flag='0' and n.user = '".$ocertify->auth_user."'"; 
  
  $notice_micro_sql = "select n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at,n.is_show,n.deleted,bm.`to` to_users,bm.`from` from_users from ".TABLE_NOTICE." n,".TABLE_BUSINESS_MEMO." bm where n.from_notice=bm.id and n.type = '1' and n.is_show='1' and bm.is_show='1' and bm.deleted='0'";

  $notice_micro_query = tep_db_query($notice_micro_sql);
  $num = 0;
  while($notice_micro_array = tep_db_fetch_array($notice_micro_query)){

    if($notice_micro_array['to_users'] == ''){

      if($notice_micro_array['deleted'] == ''){

        $num++;
      }else{

        $notice_users_array = array();
        $notice_users_array = explode(',',$notice_micro_array['deleted']);
        if(!in_array($ocertify->auth_user,$notice_users_array)){

          $num++;
        }
      }
    }else{

      $users_id_array = array();
      $users_id_array = explode(',',$notice_micro_array['to_users']);
      array_push($users_id_array,$notice_micro_array['from_users']);

      if(in_array($ocertify->auth_user,$users_id_array)){

        if($notice_micro_array['deleted'] == ''){

          $num++;
        }else{

          $notice_users_array = array();
          $notice_users_array = explode(',',$notice_micro_array['deleted']);
          if(!in_array($ocertify->auth_user,$notice_users_array)){

            $num++;
          }
        }
      }
    }
  } 

  $alarm_order_sql = "select n.id,n.type,n.title,n.set_time,n.from_notice,n.user,n.created_at,n.is_show,n.deleted from ".TABLE_NOTICE." n,".TABLE_ALARM." a where n.from_notice=a.alarm_id and n.type = '0' and n.is_show='1' and a.alarm_flag='1'";
  
  $notice_total_sql = "select * from (".$notice_order_sql." union ".$alarm_order_sql.") taf"; 
  $notice_list_raw = tep_db_query($notice_total_sql);  

  //获取下拉列表数据的ID
  $notice_id_array = array();
  $num_i = 0;
  while($notice_list_array = tep_db_fetch_array($notice_list_raw)){

    if($notice_list_array['deleted'] == ''){

      $num_i++;
      $notice_id_array[] = $notice_list_array['id'];
    }else{

      $notice_users_array = array();
      $notice_users_array = explode(',',$notice_list_array['deleted']);
      if(!in_array($ocertify->auth_user,$notice_users_array)){

        $num_i++;
        $notice_id_array[] = $notice_list_array['id'];
      }
    }
  }
  


  $notice_list_num = $num_i+$num;
  tep_db_free_result($notice_list_raw);

  $order_notice_array = array();
  $micro_notice_array = array();

  $order_notice_raw = tep_db_query("select id, type, title, set_time, from_notice, user,created_at,deleted from (".$notice_order_sql." union ".$alarm_order_sql.") taf where type = '0' and is_show='1' order by created_at desc,set_time asc");

  $notice_tmp_num = 0; 
  while($order_notice = tep_db_fetch_array($order_notice_raw)){

    if($order_notice['deleted'] == ''){

      $order_notice_array['id'] = $order_notice['id']; 
      $order_notice_array['title'] = $order_notice['title']; 
      $order_notice_array['set_time'] = $order_notice['set_time']; 
      $order_notice_array['from_notice'] = $order_notice['from_notice']; 
      $order_notice_array['type'] = $order_notice['type'];
      $order_notice_array['user'] = $order_notice['user'];
      $order_notice_array['created_at'] = $order_notice['created_at'];
      unset($notice_id_array[array_search($order_notice['id'],$notice_id_array)]);
      break;
    }else{

      $notice_users_array = array();
      $notice_users_array = explode(',',$order_notice['deleted']);
      if(!in_array($ocertify->auth_user,$notice_users_array)){
        $order_notice_array['id'] = $order_notice['id']; 
        $order_notice_array['title'] = $order_notice['title']; 
        $order_notice_array['set_time'] = $order_notice['set_time']; 
        $order_notice_array['from_notice'] = $order_notice['from_notice']; 
        $order_notice_array['type'] = $order_notice['type'];
        $order_notice_array['user'] = $order_notice['user'];
        $order_notice_array['created_at'] = $order_notice['created_at'];
        unset($notice_id_array[array_search($order_notice['id'],$notice_id_array)]);
        break;
      }
    }
  }

  $order_notice_query = tep_db_query("select id, type, title, set_time, from_notice, user,created_at,deleted from (".$notice_order_sql." union ".$alarm_order_sql.") taf where type = '0' and is_show='1' order by created_at desc,set_time asc");

  $notice_tmp_num = 0; 
  while($order_notice = tep_db_fetch_array($order_notice_query)){

    if($order_notice['deleted'] == ''){

      $notice_tmp_num++; 
      if($notice_tmp_num == 2){break;}
    }else{

      $notice_users_array = array();
      $notice_users_array = explode(',',$order_notice['deleted']);
      if(!in_array($ocertify->auth_user,$notice_users_array)){

        $notice_tmp_num++; 
        if($notice_tmp_num == 2){break;}   
      }
    }
  }
  tep_db_free_result($order_notice_query);

  $notice_num = 0;
  if ($notice_tmp_num) {
    $notice_num = $notice_tmp_num; 
  }

  $micro_notice_raw = tep_db_query("select n.id, n.title, n.set_time, n.from_notice, n.created_at,n.deleted users_deleted,bm.`to` to_users,bm.`from` from_users,bm.icon icon,bm.id bm_id,bm.date_update date_update from ".TABLE_NOTICE." n,".TABLE_BUSINESS_MEMO." bm where n.from_notice=bm.id and n.is_show='1' and bm.is_show='1' and bm.deleted='0' and n.type = '1' order by created_at desc,set_time asc");

  $micro_tmp_num = 0; 
  while($micro_notice = tep_db_fetch_array($micro_notice_raw)){
    if($micro_notice['to_users'] == ''){
      if($micro_notice['users_deleted'] == ''){
        $micro_notice_array['id'] = $micro_notice['id']; 
        $micro_notice_array['title'] = $micro_notice['title']; 
        $micro_notice_array['set_time'] = $micro_notice['set_time']; 
        $micro_notice_array['from_notice'] = $micro_notice['from_notice']; 
        $micro_notice_array['created_at'] = $micro_notice['created_at'];
        $micro_notice_array['icon'] = $micro_notice['icon'];
        $micro_notice_array['bm_id'] = $micro_notice['bm_id'];
        $micro_notice_array['date_update'] = $micro_notice['date_update'];
        unset($notice_id_array[array_search($micro_notice['id'],$notice_id_array)]);
        break;
      }else{

        $notice_users_array = array();
        $notice_users_array = explode(',',$micro_notice['users_deleted']);
        if(!in_array($ocertify->auth_user,$notice_users_array)){

          $micro_notice_array['id'] = $micro_notice['id']; 
          $micro_notice_array['title'] = $micro_notice['title']; 
          $micro_notice_array['set_time'] = $micro_notice['set_time']; 
          $micro_notice_array['from_notice'] = $micro_notice['from_notice']; 
          $micro_notice_array['created_at'] = $micro_notice['created_at'];
          $micro_notice_array['icon'] = $micro_notice['icon'];
          $micro_notice_array['bm_id'] = $micro_notice['bm_id'];
          $micro_notice_array['date_update'] = $micro_notice['date_update'];
          unset($notice_id_array[array_search($micro_notice['id'],$notice_id_array)]);
          break;
        }
      }
    }else{

      $users_id_array = array();
      $users_id_array = explode(',',$micro_notice['to_users']);
      array_push($users_id_array,$micro_notice['from_users']);

      if(in_array($ocertify->auth_user,$users_id_array)){

        if($micro_notice['users_deleted'] == ''){
          $micro_notice_array['id'] = $micro_notice['id']; 
          $micro_notice_array['title'] = $micro_notice['title']; 
          $micro_notice_array['set_time'] = $micro_notice['set_time']; 
          $micro_notice_array['from_notice'] = $micro_notice['from_notice']; 
          $micro_notice_array['created_at'] = $micro_notice['created_at'];
          $micro_notice_array['icon'] = $micro_notice['icon'];
          $micro_notice_array['bm_id'] = $micro_notice['bm_id'];
          $micro_notice_array['date_update'] = $micro_notice['date_update'];
          unset($notice_id_array[array_search($micro_notice['id'],$notice_id_array)]);
          break;
        }else{

          $notice_users_array = array();
          $notice_users_array = explode(',',$micro_notice['users_deleted']);
          if(!in_array($ocertify->auth_user,$notice_users_array)){

            $micro_notice_array['id'] = $micro_notice['id']; 
            $micro_notice_array['title'] = $micro_notice['title']; 
            $micro_notice_array['set_time'] = $micro_notice['set_time']; 
            $micro_notice_array['from_notice'] = $micro_notice['from_notice']; 
            $micro_notice_array['created_at'] = $micro_notice['created_at'];
            $micro_notice_array['icon'] = $micro_notice['icon'];
            $micro_notice_array['bm_id'] = $micro_notice['bm_id'];
            $micro_notice_array['date_update'] = $micro_notice['date_update'];
            unset($notice_id_array[array_search($micro_notice['id'],$notice_id_array)]);
            break;
          }
        }  
      }
    } 
  }

  $micro_notice_query = tep_db_query("select n.id, n.title, n.set_time, n.from_notice, n.created_at,n.deleted users_deleted,bm.`to` to_users,bm.`from` from_users,bm.icon icon from ".TABLE_NOTICE." n,".TABLE_BUSINESS_MEMO." bm where n.from_notice=bm.id and n.is_show='1' and bm.is_show='1' and bm.deleted='0' and n.type = '1' order by created_at desc,set_time asc");

  while($micro_notice = tep_db_fetch_array($micro_notice_query)){
    if($micro_notice['to_users'] == ''){
      if($micro_notice['users_deleted'] == ''){ 
        $micro_tmp_num++;
        if($micro_tmp_num == 2){break;}
      }else{

        $notice_users_array = array();
        $notice_users_array = explode(',',$micro_notice['users_deleted']);
        if(!in_array($ocertify->auth_user,$notice_users_array)){
          
          $micro_tmp_num++;
          if($micro_tmp_num == 2){break;}
        }
      }
    }else{

      $users_id_array = array();
      $users_id_array = explode(',',$micro_notice['to_users']);
      array_push($users_id_array,$micro_notice['from_users']);

      if(in_array($ocertify->auth_user,$users_id_array)){

        if($micro_notice['users_deleted'] == ''){
          
          $micro_tmp_num++;
          if($micro_tmp_num == 2){break;}
        }else{

          $notice_users_array = array();
          $notice_users_array = explode(',',$micro_notice['users_deleted']);
          if(!in_array($ocertify->auth_user,$notice_users_array)){
 
            $micro_tmp_num++;
            if($micro_tmp_num == 2){break;}
          }
        }  
      }
    } 
  }
  tep_db_free_result($micro_notice_query);

  $micro_num = 0;
  if ($micro_tmp_num) {
    $micro_num = $micro_tmp_num; 
  }

  $notice_id_str = implode(',',$notice_id_array);
  
  $show_type = 0; 
  $rise_bg = 0;
  $now_time = strtotime(date('Y-m-d H:i:00', time())); 
  
  if (!empty($order_notice_array)) { 
    $order_notice_time = strtotime($order_notice_array['set_time']); 
    $diff_o_time = $order_notice_time - $now_time; 
    if ($diff_o_time <= 0) {
      $rise_bg = 1;
      $show_type = 1; 
    } else {
      if (!empty($micro_notice_array)) {
        $micro_notice_time = strtotime($micro_notice_array['set_time']); 
        $diff_m_time = $micro_notice_time - $now_time; 
        if ($diff_m_time <= 0) {
          $show_type = 2; 
          $rise_bg = 1;
        } else {
          $o_compare_time = strtotime($order_notice_array['set_time']);
          $m_compare_time = strtotime($micro_notice_array['set_time']);
          
          if ($o_compare_time <= $m_compare_time) {
            $show_type = 1; 
          } else {
            $show_type = 2; 
          }
        }
      } else {
        $show_type = 1; 
      }
    }
  } else {
    if (!empty($micro_notice_array)) {
      $show_type = 2; 
      $micro_notice_time = strtotime($micro_notice_array['set_time']); 
      $diff_m_time = $micro_notice_time - $now_time; 
      if ($diff_m_time <= 0) {
        $rise_bg = 1;
      }
    }
  }

  $alert_flag_query = tep_db_query("select alarm_flag from ".TABLE_ALARM." where alarm_id='".$order_notice_array['from_notice']."'");
  $alert_flag_array = tep_db_fetch_array($alert_flag_query);
  tep_db_free_result($alert_flag_query);
  if($alert_flag_array['alarm_flag'] == '1' && $order_notice_array['created_at'] > $micro_notice_array['created_at']){

    $show_type = 1;
  }
  $more_single = 0; 
  if ($show_type == 1) {
    $order_notice_time = strtotime($order_notice_array['set_time']); 
    $leave_time = $order_notice_time - $now_time; 
    if ($leave_time > 0) {
      $leave_time_day = floor($leave_time/(3600*24));
      $leave_time_tmp = $leave_time % (3600*24);
      $leave_time_seconds = $leave_time_tmp % 3600;
      $leave_time_hour = ($leave_time_tmp - $leave_time_seconds) / 3600;
      $leave_time_minute = $leave_time_seconds % 60;
      $leave_time_minute = ($leave_time_seconds - $leave_time_minute) / 60;
      $leave_date = sprintf('%02d', $leave_time_day).DAY_TEXT.sprintf('%02d', $leave_time_hour).HOUR_TEXT.sprintf('%02d', $leave_time_minute).MINUTE_TEXT;
    } else {
      $leave_date = '00'.DAY_TEXT.'00'.HOUR_TEXT.'00'.MINUTE_TEXT; 
    }
    $html_str = '<table cellspacing="0" cellpadding="0" border="0"  width="100%" id="alarm_delete_'.$order_notice_array['from_notice'].'">';
    $html_str .= '<tr>'; 

    if($order_notice_array['type'] == '0'){
    $alarm_flag_query = tep_db_query("select alarm_id,alarm_flag,alarm_show,orders_flag from ".TABLE_ALARM." where alarm_id='".$order_notice_array['from_notice']."'"); 
    $alarm_flag_array = tep_db_fetch_array($alarm_flag_query);
    tep_db_free_result($alarm_flag_query);
    }
    $html_str .= '<td width="'.($alarm_flag_array['alarm_flag'] == '1' ? "174" : "142").'" id="alert_buttons">'; 
    if($alarm_flag_array['orders_flag'] == '1'){

      $title_str = HEADER_TEXT_ALERT_TITLE;
    }else{
      $title_str = HEADER_TEXT_ALERT_TITLE_PREORDERS; 
    }
    if (($notice_num + $micro_num) > 1) {
      $more_single = 1; 
      $html_str .= '&nbsp;<a href="javascript:void(0);" onclick="expend_all_notice(\''.$order_notice_array['id'].'\');" style="text-decoration:underline; color:#0000ff;"><font color="#0000ff">'.($alarm_flag_array['alarm_flag'] == '0' ? NOTICE_ALARM_TITLE : $title_str).'▼</font></a>'.str_replace('${ALERT_NUM}',$notice_list_num-1,HEADER_TEXT_ALERT_NUM);
    } else { 
      $html_str .= '&nbsp;'.($alarm_flag_array['alarm_flag'] == '0' ? NOTICE_ALARM_TITLE : $title_str);
    }
    $html_str .= '</td>'; 
    $html_str .= '<td class="notice_info"  id="alert_time">'; 
    $alarm_raw = tep_db_query("select orders_id from ".TABLE_ALARM." where alarm_id = '".$order_notice_array['from_notice']."'"); 
    $alarm = tep_db_fetch_array($alarm_raw); 
    $html_str .= '<div style="float:left; width:136px;">'; 
    $html_str .= '<span'.($order_notice_array['type'] == '0' && $alarm_flag_array['alarm_flag'] == '1' ? ' id="leave_time_'.$order_notice_array['id'].'"' : '').'>'.date('Y'.YEAR_TEXT.'m'.MONTH_TEXT.'d'.DAY_TEXT.' H'.TEXT_TORIHIKI_HOUR_STR.'i'.TEXT_TORIHIKI_MIN_STR,strtotime($order_notice_array['created_at'])).'</span>';
    $html_str .= '</div>'; 

    $html_str .= '<div style="float:left; width:16px;margin:3px 8px 0 8px;">';
    $html_str .= '</div>';
    $html_str .= '<div style="float:left;margin-right: 35px;">';
    if($alarm_flag_array['orders_flag'] == '1'){

      $filename_str = FILENAME_ORDERS;
    }else{
      $filename_str = FILENAME_PREORDERS; 
    }
    $html_str .= '<a href="'.tep_href_link($filename_str, 'oID='.$alarm['orders_id'].'&action=edit').'">'.($alarm_flag_array['alarm_flag'] == '0' ? (mb_strlen($order_notice_array['title'],'utf-8') > 30 ? mb_substr($order_notice_array['title'],0,30,'utf-8').'...' : $order_notice_array['title']) : $alarm['orders_id']).'</a>'; 
    $html_str .='</div>';
    if($alarm_flag_array['alarm_flag'] == '1'){
      $html_str .= '<div style="float:left;" id="alarm_user_'.$order_notice_array['from_notice'].'">';
      $html_str .= $order_notice_array['user'].'&nbsp;'.HEADER_TEXT_ALERT_LINK;
      $html_str .='</div>';
      $html_str .='<div style="float:left;">';
      if($alarm_flag_array['alarm_show'] == '1'){
         $html_str .='&nbsp;'.str_replace('${ALERT_TITLE}',(mb_strlen($order_notice_array['title'],'utf-8') > 30 ? mb_substr($order_notice_array['title'],0,30,'utf-8').'...' : $order_notice_array['title']),HEADER_TEXT_ALERT_COMMENT).'/&nbsp;<span id="alarm_id_'.$order_notice_array['from_notice'].'">ON</span>';
      }else{
         $html_str .='&nbsp;'.str_replace('${ALERT_TITLE}',(mb_strlen($order_notice_array['title'],'utf-8') > 30 ? mb_substr($order_notice_array['title'],0,30,'utf-8').'...' : $order_notice_array['title']),HEADER_TEXT_ALERT_COMMENT).'/&nbsp;<span id="alarm_id_'.$order_notice_array['from_notice'].'">OFF</span>'; 
      }
      $html_str .='</div>';
    }else{
      $html_str .= '<div style="float:left;">';
      $html_str .= NOTICE_DIFF_TIME_TEXT.'&nbsp;<span id="leave_time_'.$order_notice_array['id'].'">'.$leave_date.'</span>'; 
      $html_str .= '</div>';
    }
    $html_str .= '</td>'; 
    $html_str .= '<td align="right"  id="alert_close">'; 
    $html_str .= '<a href="javascript:void(0);" onclick="delete_alarm_notice(\''.$order_notice_array['id'].'\', \'0\');"><img src="images/icons/del_img.gif" alt="close"></a>'; 
    $html_str .= '<script type="text/javascript">$(function(){calc_notice_time(\''.strtotime($order_notice_array['set_time']).'\', '.$order_notice_array['id'].', 0, '.$alarm_flag_array['alarm_flag'].', \''.date('Y'.YEAR_TEXT.'m'.MONTH_TEXT.'d'.DAY_TEXT.' H'.TEXT_TORIHIKI_HOUR_STR.'i'.TEXT_TORIHIKI_MIN_STR,strtotime($order_notice_array['created_at'])).'\');});</script>'; 
    $html_str .= '</td>'; 
    $html_str .= '</tr>'; 
    $html_str .= '</table>'; 
    $html_str .= '<input type="hidden" value="'.$more_single.'" name="more_single" id="more_single">'; 
    $html_str .= '<input type="hidden" value="'.$notice_id_str.'" id="notice_id_str">';
    if ($return_type == 1) {
      return $more_single.'|||'.$leave_time.'|||'.$order_notice_array['id'].'|||'.$html_str.'|||'.$alarm_flag_array['alarm_id'].'|||'.$alarm_flag_array['alarm_show'].'|||'.$order_notice_array['user'];
    } 
    return $html_str;
  } else if ($show_type == 2) {
    $micro_notice_time = strtotime($micro_notice_array['set_time']); 
    $leave_time = $micro_notice_time - $now_time; 
    if ($leave_time > 0) {
      $leave_time_day = floor($leave_time/(3600*24));
      $leave_time_tmp = $leave_time % (3600*24);
      $leave_time_seconds = $leave_time_tmp % 3600;
      $leave_time_hour = ($leave_time_tmp - $leave_time_seconds) / 3600;
      $leave_time_minute = $leave_time_seconds % 60;
      $leave_time_minute = ($leave_time_seconds - $leave_time_minute) / 60;
      $leave_date = sprintf('%02d', $leave_time_day).DAY_TEXT.sprintf('%02d', $leave_time_hour).HOUR_TEXT.sprintf('%02d', $leave_time_minute).MINUTE_TEXT;
    
    } else {
      $leave_date = '00'.DAY_TEXT.'00'.HOUR_TEXT.'00'.MINUTE_TEXT; 
    }
    
    $html_str = '<table cellspacing="0" cellpadding="0" border="0"  width="100%">';
    $html_str .= '<tr>'; 
    
    $html_str .= '<td width="142" id="alert_buttons">'; 
    if (($notice_num + $micro_num) > 1) {
      $html_str .= '&nbsp;<a href="javascript:void(0);" onclick="expend_all_notice(\''.$micro_notice_array['id'].'\');" style="text-decoration:underline; color:#0000ff;"><font color="#0000ff">'.NOTICE_EXTEND_TITLE.'▼</font></a>'.str_replace('${ALERT_NUM}',$notice_list_num-1,HEADER_TEXT_ALERT_NUM);
      $more_single = 1; 
    } else {
      $html_str .= '&nbsp;'.NOTICE_EXTEND_TITLE;
    } 
    $html_str .= '</td>'; 
 
    $html_str .= '<td class="notice_info" id="alert_time">';  
    $html_str .= '<div style="float:left; width:136px;">'; 
    $html_str .= date('Y'.YEAR_TEXT.'m'.MONTH_TEXT.'d'.DAY_TEXT.' H'.TEXT_TORIHIKI_HOUR_STR.'i'.TEXT_TORIHIKI_MIN_STR,strtotime($micro_notice_array['created_at'])); 
    $html_str .= '</div>'; 

    //获取图标信息
    $icon_query = tep_db_query("select pic_name,pic_alt from ". TABLE_CUSTOMERS_PIC_LIST ." where id='".$micro_notice_array['icon']."'");
    $icon_array = tep_db_fetch_array($icon_query);
    tep_db_free_result($icon_query);
    $html_str .= '<div id="icon_images_id" style="float:left; width:16px;margin:3px 8px 0 8px;">';
    $html_str .= $micro_notice_array['icon'] != 0 ? tep_image(DIR_WS_IMAGES.'icon_list/'.$icon_array['pic_name'],$icon_array['pic_alt']) : '';
    $html_str .= '</div>';

    $html_str .= '<a href="'.tep_href_link(FILENAME_BUSINESS_MEMO,'cID='.$micro_notice_array['bm_id']).'"><span id="memo_contents">'.(mb_strlen($micro_notice_array['title']) > 30 ? mb_substr($micro_notice_array['title'],0,30,'utf-8').'...' : $micro_notice_array['title']).'</span></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; 
    $html_str .= '&nbsp;<span id="leave_time_'.$micro_notice_array['id'].'" style="display:none;">'.$leave_date.'</span>';
    $html_str .= '</td>'; 
    $html_str .= '<td align="right" id="alert_close">'; 
    $html_str .= '<a href="javascript:void(0);" onclick="delete_micro_notice(\''.$micro_notice_array['id'].'\', \'0\');"><img src="images/icons/del_img.gif" alt="close"></a>'; 
    $html_str .= '<script type="text/javascript">$(function () {calc_notice_time(\''.strtotime($micro_notice_array['set_time']).'\', '.$micro_notice_array['id'].', 0);});</script>'; 
    $html_str .= '</td>'; 
    $html_str .= '</tr>'; 
    $html_str .= '</table>'; 
    $html_str .= '<input type="hidden" value="'.$more_single.'" name="more_single" id="more_single">'; 
    $html_str .= '<input type="hidden" value="'.$micro_notice_array['date_update'].'" name="update_time" id="update_time_id">';
    
    if ($return_type == 1) {
      return $more_single.'|||'.$leave_time.'|||'.$micro_notice_array['id'].'|||'.$html_str.'|||'.($micro_notice_array['icon'] != 0 ? tep_image(DIR_WS_IMAGES.'icon_list/'.$icon_array['pic_name'],$icon_array['pic_alt']) : '').'|||'.(mb_strlen($micro_notice_array['title']) > 30 ? mb_substr($micro_notice_array['title'],0,30,'utf-8').'...' : $micro_notice_array['title']).'|||'.$micro_notice_array['date_update']; 
    }
    return $html_str;
  }
  
  return '';
}

/* -------------------------------------
    功能: 把换行替换成html的换行 
    参数: $string(string) 字符串 
    返回值: 替换后的字符串(string) 
 ------------------------------------ */
function new_nl2br($string) {
  $string = str_replace(array("\r\n", "\r", "\n"), "<br>", $string);
  return $string;
} 

/* -------------------------------------
    功能: 检查该状态是否取消 
    参数: $status_id(int) 状态id 
    返回值: 是否取消(boolean) 
 ------------------------------------ */
function check_order_transaction_button($status_id)
{
  $order_status_raw = tep_db_query("select is_cancle from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$status_id."'");
  $order_status = tep_db_fetch_array($order_status_raw);
  if ($order_status['is_cancle'] == '1') {
    return true;
  }
  return false;
}

/* -------------------------------------
    功能: 检测该订单的最近的状态 
    参数: $oid(int) 订单id 
    返回值: 完成/取消(boolean) 
 ------------------------------------ */
function check_order_latest_status($oid)
{
   $orders_raw = tep_db_query("select orders_status from ".TABLE_ORDERS." where orders_id = '".$oid."'"); 
   $orders_info = tep_db_fetch_array($orders_raw);
   if ($orders_info) {
     $orders_status_raw = tep_db_query("select finished from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$orders_info['orders_status']."'");    
     $orders_status = tep_db_fetch_array($orders_status_raw); 
     if ($orders_status) {
       if ($orders_status['finished'] == '1') {
         return true; 
       }
       $orders_status_history_raw = tep_db_query("select orders_status_id from ".TABLE_ORDERS_STATUS_HISTORY." where orders_id = '".$oid."' order by date_added desc");  
       while ($orders_status_history = tep_db_fetch_array($orders_status_history_raw)) {
         $tmp_orders_status_raw = tep_db_query("select finished, is_cancle from ".TABLE_ORDERS_STATUS." where orders_status_id = '".$orders_status_history['orders_status_id']."'");    
         $tmp_orders_status = tep_db_fetch_array($tmp_orders_status_raw); 
         if ($tmp_orders_status) {
           if ($tmp_orders_status['is_cancle'] == '1') {
             return false; 
           }
           if ($tmp_orders_status['finished'] == '1') {
             return true; 
           }
         }
       }
     }
   }
   return false;
}

/* -------------------------------------
    功能: 检查该同业者是否在指定列表里 
    参数: $d_id(int) 同业者id 
    参数: $dougyousya(array) 同业者id 
    返回值: 是否在(boolean) 
 ------------------------------------ */
function check_in_dougyousya($d_id, $dougyousya)
{
  foreach ($dougyousya as $d) {
    if ($d['dougyousya_id'] === $d_id) {
      return true;
    }
  }
  return false;
}

/* -------------------------------------
    功能: 获取指定商品的详细信息 
    参数: $pid(int) 商品id 
    参数: $site_id(int) 网站id 
    返回值: 商品的详细信息(object) 
 ------------------------------------ */
function tep_get_pinfo_by_pid($pid,$site_id=0)
{
  global $languages_id;
  $product_query = tep_db_query("
          select pd.products_name, 
                 pd.products_description, 
                 pd.products_url, 
                 pd.romaji, 
                 p.products_attention_5,
                 p.products_id,
                 p.option_type, 
                 p.products_real_quantity + p.products_virtual_quantity as products_quantity,
                 p.products_real_quantity, 
                 p.products_virtual_quantity, 
                 p.products_model, 
                 p.products_image,
                 p.products_image2,
                 p.products_image3, 
                 p.products_price, 
                 p.products_price_offset,
                 p.products_weight, 
                 p.products_user_added,
                 p.products_date_added, 
                 pd.products_last_modified, 
                 pd.products_user_update,
                 date_format(p.products_date_available, '%Y-%m-%d') as products_date_available, 
                 p.products_shipping_time,
                 p.products_weight,
                 pd.products_status, 
                 p.products_tax_class_id, 
                 p.manufacturers_id, 
                 p.products_bflag, 
                 p.products_cflag, 
                 p.relate_products_id,
                 p.sort_order,
                 p.max_inventory,
                 p.min_inventory,
                 p.products_small_sum,
                 p.products_cartflag ,
                 p.products_cart_buyflag,
                 p.products_cart_image,
                 p.products_cart_min,
                 p.products_cartorder,
                 p.belong_to_option,
                 pd.preorder_status
          from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
          where p.products_id = '" . $pid . "' 
            and p.products_id = pd.products_id 
            and pd.language_id = '" . $languages_id . "' 
            and pd.site_id = '".(tep_products_description_exist($pid, $site_id, $languages_id)?$site_id:0)."'");
      $product = tep_db_fetch_array($product_query);
       $reviews_query = tep_db_query("select 
           (avg(reviews_rating) / 5 * 100) as average_rating from " 
           . TABLE_REVIEWS . " where 
           products_id = '" . $product['products_id'] . "'");
      $reviews = tep_db_fetch_array($reviews_query);
      $pInfo_array = tep_array_merge($product, $reviews);
      $pInfo = new objectInfo($pInfo_array);
      return $pInfo;
}

/* -------------------------------------
    功能: 判断是否出现结束标识符 
    参数: 无 
    返回值: 无 
 ------------------------------------ */
function tep_isset_eof()
{
   $referer_url = $_SERVER['HTTP_REFERER'];
   if(preg_match('/&eof=error/',$referer_url)){
     $referer_url = str_replace('&eof=error','',$referer_url);
   }
   if(preg_match('/eof=error/',$referer_url)){
     $referer_url = str_replace('eof=error','',$referer_url);
   }
   if(!isset($_POST['eof'])||$_POST['eof']!='eof'){
     if (preg_match('/php\?/',$referer_url)){
       tep_redirect($referer_url.'&eof=error');
     }else{
       tep_redirect($referer_url.'?eof=error');
     }
     exit;
   }
}

/* -------------------------------------
    功能: 获取js文件信息 
    参数: $name(string) 文件名 
    参数: $path(string) 路径 
    参数: $type(string) 类型 
    返回值: 无 
 ------------------------------------ */
function tep_get_javascript($name,$path='',$type='js'){
  global $language; 
  if($name&&$type=='js'&&$path){
    // name not empty and type is js require file *.php.js
    $path = str_replace('|','/',$path);
    if(preg_match('/^[_a-zA-Z][A-Za-z0-9_]+$/',$name)){
      if(file_exists(DIR_WS_LANGUAGES.$language .'/javascript/'.$name.'.php')){
        require_once(DIR_WS_LANGUAGES.$language .'/javascript/'.$name.'.php');
      }
      $file_path = $path.'/'.$name.".js.php";
      if(file_exists($file_path)){
        require_once($file_path);
      }
    }
  }
}

/* -------------------------------------
    功能: 生成0-9的下拉列表 
    参数: $name_str(string) 名 
    参数: $id_str(string) id值 
    参数: $default_value(string) 默认值 
    返回值: 下拉列表(string) 
 ------------------------------------ */
function tep_get_time_select_symbol($name_str, $id_str, $default_value) {
  $html_str = '<select name="'.$name_str.'" id="'.$id_str.'" style="padding:0;margin:0;">';
  for ($i = 0; $i <= 9; $i++) {
    $html_str .= '<option value="'.$i.'"'.(($default_value == $i)?' selected':'').'>'.$i.'</option>';  
  }
  $html_str .= '</select>';
  return $html_str;
}

/* -------------------------------------
    功能: 生成信号灯html 
    参数: $default_value(string) 默认值 
    返回值: 信号灯html(string) 
 ------------------------------------ */
function tep_cfg_time_select($default_value) {
  $string = '';
  $default_info_array = @unserialize(stripslashes($default_value));
  $string .= SIGNAL_GREEN.'&nbsp;&nbsp;'.NOW_TIME_TEXT.'&nbsp;&nbsp;<p style="padding:0;margin:0;">'.tep_get_time_select_symbol('configuration_value[green][]', 'green_1', $default_info_array['green'][0]).tep_get_time_select_symbol('configuration_value[green][]', 'green_2', $default_info_array['green'][1]).tep_get_time_select_symbol('configuration_value[green][]', 'green_3', $default_info_array['green'][2]).tep_get_time_select_symbol('configuration_value[green][]', 'green_4', $default_info_array['green'][3]).NOW_TIME_LINK_TEXT.'</p><br>'; 
  
  $string .= SIGNAL_YELLOW.'&nbsp;&nbsp;'.NOW_TIME_TEXT.'&nbsp;&nbsp;<p style="padding:0;margin:0;">'.tep_get_time_select_symbol('configuration_value[yellow][]', 'yellow_1', $default_info_array['yellow'][0]).tep_get_time_select_symbol('configuration_value[yellow][]', 'yellow_2', $default_info_array['yellow'][1]).tep_get_time_select_symbol('configuration_value[yellow][]', 'yellow_3', $default_info_array['yellow'][2]).tep_get_time_select_symbol('configuration_value[yellow][]', 'yellow_4', $default_info_array['yellow'][3]).NOW_TIME_LINK_TEXT.'</p><br>'; 
  
  $string .= SIGNAL_RED.'&nbsp;&nbsp;'.NOW_TIME_TEXT.'&nbsp;&nbsp;<p style="padding:0;margin:0;">'.tep_get_time_select_symbol('configuration_value[red][]', 'red_1', $default_info_array['red'][0]).tep_get_time_select_symbol('configuration_value[red][]', 'red_2', $default_info_array['red'][1]).tep_get_time_select_symbol('configuration_value[red][]', 'red_3', $default_info_array['red'][2]).tep_get_time_select_symbol('configuration_value[red][]', 'red_4', $default_info_array['red'][3]).NOW_TIME_LINK_TEXT.'</p><br>'; 
  
  
  $string .= SIGNAL_BLNK.'&nbsp;&nbsp;'.SIGNAL_BLINK_READ_TEXT;

  return $string;
}

/* -------------------------------------
    功能: 根据最近修改时间判断信号灯的状态 
    参数: $last_modified_info(string) 修改时间 
    返回值: 信号灯的状态(string) 
 ------------------------------------ */
function tep_get_signal_pic_info($last_modified_info) {
  $last_modified_str = date('n/j H:i:s', strtotime(tep_datetime_short($last_modified_info))); 
  $origin_last_modified_time = strtotime($last_modified_info);

  $html_str = tep_image(DIR_WS_ICONS.'info_blink.gif', tep_datetime_short($last_modified_info));
  $now_time = time();
  
  $set_time_array = unserialize(get_configuration_by_site_id('DS_ADMIN_SIGNAL_TIME', '0'));
  
  $set_time_part_1 = (int)($set_time_array['green'][0].$set_time_array['green'][1].$set_time_array['green'][2].$set_time_array['green'][3]); 
  $set_time_part_2 = (int)($set_time_array['yellow'][0].$set_time_array['yellow'][1].$set_time_array['yellow'][2].$set_time_array['yellow'][3]); 
  $set_time_part_3 = (int)($set_time_array['red'][0].$set_time_array['red'][1].$set_time_array['red'][2].$set_time_array['red'][3]); 
  
  if ($origin_last_modified_time >= ($now_time - $set_time_part_1*60*60)) {
    $html_str = tep_image(DIR_WS_ICONS.'info_green.gif', tep_datetime_short($last_modified_info));
  } else if ($origin_last_modified_time >= ($now_time - $set_time_part_2*60*60)) {
    $html_str = tep_image(DIR_WS_ICONS.'info_yellow.gif', tep_datetime_short($last_modified_info));
  } else if ($origin_last_modified_time >= ($now_time - $set_time_part_3*60*60)) {
    $html_str = tep_image(DIR_WS_ICONS.'info_red.gif', tep_datetime_short($last_modified_info));
  }
  
  return $html_str;
}

/* -------------------------------------
    功能: 获取该顾客在指定网站的订单数量 
    参数: $customers_id(int) 顾客id 
    参数: $site_id(int) 网站id 
    返回值: 订单数量(int) 
 ------------------------------------ */
function tep_get_orders_by_customers_id($customers_id,$site_id){
  $sql = "select distinct o.orders_id from orders o 
    where  o.customers_id = '".$customers_id."' 
    and o.site_id ='".$site_id."'";
  $query = tep_db_query($sql);
  $arr = array();
  while($row = tep_db_fetch_array($query)){
    if(!in_array($row['orders_id'],$arr)){
      $arr[] = $row['orders_id'];
    }
  }
  return count($arr);
}

/* -------------------------------------
    功能: 获取当前页的memo的z-index的最大值 
    参数: $belong_str(string) 所属页面 
    返回值: z-index的最大值(int) 
 ------------------------------------ */
function tep_get_note_top_layer($belong_str) 
{
  $z_index = '1';
 
  $note_list_raw = mysql_query("select xyz from notes where belong = '".$belong_str."'");
  $note_list_array = array();
  
  while ($note_list_res = mysql_fetch_array($note_list_raw)) {
    $note_list_tmp_array = explode('|', $note_list_res['xyz']); 
    $note_list_array[] = $note_list_tmp_array[2]; 
  }
  
  if (!empty($note_list_array)) {
    $z_index = max($note_list_array) + 1; 
  }
  
  return $z_index;
}

/* -------------------------------------
    功能: 检查订单的商品的option是否不足 
    参数: $opa_id(string) 订单id 
    参数: $is_pre_single(boolean) 是否是预约 
    返回值: 是否不足(boolean) 
 ------------------------------------ */
function tep_check_less_option_product($opa_id, $is_pre_single = false)
{
  $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>"); 
  if ($is_pre_single) {
    $orders_products_raw = tep_db_query("select orders_id, products_id from ".TABLE_PREORDERS_PRODUCTS." where orders_products_id = '".$opa_id."'"); 
  } else {
    $orders_products_raw = tep_db_query("select orders_id, products_id from ".TABLE_ORDERS_PRODUCTS." where orders_products_id = '".$opa_id."'"); 
  }
  $orders_products = tep_db_fetch_array($orders_products_raw); 
  //检查订单商品是否存在 
  if ($orders_products) {
    $exists_products_raw = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$orders_products['products_id']."'"); 
    $exists_products = tep_db_fetch_array($exists_products_raw);
    //检查商品是否存在 
    if ($exists_products) {
      if ($is_pre_single) {
        $item_list_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where status = '1' and group_id = '".$exists_products['belong_to_option']."' and place_type = '0'");  
      } else {
        $item_list_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where status = '1' and group_id = '".$exists_products['belong_to_option']."'");  
      }
      //判断该商品说对应组的元素是否存在 
      if (tep_db_num_rows($item_list_raw)) {
        $item_list_array = array();
        while ($item_list = tep_db_fetch_array($item_list_raw)) {
          $item_list_array[] = $item_list; 
        }
        $orders_attr_array = array(); 
        if ($is_pre_single) {
          $orders_attr_raw = tep_db_query("select option_info, option_group_id, option_item_id from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id = '".$opa_id."'"); 
        } else {
          $orders_attr_raw = tep_db_query("select option_info, option_group_id, option_item_id from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id = '".$opa_id."'"); 
        }
        while ($orders_attr = tep_db_fetch_array($orders_attr_raw)) {
          $orders_attr_array[] = $orders_attr; 
        }
        $attr_num = count($item_list_array);
        if (!empty($orders_attr_array)) {
          $attr_tmp_num = count($orders_attr_array);
        } else {
          $attr_tmp_num = 0;
        }
        if ($attr_num != $attr_tmp_num) {
          return true; 
        }
        $at_exclude_array = array();
        foreach ($orders_attr_array as $att_key => $att_value) {
          $att_option_info = @unserialize(stripslashes($att_value['option_info'])); 
          if ($att_value['option_group_id'] != '0' || $att_value['option_item_id'] != '0') {
            $exists_item_raw = tep_db_query("select * from ".TABLE_OPTION_ITEM." where group_id = '".(int)$att_value['option_group_id']."' and id = '".$att_value['option_item_id']."' and status = '1'"); 
            $exists_item = tep_db_fetch_array($exists_item_raw); 
            if ($exists_item) {
              $ao_option = @unserialize(stripslashes($exists_item['option'])); 
              if ($exists_item['type'] == 'radio') {
                $aop_single = false;
                foreach ($ao_option['radio_image'] as $r_key => $r_value) {
                  if (trim(str_replace($replace_arr, '', nl2br(stripslashes($r_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($att_option_info['value']))))) {
                    $aop_single = true;
                    break;
                  }
                }
                if (!$aop_single) {
                  return true; 
                }
              } else if ($exists_item['type'] == 'text') {
                if (trim(str_replace($replace_arr, '', nl2br(stripslashes($ao_option['itextarea'])))) != trim(str_replace($replace_arr, '', nl2br(stripslashes($att_option_info['value']))))) {
                  return true; 
                }
              } else if ($exists_item['type'] == 'select') {
                if (!empty($ao_option['se_option'])) {
                  $ao_se_single = false; 
                  foreach ($ao_option['se_option'] as $se_key => $se_value) {
                    if ($se_value == $att_option_info['value']) {
                      $ao_se_single = true;
                      break;
                    }
                  }
                  if (!$ao_se_single) {
                    return true; 
                  }
                } else {
                  return true; 
                }
              }
              if (!empty($att_value['option_item_id'])) {
                $at_exclude_array[] = $att_value['option_item_id']; 
              }
            } else {
              return true; 
            }
          } else {
            $is_exists = false; 
            foreach ($item_list_array as $it_key => $item_list_info) {
              if (in_array($item_list_info['id'], $at_exclude_array)) {
                continue; 
              }
              if ($item_list_info['front_title'] = $att_option_info['title']) {
                $ao_option = @unserialize(stripslashes($item_list_info['option_info'])); 
                if ($item_list_info['type'] == 'radio') {
                  foreach ($ao_option['radio_image'] as $r_key => $r_value) {
                     if (trim(str_replace($replace_arr, '', nl2br(stripslashes($r_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($att_option_info['value']))))) {
                       $at_exclude_array[] = $item_list_info['id']; 
                       $is_exists = true; 
                       break;
                     }
                  }
                } else if ($item_list_info['type'] == 'text') {
                  if (trim(str_replace($replace_arr, '', nl2br(stripslashes($ao_option['itextarea'])))) != trim(str_replace($replace_arr, '', nl2br(stripslashes($att_option_info['value']))))) {
                    $at_exclude_array[] = $item_list_info['id']; 
                    $is_exists = true; 
                  }
                } else if ($item_list_info['type'] == 'select') {
                  if (!empty($ao_option['se_option'])) {
                    foreach ($ao_option['se_option'] as $se_key => $se_value) {
                      if ($se_value == $att_option_info['value']) {
                        $at_exclude_array[] = $item_list_info['id']; 
                        $is_exists = true; 
                        break;
                      }
                    }
                  } 
                } else {
                  $at_exclude_array[] = $item_list_info['id']; 
                }
              }
            }
          }
        }
      } else {
        if ($is_pre_single) {
          $orders_attr_raw = tep_db_query("select option_info, option_group_id, option_item_id from ".TABLE_PREORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id = '".$opa_id."' limit 1"); 
        } else {
          $orders_attr_raw = tep_db_query("select option_info, option_group_id, option_item_id from ".TABLE_ORDERS_PRODUCTS_ATTRIBUTES." where orders_products_id = '".$opa_id."' limit 1"); 
        }
        $orders_attr = tep_db_fetch_array($orders_attr_raw);
        if ($orders_attr) {
          return true; 
        }
      }
    } else {
      return true; 
    }
  } else {
    return true; 
  }
  return false;
}

/* -------------------------------------
    功能: 检查指定商品的option是否不足 
    参数: $products_id(string) 商品id 
    参数: $pro_attr_info(array) 商品属性 
    返回值: 是否不足(boolean) 
 ------------------------------------ */
function tep_check_less_option_product_by_products_id($products_id, $pro_attr_info)
{
  $replace_arr = array("<br>", "<br />", "<br/>", "\r", "\n", "\r\n", "<BR>"); 
  $exists_product_raw = tep_db_query("select belong_to_option from ".TABLE_PRODUCTS." where products_id = '".$products_id."'"); 
  $exists_product = tep_db_fetch_array($exists_product_raw); 
  if ($exists_product) {
    $item_list_array = array(); 
    $item_list_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where group_id = '".$exists_product['belong_to_option']."' and status = '1'");
    if (tep_db_num_rows($item_list_query)) {
      while ($item_list = tep_db_fetch_array($item_list_query)) {
        $item_list_array[] = $item_list; 
      }
      $op_num = count($item_list_array); 
      if (!empty($pro_attr_info)) {
        $op_tmp_num = count($pro_attr_info);
      } else {
        $op_tmp_num = 0; 
      }
      if ($op_num != $op_tmp_num) {
        return true; 
      }
      foreach ($pro_attr_info as $p_key => $p_value) {
        $item_info_query = tep_db_query("select * from ".TABLE_OPTION_ITEM." where id = '".$p_value['option_item_id']."' and status = '1'"); 
        $item_info = tep_db_fetch_array($item_info_query);
        if ($item_info) {
          $ao_option = @unserialize($item_info['option']); 
          if ($item_info['type'] == 'radio') {
            $aop_single = false;  
            foreach ($ao_option['radio_image'] as $r_key => $r_value) {
              if (trim(str_replace($replace_arr, '', nl2br(stripslashes($r_value['title'])))) == trim(str_replace($replace_arr, '', nl2br(stripslashes($p_value['option_info']['value']))))) {
                $aop_single = true;
                break;
              }
            }
            if (!$aop_single) {
              return true; 
            }
          } else if ($item_info['type'] == 'text') {
            if (trim(str_replace($replace_arr, '', nl2br(stripslashes($ao_option['itextarea'])))) != trim(str_replace($replace_arr, '', nl2br(stripslashes($p_value['option_info']['value']))))) {
              return true; 
            } 
          } else if ($item_info['type'] == 'select') {
            if (!empty($ao_option['se_option'])) {
              $ao_se_single = false;
              foreach ($ao_option['se_option'] as $se_key => $se_value) {
                if ($se_value == $p_value['option_info']['value']) {
                  $ao_se_single = true;
                  break; 
                }
              }
              if (!$ao_se_single) {
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
      if (!empty($pro_attr_info)) {
        return true; 
      }
    }
  } else {
    return true; 
  }
  return false;
}

/* -------------------------------------
    功能: 读取所有订单状态是否标记了交易过期警告 
    参数: 无 
    返回值: 是否标记了交易过期警告的数组(array) 
 ------------------------------------ */
function check_orders_transaction_expired()
{
  $orders_expired_array = array();
  $order_status_raw = tep_db_query("select orders_status_id,transaction_expired from ".TABLE_ORDERS_STATUS);
  while($order_status = tep_db_fetch_array($order_status_raw)){

    $orders_expired_array[$order_status['orders_status_id']] = $order_status['transaction_expired'];
  } 
  tep_db_free_result($order_status_raw);
  return $orders_expired_array;
}

/* -------------------------------------
    功能: 获取预约订单状态的下拉列表 
    参数: $order_status_id(int) 预约订单状态id 
    参数: $key(string) 列表名 
    返回值: 预约订单状态的下拉列表(string) 
 ------------------------------------ */
function tep_cfg_pull_down_preorder_statuses($order_status_id, $key = '') {
  global $languages_id;

  $name = (($key) ? 'configuration[' . $key . ']' : 'configuration_value');

  $statuses_array = array(array('id' => '0', 'text' => TEXT_DEFAULT));
  $statuses_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_PREORDERS_STATUS . " where language_id = '" . $languages_id . "' order by orders_status_name");
  while ($statuses = tep_db_fetch_array($statuses_query)) {
    $statuses_array[] = array('id' => $statuses['orders_status_id'],
        'text' => $statuses['orders_status_name']);
  }

  return tep_draw_pull_down_menu($name, $statuses_array, $order_status_id);
}

/* -------------------------------------
    功能: 获取指定的预约订单状态的名字 
    参数: $order_status_id(int) 预约订单状态id 
    参数: $language_id(int) 语言id 
    返回值: 预约订单状态的名字(string) 
 ------------------------------------ */
function tep_get_preorder_status_name($order_status_id, $language_id = '') {
  global $languages_id;

  if ($order_status_id < 1) return TEXT_DEFAULT;

  if (!is_numeric($language_id)) $language_id = $languages_id;

  $status_query = tep_db_query("select orders_status_name from " . TABLE_PREORDERS_STATUS . " where orders_status_id = '" . $order_status_id . "' and language_id = '" . $language_id . "'");
  $status = tep_db_fetch_array($status_query);

  return $status['orders_status_name'];
}

/* -------------------------------------
    功能: 生成月份列表  
    参数: $month(int) 几个月 
    参数: $empty_params(string) URL参数
    参数: $params(string) URL参数
    返回值: 月份列表(string)
 ------------------------------------ */
function tep_cfg_pull_down_month_list($month,$empty_params ='',$params = '') {

  $month_array = array();
  for($i=1;$i<=12;$i++){

    $month_array[] = array('id'=>$i,'text'=>$i);
  }
  if($params != ''){
    return TEXT_CALENDAR_SETTING_MONTH_START.tep_draw_pull_down_menu('configuration_value', $month_array, $month,$params).TEXT_CALENDAR_SETTING_MONTH_END;
  }else{
    return TEXT_CALENDAR_SETTING_MONTH_START.tep_draw_pull_down_menu('configuration_value', $month_array, $month).TEXT_CALENDAR_SETTING_MONTH_END;
  }
}
/*-----------------------------------
    功能: 网站显示
    参数: $filename (string) 页面
    参数: $ca_single(boolean) 是否过滤指定的url参数 
    参数: $show_all (array) 是否特殊显示ALL 和其他网站
    返回值: 网站的列表(string) 
-----------------------------------*/
function tep_show_site_filter($filename,$ca_single=false,$show_all=array()){
  global $_GET, $_POST, $ocertify;
  $site_list_array = array();
  $site_array = array();
  $site_list_query = tep_db_query("select id,romaji from ". TABLE_SITES);
  $site_list_array[0] = 'all';
  $site_array[] = '0';
  while($site_list_rows = tep_db_fetch_array($site_list_query)){
    $site_list_array[$site_list_rows['id']] = $site_list_rows['romaji'];
    $site_array[] = $site_list_rows['id'];
  }
  if(!empty($show_all)){
    $show_site_list = array_diff($site_array,$show_all);
  }else{
    $show_site_list = $site_array;
  }
  tep_db_free_result($site_list_query);
  $user_info = tep_get_user_info($ocertify->auth_user);
  //获得用户ID 和 当前页面 取得设置的显示网站列表
  $userid = $user_info['userid'];
  $page = $filename;
  $show_site_sql = "select * from ".TABLE_SHOW_SITE." WHERE user='".$userid."' and page='".$page."' limit 1";
  $show_id = '';
  $show_site_query = tep_db_query($show_site_sql);
  if($show_site_rows = tep_db_fetch_array($show_site_query)){
    $site_array = explode('-',$show_site_rows['site']);
    $site_id = $show_site_rows['show_id'];
  }
  $unshow_list = array();
  // 循环输出所有网站列表 ALL 需要特殊处理
  ?>
    <div id="tep_site_filter">
    <input type="hidden" id="show_site_id" value="<?php echo implode('-',$show_site_list);?>">
  <?php
              foreach ($site_list_array as $sid => $sromaji) {
               $site = array();
               $site['id'] = $sid;
               $site['romaji'] = $sromaji;
               if(!empty($show_all)){
                 if(in_array($site['id'],$show_all)){
                   $unshow_list[] = $site['id'];
                 ?>
              <span id="site_<?php echo $site['id'];?>" class="site_filter_unselected"><?php echo $site['romaji'];?></a></span>  
                 <?php
                 continue;
                 }
               }
               if(!isset($_GET['site_id']) || trim($_GET['site_id']) == ''){
                if(in_array($site['id'],$site_array)){
           ?>  
                <span id="site_<?php echo $site['id'];?>" class="site_filter_selected"><a href="javascript:void(0);" onclick="change_show_site(<?php echo $site['id'];?>,0,'<?php echo $_GET['site_id'];?>','<?php echo urlencode(tep_get_all_get_params(array('page', 'site_id')));?>', '<?php echo $filename;?>');"><?php echo $site['romaji'];?></a></span>
          <?php
               }else{
          ?>
              <span id="site_<?php echo $site['id'];?>"><a href="javascript:void(0);" onclick="change_show_site(<?php echo $site['id'];?>,1,'<?php echo $_GET['site_id'];?>','<?php echo urlencode(tep_get_all_get_params(array('page', 'site_id')));?>', '<?php echo $filename;?>');"><?php echo $site['romaji'];?></a></span>  
          <?php
               }
               }else{
                 $site_id_array = explode('-',$_GET['site_id']); 
                 if(in_array($site['id'],$site_id_array)){
          ?>
              <span id="site_<?php echo $site['id'];?>" class="site_filter_selected"><a href="javascript:void(0);" onclick="change_show_site(<?php echo $site['id'];?>,0,'<?php echo $_GET['site_id'];?>','<?php echo urlencode(tep_get_all_get_params(array('page', 'site_id')));?>', '<?php echo $filename;?>');"><?php echo $site['romaji'];?></a></span>
          <?php
               }else{
          ?>
              <span id="site_<?php echo $site['id'];?>"><a href="javascript:void(0);" onclick="change_show_site(<?php echo $site['id'];?>,1,'<?php echo $_GET['site_id'];?>','<?php echo urlencode(tep_get_all_get_params(array('page', 'site_id')));?>', '<?php echo $filename;?>');"><?php echo $site['romaji'];?></a></span>
<?php
               }
               }
              }
  ?></div><?php
  if(!empty($show_all)&&!empty($unshow_list)){
    ?>
    <input type="hidden" id="unshow_site_list" value="<?php
    echo implode('-',$unshow_list);?>">
    <?php
  }else{
    echo '<input type="hidden" id="unshow_site_list" value="">';
  }
}

/*-----------------------------------
    功能: 获得指定页面的设置的网站id
    参数: $current_page (string) 指定页面
    返回值: 网站id列表(string) 
-----------------------------------*/
function tep_get_setting_site_info($current_page)
{
  global $ocertify;
  $site_list_array = array(); 
  $site_list_query = tep_db_query("select * from sites");
  $site_list_array[] = 0; 
  while ($site_list_info = tep_db_fetch_array($site_list_query)) {
    $site_list_array[] = $site_list_info['id']; 
  }
  sort($site_list_array); 
  $exists_site_query = tep_db_query("select * from show_site where user = '".$ocertify->auth_user."' and page = '".$current_page."'");
  $exists_site = tep_db_fetch_array($exists_site_query);
  if ($exists_site) {
    $return_site = explode('-', $exists_site['site']);
    if (!empty($return_site)) {
      return implode(',', $return_site); 
    } else {
      return implode(',', $site_list_array); 
    }
  } 
  return implode(',', $site_list_array); 
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

/*-----------------------------------
    功能: 删除超时的未认证顾客 
    参数: 无 
    返回值: 无 
-----------------------------------*/
function tep_customers_not_certified_timeout()
{
  $customers_id_array = array();
  $customers_query = tep_db_query("select customers_id from ".TABLE_CUSTOMERS." where is_active = '0' and send_mail_time!='' and datediff(now(),from_unixtime(send_mail_time,'%Y-%m-%d %H:%i:%s'))>3");
  while($customers_array = tep_db_fetch_array($customers_query)){
 
    $customers_id_array[] = $customers_array['customers_id'];  
  }
  tep_db_free_result($customers_query);
  $customers_id_str = implode(',',$customers_id_array);

  if(!empty($customers_id_array)){ 
    //删除关联数据
    tep_db_query("delete from ".TABLE_CUSTOMERS." where customers_id in (".$customers_id_str.")");
    tep_db_query("delete from ".TABLE_CUSTOMERS_INFO." where customers_info_id in (".$customers_id_str.")");
    tep_db_query("delete from ".TABLE_ADDRESS_BOOK." where customers_id in (".$customers_id_str.")");
    tep_db_query("delete from ".TABLE_CUSTOMERS_BASKET." where customers_id in (".$customers_id_str.")");
    tep_db_query("delete from ".TABLE_CUSTOMERS_BASKET_OPTIONS." where customers_id in (".$customers_id_str.")"); 
  }
}

/*------------------------------------
 功能：验证邮件
 参数: $email(string) 用户邮件
 返回值：验证邮箱成功或者失败(boolean)
 -----------------------------------*/
function tep_validate_new_email($email) {
  $isValid = true;
  $atIndex = strrpos($email, "@");
  if (is_bool($atIndex) && !$atIndex) {
    $isValid = false;
  } else {
    $domain = substr($email, $atIndex+1);
    $local = substr($email, 0, $atIndex);
    $localLen = strlen($local);
    $domainLen = strlen($domain);
    if ($localLen < 1 || $localLen > 64) {
      // front @ length 
      $isValid = false;
    } else if ($domainLen < 1 || $domainLen > 255) {
      // back @  length 
      $isValid = false;
    } else if ($local[0] == '.') {
      // dot at start or end
      $isValid = false;
    } else if (!preg_match('/^[\]\\:[A-Za-z0-9\\-\\.]+$/', $domain)) {
      // character not valid in domain part
      $isValid = false;
    } else if (preg_match('/\\.\\./', $domain)||preg_match('/^\./',$domain)) {
      // domain part has two consecutive dots
      $isValid = false;
    } else if(!preg_match('/^(\\\\."|[\(\)\<\>\[\]\:\;\,A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
      // character not valid in local part unless 
      // local part is quoted
      if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))) {
        $isValid = false;
      }
    }
    if ($isValid && ENTRY_EMAIL_ADDRESS_CHECK == 'true') {
      if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
        $isValid = false;
      }
    }
  }
  return $isValid;
}

/*------------------------------------
 功能：检查页面是否可以访问
 参数: $current_page(string) 页面
 返回值：是否可以访问(boolean)
 -----------------------------------*/
function check_whether_is_limited($current_page)
{
  global $ocertify;
  if ($ocertify->npermission != 31) {
    $check_value_array = array(); 
    $c_pwd_check_query = tep_db_query("select * from ".TABLE_PWD_CHECK." where page_name = '/admin/".$current_page."'"); 
    while ($c_pwd_check_res = tep_db_fetch_array($c_pwd_check_query)) {
      $check_value_array[] = $c_pwd_check_res['check_value']; 
    }
    if (empty($check_value_array)) {
      return true; 
    } else {
      if (!in_array('onetime', $check_value_array)) {
        if (!in_array('admin', $check_value_array) && ($ocertify->npermission == 15)) {
          return true; 
        }
        if (!in_array('chief', $check_value_array) && ($ocertify->npermission == 10)) {
          return true; 
        }
        if (!in_array('staff', $check_value_array) && ($ocertify->npermission == 7)) {
          return true; 
        }
      }
    }
  }
  return false;
}

/*------------------------------------
 功能：判断用户是否允许输入密码
 参数: 无 
 返回值：是否允许输入密码(boolean)
 -----------------------------------*/
function check_input_user_password($check_user_permission, $check_userid)
{
  global $ocertify;
  if ($ocertify->npermission != 31) {
    if ($check_userid != $ocertify->auth_user) {
      if ($ocertify->npermission <= $check_user_permission) {
        return false; 
      }
    }
  }
  
  return true;
}

/*------------------------------------
 功能：根据相应参数计算商品的配送费用
 参数：  
 返回值：配送费用(int)
 -----------------------------------*/
function tep_products_shipping_fee($oID,$total){

  //计算配送费用
  $shipping_weight_total = 0;

  //新添加商品重量计算

  if($_SESSION['new_products_list'][$_GET['oID']]['orders_products']){
    foreach($_SESSION['new_products_list'][$_GET['oID']]['orders_products'] as $new_products_key=>$new_products_value){

      $shipping_fee_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id=". $new_products_value['products_id']);
      $shipping_fee_array = tep_db_fetch_array($shipping_fee_query);
      $shipping_weight_total += (isset($_SESSION['orders_update_products'][$oID]['o_'.$new_products_key]['qty']) ? $_SESSION['orders_update_products'][$oID]['o_'.$new_products_key]['qty'] :$new_products_value['products_quantity']) * $shipping_fee_array['products_weight'];
      tep_db_free_result($shipping_fee_query);
    }
  }
  $shipping_query = tep_db_query("select orders_products_id,products_id,products_quantity from ". TABLE_ORDERS_PRODUCTS ." where orders_id='".$oID."'");
  while($shipping_array = tep_db_fetch_array($shipping_query)){

    $shipping_fee_query = tep_db_query("select products_weight from ". TABLE_PRODUCTS ." where products_id=". $shipping_array['products_id']);
    $shipping_fee_array = tep_db_fetch_array($shipping_fee_query);
    $shipping_weight_total += (isset($_SESSION['orders_update_products'][$oID][$shipping_array['orders_products_id']]['qty']) ? $_SESSION['orders_update_products'][$oID][$shipping_array['orders_products_id']]['qty'] :$shipping_array['products_quantity']) * $shipping_fee_array['products_weight'];
    tep_db_free_result($shipping_fee_query);
  }
  tep_db_free_result($shipping_query);

  $weight = $shipping_weight_total;

  $shipping_orders_array = array();
  $shipping_address_orders_query = tep_db_query("select * from ". TABLE_ADDRESS_ORDERS ." where orders_id='". $oID ."'");
  while($shipping_address_orders_array = tep_db_fetch_array($shipping_address_orders_query)){

    $shipping_orders_array[$shipping_address_orders_array['name']] = $shipping_address_orders_array['value'];
  }
  tep_db_free_result($shipping_address_orders_query);

  $country_fee_array = array();
  $country_fee_id_query = tep_db_query("select name_flag,fixed_option from ". TABLE_ADDRESS ." where fixed_option!='0' and status='0'");
  while($country_fee_id_array = tep_db_fetch_array($country_fee_id_query)){

    $country_fee_array[$country_fee_id_array['fixed_option']] = $country_fee_id_array['name_flag'];
  }
  tep_db_free_result($country_fee_id_query);

  foreach($shipping_orders_array  as $op_key=>$op_value){
    if($op_key == $country_fee_array[3]){
      $city_query = tep_db_query("select * from ". TABLE_COUNTRY_CITY ." where name='". $op_value ."' and status='0'");
      $city_num = tep_db_num_rows($city_query);
    }
 
  
    if($op_key == $country_fee_array[2]){
      $address_query = tep_db_query("select * from ". TABLE_COUNTRY_AREA ." where name='". $op_value ."' and status='0'");
      $address_num = tep_db_num_rows($address_query);
    }

   
    if($op_key == $country_fee_array[1]){
      $country_query = tep_db_query("select * from ". TABLE_COUNTRY_FEE ." where name='". $op_value ."' and status='0'");
      $address_country_num = tep_db_num_rows($country_query);
    }

    if($city_num > 0 && $op_key == $country_fee_array[3]){
      $city_array = tep_db_fetch_array($city_query);
      tep_db_free_result($city_query);
      $city_free_value = $city_array['free_value'];
      $city_weight_fee_array = unserialize($city_array['weight_fee']);

      //根据重量来获取相应的配送费用
      foreach($city_weight_fee_array as $key=>$value){
    
        if(strpos($key,'-') > 0){

          $temp_array = explode('-',$key);
          $city_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
        }else{
  
          $city_weight_fee = $weight <= $key ? $value : 0;
        }

        if($city_weight_fee > 0){

          break;
        }
     }
  }elseif($address_num > 0 && $op_key == $country_fee_array[2]){
    $address_array = tep_db_fetch_array($address_query);
    tep_db_free_result($address_query);
    $address_free_value = $address_array['free_value'];
    $address_weight_fee_array = unserialize($address_array['weight_fee']);

    //根据重量来获取相应的配送费用
    foreach($address_weight_fee_array as $key=>$value){
    
      if(strpos($key,'-') > 0){

        $temp_array = explode('-',$key);
        $address_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
      }else{
  
        $address_weight_fee = $weight <= $key ? $value : 0;
      }

      if($address_weight_fee > 0){

        break;
      }
    }
  }else{
    if($address_country_num > 0 && $op_key == $country_fee_array[1]){
      $country_array = tep_db_fetch_array($country_query);
      tep_db_free_result($country_query);
      $country_free_value = $country_array['free_value'];
      $country_weight_fee_array = unserialize($country_array['weight_fee']);

      //根据重量来获取相应的配送费用
      foreach($country_weight_fee_array as $key=>$value){
    
        if(strpos($key,'-') > 0){

          $temp_array = explode('-',$key);
          $country_weight_fee = $weight >= $temp_array[0] && $weight <= $temp_array[1] ? $value : 0; 
        }else{
  
          $country_weight_fee = $weight <= $key ? $value : 0;
        }

       if($country_weight_fee > 0){

         break;
       }
    }
  }
 }

 }

 $shipping_money_total = $total;
 if($city_weight_fee != ''){
   $weight_fee = $city_weight_fee;
 }else{
   $weight_fee = $address_weight_fee != '' ? $address_weight_fee : $country_weight_fee;
 }
 if($city_free_value != ''){

   $free_value = $city_free_value;
 }else{
   $free_value = $address_free_value != '' ? $address_free_value : $country_free_value;
 }

 $shipping_fee = $shipping_money_total > $free_value ? 0 : $weight_fee;

 return $shipping_fee;
}
/*-----------------------------
  功能: 获得订单状态和是否计算订单数量的数组
  返回: 返回在产品页面计算订单个数用的订单状态的数组
  ----------------------------*/
function tep_get_orders_status_array(){
  $order_status_info = array();
  $order_status_raw = tep_db_query("select orders_status_id,is_cancle from ".TABLE_ORDERS_STATUS);
  while($order_status = tep_db_fetch_array($order_status_raw)){
    $order_status_info[$order_status['orders_status_id']] = $order_status['is_cancle'];
  }
  return $order_status_info;
}
/*------------------------------
  功能: 获得规矩时间内的订单号
  参数: $limit_time_info(int) 时间
  返回: 所有符合条件的订单的数组
  -----------------------------*/
function tep_get_beforday_orders($limit_time_info){
    $now_time = time(); 
    $limit_time = 0; 
    
    if ($limit_time_info !== '') {
      $limit_time = $limit_time_info; 
    }
    if ($limit_time == 0) {
      return array(); 
    } else {
      if ($limit_time == 1) {
        $before_time = strtotime("-".$limit_time." day", $now_time); 
      } else {
        $before_time = strtotime("-".$limit_time." days", $now_time); 
      }
      $order_arr = array();
      $order_query = tep_db_query("select orders_id from ".TABLE_ORDERS." where date_purchased >= '".date('Y-m-d H:i:s', $before_time)."'");
      while($order_row = tep_db_fetch_array($order_query)){
        $order_arr[] = $order_row['orders_id'];
      }
    }
    return $order_arr;
}
/*------------------------------
  功能: 获得用户支付方法组ID
  参数: orders_id(int) 订单ID
  参数: customer_id(int) 客户ID
  返回: 返回支付方法组ID
  -----------------------------*/
function tep_get_payment_customer_chk($orders_id='',$cid='',$flag = true){
  $error = false;
  if($orders_id!=''){
    if($flag){
      $sql = "select `customers_guest_chk` from ".TABLE_ORDERS." 
        o left join ".TABLE_CUSTOMERS." c 
        on o.customers_id = c.customers_id
        where orders_id ='".$orders_id."'";
    }else{
      $sql = "select `customers_guest_chk` from ".TABLE_PREORDERS." 
        o left join ".TABLE_CUSTOMERS." c 
        on o.customers_id = c.customers_id
        where orders_id ='".$orders_id."'";
    }
  }else if ($cid!=''){
    $sql = "select `customers_guest_chk` from ".TABLE_CUSTOMERS." 
      where customers_id='".$cid."'";
  }else{
    $error = true;
  }
  if($error){
    return 0;
  }else{
    $query = tep_db_query($sql);
    if($row = tep_db_fetch_array($query)){
      if($row['customers_guest_chk'] == '0'){
        return '1';
      }else if($row['customers_guest_chk'] == '1'){
        return '2';
      }else{
        return 0;
      }
    }else{
      return 0;
    }
  }
}
/*------------------------------
  功能: 获得用户支付方法是否开启
  参数: payment(string) 支付方法
  参数: site_id(string) 网站ID
  参数: orders_id(int) 订单ID
  参数: cid(ind) 用户ID
  返回: 返回支付方法组ID
  -----------------------------*/

function tep_get_payment_flag($payment,$cid='',$site_id=0,$orders_id='',$flag=true,$type='order'){
  if($type=='order'){
    $payment_status = get_configuration_by_site_id_or_default('MODULE_PAYMENT_'.strtoupper($payment).'_STATUS',$site_id);
  }else{
    $payment_status = get_configuration_by_site_id_or_default('MODULE_PAYMENT_'.strtoupper($payment).'_PREORDER_SHOW',$site_id);
  }
  if($payment_status == 'True'){
    $customer_info = get_configuration_by_site_id_or_default('MODULE_PAYMENT_'.strtoupper($payment).'_LIMIT_SHOW',$site_id);
    $customer_arr = @unserialize($customer_info);
    if($cid!=''){
      $c_chk = tep_get_payment_customer_chk('',$cid);
    }else{
      $c_chk = tep_get_payment_customer_chk($orders_id,'',$flag);
    }
    if(in_array($c_chk,$customer_arr)){
      return true;
    }else{
      return false;
    }
  }else{
    return false;
  }
}
function tep_update_faq_sort($fid,$site_id,$type,$action='update'){
  if($type=='c'){
    if($action=='update'){
    $c_sql = "SELECT * FROM `faq_categories` c, `faq_categories_description` cd WHERE c.id = cd.faq_category_id and cd.faq_category_id='".$fid."' and site_id = '".$site_id."' limit 1";
    }else{
    $c_sql = "SELECT * FROM `faq_categories` c, `faq_categories_description` cd WHERE c.id = cd.faq_category_id and cd.id='".$fid."' and site_id = '".$site_id."' limit 1";
    }
    $c_query = tep_db_query($c_sql);
    if($c_row = tep_db_fetch_array($c_query)){
      $search_text = $c_row['romaji'].'>>>'.$c_row['title'].'>>>'.$c_row['keywords'].'>>>'.$c_row['description'];
      $title = $c_row['title'];
      $sort_order = $c_row['sort_order'];
      $is_show = $c_row['is_show'];
      $parent_id = $c_row['parent_id'];
      $info_id = $c_row['faq_category_id'];
      $updated_at = $c_row['updated_at'];
    }
  }
  if($type=='q'){
    $q_sql = "SELECT qd.site_id,qd.faq_question_id,qd.romaji,qd.ask,
      q.sort_order,qd.is_show,q2c.faq_category_id,q.updated_at,
      qd.keywords,qd.answer
      FROM
      `faq_question_description` qd, `faq_question` q,
      `faq_question_to_categories` q2c
      WHERE q.id = qd.`faq_question_id`
      and qd.`faq_question_id` = q2c.`faq_question_id` ";
    if($action=='update'){
      $q_sql .= "and qd.faq_question_id = '".$fid."' and site_id = '".$site_id."' limit 1";
    }else{
      $q_sql .= "and qd.id = '".$fid."' and site_id = '".$site_id."' limit 1";
    }
    $q_query = tep_db_query($q_sql);
    if($q_row = tep_db_fetch_array($q_query)){
      $search_text = $q_row['romaji'].'>>>'.$q_row['ask'].'>>>'.$q_row['keyworeds'].'>>>'.$q_row['answer'];
      $title = $q_row['ask'];
      $sort_order = $q_row['sort_order'];
      $is_show = $q_row['is_show'];
      $parent_id = $q_row['faq_category_id'];
      $info_id = $q_row['faq_question_id'];
      $updated_at = $q_row['updated_at'];
    }
  }
  if($action=='update'){
    $sql_fs = "UPDATE ".TABLE_FAQ_SORT." SET 
      `title`='".$title."',
      `sort_order`='".$sort_order."',
      `is_show`='".$is_show."',
      `parent_id`='".$parent_id."',
      `info_id`='".$info_id."',
      `info_type`='".$type."',
      `updated_at`='".$updated_at."',
      `search_text`='".$search_text."' 
        WHERE `info_id`='".$info_id."' 
        and `site_id`='".$site_id."'
        and `info_type`='".$type."'";
  }else if($action=='insert'){
    $sql_fs = "INSERT INTO ".TABLE_FAQ_SORT." 
      (`id` , `site_id` , `title` , `sort_order` ,
       `is_show` , `parent_id` , `info_id` , `info_type`,
       `updated_at`,`search_text`)  VALUES
      ( NULL , '".$site_id."', '".$title."',
      '".$sort_order."', '".$is_show."',
      '".$parent_id."', '".$info_id."',
      '".$type."','".$updated_at."','".$search_text."')";
  }
  tep_db_query($sql_fs);
}
