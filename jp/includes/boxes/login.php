<?php
/*
  $Id: reviews.php,v 1.1.1.1 2003/02/20 01:03:53 ptosh Exp $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
  <meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
*/
?>
<!-- login //-->
<script type="text/javascript" src="js/logincheck.js"></script>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr><td height="25"><?php echo tep_image(DIR_WS_IMAGES.'design/box/login.gif',HEADER_TITLE_LOGIN,'171','25'); ?></td></tr>
</table>
<?php
	if (!tep_session_is_registered('customer_id')) {
?>
<?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL'), 'post', 'onSubmit="return msg();"') . "\n"; ?>
	<table border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:5px; ">
		<tr>
			<td width="25" height="25"><?php echo tep_image(DIR_WS_IMAGES.'design/box/id.gif','メールアドレス','10','8','class="middle"');?></td>
			<td><input type="text" name="email_address" size="20"></td>
		</tr>
		<tr>
			<td height="25"><?php echo tep_image(DIR_WS_IMAGES.'design/box/pw.gif','パスワード','17','8','class="middle"');?></td>
			<td><input type="password" name="password" size="20"></td>
		</tr>
	</table>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td height="35" class="smallText" style="padding-left:5px; ">
				<a href="<?php echo tep_href_link(FILENAME_PASSWORD_FORGOTTEN,'','SSL');?>">
					<img class="middle" src="images/design/box/arrow_1.gif" width="5" height="5" hspace="2" alt="">
					PW忘れた?
				</a>
			</td>
			<td align="right" style="padding-right:5px; "><input type="image" name="image" src="images/design/button/login.gif" alt="Login" title="Login"></td>
		</tr>
		<tr>
			<td height="1" colspan="2" bgcolor="#CCCCCC"></td>
		</tr>
	</table>
</form>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="100" align="center"><a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/box/signup.gif',HEADER_TITLE_CREATE_ACCOUNT,'158','90'); ?></a></td>
	</tr>
</table>
<?php 
	} else { 
		if($guestchk == '1') {
?>
<?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL'), 'post', 'onSubmit="return msg();"') . "\n"; ?>
	<table border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:5px; ">
		<tr>
			<td width="25" height="25"><?php echo tep_image(DIR_WS_IMAGES.'design/box/id.gif','メールアドレス','10','8');?></td>
			<td><input type="text" name="email_address" size="20"></td>
		</tr>
		<tr>
			<td height="25"><?php echo tep_image(DIR_WS_IMAGES.'design/box/pw.gif','パスワード','17','8');?></td>
			<td><input type="password" name="password" size="20"></td>
		</tr>
	</table>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td height="35" class="smallText" style="padding-left:5px; ">
				<a href="<?php echo tep_href_link(FILENAME_PASSWORD_FORGOTTEN,'',SSL);?>">
					<img class="middle" src="images/design/box/arrow_1.gif" width="5" height="5" hspace="2" alt="img">
					PW忘れた?
				</a>
			</td>
			<td align="right" style="padding-right:5px; "><input type="image" name="image" alt="<?php echo HEADER_TITLE_LOGIN ; ?>" src="images/design/button/login.gif"></td>
		</tr>
		<tr>
			<td height="1" colspan="2" bgcolor="#CCCCCC"></td>
		</tr>
	</table>
</form>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="100" align="center"><a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'',SSL); ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/box/signup.gif',HEADER_TITLE_CREATE_ACCOUNT,'158','90'); ?></a></td>
	</tr>
</table>
<?php	
		} else {
?>
<table border="0" width="100%" cellpadding="0" cellspacing="0" class="tableUnderbar">
	<tr>
		<td height="25" class="menu">
			<img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt="img">
			<?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . HEADER_TITLE_MY_ACCOUNT . '</a>'."\n"; ?>
		</td>
	</tr>
	<tr>
		<td height="25" class="menu">
			<img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt="img">
			<?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . MYACCOUNT_EDIT . '</a>'."\n"; ?>
		</td>
	</tr>
	<tr>
		<td height="25" class="menu">
			<img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt="img">
			<?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . MYACCOUNT_HISTORY . '</a>'."\n"; ?>
		</td>
	</tr>
	<tr>
		<td height="25" class="menu">
			<img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt="img">
			<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL') . '">' . MYACCOUNT_NOTIFICATION . '</a>'."\n"; ?>
		</td>
	</tr>
	<tr>
		<td height="25" class="menu">
			<img class="middle" src="images/design/box/arrow_2.gif" width="5" height="5" hspace="3" alt="img">
			<?php echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '">' . HEADER_TITLE_LOGOFF . '</a>'."\n"; ?>
		</td>
	</tr>
</table>
<?php 
		}
 	} 
?>
<!-- login_eof //-->
