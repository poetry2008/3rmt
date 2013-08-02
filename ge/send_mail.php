<?php
/*
 $Id$
*/
  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_LOGIN);
  
  $breadcrumb->add(LINK_SENDMAIL_TITLE, tep_href_link(FILENAME_SEND_MAIL));
  
  $error_single = false; 
  $success_single = false; 
  //测试邮件
  $mail_array = tep_get_mail_templates('TEXT_BODY_TEXT',SITE_ID);
  $subject = str_replace('${SITE_NAME}',STORE_NAME,$mail_array['title']);
  $body_text = '';
  $body_text = $mail_array['contents'];
  $mode_array = array(
                      '${SITE_NAME}', 
                      '${COMPANY_NAME}',
                      '${SITE_URL}',
                      '${SUPPORT_EMAIL}'
                    );
  $replace_array = array(
                      STORE_NAME, 
                      COMPANY_NAME,
                      HTTP_SERVER,
                      SUPPORT_EMAIL_ADDRESS 
                    );
  $body_text = str_replace($mode_array,$replace_array,$body_text);
  
  $body_text = tep_replace_mail_templates($body_text,$_POST['email_address'],''); 
  if (isset($_POST['email_address'])) {
    if (empty($_POST['email_address'])) {
      $error_single = true;
      $error_msg =  EMAIL_PATTERN_WRONG;
    } else if (!tep_validate_email($_POST['email_address'])){
      $error_single = true;
      $error_msg =  EMAIL_PATTERN_WRONG;
    } else {
      tep_mail('', $_POST['email_address'], $subject, $body_text, EMAIL_FROM, STORE_OWNER_EMAIL_ADDRESS);  
      $success_single = true; 
    }
  }
?>
<?php page_head();?>
</head>
<body>
<!-- header --> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof --> 
<!-- body --> 
<div id="main">
<!-- left_navigation -->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof -->
<!-- body_text -->
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php echo SEND_MAIL_HEADING_TITLE; ?></h1>
<div class="send_box01">
      <div class="send_mail">
<?php
  if ($success_single == false) {  
  if ($error_single == true) {
     echo '<div style="color:#ff0000;" class="form_error">'.$error_msg.'</div>'; 
   }
   echo tep_draw_form('login', tep_href_link('send_mail.php','','SSL'), 'post') . "\n";
?>
      <table class="login" width="100%">
        <tr>
          <td width="93"><b><?php echo INPUT_SEND_MAIL; ?>:</b></td>
          <td>
            <input type="text" name="email_address" class="input_text">
          </td>
         
        </tr>
        <tr>
         <td colspan="2" class="td_submit" align="right" style="padding:0 10px 0 0;">
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
       <div class="box_text"> 
       <?php
       if ($success_single) { 
       ?>
       <?php echo SENDMAIL_SUCCESS_COMMENT_TEXT;?> 
       <?php
      echo '<br>';
          echo SENDMAIL_TROUBLE_PRE;  
       ?> 
          <a href="<?php echo tep_href_link('email_trouble.php');?>"><?php echo SENDMAIL_TROUBLE_LINK;?></a>  
        <?php 
          echo SENDMAIL_TROUBLE_END;  
        ?>
       <?php } else {?> 
       <?php echo SENDMAIL_READ_TEXT;?> 
       <?php
          echo '<br>'; 
          echo SENDMAIL_TROUBLE_PRE;  
       ?> 
          <a href="<?php echo tep_href_link('email_trouble.php');?>"><?php echo SENDMAIL_TROUBLE_LINK;?></a>  
        <?php 
          echo SENDMAIL_TROUBLE_END;  
        ?>
        <?php }?> 
        </div> 
        <?php 
      if ($success_single == true) {  
         echo '<div style="width:100%;text-align:left;padding:10px 0 0 0;"><a href="'.tep_href_link(FILENAME_DEFAULT).'"><img src="includes/languages/japanese/images/buttons/button_back.gif"></a></div>';
         }
    ?>
        </div>

  </div></div>
      <!-- body_text_eof --> 
<!-- right_navigation --> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<!-- right_navigation_eof -->
  <!-- body_eof -->
  <!-- footer -->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof -->
</div>
</body>
</html>
