<?php
/*
   $Id$
*/

  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_CREATE_ACCOUNT);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
  <title><?php echo TITLE ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<?php require('includes/step-by-step/form_check.js.php'); ?>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
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
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
  <br>
  <?php echo tep_draw_form('account_edit', FILENAME_CREATE_ACCOUNT_PROCESS, 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?>
  <div class="compatible">
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
      </tr>
      <tr>
        <td class="main"><br><?php echo CREATE_ACCOUNT_HEAD_TEXT;?><br><br></td>
      </tr>
      <tr>
        <td>
<?php
  $laguage_to_country = array('english'=>223,'espanol'=>195,'german'=>81,'japanese'=>107);
  $account['entry_country_id'] = $laguage_to_country[$language] ? $laguage_to_country[$language] : STORE_COUNTRY;

  require(DIR_WS_INCLUDES . 'step-by-step/account_details.php');
?>
        </td>
      </tr>
      <tr>
        <td align="right" class="main"><br><?php echo
        tep_html_element_submit(IMAGE_SAVE); ?></td>
      </tr>
    </table>
    </div>
    </form>
    </td>
<!-- body_text_eof //-->
    </div>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php
    require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
