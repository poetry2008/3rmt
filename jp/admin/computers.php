<?php
  /**
   * $Id$
   *
   * PC管理
   */
  require('includes/application_top.php');

if (isset($_GET['action']) and $_GET['action']) {
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'insert' 新建按钮     
   case 'save' 更新按钮     
   case 'deleteconfirm' 删除按钮      
------------------------------------------------------*/
      case 'insert':
        $computers_name = tep_db_prepare_input($_POST['computers_name']);
        $sort_order = tep_db_prepare_input($_POST['sort_order']);

        $t_query = tep_db_query("select * from ". TABLE_COMPUTERS . " where computers_name = '" . $computers_name . "'");
        $t_res = tep_db_fetch_array($t_query);
        if ($t_res) {
          $messageStack->add_session(TEXT_COMPUTERS_NAME_EXISTS, 'error');
          tep_redirect(tep_href_link(FILENAME_COMPUTERS, 'cPath=&action=new'));
        }
        tep_db_query("insert into " . TABLE_COMPUTERS . " (computers_name, sort_order,user_added,date_added,user_update,date_update) values ('" . tep_db_input($computers_name) . "','" . tep_db_input($sort_order) . "','".$_POST['user_added']."',now(),'".$_POST['user_update']."',now())");
        tep_redirect(tep_href_link(FILENAME_COMPUTERS));
        break;
      case 'save':
        $computers_id = tep_db_prepare_input($_GET['cID']);
        $computers_name = tep_db_prepare_input($_POST['computers_name']);
        $sort_order = tep_db_prepare_input($_POST['sort_order']);
        
        $t_query = tep_db_query("select * from ". TABLE_COMPUTERS . " where computers_name = '" . $computers_name . "'");
        $t_res = tep_db_fetch_array($t_query);
        if ($t_res && $t_res['computers_id'] != $computers_id) {
          $messageStack->add_session(TEXT_COMPUTERS_NAME_EXISTS, 'error');
          tep_redirect(tep_href_link(FILENAME_COMPUTERS, 'cPath=&action=new'));
        }
        tep_db_query("update " . TABLE_COMPUTERS . " set computers_name = '" . tep_db_input($computers_name) . "',sort_order = '" . tep_db_input($sort_order) . "' ,user_update='".$_POST["user_update"]."',date_update=now() where computers_id = '" . tep_db_input($computers_id) . "'");
        tep_redirect(tep_href_link(FILENAME_COMPUTERS, 'page=' . $_GET['page'] . '&cID=' . $computers_id));
        break;
      case 'deleteconfirm':
        $computers_id = tep_db_prepare_input($_GET['cID']);
        tep_db_query("delete from " . TABLE_COMPUTERS . " where computers_id = '" . tep_db_input($computers_id) . "'");
        tep_db_query("delete from " . TABLE_ORDERS_TO_COMPUTERS . " where computers_id = '" . tep_db_input($computers_id) . "'");
        tep_redirect(tep_href_link(FILENAME_COMPUTERS, 'page=' . $_GET['page']));
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
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
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
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
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
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COMPUTERS_NAME; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_COMPUTER_ORDER?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  //echo MAX_DISPLAY_SEARCH_RESULTS;
  $computers_query_raw = "select * from " . TABLE_COMPUTERS . " order by sort_order asc";
  $computers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $computers_query_raw, $computers_query_numrows);
  $computers_query = tep_db_query($computers_query_raw);
  while ($computers = tep_db_fetch_array($computers_query)) {
      if (( (!@$_GET['cID']) || (@$_GET['cID'] == $computers['computers_id'])) && (!@$cInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($computers);
    }
    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }

    if (isset($cInfo) && (is_object($cInfo)) && ($computers['computers_id'] == $cInfo->computers_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_COMPUTERS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->computers_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_COMPUTERS, 'page=' . $_GET['page'] . '&cID=' . $computers['computers_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $computers['computers_name']; ?></td>
                <td class="dataTableContent"><?php echo $computers['sort_order']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( isset($cInfo) && (is_object($cInfo)) && ($computers['computers_id'] == $cInfo->computers_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_COMPUTERS, 'page=' . $_GET['page'] . '&cID=' . $computers['computers_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
            </table>
			<table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $computers_split->display_count($computers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COMPUTERS); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $computers_split->display_links($computers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></div></td>
                  </tr>
<?php
        if (!isset($_GET['action'])) {
?>
                  <tr>
                    <td colspan="2" align="right"><div class="td_button"><?php echo '<a href="' .
                    tep_href_link(FILENAME_COMPUTERS, 'page=' . $_GET['page'] .
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
switch (isset($_GET['action'])? $_GET['action']:'') {
/* -----------------------------------------------------
   case 'new' 右侧新建按钮页面     
   case 'edit' 右侧更新按钮页面     
   case 'delete' 右侧删除按钮页面 
   default 右侧默认页面
------------------------------------------------------*/
    case 'new':
      $heading[] = array('text' => TEXT_INFO_HEADING_NEW_COMPUTER);

      $contents = array('form' => tep_draw_form('computers', FILENAME_COMPUTERS, 'page=' . $_GET['page'] . '&action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<input type="hidden" name="user_added"   value="'.$_SESSION['user_name'].'">');
      $contents[] = array('text' => '<input type="hidden" name="user_update"   value="'.$_SESSION['user_name'].'">');

      $contents[] = array('text' => '<br>' . TEXT_INFO_COMPUTERS_NAME . '<br>' . tep_draw_input_field('computers_name'));
      $contents[] = array('text' => '<br>'.TABLE_HEADING_COMPUTER_ORDER.'<br>' . tep_draw_input_field('sort_order'));
      $contents[] = array('align' => 'center', 'text' => '<br>' .
          tep_html_element_submit(IMAGE_SAVE) . '&nbsp;<a href="' .  tep_href_link(FILENAME_COMPUTERS, 'page=' . $_GET['page']) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    case 'edit':
      $heading[] = array('text' => TEXT_INFO_HEADING_EDIT_COMPUTER);

      $contents = array('form' => tep_draw_form('computers', FILENAME_COMPUTERS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->computers_id . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<input type="hidden" name="user_update" value="'.$user_info['name'].'">');

      $contents[] = array('text' => '<br>' . TEXT_INFO_COMPUTERS_NAME . '<br>' . tep_draw_input_field('computers_name', $cInfo->computers_name));
      $contents[] = array('text' => '<br>'.TABLE_HEADING_COMPUTER_ORDER.'<br>' . tep_draw_input_field('sort_order', $cInfo->sort_order));
      $contents[] = array('align' => 'center', 'text' => '<br>' .
          tep_html_element_submit(IMAGE_SAVE) . '&nbsp;<a href="' .  tep_href_link(FILENAME_COMPUTERS, 'page=' . $_GET['page'] . '&cID=' .  $cInfo->computers_id) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => TEXT_INFO_HEADING_DELETE_COMPUTER);

      $contents = array('form' => tep_draw_form('computers', FILENAME_COMPUTERS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->computers_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br>' . $cInfo->computers_name);
      $contents[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_DELETE) . '&nbsp;<a href="' .  tep_href_link(FILENAME_COMPUTERS, 'page=' . $_GET['page'] . '&cID=' .  $cInfo->computers_id) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($cInfo)) {
        $heading[] = array('text' => $cInfo->computers_name);

        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_COMPUTERS, 'page=' . $_GET['page'] .  '&cID=' . $cInfo->computers_id . '&action=edit') . '">' . tep_html_element_button(IMAGE_EDIT) . '</a>' 
          . ($ocertify->npermission == 15 ? (' <a href="' .  tep_href_link(FILENAME_COMPUTERS, 'page=' . $_GET['page'] . '&cID=' .  $cInfo->computers_id . '&action=delete') . '">' . tep_html_element_button(IMAGE_DELETE) . '</a>'):'')
        );
        $contents[] = array('text' => '<br>' . TEXT_INFO_COMPUTERS_NAME . '<br>' . $cInfo->computers_name . '<br>');
if(tep_not_null($cInfo->user_added)){
$contents[] = array('text' =>  TEXT_USER_ADDED. ' ' .$cInfo->user_added);
}else{
$contents[] = array('text' =>  TEXT_USER_ADDED. ' ' .TEXT_UNSET_DATA);
}if(tep_not_null(tep_datetime_short($cInfo->date_added))){
$contents[] = array('text' =>  TEXT_DATE_ADDED. ' ' .tep_datetime_short($cInfo->date_added));
}else{
$contents[] = array('text' =>  TEXT_DATE_ADDED. ' ' .TEXT_UNSET_DATA);
}if(tep_not_null($cInfo->user_update)){
$contents[] = array('text' =>  TEXT_USER_UPDATE. ' ' .$cInfo->user_update);
}else{
$contents[] = array('text' =>  TEXT_USER_UPDATE. ' ' .TEXT_UNSET_DATA);
}if(tep_not_null(tep_datetime_short($cInfo->date_update))){
$contents[] = array('text' =>  TEXT_DATE_UPDATE. ' ' .tep_datetime_short($cInfo->date_update));
}else{
$contents[] = array('text' =>  TEXT_DATE_UPDATE. ' ' .TEXT_UNSET_DATA);
}


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
