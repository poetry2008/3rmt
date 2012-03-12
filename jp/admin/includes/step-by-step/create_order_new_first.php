<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<?php require('includes/step-by-step/form_check.js.php'); ?>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="javascript" src="includes/javascript/address_search.js"></script>

<script type="text/javascript">

/*
function fee(address_value){

  var address = document.getElementById("list_option7").value;
  var country = document.getElementById("list_option4").value;
 
  if(address_value != ''){

    address = address_value;
  }
  $.ajax({
       url: 'address_fee_ajax.php',
       data: {country:country,address:address,weight:<?php echo $cart->weight; ?>},
       type: 'POST',
       dataType: 'text',
       async : false,
       success: function(data){
           $("#address_fee").html(''); 
           $("#address_fee").html(data);
       }
    }); 
 }
  $(document).ready(function(){
   
    fee();

  });
 */

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
  echo '<p class=main>'.CREATE_ORDER_SEARCH_TEXT.'<br>'.CREATE_ORDER_EMAIL_TEXT.'&nbsp;<input type="text" value="'.$lastemail.'" name="Customer_mail" size="40">'.tep_site_pull_down_menu('', false).'&nbsp;&nbsp;<input type="submit" value="'.CREATE_ORDER_SEARCH_BUTTON_TEXT.'"></p>' . "\n";
  echo '</form>' . "\n";
?>
  <br>
  <?php 
  }
  echo tep_draw_form('create_order', FILENAME_CREATE_ORDER_PROCESS, '', 'post', '', '') ;
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
        <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT, '', 'SSL') . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?></td> <td class="main" align="right"><?php echo tep_html_element_submit(IMAGE_CONFIRM); ?></td>
      </tr>
    </table>
  </form>
  </td>
  </tr>
</table>
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
