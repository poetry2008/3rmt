<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  switch (isset($_GET['action']) ? $_GET['action']:'') {
    case 'insert':
    case 'save':
      $color_id = tep_db_prepare_input($_GET['mID']);
      $color_name = tep_db_prepare_input($_POST['color_name']);
      $color_tag = tep_db_prepare_input($_POST['color_tag']);
      $sort_id = tep_db_prepare_input($_POST['sort_id']);

      $sql_data_array = array('color_name' => $color_name,
                              'color_tag' => $color_tag,
                              'sort_id' => $sort_id);

      if ($_GET['action'] == 'insert') {
        $insert_sql_data = array();
        $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
        tep_db_perform(TABLE_COLOR, $sql_data_array);
        $color_id = tep_db_insert_id();
      } elseif ($_GET['action'] == 'save') {
        $update_sql_data = array();
        $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
        tep_db_perform(TABLE_COLOR, $sql_data_array, 'update', "color_id = '" . tep_db_input($color_id) . "'");
      }
      if (USE_CACHE == 'true') {
        tep_reset_cache_block('color');
      }

      tep_redirect(tep_href_link(FILENAME_COLOR, 'page=' . $_GET['page'] . '&mID=' . $color_id));
      break;
    case 'deleteconfirm':
      $color_id = tep_db_prepare_input($_GET['mID']);

      tep_db_query("delete from " . TABLE_COLOR . " where color_id = '" . tep_db_input($color_id) . "'");
      tep_db_query("delete from " . TABLE_COLOR_TO_PRODUCTS . " where color_id = '" . tep_db_input($color_id) . "'");

      if (USE_CACHE == 'true') {
        tep_reset_cache_block('color');
      }

      tep_redirect(tep_href_link(FILENAME_COLOR, 'page=' . $_GET['page']));
      break;
  }
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
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
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COLORS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $color_query_raw = "select color_id, color_name, color_tag, sort_id from " . TABLE_COLOR . " order by sort_id, color_name";
  $color_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $color_query_raw, $color_query_numrows);
  $color_query = tep_db_query($color_query_raw);
  while ($color = tep_db_fetch_array($color_query)) {
    if (((!isset($_GET['mID']) || !$_GET['mID']) || (@$_GET['mID'] == $color['color_id'])) && (!isset($mInfo) || !$mInfo) && (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new')) {
      $mInfo_array = $color;
      $mInfo = new objectInfo($mInfo_array);
    }

    if ( (isset($mInfo) && is_object($mInfo)) && ($color['color_id'] == $mInfo->color_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_COLOR, 'page=' . $_GET['page'] . '&mID=' . $color['color_id'] . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_COLOR, 'page=' . $_GET['page'] . '&mID=' . $color['color_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $color['color_name']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (isset($mInfo) && is_object($mInfo)) && ($color['color_id'] == $mInfo->color_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_COLOR, 'page=' . $_GET['page'] . '&mID=' . $color['color_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $color_split->display_count($color_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COLORS); ?></td>
                    <td class="smallText" align="right"><?php echo $color_split->display_links($color_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                </table></td>
              </tr>
<?php
  if (!isset($_GET['action']) || $_GET['action'] != 'new') {
?>
              <tr>
                <td align="right" colspan="2" class="smallText"><?php echo '<a href="' . tep_href_link(FILENAME_COLOR, 'page=' . $_GET['page'] . '&mID=' . $mInfo->color_id . '&action=new') . '">' . tep_image_button('button_insert.gif', IMAGE_INSERT) . '</a>'; ?></td>
              </tr>
<?php
  }
?>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch (isset($_GET['action'])?$_GET['action']:'') {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_NEW_MANUFACTURER . '</b>');

      $contents = array('form' => tep_draw_form('color', FILENAME_COLOR, 'action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_NEW_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_COLORS_NAME . '<br>' . tep_draw_input_field('color_name'));
	  $contents[] = array('text' => '<br>' . TEXT_COLORS_TAG . '<br>' . tep_draw_input_field('color_tag'));
	  $contents[] = array('text' => '<br>ソート順<br>' . tep_draw_input_field('sort_id'));

      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_COLOR, 'page=' . $_GET['page'] . '&mID=' . $_GET['mID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_EDIT_MANUFACTURER . '</b>');

      $contents = array('form' => tep_draw_form('color', FILENAME_COLOR, 'page=' . $_GET['page'] . '&mID=' . $mInfo->color_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_COLORS_NAME . '<br>' . tep_draw_input_field('color_name', $mInfo->color_name));
	  $contents[] = array('text' => '<br>' . TEXT_COLORS_TAG . '<br>' . tep_draw_input_field('color_tag', $mInfo->color_tag));
	  $contents[] = array('text' => '<br>ソート順<br>' . tep_draw_input_field('sort_id', $mInfo->sort_id));

      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_COLOR, 'page=' . $_GET['page'] . '&mID=' . $mInfo->color_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_DELETE_MANUFACTURER . '</b>');

      $contents = array('form' => tep_draw_form('color', FILENAME_COLOR, 'page=' . $_GET['page'] . '&mID=' . $mInfo->color_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $mInfo->color_name . '</b>');


      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_COLOR, 'page=' . $_GET['page'] . '&mID=' . $mInfo->color_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($mInfo)) {
        $heading[] = array('text' => '<b>' . $mInfo->color_name . '</b>');

        $contents[] = array('text' => '<br>' . TEXT_COLORS_NAME . $mInfo->color_name);
		$contents[] = array('text' => '<br>' . TEXT_COLORS_TAG2 . $mInfo->color_tag);
		$contents[] = array('text' => '<br><br>');
		$contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_COLOR, 'page=' . $_GET['page'] . '&mID=' . $mInfo->color_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_COLOR, 'page=' . $_GET['page'] . '&mID=' . $mInfo->color_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
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
