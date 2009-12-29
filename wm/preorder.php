<?php
/*
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  if (tep_session_is_registered('customer_id')) {
    $account = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . $customer_id . "'");
    $account_values = tep_db_fetch_array($account);
  } elseif (ALLOW_GUEST_TO_TELL_A_FRIEND == 'false') {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  $valid_product = false;
  if (isset($HTTP_GET_VARS['products_id'])) {
    $product_info_query = tep_db_query("select pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "'");
    $valid_product = (tep_db_num_rows($product_info_query) > 0);
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PREORDER);

  $product_info = tep_db_fetch_array($product_info_query);
  $breadcrumb->add($product_info['products_name'] . '��ͽ�󤹤�', tep_href_link(FILENAME_PREORDER, 'products_id=' . $HTTP_GET_VARS['products_id']));
  $po_game_c = ds_tep_get_categories((int)$HTTP_GET_VARS['products_id'],1);
?>
<?php page_head();?>
</head>
<body>
<div align="center">
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
	<tr>
		<td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
			<!-- left_navigation //-->
			<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
			<!-- left_navigation_eof //-->
		</td>
		<!-- body_text //-->
		<td valign="top" id="contents">
<?php
	if ($valid_product == false) {
?>
			<p class="main">
				<?php echo HEADING_TITLE_ERROR; ?><br><?php echo ERROR_INVALID_PRODUCT; ?>
			</p>
<?php
	} else {
		//$product_info = tep_db_fetch_array($product_info_query);
?>
			<h1 class="pageHeading"><?php echo $po_game_c . '&nbsp;' . $product_info['products_name']; ?>��ͽ�󤹤�</h1>
            <div class="comment">
			<p>
				RMT���ɥޥ͡��Ǥϡ�<?php echo $po_game_c; ?>��ͽ�󥵡��ӥ���ԤäƤ���ޤ���<br>
				����˾������̤����Һ߸ˤˤ�����ϡ�<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $HTTP_GET_VARS['products_id']) . '" target="_blank">' . $product_info['products_name']; ?></a>�פ򥯥�å����Ƥ���³������������
			</p>
<?php
		$error = false;
	
		if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process') && empty($HTTP_POST_VARS['quantity'])) {
			$quantity_error = true;
			$error = true;
		} else {
			$quantity_error = false;
		}
		
		if (tep_session_is_registered('customer_id')) {
			$from_name = tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']);
			$from_email_address = $account_values['customers_email_address'];
		} else {
			$from_name = $HTTP_POST_VARS['yourname'];
			$from_email_address = $HTTP_POST_VARS['from'];
		}
		
		if (!tep_session_is_registered('customer_id')) {
			if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process') && !tep_validate_email(trim($from_email_address))) {
				$fromemail_error = true;
				$error = true;
				} else {
					$fromemail_error = false;
				}
			}
		
		if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process') && empty($from_name)) {
			$fromname_error = true;
			$error = true;
		} else {
			$fromname_error = false;
		}
		
		if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process') && ($error == false)) {
			$email_subject = sprintf(TEXT_EMAIL_SUBJECT, $product_info['products_name'], STORE_NAME);
			$email_body = sprintf(TEXT_EMAIL_INTRO, $from_name, STORE_NAME, $from_name, $from_email_address, $HTTP_POST_VARS['products_name'], $HTTP_POST_VARS['quantity'], $HTTP_POST_VARS['timelimit'], STORE_NAME) . "\n\n";
		
			if (tep_not_null($HTTP_POST_VARS['yourmessage'])) {
				$email_body .= '������˾' . "\n" . $HTTP_POST_VARS['yourmessage'] . "\n\n";
			}
		
			$email_body .= sprintf(TEXT_EMAIL_LINK, tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $HTTP_GET_VARS['products_id'])) . "\n\n" .
			sprintf(TEXT_EMAIL_SIGNATURE, STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n");
		
			tep_mail('', SEND_EXTRA_ORDER_EMAILS_TO, $email_subject, stripslashes($email_body), $from_name, $from_email_address);
			tep_mail('', $from_email_address, $email_subject, stripslashes($email_body), STORE_NAME, STORE_OWNER_EMAIL_ADDRESS);
?>
			<div>
				<?php echo sprintf(TEXT_EMAIL_SUCCESSFUL_SENT, $from_email_address, stripslashes($HTTP_POST_VARS['products_name']), $HTTP_POST_VARS['quantity'], $HTTP_POST_VARS['timelimit']); ?>
				<div align="center"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $HTTP_GET_VARS['products_id']) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></div>
			</div>
<?php
		} else {
			if (tep_session_is_registered('customer_id')) {
				$your_name_prompt = tep_output_string_protected(tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']));
				$your_email_address_prompt = $account_values['customers_email_address'];
			} else {
				$your_name_prompt = tep_draw_input_field('yourname', (($fromname_error == true) ? $HTTP_POST_VARS['yourname'] : $HTTP_GET_VARS['yourname']), 'class="input_text"');
				if ($fromname_error == true) $your_name_prompt .= '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';
				$your_email_address_prompt = tep_draw_input_field('from', (($fromemail_error == true) ? $HTTP_POST_VARS['from'] : $HTTP_GET_VARS['from']) , 'size="30" class="input_text"') . '&nbsp;&nbsp;�������å᡼�륢�ɥ쥹�侩';
				if ($fromemail_error == true) $your_email_address_prompt .= ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
			}
?>
			<?php echo tep_draw_form('email_friend', tep_href_link(FILENAME_PREORDER, 'action=process&products_id=' . $HTTP_GET_VARS['products_id'])) . tep_draw_hidden_field('products_name', $product_info['products_name']); ?>

			<p>
				���Һ߸ˤˤ����ͤ�����˾������̤��ʤ����ϡ�������ɬ�׻�������Ϥξ太�������ߤ���������<br>
				ͽ���³������λ�������ޤ��ȡ����ټ��衢�����ͤ�ͥ��Ū�ˤ����⤤�����ޤ���
			</p>
			<p class="red"><b>��ͽ�󡦤����Ѥ��̵���Ǥ��Τǡ������ڤˤ��䤤��碌����������</b></p>
<?php
			if($error == true) {
				echo '<span class="errorText"><b>���Ϥ������Ƥ˸�꤬�������ޤ������������Ϥ��Ƥ���������</span></b><br><br>';
			}
?>
			<h3 class="formAreaTitle"><?php echo FORM_TITLE_CUSTOMER_DETAILS; ?></h3>
			<table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
				<tr>  
					<td class="main"><?php echo FORM_FIELD_CUSTOMER_NAME; ?></td>
					<td class="main"><?php echo $your_name_prompt; ?></td>
				</tr>
				<tr>
					<td class="main"><?php echo FORM_FIELD_CUSTOMER_EMAIL; ?></td>
					<td class="main"><?php echo $your_email_address_prompt; ?></td>
				</tr>
				<tr> 
					<td colspan="2" class="main">������֤����¤��������ޤ������Ĥ���Ѥ��Ƥ���᡼�륢�ɥ쥹�����Ϥ���������</td>
				</tr>
			</table><br>
			<h3 class="formAreaTitle"><?php echo FORM_TITLE_FRIEND_DETAILS; ?></h3>
			<table width="100%" cellpadding="2" cellspacing="2" border="0" class="formArea">
				<tr>
					<td class="main" valign="top">����̾:</td>
					<td class="main"><strong><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $HTTP_GET_VARS['products_id']) . '" target="_blank">' . $po_game_c . '&nbsp;/&nbsp;' . $product_info['products_name']; ?></a></strong></td>
				</tr>
				<tr>
					<td class="main"><?php echo FORM_FIELD_FRIEND_NAME; ?></td>
					<td class="main">
<?php
						echo tep_draw_input_field('quantity', (($quantity_error == true) ? $HTTP_POST_VARS['quantity'] : $HTTP_GET_VARS['quantity']) , 'size="7" maxlength="15" class="input_text_short"');
						echo '&nbsp;&nbsp;��';
			if ($quantity_error == true) echo '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';
?>
					</td>
				</tr>
				<tr>
					<td class="main"><?php echo FORM_FIELD_FRIEND_EMAIL; ?></td>
					<td class="main">
<?php
						echo tep_draw_input_field('timelimit', (($timelimit_error == true) ? $HTTP_POST_VARS['timelimit'] : $HTTP_GET_VARS['send_to']) , 'size="30" maxlength="50" class="input_text"');
						echo '&nbsp;&nbsp;(��.&nbsp;20���ޤǤ��Ϥ����ߤ�����)';
?>
					</td>
				</tr>
			</table>
			<br>
			<h3 class="formAreaTitle"><?php echo $po_game_c; ?>�ˤĤ��ƤΤ���˾</h3>
			<table width="100%" cellpadding="2" cellspacing="0" border="0" class="formArea">
				<tr><td class="main"><?php echo tep_draw_textarea_field('yourmessage', 'soft', 40, 8);?></td></tr>
			</table>
			<br>
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="main">
						<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $HTTP_GET_VARS['products_id']) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
					</td>
					<td align="right" class="main">
						<?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?>
					</td>
				</tr>
			</table>
		</form>
<?php
		}
	}
?>
		</div>
        <p class="pageBottom"></p>
		</td>      
		<!-- body_text_eof //-->
		<td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
			<!-- right_navigation //-->
			<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
			<!-- right_navigation_eof //-->
		</td>
	</tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</div>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
