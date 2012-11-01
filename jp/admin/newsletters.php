<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
      case 'lock':
      case 'unlock':
        $newsletter_id = tep_db_prepare_input($_GET['nID']);
        $status = (($_GET['action'] == 'lock') ? '1' : '0');

        tep_db_query("update " . TABLE_NEWSLETTERS . " set locked = '" . $status . "' where newsletters_id = '" . tep_db_input($newsletter_id) . "'");

        tep_redirect(tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')));
        break;
      case 'insert':
      case 'update':
  if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];//权限判断
         else $site_arr="";
 forward401Unless(editPermission($site_arr, $lsite_id));
        $newsletter_id = tep_db_prepare_input($_POST['newsletter_id']);
        $newsletter_module = tep_db_prepare_input($_POST['module']);
        $title = tep_db_prepare_input($_POST['title']);
        $content = tep_db_prepare_input($_POST['content']);

        $newsletter_error = false;
        if (empty($title)) {
          $messageStack->add(ERROR_NEWSLETTER_TITLE, 'error');
          $newsletter_error = true;
        }
        if (empty($newsletter_module)) { 
          $messageStack->add(ERROR_NEWSLETTER_MODULE, 'error');
          $newsletter_error = true;
        }

        if (!$newsletter_error) {
          $sql_data_array = array('title' => $title,
                                  'content' => $content,
				  'module' => $newsletter_module,
				  'user_added' => $_POST['user_added'],
				  'user_update'=> $_POST['user_update'],
				  'last_modified' => 'now()',
			  );

          if ($_GET['action'] == 'insert') {
            $site_id = tep_db_prepare_input($_POST['site_id']);
            $sql_data_array['date_added'] = 'now()';
            $sql_data_array['status'] = '0';
            $sql_data_array['locked'] = '0';
            $sql_data_array['site_id'] = $site_id;
	    //$sql_data_array['user_added'] = $_POST['user_added'];
	    //$sql_data_array['user_update'] = $_POST['user_update'];
	    //$sql_data_array['last_modified'] = 'now()';
            tep_db_perform(TABLE_NEWSLETTERS, $sql_data_array);
            $newsletter_id = tep_db_insert_id();
          } elseif ($_GET['action'] == 'update') {
            tep_db_perform(TABLE_NEWSLETTERS, $sql_data_array, 'update', 'newsletters_id = \'' . tep_db_input($newsletter_id) . '\'');
          }

          tep_redirect(tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $newsletter_id.(isset($_GET['lsite_id'])?('&site_id='.$_GET['lsite_id']):'')));
        } else {
          $_GET['action'] = 'new';
        }
        break;
      case 'deleteconfirm':
        $newsletter_id = tep_db_prepare_input($_GET['nID']);

        tep_db_query("delete from " . TABLE_NEWSLETTERS . " where newsletters_id = '" . tep_db_input($newsletter_id) . "'");

        tep_redirect(tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')));
        break;
      case 'delete':
      case 'new': if (!@$_GET['nID']) break;
      case 'send':
      case 'confirm_send':
        $newsletter_id = tep_db_prepare_input($_GET['nID']);

        $check_query = tep_db_query("select locked from " . TABLE_NEWSLETTERS . " where newsletters_id = '" . tep_db_input($newsletter_id) . "'");
        $check = tep_db_fetch_array($check_query);

        if ($check['locked'] < 1) {
          switch ($_GET['action']) {
            case 'delete': $error = ERROR_REMOVE_UNLOCKED_NEWSLETTER; break;
            case 'new': $error = ERROR_EDIT_UNLOCKED_NEWSLETTER; break;
            case 'send': $error = ERROR_SEND_UNLOCKED_NEWSLETTER; break;
            case 'confirm_send': $error = ERROR_SEND_UNLOCKED_NEWSLETTER; break;
          }
          $messageStack->add_session($error, 'error');
          tep_redirect(tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')));
        }
        break;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script type="text/javascript">
function check_send_mail()
{
   alert('<?php echo NOTICE_SEND_ZERO_MAIL_TEXT;?>');
}
</script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=[^&]+/',$belong,$belong_temp_array);
if($belong_temp_array[0][0] != ''){
  preg_match_all('/nID=[^&]+/',$belong,$belong_array);
  if($belong_array[0][0] != '' && $belong_temp_array[0][0] != 'action=delete'){

    $belong = $href_url.'?'.$belong_array[0][0].'|||'.$belong_temp_array[0][0];
  }else{
    if($belong_temp_array[0][0] != '' && $belong_temp_array[0][0] != 'action=delete'){
      if($belong_temp_array[0][0] != 'action=update'){
        if($belong_temp_array[0][0] != 'action=insert'){
          $belong = $href_url.'?'.$belong_temp_array[0][0];
        }else{
          $belong = $href_url.'?action=new'; 
        }
      }else{
        $belong = $href_url.'?'.'nID='.$_POST['newsletter_id'].'|||action=new'; 
      }
    }else{
      $belong = $href_url; 
    }
  }
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<div id="spiffycalendar" class="text"></div>
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
    <td width="100%" valign="top"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
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
    if (@$_GET['nID']) {
      $nID = tep_db_prepare_input($_GET['nID']);
      $form_action = 'update';

      $newsletter_query = tep_db_query("
          select n.title, 
                 n.content, 
                 n.module, 
                 s.romaji,
                 s.name as site_name,
                 s.id as site_id 
          from " . TABLE_NEWSLETTERS . " n, ".TABLE_SITES." s
          where newsletters_id = '" . tep_db_input($nID) . "'
            and s.id = n.site_id
          ");
      $newsletter = tep_db_fetch_array($newsletter_query);

      $nInfo = new objectInfo($newsletter);
    } elseif ($_POST) {
      $nInfo = new objectInfo($_POST);
    } else {
      $nInfo = new objectInfo(array());
    }

    $file_extension = substr($PHP_SELF, strrpos($PHP_SELF, '.'));
    $directory_array = array();
    if (!$dir = dir($libpath.'includes/modules/'. 'newsletters/')) $dir =  dir($libpath.'includes/modules/'.'newsletters/'); 
    if($dir)
  {
//    if ($dir = dir(DIR_WS_MODULES . 'newsletters/') or dir($libpath.'includes/modules/'.'newsletters/')) {
    
      while ($file = $dir->read()) {
        if (!is_dir(DIR_WS_MODULES . 'newsletters/' . $file)) {
          if (substr($file, strrpos($file, '.')) == $file_extension) {
            $directory_array[] = $file;
          }
        }
      }
      sort($directory_array);
      $dir->close();
    }

    for ($i = 0, $n = sizeof($directory_array); $i < $n; $i++) {
      $modules_array[] = array('id' => substr($directory_array[$i], 0, strrpos($directory_array[$i], '.')), 'text' => substr($directory_array[$i], 0, strrpos($directory_array[$i], '.')));
    }
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr><?php echo tep_draw_form('newsletter', FILENAME_NEWSLETTERS, 'page=' . (isset($_GET['page'])?$_GET['page']:'') . '&action=' . $form_action.(isset($_GET['lsite_id'])?('&lsite_id='.$_GET['lsite_id']):'')); if ($form_action == 'update') echo tep_draw_hidden_field('newsletter_id', $nID); ?>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
	    <td class="main">
<input type="hidden" name="user_added" value="<?php echo $user_info['name'];?>">
<input type="hidden" name="user_update" value="<?php echo $user_info['name'];?>">
<?php echo ENTRY_SITE; ?>
</td>
            <td class="main"><?php echo isset($_GET['nID']) && $_GET['nID']?$newsletter['site_name']:tep_site_pull_down_menu(); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_NEWSLETTER_MODULE; ?></td>
            <td class="main"><?php echo tep_draw_pull_down_menu('module', $modules_array, isset($nInfo->mdule)?$nInfo->module:''); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_NEWSLETTER_TITLE; ?></td>
            <td class="main"><?php echo tep_draw_input_field('title', isset($nInfo->title)?$nInfo->title:'', '', true); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main" valign="top"><?php echo TEXT_NEWSLETTER_CONTENT; ?></td>
            <td class="main"><?php echo tep_draw_textarea_field('content', 'soft', '100%', '20', isset($nInfo->content)?$nInfo->content:''); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" align="right"><?php echo (($form_action == 'insert') ?
                tep_html_element_submit(IMAGE_SAVE) :
                tep_html_element_submit(IMAGE_SAVE)).  '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' .  (isset($_GET['page'])?$_GET['page']:'') . '&nID=' .  (isset($_GET['nID'])?$_GET['nID']:'') .  (isset($_GET['lsite_id'])?('&site_id='.$_GET['lsite_id']):'')) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>'; ?></td>
          </tr>
        </table></td>
      </form></tr>
<?php
  } elseif (@$_GET['action'] == 'preview') {
    $nID = tep_db_prepare_input($_GET['nID']);

    $newsletter_query = tep_db_query("select title, content, module from " . TABLE_NEWSLETTERS . " where newsletters_id = '" . tep_db_input($nID) . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);

    $nInfo = new objectInfo($newsletter);
?>
      <tr>
        <td align="right"><!--<?php echo '<a href="' .  tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' .  $_GET['nID'] .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' .  tep_html_element_button(IMAGE_BACK) . '</a>'; ?>--></td>
      </tr>
      <tr>
        <td><tt><?php echo nl2br($nInfo->content); ?></tt></td>
      </tr>
      <tr>
        <td align="right"><?php echo '<a href="' .  tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' .  $_GET['nID'] .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' .  tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td>
      </tr>
<?php
  } elseif (@$_GET['action'] == 'send') {
    $nID = tep_db_prepare_input($_GET['nID']);

    $newsletter_query = tep_db_query("select title, content, module from " . TABLE_NEWSLETTERS . " where newsletters_id = '" . tep_db_input($nID) . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);

    $nInfo = new objectInfo($newsletter);

    include(DIR_WS_LANGUAGES . $language . '/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    include(DIR_WS_MODULES . 'newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    $module_name = $nInfo->module;
    $module = new $module_name($nInfo->title, $nInfo->content);
?>
      <tr>
        <td><?php if ($module->show_choose_audience) { echo
          $module->choose_audience(); } else { echo
            $module->confirm($_GET['send_site_id']); } ?></td>
      </tr>
<?php
  } elseif (isset($_GET['action']) && $_GET['action'] == 'confirm') {
    $nID = tep_db_prepare_input($_GET['nID']);

    $newsletter_query = tep_db_query("select title, content, module from " . TABLE_NEWSLETTERS . " where newsletters_id = '" . tep_db_input($nID) . "'");
    $newsletter = tep_db_fetch_array($newsletter_query);

    $nInfo = new objectInfo($newsletter);

    include(DIR_WS_LANGUAGES . $language . '/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    include(DIR_WS_MODULES . 'newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    $module_name = $nInfo->module;
    $module = new $module_name($nInfo->title, $nInfo->content);
?>
      <tr>
        <td><?php echo $module->confirm(); ?></td>
      </tr>
<?php
  } elseif (isset($_GET['action']) && $_GET['action'] == 'confirm_send') {
    $nID = tep_db_prepare_input($_GET['nID']);

    $newsletter_query = tep_db_query("
        select newsletters_id, 
               title, 
               content, 
               module
        from " . TABLE_NEWSLETTERS . "
        where newsletters_id = '" . tep_db_input($nID) . "'
        ");
    $newsletter = tep_db_fetch_array($newsletter_query);

    $nInfo = new objectInfo($newsletter);
    include(DIR_WS_LANGUAGES . $language . '/modules/newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    include(DIR_WS_MODULES . 'newsletters/' . $nInfo->module . substr($PHP_SELF, strrpos($PHP_SELF, '.')));
    $module_name = $nInfo->module;
    $module = new $module_name($nInfo->title, $nInfo->content);
?>
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main" valign="middle">
            <?php
            if (!isset($_GET['send_finish'])) { 
            ?>
            <b><?php echo TEXT_PLEASE_WAIT; ?></b>
            <?php } else {?> 
            <font color="#ff0000"><b><?php echo TEXT_FINISHED_SENDING_EMAILS; ?></b></font>
            <?php }?> 
            </td>
          </tr>
        </table></td>
      </tr>
<?php
  tep_set_time_limit(0);
  flush();
  if (!isset($_GET['send_finish'])) { 
    if (!isset($_GET['selected_box'])) {
      $module->send($nInfo->newsletters_id,$_GET['send_site_id']); 
      ?>
      <script type="text/javascript">
        setTimeout("window.location.href='<?php echo tep_href_link('newsletters.php', 'page='.$_GET['page'].'&nID='.$_GET['nID'].'&action=confirm_send&send_site_id='.$_GET['send_site_id'].'&send_finish=1');?>';", 6000); 
      </script>
      <?php
    } 
  } 
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><?php echo '<a href="' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' .
        $_GET['page'] . '&nID=' . $_GET['nID'] .
        (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' .
        tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td>
      </tr>
<?php
  } else {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            <?php tep_site_filter(FILENAME_NEWSLETTERS);?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SITE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_NEWSLETTERS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_SIZE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_MODULE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_SENT; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $newsletters_query_raw = "
      select n.newsletters_id, 
             n.title, 
             length(n.content) as content_length, 
             n.module, 
             n.date_added, 
	     n.user_added,
	     n.user_update,
	     n.last_modified,
             n.date_sent, 
             n.status, 
             n.locked, 
             s.romaji,
             s.id as site_id 
    from " . TABLE_NEWSLETTERS . " n, ".TABLE_SITES." s
    where s.id = n.site_id
      " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and s.id = '" . intval($_GET['site_id']) . "' " : '') . "
    order by n.date_added desc";
    $newsletters_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $newsletters_query_raw, $newsletters_query_numrows);
    $newsletters_query = tep_db_query($newsletters_query_raw);
    while ($newsletters = tep_db_fetch_array($newsletters_query)) {
      if (((!isset($_GET['nID']) || !$_GET['nID']) || (@$_GET['nID'] == $newsletters['newsletters_id'])) && (!isset($nInfo) || !$nInfo) && (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new')) {
        $nInfo = new objectInfo($newsletters);
      }

      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      if ( isset($nInfo) && (is_object($nInfo)) && ($newsletters['newsletters_id'] == $nInfo->newsletters_id) ) {
        echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=preview'.(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '\'">' . "\n";
      } else {
        echo '                  <tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $newsletters['newsletters_id'] . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo $newsletters['romaji'];?></td>
                <td class="dataTableContent"><?php echo '<a href="' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $newsletters['newsletters_id'] . '&action=preview'.(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;' . $newsletters['title']; ?></td>
                <td class="dataTableContent" align="right"><?php echo number_format($newsletters['content_length']) . ' bytes'; ?></td>
                <td class="dataTableContent" align="right"><?php echo $newsletters['module']; ?></td>
                <td class="dataTableContent" align="center"><?php if ($newsletters['status'] == '1') { echo tep_image(DIR_WS_ICONS . 'tick.gif', ICON_TICK); } else { echo tep_image(DIR_WS_ICONS . 'cross.gif', ICON_CROSS); } ?></td>
                <td class="dataTableContent" align="center"><?php if ($newsletters['locked'] > 0) { echo tep_image(DIR_WS_ICONS . 'locked.gif', ICON_LOCKED); } else { echo tep_image(DIR_WS_ICONS . 'unlocked.gif', ICON_UNLOCKED); } ?></td>
                <td class="dataTableContent" align="right"><?php if ( isset($nInfo) && (is_object($nInfo)) && ($newsletters['newsletters_id'] == $nInfo->newsletters_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $newsletters['newsletters_id']) . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').'">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
              <tr>
                <td colspan="7"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $newsletters_split->display_count($newsletters_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS); ?></td>
                    <td class="smallText" align="right"><?php echo $newsletters_split->display_links($newsletters_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
                  <tr>
                    <td align="right" colspan="2"><?php echo '<a href="' .
                    tep_href_link(FILENAME_NEWSLETTERS, 'action=new') .
                    (isset($_GET['site_id'])?('&lsite_id='.$_GET['site_id']):'').'">'
                    . tep_html_element_button(IMAGE_NEW_PROJECT) . '</a>'; ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch (@$_GET['action']) {
    case 'delete':
      $heading[] = array('text' => '<b>' . $nInfo->title . '</b>');

      $contents = array('form' => tep_draw_form('newsletters', FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=deleteconfirm'.(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $nInfo->title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_DELETE) . ' <a href="' .  tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' .  $_GET['nID'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    default:
  if (isset($nInfo) && is_object($nInfo)) {
        $site_id = $nInfo->site_id;
        $heading[] = array('text' => '<b>' . $nInfo->title . '</b>');

        if ($nInfo->locked > 0) {
          $contents[] = array('align' => 'center', 'text' => 
            '<a href="' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' .  $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=new' .  (isset($_GET['site_id'])?('&lsite_id='.$_GET['site_id']):'')) . '">' .  tep_html_element_button(IMAGE_EDIT) . '</a>' . ($ocertify->npermission == 15 ? (' <a href="' .  tep_href_link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=delete' .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_html_element_button(IMAGE_DELETE) . '</a>'):'')
          . ' <a href="' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' .  $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=preview' .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' .  tep_html_element_button(IMAGE_PREVIEW) . '</a>' 
          . ' <a href="' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' .
            $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=send' .
            (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').
            (isset($site_id)?('&send_site_id='.$site_id):'')) . '">' .  tep_html_element_button(IMAGE_SEND) . '</a>' 
          . ' <a href="' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' .  $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=unlock' .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' .  tep_html_element_button(IMAGE_UNLOCK) . '</a>'
          );
        } else {
          $contents[] = array('align' => 'center', 'text' => 
            '<a href="' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' .  $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=preview' .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' .  tep_html_element_button(IMAGE_PREVIEW) . '</a>' 
          . ' <a href="' . tep_href_link(FILENAME_NEWSLETTERS, 'page=' .  $_GET['page'] . '&nID=' . $nInfo->newsletters_id . '&action=lock' .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' .  tep_html_element_button(IMAGE_LOCK) . '</a>'
          );
        }
$contents[] = array('text' => '<br>' . TEXT_USER_ADDED . ' ' . $nInfo->user_added);
$contents[] = array('text' => '<br>' . TEXT_NEWSLETTER_DATE_ADDED . ' ' . tep_datetime_short($nInfo->date_added));
$contents[] = array('text' => '<br>' . TEXT_USER_UPDATE . ' ' . $nInfo->user_update);
$contents[] = array('text' => '<br>' . TEXT_LAST_MODIFIED . ' ' . tep_datetime_short($nInfo->last_modified));

        if ($nInfo->status == '1') $contents[] = array('text' => '<br>'.TEXT_NEWSLETTER_DATE_SENT . ' ' . tep_datetime_short($nInfo->date_sent));
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td class="right_column_a" width="25%" valign="top">' . "\n";

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
    </table>
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
