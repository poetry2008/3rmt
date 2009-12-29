<?php
/*
  $Id: reviews.php,v 1.1.1.1 2003/02/20 01:03:53 ptosh Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- login //-->
<script type="text/javascript" src="js/logincheck.js"></script>
<?php
   if (!tep_session_is_registered('customer_id')) {
?>
<?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL'), 'post', 'onSubmit="return msg();"')."\n"; ?>
<div class="box_des">ログイン</div> 
<table  border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="25" height="25">Email:</td>
    <td><input type="text" name="email_address" style="width:90px; height:22px;"></td>
  </tr>
  <tr>
    <td height="25">PW:</td>
    <td><input type="password" name="password" style="width:90px; height:22px;"></td>
  </tr>
</table>
<table  border="0" align="center" cellpadding="0" cellspacing="3">
  <tr>
    <td class="smallText"><a href="<?php echo tep_href_link(FILENAME_PASSWORD_FORGOTTEN,'',SSL);?>">PW忘れた?</a></td>
    <td align="right" style="padding-right:5px; "><input name="image" type="image" alt="img" title="<?php echo HEADER_TITLE_LOGIN ; ?>" src="images/design/button/login.gif"></td>
  </tr>
</table>
</form>
<div class="sep">&nbsp;</div>
<?php	
	} else {
?>
<div class="box_titleR">アカウント</div> 
<ul>
    <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . HEADER_TITLE_MY_ACCOUNT . '</a>'; ?></li>
    <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . MYACCOUNT_EDIT . '</a>'; ?></li>
    <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . MYACCOUNT_HISTORY . '</a>'; ?></li>
    <li><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL') . '">' . MYACCOUNT_NOTIFICATION . '</a>'; ?></li>
	<li><?php echo '<a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '">' . 'ショッピングカート' . '</a>'; ?></li>
    <li><?php echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '">' . HEADER_TITLE_LOGOFF . '</a>'; ?></li>
</ul>
<?php 
 //   }
  } 
?>
<!-- login_eof //-->
