<?php
/*
  $Id: contact_us.php,v 1.2 2003/03/12 13:32:37 hawk Exp $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
*/

	require('includes/application_top.php');
	
	require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CONTACT_US);
	
	$mail_text = '';
	
	//product_id�����������羦��̾��ƤӽФ�
	if (isset($HTTP_GET_VARS['products_id'])) {
		$product_info_query = tep_db_query("select pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "'");
		$product_info = tep_db_fetch_array($product_info_query);
	}
	
	//�־���̾�פˤĤ��ƤΤ��䤤��碌
	define('HEADING_TITLE_CONTACT', '�ˤĤ��ƤΤ��䤤��碌');
	define('TITLE_CONTACT_END', '�ˤĤ���');
	define('EMAIL_SEPARATOR', '-----------------------------------------------' . "\n\n");
	
	
	$error = false;
	if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'send')) {
		if (tep_validate_email(trim($HTTP_POST_VARS['email']))) {
			$mail_text .= $HTTP_POST_VARS['name'] . ' �ͤ��餪�䤤��碌�򾵤�ޤ�����' . "\n";
			$mail_text .= '�᡼�륢�ɥ쥹��' . $HTTP_POST_VARS['email'] . "\n\n";
			$mail_text .= $HTTP_POST_VARS['enquiry'];
			tep_mail(STORE_OWNER, SUPPORT_EMAIL_ADDRESS, EMAIL_SUBJECT, nl2br($mail_text), $HTTP_POST_VARS['name'], $HTTP_POST_VARS['email']);
			tep_redirect(tep_href_link(FILENAME_CONTACT_US, 'action=success'));
		} else {
			$error = true;
		}
	}
	
	//prouct_id������������ν�������̾����̾�ˤĤ��ƤΤ��䤤��碌���ѹ���
	if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'send_p')) {
		if (tep_validate_email(trim($HTTP_POST_VARS['email']))) {
			$mail_text .= $HTTP_POST_VARS['name'] . ' �ͤ��餪�䤤��碌�򾵤�ޤ�����' . "\n";
			$mail_text .= '�᡼�륢�ɥ쥹��' . $HTTP_POST_VARS['email'] . "\n\n";
			$mail_text .= $HTTP_POST_VARS['enquiry'];
			tep_mail(STORE_OWNER, SUPPORT_EMAIL_ADDRESS, $HTTP_POST_VARS['email_title'] . HEADING_TITLE_CONTACT, nl2br($mail_text), $HTTP_POST_VARS['name'], $HTTP_POST_VARS['email']);
			tep_redirect(tep_href_link(FILENAME_CONTACT_US, 'action=success'));
		} else {
			$error = true;
		}
	}
	
	$breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CONTACT_US));
?>
<?php page_head();?>
</head>
<body> 
	<!-- header //--> 
	<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
	<!-- header_eof //--> 
	<!-- body //--> 
	<div id="main">
		<!-- left_navigation //-->
		<div id="l_menu">
			<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
		</div>
		<!-- left_navigation_eof //-->
		<!-- body_text //-->
		<div id="content">
		<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
			<h1 class="pageHeading"><?php
	if (isset($HTTP_GET_VARS['products_id'])) {
		echo $product_info['products_name'] . HEADING_TITLE_CONTACT;
	}else{
		echo HEADING_TITLE;
	}
?></h1> 
			<div> 
				<table border="0" class="box_des" width="100%" cellspacing="0" cellpadding="0"> 
<?php
	if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'success')) {
?> 
					<tr> 
						<td>
							<table class="box_des" border="0" width="95%" cellspacing="0" cellpadding="2" align="center"> 
								<tr> 
									<td class="main"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_man_on_board.gif', HEADING_TITLE, '0', '0', 'align="left"') . TEXT_SUCCESS; ?></td> 
								</tr> 
								<tr> 
									<td align="right"><br> 
										<a href="<?php echo tep_href_link(FILENAME_DEFAULT); ?>"><?php echo tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></a>
									</td> 
								</tr> 
							</table>
						</td> 
					</tr> 
<?php
	} else {
?> 
					<tr> 
						<td>
<?php 
	if (isset($HTTP_GET_VARS['products_id'])) {
		//product_id������������Υե�����Υ����������
		echo tep_draw_form('contact_us', tep_href_link(FILENAME_CONTACT_US, 'action=send_p'));
	} else {
		//�̾�
		echo tep_draw_form('contact_us', tep_href_link(FILENAME_CONTACT_US, 'action=send'));
	}
?> 
							<table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2"> 
								<tr> 
									<td class="main"><?php echo ENTRY_NAME; ?><br> 
										<?php echo tep_draw_input_field('name', ($error ? $HTTP_POST_VARS['name'] : (($language == 'japanese') ? ($last_name . ' ' . $first_name) : ($first_name . ' ' . $last_name)))); // 2003.03.10 Edit Japanese osCommerce ?>
									</td> 
								</tr> 
								<tr> 
									<td class="main"><?php echo ENTRY_EMAIL; ?><br> 
										<?php echo tep_draw_input_field('email', ($error ? $HTTP_POST_VARS['email'] : $email_address), 'size="30"'); if ($error) echo ENTRY_EMAIL_ADDRESS_CHECK_ERROR; ?></td> 
									</tr> 
								<tr> 
									<td class="main"><?php echo ENTRY_ENQUIRY; ?></td> 
								</tr> 

								<tr> 
									<td><?php 
	if (isset($HTTP_GET_VARS['products_id'])) {
		$product_name = $product_info['products_name']; //�ѿ��˾���̾���Ǽ
		//product_id������������
		echo tep_draw_hidden_field('email_title', $product_name); 
	} 
?> <?php echo tep_draw_textarea_field('enquiry', 'soft', 50, 15, '����' . $product_name . TITLE_CONTACT_END . "\n" . EMAIL_SEPARATOR); ?></td> 
								</tr> 
								<tr> 
									<td class="main" align="right" style="padding-right:20px;"><br> 
										<?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?>
									</td> 
								</tr> 
							</table> 
							</form>
						</td> 
					</tr> 
<?php
	}
?> 
				</table> 
			</div>
		</div>
		<!-- body_text_eof //--> 
		<!-- right_navigation //--> 
		<div id="r_menu">
			<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
		</div>
		<!-- right_navigation_eof //--> 
		<!-- body_eof //--> 
		<!-- footer //--> 
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
		<!-- footer_eof //--> 
	</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
