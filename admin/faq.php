<?php
  require('includes/application_top.php');

  //define('FILENAME_FAQ', 'faq.php');
  define('TABLE_FAQ_CATEGORIES', 'faq_categories');
  define('TABLE_FAQ_QUESTIONS',  'faq_questions');

  //require(DIR_WS_LANGUAGES . $language . '/' .  FILENAME_FAQ);

if (isset($_GET['action']) and $_GET['action']) {
    switch ($_GET['action']) {
      case 'insert_category':
        $g_id     = tep_db_prepare_input($_POST['g_id']);
        $category = tep_db_prepare_input($_POST['category']);
        $c_order  = tep_db_prepare_input($_POST['c_order']);
        tep_db_query("insert into " . TABLE_FAQ_CATEGORIES . " (
          c_id, g_id, c_order, category
          ) values (
          NULL, '" . $g_id . "', '" . $c_order . "', '" . $category . "')");
        $messageStack->add(TEXT_FAQ_INSERT_CATEGORY_SUCCESS, 'success');
        tep_redirect(tep_href_link(FILENAME_FAQ, 'g_id='.$g_id.'&cID='.tep_db_insert_id()));
        break;
      case 'insert_question':
        $c_id     = tep_db_prepare_input($_POST['c_id']);
        $q_order  = tep_db_prepare_input($_POST['q_order']);
        $question = tep_db_prepare_input($_POST['question']);
        $answer   = tep_db_prepare_input($_POST['answer']);

        tep_db_query("insert into " . TABLE_FAQ_QUESTIONS . " (
          q_id, c_id, q_order, question, answer) values (NULL, '".$c_id."', '".$q_order."', '".$question."', '".$answer."')");
        $messageStack->add(TEXT_FAQ_INSERT_QUESTION_SUCCESS, 'success');
        tep_redirect(tep_href_link(FILENAME_FAQ, 'c_id='.$c_id.'&q_id='.tep_db_insert_id()));
        break;
      case 'save_question':
        $q_id     = tep_db_prepare_input($_GET['q_id']);
        $q_order  = tep_db_prepare_input($_POST['q_order']);
        $question = tep_db_prepare_input($_POST['question']);
        $answer   = tep_db_prepare_input($_POST['answer']);

        tep_db_query("
            update " . TABLE_FAQ_QUESTIONS . " set q_order='".$q_order."', question='".$question."', answer='".$answer."' where q_id = '".$q_id."'");
        $messageStack->add(TEXT_FAQ_UPDATE_QUESTION_SUCCESS, 'success');
        $q = tep_get_faq_question($q_id);
        tep_redirect(tep_href_link(FILENAME_FAQ, 'c_id='.$q['c_id'].'&q_id='. $q_id));
        break;
      case 'save_category':
        $c_id    = tep_db_prepare_input($_GET['c_id']);
        $c_order = tep_db_prepare_input($_POST['c_order']);
        $category = tep_db_prepare_input($_POST['category']);
        
        tep_db_query("update " . TABLE_FAQ_CATEGORIES . " set c_order='".$c_order."', category='".$category."' where c_id = '".$c_id."'");
        $messageStack->add(TEXT_FAQ_UPDATE_CATEGORY_SUCCESS, 'success');
        $c = tep_get_faq_category($c_id);
        tep_redirect(tep_href_link(FILENAME_FAQ, 'g_id='.$c['g_id'].'&cID='.$c_id));
        break;
      case 'delete_category_confirm':
        $c_id = tep_db_prepare_input($_GET['c_id']);
        $c = tep_get_faq_category($c_id);
        tep_db_query("delete from " . TABLE_FAQ_CATEGORIES . " where c_id = '" . tep_db_input($c_id) . "'");
        tep_db_query("delete from " . TABLE_FAQ_QUESTIONS . " where c_id = '" . tep_db_input($c_id) . "'");
        $messageStack->add(TEXT_FAQ_DELETE_CATEGORY_SUCCESS, 'success');
        tep_redirect(tep_href_link(FILENAME_FAQ, 'g_id='.$c['g_id']));
        break;
      case 'delete_question_confirm':
        $q_id = tep_db_prepare_input($_GET['q_id']);
        $q = tep_get_faq_question($q_id);
        tep_db_query("delete from " . TABLE_FAQ_QUESTIONS . " where q_id = '" . tep_db_input($q_id) . "'");
        $messageStack->add(TEXT_FAQ_DELETE_QUESTION_SUCCESS, 'success');
        tep_redirect(tep_href_link(FILENAME_FAQ, 'c_id='.$q['c_id']));
        break;
    }
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
            <td valign="top">
            
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php if (isset($_GET['c_id'])) {
  // question list
?>
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_QUESTION_QUESTION; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_QUESTION_ORDER; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $question_query = tep_db_query("select * from " . TABLE_FAQ_QUESTIONS . " where c_id='".$c_id."'  order by q_order ASC");
  while ($question = tep_db_fetch_array($question_query)) {
      if (( (!isset($_GET['q_id']) || !$_GET['q_id']) || ($_GET['q_id'] == $question['q_id'])) && (!isset($qInfo) || !$qInfo) && (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new')) {
      $qInfo = new objectInfo($question);
    }

    if (isset($qInfo) && (is_object($qInfo)) && ($question['q_id'] == $qInfo->q_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_FAQ, 'c_id=' . $qInfo->q_id. '&action=edit_question') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_FAQ, 'c_id='.$_GET['c_id'].'&q_id=' . $question['q_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $question['question']; ?></td>
                <td class="dataTableContent"><?php echo $question['q_order']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( isset($qInfo) && (is_object($qInfo)) && ($question['q_id'] == $qInfo->q_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_FAQ, 'c_id=' . $_GET['c_id'] . '&q_id=' . $question['q_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
            } else if(isset($_GET['g_id'])) {
  // category list
?>
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CATEGORY_CATEGORY; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CATEGORY_ORDER; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $categories_query = tep_db_query("select * from " . TABLE_FAQ_CATEGORIES . " where g_id = '".$_GET['g_id']."' order by c_order ASC");
  while ($category = tep_db_fetch_array($categories_query)) {
      if (( (!isset($_GET['cID']) || !$_GET['cID']) || ($_GET['cID'] == $category['c_id'])) && (!isset($cInfo) || !$cInfo) && (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($category);
    }

    if (isset($cInfo) && (is_object($cInfo)) && ($category['c_id'] == $cInfo->c_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_FAQ, 'g_id='.$_GET['g_id'].'&cID=' . $cInfo->c_id . '&action=edit_category') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_FAQ, 'g_id='.$_GET['g_id'].'&cID=' . $category['c_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php 
                    echo '<a href="' . tep_href_link(FILENAME_FAQ, 'c_id=' . $category['c_id']) . '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;' .  $category['category']; ?></td>
                <td></td>
                <td class="dataTableContent" align="right"><?php if ( isset($cInfo) && (is_object($cInfo)) && ($category['c_id'] == $cInfo->c_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_FAQ, '&cID=' . $category['c_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
            } else {
              // game list
?>
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_GAME_NAME; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $games_query = tep_db_query("select * from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.site_id = 0 and c.parent_id = '0' and c.categories_id IN (". tep_get_faq_game_id_string() .") order by sort_order ASC");
  while ($game = tep_db_fetch_array($games_query)) {
      if (( (!isset($_GET['cID']) || !$_GET['cID']) || ($_GET['cID'] == $game ['categories_id'])) && (!isset($gInfo) || !$gInfo) && (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new')) {
      $gInfo = new objectInfo($game );
    }

      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_FAQ, '&g_id=' . $game['categories_id']) . '\'">' . "\n";
?>
                <td class="dataTableContent"><?php echo $game['categories_name']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( isset($gInfo) && (is_object($gInfo)) && ($game['categories_id'] == $gInfo->categories_id) ) { 
                  echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); 
                } else { 
                  echo '<a href="' . tep_href_link(FILENAME_FAQ, '&cID=' . $game['categories_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
                } ?>&nbsp;</td>
              </tr>
<?php
  }
            }

?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (!isset($_GET['action'])) {
    if (isset($_GET['g_id'])) {
?>
                  <tr>
                    <td colspan="2" align="right">
                    <?php echo '<a href="' . tep_href_link(FILENAME_FAQ, 'action=new_category&g_id=' . $_GET['g_id']) . '">' . tep_image_button('button_new_tag.gif', IMAGE_NEW_TAG) . '</a>'; ?>

                    <?php echo '<a href="' . tep_href_link(FILENAME_FAQ) . '">' . tep_image_button('button_new_tag.gif', IMAGE_BACK) . '</a>'; ?>
                    </td>
                  </tr>
<?php
    } else if (isset($_GET['c_id'])) {
      $current_category = tep_get_faq_category($_GET['c_id']);
?>
                  <tr>
                    <td colspan="2" align="right">
                    <?php echo '<a href="' . tep_href_link(FILENAME_FAQ, 'action=new_question&c_id=' . $_GET['c_id']) . '">' . tep_image_button('button_new_tag.gif', IMAGE_NEW_TAG) . '</a>'; ?>

                    <?php echo '<a href="' . tep_href_link(FILENAME_FAQ, 'g_id=' . $current_category['g_id']) . '">' . tep_image_button('button_new_tag.gif', IMAGE_BACK) . '</a>'; ?>
                    </td>
                  </tr>
<?php
    }
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
switch (isset($_GET['action'])? $_GET['action']:'') {
    case 'new_category':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CATEGORY . '</b>');

      $contents = array('form' => tep_draw_form('category', FILENAME_FAQ, 'action=insert_category', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => tep_draw_hidden_field('g_id', $_GET['g_id']));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_CATEGORY_CATEGORY . '<br>' . tep_draw_input_field('category'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_QUESTION_ORDER . '<br>' . tep_draw_input_field('c_order'));
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT) . '&nbsp;<a href="' . tep_href_link(FILENAME_FAQ, 'g_id=' . $_GET['g_id']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'new_question':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_QUESTION . '</b>');

      $contents = array('form' => tep_draw_form('question', FILENAME_FAQ, 'action=insert_question', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => tep_draw_hidden_field('c_id'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_QUESTION_QUESTION . '<br>' . tep_draw_input_field('question'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_QUESTION_ANSWER . '<br>' . tep_draw_textarea_field('answer','',30,3));
      $contents[] = array('text' => '<br>' . TEXT_INFO_QUESTION_ORDER . '<br>' . tep_draw_input_field('q_order'));
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT) . '&nbsp;<a href="' . tep_href_link(FILENAME_FAQ, 'c_id=' . $_GET['c_id']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'edit_category':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CATEGORY . '</b>');

      $contents = array('form' => tep_draw_form('category', FILENAME_FAQ, 'c_id=' . $cInfo->c_id . '&action=save_category', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_CATEGORY_CATEGORY . '<br>' . tep_draw_input_field('category', $cInfo->category));
      $contents[] = array('text' => '<br>' . TEXT_INFO_QUESTION_ORDER . '<br>' . tep_draw_input_field('c_order', $cInfo->c_order));
      
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_update.gif', IMAGE_UPDATE) . '&nbsp;<a href="' . tep_href_link(FILENAME_FAQ, 'g_id=' . $cInfo->g_id . '&cID=' . $cInfo->c_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'edit_question':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_QUESTION . '</b>');

      $contents = array('form' => tep_draw_form('question', FILENAME_FAQ, 'q_id=' . $qInfo->q_id . '&action=save_question', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_QUESTION_QUESTION . '<br>' . tep_draw_input_field('question', $qInfo->question));
      $contents[] = array('text' => '<br>' . TEXT_INFO_QUESTION_ANSWER . '<br>' . tep_draw_textarea_field('answer','',30,3, $qInfo->answer));
      $contents[] = array('text' => '<br>' . TEXT_INFO_QUESTION_ORDER . '<br>' . tep_draw_input_field('q_order', $qInfo->q_order));
      
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_update.gif', IMAGE_UPDATE) . '&nbsp;<a href="' . tep_href_link(FILENAME_FAQ, 'c_id=' . $qInfo->c_id . '&q_id=' . $qInfo->q_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'delete_category':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CATEGORY . '</b>');

      $contents = array('form' => tep_draw_form('question', FILENAME_FAQ, 'c_id=' . $cInfo->c_id . '&action=delete_category_confirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $cInfo->category . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . '&nbsp;<a href="' . tep_href_link(FILENAME_FAQ, 'g_id=' . $cInfo->g_id . '&cID=' . $cInfo->c_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    case 'delete_question':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_QUESTION . '</b>');

      $contents = array('form' => tep_draw_form('question', FILENAME_FAQ, 'q_id=' . $qInfo->q_id . '&action=delete_question_confirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $qInfo->question. '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . '&nbsp;<a href="' . tep_href_link(FILENAME_FAQ, 'c_id=' . $qInfo->c_id . '&q_id=' . $qInfo->q_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (isset($cInfo) && is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . $cInfo->category. '</b>');

        $contents[] = array(
            'align' => 'center', 
            'text' => '<a href="' . tep_href_link(FILENAME_FAQ, 'g_id='.$_GET['g_id'].'&cID=' . $cInfo->c_id. '&action=edit_category') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_FAQ, 'g_id='.$_GET['g_id'].'&cID=' . $cInfo->c_id. '&action=delete_category') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_CATEGORY_CATEGORY . '<br>' . $cInfo->category. '<br>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_CATEGORY_ORDER . '<br>' . $cInfo->c_order. '<br>');
      } else if (isset($qInfo) && is_object($qInfo)) {
        $heading[] = array('text' => '<b>' . $qInfo->question . '</b>');

        $contents[] = array(
            'align' => 'center', 
            'text' => '<a href="' . tep_href_link(FILENAME_FAQ, 'c_id=' . $_GET['c_id'] . '&q_id=' . $qInfo->q_id. '&action=edit_question') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_FAQ, 'c_id='.$_GET['c_id'].'&q_id=' . $qInfo->q_id. '&action=delete_question') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_QUESTION_QUESTION . '<br>' . $qInfo->question. '<br>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_QUESTION_ORDER . '<br>' . $qInfo->q_order. '<br>');
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
