<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  if (tep_session_is_registered('customer_id')) {
    // ccdd
    $account = tep_db_query("
        select customers_firstname, 
               customers_lastname, 
               customers_email_address 
        from " . TABLE_CUSTOMERS . " 
        where customers_id = '" . $customer_id . "' 
        and site_id = '".SITE_ID."'
    ");
    $account_values = tep_db_fetch_array($account);
  } elseif (ALLOW_GUEST_TO_TELL_A_FRIEND == 'false') {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  $valid_product = false;
  if (isset($_GET['products_id'])) {
    // ccdd
    $product_info_query = tep_db_query("
        select pd.products_name
        from " . TABLE_PRODUCTS_DESCRIPTION . " pd 
        where pd.products_status != '0' and pd.products_status != '3' 
          and pd.products_id = '" .  (int)$_GET['products_id'] . "' 
          and pd.language_id = '" . $languages_id . "' 
          and (pd.site_id = '".SITE_ID."' or pd.site_id = '0')
        order by pd.site_id DESC
        limit 1
    ");
    $valid_product = (tep_db_num_rows($product_info_query) > 0);
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_TELL_A_FRIEND);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_TELL_A_FRIEND, 'send_to=' . $_GET['send_to'] . '&products_id=' . $_GET['products_id']));
?>
<?php page_head();?>
</head>
<body>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<div id="main">
  <!-- left_navigation //-->
  <div id="l_menu">
    <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  </div>
  <!-- left_navigation_eof //-->
  <!-- body_text //-->
  <div id="content">
    <?php
  if ($valid_product == false) {
?>
    <p class="main"><?php echo HEADING_TITLE_ERROR; ?><br>
      <?php echo ERROR_INVALID_PRODUCT; ?> </p>
    <?php
  } else {
    $product_info = tep_db_fetch_array($product_info_query);
?>
    <div class="headerNavigation">
      <?php echo $breadcrumb->trail(' &raquo; '); ?>
    </div>
    <h1 class="pageHeading"><?php echo sprintf(HEADING_TITLE, $product_info['products_name']); ?></h1>
    <?php
    $error = false;

    if (isset($_GET['action']) && ($_GET['action'] == 'process') && !tep_validate_email(trim($_POST['friendemail']))) {
      $friendemail_error = true;
      $error = true;
    } else {
      $friendemail_error = false;
    }

    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($_POST['friendname'])) {
      $friendname_error = true;
      $error = true;
    } else {
      $friendname_error = false;
    }

    if (tep_session_is_registered('customer_id')) {
      $from_name = tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']);
      $from_email_address = $account_values['customers_email_address'];
    } else {
      if (!isset($_POST['yourname'])) $_POST['yourname'] = NULL; 
      $from_name = $_POST['yourname'];
      if (!isset($_POST['from'])) $_POST['from'] = NULL; 
      $from_email_address = $_POST['from'];
    }
      
    if (!tep_session_is_registered('customer_id')) {
      if (isset($_GET['action']) && ($_GET['action'] == 'process') && !tep_validate_email(trim($from_email_address))) {
        $fromemail_error = true;
        $error = true;
      } else {
        $fromemail_error = false;
      }
    }

    if (isset($_GET['action']) && ($_GET['action'] == 'process') && empty($from_name)) {
      $fromname_error = true;
      $error = true;
    } else {
      $fromname_error = false;
    }

    if (isset($_GET['action']) && ($_GET['action'] == 'process') && ($error == false)) {
      $email_subject = sprintf(TEXT_EMAIL_SUBJECT, $from_name, STORE_NAME);
      $email_body = sprintf(TEXT_EMAIL_INTRO, $_POST['friendname'], $from_name, $_POST['products_name'], STORE_NAME) . "\n\n";

      if (tep_not_null($_POST['yourmessage'])) {
        $email_body .= $_POST['yourmessage'] . "\n\n";
      }

      $email_body .= sprintf(TEXT_EMAIL_LINK, tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $_GET['products_id'])) . "\n\n" .
                     sprintf(TEXT_EMAIL_SIGNATURE, STORE_NAME . "\n" . HTTP_SERVER . DIR_WS_CATALOG . "\n");

      tep_mail($_POST['friendname'], $_POST['friendemail'], $email_subject, stripslashes($email_body), '', $from_email_address);
?>
    <div class="tell_msg">
      <p class="main"><?php echo sprintf(TEXT_EMAIL_SUCCESSFUL_SENT, stripslashes($_POST['products_name']), $_POST['friendemail']); ?></p>
      <div align="right">
        <?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $_GET['products_id']) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?>
      </div>
    </div>
    <?php
    } else {
      if (tep_session_is_registered('customer_id')) {
        $your_name_prompt = tep_output_string_protected(tep_get_fullname($account_values['customers_firstname'],$account_values['customers_lastname']));
        $your_email_address_prompt = $account_values['customers_email_address'];
      } else {
        if (!isset($_GET['yourname'])) $_GET['yourname'] = NULL; 
        $your_name_prompt = tep_draw_input_field('yourname', (($fromname_error == true) ? $_POST['yourname'] : $_GET['yourname']));
        if ($fromname_error == true) $your_name_prompt .= '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';
      if (!isset($_GET['from'])) $_GET['from'] = NULL; 
        $your_email_address_prompt = tep_draw_input_field('from', (($fromemail_error == true) ? $_POST['from'] : $_GET['from']));
        if ($fromemail_error == true) $your_email_address_prompt .= ENTRY_EMAIL_ADDRESS_CHECK_ERROR;
      }
?>
    <?php echo tep_draw_form('email_friend', tep_href_link(FILENAME_TELL_A_FRIEND, 'action=process&products_id=' . $_GET['products_id'])) . tep_draw_hidden_field('products_name', $product_info['products_name']); ?>
    <table class="box_des" width="95%" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td class="formAreaTitle"><?php echo FORM_TITLE_CUSTOMER_DETAILS; ?></td>
      </tr>
      <tr>
        <td class="main">
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main">
                <table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main"><?php echo FORM_FIELD_CUSTOMER_NAME; ?></td>
                    <td class="main"><?php echo $your_name_prompt; ?></td>
                  </tr>
                  <tr>
                    <td class="main"><?php echo FORM_FIELD_CUSTOMER_EMAIL; ?></td>
                    <td class="main"><?php echo $your_email_address_prompt; ?></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="formAreaTitle"><br>
          <?php echo FORM_TITLE_FRIEND_DETAILS; ?></td>
      </tr>
      <tr>
        <td class="main">
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main">
                <table class="box_des" border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main"><?php echo FORM_FIELD_FRIEND_NAME; ?></td>
                    <td class="main">
                    <?php 
                    if (!isset($_GET['friendname'])) $_GET['friendname'] = NULL; 
                    echo tep_draw_input_field('friendname', (($friendname_error == true) ? $_POST['friendname'] : $_GET['friendname'])); if ($friendname_error == true) echo '&nbsp;<span class="errorText">' . TEXT_REQUIRED . '</span>';
                    ?>
                    </td>
                  </tr>
                  <tr>
                    <td class="main"><?php echo FORM_FIELD_FRIEND_EMAIL; ?></td>
                    <td class="main">
                    <?php 
                    if (!isset($_GET['send_to'])) $_GET['send_to'] = NULL; 
                    echo tep_draw_input_field('friendemail', (($friendemail_error == true) ? $_POST['friendemail'] : $_GET['send_to'])); if ($friendemail_error == true) echo ENTRY_EMAIL_ADDRESS_CHECK_ERROR; 
                    ?>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="formAreaTitle"><br>
          <?php echo FORM_TITLE_FRIEND_MESSAGE; ?></td>
      </tr>
      <tr>
        <td class="main">
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td><?php echo tep_draw_textarea_field('yourmessage', 'soft', 40, 8);?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td><br>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $_GET['products_id']) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
              <td align="right" class="main"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    </form>
    <?php
    }
}
?>
  </div>
  <!-- body_text_eof //-->
  <!-- right_navigation //-->
  <div id="r_menu">
    <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
  </div>
  <!-- right_navigation_eof //-->
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>