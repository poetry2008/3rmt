<?php page_head();?>
<script type="text/javascript" src="./js/jquery-1.3.2.min.js">
  </script>
  <script type="text/javascript" src="./js/payment.js">
  </script>
  <?php
  //输出payment 的javascript验证
  if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true') 
    {
      echo $payment_modules->javascript_validation($point['point']); 
    }
?>
</head>
<body> 
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
  <tr> 
  <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
  <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
  <!-- left_navigation_eof //--> </td> 
  <!-- body_text //--> 
  <td valign="top" id="contents">
  <?php 
  echo tep_draw_form('checkout_payment', tep_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'), 'post', 'onsubmit="return check_form();"'); 
?>
<h1 class="pageHeading">
  <?php 
  echo HEADING_TITLE ; 
?>
</h1> 
<div> 
<table border="0" width="100%" cellspacing="0" cellpadding="0"> 
  <tr> 
  <td>
  <?php require(DIR_WS_INCLUDES . 'payment_nav_box.php'); ?> 
  </td> 
  </tr> 
  <tr>
  <td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
  <tr> 
  <td class="main">
  <b>
  <?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b>
				<br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?>
</td> 
<td class="main" align="right">
  <?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?>
  </td> 
  </tr> 
  </table>
  </td> 
  </tr> 
  <?php
		 
  if (isset($_GET['payment_error']) && ($error = $payment_modules->get_error($_GET['payment_error']))) {
    ?> 
    <tr> 
    <td>
    <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
    <tr> 
    <td class="main">
    <b>
    <?php 
    echo htmlspecialchars($error['title']); 
    ?>
    </b>
    </td> 
    </tr> 
    </table>
    </td> 
    </tr> 
    <tr> 
    <td>
    <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxNotice"> 
    <tr class="infoBoxNoticeContents"> 
    <td>
    <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
    <tr> 
    <td>
    </td> 
    <td class="main" width="100%" valign="top">
    <?php 
    echo htmlspecialchars($error['error']); 
    ?>
    </td> 
    <td>
    </td> 
    </tr> 
    </table>
    </td> 
    </tr> 
    </table>
    </td> 
    </tr> 
    <tr> 
    <td>
    <?php 
    echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
    </td> 
    </tr> 
    <?php
  }
?>
<tr> 
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="2"> 
  <tr> 
  <td class="main">
  <b>
  <?php echo TABLE_HEADING_PAYMENT_METHOD; ?>
  </b>
  </td> 
  </tr> 
  </table>
  </td> 
  </tr> 

  <tr> 
  <td>
<!-- selection start -->
<div class="checkout_payment_info">
	<table cellspacing="0" cellcpadding="0" border="0">
  <?php
    if (sizeof($selection) > 1) {
      echo "<tr>";
      echo "<td></td>";
      echo '<td class="main" width="50%" valign="top">'.TEXT_SELECT_PAYMENT_METHOD."</td>";
      echo '<td class="main" width="50%" valign="top" align="right"><b>'.TITLE_PLEASE_SELECT.'</b><br>'.tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif').'</td> ';
      echo "<td></td>";
      echo "</tr> ";
    }else {
      echo "<tr>";
      echo "<td></td>";
      echo '<td class="main" width="100%" colspan="2">';
      echo TEXT_ENTER_PAYMENT_INFORMATION;
      echo '</td><td></td>';
      echo "</tr>";
    }
  ?>
	</table>
  <!-- loop start  -->
<?php 
    foreach ($selection as $key=>$singleSelection){
      //判断支付范围 
      if($payment_modules->moneyInRange($singleSelection['id'],$order->info['total'])){
	continue;
      }
      if(!$payment_modules->showToUser($singleSelection['id'],$_SESSION['guestchk'])){
        continue;
      }
      
?>
	<table cellspacing="0" cellcpadding="0" border="0">
		<tr class="moduleRowSelected">
			<td><b><?php echo $singleSelection['module'];?></b></td>
			<td align="right">
                                                               <?php echo tep_draw_radio_field('payment',$singleSelection['id'] ,$_SESSION['payment']==$singleSelection['id']); ?>
</td>
		</tr>
		<tr>
			<td>
                <div class="cp_description"> <?php  echo $singleSelection['description'];?></div>
				<div class="cp_content">
                                                                                        <div style="display: none;"  class="rowHide rowHide_<?php echo $singleSelection['id'];?>">
                                                                                               <?php 
                                                                                               echo $singleSelection['fields_description']; 
                                                                                        foreach ($singleSelection['fields'] as $key2=>$field){

?>
                                                                                  
						<div>
<div class="payment_field_title"><?php echo $field['title'];?></div>
							<div class="payment_field_field">
<?php echo $field['field'];?>
<small><font color="#AE0E30"><?php echo $field['message'];?></font></small></div>
						</div>
<?php 
                                                                                        }
                                                                                        echo $singleSelection['footer'];
?>
				</div>
											<div class="cp_codefee"><?php echo $singleSelection['codefee'];?></div>

			</td>
		</tr>
	</table>
<?
    }
?>
<!-- loop end  -->
</div>

<!-- selection end -->

  </td> 
  </tr> 
  <tr> 
  <td>
  <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
  <tr class="infoBoxContents"> 
  <td>
  <br> 
  <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
  <tr>
  <td class="main">
  <b><?php echo TABLE_HEADING_COMMENTS;?></b> 
  </td> 
  </tr> 
  <tr> 
  <td>
  <?php echo tep_draw_textarea_field('comments', 'soft', '60', '5') . tep_draw_hidden_field('comments_added', 'YES'); ?>
  </td> 
  </tr> 
  </table>
  </td> 
  </tr> 
  </table>
  </td> 
  </tr> 
  <tr> 
  <td>
  <?php //echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
  </td> 
  </tr> 
  <?php
  if(MODULE_ORDER_TOTAL_POINT_STATUS == 'true' && $cart->show_total() > 0) {//point --  
    if($guestchk == '1') {
      echo '<input type="hidden" name="point" value="0">';
    } else if(tep_only_sell_product()) {
      ?> 
      <tr> 
      <td>
	  <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
      <tr> 
      <td class="main">
      <b>
      <?php echo TEXT_POINT; ?>
      </b>
      </td> 
      </tr> 
      </table>
      </td> 
      </tr> 
      <tr> 
      <td>
	  <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox"> 
      <tr class="infoBoxContents"> 
      <td>
      <table border="0" width="100%" cellspacing="0" cellpadding="2"> 
      <tr> 
      <td class="main">
      <input type="text" value="0" name="point" size="4" style="text-align:right"> 
      /<?php echo $point['point'] ?>
      </td> 
      </tr> 
      </table>
      </td> 
      </tr> 
      </table>
      </td> 
      </tr> 
      <tr> 
      <td>
	  <?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
      </td> 
      </tr> 
      <?php 
    }else{
      echo '<input type="hidden" name="point" value="0">';
    }
  }//point eof// 
?> 
<tr> 
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="0" class="c_pay_info"> 
  <tr> 
  <td class="main">
  <b>
  <?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b>
			<br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?>
</td> 
<td class="main" align="right">
  <?php echo tep_image_submit('button_continue_02.gif', IMAGE_BUTTON_CONTINUE); ?>
  </td> 
  </tr> 
  </table>
  </td> 
  </tr> 
  <tr> 
  <td>
  <?php //echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?>
  </td> 
  </tr> 
  </table> 
  </form> 
  </div>
  </td> 
  <!-- body_text_eof //--> 
  <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
  <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
  <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
  </div> 
  </body>
  </html>
  <?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
