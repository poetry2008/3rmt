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
          <div class="menu_top_login"><img src="images/menu_ico.gif" alt="" align="top">&nbsp;会員登録</div>
          <div id="login_form">
          <div class="login_form_warpper">
              <table border="0">
                    <tr>
                    	<td align="center"><a href="<?php echo tep_href_link(FILENAME_LOGIN,'','SSL'); ?>"><img class="middle" style="margin-left:1px;" src="images/design/login02.gif" alt="ログイン"></a></td>
                    </tr>
                    <tr><td colspan="2" align="center"><?php
  if (!tep_session_is_registered('customer_id')) {
?>
<a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo '<img src="images/design/box/signup.gif" alt="'.HEADER_TITLE_CREATE_ACCOUNT.'" width="147" height="94">'; ?></a>
<?php 
   }
?></td>
                    </tr></table>
                </div>
          <div class="login_bottom"><img height="14" width="170" alt="" src="images/design/box/box_bottom_bg_01.gif"></div>
  </div></div>
<?php 
  } else { 
    if($guestchk == '1') {
?>
    <div class="login_box">
          <div class="menu_top_login"><img src="images/menu_ico.gif" alt="" align="top">&nbsp;会員登録</div>
          <div id="login_form">
          <div class="login_form_warpper">
              <table border="0">
                    <tr>
                    	<td align="center"><a href="<?php echo tep_href_link(FILENAME_LOGIN,'','SSL'); ?>"><img class="middle" style="margin-left:1px;" src="images/design/login02.gif" alt="ログイン"></a></td>
                    </tr>
                    <tr><td colspan="2" align="center"><?php
  if (!tep_session_is_registered('customer_id')) {
?>
<a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo '<img src="images/design/box/signup.gif" alt="'.HEADER_TITLE_CREATE_ACCOUNT.'" width="147" height="94">'; ?></a>
<?php 
   }
?></td>
                    </tr></table>
                </div>
          <div class="login_bottom"><img height="14" width="170" alt="" src="images/design/box/box_bottom_bg_01.gif"></div>
  </div></div>
<?php 
    } else {
?>
<div class="login box">
        <div class="menu_top_login"><img src="images/menu_ico.gif" alt="" align="top">&nbsp;会員登録</div>
        <div id="login_form">
        <div class="login_form_warpper">
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
            </div>
        </div>
        <div class="login_bottom"><img height="14" width="170" alt="" src="images/design/box/box_bottom_bg_01.gif"></div>
</div>
<?php 
    }
  } 
?>
<!-- login_eof //-->
