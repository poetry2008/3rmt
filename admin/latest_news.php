<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 Will Mays

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if ($HTTP_GET_VARS['action']) {
    switch ($HTTP_GET_VARS['action']) {
      case 'setflag': //set the status of a news item.
        if ( ($HTTP_GET_VARS['flag'] == '0') || ($HTTP_GET_VARS['flag'] == '1') ) {
          if ($HTTP_GET_VARS['latest_news_id']) {
            tep_db_query("update " . TABLE_LATEST_NEWS . " set status = '" . $HTTP_GET_VARS['flag'] . "' where news_id = '" . $HTTP_GET_VARS['latest_news_id'] . "'");
          }
        }

        tep_redirect(tep_href_link(FILENAME_LATEST_NEWS));
        break;
      case 'setfirst':
        if ($HTTP_GET_VARS['isfirst'] == '0') {
          if ($HTTP_GET_VARS['latest_news_id']) {
            tep_db_query("update " . TABLE_LATEST_NEWS . " set isfirst = '" . $HTTP_GET_VARS['isfirst'] . "' where news_id = '" . $HTTP_GET_VARS['latest_news_id'] . "'");
          }
        }
        if ($HTTP_GET_VARS['isfirst'] == '1') {
          $listAllQuery = tep_db_query("select * from " . TABLE_LATEST_NEWS . " where isfirst = '1'");
          $listAllRes = tep_db_fetch_array($listAllQuery);
          if (!$listAllRes) {
            if ($HTTP_GET_VARS['latest_news_id']) {
              tep_db_query("update " . TABLE_LATEST_NEWS . " set isfirst = '" . $HTTP_GET_VARS['isfirst'] . "' where news_id = '" . $HTTP_GET_VARS['latest_news_id'] . "'");
            }
          } else {
            tep_db_query("update " . TABLE_LATEST_NEWS . " set isfirst = '0' where news_id = '" . $listAllRes['news_id'] . "'");
            if ($HTTP_GET_VARS['latest_news_id']) {
              tep_db_query("update " . TABLE_LATEST_NEWS . " set isfirst = '" . $HTTP_GET_VARS['isfirst'] . "' where news_id = '" . $HTTP_GET_VARS['latest_news_id'] . "'");
            }
          }
        }
        tep_redirect(tep_href_link(FILENAME_LATEST_NEWS));
        break;
      case 'delete_latest_news_confirm': //user has confirmed deletion of news article.
        if ($HTTP_POST_VARS['latest_news_id']) {
          $latest_news_id = tep_db_prepare_input($HTTP_POST_VARS['latest_news_id']);
          tep_db_query("delete from " . TABLE_LATEST_NEWS . " where news_id = '" . tep_db_input($latest_news_id) . "'");
        }

        tep_redirect(tep_href_link(FILENAME_LATEST_NEWS));
        break;

      case 'insert_latest_news': //insert a new news article.
        if ($HTTP_POST_VARS['headline']) {
          $sql_data_array = array('headline'   => tep_db_prepare_input($HTTP_POST_VARS['headline']),
                                  'content'    => tep_db_prepare_input($HTTP_POST_VARS['content']),
		  						  'news_image' => tep_db_prepare_input($HTTP_POST_VARS['news_image']),
								  'news_image_description' => tep_db_prepare_input($HTTP_POST_VARS['news_image_description']),
                                  'date_added' => 'now()', //uses the inbuilt mysql function 'now'
                                  'status'     => '1' );

          tep_db_perform(TABLE_LATEST_NEWS, $sql_data_array);
          $news_id = tep_db_insert_id(); //not actually used ATM -- just there in case
        }
		
        $news_image = tep_get_uploaded_file('news_image');
        if (!empty($news_image['name'])) {
          $pic_rpos = strrpos($news_image['name'], ".");
          $pic_ext = substr($news_image['name'], $pic_rpos+1);
          $news_image_name = 'news'.time().".".$pic_ext;
          $news_image['name'] = $news_image_name; 
        } else {
          $news_image_name = ''; 
        }
        $image_directory = tep_get_local_path(DIR_FS_CATALOG_IMAGES . 'news/');
		$path = 'news/';
		
		if (is_uploaded_file($news_image['tmp_name'])) {
          tep_db_query("update " . TABLE_LATEST_NEWS . " set news_image = '" . $path . $news_image_name . "' where news_id = '" . $news_id . "'");
          tep_copy_uploaded_file($news_image, $image_directory);
        }
				
        tep_redirect(tep_href_link(FILENAME_LATEST_NEWS));
        break;

      case 'update_latest_news': //user wants to modify a news article.
	  
        if($HTTP_GET_VARS['latest_news_id']) {
          $sql_data_array = array('headline' => tep_db_prepare_input($HTTP_POST_VARS['headline']),
		  						  //'news_image' => (($HTTP_POST_VARS['news_image'] == 'none') ? '' : tep_db_prepare_input($HTTP_POST_VARS['news_image'])),
		  						  'news_image_description' => tep_db_prepare_input($HTTP_POST_VARS['news_image_description']),
                                  'content'  => tep_db_prepare_input($HTTP_POST_VARS['content']) );
                                  
          tep_db_perform(TABLE_LATEST_NEWS, $sql_data_array, 'update', "news_id = '" . tep_db_prepare_input($HTTP_GET_VARS['latest_news_id']) . "'");
        }
        $news_image = tep_get_uploaded_file('news_image');
        if (!empty($news_image['name'])) {
          $pic_rpos = strrpos($news_image['name'], ".");
          $pic_ext = substr($news_image['name'], $pic_rpos+1);
          $news_image_name = 'news'.time().".".$pic_ext;
          $news_image['name'] = $news_image_name; 
        } else {
          $news_image_name = ''; 
        }
        $image_directory = tep_get_local_path(DIR_FS_CATALOG_IMAGES . 'news/');
		$path = 'news/';
		
		if (is_uploaded_file($news_image['tmp_name'])) {
		  tep_db_query("update " . TABLE_LATEST_NEWS . " set news_image = '"
                      . $path . $news_image_name . "' where news_id = '" . $HTTP_GET_VARS['latest_news_id'] . "'");
          tep_copy_uploaded_file($news_image, $image_directory);
        }
        tep_redirect(tep_href_link(FILENAME_LATEST_NEWS));
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
  if ($HTTP_GET_VARS['action'] == 'new_latest_news') { //insert or edit a news item
    if ( isset($HTTP_GET_VARS['latest_news_id']) ) { //editing exsiting news item
      $latest_news_query = tep_db_query("select news_id, headline, content, news_image, news_image_description from " . TABLE_LATEST_NEWS . " where news_id = '" . $HTTP_GET_VARS['latest_news_id'] . "'");
      $latest_news = tep_db_fetch_array($latest_news_query);
	  
	  $nInfo = new objectInfo($latest_news);
    } else { //adding new news item
      $latest_news = array();
    }
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo TEXT_NEW_LATEST_NEWS; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr><?php echo tep_draw_form('new_latest_news', FILENAME_LATEST_NEWS, isset($HTTP_GET_VARS['latest_news_id']) ? 'latest_news_id=' . $HTTP_GET_VARS['latest_news_id'] . '&action=update_latest_news' : 'action=insert_latest_news', 'post', 'enctype="multipart/form-data"'); ?>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><?php echo TEXT_LATEST_NEWS_HEADLINE; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_input_field('headline', $latest_news['headline'], '', true); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_LATEST_NEWS_CONTENT; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_textarea_field('content', 'soft', '70', '15', stripslashes($latest_news['content'])); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_LATEST_NEWS_IMAGE; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_file_field('news_image') . '<br>' . tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . $latest_news['news_image'] . tep_draw_hidden_field('news_image', $latest_news['news_image']); ?></td>
          </tr>		  
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_LATEST_NEWS_IMAGE_DESCRIPTION; ?></td>
            <td class="main"><?php echo tep_draw_separator('pixel_trans.gif', '24', '15') . '&nbsp;' . tep_draw_textarea_field('news_image_description', 'soft', '70', '7', stripslashes($latest_news['news_image_description'])); ?></td>
          </tr>		  
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td class="main" align="right">
          <?php
            isset($HTTP_GET_VARS['latest_news_id']) ? $cancel_button = '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $HTTP_GET_VARS['latest_news_id']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>' : $cancel_button = '';
            echo tep_image_submit('button_insert.gif', IMAGE_INSERT) . $cancel_button;
          ?>
        </td>
		
      </form></tr>
<?php

  } else {
?>
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LATEST_NEWS_HEADLINE; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LATEST_NEWS_STATUS; ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LATEST_NEWS_ISFIRST; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_LATEST_NEWS_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $rows = 0;

    $latest_news_count = 0;
    $latest_news_query = tep_db_query('select news_id, headline, content, status, news_image, news_image_description from ' . TABLE_LATEST_NEWS . ' order by date_added desc');
    
    while ($latest_news = tep_db_fetch_array($latest_news_query)) {
      $latest_news_count++;
      $rows++;
      
      if ( ((!$HTTP_GET_VARS['latest_news_id']) || (@$HTTP_GET_VARS['latest_news_id'] == $latest_news['news_id'])) && (!$selected_item) && (substr($HTTP_GET_VARS['action'], 0, 4) != 'new_') ) {
        $selected_item = $latest_news;
      }
      if ( (is_array($selected_item)) && ($latest_news['news_id'] == $selected_item['news_id']) ) {
        echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $latest_news['news_id']) . '\'">' . "\n";
      } else {
        echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $latest_news['news_id']) . '\'">' . "\n";
      }
?>
                <td class="dataTableContent"><?php echo '&nbsp;' . $latest_news['headline']; ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($latest_news['status'] == '1') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'action=setflag&flag=0&latest_news_id=' . $latest_news['news_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'action=setflag&flag=1&latest_news_id=' . $latest_news['news_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
                <td class="dataTableContent" align="center">
                <?php
$listAllQuery = tep_db_query("select * from " . TABLE_LATEST_NEWS . " where isfirst = '1'");
$listAllRes = tep_db_fetch_array($listAllQuery);
if ($listAllRes) {
  if ($latest_news['news_id'] == $listAllRes['news_id']) {
    echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'action=setfirst&isfirst=0&latest_news_id=' . $latest_news['news_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
  } else {
        //echo tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
        echo '<a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'action=setfirst&isfirst=1&latest_news_id=' . $latest_news['news_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
  }
} else {
        echo '<a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'action=setfirst&isfirst=1&latest_news_id=' . $latest_news['news_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
}
                ?>
                </td>
                <td class="dataTableContent" align="right"><?php if ($latest_news['news_id'] == $HTTP_GET_VARS['latest_news_id']) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $latest_news['news_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }

?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo '<br>' . TEXT_NEWS_ITEMS . '&nbsp;' . $latest_news_count; ?></td>
                    <td align="right" class="smallText"><?php echo '&nbsp;<a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'action=new_latest_news') . '">' . tep_image_button('button_new_news_item.gif', IMAGE_NEW_NEWS_ITEM) . '</a>'; ?>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
    $heading = array();
    $contents = array();
    switch ($HTTP_GET_VARS['action']) {
      case 'delete_latest_news': //generate box for confirming a news article deletion
        $heading[] = array('text'   => '<b>' . TEXT_INFO_HEADING_DELETE_ITEM . '</b>');
        
        $contents = array('form'    => tep_draw_form('news', FILENAME_LATEST_NEWS, 'action=delete_latest_news_confirm') . tep_draw_hidden_field('latest_news_id', $HTTP_GET_VARS['latest_news_id']));
        $contents[] = array('text'  => TEXT_DELETE_ITEM_INTRO);
        $contents[] = array('text'  => '<br><b>' . $selected_item['headline'] . '</b>');
        
        $contents[] = array('align' => 'center',
                            'text'  => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $selected_item['news_id']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
        break;

      default:
        if ($rows > 0) {
          if (is_array($selected_item)) { //an item is selected, so make the side box
            $heading[] = array('text' => '<b>' . $selected_item['headline'] . '</b>');

            $contents[] = array('align' => 'center', 
                                'text' => '<a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $selected_item['news_id'] . '&action=new_latest_news') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_LATEST_NEWS, 'latest_news_id=' . $selected_item['news_id'] . '&action=delete_latest_news') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
            $contents[] = array('text' => '<br>' . $selected_item['content']);
          }
        } else { // create category/product info
          $heading[] = array('text' => '<b>' . EMPTY_CATEGORY . '</b>');

          $contents[] = array('text' => sprintf(TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS, $parent_categories_name));
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
