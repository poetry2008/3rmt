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
    <div class="login_box">
          <div class="menu_top">メンバーズページ</div>
          <div id="login_form">
				<a class="login_link" href="<?php echo tep_href_link(FILENAME_LOGIN,'','SSL'); ?>"><img src="images/design/login02.gif" alt="ログイン"></a>
<?php
  if (!tep_session_is_registered('customer_id')) {
?>
                <a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo '<img src="images/design/box/signup.gif" alt="'.HEADER_TITLE_CREATE_ACCOUNT.'" width="148" height="88">'; ?></a>
<?php 
   }
?>
  </div></div>
<?php 
  } else { 
    $customers = tep_db_query('select * from '.TABLE_CUSTOMERS.' where customers_id='.$_SESSION['customer_id']);
    $customers_row = tep_db_fetch_array($customers);
    $guestchk= $customers_row['customers_guest_chk'];
    if($guestchk == '1') {
?>
    <div class="login_box">
          <div class="menu_top">メンバーズページ</div>
          <div id="login_form">
          <a href="<?php echo tep_href_link(FILENAME_LOGIN,'','SSL'); ?>"><img class="middle" style="margin-left:1px;" src="images/design/login02.gif" alt="ログイン"></a>
                        <?php
  if (!tep_session_is_registered('customer_id')) {
?>
<a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo '<img src="images/design/box/signup.gif" alt="'.HEADER_TITLE_CREATE_ACCOUNT.'" width="147" height="94">'; ?></a>
<?php 
   }
?>
  </div></div>
<?php 
    } else {
?>
<div class="login_box">
        <div class="menu_top">メンバーズページ</div>
        <div id="login_form02">
        <ul class="login_list">
          <li class="login_list_info02"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . HEADER_TITLE_MY_ACCOUNT . '</a>'."\n"; ?></li>
          <li class="login_list_info02"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . MYACCOUNT_EDIT . '</a>'."\n"; ?></li>
		  <li class="login_list_info02"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . MYACCOUNT_HISTORY . '</a>'."\n"; ?></li>
          <li class="login_list_info02"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL') . '">' . MYACCOUNT_NOTIFICATION . '</a>'."\n"; ?></li>
          <li class="login_list_info02"><?php echo '<a href="' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . '">' . HEADER_TITLE_LOGOFF . '</a>'."\n"; ?></li>
           </ul>
        </div>
</div>
<?php 
    }
  } 
?>
<!-- login_eof //-->
