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
<?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL'), 'post', 'onSubmit="return msg();"') . "\n"; ?>
    <div class="login_box">
          <div class="menu_top_login"><img src="images/menu_ico.gif" alt="" align="top">&nbsp;会員登録</div>
          <div id="login_form">
          <div class="login_form_warpper">
              <table border="0">
                    <tr>
                        <td width="23" align="right" style="padding-right: 5px;"><?php echo tep_image(DIR_WS_IMAGES.'design/box/id.gif','メールアドレス','10','8','class="middle"');?></td>
                        <td><div class="login_text_box"><input type="text" name="email_address" class="login_text" value=""></div></td>
                    </tr>
                    <tr>
                        <td align="right" style="padding-right: 5px;"><?php echo tep_image(DIR_WS_IMAGES.'design/box/pw.gif','パスワード','17','8','class="middle"');?></td>
                        <td><div class="login_text_box"><input type="password" name="password" class="login_text" ></div></td>
                    </tr>
                    <tr>
                        <td class="smallText" colspan="2" align="right">
                            <a href="<?php echo tep_href_link(FILENAME_PASSWORD_FORGOTTEN,'',SSL);?>" class="back_password">
                                <img class="middle" src="images/design/box/login_arrow.gif" width="15" height="16" alt="">
                                PW忘れた?
                            </a>
                          <input type="image" align="middle" class="login_submit" name="image" alt="<?php echo HEADER_TITLE_LOGIN ; ?>" src="images/design/button/login.gif">
                        </td>
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
</form>
<?php 
  } else { 
    if($guestchk == '1') {
?>
<?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL'), 'post', 'onSubmit="return msg();"') . "\n"; ?>
    <div class="login_box">
          <div class="menu_top_login"><img src="images/menu_ico.gif" alt="" align="top">&nbsp;会員登録</div>
          <div id="login_form">
          <div class="login_form_warpper">
              <table border="0">
                    <tr>
                        <td width="23" align="right" style="padding-right: 5px;"><?php echo tep_image(DIR_WS_IMAGES.'design/box/id.gif','メールアドレス','10','8','class="middle"');?></td>
                        <td><div class="login_text_box"><input type="text" name="email_address" class="login_text" value=""></div></td>
                    </tr>
                    <tr>
                        <td align="right" style="padding-right: 5px;"><?php echo tep_image(DIR_WS_IMAGES.'design/box/pw.gif','パスワード','17','8','class="middle"');?></td>
                        <td><div class="login_text_box"><input type="password" name="password" class="login_text" ></div></td>
                    </tr>
                    <tr>
                        <td class="smallText" colspan="2" align="right">
                            <a href="<?php echo tep_href_link(FILENAME_PASSWORD_FORGOTTEN,'',SSL);?>" class="back_password">
                                <img class="middle" src="images/design/box/login_arrow.gif" width="15" height="16" alt="">
                                PW忘れた?
                            </a>
                          <input type="image" align="middle" class="login_submit" name="image" alt="<?php echo HEADER_TITLE_LOGIN ; ?>" src="images/design/button/login.gif">
                        </td>
                    </tr>
                    <tr>
                      <td colspan="2" align="center">
                        <a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo '<img src="images/design/box/signup.gif" alt="'.HEADER_TITLE_CREATE_ACCOUNT.'" width="147" height="94">'; ?></a>
                      </td>
                    </tr></table>
                </div>
          <div class="login_bottom"><img height="14" width="170" alt="" src="images/design/box/box_bottom_bg_01.gif"></div>
  </div></div>
</form>
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
