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
<div class="login02">
  <a href="<?php echo tep_href_link(FILENAME_LOGIN,'','SSL'); ?>"><img class="middle" src="images/banners/login02.gif" alt="ログイン"></a>
</div>
<div class="login03">
  <a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><img class="middle" src="images/banners/login03.gif" alt="会員登録"></a>
</div>
<?php 
  } else {
?>
<div class="login_online01">
<div class="box_titleR">アカウント</div> 
<ul>
    <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . HEADER_TITLE_MY_ACCOUNT . '</a>'; ?></li>
    <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . MYACCOUNT_EDIT . '</a>'; ?></li>
    <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . MYACCOUNT_HISTORY . '</a>'; ?></li>
    <li><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL') . '">' . MYACCOUNT_NOTIFICATION . '</a>'; ?></li>
    <li><?php echo '<a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '">' . 'ショッピングカート' . '</a>'; ?></li>
    <li><?php echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '">' . HEADER_TITLE_LOGOFF . '</a>'; ?></li>
</ul>
</div>
<?php 
 //   }
  } 
?>
<!-- login_eof //-->
