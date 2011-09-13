<?php
  require('includes/application_top.php');
$sort_str = '';
if (isset($_GET['sort'])&&$_GET['sort']){
$sort_str = '&sort='.$_GET['sort'];
}
if (isset($_GET['action']) and $_GET['action']) {
    switch ($_GET['action']) {
      case 'insert':
        $tags_name = tep_db_prepare_input($_POST['tags_name']);

        $t_query = tep_db_query("select * from ". TABLE_TAGS . " where tags_name = '" . $tags_name . "'");
        $t_res = tep_db_fetch_array($t_query);
        if ($t_res) {
          $messageStack->add_session(TEXT_TAGS_NAME_EXISTS, 'error');
          tep_redirect(tep_href_link(FILENAME_TAGS, 'cPath=&action=new'.$sort_str));
        }
    
    $tags_images = tep_get_uploaded_file('tags_images');

        //$image_directory = tep_get_local_path(DIR_FS_CATALOG_IMAGES) . DIRECTORY_SEPARATOR . 'tags' . DIRECTORY_SEPARATOR;
        $image_directory = tep_get_local_path(tep_get_upload_dir().'tags/');
    if (is_uploaded_file($tags_images['tmp_name'])) {
          tep_copy_uploaded_file($tags_images, $image_directory);
        }

        tep_db_query("insert into " . TABLE_TAGS . " (tags_checked, tags_images, tags_name) values ('0', '" . (isset($tags_images['name']) ? 'tags/'.$tags_images['name'] : '') . "', '" . tep_db_input($tags_name) . "')");
        if($sort_str){
        tep_redirect(tep_href_link(FILENAME_TAGS.'?'.$sort_str));
        }else{
        tep_redirect(tep_href_link(FILENAME_TAGS));
        }
        break;
      case 'save':
        $tags_id = tep_db_prepare_input($_GET['cID']);
        $tags_name = tep_db_prepare_input($_POST['tags_name']);
        
        $t_query = tep_db_query("select * from ". TABLE_TAGS . " where tags_name = '" . $tags_name . "'");
        $t_res = tep_db_fetch_array($t_query);
        if ($t_res && $t_res['tags_id'] != $tags_id) {
          $messageStack->add_session(TEXT_TAGS_NAME_EXISTS, 'error');
          tep_redirect(tep_href_link(FILENAME_TAGS, 'cPath=&action=new'));
        }

        if(isset($_POST['delete_image']) && $_POST['delete_image']){
          //unlink(DIR_FS_CATALOG_IMAGES . $t_res['tags_images']);
          unlink(tep_get_upload_dir(). $t_res['tags_images']);
          tep_db_query("update " . TABLE_TAGS . " set tags_images = '' where tags_id = '" . tep_db_input($tags_id) . "'");
        }

        $tags_image = tep_get_uploaded_file('tags_images');

        //$image_directory = tep_get_local_path(DIR_FS_CATALOG_IMAGES) . DIRECTORY_SEPARATOR . 'tags' . DIRECTORY_SEPARATOR;
        $image_directory = tep_get_local_path(tep_get_upload_dir().'tags/');
        if (is_uploaded_file($tags_image['tmp_name'])) {
          tep_copy_uploaded_file($tags_image, $image_directory);
        }

        tep_db_query("update " . TABLE_TAGS . " set " . (isset($tags_image['name']) && $tags_image['name'] ? "tags_images = 'tags/" . tep_db_input($tags_image['name'])."', " : '') . " tags_name = '" . tep_db_input($tags_name) . "' where tags_id = '" . tep_db_input($tags_id) . "'");
        tep_redirect(tep_href_link(FILENAME_TAGS, 'page=' . $_GET['page'] . '&cID=' . $tags_id.$sort_str));
        break;
      case 'deleteconfirm':
        $tags_id = tep_db_prepare_input($_GET['cID']);
        //unlink();
        tep_db_query("delete from " . TABLE_TAGS . " where tags_id = '" . tep_db_input($tags_id) . "'");
        tep_db_query("delete from " . TABLE_PRODUCTS_TO_TAGS . " where tags_id = '" . tep_db_input($tags_id) . "'");
        tep_redirect(tep_href_link(FILENAME_TAGS, 'page=' . $_GET['page'].$sort_str));
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
<!-- header //-->
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
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
              <tr>
              <td colspan='2' align="right">
                 <select onchange="if(options[selectedIndex].value) change_sort_type(options[selectedIndex].value)">
                 <?php if(!isset($_GET['sort'])){ ?>
                    <option selected="" value="4a"><?php echo LISTING_TITLE_A_TO_Z;?></option> <option value="4d"><?php echo LISTING_TITLE_Z_TO_A;?></option>
                    <option value="5a"><?php echo LISTING_TITLE_A_TO_N;?></option>
                    <option value="5d"><?php echo LISTING_TITLE_N_TO_A;?></option>
                    <?php }else{ 
                    if($_GET['sort']=='4a'){
                      echo '<option selected="" value="4a">'.LISTING_TITLE_A_TO_Z.'</option>';
                    }else{
                      echo '<option value="4a">'.LISTING_TITLE_A_TO_Z.'</option>';
                    }
                    if($_GET['sort']=='4d'){
                      echo '<option selected="" value="4d">'.LISTING_TITLE_Z_TO_A.'</option>';
                    }else{
                      echo '<option value="4d">'.LISTING_TITLE_Z_TO_A.'</option>';
                    } if($_GET['sort']=='5a'){
                      echo '<option selected="" value="5a">'.LISTING_TITLE_A_TO_N.'</option>';
                    }else{
                      echo '<option value="5a">'.LISTING_TITLE_A_TO_N.'</option>';
                    }
                    if($_GET['sort']=='5d'){
                      echo '<option selected="" value="5d">'.LISTING_TITLE_N_TO_A.'</option>';
                    }else{
                      echo '<option value="5d">'.LISTING_TITLE_N_TO_A.'</option>';
                    }
                    }
                    ?>
                 </select>
              </td>
              </tr>
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAGS_NAME; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  //echo MAX_DISPLAY_SEARCH_RESULTS;
  $tags_query_raw = "
  select t.tags_id, t.tags_name, t.tags_images, t.tags_checked 
  from " . TABLE_TAGS . " t order by t.tags_order,t.tags_name";
  if(isset($_GET['sort'])&&$_GET['sort']){
    $tags_query_raw = "
      select t.tags_id, t.tags_name, t.tags_images, t.tags_checked
      from " . TABLE_TAGS ." t ";
    switch($_GET['sort']){
      case '4a':
        $tags_query_raw .=' order by t.tags_name asc'; 
        break;
      case '4d':
        $tags_query_raw .=' order by t.tags_name desc'; 
        break;
      case '5a':
        $tags_query_raw .=' order by t.tags_name asc'; 
        break;
      case '5d':
        $tags_query_raw .=' order by t.tags_name desc'; 
        break;
    }
  }
  $tags_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $tags_query_raw, $tags_query_numrows);
  $tags_query = tep_db_query($tags_query_raw);
  while ($tags = tep_db_fetch_array($tags_query)) {
      if (( (!@$_GET['cID']) || (@$_GET['cID'] == $tags['tags_id'])) && (!@$cInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($tags);
    }

    if (isset($cInfo) && (is_object($cInfo)) && ($tags['tags_id'] == $cInfo->tags_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_TAGS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->tags_id . '&action=edit'.$sort_str) . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_TAGS, 'page=' . $_GET['page'] . '&cID=' . $tags['tags_id'].$sort_str) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $tags['tags_name']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( isset($cInfo) && (is_object($cInfo)) && ($tags['tags_id'] == $cInfo->tags_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); } else { echo '<a href="' . tep_href_link(FILENAME_TAGS, 'page=' . $_GET['page'] . '&cID=' . $tags['tags_id'].$sort_str) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $tags_split->display_count($tags_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAGS); ?></td>
                    <?php if(isset($_GET['sort'])&&$_GET['sort']){ ?>
                    <td class="smallText" align="right"><?php echo $tags_split->display_links($tags_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],'sort='.$_GET['sort']); ?></td>
                    <?php }else{ ?>
                    <td class="smallText" align="right"><?php echo $tags_split->display_links($tags_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                     <?php }?>
                  </tr>
<?php
        if (!isset($_GET['action'])) {
//  if (!$_GET['action']) {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a href="' .  tep_href_link(FILENAME_TAGS, 'page=' . $_GET['page'] .  '&action=new'.$sort_str) . '">' .  tep_html_element_button(BUTTON_NEW_TAG) . '</a>'; ?></td>
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
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_TAG . '</b>');

      $contents = array('form' => tep_draw_form('tags', FILENAME_TAGS, 'page=' . $_GET['page'] . '&action=insert'.$sort_str, 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_TAGS_NAME . '<br>' . tep_draw_input_field('tags_name'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_TAGS_IMAGE . '<br>' . tep_draw_file_field('tags_images')) ;
      //$contents[] = array('text' => '<br>' . TEXT_INFO_TAGS_IMAGE . '<br>' . tep_draw_input_field('tags_images'));
      $contents[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_INSERT) . '&nbsp;<a href="' .  tep_href_link(FILENAME_TAGS, 'page=' . $_GET['page'].$sort_str) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_TAG . '</b>');

      $contents = array('form' => tep_draw_form('tags', FILENAME_TAGS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->tags_id . '&action=save'.$sort_str, 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_TAGS_NAME . '<br>' . tep_draw_input_field('tags_name', $cInfo->tags_name));
      $contents[] = array('text' => '<br>' . TEXT_INFO_TAGS_IMAGE . '<br>' . tep_draw_file_field('tags_images')) ;
      
      //if(!is_dir(DIR_FS_CATALOG_IMAGES.$cInfo->tags_images) && file_exists(DIR_FS_CATALOG_IMAGES.$cInfo->tags_images)) {
      if(!is_dir(tep_get_upload_dir().$cInfo->tags_images) && file_exists(tep_get_upload_dir().$cInfo->tags_images)) {
        $contents[] = array('text' => '<br>' . tep_image(DIR_WS_CATALOG_IMAGES . $cInfo->tags_images, $cInfo->tags_name));
        $contents[] = array('text' => '<br><input type="checkbox" name="delete_image" value="1" >'.TEXT_CONFIRM_DELETE_TAG);
      }
      
      //$contents[] = array('text' => '<br>' . TEXT_INFO_TAGS_IMAGE . '<br>' . tep_image(DIR_WS_CATALOG_IMAGES . $cInfo->tags_images)) ;
      $contents[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_UPDATE) . '&nbsp;<a href="' .  tep_href_link(FILENAME_TAGS, 'page=' . $_GET['page'] . '&cID=' .  $cInfo->tags_id.$sort_str) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_TAG . '</b>');

      $contents = array('form' => tep_draw_form('tags', FILENAME_TAGS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->tags_id . '&action=deleteconfirm'.$sort_str));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $cInfo->tags_name . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' .  tep_html_element_submit(IMAGE_DELETE) . '&nbsp;<a href="' .  tep_href_link(FILENAME_TAGS, 'page=' . $_GET['page'] . '&cID=' .  $cInfo->tags_id.$sort_str) . '">' . tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
    default:
      if (is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . $cInfo->tags_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => 
          '<a href="' . tep_href_link(FILENAME_TAGS, 'page=' . $_GET['page'] .
          '&cID=' . $cInfo->tags_id . '&action=edit'.$sort_str) . '">' .
          tep_html_element_button(IMAGE_EDIT) . '</a>' . ($ocertify->npermission ==
            15 ? (' <a href="' . tep_href_link(FILENAME_TAGS, 'page=' .  $_GET['page'] . '&cID=' . $cInfo->tags_id . '&action=delete'.$sort_str) .  '">' . tep_html_element_button(IMAGE_DELETE) . '</a>'):'')
        );
        $contents[] = array('text' => '<br>' . TEXT_INFO_TAGS_NAME . '<br>' . $cInfo->tags_name . '<br>');
        if ($cInfo->tags_images) {
          $contents[] = array('text' => '<br>' . TEXT_INFO_TAGS_IMAGE . '<br>' . tep_image(tep_get_web_upload_dir(). $cInfo->tags_images) . '<br>');
        }
      }
      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td class="right_column_c" width="25%" valign="top">' . "\n";

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
