<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_WS_CLASSES . 'currencies.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
<title><?php echo TITLE; ?></title>
</head>
<body>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
    </script>
    <?php }?>
    <div class='header'>
    <?php
    require(DIR_WS_INCLUDES . 'header.php');
    ?>
    </div>
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
    <tr>
    <?php
    if ($ocertify->npermission >= 10) {
      echo '<td width="' . BOX_WIDTH . '" valign="top">';
      echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
      require(DIR_WS_INCLUDES . 'column_left.php');
      echo '</table>';
      echo '</td>';
    } else {
      echo '<td>&nbsp;</td>';
    }
?>
  <td width="100%" valign="top"><?php echo $notes;?>
</td>
</tr>
</table>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<br>
</body>
</html>
