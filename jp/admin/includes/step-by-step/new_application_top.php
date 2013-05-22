<?php
//定义文件名常量 
define('FILENAME_CREATE_ACCOUNT', 'create_account.php');
define('FILENAME_CREATE_ACCOUNT_PROCESS', 'create_account_process.php');
define('FILENAME_CREATE_ACCOUNT_SUCCESS', 'create_account_success.php');
define('FILENAME_CREATE_ORDER_PROCESS', 'create_order_process.php');
define('FILENAME_EDIT_CREATE_ORDER', 'edit_create_order.php');
define('FILENAME_CREATE_ORDER', 'create_order.php');
define('FILENAME_EDIT_ORDERS', 'edit_orders.php');
define('FILENAME_EDIT_NEW_ORDERS', 'edit_new_orders.php');

// 工商业者进货
define('FILENAME_CREATE_ORDER_PROCESS2', 'create_order_process2.php');
define('FILENAME_CREATE_ORDER2', 'create_order2.php');
define('FILENAME_EDIT_NEW_ORDERS2', 'edit_new_orders2.php');
// 翻译文件
require('includes/languages/'.$language.'/step-by-step/'.$language.'.php');
require(DIR_WS_CLASSES . 'payment.php');

/*------------------------------------
 功能：验证邮件
 参数: $email(string) 用户邮件
 返回值：验证邮箱成功或者失败(boolean)
 -----------------------------------*/
function tep_validate_email($email) {
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
    } else if(!preg_match('/^(\\\\."|[\(\)\<\>\[\]\:\;\,A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
          str_replace("\\\\","",$local))) {
      // character not valid in local part unless 
      // local part is quoted
      if (!preg_match('/^"(\\\\"|[^"])+"$/',
            str_replace("\\\\","",$local))) {
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

/*-----------------------------------
 功能：绘制下拉菜单目录
 参数：$name(string) 名字
 参数：$value(string) select下拉菜单中的option值
 参数：$default(string) select下拉菜单默认值
 参数：$params(string)  自定义参数
 参数：$required(string) 需求
 返回值：返回select下拉菜单(string)
 ----------------------------------*/
function tep_draw_pull_down_menu_catalog($name, $values, $default = '', $params = '', $required = false) {
  $field = '<select name="' . $name . '"';
  if ($params) $field .= ' ' . $params;
  $field .= '>';
  for ($i=0; $i<sizeof($values); $i++) {
    $field .= '<option value="' . $values[$i]['id'] . '"';
    if ( ((strlen($values[$i]['id']) > 0) && ($GLOBALS[$name] == $values[$i]['id'])) || ($default == $values[$i]['id']) ) {
      $field .= ' SELECTED';
    }
    $field .= '>' . $values[$i]['text'] . '</option>';
  }
  $field .= '</select>';

  if ($required) $field .= TEXT_FIELD_REQUIRED;

  return $field;
}  

/*-------------------------------------
 功能：获得国家名单
 参数：$name(string) 名字
 参数：$selected(string) 默认值
 参数：$parameters(string) 自定义参数
 返回值：返回下拉菜单(string)
 ------------------------------------*/
function tep_get_country_list($name, $selected = '', $parameters = '') {
  $countries_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT));
  $countries = tep_get_countries_catalog();

  for ($i=0, $n=sizeof($countries); $i<$n; $i++) {
    $countries_array[] = array('id' => $countries[$i]['countries_id'], 'text' => $countries[$i]['countries_name']);
  }

  return tep_draw_pull_down_menu_catalog($name, $countries_array, $selected, $parameters);
}

/*-----------------------------------
 功能：获得国家下拉目录
 参数：$countries_id(string) 国家编号值
 参数：$with_iso_codes(string) 代码值
 返回值: 返回国家下拉目录列表(array)
 ----------------------------------*/
function tep_get_countries_catalog($countries_id = '', $with_iso_codes = false) {
  $countries_array = array();
  if (tep_not_null($countries_id)) {
    if ($with_iso_codes == true) {
      $countries = tep_db_query("select countries_name, countries_iso_code_2, countries_iso_code_3 from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "' order by countries_name");
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

/*---------------------------------
 功能：获得地区列表
 参数：$name(string) 名字
 参数：$country_code(string) 国家地名编号
 参数：$selected(string) 默认值
 参数：$parameters(string) 自定义参数
 返回值：返回地区列表菜单(string)
 --------------------------------*/
function tep_get_zone_list($name, $country_code = '', $selected = '', $parameters = '') {
  $zones_array = array();
  $zones_query = tep_db_query("select zone_name from " . TABLE_ZONES
      . " where zone_country_id = '" . tep_db_input($country_code)
      . "' order by " . (($country_code == 107) ? "zone_code" : "zone_name"));
  while ($zones_values = tep_db_fetch_array($zones_query)) {
    $zones_array[] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
  }
  return tep_draw_pull_down_menu($name, $zones_array, $selected, $parameters);
}

/*---------------------------------
 功能: 加密密码
 参数：$plain(string) 简单的密码值
 返回值：返回MD5加密完之后的密码(string)
 --------------------------------*/
function tep_encrypt_password($plain) {
  $password = '';

  for ($i=0; $i<10; $i++) {
    $password .= tep_rand();
  }

  $salt = substr(md5($password), 0, 2);

  $password = md5($salt . $plain) . ':' . $salt;

  return $password;
}

function sbs_get_zone_name($country_id, $zone_id) {
  $zone_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . $country_id . "' and zone_id = '" . $zone_id . "'");
  if (tep_db_num_rows($zone_query)) {
    $zone = tep_db_fetch_array($zone_query);
    return $zone['zone_name'];
  } else {
    return $default_zone;
  }
}

/*-------------------------------
 功能：获得国家列表信息
 参数：$countries_id(string) 国家编号值
 参数：$with_iso_codes(string) 代码值
 返回值：返回国家列表数组(string)
 ------------------------------*/
function sbs_get_countries($countries_id = '', $with_iso_codes = false) {
  $countries_array = array();
  if ($countries_id) {
    if ($with_iso_codes) {
      $countries = tep_db_query("select countries_name, countries_iso_code_2, countries_iso_code_3 from " . TABLE_COUNTRIES . " where countries_id = '" . $countries_id . "' order by countries_name");
      $countries_values = tep_db_fetch_array($countries);
      $countries_array = array('countries_name' => $countries_values['countries_name'],
          'countries_iso_code_2' => $countries_values['countries_iso_code_2'],
          'countries_iso_code_3' => $countries_values['countries_iso_code_3']);
    } else {
      $countries = tep_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . $countries_id . "'");
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
/*------------------------------
 功能：获得国家名单
 参数：$name(string) 名字
 参数：$selected(string) 默认值
 参数：$parameters(string) 自定义参数
 返回值：返回国家名单下拉列表(string)
 -----------------------------*/
function sbs_get_country_list($name, $selected = '', $parameters = '') { 
  $countries_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT)); 
  $countries = sbs_get_countries(); 
  $size = sizeof($countries); 
  for ($i=0; $i<$size; $i++) { 
    $countries_array[] = array('id' => $countries[$i]['countries_id'], 'text' => $countries[$i]['countries_name']); 
  } 

  return tep_draw_pull_down_menu($name, $countries_array, $selected, $parameters); 
}  
/* -------------------------------------
    功能: 检查字符串的字符是否在指定字符范围内 
    参数: $c_str(string) 字符串   
    返回值: 是否在指定字符范围内(boolean) 
------------------------------------ */
  function check_character($c_str)
  {
    $character_array = array('ア' , 'ｱ', 'ぁ' , 'ァ', 'ｧ' ,'あ', 'イ' , 'ｲ' , 'ぃ' ,
        'ィ' , 'ｨ' , 'い', 'ウ' , 'ｳ' , 'ぅ' , 'ゥ' , 'ｩ' ,'う', 'エ' , 'ｴ' , 'ぇ'
        , 'ェ' , 'ｪ' ,'え', 'オ' , 'ｵ' , 'ぉ' , 'ォ' , 'ｫ' , 'お', 'カ', 'ｶ', 'ヵ' ,
        'か', 'キ' , 'ｷ' ,'き', 'ク' , 'ｸ' , 'く', 'ケ', 'ｹ', 'ヶ' ,'け', 'コ',
        'ｺ','こ', 'サ', 'ｻ','さ', 'シ' , 'ｼ','し', 'ス', 'ｽ','す', 'セ', 'ｾ','せ',
        'ソ', 'ｿ','そ', 'タ', 'ﾀ','た', 'チ' , 'ﾁ' , 'ち', 'ツ', 'ﾂ', 'っ' , 'ッ' ,
        'ｯ','つ', 'テ', 'ﾃ','て', 'ト' , 'ﾄ','と', 'ナ', 'ﾅ','な', 'ニ', 'ﾆ','に',
        'ヌ', 'ﾇ','ぬ', 'ネ', 'ﾈ','ね', 'ノ', 'ﾉ' , 'の', 'ハ', 'ﾊ','は', 'ヒ' ,
        'ﾋ','ひ', 'フ', 'ﾌ', 'ふ', 'ヘ' , 'ﾍ','へ', 'ホ' , 'ﾎ','ほ','マ', 'ﾏ' ,'ま',
        'ミ', 'ﾐ','み', 'ム' , 'ﾑ' ,'む', 'メ', 'ﾒ','め', 'モ' , 'ﾓ','も', 'ヤ',
        'ゃ', 'ゃ', 'ャ' , 'ｬ','や', 'ユ', 'ﾕ', 'ゅ', 'ュ', 'ｭ','ゆ', 'ヨ', 'ﾖ',
        'ょ', 'ョ' , 'ｮ','よ', 'ラ' , 'ﾗ','ら', 'リ', 'ﾘ','り', 'ル', 'ﾙ','る',
        'レ', 'ﾚ','れ', 'ロ' , 'ﾛ' , 'ろ', 'ワ' , 'ﾜ','わ', 'ゎ', 'ヮ', 'ヮ','わ',
        'ン', 'ﾝ' , 'ん', 'ガ', 'ｶﾞ','が', 'ギ', 'ｷﾞ','ぎ', 'グ' , 'ｸﾞ','ぐ', 'ゲ',
        'ｹﾞ','げ', 'ゴ', 'ｺﾞ','ご', 'ザ', 'ｻﾞ','ざ', 'ジ', 'ｼﾞ','じ', 'ズ',
        'ｽﾞ','ず', 'ゼ', 'ｾﾞ','ぜ', 'ゾ', 'ｿﾞ','ぞ', 'ダ', 'ﾀﾞ','だ', 'ヂ',
        'ﾁﾞ','ぢ', 'ヅ', 'ﾂﾞ','づ', 'デ', 'ﾃﾞ','で', 'ド', 'ﾄﾞ','ど', 'バ', 'ﾊﾞ',
        'ば', 'ビ', 'ﾋﾞ','び', 'ブ', 'ﾌﾞ','ぶ', 'ベ', 'ﾍﾞ','べ', 'ボ', 'ﾎﾞ', 'ぼ',
        'パ', 'ﾊﾟ','ぱ', 'ピ', 'ﾋﾟ','ぴ', 'プ', 'ﾌﾟ','ぷ', 'ペ', 'ﾍﾟ','ぺ', 'ポ',
        'ﾎ','ぽ'); 
    
    $c_str_len = mb_strlen($c_str, 'UTF-8');
    if ($c_str_len) {
      for($i=0; $i<$c_str_len; $i++) {
        $trac_str = mb_substr($c_str, $i, 1, 'UTF-8'); 
        if (!in_array($trac_str, $character_array)) {
          return true; 
        }
      }
    }
    
    return false;
  }
?>
