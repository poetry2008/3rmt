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
      $random_str = md5(time().$check_customer['customers_id'].$_POST['email_address']); 
        
        $send_url = HTTP_SERVER.'/password_token.php?pud='.$random_str;
        $exists_password_raw = tep_db_query("select customers_id from customers_password_info where customers_id = '".$check_customer['customers_id']."'"); 
        if (tep_db_num_rows($exists_password_raw)) {
          tep_db_query("update `customers_password_info` set `customers_email` = '".$_POST['email_address']."', `customers_ip` = '".$_SERVER["REMOTE_ADDR"]."', `random_num` = '".$random_str."', `created_at` = '".date('Y-m-d H:i:s',time())."', `is_update` = '0' where `customers_id` = '".$check_customer['customers_id']."'"); 
        } else {
          tep_db_query("insert into `customers_password_info` values('".$check_customer['customers_id']."', '".$_POST['email_address']."', '".$_SERVER["REMOTE_ADDR"]."', '".$random_str."', '".date('Y-m-d H:i:s',time())."', '0')");
        }
        
        $email_body = SEND_PASSWORLD_EMAIL_CONTENT;
        $email_body = str_replace('${URL}', $send_url, $email_body);
        $email_body = str_replace('${SITE_NAME}', STORE_NAME, $email_body);
        $email_body = str_replace('${SITE_URL}', HTTP_SERVER, $email_body);
        $email_body = str_replace('${IP}', $_SERVER["REMOTE_ADDR"], $email_body);
        $email_body = str_replace('${NAME}', tep_get_fullname($check_customer['customers_firstname'], $check_customer['customers_lastname']), $email_body);
        
        tep_mail(tep_get_fullname($check_customer['customers_firstname'],$check_customer['customers_lastname']), $_POST['email_address'], str_replace('${SITE_NAME}', STORE_NAME, SEND_PASSWORLD_EMAIL_TITLE), $email_body, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
          
      tep_redirect(tep_href_link('send_success.php', 'send_mail='.$_POST['email_address']));
    } else {
      tep_redirect(tep_href_link('send_success.php', 'send_mail='.$_POST['email_address']));
    }
    } else {
      tep_redirect(tep_href_link('send_success.php', 'send_mail='.$_POST['email_address']));
    }
  } else {

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL'));
?>
<?php page_head();?>
</head>
<body>
<div align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" summary="box">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" id="contents">
        <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>
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
        <p class="pageBottom"></p>
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
</div>
</body>
</html><?php
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
