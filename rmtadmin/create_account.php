<?php
/*
	JP、GM共通ファイル
*/

  require('includes/application_top.php');
  require('includes/step-by-step/new_application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/step-by-step/' . FILENAME_CREATE_ACCOUNT);

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
  <title><?php echo TITLE ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<?php require('includes/step-by-step/form_check.js.php'); ?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
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
    <td width="100%" valign="top">
	<br>
	<?php echo tep_draw_form('account_edit', FILENAME_CREATE_ACCOUNT_PROCESS, 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
		<td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
      </tr>
      <tr>
        <td class="main"><br>ゲストユーザーを作成します。会員はお客様自身でなければ作れません。<br><br></td>
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
        <td align="right" class="main"><br><?php echo tep_image_submit('button_insert.gif', IMAGE_INSERT); ?></td>
      </tr>
    </table></form></td>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
    </table></td>
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