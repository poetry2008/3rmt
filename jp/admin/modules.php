<?php
/*
  $Id$
*/
require('includes/application_top.php');
$set = $_GET['set'];
if(empty($set) or !is_dir( DIR_FS_CATALOG_MODULES .$_GET['set'])){
  $set = $_GET['set'];
}
$module_type = $set ;
$module_directory = DIR_FS_CATALOG_MODULES . $module_type.'/';
$module_key = 'MODULE_'.strtoupper($module_type).'_INSTALLED';
define('HEADING_TITLE', $tmp  =
    constant('HEADING_TITLE_MODULES_'.strtoupper($module_type)));

if (isset($_GET['action'])) 
  switch ($_GET['action']) {
/*-----------------------------------
 case 'save'  更新模块
 case 'install' 安装模块
 case 'remove' 清除模块
 ----------------------------------*/
  case 'save':
    $post_configuration = $_POST['configuration'];
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
  
 tep_db_query("update " . TABLE_CONFIGURATION . " set user_update = '".$_SESSION['user_name']."', last_modified = '".date('Y-m-d H:i:s',time())."' where configuration_key =  'MODULE_".strtoupper($set)."_".strtoupper($_GET['module'])."_STATUS' and site_id = '".$_POST['site_id']."'");
 tep_db_query("update " . TABLE_CONFIGURATION . " set user_update =  '".$_SESSION['user_name']."', last_modified = '".date('Y-m-d H:i:s',time())."' where configuration_key =  'MODULE_".strtoupper($set)."_".str_replace('OT_','',strtoupper($_GET['module']))."_STATUS' and site_id = '".$_POST['site_id']."'");

 tep_db_query("update " . TABLE_CONFIGURATION . " set user_update =
     '".$_SESSION['user_name']."', last_modified = '".date('Y-m-d H:i:s',time())."'
     where configuration_key =
     'MODULE_".strtoupper($set)."_".strtoupper($_GET['module'])."_TITLE' and site_id = '".$_POST['site_id']."'");
    if ($_GET['set'] == 'payment') { 
      if ($site_id != 0) {
        $limit_show_str = ''; 
        foreach ($_POST['configuration'] as $key => $value){
          if(preg_match('/.*LIMIT_SHOW/', $key)) {
            $limit_show_str = $key;
            break;
          }
        }
        //如果有CHECKBOX
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
          tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . serialize($value) . "' where configuration_key = '" .  $limit_show_str . "' and site_id = '".$site_id."'");
        } else {

          if (!tep_db_num_rows(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key='MODULE_PAYMENT_".strtoupper($_GET['module'])."_LIMIT_SHOW' and site_id='".$site_id."'"))) {

            $cp_show_configuration = tep_db_fetch_array(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key='MODULE_PAYMENT_".strtoupper($_GET['module'])."_LIMIT_SHOW' and site_id='0'"));
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
                  '".serialize(array())."', 
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
          tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . serialize($blank_show_arr) . "' where configuration_key = 'MODULE_PAYMENT_" . strtoupper($_GET['module'])  . "_LIMIT_SHOW' and site_id = '".$site_id."'");
        }
      }
    }
    
    $key = '';
    $value = '';
    foreach($post_configuration as $key => $value){

      if (
          !tep_db_num_rows(tep_db_query("select * from ".TABLE_CONFIGURATION." where configuration_key='".$key."' and site_id='".$site_id."'")
                           )
          ) {

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

      if (preg_match('/.*LIMIT_SHOW/', $key)) {
        tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . serialize($value) . "' where configuration_key = '" . $key . "' and site_id = '".$site_id."'");
      } else if (preg_match('/.*_POINT_RATE$/', $key)) {
        tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . $value/100 . "' where configuration_key = '" . $key . "' and site_id = '".$site_id."'");
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
  <title>
<?php 
if(isset($_GET['set']) && $_GET['set'] == "payment"){
echo HEADING_TITLE_MODULES_PAYMENT;
}else if(isset($_GET['set']) && $_GET['set'] == "order_total"){
echo HEADING_TITLE_MODULES_ORDER_TOTAL;
}else if(isset($_GET['set']) && $_GET['set'] == "metaseo"){
echo HEADING_TITLE_MODULES_METASEO;
}
else{
	echo TITLE;
}
?>

</title>
  <link rel="stylesheet" type="text/css" href="includes/stylesheet.css?v=<?php echo $back_rand_info?>">
  <script language="javascript" src="includes/javascript/jquery_include.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js&v=<?php echo $back_rand_info?>"></script>
<script type="text/javascript">
	var js_modules_self = '<?php echo $_SERVER['PHP_SELF']?>';
	var js_onetime_pwd = '<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>';
	var js_onetime_error = '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>';
</script>
<script language="javascript" src="includes/javascript/admin_modules.js?v=<?php echo $back_rand_info?>"></script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/set=[^&]+/',$belong,$belong_array);
if($belong_array[0][0] != ''){

  $belong = $href_url.'?'.$belong_array[0][0];
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
  </head>
  <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
  <?php
  if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
</script>
   <?php }?>
     <!-- header //-->
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
  <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
  <!-- left_navigation //-->
  <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  <!-- left_navigation_eof //-->
  </table></td>
  <!-- body_text //-->
  <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
  <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
  <td class="pageHeading" height="40"><?php echo HEADING_TITLE; ?></td>
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
ksort($directory_array_sorted);
foreach ($directory_array_sorted as $i => $files) {

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

      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      if (isset($mInfo)&& (is_object($mInfo)) && ($class == $mInfo->code) ) {
        if ($module->check() > 0) {
          echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ondblclick="document.location.href=\'' . tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class . '&action=edit') . '\'">' . "\n";
        } else {
          echo '              <tr class="dataTableRowSelected">' . "\n";
        }
      } else {
        echo '              <tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'" ondblclick="document.location.href=\'' . tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $class) . '\'">' . "\n";
      }
      ?>
      <td class="dataTableContent">
      <?php if (isset($module->link) && $module->link) {?><div class="float_left"><a target='_blank' href="<?php echo $ex_site['url'].'/'.$module->link;?>"><?php echo tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW);?></a></div><?php } ?>
      <div class="comp_width">
      <?php 
      echo $module->title;
      ?>
      </div>
      </td>
      <td class="dataTableContent" align="left"><?php echo $module->link ? ($ex_site['url'].'/'.$module->link) : '';?></td>
                                                                                                                          <td class="dataTableContent" align="right"><?php if (is_numeric($module->sort_order)) echo $module->sort_order; ?></td>
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
  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title,
    configuration_key, configuration_value, configuration_description,
    configuration_group_id, sort_order, date_added , user_added) values ('Installed Modules', '".
      $module_key . "', '" . implode(';', $installed_modules) . "', 'This is
      automatically updated. No need to edit.', '6', '0', now(),
      '".$_SESSION['user_name']."')");
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
/*-----------------------------------------
 case 'edit' 编辑模块内容 
 ----------------------------------------*/
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
    } else {
      $keys .=  $value['title'] . '<br>' . $value['description'] . '<br>';
      if ($value['set_function']) {
        eval('$keys .= ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");

      } else {
        if (preg_match("/^MODULE_PAYMENT_.*_POINT_RATE$/",$key)) {
          $keys .= tep_draw_input_field('configuration[' . $key . ']', sprintf('%s', $value['value']*100)).'&nbsp;%';
        } else {
          $keys .= tep_draw_input_field('configuration[' . $key . ']', $value['value']);
        }
      }
      $keys .= '<br><br>';
    }
  }
  $keys = substr($keys, 0, strrpos($keys, '<br><br>'));
  $heading[] = array('text' => $mInfo->title);

  $contents = array('form' => tep_draw_form('modules', FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' . $_GET['module'] . '&action=save'));
  $contents[] = array('text' => $keys);
  $contents[] = array('text' => '<input type="hidden" name="site_id" value="'.$site_id.'">');
  $contents[] = array('align' => 'center', 'text' => '<br><a href="javascript:void(0);">' .tep_html_element_button(IMAGE_SAVE, 'onclick="toggle_module_form(\''.$ocertify->npermission.'\')"') . '</a> <a href="' .  tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' .  $_GET['module']) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');

  break;
default:

  $heading[] = array('text' => (isset($mInfo->title)?$mInfo->title:''));
  if (isset($mInfo->status) && $mInfo->status == '1') {

    $keys = '';

    foreach(tep_get_sites() as $s){
      $keys .= "<br>".$s['romaji']."<hr>";
      reset($mInfo->keys);
      while (list($k, $value) = each($mInfo->keys)) {
        $module_item = tep_db_fetch_array(tep_db_query("select * from configuration where configuration_key = '".$k."' and site_id = '".$s['id']."'"));
        if ($module_item === false) {
          $module_item = tep_db_fetch_array(tep_db_query("select * from configuration where configuration_key = '".$k."' and site_id = '0'"));
        }
        $keys .=  $module_item['configuration_title'] . '<br>';
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
          if(preg_match("/MODULE_PAYMENT_.*_LIMIT_SHOW/",$module_item['configuration_key'])) {
            $con_limit_show = unserialize($module_item['configuration_value']);   
            $con_limit_show_str = ''; 
            if (!empty($con_limit_show)) {
              foreach ($con_limit_show as $lim_key => $lim_value) {
                if ($lim_value == 1) {
                  $con_limit_show_str .= TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_MEMBER.'&nbsp;&nbsp;'; 
                } elseif($lim_value ==2){
                  $con_limit_show_str .= TEXT_TEP_CFG_PAYMENT_CHECKBOX_OPTION_CUSTOMER; 
                }
              }
            }
            $keys .= $con_limit_show_str;
          } else if (preg_match("/^MODULE_PAYMENT_.*_POINT_RATE$/",$module_item['configuration_key'])) {
            $keys .= $module_item['configuration_value'] * 100 . '%';
          } else {
            $keys .= $module_item['configuration_value'];
          }
        }
        $keys .= '<br><br>';
      }
    }
    
    if (!isset($_GET['module']) || !$_GET['module']) {
      if (!empty($directory_array_sorted)) {
        foreach ($directory_array_sorted as $ds_key => $ds_info) {
          if (isset($ds_info[0])) {
            $_GET['module'] = str_replace('.php', '', $ds_info[0]);
          }
          break; 
        }
      }
    }


    // 临时隐藏
    $contents[] = array('align' => 'left', 'text' => '<a href="' .  tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' .  @$_GET['module'] . '&action=edit') . '">' . tep_html_element_button(IMAGE_EDIT) . '</a>');
    if ($_GET['set'] == 'payment') {
      $link_form_str .= '<a href="'.tep_href_link(FILENAME_OA_FORM, 'preturn='.$mInfo->code.'&pcode='.$mInfo->title.'&type=1').'">'.tep_html_element_button(FORM_SELL_TEXT).'</a>'; 
      $link_form_str .= '<a href="'.tep_href_link(FILENAME_OA_FORM, 'preturn='.$mInfo->code.'&pcode='.$mInfo->title.'&type=2').'">'.tep_html_element_button(FORM_BUY_TEXT).'</a>'; 
      $link_form_str .= '<a href="'.tep_href_link(FILENAME_OA_FORM, 'preturn='.$mInfo->code.'&pcode='.$mInfo->title.'&type=3').'">'.tep_html_element_button(FORM_MIX_TEXT).'</a>'; 
      $link_form_str .= '<a href="'.tep_href_link(FILENAME_OA_FORM, 'preturn='.$mInfo->code.'&pcode='.$mInfo->title.'&type=4').'">'.tep_html_element_button(FORM_PREORDER_TEXT).'</a>'; 
      $contents[] = array('align' => 'left', 'text' => $link_form_str);
           
    }
    if($set == 'order_total'){
     $get_module = str_replace('OT_','',strtoupper($_GET['module']));
     $suffix = 'STATUS';
    }else if($set == 'payment'){
     $get_module = strtoupper($_GET['module']); 
     $suffix = 'STATUS';
    }else if($set == 'metaseo'){
     $get_module = strtoupper($_GET['module']); 
     $suffix = 'TITLE';
    }
    foreach(tep_get_sites() as $s){
     $check_query = tep_db_query(" select *  from " .  TABLE_CONFIGURATION . "
         where configuration_key =  'MODULE_".strtoupper($set)."_".$get_module."_".$suffix."'  and site_id = '".$s['id']."'");
     $check = tep_db_fetch_array($check_query);
      $contents[] = array('text' => $s['romaji']);
      $contents[] = array('align' => 'left', 'text' => '<a href="' .  tep_href_link(FILENAME_MODULES, 'set=' . $_GET['set'] . '&module=' .  @$_GET['module'] . '&action=edit&site_id='.$s['id']) . '">' .  tep_html_element_button(IMAGE_EDIT) . '</a>');
      if(tep_not_null($check['user_added'])){
      $contents[] = array('align' => 'left', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.$check['user_added']);
      }else{
      $contents[] = array('align' => 'left', 'text' => TEXT_USER_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA);
      }if(tep_not_null(tep_datetime_short($check['date_added']))){
      $contents[] = array('align' => 'left', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.$check['date_added']);
      }else{
      $contents[] = array('align' => 'left', 'text' => TEXT_DATE_ADDED.'&nbsp;&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA);
      }if(tep_not_null($check['user_update'])){
      $contents[] = array('align' => 'left', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.$check['user_update']);
      }else{
      $contents[] = array('align' => 'left', 'text' => TEXT_USER_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA);
      }if(tep_not_null(tep_datetime_short($check['last_modified']))){
      $contents[] = array('align' => 'left', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.$check['last_modified']);
      }else{
      $contents[] = array('align' => 'left', 'text' => TEXT_DATE_UPDATE.'&nbsp;&nbsp;&nbsp;&nbsp;'.TEXT_UNSET_DATA);
      }
    }
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
</table>
</div>
</div>
</td>
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
