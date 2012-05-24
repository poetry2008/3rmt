<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo CREATE_ORDER_TITLE_TEXT; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/jquery.autocomplete.css">
<?php require('includes/step-by-step/form_check.js.php'); ?>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/all_order.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="javascript" src="includes/javascript/address_search.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery.autocomplete.js"></script>

<script type="text/javascript">
function check_submit(){
  var fax = document.getElementById("fax");
  var fax_value = document.getElementById("fax_value");
  fax_value.value = fax.value;
  document.create_order_form_1.submit();
}

$(function() {
      function format(group) {
          return group.name;
      }
      $("#keyword").autocomplete('ajax_create_order.php?action=search_email', {
        multipleSeparator: '',
        dataType: "json",
        parse: function(data) {
        return $.map(data, function(row) {
            return {
             data: row,
             value: row.name,
             result: row.name
            }
          });
        },
        formatItem: function(item) {
          return format(item);
        }
      }).result(function(e, item) {
      });
});
</script>

</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<?php
if($order_exists == true){
?>
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
  <table border='0' bgcolor='#7c6bce' width='100%'>
      <tr>
        <td class="main"><font color="#ffffff"><b><?php echo CREATE_ORDER_STEP_ONE;?></b></font></td>
      </tr>
    </table>
  <?php 
  if ($_SERVER['PHP_SELF'] != '/admin/create_order_process.php') {
  ?>
  <p class="pageHeading"><?php echo CREATE_ORDER_TITLE_TEXT;?></p>
<?php
                                                                //显示用户查询表单
  echo '<form action="' . "create_order.php" . '" method="GET">' . "\n";
  if(isset($_GET['oID']) && $_GET['oID'] != ''){
    echo '<input type="hidden" name="oID" value="'.$_GET['oID'].'">';
  }
  echo '<p class=main>'.CREATE_ORDER_SEARCH_TEXT.'<br>'.CREATE_ORDER_EMAIL_TEXT.'&nbsp;<input type="text" value="'.$lastemail.'" id="keyword" name="Customer_mail" size="40">'.tep_site_pull_down_menu('', false).'&nbsp;&nbsp;<input type="submit" value="'.CREATE_ORDER_SEARCH_BUTTON_TEXT.'"></p>' . "\n";
  echo '</form>' . "\n";
?>
  <br>
  <?php 
  }
?>

  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading">
      <?php 
      if ($_SERVER['PHP_SELF'] == '/admin/create_order_process.php') {
        echo '<font color="red">'.CREATE_ORDER_RED_TITLE_TEXT.'</font>'; 
      } else {
        echo HEADING_CREATE; 
      }
      ?>
      </td>
    </tr>
    <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
  </table>

<?php
  //変数挿入
    require(DIR_WS_INCLUDES . 'step-by-step/create_order_details.php');
?>
  <br>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'SSL') . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td> <td class="main" align="right"><input type="button" value="<?php echo IMAGE_CONFIRM_NEXT; ?>" onclick="check_submit();"></td>
      </tr>
    </table>
  </td>
  </tr>
</table>
<?php
}
?>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<br>
</body>
</html>
<?php 
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>
