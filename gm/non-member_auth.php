<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/non-member_auth.php');
  
  $error = false;
  $cus_email = '';
  $gud_id = 0; 
  if (isset($_SESSION['pa_gud'])) {
    $gud_id = $_SESSION['pa_gud']; 
  }
  
  if (!$gud_id) {
    $error = true;
    $error_msg = ALREADY_SEND_MAIL_TEXT;
  }
  
  $customers_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".(int)$gud_id."' and site_id = '".SITE_ID."'");
  $customers_res = tep_db_fetch_array($customers_raw); 
  if(isset($_POST['cemail'])){
    $_POST['cemail'] = str_replace("\xe2\x80\x8b", '', $_POST['cemail']);
    $val_email = tep_db_prepare_input($_POST['cemail']);
  }
  if ($customers_res) {
    $cus_email = $customers_res['customers_email_address']; 
    if ($_GET['action'] == 'send') {
      if (empty($_POST['cemail'])) {
        $error = true;
      } else if (!tep_validate_email($val_email)) {
        $error = true;
        $error_msg = WRONG_EMAIL_PATTERN_NOTICE; 
      } else if (tep_check_exists_cu_email($_POST['cemail'], $customers_res['customers_id'], 1)) {
        $error = true;
        $error_msg = CHECK_EMAIL_EXISTS_ERROR; 
      } else if ($customers_res['is_active']) {
        $error = true;
        $error_msg = ALREADY_SEND_MAIL_TEXT; 
      } else {
        $mail_name = tep_get_fullname($customers_res['customers_firstname'], $customers_res['customers_lastname']);   
        $gu_email_srandom = md5(time().$customers_res['customers_id'].$_POST['cemail']); 
        
        $email_text = stripslashes($customers_res['customers_lastname'].' '.$customers_res['customers_firstname']).EMAIL_NAME_COMMENT_LINK .  "\n\n"; 
        $old_str_array = array('${URL}', '${NAME}', '${SITE_NAME}', '${SITE_URL}'); 
        $new_str_array = array(
            HTTP_SERVER.'/nm_token.php?gud='.$gu_email_srandom,
            $mail_name, 
            STORE_NAME,
            HTTP_SERVER
            ); 
        //游客邮件认证
        $guest_mail_array = tep_get_mail_templates('GUEST_LOGIN_EMAIL_CONTENT',SITE_ID);
        $email_text .= str_replace($old_str_array, $new_str_array, $guest_mail_array['contents']);  
        $gu_email_text = str_replace('${SITE_NAME}', STORE_NAME, $guest_mail_array['title']);
        
        if ($customers_res['is_send_mail'] != '1') {
          tep_mail($mail_name, $_POST['cemail'], $gu_email_text, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        } 
        
        tep_db_query("update `".TABLE_CUSTOMERS."` set `check_login_str` = '".$gu_email_srandom."' where `customers_id` = '".$customers_res['customers_id']."' and site_id = '".SITE_ID."'"); 
        
        tep_db_query("update `".TABLE_CUSTOMERS."` set `customers_email_address` = '".$_POST['cemail']."' where `customers_id` = '".$customers_res['customers_id']."' and site_id = '".SITE_ID."'"); 
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
              ?>
               <script type="text/javascript">
               alert('<?php echo $error_msg;?>');
               window.location.href="<?php echo tep_href_link(FILENAME_DEFAULT);?>"; 
               </script> 
              <?php
              } else {
                echo '<div style="color:ff0000;">'.$error_msg.'</div>'; 
              }
            } else {
              echo '<div style="color:ff0000;">'.EMAIL_PATTERN_WRONG.'</div>'; 
            }
          }
        ?>
        <?php
          echo tep_draw_form('form', tep_href_link('non-member_auth.php', 'action=send'.(isset($_GET['cu'])?'&cu='.$_GET['cu']:''), 'SSL')); 
        ?>
          
         <table align="center" border="0" width="100%" cellspacing="0" cellpadding="0"> 
        <?php
        if ($_GET['cu'] == 1) {
        ?>
        <tr>
          <td>
          <?php echo CHECK_FINISH_TEXT;?> 
          </td>
        </tr>
        <?php
        } else {
        ?>
        <tr>
          <td>
           <table border="0" cellpadding="0" cellspacing="0" width="100%" class="content_account">
            <tr>
              <td colspan="3" align="center"><img src="images/design/mail_top.gif" alt=""></td> 
            </tr>
            <tr>
              <td colspan="3" class="information_color">
              <?php
                echo sprintf(NOTICE_SEND_TO_EMAIL_TEXT, (isset($_POST['cemail'])?$_POST['cemail']:$cus_email)); 
              ?>
              </td>
            </tr>
            <tr>
              <td colspan="3">
              <br><?php echo ACTIVE_INFO_FRONT_COMMENT;?><br><br> 
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
                  SENDMAIL_BUTTON,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_send_mail.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_send_mail_hover.gif\'"');?> 
              </td>
            </tr>
            <tr>
              <td colspan="3">
              <br><?php echo ACTIVE_INFO_END_COMMENT;?> 
              </td>
            </tr>
          </table>
          </td>
        </tr>
        <tr>
          <td align="center">
          <img src="images/design/mail_bottom.gif" alt=""> 
          </td>
        </tr>
        <tr>
          <td>
          <?php echo ACTIVE_INFO_EMAIL_READ;?> 
          </td>
        </tr>
        <?php }?> 
           <tr> 
              <td><br> 
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr> 
                    <td align="right">
          <?php 
          if ($_GET['cu'] == 1) {
            echo '<a href="'.tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL').'">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; 
          }
          ?>
                    </td> 
                    <td align="right">
                    </td> 
                  </tr> 
                </table></td> 
            </tr> 
          </table> 
          </form> 
        </div>
              </div>
               
        
        </div>
   <?php include('includes/float-box.php');?>


  </div>
 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
