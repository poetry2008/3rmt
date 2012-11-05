<?php
  require('includes/application_top.php');
  if(isset($_GET['from'])&&$_GET['from']){
    $from_url = $_GET['from'];
  }
  switch($_GET['action']){
    case 'rename':
     $siteurl = str_replace('_','.',$_GET['url']); 
     $site_sql = "select * from ".TABLE_SITENAME."
                  where siteurl='".$siteurl."'";
     $site_query = tep_db_query($site_sql);
     if(tep_db_num_rows($site_query)>0){
       $is_insert = false;
       $site_info = tep_db_fetch_array($site_query);
     }else{
       $is_insert = true;
     }
    break;
    case 'insert':
    $sql_data_array = array(
         'siteurl' => trim($_POST['siteurl']),
         'sitename' => trim($_POST['sitename']),
         );
    tep_db_perform(TABLE_SITENAME, $sql_data_array);
    tep_redirect(tep_href_link($from_url,
          'action='.$_GET['act'].'&cID='.$_GET['cID'].'&cPath='.$_GET['cPath']));
    break;
    case 'update':
    $sql_data_array = array(
         'siteurl' => trim($_POST['siteurl']),
         'sitename' => trim($_POST['sitename']),
         );
    tep_db_perform(TABLE_SITENAME, $sql_data_array,'update','id='.$_POST['id']);
    tep_redirect(tep_href_link($from_url,
          'action='.$_GET['act'].'&cID='.$_GET['cID'].'&cPath='.$_GET['cPath']));
    break;
  }
  
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
<title><?php echo TITLE; ?></title>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>

            <td class="pageHeading"><?php echo RECORD_TITLE_TEXT;?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <?php if($is_insert){?>
          <tr><FORM method="POST" action="<?php echo tep_href_link(FILENAME_RECORD,
          'from='.$from_url.'&action=insert&act='.$_GET['action'].'&cID='.$_GET['cID'].'&cPath='.$_GET['cPath']);?>" enctype="multipart/form-data">
            <td><table border="0" cellpadding="0" cellspacing="2">
            <tr>
            <td><?php echo SITEURL;?></td>
            <td><?php echo tep_draw_input_field('siteurl');?></td>
            </tr>
            <tr>
            <td><?php echo SITEURL_NAME;?></td>
            <td><?php echo tep_draw_input_field('sitename')?></td>
            </tr>
            <tr>
          <?php }else {?>
          <tr><FORM method="POST" action="<?php echo tep_href_link(FILENAME_RECORD,
          'from='.$from_url.'&action=update&act='.$_GET['action'].'&cID='.$_GET['cID'].'&cPath='.$_GET['cPath']);?>" enctype="multipart/form-data">
            <td><table border="0" cellpadding="0" cellspacing="2">
            <tr>
            <td><?php echo SITEURL;?></td>
            <td>
            <?php
            echo $site_info['siteurl'];
            echo tep_draw_hidden_field('id', $site_info['id']);
            echo tep_draw_hidden_field('siteurl', $site_info['siteurl']);
            ?>
            </td>
            </tr>
            <tr>
            <td><?php echo SITEURL_NAME;?></td>
            <td><?php echo tep_draw_input_field('sitename',$site_info['sitename'])?></td>
            </tr>
            <tr>
          <?php }?>
            <td>
            <?php
            echo tep_html_element_submit(IMAGE_SAVE);
            echo "&nbsp;&nbsp;";
            echo '</td><td>'; 
            if (isset($from_url)) { 
              echo '<a href="' .  tep_href_link($from_url,'action='.$_GET['act'].'&cID='.$_GET['cID'].'&cPath='.$_GET['cPath']) . '">';
            } else {
              echo '<a href="'.tep_href_link(FILENAME_RECORD).'">'; 
            }
            echo tep_html_element_button(IMAGE_CANCEL) . '</a>'
            ?>
            </td>
            </tr>
            </table></td>
          </form></tr>


<!-- body_text_eof -->
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- body_eof -->
<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php 
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
