<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/ac_mail_finish.php');
  
  $error = false;
  $cus_email = '';
  $cud_id = 0;
  
  if (isset($_SESSION['acu_cud'])) {
    $cud_id = $_SESSION['acu_cud']; 
  }
  
    
  $customers_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".(int)$cud_id."' and site_id = '".SITE_ID."'");
  $customers_res = tep_db_fetch_array($customers_raw); 
  if ($customers_res) {
    $cus_email = $customers_res['new_email_address']; 
    if ($_GET['action'] == 'send') {
      if (empty($_POST['cemail'])) {
        $error = true;
      } else if (!tep_validate_email($_POST['cemail'])) {
        $error = true;
      } else {
        $check_email_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".tep_db_input($_POST['cemail'])."' and customers_id <> '".$customers_res['customers_id']."' and site_id = '".SITE_ID."'");
        if (tep_db_num_rows($check_email_raw)) {
          $error = true;
          $error_msg = CHECK_EMAIL_EXISTS_ERROR; 
        } else {
          $mail_name = tep_get_fullname($customers_res['customers_firstname'], $customers_res['customers_lastname']);   
          
          $ac_email_srandom = md5(time().$customers_res['customers_id'].$_POST['cemail']); 
          $old_str_array = array('${MAIL_CONFIRM_URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
          $new_str_array = array(
              HTTP_SERVER.'/m_edit_token.php?aid='.$ac_email_srandom,
              $mail_name, 
              STORE_NAME,
              HTTP_SERVER
              ); 
          //会员编辑邮件认证
          $edit_users_mail_array = tep_get_mail_templates('ACTIVE_EDIT_ACCOUNT_EMAIL_CONTENT',SITE_ID); 
          $email_text = str_replace($old_str_array, $new_str_array, $edit_users_mail_array['contents']);  
          $email_text = tep_replace_mail_templates($email_text,$_POST['cemail'],$mail_name);
          
          $ed_email_text = str_replace('${SITE_NAME}', STORE_NAME, $edit_users_mail_array['title']); 
          tep_mail($mail_name, $_POST['cemail'], $ed_email_text, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
          
          tep_db_query("update `".TABLE_CUSTOMERS."` set `new_email_address` = '".$_POST['cemail']."' where `customers_id` = '".$customers_res['customers_id']."' and site_id = '".SITE_ID."'"); 
          
          tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$ac_email_srandom."' where `customers_id` = '".$customers_res['customers_id']."' and site_id = '".SITE_ID."'"); 
        }
      }
    }
  }
  $breadcrumb->add(NAVBAR_TITLE);

?>
<?php page_head();?>
</head>
<body><div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
        <h1 class="pageHeading"><?php echo HEADING_TITLE; ?></h1> 
        
        <div class="comment"> 
         <?php
         if ($error == true) {
           if (isset($error_msg)) {
             if ($error_msg == ALREADY_SEND_MAIL_TEXT) {
               echo '<script type="text/javascript">alert(\''.ALREADY_SEND_MAIL_TEXT.'\')</script>'; 
             } else {
               echo '<div style="color:ff0000;">'.CHECK_EMAIL_EXISTS_ERROR.'</div>'; 
             }
           } else {
             echo '<div style="color:ff0000;">'.EMAIL_PATTERN_WRONG.'</div>'; 
           }
         }
         ?>
         <?php echo tep_draw_form('form', tep_href_link('ac_mail_finish.php', 'action=send', 'SSL'));?> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0" class="product_info_box"> 
            <tr>
              <td>
              <table class="captcha_comment">
                <tr>
                  <td colspan="3"><img src="images/design/mail_top.gif" alt=""></td>
                </tr>
                <tr>
                <td colspan="3" class="information_color">
                  <?php echo sprintf(NOTICE_SEND_TO_EMAIL_TEXT, (isset($_POST['cemail'])?$_POST['cemail']:$cus_email)); ?>
                </td>
              </tr>
              <tr>
                <td colspan="3">
                <br><?php echo ACTIVE_INFO_FRONT_COMMENT?><br><br> 
                </td>
                </tr>
                <tr>
                  <td width="90">
                  <b><?php echo INPUT_SEND_MAIL;?>:</b> 
                  </td>
                  <td width="365">
                  <?php echo tep_draw_input_field('cemail', (isset($_POST['cemail'])?$_POST['cemail']:$cus_email),'size="50"');?> 
                  </td>
                  <td>
                  <?php echo tep_image_submit('button_send_mail.gif', SENDMAIL_BUTTON);?> 
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                  <br><?php echo ACTIVE_INFO_END_COMMENT?>
                  </td>
                </tr>
              </table>
              </td>
            </tr>
            <tr>
              <td align="center"><img src="images/design/mail_bottom.gif" alt=""></td>
            </tr>
            <tr> 
              <td>
              <?php echo ACTIVE_INFO_EMAIL_READ;?> 
              </td>
            </tr>
          </table> 
          </form> 
        </div>
        <p class="pageBottom"></p>
        </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
      <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
      <!-- right_navigation_eof //--> </td> 
    </tr> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
