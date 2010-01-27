<?php
/*
  $Id: customers.php,v 1.9 2004/05/22 03:45:29 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if ($HTTP_GET_VARS['act']) {
    switch ($HTTP_GET_VARS['act']) {
      case 'update':
        // tamura 2002/12/30 「全角」英数字を「半角」に変換
        $an_cols = array('navbar_title','heading_title','text_information');
        $error = false; 
        foreach ($an_cols as $col) {
          $HTTP_POST_VARS[$col] = tep_an_zen_to_han($HTTP_POST_VARS[$col]);
        }

        $cID = tep_db_prepare_input($HTTP_POST_VARS['cID']);
		$navbar_title = tep_db_prepare_input($HTTP_POST_VARS['navbar_title']);
		$heading_title = tep_db_prepare_input($HTTP_POST_VARS['heading_title']);
		$text_information = tep_db_prepare_input($HTTP_POST_VARS['text_information']);
		$status = tep_db_prepare_input($HTTP_POST_VARS['status']);
		$sort_id = tep_db_prepare_input($HTTP_POST_VARS['sort_id']);
		$page = tep_db_prepare_input($HTTP_POST_VARS['page']);
		$romaji = tep_db_prepare_input($HTTP_POST_VARS['romaji']);
        if (empty($romaji)) {
         $error = true;
         $error_message = ROMAJI_NOT_NULL;
         $HTTP_GET_VARS['action'] = 'edit';
        }

        if (preg_match('/[^a-zA-Z0-9_]/i', $romaji))
        {
          $error = true;
          $error_message = ROMAJI_WRONG_FORMAT;
          $HTTP_GET_VARS['action'] = 'edit';
        }
        $exists_romaji_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where romaji = '".$romaji."' and pID != '".$cID."'"); 
        $exists_romaji_num = tep_db_num_rows($exists_romaji_query); 
        if ($exists_romaji_num > 0) {
          $error_message = ROMAJI_EXISTS; 
          $error = true;
          $HTTP_GET_VARS['action'] = 'edit';
        }

        $sql_data_array = array('pID' => $cID,
                                'navbar_title' => $navbar_title,
                                'heading_title' => $heading_title,
                                'text_information' => $text_information,
                                'status' => $status,
                                'romaji' => $romaji,
                                'sort_id' => $sort_id);

        if ($error == false) {
          tep_db_perform(TABLE_INFORMATION_PAGE, $sql_data_array, 'update', "pID = '" . tep_db_input($cID) . "'");
          tep_redirect(tep_href_link(FILENAME_CONTENTS, 'cID=' . $cID . '&page='.$page));
        }
        break;
      case 'insert':
        // tamura 2002/12/30 「全角」英数字を「半角」に変換
        $an_cols = array('navbar_title','heading_title','text_information');
        $error = false; 
        foreach ($an_cols as $col) {
          $HTTP_POST_VARS[$col] = tep_an_zen_to_han($HTTP_POST_VARS[$col]);
        }

		$navbar_title = tep_db_prepare_input($HTTP_POST_VARS['navbar_title']);
		$heading_title = tep_db_prepare_input($HTTP_POST_VARS['heading_title']);
		$text_information = tep_db_prepare_input($HTTP_POST_VARS['text_information']);
		$status = tep_db_prepare_input($HTTP_POST_VARS['status']);
		$sort_id = tep_db_prepare_input($HTTP_POST_VARS['sort_id']);
		$romaji = tep_db_prepare_input($HTTP_POST_VARS['romaji']);
        if (empty($romaji)) {
         $error = true;
         $error_message = ROMAJI_NOT_NULL;
         $HTTP_GET_VARS['action'] = 'insert';
        }
        $exists_romaji_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where romaji = '".$romaji."'"); 
        $exists_romaji_num = tep_db_num_rows($exists_romaji_query); 
        if ($exists_romaji_num > 0) {
          $error_message = ROMAJI_EXISTS; 
          $error = true;
          $HTTP_GET_VARS['action'] = 'insert';
        }

        $sql_data_array = array('navbar_title' => $navbar_title,
                                'heading_title' => $heading_title,
                                'text_information' => $text_information,
                                'status' => $status,
                                'romaji' => $romaji,
                                'sort_id' => $sort_id);

        if ($error == false) {
          tep_db_perform(TABLE_INFORMATION_PAGE, $sql_data_array);
          tep_redirect(tep_href_link(FILENAME_CONTENTS));
        }
        break;	  
	  case 'setflag':
	    $status = tep_db_prepare_input($HTTP_GET_VARS['flag']);
		$cID = tep_db_prepare_input($HTTP_GET_VARS['cID']);
		$page = tep_db_prepare_input($HTTP_GET_VARS['page']);
		
		tep_db_query("update ".TABLE_INFORMATION_PAGE." set status = '".$status."' where pID = '".tep_db_input($cID)."'");
		tep_redirect(tep_href_link(FILENAME_CONTENTS, 'cID=' . $cID . '&page='.$page));
	    break;
	  case 'deleteconfirm':
        $cID = tep_db_prepare_input($HTTP_GET_VARS['cID']);

        tep_db_query("delete from " . TABLE_INFORMATION_PAGE . " where pID = '" . tep_db_input($cID) . "'");

        tep_redirect(tep_href_link(FILENAME_CONTENTS, '')); 
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
<?php
  if ($HTTP_GET_VARS['action'] == 'edit') {
  $detail_query = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '".$cID."'");
  $detail = tep_db_fetch_array($detail_query);
  
    switch ($detail['status']) {
      case '0': $in_status = false; $out_status = true; break;
      case '1':
      default: $in_status = true; $out_status = false;
    }
?> 
        <tr> 
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td> 
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td> <?php echo tep_draw_form('update', FILENAME_CONTENTS, 'act=update'); ?> 
            <table border="0" cellspacing="0" cellpadding="5"> 
              <tr> 
                <td class="main"><?php echo TEXT_DETAIL_STATUS; ?></td> 
            	<td class="main"><?php echo tep_draw_radio_field('status', '1', $in_status) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . tep_draw_radio_field('status', '0', $out_status) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?></td>
              </tr> 
              <tr> 
                <td class="main"><?php echo TEXT_DETAIL_SORT; ?></td> 
                <td><?php echo tep_draw_input_field('sort_id', $detail['sort_id']); ?></td> 
              </tr> 			  
              <tr>
                <td class="main"><?php echo TEXT_DETAIL_ROMAJI; ?></td>
                <td>
                <?php echo tep_draw_input_field('romaji', $detail['romaji']);?> 
                <?php
                if (isset($error_message)) {
                  echo $error_message; 
                }
                ?>
                </td>
              </tr>
			  <tr> 
                <td class="main"><?php echo TEXT_DETAIL_NAVBAR_TITLE; ?></td> 
                <td><?php echo tep_draw_input_field('navbar_title', $detail['navbar_title']); ?></td> 
              </tr> 
              <tr> 
                <td class="main"><?php echo TEXT_DETAIL_HEADING_TITLE; ?></td> 
                <td><?php echo tep_draw_input_field('heading_title', $detail['heading_title']); ?></td> 
              </tr> 
              <tr> 
                <td class="main" valign="top"><?php echo TEXT_DETAIL_CONTENTS; ?></td> 
                <td><?php echo tep_draw_textarea_field('text_information', 'soft', '70', '20', stripslashes($detail['text_information'])); ?></td> 
              </tr> 
              <tr> 
                <td colspan="2" align="right"><?php echo tep_image_submit('button_save.gif', IMAGE_INSERT) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CONTENTS, 'cID=' . $cID . '&page=' . $HTTP_GET_VARS['page']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td> 
              </tr> 
            </table> 
            <?php echo tep_draw_hidden_field('cID', $cID); ?> 
			<?php echo tep_draw_hidden_field('page', htmlspecialchars($HTTP_GET_VARS['page'])); ?>
            </form> </td> 
        </tr> 
<?php
  } elseif ($HTTP_GET_VARS['action'] == 'insert') {
?>
        <tr> 
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td> 
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td> <?php echo tep_draw_form('update', FILENAME_CONTENTS, 'act=insert'); ?> 
            <table border="0" cellspacing="0" cellpadding="5"> 
              <tr> 
                <td class="main"><?php echo TEXT_DETAIL_STATUS; ?></td> 
            	<td class="main"><?php echo tep_draw_radio_field('status', '1', true) . '&nbsp;' . TEXT_PRODUCT_AVAILABLE . '&nbsp;' . tep_draw_radio_field('status', '0', false) . '&nbsp;' . TEXT_PRODUCT_NOT_AVAILABLE; ?></td>
              </tr> 
              <tr> 
                <td class="main"><?php echo TEXT_DETAIL_SORT; ?></td> 
                <td><?php echo tep_draw_input_field('sort_id', ''); ?></td> 
              </tr> 			  			
              <tr> 
                <td class="main"><?php echo TEXT_DETAIL_ROMAJI; ?></td> 
                <td>
                <?php echo tep_draw_input_field('romaji', ''); ?>
                <?php
                if (isset($error_message)) {
                  echo $error_message; 
                }
                ?>
                </td> 
              </tr> 			  			
              <tr> 
                <td class="main"><?php echo TEXT_DETAIL_NAVBAR_TITLE; ?></td> 
                <td><?php echo tep_draw_input_field('navbar_title', ''); ?></td> 
              </tr> 
              <tr> 
                <td class="main"><?php echo TEXT_DETAIL_HEADING_TITLE; ?></td> 
                <td><?php echo tep_draw_input_field('heading_title', ''); ?></td> 
              </tr> 
              <tr> 
                <td class="main" valign="top"><?php echo TEXT_DETAIL_CONTENTS; ?></td> 
                <td><?php echo tep_draw_textarea_field('text_information', 'soft', '70', '20', ''); ?></td> 
              </tr> 
              <tr> 
                <td colspan="2" align="right"><?php echo tep_image_submit('button_save.gif', IMAGE_INSERT) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CONTENTS, 'cID=' . $cID . '&page=' . $HTTP_GET_VARS['page']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>'; ?></td> 
              </tr> 
            </table> 
            </form> </td> 
        </tr> 
<?php
  } else {
  	  $cID = trim($HTTP_GET_VARS['cID']);
?> 
        <tr> 
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td> 
                <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                    <tr class="dataTableHeadingRow"> 
                      <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CONTENTS_TITLE; ?></td> 
                      <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_CONTENTS_STATUS; ?></td> 
                      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_CONTENTS_SORT; ?></td> 
                      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td> 
                    </tr> 
                    <?php
    $search = '';
	$count = 0;
    $contents_query_raw = "select * from ".TABLE_INFORMATION_PAGE." order by sort_id, heading_title";
    $contents_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $contents_query_raw, $contents_query_numrows);
    $contents_query = tep_db_query($contents_query_raw);
    while ($contents = tep_db_fetch_array($contents_query)) {

	  $count++;
	  if ( ($contents['pID'] == $cID || (!$HTTP_GET_VARS['cID'] && $count == 1)) ) {
        echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CONTENTS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $contents['pID'] . '&action=edit') . '\'">' . "\n";
        if(!$HTTP_GET_VARS['cID']) {
		  $cID = $contents['pID'];
		}
	  } else {
        echo '          <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CONTENTS, tep_get_all_get_params(array('cID')) . 'cID=' . $contents['pID']) . '\'">' . "\n";
      }
?> 
                    <td class="dataTableContent"><?php echo htmlspecialchars($contents['heading_title']); ?></td> 
                      <td class="dataTableContent" align="center">
					  <?php
						  if ($contents['status'] == '1') {
							echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_CONTENTS, 'act=setflag&flag=0&cID=' . $contents['pID']) . '&page='.$HTTP_GET_VARS['page'].'">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
						  } else {
							echo '<a href="' . tep_href_link(FILENAME_CONTENTS, 'act=setflag&flag=1&cID=' . $contents['pID']) . '&page='.$HTTP_GET_VARS['page'].'">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
						  }
					  ?></td>
					  <td class="dataTableContent" align="right"><?php echo htmlspecialchars($contents['sort_id']); ?></td> 
                      <td class="dataTableContent" align="right"><?php if ( ($contents['pID'] == $cID) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_CONTENTS, tep_get_all_get_params(array('cID')) . 'cID=' . $customers['customers_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?> 
&nbsp;</td> 
                    </tr> <?php
    }
?> 
                    <tr> 
                      <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                          <tr> 
                            <td class="smallText" valign="top"><?php echo $contents_split->display_count($contents_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_CONTENS); ?></td> 
                            <td class="smallText" align="right"><?php echo $contents_split->display_links($contents_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></td> 
                          </tr> 
                        </table></td> 
                    </tr> 
					<tr>
					  <td><a href="<?php echo tep_href_link(FILENAME_CONTENTS, 'action=insert'); ?>"><?php echo tep_image_button('button_insert.gif', IMAGE_INSERT); ?></a></td>
					</tr>
                  </table></td> 
                <?php
  if($cID && tep_not_null($cID)) {
	$cquery = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '".$cID."'");
	$cresult = tep_db_fetch_array($cquery);
	$c_title = $cresult['heading_title'];
  } else {
	$c_title = '&nbsp;';
  }
    
  $heading = array();
  $contents = array();
  switch ($HTTP_GET_VARS['action']) {
	case 'confirm':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CONTENTS . '</b>');

      $contents = array('form' => tep_draw_form('contents', FILENAME_CONTENTS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cID . '&act=deleteconfirm'));
      $contents[] = array('text' => TEXT_DELETE_INTRO . '<br><br><b>' . $c_title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CONTENTS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cID) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      break;
    default:
      if ($cID && tep_not_null($cID)) {
		$heading[] = array('text' => '<b>' . $c_title . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<br>このページのリンクを表示させるには以下のソースコードを表示したい箇所にコピーしてください。<br>'.tep_draw_textarea_field('link','soft',30,5,'<a href="'.tep_catalog_href_link('page.php','pID='.$HTTP_GET_VARS['cID']).'">'.$c_title.'</a>').'<br><a href="' . tep_href_link(FILENAME_CONTENTS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cID . '&action=edit') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_CONTENTS, tep_get_all_get_params(array('cID', 'action')) . 'cID=' . $cID . '&action=confirm') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
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
