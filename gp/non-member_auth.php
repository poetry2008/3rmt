<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
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
  }
  if ($customers_res) {
    $cus_email = $customers_res['customers_email_address']; 
    if ($_GET['action'] == 'send') {
      if (empty($_POST['cemail'])) {
        $error = true;
      } else if (!tep_validate_email($_POST['cemail'])) {
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
        $email_text .= str_replace($old_str_array, $new_str_array, GUEST_LOGIN_EMAIL_CONTENT);  
        $gu_email_text = str_replace('${SITE_NAME}', STORE_NAME, GUEST_LOGIN_EMAIL_TITLE); 
        tep_mail($mail_name, $_POST['cemail'], $gu_email_text, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        
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
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
      <div class="pageHeading"><h1><?php echo ($_GET['cu'] == 1)?HEADING_TITLE_FINISH:HEADING_TITLE; ?></h1></div> 
        
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
        <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;"> 
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
                <td style="color: rgb(255, 0, 0);" colspan="3">
                <font size="3"><b><?php echo EMAIL_RED_TEXT_INFO;?></b></font>
                </td>
              </tr>
              <tr>
                <td style="font-size:11px;" colspan="3">
                <?php echo EMAIL_READ_INFO_TEXT;?>
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
          </table>
          </td>
        </tr>
        <tr>
          <td>
          <?php echo GUEST_SUCCESS_INFO_COMMENT;?> 
          </td>
        </tr>
        <?php }?> 
        <tr>
          <td align="right"><br>
          <?php 
          if ($_GET['cu'] == 1) {
            echo '<a href="'.tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL').'">' . tep_image_button('button_continue02.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; 
          } else {
            echo '<a href="'.HTTP_SERVER.'?'.tep_session_name().'='.tep_session_id().'">' . tep_image_button('button_continue02.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; 
          }
          ?>
          </td>
        </tr>
      </table>
      </form> 
      </div>
      </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
