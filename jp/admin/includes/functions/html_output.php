<?php
/*
  $Id$
*/

/* -------------------------------------
    功能: 生成url 
    参数: $page(string) 链接页面 
    参数: $parameters(string) url参数 
    参数: $connection(string) ssl/nossl链接 
    返回值: 生成的url(string) 
------------------------------------ */
  function tep_href_link($page = '', $parameters = '', $connection = 'NONSSL') {
    if ($page == '') {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine the page link!<br><br>Function used:<br><br>tep_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
    }

    if(defined('BACKEND_LAN_URL_ENABLED') and BACKEND_LAN_URL_ENABLED){
  $absolute = 1;
    }else {
        $absolute = 0;
    }

    $request_type = (getenv('HTTPS') == 'on') ? 'SSL' : 'NONSSL';
    $needabs = $request_type == $connection;

    if ($connection == 'NONSSL') {
      $link = ($absolute==0 or $needabs)?HTTP_SERVER . DIR_WS_ADMIN:DIR_WS_ADMIN;
    } 
    elseif ($connection == 'SSL') {
      if (defined('ENABLE_SSL') && ENABLE_SSL == 'true') {
        $link = ($absolute==0 or $needabs)?HTTPS_SERVER . DIR_WS_ADMIN:DIR_WS_ADMIN;
      } else {
        $link =($absolute==0 or $needabs)?HTTP_SERVER . DIR_WS_ADMIN:DIR_WS_ADMIN;
      }
    } else {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL<br><br>Function used:<br><br>tep_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
    }
    if ($parameters == '') {
      $link = $link . $page . '?' . SID;
    } else {
      $link = $link . $page . '?' . $parameters . '&' . SID;
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);
    return $link;

  }

/* -------------------------------------
    功能: 生成url 
    参数: $page(string) 链接页面 
    参数: $parameters(string) url参数 
    参数: $connection(string) ssl/nossl链接 
    返回值: 生成的url(string) 
------------------------------------ */
  function tep_catalog_href_link($page = '', $parameters = '', $connection = 'NONSSL') {
    if ($connection == 'NONSSL') {
      $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
    } elseif ($connection == 'SSL') {
      if (ENABLE_SSL_CATALOG == 'true') {
        $link = HTTPS_CATALOG_SERVER . DIR_WS_CATALOG;
      } else {
        $link = HTTP_CATALOG_SERVER . DIR_WS_CATALOG;
      }
    } else {
      die('</td></tr></table></td></tr></table><br><br><font color="#ff0000"><b>Error!</b></font><br><br><b>Unable to determine connection method on a link!<br><br>Known methods: NONSSL SSL<br><br>Function used:<br><br>tep_href_link(\'' . $page . '\', \'' . $parameters . '\', \'' . $connection . '\')</b>');
    }
    if ($parameters == '') {
      $link .= $page;
    } else {
      $link .= $page . '?' . $parameters;
    }

    while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) $link = substr($link, 0, -1);

    return $link;
  }

/* -------------------------------------
    功能: 生成img的html 
    参数: $src(string) 图片路径 
    参数: $alt(string) 图片说明 
    参数: $width(int) 图片宽度 
    参数: $height(int) 图片高度 
    参数: $params(string) 其它参数 
    返回值: 生成的img(string) 
------------------------------------ */
  function tep_image($src, $alt = '', $width = '', $height = '', $params = '') {
    $image = '<img src="' . $src . '" border="0" alt="' . $alt . '"';
    if ($alt) {
      $image .= ' title=" ' . $alt . ' "';
    }
    if ($width) {
      $image .= ' width="' . $width . '"';
    }
    if ($height) {
      $image .= ' height="' . $height . '"';
    }
    if ($params) {
      $image .= ' ' . $params;
    }
    $image .= '>';

    return $image;
  }

/* -------------------------------------
    功能: 生成带图片的submit 
    参数: $image(string) 图片名字 
    参数: $alt(string) 按钮说明 
    参数: $params(string) 其它参数 
    返回值: 生成的submit(string) 
------------------------------------ */
  function tep_image_submit($image, $alt, $params = '') {
    global $language;

    return '<input type="image" src="' . DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image . '" border="0" alt="' . $alt . '"' . (($params) ? ' ' . $params : '') . '>';
  }

/* -------------------------------------
    功能: 带黑线的图片 
    参数: 无 
    返回值: 带黑线的图片的html(string) 
------------------------------------ */
  function tep_black_line() {
    return tep_image(DIR_WS_IMAGES . 'pixel_black.gif', '', '100%', '1');
  }

/* -------------------------------------
    功能: 带黑线的图片(可以设置宽度和高度) 
    参数: $image(string) 图片名字 
    参数: $width(string) 图片宽度 
    参数: $height(string) 图片高度 
    返回值: 带黑线的图片的html(string) 
------------------------------------ */
  function tep_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
    return tep_image(DIR_WS_IMAGES . $image, '', $width, $height);
  }

/* -------------------------------------
    功能: 生成图片的img标签 
    参数: $image(string) 图片名字 
    参数: $alt(string) 图片说明 
    参数: $params(string) 其它参数 
    返回值: 生成图片的img标签(string) 
------------------------------------ */
  function tep_image_button($image, $alt = '', $params = '') {
    global $language;

    return tep_image(DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image, $alt, '', '', $params);
  }

/* -------------------------------------
    功能: 生成区域的选择元素
    参数: $country(int) 国家id 
    参数: $form(string) 表单的名字 
    参数: $field(string) 字段名字 
    返回值: 生成区域的选择元素(string) 
------------------------------------ */
  function tep_js_zone_list($country, $form, $field) {
    $countries_query = tep_db_query("select distinct zone_country_id from " . TABLE_ZONES . " order by zone_country_id");
    $num_country = 1;
    $output_string = '';
    while ($countries = tep_db_fetch_array($countries_query)) {
      if ($num_country == 1) {
        $output_string .= '  if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";
      } else {
        $output_string .= '  } else if (' . $country . ' == "' . $countries['zone_country_id'] . '") {' . "\n";
      }

      $states_query = tep_db_query("select zone_name, zone_id from " . TABLE_ZONES . " where zone_country_id = '" . $countries['zone_country_id'] . "' order by " . ($countries['zone_country_id'] == 107 ? "zone_code" : "zone_name"));

      $num_state = 1;
      while ($states = tep_db_fetch_array($states_query)) {
        if ($num_state == '1') $output_string .= '    ' . $form . '.' . $field . '.options[0] = new Option("' . PLEASE_SELECT . '", "");' . "\n";
        $output_string .= '    ' . $form . '.' . $field . '.options[' . $num_state . '] = new Option("' . $states['zone_name'] . '", "' . $states['zone_id'] . '");' . "\n";
        $num_state++;
      }
      $num_country++;
    }
    $output_string .= '  } else {' . "\n" .
                      '    ' . $form . '.' . $field . '.options[0] = new Option("' . TYPE_BELOW . '", "");' . "\n" .
                      '  }' . "\n";

    return $output_string;
  }

/* -------------------------------------
    功能: 生成form的html 
    参数: $name(string) 表单的名字 
    参数: $action(string) 表单跳转的页面 
    参数: $parameters(string) 跳转页面的参数 
    参数: $method(string) 表单的提交方式 
    参数: $params(string) 其它参数 
    返回值: 生成form的html(string) 
------------------------------------ */
  function tep_draw_form($name, $action, $parameters = '', $method = 'post', $params = '') {
    $form = '<form name="' . $name . '" action="';
    if ($parameters) {
      $form .= tep_href_link($action, $parameters);
    } else {
      $form .= tep_href_link($action);
    }
    $form .= '" method="' . $method . '"';
    if ($params) {
      $form .= ' ' . $params;
    }
    $form .= '>';

    return $form;
  }

/* -------------------------------------
    功能: 生成input的html 
    参数: $name(string) 文本框的名字 
    参数: $value(string) 文本框的默认值 
    参数: $parameters(string) 其它参数 
    参数: $required(boolean) 是否添加必须注释 
    参数: $type(string) 文本框类型 
    参数: $reinsert_value(boolean) 是否保存值 
    返回值: 生成input的html(string) 
------------------------------------ */
  function tep_draw_input_field($name, $value = '', $parameters = '', $required = false, $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . $type . '" name="' . $name . '"';
    if ( isset($GLOBALS[$name]) && ($GLOBALS[$name]) && ($reinsert_value) ) {
      $field .= ' value="' . htmlspecialchars(trim($GLOBALS[$name])) . '"';
    } elseif ($value != '') {
      $field .= ' value="' . htmlspecialchars(trim($value)) . '"';
    }
    if ($parameters != '') {
      $field .= ' ' . $parameters;
    }
    $field .= '>';

    if ($required) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

/* -------------------------------------
    功能: 生成输入密码的input的html 
    参数: $name(string) 文本框的名字 
    参数: $value(string) 文本框的默认值 
    参数: $required(boolean) 是否添加必须注释 
    参数: $parameters(string) 其它参数 
    返回值: 生成输入密码的input的html(string) 
------------------------------------ */
  function tep_draw_password_field($name, $value = '', $required =
      false,$parameters='') {
    $field = tep_draw_input_field($name, $value, 'maxlength="40" '.$parameters, $required, 'password', false);

    return $field;
  }

/* -------------------------------------
    功能: 生成上传文件的input的html 
    参数: $name(string) 文本框的名字 
    参数: $required(boolean) 是否添加必须注释 
    返回值: 生成上传文件的input的html(string) 
------------------------------------ */
  function tep_draw_file_field($name, $required = false,$params) {
    $field = tep_draw_input_field($name, '', $params, $required, 'file');

    return $field;
  }

/* -------------------------------------
    功能: 生成radio/checkbox的input的html 
    参数: $name(string) 名字 
    参数: $type(string) 类型 
    参数: $value(string) 默认值 
    参数: $checked(boolean) 是否选中 
    参数: $compare(string) 对比的值 
    参数: $parameters(string) 其它参数 
    返回值: 生成radio/checkbox的input的html(string) 
------------------------------------ */
  function tep_draw_selection_field($name, $type, $value = '', $checked = false, $compare = '', $parameters = '') {
    $selection = '<input type="' . $type . '" name="' . $name . '"';
    if ($value != '') {
      $selection .= ' value="' . $value . '"';
    }
    if ( 
        ($checked == true) 
        || (isset($GLOBALS[$name]) && $GLOBALS[$name] == 'on') 
        || ($value && (isset($GLOBALS[$name]) && $GLOBALS[$name] == $value)) 
        || ($value && ($value == $compare)) 
      ) {
      $selection .= ' CHECKED';
    }
    $selection .= ' ' . $parameters . '>';

    return $selection;
  }

/* -------------------------------------
    功能: 生成checkbox的input的html 
    参数: $name(string) 名字 
    参数: $value(string) 默认值 
    参数: $checked(boolean) 是否选中 
    参数: $compare(string) 对比的值 
    参数: $parameters(string) 其它参数 
    返回值: 生成checkbox的input的html(string) 
------------------------------------ */
  function tep_draw_checkbox_field($name, $value = '', $checked = false, $compare = '', $parameters = '') {
    return tep_draw_selection_field($name, 'checkbox', $value, $checked, $compare, $parameters);
  }

/* -------------------------------------
    功能: 生成radio的input的html 
    参数: $name(string) 名字 
    参数: $value(string) 默认值 
    参数: $checked(boolean) 是否选中 
    参数: $compare(string) 对比的值 
    参数: $parameters(string) 其它参数 
    返回值: 生成radio的input的html(string) 
------------------------------------ */
  function tep_draw_radio_field($name, $value = '', $checked = false, $compare = '', $parameters='') {
    return tep_draw_selection_field($name, 'radio', $value, $checked, $compare, $parameters);
  }

/* -------------------------------------
    功能: 生成textarea的html 
    参数: $name(string) 名字 
    参数: $wrap(string) 是否换行 
    参数: $width(int) 文本域的宽度 
    参数: $height(int) 文本域的高度 
    参数: $text(string) 默认值 
    参数: $params(string) 其它参数 
    参数: $reinsert_value(boolean) 是否保存值 
    返回值: 生成textarea的html(string) 
------------------------------------ */
  function tep_draw_textarea_field($name, $wrap, $width, $height, $text = '', $params = '', $reinsert_value = true) {
    $field = '<textarea name="' . $name . '" wrap="' . $wrap . '" cols="' . $width . '" rows="' . $height . '"';
    if ($params) $field .= ' ' . $params;
    $field .= '>';
    if ( isset($GLOBALS[$name]) && ($GLOBALS[$name]) && ($reinsert_value) ) {
      $field .= $GLOBALS[$name];
    } elseif ($text != '') {
      $field .= $text;
    }
    $field .= '</textarea>';

    return $field;
  }

/* -------------------------------------
    功能: 生成隐藏的input的html 
    参数: $name(string) 名字 
    参数: $value(string) 默认值 
    返回值: 生成隐藏的input的html(string) 
------------------------------------ */
  function tep_draw_hidden_field($name, $value = '') {
    $field = '<input type="hidden" name="' . $name . '" value="';
    if ($value != '') {
      $field .= trim($value);
    } else {
      $field .= isset($GLOBALS[$name]) && is_string($GLOBALS[$name]) ? trim($GLOBALS[$name]) : '';
    }
    $field .= '">';

    return $field;
  }

/* -------------------------------------
    功能: 生成select的html 
    参数: $name(string) 名字 
    参数: $value(array) 选择的值的数组 
    参数: $default(string) 默认值 
    参数: $params(string) 其它参数 
    参数: $required(boolean) 是否添加必须注释 
    返回值: 生成select的html(string) 
------------------------------------ */
  function tep_draw_pull_down_menu($name, $values, $default = '', $params = '', $required = false) {
    $field = '<select name="' . $name . '"';
    if ($params) $field .= ' ' . $params;
    $field .= '>';
    for ($i=0; $i<sizeof($values); $i++) {
      $field .= '<option value="' . (isset($values[$i]['id'])?$values[$i]['id']:'') . '"';
      if ( ( isset($values[$i]['id']) && (strlen($values[$i]['id']) > 0) && isset($GLOBALS[$name]) && ($GLOBALS[$name] == $values[$i]['id'])) || ($default == (isset($values[$i]['id'])?$values[$i]['id']:'')) ) {
        $field .= ' SELECTED';
      }
      $field .= '>' . (isset($values[$i]['text'])?$values[$i]['text']:'') . '</option>';
    }
    $field .= '</select>';

    if ($required) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

/* -------------------------------------
    功能: 生成指定客户的select的html 
    参数: 无 
    返回值: 生成指定客户的select的html(string) 
------------------------------------ */
function tep_customer_list_pull_down_menu()
{
   $select_str = '<select name="cmail">';
   $customer_query = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_guest_chk = '9' order by customers_id"); 
   while ($customer_res = tep_db_fetch_array($customer_query)) {
     $carr = array();
     $svalue = $customer_res['customers_email_address'].'|||'.$customer_res['site_id'];
     
     $site_query = tep_db_query("select * from sites where id = '".$customer_res['site_id']."'"); 
     $site_res = tep_db_fetch_array($site_query);
     $site_name = $site_res['name'];
     
     $select_str .= '<option value=\''.$svalue.'\'>'; 
     $select_str .= $customer_res['customers_lastname'].'&nbsp;'.$customer_res['customers_firstname'].'&nbsp;&nbsp;'.$site_name; 
     $select_str .= '</option>'; 
   }
   $select_str .= '</select>';
   
   return $select_str;
}

/* -------------------------------------
    功能: 生成button的html 
    参数: $value(string) 默认值 
    参数: $other_str(string) 其它参数 
    参数: $class_name(string) class的名字 
    返回值: 生成button的html(string) 
------------------------------------ */
function tep_html_element_button($value, $other_str = '', $class_name = 'element_button') {
  if(preg_match('/onclick/',$other_str)){
  $button_str = '<input type="button" class="'.$class_name.'" value="'.$value.'"';
  }else{
  $button_str = '<input type="button" class="'.$class_name.'" onclick="redirect_new_url(this);" value="'.$value.'"';
   
  }
  if ($other_str != '') {
    $button_str .= ' '.$other_str; 
  } 
  $button_str .= '>'; 
  return $button_str;
}

/* -------------------------------------
    功能: 生成submit的html 
    参数: $value(string) 默认值 
    参数: $other_str(string) 其它参数 
    参数: $class_name(string) class的名字 
    返回值: 生成submit的html(string) 
------------------------------------ */
function tep_html_element_submit($value, $other_str = '', $class_name = 'element_button') {
  $button_str = '<input type="submit" class="'.$class_name.'" value="'.$value.'"';
   
  if ($other_str != '') {
    $button_str .= ' '.$other_str; 
  }
  $button_str .= '>'; 
  return $button_str;
}

/* -------------------------------------
    功能: 生成eof的隐藏input 
    参数: 无 
    返回值: eof的隐藏input(string) 
------------------------------------ */
function tep_eof_hidden(){
  //判断 POST 值 是否存在
  $hidden_str = '<input type="hidden" name="eof" value="eof">';
  return $hidden_str;
}
