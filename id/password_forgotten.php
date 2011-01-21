<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PASSWORD_FORGOTTEN);

  if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
//ccdd
    $check_customer_query = tep_db_query("
        select customers_firstname, 
               customers_lastname, 
               customers_password, 
               customers_id, 
               customers_guest_chk 
        from " . TABLE_CUSTOMERS . " 
        where customers_email_address = '" .  $_POST['email_address'] . "' 
          and site_id =".SITE_ID
    );
    if (tep_db_num_rows($check_customer_query)) {
      $check_customer = tep_db_fetch_array($check_customer_query);
    if($check_customer['customers_guest_chk'] == '0') {
        // Crypted password mods - create a new password, update the database and mail it to them
        $newpass = tep_create_random_value(ENTRY_PASSWORD_MIN_LENGTH);
        $crypted_password = tep_encrypt_password($newpass);
//ccdd
        tep_db_query("
            update " . TABLE_CUSTOMERS . " 
            set customers_password = '" . $crypted_password . "' 
            where customers_id = '" . $check_customer['customers_id'] . "'
        ");
        tep_mail(tep_get_fullname($check_customer['customers_firstname'],$check_customer['customers_lastname']), $_POST['email_address'], EMAIL_PASSWORD_REMINDER_SUBJECT, sprintf(EMAIL_PASSWORD_REMINDER_BODY, $newpass), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        tep_redirect(tep_href_link(FILENAME_LOGIN, 'info_message=' . urlencode(TEXT_PASSWORD_SENT), 'SSL', true, false));
    } else {
      tep_redirect(tep_href_link(FILENAME_PASSWORD_FORGOTTEN, 'email=nonexistent', 'SSL'));
    }
    } else {
      tep_redirect(tep_href_link(FILENAME_PASSWORD_FORGOTTEN, 'email=nonexistent', 'SSL'));
    }
  } else {

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL'));
?>
<?php page_head();?>
</head>
<body>
<div class="body_shadow" align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" summary="box">
    <tr>
      <!-- body_text //-->
      <td valign="top" id="contents">
        <h1 class="pageHeading">
        <span class="game_t">
        <?php echo HEADING_TITLE ; ?>
        </span>
        </h1>
        <div class="comment">
        <?php echo tep_draw_form('password_forgotten', tep_href_link(FILENAME_PASSWORD_FORGOTTEN, 'action=process', 'SSL')); ?>
          <table border="0" width="100%" cellspacing="0" cellpadding="0" summary="table">
            <tr>
              <td colspan="2"></td>
            <tr>
              <td align="right" class="main"><?php echo ENTRY_FORGOTTEN_EMAIL_ADDRESS; // 2003.03.06 nagata Edit Japanese osCommerce ?></td>
              <td class="main"><?php echo tep_draw_input_field('email_address', '', 'maxlength="96" class="input_text"'); ?></td>
            </tr>
            <tr>
              <td colspan="2"><br>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" summary="table">
                  <tr>
                    <td valign="top">
                      <a href="<?php echo tep_href_link(FILENAME_LOGIN, '', 'SSL') ;?>"><?php echo tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) ; ?></a>
                    </td>
                    <td align="right" valign="top"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
                  </tr>
                </table>
              </td>
            </tr>
            <?php
  if (isset($_GET['email']) && ($_GET['email'] == 'nonexistent')) {
    echo '          <tr>' . "\n";
    echo '            <td colspan="2" class="smallText">' .  TEXT_NO_EMAIL_ADDRESS_FOUND . '</td>' . "\n";
    echo '          </tr>' . "\n";
  }
?>
          </table>
          </form>
        </div>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
        <!-- right_navigation_eof //-->
      </td>
     </tr>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html><?php
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
