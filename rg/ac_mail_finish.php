<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
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
      } else if (!preg_match("/^([a-zA-Z0-9]+[_|\-|\.|\+]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/", $_POST['cemail'])) {
        $error = true;
      } else if ($customers_res['customers_email_address'] == $_POST['cemail']) {
        $error = true;
        $error_msg = ALREADY_SEND_MAIL_TEXT; 
      } else {
        $check_email_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_email_address = '".tep_db_input($_POST['cemail'])."' and customers_id <> '".$customers_res['customers_id']."' and site_id = '".SITE_ID."' and customers_guest_chk = '0'");
        if (tep_db_num_rows($check_email_raw)) {
          $error = true;
          $error_msg = CHECK_EMAIL_EXISTS_ERROR; 
        } else {
          $mail_name = tep_get_fullname($customers_res['customers_firstname'], $customers_res['customers_lastname']);   
          
          $ac_email_srandom = md5(time().$customers_res['customers_id'].$_POST['cemail']); 
          
          $email_text = str_replace('${URL}', HTTP_SERVER.'/m_edit_token.php?aid='.$ac_email_srandom, ACTIVE_EDIT_ACCOUNT_EMAIL_CONTENT);  
          
          tep_mail($mail_name, $_POST['cemail'], ACTIVE_EDIT_ACCOUNT_EMAIL_TITLE, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
          
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
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"> 
      <div class="pageHeading"><img align="top" src="images/menu_ico_a.gif" alt=""><h1><?php echo HEADING_TITLE ; ?></h1></div> 
        
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
          <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;"> 
            <tr>
              <td>
              <table>
                <tr>
                  <td style="font-size:11px;">
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
              <?php echo ACTIVE_INFO_COMMENT;?> 
              </td>
            </tr>
        <tr>
          <td align="right"><br><?php echo '<a href="'.tep_href_link(FILENAME_ACCOUNT, '', 'SSL').'">' . tep_image_button('button_continue02.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
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
