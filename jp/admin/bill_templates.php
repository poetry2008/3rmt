<?php
  require('includes/application_top.php');
if (isset($_GET['action']) and $_GET['action']) {
    switch ($_GET['action']) {
      case 'insert':
        $name  = tep_db_prepare_input($_POST['name']);
        $data1 = tep_db_prepare_input($_POST['data1']);
        $data2 = tep_db_prepare_input($_POST['data2']);
        $data3 = tep_db_prepare_input($_POST['data3']);
        $data4 = tep_db_prepare_input($_POST['data4']);
        $data5 = tep_db_prepare_input($_POST['data5']);
        $data6 = tep_db_prepare_input($_POST['data6']);
        $data7 = tep_db_prepare_input($_POST['data7']);
        $data8 = tep_db_prepare_input($_POST['data8']);
        $data9 = tep_db_prepare_input($_POST['data9']);
        $data10 = tep_db_prepare_input($_POST['data10']);
        $data11 = tep_db_prepare_input($_POST['data11']);
        $email = tep_db_prepare_input($_POST['email']);
        $responsible = tep_db_prepare_input($_POST['responsible']);
        $sort_order  = tep_db_prepare_input($_POST['sort_order']);

        $t_query = tep_db_query("select * from ". TABLE_BILL_TEMPLATES . " where name = '" . $name . "'");
        $t_res = tep_db_fetch_array($t_query);
        if ($t_res) {
          $messageStack->add_session(TEXT_BILL_TEMPLATES_NAME_EXISTS, 'error');
          tep_redirect(tep_href_link(FILENAME_BILL_TEMPLATES, 'cPath=&action=new'));
        }
        tep_db_perform(TABLE_BILL_TEMPLATES, array(
          'name'  => $name,
          'data1' => $data1,
          'data2' => $data2,
          'data3' => $data3,
          'data4' => $data4,
          'data5' => $data5,
          'data6' => $data6,
          'data7' => $data7,
          'data8' => $data8,
          'data9' => $data9,
          'data10' => $data10,
          'data11' => $data11,
          'email' => $email,
          'responsible' => $responsible,
          'sort_order'  => $sort_order
        ));
        tep_redirect(tep_href_link(FILENAME_BILL_TEMPLATES));

        break;
      case 'save':
        $bill_templates_id = tep_db_prepare_input($_GET['cID']);
        
        $name  = tep_db_prepare_input($_POST['name']);
        $data1 = tep_db_prepare_input($_POST['data1']);
        $data2 = tep_db_prepare_input($_POST['data2']);
        $data3 = tep_db_prepare_input($_POST['data3']);
        $data4 = tep_db_prepare_input($_POST['data4']);
        $data5 = tep_db_prepare_input($_POST['data5']);
        $data6 = tep_db_prepare_input($_POST['data6']);
        $data7 = tep_db_prepare_input($_POST['data7']);
        $data8 = tep_db_prepare_input($_POST['data8']);
        $data9 = tep_db_prepare_input($_POST['data9']);
        $data10 = tep_db_prepare_input($_POST['data10']);
        $data11 = tep_db_prepare_input($_POST['data11']);
        $email = tep_db_prepare_input($_POST['email']);
        $responsible = tep_db_prepare_input($_POST['responsible']);
        $sort_order  = tep_db_prepare_input($_POST['sort_order']);
        
        $t_query = tep_db_query("select * from ". TABLE_BILL_TEMPLATES . " where name = '" . $name . "'");
        $t_res = tep_db_fetch_array($t_query);
        if ($t_res && $t_res['id'] != $bill_templates_id) {
          $messageStack->add_session(TEXT_BILL_TEMPLATES_NAME_EXISTS, 'error');
          tep_redirect(tep_href_link(FILENAME_BILL_TEMPLATES, 'action=new'));
        }
        tep_db_perform(TABLE_BILL_TEMPLATES, array(
          'name'  => $name,
          'data1' => $data1,
          'data2' => $data2,
          'data3' => $data3,
          'data4' => $data4,
          'data5' => $data5,
          'data6' => $data6,
          'data7' => $data7,
          'data8' => $data8,
          'data9' => $data9,
          'data10' => $data10,
          'data11' => $data11,
          'email' => $email,
          'responsible' => $responsible,
          'sort_order' => $sort_order
        ), 'update', "id = '" . tep_db_input($bill_templates_id) . "'");
        tep_redirect(tep_href_link(FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page'] . '&cID=' . $bill_templates_id));
        break;
      case 'deleteconfirm':
        $bill_templates_id = tep_db_prepare_input($_GET['cID']);
        tep_db_query("delete from " . TABLE_BILL_TEMPLATES . " where id = '" . tep_db_input($bill_templates_id) . "'");
        tep_redirect(tep_href_link(FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page']));
        break;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
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
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_BILL_TEMPLATES_NAME; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $bill_templates_query_raw = "
  select * 
  from " . TABLE_BILL_TEMPLATES . " order by sort_order,name";
                    
  $bill_templates_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $bill_templates_query_raw, $bill_templates_query_numrows);
  $bill_templates_query = tep_db_query($bill_templates_query_raw);
  while ($bill_templates = tep_db_fetch_array($bill_templates_query)) {
      if (( (!@$_GET['cID']) || (@$_GET['cID'] == $bill_templates['id'])) && (!@$cInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($bill_templates);
    }

    if (isset($cInfo) && (is_object($cInfo)) && ($bill_templates['id'] == $cInfo->id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page'] . '&cID=' . $bill_templates['id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $bill_templates['name']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( isset($cInfo) && (is_object($cInfo)) && ($bill_templates['id'] == $cInfo->id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page'] . '&cID=' . $bill_templates['id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $bill_templates_split->display_count($bill_templates_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BILL_TEMPLATES); ?></td>
                    <?php if(isset($_GET['sort'])&&$_GET['sort']){ ?>
                    <td class="smallText" align="right"><?php echo $bill_templates_split->display_links($bill_templates_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],'sort='.$_GET['sort']); ?></td>
                    <?php }else{ ?>
                    <td class="smallText" align="right"><?php echo $bill_templates_split->display_links($bill_templates_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                     <?php }?>
                  </tr>
<?php
        if (!isset($_GET['action'])) {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a href="' .  tep_href_link(FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page'] .  '&action=new') . '">' .  tep_html_element_button(BUTTON_NEW_TEXT) . '</a>'; ?></td>
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
switch (isset($_GET['action'])? $_GET['action']:'') {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_BILL_TEMPLATE . '</b>');

      $contents = array('form' => tep_draw_form('bill_templates', FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page'] . '&action=insert', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_NAME  . '<br>' . tep_draw_input_field('name'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA1 . '<br>' . tep_draw_textarea_field('data1','','25',''));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA2 . '<br>' . tep_draw_textarea_field('data2','','25',''));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA3 . '<br>' . tep_draw_textarea_field('data3','','25',''));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA4 . '<br>' . tep_draw_textarea_field('data4','','25',''));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA5 . '<br>' . tep_draw_textarea_field('data5','','25',''));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA6 . '<br>' . tep_draw_textarea_field('data6','','25',''));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA7 . '<br>' . tep_draw_textarea_field('data7','','25',''));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA8 . '<br>' . tep_draw_textarea_field('data8','','25',''));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA9 . '<br>' . tep_draw_textarea_field('data9','','25',''));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA11 . '<br>' . tep_draw_textarea_field('data11','','25',''));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA10 . '<br>' . tep_draw_textarea_field('data10','','25',''));
      
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_EMAIL . '<br>' . tep_draw_input_field('email'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_RESPONSIBLE . '<br>' . tep_draw_input_field('responsible'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', 1000));

      $contents[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_INSERT) . '&nbsp;<a href="' .  tep_href_link(FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page']) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_BILL_TEMPLATE . '</b>');

      $contents = array('form' => tep_draw_form('bill_templates', FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->id . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_NAME  . '<br>' . tep_draw_input_field('name', $cInfo->name));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA1 . '<br>' . tep_draw_textarea_field('data1','','25','',$cInfo->data1));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA2 . '<br>' . tep_draw_textarea_field('data2','','25','',$cInfo->data2));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA3 . '<br>' . tep_draw_textarea_field('data3','','25','',$cInfo->data3));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA4 . '<br>' . tep_draw_textarea_field('data4','','25','',$cInfo->data4));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA5 . '<br>' . tep_draw_textarea_field('data5','','25','',$cInfo->data5));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA6 . '<br>' . tep_draw_textarea_field('data6','','25','',$cInfo->data6));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA7 . '<br>' . tep_draw_textarea_field('data7','','25','',$cInfo->data7));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA8 . '<br>' . tep_draw_textarea_field('data8','','25','',$cInfo->data8));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA9 . '<br>' . tep_draw_textarea_field('data9','','25','',$cInfo->data9));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA11 . '<br>' . tep_draw_textarea_field('data11','','25','',$cInfo->data11));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_DATA10 . '<br>' . tep_draw_textarea_field('data10','','25','',$cInfo->data10));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_EMAIL . '<br>' . tep_draw_input_field('email', $cInfo->email));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_RESPONSIBLE . '<br>' . tep_draw_input_field('responsible', $cInfo->responsible));
      $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', $cInfo->sort_order));
      
      $contents[] = array('align' => 'center', 'text' => '<br>' .
          tep_html_element_submit(IMAGE_SAVE) . '&nbsp;<a href="' .  tep_href_link(FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page'] . '&cID=' .  $cInfo->id) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_BILL_TEMPLATE . '</b>');

      $contents = array('form' => tep_draw_form('bill_templates', FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $cInfo->name . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_DELETE) . '&nbsp;<a href="' .  tep_href_link(FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page'] . '&cID=' .  $cInfo->id) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . $cInfo->name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_BILL_TEMPLATES, 'page=' .  $_GET['page'] . '&cID=' . $cInfo->id . '&action=edit') . '">' .  tep_html_element_button(IMAGE_EDIT) . '</a>' . ($ocertify->npermission == 15 ? (' <a href="' .  tep_href_link(FILENAME_BILL_TEMPLATES, 'page=' . $_GET['page'] .  '&cID=' . $cInfo->id . '&action=delete') . '">' . tep_html_element_button(IMAGE_DELETE) . '</a>'):'')
        );
        $contents[] = array('text' => '<br>' . TEXT_INFO_BILL_TEMPLATES_NAME . '<br>' . $cInfo->name . '<br>');
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
