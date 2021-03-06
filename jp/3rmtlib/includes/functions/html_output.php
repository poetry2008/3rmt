<?php
/*
  $Id$
*/


/* -------------------------------------
    功能: 生成url 
    参数: $page(string) 链接页面   
    参数: $parameters(string) url参数   
    参数: $connection(string) ssl/nossl链接   
    参数: $add_session_id(boolean) 是否重新生成session_id   
    参数: $search_engine_safe(boolean) 是否安全   
    返回值: 生成的url(string) 
------------------------------------ */
  function tep_href_link($page = '', $parameters = '', $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
    global $seo_urls;
    return $seo_urls->href_link($page, $parameters, $connection, $add_session_id);
  }

/* -------------------------------------
    功能: 生成img的html 
    参数: $src(string) 图片路径   
    参数: $alt(string) 图片说明   
    参数: $width(int) 图片宽度   
    参数: $height(int) 图片高度   
    参数: $parameters(string) 其它参数   
    返回值: 生成的img(string) 
------------------------------------ */
  function tep_image($src, $alt = '', $width = '', $height = '', $parameters = '') {
    if ( (empty($src) || ($src == DIR_WS_IMAGES)) && (IMAGE_REQUIRED == 'false') ) {
      return false;
    }
   if(!file_exists(DIR_FS_CATALOG . '/' . $src)
       && file_exists(DIR_FS_CATALOG . '/' . str_replace('images/', 'default_images/', $src))
       ){
     $src = str_replace('images/', 'default_images/', $src);
     }
   if ($image_size = @getimagesize($src)) {
      if ((CONFIG_CALCULATE_IMAGE_SIZE == 'true' && $src != DIR_WS_IMAGES . 'pixel_black.gif' && $src != DIR_WS_IMAGES . 'pixel_trans.gif' && $src != DIR_WS_IMAGES . 'pixel_silver.gif' )) {
    if ( ($width) || ($height) ) {
      if ( $width=="100%" ) {
        $width = $image_size[0];
      } elseif ( $height=="100%" ) {
        $height = $image_size[1];
      } elseif ( $width==0 ) {
        unset($width);
      } elseif ( $height==0 ) {
        unset($height);
      }
      $src = thumbimage(DIR_FS_CATALOG . '/' .$src, $width, $height, 1, 1, DIR_FS_CATALOG . '/' . DIR_WS_IMAGES . 'cache_lists');
      if($height==0){
        $t_height = 0;
      }else{
        $t_height = ($image_size[1]/$height);
      }
      if($width==0){
        $t_width = 0;
      }else{
        $t_width = ($image_size[0]/$width);
      }
      if ((($t_height) > ($t_width) ) && $height>0){
         $width=ceil(($image_size[0]/$image_size[1])* $height);
      } elseif ($width>0) {
         $height=ceil($width/($image_size[0]/$image_size[1]));
      }
    }
    }
      } elseif (IMAGE_REQUIRED == 'false') {
        return '';
      } 
  

// alt is added to the img tag even if it is null to prevent browsers from outputting
// the image filename as default
    $image = '<img src="' . tep_output_string($src) . '" alt="' . tep_output_string($alt) . '"';

    if (tep_not_null($width) && tep_not_null($height)) {
      $image .= ' width="' . tep_output_string($width) . '" height="' . tep_output_string($height) . '"';
    }

    if (tep_not_null($parameters)) $image .= ' ' . $parameters;

    $image .= '>';

    return $image;
  }

/* -------------------------------------
    功能: 生成img的html 
    参数: $src(string) 图片路径   
    参数: $alt(string) 图片说明   
    参数: $width(int) 图片宽度   
    参数: $height(int) 图片高度   
    参数: $parameters(string) 其它参数   
    返回值: 生成的img(string) 
------------------------------------ */
  function tep_image2($src, $alt = '', $width = '', $height = '', $parameters = '') {
    if ( (empty($src) || ($src == DIR_WS_IMAGES)) && (IMAGE_REQUIRED == 'false') ) {
      return false;
    }
   if(!file_exists(DIR_FS_CATALOG . '/' . $src)
       && file_exists(DIR_FS_CATALOG . '/' . str_replace('images/', 'default_images/', $src))
       ){
     $src = str_replace('images/', 'default_images/', $src);
     }
   if ($image_size = @getimagesize($src)) {
      if ((CONFIG_CALCULATE_IMAGE_SIZE == 'true' && $src != DIR_WS_IMAGES . 'pixel_black.gif' && $src != DIR_WS_IMAGES . 'pixel_trans.gif' && $src != DIR_WS_IMAGES . 'pixel_silver.gif' )) {
    if ( ($width) || ($height) ) {
      if ( $width=="100%" ) {
        $width = $image_size[0];
      } elseif ( $height=="100%" ) {
        $height = $image_size[1];
      } elseif ( $width==0 ) {
        unset($width);
      } elseif ( $height==0 ) {
        unset($height);
      }
      $src=thumbimage2(DIR_FS_CATALOG . '/' .$src, $width, $height, 1, 1, DIR_FS_CATALOG . '/' . DIR_WS_IMAGES . 'cache_small');
      if ((($image_size[1]/$height) > ($image_size[0]/$width) ) && $height>0){
         $width=ceil(($image_size[0]/$image_size[1])* $height);
      } elseif ($width>0) {
         $height=ceil($width/($image_size[0]/$image_size[1]));
      }
    }
    }
      } elseif (IMAGE_REQUIRED == 'false') {
        return '';
      } 
  

// alt is added to the img tag even if it is null to prevent browsers from outputting
// the image filename as default
    $image = '<img src="' . tep_output_string($src) . '" alt="' . tep_output_string($alt) . '"';

    if (tep_not_null($width) && tep_not_null($height)) {
      $image .= ' width="' . tep_output_string($width) . '" height="' . tep_output_string($height) . '"';
    }

    if (tep_not_null($parameters)) $image .= ' ' . $parameters;

    $image .= '>';

    return $image;
  }

/* -------------------------------------
    功能: 生成img的html 
    参数: $src(string) 图片路径   
    参数: $alt(string) 图片说明   
    参数: $width(int) 图片宽度   
    参数: $height(int) 图片高度   
    参数: $parameters(string) 其它参数   
    返回值: 生成的img(string) 
------------------------------------ */
  function tep_image3($src, $alt = '', $width = '', $height = '', $parameters = '',$boder_image=false) {
    if ( (empty($src) || ($src == DIR_WS_IMAGES)) && (IMAGE_REQUIRED == 'false') ) {
      return false;
    }
   if(!file_exists(DIR_FS_CATALOG . '/' . $src)
       && file_exists(DIR_FS_CATALOG . '/' . str_replace('images/', 'default_images/', $src))
       ){
     $src = str_replace('images/', 'default_images/', $src);
     }
   if ($image_size = @getimagesize($src)) {
      if ((CONFIG_CALCULATE_IMAGE_SIZE == 'true' && $src != DIR_WS_IMAGES . 'pixel_black.gif' && $src != DIR_WS_IMAGES . 'pixel_trans.gif' && $src != DIR_WS_IMAGES . 'pixel_silver.gif' )) {
    if ( ($width) || ($height) ) {
      if ( $width=="100%" ) {
        $width = $image_size[0];
      } elseif ( $height=="100%" ) {
        $height = $image_size[1];
      } elseif ( $width==0 ) {
        unset($width);
      } elseif ( $height==0 ) {
        unset($height);
      }
      $src=thumbimage3(DIR_FS_CATALOG . '/' .$src, $width, $height, 1, 1, DIR_FS_CATALOG . '/' . DIR_WS_IMAGES . 'cache_large');
      if($boder_image){
        if(overlayjpg(DIR_FS_CATALOG.'/'.$src, $width, $height)){
	  $t_src = imgtopng_name($src);
	  if(file_exists($src)){
            unlink($src);
          }
  	  $src = $t_src;
        }
      }
      if ((($image_size[1]/$height) > ($image_size[0]/$width) ) && $height>0){
         $width=ceil(($image_size[0]/$image_size[1])* $height);
      } elseif ($width>0) {
         $height=ceil($width/($image_size[0]/$image_size[1]));
      }
    }
    }
      } elseif (IMAGE_REQUIRED == 'false') {
        return '';
      } 
  

// alt is added to the img tag even if it is null to prevent browsers from outputting
// the image filename as default
    $image = '<img src="' . tep_output_string($src) . '" border="0" alt="' . tep_output_string($alt) . '"';
    if (tep_not_null($width) && tep_not_null($height)) {
      $image .= ' width="' . tep_output_string($width) . '" height="' . tep_output_string($height) . '"';
    }

    if (tep_not_null($parameters)) $image .= ' ' . $parameters;

    $image .= '>';

    return $image;
  }

/* -------------------------------------
    功能: 生成带图片的submit 
    参数: $image(string) 图片名字   
    参数: $alt(string) 按钮说明   
    参数: $parameters(string) 其它参数   
    返回值: 生成的submit(string) 
------------------------------------ */
  function tep_image_submit($image, $alt = '', $parameters = '') {
    global $language;

    $image_submit = '<input type="image" src="' . tep_parse_input_field_data(DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image, array('"' => '&quot;')) . '" alt="' . tep_parse_input_field_data($alt, array('"' => '&quot;')) . '"';


    if (tep_not_null($parameters)) $image_submit .= ' ' . $parameters;

    $image_submit .= '>';

    return $image_submit;
  }

/* -------------------------------------
    功能: 生成带图片的img标签 
    参数: $image(string) 图片名字   
    参数: $alt(string) 图片说明   
    参数: $parameters(string) 其它参数   
    返回值: 生成图片的img标签(string) 
------------------------------------ */
  function tep_image_button($image, $alt = '', $parameters = '') {
    global $language;

    return tep_image(DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image, $alt, '', '', $parameters);
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
    功能: 生成form的html 
    参数: $name(string) 表单的名字   
    参数: $action(string) 表单跳转的页面   
    参数: $method(string) 表单的提交方式   
    参数: $parameters(string) 其它参数   
    返回值: 生成form的html(string) 
------------------------------------ */
  function tep_draw_form($name, $action, $method = 'post', $parameters = '') {
    $form = '<form name="' . tep_parse_input_field_data($name, array('"' => '&quot;')) . '" action="' . tep_parse_input_field_data($action, array('"' => '&quot;')) . '" method="' . tep_parse_input_field_data($method, array('"' => '&quot;')) . '"';

    if (tep_not_null($parameters)) $form .= ' ' . $parameters;

    $form .= '>';

    return $form;
  }

/* -------------------------------------
    功能: 生成input的html 
    参数: $name(string) 文本框的名字   
    参数: $value(string) 文本框的默认值   
    参数: $parameters(string) 其它参数   
    参数: $type(string) 文本框类型   
    参数: $reinsert_value(boolean) 是否保存值   
    返回值: 生成input的html(string) 
------------------------------------ */
  function tep_draw_input_field($name, $value = '', $parameters = '', $type = 'text', $reinsert_value = true) {
    $field = '<input type="' . tep_parse_input_field_data($type, array('"' => '&quot;')) . '" name="' . tep_parse_input_field_data($name, array('"' => '&quot;')) . '"';

    if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {
      $field .= ' value="' . tep_parse_input_field_data($GLOBALS[$name], array('"' => '&quot;')) . '"';
    } elseif (tep_not_null($value)) {
      $field .= ' value="' . tep_parse_input_field_data($value, array('"' => '&quot;')) . '"';
    } else {
      $field .= ' value=""';
    }

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    return $field;
  }

/* -------------------------------------
    功能: 生成输入密码的input的html 
    参数: $name(string) 文本框的名字   
    参数: $value(string) 文本框的默认值   
    参数: $parameters(string) 其它参数   
    返回值: 生成输入密码的input的html(string) 
------------------------------------ */
  function tep_draw_password_field($name, $value = '', $parameters = 'maxlength="40"') {
    return tep_draw_input_field($name, $value, $parameters, 'password', false);
  }

/* -------------------------------------
    功能: 生成radio/checkbox的input的html 
    参数: $name(string) 名字   
    参数: $type(string) 类型   
    参数: $value(string) 默认值   
    参数: $checked(boolean) 是否被选中   
    参数: $parameters(string) 其它参数   
    返回值: 生成radio/checkbox的input的html(string) 
------------------------------------ */
  function tep_draw_selection_field($name, $type, $value = '', $checked = false, $parameters = '') {
    $selection = '<input type="' . tep_parse_input_field_data($type, array('"' => '&quot;')) . '" name="' . tep_parse_input_field_data($name, array('"' => '&quot;')) . '"';

    if (tep_not_null($value)) $selection .= ' value="' . tep_parse_input_field_data($value, array('"' => '&quot;')) . '"';

    if (!isset($GLOBALS[$name])) $GLOBALS[$name] = NULL;
    if ( ($checked == true) || ($GLOBALS[$name] == 'on') || ( (isset($value)) && ($GLOBALS[$name] == $value) ) ) {
      $selection .= ' CHECKED';
    }

    if (tep_not_null($parameters)) $selection .= ' ' . $parameters;

    $selection .= '>';

    return $selection;
  }

/* -------------------------------------
    功能: 生成checkbox的input的html 
    参数: $name(string) 名字   
    参数: $value(string) 默认值   
    参数: $checked(boolean) 是否被选中   
    参数: $parameters(string) 其它参数   
    返回值: 生成checkbox的input的html(string) 
------------------------------------ */
  function tep_draw_checkbox_field($name, $value = '', $checked = false, $parameters = '') {
    return tep_draw_selection_field($name, 'checkbox', $value, $checked, $parameters);
  }

/* -------------------------------------
    功能: 生成radio的input的html 
    参数: $name(string) 名字   
    参数: $value(string) 默认值   
    参数: $checked(boolean) 是否被选中   
    参数: $parameters(string) 其它参数   
    返回值: 生成radio的input的html(string) 
------------------------------------ */
  function tep_draw_radio_field($name, $value = '', $checked = false, $parameters = '') {
    return tep_draw_selection_field($name, 'radio', $value, $checked, $parameters);
  }

/* -------------------------------------
    功能: 生成textarea的html 
    参数: $name(string) 名字   
    参数: $wrap(string) 是否换行   
    参数: $width(string) 文本域的宽度   
    参数: $height(string) 文本域的高度   
    参数: $text(string) 默认值   
    参数: $parameters(string) 其它参数   
    参数: $reinsert_value(boolean) 是否保存值   
    返回值: 生成textarea的html(string) 
------------------------------------ */
  function tep_draw_textarea_field($name, $wrap, $width, $height, $text = '', $parameters = '', $reinsert_value = true) {
    $field = '<textarea name="' . tep_parse_input_field_data($name, array('"' => '&quot;')) . '" wrap="' . tep_parse_input_field_data($wrap, array('"' => '&quot;')) . '" cols="' . tep_parse_input_field_data($width, array('"' => '&quot;')) . '" rows="' . tep_parse_input_field_data($height, array('"' => '&quot;')) . '"';

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if ( (isset($GLOBALS[$name])) && ($reinsert_value == true) ) {
      $field .= stripslashes($GLOBALS[$name]);
    } elseif (tep_not_null($text)) {
      $field .= $text;
    }

    $field .= '</textarea>';

    return $field;
  }

/* -------------------------------------
    功能: 生成隐藏的input的html 
    参数: $name(string) 名字   
    参数: $value(string) 默认值   
    参数: $parameters(string) 其它参数   
    返回值: 生成隐藏的input的html(string) 
------------------------------------ */
  function tep_draw_hidden_field($name, $value = '', $parameters = '',$stripslashes = true) {
    $field = '<input type="hidden" name="' . tep_output_string($name) . '"';

    if (tep_not_null($value)) {
      $field .= ' value="' . tep_output_string($value) . '"';
    } elseif (isset($GLOBALS[$name]) && $stripslashes == true) {
      $field .= ' value="' . tep_output_string(stripslashes($GLOBALS[$name])) . '"';
    }elseif (isset($GLOBALS[$name]) && $stripslashes == false){
      $field .= ' value="' . tep_output_string($GLOBALS[$name]) . '"';
    }

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';
    return $field;
  }

/* -------------------------------------
    功能: 生成session隐藏的input 
    参数: 无   
    返回值: 生成session隐藏的input(string) 
------------------------------------ */
  function tep_hide_session_id($show_hidden=false) {
    if (defined('SID') && tep_not_null(SID)) 
    if($show_hidden){
      return tep_draw_hidden_field(tep_session_name(), tep_session_id());
    }else{
      return '';
    }
  }

/* -------------------------------------
    功能: 生成select的html 
    参数: $name(string) 名字   
    参数: $values(string) 选择的值的数组   
    参数: $default(string) 默认值   
    参数: $parameters(string) 其它参数   
    参数: $required(boolean) 是否添加必须注释   
    返回值: 生成select的html(string) 
------------------------------------ */
  function tep_draw_pull_down_menu($name, $values, $default = '', $parameters = '', $required = false) {
    $field = '<select id="' . $name . '" name="' . tep_parse_input_field_data($name, array('"' => '&quot;')) . '"';

    if (tep_not_null($parameters)) $field .= ' ' . $parameters;

    $field .= '>';

    if (empty($default) && isset($GLOBALS[$name])) $default = $GLOBALS[$name];

    for ($i=0, $n=sizeof($values); $i<$n; $i++) {
      $field .= '<option value="' . tep_parse_input_field_data($values[$i]['id'], array('"' => '&quot;')) . '"';
      if ($default == $values[$i]['id']) {
        $field .= ' SELECTED';
      }

      $field .= '>' . tep_parse_input_field_data($values[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</option>';
    }
    $field .= '</select>';

    if ($required == true) $field .= TEXT_FIELD_REQUIRED;

    return $field;
  }

/* -------------------------------------
    功能: 生成国家的下拉列表 
    参数: $name(string) 名字   
    参数: $selected(string) 默认值   
    参数: $parameters(string) 其它参数   
    返回值: 国家的下拉列表(string) 
------------------------------------ */
  function tep_get_country_list($name, $selected = '', $parameters = '') {
    $countries_array = array(array('id' => '', 'text' => PULL_DOWN_DEFAULT));
    $countries = tep_get_countries();

    for ($i=0, $n=sizeof($countries); $i<$n; $i++) {
      $countries_array[] = array('id' => $countries[$i]['countries_id'], 'text' => $countries[$i]['countries_name']);
    }

    return tep_draw_pull_down_menu($name, $countries_array, $selected, $parameters);
  }

/* -------------------------------------
    功能: 生成区域的下拉列表 
    参数: $name(string) 名字   
    参数: $country_code(int) 国家id   
    参数: $selected(string) 默认值   
    参数: $parameters(string) 其它参数   
    返回值: 区域的下拉列表(string) 
------------------------------------ */
  function tep_get_zone_list($name, $country_code = '', $selected = '', $parameters = '') {
    $zones_array = array();
    $zones_query = tep_db_query("
        select zone_name 
        from " . TABLE_ZONES . " 
        where zone_country_id = '" . tep_db_input($country_code) . "' 
        order by " . (($country_code == STORE_COUNTRY) ? "zone_code" : "zone_name")
    );
    while ($zones_values = tep_db_fetch_array($zones_query)) {
      $zones_array[] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
    }
    return tep_draw_pull_down_menu($name, $zones_array, $selected, $parameters);
  }

/* -------------------------------------
    功能: 缩放图片 
    参数: $image(string) 图片路径   
    参数: $x(int) 宽度   
    参数: $y(int) 高度   
    参数: $aspectratio(int) 缩哪个方向   
    参数: $resize(boolean) 是否缩放   
    参数: $cachedir(string) 缓存目录   
    返回值: 缩放图片的html(string) 
------------------------------------ */
  function thumbimage ($image, $x, $y, $aspectratio, $resize, $cachedir){
     $types = array (1 => "gif", "jpeg", "png", "swf", "psd", "wbmp");
     $not_supported_formats = array ("GIF"); 
     umask(0);
     !is_dir ($cachedir)
         ? mkdir ($cachedir, 0777)
         : @chmod($cachedir, 0777);

       (!isset ($x) || preg_match ('/^[0-9]{1,}$/', $x, $regs)) &&
       (!isset ($y) || preg_match ('/^[0-9]{1,}$/', $y, $regs)) &&
       (isset ($x) || isset ($y))
            ? true
          : DIE ('Image width or height undefine!');

     !isset ($resize) || !preg_match ('/^[0|1]$/', $resize, $regs)
          ? $resize = 0
          : $resize;

     !isset ($aspectratio) || !preg_match ('/^[0|1]$/', $aspectratio, $regs)
          ? isset ($x) && isset ($y)
                 ? $aspectratio = 1
                 : $aspectratio = 0
          : $aspectratio;

     !isset ($image)
          ? DIE ('Image undefine.')
          : !file_exists($image)
               ? DIE ('Image not exists!')
               : false;

     $imagedata = getimagesize($image);

     !$imagedata[2] || $imagedata[2] == 4 || $imagedata[2] == 5
          ? DIE ('Image type not avaliable!')
          : false;

     $imgtype = "!(ImageTypes() & IMG_" . strtoupper($types[$imagedata[2]]) . ");";
     if ((eval($imgtype)) || (in_array(strtoupper(array_pop(explode('.', basename($image)))),$not_supported_formats))) {
        $image = substr ($image, (strrpos (DIR_FS_CATALOG . '/', '/'))+1);
        return $image;
     }

     if (!isset ($x)) $x = floor ($y * $imagedata[0] / $imagedata[1]);


     if (!isset ($y)) $y = floor ($x * $imagedata[1] / $imagedata[0]);

     if ($aspectratio && isset ($x) && isset ($y)) {
    if ((($imagedata[1]/$y) > ($imagedata[0]/$x) )){
       $x=ceil(($imagedata[0]/$imagedata[1])* $y);
    } else {
       $y=ceil($x/($imagedata[0]/$imagedata[1]));
    }
     }

     $thumbfile =  '/' . basename($image);
     if (file_exists ($cachedir.$thumbfile)) {
          $thumbdata = getimagesize ($cachedir.$thumbfile);
          $thumbdata[0] == $x && $thumbdata[1] == $y
               ? $iscached = true
               : $iscached = false;
     } else {
          $iscached = false;
     }

     if (!$iscached) {
          ($imagedata[0] > $x || $imagedata[1] > $y) || (($imagedata[0] < $x || $imagedata[1] < $y) && $resize)
               ? $makethumb = true
               : $makethumb = false;
       if ($imagedata[0] == $x) {
         if ($imagedata[1] == $y) {
           if ($resize) {
             $makethumb = true;
           }
         }
       }
     } else {
          $makethumb = false;
     }

     if ($makethumb) {
          $image = call_user_func("imagecreatefrom".$types[$imagedata[2]], $image);
        if (function_exists("imagecreatetruecolor") && ($thumb = imagecreatetruecolor ($x, $y))) {
        imagecopyresampled ($thumb, $image, 0, 0, 0, 0, $x, $y, $imagedata[0], $imagedata[1]);
      } else {
        $thumb = imagecreate ($x, $y);
        imagecopyresized ($thumb, $image, 0, 0, 0, 0, $x, $y, $imagedata[0], $imagedata[1]);
      }
          call_user_func("image".$types[$imagedata[2]], $thumb, $cachedir.$thumbfile);
          imagedestroy ($image);
          imagedestroy ($thumb);
          $image = DIR_WS_IMAGES . 'cache_lists' . $thumbfile;
     } else {
          $iscached
               ? $image = DIR_WS_IMAGES . 'cache_lists' . $thumbfile
               : $image = substr ($image, (strrpos (DIR_FS_CATALOG . '/', '/'))+1);
     }
  return $image;

}

/* -------------------------------------
    功能: 缩放图片 
    参数: $image(string) 图片路径   
    参数: $x(int) 宽度   
    参数: $y(int) 高度   
    参数: $aspectratio(int) 缩哪个方向   
    参数: $resize(boolean) 是否缩放   
    参数: $cachedir(string) 缓存目录   
    返回值: 缩放图片的html(string) 
------------------------------------ */
  function thumbimage2 ($image, $x, $y, $aspectratio, $resize, $cachedir){


     $types = array (1 => "gif", "jpeg", "png", "swf", "psd", "wbmp");
   $not_supported_formats = array ("GIF"); 
     umask(0);
     !is_dir ($cachedir)
         ? mkdir ($cachedir, 0777)
         : @chmod($cachedir, 0777);

       (!isset ($x) || preg_match ('/^[0-9]{1,}$/', $x, $regs)) &&
       (!isset ($y) || preg_match ('/^[0-9]{1,}$/', $y, $regs)) &&
       (isset ($x) || isset ($y))
            ? true
          : DIE ('Image width or height undefine!');

     !isset ($resize) || !preg_match ('/^[0|1]$/', $resize, $regs)
          ? $resize = 0
          : $resize;

     !isset ($aspectratio) || !preg_match ('/^[0|1]$/', $aspectratio, $regs)
          ? isset ($x) && isset ($y)
                 ? $aspectratio = 1
                 : $aspectratio = 0
          : $aspectratio;

     !isset ($image)
          ? DIE ('Es wurde kein Bild angegeben!')
          : !file_exists($image)
               ? DIE ('Die angegebene Datei konnte nicht auf dem Server gefunden werden!')
               : false;

     $imagedata = getimagesize($image);

     !$imagedata[2] || $imagedata[2] == 4 || $imagedata[2] == 5
          ? DIE ('Bei der angegebenen Datei handelt es sich nicht um ein Bild!')
          : false;

   $imgtype="!(ImageTypes() & IMG_" . strtoupper($types[$imagedata[2]]) . ");";
     if ((eval($imgtype)) || (in_array(strtoupper(array_pop(explode('.', basename($image)))),$not_supported_formats))) {
      $image = substr ($image, (strrpos (DIR_FS_CATALOG . '/', '/'))+1);
    return $image;

     }

     if (!isset ($x)) $x = floor ($y * $imagedata[0] / $imagedata[1]);


     if (!isset ($y)) $y = floor ($x * $imagedata[1] / $imagedata[0]);

     if ($aspectratio && isset ($x) && isset ($y)) {
    if ((($imagedata[1]/$y) > ($imagedata[0]/$x) )){
       $x=ceil(($imagedata[0]/$imagedata[1])* $y);
    } else {
       $y=ceil($x/($imagedata[0]/$imagedata[1]));
    }
     }

     $thumbfile =  '/' . basename($image);
     if (file_exists ($cachedir.$thumbfile)) {
          $thumbdata = getimagesize ($cachedir.$thumbfile);
          $thumbdata[0] == $x && $thumbdata[1] == $y
               ? $iscached = true
               : $iscached = false;
     } else {
          $iscached = false;
     }

     if (!$iscached) {
          ($imagedata[0] > $x || $imagedata[1] > $y) || (($imagedata[0] < $x || $imagedata[1] < $y) && $resize)
               ? $makethumb = true
               : $makethumb = false;
     
       if ($imagedata[0] == $x) {
         if ($imagedata[1] == $y) {
           if ($resize) {
             $makethumb = true;
           }
         }
       }
     } else {
          $makethumb = false;
     }



     if ($makethumb) {
          $image = call_user_func("imagecreatefrom".$types[$imagedata[2]], $image);
    if (function_exists("imagecreatetruecolor") && ($thumb = imagecreatetruecolor ($x, $y))) {
    imagecopyresampled ($thumb, $image, 0, 0, 0, 0, $x, $y, $imagedata[0], $imagedata[1]);
    } else {
    $thumb = imagecreate ($x, $y);
    imagecopyresized ($thumb, $image, 0, 0, 0, 0, $x, $y, $imagedata[0], $imagedata[1]);
    }
          call_user_func("image".$types[$imagedata[2]], $thumb, $cachedir.$thumbfile);
          imagedestroy ($image);
          imagedestroy ($thumb);
          $image = DIR_WS_IMAGES . 'cache_small' . $thumbfile;
     } else {
          $iscached
               ? $image = DIR_WS_IMAGES . 'cache_small' . $thumbfile
               : $image = substr ($image, (strrpos (DIR_FS_CATALOG . '/', '/'))+1);
     }
return $image;

}

/* -------------------------------------
    功能: 缩放图片 
    参数: $image(string) 图片路径   
    参数: $x(int) 宽度   
    参数: $y(int) 高度   
    参数: $aspectratio(int) 缩哪个方向   
    参数: $resize(boolean) 是否缩放   
    参数: $cachedir(string) 缓存目录   
    返回值: 缩放图片的html(string) 
------------------------------------ */
  function thumbimage3 ($image, $x, $y, $aspectratio, $resize, $cachedir){


     $types = array (1 => "gif", "jpeg", "png", "swf", "psd", "wbmp");
   $not_supported_formats = array ("GIF"); 
     umask(0);
     !is_dir ($cachedir)
         ? mkdir ($cachedir, 0777)
         : @chmod($cachedir, 0777);

       (!isset ($x) || preg_match ('/^[0-9]{1,}$/', $x, $regs)) &&
       (!isset ($y) || preg_match ('/^[0-9]{1,}$/', $y, $regs)) &&
       (isset ($x) || isset ($y))
            ? true
          : DIE ('Image width or height undefine!');

     !isset ($resize) || !preg_match ('/^[0|1]$/', $resize, $regs)
          ? $resize = 0
          : $resize;

     !isset ($aspectratio) || !preg_match ('/^[0|1]$/', $aspectratio, $regs)
          ? isset ($x) && isset ($y)
                 ? $aspectratio = 1
                 : $aspectratio = 0
          : $aspectratio;

     !isset ($image)
          ? DIE ('Es wurde kein Bild angegeben!')
          : !file_exists($image)
               ? DIE ('Die angegebene Datei konnte nicht auf dem Server gefunden werden!')
               : false;

     $imagedata = getimagesize($image);

     !$imagedata[2] || $imagedata[2] == 4 || $imagedata[2] == 5
          ? DIE ('Bei der angegebenen Datei handelt es sich nicht um ein Bild!')
          : false;

   $imgtype="!(ImageTypes() & IMG_" . strtoupper($types[$imagedata[2]]) . ");";
   //echo $imgtype;
     if ((eval($imgtype)) || (in_array(strtoupper(array_pop(explode('.', basename($image)))),$not_supported_formats))) {
        $image = substr ($image, (strrpos (DIR_FS_CATALOG . '/', '/'))+1);
      return $image;

     }

     if (!isset ($x)) $x = floor ($y * $imagedata[0] / $imagedata[1]);


     if (!isset ($y)) $y = floor ($x * $imagedata[1] / $imagedata[0]);

     if ($aspectratio && isset ($x) && isset ($y)) {
    if ((($imagedata[1]/$y) > ($imagedata[0]/$x) )){
       $x=ceil(($imagedata[0]/$imagedata[1])* $y);
    } else {
       $y=ceil($x/($imagedata[0]/$imagedata[1]));
    }
     }

     $thumbfile =  '/' . basename($image);
     if (file_exists ($cachedir.$thumbfile)) {
          $thumbdata = getimagesize ($cachedir.$thumbfile);
          $thumbdata[0] == $x && $thumbdata[1] == $y
               ? $iscached = true
               : $iscached = false;
     } else {
          $iscached = false;
     }

     if (!$iscached) {
          ($imagedata[0] > $x || $imagedata[1] > $y) || (($imagedata[0] < $x || $imagedata[1] < $y) && $resize)
               ? $makethumb = true
               : $makethumb = false;
     
       if ($imagedata[0] == $x) {
         if ($imagedata[1] == $y) {
           if ($resize) {
             $makethumb = true;
           }
         }
       }
     } else {
          $makethumb = false;
     }



     if ($makethumb) {
          $image = call_user_func("imagecreatefrom".$types[$imagedata[2]], $image);
    if (function_exists("imagecreatetruecolor") && ($thumb = imagecreatetruecolor ($x, $y))) {
    imagecopyresampled ($thumb, $image, 0, 0, 0, 0, $x, $y, $imagedata[0], $imagedata[1]);
    } else {
    $thumb = imagecreate ($x, $y);
    imagecopyresized ($thumb, $image, 0, 0, 0, 0, $x, $y, $imagedata[0], $imagedata[1]);
    }
          call_user_func("image".$types[$imagedata[2]], $thumb, $cachedir.$thumbfile);
          imagedestroy ($image);
          imagedestroy ($thumb);
          $image = DIR_WS_IMAGES . 'cache_large' . $thumbfile;
     } else {
          $iscached
               ? $image = DIR_WS_IMAGES . 'cache_large' . $thumbfile
               : $image = substr ($image, (strrpos (DIR_FS_CATALOG . '/', '/'))+1);
     }
return $image;

}

/* -------------------------------------
    功能: 生成info页的url 
    参数: $romaji(string) 罗马字   
    返回值: 生成info页的url(string) 
------------------------------------ */
function info_tep_href_link($romaji)
{
  global $request_type;
  $returnstr = HTTP_SERVER . DIR_WS_CATALOG;
    // 为了适应不同域名的ssl
    if ($_SERVER['HTTP_HOST'] == substr(HTTPS_SERVER, 8)) {
      $returnstr .= "info/".urlencode($romaji).".html";
    } else {
      // id 要求登陆之前不传sid
      if (!defined('NEW_TYPE_SYMBOL')) {
        if (($request_type == 'NONSSL' && $connection == 'SSL') || ($request_type == 'SSL' && tep_session_is_registered('customer_id'))) {
          $returnstr .= "info/".urlencode($romaji).".html?".tep_session_name()."=".tep_session_id();
        } else {
          $returnstr .= "info/".urlencode($romaji).".html";
        }
      } else {
        if ($request_type == 'SSL' && $connection == 'SSL') {
          // 不同域名间ssl间跳转不加sid
          $returnstr .= "info/".urlencode($romaji).".html";
        } else if ($request_type == 'NONSSL' && $connection == 'NONSSL') {
          $returnstr .= "info/".urlencode($romaji).".html";
        } else {
          // 不同域名间ssl和非ssl互相跳转增加sid
          $returnstr .= "info/".urlencode($romaji).".html?".tep_session_name()."=".tep_session_id();
        }
      }
    }
  return $returnstr;
}

/* -------------------------------------
    功能: 生成标签的url 
    参数: $tags_id(string) 标签id   
    返回值: 生成标签的url(string) 
------------------------------------ */
function tags_tep_href_link($tags_id)
{
  $returnstr = DIR_WS_CATALOG;
  
  $returnstr .= "tags/t-".$tags_id.".html";
  return $returnstr;
}

/* -------------------------------------
    功能: 判断是否是数字 
    参数: $var(string) 值   
    返回值: 是否是数字(int/boolean) 
------------------------------------ */
function toNumber($var){
  if( is_numeric( $var ) )
    {
      if( (float)$var != (int)$var )
        {
          return (float)$var;
        }
      else
        {
          return (int)$var;
        }
    }
    if( $var == "true" )    return true;
    if( $var == "false" )    return false;
    return $var;
}

/* -------------------------------------
    功能: 生成标签列表页的url 
    参数: 无   
    返回值: 标签列表页的url(string) 
------------------------------------ */
function tep_tags_link()
{
  global $request_type;
  $returnstr = HTTP_SERVER . DIR_WS_CATALOG;
  if ($request_type == 'SSL') {
    $returnstr .= "tags/?".tep_session_name()."=".tep_session_id();
  } else {
    $returnstr .= "tags/"; 
  }
  return $returnstr;
}

/* -------------------------------------
    功能: 生成预约商品的url 
    参数: $pid(int) 商品id   
    参数: $romaji(string) 罗马字   
    参数: $param(string) 其它参数   
    返回值: 预约商品的url(string) 
------------------------------------ */
function tep_preorder_href_link($pid, $romaji, $param = null,$connection='NOSSL')
{
  global $request_type;
  $request_type = $connection != '' ? $connection : $request_type;
  if ($connection == 'NOSSL') {
    $returnstr = HTTP_SERVER . DIR_WS_CATALOG;
  }else{
    if(ENABLE_SSL){
      $returnstr = HTTPS_SERVER . DIR_WS_CATALOG;
    }else{
      $returnstr = HTTP_SERVER . DIR_WS_CATALOG;
    }
  }
  $param_str = '';  
  if ($param) {
    $param_str = '?'.$param; 
  }
  
  $categories = tep_get_categories_by_pid($pid);
  $categoriesToString = '';
  if (count($categories)) {
    foreach ($categories as $k => $v) {
      $categories[$k] = urlencode($v); 
    }
    $categoriesToString = @join('/', $categories).'/'; 
  }
  
  if ($_SERVER['HTTP_HOST'] == substr(HTTPS_SERVER, 8)) {
      $returnstr .= 'preorder/'.$categoriesToString.urlencode($romaji).'.html'.$param_str;
    } else {
        if ($request_type == 'SSL') {
          $returnstr .= "preorder/".$categoriesToString.urlencode($romaji).".html".$param_str.'&'.tep_session_name()."=".tep_session_id();
        } else {
          $returnstr .= "preorder/".$categoriesToString.urlencode($romaji).".html".$param_str;
        }
    }
  return $returnstr;
}

/* -------------------------------------
    功能: 缩放图片 
    参数: $src(string) 图片路径   
    参数: $alt(string) 图片说明   
    参数: $width(int) 图片宽度   
    参数: $height(int) 图片高度   
    参数: $parameters(string) 其它参数   
    返回值: 缩放后的图片的img的html(string) 
------------------------------------ */
function tep_image_new($src, $alt = '', $width = '', $height = '', $parameters = '') {
    if($alt==''){
      $alt='img';
    }
  if(substr(DIR_FS_CATALOG,-1)=='/'){
    $fs_catalog = DIR_FS_CATALOG;
  }else{
    $fs_catalog = DIR_FS_CATALOG.'/';
  }
  if(!file_exists($fs_catalog .  $src)
       && file_exists($fs_catalog .  str_replace('images/', 'default_images/', $src))
       ){
     $src = str_replace('images/', 'default_images/', $src);
   }

    if ( (empty($src) || ($src == DIR_WS_IMAGES)) && (IMAGE_REQUIRED == 'false') ) {
      return false;
    }
   if ($image_size = @getimagesize($src)) {
      if ((CONFIG_CALCULATE_IMAGE_SIZE == 'true' && $src != DIR_WS_IMAGES . 'pixel_black.gif' && $src != DIR_WS_IMAGES . 'pixel_trans.gif' && $src != DIR_WS_IMAGES . 'pixel_silver.gif' )) {
if ( ($width) || ($height) ) {
if ( $width=="100%" ) {
  $width = $image_size[0];
} elseif ( $height=="100%" ) {
  $height = $image_size[1];
} elseif ( $width==0 ) {
  unset($width);
} elseif ( $height==0 ) {
  unset($height);
}
if ($height>0 && $width>0 && (($image_size[1]/$height) > ($image_size[0]/$width) )){
 $width=ceil(($image_size[0]/$image_size[1])* $height);
} elseif ($width>0) {
 $height=ceil($width/($image_size[0]/$image_size[1]));
}
}
  }
      } elseif (IMAGE_REQUIRED == 'false') {
        return '';
      }
    $image = '<img src="' . tep_output_string($src) . '" alt="' . tep_output_string($alt) . '"';
    if (tep_not_null($alt)) {
      $image .= ' title=" ' . tep_output_string($alt) . ' "';
    }
    if (tep_not_null($width) && tep_not_null($height)) {
      $image .= ' width="' . tep_output_string($width) . '" height="' . tep_output_string($height) . '"';
    }
    if (tep_not_null($parameters)) $image .= ' ' . $parameters;
    $image .= '>';
    return $image;
  }

function overlayjpg($imgsrc,$width,$height=""){
//$imgsrc jpg格式图像路径 $imgdst 图像保存文件名 $imgwidth要改变的宽度 $imgheight要改变的高度
//取得图片的宽度,高度值
  $arr = getimagesize($imgsrc);
//计算图片X轴位置
  $img_X = ($width - $arr[0])/2;
  if($height == ""){
    $heights = $arr[1];
    $img_Y = 0;
  } else{
    if($height <= $arr[1]){
        $heights = $arr[1];
        $img_Y = 0;
    } else{
        $heights = $height;
        $img_Y = ($height - $arr[1])/2;
    }
  }
  if(!file_exists($imgsrc)){
    return false;
  }
  $image = imagecreatetruecolor($width,$heights); //创建一个彩色的底图
  imagealphablending($image, false);
  imagesavealpha($image, true);
  $white = imagecolorallocatealpha($image,255,255,255,127);
  imagefill($image,0,0,$white);
  $png_name = imgtopng_name($imgsrc);
  $imgsrc = loadimg($imgsrc,$arr['mime']);
  imagecopy($image,$imgsrc,$img_X,$img_Y,0,0,$arr[0],$arr[1]);

  imagepng($image,$png_name);
  imagedestroy($image);
  return true;
}

function loadimg($imgname,$mime) {
    if($mime == "image/gif"){
        $im = @imagecreatefromgif($imgname); /* Attempt to open */
    } elseif ($mime == "image/png"){
        $im = @imagecreatefrompng($imgname); /* Attempt to open */
    } else{
        $im = @imagecreatefromjpeg($imgname); /* Attempt to open */
    }
    if(!$im) { /* See if it failed */
        $im = imagecreatetruecolor(150, 30); /* Create a blank image */
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
        /* Output an errmsg */
        imagestring($im, 1, 5, 5, "Error loading $imgname", $tc);
    }
    return $im;
}
function imgtopng_name($src){
    $arr = getimagesize($src);
    $new_src = $src;
    if($arr['mime'] == "image/gif"){
      $new_src =  preg_replace('/gif$/','png',$src);
    } elseif ($arr['mime'] == "image/jpeg"){
      $new_src =  preg_replace('/jpg$/','png',$src);
      $new_src =  preg_replace('/jpeg$/','png',$new_src);
    }
    return $new_src;
}
