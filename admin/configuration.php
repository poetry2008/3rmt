<?php
/*
  $Id$
*/
require('includes/application_top.php');

if (isset($HTTP_GET_VARS['action']) && $HTTP_GET_VARS['action']) {
    if  ($HTTP_GET_VARS['action']=='save') {
        $configuration_value = tep_db_prepare_input($HTTP_POST_VARS['configuration_value']);
        $cID = tep_db_prepare_input($HTTP_GET_VARS['cID']);
		
	//画像アップロード時のみ
        $upfile_name = $_FILES["upfile"]["name"];
        $upfile = $_FILES["upfile"]["tmp_name"];
	if(file_exists($upfile)){
	    $path = DIR_FS_CATALOG . DIR_WS_IMAGES . $upfile_name;
	    move_uploaded_file($upfile, $path);
	    $configuration_value = tep_db_input($upfile_name);
	    tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . tep_db_input($upfile_name) . "', last_modified = now() where configuration_id = '" . tep_db_input($cID) . "'");
	}
		

        tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . tep_db_input($configuration_value) . "', last_modified = now() where configuration_id = '" . tep_db_input($cID) . "'");
        tep_redirect(tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $cID));
    }
}

//取得了title
$cfg_group_query = tep_db_query("
      select configuration_group_title 
      from " . TABLE_CONFIGURATION_GROUP . " 
      where   configuration_group_id = '" . $HTTP_GET_VARS['gID'] . "'
  ");
$cfg_group = tep_db_fetch_array($cfg_group_query);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
    <script language="javascript" src="includes/general.js"></script>
    <script language="javascript" >

    </script>
    </head>
    <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
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
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <td class="pageHeading"><?php echo $cfg_group ['configuration_group_title']; ?></td>
    <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
    </tr>
    </table></td>
    </tr>
    <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
    <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CONFIGURATION_TITLE; ?></td>
    <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CONFIGURATION_VALUE; ?></td>
    <td class="dataTableHeadingContent" align="right" nowrap><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
    </tr>
<?php
													//ccdd  只先默认的值 
    $configuration_query = tep_db_query("
select 
    configuration_id, 
    configuration_title, 
    configuration_value, 
    use_function 
from " . TABLE_CONFIGURATION . " 
where 
    configuration_group_id = '" . $HTTP_GET_VARS['gID'] . "' 
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
        ((!isset($HTTP_GET_VARS['cID']) || !$HTTP_GET_VARS['cID']) || ($HTTP_GET_VARS['cID'] == $configuration['configuration_id'])) 
        && (!isset($cInfo) || !$cInfo) 
        && (!isset($HTTP_GET_VARS['action']) or substr($HTTP_GET_VARS['action'], 0, 3) != 'new')
    ) {
	$cfg_extra_query = tep_db_query("select  configuration_key, configuration_description, date_added, last_modified, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
//	$cfg_extra_query = tep_db_query("select configuration_key,configuration_description,date_added,last_modified,use_function,set_function  from ".TABLE_CONFIGURATION. " where configuration_key = ( select configuration_key from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "')");
	$cfg_extra= tep_db_fetch_array($cfg_extra_query);
/*	while($cfg_extra = tep_db_fetch_array($cfg_extra_query))
	{
	// $cInfo_array = tep_array_merge($configuration, $cfg_extra);
	$cInfos_array[]=$cfg_extra;
	}
*/
	$cInfo_array = tep_array_merge($configuration, $cfg_extra);
	$cInfo = new objectInfo($cInfo_array);
    }

    if ( (isset($cInfo) && is_object($cInfo)) && ($configuration['configuration_id'] == $cInfo->configuration_id) ) {
	echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $cInfo->configuration_id . '&action=edit') . '\'">' . "\n";
    } else {
	echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $configuration['configuration_id']) . '\'">' . "\n";
    }
?>
    <td class="dataTableContent"><?php echo $configuration['configuration_title']; ?></td>
											   <td class="dataTableContent"><?php echo mb_substr(htmlspecialchars($cfgValue),0,50); ?></td>
																							<td class="dataTableContent" align="right"><?php if ( (isset($cInfo) && is_object($cInfo)) && ($configuration['configuration_id'] == $cInfo->configuration_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $configuration['configuration_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
    </tr>
<?php
																																																																																       }
?>
</table></td>
<?php
$heading = array();
$contents = array();
switch (isset($HTTP_GET_VARS['action']) && $HTTP_GET_VARS['action']) {
case 'edit':
    $heading[] = array('text' => '<b>' . $cInfo->configuration_title . '</b>');

    if ($cInfo->set_function) {
	eval('$value_field = ' . $cInfo->set_function . '"' . htmlspecialchars($cInfo->configuration_value) . '");');
    } else {
        if($cInfo->configuration_key == 'ADMINPAGE_LOGO_IMAGE') {
	    $value_field = tep_draw_file_field('upfile') . '<br>' . $cInfo->configuration_value;
	} else {
	    $value_field = tep_draw_input_field('configuration_value', $cInfo->configuration_value);
	}
    }
// 针对 logo—image 做特殊处理
    if($cInfo->configuration_key == 'ADMINPAGE_LOGO_IMAGE') {
	$contents = array('form' => tep_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $cInfo->configuration_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
    } else {
	$contents = array('form' => tep_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $cInfo->configuration_id . '&action=save'));
    }
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br><b>' . $cInfo->configuration_title . '</b><br>' . $cInfo->configuration_description . '<br>' . $value_field);
	
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_update.gif', IMAGE_UPDATE) . '&nbsp;<a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $cInfo->configuration_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');

//----------------------------------
// for 3rmt
      $contents_sites_array = array();
    $select_site_configure = tep_db_query('select * from sites order by order_num');
    while(    $site = tep_db_fetch_array($select_site_configure)) {
	$select_configurations = tep_db_query('select * from configuration where configuration_key =\''.$cInfo->configuration_key.'\' and site_id = '.$site['id'] );
        $fetch_result = tep_db_fetch_array($select_configurations);

	if($fetch_result['set_function']) {
	    eval('$value_field = ' . $fetch_result['set_function'] . '"' . htmlspecialchars($fetch_result['configuration_value']) . '");');
	} else {
	    if($fetch_result['configuration_key'] == 'ADMINPAGE_LOGO_IMAGE') {
		$value_field = tep_draw_file_field('upfile'). '<br>' . $fetch_result['configuration_value'];
	    } else {
		$value_field = tep_draw_input_field('configuration_value', $fetch_result['configuration_value']);
	    }
	}
    if($fetch_result['configuration_key'] == 'ADMINPAGE_LOGO_IMAGE') {
	$contents_site = array('form' => tep_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $fetch_result['configuration_id'] . '&action=save', 'post', 'enctype="multipart/form-data"'));
    } else {
	$contents_site = array('form' => tep_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $fetch_result['configuration_id'] . '&action=save'));
    }
      $contents_site[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents_site[] = array('text' => '<br><b>' . $fetch_result['configuration_title'] . '</b><br>' . $fetch_result['configuration_description'] . '<br>' . $value_field);
      var_dump($fetch_result);
      $contents_site[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_update.gif', IMAGE_UPDATE) . '&nbsp;<a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $fetch_result['configuration_id']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
			       $contents_sites_array[] = $contents_site;
    }
//for 3rmt
//---------------------------------------------------------------
      break;
    default:
      if (isset($cInfo) && is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . $cInfo->configuration_title . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $cInfo->configuration_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>');
        $contents[] = array('text' => '<br>' . $cInfo->configuration_description);
        $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($cInfo->date_added));
        if (tep_not_null($cInfo->last_modified)) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($cInfo->last_modified));
      }
      break;
  }
  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;


    echo $box->infoBox($heading, $contents);
    $box = null;
  foreach($contents_sites_array as $contents_site) {
    $box = new box;
    echo $box->infoBox(array(array('text'=>'xcvxc')),$contents_site);
  }
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
