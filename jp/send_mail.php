<?php
  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGIN);
  
  $breadcrumb->add(LINK_SENDMAIL_TITLE, tep_href_link(FILENAME_SEND_MAIL));
  
  $error_single = false; 
  $success_single = false; 
  $subject = 'RMTジャックポット';
  $body_text = '';
  $body_text = 'RMTジャックポットが、会員登録をご利用予定のメールアドレスへ、'."\n".' 
  受信確認のためにお送りしています。'."\n".
  'このメールを、無事に受信ボックスで確認できましたら、'."\n".
  'こちらのメールアドレスはRMTジャックポットで問題なくご利用いただけます。'."\n".
  '以下のURLにアクセスして会員登録を行ってください。'."\n".
  HTTP_SERVER.'/create_account.php' ."\n".
  'ご不明な点がありましたら、RMTジャックポットまでお問い合わせください。'."\n".
  'ご連絡・お問い合わせ先━━━━━━━━━━'."\n".
  'RMTジャックポット'."\n".
  STORE_NAME_ADDRESS."\n".	  	  
  HTTP_SERVER."\n".  
  STORE_OWNER_EMAIL_ADDRESS;
  if (isset($HTTP_POST_VARS['email_address'])) {
    if (empty($HTTP_POST_VARS['email_address'])) {
      $error_single = true;
      $error_msg =  EMAIL_PATTERN_WRONG;
    } else if (!tep_validate_email($HTTP_POST_VARS['email_address'])){
      $error_single = true;
      $error_msg =  EMAIL_PATTERN_WRONG;
    } else {
      tep_mail('', $HTTP_POST_VARS['email_address'], $subject, $body_text, EMAIL_FROM, STORE_OWNER_EMAIL_ADDRESS);  
      $success_single = true; 
    }
  }
?>
<?php page_head();?>
</head>
<body>
<div align="center">
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<div id="main">
<table class="side_border" border="0" width="900" cellspacing="0" cellpadding="0">
<tr>
<td class="left_colum_border">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</td>
<!-- left_navigation_eof //-->
<td id="contents" valign="top">
<table width="100%">
<tr>
<td>
<h1 class="pageHeading"><?php echo SEND_MAIL_HEADING_TITLE; ?></h1>
</td>
</tr>

<tr>
<td>
<div class="box">
      <div class="send_mail">
<?php
  if ($success_single == false) {  
  if ($error_single == true) {
     echo '<div style="color:#ff0000;">'.$error_msg.'</div>'; 
   }
   echo tep_draw_form('login', tep_href_link('send_mail.php'), 'post') . "\n";
?>
      <table class="login" width="100%">
        <tr>
          <td valign="top" width="110"><b><?php echo INPUT_SEND_MAIL; ?>:</b></td>
          <td class="login_text" valign="top">
            <input type="text" name="email_address">
          </td>
          <td class="td_submit" align="right" style="padding:0 10px 25px 0;">
            <?php echo tep_image_submit('button_send_mail.gif', SENDMAIL_BUTTON); ?>
          </td>
        </tr>
      </table>
      </form>
  <?php
  } else {
    echo SENDMAIL_SUCCESS_TEXT; 
  }
  ?>
       <?php echo '<br>'.SENDMAIL_READ_TEXT;?> 
       <?php
          echo '<br>'; 
		  echo '<br>';
          echo SENDMAIL_TROUBLE_PRE;  
       ?> 
          <a href="<?php echo tep_href_link('email_trouble.php');?>"><?php echo SENDMAIL_TROUBLE_LINK;?></a>  
        <?php 
          echo SENDMAIL_TROUBLE_END;  
        ?>
        <?php 
		  if ($success_single == true) {  
 			   echo '<div style="width:100%;text-align:left;padding:10px 0 0 0;"><a href="'.tep_href_link(FILENAME_DEFAULT).'"><img src="includes/languages/japanese/images/buttons/button_back.gif"></a></div>';
			   }
		?>
        </div>

  </div>

  </td>
  </tr>
  </table>
  </td>
  <td class="right_colum_border" valign="top" width="171">

<!-- right_navigation //--> 
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</td>
</tr>
</table>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
</div><!-- end of-->
</body>
</html>
