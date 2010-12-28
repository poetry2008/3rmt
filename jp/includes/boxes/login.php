<?php
/*
  $Id$
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
<table border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:5px; ">
	<tr>
    	<td>
			<a href="<?php echo tep_href_link(FILENAME_LOGIN, '', 'SSL');?>"><img class="middle" src="images/design/box/login02.gif" width="158" alt="ログイン"></a>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="100" align="center"><a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/box/signup.gif',HEADER_TITLE_CREATE_ACCOUNT,'158','90'); ?></a></td>
  </tr>
</table>
<?php 
  } else { 
    if($guestchk == '1') {
?>
<table border="0" align="center" cellpadding="0" cellspacing="0" style="margin-top:5px; ">
	<tr>
    	<td>
			<a href="<?php echo tep_href_link(FILENAME_LOGIN, '', 'SSL');?>"><img class="middle" src="images/design/box/login02.gif" width="158" alt="ログイン"></a>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td height="100" align="center"><a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/box/signup.gif',HEADER_TITLE_CREATE_ACCOUNT,'158','90'); ?></a></td>
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
