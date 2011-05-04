<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_ACCOUNT_EDIT));
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  # For Guest
  if($guestchk == '1') {
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  if (!isset($_POST['action']) || ($_POST['action'] != 'process')) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
  }

  // tamura 2002/12/30 「全角」英数字を「半角」に変換
  $an_cols = array('password','confirmation','email_address','postcode','telephone','fax');
  if (ACCOUNT_DOB) $an_cols[] = 'dob';
  foreach ($an_cols as $col) {
    $_POST[$col] = tep_an_zen_to_han($_POST[$col]);
  }

  $gender = tep_db_prepare_input($_POST['gender']);
  $firstname = tep_db_prepare_input($_POST['firstname']);
  $lastname = tep_db_prepare_input($_POST['lastname']);
  //add
  $firstname_f = tep_db_prepare_input($_POST['firstname_f']);
  $lastname_f = tep_db_prepare_input($_POST['lastname_f']);
  $dob = tep_db_prepare_input($_POST['dob']);
  $email_address = tep_db_prepare_input($_POST['email_address']);
  $telephone = tep_db_prepare_input($_POST['telephone']);
  $fax = tep_db_prepare_input($_POST['fax']);
  $newsletter = tep_db_prepare_input($_POST['newsletter']);
  $password = tep_db_prepare_input($_POST['password']);
  $confirmation = tep_db_prepare_input($_POST['confirmation']);
  $street_address = tep_db_prepare_input($_POST['street_address']);
  $company = tep_db_prepare_input($_POST['company']);
  $suburb = tep_db_prepare_input($_POST['suburb']);
  $postcode = tep_db_prepare_input($_POST['postcode']);
  $city = tep_db_prepare_input($_POST['city']);
  $zone_id = tep_db_prepare_input($_POST['zone_id']);
  $state = tep_db_prepare_input($_POST['state']);
  $country = tep_db_prepare_input($_POST['country']);

  $error = false; // reset error flag
/*
  if (ACCOUNT_GENDER == 'true') {
    if ( ($gender == 'm') || ($gender == 'f') ) {
      $entry_gender_error = false;
    } else {
      $error = true;
      $entry_gender_error = true;
    }
  }
*/
  if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_firstname_error = true;
  } else {
    $entry_firstname_error = false;
  }

  if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {
    $error = true;
    $entry_lastname_error = true;
  } else {
    $entry_lastname_error = false;
  }
/*
  if (ACCOUNT_DOB == 'true') {
    if (checkdate(substr(tep_date_raw($dob), 4, 2), substr(tep_date_raw($dob), 6, 2), substr(tep_date_raw($dob), 0, 4))) {
      $entry_date_of_birth_error = false;
    } else {
      $error = true;
      $entry_date_of_birth_error = true;
    }
  }
*/

  if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {
    $error = true;
    $entry_email_address_error = true;
  } else {
    $entry_email_address_error = false;
  }

  if (!tep_validate_email($email_address)) {
    $error = true;
    $entry_email_address_check_error = true;
  } else {
    $entry_email_address_check_error = false;
  }

  if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {
    $error = true;
    $entry_password_error = true;
  } else {
    $entry_password_error = false;
  }

  if ($password !== $confirmation) {
    $error = true;
    $entry_password_error = true;
    $entry_password_confirmation_error = true;
  }

//ccdd
  $check_email_query = tep_db_query("select count(*) as total from " .  TABLE_CUSTOMERS . " where customers_email_address = '" .  tep_db_input($email_address) . "' and customers_id != '" .  tep_db_input($customer_id) . "' and site_id = '".SITE_ID."'");
  $check_email = tep_db_fetch_array($check_email_query);
  if ($check_email['total'] > 0) {
    $error = true;
    $entry_email_address_exists = true;
  } else {
    $entry_email_address_exists = false;
  }

  if ($error == true) {
    $processed = true;

    include(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT_EDIT_PROCESS);

    $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
    $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL'));
?>
<?php page_head();?>
<?php require('includes/form_check.js.php'); ?>
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
        <div class="pageHeading"><img align="top" alt="" src="images/menu_ico.gif"><h1><span><?php echo HEADING_TITLE ; ?></span></h1></div> 
        
        <div class="comment"> 
      <?php echo tep_draw_form('account_edit', tep_href_link(FILENAME_ACCOUNT_EDIT_PROCESS, '', 'SSL'), 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td><?php include(DIR_WS_MODULES . 'account_details.php'); ?></td> 
            </tr> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <tr> 
              <td><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr> 
                    <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td> 
                    <td class="main" align="right"><?php echo tep_image_submit('button_continue02.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
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
</body>
</html>
<?php
  } else {
    $sql_data_array = array('customers_firstname' => $firstname,
                            'customers_lastname' => $lastname,
              //add
              'customers_firstname_f' => $firstname_f,
                            'customers_lastname_f' => $lastname_f,
                            'customers_email_address' => $email_address,
                            'customers_telephone' => $telephone,
                            //'customers_fax' => $fax,
                            'customers_newsletter' => $newsletter,
                            'customers_password' => tep_encrypt_password($password));

    if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;
    if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

    // ccdd
    tep_db_perform(TABLE_CUSTOMERS, $sql_data_array, 'update', "customers_id = '" .  tep_db_input($customer_id) . "' and site_id = '".SITE_ID."'");

// 2003-06-06 add_telephone
    $sql_data_array = array('entry_street_address' => $street_address,
                            'entry_firstname' => $firstname,
                            'entry_lastname' => $lastname,
                            'entry_postcode' => $postcode,
                            'entry_city' => $city,
                            'entry_country_id' => $country,
                            'entry_telephone' => $telephone);

    if (ACCOUNT_GENDER == 'true') $sql_data_array['entry_gender'] = $gender;
    if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $company;
    if (ACCOUNT_SUBURB == 'true') $sql_data_array['entry_suburb'] = $suburb;
    if (ACCOUNT_STATE == 'true') {
      if ($zone_id > 0) {
        $sql_data_array['entry_zone_id'] = $zone_id;
        $sql_data_array['entry_state'] = '';
      } else {
        $sql_data_array['entry_zone_id'] = '0';
        $sql_data_array['entry_state'] = $state;
      }
    }

    // ccdd
    tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array, 'update', "customers_id = '" . tep_db_input($customer_id) . "' and address_book_id = '" . tep_db_input($customer_default_address_id) . "'");
//ccdd
    tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . tep_db_input($customer_id) . "'");

    $customer_country_id = $country;
    $customer_zone_id = $zone_id;

    tep_redirect(tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  }

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
