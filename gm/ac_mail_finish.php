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
          
          $old_str_array = array('${URL}', '${NAME}', '${SITE_NAME}', '${SITE_URL}'); 
          $new_str_array = array(
              HTTP_SERVER.'/m_edit_token.php?aid='.$ac_email_srandom,
              $mail_name, 
              STORE_NAME,
              HTTP_SERVER
              ); 
          
          $email_text = str_replace($old_str_array, $new_str_array, ACTIVE_EDIT_ACCOUNT_EMAIL_CONTENT);  
          
          $ed_email_text = str_replace('${SITE_NAME}', STORE_NAME, ACTIVE_EDIT_ACCOUNT_EMAIL_TITLE); 
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
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<?php //require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- body_text //-->
<div id="layout" class="yui3-u">
        <div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
       <?php include('includes/search_include.php');?>
    <div id="main-content">
	<h2><?php echo HEADING_TITLE; ?></h2> 
        
        <div> 
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
          <table align="center" border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr>
              <td>
              <table border="0" cellpadding="0" cellspacing="0" width="100%" class="content_account">
                <tr>
                  <td colspan="3" align="center"><img src="images/design/mail_top.gif" alt=""></td>
                </tr>
                <tr>
                  <td colspan="3">
                  <?php
                    echo sprintf(NOTICE_SEND_TO_EMAIL_TEXT, (isset($_POST['cemail'])?$_POST['cemail']:$cus_email)); 
                  ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                  <br><?php echo ACTIVE_INFO_FRONT_COMMENT?><br><br> 
                  </td>
                </tr>
                <tr>
                  <td width="20%">
                  <b><?php echo INPUT_SEND_MAIL;?>:</b> 
                  </td>
                  <td width="70%">
                  <?php echo tep_draw_input_field('cemail', (isset($_POST['cemail'])?$_POST['cemail']:$cus_email),' style=" width:80%;" ');?> 
                  </td>
                  <td align="right" width="10%">
                  <?php echo tep_image_submit('button_send_mail.gif',
                      SENDMAIL_BUTTON,'  onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_send_mail.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_send_mail_hover.gif\'"');?> 
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
        </div></div></div>
        <?php include('includes/float-box.php');?>
      <!-- body_text_eof //--> 
<!-- right_navigation //--> 
<div id="r_menu">
<?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
</div>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
