<?php
/*
  $Id$
*/
require('includes/application_top.php');

if (!isset($_GET['gID'])) {
  $first_configuration_group_query = tep_db_query("select * from ".TABLE_CONFIGURATION_GROUP." where visible = '1' order by sort_order limit 1"); 
  $first_configuration_group = tep_db_fetch_array($first_configuration_group_query);
  tep_redirect(tep_href_link(FILENAME_CONFIGURATION, 'gID='.$first_configuration_group['configuration_group_id']));
}
if (isset($_GET['action']) && $_GET['action']) {
if(isset($_GET['cID'])){
  if (!is_numeric($_GET['cID'])) {
    $tmp_cid_info = explode('_',$_GET['cID']);
    $tmp_cid = $tmp_cid_info[0];
  } else {
    $tmp_cid = $_GET['cID']; 
  }
  $tmp_gid = $_GET['gID']; 
  $cfg_isset_query = tep_db_query("
      select configuration_id from ".TABLE_CONFIGURATION." 
      where configuration_group_id = '" . $tmp_gid . "' 
      and configuration_id = '" . $tmp_cid . "'");
  $cfg_isset = tep_db_fetch_array($cfg_isset_query);
  if ($_GET['action'] != 'tdel') {
    forward404Unless($cfg_isset);
  }
}
if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];//权限判断
         else $site_arr="";
    switch  ($_GET['action']) {
/*----------------------------------------
 case 'save' 判断该配置是添加或者更新
 case 'tdel' 删除配置
 case 'edit' 修改配置
 ---------------------------------------*/
    case 'save': 
        $configuration_value = tep_db_prepare_input($_POST['configuration_value']);
        $cID = tep_db_prepare_input($_GET['cID']);
        if (!is_numeric($cID))
      {
    $exploded_cid = explode('_',$cID);
    $site_id = $exploded_cid[1];
  forward401Unless(editPermission($site_arr, $site_id));//权限不够 跳到401
    $config_id = $exploded_cid[0];
    $upfile_name = $_FILES["upfile"]["name"];
    $upfile = $_FILES["upfile"]["tmp_name"];
    if(file_exists($upfile)){
        $path = DIR_FS_CATALOG . DIR_WS_IMAGES . $upfile_name;
        move_uploaded_file($upfile, $path);
        $configuration_value = tep_db_input($upfile_name);
    }
    tep_db_query(
        "insert into  " . TABLE_CONFIGURATION . " (
`configuration_title`, 
`configuration_key`, 
`configuration_value`, 
`configuration_description`, 
`configuration_group_id`, 
`sort_order`, 
`last_modified`, 
`date_added`, 
`use_function`, 
`set_function`, 
`site_id` )
 SELECT 
`configuration_title`, 
`configuration_key`, '".
        $configuration_value."',
`configuration_description`, 
`configuration_group_id`, 
`sort_order`, 
now(),
now(),
`use_function`, 
`set_function`, '".
        $site_id.
        "' FROM ".TABLE_CONFIGURATION." 
WHERE
`configuration_id` = ".$config_id);

        tep_redirect(tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $config_id.'&action=edit'));
      }
 $site_id= tep_get_conf_sid_by_id($cID);
        if($site_id['site_id'])    forward401Unless(editPermission($site_arr, $site_id['site_id']));//权限不够 跳到401
  //只在图像上传的时候
        $upfile_name = $_FILES["upfile"]["name"];
        $upfile = $_FILES["upfile"]["tmp_name"];
  if(file_exists($upfile)){
      $path = DIR_FS_CATALOG . DIR_WS_IMAGES . $upfile_name;
      move_uploaded_file($upfile, $path);
      $configuration_value = tep_db_input($upfile_name);
      tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . tep_db_input($upfile_name) . "', last_modified = now(),user_update='".$_POST['user_update']."' where configuration_id = '" . tep_db_input($cID) . "'");
  }
  
  $exists_configuration_raw = tep_db_query("select configuration_key from ".TABLE_CONFIGURATION." where configuration_id = '".$cID."'");       
  $exists_configuration_res = tep_db_fetch_array($exists_configuration_raw);
  $signal_single = false; 
  if ($exists_configuration_res) {
    if ($exists_configuration_res['configuration_key'] == 'DS_ADMIN_SIGNAL_TIME') {
      $signal_single = true; 
    }
  }
  if ($signal_single) {
    tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" .  tep_db_input(serialize($configuration_value)) . "', last_modified = now(),user_update='".$_POST['user_update']."' where configuration_id = '" . tep_db_input($cID) . "'");
  } else {
    tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . tep_db_input($configuration_value) . "', last_modified = now(),user_update='".$_POST['user_update']."' where configuration_id = '" . tep_db_input($cID) . "'");
  }
  tep_redirect(tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' .  tep_get_default_configuration_id_by_id($cID)));
  break;
    case 'tdel':
  $two_id = explode('_',$_GET['cID']);
  $config_id =$two_id[0];
  $default_id = $two_id[1];

  tep_db_query("DELETE FROM ".TABLE_CONFIGURATION." WHERE configuration_id = ".$config_id);
        tep_redirect(tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $default_id.'&action=edit'));
break;


    }
}

//取得了title
$cfg_group_query = tep_db_query("
      select configuration_group_title 
      from " . TABLE_CONFIGURATION_GROUP . " 
      where   configuration_group_id = '" . $_GET['gID'] . "'
  ");
$cfg_group = tep_db_fetch_array($cfg_group_query);
forward404Unless($cfg_group);
if(isset($_GET['cID'])){
  $cfg_isset_query = tep_db_query("
      select configuration_id from ".TABLE_CONFIGURATION." 
      where configuration_group_id = '" . $_GET['gID'] . "' 
      and  configuration_id = '" . $_GET['cID'] . "'
      and site_id='0'");
  $cfg_isset = tep_db_fetch_array($cfg_isset_query);
  forward404Unless($cfg_isset);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php echo constant('HEADING_TITLE_'.intval($_GET['gID'])); ?>
</title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
    <script language="javascript" src="includes/javascript/jquery_include.js"></script>
    <script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
    <script language="javascript" >

    </script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/gID=[^&]+/',$belong,$belong_array);
if($belong_array[0][0] != ''){

  $belong = $href_url.'?'.$belong_array[0][0];
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
    </head>
    <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
    <!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof -->

    <!-- body -->
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
       <tr>
          <td width="<?php echo BOX_WIDTH; ?>" valign="top">
             <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
    <!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
    <!-- left_navigation_eof -->
             </table>
          </td>
    <!-- body_text -->
          <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2"></td>
       </tr>
       <tr>
          <td>
             <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                   <td class="pageHeading"><?php echo constant($cfg_group ['configuration_group_title']); ?></td>
                   <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                </tr>
             </table>
          </td>
       </tr>
       <tr>
          <td>
             <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                   <td valign="top">
                      <table border="0" width="100%" cellspacing="0" cellpadding="2">
                         <tr class="dataTableHeadingRow">
                            <td class="dataTableHeadingContent" nowrap><?php echo TABLE_HEADING_CONFIGURATION_TITLE; ?></td>
                            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CONFIGURATION_VALUE; ?></td>
                            <td class="dataTableHeadingContent" align="right" nowrap><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                         </tr>
<?php
                          //ccdd  只先默认的值 
    $configuration_query = tep_db_query("
select 
    configuration_id, 
    configuration_title, 
    configuration_key, 
    configuration_value, 
    use_function 
from " . TABLE_CONFIGURATION . " 
where 
    configuration_group_id = '" . $_GET['gID'] . "' 
    and 
    `site_id` = '0'  order by sort_order"
               );
while ($configuration = tep_db_fetch_array($configuration_query)) {
    if (tep_not_null($configuration['use_function'])) {
  $use_function = $configuration['use_function'];
  if (ereg('->', $use_function)) {
      $class_method = explode('->', $use_function);
      if (!is_object(${$class_method[0]})) {
    include(DIR_WS_CLASSES . $class_method[0] . '.php');
    ${$class_method[0]} = new $class_method[0]();
      }
      $cfgValue = tep_call_function($class_method[1], $configuration['configuration_value'], ${$class_method[0]});
  } else {
      $cfgValue = tep_call_function($use_function, $configuration['configuration_value']);
  }
    } else {
  $cfgValue = $configuration['configuration_value'];
    }

    if (
        ((!isset($_GET['cID']) || !$_GET['cID']) || ($_GET['cID'] == $configuration['configuration_id'])) 
        && (!isset($cInfo) || !$cInfo) 
        && (!isset($_GET['action']) or substr($_GET['action'], 0, 3) != 'new')
    ) {
  $cfg_extra_query = tep_db_query("select  configuration_key, configuration_description, date_added, last_modified, use_function, set_function,user_added,user_update from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
//  $cfg_extra_query = tep_db_query("select configuration_key,configuration_description,date_added,last_modified,use_function,set_function  from ".TABLE_CONFIGURATION. " where configuration_key = ( select configuration_key from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "')");
  $cfg_extra= tep_db_fetch_array($cfg_extra_query);
/*  while($cfg_extra = tep_db_fetch_array($cfg_extra_query))
  {
  // $cInfo_array = tep_array_merge($configuration, $cfg_extra);
  $cInfos_array[]=$cfg_extra;
  }
*/
  $cInfo_array = tep_array_merge($configuration, $cfg_extra);
  $cInfo = new objectInfo($cInfo_array);
    }
  $even = 'dataTableSecondRow';
  $odd  = 'dataTableRow';
  if (isset($nowColor) && $nowColor == $odd) {
    $nowColor = $even; 
  } else {
    $nowColor = $odd; 
  }
    if ( (isset($cInfo) && is_object($cInfo)) && ($configuration['configuration_id'] == $cInfo->configuration_id) ) {
  echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id . '&action=edit') . '\'">' . "\n";
    } else {
  echo '                  <tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $configuration['configuration_id']) . '\'">' . "\n";
    }
?>
    <td class="dataTableContent" nowrap><?php echo $configuration['configuration_title']; ?></td>
                         <td class="dataTableContent">
                         <?php 
                         if ($configuration['configuration_key'] == 'DS_ADMIN_SIGNAL_TIME') {
                           $tmp_setting_array = @unserialize(stripslashes($cfgValue));
                           echo SIGNAL_GREEN.':'.(int)($tmp_setting_array['green'][0].$tmp_setting_array['green'][1].$tmp_setting_array['green'][2].$tmp_setting_array['green'][3]);
                           echo SIGNAL_YELLOW.':'.(int)($tmp_setting_array['yellow'][0].$tmp_setting_array['yellow'][1].$tmp_setting_array['yellow'][2].$tmp_setting_array['yellow'][3]);
                           echo SIGNAL_RED.':'.(int)($tmp_setting_array['red'][0].$tmp_setting_array['red'][1].$tmp_setting_array['red'][2].$tmp_setting_array['red'][3]);
                         } else {
                           echo mb_substr(htmlspecialchars($cfgValue),0,50); 
                         }
                         ?>
                         </td>
                                              <td class="dataTableContent" align="right"><?php if ( (isset($cInfo) && is_object($cInfo)) && ($configuration['configuration_id'] == $cInfo->configuration_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $configuration['configuration_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
    </tr>
<?php
                                                                                                                                                                       }
?>
</table></td>
<?php
$heading = array();
$contents = array();
switch (isset($_GET['action']) && $_GET['action']) {
case 'edit':
    $heading[] = array('text' => '<b>' . $cInfo->configuration_title . '</b>');
    if ($cInfo->set_function) {
      if ($cInfo->configuration_key == 'DS_ADMIN_SIGNAL_TIME') {
        eval('$value_field = '.$cInfo->set_function."'".$cInfo->configuration_value."');"); 
      } else {
        eval('$value_field = ' . $cInfo->set_function . '\'' .  htmlspecialchars(addcslashes($cInfo->configuration_value, '\'')) . '\');');
      }
      $value_field = htmlspecialchars_decode($value_field);
    } else {
      if($cInfo->configuration_key == 'ADMINPAGE_LOGO_IMAGE') {
        $value_field = tep_draw_file_field('upfile') . '<br>' . $cInfo->configuration_value;
      } else {
    //$value_field = tep_draw_input_field('configuration_value', $cInfo->configuration_value);
        $value_field = '<textarea name="configuration_value" rows="5" cols="35">'. $cInfo->configuration_value .'</textarea>';
      }
    }
// 针对 logo—image 做特殊处理
    if($cInfo->configuration_key == 'ADMINPAGE_LOGO_IMAGE') {
  $contents = array('form' => tep_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
    } else {
  $contents = array('form' => tep_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id . '&action=save'));
    }
    $contents[] = array('text' => '<input type="hidden" name="user_update" value="'.$user_info['name'].'">');
    $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
    $contents[] = array('text' => '<br><b>' . $cInfo->configuration_title . '</b><br>' . $cInfo->configuration_description . '<br>' . $value_field);
  
    if ($cInfo->configuration_key == 'DS_ADMIN_SIGNAL_TIME') {
      $contents[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_button(IMAGE_UPDATE, 'onclick="check_signal_time_select()"') . '&nbsp;<a class="new_product_reset" href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] .  '&cID=' . $cInfo->configuration_id) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
    } else {
      $contents[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_UPDATE) . '&nbsp;<a class="new_product_reset" href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] .  '&cID=' . $cInfo->configuration_id) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
    }

//----------------------------------

    $contents_sites_array = array();
    $select_site_configure = tep_db_query('select * from sites order by order_num');
    // configuration admin page only
    if(!in_array($cInfo->configuration_key, array(
            'MAX_DISPLAY_CUSTOMER_MAIL_RESULTS',
            'MAX_DISPLAY_FAQ_ADMIN',
            'POINT_EMAIL_TEMPLATE',
            'POINT_EMAIL_DATE',
            'ADMINPAGE_LOGO_IMAGE',
            'MAX_DISPLAY_PW_MANAGER_RESULTS',
            'MAX_DISPLAY_ORDERS_RESULTS',
            'USER_AGENT_LIGHT_KEYWORDS',
            'HOST_NAME_LIGHT_KEYWORDS',
            'USER_AGENT_LIGHT_KEYWORDS',
            'HOST_NAME_LIGHT_KEYWORDS',
            'IP_LIGHT_KEYWORDS',
            'OS_LIGHT_KEYWORDS',
            'BROWSER_LIGHT_KEYWORDS',
            'HTTP_ACCEPT_LANGUAGE_LIGHT_KEYWORDS',
            'SYSTEM_LANGUAGE_LIGHT_KEYWORDS',
            'USER_LANGUAGE_LIGHT_KEYWORDS',
            'SCREEN_RESOLUTION_LIGHT_KEYWORDS',
            'COLOR_DEPTH_LIGHT_KEYWORDS',
            'FLASH_LIGHT_KEYWORDS',
            'FLASH_VERSION_LIGHT_KEYWORDS',
            'DIRECTOR_LIGHT_KEYWORDS',
            'QUICK_TIME_LIGHT_KEYWORDS',
            'REAL_PLAYER_LIGHT_KEYWORDS',
            'WINDOWS_MEDIA_LIGHT_KEYWORDS',
            'PDF_LIGHT_KEYWORDS',
            'JAVA_LIGHT_KEYWORDS',
            'TELNO_KEYWORDS', 
            'ORDER_INFO_TRANS_NOTICE', 
            'ORDER_INFO_TRANS_WAIT', 
            'ORDER_INFO_INPUT_FINISH', 
            'ORDER_INFO_ORDER_INFO', 
            'ORDER_INFO_CUSTOMER_INFO', 
            'ORDER_INFO_REFERER_INFO', 
            'ORDER_INFO_ORDER_HISTORY', 
            'ORDER_INFO_REPUTAION_SEARCH', 
            'ORDER_INFO_PRODUCT_LIST', 
            'ORDER_INFO_ORDER_COMMENT', 
            'ORDER_INFO_BASIC_TEXT',
            'DB_CALC_PRICE_HISTORY_DATE',
            'ORDERS_EMPTY_EMAIL_TITLE',
            'ORDERS_EMPTY_EMAIL_TEXT',
            'ORDER_EFFECTIVE_DATE',
            'DS_ADMIN_SIGNAL_TIME',
            'SEG_CRONTAB_ROW',
            'SEG_CRONTAB_SLEEP',
            ))) 
    while($site = tep_db_fetch_array($select_site_configure)) {
  $site_romaji[] = $site['romaji'];
  $select_configurations = tep_db_query('select * from configuration where configuration_key =\''.$cInfo->configuration_key.'\' and site_id = '.$site['id'] );
        $fetch_result = tep_db_fetch_array($select_configurations);
  // if not exist ,copy from which site_id = 0
        if (!$fetch_result){
      $fetch_result = tep_db_fetch_array(tep_db_query('select * from configuration where configuration_key=\''.$cInfo->configuration_key.'\' and site_id = 0'));
      $fetch_result['configuration_id'].='_'.$site['id'];
      $fetch_result['site_id']=$site['id'];
  }
  if($fetch_result['set_function']) {
      //eval('$value_field = ' . $cInfo->set_function . '\'' . htmlspecialchars($cInfo->configuration_value) . '\');');
      //$value_field = htmlspecialchars_decode($value_field);
    
      eval('$value_field = ' . $fetch_result['set_function'] . '\'' .  htmlspecialchars(addcslashes($fetch_result['configuration_value'], '\'')) . '\');');
  } else {
      if($fetch_result['configuration_key'] == 'ADMINPAGE_LOGO_IMAGE') {
    $value_field = tep_draw_file_field('upfile'). '<br>' . $fetch_result['configuration_value'];
      } else {
    $value_field = '<textarea name="configuration_value" rows="5" cols="35">'. $fetch_result['configuration_value'] .'</textarea>';
      }
  }
  if($fetch_result['configuration_key'] == 'ADMINPAGE_LOGO_IMAGE') {
      $contents_site = array('form' => tep_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $fetch_result['configuration_id'] . '&action=save', 'post', 'enctype="multipart/form-data"'));
  } else {
      $contents_site = array('form' => tep_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $fetch_result['configuration_id'] . '&action=save'));
  }
//  $contents_site[] = array('text' => TEXT_INFO_EDIT_INTRO); 
  $contents_site[] = array('text' => '<br><b>' . $fetch_result['configuration_title'] . '</b><br>' . $fetch_result['configuration_description'] . '<br>' . $value_field);
  //if exists ,can be delete ,or  can not 
  if (is_numeric($fetch_result['configuration_id'])){
  $contents_site[] = array(
      'align' => 'center',
      'text' => '<br>' .  tep_html_element_submit(IMAGE_UPDATE) .'&nbsp;<a href="' .  tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] .  '&action=tdel&cID=' .  $fetch_result['configuration_id'].'_'.$cInfo->configuration_id) .  '">'.tep_html_element_button(IMAGE_DEFFECT).'</a>'. '&nbsp;<a class="new_product_reset" href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>');
//      $contents_site[] = array('align' => 'center', 'text' => '<br>' . '<a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&action=tdel&cID=' . $fetch_result['configuration_id'].'_'.$cInfo->configuration_id) . '">'.tep_image_button('button_delete.gif',IMAGE_DELETE).'</a>');
  }else {
    $contents_site[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_EFFECT) . '&nbsp;<a class="new_product_reset" href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] .  '&cID=' . $cInfo->configuration_id) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
  }
  $contents_sites_array[] = $contents_site;
  
    }


    break;
default:
    if (isset($cInfo) && is_object($cInfo)) {

        $heading[] = array('text' => '<b>' . $cInfo->configuration_title . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' .  tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' .  $cInfo->configuration_id . '&action=edit') . '">' .  tep_html_element_button(IMAGE_EDIT) . '</a>');
        $contents[] = array('text' => '<br>'. $cInfo->configuration_description);
      if(tep_not_null($cInfo->user_added)){
$contents[] = array('text' => TEXT_USER_ADDED . '&nbsp;' . $cInfo->user_added);
      }else{
$contents[] = array('text' => TEXT_USER_ADDED . '&nbsp;' . TEXT_UNSET_DATA);
      }
      if(tep_not_null(tep_datetime_short($cInfo->date_added))){
$contents[] = array('text' =>  TEXT_INFO_DATE_ADDED . '&nbsp;' . tep_datetime_short($cInfo->date_added));
      }else{
$contents[] = array('text' =>  TEXT_INFO_DATE_ADDED . '&nbsp;' . TEXT_UNSET_DATA);
      }
      if(tep_not_null($cInfo->user_update)){
$contents[] = array('text' =>  TEXT_USER_UPDATE . '&nbsp;' . $cInfo->user_update);
      }else{
$contents[] = array('text' =>  TEXT_USER_UPDATE . '&nbsp;' . TEXT_UNSET_DATA);}
      if(tep_not_null(tep_datetime_short($cInfo->last_modified))){
$contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . '&nbsp;' . tep_datetime_short($cInfo->last_modified));
      }else{
$contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . '&nbsp;' . TEXT_UNSET_DATA);
      }
    }
    break;
}
if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";
    
    $box = new box;
    echo $box->infoBox($heading, $contents);
    echo '</form>';

    $box = null;
    if ( isset($contents_sites_array)){
  $romaji_i = 0;
    foreach($contents_sites_array as $contents_site) {
  
  $box = new box;
  echo $box->infoBox(array(array('text'=>$site_romaji[$romaji_i])),$contents_site);
  $romaji_i++;
    echo '</form>';
    }
}


    echo '            </td>' . "\n";
  }



?>
          </tr>
        </table>
        </td>
      </tr>
    </table>
    </div></div></td>
<!-- body_text_eof -->
  </tr>
</table>
<!-- body_eof -->

<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
