<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PASSWORD_FORGOTTEN);

  if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
    $_POST['email_address'] =  str_replace("\xe2\x80\x8b", '',$_POST['email_address']);
    $val_email_address = tep_db_prepare_input($_POST['email_address']);
    $check_customer_query = tep_db_query("
        select customers_firstname, 
               customers_lastname, 
               customers_password, 
               customers_id, 
               customers_guest_chk 
        from " . TABLE_CUSTOMERS . " 
        where customers_email_address = '" .  $_POST['email_address'] . "' 
          and site_id =".SITE_ID." and is_quited != 1"
    );
    if (tep_db_num_rows($check_customer_query)) {
      $check_customer = tep_db_fetch_array($check_customer_query);
    if($check_customer['customers_guest_chk'] == '0') {
        $random_str = md5(time().$check_customer['customers_id'].$_POST['email_address']); 

        $send_url = HTTP_SERVER.'/password_token.php?pud='.$random_str;
        $exists_password_raw = tep_db_query("select customers_id from customers_password_info where customers_id = '".$check_customer['customers_id']."'"); 
        if (tep_db_num_rows($exists_password_raw)) {
          tep_db_query("update `customers_password_info` set `customers_email` = '".$_POST['email_address']."', `customers_ip` = '".$_SERVER["REMOTE_ADDR"]."', `random_num` = '".$random_str."', `created_at` = '".date('Y-m-d H:i:s',time())."', `is_update` = '0' where `customers_id` = '".$check_customer['customers_id']."'"); 
        } else {
          tep_db_query("insert into `customers_password_info` values('".$check_customer['customers_id']."', '".$_POST['email_address']."', '".$_SERVER["REMOTE_ADDR"]."', '".$random_str."', '".date('Y-m-d H:i:s',time())."', '0')");
        }

        //密码重置邮件
        $password_mail_array = tep_get_mail_templates('SEND_PASSWORLD_EMAIL_CONTENT',SITE_ID); 
        $email_body = $password_mail_array['contents'];
        $email_body = str_replace('${PASSWORD_REISSUE_URL}', $send_url, $email_body);
        $email_body = str_replace('${SITE_NAME}', STORE_NAME, $email_body);
        $email_body = str_replace('${SITE_URL}', HTTP_SERVER, $email_body);
        $email_body = str_replace('${IP}', $_SERVER["REMOTE_ADDR"], $email_body);
        $email_body = str_replace('${USER_NAME}', tep_get_fullname($check_customer['customers_firstname'], $check_customer['customers_lastname']), $email_body);
        $email_body = tep_replace_mail_templates($email_body,$_POST['email_address'],tep_get_fullname($check_customer['customers_firstname'],$check_customer['customers_lastname']));
        tep_mail(tep_get_fullname($check_customer['customers_firstname'],$check_customer['customers_lastname']), $_POST['email_address'], str_replace('${SITE_NAME}', STORE_NAME, $password_mail_array['title']), $email_body, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
      tep_redirect(tep_href_link('send_success.php',
            'send_mail='.rawurlencode($val_email_address)));
    } else {
      tep_redirect(tep_href_link('send_success.php',
            'send_mail='.rawurlencode($val_email_address)));
    }
    } else {
      if(tep_validate_email($val_email_address)){
      tep_redirect(tep_href_link('send_success.php',
            'send_mail='.rawurlencode($val_email_address)));
      }else{
        tep_redirect(tep_href_link(FILENAME_PASSWORD_FORGOTTEN, 'error=1', 'SSL'));
      }
    }
  } else {

  $breadcrumb->add(NAVBAR_TITLE_FIRST, tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  $breadcrumb->add(HEADING_TITLE, tep_href_link(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL'));
?>
<?php page_head();?>
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<!-- body_text //-->
<div id="layout" class="yui3-u">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<?php include('includes/search_include.php');?>
<div id="main-content">
<h2><?php echo HEADING_TITLE; ?></h2>
        <div style="margin-top:13px;"> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td><?php echo tep_draw_form('password_forgotten', tep_href_link(FILENAME_PASSWORD_FORGOTTEN, 'action=process', 'SSL')); ?>
          <tr>
            <td width="20%"><?php echo ENTRY_FORGOTTEN_EMAIL_ADDRESS; ?></td>
            <td><?php echo tep_draw_input_field('email_address', '',
                'id="input_width" maxlength="96"'); ?></td>
          </tr>
          <tr>
            <td colspan="2"><table class="botton-continue" border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td valign="top"><a href="<?php echo tep_href_link(FILENAME_LOGIN,
            '', 'SSL') ;?>"><?php echo tep_image_button('button_back.gif',
            IMAGE_BUTTON_BACK,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_back.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_back_hover.gif\'"') ; ?></a></td>
                <td align="right" valign="top"><?php echo
                tep_image_submit('button_continue.gif',
                    IMAGE_BUTTON_CONTINUE,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_continue.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_continue_hover.gif\'"'); ?></td>
              </tr>
            </table></td>
          </tr>
<?php
  if (isset($_GET['email']) && ($_GET['email'] == 'nonexistent')) {
    echo '          <tr>' . "\n";
    echo '            <td colspan="2" class="smallText">' .  TEXT_NO_EMAIL_ADDRESS_FOUND . '</td>' . "\n";
    echo '          </tr>' . "\n";
  }else if(isset($error)&&$error){
    echo '<tr>';
    echo '<td colspan="2" class="smallText">'.PASSWORD_USER_EMAIL_ERROR.'</td>';
    echo '</tr>';
  }
?>
        </table></form></div>
        
        
        </div>
		</div>
        <?php include('includes/float-box.php');?>
      <!-- body_text_eof //--> 
  <!-- body_eof //-->  
  <!-- footer //--> 
    <!-- footer_eof //-->

</div>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
