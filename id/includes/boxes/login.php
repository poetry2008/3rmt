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
<table border="0" width="171" cellpadding="0" cellspacing="0" summary="login box">
	<tr>
		<td><img width="171" height="27" alt="RMTアイテムデポ 会員登録" src="images/design/title_img01.gif"></td>
	</tr>
	<tr>
    	<td><a href="<?php echo tep_href_link(FILENAME_LOGIN,'','SSL'); ?>"><img class="middle" src="images/design/login02.gif" alt="ログイン"></a></td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="login">
  <tr>
    <td class="login_ig" style="padding-bottom:4px;" align="center"><a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/box/signup.gif',HEADER_TITLE_CREATE_ACCOUNT,'172','123'); ?></a></td>
  </tr>
</table>
<?php 
  } else { 
    if($guestchk == '1') {
?>
<table border="0" width="171" cellpadding="0" cellspacing="0" summary="login box">
	<tr>
		<td><img width="171" height="27" alt="RMTアイテムデポ 会員登録" src="images/design/title_img01.gif"></td>
	</tr>
	<tr>
    	<td><a href="<?php echo tep_href_link(FILENAME_LOGIN,'','SSL'); ?>"><img class="middle" src="images/design/login02.gif" alt="ログイン"></a></td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="login">
  <tr>
    <td class="login_ig" style="padding-bottom:4px;" align="center"><a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/box/signup.gif',HEADER_TITLE_CREATE_ACCOUNT,'172','123'); ?></a></td>
  </tr>
</table>
<?php 
    } else {
?>
  <img width="171" height="27"  src="images/design/title_img01.gif" alt=""> 
  <ul class="l_m_category_ul">
    <li class="l_m_category_li">
      <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . HEADER_TITLE_MY_ACCOUNT . '</a>'."\n"; ?>
    </li>
    <li class="l_m_category_li">
      <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . MYACCOUNT_EDIT . '</a>'."\n"; ?>
    </li>
    <li class="l_m_category_li">
      <?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . MYACCOUNT_HISTORY . '</a>'."\n"; ?>
    </li>
    <li class="l_m_category_li">
      <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL') . '">' . MYACCOUNT_NOTIFICATION . '</a>'."\n"; ?>
    </li>
    <li class="l_m_category_li">
      <?php echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '">' . HEADER_TITLE_LOGOFF . '</a>'."\n"; ?>
    </li>
  </ul>
<?php 
    }
  } 
?>
<!-- login_eof //-->
