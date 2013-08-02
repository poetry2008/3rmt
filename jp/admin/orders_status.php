<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  include(DIR_FS_ADMIN . DIR_WS_LANGUAGES .  '/default.php');
  if (isset($_GET['action'])) 
  switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'insert' 新建订单状态    
   case 'save' 更新订单状态    
   case 'deleteconfirm' 删除订单状态    
   case 'delete' 判断该状态是否被删除    
------------------------------------------------------*/
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
            'nomail' => tep_db_prepare_input((int)$_POST['nomail']),
            'calc_price' => tep_db_prepare_input((int)$_POST['calc_price']),
	    'user_update' => $_POST['user_update'],
            'date_update' => 'now()',
            'transaction_expired' => tep_db_prepare_input((int)$_POST['transaction_expired']),
            );
        switch($_POST['option_status']) {
          case '1':
            $sql_data_array['finished'] = 0; 
            $sql_data_array['is_cancle'] = 0; 
            break;
          case '2':
            $sql_data_array['finished'] = 1; 
            $sql_data_array['is_cancle'] = 0; 
            break;
          case '3':
            $sql_data_array['finished'] = 0; 
            $sql_data_array['is_cancle'] = 1; 
            break;
        }
        if ($_GET['action'] == 'insert') {
          if (!tep_not_null($orders_status_id)) {
            $next_id_query = tep_db_query("select max(orders_status_id) as orders_status_id from " . TABLE_ORDERS_STATUS . "");
            $next_id = tep_db_fetch_array($next_id_query);
            $orders_status_id = $next_id['orders_status_id'] + 1;
          }

          $insert_sql_data = array('orders_status_id' => $orders_status_id,
                                   'language_id' => $language_id,
				   'user_added' => $_POST['user_added'],
				   'date_added' => 'now()'
                                   );
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_ORDERS_STATUS, $sql_data_array);
          //同步对应的邮件模板
          tep_db_query("insert into ". TABLE_MAIL_TEMPLATES ." values(NULL,'ORDERS_STATUS_MAIL_TEMPLATES_".$orders_status_id."','0','".tep_db_prepare_input($orders_status_name_array[$language_id]).TEXT_ORDERS_STATUS_MAIL_TITLE."','".TEXT_ORDERS_STATUS_MAIL_USE_DESCRIPTION."','','','".TEXT_ORDERS_STATUS_MAIL_DESCRIPTION."','1','1','".$_POST['user_added']."',now(),'','')"); 
        } elseif ($_GET['action'] == 'save') {
          tep_db_perform(TABLE_ORDERS_STATUS, $sql_data_array, 'update', "orders_status_id = '" . tep_db_input($orders_status_id) . "' and language_id = '" . $language_id . "'");
        }
        //orders_status_image upload => UPDATE
        $orders_status_image = tep_get_uploaded_file('orders_status_image');
        $image_directory = tep_get_local_path(tep_get_upload_dir().'orders_status/');
        if (is_uploaded_file($orders_status_image['tmp_name'])) {
          tep_db_query("update " . TABLE_ORDERS_STATUS . " set orders_status_image = '" . $orders_status_image['name'] . "' where orders_status_id = '" . tep_db_input($orders_status_id) . "'");
          tep_copy_uploaded_file($orders_status_image, $image_directory);
        }
        if(isset($_POST['delete_image']) && $_POST['delete_image']){
          tep_db_query("update " . TABLE_ORDERS_STATUS . " set orders_status_image = '' where orders_status_id = '" . tep_db_input($orders_status_id) . "'");
        }
      }
     
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
      //删除对应的邮件模板
      tep_db_query("delete from ". TABLE_MAIL_TEMPLATES ." where flag='ORDERS_STATUS_MAIL_TEMPLATES_".tep_db_input($oID)."'");

      tep_redirect(tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page']));
      break;
    case 'delete':
      $oID = tep_db_prepare_input($_GET['oID']);

      $status_query = tep_db_query("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '" . tep_db_input($oID) . "'");
      $status = tep_db_fetch_array($status_query);

      $remove_status = true;
      if ($oID == DEFAULT_ORDERS_STATUS_ID) {
        //该状态是否是默认状态 
        $remove_status = false;
        $messageStack->add(ERROR_REMOVE_DEFAULT_ORDER_STATUS, 'error');
      } elseif ($status['count'] > 0) {
        //该状态是否在订单中出现 
        $remove_status = false;
        $messageStack->add(ERROR_STATUS_USED_IN_ORDERS, 'error');
      } else {
        $history_query = tep_db_query("select count(*) as count from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_status_id = '" . tep_db_input($oID) . "'");
        $history = tep_db_fetch_array($history_query);
        if ($history['count'] > 0) {
          //该状态是否在订单状态记录里出现 
          $remove_status = false;
          $messageStack->add(ERROR_STATUS_USED_IN_HISTORY, 'error');
        }
      }
      break;
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php echo HEADING_TITLE; ?>
</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script type="text/javascript">
<?php //提交表单?>
function check_status_form() 
{
  <?php
  if ($ocertify->npermission == 31) {
  ?>
  document.forms.orders_status.submit(); 
  <?php
  } else {
  ?>
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
    async: false,
    success: function(msg) {
      var tmp_msg_arr = msg.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      if (tmp_msg_arr[0] == '0') {
        document.forms.orders_status.submit(); 
      } else {
        var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
        if (in_array(input_pwd_str, pwd_list_array)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.orders_status.action),
            async: false,
            success: function(msg_info) {
              document.forms.orders_status.submit(); 
            }
          }); 
        } else {
          alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
        }
      }
    }
  });
  <?php
  }
  ?>
}
</script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
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

    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }
    if ( isset($oInfo) && (is_object($oInfo)) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '\'">' . "\n";
    }

    if (DEFAULT_ORDERS_STATUS_ID == $orders_status['orders_status_id']) {
      echo '                <td class="dataTableContent">' . $orders_status['orders_status_name'] . ' (' . TEXT_DEFAULT . ')</td>' . "\n";
    } else {
      echo '                <td class="dataTableContent">' . $orders_status['orders_status_name'] . '</td>' . "\n";
    }
?>
                <td class="dataTableContent" align="right"><?php if ( isset($oInfo) && (is_object($oInfo)) && ($orders_status['orders_status_id'] == $oInfo->orders_status_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $orders_status['orders_status_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>

            </table>
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $orders_status_split->display_count($orders_status_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $orders_status_split->display_links($orders_status_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div></td>
                  </tr>
<?php
  if (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new') {
?>
                  <tr>
                    <td colspan="2" align="right"><div class="td_button"><?php echo '<a href="' .
                    tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] .
                        '&action=new') . '">' .
                    tep_html_element_button(IMAGE_NEW_PROJECT) . '</a>'; ?></div></td>
                  </tr>
<?php
  }
?>
                </table>
			</td>
<?php
  $heading = array();
  $contents = array();
  $explanation = TEXT_ORDERS_STATUS_DESCRIPTION;
  switch (isset($_GET['action'])?$_GET['action']:null) {
/* -----------------------------------------------------
   case 'new' 右侧新建订单状态页面    
   case 'edit' 右侧编辑订单状态页面   
   case 'delete' 右侧删除订单状态页面    
   default 右侧默认页面    
------------------------------------------------------*/
    case 'new':
      $site_id   = isset($_GET['site_id']) ? (int)$_GET['site_id']:0;
      $heading[] = array('text' => TEXT_INFO_HEADING_NEW_ORDERS_STATUS);

      $contents = array('form' => tep_draw_form('orders_status', FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<input type="hidden" name="site_id" value="'.$site_id.'">');
      $contents[] = array('text' => '<input type="hidden" name="user_added" value="'.$user_info['name'].'">');
      $contents[] = array('text' => '<input type="hidden" name="user_update" value="'.$user_info['name'].'">');

      $orders_status_inputs_string = '';
      $languages = tep_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $orders_status_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']');
      }
    
      $contents[] = array('text' => '<br>' . TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string);
      
      $contents[] = array('text' => '<br>' . TEXT_EDIT_ORDERS_STATUS_IMAGE . '&nbsp;' .  tep_draw_file_field('orders_status_image'));
      $contents[] = array('text' => '<br>' . TEXT_ORDERS_STATUS_OPTION . '<br>' .  tep_draw_radio_field('option_status', '1', true).TEXT_ORDERS_STATUS_OPTION_NORMAL.'&nbsp;'.tep_draw_radio_field('option_status', '2', false).TEXT_ORDERS_STATUS_OPTION_SUCCESS.'&nbsp;'.tep_draw_radio_field('option_status', '3', false).TEXT_ORDERS_STATUS_OPTION_FAIL);
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('nomail', '1') . ' ' . 'DON\'T SEND MAIL');
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('calc_price', '1') . ' ' . TEXT_ORDERS_STATUS_SET_PRICE_CALCULATION);
      
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('is_thzk', '1') . ' ' . TEXT_ORDERS_FETCH_CONDITION);

      //交易过期警告设置
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('transaction_expired', '1') . ' ' . TEXT_TRANSACTION_EXPIRED);

      $contents[] = array('text' => TEXT_TRANSACTION_EXPIRED_COMMENT);

      $contents[] = array('align' => 'center', 'text' => '<br><a href="javascript:void(0);">' .  tep_html_element_button(IMAGE_SAVE, 'onclick="check_status_form();"') . '</a> <a class="new_product_reset" href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page']) .  '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    case 'edit':
      $heading[] = array('text' => TEXT_INFO_HEADING_EDIT_ORDERS_STATUS);

      $contents = array('form' => tep_draw_form('orders_status', FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<input type="hidden" name="user_update" value="'.$user_info['name'].'">');

      $orders_status_inputs_string = '';
      $languages = tep_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $orders_status_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('orders_status_name[' . $languages[$i]['id'] . ']', tep_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']));
      }
    
    $site_id = isset($_GET['site_id']) ? (int) $_GET['site_id']: 0; 
    $contents[] = array('text' => '<input type="hidden" name="site_id" value="'.($os_result?$os_result['site_id']:$site_id).'">');
    
      $contents[] = array('text' => '<br>' . TEXT_INFO_ORDERS_STATUS_NAME . $orders_status_inputs_string);
      
      if(!is_dir(tep_get_upload_dir().'orders_status/'.$oInfo->orders_status_image) && file_exists(tep_get_upload_dir().'orders_status/'.$oInfo->orders_status_image)) {
        $contents[] = array('text' => '<br>' . tep_image(tep_get_web_upload_dir() .'orders_status/'. $oInfo->orders_status_image, $oInfo->orders_status_name, 15, 15));
        $contents[] = array('text' => '<br><input type="checkbox" name="delete_image" value="1" >'.TEXT_DEL_IMAGE);
      }
      $contents[] = array('text' => '<br>' . TEXT_EDIT_ORDERS_STATUS_IMAGE . '&nbsp;' .  tep_draw_file_field('orders_status_image'));
     
      $default_sel = '1';
      if ($oInfo->finished == '1') {
        $default_sel = '2';
      }
      if ($oInfo->is_cancle == '1') {
        $default_sel = '3'; 
      }
      $contents[] = array('text' => '<br>' . TEXT_ORDERS_STATUS_OPTION . '<br>' .  tep_draw_radio_field('option_status', '1', (($default_sel == '1')?true:false)).TEXT_ORDERS_STATUS_OPTION_NORMAL.'&nbsp;'.tep_draw_radio_field('option_status', '2', (($default_sel == '2')?true:false)).TEXT_ORDERS_STATUS_OPTION_SUCCESS.'&nbsp;'.tep_draw_radio_field('option_status', '3', (($default_sel == '3')?true:false)).TEXT_ORDERS_STATUS_OPTION_FAIL);
      
      if (DEFAULT_ORDERS_STATUS_ID != $oInfo->orders_status_id) $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('nomail', '1', $oInfo->nomail) . ' ' . 'DON\'T SEND MAIL');
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('calc_price', '1', $oInfo->calc_price) . ' ' . TEXT_ORDERS_STATUS_SET_PRICE_CALCULATION);
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('is_thzk', '1', $oInfo->is_thzk) . ' ' . TEXT_ORDERS_FETCH_CONDITION);

      //交易过期警告设置
      $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('transaction_expired', '1', $oInfo->transaction_expired) . ' ' . TEXT_TRANSACTION_EXPIRED);

      $contents[] = array('text' => TEXT_TRANSACTION_EXPIRED_COMMENT);
      
      $contents[] = array('align' => 'center', 'text' => '<br><a href="javascript:void(0);">' .  tep_html_element_button(IMAGE_SAVE, 'onclick="check_status_form();"') .  '</a> <a class="new_product_reset" href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] .  '&oID=' . $oInfo->orders_status_id) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => TEXT_INFO_HEADING_DELETE_ORDERS_STATUS);

      $contents = array('form' => tep_draw_form('orders_status', FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br>' . $oInfo->orders_status_name);
      if ($remove_status) $contents[] = array('align' => 'center', 'text' => '<br><a href="javascript:void(0);">' . tep_html_element_button(IMAGE_DELETE, 'onclick="check_status_form();"') .  '</a> <a class="new_product_reset" href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] .  '&oID=' . $oInfo->orders_status_id) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    default:
  if (isset($oInfo) and is_object($oInfo)) {
        $heading[] = array('text' => $oInfo->orders_status_name);
        $contents[] = array('align' => 'ledt', 'text' => 
          '<a href="' . tep_href_link(FILENAME_ORDERS_STATUS, 'page=' .  $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=edit') .  '">' . tep_html_element_button(IMAGE_EDIT) . '</a>' .  ($ocertify->npermission >= 15 ? (' <a href="' .  tep_href_link(FILENAME_ORDERS_STATUS, 'page=' . $_GET['page'] . '&oID=' . $oInfo->orders_status_id . '&action=delete') . '">' .  tep_html_element_button(IMAGE_DELETE) . '</a>'):'')
        );

        $orders_status_inputs_string = '';
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $orders_status_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_get_orders_status_name($oInfo->orders_status_id, $languages[$i]['id']);
        }

        $contents[] = array('text' => $orders_status_inputs_string);
      }
  if(tep_not_null($oInfo->user_added)){
$contents[] = array('text' =>  TEXT_USER_ADDED. ' ' .$oInfo->user_added);
  }else{
$contents[] = array('text' =>  TEXT_USER_ADDED. ' ' .TEXT_UNSET_DATA);
  }if(tep_not_null(tep_datetime_short($oInfo->date_added))){
$contents[] = array('text' =>  TEXT_DATE_ADDED. ' ' .tep_datetime_short($oInfo->date_added));
  }else{
$contents[] = array('text' =>  TEXT_DATE_ADDED. ' ' .TEXT_UNSET_DATA);
  }if(tep_not_null($oInfo->user_update)){
$contents[] = array('text' =>  TEXT_USER_UPDATE. ' ' .$oInfo->user_update);
  }else{
$contents[] = array('text' =>  TEXT_USER_UPDATE. ' ' .TEXT_UNSET_DATA);
  }if(tep_not_null(tep_datetime_short($oInfo->date_update))){
$contents[] = array('text' =>  TEXT_DATE_UPDATE. ' ' .tep_datetime_short($oInfo->date_update));
  }else{
$contents[] = array('text' =>  TEXT_DATE_UPDATE. ' ' .TEXT_UNSET_DATA);
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
<?php

?>
