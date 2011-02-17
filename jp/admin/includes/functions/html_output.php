<?php
/*
  $Id$
*/

////
// The HTML href link wrapper function
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

////
// The HTML image wrapper function
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

////
// The HTML form submit button wrapper function
// Outputs a button in the selected language
  function tep_image_submit($image, $alt, $params = '') {
    global $language;

    return '<input type="image" src="' . DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image . '" border="0" alt="' . $alt . '"' . (($params) ? ' ' . $params : '') . '>';
  }

////
// Draw a 1 pixel black line
  function tep_black_line() {
    return tep_image(DIR_WS_IMAGES . 'pixel_black.gif', '', '100%', '1');
  }

////
// Output a separator either through whitespace, or with an image
  function tep_draw_separator($image = 'pixel_black.gif', $width = '100%', $height = '1') {
    return tep_image(DIR_WS_IMAGES . $image, '', $width, $height);
  }

////
// Output a function button in the selected language
  function tep_image_button($image, $alt = '', $params = '') {
    global $language;

    return tep_image(DIR_WS_LANGUAGES . $language . '/images/buttons/' . $image, $alt, '', '', $params);
  }

////
// javascript to dynamically update the states/provinces list when the country is changed
// TABLES: zones
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

////
// Output a form
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

////
// Output a form input field
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

////
// Output a form password field
  function tep_draw_password_field($name, $value = '', $required = false) {
    $field = tep_draw_input_field($name, $value, 'maxlength="40"', $required, 'password', false);

    return $field;
  }

////
// Output a form filefield
  function tep_draw_file_field($name, $required = false) {
    $field = tep_draw_input_field($name, '', '', $required, 'file');

    return $field;
  }

////
// Output a selection field - alias function for tep_draw_checkbox_field() and tep_draw_radio_field()
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

////
// Output a form checkbox field
  function tep_draw_checkbox_field($name, $value = '', $checked = false, $compare = '', $parameters = '') {
    return tep_draw_selection_field($name, 'checkbox', $value, $checked, $compare, $parameters);
  }

////
// Output a form radio field
  function tep_draw_radio_field($name, $value = '', $checked = false, $compare = '', $parameters='') {
    return tep_draw_selection_field($name, 'radio', $value, $checked, $compare, $parameters);
  }

////
// Output a form textarea field
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

////
// Output a form hidden field
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

////
// Output a form pull down menu
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
     $select_str .= $customer_res['customers_firstname'].'&nbsp;'.$customer_res['customers_lastname'].'&nbsp;&nbsp;'.$site_name; 
     $select_str .= '</option>'; 
   }
   $select_str .= '</select>';
   
   return $select_str;
}
