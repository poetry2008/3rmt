<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  require(DIR_WS_LANGUAGES . $language . '/guest_info.php');
  
  $error = false;
  $cus_email = '';
  $customers_raw = tep_db_query("select * from ".TABLE_CUSTOMERS." where customers_id = '".base64_decode($_GET['gud'])."' and site_id = '".SITE_ID."'");
  $customers_res = tep_db_fetch_array($customers_raw); 
  if ($customers_res) {
    $cus_email = $customers_res['customers_email_address']; 
    if ($_GET['action'] == 'send') {
      if (empty($_POST['cemail'])) {
        $error = true;
      } else if (!tep_validate_email($_POST['cemail'])) {
        $error = true;
      } else {
        $mail_name = tep_get_fullname($customers_res['customers_firstname'], $customers_res['customers_lastname']);   
        $email_text = str_replace('${URL}', HTTP_SERVER.'/guest_autologin.php?gud='.base64_encode($customers_res['customers_id']), GUEST_LOGIN_EMAIL_CONTENT);  
        tep_mail($mail_name, $_POST['cemail'], GUEST_LOGIN_EMAIL_TITLE, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
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
      <div class="pageHeading"><img align="top" src="images/menu_ico_a.gif" alt=""><h1><?php echo ($_GET['cu'] == 1)?HEADING_TITLE_FINISH:HEADING_TITLE; ?></h1></div> 
        
        <div class="comment"> 
        <?php
          if ($error == true) {
            echo '<div style="color:ff0000;">'.EMAIL_PATTERN_WRONG.'</div>'; 
          }
        ?>
        <?php
          echo tep_draw_form('form', tep_href_link('guest_info.php', 'gud='.$_GET['gud'].'&action=send'.(isset($_GET['cu'])?'&cu='.$_GET['cu']:''))); 
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
            echo '<a href="'.tep_href_link(FILENAME_DEFAULT).'">' . tep_image_button('button_continue02.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; 
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
