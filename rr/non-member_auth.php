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
        $old_str_array = array('${URL}', '${USER_NAME}', '${SITE_NAME}', '${SITE_URL}'); 
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
        
        $email_text = tep_replace_mail_templates($email_text,$_POST['cemail'],$mail_name); 
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
        <h1 class="pageHeading"><span><?php echo HEADING_TITLE; ?></span></h1> 
        
        <div class="comment"> 
        <?php
          if ($error == true) {
            if (isset($error_msg)) {
              if ($error_msg == ALREADY_SEND_MAIL_TEXT) {
              ?>
               <script type="text/javascript">
               alert('<?php echo $error_msg;?>');
               window.location.href="<?php echo HTTP_SERVER.'?'.tep_session_name.'='.tep_session_id();?>"; 
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
          <table border="0" width="100%" cellspacing="0" cellpadding="0" class="product_info_box" style="font-size:12px;"> 
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
              <table>
              <tr>
                <td colspan="3" align="center"><img src="images/design/mail_top.gif" alt=""></td>
              </tr>
              <tr>
                <td class="information_color" colspan="3"><?php echo sprintf(NOTICE_SEND_TO_EMAIL_TEXT, (isset($_post['cemail'])?$_post['cemail']:$cus_email));?>
                </td>
              </tr>

              <tr>
                <td colspan="3" class="main">
                 <br>
                <?php echo ACTIVE_INFO_FRONT_COMMENT;?>
                <br>
                <br>
                </td>
              </tr>
                <tr>
                  <td class="active_email_name">
                  <b><?php echo INPUT_SEND_MAIL;?>:</b> 
                  </td>
                  <td class="active_email">
                  <?php echo tep_draw_input_field('cemail', (isset($_POST['cemail'])?$_POST['cemail']:$cus_email));?> 
                  </td>
                  <td>
                  <?php echo tep_image_submit('button_send_mail.gif', SENDMAIL_BUTTON);?> 
                  </td>
                </tr>
           <tr>
                <td colspan="3" class="main">
                <br><?php echo ACTIVE_INFO_END_COMMENT;?>
                </td>
              </tr>
              </table>
              </td>
            </tr>
		  <tr>
            <td colspan="3" align="center"><img src="images/design/mail_bottom.gif" alt=""></td>
          </tr>
		            <tr>
            <td colspan="3" class="main">
           <?php echo ACTIVE_INFO_EMAIL_READ;?>
            </td>
          </tr>
            <?php }?> 
            <tr> 
              <td><br>
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr> 
                    <td class="main" align="right">
                    <?php 
                    if ($_GET['cu'] == 1) {
                      echo '<a href="'.tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL').'">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; 
                    } else {
                      echo '<a href="'.HTTP_SERVER.'?'.tep_session_name().'='.tep_session_id().'">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; 
                    }
                    ?>
                    </td> 
                    <td align="right" class="main">
                    </td> 
                  </tr> 
                </table></td> 
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
