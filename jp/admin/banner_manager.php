<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  $banner_extension = tep_banner_image_extension();

  if (isset($_GET['action']) && $_GET['action']) {
 if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];//权限判断
         else $site_arr="";
    switch ($_GET['action']) {
      case 'setflag':
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          tep_set_banner_status($_GET['bID'], $_GET['flag']);
          $messageStack->add_session(SUCCESS_BANNER_STATUS_UPDATED, 'success');
        } else {
          $messageStack->add_session(ERROR_UNKNOWN_STATUS_FLAG, 'error');
        }

        tep_redirect(tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $_GET['bID'] . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')));
        break;
      case 'insert':
        //echo "<pre>";
        //print_r($_POST);
        //exit;
 forward401Unless(editPermission($site_arr, $lsite_id));
        $site_id              = tep_db_prepare_input($_POST['site_id']);
        if (empty($site_id)) {
          $messageStack->add(SITE_ID_NOT_NULL, 'error');
          $banner_error = true;
        }
      case 'update':
 forward401Unless(editPermission($site_arr, $lsite_id));
        $banners_id           = tep_db_prepare_input($_POST['banners_id']);
        $banners_title        = tep_db_prepare_input($_POST['banners_title']);
        $banners_url          = tep_db_prepare_input($_POST['banners_url']);
        $new_banners_group    = tep_db_prepare_input($_POST['new_banners_group']);
        $banners_group        = (empty($new_banners_group)) ? tep_db_prepare_input($_POST['banners_group']) : $new_banners_group;
        $html_text            = tep_db_prepare_input($_POST['html_text']);
        $banners_image        = tep_get_uploaded_file('banners_image');
        $banners_image_local  = tep_db_prepare_input($_POST['banners_image_local']);
        $banners_image_target = tep_db_prepare_input($_POST['banners_image_target']);
        $db_image_location    = '';

        //$image_directory      = tep_get_local_path(DIR_FS_CATALOG_IMAGES . $banners_image_target);
        $banners = tep_get_banner($banners_id);
        $image_directory      = tep_get_local_path(tep_get_upload_dir(isset($banners['site_id']) ? $banners['site_id']: $site_id ) . $banners_image_target);

        $banner_error = false;
        if (empty($banners_title)) {
          $messageStack->add(ERROR_BANNER_TITLE_REQUIRED, 'error');
          $banner_error = true;
        }
        if (empty($banners_group)) {
          $messageStack->add(ERROR_BANNER_GROUP_REQUIRED, 'error');
          $banner_error = true;
        }
        if ( (isset($banners_image)) && ($banners_image['name'] != 'none') && (is_uploaded_file($banners_image['tmp_name'])) ) {
          $store_image = false;
          if (!is_writeable($image_directory)) {
            if (is_dir($image_directory)) {
              $messageStack->add(sprintf(ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE, $image_directory), 'error');
            } else {
              $messageStack->add(sprintf(ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST, $image_directory), 'error');
            }
            $banner_error = true;
          } else {
            $store_image = true;
          }
        }

        if (!$banner_error) {
          if ( (empty($html_text)) && ($store_image == true) ) {
            tep_copy_uploaded_file($banners_image, $image_directory);
          }
          $db_image_location = (tep_not_null($banners_image_local)) ? $banners_image_local : $banners_image_target . $banners_image['name'];
          $sql_data_array = array('banners_title'     => $banners_title,
                                  'banners_url'       => $banners_url,
                                  'banners_image'     => $db_image_location,
                                  'banners_group'     => $banners_group,
                                  'banners_html_text' => $html_text);

          if ($_GET['action'] == 'insert') {
            $insert_sql_data = array('date_added' => 'now()',
                                      'status' => '1',
                                      'site_id' => $site_id
                                     );
            $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
            tep_db_perform(TABLE_BANNERS, $sql_data_array);
            $banners_id = tep_db_insert_id();
            $messageStack->add_session(SUCCESS_BANNER_INSERTED, 'success');
          } elseif ($_GET['action'] == 'update') {
            tep_db_perform(TABLE_BANNERS, $sql_data_array, 'update', 'banners_id = \'' . $banners_id . '\'');
            $messageStack->add_session(SUCCESS_BANNER_UPDATED, 'success');
          }

          if (isset($_POST['expires_date']) && $_POST['expires_date']) {
            $expires_date = tep_db_prepare_input($_POST['expires_date']);
            list($day, $month, $year) = explode('/', $expires_date);

            $expires_date = $year .
                            ((strlen($month) == 1) ? '0' . $month : $month) .
                            ((strlen($day) == 1) ? '0' . $day : $day);

            tep_db_query("
                update " . TABLE_BANNERS . " 
                set expires_date = '" . tep_db_input($expires_date) . "', 
                    expires_impressions = null 
                where banners_id = '" . $banners_id . "'
            ");
          } elseif (isset($_POST['impressions']) && $_POST['impressions']) {
            $impressions = tep_db_prepare_input($_POST['impressions']);
            tep_db_query("
                update " . TABLE_BANNERS . " 
                set expires_impressions = '" . tep_db_input($impressions) . "', 
                    expires_date = null 
                where banners_id = '" . $banners_id . "'
            ");
          }

          if ($_POST['date_scheduled']) {
            $date_scheduled = tep_db_prepare_input($_POST['date_scheduled']);
            list($day, $month, $year) = explode('/', $date_scheduled);

            $date_scheduled = $year .
                              ((strlen($month) == 1) ? '0' . $month : $month) .
                              ((strlen($day) == 1) ? '0' . $day : $day);

            tep_db_query("
                update " . TABLE_BANNERS . " 
                set status = '0', 
                    date_scheduled = '" . tep_db_input($date_scheduled) . "' 
                where banners_id = '" . $banners_id . "'");
          }

          tep_redirect(tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners_id . (isset($_GET['lsite_id'])?('&site_id='.$_GET['lsite_id']):'')));
        } else {
          $_GET['action'] = 'new';
        }
        break;
      case 'deleteconfirm':
        $banners_id   = tep_db_prepare_input($_GET['bID']);
        $delete_image = tep_db_prepare_input($_POST['delete_image']);

        if ($delete_image == 'on') {
          $banner_query = tep_db_query("
              select *
              from " . TABLE_BANNERS . " 
              where banners_id = '" . tep_db_input($banners_id) . "'
          ");
          $banner = tep_db_fetch_array($banner_query);
          //if (is_file(DIR_FS_CATALOG_IMAGES . $banner['banners_image'])) {
            //if (is_writeable(DIR_FS_CATALOG_IMAGES . $banner['banners_image'])) {
              //unlink(DIR_FS_CATALOG_IMAGES . $banner['banners_image']);
          if (is_file(tep_get_upload_dir($banner['site_id']). $banner['banners_image'])) {
            if (is_writeable(DIR_FS_CATALOG_IMAGES . $banner['banners_image'])) {
              unlink(DIR_FS_CATALOG_IMAGES . $banner['banners_image']);
            } else {
              $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
            }
          } else {
            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
          }
        }

        tep_db_query("delete from " . TABLE_BANNERS . " where banners_id = '" . tep_db_input($banners_id) . "'");
        tep_db_query("delete from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . tep_db_input($banners_id) . "'");

        if ( (function_exists('imagecreate')) && ($banner_extension) ) {
          if (is_file(DIR_WS_IMAGES . 'graphs/banner_infobox-' . $banners_id . '.' . $banner_extension)) {
            if (is_writeable(DIR_WS_IMAGES . 'graphs/banner_infobox-' . $banners_id . '.' . $banner_extension)) {
              unlink(DIR_WS_IMAGES . 'graphs/banner_infobox-' . $banners_id . '.' . $banner_extension);
            }
          }

          if (is_file(DIR_WS_IMAGES . 'graphs/banner_yearly-' . $banners_id . '.' . $banner_extension)) {
            if (is_writeable(DIR_WS_IMAGES . 'graphs/banner_yearly-' . $banners_id . '.' . $banner_extension)) {
              unlink(DIR_WS_IMAGES . 'graphs/banner_yearly-' . $banners_id . '.' . $banner_extension);
            }
          }

          if (is_file(DIR_WS_IMAGES . 'graphs/banner_monthly-' . $banners_id . '.' . $banner_extension)) {
            if (is_writeable(DIR_WS_IMAGES . 'graphs/banner_monthly-' . $banners_id . '.' . $banner_extension)) {
              unlink(DIR_WS_IMAGES . 'graphs/banner_monthly-' . $banners_id . '.' . $banner_extension);
            }
          }

          if (is_file(DIR_WS_IMAGES . 'graphs/banner_daily-' . $banners_id . '.' . $banner_extension)) {
            if (is_writeable(DIR_WS_IMAGES . 'graphs/banner_daily-' . $banners_id . '.' . $banner_extension)) {
              unlink(DIR_WS_IMAGES . 'graphs/banner_daily-' . $banners_id . '.' . $banner_extension);
            }
          }
        }

        $messageStack->add_session(SUCCESS_BANNER_REMOVED, 'success');

        tep_redirect(tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')));
        break;
    }
  }

// check if the graphs directory exists
  $dir_ok = false;
  if ( (function_exists('imagecreate')) && ($banner_extension) ) {
    if (is_dir(DIR_WS_IMAGES . 'graphs')) {
      if (is_writeable(DIR_WS_IMAGES . 'graphs')) {
        $dir_ok = true;
      } else {
        $messageStack->add(ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE, 'error');
      }
    } else {
      $messageStack->add(ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST, 'error');
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript"><!--
function popupImageWindow(url) {
  window.open(url,'popupImageWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
  <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
<?php
  if (isset($_GET['action']) && $_GET['action'] == 'new') {
    $form_action = 'insert';
    if (isset($_GET['bID']) && $_GET['bID']) {
      $bID = tep_db_prepare_input($_GET['bID']);
      $form_action = 'update';

      $banner_query = tep_db_query("
          select b.banners_title, 
                 b.banners_url, 
                 b.banners_image, 
                 b.banners_group, 
                 b.banners_html_text, 
                 b.status, 
                 date_format(b.date_scheduled, '%d/%m/%Y') as date_scheduled, 
                 date_format(b.expires_date, '%d/%m/%Y') as expires_date, 
                 b.expires_impressions, 
                 b.date_status_change,
                 b.site_id,
                 s.romaji,
                 s.name as site_name
          from " . TABLE_BANNERS . " b, ".TABLE_SITES." s
          where banners_id = '" . tep_db_input($bID) . "'
            and s.id = b.site_id
          ");
      $banner = tep_db_fetch_array($banner_query);

      $bInfo = new objectInfo($banner);
    } elseif ($_POST) {
      $bInfo = new objectInfo($_POST);
    } else {
      $bInfo = new objectInfo(array());
    }

    $groups_array = array();
    $groups_query = tep_db_query("
        select distinct banners_group 
        from " . TABLE_BANNERS . " 
        order by banners_group");
    while ($groups = tep_db_fetch_array($groups_query)) {
      $groups_array[] = array('id' => $groups['banners_group'], 'text' => $groups['banners_group']);
    }
?>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript">
  var dateExpires = new ctlSpiffyCalendarBox("dateExpires", "new_banner", "expires_date","btnDate1","<?php echo isset($bInfo->expires_date)?$bInfo->expires_date:''; ?>",scBTNMODE_CUSTOMBLUE);
  var dateScheduled = new ctlSpiffyCalendarBox("dateScheduled", "new_banner", "date_scheduled","btnDate2","<?php echo isset($bInfo->date_scheduled)?$bInfo->date_scheduled:''; ?>",scBTNMODE_CUSTOMBLUE);
</script>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr><?php echo tep_draw_form('new_banner', FILENAME_BANNER_MANAGER, 'page=' . (isset($_GET['page'])?$_GET['page']:'') . '&action=' . $form_action . (isset($_GET['lsite_id'])?('&lsite_id='.$_GET['lsite_id']):''), 'post', 'enctype="multipart/form-data"'); if ($form_action == 'update') echo tep_draw_hidden_field('banners_id', $bID); ?>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php echo ENTRY_SITE; ?></td>
            <td class="main"><?php echo (isset($_GET['bID']) && $_GET['bID'])?$banner['site_name']:tep_site_pull_down_menu(); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANNERS_TITLE; ?></td>
            <td class="main"><?php echo tep_draw_input_field('banners_title', isset($bInfo->banners_title)?$bInfo->banners_title:'', '', true); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANNERS_URL; ?></td>
            <td class="main"><?php echo tep_draw_input_field('banners_url', isset($bInfo->banners_url)?$bInfo->banners_url:''); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php echo TEXT_BANNERS_GROUP; ?></td>
            <td class="main"><?php echo tep_draw_pull_down_menu('banners_group', $groups_array, isset($bInfo->banners_group)?$bInfo->banners_group:'') . TEXT_BANNERS_NEW_GROUP . '<br>' . tep_draw_input_field('new_banners_group', '', '', ((sizeof($groups_array) > 0) ? false : true)); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php echo TEXT_BANNERS_IMAGE; ?></td>
            <td class="main"><?php echo tep_draw_file_field('banners_image') . ' ' . TEXT_BANNERS_IMAGE_LOCAL . '<br>' . (tep_get_upload_root().'x/') . tep_draw_input_field('banners_image_local', isset($bInfo->banners_image)?$bInfo->banners_image:''); ?><br>
<?php if(isset($bInfo->banners_image) && $bInfo->banners_image) echo tep_info_image($bInfo->banners_image, $bInfo->banners_title, '', '', $bInfo->site_id) ; ?>
<br>
</td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANNERS_IMAGE_TARGET; ?></td>
            <td class="main"><?php echo (tep_get_upload_root().'x/') . tep_draw_input_field('banners_image_target'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_BANNERS_HTML_TEXT; ?></td>
            <td class="main"><?php echo tep_draw_textarea_field('html_text', 'soft', '60', '5', isset($bInfo->banners_html_text)?$bInfo->banners_html_text:''); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_BANNERS_SCHEDULED_AT; ?><br><small>(dd/mm/yyyy)</small></td>
            <td valign="top" class="main"><script language="javascript">dateScheduled.writeControl(); dateScheduled.dateFormat="dd/MM/yyyy";</script></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td valign="top" class="main"><?php echo TEXT_BANNERS_EXPIRES_ON; ?><br><small>(dd/mm/yyyy)</small></td>
            <td class="main"><script language="javascript">dateExpires.writeControl(); dateExpires.dateFormat="dd/MM/yyyy";</script><?php echo TEXT_BANNERS_OR_AT . '<br>' . tep_draw_input_field('impressions', isset($bInfo->expires_impressions)?$bInfo->expires_impressions:'', 'maxlength="7" size="7"') . ' ' . TEXT_BANNERS_IMPRESSIONS; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php echo TEXT_BANNERS_BANNER_NOTE . '<br>' . TEXT_BANNERS_INSERT_NOTE . '<br>' . TEXT_BANNERS_EXPIRCY_NOTE . '<br>' . TEXT_BANNERS_SCHEDULE_NOTE; ?></td>
            <td class="main" align="right" valign="top" nowrap><?php echo (($form_action == 'insert') ? tep_image_submit('button_insert.gif', IMAGE_INSERT) : tep_image_submit('button_update.gif', IMAGE_UPDATE)). '&nbsp;&nbsp;<a class="new_product_reset" href="' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' .(isset($_GET['page'])?$_GET['page']:'') . '&bID=' . (isset($_GET['bID'])?$_GET['bID']:'') . (isset($_GET['lsite_id'])?('&site_id='.$_GET['lsite_id']):'')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td>
          </tr>
        </table></td>
      </form></tr>
<?php
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            <?php tep_site_filter(FILENAME_BANNER_MANAGER);?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SITE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_BANNERS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_GROUPS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATISTICS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $banners_query_raw = "
      select b.banners_id, 
             b.banners_title, 
             b.banners_image, 
             b.banners_group, 
             b.status, 
             b.expires_date, 
             b.expires_impressions, 
             b.date_status_change, 
             b.date_scheduled, 
             b.date_added,
             s.romaji,
             s.name as site_name
      from " . TABLE_BANNERS . " b, ".TABLE_SITES." s
      where s.id = b.site_id
        " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and s.id = '" . intval($_GET['site_id']) . "' " : '') . "
      order by b.banners_group";
    $banners_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $banners_query_raw, $banners_query_numrows);
    $banners_query = tep_db_query($banners_query_raw);
    while ($banners = tep_db_fetch_array($banners_query)) {
      $info_query = tep_db_query("select sum(banners_shown) as banners_shown, sum(banners_clicked) as banners_clicked from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $banners['banners_id'] . "'");
      $info = tep_db_fetch_array($info_query);

      if (((!isset($_GET['bID']) || !$_GET['bID']) || ($_GET['bID'] == $banners['banners_id'])) && (!isset($bInfo) || !$bInfo) && (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new')) {
        $bInfo_array = tep_array_merge($banners, $info);
        $bInfo = new objectInfo($bInfo_array);
      }

      $banners_shown = ($info['banners_shown'] != '') ? $info['banners_shown'] : '0';
      $banners_clicked = ($info['banners_clicked'] != '') ? $info['banners_clicked'] : '0';

      if ( (isset($bInfo) && is_object($bInfo)) && ($banners['banners_id'] == $bInfo->banners_id) ) {
        echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_BANNER_STATISTICS, 'page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['banners_id'] . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo $banners['romaji'];?></td>
                <td class="dataTableContent"><?php echo '<a href="javascript:popupImageWindow(\'' . FILENAME_POPUP_IMAGE . '?banner=' . $banners['banners_id'] . '\')">' . tep_image(DIR_WS_IMAGES . 'icon_popup.gif', 'View Banner') . '</a>&nbsp;' . $banners['banners_title']; ?></td>
                <td class="dataTableContent" align="right"><?php echo $banners['banners_group']; ?></td>
                <td class="dataTableContent" align="right"><?php echo $banners_shown . ' / ' . $banners_clicked; ?></td>
                <td class="dataTableContent" align="right">
<?php
      if ($banners['status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', 'Active', 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['banners_id'] . '&action=setflag&flag=0' . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', 'Set Inactive', 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['banners_id'] . '&action=setflag&flag=1' . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', 'Set Active', 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', 'Inactive', 10, 10);
      }
?></td>
                <td class="dataTableContent" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_BANNER_STATISTICS, 'page=' . $_GET['page'] . '&bID=' . $banners['banners_id'] . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image(DIR_WS_ICONS . 'statistics.gif', ICON_STATISTICS) . '</a>&nbsp;'; if ( (isset($bInfo) && is_object($bInfo)) && ($banners['banners_id'] == $bInfo->banners_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['banners_id'] . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $banners_split->display_count($banners_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BANNERS); ?></td>
                    <td class="smallText" align="right"><?php echo $banners_split->display_links($banners_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'bID'))); ?></td>
                  </tr>
                  <tr>
                    <td align="right" colspan="2"><?php echo '<a href="' . tep_href_link(FILENAME_BANNER_MANAGER, 'action=new' . (isset($_GET['site_id'])?('&lsite_id='.$_GET['site_id']):'')) . '">' . tep_image_button('button_new_banner.gif', IMAGE_NEW_BANNER) . '</a>'; ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch (isset($_GET['action'])?$_GET['action']:null) {
    case 'delete':
      $heading[] = array('text' => '<b>' . $bInfo->banners_title . '</b>');

      $contents = array('form' => tep_draw_form('banners', FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id . '&action=deleteconfirm' . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $bInfo->banners_title . '</b>');
      if ($bInfo->banners_image) $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('delete_image', 'on', true) . ' ' . TEXT_INFO_DELETE_IMAGE);
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . '&nbsp;<a href="' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $_GET['bID'] . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($bInfo)) {
        $heading[] = array('text' => '<b>' . $bInfo->banners_title . '</b>');

        $contents[] = array('align' => 'center', 'text' => 
          '<a href="' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id . '&action=new' . (isset($_GET['site_id'])?('&lsite_id='.$_GET['site_id']):'')) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>' 
        . ($ocertify->npermission == 15 ? (' <a href="' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id . '&action=delete' . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>'):'')
        );
        $contents[] = array('text' => '<br>' . TEXT_BANNERS_DATE_ADDED . ' ' . tep_date_short($bInfo->date_added));

        if ( (function_exists('imagecreate')) && ($dir_ok) && ($banner_extension) ) {
          $banner_id = $bInfo->banners_id;
          $days = '3';
          include(DIR_WS_INCLUDES . 'graphs/banner_infobox.php');
          $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image(DIR_WS_IMAGES . 'graphs/banner_infobox-' . $banner_id . '.' . $banner_extension));
        } else {
          include(DIR_WS_FUNCTIONS . 'html_graphs.php');
          $contents[] = array('align' => 'center', 'text' => '<br>' . tep_banner_graph_infoBox($bInfo->banners_id, '3'));
        }

        $contents[] = array('text' => tep_image(DIR_WS_IMAGES . 'graph_hbar_blue.gif', 'Blue', '5', '5') . ' ' . TEXT_BANNERS_BANNER_VIEWS . '<br>' . tep_image(DIR_WS_IMAGES . 'graph_hbar_red.gif', 'Red', '5', '5') . ' ' . TEXT_BANNERS_BANNER_CLICKS);

        if ($bInfo->date_scheduled) $contents[] = array('text' => '<br>' . sprintf(TEXT_BANNERS_SCHEDULED_AT_DATE, tep_date_short($bInfo->date_scheduled)));

        if ($bInfo->expires_date) {
          $contents[] = array('text' => '<br>' . sprintf(TEXT_BANNERS_EXPIRES_AT_DATE, tep_date_short($bInfo->expires_date)));
        } elseif ($bInfo->expires_impressions) {
          $contents[] = array('text' => '<br>' . sprintf(TEXT_BANNERS_EXPIRES_AT_IMPRESSIONS, $bInfo->expires_impressions));
        }

        if ($bInfo->date_status_change) $contents[] = array('text' => '<br>' . sprintf(TEXT_BANNERS_STATUS_CHANGE, tep_date_short($bInfo->date_status_change)));
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
<?php
  }
?>
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
