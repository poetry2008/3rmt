<?php
/*
  $Id$
*/
?>
<!-- login //-->
<script type="text/javascript" src="js/logincheck.js"></script>
<?php
  if (!tep_session_is_registered('customer_id')) {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="login">
  <tr>
    <td height="100" align="center"><a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/box/signup.gif',HEADER_TITLE_CREATE_ACCOUNT,'164','150'); ?></a></td>
  </tr>
</table>
<table border="0" width="162" cellpadding="0" cellspacing="0" style="margin: 0 5px 0 5px;" summary="login box">
	<tr>
    	<td><a href="<?php echo tep_href_link(FILENAME_LOGIN,'','SSL'); ?>"><img class="middle" src="images/design/box/login02.gif" width="158" alt="ログイン"></a></td>
    </tr>
</table>
<?php 
  } else { 
    if($guestchk == '1') {
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="login">
  <tr>
    <td height="100" align="center"><a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/box/signup.gif',HEADER_TITLE_CREATE_ACCOUNT,'164','150'); ?></a></td>
  </tr>
</table>
<table border="0" width="162" cellpadding="0" cellspacing="0" style="margin: 0 5px 0 5px;" summary="login box">
	<tr>
    	<td><a href="<?php echo tep_href_link(FILENAME_LOGIN,'','SSL'); ?>"><img class="middle" src="images/design/box/login02.gif" width="158" alt="ログイン"></a></td>
    </tr>
</table>
<?php 
    } else {
?>
<table border="0" width="162" cellpadding="0" cellspacing="0" style="margin: 5px 5px 10px 5px;" summary="login box">
    <tr>
        <td><img src="images/design/box/login_top_bg.gif" width="162" height="15" alt="" ></td>
    </tr>
    <tr>
        <td id="login_form">
            <table width="152" summary="login">
                <tr>
                    <td class="menu">
                        <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . HEADER_TITLE_MY_ACCOUNT . '</a>'."\n"; ?>
                    </td>
                </tr>
                <tr>
                    <td class="menu">
                        <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . MYACCOUNT_EDIT . '</a>'."\n"; ?>
                    </td>
                </tr>
                <tr>
                    <td class="menu">
                        <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . MYACCOUNT_HISTORY . '</a>'."\n"; ?>
                    </td>
                </tr>
                <tr>
                    <td class="menu">
                        <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL') . '">' . MYACCOUNT_NOTIFICATION . '</a>'."\n"; ?>
                    </td>
                </tr>
                <tr>
                    <td class="menu">
                        <?php echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '">' . HEADER_TITLE_LOGOFF . '</a>'."\n"; ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td height="15" style="background: url(images/design/box/login_bottom_bg.gif) top no-repeat;"></td>
    </tr>
</table>
<?php 
    }
  } 
?>
<!-- login_eof //-->
