<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  if (isset($_GET['action'])) 
  switch ($_GET['action']) {
    case 'insert':
    case 'save':
      $orders_status_id = tep_db_prepare_input($_GET['oID']);
      $site_id          = isset($_POST['site_id']) ? $_POST['site_id'] : 0 ;

      $languages = tep_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $orders_status_name_array = $_POST['orders_status_name'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = array(
            'orders_status_name' => tep_db_prepare_input($orders_status_name_array[$language_id]),
            'finished' => tep_db_prepare_input((int)$_POST['finished']),
            );

        if ($_GET['action'] == 'insert') {
          if (!tep_not_null($orders_status_id)) {
            $next_id_query = tep_db_query("select max(orders_status_id) as orders_status_id from " . TABLE_ORDERS_STATUS . "");
            $next_id = tep_db_fetch_array($next_id_query);
            $orders_status_id = $next_id['orders_status_id'] + 1;
          }

          $insert_sql_data = array('orders_status_id' => $orders_status_id,
                                   'language_id' => $language_id
                                   );
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_ORDERS_STATUS, $sql_data_array);
        } elseif ($_GET['action'] == 'save') {
          tep_db_perform(TABLE_ORDERS_STATUS, $sql_data_array, 'update', "orders_status_id = '" . tep_db_input($orders_status_id) . "' and language_id = '" . $language_id . "'");
        }
        //orders_status_image upload => UPDATE
        $orders_status_image = tep_get_uploaded_file('orders_status_image');
        //$image_directory = tep_get_local_path(DIR_FS_CATALOG_IMAGES);
        $image_directory = tep_get_local_path(tep_get_upload_dir().'orders_status/');
        if (is_uploaded_file($orders_status_image['tmp_name'])) {
          tep_db_query("update " . TABLE_ORDERS_STATUS . " set orders_status_image = '" . $orders_status_image['name'] . "' where orders_status_id = '" . tep_db_input($orders_status_id) . "'");
          tep_copy_uploaded_file($orders_status_image, $image_directory);
        }
        if(isset($_POST['delete_image']) && $_POST['delete_image']){
          tep_db_query("update " . TABLE_ORDERS_STATUS . " set orders_status_image = '' where orders_status_id = '" . tep_db_input($orders_status_id) . "'");
          //unlink();
        }
      }
    
    //mail本文 add
    if ($_GET['action'] == 'insert') {
      $sql_os_array = array('orders_status_id' => $orders_status_id,
                            'language_id' => $languages_id,
                            'orders_status_title' => tep_db_prepare_input($os_title),
                            'orders_status_mail' => tep_db_prepare_input($os_mail),
                            'site_id' => $site_id
                            );
        tep_db_perform(TABLE_ORDERS_MAIL, $sql_os_array);
    
    } elseif ($_GET['action'] == 'save') {    
    
    $om_check_query = tep_db_query("
        select count(*) 
        from ".TABLE_ORDERS_MAIL." 
        where orders_status_id = " . tep_db_input($orders_status_id) . " 
          and site_id = '".$site_id."'
          and language_id = " . $languages_id);
    $om_check_result = tep_db_fetch_array($om_check_query);
    $om_count = $om_check_result['count(*)'];
    
    if($om_count == 0){
      $sql_os_array = array('orders_status_id' => $orders_status_id,
                            'language_id' => $languages_id,
                            'orders_status_title' => tep_db_prepare_input($os_title),
                            'orders_status_mail' => tep_db_prepare_input($os_mail), 
                            'site_id' => $site_id
                            );
          tep_db_perform(TABLE_ORDERS_MAIL, $sql_os_array);
    }else{
      $sql_os_array = array('orders_status_mail' => tep_db_prepare_input($os_mail),
                            'orders_status_title' => tep_db_prepare_input($os_title));
      tep_db_perform(TABLE_ORDERS_MAIL, $sql_os_array, 'update', "orders_status_id = '" . tep_db_input($orders_status_id) . "' and language_id = '" . $languages_id . "' and site_id = '".$site_id."'");
      }
    
    }
    //mail本文 add end

      if ($_POST['default'] == 'on') {
        tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '" . tep_db_input($orders_status_id) . "' where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
      }

      tep_redirect(tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status_id));
      break;
    case 'deleteconfirm':
      $oID = tep_db_prepare_input($_GET['oID']);

      $orders_status_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
      $orders_status = tep_db_fetch_array($orders_status_query);
      if ($orders_status['configuration_value'] == $oID) {
        tep_db_query("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_ORDERS_STATUS_ID'");
      }

      tep_db_query("delete from " . TABLE_ORDERS_STATUS . " where orders_status_id = '" . tep_db_input($oID) . "'");

      tep_redirect(tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page']));
      break;
    case 'delete':
      $oID = tep_db_prepare_input($_GET['oID']);

      $status_query = tep_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '" . tep_db_input($oID) . "'");
      $status = tep_db_fetch_array($status_query);

      $remove_status = true;
      if ($oID == DEFAULT_ORDERS_STATUS_ID) {
        $remove_status = false;
        $messageStack->add(ERROR_REMOVE_DEFAULT_ORDER_STATUS, 'error');
      } elseif ($status['count'] > 0) {
        $remove_status = false;
        $messageStack->add(ERROR_STATUS_USED_IN_ORDERS, 'error');
      } else {
        $history_query = tep_db_query("select count(*) as count from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_status_id = '" . tep_db_input($oID) . "'");
        $history = tep_db_fetch_array($history_query);
        if ($history['count'] > 0) {
          $remove_status = false;
          $messageStack->add(ERROR_STATUS_USED_IN_HISTORY, 'error');
        }
      }
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
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
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
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_ORDERS_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $orders_status_query_raw = "
    select *
    from " . TABLE_ORDERS_STATUS . " 
    where language_id = '" . $languages_id . "' 
    order by orders_status_id";
  $orders_status_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $orders_status_query_raw, $orders_status_query_numrows);
  $orders_status_query = tep_db_query($orders_status_query_raw);
  while ($orders_status = tep_db_fetch_array($orders_status_query)) {
    if (((!isset($_GET['oID']) || !$_GET['oID']) || ($_GET['oID'] == $orders_status['orders_status_id'])) && (!isset($oInfo) || !$oInfo) && (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new')) {
      $oInfo = new objectInfo($orders_status);
    }

    if ( isset($oInfo) && (is_object($oInfo)) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '\'">' . "\n";
    }

    if (DEFAULT_ORDERS_STATUS_ID == $orders_status['orders_status_id']) {
      echo '                <td class="dataTableContent"><b>' . $orders_status['orders_status_name'] . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
    } else {
      echo '                <td class="dataTableContent">' . $orders_status['orders_status_name'] . '</td>' . "\n";
    }
?>
                <td class="dataTableContent" align="right"><?php if ( isset($oInfo) && (is_object($oInfo)) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $orders_status_split->display_count($orders_status_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS); ?></td>
                    <td class="smallText" align="right"><?php echo $orders_status_split->display_links($orders_status_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new') {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&action=new') . '">' . tep_image_button('button_insert.gif', IMAGE_INSERT) . '</a>'; ?></td>
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
  $explanation = '名前：${NAME}<br>メールアドレス：${MAIL}<br>注文日：${ORDER_D}<br>注文番号：${ORDER_N}<br>支払い方法：${PAY}<br>注文金額：${ORDER_M}<br>取引方法：${TRADING}<br>注文ステータス：${ORDER_S}<br>自社キャラ名：${ORDER_A}';
  switch (isset($_GET['action'])?$_GET['action']:null) {
    case 'new':
      $site_id   = isset($_GET['site_id']) ? (int)$_GET['site_id']:0;
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_ORDERS_STATUS . '</b>');

      $contents = array('form' => tep_draw_form('status', FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<input type="hidden" name="site_id" value="'.$site_id.'">');

      $orders_status_inputs_string = '';
      $languages = tep_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $orders_status_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']');
      }
    
    //mailタイトル add
    $orders_status_inputs_string .= '<br><br>' . TEXT_INFO_ORDERS_STATUS_TITLE . '<br>' . tep_draw_input_field('os_title');
    //mailタイトル add end
    
    //mail本文 add
    $orders_status_inputs_string .= '<br><br>' . TEXT_INFO_ORDERS_STATUS_MAIL . '<br>' . tep_draw_textarea_field('os_mail', 'soft', '25', '5').'<br>'.$explanation ;
    //mail本文 add end

      $contents[] = array('text' => '<br>' . TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string);
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      
      $contents[] = array('text' => '<br>' . TEXT_EDIT_ORDERS_STATUS_IMAGE . '<br>' . tep_draw_file_field('orders_status_image'));
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('finished', '1') . ' ' . TEXT_ORDERS_STATUS_FINISHED);

      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT) . ' <a href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_ORDERS_STATUS . '</b>');

      $contents = array('form' => tep_draw_form('status', FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);

      $orders_status_inputs_string = '';
      $languages = tep_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $orders_status_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']', tep_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']));
      }
    
    $site_id = isset($_GET['site_id']) ? (int) $_GET['site_id']: 0;
    $os_query = tep_db_query("
        select * 
        from ".TABLE_ORDERS_MAIL." 
        where orders_status_id = '".$oID."' 
          and language_id = '".$languages_id."'
          and site_id = '".$site_id."'
    ");
    $os_result = tep_db_fetch_array($os_query);
    $os_mail = $os_result['orders_status_mail'];
    $os_title = $os_result['orders_status_title'];
    $contents[] = array('text' => '<input type="hidden" name="site_id" value="'.($os_result?$os_result['site_id']:$site_id).'">');
    
    //mailタイトル add
    $orders_status_inputs_string .= '<br><br>' . TEXT_INFO_ORDERS_STATUS_TITLE . '<br>' . tep_draw_input_field('os_title', $os_title);
    //mailタイトル add end

    //mail本文 add
    $orders_status_inputs_string .= '<br><br>' . TEXT_INFO_ORDERS_STATUS_MAIL . '<br>' . tep_draw_textarea_field('os_mail', 'soft', '25', '5', $os_mail) . '<br>' . $explanation;
    //mail本文 add end

      $contents[] = array('text' => '<br>' . TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string);
      if (DEFAULT_ORDERS_STATUS_ID != $oInfo->orders_status_id) $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      
      //if(!is_dir(tep_get_local_path(DIR_FS_CATALOG_IMAGES).DIRECTORY_SEPARATOR.$oInfo->orders_status_image) && file_exists(tep_get_local_path(DIR_FS_CATALOG_IMAGES).DIRECTORY_SEPARATOR.$oInfo->orders_status_image)) {
      if(!is_dir(tep_get_upload_dir().'orders_status/'.$oInfo->orders_status_image) && file_exists(tep_get_upload_dir().'orders_status/'.$oInfo->orders_status_image)) {
        $contents[] = array('text' => '<br>' . tep_image(tep_get_web_upload_dir() .'orders_status/'. $oInfo->orders_status_image, $oInfo->orders_status_name, 15, 15));
        $contents[] = array('text' => '<br><input type="checkbox" name="delete_image" value="1" >アイコンを削除');
      }
      $contents[] = array('text' => '<br>' . TEXT_EDIT_ORDERS_STATUS_IMAGE . '<br>' . tep_draw_file_field('orders_status_image'));
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('finished', '1', $oInfo->finished) . ' ' . TEXT_ORDERS_STATUS_FINISHED);
      
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_update.gif', IMAGE_UPDATE) . ' <a href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_ORDERS_STATUS . '</b>');

      $contents = array('form' => tep_draw_form('status', FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $oInfo->orders_status_name . '</b>');
      if ($remove_status) $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
  if (isset($oInfo) and is_object($oInfo)) {
        $heading[] = array('text' => '<b>' . $oInfo->orders_status_name . '</b>');

        $contents[] = array('align' => 'ledt', 'text' => 
          '<a href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>' 
        . ($ocertify->npermission == 15 ? (' <a href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=delete') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>'):'')
        );

        foreach(tep_get_sites() as $s){
          $contents[] = array('text' => '<b>' . $s['romaji'] . '</b>');
          $contents[] = array('align' => 'ledt', 'text' => '<a href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit&site_id=' . $s['id']) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>');
        }
        $orders_status_inputs_string = '';
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $orders_status_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']);
        }

        $contents[] = array('text' => $orders_status_inputs_string);
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
