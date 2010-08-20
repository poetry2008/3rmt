<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  if (isset($_GET['action']))
  switch ($_GET['action']) {
    case 'setflag':
      tep_set_specials_status($_GET['id'], $_GET['flag']);
      tep_redirect(tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'], 'NONSSL'));
      break;
    case 'insert':
// insert a product on special
      if (substr($_POST['specials_price'], -1) == '%') {
        $new_special_insert_query = tep_db_query("select products_id, products_price from " . TABLE_PRODUCTS . " where products_id = '" . $_POST['products_id'] . "'");
        $new_special_insert = tep_db_fetch_array($new_special_insert_query);
        $_POST['products_price'] = $new_special_insert['products_price'];
        $_POST['specials_price'] = ($_POST['products_price'] - (($_POST['specials_price'] / 100) * $_POST['products_price']));
      }	

      $expires_date = '';
      if ($_POST['day'] && $_POST['month'] && $_POST['year']) {
        $expires_date = $_POST['year'];
        $expires_date .= (strlen($_POST['month']) == 1) ? '0' . $_POST['month'] : $_POST['month'];
        $expires_date .= (strlen($_POST['day']) == 1) ? '0' . $_POST['day'] : $_POST['day'];
      }

      tep_db_query("insert into " . TABLE_SPECIALS . " (products_id, specials_new_products_price, specials_date_added, expires_date, status) values ('" . $_POST['products_id'] . "', '" . $_POST['specials_price'] . "', now(), '" . $expires_date . "', '1')");
      tep_redirect(tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page']));
      break;
    case 'update':
// update a product on special
      if (substr($_POST['specials_price'], -1) == '%') $_POST['specials_price'] = ($_POST['products_price'] - (($_POST['specials_price'] / 100) * $_POST['products_price']));

      $expires_date = '';
      if ($_POST['day'] && $_POST['month'] && $_POST['year']) {
        $expires_date = $_POST['year'];
        $expires_date .= (strlen($_POST['month']) == 1) ? '0' . $_POST['month'] : $_POST['month'];
        $expires_date .= (strlen($_POST['day']) == 1) ? '0' . $_POST['day'] : $_POST['day'];
      }

      tep_db_query("update " . TABLE_SPECIALS . " set specials_new_products_price = '" . $_POST['specials_price'] . "', specials_last_modified = now(), expires_date = '" . $expires_date . "' where specials_id = '" . $_POST['specials_id'] . "'");
      tep_redirect(tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $specials_id));
      break;
    case 'deleteconfirm':
      $specials_id = tep_db_prepare_input($_GET['sID']);

      tep_db_query("delete from " . TABLE_SPECIALS . " where specials_id = '" . tep_db_input($specials_id) . "'");

      tep_redirect(tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page']));
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
<?php
  if ( isset($_GET['action']) && (($_GET['action'] == 'new') || ($_GET['action'] == 'edit')) ) {
?>
<link rel="stylesheet" type="text/css" href="includes/javascript/calendar.css">
<script language="JavaScript" src="includes/javascript/calendarcode.js"></script>
<?php
  }
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<div id="popupcalendar" class="text"></div>
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
<?php
  if ( isset($_GET['action']) && (($_GET['action'] == 'new') || ($_GET['action'] == 'edit')) ) {
    $form_action = 'insert';
    if ( ($_GET['action'] == 'edit') && ($_GET['sID']) ) {
	  $form_action = 'update';

      $product_query = tep_db_query("
          select p.products_id, 
                 pd.products_name, 
                 pd.site_id,
                 p.products_price, 
                 s.specials_new_products_price, 
                 s.expires_date 
          from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s 
          where p.products_id = pd.products_id 
            and pd.language_id = '" . $languages_id . "' 
            and p.products_id = s.products_id 
            and s.specials_id = '" . $_GET['sID'] . "'
            and pd.site_id = '0'
            ");
      $product = tep_db_fetch_array($product_query);

      $sInfo = new objectInfo($product);
    } else {
      $sInfo = new objectInfo(array());

// create an array of products on special, which will be excluded from the pull down menu of products
// (when creating a new product on special)
      $specials_array = array();
      $specials_query = tep_db_query("
          select p.products_id 
          from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s 
          where s.products_id = p.products_id");
      while ($specials = tep_db_fetch_array($specials_query)) {
        $specials_array[] = $specials['products_id'];
      }
    }
?>
      <tr><form name="new_special" <?php echo 'action="' . tep_href_link(FILENAME_SPECIALS, tep_get_all_get_params(array('action', 'info', 'sID')) . 'action=' . $form_action, 'NONSSL') . '"'; ?> method="post"><?php if ($form_action == 'update') echo tep_draw_hidden_field('specials_id', $_GET['sID']); ?>
        <td><br><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php echo TEXT_SPECIALS_PRODUCT; ?>&nbsp;</td>
            <td class="main"><?php echo (isset($sInfo->products_name) && $sInfo->products_name) ? $sInfo->products_name . ' <small>(' . $currencies->format(isset($sInfo->products_price)?$sInfo->products_price:'') . ')</small>' : tep_draw_products_pull_down('products_id', 'style="font-size:10px"', $specials_array); echo tep_draw_hidden_field('products_price', isset($sInfo->products_price) ? $sInfo->products_price : ''); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_SPECIALS_SPECIAL_PRICE; ?>&nbsp;</td>
            <td class="main"><?php echo tep_draw_input_field('specials_price', isset($sInfo->specials_new_products_price)?$sInfo->specials_new_products_price:''); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_SPECIALS_EXPIRES_DATE; ?>&nbsp;</td>
            <td class="main"><?php echo tep_draw_input_field('day', isset($sInfo->expires_date)?substr($sInfo->expires_date, 8, 2):'', 'size="2" maxlength="2" class="cal-TextBox"') . tep_draw_input_field('month', isset($sInfo->expires_date) ? substr($sInfo->expires_date, 5, 2) :'', 'size="2" maxlength="2" class="cal-TextBox"') . tep_draw_input_field('year', isset($sInfo->expires_date)?substr($sInfo->expires_date, 0, 4):'', 'size="4" maxlength="4" class="cal-TextBox"'); ?><a class="so-BtnLink" href="javascript:calClick();return false;" onmouseover="calSwapImg('BTN_date', 'img_Date_OVER',true);" onmouseout="calSwapImg('BTN_date', 'img_Date_UP',true);" onclick="calSwapImg('BTN_date', 'img_Date_DOWN');showCalendar('new_special','dteWhen','BTN_date');return false;"><?php echo tep_image(DIR_WS_IMAGES . 'cal_date_up.gif', 'Calendar', '22', '17', 'align="absmiddle" name="BTN_date"'); ?></a></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><br><?php echo TEXT_SPECIALS_PRICE_TIP; ?></td>
            <td class="main" align="right" valign="top"><br><?php echo (($form_action == 'insert') ? tep_image_submit('button_insert.gif', IMAGE_INSERT) : tep_image_submit('button_update.gif', IMAGE_UPDATE)). '&nbsp;&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . (isset($_GET['sID'])?$_GET['sID']:'')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
          </tr>
        </table></td>
      </form></tr>
<?php
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRODUCTS_PRICE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $specials_query_raw = "
      select p.products_id, 
             pd.products_name, 
             p.products_price, 
             s.specials_id, 
             s.specials_new_products_price, 
             s.specials_date_added, 
             s.specials_last_modified, 
             s.expires_date, 
             s.date_status_change, 
             s.status 
      from " . TABLE_PRODUCTS . " p, " . TABLE_SPECIALS . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd 
      where p.products_id = pd.products_id 
        and pd.language_id = '" . $languages_id . "' 
        and p.products_id = s.products_id 
        and pd.site_id = '0'
      order by pd.products_name";
    $specials_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $specials_query_raw, $specials_query_numrows);
    $specials_query = tep_db_query($specials_query_raw);
    while ($specials = tep_db_fetch_array($specials_query)) {
      if ( ((!isset($_GET['sID']) || !$_GET['sID']) || ($_GET['sID'] == $specials['specials_id'])) && (!isset($sInfo) || !$sInfo) ) {

        $products_query = tep_db_query("
            select products_image 
            from " . TABLE_PRODUCTS . " 
            where products_id = '" . $specials['products_id'] . "'");
        $products = tep_db_fetch_array($products_query);
        $sInfo_array = tep_array_merge($specials, $products);
        $sInfo = new objectInfo($sInfo_array);
      }

      if ( isset($sInfo) && (is_object($sInfo)) && ($specials['specials_id'] == $sInfo->specials_id) ) {
        echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $specials['specials_id']) . '\'">' . "\n";
      }
?>
                <td  class="dataTableContent"><?php echo $specials['products_name']; ?></td>
                <td  class="dataTableContent" align="right"><span class="oldPrice"><?php echo $currencies->format($specials['products_price']); ?></span> <span class="specialPrice"><?php echo $currencies->format($specials['specials_new_products_price']); ?></span></td>
                <td  class="dataTableContent" align="right">
<?php
      if ($specials['status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_SPECIALS, 'action=setflag&flag=0&page=' . (isset($_GET['page'])?$_GET['page']:'') . '&id=' . $specials['specials_id'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_SPECIALS, 'action=setflag&flag=1&page=' . (isset($_GET['page'])?$_GET['page']:'') . '&id=' . $specials['specials_id'], 'NONSSL') . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
                <td class="dataTableContent" align="right"><?php if ( isset($sInfo) && (is_object($sInfo)) && ($specials['specials_id'] == $sInfo->specials_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $specials['specials_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
      </tr>
<?php
    }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellpadding="0"cellspacing="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $specials_split->display_count($specials_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_SPECIALS); ?></td>
                    <td class="smallText" align="right"><?php echo $specials_split->display_links($specials_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!isset($_GET['action']) || !$_GET['action']) {
?>
                  <tr> 
                    <td colspan="2" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&action=new') . '">' . tep_image_button('button_new_product.gif', IMAGE_NEW_PRODUCT) . '</a>'; ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch (isset($_GET['action']) ? $_GET['action'] : '') {
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_SPECIALS . '</b>');

      $contents = array('form' => tep_draw_form('specials', FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $sInfo->products_name . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . '&nbsp;<a href="' . tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($sInfo)) {
        $heading[] = array('text' => '<b>' . $sInfo->products_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_SPECIALS, 'page=' . $_GET['page'] . '&sID=' . $sInfo->specials_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_ADDED . ' ' . tep_date_short($sInfo->specials_date_added));
        $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . tep_date_short($sInfo->specials_last_modified));
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_info_image('products/'.$sInfo->products_image, $sInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 0));
        $contents[] = array('text' => '<br>' . TEXT_INFO_ORIGINAL_PRICE . ' ' . $currencies->format($sInfo->products_price));
        $contents[] = array('text' => '' . TEXT_INFO_NEW_PRICE . ' ' . $currencies->format($sInfo->specials_new_products_price));
        $contents[] = array('text' => '' . TEXT_INFO_PERCENTAGE . ' ' . number_format(100 - (($sInfo->specials_new_products_price / $sInfo->products_price) * 100)) . '%');

        $contents[] = array('text' => '<br>' . TEXT_INFO_EXPIRES_DATE . ' <b>' . tep_date_short($sInfo->expires_date) . '</b>');
        $contents[] = array('text' => '' . TEXT_INFO_STATUS_CHANGE . ' ' . tep_date_short($sInfo->date_status_change));
      }
      break;
  }
  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
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
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
