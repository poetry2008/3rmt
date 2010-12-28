<?php
/*
  $Id$
*/
?>
<!-- login //-->
<script type="text/javascript" src="js/logincheck.js"></script>
<table border="0" width="171" cellpadding="0" cellspacing="0" summary="login box">
	<tr>
		<td><h3><img width="171" height="27" alt="RMTアイテムデポ 会員登録" src="images/design/title_img01.gif"></h3></td>
	</tr>
	<tr>
    	<td><a href="<?php echo tep_href_link(FILENAME_LOGIN,'','SSL'); ?>"><img class="middle" src="images/design/login02.gif" alt="ログイン"></a></td>
    </tr>
</table>

<?php
  if (!tep_session_is_registered('customer_id')) {
?>
<?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL'), 'post', 'onSubmit="return msg();"') . "\n"; ?>
<!--
  <table border="0" width="171" cellpadding="0" cellspacing="0" summary="login box">
    <tr>
          <td><h3><img width="171" height="27" alt="RMTアイテムデポ 会員登録" src="images/design/title_img01.gif"></h3></td>
        </tr>
        <tr>
          <td id="login_form">
              <table width="171" summary="login">
                    <tr>
                        <td width="25" style="padding-left:8px; background-color:#e9e9e9;"><?php echo tep_image(DIR_WS_IMAGES.'design/box/id.gif','メールアドレス','10','8','class="middle"');?></td>
                        <td style="background-color:#e9e9e9;"><div class="login_text_box"><input type="text" name="email_address" class="login_text" value=""></div></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 8px; background-color:#e9e9e9;"><?php echo tep_image(DIR_WS_IMAGES.'design/box/pw.gif','パスワード','17','8','class="middle"');?></td>
                        <td style="background-color:#e9e9e9;"><div class="login_text_box"><input type="password" name="password" class="login_text" ></div></td>
                    </tr>
                    <tr>
                        <td class="smallText" colspan="2" align="right">
                            <a href="<?php echo tep_href_link(FILENAME_PASSWORD_FORGOTTEN,'',SSL);?>" class="back_password">
                                <img class="middle" src="images/design/box/login_arrow.gif" width="5" height="6" alt="">
                                PW忘れた?
                            </a>
                          <input type="image" align="middle" class="login_submit" name="image" alt="<?php echo HEADER_TITLE_LOGIN ; ?>" src="images/design/button/login.gif">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
  </table>
-->
</form>
<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="login">
  <tr>
    <td class="login_ig" style="padding-bottom:4px;" align="center"><a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'','SSL'); ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/box/signup.gif',HEADER_TITLE_CREATE_ACCOUNT,'172','123'); ?></a></td>
  </tr>
</table>
<?php 
  } else { 
    if($guestchk == '1') {
?>
<?php echo tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL'), 'post', 'onSubmit="return msg();"') . "\n"; ?>
  <table border="0" width="171" cellpadding="0" cellspacing="0" summary="login box">
    <tr>
          <td><img src="images/design/title_img01.gif" height="27" alt="" ></td>
        </tr>
        <tr>
          <td id="login_form">
              <table width="171" summary="login">
                    <tr>
                        <td width="25" style="padding-left: 8px; background-color:#e9e9e9;"><?php echo tep_image(DIR_WS_IMAGES.'design/box/id.gif','メールアドレス','10','8','class="middle"');?></td>
                        <td style="background-color:#e9e9e9;"><div class="login_text_box"><input type="text" name="email_address" class="login_text"></div></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 8px; background-color:#e9e9e9;"><?php echo tep_image(DIR_WS_IMAGES.'design/box/pw.gif','パスワード','17','8','class="middle"');?></td>
                        <td style="background-color:#e9e9e9;"><div class="login_text_box"><input type="password" name="password" class="login_text"></div></td>
                    </tr>
                    <tr>
                        <td class="smallText" colspan="2" align="right">
                            <a href="<?php echo tep_href_link(FILENAME_PASSWORD_FORGOTTEN,'',SSL);?>" class="back_password">
                                <img class="middle" src="images/design/box/login_arrow.gif" width="5" height="6" alt="">
                                PW忘れた?
                            </a>
                          <input type="image" align="middle" class="login_submit" name="image" alt="<?php echo HEADER_TITLE_LOGIN ; ?>" src="images/design/button/login.gif">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
  </table>
</form>
<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="login">
  <tr>
    <td height="100" align="center" style="padding-bottom:4px;"><a href="<?php echo tep_href_link(FILENAME_CREATE_ACCOUNT,'',SSL); ?>"><?php echo tep_image(DIR_WS_IMAGES.'design/box/signup.gif',HEADER_TITLE_CREATE_ACCOUNT,'172','123'); ?></a></td>
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
