<body>
<!-- header --> 
 <?php
require_once DIR_WS_INCLUDES . 'header.php'; 
?>
 
<!-- header_eof --> 
<!-- body --> 
<div id="main">
   <!-- left_navigation -->
   <div id="l_menu">
   <?php
   require_once DIR_WS_INCLUDES . 'column_left.php'; 
?>

</div>
<!-- left_navigation_eof -->
<!-- body_text -->
<div id="content">
   <?php
   echo tep_draw_form('checkout_payment', tep_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'), 'post', 'onsubmit="return check_form();"'); 
?>

<div class="headerNavigation">
   <?php
   echo $breadcrumb->trail(' &raquo; '); 
?>
</div>
<h1 class="pageHeading">
   <?php
   echo HEADING_TITLE ; 
?>
</h1>
<table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="0"> 
   <tr> 
   <td>
   <table border="0" width="100%" cellspacing="0" cellpadding="0" class="checkout_s_link"> 
   <tr> 
   <td width="20%">
   <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
   <tr> 
   <td width="50%" align="right">
   <?php
   echo tep_draw_separator('pixel_silver.gif', '1', '5'); 
?>
</td> 
<td width="50%">
   <?php
   echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
?>
</td> 
</tr> 
</table>
</td> 
<td width="20%">
   <?php
   echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
?>
</td> 
<td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
   <tr> 
   <td width="50%">
   <?php
   echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
?>
</td> 
<td>
 <?php
echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); 
?>
</td> 
<td width="50%">
   <?php
   echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
?>
</td> 
</tr> 
</table></td> 
<td width="20%">
   <?php
   echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
?>
</td> 
<td width="20%"><table border="0" width="100%" cellspacing="0" cellpadding="0"> 
   <tr> 
   <td width="50%">
   <?php
   echo tep_draw_separator('pixel_silver.gif', '100%', '1'); 
?>
</td> 
<td width="50%">
   <?php
   echo tep_draw_separator('pixel_silver.gif', '1', '5'); 
?>
</td> 
</tr> 
</table></td> 
</tr> 
<tr class="box_des"> 
   <td align="center" nowrap="nowrap" width="20%" class="checkoutBarFrom">
   <?php
   echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_OPTION, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_OPTION . '</a>'; 
?>
</td>
<td align="center" nowrap="nowrap" width="20%" class="checkoutBarFrom">
   <?php
   echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; 
?>
</td> 
<td align="center" nowrap="nowrap" width="20%" class="checkoutBarCurrent">
   <?php
   echo CHECKOUT_BAR_PAYMENT; 
?>
</td> 
<td align="center" nowrap="nowrap" width="20%" class="checkoutBarTo">
   <?php
   echo CHECKOUT_BAR_CONFIRMATION; 
?>
</td> 
<td align="center" nowrap="nowrap" width="20%" class="checkoutBarTo">
   <?php
   echo CHECKOUT_BAR_FINISHED; 
?>
</td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="c_pay_info"> 
   <tr> 
   <td class="main"><b>
   <?php
   echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><div style="margin-top:5px;">' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; 
?>
</div></td> 
<td class="main" align="right">
   <?php
   echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); 
?>
</td> 
</tr> 
</table>
</td> 
</tr> 
<tr> 
<td class="main03"><b>
   <?php
   echo TABLE_HEADING_PAYMENT_METHOD; 
?>
</b></td> 
</tr> 
<tr> 
<td>
 <?php tep_payment_out_selection();?>
</td> 
</tr> 
</table></td> 
</tr> 
      
<tr> 
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="2" class="box_des"> 
  <tr> 
  <td class="main03"><b>
  <?php
  echo TABLE_HEADING_COMMENTS; 
?>
</td> 
</tr> 
</table>
</td> 
</tr> 
<tr> 
<?php
//if($cart->show_total() >= 0) { 
if(true) { 
  ?>

  <td><table border="0" width="95%" cellspacing="1" cellpadding="2" class="formArea"> 
    <?php
    }else{ 
  ?>

  <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="formArea"> 
    <?php
    } 
?>

<tr> 
<td><table border="0" width="100%" cellspacing="0" cellpadding="2" class="box_des"> 
  <tr> 
  <td>
  <?php
  echo tep_draw_textarea_field('comments', 'soft', '50', '5', '','style="width:100%"') . tep_draw_hidden_field('comments_added', 'YES'); 
?>
</td> 
</tr> 
</table></td> 
</tr> 
</table></td> 
</tr> 
<tr> 
<td>

<?php

//if($cart->show_total() >= 0) {
if(true) {
  
  ?>

  <table border="0" width="95%" cellspacing="0" cellpadding="2" class="c_pay_info"> 
    <?php

    }else{
  
  ?>

  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="c_pay_info"> 
    <?php
 
    }

?>

<tr> 
<td class="main"><b>
  <?php
  echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><div style="margin-top:5px;">' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; 
?>
</div></td> 
<td class="main" align="right">
  <?php
  echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); 
?>
</td> 
</tr> 
</table>
</td> 
</tr> 
</table> 
</table>
</form>
</div>
<!-- body_text_eof --> 
<!-- right_navigation --> 
<div id="r_menu">
  <?php
  require(DIR_WS_INCLUDES . 'column_right.php'); 
?>
 
</div>
<!-- right_navigation_eof -->
<!-- body_eof --> 
<!-- footer --> 
<?php
require(DIR_WS_INCLUDES . 'footer.php'); 
?>
 
<!-- footer_eof --> 
</div> 
</body>
</html>
<?php
require(DIR_WS_INCLUDES . 'application_bottom.php'); 
?>

