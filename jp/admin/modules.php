<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  switch ($_GET['set']) {
    case 'shipping':
      $module_type = 'shipping';
      $module_directory = DIR_FS_CATALOG_MODULES . 'shipping/';
      $module_key = 'MODULE_SHIPPING_INSTALLED';
      define('HEADING_TITLE', HEADING_TITLE_MODULES_SHIPPING);
      break;
    case 'ordertotal':
      $module_type = 'order_total';
      $module_directory = DIR_FS_CATALOG_MODULES . 'order_total/';
      $module_key = 'MODULE_ORDER_TOTAL_INSTALLED';
      define('HEADING_TITLE', HEADING_TITLE_MODULES_ORDER_TOTAL);
      break;
    case 'metaseo':
      $module_type = 'metaseo';
      $module_directory = DIR_FS_CATALOG_MODULES . 'metaseo/';
      $module_key = 'MODULE_METASEO_INSTALLED';
      define('HEADING_TITLE', HEADING_TITLE_MODULES_METASEO);
      break;
    case 'payment':
    default:
      $module_type = 'payment';
      $module_directory = DIR_FS_CATALOG_MODULES . 'payment/';
      $module_key = 'MODULE_PAYMENT_INSTALLED';
      define('HEADING_TITLE', HEADING_TITLE_MODULES_PAYMENT);
      break;
  }

  if (isset($_GET['action'])) 
  switch ($_GET['action']) {
    case 'save':
      $site_id = isset($_POST['site_id'])?(int)$_POST['site_id']:0;
   if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];//权限判断
         else $site_arr="";
 forward401Unless(editPermission($site_arr, $site_id));
      $class = basename($_GET['module']);
      $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
      if (file_exists($module_directory . $class . $file_extension)) {
        include($module_directory . $class . $file_extension);
      }
      if(!tep_module_installed($class, $site_id)){
          $module = new $class($site_id);
          $module->install();
      }
      
      if ($_GET['set'] == 'payment') { 
        if ($site_id != 0) {
          $limit_show_str = ''; 
          if (!isset($_POST['configuration']['MODULE_PAYMENT_BUYING_LIMIT_SHOW']) && $_GET['module'] == 'buying') {  
            $limit_show_str = 'MODULE_PAYMENT_BUYING_LIMIT_SHOW';  
          }
          
          if (!isset($_POST['configuration']['MODULE_PAYMENT_MONEYORDER_LIMIT_SHOW']) && $_GET['module'] == 'moneyorder') {  
            $limit_show_str = 'MODULE_PAYMENT_MONEYORDER_LIMIT_SHOW';  
          }
          
          if (!isset($_POST['configuration']['MODULE_PAYMENT_POSTALMONEYORDER_LIMIT_SHOW']) && $_GET['module'] == 'postalmoneyorder') {  
            $limit_show_str = 'MODULE_PAYMENT_POSTALMONEYORDER_LIMIT_SHOW';  
          }
          
          if (!isset($_POST['configuration']['MODULE_PAYMENT_CONVENIENCE_STORE_LIMIT_SHOW']) && $_GET['module'] == 'convenience_store') {  
            $limit_show_str = 'MODULE_PAYMENT_CONVENIENCE_STORE_LIMIT_SHOW';  
          }
          
          if (!isset($_POST['configuration']['MODULE_PAYMENT_TELECOM_LIMIT_SHOW']) && $_GET['module'] == 'telecom') {  
            $limit_show_str = 'MODULE_PAYMENT_TELECOM_LIMIT_SHOW';  
          }
          
          if (!isset($_POST['configuration']['MODULE_PAYMENT_PAYPAL_LIMIT_SHOW']) && $_GET['module'] == 'paypal') {  
            $limit_show_str = 'MODULE_PAYMENT_PAYPAL_LIMIT_SHOW';  
          }
          
          if (!isset($_POST['configuration']['MODULE_PAYMENT_POINT_LIMIT_SHOW']) && $_GET['module'] == 'buyingpoint') {  
            $limit_show_str = 'MODULE_PAYMENT_POINT_LIMIT_SHOW';  
          }
          
          if (!isset($_POST['configuration']['MODULE_PAYMENT_FETCH_GOOD_LIMIT_SHOW']) && $_GET['module'] == 'fetchgood') {  
            $limit_show_str = 'MODULE_PAYMENT_FETCH_GOOD_LIMIT_SHOW';  
          }
          
          if (!isset($_POST['configuration']['MODULE_PAYMENT_FREE_PAYMENT_LIMIT_SHOW']) && $_GET['module'] == 'freepayment') {  
            $limit_show_str = 'MODULE_PAYMENT_FREE_PAYMENT_LIMIT_SHOW';  
          }
          if (!empty($limit_show_str)) {
            if (!tep_db_num_rows(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key='".$limit_show_str."' and site_id='".$site_id."'"))) {
              $cp_show_configuration = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key='".$limit_show_str."' and site_id='0'"));
              if ($cp_show_configuration) {
                tep_db_query("
                  INSERT INTO `configuration` (
                  `configuration_id` ,
                  `configuration_title` ,
                  `configuration_key` ,
                  `configuration_value` ,
                  `configuration_description` ,
                  `configuration_group_id` ,
                  `sort_order` ,
                  `last_modified` ,
                  `date_added` ,
                  `use_function` ,
                  `set_function` ,
                  `site_id`
                  )
                  VALUES (
                  NULL , 
                  '".mysql_real_escape_string($cp_show_configuration['configuration_title'])."', 
                  '".$cp_show_configuration['configuration_key']."', 
                  '".$cp_show_configuration['configuration_value']."', 
                  '".mysql_real_escape_string($cp_show_configuration['configuration_description'])."', 
                  '".$cp_show_configuration['configuration_group_id']."', 
                  '".$cp_show_configuration['sort_order']."' , 
                  '".$cp_show_configuration['last_modified']."' , 
                  '".$cp_show_configuration['date_added']."', 
                  '".mysql_real_escape_string($cp_show_configuration['use_function'])."' , 
                  '".mysql_real_escape_string($cp_show_configuration['set_function'])."' , 
                  '".$site_id."'
                  )
                ");
              }
            }
            $blank_show_arr = array(); 
            tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . serialize($blank_show_arr) . "' where configuration_key = '" .  $limit_show_str . "' and site_id = '".$site_id."'");
          }
        }
      }
      while (list($key, $value) = each($_POST['configuration'])) {
        if (!tep_db_num_rows(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key='".$key."' and site_id='".$site_id."'"))) {
          $cp_configuration = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key='".$key."' and site_id='0'"));
          if ($cp_configuration) {
            tep_db_query("
              INSERT INTO `configuration` (
              `configuration_id` ,
              `configuration_title` ,
              `configuration_key` ,
              `configuration_value` ,
              `configuration_description` ,
              `configuration_group_id` ,
              `sort_order` ,
              `last_modified` ,
              `date_added` ,
              `use_function` ,
              `set_function` ,
              `site_id`
              )
              VALUES (
              NULL , 
              '".mysql_real_escape_string($cp_configuration['configuration_title'])."', 
              '".$cp_configuration['configuration_key']."', 
              '".$cp_configuration['configuration_value']."', 
              '".mysql_real_escape_string($cp_configuration['configuration_description'])."', 
              '".$cp_configuration['configuration_group_id']."', 
              '".$cp_configuration['sort_order']."' , 
              '".$cp_configuration['last_modified']."' , 
              '".$cp_configuration['date_added']."', 
              '".mysql_real_escape_string($cp_configuration['use_function'])."' , 
              '".mysql_real_escape_string($cp_configuration['set_function'])."' , 
              '".$site_id."'
              )
            ");
          }
        }
        if ($key == 'MODULE_PAYMENT_BUYING_LIMIT_SHOW' || $key == 'MODULE_PAYMENT_MONEYORDER_LIMIT_SHOW' || $key == 'MODULE_PAYMENT_POSTALMONEYORDER_LIMIT_SHOW' || $key == 'MODULE_PAYMENT_CONVENIENCE_STORE_LIMIT_SHOW' || $key == 'MODULE_PAYMENT_TELECOM_LIMIT_SHOW' || $key == 'MODULE_PAYMENT_PAYPAL_LIMIT_SHOW' || $key == 'MODULE_PAYMENT_POINT_LIMIT_SHOW' || $key == 'MODULE_PAYMENT_FETCH_GOOD_LIMIT_SHOW' || $key == 'MODULE_PAYMENT_FREE_PAYMENT_LIMIT_SHOW' 
            ) {
          tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . serialize($value) . "' where configuration_key = '" . $key . "' and site_id = '".$site_id."'");
        } else {
          tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value . "' where configuration_key = '" . $key . "' and site_id = '".$site_id."'");
        }
        
      }
      tep_redirect(tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module']));
      break;
    case 'install':
    case 'remove':
      $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
      $class = basename($_GET['module']);
      if (file_exists($module_directory . $class . $file_extension)) {
        include($module_directory . $class . $file_extension);
        if ($_GET['action'] == 'install') {
          $module = new $class;
          $module->install();
        } elseif ($_GET['action'] == 'remove') {
          foreach(tep_get_sites() as $s){
            $module = new $class($s['id']);
            $module->remove();
          }
        }
      }
      tep_redirect(tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class));
      break;
  }
$site_id = isset($_GET['site_id'])?$_GET['site_id']:'0';
$sites = tep_get_sites();
$ex_site = $sites[0];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_MODULES; ?></td>
                <td class="dataTableHeadingContent" align="right">&nbsp;</td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_SORT_ORDER; ?></td>
                <!--<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>-->
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
  $directory_array = array();
  if ($dir = @dir($module_directory)) {
    while ($file = $dir->read()) {
      if (!is_dir($module_directory . $file)) {
        if (substr($file, strrpos($file, '.')) == $file_extension) {
          $directory_array[] = $file;
        }
      }
    }
    sort($directory_array);
    $dir->close();
  }

  $installed_modules = $directory_array_sorted= array();
  
  for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++) {
    $file = $directory_array[$i];
    include(DIR_WS_LANGUAGES . $language . '/modules/' . $module_type . '/' . $file);
    include($module_directory . $file);
    $class = substr($file, 0, strrpos($file, '.'));
    if (tep_class_exists($class)) {
      $module = new $class;
      $directory_array_sorted[$module->sort_order][] = $file;
    }
  }
  //print_r($directory_array_sorted);
  ksort($directory_array_sorted);
  foreach ($directory_array_sorted as $i => $files) {
    //$file = $directory_array_sorted[$i];
    //include(DIR_WS_LANGUAGES . $language . '/modules/' . $module_type . '/' . $file);
    //include($module_directory . $file);
  foreach ($files as $j => $file) {
    $class = substr($file, 0, strrpos($file, '.'));
    if (tep_class_exists($class)) {
      $module = new $class;
      if ($module->check() > 0) {
        if ($module->sort_order > 0) {
          $installed_modules[$module->sort_order] = $file;
        } else {
          $installed_modules[] = $file;
        }
      }

      if (((!@$_GET['module']) || ($_GET['module'] == $class)) && (!@$mInfo)) {
        $module_info = array('code' => $module->code,
                             'title' => $module->title,
                             'description' => $module->description,
                             'status' => $module->check());

        $module_keys = $module->keys();

        $keys_extra = array();
        $get_site_id = tep_module_installed($class, $site_id) ? $site_id : 0;
        for ($j = 0, $k = sizeof($module_keys); $j < $k; $j++) {
          $key_value_query = tep_db_query("select configuration_title, configuration_value, configuration_description, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_key = '" . $module_keys[$j] . "' and site_id = '".$get_site_id."'");
          $key_value = tep_db_fetch_array($key_value_query);

          $keys_extra[$module_keys[$j]]['title'] = $key_value['configuration_title'];
          $keys_extra[$module_keys[$j]]['value'] = $key_value['configuration_value'];
          $keys_extra[$module_keys[$j]]['description'] = $key_value['configuration_description'];
          $keys_extra[$module_keys[$j]]['use_function'] = $key_value['use_function'];
          $keys_extra[$module_keys[$j]]['set_function'] = $key_value['set_function'];
        }

        $module_info['keys'] = $keys_extra;
        $mInfo = new objectInfo($module_info);
      }

      if (isset($mInfo)&& (is_object($mInfo)) && ($class == $mInfo->code) ) {
        if ($module->check() > 0) {
          echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ondblclick="document.location.href=\'' . tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class . '&action=edit') . '\'">' . "\n";
        } else {
          echo '              <tr class="dataTableRowSelected">' . "\n";
        }
      } else {
        echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" ondblclick="document.location.href=\'' . tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php if (isset($module->link) && $module->link) {?><a target='_blank' href="<?php echo $ex_site['url'].'/'.$module->link;?>"><?php echo tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW);?></a><?php } ?><?php echo $module->title; ?></td>
                <td class="dataTableContent" align="left"><?php echo $module->link ? ($ex_site['url'].'/'.$module->link) : '';?></td>
                <td class="dataTableContent" align="right"><?php if (is_numeric($module->sort_order)) echo $module->sort_order; ?></td>
                <!--<td class="dataTableContent" align="right"><?php if ($module->check() > 0) { echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="' . tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class . '&action=remove') . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>'; } else { echo '<a href="' . tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class . '&action=install') . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10); } ?></td>-->
                <td class="dataTableContent" align="right"><?php if ( (@is_object($mInfo)) && ($class == $mInfo->code) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
  }
  }
  ksort($installed_modules);
  $check_query = tep_db_query("
      select configuration_value 
      from " . TABLE_CONFIGURATION . " 
      where configuration_key = '" . $module_key . "' 
        and site_id = '0'");
  if (tep_db_num_rows($check_query)) {
    $check = tep_db_fetch_array($check_query);
    if ($check['configuration_value'] != implode(';', $installed_modules)) {
      tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . implode(';', $installed_modules) . "', last_modified = now() where configuration_key = '" . $module_key . "'");
    }
  } else {
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Installed Modules', '" . $module_key . "', '" . implode(';', $installed_modules) . "', 'This is automatically updated. No need to edit.', '6', '0', now())");
  }
?>
              <tr>
                <td colspan="4" class="smallText"><?php echo TEXT_MODULE_DIRECTORY . ' ' . $module_directory; ?></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch (isset($_GET['action'])?$_GET['action']:'') {
    case 'edit':
      $keys = '';
      reset($mInfo->keys);
      while (list($key, $value) = each($mInfo->keys)) {
          $_value_query = tep_db_query("select configuration_key, configuration_title, configuration_value, configuration_description, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_key = '" . $key . "' and site_id = '".$site_id."'");
          $_value = tep_db_fetch_array($_value_query);
          if (!$_value) {
            $_value_query = tep_db_query("select configuration_key, configuration_title, configuration_value, configuration_description, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_key = '" . $key . "' and site_id = '0'");
            $_value = tep_db_fetch_array($_value_query);
          }
          $value['title'] = $_value['configuration_title'];
          $value['value'] = $_value['configuration_value'];
          $value['description'] = $_value['configuration_description'];
          $value['use_function'] = $_value['use_function'];
          $value['set_function'] = $_value['set_function'];
        
        if ($site_id == 0 && !preg_match('/.*SORT_ORDER$/', $key)) {
          $keys .= tep_draw_hidden_field('configuration[' . $key . ']', $value['value']);
        } else {
          $keys .= '<b>' . $value['title'] . '</b><br>' . $value['description'] . '<br>';
          if ($value['set_function']) {
            eval('$keys .= ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
          } else {
            $keys .= tep_draw_input_field('configuration[' . $key . ']', $value['value']);
          }
          $keys .= '<br><br>';
        }
        
      }
      $keys = substr($keys, 0, strrpos($keys, '<br><br>'));
      $heading[] = array('text' => '<b>' . $mInfo->title . '</b>');

      $contents = array('form' => tep_draw_form('modules', FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&action=save'));
      $contents[] = array('text' => $keys);
      $contents[] = array('text' => '<input type="hidden" name="site_id" value="'.$site_id.'">');
      $contents[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_UPDATE) . ' <a href="' .  tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' .  $_GET['module']) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    default:

      $heading[] = array('text' => '<b>' . (isset($mInfo->title)?$mInfo->title:'') . '</b>');
      if (isset($mInfo->status) && $mInfo->status == '1') {

        $keys = '';
        /* 临时隐藏 
        reset($mInfo->keys);
        while (list(, $value) = each($mInfo->keys)) {
          $keys .= '<b>' . $value['title'] . '</b><br>';
          if ($value['use_function']) {
            $use_function = $value['use_function'];
            if (ereg('->', $use_function)) {
              $class_method = explode('->', $use_function);
              if (!is_object(${$class_method[0]})) {
                include(DIR_WS_CLASSES . $class_method[0] . '.php');
                ${$class_method[0]} = new $class_method[0]();
              }
              $keys .= tep_call_function($class_method[1], $value['value'], ${$class_method[0]});
            } else {
              $keys .= tep_call_function($use_function, $value['value']);
            }
          } else {
            $keys .= $value['value'];
          }
          $keys .= '<br><br>';
        }
        */
        
        foreach(tep_get_sites() as $s){
          $keys .= "<br>".$s['romaji']."<hr>";
          reset($mInfo->keys);
          while (list($k, $value) = each($mInfo->keys)) {
            $module_item = tep_db_fetch_array(tep_db_query("select * from configuration where configuration_key = '".$k."' and site_id = '".$s['id']."'"));
            $keys .= '<b>' . $module_item['configuration_title'] . '</b><br>';
            if ($module_item['use_function']) {
              $use_function = $module_item['use_function'];
              if (ereg('->', $use_function)) {
                $class_method = explode('->', $use_function);
                if (!is_object(${$class_method[0]})) {
                  include(DIR_WS_CLASSES . $class_method[0] . '.php');
                  ${$class_method[0]} = new $class_method[0]();
                }
                $keys .= tep_call_function($class_method[1], $module_item['configuration_value'], ${$class_method[0]});
              } else {
                $keys .= tep_call_function($use_function, $module_item['configuration_value']);
              }
            } else {
              if ($module_item['configuration_key'] == 'MODULE_PAYMENT_BUYING_LIMIT_SHOW' || $module_item['configuration_key'] == 'MODULE_PAYMENT_MONEYORDER_LIMIT_SHOW' || $module_item['configuration_key'] == 'MODULE_PAYMENT_POSTALMONEYORDER_LIMIT_SHOW' || $module_item['configuration_key'] == 'MODULE_PAYMENT_CONVENIENCE_STORE_LIMIT_SHOW' || $module_item['configuration_key'] == 'MODULE_PAYMENT_TELECOM_LIMIT_SHOW' || $module_item['configuration_key'] == 'MODULE_PAYMENT_PAYPAL_LIMIT_SHOW' || $module_item['configuration_key'] == 'MODULE_PAYMENT_POINT_LIMIT_SHOW' || $module_item['configuration_key'] == 'MODULE_PAYMENT_FETCH_GOOD_LIMIT_SHOW' || $module_item['configuration_key'] == 'MODULE_PAYMENT_FREE_PAYMENT_LIMIT_SHOW') {
                $con_limit_show = unserialize($module_item['configuration_value']);   
                $con_limit_show_str = ''; 
                if (!empty($con_limit_show)) {
                  foreach ($con_limit_show as $lim_key => $lim_value) {
                    if ($lim_value == 1) {
                      $con_limit_show_str .= '会員&nbsp;&nbsp;'; 
                    } else {
                      $con_limit_show_str .= 'ゲスト'; 
                    }
                  }
                }
                $keys .= $con_limit_show_str;
              } else {
                $keys .= $module_item['configuration_value'];
              }
            }
            $keys .= '<br><br>';
          }
        }
        if (!isset($_GET['module']) || !$_GET['module']) {
          if(isset($directory_array[0])) {
            $_GET['module'] = str_replace('.php', '', $directory_array[0]);
          }
        }


        // 临时隐藏
        $contents[] = array('align' => 'left', 'text' => '<a href="' .  tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' .  @$_GET['module'] . '&action=edit') . '">' . tep_html_element_button(IMAGE_EDIT) . '</a>');
        if ($_GET['set'] == 'payment') {
          $link_form_str = '<a href="'.tep_href_link(FILENAME_OA_FORM, 'pcode='.$mInfo->code.'&type=1').'"><input type="button" value="'.FORM_SELL_TEXT.'"></a>'; 
          $link_form_str .= '<a href="'.tep_href_link(FILENAME_OA_FORM, 'pcode='.$mInfo->code.'&type=2').'"><input type="button" value="'.FORM_BUY_TEXT.'"></a>'; 
          $link_form_str .= '<a href="'.tep_href_link(FILENAME_OA_FORM, 'pcode='.$mInfo->code.'&type=3').'"><input type="button" value="'.FORM_MIX_TEXT.'"></a>'; 
          $contents[] = array('align' => 'left', 'text' => $link_form_str);
           
        }
        foreach(tep_get_sites() as $s){
          $contents[] = array('text' => '<b>'.$s['romaji'].'</b>');
          $contents[] = array('align' => 'left', 'text' => '<a href="' .  tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' .  @$_GET['module'] . '&action=edit&site_id='.$s['id']) . '">' .  tep_html_element_button(IMAGE_EDIT) . '</a>');
        }
        // 临时隐藏
        // $contents[] = array('text' => '<br>' . $mInfo->description . "<hr>");
        $contents[] = array('text' => '<div style="word-wrap:break-word;width:200px;overflow:hidden;"><br>' . $keys . '</div>');
      } else {
        $contents[] = array('text' => isset($mInfo->description)?$mInfo->description:'');
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
