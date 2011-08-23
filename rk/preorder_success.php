<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES.$language.'/preorder_success.php');
  $error = false;
  $pe_email = '';
  $preorder_id = 0;  
  
  if (isset($_SESSION['send_preorder_id'])) {
    $preorder_id = $_SESSION['send_preorder_id'];
  }
  
  if (!$preorder_id) {
    $error = true;
    $error_msg = ALREADY_SEND_PREMAIL_TEXT;
  }
  
  $preorder_raw = tep_db_query("select * from ".TABLE_PREORDERS." where orders_id = '".$preorder_id."' and site_id = '".SITE_ID."'"); 
  $preorder = tep_db_fetch_array($preorder_raw);

  if ($preorder) {
    $pe_email = $preorder['customers_email_address']; 
    if ($_POST['action'] == 'send') {
      if (empty($_POST['pemail'])) {
        $error = true; 
      } else if (!tep_validate_email($_POST['pemail'])) {
        $error = true; 
        $error_msg = PREORDER_EMAIL_PATTENR_WRONG; 
      }
    
      if ($preorder['is_active']) {
        $preorder_email_subject = PREORDER_MAIL_SUBJECT; 
        $preorder_email_text = PREORDER_MAIL_CONTENT; 
      } else {
        $preorder_email_subject = PREORDER_MAIL_ACTIVE_SUBJECT; 
        $active_url = HTTP_SERVER.'/preorder_auth.php?pid='.$preorder_id; 
        $preorder_email_text = str_replace('${URL}', $active_url, PREORDER_MAIL_ACTIVE_CONTENT); 
      }
      tep_mail($preorder['customers_name'], $_POST['pemail'], $preorder_email_subject, $preorder_email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS); 
    }
  }
  $breadcrumb->add(PREORDER_SUCCESS_TITLE, '');
?>
<?php page_head();?>
</head>
<body><div align="center"> 
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
      <h1 class="pageHeading"><?php echo PREORDER_SUCCESS_TITLE;?></h1> 
      <div class="comment">
      <?php
      if ($error == true) {
        if (isset($error_msg)) {
          if ($error_msg == ALREADY_SEND_PREMAIL_TEXT) {
          ?>
          <script type="text/javascript">
          alert('<?php echo $error_msg;?>'); 
          window.location.href="<?php echo HTTP_SERVER;?>"; 
          </script>
          <?php
          } else {
            echo '<div style="color:ff0000;">'.$error_msg.'</div>'; 
          }
        }
      }
      echo tep_draw_form('form', tep_href_link('preorder_success.php')); 
      echo tep_draw_hidden_field('action', 'send'); 
      ?>
      <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size:12px;">
        <tr>
          <td>
            <table>
              <tr>
                <td style="font-size:11px;"><?php echo INPUT_PREORDER_SEND_MAIL;?></td> 
                <td>
                <?php echo tep_draw_input_field('pemail', (isset($_POST['pemail'])?$_POST['pemail']:$pe_email));?> 
                </td>
                <td>
                <?php echo tep_image_submit('button_send_mail.gif', 'mail');?> 
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
          <?php echo PREORDER_SUCCESS_TEXT;?> 
          </td>
        </tr>
        <tr>
          <td>
                <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
                  <tr> 
                    <td class="main" align="right"><?php echo '<a href="' .tep_href_link(FILENAME_DEFAULT). '">' .  tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td> 
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
