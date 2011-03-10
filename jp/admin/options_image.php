<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  if (isset($_GET['action'])) {
    switch ($_GET['action']) {
      case 'insert_image':
      foreach ($_FILES as $key => $value) {
        $site_str = substr($key, 1); 
        $image_directory = tep_get_local_path(tep_get_upload_dir($site_str).'op_image/'); 
        if (is_uploaded_file($value['tmp_name'])) {
          $insert_sql = "insert into products_options_image values('".$_GET['value_id']."', '".$site_str."', '".tep_db_prepare_input($value['name'])."')"; 
          mysql_query($insert_sql); 
          move_uploaded_file($value['tmp_name'], $image_directory. '/'.$value['name']);
        }
      }
      tep_redirect(tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES)); 
      break;
      case 'delete_image':
      $op_image_query = tep_db_query("select * from `products_options_image` where `products_options_values_id` = '".$_GET['value_id']."' and `site_id` = '".$_GET['site_id']."'"); 
      $op_image_res = tep_db_fetch_array($op_image_query); 
      if ($op_image_res) {
        $delete_sql = "delete from `products_options_image` where `products_options_values_id` = '".$_GET['value_id']."' and `site_id` = '".$_GET['site_id']."'"; 
        tep_db_query($delete_sql); 
        $image_path = DIR_FS_CATALOG_IMAGES.$_GET['site_id'].'/op_image/'.$op_image_res['option_image'];
        unlink($image_path); 
      }
      tep_redirect(tep_href_link('options_image.php', 'value_id='.$_GET['value_id'])); 
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
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
    
    </table>
    </td>
    <td>
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td class="pageHeading"><?php echo HEADING_IMAGE_TITLE?></td> 
        <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', '1', HEADING_IMAGE_HEIGHT);?></td> 
      </tr>
    </table>
    <?php
    $option_images_query = tep_db_query("select * from products_options_image where products_options_values_id = '".$_GET['value_id']."'"); 
    $image_arr = array(); 
    while ($option_images_res = tep_db_fetch_array($option_images_query)) {
      $image_arr[$option_images_res['site_id']] = $option_images_res['option_image']; 
    }
    echo tep_draw_form('option_image', 'options_image.php', 'action=insert_image&value_id='.$_GET['value_id'], 'post', 'enctype="multipart/form-data"'); 
    $site_query = tep_db_query("select * from ".TABLE_SITES); 
    echo '<table>'; 
    echo '<tr><td>all:</td><td>'.tep_draw_file_field('s0').'</td></tr>'; 
    if (isset($image_arr[0])) {
      echo '<tr><td colspan="2">';
      echo tep_image(tep_get_web_upload_dir(0).'op_image/'.$image_arr[0]); 
      echo '<br>'; 
      echo '<a href="'.tep_href_link('options_image.php', 'action=delete_image&value_id='.$_GET['value_id'].'&site_id=0').'">'.DELETE_IMAGE_TEXT.'</a>'; 
      echo '</td></tr>'; 
    }
    while ($site_res = tep_db_fetch_array($site_query)) {
      echo '<tr><td>'.$site_res['romaji'].':</td><td>'.tep_draw_file_field('s'.$site_res['id']).'</td></tr>'; 
      if (isset($image_arr[$site_res['id']])) {
        echo '<tr><td colspan="2">';
        echo tep_image(tep_get_web_upload_dir($site_res['id']).'op_image/'.$image_arr[$site_res['id']]); 
        echo '<br>'; 
        echo '<a href="'.tep_href_link('options_image.php', 'action=delete_image&value_id='.$_GET['value_id'].'&site_id='.$site_res['id']).'">'.DELETE_IMAGE_TEXT.'</a>'; 
        echo '</td></tr>'; 
      }
    }
    echo '<tr><td align="right">'; 
    echo '<a href="'.tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES).'">'.tep_image_button('button_back.gif', 'back').'</a></td>';
    echo '<td>'.tep_image_submit('button_insert.gif', IMAGE_INSERT); 
    echo '</td></tr>'; 
    echo '</table>'; 
    ?>
    </form> 
    </td>
  </tr>
</table>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
